<?php

if(!class_exists('Vidsoe_Facebook_OAuth')){
    final class Vidsoe_Facebook_OAuth extends Vidsoe_Base {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private $default_role = '', $fb = null, $remember = false, $userinfo = null, $users_can_register = false;

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private function authenticate(){
            if(is_user_logged_in()){
                return;
            }
            $email = $this->userinfo['email'];
            if(email_exists($email)){
                $user = get_user_by('email', $email);
                $user = vidsoe()->signon_without_password($user->user_login, $this->remember);
                if(is_wp_error($user)){
                    wp_die($user);
                }
                update_user_meta($user->ID, 'facebook_userinfo', $this->userinfo);
                return;
            }
            if(!$this->users_can_register){
                wp_die(__('<strong>Error</strong>: User registration is currently not allowed.'));
            }
            $email = wp_slash($email);
            $first_name = $this->userinfo['first_name'];
            $last_name = $this->userinfo['last_name'];
            $name = $this->userinfo['name'];
            $password = wp_generate_password();
            $user_login = md5($email);
            $userdata = [
                'display_name' => $name,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'nickname' => $name,
                'role' => $this->default_role,
                'user_email' => $email,
                'user_login' => $user_login,
                'user_pass' => $password,
            ];
            $user_id = wp_insert_user($userdata);
            if(is_wp_error($user_id)){
                wp_die($user_id);
            }
            update_user_meta($user_id, 'facebook_userinfo', $this->userinfo);
            $credentials = [
                'user_login' => $user_login,
                'user_password' => $password,
                'remember' => $this->remember,
            ];
            $user = wp_signon($credentials);
            if(is_wp_error($user)){
                wp_die($user);
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private function setup(){
            if(null !== $this->fb){
                return;
            }
            if(!class_exists('Facebook\Facebook')){
                return;
            }
            $config = [];
            if(defined('FACEBOOK_APP_ID')){
                $config['app_id'] = FACEBOOK_APP_ID;
            }
            if(defined('FACEBOOK_APP_SECRET')){
                $config['app_secret'] = FACEBOOK_APP_SECRET;
            }
            $config['default_graph_version'] = 'v2.10';
            $this->fb = new Facebook\Facebook($config);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// protected
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        protected function load(){
            $this->default_role = get_option('default_role', 'subscriber');
            $this->redirect_to = home_url();
            $this->remember = false;
            $this->user_info = new stdClass;
            $this->users_can_register = get_option('users_can_register', false);
            $lib = vidsoe()->facebook()->require();
            if(is_wp_error($lib)){
                return;
            }
            vidsoe()->support_sessions();
            add_action('init', [$this, 'init']);
            add_action('template_redirect', [$this, 'template_redirect']);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function init(){
            add_shortcode('vidsoe_facebook_oauth_link', function($atts, $content = ''){
                $atts = shortcode_atts([
                    'redirect_to' => '',
                ], $atts, 'vidsoe_facebook_oauth_link');
                $url = $this->url($atts['redirect_to']);
                $content = trim($content);
                if('' === $content){
                    $content = 'Sign in with Facebook';
                }
                return '<a href="' . $url . '">' . $content . '</a>';
            });
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function template_redirect(){
            global $wp;
            if('facebook-oauth' !== $wp->request){
                return;
            }
            $this->setup();
            try {
                $helper = $this->fb->getRedirectLoginHelper();
                $accessToken = $helper->getAccessToken();
                $response = $this->fb->get('/me?fields=id,email,first_name,last_name,name', $accessToken->getValue());
                $user = $response->getGraphUser();
        		$response = $this->fb->get('/me/picture?redirect=0&type=large', $accessToken->getValue());
        		$picture = $response->getGraphNode();
                $this->userinfo = [
        			'id' => $user['id'],
        			'name' => $user['name'],
        			'first_name' => $user['first_name'],
        			'last_name' => $user['last_name'],
        			'email' => $user['email'],
        			'picture' => $picture['url'],
        		];
            } catch(Throwable $t){ // Executed only in PHP 7, will not match in PHP 5
        		wp_die($t->getMessage());
        	} catch(Exception $e){ // Executed only in PHP 5, will not be reached in PHP 7
                wp_die($e->getMessage());
        	}
            $this->authenticate();
            if(isset($_SESSION['vidsoe_facebook_oauth_state'])){
                if(wp_http_validate_url($_SESSION['vidsoe_facebook_oauth_state'])){
                    $this->redirect_to = $_SESSION['vidsoe_facebook_oauth_state'];
                }
                unset($_SESSION['vidsoe_facebook_oauth_state']);
            }
            wp_safe_redirect($this->redirect_to);
            exit;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function url($redirect_to = ''){
            if('' === $redirect_to and isset($_GET['redirect_to'])){
                $redirect_to = $_GET['redirect_to'];
            }
            $redirect_to = wp_http_validate_url($redirect_to);
            $this->setup();
            if(null === $this->fb){
                return wp_login_url($redirect_to);
            }
            try {
                if($redirect_to){
                    $_SESSION['vidsoe_facebook_oauth_state'] = $redirect_to;
                }
                $helper = $this->fb->getRedirectLoginHelper();
                $permissions = ['email']; // Optional permissions
                return $helper->getLoginUrl(site_url('facebook-oauth'), $permissions);
            } catch(Throwable $t){ // Executed only in PHP 7, will not match in PHP 5
                return wp_login_url($redirect_to);
            } catch(Exception $e){ // Executed only in PHP 5, will not be reached in PHP 7
                return wp_login_url($redirect_to);
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
