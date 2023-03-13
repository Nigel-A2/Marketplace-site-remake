<?php

namespace {

	define( 'OBJECT', 'OBJECT' );
	define( 'OBJECT_K', 'OBJECT_K' );
	define( 'ARRAY_A', 'ARRAY_A' );

	define( 'MINUTE_IN_SECONDS', 60 );
	define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
	define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
	define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
	define( 'MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS );
	define( 'YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS );
	define( 'ABSPATH', realpath( __FILE__ . '/../../../../' ) );
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
	define( 'WPINC', 'wp-includes' );
	define( 'PCLZIP_OPT_REMOVE_ALL_PATH', 77004 );
	define( 'PCLZIP_OPT_REMOVE_PATH', 77003 );
	define( 'PCLZIP_OPT_PATH', 77001 );
	define( 'PCLZIP_OPT_ADD_PATH', 77002 );

	define( 'WPBDP_POST_TYPE', 'wpbdp_listing' );
	define( 'WPBDP_CATEGORY_TAX', 'wpbdp_category' );
	define( 'WPBDP_PLUGIN_FILE', 'business-directory-plugin/business-directory-plugin.php' );
	define( 'WPBDP_PATH', wp_normalize_path( plugin_dir_path( WPBDP_PLUGIN_FILE ) ) );
	define( 'WPBDP_TEMPLATES_PATH', WPBDP_PATH . 'templates' );
	define( 'WPBDP_TAGS_TAX', 'wpbdp_tag' );
	define( 'WPBDP_URL', trailingslashit( plugins_url( '/', WPBDP_PLUGIN_FILE ) ) );
	define( 'WPBDP_ASSETS_URL', WPBDP_URL . 'assets/' );
	define( 'WPBDP_VERSION', '6.0' );
	define( 'WPBDP_INC', trailingslashit( WPBDP_PATH . 'includes' ) );

	define( 'AUTH_KEY', '' );

	function wpbdp() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new WPBDP();
		}

		return $instance;
	}

	abstract class AuthorizeNetRequest {
		public function __construct( $api_login_id = false, $transaction_key = false ) {
		}
	}

	class AuthorizeNetException extends Exception {
	}

	class AuthorizeNetARB extends AuthorizeNetRequest {
	}

	class AuthorizeNetAIM extends AuthorizeNetRequest {
	}

	class AuthorizeNet_Subscription {
	}

	/* Add-ons */

	function wpbdp_regions_taxonomy() {
	}

	function wpbdp_regions_api() {
	}

	class WPBDP_Addons {
		public static function show_conditional_action_button( $atts ) {
		}
	}

	/* Integrations */

	class bcn_breadcrumb {
		public function __construct( $title = '', $template = '', array $type = array(), $url = '', $id = null, $linked = false ) {
		}
	}

	function icl_object_id( $id, $type = 'post', $return_original_if_missing = false, $lang = false ) {
	}

	function icl_get_languages( $args = '' ) {
	}

	abstract class WPSEO_Option {
	}

	class WPSEO_Taxonomy_Meta extends WPSEO_Option {
		public static function get_term_meta( $term, $taxonomy, $meta = null ) {
		}
	}

	function wpseo_replace_vars( $string, $args, $omit = [] ) {
	}

	// Jetpack.
	function sharing_display( $text = '', $echo = false ) {
	}
}

namespace WPMailSMTP {
	class Options {
		public static function init() {
		}
	}
}
