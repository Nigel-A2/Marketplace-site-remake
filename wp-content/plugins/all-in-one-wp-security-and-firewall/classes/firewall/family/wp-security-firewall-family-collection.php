<?php
namespace AIOWPS\Firewall;

/**
 * Holds all our families
 */
class Family_Collection {

	/**
	 * Holds our families
	 *
	 * @var array
	 */
	protected $families;


	/**
	 * Constructs our family collection object
	 *
	 * @param array $families - The sorted families to contain
	 */
	public function __construct($families = array()) {
		$this->families = $families;
	}

	/**
	 * Generator method to iterate over the familes
	 *
	 * @return iterable
	 */
	public function get_family() {
		foreach ($this->families as $family) {
			yield $family;
		}
	}

	/**
	 * Adds a new rule to a family member
	 *
	 * @param Rule $rule - an active rule to add to its family
	 * @return void
	 */
	public function add_rule_to_member(Rule $rule) {
		$key = strtolower($rule->family);
		if (array_key_exists($key, $this->families)) {
			$this->families[$key]->add_rule($rule);
		}
	}
}
