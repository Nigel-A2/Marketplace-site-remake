<?php

/**
 * Helper class for working with icons.
 *
 * @since 1.4.6
 */

final class FLBuilderIcons {

	/**
	 * An array of data for each icon set.
	 *
	 * @since 1.4.6
	 * @access private
	 * @var array $sets
	 */
	static private $sets = null;

	/**
	 * Gets an array of data for core and custom icon sets.
	 *
	 * @since 1.4.6
	 * @return array An array of data for each icon set.
	 */
	static public function get_sets() {
		$switched = false;
		// Return the sets if already registered.
		if ( self::$sets ) {
			return self::$sets;
		}
		global $blog_id;
		// Check to see if we should pull sets from the main site.
		if ( is_multisite() ) {

			$id            = defined( 'BLOG_ID_CURRENT_SITE' ) ? BLOG_ID_CURRENT_SITE : 1;
			$enabled_icons = get_option( '_fl_builder_enabled_icons' );

			if ( ( $id != $blog_id ) && empty( $enabled_icons ) ) {
				switch_to_blog( $id );
				$switched = true;
			}
		}

		// Register the icon sets.
		self::register_custom_sets();
		self::register_core_sets();

		// Revert to the current site if we pulled from the main site.
		if ( is_multisite() && $switched ) {
			restore_current_blog();
		}

		/**
		 * Filter the icon sets.
		 * @see fl_builder_icon_sets
		 */
		self::$sets = apply_filters( 'fl_builder_icon_sets', self::$sets );

		// Return the sets.
		return self::$sets;
	}

	/**
	 * Gets an array of data for icon sets of the current
	 * site on a multisite install.
	 *
	 * @since 1.4.6
	 * @return array An array of data for each icon set.
	 */
	static public function get_sets_for_current_site() {
		if ( ! is_multisite() ) {
			return self::get_sets();
		}

		// Store the original sets.
		$original_sets = self::$sets;

		// Register the icon sets.
		self::register_custom_sets();
		self::register_core_sets();

		// Get the new sets.
		$sets = apply_filters( 'fl_builder_current_site_icon_sets', self::$sets );

		// Revert to the original sets.
		self::$sets = $original_sets;

		// Return the sets.
		return $sets;
	}

	/**
	 * Remove an icon set from the internal sets array.
	 *
	 * @since 1.4.6
	 * @param string $key The key for the set to remove.
	 * @return void
	 */
	static public function remove_set( $key ) {
		if ( self::$sets && isset( self::$sets[ $key ] ) ) {
			unset( self::$sets[ $key ] );
		}
	}

	/**
	 * Get the key for an icon set from the path to an icon set stylesheet.
	 *
	 * @since 1.4.6
	 * @param string $path The path to retrieve a key for.
	 * @return string The icon set key.
	 */
	static public function get_key_from_path( $path ) {
		$sets = self::get_sets();

		foreach ( $sets as $key => $set ) {
			if ( $path == $set['path'] ) {
				return $key;
			}
		}
	}

	/**
	 * Register core icon set data in the internal sets array.
	 *
	 * @since 1.4.6
	 * @access private
	 * @return void
	 */
	static private function register_core_sets() {
		$enabled_icons = FLBuilderModel::get_enabled_icons();
		/**
		 * Array of core icon sets
		 * @see fl_builder_core_icon_sets
		 */
		$core_sets = apply_filters( 'fl_builder_core_icon_sets', array(
			'font-awesome-5-solid'   => array(
				'name'   => 'Font Awesome Solid',
				'prefix' => 'fas',
			),
			'font-awesome-5-regular' => array(
				'name'   => 'Font Awesome Regular',
				'prefix' => 'far',
			),
			'font-awesome-5-light'   => array(
				'name'   => 'Font Awesome Light (pro only)',
				'prefix' => 'fal',
			),
			'font-awesome-5-duotone' => array(
				'name'   => 'Font Awesome DuoTone (pro only)',
				'prefix' => 'fad',
			),
			'font-awesome-5-brands'  => array(
				'name'   => 'Font Awesome Brands',
				'prefix' => 'fab',
			),
			'foundation-icons'       => array(
				'name'   => 'Foundation Icons',
				'prefix' => '',
			),
			'dashicons'              => array(
				'name'   => 'WordPress Dashicons',
				'prefix' => 'dashicons dashicons-before',
			),
		) );

		if ( ! FLBuilder::fa5_pro_enabled() ) {
			unset( $core_sets['font-awesome-5-light'] );
			unset( $core_sets['font-awesome-5-duotone'] );
		}

		// Add the core sets.
		foreach ( $core_sets as $set_key => $set_data ) {
			if ( is_admin() || in_array( $set_key, $enabled_icons ) ) {
				self::$sets[ $set_key ] = array(
					'name'   => $set_data['name'],
					'prefix' => $set_data['prefix'],
					'type'   => 'core',
				);
			}
		}

		// if there are no registered sets stop here.
		if ( ! is_array( self::$sets ) ) {
			return;
		}

		// Loop through core sets and add icons.
		foreach ( self::$sets as $set_key => $set_data ) {
			if ( 'core' == $set_data['type'] && 'font-awesome-kit' !== $set_key ) {

				$key = $set_key;

				if ( FLBuilder::fa5_pro_enabled() ) {
					switch ( $set_key ) {
						case 'font-awesome-5-light':
							$key = 'font-awesome-5-light-pro';
							break;

						case 'font-awesome-5-regular':
							$key = 'font-awesome-5-regular-pro';
							break;

						case 'font-awesome-5-solid':
							$key = 'font-awesome-5-solid-pro';
							break;
						case 'font-awesome-5-duotone':
							$key = 'font-awesome-5-duotone-pro';
							break;
					}
				}

				$config_path = apply_filters( 'fl_builder_core_icon_set_config', FL_BUILDER_DIR . 'json/' . $key . '.json', $set_data );

				$icons                           = json_decode( file_get_contents( $config_path ) );
				self::$sets[ $set_key ]['icons'] = $icons;
			}
		}

	}

	/**
	 * Register custom icon set data in the internal sets array.
	 *
	 * @since 1.4.6
	 * @access private
	 * @return void
	 */
	static private function register_custom_sets() {
		// Get uploaded sets.
		$enabled_icons = FLBuilderModel::get_enabled_icons();
		$upload_info   = FLBuilderModel::get_cache_dir( 'icons' );
		$folders       = glob( $upload_info['path'] . '*' );
		$kit           = FLBuilderFontAwesome::get_kit_data();

		// add kit set
		if ( is_object( $kit ) && ! empty( $kit->data->me->kit->iconUploads ) ) {
			self::$sets['font-awesome-kit'] = array(
				'name'   => sprintf( 'FA Custom Kit: %s (%s)', $kit->data->me->kit->name, $kit->data->me->kit->token ),
				'prefix' => 'fak',
				'type'   => 'core',
				'icons'  => FLBuilderFontAwesome::get_kit_icons(),
			);
		}

		// Make sure we have an array.
		if ( ! is_array( $folders ) ) {
			return;
		}

		// Loop through uploaded sets.
		foreach ( $folders as $folder ) {

			// Make sure we have a directory.
			if ( ! fl_builder_filesystem()->is_dir( $folder ) ) {
				continue;
			}

			$folder = trailingslashit( $folder );

			// This is an Icomoon font.
			if ( fl_builder_filesystem()->file_exists( $folder . 'selection.json' ) ) {

				$data = json_decode( fl_builder_filesystem()->file_get_contents( $folder . 'selection.json' ) );
				$key  = basename( $folder );
				$url  = str_ireplace( $upload_info['path'], $upload_info['url'], $folder );

				if ( isset( $data->icons ) ) {

					if ( is_admin() || in_array( $key, $enabled_icons ) ) {

						self::$sets[ $key ] = array(
							'name'       => $data->metadata->name,
							'prefix'     => '',
							'type'       => 'icomoon',
							'path'       => $folder,
							'url'        => $url,
							'stylesheet' => $url . 'style.css',
							'icons'      => array(),
						);

						foreach ( $data->icons as $icon ) {

							$prefs   = $data->preferences->fontPref;
							$postfix = isset( $prefs->postfix ) ? $prefs->postfix : '';

							if ( isset( $prefs->selector ) && 'class' == $prefs->selector ) {
								// @codingStandardsIgnoreLine
								$selector = trim( str_replace( '.', ' ', $prefs->classSelector ) ) . ' ';
							} else {
								$selector = '';
							}

							self::$sets[ $key ]['icons'][] = $selector . $prefs->prefix . $icon->properties->name . $postfix;
						}
					}
				}
			} elseif ( fl_builder_filesystem()->file_exists( $folder . 'config.json' ) ) {

				$data  = json_decode( fl_builder_filesystem()->file_get_contents( $folder . 'config.json' ) );
				$key   = basename( $folder );
				$name  = empty( $data->name ) ? 'Fontello' : $data->name;
				$url   = str_ireplace( $upload_info['path'], $upload_info['url'], $folder );
				$style = empty( $data->name ) ? 'fontello' : $data->name;

				// Append the date to the name?
				if ( empty( $data->name ) ) {
					$time        = str_replace( 'icon-', '', $key );
					$date_format = get_option( 'date_format' );
					$time_format = get_option( 'time_format' );
					$date        = gmdate( $date_format . ' ' . $time_format );
					$name       .= ' (' . $date . ')';
				}

				if ( isset( $data->glyphs ) ) {

					if ( is_admin() || in_array( $key, $enabled_icons ) ) {

						self::$sets[ $key ] = array(
							'name'       => $name,
							'prefix'     => '',
							'type'       => 'fontello',
							'path'       => $folder,
							'url'        => $url,
							'stylesheet' => $url . 'css/' . $style . '.css',
							'icons'      => array(),
						);

						foreach ( $data->glyphs as $icon ) {
							if ( $data->css_use_suffix ) {
								self::$sets[ $key ]['icons'][] = $icon->css . $data->css_prefix_text;
							} else {
								self::$sets[ $key ]['icons'][] = $data->css_prefix_text . $icon->css;
							}
						}
					}
				}
			} elseif ( fl_builder_filesystem()->file_exists( $folder . '/metadata/icons.json' ) ) { // font awesome pro subsets
				$data = json_decode( fl_builder_filesystem()->file_get_contents( $folder . '/metadata/icons.json' ) );
				$key  = basename( $folder );
				$url  = str_ireplace( $upload_info['path'], $upload_info['url'], $folder );

				if ( is_object( $data ) ) {

					if ( is_admin() || in_array( $key, $enabled_icons ) ) {

						self::$sets[ $key ] = array(
							'name'       => 'Font Awesome Custom Subset',
							'prefix'     => '',
							'type'       => 'awesome',
							'path'       => $folder,
							'url'        => $url,
							'stylesheet' => $url . '/css/all.min.css',
							'icons'      => array(),
						);

						foreach ( $data as $k => $icon ) {

							foreach ( $icon->styles as $style ) {
								switch ( $style ) {
									case 'solid':
										self::$sets[ $key ]['icons'][] = 'subset fas fa-' . $k;
										break;
									case 'regular':
										self::$sets[ $key ]['icons'][] = 'subset far fa-' . $k;
										break;
									case 'duotone':
										self::$sets[ $key ]['icons'][] = 'subset fad fa-' . $k;
										break;
									case 'light':
										self::$sets[ $key ]['icons'][] = 'subset fal fa-' . $k;
										break;
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Enqueue the stylesheets for all icon sets.
	 *
	 * @since 1.4.6
	 * @return void
	 */
	static public function enqueue_all_custom_icons_styles() {
		$sets = self::get_sets();

		foreach ( (array) $sets as $key => $data ) {

			// Don't enqueue core icons.
			if ( 'core' == $data['type'] ) {
				continue;
			}

			// Enqueue the custom icon styles.
			self::enqueue_custom_styles_by_key( $key );
		}
	}

	/**
	 * Enqueue the stylesheet(s) for icons in a module.
	 *
	 * @since 1.4.6
	 * @param object $module The module to enqueue for.
	 * @return void
	 */
	static public function enqueue_styles_for_module( $module ) {
		$fields = FLBuilderModel::get_settings_form_fields( $module->form );

		foreach ( $fields as $name => $field ) {
			if ( isset( $field['form'] ) ) {
				$form = FLBuilderModel::$settings_forms[ $field['form'] ];
				self::enqueue_styles_for_nested_module_form( $module, $form['tabs'], $name );
			} elseif ( 'icon' == $field['type'] && isset( $module->settings->$name ) ) {
				self::enqueue_styles_for_icon( $module->settings->$name );
			}
		}
	}

	/**
	 * Enqueue the stylesheet(s) for icons in a nested form field.
	 *
	 * @since 1.4.6
	 * @access private
	 * @param object $module The module to enqueue for.
	 * @param array $form The nested form.
	 * @param string $setting The nested form setting key.
	 * @return void
	 */
	static private function enqueue_styles_for_nested_module_form( $module, $form, $setting ) {
		$fields = FLBuilderModel::get_settings_form_fields( $form );

		foreach ( $fields as $name => $field ) {
			if ( 'icon' == $field['type'] && ! empty( $module->settings->$setting ) ) {
				foreach ( $module->settings->$setting as $key => $val ) {
					if ( isset( $val->$name ) ) {
						if ( 'array' === gettype( $val->$name ) ) {
							foreach ( $val->$name as $v ) {
								self::enqueue_styles_for_icon( $v );
							}
						} else {
							self::enqueue_styles_for_icon( $val->$name );
						}
					} elseif ( $name == $key && ! empty( $val ) ) {
						self::enqueue_styles_for_icon( $val );
					}
				}
			}
		}
	}

	/**
	 * Enqueue the stylesheet for an icon.
	 *
	 * @since 1.4.6
	 * @access public
	 * @param string $icon The icon CSS classname.
	 * @return void
	 */
	static public function enqueue_styles_for_icon( $icon ) {
		/**
		 * Enqueue the stylesheet for an icon.
		 * @see fl_builder_enqueue_styles_for_icon
		 */
		do_action( 'fl_builder_enqueue_styles_for_icon', $icon );

		// Make sure there is no whitespace
		// Fixes broken uabb icons
		$icon = ltrim( $icon );

		// Is this a core icon?
		if ( stristr( $icon, 'fa fa-' ) ) {
			wp_enqueue_style( 'font-awesome' );
			return;
		} elseif ( stristr( $icon, 'fi-' ) ) {
			wp_enqueue_style( 'foundation-icons' );
			return;
		} elseif ( stristr( $icon, 'dashicon' ) ) {
			wp_enqueue_style( 'dashicons' );
			return;
		}

		$sets = self::get_sets();
		foreach ( (array) $sets as $key => $data ) {
			if ( in_array( $icon, $data['icons'] ) ) {
				self::enqueue_custom_styles_by_key( $key );
				return;
			}
		}

		// finally check for fa5, we do this last because subsets miight be loaded in the block above.
		$types = array(
			'far',
			'fas',
			'fab',
			'fal',
			'fad',
		);

		foreach ( $types as $type ) {
			if ( stristr( $icon, $type . ' fa-' ) ) {
				wp_enqueue_style( 'font-awesome-5' );
				FLBuilderFonts::$preload_fa5[] = $type;
			}
		}
	}

	/**
	 * Enqueue the stylesheet for an icon set by key.
	 *
	 * @since 1.4.6
	 * @access private
	 * @param string $key The icon set key.
	 * @return void
	 */
	static private function enqueue_custom_styles_by_key( $key ) {
		if ( apply_filters( 'fl_builder_enqueue_custom_styles_by_key', true, $key ) ) {
			$sets = self::get_sets();

			if ( isset( $sets[ $key ] ) ) {

				$set = $sets[ $key ];

				if ( 'icomoon' == $set['type'] ) {
					wp_enqueue_style( $key, $set['stylesheet'], array(), FL_BUILDER_VERSION );
				}
				if ( 'fontello' == $set['type'] ) {
					wp_enqueue_style( $key, $set['stylesheet'], array(), FL_BUILDER_VERSION );
				}
				if ( 'awesome' == $set['type'] ) {
					wp_enqueue_style( $key, $set['stylesheet'], array(), FL_BUILDER_VERSION );
				}
			}
		}
	}
}
