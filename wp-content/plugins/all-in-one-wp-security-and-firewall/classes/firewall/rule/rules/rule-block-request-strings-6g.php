<?php
namespace AIOWPS\Firewall;

/**
 * Rule that blocks certain kinds of data from the request string
 */
class Rule_Block_Request_Strings_6g extends Rule {

	/**
	 * Implements the action to be taken
	 */
	use Action_Forbid_and_Exit_Trait;

	/**
	 * Construct our rule
	 */
	public function __construct() {
		// Set the rule's metadata
		$this->name     = 'Block request strings';
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
		return (bool) $aiowps_firewall_config->get_value('aiowps_6g_block_request');
	}

	/**
	 * The condition to be satisfied for the rule to apply
	 *
	 * @return boolean
	 */
	public function is_satisfied() {
		
		if (empty($_SERVER['PHP_SELF'])) return !Rule::SATISFIED;
		
		//Patterns to match against
		$patterns = array(
			'/[a-z0-9]{2000,}/i',
			'#(https?|ftp|php):/#i',
			'#(base64_encode)(.*)(\()#i',
			'#(=\'|=\%27|/\'/?)\.#i',
			'#/(\$(\&)?|\*|\"|\.|,|&|&amp;?)/?$#i',
			'#(\{0\}|\(/\(|\.\.\.|\+\+\+|\\"\\")#i',
			'#(~|`|<|>|:|;|,|%|\|\s|\{|\}|\[|\]|\|)#i',
			'#/(=|\$&|_mm|cgi-|etc/passwd|muieblack)#i',
			'#(&pws=0|_vti_|\(null\)|\{\$itemURL\}|echo(.*)kae|etc/passwd|eval\(|self/environ)#i',
			'#\.(aspx?|bash|bak?|cfg|cgi|dll|exe|git|hg|ini|jsp|log|mdb|out|sql|svn|swp|tar|rar|rdf)$#i',
			'#/(^$|(wp-)?config|mobiquo|phpinfo|shell|sqlpatch|thumb|thumb_editor|thumbopen|timthumb|webshell)\.php#i',
		);

		return Rule_Utils::contains_pattern(rawurldecode($_SERVER['PHP_SELF']), $patterns);
	}

}
