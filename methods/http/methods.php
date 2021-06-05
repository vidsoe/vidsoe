<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

require_once(plugin_dir_path(__FILE__) . 'class-vidsoe-html.php');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Vidsoe::add_method('download', ['Vidsoe_HTML', 'download']);
Vidsoe::add_method('download_and_unzip', ['Vidsoe_HTML', 'download_and_unzip']);
Vidsoe::add_method('prepare', ['Vidsoe_HTML', 'prepare']);
Vidsoe::add_method('request', ['Vidsoe_HTML', 'request']);
Vidsoe::add_method('response', ['Vidsoe_HTML', 'response']);
Vidsoe::add_method('response_error', ['Vidsoe_HTML', 'response_error']);
Vidsoe::add_method('response_success', ['Vidsoe_HTML', 'response_success']);
Vidsoe::add_method('sanitize_timeout', ['Vidsoe_HTML', 'sanitize_timeout']);
Vidsoe::add_method('seems_response', ['Vidsoe_HTML', 'seems_response']);
Vidsoe::add_method('seems_successful', ['Vidsoe_HTML', 'seems_successful']);
Vidsoe::add_method('seems_wp_http_requests_response', ['Vidsoe_HTML', 'seems_wp_http_requests_response']);
Vidsoe::add_method('support_authorization_header', ['Vidsoe_HTML', 'support_authorization_header']);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
