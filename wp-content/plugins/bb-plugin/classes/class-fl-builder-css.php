<?php

/**
 * Helper class for outputting CSS.
 *
 * @since 2.2
 */
final class FLBuilderCSS {

	/**
	 * An array of rule arg arrays that is used
	 * and cleared when the render method is called.
	 *
	 * @since 2.2
	 * @var array $rules
	 */
	static protected $rules = array();

	/**
	 * Adds a rule config array.
	 *
	 * @since 2.2
	 * @param array $rules
	 * @return void
	 */
	static public function rule( $args = array() ) {
		self::$rules[] = $args;
	}

	/**
	 * Adds rule config arrays for responsive settings.
	 *
	 * @since 2.2
	 * @param array $args
	 * @return void
	 */
	static public function responsive_rule( $args = array() ) {
		$global_settings   = FLBuilderModel::get_global_settings();
		$default_args      = array(
			'settings'          => null,
			'setting_name'      => '',
			'setting_base_name' => '',
			'selector'          => '',
			'prop'              => '',
			'props'             => array(),
			'unit'              => '',
			'enabled'           => true,
			'ignore'            => array(),
		);
		$args              = wp_parse_args( $args, $default_args );
		$settings          = $args['settings'];
		$setting_name      = $args['setting_name'];
		$setting_base_name = $args['setting_base_name'];
		$selector          = $args['selector'];
		$prop              = $args['prop'];
		$props             = $args['props'];
		$default_unit      = $args['unit'];
		$enabled           = $args['enabled'];
		$breakpoints       = array( '', 'medium', 'responsive' );
		$ignore            = $args['ignore'];

		if ( ! $settings || empty( $setting_name ) || empty( $selector ) ) {
			return;
		}

		foreach ( $breakpoints as $breakpoint ) {

			if ( ! empty( $breakpoint ) && ! $global_settings->responsive_enabled ) {
				continue;
			}

			$suffix    = empty( $breakpoint ) ? '' : "_{$breakpoint}";
			$name      = $setting_name . $suffix;
			$base_name = empty( $setting_base_name ) ? $name : $setting_base_name . $suffix;
			$setting   = isset( $settings->{$name} ) ? $settings->{$name} : null;

			if ( null === $setting ) {
				continue;
			}

			if ( $enabled && ! in_array( $setting, $ignore ) ) {

				if ( ! empty( $prop ) ) {
					$props[ $prop ] = array(
						'value' => $setting,
						'unit'  => FLBuilderCSS::get_unit( $base_name, $settings, $default_unit ),
					);
				}

				self::$rules[] = array(
					'media'    => $breakpoint,
					'selector' => $selector,
					'props'    => $props,
				);
			}
		}
	}

	/**
	 * Adds a responsive rule config array for a dimension field.
	 *
	 * @since 2.2
	 * @param array $args
	 * @return void
	 */
	static public function dimension_field_rule( $args = array() ) {
		$args              = wp_parse_args( $args, array(
			'settings'     => null,
			'setting_name' => '',
			'selector'     => '',
			'props'        => array(),
			'unit'         => '',
		) );
		$settings          = $args['settings'];
		$setting_base_name = $args['setting_name'];
		$selector          = $args['selector'];
		$props             = $args['props'];
		$unit              = $args['unit'];

		if ( ! $settings || empty( $setting_base_name ) || empty( $selector ) ) {
			return;
		}

		foreach ( $props as $prop => $settings_name ) {
			$rules = self::responsive_rule( array(
				'settings'          => $settings,
				'setting_name'      => $settings_name,
				'setting_base_name' => $setting_base_name,
				'selector'          => $selector,
				'prop'              => $prop,
				'unit'              => $unit,
			) );

			if ( ! empty( $rules ) ) {
				self::$rules = array_merge( self::$rules, $rules );
			}
		}
	}

	/**
	 * Adds a responsive rule config array for a compound field.
	 *
	 * @since 2.2
	 * @param array $args
	 * @return void
	 */
	static public function compound_field_rule( $args = array() ) {
		$global_settings = FLBuilderModel::get_global_settings();
		$args            = wp_parse_args( $args, array(
			'type'         => '',
			'selector'     => '',
			'settings'     => null,
			'setting_name' => '',
		) );
		$type            = $args['type'];
		$selector        = $args['selector'];
		$settings        = $args['settings'];
		$setting_name    = $args['setting_name'];
		$breakpoints     = array( '', 'medium', 'responsive' );

		if ( empty( $type ) || empty( $selector ) || ! $settings || empty( $setting_name ) ) {
			return;
		}

		foreach ( $breakpoints as $breakpoint ) {

			if ( ! empty( $breakpoint ) && ! $global_settings->responsive_enabled ) {
				continue;
			}

			$name     = empty( $breakpoint ) ? $setting_name : "{$setting_name}_{$breakpoint}";
			$setting  = isset( $settings->{$name} ) ? $settings->{$name} : null;
			$callback = "{$type}_field_props";
			$props    = array();

			// Settings must be an array. Settings in nested forms can become objects when encoded.
			if ( is_object( $setting ) ) {
				$setting = (array) $setting;
				foreach ( $setting as $key => $value ) {
					if ( is_object( $value ) ) {
						$setting[ $key ] = (array) $value;
					}
				}
			}

			if ( ! is_array( $setting ) ) {
				continue;
			}
			if ( method_exists( __CLASS__, $callback ) ) {
				$props = call_user_func( array( __CLASS__, $callback ), $setting );
			}

			self::$rules[] = array(
				'media'    => $breakpoint,
				'selector' => $selector,
				'props'    => $props,
			);
		}
	}

	/**
	 * Adds a responsive rule config array for a border field.
	 *
	 * @since 2.2
	 * @param array $args
	 * @return void
	 */
	static public function border_field_rule( $args = array() ) {
		$args['type'] = 'border';
		self::compound_field_rule( $args );
	}

	/**
	 * Returns a property config array for a border field.
	 *
	 * @since 2.2
	 * @param array $setting
	 * @return array
	 */
	static public function border_field_props( $setting = array() ) {
		$props = array();

		if ( isset( $setting['style'] ) && ! empty( $setting['style'] ) ) {
			$props['border-style']    = $setting['style'];
			$props['border-width']    = '0'; // Default to zero.
			$props['background-clip'] = 'border-box';
		}
		if ( isset( $setting['color'] ) && ! empty( $setting['color'] ) ) {
			$props['border-color'] = $setting['color'];
		}
		if ( isset( $setting['width'] ) && is_array( $setting['width'] ) ) {
			if ( '' !== $setting['width']['top'] ) {
				$props['border-top-width'] = $setting['width']['top'] . 'px';
			}
			if ( '' !== $setting['width']['right'] ) {
				$props['border-right-width'] = $setting['width']['right'] . 'px';
			}
			if ( '' !== $setting['width']['bottom'] ) {
				$props['border-bottom-width'] = $setting['width']['bottom'] . 'px';
			}
			if ( '' !== $setting['width']['left'] ) {
				$props['border-left-width'] = $setting['width']['left'] . 'px';
			}
		}
		if ( isset( $setting['radius'] ) && is_array( $setting['radius'] ) ) {
			if ( isset( $setting['radius']['top_left'] ) && '' !== $setting['radius']['top_left'] ) {
				$props['border-top-left-radius'] = $setting['radius']['top_left'] . 'px';
			}
			if ( '' !== $setting['radius']['top_right'] ) {
				$props['border-top-right-radius'] = $setting['radius']['top_right'] . 'px';
			}
			if ( isset( $setting['radius']['bottom_left'] ) && '' !== $setting['radius']['bottom_left'] ) {
				$props['border-bottom-left-radius'] = $setting['radius']['bottom_left'] . 'px';
			}
			if ( isset( $setting['radius']['bottom_right'] ) && '' !== $setting['radius']['bottom_right'] ) {
				$props['border-bottom-right-radius'] = $setting['radius']['bottom_right'] . 'px';
			}
		}
		if ( isset( $setting['shadow'] ) && is_array( $setting['shadow'] ) ) {
			$props['box-shadow'] = FLBuilderColor::shadow( $setting['shadow'] );
		}

		return $props;
	}

	/**
	 * Adds a responsive rule config array for a typography field.
	 *
	 * @since 2.2
	 * @param array $args
	 * @return void
	 */
	static public function typography_field_rule( $args = array() ) {
		$args['type'] = 'typography';
		self::compound_field_rule( $args );
	}

	/**
	 * Returns a property config array for a typography field.
	 *
	 * @since 2.2
	 * @param array $setting
	 * @return array
	 */
	static public function typography_field_props( $setting = array() ) {
		$props    = array();
		$settings = FLBuilderModel::get_global_settings();
		$pattern  = '%s, %s';
		if ( isset( $setting['font_family'] ) && 'Default' !== $setting['font_family'] ) {
			$fallback = FLBuilderFonts::get_font_fallback( $setting['font_family'] );
			if ( preg_match( '#[0-9\s]#', $setting['font_family'] ) ) {
				$pattern = '"%s", %s';
			}
			$props['font-family'] = sprintf( $pattern, $setting['font_family'], $fallback );
		}
		if ( isset( $setting['font_weight'] ) && 'i' == substr( $setting['font_weight'], -1 ) ) {
			$props['font-weight'] = substr( $setting['font_weight'], 0, -1 );
			$props['font-style']  = 'italic';
		}
		if ( isset( $setting['font_weight'] ) && 'default' !== $setting['font_weight'] && 'italic' !== $setting['font_weight'] ) {
			$props['font-weight'] = $setting['font_weight'];
		}
		if ( isset( $setting['font_size'] ) && ! empty( $setting['font_size']['length'] ) ) {
			if ( 'vw' == $setting['font_size']['unit'] && isset( $settings->responsive_base_fontsize ) ) {
				$props['font-size'] = sprintf( 'calc(%spx + %svw)', $settings->responsive_base_fontsize, $setting['font_size']['length'] );
			} else {
				$props['font-size'] = $setting['font_size']['length'] . $setting['font_size']['unit'];
			}
		}
		if ( isset( $setting['line_height'] ) && ! empty( $setting['line_height']['length'] ) && is_numeric( $setting['line_height']['length'] ) ) {
			$props['line-height'] = $setting['line_height']['length'];
			if ( isset( $setting['line_height']['unit'] ) && ! empty( $setting['line_height']['unit'] ) ) {
				$props['line-height'] .= $setting['line_height']['unit'];
			}
		}
		if ( isset( $setting['letter_spacing'] ) && ! empty( $setting['letter_spacing']['length'] ) ) {
			$props['letter-spacing'] = $setting['letter_spacing']['length'] . 'px';
		}
		if ( isset( $setting['text_align'] ) ) {
			$props['text-align'] = $setting['text_align'];
		}
		if ( isset( $setting['text_transform'] ) ) {
			$props['text-transform'] = $setting['text_transform'];
		}
		if ( isset( $setting['text_decoration'] ) ) {
			$props['text-decoration'] = $setting['text_decoration'];
		}
		if ( isset( $setting['font_style'] ) ) {
			$props['font-style'] = $setting['font_style'];
		}
		if ( isset( $setting['font_variant'] ) ) {
			$props['font-variant'] = $setting['font_variant'];
		}
		if ( isset( $setting['text_shadow'] ) ) {
			$props['text-shadow'] = FLBuilderColor::shadow( $setting['text_shadow'] );
		}

		return $props;
	}

	/**
	 * Renders the CSS for all of the rules that have
	 * been added and resets the $rules array.
	 *
	 * @since 2.2
	 * @return void
	 */
	static public function render() {
		$rendered    = array();
		$breakpoints = array( 'default', 'medium', 'responsive' );
		$css         = '';

		// Setup system breakpoints here to ensure proper order.
		foreach ( $breakpoints as $breakpoint ) {
			$media              = self::media_value( $breakpoint );
			$rendered[ $media ] = array();
		}

		/**
		 * Filter all responsive css rules before css is rendered
		 * @see fl_builder_pre_render_css_rules
		 */
		$rules = apply_filters( 'fl_builder_pre_render_css_rules', self::$rules );

		foreach ( $rules as $args ) {
			$defaults = array(
				'media'    => '',
				'selector' => '',
				'enabled'  => true,
				'props'    => array(),
			);

			$args     = array_merge( $defaults, $args );
			$media    = self::media_value( $args['media'] );
			$selector = $args['selector'];
			$props    = self::properties( $args['props'] );

			if ( ! $args['enabled'] || empty( $selector ) || empty( $props ) ) {
				continue;
			}

			if ( ! isset( $rendered[ $media ] ) ) {
				$rendered[ $media ] = array();
			}

			if ( ! isset( $rendered[ $media ][ $selector ] ) ) {
				$rendered[ $media ][ $selector ] = array();
			}

			$rendered[ $media ][ $selector ][] = $props;
		}

		foreach ( $rendered as $media => $selectors ) {

			if ( ! empty( $media ) && ! empty( $selectors ) ) {
				$css .= "@media($media) {\n";
				$tab  = "\t";
			} else {
				$tab = '';
			}

			foreach ( $selectors as $selector => $group ) {
				$css .= "$tab$selector {\n";
				foreach ( $group as $props ) {
					$css .= str_replace( "\t", "$tab\t", $props );
				}
				$css .= "$tab}\n";
			}

			if ( ! empty( $media ) && ! empty( $selectors ) ) {
				$css .= "}\n";
			}
		}

		self::$rules = array();

		echo $css;
	}

	/**
	 * Returns the property string for a rule block.
	 *
	 * @since 2.2
	 * @param array $props
	 * @return string
	 */
	static public function properties( $props ) {
		$css      = '';
		$defaults = array(
			'value'   => '',
			'unit'    => '',
			'enabled' => true,
		);

		foreach ( $props as $name => $args ) {

			if ( ! is_array( $args ) ) {
				$args = array(
					'value' => $args,
				);
			}

			$args  = array_merge( $defaults, $args );
			$value = $args['value'];
			$type  = self::property_type( $name );

			if ( '' === $value || ! $args['enabled'] ) {
				continue;
			}

			switch ( $type ) {

				case 'color':
					if ( strstr( $value, 'rgb' ) || strstr( $value, 'url' ) ) {
						$css .= "\t$name: $value;\n";
					} elseif ( 'inherit' === $value ) {
						$css .= "\t$name: inherit;\n";
					} elseif ( 'transparent' === $value ) {
						$css .= "\t$name: transparent;\n";
					} else {
						$css .= sprintf( "\t%s: #%s;\n", $name, ltrim( $value, '#' ) );
						if ( isset( $args['opacity'] ) && '' !== $args['opacity'] ) {
							$rgb  = implode( ',', FLBuilderColor::hex_to_rgb( $value ) );
							$a    = $args['opacity'] / 100;
							$css .= "\t$name: rgba($rgb,$a);\n";
						}
					}
					break;

				case 'image':
					if ( stristr( $value, 'gradient(' ) ) {
						$css .= "\t$name: $value;\n";
					} else {
						$css .= "\t$name: url($value);\n";
					}
					break;

				default:
					$css .= "\t$name: $value";
					if ( isset( $args['unit'] ) && '' !== $args['unit'] ) {
						$css .= $args['unit'];
					}
					$css .= ";\n";
			}
		}

		return $css;
	}

	/**
	 * Returns the type for a single property.
	 *
	 * @since 2.2
	 * @param string $name
	 * @return string|bool
	 */
	static public function property_type( $name ) {
		if ( strstr( $name, 'image' ) ) {
			return 'image';
		} elseif ( strstr( $name, 'color' ) ) {
			return 'color';
		}
		// Support SVG color properties
		if ( 'fill' === $name || 'stroke' === $name ) {
			return 'color';
		}
		return false;
	}

	/**
	 * Returns the value for a media declaration.
	 *
	 * @since 2.2
	 * @param string $media
	 * @return string
	 */
	static public function media_value( $media ) {
		$settings = FLBuilderModel::get_global_settings();

		if ( 'default' === $media ) {
			$media = '';
		} elseif ( 'medium' === $media ) {
			$media = "max-width: {$settings->medium_breakpoint}px";
		} elseif ( 'responsive' === $media ) {
			$media = "max-width: {$settings->responsive_breakpoint}px";
		}

		return $media;
	}

	/**
	 * Checks is unit field value is actually empty or not.
	 *
	 * @since 2.2
	 * @param string $value
	 * @return bool
	 */
	static public function is_empty( $value = '' ) {
		return empty( $value ) && '0' !== $value;
	}

	/**
	 * Get the unit for a given setting. If no default unit is passed, it looks for a _unit setting.
	 *
	 * @since 2.2
	 * @param string $name
	 * @param object $settings
	 * @param string $default_unit
	 * @return string
	 */
	static public function get_unit( $setting_name, $settings, $default_unit = '' ) {
		$unit = $default_unit;
		if ( '' === $unit && property_exists( $settings, $setting_name . '_unit' ) ) {
			$unit = $settings->{$setting_name . '_unit'};
		}
		return $unit;
	}
}
