<?php

FLBuilder::register_settings_form('layout', array(
	'title' => __( 'Layout CSS / Javascript', 'fl-builder' ),
	'tabs'  => array(
		'css' => array(
			'title'    => __( 'CSS', 'fl-builder' ),
			'sections' => array(
				'css' => array(
					'title'  => '',
					'fields' => array(
						'css' => array(
							'type'    => 'code',
							'label'   => '',
							'editor'  => 'css',
							'rows'    => '18',
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
		'js'  => array(
			'title'    => __( 'JavaScript', 'fl-builder' ),
			'sections' => array(
				'js' => array(
					'title'  => '',
					'fields' => array(
						'js' => array(
							'type'    => 'code',
							'label'   => '',
							'editor'  => 'javascript',
							'rows'    => '18',
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
	),
));
