<?php
/*
Author: Vidsoe
Author URI: https://vidsoe.com
Description: Sitios web con la más alta calidad y la mayor capacidad, al mejor precio.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: Vidsoe
Plugin URI: https://vidsoe.com
Requires at least: 5.7
Requires PHP: 5.6
Text Domain: vidsoe
Version: 1.6.3
*/

if(defined('ABSPATH')){
    require_once(plugin_dir_path(__FILE__) . 'classes/class-vidsoe-loader.php');
    Vidsoe_Loader::load(__FILE__);
}
