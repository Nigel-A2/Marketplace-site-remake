<?php
/**
 * Sends opt-in usage data
 * @since 2.1
 */
final class FLBuilderUsage {

	protected static $url = 'https://stats.wpbeaverbuilder.com/';

	protected static $seconds = 604800;

	public static function init() {

		$hook = is_network_admin() ? 'network_admin_notices' : 'admin_notices';

		add_action( 'admin_init', array( 'FLBuilderUsage', 'enable_disable' ) );
		add_action( 'init', array( 'FLBuilderUsage', 'set_schedule' ) );
		add_action( $hook, array( 'FLBuilderUsage', 'render_notification' ) );
		add_action( 'fl_builder_usage_event', array( 'FLBuilderUsage', 'send_stats' ) );
		add_action( 'wp_ajax_fl_usage_toggle', array( 'FLBuilderUsage', 'callback' ) );

	}

	public static function callback() {

		$enable = intval( $_POST['enable'] );

		if ( wp_verify_nonce( $_POST['_wpnonce'], 'fl-usage' ) ) {
			update_site_option( 'fl_builder_usage_enabled', $enable );
		}

		wp_die();
	}

	public static function browser_stats( $browser_data ) {

		update_user_meta( get_current_user_id(), 'fl_builder_browser_stats', $browser_data );
		exit();
	}

	public static function scripts() {
		wp_enqueue_style( 'fl-builder-admin-usage', FL_BUILDER_URL . 'css/fl-builder-admin-usage.css', array(), FL_BUILDER_VERSION );
		wp_enqueue_script( 'fl-builder-admin-usage', FL_BUILDER_URL . 'js/fl-builder-admin-usage.js', array( 'jquery' ), FL_BUILDER_VERSION );
	}

	/**
	 * Add scheduled event
	 * @since 2.1
	 */
	public static function set_schedule() {

		if ( '1' == get_site_option( 'fl_builder_usage_enabled', false ) ) {
			if ( ! wp_next_scheduled( 'fl_builder_usage_event' ) ) {
				wp_schedule_single_event( time() + self::$seconds, 'fl_builder_usage_event' );
			}
		}
	}

	/**
	 * Send stats callback
	 * @since 2.1
	 */
	public static function send_stats() {

		if ( ! get_site_option( 'fl_builder_usage_enabled', false ) ) {
			return false;
		}
		$request = wp_remote_post( self::$url, array(
			'body'    => json_encode( self::get_data() ),
			'timeout' => 30,
		) );
	}

	/**
	 * Enable/disable
	 * @since 2.1
	 */
	public static function enable_disable() {

		if ( isset( $_GET['fl_usage'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'stats_enable' ) ) {
			update_site_option( 'fl_builder_usage_enabled', $_GET['fl_usage'] );
		}
	}

	/**
	 * Render admin admin notice
	 * @since 2.1
	 */
	public static function render_notification() {

		if ( ! self::notification_enabled() ) {
			return false;
		}

		$btn = sprintf( '<div class="buttons"><span class="button button-primary enable-stats">%s</span>&nbsp;<span class="button disable-stats">%s</span>%s</div>',
			__( "Sure, I'll help", 'fl-builder' ),
			__( 'No, Thank You', 'fl-builder' ),
			wp_nonce_field( 'fl-usage', '_wpnonce', false )
		);

		$message = sprintf(
			/* translators: %s: branded builder name */
			__( 'Would you like to help us improve %s by sending anonymous usage data?', 'fl-builder' ),
			FLBuilderModel::get_branding()
		);

		echo '<div class="notice notice-info">';

		echo '<div class="fl-usage">';

		echo '<p>';

		printf( '%s %s', $message, $btn );

		echo '</p>';

		printf( '</div>%s</div>', FLBuilderUsage::data_demo() );

	}

	/**
	 * Whether to show the stats settings in bb admin.
	 */
	public static function show_settings() {

		// super admin and network settings
		if ( is_multisite() && is_super_admin() && is_network_admin() ) {
			return true;
		}

		// single site admin
		if ( ! is_multisite() && is_super_admin() ) {
			return true;
		}

		return false;
	}

	/**
	 * Is notification enabled
	 * @since 2.1
	 * @return bool
	 */
	private static function notification_enabled() {

		global $pagenow;
		$screen = get_current_screen();
		$show   = false;

		if ( 'fl-builder-template' == $screen->post_type ) {
			$show = true;
		}

		if ( 'fl-theme-layout' == $screen->post_type ) {
			$show = true;
		}

		if ( 'options-general.php' == $pagenow && isset( $_GET['page'] ) && 'fl-builder-settings' == $_GET['page'] ) {
			$show = true;
		}

		if ( 'dashboard-network' == $screen->id ) {
			$show = true;
		}

		if ( '0' === get_site_option( 'fl_builder_usage_enabled' ) ) {
			$show = false;
		}

		if ( ! is_super_admin() ) {
			$show = false;
		}

		return ( $show && ! get_site_option( 'fl_builder_usage_enabled' ) ) ? true : false;
	}

	/**
	 * Show a user what kind of data we are collecting.
	 * @since 2.1
	 * @return string
	 */
	public static function data_demo() {

		self::scripts();

		$data     = self::get_data( true );
		$output   = '';
		$txt      = '';
		$settings = array(
			'server'   => array(
				'name' => __( 'Server Type', 'fl-builder' ),
				'data' => $data['data']['server'],
			),
			'php'      => array(
				'name' => __( 'PHP Version', 'fl-builder' ),
				'data' => $data['data']['php'],
			),
			'wp'       => array(
				'name' => __( 'WP Version', 'fl-builder' ),
				'data' => $data['data']['wp'],
			),
			'mu'       => array(
				'name' => __( 'WP Multisite', 'fl-builder' ),
				'data' => $data['data']['multisite'],
			),
			'locale'   => array(
				'name' => __( 'Locale', 'fl-builder' ),
				'data' => $data['data']['locale'],
			),
			'plugins'  => array(
				'name' => __( 'Plugins Count', 'fl-builder' ),
				'data' => $data['data']['plugins'],
			),
			'modules'  => array(
				'name' => __( 'Modules Used', 'fl-builder' ),
				'data' => __( 'Which modules are used and how many times.', 'fl-builder' ),
			),
			'settings' => array(
				'name' => __( 'Builder Settings', 'fl-builder' ),
				'data' => __( 'UI theme, pinned settings etc.', 'fl-builder' ),
			),
		);

		foreach ( $settings as $k => $data ) {
			$txt .= sprintf( '<span class="usage-demo-left">%s</span><span class="usage-demo-right">: %s</span><br />', $data['name'], $data['data'] );
		}

		$output = sprintf( '<div class="usage-demo"><a class="stats-info" href="#">%s</a><div class="stats-info-data"><p>%s</p><p><em>%s</em></p></div></div>',
			__( 'What kind of info will we collect?', 'fl-builder' ),
			$txt,
			__( 'We will never collect any private data such as IP, email addresses or usernames.', 'fl-builder' )
		);

		return $output;
	}

	/**
	 * Gather stats to send
	 * @since 2.1
	 * @return array
	 */
	public static function get_data( $demo = false ) {

		global $wp_version, $wpdb;

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$data                             = array(
			'modules' => array(),
			'license' => array(),
			'themer'  => array(
				'header'   => 0,
				'footer'   => 0,
				'part'     => 0,
				'404'      => 0,
				'singular' => 0,
			),
			'pinned'  => array(
				'left'     => 0,
				'right'    => 0,
				'unpinned' => 0,
			),
		);
		$users                            = count_users();
		$plugins_data                     = get_plugins();
		$data['plugins']                  = count( $plugins_data );
		$data['plugins_active']           = 0;
		$data['active_plugins_installed'] = array();

		foreach ( (array) $plugins_data as $plugin_slug => $plugin ) {
			if ( is_plugin_active( $plugin_slug ) ) {
				$data['active_plugins_installed'][] = array(
					'name'    => $plugin['Name'],
					'version' => $plugin['Version'],
					'slug'    => $plugin_slug,
				);
				$data['plugins_active'] ++;
			}
		}

		if ( false === $demo ) {
			/**
			* Setup an array of post types to query
			*/
			$post_types = get_post_types( array(
				'public'   => true,
				'_builtin' => true,
			) );

			if ( isset( $post_types['attachment'] ) ) {
				unset( $post_types['attachment'] );
			}
			//	$post_types['fl-builder-template'] = 'fl-builder-template';

			/**
			* Get a count of all posts/pages that are *not* builder enabled.
			*/
			$args                = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'meta_query'     => array(
					'key'     => '_fl_builder_enabled',
					'value'   => '1',
					'compare' => '!=',
				),
				'posts_per_page' => -1,
			);
			$query               = new WP_Query( $args );
			$data['not-enabled'] = count( $query->posts );

			/**
			* Get a count of all posts pages that are using the builder.
			*/
			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'meta_key'       => '_fl_builder_enabled',
				'meta_value'     => '1',
				'posts_per_page' => -1,
			);

			$query           = new WP_Query( $args );
			$data['enabled'] = count( $query->posts );

			/**
			* Using the array of pages/posts using builder get a list of all used modules
			*/
			if ( is_array( $query->posts ) && ! empty( $query->posts ) ) {
				foreach ( $query->posts as $post ) {
					$meta = get_post_meta( $post->ID, '_fl_builder_data', true );
					foreach ( (array) $meta as $node_id => $node ) {
						if ( @isset( $node->type ) && 'module' == $node->type ) { // @codingStandardsIgnoreLine
							if ( ! isset( $data['modules'][ $node->settings->type ] ) ) {
								$data['modules'][ $node->settings->type ] = 1;
							} else {
								$data['modules'][ $node->settings->type ] ++;
							}
						}
					}
				}
			}

			// themer settings.
			$args                    = array(
				'post_type'      => 'fl-theme-layout',
				'post_status'    => 'publish',
				'meta_key'       => '_fl_builder_enabled',
				'meta_value'     => '1',
				'posts_per_page' => -1,
			);
			$query                   = new WP_Query( $args );
			$data['themer']['total'] = count( $query->posts );
			if ( is_array( $query->posts ) && ! empty( $query->posts ) ) {
				foreach ( $query->posts as $post ) {
					$meta = get_post_meta( $post->ID );
					if ( isset( $meta['_fl_theme_layout_type'] ) ) {
						if ( ! isset( $data['themer'][ $meta['_fl_theme_layout_type'][0] ] ) ) {
							$data['themer'][ $meta['_fl_theme_layout_type'][0] ] = 1;
						} else {
							$data['themer'][ $meta['_fl_theme_layout_type'][0] ] ++;
						}
					}
				}
			}

			/**
			* Find all users that are using the builder.
			*/
			$args          = array(
				'meta_key'     => 'fl_builder_user_settings',
				'meta_value'   => 'null',
				'meta_compare' => '!=',
			);
			$query         = new WP_User_Query( $args );
			$user_settings = array();
			foreach ( $query->results as $user ) {
				$meta                       = get_user_meta( $user->ID, 'fl_builder_user_settings', true );
				$user_settings[ $user->ID ] = $meta;
			}

			$data['user_settings'] = $user_settings;

			$args  = array(
				'meta_key'     => 'fl_builder_browser_stats',
				'meta_value'   => 'null',
				'meta_compare' => '!=',
			);
			$query = new WP_User_Query( $args );

			$browsers = array();

			foreach ( $query->results as $user ) {
				$meta                  = get_user_meta( $user->ID, 'fl_builder_browser_stats', true );
				$browsers[ $user->ID ] = $meta;
			}
			$data['browsers'] = $browsers;
		}

		/**
		* General data
		*/
		$data['server']     = $_SERVER['SERVER_SOFTWARE'];
		$data['database']   = ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : 'Unknown' );
		$data['multisite']  = is_multisite() ? 'Yes' : 'No';
		$data['subsites']   = is_multisite() ? get_blog_count() : '';
		$data['locale']     = get_locale();
		$data['users']      = $users['total_users'];
		$data['php']        = phpversion();
		$data['wp']         = $wp_version;
		$data['fl-builder'] = FL_BUILDER_VERSION;
		$data['fl-theme']   = ( defined( 'FL_THEME_VERSION' ) ) ? FL_THEME_VERSION : false;
		$data['fl-themer']  = ( defined( 'FL_THEME_BUILDER_VERSION' ) ) ? FL_THEME_BUILDER_VERSION : false;

		$settings_orig = FLBuilderModel::get_global_settings();

		$settings = clone $settings_orig;

		// we dont need these
		unset( $settings->js );
		unset( $settings->css );

		foreach ( $settings as $k => $setting ) {
			$data['settings'][ $k ] = $setting;
		}

		$theme = wp_get_theme();
		if ( $theme->get( 'Template' ) ) {
			$parent              = wp_get_theme( $theme->get( 'Template' ) );
			$data['theme']       = $parent->get( 'Name' );
			$data['theme_child'] = $theme->get( 'Name' );
		} else {
			$data['theme'] = $theme->get( 'Name' );
		}

		if ( class_exists( 'FLUpdater' ) && false == $demo ) {

			$subscription = FLUpdater::get_subscription_info();

			if ( ! $subscription->active ) {
				$data['license'] = 'none';
			} else {
				$data['license'] = array();
				foreach ( (array) $subscription->subscriptions as $subscription ) {
					if ( false !== strpos( $subscription->name, 'Beaver Builder' ) ) {
						$data['license']['bb-plugin'] = $subscription->name;
					}
					if ( 'Beaver Themer Plugin' == $subscription->name ) {
						$data['license']['bb-themer'] = $subscription->name;
					}
				}
			}
		} else {
			$data['license'] = 'none';
		}
		$output = array(
			'id'   => md5( get_bloginfo( 'url' ) . get_bloginfo( 'admin_email' ) ),
			'data' => $data,
		);
		return $output;
	}
}
FLBuilderUsage::init();
