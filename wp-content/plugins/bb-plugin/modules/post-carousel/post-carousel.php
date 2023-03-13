<?php

/**
 * @class FLPostCarouselModule
 */
class FLPostCarouselModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Posts Carousel', 'fl-builder' ),
			'description'     => __( 'Display a carousel of your WordPress posts.', 'fl-builder' ),
			'category'        => __( 'Posts', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'slides.svg',
		));

		$this->add_css( 'jquery-bxslider' );
		$this->add_js( 'jquery-bxslider' );
	}

	/**
	 * @method update
	 * @param $settings {object}
	 * @return object
	 */
	public function update( $settings ) {
		// remove old settings values
		if ( isset( $settings->text_color ) ) {
			unset( $settings->text_color );
		}

		if ( isset( $settings->link_color ) ) {
			unset( $settings->link_color );
		}

		if ( isset( $settings->link_hover_color ) ) {
			unset( $settings->link_hover_color );
		}

		return $settings;
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

		// Handle old opacity inputs.
		$helper->handle_opacity_inputs( $settings, 'text_bg_opacity', 'text_bg_color' );

		// migrate old color settings
		if ( isset( $settings->text_color ) && ! empty( $settings->text_color ) ) {
			$settings->meta_color    = $settings->text_color;
			$settings->content_color = $settings->text_color;
		}

		if ( isset( $settings->link_color ) && ! empty( $settings->link_color ) ) {
			$settings->title_color        = $settings->link_color;
			$settings->meta_link_color    = $settings->link_color;
			$settings->content_link_color = $settings->link_color;
			$settings->more_link_color    = $settings->link_color;
		}

		if ( isset( $settings->link_hover_color ) && ! empty( $settings->link_hover_color ) ) {
			$settings->title_hover_color        = $settings->link_hover_color;
			$settings->meta_link_hover_color    = $settings->link_hover_color;
			$settings->content_link_hover_color = $settings->link_hover_color;
			$settings->more_link_hover_color    = $settings->link_hover_color;

			// migrate settings for gallery layout
			if ( 'gallery' == $settings->layout ) {
				$settings->title_color = $settings->link_hover_color;
				$settings->meta_color  = $settings->link_hover_color;
			}
		}

		return $settings;
	}

	/**
	 * Remove pagination parameters
	 *
	 * @param array $query_args     Generated query args to override
	 * @return array                Updated query args
	 */
	public function remove_pagination_args( $query_args ) {
		$query_args['paged']  = 0;
		$query_args['offset'] = isset( $this->settings->offset ) ? $this->settings->offset : 0;
		return $query_args;
	}


	/**
	 * Full attachment image url.
	 *
	 * Gets a post ID and returns the url for the 'full' size of the attachment
	 * set as featured image.
	 *
	 * @param  int $id   The post ID.
	 * @since  1.5.9
	 * @return string    The featured image url for the 'full' size.
	 */
	protected function _get_uncropped_url( $id ) {
		$thumb_id = get_post_thumbnail_id( $id );
		$size     = isset( $this->settings->image_size ) ? $this->settings->image_size : 'medium';
		$img      = wp_get_attachment_image_src( $thumb_id, $size );
		return $img[0];
	}


	/**
	 * Get the featured image data.
	 *
	 * Gets a post ID and returns an array containing the featured image data.
	 *
	 * @param  int $id   The post ID.
	 * @since  1.5.9
	 * @return array    The image data.
	 */
	protected function _get_img_data( $id ) {

		$thumb_id = get_post_thumbnail_id( $id );

		return FLBuilderPhoto::get_attachment_data( $thumb_id );

	}


	/**
	 * Render thumbnail image for mobile.
	 *
	 * Gets the post ID and renders the html markup for the featured image
	 * in the desired cropped size.
	 *
	 * @param  int $id    The post ID.
	 * @since  1.5.9
	 * @return void
	 */
	public function render_img( $id = null ) {

		// check if image_type is set
		if ( isset( $this->settings->show_image ) && 1 == $this->settings->show_image ) {

			// get image source and data
			$src        = $this->_get_uncropped_url( $id );
			$photo_data = $this->_get_img_data( $id );

			// set params
			$photo_settings = array(
				'align'        => 'center',
				'link_type'    => 'url',
				'crop'         => $this->settings->crop,
				'photo'        => $photo_data,
				'photo_src'    => $src,
				'photo_source' => 'library',
				'attributes'   => array(
					'loading' => 'false',
				),
			);

			// if link id is provided, set link_url param
			if ( $id ) {
				$photo_settings['link_url'] = get_the_permalink( $id );
			}

			// render image
			FLBuilder::render_module_html( 'photo', $photo_settings );

		}

	}

	/**
	 * Renders the CSS class for each post item.
	 *
	 * @since 2.4
	 * @return void
	 */
	public function render_post_class( $layout ) {
		$settings     = $this->settings;
		$no_thumbnail = ! has_post_thumbnail( get_the_ID() );
		$classes      = array( 'fl-post-carousel-post' );

		if ( 'gallery' === $layout && $no_thumbnail ) {
			$classes[] = 'fl-post-no-thumb';
		}

		post_class( apply_filters( 'fl_builder_post_carousel_classes', $classes, $settings ) );
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLPostCarouselModule', array(
	'slider'  => array(
		'title'    => __( 'Slider', 'fl-builder' ),
		'sections' => array(
			'general'  => array(
				'title'  => '',
				'fields' => array(
					'layout'              => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'grid',
						'options' => array(
							'grid'    => __( 'Grid', 'fl-builder' ),
							'gallery' => __( 'Gallery', 'fl-builder' ),
						),
						'toggle'  => array(
							'grid'    => array(
								'sections' => array( 'content', 'content_style', 'more_link_style' ),
								'fields'   => array( 'equal_height', 'title_hover_color', 'meta_link_color', 'meta_link_hover_color' ),
							),
							'gallery' => array(
								'sections' => array( 'icons' ),
								'fields'   => array( 'hover_transition', 'post_icon_color' ),
							),
						),
					),
					'auto_play'           => array(
						'type'    => 'select',
						'label'   => __( 'Auto Play', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
					),
					'carousel_loop'       => array(
						'type'    => 'select',
						'label'   => __( 'Loop', 'fl-builder' ),
						'default' => 'false',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
					),
					'speed'               => array(
						'type'    => 'unit',
						'label'   => __( 'Delay', 'fl-builder' ),
						'default' => '5',
						'units'   => array( 'seconds' ),
						'slider'  => array(
							'max'  => 10,
							'step' => .5,
						),
					),
					'direction'           => array(
						'type'    => 'select',
						'label'   => __( 'Transition Direction', 'fl-builder' ),
						'default' => 'next',
						'options' => array(
							'next' => __( 'Right To Left', 'fl-builder' ),
							'prev' => __( 'Left To Right', 'fl-builder' ),
						),
					),
					'transition_duration' => array(
						'type'    => 'unit',
						'label'   => __( 'Transition Speed', 'fl-builder' ),
						'default' => '1',
						'units'   => array( 'seconds' ),
						'slider'  => array(
							'max'  => 10,
							'step' => .5,
						),
					),
				),
			),
			'controls' => array(
				'title'  => __( 'Slider Controls', 'fl-builder' ),
				'fields' => array(
					'pagination' => array(
						'type'    => 'select',
						'label'   => __( 'Show Dots', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
					),
					'navigation' => array(
						'type'    => 'select',
						'label'   => __( 'Show Arrows', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'sections' => array( 'nav_arrow_color' ),
							),
						),
					),
				),
			),

		),
	),
	'layout'  => array(
		'title'    => __( 'Layout', 'fl-builder' ),
		'sections' => array(
			'posts'   => array(
				'title'  => __( 'Posts', 'fl-builder' ),
				'fields' => array(
					'posts_per_page'   => array(
						'type'    => 'unit',
						'label'   => __( 'Number of Posts', 'fl-builder' ),
						'default' => '10',
						'size'    => '4',
					),
					'move_slides'      => array(
						'type'    => 'unit',
						'label'   => __( 'Number of Posts to Move', 'fl-builder' ),
						'default' => '1',
						'size'    => '2',
					),
					'slide_width'      => array(
						'type'    => 'unit',
						'label'   => __( 'Post Max Width', 'fl-builder' ),
						'default' => '300',
						'units'   => array( 'px' ),
						'slider'  => array(
							'max'  => 500,
							'step' => 10,
						),
					),
					'space_between'    => array(
						'type'    => 'unit',
						'label'   => __( 'Post Spacing', 'fl-builder' ),
						'default' => '30',
						'units'   => array( 'px' ),
						'slider'  => true,
					),
					'equal_height'     => array(
						'type'    => 'select',
						'label'   => __( 'Equalize Column Heights', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
					),
					'hover_transition' => array(
						'type'    => 'select',
						'label'   => __( 'Post Hover Transition', 'fl-builder' ),
						'default' => 'fade',
						'options' => array(
							'fade'       => __( 'Fade', 'fl-builder' ),
							'slide-up'   => __( 'Slide Up', 'fl-builder' ),
							'slide-down' => __( 'Slide Down', 'fl-builder' ),
							'scale-up'   => __( 'Scale Up', 'fl-builder' ),
							'scale-down' => __( 'Scale Down', 'fl-builder' ),
						),
					),
				),
			),
			'image'   => array(
				'title'  => __( 'Featured Image', 'fl-builder' ),
				'fields' => array(
					'show_image' => array(
						'type'    => 'select',
						'label'   => __( 'Image', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'image_size', 'crop' ),
							),
						),
					),
					'image_size' => array(
						'type'    => 'photo-sizes',
						'label'   => __( 'Size', 'fl-builder' ),
						'default' => 'medium',
					),
					'crop'       => array(
						'type'    => 'select',
						'label'   => __( 'Crop', 'fl-builder' ),
						'default' => 'landscape',
						'options' => array(
							''          => _x( 'None', 'Photo Crop.', 'fl-builder' ),
							'landscape' => __( 'Landscape', 'fl-builder' ),
							'panorama'  => __( 'Panorama', 'fl-builder' ),
							'portrait'  => __( 'Portrait', 'fl-builder' ),
							'square'    => __( 'Square', 'fl-builder' ),
							'circle'    => __( 'Circle', 'fl-builder' ),
						),
					),

				),
			),
			'icons'   => array(
				'title'  => __( 'Icons', 'fl-builder' ),
				'fields' => array(
					'post_has_icon'      => array(
						'type'    => 'select',
						'label'   => __( 'Use Icon for Posts', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'post_icon', 'post_icon_position', 'post_icon_color', 'post_icon_size' ),
							),
						),
					),
					'post_icon'          => array(
						'type'  => 'icon',
						'label' => __( 'Post Icon', 'fl-builder' ),
					),
					'post_icon_position' => array(
						'type'    => 'select',
						'label'   => __( 'Post Icon Position', 'fl-builder' ),
						'default' => 'above',
						'options' => array(
							'above' => __( 'Above Text', 'fl-builder' ),
							'below' => __( 'Below Text', 'fl-builder' ),
						),
					),
					'post_icon_size'     => array(
						'type'    => 'unit',
						'label'   => __( 'Post Icon Size', 'fl-builder' ),
						'default' => '24',
						'units'   => array( 'px' ),
						'slider'  => true,
					),
				),
			),
			'info'    => array(
				'title'  => __( 'Post Info', 'fl-builder' ),
				'fields' => array(
					'show_author' => array(
						'type'    => 'select',
						'label'   => __( 'Author', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_date'   => array(
						'type'    => 'select',
						'label'   => __( 'Date', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'date_format' ),
							),
						),
					),
					'date_format' => array(
						'type'    => 'select',
						'label'   => __( 'Date Format', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'M j, Y'  => gmdate( 'M j, Y' ),
							'F j, Y'  => gmdate( 'F j, Y' ),
							'm/d/Y'   => gmdate( 'm/d/Y' ),
							'm-d-Y'   => gmdate( 'm-d-Y' ),
							'd M Y'   => gmdate( 'd M Y' ),
							'd F Y'   => gmdate( 'd F Y' ),
							'Y-m-d'   => gmdate( 'Y-m-d' ),
							'Y/m/d'   => gmdate( 'Y/m/d' ),
						),
					),
				),
			),
			'content' => array(
				'title'  => __( 'Content', 'fl-builder' ),
				'fields' => array(
					'show_content'   => array(
						'type'    => 'select',
						'label'   => __( 'Content', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'sections' => array( 'content_style' ),
							),
						),
					),
					'show_more_link' => array(
						'type'    => 'select',
						'label'   => __( 'More Link', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'sections' => array( 'more_link_style' ),
								'fields'   => array( 'more_link_text' ),
							),
						),
					),
					'more_link_text' => array(
						'type'    => 'text',
						'label'   => __( 'More Link Text', 'fl-builder' ),
						'default' => __( 'Read More', 'fl-builder' ),
					),
				),
			),
		),
	),
	'style'   => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'item_style'      => array(
				'title'  => __( 'Carousel Item', 'fl-builder' ),
				'fields' => array(
					'text_bg_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'default'     => 'ffffff',
						'help'        => __( 'The color applies to the overlay behind text over the background selections.', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'post_icon_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Icon Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'duo_color1'      => array(
						'label'      => __( 'DuoTone Icon Primary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-carousel-icon i.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'duo_color2'      => array(
						'label'      => __( 'DuoTone Icon Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-carousel-icon i.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
				),
			),
			'title_style'     => array(
				'title'  => __( 'Post Title', 'fl-builder' ),
				'fields' => array(
					'title_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '{node} .fl-post-carousel-title a',
									'property' => 'color',
								),
							),
						),
					),
					'title_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type' => 'none',
						),
					),
					'title_typography'  => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-post-carousel-title',
						),
					),
				),
			),
			'info_style'      => array(
				'title'  => __( 'Post Info', 'fl-builder' ),
				'fields' => array(
					'meta_color'            => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '{node} .fl-post-carousel-meta',
									'property' => 'color',
								),
							),
						),
					),
					'meta_link_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => 'cccccc',
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '{node} .fl-post-carousel-meta a',
									'property' => 'color',
								),
							),
						),
					),
					'meta_link_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => 'ffffff',
						'preview'     => array(
							'type' => 'none',
						),
					),
					'meta_typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-post-carousel-meta',
						),
					),
				),
			),
			'content_style'   => array(
				'title'  => __( 'Post Content', 'fl-builder' ),
				'fields' => array(
					'content_color'            => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '{node} .fl-post-carousel-content',
									'property' => 'color',
								),
							),
						),
					),
					'content_link_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '{node} .fl-post-carousel-content a:not(.fl-post-carousel-more)',
									'property' => 'color',
								),
							),
						),
					),
					'content_link_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type' => 'none',
						),
					),
					'content_typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-post-carousel-content :not(.fl-post-carousel-more)',
						),
					),
				),
			),
			'more_link_style' => array(
				'title'  => __( 'More Link', 'fl-builder' ),
				'fields' => array(
					'more_link_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '{node} .fl-post-carousel-more',
									'property' => 'color',
								),
							),
						),
					),
					'more_link_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type' => 'none',
						),
					),
					'more_link_typography'  => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-post-carousel-more',
						),
					),
				),
			),
			'nav_arrow_color' => array(
				'title'  => 'Nav Arrows',
				'fields' => array(
					'arrows_bg_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Arrows Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'arrows_bg_style'   => array(
						'type'    => 'select',
						'label'   => __( 'Arrows Background Style', 'fl-builder' ),
						'default' => 'circle',
						'options' => array(
							'circle' => __( 'Circle', 'fl-builder' ),
							'square' => __( 'Square', 'fl-builder' ),
						),
					),
					'arrows_text_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Arrows Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-carousel-navigation path',
							'property' => 'fill',
						),
					),
				),
			),
		),
	),
	'content' => array(
		'title' => __( 'Content', 'fl-builder' ),
		'file'  => FL_BUILDER_DIR . 'includes/loop-settings.php',
	),
));
