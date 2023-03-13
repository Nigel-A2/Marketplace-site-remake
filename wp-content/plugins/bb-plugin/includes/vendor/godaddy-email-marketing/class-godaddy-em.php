<?php

/**
 * GoDaddy Email Marketing Dispatcher.
 *
 * A modified version from GoDaddy Email Marketing plugin
 * (https://wordpress.org/plugins/godaddy-email-marketing-sign-up-forms/)
 *
 * @since 1.0
 */
class GoDaddyEM {

	/**
	 * API Credentials
	 * @var string
	 */
	private static $api_username;
    private static $api_key;

    /**
     * Base API URI
     */
    private static $api_base_url = 'https://gem.godaddy.com/';

	/**
	 * HTTP response codes
	 *
	 * @var array
	 */
	private static $ok_codes = array( 200, 304 );

	/**
	 * Constructor
	 *
	 * @param string  $email	The username.
	 * @param string  $api_key 	The API key.
	 */
	public function __construct($username, $api_key) {

		self::$api_username = $username;
		self::$api_key = $api_key;
	}

	/**
	 * Gets and sets the forms.
	 *
	 * @param string $username The username.
	 * @param string $api_key
	 *
	 * @return string $api_key  The API key.
	 */
	public static function fetch_forms( $username = '', $api_key = '' ) {
		if ( ! $username && ! $api_key ) {
			$username = self::$api_username;
			$api_key  = self::$api_key;
		}

		if ( ! $username || ! $api_key ) {
			return false;
		}

		$auth = array(
			'username' => $username,
			'api_key'  => $api_key,
		);

		// Prepare the URL that includes our credentials.
		$response = wp_remote_get( self::get_method_url( 'forms', false, $auth ), array(
			'timeout' => 10,
		) );

		// Credentials are incorrect.
		if ( ! in_array( wp_remote_retrieve_response_code( $response ), self::$ok_codes, true ) ) {
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		return $data;
	}

	/**
	 * Gets the forms.
	 *
	 * @return array|false The form fields array or false.
	 */
	public static function get_forms() {

		if ( ! self::$api_username ) {
			return false;
		}

		$data = self::fetch_forms();

		return $data;
	}

	/**
	 * Gets and sets the form fields.
	 *
	 * @param string $form_id Form ID.
	 * @return false|object The form fields JSON object or false.
	 */
	public static function get_fields( $form_id ) {

		// Fields are not cached. fetch and cache.
		$response = wp_remote_get( self::get_method_url( 'fields', array(
			'id' => $form_id,
		) ) );

		// Was there an error, connection is down? bail and try again later.
		if ( ! self::is_response_ok( $response ) ) {
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		return $data;
	}

	/**
	 * Return the API base URL.
	 *
	 * @param  string $path (optional)
	 *
	 * @return string
	 */
	public static function get_api_base_url( $path = '' ) {

		return self::$api_base_url . $path;
	}

	/**
	 * Utility function for getting a URL for various API methods
	 *
	 * @param string $method The short of the API method.
	 * @param array  $params Extra parameters to pass on with the request.
	 * @param bool   $auth   Autentication array including API key and username.
	 *
	 * @return string The final URL to use for the request
	 */
	public static function get_method_url( $method, $params = array(), $auth = false ) {
		$auth = $auth ? $auth : array(
			'username' => self::$api_username,
			'api_key' => self::$api_key
		);

		$path = '';

		switch ( $method ) {

			case 'forms' :
				$path = add_query_arg( $auth, 'signups.json' );
				break;
			case 'fields' :
				$path = add_query_arg( $auth, 'signups/' . $params['id'] . '.json' );
				break;
			case 'account' :
				$path = add_query_arg( $auth, 'user/account_status' );
				break;
		}

		return self::get_api_base_url( $path );
	}

	/**
	 * Check for an OK response.
	 *
	 * @param array $request HTTP response by reference.
	 * @return bool
	 */
	public static function is_response_ok( $request ) {
		return ( ! is_wp_error( $request ) && in_array( wp_remote_retrieve_response_code( $request ), self::$ok_codes, true ) );
	}

	/**
	 * Check if an account exists from GoDaddy
	 *
	 * @return bool
	 */
	public static function is_account_ok() {
		// Request account details
		$response = wp_remote_get( self::get_method_url( 'account' ) );

		// Was there an error, connection is down? bail and try again later.
		if ( ! self::is_response_ok( $response ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add new subscriber
	 *
	 * @param array $data	An array of subscriber data
	 */
	public static function add_subscriber( $data ) {
		if ( empty( $data ) ) {
			return false;
		}

		if ( ! self::$api_username || ! self::$api_key ) {
			return false;
		}

		if ( isset( $data[ 'form_id' ] ) && isset( $data[ 'email' ] ) ) {
			$form = self::get_fields( $data[ 'form_id' ] );

			$submit_data = array(
				'username' 		=> self::$api_username,
				'api_key' 		=> self::$api_key,
				'integration' 	=> 'WordPress',
				'signup[email]'	=> $data['email']
			);

			if ( isset( $data[ 'first_name' ] ) ) {
				$submit_data[ 'signup[first_name]' ] = $data['first_name'];
			}

			if ( isset( $data[ 'last_name' ] ) ) {
				$submit_data[ 'signup[last_name]' ] = $data['last_name'];
			}

			// Prepare the URL that includes our credentials.
			$response = wp_remote_post( $form->submit, array(
				'method' => 'POST',
				'timeout' => 10,
				'body' => $submit_data
			) );

			// Credentials are correct.
			if ( self::is_response_ok( $response ) ) {
				return true;
			}
		}

		return false;
	}
}
