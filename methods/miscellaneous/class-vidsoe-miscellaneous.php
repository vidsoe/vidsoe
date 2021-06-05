<?php

if(!class_exists('Vidsoe_Miscellaneous')){
    final class Vidsoe_Miscellaneous {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private static $admin_notices = [];

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function add_admin_notice($admin_notice = '', $class = 'error', $is_dismissible = false){
	        if($admin_notice){
	            if(!in_array($class, ['error', 'warning', 'success', 'info'])){
	    			$class = 'warning';
	    		}
	    		if($is_dismissible){
	    			$class .= ' is-dismissible';
	    		}
	    		self::$admin_notices[] = '<div class="notice notice-' . $class . '"><p>' . $admin_notice . '</p></div>';
	        }
			vidsoe()->one('admin_notices', function(){
				if(self::$admin_notices){
					foreach(self::$admin_notices as $admin_notice){
						echo $admin_notice;
					}
				}
			});
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function are_plugins_active($plugins = []){
			$r = false;
			if($plugins){
				$r = true;
				foreach($plugins as $plugin){
					if(!vidsoe()->is_plugin_active($plugin)){
						$r = false;
						break;
					}
				}
			}
			return $r;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function array_keys_exist($keys = [], $array = []){
			if(!$keys or !$array or !is_array($keys) or !is_array($array)){
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

		public static function base64_urldecode($data = '', $strict = false){
			return base64_decode(strtr($data, '-_', '+/'), $strict);
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function base64_urlencode($data = ''){
			return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function clone_role($source = '', $destination = '', $display_name = ''){
			if($source and $destination and $display_name){
	            $role = get_role($source);
	            $capabilities = $role->capabilities;
	            add_role($destination, $display_name, $capabilities);
	        }
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function current_screen_in($ids = []){
			if(is_admin()){
				if(function_exists('get_current_screen')){
					$current_screen = get_current_screen();
		            if($current_screen){
						return in_array($current_screen->id, $ids);
		            }
				}
	        }
	        return false;
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function current_screen_is($id = ''){
			if(is_admin()){
				if(function_exists('get_current_screen')){
					$current_screen = get_current_screen();
		            if($current_screen){
						return ($current_screen->id == $id);
		            }
				}
	        }
	        return false;
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function destroy_other_sessions(){
			vidsoe()->one('init', function(){
				wp_destroy_other_sessions();
	        });
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function error($code = '', $message = '', $data = ''){
            if(!$code){
                $code = 'error';
            }
            if(!$message){
                $message = __('Something went wrong.');
            }
            return new WP_Error($code, $message, $data);
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function format_function($function_name = '', $args = []){
			$str = '';
			if($function_name){
				$str .= '<div style="color: #24831d; font-family: monospace; font-weight: 400;">' . $function_name . '(';
				$function_args = [];
				if($args){
					foreach($args as $arg){
						$arg = shortcode_atts([
	                        'default' => 'null',
							'name' => '',
							'type' => '',
	                    ], $arg);
						if($arg['default'] and $arg['name'] and $arg['type']){
							$function_args[] = '<span style="color: #cd2f23; font-family: monospace; font-style: italic; font-weight: 400;">' . $arg['type'] . '</span> <span style="color: #0f55c8; font-family: monospace; font-weight: 400;">$' . $arg['name'] . '</span> = <span style="color: #000; font-family: monospace; font-weight: 400;">' . $arg['default'] . '</span>';
						}
					}
				}
				if($function_args){
					$str .= ' ' . implode(', ', $function_args) . ' ';
				}
				$str .= ')</div>';
			}
			return $str;
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function get_memory_size(){
			if(!function_exists('exec')){
		        return 0;
		    }
		    exec('free -b', $output);
		    $output = $output[1];
		    $output = trim(preg_replace('/\s+/', ' ', $output));
		    $output = explode(' ', $output);
		    $output = $output[1];
		    return absint($output);
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function is_array_assoc($array = []){
			if(is_array($array)){
	            return (array_keys($array) !== range(0, count($array) - 1));
	        }
			return false;
		}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function is_doing_heartbeat(){
			return (defined('DOING_AJAX') and DOING_AJAX and isset($_POST['action']) and $_POST['action'] == 'heartbeat');
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function is_plugin_active($plugin = ''){
			if(!function_exists('is_plugin_active')){
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}
			return is_plugin_active($plugin);
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function is_plugin_deactivating($file = ''){
			global $pagenow;
	        if(is_file($file)){
	            return (is_admin() and $pagenow == 'plugins.php' and isset($_GET['action'], $_GET['plugin']) and $_GET['action'] == 'deactivate' and $_GET['plugin'] == plugin_basename($file));
	        }
	        return false;
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function is_post_revision_or_auto_draft($post = null){
			return (wp_is_post_revision($post) or get_post_status($post) == 'auto-draft');
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function ksort_deep($data = []){
			if(vidsoe()->is_array_assoc($data)){
	            ksort($data);
	            foreach($data as $index => $item){
	                $data[$index] = vidsoe()->ksort_deep($item);
	            }
	        }
	        return $data;
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function md5($data = ''){
			if(is_object($data)){
				if($data instanceof Closure){
					return vidsoe()->md5_closure($data);
				} else {
					$data = json_decode(wp_json_encode($data), true);
				}
	        }
	        if(is_array($data)){
	            $data = vidsoe()->ksort_deep($data);
	            $data = maybe_serialize($data);
	        }
			return md5($data);
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function md5_to_uuid4($md5 = ''){
			if(strlen($md5) == 32){
	    		return substr($md5, 0, 8) . '-' . substr($md5, 8, 4) . '-' . substr($md5, 12, 4) . '-' . substr($md5, 16, 4) . '-' . substr($md5, 20, 12);
	    	}
	        return '';
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function new(...$args){
			if(!$args){
	            return null;
	        }
	        $class_name = array_shift($args);
	        if(!class_exists($class_name)){
	            return null;
	        }
	        return new $class_name(...$args);
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function post_type_labels($singular = '', $plural = '', $all = true){
			if($singular and $plural){
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
	    	return [];
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function remove_whitespaces($str = ''){
			return trim(preg_replace('/[\r\n\t\s]+/', ' ', $str));
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function signon_without_password($username_or_email = '', $remember = false){
			if(is_user_logged_in()){
	            return wp_get_current_user();
	        } else {
	            $hook = vidsoe()->one('authenticate', function($user = null, $username_or_email = ''){
	                if(is_null($user)){
	                    $user = get_user_by('login', $username_or_email);
	                    if(!$user){
							$user = get_user_by('email', $username_or_email);
		                    if(!$user){
		                        return new WP_Error('does_not_exist', __('The requested user does not exist.'));
		                    }
	                    }
	                }
	                return $user;
	            }, 10, 2);
	            $user = wp_signon([
	                'user_login' => $username_or_email,
	                'user_password' => '',
	                'remember' => $remember,
	            ]);
	            vidsoe()->off('authenticate', $hook);
	            return $user;
	        }
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function support_sessions($defaults = true){ // Shoud be used in conjunction with the WordPress Native PHP Sessions plugin by Pantheon.
            vidsoe()->one('init', function() use($defaults){
    			if(!session_id()){
            		session_start();
            	}
    			if($defaults){
    				if(empty($_SESSION['vidsoe_current_user_id'])){
    	        		$_SESSION['vidsoe_current_user_id'] = get_current_user_id();
    	        	}
    	            if(empty($_SESSION['vidsoe_utm'])){
    	        		$_SESSION['vidsoe_utm'] = [];
    	                foreach($_GET as $key => $value){
    	                    if(substr($key, 0, 4) == 'utm_'){
    	                        $_SESSION['vidsoe_utm'][$key] = $value;
    	                    }
    	                }
    	        	}
    			}
    		}, 9);
    		vidsoe()->one('wp_login', function($user_login, $user) use($defaults){
    			if($defaults){
    				$_SESSION['vidsoe_current_user_id'] = $user->ID;
    			}
    		}, 10, 2);
    		vidsoe()->one('wp_logout', function(){
    			if(session_id()){
            		session_destroy();
            	}
    		});
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function upload_basedir(){
			$wp_upload_dir = wp_get_upload_dir();
	        return $wp_upload_dir['basedir'] . '/vidsoe';
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function upload_baseurl(){
			$wp_upload_dir = wp_get_upload_dir();
	        return $wp_upload_dir['baseurl'] . '/vidsoe';
		}

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	}
}
