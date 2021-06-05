<?php

if(!class_exists('Vidsoe_HTTP')){
    class Vidsoe_HTTP {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // public static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function download($url = '', $args = [], $parent = 0){
            return vidsoe()->request($url, $args)->download($parent);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function download_and_unzip($url = '', $dir = ''){
            $wp_upload_dir = wp_get_upload_dir();
            if(strpos($dir, $wp_upload_dir['basedir']) !== 0){
                return vidsoe()->error('http_request_failed', 'Destination directory for file streaming is not valid.');
            }
            if(is_dir($dir)){
                return true;
            }
            if(!wp_mkdir_p($dir)){
                return vidsoe()->error('http_request_failed', 'Could not create directory.');
            }
            $attachment_id = vidsoe()->request($url)->download();
            if(is_wp_error($attachment_id)){
                return $attachment_id;
            }
            if(get_post_mime_type($attachment_id) != 'application/zip'){
                return vidsoe()->error('http_request_failed', 'Filetype is not valid.');
            }
            $file = get_attached_file($attachment_id);
            if(!file_exists($file)){
                return vidsoe()->error('http_request_failed', 'Filename is not valid.');
            }
            if(!function_exists('unzip_file')){
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            $result = unzip_file($file, $dir);
            if(is_wp_error($result)){
                return $result;
            }
            return true;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function prepare(...$args){
            global $wpdb;
            if(!$args){
                return '';
            }
            if(strpos($args[0], '%') !== false and count($args) > 1){
                return str_replace("'", '', $wpdb->remove_placeholder_escape($wpdb->prepare(...$args)));
            } else {
                return $args[0];
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function request($url = '', $args = []){
            if(!class_exists('Vidsoe_Request')){
                require_once(plugin_dir_path(__FILE__) . 'class-vidsoe-request.php');
            }
            return new Vidsoe_Request($url, $args);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function response($response = null){
            if(!class_exists('Vidsoe_Response')){
                require_once(plugin_dir_path(__FILE__) . 'class-vidsoe-response.php');
            }
            return new Vidsoe_Response($response);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function response_error($message = '', $code = 0, $data = ''){
            if(!$code){
                $code = 500;
            }
            if(!$message){
                $message = get_status_header_desc($code);
            }
            if(!$message){
                $message = __('Something went wrong.');
            }
            $success = false;
            return vidsoe()->response(compact('code', 'data', 'message', 'success'));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function response_success($data = '', $code = 0, $message = ''){
            if(!$code){
                $code = 200;
            }
            if(!$message){
                $message = get_status_header_desc($code);
            }
            if(!$message){
                $message = 'OK';
            }
            $success = true;
            return vidsoe()->response(compact('code', 'data', 'message', 'success'));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function sanitize_timeout($timeout = 0){
            $timeout = absint($timeout);
            $max_execution_time = absint(ini_get('max_execution_time'));
            if($max_execution_time){
                if(!$timeout or $timeout > $max_execution_time){
                    $timeout = $max_execution_time; // Prevents timeout error
                }
            }
            return apply_filters('vidsoe_sanitize_timeout', $timeout);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function seems_response($response = []){
            return vidsoe()->array_keys_exist(['code', 'data', 'message', 'success'], $response);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function seems_successful($code = 0){
            if(!is_numeric($code)){
                if($code instanceof Vidsoe_Response){
                    $code = $code->code;
                } else {
                    return false;
                }
            } else {
                $code = absint($code);
            }
            return ($code >= 200 and $code < 300);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function seems_wp_http_requests_response($response = []){
            return (vidsoe()->array_keys_exist(['headers', 'body', 'response', 'cookies', 'filename', 'http_response'], $response) and ($response['http_response'] instanceof WP_HTTP_Requests_Response));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function support_authorization_header(){
            vidsoe()->one('mod_rewrite_rules', function($rules){
                return str_replace("RewriteEngine On\n", "RewriteEngine On\nRewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]\n", $rules);
            });
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
