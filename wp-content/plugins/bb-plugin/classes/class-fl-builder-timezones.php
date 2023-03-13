<?php

/**
 * Timezones helper used by timezone field.
 *
 * @class   FLBuilderTimezones
 * @since   1.6.3
 */
final class FLBuilderTimezones {

	// This function was originally copied from WordPress Core wp-includes/functions.php
	// Original function wp_timezone_choice() is not pluggable or filterable and will
	// return data that breaks the module options.

	/**
	 * An array of continents.
	 * @var array
	 */
	static private $continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

	/**
	 * Generates a list of timezones.
	 *
	 * @since 1.6.5
	 *
	 * @var bool $mo_loaded
	 *
	 * @param string $selected_zone Selected timezone.
	 * @return string
	 */
	static public function build_timezones( $selected_zone ) {
		static $mo_loaded = false;

		$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

		// Load translations for continents and cities
		if ( ! $mo_loaded ) {
			$locale = get_locale();
			$mofile = WP_LANG_DIR . '/continents-cities-' . $locale . '.mo';
			load_textdomain( 'continents-cities', $mofile );
			$mo_loaded = true;
		}

		$zonen = array();
		foreach ( timezone_identifiers_list() as $zone ) {
			$zone = explode( '/', $zone );
			if ( ! in_array( $zone[0], $continents ) ) {
				continue;
			}

			// This determines what gets set and translated - we don't translate Etc/* strings here, they are done later
			$exists    = array(
				0 => ( isset( $zone[0] ) && $zone[0] ),
				1 => ( isset( $zone[1] ) && $zone[1] ),
				2 => ( isset( $zone[2] ) && $zone[2] ),
			);
			$exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
			$exists[4] = ( $exists[1] && $exists[3] );
			$exists[5] = ( $exists[2] && $exists[3] );

			$zonen[] = array(
				'continent'   => ( $exists[0] ? $zone[0] : '' ),
				'city'        => ( $exists[1] ? $zone[1] : '' ),
				'subcity'     => ( $exists[2] ? $zone[2] : '' ),
				't_continent' => ( $exists[3] ? translate( str_replace( '_', ' ', $zone[0] ), 'continents-cities' ) : '' ), // @codingStandardsIgnoreLine
				't_city'      => ( $exists[4] ? translate( str_replace( '_', ' ', $zone[1] ), 'continents-cities' ) : '' ), // @codingStandardsIgnoreLine
				't_subcity'   => ( $exists[5] ? translate( str_replace( '_', ' ', $zone[2] ), 'continents-cities' ) : '' ), // @codingStandardsIgnoreLine
			);
		}
		usort( $zonen, '_wp_timezone_choice_usort_callback' );

		$structure = array();

		if ( empty( $selected_zone ) ) {
			$structure[] = '<option selected="selected" value="">' . __( 'Select a city', 'fl-builder' ) . '</option>';
		}

		foreach ( $zonen as $key => $zone ) {
			// Build value in an array to join later
			$value = array( $zone['continent'] );

			if ( empty( $zone['city'] ) ) {
				// It's at the continent level (generally won't happen)
				$display = $zone['t_continent'];
			} else {
				// It's inside a continent group

				// Continent optgroup
				if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
					$label       = $zone['t_continent'];
					$structure[] = '<optgroup label="' . esc_attr( $label ) . '">';
				}

				// Add the city to the value
				$value[] = $zone['city'];

				$display = $zone['t_city'];
				if ( ! empty( $zone['subcity'] ) ) {
					// Add the subcity to the value
					$value[]  = $zone['subcity'];
					$display .= ' - ' . $zone['t_subcity'];
				}
			}

			// Build the value
			$value    = join( '/', $value );
			$selected = '';
			if ( $value === $selected_zone ) {
				$selected = 'selected="selected" ';
			}
			$structure[] = '<option ' . $selected . 'value="' . esc_attr( $value ) . '">' . esc_html( $display ) . '</option>';

			// Close continent optgroup
			if ( ! empty( $zone['city'] ) && ( ! isset( $zonen[ $key + 1 ] ) || ( isset( $zonen[ $key + 1 ] ) && $zonen[ $key + 1 ]['continent'] !== $zone['continent'] ) ) ) {
				$structure[] = '</optgroup>';
			}
		}

		// Do UTC
		$structure[] = '<optgroup label="' . esc_attr__( 'UTC', 'fl-builder' ) . '">';
		$selected    = '';
		if ( 'UTC' === $selected_zone ) {
			$selected = 'selected="selected" ';
		}
		$structure[] = '<option ' . $selected . 'value="' . esc_attr__( 'UTC', 'fl-builder' ) . '">' . __( 'UTC', 'fl-builder' ) . '</option>';
		$structure[] = '</optgroup>';

		return join( "\n", $structure );
	}
}
