<?php

/**
 * Helper class for font settings.
 *
 * @class FLFonts
 */
final class FLFonts {

	/**
	 * @method json
	 */
	static public function js() {
		$system = json_encode( FLFontFamilies::get_system() );
		$google = json_encode( FLFontFamilies::get_google() );

		if ( class_exists( 'FLBuilderFonts' ) && method_exists( 'FLBuilderFonts', 'get_font_weight_strings' ) ) {
			$weights = json_encode( FLBuilderFonts::get_font_weight_strings() );
		} else {
			$weights = json_encode( array(
				'default'   => __( 'Default', 'fl-automator' ),
				'regular'   => __( 'Regular', 'fl-automator' ),
				'italic'    => __( 'Italic', 'fl-automator' ),
				'100'       => __( 'Thin', 'fl-automator' ),
				'100italic' => __( 'Thin Italic', 'fl-automator' ),
				'200'       => __( 'Extra-Light', 'fl-automator' ),
				'200italic' => __( 'Extra-Light Italic', 'fl-automator' ),
				'300'       => __( 'Light', 'fl-automator' ),
				'300italic' => __( 'Light Italic', 'fl-automator' ),
				'400'       => __( 'Normal', 'fl-automator' ),
				'500'       => __( 'Medium', 'fl-automator' ),
				'500italic' => __( 'Medium Italic', 'fl-automator' ),
				'600'       => __( 'Semi-Bold', 'fl-automator' ),
				'600italic' => __( 'Semi-Bold Italic', 'fl-automator' ),
				'700'       => __( 'Bold', 'fl-automator' ),
				'700italic' => __( 'Bold Italic', 'fl-automator' ),
				'800'       => __( 'Extra-Bold', 'fl-automator' ),
				'800italic' => __( 'Extra-Bold Italic', 'fl-automator' ),
				'900'       => __( 'Ultra-Bold', 'fl-automator' ),
				'900italic' => __( 'Ultra-Bold Italic', 'fl-automator' ),
			));
		}

		echo 'var FLFontFamilies = { system: ' . $system . ', google: ' . $google . ', weights: ' . $weights . ' };';
	}

	/**
	 * @method display_select_options
	 */
	static public function display_select_options( $selected ) {
		echo '<optgroup label="System">';

		foreach ( FLFontFamilies::get_system() as $name => $variants ) {
			echo '<option value="' . $name . '" ' . selected( $name, $selected ) . '>' . $name . '</option>';
		}

		echo '<optgroup label="Google">';

		foreach ( FLFontFamilies::get_google() as $name => $variants ) {
			echo '<option value="' . $name . '" ' . selected( $name, $selected ) . '>' . $name . '</option>';
		}
	}
}

/**
 * Font info class for system and Google fonts.
 *
 * @class FLFontFamilies
 */
final class FLFontFamilies {

	/**
	 * Cache for google fonts
	 */
	static private $_google_fonts = false;

	/**
	 * @property system
	 */
	static public $system = array(
		'Helvetica' => array(
			'fallback' => 'Verdana, Arial, sans-serif',
			'weights'  => array(
				'300',
				'400',
				'700',
			),
		),
		'Verdana'   => array(
			'fallback' => 'Helvetica, Arial, sans-serif',
			'weights'  => array(
				'300',
				'400',
				'700',
			),
		),
		'Arial'     => array(
			'fallback' => 'Helvetica, Verdana, sans-serif',
			'weights'  => array(
				'300',
				'400',
				'700',
			),
		),
		'Times'     => array(
			'fallback' => 'Georgia, serif',
			'weights'  => array(
				'300',
				'400',
				'700',
			),
		),
		'Georgia'   => array(
			'fallback' => 'Times, serif',
			'weights'  => array(
				'300',
				'400',
				'700',
			),
		),
		'Courier'   => array(
			'fallback' => 'monospace',
			'weights'  => array(
				'300',
				'400',
				'700',
			),
		),
		'system-ui' => array(
			'fallback' => "-apple-system, BlinkMacSystemFont,
             'Segoe UI', Roboto, 'Helvetica Neue',
             Ubuntu, Arial, sans-serif",
			'weights'  => array(
				'300',
				'400',
				'700',
			),
		),
	);

	/**
	 * @method get_system
	 */
	static function get_system() {
		return apply_filters( 'fl_theme_system_fonts', FLFontFamilies::$system );
	}

	/**
	 * @since 1.7.1
	 */
	static private function _get_json() {

		$json = (array) json_decode( file_get_contents( trailingslashit( FL_THEME_DIR ) . 'json/fonts.json' ), true );

		/**
		 * Filter raw google json data
		 * @see fl_theme_get_google_json
		 */
		return apply_filters( 'fl_theme_get_google_json', $json );
	}

	/**
	 * @method get_google
	 */
	static function get_google() {

		if ( ! self::$_google_fonts ) {
			$fonts = array();

			$json = self::_get_json();

			foreach ( $json as $k => $font ) {
				$name = key( $font );

				foreach ( $font[ $name ]['variants'] as $key => $variant ) {

					if ( 'regular' === $variant ) {
						$font[ $name ]['variants'][ $key ] = '400';
					}
				}

				$fonts[ $name ] = $font[ $name ]['variants'];
			}
			self::$_google_fonts = $fonts;
		}

		return apply_filters( 'fl_theme_google_fonts', self::$_google_fonts );
	}

	/**
	 * @since 1.7.1
	 */
	static public function get_google_fallback( $font ) {
		$json = self::_get_json();
		foreach ( $json as $k => $google ) {
			$name = key( $google );
			if ( $name === $font ) {
				return $google[ $name ]['fallback'];
			}
		}
		return false;
	}

}
