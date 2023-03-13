<?php
/**
 * Plugin component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Plugin component class.
 *
 * @class Plugin
 */
final class Plugin extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {
		if ( is_admin() ) {

			// Load TGMPA.
			add_action( 'after_setup_theme', [ $this, 'load_tgmpa' ] );

			// Register plugins.
			add_action( 'tgmpa_register', [ $this, 'register_plugins' ] );
		}

		parent::__construct( $args );
	}

	/**
	 * Loads TGMPA.
	 */
	public function load_tgmpa() {
		$filepath = hivetheme()->get_path() . '/vendor/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php';

		if ( file_exists( $filepath ) ) {
			require_once $filepath;
		}
	}

	/**
	 * Registers plugins.
	 */
	public function register_plugins() {
		tgmpa( hivetheme()->get_config( 'plugins' ) );
	}
}
