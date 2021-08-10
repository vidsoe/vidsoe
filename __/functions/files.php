<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_image_size')){
    function __add_image_size($name = '', $width = 0, $height = 0, $crop = false){
        if(!array_key_exists('image_sizes', $GLOBALS['__'])){
            $GLOBALS['__']['image_sizes'] = [];
        }
		$size = sanitize_title($name);
        if(!array_key_exists($size, $GLOBALS['__']['image_sizes'])){
            $GLOBALS['__']['image_sizes'][$size] = $name;
			add_image_size($size, $width, $height, $crop);
        }
        __one('image_size_names_choose', function($sizes){
            if(!$GLOBALS['__']['image_sizes']){
                return $sizes;
            }
			foreach($GLOBALS['__']['image_sizes'] as $size => $name){
				$sizes[$size] = $name;
			}
            return $sizes;
        });
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_larger_image_sizes')){
    function __add_larger_image_sizes(){
        __add_image_size('HD', 1280, 1280);
        __add_image_size('Full HD', 1920, 1920);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__attachment_url_to_postid')){
    function __attachment_url_to_postid($url = ''){
        $post_id = __guid_to_postid($url);
        if($post_id){
            return $post_id;
        }
        preg_match('/^(.+)(\-\d+x\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // resized
        if($matches){
            $url = $matches[1];
            if(isset($matches[3])){
                $url .= $matches[3];
            }
            $post_id = __guid_to_postid($url);
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
            $post_id = __guid_to_postid($url);
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
            $post_id = __guid_to_postid($url);
            if($post_id){
                return $post_id;
            }
        }
        return 0;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__copy')){
    function __copy($source = '', $destination = '', $overwrite = false, $mode = false){
        global $wp_filesystem;
        $fs = __filesystem();
        if(is_wp_error($fs)){
            return $fs;
        }
        if(!$wp_filesystem->copy($source, $destination, $overwrite, $mode)){
            return __error(sprintf(__('The uploaded file could not be moved to %s.'), $destination));
        }
        return $destination;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__download')){
    function __download($url = '', $args = []){
        $args = wp_parse_args($args, [
            'filename' => '',
            'timeout' => 300,
        ]);
        if($args['filename']){
            if(!__in_uploads($args['filename'])){
                return __error(sprintf(__('Unable to locate needed folder (%s).'), __('The uploads directory')));
            }
        } else {
            $download_dir = __download_dir();
            if(is_wp_error($download_dir)){
                return $download_dir;
            }
            $args['filename'] = trailingslashit($download_dir) . __filename($url);
        }
        $args['stream'] = true;
        $args['timeout'] = __sanitize_timeout($args['timeout']);
        $response = __remote($url, $args)->get();
        if(!$response->success){
            @unlink($args['filename']);
            return $response->to_wp_error();
        }
        return $args['filename'];
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__download_dir')){
    function __download_dir(){
        $upload_dir = wp_get_upload_dir();
        $dir = $upload_dir['basedir'] . '/__downloads';
        if(!wp_mkdir_p($dir)){
            return __error(__('Could not create directory.'));
        }
        if(!wp_is_writable($dir)){
            return __error(__('Destination directory for file streaming does not exist or is not writable.'));
        }
        return $dir;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__download_url')){
    function __download_url($file = ''){
        $upload_dir = wp_get_upload_dir();
        if('' !== $file){
            return str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file);
        } else {
            return $upload_dir['baseurl'] . '/__downloads';
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__filename')){
    function __filename($filename = ''){
        return preg_replace('/\?.*/', '', wp_basename($filename));
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__filesystem')){
    function __filesystem(){
        global $wp_filesystem;
        if($wp_filesystem instanceof WP_Filesystem_Direct){
            return true;
        }
        if(!function_exists('get_filesystem_method')){
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        if('direct' !== get_filesystem_method()){
            return __error(__('Could not access filesystem.'));
        }
        if(!WP_Filesystem()){
            return __error(__('Filesystem error.'));
        }
        return true;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__fix_audio_video_ext')){
    function __fix_audio_video_ext(){
        __one('wp_check_filetype_and_ext', function($wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime){
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
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__guid_to_postid')){
    function __guid_to_postid($guid = ''){
        global $wpdb;
        $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s", $guid);
        $post_id = $wpdb->get_var($query);
        if(null === $post_id){
            return 0;
        }
		return (int) $post_id;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__in_uploads')){
    function __in_uploads($file = ''){
        $upload_dir = wp_get_upload_dir();
        return (0 === strpos($file, $upload_dir['basedir']));
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_extension_allowed')){
    function __is_extension_allowed($extension = ''){
        foreach(wp_get_mime_types() as $exts => $mime){
            if(preg_match('!^(' . $exts . ')$!i', $extension)){
                return true;
            }
        }
        return false;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_generate_attachment_metadata')){
    function __maybe_generate_attachment_metadata($attachment_id = 0){
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
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__move_uploaded_file')){
    function __move_uploaded_file($tmp_name = ''){
        global $wp_filesystem;
        $fs = __filesystem();
        if(is_wp_error($fs)){
            return $fs;
        }
        if(!$wp_filesystem->exists($tmp_name)){
            return __error(__('File does not exist! Please double check the name and try again.'));
        }
        $upload_dir = wp_upload_dir();
        $original_filename = wp_basename($tmp_name);
        $filename = wp_unique_filename($upload_dir['path'], $original_filename);
        $file = trailingslashit($upload_dir['path']) . $filename;
        $result = __copy($tmp_name, $file);
        if(is_wp_error($result)){
            return $result;
        }
        return $file;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__package')){
    function __package($url = '', $dir = ''){
        global $wp_filesystem;
        $md5 = md5($url);
        $option = '__package_' . $md5;
        $value = get_option($option, '');
        if('' !== $value){
            return $value;
        }
        $download_dir = __download_dir();
        if(is_wp_error($download_dir)){
            return $download_dir;
        }
        $to = $download_dir . '/__package-' . $md5;
        if($dir){
            $dir = ltrim($dir, '/');
            $dir = untrailingslashit($dir);
            $dir = trailingslashit($to) . $dir;
        } else {
            $dir = $to;
        }
        $fs = __filesystem();
        if(is_wp_error($fs)){
            return $fs;
        }
        if($wp_filesystem->dirlist($dir, false)){
            return __error(__('Destination folder already exists.'));
        }
        $file = __download($url);
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
        if(!$wp_filesystem->dirlist($dir, false)){
            return __error(__('Destination directory for file streaming does not exist or is not writable.'));
        }
        update_option($option, $dir);
        return $dir;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__read_file_chunk')){
    function __read_file_chunk($handle = null, $chunk_size = 0){
        $giant_chunk = '';
    	if(is_resource($handle) and is_int($chunk_size)){
    		$byte_count = 0;
    		while(!feof($handle)){
                $length = __filter('__file_chunk_lenght', (KB_IN_BYTES * 8));
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
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__upload')){
    function __upload($file = '', $post_id = 0){
        global $wp_filesystem;
        $fs = __filesystem();
        if(is_wp_error($fs)){
            return $fs;
        }
        if(!$wp_filesystem->exists($file)){
            return __error(__('File does not exist! Please double check the name and try again.'));
        }
        if(!__in_uploads($file)){
            return __error(sprintf(__('Unable to locate needed folder (%s).'), __('The uploads directory')));
        }
        $filename = wp_basename($file);
        $filetype_and_ext = wp_check_filetype_and_ext($file, $filename);
        if(!$filetype_and_ext['type']){
            return __error(__('Sorry, this file type is not permitted for security reasons.'));
        }
        $upload_dir = wp_get_upload_dir();
        $attachment_id = wp_insert_attachment([
            'guid' => __download_url($file),
            'post_mime_type' => $filetype_and_ext['type'],
            'post_status' => 'inherit',
            'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
        ], $file, $post_id, true);
        if(is_wp_error($attachment_id)){
            return $attachment_id;
        }
        __maybe_generate_attachment_metadata($attachment_id);
        return $attachment_id;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__upload_file')){
    function __upload_file($tmp_name = '', $post_id = 0){
        $file = __move_uploaded_file($tmp_name);
        if(is_wp_error($file)){
            return $file;
        }
        return __upload($file, $post_id);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
