<?php
namespace AIOWPS\Firewall;

/**
 * Trait to set the header to forbidden
 */
trait Action_Forbid_Trait {

	/**
	 * Forbid 403 when the rule condition is satisfied.
	 *
	 * @return void
	 */
	public function do_action() {
		header('HTTP/1.1 403 Forbidden');
	}
}
