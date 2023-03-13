<?php

/* Header Panel */
FLCustomizer::add_panel('fl-header', array(
	'title'    => _x( 'Header', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* Top Bar Layout Section */
		'fl-topbar-layout' => array(
			'title'   => _x( 'Top Bar Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Top Bar Layout */
				'fl-topbar-layout'      => array(
					'setting' => array(
						'default' => 'none',
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
				'fl-topbar-line1'       => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Top Bar Column 1 Layout */
				'fl-topbar-col1-layout' => array(
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
							'text-social' => __( 'Text &amp; Social Icons', 'fl-automator' ),
							'menu'        => __( 'Menu', 'fl-automator' ),
							'menu-social' => __( 'Menu &amp; Social Icons', 'fl-automator' ),
							'social'      => __( 'Social Icons', 'fl-automator' ),
						),
					),
				),

				/* Top Bar Column 1 Text */
				'fl-topbar-col1-text'   => array(
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
				'fl-topbar-line2'       => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Top Bar Column 2 Layout */
				'fl-topbar-col2-layout' => array(
					'setting' => array(
						'default' => 'menu',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						/* translators: %d: Column order number */
						'label'   => sprintf( _x( 'Column %d Layout', '%d stands for column order number.', 'fl-automator' ), 2 ),
						'type'    => 'select',
						'choices' => array(
							'text'        => __( 'Text', 'fl-automator' ),
							'text-social' => __( 'Text &amp; Social Icons', 'fl-automator' ),
							'menu'        => __( 'Menu', 'fl-automator' ),
							'menu-social' => __( 'Menu &amp; Social Icons', 'fl-automator' ),
							'social'      => __( 'Social Icons', 'fl-automator' ),
						),
					),
				),

				/* Top Bar Column 2 Text */
				'fl-topbar-col2-text'   => array(
					'setting' => array(
						'default'   => '',
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

		/* Top Bar Style Section */
		'fl-topbar-style'  => array(
			'title'   => _x( 'Top Bar Style', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Top Bar Background Color */
				'fl-topbar-bg-color'      => array(
					'setting' => array(
						'default' => '#ffffff',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Top Bar Background Opacity */
				'fl-topbar-bg-opacity'    => array(
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

				/* Top Bar Background Gradient */
				'fl-topbar-bg-gradient'   => array(
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

				/* Top Bar Background Image */
				'fl-topbar-bg-image'      => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Background Image', 'fl-automator' ),
					),
				),

				/* Top Bar Background Repeat */
				'fl-topbar-bg-repeat'     => array(
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

				/* Top Bar Background Position */
				'fl-topbar-bg-position'   => array(
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

				/* Top Bar Background Attachment */
				'fl-topbar-bg-attachment' => array(
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

				/* Top Bar Background Size */
				'fl-topbar-bg-size'       => array(
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

				/* Top Bar Text Color */
				'fl-topbar-text-color'    => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Text Color', 'fl-automator' ),
					),
				),

				/* Top Bar Link Color */
				'fl-topbar-link-color'    => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Link Color', 'fl-automator' ),
					),
				),

				/* Top Bar Hover Color */
				'fl-topbar-hover-color'   => array(
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

		/* Header Layout Section */
		'fl-header-layout' => array(
			'title'   => _x( 'Header Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Header Layout */
				'fl-header-layout'                   => array(
					'setting' => array(
						'default' => 'right',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Layout', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'none'                 => __( 'None', 'fl-automator' ),
							'bottom'               => __( 'Nav Bottom', 'fl-automator' ),
							'right'                => __( 'Nav Right', 'fl-automator' ),
							'left'                 => __( 'Nav Left', 'fl-automator' ),
							'centered'             => __( 'Nav Centered', 'fl-automator' ),
							'centered-inline-logo' => __( 'Nav Centered + Inline Logo', 'fl-automator' ),
							'vertical-left'        => __( 'Nav Vertical Left', 'fl-automator' ),
							'vertical-right'       => __( 'Nav Vertical Right', 'fl-automator' ),
						),
					),
				),

				/* Inline Logo Side */
				'fl-inline-logo-side'                => array(
					'setting' => array(
						'default' => 'right',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Inline Logo Position', 'fl-automator' ),
						'description' => __( 'The inline logo will appear on the left or right side of odd menu items.', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'left'  => __( 'Left', 'fl-automator' ),
							'right' => __( 'Right', 'fl-automator' ),
						),
					),
				),

				/* Vertical Header Width */
				'fl-vertical-header-width'           => array(
					'setting' => array(
						'default'   => '230',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Vertical Nav Width', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 200,
							'max'  => 400,
							'step' => 1,
						),
					),
				),

				/* Header Padding */
				'fl-header-padding'                  => array(
					'setting' => array(
						'default'   => '30',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Padding', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
				),

				/* Fixed Header */
				'fl-fixed-header'                    => array(
					'setting' => array(
						'default' => 'fadein',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Fixed Header', 'fl-automator' ),
						'description' => __( 'Show a fixed header as the page is scrolled.', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'hidden' => _x( 'Disabled', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
							'fadein' => _x( 'Fade In', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
							'shrink' => _x( 'Shrink', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
							'fixed'  => _x( 'Fixed', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
						),
					),
				),

				/* Logo Max Height */
				'fl-logo-max-height'                 => array(
					'setting' => array(
						'default'   => '46',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Logo Max Height', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 200,
							'step' => 1,
						),
					),
				),

				/* Top Padding */
				'fl-fixed-header-padding-top'        => array(
					'setting' => array(
						'default' => 'auto',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Top Padding', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'auto'   => __( 'Auto', 'fl-automator' ),
							'custom' => __( 'Custom', 'fl-automator' ),
						),
					),
				),

				/* Custom Top Padding */
				'fl-fixed-header-padding-top-custom' => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Custom Top Padding', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 300,
							'step' => 1,
						),
					),
				),

				/* Hide Header Until Scroll */
				'fl-hide-until-scroll-header'        => array(
					'setting' => array(
						'default' => 'disable',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Hide Header Until Scroll', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'disable' => __( 'Disabled', 'fl-automator' ),
							'enable'  => __( 'Enabled', 'fl-automator' ),
						),
					),
				),

				/* Scroll Distance for Hide Header Until Scroll */
				'fl-scroll-distance'                 => array(
					'setting' => array(
						'default' => '200',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Scroll Distance', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 50,
							'max'  => 1000,
							'step' => 1,
						),
					),
				),

				/* Line */
				'fl-header-line1'                    => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Header Content Layout */
				'fl-header-content-layout'           => array(
					'setting' => array(
						'default' => 'social-text',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Content Layout', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'none'        => __( 'None', 'fl-automator' ),
							'text'        => __( 'Text', 'fl-automator' ),
							'social'      => __( 'Social Icons', 'fl-automator' ),
							'social-text' => __( 'Text &amp; Social Icons', 'fl-automator' ),
						),
					),
				),

				/* Header Content Text */
				'fl-header-content-text'             => array(
					'setting' => array(
						'default' => 'Call Us! 1-800-555-5555',
					),
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'Content Text', 'fl-automator' ),
						'type'  => 'textarea',
					),
				),
			),
		),

		/* Header Style Section */
		'fl-header-style'  => array(
			'title'   => _x( 'Header Style', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Header Background Color */
				'fl-header-bg-color'      => array(
					'setting' => array(
						'default' => '#ffffff',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Header Background Opacity */
				'fl-header-bg-opacity'    => array(
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

				/* Nav Drop Shadow Size */
				'fl-nav-shadow-size'      => array(
					'setting' => array(
						'default' => '4',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Drop Shadow Size', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 75,
							'step' => 1,
						),
					),
				),

				/* Nav Shadow Color */
				'fl-nav-shadow-color'     => array(
					'setting' => array(
						'default' => '#cecece',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Drop Shadow Color', 'fl-automator' ),
					),
				),

				/* Header Background Gradient */
				'fl-header-bg-gradient'   => array(
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

				/* Header Background Image */
				'fl-header-bg-image'      => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Background Image', 'fl-automator' ),
					),
				),

				/* Header Background Repeat */
				'fl-header-bg-repeat'     => array(
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

				/* Header Background Position */
				'fl-header-bg-position'   => array(
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

				/* Header Background Attachment */
				'fl-header-bg-attachment' => array(
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

				/* Header Background Size */
				'fl-header-bg-size'       => array(
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

				/* Header Text Color */
				'fl-header-text-color'    => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Text Color', 'fl-automator' ),
					),
				),

				/* Header Link Color */
				'fl-header-link-color'    => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Link Color', 'fl-automator' ),
					),
				),

				/* Header Hover Color */
				'fl-header-hover-color'   => array(
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

		/* Header Logo Section */
		'fl-header-logo'   => array(
			'title'   => _x( 'Header Logo', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Logo Type */
				'fl-logo-type'                 => array(
					'setting' => array(
						'default' => 'text',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Logo Type', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'text'  => __( 'Text', 'fl-automator' ),
							'image' => __( 'Image', 'fl-automator' ),
						),
					),
				),

				/* Logo Image (Regular) */
				'fl-logo-image'                => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Logo Image (Regular)', 'fl-automator' ),
					),
				),

				/* Logo Image (Retina) */
				'fl-logo-image-retina'         => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Logo Image (Retina)', 'fl-automator' ),
					),
				),

				/* Sticky Header Logo */
				'fl-sticky-header-logo'        => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class'       => 'WP_Customize_Image_Control',
						'label'       => __( 'Fade In Header Logo', 'fl-automator' ),
						'description' => __( 'Use a different logo when you have a Fade In header', 'fl-automator' ),
					),
				),

				/* Sticky Header Logo (Retina) */
				'fl-sticky-header-logo-retina' => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Fade In Header Logo (Retina)', 'fl-automator' ),
					),
				),

				/* Mobile Header Logo */
				'fl-mobile-header-logo'        => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class'       => 'WP_Customize_Image_Control',
						'label'       => __( 'Mobile Header Logo', 'fl-automator' ),
						'description' => __( 'Use a different logo for mobile devices.', 'fl-automator' ),
					),
				),

				/* Logo Text */
				'fl-logo-text'                 => array(
					'setting' => array(
						'default'   => get_bloginfo( 'name' ),
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'Logo Text', 'fl-automator' ),
						'type'  => 'text',
					),
				),

				/* Logo Text */
				'fl-theme-tagline'             => array(
					'setting' => array(
						'default'   => false,
						'transport' => 'refresh',
					),
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'Show Tagline', 'fl-automator' ),
						'type'  => 'checkbox',
					),
				),

				/* Logo Font Family */
				'fl-logo-font-family'          => array(
					'setting' => array(
						'default'   => 'Helvetica',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Family', 'fl-automator' ),
						'type'    => 'font',
						'connect' => 'fl-logo-font-weight',
					),
				),

				/* Logo Font Weight */
				'fl-logo-font-weight'          => array(
					'setting' => array(
						'default' => '400',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Weight', 'fl-automator' ),
						'type'    => 'font-weight',
						'connect' => 'fl-logo-font-family',
					),
				),

				/* Logo Font Size */
				'fl-logo-font-size'            => array(
					'setting' => array(
						'default'   => '30',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Size', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => get_font_size_limits(),
					),
				),
				// logo text colour
				'fl-logo-text-color'           => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Logo Text Color', 'fl-automator' ),
					),
				),

				// logo text hover colour
				'fl-logo-text-hover-color'     => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Logo Text Hover Color', 'fl-automator' ),
					),
				),

				// logo text hover colour
				'fl-logo-tagline-color'        => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Logo Tagline Color', 'fl-automator' ),
					),
				),

				/* Logo Top Spacing */
				'fl-header-logo-top-spacing'   => array(
					'setting' => array(
						'default'   => '50',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Logo Top Spacing', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 1,
							'max'  => 200,
							'step' => 1,
						),
					),
				),
			),
		),

		/* Nav Layout Section */
		'fl-nav-layout'    => array(
			'title'   => _x( 'Nav Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Nav Item Spacing */
				'fl-nav-item-spacing'            => array(
					'setting' => array(
						'default'   => '15',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Nav Item Spacing', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 5,
							'max'  => 30,
							'step' => 1,
						),
					),
				),

				/* Nav Menu Top Spacing */
				'fl-nav-menu-top-spacing'        => array(
					'setting' => array(
						'default'   => '30',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Nav Menu Top Spacing', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 1,
							'max'  => 200,
							'step' => 1,
						),
					),
				),

				/* Nav Item Align */
				'fl-nav-item-align'              => array(
					'setting' => array(
						'default' => 'left',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Nav Item Align', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'left'   => __( 'Left', 'fl-automator' ),
							'center' => __( 'Center', 'fl-automator' ),
							'right'  => __( 'Right', 'fl-automator' ),
						),
					),
				),

				/* Nav Search */
				'fl-header-nav-search'           => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Nav Search Icon', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => _x( 'Enabled', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
							'hidden'  => _x( 'Disabled', 'Used for multiple Customizer options values. Use generalized form of the word when translating.', 'fl-automator' ),
						),
					),
				),

				/* Mobile Nav Toggle */
				'fl-mobile-nav-toggle'           => array(
					'setting' => array(
						'default' => 'button',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Responsive Nav Toggle', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'button' => __( 'Menu Button', 'fl-automator' ),
							'icon'   => __( 'Hamburger Icon', 'fl-automator' ),
						),
					),
				),

				/* Mobile Nav Text */
				'fl-mobile-nav-text'             => array(
					'setting' => array(
						'default' => _x( 'Menu', 'Mobile navigation toggle button text.', 'fl-automator' ),
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Menu Button Text', 'fl-automator' ),
						'type'        => 'text',
						'input_attrs' => array(
							'placeholder' => _x( 'Menu', 'Mobile navigation toggle button text.', 'fl-automator' ),
						),
					),
				),

				/* Responsive Nav Breakpoint */
				'fl-nav-breakpoint'              => array(
					'setting' => array(
						'default' => 'mobile',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Responsive Nav Breakpoint', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'always'        => __( 'Always', 'fl-automator' ),
							'medium-mobile' => __( 'Medium &amp; Small Devices Only', 'fl-automator' ),
							'mobile'        => __( 'Small Devices Only', 'fl-automator' ),
						),
					),
				),

				/* Hamburger Icon Top Position (Responsive) */
				'fl-hamburger-icon-top-position' => array(
					'setting' => array(
						'default' => '24',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						'label'      => __( 'Hamburger Icon Position', 'fl-automator' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0,
							'max'  => 200,
							'step' => 1,
						),
						'responsive' => true,
					),
				),

				/* Responsive Nav Layout */
				'fl-nav-mobile-layout'           => array(
					'setting' => array(
						'default' => 'dropdown',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Responsive Nav Layout', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'dropdown'     => __( 'Dropdown', 'fl-automator' ),
							'overlay'      => __( 'Flyout Overlay', 'fl-automator' ),
							'push'         => __( 'Flyout Push', 'fl-automator' ),
							'push-opacity' => __( 'Flyout Push with Opacity', 'fl-automator' ),
						),
					),
				),

				'fl-nav-mobile-layout-position'  => array(
					'setting' => array(
						'default' => 'left',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Responsive Nav Layout Position', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'left'  => __( 'Left', 'fl-automator' ),
							'right' => __( 'Right', 'fl-automator' ),
						),
					),
				),

				/* Submenu Toggle */
				'fl-nav-submenu-toggle'          => array(
					'setting' => array(
						'default' => 'disable',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Responsive Submenu Toggle', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'disable' => __( 'Disabled', 'fl-automator' ),
							'enable'  => __( 'Enabled', 'fl-automator' ),
						),
					),
				),

				/* Responsive Collapse */
				'fl-nav-collapse-menu'           => array(
					'setting' => array(
						'default' => '0',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Responsive Collapse', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'1' => __( 'Yes', 'fl-automator' ),
							'0' => __( 'No', 'fl-automator' ),
						),
						'description' => __( 'Only allow one menu item at a time to be expanded?', 'fl-automator' ),
					),
				),
			),
		),

		/* Nav Style Section */
		'fl-nav-style'     => array(
			'title'   => _x( 'Nav Style', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Submenu Indicator */
				'fl-nav-submenu-indicator' => array(
					'setting' => array(
						'default' => 'disable',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Submenu Indicator', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'disable' => __( 'Disabled', 'fl-automator' ),
							'enable'  => __( 'Enabled', 'fl-automator' ),
						),
					),
				),

				/* Nav Background Color */
				'fl-nav-bg-color'          => array(
					'setting' => array(
						'default' => '#ffffff',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Nav Background Opacity */
				'fl-nav-bg-opacity'        => array(
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

				/* Nav Background Gradient */
				'fl-nav-bg-gradient'       => array(
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

				/* Nav Background Image */
				'fl-nav-bg-image'          => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Background Image', 'fl-automator' ),
					),
				),

				/* Nav Background Repeat */
				'fl-nav-bg-repeat'         => array(
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

				/* Nav Background Position */
				'fl-nav-bg-position'       => array(
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

				/* Nav Background Attachment */
				'fl-nav-bg-attachment'     => array(
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

				/* Nav Background Size */
				'fl-nav-bg-size'           => array(
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

				/* Nav Link Color */
				'fl-nav-link-color'        => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Link Color', 'fl-automator' ),
					),
				),

				/* Nav Hover Color */
				'fl-nav-hover-color'       => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Hover Color', 'fl-automator' ),
					),
				),

				/* Nav Font Family */
				'fl-nav-font-family'       => array(
					'setting' => array(
						'default'   => 'Helvetica',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Family', 'fl-automator' ),
						'type'    => 'font',
						'connect' => 'fl-nav-font-weight',
					),
				),

				/* Nav Font Weight */
				'fl-nav-font-weight'       => array(
					'setting' => array(
						'default' => '400',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Weight', 'fl-automator' ),
						'type'    => 'font-weight',
						'connect' => 'fl-nav-font-family',
					),
				),

				/* Nav Font Format */
				'fl-nav-font-format'       => array(
					'setting' => array(
						'default'   => 'none',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Font Format', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'none'       => __( 'Regular', 'fl-automator' ),
							'capitalize' => __( 'Capitalize', 'fl-automator' ),
							'uppercase'  => __( 'Uppercase', 'fl-automator' ),
							'lowercase'  => __( 'Lowercase', 'fl-automator' ),
						),
					),
				),

				/* Nav Font Size */
				'fl-nav-font-size'         => array(
					'setting' => array(
						'default'   => '16',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Size', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => get_font_size_limits(),
					),
				),
			),
		),
	),
));
