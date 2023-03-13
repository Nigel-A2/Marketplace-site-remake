<?php
namespace AIOWPS\Firewall;

/**
 * Builds our rules
 */
class Rule_Builder {

	/**
	 * Gets our rule if it's active
	 *
	 * @return iterable
	 */
	public static function get_active_rule() {

		foreach (self::get_rule_classname() as $classname) {

			$rule = new $classname();

			if (!$rule->is_active()) {
				continue;
			}

			yield $rule;
		}
	}

	/**
	 * Generates the classname for each rule
	 *
	 * @return iterable
	 */
	private static function get_rule_classname() {

		$handle = opendir(AIOWPS_FIREWALL_DIR.'/rule/rules/');
		if ($handle) {
			while (false !== ($entry = readdir($handle))) {
				$matches = array();
				if (preg_match('/^rule-(.*)\.php$/', $entry, $matches)) {
					yield "AIOWPS\Firewall\Rule_".ucwords(str_replace('-', '_', $matches[1]), '_');
				}
			}
			@closedir($handle); //phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}

	}

}
