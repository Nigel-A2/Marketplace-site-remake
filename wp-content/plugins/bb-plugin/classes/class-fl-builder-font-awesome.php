<?php
/**
 * Font Awesome Support Class
 *
 * @since 2.4
 */

use function FortAwesome\fa;

final class FLBuilderFontAwesome {

	static $cache_slug = '_fl_builder_font_awesome_data';
	/**
	 * @since 2.4
	 * @return void
	 */
	static public function init() {
		add_action( 'font_awesome_preferences', array( __CLASS__, 'register_plugin' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'fa_official_support' ), 999999 );
		add_action( 'fl_builder_after_sanity_checks', array( __CLASS__, 'sanity_checks' ) );
		add_filter( 'fl_builder_ui_js_config', array( __CLASS__, 'js_config' ) );
		add_filter( 'fl_enable_fa5_pro', array( __CLASS__, 'is_pro_enabled' ) );
		FLBuilderAJAX::add_action( 'query_icons', __CLASS__ . '::query_icons', array( 'text' ) );
		FLBuilderAJAX::add_action( 'recent_icons', __CLASS__ . '::recent_icons', array( 'icon' ) );
	}

	public static function recent_icons( $icon ) {
		$icons   = get_option( 'fl_plugin_recent_icons', array() );
		$icons[] = $icon;
		$icons   = array_reverse( array_unique( $icons ) );
		update_option( 'fl_plugin_recent_icons', $icons );
		echo json_encode( $icons );
		exit;
	}

	public static function query_icons( $text ) {
		if ( '' === $text || ! $text ) {
			return '{}';
		}
		$output    = array();
		$icons     = array();
		$icon_sets = FLBuilderIcons::get_sets();
		$enabled   = FLBuilderModel::get_enabled_icons();
		$styles    = array();

		foreach ( (array) $enabled as $set ) {
			switch ( $set ) {
				case 'font-awesome-5-solid':
					$styles[] = 'solid';
					break;
				case 'font-awesome-5-regular':
					$styles[] = 'regular';
					break;
				case 'font-awesome-5-light':
					$styles[] = 'light';
					break;
				case 'font-awesome-5-brands':
					$styles[] = 'brands';
					break;
				case 'font-awesome-5-duotone':
					$styles[] = 'duotone';
					break;
			}
		}

		if ( self::is_installed() ) {
			$version    = fa()->version();
			$membership = ( fa()->pro() ) ? '' : ',membership{free}';

			$query = sprintf( '{search(version: "%s", query: "%s") { id,label,styles%s }}', fa()->version(), $text, $membership );

			$result = json_decode( fa()->query( $query ) );

			if ( isset( $result->data ) && isset( $result->data->search ) ) {
				foreach ( (array) $result->data->search as $icon ) {
					if ( ! fa()->pro() ) {
						if ( empty( $icon->membership->free ) ) {
							continue;
						} else {
							$icon->styles = $icon->membership->free;
						}
					}
					foreach ( $icon->styles as $k => $style ) {
						if ( ! in_array( $style, $styles ) ) {
							unset( $icon->styles[ $k ] );
						}
					}
					$icons[] = array(
						'tag'    => 'fa-' . $icon->id,
						'label'  => $icon->label,
						'styles' => $icon->styles,
					);
				}
			}
			if ( count( $icons ) > 0 ) {
				$output[] = array(
					'name' => 'Font Awesome',
					'slug' => 'font-awesome',
					'data' => $icons,
				);
			}
			unset( $icon_sets['font-awesome-5-solid'] );
			unset( $icon_sets['font-awesome-5-regular'] );
			unset( $icon_sets['font-awesome-5-brands'] );
			unset( $icon_sets['font-awesome-5-light'] );
			unset( $icon_sets['font-awesome-5-duotone'] );
		}

		if ( ! in_array( 'font-awesome-kit', $enabled ) ) {
			unset( $icon_sets['font-awesome-kit'] );
		}

		foreach ( $icon_sets as $set ) {

			$icons = array();
			foreach ( $set['icons'] as $icon ) {
				if ( strstr( $icon, $text ) ) {
					$icons[] = array(
						'tag'    => $icon,
						'label'  => $icon,
						'styles' => array( 'legacy' ),
					);
				}
			}
			if ( ! empty( $icons ) ) {
				$output[] = array(
					'name'   => $set['name'],
					'prefix' => $set['prefix'],
					'slug'   => '',
					'data'   => $icons,
				);
			}
		}
		echo json_encode( $output );
		exit;
	}

	/**
	 * Register this plugin for official FA support.
	 */
	public static function register_plugin() {
		$args = apply_filters( 'fl_builder_font_awesome_register_args', array(
			'name'       => __( 'Beaver Builder', 'fl-builder' ),
			'v4Compat'   => false,
			'technology' => 'webfont',
		) );
		fa()->register( $args );
	}

	/**
	 * Basic check show warning on BB admin settings.
	 */
	public static function sanity_checks() {

		if ( self::is_installed() && 'svg' === fa()->technology() ) {
			FLBuilderAdminSettings::add_error( self::error_text() );
		}
	}

	/**
	 * Filter our main JS config set fontAwesome var
	 */
	public static function js_config( $config ) {
		$config['fontAwesome'] = self::is_installed() ? fa()->technology() : '';
		return $config;
	}

	/**
	 * Dequeue our FA scripts/styles if enqueued
	 * @since 2.4
	 */
	public static function fa_official_support() {

		/*
		When BB UI is open:
		If there is no custom kit, or its disabled load regular css.

		If there is a kit and its enabled:
			if it has icons we need to load the JS
		*/
		$kit_enabled = false;
		$enabled     = FLBuilderModel::get_enabled_icons();

		if ( in_array( 'font-awesome-kit', $enabled ) ) {
			$kit_enabled = true;
		}

		if ( self::is_installed() && ( wp_script_is( 'font-awesome-official', 'enqueued' ) || wp_style_is( 'font-awesome-official', 'enqueued' ) ) ) {
			wp_dequeue_style( 'font-awesome' );
			wp_dequeue_style( 'font-awesome-5' );
			wp_deregister_style( 'font-awesome' );
			wp_deregister_style( 'font-awesome-5' );
		}
	}

	public static function is_pro_enabled( $enabled ) {

		if ( ! function_exists( 'FortAwesome\fa' ) ) {
			return $enabled;
		}
		if ( self::is_installed() && fa()->pro() ) {
			return true;
		}

		return $enabled;
	}

	/**
	 * Check if font awesome plugin is available.
	 */
	public static function is_installed() {
		return class_exists( 'FortAwesome\FontAwesome_Loader' ) && function_exists( 'FortAwesome\fa' );
	}

	/**
	 * Error text used in wp-admin and modal popup in buildef UI
	 */
	public static function error_text() {
		return __( 'You appear to have the font awesome plugin configured to use svg icons, this currently is incompatible with Beaver Builder. You must switch to a webfont set/kit.', 'fl-builder' );
	}

	/**
	 * Latest supported version.
	 */
	public static function latest_supported() {
		return '5.15.4';
	}

	public static function get_kit_icons() {
		$icons  = array();
		$result = self::get_kit_data();

		if ( ! $result ) {
			return array();
		}

		$data = $result->data->me->kit->iconUploads;
		foreach ( $data as $icon ) {
			$icons[] = 'fa-' . $icon->name;
		}
		return $icons;
	}

	public static function get_kit_data() {

		if ( ! self::is_installed() ) {
			return false;
		}
		$token = fa()->options()['kitToken'];

		if ( ! $token ) {
			return false;
		}
		$result = get_transient( self::$cache_slug );
		if ( false === $result ) {
			// It wasn't there, so regenerate the data and save the transient
			$query  = sprintf( '{ me { kit(token:"%s") { name token iconUploads { name } } } }', $token );
			$result = json_decode( fa()->query( $query ) );
			set_transient( self::$cache_slug, $result );
		}
		return $result;
	}

	static function get_fa_data() {

		if ( ! self::is_installed() ) {
			return false;
		}

		$kit = self::get_kit_data();

		$data = array(
			'pro'        => array(
				'name'  => __( 'Pro Icons', 'fl-builder' ),
				'value' => ( fa()->pro() ) ? __( 'Yes', 'fl-builder' ) : __( 'No', 'fl-builder' ),
			),
			'technology' => array(
				'name'  => __( 'Technology', 'fl-builder' ),
				'value' => fa()->technology(),
			),
			'version'    => array(
				'name'  => __( 'Version', 'fl-builder' ),
				'value' => fa()->version(),
			),
			'version4'   => array(
				'name'  => __( 'V4 Compatibility Mode', 'fl-builder' ),
				'value' => ( fa()->v4_compatibility() ) ? __( 'Enabled', 'fl-builder' ) : __( 'Disabled', 'fl-builder' ),
			),
		);

		if ( ! $kit ) {
			$data['cdn'] = array(
				'name'  => __( 'Using CDN', 'fl-builder' ),
				'value' => __( 'Yes', 'fl-builder' ),
			);
		} else {
			$data['kit'] = array(
				'name'  => __( 'Using Kit', 'fl-builder' ),
				'value' => $kit->data->me->kit->name,
			);

			// translators: %s, number of icons
			$number           = sprintf( _n( '%s custom icon', '%s custom icons', count( self::get_kit_icons() ), 'fl-builder' ), number_format_i18n( count( self::get_kit_icons() ) ) );
			$data['kitcount'] = array(
				'name'  => __( 'Custom Icons', 'fl-builder' ),
				'value' => sprintf( 'Kit contains %s', $number ),
			);

			// check domain is valid
			$result = wp_remote_get(
				sprintf( 'https://kit.fontawesome.com/%s.js', $kit->data->me->kit->token ),
				array(
					'headers' => array(
						'referer' => home_url(),
					),
				)
			);

			if ( is_array( $result ) && 403 === $result['response']['code'] ) {
				$data['kit403'] = array(
					'name'  => __( 'Issue Detected', 'fl-builder' ),
					/* translators: %s: kit name */
					'value' => sprintf( __( 'This domain appears to be blocked for kit %s', 'fl-builder' ), $kit->data->me->kit->name ),
				);
			}
		}
		return $data;
	}

	static function clear_cache() {
		delete_transient( self::$cache_slug );
	}
}
/**
 * Needs to be on wp_loaded as that is where FA plugin is started.
 */
add_action( 'wp_loaded', function() {
	FLBuilderFontAwesome::init();
});
add_action( 'update_option_font-awesome', array( 'FLBuilderFontAwesome', 'clear_cache' ) );
