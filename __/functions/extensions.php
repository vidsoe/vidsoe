<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__meta_table')){
    function __meta_table($table = ''){
        if(!class_exists('__Meta_Table')){
            $dir = __package('https://github.com/vidsoe/meta-table/archive/refs/tags/meta-table-1.2.3.zip', 'meta-table-1.2.3');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/meta-table.php');
        }
        return __Meta_Table::get_instance($table);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
