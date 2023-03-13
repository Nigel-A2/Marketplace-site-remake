<?php
/**
 * @package WPBDP\Admin\Upgrades\Migrations
 */

/**
 * Migration for DB version 18.5
 */
class WPBDP__Migrations__18_5 extends WPBDP__Migration {

	/**
	 * We scan all enabled plans first for this migration and disable
	 * the plans that shouldn't be displayed.
	 * If payments were enabled, we disable the default free plan.
	 * If payments were disabled, we disabled the paid plans.
	 *
	 * @since 5.17
	 */
	public function migrate() {
		global $wpdb;
		$payments_on  = wpbdp_get_option( 'payments-on' );
		$sql          = "SELECT id, amount, tag FROM {$wpdb->prefix}wpbdp_plans WHERE enabled != 0";
		$active_plans = $wpdb->get_results( $sql );
		$to_disable   = array();

		if ( ! $active_plans ) {
			return;
		}

		foreach ( $active_plans as $plan ) {
			if ( ! $payments_on && $plan->amount > 0.0 ) {
				// Disable any paid plan if payments are off.
				$to_disable[] = $plan->id;
			} elseif ( $payments_on && 'free' === $plan->tag ) {
				// Disable the default plan since it was hidden before fee changes.
				$to_disable[] = $plan->id;
			}
		}

		if ( empty( $to_disable ) ) {
			return;
		}

		$sql = "UPDATE {$wpdb->prefix}wpbdp_plans SET enabled = 0 WHERE id IN(" . implode( ', ', array_fill( 0, count( $to_disable ), '%d' ) ) . ')';
		// Call $wpdb->prepare passing the values of the array as separate arguments.
		$query = call_user_func_array( array( $wpdb, 'prepare' ), array_merge( array( $sql ), $to_disable ) );
		$wpdb->query( $query );
	}
}
