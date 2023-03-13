<?php

class WPBDP__Migrations__2_3 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        $count = $wpdb->get_var(
			sprintf( "SELECT COUNT(*) FROM {$wpdb->prefix}options WHERE option_name LIKE '%%%s%%'", 'wpbusdirman_settings_fees_label_' ) );

		for ( $i = 1; $i <= $count; $i++ ) {
			$label  = get_option( '_settings_fees_label_' . $i, get_option( 'wpbusdirman_settings_fees_label_' . $i ) );
			$amount = get_option( '_settings_fees_amount' . $i, get_option( 'wpbusdirman_settings_fees_amount_' . $i, '0.00' ) );
			$days   = intval( get_option( '_settings_fees_increment_' . $i, get_option( 'wpbusdirman_settings_fees_increment_' . $i, 0 ) ) );
			$images = intval( get_option( '_settings_fees_images_' . $i, get_option( 'wpbusdirman_settings_fees_images_' . $i, 0 ) ) );
			$categories = get_option( '_settings_fees_categories_' . $i, get_option( 'wpbusdirman_settings_fees_categories_' . $i, '' ) );

            $newfee = array();
            $newfee['label'] = $label;
            $newfee['amount'] = $amount;
            $newfee['days'] = $days;
            $newfee['images'] = $images;

            $category_data = array('all' => false, 'categories' => array());
            if ($categories == '0') {
                $category_data['all'] = true;
            } else {
				foreach ( explode( ',', $categories ) as $category_id ) {
					$category_data['categories'][] = intval( $category_id );
                }
            }

			$newfee['categories'] = serialize( $category_data );

			if ( $wpdb->insert( $wpdb->prefix . 'wpbdp_fees', $newfee ) ) {
                $new_id = $wpdb->insert_id;

                $query = $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_key = %s AND meta_value = %s AND {$wpdb->postmeta}.post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
                                         $new_id, '_wpbdp_listingfeeid', $i, 'wpbdm-directory');
				$wpdb->query( $query );

				foreach ( array( 'label', 'amount', 'increment', 'images', 'categories' ) as $k ) {
					delete_option( 'wpbusdirman_settings_fees_' . $k . '_' . $i );
					delete_option( '_settings_fees_' . $k . '_' . $i );
				}
            }

        }
    }

}
