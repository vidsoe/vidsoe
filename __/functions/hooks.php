<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__did')){
    function __did($hook_name){
        return did_action($hook_name);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__do')){
    function __do($hook_name, ...$args){
        return do_action($hook_name, ...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__filter')){
    function __filter($hook_name, $value, ...$args){
        return apply_filters($hook_name, $value, ...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__has')){
    function __has($hook_name, $callback = false){
        return has_filter($hook_name, $callback);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__off')){
    function __off($hook_name, $callback, $priority = 10){
        return remove_filter($hook_name, $callback, $priority);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__on')){
    function __on($hook_name, $callback, $priority = 10, $accepted_args = 1){
        add_filter($hook_name, $callback, $priority, $accepted_args);
        return _wp_filter_build_unique_id($hook_name, $callback, $priority);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__one')){
    function __one($hook_name, $callback, $priority = 10, $accepted_args = 1){
        if(!array_key_exists('hooks', $GLOBALS['__'])){
            $GLOBALS['__']['hooks'] = [];
        }
        if(!array_key_exists($hook_name, $GLOBALS['__']['hooks'])){
            $GLOBALS['__']['hooks'][$hook_name] = [];
        }
        $idx = _wp_filter_build_unique_id($hook_name, $callback, $priority);
        $md5 = md5($idx);
        if($callback instanceof Closure){
            $md5_closure = __md5_closure($callback);
            if(!is_wp_error($md5_closure)){
                $md5 = $md5_closure;
            }
        }
        if(array_key_exists($md5, $GLOBALS['__']['hooks'][$hook_name])){
            return $GLOBALS['__']['hooks'][$hook_name][$md5];
        } else {
            $GLOBALS['__']['hooks'][$hook_name][$md5] = $idx;
            add_filter($hook_name, $callback, $priority, $accepted_args);
            return $idx;
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
