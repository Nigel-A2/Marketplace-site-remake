<?php

/**
 * @class FLButtonGroupModule
 */
class FLButtonGroupModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Button Group', 'fl-builder' ),
			'description'     => __( 'Renders a series of call to action buttons.', 'fl-builder' ),
			'category'        => __( 'Basic', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'button.svg',
		));
	}

	/**
	 * This is used in generating the alignment of the horizontal buttons.
	 *
	 * @method map_horizontal_alignment
	 * @return string
	 */
	public function map_horizontal_alignment( $align ) {
		$map_alignment = array(
			'left'   => 'flex-start',
			'center' => 'center',
			'right'  => 'flex-end',
		);

		return $map_alignment[ $align ];
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		$this->add_js( 'jquery-magnificpopup' );
		$this->add_css( 'font-awesome-5' );
		$this->add_css( 'jquery-magnificpopup' );
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {

		// Convert 'space_between' Unit field to 'button_spacing' Dimension field.
		// @since 2.4.1
		if ( isset( $settings->space_between ) ) {

			// Left and Top Spacing
			foreach ( array( '_left', '_left_medium', '_left_responsive', '_top', '_top_medium', '_top_responsive' ) as $key ) {
				$settings->{ 'button_spacing' . $key } = 0;
			}

			// Right Spacing -- apply on horizontal layout.
			if ( 'horizontal' === $settings->layout ) {
				foreach ( array( '_right', '_right_medium', '_right_responsive' )  as $key ) {
					$settings->{ 'button_spacing' . $key } = intval( $settings->space_between );
				}
			}

			// Bottom Spacing
			foreach ( array( '_bottom', '_bottom_medium', '_bottom_responsive' )  as $key ) {
				$settings->{ 'button_spacing' . $key } = intval( $settings->space_between );
			}

			unset( $settings->space_between );
		}

		if ( isset( $settings->space_between_unit ) ) {
			foreach ( array( '_unit', '_medium_unit', '_responsive_unit' ) as $unit ) {
				$settings->{'button_spacing' . $unit } = $settings->space_between_unit;
			}
			unset( $settings->space_between_unit );
		}

		// Width changed from Full 'full' to Default ''.
		// @since 2.4.2
		if ( 'full' === $settings->width ) {
			$settings->width = '';
		}

		// Handle individual button settings.
		for ( $i = 0; $i < count( $settings->items ); $i++ ) {

			$button_item = $settings->items[ $i ];

			if ( ! is_object( $button_item ) ) {
				continue;
			}

			// Button Item Typography -- Rename 'typography' to 'button_item_typography'.
			if ( empty( $button_item->button_item_typography ) && ! empty( $button_item->typography ) ) {
				$settings->items[ $i ]->button_item_typography = $button_item->typography;
			}

			// Button Item Text Color -- Rename 'text_color' to 'button_item_text_color'.
			if ( empty( $button_item->button_item_text_color ) && ! empty( $button_item->text_color ) ) {
				$settings->items[ $i ]->button_item_text_color = $button_item->text_color;
			}

			// Button Item Background Color -- Rename 'bg_color' to 'button_item_bg_color'.
			if ( empty( $button_item->button_item_bg_color ) && ! empty( $button_item->bg_color ) ) {
				$settings->items[ $i ]->button_item_bg_color = $button_item->bg_color;
			}

			// Button Item Text Hover Color -- Rename 'text_hover_color' to 'button_item_text_hover_color'.
			if ( empty( $button_item->button_item_text_hover_color ) && ! empty( $button_item->text_hover_color ) ) {
				$settings->items[ $i ]->button_item_text_hover_color = $button_item->text_hover_color;
			}

			// Button Item Background Hover Color -- Rename 'bg_hover_color' to 'button_item_bg_hover_color'
			if ( empty( $button_item->button_item_bg_hover_color ) && ! empty( $button_item->bg_hover_color ) ) {
				$settings->items[ $i ]->button_item_bg_hover_color = $button_item->bg_hover_color;
			}

			// Button Item Border Hover Color -- Rename 'border_hover_color' to 'button_item_border_hover_color'
			if ( empty( $button_item->button_item_border_hover_color ) && ! empty( $button_item->border_hover_color ) ) {
				$settings->items[ $i ]->button_item_border_hover_color = $button_item->border_hover_color;
			}

			// Button Item Background Style -- Rename 'style' to 'button_item_style'.
			if ( empty( $button_item->button_item_style ) && ! empty( $button_item->style ) ) {
				$settings->items[ $i ]->button_item_style = $button_item->style;
			}

			// Button Item Border -- Rename 'border' to 'button_item_border'.
			if ( empty( $button_item->button_item_border ) && ! empty( $button_item->border ) ) {
				$settings->items[ $i ]->button_item_border = $button_item->border;
			}
		}

		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLButtonGroupModule', array(
	'buttons' => array(
		'title'    => __( 'Buttons', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'button_group_label' => array(
						'type'        => 'text',
						'label'       => __( 'Button Group Label', 'fl-builder' ),
						'placeholder' => __( 'Button Group Label', 'fl-builder' ),
						'connections' => array( 'string' ),
						'help'        => __( 'A unique identifier for the Button Group. This helps in accessibility.', 'fl-builder' ),
					),
					'items'              => array(
						'type'         => 'form',
						'label'        => __( 'Button', 'fl-builder' ),
						'form'         => 'buttons_form', // ID from registered form below
						'preview_text' => 'text', // Name of a field to use for the preview text
						'multiple'     => true,
					),
				),
			),
		),
	),
	'style'   => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'style'  => array(
				'title'  => '',
				'fields' => array(
					'layout'         => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'horizotal',
						'options' => array(
							'horizontal' => __( 'Horizontal', 'fl-builder' ),
							'vertical'   => __( 'Vertical', 'fl-builder' ),
						),
					),
					'width'          => array(
						'type'    => 'select',
						'label'   => __( 'Width', 'fl-builder' ),
						'default' => '',
						'options' => array(
							''       => __( 'Default', 'fl-builder' ),
							'custom' => __( 'Custom', 'fl-builder' ),
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'custom_width' ),
							),
						),
					),
					'custom_width'   => array(
						'type'    => 'unit',
						'label'   => __( 'Custom Width', 'fl-builder' ),
						'default' => '200',
						'slider'  => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
						),
						'units'   => array(
							'px',
							'vw',
							'%',
						),
					),
					'align'          => array(
						'type'       => 'align',
						'label'      => __( 'Align', 'fl-builder' ),
						'default'    => 'left',
						'responsive' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'padding'        => array(
						'type'       => 'dimension',
						'label'      => __( 'Container Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'help'       => __( 'This applies to the entire Button Group module itself.', 'fl-builder' ),
					),
					'button_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Button Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'help'       => __( 'Apply padding to all buttons. This can be overridden in the individual button settings.', 'fl-builder' ),
					),
					'button_spacing' => array(
						'type'         => 'dimension',
						'label'        => __( 'Button Spacing', 'fl-builder' ),
						'responsive'   => true,
						'slider'       => true,
						'default'      => '5',
						'default_unit' => 'px',
						'units'        => array(
							'px',
							'em',
							'%',
							'vw',
						),
					),
				),
			),
			'text'   => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'text_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'text_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
					),
				),
			),
			'colors' => array(
				'title'  => __( 'Background', 'fl-builder' ),
				'fields' => array(
					'bg_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'refresh',
						),
					),
					'bg_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-button-group-buttons a.fl-button:hover',
							'property' => 'background-color',
						),
					),
					'style'             => array(
						'type'    => 'select',
						'label'   => __( 'Background Style', 'fl-builder' ),
						'default' => 'flat',
						'options' => array(
							'flat'     => __( 'Flat', 'fl-builder' ),
							'gradient' => __( 'Gradient', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'refresh',
						),
					),
					'button_transition' => array(
						'type'    => 'select',
						'label'   => __( 'Background Animation', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Disabled', 'fl-builder' ),
							'enable'  => __( 'Enabled', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'border' => array(
				'title'  => __( 'Border', 'fl-builder' ),
				'fields' => array(
					'border'             => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-button-group .fl-button-group-buttons a.fl-button',
						),
					),
					'border_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Border Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
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
FLBuilder::register_settings_form('buttons_form', array(
	'title' => __( 'Add Button', 'fl-builder' ),
	'tabs'  => array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'general'  => array(
					'title'  => '',
					'fields' => array(
						'text'           => array(
							'type'        => 'text',
							'label'       => __( 'Text', 'fl-builder' ),
							'default'     => __( 'Click Here', 'fl-builder' ),
							'preview'     => array(
								'type'     => 'text',
								'selector' => '.fl-button-text',
							),
							'connections' => array( 'string' ),
						),
						'icon'           => array(
							'type'        => 'icon',
							'label'       => __( 'Icon', 'fl-builder' ),
							'show_remove' => true,
							'show'        => array(
								'fields' => array( 'icon_position', 'icon_animation' ),
							),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'icon_position'  => array(
							'type'    => 'select',
							'label'   => __( 'Icon Position', 'fl-builder' ),
							'default' => 'before',
							'options' => array(
								'before' => __( 'Before Text', 'fl-builder' ),
								'after'  => __( 'After Text', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'icon_animation' => array(
							'type'    => 'select',
							'label'   => __( 'Icon Visibility', 'fl-builder' ),
							'default' => 'disable',
							'options' => array(
								'disable' => __( 'Always Visible', 'fl-builder' ),
								'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'click_action'   => array(
							'type'    => 'select',
							'label'   => __( 'Click Action', 'fl-builder' ),
							'default' => 'link',
							'options' => array(
								'link'     => __( 'Link', 'fl-builder' ),
								'lightbox' => __( 'Lightbox', 'fl-builder' ),
							),
							'toggle'  => array(
								'link'     => array(
									'fields' => array( 'link' ),
								),
								'lightbox' => array(
									'sections' => array( 'lightbox' ),
								),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'link'           => array(
							'type'          => 'link',
							'label'         => __( 'Link', 'fl-builder' ),
							'placeholder'   => __( 'https://www.example.com', 'fl-builder' ),
							'show_target'   => true,
							'show_nofollow' => true,
							'show_target'   => true,
							'show_download' => true,
							'preview'       => array(
								'type' => 'none',
							),
							'connections'   => array( 'url' ),
						),
					),
				),
				'lightbox' => array(
					'title'  => __( 'Lightbox Content', 'fl-builder' ),
					'fields' => array(
						'lightbox_content_type' => array(
							'type'    => 'select',
							'label'   => __( 'Content Type', 'fl-builder' ),
							'default' => 'html',
							'options' => array(
								'html'  => __( 'HTML', 'fl-builder' ),
								'video' => __( 'Video', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
							'toggle'  => array(
								'html'  => array(
									'fields' => array( 'lightbox_content_html' ),
								),
								'video' => array(
									'fields' => array( 'lightbox_video_link' ),
								),
							),
						),
						'lightbox_content_html' => array(
							'type'        => 'code',
							'editor'      => 'html',
							'label'       => '',
							'rows'        => '19',
							'preview'     => array(
								'type' => 'none',
							),
							'connections' => array( 'string' ),
						),
						'lightbox_video_link'   => array(
							'type'        => 'text',
							'label'       => __( 'Video Link', 'fl-builder' ),
							'placeholder' => 'https://vimeo.com/122546221',
							'preview'     => array(
								'type' => 'none',
							),
							'connections' => array( 'custom_field' ),
						),
					),
				),
			),
		),
		'style'   => array(
			'title'    => __( 'Style', 'fl-builder' ),
			'sections' => array(
				'style'              => array(
					'title'  => '',
					'fields' => array(
						'padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'fl-builder' ),
							'responsive' => true,
							'slider'     => true,
							'units'      => array( 'px' ),
						),
					),
				),
				'text'               => array(
					'title'  => __( 'Text', 'fl-builder' ),
					'fields' => array(
						'button_item_text_color'       => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Text Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
						),
						'button_item_text_hover_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Text Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
						),
						'button_item_typography'       => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'fl-builder' ),
							'responsive' => true,
						),
					),
				),
				'colors'             => array(
					'title'  => __( 'Background', 'fl-builder' ),
					'fields' => array(
						'button_item_bg_color'       => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Background Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_item_bg_hover_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Background Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_item_style'          => array(
							'type'    => 'select',
							'label'   => __( 'Background Style', 'fl-builder' ),
							'default' => 'flat',
							'options' => array(
								'flat'     => __( 'Flat', 'fl-builder' ),
								'gradient' => __( 'Gradient', 'fl-builder' ),
							),
						),
						'button_transition'          => array(
							'type'    => 'select',
							'label'   => __( 'Background Animation', 'fl-builder' ),
							'default' => 'disable',
							'options' => array(
								'disable' => __( 'Disabled', 'fl-builder' ),
								'enable'  => __( 'Enabled', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
				'button_item_border' => array(
					'title'  => __( 'Border', 'fl-builder' ),
					'fields' => array(
						'button_item_border'             => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
						'button_item_border_hover_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Border Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
	),
));
