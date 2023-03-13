<?php

/**
 * Handles version specific update logic.
 *
 * @since 1.3.1
 */
final class FLThemeUpdate {

	/**
	 * Checks to see if update logic needs to run.
	 *
	 * @since 1.3.1
	 * @return void
	 */
	static public function init() {

		add_action( 'fl_theme_updated', array( 'FLCustomizer', 'refresh_css' ) );

		// Don't update for dev versions.
		if ( '{FL_THEME_VERSION}' === FL_THEME_VERSION ) {
			return;
		}

		// Get the saved version number.
		$saved_version = self::get_saved_version();

		// Don't update if the saved version matches the current version.
		if ( version_compare( $saved_version, FL_THEME_VERSION, '=' ) ) {
			return;
		}

		// Update to 1.7.6
		if ( version_compare( $saved_version, '1.7.6', '<' ) ) {
			self::v_1_7_6();
		}

		// Update to 1.7.3
		if ( version_compare( $saved_version, '1.7.3', '<' ) ) {
			self::v_1_7_3();
		}

		// Export CSS Code if user is using WP > 4.7 and custom css exists.
		self::wp_4_7_export_css();

		// Update to 1.2.0 or greater.
		if ( version_compare( $saved_version, '1.2.0', '<' ) ) {
			self::v_1_2_0();
		}

		// Update to 1.3.1 or greater.
		if ( version_compare( $saved_version, '1.3.1', '<' ) ) {
			self::v_1_3_1();
		}

		// Update to 1.7 or greater.
		if ( version_compare( $saved_version, '1.7-alpha.1', '<' ) ) {
			self::v_1_7();
		}

		do_action( 'fl_theme_updated' );

		// Update the saved version number.
		update_option( '_fl_automator_version', FL_THEME_VERSION );
	}

	/**
	 * Returns the theme version saved in the database.
	 *
	 * @since 1.7
	 * @return string
	 */
	static public function get_saved_version() {
		return get_option( '_fl_automator_version', '0' );
	}

	/**
	 * Updates to version 1.2.0 when settings were moved from
	 * a custom options page to the Customizer.
	 *
	 * @since 1.3.1
	 * @access private
	 * @return void
	 */
	static private function v_1_2_0() {
		$key_map = array(
			'preset'                  => 'fl-preset',
			'layout'                  => 'fl-layout-width',
			'accent_color'            => 'fl-accent',
			'heading_font'            => 'fl-heading-font-family',
			'heading_weight'          => 'fl-heading-font-weight',
			'heading_color'           => 'fl-heading-text-color',
			'text_font'               => 'fl-body-font-family',
			'text_color'              => 'fl-body-text-color',
			'bg_color'                => 'fl-body-bg-color',
			'bg_image'                => 'fl-body-bg-image',
			'bg_repeat'               => 'fl-body-bg-repeat',
			'bg_position'             => 'fl-body-bg-position',
			'bg_attachment'           => 'fl-body-bg-attachment',
			'bg_size'                 => 'fl-body-bg-size',
			'content_bg_color'        => 'fl-content-bg-color',
			'top_bar_bg_type'         => 'fl-topbar-bg-type',
			'top_bar_bg_color'        => 'fl-topbar-bg-color',
			'top_bar_bg_grad'         => 'fl-topbar-bg-gradient',
			'header_bg_type'          => 'fl-header-bg-type',
			'header_bg_color'         => 'fl-header-bg-color',
			'header_bg_grad'          => 'fl-header-bg-gradient',
			'nav_bg_type'             => 'fl-nav-bg-type',
			'nav_bg_color'            => 'fl-nav-bg-color',
			'nav_bg_grad'             => 'fl-nav-bg-gradient',
			'footer_widgets_bg_type'  => 'fl-footer-widgets-bg-type',
			'footer_widgets_bg_color' => 'fl-footer-widgets-bg-color',
			'footer_bg_type'          => 'fl-footer-bg-type',
			'footer_bg_color'         => 'fl-footer-bg-color',
			'top_bar_layout'          => 'fl-topbar-layout',
			'top_bar_col1_layout'     => 'fl-topbar-col1-layout',
			'top_bar_col1_text'       => 'fl-topbar-col1-text',
			'top_bar_col2_layout'     => 'fl-topbar-col2-layout',
			'top_bar_col2_text'       => 'fl-topbar-col2-text',
			'fixed_header'            => 'fl-fixed-header',
			'logo_type'               => 'fl-logo-type',
			'logo_text'               => 'fl-logo-text',
			'logo_font'               => 'fl-logo-font-family',
			'logo_weight'             => 'fl-logo-font-weight',
			'logo_image'              => 'fl-logo-image',
			'logo_size'               => 'fl-logo-font-size',
			'nav_position'            => 'fl-header-layout',
			'nav_search'              => 'fl-header-nav-search',
			'header_content'          => 'fl-header-content-layout',
			'header_text'             => 'fl-header-content-text',
			'show_footer_widgets'     => 'fl-footer-widgets-display',
			'footer_layout'           => 'fl-footer-layout',
			'footer_col1_layout'      => 'fl-footer-col1-layout',
			'footer_col1_text'        => 'fl-footer-col1-text',
			'footer_col2_layout'      => 'fl-footer-col2-layout',
			'footer_col2_text'        => 'fl-footer-col2-text',
			'social_color'            => 'fl-social-icons-color',
			'facebook'                => 'fl-social-facebook',
			'twitter'                 => 'fl-social-twitter',
			'google'                  => 'fl-social-google',
			'linkedin'                => 'fl-social-linkedin',
			'yelp'                    => 'fl-social-yelp',
			'pinterest'               => 'fl-social-pinterest',
			'tumblr'                  => 'fl-social-tumblr',
			'vimeo'                   => 'fl-social-vimeo',
			'youtube'                 => 'fl-social-youtube',
			'flickr'                  => 'fl-social-flickr',
			'instagram'               => 'fl-social-instagram',
			'skype'                   => 'fl-social-skype',
			'dribbble'                => 'fl-social-dribbble',
			'500px'                   => 'fl-social-500px',
			'blogger'                 => 'fl-social-blogger',
			'github'                  => 'fl-social-github',
			'rss'                     => 'fl-social-rss',
			'email'                   => 'fl-social-email',
			'blog_layout'             => 'fl-blog-layout',
			'blog_sidebar_size'       => 'fl-blog-sidebar-size',
			'blog_show_author'        => 'fl-blog-post-author',
			'blog_show_date'          => 'fl-blog-post-date',
			'blog_show_full'          => 'fl-archive-show-full',
			'blog_show_thumbs'        => 'fl-archive-show-thumbs',
			'blog_show_cats'          => 'fl-posts-show-cats',
			'blog_show_tags'          => 'fl-posts-show-tags',
			'woo_layout'              => 'fl-woo-layout',
			'woo_sidebar_size'        => 'fl-woo-sidebar-size',
			'woo_cats_add_button'     => 'fl-woo-cart-button',
			'css'                     => 'fl-css-code',
			'js'                      => 'fl-js-code',
			'head'                    => 'fl-head-code',
			'favicon'                 => 'fl-favicon',
			'lightbox'                => 'fl-lightbox',
		);

		$color_keys = array(
			'accent_color',
			'heading_color',
			'text_color',
			'bg_color',
			'content_bg_color',
			'top_bar_bg_color',
			'header_bg_color',
			'nav_bg_color',
			'footer_widgets_bg_color',
			'footer_bg_color',
		);

		// Get the options to migrate.
		$settings = get_option( 'fl_theme_settings' );
		$skin_id  = get_option( 'fl_theme_skin_id' );

		// Return if we don't have any options to migrate.
		if ( ! $settings ) {
			return;
		}

		// Save a backup of the old settings.
		$cache_dir = FLCustomizer::get_cache_dir();
		file_put_contents( $cache_dir['path'] . 'backup.dat', $settings );

		// Decode the theme settings.
		$settings = json_decode( $settings );

		// Loop through the theme settings and migrate each to the customizer.
		foreach ( $settings as $key => $val ) {

			if ( isset( $key_map[ $key ] ) ) {

				if ( in_array( $key, $color_keys, true ) && ! strstr( $val, '#' ) ) {
					$val = '#' . $val;
				} else {
					$val = htmlspecialchars_decode( $val );
				}

				set_theme_mod( $key_map[ $key ], $val );
			}
		}

		// Update the css key options.
		update_option( 'fl_theme_css_key-skin', $skin_id );
		update_option( 'fl_theme_css_key-customizer', $skin_id );

		// Delete the old options.
		delete_option( 'fl_theme_settings' );
		delete_option( 'fl_theme_skin_id' );
	}

	/**
	 * Updates to version 1.3.1 when more color settings were
	 * added to the Customizer.
	 *
	 * @since 1.3.1
	 * @access private
	 * @return void
	 */
	static private function v_1_3_1() {
		$mods = FLCustomizer::get_mods();

		self::v_1_3_1_update_colors( 'topbar', $mods );
		self::v_1_3_1_update_colors( 'header', $mods );
		self::v_1_3_1_update_colors( 'nav', $mods );
		self::v_1_3_1_update_colors( 'footer-widgets', $mods );
		self::v_1_3_1_update_colors( 'footer', $mods );

		if ( ! isset( $mods['fl-nav-text-type'] ) || ( isset( $mods['fl-nav-text-type'] ) && 'default' === $mods['fl-nav-text-type'] ) ) {
			set_theme_mod( 'fl-nav-font-family', $mods['fl-body-font-family'] );
			set_theme_mod( 'fl-nav-font-weight', '400' );
			set_theme_mod( 'fl-nav-font-format', 'none' );
			set_theme_mod( 'fl-nav-font-size', $mods['fl-body-font-size'] );
		}
	}

	/**
	 * Updates color mods for a specific section in 1.3.1.
	 *
	 * @since 1.3.1
	 * @access private
	 * @param string $slug The section slug.
	 * @param array $mods The current theme mods.
	 * @return void
	 */
	static private function v_1_3_1_update_colors( $slug, $mods ) {
		if ( ! isset( $mods[ 'fl-' . $slug . '-bg-type' ] ) ) {
			$bg_type = 'content';
		} else {
			$bg_type = $mods[ 'fl-' . $slug . '-bg-type' ];
		}

		if ( 'none' === $bg_type ) {
			$bg   = '';
			$text = FLColor::foreground( $mods['fl-body-bg-color'] );
			$link = $text;
		} elseif ( 'content' === $bg_type ) {
			$bg   = $mods['fl-content-bg-color'];
			$text = FLColor::foreground( $mods['fl-content-bg-color'] );
			$link = $mods['fl-accent'];
		} else {
			$bg   = $mods[ 'fl-' . $slug . '-bg-color' ];
			$text = FLColor::foreground( $mods[ 'fl-' . $slug . '-bg-color' ] );
			$link = $text;
		}

		set_theme_mod( 'fl-' . $slug . '-bg-color', $bg );
		set_theme_mod( 'fl-' . $slug . '-text-color', $text );
		set_theme_mod( 'fl-' . $slug . '-link-color', $link );
		set_theme_mod( 'fl-' . $slug . '-hover-color', $link );
	}

	/**
	 *  Export CSS Code mod to the built-in WP CSS. Available since WP 4.7.0
	 *
	 * @since 1.6
	 * @access private
	 * @return void
	 */
	static private function wp_4_7_export_css() {

		// Export BB CSS code to WP core 'Additional CSS', it's only available since WP 4.7.0
		if ( function_exists( 'wp_get_custom_css_post' ) && false !== get_theme_mod( 'fl-css-code' ) ) {

			$mods = FLCustomizer::get_mods();

			// Export BB CSS code if exists
			if ( isset( $mods['fl-css-code'] ) && ! empty( $mods['fl-css-code'] ) ) {

				$fl_css = $mods['fl-css-code'];
				$wp_css = wp_get_custom_css_post();

				$css_code  = "\r\n\r\n/*\r\n" . esc_js( __( 'CSS Migrated from BB theme:', 'fl-automator' ) ) . "\r\n*/\r\n\r\n";
				$css_code .= $fl_css;

				// Append BB CSS code if WP CSS is not empty.
				if ( $wp_css ) {
					$css_code = $wp_css->post_content . $css_code;
				}

				wp_update_custom_css_post( $css_code );

				// Remove mod so it would only export once.
				remove_theme_mod( 'fl-css-code' );
			}
		}
	}

	/**
	 * Updates to version 1.7. This will set the default layout
	 * framework to Bootstrap 3 for existing sites to maintain
	 * backwards compatibility.
	 *
	 * @since 1.7
	 * @access private
	 * @return void
	 */
	static private function v_1_7() {
		$saved_version = self::get_saved_version();

		if ( $saved_version ) {
			set_theme_mod( 'fl-framework', 'bootstrap' );
		}
	}

	/**
	 * @since 1.7.2.1
	 */
	static private function v_1_7_3() {

		$responsive_mods = FLCustomizer::_get_responsive_mods();
		$updated         = false;
		$mods            = FLCustomizer::get_mods();
		$defaults        = FLCustomizer::_get_default_mods();

		foreach ( $responsive_mods as $var => $mod_data ) {
			foreach ( array( 'desktop', 'medium', 'mobile' ) as $device ) {
				if ( 'desktop' !== $device ) {
					$option_key = $mod_data['key'] . '_' . $device;
					$var_key    = 'desktop' !== $device ? $device . '-' . $var : '';
				} else {
					$option_key = $mod_data['key'];
					$var_key    = $var;
				}
				if ( 'desktop' !== $device && ! get_theme_mod( $option_key ) ) {
					if ( $mods[ $mod_data['key'] ] !== $defaults[ $mod_data['key'] ] ) {
						$mod_value = $mods[ $mod_data['key'] ];
					} else {
						$mod_value = $defaults[ $option_key ];
					}
					set_theme_mod( $option_key, $mod_value );
					$updated = true;
				}
			}
		}
		if ( $updated ) {
			do_action( 'fl_theme_updated' );
		}
	}

	/**
	 * 1.7.6 adds new colour controls for the text logo which previously
	 * inherited its color from the main text colour.
	 * So if main text colour is NOT default, set new logo colour to use same.
	 */
	private static function v_1_7_6() {

		self::v_1_7_3();
		$mods = FLCustomizer::get_mods();

		if ( isset( $mods['fl-body-text-color'] ) && '' !== $mods['fl-body-text-color'] && '#808080' !== $mods['fl-body-text-color'] ) {
			set_theme_mod( 'fl-logo-text-color', $mods['fl-body-text-color'] );
		}
	}
}
