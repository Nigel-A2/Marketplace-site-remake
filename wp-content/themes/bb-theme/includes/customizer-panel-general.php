<?php

/* General Panel */
FLCustomizer::add_panel('fl-general', array(
	'title'    => _x( 'General', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* Layout Section */
		'fl-layout'       => array(
			'title'   => _x( 'Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Layout Width */
				'fl-layout-width'        => array(
					'setting' => array(
						'default' => 'full-width',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Width', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'boxed'      => __( 'Boxed', 'fl-automator' ),
							'full-width' => __( 'Full Width', 'fl-automator' ),
						),
					),
				),

				/* Content Width */
				'fl-content-width'       => array(
					'setting' => array(
						'default' => '1020',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Content Width', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 960,
							'max'  => 1920,
							'step' => 1,
						),
					),
				),

				/* Spacing */
				'fl-layout-spacing'      => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Spacing', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 150,
							'step' => 1,
						),
					),
				),

				/* Drop Shadow Size */
				'fl-layout-shadow-size'  => array(
					'setting' => array(
						'default' => '0',
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

				/* Drop Shadow Color */
				'fl-layout-shadow-color' => array(
					'setting' => array(
						'default' => '#d9d9d9',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Drop Shadow Color', 'fl-automator' ),
					),
				),

				/* Scroll To Top Button */
				'fl-scroll-to-top'       => array(
					'setting' => array(
						'default' => 'disable',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Scroll To Top Button', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'enable'  => __( 'Enabled', 'fl-automator' ),
							'disable' => __( 'Disabled', 'fl-automator' ),
						),
					),
				),
				/* Framework */
				'fl-framework'           => array(
					'setting' => array(
						'default' => 'base',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'CSS Framework', 'fl-automator' ),
						'type'        => 'select',
						'description' =>
						__( 'Select a CSS framework for the theme. Default is a bare minimal Bootstrap 3.', 'fl-automator' ),
						'choices'     => array(
							'base'        => __( 'Minimal Bootstrap 3', 'fl-automator' ),
							'base-4'      => __( 'Minimal Bootstrap 4', 'fl-automator' ),
							'bootstrap'   => __( 'Full Bootstrap 3', 'fl-automator' ),
							'bootstrap-4' => __( 'Full Bootstrap 4', 'fl-automator' ),
						),
					),
				),

				/* Font Awesome */
				'fl-awesome'             => array(
					'setting' => array(
						'default' => 'none',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Font Awesome Icons', 'fl-automator' ),
						'type'        => 'select',
						'description' =>
						__( 'Select which icon library to load on all pages. If unsure choose None.', 'fl-automator' ),
						'choices'     => array(
							'none' => __( 'None', 'fl-automator' ),
							'fa4'  => __( 'Font Awesome 4 Shim', 'fl-automator' ),
							'fa5'  => __( 'Font Awesome 5', 'fl-automator' ),
						),
					),
				),
				/* Medium Breakpoint */
				'fl-medium-breakpoint'   => array(
					'setting' => array(
						'default' => 992,
					),
					'control' => array(
						'class'       => 'FLCustomizerControl',
						'label'       => __( 'Theme Medium Breakpoint', 'fl-automator' ),
						'description' => __( 'Medium device behavior starts below this setting.', 'fl-automator' ),
						'type'        => 'slider',
						'choices'     => array(
							'min'  => 500,
							'max'  => 1200,
							'step' => 1,
						),
					),
				),

				/* Mobile Breakpoint */
				'fl-mobile-breakpoint'   => array(
					'setting' => array(
						'default' => 768,
					),
					'control' => array(
						'class'       => 'FLCustomizerControl',
						'label'       => __( 'Theme Mobile Breakpoint', 'fl-automator' ),
						'description' => __( 'Mobile device behavior starts below this setting.', 'fl-automator' ),
						'type'        => 'slider',
						'choices'     => array(
							'min'  => 500,
							'max'  => 1200,
							'step' => 1,
						),
					),
				),
			),
		),

		/* Body Background Section */
		'fl-body-bg'      => array(
			'title'   => _x( 'Background', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Background Color */
				'fl-body-bg-color'      => array(
					'setting' => array(
						'default' => '#f2f2f2',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Background Image */
				'fl-body-bg-image'      => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Background Image', 'fl-automator' ),
					),
				),

				/* Background Repeat */
				'fl-body-bg-repeat'     => array(
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

				/* Background Position */
				'fl-body-bg-position'   => array(
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

				/* Background Attachment */
				'fl-body-bg-attachment' => array(
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

				/* Background Size */
				'fl-body-bg-size'       => array(
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
			),
		),

		/* Accent Color Section */
		'fl-accent-color' => array(
			'title'   => _x( 'Accent Color', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Accent Color */
				'fl-accent'       => array(
					'setting' => array(
						'default' => '#428bca',
					),
					'control' => array(
						'class'       => 'WP_Customize_Color_Control',
						'label'       => __( 'Color', 'fl-automator' ),
						'description' => __( 'The accent color will be used to color elements such as links and buttons as well as various elements in your theme.', 'fl-automator' ),
					),
				),

				/* Accent Hover Color */
				'fl-accent-hover' => array(
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

		/* Heading Font Section */
		'fl-heading-font' => array(
			'title'   => _x( 'Headings', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				'fl-heading-style'       => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class'    => 'WP_Customize_Control',
						'type'     => 'select',
						'label'    => __( 'Style Headings', 'fl-automator' ),
						'priority' => 0,
						'choices'  => array(
							''      => __( 'All Headings', 'fl-automator' ),
							'title' => __( 'Custom H1 Style', 'fl-automator' ),
						),
					),
				),

				'fl-title-text-color'    => array(
					'setting' => array(
						'default' => '#333333',
					),
					'control' => array(
						'class'    => 'WP_Customize_Color_Control',
						'label'    => __( 'H1 Color', 'fl-automator' ),
						'priority' => 1.0,
					),
				),
				'fl-title-font-family'   => array(
					'setting' => array(
						'default' => 'Helvetica',
					),
					'control' => array(
						'class'    => 'FLCustomizerControl',
						'label'    => __( 'H1 Font Family', 'fl-automator' ),
						'priority' => 1.1,
						'type'     => 'font',
						'connect'  => 'fl-title-font-weight',
					),
				),
				/* Heading Font Weight */
				'fl-title-font-weight'   => array(
					'setting' => array(
						'default' => '400',
					),
					'control' => array(
						'class'    => 'FLCustomizerControl',
						'label'    => __( 'H1 Font Weight', 'fl-automator' ),
						'type'     => 'font-weight',
						'priority' => 1.2,
						'connect'  => 'fl-title-font-family',
					),
				),
				/* Heading Font Format */
				'fl-title-font-format'   => array(
					'setting' => array(
						'default' => 'none',
					),
					'control' => array(
						'class'    => 'WP_Customize_Control',
						'label'    => __( 'H1 Font Format', 'fl-automator' ),
						'priority' => 1.3,
						'type'     => 'select',
						'choices'  => array(
							'none'       => __( 'Regular', 'fl-automator' ),
							'capitalize' => __( 'Capitalize', 'fl-automator' ),
							'uppercase'  => __( 'Uppercase', 'fl-automator' ),
							'lowercase'  => __( 'Lowercase', 'fl-automator' ),
						),
					),
				),

				/* Below Title Styles */
				'fl-title-heading-line'  => array(
					'control' => array(
						'class'    => 'FLCustomizerControl',
						'type'     => 'line',
						'priority' => 2.0,
					),
				),

				/* Heading Text Color */
				'fl-heading-text-color'  => array(
					'setting' => array(
						'default' => '#333333',
					),
					'control' => array(
						'class'    => 'WP_Customize_Color_Control',
						'label'    => __( 'Color', 'fl-automator' ),
						'priority' => 3.0,
					),
				),

				/* Heading Font Family */
				'fl-heading-font-family' => array(
					'setting' => array(
						'default'   => 'Helvetica',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'    => 'FLCustomizerControl',
						'label'    => __( 'Font Family', 'fl-automator' ),
						'type'     => 'font',
						'connect'  => 'fl-heading-font-weight',
						'priority' => 3.1,
					),
				),

				/* Heading Font Weight */
				'fl-heading-font-weight' => array(
					'setting' => array(
						'default' => '400',
					),
					'control' => array(
						'class'    => 'FLCustomizerControl',
						'label'    => __( 'Font Weight', 'fl-automator' ),
						'type'     => 'font-weight',
						'connect'  => 'fl-heading-font-family',
						'priority' => 3.2,
					),
				),

				/* Heading Font Format */
				'fl-heading-font-format' => array(
					'setting' => array(
						'default'   => 'none',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'    => 'WP_Customize_Control',
						'label'    => __( 'Font Format', 'fl-automator' ),
						'type'     => 'select',
						'priority' => 3.3,
						'choices'  => array(
							'none'       => __( 'Regular', 'fl-automator' ),
							'capitalize' => __( 'Capitalize', 'fl-automator' ),
							'uppercase'  => __( 'Uppercase', 'fl-automator' ),
							'lowercase'  => __( 'Lowercase', 'fl-automator' ),
						),
					),
				),

				/* Line */
				'fl-heading-font-line1'  => array(
					'control' => array(
						'class'    => 'FLCustomizerControl',
						'type'     => 'line',
						'priority' => 4.0,
					),
				),

				/* H1 Font Size */
				'fl-h1-font-size'        => array(
					'setting' => array(
						'default'   => '36',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Font Size', '%s stands for HTML heading tag.', 'fl-automator' ), 'H1' ),
						'type'       => 'slider',
						'priority'   => 5.0,
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				/* H1 Line Height */
				'fl-h1-line-height'      => array(
					'setting' => array(
						'default'   => '1.4',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Line Height', '%s stands for HTML heading tag.', 'fl-automator' ), 'H1' ),
						'priority'   => 5.1,
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),

				/* H1 Letter Spacing */
				'fl-h1-letter-spacing'   => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Letter Spacing', '%s stands for HTML heading tag.', 'fl-automator' ), 'H1' ),
						'priority'   => 5.2,
						'type'       => 'slider',
						'choices'    => array(
							'min'  => -3,
							'max'  => 10,
							'step' => 1,
						),
						'responsive' => true,
					),
				),

				/* Line */
				'fl-h1-line'             => array(
					'control' => array(
						'class'    => 'FLCustomizerControl',
						'type'     => 'line',
						'priority' => 5.3,
					),
				),

				/* H2 Font Size */
				'fl-h2-font-size'        => array(
					'setting' => array(
						'default'   => '30',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Font Size', '%s stands for HTML heading tag.', 'fl-automator' ), 'H2' ),
						'type'       => 'slider',
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				/* H2 Line Height */
				'fl-h2-line-height'      => array(
					'setting' => array(
						'default'   => '1.4',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Line Height', '%s stands for HTML heading tag.', 'fl-automator' ), 'H2' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),

				/* H2 Letter Spacing */
				'fl-h2-letter-spacing'   => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Letter Spacing', '%s stands for HTML heading tag.', 'fl-automator' ), 'H2' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => -3,
							'max'  => 10,
							'step' => 1,
						),
						'responsive' => true,
					),
				),

				/* Line */
				'fl-h2-line'             => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* H3 Font Size */
				'fl-h3-font-size'        => array(
					'setting' => array(
						'default'   => '24',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Font Size', '%s stands for HTML heading tag.', 'fl-automator' ), 'H3' ),
						'type'       => 'slider',
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				/* H3 Line Height */
				'fl-h3-line-height'      => array(
					'setting' => array(
						'default'   => '1.4',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Line Height', '%s stands for HTML heading tag.', 'fl-automator' ), 'H3' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),

				/* H3 Letter Spacing */
				'fl-h3-letter-spacing'   => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Letter Spacing', '%s stands for HTML heading tag.', 'fl-automator' ), 'H3' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => -3,
							'max'  => 10,
							'step' => 1,
						),
						'responsive' => true,
					),
				),

				/* Line */
				'fl-h3-line'             => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* H4 Font Size */
				'fl-h4-font-size'        => array(
					'setting' => array(
						'default'   => '18',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Font Size', '%s stands for HTML heading tag.', 'fl-automator' ), 'H4' ),
						'type'       => 'slider',
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				/* H4 Line Height */
				'fl-h4-line-height'      => array(
					'setting' => array(
						'default'   => '1.4',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Line Height', '%s stands for HTML heading tag.', 'fl-automator' ), 'H4' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),

				/* H4 Letter Spacing */
				'fl-h4-letter-spacing'   => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Letter Spacing', '%s stands for HTML heading tag.', 'fl-automator' ), 'H4' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => -3,
							'max'  => 10,
							'step' => 1,
						),
						'responsive' => true,
					),
				),

				/* Line */
				'fl-h4-line'             => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* H5 Font Size */
				'fl-h5-font-size'        => array(
					'setting' => array(
						'default'   => '14',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Font Size', '%s stands for HTML heading tag.', 'fl-automator' ), 'H5' ),
						'type'       => 'slider',
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				/* H5 Line Height */
				'fl-h5-line-height'      => array(
					'setting' => array(
						'default'   => '1.4',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Line Height', '%s stands for HTML heading tag.', 'fl-automator' ), 'H5' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),

				/* H5 Letter Spacing */
				'fl-h5-letter-spacing'   => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Letter Spacing', '%s stands for HTML heading tag.', 'fl-automator' ), 'H5' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => -3,
							'max'  => 10,
							'step' => 1,
						),
						'responsive' => true,
					),
				),

				/* Line */
				'fl-h5-line'             => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* H6 Font Size */
				'fl-h6-font-size'        => array(
					'setting' => array(
						'default'   => '12',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Font Size', '%s stands for HTML heading tag.', 'fl-automator' ), 'H6' ),
						'type'       => 'slider',
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				/* H6 Line Height */
				'fl-h6-line-height'      => array(
					'setting' => array(
						'default'   => '1.4',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Line Height', '%s stands for HTML heading tag.', 'fl-automator' ), 'H6' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),

				/* H6 Letter Spacing */
				'fl-h6-letter-spacing'   => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						/* translators: %s: HTML heading tag */
						'label'      => sprintf( _x( '%s Letter Spacing', '%s stands for HTML heading tag.', 'fl-automator' ), 'H6' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => -3,
							'max'  => 10,
							'step' => 1,
						),
						'responsive' => true,
					),
				),
			),
		),

		/* Body Font Section */
		'fl-body-font'    => array(
			'title'   => _x( 'Text', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Body Text Color */
				'fl-body-text-color'  => array(
					'setting' => array(
						'default' => '#808080',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Color', 'fl-automator' ),
					),
				),

				/* Body Font Family */
				'fl-body-font-family' => array(
					'setting' => array(
						'default' => 'Helvetica',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Family', 'fl-automator' ),
						'type'    => 'font',
						'connect' => 'fl-body-font-weight',
					),
				),

				/* Body Font Weight */
				'fl-body-font-weight' => array(
					'setting' => array(
						'default' => '400',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Weight', 'fl-automator' ),
						'type'    => 'font-weight',
						'connect' => 'fl-body-font-family',
					),
				),

				/* Body Font Size */
				'fl-body-font-size'   => array(
					'setting' => array(
						'default'   => '14',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						'label'      => __( 'Font Size', 'fl-automator' ),
						'type'       => 'slider',
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				/* Body Line Height */
				'fl-body-line-height' => array(
					'setting' => array(
						'default'   => '1.45',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						'label'      => __( 'Line Height', 'fl-automator' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),
			),
		),
		/* Buttons Section */
		'fl-buttons'      => array(
			'title'   => _x( 'Buttons', 'Customizer section title.', 'fl-automator' ),
			'options' => array(
				'fl-button-style'                  => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Button Style', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							''       => __( 'None (default)', 'fl-automator' ),
							'custom' => __( 'Custom', 'fl-automator' ),
						),
					),
				),

				/* Line */
				'fl-button-color-line'             => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),


				'fl-button-color'                  => array(
					'setting' => array(
						'default'   => '#808080',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Color', 'fl-automator' ),
					),
				),
				'fl-button-hover-color'            => array(
					'setting' => array(
						'default'   => '#555',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Hover Color', 'fl-automator' ),
					),
				),
				'fl-button-background-color'       => array(
					'setting' => array(
						'default'   => '#fff',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),
				'fl-button-background-hover-color' => array(
					'setting' => array(
						'default'   => '#eee',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Hover Color', 'fl-automator' ),
					),
				),

				/* Line */
				'fl-button-font-line'              => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Body Font Family */
				'fl-button-font-family'            => array(
					'setting' => array(
						'default' => 'Helvetica',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Family', 'fl-automator' ),
						'type'    => 'font',
						'connect' => 'fl-button-font-weight',
					),
				),
				/* Body Font Weight */
				'fl-button-font-weight'            => array(
					'setting' => array(
						'default' => '400',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Font Weight', 'fl-automator' ),
						'type'    => 'font-weight',
						'connect' => 'fl-button-font-family',
					),
				),

				'fl-button-font-size'              => array(
					'setting' => array(
						'default'   => '16',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						'label'      => _x( 'Font Size', 'Font size for buttons.', 'fl-automator' ),
						'type'       => 'slider',
						'choices'    => get_font_size_limits(),
						'responsive' => true,
					),
				),

				'fl-button-line-height'            => array(
					'setting' => array(
						'default'   => '1.2',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'      => 'FLCustomizerControl',
						'label'      => __( 'Line Height', 'fl-automator' ),
						'type'       => 'slider',
						'choices'    => array(
							'min'  => 0.5,
							'max'  => 2.5,
							'step' => 0.05,
						),
						'responsive' => true,
					),
				),
				'fl-button-text-transform'         => array(
					'setting' => array(
						'default'   => 'none',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => _x( 'Text Transform', 'Text transform for buttons.', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'none'       => __( 'Regular', 'fl-automator' ),
							'capitalize' => __( 'Capitalize', 'fl-automator' ),
							'uppercase'  => __( 'Uppercase', 'fl-automator' ),
							'lowercase'  => __( 'Lowercase', 'fl-automator' ),
						),
					),
				),

				/* Line */
				'fl-button-border-line'            => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),
				'fl-button-border-style'           => array(
					'setting' => array(
						'default'   => 'none',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => _x( 'Border Style', 'Border style for buttons.', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'none'    => __( 'None', 'fl-automator' ),
							'solid'   => __( 'Solid', 'fl-automator' ),
							'dotted'  => __( 'Dotted', 'fl-automator' ),
							'dashed'  => __( 'Dashed', 'fl-automator' ),
							'double'  => __( 'Double', 'fl-automator' ),
							'groove'  => __( 'Groove', 'fl-automator' ),
							'ridge'   => __( 'Ridge', 'fl-automator' ),
							'inset'   => __( 'Inset', 'fl-automator' ),
							'outset'  => __( 'Outset', 'fl-automator' ),
							'initial' => __( 'Initial', 'fl-automator' ),
							'inherit' => __( 'Inherit', 'fl-automator' ),
						),
					),
				),
				'fl-button-border-width'           => array(
					'setting' => array(
						'default'   => '0',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => _x( 'Border Width', 'Border width for buttons.', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 10,
							'step' => 1,
						),
					),
				),
				'fl-button-border-radius'          => array(
					'setting' => array(
						'default'   => 0,
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => _x( 'Border Radius', 'Font size for buttons.', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
				),
				'fl-button-border-color'           => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Border Color', 'fl-automator' ),
					),
				),
				'fl-button-border-hover-color'     => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Border Hover Color', 'fl-automator' ),
					),
				),
			),
		),
		/* Social Links Section */
		'fl-social-links' => array(
			'title'   => _x( 'Social Links', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Social Icons Color */
				'fl-social-icons-color'       => array(
					'setting' => array(
						'default' => 'mono',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Social Icons Color', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'branded' => __( 'Branded', 'fl-automator' ),
							'mono'    => __( 'Monochrome', 'fl-automator' ),
							'custom'  => __( 'Custom', 'fl-automator' ),
						),
					),
				),

				/* Social Icons bg Shape */
				'fl-social-icons-bg-shape'    => array(
					'setting' => array(
						'default' => 'circle',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Shape', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'circle' => __( 'Round', 'fl-automator' ),
							'square' => __( 'Square', 'fl-automator' ),
						),
					),
				),

				/* Social Icons bg Color */
				'fl-social-icons-bg-color'    => array(
					'setting' => array(
						'default' => '#000',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Social Icons fg Color */
				'fl-social-icons-fg-color'    => array(
					'setting' => array(
						'default' => '#FFF',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Icon Color', 'fl-automator' ),
					),
				),

				/* Social Icons hover Color */
				'fl-social-icons-hover-color' => array(
					'setting' => array(
						'default' => '#666',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Hover Color', 'fl-automator' ),
					),
				),

				/* Drop Shadow Size */
				'fl-social-icons-size'        => array(
					'setting' => array(
						'default' => '2',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Icon Size', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 1,
							'max'  => 8,
							'step' => 1,
						),
					),
				),

				'fl-social-link-new-tab'      => array(
					'setting' => array(
						'default' => false,
					),
					'control' => array(
						'class' => 'FLCustomizerControl',
						'label' => __( 'Open Link In New Tab', 'fl-automator' ),
						'type'  => 'switch',
					),
				),

				/* Social Links (no need to translate brand names) */
				'fl-social-facebook'          => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Facebook',
					),
				),
				'fl-social-twitter'           => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Twitter',
					),
				),
				'fl-social-google'            => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Google',
					),
				),

				'fl-social-google-maps'       => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Google Maps',
					),
				),

				'fl-social-snapchat'          => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Snapchat',
					),
				),
				'fl-social-linkedin'          => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'LinkedIn',
					),
				),
				'fl-social-yelp'              => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Yelp',
					),
				),
				'fl-social-xing'              => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Xing',
					),
				),
				'fl-social-pinterest'         => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Pinterest',
					),
				),
				'fl-social-tumblr'            => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Tumblr',
					),
				),
				'fl-social-vimeo'             => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Vimeo',
					),
				),
				'fl-social-youtube'           => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'YouTube',
					),
				),
				'fl-social-flickr'            => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Flickr',
					),
				),
				'fl-social-instagram'         => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Instagram',
					),
				),
				'fl-social-skype'             => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Skype',
					),
				),
				'fl-social-dribbble'          => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Dribbble',
					),
				),
				'fl-social-500px'             => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => '500px',
					),
				),
				'fl-social-blogger'           => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Blogger',
					),
				),
				'fl-social-github'            => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => 'GitHub',
					),
				),
				'fl-social-rss'               => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'RSS', 'fl-automator' ),
					),
				),
				'fl-social-email'             => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'Email', 'fl-automator' ),
					),
				),
				'fl-social-wordpress'         => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'WordPress', 'fl-automator' ),
					),
				),
				'fl-social-tiktok'            => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'Tik Tok', 'fl-automator' ),
					),
				),
				'fl-social-spotify'           => array(
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'Spotify', 'fl-automator' ),
					),
				),
			),
		),
	),
));
