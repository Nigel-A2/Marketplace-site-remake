<?php
/**
 * Establishes API connection to ConvertKit App
 */
class ConvertKit {
	protected $api_key;
	protected $api_version  = 3;
	protected $api_url_base = 'https://api.convertkit.com/';
	protected $resources    = array();
	protected $markup       = array();
	static $response     = array();

	/**
	  * Constructor for ConvertKit instance
	  *
	  * @param String $api_key ConvertKit API Key
	  */
	public function __construct($api_key) {
		$this->api_key = $api_key;
		$this->api_url_base .= 'v' . $this->api_version . '/';
	}

	/**
	 * Check if the API connection is authenticated
	 *
	 * @param string $resource
	 * @return boolean
	 */
	public function is_authenticated() {
		$api_response = $this->_get_api_response('forms');

		self::$response = $api_response;
		if (is_wp_error($api_response) || isset($api_response['error']) || isset($api_response['error_message'])) {
			return false;
		}
		return true;
	}

	/**
	* Gets a resource index
	*
	* GET /{$resource}/
	*
	* @param  string $resource Resource type
	* @return object           API response
	*/
	public function get_resources($resource) {

		if(!array_key_exists($resource, $this->resources)) {
		  $api_response = $this->_get_api_response($resource);

		  self::$response = $api_response;
		  if (is_wp_error($api_response) || isset($api_response['error']) || isset($api_response['error_message'])) {
			$this->resources[$resource] = array();
		  } else {
		  	$this->resources[$resource] = $api_response;
		  }
		}

		return $this->resources[$resource];
	}

	/**
	* Adds a subscriber to a form
	*
	* @param string $form_id Form ID
	* @param array  $options Array of user data
	*/
	public function form_subscribe($form_id, $options) {
		$request = sprintf('forms/%s/subscribe', $form_id);
		$args    = array(
		  'email' => $options['email'],
		  'first_name' => isset( $options['fname'] ) ? $options['fname'] : '',
		);
		return $this->make_request($request, 'POST', $args);
	}

	/**
	* Unsubscribes a subscriber from a form
	*
	* @param string $form_id Resource ID
	* @param array  $options Array of user data
	*/
	public function form_unsubscribe($form_id, $options) {
		$request = sprintf('forms/%s/unsubscribe', $form_id);
		$args    = array(
			'email' => $options['email']
		);
		return $this->make_request($request, 'POST', $args);
	}

	/**
	 * Get API response
	 * @param  string $path
	 * @return array|object
	 */
	private function _get_api_response($path = '') {
		$args = array('api_key' => $this->api_key);
		$url = add_query_arg($args, path_join($this->api_url_base, $path));
		$response = wp_remote_get($url);

		if(is_wp_error($response)) {
			$data = $response;
		} else {
			$data = json_decode(wp_remote_retrieve_body($response), true);
		}
		return $data;
	}

	/**
	* Make a request to the ConvertKit API
	*
	* @param  string $request Request string
	* @param  string $method  HTTP Method
	* @param  array  $args    Request arguments
	* @return object          Response object
	*/
	public function make_request($request, $method = 'GET', $args = array()) {
		$url = $this->build_request_url($request, $args);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		$results = curl_exec($ch);
		curl_close($ch);

		return json_decode($results);
	}

	/**
	* Build the full request URL
	*
	* @param  string $request Request path
	* @param  array  $args    Request arguments
	* @return string          Request URL
	*/
	public function build_request_url($request, array $args) {
		return $this->api_url_base . $request . '?' . http_build_query($this->filter_request_arguments($args));
	}

	/**
	* Merge default request arguments with those of this request
	*
	* @param  array  $args Request arguments
	* @return array        Request arguments
	*/
	public function filter_request_arguments($args = array()) {
		return array_merge($args, array('api_key' => $this->api_key));
	}
}
