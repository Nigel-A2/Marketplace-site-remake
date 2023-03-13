<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * @since 5.0
 */
class WPBDP__Admin__Payments_Table extends WP_List_Table {

    /**
     * @var WPBDP__DB__Query_Set $items
     */
    public $items;

    public function __construct() {
        parent::__construct(
			array(
				'singular' => _x( 'payment', 'payments admin', 'business-directory-plugin' ),
				'plural'   => _x( 'payments', 'payments admin', 'business-directory-plugin' ),
				'ajax'     => false,
			)
		);
    }

    public function no_items() {
		echo esc_html_x( 'No payments found.', 'payments admin', 'business-directory-plugin' );
    }

    public function get_current_view() {
		return wpbdp_get_var( array( 'param' => 'status', 'default' => 'all' ) );
    }

    public function get_views() {
        global $wpdb;

        $views_ = array();

        $count = WPBDP_Payment::objects()->count();
        $views_['all'] = array( _x( 'All', 'payments admin', 'business-directory-plugin' ), $count );

        foreach ( WPBDP_Payment::get_stati() as $status => $status_label ) {
            $count = WPBDP_Payment::objects()->filter( array( 'status' => $status ) )->count();
            $views_[ $status ] = array( $status_label, $count );
        }

        $views = array();
        foreach ( $views_ as $view_id => $view_data ) {
            $views[ $view_id ] = sprintf(
				'<a href="%s" class="%s">%s</a> <span class="count">(%s)</span></a>',
				esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments&status=' . $view_id ) ),
				$view_id == $this->get_current_view() ? 'current' : '',
				$view_data[0],
				number_format_i18n( $view_data[1] )
			);
        }

        return $views;
    }

    public function get_columns() {
        $cols = array(
			'listing'    => __( 'Listing', 'business-directory-plugin' ),
            'payment_id' => __( 'ID', 'business-directory-plugin' ),
            'date' => _x( 'Date', 'fees admin', 'business-directory-plugin' ),
            'details' => _x( 'Payment History', 'fees admin', 'business-directory-plugin' ),
            'amount' => __( 'Amount', 'business-directory-plugin' ),
            'status' => _x( 'Status', 'fees admin', 'business-directory-plugin' )
        );

        return $cols;
    }

    public function prepare_items() {
        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $args = array();

        if ( 'all' != $this->get_current_view() )
            $args['status'] = $this->get_current_view();

		$listing_id = wpbdp_get_var( array( 'param' => 'listing' ) );
		if ( ! empty( $listing_id ) ) {
			$args['listing_id'] = absint( $listing_id );
		}

        $this->items = WPBDP_Payment::objects()->filter( $args )->order_by( '-id' );

        if ( ! empty( $_GET['s'] ) ) {
            $s = trim( wpbdp_get_var( array( 'param' => 's' ) ) );

            $this->items = $this->items->filter(
                array(
                    'payer_first_name__icontains' => $s,
                    'payer_last_name__icontains'  => $s,
                    'payer_email__icontains'      => $s,
                    'gateway_tx_id'               => $s
                ),
                false,
                'OR'
            );

            // wpbdp_debug_e( $s, $this->items );
        }

        $this->items = $this->items;
    }

    public function has_items() {
        return $this->items->count() > 0;
    }

    public function column_payment_id( $payment ) {
		return sprintf(
			'<a href="%s">%d</a>',
			esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details&payment-id=' . $payment->id ) ),
			esc_html( $payment->id )
		);
    }

    public function column_date( $payment ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $payment->created_at ) );
    }

    public function column_amount( $payment ) {
        return wpbdp_currency_format( $payment->amount );
    }

	public function column_status( $payment ) {
		$class = 'wpbdp-tag wpbdp-listing-attr-payment-' . esc_attr( $payment->status );
		$value = '<span class="' . esc_attr( $class ) . '">' .
			WPBDP_Payment::get_status_label( $payment->status ) .
			'</span>';

		if ( $payment->is_test ) {
			$value .= ' <span class="wpbdp-tag wpbdp-test-payment">Test</span>';
		}
		return $value;
	}

	public function column_details( $payment ) {
		return '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details&payment-id=' . $payment->id ) ) . '">' .
			esc_html__( 'View Payment', 'business-directory-plugin' ) .
			'</a>';
	}

    public function column_listing( $payment ) {
        $listing = $payment->listing;

        if ( ! $listing )
            return '';

        return '<a href="' . esc_url( $listing->get_admin_edit_link() ) . '">' . esc_html( $listing->get_title() ) . '</a>';
    }

}
