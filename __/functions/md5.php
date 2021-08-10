<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__md5')){
    function __md5($data = null){
        if(is_object($data)){
            if($data instanceof Closure){
                return __md5_closure($data);
            } else {
                $data = json_decode(wp_json_encode($data), true);
            }
        }
        if(is_array($data)){
            $data = __ksort_deep($data);
            $data = maybe_serialize($data);
        }
        return md5($data);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__md5_closure')){
    function __md5_closure($data = null, $spl_object_hash = false){
        if($data instanceof Closure){
            $wrapper = __serializable_closure($data);
            if(is_wp_error($wrapper)){
                return $wrapper;
            }
            $serialized = maybe_serialize($wrapper);
            if(!$spl_object_hash){
                $serialized = str_replace(spl_object_hash($data), 'spl_object_hash', $serialized);
            }
            return md5($serialized);
        } else {
            return __error(__('Invalid object type.'));
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__md5_to_uuid4')){
    function __md5_to_uuid4($md5 = ''){
        if(32 === strlen($md5)){
            return substr($md5, 0, 8) . '-' . substr($md5, 8, 4) . '-' . substr($md5, 12, 4) . '-' . substr($md5, 16, 4) . '-' . substr($md5, 20, 12);
        }
        return __error(__('Invalid data provided.'));
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
