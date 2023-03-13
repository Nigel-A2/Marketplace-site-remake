<?php
/**
 * Abstract component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;
use HiveTheme\Traits;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Abstract component class.
 *
 * @class Component
 */
abstract class Component {
	use Traits\Mutator;

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Set properties.
		foreach ( $args as $name => $value ) {
			$this->set_property( $name, $value );
		}

		// Bootstrap properties.
		$this->boot();
	}

	/**
	 * Bootstraps component properties.
	 */
	protected function boot() {}

	/**
	 * Sets callbacks.
	 *
	 * @param array $callbacks Callback arguments.
	 */
	final protected function set_callbacks( $callbacks ) {
		foreach ( $callbacks as $callback ) {

			// Get hook type.
			$type = ht\get_array_value( $callback, 'filter' ) ? 'filter' : 'action';

			// Register callback.
			call_user_func_array(
				'add_' . $type,
				[
					$callback['hook'],
					$callback['action'],
					ht\get_array_value( $callback, 'priority', 10 ),
					ht\get_array_value( $callback, 'args', 1 ),
				]
			);
		}
	}
}
