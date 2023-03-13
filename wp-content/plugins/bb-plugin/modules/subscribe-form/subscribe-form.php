<?php

/**
 * A module that adds a simple subscribe form to your layout
 * with third party opt-in integration.
 *
 * @since 1.5.2
 */
class FLSubscribeFormModule extends FLBuilderModule {

	/**
	 * Holds any errors that may arise from
	 * wp_mail.
	 *
	 * @since 2.5
	 * @var array $errors
	 */
	static public $errors = array();

	/**
	 * @since 1.5.2
	 * @return void
	 */
	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Subscribe Form', 'fl-builder' ),
			'description'     => __( 'Adds a simple subscribe form to your layout.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'editor-table.svg',
		));
		add_action( 'wp_mail_failed', array( $this, 'mail_failed' ) );
		add_action( 'wp_ajax_fl_builder_subscribe_form_submit', array( $this, 'submit' ) );
		add_action( 'wp_ajax_nopriv_fl_builder_subscribe_form_submit', array( $this, 'submit' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {

		// Handle old button module settings.
		$helper->filter_child_module_settings( 'button', $settings, array(
			'btn_3d'                 => 'three_d',
			'btn_style'              => 'style',
			'btn_padding'            => 'padding',
			'btn_padding_top'        => 'padding_top',
			'btn_padding_bottom'     => 'padding_bottom',
			'btn_padding_left'       => 'padding_left',
			'btn_padding_right'      => 'padding_right',
			'btn_mobile_align'       => 'mobile_align',
			'btn_align_responsive'   => 'align_responsive',
			'btn_font_size'          => 'font_size',
			'btn_font_size_unit'     => 'font_size_unit',
			'btn_typography'         => 'typography',
			'btn_bg_color'           => 'bg_color',
			'btn_bg_hover_color'     => 'bg_hover_color',
			'btn_bg_opacity'         => 'bg_opacity',
			'btn_bg_hover_opacity'   => 'bg_hover_opacity',
			'btn_border'             => 'border',
			'btn_border_hover_color' => 'border_hover_color',
			'btn_border_radius'      => 'border_radius',
			'btn_border_size'        => 'border_size',
		) );

		// Return the filtered settings.
		return $settings;
	}

	/**
	 *
	 * @since 2.5
	 * @param object $wp_error object with the PHPMailerException message.
	 */
	public function mail_failed( $wp_error ) {

		if ( is_wp_error( $wp_error ) && ! empty( $wp_error->errors['wp_mail_failed'] ) ) {
			self::$errors = $wp_error->errors['wp_mail_failed'][0];
		}
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		$settings = $this->settings;
		if ( isset( $settings->show_recaptcha ) && 'show' == $settings->show_recaptcha
			&& isset( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_site_key )
			) {

			$site_lang = substr( get_locale(), 0, 2 );
			$this->add_js(
				'g-recaptcha',
				'https://www.google.com/recaptcha/api.js?onload=onLoadFLReCaptcha&render=explicit&hl=' . $site_lang,
				array(),
				'2.0',
				true
			);
		}
	}

	/**
	 * @method  add_async_attribute for the enqueued `g-recaptcha` script
	 * @param string $tag    Script tag
	 * @param string $handle Registered script handle
	 */
	public function add_async_attribute( $tag, $handle ) {
		if ( ( 'g-recaptcha' !== $handle ) || ( 'g-recaptcha' === $handle && strpos( $tag, 'g-recaptcha-api' ) !== false ) ) {
			return $tag;
		}

		return str_replace( ' src', ' id="g-recaptcha-api" async="async" defer="defer" src', $tag );
	}

	/**
	 * Render reCaptcha attributes.
	 * @return string
	 */
	public function recaptcha_data_attributes() {
		$settings               = $this->settings;
		$attrs['data-sitekey']  = $settings->recaptcha_site_key;
		$attrs['data-validate'] = 'invisible_v3' == $settings->recaptcha_validate_type ? 'invisible' : $settings->recaptcha_validate_type;
		$attrs['data-theme']    = $settings->recaptcha_theme;

		if ( 'invisible_v3' == $settings->recaptcha_validate_type && ! empty( $settings->recaptcha_action ) ) {
			$attrs['data-action'] = $settings->recaptcha_action;
		}

		foreach ( $attrs as $attr_key => $attr_val ) {
			echo ' ' . $attr_key . '="' . $attr_val . '"';
		}
	}

	/**
	 * Called via AJAX to submit the subscribe form.
	 *
	 * @since 1.5.2
	 * @return string The JSON encoded response.
	 */
	public function submit() {
		$name             = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : false;
		$email            = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : false;
		$success_url      = isset( $_POST['success_url'] ) ? $_POST['success_url'] : '';
		$terms_checked    = isset( $_POST['terms_checked'] ) && 1 == $_POST['terms_checked'] ? true : false;
		$recaptcha        = isset( $_POST['recaptcha'] ) ? $_POST['recaptcha'] : false;
		$post_id          = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
		$node_id          = isset( $_POST['node_id'] ) ? sanitize_text_field( $_POST['node_id'] ) : false;
		$template_id      = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : false;
		$template_node_id = isset( $_POST['template_node_id'] ) ? sanitize_text_field( $_POST['template_node_id'] ) : false;
		$result           = array(
			'action'  => false,
			'error'   => false,
			'message' => false,
			'url'     => false,
		);

		if ( $email && $node_id ) {

			// Get the module settings.
			if ( $template_id ) {
				$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
				$data     = FLBuilderModel::get_layout_data( 'published', $post_id );
				$settings = $data[ $template_node_id ]->settings;
			} else {
				$module   = FLBuilderModel::get_module( $node_id );
				$settings = $module->settings;
			}

			// Validate terms and conditions if enabled
			if ( ( isset( $settings->terms_checkbox ) && 'show' == $settings->terms_checkbox ) && ! $terms_checked ) {
				$result = array(
					'error' => __( 'You must accept the Terms and Conditions.', 'fl-builder' ),
				);
			}

			if ( ! isset( $settings->service ) ) {
				$result['error'] = __( 'There was an error subscribing. Please try again.', 'fl-builder' );
			}

			// Validate reCAPTCHA first if enabled
			if ( $recaptcha && ! $result['error'] ) {

				if ( ! empty( $settings->recaptcha_secret_key ) && ! empty( $settings->recaptcha_site_key ) ) {
					if ( version_compare( phpversion(), '5.3', '>=' ) ) {
						include FLBuilderModel::$modules['subscribe-form']->dir . 'includes/validate-recaptcha.php';
					} else {
						$result['error'] = false;
					}
				} else {
					$result['error'] = __( 'Your reCAPTCHA Site or Secret Key is missing!', 'fl-builder' );
				}
			}

			if ( ! $result['error'] ) {

				// Subscribe.
				$instance = FLBuilderServices::get_service_instance( $settings->service );
				$response = $instance->subscribe( $settings, $email, $name );

				// Check for an error from the service.
				if ( $response['error'] ) {
					$result['error'] = $response['error'];
				} else {

					$result['action'] = $settings->success_action;

					// Success message.
					if ( 'message' == $settings->success_action ) {
						// Existing email message.
						if ( method_exists( $instance, 'subscriber_status' ) ) {
							if ( in_array( $instance->subscriber_status(), array( 'subscribed', 'unsubscribed' ) ) ) {
								$result['message'] = __( 'Subscription has been updated. Please check your email for further instructions.', 'fl-builder' );
							}
						}
					}

					if ( 'redirect' == $settings->success_action ) {
						$result['url'] = $success_url;
					}
				}

				if ( 'email-address' == $settings->service ) {
					if ( ! empty( self::$errors ) ) {
						$result['error']     = __( 'There was an error subscribing. Please check the console for possible error message.', 'fl-builder' );
						$result['errorInfo'] = self::$errors;
					}
				}

				do_action( 'fl_builder_subscribe_form_submission_complete', $response, $settings, $email, $name, $template_id, $post_id );
			}
		} else {
			$result['error'] = __( 'There was an error subscribing. Please try again.', 'fl-builder' );
		}

		echo json_encode( $result );

		die();
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_button_settings() {
		$settings = array(
			'link'        => '#',
			'link_target' => '_self',
			'width'       => 'full',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'btn_' ) ) {
				$key              = str_replace( 'btn_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module( 'FLSubscribeFormModule', array(
	'general'   => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'service'   => array(
				'title'    => '',
				'services' => 'autoresponder',
				'template' => array(
					'id'   => 'fl-builder-service-settings',
					'file' => FL_BUILDER_DIR . 'includes/ui-service-settings.php',
				),
			),
			'structure' => array(
				'title'  => __( 'Structure', 'fl-builder' ),
				'fields' => array(
					'layout'              => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'stacked',
						'options' => array(
							'stacked' => __( 'Stacked', 'fl-builder' ),
							'inline'  => __( 'Inline', 'fl-builder' ),
						),
					),
					'show_name'           => array(
						'type'    => 'select',
						'label'   => __( 'Name Field', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'name_field_text' ),
							),
						),
					),
					'name_field_text'     => array(
						'type'    => 'text',
						'label'   => __( 'Name Field Text', 'fl-builder' ),
						'default' => __( 'Name', 'fl-builder' ),
					),
					'email_field_text'    => array(
						'type'    => 'text',
						'label'   => __( 'Email Field Text', 'fl-builder' ),
						'default' => __( 'Email Address', 'fl-builder' ),
					),
					'terms_checkbox'      => array(
						'type'    => 'select',
						'label'   => __( 'Terms and Conditions Checkbox', 'fl-builder' ),
						'default' => 'hide',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'terms_checkbox_text', 'terms_text' ),
							),
						),
					),
					'terms_checkbox_text' => array(
						'type'    => 'text',
						'label'   => __( 'Checkbox Text', 'fl-builder' ),
						'default' => __( 'I Accept the Terms and Conditions', 'fl-builder' ),
					),
					'terms_text'          => array(
						'type'          => 'editor',
						'label'         => 'Terms and Conditions',
						'media_buttons' => false,
						'rows'          => 8,
						'preview'       => array(
							'type'     => 'text',
							'selector' => '.fl-terms-checkbox-text',
						),
						'connections'   => array( 'string' ),
					),
				),
			),
			'success'   => array(
				'title'  => __( 'Success', 'fl-builder' ),
				'fields' => array(
					'custom_subject'  => array(
						'type'        => 'text',
						'label'       => __( 'Notification Subject', 'fl-builder' ),
						'placeholder' => __( 'Subscribe Form Signup', 'fl-builder' ),
					),
					'success_action'  => array(
						'type'    => 'select',
						'label'   => __( 'Success Action', 'fl-builder' ),
						'options' => array(
							'message'  => __( 'Show Message', 'fl-builder' ),
							'redirect' => __( 'Redirect', 'fl-builder' ),
						),
						'toggle'  => array(
							'message'  => array(
								'fields' => array( 'success_message' ),
							),
							'redirect' => array(
								'fields' => array( 'success_url' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'success_message' => array(
						'type'          => 'editor',
						'label'         => '',
						'media_buttons' => false,
						'rows'          => 8,
						'default'       => __( 'Thanks for subscribing! Please check your email for further instructions.', 'fl-builder' ),
						'preview'       => array(
							'type' => 'none',
						),
						'connections'   => array( 'string' ),
					),
					'success_url'     => array(
						'type'        => 'link',
						'label'       => __( 'Success URL', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
						'connections' => array( 'url' ),
					),
				),
			),
		),
	),
	'button'    => array(
		'title'    => __( 'Button', 'fl-builder' ),
		'sections' => array(
			'btn_general' => array(
				'title'  => '',
				'fields' => array(
					'btn_text' => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'fl-builder' ),
						'default' => __( 'Subscribe!', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-button-text',
						),
					),
				),
			),
			'btn_icon'    => array(
				'title'  => __( 'Button Icon', 'fl-builder' ),
				'fields' => array(
					'btn_icon'           => array(
						'type'        => 'icon',
						'label'       => __( 'Button Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields' => array( 'btn_icon_position', 'btn_icon_animation' ),
						),
					),
					'btn_duo_color1'     => array(
						'label'      => __( 'DuoTone Primary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-button-icon.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_duo_color2'     => array(
						'label'      => __( 'DuoTone Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-button-icon.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_icon_position'  => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Position', 'fl-builder' ),
						'default' => 'before',
						'options' => array(
							'before' => __( 'Before Text', 'fl-builder' ),
							'after'  => __( 'After Text', 'fl-builder' ),
						),
					),
					'btn_icon_animation' => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Visibility', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Always Visible', 'fl-builder' ),
							'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
						),
					),
				),
			),
			'btn_style'   => array(
				'title'  => __( 'Button Style', 'fl-builder' ),
				'fields' => array(
					'btn_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Button Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button, .fl-form-field input, .fl-form-field input[type=text]',
							'property' => 'padding',
						),
					),
				),
			),
			'btn_text'    => array(
				'title'  => __( 'Button Text', 'fl-builder' ),
				'fields' => array(
					'btn_text_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Text Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button, a.fl-button *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_text_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Text Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button:hover, a.fl-button:hover *, a.fl-button:focus, a.fl-button:focus *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Button Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button, .fl-form-field input, .fl-form-field input[type=text]',
						),
					),
				),
			),
			'btn_colors'  => array(
				'title'  => __( 'Button Background', 'fl-builder' ),
				'fields' => array(
					'btn_bg_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'btn_bg_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'btn_style'             => array(
						'type'    => 'select',
						'label'   => __( 'Button Background Style', 'fl-builder' ),
						'default' => 'flat',
						'options' => array(
							'flat'     => __( 'Flat', 'fl-builder' ),
							'gradient' => __( 'Gradient', 'fl-builder' ),
						),
					),
					'btn_button_transition' => array(
						'type'    => 'select',
						'label'   => __( 'Button Background Animation', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Disabled', 'fl-builder' ),
							'enable'  => __( 'Enabled', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'btn_border'  => array(
				'title'  => __( 'Button Border', 'fl-builder' ),
				'fields' => array(
					'btn_border'             => array(
						'type'       => 'border',
						'label'      => __( 'Button Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button',
							'important' => true,
						),
					),
					'btn_border_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Border Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
	'reCAPTCHA' => array(
		'title'       => __( 'Captcha', 'fl-builder' ),
		'sections'    => array(
			'recaptcha_general' => array(
				'title'  => '',
				'fields' => array(
					'show_recaptcha'          => array(
						'type'    => 'select',
						'label'   => __( 'reCAPTCHA Field', 'fl-builder' ),
						'default' => 'hide',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'recaptcha_site_key', 'recaptcha_secret_key', 'recaptcha_validate_type', 'recaptcha_theme' ),
							),
						),
						'help'    => __( 'If you want to show this field, please provide valid Site and Secret Keys.', 'fl-builder' ),
					),
					'recaptcha_validate_type' => array(
						'type'    => 'select',
						'label'   => __( 'Validate Type', 'fl-builder' ),
						'default' => 'normal',
						'options' => array(
							'normal'       => __( '"I\'m not a robot" checkbox (V2)', 'fl-builder' ),
							'invisible'    => __( 'Invisible (V2)', 'fl-builder' ),
							'invisible_v3' => __( 'Invisible (V3)', 'fl-builder' ),
						),
						'toggle'  => array(
							'invisible_v3' => array(
								'fields' => array( 'recaptcha_action' ),
							),
						),
						'help'    => __( 'Validate users with checkbox or in the background.', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'recaptcha_action'        => array(
						'type'        => 'text',
						'label'       => __( 'Action', 'fl-builder' ),
						'help'        => __( 'Optional advanced feature to make use of Google’s v3 analytical capabilities.', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
						'placeholder' => __( 'Optional', 'fl-builder' ),
					),
					'recaptcha_site_key'      => array(
						'type'    => 'text',
						'label'   => __( 'Site Key', 'fl-builder' ),
						'default' => '',
						'preview' => array(
							'type' => 'none',
						),
					),
					'recaptcha_secret_key'    => array(
						'type'    => 'text',
						'label'   => __( 'Secret Key', 'fl-builder' ),
						'default' => '',
						'preview' => array(
							'type' => 'none',
						),
					),

					'recaptcha_theme'         => array(
						'type'    => 'select',
						'label'   => __( 'Theme', 'fl-builder' ),
						'default' => 'light',
						'options' => array(
							'light' => __( 'Light', 'fl-builder' ),
							'dark'  => __( 'Dark', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
		),
		/* translators: %s: Google admin url */
		'description' => sprintf( __( 'Register keys for your website at the <a%1$s>Google Admin Console</a>. You need a different key pair for each reCAPTCHA validation type. <br /><br /><a%2$s>More info about v3 reCAPTCHA.</a>', 'fl-builder' ), ' href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener"', ' href="https://developers.google.com/recaptcha/docs/v3" target="_blank" rel="noopener"' ),
	),
));
