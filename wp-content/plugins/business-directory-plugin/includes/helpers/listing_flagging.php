<?php
/**
 * @since 5.1.6
 */
class WPBDP__Listing_Flagging {

    public static function get_flagging_options() {
        $flagging_options = wpbdp_get_option( 'listing-flagging-options' );
        $flagging_options = stripslashes( $flagging_options );
        $flagging_options = str_replace( "\r\n", "\n", $flagging_options );
        $flagging_options = trim( $flagging_options );

        if ( ! $flagging_options ) {
            return array();
        }

        $flagging_options = explode( "\n", $flagging_options );
        $flagging_options = array_map( 'trim', $flagging_options );

        return $flagging_options;
    }

    public static function is_flagged( $listing_id ) {
        return ( 1 == get_post_meta( $listing_id, '_wpbdp_flagged', true ) );
    }

    public static function user_has_flagged( $listing_id, $user_id ) {
		if ( ! $user_id ) {
            return false;
        }

        return array_search( $user_id, self::get_flagging_meta( $listing_id, 'user_id' ) );
    }


    public static function ip_has_flagged( $listing_id, $ip ) {
        return array_search( $ip, self::get_flagging_meta( $listing_id, 'ip' ) );
    }

    public static function get_flagging_meta( $listing_id, $key = 'all' ) {
        if ( ! $listing_id ) {
            return new WP_Error( 'missing_data', _x( 'Listing ID is required to save a report', 'flag listing', 'business-directory-plugin' ) );
        }

        $flagging_data = get_post_meta( $listing_id, '_wpbdp_flagged_data', true );

        if ( empty( $flagging_data ) ) {
            return array();
        }

        if ( 'all' == $key ) {
            return $flagging_data;
        }

        if ( ! in_array( $key, array( 'user_id', 'ip' ) ) ) {
            return array();
        }

        return wp_list_pluck( $flagging_data, $key );

    }

    public static function add_flagging( $listing_id, $data ) {
        if ( ! $listing_id ) {
            return new WP_Error( 'missing_data', _x( 'Listing ID is required to save a report', 'flag listing', 'business-directory-plugin' ) );
        }

        $defaults = array( 'user_id' => 0, 'ip' => 0, 'reason' => '', 'comments' => '' );

        $data = array_merge( $defaults, $data );

		if ( ! $data['user_id'] && ! $data['ip'] ) {
            return new WP_Error( 'missing_data', _x( 'User ID or IP address is required to save a report', 'flag listing', 'business-directory-plugin' ) );
        }

        $flagging_options = self::get_flagging_options();

        if ( ! empty( $flagging_options ) ) {
            if ( empty( $data['reason'] ) ) {
                return new WP_Error( 'missing_data', _x( 'Report reason is required to save a report', 'flag listing', 'business-directory-plugin' ) );
            }
        } else {
            if ( empty( $data['comments'] ) ) {
                return new WP_Error( 'missing_data', _x( 'Report comment is required to save a report', 'flag listing', 'business-directory-plugin' ) );
            }
        }

		if ( ! isset( $data['date'] ) ) {
			$data['date'] = time();
		}

        $flagging_data = self::get_flagging_meta( $listing_id );
        $flagging_data[] = $data;

        update_post_meta( $listing_id, '_wpbdp_flagged_data', $flagging_data );
        update_post_meta( $listing_id, '_wpbdp_flagged', 1 );

        do_action( 'wpbdp_listing_maybe_flagging_notice', WPBDP_Listing::get( $listing_id ), $data );

        return true;

    }

    public static function remove_flagging( $listing_id, $meta_pos = 'all' ) {
		if ( $meta_pos === 'all' ) {
            self::clear_flagging( $listing_id );
            return;
        }

        $flagging_data = self::get_flagging_meta( $listing_id );
        unset( $flagging_data[ $meta_pos ] );

        if ( ! count( $flagging_data ) ) {
            self::clear_flagging( $listing_id );
            return;
        }

        update_post_meta( $listing_id, '_wpbdp_flagged_data', $flagging_data );
    }

    public static function clear_flagging( $listing_id ) {
        delete_post_meta( $listing_id, '_wpbdp_flagged_data' );
        update_post_meta( $listing_id, '_wpbdp_flagged', 0 );
    }

}
