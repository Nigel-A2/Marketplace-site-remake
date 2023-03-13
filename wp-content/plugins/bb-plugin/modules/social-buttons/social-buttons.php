<?php

/**
 * @class FLShortcodeModule
 */
class FLSocialButtonModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Social Buttons', 'fl-builder' ),
			'description'     => __( 'Displays social buttons.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'share-alt2.svg',
		));
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		global $post;

		// If the URL is not custom, build the current page's URL
		if ( 'current' == $settings->url_type ) {
			$settings->the_url = get_permalink( $post->ID );
		} else {
			$settings->the_url = $settings->custom_url;
		}

		return $settings;
	}

	/**
	 * Adds the fb-root div to the page footer
	 * @method add_fb_root
	 */
	public function add_fb_root() {
		add_action( 'wp_footer', array( 'FLSocialButtonModule', 'fb_root' ) );
	}

	/**
	 * Actually echos the fb_root div
	 * @method fb_root
	 */
	public static function fb_root() {
		echo '<div id="fb-root"></div>';
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLSocialButtonModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'url_type'      => array(
						'type'    => 'select',
						'label'   => __( 'Target URL', 'fl-builder' ),
						'default' => 'current',
						'options' => array(
							'custom'  => __( 'Custom', 'fl-builder' ),
							'current' => __( 'Current Page', 'fl-builder' ),
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'custom_url' ),
							),
						),
						'help'    => __( 'The Target URL field correlates to the page you would like your social icons to interface with. For example, if you show Facebook, the user will "Like" whatever you put in this field.', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'custom_url'    => array(
						'type'        => 'text',
						'label'       => __( 'Custom URL', 'fl-builder' ),
						'placeholder' => 'http://www.example.com',
						'preview'     => array(
							'type' => 'none',
						),
					),
					'align'         => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'fl-builder' ),
						'default' => 'left',
						'options' => array(
							'center' => __( 'Center', 'fl-builder' ),
							'left'   => __( 'Left', 'fl-builder' ),
							'right'  => __( 'Right', 'fl-builder' ),
						),
					),
					'show_facebook' => array(
						'type'    => 'select',
						'label'   => __( 'Show Facebook', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
					),
					'show_twitter'  => array(
						'type'    => 'select',
						'label'   => __( 'Show Twitter', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
					),
					'show_gplus'    => array(
						'type'    => 'select',
						'label'   => __( 'Show Google+', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
));
