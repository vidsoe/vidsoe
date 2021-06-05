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

    	private $admin_notices = [], $file = '', $hooks = [];

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct($file = ''){
            $this->file = $file;
            $this->load();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function add_admin_notice($admin_notice = '', $class = 'error', $is_dismissible = false){
            $md5 = md5($admin_notice);
            if(!array_key_exists($md5, $this->admin_notices)){
                $this->admin_notices[$md5] = $this->admin_notice($admin_notice);
            }
            $this->one('admin_notices', function(){
                if$this->admin_notices){
                    foreach($this->admin_notices as $admin_notice){
                        echo $admin_notice;
                    }
                }
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function admin_notice($admin_notice = '', $class = 'error', $is_dismissible = false){
            if(!in_array($class, ['error', 'info', 'success', 'warning'])){
                $class = 'warning';
            }
            if($is_dismissible){
                $class .= ' is-dismissible';
            }
            return '<div class="notice notice-' . $class . '"><p>' . $admin_notice . '</p></div>';
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // Alias of beaver_builder

    	public function bb(){
            return $this->beaver_builder();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function beaver_builder(){
            if(!class_exists('Vidsoe_Beaver_Builder')){
                require_once(plugin_dir_path($file) . 'classes/class-vidsoe-beaver-builder.php');
            }
            return Vidsoe_Beaver_Builder::get_instance();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // Alias of buddypress

    	public function bp(){
            return $this->buddypress();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function buddypress(){
            if(!class_exists('Vidsoe_BuddyPress')){
                require_once(plugin_dir_path($file) . 'classes/class-vidsoe-buddypress.php');
            }
            return Vidsoe_BuddyPress::get_instance();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function build_update_checker(...$args){
            $library = $this->require()->plugin_update_checker();
            if(is_wp_error($library)){
                return $library;
            }
            return Puc_v4_Factory::buildUpdateChecker(...$args);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // Alias of contact_form_7

    	public function cf7(){
            return $this->contact_form_7();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function contact_form_7(){
            // validar si el plugin está activo, si no regresar wp_error
            if(!class_exists('Vidsoe_Contact_Form_7')){
                require_once(plugin_dir_path($file) . 'classes/class-vidsoe-contact-form-7.php');
            }
            return Vidsoe_Contact_Form_7::get_instance();
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

    	public function facebook(){
            if(!class_exists('Vidsoe_Facebook')){
                require_once(plugin_dir_path($file) . 'classes/class-vidsoe-facebook.php');
            }
            return Vidsoe_Facebook::get_instance();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // Alias of facebook

    	public function fb(){
            return $this->facebook();
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

        public function load(){
            $vidsoe->one('admin_enqueue_scripts', function(){
                wp_enqueue_script('vidsoe', $this->url() . 'assets/vidsoe.js', ['jquery'], filemtime($this->dir() . 'assets/vidsoe.js'), true);
            });
            $vidsoe->one('login_enqueue_scripts', function(){
                wp_enqueue_script('vidsoe', $this->url() . 'assets/vidsoe.js', ['jquery'], filemtime($this->dir() . 'assets/vidsoe.js'), true);
            });
            $vidsoe->one('wp_enqueue_scripts', function(){
                wp_enqueue_script('vidsoe', $this->url() . 'assets/vidsoe.js', ['jquery'], filemtime($this->dir() . 'assets/vidsoe.js'), true);
            });
            vidsoe()->one('mb_settings_pages', [$this, 'mb_settings_pages']);
            vidsoe()->one('rwmb_meta_boxes', [$this, 'rwmb_meta_boxes']);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function mb_settings_pages($settings_pages){
            $settings_pages[] = [
        		'columns' => 1,
        		'icon_url' => 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzNTkuNiAzMjAiPjxkZWZzPjxzdHlsZT4uY2xzLTF7ZmlsbDojZmZmO308L3N0eWxlPjwvZGVmcz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0zODIuNDYsNTExLjMzYTMyLDMyLDAsMCwxLTQuMjcsMTZMMjMwLjQzLDc4My4yNWwwLC4wOGEzMiwzMiwwLDAsMS01NS40NCwwLC41Ni41NiwwLDAsMSwwLS4wOEwyNy4xNSw1MjcuMzNhMzIsMzIsMCwxLDEsNTUuNDEtMzJoMGwuNDQuNzVhLjgzLjgzLDAsMCwwLC4wNy4xM0wyMDIuNjYsNzAzLjM0LDMyMi4zMyw0OTYuMDhjLjEzLS4yNi4yOC0uNTEuNDMtLjc1YTMyLDMyLDAsMCwxLDU5LjcsMTZaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMjIuODcgLTQ3OS4zMykiLz48L3N2Zz4=',
        		'id' => 'vidsoe',
        		'menu_title' => 'Vidsoe',
        		'option_name' => 'vidsoe',
        		'page_title' => __('Dashboard'),
        		'revision' => true,
        		'submenu_title' => __('Dashboard'),
                'submit_button' => 'Vidsoe',
            ];
        	return $settings_pages;
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

    	public function roles(){
            return array_map('translate_user_role', wp_list_pluck(array_reverse(get_editable_roles()), 'name'));
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function rwmb_meta_boxes($meta_boxes){
            $meta_boxes[] = [
    			'fields' => [
    				[
                        'std' => '<p><img alt="Vidsoe" src="' . $this->dir() . 'vidsoe.png" title="Vidsoe"></p><p>Sitios web con la más alta calidad y la mayor capacidad, al mejor precio.</p><p><a class="button" href="https://vidsoe.com" target="_blank">vidsoe.com</a></p>',
                        'type' => 'custom_html',
    				],
    			],
    			'id' => 'vidsoe',
    			'title' => __('Dashboard'),
    		];
            return $meta_boxes;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function url(){
            return plugin_dir_url($this->file);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Alias of vidsoe

if(!function_exists('v')){
    function v(){
        return Vidsoe::get_instance();
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('vidsoe')){
    function vidsoe(){
        return Vidsoe::get_instance();
    }
}
