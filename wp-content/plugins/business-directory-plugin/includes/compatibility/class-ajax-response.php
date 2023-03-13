<?php

/**
 * @deprecated since 5.0. Use `wp_send_json()` family of functions.
 */
class WPBDP_AJAX_Response {
    public $success = true;
    public $error = '';
    public $message = '';
    public $data = array();

	/**
	 * @todo Show deprecated message.
	 */
	public function __construct() {
		//_deprecated_function( __METHOD__, '5.0', 'wp_send_json' );
	}

    public function add( $k, $v ) {
        $this->data[ $k ] = $v;
    }

    public function set_message( $s ) {
        $this->message = $s;
    }

    public function send_error( $error = null ) {
        if ( $error )
            $this->error = $error;

        $this->success = false;
        $this->message = '';
        $this->data = null;

        $this->send();
    }

    public function send() {
        $response = array();
        $response['success'] = $this->success;

        if ( ! $this->success ) {
            $response['error'] = $this->error ? $this->error : 'Unknown error';
        } else {
            $response['data'] = $this->data;
            $response['message'] = $this->message;
        }

        print json_encode( $response );
        wp_die();
    }
}
