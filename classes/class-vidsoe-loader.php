<?php

if(!class_exists('Vidsoe_Loader')){
    final class Vidsoe_Loader {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function load($file = ''){
            require_once(plugin_dir_path($file) . 'classes/class-vidsoe.php');
            $vidsoe = Vidsoe::get_instance($file);
            $filesystem = $vidsoe->filesystem();
            if(is_wp_error($filesystem)){
                $vidsoe->add_admin_notice('<strong>' . __('Error') . '</strong>: ' . $filesystem->get_error_message());
            }
            $vidsoe->build_update_checker('https://github.com/vidsoe/vidsoe', $file, 'vidsoe');
            $vidsoe->on('admin_enqueue_scripts', function(){
                vidsoe()->enqueue()->functions();
            });
            $vidsoe->on('wp_enqueue_scripts', function(){
                vidsoe()->enqueue()->functions();
            });
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
