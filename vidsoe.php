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
Plugin URI: https://github.com/vidsoe/vidsoe
Requires at least: 5.6
Requires PHP: 5.6
Text Domain: vidsoe
Version: 0.9.9
*/

defined('ABSPATH') or die('Hi there! I\'m just a plugin, not much I can do when called directly.');
require_once(plugin_dir_path(__FILE__) . 'classes/class-vidsoe-base.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class-vidsoe.php');
require_once(plugin_dir_path(__FILE__) . 'functions.php');
Vidsoe::get_instance(__FILE__);
