<?php

/**
 * Main builder admin class.
 *
 * @since 1.0
 */
final class FLBuilderAdmin {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		$basename = plugin_basename( FL_BUILDER_FILE );

		// Activation
		register_activation_hook( FL_BUILDER_FILE, __CLASS__ . '::activate' );

		// Actions
		add_action( 'admin_init', __CLASS__ . '::show_activate_notice' );
		add_action( 'admin_init', __CLASS__ . '::sanity_checks' );
		add_action( 'fl_after_license_form', __CLASS__ . '::check_curl', 11 );

		// Filters
		add_filter( 'plugin_action_links_' . $basename, __CLASS__ . '::render_plugin_action_links' );

	}

	/**
	 * Called on plugin activation and checks to see if the correct
	 * WordPress version is installed and multisite is supported. If
	 * all checks are passed the install method is called.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function activate() {
		global $wp_version;

		// Check for WordPress 3.5 and above.
		if ( ! version_compare( $wp_version, '4.6', '>=' ) ) {
			self::show_activate_error( __( 'The <strong>Beaver Builder</strong> plugin requires WordPress version 4.6 or greater. Please update WordPress before activating the plugin.', 'fl-builder' ) );
		}

		/**
		 * Allow extensions to hook activation.
		 * @see fl_builder_activate
		 */
		$activate = apply_filters( 'fl_builder_activate', true );

		// Should we continue with activation?
		if ( $activate ) {

			// Check for multisite.
			if ( is_multisite() ) {
				$url = FLBuilderModel::get_upgrade_url( array(
					'utm_medium'   => 'bb-pro',
					'utm_source'   => 'plugins-admin-page',
					'utm_campaign' => 'no-multisite-support',
				) );
				/* translators: %s: upgrade url */
				self::show_activate_error( sprintf( __( 'This version of the <strong>Beaver Builder</strong> plugin is not compatible with WordPress Multisite. <a%s>Please upgrade</a> to the Multisite version of this plugin.', 'fl-builder' ), ' href="' . $url . '" target="_blank"' ) );
			}

			// Success! Run the install.
			self::install();

			// Trigger the activation notice.
			self::trigger_activate_notice();

			/**
			 * Allow add-ons to hook into activation.
			 * @see fl_builder_activated
			 */
			do_action( 'fl_builder_activated' );

			// Flush the rewrite rules.
			flush_rewrite_rules();
		}
	}

	/**
	 * Restrict builder settings accessibility based on the defined capability.
	 *
	 * @since 2.0.6
	 * @return void
	 */
	static public function current_user_can_access_settings() {
		return current_user_can( self::admin_settings_capability() );
	}

	/**
	 * Define capability.
	 *
	 * @since 2.0.6
	 * @return string
	 */
	static public function admin_settings_capability() {
		/**
		 * Default admin settings capability ( manage_options )
		 * @see fl_builder_admin_settings_capability
		 */
		return apply_filters( 'fl_builder_admin_settings_capability', 'manage_options' );
	}

	/**
	 * Show a message if there is an activation error and
	 * deactivates the plugin.
	 *
	 * @since 1.0
	 * @param string $message The message to show.
	 * @return void
	 */
	static public function show_activate_error( $message ) {
		deactivate_plugins( FLBuilderModel::plugin_basename(), false, is_network_admin() );

		die( $message );
	}

	/**
	 * @since 2.1.3
	 */
	static public function sanity_checks() {

		if ( true !== FL_BUILDER_LITE ) {
			// fetch the plugin install folder this should be bb-plugin
			$folder = rtrim( FLBuilderModel::plugin_basename(), '/fl-builder.php' );

			if ( 'bb-plugin' != $folder ) {
				/* translators: %s: folder path */
				$error = sprintf( __( 'Install Error! We detected that Beaver Builder appears to be installed in a folder called <kbd>%s</kbd>.<br />For automatic updates to work the plugin must be installed in the folder <kbd>bb-plugin</kbd>.', 'fl-builder' ), $folder );
				FLBuilderAdminSettings::add_error( $error );
			}
		}

		//Check for one.com htaccess file in uploads that breaks everything.
		$upload_dir = wp_upload_dir();
		$file       = trailingslashit( $upload_dir['basedir'] ) . '.htaccess';
		if ( file_exists( $file ) ) {
			$htaccess = file_get_contents( $file );
			if ( false !== strpos( $htaccess, 'Block javascript except for visualcomposer (VC) plugin' ) ) {
				FLBuilderAdminSettings::add_error(
				/* translators: %s formatted .htaccess */
				sprintf( __( 'Install Error! You appear to have a %s file in your uploads folder that will block all javascript files resulting in 403 errors. If you did not add this file please consult your host.', 'fl-builder' ), '<code>.htaccess</code>' ) );
			}
		}

		do_action( 'fl_builder_after_sanity_checks' );
	}

	/**
	 * Sets the transient that triggers the activation notice
	 * or welcome page redirect.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function trigger_activate_notice() {
		if ( self::current_user_can_access_settings() ) {
			set_transient( '_fl_builder_activation_admin_notice', true, 30 );
		}
	}

	/**
	 * Shows the activation success message or redirects to the
	 * welcome page.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function show_activate_notice() {
		// Bail if no activation transient is set.
		if ( ! get_transient( '_fl_builder_activation_admin_notice' ) ) {
			return;
		}

		// Delete the activation transient.
		delete_transient( '_fl_builder_activation_admin_notice' );

		if ( isset( $_GET['activate-multi'] ) || is_multisite() ) {
			// Show the notice if we are activating multiple plugins or on multisite.
			add_action( 'admin_notices', __CLASS__ . '::activate_notice' );
			add_action( 'network_admin_notices', __CLASS__ . '::activate_notice' );
		} else {
			// Redirect to the welcome page.
			wp_safe_redirect( add_query_arg( array(
				'page' => 'fl-builder-settings',
			), admin_url( 'options-general.php' ) ) );
		}
	}

	/**
	 * Shows the activation success message.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function activate_notice() {
		if ( FL_BUILDER_LITE !== true ) {
			$hash = '#license';
			/* translators: %s: link to licence page */
			$message = __( 'Beaver Builder activated! <a%s>Click here</a> to enable remote updates.', 'fl-builder' );
		} else {
			$hash = '#welcome';
			/* translators: %s: link to welcome page */
			$message = __( 'Beaver Builder activated! <a%s>Click here</a> to get started.', 'fl-builder' );
		}

		/**
		 * Url to redirect to on activation
		 * @see fl_builder_activate_redirect_url
		 */
		$url = apply_filters( 'fl_builder_activate_redirect_url', admin_url( '/options-general.php?page=fl-builder-settings' . $hash ) );

		echo '<div class="updated" style="background: #d3ebc1;">';
		echo '<p><strong>' . sprintf( $message, ' href="' . esc_url( $url ) . '"' ) . '</strong></p>';
		echo '</div>';
	}

	/**
	 * Installs the builder upon successful activation.
	 * Currently not used but may be in the future.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function install() {}

	/**
	 * Uninstalls the builder.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function uninstall() {
		FLBuilderModel::uninstall_database();
	}

	/**
	 * Renders the link for the row actions on the plugins page.
	 *
	 * @since 1.0
	 * @param array $actions An array of row action links.
	 * @return array
	 */
	static public function render_plugin_action_links( $actions ) {

		/**
		 * Some bad plugins set $actions to '' or false to remove all plugin actions
		 * when it should be an empty array, in later PHP versions this results in a fatal error.
		 */
		if ( ! is_array( $actions ) ) {
			$actions = array();
		}
		if ( FL_BUILDER_LITE === true ) {
			$url       = FLBuilderModel::get_upgrade_url( array(
				'utm_medium'   => 'bb-lite',
				'utm_source'   => 'plugins-admin-page',
				'utm_campaign' => 'plugins-admin-upgrade',
			) );
			$actions[] = '<a href="' . $url . '" style="color:#3db634;" target="_blank">' . _x( 'Upgrade', 'Plugin action link label.', 'fl-builder' ) . '</a>';
		}

		if ( ! FLBuilderModel::is_white_labeled() ) {
			$url       = FLBuilderModel::get_store_url( 'change-logs', array(
				'utm_medium'   => 'bb-pro',
				'utm_source'   => 'plugins-admin-page',
				'utm_campaign' => 'plugins-admin-changelog',
			) );
			$actions[] = '<a href="' . $url . '" target="_blank">' . _x( 'Change Log', 'Plugin action link label.', 'fl-builder' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * If Curl module is not installed, inform user on license page as updates are likely to not work.
	 * @since 2.2.2
	 */
	static public function check_curl() {

		$curl = ( function_exists( 'curl_version' ) ) ? true : false;

		if ( ! $curl ) {
			$text     = __( 'We’ve detected that your server does not have the PHP cURL extension installed. Ask your hosting provider to install it so you’ll be able to perform automatic updates without error.', 'fl-builder' );
			$link     = 'https://docs.wpbeaverbuilder.com/beaver-builder/troubleshooting/common-issues/error-when-trying-to-install-update';
			$link_txt = __( 'See our Knowledge Base for more info.', 'fl-builder' );
			printf( '<div class="curl-alert"><p>%s</p><p><a target="_blank" href="%s">%s</a></p></div>', $text, $link, $link_txt );
		}
	}

	static public function render_form_lite() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_slug       = 'bb-plugin/fl-builder.php';
		$installed_plugins = get_plugins();
		$installed         = array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true );
		$plugins_link      = sprintf( "<a href='%s'>%s</a>", admin_url( 'plugins.php' ), __( 'Plugins Page', 'fl-builder' ) );
		$docs_link         = sprintf( "<a target='_blank' href='%s'>%s</a>", 'https://docs.wpbeaverbuilder.com/beaver-builder/getting-started/install-beaver-builder', __( 'Documentation', 'fl-builder' ) );
		?>
		<div id="fl-upgrade-lite-form" class="fl-upgrade-page-content">

			<h3 class="fl-settings-form-header"><?php _e( 'Beaver Builder (Lite version)', 'fl-builder' ); ?></h3>
			<?php if ( $installed ) : ?>
				<p><?php _e( "We have detected that a premium version of Beaver Builder plugin is installed but not activated, so you're still using the free version of Beaver Builder.", 'fl-builder' ); ?></p>
				<?php // translators: %s: Link to plugins page ?>
				<p><?php printf( __( 'You can activate the premium version on the %s.', 'fl-builder' ), $plugins_link ); ?></p>
				<?php // translators: %s: Link to docs page ?>
				<p><?php printf( __( 'For detailed instructions to activate and license the premium version, see the %s.', 'fl-builder' ), $docs_link ); ?></p>

			<?php else : ?>
			<p><?php _e( 'You currently have the free Beaver Builder plugin activated, no license is required.', 'fl-builder' ); ?></p>
				<?php // translators: %s: Link to docs page ?>
			<p><?php printf( __( 'If you have purchased a premium version of Beaver Builder, see our %s for step-by-step upgrade instructions.', 'fl-builder' ), $docs_link ); ?></p>
		<?php endif; ?>
		</div>
			<?php
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function init_classes() {
		_deprecated_function( __METHOD__, '1.8' );
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function init_settings() {
		_deprecated_function( __METHOD__, '1.8' );
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function init_multisite() {
		_deprecated_function( __METHOD__, '1.8' );
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function init_templates() {
		_deprecated_function( __METHOD__, '1.8' );
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function white_label_plugins_page( $plugins ) {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderWhiteLabel::plugins_page()' );

		if ( class_exists( 'FLBuilderWhiteLabel' ) ) {
			return FLBuilderWhiteLabel::plugins_page( $plugins );
		}

		return $plugins;
	}

	/**
	 * @since 1.6.4.3
	 * @deprecated 1.8
	 */
	static public function white_label_themes_page( $themes ) {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderWhiteLabel::themes_page()' );

		if ( class_exists( 'FLBuilderWhiteLabel' ) ) {
			return FLBuilderWhiteLabel::themes_page( $themes );
		}

		return $themes;
	}

	/**
	 * @since 1.6.4.4
	 * @deprecated 1.8
	 */
	static public function white_label_theme_gettext( $text ) {
		if ( class_exists( 'FLBuilderWhiteLabel' ) ) {
			return FLBuilderWhiteLabel::theme_gettext( $text );
		}

		return $text;
	}
}

FLBuilderAdmin::init();
