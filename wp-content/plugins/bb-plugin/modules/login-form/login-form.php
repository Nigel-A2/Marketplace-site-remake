<?php

/**
 * A module that adds a simple login form.
 *
 * @since 2.3
 */
class FLLoginFormModule extends FLBuilderModule {

	/**
	 * @since 1.5.2
	 * @return void
	 */
	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Login Form', 'fl-builder' ),
			'description'     => __( 'Allow users to login/out.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'editor-table.svg',
		));

		if ( ! is_user_logged_in() ) {
			add_action( 'wp_ajax_nopriv_fl_builder_login_form_submit', array( $this, 'login' ) );
		} else {
			add_action( 'wp_ajax_fl_builder_logout_form_submit', array( $this, 'logout' ) );
		}
	}

	/**
	 * Called via AJAX to submit the subscribe form.
	 *
	 * @since 1.5.2
	 * @return string The JSON encoded response.
	 */
	public function login() {
		//	error_log( print_r( $_POST, true ) );
		$name             = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : false;
		$password         = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : false;
		$remember         = isset( $_POST['remember'] ) ? $_POST['remember'] : false;
		$post_id          = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
		$node_id          = isset( $_POST['node_id'] ) ? sanitize_text_field( $_POST['node_id'] ) : false;
		$template_id      = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : false;
		$template_node_id = isset( $_POST['template_node_id'] ) ? sanitize_text_field( $_POST['template_node_id'] ) : false;
		$nonce            = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : false;
		$result           = array(
			'action'  => false,
			'error'   => false,
			'message' => false,
			'url'     => false,
		);

		if ( $name && $password && $node_id && $nonce ) {

			// Get the module settings.
			if ( $template_id ) {
				$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
				$data     = FLBuilderModel::get_layout_data( 'published', $post_id );
				$settings = $data[ $template_node_id ]->settings;
			} else {
				$module   = FLBuilderModel::get_module( $node_id );
				$settings = $module->settings;
			}

			if ( ! $result['error'] ) {
				$creds = array(
					'user_login'    => $name,
					'user_password' => $password,
					'remember'      => $remember,
				);

				if ( ! wp_verify_nonce( $nonce, 'fl-login-form' ) ) {
					wp_send_json_error();
				}

				$user = wp_signon( $creds, is_ssl() );

				if ( is_wp_error( $user ) ) {
					wp_send_json_error( $user->get_error_message() );
				}

				$args = array(
					'url' => ( 'url' === $settings->redirect_to ) ? ( empty( $settings->success_url ) ? 'current' : $settings->success_url ) : $settings->redirect_to,
				);

				do_action( 'fl_builder_login_form_submission_complete', $settings, $password, $name, $template_id, $post_id );

				wp_send_json_success( $args );
			}
		} else {
			wp_send_json_error( $result['error'] );
		}
	}

	public static function logout() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'fl-login-form' ) ) {
			wp_logout();
			$args = array(
				'url' => '',
			);
			wp_send_json_success( $args );
		}
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_button_settings( $id ) {
		$settings = array(
			'link'        => '#',
			'link_target' => '_self',
			'width'       => 'full',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, $id ) ) {
				if ( 0 === strpos( $key, $id ) ) {
					$key              = str_replace( $id, '', $key );
					$settings[ $key ] = $value;
				}
			}
		}
		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module( 'FLLoginFormModule', array(
	'general'       => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'structure' => array(
				'title'  => __( 'Structure', 'fl-builder' ),
				'fields' => array(
					'layout'              => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'stacked',
						'toggle'  => array(
							'stacked' => array(
								'fields' => array( 'remember', 'forget' ),
							),
						),
						'options' => array(
							'stacked' => __( 'Stacked', 'fl-builder' ),
							'inline'  => __( 'Inline', 'fl-builder' ),
						),
					),
					'name_field_text'     => array(
						'type'    => 'text',
						'label'   => __( 'Name Field Text', 'fl-builder' ),
						'default' => __( 'Username', 'fl-builder' ),
					),
					'password_field_text' => array(
						'type'    => 'text',
						'label'   => __( 'Password Field Text', 'fl-builder' ),
						'default' => __( 'Password', 'fl-builder' ),
					),
					'remember'            => array(
						'type'    => 'select',
						'label'   => __( 'Show Remember Login', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
					),
					'forget'              => array(
						'type'    => 'select',
						'label'   => __( 'Show Forget Password Link', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
					),

				),
			),
		),
	),
	'button'        => array(
		'title'    => __( 'Login Button', 'fl-builder' ),
		'sections' => array(
			'btn_general' => array(
				'title'  => '',
				'fields' => array(
					'btn_text'    => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'fl-builder' ),
						'default' => __( 'Login', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-button-text',
						),
					),
					'redirect_to' => array(
						'type'    => 'select',
						'label'   => __( 'Redirect To', 'fl-builder' ),
						'default' => 'url',
						'options' => array(
							'url'      => __( 'URL', 'fl-builder' ),
							'current'  => __( 'Current URL', 'fl-builder' ),
							'referrer' => __( 'Referrer URL', 'fl-builder' ),
						),
						'toggle'  => array(
							'url' => array(
								'fields' => array( 'success_url' ),
							),
						),
					),
					'success_url' => array(
						'type'        => 'link',
						'label'       => __( 'Redirect URL', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
						'connections' => array( 'url' ),
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
			'btn_colors'  => array(
				'title'  => __( 'Button Background', 'fl-builder' ),
				'fields' => array(
					'btn_bg_color'       => array(
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
					'btn_bg_hover_color' => array(
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

				),
			),

		),
	),
	'logout_button' => array(
		'title'    => __( 'Logout Button', 'fl-builder' ),
		'sections' => array(
			'lo_btn_general' => array(
				'title'  => '',
				'fields' => array(
					'lo_btn_enabled' => array(
						'type'    => 'select',
						'label'   => __( 'Show Logout Button', 'fl-builder' ),
						'default' => 'yes',
						'preview' => 'none',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
					),
					'lo_btn_text'    => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'fl-builder' ),
						'default' => __( 'Logout', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-button-text',
						),
					),
					'lo_success_url' => array(
						'type'        => 'link',
						'label'       => __( 'Redirect URL', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
						'connections' => array( 'url' ),
					),
				),
			),
			'lo_btn_icon'    => array(
				'title'  => __( 'Button Icon', 'fl-builder' ),
				'fields' => array(
					'lo_btn_icon'           => array(
						'type'        => 'icon',
						'label'       => __( 'Button Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields' => array( 'lo_btn_icon_position', 'lo_btn_icon_animation' ),
						),
					),
					'lo_btn_icon_position'  => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Position', 'fl-builder' ),
						'default' => 'before',
						'options' => array(
							'before' => __( 'Before Text', 'fl-builder' ),
							'after'  => __( 'After Text', 'fl-builder' ),
						),
					),
					'lo_btn_bg_color'       => array(
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
					'lo_btn_bg_color_hover' => array(
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
				),
			),
		),
	),

	'style'         => array(
		'title'    => __( 'Shared Styles', 'fl-builder' ),
		'sections' => array(
			'style' => array(
				'title'  => '',
				'fields' => array(
					'btn_padding'          => array(
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
					'input_padding'        => array(
						'type'       => 'dimension',
						'label'      => __( 'Input Field Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-form-field input[type=text],.fl-form-field input[type=password]',
							'property' => 'padding',
						),
					),
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
					'input_typography'     => array(
						'type'       => 'typography',
						'label'      => __( 'Input Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-form-field input[type=text],.fl-form-field input[type=password]',
						),
					),
				),
			),
		),
	),
));
