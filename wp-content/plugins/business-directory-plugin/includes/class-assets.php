<?php
/**
 * Assets to be used by Business Directory Plugin
 *
 * @package BDP/Includes/
 */

/**
 * Class WPBDP__Assets
 *
 * @since 5.0
 */
class WPBDP__Assets {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'register_common_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_common_scripts' ) );

        // Scripts & styles.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css_override' ), 9999, 0 );

		// Admin
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * Registers scripts and styles that can be used either by frontend or backend code.
     * The scripts are just registered, not enqueued.
     *
     * @since 3.4
     */
    public function register_common_scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        wp_register_script(
            'jquery-file-upload-iframe-transport',
            WPBDP_ASSETS_URL . 'vendor/jQuery-File-Upload/js/jquery.iframe-transport.js',
            array(),
			'10.32.0',
			true
        );

        wp_register_script(
            'jquery-file-upload',
            WPBDP_ASSETS_URL . 'vendor/jQuery-File-Upload/js/jquery.fileupload.js',
            array( 'jquery', 'jquery-ui-widget', 'jquery-file-upload-iframe-transport' ),
			'10.32.0',
			true
        );

        $this->maybe_register_script(
            'breakpoints.js',
            WPBDP_ASSETS_URL . 'vendor/jquery-breakpoints/jquery-breakpoints' . $min . '.js',
            array( 'jquery' ),
            '0.0.11',
            true
        );

        // Views.
        wp_register_script(
            'wpbdp-checkout',
            WPBDP_ASSETS_URL . 'js/checkout.js',
            array( 'wpbdp-js' ),
            WPBDP_VERSION,
			true
        );

        // Drag & Drop.
        wp_register_script(
            'wpbdp-dnd-upload',
            WPBDP_ASSETS_URL . 'js/dnd-upload' . $min . '.js',
            array( 'jquery-file-upload' ),
            WPBDP_VERSION,
			true
        );

        $this->register_select2();

		wp_register_style( 'wpbdp-base-css', WPBDP_ASSETS_URL . 'css/wpbdp.min.css', array(), WPBDP_VERSION );
    }

	private function register_select2() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // Use Select2 styles and scripts from selectWoo https://woocommerce.wordpress.com/2017/08/08/selectwoo-an-accessible-replacement-for-select2/.
        wp_register_style(
            'wpbdp-js-select2-css',
            WPBDP_ASSETS_URL . 'vendor/selectWoo/css/selectWoo.min.css',
            array(),
            '4.0.5'
        );

        wp_register_script(
            'wpbdp-js-select2',
			WPBDP_ASSETS_URL . 'vendor/selectWoo/js/selectWoo.full' . $min . '.js',
            array( 'jquery' ),
            '4.0.5',
			true
        );
	}

	/**
	 * @since 5.8.2
	 */
	public function enqueue_select2() {
		$this->register_select2();
		wp_enqueue_script( 'wpbdp-js-select2' );
		wp_enqueue_style( 'wpbdp-js-select2-css' );
	}

    private function maybe_register_script( $handle, $src, $deps, $ver, $in_footer = false ) {
        $scripts = wp_scripts();

        if ( isset( $scripts->registered[ $handle ] ) ) {
            $registered_script = $scripts->registered[ $handle ];
        } else {
            $registered_script = null;
        }

        if ( $registered_script && version_compare( $registered_script->ver, $ver, '>=' ) ) {
            return;
        }

        if ( $registered_script ) {
            wp_deregister_script( $handle );
        }

        wp_register_script( $handle, $src, $deps, $ver, $in_footer );
    }

    public function enqueue_scripts() {
        $enqueue_scripts_and_styles = apply_filters( 'wpbdp_should_enqueue_scripts_and_styles', wpbdp()->is_plugin_page() );
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style(
            'wpbdp-widgets',
            WPBDP_ASSETS_URL . 'css/widgets.min.css',
            array(),
            WPBDP_VERSION
        );

        if ( ! $enqueue_scripts_and_styles ) {
            return;
        }

        wp_register_script(
            'wpbdp-js',
			WPBDP_ASSETS_URL . 'js/wpbdp' . $min . '.js',
            array(
                'jquery',
                'breakpoints.js',
                'jquery-ui-sortable',
            ),
            WPBDP_VERSION,
			true
        );

		$this->global_localize( 'wpbdp-js' );

        wp_enqueue_script( 'wpbdp-dnd-upload' );

        if ( wpbdp_get_option( 'use-thickbox' ) ) {
            add_thickbox();
        }

        wp_enqueue_style( 'wpbdp-base-css' );
        wp_enqueue_script( 'wpbdp-js' );

		$this->load_css();

        do_action( 'wpbdp_enqueue_scripts' );

        // enable legacy css (should be removed in a future release) XXX
        if ( _wpbdp_template_mode( 'single' ) == 'template' || _wpbdp_template_mode( 'category' ) == 'template' ) {
            wp_enqueue_style(
                'wpbdp-legacy-css',
                WPBDP_ASSETS_URL . 'css/wpbdp-legacy.min.css',
                array(),
                WPBDP_VERSION
            );
        }
    }

	/**
	 * @since 5.9.2
	 */
	public function global_localize( $script = 'wpbdp-js' ) {
		$global = array(
			'ajaxurl' => wpbdp_ajaxurl(),
			'nonce'   => wp_create_nonce( 'wpbdp_ajax' ),
		);
		if ( $script === 'wpbdp-admin-js' ) {
			$global['assets']   = WPBDP_ASSETS_URL;
			$global['cancel']   = __( 'Cancel', 'business-directory-plugin' );
			$global['continue'] = __( 'Continue', 'business-directory-plugin' );
			$global['confirm']  = __( 'Are you sure?', 'business-directory-plugin' );
		}
		wp_localize_script( $script, 'wpbdp_global', $global );
	}

    public function load_css() {
		$rootline_color    = sanitize_hex_color( wpbdp_get_option( 'rootline-color' ) );
		$thumbnail_width   = wpbdp_get_option( 'thumbnail-width' );
		$thumbnail_height  = wpbdp_get_option( 'thumbnail-height' );

		if ( ! $rootline_color ) {
			$rootline_color = '#569AF6';
		}

		$css = 'html{
			--bd-main-color:' . $rootline_color . ';
			--bd-main-color-20:' . $rootline_color . '33;
			--bd-main-color-8:' . $rootline_color . '14;
			--bd-thumbnail-width:' . esc_attr( $thumbnail_width ) . 'px;
			--bd-thumbnail-height:' . esc_attr( $thumbnail_height ) . 'px;
		}';

		wp_add_inline_style( 'wpbdp-base-css', WPBDP_App_Helper::minimize_code( $css ) );
    }

    /**
     * @since 3.5.3
     */
    public function enqueue_css_override() {
        $stylesheet_dir     = trailingslashit( get_stylesheet_directory() );
        $stylesheet_dir_uri = trailingslashit( get_stylesheet_directory_uri() );
        $template_dir       = trailingslashit( get_template_directory() );
        $template_dir_uri   = trailingslashit( get_template_directory_uri() );

        $folders_uris = array(
            array( trailingslashit( WP_PLUGIN_DIR ), trailingslashit( WP_PLUGIN_URL ) ),
            array( $stylesheet_dir, $stylesheet_dir_uri ),
            array( $stylesheet_dir . 'css/', $stylesheet_dir_uri . 'css/' ),
        );

        if ( $template_dir != $stylesheet_dir ) {
            $folders_uris[] = array( $template_dir, $template_dir_uri );
            $folders_uris[] = array( $template_dir . 'css/', $template_dir_uri . 'css/' );
        }

        $filenames = array(
			'wpbdp.css',
			'wpbusdirman.css',
			'wpbdp_custom_style.css',
			'wpbdp_custom_styles.css',
			'wpbdm_custom_style.css',
			'wpbdm_custom_styles.css',
		);

        $n = 0;
        foreach ( $folders_uris as $folder_uri ) {
            list( $dir, $uri ) = $folder_uri;

            foreach ( $filenames as $f ) {
                if ( file_exists( $dir . $f ) ) {
                    wp_enqueue_style(
                        'wpbdp-custom-' . $n,
                        $uri . $f,
                        array(),
                        WPBDP_VERSION
                    );
                    $n++;
                }
            }
        }
    }

    /**
     * Load resources on admin page
     *
     * @param bool $force Force reloading the resources.
	 *
	 * @since 5.18 Deprecate the $force parameter to not load on non BD pages.
     */
	public function enqueue_admin_scripts( $force = false ) {
		if ( $force === true ) {
			_deprecated_argument( __FUNCTION__, '5.17.2', 'Loading admin scripts can no longer be forced. Use the wpbdp_is_bd_page hook instead.' );
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( ! WPBDP_App_Helper::is_bd_page() ) {
			wp_enqueue_script( 'wpbdp-wp-admin-js', WPBDP_ASSETS_URL . 'js/wp-admin' . $min . '.js', array( 'jquery' ), WPBDP_VERSION, true );

			return;
		}

		// Add admin body class for parent page class to avoid css conflicts.
		add_filter( 'admin_body_class', array( &$this, 'add_body_class' ) );

		wp_enqueue_style( 'wpbdp-admin', WPBDP_ASSETS_URL . 'css/admin.min.css', array(), WPBDP_VERSION );

		wp_enqueue_style( 'thickbox' );

		wp_enqueue_style( 'wpbdp-base-css' );

		wp_enqueue_script( 'wpbdp-frontend-js', WPBDP_ASSETS_URL . 'js/wpbdp' . $min . '.js', array( 'jquery' ), WPBDP_VERSION, true );

		wp_enqueue_script( 'wpbdp-admin-js', WPBDP_ASSETS_URL . 'js/admin' . $min . '.js', array( 'jquery', 'thickbox', 'jquery-ui-sortable', 'jquery-ui-dialog', 'jquery-ui-tooltip' ), WPBDP_VERSION, true );
		$this->global_localize( 'wpbdp-admin-js' );

		wp_enqueue_script( 'wpbdp-user-selector-js', WPBDP_ASSETS_URL . 'js/user-selector' . $min . '.js', array( 'jquery', 'wpbdp-js-select2' ), WPBDP_VERSION, true );

		wp_enqueue_style( 'wpbdp-js-select2-css' );

		/**
		 * Load additional scripts or styles used only in BD plugin pages.
		 * This hook can be used to load scripts and resources using `wp_enqueue_script` or `wp_enqueue_style` WordPress hooks.
		 *
		 * @since 5.18
		 */
		do_action( 'wpbdp_enqueue_admin_scripts' );

		if ( ! WPBDP_App_Helper::is_bd_post_page() ) {
			return;
		}

		$this->load_css();

		self::load_datepicker();

		wp_enqueue_style(
			'wpbdp-listing-admin-metabox',
			WPBDP_ASSETS_URL . 'css/admin-listing-metabox.min.css',
			array(),
			WPBDP_VERSION
		);

		wp_enqueue_script(
			'wpbdp-admin-listing',
			WPBDP_ASSETS_URL . 'js/admin-listing.min.js',
			array( 'wpbdp-admin-js', 'wpbdp-dnd-upload' ),
			WPBDP_VERSION,
			true
		);

		wp_enqueue_script(
			'wpbdp-admin-listing-metabox',
			WPBDP_ASSETS_URL . 'js/admin-listing-metabox' . $min . '.js',
			array( 'wpbdp-admin-js', 'jquery-ui-datepicker' ),
			WPBDP_VERSION,
			true
		);

		wp_localize_script(
			'wpbdp-admin-listing-metabox',
			'wpbdpListingMetaboxL10n',
			array(
				'planDisplayFormat' => sprintf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'admin.php?page=wpbdp-admin-fees&wpbdp_view=edit-fee&id={{plan_id}}' ) ),
					'{{plan_label}}'
				),
			)
		);

		wp_localize_script(
			'wpbdp-admin-listing',
			'WPBDP_admin_listings_config',
			array(
				'messages' => array(
					'preview_button_tooltip' => __(
						"Preview is only available after you've saved the first draft. This is due to how WordPress stores the data.",
						'business-directory-plugin'
					),
				),
			)
		);
	}

	/**
	 * Add admin body class.
	 * This will be used a wrapper for admin css classes to prevent conflicts with other page styles.
	 *
	 * @param string $admin_body_classes The current admin body classes.
	 *
	 * @since 5.14.3
	 *
	 * @return string $admin_body_classes The body class with the added plugin class.
	 */
	public function add_body_class( $admin_body_classes ) {
		if ( WPBDP_App_Helper::is_bd_page() ) {
			$admin_body_classes = ' wpbdp-admin-page';
		}

		return $admin_body_classes;
	}

	/**
	 * Register resources required in installation only.
	 *
	 * @since 5.18
	 */
	public function register_installation_resources() {
		wp_enqueue_script( 'wpbdp-admin-install-js', WPBDP_ASSETS_URL . 'js/admin-install.min.js', array( 'jquery' ), WPBDP_VERSION, true );
	}

	/**
	 * Load Jquery UI Style.
	 *
	 * @since 6.0
	 */
	public static function load_datepicker() {
		wp_enqueue_script( 'jquery-ui-datepicker' );

		if ( self::is_jquery_ui_css_loaded() ) {
			return;
		}

		wp_enqueue_style(
			'jquery-theme',
			WPBDP_ASSETS_URL . 'css/jquery-ui.css',
			array(),
			WPBDP_VERSION
		);
	}

	/**
	 * Check if Jquery UI CSS is loaded.
	 *
	 * @since 6.0
	 *
	 * @return bool
	 */
	private static function is_jquery_ui_css_loaded() {
		$possible_styles = array( 'jquery-ui', 'jquery-ui-css', 'jquery-theme' );
		foreach ( $possible_styles as $style ) {
			if ( wp_style_is( $style ) ) {
				return true;
			}
		}
		return false;
	}
}
