<?php

/**
 * Helper class for theme functions.
 *
 * @since 1.0
 */
final class FLTheme {

	/**
	 * An array of data for each font to render.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $fonts
	 */
	static private $fonts;

	/**
	 * Font Awesome 5 CDN URL.
	 *
	 * @since 1.7
	 * @var string $fa5_url
	 */
	static public $fa5_url = 'https://use.fontawesome.com/releases/v5.15.4/css/all.css';

	/**
	 * Font Awesome 4 CDN URL.
	 *
	 * @since 1.7
	 * @var string $fa4_url
	 */
	static public $fa4_url = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';


	/**
	 * Returns a Customizer setting.
	 *
	 * @since 1.0
	 * @param array $key The key of the setting to return.
	 * @return mixed
	 */
	static public function get_setting( $key = '', $default = '' ) {
		$settings = FLCustomizer::get_mods();
		$setting  = '';
		if ( isset( $settings[ $key ] ) ) {
			$setting = $settings[ $key ];
		} else {
			$setting = $default;
		}

		return apply_filters( 'fl_theme_get_setting_' . $key, $setting, $default, $settings );

	}

	/**
	 * Returns an array of all Customizer settings.
	 *
	 * @since 1.0
	 * @return array
	 */
	static public function get_settings() {
		return FLCustomizer::get_mods();
	}

	/**
	 * Checks to see if the current site is being accessed over SSL.
	 *
	 * @since 1.2.3
	 * @return bool
	 */
	static public function is_ssl() {
		if ( is_ssl() ) {
			return true;
		} elseif ( 0 === stripos( get_option( 'siteurl' ), 'https://' ) ) {
			return true;
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks to see if the current site is in debug mode.
	 *
	 * @since 1.7
	 * @return bool
	 */
	static public function is_debug() {
		if ( method_exists( 'FLBuilder', 'is_debug' ) ) {
			return FLBuilder::is_debug();
		}
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Theme setup logic called by the after_setup_theme action.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function setup() {

		global $wp_version;
		// Localization (load as first thing before any translation texts)
		// Note: the first-loaded translation file overrides any following ones if the same translation is present.

		//wp-content/languages/theme-name/it_IT.mo
		load_theme_textdomain( 'fl-automator', trailingslashit( WP_LANG_DIR ) . 'themes/' . get_template() );

		//wp-content/themes/child-theme-name/languages/it_IT.mo
		load_theme_textdomain( 'fl-automator', get_stylesheet_directory() . '/languages' );

		//wp-content/themes/theme-name/languages/it_IT.mo
		load_theme_textdomain( 'fl-automator', get_template_directory() . '/languages' );

		// RSS feed links support
		add_theme_support( 'automatic-feed-links' );

		// Title tag support
		add_theme_support( 'title-tag' );

		// Post thumbnail support
		add_theme_support( 'post-thumbnails' );

		// WooCommerce support
		add_theme_support( 'woocommerce' );

		// Wide block support
		add_theme_support( 'align-wide' );

		// Default block styles
		add_theme_support( 'wp-block-styles' );

		if ( version_compare( $wp_version, '5.3', '>=' ) ) {
			add_theme_support( 'html5', array( 'script', 'style' ) );
		} else {
			add_theme_support( 'html5' );
		}

		// Nav menus
		register_nav_menus( self::get_nav_locations() );

		// Include customizer settings.
		require_once FL_THEME_DIR . '/includes/customizer-functions.php';
		require_once FL_THEME_DIR . '/includes/customizer-panel-general.php';
		require_once FL_THEME_DIR . '/includes/customizer-panel-header.php';
		require_once FL_THEME_DIR . '/includes/customizer-panel-content.php';
		require_once FL_THEME_DIR . '/includes/customizer-panel-footer.php';
		require_once FL_THEME_DIR . '/includes/customizer-panel-widgets.php';
		require_once FL_THEME_DIR . '/includes/customizer-panel-code.php';
		require_once FL_THEME_DIR . '/includes/customizer-panel-settings.php';
		require_once FL_THEME_DIR . '/includes/customizer-presets.php';

		// Since WooCommerce 3.0 we have to declare gallery support
		$type = self::get_setting( 'fl-woo-gallery' );

		if ( 'none' !== $type ) {
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}
	}

	/**
	 * Return array of possible menu locations.
	 * @since 1.6.4
	 */
	static public function get_nav_locations( $location = false ) {

		$locations = array(
			'bar'    => __( 'Top Bar Menu', 'fl-automator' ),
			'header' => __( 'Header Menu', 'fl-automator' ),
			'footer' => __( 'Footer Menu', 'fl-automator' ),
		);

		if ( $location && isset( $locations[ $location ] ) ) {
			return $locations[ $location ];
		}

		return $locations;
	}

	/**
	 * Enqueues theme styles and scripts.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function enqueue_scripts() {

		// Fonts
		switch ( self::get_setting( 'fl-awesome' ) ) {

			case 'fa5':
				self::enqueue_fontawesome();
				break;
			case 'fa4':
				self::enqueue_fontawesome_legacy();
				break;
		}

		// jQuery
		wp_enqueue_script( 'jquery-throttle', FL_THEME_URL . '/js/jquery.throttle.min.js', array( 'jquery' ), FL_THEME_VERSION, true );
		if ( 'fadein' !== self::get_setting( 'fl-fixed-header' ) ) {
			global $wp_version;
			if ( version_compare( $wp_version, '4.6', '<' ) ) {
				wp_register_script( 'imagesloaded', FL_THEME_URL . '/js/jquery.imagesloaded.min.js', array( 'jquery' ), FL_THEME_VERSION, true );
			}
			wp_enqueue_script( 'imagesloaded' );
		}

		// Lightbox
		if ( self::get_setting( 'fl-lightbox' ) === 'enabled' ) {
			wp_enqueue_style( 'jquery-magnificpopup', FL_THEME_URL . '/css/jquery.magnificpopup.css', array(), FL_THEME_VERSION );
			wp_enqueue_script( 'jquery-magnificpopup', FL_THEME_URL . '/js/jquery.magnificpopup.min.js', array(), FL_THEME_VERSION, true );
		}

		// FitVids
		$body_classes = get_body_class();
		if ( ! in_array( 'fl-builder', $body_classes, true ) ) {
			wp_enqueue_script( 'jquery-fitvids', FL_THEME_URL . '/js/jquery.fitvids.js', array(), FL_THEME_VERSION, true );
		}

		// Threaded Comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Preview JS
		if ( FLCustomizer::is_preset_preview() ) {
			wp_enqueue_script( 'fl-automator-preview', FL_THEME_URL . '/js/preview.js', array(), FL_THEME_VERSION, true );
			wp_localize_script( 'fl-automator-preview', 'preview', array(
				'preset' => $_GET['fl-preview'],
			) );
		}

		// Layout Framework
		FLLayout::enqueue_framework();

		// Core theme JS
		wp_enqueue_script( 'fl-automator', FL_THEME_URL . '/js/theme' . ( self::is_debug() ? '' : '.min' ) . '.js', array(), FL_THEME_VERSION, true );

		/**
		 * JS Options for breakpoints
		 * @see fl_theme_breakpoint_opts
		 */
		$themeopts = self::get_theme_breakpoints();
		if ( true === apply_filters( 'fl_theme_disable_smoothscroll_links', false ) ) {
			$themeopts['smooth'] = 'disabled';
		}
		wp_localize_script( 'fl-automator', 'themeopts', $themeopts );

		// Skin
		if ( 'file' === FLTheme::get_asset_enqueue_method() ) {
			wp_enqueue_style( 'fl-automator-skin', FLCustomizer::css_url(), array(), FL_THEME_VERSION );
		} else {
			wp_enqueue_style( 'bb-theme-style', get_stylesheet_uri() );
			wp_add_inline_style( 'bb-theme-style', self::get_cached_css( true === FLCustomizer::is_customizer_preview() ? 'customizer' : 'skin' ) );
		}

		// RTL Support
		if ( is_rtl() ) {
			wp_enqueue_style( 'fl-automator-rtl', FL_THEME_URL . '/css/rtl.css', array(), FL_THEME_VERSION );
		}
	}

	/**
	 * Return array of theme breakpoints
	 * @since 1.7.3
	 */
	static public function get_theme_breakpoints() {

		$args = array(
			'medium_breakpoint' => get_theme_mod( 'fl-medium-breakpoint' ) ? get_theme_mod( 'fl-medium-breakpoint' ) : 992,
			'mobile_breakpoint' => get_theme_mod( 'fl-mobile-breakpoint' ) ? get_theme_mod( 'fl-mobile-breakpoint' ) : 768,
		);

		return apply_filters( 'fl_theme_breakpoint_opts', $args );
	}

	/**
	 * Fetch CSS from cache
	 * @since 1.7
	 */
	static public function get_cached_css( $slug ) {
		$css = get_option( 'fl-theme-' . $slug );
		return $css ? $css : FLCustomizer::refresh_css();
	}

	/**
	 * Save CSS to cache
	 * @since 1.7
	 */
	static public function update_cached_css( $slug, $css ) {
		update_option( 'fl-theme-' . $slug, $css );
	}

	/**
	 * Determine cache method.
	 * @since 1.7
	 */
	static public function get_asset_enqueue_method() {
		if ( class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'get_asset_enqueue_method' ) ) {
			$method = FLBuilderModel::get_asset_enqueue_method();
		} else {
			$method = 'file';
		}
		return $method;
	}

	/**
	 * Enqueues Font Awesome 5.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function enqueue_fontawesome() {
		if ( class_exists( 'FLBuilder' ) && method_exists( 'FLBuilder', 'get_fa5_url' ) ) {
			wp_enqueue_style( 'font-awesome-5' );
		} else {
			$url = self::$fa5_url;
			wp_enqueue_style( 'font-awesome-5', $url, array(), FL_THEME_VERSION );
		}
	}

	/**
	 * Enqueues Font Awesome 5.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function enqueue_fontawesome_legacy() {
		if ( class_exists( 'FLBuilder' ) && method_exists( 'FLBuilder', 'get_fa5_url' ) ) {
			if ( isset( FLBuilder::$fa4_url ) && '' === FLBuilder::$fa4_url ) {
				wp_enqueue_style( 'font-awesome' );
				return;
			}
		}
		wp_enqueue_style( 'font-awesome', self::$fa4_url, array(), FL_THEME_VERSION );
	}

	/**
	 * Initializes theme sidebars.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function widgets_init() {
		$footer_widgets_display = self::get_setting( 'fl-footer-widgets-display' );
		$woo_layout             = self::get_setting( 'fl-woo-layout' );

		// Primary Sidebar
		register_sidebar(array(
			'name'          => __( 'Primary Sidebar', 'fl-automator' ),
			'id'            => 'blog-sidebar',
			'before_widget' => '<aside id="%1$s" class="fl-widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="fl-widget-title">',
			'after_title'   => '</h4>',
		));

		// Footer Widgets
		if ( 'disabled' !== $footer_widgets_display ) {
			register_sidebars( 4, array(
				/* translators: %d: order number of the auto-created sidebar */
				'name'          => _x( 'Footer Column %d', 'Sidebar title. %d stands for the order number of the auto-created sidebar, 4 in total.', 'fl-automator' ),
				'id'            => 'footer-col',
				'before_widget' => '<aside id="%1$s" class="fl-widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="fl-widget-title">',
				'after_title'   => '</h4>',
			) );
		}

		// WooCommerce Sidebar
		if ( 'no-sidebar' !== $woo_layout && self::is_plugin_active( 'woocommerce' ) ) {
			register_sidebar( array(
				'name'          => __( 'WooCommerce Sidebar', 'fl-automator' ),
				'id'            => 'woo-sidebar',
				'before_widget' => '<aside id="%1$s" class="fl-widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="fl-widget-title">',
				'after_title'   => '</h4>',
			) );
		}

		// After Post Widget
		register_sidebar( array(
			'name'          => __( 'After Post Widget', 'fl-automator' ),
			'id'            => 'after-post-widget',
			'before_widget' => '<aside id="%1$s" class="fl-widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="fl-widget-title">',
			'after_title'   => '</h4>',
		) );

	}

	/**
	 * Renders the text for the <title> tag.
	 *
	 * @since 1.0
	 * @since 1.3.1.3 Only render if the core title tag isn't supported.
	 * @return void
	 */
	static public function title() {
		if ( ! function_exists( '_wp_render_title_tag' ) ) {

			$sep         = apply_filters( 'fl_title_separator', ' | ' );
			$title       = wp_title( $sep, false, 'right' );
			$name        = get_bloginfo( 'name' );
			$description = get_bloginfo( 'description' );

			if ( empty( $title ) && empty( $description ) ) {
				$title = $name;
			} elseif ( empty( $title ) ) {
				$title = $name . ' | ' . $description;
			} elseif ( ! empty( $name ) && ! stristr( $title, $name ) ) {
				$title = ! stristr( $title, $sep ) ? $title . $sep . $name : $title . $name;
			}

			echo '<title>' . apply_filters( 'fl_title', $title ) . '</title>';
		}
	}

	/**
	 * Renders the favicon tags.
	 *
	 * @since 1.0
	 * @since 1.3.1.3 Only show the deprecated favicon if we don't have a core Site Icon saved.
	 * @return void
	 */
	static public function favicon() {
		if ( false === get_option( 'site_icon', false ) ) {

			$favicon = self::get_setting( 'fl-favicon' );
			$apple   = self::get_setting( 'fl-apple-touch-icon' );

			if ( ! empty( $favicon ) ) {
				echo '<link rel="shortcut icon" href="' . $favicon . '" />' . "\n";
			}
			if ( ! empty( $apple ) ) {
				echo '<link rel="apple-touch-icon" href="' . $apple . '" />' . "\n";
			}
		}
	}

	/**
	 * Adds and renders the <link> tags for fonts.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function fonts() {
		$settings = self::get_settings();

		$defaults = array(
			300,
			400,
			700,
			$settings['fl-body-font-weight'],
		);

		self::add_font( $settings['fl-body-font-family'], apply_filters( 'fl_body_font_family', $defaults ) );
		self::add_font( $settings['fl-heading-font-family'], $settings['fl-heading-font-weight'] );
		self::add_font( $settings['fl-nav-font-family'], $settings['fl-nav-font-weight'] );
		self::add_font( $settings['fl-button-font-family'], $settings['fl-button-font-weight'] );

		if ( '' !== $settings['fl-heading-style'] ) {
			self::add_font( $settings['fl-title-font-family'], $settings['fl-title-font-weight'] );
		}

		if ( 'text' === $settings['fl-logo-type'] ) {
			self::add_font( $settings['fl-logo-font-family'], $settings['fl-logo-font-weight'] );
		}

		self::render_fonts();
	}

	/**
	 * Adds data to the $fonts array for a font to be rendered.
	 *
	 * @since 1.0
	 * @param string $name The name key of the font to add.
	 * @param array $variants An array of weight variants.
	 * @return void
	 */
	static public function add_font( $name, $variants = array() ) {
		$google_fonts_domain = apply_filters( 'fl_theme_google_fonts_domain', 'https://fonts.googleapis.com/' );
		$google_url          = $google_fonts_domain . 'css?family=';

		if ( isset( self::$fonts[ $name ] ) ) {
			foreach ( (array) $variants as $variant ) {
				if ( ! in_array( $variant, self::$fonts[ $name ]['variants'], true ) ) {
					self::$fonts[ $name ]['variants'][] = $variant;
				}
			}
		} else {
			$google               = FLFontFamilies::get_google();
			self::$fonts[ $name ] = array(
				'url'      => isset( $google[ $name ] ) ? $google_url . $name : '',
				'variants' => (array) $variants,
			);
		}
	}

	/**
	 * Renders the <link> tag for all fonts in the $fonts array.
	 *
	 * @since 1.0
	 * @since 1.5.3 Changed the rendering of fonts from echoing <link> to wp_enqueue_style
	 * @return void
	 */
	static public function render_fonts() {
		foreach ( self::$fonts as $name => $font ) {
			if ( ! empty( $font['url'] ) ) {
				$subset = apply_filters( 'fl_font_subset', '', $name );

				$google_font_url = $font['url'] . ':' . implode( ',', $font['variants'] ) . $subset;

				wp_enqueue_style( 'fl-builder-google-fonts-' . md5( $google_font_url ), $google_font_url, array() );
			}
		}
	}

	/**
	 * Renders the <link> tags for theme CSS and any custom head code.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function head() {
		$settings = self::get_settings();

		// CSS
		if ( isset( $settings['fl-css-code'] ) && ( ! empty( $settings['fl-css-code'] ) || FLCustomizer::is_customizer_preview() ) ) {
			echo '<style id="fl-theme-custom-css">' . $settings['fl-css-code'] . '</style>' . "\n";
		}

		// JS
		if ( ! empty( $settings['fl-js-code'] ) ) {
			echo '<script id="fl-theme-custom-js">' . $settings['fl-js-code'] . '</script>' . "\n";
		}

		// Head
		if ( ! empty( $settings['fl-head-code'] ) ) {
			echo $settings['fl-head-code'] . "\n";
		}

		do_action( 'fl_head' );
	}

	/**
	 * Adds custom CSS classes to the <body> tag.
	 * Called by the body_class filter.
	 *
	 * @since 1.0
	 * @param array $classes An array of the existing classes.
	 * @return array
	 */
	static public function body_class( $classes ) {
		$preset         = self::get_setting( 'fl-preset' );
		$header_enabled = apply_filters( 'fl_header_enabled', true );

		// Preset
		if ( empty( $preset ) ) {
			$classes[] = 'fl-preset-default';
		} else {
			$classes[] = 'fl-preset-' . $preset;
		}

		// Width
		if ( self::get_setting( 'fl-layout-width' ) === 'full-width' ) {
			$classes[] = 'fl-full-width';
		} else {
			$classes[] = 'fl-fixed-width';
		}

		if ( is_singular( 'post' ) ) {
			$sidebar       = get_theme_mod( 'fl-blog-layout' );
			$page_template = basename( get_page_template() );

			if ( 'tpl-full-width.php' !== $page_template && 'no-sidebar' !== $sidebar ) {
				$classes[] = 'fl-has-sidebar';
			}
		}

		// Header classes
		if ( $header_enabled ) {

			// Nav Vertical Left
			if ( self::get_setting( 'fl-header-layout' ) === 'vertical-left' ) {
				$classes[] = 'fl-nav-vertical fl-nav-vertical-left';
			}

			// Nav Vertical Right
			if ( self::get_setting( 'fl-header-layout' ) === 'vertical-right' ) {
				$classes[] = 'fl-nav-vertical fl-nav-vertical-right';
			}

			// Nav Left
			if ( self::get_setting( 'fl-header-layout' ) === 'left' ) {
				$classes[] = 'fl-nav-left';
			}

			// Responsive Nav Layout (Offcanvas)
			if ( self::get_setting( 'fl-nav-mobile-layout' ) !== 'dropdown' ) {
				$nav_layout          = self::get_setting( 'fl-nav-mobile-layout' );
				$nav_layout_position = self::get_setting( 'fl-nav-mobile-layout-position' );

				$classes[] = 'fl-nav-mobile-offcanvas fl-offcanvas-' . $nav_layout . '-' . $nav_layout_position;
			}

			// Shrink Fixed Header
			if ( ( self::get_setting( 'fl-fixed-header' ) === 'shrink' ) && ( self::get_setting( 'fl-header-layout' ) !== 'vertical-left' ) && ( self::get_setting( 'fl-header-layout' ) !== 'vertical-right' ) ) {
				$classes[] = 'fl-shrink';
			}

			// Fixed Header
			if ( ( self::get_setting( 'fl-fixed-header' ) === 'fixed' ) && ( self::get_setting( 'fl-header-layout' ) !== 'vertical-left' ) && ( self::get_setting( 'fl-header-layout' ) !== 'vertical-right' ) ) {
				$classes[] = 'fl-fixed-header';
			}

			// Custom Padding Top
			if ( 'custom' === self::get_setting( 'fl-fixed-header-padding-top' ) && 1 === count( array_diff( array( 'fl-shrink', 'fl-fixed-header' ), $classes ) ) ) {
				$classes[] = 'fl-header-padding-top-custom';
			}

			// Hide Header Until Scroll
			if ( ( self::get_setting( 'fl-hide-until-scroll-header' ) === 'enable' ) && ( self::get_setting( 'fl-fixed-header' ) === 'hidden' ) && ( self::get_setting( 'fl-header-layout' ) !== 'vertical-left' ) && ( self::get_setting( 'fl-header-layout' ) !== 'vertical-right' ) ) {
				$classes[] = 'fl-fixed-header';
				$classes[] = 'fl-scroll-header';
			}
		}

		// Footer Parallax Effect
		if ( ( self::get_setting( 'fl-footer-parallax-effect' ) === 'enable' ) && ( self::get_setting( 'fl-layout-width' ) === 'full-width' ) ) {
			$classes[] = 'fl-footer-effect';
		}

		// Scroll To Top Button
		if ( self::get_setting( 'fl-scroll-to-top' ) === 'enable' ) {
			$classes[] = 'fl-scroll-to-top';
		}

		// Search Active
		if ( self::get_setting( 'fl-header-nav-search' ) === 'visible' ) {
			$classes[] = 'fl-search-active';
		}

		// WooCommerce Columns
		if ( class_exists( 'woocommerce' ) && is_woocommerce() ) {
			$fl_woo_columns = self::get_setting( 'fl-woo-columns' );
			$classes[]      = 'woo-' . $fl_woo_columns;
		}

		// WooCommerce Products Per Page
		if ( class_exists( 'woocommerce' ) && is_woocommerce() ) {
			$fl_woo_products_per_page = self::get_setting( 'fl-woo-products-per-page' );
			$classes[]                = 'woo-products-per-page-' . $fl_woo_products_per_page;
		}

		// Submenu Indicator
		if ( self::get_setting( 'fl-nav-submenu-indicator' ) === 'enable' ) {
			FLTheme::enqueue_fontawesome();
			$classes[] = 'fl-submenu-indicator';
		}

		// Submenu Toggle
		if ( self::get_setting( 'fl-nav-submenu-toggle' ) === 'enable' ) {
			FLTheme::enqueue_fontawesome();
			$classes[] = 'fl-submenu-toggle';
		}

		// Responsive collapse menu items
		if ( self::get_setting( 'fl-nav-collapse-menu' ) === '1' ) {
			$classes[] = 'fl-nav-collapse-menu';
		}

		// Gutenberg support.
		// If this is a vanilla Gutenberg page and it has blocks add has-blocks class.
		if ( is_singular() && function_exists( 'has_block' ) && has_blocks() && ! has_block( 'fl-builder/layout' ) ) {
			$classes[] = 'has-blocks';
		}

		return $classes;
	}

	/**
	 * Renders the markup for the top bar's first column.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function top_bar_col1() {
		$settings   = self::get_settings();
		$layout     = $settings['fl-topbar-layout'];
		$col_layout = $settings['fl-topbar-col1-layout'];
		$col_text   = $settings['fl-topbar-col1-text'];

		include locate_template( 'includes/top-bar-col1.php' );
	}

	/**
	 * Renders the markup for the top bar's second column.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function top_bar_col2() {
		$settings   = self::get_settings();
		$layout     = $settings['fl-topbar-layout'];
		$col_layout = $settings['fl-topbar-col2-layout'];
		$col_text   = $settings['fl-topbar-col2-text'];

		include locate_template( 'includes/top-bar-col2.php' );
	}

	/**
	 * Renders the markup for the top bar.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function top_bar() {
		$top_bar_layout  = self::get_setting( 'fl-topbar-layout' );
		$top_bar_enabled = apply_filters( 'fl_topbar_enabled', true );

		if ( 'none' !== $top_bar_layout && $top_bar_enabled ) {
			get_template_part( 'includes/top-bar' );
		}
	}

	/**
	 * Renders the custom code that is displayed before the page header.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function header_code() {
		echo self::get_setting( 'fl-header-code' );
	}

	/**
	 * Renders the markup for the fixed header.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function fixed_header() {
		$header_layout  = self::get_setting( 'fl-fixed-header' );
		$nav_layout     = self::get_setting( 'fl-header-layout' );
		$header_enabled = apply_filters( 'fl_fixed_header_enabled', true );
		if ( 'fadein' === $header_layout && $header_enabled && 'vertical-left' !== $nav_layout && 'vertical-right' !== $nav_layout ) {
			get_template_part( 'includes/fixed-header' );
		}
	}

	/**
	 * Renders the fixed header logo if one exists.
	 * Otherwise, falls back to the standard logo.
	 *
	 * @since 1.6.5
	 * @return void
	 */
	static public function fixed_header_logo() {
		$logo_type     = self::get_setting( 'fl-logo-type' );
		$sticky_logo   = self::get_setting( 'fl-sticky-header-logo' );
		$sticky_retina = self::get_setting( 'fl-sticky-header-logo-retina' );
		$header_fixed  = self::get_setting( 'fl-fixed-header' );

		if ( $sticky_logo && 'fadein' === $header_fixed && 'image' === $logo_type ) {
			$logo_text  = apply_filters( 'fl_logo_text', get_bloginfo( 'name' ) );
			$logo_title = apply_filters( 'fl_logo_title', '' );

			echo '<img loading="false" data-no-lazy="1" class="fl-logo-img"' . FLTheme::print_schema( ' itemscope itemtype="https://schema.org/ImageObject"', false ) . ' src="' . $sticky_logo . '"';
			echo ' data-retina="' . $sticky_retina . '"';
			echo ' title="' . esc_attr( $logo_title ) . '"';
			echo ' alt="' . esc_attr( $logo_text ) . '" />';
			echo '<meta itemprop="name" content="' . esc_attr( $logo_text ) . '" />';
		} else {
			self::logo();
		}
	}

	/**
	 * Renders the markup for the main header.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function header_layout() {
		$header_layout  = self::get_setting( 'fl-header-layout' );
		$header_enabled = apply_filters( 'fl_header_enabled', true );

		if ( 'none' !== $header_layout && $header_enabled ) {
			get_template_part( 'includes/nav-' . $header_layout );
		}
	}

	/**
	 * Renders additional classes for the main header based on the
	 * selected header layout and mobile nav toggle.
	 *
	 * @since 1.3.1
	 * @return void
	 */
	static public function header_classes() {
		$header_layout   = self::get_setting( 'fl-header-layout' );
		$nav_toggle_type = self::get_setting( 'fl-mobile-nav-toggle' );
		$nav_breakpoint  = self::get_setting( 'fl-nav-breakpoint' );

		echo ' fl-page-nav-' . $header_layout;
		echo ' fl-page-nav-toggle-' . $nav_toggle_type;
		echo ' fl-page-nav-toggle-visible-' . $nav_breakpoint;
	}

	/**
	 * Renders additional data attributes for the main header
	 *
	 * @since 1.5
	 * @return void
	 */
	static public function header_data_attrs() {

		// Scroll Distance
		if ( self::get_setting( 'fl-hide-until-scroll-header' ) === 'enable' ) {
			$scroll_distance = self::get_setting( 'fl-scroll-distance' );
			echo ' data-fl-distance=' . $scroll_distance;
		}

	}

	/**
	 * Renders the markup for the header content section.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function header_content() {
		$settings = self::get_settings();
		$layout   = $settings['fl-header-content-layout'];
		$text     = $settings['fl-header-content-text'];

		do_action( 'fl_header_content_open' );

		if ( 'text' === $layout || 'social-text' === $layout ) {
			echo '<div class="fl-page-header-text">' . do_shortcode( $text ) . '</div>';
		}
		if ( 'social' === $layout || 'social-text' === $layout ) {
			self::social_icons();
		}

		do_action( 'fl_header_content_close' );
	}

	/**
	 * Renders the header logo text or image.
	 *
	 * @since 1.0
	 * @deprecated 1.6.3 Use 'fl_logo_text' instead
	 * @return void
	 */
	static public function logo() {
		$logo_type   = self::get_setting( 'fl-logo-type' );
		$logo_image  = self::get_setting( 'fl-logo-image' );
		$logo_retina = self::get_setting( 'fl-logo-image-retina' );
		$mobile_logo = self::get_setting( 'fl-mobile-header-logo' );

		if ( function_exists( 'apply_filters_deprecated' ) ) {
			$logo_text = apply_filters_deprecated( 'fl-logo-text', array( self::get_setting( 'fl-logo-text' ) ), '1.6.3', 'fl_logo_text' );
		} else {
			$logo_text    = apply_filters( 'fl-logo-text', self::get_setting( 'fl-logo-text' ) ); // @codingStandardsIgnoreLine
		}

		if ( 'image' === $logo_type ) {

			$id         = attachment_url_to_postid( $logo_image );
			$image_data = wp_get_attachment_metadata( $id );
			$logo_text  = apply_filters( 'fl_logo_text', get_bloginfo( 'name' ) );
			$logo_title = apply_filters( 'fl_logo_title', '' );

			$logo_image_class = apply_filters( 'fl_logo_image_class', 'fl-logo-img' );
			$logo_attr        = apply_filters( 'fl_logo_image_attr', array(
				'loading'      => 'false',
				'data-no-lazy' => '1',
			) );

			$safe_logo_attr = '';
			foreach ( $logo_attr as $key => $value ) {
				$safe_logo_attr .= sanitize_key( $key ) . '="' . esc_attr( strval( $value ) ) . '" ';
			}

			echo '<img class="' . esc_attr( $logo_image_class ) . '" ' . $safe_logo_attr . ' ' . FLTheme::print_schema( ' itemscope itemtype="https://schema.org/ImageObject"', false ) . ' src="' . $logo_image . '"';
			echo ' data-retina="' . $logo_retina . '"';

			if ( $mobile_logo ) {
				echo ' data-mobile="' . $mobile_logo . '"';
			}
			echo ' title="' . esc_attr( $logo_title ) . '"';
			echo ( isset( $image_data['width'] ) && ! empty( $image_data['width'] ) ) ? sprintf( ' width="%s"', $image_data['width'] ) : '';
			echo ( isset( $image_data['height'] ) && ! empty( $image_data['width'] ) ) ? sprintf( ' height="%s"', $image_data['height'] ) : '';
			echo ' alt="' . esc_attr( $logo_text ) . '" />';
			echo '<meta itemprop="name" content="' . esc_attr( $logo_text ) . '" />';
		} else {
			echo '<div class="fl-logo-text" itemprop="name">' . do_shortcode( $logo_text ) . '</div>';
		}
	}

	/**
	 * Get the WP tagline.
	 * @return string
	 * @since 1.7
	 */
	static public function get_tagline() {
		if ( 'text' === self::get_setting( 'fl-logo-type' ) && self::get_setting( 'fl-theme-tagline' ) ) {
			return '<div class="fl-theme-tagline">' . get_bloginfo( 'description' ) . '</div>';
		}
	}

	/**
	 * Callback method for the nav menu fallback when no menu
	 * has been selected.
	 *
	 * @since 1.0
	 * @param array $args An array of args for the menu.
	 * @return void
	 */
	static public function nav_menu_fallback( $args ) {
		$url  = current_user_can( 'edit_theme_options' ) ? admin_url( 'nav-menus.php' ) : esc_url( home_url( '/' ) );
		$url  = apply_filters( 'fl_nav_menu_fallback_url', $url );
		$text = current_user_can( 'edit_theme_options' ) ? __( 'Choose Menu', 'fl-automator' ) : __( 'Home', 'fl-automator' );

		echo '<ul class="fl-page-' . $args['theme_location'] . '-nav nav navbar-nav menu">';
		echo '<li>';
		echo '<a class="no-menu" href="' . $url . '">' . $text . '</a>';
		echo '</li>';
		echo '</ul>';
	}

	/**
	 * Renders the nav search icon and form.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function nav_search() {
		$nav_search = self::get_setting( 'fl-header-nav-search' );

		if ( 'visible' === $nav_search ) {
			FLTheme::enqueue_fontawesome();
			get_template_part( 'includes/nav-search' );
		}
	}

	/**
	 * Renders the text for the mobile nav toggle button.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function nav_toggle_text() {
		$type = self::get_setting( 'fl-mobile-nav-toggle' );
		$menu = self::get_setting( 'fl-mobile-nav-text' );

		if ( 'icon' === $type ) {
			FLTheme::enqueue_fontawesome();
			$mobile_nav_icon = apply_filters( 'fl_theme_mobile_nav_icon', '<i class="fas fa-bars" aria-hidden="true"></i>' );
			$text            = sprintf( '%s<span class="sr-only">%s</span>', $mobile_nav_icon, __( 'Menu', 'fl-automator' ) );
		} else {
			$text = '' !== $menu ? $menu : _x( 'Menu', 'Mobile navigation toggle button text.', 'fl-automator' );
		}

		echo apply_filters( 'fl_nav_toggle_text', $text );
	}

	/**
	 * Renders a group of social icons whose URL has been set in the Customizer.
	 *
	 * @since 1.0
	 * @param bool $circle Whether to use circle icons or not.
	 * @return void
	 */
	static public function social_icons( $circle = true ) {
		$settings = self::get_settings();

		$icons = apply_filters( 'fl_social_icons', array(
			'facebook',
			'twitter',
			'google',
			'google-maps',
			'snapchat',
			'linkedin',
			'yelp',
			'xing',
			'pinterest',
			'tumblr',
			'vimeo',
			'youtube',
			'flickr',
			'instagram',
			'skype',
			'dribbble',
			'500px',
			'blogger',
			'github',
			'rss',
			'email',
			'wordpress',
			'tiktok',
			'spotify',
		) );

		FLTheme::enqueue_fontawesome();
		include locate_template( 'includes/social-icons.php' );
	}

	/**
	 * Checks to see if the footer widgets and footer sections
	 * are enabled.
	 *
	 * @since 1.0
	 * @return bool
	 */
	static public function has_footer() {
		$footer_layout  = self::get_setting( 'fl-footer-layout' );
		$footer_enabled = apply_filters( 'fl_footer_enabled', true );

		return $footer_enabled && ( self::has_footer_widgets() || 'none' !== $footer_layout );
	}

	/**
	 * Renders the footer widgets section.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function footer_widgets() {
		if ( self::has_footer_widgets() ) {
			get_template_part( 'includes/footer-widgets' );
		}
	}

	/**
	 * Checks to see if any footer widgets exist.
	 *
	 * @since 1.0
	 * @return bool
	 */
	static public function has_footer_widgets() {
		$show = self::get_setting( 'fl-footer-widgets-display' );

		if ( 'disabled' === $show || ( ! is_front_page() && 'home' === $show ) ) {
			return false;
		}

		for ( $i = 1; $i <= 4; $i++ ) {

			$id = 1 === $i ? 'footer-col' : 'footer-col-' . $i;

			if ( is_active_sidebar( $id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Renders the columns and widgets for the footer widgets section.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function display_footer_widgets() {
		$active     = array();
		$num_active = 0;

		for ( $i = 1; $i <= 4; $i++ ) {

			$id = 1 === $i ? 'footer-col' : 'footer-col-' . $i;

			if ( is_active_sidebar( $id ) ) {
				$active[] = $id;
				$num_active++;
			}
		}
		if ( $num_active > 0 ) {

			$col_length = 12 / $num_active;

			for ( $i = 0; $i < $num_active; $i++ ) {
				$count    = $i + 1;
				$sm_class = FLLayout::get_col_class( 'sm', $col_length );
				$md_class = FLLayout::get_col_class( 'md', $col_length );
				echo '<div class="' . $sm_class . ' ' . $md_class . ' fl-page-footer-widget-col fl-page-footer-widget-col-' . $count . '">';
				dynamic_sidebar( $active[ $i ] );
				echo '</div>';
			}
		}
	}

	/**
	 * Renders the footer.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function footer() {
		$footer_layout = self::get_setting( 'fl-footer-layout' );

		if ( 'none' !== $footer_layout ) {
			get_template_part( 'includes/footer' );
		}
	}

	/**
	 * Renders the footer's first column.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function footer_col1() {
		$settings   = self::get_settings();
		$layout     = $settings['fl-footer-layout'];
		$col_layout = $settings['fl-footer-col1-layout'];
		$col_text   = $settings['fl-footer-col1-text'];

		include locate_template( 'includes/footer-col1.php' );
	}

	/**
	 * Renders the footer's second column.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function footer_col2() {
		$settings   = self::get_settings();
		$layout     = $settings['fl-footer-layout'];
		$col_layout = $settings['fl-footer-col2-layout'];
		$col_text   = $settings['fl-footer-col2-text'];

		include locate_template( 'includes/footer-col2.php' );
	}

	/**
	 * Renders custom code that is displayed after the footer.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function footer_code() {
		echo self::get_setting( 'fl-footer-code' );
	}

	/**
	 * Renders a theme sidebar.
	 *
	 * @since 1.0
	 * @param string $position The sidebar position, either left or right.
	 * @param string $section The section this sidebar belongs to.
	 * @return void
	 */
	static public function sidebar( $position, $section = 'blog' ) {
		$size    = self::get_setting( 'fl-' . $section . '-sidebar-size' );
		$display = self::get_setting( 'fl-' . $section . '-sidebar-display' );
		$layout  = self::get_setting( 'fl-' . $section . '-layout' );

		if ( strstr( $layout, $position ) && self::is_sidebar_enabled( $section ) ) {
			include locate_template( 'sidebar.php' );
		}
	}

	/**
	 * Check to see if the sidebar is enabled for blog, single post, shop & single product.
	 *
	 * @since 1.5.3
	 * @param string $section The section this sidebar belongs to.
	 * @return boolean
	 */
	static public function is_sidebar_enabled( $section = 'blog' ) {
		$locations     = FLCustomizer::sanitize_checkbox_multiple( self::get_setting( 'fl-' . $section . '-sidebar-location' ) );
		$post_types    = FLCustomizer::sanitize_checkbox_multiple( self::get_setting( 'fl-' . $section . '-sidebar-location-post-types' ) );
		$is_woo        = ( 'woo' === $section && class_exists( 'WooCommerce' ) ) ? true : false;
		$show_sidebar  = false;
		$get_post_type = get_query_var( 'post_type' );
		if ( in_array( 'single', $locations, true ) && is_single() ) {
			$show_sidebar = true;

			if ( in_array( 'all', $post_types, true ) ) {
				$show_sidebar = true;
			} elseif ( ! is_singular( $post_types ) ) {
				$show_sidebar = false;
			}
		}
		if ( in_array( 'blog', $locations, true ) && is_home() ) {
			$show_sidebar = true;
		}

		if ( in_array( 'search', $locations, true ) && is_search() ) {
			$show_sidebar = true;
		}

		if ( in_array( 'archive', $locations, true ) && is_archive() ) {
			$show_sidebar = true;

			if ( in_array( 'all', $post_types, true ) ) {
				$show_sidebar = true;
			} elseif ( ! empty( $get_post_type ) && ! is_post_type_archive( $post_types ) ) {
				$show_sidebar = false;
			}
		}

		// do we show sidebars on pages?
		// @since 1.6.3
		if ( is_page() ) {
			$page_template = basename( get_page_template() );
			switch ( $page_template ) {
				case 'tpl-full-width.php':
					$show_sidebar = false;
					break;
				default:
					$show_sidebar = true;
			}
		}

		if ( $is_woo && is_shop() && in_array( 'shop', $locations, true ) ) {
			$show_sidebar = true;
		}

		if ( $is_woo && is_product() && in_array( 'single', $locations, true ) ) {
			$show_sidebar = true;
		}

		if ( $is_woo && is_product_category() && in_array( 'archive', $locations, true ) ) {
			$show_sidebar = true;
		}

		return $show_sidebar;
	}

	/**
	 * This method is only here for backwards compatibility with
	 * child theme files that used this method before the FLLayout
	 * class existed.
	 *
	 * @since 1.0
	 * @param string $section The section this content belongs to.
	 * @return void
	 */
	static public function content_class( $section = 'blog' ) {
		echo FLLayout::content_class( $section );
	}

	/**
	 * Renders the content header for post archives.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function archive_page_header() {
		// Category
		if ( is_category() ) {
			$page_title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			/* translators: %s: Archive title tag */
			$page_title = sprintf( _x( 'Posts Tagged &#8216;%s&#8217;', 'Archive title: tag.', 'fl-automator' ), single_tag_title( '', false ) );
		} elseif ( is_day() ) { // Day
			/* translators: %s: Archive title day */
			$page_title = sprintf( _x( 'Archive for %s', 'Archive title: day.', 'fl-automator' ), get_the_date() );
		} elseif ( is_month() ) { // Month
			/* translators: %s: Archive title month */
			$page_title = sprintf( _x( 'Archive for %s', 'Archive title: month.', 'fl-automator' ), single_month_title( ' ', false ) );
		} elseif ( is_year() ) { // Year
			/* translators: %s: Archive title year */
			$page_title = sprintf( _x( 'Archive for %s', 'Archive title: year.', 'fl-automator' ), get_the_time( 'Y' ) );
		} elseif ( is_author() ) { // Author
			/* translators: %s: Archive title author */
			$page_title = sprintf( _x( 'Posts by %s', 'Archive title: author.', 'fl-automator' ), get_the_author() );
		} elseif ( is_search() ) { // Search
			/* translators: %s: Search results title */
			$page_title = sprintf( _x( 'Search results for: %s', 'Search results title.', 'fl-automator' ), get_search_query() );
		} elseif ( isset( $_GET['paged'] ) && ! empty( $_GET['paged'] ) ) { // Paged
			$page_title = _x( 'Archives', 'Archive title: paged archive.', 'fl-automator' );
		} else { // Index
			$page_title = '';
		}

		if ( ! empty( $page_title ) ) {
			include locate_template( 'includes/archive-header.php' );
		}
	}

	/**
	 * Renders the nav for post archives.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function archive_nav() {
		global $wp_query;

		if ( function_exists( 'wp_pagenavi' ) ) {
			wp_pagenavi();
		} elseif ( $wp_query->max_num_pages > 1 ) {
			echo '<nav class="fl-archive-nav clearfix" role="navigation">';
			echo '<div class="fl-archive-nav-prev">' . get_previous_posts_link( __( '&laquo; Newer Posts', 'fl-automator' ) ) . '</div>';
			echo '<div class="fl-archive-nav-next">' . get_next_posts_link( __( 'Older Posts &raquo;', 'fl-automator' ) ) . '</div>';
			echo '</nav>';
		}
	}

	/**
	 * Renders the excerpt more text.
	 *
	 * @since 1.0
	 * @param string $more The existing more text.
	 * @return string
	 */
	static public function excerpt_more( $more ) {
		return '&hellip;';
	}

	/**
	 * Checks to see if the markup for the post content header
	 * should be rendered or not.
	 *
	 * @since 1.0
	 * @return bool
	 */
	static public function show_post_header() {
		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_enabled() ) {

			$global_settings = FLBuilderModel::get_global_settings();

			if ( ! $global_settings->show_default_heading ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Renders the markup for the post top meta.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function post_top_meta() {
		global $post;
		$settings = self::get_settings();

		if ( is_search() ) {
			$show_author   = 'visible' === $settings['fl-search-results-author'];
			$show_date     = 'visible' === $settings['fl-search-results-date'];
			$comment_count = 'visible' === $settings['fl-search-results-comment-count'];
		} else {
			$show_author   = 'visible' === $settings['fl-blog-post-author'];
			$show_date     = 'visible' === $settings['fl-blog-post-date'];
			$comment_count = 'visible' === $settings['fl-blog-comment-count'];
		}

		$comments = comments_open() || '0' !== get_comments_number();
		include locate_template( 'includes/post-top-meta.php' );
	}

	/**
	 * Renders the markup for the post bottom meta.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function post_bottom_meta() {
		$settings  = self::get_settings();
		$show_full = $settings['fl-archive-show-full'];
		$show_cats = 'visible' === $settings['fl-posts-show-cats'] ? true : false;
		$show_tags = 'visible' === $settings['fl-posts-show-tags'] && get_the_tags() ? true : false;
		$comments  = comments_open() || '0' !== get_comments_number();

		include locate_template( 'includes/post-bottom-meta.php' );
	}

	/**
	 * Renders the markup for schema structured data.
	 *
	 * @since 1.4.1
	 * @return void
	 */
	static public function post_schema_meta() {

		if ( ! self::is_schema_enabled() ) {
			return false;
		}
		// General Schema Meta
		echo '<meta itemscope itemprop="mainEntityOfPage" itemtype="https://schema.org/WebPage" itemid="' . esc_url( get_permalink() ) . '" content="' . the_title_attribute( array(
			'echo' => false,
		) ) . '" />';
		echo '<meta itemprop="datePublished" content="' . get_the_time( 'Y-m-d' ) . '" />';
		echo '<meta itemprop="dateModified" content="' . get_the_modified_date( 'Y-m-d' ) . '" />';

		// Publisher Schema Meta
		echo '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
		echo '<meta itemprop="name" content="' . get_bloginfo( 'name' ) . '">';

		if ( 'image' === self::get_setting( 'fl-logo-type' ) ) {
			echo '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
			echo '<meta itemprop="url" content="' . self::get_setting( 'fl-logo-image' ) . '">';
			echo '</div>';
		}

		echo '</div>';

		// Author Schema Meta
		echo '<div itemscope itemprop="author" itemtype="https://schema.org/Person">';
		echo '<meta itemprop="url" content="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" />';
		echo '<meta itemprop="name" content="' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '" />';
		echo '</div>';

		// Image Schema Meta
		if ( has_post_thumbnail() ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

			if ( is_array( $image ) ) {
				echo '<div itemscope itemprop="image" itemtype="https://schema.org/ImageObject">';
				echo '<meta itemprop="url" content="' . $image[0] . '" />';
				echo '<meta itemprop="width" content="' . $image[1] . '" />';
				echo '<meta itemprop="height" content="' . $image[2] . '" />';
				echo '</div>';
			}
		}

		// Comment Schema Meta
		echo '<div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter">';
		echo '<meta itemprop="interactionType" content="https://schema.org/CommentAction" />';
		echo '<meta itemprop="userInteractionCount" content="' . wp_count_comments( get_the_ID() )->approved . '" />';
		echo '</div>';
	}

	/**
	 * Renders the post nav for single post pages.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function post_navigation() {
		$show_nav = self::get_setting( 'fl-posts-show-nav' );

		if ( 'visible' === $show_nav ) {
			$in_same_term = apply_filters( 'fl_post_navigation_same_term', false );
			echo '<div class="fl-post-nav clearfix">';
			previous_post_link( '<span class="fl-post-nav-prev">%link</span>', '&larr; %title', $in_same_term );
			next_post_link( '<span class="fl-post-nav-next">%link</span>', '%title &rarr;', $in_same_term );
			echo '</div>';
		}
	}

	/**
	 * Renders the markup for each comment in a comments list.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function display_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;

		include locate_template( 'includes/comment.php' );
	}

	/**
	 * Initializes WooCommerce layout support.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init_woocommerce() {
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

		add_action( 'woocommerce_before_main_content', 'FLTheme::woocommerce_wrapper_start', 10 );
		add_action( 'woocommerce_after_main_content', 'FLTheme::woocommerce_wrapper_end', 10 );
	}

	/**
	 * Renders the opening markup for WooCommerce pages.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function woocommerce_wrapper_start() {
		$layout                = self::get_setting( 'fl-woo-layout' );
		$fl_woo_content_layout = 'fl-woo-content';

		if ( 'sidebar-left' === $layout ) {
			$fl_woo_content_layout = 'fl-woo-content-right';
		} elseif ( 'sidebar-right' === $layout ) {
			$fl_woo_content_layout = 'fl-woo-content-left';
		}

		echo '<div class="container">';
		echo '<div class="row">';
		echo '<div class="fl-content ' . $fl_woo_content_layout . ' ';
		FLLayout::content_class( 'woo' );
		echo '">';
	}

	/**
	 * Renders the closing markup for WooCommerce pages.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function woocommerce_wrapper_end() {
		$layout = self::get_setting( 'fl-woo-layout' );
		$size   = ( 'no-sidebar' === $layout ) ? '12' : '8';

		echo '</div>';
		if ( 'sidebar-left' === $layout ) {
			self::sidebar( 'left', 'woo' );
		} elseif ( 'sidebar-right' === $layout ) {
			self::sidebar( 'right', 'woo' );
		}
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Checks to see if a plugin is currently active.
	 *
	 * @since 1.0
	 * @param string $slug The slug of the plugin to check.
	 * @return bool
	 */
	static public function is_plugin_active( $slug ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return is_plugin_active( $slug . '/' . $slug . '.php' );
	}

	/**
	 * Renders scroll to top button for wp_footer.
	 *
	 * @since 1.5
	 * @return void
	 */
	static public function go_to_top() {
		if ( self::get_setting( 'fl-scroll-to-top' ) === 'enable' ) {
			FLTheme::enqueue_fontawesome();
			printf( '<a href="#" id="fl-to-top"><span class="sr-only">%s</span><i class="fas fa-chevron-up" aria-hidden="true"></i></a>', esc_attr__( 'Scroll To Top', 'fl-automator' ) );
		}
	}

	/**
	 * Add custom widget after post content on single post.
	 *
	 * @since 1.6
	 * @return void
	 */
	static public function after_post_widget() {
		if ( is_active_sidebar( 'after-post-widget' ) && is_single() ) {
			echo '<div class="fl-after-post-widget">';
			dynamic_sidebar( 'after-post-widget' );
			echo '</div>';
		}
	}

	/**
	 * Add author box after post content on single post.
	 *
	 * @since 1.6
	 * @return void
	 */
	static public function post_author_box() {
		$allowable_types = apply_filters( 'post_author_box_types', array( 'post' ) );

		if ( self::get_setting( 'fl-post-author-box' ) === 'visible' && is_single() && in_array( get_post_type(), $allowable_types, true ) ) {
			get_template_part( 'content', 'author' );
		}
	}

	/**
	 * Renders number of columns for WooCommerce.
	 *
	 * @since 1.5.4
	 * @return void
	 */
	static public function woocommerce_columns() {
		$columns = self::get_setting( 'fl-woo-columns' );
		return $columns; // Products per row
	}

	/**
	 * Renders number of products per page for WooCommerce.
	 *
	 * @since 1.7.7
	 * @return int
	 */
	static public function woocommerce_shop_products_per_page() {

		$products_per_page = self::get_setting( 'fl-woo-products-per-page' );

		return empty( $products_per_page ) ? 16 : absint( $products_per_page );
	}

	/**
	 * Filter comment form fields.
	 * @since 1.6.6
	 */
	static public function comment_form_default_fields( $fields ) {

		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );

		$fields['author'] = '<label for="fl-author">' . _x( 'Name', 'Comment form label: comment author name.', 'fl-automator' ) . ( $req ? __( ' (required)', 'fl-automator' ) : '' ) . '</label>
									<input type="text" id="fl-author" name="author" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" tabindex="1"' . ( $req ? ' aria-required="true"' : '' ) . ' /><br />';

		$fields['email'] = '<label for="fl-email">' . _x( 'Email (will not be published)', 'Comment form label: comment author email.', 'fl-automator' ) . ( $req ? __( ' (required)', 'fl-automator' ) : '' ) . '</label>
									<input type="text" id="fl-email" name="email" class="form-control" value="' . esc_attr( $commenter['comment_author_email'] ) . '" tabindex="2"' . ( $req ? ' aria-required="true"' : '' ) . ' /><br />';

		$fields['url'] = '<label for="fl-url">' . _x( 'Website', 'Comment form label: comment author website.', 'fl-automator' ) . '</label>
									<input type="text" id="fl-url" name="url" class="form-control" value="' . esc_attr( $commenter['comment_author_url'] ) . '" tabindex="3" /><br />';

		return $fields;
	}

	/**
	 * Filter woocommerce mobile breakpoint.
	 *
	 * @param $px The current breakpoint.
	 * @since 1.7
	 */
	static public function woo_mobile_breakpoint( $px ) {
		$px = '767px';
		return $px;
	}

	/**
	 * @since 1.7.1
	 */
	static public function pingback_url() {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
		}
	}

	/**
	 * Filters a menu item's starting output for submenu indicator.
	 *
	 * @param string   $item_output The menu item's starting HTML output.
	 * @param WP_Post  $item        Menu item data object.
	 * @param int      $depth       Depth of menu item. Used for padding.
	 * @param stdClass $args        An object of wp_nav_menu() arguments.
	 * @since 1.7.3
	 */
	static public function nav_menu_start_el( $item_output, $item, $depth, $args ) {
		if ( ! in_array( 'fl-theme-menu', explode( ' ', $args->menu_class ), true ) ) {
			return $item_output;
		}
		if ( ! in_array( 'menu-item-has-children', $item->classes, true ) ) {
			return $item_output;
		}

		$icon        = '<div class="fl-submenu-icon-wrap"><span class="fl-submenu-toggle-icon"></span></div>';
		$item_output = str_replace( '</a>', '</a>' . $icon, $item_output );

		return $item_output;
	}
	/**
	 * @since 1.7.3
	 */
	static public function is_schema_enabled() {

		/**
		 * Disable all schema.
		 * @see fl_theme_disable_schema
		 */
		if ( false !== apply_filters( 'fl_theme_disable_schema', false ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @since 1.7.3
	 */
	static public function print_schema( $schema, $echo = true ) {
		if ( self::is_schema_enabled() ) {
			$schema = apply_filters( 'fl_theme_print_schema', $schema );
			if ( $echo ) {
				echo $schema;
			} else {
				return $schema;
			}
		}
	}

	/**
	 * @since 1.7.4
	 */
	public static function comments_popup_link_attributes( $attrs ) {
		return $attrs . ' tabindex="-1" aria-hidden="true"';
	}

	/**
	 * @since 1.7.4
	 */
	public static function skip_to_link() {
		printf( '<a aria-label="%1$s" class="fl-screen-reader-text" href="#fl-main-content">%1$s</a>', __( 'Skip to content', 'fl-automator' ) );
	}

	public static function comment_form_defaults( $args ) {

		global $user_identity;
		$args = array(
			'id_form'              => 'fl-comment-form',
			'class_form'           => 'fl-comment-form',
			'id_submit'            => 'fl-comment-form-submit',
			'class_submit'         => 'btn btn-primary',
			'name_submit'          => 'submit',
			'label_submit'         => __( 'Submit Comment', 'fl-automator' ),
			'title_reply'          => _x( 'Leave a Comment', 'Respond form title.', 'fl-automator' ),
			'title_reply_to'       => __( 'Leave a Reply', 'fl-automator' ),
			'cancel_reply_link'    => __( 'Cancel Reply', 'fl-automator' ),
			'format'               => 'xhtml',
			'comment_notes_before' => '',
			'comment_notes_after'  => '',

			'comment_field'        => '<label for="fl-comment">' . _x( 'Comment', 'Comment form label: comment content.', 'fl-automator' ) . '</label><textarea id="fl-comment" name="comment" class="form-control" cols="60" rows="8" tabindex="4"></textarea><br />',
			/* translators: %s: Please, keep the HTML tags */
			'must_log_in'          => '<p>' . sprintf( _x( 'You must be <a%s>logged in</a> to post a comment.', 'Please, keep the HTML tags.', 'fl-automator' ), ' href="' . esc_url( wp_login_url() ) . '?redirect_to=' . urlencode( get_permalink() ) . '"' ) . '</p>',
			/* translators: %s: user name */
			'logged_in_as'         => '<p>' . sprintf( __( 'Logged in as %s.', 'fl-automator' ), '<a href="' . esc_url( home_url( '/wp-admin/profile.php' ) ) . '">' . $user_identity . '</a>' ) . ' <a href="' . wp_logout_url( get_permalink() ) . '" title="' . __( 'Log out of this account', 'fl-automator' ) . '">' . __( 'Log out &raquo;', 'fl-automator' ) . '</a></p>',
		);
		return $args;
	}
}
