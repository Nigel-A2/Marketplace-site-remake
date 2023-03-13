<?php
namespace AIOWPS\Firewall;

/**
 * Utility methods to help with the rules
 */
class Rule_Utils {

	/**
	 * Check if the subject contains the given pattern or patterns
	 *
	 * @param string       $subject - The subject we wish to check the pattern or patterns against.
	 * @param string|array $pattern - Regex pattern. An array for multiple patterns; a string otherwise.
	 * @return boolean
	 */
	public static function contains_pattern($subject, $pattern) {

		if (is_string($pattern)) return (1 === preg_match($pattern, $subject));

		if (!is_array($pattern)) return false;

		foreach ($pattern as $patt) {
			if (preg_match($patt, $subject)) return true;
		}

		return false;
	}

}
