<?php
/**
 * @package WPBDP\Admin\Settings
 */
final class WPBDP__Settings__Bootstrap {

    public static function register_initial_groups() {
		wpbdp_register_settings_group( 'general', _x( 'General', 'settings', 'business-directory-plugin' ), '', array( 'icon' => 'cog' ) );

		wpbdp_register_settings_group( 'listings', _x( 'Listings', 'settings', 'business-directory-plugin' ), '', array( 'icon' => 'list' ) );
		wpbdp_register_settings_group( 'listings/main', _x( 'General Settings', 'settings', 'business-directory-plugin' ), 'listings' );

		wpbdp_register_settings_group( 'email', __( 'Email', 'business-directory-plugin' ), '', array( 'icon' => 'email' ) );
		wpbdp_register_settings_group( 'email/main', _x( 'General Settings', 'settings', 'business-directory-plugin' ), 'email' );

		wpbdp_register_settings_group( 'payment', _x( 'Payment', 'settings', 'business-directory-plugin' ), '', array( 'icon' => 'money' ) );
		wpbdp_register_settings_group( 'payment/main', _x( 'General Settings', 'settings', 'business-directory-plugin' ), 'payment' );

		wpbdp_register_settings_group( 'appearance', _x( 'Appearance', 'settings', 'business-directory-plugin' ), '', array( 'icon' => 'layout' ) );
    }

    public static function register_initial_settings() {
        self::settings_general();
        self::settings_listings();
        self::settings_email();
        self::settings_payment();
        self::settings_appearance();
		add_action( 'wpbdp_register_settings', __CLASS__ . '::settings_misc', 50 );
    }

    private static function settings_general() {
        wpbdp_register_settings_group( 'general/main', _x( 'General Settings', 'settings', 'business-directory-plugin' ), 'general' );
		wpbdp_register_settings_group( 'upgrade', __( 'License Key', 'business-directory-plugin' ), 'general/main' );

		wpbdp_register_setting(
			array(
				'id'    => 'pro_license',
				'name'  => '',
				'type'  => 'pro_license',
				'group' => 'upgrade',
			)
		);

		wpbdp_register_setting(
			array(
				'id'      => 'uninstall',
				'name'    => '',
				'type'    => 'none',
				'group'   => 'uninstall',
			)
		);

        // Permalinks.
		wpbdp_register_settings_group( 'seo/main', __( 'SEO', 'business-directory-plugin' ), 'general' );
		wpbdp_register_settings_group( 'permalink_settings', _x( 'Permalink Settings', 'settings', 'business-directory-plugin' ), 'seo/main' );
        wpbdp_register_setting(
            array(
                'id'        => 'permalinks-directory-slug',
                'type'      => 'text',
                'name'      => _x( 'Directory Listings Slug', 'settings', 'business-directory-plugin' ),
                'default'   => 'wpbdp_listing',
                'group'     => 'permalink_settings',
                'validator' => 'no-spaces,trim,required',
				'class'     => 'wpbdp-half',
				'requirements' => array( 'disable-cpt' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'        => 'permalinks-category-slug',
                'type'      => 'text',
                'name'      => _x( 'Categories Slug', 'settings', 'business-directory-plugin' ),
				'tooltip'   => _x( 'The slug can\'t be in use by another term. Avoid "category", for instance.', 'settings', 'business-directory-plugin' ),
                'default'   => 'wpbdp_category',
                'group'     => 'permalink_settings',
                'taxonomy'  => WPBDP_CATEGORY_TAX,
                'validator' => 'taxonomy_slug',
				'class'     => 'wpbdp-half',
            )
        );
        wpbdp_register_setting(
            array(
                'id'        => 'permalinks-tags-slug',
                'type'      => 'text',
                'name'      => _x( 'Tags Slug', 'settings', 'business-directory-plugin' ),
				'tooltip'   => _x( 'The slug can\'t be in use by another term. Avoid "tag", for instance.', 'settings', 'business-directory-plugin' ),
                'default'   => 'wpbdp_tag',
                'group'     => 'permalink_settings',
                'taxonomy'  => WPBDP_TAGS_TAX,
                'validator' => 'taxonomy_slug',
				'class'     => 'wpbdp-half',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'permalinks-no-id',
                'type'    => 'toggle',
				'default' => true,
                'name'    => _x( 'Remove listing ID from URLs for better SEO', 'settings', 'business-directory-plugin' ),
                'tooltip' => _x( 'Prior to 3.5.1, we included the ID in the listing URL, like "/business-directory/1809/listing-title".', 'settings', 'business-directory-plugin' ) . ' ' . _x( 'IMPORTANT: subpages of the main directory page cannot be accesed while this setting is checked.', 'admin settings', 'business-directory-plugin' ),
                'group'   => 'permalink_settings',
            )
        );

        // reCAPTCHA.
        wpbdp_register_settings_group(
			'spam',
			'SPAM',
            'general',
            array(
                'desc' => str_replace( '<a>', '<a href="http://www.google.com/recaptcha" target="_blank" rel="noopener">', _x( 'Need API keys for reCAPTCHA? Get them <a>here</a>.', 'settings', 'business-directory-plugin' ) ),
            )
        );
		wpbdp_register_settings_group( 'recaptcha', __( 'Use reCAPTCHA for:', 'business-directory-plugin' ), 'spam' );

        wpbdp_register_setting(
            array(
                'id'    => 'recaptcha-for-submits',
                'type'  => 'checkbox',
                'name'  => __( 'Creating listings', 'business-directory-plugin' ),
                'group' => 'recaptcha',
				'class' => 'wpbdp-half',
            )
        );
		wpbdp_register_setting(
			array(
				'id'    => 'recaptcha-on',
				'type'  => 'checkbox',
				'name'  => __( 'Contact form submissions', 'business-directory-plugin' ),
				'group' => 'recaptcha',
				'class' => 'wpbdp-half',
			)
		);
		wpbdp_register_setting(
			array(
				'id'    => 'recaptcha-for-edits',
				'type'  => 'checkbox',
				'name'  => __( 'Editing listings', 'business-directory-plugin' ),
				'group' => 'recaptcha',
				'class' => 'wpbdp-half',
			)
		);
		wpbdp_register_setting(
            array(
                'id'    => 'recaptcha-for-comments',
                'type'  => 'checkbox',
                'name'  => __( 'Listing comments', 'business-directory-plugin' ),
                'group' => 'recaptcha',
				'class' => 'wpbdp-half',
            )
        );
		wpbdp_register_setting(
			array(
				'id'    => 'recaptcha-for-flagging',
				'type'  => 'checkbox',
				'name'  => __( 'Reporting listings', 'business-directory-plugin' ),
				'group' => 'recaptcha',
				'class' => 'wpbdp-half',
			)
		);
		wpbdp_register_setting(
			array(
				'id'    => 'hide-recaptcha-loggedin',
				'type'  => 'checkbox',
				'name'  => __( 'Logged-out users only', 'business-directory-plugin' ),
				'group' => 'recaptcha',
				'class' => 'wpbdp-half',
			)
		);
        wpbdp_register_setting(
            array(
                'id'      => 'recaptcha-public-key',
                'type'    => 'text',
				'name'    => __( 'Site Key', 'business-directory-plugin' ),
                'default' => '',
                'group'   => 'recaptcha',
				'class'   => 'wpbdp-half',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'recaptcha-private-key',
                'type'    => 'text',
				'name'    => __( 'Secret Key', 'business-directory-plugin' ),
                'default' => '',
                'group'   => 'recaptcha',
				'class'   => 'wpbdp-half',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'recaptcha-version',
                'type'    => 'select',
                'name'    => _x( 'reCAPTCHA version', 'settings', 'business-directory-plugin' ),
                'default' => 'v2',
                'options' => array(
                    'v2' => 'V2',
                    'v3' => 'V3',
                ),
                'group'   => 'recaptcha',
				'grid_classes' => array(
					'left'  => 'wpbdp8',
					'right' => 'wpbdp4'
				),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'recaptcha-threshold',
                'type'    => 'number',
                'name'    => _x( 'reCAPTCHA V3 threshold score', 'settings', 'business-directory-plugin' ),
                'default' => 0.5,
                'min'     => 0,
                'step'    => 0.1,
                'max'     => 1,
				'tooltip' => _x( 'reCAPTCHA v3 returns a score (1.0 is very likely a good interaction, 0.0 is very likely a bot). Based on the score, you can take variable action in the context of your site. You can set here the score threshold, scores under this value will result in reCAPTCHA validation error.', 'settings', 'business-directory-plugin' ),
                'group'   => 'recaptcha',
				'grid_classes' => array(
					'left'  => 'wpbdp8',
					'right' => 'wpbdp4'
				),
            )
        );

		wpbdp_register_settings_group(
			'registration',
			_x( 'Registration', 'settings', 'business-directory-plugin' ),
			'general',
			array(
				'desc' => __( "We expect that a membership plugin supports the 'redirect_to' parameter for the URLs below to work. If the plugin does not support them, these settings will not function as expected.", 'business-directory-plugin' ),
			)
		);
        wpbdp_register_setting(
            array(
                'id'      => 'require-login',
                'type'    => 'toggle',
                'name'    => _x( 'Require login to post listings', 'settings', 'business-directory-plugin' ),
                'default' => 1,
                'group'   => 'registration',
            )
        );
        wpbdp_register_setting(
            array(
                'id'    => 'enable-key-access',
                'type'  => 'toggle',
                'name'  => _x( 'Allow anonymous users to edit/manage listings with an access key', 'settings', 'business-directory-plugin' ),
                'group' => 'registration',
            )
        );
        wpbdp_register_setting(
            array(
                'id'          => 'login-url',
                'type'        => 'text',
                'name'        => _x( 'Login URL', 'settings', 'business-directory-plugin' ),
				'tooltip'     => _x( 'Only enter this if using a membership plugin or custom login page', 'settings', 'business-directory-plugin' ),
                'placeholder' => _x( 'URL of your membership plugin\'s login page.', 'settings', 'business-directory-plugin' ),
                'default'     => '',
                'group'       => 'registration',
            )
        );
        wpbdp_register_setting(
            array(
                'id'          => 'registration-url',
                'type'        => 'text',
                'name'        => _x( 'Registration URL', 'settings', 'business-directory-plugin' ),
				'tooltip'     => _x( 'Only enter this if using a membership plugin or custom registration page.', 'settings', 'business-directory-plugin' ),
                'placeholder' => _x( 'URL of your membership plugin\'s registration page', 'settings', 'business-directory-plugin' ),
                'default'     => '',
                'group'       => 'registration',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'create-account-during-submit-mode',
				'type'    => 'select',
                'name'    => _x( 'Allow users to create accounts during listing submit', 'settings', 'business-directory-plugin' ),
                'default' => 'required',
                'options' => array(
                    'disabled' => __( 'No', 'business-directory-plugin' ),
                    'optional' => __( 'Yes, and make it optional', 'business-directory-plugin' ),
                    'required' => __( 'Yes, and make it required', 'business-directory-plugin' ),
                ),
                'group'   => 'registration',
				'grid_classes' => array(
					'left'  => 'wpbdp8',
					'right' => 'wpbdp4'
				),
            )
        );

        // Terms & Conditions.
        wpbdp_register_settings_group( 'tos_settings', __( 'Terms and Conditions', 'business-directory-plugin' ), 'listings/main' );
        wpbdp_register_setting(
            array(
                'id'    => 'display-terms-and-conditions',
                'type'  => 'toggle',
                'name'  => __( 'User Agreement', 'business-directory-plugin' ),
				'desc'  => __( 'Display and require user agreement to Terms and Conditions', 'business-directory-plugin' ),
                'group' => 'tos_settings',
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'terms-and-conditions',
                'type'         => 'textarea',
                'name'         => __( 'Terms and Conditions', 'business-directory-plugin' ),
                'desc'         => _x( 'Enter text or a URL starting with http. If you use a URL, the Terms and Conditions text will be replaced by a link to the appropiate page.', 'settings', 'business-directory-plugin' ),
                'default'      => '',
                'placeholder'  => _x( 'Terms and Conditions text goes here', 'settings', 'business-directory-plugin' ),
                'group'        => 'tos_settings',
                'requirements' => array( 'display-terms-and-conditions' ),
            )
        );

        // Search.
        wpbdp_register_settings_group( 'search_settings', __( 'Searching', 'business-directory-plugin' ), 'listings' );
        wpbdp_register_setting(
            array(
                'id'      => 'search-form-in-results',
                'type'    => 'radio',
				'name'    => __( 'Display advanced search form', 'business-directory-plugin' ),
                'default' => 'above',
                'options' => array(
                    'above' => _x( 'Above results', 'admin settings', 'business-directory-plugin' ),
                    'below' => _x( 'Below results', 'admin settings', 'business-directory-plugin' ),
                    'none'  => _x( 'Don\'t show with results', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'search_settings',
            )
        );

        self::register_quick_search();

        wpbdp_register_setting(
            array(
                'id'    => 'quick-search-enable-performance-tricks',
                'type'  => 'toggle',
                'name'  => _x( 'Enable high performance search', 'settings', 'business-directory-plugin' ),
				'tooltip' => __( 'Enabling this sacrifices result quality to improve speed. This is helpful if you\'re on shared hosting plans, where database performance is an issue.', 'business-directory-plugin' ),
                'group' => 'search_settings',
            )
        );

		WPBDP_Admin_Education::add_tip_in_settings( 'zip', 'search_settings' );

        // Advanced settings.
        wpbdp_register_settings_group( 'general/advanced', _x( 'Advanced', 'settings', 'business-directory-plugin' ), 'general' );

        wpbdp_register_setting(
            array(
                'id'    => 'disable-cpt',
                'type'  => 'toggle',
                'name'  => _x( 'Disable advanced CPT integration', 'settings', 'business-directory-plugin' ),
                'group' => 'general/advanced',
            )
        );

        wpbdp_register_setting(
            array(
                'id'    => 'disable-submit-listing',
                'type'  => 'toggle',
                'name'  => _x( 'Disable frontend listing submission', 'settings', 'business-directory-plugin' ),
                'desc'  => _x( 'Prevents the Submit Listing button from showing on the main UI, but allows a shortcode for submit listing to function on other pages.', 'settings', 'business-directory-plugin' ),
                'group' => 'general/advanced',
            )
        );
    }

	/**
	 * @since 5.11
	 */
	private static function register_quick_search() {
		$fields         = array();
		$text_fields    = array();
		$default_fields = array();
		if ( is_admin() ) {
			list( $fields, $text_fields, $default_fields ) = self::get_quicksearch_fields();
		}

		wpbdp_register_setting(
			array(
				'id'       => 'quick-search-fields',
				'type'     => 'multicheck',
				'name'     => __( 'Quick search data', 'business-directory-plugin' ),
				'desc'     => self::quicksearch_field_desc( $default_fields ),
				'default'  => array(),
				'multiple' => true,
				'options'  => $fields,
				'group'    => 'search_settings',
				'attrs'    => array(
					'data-text-fields' => wp_json_encode( $text_fields ),
				),
				'class'    => 'wpbdp-col-grid-2',
			)
		);
	}

	/**
	 * @since 5.11
	 */
	private static function quicksearch_field_desc( $default_fields ) {
		if ( ! is_admin() ) {
			return '';
		}

		$too_many_fields  = '<span class="text-fields-warning wpbdp-note" style="display: none;">';
		$too_many_fields .= _x( 'You have selected a textarea field to be included in quick searches. Searches involving those fields are very expensive and could result in timeouts and/or general slowness.', 'admin settings', 'business-directory-plugin' );
		$too_many_fields .= '</span>';

		$no_fields = '<p><strong>' . __( 'If no fields are selected, the following fields will be searched in Quick Searches:', 'business-directory-plugin' ) . ' ' . esc_html( implode( ', ', $default_fields ) ) . '.</strong></p>';

		return __( 'The Quick Search is a single search box, but you may choose what data is searched. Searching too many fields can result in very slow search performance.', 'business-directory-plugin' ) . $no_fields . $too_many_fields;
	}

    /**
     * Find fields that can be used in Quick Search.
     */
    private static function get_quicksearch_fields() {
        $fields         = array();
        $text_fields    = array();
        $default_fields = array();

        foreach ( wpbdp_get_form_fields( 'association=-custom' ) as $field ) {
            if ( in_array( $field->get_association(), array( 'title', 'excerpt', 'content' ), true ) ) {
                $default_fields[] = $field->get_label();
            }

            if ( in_array( $field->get_association(), array( 'excerpt', 'content' ), true ) || 'textarea' === $field->get_field_type_id() ) {
                $text_fields[] = $field->get_id();
            }

            if ( in_array( $field->get_field_type_id(), array( 'image' ) ) ) {
                continue;
            }

            $fields[ $field->get_id() ] = $field->get_label();
        }

        return array( $fields, $text_fields, $default_fields );
    }

    private static function settings_listings() {
        wpbdp_register_settings_group( 'listings/post_category', __( 'Categories', 'business-directory-plugin' ), 'listings' );
        wpbdp_register_settings_group( 'listings/contact', _x( 'Contact Form', 'settings', 'business-directory-plugin' ), 'listings' );
		wpbdp_register_settings_group( 'listings/report', __( 'Buttons', 'business-directory-plugin' ), 'listings' );

        wpbdp_register_settings_group( 'listings/sorting', __( 'Sorting', 'business-directory-plugin' ), 'listings' );

        wpbdp_register_setting(
            array(
                'id'      => 'listings-per-page',
                'type'    => 'number',
                'name'    => _x( 'Listings per page', 'settings', 'business-directory-plugin' ),
                'desc'    => _x( 'Number of listings to show per page. Use a value of "0" to show all listings.', 'settings', 'business-directory-plugin' ),
                'default' => '10',
                'min'     => 0,
                'step'    => 1,
                'group'   => 'listings/main',
            )
        );

        wpbdp_register_setting(
            array(
                'id'      => 'listing-renewal',
                'type'    => 'toggle',
                'name'    => _x( 'Turn on listing renewal option', 'settings', 'business-directory-plugin' ),
                'default' => true,
                'group'   => 'listings/main',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'listing-link-in-new-tab',
                'type'    => 'toggle',
                'name'    => _x( 'Open detailed view of listing in new tab', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'listings/main',
            )
        );

        wpbdp_register_setting(
            array(
                'id'      => 'enable-listing-flagging',
                'type'    => 'toggle',
                'name'    => _x( 'Include button to report listings', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'listings/report',
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'listing-flagging-register-users',
                'type'         => 'toggle',
                'name'         => _x( 'Enable report listing for registered users only', 'settings', 'business-directory-plugin' ),
                'default'      => true,
                'group'        => 'listings/report',
                'requirements' => array( 'enable-listing-flagging' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'listing-flagging-options',
                'type'         => 'textarea',
                'name'         => _x( 'Report listing option list', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Form option list to report a listing as inappropriate. One option per line.', 'settings', 'business-directory-plugin' ),
                'default'      => false,
                'group'        => 'listings/report',
                'requirements' => array( 'enable-listing-flagging' ),
            )
        );

        wpbdp_register_setting(
            array(
                'id'      => 'show-contact-form',
                'type'    => 'toggle',
                'name'    => _x( 'Include listing contact form on listing pages', 'settings', 'business-directory-plugin' ),
                'desc'    => _x( 'Allows visitors to contact listing authors privately. Authors will receive the messages via email.', 'settings', 'business-directory-plugin' ),
                'default' => true,
                'group'   => 'listings/contact',
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'contact-form-require-login',
                'type'         => 'toggle',
                'name'         => _x( 'Require login for using the contact form', 'settings', 'business-directory-plugin' ),
                'default'      => false,
                'group'        => 'listings/contact',
                'requirements' => array( 'show-contact-form' ),
            )
        );

        wpbdp_register_setting(
            array(
                'id'           => 'contact-form-registered-users-limit',
                'type'         => 'number',
                'name'         => _x( 'Maximum number of daily contact form submits from registered users', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Use this to prevent spamming of listing owners from logged in users. 0 means unlimited submits per day.', 'settings', 'business-directory-plugin' ),
                'default'      => '0',
                'min'          => 0,
                'step'         => 1,
                'group'        => 'listings/contact',
                'requirements' => array( 'show-contact-form', 'contact-form-require-login' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'contact-form-daily-limit',
                'type'         => 'number',
                'name'         => _x( 'Maximum number of contact form submits for each listing per day', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Use this to set contact submits limit for each listing in the directory. 0 means unlimited submits per day.', 'settings', 'business-directory-plugin' ),
                'default'      => '0',
                'min'          => 0,
                'step'         => 1,
                'group'        => 'listings/contact',
                'requirements' => array( 'show-contact-form' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'allow-comments-in-listings',
                'type'    => 'radio',
                'name'    => _x( 'Include comment form on listing pages?', 'settings', 'business-directory-plugin' ),
                'desc'    => __( 'Business Directory Plugin uses the standard WordPress comments. Most themes allow for comments on posts, not pages. Some themes handle both. Since the directory is displayed on a page, we need a theme that can handle both. Use the 2nd option if you want to allow comments on listings. If that doesn\'t work, try the 3rd option.', 'business-directory-plugin' ),
                'default' => 'allow-comments-and-insert-template',
                'options' => array(
                    'do-not-allow-comments'              => _x( 'Do not include comments in listings', 'admin settings', 'business-directory-plugin' ),
                    'allow-comments'                     => __( 'Include theme comment form (standard option)', 'business-directory-plugin' ),
                    'allow-comments-and-insert-template' => __( 'Include directory comment form (use only if 2nd option does not work)', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/main',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'show-listings-under-categories',
                'type'    => 'toggle',
                'name'    => _x( 'Show listings under categories on main page', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'listings/post_category',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'prevent-sticky-on-directory-view',
                'type'    => 'multicheck',
                'name'    => __( 'Prevent featured (sticky) status on directory pages?', 'business-directory-plugin' ),
                'desc'    => _x( 'Prevents featured listings from floating to the top of the selected page.', 'settings', 'business-directory-plugin' ),
                'default' => array(),
                'options' => array(
                    'main'          => _x( 'Directory view.', 'admin settings', 'business-directory-plugin' ),
                    'all_listings'  => _x( 'All Listings view.', 'admin settings', 'business-directory-plugin' ),
                    'show_category' => _x( 'Category view.', 'admin settings', 'business-directory-plugin' ),
                    'search'        => _x( 'Search view.', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/main',
            )
        );

        wpbdp_register_setting(
            array(
				'id'          => 'default-listing-author',
				'name'        => __( 'Owner of anonymous listings', 'business-directory-plugin' ),
				'type'        => 'text',
				'default'     => '',
 				'tooltip'      => _x( 'The user ID or login of an existing user account. If login is not required to submit listings, this user will own them. A site admin or another user that will not a be posting a listing is best.', 'settings', 'business-directory-plugin' ),
				'group'        => 'registration',
				'requirements' => array( '!require-login' ),
				'grid_classes' => array(
					'left'  => 'wpbdp8',
					'right' => 'wpbdp4'
				),
            )
        );

        wpbdp_register_setting(
            array(
                'id'      => 'new-post-status',
                'type'    => 'radio',
				'name'    => __( 'Default listing status', 'business-directory-plugin' ),
                'default' => 'pending',
                'options' => array(
                    'publish' => _x( 'Published', 'post status', 'business-directory-plugin' ),
                    'pending' => __( 'Pending (Require approval)', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/main',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'edit-post-status',
                'type'    => 'radio',
                'name'    => _x( 'Edit post status', 'settings', 'business-directory-plugin' ),
                'default' => 'publish',
                'options' => array(
                    'publish' => _x( 'Published', 'post status', 'business-directory-plugin' ),
                    'pending' => __( 'Pending (Require approval)', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/main',
            )
        );

        wpbdp_register_setting(
            array(
                'id'      => 'deleted-status',
                'type'    => 'hidden',
				'class'   => 'hidden',
                'name'    => _x( 'Status of deleted listings', 'settings', 'business-directory-plugin' ),
                'default' => 'trash',
                'options' => array(
                    'draft' => _x( 'Draft', 'post status', 'business-directory-plugin' ),
                    'trash' => _x( 'Trash', 'post status', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/main',
            )
        );

		wpbdp_register_settings_group( 'listings/strings', __( 'Message Defaults', 'business-directory-plugin' ), 'listings/main' );
		wpbdp_register_setting(
			array(
				'id'      => 'listing-label',
				'type'    => 'text',
				'name'    => __( 'Listing label', 'business-directory-plugin' ),
				'desc'    => __( 'What is a single listing called?', 'business-directory-plugin' ),
				'default' => __( 'Listing', 'business-directory-plugin' ),
				'placeholder' => __( 'Listing', 'business-directory-plugin' ),
				'group'   => 'listings/strings',
			)
		);

		wpbdp_register_setting(
			array(
				'id'      => 'listings-label',
				'type'    => 'text',
				'name'    => __( 'Listing label (Plural)', 'business-directory-plugin' ),
				'desc'    => __( 'What are your listings called?', 'business-directory-plugin' ),
				'default' => __( 'Listings', 'business-directory-plugin' ),
				'placeholder' => __( 'Listings', 'business-directory-plugin' ),
				'group'   => 'listings/strings',
			)
		);

		wpbdp_register_setting(
			array(
				'id'      => 'directory-label',
				'type'    => 'text',
				'name'    => __( 'Directory label', 'business-directory-plugin' ),
				'desc'    => __( 'What should we call your directory?', 'business-directory-plugin' ),
				'default' => __( 'Directory', 'business-directory-plugin' ),
				'placeholder' => __( 'Directory', 'business-directory-plugin' ),
				'group'   => 'listings/strings',
			)
		);

		wpbdp_register_setting(
			array(
				'id'      => 'submit-instructions',
				'type'    => 'textarea',
				'name'    => __( 'Submit listing instructions', 'business-directory-plugin' ),
				'tooltip' => __( 'This text is displayed on the first page of the Submit Listing process. You can use it for instructions about filling out the form or information to get started.', 'business-directory-plugin' ),
				'default' => '',
				'group'   => 'listings/strings',
			)
		);

        wpbdp_register_setting(
            array(
                'id'      => 'categories-order-by',
                'type'    => 'radio',
                'name'    => _x( 'Order categories list by', 'settings', 'business-directory-plugin' ),
                'default' => 'name',
                'options' => array(
                    'name'  => __( 'Name', 'business-directory-plugin' ),
                    'slug'  => _x( 'Slug', 'admin settings', 'business-directory-plugin' ),
                    'count' => __( 'Listing Count', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/post_category',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'categories-sort',
                'type'    => 'radio',
                'name'    => _x( 'Sort order for categories', 'settings', 'business-directory-plugin' ),
                'default' => 'ASC',
                'options' => array(
                    'ASC'  => _x( 'Ascending', 'admin settings', 'business-directory-plugin' ),
                    'DESC' => _x( 'Descending', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/post_category',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'show-category-post-count',
                'type'    => 'toggle',
                'name'    => _x( 'Show category post count', 'settings', 'business-directory-plugin' ),
                'default' => true,
                'group'   => 'listings/post_category',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'hide-empty-categories',
                'type'    => 'toggle',
                'name'    => _x( 'Hide empty categories', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'listings/post_category',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'show-only-parent-categories',
                'type'    => 'toggle',
                'name'    => _x( 'Show only parent categories in category list', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'listings/post_category',
            )
        );

        $msg = _x( 'Plan Custom Order can be changed under <a>Plans</a>', 'admin settings', 'business-directory-plugin' );
        $msg = str_replace( '<a>', '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp-admin-fees' ) ) . '">', $msg );
        wpbdp_register_setting(
            array(
                'id'      => 'listings-order-by',
                'type'    => 'select',
                'name'    => _x( 'Order directory listings by', 'settings', 'business-directory-plugin' ),
                'desc'    => $msg,
                'default' => 'title',
                'options' => apply_filters(
                    'wpbdp_sort_options',
                    array(
                        'title'            => __( 'Title', 'business-directory-plugin' ),
                        'author'           => _x( 'Author', 'admin settings', 'business-directory-plugin' ),
                        'date'             => _x( 'Date posted', 'admin settings', 'business-directory-plugin' ),
                        'modified'         => _x( 'Date last modified', 'admin settings', 'business-directory-plugin' ),
                        'rand'             => _x( 'Random', 'admin settings', 'business-directory-plugin' ),
                        'paid'             => _x( 'Paid first then free. Inside each group by date.', 'admin settings', 'business-directory-plugin' ),
                        'paid-title'       => _x( 'Paid first then free. Inside each group by title.', 'admin settings', 'business-directory-plugin' ),
                        'plan-order-date'  => _x( 'Plan Custom Order, then Date', 'admin settings', 'business-directory-plugin' ),
                        'plan-order-title' => _x( 'Plan Custom Order, then Title', 'admin settings', 'business-directory-plugin' ),
                    )
                ),
                'group'   => 'listings/sorting',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'listings-sort',
                'type'    => 'radio',
                'name'    => _x( 'Sort directory listings by', 'settings', 'business-directory-plugin' ),
                'desc'    => _x( 'Ascending for ascending order A-Z, Descending for descending order Z-A', 'settings', 'business-directory-plugin' ),
                'default' => 'ASC',
                'options' => array(
                    'ASC'  => _x( 'Ascending', 'admin settings', 'business-directory-plugin' ),
                    'DESC' => _x( 'Descending', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'listings/sorting',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'listings-sortbar-enabled',
                'type'    => 'toggle',
                'name'    => _x( 'Enable sort bar', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'listings/sorting',
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'listings-sortbar-fields',
                'type'         => 'multicheck',
                'name'         => _x( 'Sortbar Fields', 'settings', 'business-directory-plugin' ),
                'default'      => array(),
                'options'      => is_admin() ? wpbdp_sortbar_get_field_options() : array(),
                'group'        => 'listings/sorting',
                'requirements' => array( 'listings-sortbar-enabled' ),
				'class'        => 'wpbdp-col-grid-2',
            )
        );

		WPBDP_Admin_Education::add_tip_in_settings( 'abc', 'listings/sorting' );
    }

    private static function settings_appearance() {
        // Display Options.
		wpbdp_register_settings_group( 'display_options', __( 'Show Buttons', 'business-directory-plugin' ), 'listings/report' );
        wpbdp_register_setting(
            array(
                'id'           => 'show-submit-listing',
                'type'         => 'checkbox',
                'name'         => _x( 'Show the "Submit listing" button', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Hides the button used by the main UI to allow listing submission, but does not shut off the use of the link for submitting listings (allows you to customize the submit listing button on your own)', 'settings', 'business-directory-plugin' ),
                'default'      => true,
                'group'        => 'display_options',
                'requirements' => array( '!disable-submit-listing' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'show-search-listings',
                'type'    => 'checkbox',
                'name'    => _x( 'Show "Search listings"', 'settings', 'business-directory-plugin' ),
                'default' => true,
                'group'   => 'display_options',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'show-view-listings',
                'type'    => 'checkbox',
                'name'    => _x( 'Show the "View Listings" button', 'settings', 'business-directory-plugin' ),
                'default' => true,
                'group'   => 'display_options',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'show-manage-listings',
                'type'    => 'checkbox',
                'name'    => _x( 'Show the "Manage Listings" button', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'display_options',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'show-directory-button',
                'type'    => 'checkbox',
                'name'    => __( 'Show the "Directory" and "Return to Directory" button', 'business-directory-plugin' ),
                'default' => true,
                'group'   => 'display_options',
            )
        );

        // Themes.
        wpbdp_register_settings_group( 'themes', __( 'Styling', 'business-directory-plugin' ), 'appearance' );

        wpbdp_register_setting(
            array(
                'id'      => 'themes-button-style',
                'type'    => 'toggle',
                'name'    => __( 'Button style', 'business-directory-plugin' ),
                'default' => 'theme',
                'option'  => 'theme',
				'desc'    => __( 'Override WP theme button styling', 'business-directory-plugin' ),
                'group'   => 'themes',
            )
        );
		wpbdp_register_setting(
			array(
				'id'      => 'rootline-color',
				'type'    => 'color',
				'name'    => __( 'Primary color', 'business-directory-plugin' ),
				'default' => '#569AF6',
				'group'   => 'themes',
				'desc'    => __( 'This is used for form buttons and form rootline.', 'business-directory-plugin' ),
			)
		);

		WPBDP_Admin_Education::add_tip_in_settings( 'table', 'themes' );

        // Image.
        wpbdp_register_settings_group( 'appearance/image', __( 'Images', 'business-directory-plugin' ), 'appearance' );
        wpbdp_register_settings_group( 'images/general', _x( 'Image Settings', 'settings', 'business-directory-plugin' ), 'appearance/image', array( 'desc' => 'Any changes to these settings will affect new listings only.  Existing listings will not be affected.  If you wish to change existing listings, you will need to re-upload the image(s) on that listing after changing things here.' ) );
        wpbdp_register_setting(
            array(
                'id'      => 'allow-images',
                'type'    => 'toggle',
                'name'    => _x( 'Allow images', 'settings', 'business-directory-plugin' ),
                'default' => true,
                'group'   => 'images/general',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'image-min-filesize',
                'type'    => 'number',
                'min'     => 0,
                'step'    => 1,
				'name'    => __( 'Min image file size (KB)', 'business-directory-plugin' ),
                'default' => '0',
                'group'   => 'images/general',
				'class'   => 'wpbdp-half',
				'requirements' => array( 'allow-images' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'image-max-filesize',
                'type'    => 'number',
                'min'     => 0,
                'step'    => 1,
				'name'    => __( 'Max image file size (KB)', 'business-directory-plugin' ),
                'default' => '10000',
                'group'   => 'images/general',
				'class'   => 'wpbdp-half',
				'requirements' => array( 'allow-images' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'image-min-width',
                'type'    => 'number',
                'min'     => 0,
                'step'    => 1,
                'name'    => _x( 'Min image width (px)', 'settings', 'business-directory-plugin' ),
                'default' => '0',
                'group'   => 'images/general',
				'class'   => 'wpbdp-half',
				'requirements' => array( 'allow-images' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'image-min-height',
                'type'    => 'number',
                'name'    => _x( 'Min image height (px)', 'settings', 'business-directory-plugin' ),
                'default' => '0',
                'min'     => 0,
                'step'    => 1,
                'group'   => 'images/general',
				'class'   => 'wpbdp-half',
				'requirements' => array( 'allow-images' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'image-max-width',
                'type'    => 'number',
                'min'     => 0,
                'step'    => 1,
                'name'    => _x( 'Max image width (px)', 'settings', 'business-directory-plugin' ),
                'default' => '500',
                'group'   => 'images/general',
				'class'   => 'wpbdp-half',
				'requirements' => array( 'allow-images' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'image-max-height',
                'type'    => 'number',
                'min'     => 0,
                'step'    => 1,
                'name'    => _x( 'Max image height (px)', 'settings', 'business-directory-plugin' ),
                'default' => '500',
                'group'   => 'images/general',
				'class'   => 'wpbdp-half',
				'requirements' => array( 'allow-images' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'use-thickbox',
                'type'    => 'toggle',
                'min'     => 0,
                'step'    => 1,
                'name'    => _x( 'Turn on thickbox/lightbox', 'settings', 'business-directory-plugin' ),
				'tooltip' => _x( 'Uncheck if it conflicts with other elements or plugins installed on your site', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'images/general',
            )
        );

        wpbdp_register_settings_group( 'image/thumbnails', _x( 'Thumbnails', 'settings', 'business-directory-plugin' ), 'appearance/image' );

		wpbdp_register_setting(
			array(
				'id'      => 'show-thumbnail',
				'type'    => 'toggle',
				'name'    => __( 'Show thumbnail in excerpt', 'business-directory-plugin' ),
				'default' => true,
				'group'   => 'image/thumbnails',
			)
		);

		wpbdp_register_setting(
			array(
				'id'      => 'which-thumbnail',
				'type'    => 'radio',
				'name'    => __( 'Show thumbnail in listing from:', 'business-directory-plugin' ),
				'default' => 'auto',
				'options' => array(
					'auto'  => __( 'Business Directory Plugin', 'business-directory-plugin' ),
					'theme' => __( 'WordPress Theme', 'business-directory-plugin' ),
					'none'  => __( 'None', 'business-directory-plugin' ),
				),
				'group'   => 'image/thumbnails',
			)
		);

		wpbdp_register_setting(
			array(
				'id'      => 'listing-main-image-default-size',
				'type'    => 'select',
				'name'    => _x( 'Main thumbnail image size', 'settings', 'business-directory-plugin' ),
				'default' => 'wpbdp-thumb',
				'options' => is_admin() ? self::get_registered_image_sizes() : array(),
				'tooltip' => _x( 'This indicates the size of the thumbnail to be used both in excerpt and detail views. For CROPPED image size values, we use the EXACT size defined. For all other values, we preserve the aspect ratio of the image and use the width as the starting point.', 'settings', 'business-directory-plugin' ),
				'group'   => 'image/thumbnails',
			)
		);

        wpbdp_register_setting(
            array(
                'id'      => 'thumbnail-width',
                'type'    => 'number',
                'min'     => 0,
                'step'    => 1,
                'name'    => _x( 'Gallery thumbnail width (px)', 'settings', 'business-directory-plugin' ),
                'default' => '150',
                'group'   => 'image/thumbnails',
				'class'   => 'wpbdp-half',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'thumbnail-height',
                'type'    => 'number',
                'min'     => 0,
                'step'    => 1,
                'name'    => _x( 'Gallery thumbnail height (px)', 'settings', 'business-directory-plugin' ),
                'default' => '150',
                'group'   => 'image/thumbnails',
				'class'   => 'wpbdp-half',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'thumbnail-crop',
                'type'    => 'toggle',
                'name'    => _x( 'Crop thumbnails to exact dimensions', 'settings', 'business-directory-plugin' ),
                'tooltip' => __( 'Images will use the dimensions above but part of the image may be cropped out. If disabled, image thumbnails will be resized to match the specified width and their height will be adjusted proportionally. Depending on the uploaded images, thumbnails may have different heights.', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'image/thumbnails',
            )
        );

        wpbdp_register_settings_group( 'image/listings', __( 'Default Images', 'business-directory-plugin' ), 'appearance/image' );
        wpbdp_register_setting(
            array(
                'id'      => 'enforce-image-upload',
                'type'    => 'toggle',
                'name'    => __( 'Require images on submit/edit', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'images/general',
            )
        );

        wpbdp_register_setting(
            array(
                'id'      => 'use-default-picture',
                'type'    => 'multicheck',
                'name'    => _x( 'Use "Coming Soon" photo for listings without any (primary) images?', 'settings', 'business-directory-plugin' ),
                'default' => array(),
                'options' => array(
                    'excerpt' => _x( 'Excerpt view.', 'admin settings', 'business-directory-plugin' ),
                    'listing' => _x( 'Detail view.', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'image/listings',
            )
        );
		wpbdp_register_setting(
			array(
				'id'      => 'listings-coming-soon-image',
				'type'    => 'file',
				'name'    => __( 'Coming Soon image', 'business-directory-plugin' ),
				'default' => '',
				'group'   => 'image/listings',
				'requirements' => array( 'use-default-picture' ),
			)
		);

        wpbdp_register_setting(
            array(
                'id'      => 'display-sticky-badge',
                'type'    => 'multicheck',
                'name'    => _x( 'Display featured (sticky) badge', 'settings', 'business-directory-plugin' ),
                'default' => array( 'single' ),
                'options' => array(
                    'excerpt' => _x( 'Excerpt view.', 'admin settings', 'business-directory-plugin' ),
                    'single'  => _x( 'Detail view.', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'image/listings',
            )
        );

        wpbdp_register_setting(
            array(
                'id'      => 'listings-sticky-image',
                'type'    => 'file',
                'name'    => _x( 'Featured Badge image', 'settings', 'business-directory-plugin' ),
                'default' => '',
                'group'   => 'image/listings',
				'requirements' => array( 'display-sticky-badge' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'          => 'sticky-image-link-to',
                'type'        => 'url',
                'name'        => _x( 'Featured Badge URL', 'settings', 'business-directory-plugin' ),
				'tooltip'     => _x( 'Use this to set Featured Badge image as a link to a defined URL.', 'settings', 'business-directory-plugin' ),
                'placeholder' => __( 'URL', 'business-directory-plugin' ),
                'default'     => '',
                'group'       => 'image/listings',
				'requirements' => array( 'display-sticky-badge' ),
            )
        );
    }

    private static function settings_payment() {
		wpbdp_register_setting(
			array(
				'id'      => 'payments-on',
				'type'    => 'hidden',
				'class'   => 'hidden',
				'default' => true,
				'group'   => 'payment/main',
			)
		);

        wpbdp_register_setting(
            array(
                'id'      => 'fee-order',
                'type'    => 'silent',
                'name'    => _x( 'Fee Order', 'settings', 'business-directory-plugin' ),
                'default' => array(
                    'method' => 'label',
                    'order'  => 'asc',
                ),
                'group'   => 'payment/main',
            )
        );

        wpbdp_register_setting(
            array(
                'id'           => 'payments-test-mode',
                'type'         => 'toggle',
                'name'         => _x( 'Put payment gateways in test mode', 'settings', 'business-directory-plugin' ),
                'default'      => true,
                'group'        => 'payment/main',
            )
        );

        wpbdp_register_setting(
            array(
                'id'           => 'currency',
                'type'         => 'select',
                'name'         => __( 'Currency', 'business-directory-plugin' ),
                'default'      => 'USD',
                'options'      => WPBDP_Currency_Helper::list_currencies() + array( '' => '- ' . __( 'Custom', 'business-directory-plugin' ) . ' -' ),
                'desc'         => self::gateway_description(),
                'group'        => 'payment/main',
				'class'        => 'wpbdp5',
            )
        );
		wpbdp_register_setting(
			array(
				'id'           => 'currency-code',
				'type'         => 'text',
				'name'         => _x( 'Currency Code', 'settings', 'business-directory-plugin' ),
				'default'      => '',
				'group'        => 'payment/main',
				'class'        => 'wpbdp5',
				'requirements' => array( '!currency' ),
			)
		);
        wpbdp_register_setting(
            array(
                'id'           => 'currency-symbol',
                'type'         => 'text',
                'name'         => _x( 'Currency Symbol', 'settings', 'business-directory-plugin' ),
                'default'      => '$',
                'group'        => 'payment/main',
				'class'        => 'wpbdp5',
				'requirements' => array( '!currency' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'currency-symbol-position',
                'type'         => 'select',
                'name'         => _x( 'Currency symbol display', 'settings', 'business-directory-plugin' ),
                'default'      => 'left',
				'class'        => 'wpbdp5',
                'options'      => array(
                    'left'  => __( 'On the left', 'business-directory-plugin' ),
                    'right' => __( 'On the right', 'business-directory-plugin' ),
                    'none'  => __( 'None', 'business-directory-plugin' ),
                ),
                'group'        => 'payment/main',
				'requirements' => array( '!currency' ),
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'include-fee-description',
                'type'         => 'toggle',
                'name'         => _x( 'Include plan description in receipt', 'settings', 'business-directory-plugin' ),
                'default'      => false,
                'group'        => 'payment/main',
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'payment-message',
                'type'         => 'textarea',
                'name'         => _x( 'Thank you for payment message', 'settings', 'business-directory-plugin' ),
                'default'      => __( 'Thank you for your payment.', 'business-directory-plugin' ),
                'group'        => 'payment/main',
            )
        );

		self::maybe_show_deprecated();
    }

	/**
	 * Don't run db calls unless we need it.
	 *
	 * @since 5.11
	 */
	private static function gateway_description() {
		if ( ! is_admin() ) {
			return '';
		}

		$aed_usupported_gateways = apply_filters( 'wpbdp_aed_not_supported', wpbdp_get_option( 'authorize-net', false ) ? array( 'Authorize.net' ) : array() );

		if ( ! $aed_usupported_gateways ) {
			return '';
		}

		return sprintf(
			/* translators: %1$s: gateway name, %2$s: explanation string */
			_x( 'AED currency is not supported by %1$s. %2$s', 'admin settings', 'business-directory-plugin' ),
			'<b>' . implode( ' or ', $aed_usupported_gateways ) . '</b>',
			_n(
				'If you are using this gateway, we recommend you disable it if you wish to collect payments in this currency.',
				'If you are using these gateways, we recommend you disable them if you wish to collect payments in this currency.',
				count( $aed_usupported_gateways ),
				'business-directory-plugin'
			)
		);
	}

	/**
	 * @since 5.9.1
	 */
	private static function maybe_show_deprecated() {
		// Deprecated setting.
		$turned_on = wpbdp_get_option( 'payment-abandonment' );
		if ( ! $turned_on ) {
			WPBDP_Admin_Education::add_tip_in_settings( 'abandon', 'payment/main' );
			return;
		}

        wpbdp_register_setting(
            array(
                'id'           => 'payment-abandonment',
                'type'         => 'checkbox',
                'desc'         => _x( 'Ask users to come back for abandoned payments', 'settings', 'business-directory-plugin' ),
                'default'      => false,
                'group'        => 'payment/main',
            )
        );

		// Deprecated setting.
        wpbdp_register_setting(
            array(
				'id'           => 'payment-abandonment-threshold',
				'type'         => 'number',
				'name'         => _x( 'Listing abandonment threshold (hours)', 'settings', 'business-directory-plugin' ),
				'desc'         => str_replace( '<a>', '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_settings&tab=email' ) ) . '#email-templates-payment-abandoned">', _x( 'Listings with pending payments are marked as abandoned after this time. You can also <a>customize the email</a> users receive.', 'admin settings', 'business-directory-plugin' ) ),
				'default'      => '24',
				'min'          => 0,
				'step'         => 1,
				'group'        => 'payment/main',
				'requirements' => array( 'payment-abandonment' ),
            )
        );
	}

    private static function settings_email() {
        wpbdp_register_settings_group( 'email/main/general', _x( 'General Settings', 'settings', 'business-directory-plugin' ), 'email/main' );
        wpbdp_register_setting(
            array(
                'id'      => 'override-email-blocking',
                'type'    => 'checkbox',
                'name'    => _x( 'Display email address fields publicly', 'settings', 'business-directory-plugin' ),
                'desc'    => _x( 'Shows the email address of the listing owner to all web users. NOT RECOMMENDED as this increases spam to the address and allows spam bots to harvest it for future use.', 'settings', 'business-directory-plugin' ),
                'default' => false,
                'group'   => 'email/main/general',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'listing-email-mode',
                'type'    => 'radio',
                'name'    => _x( 'How to determine the listing\'s email address?', 'settings', 'business-directory-plugin' ),
                'desc'    => _x( 'This affects emails sent to listing owners via contact forms or when their listings expire.', 'settings', 'business-directory-plugin' ),
                'default' => 'field',
                'options' => array(
					'field' => __( 'Try listing email field first, then listing owner.', 'business-directory-plugin' ),
					'user'  => __( 'Try listing owner first and then listing email field.', 'business-directory-plugin' ),
                ),
                'group'   => 'email/main/general',
            )
        );
        wpbdp_register_setting(
            array(
                'id'      => 'listing-email-content-type',
                'type'    => 'radio',
                'name'    => _x( 'Email Content-Type header', 'settings', 'business-directory-plugin' ),
                'desc'    => _x( 'Use this setting to control the format of the emails explicitly. Some plugins for email do not correctly support Content Type unless explicitly set, you can do that here. If you\'re unsure, try "HTML", "Plain" and then "Both".', 'settings', 'business-directory-plugin' ),
                'default' => 'html',
                'options' => array(
                    'plain' => _x( 'Plain (text/plain)', 'admin settings', 'business-directory-plugin' ),
                    'html'  => _x( 'HTML (text/html)', 'admin settings', 'business-directory-plugin' ),
                    'both'  => _x( 'Both (multipart/alternative)', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'email/main/general',
            )
        );

        wpbdp_register_settings_group( 'email_notifications', __( 'Email Notifications', 'business-directory-plugin' ), 'email/main' );
        wpbdp_register_setting(
            array(
                'id'      => 'admin-notifications',
                'type'    => 'multicheck',
                'name'    => __( 'Notify admin via email when...', 'business-directory-plugin' ),
                'default' => array(),
                'options' => array(
                    'new-listing'       => _x( 'A new listing is submitted.', 'admin settings', 'business-directory-plugin' ),
                    'listing-edit'      => _x( 'A listing is edited.', 'admin settings', 'business-directory-plugin' ),
                    'renewal'           => _x( 'A listing expires.', 'admin settings', 'business-directory-plugin' ),
                    'after_renewal'     => _x( 'A listing is renewed.', 'admin settings', 'business-directory-plugin' ),
                    'payment-completed' => _x( 'A listing payment is completed.', 'admin settings', 'business-directory-plugin' ),
                    'flagging_listing'  => _x( 'A listing has been reported as inappropriate.', 'admin settings', 'business-directory-plugin' ),
                    'listing-contact'   => _x( 'A contact message is sent to a listing\'s owner.', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'email_notifications',
            )
        );
        wpbdp_register_setting(
            array(
                'id'    => 'admin-notifications-cc',
                'type'  => 'text',
                'name'  => __( 'CC this email address too', 'business-directory-plugin' ),
                'group' => 'email_notifications',
            )
        );

        $settings_url = admin_url( 'admin.php?page=wpbdp_settings&tab=email&subtab=email_templates' );
        $description  = __( 'You can modify the text template used for most of these emails in the <templates-link>Templates</templates-link> tab.', 'business-directory-plugin' );
        $description  = str_replace( '<templates-link>', '<a href="' . $settings_url . '">', $description );
        $description  = str_replace( '</templates-link>', '</a>', $description );

        wpbdp_register_setting(
            array(
                'id'      => 'user-notifications',
                'type'    => 'multicheck',
                'name'    => __( 'Notify users via email when...', 'business-directory-plugin' ),
                'desc'    => $description,
                'default' => array( 'new-listing', 'listing-published', 'listing-expires' ),
                'options' => array(
                    'new-listing'       => _x( 'Their listing is submitted.', 'admin settings', 'business-directory-plugin' ),
                    'listing-published' => _x( 'Their listing is approved/published.', 'admin settings', 'business-directory-plugin' ),
                    'payment-completed' => _x( 'A payment for their listing is completed.', 'admin settings', 'business-directory-plugin' ),
                    'listing-expires'   => _x( 'Their listing expired or is about to expire.', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'   => 'email_notifications',
            )
        );

        wpbdp_register_settings_group( 'email_templates', _x( 'Templates', 'settings', 'business-directory-plugin' ), 'email' );
        wpbdp_register_setting(
            array(
                'id'           => 'email-confirmation-message',
                'type'         => 'email_template',
                'name'         => _x( 'Email confirmation message', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Sent after a listing has been submitted.', 'settings', 'business-directory-plugin' ),
                'default'      => array(
                    'subject' => '[[site-title]] Listing "[listing]" received',
                    'body'    => 'Your submission \'[listing]\' has been received and it\'s pending review. This review process could take up to 48 hours.',
                ),
                'placeholders' => array(
                    'listing'         => _x( 'Listing\'s title', 'admin settings', 'business-directory-plugin' ),
                    'fee_name'        => _x( 'Listing\'s plan name', 'admin settings', 'business-directory-plugin' ),
                    'fee_description' => _x( 'Listing\'s plan description', 'admin settings', 'business-directory-plugin' ),
                    'fee_details'     => _x( 'Listing\'s plan details', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'        => 'email_templates',
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'email-templates-listing-published',
                'type'         => 'email_template',
                'name'         => _x( 'Listing published message', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Sent when the listing has been published or approved by an admin.', 'settings', 'business-directory-plugin' ),
                'default'      => array(
                    'subject' => '[[site-title]] Listing "[listing]" published',
                    'body'    => _x( 'Your listing "[listing]" is now available at [listing-url] and can be viewed by the public.', 'admin settings', 'business-directory-plugin' ),
                ),
                'placeholders' => array(
                    'listing'     => _x( 'Listing\'s title', 'admin settings', 'business-directory-plugin' ),
                    'listing-url' => _x( 'Listing\'s URL', 'admin settings', 'business-directory-plugin' ),
                    'access_key'  => _x( 'Listing\'s Access Key', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'        => 'email_templates',
            )
        );
        wpbdp_register_setting(
            array(
                'id'           => 'email-templates-contact',
                'type'         => 'email_template',
                'name'         => _x( 'Listing Contact Message', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Sent to listing owners when someone uses the contact form on their listing pages.', 'settings', 'business-directory-plugin' ),
                'default'      => array(
                    'subject' => '[[site-title]] Contact via "[listing]"',
                    'body'    => '' .
                                /* translators: %s: url shortcode */
                                sprintf( _x( 'You have received a reply from your listing at %s.', 'contact email', 'business-directory-plugin' ), '[listing-url]' ) . "\n\n" .

                                /* translators: %s: name shortcode */
                                sprintf( _x( 'Name: %s', 'contact email', 'business-directory-plugin' ), '[name]' ) . "\n" .

                                /* translators: %s: email shortcode */
                                sprintf( __( 'Email: %s', 'business-directory-plugin' ), '[email]' ) . "\n" .

                                /* translators: %s: phone shortcode */
                                sprintf( __( 'Phone Number: %s', 'business-directory-plugin' ), '[phone]' ) . "\n" .

                                _x( 'Message:', 'contact email', 'business-directory-plugin' ) . "\n" .
                                '[message]' . "\n\n" .

                                /* translators: %s: date shortcode */
                                sprintf( _x( 'Time: %s', 'contact email', 'business-directory-plugin' ), '[date]' ),
                ),
                'placeholders' => array(
                    'listing-url' => _x( 'Listing\'s URL', 'admin settings', 'business-directory-plugin' ),
                    'listing'     => _x( 'Listing\'s title', 'admin settings', 'business-directory-plugin' ),
                    'name'        => _x( 'Sender\'s name', 'admin settings', 'business-directory-plugin' ),
                    'email'       => __( 'Sender\'s email address', 'business-directory-plugin' ),
                    'phone'       => __( 'Sender\'s phone number', 'business-directory-plugin' ),
                    'message'     => _x( 'Contact message', 'admin settings', 'business-directory-plugin' ),
                    'date'        => _x( 'Date and time the message was sent', 'admin settings', 'business-directory-plugin' ),
                    'access_key'  => _x( 'Listing\'s Access Key', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'        => 'email_templates',
            )
        );

        wpbdp_register_setting(
            array(
                'id'           => 'email-templates-payment-completed',
                'type'         => 'email_template',
                'name'         => _x( 'Payment completed message', 'settings', 'business-directory-plugin' ),
                'desc'         => _x( 'Sent after a Listing\'s payment is verified by Gateway or admins.', 'settings', 'business-directory-plugin' ),
                'default'      => array(
                    'subject' => '[[site-title]] Payment completed for "[listing]"',
                    'body'    => '
        Dear Customer,
        
        We have verified with [gateway] your payment for the listing "[listing]".

        Details:
        
        [payment_details]

        If you have any issues, please contact us directly by hitting reply to this
        email!

        Thanks,
        - The Administrator of [site-title]',
                ),
                'placeholders' => array(
                    'listing'         => _x( 'Listing\'s title', 'admin settings', 'business-directory-plugin' ),
                    'fee_name'        => _x( 'Listing\'s plan name', 'admin settings', 'business-directory-plugin' ),
                    'fee_description' => _x( 'Listing\'s plan description', 'admin settings', 'business-directory-plugin' ),
                    'fee_details'     => _x( 'Listing\'s plan details', 'admin settings', 'business-directory-plugin' ),
                    'payment_details' => _x( 'Payment items details.', 'admin settings', 'business-directory-plugin' ),
                    'receipt_url'     => _x( 'URL where user can review and print payment receipt.', 'admin settings', 'business-directory-plugin' ),
                    'gateway'         => _x( 'Gateway used to process listing\'s payment.', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'        => 'email_templates',
            )
        );

		// Deprecated setting.
        wpbdp_register_setting(
            array(
                'id'           => 'email-templates-payment-abandoned',
                'type'         => 'email_template',
                'name'         => _x( 'Payment abandoned reminder message', 'settings', 'business-directory-plugin' ),
                'placeholders' => array(
                    'listing' => _x( 'Listing\'s title', 'admin settings', 'business-directory-plugin' ),
                    'link'    => _x( 'Checkout URL link', 'admin settings', 'business-directory-plugin' ),
                ),
                'group'        => 'email_templates',
				'requirements' => array( 'payment-abandonment' ),
            )
        );

        wpbdp_register_setting(
            array(
                'id'        => 'expiration-notices',
                'type'      => 'expiration_notices',
                'name'      => __( 'Renewal and expiration', 'business-directory-plugin' ),
                'default'   => self::get_default_expiration_notices(),
                'group'     => 'email_templates',
                'validator' => array( __class__, 'validate_expiration_notices' ),
            )
        );
    }

    public static function get_default_expiration_notices() {
        $notices = array();

        /* renewal-pending-message, non-recurring only */
        $notices[] = array(
            'event'         => 'expiration',
            'relative_time' => '+5 days', /* renewal-email-threshold, def: 5 days */
            'listings'      => 'non-recurring',
            'subject'       => '[[site-title]] [listing] - Your listing is about to expire',
            'body'          => 'Your listing "[listing]" is about to expire at [site]. You can renew it here: [link].',
        );
        /* listing-renewal-message, non-recurring only */
        $notices[] = array(
            'event'         => 'expiration',
            'relative_time' => '0 days', /* at time of expiration */
            'listings'      => 'non-recurring',
            'subject'       => 'Your listing on [site-title] expired',
            'body'          => "Your listing \"[listing]\" in category [category] expired on [expiration]. To renew your listing click the link below.\n[link]",
        );
        /* renewal-reminder-message, both recurring and non-recurring */
        $notices[] = array(
            'event'         => 'expiration',
            'relative_time' => '-5 days', /* renewal-reminder-threshold */
            'listings'      => 'both',
            'subject'       => '[[site-title]] [listing] - Expiration reminder',
            'body'          => "Dear Customer\nWe've noticed that you haven't renewed your listing \"[listing]\" for category [category] at [site] and just wanted to remind you that it expired on [expiration]. Please remember you can still renew it here: [link].",
        );
        /* listing-autorenewal-notice, recurring only, controlled by the send-autorenewal-expiration-notice setting */
        $notices[] = array(
            'event'         => 'expiration',
            'relative_time' => '+5 days', /*  renewal-email-threshold, def: 5 days */
            'listings'      => 'recurring',
            'subject'       => '[[site-title]] [listing] - Renewal reminder',
            'body'          => "Hey [author],\n\nThis is just to remind you that your listing [listing] is going to be renewed on [expiration] for another period.\nIf you want to review or cancel your subscriptions please visit [link].\n\nIf you have any questions, contact us at [site].",
        );
        /* listing-autorenewal-message, after IPN notification of renewal of recurring */
        $notices[] = array(
            'event'         => 'renewal',
            'relative_time' => '0 days',
            'listings'      => 'recurring',
            'subject'       => '[[site-title]] [listing] renewed',
            'body'          => "Hey [author],\n\nThanks for your payment. We just renewed your listing [listing] on [payment_date] for another period.\n\nIf you have any questions, contact us at [site].",
        );
        return $notices;
    }

    public static function validate_expiration_notices( $value ) {
        // We remove notices with no subject and no content.
        foreach ( array_keys( $value ) as $notice_id ) {
            $value[ $notice_id ] = array_map( 'trim', $value[ $notice_id ] );

            if ( empty( $value[ $notice_id ]['subject'] ) && empty( $value[ $notice_id ]['content'] ) ) {
                unset( $value[ $notice_id ] );
            }
        }

        // Remove enforce that there's always one notice applying to the expiration time of non-recurring listings. (#3795)

        return $value;
    }

	public static function setup_ajax_compat_mode( $setting, $value ) {
		_deprecated_function( __METHOD__, '5.12.1' );
	}

    private static function register_image_sizes() {
        $thumbnail_width  = absint( wpbdp_get_option( 'thumbnail-width' ) );
        $thumbnail_height = absint( wpbdp_get_option( 'thumbnail-height' ) );

        $max_width  = absint( wpbdp_get_option( 'image-max-width' ) );
        $max_height = absint( wpbdp_get_option( 'image-max-height' ) );

        $crop = (bool) wpbdp_get_option( 'thumbnail-crop' );

        add_image_size( 'wpbdp-thumb', $thumbnail_width, $crop ? $thumbnail_height : 9999, $crop ); // Thumbnail size.
        add_image_size( 'wpbdp-large', $max_width, $max_height, false ); // Large size.
    }

    private static function get_registered_image_sizes() {
        self::register_image_sizes();

        global $_wp_additional_image_sizes;

        $sizes = array(
			'uploaded'    => _x( 'Uploaded Image (no resize)', 'admin settings', 'business-directory-plugin' ),
			'wpbdp-thumb' => __( 'Default (size set below)', 'business-directory-plugin' ),
		);

        foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( $_size === 'wpbdp-thumb' ) {
				continue;
			}

            if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
                $name   = 'WP ' . ucwords( str_replace( '_', ' ', $_size ) );
                $width  = get_option( "{$_size}_size_w" );
                $height = get_option( "{$_size}_size_h" );
                $crop   = (bool) get_option( "{$_size}_crop" );
            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $name   = ucwords( str_replace( 'wpbdp', 'Directory', str_replace( array( '_', '-' ), ' ', $_size ) ) );
                $name   = str_replace( 'Directory Thumb', 'Directory Thumbnail', $name );
                $width  = $_wp_additional_image_sizes[ $_size ]['width'];
                $height = $_wp_additional_image_sizes[ $_size ]['height'];
                $crop   = (bool) $_wp_additional_image_sizes[ $_size ]['crop'];
            }

            $sizes[ $_size ] = sprintf(
                '%s (%s x %s px%s) ',
                $name,
                $width,
                $height == 9999 ? '*' : $height,
                $crop ? ' ' . _x( 'Cropped', 'settings', 'business-directory-plugin' ) : ''
            );
        }

        return $sizes;
    }

	/**
	 * @since v5.9
	 */
	public static function settings_misc() {
		// Tracking.
		wpbdp_register_settings_group( 'misc/misc', __( 'Miscellaneous', 'business-directory-plugin' ), 'misc' );

		wpbdp_register_setting(
			array(
				'id'    => 'tracking-on',
				'type'  => 'toggle',
				'name'  => __( 'Data Collection', 'business-directory-plugin' ),
				'desc'  => __( 'Allow Business Directory to anonymously collect information about your installed plugins, themes and WP version?', 'business-directory-plugin' ) .
					' <a href="https://businessdirectoryplugin.com/what-we-track/" target="_blank" rel="noopener">' . __( 'Learn more', 'business-directory-plugin' ) . '</a>',
				'group' => 'misc/misc',
			)
		);

		self::uninstall_section();
	}

	/**
	 * @since v5.9
	 */
	private static function uninstall_section() {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		wpbdp_register_settings_group(
			'uninstall',
			__( 'Uninstall', 'business-directory-plugin' ),
			'misc',
			array(
				'custom_form' => true,
			)
		);

        wpbdp_register_setting(
            array(
				'id'      => 'uninstall',
				'name'    => '',
				'type'    => 'none',
				'group'   => 'uninstall',
            )
        );

	}
}
