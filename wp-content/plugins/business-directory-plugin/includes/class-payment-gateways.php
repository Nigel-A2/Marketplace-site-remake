<?php
/**
 * Class Payment Gateways
 *
 * @package BDP
 */

/**
 * @since 5.0
 */
class WPBDP__Payment_Gateways {

    private $gateways = array();


    public function __construct() {
        add_action( 'wpbdp_modules_loaded', array( $this, 'load_gateways' ) );
        add_action( 'wpbdp_loaded', array( $this, '_execute_listener' ) );
        add_action( 'wpbdp_register_settings', array( $this, '_add_gateway_settings' ) );
        add_action( 'wpbdp_admin_notices', array( $this, '_admin_warnings' ) );
    }

    public function load_gateways() {
        $gateways = array();

        // Add Authorize.net by default.
        require_once WPBDP_PATH . 'includes/gateways/class-gateway-authorize-net.php';
        $gateways[] = new WPBDP__Gateway__Authorize_Net();

        // Allow modules to add gateways.
        $gateways = apply_filters( 'wpbdp_payment_gateways', $gateways );

        foreach ( $gateways as $gateway_ ) {
            $gateway = is_string( $gateway_ ) ? new $gateway_() : $gateway_;

            $this->gateways[ $gateway->get_id() ] = $gateway;
        }
    }

    public function _execute_listener() {
		$listener_id = wpbdp_get_var( array( 'param' => 'wpbdp-listener' ) );
        if ( ! $listener_id ) {
            return;
        }

        if ( ! $this->can_use( $listener_id ) ) {
            wp_die();
        }

        $gateway = $this->get( $listener_id );
        $gateway->process_postback();
        exit;
    }

    public function get_available_gateways( $conditions = array() ) {
        $res = array();

        foreach ( $this->gateways as $gateway ) {
            if ( $gateway->is_enabled() ) {
                if ( $conditions ) {
                    if ( isset( $conditions['currency_code'] ) && ! $gateway->supports_currency( $conditions['currency_code'] ) ) {
                        continue;
                    }
                }

                $res[ $gateway->get_id() ] = $gateway;
            }
        }

        return $res;
    }

    public function can_use( $gateway_id ) {
        return isset( $this->gateways[ $gateway_id ] ) && $this->gateways[ $gateway_id ]->is_enabled();
    }

    public function get( $gateway_id ) {
        if ( isset( $this->gateways[ $gateway_id ] ) ) {
            return $this->gateways[ $gateway_id ];
        }

        return false;
    }

    public function can_pay() {
        return count( $this->get_available_gateways() ) > 0;
    }

    public function _add_gateway_settings( $api ) {
        foreach ( $this->gateways as $gateway ) {
            wpbdp_register_settings_group( 'gateway_' . $gateway->get_id(), $gateway->get_title(), 'payment', array( 'desc' => $gateway->get_settings_text() ) );
            wpbdp_register_setting(
                array(
                    'id'           => $gateway->get_id(),
                    'name'         => sprintf( _x( 'Enable %s?', 'payment-gateways', 'business-directory-plugin' ), $gateway->get_title() ),
                    'type'         => 'toggle',
                    'default'      => false,
                    'group'        => 'gateway_' . $gateway->get_id(),
                )
            );
            foreach ( $gateway->get_settings() as $setting ) {
                $setting                 = array_merge( $setting, array( 'group' => 'gateway_' . $gateway->get_id() ) );
                $setting['id']           = $gateway->get_id() . '-' . $setting['id'];
                $setting['requirements'] = array( $gateway->get_id() );

                wpbdp_register_setting( $setting );
            }
        }
    }

    public function _admin_warnings() {
		$page        = wpbdp_get_var( array( 'param' => 'page' ) );
		$page_tab    = wpbdp_get_var( array( 'param' => 'tab' ) );
		$is_settings = 'wpbdp_settings' === $page;
		$is_plans    = 'wpbdp-admin-fees' === $page;
		$is_payments = ( $is_settings && 'payment' === $page_tab );
		if ( ! $is_settings && ! $is_plans ) {
            return;
        }

		// Check if there are premium fees.
		if ( ! WPBDP_Fees_API::has_paid_plans() ) {
			return;
		}

        $at_least_one_gateway = false;
        foreach ( $this->gateways as $gateway ) {
            if ( $gateway->is_enabled( true ) ) {
                $at_least_one_gateway = true;
			} elseif ( $gateway->is_enabled( false ) && $is_settings ) {
                $errors = rtrim( '&#149; ' . implode( ' &#149; ', $gateway->validate_settings() ), '.' );

                $msg  = _x( 'The <gateway> gateway is enabled but not properly configured. The gateway won\'t be available until the following problems are fixed: <problems>.', 'payment-gateways', 'business-directory-plugin' );
                $msg .= '<br />';
                $msg .= _x( 'Please check the <link>payment settings</link>.', 'payment-gateways', 'business-directory-plugin' );

                $msg = str_replace( '<gateway>', '<b>' . $gateway->get_title() . '</b>', $msg );
                $msg = str_replace( '<problems>', '<b>' . $errors . '</b>', $msg );
				$msg = str_replace( array( '<link>', '</link>' ), array( '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_settings&tab=payment' ) ) . '">', '</a>' ), $msg );

                wpbdp_admin_message( $msg, 'notice-error is-dismissible', array( 'dismissible-id' => 'no_gateway_' . $gateway->get_title() ) );
            }
        }

		if ( ! $at_least_one_gateway && ( $is_plans || $is_payments ) ) {
			$msg = __( 'You have paid plans but no payment gateway. Go to %1$sSettings - Payment%2$s to set up a gateway. Until you do this, only free plans will be available.', 'business-directory-plugin' );
			$msg = sprintf( $msg, '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_settings&tab=payment' ) ) . '">', '</a>' );
			wpbdp_admin_message( $msg, 'notice-error is-dismissible', array( 'dismissible-id' => 'no_gateway' ) );
		}
    }
}
