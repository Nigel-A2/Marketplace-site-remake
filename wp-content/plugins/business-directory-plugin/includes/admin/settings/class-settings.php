<?php
/**
 * @package WPBDP\Settings
 */

class WPBDP__Settings {

    const PREFIX = 'wpbdp-';

    private $groups = array();
    private $settings = array();
    private $options = array();

    private $deps = array();

    public function __construct() {
        // Make sure our option exists.
        if ( false === ( $settings_opt = get_option( 'wpbdp_settings' ) ) ) {
            add_option( 'wpbdp_settings', array() );
        }

        // register_setting is not available on init in WordPress 4.3
        if ( ! function_exists( 'register_setting' ) && file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
		    require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        register_setting( 'wpbdp_settings', 'wpbdp_settings', array( $this, 'sanitize_settings' ) );

        // Cache current values.
        $this->options = is_array( $settings_opt ) ? $settings_opt : array();
    }

    public function bootstrap() {
        // Add initial settings.
        require_once WPBDP_INC . 'admin/settings/class-settings-bootstrap.php';
        WPBDP__Settings__Bootstrap::register_initial_groups();
        WPBDP__Settings__Bootstrap::register_initial_settings();
    }

    public function sanitize_settings( $input ) {
        if ( empty( $input ) ) {
            return $this->options;
        }

        $on_admin = ! empty( $_POST['_wp_http_referer'] );

        $output = array_merge( $this->options, $input );

        // Validate each setting.
        foreach ( $input as $setting_id => $value ) {
            $output[ $setting_id ] = apply_filters( 'wpbdp_settings_sanitize', $value, $setting_id );
            $output[ $setting_id ] = apply_filters( 'wpbdp_settings_sanitize_' . $setting_id, $input[ $setting_id ], $setting_id );

            if ( ! empty( $this->settings[ $setting_id ] ) ) {
                $setting = $this->settings[ $setting_id ];

                // XXX: maybe this should always be executed, not only admin side?
                if ( $on_admin ) {
                    switch ( $setting['type'] ) {
						case 'multicheck':
							if ( is_array( $value ) ) {
								$input[ $setting_id ] = array_filter( $value, 'strlen' );
								$output[ $setting_id ] = array_filter( $value, 'strlen' );
							}

							break;
                    }
                }

                if ( ! empty( $setting['on_update'] ) && is_callable( $setting['on_update'] ) ) {
                    call_user_func( $setting['on_update'], $setting, $input[ $setting_id ], ! empty( $this->options[ $setting_id ] ) ? $this->options[ $setting_id ] : null );
                }
            }

            // XXX: Settings hasn't been stored into the database yet here.
            do_action( 'wpbdp_setting_updated', $setting_id, $output[ $setting_id ], $value );
            do_action( "wpbdp_setting_updated_{$setting_id}", $output[ $setting_id ], $value, $setting_id );
        }

        $this->options = $output;

        return $this->options;
    }

    /**
     * Register a setings group within the Settings API.
	 *
     * @since 5.0
     */
    public function register_group( $slug, $title = '', $parent = '', $args = array() ) {
		if ( $parent === 'modules' ) {
			// Remove the top-level modules menu.
			$parent = '';
		}

		// Allow for later setting registration for a lower menu.
		if ( $parent === 'misc' && ! isset( $this->groups[ $parent ] ) ) {
			wpbdp_register_settings_group( 'misc', __( 'Miscellaneous', 'business-directory-plugin' ), '', array( 'icon' => 'misc' ) );
		}

        if ( $parent && ! isset( $this->groups[ $parent ] ) ) {
			// throw new Exception( sprintf( 'Parent settings group does not exist: %s', $parent ) );
			return false;
        }

		/**
		 * @since 5.7.6
		 */
		do_action( 'wpbdp_register_group', compact( 'slug', 'title', 'parent' ) );

        $parents = array();
        $parent_ = $parent;

		while ( $parent_ ) {
            $parents[] = $parent_;
            $parent_ = $this->groups[ $parent_ ]['parent'];
        }

        switch ( count( $parents ) ) {
			case 0:
				$group_type = 'tab';
				break;
			case 1:
				$group_type = 'subtab';
				break;
			case 2:
				$group_type = 'section';
				break;
			default:
				// throw new Exception( sprintf( 'Invalid # of parents in the tree for settings group "%s"', $slug ) );
				return false;
        }

        if ( $parent ) {
            $this->groups[ $parent ]['count'] += 1;
        }

        $this->groups[ $slug ] = array_merge(
            $args,
            array(
                'title'  => $title,
                'desc'   => isset( $args['desc'] ) ? $args['desc'] : '',
                'type'   => $group_type,
                'parent' => $parent,
                'count'  => 0,
                'icon'   => isset( $args['icon'] ) ? $args['icon'] : 'archive',
                'class'  => isset( $args['class'] ) ? $args['class'] : '',
            )
        );
    }

    /**
     * Register a setting within the Settings API.
	 *
     * @since 5.0
     */
    public function register_setting( $id_or_args, $name = '', $type = 'text', $group = '', $args = array() ) {
        if ( is_array( $id_or_args ) ) {
            $args = $id_or_args;
        } else {
            $args = array_merge(
                $args,
                array(
                    'id'    => $id_or_args,
                    'name'  => $name,
                    'type'  => $type,
                    'group' => $group
                )
            );
        }

        $args = wp_parse_args(
			$args,
			array(
				'id'           => '',
				'name'         => '',
				'type'         => 'text',
				'group'        => 'general/main',
				'desc'         => '',
				'validator'    => false,
				'default'      => false,
				'on_update'    => false,
				'class'        => '',
				'grid_classes' => false,
				'dependencies' => array()
			)
		);

		if ( isset( $this->settings[ $args['id'] ] ) ) {
            return false;
        }

        if ( 'silent' != $args['type'] && ! isset( $this->groups[ $args['group'] ] ) ) {
            // throw new Exception( sprintf( 'Invalid settings group "%s" for setting "%s".', $args['group'], $args['id'] ) );
            return false;
        }

        if ( 'number' == $args['type'] ) {
            add_filter( 'wpbdp_settings_sanitize_' . $args['id'], array( $this, 'validate_number_setting' ), 10, 2 );
        } elseif ( 'text' === $args['type'] || 'radio' === $args['type'] ) {
        	add_filter( 'wpbdp_settings_sanitize_' . $args['id'], 'sanitize_text_field' );
        } elseif ( 'textarea' === $args['type'] ) {
        	add_filter( 'wpbdp_settings_sanitize_' . $args['id'], 'wp_kses_post' );
        }

		$this->settings[ $args['id'] ] = $args;

        if ( 'silent' != $args['type'] ) {
            $this->groups[ $args['group'] ]['count'] += 1;
        }

        if ( ! empty( $args['validator'] ) ) {
            add_filter( 'wpbdp_settings_sanitize_' . $args['id'], array( $this, 'validate_setting' ), 10, 2 );
        }
    }

	/**
	 * Deregister a group if it has no settings.
	 *
	 * @since 5.9.1
	 */
	public function deregister_empty_group( $id ) {
		if ( ! isset( $this->groups[ $id ] ) ) {
			return;
		}

		// Check if there are any settings in the group.
		foreach ( $this->settings as $setting => $details ) {
			if ( $details['group'] === $id ) {
				return;
			}
		}

		// Check if there are any sub groups in the group.
		foreach ( $this->groups as $group => $details ) {
			if ( $details['parent'] === $id ) {
				return;
			}
		}

		$parent = $this->groups[ $id ]['parent'];
		unset( $this->groups[ $id ] );

		// Unset parent if it's empty now.
		$this->deregister_empty_group( $parent );
	}

	/**
	 * Register a setting within the Settings API.
	 *
	 * @since 5.7.6
	 */
	public function deregister_setting( $id ) {
		if ( isset( $this->settings[ $id ] ) ) {
			unset( $this->settings[ $id ] );
		}
	}

    public function get_registered_groups() {
        return $this->groups;
    }

    public function get_registered_settings() {
        return $this->settings;
    }

    /**
     * @return int|string|array
     */
    public function get_option( $setting_id, $default = false ) {
        $default_provided = func_num_args() > 1;

        if ( array_key_exists( $setting_id, $this->options ) ) {
            $value = $this->options[ $setting_id ];
        } elseif ( $default_provided ) {
			$value = $default;
		} elseif ( ! empty( $this->settings[ $setting_id ] ) ) {
			$value = $this->settings[ $setting_id ]['default'];
		} else {
			$value = false;
        }

        $value = apply_filters( 'wpbdp_get_option', $value, $setting_id );
        $value = apply_filters( 'wpbdp_get_option_' . $setting_id, $value );

        // Sanitize the value (if empty) based on setting type.
        if ( empty( $value ) ) {
            if ( $setting = $this->get_setting( $setting_id ) ) {
                switch ( $setting['type'] ) {
                    case 'checkbox':
                        $value = (int) $value;
                        break;
                    case 'multicheck':
                        $value = array();
                        break;
                }
            }
        }

		if ( is_string( $value ) ) {
			// Trim the value so we don't have to do it everywhere else.
			$value = trim( $value );
		}

        return $value;
    }

    public function set_option( $setting_id, $value = null ) {
        $old = get_option( 'wpbdp_settings', array() );
        $old[ $setting_id ] = $value;
        update_option( 'wpbdp_settings', $old );
    }

	/**
	 * @since 5.9.1
	 */
    public function delete_option( $setting_id ) {
        $this->set_option( $setting_id );
    }

    /**
     * @deprecated 5.0. Use {@link WPBDP__Settings::register_group()}.
     */
    public function add_group( $slug, $name, $help_text = '' ) {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Settings::register_group' );

        if ( ! isset( $this->groups[ $slug ] ) ) {
            $this->register_group( $slug, $name, '', array( 'desc' => $help_text ) );
        }

        return $slug;
    }

    /**
     * @deprecated 5.0. Use {@link WPBDP__Settings::register_group()}.
     */
	public function add_section( $group_slug, $slug, $name, $help_text = '' ) {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Settings::register_group' );

        $tab = $group_slug;
        $subtab = $group_slug . '/main';

        if ( ! isset( $this->groups[ $subtab ] ) ) {
            $this->register_group( $subtab, _x( 'General Settings', 'settings', 'business-directory-plugin' ), $tab );
        }

        $this->register_group( "{$subtab}:{$slug}", $name, $subtab, array( 'desc' => $help_text ) );

        return "{$subtab}:{$slug}";
    }

    /**
     * @deprecated 5.0. Use {@link WPBDP__Settings::register_setting()}.
     */
    public function add_core_setting() {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Settings::register_setting' );
        return false;
    }

    /**
     * @deprecated 5.0. Use {@link WPBDP__Settings::register_setting()}.
     */
    public function add_setting( $section_key, $name, $label, $type = 'text', $default = null, $help_text = '', $args = array(), $validator = null, $callback = null ) {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Settings::register_setting' );
    }

    /**
     * @deprecated 5.0. Specify dependencies while registering the setting using {@link WPBDP__Settings::register_setting()}.
     */
    public function register_dep( $setting, $dep, $arg = null ) {
		_deprecated_function( __METHOD__, '5.0', 'WPBDP__Settings::register_setting' );
    }

    public function get_dependencies( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'setting' => null,
				'type'    => null,
			)
		);
        extract( $args );

        if ( $setting )
            return isset( $this->deps[ $setting ] ) ? $this->deps[ $setting ] : array();

        if ( $type ) {
            $res = array();

            foreach ( $this->deps as $s => $deps ) {
                foreach ( $deps as $d => $a ) {
                    if ( $type == $d )
                        $res[ $s ] = $a;
                }
            }
        }

        return $res;
    }

    public function get_setting( $name ) {
        if ( isset( $this->settings[ $name ] ) )
            return $this->settings[ $name ];

        return false;
    }

    /**
     * Resets settings to their default values. This includes ALL premium modules too, so use with care.
     */
    public function reset_defaults() {
        $options = $this->options;

        foreach ( $options as $option_id => $option_value ) {
            if ( preg_match( '/^license-key-/', $option_id ) ) {
                continue;
            }

            unset( $this->options[ $option_id ] );
        }

        update_option( 'wpbdp_settings', $this->options );
    }

    public function validate_setting( $value, $setting_id ) {
        $on_admin = ! empty( $_POST['_wp_http_referer'] );
        if ( ! $on_admin ) {
            return $value;
        }

        if ( ! empty( $this->settings[ $setting_id ] ) ) {
            $setting = $this->get_setting( $setting_id );

            if ( is_string( $setting['validator'] ) ) {
                $validators = explode( ',', $setting['validator'] );
            } else if ( is_callable( $setting['validator'] ) ) {
                $validators = array( $setting['validator'] );
            } else if ( is_array( $setting['validator'] ) ) {
                $validators = $setting['validator'];
            }
        } else {
            $setting    = null;
            $validators = array();
        }

        if ( isset( $this->options[ $setting_id ] ) ) {
            $old_value = $this->options[ $setting_id ];
        } else {
            $old_value = null;
        }

        $has_error = false;

        foreach ( $validators as $validator ) {
            switch ( $validator ) {
				case 'trim':
					$value = trim( $value );
					break;
				case 'no-spaces':
					$value = trim( preg_replace( '/\s+/', '', $value ) );
					break;
				case 'required':
					if ( is_array( $value ) ) {
						$value = array_filter( $value, 'strlen' );
					}

					if ( empty( $value ) ) {
						add_settings_error( 'wpbdp_settings', $setting_id, sprintf( _x( '"%s" can not be empty.', 'settings', 'business-directory-plugin' ), $setting['name'] ), 'error' );
						$has_error = true;
					}

					break;
				case 'taxonomy_slug':
					// Don't use sanitize_title because it replaes unicode characters
					// with octets and breaks the Rewrite Rules.
					$value = trim( $value );

					if ( empty( $value ) ) {
						add_settings_error( 'wpbdp_settings', $setting_id, sprintf( _x( '"%s" can not be empty.', 'settings', 'business-directory-plugin' ), $setting['name'] ), 'error' );
						$has_error = true;
						continue 2;
					}

					// Check for characters that will break the url.
					$disallow = array( ' ', ',', '&' );
					$stripped = str_replace( $disallow, '', $value );
					if ( $stripped !== $value ) {
						add_settings_error( 'wpbdp_settings', $setting_id, sprintf( __( '%s cannot include spaces, commas, or &', 'business-directory-plugin' ), $setting['name'] ), 'error' );
						$has_error = true;
						continue 2;
					}

					if ( ! empty( $setting ) && ! empty( $setting['taxonomy'] ) ) {
						foreach ( get_taxonomies( array(), 'objects' ) as $taxonomy ) {
							if ( $taxonomy->rewrite && $taxonomy->rewrite['slug'] == $value && $taxonomy->name != $setting['taxonomy'] ) {
								add_settings_error( 'wpbdp_settings', $setting_id, sprintf( _x( 'The slug "%s" is already in use for another taxonomy.', 'settings', 'business-directory-plugin' ), $value ), 'error' );
								$has_error = true;
							}
						}
					}

					break;
				default:
					// TODO: How to handle errors to set $has_error = true?
					if ( is_callable( $validator ) ) {
						if ( is_string( $validator ) ) {
							$value = call_user_func( $validator, $value );
						} else {
							$value = call_user_func( $validator, $value, $old_value, $setting );
						}
					}

					break;
			}
        }

        return ( $has_error ? $old_value : $value );
    }

    public function validate_number_setting( $value, $setting_id ) {
        $setting = $this->get_setting( $setting_id );

        if ( ! $setting ) {
            return $value;
        }

        if ( ! empty( $setting['step'] ) && is_int( $setting['step'] ) ) {
            $value = intval( $value );
        } else {
            $value = floatval( $value );
        }

        // Min and max.
        $value = ( array_key_exists( 'min', $setting ) && $value < $setting['min'] ) ? $setting['min'] : $value;
        $value = ( array_key_exists( 'max', $setting ) && $value > $setting['max'] ) ? $setting['max'] : $value;

        return $value;
    }

    public function set_new_install_settings() {
        $this->set_option( 'show-manage-listings', true );
    }

	/**
	 * @deprecated 6.1
	 */
	public function pre_2_0_options() {
		_deprecated_function( __METHOD__, '6.1' );
		return array();
	}

	/**
	 * @deprecated 6.1
	 */
	public function upgrade_options() {
		_deprecated_function( __METHOD__, '6.1' );
	}

	/**
	 * Emulates get_wpbusdirman_config_options() in version 2.0 until
	 * all deprecated code has been ported.
	 *
	 * @deprecated 6.1
	 */
	public function pre_2_0_compat_get_config_options() {
		_deprecated_function( __METHOD__, '6.1' );
		return array();
	}
}

// For backwards compat.
class WPBDP_Settings extends WPBDP__Settings {
	public function __construct() {
		_deprecated_constructor( __CLASS__, '5.0', 'WPBDP__Settings' );
		parent::__construct();
	}
}
