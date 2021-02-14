<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe_build_update_checker')){
    function vidsoe_build_update_checker(...$args){
        if(!class_exists('Puc_v4_Factory')){
			require_once(plugin_dir_path(__FILE__) . 'plugin-update-checker-4.10/plugin-update-checker.php');
		}
        return Puc_v4_Factory::buildUpdateChecker(...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Vidsoe::add_method('build_update_checker', 'vidsoe_build_update_checker');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
