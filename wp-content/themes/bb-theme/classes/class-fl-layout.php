<?php

/**
 * Handles layout specific logic such as outputting
 * container, row, and column classes for the selected
 * layout framework.
 *
 * @since 1.7
 */
final class FLLayout {

	/**
	 * The framework slug selected in the Customizer.
	 *
	 * @since 1.7
	 * @access private
	 * @var array $framework
	 */
	static private $framework;

	/**
	 * @since 1.7
	 * @return void
	 */
	static public function init() {
		add_filter( 'fl_theme_compile_less_paths', __CLASS__ . '::filter_less_paths' );
		add_filter( 'body_class', __CLASS__ . '::filter_body_class' );
		add_filter( 'nav_menu_css_class', __CLASS__ . '::filter_nav_menu_item_classes', 999, 4 );
		add_filter( 'nav_menu_link_attributes', __CLASS__ . '::filter_nav_menu_link_classes', 999, 4 );
		add_filter( 'fl_theme_framework_enqueue', __CLASS__ . '::fl_theme_framework_enqueue' );
	}

	/**
	 * Returns the framework slug selected in the Customizer.
	 *
	 * @since 1.7
	 * @return string
	 */
	static public function get_framework() {
		if ( ! self::$framework ) {
			self::$framework = FLTheme::get_setting( 'fl-framework' );
		}
		return self::$framework;
	}

	/**
	 * Enqueue's assets for the selected framework.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function enqueue_framework() {
		$framework = apply_filters( 'fl_theme_framework_enqueue', self::get_framework() );
		$css_path  = '/css/' . $framework . '.min.css';
		$js_path   = '/js/' . $framework . '.min.js';

		if ( file_exists( FL_THEME_DIR . $css_path ) ) {
			wp_enqueue_style( $framework, FL_THEME_URL . $css_path, array(), FL_THEME_VERSION );
		}
		if ( file_exists( FL_THEME_DIR . $js_path ) ) {
			wp_enqueue_script( $framework, FL_THEME_URL . $js_path, array(), FL_THEME_VERSION, true );
		}
	}

	/**
	 * Adds the selected framework's less file to the theme's
	 * less paths that get compiled into the skin file.
	 *
	 * @since 1.7
	 * @param array $paths
	 * @return array
	 */
	static public function filter_less_paths( $paths ) {
		$framework = self::get_framework();
		switch ( $framework ) {
			case 'base-4':
			case 'bootstrap-4':
				$paths[] = FL_THEME_DIR . '/less/theme-bootstrap-4.less';
				break;
			default:
				$paths[] = FL_THEME_DIR . '/less/theme-' . self::get_framework() . '.less';
				break;
		}
		return $paths;
	}

	/**
	 * Adds the framework class name to the body tag.
	 *
	 * @since 1.7
	 * @param array $classes
	 * @return array
	 */
	static public function filter_body_class( $classes ) {
		$classes[] = 'fl-framework-' . self::get_framework();
		return $classes;
	}

	/**
	 * Returns the container class for the selected framework.
	 *
	 * @since 1.7
	 * @return string
	 */
	static public function get_container_class() {
		switch ( self::get_framework() ) {
			case 'base':
			case 'bootstrap':
			case 'bootstrap-4':
			case 'base-4':
				return 'container';
			break;
		}
	}

	/**
	 * Outputs the container class for the selected framework.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function container_class() {
		echo self::get_container_class();
	}

	/**
	 * Returns the row class for the selected framework.
	 *
	 * @since 1.7
	 * @return string
	 */
	static public function get_row_class() {
		switch ( self::get_framework() ) {
			case 'base':
			case 'bootstrap':
			case 'bootstrap-4':
			case 'base-4':
				return 'row';
			break;
		}
	}

	/**
	 * Outputs the row class for the selected framework.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function row_class() {
		echo self::get_row_class();
	}

	/**
	 * Returns the column class for the selected framework.
	 *
	 * @since 1.7
	 * @param string $size
	 * @param int $cols
	 * @return string
	 */
	static public function get_col_class( $size, $cols ) {
		$frameworks = array(
			'base'        => array(
				'xs' => 'xs',
				'sm' => 'sm',
				'md' => 'md',
				'lg' => 'lg',
			),
			'bootstrap'   => array(
				'xs' => 'xs',
				'sm' => 'sm',
				'md' => 'md',
				'lg' => 'lg',
			),
			'bootstrap-4' => array(
				'sm' => 'md',
				'md' => 'lg',
				'lg' => 'xl',
			),
			'base-4'      => array(
				'sm' => 'md',
				'md' => 'lg',
				'lg' => 'xl',
			),
		);

		$size = $frameworks[ self::get_framework() ][ $size ];

		return sprintf( 'col-%s-%s', $size, $cols );
	}

	/**
	 * Outputs the column class for the selected framework.
	 *
	 * @since 1.7
	 * @param string $size
	 * @param int $cols
	 * @return void
	 */
	static public function col_class( $size, $cols ) {
		echo self::get_col_class( $size, $cols );
	}

	/**
	 * Returns column classes for the selected framework.
	 *
	 * @since 1.7
	 * @param array $sizes
	 * @return string
	 */
	static public function get_col_classes( $sizes ) {
		$classes = array();
		foreach ( $sizes as $size => $cols ) {
			$classes[] = self::get_col_class( $size, $cols );
		}
		return implode( ' ', $classes );
	}

	/**
	 * Outputs column classes for the selected framework.
	 *
	 * @since 1.7
	 * @param array $sizes
	 * @return void
	 */
	static public function col_classes( $sizes ) {
		echo self::get_col_classes( $sizes );
	}

	/**
	 * Returns the class for the main content wrapper.
	 *
	 * @since 1.7
	 * @param string $section The section this content belongs to.
	 * @return string
	 */
	static public function get_content_class( $section = 'blog' ) {
		$layout       = FLTheme::get_setting( 'fl-' . $section . '-layout' );
		$sidebar_size = FLTheme::get_setting( 'fl-' . $section . '-sidebar-size' );
		$content_size = '8';

		if ( '2' === $sidebar_size ) {
			$content_size = '10';
		} elseif ( '3' === $sidebar_size ) {
			$content_size = '9';
		}

		if ( ! FLTheme::is_sidebar_enabled( $section ) ) {
			return self::get_col_class( 'md', 12 );
		} elseif ( strstr( $layout, 'left' ) ) {
			return 'fl-content-right ' . self::get_col_class( 'md', $content_size );
		} elseif ( strstr( $layout, 'right' ) ) {
			return 'fl-content-left ' . self::get_col_class( 'md', $content_size );
		}

		return self::get_col_class( 'md', 12 );
	}

	/**
	 * Outputs the class for the main content wrapper.
	 *
	 * @since 1.7
	 * @param string $section The section this content belongs to.
	 * @return void
	 */
	static public function content_class( $section = 'blog' ) {
		echo self::get_content_class( $section );
	}

	/**
	 * Adds the nav-item class to nav menu <li> tags via
	 * the nav_menu_css_class filter.
	 *
	 * @since 1.7
	 * @param array $classes
	 * @param object $item
	 * @param object $args
	 * @return array
	 */
	static public function filter_nav_menu_item_classes( $classes, $item, $args, $depth = 0 ) {
		$locations = FLTheme::get_nav_locations();
		if ( isset( $locations[ $args->theme_location ] ) ) {
			$classes[] = 'nav-item';
		}
		return $classes;
	}

	/**
	 * Adds the nav-link class to nav menu <a> tags via
	 * the nav_menu_link_attributes filter.
	 *
	 * @since 1.7
	 * @param array $attrs
	 * @param object $item
	 * @param object $args
	 * @return array
	 */
	static public function filter_nav_menu_link_classes( $attrs, $item, $args, $depth = 0 ) {
		$locations = FLTheme::get_nav_locations();
		if ( isset( $locations[ $args->theme_location ] ) ) {
			$attrs['class'] = isset( $attrs['class'] ) ? $attrs['class'] . ' nav-link' : 'nav-link';
		}
		return $attrs;
	}

	/**
	 * @since 1.7.7
	 */
	static public function fl_theme_framework_enqueue( $framework ) {
		if ( isset( $_GET['fl_builder'] ) ) {
			return str_replace( 'bootstrap', 'base', $framework );
		}
		return $framework;
	}
}

FLLayout::init();
