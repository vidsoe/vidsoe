<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__build_update_checker')){
    function __build_update_checker(...$args){
        if(!class_exists('Puc_v4_Factory')){
            $dir = __package('https://github.com/YahnisElsts/plugin-update-checker/archive/refs/tags/v4.11.zip', 'plugin-update-checker-4.11');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/plugin-update-checker.php');
        }
        return Puc_v4_Factory::buildUpdateChecker(...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__facebook')){
	function __facebook(...$args){
        if(!class_exists('Facebook\Facebook')){
            $dir = __package('https://github.com/facebookarchive/php-graph-sdk/archive/refs/tags/5.7.0.zip', 'php-graph-sdk-5.7.0');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/vendor/autoload.php');
        }
        return new Facebook\Facebook(...$args);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__file_get_html')){
    function __file_get_html(...$args){
        if(!class_exists('simple_html_dom')){
            $dir = __package('https://github.com/simplehtmldom/simplehtmldom/archive/refs/tags/1.9.1.zip', 'simplehtmldom-1.9.1');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/simple_html_dom.php');
        }
        return file_get_html(...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__google_client')){
	function __google_client(...$args){
        if(!class_exists('Google\Client')){
            switch(true){
                case is_php_version_compatible('8.0'):
                    $version = '8.0';
                    break;
                case is_php_version_compatible('7.4'):
                    $version = '7.4';
                    break;
                case is_php_version_compatible('7.0'):
                    $version = '7.0';
                    break;
                default:
                    $version = '5.6';
            }
            $dir = __package("https://github.com/googleapis/google-api-php-client/releases/download/v2.10.1/google-api-php-client--PHP{$version}.zip", "google-api-php-client--PHP{$version}");
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/vendor/autoload.php');
        }
        return new Google\Client(...$args);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__jwt_decode')){
	function __jwt_decode(...$args){
        if(!class_exists('Firebase\JWT\JWT')){
            $dir = __package('https://github.com/firebase/php-jwt/archive/refs/tags/v5.4.0.zip', 'php-jwt-5.4.0');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/src/BeforeValidException.php');
            require_once($dir . '/src/ExpiredException.php');
            require_once($dir . '/src/JWK.php');
            require_once($dir . '/src/JWT.php');
            require_once($dir . '/src/SignatureInvalidException.php');
        }
        return Firebase\JWT\JWT::decode(...$args);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__jwt_encode')){
	function __jwt_encode(...$args){
        if(!class_exists('Firebase\JWT\JWT')){
            $dir = __package('https://github.com/firebase/php-jwt/archive/refs/tags/v5.4.0.zip', 'php-jwt-5.4.0');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/src/BeforeValidException.php');
            require_once($dir . '/src/ExpiredException.php');
            require_once($dir . '/src/JWK.php');
            require_once($dir . '/src/JWT.php');
            require_once($dir . '/src/SignatureInvalidException.php');
        }
        return Firebase\JWT\JWT::encode(...$args);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__seems_cloudflare')){
    function __seems_cloudflare(){
        return isset($_SERVER['HTTP_CF_RAY']);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__serializable_closure')){
    function __serializable_closure(...$args){
        if(!class_exists('Opis\Closure\SerializableClosure')){
            $dir = __package('https://github.com/opis/closure/archive/3.6.2.zip', 'closure-3.6.2');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/autoload.php');
        }
        return new Opis\Closure\SerializableClosure(...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__str_get_html')){
    function __str_get_html(...$args){
        if(!class_exists('simple_html_dom')){
            $dir = __package('https://github.com/simplehtmldom/simplehtmldom/archive/refs/tags/1.9.1.zip', 'simplehtmldom-1.9.1');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/simple_html_dom.php');
        }
        return str_get_html(...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__tgmpa')){
    function __tgmpa(...$args){
        if(!class_exists('TGM_Plugin_Activation')){
            $dir = __package('https://github.com/TGMPA/TGM-Plugin-Activation/archive/refs/tags/2.6.1.zip', 'TGM-Plugin-Activation-2.6.1');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/class-tgm-plugin-activation.php');
        }
        return tgmpa(...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__xlsx')){
    function __xlsx(...$args){
        if(!class_exists('XLSXWriter')){
            $dir = __package('https://github.com/mk-j/PHP_XLSXWriter/archive/refs/tags/0.38.zip', 'PHP_XLSXWriter-0.38');
            if(is_wp_error($dir)){
                return $dir;
            }
            require_once($dir . '/xlsxwriter.class.php');
        }
        return new XLSXWriter(...$args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__zoom_jwt')){
	function __zoom_jwt($api_key = '', $api_secret = ''){
        $payload = [
            'exp' => time() + DAY_IN_SECONDS,
            'iss' => $api_key,
        ];
        return __jwt_encode($payload, $api_secret);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
