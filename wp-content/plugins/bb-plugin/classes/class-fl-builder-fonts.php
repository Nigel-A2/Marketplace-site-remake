<?php

/**
 * Helper class for font settings.
 *
 * @class   FLBuilderFonts
 * @since   1.6.3
 */
final class FLBuilderFonts {

	/**
	 * An array of fonts / weights.
	 * @var array
	 */
	static private $fonts = array();

	static private $enqueued_google_fonts_done = false;

	static $preload_fa5 = array();

	/**
	 * @since 1.9.5
	 * @return void
	 */
	static public function init() {
		add_filter( 'the_content', __CLASS__ . '::combine_google_fonts', 11 );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::combine_google_fonts', 10000 );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_google_fonts', 9999 );
		add_filter( 'wp_resource_hints', __CLASS__ . '::resource_hints', 10, 2 );
		add_action( 'wp_head', array( __CLASS__, 'preload' ), 5 );
	}

	static public function preload() {
		$fa_version = FLBuilder::get_fa5_version();
		$icons      = array(
			'foundation-icons' => array(
				'https://cdnjs.cloudflare.com/ajax/libs/foundicons/3.0.0/foundation-icons.woff',
			),
			'font-awesome-5'   => array(),
		);

		foreach ( array_unique( FLBuilderFonts::$preload_fa5 ) as $type ) {
			switch ( $type ) {
				case 'fas':
					$icons['font-awesome-5'][] = FL_BUILDER_URL . 'fonts/fontawesome/' . $fa_version . '/webfonts/fa-solid-900.woff2';
					break;
				case 'far':
					$icons['font-awesome-5'][] = FL_BUILDER_URL . 'fonts/fontawesome/' . $fa_version . '/webfonts/fa-regular-400.woff2';
					break;
				case 'fab':
					$icons['font-awesome-5'][] = FL_BUILDER_URL . 'fonts/fontawesome/' . $fa_version . '/webfonts/fa-brands-400.woff2';
					break;
			}
		}

		// if using pro cdn do not preload as we have no idea what the url will be.
		if ( get_option( '_fl_builder_enable_fa_pro', false ) || apply_filters( 'fl_enable_fa5_pro', false ) || empty( $icons['font-awesome-5'] ) ) {
			unset( $icons['font-awesome-5'] );
		}

		foreach ( $icons as $key => $preloads ) {
			if ( wp_style_is( $key, 'enqueued' ) ) {
				foreach ( $preloads as $url ) {
					printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin="anonymous">' . "\n", $url );
				}
			}
		}
	}

	/**
	 * Renders the JavasCript variable for font settings dropdowns.
	 *
	 * @since  1.6.3
	 * @return void
	 */
	static public function js() {
		/**
		 * @see fl_builder_font_families_default
		 */
		$default = json_encode( apply_filters( 'fl_builder_font_families_default', FLBuilderFontFamilies::$default ) );
		/**
		 * @see fl_builder_font_families_system
		 */
		$system = json_encode( apply_filters( 'fl_builder_font_families_system', FLBuilderFontFamilies::$system ) );
		/**
		 * @see fl_builder_font_families_google
		 */
		$google = json_encode( apply_filters( 'fl_builder_font_families_google', self::prepare_google_fonts( FLBuilderFontFamilies::google() ) ) );

		echo 'var FLBuilderFontFamilies = { default: ' . $default . ', system: ' . $system . ', google: ' . $google . ' };';
	}

	static public function prepare_google_fonts( $fonts ) {
		foreach ( $fonts as $family => $variants ) {
			foreach ( $variants as $k => $variant ) {
				if ( 'italic' == $variant || 'i' == substr( $variant, -1 ) ) {
					unset( $fonts[ $family ][ $k ] );
				}
			}
		}
		return $fonts;
	}

	/**
	 * Renders a list of all available fonts.
	 *
	 * @since  1.6.3
	 * @param  string $font The current selected font.
	 * @return void
	 */
	static public function display_select_font( $font ) {
		$system_fonts = apply_filters( 'fl_builder_font_families_system', FLBuilderFontFamilies::$system );
		$google_fonts = apply_filters( 'fl_builder_font_families_google', FLBuilderFontFamilies::google() );
		$recent_fonts = get_option( 'fl_builder_recent_fonts', array() );

		echo '<option value="Default" ' . selected( 'Default', $font, false ) . '>' . __( 'Default', 'fl-builder' ) . '</option>';

		if ( is_array( $recent_fonts ) && ! empty( $recent_fonts ) ) {
			echo '<optgroup label="Recently Used" class="recent-fonts">';
			foreach ( $recent_fonts as $name => $variants ) {
				if ( 'Default' == $name ) {
					continue;
				}
				echo '<option value="' . $name . '">' . $name . '</option>';
			}
		}

		echo '<optgroup label="System">';

		foreach ( $system_fonts as $name => $variants ) {
			echo '<option value="' . $name . '" ' . selected( $name, $font, false ) . '>' . $name . '</option>';
		}

		echo '<optgroup label="Google">';

		foreach ( $google_fonts as $name => $variants ) {
			echo '<option value="' . $name . '" ' . selected( $name, $font, false ) . '>' . $name . '</option>';
		}
	}

	/**
	 * Renders a list of all available weights for a selected font.
	 *
	 * @since  1.6.3
	 * @param  string $font   The current selected font.
	 * @param  string $weight The current selected weight.
	 * @return void
	 */
	static public function display_select_weight( $font, $weight ) {
		if ( 'Default' == $font ) {
			echo '<option value="default" selected="selected">' . __( 'Default', 'fl-builder' ) . '</option>';
		} else {
			$system_fonts = apply_filters( 'fl_builder_font_families_system', FLBuilderFontFamilies::$system );
			$google_fonts = apply_filters( 'fl_builder_font_families_google', FLBuilderFontFamilies::google() );

			if ( array_key_exists( $font, $system_fonts ) ) {
				foreach ( $system_fonts[ $font ]['weights'] as $variant ) {
					echo '<option value="' . $variant . '" ' . selected( $variant, $weight, false ) . '>' . FLBuilderFonts::get_weight_string( $variant ) . '</option>';
				}
			} else {
				foreach ( $google_fonts[ $font ] as $variant ) {
					echo '<option value="' . $variant . '" ' . selected( $variant, $weight, false ) . '>' . FLBuilderFonts::get_weight_string( $variant ) . '</option>';
				}
			}
		}

	}

	/**
	 * Returns a font weight name for a respective weight.
	 *
	 * @since  1.6.3
	 * @param  string $weight The selected weight.
	 * @return string         The weight name.
	 */
	static public function get_weight_string( $weight ) {

		$weight_string = self::get_font_weight_strings();

		return $weight_string[ $weight ];
	}

	/**
	 * Return font weight strings.
	 */
	static public function get_font_weight_strings() {
		/**
		 * Array of font weights
		 * @see fl_builder_font_weight_strings
		 */
		return apply_filters( 'fl_builder_font_weight_strings', array(
			'default'   => __( 'Default', 'fl-builder' ),
			'regular'   => __( 'Regular', 'fl-builder' ),
			'italic'    => __( 'Italic', 'fl-builder' ),
			'100'       => __( 'Thin', 'fl-builder' ),
			'100i'      => __( 'Thin Italic', 'fl-builder' ),
			'100italic' => __( 'Thin Italic', 'fl-builder' ),
			'200'       => __( 'Extra-Light', 'fl-builder' ),
			'200i'      => __( 'Extra-Light Italic', 'fl-builder' ),
			'200italic' => __( 'Extra-Light Italic', 'fl-builder' ),
			'300'       => __( 'Light', 'fl-builder' ),
			'300i'      => __( 'Light Italic', 'fl-builder' ),
			'300italic' => __( 'Light Italic', 'fl-builder' ),
			'400'       => __( 'Normal', 'fl-builder' ),
			'400i'      => __( 'Normal Italic', 'fl-builder' ),
			'400italic' => __( 'Normal Italic', 'fl-builder' ),
			'500'       => __( 'Medium', 'fl-builder' ),
			'500i'      => __( 'Medium Italic', 'fl-builder' ),
			'500italic' => __( 'Medium Italic', 'fl-builder' ),
			'600'       => __( 'Semi-Bold', 'fl-builder' ),
			'600i'      => __( 'Semi-Bold Italic', 'fl-builder' ),
			'600italic' => __( 'Semi-Bold Italic', 'fl-builder' ),
			'700'       => __( 'Bold', 'fl-builder' ),
			'700i'      => __( 'Bold Italic', 'fl-builder' ),
			'700italic' => __( 'Bold Italic', 'fl-builder' ),
			'800'       => __( 'Extra-Bold', 'fl-builder' ),
			'800i'      => __( 'Extra-Bold Italic', 'fl-builder' ),
			'800italic' => __( 'Extra-Bold Italic', 'fl-builder' ),
			'900'       => __( 'Ultra-Bold', 'fl-builder' ),
			'900i'      => __( 'Ultra-Bold Italic', 'fl-builder' ),
			'900italic' => __( 'Ultra-Bold Italic', 'fl-builder' ),
		) );
	}

	/**
	 * Helper function to render css styles for a selected font.
	 *
	 * @since  1.6.3
	 * @param  array $font An array with font-family and weight.
	 * @return void
	 */
	static public function font_css( $font ) {

		$system_fonts = apply_filters( 'fl_builder_font_families_system', FLBuilderFontFamilies::$system );
		$google       = FLBuilderFontFamilies::get_google_fallback( $font['family'] );

		$css = '';

		if ( array_key_exists( $font['family'], $system_fonts ) ) {

			$css .= 'font-family: "' . $font['family'] . '",' . $system_fonts[ $font['family'] ]['fallback'] . ';';

		} elseif ( $google ) {
			$css .= 'font-family: "' . $font['family'] . '", ' . $google . ';';
		} else {
			$css .= 'font-family: "' . $font['family'] . '", sans-serif;';
		}

		if ( 'regular' == $font['weight'] ) {
			$css .= 'font-weight: normal;';
		} else {
			if ( 'i' == substr( $font['weight'], -1 ) ) {
				$css .= 'font-weight: ' . substr( $font['weight'], 0, -1 ) . ';';
				$css .= 'font-style: italic;';
			} elseif ( 'italic' == $font['weight'] ) {
				$css .= 'font-style: italic;';
			} else {
				$css .= 'font-weight: ' . $font['weight'] . ';';
			}
		}

		echo $css;
	}

	/**
	 * Add fonts to the $font array for a module.
	 *
	 * @since  1.6.3
	 * @param  object $module The respective module.
	 * @return void
	 */
	static public function add_fonts_for_module( $module ) {
		$fields = FLBuilderModel::get_settings_form_fields( $module->form );

		// needed for italics.
		$google = FLBuilderFontFamilies::google();
		$bold   = false;

		foreach ( $fields as $name => $field ) {

			if ( 'font' == $field['type'] && isset( $module->settings->$name ) ) {
				self::add_font( $module->settings->$name );
			} elseif ( 'typography' == $field['type'] && ! empty( $module->settings->$name ) && isset( $module->settings->{ $name }['font_family'] ) ) {
				$fname  = $module->settings->{ $name }['font_family'];
				$weight = isset( $module->settings->{ $name }['font_weight'] ) && '' !== $module->settings->{ $name }['font_weight'] ? $module->settings->{ $name }['font_weight'] : '400';
				// handle google italics.
				if ( isset( $google[ $fname ] ) ) {
					$selected_weight = $weight;
					$italic          = ( isset( $module->settings->{ $name }['font_style'] ) ) ? $module->settings->{ $name }['font_style'] : '';
					if ( ! $italic && count( $google[ $fname ] ) === 1 && 'italic' === $google[ $fname ][0] ) {
						$italic = 'italic';
					}
					if ( in_array( $selected_weight . 'i', $google[ $fname ] ) && 'italic' == $italic ) {
						$weight = $selected_weight . 'i';
					}
					if ( ( '400' == $selected_weight || 'regular' == $selected_weight ) && 'italic' == $italic && in_array( 'italic', $google[ $fname ] ) ) {
						$weight = '400i';
					}
				}

				if ( 'Molle' === $module->settings->{ $name }['font_family'] ) {
					$weight = 'i';
				}

				if ( $module instanceof FLRichTextModule ) {
					$bold = true;
				}
				self::add_font( array(
					'family' => $module->settings->{ $name }['font_family'],
					'weight' => $weight,
				), $bold );
			} elseif ( isset( $field['form'] ) ) {
				$form = FLBuilderModel::$settings_forms[ $field['form'] ];
				self::add_fonts_for_nested_module_form( $module, $form['tabs'], $name );
			}
		}
	}

	/**
	 * Add fonts to the $font array for a nested module form.
	 *
	 * @since 1.8.6
	 * @access private
	 * @param object $module The module to add for.
	 * @param array $form The nested form.
	 * @param string $setting The nested form setting key.
	 * @return void
	 */
	static private function add_fonts_for_nested_module_form( $module, $form, $setting ) {
		$fields = FLBuilderModel::get_settings_form_fields( $form );

		foreach ( $fields as $name => $field ) {
			if ( 'font' == $field['type'] && isset( $module->settings->$setting ) ) {
				foreach ( $module->settings->$setting as $key => $val ) {
					if ( isset( $val->$name ) ) {
						self::add_font( (array) $val->$name );
					} elseif ( $name == $key && ! empty( $val ) ) {
						self::add_font( (array) $val );
					}
				}
			}
		}
	}

	/**
	 * Enqueue the stylesheet for fonts.
	 *
	 * @since  1.6.3
	 * @return void
	 */
	static public function enqueue_styles() {
		return false;
	}

	/**
	 * @since 2.1.3
	 */
	static public function enqueue_google_fonts() {
		/**
		 * Google fonts domain
		 * @see fl_builder_google_fonts_domain
		 */
		$google_fonts_domain = apply_filters( 'fl_builder_google_fonts_domain', '//fonts.googleapis.com/' );
		$google_url          = $google_fonts_domain . 'css?family=';

		/**
		 * Allow users to control what fonts are enqueued by modules.
		 * Returning array() will disable all enqueues.
		 * @see fl_builder_google_fonts_pre_enqueue
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/how-to-tips/load-google-fonts-locally-gdpr
		 */
		if ( count( apply_filters( 'fl_builder_google_fonts_pre_enqueue', self::$fonts ) ) > 0 ) {

			foreach ( self::$fonts as $family => $weights ) {
				$google_url .= $family . ':' . implode( ',', $weights ) . '|';
			}

			$google_url = substr( $google_url, 0, -1 );

			wp_enqueue_style( 'fl-builder-google-fonts-' . md5( $google_url ), $google_url, array() );

			self::$fonts = array();
		}
	}

	/**
	 * Adds data to the $fonts array for a font to be rendered.
	 *
	 * @since  1.6.3
	 * @param  array $font an array with the font family and weight to add.
	 * @return void
	 */
	static public function add_font( $font, $bold = false ) {

		$recent_fonts_db = get_option( 'fl_builder_recent_fonts', array() );
		$recent_fonts    = array();

		if ( is_array( $font ) && isset( $font['family'] ) && isset( $font['weight'] ) && 'Default' != $font['family'] ) {

			$system_fonts = apply_filters( 'fl_builder_font_families_system', FLBuilderFontFamilies::$system );

			// check if is a Google Font
			if ( ! array_key_exists( $font['family'], $system_fonts ) ) {

				// check if font family is already added
				if ( array_key_exists( $font['family'], self::$fonts ) ) {

					// check if the weight is already added
					if ( ! in_array( $font['weight'], self::$fonts[ $font['family'] ] ) ) {
						self::$fonts[ $font['family'] ][] = $font['weight'];
					}
				} else {
					// adds a new font and weight
					self::$fonts[ $font['family'] ] = array( $font['weight'] );

				}
				if ( $bold ) {
					self::$fonts[ $font['family'] ][] = ( strstr( $font['weight'], 'i' ) ) ? '700i' : '700';
				}
				self::$fonts[ $font['family'] ] = array_unique( self::$fonts[ $font['family'] ] );
			}
			if ( ! isset( $recent_fonts_db[ $font['family'] ] ) ) {
				$recent_fonts[ $font['family'] ] = $font['weight'];
			}
		}

		$recent = array_merge( (array) $recent_fonts, (array) $recent_fonts_db );

		if ( isset( $_GET['fl_builder'] ) && ! empty( $recent ) && serialize( $recent ) !== serialize( $recent_fonts_db ) ) {
			FLBuilderUtils::update_option( 'fl_builder_recent_fonts', array_slice( $recent, -11 ) );
		}

	}

	/**
	 * Combines all enqueued google font HTTP calls into one URL.
	 *
	 * @since  1.9.5
	 * @return void
	 */
	static public function combine_google_fonts( $content = false ) {
		global $wp_styles;

		// Check for any enqueued `fonts.googleapis.com` from BB theme or plugin
		if ( isset( $wp_styles->queue ) ) {

			/**
			 * @see fl_builder_combine_google_fonts_domain
			 */
			$google_fonts_domain   = apply_filters( 'fl_builder_combine_google_fonts_domain', '//fonts.googleapis.com/css' );
			$enqueued_google_fonts = array();
			$families              = array();
			$subsets               = array();
			$font_args             = array();

			// Collect all enqueued google fonts
			foreach ( $wp_styles->queue as $key => $handle ) {

				if ( ! isset( $wp_styles->registered[ $handle ] ) || strpos( $handle, 'fl-builder-google-fonts-' ) === false ) {
					continue;
				}

				$style_src = $wp_styles->registered[ $handle ]->src;

				if ( strpos( $style_src, 'fonts.googleapis.com/css' ) !== false ) {
					$url = wp_parse_url( $style_src );

					if ( is_string( $url['query'] ) ) {
						parse_str( $url['query'], $parsed_url );

						if ( isset( $parsed_url['family'] ) ) {

							// Collect all subsets
							if ( isset( $parsed_url['subset'] ) ) {
								$subsets[] = urlencode( trim( $parsed_url['subset'] ) );
							}

							$font_families = explode( '|', $parsed_url['family'] );
							foreach ( $font_families as $parsed_font ) {

								$get_font = explode( ':', $parsed_font );

								// Extract the font data
								if ( isset( $get_font[0] ) && ! empty( $get_font[0] ) ) {
									$family  = $get_font[0];
									$weights = isset( $get_font[1] ) && ! empty( $get_font[1] ) ? explode( ',', $get_font[1] ) : array();

									// Combine weights if family has been enqueued
									if ( isset( $enqueued_google_fonts[ $family ] ) && $weights != $enqueued_google_fonts[ $family ]['weights'] ) {
										$combined_weights                            = array_merge( $weights, $enqueued_google_fonts[ $family ]['weights'] );
										$enqueued_google_fonts[ $family ]['weights'] = array_unique( $combined_weights );
									} else {
										$enqueued_google_fonts[ $family ] = array(
											'handle'  => $handle,
											'family'  => $family,
											'weights' => $weights,
										);

									}
									// Remove enqueued google font style, so we would only have one HTTP request.
									wp_dequeue_style( $handle );
								}
							}
						}
					}
				}
			}

			// Start combining all enqueued google fonts
			if ( count( $enqueued_google_fonts ) > 0 ) {

				foreach ( $enqueued_google_fonts as $family => $data ) {
					// Collect all family and weights
					if ( ! empty( $data['weights'] ) ) {
						$families[] = $family . ':' . implode( ',', $data['weights'] );
					} else {
						$families[] = $family;
					}
				}

				if ( ! empty( $families ) ) {
					$font_args['family'] = implode( '|', $families );

					if ( ! empty( $subsets ) ) {
						$font_args['subset'] = implode( ',', $subsets );
					}

					/**
					 * Array of extra args passed to google fonts.
					 * @see fl_builder_google_font_args
					 */
					$font_args = apply_filters( 'fl_builder_google_font_args', $font_args );

					$src = add_query_arg( $font_args, $google_fonts_domain );

					// Enqueue google fonts into one URL request
					wp_enqueue_style(
						'fl-builder-google-fonts-' . md5( $src ),
						$src,
						array()
					);
					self::$enqueued_google_fonts_done = true;
					// Clears data
					$enqueued_google_fonts = array();
				}
			}
		}
		return $content;
	}

	/**
	 * Preconnect to fonts.gstatic.com to speed up google fonts.
	 * @since 2.1.5
	 */
	static public function resource_hints( $urls, $relation_type ) {
		if ( true == self::$enqueued_google_fonts_done && 'preconnect' === $relation_type ) {
			$urls[] = array(
				'href' => 'https://fonts.gstatic.com',
				'crossorigin',
			);
		}
		return $urls;
	}

	/**
	 * Find font fallback, used by FLBuilderCSS
	 * @since 2.2
	 */
	static public function get_font_fallback( $font_family ) {
		$fallback = 'sans-serif';
		$default  = apply_filters( 'fl_builder_font_families_default', FLBuilderFontFamilies::$default );
		$system   = apply_filters( 'fl_builder_font_families_system', FLBuilderFontFamilies::$system );
		$google   = apply_filters( 'fl_builder_font_families_google', FLBuilderFontFamilies::google() );
		foreach ( $default as $font => $data ) {
			if ( $font_family == $font && isset( $data['fallback'] ) ) {
				$fallback = $data['fallback'];
			}
		}
		foreach ( $system as $font => $data ) {
			if ( $font_family == $font && isset( $data['fallback'] ) ) {
				$fallback = $data['fallback'];
			}
		}
		foreach ( $google as $font => $data ) {
			if ( $font_family == $font ) {
				$fallback = FLBuilderFontFamilies::get_google_fallback( $font );
			}
		}
		return $fallback;
	}

}

FLBuilderFonts::init();

/**
 * Font info class for system and Google fonts.
 *
 * @class FLFontFamilies
 * @since 1.6.3
 */
final class FLBuilderFontFamilies {

	/**
	 * Cache for google fonts
	 */
	static private $_google_json  = array();
	static private $_google_fonts = false;
	static private $_google_run   = 0;

	/**
	 * Array with a list of default font weights.
	 * @var array
	 */
	static public $default = array(
		'Default' => array(
			'default',
			'100',
			'200',
			'300',
			'400',
			'500',
			'600',
			'700',
			'800',
			'900',
		),
	);

	/**
	 * Array with a list of system fonts.
	 * @var array
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
	);

	/**
	 * Parse fonts.json to get all possible Google fonts.
	 * @since 1.10.7
	 * @return array
	 */
	static function google() {

		if ( false !== self::$_google_fonts ) {
			return self::$_google_fonts;
		}

		$fonts = array();
		$json  = self::_get_json();

		foreach ( $json as $k => $font ) {

			$name = key( $font );

			foreach ( $font[ $name ]['variants'] as $key => $variant ) {
				if ( 'italic' !== $variant ) {
					if ( stristr( $variant, 'italic' ) ) {
						$font[ $name ]['variants'][ $key ] = str_replace( 'talic', '', $variant );
					}
				}
				if ( 'regular' == $variant ) {
					$font[ $name ]['variants'][ $key ] = '400';
				}
			}
			$fonts[ $name ] = $font[ $name ]['variants'];
		}
		// only cache after 1st run to save rams.
		if ( self::$_google_run > 0 ) {
			self::$_google_fonts = $fonts;
		}
		self::$_google_run++;
		return $fonts;
	}

	/**
	 * @since 2.1.5
	 */
	static private function _get_json() {
		if ( ! empty( self::$_google_json ) ) {
			$json = self::$_google_json;
		} else {
			$json               = (array) json_decode( file_get_contents( trailingslashit( FL_BUILDER_DIR ) . 'json/fonts.json' ), true );
			self::$_google_json = $json;
		}
		/**
		 * Filter raw google json data
		 * @see fl_builder_get_google_json
		 */
		return apply_filters( 'fl_builder_get_google_json', $json );
	}


	/**
	 * @since 2.1.5
	 */
	static public function get_google_fallback( $font ) {
		$json = self::_get_json();
		foreach ( $json as $k => $google ) {
			$name = key( $google );
			if ( $name == $font ) {
				return $google[ $name ]['fallback'];
			}
		}
		return false;
	}
}
