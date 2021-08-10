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
Version: 0.8.10.1
*/

if(!defined('ABSPATH')){
    echo "Hi there! I'm just a plugin, not much I can do when called directly.";
	exit;
}
require_once(plugin_dir_path(__FILE__) . '__/__.php');
__on('plugins_loaded', function(){
    $fs = __filesystem();
    if(is_wp_error($fs)){
        __add_admin_notice('<strong>Vidsoe</strong>: ' . $fs->get_error_message());
    } else {
        __build_update_checker('https://github.com/vidsoe/vidsoe', __FILE__, 'vidsoe');
        __do('vidsoe');
    }
});
