<?php

if(!class_exists('Vidsoe_Beaver_Builder')){
    final class Vidsoe_Beaver_Builder {

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

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function check_default_styles(){
        	$defaults = array_map('strval', $this->default_styles());
        	$mods = get_theme_mods();
        	$mods = array_map('strval', $mods);
        	$intersection = array_intersect_assoc($defaults, $mods);
        	$difference = array_diff_assoc($defaults, $intersection);
        	return empty($difference);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function default_styles(){
            return [
        		'fl-body-font-size_medium' => 14,
        		'fl-body-font-size_mobile' => 14,
        		'fl-button-font-size_medium' => 16,
        		'fl-button-font-size_mobile' => 16,
        		'fl-button-line-height_medium' => 1.2,
        		'fl-button-line-height_mobile' => 1.2,
        		'fl-body-line-height_medium' => 1.45,
        		'fl-body-line-height_mobile' => 1.45,
        		'fl-h1-font-size_medium' => 36,
        		'fl-h1-font-size_mobile' => 36,
        		'fl-h1-line-height_medium' => 1.4,
        		'fl-h1-line-height_mobile' => 1.4,
        		'fl-h1-letter-spacing_medium' => 0,
        		'fl-h1-letter-spacing_mobile' => 0,
        		'fl-h2-font-size_medium' => 30,
        		'fl-h2-font-size_mobile' => 30,
        		'fl-h2-line-height_medium' => 1.4,
        		'fl-h2-line-height_mobile' => 1.4,
        		'fl-h2-letter-spacing_medium' => 0,
        		'fl-h2-letter-spacing_mobile' => 0,
        		'fl-h3-font-size_medium' => 24,
        		'fl-h3-font-size_mobile' => 24,
        		'fl-h3-line-height_medium' => 1.4,
        		'fl-h3-line-height_mobile' => 1.4,
        		'fl-h3-letter-spacing_medium' => 0,
        		'fl-h3-letter-spacing_mobile' => 0,
        		'fl-h4-font-size_medium' => 18,
        		'fl-h4-font-size_mobile' => 18,
        		'fl-h4-line-height_medium' => 1.4,
        		'fl-h4-line-height_mobile' => 1.4,
        		'fl-h4-letter-spacing_medium' => 0,
        		'fl-h4-letter-spacing_mobile' => 0,
        		'fl-h5-font-size_medium' => 14,
        		'fl-h5-font-size_mobile' => 14,
        		'fl-h5-line-height_medium' => 1.4,
        		'fl-h5-line-height_mobile' => 1.4,
        		'fl-h5-letter-spacing_medium' => 0,
        		'fl-h5-letter-spacing_mobile' => 0,
        		'fl-h6-font-size_medium' => 12,
        		'fl-h6-font-size_mobile' => 12,
        		'fl-h6-line-height_medium' => 1.4,
        		'fl-h6-line-height_mobile' => 1.4,
        		'fl-h6-letter-spacing_medium' => 0,
        		'fl-h6-letter-spacing_mobile' => 0,
        		'fl-hamburger-icon-top-position_medium' => 24,
        		'fl-hamburger-icon-top-position_mobile' => 24,
        		'fl-topbar-bg-color' => '#ffffff',
        		'fl-topbar-text-color' => '#000000',
        		'fl-topbar-link-color' => '#428bca',
        		'fl-topbar-hover-color' => '#428bca',
        		'fl-header-bg-color' => '#ffffff',
        		'fl-header-text-color' => '#000000',
        		'fl-header-link-color' => '#428bca',
        		'fl-header-hover-color' => '#428bca',
        		'fl-nav-bg-color' => '#ffffff',
        		'fl-nav-text-color' => '#000000',
        		'fl-nav-link-color' => '#428bca',
        		'fl-nav-hover-color' => '#428bca',
        		'fl-footer-widgets-bg-color' => '#ffffff',
        		'fl-footer-widgets-text-color' => '#000000',
        		'fl-footer-widgets-link-color' => '#428bca',
        		'fl-footer-widgets-hover-color' => '#428bca',
        		'fl-footer-bg-color' => '#ffffff',
        		'fl-footer-text-color' => '#000000',
        		'fl-footer-link-color' => '#428bca',
        		'fl-footer-hover-color' => '#428bca',
        		'fl-nav-font-family' => 'Helvetica',
        		'fl-nav-font-weight' => 400,
        		'fl-nav-font-format' => 'none',
        		'fl-nav-font-size' => 14,
        	];
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function maybe_reboot_default_styles(){
            if($this->check_default_styles()){
            	$this->reboot_default_styles();
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function reboot_default_styles(){
            $mods = get_theme_mods();
        	$mods['fl-scroll-to-top'] = 'enable';
            $mods['fl-framework'] = 'bootstrap-4';
            $mods['fl-awesome'] = 'fa5';
            $mods['fl-body-bg-color'] = '#ffffff';
            $mods['fl-accent'] = '#007bff';
            $mods['fl-accent-hover'] = '#0056b3';
            $mods['fl-heading-text-color'] = '#343a40';
            $mods['fl-heading-font-family'] = 'Open Sans';
            $mods['fl-h1-font-size'] = 40;
            $mods['fl-h1-font-size_medium'] = 33;
            $mods['fl-h1-font-size_mobile'] = 28;
            $mods['fl-h1-line-height'] = 1.2;
            $mods['fl-h1-line-height_medium'] = 1.2;
            $mods['fl-h1-line-height_mobile'] = 1.2;
            $mods['fl-h2-font-size'] = 32;
            $mods['fl-h2-font-size_medium'] = 28;
            $mods['fl-h2-font-size_mobile'] = 24;
            $mods['fl-h2-line-height'] = 1.2;
            $mods['fl-h2-line-height_medium'] = 1.2;
            $mods['fl-h2-line-height_mobile'] = 1.2;
            $mods['fl-h3-font-size'] = 28;
            $mods['fl-h3-font-size_medium'] = 25;
            $mods['fl-h3-font-size_mobile'] = 22;
            $mods['fl-h3-line-height'] = 1.2;
            $mods['fl-h3-line-height_medium'] = 1.2;
            $mods['fl-h3-line-height_mobile'] = 1.2;
            $mods['fl-h4-font-size'] = 24;
            $mods['fl-h4-font-size_medium'] = 22;
            $mods['fl-h4-font-size_mobile'] = 20;
            $mods['fl-h4-line-height'] = 1.2;
            $mods['fl-h4-line-height_medium'] = 1.2;
            $mods['fl-h4-line-height_mobile'] = 1.2;
            $mods['fl-h5-font-size'] = 20;
            $mods['fl-h5-font-size_medium'] = 19;
            $mods['fl-h5-font-size_mobile'] = 16;
            $mods['fl-h5-line-height'] = 1.2;
            $mods['fl-h5-line-height_medium'] = 1.2;
            $mods['fl-h5-line-height_mobile'] = 1.2;
            $mods['fl-h6-font-size'] = 16;
            $mods['fl-h6-font-size_medium'] = 16;
            $mods['fl-h6-font-size_mobile'] = 16;
            $mods['fl-h6-line-height'] = 1.2;
            $mods['fl-h6-line-height_medium'] = 1.2;
            $mods['fl-h6-line-height_mobile'] = 1.2;
            $mods['fl-body-text-color'] = '#6c757d';
            $mods['fl-body-font-family'] = 'Open Sans';
            $mods['fl-body-font-size'] = 16;
            $mods['fl-body-font-size_medium'] = 16;
            $mods['fl-body-font-size_mobile'] = 16;
            $mods['fl-body-line-height'] = 1.5;
            $mods['fl-body-line-height_medium'] = 1.5;
            $mods['fl-body-line-height_mobile'] = 1.5;
            $mods['fl-header-layout'] = 'none';
            $mods['fl-fixed-header'] = 'hidden';
            $mods['fl-footer-widgets-display'] = 'disabled';
            $mods['fl-footer-layout'] = 'none';
            return update_option('theme_mods_' . get_stylesheet(), $mods);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
