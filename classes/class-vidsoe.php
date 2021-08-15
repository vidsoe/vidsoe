<?php

if(!class_exists('Vidsoe')){
    final class Vidsoe {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private $admin_notices = [], $external_rules = [], $hooks = [], $image_sizes = [], $rewrite_rules = [];

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct($file = ''){
            $this->file = $file;
            add_action('wp_enqueue_scripts', function(){
                $src = plugin_dir_url($this->file) . 'assets/vidsoe.js';
                $ver = filemtime(plugin_dir_path($this->file) . 'assets/vidsoe.js');
                wp_enqueue_script('vidsoe', $src, ['jquery'], $ver, true);
            });
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private function extension($extension = '', $version = ''){
            $class = 'vidsoe_' . $this->canonicalize($extension);
            if(!class_exists($class)){
                $directory = $this->require("https://github.com/vidsoe/vidsoe-{$extension}/archive/refs/tags/vidsoe-{$extension}-{$version}.zip", "vidsoe-{$extension}-{$version}");
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . "/vidsoe-{$extension}.php");
            }
            return call_user_func([$class, 'get_instance']);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private static $instance = null;

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function add_admin_notice($admin_notice = '', $class = 'error', $is_dismissible = false){
            $admin_notice = $this->admin_notice($admin_notice, $class, $is_dismissible);
            $md5 = md5($admin_notice);
            if(!array_key_exists($md5, $this->admin_notices)){
                $this->admin_notices[$md5] = $admin_notice;
            }
            $this->one('admin_notices', function(){
                if(!$this->admin_notices){
                    return;
                }
                foreach($this->admin_notices as $admin_notice){
                    echo $admin_notice;
                }
            });
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function add_external_rule($regex = '', $query = ''){
    		$rule = [
    			'query' => $query,
                'regex' => $regex,
    		];
    		$md5 = $this->md5($rule);
    		if(!array_key_exists($md5, $this->external_rules)){
    			$this->external_rules[$md5] = $rule;
    		}
    		$this->one('admin_init', function(){
                if(!$this->external_rules){
                    return;
                }
                if(!current_user_can('manage_options')){
                    return;
                }
                $add_admin_notice = false;
                foreach($this->external_rules as $rule){
                    $regex = str_replace(home_url('/'), '', $rule['regex']);
                    $query = str_replace(home_url('/'), '', $rule['query']);
                    if(!$this->external_rule_exists($regex, $query)){
                        $add_admin_notice = true;
                        break;
                    }
                }
                if($add_admin_notice){
                    $this->add_admin_notice(sprintf(__('You should update your %s file now.'), '<code>.htaccess</code>') . ' ' . sprintf('<a href="%s">%s</a>', esc_url(admin_url('options-permalink.php')), __('Flush permalinks')) . '.');
                }
    		});
    		$this->one('generate_rewrite_rules', function($wp_rewrite){
    			if(!$this->external_rules){
    				return;
    			}
    			foreach($this->external_rules as $rule){
    				$regex = str_replace(home_url('/'), '', $rule['regex']);
    				$query = str_replace(home_url('/'), '', $rule['query']);
    				$wp_rewrite->add_external_rule($regex, $query);
    			}
    		});
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function add_image_size($name = '', $width = 0, $height = 0, $crop = false){
    		$size = sanitize_title($name);
            if(!array_key_exists($size, $this->image_sizes)){
                $this->image_sizes[$size] = $name;
    			add_image_size($size, $width, $height, $crop);
            }
            $this->one('image_size_names_choose', function($sizes){
                if(!$this->image_sizes){
                    return $sizes;
                }
    			foreach($this->image_sizes as $size => $name){
    				$sizes[$size] = $name;
    			}
                return $sizes;
            });
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function add_larger_image_sizes(){
            $this->add_image_size('HD', 1280, 1280);
            $this->add_image_size('Full HD', 1920, 1920);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function admin_notice($admin_notice = '', $class = 'warning', $is_dismissible = false){
            if(!in_array($class, ['error', 'info', 'success', 'warning'])){
                $class = 'warning';
            }
            if($is_dismissible){
                $class .= ' is-dismissible';
            }
            return '<div class="notice notice-' . $class . '"><p>' . $admin_notice . '</p></div>';
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function are_plugins_active($plugins = []){
            if(!is_array($plugins)){
                return false;
            }
            foreach($plugins as $plugin){
                if(!$this->is_plugin_active($plugin)){
                    return false;
                }
            }
            return true;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function array_keys_exist($keys = [], $array = []){
            if(!is_array($keys) or !is_array($array)){
                return false;
            }
            foreach($keys as $key){
                if(!array_key_exists($key, $array)){
                    return false;
                }
            }
            return true;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function attachment_url_to_postid($url = ''){
            $post_id = $this->guid_to_postid($url);
            if($post_id){
                return $post_id;
            }
            preg_match('/^(.+)(\-\d+x\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // resized
            if($matches){
                $url = $matches[1];
                if(isset($matches[3])){
                    $url .= $matches[3];
                }
                $post_id = $this->guid_to_postid($url);
                if($post_id){
                    return $post_id;
                }
            }
            preg_match('/^(.+)(\-scaled)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // scaled
            if($matches){
                $url = $matches[1];
                if(isset($matches[3])){
                    $url .= $matches[3];
                }
                $post_id = $this->guid_to_postid($url);
                if($post_id){
                    return $post_id;
                }
            }
            preg_match('/^(.+)(\-e\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // edited
            if($matches){
                $url = $matches[1];
                if(isset($matches[3])){
                    $url .= $matches[3];
                }
                $post_id = $this->guid_to_postid($url);
                if($post_id){
                    return $post_id;
                }
            }
            return 0;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function base64_urldecode($data = '', $strict = false){
            return base64_decode(strtr($data, '-_', '+/'), $strict);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function base64_urlencode($data = ''){
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function build_update_checker(...$args){
            if(!class_exists('Puc_v4_Factory')){
                $directory = $this->require('https://github.com/YahnisElsts/plugin-update-checker/archive/refs/tags/v4.11.zip', 'plugin-update-checker-4.11');
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . '/plugin-update-checker.php');
            }
            return Puc_v4_Factory::buildUpdateChecker(...$args);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function call_prefixed_methods($object = null, $methods = [], $prefix = ''){
            if(!is_object($object)){
                return;
            }
            $methods = (array) $methods;
            if(!$methods){
                return;
            }
            foreach($methods as $method){
                $method = $prefix . $this->canonicalize($method);
                if(is_callable($object, $method)){
                    call_user_func([$object, $method]);
                }
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function canonicalize($key = ''){
            $key = $this->remove_whitespaces($key);
            $key = str_replace(' ', '', $key);
            return WP_REST_Request::canonicalize_header_name($key);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function cf7(){
            if(!$this->is_plugin_active('contact-form-7/wp-contact-form-7.php')){
                return $this->error('This method requires Contact Form 7.');
            }
            return $this->extension('contact-form-7', '1.2.3');
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function clone_role($source = '', $destination = '', $display_name = ''){
            $role = get_role($source);
            if(is_null($role)){
                return null;

            }
            $destination = $this->canonicalize($destination);
            return add_role($destination, $display_name, $role->capabilities);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function copy($source = '', $destination = '', $overwrite = false, $mode = false){
            global $wp_filesystem;
            $fs = $this->filesystem();
            if(is_wp_error($fs)){
                return $fs;
            }
            if(!$wp_filesystem->copy($source, $destination, $overwrite, $mode)){
                return $this->error(sprintf(__('The uploaded file could not be moved to %s.'), $destination));
            }
            return $destination;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function current_screen_in($ids = []){
            global $current_screen;
            if(!is_array($ids)){
                return false;
            }
            if(!isset($current_screen)){
                return false;
            }
            return in_array($current_screen->id, $ids);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function current_screen_is($id = ''){
            global $current_screen;
            if(!is_string($id)){
                return false;
            }
            if(!isset($current_screen)){
                return false;
            }
            return ($current_screen->id === $id);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function current_time($type = 'U', $offset_or_tz = ''){ // If $offset_or_tz is an empty string, the output is adjusted with the GMT offset in the WordPress option.
            if('timestamp' === $type){
                $type = 'U';
            }
            if('mysql' === $type){
                $type = 'Y-m-d H:i:s';
            }
            $timezone = $offset_or_tz ? $this->timezone($offset_or_tz) : wp_timezone();
            $datetime = new DateTime('now', $timezone);
            return $datetime->format($type);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function custom_login_logo($attachment_id = 0, $half = true){
            if(!wp_attachment_is_image($attachment_id)){
                return;
            }
            $this->one('login_enqueue_scripts', function() use($attachment_id, $half){
                $custom_logo = wp_get_attachment_image_src($attachment_id, 'medium');
                $height = $custom_logo[2];
                $width = $custom_logo[1];
                if($half){
                    $height = $height / 2;
                    $width = $width / 2;
                } ?>
                <style type="text/css">
                    #login h1 a,
                    .login h1 a {
                        background-image: url(<?php echo $custom_logo[0]; ?>);
                        background-size: <?php echo $width; ?>px <?php echo $height; ?>px;
                        height: <?php echo $height; ?>px;
                        width: <?php echo $width; ?>px;
                    }
                </style><?php
    		});
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function date_convert($string = '', $fromtz = '', $totz = '', $format = 'Y-m-d H:i:s'){
            $datetime = date_create($string, $this->timezone($fromtz));
            if($datetime === false){
                return gmdate($format, 0);
            }
            return $datetime->setTimezone($this->timezone($totz))->format($format);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function destroy_other_sessions(){
            $this->one('init', 'wp_destroy_other_sessions');
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function download($url = '', $args = []){
            $args = wp_parse_args($args, [
                'filename' => '',
                'timeout' => 300,
            ]);
            if($args['filename']){
                if(!$this->in_uploads($args['filename'])){
                    return $this->error(sprintf(__('Unable to locate needed folder (%s).'), __('The uploads directory')));
                }
            } else {
                $download_dir = $this->download_dir();
                if(is_wp_error($download_dir)){
                    return $download_dir;
                }
                $args['filename'] = trailingslashit($download_dir) . $this->filename($url);
            }
            $args['stream'] = true;
            $args['timeout'] = $this->sanitize_timeout($args['timeout']);
            $response = $this->remote($url, $args)->get();
            if(!$response->success){
                @unlink($args['filename']);
                return $response->to_wp_error();
            }
            return $args['filename'];
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function download_dir(){
            $upload_dir = wp_get_upload_dir();
            $dir = $upload_dir['basedir'] . '/vidsoe-downloads';
            if(!wp_mkdir_p($dir)){
                return $this->error(__('Could not create directory.'));
            }
            if(!wp_is_writable($dir)){
                return $this->error(__('Destination directory for file streaming does not exist or is not writable.'));
            }
            return $dir;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function download_url($file = ''){
            $upload_dir = wp_get_upload_dir();
            if('' !== $file){
                return str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file);
            } else {
                return $upload_dir['baseurl'] . '/vidsoe-downloads';
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function enqueue_floating_labels(){
            $this->one('wp_enqueue_scripts', function(){
                $src = plugin_dir_url($this->file) . 'assets/floating-labels.js';
                $ver = filemtime(plugin_dir_path($this->file) . 'assets/floating-labels.js');
                wp_enqueue_script('vidsoe-floating-labels', $src, ['vidsoe'], $ver, true);
                wp_add_inline_script('vidsoe-floating-labels', 'vidsoe.floating_labels.init();');
                $src = plugin_dir_url($this->file) . 'assets/floating-labels.css';
                $ver = filemtime(plugin_dir_path($this->file) . 'assets/floating-labels.css');
                wp_enqueue_style('vidsoe-floating-labels', $src, [], $ver);
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function error($message = '', $data = ''){
            if(is_wp_error($message)){
                $data = $message->get_error_data();
                $message = $message->get_error_message();
            }
            if(!$message){
                $message = __('Something went wrong.');
            }
            if(!class_exists('Vidsoe_Error')){
                require_once(plugin_dir_path($this->file) . 'classes/class-vidsoe-error.php');
            }
            return new Vidsoe_Error('vidsoe_error', $message, $data);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function external_rule_exists($regex = '', $query = ''){
            if(!$this->rewrite_rules){
                $rewrite_rules = extract_from_markers(get_home_path() . '.htaccess', 'WordPress');
                $this->rewrite_rules = array_filter($rewrite_rules);
            }
            $regex = str_replace(home_url('/'), '', $regex);
        	$regex = str_replace('.+?', '.+', $regex);
        	$query = str_replace(home_url('/'), '', $query);
        	$rule = 'RewriteRule ^' . $regex . ' ' . $this->home_root() . $query . ' [QSA,L]';
        	return in_array($rule, $this->rewrite_rules);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function file_get_html(...$args){
            if(!class_exists('simple_html_dom')){
                $directory = $this->require('https://github.com/simplehtmldom/simplehtmldom/archive/refs/tags/1.9.1.zip', 'simplehtmldom-1.9.1');
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . '/simple_html_dom.php');
            }
            return file_get_html(...$args);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function filename($filename = ''){
            return preg_replace('/\?.*/', '', wp_basename($filename));
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

        public function first_p($text = '', $dot = true){
            return $this->one_p($text, $dot, 'first');
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function fix_audio_video_extensions(){
            $this->one('wp_check_filetype_and_ext', function($wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime){
                if($wp_check_filetype_and_ext['ext'] and $wp_check_filetype_and_ext['type']){
                    return $wp_check_filetype_and_ext;
                }
                if(0 === strpos($real_mime, 'audio/') or 0 === strpos($real_mime, 'video/')){
                    $filetype = wp_check_filetype($filename);
                    if(in_array(substr($filetype['type'], 0, strcspn($filetype['type'], '/')), ['audio', 'video'])){
                        $wp_check_filetype_and_ext['ext'] = $filetype['ext'];
                        $wp_check_filetype_and_ext['type'] = $filetype['type'];
                    }
                }
                return $wp_check_filetype_and_ext;
            }, 10, 5);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function get_memory_size(){
            if(!function_exists('exec')){
                return 0;
            }
            exec('free -b', $output);
            $output = $this->remove_whitespaces($output[1]);
            $output = explode(' ', $output);
            return (int) $output[1];
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function guid_to_postid($guid = ''){
            global $wpdb;
            $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s", $guid);
            $post_id = $wpdb->get_var($query);
            if(null === $post_id){
                return 0;
            }
    		return (int) $post_id;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function home_root(){
            $home_root = parse_url(home_url());
        	if(isset($home_root['path'])){
        		$home_root = trailingslashit($home_root['path']);
        	} else {
        		$home_root = '/';
        	}
        	return $home_root;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function in_uploads($file = ''){
            $upload_dir = wp_get_upload_dir();
            return (0 === strpos($file, $upload_dir['basedir']));
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function is_array_assoc($array = []){
            if(!is_array($array)){
                return false;
            }
            return (array_keys($array) !== range(0, count($array) - 1));
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function is_doing_heartbeat(){
            return (defined('DOING_AJAX') and DOING_AJAX and isset($_POST['action']) and $_POST['action'] == 'heartbeat');
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function is_extension_allowed($extension = ''){
            foreach(wp_get_mime_types() as $exts => $mime){
                if(preg_match('!^(' . $exts . ')$!i', $extension)){
                    return true;
                }
            }
            return false;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function is_plugin_active($plugin = ''){
            if(!function_exists('is_plugin_active')){
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            return is_plugin_active($plugin);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function is_plugin_deactivating($file = ''){
            global $pagenow;
            if(!is_file($file)){
                return false;
            }
            return (is_admin() and 'plugins.php' === $pagenow and isset($_GET['action'], $_GET['plugin']) and 'deactivate' === $_GET['action'] and plugin_basename($file) === $_GET['plugin']);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function is_post_revision_or_auto_draft($post = null){
            return (wp_is_post_revision($post) or 'auto-draft' === get_post_status($post));
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function jwt_decode(...$args){
            if(!class_exists('Firebase\JWT\JWT')){
                $directory = $this->require('https://github.com/firebase/php-jwt/archive/refs/tags/v5.4.0.zip', 'php-jwt-5.4.0');
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . '/src/BeforeValidException.php');
                require_once($directory . '/src/ExpiredException.php');
                require_once($directory . '/src/JWK.php');
                require_once($directory . '/src/JWT.php');
                require_once($directory . '/src/SignatureInvalidException.php');
            }
            return Firebase\JWT\JWT::decode(...$args);
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function jwt_encode(...$args){
            if(!class_exists('Firebase\JWT\JWT')){
                $directory = $this->require('https://github.com/firebase/php-jwt/archive/refs/tags/v5.4.0.zip', 'php-jwt-5.4.0');
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . '/src/BeforeValidException.php');
                require_once($directory . '/src/ExpiredException.php');
                require_once($directory . '/src/JWK.php');
                require_once($directory . '/src/JWT.php');
                require_once($directory . '/src/SignatureInvalidException.php');
            }
            return Firebase\JWT\JWT::encode(...$args);
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

        public function last_p($text = '', $dot = true){
            return $this->one_p($text, $dot, 'last');
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function maybe_generate_attachment_metadata($attachment_id = 0){
            $attachment = get_post($attachment_id);
    		if(null === $attachment){
    			return false;
    		}
            if('attachment' !== $attachment->post_type){
    			return false;
    		}
    		wp_raise_memory_limit('image');
            if(!function_exists('wp_generate_attachment_metadata')){
                require_once(ABSPATH . 'wp-admin/includes/image.php');
            }
    		wp_maybe_generate_attachment_metadata($attachment);
    		return true;
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
            if(32 === strlen($md5)){
                return substr($md5, 0, 8) . '-' . substr($md5, 8, 4) . '-' . substr($md5, 12, 4) . '-' . substr($md5, 16, 4) . '-' . substr($md5, 20, 12);
            }
            return $this->error(__('Invalid data provided.'));
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function move_uploaded_file($tmp_name = ''){
            global $wp_filesystem;
            $fs = $this->filesystem();
            if(is_wp_error($fs)){
                return $fs;
            }
            if(!$wp_filesystem->exists($tmp_name)){
                return $this->error(__('File does not exist! Please double check the name and try again.'));
            }
            $upload_dir = wp_upload_dir();
            $original_filename = wp_basename($tmp_name);
            $filename = wp_unique_filename($upload_dir['path'], $original_filename);
            $file = trailingslashit($upload_dir['path']) . $filename;
            $result = $this->copy($tmp_name, $file);
            if(is_wp_error($result)){
                return $result;
            }
            return $file;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function off($hook_name, $callback, $priority = 10){
            return remove_filter($hook_name, $callback, $priority);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function offset_or_tz($offset_or_tz = ''){ // Default GMT offset or timezone string. Must be either a valid offset (-12 to 14) or a valid timezone string.
            if(is_numeric($offset_or_tz)){
                return [
                    'gmt_offset' => $offset_or_tz,
                    'timezone_string' => '',
                ];
            }
            if(preg_match('/^UTC[+-]/', $offset_or_tz)){ // Map UTC+- timezones to gmt_offsets and set timezone_string to empty.
                return [
                    'gmt_offset' => (int) preg_replace('/UTC\+?/', '', $offset_or_tz),
                    'timezone_string' => '',
                ];
            }
            if(in_array($offset_or_tz, timezone_identifiers_list())){
                return [
                    'gmt_offset' => 0,
                    'timezone_string' => $offset_or_tz,
                ];
            }
            return [
                'gmt_offset' => 0,
                'timezone_string' => 'UTC',
            ];
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function on($hook_name, $callback, $priority = 10, $accepted_args = 1){
            add_filter($hook_name, $callback, $priority, $accepted_args);
            return _wp_filter_build_unique_id($hook_name, $callback, $priority);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function one($hook_name, $callback, $priority = 10, $accepted_args = 1){
            if(!array_key_exists($hook_name, $this->hooks)){
                $this->hooks[$hook_name] = [];
            }
            $idx = _wp_filter_build_unique_id($hook_name, $callback, $priority);
            $md5 = md5($idx);
            if($callback instanceof Closure){
                $md5_closure = $this->md5_closure($callback);
                if(!is_wp_error($md5_closure)){
                    $md5 = $md5_closure;
                }
            }
            if(array_key_exists($md5, $this->hooks[$hook_name])){
                return $this->hooks[$hook_name][$md5];
            } else {
                $this->hooks[$hook_name][$md5] = $idx;
                add_filter($hook_name, $callback, $priority, $accepted_args);
                return $idx;
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function one_p($text = '', $dot = true, $p = 'first'){
            if(false === strpos($text, '.')){
                if($dot){
                    $text .= '.';
                }
                return $text;
            } else {
                $text = explode('.', $text);
                $text = array_filter($text);
                switch($p){
                    case 'first':
                        $text = array_shift($text);
                        break;
                    case 'last':
                        $text = array_pop($text);
                        break;
                    default:
                        $text = __('Error');
                }
                if($dot){
                    $text .= '.';
                }
                return $text;
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function post_type_labels($singular = '', $plural = '', $all = true){
            if(!$singular or !$plural){
                return [];
            }
            return [
                'name' => $plural,
                'singular_name' => $singular,
                'add_new' => 'Add New',
                'add_new_item' => 'Add New ' . $singular,
                'edit_item' => 'Edit ' . $singular,
                'new_item' => 'New ' . $singular,
                'view_item' => 'View ' . $singular,
                'view_items' => 'View ' . $plural,
                'search_items' => 'Search ' . $plural,
                'not_found' => 'No ' . strtolower($plural) . ' found.',
                'not_found_in_trash' => 'No ' . strtolower($plural) . ' found in Trash.',
                'parent_item_colon' => 'Parent ' . $singular . ':',
                'all_items' => ($all ? 'All ' : '') . $plural,
                'archives' => $singular . ' Archives',
                'attributes' => $singular . ' Attributes',
                'insert_into_item' => 'Insert into ' . strtolower($singular),
                'uploaded_to_this_item' => 'Uploaded to this ' . strtolower($singular),
                'featured_image' => 'Featured image',
                'set_featured_image' => 'Set featured image',
                'remove_featured_image' => 'Remove featured image',
                'use_featured_image' => 'Use as featured image',
                'filter_items_list' => 'Filter ' . strtolower($plural) . ' list',
                'items_list_navigation' => $plural . ' list navigation',
                'items_list' => $plural . ' list',
                'item_published' => $singular . ' published.',
                'item_published_privately' => $singular . ' published privately.',
                'item_reverted_to_draft' => $singular . ' reverted to draft.',
                'item_scheduled' => $singular . ' scheduled.',
                'item_updated' => $singular . ' updated.',
            ];
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function prepare($str = '', ...$args){
            global $wpdb;
            if(!$args){
                return $str;
            }
            if(false === strpos($str, '%')){
                return $str;
            } else {
                return str_replace("'", '', $wpdb->remove_placeholder_escape($wpdb->prepare(...$args)));
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function read_file_chunk($handle = null, $chunk_size = 0){
            $giant_chunk = '';
        	if(is_resource($handle) and is_int($chunk_size)){
        		$byte_count = 0;
        		while(!feof($handle)){
                    $length = apply_filters('vidsoe_file_chunk_lenght', (KB_IN_BYTES * 8));
        			$chunk = fread($handle, $length);
        			$byte_count += strlen($chunk);
        			$giant_chunk .= $chunk;
        			if($byte_count >= $chunk_size){
        				return $giant_chunk;
        			}
        		}
        	}
            return $giant_chunk;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function remote($url = '', $args = []){
            if(!class_exists('Vidsoe_Remote')){
                require_once(plugin_dir_path($this->file) . 'classes/class-vidsoe-remote.php');
            }
            return new Vidsoe_Remote($url, $args);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function remove_whitespaces($str = ''){
            $str = preg_replace('/[\r\n\t\s]+/', ' ', $str);
            return trim($str);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function require($url = '', $expected_dir = ''){
            global $wp_filesystem;
            $md5 = md5($url);
            $option = 'vidsoe_package_' . $md5;
            $value = get_option($option, '');
            if('' !== $value){
                return $value;
            }
            $download_dir = $this->download_dir();
            if(is_wp_error($download_dir)){
                return $download_dir;
            }
            $to = $download_dir . '/vidsoe-package-' . $md5;
            if($expected_dir){
                $expected_dir = ltrim($expected_dir, '/');
                $expected_dir = untrailingslashit($expected_dir);
                $expected_dir = trailingslashit($to) . $expected_dir;
            } else {
                $expected_dir = $to;
            }
            $fs = $this->filesystem();
            if(is_wp_error($fs)){
                return $fs;
            }
            if($wp_filesystem->dirlist($expected_dir, false)){
                return $this->error(__('Destination folder already exists.'));
            }
            $file = $this->download($url);
            if(is_wp_error($file)){
                return $file;
            }
            $result = unzip_file($file, $to);
            if(is_wp_error($result)){
                @unlink($file);
                $wp_filesystem->rmdir($to, true);
                return $result;
            }
            @unlink($file);
            if(!$wp_filesystem->dirlist($expected_dir, false)){
                return $this->error(__('Destination directory for file streaming does not exist or is not writable.'));
            }
            update_option($option, $expected_dir);
            return $expected_dir;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function response($response = null){
            if(!class_exists('Vidsoe_Response')){
                require_once(plugin_dir_path($this->file) . 'classes/class-vidsoe-response.php');
            }
            return new Vidsoe_Response($response);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function sanitize_timeout($timeout = 0){
            $timeout = (int) $timeout;
            $max_execution_time = (int) ini_get('max_execution_time');
            if(0 !== $max_execution_time){
                if(0 === $timeout or $timeout > $max_execution_time){
                    $timeout = $max_execution_time - 1; // Prevents error 504
                }
            }
            if(isset($_SERVER['HTTP_CF_RAY'])){
                if(0 === $timeout or $timeout > 99){
                    $timeout = 99; // Prevents error 524: https://support.cloudflare.com/hc/en-us/articles/115003011431#524error
                }
            }
            return $timeout;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function seems_false($data = ''){
            return in_array((string) $data, ['0', '', 'false', 'off'], true);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function seems_mysql_date($pattern = ''){
            return preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $pattern);
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function seems_true($data = ''){
            return in_array((string) $data, ['1', 'on', 'true'], true);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function serializable_closure(...$args){
            if(!class_exists('Opis\Closure\SerializableClosure')){
                $dir = $this->require('https://github.com/opis/closure/archive/3.6.2.zip', 'closure-3.6.2');
                if(is_wp_error($dir)){
                    return $dir;
                }
                require_once($dir . '/autoload.php');
            }
            return new Opis\Closure\SerializableClosure(...$args);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function session_destroy(){
            if(!session_id()){
                return false;
            }
            return session_destroy();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function session_start(){
            if(session_id()){
                return false;
            }
            return session_start();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function set_local_login_header(){
            $this->one('login_headertext', function($login_header_text){
    			return get_option('blogname');
    		});
    		$this->one('login_headerurl', function($login_header_url){
    			return home_url();
    		});
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function signon_without_password($username_or_email = '', $remember = false){
            if(is_user_logged_in()){
                return wp_get_current_user();
            } else {
                $idx = $this->on('authenticate', function($user, $username_or_email){
                    if(is_null($user)){
                        if(is_email($username_or_email)){
                            $user = get_user_by('email', $username_or_email);
                        }
                        if(is_null($user)){
                            $user = get_user_by('login', $username_or_email);
                            if(is_null($user)){
                                return $this->error(__('The requested user does not exist.'));
                            }
                        }
                    }
                    return $user;
                }, 10, 2);
                $user = wp_signon([
                    'remember' => $remember,
                    'user_login' => $username_or_email,
                    'user_password' => '',
                ]);
                $this->off('authenticate', $idx);
                return $user;
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function str_get_html(...$args){
            if(!class_exists('simple_html_dom')){
                $directory = $this->require('https://github.com/simplehtmldom/simplehtmldom/archive/refs/tags/1.9.1.zip', 'simplehtmldom-1.9.1');
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . '/simple_html_dom.php');
            }
            return str_get_html(...$args);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function support_sessions(){
            $this->one('init', [$this, 'session_start']);
            $this->one('wp_login', [$this, 'session_destroy']);
            $this->one('wp_logout', [$this, 'session_destroy']);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function tgmpa(...$args){
            if(!class_exists('TGM_Plugin_Activation')){
                $directory = $this->require('https://github.com/TGMPA/TGM-Plugin-Activation/archive/refs/tags/2.6.1.zip', 'TGM-Plugin-Activation-2.6.1');
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . '/class-tgm-plugin-activation.php');
            }
            return tgmpa(...$args);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function timezone($offset_or_tz = ''){
            return new DateTimeZone($this->timezone_string($offset_or_tz));
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function timezone_string($offset_or_tz = ''){
            $offset_or_tz = $this->offset_or_tz($offset_or_tz);
            $timezone_string = $offset_or_tz['timezone_string'];
            if($timezone_string){
                return $timezone_string;
            }
            $offset = (float) $offset_or_tz['gmt_offset'];
            $hours = (int) $offset;
            $minutes = ($offset - $hours);
            $sign = ($offset < 0) ? '-' : '+';
            $abs_hour = abs($hours);
            $abs_mins = abs($minutes * 60);
            $tz_offset = sprintf('%s%02d:%02d', $sign, $abs_hour, $abs_mins);
            return $tz_offset;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function upload($file = '', $post_id = 0){
            global $wp_filesystem;
            $fs = $this->filesystem();
            if(is_wp_error($fs)){
                return $fs;
            }
            if(!$wp_filesystem->exists($file)){
                return $this->error(__('File does not exist! Please double check the name and try again.'));
            }
            if(!$this->in_uploads($file)){
                return $this->error(sprintf(__('Unable to locate needed folder (%s).'), __('The uploads directory')));
            }
            $filename = wp_basename($file);
            $filetype_and_ext = wp_check_filetype_and_ext($file, $filename);
            if(!$filetype_and_ext['type']){
                return $this->error(__('Sorry, this file type is not permitted for security reasons.'));
            }
            $upload_dir = wp_get_upload_dir();
            $attachment_id = wp_insert_attachment([
                'guid' => $this->download_url($file),
                'post_mime_type' => $filetype_and_ext['type'],
                'post_status' => 'inherit',
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
            ], $file, $post_id, true);
            if(is_wp_error($attachment_id)){
                return $attachment_id;
            }
            $this->maybe_generate_attachment_metadata($attachment_id);
            return $attachment_id;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function upload_file($tmp_name = '', $post_id = 0){
            $file = $this->move_uploaded_file($tmp_name);
            if(is_wp_error($file)){
                return $file;
            }
            return $this->upload($file, $post_id);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function xlsx(...$args){
            if(!class_exists('XLSXWriter')){
                $directory = $this->require('https://github.com/mk-j/PHP_XLSXWriter/archive/refs/tags/0.38.zip', 'PHP_XLSXWriter-0.38');
                if(is_wp_error($directory)){
                    return $directory;
                }
                require_once($directory . '/xlsxwriter.class.php');
            }
            return new XLSXWriter(...$args);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function get_instance($file = ''){
            if(null !== self::$instance){
                return self::$instance;
            }
            if('' === $file){
                wp_die(__('File doesn&#8217;t exist?'));
            }
            if(!is_file($file)){
                wp_die(sprintf(__('File &#8220;%s&#8221; doesn&#8217;t exist?'), $file));
            }
            self::$instance = new self($file);
            return self::$instance;
    	}

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// useful methods
    	//
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // add_larger_image_sizes
        // destroy_other_sessions
        // fix_audio_video_extensions
        // set_local_login_header
        // support_sessions
        //
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
