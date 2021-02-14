<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

require_once(plugin_dir_path(__FILE__) . 'class-vidsoe.php');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe')){
    function vidsoe(){
        return Vidsoe::get_instance();
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$vidsoe_dir = trailingslashit(dirname(dirname(__FILE__)));
foreach(glob($vidsoe_dir . '*', GLOB_ONLYDIR) as $vidsoe_dir){
    if(file_exists($vidsoe_dir . '/methods.php')){
        require_once($vidsoe_dir . '/methods.php');
    }
}
unset($vidsoe_dir);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
