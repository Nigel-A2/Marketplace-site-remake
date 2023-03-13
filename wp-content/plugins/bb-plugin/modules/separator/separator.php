<?php

/**
 * @class FLSeparatorModule
 */
class FLSeparatorModule extends FLBuilderModule {

	/**
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Separator', 'fl-builder' ),
			'description'     => __( 'A divider line to separate content.', 'fl-builder' ),
			'category'        => __( 'Basic', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'minus.svg',
		));
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

		// Opacity
		$helper->handle_opacity_inputs( $settings, 'opacity', 'color' );

		// Width
		if ( isset( $settings->custom_width ) ) {
			if ( 'full' === $settings->width ) {
				$settings->width = '100';
			} else {
				$settings->width = $settings->custom_width;
			}
			$settings->width_unit = '%';
			unset( $settings->custom_width );
		}

		// Alignment
		if ( 'center' == $settings->align ) {
			$settings->align = 'auto';
		} elseif ( 'left' == $settings->align ) {
			$settings->align = '0 0 0 0';
		} elseif ( 'right' == $settings->align ) {
			$settings->align = '0 0 0 auto';
		}

		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLSeparatorModule', array(
	'general' => array( // Tab
		'title'    => __( 'General', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'color'  => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'default'     => 'cccccc',
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-separator',
							'property' => 'border-top-color',
						),
					),
					'height' => array(
						'type'       => 'unit',
						'label'      => __( 'Height', 'fl-builder' ),
						'default'    => '1',
						'maxlength'  => '2',
						'size'       => '3',
						'sanitize'   => 'absint',
						'slider'     => true,
						'units'      => array(
							'px',
						),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-separator',
							'property' => 'border-top-width',
						),
					),
					'width'  => array(
						'type'       => 'unit',
						'label'      => __( 'Width', 'fl-builder' ),
						'default'    => '100',
						'maxlength'  => '3',
						'size'       => '4',
						'units'      => array(
							'%',
							'px',
							'vw',
						),
						'slider'     => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
						),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-separator',
							'property' => 'max-width',
						),
					),
					'align'  => array(
						'type'       => 'align',
						'label'      => __( 'Align', 'fl-builder' ),
						'default'    => 'center',
						'values'     => array(
							'left'   => '0 0 0 0',
							'center' => 'auto',
							'right'  => '0 0 0 auto',
						),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-separator',
							'property' => 'margin',
						),
					),
					'style'  => array(
						'type'    => 'select',
						'label'   => __( 'Style', 'fl-builder' ),
						'default' => 'solid',
						'options' => array(
							'solid'  => _x( 'Solid', 'Border type.', 'fl-builder' ),
							'dashed' => _x( 'Dashed', 'Border type.', 'fl-builder' ),
							'dotted' => _x( 'Dotted', 'Border type.', 'fl-builder' ),
							'double' => _x( 'Double', 'Border type.', 'fl-builder' ),
						),
						'preview' => array(
							'type'     => 'css',
							'selector' => '.fl-separator',
							'property' => 'border-top-style',
						),
						'help'    => __( 'The type of border to use. Double borders must have a height of at least 3px to render properly.', 'fl-builder' ),
					),
				),
			),
		),
	),
));
