<?php
require_once WPBDP_PATH . 'includes/models/class-payment.php';

/**
 * @since 5.0
 */
class WPBDP__Admin__Payments extends WPBDP__Admin__Controller {

	function _enqueue_scripts() {
		WPBDP__Assets::load_datepicker();
		parent::_enqueue_scripts();
	}

	function index() {
		$_SERVER['REQUEST_URI'] = remove_query_arg( 'listing' );

		if ( 'payment_delete' === wpbdp_get_var( array( 'param' => 'message' ) ) ) {
			wpbdp_admin_message( _x( 'Payment deleted.', 'payments admin', 'business-directory-plugin' ) );
		}

		require_once WPBDP_INC . 'admin/helpers/tables/class-payments-table.php';

		$table = new WPBDP__Admin__Payments_Table();
		$table->prepare_items();

		$listing_id = wpbdp_get_var( array( 'param' => 'listing' ) );
		if ( ! empty( $listing_id ) ) {
			$listing = WPBDP_Listing::get( $listing_id );

			if ( $listing ) {
				wpbdp_admin_message(
					str_replace(
						'<a>',
						'<a href="' . esc_url( remove_query_arg( 'listing' ) ) . '">',
						sprintf(
							_x( 'You\'re seeing payments related to listing: "%1$s" (ID #%2$d). <a>Click here</a> to see all payments.', 'payments admin', 'business-directory-plugin' ),
							esc_html( $listing->get_title() ),
							esc_html( $listing->get_id() )
						)
					)
				);
			}
		}

		return compact( 'table' );
	}

	/**
	 * Used to render the backend payments admin page.
	 * Payment object null is checked in templates/admin/payments-details.tpl.php file.
	 * Adding a redirect here will cause an indefinite loop.
	 */
	public function details() {
		if ( 1 === (int) wpbdp_get_var( array( 'param' => 'message' ) ) ) {
			wpbdp_admin_message( _x( 'Payment details updated.', 'payments admin', 'business-directory-plugin' ) );
		}

		$payment_id = wpbdp_get_var( array( 'param' => 'payment-id' ) );
		$payment    = WPBDP_Payment::objects()->get( $payment_id );
		return compact( 'payment' );
	}

	public function payment_update() {
		$data  = wpbdp_get_var( array( 'param' => 'payment' ), 'post' );
		$nonce = array( 'nonce' => 'payment-' . absint( $data['id'] ) );
		WPBDP_App_Helper::permission_check( 'edit_posts', $nonce );

		$payment = WPBDP_Payment::objects()->get( absint( $data['id'] ) );
		$this->handle_payment_not_found_redirect( $payment );
		$payment->update( $data );
		$payment->save();

		wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details&payment-id=' . $payment->id . '&message=1' ) ) );
		exit;
	}

	public function payment_delete() {
		$payment_id = wpbdp_get_var( array( 'param' => 'payment-id', 'sanitize' => 'absint' ), 'request' );
		$nonce = array( 'nonce' => 'payment-' . $payment_id );
		WPBDP_App_Helper::permission_check( 'edit_posts', $nonce );

		$payment = WPBDP_Payment::objects()->get( $payment_id );
		$this->handle_payment_not_found_redirect( $payment );
		$payment->delete();

		wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wpbdp_admin_payments&message=payment_delete' ) ) );
		exit;
	}

	public function ajax_add_note() {
		WPBDP_App_Helper::permission_check( 'edit_posts' );
		check_ajax_referer( 'wpbdp_ajax', 'nonce' );

		$payment_id = wpbdp_get_var( array( 'param' => 'payment_id', 'sanitize' => 'absint' ), 'post' );
		$payment    = WPBDP_Payment::objects()->get( $payment_id );
		$text       = trim( wpbdp_get_var( array( 'param' => 'note', 'sanitize' => 'sanitize_textarea_field' ), 'post' ) );

		$res = new WPBDP_AJAX_Response();

		if ( ! $payment || ! $text ) {
			$res->send_error();
		}

		$note = wpbdp_insert_log(
			array(
				'log_type'  => 'payment.note',
				'message'   => $text,
				'actor'     => 'user:' . get_current_user_id(),
				'object_id' => $payment_id,
			)
		);
		if ( ! $note ) {
			$res->send_error();
		}

		$res->add( 'note', $note );
		$res->add( 'html', wpbdp_render_page( WPBDP_PATH . 'templates/admin/payments-note.tpl.php', compact( 'note', 'payment_id' ) ) );
		$res->send();
	}

	public function ajax_delete_note() {
		$nonce = array( 'nonce' => 'wpbdp_ajax' );
		WPBDP_App_Helper::permission_check( 'edit_posts', $nonce );

		$payment_id = wpbdp_get_var( array( 'param' => 'payment_id', 'sanitize' => 'absint' ) );
		$note_key   = trim( wpbdp_get_var( array( 'param' => 'note', 'sanitize' => 'sanitize_textarea_field' ) ) );

		$res = new WPBDP_AJAX_Response();

		$note = wpbdp_get_log( $note_key );
		if ( 'payment.note' != $note->log_type || $payment_id != $note->object_id ) {
			$res->send_error();
		}

		wpbdp_delete_log( $note_key );

		$res->add( 'note', $note );
		$res->send();
	}

	/**
	 * Redirect to error page if the payment object id not found.
	 *
	 * @param WPBDP_Payment|null $payment The payment object pulled from the database. If the object does not exist, null is returned.
	 *
	 * @since 5.14.3
	 */
	private function handle_payment_not_found_redirect( $payment ) {
		if ( ! $payment ) {
            // Not found.
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details' ) ) );
			exit;
		}
	}

}
