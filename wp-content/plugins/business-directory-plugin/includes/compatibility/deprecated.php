<?php
/*
 * Deprecated functionality.
 */

define( 'WPBUSDIRMAN_TEMPLATES_PATH', WPBDP_PATH . '/includes/compatibility/templates' );

/* template-related */

function wpbusdirman_the_listing_meta( $excerptorsingle ) {
	_deprecated_function( __FUNCTION__, '' );

	$html = '';
	$fields = wpbdp_get_form_fields( array( 'association' => 'meta' ) );

	foreach ( $fields as &$f ) {
		if ( $excerptorsingle === 'excerpt' && ! $f->display_in( 'excerpt' ) ) {
			continue;
		}

		$html .= $f->display( get_the_ID() );
	}

	return $html;
}

function wpbusdirman_post_extra_thumbnails() {
	_deprecated_function( __FUNCTION__, '' );

	$html = '';

	$listing = WPBDP_Listing::get( get_the_ID() );
	$thumbnail_id = $listing->get_thumbnail_id();
	$images = $listing->get_images();

	if ( $images ) {
		$html .= '<div class="extrathumbnails">';

		foreach ( $images as $img ) {
			if ($img->ID == $thumbnail_id)
				continue;

			$html .= sprintf(
				'<a class="thickbox" href="%s"><img class="wpbdmthumbs" src="%s" alt="%s" title="%s" border="0" /></a>',
				esc_url( wp_get_attachment_url( $img->ID ) ),
				esc_url( wp_get_attachment_thumb_url( $img->ID ) ),
				esc_attr( the_title( '', '', false ) ),
				esc_attr( the_title( '', '', false ) )
			);
		}

		$html .= '</div>';
	}

	return $html;
}

// Display the listing fields in excerpt view
function wpbusdirman_display_the_listing_fields() {
	_deprecated_function( __FUNCTION__, '' );
	global $post;

	$html = '';

	foreach ( wpbdp_formfields_api()->get_fields() as $field ) {
		if ( ! $field->display_in( 'excerpt' ) ) {
			continue;
		}

		$html .= $field->display( $post->ID, 'excerpt' );
	}

	return $html;
}

function wpbusdirman_post_catpage_title() {
	_deprecated_function( __FUNCTION__, '' );
	$categories = WPBDP_CATEGORY_TAX;

	if ( get_query_var( $categories ) ) {
		$term = get_term_by( 'slug', get_query_var( $categories ), $categories );
	} elseif ( get_query_var( 'taxonomy' ) == $categories ) {
		$term = get_term_by( 'slug', get_query_var( 'term' ), $categories );
	} elseif ( get_query_var( 'taxonomy' ) == WPBDP_TAGS_TAX ) {
		$term = get_term_by( 'slug', get_query_var( 'term' ), WPBDP_TAGS_TAX );
	}

	return esc_attr( $term->name );
}

function wpbusdirman_post_list_categories() {
	_deprecated_function( __FUNCTION__, '', 'wpbdp_directory_categories' );
	return wpbdp_directory_categories();
}

/**
 * TODO: There doesn't seem to be a replacement for this deprecated function.
 *
 * @deprecated
 * @since 2.3
 */
function wpbusdirman_get_the_business_email( $post_id ) {
	// _deprecated_function( __FUNCTION__, '2.3' );

	$email_mode = wpbdp_get_option( 'listing-email-mode' );

	$email_field_value = '';
	if ( $email_field = wpbdp_get_form_fields( 'validators=email&unique=1' ) ) {
		$email_field_value = trim( $email_field->plain_value( $post_id ) );
	}

	if ( $email_mode === 'field' && ! empty( $email_field_value ) ) {
		return $email_field_value;
	}

	$author_email = '';
	$post = get_post( $post_id );
	$author_email = trim( get_the_author_meta( 'user_email', (int) $post->post_author ) );

	if ( empty( $author_email ) && ! empty( $email_field_value ) ) {
		return $email_field_value;
	}

	return $author_email ? $author_email : '';
}

/**
 * Finds a fee by its ID. The special ID of 0 is reserved for the "free fee".
 *
 * @param int $fee_id fee ID
 * @return object a fee object or NULL if nothing is found
 * @since 3.0.3
 * @deprecated since 3.7. Use {@link wpbdp_get_fee_plan()} instead.
 */
function wpbdp_get_fee( $fee_id ) {
	_deprecated_function( __FUNCTION__, '3.7', 'wpbdp_get_fee_plan' );

	return wpbdp_get_fee_plan( $fee_id );
}

/**
 * @since 2.3
 * @deprecated since 5.0
 */
function wpbdp_has_module( $module ) {
	_deprecated_function( __FUNCTION__, '5.0', 'wpbdp()->modules->is_loaded' );
	return wpbdp()->modules->is_loaded( $module );
}

function wpbdp_listing_upgrades_api() {
	return new WPBDP_NoopObject();
}

function wpbdp_get_page_ids_from_cache( $cache, $page_id ) {
	_deprecated_function( __FUNCTION__, '5.16.1' );

	global $wpdb;

	if ( ! is_array( $cache ) || empty( $cache[ $page_id ] ) ) {
		return null;
	}

	// Validate the cached IDs.
	$query  = _wpbdp_page_lookup_query( $page_id, true );
	$query .= ' AND ID IN ( ' . implode( ',', array_map( 'intval', $cache[ $page_id ] ) ) . ' ) ';

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$count = intval( $wpdb->get_var( $query ) );

	if ( $count != count( $cache[ $page_id ] ) ) {
		wpbdp_debug( 'Page cache is invalid.' );
		return null;
	}

	return $cache[ $page_id ];
}

/*
 * @since 2.1.7
 * @deprecated since 3.6.10. See {@link wpbdp_currency_format()}.
 */
function wpbdp_format_currency( $amount, $decimals = 2, $currency = null ) {
	_deprecated_function( __FUNCTION__, '3.6.10', 'wpbdp_currency_format' );

	if ( $amount == 0.0 ) {
		return '—';
	}

	return ( ! $currency ? wpbdp_get_option( 'currency-symbol' ) : $currency ) . ' ' . number_format( $amount, $decimals );
}

/**
 * @deprecated since 2.2.1
 */
function wpbdp_bar( $parts = array() ) {
	_deprecated_function( __FUNCTION__, '2.2.1' );

    $parts = wp_parse_args(
        $parts, array(
            'links'  => true,
            'search' => false,
        )
    );

    $html  = '<div class="wpbdp-bar cf">';
    $html .= apply_filters( 'wpbdp_bar_before', '', $parts );

    if ( $parts['links'] ) {
        $html .= wpbdp_main_links();
    }
    if ( $parts['search'] ) {
        $html .= wpbdp_search_form();
    }

    $html .= apply_filters( 'wpbdp_bar_after', '', $parts );
    $html .= '</div>';

    return $html;
}

/**
 * Recursively deletes a directory.
 *
 * @param string $path a directory.
 * @since 3.3
 * @deprecated since 3.6.10. Use {@link WPBDP_FS::rmdir} instead.
 */
function wpbdp_rrmdir( $path ) {
	_deprecated_function( __FUNCTION__, '3.6.10', 'WPBDP_FS::rmdir' );
    return WPBDP_FS::rmdir( $path );
}
