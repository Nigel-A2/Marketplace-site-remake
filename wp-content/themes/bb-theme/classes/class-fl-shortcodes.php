<?php
/**
 * shortcodes class
 *
 * @since 1.7
 */
final class FLThemeShortcodes {

	/**
	 * Add shortcodes available in theme.
	 */
	static public function init() {
		add_shortcode( 'fl_year', array( 'FLThemeShortcodes', 'fl_year_callback' ) );
	}

	/**
	 * Year shortcode.
	 */
	static function fl_year_callback( $atts ) {

		$atts = shortcode_atts( array(
			'format' => 'Y',
		), $atts );

		$date = gmdate( $atts['format'] );

		$tz = get_option( 'timezone_string' );

		if ( $tz ) {
			$tzdate = new DateTime( gmdate( 'Y-m-d H:i:s' ), new DateTimeZone( 'UTC' ) );
			$tzdate->setTimezone( new DateTimeZone( $tz ) );
			$date = $tzdate->format( $atts['format'] );
		}

		return $date;
	}

}
FLThemeShortcodes::init();
