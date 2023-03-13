<?php
namespace AIOWPS\Firewall;

/**
 * Combines the forbid and exit trait
 */
trait Action_Forbid_and_Exit_Trait {

	use Action_Forbid_Trait, Action_Exit_Trait {
		Action_Forbid_Trait::do_action as protected do_action_forbid;
		Action_Exit_Trait::do_action as protected do_action_exit;
	}

	/**
	 * Forbid 403 and Exit when the rule condition is satisfied.
	 *
	 * @return void
	 */
	public function do_action() {
		$this->do_action_forbid();
		$this->do_action_exit();
	}
}
