<?php

if ( ! class_exists( 'FLBuilderLoader' ) ) {

	/**
	 * Responsible for setting up builder constants, classes and includes.
	 *
	 * @since 1.8
	 */
	final class FLBuilderLoader {

		/**
		 * Load the builder if it's not already loaded, otherwise
		 * show an admin notice.
		 *
		 * @since 1.8
		 * @return void
		 */
		static public function init() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$lite_dirname   = 'beaver-builder-lite-version';
			$lite_active    = is_plugin_active( $lite_dirname . '/fl-builder.php' );
			$plugin_dirname = basename( dirname( dirname( __FILE__ ) ) );

			if ( $lite_active && $plugin_dirname != $lite_dirname ) {
				add_action( 'admin_init', function() {
					deactivate_plugins( array( 'beaver-builder-lite-version/fl-builder.php' ), false, is_network_admin() );
				});
				return;
			} elseif ( class_exists( 'FLBuilder' ) ) {
				add_action( 'admin_notices', __CLASS__ . '::double_install_admin_notice' );
				add_action( 'network_admin_notices', __CLASS__ . '::double_install_admin_notice' );
				return;
			}

			self::define_constants();
			self::load_files();
			self::check_permissions();
		}

		/**
		 * Define builder constants.
		 *
		 * @since 1.8
		 * @return void
		 */
		static private function define_constants() {
			define( 'FL_BUILDER_VERSION', '2.5.4.3' );
			define( 'FL_BUILDER_FILE', trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'fl-builder.php' );
			define( 'FL_BUILDER_DIR', plugin_dir_path( FL_BUILDER_FILE ) );
			define( 'FL_BUILDER_URL', esc_url( plugins_url( '/', FL_BUILDER_FILE ) ) );
			define( 'FL_BUILDER_LITE', false );
			define( 'FL_BUILDER_SUPPORT_URL', 'https://www.wpbeaverbuilder.com/support/' ); // Deprecated, do not use.
			define( 'FL_BUILDER_UPGRADE_URL', 'https://www.wpbeaverbuilder.com/' ); // Deprecated, do not use.
			define( 'FL_BUILDER_STORE_URL', 'https://www.wpbeaverbuilder.com/' );
			define( 'FL_BUILDER_DEMO_DOMAIN', 'demos.wpbeaverbuilder.com' );
			define( 'FL_BUILDER_DEMO_URL', 'http://demos.wpbeaverbuilder.com' );
			define( 'FL_BUILDER_OLD_DEMO_URL', 'http://demos.fastlinemedia.com' );
			define( 'FL_BUILDER_DEMO_CACHE_URL', 'http://demos.wpbeaverbuilder.com/wp-content/uploads/bb-plugin/cache/' );
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.8
		 * @return void
		 */
		static private function load_files() {

			/* Classes */
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-filesystem.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-admin.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-admin-pointers.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-admin-posts.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-admin-settings.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-ajax.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-ajax-layout.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-art.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-auto-suggest.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-color.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-css.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-export.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-extensions.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-fonts.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-history-manager.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-debug.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-usage.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-icons.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-iframe-preview.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-import.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-loop.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-model.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-module.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-photo.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-revisions.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-services.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-settings-compat.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-shortcodes.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-timezones.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-ui-content-panel.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-ui-settings-forms.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-notifications.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-update.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-user-access.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-user-settings.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-utils.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-wpml.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-seo.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-privacy.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-settings-presets.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-compatibility.php';
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-font-awesome.php';

			/* WP CLI Commands */
			if ( defined( 'WP_CLI' ) ) {
				require_once FL_BUILDER_DIR . 'classes/class-fl-builder-wpcli-command.php';
			}

			/* WP Blocks Support */
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-wp-blocks.php';

			/* Includes */
			require_once FL_BUILDER_DIR . 'includes/compatibility.php';

			/* Updater */
			if ( file_exists( FL_BUILDER_DIR . 'includes/updater/updater.php' ) ) {
				require_once FL_BUILDER_DIR . 'includes/updater/updater.php';
			}
		}

		/**
		 * Checks to see if we can write to files and shows
		 * an admin notice if we can't.
		 *
		 * @since 1.8.2
		 * @access private
		 * @return void
		 */
		static private function check_permissions() {
			if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], array( 'fl-builder-settings', 'fl-builder-multisite-settings' ) ) ) {

				$wp_upload_dir = wp_upload_dir( null, false );
				$bb_upload_dir = FLBuilderModel::get_upload_dir();

				if ( ! fl_builder_filesystem()->is_writable( $wp_upload_dir['basedir'] ) || ! fl_builder_filesystem()->is_writable( $bb_upload_dir['path'] ) ) {
					add_action( 'admin_notices', __CLASS__ . '::permissions_admin_notice' );
					add_action( 'network_admin_notices', __CLASS__ . '::permissions_admin_notice' );
				}
			}
		}

		/**
		 * Shows an admin notice if we can't write to files.
		 *
		 * @since 1.8.2
		 * @return void
		 */
		static public function permissions_admin_notice() {
			$message = __( 'Beaver Builder may not be functioning correctly as it does not have permission to write files to the WordPress uploads directory on your server. Please update the WordPress uploads directory permissions before continuing or contact your host for assistance.', 'fl-builder' );

			self::render_admin_notice( $message, 'error' );
		}

		/**
		 * Shows an admin notice if another version of the builder
		 * has already been loaded before this one.
		 *
		 * @since 1.8
		 * @return void
		 */
		static public function double_install_admin_notice() {
			/* translators: %s: plugins page link */
			$message = __( 'You currently have two versions of Beaver Builder active on this site. Please <a href="%s">deactivate one</a> before continuing.', 'fl-builder' );

			self::render_admin_notice( sprintf( $message, admin_url( 'plugins.php' ) ), 'error' );
		}

		/**
		 * Renders an admin notice.
		 *
		 * @since 1.8.2
		 * @access private
		 * @param string $message
		 * @param string $type
		 * @return void
		 */
		static private function render_admin_notice( $message, $type = 'update' ) {
			if ( ! is_admin() ) {
				return;
			} elseif ( ! is_user_logged_in() ) {
				return;
			} elseif ( ! current_user_can( 'update_plugins' ) ) {
				return;
			}

			echo '<div class="' . $type . '">';
			echo '<p>' . $message . '</p>';
			echo '</div>';
		}
	}
}

FLBuilderLoader::init();
