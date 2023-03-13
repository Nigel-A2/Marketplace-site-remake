<?php
namespace FLCacheClear;
class Plugin {

	private $classes = array();

	private $filters = array();

	private static $plugins = array();

	private $actions = array(
		'fl_builder_cache_cleared',
		'fl_builder_after_save_layout',
		'fl_builder_after_save_user_template',
	);

	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'unload_helper_plugin' ) );
		add_action( 'plugins_loaded', array( $this, 'load_files' ) );
		add_action( 'admin_init', array( $this, 'check_urls' ) );
		add_action( 'fl_builder_admin_settings_save', array( $this, 'save_settings' ) );
	}

	/**
	 * If the base url has changed clear bb cached css/js
	 * @since 2.4.1
	 */
	public function check_urls() {
		$replace = array( 'https://', 'http://' );
		$current = str_replace( $replace, '', get_option( 'siteurl' ) );
		$saved   = str_replace( $replace, '', base64_decode( get_option( 'fl_site_url' ), true ) );

		if ( $current !== $saved ) {
			\FLBuilderUtils::update_option( 'fl_site_url', base64_encode( $current ) );
			if ( '' !== $saved ) {
				\FLBuilderModel::delete_asset_cache_for_all_posts();
				if ( class_exists( '\FLCustomizer' ) && method_exists( '\FLCustomizer', 'clear_all_css_cache' ) ) {
					\FLCustomizer::clear_all_css_cache();
				}

				\FLBuilder::log( 'Beaver Builder: URL change detected, cache cleared.' );
				do_action( 'fl_site_url_changed', $current, $saved );
			}
		}
	}

	/**
	 * Save settings added to Tools page.
	 * @since 2.1.5
	 */
	public function save_settings() {
		if ( ! isset( $_POST['fl-cache-plugins-nonce'] ) || ! wp_verify_nonce( $_POST['fl-cache-plugins-nonce'], 'cache-plugins' ) ) {
			return false;
		}

		$enabled = isset( $_POST['fl-cache-plugins-enabled'] ) ? $_POST['fl-cache-plugins-enabled'] : 0;
		$varnish = isset( $_POST['fl-cache-varnish-enabled'] ) ? $_POST['fl-cache-varnish-enabled'] : 0;

		$settings = array(
			'enabled' => $enabled,
			'varnish' => $varnish,
		);

		\FLBuilderModel::update_admin_settings_option( '_fl_builder_cache_plugins', $settings, false );
	}

	/**
	 * Get settings.
	 * @since 2.1.5
	 */
	public static function get_settings() {

		$defaults = array(
			'enabled' => true,
			'varnish' => false,
		);

		$settings = \FLBuilderModel::get_admin_settings_option( '_fl_builder_cache_plugins', false );
		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Remove actions added by the cache helper plugin.
	 * @since 2.1.5
	 */
	public function unload_helper_plugin() {
		if ( class_exists( 'FL_Cache_Buster' ) ) {
			$settings = self::get_settings();
			if ( $settings['enabled'] ) {
				remove_action( 'upgrader_process_complete', array( 'FL_Cache_Buster', 'clear_caches' ) );
				remove_action( 'fl_builder_after_save_layout', array( 'FL_Cache_Buster', 'clear_caches' ) );
				remove_action( 'fl_builder_after_save_user_template', array( 'FL_Cache_Buster', 'clear_caches' ) );
				remove_action( 'fl_builder_cache_cleared', array( 'FL_Cache_Buster', 'clear_caches' ) );
				remove_action( 'template_redirect', array( 'FL_Cache_Buster', 'donotcache' ) );
			}
		}
	}

	/**
	 * Load the cache plugin files.
	 */
	public function load_files() {

		foreach ( glob( FL_BUILDER_CACHE_HELPER_DIR . 'plugins/*.php' ) as $file ) {

			$classname = 'FLCacheClear\\' . ucfirst( str_replace( '.php', '', basename( $file ) ) );
			include_once( $file );
			$class = new $classname();

			$actions = isset( $class->actions ) ? $class->actions : $this->actions;
			$filters = isset( $class->filters ) ? $class->filters : $this->filters;

			if ( isset( $class->name ) ) {
				self::$plugins[ $classname ]['name'] = $class->name;
			}

			if ( isset( $class->url ) ) {
				self::$plugins[ $classname ]['url'] = $class->url;
			}

			$settings = self::get_settings();
			if ( ! $settings['enabled'] ) {
				return false;
			}

			if ( ! empty( $actions ) ) {
				$this->add_actions( $class, $actions );
			}
			if ( ! empty( $filters ) ) {
				$this->add_filters( $class, $filters );
			}
		}
	}

	/**
	 * Return list of plugins to be used on admin page.
	 */
	public static function get_plugins() {
		$plugins = self::$plugins;
		$output  = '';
		foreach ( $plugins as $plugin ) {
			if ( isset( $plugin['url'] ) ) {
				$output .= sprintf( '<li><a target="_blank" href="%s">%s</a>', $plugin['url'], $plugin['name'] );
			} else {
				$output .= sprintf( '<li>%s</li>', $plugin['name'] );
			}
		}
		return '<ul>' . $output . '</ul>';
	}

	function add_actions( $class, $actions ) {
		foreach ( $actions as $action ) {
			add_action( $action, array( $class, 'run' ) );
		}
	}

	function add_filters( $class, $filters ) {
		foreach ( $filters as $filter ) {
			add_action( $filter, array( $class, 'filters' ) );
		}
	}

	public static function define( $define, $setting = true ) {
		if ( ! defined( $define ) ) {
			define( $define, $setting );
		}
	}
}
new Plugin;
