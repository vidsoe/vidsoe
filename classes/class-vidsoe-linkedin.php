<?php

if(!class_exists('Vidsoe_LinkedIn')){
    final class Vidsoe_LinkedIn extends Vidsoe_Base {

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

        public function oauth(){
            if(!class_exists('Vidsoe_LinkedIn_OAuth')){
                require_once(plugin_dir_path($this->file) . 'classes/class-vidsoe-linkedin-oauth.php');
            }
            return Vidsoe_LinkedIn_OAuth::get_instance($this->file);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
