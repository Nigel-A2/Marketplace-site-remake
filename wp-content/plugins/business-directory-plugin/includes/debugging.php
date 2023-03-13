<?php
class WPBDP_Debugging {

	private static $debug = false;
	private static $messages = array();

	public static function is_debug_on() {
	    return self::$debug;
    }

	public static function debug_on() {
		self::$debug = true;

		error_reporting( E_ALL | E_DEPRECATED );

		// Disable our debug util for AJAX requests in order to be able to see the errors.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return;

		// @ini_set( 'display_errors', '1' );
		/** @phpstan-ignore-next-line */
		set_error_handler( array( 'WPBDP_Debugging', '_php_error_handler' ) );

		add_action( 'wp_enqueue_scripts', array( 'WPBDP_Debugging', '_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( 'WPBDP_Debugging', '_enqueue_scripts' ) );

		add_action( 'admin_footer', array( 'WPBDP_Debugging', '_debug_bar_footer' ), 99999 );
		add_action( 'wp_footer', array( 'WPBDP_Debugging', '_debug_bar_footer' ), 99999 );
	}

	public static function _enqueue_scripts() {
        wp_enqueue_script(
            'wpbdp-debugging-js',
            WPBDP_ASSETS_URL . 'js/debug.min.js',
            array( 'jquery' ),
            WPBDP_VERSION,
            true
        );

        wp_enqueue_style(
            'wpbdp-debugging-styles',
            WPBDP_ASSETS_URL . 'css/debug.min.css',
            array(),
            WPBDP_VERSION
        );
	}

	public static function _php_error_handler( $errno, $errstr, $file, $line, $context ) {
		static $errno_to_string = array(
			E_ERROR => 'error',
			E_WARNING => 'warning',
			E_NOTICE => 'notice',
			E_USER_ERROR => 'user-error',
			E_USER_WARNING => 'user-warning',
			E_USER_NOTICE => 'user-notice',
			E_DEPRECATED => 'deprecated'
		);

		self::add_debug_msg(
			$errstr,
			isset( $errno_to_string[ $errno ] ) ? 'php-' . $errno_to_string[ $errno ] : 'php',
			array(
				'file' => $file,
				'line' => $line,
			)
		);
	}

	public static function debug_off() {
		self::$debug = false;

		remove_action( 'admin_footer', array( 'WPBDP_Debugging', '_debug_bar_footer' ), 99999 );
		remove_action( 'wp_footer', array( 'WPBDP_Debugging', '_debug_bar_footer' ), 99999 );
	}

	public static function _debug_bar_footer() {
		if ( ! self::$debug ) {
			return;
		}

		global $wpdb;
		$queries = $wpdb->queries;

		if ( ! self::$messages && ! $queries ) {
			return;
		}

		echo '<div id="wpbdp-debugging">';
		echo '<ul class="tab-selector">';
		echo '<li class="active"><a href="#logging">Logging</a></li>';
		echo '<li><a href="#wpdbqueries">$wpdb queries</a></li>';
		echo '</ul>';
		echo '<div class="tab" id="wpbdp-debugging-tab-logging">';
		echo '<table>';

		foreach ( self::$messages as $item ) {
			$time = explode( ' ', $item['timestamp'] );

			echo '<tr class="' . $item['type'] . '">';
			echo '<td class="handle">&raquo;</td>';
			echo '<td class="timestamp">' . date( 'H:i:s', (int) $time[1] ) . '</td>';

			echo '<td class="type">' . $item['type'] . '</td>';
			echo '<td class="message">' . $item['message'] . '</td>';

			if ( $item['context'] ) {
				echo '<td class="context">' . $item['context']['function'] . '</td>';
				echo '<td class="file">' . basename( $item['context']['file'] ) . ':' . $item['context']['line'] . '</td>';
			} else {
				echo '<td class="context"></td><td class="file"></td>';
			}
			echo '</tr>';
		}

		echo '</table>';
		echo '</div>';

		echo '<div class="tab" id="wpbdp-debugging-tab-wpdbqueries">';
		if ( ! $queries ) {
			echo 'No SQL queries were logged.';
		} else {
			echo '<table>';

			foreach ( $queries as $q ) {
				echo '<tr class="wpdbquery">';
				echo '<td class="handle">&raquo;</td>';
				echo '<td class="query">';
				echo $q[0];
				echo '<div class="extradata">';
				echo '<dl>';
				echo '<dt>Time Spent:</dt><dd>' . $q[1] . '</dd>';
				echo '<dt>Backtrace:</dt><dd>' . $q[2] . '</dd>';
				echo '</dl>';
				echo '</div>';
				echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
		}
		echo '</div>';
		echo '</div>';
	}

	private static function _extract_context( $stack ) {
		if ( ! is_array( $stack ) || empty( $stack ) ) {
			return array();
		}

		$context = array( 'class' => '', 'file' => '', 'function' => '', 'line' => '' );

		foreach ( $stack as $i => &$item ) {
			if ( ( isset( $item['class'] ) && $item['class'] == 'WPBDP_Debugging' ) || ( isset( $item['file'] ) && $item['file'] == __FILE__ ) )
				continue;

			if ( isset( $item['function'] ) && in_array( $item['function'], array( 'wpbdp_log', 'wpbdp_debug', 'wpbdp_log_deprecated' ) ) ) {
				$context['file'] = $item['file'];
				$context['line'] = $item['line'];
				$context['function'] = $item['function'];

				$i2 = current( $stack );
				$context['function'] = $i2['function'];
				break;
			} else {
				$context['file'] = $item['file'];
				$context['line'] = $item['line'];
				$context['stack'] = $stack;
			}
		}

		return $context;
	}

	private static function add_debug_msg( $msg, $type = 'debug', $context = null ) {
		self::$messages[] = array( 'timestamp' => microtime(),
								   'message' => $msg,
								   'type' => $type,
								   'context' => wpbdp_starts_with( $type, 'php', false ) ? $context : self::_extract_context( $context ),
								 );
	}

	private static function _var_dump( $var ) {
		if ( is_bool( $var ) || is_int( $var ) || ( is_string( $var ) && empty( $var ) ) )
			return var_export( $var, true );

		return print_r( $var, true );
	}

	/* API */

	public static function debug() {
		if ( self::$debug ) {
			foreach ( func_get_args() as $var ) {
				self::add_debug_msg( self::_var_dump( $var ), 'debug', debug_backtrace() );
			}
		}
	}

	public static function debug_e() {
		$ret = '';

		foreach ( func_get_args() as $arg ) {
			$ret .= self::_var_dump( $arg ) . "\n";
		}

		wp_die( sprintf( '<pre>%s</pre>', $ret ), '' );
	}

	public static function log( $msg, $type = 'info' ) {
		self::add_debug_msg( $msg, sprintf( 'log-%s', $type ), debug_backtrace() );
	}

}

function wpbdp_log( $msg, $type = 'info' ) {
	call_user_func( array( 'WPBDP_Debugging', 'log' ), $msg, $type );
}

function wpbdp_log_deprecated() {
	wpbdp_log( 'Deprecated function called.', 'deprecated' );
}

function wpbdp_debug() {
	$args = func_get_args();
	call_user_func_array( array( 'WPBDP_Debugging', 'debug' ), $args );
}

function wpbdp_debug_e() {
	$args = func_get_args();
	call_user_func_array( array( 'WPBDP_Debugging', 'debug_e' ), $args );
}

