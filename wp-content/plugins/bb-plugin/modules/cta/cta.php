<?php

/**
 * @class FLCtaModule
 */
class FLCtaModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Call to Action', 'fl-builder' ),
			'description'     => __( 'Display a heading, subheading and a button.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'megaphone.svg',
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

		// Old spacing setting.
		if ( isset( $settings->spacing ) ) {
			$settings->wrap_padding_top    = $settings->spacing;
			$settings->wrap_padding_right  = $settings->spacing;
			$settings->wrap_padding_bottom = $settings->spacing;
			$settings->wrap_padding_left   = $settings->spacing;
			$settings->wrap_padding_unit   = 'px';
			unset( $settings->spacing );
		}

		// Old bg opacity setting.
		$helper->handle_opacity_inputs( $settings, 'bg_opacity', 'bg_color' );

		// Handle old title size settings. Add title color if we have a text color.
		if ( isset( $settings->title_size ) ) {
			if ( 'custom' === $settings->title_size ) {
				if ( ! isset( $settings->title_typography ) || ! is_array( $settings->title_typography ) ) {
					$settings->title_typography = array();
				}
				$settings->title_typography['font_size'] = array(
					'length' => $settings->title_custom_size,
					'unit'   => 'px',
				);
			}
			if ( ! empty( $settings->text_color ) ) {
				$settings->title_color = $settings->text_color;
			}
			unset( $settings->title_size );
			unset( $settings->title_custom_size );
		}

		// Handle old button module settings.
		$helper->filter_child_module_settings( 'button', $settings, array(
			'btn_3d'                 => 'three_d',
			'btn_style'              => 'style',
			'btn_padding'            => 'padding',
			'btn_padding_top'        => 'padding_top',
			'btn_padding_bottom'     => 'padding_bottom',
			'btn_padding_left'       => 'padding_left',
			'btn_padding_right'      => 'padding_right',
			'btn_font_size'          => 'font_size',
			'btn_font_size_unit'     => 'font_size_unit',
			'btn_typography'         => 'typography',
			'btn_bg_color'           => 'bg_color',
			'btn_bg_hover_color'     => 'bg_hover_color',
			'btn_bg_opacity'         => 'bg_opacity',
			'btn_bg_hover_opacity'   => 'bg_hover_opacity',
			'btn_border'             => 'border',
			'btn_border_hover_color' => 'border_hover_color',
			'btn_border_radius'      => 'border_radius',
			'btn_border_size'        => 'border_size',
		) );

		return $settings;
	}

	/**
	 * @method get_classname
	 */
	public function get_classname() {
		$classname = 'fl-cta-wrap fl-cta-' . $this->settings->layout;

		if ( 'stacked' == $this->settings->layout ) {
			$classname .= ' fl-cta-' . $this->settings->alignment;
		}

		return $classname;
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_button_settings() {
		$settings = array(
			'align' => '',
			'width' => 'stacked' == $this->settings->layout ? 'auto' : 'full',
		);
		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'btn_' ) ) {
				$key              = str_replace( 'btn_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * @method render_button
	 */
	public function render_button() {
		FLBuilder::render_module_html( 'button', $this->get_button_settings() );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLCtaModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'title' => array(
				'title'  => '',
				'fields' => array(
					'title'     => array(
						'type'        => 'text',
						'label'       => __( 'Heading', 'fl-builder' ),
						'default'     => __( 'Ready to find out more?', 'fl-builder' ),
						'preview'     => array(
							'type'     => 'text',
							'selector' => '.fl-cta-title',
						),
						'connections' => array( 'string' ),
					),
					'title_tag' => array(
						'type'    => 'select',
						'label'   => __( 'Heading Tag', 'fl-builder' ),
						'default' => 'h3',
						'options' => array(
							'h1' => 'h1',
							'h2' => 'h2',
							'h3' => 'h3',
							'h4' => 'h4',
							'h5' => 'h5',
							'h6' => 'h6',
						),
					),
				),
			),
			'text'  => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'text' => array(
						'type'          => 'editor',
						'label'         => '',
						'media_buttons' => false,
						'wpautop'       => false,
						'default'       => __( 'Drop us a line today for a free quote!', 'fl-builder' ),
						'preview'       => array(
							'type'     => 'text',
							'selector' => '.fl-cta-text-content',
						),
						'connections'   => array( 'string' ),
					),
				),
			),
		),
	),
	'style'   => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'structure'       => array(
				'title'  => '',
				'fields' => array(
					'layout'       => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'inline',
						'options' => array(
							'inline'  => __( 'Inline', 'fl-builder' ),
							'stacked' => __( 'Stacked', 'fl-builder' ),
						),
						'toggle'  => array(
							'stacked' => array(
								'fields' => array( 'alignment' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'bg_color'     => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-module-content',
							'property' => 'background-color',
						),
					),
					'border'       => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-module-content',
						),
					),
					'alignment'    => array(
						'type'    => 'align',
						'label'   => __( 'Alignment', 'fl-builder' ),
						'default' => 'center',
						'preview' => array(
							'type'      => 'css',
							'selector'  => '.fl-cta-wrap',
							'property'  => 'text-align',
							'important' => true,
						),
					),
					'wrap_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'default'    => '0',
						'responsive' => true,
						'slider'     => true,
						'units'      => array(
							'px',
							'em',
							'%',
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-module-content',
							'property' => 'padding',
						),
					),
				),
			),
			'title_structure' => array(
				'title'  => __( 'Heading', 'fl-builder' ),
				'fields' => array(
					'title_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Heading Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-cta-title',
							'property'  => 'color',
							'important' => true,
						),
					),
					'title_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Heading Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-cta-title',
							'important' => true,
						),
					),
				),
			),
			'content'         => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'text_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-cta-text-content, .fl-cta-text-content *',
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
							'selector' => '.fl-cta-text-content',
						),
					),
				),
			),
		),
	),
	'button'  => array(
		'title'    => __( 'Button', 'fl-builder' ),
		'sections' => array(
			'btn_text'        => array(
				'title'  => '',
				'fields' => array(
					'btn_text' => array(
						'type'        => 'text',
						'label'       => __( 'Button Text', 'fl-builder' ),
						'default'     => __( 'Click Here', 'fl-builder' ),
						'preview'     => array(
							'type'     => 'text',
							'selector' => '.fl-button-text',
						),
						'connections' => array( 'string' ),
					),
					'btn_link' => array(
						'type'          => 'link',
						'label'         => __( 'Button Link', 'fl-builder' ),
						'show_target'   => true,
						'show_nofollow' => true,
						'preview'       => array(
							'type' => 'none',
						),
						'connections'   => array( 'url' ),
					),
				),
			),
			'btn_icon'        => array(
				'title'  => __( 'Button Icon', 'fl-builder' ),
				'fields' => array(
					'btn_icon'           => array(
						'type'        => 'icon',
						'label'       => __( 'Button Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields' => array( 'btn_icon_position', 'btn_icon_animation' ),
						),
					),
					'btn_duo_color1'     => array(
						'label'      => __( 'DuoTone Primary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-button-icon.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_duo_color2'     => array(
						'label'      => __( 'DuoTone Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-button-icon.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_icon_position'  => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Position', 'fl-builder' ),
						'default' => 'before',
						'options' => array(
							'before' => __( 'Before Text', 'fl-builder' ),
							'after'  => __( 'After Text', 'fl-builder' ),
						),
					),
					'btn_icon_animation' => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Visibility', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Always Visible', 'fl-builder' ),
							'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
						),
					),
				),
			),
			'btn_style'       => array(
				'title'  => __( 'Button Style', 'fl-builder' ),
				'fields' => array(
					'btn_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Button Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button',
							'property' => 'padding',
						),
					),
				),
			),
			'btn_text_colors' => array(
				'title'  => __( 'Button Text', 'fl-builder' ),
				'fields' => array(
					'btn_text_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Text Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button, a.fl-button *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_text_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Text Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button:hover, a.fl-button:hover *, a.fl-button:focus, a.fl-button:focus *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Button Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button',
						),
					),
				),
			),
			'btn_colors'      => array(
				'title'  => __( 'Button Background', 'fl-builder' ),
				'fields' => array(
					'btn_bg_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'btn_bg_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'btn_style'             => array(
						'type'    => 'select',
						'label'   => __( 'Button Background Style', 'fl-builder' ),
						'default' => 'flat',
						'options' => array(
							'flat'     => __( 'Flat', 'fl-builder' ),
							'gradient' => __( 'Gradient', 'fl-builder' ),
						),
					),
					'btn_button_transition' => array(
						'type'    => 'select',
						'label'   => __( 'Button Background Animation', 'fl-builder' ),
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
			'btn_border'      => array(
				'title'  => __( 'Button Border', 'fl-builder' ),
				'fields' => array(
					'btn_border'             => array(
						'type'       => 'border',
						'label'      => __( 'Button Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button',
							'important' => true,
						),
					),
					'btn_border_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Border Hover Color', 'fl-builder' ),
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
