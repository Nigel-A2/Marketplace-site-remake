<?php
namespace AIOWPS\Firewall;

/**
 * Represents a family (a grouping of rules)
 */
class Family {

	/**
	 * Name of the family
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Priority of the family (0 is the highest)
	 *
	 * @var int
	 */
	public $priority;

	/**
	 * List of rules to apply
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * Builds our family object
	 *
	 * @param string  $name
	 * @param integer $priority
	 */
	public function __construct($name, $priority = 999999) {
		$this->name     = $name;
		$this->priority = $priority;
		$this->rules    = array();
	}

	/**
	 * Adds a rule to the family
	 *
	 * @param Rule $rule
	 * @return void
	 */
	public function add_rule(Rule $rule) {
		$this->rules[] = $rule;
	}

	/**
	 * Applies all the rules in the family
	 *
	 * @return void
	 */
	public function apply_all() {

		if (empty($this->rules)) {
			return;
		}

		//ensure the rules are ordered by priority
		usort($this->rules, function(Rule $rule, Rule $rule2) {
			if ($rule->priority == $rule2->priority) {
				return 0;
			}
			return ($rule->priority > $rule2->priority) ? 1 : -1;
		});

		foreach ($this->rules as $rule) {
			$rule->apply();
		}

	}

	/**
	 * Returns the family name if used as a string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

}
