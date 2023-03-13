<?php

/**
 * Helper class for working with colors.
 *
 * @since 1.0
 */
final class FLColor {

	/**
	 * Checks to see if a value from a Customizer color
	 * field is a hex color value.
	 *
	 * @since 1.0
	 * @param string $hex The value to check.
	 * @return bool
	 */
	static public function is_hex( $hex ) {
		if ( 'transparent' === $hex || 'false' === $hex || '#' === $hex || empty( $hex ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Converts a value or array of values to a hex color
	 * string. Returns "transparent" if the passed value
	 * cannot be converted to a hex color string.
	 *
	 * @since 1.0
	 * @param string|array $hex If an array, the first value that is a hex will be returned.
	 * @return string
	 */
	static public function hex( $hex ) {
		// No hex. Return transparent.
		if ( ! self::is_hex( $hex ) ) {
			return 'transparent';
		} elseif ( is_string( $hex ) ) {
			return strstr( $hex, '#' ) ? $hex : '#' . $hex;
		} elseif ( is_array( $hex ) ) {
			// Hex is an array. Return first that's not false.
			foreach ( $hex as $key => $value ) {

				if ( ! self::is_hex( $hex[ $key ] ) ) {
					continue;
				}

				return self::hex( $hex[ $key ] );
			}
		}

		return 'transparent';
	}

	/**
	 * Returns a hex color value or "transparent" if the value
	 * cannot be converted to a hex string.
	 *
	 * @since 1.3.1
	 * @param string $hex The value to check.
	 * @return string
	 */
	static public function hex_or_transparent( $hex ) {
		if ( ! self::is_hex( $hex ) ) {
			return 'transparent';
		}

		return self::hex( $hex );
	}

	/**
	 * Returns the foreground color for the provided hex value.
	 *
	 * @since 1.0
	 * @param string $hex The hex to provide a foreground for.
	 * @return string
	 */
	static public function foreground( $hex ) {
		if ( ! self::is_hex( $hex ) ) {
			return 'transparent';
		}

		return self::yiq( $hex ) >= 128 ? '#000000' : '#ffffff';
	}

	/**
	 * Returns a LESS color function for a color that is similar to the
	 * the provided hex value. The color will be adjusted by the number
	 * of levels in the $levels array. The first value in the $levels
	 * array is for light colors, the second is for dark colors and the
	 * third is for colors close to black. The higher the level, the more
	 * the color is adjusted.
	 *
	 * @since 1.0
	 * @param array $levels An array of levels for the color adjustment.
	 * @param string $hex The hex to provide a similar color for.
	 * @return string
	 */
	static public function similar( $levels, $hex ) {
		$hex = self::hex( $hex );

		if ( ! self::is_hex( $hex ) ) {
			return 'transparent';
		}

		$yiq = self::yiq( $hex );
		$hex = strstr( $hex, '#' ) ? $hex : '#' . $hex;

		// Color is light, darken new color.
		if ( $yiq >= 128 ) {
			$level = $levels[0];
			$func  = 'darken';
		} elseif ( $yiq >= 6 ) {
			$level = $levels[1];
			$func  = 'lighten';
		} else {
			// Color is black or close to it, lighten new color.
			$level = $levels[2];
			$func  = 'lighten';
		}

		return ( 0 === $level ) ? $hex : $func . '(' . $hex . ', ' . $level . '%)';
	}

	/**
	 * Returns a formatted hex string.
	 *
	 * @since 1.0
	 * @param string $hex The hex value to format.
	 * @return string
	 */
	static public function clean_hex( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) === 3 ) {
			$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
		}

		return $hex;
	}

	/**
	 * Used to check the brightness of the provided hex value.
	 * Higher return values mean a brighter color.
	 *
	 * @since 1.0
	 * @param string $hex The hex value to check.
	 * @return string
	 */
	static public function yiq( $hex ) {
		$hex = self::clean_hex( $hex );
		$r   = hexdec( substr( $hex, 0, 2 ) );
		$g   = hexdec( substr( $hex, 2, 2 ) );
		$b   = hexdec( substr( $hex, 4, 2 ) );
		$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		return $yiq;
	}

	/**
	 * Used to convert opacity value into percentage.
	 *
	 *
	 *
	 * @param string $opcty The opacity value.
	 * @return string
	 */
	static public function clean_opa( $opacity = false ) {
		if ( empty( $opacity ) || false === $opacity ) {
			$opacity = 0;
		}

		return $opacity . '%';
	}
}
