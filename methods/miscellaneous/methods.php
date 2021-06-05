<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

require_once(plugin_dir_path(__FILE__) . 'class-vidsoe-miscellaneous.php');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Vidsoe::add_method('add_admin_notice', ['Vidsoe_Miscellaneous', 'add_admin_notice']);
Vidsoe::add_method('are_plugins_active', ['Vidsoe_Miscellaneous', 'are_plugins_active']);
Vidsoe::add_method('array_keys_exist', ['Vidsoe_Miscellaneous', 'array_keys_exist']);
Vidsoe::add_method('base64_urldecode', ['Vidsoe_Miscellaneous', 'base64_urldecode']);
Vidsoe::add_method('base64_urlencode', ['Vidsoe_Miscellaneous', 'base64_urlencode']);
Vidsoe::add_method('clone_role', ['Vidsoe_Miscellaneous', 'clone_role']);
Vidsoe::add_method('current_screen_in', ['Vidsoe_Miscellaneous', 'current_screen_in']);
Vidsoe::add_method('current_screen_is', ['Vidsoe_Miscellaneous', 'current_screen_is']);
Vidsoe::add_method('destroy_other_sessions', ['Vidsoe_Miscellaneous', 'destroy_other_sessions']);
Vidsoe::add_method('format_function', ['Vidsoe_Miscellaneous', 'format_function']);
Vidsoe::add_method('get_memory_size', ['Vidsoe_Miscellaneous', 'get_memory_size']);
Vidsoe::add_method('is_array_assoc', ['Vidsoe_Miscellaneous', 'is_array_assoc']);
Vidsoe::add_method('is_doing_heartbeat', ['Vidsoe_Miscellaneous', 'is_doing_heartbeat']);
Vidsoe::add_method('is_plugin_active', ['Vidsoe_Miscellaneous', 'is_plugin_active']);
Vidsoe::add_method('is_plugin_deactivating', ['Vidsoe_Miscellaneous', 'is_plugin_deactivating']);
Vidsoe::add_method('is_post_revision_or_auto_draft', ['Vidsoe_Miscellaneous', 'is_post_revision_or_auto_draft']);
Vidsoe::add_method('ksort_deep', ['Vidsoe_Miscellaneous', 'ksort_deep']);
Vidsoe::add_method('md5', ['Vidsoe_Miscellaneous', 'md5']);
Vidsoe::add_method('md5_to_uuid4', ['Vidsoe_Miscellaneous', 'md5_to_uuid4']);
Vidsoe::add_method('new', ['Vidsoe_Miscellaneous', 'new']);
Vidsoe::add_method('post_type_labels', ['Vidsoe_Miscellaneous', 'post_type_labels']);
Vidsoe::add_method('remove_whitespaces', ['Vidsoe_Miscellaneous', 'remove_whitespaces']);
Vidsoe::add_method('signon_without_password', ['Vidsoe_Miscellaneous', 'signon_without_password']);
Vidsoe::add_method('support_sessions', ['Vidsoe_Miscellaneous', 'support_sessions']);
Vidsoe::add_method('upload_basedir', ['Vidsoe_Miscellaneous', 'upload_basedir']);
Vidsoe::add_method('upload_baseurl', ['Vidsoe_Miscellaneous', 'upload_baseurl']);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
