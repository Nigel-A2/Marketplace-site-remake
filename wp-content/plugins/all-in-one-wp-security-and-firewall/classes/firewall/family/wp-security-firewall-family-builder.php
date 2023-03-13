<?php
namespace AIOWPS\Firewall;

/**
 * Builds all our families
 */
class Family_Builder {

	/**
	 * Get our families sorted by priority
	 *
	 * @return array
	 */
	public static function get_families() {

		$family_list = include(AIOWPS_FIREWALL_DIR.'/family/wp-security-firewall-families.php');

		//Prioritise the families
		usort($family_list, function($member, $member2) {
			if ($member['priority'] == $member2['priority']) {
				return 0;
			}
			return ($member['priority'] > $member2['priority']) ? 1 : -1;
		});

		$families = array();
		foreach ($family_list as $member) {
			$families[strtolower($member['name'])] = new Family($member['name'], $member['priority']);
		}

		return $families;
	}
}
