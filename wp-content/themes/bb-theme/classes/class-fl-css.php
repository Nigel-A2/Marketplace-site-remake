<?php
class FLCSS {

	/**
	* Get the string contents of an array of file paths
	*
	* @access public
	* @param Array $paths
	* @return string
	*/
	static public function paths_get_contents( $paths = array() ) {
		$css = '';
		foreach ( $paths as $path ) {
			if ( fl_theme_filesystem()->file_exists( $path ) ) {
				$css .= fl_theme_filesystem()->file_get_contents( $path );
			}
		}
		return $css;
	}

	/**
	* Remove whitespace from a css string
	*
	* @access public
	* @param String $css
	* @return String
	*/
	static public function compress_css( $css = '' ) {
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
		return $css;
	}

	/**
	* Replace any tokens in a CSS String
	*
	* @access public
	* @param String $css
	* @return String
	*/
	static public function replace_tokens( $css = '' ) {
		return str_replace( '{FL_THEME_URL}', FL_THEME_URL, $css );
	}

	/**
	* Handle filters for internet explorer
	*
	* @access public
	* @param String $css
	* @return String
	*/
	static public function normalize_ie_filters( $css = '' ) {
		// Fix issue with IE filters
		return preg_replace_callback( '(filter\s?:\s?(.*);)', 'FLCustomizer::_preg_replace_less', $css );
	}

	/**
	* Compile a less string into pure css
	*
	* @access public
	* @param String $css
	* @return String
	*/
	static public function compile_less( $css = '' ) {

		if ( has_filter( 'fl_theme_compile_less_overide' ) ) {
			/**
			 * Allow devs to use custom Less compile tools.
			 * Note: Must return compiled CSS or WP_Error object on compile error.
			 * @since 1.7.1
			 * @see fl_theme_compile_less_overide
			 */
			return apply_filters( 'fl_theme_compile_less_overide', $css );
		}

		$result = '';

		if ( version_compare( PHP_VERSION, '5.3.0', '<' ) || defined( 'FL_THEME_DEPRECATED_LESSC' ) ) {
			if ( ! class_exists( 'lessc' ) ) {
				require_once FL_THEME_DIR . '/classes/class-lessc-deprecated.php';
			}
			try {
				$less   = new lessc;
				$result = $less->compile( $css );
			} catch ( Exception $e ) {
				$error     = $e->getMessage();
				$error_txt = sprintf( 'Beaver Builder LESS Error: %s', $error );
				$result    = new WP_Error( 'Guru Meditation', $error );
				error_log( $error_txt );
			}
			return $result;
		}

		if ( ! class_exists( 'Less_Autoloader' ) ) {
			require_once FL_THEME_DIR . '/includes/vendor/Less/Autoloader.php';
			Less_Autoloader::register();
		}

		$parser = new Less_Parser(
			array(
				'compress' => true,
			)
		);

		try {
			$parser->parse( $css );
			$result = $parser->getCss();
		} catch ( Exception $e ) {
			$error     = str_replace( ' in file anonymous-file-0.less in anonymous-file-0.less', '.', $e->getMessage() );
			$error_txt = sprintf( 'Beaver Builder LESS Error: %s', $error );
			$result    = new WP_Error( 'Guru Meditation', $error );
			error_log( $error_txt );
		}
		return $result;
	}
}
