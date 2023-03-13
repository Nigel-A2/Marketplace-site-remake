<?php
namespace AIOWPS\Firewall;

/**
 * Rule that blocks certain kinds of HTTP request methods (e.g DEBUG or PUT)
 */
class Rule_Request_Method_6g extends Rule {

	/**
	 * Implements the action to be taken
	 */
	use Action_Forbid_and_Exit_Trait;

	/**
	 * List of request methods to block
	 *
	 * @var array
	 */
	private $blocked_methods;

	/**
	 * Construct our rule
	 */
	public function __construct() {
		global $aiowps_firewall_config;

		// Set the rule's metadata
		$this->name     = 'Block request methods';
		$this->family   = '6G';
		$this->priority = 0;

		$this->blocked_methods = $aiowps_firewall_config->get_value('aiowps_6g_block_request_methods');
	}

	/**
	 * Determines whether the rule is active
	 *
	 * @return boolean
	 */
	public function is_active() {
		return !empty($this->blocked_methods);
	}

	/**
	 * The condition to be satisfied for the rule to apply
	 *
	 * @return boolean
	 */
	public function is_satisfied() {
		return isset($_SERVER['REQUEST_METHOD']) && in_array(strtoupper($_SERVER['REQUEST_METHOD']), $this->blocked_methods);
	}

}
