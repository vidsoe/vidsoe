<?php

if(!class_exists('Vidsoe_BuddyPress')){
    final class Vidsoe_BuddyPress {

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

    	public static function get_instance(){
            if(null === self::$instance){
                self::$instance = new self();
            }
            return self::$instance;
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private $lastname_field_name = '';

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function first_name_and_last_name($lastname_field_name = ''){
            if($lastname_field_name){
                $this->lastname_field_name = $lastname_field_name;
                vidsoe()->one('bp_core_signup_user', [$this, 'xprofile_sync_wp_profile'], 11);
                vidsoe()->one('bp_core_activated_user', [$this, 'xprofile_sync_wp_profile'], 11);
                vidsoe()->one('xprofile_data_after_save', [$this, 'xprofile_data_after_save'], 11);
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function xprofile_data_after_save($data){
            if(bp_xprofile_fullname_field_id() !== $data->field_id and xprofile_get_field_id_from_name($this->lastname_field_name) !== $data->field_id){
        		return;
        	}
        	$this->xprofile_sync_wp_profile($data->user_id);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function xprofile_sync_wp_profile($user_id = 0){
        	if(bp_disable_profile_sync()){
        		return true; // Bail if profile syncing is disabled.
        	}
        	if(empty($user_id)){
        		$user_id = bp_loggedin_user_id();
        	}
        	if(empty($user_id)){
        		return false;
        	}
        	$firstname = xprofile_get_field_data(bp_xprofile_fullname_field_id(), $user_id);
            $lastname = xprofile_get_field_data($this->lastname_field_name, $user_id);
        	$fullname = $firstname . ' ' . $lastname;
        	bp_update_user_meta($user_id, 'nickname',   $fullname );
        	bp_update_user_meta($user_id, 'first_name', $firstname);
        	bp_update_user_meta($user_id, 'last_name',  $lastname);
        	wp_update_user([
                'ID' => $user_id,
                'display_name' => $fullname,
            ]);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
