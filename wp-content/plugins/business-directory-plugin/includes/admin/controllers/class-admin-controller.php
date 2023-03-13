<?php
/**
 * @since 5.0
 */
class WPBDP__Admin__Controller {

    protected $controller_id = '';
    protected $wpbdp;
    protected $current_view = '';


    function __construct() {
        $this->wpbdp = $GLOBALS['wpbdp'];
        $this->controller_id = str_replace( 'wpbdp__admin__', '', WPBDP_Utils::normalize( get_class( $this ) ) );
    }

    function _enqueue_scripts() {
        if ( file_exists( WPBDP_PATH . 'assets/js/admin-' . $this->controller_id . '.js' ) ) {
            wp_enqueue_script(
                'wpbdp-' . $this->controller_id . '-js',
                WPBDP_ASSETS_URL . 'js/admin-' . $this->controller_id . '.js',
                array( 'wpbdp-admin-js' ),
                WPBDP_VERSION,
				true
            );
        }
    }

    function _ajax_dispatch() {
		$handler = trim( wpbdp_get_var( array( 'param' => 'handler' ), 'request' ) );
        $parts = explode( '__', $handler );
        $controller_id = $parts[0];
        $function = isset( $parts[1] ) ? $parts[1] : '';

        if ( method_exists( $this, 'ajax_' . $function ) )
            return call_user_func( array( $this, 'ajax_' . $function ) );
    }

    function _dispatch() {
		if ( empty( $this->current_view ) ) {
			$this->current_view = wpbdp_get_var(
				array(
					'param'   => 'wpbdp-view',
					'default' => 'index',
				)
			);
		}

        $this->current_view = WPBDP_Utils::normalize( $this->current_view );

        $result = false;
        $output = '';

        $callback = ( false !== strpos( $this->current_view, '-' ) ? str_replace( '-', '_', $this->current_view ) : $this->current_view );

        // Remove query args.
        $orig_uri               = wpbdp_get_server_value( 'REQUEST_URI' );
        $_SERVER['REQUEST_URI'] = remove_query_arg( array( 'wpbdp-view', 'id' ), $orig_uri );

        if ( method_exists( $this, $callback ) )
            $result = call_user_func( array( $this, $callback ) );

        if ( is_array( $result ) ) {
            $template = WPBDP_PATH . 'templates/admin/' . $this->controller_id . '-' . $this->current_view . '.tpl.php';

			if ( ! file_exists( $template ) ) {
				$output = json_encode( $result );
			} else {
				$output = wpbdp_render_page( $template, $result );
			}
        } else {
            $output = $result;
        }

        $_SERVER['REQUEST_URI'] = $orig_uri;

        echo $output;
    }

    function _redirect( $view_or_url ) {
        $this->current_view = $view_or_url;
        return $this->_dispatch();
    }

    function _confirm_action( $args = array() ) {
        $defaults = array(
			'title'       => __( 'Are you sure you want to do this?', 'business-directory-plugin' ),
			'cancel_url'  => '',
            'cancel_text' => _x( 'No, go back', 'admin confirm', 'business-directory-plugin' ),
            'submit_text' => _x( 'Yes, I\'m sure', 'admin confirm', 'business-directory-plugin' ),
            'explanation' => ''
        );
        $args = wp_parse_args( $args, $defaults );
		$nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );

        if ( $nonce && wp_verify_nonce( $nonce, 'confirm ' . md5( $args['title'] ) ) )
            return array( true, '' );

        return array( false, wpbdp_render_page( WPBDP_PATH . 'templates/admin/confirm-page.tpl.php', $args ) );
    }

}
