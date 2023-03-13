<?php

FLBuilder::register_settings_form('node_template', array(
	'tabs' => array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'general' => array(
					'title'  => '',
					'fields' => array(
						'name'   => array(
							'type'  => 'text',
							'label' => _x( 'Name', 'Template name.', 'fl-builder' ),
						),
						'global' => array(
							'type'    => 'select',
							'label'   => _x( 'Global', 'Whether this is a global row, column or module.', 'fl-builder' ),
							'help'    => __( 'Global rows, columns and modules can be added to multiple pages and edited in one place.', 'fl-builder' ),
							'default' => '0',
							'options' => array(
								'0' => __( 'No', 'fl-builder' ),
								'1' => __( 'Yes', 'fl-builder' ),
							),
						),
					),
				),
			),
		),
	),
));
