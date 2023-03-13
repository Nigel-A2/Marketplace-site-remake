<?php

/**
 * @class FLSlideshowModule
 */
class FLSlideshowModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Slideshow', 'fl-builder' ),
			'description'     => __( 'Display multiple photos in a slideshow view.', 'fl-builder' ),
			'category'        => __( 'Media', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'slides.svg',
		));

		$this->add_js( 'yui3' );
		$this->add_js( 'fl-slideshow' );
		$this->add_css( 'fl-slideshow' );
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		// Cache the photo data if using the WordPress media library.
		if ( 'wordpress' == $settings->source ) {
			$settings->photo_data = $this->get_wordpress_photos();
		}

		return $settings;
	}

	/**
	 * @method get_source
	 */
	public function get_source() {
		// WordPress
		if ( 'wordpress' == $this->settings->source ) {
			return $this->get_wordpress_source();
		}

		// SmugMug
		if ( 'smugmug' == $this->settings->source ) {
			return $this->get_smugmug_source();
		}
	}

	/**
	 * @method get_wordpress_photos
	 */
	public function get_wordpress_photos() {
		$photos   = array();
		$ids      = $this->settings->photos;
		$thumb_w  = get_option( 'thumbnail_size_w' );
		$medium_w = get_option( 'medium_size_w' );
		$large_w  = get_option( 'large_size_w' );

		if ( empty( $ids ) ) {
			return $photos;
		}

		foreach ( $ids as $id ) {

			$photo = FLBuilderPhoto::get_attachment_data( $id );

			// Use the cache if we didn't get a photo from the id.
			if ( ! $photo ) {

				if ( ! isset( $this->settings->photo_data ) ) {
					continue;
				} elseif ( is_array( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data[ $id ];
				} elseif ( is_object( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data->{$id};
				} else {
					continue;
				}
			}

			// Only use photos who have the sizes object.
			if ( isset( $photo->sizes ) ) {

				// Photo data object
				$data          = new stdClass();
				$data->caption = $photo->caption;
				$data->alt     = $photo->alt;

				// Photo sizes
				if ( isset( $photo->sizes->large ) ) {

					$data->largeURL = $photo->sizes->large->url; // @codingStandardsIgnoreLine

					if ( $photo->sizes->full->width <= 2560 ) {
						$data->x3largeURL = $photo->sizes->full->url; // @codingStandardsIgnoreLine
					} else {
						$data->x3largeURL = $photo->sizes->large->url; // @codingStandardsIgnoreLine
					}
				} else {
					$data->largeURL = $photo->sizes->full->url; // @codingStandardsIgnoreLine
					$data->x3largeURL = $photo->sizes->full->url; // @codingStandardsIgnoreLine
				}

				// Thumb size
				if ( isset( $photo->sizes->thumbnail ) ) {
					$data->thumbURL = $photo->sizes->thumbnail->url; // @codingStandardsIgnoreLine
				} else {
					$data->thumbURL = $photo->sizes->full->url; // @codingStandardsIgnoreLine
				}

				// Push the photo data
				$photos[ $id ] = $data;
			}
		}

		return $photos;
	}

	/**
	 * @method get_wordpress_source
	 */
	public function get_wordpress_source() {
		$photos = $this->get_wordpress_photos();

		// Build the source js object
		if ( count( $photos ) > 0 ) {

			$source  = 'type: "urls", urls:';
			$objects = array();

			foreach ( $photos as $photo ) {
				$caption   = str_replace( array( "\r", "\n" ), '', nl2br( htmlspecialchars( $photo->caption ) ) );
				$alt       = empty( $photo->alt ) ? '' : esc_attr( $photo->alt );
				$urls      = '{' . "\n";
				$urls .= 'thumbURL: "' . $photo->thumbURL . '",'; // @codingStandardsIgnoreLine
				$urls .= 'largeURL: "' . $photo->largeURL . '",'; // @codingStandardsIgnoreLine
				$urls .= 'x3largeURL: "' . $photo->x3largeURL . '",'; // @codingStandardsIgnoreLine
				$urls     .= 'caption: "' . $caption . '",';
				$urls     .= 'alt: "' . $alt . '",';
				$urls     .= '}';
				$objects[] = $urls;
			}

			return $source . '[' . implode( ',', $objects ) . ']';
		} else {
			return '';
		}
	}

	/**
	 * @method get_smugmug_source
	 */
	public function get_smugmug_source() {
		$gallery_id = array();
		$parts      = explode( '?', $this->settings->feed_url );

		if ( count( $parts ) == 2 ) {

			$parts = explode( '&', $parts[1] );

			foreach ( $parts as $part ) {
				if ( stristr( $part, 'data' ) ) {
					$parts      = explode( '=', $part );
					$gallery_id = explode( '_', $parts[1] );
					break;
				}
			}
		}

		if ( count( $gallery_id ) > 0 ) {
			return 'type: "smugmug", id: "' . $gallery_id[0] . '", key: "' . $gallery_id[1] . '"';
		} else {
			return '';
		}
	}

	/**
	 * @method get_nav_buttons
	 */
	public function get_nav_buttons() {
		$buttons = array();

		if ( 'buttons' == $this->settings->nav_type ) {

			if ( $this->settings->arrow_buttons ) {
				$buttons[] = '"prev"';
			}
			if ( $this->settings->play_button ) {
				$buttons[] = '"play"';
			}
			if ( $this->settings->arrow_buttons ) {
				$buttons[] = '"next"';
			}

			echo implode( ',', $buttons );
		}
	}

	/**
	 * @method get_nav_buttons_left
	 */
	public function get_nav_buttons_left() {

		$buttons = array();

		if ( $this->settings->thumbs_button && 'thumbs' != $this->settings->nav_type ) {
			$buttons[] = '"thumbs"';
		}
		if ( $this->settings->caption_button ) {
			$buttons[] = '"caption"';
		}
		if ( $this->settings->social_button ) {
			$buttons[] = '"social"';
		}
		if ( 'thumbs' == $this->settings->nav_type ) {
			$buttons[] = '"prevPage"';
		}

		echo implode( ',', $buttons );
	}

	/**
	 * @method get_nav_buttons_right
	 */
	public function get_nav_buttons_right() {
		$buttons = array();

		if ( $this->settings->count ) {
			$buttons[] = '"count"';
		}
		if ( $this->settings->fs_button ) {
			$buttons[] = '"fullscreen"';
		}
		if ( 'thumbs' == $this->settings->nav_type ) {
			$buttons[] = '"nextPage"';
		}

		echo implode( ',', $buttons );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLSlideshowModule', array(
	'general'  => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general'      => array(
				'title'  => '',
				'fields' => array(
					'source'   => array(
						'type'    => 'select',
						'label'   => __( 'Source', 'fl-builder' ),
						'default' => 'wordpress',
						'options' => array(
							'wordpress' => __( 'Media Library', 'fl-builder' ),
							'smugmug'   => 'SmugMug',
						),
						'help'    => __( 'Pull images from the WordPress media library or a gallery on your SmugMug site by inserting the RSS feed URL from SmugMug. The RSS feed URL can be accessed by using the get a link function in your SmugMug gallery.', 'fl-builder' ),
						'toggle'  => array(
							'wordpress' => array(
								'fields' => array( 'photos' ),
							),
							'smugmug'   => array(
								'fields' => array( 'feed_url' ),
							),
						),
					),
					'photos'   => array(
						'type'        => 'multiple-photos',
						'label'       => __( 'Photos', 'fl-builder' ),
						'connections' => array( 'multiple-photos' ),
					),
					'feed_url' => array(
						'type'        => 'text',
						'label'       => __( 'Feed URL', 'fl-builder' ),
						'connections' => array( 'custom_field' ),
					),
				),
			),
			'display'      => array(
				'title'  => __( 'Display', 'fl-builder' ),
				'fields' => array(
					'height'  => array(
						'type'     => 'unit',
						'label'    => __( 'Height', 'fl-builder' ),
						'default'  => '500',
						'sanitize' => 'absint',
						'units'    => array( 'px' ),
						'slider'   => array(
							'step' => 10,
							'max'  => 1000,
						),
					),
					'color'   => array(
						'type'    => 'select',
						'label'   => __( 'Skin Color', 'fl-builder' ),
						'default' => 'light',
						'options' => array(
							'light' => _x( 'Light', 'Color.', 'fl-builder' ),
							'dark'  => _x( 'Dark', 'Color.', 'fl-builder' ),
						),
						'help'    => __( 'If your overall theme/images are lighter in color, light will display buttons in a darker color scheme and vice versa for dark.', 'fl-builder' ),
					),
					'crop'    => array(
						'type'    => 'select',
						'label'   => __( 'Crop', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'help'    => __( 'Crop set to no will fit the slideshow images to the height you specify and keep the width proportional, whereas crop set to yes will fit the slideshow images to all sides of the content area while cropping the left and right to fit the height you specify.', 'fl-builder' ),
					),
					'protect' => array(
						'type'    => 'select',
						'label'   => __( 'Disable Right-Click', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'click_action' => array(
				'title'  => __( 'Click Action', 'fl-builder' ),
				'fields' => array(
					'click_action'     => array(
						'type'    => 'select',
						'label'   => __( 'Type', 'fl-builder' ),
						'default' => 'none',
						'options' => array(
							'none' => _x( 'None', 'Click action type.', 'fl-builder' ),
							'url'  => __( 'Link', 'fl-builder' ),
						),
						'toggle'  => array(
							'url' => array(
								'fields' => array( 'click_action_url' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'click_action_url' => array(
						'type'    => 'link',
						'label'   => __( 'Link URL', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
	'playback' => array(
		'title'    => __( 'Playback', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'auto_play'          => array(
						'type'    => 'select',
						'label'   => __( 'Auto Play', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
					),
					'speed'              => array(
						'type'     => 'unit',
						'label'    => __( 'Speed', 'fl-builder' ),
						'default'  => '3',
						'sanitize' => 'FLBuilderUtils::sanitize_non_negative_number',
						'units'    => array( 'seconds' ),
						'slider'   => true,
					),
					'transition'         => array(
						'type'    => 'select',
						'label'   => __( 'Transition', 'fl-builder' ),
						'default' => 'fade',
						'options' => array(
							'none'            => _x( 'None', 'Slideshow transition.', 'fl-builder' ),
							'fade'            => __( 'Fade', 'fl-builder' ),
							'kenBurns'        => __( 'Ken Burns', 'fl-builder' ),
							'slideHorizontal' => __( 'Slide Horizontal', 'fl-builder' ),
							'slideVertical'   => __( 'Slide Vertical', 'fl-builder' ),
							'blinds'          => __( 'Blinds', 'fl-builder' ),
							'bars'            => __( 'Bars', 'fl-builder' ),
							'barsRandom'      => __( 'Random Bars', 'fl-builder' ),
							'boxes'           => __( 'Boxes', 'fl-builder' ),
							'boxesRandom'     => __( 'Random Boxes', 'fl-builder' ),
							'boxesGrow'       => __( 'Boxes Grow', 'fl-builder' ),
						),
					),
					'transitionDuration' => array(
						'type'     => 'unit',
						'label'    => __( 'Transition Speed', 'fl-builder' ),
						'default'  => '1',
						'sanitize' => 'FLBuilderUtils::sanitize_non_negative_number',
						'units'    => array( 'seconds' ),
						'slider'   => true,
					),
					'randomize'          => array(
						'type'    => 'select',
						'label'   => __( 'Randomize Photos', 'fl-builder' ),
						'default' => 'false',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
	'controls' => array(
		'title'    => __( 'Controls', 'fl-builder' ),
		'sections' => array(
			'general'             => array(
				'title'  => '',
				'fields' => array(
					'image_nav' => array(
						'type'    => 'select',
						'label'   => __( 'Navigation Arrows', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
						'help'    => __( 'Navigational arrows allow the visitor to freely move through the images in your slideshow. These are larger arrows that overlay your slideshow images and are separate from the control bar navigational arrows.', 'fl-builder' ),
					),
				),
			),
			'control_bar'         => array(
				'title'  => __( 'Control Bar', 'fl-builder' ),
				'fields' => array(
					'nav_type'     => array(
						'type'    => 'select',
						'label'   => __( 'Nav Type', 'fl-builder' ),
						'default' => 'none',
						'options' => array(
							'none'    => _x( 'None', 'Nav type.', 'fl-builder' ),
							'buttons' => __( 'Buttons', 'fl-builder' ),
							'thumbs'  => __( 'Thumbs', 'fl-builder' ),
						),
						'toggle'  => array(
							'buttons' => array(
								'sections' => array( 'control_bar_buttons', 'control_bar_overlay', 'thumbs', 'social' ),
								'fields'   => array( 'nav_position', 'arrow_buttons', 'thumbs_button' ),
							),
							'thumbs'  => array(
								'sections' => array( 'control_bar_buttons', 'control_bar_overlay', 'thumbs', 'social' ),
								'fields'   => array( 'nav_position' ),
							),
						),
					),
					'nav_position' => array(
						'type'    => 'select',
						'label'   => __( 'Nav Position', 'fl-builder' ),
						'default' => 'bottom',
						'options' => array(
							'bottom' => __( 'Bottom', 'fl-builder' ),
							'top'    => __( 'Top', 'fl-builder' ),
						),
					),
				),
			),
			'control_bar_buttons' => array(
				'title'  => __( 'Control Bar Buttons', 'fl-builder' ),
				'fields' => array(
					'arrow_buttons'  => array(
						'type'    => 'select',
						'label'   => __( 'Navigation Arrows', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'play_button'    => array(
						'type'    => 'select',
						'label'   => __( 'Play Button', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'fs_button'      => array(
						'type'    => 'select',
						'label'   => __( 'Fullscreen Button', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'count'          => array(
						'type'    => 'select',
						'label'   => __( 'Photo Count', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'thumbs_button'  => array(
						'type'    => 'select',
						'label'   => __( 'Thumbs Button', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'caption_button' => array(
						'type'    => 'select',
						'label'   => __( 'Caption Button', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'social_button'  => array(
						'type'    => 'select',
						'label'   => __( 'Social Button', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
				),
			),
			'control_bar_overlay' => array(
				'title'  => __( 'Control Bar Overlay', 'fl-builder' ),
				'fields' => array(
					'nav_overlay'        => array(
						'type'    => 'select',
						'label'   => __( 'Overlay Enabled', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'overlay_hide', 'overlay_hide_delay' ),
							),
						),
						'help'    => __( 'Enabling this setting displays the control bar in an overlay at the bottom or top of the slides.', 'fl-builder' ),
					),
					'overlay_hide'       => array(
						'type'    => 'select',
						'label'   => __( 'Overlay Hide', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
						'help'    => __( 'If set to Yes, hides the control bar overlay after the number of seconds you specify. Control bar overlay reappears upon mouseover.', 'fl-builder' ),
					),
					'overlay_hide_delay' => array(
						'type'        => 'text',
						'label'       => __( 'Overlay Hide Delay', 'fl-builder' ),
						'default'     => '3',
						'size'        => '5',
						'description' => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'fl-builder' ),
					),
				),
			),
			'thumbs'              => array(
				'title'  => __( 'Thumbs', 'fl-builder' ),
				'fields' => array(
					'thumbs_size' => array(
						'type'     => 'unit',
						'label'    => __( 'Thumbs Size', 'fl-builder' ),
						'default'  => '50',
						'sanitize' => 'absint',
						'units'    => array( 'px' ),
						'slider'   => true,
					),
				),
			),
			'social'              => array(
				'title'  => __( 'Social', 'fl-builder' ),
				'fields' => array(
					'facebook'  => array(
						'type'    => 'select',
						'label'   => __( 'Facebook Button', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'twitter'   => array(
						'type'    => 'select',
						'label'   => __( 'Twitter Button', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'pinterest' => array(
						'type'    => 'select',
						'label'   => __( 'Pinterest Button', 'fl-builder' ),
						'default' => 'true',
						'options' => array(
							'false' => __( 'No', 'fl-builder' ),
							'true'  => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
));
