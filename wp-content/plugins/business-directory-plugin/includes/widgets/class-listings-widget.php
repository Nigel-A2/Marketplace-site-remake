<?php
/**
 * @since 3.5.3
 */
class WPBDP_Listings_Widget extends WP_Widget {

	protected $supports = array( 'images' );
	protected $defaults = array();


	public function __construct( $name, $description = '' ) {
		parent::__construct( '', $name, array( 'description' => $description ) );
		$this->defaults['title'] = str_replace( array( 'WPBDP', '_' ), array( '', ' ' ), get_class( $this ) );
	}

	/**
	 * Default Form Settings.
	 *
	 * @since  x.x
	 *
	 * @return array
	 */
	protected function defaults() {
		return array(
			'number_of_listings' => 5,
			'show_images'        => 0,
			'default_image'      => 0,
			'thumbnail_desktop'  => 'left',
			'thumbnail_mobile'   => 'above',
			'fields'             => array(),
		);
	}

	/**
	 * Instance defaults
	 *
	 * @return array
	 */
	protected function instance_defaults( $instance ) {
		return array_merge( $this->defaults(), $instance );
	}

	protected function set_default_option_value( $k, $v = '' ) {
		$this->defaults[ $k ] = $v;
	}

	protected function get_field_value( $instance, $k ) {
		$instance = $this->instance_defaults( $instance );
		if ( isset( $instance[ $k ] ) ) {
			return $instance[ $k ];
		}

		if ( isset( $this->defaults[ $k ] ) ) {
			return $this->defaults[ $k ];
		}

		return false;
	}

	public function print_listings( $instance ) {
		return '';
	}

	public function get_listings( $instance ) {
		return array();
	}

	protected function _form( $instance ) { }

	/**
	 * Render the settings form
	 */
	public function form( $instance ) {
		$instance = $this->instance_defaults( $instance );
		require WPBDP_INC . 'views/widget/widget-settings.php';
		return '';
	}

	/**
	 * Handle settings update
	 */
	public function update( $new, $old ) {
		$instance                       = $old;
		$instance['title']              = strip_tags( $new['title'] );
		$instance['number_of_listings'] = max( intval( $new['number_of_listings'] ), 1 );
		$instance['show_images']        = ! empty( $new['show_images'] ) ? 1 : 0;
		$instance['fields']             = ! empty( $new['fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $new['fields'] ) ) : array();

		if ( $instance['show_images'] ) {
			$instance['default_image']     = ! empty( $new['default_image'] ) ? 1 : 0;
			$instance['thumbnail_desktop'] = sanitize_text_field( $new['thumbnail_desktop'] );
			$instance['thumbnail_mobile']  = sanitize_text_field( $new['thumbnail_mobile'] );
			$instance['thumbnail_width']   = max( intval( $new['thumbnail_width'] ), 0 );
			$instance['thumbnail_height']  = max( intval( $new['thumbnail_height'] ), 0 );
		}

		return $instance;
	}

	/**
	 * Escape content and allow svg in the content.
	 * This is mainly becuase some of the fields, like the ratings, use svg.
	 *
	 * @param string $content The text to filter.
	 *
	 * @since 5.15
	 *
	 * @return string
	 */
	protected function escape_content( $content ) {
		$kses_defaults = wp_kses_allowed_html( 'post' );

		$svg_args = array(
			'svg'   => array(
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true, // <= Must be lower case!
			),
			'g'     => array( 'fill' => true ),
			'title' => array( 'title' => true ),
			'path'  => array( 'd' => true, 'fill' => true,  ),
		);

		$allowed_tags = array_merge( $kses_defaults, $svg_args );

		return wp_kses( $content, $allowed_tags );
	}

	/**
	 * Render the widget
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$title    = apply_filters( 'widget_title', $this->get_field_value( $instance, 'title' ) );
		$instance = $this->instance_defaults( $instance );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		$out = $this->print_listings( $instance );

		if ( ! $out ) {
			$listings = $this->get_listings( $instance );
			$out     .= '<ul class="wpbdp-listings-widget-list">';
			$out     .= $this->render( $listings, $instance );
			$out     .= '</ul>';
		}

		echo $out;
		echo $after_widget;
	}


	/**
	 * @param array  $items
	 * @param array  $instance
	 * @param string $html_class CSS class for each LI element.
	 *
	 * @return string HTML
	 */
	protected function render( $items, $instance, $html_class = '' ) {
		if ( empty( $items ) ) {
			return $this->render_empty_widget( $html_class );
		}

		return $this->render_widget( $items, $instance, $html_class );
	}

	/**
	 * Render empty message
	 *
	 * @param string $html_class - the html class to append to the view
	 *
	 * @since  x.x
	 *
	 * @return string
	 */
	private function render_empty_widget( $html_class ) {
		return sprintf( '<li class="wpbdp-empty-widget %s">%s</li>', esc_attr( $html_class ), esc_html__( 'There are currently no listings to show.', 'business-directory-plugin' ) );
	}

	/**
	 * Render the widget
	 *
	 * @param array $items - the widget items
	 * @param array $instance - the settings instance
	 * @param string $html_class - the html class to append to the view
	 *
	 * @since 5.15
	 *
	 * @return string
	 */
	private function render_widget( $items, $instance, $html_class ) {
		$html_class = implode(
			' ',
			array(
				$this->get_item_thumbnail_position_css_class( $instance['thumbnail_desktop'], 'desktop' ),
				$this->get_item_thumbnail_position_css_class( $instance['thumbnail_mobile'], 'mobile' ),
				$html_class,
			)
		);

		$show_images       = in_array( 'images', $this->supports, true ) && isset( $instance['show_images'] ) && $instance['show_images'];
		$img_size          = $this->get_image_size( $instance );
		$default_image     = $show_images && isset( $instance['default_image'] ) && $instance['default_image'];
		$coming_soon_image = WPBDP_Listing_Display_Helper::get_coming_soon_image();
		$fields            = is_array( $instance['fields'] ) ? $instance['fields'] : array();
		foreach ( $items as $post ) {
			$html[] = $this->render_item( $post, compact( 'show_images', 'img_size', 'default_image', 'coming_soon_image', 'html_class', 'fields' ) );
		}

		$this->add_css( $img_size, $html );

		return join( "\n", $html );
	}

	/**
	 * Generate the thumbnail position classes.
	 *
	 * @param string $thumbnail_position - the thumbnail position ( left, right )
	 * @param string $device - the device being used ( desktop, mobile )
	 *
	 * @since 5.15
	 *
	 * @return string
	 */
	private function get_item_thumbnail_position_css_class( $thumbnail_position, $device ) {
		if ( $thumbnail_position == 'left' || $thumbnail_position == 'right' ) {
			$css_class = sprintf( 'wpbdp-listings-widget-item-with-%s-thumbnail-in-%s', $thumbnail_position, $device );
		} else {
			$css_class = sprintf( 'wpbdp-listings-widget-item-with-thumbnail-above-in-%s', $device );
		}

		return $css_class;
	}

	/**
	 * @since 5.15
	 *
	 * @return string|array
	 */
	private function get_image_size( $instance ) {
		$width  = isset( $instance['thumbnail_width'] ) ? $instance['thumbnail_width'] : 0;
		$height = isset( $instance['thumbnail_height'] ) ? $instance['thumbnail_height'] : 0;

		$img_size = 'wpbdp-thumb';
		if ( $width > 0 || $height > 0 ) {
			$img_size = array( $width, $height );
		}

		return $img_size;
	}

	/**
	 * Render item for widget.
	 *
	 * @param WP_Post $post The current listing post.
	 * @param array $args The view arguments.
	 *
	 * @since 5.15
	 *
	 * @return string
	 */
	private function render_item( $post, $args ) {
		$listing       = wpbdp_get_listing( $post->ID );
		$listing_title = sprintf( '<div class="wpbdp-listing-title"><a class="listing-title" href="%s">%s</a></div>', esc_url( $listing->get_permalink() ), esc_html( $listing->get_title() ) );
		$html_image    = $this->render_image( $listing, $args );
		$fields        = sprintf( '<div class="wpbdp-listing-fields">%s</div>', $this->render_fields( $listing, $args['fields'] ) );
		$template      = '<li class="wpbdp-listings-widget-item %1$s"><div class="wpbdp-listings-widget-container">';
		if ( ! empty( $html_image ) ) {
			$template .= '<div class="wpbdp-listings-widget-thumb">%2$s</div>';
		} else {
			$args['html_class'] .= ' wpbdp-listings-widget-item-without-thumbnail';
		}
		$template      .= '<div class="wpbdp-listings-widget-item--title-and-content">%3$s %4$s</div></li>';
		$args['image'] = $html_image;
		$output        = sprintf( $template, esc_attr( $args['html_class'] ), $html_image, $listing_title, $fields );
		return apply_filters( 'wpbdp_listing_widget_item', $this->escape_content( $output ), $args );
	}

	/**
	 * Render the listing image.
	 * Depending on the settings, this will return the listing image or the default image or none.
	 *
	 * @param object $listing The listing object.
	 * @param array $args The view arguments.
	 *
	 * @since  x.x
	 *
	 * @return string
	 */
	private function render_image( $listing, $args ) {
		$image_link = '';
		if ( $args['show_images'] ) {
			$img_size = $args['img_size'];
			if ( is_array( $img_size ) ) {
				$img_size = 'medium';
			}

			$img_id    = $listing->get_thumbnail_id();
			$permalink = $listing->get_permalink();

			if ( $img_id ) {
				$image_link = '<a href="' . esc_url( $permalink ) . '">' .
					wp_get_attachment_image( $img_id, $img_size, false, array( 'class' => 'listing-image' ) ) .
					'</a>';
			} elseif ( $args['default_image'] ) {
				$class      = "attachment-$img_size size-$img_size listing-image";
				$image_link = '<a href="' . esc_url( $permalink ) . '">' .
					'<img src="' . esc_url( $args['coming_soon_image'] ) . '" class="' . esc_attr( $class ) . '" alt="' . esc_attr( $listing->get_title() ) . '" loading="lazy" />' .
					'</a>';
			} else {
				// For image spacing.
				$image_link = '<span></span>';
			}
		}
		return apply_filters( 'wpbdp_listings_widget_render_image', wp_kses_post( $image_link ), $listing );
	}

	/**
	 * Render fields.
	 * Render the field items in the widget.
	 *
	 * @param object $listing The listing object.
	 * @param array $allowed_fields The field ids to show.
	 *
	 * @since 5.15
	 *
	 * @return string
	 */
	private function render_fields( $listing, $allowed_fields ) {
		if ( empty( $allowed_fields ) ) {
			return '';
		}

		$listing_data = WPBDP_Listing_Display_Helper::fields_vars( $listing->get_id(), 'excerpt' );
		if ( empty( $listing_data['fields'] ) ) {
			return '';
		}

		$fields     = $listing_data['fields'];
		$field_html = array();
		$field_obj  = null;
		$field_id   = '';
		foreach ( $fields->not( 'social' ) as $field ) {
			$field_obj = $field->field;
			$field_id  = $field_obj->get_field_type()->get_id();
			if ( $field_id === 'title' ) {
				continue;
			}

			if ( ! in_array( $field_obj->get_id(), $allowed_fields ) ) {
				continue;
			}

			$html = $field_obj->html_value( $listing->get_id(), 'widget' );
			if ( ! empty( $html ) ) {
				$field_html[] = sprintf( '<div class="wpbdp-listings-widget-item--field-%1$s">%2$s</div>', $field_id, $html );
			}
		}

		return $this->escape_content( join( "\n", $field_html ) );
	}

	/**
	 * Use the image height/width settings
	 *
	 * @since 5.15
	 */
	private function add_css( $img_size, &$html ) {
		if ( ! is_array( $img_size ) ) {
			return;
		}

		$img_style = '<style>#' . esc_attr( $this->id ) . ' .listing-image{';
		if ( $img_size[0] ) {
			$img_style .= 'max-width:' . absint( $img_size[0] ) . 'px;';
		}
		if ( $img_size[1] ) {
			$img_style .= 'max-height:' . absint( $img_size[1] ) . 'px;';
		}
		$img_style .= '}</style>';

		$html[] = $img_style;
	}
}
