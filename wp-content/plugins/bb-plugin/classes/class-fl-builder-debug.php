<?php

final class FL_Debug {

	static private $tests = array();

	public static function init() {
		if ( isset( $_GET['fldebug'] ) && get_transient( 'fl_debug_mode', false ) === $_GET['fldebug'] ) {
			if ( isset( $_GET['info'] ) ) {
				phpinfo();
				exit;
			}
			add_action( 'init', array( 'FL_Debug', 'display_tests' ) );
		}

		if ( get_transient( 'fl_debug_mode' ) ) {
			self::enable_logging();
			add_filter( 'fl_is_debug', '__return_true' );
		}
	}


	public static function enable_logging() {
		if ( isset( $_GET['showerrors'] ) ) {
			@ini_set( 'display_errors', 1 ); // @codingStandardsIgnoreLine
			@ini_set( 'display_startup_errors', 1 ); // @codingStandardsIgnoreLine
			@error_reporting( E_ALL ); // @codingStandardsIgnoreLine
		}
	}

	public static function display_tests() {

		self::prepare_tests();

		header( 'Content-Type:text/plain' );

		foreach ( (array) self::$tests as $test ) {
			echo self::display( $test );
		}
		die();
	}

	private static function display( $test ) {

		if ( is_array( $test['data'] ) ) {
			$test['data'] = implode( "\n", $test['data'] );
		}
		return sprintf( "%s\n%s\n\n", $test['name'], $test['data'] );
	}

	private static function register( $slug, $args ) {
		self::$tests[ $slug ] = $args;
	}

	private static function formatbytes( $size, $precision = 2 ) {
		$base     = log( $size, 1024 );
		$suffixes = array( '', 'K', 'M', 'G', 'T' );

		return round( pow( 1024, $base - floor( $base ) ), $precision ) . $suffixes[ floor( $base ) ];
	}

	private static function get_plugins() {

		$plugins = array();
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		require_once( ABSPATH . 'wp-admin/includes/update.php' );

		$plugins_data = get_plugins();

		foreach ( $plugins_data as $plugin_path => $plugin ) {
			if ( is_plugin_active( $plugin_path ) ) {
				$plugins['active'][] = sprintf( '%s - version %s by %s.', $plugin['Name'], $plugin['Version'], $plugin['Author'] );
			} else {
				$plugins['deactive'][] = sprintf( '%s - version %s by %s.', $plugin['Name'], $plugin['Version'], $plugin['Author'] );
			}
		}
		return $plugins;
	}

	private static function get_mu_plugins() {
		$plugins_data = get_mu_plugins();
		$plugins      = array();

		foreach ( $plugins_data as $plugin_path => $plugin ) {
			$plugins[] = sprintf( '%s version %s by %s', $plugin['Name'], $plugin['Version'], $plugin['Author'] );
		}
		return $plugins;
	}

	public static function safe_ini_get( $ini ) {
		return @ini_get( $ini ); // @codingStandardsIgnoreLine
	}

	private static function divider() {
		return '----------------------------------------------';
	}


	private static function prepare_tests() {

		global $wpdb, $wp_version, $wp_json;

		$args = array(
			'name' => 'WordPress',
			'data' => self::divider(),
		);
		self::register( 'wp', $args );

		$args = array(
			'name' => 'WordPress Address',
			'data' => get_option( 'siteurl' ),
		);
		self::register( 'wp_url', $args );

		$args = array(
			'name' => 'Site Address',
			'data' => get_option( 'home' ),
		);
		self::register( 'site_url', $args );

		$args = array(
			'name' => 'IP',
			'data' => $_SERVER['SERVER_ADDR'],
		);
		self::register( 'wp_ip', $args );

		$args = array(
			'name' => 'WP Version',
			'data' => $wp_version,
		);
		self::register( 'wp_version', $args );

		$args = array(
			'name' => 'WP Debug',
			'data' => defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Yes' : 'No',
		);
		self::register( 'wp_debug', $args );

		$args = array(
			'name' => 'FL Debug',
			'data' => FLBuilder::is_debug() ? 'Yes' : 'No',
		);
		self::register( 'fl_debug', $args );

		$args = array(
			'name' => 'FL Modsec Fix',
			'data' => defined( 'FL_BUILDER_MODSEC_FIX' ) && FL_BUILDER_MODSEC_FIX ? 'Yes' : 'No',
		);
		self::register( 'fl_modsec', $args );

		$args = array(
			'name' => 'SSL Enabled',
			'data' => is_ssl() ? 'Yes' : 'No',
		);
		self::register( 'wp_ssl', $args );

		$args = array(
			'name' => 'Language',
			'data' => get_locale(),
		);
		self::register( 'lang', $args );

		$args = array(
			'name' => 'Multisite',
			'data' => is_multisite() ? 'Yes' : 'No',
		);
		self::register( 'is_multi', $args );

		$args = array(
			'name' => 'WordPress memory limit',
			'data' => WP_MAX_MEMORY_LIMIT,
		);
		self::register( 'wp_max_mem', $args );

		if ( get_option( 'upload_path' ) != 'wp-content/uploads' && get_option( 'upload_path' ) ) {
			$args = array(
				'name' => 'Possible Issue: upload_path is set, can lead to cache dir issues and css not loading. Check Settings -> Media for custom path.',
				'data' => get_option( 'upload_path' ),
			);
			self::register( 'wp_media_upload_path', $args );
		}

		if ( defined( 'DISALLOW_UNFILTERED_HTML' ) && DISALLOW_UNFILTERED_HTML ) {
			$args = array(
				'name' => 'Unfiltered HTML is globally disabled! ( DISALLOW_UNFILTERED_HTML )',
				'data' => 'Yes',
			);
			self::register( 'is_multi', $args );
		}

		$args = array(
			'name' => 'Post Counts',
			'data' => self::divider(),
		);
		self::register( 'post_counts', $args );

		$templates = wp_count_posts( 'fl-builder-template' );

		$post_types = get_post_types( null, 'object' );

		foreach ( $post_types as $type => $type_object ) {

			if ( in_array( $type, array( 'wp_block', 'user_request', 'oembed_cache', 'customize_changeset', 'custom_css', 'nav_menu_item' ) ) ) {
				continue;
			}

			$count = wp_count_posts( $type );

			$args = array(
				'name' => ( 'fl-builder-template' == $type ) ? 'Builder Templates' : 'WordPress ' . $type_object->label,
				'data' => ( $count->inherit > 0 ) ? $count->inherit : $count->publish,
			);
			self::register( 'wp_type_count_' . $type, $args );
		}

		$args = array(
			'name' => 'Themes',
			'data' => self::divider(),
		);
		self::register( 'themes', $args );

		$theme = wp_get_theme();
		$args  = array(
			'name' => 'Active Theme',
			'data' => array(
				sprintf( '%s - v%s', $theme->get( 'Name' ), $theme->get( 'Version' ) ),
				sprintf( 'Parent Theme: %s', ( $theme->get( 'Template' ) ) ? $theme->get( 'Template' ) : 'Not a child theme' ),
			),
		);
		self::register( 'active_theme', $args );

		if ( 'bb-theme' === $theme->get( 'Template' ) ) {
			if ( is_dir( trailingslashit( get_stylesheet_directory() ) . 'includes' ) ) {
				$args = array(
					'name' => 'Child Theme includes folder detected.',
					'data' => trailingslashit( get_stylesheet_directory() ) . 'includes/',
				);
				self::register( 'child_includes', $args );
			}

			if ( is_dir( trailingslashit( get_stylesheet_directory() ) . 'fl-builder/modules' ) ) {
				$modules = glob( trailingslashit( get_stylesheet_directory() ) . 'fl-builder/modules/*' );
				if ( ! empty( $modules ) ) {
					$args = array(
						'name' => 'Child Theme builder modules folder detected.',
						'data' => implode( "\n", $modules ),
					);
					self::register( 'child_bb_modules', $args );
				}
			}
		}

		// child theme functions
		if ( $theme->get( 'Template' ) ) {
			$functions_file = trailingslashit( get_stylesheet_directory() ) . 'functions.php';
			$contents       = file_exists( $functions_file ) ? file_get_contents( $functions_file ) : 'No functions.php found.';

			$args = array(
				'name' => 'Child Theme Functions',
				'data' => $contents,
			);
			self::register( 'child_funcs', $args );
		}

		$args = array(
			'name' => 'Plugins',
			'data' => self::divider(),
		);
		self::register( 'plugins', $args );

		$args = array(
			'name' => 'Plugins',
			'data' => self::divider(),
		);
		self::register( 'wp_plugins', $args );

		$defaults = array(
			'active'   => array(),
			'deactive' => array(),
		);

		$plugins = wp_parse_args( self::get_plugins(), $defaults );
		$args    = array(
			'name' => 'Active Plugins',
			'data' => $plugins['active'],
		);
		self::register( 'wp_plugins', $args );

		$args = array(
			'name' => 'Unactive Plugins',
			'data' => $plugins['deactive'],
		);
		self::register( 'wp_plugins_deactive', $args );

		$args = array(
			'name' => 'Must-Use Plugins',
			'data' => self::get_mu_plugins(),
		);
		self::register( 'mu_plugins', $args );

		$args = array(
			'name' => 'PHP',
			'data' => self::divider(),
		);
		self::register( 'php', $args );

		$args = array(
			'name' => 'PHP SAPI',
			'data' => php_sapi_name(),
		);
		self::register( 'php_sapi', $args );

		$args = array(
			'name' => 'PHP JSON Support',
			'data' => ( $wp_json instanceof Services_JSON ) ? '*** NO JSON MODULE ***' : 'yes',
		);
		self::register( 'php_json', $args );

		$args = array(
			'name' => 'PHP Memory Limit',
			'data' => self::safe_ini_get( 'memory_limit' ),
		);
		self::register( 'php_mem_limit', $args );

		$args = array(
			'name' => 'PHP Version',
			'data' => phpversion(),
		);
		self::register( 'php_ver', $args );

		$args = array(
			'name' => 'Post Max Size',
			'data' => self::safe_ini_get( 'post_max_size' ),
		);
		self::register( 'post_max', $args );

		$args = array(
			'name' => 'PHP Max Input Vars',
			'data' => self::safe_ini_get( 'max_input_vars' ),
		);
		self::register( 'post_max_input', $args );

		$args = array(
			'name' => 'PHP Max Execution Time',
			'data' => self::safe_ini_get( 'max_execution_time' ),
		);
		self::register( 'post_max_time', $args );

		$args = array(
			'name' => 'Max Upload Size',
			'data' => self::formatbytes( wp_max_upload_size() ),
		);
		self::register( 'post_max_upload', $args );

		$curl = ( function_exists( 'curl_version' ) ) ? curl_version() : false;
		$args = array(
			'name' => 'Curl',
			'data' => ( $curl ) ? sprintf( '%s - %s', $curl['version'], $curl['ssl_version'] ) : 'Not Enabled.',
		);
		self::register( 'curl', $args );

		$args = array(
			'name' => 'PCRE Backtrack Limit ( default 1000000 )',
			'data' => self::safe_ini_get( 'pcre.backtrack_limit' ),
		);
		self::register( 'backtrack', $args );

		$args = array(
			'name' => 'PCRE Recursion Limit ( default 100000 )',
			'data' => self::safe_ini_get( 'pcre.recursion_limit' ),
		);
		self::register( 'recursion', $args );

		$zlib = self::safe_ini_get( 'zlib.output_compression' );

		if ( $zlib ) {
			$args = array(
				'name' => 'ZLIB Output Compression',
				'data' => $zlib,
			);
			self::register( 'zlib', $args );
		}

		$zlib_handler = self::safe_ini_get( 'zlib.output_handler' );

		if ( $zlib_handler ) {
			$args = array(
				'name' => 'ZLIB Handler',
				'data' => $zlib,
			);
			self::register( 'zlib_handler', $zlib_handler );
		}

		$args = array(
			'name' => 'BB Products',
			'data' => self::divider(),
		);
		self::register( 'bb', $args );

		$args = array(
			'name' => 'Beaver Builder',
			'data' => FL_BUILDER_VERSION,
		);
		self::register( 'bb_version', $args );

		$args = array(
			'name' => 'Beaver Themer',
			'data' => ( defined( 'FL_THEME_BUILDER_VERSION' ) ) ? FL_THEME_BUILDER_VERSION : 'Not active/installed.',
		);
		self::register( 'themer_version', $args );

		$args = array(
			'name' => 'Beaver Theme',
			'data' => ( defined( 'FL_THEME_VERSION' ) ) ? FL_THEME_VERSION : 'Not active/installed.',
		);
		self::register( 'theme_version', $args );

		$args = array(
			'name' => 'Cache Folders',
			'data' => self::divider(),
		);
		self::register( 'cache_folders', $args );

		$cache = FLBuilderModel::get_cache_dir();

		$args = array(
			'name' => 'Beaver Builder Cache Path',
			'data' => $cache['path'],
		);
		self::register( 'bb_cache_path', $args );

		$args = array(
			'name' => 'Beaver Builder Path writable',
			'data' => ( fl_builder_filesystem()->is_writable( $cache['path'] ) ) ? 'Yes' : 'No',
		);
		self::register( 'bb_cache_path_writable', $args );

		if ( class_exists( 'FLCustomizer' ) ) {
			$cache = FLCustomizer::get_cache_dir();

			$args = array(
				'name' => 'Beaver Theme Cache Path',
				'data' => $cache['path'],
			);
			self::register( 'bb_theme_cache_path', $args );

			$args = array(
				'name' => 'Beaver Theme Path writable',
				'data' => ( fl_builder_filesystem()->is_writable( $cache['path'] ) ) ? 'Yes' : 'No',
			);
			self::register( 'bb_theme_cache_path_writable', $args );
		}

		$args = array(
			'name' => 'WordPress Content Path',
			'data' => WP_CONTENT_DIR,
		);
		self::register( 'bb_content_path', $args );

		$args = array(
			'name' => 'License',
			'data' => self::divider(),
		);
		self::register( 'license', $args );

		if ( true === FL_BUILDER_LITE ) {
			$args = array(
				'name' => 'Beaver Builder License',
				'data' => 'Lite version detected',
			);
			self::register( 'bb_sub_lite', $args );

		} elseif ( class_exists( 'FLUpdater' ) ) {
			$subscription = FLUpdater::get_subscription_info();
			$args         = array(
				'name' => 'Beaver Builder License',
				'data' => ( isset( $subscription->active ) && ! isset( $subscription->error ) ) ? 'Active' : 'Not Active',
			);
			self::register( 'bb_sub', $args );

			if ( isset( $subscription->error ) ) {
				$args = array(
					'name' => 'License Error',
					'data' => $subscription->error,
				);
				self::register( 'bb_sub_err', $args );
			}

			if ( isset( $subscription->domain ) ) {
				$args = array(
					'name' => 'Domain Active',
					'data' => ( '1' == $subscription->domain->active ) ? 'Yes' : 'No',
				);
				self::register( 'bb_sub_domain', $args );
			}
			if ( isset( $subscription->downloads ) && is_array( $subscription->downloads ) && ! empty( $subscription->downloads ) ) {
				$args = array(
					'name' => 'Available Downloads',
					'data' => implode( "\n", $subscription->downloads ),
				);
				self::register( 'av_downloads', $args );
			}
		}

		$args = array(
			'name' => 'Server',
			'data' => self::divider(),
		);
		self::register( 'serv', $args );

		$args = array(
			'name' => 'MySQL Version',
			'data' => ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : 'Unknown' ),
		);
		self::register( 'mysql_version', $args );

		$results = (array) $wpdb->get_results( 'SHOW VARIABLES' );

		foreach ( $results as $k => $result ) {
			if ( 'max_allowed_packet' === $result->Variable_name ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$args = array(
					'name' => 'MySQL Max Allowed Packet',
					'data' => number_format( $result->Value / 1048576 ) . 'MB', // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				);
				self::register( 'mysql_packet', $args );
			}
		}

		$db_bytes = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT SUM(data_length + index_length) FROM information_schema.TABLES where table_schema = %s GROUP BY table_schema;',
				DB_NAME
			)
		);

		if ( is_numeric( $db_bytes ) ) {
			$args = array(
				'name' => 'MySQL Database Size',
				'data' => number_format( $db_bytes / 1048576 ) . 'MB',
			);
			self::register( 'mysql_size', $args );
		}

		$collation = $wpdb->get_var( "SELECT TABLE_COLLATION FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$wpdb->postmeta}';" );
		$args      = array(
			'name' => 'PostMeta DB Collation',
			'data' => $collation,
		);
		self::register( 'collationdb', $args );

		$args = array(
			'name' => 'DB_CHARSET',
			'data' => DB_CHARSET,
		);
		self::register( 'charset', $args );

		$args = array(
			'name' => 'DB_COLLATE',
			'data' => DB_COLLATE,
		);
		self::register( 'collation', $args );

		$args = array(
			'name' => 'Server Info',
			'data' => $_SERVER['SERVER_SOFTWARE'],
		);
		self::register( 'server', $args );

		$args = array(
			'name' => 'htaccess files',
			'data' => self::divider(),
		);
		self::register( 'up_htaccess', $args );

		// detect uploads folder .htaccess file and display it if found.
		$uploads          = wp_upload_dir( null, false );
		$uploads_htaccess = trailingslashit( $uploads['basedir'] ) . '.htaccess';
		$root_htaccess    = trailingslashit( ABSPATH ) . '.htaccess';

		if ( file_exists( $root_htaccess ) ) {
			ob_start();
			readfile( $root_htaccess );
			$htaccess = ob_get_clean();
			$args     = array(
				'name' => $root_htaccess . "\n",
				'data' => $htaccess,
			);
			self::register( 'up_htaccess_root', $args );
		}
		if ( file_exists( $uploads_htaccess ) ) {
			ob_start();
			readfile( $uploads_htaccess );
			$htaccess = ob_get_clean();
			$args     = array(
				'name' => $uploads_htaccess . "\n",
				'data' => $htaccess,
			);
			self::register( 'up_htaccess_uploads', $args );
		}
	}
}
add_action( 'plugins_loaded', array( 'FL_Debug', 'init' ) );
