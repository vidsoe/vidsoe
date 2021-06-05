<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

require_once(plugin_dir_path(__FILE__) . 'class-vidsoe-datetime.php');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Vidsoe::add_method('current_time', ['Vidsoe_DateTime', 'current_time']);
Vidsoe::add_method('date_convert', ['Vidsoe_DateTime', 'date_convert']);
Vidsoe::add_method('offset_or_tz', ['Vidsoe_DateTime', 'offset_or_tz']);
Vidsoe::add_method('seems_mysql_date', ['Vidsoe_DateTime', 'seems_mysql_date']);
Vidsoe::add_method('timezone', ['Vidsoe_DateTime', 'timezone']);
Vidsoe::add_method('timezone_string', ['Vidsoe_DateTime', 'timezone_string']);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
