<?php
/**
 * BD Premium Themes setup
 *
 * @since 4.0
 */

/**
 * Class WPBDP_Themes
 *
 * @since 4.0
 */
class WPBDP_Themes {

    private $themes        = array();
    private $template_dirs = array();
    private $cache         = array(
		'templates'           => array(),
		'rendered'            => array(),
		'template_vars_stack' => array(),
	);

	private $folder_name = 'businessdirectory-themes';

    function __construct() {
        $this->find_themes();

        $this->set_template_dirs();

        // Add some extra data to theme information.
        $this->add_theme_data();

        // Load special theme .php file.
        $this->call_theme_function( '' );
        $this->call_theme_function( 'init' );

        $this->load_theme_translation();

        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_theme_scripts' ), 999 );
        add_filter( 'wpbdp_form_field_display', array( &$this, 'field_theme_override' ), 999, 4 );
        add_action( 'wp_footer', array( $this, 'fee_specific_coloring' ), 999 );

        if ( is_admin() ) {
            require_once WPBDP_PATH . 'includes/admin/controllers/class-themes-admin.php';
            $this->admin = new WPBDP_Themes_Admin( $this, wpbdp()->licensing );
        }
    }

	/**
	 * Get the list of folders to check for override templates.
	 *
	 * @since 5.13.2
	 */
	private function set_template_dirs() {
		// Theme BD template dir is priority 1.
		$theme                       = $this->get_active_theme_data();
		$this->template_dirs['bd']   = $theme->path . 'templates/';

		$this->add_core_template_dir();
	}

	/**
	 * @since 5.13.2
	 */
	private function add_core_template_dir() {
		// Core templates are last priority.
		$this->template_dirs['core'] = trailingslashit( WPBDP_TEMPLATES_PATH );
	}

    function call_theme_function( $fname, $args = array() ) {
        $theme = $this->get_active_theme_data();

        // If no function name is provided, just load the file.
        if ( ! $fname && file_exists( $theme->path . 'theme.php' ) ) {
            include_once $theme->path . 'theme.php';
        }

        if ( ! $fname ) {
            return;
        }

        $theme_name = str_replace( array( '-' ), array( '_' ), $theme->id );

		$alternatives = array();
		$base = $theme_name . '_' . $fname;
		$this->add_function_names( $base, $alternatives );

		// Allow for themes with bd- prefix.
		if ( strpos( $theme_name, 'bd_' ) === 0 ) {
			$base = str_replace( 'bd_', '', $base );
			$this->add_function_names( $base, $alternatives );
		}

        foreach ( $alternatives as $alt ) {
            if ( function_exists( $alt ) ) {
                call_user_func_array( $alt, $args );
                return;
            }
        }
    }

	/**
	 * @since 6.2.5
	 */
	private function add_function_names( $base, &$alternatives ) {
		$alternatives[] = 'wpbdp_themes__' . $base;
		$alternatives[] = 'wpbdp_' . $base;
		$alternatives[] = $base;
	}

    function load_theme_translation() {
        $theme  = $this->get_active_theme_data();
        $locale = get_locale();

        $mofile = WP_CONTENT_DIR . "/languages/plugins/wpbdp-{$theme->id}-{$locale}.mo";

        if ( ! file_exists( $mofile ) ) {
            $mofile = untrailingslashit( $theme->path ) . "/languages/wpbdp-{$theme->id}-{$locale}.mo";
        }

        if ( ! file_exists( $mofile ) ) {
            return;
        }

        return load_textdomain( 'wpbdp-' . $theme->id, $mofile );
    }

    function enqueue_theme_scripts() {
        $theme = $this->get_active_theme_data();
        $css   = array_filter( (array) $theme->assets->css );
        $js    = array_filter( (array) $theme->assets->js );

        foreach ( $css as $c ) {
            wp_enqueue_style(
                $theme->id . '-' . $this->_normalize_asset_name( $c ),
                $theme->url . 'assets/' . $c,
                array(),
                $theme->version
            );
        }

        if ( 'theme' == wpbdp_get_option( 'themes-button-style' ) && file_exists( $theme->path . 'assets/buttons.css' ) ) {
            wp_enqueue_style(
                $theme->id . '-buttons',
                $theme->url . 'assets/buttons.css',
                array(),
                $theme->version
            );
        }

        foreach ( $js as $j ) {
            if ( is_object( $j ) ) {
                $handle = $theme->id . '-' . $this->_normalize_asset_name( $j->handle );
                $source = $theme->url . 'assets/' . $j->handle;
                $deps   = $j->deps;
            } else {
                $handle = $theme->id . '-' . $this->_normalize_asset_name( $j );
                $source = $theme->url . 'assets/' . $j;
                $deps   = array();
            }

            wp_enqueue_script( $handle, $source, $deps, $theme->version, true );
        }

        $this->call_theme_function( 'enqueue_scripts' );
    }

    function field_theme_override( $html, $field, $context, $listing_id ) {
        $options = array();

        foreach ( array( $context . '-', '' ) as $prefix ) {
            $options[] = $prefix . 'field-' . $field->get_id();
            $options[] = $prefix . 'field-' . $field->get_short_name();

            if ( $field->get_tag() ) {
                $options[] = $prefix . 'field-' . $field->get_tag();
            }

            $options[] = $prefix . 'field-type-' . $field->get_field_type_id();
            $options[] = $prefix . 'field';
        }

        $path = '';
        foreach ( $options as $o ) {
            if ( $path = $this->locate_template( $o ) ) {
                break;
            }
        }

        if ( ! $path ) {
            return $html;
        }

        $vars = array(
			'field'      => $field,
			'context'    => $context,
			'listing_id' => $listing_id,
			'value'      => $field->html_value( $listing_id ),
			'raw'        => $field->value( $listing_id ),
		);

        return $this->render_template_file( $path, $path, $vars );
    }

	public function fee_specific_coloring() {
		global $wpbdp;

		if ( empty( $wpbdp->fee_colors ) ) {
			return;
		}

		echo '<style>';
		foreach ( $wpbdp->fee_colors as $id => $color ) {
			echo '.wpbdp-listing-excerpt.wpbdp-listing-plan-id-' . $id . '{ background-color: ' . esc_attr( $color ) . '}';
		}
		echo '</style>';
	}

    function _normalize_asset_name( $a ) {
        $a = strtolower( $a );
        $a = str_replace( ' ', '_', $a );
        $a = str_replace( '.css', '', $a );
        return $a;
    }

    function get_themes_dir() {
        return WP_CONTENT_DIR . '/' . $this->folder_name . '/';
    }

	public function get_themes_directories() {
		$folder = 'themes/';
		$res = array(
			WPBDP_PATH . $folder  => WPBDP_URL . $folder,
			$this->get_themes_dir() => trailingslashit( content_url( $this->folder_name ) ),
		);

        $res = array_combine(
            array_map( 'wp_normalize_path', array_keys( $res ) ),
            array_values( $res )
        );

        return $res;
    }

    /**
     * Scans all theme directories to find themes and returns information about them.
     * Subsequent calls to this function use an internal cache to avoid unnecessary I/O.
     *
     * @return array An array of theme objects.
     */
	public function get_installed_themes() {
        // Use cached info if available.
        if ( ! empty( $this->themes ) ) {
            return $this->themes;
        }

        $this->find_themes();
        return $this->themes;
    }

    function find_themes( $reload = false ) {
        if ( ! empty( $this->themes ) && ! $reload ) {
            return;
        }

        $this->themes = array();

        foreach ( $this->get_themes_directories() as $path => $url ) {
            $dirs = WPBDP_FS::ls( $path, 'filter=dir' );

            if ( ! $dirs ) {
                continue;
            }

            foreach ( $dirs as $d ) {
                $info = $this->_get_theme_info( $d );

                if ( ! $info ) {
                    continue;
                }

                $this->themes[ $info->id ] = $info;
            }
        }
    }

    /**
     * Changes the active theme.
     *
     * @param string $theme_id
     * @return boolean True if theme was changed successfully, False otherwise.
     */
    function set_active_theme( $theme_id = '' ) {
        if ( ! $theme_id ) {
            return false;
        }

        $themes = $this->get_installed_themes();
        if ( ! isset( $themes[ $theme_id ] ) ) {
            return false;
        }

        if ( $theme_id == $this->get_active_theme() ) {
            return true;
        }

        $ok = update_option( 'wpbdp-active-theme', $theme_id );

        if ( $ok ) {
            global $wpbdp;
            $wpbdp->formfields->maybe_correct_tags();
        }

        return $ok;
    }

    /**
     * Retrieves the ID for the current active theme.
     *
     * @return string
     */
    function get_active_theme() {
        $active = get_option( 'wpbdp-active-theme', 'default' );
        $themes = $this->get_installed_themes();

        if ( ! isset( $themes[ $active ] ) ) {
            return 'default';
        }

        return $active;
    }

    /**
     * Retrieves theme information for the current active theme.
     *
     * @return object
     */
    function get_active_theme_data( $key = null ) {
        $active = $this->get_active_theme();
        $data   = $this->themes[ $active ];

        if ( ! is_null( $key ) ) {
            return isset( $data->{$key} ) ? $data->{$key} : false;
        }

        return $data;
    }

    public function get_theme( $theme_id ) {
        if ( isset( $this->themes[ $theme_id ] ) ) {
            return $this->themes[ $theme_id ];
        }

        return false;
    }

    /**
     * @since 4.0
     */
    function missing_suggested_fields( $key = '' ) {
        global $wpbdp;
        global $wpdb;

        $key = ( ! $key ) ? 'tag' : $key;

        $missing             = array();
        $suggested_fields    = array_filter( (array) $this->get_active_theme_data( 'suggested_fields' ) );
        $current_fields_tags = $wpdb->get_col( "SELECT tag FROM {$wpdb->prefix}wpbdp_form_fields" );

        $missing_tags = array_diff( $suggested_fields, $current_fields_tags );

        foreach ( $missing_tags as $mt ) {
            $info = $wpbdp->formfields->get_default_fields( $mt );

            if ( ! $info ) {
                continue;
            }

            $missing[] = $info[ $key ];
        }

        return $missing;
    }

    function _get_theme_info( $d ) {
        $d = trailingslashit( $d );

        $manifest_file = $d . 'theme.json';

		if ( ! is_readable( $manifest_file ) ) {
			return false;
		}

        $manifest = (array) json_decode( file_get_contents( $manifest_file ) );
        if ( ! $manifest ) {
            return false;
        }

        $theme_keys = array(
            array( 'id', 'string', basename( $d ) ),
            array( 'name', 'string', basename( $d ) ),
            array( 'edd_name', 'string', '' ),
            array( 'description', 'string', '' ),
            array( 'version', 'string', '0' ),
            array( 'author', 'string', '' ),
            array( 'author_email', 'email', '' ),
            array( 'author_url', 'url', '' ),
            array( 'requires', 'string', '4.0dev' ),
            array(
				'assets',
				'array',
				array(
					'css' => null,
					'js'  => null,
				),
				array( 'allow_other_keys' => false ),
			),
            array( 'template_variables', 'array', array() ),
            array( 'suggested_fields', 'array', array() ),
            array( 'thumbnails', 'array', array() ),
        );

        $info = new StdClass();

        foreach ( $theme_keys as $i ) {
            list( $k, $type, $default ) = $i;
            $value                      = isset( $manifest[ $k ] ) ? $manifest[ $k ] : $default;

            switch ( $type ) {
                case 'string':
                case 'email':
                case 'url':
                    $value = is_string( $value ) ? $value : strval( $value );
                    break;

                case 'int':
                    $value = is_numeric( $value ) ? intval( $value ) : null;
                    break;

                case 'array':
                    break;

                default:
                    $value = null;
                    break;
            }

            if ( is_null( $value ) ) {
                continue;
            }

            $info->{$k} = $value;
        }

        $info->path = $d;

        if ( ! $this->_guess_theme_path_info( $info ) ) {
            return false;
        }

        return $info;
    }

    function add_theme_data() {
        foreach ( $this->themes as &$t ) {
            $t->is_core_theme = $this->_is_core_theme( $t );

            if ( ! $t->is_core_theme ) {
                wpbdp()->licensing->add_item(
                    array(
						'item_type' => 'theme',
						'id'        => $t->id,
						'name'      => $t->name,
						'version'   => $t->version,
                    )
                );
                // $t->license_key = wpbdp_get_option( 'license-key-theme-' . $t->id );
                // $t->license_status = get_option( 'wpbdp-license-status-theme-' . $t->id );
            }

            $t->active = ( $t->id == $this->get_active_theme() );
        }
    }

    private function _is_core_theme( $theme ) {
        if ( in_array( $theme->id, array( 'no_theme', 'default' ), true ) ) {
            return true;
        }

        return $this->_is_premium_theme( $theme ) ? false : true;
    }

    private function _is_premium_theme( $theme ) {
        $official_themes = $this->_get_official_themes();

        if ( ! $official_themes ) {
            // Assume it's a premium theme until information can be verified.
            return true;
        } else {
            foreach ( $this->_get_official_themes() as $official_theme ) {
                if ( $theme->name == $official_theme->name ) {
                    return true;
                }
            }
        }

        return false;
    }

    private function _get_official_themes() {
        $official_themes = get_transient( 'wpbdp-official-themes' );

        if ( is_array( $official_themes ) ) {
            return $official_themes;
		}

        $official_themes = array();

        $params = array(
            'tag'    => 'theme',
            'number' => 10,
        );

        $url = add_query_arg( $params, 'http://businessdirectoryplugin.com/edd-api/v2/products/' );

        $response = wp_remote_get(
            $url, array(
				'timeout'   => 15,
				'sslverify' => false,
            )
        );

        if ( is_wp_error( $response ) ) {
            set_transient( 'wpbdp-official-themes', array(), HOUR_IN_SECONDS );
            return array();
        }

        $response_data = json_decode( wp_remote_retrieve_body( $response ) );

        if ( ! isset( $response_data->products ) || ! is_array( $response_data->products ) ) {
            set_transient( 'wpbdp-official-themes', array(), HOUR_IN_SECONDS );
            return array();
        }

        foreach ( $response_data->products as $product ) {
            $official_themes[] = (object) array(
                'name' => $product->info->title,
            );
        }

        set_transient( 'wpbdp-official-themes', $official_themes, WEEK_IN_SECONDS );

        return $official_themes;
    }

    /**
     * Attempts to find the theme's URL based on the location on disk:
     *
     * - Inside Business Directory Plugin directory.
     * - On Business Directory Themes directory.
     */
    function _guess_theme_path_info( &$theme ) {
        $valid_parents = $this->get_themes_directories();

        foreach ( $valid_parents as $path => $url ) {
            if ( false !== stripos( $theme->path, $path ) ) {
                $theme->url = str_replace( $path, $url, $theme->path );
                break;
            }
        }

        $theme->thumbnail = is_readable( $theme->path . 'thumbnail.png' ) ? $theme->url . 'thumbnail.png' : '';

        return ! empty( $url );
    }

    function add_template_dir( $dir_or_file ) {
        if ( ! is_dir( $dir_or_file ) ) {
            return false;
        }

        $path = trailingslashit( wp_normalize_path( $dir_or_file ) );

        if ( in_array( $path, $this->template_dirs, true ) ) {
            return true;
        }

		// Add the template before core.
		unset( $this->template_dirs['core'] );
		$this->template_dirs[] = $path;
		$this->add_core_template_dir();

        return true;
    }

    public function render( $template_id, $vars = array() ) {
        return $this->render_template_file( $template_id, $this->locate_template( $template_id ), $vars );
    }

    private function render_template_file( $template_id, $path, $vars = array() ) {
        if ( ! $path ) {
			wpbdp_log( 'Invalid template path for template: "' . $template_id . '"' );
			return '';
        }

        $in_wrapper    = isset( $vars['_child'] );
        $template_meta = $this->get_template_meta( $path );

        if ( ! $in_wrapper ) {
            // Setup default and hook-added variables.
            $this->_configure_template_vars( $template_id, $path, $vars );

            // Process variables using templates or callbacks.
            $this->_process_template_vars( $vars );

            // Configure blocks depending on theme overrides.
            $this->_configure_template_blocks( $vars, $template_meta['variables'] );
        }

        array_push( $this->cache['template_vars_stack'], $vars );
        extract( $vars );

        ob_start();
        include $path;
        $html = ob_get_contents();
        ob_end_clean();

        if ( isset( $__template__['blocks'] ) && is_array( $__template__['blocks'] ) ) {
            $template_meta['blocks'] = array_merge( $__template__['blocks'], $template_meta['blocks'] );
        }

        $is_part = isset( $vars['_part'] ) && $vars['_part'];

        // Add before/after to the HTML directly.
        if ( $is_part || in_array( 'before', $template_meta['blocks'], true ) ) {
            // leave html unmodified
        } elseif ( ! empty( $vars['blocks']['before'] ) ) {
            $html = $vars['blocks']['before'] . $html;
        }

        if ( $is_part || in_array( 'after', $template_meta['blocks'], true ) ) {
            // leave html unmodified
        } elseif ( ! empty( $vars['blocks']['after'] ) ) {
            $html = $html . $vars['blocks']['after'];
        }

        if ( ! $in_wrapper && $vars['_wrapper_path'] ) {
            $in_wrapper = true;

            $vars2        = array(
				'_template' => $vars['_wrapper'],
				'_path'     => $vars['_wrapper_path'],
				'_class'    => $vars['_class'],
				'_child'    => (object) $vars,
				'content'   => $html,
			);
            $wrapper_html = $this->render_template_file( $vars['_wrapper_path'], $vars['_wrapper_path'], $vars2 );

            $in_wrapper = false;
            $html       = $wrapper_html;
        }

        array_pop( $this->cache['template_vars_stack'] );

        $html = apply_filters( 'wpbdp_x_render', $html, $template_id, $vars );
        return $html;
    }

    /**
     * Searches for block and block variable customization metadata in the first 8kiB
     * of a template file (core or custom).
     *
     * @link http://docs.businessdirectoryplugin.com/themes/customization.html#block-and-block-variable-customization
     *
     * @since 5.0
     *
     * @param string $template_path Path to the template file.
     *
     * @return Array of meta information in `variable => array()` format.
     */
    private function get_template_meta( $template_path ) {
        $default_headers = array(
			'blocks'    => 'Template Blocks',
			'variables' => 'Template Variables',
		);
        $template_meta   = get_file_data( $template_path, $default_headers, 'business_directory_template' );

        foreach ( array_keys( $default_headers ) as $variable ) {
            if ( ! $template_meta[ $variable ] ) {
                $template_meta[ $variable ] = array();
                continue;
            }

            $template_meta[ $variable ] = array_map( 'trim', explode( ',', $template_meta[ $variable ] ) );
        }

        return $template_meta;
    }

    function render_part( $template_id, $additional_vars = array() ) {
        $output = '';

        $last = count( $this->cache['template_vars_stack'] ) - 1;

        if ( $last >= 0 ) {
            $vars = $this->cache['template_vars_stack'][ $last ];
        } else {
			$vars = array();
        }

        $vars['_part']         = true;
        $vars['_wrapper']      = '';
        $vars['_wrapper_path'] = '';

        $output = $this->render( $template_id, array_merge( $additional_vars, $vars ) );
        return $output;
    }

    function _configure_template_vars( $template_id, $path, &$vars ) {
        $defaults = array(
            '_id'           => str_replace(
                array( '.tpl.php', ' ' ),
                array( '', '-' ),
                $template_id
            ),
            '_template'     => $template_id,
            '_path'         => $path,
            '_wrapper'      => '',
            '_wrapper_path' => '',
            '_parent'       => '',
			/*
		            '_bar' => false,
			'_bar_items' => array( 'links', 'search' ),*/
				'_class'    => '',
        );

        $vars = array_merge( $defaults, $vars );

        if ( $vars['_wrapper'] ) {
            $vars['_wrapper_path'] = $this->locate_template( $vars['_wrapper'] );

            if ( ! $vars['_wrapper_path'] ) {
                $vars['_wrapper'] = '';
            }
        }

        if ( $this->cache['template_vars_stack'] ) {
            $cnt  = count( $this->cache['template_vars_stack'] );
            $last = $this->cache['template_vars_stack'][ $cnt - 1 ];

            if ( ! empty( $last['_template'] ) ) {
                $vars['_parent'] = $last['_template'];
            }
        }

        $vars = apply_filters( 'wpbdp_template_variables', $vars, $template_id );
        $vars = apply_filters( 'wpbdp_template_variables__' . $template_id, $vars, $path );

        // Add info about current theme.
        $theme              = $this->get_active_theme_data();
        $vars['THEME_PATH'] = $theme->path;
        $vars['THEME_URL']  = $theme->url;
    }

    function _process_template_vars( &$vars ) {
        foreach ( $vars as $k => $v ) {
            if ( '#' != $k[0] ) {
                continue;
            }

            $k_ = substr( $k, 1 );

            if ( ! is_array( $v ) || ! array_key_exists( 'position', $v ) ) {
                $vars[ $k_ ] = $v;
                unset( $vars[ $k ] );
            }

            $vars[ $k ]['weight'] = isset( $v['weight'] ) ? intval( $v['weight'] ) : 10;

            if ( array_key_exists( 'value', $v ) ) {
                continue;
            }

            if ( array_key_exists( 'callback', $v ) ) {
                $vars[ $k ]['value'] = call_user_func_array( $v['callback'], array( $vars, $vars['_template'] ) ); // TODO: support 'echo'ed output too.
                unset( $vars[ $k ]['callback'] );
            }
        }
    }

    private function _configure_template_blocks( &$vars, $template_variables = array() ) {
        $template_id = $vars['_template'];

        $blocks = array(
			'after'  => array(),
			'before' => array(),
		);
        // Merge blocks from parent.
        // TODO: how do we handle cases where the parent says it is going to handle a block and a "part" should do that?
        // Maybe we should not process blocks for "parts" and just use whatever the calling template had?
        if ( isset( $vars['blocks'] ) && $vars['blocks'] ) {
            foreach ( $vars['blocks'] as $pos => $bl ) {
                $vars[ '#inherited_' . $pos ] = array(
					'position' => $pos,
					'value'    => $bl,
					'weight'   => 0,
				);
            }
        }
        $vars['blocks'] = array();

        // Current theme info.
        $current_theme = $this->get_active_theme_data();

        if ( isset( $current_theme->template_variables->{$template_id} ) ) {
            $theme_vars = array_merge( $current_theme->template_variables->{$template_id}, $template_variables );
        } else {
            $theme_vars = $template_variables;
        }

        foreach ( $vars as $var => $content ) {
            if ( '#' != $var[0] ) {
                continue;
            }

            $new_key      = substr( $var, 1 );
            $var_position = $content['position'];
            $var_value    = $content['value'];
            $var_weight   = $content['weight'];

            if ( ! in_array( $new_key, $theme_vars, true ) ) {
                if ( isset( $blocks[ $var_position ] ) ) {
                    if ( ! isset( $blocks[ $var_position ][ $var_weight ] ) ) {
                        $blocks[ $var_position ][ $var_weight ] = array();
                    }

                    $blocks[ $var_position ][ $var_weight ][ $new_key ] = $var_value;
                } else {
                    $vars[ $new_key ] = $var_value;
                }
            } else {
                $vars[ $new_key ] = $var_value;
            }

            unset( $vars[ $var ] );
        }

        // Sort blocks.
        foreach ( $blocks as $block_id => &$block_content ) {
            $vars['blocks'][ $block_id ] = '';

            if ( ! $block_content ) {
                continue;
            }

            ksort( $block_content, SORT_NUMERIC );

            foreach ( $block_content as $prio => $c ) {
                $vars['blocks'][ $block_id ] .= implode( '', $c );
            }
        }
    }

	public function locate_template( $id ) {
        $id = str_replace( '.tpl.php', '', $id );

        if ( isset( $this->cache['templates'][ $id ] ) ) {
            return $this->cache['templates'][ $id ];
        }

        $filename = str_replace( ' ', '-', $id ) . '.tpl.php';
		$path     = locate_template( 'business-directory/' . $filename );

        // Find the template.
        foreach ( $this->template_dirs as $p ) {
			if ( empty( $path ) && file_exists( $p . $filename ) ) {
				$path = $p . $filename;
			}

			if ( $path ) {
				/**
				 * Allow override since the order isn't the most dependable indicator.
				 *
				 * @since 5.9.2
				 */
				$path = apply_filters( 'wpbdp_use_template_' . $id, $path );
				if ( $path ) {
					break;
				}
            }
        }

        if ( $path ) {
            $this->cache['templates'][ $id ] = $path;
        }

		return empty( $path ) ? false : $path;
    }

	/**
	 * @since 5.13.2
	 */
	public function template_has_override( $id ) {
		unset( $this->template_dirs['core'] );
		$template = $this->locate_template( $id );
		$this->add_core_template_dir();
		return $template;
	}

	public function install_theme( $file ) {
		$themes_dir                       = wp_normalize_path( $this->get_themes_dir() );
        list( $temp_dir, $unzipped_dir, ) = WPBDP_FS::unzip_to_temp_dir( $file );
        $package_dir                      = $unzipped_dir;

        // Search for a dir containing 'theme.json'.
        $files = WPBDP_FS::ls( $unzipped_dir, 'recursive=1' );
        foreach ( $files as $f ) {
            if ( 'theme.json' == basename( $f ) ) {
                $package_dir = dirname( $f );
                break;
            }
        }

        if ( ! file_exists( WPBDP_FS::join( $package_dir, 'theme.json' ) ) ) {
            WPBDP_FS::rmdir( $temp_dir );
            return new WP_Error(
                'no-theme-file',
                __( 'ZIP file is not a valid Business Directory theme file.', 'business-directory-plugin' )
            );
        }

        if ( ! WPBDP_FS::mkdir( $themes_dir ) ) {
            WPBDP_FS::rmdir( $temp_dir );
            return new WP_Error(
                'no-themes-directory',
                _x( 'Could not create themes directory.', 'themes', 'business-directory-plugin' )
            );
        }

        $dest_dir = $themes_dir . basename( $package_dir );

        if ( ! WPBDP_FS::rmdir( $dest_dir ) ) {
            WPBDP_FS::rmdir( $temp_dir );
            return new WP_Error(
                'old-theme-not-removed',
                sprintf(
                    _x( 'Could not remove previous theme directory "%s".', 'themes', 'business-directory-plugin' ),
                    $dest_dir
                )
            );
        }

        if ( ! WPBDP_FS::movedir( $package_dir, $themes_dir ) ) {
            WPBDP_FS::rmdir( $temp_dir );
            return new WP_Error( 'theme-not-copied', _x( 'Could not move new theme into theme directory.', 'themes', 'business-directory-plugin' ) );
        }

        WPBDP_FS::rmdir( $temp_dir );

        return $dest_dir;
    }

	public function sync_settings() {
		_deprecated_function( __METHOD__, '5.0' );
	}
}

function wpbdp_x_render( $template_id, $vars = array(), $wrapper = '' ) {
    global $wpbdp;

    if ( $wrapper && ! isset( $vars['_wrapper'] ) ) {
        $vars['_wrapper'] = $wrapper;
    }

    return $wpbdp->themes->render( $template_id, $vars );
}

function wpbdp_x_render_page( $template_id, $vars = array() ) {
    return wpbdp_x_render( $template_id, $vars, 'page' );
}

/**
 * Used when we want a BD theme to be able to override.
 */
function wpbdp_x_part( $template_id, $vars = array() ) {
    global $wpbdp;
	$echo = ! isset( $vars['echo'] ) || $vars['echo'] === true;

	// Temporary reverse compatibilty
	if ( isset( $vars['images'] ) && $template_id === 'parts/listing-images' ) {
		_deprecated_argument( __FUNCTION__, '5.13.1', '$vars[images] has been replaced with $vars[extra_images]' );
		$vars['extra_images'] = $vars['images'];
		unset( $vars['images'] );
	}

	$part = $wpbdp->themes->render_part( $template_id, $vars );
	if ( ! $echo ) {
		return $part;
	}
	echo $part;
}

function wpbdp_add_template_dir( $dir_or_file ) {
    global $wpbdp;
    return $wpbdp->themes->add_template_dir( $dir_or_file );
}

