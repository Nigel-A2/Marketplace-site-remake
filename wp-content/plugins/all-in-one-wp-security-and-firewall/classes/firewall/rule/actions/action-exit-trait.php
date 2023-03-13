<?php
namespace AIOWPS\Firewall;

/**
 * Trait which exits the current request
 */
trait Action_Exit_Trait {

	/**
	 * Exit when the rule condition is satisfied.
	 *
	 * @return void
	 */
	public function do_action() {
		exit();
	}
}
