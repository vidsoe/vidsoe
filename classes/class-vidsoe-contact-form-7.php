<?php

if(!class_exists('Vidsoe_Contact_Form_7')){
    final class Vidsoe_Contact_Form_7 {

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

    	private $active_tab = 0;

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct(){
            $this->load();
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function is_editing(){
            global $pagenow;
            if(null === $pagenow){
                return false;
            }
            if('post.php' !== $pagenow){
                return false;
            }
            if(!isset($_GET['action'])){
                return false;
            }
            if('edit' !== $_GET['action']){
                return false;
            }
            if(!isset($_GET['post'])){
                return false;
            }
            $post = get_post($_GET['post']);
            if(null === $post){
                return false;
            }
            if('wpcf7_contact_form' !== $post->post_type){
                return false;
            }
            if(!isset($_GET['_wpnonce'])){
                return false;
            }
            return wp_verify_nonce($_GET['_wpnonce'], 'vidsoe-edit-' . $post->ID);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function load(){
            $vidsoe->one('admin_enqueue_scripts', function(){
                wp_enqueue_script('vidsoe-contact-form-7', $this->url() . 'assets/vidsoe-contact-form-7.js', ['vidsoe'], filemtime($this->dir() . 'assets/vidsoe-contact-form-7.js'), true);
            });
            $vidsoe->one('wp_enqueue_scripts', function(){
                wp_enqueue_script('vidsoe-contact-form-7', $this->url() . 'assets/vidsoe-contact-form-7.js', ['vidsoe'], filemtime($this->dir() . 'assets/vidsoe-contact-form-7.js'), true);
            });
            vidsoe()->one('redirect_post_location', [$this, 'redirect_post_location'], 10, 2);
            vidsoe()->one('register_post_type_args', [$this, 'register_post_type_args'], 10, 2);
            vidsoe()->one('rwmb_meta_boxes', [$this, 'rwmb_meta_boxes']);
            vidsoe()->one('wpcf7_editor_panels', [$this, 'wpcf7_editor_panels']);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function redirect_post_location($location, $post_id){
            if('wpcf7_contact_form' !== get_post_type($post_id)){
                return $location;
            }
            $referer = wp_get_referer();
            if(false === $referer){
                return $location;
            }
            return add_query_arg('message', 1, $referer);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function register_post_type_args($args, $post_type){
            if('wpcf7_contact_form' !== $post_type){
                return $args;
            }
            if(!$this->is_editing()){
                return $args;
            }
            $args['show_in_menu'] = false;
            $args['show_ui'] = true;
            $args['supports'] = ['title'];
            return $args;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	public function use_ace(){
            vidsoe()->one('admin_enqueue_scripts', function(){
                vidsoe()->enqueue()->ace(['vidsoe-contact-form-7', 'wpcf7-admin']);
                wp_add_inline_script('vidsoe-contact-form-7', 'vidsoe.contact_form_7.ace_load();');
                wp_add_inline_style('contact-form-7-admin', '#tag-generator-list, #wpcf7-form { display: none; } .editor-container { background-color: #272822; border-radius: 4px; box-sizing: border-box; padding: 4px; width: 99%; }');
            });
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function rwmb_meta_boxes($meta_boxes){
            $prefix = 'vidsoe_';
            if($this->is_editing()){
                $meta_boxes[] = [
        			'context' => 'side',
        			'fields' => [
        				[
        					'std' => '<style>#delete-action, #minor-publishing, .page-title-action { display: none !important; } #major-publishing-actions { border-top: 0 !important; }</style><a href="' . admin_url('admin.php?action=edit&active-tab=' . $_GET['active-tab'] . '&page=wpcf7&post=' . $_GET['post']) . '">' . __('Go back') . '</a>',
        					'type' => 'custom_html',
        				],
        			],
        			'id' => $prefix . 'go_back',
        			'post_types' => 'wpcf7_contact_form',
        			'priority' => 'low',
        			'title' => __('Additional Settings', 'contact-form-7'),
        		];
            }
            $meta_boxes[] = [
        		'fields' => [
        			[
        				'id' => $prefix . 'contact_form_type',
        				'name' => 'Tipo de formulario',
        				'options' => [
        					'contact_form' => 'Formulario de contacto',
        					'signup' => 'Formulario de registro',
        					'login' => 'Formulario de acceso',
        				],
        				'required' => true,
        				'std' => 'contact_form',
        				'type' => 'radio',
        			],
        			[
        				'id' => $prefix . 'login_with',
        				'name' => 'Acceder con',
        				'options' => [
        					'username_or_email' => __('Username or email address'),
        					'email' => __('Email'),
        					'username' => __('Username'),
        				],
        				'required' => true,
        				'std' => 'username_or_email',
        				'type' => 'select',
        				'visible' => [$prefix . 'contact_form_type', 'login'],
        			],
        			[
        				'id' => $prefix . 'default_role',
        				'name' => __('New User Default Role'),
        				'options' => vidsoe()->roles(),
        				'required' => true,
        				'std' => 'subscriber',
        				'type' => 'select',
        				'visible' => [$prefix . 'contact_form_type', 'signup'],
        			],
        			[
        				'id' => $prefix . 'automatically_log_in_on_registration',
        				'name' => 'Acceder automáticamente después de registrarse exitosamente',
        				'options' => [
        					'yes' => __('Yes'),
        					'no' => __('No'),
        				],
        				'required' => true,
        				'std' => 'yes',
        				'type' => 'radio',
        				'visible' => [$prefix . 'contact_form_type', 'signup'],
        			],
        			[
        				'type' => 'divider',
        			],
        			[
        				'id' => $prefix . 'hide_form_fields_on_wpcf7mailsent',
        				'name' => 'Ocultar campos después de enviar exitosamente',
        				'options' => [
        					'yes' => __('Yes'),
        					'no' => __('No'),
        				],
        				'required' => true,
        				'std' => 'yes',
        				'type' => 'radio',
        			],
        		],
        		'id' => $prefix . 'general_settings',
        		'post_types' => 'wpcf7_contact_form',
        		'title' => __('General Settings'),
        	];
            $meta_boxes[] = [
        		'fields' => [
        			[
        				'id' => $prefix . 'redirect_on_wpcf7mailsent',
        				'name' => 'Redirigir después de enviar exitosamente',
        				'options' => [
        					'yes' => __('Yes'),
        					'no' => __('No'),
        				],
        				'required' => true,
        				'std' => 'yes',
        				'type' => 'radio',
        			],
        			[
        				'id' => $prefix . 'redirect_to',
        				'name' => 'Redirigir a',
        				'options' => [
        					'same_url' => 'Misma URL',
        					'custom_url' => 'Otra URL',
        				],
        				'required' => true,
        				'std' => 'same_url',
        				'type' => 'radio',
        				'visible' => [$prefix . 'redirect_on_wpcf7mailsent', 'yes'],
        			],
        			[
        				'id' => $prefix . 'custom_url',
        				'name' => 'URL',
        				'required' => true,
        				'placeholder' => 'https://example.com',
        				'type' => 'url',
        				'visible' => [$prefix . 'redirect_to', 'custom_url'],
        			],
        		],
        		'id' => $prefix . 'redirect',
        		'post_types' => 'wpcf7_contact_form',
        		'title' => 'Redirección',
        	];
            $meta_boxes[] = [
        		'fields' => [
        			[
        				'id' => $prefix . 'hide_form_before',
        				'name' => 'Ocultar antes de',
        				'options' => [
        					'never' => 'Nunca',
        					'datetime' => 'Fecha y hora',
        				],
        				'required' => true,
        				'std' => 'never',
        				'type' => 'radio',
        			],
        			[
        				'id' => $prefix . 'hide_form_before_date',
        				'js_options' => [
        					'dateFormat' => "d 'de' MM 'del' yy",
        					'showButtonPanel' => false,
        				],
                        'name' => 'Fecha',
        				'required' => true,
        				'type' => 'date',
        				'visible' => [$prefix . 'hide_form_before', 'datetime'],
        			],
        			[
        				'id' => $prefix . 'hide_form_before_time',
        				'js_options' => [
                            'controlType' => 'select',
                            'oneLine' => true,
        					'showButtonPanel' => false,
        					'timeFormat' => "h:mm t'. m.'",
        				],
                        'name' => 'Hora',
        				'required' => true,
        				'type' => 'time',
        				'visible' => [$prefix . 'hide_form_before', 'datetime'],
        			],
        			[
        				'id' => $prefix . 'hide_form_before_message',
        				'name' => 'Mensaje de apertura',
        				'placeholder' => 'Este formulario se abrirá el ###DATE### a las ###TIME###',
        				'rows' => 2,
        				'type' => 'textarea',
        				'visible' => [$prefix . 'hide_form_before', 'datetime'],
        			],
        			[
        				'type' => 'divider',
        			],
        			[
        				'id' => $prefix . 'hide_form_after',
        				'name' => 'Ocultar después de',
        				'options' => [
        					'never' => 'Nunca',
        					'datetime' => 'Fecha y hora',
        				],
        				'required' => true,
        				'std' => 'never',
        				'type' => 'radio',
        			],
        			[
        				'id' => $prefix . 'hide_form_after_date',
        				'js_options' => [
        					'dateFormat' => "d 'de' MM 'del' yy",
        					'showButtonPanel' => false,
        				],
                        'name' => 'Fecha',
        				'required' => true,
        				'type' => 'date',
        				'visible' => [$prefix . 'hide_form_after', 'datetime'],
        			],
        			[
        				'id' => $prefix . 'hide_form_after_time',
        				'js_options' => [
                            'controlType' => 'select',
                            'oneLine' => true,
                            'showButtonPanel' => false,
        					'timeFormat' => "h:mm t'. m.'",
        				],
                        'name' => 'Hora',
        				'required' => true,
        				'type' => 'time',
        				'visible' => [$prefix . 'hide_form_after', 'datetime'],
        			],
        			[
        				'id' => $prefix . 'hide_form_after_message',
        				'name' => 'Mensaje de cierre',
        				'placeholder' => 'Este formulario se cerró el ###DATE### a las ###TIME###',
        				'rows' => 2,
        				'type' => 'textarea',
        				'visible' => [$prefix . 'hide_form_after', 'datetime'],
        			],
        			[
        				'type' => 'divider',
        			],
        			[
        				'id' => $prefix . 'hide_form_limit',
        				'name' => 'Ocultar al llegar a',
        				'options' => [
        					'unlimited' => 'Ilimitado',
        					'number' => 'Número de envíos exitosos',
        				],
        				'required' => true,
        				'std' => 'unlimited',
        				'type' => 'radio',
        			],
        			[
        				'id' => $prefix . 'hide_form_limit_number',
        				'name' => 'Número',
        				'required' => true,
        				'type' => 'number',
        				'visible' => [$prefix . 'hide_form_limit', 'number'],
        			],
        			[
        				'id' => $prefix . 'hide_form_limit_message',
        				'name' => 'Mensaje de cupo lleno',
        				'placeholder' => 'Este formulario se cerró al llegar a ###LIMIT### envíos.',
        				'rows' => 2,
        				'type' => 'textarea',
        				'visible' => [$prefix . 'hide_form_limit', 'number'],
        			],
        		],
        		'id' => $prefix . 'hide_form',
        		'post_types' => 'wpcf7_contact_form',
        		'title' => 'Ocultar formulario',
        	];
            return $meta_boxes;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function wpcf7_editor_panel($contact_form){
            $html = '<h2>' . __('Additional Settings', 'contact-form-7') . '</h2>';
            $html .= '<fieldset>';
            $html .= '<legend>';
            if($contact_form->id()){
                $nonce_url = wp_nonce_url(admin_url('post.php?action=edit&active-tab=' . $this->active_tab . '&post=' . $contact_form->id()), 'vidsoe-edit-' . $contact_form->id());
                $html .= '<a href="' . $nonce_url . '">' . __('Edit This') . '</a>';
            } else {
                $html .= __('Save Changes');
            }
            $html .= '</legend>';
            $html .= '</fieldset>';
            echo $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function wpcf7_editor_panels($panels){
            if(isset($panels['vidsoe'])){
               return $panels;
            }
            $this->active_tab = count($panels);
            $panels['vidsoe'] = [
               'callback' => [$this, 'wpcf7_editor_panel'],
               'title' => 'Vidsoe',
            ];
            return $panels;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
