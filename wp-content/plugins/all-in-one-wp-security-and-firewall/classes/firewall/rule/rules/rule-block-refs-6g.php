<?php
namespace AIOWPS\Firewall;

/**
 * Rule that blocks certain referrers recommended by 6G
 */
class Rule_Block_Refs_6g extends Rule {

	/**
	 * Implements the action to be taken
	 */
	use Action_Forbid_and_Exit_Trait;

	/**
	 * Construct our rule
	 */
	public function __construct() {
		// Set the rule's metadata
		$this->name     = 'Block referrer strings';
		$this->family   = '6G';
		$this->priority = 0;
	}

	/**
	 * Determines whether the rule is active
	 *
	 * @return boolean
	 */
	public function is_active() {
		global $aiowps_firewall_config;
		return (bool) $aiowps_firewall_config->get_value('aiowps_6g_block_referrers');
	}

	/**
	 * The condition to be satisfied for the rule to apply
	 *
	 * @return boolean
	 */
	public function is_satisfied() {
		
		if (empty($_SERVER['HTTP_REFERER'])) return !Rule::SATISFIED;

		//Patterns to match against
		$patterns = array(
			'/[a-z0-9]{2000,}/i',
			'/(semalt.com|todaperfeita)/i',
		);

		return Rule_Utils::contains_pattern($_SERVER['HTTP_REFERER'], $patterns);
	}

}
