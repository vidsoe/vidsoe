<?php

if(!class_exists('Vidsoe_LinkedIn_OAuth')){
    final class Vidsoe_LinkedIn_OAuth extends Vidsoe_Base {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private $access_token = '', $default_role = '', $remember = false, $userinfo = null, $users_can_register = false;

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
                update_user_meta($user->ID, 'linkedin_userinfo', $this->userinfo);
                return;
            }
            if(!$this->users_can_register){
                wp_die(__('<strong>Error</strong>: User registration is currently not allowed.'));
            }
            $email = wp_slash($email);
            $first_name = $this->userinfo['first_name'];
            $last_name = $this->userinfo['last_name'];
            $name = $this->userinfo['first_name'] . ' ' . $this->userinfo['last_name'];
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
            update_user_meta($user_id, 'linkedin_userinfo', $this->userinfo);
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

        private function get_email(){
            $args = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token,
                ],
            ];
            $url = 'https://api.linkedin.com/v2/emailAddress';
            $response = vidsoe()->remote($url, $args)->get([
                'projection' => '(elements*(handle~))',
				'q' => 'members',
            ]);
            if(!$response->success){
                return $response->to_wp_error();
            }
            return $response->data['elements'][0]['handle~']['emailAddress'];
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private function get_profile(){
            $args = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token,
                ],
            ];
            $url = 'https://api.linkedin.com/v2/me';
            $response = vidsoe()->remote($url, $args)->get([
                'projection' => '(id,firstName,lastName,profilePicture(displayImage~:playableStreams))',
            ]);
            if(!$response->success){
                return $response->to_wp_error();
            }
            $country = $response->data['firstName']['preferredLocale']['country'];
            $language = $response->data['firstName']['preferredLocale']['language'];
            $first_name = $response->data['firstName']['localized'][$language . '_' . $country];
            $id = $response->data['id'];
            $elements = $response->data['profilePicture']['displayImage~']['elements'];
            $index = count($elements) - 1;
            $identifier = $elements[$index]['identifiers'][0]['identifier'];
            $country = $response->data['lastName']['preferredLocale']['country'];
            $language = $response->data['lastName']['preferredLocale']['language'];
            $last_name = $response->data['lastName']['localized'][$language . '_' . $country];
            $media_type = $elements[$index]['identifiers'][0]['mediaType'];
            return [
                'first_name' => $first_name,
				'id' => $id,
				'identifier' => $identifier,
				'last_name' => $last_name,
                'media_type' => $media_type,
            ];
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
            add_action('init', [$this, 'init']);
            add_action('template_redirect', [$this, 'template_redirect']);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function init(){
            add_shortcode('vidsoe_linkedin_oauth_link', function($atts, $content = ''){
                $atts = shortcode_atts([
                    'redirect_to' => '',
                ], $atts, 'vidsoe_linkedin_oauth_link');
                $url = $this->url($atts['redirect_to']);
                $content = trim($content);
                if('' === $content){
                    $content = 'Sign in with LinkedIn';
                }
                return '<a href="' . $url . '">' . $content . '</a>';
            });
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function template_redirect(){
            global $wp;
            if('linkedin-oauth' !== $wp->request){
                return;
            }
            if(!isset($_GET['code'])){
                wp_die(sprintf(__('Missing parameter(s): %s'), 'code'));
            }
            $args = [];
            if(defined('LINKEDIN_CLIENT_ID')){
                $args['client_id'] = LINKEDIN_CLIENT_ID;
            }
            if(defined('LINKEDIN_CLIENT_SECRET')){
                $args['client_secret'] = LINKEDIN_CLIENT_SECRET;
            }
            $args['code'] = $_GET['code'];
            $args['grant_type'] = 'authorization_code';
            $args['redirect_uri'] = site_url('linkedin-oauth');
            $url = 'https://www.linkedin.com/oauth/v2/accessToken';
            $response = vidsoe()->remote($url)->post($args);
            if(!$response->success){
                wp_die($response->to_wp_error());
            }
            $this->access_token = $response->data['access_token'];
            $email = $this->get_email();
            if(is_wp_error($email)){
                wp_die($email);
            }
            $profile = $this->get_profile();
            if(is_wp_error($profile)){
                wp_die($profile);
            }
            $profile['email'] = $email;
            $this->userinfo = $profile;
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
            $args = [];
            if(defined('LINKEDIN_CLIENT_ID')){
                $args['client_id'] = LINKEDIN_CLIENT_ID;
            }
            $args['redirect_uri'] = site_url('linkedin-oauth');
            $args['response_type'] = 'code';
            $args['scope'] = 'r_emailaddress r_liteprofile';
            if($redirect_to){
                $args['state'] = urlencode($redirect_to);
            }
            $url = 'https://www.linkedin.com/oauth/v2/authorization';
            return add_query_arg($args, $url);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
