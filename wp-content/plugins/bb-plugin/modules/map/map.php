<?php

/**
 * @class FLMapModule
 */
class FLMapModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Map', 'fl-builder' ),
			'description'     => __( 'Display a Google map.', 'fl-builder' ),
			'category'        => __( 'Media', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'location.svg',
		));
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLMapModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'address'             => array(
						'type'        => 'textarea',
						'rows'        => '3',
						'label'       => __( 'Address', 'fl-builder' ),
						'connections' => array( 'custom_field' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'height'              => array(
						'type'       => 'unit',
						'label'      => __( 'Height', 'fl-builder' ),
						'default'    => '400',
						'sanitize'   => 'absint',
						'responsive' => true,
						'units'      => array( 'px', 'vh' ),
						'slider'     => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-map, .fl-map iframe',
							'property' => 'height',
						),
					),
					'map_title_attribute' => array(
						'type'        => 'text',
						'label'       => __( 'Map title attribute for accessibility', 'fl-builder' ),
						'default'     => '',
						'placeholder' => __( 'Map title here', 'fl-builder' ),
						'connections' => array( 'string' ),
					),
					'border'              => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-map iframe',
						),
					),
				),
			),
		),
	),
));
