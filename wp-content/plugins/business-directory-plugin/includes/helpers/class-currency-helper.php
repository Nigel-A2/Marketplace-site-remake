<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 6.2.7
 */
class WPBDP_Currency_Helper {

	/**
	 * @since 6.2.7
	 */
	public static function currency_format( $amount, $args = array() ) {
		$defaults = array(
			'decimals'      => false,
			'force_numeric' => false,
			'currency'      => '',
		);

		$args          = wp_parse_args( $args, $defaults );
		$force_numeric = $args['force_numeric'];
		if ( ! $force_numeric && $amount == '0' ) {
			return __( 'Free', 'business-directory-plugin' );
		}

		$currency = self::get_currency( $args['currency'] );
		if ( $args['decimals'] !== false ) {
			$currency['decimals'] = $args['decimals'];
		}

		if ( ! $currency['symbol_left'] && ! $currency['symbol_right'] ) {
			$currency['symbol_right'] = strtoupper( $currency['code'] );
		}

		return self::format_amount_for_currency( $amount, $currency );
	}

	/**
	 * @since 6.2.7
	 *
	 * @param string|float $amount The string could contain the currency symbol.
	 * @param array|null   $currency
	 * @return string|float
	 */
	public static function format_amount_for_currency( $amount = 0, $currency = null ) {
		if ( is_null( $currency ) ) {
			$currency = self::get_currency();
		}

		if ( $amount === 'placeholder' ) {
			$amount = '[amount]';
		} else {
			if ( is_string( $amount ) ) {
				$amount = floatval( self::prepare_price( $amount, $currency ) );
			}
			$amount = number_format( $amount, $currency['decimals'], $currency['decimal_separator'], $currency['thousand_separator'] );
		}

		$left_symbol  = $currency['symbol_left'] . $currency['symbol_padding'];
		$right_symbol = $currency['symbol_padding'] . $currency['symbol_right'];
		$amount       = $left_symbol . $amount . $right_symbol;

		return $amount;
	}

	/**
	 * @since 6.2.7
	 */
	public static function prepare_price( $price, $currency ) {
		$price = trim( $price );
		if ( ! $price ) {
			return 0;
		}

		preg_match_all( '/[\-]*[0-9,.]*\.?\,?[0-9]+/', $price, $matches );
		$price = $matches ? end( $matches[0] ) : 0;
		if ( $price ) {
			$price = self::maybe_use_decimal( $price, $currency );
			$price = str_replace( $currency['decimal_separator'], '.', str_replace( $currency['thousand_separator'], '', $price ) );
		}
		return $price;
	}

	/**
	 * @since 6.2.7
	 */
	private static function maybe_use_decimal( $amount, $currency ) {
		if ( $currency['thousand_separator'] == '.' ) {
			$amount_parts = explode( '.', $amount );
			$used_for_decimal = ( count( $amount_parts ) == 2 && strlen( $amount_parts[1] ) == 2 );
			if ( $used_for_decimal ) {
				$amount = str_replace( '.', $currency['decimal_separator'], $amount );
			}
		}
		return $amount;
	}

	/**
	 * Check the dropdown, then the custom currency option.
	 *
	 * @return string
	 */
	public static function get_currency_code() {
		$code = wpbdp_get_option( 'currency', 'USD' );
		if ( empty( $code ) ) {
			$code = wpbdp_get_option( 'currency-code', 'USD' );
		}
		return trim( $code );
	}

	/**
	 * @since 6.2.7
	 */
	public static function get_currency( $code = '' ) {
		if ( empty( $code ) ) {
			$code = trim( wpbdp_get_option( 'currency', 'USD' ) );
		}
		if ( $code ) {
			$currency         = self::get_currencies( $code );
			$currency['code'] = $code;
		} else {
			// Use custom settings.
			$currency_symbol = wpbdp_get_option( 'currency-symbol' );
            $symbol_position = wpbdp_get_option( 'currency-symbol-position', 'left' );

			$currency = array(
				'name'               => '',
				'symbol_left'        => $symbol_position === 'left' ? $currency_symbol : '',
				'symbol_right'       => $symbol_position === 'right' ? $currency_symbol : '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
				'code'               => wpbdp_get_option( 'currency-code' ),
			);
		}

		/**
		 * Allow custom code to change the currency for different currencies per form.
		 *
		 * @since 6.2.7
		 * @param array $currency  The currency information.
		 */
		$currency = apply_filters( 'wpbdp_currency', $currency );

		return $currency;
	}

	/**
	 * Format the currencies for the options dropdown.
	 *
	 * @return array
	 */
	public static function list_currencies() {
		$currencies = self::get_currencies();
		$list       = array();
		foreach ( $currencies as $code => $currency ) {
			$list[ $code ] = $currency['name'];
		}
		return $list;
	}

	/**
	 * @since 6.2.7
	 */
	public static function get_currencies( $currency = false ) {
		$currencies = array(
			'AUD' => array(
				'name' => __( 'Australian Dollar', 'business-directory-plugin' ),
				'symbol_left'        => '$',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'BDT' => array(
				'name' => __( 'Bangladeshi Taka', 'business-directory-plugin' ),
				'symbol_left'        => '৳',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'BRL' => array(
				'name' => __( 'Brazilian Real', 'business-directory-plugin' ),
				'symbol_left' => 'R$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'CAD' => array(
				'name' => __( 'Canadian Dollar', 'business-directory-plugin' ),
				'symbol_left' => '$',
				'symbol_right' => 'CAD',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'CNY' => array(
				'name'               => __( 'Chinese Renminbi Yuan', 'business-directory-plugin' ),
				'symbol_left'        => '¥',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'CZK' => array(
				'name' => __( 'Czech Koruna', 'business-directory-plugin' ),
				'symbol_left' => '',
				'symbol_right' => '&#75;&#269;',
				'symbol_padding' => ' ',
				'thousand_separator' => ' ',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'DKK' => array(
				'name' => __( 'Danish Krone', 'business-directory-plugin' ),
				'symbol_left' => 'Kr',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'EUR' => array(
				'name' => __( 'Euro', 'business-directory-plugin' ),
				'symbol_left' => '',
				'symbol_right' => '&#8364;',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'HKD' => array(
				'name' => __( 'Hong Kong Dollar', 'business-directory-plugin' ),
				'symbol_left' => 'HK$',
				'symbol_right' => '',
				'symbol_padding' => '',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'HUF' => array(
				'name' => __( 'Hungarian Forint', 'business-directory-plugin' ),
				'symbol_left' => '',
				'symbol_right' => 'Ft',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'INR' => array(
				'name'               => __( 'Indian Rupee', 'business-directory-plugin' ),
				'symbol_left'        => '&#8377;',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'ILS' => array(
				'name' => __( 'Israeli New Sheqel', 'business-directory-plugin' ),
				'symbol_left' => '&#8362;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'JPY' => array(
				'name' => __( 'Japanese Yen', 'business-directory-plugin' ),
				'symbol_left' => '&#165;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '',
				'decimals' => 0,
			),
			'MYR' => array(
				'name' => __( 'Malaysian Ringgit', 'business-directory-plugin' ),
				'symbol_left' => '&#82;&#77;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'MXN' => array(
				'name' => __( 'Mexican Peso', 'business-directory-plugin' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'MAD' => array(
				'name'               => __( 'Moroccan Dirham', 'business-directory-plugin' ),
				'symbol_left'        => '',
				'symbol_right'       => '.د.م.',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'NOK' => array(
				'name' => __( 'Norwegian Krone', 'business-directory-plugin' ),
				'symbol_left' => 'Kr',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'NZD' => array(
				'name' => __( 'New Zealand Dollar', 'business-directory-plugin' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'PKR' => array(
				'name'               => __( 'Pakistani Rupee', 'business-directory-plugin' ),
				'symbol_left'        => '₨',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => '',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'PHP' => array(
				'name' => __( 'Philippine Peso', 'business-directory-plugin' ),
				'symbol_left' => 'Php',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'PLN' => array(
				'name' => __( 'Polish Zloty', 'business-directory-plugin' ),
				'symbol_left' => '&#122;&#322;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'GBP' => array(
				'name' => __( 'Pound Sterling', 'business-directory-plugin' ),
				'symbol_left' => '&#163;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'SGD' => array(
				'name' => __( 'Singapore Dollar', 'business-directory-plugin' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'ZAR' => array(
				'name'               => __( 'South African Rand', 'business-directory-plugin' ),
				'symbol_left'        => 'R',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ' ',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'LKR' => array(
				'name'               => __( 'Sri Lankan Rupee', 'business-directory-plugin' ),
				'symbol_left'        => '₨',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => '',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'SEK' => array(
				'name' => __( 'Swedish Krona', 'business-directory-plugin' ),
				'symbol_left' => '',
				'symbol_right' => 'Kr',
				'symbol_padding' => ' ',
				'thousand_separator' => ' ',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'CHF' => array(
				'name' => __( 'Swiss Franc', 'business-directory-plugin' ),
				'symbol_left' => 'Fr.',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => "'",
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'TWD' => array(
				'name' => __( 'Taiwan New Dollar', 'business-directory-plugin' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'THB' => array(
				'name' => __( 'Thai Baht', 'business-directory-plugin' ),
				'symbol_left' => '&#3647;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'TRY' => array(
				'name' => __( 'Turkish Liras', 'business-directory-plugin' ),
				'symbol_left' => '',
				'symbol_right' => '&#8364;',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'AED' => array(
				'name'               => __( 'United Arab Emirates Dirham', 'business-directory-plugin' ),
				'symbol_left'        => '',
				'symbol_right'       => ' ',
				'symbol_padding'     => ' ',
				'thousand_separator' => '.',
				'decimal_separator'  => ',',
				'decimals'           => 2,
			),
			'USD' => array(
				'name' => __( 'U.S. Dollar', 'business-directory-plugin' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => '',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'UYU' => array(
				'name' => __( 'Uruguayan Peso', 'business-directory-plugin' ),
				'symbol_left' => '$U',
				'symbol_right' => '',
				'symbol_padding' => '',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 0,
			),
		);

		$currencies = apply_filters( 'wpdbp_currencies', $currencies );
		if ( $currency ) {
			$currency = strtoupper( $currency );
			if ( isset( $currencies[ $currency ] ) ) {
				$currencies = $currencies[ $currency ];
			}
		}

		return $currencies;
	}
}
