<?php

/**
 * Main builder class.
 *
 * @since 1.0
 */
final class FLBuilder {

	/**
	 * The ID of a post that is currently being rendered.
	 *
	 * @since 1.6.4.2
	 * @var int $post_rendering
	 */
	static public $post_rendering = null;

	/**
	 * Stores the default directory name to look for in a theme for BB templates.
	 *
	 * @since 1.5.9-cf
	 * @var string $template_dir
	 */
	static private $template_dir = 'fl-builder/includes';

	/**
	 * An array of asset paths that have already been rendered. This is
	 * used to ensure that the same asset isn't rendered twice on the same
	 * page. That typically can happen when you do things like insert the
	 * same layout twice using the fl_builder_insert_layout shortcode.
	 *
	 * @since 2.0
	 * @var bool $rendered_assets
	 */
	static private $rendered_assets = array();

	/**
	 * An array of which global assets have already been enqueued. This is
	 * used to ensure that only one copy of either the global CSS or JS is
	 * ever loaded on the page at one time.
	 *
	 * For example, if a layout CSS file with the global CSS included in it
	 * has already been enqueued, subsequent layout CSS files will not include
	 * the global CSS.
	 *
	 * @since 1.8.2
	 * @var bool $enqueued_global_assets
	 */
	static private $enqueued_global_assets = array();

	/**
	 * @since 2.1.6
	 */
	static private $enqueued_module_js_assets  = array();
	static private $enqueued_module_css_assets = array();

	/**
	 * Used to store JS that is to be rendered inline on the wp_footer
	 * action when the fl_builder_render_assets_inline filter is true.
	 *
	 * @since 2.1
	 * @var string $inline_js
	 */
	static private $inline_js = '';

	/**
	 * Font awesome urls.
	 * @since 2.1
	 */
	static public $fa4_url     = '';
	static public $fa5_pro_url = 'https://pro.fontawesome.com/releases/v5.15.4/css/all.css';

	/**
	 * Initializes hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textdomain' );
		add_action( 'send_headers', __CLASS__ . '::no_cache_headers' );
		add_action( 'wp', __CLASS__ . '::init_ui', 11 );
		add_action( 'wp', __CLASS__ . '::rich_edit' );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::register_layout_styles_scripts' );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_ui_styles_scripts', 11 );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_all_layouts_styles_scripts' );
		add_action( 'wp_head', __CLASS__ . '::render_custom_css_for_editing', 999 );
		add_action( 'admin_bar_menu', __CLASS__ . '::admin_bar_menu', 999 );
		add_action( 'wp_footer', __CLASS__ . '::render_ui' );

		// TODO this needs fixing.
		//add_action( 'wp_footer', __CLASS__ . '::check_content_filters', 1000 );

		/* Filters */
		add_filter( 'fl_builder_render_css', __CLASS__ . '::rewrite_css_cache_urls', 9999 );
		add_filter( 'body_class', __CLASS__ . '::body_class' );
		add_filter( 'wp_default_editor', __CLASS__ . '::default_editor' );
		add_filter( 'mce_css', __CLASS__ . '::add_editor_css' );
		add_filter( 'mce_buttons', __CLASS__ . '::editor_buttons' );
		add_filter( 'mce_buttons_2', __CLASS__ . '::editor_buttons_2' );
		add_filter( 'mce_external_plugins', __CLASS__ . '::editor_external_plugins', 9999 );
		add_filter( 'tiny_mce_before_init', __CLASS__ . '::editor_font_sizes' );
		add_filter( 'the_content', __CLASS__ . '::render_content' );
		add_filter( 'wp_handle_upload_prefilter', __CLASS__ . '::wp_handle_upload_prefilter_filter' );
		add_filter( 'wp_link_query_args', __CLASS__ . '::wp_link_query_args_filter' );
		add_filter( 'fl_builder_load_modules_paths', __CLASS__ . '::load_module_paths', 9999 );
	}

	/**
	 * @since 2.3
	 */
	static public function load_module_paths( $paths ) {

		$enabled      = array();
		$dependencies = self::_module_dependencies();
		$protected    = array();

		if ( is_admin() ) {
			return $paths;
		}

		if ( ! self::is_module_disable_enabled() ) {
			return $paths;
		}

		$enabled_modules = FLBuilderModel::get_enabled_modules();

		if ( is_array( $enabled_modules ) && empty( $enabled_modules ) ) {
			return $paths;
		}

		if ( isset( $enabled_modules[0] ) && 'all' === $enabled_modules[0] ) {
			return $paths;
		}

		// setup reverse dependencies
		foreach ( $paths as $k => $path ) {
			$module = basename( $path );
			$deps   = isset( $dependencies[ $module ] ) ? $dependencies[ $module ] : array();
			if ( count( $deps ) > 0 ) {
				foreach ( $deps as $dep ) {
					$protected[] = $dep;
				}
			}
		}

		foreach ( $paths as $k => $path ) {
			$module = basename( $path );

			if ( in_array( $module, $enabled_modules, true ) || in_array( $module, $protected, true ) ) {
				$enabled[] = $path;
			}
		}
		return ! empty( $enabled ) ? $enabled : $paths;
	}

	/**
	 * @since 2.3
	 */
	public static function is_module_disable_enabled() {
		/**
		 * Enable Module enable/disable advanced mode.
		 * @since 2.3
		 * @see is_module_disable_enabled
		 */
		return apply_filters( 'is_module_disable_enabled', false );
	}

	/**
	 * @since 2.3
	 */
	public static function _module_dependencies() {
		$deps = array(
			'post-carousel'  => array(
				'post-grid',
				'photo',
			),
			'post-gallery'   => array(
				'post-grid',
			),
			'post-grid'      => array(
				'button',
			),
			'post-slider'    => array(
				'photo',
			),
			'subscribe-form' => array(
				'button',
			),
			'callout'        => array(
				'button',
				'photo',
				'icon',
			),
			'contact-form'   => array(
				'button',
			),
			'content-slider' => array(
				'button',
			),
			'cta'            => array(
				'button',
			),
			'gallery'        => array(
				'photo',
			),
			'icon-group'     => array(
				'icon',
			),
			'pricing-table'  => array(
				'button',
			),
			'menu'           => array(
				'search',
			),
		);
		return apply_filters( 'fl_module_dependencies', $deps );
	}


	/**
	 * Localization
	 *
	 * Load the translation file for current language. Checks the default WordPress
	 * languages folder first and then the languages folder inside the plugin.
	 *
	 * @since 1.4.4
	 * @return string|bool The translation file path or false if none is found.
	 */
	static public function load_plugin_textdomain() {
		// Traditional WordPress plugin locale filter
		// Uses get_user_locale() which was added in 4.7 so we need to check its available.
		if ( function_exists( 'get_user_locale' ) ) {
			$locale = apply_filters( 'plugin_locale', get_user_locale(), 'fl-builder' );
		} else {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'fl-builder' );
		}

		/**
		 * Allow users to override the locale.
		 * @see fl_set_ui_locale
		 * @since 2.2.4
		 */
		$locale = apply_filters( 'fl_set_ui_locale', $locale );

		//Setup paths to current locale file
		$mofile_global = trailingslashit( WP_LANG_DIR ) . 'plugins/bb-plugin/' . $locale . '.mo';
		$mofile_local  = trailingslashit( FL_BUILDER_DIR ) . 'languages/' . $locale . '.mo';

		if ( file_exists( $mofile_global ) ) {
			//Look in global /wp-content/languages/plugins/bb-plugin/ folder
			return load_textdomain( 'fl-builder', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			//Look in local /wp-content/plugins/bb-plugin/languages/ folder
			return load_textdomain( 'fl-builder', $mofile_local );
		}

		//Nothing found
		return false;
	}

	static public function rich_edit() {
		global $wp_version;
		if ( FLBuilderModel::is_builder_active() ) {
			if ( version_compare( $wp_version, '5.4.99', '<' ) ) {
				add_filter( 'get_user_option_rich_editing', '__return_true' );
			} else {
				add_filter( 'user_can_richedit', '__return_true' ); // WP 5.5
			}
		}
	}

	/**
	 * Alias method for registering a template data file with the builder.
	 *
	 * @since 1.8
	 * @param string $path The directory path to the template data file.
	 * @return void
	 */
	static public function register_templates( $path, $args = array() ) {
		FLBuilderModel::register_templates( $path, $args );
	}

	/**
	 * Alias method for registering a module with the builder.
	 *
	 * @since 1.0
	 * @param string $class The module's PHP class name.
	 * @param array $form The module's settings form data.
	 * @return void
	 */
	static public function register_module( $class, $form ) {
		FLBuilderModel::register_module( $class, $form );
	}

	/**
	 * Alias method for registering module aliases with the builder.
	 *
	 * @since 1.10
	 * @param string $alias The alias key.
	 * @param array $config The alias config.
	 * @return void
	 */
	static public function register_module_alias( $alias, $config ) {
		FLBuilderModel::register_module_alias( $alias, $config );
	}

	/**
	 * Alias method for registering a settings form with the builder.
	 *
	 * @since 1.0
	 * @param string $id The form's ID.
	 * @param array $form The form data.
	 * @return void
	 */
	static public function register_settings_form( $id, $form ) {
		FLBuilderModel::register_settings_form( $id, $form );
	}

	/**
	 * Send no cache headers when the builder interface is active.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function no_cache_headers() {
		if ( isset( $_GET['fl_builder'] ) ) {
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
			header( 'Cache-Control: no-store, no-cache, must-revalidate' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
			header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		}
	}

	/**
	 * Returns the markup for creating new WP editors in the builder.
	 *
	 * @since 2.0
	 * @return string
	 */
	static public function get_wp_editor() {
		ob_start();
		/**
		 * Args passed to wp_editor for text modules.
		 * @see fl_get_wp_editor_args
		 */
		wp_editor( '{FL_EDITOR_CONTENT}', 'flbuildereditor', apply_filters( 'fl_get_wp_editor_args', array(
			'media_buttons' => true,
			'wpautop'       => true,
			'textarea_rows' => 16,
		) ) );

		return ob_get_clean();
	}

	/**
	 * Set the default text editor to tinymce when the builder is active.
	 *
	 * @since 1.0
	 * @param string $type The current default editor type.
	 * @return string
	 */
	static public function default_editor( $type ) {
		return FLBuilderModel::is_builder_active() ? 'tinymce' : $type;
	}

	/**
	 * Add custom CSS for the builder to the text editor.
	 *
	 * @since 1.0
	 * @param string $mce_css
	 * @return string
	 */
	static public function add_editor_css( $mce_css ) {
		if ( FLBuilderModel::is_builder_active() ) {

			if ( ! empty( $mce_css ) ) {
				$mce_css .= ',';
			}

			$mce_css .= FL_BUILDER_URL . 'css/editor.css';
		}

		return $mce_css;
	}

	/**
	 * Filter text editor buttons for the first row
	 *
	 * @since 1.0
	 * @param array $buttons The current buttons array.
	 * @return array
	 */
	static public function editor_buttons( $buttons ) {
		if ( FLBuilderModel::is_builder_active() ) {
			if ( ( $key = array_search( 'wp_more', $buttons ) ) !== false ) { // @codingStandardsIgnoreLine
				unset( $buttons[ $key ] );
			}
		}

		return $buttons;
	}

	/**
	 * Add additional buttons to the text editor.
	 *
	 * @since 1.0
	 * @param array $buttons The current buttons array.
	 * @return array
	 */
	static public function editor_buttons_2( $buttons ) {
		global $wp_version;

		if ( FLBuilderModel::is_builder_active() ) {

			array_shift( $buttons );
			array_unshift( $buttons, 'fontsizeselect' );

			if ( version_compare( $wp_version, '4.6.9', '<=' ) ) {
				array_unshift( $buttons, 'formatselect' );
			}

			if ( ( $key = array_search( 'wp_help', $buttons ) ) !== false ) { // @codingStandardsIgnoreLine
				unset( $buttons[ $key ] );
			}
		}

		return $buttons;
	}

	/**
	 * Custom font size options for the editor font size select.
	 *
	 * @since 1.6.3
	 * @param array $init The TinyMCE init array.
	 * @return array
	 */
	static public function editor_font_sizes( $init ) {
		if ( FLBuilderModel::is_builder_active() ) {
			$init['fontsize_formats'] = implode( ' ', array(
				'10px',
				'12px',
				'14px',
				'16px',
				'18px',
				'20px',
				'22px',
				'24px',
				'26px',
				'28px',
				'30px',
				'32px',
				'34px',
				'36px',
				'38px',
				'40px',
				'42px',
				'44px',
				'46px',
				'48px',
			));
		}

		return $init;
	}

	/**
	 * Only allows certain text editor plugins to avoid conflicts
	 * with third party plugins.
	 *
	 * @since 1.0
	 * @param array $plugins The current editor plugins.
	 * @return array
	 */
	static public function editor_external_plugins( $plugins ) {
		if ( FLBuilderModel::is_builder_active() ) {

			$allowed = array(
				'anchor',
				'code',
				'insertdatetime',
				'nonbreaking',
				'print',
				'searchreplace',
				'table',
				'visualblocks',
				'visualchars',
				'emoticons',
				'advlist',
				'wptadv',
			);

			foreach ( $plugins as $key => $val ) {
				if ( ! in_array( $key, $allowed ) ) {
					unset( $plugins[ $key ] );
				}
			}
		}

		return $plugins;
	}

	/**
	 * Register the styles and scripts for builder layouts.
	 *
	 * @since 1.7.4
	 * @return void
	 */
	static public function register_layout_styles_scripts() {
		$ver     = FL_BUILDER_VERSION;
		$css_url = FL_BUILDER_URL . 'css/';
		$js_url  = FL_BUILDER_URL . 'js/';
		$min     = ( self::is_debug() ) ? '' : '.min';

		// Register additional CSS
		wp_register_style( 'fl-slideshow', $css_url . 'fl-slideshow' . $min . '.css', array( 'yui3' ), $ver );
		wp_register_style( 'jquery-bxslider', $css_url . 'jquery.bxslider.css', array(), $ver );
		wp_register_style( 'jquery-magnificpopup', $css_url . 'jquery.magnificpopup' . $min . '.css', array(), $ver );
		wp_register_style( 'yui3', $css_url . 'yui3.css', array(), $ver );

		// Register icon CDN CSS
		wp_register_style( 'font-awesome-5', self::get_fa5_url(), array(), $ver );
		wp_register_style( 'font-awesome', FL_BUILDER_URL . 'fonts/fontawesome/' . self::get_fa5_version() . '/css/v4-shims.min.css', array( 'font-awesome-5' ), $ver );

		wp_register_style( 'foundation-icons', 'https://cdnjs.cloudflare.com/ajax/libs/foundicons/3.0.0/foundation-icons.css', array(), $ver );

		// Register additional JS
		wp_register_script( 'fl-slideshow', $js_url . 'fl-slideshow' . $min . '.js', array( 'yui3' ), $ver, true );
		wp_register_script( 'fl-gallery-grid', $js_url . 'fl-gallery-grid.js', array( 'jquery' ), $ver, true );
		wp_register_script( 'jquery-bxslider', $js_url . 'jquery.bxslider' . $min . '.js', array( 'jquery-easing', 'jquery-fitvids' ), $ver, true );
		wp_register_script( 'jquery-easing', $js_url . 'jquery.easing.min.js', array( 'jquery' ), '1.4', true );
		wp_register_script( 'jquery-fitvids', $js_url . 'jquery.fitvids.min.js', array( 'jquery' ), '1.2', true );
		wp_register_script( 'jquery-infinitescroll', $js_url . 'jquery.infinitescroll.min.js', array( 'jquery' ), $ver, true );
		wp_register_script( 'jquery-magnificpopup', $js_url . 'jquery.magnificpopup' . $min . '.js', array( 'jquery' ), $ver, true );
		wp_register_script( 'jquery-mosaicflow', $js_url . 'jquery.mosaicflow' . $min . '.js', array( 'jquery' ), $ver, true );
		wp_register_script( 'jquery-waypoints', $js_url . 'jquery.waypoints.min.js', array( 'jquery' ), $ver, true );
		wp_register_script( 'jquery-wookmark', $js_url . 'jquery.wookmark.min.js', array( 'jquery' ), $ver, true );
		wp_register_script( 'jquery-throttle', $js_url . 'jquery.ba-throttle-debounce.min.js', array( 'jquery' ), $ver, true );
		wp_register_script( 'yui3', $js_url . 'yui3.min.js', array(), $ver, true );
		wp_register_script( 'youtube-player', 'https://www.youtube.com/iframe_api', array(), $ver, true );
		wp_register_script( 'vimeo-player', 'https://player.vimeo.com/api/player.js', array(), $ver, true );
		wp_deregister_script( 'imagesloaded' );
		wp_register_script( 'imagesloaded', includes_url( 'js/imagesloaded.min.js' ), array( 'jquery' ) );
	}

	/**
	 * Enqueue the styles and scripts for all builder layouts
	 * in the main WordPress query.
	 *
	 * @since 1.7.4
	 * @return void
	 */
	static public function enqueue_all_layouts_styles_scripts() {
		global $wp_query;
		global $post;

		$original_post = $post;
		$is_archive    = is_archive() || is_home() || is_search();

		// Enqueue assets for posts in the main query.
		if ( ! $is_archive && isset( $wp_query->posts ) ) {
			foreach ( $wp_query->posts as $post ) {
				self::enqueue_layout_styles_scripts();
			}
		}

		// Enqueue assets for posts via the fl_builder_global_posts filter.
		$post_ids = FLBuilderModel::get_global_posts();

		if ( count( $post_ids ) > 0 ) {

			$posts = get_posts(array(
				'post__in'       => $post_ids,
				'post_type'      => get_post_types(),
				'posts_per_page' => -1,
			));

			foreach ( $posts as $post ) {
				self::enqueue_layout_styles_scripts();
			}
		}

		// Reset the global post variable.
		$post = $original_post;
	}

	/**
	 * Enqueue the styles and scripts for a single layout.
	 *
	 * @since 1.0
	 * @param bool $rerender Whether to rerender the CSS and JS.
	 * @return void
	 */
	static public function enqueue_layout_styles_scripts( $rerender = false ) {
		if ( FLBuilderModel::is_builder_enabled() ) {

			$nodes = FLBuilderModel::get_categorized_nodes();

			// Enqueue required row CSS and JS
			foreach ( $nodes['rows'] as $row ) {
				if ( 'slideshow' == $row->settings->bg_type ) {
					wp_enqueue_script( 'yui3' );
					wp_enqueue_script( 'fl-slideshow' );
					wp_enqueue_script( 'imagesloaded' );
					wp_enqueue_style( 'fl-slideshow' );
				} elseif ( 'video' == $row->settings->bg_type ) {
					wp_enqueue_script( 'imagesloaded' );
					if ( 'video_service' == $row->settings->bg_video_source ) {

						$video_data = FLBuilderUtils::get_video_data( do_shortcode( $row->settings->bg_video_service_url ) );
						if ( isset( $video_data['type'] ) ) {
							if ( 'youtube' == $video_data['type'] ) {
								wp_enqueue_script( 'youtube-player' );
							} elseif ( 'vimeo' == $video_data['type'] ) {
								wp_enqueue_script( 'vimeo-player' );
							}
						}
					}
				}
				if ( is_array( $row->settings->animation ) && ! empty( $row->settings->animation['style'] ) ) {
					wp_enqueue_script( 'jquery-waypoints' );
				}
			}

			// Enqueue required column CSS and JS
			foreach ( $nodes['columns'] as $col ) {
				if ( is_array( $col->settings->animation ) && ! empty( $col->settings->animation['style'] ) ) {
					wp_enqueue_script( 'jquery-waypoints' );
				}
			}

			// Enqueue required module CSS and JS
			foreach ( $nodes['modules'] as $module ) {

				$module->enqueue_icon_styles();
				$module->enqueue_font_styles();
				$module->enqueue_scripts();

				foreach ( $module->css as $handle => $props ) {
					wp_enqueue_style( $handle, $props[0], $props[1], $props[2], $props[3] );
				}
				foreach ( $module->js as $handle => $props ) {
					wp_enqueue_script( $handle, $props[0], $props[1], $props[2], $props[3] );
				}
				if ( is_array( $module->settings->animation ) && ! empty( $module->settings->animation['style'] ) ) {
					wp_enqueue_script( 'jquery-waypoints' );
				}
			}

			// Enqueue Google Fonts
			FLBuilderFonts::enqueue_google_fonts();

			// Enqueue layout CSS
			self::enqueue_layout_cached_asset( 'css', $rerender );

			// Enqueue layout JS
			self::enqueue_layout_cached_asset( 'js', $rerender );
		}
	}

	/**
	 * Enqueue the styles and scripts for a single layout
	 * using the provided post ID.
	 *
	 * @since 1.10
	 * @param int $post_id
	 * @return void
	 */
	static public function enqueue_layout_styles_scripts_by_id( $post_id ) {
		FLBuilderModel::set_post_id( $post_id );
		FLBuilder::enqueue_layout_styles_scripts();
		FLBuilderModel::reset_post_id();
	}

	/**
	 * Enqueues the cached CSS or JS asset for a layout.
	 *
	 * @since 1.8.2
	 * @access private
	 * @param string $type The type of asset. Either CSS or JS.
	 * @param bool $rerender Whether to rerender the CSS or JS.
	 * @return string
	 */
	static private function enqueue_layout_cached_asset( $type = 'css', $rerender = false ) {
		$post_id    = FLBuilderModel::get_post_id();
		$asset_info = FLBuilderModel::get_asset_info();
		$asset_ver  = FLBuilderModel::get_asset_version();
		$active     = FLBuilderModel::is_builder_active();
		$preview    = FLBuilderModel::is_builder_draft_preview();
		$handle     = 'fl-builder-layout-' . $post_id;
		/**
		 * Use this filter to add dependencies to the dependency array when the main builder layout CSS file is enqueued using wp_enqueue_style.
		 * @see fl_builder_layout_style_dependencies
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		$css_deps  = apply_filters( 'fl_builder_layout_style_dependencies', array() );
		$css_media = apply_filters( 'fl_builder_layout_style_media', 'all' );

		// Enqueue with the global code included?
		if ( in_array( 'global-' . $type, self::$enqueued_global_assets ) ) {
			$path   = $asset_info[ $type . '_partial' ];
			$url    = $asset_info[ $type . '_partial_url' ];
			$global = false;
		} else {
			$path                           = $asset_info[ $type ];
			$url                            = $asset_info[ $type . '_url' ];
			$global                         = true;
			self::$enqueued_global_assets[] = 'global-' . $type;
		}

		// Render the asset inline instead of enqueuing the file?
		if ( 'inline' === FLBuilderModel::get_asset_enqueue_method() ) {

			// Bail if we've already rendered this.
			if ( in_array( $path, self::$rendered_assets ) ) {
				return;
			} else {
				self::$rendered_assets[] = $path;
			}

			// Enqueue inline.
			if ( 'css' === $type ) {
				wp_register_style( $handle, false, $css_deps, $asset_ver, $css_media );
				wp_enqueue_style( $handle );
				wp_add_inline_style( $handle, self::render_css( $global ) );
			} else {
				self::$inline_js .= self::render_js( $global );
				if ( ! has_action( 'wp_footer', __CLASS__ . '::render_inline_js' ) ) {
					add_action( 'wp_footer', __CLASS__ . '::render_inline_js', PHP_INT_MAX );
				}
			}
		} else {

			// Render if the file doesn't exist.
			if ( ! in_array( $path, self::$rendered_assets ) && ( ! fl_builder_filesystem()->file_exists( $path ) || $rerender || $preview || self::is_debug() ) ) {
				call_user_func_array( array( 'FLBuilder', 'render_' . $type ), array( $global ) );
				self::$rendered_assets[] = $path;
			}

			// Don't enqueue if we don't have a file after trying to render.
			if ( ! fl_builder_filesystem()->file_exists( $path ) || 0 === fl_builder_filesystem()->filesize( $path ) ) {
				return;
			}

			if ( $global ) {
				$asset_ver = FLBuilderModel::get_asset_version( $path );
			}

			// Enqueue.
			if ( 'css' == $type ) {
				wp_enqueue_style( $handle, $url, $css_deps, $asset_ver, $css_media );
			} elseif ( 'js' == $type ) {
				wp_enqueue_script( $handle, $url, array( 'jquery' ), $asset_ver, true );
			}
		}
	}

	/**
	 *
	 *
	 * @since 2.1
	 * @return void
	 */
	static public function render_inline_js() {
		echo '<script>' . self::$inline_js . '</script>';
	}

	/**
	 * Clears the enqueued global assets cache to ensure new asset
	 * renders include global node assets.
	 *
	 * @since 1.10.2
	 * @return void
	 */
	static public function clear_enqueued_global_assets() {
		self::$enqueued_global_assets = array();
	}

	/**
	 * Register common JS vendors
	 * This is primarily for consistent sharing with Assistant.
	 *
	 * @return void
	 */
	static public function register_shared_vendors() {
		global $wp_version;

		$ver        = FL_BUILDER_VERSION;
		$css_build  = FL_BUILDER_URL . 'css/build/';
		$js_vendors = FL_BUILDER_URL . 'js/vendors/';
		$js_build   = FL_BUILDER_URL . 'js/build/';
		$tag        = '.bundle.min';
		$vendor_tag = '.min';
		// @beaverbuilder/app-core
		$app_core_deps = array( 'react', 'redux', 'react-router-dom', 'wp-i18n' );
		// @beaverbuilder/fluid
		$fluid_deps = array(
			'react',
			'react-dom',
			'redux',
			'react-router-dom',
			'framer-motion',
			'react-laag',
			'wp-i18n',
			'bb-icons',
		);

		if ( self::is_debug() ) {
			$tag        = '.bundle';
			$vendor_tag = '';
		}

		// React polyfill for older versions of WordPress.
		if ( version_compare( $wp_version, '5.2', '<=' ) ) {

			// React
			wp_deregister_script( 'react' );
			wp_enqueue_script( 'react', "{$js_vendors}react{$vendor_tag}.js", array(), $ver, true );

			// React-DOM
			wp_deregister_script( 'react-dom' );
			wp_enqueue_script( 'react-dom', "{$js_vendors}react-dom{$vendor_tag}.js", array(), $ver, true );

			// @beaverbuilder/app-core
			$app_core_deps = array( 'react', 'redux', 'react-router-dom' );
			// @beaverbuilder/fluid
			$fluid_deps = array(
				'react',
				'react-dom',
				'redux',
				'react-router-dom',
				'framer-motion',
				'react-laag',
				'bb-icons',
			);
			if ( ! wp_script_is( 'wp-i18n', 'registered' ) ) {
				wp_enqueue_script( 'fl-wp-i18n', "{$js_vendors}i18n-polyfill.js" );
			}
		}

		/**
		 * Shared Vendors
		 * These vendor bundles are special in that they attach a global reference to themselves on the FL.vendors object.
		 */

		// redux
		wp_register_script( 'redux', "{$js_vendors}redux.min.js", array(), $ver, false );

		// react-router-dom
		wp_register_script( 'react-router-dom', "{$js_vendors}react-router-dom.min.js", array( 'react' ), $ver, false );

		// framer-motion
		wp_register_script( 'framer-motion', "{$js_build}vendor-framer-motion{$tag}.js", array( 'react', 'react-dom' ), $ver, false );

		// react-laag
		wp_register_script( 'react-laag', "{$js_build}vendor-react-laag{$tag}.js", array( 'react' ), $ver, false );

		wp_register_script( 'bb-app-core', "{$js_build}vendor-bb-app-core{$tag}.js", $app_core_deps, $ver, false );

		// @beaverbuilder/icons
		wp_register_script( 'bb-icons', "{$js_build}vendor-bb-icons{$tag}.js", array( 'react' ), $ver, false );

		wp_register_script( 'bb-fluid', "{$js_build}vendor-bb-fluid{$tag}.js", $fluid_deps, $ver, false );
		wp_register_style( 'bb-fluid', "{$css_build}vendor-bb-fluid{$tag}.css", array(), $ver, null );
	}

	/**
	 * Register and enqueue the styles and scripts for the builder UI.
	 *
	 * @since 1.7.4
	 * @return void
	 */
	static public function enqueue_ui_styles_scripts() {
		if ( FLBuilderModel::is_builder_active() ) {
			global $wp_the_query;
			global $wp_version;

			// Remove wp admin bar top margin
			remove_action( 'wp_head', '_admin_bar_bump_cb' );

			$ver     = FL_BUILDER_VERSION;
			$css_url = FL_BUILDER_URL . 'css/';
			$js_url  = FL_BUILDER_URL . 'js/';

			// Register React and other vendor bundles
			self::register_shared_vendors();

			/* Frontend builder styles */
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'font-awesome-5' );
			wp_enqueue_style( 'foundation-icons' );
			wp_enqueue_style( 'jquery-nanoscroller', $css_url . 'jquery.nanoscroller.css', array(), $ver );
			wp_enqueue_style( 'jquery-autosuggest', $css_url . 'jquery.autoSuggest.min.css', array(), $ver );
			wp_enqueue_style( 'fl-jquery-tiptip', $css_url . 'jquery.tiptip.css', array(), $ver );
			wp_enqueue_style( 'bootstrap-tour', $css_url . 'bootstrap-tour-standalone.min.css', array(), $ver );
			if ( true === apply_filters( 'fl_select2_enabled', true ) ) {
				wp_enqueue_style( 'select2', $css_url . 'select2.min.css', array(), $ver );
			}

			// Enqueue individual builder styles if WP_DEBUG is on.
			if ( self::is_debug() ) {
				wp_enqueue_style( 'fl-color-picker', $css_url . 'fl-color-picker.css', array(), $ver );
				wp_enqueue_style( 'fl-lightbox', $css_url . 'fl-lightbox.css', array(), $ver );
				wp_enqueue_style( 'fl-icon-selector', $css_url . 'fl-icon-selector.css', array(), $ver );
				wp_enqueue_style( 'fl-builder', $css_url . 'fl-builder.css', array(), $ver );

				// skins need to come after default ui styles
				wp_enqueue_style( 'fl-builder-ui-skin-dark', $css_url . 'fl-builder-ui-skin-dark.css', array(), $ver );

				wp_enqueue_style( 'fl-builder-system', $css_url . 'build/builder.bundle.css', array( 'bb-fluid' ), $ver );
			} else {
				wp_enqueue_style( 'fl-builder-min', $css_url . 'fl-builder.min.css', array(), $ver );
				wp_enqueue_style( 'fl-builder-system', $css_url . 'build/builder.bundle.min.css', array( 'bb-fluid' ), $ver );
			}

			/* Custom Icons */
			FLBuilderIcons::enqueue_all_custom_icons_styles();

			/* RTL Support */
			if ( is_rtl() ) {
				wp_enqueue_style( 'fl-builder-rtl', $css_url . 'fl-builder-rtl.css', array(), $ver );
			}

			/* We have custom versions of these that fixes bugs. */
			wp_deregister_script( 'jquery-ui-sortable' );
			wp_dequeue_script( 'jquery-touch-punch' );
			wp_deregister_script( 'jquery-touch-punch' );
			wp_register_script( 'jquery-touch-punch', $js_url . 'jquery.touch-punch.min.js', array(), $ver );

			/* Frontend builder scripts */
			wp_enqueue_media();
			wp_enqueue_script( 'heartbeat' );
			wp_enqueue_script( 'wpdialogs' );
			wp_enqueue_script( 'wpdialogs-popup' );
			wp_enqueue_script( 'wplink' );
			wp_enqueue_script( 'editor' );
			wp_enqueue_script( 'quicktags' );
			wp_enqueue_script( 'json2' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script( 'jquery-ui-position' );
			wp_enqueue_script( 'jquery-touch-punch' );
			wp_enqueue_script( 'jquery-throttle' );

			/**
			 * Before jquery.ui.sortable.js is enqueued.
			 * @see fl_before_sortable_enqueue
			 */
			do_action( 'fl_before_sortable_enqueue' );

			wp_enqueue_script( 'jquery-ui-sortable', $js_url . 'jquery.ui.sortable.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse' ), $ver );
			wp_enqueue_script( 'jquery-nanoscroller', $js_url . 'jquery.nanoscroller.min.js', array(), $ver );
			wp_enqueue_script( 'jquery-autosuggest', $js_url . 'jquery.autoSuggest.min.js', array(), $ver );
			wp_enqueue_script( 'fl-jquery-tiptip', $js_url . 'jquery.tiptip.min.js', array(), $ver );
			wp_enqueue_script( 'jquery-showhideevents', $js_url . 'jquery.showhideevents.js', array(), $ver );
			wp_enqueue_script( 'jquery-simulate', $js_url . 'jquery.simulate.js', array(), $ver );
			wp_enqueue_script( 'jquery-validate', $js_url . 'jquery.validate.min.js', array(), $ver );
			wp_enqueue_script( 'clipboard', $js_url . 'clipboard.min.js', array(), $ver );
			wp_enqueue_script( 'bootstrap-tour', $js_url . 'bootstrap-tour-standalone.min.js', array(), $ver );
			wp_enqueue_script( 'ace', $js_url . 'ace/ace.js', array(), $ver );
			wp_enqueue_script( 'ace-language-tools', $js_url . 'ace/ext-language_tools.js', array(), $ver );
			wp_enqueue_script( 'mousetrap', $js_url . 'mousetrap-custom.js', array(), $ver );
			if ( true === apply_filters( 'fl_select2_enabled', true ) ) {
				wp_enqueue_script( 'select2', $js_url . 'select2.min.js', array(), $ver );
			}

			// Enqueue individual builder scripts if WP_DEBUG is on.
			$bundle_deps = array( 'react', 'react-dom', 'bb-app-core', 'bb-fluid' );
			if ( self::is_debug() ) {

				wp_enqueue_script( 'fl-color-picker', $js_url . 'fl-color-picker.js', array(), $ver );
				wp_enqueue_script( 'fl-lightbox', $js_url . 'fl-lightbox.js', array(), $ver );
				wp_enqueue_script( 'fl-icon-selector', $js_url . 'fl-icon-selector.js', array(), $ver );
				wp_enqueue_script( 'fl-stylesheet', $js_url . 'fl-stylesheet.js', array(), $ver );
				wp_enqueue_script( 'fl-builder', $js_url . 'fl-builder.js', array( 'jquery' ), $ver );
				wp_enqueue_script( 'fl-builder-libs', $js_url . 'fl-builder-libs.js', array( 'fl-builder' ), $ver );
				wp_enqueue_script( 'fl-builder-ajax-layout', $js_url . 'fl-builder-ajax-layout.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-preview', $js_url . 'fl-builder-preview.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-simulate-media-query', $js_url . 'fl-builder-simulate-media-query.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-responsive-editing', $js_url . 'fl-builder-responsive-editing.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-responsive-preview', $js_url . 'fl-builder-responsive-preview.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-services', $js_url . 'fl-builder-services.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-tour', $js_url . 'fl-builder-tour.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-ui', $js_url . 'fl-builder-ui.js', array( 'fl-builder', 'mousetrap' ), $ver );
				wp_enqueue_script( 'fl-builder-ui-main-menu', $js_url . 'fl-builder-ui-main-menu.js', array( 'fl-builder-ui' ), $ver );
				wp_enqueue_script( 'fl-builder-ui-panel-content', $js_url . 'fl-builder-ui-panel-content-library.js', array( 'fl-builder-ui' ), $ver );
				wp_enqueue_script( 'fl-builder-ui-settings-forms', $js_url . 'fl-builder-ui-settings-forms.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-ui-settings-copy-paste', $js_url . 'fl-builder-ui-settings-copy-paste.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-ui-pinned', $js_url . 'fl-builder-ui-pinned.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-revisions', $js_url . 'fl-builder-revisions.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-search', $js_url . 'fl-builder-search.js', array( 'jquery' ), $ver );
				wp_enqueue_script( 'fl-builder-save-manager', $js_url . 'fl-builder-save-manager.js', array( 'jquery' ), $ver );
				wp_enqueue_script( 'fl-builder-history-manager', $js_url . 'fl-builder-history-manager.js', array(), $ver );
				wp_enqueue_script( 'fl-builder-system', $js_url . 'build/builder.bundle.js', $bundle_deps, $ver, true );
			} else {

				wp_enqueue_script( 'fl-builder-min', $js_url . 'fl-builder.min.js', array( 'jquery', 'mousetrap' ), $ver );
				wp_enqueue_script( 'fl-builder-system', $js_url . 'build/builder.bundle.min.js', $bundle_deps, $ver, true );
			}

			/* Additional module styles and scripts */
			foreach ( FLBuilderModel::$modules as $module ) {

				$module->enqueue_scripts();

				foreach ( $module->css as $handle => $props ) {
					wp_enqueue_style( $handle, $props[0], $props[1], $props[2], $props[3] );
				}
				foreach ( $module->js as $handle => $props ) {
					wp_enqueue_script( $handle, $props[0], $props[1], $props[2], $props[3] );
				}
			}

			/**
			 * Enqueue the canvas script for handling dom manipulation.
			 */
			self::enqueue_canvas();
		}
		wp_add_inline_style( 'admin-bar', '#wp-admin-bar-fl-builder-frontend-edit-link .ab-icon:before { content: "\f116" !important; top: 2px; margin-right: 3px; }' );
		$args = array(
			'product'     => FLBuilderModel::get_branding(),
			'white_label' => FLBuilderModel::is_white_labeled(),
			/**
			 * Custom info text for crash popup.
			 * @see fl_builder_crash_white_label_text
			 */
			'labeled_txt' => apply_filters( 'fl_builder_crash_white_label_text', '' ),
			'vars'        => array(
				'PHP Version'    => phpversion(),
				'Memory Limit'   => FL_Debug::safe_ini_get( 'memory_limit' ),
				'max_input_vars' => FL_Debug::safe_ini_get( 'max_input_vars' ),
				'modsecfix'      => ( defined( 'FL_BUILDER_MODSEC_FIX' ) && FL_BUILDER_MODSEC_FIX ) ? 'Enabled' : 'Disabled',
			),
		);
		wp_localize_script( 'fl-builder-min', 'crash_vars', $args );
		wp_localize_script( 'fl-builder', 'crash_vars', $args );
	}

	/**
	 * Enqueue the canvas API
	 *
	 * @since 2.5
	 * @return void
	 */
	static public function enqueue_canvas() {
		global $wp_the_query;

		$ver     = FL_BUILDER_VERSION;
		$js_url  = FL_BUILDER_URL . 'js/build/';
		$post_id = is_object( $wp_the_query->post ) ? $wp_the_query->post->ID : null;

		if ( self::is_debug() ) {
			wp_enqueue_script( 'fl-builder-canvas', $js_url . 'canvas.bundle.js', array(), $ver, true );
		} else {
			wp_enqueue_script( 'fl-builder-canvas', $js_url . 'canvas.bundle.min.js', array(), $ver, true );
		}

		// FLBuilderCanvasConfig
		$args = array(
			'postId' => $post_id,
		);
		wp_localize_script( 'fl-builder-canvas', 'FLBuilderCanvasConfig', $args );
	}

	/**
	 * Adds builder classes to the body class.
	 *
	 * @since 1.0
	 * @param array $classes An array of existing classes.
	 * @return array
	 */
	static public function body_class( $classes ) {
		$do_render     = apply_filters( 'fl_builder_do_render_content', true, FLBuilderModel::get_post_id() );
		$simple_ui     = ! FLBuilderUserAccess::current_user_can( 'unrestricted_editing' );
		$template_type = FLBuilderModel::get_user_template_type();

		if ( $do_render && FLBuilderModel::is_builder_enabled() && ! is_archive() ) {
			$classes[] = 'fl-builder';
		}
		if ( FLBuilderModel::is_builder_active() ) {
			$classes[] = 'fl-builder-edit';

			// Lite version
			if ( true === FL_BUILDER_LITE ) {
				$classes[] = 'fl-builder-lite';
			}

			// Simple UI
			if ( $simple_ui ) {
				$classes[] = 'fl-builder-simple';
			}

			// Simple pinned UI
			if ( $simple_ui || 'module' === $template_type ) {
				$classes[] = 'fl-builder-simple-pinned';
			}

			// Skin
			$user_settings = FLBuilderUserSettings::get();
			$classes[]     = 'fl-builder-ui-skin--' . $user_settings['skin'];
			$classes[]     = 'fluid-color-scheme-' . $user_settings['skin'];

			// Draft changes
			if ( FLBuilderModel::layout_has_drafted_changes() ) {
				$classes[] = 'fl-builder--layout-has-drafted-changes';
			}

			// RTL
			if ( is_rtl() ) {
				$classes[] = 'fl-builder-direction-rtl';
			} else {
				$classes[] = 'fl-builder-direction-ltr';
			}

			// Has notifications
			$has_new_notifications = FLBuilderNotifications::get_notifications();
			if ( ! $has_new_notifications['read'] ) {
				$classes[] = 'fl-builder-has-new-notifications';
			}
		}

		return $classes;
	}

	/**
	 * Adds the page builder button to the WordPress admin bar.
	 *
	 * @since 1.0
	 * @param object $wp_admin_bar An instance of the WordPress admin bar.
	 * @return void
	 */
	static public function admin_bar_menu( $wp_admin_bar ) {
		global $wp_the_query;

		if ( FLBuilderModel::is_post_editable() && is_object( $wp_the_query->post ) ) {

			$enabled = get_post_meta( $wp_the_query->post->ID, '_fl_builder_enabled', true );
			$dot     = ' <span class="fl-builder-admin-bar-status-dot" style="color:' . ( $enabled ? '#6bc373' : '#d9d9d9' ) . '; font-size:18px; line-height:1;">&bull;</span>';

			$wp_admin_bar->add_node( array(
				'id'    => 'fl-builder-frontend-edit-link',
				'title' => '<span class="ab-icon"></span>' . FLBuilderModel::get_branding() . $dot,
				'href'  => FLBuilderModel::get_edit_url( $wp_the_query->post->ID ),
			));
		}
	}

	static public function locate_template_file( $template_base, $slug ) {
		$specific_template = $template_base . '-' . $slug . '.php';
		$general_template  = $template_base . '.php';
		$default_dir       = trailingslashit( FL_BUILDER_DIR ) . 'includes/';

		// Try to find the specific template, then repeat the same process for general.

		$locate_template_order = apply_filters( 'fl_builder_locate_template_order', array(
			trailingslashit( self::$template_dir ) . $specific_template,
			trailingslashit( self::$template_dir ) . $general_template,
		), self::$template_dir, $template_base, $slug );

		$template_path = locate_template( $locate_template_order );

		if ( ! $template_path ) {
			if ( file_exists( $default_dir . $specific_template ) ) {
				$template_path = $default_dir . $specific_template;
			} elseif ( file_exists( $default_dir . $general_template ) ) {
				$template_path = $default_dir . $general_template;
			}
		}

		return apply_filters( 'fl_builder_template_path', $template_path, $template_base, $slug );
	}

	/**
	 * Initializes the builder interface.
	 *
	 * @since 1.0
	 * @since 1.8 Method name changed from init to init_ui.
	 * @return void
	 */
	static public function init_ui() {
		// Enable editing if the builder is active.
		if ( FLBuilderModel::is_builder_active() && ! FLBuilderAJAX::doing_ajax() ) {

			/**
			 * Fire an action as the builder inits.
			 * @see fl_builder_init_ui
			 */
			do_action( 'fl_builder_init_ui' );

			// Remove 3rd party editor buttons.
			remove_all_actions( 'media_buttons', 999999 );
			remove_all_actions( 'media_buttons_context', 999999 );

			// Increase available memory.
			if ( function_exists( 'wp_raise_memory_limit' ) ) {
				wp_raise_memory_limit( 'bb-plugin' );
			}

			// Get the post.
			require_once ABSPATH . 'wp-admin/includes/post.php';
			$post_id = FLBuilderModel::get_post_id();

			// Check to see if the post is locked.
			if ( wp_check_post_lock( $post_id ) !== false ) {
				header( 'Location: ' . admin_url( '/post.php?post=' . $post_id . '&action=edit' ) );
			} else {
				FLBuilderModel::enable_editing();
			}
		}
	}

	/**
	 * Renders the markup for the builder interface.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_ui() {
		global $wp_the_query;

		if ( FLBuilderModel::is_builder_active() ) {

			$post_id         = is_object( $wp_the_query->post ) ? $wp_the_query->post->ID : null;
			$unrestricted    = FLBuilderUserAccess::current_user_can( 'unrestricted_editing' );
			$simple_ui       = ! $unrestricted;
			$global_settings = FLBuilderModel::get_global_settings();

			include FL_BUILDER_DIR . 'includes/ui-extras.php';
			include FL_BUILDER_DIR . 'includes/ui-js-templates.php';
			include FL_BUILDER_DIR . 'includes/ui-js-config.php';
		}
	}

	/**
	 * Get data structure for main builder menu.
	 *
	 * @since 2.0
	 * @return array
	 */
	static function get_main_menu_data() {
		global $post;

		$views             = array();
		$is_lite = true === FL_BUILDER_LITE; // @codingStandardsIgnoreLine
		$is_user_template  = FLBuilderModel::is_post_user_template();
		$enabled_templates = FLBuilderModel::get_enabled_templates();
		$is_simple_ui      = ! FLBuilderUserAccess::current_user_can( 'unrestricted_editing' );
		$key_shortcuts     = self::get_keyboard_shortcuts();
		$help              = FLBuilderModel::get_help_button_settings();
		$default_view      = array(
			'name'       => __( 'Unnamed Menu', 'fl-builder' ),
			'isShowing'  => false,
			'isRootView' => false,
			'items'      => array(),
		);

		// Tools
		$tools_view = array(
			'name'       => __( 'Tools', 'fl-builder' ) . ( $is_lite ? '<button class="fl-builder-upgrade-button fl-builder-button" onclick="FLBuilder._upgradeClicked()">Upgrade</button>' : '' ),
			'isShowing'  => true,
			'isRootView' => true,
			'items'      => array(),
		);

		$tools_view['items'][04] = array(
			'label'     => __( 'Publish Layout', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'publishAndRemain',
			'accessory' => isset( $key_shortcuts['publishAndRemain'] ) ? $key_shortcuts['publishAndRemain']['keyLabel'] : null,
		);

		$tools_view['items'][05] = array(
			'type' => 'separator',
		);

		if ( ! $is_user_template && ( 'enabled' == $enabled_templates || 'user' == $enabled_templates ) ) {
			$tools_view['items'][10] = array(
				'label'     => __( 'Save Template', 'fl-builder' ) . ( $is_lite ? '<span class="fl-builder-pro-badge">PRO</span>' : '' ),
				'type'      => 'event',
				'eventName' => 'saveTemplate',
				'accessory' => isset( $key_shortcuts['saveTemplate'] ) ? $key_shortcuts['saveTemplate']['keyLabel'] : null,
			);
		}

		$tools_view['items'][20] = array(
			'label'     => __( 'Duplicate Layout', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'duplicateLayout',
		);

		$tools_view['items'][30] = array(
			'label'     => __( 'Preview Layout', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'previewLayout',
			'accessory' => isset( $key_shortcuts['previewLayout'] ) ? $key_shortcuts['previewLayout']['keyLabel'] : null,
		);

		$tools_view['items'][31] = array(
			'label'     => __( 'Responsive Editing', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'responsiveEditing',
			'accessory' => isset( $key_shortcuts['responsiveEditing'] ) ? $key_shortcuts['responsiveEditing']['keyLabel'] : null,
		);

		$tools_view['items'][40] = array(
			'type' => 'separator',
		);

		$tools_view['items'][50] = array(
			'label'     => __( 'Layout CSS & Javascript', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'showLayoutSettings',
			'accessory' => isset( $key_shortcuts['showLayoutSettings'] ) ? $key_shortcuts['showLayoutSettings']['keyLabel'] : null,
		);

		$tools_view['items'][60] = array(
			'label'     => __( 'Global Settings', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'showGlobalSettings',
			'accessory' => isset( $key_shortcuts['showGlobalSettings'] ) ? $key_shortcuts['showGlobalSettings']['keyLabel'] : null,
		);

		if ( $is_lite || defined( 'FL_THEME_BUILDER_VERSION' ) ) {
			$tools_view['items'][65] = array(
				'label'     => __( 'Themer Layouts', 'fl-builder' ) . ( $is_lite ? '<span class="fl-builder-pro-badge">PRO</span>' : '' ),
				'type'      => 'event',
				'eventName' => 'launchThemerLayouts',
			);
		}

		$tools_view['items'][70] = array(
			'type' => 'separator',
		);

		$tools_view['items'][80] = array(
			'label'     => __( 'Change UI Brightness', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'toggleUISkin',
		);

		$tools_view['items'][100] = array(
			'label' => __( 'WordPress Admin', 'fl-builder' ),
			'type'  => 'view',
			'view'  => 'admin',
		);

		if ( $help['enabled'] && ! $is_simple_ui ) {
			$tools_view['items'][110] = array(
				'label' => __( 'Help', 'fl-builder' ),
				'type'  => 'view',
				'view'  => 'help',
			);
		}

		$tools_view['items'][120] = array(
			'label'     => __( 'Keyboard Shortcuts', 'fl-builder' ),
			'type'      => 'event',
			'eventName' => 'showKeyboardShortcuts',
		);

		$views['main'] = wp_parse_args( $tools_view, $default_view );

		// Admin
		$admin_view = array(
			'name'  => __( 'WordPress Admin', 'fl-builder' ),
			'items' => array(),
		);

		// Edit current post/page/cpt
		if ( is_single( $post->ID ) || is_page( $post->ID ) ) {
			$edit_label              = get_post_type_object( $post->post_type )->labels->edit_item;
			$admin_view['items'][10] = array(
				'label' => $edit_label,
				'type'  => 'link',
				'url'   => get_edit_post_link( $post->ID ),
			);
		}

		$admin_view['items'][15] = array(
			'type' => 'separator',
		);

		// Dashboard
		$admin_view['items'][17] = array(
			'label' => _x( 'Dashboard', 'label for the WordPress Dashboard link', 'fl-builder' ),
			'type'  => 'link',
			'url'   => admin_url( 'index.php' ),
		);

		$templates_enabled = FLBuilderUserAccess::current_user_can( 'builder_admin' );

		if ( $templates_enabled ) {
			$admin_view['items'][20] = array(
				'label' => __( 'Manage Templates', 'fl-builder' ),
				'type'  => 'link',
				'url'   => admin_url( 'edit.php?post_type=fl-builder-template' ),
			);
		}

		if ( current_user_can( 'customize' ) ) {
			$post_url = get_permalink( $post->ID );
			if ( $post_url ) {
				$url = admin_url( 'customize.php?url=' . $post_url );
			} else {
				$url = admin_url( 'customize.php' );
			}
			$admin_view['items'][30] = array(
				'label' => __( 'Customize Theme', 'fl-builder' ),
				'type'  => 'link',
				'url'   => $url,
			);
		}

		$views['admin'] = wp_parse_args( $admin_view, $default_view );

		// Help
		if ( $help['enabled'] && ! $is_simple_ui ) {
			$help_view = array(
				'name'  => __( 'Help', 'fl-builder' ),
				'items' => array(),
			);

			if ( $help['video'] && isset( $help['video_embed'] ) ) {
				// Disable Auto Play
				$help['video_embed'] = str_replace( 'autoplay=1', 'autoplay=0', $help['video_embed'] );

				// Remove Height from iframe
				$help['video_embed'] = str_replace( 'height="315"', 'height="173"', $help['video_embed'] );

				$help_view['items'][10] = array(
					'type'  => 'video',
					'embed' => $help['video_embed'],
				);
			}

			if ( $help['tour'] ) {
				$help_view['items'][20] = array(
					'label'     => __( 'Take A Tour', 'fl-builder' ),
					'type'      => 'event',
					'eventName' => 'beginTour',
				);
			}

			if ( $help['knowledge_base'] && isset( $help['knowledge_base_url'] ) ) {
				$help_view['items'][30] = array(
					'label' => __( 'View Knowledge Base', 'fl-builder' ),
					'type'  => 'link',
					'url'   => $help['knowledge_base_url'],
				);
			}

			if ( $help['forums'] && isset( $help['forums_url'] ) ) {
				$help_view['items'][40] = array(
					'label' => __( 'Contact Support', 'fl-builder' ),
					'type'  => 'link',
					'url'   => $help['forums_url'],
				);
			}

			$views['help'] = wp_parse_args( $help_view, $default_view );
		}

		return apply_filters( 'fl_builder_main_menu', $views );
	}

	/**
	 * Get array of registered keyboard shortcuts. The key corresponds to
	 * an event to be triggered by FLbuilder.triggerHook()
	 *
	 * @since 2.0
	 * @return array
	 */
	static function get_keyboard_shortcuts() {
		$default_action = array(
			'label'    => _x( 'Untitled Shortcut', 'A keyboard shortcut with no label given', 'fl-builder' ),
			'keyCode'  => '',
			'keyLabel' => '',
			'isGlobal' => false,
			'enabled'  => true,
		);
		$data           = array(
			'showModules'        => array(
				'label'   => _x( 'Open Modules Tab', 'Keyboard action to show modules tab', 'fl-builder' ),
				'keyCode' => 'j',
			),
			'showRows'           => array(
				'label'   => _x( 'Open Rows Tab', 'Keyboard action to show rows tab', 'fl-builder' ),
				'keyCode' => 'k',
			),
			'showTemplates'      => array(
				'label'   => _x( 'Open Templates Tab', 'Keyboard action to show templates tab', 'fl-builder' ),
				'keyCode' => 'l',
			),
			'showSaved'          => array(
				'label'   => _x( 'Open Saved Tab', 'Keyboard action to show saved tab', 'fl-builder' ),
				'keyCode' => ';',
				'enabled' => true !== FL_BUILDER_LITE,
			),
			'saveTemplate'       => array(
				'label'   => _x( 'Save New Template', 'Keyboard action to open save template form', 'fl-builder' ),
				'keyCode' => 'mod+j',
				'enabled' => true !== FL_BUILDER_LITE,
			),
			'previewLayout'      => array(
				'label'   => _x( 'Toggle Preview Mode', 'Keyboard action to toggle preview mode', 'fl-builder' ),
				'keyCode' => 'p',
			),
			'responsiveEditing'  => array(
				'label'   => _x( 'Toggle Responsive Editing Mode', 'Keyboard action to toggle responsive editing', 'fl-builder' ),
				'keyCode' => 'r',
			),
			'showGlobalSettings' => array(
				'label'   => _x( 'Open Global Settings', 'Keyboard action to open the global settings panel', 'fl-builder' ),
				'keyCode' => 'mod+u',
			),
			'showLayoutSettings' => array(
				'label'   => _x( 'Open Layout Settings', 'Keyboard action to open the layout settings panel', 'fl-builder' ),
				'keyCode' => 'mod+y',
			),
			'showSearch'         => array(
				'label'   => _x( 'Display Module Search', 'Keyboard action to open the module search panel', 'fl-builder' ),
				'keyCode' => 'mod+i',
				'enabled' => true !== FL_BUILDER_LITE,
			),
			'showSavedMessage'   => array(
				'label'    => _x( 'Save Layout', 'Keyboard action to save changes', 'fl-builder' ),
				'keyCode'  => 'mod+s',
				'isGlobal' => true,
			),
			'publishAndRemain'   => array(
				'label'    => _x( 'Publish changes without leaving builder', 'Keyboard action to publish any pending changes', 'fl-builder' ),
				'keyCode'  => 'mod+p',
				'isGlobal' => true,
			),
			'cancelTask'         => array(
				'label'    => _x( 'Dismiss Active Panel', 'Keyboard action to dismiss the current task or panel', 'fl-builder' ),
				'keyCode'  => 'esc',
				'isGlobal' => true,
			),
			'undo'               => array(
				'label'   => _x( 'Undo', 'Keyboard action to undo changes', 'fl-builder' ),
				'keyCode' => 'mod+z',
			),
			'redo'               => array(
				'label'   => _x( 'Redo', 'Keyboard action to redo changes', 'fl-builder' ),
				'keyCode' => 'shift+mod+z',
			),
		);

		$data = apply_filters( 'fl_builder_keyboard_shortcuts', $data );

		foreach ( $data as $hook => $args ) {

			// Check for old (alpha) format and normalize
			if ( is_string( $args ) ) {
				$args = array(
					'label'   => ucwords( preg_replace( '/([^A-Z])([A-Z])/', '$1 $2', $hook ) ),
					'keyCode' => $args,
				);
			}

			$args = wp_parse_args( $args, $default_action );

			// Unset this shortcut if it's not enabled.
			if ( ! $args['enabled'] ) {
				unset( $data[ $hook ] );
				continue;
			}

			// Map 'mod' to mac or pc equivalent
			$code = $args['keyCode'];
			$code = str_replace( '+', '', $code );

			if ( false !== strpos( $code, 'mod' ) ) {
				$is_mac = strpos( $_SERVER['HTTP_USER_AGENT'], 'Macintosh' ) ? true : false;

				if ( $is_mac ) {
					$code = str_replace( 'mod', 'command', $code );
				} else {
					$code = str_replace( 'mod', 'Ctrl+', $code );
				}
			}

			// Replace 'command'
			$code = str_replace( 'command', '&#8984;', $code );

			// Replace 'shift'
			$code = str_replace( 'shift', '&#x21E7;', $code );

			// Replace 'delete'
			$code = str_replace( 'delete', '&#x232b;', $code );

			// Replace 'left' arrow
			$code = str_replace( 'left', '&larr;', $code );

			// Replace 'right' arrow
			$code = str_replace( 'right', '&rarr;', $code );

			$args['keyLabel'] = $code;
			$data[ $hook ]    = $args;
		}

		return $data;
	}

	/**
	 * Renders the markup for the title in the builder's bar.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_ui_bar_title() {

		global $post;
		$simple_ui = ! FLBuilderUserAccess::current_user_can( 'unrestricted_editing' );

		$title           = apply_filters( 'fl_builder_ui_bar_title', get_the_title( $post->ID ) );
		$icon_url        = FLBuilderModel::get_branding_icon();
		$wrapper_classes = array( 'fl-builder-bar-title' );

		if ( '' == $icon_url ) {
			$wrapper_classes[] = 'fl-builder-bar-title-no-icon';
		}
		$type = FLBuilderModel::get_user_template_type( $post->ID );
		if ( $type && 'layout' !== $type ) {
			$edited_object_label = ucfirst( $type );
		} else {
			$edited_object_label = get_post_type_object( $post->post_type )->labels->singular_name;
		}

		/* translators: %s: post label */
		$pretitle = sprintf( _x( 'Currently Editing %s', 'Currently editing message', 'fl-builder' ), $edited_object_label );
		$pretitle = apply_filters( 'fl_builder_ui_bar_pretitle', $pretitle );

		// Render the bar title.
		include FL_BUILDER_DIR . 'includes/ui-bar-title-area.php';
	}

	/**
	 * Renders the markup for the buttons in the builder's bar.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_ui_bar_buttons() {
		$help_button           = FLBuilderModel::get_help_button_settings();
		$simple_ui             = ! FLBuilderUserAccess::current_user_can( 'unrestricted_editing' );
		$should_display_search = ! FLBuilderModel::is_post_user_template( 'module' ) && ! $simple_ui;
		$add_btn_svg           = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="24" height="24"><rect x="0" fill="none" width="24" height="24" /><g><path d="M17 9v2h-6v6H9v-6H3V9h6V3h2v6h6z"/></g></svg>';
		$notifications         = FLBuilderNotifications::get_notifications();
		$feedback_label        = __( 'Dev Feedback', 'fl-builder' );
		$show_feedback         = false;

		$show_notifications = ! $simple_ui &&
							! FLBuilderModel::is_white_labeled() &&
							$notifications['data'] &&
							'{}' !== $notifications['data'] &&
							! apply_filters( 'fl_disable_notifications', false );

		if ( strstr( FL_BUILDER_VERSION, '-dev' ) ) {
			$show_feedback = true;
		} elseif ( strstr( FL_BUILDER_VERSION, '-alpha' ) ) {
			$feedback_label = __( 'Alpha Feedback', 'fl-builder' );
			$show_feedback  = true;
		} elseif ( strstr( FL_BUILDER_VERSION, '-beta' ) ) {
			$feedback_label = __( 'Beta Feedback', 'fl-builder' );
			$show_feedback  = true;
		}

		$buttons = apply_filters( 'fl_builder_ui_bar_buttons', array(
			'feedback'      => array(
				'label'   => $feedback_label . ' <i class="fas fa-external-link-alt"></i>',
				'show'    => $show_feedback,
				'onclick' => "window.open('" . FLBuilderModel::get_store_url( 'contact', array(
					'topic'        => 'Pre-Release Feedback',
					'utm_medium'   => true === FL_BUILDER_LITE ? 'bb-lite' : 'bb-pro',
					'utm_source'   => 'builder-ui',
					'utm_campaign' => 'feedback-cta',
				) ) . "');",
			),
			'buy'           => array(
				'label' => __( 'Buy Now', 'fl-builder' ) . ' <i class="fas fa-external-link-alt"></i>',
				'show'  => stristr( home_url(), 'demo.wpbeaverbuilder.com' ),
			),
			'done'          => array(
				'label' => __( 'Done', 'fl-builder' ),
			),
			'content-panel' => array(
				'label' => $add_btn_svg,
				'show'  => ! $simple_ui,
				'class' => 'fl-builder-button-silent',
			),
			'add-content'   => array( // Only added here for backwards compat.
				'label' => $add_btn_svg,
				'show'  => ! $simple_ui,
			),
		) );

		// Check if Assistant is at least v0.6.0
		if ( isset( $buttons['fl-assistant'] ) && defined( 'FL_ASSISTANT_VERSION' ) ) {
			$buttons['fl-assistant']['show'] = version_compare( FL_ASSISTANT_VERSION, '0.6', '>=' );
		}

		echo '<div class="fl-builder-bar-actions">';
		$i = 0;

		foreach ( $buttons as $slug => $button ) {

			if ( 'add-content' == $slug ) {
				continue; // The old add content button is no longer supported.
			}

			if ( isset( $button['show'] ) && ! $button['show'] ) {
				continue;
			}

			// Class
			echo '<button class="fl-builder-' . $slug . '-button fl-builder-button';
			if ( isset( $button['class'] ) ) {
				echo ' ' . $button['class'];
			}
			echo '"';

			// ID
			if ( isset( $button['id'] ) ) {
				echo ' id="' . $button['id'] . '"';
			}

			// Title Attribute
			if ( isset( $button['title'] ) ) {
				echo ' title="' . $button['title'] . '"';
			}

			// onClick Attribute
			if ( isset( $button['onclick'] ) ) {
				echo ' onclick="' . $button['onclick'] . '"';
			}

			// Button Content
			echo '>' . $button['label'] . '</button>';

			$i++;
		}

		echo '<span class="fl-builder--saving-indicator"></span>';

		if ( ! $simple_ui && ! FLBuilderModel::is_white_labeled() && $notifications['data'] && '{}' !== $notifications['data'] && ! apply_filters( 'fl_disable_notifications', false ) ) {
			echo '<span class="fl-builder-bar-spacer"></span>';
			echo '<button id="fl-builder-toggle-notifications" class="fl-builder-button fl-builder-button-silent">';
			include FL_BUILDER_DIR . 'img/svg/bell-active.svg';
			echo '</button>';
		}

		echo '</div>';
	}

	/**
	 * Renders layouts using a new instance of WP_Query with the provided
	 * args and enqueues the necessary styles and scripts. We set the global
	 * $wp_query variable so the builder thinks we are in the loop when content
	 * is rendered without having to call query_posts.
	 *
	 * @link https://codex.wordpress.org/Class_Reference/WP_Query See for a complete list of args.
	 *
	 * @since 1.7
	 * @param array|string $args An array or string of args to be passed to a new instance of WP_Query.
	 * @param int $site_id The ID of a site on a network to pull the query from.
	 * @return void
	 */
	static public function render_query( $args, $site_id = null ) {
		global $post;
		$switched = false;

		// Pull from a site on the network?
		if ( $site_id && is_multisite() ) {
			switch_to_blog( $site_id );
			$switched = true;
		}

		// Get the query.
		$query = new WP_Query( $args );

		// Loop through the posts.
		foreach ( $query->posts as $query_post ) {

			// Make sure this isn't the same post as the original post to prevent infinite loops.
			if ( is_object( $post ) && $post->ID === $query_post->ID && ! $switched ) {
				continue;
			}

			if ( FLBuilderModel::is_builder_enabled( $query_post->ID ) ) {

				// Enqueue styles and scripts for this post.
				self::enqueue_layout_styles_scripts_by_id( $query_post->ID );

				// Print the styles if we are outside of the head tag.
				if ( did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' ) ) {
					wp_print_styles();
				}

				// Render the builder content.
				FLBuilder::render_content_by_id( $query_post->ID );

			} else {

				// Render the WP editor content if the builder isn't enabled.
				echo apply_filters( 'the_content', $query_post->post_content );
			}
		}

		// Reset the site data?
		if ( $site_id && is_multisite() ) {
			restore_current_blog();
		}
	}

	/**
	 * Renders the layout for a post with the given post ID.
	 * This is useful for rendering builder content outside
	 * of the loop.
	 *
	 * @since 1.10
	 * @param int $post_id The ID of the post with the layout to render.
	 * @param string $tag The HTML tag for the content wrapper.
	 * @param array $attrs An array of key/value attribute data for the content wrapper.
	 * @return void
	 */
	static public function render_content_by_id( $post_id, $tag = 'div', $attrs = array() ) {
		// Force the builder to use this post ID.
		FLBuilderModel::set_post_id( $post_id );

		$layout_type = empty( $attrs['data-type'] ) ? '' : $attrs['data-type'];

		// Build the attributes string.
		$attr_string = '';
		/**
		 * Change attributes for container.
		 * @see fl_render_content_by_id_attrs
		 */
		$attrs = apply_filters( 'fl_render_content_by_id_attrs', $attrs, $post_id );

		foreach ( $attrs as $attr_key => $attr_value ) {
			$attr_string .= ' ' . $attr_key . '="' . $attr_value . '"';
		}

		// Prevent the builder's render_content filter from running.
		add_filter( 'fl_builder_do_render_content', '__return_false' );

		/**
		 * Fire the render content start action.
		 * @see fl_builder_render_content_start
		 */
		do_action( 'fl_builder_render_content_start' );

		// Render the content.
		ob_start();
		/**
		 * Before render content
		 * @see fl_builder_before_render_content
		 */
		do_action( 'fl_builder_before_render_content' );
		echo '<' . $tag . ' class="' . self::render_content_classes( $layout_type ) . '" data-post-id="' . $post_id . '"' . $attr_string . '>';
		self::render_nodes();
		echo '</' . $tag . '>';
		/**
		 * After render content
		 * @see fl_builder_after_render_content
		 */
		do_action( 'fl_builder_after_render_content' );
		$content = ob_get_clean();

		// Allow the builder's render_content filter to run again.
		remove_filter( 'fl_builder_do_render_content', '__return_false' );

		// Process shortcodes.
		if ( apply_filters( 'fl_builder_render_shortcodes', true ) ) {
			global $wp_embed;
			$content = apply_filters( 'fl_builder_before_render_shortcodes', $content );
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", 'FLBuilder::double_escape_shortcodes', $content );
			$content = $wp_embed->run_shortcode( $content );
			$content = do_shortcode( $content );
			/**
			 * Allow content to be filtered after shortcodes are processed.
			 * @see fl_builder_after_render_shortcodes
			 * @since 2.2.4
			 */
			$content = apply_filters( 'fl_builder_after_render_shortcodes', $content );
		}

		// Add srcset attrs to images with the class wp-image-<ID>.
		if ( function_exists( 'wp_filter_content_tags' ) ) {
			$content = wp_filter_content_tags( $content );
		} elseif ( function_exists( 'wp_make_content_images_responsive' ) ) {
			$content = wp_make_content_images_responsive( $content );
		}

		/**
		 * Fire the render content complete action.
		 * @see fl_builder_render_content_complete
		 */
		do_action( 'fl_builder_render_content_complete' );

		// Stop forcing the builder to use this post ID.
		FLBuilderModel::reset_post_id();

		echo $content;
	}

	/**
	 * Renders the content for a builder layout while in the loop.
	 * This method should only be called by the_content filter as
	 * defined in this class. To output builder content, use
	 * the_content function while in a WordPress loop or use
	 * the FLBuilder::render_content_by_id method.
	 *
	 * @since 1.0
	 * @param string $content The existing content.
	 * @return string
	 */
	static public function render_content( $content ) {
		$post_id   = FLBuilderModel::get_post_id( true );
		$enabled   = FLBuilderModel::is_builder_enabled( $post_id );
		$rendering = $post_id === self::$post_rendering;
		$do_render = apply_filters( 'fl_builder_do_render_content', true, $post_id );
		$in_loop   = in_the_loop();
		$is_global = in_array( $post_id, FLBuilderModel::get_global_posts() );

		if ( $enabled && ! $rendering && $do_render && ( $in_loop || $is_global ) ) {
			// Set the post rendering ID.
			self::$post_rendering = $post_id;

			// Try to enqueue here in case it didn't happen in the head for this layout.
			self::enqueue_layout_styles_scripts();

			// Render the content.
			ob_start();
			self::render_content_by_id( $post_id );
			$content = ob_get_clean();

			// Clear the post rendering ID.
			self::$post_rendering = null;

			do_action( 'fl_did_render_content_filter' );
		}

		return $content;
	}

	/**
	 * Escaped shortcodes need to be double escaped or they will
	 * be parsed by WP's shortcodes filter.
	 *
	 * @since 1.6.4.1
	 * @param array $matches The existing content.
	 * @return string
	 */
	static public function double_escape_shortcodes( $matches ) {
		if ( '[' == $matches[1] && ']' == $matches[6] ) {
			return '[' . $matches[0] . ']';
		}

		return $matches[0];
	}

	/**
	 * Renders the CSS classes for the main content div tag.
	 *
	 * @since 1.6.4
	 * @param string $layout_type
	 * @return string
	 */
	static public function render_content_classes( $layout_type = '' ) {
		global $wp_the_query;

		$post_id = FLBuilderModel::get_post_id();

		// Build the content class.
		$classes = 'fl-builder-content fl-builder-content-' . $post_id;

		// Add the primary content class.
		if ( isset( $wp_the_query->post ) && $wp_the_query->post->ID == $post_id ) {
			$classes .= ' fl-builder-content-primary';
		}

		// Add browser specific classes.
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			if ( stristr( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0' ) && stristr( $_SERVER['HTTP_USER_AGENT'], 'rv:11.0' ) ) {
				$classes .= ' fl-builder-ie-11';
			}
		}

		return apply_filters( 'fl_builder_content_classes', $classes, $layout_type );
	}

	/**
	 * Renders the markup for all nodes in a layout.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_nodes() {
		/**
		 * Before render nodes.
		 * @see fl_builder_before_render_nodes
		 */
		do_action( 'fl_builder_before_render_nodes' );

		if ( apply_filters( 'fl_builder_render_nodes', true ) ) {
			self::render_rows();
		}

		/**
		 * After render nodes.
		 * @see fl_builder_after_render_nodes
		 */
		do_action( 'fl_builder_after_render_nodes' );
	}

	/**
	 * Renders the markup for a node's attributes.
	 *
	 * @since 1.8
	 * @param array $attrs
	 * @return void
	 */
	static public function render_node_attributes( $attrs ) {
		foreach ( $attrs as $attr_key => $attr_value ) {

			if ( empty( $attr_value ) ) {
				continue;
			} elseif ( is_string( $attr_value ) ) {
				echo ' ' . $attr_key . '="' . $attr_value . '"';
			} elseif ( is_array( $attr_value ) && ! empty( $attr_value ) ) {

				echo ' ' . $attr_key . '="';

				for ( $i = 0; $i < count( $attr_value ); $i++ ) {

					echo $attr_value[ $i ];

					if ( $i < count( $attr_value ) - 1 ) {
						echo ' ';
					}
				}

				echo '"';
			}
		}
	}

	/**
	 * Renders the stripped down content for a layout
	 * that is saved to the WordPress editor.
	 *
	 * @since 1.0
	 * @param string $content The existing content.
	 * @return string
	 */
	static public function render_editor_content() {
		$rows = FLBuilderModel::get_nodes( 'row' );

		ob_start();

		// Render the modules.
		foreach ( $rows as $row ) {

			if ( FLBuilderModel::node_has_visibility_rules( $row ) ) {
				continue;
			}

			$groups = FLBuilderModel::get_nodes( 'column-group', $row );

			foreach ( $groups as $group ) {

				$cols = FLBuilderModel::get_nodes( 'column', $group );

				foreach ( $cols as $col ) {

					if ( FLBuilderModel::node_has_visibility_rules( $col ) ) {
						continue;
					}

					$col_children = FLBuilderModel::get_nodes( null, $col );

					foreach ( $col_children as $col_child ) {

						if ( 'module' == $col_child->type ) {

							$module = FLBuilderModel::get_module( $col_child );

							if ( FLBuilderModel::node_has_visibility_rules( $module ) ) {
								continue;
							}

							if ( $module && $module->editor_export ) {

								// Don't crop photos to ensure media library photos are rendered.
								if ( 'photo' == $module->settings->type ) {
									$module->settings->crop = false;
								}

								FLBuilder::render_module_html( $module->settings->type, $module->settings, $module );
							}
						} elseif ( 'column-group' == $col_child->type ) {

							$group_cols = FLBuilderModel::get_nodes( 'column', $col_child );

							foreach ( $group_cols as $group_col ) {

								$modules = FLBuilderModel::get_modules( $group_col );

								foreach ( $modules as $module ) {

									if ( FLBuilderModel::node_has_visibility_rules( $module ) ) {
										continue;
									}

									if ( $module->editor_export ) {

										// Don't crop photos to ensure media library photos are rendered.
										if ( 'photo' == $module->settings->type ) {
											$module->settings->crop = false;
										}

										FLBuilder::render_module_html( $module->settings->type, $module->settings, $module );
									}
								}
							}
						}
					}
				}
			}
		}

		// Get the content.
		$content = ob_get_clean();

		// Remove unnecessary tags and attributes.
		$content = preg_replace( '/<\/?div[^>]*\>/i', '', $content );
		$content = preg_replace( '/<\/?span[^>]*\>/i', '', $content );
		$content = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );
		$content = preg_replace( '/<\/?noscript[^>]*\>/i', '', $content );
		$content = preg_replace( '#<svg(.*?)>(.*?)</svg>#is', '', $content );
		$content = preg_replace( '/<i [^>]*><\\/i[^>]*>/', '', $content );
		$content = preg_replace( '/ class=".*?"/', '', $content );
		$content = preg_replace( '/ style=".*?"/', '', $content );

		// Remove empty lines.
		$content = preg_replace( '/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $content );

		return apply_filters( 'fl_builder_editor_content', $content );
	}

	/**
	 * Renders a settings via PHP. This method is only around for
	 * backwards compatibility with third party settings forms that are
	 * still being rendered via AJAX. Going forward, all settings forms
	 * should be rendered on the frontend using FLBuilderSettingsForms.render.
	 *
	 * @since 1.0
	 * @param array $form The form data.
	 * @param object $settings The settings data.
	 * @return array
	 */
	static public function render_settings( $form, $settings ) {
		return FLBuilderUISettingsForms::render_settings( (array) $form, $settings );
	}

	/**
	 * Renders a settings form via PHP. This method is only around for
	 * backwards compatibility with third party settings forms that are
	 * still being rendered via AJAX. Going forward, all settings forms
	 * should be rendered on the frontend using FLBuilderSettingsForms.render.
	 *
	 * @since 1.0
	 * @param string $type The type of form to render.
	 * @param object $settings The settings data.
	 * @return array
	 */
	static public function render_settings_form( $type = null, $settings = null ) {
		return FLBuilderUISettingsForms::render_settings_form( $type, $settings );
	}

	/**
	 * Renders a settings field via PHP. This method is only around for
	 * backwards compatibility with third party settings forms that are
	 * still being rendered via AJAX. Going forward, all settings forms
	 * should be rendered on the frontend using FLBuilderSettingsForms.render.
	 *
	 * @since 1.0
	 * @param string $name The field name.
	 * @param array $field An array of setup data for the field.
	 * @param object $settings Form settings data object.
	 * @return void
	 */
	static public function render_settings_field( $name, $field, $settings = null ) {
		return FLBuilderUISettingsForms::render_settings_field( $name, $field, $settings );
	}

	/**
	 * Renders the markup for the icon selector.
	 *
	 * @since 1.0
	 * @return array
	 */
	static public function render_icon_selector() {
		return FLBuilderUISettingsForms::render_icon_selector();
	}

	/**
	 * Renders the markup for all of the rows in a layout.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_rows() {
		$rows = FLBuilderModel::get_nodes( 'row' );

		/**
		 * Before rendering the markup for all of the rows in a layout.
		 * @see fl_builder_before_render_rows
		 */
		do_action( 'fl_builder_before_render_rows', $rows );

		foreach ( $rows as $row ) {
			self::render_row( $row );
		}

		/**
		 * After rendering the markup for all of the rows in a layout.
		 * @see fl_builder_after_render_rows
		 */
		do_action( 'fl_builder_after_render_rows', $rows );
	}

	/**
	 * Renders the markup for a single row.
	 *
	 * @since 1.0
	 * @param object $row The row to render.
	 * @return void
	 */
	static public function render_row( $row ) {
		global $wp_the_query;

		$groups  = FLBuilderModel::get_nodes( 'column-group', $row );
		$post_id = FLBuilderModel::get_post_id();
		$active  = FLBuilderModel::is_builder_active() && $post_id == $wp_the_query->post->ID;
		$visible = FLBuilderModel::is_node_visible( $row );

		if ( $active || $visible ) {

			/**
			 * Before rendering a row
			 * @see fl_builder_before_render_row
			 */
			do_action( 'fl_builder_before_render_row', $row, $groups );

			$template_file = self::locate_template_file(
				apply_filters( 'fl_builder_row_template_base', 'row', $row ),
				apply_filters( 'fl_builder_row_template_slug', '', $row )
			);

			if ( $template_file ) {
				include $template_file;
			}

			/**
			 * After rendering a row.
			 * @see fl_builder_after_render_row
			 */
			do_action( 'fl_builder_after_render_row', $row, $groups );
		} else {
			/**
			 * Fires in place of a hidden row.
			 * @see fl_builder_hidden_node
			 */
			do_action( 'fl_builder_hidden_node', $row );
		}
	}

	/**
	 * Renders the HTML attributes for a single row.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return void
	 */
	static public function render_row_attributes( $row ) {
		/**
		 * Use this filter to work with the custom class a user adds to a row under Row Settings > Advanced > Class.
		 * @see fl_builder_row_custom_class
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		$custom_class = apply_filters( 'fl_builder_row_custom_class', $row->settings->class, $row );
		$overlay_bgs  = array( 'photo', 'parallax', 'slideshow', 'video' );
		$active       = FLBuilderModel::is_builder_active();
		$visible      = FLBuilderModel::is_node_visible( $row );
		$has_rules    = FLBuilderModel::node_has_visibility_rules( $row );
		$rules        = FLBuilderModel::node_visibility_rules( $row );
		$attrs        = array(
			'id'        => $row->settings->id,
			'class'     => array(
				'fl-row',
				'fl-row-' . $row->settings->width . '-width',
				'fl-row-bg-' . $row->settings->bg_type,
				'fl-node-' . $row->node,
			),
			'data-node' => $row->node,
		);

		// Classes
		if ( ! empty( $row->settings->full_height ) && 'full' == $row->settings->full_height ) {

			$attrs['class'][] = 'fl-row-full-height';

			if ( isset( $row->settings->content_alignment ) ) {
				$attrs['class'][] = 'fl-row-align-' . $row->settings->content_alignment;
			}

			if ( isset( $row->settings->margin_top ) && (int) $row->settings->margin_top < 0 ) {
				$attrs['class'][] = 'fl-row-overlap-top';
			}
		}
		if ( ! empty( $row->settings->full_height ) && 'custom' == $row->settings->full_height ) {

			$attrs['class'][] = 'fl-row-custom-height';

			if ( isset( $row->settings->content_alignment ) ) {
				$attrs['class'][] = 'fl-row-align-' . $row->settings->content_alignment;
			}

			if ( isset( $row->settings->margin_top ) && (int) $row->settings->margin_top < 0 ) {
				$attrs['class'][] = 'fl-row-overlap-top';
			}
		}
		if ( in_array( $row->settings->bg_type, $overlay_bgs ) ) {
			if ( 'color' === $row->settings->bg_overlay_type && ! empty( $row->settings->bg_overlay_color ) ) {
				$attrs['class'][] = 'fl-row-bg-overlay';
			} elseif ( 'gradient' === $row->settings->bg_overlay_type ) {
				$attrs['class'][] = 'fl-row-bg-overlay';
			}
		}
		if ( ! empty( $row->settings->responsive_display ) ) {
			$attrs['class'][] = 'fl-visible-' . $row->settings->responsive_display;
		}
		if ( is_array( $row->settings->animation ) && ! empty( $row->settings->animation['style'] ) ) {
			$attrs['class'][]                = 'fl-animation fl-' . $row->settings->animation['style'];
			$attrs['data-animation-delay'][] = $row->settings->animation['delay'];
			if ( isset( $row->settings->animation['duration'] ) ) {
				$attrs['data-animation-duration'][] = $row->settings->animation['duration'];
			}
		}
		if ( ! empty( $custom_class ) ) {
			$attrs['class'][] = trim( esc_attr( $custom_class ) );
		}
		if ( $active && ! $visible ) {
			$attrs['class'][] = 'fl-node-hidden';
		}
		if ( $active && $has_rules ) {
			$attrs['class'][]         = 'fl-node-has-rules';
			$attrs['data-rules-type'] = $rules['type'];
			$attrs['data-rules-text'] = esc_attr( $rules['text'] );
		}
		if ( ! empty( $row->settings->top_edge_shape ) || ! empty( $row->settings->bottom_edge_shape ) ) {
			$attrs['class'][] = 'fl-row-has-layers';
		}
		if ( ( 'photo' === $row->settings->bg_type ) && ( 'fixed' === $row->settings->bg_attachment ) ) {
			$attrs['class'][] = 'fl-row-bg-fixed';
		}

		// Data
		if ( 'parallax' == $row->settings->bg_type && ! empty( $row->settings->bg_parallax_image_src ) ) {
			$attrs['data-parallax-speed']  = $row->settings->bg_parallax_speed;
			$attrs['data-parallax-image']  = $row->settings->bg_parallax_image_src;
			$attrs['data-parallax-offset'] = $row->settings->bg_parallax_offset;
		}

		// Row Parallax Data
		if ( 'parallax' == $row->settings->bg_type ) {
			$attrs['data-parallax-speed'] = $row->settings->bg_parallax_speed;

			if ( ! empty( $row->settings->bg_parallax_image_src ) ) {
				$attrs['data-parallax-image'] = $row->settings->bg_parallax_image_src;
			}

			if ( ! empty( $row->settings->bg_parallax_image_medium_src ) ) {
				$attrs['data-parallax-image-medium'] = $row->settings->bg_parallax_image_medium_src;
			}

			if ( ! empty( $row->settings->bg_parallax_image_responsive_src ) ) {
				$attrs['data-parallax-image-responsive'] = $row->settings->bg_parallax_image_responsive_src;
			}
		}
		// filter node attrs first to make sure specific node has highest priority
		$attrs = apply_filters( 'fl_builder_node_attributes', $attrs, $row );

		self::render_node_attributes( apply_filters( 'fl_builder_row_attributes', $attrs, $row ) );
	}

	/**
	 * Renders the markup for a row's background.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return void
	 */
	static public function render_row_bg( $row ) {
		/**
		 * Before rendering a row background
		 * @see fl_builder_before_render_row_bg
		 */
		do_action( 'fl_builder_before_render_row_bg', $row );

		if ( 'video' == $row->settings->bg_type ) {

			$vid_data = FLBuilderModel::get_row_bg_data( $row );

			if ( $vid_data || in_array( $row->settings->bg_video_source, array( 'video_url', 'video_service', 'video_embed' ) ) ) {
				$template_file = self::locate_template_file(
					apply_filters( 'fl_builder_row_video_bg_template_base', 'row-video', $row ),
					apply_filters( 'fl_builder_row_video_bg_template_slug', '', $row )
				);

				if ( $template_file ) {
					include $template_file;
				}
			}
		} elseif ( 'embed' == $row->settings->bg_type && ! empty( $row->settings->bg_embed_code ) ) {
			echo '<div class="fl-bg-embed-code">' . $row->settings->bg_embed_code . '</div>';
		} elseif ( 'slideshow' == $row->settings->bg_type ) {
			echo '<div class="fl-bg-slideshow"></div>';
		}

		/**
		 * After rendering a row background
		 * @see fl_builder_after_render_row_bg
		 */
		do_action( 'fl_builder_after_render_row_bg', $row );
	}

	/**
	 * Renders the HTML class for a row's content wrapper.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return void
	 */
	static public function render_row_content_class( $row ) {
		echo 'fl-row-content';
		echo ' fl-row-' . $row->settings->content_width . '-width';
		echo ' fl-node-content';
	}

	/**
	 * Renders the markup for a column group.
	 *
	 * @since 1.0
	 * @param object $group A column group node object.
	 * @return void
	 */
	static public function render_column_group( $group ) {
		$cols = FLBuilderModel::get_nodes( 'column', $group );

		/**
		 * Before rendering a column group
		 * @see fl_builder_before_render_column_group
		 */
		do_action( 'fl_builder_before_render_column_group', $group, $cols );

		$template_file = self::locate_template_file(
			apply_filters( 'fl_builder_column_group_template_base', 'column-group', $group ),
			apply_filters( 'fl_builder_column_group_template_slug', '', $group )
		);

		if ( $template_file ) {
			include $template_file;
		}
		/**
		 * After rendering a column group.
		 * @see fl_builder_after_render_column_group
		 */
		do_action( 'fl_builder_after_render_column_group', $group, $cols );
	}

	/**
	 * Renders the attrs for a column group.
	 *
	 * @since 1.0
	 * @param object $group
	 * @return void
	 */
	static public function render_column_group_attributes( $group ) {
		$cols   = FLBuilderModel::get_nodes( 'column', $group );
		$parent = FLBuilderModel::get_node_parent( $group );
		$attrs  = array(
			'class'     => array(
				'fl-col-group',
				'fl-node-' . $group->node,
			),
			'data-node' => $group->node,
		);

		if ( isset( $parent->type ) && 'column' == $parent->type ) {
			$attrs['class'][] = 'fl-col-group-nested';
		}

		foreach ( $cols as $col ) {

			if ( isset( $col->settings->equal_height ) && 'yes' == $col->settings->equal_height ) {
				if ( ! in_array( 'fl-col-group-equal-height', $attrs['class'] ) ) {
					$attrs['class'][] = 'fl-col-group-equal-height';
				}
				if ( isset( $col->settings->content_alignment ) ) {
					if ( ! in_array( 'fl-col-group-align-' . $col->settings->content_alignment, $attrs['class'] ) ) {
						$attrs['class'][] = 'fl-col-group-align-' . $col->settings->content_alignment;
					}
				}
			}
			if ( isset( $col->settings->size_responsive ) && ! empty( $col->settings->size_responsive ) ) {
				if ( ! in_array( 'fl-col-group-custom-width', $attrs['class'] ) ) {
					$attrs['class'][] = 'fl-col-group-custom-width';
				}
			}
			if ( isset( $col->settings->responsive_order ) && 'reversed' == $col->settings->responsive_order ) {
				if ( ! in_array( 'fl-col-group-responsive-reversed', $attrs['class'] ) ) {
					$attrs['class'][] = 'fl-col-group-responsive-reversed';
				}
			}
		}

		// filter node attrs first to make sure specific node has highest priority
		$attrs = apply_filters( 'fl_builder_node_attributes', $attrs, $group );

		self::render_node_attributes( apply_filters( 'fl_builder_column_group_attributes', $attrs, $group ) );
	}

	/**
	 * Renders the markup for a single column.
	 *
	 * @since 1.7
	 * @param string|object $col_id A column ID or object.
	 * @return void
	 */
	static public function render_column( $col_id = null ) {
		global $wp_the_query;

		$col     = is_object( $col_id ) ? $col_id : FLBuilderModel::get_node( $col_id );
		$post_id = FLBuilderModel::get_post_id();
		$active  = FLBuilderModel::is_builder_active() && $post_id == $wp_the_query->post->ID;
		$visible = FLBuilderModel::is_node_visible( $col );

		if ( $active || $visible ) {
			include FL_BUILDER_DIR . 'includes/column.php';
		} else {
			/**
			 * Fires in place of a hidden column.
			 * @see fl_builder_hidden_node
			 */
			do_action( 'fl_builder_hidden_node', $col );
		}
	}

	/**
	 * Renders the HTML attributes for a single column.
	 *
	 * @since 1.0
	 * @param object $col A column node object.
	 * @return void
	 */
	static public function render_column_attributes( $col ) {
		/**
		 * Use this filter to work with the custom class a user adds to a column under Column Settings > Advanced > Class.
		 * @see fl_builder_column_custom_class
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		$custom_class    = apply_filters( 'fl_builder_column_custom_class', $col->settings->class, $col );
		$overlay_bgs     = array( 'photo' );
		$nested          = FLBuilderModel::get_nodes( 'column-group', $col );
		$active          = FLBuilderModel::is_builder_active();
		$visible         = FLBuilderModel::is_node_visible( $col );
		$has_rules       = FLBuilderModel::node_has_visibility_rules( $col );
		$rules           = FLBuilderModel::node_visibility_rules( $col );
		$attrs           = array(
			'id'        => $col->settings->id,
			'class'     => array(
				'fl-col',
				'fl-node-' . $col->node,
			),
			'data-node' => $col->node,
			'style'     => array(),
		);
		$global_settings = FLBuilderModel::get_global_settings();

		// Classes
		if ( $col->settings->size <= 50 ) {
			$attrs['class'][] = 'fl-col-small';

			if ( $global_settings->responsive_enabled && ! $global_settings->responsive_col_max_width ) {
				$attrs['class'][] = 'fl-col-small-full-width';
			}
		}
		if ( count( $nested ) > 0 ) {
			$attrs['class'][] = 'fl-col-has-cols';
		}
		if ( in_array( $col->settings->bg_type, $overlay_bgs ) ) {
			if ( 'color' === $col->settings->bg_overlay_type && ! empty( $col->settings->bg_overlay_color ) ) {
				$attrs['class'][] = 'fl-col-bg-overlay';
			} elseif ( 'gradient' === $col->settings->bg_overlay_type ) {
				$attrs['class'][] = 'fl-col-bg-overlay';
			}
		}
		if ( ! empty( $col->settings->responsive_display ) ) {
			$attrs['class'][] = 'fl-visible-' . $col->settings->responsive_display;
		}
		if ( is_array( $col->settings->animation ) && ! empty( $col->settings->animation['style'] ) ) {
			$attrs['class'][]                = 'fl-animation fl-' . $col->settings->animation['style'];
			$attrs['data-animation-delay'][] = $col->settings->animation['delay'];
			if ( isset( $col->settings->animation['duration'] ) ) {
				$attrs['data-animation-duration'][] = $col->settings->animation['duration'];
			}
		}
		if ( ! empty( $custom_class ) ) {
			$attrs['class'][] = trim( esc_attr( $custom_class ) );
		}
		if ( $active && ! $visible ) {
			$attrs['class'][] = 'fl-node-hidden';
		}
		if ( $active && $has_rules ) {
			$attrs['class'][]         = 'fl-node-has-rules';
			$attrs['data-rules-type'] = $rules['type'];
			$attrs['data-rules-text'] = esc_attr( $rules['text'] );
		}

		// Style
		if ( $active ) {
			$attrs['style'][] = 'width: ' . $col->settings->size . '%;';
		}

		// filter node attrs first to make sure specific node has highest priority
		$attrs = apply_filters( 'fl_builder_node_attributes', $attrs, $col );

		/**
		 * Column attributes.
		 * @see fl_builder_column_attributes
		 */
		self::render_node_attributes( apply_filters( 'fl_builder_column_attributes', $attrs, $col ) );
	}

	/**
	 * Renders the markup for all modules in a column.
	 *
	 * @since 1.0
	 * @param string|object $col_id A column ID or object.
	 * @return void
	 */
	static public function render_modules( $col_id = null ) {
		$nodes = FLBuilderModel::get_nodes( null, $col_id );

		/**
		 * Before rendering modules in a column
		 * @see fl_builder_before_render_modules
		 */
		do_action( 'fl_builder_before_render_modules', $nodes, $col_id );

		foreach ( $nodes as $node ) {

			if ( 'module' == $node->type && FLBuilderModel::is_module_registered( $node->settings->type ) ) {
				self::render_module( $node );
			} elseif ( 'column-group' == $node->type ) {
				self::render_column_group( $node );
			}
		}
		/**
		 * After rendering modules in a column
		 * @see fl_builder_after_render_modules
		 */
		do_action( 'fl_builder_after_render_modules', $nodes, $col_id );
	}

	/**
	 * Renders the markup for a single module.
	 *
	 * @since 1.7
	 * @param string|object $module_id A module ID or object.
	 * @return void
	 */
	static public function render_module( $module_id = null ) {
		global $wp_the_query;

		$module   = FLBuilderModel::get_module( $module_id );
		$settings = $module->settings;
		$id       = $module->node;
		$post_id  = FLBuilderModel::get_post_id();
		$active   = FLBuilderModel::is_builder_active() && $post_id == $wp_the_query->post->ID;
		$visible  = FLBuilderModel::is_node_visible( $module );

		if ( $active || $visible ) {

			/**
			 * Before single module is rendered via ajax.
			 * @see fl_builder_before_render_module
			 */
			do_action( 'fl_builder_before_render_module', $module );

			$template_file = self::locate_template_file(
				apply_filters( 'fl_builder_module_template_base', 'module', $module ),
				apply_filters( 'fl_builder_module_template_slug', '', $module )
			);

			if ( $template_file ) {
				include $template_file;
			}

			/**
			 * After single module is rendered via ajax.
			 * @see fl_builder_after_render_module
			 */
			do_action( 'fl_builder_after_render_module', $module );
		} else {
			/**
			 * Fires in place of a hidden module.
			 * @see fl_builder_hidden_node
			 */
			do_action( 'fl_builder_hidden_node', $module );
		}
	}

	/**
	 * Renders the markup for a single module. This can be used to render
	 * the markup of a module within another module by passing the type
	 * and settings params and leaving the module param null.
	 *
	 * @since 1.0
	 * @param string $type The type of module.
	 * @param object $settings A module settings object.
	 * @param object $module Optional. An existing module object to use.
	 * @return void
	 */
	static public function render_module_html( $type, $settings, $module = null ) {
		// Settings
		$defaults = FLBuilderModel::get_module_defaults( $type );
		$settings = (object) array_merge( (array) $defaults, (array) $settings );

		// Module
		$class            = get_class( FLBuilderModel::$modules[ $type ] );
		$module           = new $class();
		$module->settings = FLBuilderSettingsCompat::filter_node_settings( 'module', $settings );

		// Shorthand reference to the module's id.
		$id = $module->node;

		/**
		 * Before single module html is rendered.
		 * used by render_module_html()
		 * @see fl_builder_render_module_html_before
		 */
		do_action( 'fl_builder_render_module_html_before', $type, $settings, $module );

		ob_start();

		if ( has_filter( 'fl_builder_module_frontend_custom_' . $module->slug ) ) {
			echo apply_filters( 'fl_builder_module_frontend_custom_' . $module->slug, (array) $module->settings, $module );
		} else {
			include apply_filters( 'fl_builder_render_module_html', $module->dir . 'includes/frontend.php', $type, $settings, $module );
		}

		$content = ob_get_clean();

		echo apply_filters( 'fl_builder_render_module_html_content', $content, $type, $settings, $module );

		/**
		 * Before single module html is rendered.
		 * used by render_module_html()
		 * @see fl_builder_render_module_html_after
		 */
		do_action( 'fl_builder_render_module_html_after', $type, $settings, $module );
	}

	/**
	 * Renders the HTML attributes for a single module.
	 *
	 * @since 1.0
	 * @param object $module A module node object.
	 * @return void
	 */
	static public function render_module_attributes( $module ) {
		/**
		 * Use this filter to work with the custom class a user adds to a module in the Class field on the Advanced tab.
		 * @see fl_builder_module_custom_class
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		$custom_class = apply_filters( 'fl_builder_module_custom_class', $module->settings->class, $module );
		$active       = FLBuilderModel::is_builder_active();
		$visible      = FLBuilderModel::is_node_visible( $module );
		$has_rules    = FLBuilderModel::node_has_visibility_rules( $module );
		$rules        = FLBuilderModel::node_visibility_rules( $module );
		$attrs        = array(
			'id'        => esc_attr( $module->settings->id ),
			'class'     => array(
				'fl-module',
				'fl-module-' . $module->settings->type,
				'fl-node-' . $module->node,
			),
			'data-node' => $module->node,
		);

		// Classes
		if ( ! empty( $module->settings->responsive_display ) ) {
			$attrs['class'][] = 'fl-visible-' . $module->settings->responsive_display;
		}
		if ( is_array( $module->settings->animation ) && ! empty( $module->settings->animation['style'] ) ) {
			$attrs['class'][]                = 'fl-animation fl-' . $module->settings->animation['style'];
			$attrs['data-animation-delay'][] = $module->settings->animation['delay'];
			if ( isset( $module->settings->animation['duration'] ) ) {
				$attrs['data-animation-duration'][] = $module->settings->animation['duration'];
			}
		}
		if ( ! empty( $custom_class ) ) {
			$attrs['class'][] = trim( esc_attr( $custom_class ) );
		}
		if ( $active && ! $visible ) {
			$attrs['class'][] = 'fl-node-hidden';
		}
		if ( $active && $has_rules ) {
			$attrs['class'][]         = 'fl-node-has-rules';
			$attrs['data-rules-type'] = $rules['type'];
			$attrs['data-rules-text'] = esc_attr( $rules['text'] );
		}

		// Data
		if ( $active ) {
			$attrs['data-parent'] = $module->parent;
			$attrs['data-type']   = $module->settings->type;
			$attrs['data-name']   = $module->name;
		}

		// filter node attrs first to make sure specific node has highest priority
		$attrs = apply_filters( 'fl_builder_node_attributes', $attrs, $module );

		/**
		 * Module attributes.
		 * @see fl_builder_module_attributes
		 */
		self::render_node_attributes( apply_filters( 'fl_builder_module_attributes', $attrs, $module ) );
	}

	/**
	 * Renders the CSS for a single module.
	 *
	 * NOTE: This is not used to render CSS for modules in the FLBuilder::render_css
	 * method. Instead it is used to render CSS for one module inside of another.
	 * For example, you can use this along with FLBuilder::render_module_html to
	 * render a button module inside of a callout module. If you need to filter the
	 * CSS for the layout, consider using the fl_builder_render_css filter instead.
	 *
	 * @since 1.0
	 * @param string $type The type of module.
	 * @param object $id A module node ID.
	 * @param object $settings A module settings object.
	 * @return void
	 */
	static public function render_module_css( $type, $id, $settings ) {
		// Settings
		$global_settings = FLBuilderModel::get_global_settings();
		$defaults        = FLBuilderModel::get_module_defaults( $type );
		$settings        = (object) array_merge( (array) $defaults, (array) $settings );
		$settings        = apply_filters( 'fl_builder_render_module_css_settings', $settings, $id, $type );

		/**
		 * Make sure the Class is not NULL before trying to use it, see #513
		 * @since 2.4
		 */
		if ( null === FLBuilderModel::$modules[ $type ] ) {
			printf( "\n/* Critical Error!! Class for %s with ID %s not found. */\n", $type, $id );
			return false;
		}

		// Module
		$class            = get_class( FLBuilderModel::$modules[ $type ] );
		$module           = new $class();
		$module->settings = FLBuilderSettingsCompat::filter_node_settings( 'module', $settings );

		// CSS
		ob_start();
		include $module->dir . 'includes/frontend.css.php';
		FLBuilderCSS::render();
		$css = ob_get_clean();

		echo apply_filters( 'fl_builder_render_module_css', $css, $module, $id );
	}

	/**
	 * Renders the CSS and JS assets.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function render_assets() {
		self::render_css();
		self::render_js();
	}

	/**
	 * Renders custom CSS in a style tag so it can be edited
	 * using the builder interface.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function render_custom_css_for_editing() {

		if ( ! FLBuilderModel::is_builder_active() && ! isset( $_GET['fl_builder_preview'] ) ) {
			return;
		}

		$global_settings = FLBuilderModel::get_global_settings();
		$layout_settings = FLBuilderModel::get_layout_settings();

		echo '<style id="fl-builder-global-css">' . self::maybe_do_shortcode( $global_settings->css ) . '</style>';
		echo '<style id="fl-builder-layout-css">' . self::maybe_do_shortcode( $layout_settings->css ) . '</style>';
	}

	/**
	 * Renders and caches the CSS for a builder layout.
	 *
	 * @since 1.0
	 * @param bool $include_global
	 * @return string
	 */

	static public function render_css( $include_global = true ) {
		global $wp_the_query;

		$active          = FLBuilderModel::is_builder_active();
		$nodes           = FLBuilderModel::get_categorized_nodes();
		$node_status     = FLBuilderModel::get_node_status();
		$global_settings = FLBuilderModel::get_global_settings();
		$asset_info      = FLBuilderModel::get_asset_info();
		$enqueuemethod   = FLBuilderModel::get_asset_enqueue_method();
		$post_id         = FLBuilderModel::get_post_id();
		$post            = get_post( $post_id );
		$css             = '';
		$path            = $include_global ? $asset_info['css'] : $asset_info['css_partial'];

		// Render the global css.
		if ( $include_global ) {
			$css .= self::render_global_css();
		}

		// Loop through rows
		foreach ( $nodes['rows'] as $row ) {

			$row_css = '';
			// Instance row css
			$settings = $row->settings;
			$id       = $row->node;
			ob_start();
			include FL_BUILDER_DIR . 'includes/row-css.php';
			FLBuilderCSS::render();
			$row_css .= ob_get_clean();

			// Instance row margins
			$row_css .= self::render_row_margins( $row );

			// Instance row padding
			$row_css .= self::render_row_padding( $row );

			// Instance row animation
			$row_css .= self::render_node_animation_css( $row->settings );

			/**
			 * Filter each rows CSS in render_css
			 * @since 2.5
			 * @see fl_builder_render_css_row
			 */
			$row_css = apply_filters( 'fl_builder_render_css_row', $row_css, $row, $settings );
			$css    .= $row_css;
		}

		// Loop through the columns.
		foreach ( $nodes['columns'] as $col ) {

			$col_css = '';
			// Instance column css
			$settings = $col->settings;
			$id       = $col->node;
			ob_start();
			include FL_BUILDER_DIR . 'includes/column-css.php';
			FLBuilderCSS::render();
			$col_css .= ob_get_clean();

			// Instance column margins
			$col_css .= self::render_column_margins( $col );

			// Instance column padding
			$col_css .= self::render_column_padding( $col );

			// Instance column animation
			$col_css .= self::render_node_animation_css( $settings );

			/**
			 * Filter each cols CSS in render_css
			 * @since 2.5
			 * @see fl_builder_render_css_col
			 */
			$col_css = apply_filters( 'fl_builder_render_css_col', $col_css, $col, $settings );
			$css    .= $col_css;
		}

		// Loop through the modules.
		foreach ( $nodes['modules'] as $module ) {

			$module_css = '';
			// Global module css
			$file            = $module->dir . 'css/frontend.css';
			$file_responsive = $module->dir . 'css/frontend.responsive.css';

			// Only include global module css that hasn't been included yet.
			if ( ! in_array( $module->settings->type . '-module-css', self::$enqueued_global_assets ) ) {

				// Add to the compiled array so we don't include it again.
				self::$enqueued_global_assets[] = $module->settings->type . '-module-css';

				// Get the standard module css.
				if ( fl_builder_filesystem()->file_exists( $file ) ) {
					$module_css .= fl_builder_filesystem()->file_get_contents( $file );
				}

				// Get the responsive module css.
				if ( $global_settings->responsive_enabled && fl_builder_filesystem()->file_exists( $file_responsive ) ) {
					$module_css .= '@media (max-width: ' . $global_settings->responsive_breakpoint . 'px) { ';
					$module_css .= fl_builder_filesystem()->file_get_contents( $file_responsive );
					$module_css .= ' }';
				}
			}

			// Instance module css
			$file     = $module->dir . 'includes/frontend.css.php';
			$settings = $module->settings;
			$id       = $module->node;

			if ( ! in_array( $id, self::$enqueued_module_css_assets ) && fl_builder_filesystem()->file_exists( $file ) ) {
				self::$enqueued_module_css_assets[] = $id;
				ob_start();
				include $file;
				FLBuilderCSS::render();
				$module_css .= ob_get_clean();
			}

			// Instance module margins
			$module_css .= self::render_module_margins( $module );

			if ( ! isset( $global_settings->auto_spacing ) || $global_settings->auto_spacing ) {
				$module_css .= self::render_responsive_module_margins( $module );
			}

			// Instance module animation
			$module_css .= self::render_node_animation_css( $settings );

			/**
			 * Filter each modules CSS in render_css
			 * @since 2.5
			 * @see fl_builder_render_css_module
			 */
			$module_css = apply_filters( 'fl_builder_render_css_module', $module_css, $module, $settings );

			/**
			 * Dynamic Filter to target modules with slug in render_css
			 * @since 2.5
			 * @see fl_builder_render_css_module_
			 */
			$module_css = apply_filters( "fl_builder_render_css_module_{$module->slug}", $module_css, $module, $settings );
			$css       .= $module_css;
		}

		// Render all animation CSS when the builder is active.
		if ( $active ) {
			$css .= self::render_all_animation_css();
		}

		// Custom Global CSS (included here for proper specificity)
		if ( 'published' == $node_status && $include_global ) {
			$css .= self::js_comment( 'Global CSS', self::maybe_do_shortcode( $global_settings->css ) );
		}

		// Custom Global Nodes CSS
		$css .= self::js_comment( 'Global Nodes CSS', self::maybe_do_shortcode( self::render_global_nodes_custom_code( 'css' ) ) );

		// Custom Layout CSS
		if ( 'published' == $node_status || $post_id !== $wp_the_query->post->ID ) {
			$css .= self::js_comment( 'Layout CSS', self::maybe_do_shortcode( FLBuilderModel::get_layout_settings()->css ) );
		}

		/**
		 * Use this filter to modify the CSS that is compiled and cached for each builder layout.
		 * @see fl_builder_render_css
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		$css = apply_filters( 'fl_builder_render_css', $css, $nodes, $global_settings, $include_global );

		// Minify the CSS.
		if ( ! self::is_debug() ) {
			$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
			$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
		}

		// Save the CSS.
		if ( 'file' === $enqueuemethod ) {
			fl_builder_filesystem()->file_put_contents( $path, $css );
		}

		/**
		 * After CSS is compiled.
		 * @see fl_builder_after_render_css
		 */
		do_action( 'fl_builder_after_render_css' );

		return $css;
	}

	/**
	 * Renders the CSS used for all builder layouts.
	 *
	 * @since 1.8.2
	 * @return string
	 */
	static public function render_global_css() {
		// Get info on the new file.
		$global_settings = FLBuilderModel::get_global_settings();

		// Core layout css
		$css = fl_builder_filesystem()->file_get_contents( FL_BUILDER_DIR . 'css/fl-builder-layout.css' );

		// Core button defaults
		if ( ! defined( 'FL_THEME_VERSION' ) ) {
			$css .= fl_builder_filesystem()->file_get_contents( FL_BUILDER_DIR . 'css/fl-builder-layout-button-defaults.css' );
		}

		// Core layout RTL css
		if ( is_rtl() ) {
			$css .= fl_builder_filesystem()->file_get_contents( FL_BUILDER_DIR . 'css/fl-builder-layout-rtl.css' );
		}

		// Global node css
		foreach ( array(
			array( 'row_margins', '.fl-row-content-wrap { margin: ' ),
			array( 'row_padding', '.fl-row-content-wrap { padding: ' ),
			array( 'row_width', '.fl-row-fixed-width { max-width: ' ),
			array( 'column_margins', '.fl-col-content { margin: ' ),
			array( 'column_padding', '.fl-col-content { padding: ' ),
			array( 'module_margins', '.fl-module-content { margin: ' ),
		) as $data ) {
			if ( '' !== $global_settings->{ $data[0] } ) {
				$value = preg_replace( self::regex( 'css_unit' ), '', strtolower( $global_settings->{ $data[0] } ) );
				$css  .= $data[1] . esc_attr( $value );
				$css  .= ( is_numeric( $value ) ) ? ( $global_settings->{ $data[0] . '_unit' } . '; }' ) : ( '; }' );
			}
		}

		// Responsive layout css
		if ( $global_settings->responsive_enabled ) {

			// Medium devices
			$css .= '@media (max-width: ' . $global_settings->medium_breakpoint . 'px) { ';

			// Core medium layout css
			$css .= fl_builder_filesystem()->file_get_contents( FL_BUILDER_DIR . 'css/fl-builder-layout-medium.css' );

			// Global node medium css
			foreach ( array(
				array( 'row_margins_medium', '.fl-row[data-node] > .fl-row-content-wrap { margin: ' ),
				array( 'row_padding_medium', '.fl-row[data-node] > .fl-row-content-wrap { padding: ' ),
				array( 'column_margins_medium', '.fl-col[data-node] > .fl-col-content { margin: ' ),
				array( 'column_padding_medium', '.fl-col[data-node] > .fl-col-content { padding: ' ),
				array( 'module_margins_medium', '.fl-module[data-node] > .fl-module-content { margin: ' ),
			) as $data ) {
				if ( '' !== $global_settings->{ $data[0] } ) {
					$value = preg_replace( self::regex( 'css_unit' ), '', strtolower( $global_settings->{ $data[0] } ) );
					$css  .= $data[1] . esc_attr( $value );
					$css  .= ( is_numeric( $value ) ) ? ( $global_settings->{ $data[0] . '_unit' } . '; }' ) : ( '; }' );
				}
			}

			$css .= ' }';

			// Responsive devices
			$css .= '@media (max-width: ' . $global_settings->responsive_breakpoint . 'px) { ';

			// Core responsive layout css
			$css .= fl_builder_filesystem()->file_get_contents( FL_BUILDER_DIR . 'css/fl-builder-layout-responsive.css' );

			// Auto spacing
			if ( ! isset( $global_settings->auto_spacing ) || $global_settings->auto_spacing ) {
				$css .= fl_builder_filesystem()->file_get_contents( FL_BUILDER_DIR . 'css/fl-builder-layout-auto-spacing.css' );
			}

			// Global node responsive css
			foreach ( array(
				array( 'row_margins_responsive', '.fl-row[data-node] > .fl-row-content-wrap { margin: ' ),
				array( 'row_padding_responsive', '.fl-row[data-node] > .fl-row-content-wrap { padding: ' ),
				array( 'column_margins_responsive', '.fl-col[data-node] > .fl-col-content { margin: ' ),
				array( 'column_padding_responsive', '.fl-col[data-node] > .fl-col-content { padding: ' ),
				array( 'module_margins_responsive', '.fl-module[data-node] > .fl-module-content { margin: ' ),
			) as $data ) {
				if ( '' !== $global_settings->{ $data[0] } ) {
					$value = preg_replace( self::regex( 'css_unit' ), '', strtolower( $global_settings->{ $data[0] } ) );
					$css  .= $data[1] . esc_attr( $value );
					$css  .= ( is_numeric( $value ) ) ? ( $global_settings->{ $data[0] . '_unit' } . '; }' ) : ( '; }' );
				}
			}

			$css .= ' }';
		}

		// Default page heading
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : false;
		if ( FLBuilderModel::is_builder_enabled( $post_id ) ) {
			if ( ! $global_settings->show_default_heading && ! empty( $global_settings->default_heading_selector ) ) {
				$heading_selector = esc_attr( $global_settings->default_heading_selector );

				// If the value starts with `body` or `.fl-builder` selector, we use custom selectors
				if ( 0 === strpos( $heading_selector, 'body' ) || 0 === strpos( $heading_selector, '.fl-builder' ) ) {
					$css .= $heading_selector;
				} else {
					$css .= '.page ' . $heading_selector . ', .single-fl-builder-template ' . $heading_selector;
				}

				$css .= ' { display:none; }';
			}
		}

		return $css;
	}

	/**
	 * Maybe run do_shortcode on CSS/JS if enabled.
	 * @since 2.3
	 */
	static public function maybe_do_shortcode( $code ) {
		if ( true === apply_filters( 'fl_enable_shortcode_css_js', false ) ) {
			$code = do_shortcode( $code );
		}
		return $code;
	}

	/**
	 * Forcing HTTPS in URLs when `FLBuilderModel::is_ssl()` returns TRUE
	 *
	 * @since 1.7.6
	 * @param string $content A string where the URLs will be modified.
	 * @return string String with SSL ready URLs.
	 */
	static public function rewrite_css_cache_urls( $content ) {
		if ( FLBuilderModel::is_ssl() ) {
			$content = preg_replace( '#http:\/\/(.*\.(?:png|jpe?g|gif|webp|svg))#', 'https://$1', $content );
		}

		return $content;
	}

	/**
	 * Regular expressions.
	 *
	 * @since 1.9
	 * @param string $scope What regular expression to return?
	 * @return string Regular expression.
	 */
	static public function regex( $scope ) {
		$regex = array(
			'css_unit' => '/[^a-z0-9%.\-]/',
		);

		return ( isset( $regex[ $scope ] ) ) ? $regex[ $scope ] : null;
	}

	/**
	 * Renders the CSS spacing and border properties for a node.
	 *
	 * @param object $node A generic node object.
	 * @param string $prop_type One of [ 'padding', 'margin', 'border' ].
	 * @param string $selector_prefix Optional CSS selector prefix for better overrides.
	 * @return string A CSS string.
	 */
	static public function render_node_spacing( $node = null, $prop_type = '', $selector_prefix = '' ) {
		// Exit early if incorrect parameters
		if ( ! is_object( $node ) || empty( $prop_type ) ) {
			return;
		}

		$prop_type = strtolower( $prop_type );

		// Ensure type is valid
		if ( ! in_array( $prop_type, array( 'margin', 'padding', 'border' ), true ) ) {
			return;
		}

		$global_settings  = FLBuilderModel::get_global_settings();
		$settings         = $node->settings;
		$css              = '';
		$selector_prefix .= ' .fl-node-' . $node->node;

		// Determine selector suffix to apply spacing to
		switch ( $node->type ) {
			case 'row':
				$selector_suffix = ' > .fl-row-content-wrap';
				break;
			case 'column':
				$selector_suffix = ' > .fl-col-content';
				break;
			case 'module':
				$selector_suffix = ' > .fl-module-content';
				break;
		}

		// Create rules for each breakpoint
		foreach ( array( 'default', 'medium', 'responsive' ) as $breakpoint ) {
			$breakpoint_css = '';
			$setting_suffix = ( 'default' !== $breakpoint ) ? '_' . $breakpoint : '';

			// Iterate over each direction
			foreach ( array( 'top', 'right', 'bottom', 'left' ) as $dir ) {
				$setting_key = $prop_type . '_' . $dir . $setting_suffix;
				$unit_key    = $prop_type . $setting_suffix . '_unit';
				$unit        = isset( $settings->{ $unit_key } ) ? $settings->{ $unit_key } : 'px';

				if ( ! isset( $settings->{ $setting_key } ) ) {
					continue;
				}

				$prop  = $prop_type . '-' . $dir;
				$value = preg_replace( self::regex( 'css_unit' ), '', strtolower( $settings->{ $setting_key } ) );

				if ( 'border' === $prop_type ) {

					if ( empty( $settings->border_type ) ) {
						continue;
					} else {
						$prop .= '-width';
					}
				}

				if ( '' !== $value ) {
					$breakpoint_css .= "\t";
					$breakpoint_css .= $prop . ':' . esc_attr( $value );
					$breakpoint_css .= ( is_numeric( trim( $value ) ) ) ? ( $unit . ';' ) : ( ';' );
					$breakpoint_css .= "\r\n";
				}
			}

			if ( ! empty( $breakpoint_css ) ) {

				// Build the selector
				if ( 'default' !== $breakpoint ) {
					$selector = $selector_prefix . '.fl-' . str_replace( 'column', 'col', $node->type ) . $selector_suffix;
				} else {
					$selector = $selector_prefix . $selector_suffix;
				}

				// Wrap css in selector
				$breakpoint_css = $selector . ' {' . "\r\n" . $breakpoint_css . '}' . "\r\n";

				// Wrap css in media query
				if ( 'default' !== $breakpoint ) {
					$breakpoint_css = '@media ( max-width: ' . $global_settings->{ $breakpoint . '_breakpoint' } . 'px ) {' . "\r\n" . $breakpoint_css . '}' . "\r\n";
				}

				$css .= $breakpoint_css;
			}
		}

		return $css;
	}

	/**
	 * Renders the CSS margins for a row.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return string The row CSS margins string.
	 */
	static public function render_row_margins( $row ) {
		return self::render_node_spacing( $row, 'margin' );
	}

	/**
	 * Renders the CSS padding for a row.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return string The row CSS padding string.
	 */
	static public function render_row_padding( $row ) {
		return self::render_node_spacing( $row, 'padding' );
	}

	/**
	 * Renders the CSS margins for a column.
	 *
	 * @since 1.0
	 * @param object $col A column node object.
	 * @return string The column CSS margins string.
	 */
	static public function render_column_margins( $col ) {
		return self::render_node_spacing( $col, 'margin' );
	}

	/**
	 * Renders the CSS padding for a column.
	 *
	 * @since 1.0
	 * @param object $col A column node object.
	 * @return string The column CSS padding string.
	 */
	static public function render_column_padding( $col ) {
		return self::render_node_spacing( $col, 'padding' );
	}

	/**
	 * Renders the CSS margins for a module.
	 *
	 * @since 1.0
	 * @param object $module A module node object.
	 * @return string The module CSS margins string.
	 */
	static public function render_module_margins( $module ) {
		return self::render_node_spacing( $module, 'margin' );
	}

	/**
	 * Renders the (auto) responsive CSS margins for a module.
	 *
	 * @since 1.0
	 * @param object $module A module node object.
	 * @return string The module CSS margins string.
	 */
	static public function render_responsive_module_margins( $module ) {
		$global_settings = FLBuilderModel::get_global_settings();
		$settings        = $module->settings;
		$margins         = '';
		$css             = '';

		// Bail early if we have global responsive margins.
		if ( '' != $global_settings->module_margins_responsive ) {
			return $css;
		}

		// Get the global default margin value to use.
		if ( '' != $global_settings->module_margins_medium ) {
			$default = trim( $global_settings->module_margins_medium );
		} else {
			$default = trim( $global_settings->module_margins );
		}

		// Set the responsive margin CSS if necessary.
		foreach ( array( 'top', 'bottom', 'left', 'right' ) as $dimension ) {

			$responsive = 'margin_' . $dimension . '_responsive';
			$medium     = 'margin_' . $dimension . '_responsive';
			$desktop    = 'margin_' . $dimension;

			if ( '' == $settings->$responsive ) {

				$value = '' == $settings->$medium ? $settings->$desktop : $settings->$medium;

				if ( '' != $value && ( $value > $default || $value < 0 ) ) {
					$margins .= 'margin-' . $dimension . ':' . esc_attr( $default ) . 'px;';
				}
			}
		}

		// Set the media query if we have margins.
		if ( '' !== $margins ) {
			$css .= '@media (max-width: ' . esc_attr( $global_settings->responsive_breakpoint ) . 'px) { ';
			$css .= '.fl-node-' . $module->node . ' > .fl-module-content { ' . $margins . ' }';
			$css .= ' }';
		}

		return $css;
	}

	/**
	 * Renders the animation CSS for a node if it has an animation.
	 *
	 * @since 2.2
	 * @param object $settings A node settings object.
	 * @return string A CSS string.
	 */
	static public function render_node_animation_css( $settings ) {
		$css = '';

		if ( ! is_array( $settings->animation ) || empty( $settings->animation ) ) {
			return $css;
		} elseif ( in_array( 'animation-' . $settings->animation['style'], self::$enqueued_global_assets ) ) {
			return $css;
		}

		self::$enqueued_global_assets[] = 'animation-' . $settings->animation['style'];
		$path                           = FL_BUILDER_DIR . 'css/animations/' . $settings->animation['style'] . '.css';

		if ( file_exists( $path ) ) {
			$css = file_get_contents( $path );
		}

		return $css;
	}

	/**
	 * Renders all animation CSS for use in the builder UI.
	 *
	 * @since 2.2
	 * @return string A CSS string.
	 */
	static public function render_all_animation_css() {
		$css        = '';
		$animations = glob( FL_BUILDER_DIR . 'css/animations/*.css' );

		if ( ! is_array( $animations ) ) {
			return $css;
		}

		foreach ( $animations as $path ) {
			$key = basename( $path, '.css' );

			if ( in_array( 'animation-' . $key, self::$enqueued_global_assets ) ) {
				continue;
			}

			self::$enqueued_global_assets[] = 'animation-' . $key;
			$css                           .= file_get_contents( $path );
		}

		return $css;
	}

	/**
	 * Renders and caches the JavaScript for a builder layout.
	 *
	 * @since 1.0
	 * @param bool $include_global
	 * @return string
	 */
	static public function render_js( $include_global = true ) {
		// Get info on the new file.
		$nodes           = FLBuilderModel::get_categorized_nodes();
		$global_settings = FLBuilderModel::get_global_settings();
		$layout_settings = FLBuilderModel::get_layout_settings();
		$rows            = FLBuilderModel::get_nodes( 'row' );
		$asset_info      = FLBuilderModel::get_asset_info();
		$enqueuemethod   = FLBuilderModel::get_asset_enqueue_method();
		$js              = '';
		$path            = $include_global ? $asset_info['js'] : $asset_info['js_partial'];

		// Render the global js.
		if ( $include_global ) {
			$js .= self::render_global_js();
		}

		// Loop through the rows.
		foreach ( $nodes['rows'] as $row ) {
			$js .= self::render_row_js( $row );
		}

		// Loop through the modules.
		foreach ( $nodes['modules'] as $module ) {
			$js .= self::render_module_js( $module );
		}

		// Add the layout settings JS.
		if ( ! isset( $_GET['safemode'] ) ) {
			$js .= self::js_comment( 'Global Node Custom JS', self::maybe_do_shortcode( self::render_global_nodes_custom_code( 'js' ) ) );
			$js .= ( is_array( $layout_settings->js ) || is_object( $layout_settings->js ) ) ? self::js_comment( 'Layout Custom JS', self::maybe_do_shortcode( json_encode( $layout_settings->js ) ) ) : self::js_comment( 'Layout Custom JS', self::maybe_do_shortcode( $layout_settings->js ) );
		}

		// Call the FLBuilder._renderLayoutComplete method if we're currently editing.
		if ( stristr( $asset_info['js'], '-draft.js' ) || stristr( $asset_info['js'], '-preview.js' ) ) {
			$js .= "; if(typeof FLBuilder !== 'undefined' && typeof FLBuilder._renderLayoutComplete !== 'undefined') FLBuilder._renderLayoutComplete();";
		}

		// Include FLJSMin
		if ( ! class_exists( 'FLJSMin' ) ) {
			include FL_BUILDER_DIR . 'classes/class-fl-jsmin.php';
		}

		/**
		 * Use this filter to modify the JavaScript that is compiled and cached for each builder layout.
		 * @see fl_builder_render_js
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		$js = apply_filters( 'fl_builder_render_js', $js, $nodes, $global_settings, $include_global );

		// Only proceed if we have JS.
		if ( ! empty( $js ) ) {

			// Minify the JS.
			if ( ! self::is_debug() ) {
				try {
					$min = FLJSMin::minify( $js );
				} catch ( Exception $e ) {
				}

				if ( isset( $min ) ) {
					$js = $min;
				}
			}

			// Save the JS.
			if ( 'file' === $enqueuemethod ) {
				fl_builder_filesystem()->file_put_contents( $path, $js );
			}

			/**
			 * After JS is compiled.
			 * @see fl_builder_after_render_js
			 */
			do_action( 'fl_builder_after_render_js' );
		}

		return $js;
	}

	/**
	 * Renders the JS used for all builder layouts.
	 *
	 * @since 1.8.2
	 * @return string
	 */
	static public function render_global_js() {
		$global_settings = FLBuilderModel::get_global_settings();
		$js              = '';

		// Add the path legacy vars (FLBuilderLayoutConfig.paths should be used instead).
		$js .= "var wpAjaxUrl = '" . admin_url( 'admin-ajax.php' ) . "';";
		$js .= "var flBuilderUrl = '" . FL_BUILDER_URL . "';";

		// Layout config object.
		ob_start();
		include FL_BUILDER_DIR . 'includes/layout-js-config.php';
		$js .= ob_get_clean();

		// Core layout JS.
		$js .= fl_builder_filesystem()->file_get_contents( FL_BUILDER_DIR . 'js/fl-builder-layout.js' );

		// Add the global settings JS.
		if ( ! isset( $_GET['safemode'] ) ) {
			$js .= self::js_comment( 'Global JS', self::maybe_do_shortcode( $global_settings->js ) );
		}
		return $js;
	}

	static public function js_comment( $comment, $js ) {

		$js = sprintf( "\n/* Start %s */\n%s\n/* End %s */\n\n", $comment, $js, $comment );
		return $js;
	}

	/**
	 * Renders the JavaScript for a single row.
	 *
	 * @since 1.7
	 * @param string|object $row_id A row ID or object.
	 * @return string
	 */
	static public function render_row_js( $row_id ) {
		$row      = is_object( $row_id ) ? $row_id : FLBuilderModel::get_node( $row_id );
		$settings = $row->settings;
		$id       = $row->node;

		ob_start();
		include FL_BUILDER_DIR . 'includes/row-js.php';
		return ob_get_clean();
	}

	/**
	 * Renders the JavaScript for all modules in a single row.
	 *
	 * @since 1.7
	 * @param string|object $row_id A row ID or object.
	 * @return string
	 */
	static public function render_row_modules_js( $row_id ) {
		$row              = is_object( $row_id ) ? $row_id : FLBuilderModel::get_node( $row_id );
		$nodes            = FLBuilderModel::get_categorized_nodes();
		$template_post_id = FLBuilderModel::is_node_global( $row );
		$js               = '';

		// Render the JS.
		foreach ( $nodes['groups'] as $group ) {
			if ( $row->node == $group->parent || ( $template_post_id && $row->template_node_id == $group->parent ) ) {
				foreach ( $nodes['columns'] as $column ) {
					if ( $group->node == $column->parent ) {
						foreach ( $nodes['modules'] as $module ) {
							if ( $column->node == $module->parent ) {
								$js .= self::render_module_js( $module );
							}
						}
					}
				}
			}
		}

		// Return the JS.
		return $js;
	}

	/**
	 * Renders the JavaScript for all modules in a single column group.
	 *
	 * @since 2.2
	 * @param string|object $group_id A row ID or object.
	 * @return string
	 */
	static public function render_column_group_modules_js( $group_id ) {
		$group = is_object( $group_id ) ? $group_id : FLBuilderModel::get_node( $group_id );
		$nodes = FLBuilderModel::get_categorized_nodes();
		$js    = '';

		// Render the JS.
		foreach ( $nodes['columns'] as $column ) {
			if ( $group->node == $column->parent ) {
				foreach ( $nodes['modules'] as $module ) {
					if ( $column->node == $module->parent ) {
						$js .= self::render_module_js( $module );
					}
				}
			}
		}

		// Return the JS.
		return $js;
	}

	/**
	 * Renders the JavaScript for all modules in a single column.
	 *
	 * @since 1.7
	 * @param string|object $col_id A column ID or object.
	 * @return string
	 */
	static public function render_column_modules_js( $col_id ) {
		$col   = is_object( $col_id ) ? $col_id : FLBuilderModel::get_node( $col_id );
		$nodes = FLBuilderModel::get_categorized_nodes();
		$js    = '';

		// Render the JS.
		foreach ( $nodes['modules'] as $module ) {
			if ( $col->node == $module->parent ) {
				$js .= self::render_module_js( $module );
			}
		}

		// Return the JS.
		return $js;
	}

	/**
	 * Renders the JavaScript for a single module.
	 *
	 * @since 1.7
	 * @param string|object $module_id A module ID or object.
	 * @return string
	 */
	static public function render_module_js( $module_id ) {
		$module          = is_object( $module_id ) ? $module_id : FLBuilderModel::get_module( $module_id );
		$global_settings = FLBuilderModel::get_global_settings();
		$js              = '';

		// Global module JS
		$file = $module->dir . 'js/frontend.js';

		if ( fl_builder_filesystem()->file_exists( $file ) && ! in_array( $module->settings->type . '-module-js', self::$enqueued_global_assets ) ) {
			$js                            .= "\n" . fl_builder_filesystem()->file_get_contents( $file );
			self::$enqueued_global_assets[] = $module->settings->type . '-module-js';
		}

		// Instance module JS
		$file     = $module->dir . 'includes/frontend.js.php';
		$settings = $module->settings;
		$id       = $module->node;

		if ( ! in_array( $id, self::$enqueued_module_js_assets ) && fl_builder_filesystem()->file_exists( $file ) ) {
			self::$enqueued_module_js_assets[] = $id;
			ob_start();
			include $file;
			$js .= ob_get_clean();
		}

		// Return the JS.
		return $js;
	}

	/**
	 * Renders the custom CSS or JS for all global nodes in a layout.
	 *
	 * @since 1.7
	 */
	static public function render_global_nodes_custom_code( $type = 'css' ) {
		$code     = '';
		$rendered = array();

		if ( ! FLBuilderModel::is_post_node_template() ) {

			$nodes       = FLBuilderModel::get_layout_data();
			$node_status = FLBuilderModel::get_node_status();

			foreach ( $nodes as $node_id => $node ) {

				$template_post_id = FLBuilderModel::is_node_global( $node );

				if ( $template_post_id && ! in_array( $template_post_id, $rendered ) ) {

					$rendered[] = $template_post_id;
					$code      .= FLBuilderModel::get_layout_settings( $node_status, $template_post_id )->{ $type };
				}
			}
		}

		return $code;
	}

	/**
	 * Check if publish should require page to refresh.
	 *
	 * @since 2.0
	 * @return void
	 */
	static public function should_refresh_on_publish() {
		$refresh = ! is_admin_bar_showing() || isset( $_GET['safemode'] );
		return apply_filters( 'fl_builder_should_refresh_on_publish', $refresh );
	}

	/**
	 * Register svg shape art to be used in a shape layer
	 *
	 * @since 2.2
	 * @param Array $args
	 * @return void
	 */
	static public function register_shape( $args = array() ) {
		FLBuilderArt::register_shape( $args );
	}

	/**
	 * Custom logging function that handles objects and arrays.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function log() {
		foreach ( func_get_args() as $arg ) {
			ob_start();
			print_r( $arg );
			error_log( ob_get_clean() );
		}
	}

	/**
	 * Filter WP uploads and check filetype is valid for photo and video modules.
	 * @since 1.10.8
	 */
	static public function wp_handle_upload_prefilter_filter( $file ) {

		$type = isset( $_POST['fl_upload_type'] ) ? $_POST['fl_upload_type'] : false;

		$ext = pathinfo( $file['name'], PATHINFO_EXTENSION );

		$regex = array(
			'photo' => '#(jpe?g|png|gif|bmp|tiff?|webp)#i',
			'video' => '#(mp4|m4v|webm)#i',
		);

		if ( ! $type ) {
			return $file;
		}

		$regex = apply_filters( 'fl_module_upload_regex', $regex, $type, $ext, $file );

		if ( ! preg_match( $regex[ $type ], $ext ) ) {
			/* translators: %s: extension type */
			$file['error'] = sprintf( __( 'The uploaded file is not a valid %s extension.', 'fl-builder' ), $type );
		}

		return $file;
	}

	/**
	 * Default HTML for no image.
	 * @since 1.10.8
	 * @return string
	 */
	static public function default_image_html( $classes ) {
		return sprintf( '<img src="%s" class="%s" />', FL_BUILDER_URL . 'img/no-image.png', $classes );
	}

	/**
	 * Check if debug is enabled.
	 * @since 1.10.8.2
	 * @return bool
	 */
	static public function is_debug() {

		$debug = false;

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug = true;
		}

		return apply_filters( 'fl_is_debug', $debug );
	}

	/**
	 * Get the fa5 url.
	 * @since 2.2
	 * @return string url
	 */
	static public function get_fa5_url() {

		/**
		 * Enable the PRO font-awesome-5 icon set.
		 * This will also enqueue the CSS from the CDN.
		 * @see fl_enable_fa5_pro
		 */
		$url = ( self::fa5_pro_enabled() ) ? self::$fa5_pro_url : FL_BUILDER_URL . 'fonts/fontawesome/' . self::get_fa5_version() . '/css/all.min.css';

		/**
		 * Filter FA5 URL for enqueue.
		 * @see fl_get_fa5_url
		 * @since 2.2.1
		 */
		return apply_filters( 'fl_get_fa5_url', $url );
	}

	static public function get_fa5_version() {

		return FLBuilderFontAwesome::latest_supported();
	}

	static public function fa5_pro_enabled() {
		$enabled = FLBuilderFontAwesome::is_pro_enabled( apply_filters( 'fl_enable_fa5_pro', false ) );

		// if filter was set to true return true anyway.
		if ( $enabled ) {
			return true;
		}

		if ( is_multisite() && FLBuilderAdminSettings::multisite_support() ) {
			// if switched...
			if ( $GLOBALS['switched'] ) {
				if ( get_blog_option( $GLOBALS['_wp_switched_stack'][0], '_fl_builder_enable_fa_pro' ) ) {
					// override enabled...
					return get_blog_option( $GLOBALS['_wp_switched_stack'][0], '_fl_builder_enable_fa_pro' );
				} else {
					return get_option( '_fl_builder_enable_fa_pro' );
				}
			}

			// were not switched...
			if ( ! get_option( '_fl_builder_enabled_icons' ) ) {
				$id = defined( 'BLOG_ID_CURRENT_SITE' ) ? BLOG_ID_CURRENT_SITE : 1;
				return get_blog_option( $id, '_fl_builder_enable_fa_pro' );
			}
		}
		return FLBuilderModel::get_admin_settings_option( '_fl_builder_enable_fa_pro' );
	}

	/**
	 * @since 2.4.2
	 */
	static public function fa5_kit_url() {

		if ( is_multisite() && FLBuilderAdminSettings::multisite_support() ) {
			// if switched...
			if ( $GLOBALS['switched'] ) {
				if ( get_blog_option( $GLOBALS['_wp_switched_stack'][0], '_fl_builder_kit_fa_pro' ) ) {
					// override enabled...
					return get_blog_option( $GLOBALS['_wp_switched_stack'][0], '_fl_builder_kit_fa_pro' );
				} else {
					return get_option( '_fl_builder_kit_fa_pro' );
				}
			}

			// were not switched...
			if ( ! get_option( '_fl_builder_enabled_icons' ) ) {
				$id = defined( 'BLOG_ID_CURRENT_SITE' ) ? BLOG_ID_CURRENT_SITE : 1;
				return get_blog_option( $id, '_fl_builder_kit_fa_pro' );
			}
		}
		return FLBuilderModel::get_admin_settings_option( '_fl_builder_kit_fa_pro' );
	}

	/**
	 * Remove template type from wp-link suggestions.
	 * @since 2.2.2
	 */
	static public function wp_link_query_args_filter( $query ) {

		if ( array_search( 'fl-builder-template', $query['post_type'] ) ) {
			unset( $query['post_type'][ array_search( 'fl-builder-template', $query['post_type'] ) ] );
		}
		return $query;
	}

	/**
	 * @since 2.2.3
	 */
	static public function is_schema_enabled() {

		/**
		 * Disable all schema.
		 * @see fl_builder_disable_schema
		 */
		if ( false !== apply_filters( 'fl_builder_disable_schema', false ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @since 2.2.3
	 */
	static public function print_schema( $schema, $echo = true ) {
		if ( self::is_schema_enabled() ) {
			if ( $echo ) {
				echo $schema;
			} else {
				return $schema;
			}
		}
	}

	static public function check_content_filters() {

		if ( ! did_action( 'fl_did_render_content_filter' ) ) {
			// we need to do a popup
			$content = __( 'You must call the_content in the current theme template in order for Beaver Builder to work on this layout.', 'fl-builder' );
			?>
			<script>
				if ( FLBuilder ) {
					FLBuilder.alert('<?php echo esc_attr( $content ); ?>');
				}
			</script>
			<?php
		}
	}

	/**
	 * @since 1.0
	 * @deprecated 1.7.4
	 */
	static public function layout_styles_scripts( $post_id ) {
		_deprecated_function( __METHOD__, '1.7.4', __CLASS__ . '::enqueue_layout_styles_scripts()' );

		self::enqueue_layout_styles_scripts();
	}

	/**
	 * @since 1.0
	 * @deprecated 1.7.4
	 */
	static public function styles_scripts() {
		_deprecated_function( __METHOD__, '1.7.4', __CLASS__ . '::enqueue_ui_styles_scripts()' );

		self::enqueue_ui_styles_scripts();
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function register_templates_post_type() {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderUserTemplates::register_post_type()' );

		if ( class_exists( 'FLBuilderUserTemplates' ) ) {
			FLBuilderUserTemplates::register_post_type();
		}
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function render_template( $template ) {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderUserTemplates::template_include()' );

		if ( class_exists( 'FLBuilderUserTemplates' ) ) {
			FLBuilderUserTemplates::template_include();
		}
	}

	/**
	 * @since 1.6.3
	 * @deprecated 1.8
	 */
	static public function render_ui_panel_node_templates() {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderUserTemplates::render_ui_panel_node_templates()' );

		if ( class_exists( 'FLBuilderUserTemplates' ) ) {
			FLBuilderUserTemplates::render_ui_panel_node_templates();
		}
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static public function render_user_template_settings() {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderUserTemplates::render_settings()' );

		if ( class_exists( 'FLBuilderUserTemplates' ) ) {
			FLBuilderUserTemplates::render_settings();
		}
	}

	/**
	 * @since 1.6.3
	 * @deprecated 1.8
	 */
	static public function render_node_template_settings( $node_id = null ) {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderUserTemplates::render_node_settings()' );

		if ( class_exists( 'FLBuilderUserTemplates' ) ) {
			FLBuilderUserTemplates::render_node_settings( $node_id );
		}
	}

	/**
	 * @since 1.0
	 * @deprecated 2.0
	 */
	static public function render_template_selector() {
		_deprecated_function( __METHOD__, '2.0' );

		return array(
			'html' => '',
		);
	}

	/**
	 * @since 1.8
	 * @deprecated 2.0
	 */
	static public function render_ui_panel_row_templates() {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.8
	 * @deprecated 2.0
	 */
	static public function render_ui_panel_modules_templates() {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.8
	 * @deprecated 2.0
	 */
	static public function render_layout_settings() {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.0
	 * @deprecated 2.0
	 */
	static public function render_global_settings() {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.0
	 * @deprecated 2.0
	 */
	static public function render_row_settings( $node_id = null ) {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.0
	 * @deprecated 2.0
	 */
	static public function render_column_settings( $node_id = null ) {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.0
	 * @deprecated 2.0
	 */
	static public function render_module_settings( $node_id = null, $type = null, $parent_id = null, $render_state = true ) {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 2.0.1
	 * @deprecated 2.0.7
	 */
	static public function render_settings_config() {
		_deprecated_function( __METHOD__, '2.0.7', 'FLBuilderUISettingsForms::render_settings_config()' );

		FLBuilderUISettingsForms::render_settings_config();
	}

	/**
	 * @since 1.9
	 * @deprecated 2.2
	 */
	static public function render_row_border( $row ) {
		_deprecated_function( __METHOD__, '2.2', 'FLBuilderCSS::responsive_rule()' );
	}

	/**
	 * @since 1.9
	 * @deprecated 2.2
	 */
	static public function render_column_border( $col ) {
		_deprecated_function( __METHOD__, '2.2', 'FLBuilderCSS::responsive_rule()' );
	}

	/**
	 * @since 1.0
	 * @deprecated 2.2
	 */
	static public function include_jquery() {
		_deprecated_function( __METHOD__, '2.2' );
	}
}

FLBuilder::init();
