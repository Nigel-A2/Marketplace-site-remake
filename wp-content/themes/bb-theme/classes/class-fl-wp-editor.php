<?php
/**
 * Handle integration with the new (gutenberg) editor.
 *
 * @since 1.7
 */
class FLWPEditor {

	/**
	* Init the editor styles manager
	*
	* @access public
	* @return void
	*/
	static public function init() {
		add_action( 'customize_preview_init', 'FLWPEditor::refresh_css' );
		add_action( 'customize_save_after', 'FLWPEditor::refresh_css' );
		add_action( 'after_switch_theme', 'FLWPEditor::refresh_css' );
		add_action( 'enqueue_block_editor_assets', 'FLWPEditor::enqueue_styles' );
	}

	/**
	* Get the base filename slug
	*
	* @access public
	* @return string
	*/
	static public function slug() {
		return 'editor';
	}

	/**
	* Get the option prefix
	*
	* @access public
	* @return string
	*/
	static public function prefix() {
		return 'fl_theme_css_key';
	}

	/**
	* Get the url to the generated editor stylesheet
	*
	* @access public
	* @return string
	*/
	static public function css_url() {
		$cache_dir = FLCustomizer::get_cache_dir();
		$key       = get_option( self::prefix() . '-' . self::slug() );
		return $cache_dir['url'] . self::slug() . '-' . $key . '.css';
	}

	/**
	* Compile and write the editor stylesheet
	*
	* @access public
	* @return void
	*/
	static public function compile_css() {
		$cache_dir   = FLCustomizer::get_cache_dir();
		$new_key     = uniqid();
		$slug        = self::slug();
		$prefix      = self::prefix();
		$option_name = $prefix . '-' . $slug;
		$filename    = $cache_dir['path'] . $slug . '-' . $new_key . '.css';
		$vars        = FLCustomizer::_get_less_vars();

		$paths = apply_filters( 'fl_theme_compile_editor_less_paths', array(
			FL_THEME_DIR . '/less/mixins.less',
			FL_THEME_DIR . '/less/editor.less',
		));

		// Loop over paths and get contents
		$css = FLCSS::paths_get_contents( $paths );

		// Filter less before compiling
		$css = apply_filters( 'fl_theme_compile_less', $css );

		// Replace {FL_THEME_URL} placeholder.
		$css = FLCSS::replace_tokens( $css );

		// Compile LESS
		$css = FLCSS::compile_less( $vars . $css );

		/**
		 * Make sure $css is not a WP Error object.
		 */
		if ( is_wp_error( $css ) ) {
			return false;
		}

		// Compress
		if ( ! WP_DEBUG ) {
			$css = FLCSS::compress_css( $css );
		}

		// Save the new css.
		if ( 'file' === FLTheme::get_asset_enqueue_method() ) {
			fl_theme_filesystem()->file_put_contents( $filename, $css );
		} else {
			FLTheme::update_cached_css( 'editor', $css );
			return $css;
		}

		// Save the new css key.
		update_option( $option_name, $new_key );
	}

	/**
	* Clear any editor stylesheets in the cache directory
	*
	* @access public
	* @return void
	*/
	static public function clear_css_cache() {
		$dir_name  = basename( FL_THEME_DIR );
		$cache_dir = FLCustomizer::get_cache_dir();

		if ( ! empty( $cache_dir['path'] ) && stristr( $cache_dir['path'], $dir_name ) ) {

			$css = glob( $cache_dir['path'] . self::slug() . '-*' );

			foreach ( $css as $file ) {
				if ( is_file( $file ) ) {
					unlink( $file );
				}
			}
		}
	}

	/**
	* Dump any existing editor stylesheets and recompile
	*
	* @access public
	* @return void
	*/
	static public function refresh_css() {
		self::clear_css_cache();
		self::compile_css();
	}

	/**
	* Enqueue the editor stylesheet
	*
	* @access public
	* @return void
	*/
	static public function enqueue_styles() {
		self::refresh_css();
		$url = self::css_url();
		if ( 'file' === FLTheme::get_asset_enqueue_method() ) {
			wp_enqueue_style( 'fl-automator-editor', $url, array(), FL_THEME_VERSION );
		} else {
			wp_enqueue_style( 'bb-theme-style', get_stylesheet_uri() );
			wp_add_inline_style( 'bb-theme-style', FLTheme::get_cached_css( 'editor' ) );
		}
	}
}
FLWPEditor::init();
