<?php
/**
 * View API/class.
 *
 * @since 5.0
 * @package BDP/Includes
 */

/**
 * Class WPBDP__View
 */
class WPBDP__View {

    public function __construct( $args = null ) {
        if ( is_array( $args ) ) {
            foreach ( $args as $k => $v ) {
                $this->{$k} = $v;
            }
        }
    }

    public function get_title() {
        return '';
    }

	/**
	 * Load resources required for the view
	 */
	public function enqueue_resources() {
		// CSS used for plan buttons on the listing page.
		$custom_css = "
		.wpbdp-plan-price input[type=radio]+ label span:before{
			content:'" . esc_attr__( 'Select', 'business-directory-plugin' ) . "';
		}
		.wpbdp-plan-price input[type=radio]:checked + label span:before{
			content:'" . esc_attr__( 'Selected', 'business-directory-plugin' ) . "';
		}";
		wp_add_inline_style( 'wpbdp-base-css', WPBDP_App_Helper::minimize_code( $custom_css ) );

		$this->enqueue_custom_resources();
	}

	/**
	 * @since 5.14.3
	 */
	public function enqueue_custom_resources() {
		// Load custom resources in classes that extend this class.
	 	// Defaults to empty function if not overriden in the child class.
	}

    public function dispatch() {
        return '';
    }


    //
    // API for views. {
    //
    final protected function _http_404() {
        status_header( 404 );
        nocache_headers();

		$template_404 = get_404_template();
		if ( $template_404 ) {
            include $template_404;
        }

        exit;
    }

    final protected function _redirect( $url, $args = array() ) {
		if ( ! empty( $args['doing_ajax'] ) ) {
			wp_send_json_success(
				array(
					'redirect' => $url,
				)
			);
		}

        wp_redirect( $url );
        exit;
    }

    final protected function _render() {
        $args = func_get_args();
        return call_user_func_array( 'wpbdp_x_render', $args );
    }

    final protected function _render_page() {
        $args = func_get_args();
        return call_user_func_array( 'wpbdp_x_render_page', $args );
    }

    final protected function _auth_required( $args = array() ) {
        $defaults = array(
            'test'                => '',
            'login_url'           => wpbdp_url( 'login' ),
            'redirect_on_failure' => true,
            'wpbdp_view'          => '',
            'redirect_query_args' => array(),
			'listing'             => false,
            'doing_ajax'          => false,
        );
        $args     = wp_parse_args( $args, $defaults );

		$test = $args['test'];
        if ( ! $test && method_exists( $this, 'authenticate' ) ) {
            $test = array( $this, 'authenticate' );
        }

        if ( is_callable( $test ) ) {
            $passes = call_user_func( $test );
        } elseif ( 'administrator' == $test ) {
            $passes = current_user_can( 'administrator' );
        } else {
			$passes = is_user_logged_in() && $this->is_listing_owner( $args['listing'] );
        }

        if ( $passes ) {
            return;
        }

        if ( is_user_logged_in() ) {
			$args['redirect_on_failure'] = false;
        }

		if ( ! $args['redirect_on_failure'] ) {
			return wpbdp_render_msg( _x( 'Invalid credentials.', 'views', 'business-directory-plugin' ), 'error' );
		}

		$args['redirect_query_args']['redirect_to'] = rawurlencode(
			add_query_arg(
				$args['redirect_query_args'],
				$args['wpbdp_view'] ? wpbdp_url( $args['wpbdp_view'] ) : apply_filters( 'the_permalink', get_permalink() )
			)
		);

		$login_url = add_query_arg( $args['redirect_query_args'], $args['login_url'] );

		return $this->_redirect( $login_url, $args );
    }

	/**
	 * @since 5.9.2
	 */
	protected function is_listing_owner( $listing ) {
		if ( empty( $listing ) ) {
			return true;
		}
		return $listing->owned_by_user();
	}
}
