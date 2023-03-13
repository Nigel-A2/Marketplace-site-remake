<?php
namespace AIOWPS\Firewall;

/**
 * Rule that blocks certain kinds of data from the query string
 */
class Rule_Block_Query_Strings_6g extends Rule {

	/**
	 * Implements the action to be taken
	 */
	use Action_Forbid_and_Exit_Trait;

	/**
	 * Construct our rule
	 */
	public function __construct() {
		// Set the rule's metadata
		$this->name     = 'Block query strings';
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
		return (bool) $aiowps_firewall_config->get_value('aiowps_6g_block_query');
	}

	/**
	 * The condition to be satisfied for the rule to apply
	 *
	 * @return boolean
	 */
	public function is_satisfied() {
		
		if (empty($_SERVER['QUERY_STRING'])) return !Rule::SATISFIED;

		//Patterns to match against
		$patterns = array(
			'/[a-z0-9]{2000,}/i',
			'/(eval\()/i',
			'/(127\.0\.0\.1)/i',
			'/(javascript:)(.*)(;)/i',
			'/(base64_encode)(.*)(\()/i',
			'/(GLOBALS|REQUEST)(=|\[|%)/i',
			'/(<|%3C)(.*)script(.*)(>|%3)/i',
			'#(\|\.\.\.|\.\./|~|`|<|>|\|)#i',
			'#(boot\.ini|etc/passwd|self/environ)#i',
			'/(thumbs?(_editor|open)?|tim(thumb)?)\.php/i',
			'/(\'|\")(.*)(drop|insert|md5|select|union)/i',
		);

		return Rule_Utils::contains_pattern(rawurldecode($_SERVER['QUERY_STRING']), $patterns);
	}

}
