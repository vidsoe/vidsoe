<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__destroy_other_sessions')){
    function __destroy_other_sessions(){
        __one('init', 'wp_destroy_other_sessions');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__session_destroy')){
    function __session_destroy(){
        if(!session_id()){
            return false;
        }
        return session_destroy();
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__session_start')){
    function __session_start(){
        if(session_id()){
            return false;
        }
        return session_start();
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__support_sessions')){
    function __support_sessions(){
        __one('init', '__session_start');
        __one('wp_login', '__session_destroy');
        __one('wp_logout', '__session_destroy');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
