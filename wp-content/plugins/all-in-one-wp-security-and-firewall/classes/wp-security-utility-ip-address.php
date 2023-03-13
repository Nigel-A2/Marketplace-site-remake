<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_Utility_IP {
	public function __construct() {
		//NOP
	}

	/**
	 * Get sever detected visitor IP Address.
	 *
	 * @return String visitor IP Address.
	 */
	public static function get_server_detected_user_ip_address() {
		global $aio_wp_security;

		// check if user configured custom IP retrieval method
		$ip_method_id = $aio_wp_security->configs->get_value('aiowps_ip_retrieve_method');

		$visitor_ip = '';
		$ip_retrieve_methods = AIOS_Abstracted_Ids::get_ip_retrieve_methods();

		if (empty($ip_method_id) || !isset($ip_retrieve_methods[$ip_method_id])) {
			$ip_method_id = 0;
		}

		$visitor_ip = isset($_SERVER[$ip_retrieve_methods[$ip_method_id]]) ? $_SERVER[$ip_retrieve_methods[$ip_method_id]] : '';

		// Check if multiple IPs were given - these will be present as comma-separated list
		if (stristr($visitor_ip, ',')) {
			$temp = explode(',', $visitor_ip);
			$visitor_ip = trim(reset($temp)); //get first address because this will likely be the original connecting IP
		}

		// Now remove port portion if applicable
		if (false !== strpos($visitor_ip, '.') && false !== strpos($visitor_ip, ':')) {
			// likely ipv4 address with port
			$visitor_ip = preg_replace('/:\d+$/', '', $visitor_ip); //Strip off port
		}

		if (!filter_var($visitor_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($visitor_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$visitor_ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
		}

		return $visitor_ip;
	}

	/**
	 * Get user IP Address.
	 *
	 * @return string User IP Address.
	 */
	public static function get_user_ip_address() {
		$visitor_ip = self::get_server_detected_user_ip_address();

		if ((!defined('AIOS_DISABLE_GET_EXTERNAL_IP') || !AIOS_DISABLE_GET_EXTERNAL_IP) && in_array($visitor_ip, array('', '127.0.0.1', '::1'))) {
			$visitor_ip = self::get_external_ip_address();
		}

		return $visitor_ip;
	}

	/**
	 * Get user IP Address using an external service.
	 * This can be used as a fallback for users on localhost where
	 * get_ip_address() will be a local IP and non-geolocatable.
	 *
	 * @return string external ip address.
	 */
	public static function get_external_ip_address() {
		$external_ip_address = '0.0.0.0';
		$ip_lookup_services = array(
			'ipify'             => 'http://api.ipify.org/',
			'ipecho'            => 'https://ipecho.net/plain',
			'ident'             => 'http://ident.me',
			'tnedi'				=> 'http://tnedi.me',
		);
		$ip_lookup_services_keys = array_keys($ip_lookup_services);
		shuffle($ip_lookup_services_keys);

		foreach ($ip_lookup_services_keys as $service_name) {
			$service_endpoint = $ip_lookup_services[$service_name];
			$response         = wp_safe_remote_get($service_endpoint, array( 'timeout' => 2 ));

			if (!is_wp_error($response) && rest_is_ip_address($response['body'])) {
				$external_ip_address = sanitize_text_field($response['body']);
				break;
			}
		}

		return $external_ip_address;
	}

	/**
	 * Get server suitable IP methods.
	 *
	 * @return Array array of IP methods.
	 */
	public static function get_server_suitable_ip_methods() {
		static $server_suitable_ip_methods;
		if (!isset($server_suitable_ip_methods)) {
			$server_suitable_ip_methods = array();
			foreach (AIOS_Abstracted_Ids::get_ip_retrieve_methods() as $ip_method) {
				if (isset($_SERVER[$ip_method])) {
					$server_suitable_ip_methods[] = $ip_method;
				}
			}
		}
		return $server_suitable_ip_methods;
	}

	/**
	 * Check whether all sever suitable IP address is giving same IP address or not.
	 *
	 * @return Boolean True if all server suitable IP methods gives same IP address, otherwise false.
	 */
	public static function is_server_suitable_ip_methods_give_same_ip_address() {
		$server_suitable_ip_methods = self::get_server_suitable_ip_methods();

		if (empty($server_suitable_ip_methods)) {
			return false;
		}

		if (1 === count($server_suitable_ip_methods)) {
			return true;
		}

		$ip_addresses = array();
		foreach ($server_suitable_ip_methods as $server_suitable_ip_method) {
			$ip_addresses[] = $_SERVER[$server_suitable_ip_method];
		}

		return (1 === count(array_unique($ip_addresses)));
	}
	
	/**
	 * Returns the first three octets of a sanitized IP address so it can used as an IP address range
	 *
	 * @param int $ip
	 * @return int
	 */
	public static function get_sanitized_ip_range($ip) {
		global $aio_wp_security;
		$ip_range = '';
		$valid_ip = filter_var($ip, FILTER_VALIDATE_IP); //Sanitize the IP address
		if ($valid_ip) {
			$ip_type = WP_Http::is_ip_address($ip); //returns 4 or 6 if ipv4 or ipv6 or false if invalid
			if (6 == $ip_type || false === $ip_type) return ''; // for now return empty if ipv6 or invalid IP
			$ip_range = substr($valid_ip, 0, strrpos($valid_ip, ".")); //strip last portion of address to leave an IP range
		} else {
			//Write log if the 'REMOTE_ADDR' contains something which is not an IP
			$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_Utility_IP - Invalid IP received ".$ip, 4);
		}
		return $ip_range;
	}

	
	public static function create_ip_list_array_from_string_with_newline($ip_addresses) {
		$ip_list_array = preg_split("/\R/", $ip_addresses);
		return $ip_list_array;
	}
	
	/**
	 * Returns IPv6 ip address or IPv6 range if valid
	 *
	 * @param string $item possible IPv6 ip address or IPv6 range
	 * @return string|boolean $checked_ip trimmed IPv6 ip address or IPv6 range if given input is valid otherwise false.
	 */
	public static function is_ipv6_address_or_ipv6_range($item) {
		$checked_ip = false;
		$res = WP_Http::is_ip_address($item);
		if ('6' == $res && Requests_IPv6::check_ipv6($item)) {
			$checked_ip = trim($item);
		} else {
			//ipv6 - range check for valid CIDR range
			$item_ip_range = explode('/', $item);
			$ip_part_valid = filter_var($item_ip_range[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
			//1 - 164 range of the IPv6 subnect masking as per CISCO propersed change from 128.
			if (2 == count($item_ip_range) && $ip_part_valid && $item_ip_range[1] >= 1 && $item_ip_range[1] <= 164) {
				$checked_ip = trim($item);
			}
		}
		return $checked_ip;
	}
	
	/**
	 * Validates IP or IP range
	 *
	 * @param array  $ip_list_array
	 * @param string $list_type
	 * @return array $return_payload
	 */
	public static function validate_ip_list($ip_list_array, $list_type = '') {
		$errors = '';

		//validate list
		$submitted_ips = $ip_list_array;
		$list = array();

		if (!empty($submitted_ips)) {
			foreach ($submitted_ips as $item) {
				$item = sanitize_text_field($item);
				if (strlen($item) > 0) {
					//ipv6 - check
					if (strpos($item, ':') !== false) {
						//possible ipv6 addr
						$checked_ip = AIOWPSecurity_Utility_IP::is_ipv6_address_or_ipv6_range($item);
						if (false === $checked_ip) {
							$errors .= "\n".$item.__(' is not a valid ip address format.', 'all-in-one-wp-security-and-firewall');
						} else {
							$list[] = trim($item);
						}
						continue;
					}
					
					$ipParts = explode('.', $item);
					$isIP = 0;
					$partcount = 1;
					$goodip = true;
					$foundwild = false;
					
					if (count($ipParts) < 2) {
						$errors .= "\n".$item.__(' is not a valid ip address format.', 'all-in-one-wp-security-and-firewall');
						continue;
					}

					foreach ($ipParts as $part) {
						if (true == $goodip) {
							if ((is_numeric(trim($part)) && trim($part) <= 255 && trim($part) >= 0) || trim($part) == '*') {
								$isIP++;
							}

							switch ($partcount) {
								case 1:
									if ('*' == trim($part)) {
										$goodip = false;
										$errors .= "\n".$item.__(' is not a valid ip address format.', 'all-in-one-wp-security-and-firewall');
									}
									break;
								case 2:
									if ('*' == trim($part)) {
										$foundwild = true;
									}
									break;
								default:
									if ('*' != trim($part)) {
										if (true == $foundwild) {
											$goodip = false;
											$errors .= "\n".$item.__(' is not a valid ip address format.', 'all-in-one-wp-security-and-firewall');
										}
									} else {
										$foundwild = true;
									}
									break;
							}

							$partcount++;
						}
					}
					if (ip2long(trim(str_replace('*', '0', $item))) == false) { //invalid ip
						$errors .= "\n".$item.__(' is not a valid ip address format.', 'all-in-one-wp-security-and-firewall');
					} elseif (strlen($item) > 4 && !in_array($item, $list)) {
						$current_user_ip = AIOWPSecurity_Utility_IP::get_user_ip_address();
						if ($item == $current_user_ip && 'blacklist' == $list_type) {
							//You can't ban your own IP
							$errors .= "\n".__('You cannot ban your own IP address: ', 'all-in-one-wp-security-and-firewall').$item;
						} else {
							$list[] = trim($item);
						}
					}
				}
			}
		} else {
			//This function was called with an empty IP address array list
		}

		if (strlen($errors)> 0) {
			$return_payload = array(-1, array($errors));
			return $return_payload;
		}
		
		if (sizeof($list) >= 1) {
			sort($list);
			$list = array_unique($list, SORT_STRING);
			
			$return_payload = array(1, $list);
			return $return_payload;
		}

		$return_payload = array(1, array());
		return $return_payload;
	}
	
	
	/**
	 * Checks if IP address matches against the specified whitelist of IP addresses or IP ranges
	 *
	 * @param type $ip_address
	 * @param type $whitelisted_ips (newline separated string of IPs)
	 * @return boolean
	 */
	public static function is_ip_whitelisted($ip_address, $whitelisted_ips) {
		if (empty($ip_address) || empty($whitelisted_ips)) return false;
		
		$ip_list_array = AIOWPSecurity_Utility_IP::create_ip_list_array_from_string_with_newline($whitelisted_ips);
		if (empty($ip_list_array)) return false;
		
		require_once(AIO_WP_SECURITY_PATH.'/vendor/mlocati/ip-lib/ip-lib.php');
		$ip_address_parsed = \IPLib\Factory::parseAddressString($ip_address);
		foreach ($ip_list_array as $white_ip) {
			$ipParts = explode('.', $white_ip);
			$found = array_search('*', $ipParts);
			$found_white_ipv6 = strpos($white_ip, ':');
			// ipv4 range check starts
			if (false !== $found || false != $found_white_ipv6) {
				$range = \IPLib\Factory::parseRangeString($white_ip);
				$with_in_range = $range->contains($ip_address_parsed);
				if (true == $with_in_range) {
					return true;
				}
			} elseif ($white_ip == $ip_address) {
				return true;
			}
		}
		return false;
	}

	/**
	 * If login whitelist enabled and the user IP is not whitelisted, Then forbid access.
	 *
	 * @return void
	 */
	public static function check_login_whitelist_and_forbid() {
		if (defined('AIOS_DISABLE_LOGIN_WHITELIST') && AIOS_DISABLE_LOGIN_WHITELIST) {
			return;
		}

		global $aio_wp_security;

		if ('1' != $aio_wp_security->configs->get_value('aiowps_enable_whitelisting')) {
			return;
		}

		$ip = AIOWPSecurity_Utility_IP::get_user_ip_address(); //Get the IP address of user
		$whitelisted_ips = $aio_wp_security->configs->get_value('aiowps_allowed_ip_addresses');
		$is_whitelisted = AIOWPSecurity_Utility_IP::is_ip_whitelisted($ip, $whitelisted_ips);

		if ($is_whitelisted) {
			return;
		}

		header('HTTP/1.1 403 Forbidden');
		exit();
	}
}
