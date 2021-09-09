<?php

if(!class_exists('Vidsoe_Google_OAuth')){
    final class Vidsoe_Google_OAuth extends Vidsoe_Base {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private $client = null, $default_role = '', $remember = false, $userinfo = null, $users_can_register = false;

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private function authenticate(){
            if(is_user_logged_in()){
                return;
            }
            $email = $this->userinfo->email;
            if(email_exists($email)){
                $user = get_user_by('email', $email);
                $user = vidsoe()->signon_without_password($user->user_login, $this->remember);
                if(is_wp_error($user)){
                    wp_die($user);
                }
                update_user_meta($user->ID, 'google_userinfo', get_object_vars($this->userinfo));
                return;
            }
            if(!$this->users_can_register){
                wp_die(__('<strong>Error</strong>: User registration is currently not allowed.'));
            }
            $email = wp_slash($email);
            $first_name = $this->userinfo->givenName;
            $last_name = $this->userinfo->familyName;
            $name = $this->userinfo->name;
            $password = wp_generate_password();
            $userdata = [
                'display_name' => $name,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'nickname' => $name,
                'role' => $this->default_role,
                'user_email' => $email,
                'user_login' => $email,
                'user_pass' => $password,
            ];
            $user_id = wp_insert_user($userdata);
            if(is_wp_error($user_id)){
                wp_die($user_id);
            }
            update_user_meta($user_id, 'google_userinfo', get_object_vars($this->userinfo));
            $credentials = [
                'user_login' => $email,
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
            if(null !== $this->client){
                return;
            }
            if(!class_exists('Google\Client')){
                return;
            }
            $this->client = new Google\Client;
            $this->client->addScope('email');
            $this->client->addScope('profile');
            if(defined('GOOGLE_OAUTH_CLIENT_ID')){
                $this->client->setClientId(GOOGLE_OAUTH_CLIENT_ID);
            }
            if(defined('GOOGLE_OAUTH_CLIENT_SECRET')){
                $this->client->setClientSecret(GOOGLE_OAUTH_CLIENT_SECRET);
            }
            $this->client->setRedirectUri(site_url('google-oauth'));
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
            $this->userinfo = new stdClass;
            $this->users_can_register = get_option('users_can_register', false);
            $lib = vidsoe()->google()->require();
            if(is_wp_error($lib)){
                return;
            }
            add_action('init', [$this, 'init']);
            add_action('template_redirect', [$this, 'template_redirect']);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function init(){
            add_shortcode('vidsoe_google_oauth_link', function($atts, $content = ''){
                $atts = shortcode_atts([
                    'redirect_to' => '',
                ], $atts, 'vidsoe_google_oauth_link');
                $url = $this->url($atts['redirect_to']);
                $content = trim($content);
                if('' === $content){
                    $content = 'Sign in with Google';
                }
                return '<a href="' . $url . '">' . $content . '</a>';
            });
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function template_redirect(){
            global $wp;
            if('google-oauth' !== $wp->request){
                return;
            }
            $this->setup();
            if(!isset($_GET['code'])){
                wp_die(sprintf(__('Missing parameter(s): %s'), 'code'));
            }
            try {
                $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
                if(array_key_exists('error', $token)){
                    wp_die($token['error_description']);
                }
                $this->client->setAccessToken($token);
                $oauth = new Google\Service\Oauth2($this->client);
                $this->userinfo = $oauth->userinfo->get();
            } catch(Throwable $t){ // Executed only in PHP 7, will not match in PHP 5
                wp_die($t->getMessage());
            } catch(Exception $e){ // Executed only in PHP 5, will not be reached in PHP 7
                wp_die($e->getMessage());
            }
            $this->authenticate();
            $state = isset($_GET['state']) ? wp_http_validate_url(urldecode($_GET['state'])) : false;
            if($state){
                $this->redirect_to = $state;
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
            if(null === $this->client){
                return wp_login_url($redirect_to);
            }
            try {
                if($redirect_to){
                    $this->client->setState(urlencode($redirect_to));
                }
                return $this->client->createAuthUrl();
            } catch(Throwable $t){ // Executed only in PHP 7, will not match in PHP 5
                return wp_login_url($redirect_to);
            } catch(Exception $e){ // Executed only in PHP 5, will not be reached in PHP 7
                return wp_login_url($redirect_to);
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
