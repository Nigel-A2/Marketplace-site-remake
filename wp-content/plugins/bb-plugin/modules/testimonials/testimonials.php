<?php

/**
 * @class FLTestimonialsModule
 */
class FLTestimonialsModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Testimonials', 'fl-builder' ),
			'description'     => __( 'An animated testimonials area.', 'fl-builder' ),
			'category'        => __( 'Media', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'format-quote.svg',
		));

	}


	/**
 * @method enqueue_scripts
 */
	public function enqueue_scripts() {
		if ( $this->settings && 'compact' == $this->settings->layout && $this->settings->arrows ) {
			$this->add_css( 'font-awesome-5' );
		}
		$this->add_css( 'jquery-bxslider' );
		$this->add_js( 'jquery-bxslider' );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLTestimonialsModule', array(
	'testimonials' => array( // Tab
		'title'    => __( 'Items', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'testimonials' => array(
						'type'         => 'form',
						'label'        => __( 'Testimonial', 'fl-builder' ),
						'form'         => 'testimonials_form', // ID from registered form below
						'preview_text' => 'testimonial', // Name of a field to use for the preview text
						'multiple'     => true,
					),
				),
			),
		),
	),
	'general'      => array( // Tab
		'title'    => __( 'Slider', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'slider' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'auto_play'  => array(
						'type'    => 'select',
						'label'   => __( 'Auto Play', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'pause'      => array(
						'type'     => 'unit',
						'label'    => __( 'Delay', 'fl-builder' ),
						'default'  => '4',
						'sanitize' => 'FLBuilderUtils::sanitize_non_negative_number',
						'units'    => array( 'seconds' ),
						'slider'   => array(
							'max'  => 10,
							'step' => .5,
						),
					),
					'transition' => array(
						'type'    => 'select',
						'label'   => __( 'Transition', 'fl-builder' ),
						'default' => 'slide',
						'options' => array(
							'horizontal' => _x( 'Slide', 'Transition type.', 'fl-builder' ),
							'fade'       => __( 'Fade', 'fl-builder' ),
						),
					),
					'speed'      => array(
						'type'     => 'unit',
						'label'    => __( 'Transition Speed', 'fl-builder' ),
						'default'  => '0.5',
						'sanitize' => 'FLBuilderUtils::sanitize_non_negative_number',
						'units'    => array( 'seconds' ),
						'slider'   => array(
							'max'  => 10,
							'step' => .5,
						),
					),
					'direction'  => array(
						'type'    => 'select',
						'label'   => __( 'Transition Direction', 'fl-builder' ),
						'default' => 'next',
						'options' => array(
							'next' => __( 'Right To Left', 'fl-builder' ),
							'prev' => __( 'Left To Right', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
	'style'        => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general'    => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'layout' => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'wide',
						'options' => array(
							'wide'    => __( 'Wide', 'fl-builder' ),
							'compact' => __( 'Compact', 'fl-builder' ),
						),
						'toggle'  => array(
							'compact' => array(
								'sections' => array( 'heading', 'arrow_nav' ),
							),
							'wide'    => array(
								'sections' => array( 'dot_nav' ),
							),
						),
						'help'    => __( 'Wide is for 1 column rows, compact is for multi-column rows.', 'fl-builder' ),
					),
				),
			),
			'heading'    => array( // Section
				'title'  => __( 'Heading', 'fl-builder' ), // Section Title
				'fields' => array( // Section Fields
					'heading'      => array(
						'type'    => 'text',
						'default' => __( 'Testimonials', 'fl-builder' ),
						'label'   => __( 'Heading', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-testimonials-heading',
						),
					),
					'heading_size' => array(
						'type'    => 'unit',
						'label'   => __( 'Heading Size', 'fl-builder' ),
						'default' => '24',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'property' => 'font-size',
							'selector' => '.fl-testimonials-wrap.compact h3',
							'unit'     => 'px',
						),
					),
				),
			),
			'text_style' => array( // Section
				'title'  => __( 'Text', 'fl-builder' ), // Section Title
				'fields' => array( // Section Fields
					'text_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-testimonials-wrap .fl-testimonials .fl-testimonial, {node} .fl-testimonials-wrap .fl-testimonials .fl-testimonial *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'text_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Text Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-testimonial',
						),
					),
				),
			),
			'arrow_nav'  => array( // Section
				'title'  => __( 'Arrows', 'fl-builder' ),
				'fields' => array( // Section Fields
					'arrows'      => array(
						'type'    => 'select',
						'label'   => __( 'Show Arrows', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'arrow_color' ),
							),
						),
					),
					'arrow_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Arrow Color', 'fl-builder' ),
						'default'     => '999999',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-testimonials-wrap .fas',
							'property' => 'color',
						),
					),
				),
			),
			'dot_nav'    => array( // Section
				'title'  => __( 'Dots', 'fl-builder' ), // Section Title
				'fields' => array( // Section Fields
					'dots'      => array(
						'type'    => 'select',
						'label'   => __( 'Show Dots', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'dot_color' ),
							),
						),
					),
					'dot_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Dot Color', 'fl-builder' ),
						'default'     => '999999',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-testimonials-wrap .bx-pager.bx-default-pager a, .fl-testimonials-wrap .bx-pager.bx-default-pager a.active',
							'property' => 'background',
						),
					),
				),
			),
		),
	),
));


/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form('testimonials_form', array(
	'title' => __( 'Add Testimonial', 'fl-builder' ),
	'tabs'  => array(
		'general' => array( // Tab
			'title'    => __( 'General', 'fl-builder' ), // Tab title
			'sections' => array( // Tab Sections
				'general' => array( // Section
					'title'  => '', // Section Title
					'fields' => array( // Section Fields
						'testimonial' => array(
							'type'  => 'editor',
							'label' => '',
						),
					),
				),
			),
		),
	),
));
