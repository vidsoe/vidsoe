<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

require_once(plugin_dir_path(__FILE__) . 'class-vidsoe.php');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe')){
    function vidsoe($name = ''){
        return ($name ? Vidsoe::has_method($name) : Vidsoe::get_instance());
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$vidsoe_dir = trailingslashit(dirname(dirname(__FILE__)));
foreach(glob($vidsoe_dir . '*', GLOB_ONLYDIR) as $vidsoe_dir){
    if(file_exists($vidsoe_dir . '/methods/methods.php')){
        require_once($vidsoe_dir . '/methods/methods.php');
    }
}
unset($vidsoe_dir);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
