<?php

if(defined('ABSPATH')){
    $GLOBALS['__'] = [];
    foreach(glob(plugin_dir_path(__FILE__) . 'functions/*.php') as $__file){
        require_once($__file);
    }
    unset($__file);
}
