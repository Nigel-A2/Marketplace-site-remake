<?php
/**
 * Main Business Directory class.
 *
 * @package WPBDP
 */
final class WPBDP {

    public $_query_stack = array();

    private $_db_version = null;

	public $settings = null;

	/**
	 * When excerpt listings are displayed, add the fee color to add to css.
	 */
	public $fee_colors = array();

    public function __construct() {
		$this->_db_version = get_option( 'wpbdp-db-version', null );
		if ( $this->_db_version === null ) {
			$this->_db_version = get_option( 'wpbusdirman_db_version', null );
		}
        $this->setup_constants();
        $this->includes();
        $this->hooks();
    }

    private function setup_constants() {
		define( 'WPBDP_VERSION', '6.3.1' );

        define( 'WPBDP_PATH', wp_normalize_path( plugin_dir_path( WPBDP_PLUGIN_FILE ) ) );
        define( 'WPBDP_INC', trailingslashit( WPBDP_PATH . 'includes' ) );
        define( 'WPBDP_TEMPLATES_PATH', WPBDP_PATH . 'templates' );

        define( 'WPBDP_POST_TYPE', 'wpbdp_listing' );
        define( 'WPBDP_CATEGORY_TAX', 'wpbdp_category' );
        define( 'WPBDP_TAGS_TAX', 'wpbdp_tag' );

        //Plugin url
        define( 'WPBDP_URL', trailingslashit( plugins_url( '/', WPBDP_PLUGIN_FILE ) ) );

        //Assets url
        define( 'WPBDP_ASSETS_URL', WPBDP_URL . 'assets/' );
    }

    private function includes() {
		require_once WPBDP_INC . 'helpers/class-app.php';

        // Make DBO framework available to everyone.
        require_once WPBDP_INC . 'db/class-db-model.php';

		require_once WPBDP_PATH . 'includes/admin/class-education.php';

        require_once WPBDP_INC . 'abstracts/class-view.php';

        require_once WPBDP_INC . 'class-modules.php';
        require_once WPBDP_INC . 'licensing.php';

        require_once WPBDP_INC . 'form-fields.php';
        require_once WPBDP_INC . 'payment.php';
        require_once WPBDP_PATH . 'includes/class-payment-gateways.php';
        require_once WPBDP_INC . 'installer.php';

        require_once WPBDP_INC . 'class-cron.php';

        require_once WPBDP_INC . 'helpers/class-currency-helper.php';
        require_once WPBDP_INC . 'admin/settings/class-settings.php';

        require_once WPBDP_INC . 'helpers/functions/general.php';
        require_once WPBDP_INC . 'utils.php';

        require_once WPBDP_INC . 'helpers/listing_flagging.php';

        require_once WPBDP_INC . 'class-cpt-integration.php';
        require_once WPBDP_INC . 'class-listing-expiration.php';
        require_once WPBDP_INC . 'class-listing-email-notification.php';
        require_once WPBDP_INC . 'class-abandoned-payment-notification.php';

        require_once WPBDP_INC . 'compatibility/class-compat.php';
        require_once WPBDP_INC . 'class-rewrite.php';

        require_once WPBDP_INC . 'class-assets.php';
        require_once WPBDP_INC . 'class-meta.php';
        require_once WPBDP_INC . 'widgets/class-widgets.php';

        if ( wpbdp_is_request( 'frontend' ) ) {
            require_once WPBDP_INC . 'helpers/functions/templates-ui.php';
            require_once WPBDP_INC . 'template-sections.php';
            require_once WPBDP_INC . 'class-shortcodes.php';
            require_once WPBDP_INC . 'class-recaptcha.php';
            require_once WPBDP_INC . 'class-query-integration.php';
            require_once WPBDP_INC . 'class-dispatcher.php';
            require_once WPBDP_INC . 'class-wordpress-template-integration.php';
            require_once WPBDP_INC . 'helpers/class-seo.php';
        }

        require_once WPBDP_INC . 'themes.php';

        if ( wpbdp_is_request( 'admin' ) ) {
            require_once WPBDP_INC . 'admin/class-admin.php';
            require_once WPBDP_INC . 'admin/class-personal-data-privacy.php';
        }

        require_once WPBDP_INC . 'helpers/class-access-keys-sender.php';
    }

    /**
     * @since 5.2.1 Removed usage of create_function().
     */
    private function hooks() {
        register_activation_hook( WPBDP_PLUGIN_FILE, array( $this, 'plugin_activation' ) );
        register_deactivation_hook( WPBDP_PLUGIN_FILE, array( $this, 'plugin_deactivation' ) );

        add_action( 'init', array( $this, 'init' ), 0 );
		self::translation_filters();
        add_filter( 'plugin_action_links_' . plugin_basename( WPBDP_PLUGIN_FILE ), array( $this, 'plugin_action_links' ) );

		// Clear cache of page IDs when a page is created, trashed, or saved.
        add_action( 'save_post_page', 'wpbdp_delete_page_ids_cache' );

        // AJAX actions.
        // TODO: Use Dispatcher AJAX support instead of hardcoding these actions here.
        add_action( 'wp_ajax_wpbdp-listing-submit-image-upload', array( &$this, 'ajax_listing_submit_image_upload' ) );
        add_action( 'wp_ajax_nopriv_wpbdp-listing-submit-image-upload', array( &$this, 'ajax_listing_submit_image_upload' ) );
        add_action( 'wp_ajax_wpbdp-listing-submit-image-delete', array( &$this, 'ajax_listing_submit_image_delete' ) );
        add_action( 'wp_ajax_nopriv_wpbdp-listing-submit-image-delete', array( &$this, 'ajax_listing_submit_image_delete' ) );
        add_action( 'wp_ajax_wpbdp-listing-media-image', array( &$this, 'ajax_listing_media_image' ) );

        add_action( 'plugins_loaded', array( $this, 'register_cache_groups' ) );
        add_action( 'switch_blog', array( $this, 'register_cache_groups' ) );
    }

    public function init() {

        $this->load_textdomain();

        $this->form_fields = WPBDP_FormFields::instance();
        $this->formfields = $this->form_fields; // Backwards compat.

        $this->settings = new WPBDP__Settings();
        $this->settings->bootstrap();

        $this->cpt_integration = new WPBDP__CPT_Integration();

        $this->licensing = new WPBDP_Licensing();
        $this->modules = new WPBDP__Modules();

        $this->themes = new WPBDP_Themes();

        $this->installer = new WPBDP_Installer( $this->_db_version );
        try {
            $this->installer->install();
        } catch ( Exception $e ) {
            $this->installer->show_installation_error( $e );
            return;
        }

        $this->fees = new WPBDP_Fees_API();

        if ( wpbdp_is_request( 'admin' ) ) {
            // Make sure WPBDP_Admin class file was loaded before instantiate. See #4346.
            if ( ! class_exists( 'WPBDP_Admin' ) ) {
                require_once WPBDP_INC . 'admin/class-admin.php';
            }
            if ( ! class_exists( 'WPBDP_Personal_Data_Privacy' ) ) {
                require_once WPBDP_INC . 'admin/class-personal-data-privacy.php';
            }

            $this->admin   = new WPBDP_Admin();
            $this->privacy = new WPBDP_Personal_Data_Privacy();
        }

		$manual_upgrade = get_option( 'wpbdp-manual-upgrade-pending', array() );
		if ( $manual_upgrade && $this->installer->setup_manual_upgrade() ) {
			add_shortcode( 'businessdirectory', array( $this, 'frontend_manual_upgrade_msg' ) );
			add_shortcode( 'business-directory', array( $this, 'frontend_manual_upgrade_msg' ) );

			// XXX: Temporary fix to disable features until a pending Manual
			// Upgrades have been performed.
			//
			// Ideally, these hooks would be registered later, making the following
			// lines unnecessary.
			remove_action( 'wp_footer', array( $this->themes, 'fee_specific_coloring' ), 999 );
			remove_action( 'admin_notices', array( &$this->licensing, 'admin_notices' ) );

			return;
        }

        $this->modules->load_i18n();
        $this->modules->init(); // Change to something we can fire in WPBDP__Modules to register modules.

        $this->payment_gateways = new WPBDP__Payment_Gateways();

		// Load before wpbdp_register_settings hook runs.
		require_once WPBDP_PATH . 'includes/compatibility/class-fa-compat.php';
		new WPBDP_FA_Compat();

		do_action( 'wpbdp_modules_loaded' );

        do_action_ref_array( 'wpbdp_register_settings', array( &$this->settings ) );
		do_action( 'wpbdp_register_fields', $this->formfields );
		do_action( 'wpbdp_modules_init' );

        $this->listings = new WPBDP_Listings_API();
        $this->payments = new WPBDP_PaymentsAPI();

        $this->cpt_integration->register_hooks();

        $this->cron = new WPBDP__Cron();

        $this->setup_email_notifications();

        $this->assets = new WPBDP__Assets();
        $this->widgets = new WPBDP__Widgets();

        // We need to ask for frontend requests first, because
        // wpbdp_is_request( 'admin' ) or is_admin() return true for ajax
        // requests made from the frontend.
        if ( wpbdp_is_request( 'frontend' ) ) {
            $this->query_integration = new WPBDP__Query_Integration();
            $this->dispatcher = new WPBDP__Dispatcher();
            $this->shortcodes = new WPBDP__Shortcodes();
            $this->template_integration = new WPBDP__WordPress_Template_Integration();

            $this->meta = new WPBDP__Meta();
            $this->recaptcha = new WPBDP_reCAPTCHA();
        }

		$this->compat = new WPBDP_Compat();
        $this->rewrite = new WPBDP__Rewrite();

        do_action( 'wpbdp_loaded' );
    }

	/**
	 * @since 5.13
	 */
	public function translation_filters() {
		add_filter( 'gettext', array( &$this, 'use_custom_strings' ), 10, 3 );
		add_filter( 'gettext_with_context', array( &$this, 'use_custom_context_strings' ), 10, 4 );
	}

	/**
	 * Remove filters when an infinite loop is possible.
	 *
	 * @since 5.13
	 */
	private function remove_translation_filters() {
		remove_filter( 'gettext', array( &$this, 'use_custom_strings' ), 10 );
		remove_filter( 'gettext_with_context', array( &$this, 'use_custom_context_strings' ), 10 );
	}

	/**
	 * Replace default naming in strings with the setting.
	 *
	 * @since 5.13
	 */
	public function use_custom_strings( $translation, $text, $domain ) {
		$domains = array( 'business-directory-plugin' );
		$is_bd   = in_array( $domain, $domains ) || strpos( $domain, 'wpbdp' ) === 0;
		$is_admin = is_admin() && ! wp_doing_ajax();
		if ( ! $is_bd || $is_admin ) {
			return $translation;
		}

		$this->remove_translation_filters();
		$translation = WPBDP_App_Helper::replace_labels( $translation );
		$this->translation_filters();

		return $translation;
	}

	/**
	 * @since 5.13
	 */
	public function use_custom_context_strings( $translation, $text, $context, $domain ) {
		return $this->use_custom_strings( $translation, $text, $domain );
	}

	/**
	 * Is this a page we should be changing?
	 *
	 * @since 5.8.2
	 *
	 * @return bool
	 */
	public function is_bd_page() {
		return WPBDP_App_Helper::is_bd_page();
	}

	/**
	 * @since 5.8.2
	 */
	public function is_bd_post_page() {
		return WPBDP_App_Helper::is_bd_post_page();
	}

	/**
	 * Only activate BD plugins during BD-related AJAX requests
	 *
	 * @since 5.12.1
	 * @deprecated 5.13.2
	 *
	 * @param  array $plugins
	 * @return array $plugins
	 */
	public function run_ajax_compat_mode( $plugins ) {
		_deprecated_function( __METHOD__, '5.13.2' );
		return array_filter( $plugins, array( $this, 'keep_only_bd_plugins' ) );
	}

	/**
	 * Check if this is a BD plugin.
	 *
	 * @param string $plugin
	 *
	 * @since 5.12.1
	 * @deprecated 5.13.2
	 *
	 * @return boolean
	 */
	private function keep_only_bd_plugins( $plugin ) {
		_deprecated_function( __METHOD__, '5.13.2' );
		return false !== strpos( $plugin, 'business-directory-' );
	}

    public function setup_email_notifications() {
        global $wpdb;

        $this->listing_expiration = new WPBDP__Listing_Expiration();
        $this->listing_email_notification = new WPBDP__Listing_Email_Notification();

        if ( $this->settings->get_option( 'payment-abandonment' ) ) {
            $abandoned_payment_notification = new WPBDP__Abandoned_Payment_Notification( $this->settings, $wpdb );
            add_action( 'wpbdp_hourly_events', array( $abandoned_payment_notification, 'send_abandoned_payment_notifications' ) );
        }
    }

    public function register_cache_groups() {
        wp_cache_add_non_persistent_groups( array( 'wpbdp pages', 'wpbdp fees', 'wpbdp submit state', 'wpbdp' ) );
    }

    private function load_textdomain() {
        $languages_dir = trailingslashit( basename( (string) WPBDP_PATH ) ) . 'languages';
        load_plugin_textdomain( 'business-directory-plugin', false, $languages_dir );
    }

    public function plugin_activation() {
		add_action( 'shutdown', 'flush_rewrite_rules' );
		wpbdp_delete_page_ids_cache();
    }

    public function plugin_deactivation() {
        wp_clear_scheduled_hook( 'wpbdp_hourly_events' );
        wp_clear_scheduled_hook( 'wpbdp_daily_events' );
    }

	/**
	 * Adds a settings link to the plugins page
	 */
	public function plugin_action_links( $links ) {
		$add_links = array();

		if ( ! WPBDP_Admin_Education::is_installed( 'premium' ) ) {
			$add_links[] = '<a href="' . esc_url( wpbdp_admin_upgrade_link( 'plugin-row' ) ) . '" target="_blank" rel="noopener" style="color:#1da867" class="wpbdp-upgrade-link"><b>' . esc_html__( 'Upgrade to Premium', 'business-directory-plugin' ) . '</b></a>';
		}

		$add_links['settings'] = '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_settings' ) ) . '">' . esc_html__( 'Settings', 'business-directory-plugin' ) . '</a>';

		return array_merge( $add_links, $links );
	}

    public function is_plugin_page() {
        if ( wpbdp_current_view() ) {
            return true;
        }

        global $wp_query;

        if ( ! empty( $wp_query->wpbdp_our_query ) || ! empty( $wp_query->wpbdp_view ) )
            return true;

        global $post;

        if ( $post && $this->is_supported_post_type( $post->post_type ) && $this->shortcodes ) {
            foreach ( array_keys( $this->shortcodes->get_shortcodes() ) as $shortcode ) {
                if ( apply_filters( 'wpbdp_has_shortcode', wpbdp_has_shortcode( $post->post_content, $shortcode ), $post, $shortcode ) ) {
                    return true;
                }
            }
        }

		if ( $post && WPBDP_POST_TYPE === $post->post_type ) {
			return true;
		}

		// Load CSS in Elementor templates.
		$is_elementor = $post && $post->post_type === 'elementor_library';

		return $is_elementor;
    }

    public function get_post_type() {
        return WPBDP_POST_TYPE;
    }

    public function get_post_type_category() {
        return WPBDP_CATEGORY_TAX;
    }

    public function get_post_type_tags() {
        return WPBDP_TAGS_TAX;
    }

    public function get_supported_post_types() {
        return apply_filters( 'wpbdp_supported_post_types', array( 'page', 'post' ) );
    }

    public function is_supported_post_type( $post_type ) {
        return in_array( $post_type, $this->get_supported_post_types() );
    }

    /**
     * @deprecated since 5.0. Remove when found, kept for backwards compat.
     */
    public function is_debug_on() {
		_deprecated_function( __METHOD__, '5.0' );
        return false;
    }

    // TODO: better validation.
    public function ajax_listing_submit_image_upload() {
        $res = new WPBDP_AJAX_Response();

        $listing_id = intval( $_REQUEST['listing_id'] );

        if ( ! $listing_id )
            return $res->send_error();

        $content_range = wpbdp_get_server_value( 'HTTP_CONTENT_RANGE' );
        $size = null;

        if ( $content_range ) {
            $content_range = preg_split( '/[^0-9]+/', $content_range );
            $size          = $content_range ? $content_range[3] : null;
        }

        $attachments = array();

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        $files = wpbdp_flatten_files_array( isset( $_FILES['images'] ) ? $_FILES['images'] : array() );
        $errors = array();

        $listing = WPBDP_Listing::get( $listing_id );
        $slots_available = 0;
		$plan = $listing->get_fee_plan();
		if ( ! $plan ) {
			return $res->send_error( _x( 'Please select a plan before uploading images to the listing', 'listing image upload', 'business-directory-plugin' ) );
		}

		$slots_available = absint( $plan->fee_images ) - absint( $_POST['images_count'] );
		if ( 0 >= $slots_available ) {
			return $res->send_error( _x( 'Can not upload any more images for this listing.', 'listing image upload', 'business-directory-plugin' ) );
		} elseif ( $slots_available < count( $files ) ) {
			return $res->send_error(
				sprintf(
					_nx(
						'You\'re trying to upload %1$d images, but only have %2$d slot available. Please adjust your selection.',
						'You\'re trying to upload %1$d images, but only have %2$d slots available. Please adjust your selection.',
						$slots_available,
						'listing image upload',
						'business-directory-plugin'
					),
					count( $files ),
					$slots_available
				)
			);
		}

        foreach ( $files as $i => $file ) {
            $image_error = '';
			$attachment_id = wpbdp_media_upload(
				$file,
				true,
				true,
				array(
					'image'      => true,
					'min-size'   => intval( wpbdp_get_option( 'image-min-filesize' ) ) * 1024,
					'max-size'   => intval( wpbdp_get_option( 'image-max-filesize' ) ) * 1024,
					'min-width'  => wpbdp_get_option( 'image-min-width' ),
					'min-height' => wpbdp_get_option( 'image-min-height' ),
				),
				$image_error
			); // TODO: handle errors.

			if ( $image_error ) {
				$errors[ $file['name'] ] = $image_error;
			} else {
				$attachments[] = $attachment_id;
			}
        }

        $html = '';
        foreach ( $attachments as $attachment_id ) {
			$html .= wpbdp_render(
				'submit-listing-images-single',
				array(
					'image_id' => $attachment_id,
					'listing_id' => $listing_id,
				),
				false
			);
        }

		$has_images = $listing->get_images( 'ids' );
        $listing->set_images( $attachments, true );

		// Maybe set thumbnail if there aren't already images on this listing.
		if ( ! $has_images ) {
			$image_id = reset( $attachments );
			$listing->set_thumbnail_id( $image_id );
		}

        if ( $errors ) {
            $error_msg = '';

            foreach ( $errors as $fname => $error )
                $error_msg .= sprintf( '&#149; %s: %s', $fname, $error ) . '<br />';

            $res->add( 'uploadErrors', $error_msg );
        }

        $res->add( 'is_admin', current_user_can( 'administrator' ) );
        $res->add( 'slots_available', $slots_available );
        $res->add( 'attachmentIds', $attachments );
        $res->add( 'html', $html );
        $res->send();
    }

    public function ajax_listing_submit_image_delete() {
        $res = new WPBDP_AJAX_Response();

		$image_id   = wpbdp_get_var( array( 'param' => 'image_id', 'sanitize' => 'intval' ), 'request' );
		$listing_id = wpbdp_get_var( array( 'param' => 'listing_id', 'sanitize' => 'intval' ), 'request' );
		$nonce      = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'request' );

        if ( ! $image_id || ! $listing_id || ! wp_verify_nonce( $nonce, 'delete-listing-' . $listing_id . '-image-' . $image_id ) )
            $res->send_error();

        $listing = wpbdp_get_listing( $listing_id );

        if ( ! $listing ) {
            $res->send_error();
        }

		// Remove from images list.
		$listing->remove_image( $image_id );

        $res->add( 'imageId', $image_id );
        $res->send();
    }

    public function ajax_listing_media_image() {
		$json_data = array(
			'errorElement'   => '.media-area-and-conditions',
			'previewElement' => '#wpbdp-uploaded-images',
			'source'         => 'listing_images'
		);
		$listing_id = wpbdp_get_var( array( 'param' => 'listing_id', 'sanitize' => 'intval' ), 'request' );

		if ( ! $listing_id ) {
			$json_data['errors'] = esc_html__( 'Could not find listing ID', 'business-directory-plugin' );
			wp_send_json_error( $json_data );
		}

		$nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'request' );

		if ( ! wp_verify_nonce( $nonce, 'listing-' . $listing_id . '-image-from-media' ) ) {
			$json_data['errors'] = esc_html__( 'Could not verify the image upload request. If problem persists contact site admin.', 'business-directory-plugin' );
			wp_send_json_error( $json_data );
		}

		$image_ids = wpbdp_get_var( array( 'param' => 'image_ids', 'default' => array() ), 'request' );

		if ( ! $image_ids ) {
            $json_data['errors'] = esc_html__( 'Could not find image ID', 'business-directory-plugin' );
			wp_send_json_error( $json_data );
        }

        $image_ids = is_array( $image_ids ) ? $image_ids : array( $image_ids );
		WPBDP_Listing_Image::maybe_set_post_parent( $image_ids, $listing_id );

        $html = '';
		foreach ( $image_ids as $id ) {
            $html .= wpbdp_render(
                'submit-listing-images-single',
                array(
                    'image_id' => $id,
                    'listing_id' => $listing_id
                ),
                false
            );
        }

		$json_data['html'] = $html;

		wp_send_json_success( $json_data );
    }

    public function frontend_manual_upgrade_msg() {
        wp_enqueue_style( 'wpbdp-base-css' );

        if ( current_user_can( 'administrator' ) ) {
            return wpbdp_render_msg(
                str_replace(
                    '<a>',
                    '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp-upgrade-page' ) ) . '">',
                    __( 'The directory features are disabled at this time because a <a>manual upgrade</a> is pending.', 'business-directory-plugin' )
                ),
                'error'
            );
        }

        return wpbdp_render_msg(
            __( 'The directory is not available at this time. Please try again in a few minutes or contact the administrator if the problem persists.', 'business-directory-plugin' ),
            'error'
        );
    }

    public function get_db_version() {
        return $this->_db_version;
    }

}
