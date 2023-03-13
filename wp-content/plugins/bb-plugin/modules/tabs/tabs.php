<?php

/**
 * @class FLTabsModule
 */
class FLTabsModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Tabs', 'fl-builder' ),
			'description'     => __( 'Display a collection of tabbed content.', 'fl-builder' ),
			'category'        => __( 'Layout', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'layout.svg',
		));

		$this->add_css( 'font-awesome-5' );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLTabsModule', array(
	'items' => array(
		'title'    => __( 'Items', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'items' => array(
						'type'         => 'form',
						'label'        => __( 'Item', 'fl-builder' ),
						'form'         => 'items_form', // ID from registered form below
						'preview_text' => 'label', // Name of a field to use for the preview text
						'multiple'     => true,
					),
				),
			),
		),
	),
	'style' => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'layout'       => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'horizontal',
						'options' => array(
							'horizontal' => __( 'Horizontal', 'fl-builder' ),
							'vertical'   => __( 'Vertical', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'bg_color'     => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-tabs-panels, .fl-tabs-label.fl-tab-active',
							'property' => 'background-color',
						),
					),
					'border_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Border Color', 'fl-builder' ),
						'default'     => 'e5e5e5',
						'show_alpha'  => true,
						'show_reset'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-tabs-label.fl-tab-active, .fl-tabs-panels',
							'property' => 'border-color',
						),
					),
					'border_width' => array(
						'type'    => 'unit',
						'label'   => __( 'Border Width', 'fl-builder' ),
						'default' => '',
						'slider'  => array(
							'max' => 20,
						),
						'units'   => array( 'px' ),
						'preview' => array(
							'type'     => 'css',
							'selector' => '.fl-tabs-labels .fl-tabs-label, .fl-tabs-panels',
							'property' => 'border-width',
							'unit'     => 'px',
						),
					),
				),
			),
			'label'   => array(
				'title'  => __( 'Label', 'fl-builder' ),
				'fields' => array(
					'label_text_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Inactive Label Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-tabs-label:not(.fl-tab-active), .fl-tabs-panel-label:not(.fl-tab-active)',
							'property' => 'color',
							'important' => true,
						),
					),
					'label_bg_color'        => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Inactive Label Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-tabs-label:not(.fl-tab-active), .fl-tabs-panel-label:not(.fl-tab-active)',
							'property' => 'background-color',
						),
					),
					'label_active_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Active Label Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-tabs-label.fl-tab-active, .fl-tabs-panel-label.fl-tab-active',
							'property' => 'color',
						),
					),
					'label_active_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Active Label Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-tabs-label.fl-tab-active, .fl-tabs-panel-label.fl-tab-active',
							'property' => 'background-color',
						),
					),
					'label_padding'         => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array(
							'px',
							'em',
							'%',
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-tabs-label',
							'property' => 'padding',
						),
					),
					'label_typography'      => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-tabs-label',
							'important' => true,
						),
					),
				),
			),
			'content' => array(
				'title'  => __( 'Content', 'fl-builder' ),
				'fields' => array(
					'content_text_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-tabs-panel .fl-tabs-panel-content, {node} .fl-tabs-panel .fl-tabs-panel-content *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'content_padding'    => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array(
							'px',
							'em',
							'%',
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-tabs-panel-content',
							'property' => 'padding',
						),
					),
					'content_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-tabs-panel-content',
							'important' => true,
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
FLBuilder::register_settings_form('items_form', array(
	'title' => __( 'Add Item', 'fl-builder' ),
	'tabs'  => array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'general' => array(
					'title'  => '',
					'fields' => array(
						'label' => array(
							'type'        => 'text',
							'label'       => __( 'Label', 'fl-builder' ),
							'connections' => array( 'string' ),
						),
					),
				),
				'content' => array(
					'title'  => __( 'Content', 'fl-builder' ),
					'fields' => array(
						'content' => array(
							'type'        => 'editor',
							'label'       => '',
							'wpautop'     => false,
							'connections' => array( 'string' ),
						),
					),
				),
			),
		),
	),
));
