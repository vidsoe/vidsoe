<?php

if(!class_exists('Vidsoe_Facebook')){
    final class Vidsoe_Facebook extends Vidsoe_Base {

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
            if(class_exists('Facebook\Facebook')){
                return true;
            }
            $dir = vidsoe()->require('https://github.com/facebookarchive/php-graph-sdk/archive/refs/tags/5.7.0.zip', 'php-graph-sdk-5.7.0');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/src/Facebook/autoload.php');
            return true;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function oauth(){
            if(!class_exists('Vidsoe_Facebook_OAuth')){
                require_once(plugin_dir_path($this->file) . 'classes/class-vidsoe-facebook-oauth.php');
            }
            return Vidsoe_Facebook_OAuth::get_instance($this->file);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
