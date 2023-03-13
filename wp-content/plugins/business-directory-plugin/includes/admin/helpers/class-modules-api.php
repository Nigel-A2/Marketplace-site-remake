<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class WPBDP_Modules_API {

	protected $license = '';
	protected $cache_key = '';
	protected $cache_timeout = '+6 hours';

	/**
	 * @since 5.10
	 */
	public function __construct( $license = null ) {
		$this->set_license( $license );
		$this->set_cache_key();
	}

	/**
	 * @since 5.10
	 */
	private function set_license( $license ) {
		if ( $license === null ) {
			$pro_license = $this->get_pro_license();
			if ( ! empty( $pro_license ) ) {
				$license = $pro_license;
			}
		}
		$this->license = $license;
	}

	/**
	 * @since 5.10
	 * @return string
	 */
	public function get_license() {
		return $this->license;
	}

	/**
	 * @since 5.10
	 */
	protected function set_cache_key() {
		$this->cache_key = 'wpbdp_addons_l' . ( empty( $this->license ) ? '' : md5( $this->license ) );
	}

	/**
	 * @since 5.10
	 * @return string
	 */
	public function get_cache_key() {
		return $this->cache_key;
	}

	/**
	 * @since 5.10
	 * @return array
	 */
	public function get_api_info() {
		$url = $this->api_url();
		if ( ! empty( $this->license ) ) {
			$url .= '?l=' . urlencode( base64_encode( $this->license ) );
		}

		$addons = $this->get_cached();
		if ( ! empty( $addons ) ) {
			return $addons;
		}

		// We need to know the version number to allow different downloads.
		$agent = 'business-directory-plugin/' . WPBDP_VERSION;

		$response = wp_remote_get(
			$url,
			array(
				'timeout'    => 25,
				'user-agent' => $agent . '; ' . get_bloginfo( 'url' ),
			)
		);
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$addons = $response['body'];
			if ( ! empty( $addons ) ) {
				$addons = json_decode( $addons, true );

				foreach ( $addons as $k => $addon ) {
					if ( ! isset( $addon['categories'] ) ) {
						continue;
					}
					$cats = array_intersect( $this->skip_categories(), $addon['categories'] );
					if ( ! empty( $cats ) ) {
						unset( $addons[ $k ] );
					}
				}

				$this->set_cached( $addons );
			}
		}

		if ( empty( $addons ) ) {
			return array();
		}

		return $addons;
	}

	/**
	 * @since 5.10
	 */
	protected function api_url() {
		return 'https://businessdirectoryplugin.com/wp-json/s11edd/v1/updates/';
	}

	/**
	 * @since 5.10
	 */
	protected function skip_categories() {
		return array();
	}

	/**
	 * @since 5.10
	 */
	public function get_pro_license() {
		$license = wpbdp_get_option( 'license-key-module-business-directory-premium' );
		if ( $license ) {
			$this->set_license( $license );
		}

		return $license;
	}

	/**
	 * @since 5.10
	 * @return array|false
	 */
	protected function get_cached() {
		$cache = get_option( $this->cache_key );

		if ( empty( $cache ) || empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		$version     = WPBDP_VERSION;
		$for_current = isset( $cache['version'] ) && $cache['version'] == $version;
		if ( ! $for_current ) {
			// Force a new check.
			return false;
		}

		return json_decode( $cache['value'], true );
	}

	/**
	 * @since 5.10
	 */
	protected function set_cached( $addons ) {
		$data = array(
			'timeout' => strtotime( $this->cache_timeout, current_time( 'timestamp' ) ),
			'value'   => json_encode( $addons ),
			'version' => WPBDP_VERSION,
		);

		update_option( $this->cache_key, $data, 'no' );
	}

	/**
	 * @since 5.10
	 */
	public function reset_cached() {
		delete_option( $this->cache_key );
	}

	/**
	 * @since 5.10
	 * @return array
	 */
	public function error_for_license() {
		$errors = array();
		if ( ! empty( $this->license ) ) {
			$errors = $this->get_error_from_response();
		}

		return $errors;
	}

	/**
	 * @since 5.10
	 * @return array
	 */
	public function get_error_from_response( $addons = array() ) {
		if ( empty( $addons ) ) {
			$addons = $this->get_api_info();
		}
		$errors = array();
		if ( isset( $addons['error'] ) ) {
			$errors[] = $addons['error']['message'];
			do_action( 'wpbdp_license_error', $addons['error'] );
		}

		return $errors;
	}
}
