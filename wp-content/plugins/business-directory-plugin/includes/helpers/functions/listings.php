<?php
/**
 * Listing related useful functions
 *
 * @package BDP/Includes/Listings
 */

require_once WPBDP_PATH . 'includes/models/class-listing.php';

/**
 * @param array  $args
 * @param bool   $error
 * @param string $context
 * @return WPBDP_Listing|WP_Error|false
 *
 * @since 5.0
 */
function wpbdp_save_listing( $args = array(), $error = false, $context = '' ) {
    // TODO: how to support edits without rewriting everything? i.e. if $args has a listing ID but not all fields or
    // values, only new values are updated leaving everything as before.
    global $wpdb;

	WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );

    $args = apply_filters( 'wpbdp_save_listing_args', $args, $error, $context );

    $listing                = array();
    $listing['listing_id']  = ! empty( $args['listing_id'] ) ? absint( $args['listing_id'] ) : 0;
    $listing['sequence_id'] = ! empty( $args['sequence_id'] ) ? $args['sequence_id'] : '';

    // Basic post info.
    $listing['post_title']   = ! empty( $args['post_title'] ) ? $args['post_title'] : '';
    $listing['post_content'] = ! empty( $args['post_content'] ) ? $args['post_content'] : '';
    $listing['post_excerpt'] = ! empty( $args['post_excerpt'] ) ? $args['post_excerpt'] : '';
    $listing['post_author']  = ( ! empty( $args['post_author'] ) ? absint( $args['post_author'] ) : ( ! empty( $args['user_id'] ) ? $args['user_id'] : 0 ) );
    $listing['post_name']    = ! empty( $args['post_name'] ) ? $args['post_name'] : '';
    $listing['post_status']  = ! empty( $args['post_status'] ) ? $args['post_status'] : ( $listing['listing_id'] ? wpbdp_get_option( 'edit-post-status' ) : 'pending' );

    // Fields.
    $listing['fields'] = ! empty( $args['fields'] ) ? $args['fields'] : array();
    foreach ( array_keys( $listing['fields'] ) as $field_id ) {
        $field_obj = wpbdp_get_form_field( $field_id );

        if ( ! $field_obj ) {
            unset( $listing['fields'][ $field_id ] );
            continue;
        }

        $field_assoc = $field_obj->get_association();
        if ( in_array( $field_assoc, array( 'title', 'excerpt', 'content' ) ) ) {
            if ( empty( $listing[ 'post_' . $field_assoc ] ) ) {
                $listing[ 'post_' . $field_assoc ] = $listing['fields'][ $field_id ];
            }

            unset( $listing['fields'][ $field_id ] );
        }
    }

    // Images.
    $listing['images'] = ! empty( $args['images'] ) ? $args['images'] : array();
    $append_images     = ! empty( $args['append_images'] );

    // Categories.
    $listing['categories'] = ! empty( $args['categories'] ) ? $args['categories'] : array();
    $append_categories     = false;

    // Plan.
    $listing['plan_id'] = ! empty( $args['plan_id'] ) ? absint( $args['plan_id'] ) : 0;

    // Expiration date.
    $listing['expiration_date'] = '';
    if ( ! empty( $args['expiration_date'] ) ) {
        $listing['expiration_date'] = $args['expiration_date'];
    } elseif ( ! empty( $args['expires_on'] ) ) {
        $listing['expiration_date'] = $args['expires_on'];
    }

    // Sanitize everything.
    if ( empty( $listing['post_title'] ) ) {
		$listing['post_title'] = __( 'Untitled Listing', 'business-directory-plugin' );
    }

    if ( ! empty( $listing['post_title'] ) && empty( $listing['post_name'] ) ) {
        $listing['post_name'] = sanitize_title( trim( strip_tags( $listing['post_title'] ) ) );

        // We use a faster slug algorithm for CSV imports.
        if ( 'csv-import' == $context ) {
            $post_name_hash = 'wpbdp-slug-' . sha1( $listing['post_name'] );
            $slug_prefix    = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $post_name_hash ) );

            if ( ! is_null( $slug_prefix ) && function_exists( '_truncate_post_slug' ) ) {
                $slug_prefix          = intval( $slug_prefix ) + 1;
                $listing['post_name'] = _truncate_post_slug( $listing['post_name'], 200 - strlen( (string) $slug_prefix ) - 1 ) . '-' . $slug_prefix;

                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s", $slug_prefix, $post_name_hash ) );
            }
        }
    }

    if ( empty( $listing['post_author'] ) ) {
        $listing['post_author'] = get_current_user_id();

        // TODO: maybe add this behavior again?
        // if ( 0 == $post_author ) {
        // Create user.
        // if ( $email_field = wpbdp_get_form_fields( array( 'validators' => 'email', 'unique' => 1 ) ) ) {
        // $email = $state->fields[ $email_field->get_id() ];
        //
        // if ( email_exists( $email ) ) {
        // $post_author = get_user_by( 'email', $email );
        // $post_author = $post_author->ID;
        // } else {
        // $post_author = wp_insert_user( array(
        // 'user_login' => 'guest_' . wp_generate_password( 5, false, false ),
        // 'user_email' => $email,
        // 'user_pass' => wp_generate_password()
        // ) );
        // }
        // }
        // }
    }

    $listing = apply_filters( 'wpbpd_save_listing_data', $listing, $context );
    extract( $listing );

    $adding  = ( empty( $listing_id ) );
    $editing = ! $adding;

    $post = array(
        'ID'           => $listing_id,
        'post_author'  => $post_author,
        'post_content' => $post_content,
        'post_title'   => $post_title,
        'post_excerpt' => $post_excerpt,
        'post_status'  => $post_status,
        'post_type'    => WPBDP_POST_TYPE,
        'post_name'    => $post_name,
    );

    $listing_id = wp_insert_post( $post, true );
    if ( is_wp_error( $listing_id ) ) {
        return $error ? $listing_id : false;
    }

    if ( $sequence_id ) {
        update_post_meta( $listing_id, '_wpbdp[import_sequence_id]', $sequence_id );
    }

    $listing_obj = wpbdp_get_listing( $listing_id );
    $listing_obj->set_categories( $categories );
    $listing_obj->set_images( $images, $append_images );

    foreach ( $fields as $field_id => $field_value ) {
        $field = wpbdp_get_form_field( $field_id );
        $field->store_value( $listing_id, $field_value );
    }
    // FIXME: fake this (for compatibility with modules) until we move everything to wpbdp_save_listing() and
    // friends. See #2945.
    do_action_ref_array( 'WPBDP_Listing::set_field_values', array( &$listing_obj, $fields ) ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.NotLowercase

    // Set plan for new listings.
    if ( $adding || 'csv-import' === $context ) {
        $plan = wpbdp_get_fee_plan( $plan_id );

        if ( ! $plan ) {
            $plan = wpbdp_get_fee_plan( 'free' );
        }

        if ( 'csv-import' === $context ) {
            $payment = $listing_obj->set_fee_plan_with_payment( $plan );
        } elseif ( $adding ) {
            $listing_obj->set_fee_plan( $plan );
        }
    }

    // Update expiration date if necessary.
    $listing_obj->update_plan(
        array( 'expiration_date' => $expiration_date ), array(
			'clear'       => false,
			'recalculate' => false,
        )
    );

    // Force GUIDs to always be <home-url>?post_type=wpbdp_listing&p=<post_id>
    if ( $adding && ( ! isset( $guid ) || ! $guid ) ) {
        $post_link = add_query_arg(
            array(
				'post_type' => WPBDP_POST_TYPE,
				'p'         => $listing_id,
            ), ''
        );
        $wpdb->update( $wpdb->posts, array( 'guid' => home_url( $post_link ) ), array( 'ID' => $listing_id ) );
        clean_post_cache( $listing_id );
    }

    $listing_obj->_after_save( $adding ? 'submit-new' : 'submit-edit' );

    return $listing_obj;
}

/**
 * @since 5.0
 */
function wpbdp_get_listing( $listing_id ) {
    return WPBDP_Listing::get( $listing_id );
}

/**
 * @param string $email
 * @param int    $posts_per_page
 * @param int    $offset
 * @return array
 *
 * @since 5.0.6
 */
function wpbdp_get_listings_by_email( $email, $posts_per_page = -1, $offset = 0 ) {
    global $wpdb;

    $post_ids = array();

    // Lookup by user.
    if ( $user = get_user_by( 'email', $email ) ) {
        $user_id  = $user->ID;
        $post_ids = array_merge(
            $post_ids,
            $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s AND post_author = %d", WPBDP_POST_TYPE, 'auto-draft', $user_id ) )
        );
    }

    // Lookup by e-mail field.
    if ( $email_field = wpbdp_get_form_fields( 'validators=email&unique=1' ) ) {
        $field_id = $email_field->get_id();
        $post_ids = array_merge(
            $post_ids,
            $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND LOWER(meta_value) = %s", '_wpbdp[fields][' . $field_id . ']', strtolower( $email ) ) )
        );
    }

    // Filter everything through get_posts().
    $post_ids = get_posts(
        array(
			'post_type'      => WPBDP_POST_TYPE,
			'post_status'    => array( 'publish', 'draft', 'pending' ),
			'posts_per_page' => $posts_per_page,
			'offset'         => $offset,
			'post__in'       => $post_ids ? $post_ids : array( -1 ),
			'fields'         => 'ids',
        )
    );

    return $post_ids;
}

