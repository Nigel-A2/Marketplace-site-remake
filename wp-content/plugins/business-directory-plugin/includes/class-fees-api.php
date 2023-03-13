<?php
require_once WPBDP_INC . 'models/class-fee-plan.php';

if ( ! class_exists( 'WPBDP_Fees_API' ) ) {

class WPBDP_Fees_API {

    public function __construct() {
        $this->setup_default_fees();
    }

    private function setup_default_fees() {
        global $wpdb;

        $count = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_plans WHERE tag = %s", 'free' ) ) );

        if ( 0 === $count ) {
            // Add free plan to the DB.
            $wpdb->insert(
                $wpdb->prefix . 'wpbdp_plans',
                array(
					'tag' => 'free',
					'label' => __( 'Free Listing', 'business-directory-plugin' ),
					'amount' => 0.0,
					'images' => 0,
					'days'   => 365,
					'supported_categories' => 'all',
					'pricing_model' => 'flat',
					'sticky' => 0,
					'enabled' => 1
                )
            );
            $fee_id = $wpdb->insert_id;

            // Update all "free fee" listings to use this.
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_listings SET fee_id = %d WHERE fee_id = %d OR fee_id IS NULL", $fee_id, 0 ) );
        } else if ( $count > 1 ) {
            // Delete "extra" plans. This shouldn't happen, but sometimes it happens :/
            $fee_ids  = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wpbdp_plans WHERE tag = %s", 'free' ) );
            $first_id = $fee_ids[0];

            $fee_ids_str = implode( ',', $fee_ids );
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_listings SET fee_id = %d WHERE fee_id IN ({$fee_ids_str})", $first_id ) );

            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_plans WHERE tag = %s AND id != %d", 'free', $first_id ) );
        }
    }

	/**
	 * Check if there are enabled plans with a fee.
	 * This does a count for all enabled plans with a price greater than 0.
	 *
	 * @since 5.17
	 *
	 * @return bool
	 */
	public static function has_paid_plans() {
		$total = self::get_enabled_plans( true );
		return $total > 0;
	}


    /**
	 * Check if there are enabled plans.
	 * This does a count for all enabled plans regardless of the amount.
	 *
	 * @param bool $paid Return paid plans or all plans. Set to true to check for plans that have a price greater than 0
	 *
	 * @since 5.18
	 *
	 * @return bool
	 */
	public static function get_enabled_plans( $paid = false ) {
		global $wpdb;
		$query     = "SELECT count(*) FROM {$wpdb->prefix}wpbdp_plans WHERE enabled != 0";
		$cache_key = 'enabled_plan_count';
		if ( $paid ) {
			$cache_key = 'paid_plan_count';
			$query     .= ' AND amount > 0';
		}
		$total = WPBDP_Utils::check_cache(
			array(
				'cache_key' => $cache_key,
				'group'     => 'wpbdp_plans',
				'query'     => $query,
				'type'      => 'get_var',
			)
		);
		return $total;
	}

    /**
     * @deprecated since 3.7. See {@link wpbdp_get_fee_plans()}.
     */
    public function get_fees( $categories = null ) {
		_deprecated_function( __METHOD__, '3.7', 'wpbdp_get_fee_plans' );

        global $wpdb;

        if ( ! $categories )
            return wpbdp_get_fee_plans();

        $fees = array();
        foreach ( $categories as $cat_id ) {
            $category_fees = wpbdp_get_fee_plans( array( 'categories' => $cat_id ) );

            // XXX: For now, we keep the free plan a 'secret' when payments are enabled. This is for backwards compat.
            if ( wpbdp_payments_possible() ) {
                foreach ( $category_fees as $k => $v ) {
                    if ( 'free' == $v->tag || ! $v->enabled )
                        unset( $category_fees[ $k ] );
                }
            }

            // Do this so the first plan is at index 0.
            $category_fees = array_merge( array(), $category_fees );
            $fees[ $cat_id ] = $category_fees;
        }

        return $fees;
    }

	/**
	 * @deprecated 5.16
	 */
	public function sync_fee_plan_with_settings( $plan, $update ) {
		_deprecated_function( __METHOD__, '5.16' );
	}

	/**
	 * @deprecated 5.16
	 */
	public function sync_setting_with_free_plan() {
		_deprecated_function( __METHOD__, '5.16' );
	}
}

}
