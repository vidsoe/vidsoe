<?php

if(defined('ABSPATH')){
    $GLOBALS['__'] = [];
    define('__FILE', __FILE__);
    foreach(glob(plugin_dir_path(__FILE) . 'functions/*.php') as $__file){
        require_once($__file);
    }
    unset($__file);
}
