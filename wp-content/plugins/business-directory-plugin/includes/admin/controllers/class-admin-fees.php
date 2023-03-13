<?php
/**
 * @since 5.0
 */
class WPBDP__Admin__Fees extends WPBDP__Admin__Controller {

    function __construct() {
        parent::__construct();
        $this->api = $this->wpbdp->fees;
    }

    /**
     * @override
     */
    function _enqueue_scripts() {
        switch ( $this->current_view ) {
			case 'add-fee':
			case 'edit-fee':
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'wpbdp-js-select2-css' );

				wp_enqueue_script(
					'wpbdp-admin-fees-js',
					WPBDP_ASSETS_URL . 'js/admin-fees.min.js',
					array( 'wp-color-picker', 'wpbdp-js-select2' ),
					WPBDP_VERSION,
					true
				);

				break;
        }

        if ( ! in_array( $this->current_view, array( 'add-fee', 'edit-fee' ), true ) )
            return;
    }

    function index() {
		require_once WPBDP_INC . 'admin/helpers/tables/class-fees-table.php';

        $table = new WPBDP__Admin__Fees_Table();
        $table->prepare_items();

        $order_options = array();
		$labels        = array(
			'label'  => _x( 'Label', 'fees order', 'business-directory-plugin' ),
			'amount' => __( 'Amount', 'business-directory-plugin' ),
			'days'   => _x( 'Duration', 'fees order', 'business-directory-plugin' ),
			'images' => __( 'Images', 'business-directory-plugin' ),
			'custom' => _x( 'Custom Order', 'fees order', 'business-directory-plugin' ),
		);
		foreach ( $labels as $k => $l ) {
            $order_options[ $k ] = $l;
        }

        return array(
            'table' => $table,
            'order_options' => $order_options,
			'current_order' => wpbdp_get_option( 'fee-order' ),
			'gateways'      => $this->available_gateways(),
        );
    }

	/**
	 * Get a list of gateways that aren't currently being used.
	 *
	 * @since 6.0
	 */
	private function available_gateways() {
		$modules = array(
			array( 'stripe', 'stripe-payment-module', 'Stripe' ),
			array( 'paypal', 'paypal-gateway-module', 'PayPal' ),
			array( 'payfast', 'payfast-payment-module', 'PayFast' ),
		);

		$gateways    = array();
		$modules_obj = wpbdp()->modules;
		foreach ( $modules as $mod_info ) {
			if ( ! $modules_obj->is_loaded( $mod_info[0] ) ) {
				$mod_info['link'] = wpbdp_admin_upgrade_link( 'get-gateway', '/downloads/' . $mod_info[1] );
				$mod_info['cta']  = __( 'Upgrade', 'business-directory-plugin' );
				$gateways[]       = $mod_info;
			}
		}

		if ( ! wpbdp_payments_possible() ) {
			$gateways[] = array(
				'',
				'authorize-net-payment-module',
				'Authorize.net',
				'link' => admin_url( 'admin.php?page=wpbdp_settings&tab=payment' ),
				'cta'  => __( 'Set Up', 'business-directory-plugin' ),
			);
		}

		return $gateways;
	}

    function add_fee() {
        return $this->insert_or_update_fee( 'insert' );
    }

    function edit_fee() {
        return $this->insert_or_update_fee( 'update' );
    }

    private function insert_or_update_fee( $mode ) {
		if ( ! empty( $_POST ) ) {
			$nonce = array( 'nonce' => 'wpbdp-fees' );
			WPBDP_App_Helper::permission_check( 'edit_posts', $nonce );
		}

        if ( ! empty( $_POST['fee'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$posted_values = stripslashes_deep( $_POST['fee'] );
			$posted_values = $this->sanitize_posted_values( $posted_values );

			$cat_limit = wpbdp_get_var(
				array(
					'param'   => 'limit_categories',
					'default' => 0,
				),
				'post'
			);
			if ( 0 === intval( $cat_limit ) ) {
                $posted_values['supported_categories'] = 'all';
			}

			if ( ! isset( $posted_values['sticky'] ) ) {
                $posted_values['sticky'] = 0;
			}

			if ( ! isset( $posted_values['recurring'] ) ) {
                $posted_values['recurring'] = 0;
			}
			$images = (int) $posted_values['images'];
        } else {
            $posted_values = array();
			$images = false;
        }

		if ( 'insert' === $mode ) {
            $fee = new WPBDP__Fee_Plan( $posted_values );
			$images_changed = false;
        } else {
			$fee = $this->get_or_die();
			$images_changed = $images !== false && (int) $fee->images !== $images;
        }

		if ( ! $posted_values ) {
			return array( 'fee' => $fee );
		}

		if ( $fee->exists() ) {
			$result = $fee->update( $posted_values );
		} else {
			$result = $fee->save();
		}

		if ( ! is_wp_error( $result ) ) {
			if ( 'insert' === $mode ) {
				wpbdp_admin_message( __( 'Plan added.', 'business-directory-plugin' ) );
			} elseif ( $images_changed ) {
				$this->show_update_listing_msg( $fee );
			} else {
				wpbdp_admin_message( __( 'Plan updated.', 'business-directory-plugin' ) );
			}
		} else {
			foreach ( $result->get_error_messages() as $msg ) {
				wpbdp_admin_message( $msg, 'error' );
			}
		}

        return array( 'fee' => $fee );
    }

	/**
	 * @since 5.15.3
	 */
	private function show_update_listing_msg( $fee ) {
		$message = __( 'Plan updated.', 'business-directory-plugin' );

		$total_listings = $fee->count_listings();
		if ( ! $total_listings ) {
			wpbdp_admin_message( $message );
			return;
		}

		$data = wp_json_encode(
			array(
				'plan_id' => $fee->id,
				'nonce'   => wp_create_nonce( 'wpbdp_ajax' ),
				'action'  => 'wpbdp_admin_ajax',
				'handler' => 'fees__update_listing_plan'
			)
		);

		wpbdp_admin_message(
			$message . ' ' .
			sprintf(
				__( '%1$sClick here to update image limits%2$s of %3$s existing listings.', 'business-directory-plugin' ),
				'<a class="wpbdp-update-plan-listings wpbdp-admin-ajax" data-confirm="' . esc_attr__( 'Update listing image limits?', 'business-directory-plugin' ) . '" data-target=".wpbdp-plan-updated" data-ajax="' . esc_attr( $data ) . '" href="#">',
				'</a>',
				$total_listings
			),
			'updated wpbdp-plan-updated is-dismissible'
		);
	}

	/**
	 * Ajax action to update listing plan.
	 *
	 * @since 5.15.3
	 */
	public function ajax_update_listing_plan() {
		WPBDP_App_Helper::permission_check( 'edit_posts' );
		check_ajax_referer( 'wpbdp_ajax', 'nonce' );

		$plan_id = wpbdp_get_var(
			array(
				'param'    => 'plan_id',
				'sanitize' => 'absint',
			),
			'post'
		);
		$fee     = wpbdp_get_fee_plan( $plan_id );
		$res     = new WPBDP_AJAX_Response();
		if ( ! $fee ) {
			$res->send_error( __( 'Plan not found.', 'business-directory-plugin' ) );
		}

		$this->update_listing_images( $fee );
		$res->set_message( __( 'Plan listings updated.', 'business-directory-plugin' ) );
		$res->send();
	}

	/**
	 * Update the listing images.
	 * This updates all listings that have the same fee id.
	 *
	 * @param object $fee The fee
	 *
	 * @since 5.15.3
	 */
	private function update_listing_images( $fee ) {
		global $wpdb;
		WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );
		$wpdb->update( $wpdb->prefix . 'wpbdp_listings', array( 'fee_images' => $fee->images ), array( 'fee_id' => $fee->id ) );
	}

	/**
	 * Sanitize each field in the fee form.
	 *
	 * @since 5.11.2
	 */
	private function sanitize_posted_values( $posted_values ) {
		$sanitizing = $this->sanitize_mapping();
		foreach ( $posted_values as $k => $v ) {
			$sanitize = isset( $sanitizing[ $k ] ) ? $sanitizing[ $k ] : 'sanitize_text_field';
			wpbdp_sanitize_value( $sanitize, $posted_values[ $k ] );
		}
		return $posted_values;
	}

	/**
	 * This shows how to sanitize each field in the fee form.
	 *
	 * @since 5.11.2
	 */
	private function sanitize_mapping() {
		return array(
			'description' => 'wp_kses_post',
			'days'        => 'absint',
			'images'      => 'absint',
		);
	}

	/**
	 * @since 5.9
	 */
	private function get_or_die() {
		$fee = wpbdp_get_fee_plan( wpbdp_get_var( array( 'param' => 'id' ) ) );

		if ( ! $fee ) {
			wp_die();
		}
		return $fee;
	}

    function delete_fee() {
		$nonce = array( 'nonce' => 'delete-fee' );
		WPBDP_App_Helper::permission_check( 'manage_categories', $nonce );

		$fee = $this->get_or_die();

		if ( $fee->delete() ) {
			wpbdp_admin_message( sprintf( _x( 'Plan "%s" deleted.', 'fees admin', 'business-directory-plugin' ), $fee->label ) );
		}

		return $this->_redirect( 'index' );
    }

    function toggle_fee() {
		$fee = $this->get_or_die();
		$enabled_plans = WPBDP_Fees_API::get_enabled_plans();
		if ( $enabled_plans > 1 || ! $fee->enabled ) {
			$fee->enabled = ! $fee->enabled;
			$fee->save();
			wpbdp_admin_message( $fee->enabled ? _x( 'Plan enabled.', 'fees admin', 'business-directory-plugin' ) : _x( 'Plan disabled.', 'fees admin', 'business-directory-plugin' ) );
		} else {
			wpbdp_admin_message( __( 'Cannot disable plan. At least one plan should be enabled', 'business-directory-plugin' ), 'error' );
		}
        return $this->_redirect( 'index' );
    }

}
