<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe_off')){
    function vidsoe_off($tag = '', $function_to_add = '', $priority = 10){
        return remove_filter($tag, $function_to_add, $priority);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe_on')){
    function vidsoe_on($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1){
        add_filter($tag, $function_to_add, $priority, $accepted_args);
    	return _wp_filter_build_unique_id($tag, $function_to_add, $priority);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe_one')){
    function vidsoe_one($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1){
		static $hooks = [];
		$idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority);
		if($function_to_add instanceof Closure){
			$md5 = vidsoe()->md5_closure($function_to_add);
		} else {
			$md5 = md5($idx);
		}
		if(!isset($hooks[$tag])){
			$hooks[$tag] = [];
		}
		if(!in_array($md5, $hooks[$tag])){
			$hooks[$tag][] = $md5;
			return vidsoe()->on($tag, $function_to_add, $priority, $accepted_args);
		} else {
			return $idx;
		}
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Vidsoe::add_method('off', 'vidsoe_off');
Vidsoe::add_method('on', 'vidsoe_on');
Vidsoe::add_method('one', 'vidsoe_one');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
