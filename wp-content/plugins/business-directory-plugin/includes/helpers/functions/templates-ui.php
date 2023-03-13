<?php
/**
 * UI Functions to be called from templates.
 *
 * @package WPBDP/Templates User Interface
 */

/**
 * Returns a list of directory categories using the configured directory settings.
 * The list is actually produced by {@link wpbdp_list_categories()}.
 *
 * @return string HTML output.
 * @uses wpbdp_list_categories().
 */
function wpbdp_directory_categories() {
    $args = apply_filters(
        'wpbdp_main_categories_args',
        array(
            'hide_empty'  => wpbdp_get_option( 'hide-empty-categories' ),
            'parent_only' => wpbdp_get_option( 'show-only-parent-categories' ),
        )
    );

    $html = wpbdp_list_categories( $args );

    return apply_filters( 'wpbdp_main_categories', $html );
}

/**
 * Identical to {@link wpbdp_directory_categories()}, except the output is printed instead of returned.
 *
 * @uses wpbdp_directory_categories().
 */
function wpbdp_the_directory_categories() {
    echo wpbdp_directory_categories();
}

/**
 * @since 2.3
 * @access private
 */
function _wpbdp_padded_count( &$term, $return = false ) {
    global $wpdb;

    $found = false;
    $count = intval( wp_cache_get( 'term-padded-count-' . $term->term_id, 'wpbdp', false, $found ) );

    if ( ! $count && ! $found ) {

        $count = 0;

        $tree_ids = array_merge( array( $term->term_id ), get_term_children( $term->term_id, WPBDP_CATEGORY_TAX ) );

		$format = implode( ', ', array_fill( 0, count( $tree_ids ), '%d' ) );
		$tt_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id IN ( $format ) AND taxonomy = %s",
				array_merge( (array) $tree_ids, array( WPBDP_CATEGORY_TAX ) )
			)
		);

		if ( $tt_ids ) {
			$format = implode( ', ', array_fill( 0, count( $tt_ids ), '%d' ) );
			$query = $wpdb->prepare(
				"SELECT COUNT(DISTINCT r.object_id) FROM {$wpdb->term_relationships} r INNER JOIN {$wpdb->posts} p ON p.ID = r.object_id WHERE p.post_status = %s and p.post_type = %s AND term_taxonomy_id IN ( $format )",
				array_merge( array( 'publish', WPBDP_POST_TYPE ), (array) $tt_ids )
			);

			$count = intval( $wpdb->get_var( $query ) );
		}

        $count = apply_filters( '_wpbdp_padded_count', $count, $term );
    }

    if ( $return ) {
        return $count;
    }

    $term->count = $count;
}

/**
 * @since 2.3
 * @access private
 */
function _wpbdp_list_categories_walk( $parent, $depth, $args ) {
	$terms       = _wpbdp_get_terms_from_args( $args );
	$terms_array = array();
	$term_ids    = array();
	foreach ( $terms as $term ) {
		$term_ids[] = $term->term_id;
		$terms_array[ $term->term_id ] = $term;
	}
	unset( $terms );

    $term_ids = apply_filters( 'wpbdp_category_terms_order', $term_ids );

    $terms = array();
    foreach ( $term_ids as $term_id ) {
        $t = $terms_array[ $term_id ];
        // 'pad_counts' doesn't work because of WP bug #15626 (see http://core.trac.wordpress.org/ticket/15626).
        // we need a workaround until the bug is fixed.
		_wpbdp_padded_count( $t );

        $terms[] = $t;
    }

    // filter empty terms
	if ( $args['hide_empty'] ) {
		$terms = array_filter(
			$terms,
			function( $x ) {
				return $x->count > 0;
			}
		);
    }

    $html = '';

    if ( ! $terms && $depth == 0 ) {
        if ( $args['no_items_msg'] ) {
            $html .= '<p>' . $args['no_items_msg'] . '</p>';
        }
        return $html;
    }

    if ( $depth > 0 ) {
        $html .= str_repeat( "\t", $depth );

        if ( apply_filters( 'wpbdp_categories_list_anidate_children', true ) && $terms ) {
            $html .= '<ul id="cat-item-' . $args['parent'] . '-children" class="children">';
        }
    }
    foreach ( $terms as &$term ) {
		$class = apply_filters( 'wpbdp_categories_list_item_css', '', $term ) . ' ' . ( $depth > 0 ? 'subcat' : '' );
		$html .= '<li class="cat-item cat-item-' . esc_attr( $term->term_id . ' ' . $class ) . '">';

        $item_html = '';
        $item_html .= '<a href="' . apply_filters( 'wpbdp_categories_term_link', esc_url( get_term_link( $term ) ) ) . '" ';
        $item_html .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $term->description, $term ) ) ) . '" class="category-label">';

        $item_html .= esc_attr( $term->name );
        $item_html .= '</a>';

        if ( $args['show_count'] ) {
            $count_str  = ' (' . intval( $term->count ) . ')';
            $count_str  = apply_filters( 'wpbdp_categories_item_count_str', $count_str, $term );
            $item_html .= $count_str;
        }

        $item_html = apply_filters( 'wpbdp_categories_list_item', $item_html, $term );
        $html     .= $item_html;

        if ( ! $args['parent_only'] ) {
            $args['parent'] = $term->term_id;
            if ( $subcats = _wpbdp_list_categories_walk( $term->term_id, $depth + 1, $args ) ) {
                $html .= $subcats;
            }
        }

        $html .= '</li>';
    }

    if ( $depth > 0 ) {
        if ( apply_filters( 'wpbdp_categories_list_anidate_children', true ) && $terms ) {
            $html .= '</ul>';
        }
    }

    return $html;
}

/**
 * Get the list of categories.
 *
 * @since 5.11
 */
function _wpbdp_get_terms_from_args( $args ) {
	$term_args = array(
		'taxonomy'   => WPBDP_CATEGORY_TAX,
		'orderby'    => $args['orderby'],
		'order'      => $args['order'],
		'hide_empty' => false,
		'fields'     => 'all',
		'parent'     => intval( is_object( $args['parent'] ) ? $args['parent']->term_id : $args['parent'] ),
	);

	if ( empty( $args['parent'] ) ) {
		$term_args['pad_counts'] = true;
	} else {
		$term_args['parent'] = is_object( $args['parent'] ) ? $args['parent']->term_id : intval( $args['parent'] );
	}
	return get_terms( $term_args );
}

/**
 * Produces a list of directory categories following some configuration settings that are overridable.
 *
 * The list of arguments is below:
 *      'parent' (int|object) - Parent directory category or category ID.
 *      'orderby' (string) default is taken from BD settings - What column to use for ordering the categories.
 *      'order' (string) default is taken from BD settings - What direction to order categories.
 *      'show_count' (boolean) default is taken from BD settings - Whether to show how many listings are in the category.
 *      'hide_empty' (boolean) default is False - Whether to hide empty categories or not.
 *      'parent_only' (boolean) default is False - Whether to show only direct childs of 'parent' or make a recursive list.
 *      'echo' (boolean) default is False - If True, the list will be printed in addition to returned by this function.
 *      'no_items_msg' (string) default is "No listing categories found." - Message to display when no categories are found.
 *
 * @param string|array $args array of arguments to be used while creating the list.
 * @return string HTML output.
 * @since 2.3
 * @see wpbdp_directory_categories()
 */
function wpbdp_list_categories( $args = array() ) {
    $args = wp_parse_args(
        $args, array(
            'echo'         => false,
            'orderby'      => wpbdp_get_option( 'categories-order-by' ),
            'order'        => wpbdp_get_option( 'categories-sort' ),
            'show_count'   => wpbdp_get_option( 'show-category-post-count' ),
            'hide_empty'   => false,
            'parent_only'  => false,
            'parent'       => 0,
            'no_items_msg' => _x( 'No listing categories found.', 'templates', 'business-directory-plugin' ),
        )
    );

    $html = '';

    $categories = _wpbdp_list_categories_walk( 0, 0, $args );

    if ( $categories ) {
        $attributes = apply_filters(
            'wpbdp_categories_list_attributes', array(
                'class'                         => 'wpbdp-categories cf ' . apply_filters( 'wpbdp_categories_list_css', '' ),
                'data-breakpoints'              => esc_attr( '{"tiny": [0,360], "small": [360,560], "medium": [560,710], "large": [710,999999]}' ),
                'data-breakpoints-class-prefix' => 'wpbdp-categories',
            )
        );

        $html .= '<ul ' . trim( wpbdp_html_attributes( $attributes ) ) . '>';
        $html .= $categories;
        $html .= '</ul>';
    }

    $html = apply_filters( 'wpbdp_categories_list', $html );

    if ( $args['echo'] ) {
        echo $html;
    }

    return $html;
}

/**
 * @param string|array $buttons buttons to be displayed in wpbdp_main_box()
 * @return string
 */
function wpbdp_main_links( $buttons = null ) {
    if ( is_string( $buttons ) ) {
        if ( 'none' == $buttons ) {
            $buttons = array();
		} elseif ( 'all' === $buttons ) {
            $buttons = array( 'directory', 'listings', 'create' );
        } else {
            $buttons = explode( ',', $buttons );
        }
    }

    if ( ! is_array( $buttons ) ) {
        // Use defaults.
        $buttons = array();

        if ( wpbdp_get_option( 'show-directory-button' ) ) {
            $buttons[] = 'directory';
        }

        if ( wpbdp_get_option( 'show-view-listings' ) ) {
            $buttons[] = 'listings';
        }

        if ( wpbdp_get_option( 'show-submit-listing' ) ) {
            $buttons[] = 'create';
        }

        if ( wpbdp_get_option( 'show-manage-listings' ) && is_user_logged_in() ) {
            $buttons[] = 'manage';
        }
    }

    $buttons = array_filter( array_unique( $buttons ) );

    if ( ! $buttons ) {
        return '';
    }

    if ( wpbdp_get_option( 'disable-submit-listing' ) ) {
        $buttons = array_diff( $buttons, array( 'create' ) );
    }

	$html          = array();
	$current_page  = ( is_ssl() ? 'https://' : 'http://' ) . wpbdp_get_server_value( 'HTTP_HOST' ) . wpbdp_get_server_value( 'REQUEST_URI' );

	if ( in_array( 'directory', $buttons, true ) ) {
		$link = wpbdp_url( '/' );
		if ( $current_page !== $link ) {
			$html[] = '<a href="' . esc_url( $link ) . '" id="wpbdp-bar-show-directory-button" class="button wpbdp-button">' .
				esc_html__( 'Directory', 'business-directory-plugin' ) .
				'</a>';
		}
	}

	if ( in_array( 'listings', $buttons, true ) ) {
		$link = wpbdp_url( 'all_listings' );
		if ( $current_page !== $link ) {
			$html[] = '<a href="' . esc_url( $link ) . '" id="wpbdp-bar-view-listings-button" class="button wpbdp-button">' .
				esc_html__( 'View All Listings', 'business-directory-plugin' ) .
				'</a>';
		}
    }

	if ( in_array( 'manage', $buttons, true ) ) {
		$html[] = '<a href="' . esc_url( wpbdp_url( 'manage_listings' ) ) . '" id="wpbdp-bar-manage-listing-button" class="button wpbdp-button">' .
			esc_html__( 'Manage Listings', 'business-directory-plugin' ) .
			'</a>';
    }

	if ( in_array( 'create', $buttons, true ) ) {
		$html[] = '<a href="' . esc_url( wpbdp_url( 'submit_listing' ) ) . '" id="wpbdp-bar-submit-listing-button" class="button wpbdp-button">' .
			esc_html__( 'Add Listing', 'business-directory-plugin' ) .
			'</a>';
    }

    if ( empty( $html ) ) {
        return '';
    }

	$buttons_count = count( $html );
	$html = implode( ' ', $html );

    $content  = '<div class="wpbdp-main-links-container" data-breakpoints=\'{"tiny": [0,360], "small": [360,560], "medium": [560,710], "large": [710,999999]}\' data-breakpoints-class-prefix="wpbdp-main-links">';
    $content .= '<div class="wpbdp-main-links wpbdp-main-links-' . $buttons_count . '-buttons">' . apply_filters( 'wpbdp_main_links', $html ) . '</div>';
    $content .= '</div>';

    return $content;
}


function wpbdp_the_main_links( $buttons = null ) {
    echo wpbdp_main_links( $buttons );
}

function wpbdp_search_form() {
    $html      = '';
    $html     .= sprintf(
        '<form id="wpbdmsearchform" action="%s" method="GET" class="wpbdp-search-form">',
        wpbdp_url( 'search' )
    );
    $html .= '<input type="hidden" name="wpbdp_view" value="search" />';

    if ( ! wpbdp_rewrite_on() ) {
        $html .= sprintf( '<input type="hidden" name="page_id" value="%d" />', wpbdp_get_page_id( 'main' ) );
    }

    $html .= '<label for="wpbdp-keyword-field" style="display:none;">Keywords:</label>';
    $html .= '<input type="hidden" name="dosrch" value="1" />';
    $html .= '<input id="intextbox" maxlength="150" name="q" size="20" type="text" value="" />';
    $html .= sprintf(
        '<input id="wpbdmsearchsubmit" class="submit wpbdp-button wpbdp-submit" type="submit" value="%s" />',
		esc_attr__( 'Search Listings', 'business-directory-plugin' )
    );
    $html .= sprintf(
        '<a href="%s" class="advanced-search-link">%s</a>',
        esc_url( wpbdp_url( 'search' ) ),
        _x( 'Advanced Search', 'templates', 'business-directory-plugin' )
    );
    $html .= '</form>';

    return $html;
}

function wpbdp_the_search_form() {
    if ( wpbdp_get_option( 'show-search-listings' ) ) {
        echo wpbdp_search_form();
    }
}

function wpbdp_the_listing_excerpt() {
    echo wpbdp_render_listing( null, 'excerpt' );
}

function wpbdp_listing_sort_options( $filters = array( 'wpbdp_listing_sort_options', 'wpbdp_listing_sort_options_html' ) ) {
	$show_sort    = wpbdp_get_option( 'listings-sortbar-enabled' );
	$sort_options = $show_sort ? wpbdp_maybe_apply_filter( 'wpbdp_listing_sort_options', $filters, array() ) : array();

	$html = '';

	if ( $sort_options ) {
		$sorting = wpbdp_get_listing_sort_links( $sort_options );

		$html .= '<div class="wpbdp-listings-sort-options">';
		$html .= '<label for="wpbdp-sort-bar">' . esc_html_x( 'Sort By:', 'templates sort', 'business-directory-plugin' ) . '</label>';
		$html .= '<select id="wpbdp-sort-bar" class="">';
		$html .= implode( ' ', $sorting );
		$html .= '</select>';
		$html .= '</div>';
	}

	return wpbdp_maybe_apply_filter( 'wpbdp_listing_sort_options_html', $filters, $html );
}

/**
 * @since v5.9
 */
function wpbdp_maybe_apply_filter( $filter, $filters, $value ) {
	return in_array( $filter, $filters ) ? apply_filters( $filter, $value ) : $value;
}

/**
 * Get links to include in the sorting options.
 *
 * @since v5.9
 */
function wpbdp_get_listing_sort_links( $sort_options ) {
	$current_sort = wpbdp_get_current_sort_option();

	$links = array();

	$links['reset'] = sprintf(
		'<option value="%s" class="header-option">%s</option>',
		esc_url( remove_query_arg( 'wpbdp_sort' ) ),
		esc_html__( 'Default', 'business-directory-plugin' )
	);

	$arrows = array(
		'ASC'  => '↓ ',
		'DESC' => '↑ ',
	);

	foreach ( $sort_options as $id => $option ) {
		$default_order = isset( $option[2] ) && ! empty( $option[2] ) ? strtoupper( $option[2] ) : 'ASC';

		$dir     = $default_order === 'ASC' ? '' : '-';
		$arrow   = '';

		if ( $current_sort && $current_sort->option == $id ) {
			$sort_dir = $current_sort->order === 'ASC' ? 'ASC' : 'DESC';
			$dir      = $sort_dir === 'ASC' ? '-' : '';
			$arrow    = $arrows[ $sort_dir ];

			$links[ $id . '-s' ] = sprintf(
				'<option value="%s" selected="selected">%s</option>',
				esc_url( add_query_arg( 'wpbdp_sort', $dir . $id ) ),
				esc_html( $arrow . $option[0] )
			);

			// Swap and include option for other direction.
			$sort_dir = $sort_dir === 'ASC' ? 'DESC' : 'ASC';
			$dir      = $sort_dir === 'ASC' ? '' : '-';
			$arrow    = $arrows[ $sort_dir ];
		}

		$links[ $id ] = sprintf(
			'<option value="%s">%s</option>',
			esc_url( add_query_arg( 'wpbdp_sort', $dir . $id ) ),
			esc_html( $arrow . $option[0] )
		);
	}

	return $links;
}

function wpbdp_the_listing_sort_options() {
    echo wpbdp_listing_sort_options();
}

/**
 * Displays the listing main image.
 *
 * @since 2.3
 */
function wpbdp_listing_thumbnail( $listing_id = null, $args = array(), $display = '' ) {
    if ( ! $listing_id ) {
        $listing_id = apply_filters( 'wpbdp_listing_images_listing_id', get_the_ID() );
    }

    $listing = WPBDP_Listing::get( $listing_id );

    $main_image = $listing->get_thumbnail();

    if ( $main_image ) {
        $thumbnail_id = $main_image->ID;
    } else {
        $thumbnail_id = 0;
    }

	$defaults = array(
		'link'  => 'picture',
		'class' => '',
		'echo'  => false,
	);
	if ( is_array( $args ) ) {
		$args = array_merge( $defaults, $args );
	} else {
		// For reverse compatibility.
		$args = wp_parse_args( $args, $defaults );
	}

    $image_img               = '';
    $image_link              = '';
    $image_title             = '';
    $listing_link_in_new_tab = '';
	$image_classes           = 'attachment-wpbdp-thumb ' . $args['class'];
	$image_size              = wpbdp_get_option( 'listing-main-image-default-size', 'wpbdp-thumb' );
	if ( $image_size === 'wpbdp-thumb' ) {
		// If this size is set, using the gallery thumb settings.
		$crop_class     = wpbdp_get_option( 'thumbnail-crop' ) ? ' wpbdp-thumbnail-cropped' : '';
		$image_classes .= $crop_class . ' wpbdp-thumbnail';
	}

	if ( $main_image ) {
		$image_title = get_post_meta( $main_image->ID, '_wpbdp_image_caption', true );

		$image_img  = wp_get_attachment_image(
			$main_image->ID,
			'uploaded' !== $image_size ? $image_size : '',
			false,
			array(
				'alt'   => $image_title ? $image_title : get_the_title( $listing_id ),
				'title' => $image_title ? $image_title : get_the_title( $listing_id ),
				'class' => $image_classes,
			)
		);

		if ( $args['link'] == 'picture' ) {
			$full_image_data = wp_get_attachment_image_src( $main_image->ID, 'wpbdp-large' );
			$image_link      = $full_image_data[0];
		}
	} elseif ( has_post_thumbnail( $listing_id ) ) {
        $caption = get_post_meta( get_post_thumbnail_id( $listing_id ), '_wpbdp_image_caption', true );
        $image_img = get_the_post_thumbnail(
            $listing_id,
            'wpbdp-thumb',
            array(
                'alt'   => $caption ? $caption : get_the_title( $listing_id ),
                'title' => $caption ? $caption : get_the_title( $listing_id ),
				'class' => $image_classes,
            )
        );
	} elseif ( isset( $args['coming_soon'] ) ) {
		$use_default_img = (array) wpbdp_get_option( 'use-default-picture', array() );
		if ( ! empty( $use_default_img ) && in_array( $display, $use_default_img ) ) {

			$image_src = $args['coming_soon'];
			$image_img  = sprintf(
				'<img src="%s" alt="%s" title="%s" border="0" width="%d" class="%s" />',
				esc_url( $image_src ),
				esc_attr( get_the_title( $listing_id ) ),
				esc_attr( get_the_title( $listing_id ) ),
				esc_attr( wpbdp_get_option( 'thumbnail-width' ) ),
				esc_attr( $image_classes )
			);
			$image_link = $args['link'] == 'picture' ? $image_src : '';
		}
    }

    if ( ! $image_link && $args['link'] == 'listing' ) {
        $image_link              = get_permalink( $listing_id );
        $listing_link_in_new_tab = wpbdp_get_option( 'listing-link-in-new-tab' ) ? '_blank' : '_self';
    }

	$args['image_img']   = $image_img;
	$args['image_link']  = $image_link;
	$args['listing_id']  = $listing_id;
	$args['image_title'] = $image_title;
	$args['listing_link_in_new_tab'] = $listing_link_in_new_tab;

	$image_html = wpbdp_thumbnail_html( $args );

	/**
	 * @since v5.9
	 */
    return apply_filters( 'wpbdp_thumbnail_html', $image_html, $args );
}

/**
 * Get the html for a listing thumbnail image.
 *
 * @since v5.9
 */
function wpbdp_thumbnail_html( $args ) {
	$image_img  = $args['image_img'];
	$image_link = $args['image_link'];

	if ( ! $image_img ) {
		return '';
	}

	if ( ! $image_link ) {
        return $image_img;
	}

	$image_link = apply_filters( 'wpbdp_listing_thumbnail_link', $image_link, $args['listing_id'], $args );

	if ( ! $image_link ) {
		return sprintf(
			'<div class="listing-thumbnail">%s</div>',
			$image_img
		);
	}

	if ( $args['link'] === 'picture' ) {
		$extra = 'data-lightbox="wpbdpgal" rel="wpbdpgal"';
	} elseif ( $args['listing_link_in_new_tab'] === '_blank' ) {
		$extra = 'rel="noopener noreferrer"';
	} else {
		$extra = '';
	}

	return sprintf(
		'<div class="listing-thumbnail"><a href="%s" target="%s" class="%s" title="%s" %s>%s</a></div>',
		esc_url( $image_link ),
		esc_attr( $args['listing_link_in_new_tab'] ),
		esc_attr( $args['link'] == 'picture' ? 'thickbox' : '' ),
		esc_attr( $args['image_title'] ),
		$extra,
		$image_img
	);
}

class WPBDP_ListingFieldDisplayItem {
    private $listing_id = 0;
    private $display    = '';

    private $html_       = '';
    private $html_value_ = '';
    private $value_      = null;

    public $id = 0;
    public $field;

    public function __construct( &$field, $listing_id, $display ) {
        $this->field      = $field;
        $this->id         = $this->field->get_id();
        $this->listing_id = $listing_id;
        $this->display    = $display;
    }

    public function __get( $key ) {
        switch ( $key ) {
            case 'html':
                if ( $this->html_ ) {
                    return $this->html_;
                }

                $this->html_ = $this->field->display( $this->listing_id, $this->display );
                return $this->html_;

            case 'html_value':
                if ( $this->html_value_ ) {
                    return $this->html_value_;
                }

                $this->html_value_ = $this->field->html_value( $this->listing_id );
                return $this->html_value_;

            case 'value':
                if ( $this->value_ ) {
                    return $this->value_;
                }

                $this->value_ = $this->field->value( $this->listing_id );
                return $this->value_;
        }
    }

    public static function prepare_set( $listing_id, $display ) {
        $res = (object) array(
            'fields' => array(),
            'social' => array(),
        );

        $form_fields = wpbdp_get_form_fields();
        $form_fields = apply_filters_ref_array( 'wpbdp_render_listing_fields', array( &$form_fields, $listing_id ) );

        foreach ( $form_fields as &$f ) {
            if ( ! $f->display_in( $display ) ) {
                continue;
            }

            if ( $f->display_in( 'social' ) ) {
                $res->social[ $f->get_id() ] = new self( $f, $listing_id, 'social' );
            } else {
                $res->fields[ $f->get_id() ] = new self( $f, $listing_id, $display );
            }
        }

        return $res;
    }

    public static function walk_set( $prop, $fields = array() ) {
        $res = array();

        foreach ( $fields as $k => &$f ) {
            $res[ $k ] = $f->{$prop};
        }

        return $res;
    }
}

/**
 * @since 5.0
 */
function wpbdp_the_main_box( $args = array() ) {
    echo wpbdp_main_box( $args = array() );
}

/**
 * @since 5.0
 */
function wpbdp_main_box( $args = null ) {
    $defaults = array(
        'buttons' => null,
        'in_shortcode' => false,
    );
    $args     = wp_parse_args( $args, $defaults );

    $extra_fields  = wpbdp_capture_action( 'wpbdp_main_box_extra_fields' );
    $hidden_fields = wpbdp_capture_action( 'wpbdp_main_box_hidden_fields' );
    $search_url    = wpbdp_url( 'search' );
    $no_cols       = 1;

    if ( $extra_fields ) {
        $no_cols = 2;
    }

    $template_vars = compact( 'hidden_fields', 'extra_fields', 'search_url', 'no_cols' );
    $template_vars = array_merge( $template_vars, $args );

    $html = wpbdp_x_render( apply_filters( 'wpbdp_main_box_template_name', 'main-box' ), $template_vars );
    return $html;
}
