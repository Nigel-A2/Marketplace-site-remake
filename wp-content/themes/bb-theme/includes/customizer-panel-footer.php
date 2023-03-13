<?php

/* Footer Panel */
FLCustomizer::add_panel('fl-footer', array(
	'title'    => _x( 'Footer', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* Footer Widgets Layout Section */
		'fl-footer-widgets-layout' => array(
			'title'   => _x( 'Footer Widgets Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer Widgets Display */
				'fl-footer-widgets-display' => array(
					'setting' => array(
						'default' => 'all',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Footer Widgets Display', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'disabled' => __( 'Disabled', 'fl-automator' ),
							'all'      => __( 'All Pages', 'fl-automator' ),
							'home'     => __( 'Homepage Only', 'fl-automator' ),
						),
					),
				),
			),
		),

		/* Footer Widgets Style Section */
		'fl-footer-widgets-style'  => array(
			'title'   => _x( 'Footer Widgets Style', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer Widgets Background Color */
				'fl-footer-widgets-bg-color'      => array(
					'setting' => array(
						'default' => '#ffffff',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Footer Widgets Background Opacity */
				'fl-footer-widgets-bg-opacity'    => array(
					'setting' => array(
						'default' => '100',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Background Opacity', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
				),

				/* Footer Widgets Background Gradient */
				'fl-footer-widgets-bg-gradient'   => array(
					'setting' => array(
						'default' => '0',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Gradient', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'0' => _x( 'Disabled', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
							'1' => _x( 'Enabled', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
						),
					),
				),

				/* Footer Widgets Background Image */
				'fl-footer-widgets-bg-image'      => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Background Image', 'fl-automator' ),
					),
				),

				/* Footer Widgets Background Repeat */
				'fl-footer-widgets-bg-repeat'     => array(
					'setting' => array(
						'default'   => 'no-repeat',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Repeat', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'no-repeat' => __( 'None', 'fl-automator' ),
							'repeat'    => __( 'Tile', 'fl-automator' ),
							'repeat-x'  => __( 'Horizontal', 'fl-automator' ),
							'repeat-y'  => __( 'Vertical', 'fl-automator' ),
						),
					),
				),

				/* Footer Widgets Background Position */
				'fl-footer-widgets-bg-position'   => array(
					'setting' => array(
						'default'   => 'center top',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Position', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'left top'      => __( 'Left Top', 'fl-automator' ),
							'left center'   => __( 'Left Center', 'fl-automator' ),
							'left bottom'   => __( 'Left Bottom', 'fl-automator' ),
							'right top'     => __( 'Right Top', 'fl-automator' ),
							'right center'  => __( 'Right Center', 'fl-automator' ),
							'right bottom'  => __( 'Right Bottom', 'fl-automator' ),
							'center top'    => __( 'Center Top', 'fl-automator' ),
							'center center' => __( 'Center', 'fl-automator' ),
							'center bottom' => __( 'Center Bottom', 'fl-automator' ),
						),
					),
				),

				/* Footer Widgets Background Attachment */
				'fl-footer-widgets-bg-attachment' => array(
					'setting' => array(
						'default'   => 'scroll',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Attachment', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'scroll' => __( 'Scroll', 'fl-automator' ),
							'fixed'  => __( 'Fixed', 'fl-automator' ),
						),
					),
				),

				/* Footer Widgets Background Size */
				'fl-footer-widgets-bg-size'       => array(
					'setting' => array(
						'default'   => 'auto',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Scale', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'auto'    => __( 'None', 'fl-automator' ),
							'contain' => __( 'Fit', 'fl-automator' ),
							'cover'   => __( 'Fill', 'fl-automator' ),
						),
					),
				),

				/* Footer Widgets Text Color */
				'fl-footer-widgets-text-color'    => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Text Color', 'fl-automator' ),
					),
				),

				/* Footer Widgets Link Color */
				'fl-footer-widgets-link-color'    => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Link Color', 'fl-automator' ),
					),
				),

				/* Footer Widgets Hover Color */
				'fl-footer-widgets-hover-color'   => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Hover Color', 'fl-automator' ),
					),
				),
			),
		),

		/* Footer Layout Section */
		'fl-footer-layout'         => array(
			'title'   => _x( 'Footer Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer Layout */
				'fl-footer-layout'      => array(
					'setting' => array(
						'default' => '1-col',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Layout', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'none'   => __( 'None', 'fl-automator' ),
							'1-col'  => __( '1 Column', 'fl-automator' ),
							'2-cols' => __( '2 Columns', 'fl-automator' ),
						),
					),
				),

				/* Line */
				'fl-footer-line1'       => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Footer Column 1 Layout */
				'fl-footer-col1-layout' => array(
					'setting' => array(
						'default' => 'text',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						/* translators: %d: Column order number */
						'label'   => sprintf( _x( 'Column %d Layout', '%d stands for column order number.', 'fl-automator' ), 1 ),
						'type'    => 'select',
						'choices' => array(
							'text'        => __( 'Text', 'fl-automator' ),
							'social'      => __( 'Social Icons', 'fl-automator' ),
							'social-text' => __( 'Text &amp; Social Icons', 'fl-automator' ),
							'menu'        => __( 'Menu', 'fl-automator' ),
						),
					),
				),

				/* Footer Column 1 Text */
				'fl-footer-col1-text'   => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Control',
						/* translators: %d: Column order number */
						'label' => sprintf( _x( 'Column %d Text', '%d stands for column order number.', 'fl-automator' ), 1 ),
						'type'  => 'textarea',
					),
				),

				/* Line */
				'fl-footer-line2'       => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Footer Column 2 Layout */
				'fl-footer-col2-layout' => array(
					'setting' => array(
						'default' => 'text',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						/* translators: %d: Column order number */
						'label'   => sprintf( _x( 'Column %d Layout', '%d stands for column order number.', 'fl-automator' ), 2 ),
						'type'    => 'select',
						'choices' => array(
							'text'        => __( 'Text', 'fl-automator' ),
							'social'      => __( 'Social Icons', 'fl-automator' ),
							'social-text' => __( 'Text &amp; Social Icons', 'fl-automator' ),
							'menu'        => __( 'Menu', 'fl-automator' ),
						),
					),
				),

				/* Footer Column 2 Text */
				'fl-footer-col2-text'   => array(
					'setting' => array(
						'default'   => '1-800-555-5555 &bull; <a href="mailto:info@mydomain.com">info@mydomain.com</a>',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Control',
						/* translators: %d: Column order number */
						'label' => sprintf( _x( 'Column %d Text', '%d stands for column order number.', 'fl-automator' ), 2 ),
						'type'  => 'textarea',
					),
				),
			),
		),

		/* Footer Style Section */
		'fl-footer-style'          => array(
			'title'   => _x( 'Footer Style', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer Background Color */
				'fl-footer-bg-color'      => array(
					'setting' => array(
						'default' => '#ffffff',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Footer Background Opacity */
				'fl-footer-bg-opacity'    => array(
					'setting' => array(
						'default' => '100',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Background Opacity', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
				),

				/* Footer Background Gradient */
				'fl-footer-bg-gradient'   => array(
					'setting' => array(
						'default' => '0',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Gradient', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'0' => _x( 'Disabled', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
							'1' => _x( 'Enabled', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
						),
					),
				),

				/* Footer Background Image */
				'fl-footer-bg-image'      => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Background Image', 'fl-automator' ),
					),
				),

				/* Footer Background Repeat */
				'fl-footer-bg-repeat'     => array(
					'setting' => array(
						'default'   => 'no-repeat',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Repeat', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'no-repeat' => __( 'None', 'fl-automator' ),
							'repeat'    => __( 'Tile', 'fl-automator' ),
							'repeat-x'  => __( 'Horizontal', 'fl-automator' ),
							'repeat-y'  => __( 'Vertical', 'fl-automator' ),
						),
					),
				),

				/* Footer Background Position */
				'fl-footer-bg-position'   => array(
					'setting' => array(
						'default'   => 'center top',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Position', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'left top'      => __( 'Left Top', 'fl-automator' ),
							'left center'   => __( 'Left Center', 'fl-automator' ),
							'left bottom'   => __( 'Left Bottom', 'fl-automator' ),
							'right top'     => __( 'Right Top', 'fl-automator' ),
							'right center'  => __( 'Right Center', 'fl-automator' ),
							'right bottom'  => __( 'Right Bottom', 'fl-automator' ),
							'center top'    => __( 'Center Top', 'fl-automator' ),
							'center center' => __( 'Center', 'fl-automator' ),
							'center bottom' => __( 'Center Bottom', 'fl-automator' ),
						),
					),
				),

				/* Footer Background Attachment */
				'fl-footer-bg-attachment' => array(
					'setting' => array(
						'default'   => 'scroll',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Attachment', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'scroll' => __( 'Scroll', 'fl-automator' ),
							'fixed'  => __( 'Fixed', 'fl-automator' ),
						),
					),
				),

				/* Footer Background Size */
				'fl-footer-bg-size'       => array(
					'setting' => array(
						'default'   => 'auto',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Scale', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'auto'    => __( 'None', 'fl-automator' ),
							'contain' => __( 'Fit', 'fl-automator' ),
							'cover'   => __( 'Fill', 'fl-automator' ),
						),
					),
				),

				/* Footer Text Color */
				'fl-footer-text-color'    => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Text Color', 'fl-automator' ),
					),
				),

				/* Footer Link Color */
				'fl-footer-link-color'    => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Link Color', 'fl-automator' ),
					),
				),

				/* Footer Hover Color */
				'fl-footer-hover-color'   => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Hover Color', 'fl-automator' ),
					),
				),
			),
		),
		/* Footer Parallax */
		'fl-footer-effect'         => array(
			'title'   => _x( 'Footer Parallax', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer Effect  */
				'fl-footer-parallax-effect' => array(
					'setting' => array(
						'default' => 'disable',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Footer Parallax Effect', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'disable' => __( 'Disabled', 'fl-automator' ),
							'enable'  => __( 'Enabled', 'fl-automator' ),
						),
					),
				),
			),
		),

	),
));
