<?php

if(!class_exists('Vidsoe')){
    final class Vidsoe {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private static $instance = null;

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public static function get_instance($file = ''){
            if(null === self::$instance){
                if(@is_file($file)){
                    self::$instance = new self($file);
                } else {
                    wp_die(__('File doesn&#8217;t exist?'));
                }
            }
            return self::$instance;
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private $file = '', $hooks = [];

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct($file = ''){
            $this->file = $file;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function add_admin_notice(){

        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function build_update_checker(){

        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function did($tag = ''){
            return did_action($tag);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function dir(){
            return plugin_dir_path($this->file);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function do($tag = '', ...$arg){
            return do_action($tag, ...$arg);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function enqueue(){
            if(!class_exists('Vidsoe_Enqueue')){
                require_once(plugin_dir_path($file) . 'classes/class-vidsoe-enqueue.php');
            }
            return Vidsoe_Enqueue::get_instance();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function error($message = '', $data = ''){
            if(is_wp_error($message)){
                $error = $message;
                if('vidsoe_error' === $error->get_error_code()){
                    return $error;
                }
                $message = $error->get_error_message();
                $data = $error->get_error_data();
            } else {
                $message = (string) $message;
                $message = trim($message);
            }
            if(!$message){
                $message = __('Something went wrong.');
            }
            return new WP_Error('vidsoe_error', $message, $data);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function filesystem(){
            global $wp_filesystem;
            if($wp_filesystem instanceof WP_Filesystem_Direct){
                return true;
            }
            if(!function_exists('get_filesystem_method')){
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            if('direct' !== get_filesystem_method()){
                return $this->error(__('Could not access filesystem.'));
            }
            if(!WP_Filesystem()){
                return $this->error(__('Filesystem error.'));
            }
            return true;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function ksort_deep($data = []){
            if($this->is_array_assoc($data)){
                ksort($data);
                foreach($data as $index => $item){
                    $data[$index] = $this->ksort_deep($item);
                }
            }
            return $data;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function md5($data = null){
            if(is_object($data)){
                if($data instanceof Closure){
                    return $this->md5_closure($data);
                } else {
                    $data = json_decode(wp_json_encode($data), true);
                }
            }
            if(is_array($data)){
                $data = $this->ksort_deep($data);
                $data = maybe_serialize($data);
            }
            return md5($data);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function md5_closure($data = null, $spl_object_hash = false){
            if($data instanceof Closure){
                $wrapper = $this->serializable_closure($data);
                if(is_wp_error($wrapper)){
                    return $wrapper;
                }
                $serialized = maybe_serialize($wrapper);
                if(!$spl_object_hash){
                    $serialized = str_replace(spl_object_hash($data), 'spl_object_hash', $serialized);
                }
                return md5($serialized);
            } else {
                return $this->error(__('Invalid object type.'));
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function md5_to_uuid4($md5 = ''){
            $md5 = (string) $md5;
            if(32 !== strlen($md5)){
                return '';
            }
            return substr($md5, 0, 8) . '-' . substr($md5, 8, 4) . '-' . substr($md5, 12, 4) . '-' . substr($md5, 16, 4) . '-' . substr($md5, 20, 12);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function off($tag = '', $function_to_remove = '', $priority = 10){
            return remove_filter($tag, $function_to_remove, $priority);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function on($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1){
            add_filter($tag, $function_to_add, $priority, $accepted_args);
            return _wp_filter_build_unique_id($tag, $function_to_add, $priority);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function one($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1){
            if(!array_key_exists($tag, $this->hooks)){
                $this->hooks[$tag] = [];
            }
            $idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority);
            $md5 = md5($idx);
            if($function_to_add instanceof Closure){
                $md5_closure = $this->md5_closure($function_to_add);
                if(!is_wp_error($md5_closure)){
                    $md5 = $md5_closure;
                }
            }
            if(array_key_exists($md5, $this->hooks[$tag])){
                return $this->hooks[$tag][$md5];
            } else {
                add_filter($tag, $function_to_add, $priority, $accepted_args);
                $this->hooks[$tag][$md5] = $idx;
                return $idx;
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function url(){
            return plugin_dir_url($this->file);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe')){
    function vidsoe(){
        return Vidsoe::get_instance();
    }
}
