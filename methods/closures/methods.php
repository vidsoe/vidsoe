<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe_md5_closure')){
    function vidsoe_md5_closure($data = '', $spl_object_hash = false){
        if($data instanceof Closure){
            if(!class_exists('\Opis\Closure\SerializableClosure')){
                require_once(plugin_dir_path(__FILE__) . 'closure-3.6.1/autoload.php');
            }
			$wrapper = new \Opis\Closure\SerializableClosure($data);
			$serialized = serialize($wrapper);
			if(!$spl_object_hash){
				$serialized = str_replace(spl_object_hash($data), 'spl_object_hash', $serialized);
			}
			return md5($serialized);
        }
		return '';
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Vidsoe::add_method('md5_closure', 'vidsoe_md5_closure');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
