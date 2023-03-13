<?php
require_once WPBDP_INC . 'abstracts/class-view.php';


class WPBDP__Views__Request_Access_Keys extends WPBDP__View {

    public function dispatch() {
        if ( ! wpbdp_get_option( 'enable-key-access' ) ) {
            return wpbdp_render_msg(
                str_replace(
                    '<a>',
                    '<a href="' . esc_url( wpbdp_get_page_link( 'main' ) ) . '">',
                    _x( 'Did you mean to <a>access the Directory</a>?', 'request_access_keys', 'business-directory-plugin' )
                ),
                'error'
            );
        }

        $nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );
        $errors = array();

        if ( $nonce && wp_verify_nonce( $nonce, 'request_access_keys' ) )
            return $this->listings_and_access_keys();

        return $this->_render( 'send-access-keys', array( 'redirect_to' => wpbdp_get_var( array( 'param' => 'redirect_to' ) ) ) );
    }

    public function listings_and_access_keys() {
        $email = wpbdp_get_var( array( 'param' => 'email', 'sanitize' => 'sanitize_email' ), 'post' );

        try {
            $message_sent = $this->get_access_keys_sender()->send_access_keys( $email );
        } catch ( Exception $e ) {
            return wpbdp_render_msg( $e->getMessage(), 'error' );
        }

        if ( $message_sent ) {
            $html  = '';
            $html .= wpbdp_render_msg( _x( 'Access keys have been sent to your e-mail address.', 'request_access_keys', 'business-directory-plugin' ) );

            if ( ! empty( $_POST['redirect_to'] ) ) {
                $html .= '<p>';
				$html .= '<a href="' . esc_url( wpbdp_get_var( array( 'param' => 'redirect_to' ), 'post' ) ) . '">';
                $html .= _x( '← Return to previous page', 'request_access_keys', 'business-directory-plugin' );
                $html .= '</a>';
                $html .= '<p>';
            }

            return $html;
        }
    }

    public function get_access_keys_sender() {
        return new WPBDP__Access_Keys_Sender();
    }
}
