<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

require_once(plugin_dir_path(__FILE__) . 'class-vidsoe-loader.php');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

register_activation_hook(VIDSOE, ['Vidsoe_Loader', 'install']);
register_deactivation_hook(VIDSOE, ['Vidsoe_Loader', 'uninstall']);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$file = trailingslashit(WPMU_PLUGIN_DIR) . 'vidsoe.php';
if(file_exists($file)){
    vidsoe()->on('after_setup_theme', function(){
        $file = get_stylesheet_directory() . '/vidsoe-functions.php';
        if(file_exists($file)){
            require_once($file);
        }
    });
    vidsoe()->on('plugins_loaded', function(){
        vidsoe()->build_update_checker('https://github.com/vidsoe/vidsoe', VIDSOE, 'vidsoe');
    });
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
