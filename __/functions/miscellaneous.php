<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_admin_notice')){
    function __add_admin_notice($admin_notice = '', $class = 'error', $is_dismissible = false){
        if(!array_key_exists('admin_notices', $GLOBALS['__'])){
            $GLOBALS['__']['admin_notices'] = [];
        }
        $admin_notice = __admin_notice($admin_notice, $class, $is_dismissible);
        $md5 = md5($admin_notice);
        if(!array_key_exists($md5, $GLOBALS['__']['admin_notices'])){
            $GLOBALS['__']['admin_notices'][$md5] = $admin_notice;
        }
        __one('admin_notices', function(){
            if(!$GLOBALS['__']['admin_notices']){
                return;
            }
            foreach($GLOBALS['__']['admin_notices'] as $admin_notice){
                echo $admin_notice;
            }
        });
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__admin_notice')){
    function __admin_notice($admin_notice = '', $class = 'warning', $is_dismissible = false){
        if(!in_array($class, ['error', 'info', 'success', 'warning'])){
            $class = 'warning';
        }
        if($is_dismissible){
            $class .= ' is-dismissible';
        }
        return '<div class="notice notice-' . $class . '"><p>' . $admin_notice . '</p></div>';
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__are_plugins_active')){
    function __are_plugins_active($plugins = []){
        if(!is_array($plugins)){
            return false;
        }
        foreach($plugins as $plugin){
            if(!__is_plugin_active($plugin)){
                return false;
            }
        }
        return true;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__array_keys_exist')){
    function __array_keys_exist($keys = [], $array = []){
        if(!is_array($keys) or !is_array($array)){
            return false;
        }
        foreach($keys as $key){
            if(!array_key_exists($key, $array)){
                return false;
                break;
            }
        }
        return true;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__base64_urldecode')){
    function __base64_urldecode($data = '', $strict = false){
        return base64_decode(strtr($data, '-_', '+/'), $strict);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__base64_urlencode')){
    function __base64_urlencode($data = ''){
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__canonicalize')){
    function __canonicalize($key = ''){
        $key = __remove_whitespaces($key);
        $key = str_replace(' ', '', $key);
        return WP_REST_Request::canonicalize_header_name($key);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__clone_role')){
    function __clone_role($source = '', $destination = '', $display_name = ''){
        $role = get_role($source);
        if(is_null($role)){
            return null;

        }
        return add_role(sanitize_title($destination), $display_name, $role->capabilities);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__current_screen_in')){
    function __current_screen_in($ids = []){
        global $current_screen;
        if(!is_array($ids)){
            return false;
        }
        if(!isset($current_screen)){
            return false;
        }
        return in_array($current_screen->id, $ids);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__current_screen_is')){
    function __current_screen_is($id = ''){
        global $current_screen;
        if(!is_string($id)){
            return false;
        }
        if(!isset($current_screen)){
            return false;
        }
        return ($current_screen->id === $id);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__custom_login_logo')){
    function __custom_login_logo($attachment_id = 0, $half = true){
        if(!wp_attachment_is_image($attachment_id)){
            return;
        }
        __one('login_enqueue_scripts', function() use($attachment_id, $half){
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
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__enqueue_floating_labels')){
    function __enqueue_floating_labels(){
        __one('wp_enqueue_scripts', function(){
            $src = plugin_dir_url(__FILE__) . 'assets/floating-labels.js';
            $ver = filemtime(plugin_dir_path(__FILE__) . 'assets/floating-labels.js');
            wp_enqueue_script('__floating-labels', $src, ['jquery'], $ver, true);
            wp_add_inline_script('__floating-labels', '__floating_labels.init();');
            $src = plugin_dir_url(__FILE__) . 'assets/floating-labels.css';
            $ver = filemtime(plugin_dir_path(__FILE__) . 'assets/floating-labels.css');
            wp_enqueue_style('__floating-labels', $src, [], $ver);
        });
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__error')){
    function __error($message = '', $data = ''){
        if(is_wp_error($message)){
            $data = $message->get_error_data();
            $message = $message->get_error_message();
        }
        if(!$message){
            $message = __('Something went wrong.');
        }
        return new WP_Error('__error', $message, $data);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__first_p')){
    function __first_p($text = '', $dot = true){
        return __one_p($text, $dot, 'first');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_memory_size')){
    function __get_memory_size(){
        if(!function_exists('exec')){
            return 0;
        }
        exec('free -b', $output);
        $output = __remove_whitespaces($output[1]);
        $output = explode(' ', $output);
        return (int) $output[1];
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_post')){
    function __get_post($post = null){
        if(is_array($post)){
            $args = array_merge($post, [
                'posts_per_page' => 1,
            ]);
            $posts = get_posts($args);
            if($posts){
                return $posts[0];
            } else {
                return null;
            }
        } elseif(is_string($post) and 1 === preg_match('/^[a-z0-9]{13}$/', $post)){
            return __get_post([
                'meta_key' => '__uniqid',
                'meta_value' => $post,
                'post_status' => 'any',
            ]);
        } else {
            return get_post($post);
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_array_assoc')){
    function __is_array_assoc($array = []){
        if(!is_array($array)){
            return false;
        }
        return (array_keys($array) !== range(0, count($array) - 1));
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_doing_heartbeat')){
    function __is_doing_heartbeat(){
        return (defined('DOING_AJAX') and DOING_AJAX and isset($_POST['action']) and $_POST['action'] == 'heartbeat');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_plugin_active')){
    function __is_plugin_active($plugin = ''){
        if(!function_exists('is_plugin_active')){
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        return is_plugin_active($plugin);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_plugin_deactivating')){
    function __is_plugin_deactivating__is_plugin_deactivating($file = ''){
        global $pagenow;
        if(!is_file($file)){
            return false;
        }
        return (is_admin() and 'plugins.php' === $pagenow and isset($_GET['action'], $_GET['plugin']) and 'deactivate' === $_GET['action'] and plugin_basename($file) === $_GET['plugin']);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_post_revision_or_auto_draft')){
    function __is_post_revision_or_auto_draft($post = null){
        return (wp_is_post_revision($post) or 'auto-draft' === get_post_status($post));
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__ksort_deep')){
    function __ksort_deep($data = []){
        if(__is_array_assoc($data)){
            ksort($data);
            foreach($data as $index => $item){
                $data[$index] = __ksort_deep($item);
            }
        }
        return $data;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__last_p')){
    function __last_p($text = '', $dot = true){
        return __one_p($text, $dot, 'last');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__local_login_header')){
    function __local_login_header(){
        __one('login_headertext', function($login_header_text){
			return get_option('blogname');
		});
		__one('login_headerurl', function($login_header_url){
			return home_url();
		});
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__one_p')){
    function __one_p($text = '', $dot = true, $p = 'first'){
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
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__post_type_labels')){
    function __post_type_labels($singular = '', $plural = '', $all = true){
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
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__prepare')){
    function __prepare($str = '', ...$args){
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
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__remote')){
    function __remote($url = '', $args = []){
        if(!class_exists('__Remote')){
            require_once(plugin_dir_path(__FILE__) . 'classes/remote.php');
        }
        return new __Remote($url, $args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__remove_whitespaces')){
    function __remove_whitespaces($str = ''){
        return trim(preg_replace('/[\r\n\t\s]+/', ' ', $str));
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__response')){
    function __response($response = null){
        if(!class_exists('__Response')){
            require_once(plugin_dir_path(__FILE__) . 'classes/response.php');
        }
        return new __Response($response);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__sanitize_timeout')){
    function __sanitize_timeout($timeout = 0){
        $timeout = (int) $timeout;
        $max_execution_time = (int) ini_get('max_execution_time');
        if(0 !== $max_execution_time){
            if(0 === $timeout or $timeout > $max_execution_time){
                $timeout = $max_execution_time - 1; // Prevents error 504
            }
        }
        if(__seems_cloudflare()){
            if(0 === $timeout or $timeout > 99){
                $timeout = 99; // Prevents error 524: https://support.cloudflare.com/hc/en-us/articles/115003011431#524error
            }
        }
        return $timeout;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__seems_false')){
    function __seems_false($data = ''){
        return in_array((string) $data, ['0', '', 'false', 'off'], true);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__seems_mysql_date')){
	function __seems_mysql_date($pattern = ''){
        return preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $pattern);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__seems_true')){
    function __seems_true($data = ''){
        return in_array((string) $data, ['1', 'on', 'true'], true);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__signon_without_password')){
    function __signon_without_password($username_or_email = '', $remember = false){
        if(is_user_logged_in()){
            return wp_get_current_user();
        } else {
            $idx = __on('authenticate', function($user, $username_or_email){
                if(is_null($user)){
                    if(is_email($username_or_email)){
                        $user = get_user_by('email', $username_or_email);
                    }
                    if(is_null($user)){
                        $user = get_user_by('login', $username_or_email);
                        if(is_null($user)){
                            return __error(__('The requested user does not exist.'));
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
            __off('authenticate', $idx);
            return $user;
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
