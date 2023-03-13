<?php
/**
 * Class WPBDP_Listing_Display_Helper
 *
 * @package BDP/Helpers/Display
 */

require_once WPBDP_PATH . 'includes/helpers/class-field-display-list.php';
/**
 * @since 4.0
 */
class WPBDP_Listing_Display_Helper {


    public static function excerpt() {
        static $n = 0;

        global $post;

        $vars = array();
        $vars = array_merge( $vars, array( 'even_or_odd' => ( ( $n & 1 ) ? 'odd' : 'even' ) ) );
        $vars = array_merge( $vars, self::basic_vars( $post->ID ) );
        $vars = array_merge( $vars, self::fields_vars( $post->ID, 'excerpt' ) );
        $vars = array_merge( $vars, self::images_vars( $post->ID, 'excerpt' ) );
        $vars = array_merge( $vars, self::css_classes( $post->ID, 'excerpt' ) );

        $vars['listing_css_class'] .= ' ' . $vars['even_or_odd'];

        $vars = apply_filters( 'wpbdp_listing_template_vars', $vars, $post->ID );
        $vars = apply_filters( 'wpbdp_excerpt_template_vars', $vars, $post->ID );

        $n++;

		$pre_content = '';
		if ( $n === 1 ) {
			// Add content before.
			$pre_content = apply_filters( 'wpbdp_before_excerpts', '', $vars );
		}

        // TODO: what do we do with 'wpbdp_excerpt_listing_fields' ?
        return $pre_content . wpbdp_x_render( 'excerpt', $vars );
    }

    public static function single() {
		$vars = self::single_listing_vars();

        // TODO: is this really used? can it be changed to something else?
        // 'listing_fields' => apply_filters('wpbdp_single_listing_fields', $listing_fields, $post->ID), This is
        // complete HTML
        $html  = '';
        $html .= wpbdp_x_render( 'single', $vars );
        $html .= '<script type="application/ld+json">';
        $html .= json_encode( self::schema_org( $vars ) );
        $html .= '</script>';

        return $html;
    }

	/**
	 * Get needed parameters for full or partial listing.
	 *
	 * @since v5.9
	 * @return array
	 */
	public static function single_listing_vars( $include = array() ) {
		global $post;

		$post_id    = isset( $include['id'] ) ? $include['id'] : $post->ID;
		$is_listing = isset( $include['id'] ) || ( $post && $post->post_type === wpbdp()->get_post_type() );
		if ( empty( $post_id ) || ! $is_listing ) {
			return array();
		}

		$vars = array(
			'listing_id' => $post_id,
		);
		if ( self::maybe_include_vars( $include, 'basic_vars' ) ) {
			$vars = array_merge( $vars, self::basic_vars( $post_id ) );
		}

		if ( self::maybe_include_vars( $include, 'fields_vars' ) ) {
			$vars = array_merge( $vars, self::fields_vars( $post_id, 'listing' ) );
		}

		if ( self::maybe_include_vars( $include, 'images_vars' ) ) {
			$vars = array_merge( $vars, self::images_vars( $post_id, 'listing' ) );
		}

		if ( self::maybe_include_vars( $include, 'css_classes' ) ) {
			$vars = array_merge( $vars, self::css_classes( $post_id, 'single' ) );
		}

		if ( ! empty( $vars['images'] ) && $vars['images']->main ) {
			if ( ! isset( $vars['listing_css_class'] ) ) {
				$vars['listing_css_class'] = '';
			}
			$vars['listing_css_class'] .= ' with-image';
		}

		$vars = apply_filters( 'wpbdp_listing_template_vars', $vars, $post_id );
		$vars = apply_filters( 'wpbdp_single_template_vars', $vars, $post_id );

		return $vars;
	}

	/**
	 * Allow selective data to cut down on memeory load and db calls when not used.
	 *
	 * @since v5.9
	 */
	private static function maybe_include_vars( $include, $var_name ) {
		return empty( $include ) || in_array( $var_name, $include, true );
	}

    private static function basic_vars( $listing_id ) {
        $listing = WPBDP_Listing::get( $listing_id );

        $vars               = array();
        $vars['listing_id'] = $listing_id;
        $vars['listing']    = $listing;
        $vars['is_sticky']  = ( 'normal' != $listing->get_sticky_status() );
        $vars['sticky_tag'] = '';
		$vars['title']      = the_title( '', '', false );
		$vars['title_type'] = apply_filters( 'wpbdp_heading_type', 'h1' );

        if ( $vars['is_sticky'] && ! empty( wpbdp_get_option( 'display-sticky-badge' ) ) ) {
			$img_src = self::get_sticky_image();
			if ( $img_src ) {
				$vars['sticky_tag'] = wpbdp_x_render(
					'listing sticky tag',
					array(
						'listing' => $listing,
						'img_src' => $img_src,
					)
				);
			} else {
				$vars['sticky_tag'] = '<span class="wpbdp-sticky-tag">' . esc_html__( 'Featured', 'business-directory-plugin' ) . '</span>';
			}

            $sticky_url = wpbdp_get_option( 'sticky-image-link-to' );

            if ( ! empty( $sticky_url ) ) {
                $vars['sticky_tag'] = sprintf(
                    '<a href="%s" rel="noopener" target="_blank">%s</a>',
					esc_url( $sticky_url ),
                    $vars['sticky_tag']
                );
            }
        }

        return $vars;
    }

    private static function css_classes( $listing_id, $display ) {
        $vars                   = array();
        $vars['listing_css_id'] = 'wpbdp-listing-' . $listing_id;

        $classes   = array();
        $classes[] = 'wpbdp-listing-' . $listing_id;
        $classes[] = 'wpbdp-listing';
        $classes[] = $display;
        $classes[] = 'wpbdp-' . $display;
        $classes[] = 'wpbdp-listing-' . $display;

        // Fee-related classes.
		$fee = WPBDP_Listing::get( $listing_id )->get_fee_plan();
        if ( $fee ) {
            $classes[] = 'wpbdp-listing-plan-id-' . $fee->fee_id;
            $classes[] = 'wpbdp-listing-plan-' . WPBDP_Utils::normalize( $fee->fee_label );

			if ( ! empty( $fee->fee->extra_data['bgcolor'] ) ) {
				// Prevent DB calls later.
				global $wpbdp;
				$wpbdp->fee_colors[ $fee->fee_id ] = $fee->fee->extra_data['bgcolor'];
			}

            if ( $fee->is_sticky ) {
                $classes[] = 'sticky';
                $classes[] = 'wpbdp-listing-is-sticky';
				$img_src = self::get_sticky_image();
				if ( ! $img_src ) {
					$classes[] = 'wpbdp-has-ribbon';
				}
            }
        }

		self::add_column_count( $classes, $display );

        foreach ( WPBDP_Listing::get( $listing_id )->get_categories( 'ids' ) as $category_id ) {
			$classes[] = 'wpbdp-listing-category-id-' . $category_id;
        }

        $vars['listing_css_class']  = implode( ' ', $classes );
        $vars['listing_css_class'] .= apply_filters( 'wpbdp_' . $display . '_view_css', '', $listing_id );

        return $vars;
    }

	private static function add_column_count( &$classes, $display ) {
		$columns   = (int) apply_filters( 'wpbd_column_count', 1, compact( 'display' ) );
		$cells     = floor( 12 / $columns );
		if ( $cells < 12 ) {
			$classes[] = 'wpbdp' . $cells;
		}
	}

	/**
	 * @return array
	 */
    public static function fields_vars( $listing_id, $display ) {
        $all_fields     = wpbdp_get_form_fields();
        $display_fields = apply_filters_ref_array( 'wpbdp_render_listing_fields', array( &$all_fields, $listing_id, $display ) );
        $fields         = array();
        $listing        = WPBDP_Listing::get( $listing_id );
		if ( ! $listing ) {
			return array();
		}

        $listing_cats = $listing->get_categories( 'ids' );
        foreach ( $display_fields as $field ) {
            if ( ! $field->validate_categories( $listing_cats ) ) {
                continue;
            }

            if ( $display === 'listing' && $field->get_association() === 'title' ) {
                continue;
            }

            $fields[] = $field;
        }

        $list = new WPBDP_Field_Display_List( $listing_id, $display, $fields );
        $list->freeze();

        return array( 'fields' => $list );
    }

    private static function images_vars( $listing_id, $display ) {
        $vars           = array();
        $vars['images'] = (object) array(
			'main'      => false,
			'extra'     => array(),
			'thumbnail' => false,
		);

        if ( ! wpbdp_get_option( 'allow-images' ) ) {
            return $vars;
        }

        $listing_id = apply_filters( 'wpbdp_listing_images_listing_id', $listing_id );
        $listing    = WPBDP_Listing::get( $listing_id );

		$thumbnail_id = $listing->get_thumbnail_id();
		$pass_args = array();
		if ( ! $thumbnail_id ) {
			$pass_args['coming_soon'] = self::get_coming_soon_image();
		}

		$which_thumbnail   = wpbdp_get_option( 'which-thumbnail' );
		$excerpt_thumbnail = wpbdp_get_option( 'show-thumbnail' );
		$show_bd_thumb     = $which_thumbnail === 'auto' && $display === 'listing';

        // Thumbnail.
		if ( $display === 'listing' && ! $show_bd_thumb ) {
			$vars['images']->thumbnail = false;
		} elseif ( $excerpt_thumbnail ) {
			$pass_args['link']  = 'listing';
			$pass_args['class'] = 'wpbdmthumbs wpbdp-excerpt-thumbnail';

            $thumb       = new StdClass();
			$thumb->html = wpbdp_listing_thumbnail( $listing_id, $pass_args, $display );

            $vars['images']->thumbnail = $thumb;
        }

        // Main image.
		$main_size = wpbdp_get_option( 'listing-main-image-default-size', 'wpbdp-thumb' );
		$data_main = wp_get_attachment_image_src( $thumbnail_id, $main_size, false );

		if ( $thumbnail_id && $show_bd_thumb ) {
			$pass_args['link']  = 'picture';
			$pass_args['class'] = 'wpbdp-single-thumbnail';

            $main_image         = new StdClass();
            $main_image->id     = $thumbnail_id;
			$main_image->html   = wpbdp_listing_thumbnail( $listing_id, $pass_args, $display );
            $main_image->url    = $data_main[0];
            $main_image->width  = $data_main[1];
            $main_image->height = $data_main[2];
        } else {
            $main_image = false;
        }

        $vars['images']->main = $main_image;

        // Other images.
        $listing_images = $listing->get_images( 'ids', true );
        $def_width      = wpbdp_get_option( 'thumbnail-width' );
        $def_height     = wpbdp_get_option( 'thumbnail-height' );
		$thumbnail_crop = wpbdp_get_option( 'thumbnail-crop' );
		$crop_class     = $thumbnail_crop ? ' wpbdp-thumbnail-cropped' : '';
        foreach ( $listing_images as $img_id ) {

            if ( $img_id == $thumbnail_id ) {
				$skipped_thumb = $which_thumbnail === 'none' && $display === 'listing';
				$include_thumb = $skipped_thumb && count( $listing_images ) > 1;
				if ( ! $include_thumb ) {
					// If the thumbnail was already shown, don't include it in the gallery.
					continue;
				}
            }

            $data    = wp_get_attachment_image_src( $img_id, 'wpbdp-large', false );
            $image_caption = get_post_meta( $img_id, '_wpbdp_image_caption', true );

            $image         = new StdClass();
            $image->id     = $img_id;
            $image->url    = $data[0];
            $image->width  = $data[1];
            $image->height = $data[2];
            $image->html   = sprintf(
                '<a href="%s" class="thickbox" data-lightbox="wpbdpgal" rel="wpbdpgal" target="_blank" rel="noopener" title="%s">%s</a>',
				esc_url( $image->url ),
				esc_attr( get_post_meta( $img_id, '_wpbdp_image_caption', true ) ),
                wp_get_attachment_image(
                    $image->id, 'wpbdp-thumb', false, array(
                        'class' => 'wpbdp-thumbnail size-thumbnail' . $crop_class,
						'alt'   => $image_caption ? $image_caption : the_title( '', '', false ),
						'title' => $image_caption ? $image_caption : the_title( '', '', false ),
                    )
                )
            );

            $vars['images']->extra[] = $image;
        }

        return $vars;
    }

	/**
	 * Gets sticky image url.
	 *
	 * @since 5.12
	 * @return string
	 */
	private static function get_sticky_image() {
		return self::get_image_option( 'listings-sticky-image' );
	}

	/**
	 * Gets coming soon image url.
	 *
	 * @since 5.12
	 * @return string
	 */
	public static function get_coming_soon_image() {
		return self::get_image_option( 'listings-coming-soon-image' );
	}

	/**
	 * Gets image url from setting.
	 *
	 * @since 5.12
	 * @return string
	 */
	private static function get_image_option( $option ) {
		$setting = wpbdp_get_option( $option );
		$image = '';
		if ( $setting ) {
			$image = wp_get_attachment_url( $setting );
		}

		self::get_default_image( $option, $image );

		return $image;
	}

	/**
	 * @since 5.12
	 */
	private static function get_default_image( $option, &$image ) {
		$defaults = array(
			'listings-coming-soon-image' => WPBDP_ASSETS_URL . 'images/default-image-big.gif',
		);

		if ( empty( $image ) && isset( $defaults[ $option ] ) ) {
			$image = $defaults[ $option ];
		}
	}

    private static function schema_org( $vars ) {
        $schema               = array();
        $schema['@context']   = 'http://schema.org';
        $schema['@type']      = 'LocalBusiness';
        $schema['name']       = $vars['title'];
        $schema['url']        = get_permalink( $vars['listing_id'] );
        if ( ! empty( $vars['images']->main ) ) {
            $schema['image'] = $vars['images']->main->url;
        }
        $schema['priceRange'] = '$$';

        $fields = $vars['fields'];
        $fsx    = array();
        foreach ( $fields as $f ) {
            $field_schema = $f->field->get_schema_org( $vars['listing_id'] );

            if ( ! $field_schema ) {
                continue;
            }

            foreach ( $field_schema as $key => $value ) {
                if ( ! $value ) {
                    continue;
                }

                if ( is_array( $value ) ) {
                    $schema[ $key ] = array_merge( isset( $schema[ $key ] ) ? $schema[ $key ] : array(), $value );
                } else {
					$schema[ $key ] = $value;
                }
            }
        }

        $schema = apply_filters( 'wpbdp_listing_schema_org', $schema, $vars['listing_id'] );

        return $schema;
    }

}

/**
 * @since 4.0
 */
class _WPBDP_Listing_Display_Image {
}
