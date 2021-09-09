<?php

if(!class_exists('Vidsoe_Hide')){
    final class Vidsoe_Hide extends Vidsoe_Base {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// protected
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        protected function load(){}

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function the_dashboard($capability = 'edit_posts', $location = ''){
            vidsoe()->one('admin_init', function() use($capability, $location){
                if(wp_doing_ajax() or current_user_can($capability)){
                    return;
                }
                if(false === filter_var($location, FILTER_VALIDATE_URL)){
                    $location = home_url();
                }
                wp_safe_redirect($location);
                exit;
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function the_toolbar($capability = 'edit_posts'){
            vidsoe()->one('show_admin_bar', function($show) use($capability){
                if(!current_user_can($capability)){
					$show = false;
				}
				return $show;
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function others_media($capability = 'edit_others_posts'){
            vidsoe()->one('ajax_query_attachments_args', function($query) use($capability){
                if(!current_user_can($capability)){
					$query['author'] = get_current_user_id();
				}
				return $query;
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function others_posts($capability = 'edit_others_posts'){
            vidsoe()->one('current_screen', function($current_screen) use($capability){
                global $pagenow;
                if('edit.php' !== $pagenow or current_user_can($capability)){
                    return;
                }
                add_filter('views_' . $current_screen->id, function($views){
                    foreach($views as $index => $view){
                        $views[$index] = preg_replace('/ <span class="count">\([0-9]+\)<\/span>/', '', $view);
                    }
                    return $views;
                });
            });
            vidsoe()->one('pre_get_posts', function($query) use($capability){
                global $pagenow;
                if('edit.php' !== $pagenow){
                    return $query;
                }
                if(!current_user_can($capability)){
                    $query->set('author', get_current_user_id());
                }
                return $query;
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function the_rest_api($capability = 'read'){
            vidsoe()->one('rest_authentication_errors', function($error) use($capability){
                if(!empty($error)){
					return $error;
				}
                if(!current_user_can($capability)){
					return new WP_Error('rest_user_cannot_view', __('You need a higher level of permission.'), [
						'status' => 401,
					]);
				}
				return null;
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function the_entire_site($capability = 'read', $exclude_other_pages = [], $exclude_special_pages = []){
            vidsoe()->one('template_redirect', function() use($capability, $exclude_other_pages, $exclude_special_pages){
                if(in_array(get_the_ID(), (array) $exclude_other_pages)){
                    return;
                }
                if(is_front_page() and in_array('front_end', (array) $exclude_special_pages)){
                    return;
                }
                if(is_home() and in_array('home', (array) $exclude_special_pages)){
                    return;
                }
                if(!is_user_logged_in()){
                    auth_redirect();
                }
                if(!current_user_can($capability)){
                    wp_die('<h1>' . __('You need a higher level of permission.') . '</h1>' . '<p>' . __('Sorry, you are not allowed to access this page.') . '</p>', 403);
                }
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
