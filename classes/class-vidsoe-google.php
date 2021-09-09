<?php

if(!class_exists('Vidsoe_Google')){
    final class Vidsoe_Google extends Vidsoe_Base {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// protected
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        protected function load(){}

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function require(){
            if(class_exists('Google\Client')){
                return true;
            }
            switch(true){
                case is_php_version_compatible('8.0'):
                    $version = '8.0';
                    break;
                case is_php_version_compatible('7.4'):
                    $version = '7.4';
                    break;
                case is_php_version_compatible('7.0'):
                    $version = '7.0';
                    break;
                default:
                    $version = '5.6';
            }
            $dir = vidsoe()->require('https://github.com/googleapis/google-api-php-client/releases/download/v2.10.1/google-api-php-client--PHP' . $version . '.zip');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/vendor/autoload.php');
            return true;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function oauth(){
            if(!class_exists('Vidsoe_Google_OAuth')){
                require_once(plugin_dir_path($this->file) . 'classes/class-vidsoe-google-oauth.php');
            }
            return Vidsoe_Google_OAuth::get_instance($this->file);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
