<?php

/**
 * @class FLHtmlModule
 */
class FLContactFormModule extends FLBuilderModule {

	/**
	 * Holds any errors that may arise from
	 * wp_mail.
	 *
	 * @since 2.5
	 * @var array $errors
	 */
	static public $errors = array();

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Contact Form', 'fl-builder' ),
			'description'     => __( 'A very simple contact form.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'editor-table.svg',
		));
		add_action( 'wp_mail_failed', array( $this, 'mail_failed' ) );
		add_action( 'wp_ajax_fl_builder_email', array( $this, 'send_mail' ) );
		add_action( 'wp_ajax_nopriv_fl_builder_email', array( $this, 'send_mail' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );
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
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		$settings = $this->settings;
		if ( isset( $settings->recaptcha_toggle ) && 'show' == $settings->recaptcha_toggle
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
	 * Connects Beaver Themer field connections before sending mail
	 * as those won't be connected during a wp_ajax call.
	 *
	 * @method connect_field_connections_before_send
	 */
	public function connect_field_connections_before_send() {
		if ( class_exists( 'FLPageData' ) && isset( $_REQUEST['layout_id'] ) ) {

			$posts = query_posts( array(
				'p'         => absint( $_REQUEST['layout_id'] ),
				'post_type' => 'any',
			) );

			if ( count( $posts ) ) {
				global $post;
				$post = $posts[0];
				setup_postdata( $post );
				FLPageData::init_properties();
			}
		}
	}

	/**
	 * @method send_mail
	 */
	public function send_mail() {

		// Try to connect Themer connections before sending.
		self::connect_field_connections_before_send();

		// Get the contact form post data
		$node_id            = isset( $_POST['node_id'] ) ? sanitize_text_field( $_POST['node_id'] ) : false;
		$template_id        = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : false;
		$template_node_id   = isset( $_POST['template_node_id'] ) ? sanitize_text_field( $_POST['template_node_id'] ) : false;
		$recaptcha_response = isset( $_POST['recaptcha_response'] ) ? $_POST['recaptcha_response'] : false;
		$terms_checked      = isset( $_POST['terms_checked'] ) && 1 == $_POST['terms_checked'] ? true : false;

		$subject     = ( isset( $_POST['subject'] ) ? stripslashes( $_POST['subject'] ) : __( 'Contact Form Submission', 'fl-builder' ) );
		$admin_email = get_option( 'admin_email' );
		$site_name   = get_option( 'blogname' );
		$response    = array(
			'error'   => true,
			'message' => __( 'Message failed. Please try again.', 'fl-builder' ),
		);

		if ( $node_id ) {

			// Get the module settings.
			if ( $template_id ) {
				$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
				$data     = FLBuilderModel::get_layout_data( 'published', $post_id );
				$settings = $data[ $template_node_id ]->settings;
			} else {
				$module   = FLBuilderModel::get_module( $node_id );
				$settings = $module->settings;
			}

			if ( class_exists( 'FLThemeBuilderFieldConnections' ) ) {
				$settings = FLThemeBuilderFieldConnections::connect_settings( $settings );
			}

			if ( isset( $settings->mailto_email ) && ! empty( $settings->mailto_email ) ) {
				$mailto = $settings->mailto_email;
			} else {
				$mailto = $admin_email;
			}
			if ( isset( $settings->subject_toggle ) && ( 'hide' == $settings->subject_toggle ) && isset( $settings->subject_hidden ) && ! empty( $settings->subject_hidden ) ) {
				$subject = $settings->subject_hidden;
			}

			// Validate terms and conditions if enabled
			if ( ( isset( $settings->terms_checkbox ) && 'show' == $settings->terms_checkbox ) && ! $terms_checked ) {
				$response = array(
					'error'   => true,
					'message' => __( 'You must accept the Terms and Conditions.', 'fl-builder' ),
				);
				wp_send_json( $response );
			}

			// Validate reCAPTCHA if enabled
			if ( isset( $settings->recaptcha_toggle ) && 'show' == $settings->recaptcha_toggle && $recaptcha_response ) {
				if ( ! empty( $settings->recaptcha_secret_key ) && ! empty( $settings->recaptcha_site_key ) ) {
					if ( version_compare( phpversion(), '5.3', '>=' ) ) {
						include FLBuilderModel::$modules['contact-form']->dir . 'includes/validate-recaptcha.php';
					} else {
						$response['error'] = false;
					}
				} else {
					$response = array(
						'error'   => true,
						'message' => __( 'Your reCAPTCHA Site or Secret Key is missing!', 'fl-builder' ),
					);
				}
			} else {
				$response['error'] = false;
			}

			$fl_contact_from_email = ( isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : null );
			$fl_contact_from_name  = ( isset( $_POST['name'] ) ? stripslashes( $_POST['name'] ) : '' );

			if ( isset( $_POST['name'] ) ) {
				$site_name = apply_filters( 'fl_contact_form_from', $site_name, $_POST['name'] );
			}

			$site_name = str_replace( '&amp;', '&', $site_name );
			$headers   = array(
				'From: ' . $site_name . ' <' . $admin_email . '>',
				'Reply-To: ' . $fl_contact_from_name . ' <' . $fl_contact_from_email . '>',
			);

			// Build the email
			$template = '';

			if ( isset( $_POST['name'] ) ) {
				$template .= __( 'Name', 'fl-builder' ) . ': ' . stripslashes( $_POST['name'] ) . "\r\n";
			}
			if ( isset( $_POST['email'] ) ) {
				$template .= __( 'Email', 'fl-builder' ) . ': ' . stripslashes( $_POST['email'] ) . "\r\n";
			}
			if ( isset( $_POST['phone'] ) ) {
				$template .= __( 'Phone', 'fl-builder' ) . ': ' . stripslashes( $_POST['phone'] ) . "\r\n";
			}

			$template .= __( 'Message', 'fl-builder' ) . ": \r\n" . stripslashes( $_POST['message'] );

			// Double check the mailto email is proper and no validation error found, then send.
			if ( $mailto && false === $response['error'] ) {

				$subject = do_shortcode( $subject );
				$mailto  = esc_html( do_shortcode( $mailto ) );
				/**
				 * Before sending with wp_mail()
				 * @see fl_module_contact_form_before_send
				 */
				do_action( 'fl_module_contact_form_before_send', $mailto, $subject, $template, $headers, $settings );
				$result = wp_mail( $mailto, $subject, $template, $headers );
				/**
				 * After sending with wp_mail()
				 * @see fl_module_contact_form_after_send
				 */
				do_action( 'fl_module_contact_form_after_send', $mailto, $subject, $template, $headers, $settings, $result );
				$response['message'] = __( 'Sent!', 'fl-builder' );
				if ( ! empty( self::$errors ) ) {
					$response = array(
						'error'     => true,
						'message'   => __( 'Message failed. Please check the console for possible error message.', 'fl-builder' ),
						'errorInfo' => self::$errors,
					);
				}
			}
			wp_send_json( $response );
		}
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
FLBuilder::register_module('FLContactFormModule', array(
	'general'   => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'mailto_email'  => array(
						'type'        => 'text',
						'label'       => __( 'Send To Email', 'fl-builder' ),
						'default'     => '',
						'placeholder' => __( 'example@mail.com', 'fl-builder' ),
						'help'        => __( 'The contact form will send to this e-mail. Defaults to the admin email.', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
						'connections' => array( 'custom_field' ),
					),
					'email_explain' => array(
						'type'    => 'raw',
						'content' => sprintf( '<p class="fl-builder-settings-tab-description">%s&nbsp;<a target="_blank" href="https://docs.wpbeaverbuilder.com/beaver-builder/how-to-tips/use-smtp-to-send-form-notifications">%s</a></p>', __( 'Note: Please read the following info on email deliverability for this module.', 'fl-builder' ), __( 'Link to Doc', 'fl-builder' ) ),
					),
				),
			),
			'fields'  => array(
				'title'  => __( 'Fields', 'fl-builder' ),
				'fields' => array(
					'name_toggle'         => array(
						'type'    => 'select',
						'label'   => __( 'Name Field', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'name_placeholder' ),
							),
						),
					),
					'name_placeholder'    => array(
						'type'    => 'text',
						'label'   => __( 'Name Field Placeholder', 'fl-builder' ),
						'default' => __( 'Your name', 'fl-builder' ),
					),
					'subject_toggle'      => array(
						'type'    => 'select',
						'label'   => __( 'Subject Field', 'fl-builder' ),
						'default' => 'hide',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'subject_placeholder' ),
							),
							'hide' => array(
								'fields' => array( 'subject_hidden' ),
							),
						),
					),
					'subject_placeholder' => array(
						'type'    => 'text',
						'label'   => __( 'Subject Field Placeholder', 'fl-builder' ),
						'default' => __( 'Subject', 'fl-builder' ),
					),
					'subject_hidden'      => array(
						'type'        => 'text',
						'label'       => __( 'Email Subject', 'fl-builder' ),
						'default'     => 'Contact Form Submission',
						'help'        => __( 'You can choose the subject of the email. Defaults to Contact Form Submission.', 'fl-builder' ),
						'connections' => array( 'custom_field', 'string', 'html' ),
					),
					'email_toggle'        => array(
						'type'    => 'select',
						'label'   => __( 'Email Field', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'email_placeholder' ),
							),
						),
					),
					'email_placeholder'   => array(
						'type'    => 'text',
						'label'   => __( 'Email Field Placeholder', 'fl-builder' ),
						'default' => __( 'Your email', 'fl-builder' ),
					),
					'phone_toggle'        => array(
						'type'    => 'select',
						'label'   => __( 'Phone Field', 'fl-builder' ),
						'default' => 'hide',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'phone_placeholder' ),
							),
						),
					),
					'phone_placeholder'   => array(
						'type'    => 'text',
						'label'   => __( 'Phone Field Placeholder', 'fl-builder' ),
						'default' => __( 'Your phone', 'fl-builder' ),
					),
					'message_placeholder' => array(
						'type'    => 'text',
						'label'   => __( 'Your Message Placeholder', 'fl-builder' ),
						'default' => __( 'Your message', 'fl-builder' ),
					),
					'placeholder_labels'  => array(
						'type'    => 'select',
						'label'   => __( 'Show labels/placeholders', 'fl-builder' ),
						'default' => 'placeholder',
						'options' => array(
							'placeholder' => __( 'Show Placeholders Only', 'fl-builder' ),
							'labels'      => __( 'Show Labels Only', 'fl-builder' ),
							'both'        => __( 'Show Both', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'terms_checkbox_text', 'terms_text' ),
							),
						),
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
			'success' => array(
				'title'  => __( 'Success', 'fl-builder' ),
				'fields' => array(
					'success_action'  => array(
						'type'    => 'select',
						'label'   => __( 'Success Action', 'fl-builder' ),
						'options' => array(
							'none'         => __( 'None', 'fl-builder' ),
							'show_message' => __( 'Show Message', 'fl-builder' ),
							'redirect'     => __( 'Redirect', 'fl-builder' ),
						),
						'toggle'  => array(
							'show_message' => array(
								'fields' => array( 'success_message' ),
							),
							'redirect'     => array(
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
						'default'       => __( 'Thanks for your message! We’ll be in touch soon.', 'fl-builder' ),
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
						'default' => __( 'Send', 'fl-builder' ),
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
						'default'    => '#5b5b5b',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => 'i.fl-button-icon.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_duo_color2'     => array(
						'label'      => __( 'DuoTone Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '#757575',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => 'i.fl-button-icon.fad:after',
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
					'btn_width'   => array(
						'type'    => 'select',
						'label'   => __( 'Button Width', 'fl-builder' ),
						'default' => 'auto',
						'options' => array(
							'auto' => _x( 'Auto', 'Width.', 'fl-builder' ),
							'full' => __( 'Full Width', 'fl-builder' ),
						),
						'toggle'  => array(
							'auto' => array(
								'fields' => array( 'btn_align' ),
							),
						),
					),
					'btn_align'   => array(
						'type'       => 'align',
						'label'      => __( 'Button Align', 'fl-builder' ),
						'default'    => 'left',
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-button-wrap',
							'property' => 'text-align',
						),
					),
					'btn_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Button Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button',
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
							'selector' => 'a.fl-button',
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
					'recaptcha_toggle'        => array(
						'type'    => 'select',
						'label'   => 'reCAPTCHA Field',
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
						'help'    => __( 'Validate users with checkbox or in the background.<br />Note: Checkbox and Invisible types use separate API keys.', 'fl-builder' ),
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
		/* translators: %s: url to google admin */
		'description' => sprintf( __( 'Register keys for your website at the <a%1$s>Google Admin Console</a>. You need a different key pair for each reCAPTCHA validation type. <br /><br /><a%2$s>More info about v3 reCAPTCHA.</a>', 'fl-builder' ), ' href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener"', ' href="https://developers.google.com/recaptcha/docs/v3" target="_blank" rel="noopener"' ),
	),
));
