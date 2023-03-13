<?php

/**
 * @class FLCalloutModule
 */
class FLCalloutModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Callout', 'fl-builder' ),
			'description'     => __( 'A heading and snippet of text with an optional link, icon and image.', 'fl-builder' ),
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

		// Make sure we have a typography array.
		if ( ! isset( $settings->typography ) || ! is_array( $settings->typography ) ) {
			$settings->typography = array();
		}

		// Handle old title size settings.
		if ( isset( $settings->title_size ) && 'custom' === $settings->title_size ) {
			$settings->typography['font_size'] = array(
				'length' => $settings->title_custom_size,
				'unit'   => 'px',
			);
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
			'btn_mobile_align'       => 'mobile_align',
			'btn_align_responsive'   => 'align_responsive',
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

		// Return the filtered settings.
		return $settings;
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		// Cache the photo data.
		$settings->photo_data = FLBuilderPhoto::get_attachment_data( $settings->photo );

		return $settings;
	}

	/**
	 * @method delete
	 */
	public function delete() {
		// Delete photo module cache.
		if ( 'photo' == $this->settings->image_type && ! empty( $this->settings->photo_src ) ) {
			$module_class                         = get_class( FLBuilderModel::$modules['photo'] );
			$photo_module                         = new $module_class();
			$photo_module->settings               = new stdClass();
			$photo_module->settings->photo_source = 'library';
			$photo_module->settings->photo_src    = $this->settings->photo_src;
			$photo_module->settings->crop         = $this->settings->photo_crop;
			$photo_module->delete();
		}
	}

	/**
	 * @method get_classname
	 */
	public function get_classname() {
		$classname = 'fl-callout';

		if ( 'photo' == $this->settings->image_type ) {
			$classname .= ' fl-callout-has-photo fl-callout-photo-' . $this->settings->photo_position;
		} elseif ( 'icon' == $this->settings->image_type ) {
			$classname .= ' fl-callout-has-icon fl-callout-icon-' . $this->settings->icon_position;
		}

		return $classname;
	}

	/**
	 * @method render_title
	 */
	public function render_title() {
		echo '<' . $this->settings->title_tag . ' class="fl-callout-title">';

		if ( ! empty( $this->settings->link ) && 'icon' === $this->settings->image_type ) {
			echo '<a href="' . $this->settings->link . '" target="' . $this->settings->link_target . '" ' . $this->get_rel() . ' class="fl-callout-title-link fl-callout-title-text">';
		}

		if ( ! empty( $this->settings->link ) && 'icon' !== $this->settings->image_type ) {
			echo '<a href="' . $this->settings->link . '" target="' . $this->settings->link_target . '" ' . $this->get_rel() . ' class="fl-callout-title-link fl-callout-title-text">';
		}

		if ( 'left-title' === $this->settings->icon_position ) {
			$this->render_image( 'left-title' );
		}

		echo '<span' . ( empty( $this->settings->link ) ? ' class="fl-callout-title-text"' : '' ) . '>';
		echo $this->settings->title;
		echo '</span>';

		if ( 'right-title' === $this->settings->icon_position ) {
			$this->render_image( 'right-title' );
		}

		if ( ! empty( $this->settings->link ) ) {
			echo '</a>';
		}

		echo '</' . $this->settings->title_tag . '>';
	}

	/**
	 * @method render_text
	 */
	public function render_text() {
		global $wp_embed;

		echo '<div class="fl-callout-text">' . wpautop( $wp_embed->autoembed( $this->settings->text ) ) . '</div>';
	}

	/**
	 * Returns link rel based on settings.
	 * @since 2.5
	 * @return string
	 */
	public function get_rel() {
		$rel = array();
		if ( '_blank' == $this->settings->link_target ) {
			$rel[] = 'noopener';
		}
		if ( isset( $this->settings->link_nofollow ) && 'yes' == $this->settings->link_nofollow ) {
			$rel[] = 'nofollow';
		}
		$rel = implode( ' ', $rel );
		if ( $rel ) {
			$rel = ' rel="' . $rel . '" ';
		}
		return $rel;
	}

	/**
	 * @method render_link
	 */
	public function render_link() {
		if ( 'link' == $this->settings->cta_type ) {
			echo '<a href="' . $this->settings->link . '" ' . $this->get_rel() . ' target="' . $this->settings->link_target . '" class="fl-callout-cta-link">' . $this->settings->cta_text . '</a>';
		}
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_button_settings() {
		$settings = array(
			'link'          => $this->settings->link,
			'link_target'   => $this->settings->link_target,
			'link_nofollow' => $this->settings->link_nofollow,
			'text'          => $this->settings->cta_text,
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
		if ( 'button' == $this->settings->cta_type ) {
			echo '<div class="fl-callout-button">';
			FLBuilder::render_module_html( 'button', $this->get_button_settings() );
			echo '</div>';
		}
	}

	/**
	 * Returns an array of settings used to render an icon module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_icon_settings() {
		$settings = array(
			'id'              => $this->node,
			'align'           => '',
			'exclude_wrapper' => true,
			'icon'            => $this->settings->icon,
			'text'            => '',
			'three_d'         => $this->settings->icon_3d,
			'sr_text'         => $this->settings->sr_text,
			'link'            => $this->settings->link,
			'link_target'     => $this->settings->link_target,
			'link_nofollow'   => $this->settings->link_nofollow,
		);

		if ( isset( $this->settings->icon_position ) && ( 'left-title' === $this->settings->icon_position || 'right-title' === $this->settings->icon_position ) ) {
			unset( $settings['link'] );
		}

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'icon_' ) ) {
				$key              = str_replace( 'icon_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Returns an array of settings used to render a photo module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_photo_settings() {
		$photo_data = FLBuilderPhoto::get_attachment_data( $this->settings->photo );

		if ( ! $photo_data && isset( $this->settings->photo_data ) ) {
			$photo_data = $this->settings->photo_data;
		} elseif ( ! $photo_data ) {
			$photo_data = -1;
		}

		$settings = array(
			'link_url_target' => $this->settings->link_target,
			'link_nofollow'   => $this->settings->link_nofollow,
			'link_type'       => 'url',
			'link_url'        => $this->settings->link,
			'photo'           => $photo_data,
			'photo_src'       => $this->settings->photo_src,
			'photo_source'    => 'library',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'photo_' ) ) {
				$key              = str_replace( 'photo_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * @method render_image
	 */
	public function render_image( $position ) {
		if ( 'photo' == $this->settings->image_type && $this->settings->photo_position == $position ) {
			if ( empty( $this->settings->photo ) ) {
				return;
			}
			echo '<div class="fl-callout-photo">';
			FLBuilder::render_module_html( 'photo', $this->get_photo_settings() );
			echo '</div>';
		} elseif ( 'icon' == $this->settings->image_type && $this->settings->icon_position == $position ) {
			FLBuilder::render_module_html( 'icon', $this->get_icon_settings() );
		}
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLCalloutModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'title' => array(
				'title'  => '',
				'fields' => array(
					'title'     => array(
						'type'        => 'text',
						'label'       => __( 'Heading', 'fl-builder' ),
						'preview'     => array(
							'type'     => 'text',
							'selector' => '.fl-callout-title-text',
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
						'preview'       => array(
							'type'     => 'text',
							'selector' => '.fl-callout-text',
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
			'overall_structure' => array(
				'title'  => '',
				'fields' => array(
					'bg_color' => array(
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
					'border'   => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-module-content',
						),
					),
					'align'    => array(
						'type'       => 'align',
						'label'      => __( 'Alignment', 'fl-builder' ),
						'default'    => 'left',
						'help'       => __( 'The alignment that will apply to all elements within the callout.', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'padding'  => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'default'    => '',
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
			'title_structure'   => array(
				'title'  => __( 'Heading', 'fl-builder' ),
				'fields' => array(
					'title_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Heading Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-callout-title, .fl-callout-title-text, .fl-callout-title-text:hover',
							'property' => 'color',
						),
					),
					'title_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Heading Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-callout-title',
						),
					),
				),
			),
			'content'           => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'content_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-callout-text, .fl-callout-cta-link',
							'property' => 'color',
						),
					),
					'content_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Text Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-callout-text, .fl-callout-cta-link',
						),
					),
				),
			),
		),
	),
	'image'   => array(
		'title'    => __( 'Image', 'fl-builder' ),
		'sections' => array(
			'general'        => array(
				'title'  => '',
				'fields' => array(
					'image_type' => array(
						'type'    => 'select',
						'label'   => __( 'Image Type', 'fl-builder' ),
						'default' => 'photo',
						'options' => array(
							'none'  => _x( 'None', 'Image type.', 'fl-builder' ),
							'photo' => __( 'Photo', 'fl-builder' ),
							'icon'  => __( 'Icon', 'fl-builder' ),
						),
						'toggle'  => array(
							'none'  => array(),
							'photo' => array(
								'sections' => array( 'photo', 'photo_style' ),
							),
							'icon'  => array(
								'sections' => array( 'icon', 'icon_colors', 'icon_structure' ),
							),
						),
					),
				),
			),
			'photo'          => array(
				'title'  => __( 'Photo', 'fl-builder' ),
				'fields' => array(
					'photo'          => array(
						'type'        => 'photo',
						'show_remove' => true,
						'label'       => __( 'Photo', 'fl-builder' ),
						'connections' => array( 'photo' ),
					),
					'photo_position' => array(
						'type'    => 'select',
						'label'   => __( 'Position', 'fl-builder' ),
						'default' => 'above-title',
						'options' => array(
							'above-title' => __( 'Above Heading', 'fl-builder' ),
							'below-title' => __( 'Below Heading', 'fl-builder' ),
							'left'        => __( 'Left of Text and Heading', 'fl-builder' ),
							'right'       => __( 'Right of Text and Heading', 'fl-builder' ),
						),
					),
				),
			),
			'photo_style'    => array(
				'title'  => __( 'Photo Style', 'fl-builder' ),
				'fields' => array(
					'photo_crop'   => array(
						'type'    => 'select',
						'label'   => __( 'Crop', 'fl-builder' ),
						'default' => '',
						'options' => array(
							''          => _x( 'None', 'Photo Crop.', 'fl-builder' ),
							'landscape' => __( 'Landscape', 'fl-builder' ),
							'panorama'  => __( 'Panorama', 'fl-builder' ),
							'portrait'  => __( 'Portrait', 'fl-builder' ),
							'square'    => __( 'Square', 'fl-builder' ),
							'circle'    => __( 'Circle', 'fl-builder' ),
						),
					),
					'photo_width'  => array(
						'type'       => 'unit',
						'label'      => __( 'Width', 'fl-builder' ),
						'responsive' => true,
						'units'      => array(
							'px',
							'vw',
							'%',
						),
						'slider'     => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
						),
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-photo-img',
							'property'  => 'width',
							'important' => true,
						),
					),
					'photo_align'  => array(
						'type'       => 'align',
						'label'      => __( 'Align', 'fl-builder' ),
						'default'    => '',
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-photo',
							'property'  => 'text-align',
							'important' => true,
						),
					),
					'photo_border' => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-photo-img',
						),
					),
				),
			),
			'icon'           => array(
				'title'  => __( 'Icon', 'fl-builder' ),
				'fields' => array(
					'icon'          => array(
						'type'        => 'icon',
						'label'       => __( 'Icon', 'fl-builder' ),
						'show_remove' => true,
					),
					'sr_text'       => array(
						'type'    => 'text',
						'label'   => __( 'Screen Reader Text', 'fl-builder' ),
						'default' => '',
					),
					'icon_position' => array(
						'type'    => 'select',
						'label'   => __( 'Position', 'fl-builder' ),
						'default' => 'left-title',
						'options' => array(
							'above-title' => __( 'Above Heading', 'fl-builder' ),
							'below-title' => __( 'Below Heading', 'fl-builder' ),
							'left-title'  => __( 'Left of Heading', 'fl-builder' ),
							'right-title' => __( 'Right of Heading', 'fl-builder' ),
							'left'        => __( 'Left of Text and Heading', 'fl-builder' ),
							'right'       => __( 'Right of Text and Heading', 'fl-builder' ),
						),
					),
				),
			),
			'icon_colors'    => array(
				'title'  => __( 'Icon Colors', 'fl-builder' ),
				'fields' => array(
					'icon_duo_color1'     => array(
						'label'      => __( 'DuoTone Primary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-icon i.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'icon_duo_color2'     => array(
						'label'      => __( 'DuoTone Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-icon i.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
					'icon_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-icon i, .fl-icon i:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'icon_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'icon_bg_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'icon_bg_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'icon_3d'             => array(
						'type'    => 'select',
						'label'   => __( 'Gradient', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
				),
			),
			'icon_structure' => array(
				'title'  => __( 'Icon Structure', 'fl-builder' ),
				'fields' => array(
					'icon_size' => array(
						'type'       => 'unit',
						'label'      => __( 'Size', 'fl-builder' ),
						'default'    => '30',
						'responsive' => true,
						'units'      => array( 'px', 'em', 'rem' ),
						'slider'     => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
	'cta'     => array(
		'title'    => __( 'Link', 'fl-builder' ),
		'sections' => array(
			'link'       => array(
				'title'  => '',
				'fields' => array(
					'link' => array(
						'type'          => 'link',
						'label'         => __( 'Link', 'fl-builder' ),
						'help'          => __( 'The link applies to the entire module. If choosing a call to action type below, this link will also be used for the text or button.', 'fl-builder' ),
						'show_target'   => true,
						'show_nofollow' => true,
						'preview'       => array(
							'type' => 'none',
						),
						'connections'   => array( 'url' ),
					),
				),
			),
			'cta'        => array(
				'title'  => __( 'Call to Action', 'fl-builder' ),
				'fields' => array(
					'cta_type' => array(
						'type'    => 'select',
						'label'   => __( 'Type', 'fl-builder' ),
						'default' => 'none',
						'options' => array(
							'none'   => _x( 'None', 'Call to action.', 'fl-builder' ),
							'link'   => __( 'Link', 'fl-builder' ),
							'button' => __( 'Button', 'fl-builder' ),
						),
						'toggle'  => array(
							'none'   => array(),
							'link'   => array(
								'fields'   => array( 'cta_text' ),
								'sections' => array( 'link_text' ),
							),
							'button' => array(
								'fields'   => array( 'cta_text' ),
								'sections' => array( 'btn_icon', 'btn_style', 'btn_text', 'btn_colors', 'btn_border' ),
							),
						),
					),
					'cta_text' => array(
						'type'        => 'text',
						'label'       => __( 'Text', 'fl-builder' ),
						'default'     => __( 'Read More', 'fl-builder' ),
						'connections' => array( 'string' ),
						'preview'     => array(
							'type'     => 'text',
							'selector' => '.fl-callout-cta-link, .fl-button-text',
						),
					),
				),
			),
			'link_text'  => array(
				'title'  => __( 'Link Text', 'fl-builder' ),
				'fields' => array(
					'link_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-callout-cta-link',
							'property'  => 'color',
							'important' => true,
						),
					),
					'link_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-callout-cta-link:hover, a.fl-callout-cta-link:focus',
							'property'  => 'color',
							'important' => true,
						),
					),
					'link_typography'  => array(
						'type'       => 'typography',
						'label'      => __( 'Link Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-callout-cta-link',
						),
					),
				),
			),
			'btn_icon'   => array(
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
							'selector'  => '.fl-button i.fad:before',
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
							'selector'  => '.fl-button i.fad:after',
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
			'btn_style'  => array(
				'title'  => __( 'Button Style', 'fl-builder' ),
				'fields' => array(
					'btn_width'   => array(
						'type'    => 'select',
						'label'   => __( 'Button Width', 'fl-builder' ),
						'default' => 'auto',
						'options' => array(
							'auto' => _x( 'Auto', 'Width.', 'fl-builder' ),
							'full' => __( 'Full Width', 'fl-builder' ),
						),
						'toggle'  => array(
							'auto' => array(
								'fields' => array( 'btn_align' ),
							),
						),
					),
					'btn_align'   => array(
						'type'       => 'align',
						'label'      => __( 'Button Align', 'fl-builder' ),
						'default'    => '',
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-button-wrap',
							'property' => 'text-align',
						),
					),
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
			'btn_text'   => array(
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
			'btn_colors' => array(
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
			'btn_border' => array(
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
