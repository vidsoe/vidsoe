<?php

if(!class_exists('Vidsoe')){
    final class Vidsoe {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function  __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function  __construct(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private static $classes = [], $instance = null, $methods = [];

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function __call($name, $arguments){
            if(isset(self::$classes[$name])){
                $class = self::$classes[$name];
    			return new $class(...$arguments);
    		} else {
                if(isset(self::$methods[$name])){
                    $method = self::$methods[$name];
        			return call_user_func_array($method, $arguments);
        		} else {
                    if(defined('WP_DEBUG') and WP_DEBUG){
                        wp_die(new WP_Error('fatal_error', 'Call to undefined method Vidsoe::' . $name . '()'));
                    } else {
                        return null; // Silence is golden.
                    }
        		}
    		}
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public static function add_class($name = '', $class = ''){
    		$name = str_replace('-', '_', sanitize_title($name));
    		if($name and class_exists($class)){
    			self::$classes[$name] = $class;
    		}
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public static function add_method($name = '', $method = null){
    		$name = str_replace('-', '_', sanitize_title($name));
    		if($name and is_callable($method)){
    			self::$methods[$name] = $method;
    		}
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public static function get_instance(){
    		if(is_null(self::$instance)){
    			self::$instance = new self;
    		}
    		return self::$instance;
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public static function has_method($name = ''){
            return (isset(self::$classes[$name]) or isset(self::$methods[$name]));
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
