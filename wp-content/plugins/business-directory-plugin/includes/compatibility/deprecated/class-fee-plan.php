<?php

/**
 * @deprecated since 5.0. Use {@link WPBDP__Fee_Plan} instead. This is just kept as to not break premium modules for a while.
 */
class WPBDP_Fee_Plan {

    public static function for_category( $category_id ) {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Fee_Plan' );
        return wpbdp_get_fee_plans( array( 'categories' => $category_id, 'enabled' => 'all' ) );
    }

    public static function active_fees_for_category( $category_id ) {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Fee_Plan' );
        return wpbdp_get_fee_plans( array( 'categories' => $category_id, 'enabled' => 1 ) );
    }

    public static function active_fees() {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Fee_Plan' );
        return wpbdp_get_fee_plans();
    }

    public static function get_free_plan() {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Fee_Plan' );
        return wpbdp_get_fee_plan( 'free' );
    }
}

