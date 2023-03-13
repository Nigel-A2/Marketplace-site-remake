<?php
namespace AIOWPS\Firewall;

/**
 * Base class for our firewall rules
 */
abstract class Rule {

	/**
	 * Name of the rule
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Name of the family the rule belongs to
	 *
	 * @var string
	 */
	public $family;

	/**
	 * Rule's priority (0 is the highest)
	 *
	 * @var int
	 */
	public $priority;

	/**
	 * An abstraction for when the rule is satisfied
	 *
	 * @var boolean
	 */
	const SATISFIED = true;

	/**
	 * Executes the rule's action
	 *
	 * @return void
	 */
	abstract public function do_action();

	/**
	 * Check if the rule is active
	 *
	 * @return boolean
	 */
	abstract public function is_active();

	/**
	 * Check if the rule has been satisfied
	 *
	 * @return boolean
	 */
	abstract public function is_satisfied();

	/**
	 * Apply the rule and execute the action if satisfied
	 *
	 * @return void
	 */
	public function apply() {

		if ($this->is_satisfied()) {

			if (defined('AIOS_FIREWALL_DEBUG') && AIOS_FIREWALL_DEBUG) {
				error_log("AIOS firewall rule triggered: {$this->name}");

				if (defined('AIOS_FIREWALL_SERVER_DUMP') && AIOS_FIREWALL_SERVER_DUMP) {
					error_log(print_r($_SERVER, true));
				}
			}



			$this->do_action();
		}

	}

	/**
	 * Show the rule's name
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}
}
