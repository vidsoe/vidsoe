<?php
/*
Author: Vidsoe
Author URI: https://github.com/vidsoe
Description: A collection of useful methods for your WordPress plugins or theme's functions.php.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: Vidsoe
Plugin URI: https://github.com/vidsoe/vidsoe
Text Domain: vidsoe
Version: 0.2.15.1
*/

if(defined('ABSPATH')){
    if(!defined('VIDSOE')){
        define('VIDSOE', __FILE__);
        require_once(plugin_dir_path(VIDSOE) . 'loader/load.php');
    }
}
