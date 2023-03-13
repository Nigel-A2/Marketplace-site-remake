<?php

/* Settings Panel */
FLCustomizer::add_panel('fl-settings', array(
	'title'    => _x( 'Settings', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* Favicons Section */
		'fl-favicons' => array(
			'disable' => function_exists( 'has_site_icon' ),
			'title'   => _x( 'Favicons', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Favicon */
				'fl-favicon'          => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Favicon', 'fl-automator' ),
					),
				),

				/* Apple Touch Icon */
				'fl-apple-touch-icon' => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Apple Touch Icon', 'fl-automator' ),
					),
				),
			),
		),
	),
));
