<?php
/**
 * Submit Listing View
 *
 * @package WPBDP\Views
 */

require_once WPBDP_PATH . 'includes/helpers/class-authenticated-listing-view.php';

class WPBDP__Views__Submit_Listing extends WPBDP__Authenticated_Listing_View {

	/**
	 * @var object WPBDP_Listing
	 */
	protected $listing = null;

    protected $sections      = array();
    protected $sections_keys = array();

    protected $prevent_save = false;
    protected $editing      = false;
    protected $data         = array();
    protected $messages     = array( 'general' => array() );

    private $available_plans         = false;
    public $skip_plan_selection      = false;
    public $skip_plan_payment        = false;
    public $category_specific_fields = false;
    public $fixed_plan_id            = 0;
    public $current_section          = '';

	protected $fixed_category = '';
	protected $category_count  = false;

	/**
	 * @var bool $is_ajax
	 */
	protected $is_ajax = false;

    public function get_title() {
        return __( 'Add Listing', 'business-directory-plugin' );
    }

    /**
     * Load custom resources used for this view only.
     * This is called in the parent class `enqueue_resources` function.
     *
     * @since 5.14.3
     */
    public function enqueue_custom_resources() {
        wp_enqueue_style( 'dashicons' );

        wp_enqueue_script(
            'wpbdp-submit-listing',
            WPBDP_ASSETS_URL . 'js/submit-listing.min.js',
            array(),
            WPBDP_VERSION,
            true
        );

        wp_enqueue_script( 'wpbdp-checkout' );

        // Required for textareas with HTML support via the WP Editor.
        // XXX: wp_enqueue_editor was added in WordPress 4.8.0.
        if ( function_exists( 'wp_enqueue_editor' ) ) {
            wp_enqueue_editor();
        }

        // Required for account creation (if enabled).
        if ( 'disabled' !== wpbdp_get_option( 'create-account-during-submit-mode' ) ) {
            wp_enqueue_script( 'password-strength-meter' );
        }

        wp_localize_script(
            'wpbdp-submit-listing',
            'wpbdpSubmitListingL10n',
            array(
				'categoriesPlaceholderTxt' => _x( 'Click this field to add categories', 'submit listing', 'business-directory-plugin' ),
				'completeListingTxt'       => _x( 'Complete Listing', 'submit listing', 'business-directory-plugin' ),
				'continueToPaymentTxt'     => _x( 'Continue to Payment', 'submit listing', 'business-directory-plugin' ),
				'isAdmin'                  => current_user_can( 'administrator' ),
				'waitAMoment'              => _x( 'Please wait a moment!', 'submit listing', 'business-directory-plugin' ),
				'somethingWentWrong'       => _x( 'Something went wrong!', 'submit listing', 'business-directory-plugin' ),
            )
        );

        do_action( 'wpbdp_submit_listing_enqueue_resources' );
    }

    public function saving() {
        return '1' === wpbdp_get_var( array( 'param' => 'save_listing' ), 'post' );
    }

    public function editing() {
        return $this->editing;
    }

	public function dispatch( $ajax_load = false ) {
		$this->is_ajax = ! empty( $ajax_load );

        $msg = '';
        if ( ! $this->can_submit( $msg ) ) {
            return wpbdp_render_msg( $msg );
        }

		$this->maybe_set_editing();

		if ( $this->should_use_ajax_load() ) {
			// If we aren't already doing ajax, add a placeholder to be filled later.
			return $this->show_form_placeholder();
		}

		// At this point, 'editing' is only set if 'wpbdp_view' is 'edit_listing'.
        if ( $this->editing ) {
            $message = '';

            if ( empty( $_REQUEST['listing_id'] ) ) {
                $message = _x( 'No listing ID was specified.', 'submit listing', 'business-directory-plugin' );
            } elseif ( ! wpbdp_user_can( 'edit', wpbdp_get_var( array( 'param' => 'listing_id' ) ) ) ) {
                $message = _x( "You can't edit this listing.", 'submit listing', 'business-directory-plugin' );
            }

            if ( $message ) {
                return wpbdp_render_msg( $message );
            }
		}

		$this->find_or_create_listing();

		$this->maybe_reset_form();

        if ( ! $this->editing && 'auto-draft' !== get_post_status( $this->listing->get_id() ) ) {
			$plan_id = absint( wpbdp_get_var( array( 'param' => 'listing_plan', 'default' => 0 ), 'post' ) );
			$plan    = wpbdp_get_fee_plan( $plan_id );
			if ( $plan && $plan->enabled ) {
				$this->maybe_update_listing_plan( $plan );
				$possible_payment = WPBDP_Payment::objects()->filter(
					array(
						'listing_id'   => $this->listing->get_id(),
						'payment_type' => 'initial',
						'status'       => 'pending',
					)
				)->get();

				if ( $possible_payment ) {
					return $this->_redirect(
						$possible_payment->get_checkout_url(),
						array( 'doing_ajax' => $this->is_ajax )
					);
				}
			}

			if ( $this->can_view_receipt() ) {
				// Show the receipt.
				return $this->done();
			}
        }

        if ( $this->editing && ! $this->listing->has_fee_plan() ) {
            if ( current_user_can( 'administrator' ) ) {
                return wpbdp_render_msg(
                    str_replace(
                        '<a>',
                        '<a href="' . esc_url( $this->listing->get_admin_edit_link() ) . '">',
                        _x( 'This listing can\'t be edited at this time because it has no plan associated. Please <a>edit the listing</a> on the backend and associate it to a plan.', 'submit listing', 'business-directory-plugin' )
                    ),
                    'error'
                );
            }

			return wpbdp_render_msg( _x( 'This listing can\'t be edited at this time. Please try again later or contact the admin if the problem persists.', 'submit listing', 'business-directory-plugin' ), 'error' );
        }

        $this->configure();
        $this->sections = $this->submit_sections();
        $this->sections_keys = array_keys( $this->sections );
        $this->prepare_sections();

        $save_listing = wpbdp_get_var( array( 'param' => 'save_listing' ), 'post' );
        if ( '1' === $save_listing && ! $this->prevent_save ) {
            $res = $this->save_listing();

            if ( is_wp_error( $res ) ) {
                $errors = $res->get_error_messages();

                foreach ( $errors as $e ) {
                    $this->messages( $e, 'error', 'general' );
                }
            } else {
                return $res;
            }
        }

        return $this->show_form();
    }

	/**
	 * Load the new listing form with ajax when the page might be cached.
	 *
	 * @since 6.2.2
	 * @return bool
	 */
	private function should_use_ajax_load() {
		$use_ajax = empty( $_POST ) && ! wp_doing_ajax() && ! $this->is_ajax && ! $this->editing;
		if ( ! $use_ajax || is_user_logged_in() ) {
			return false;
		}

		/**
		 * @since 6.2.2
		 */
		return apply_filters( 'wpbdp_ajax_load_form', $use_ajax );
	}

	/**
	 * Show a placeholder and load the form with ajax to avoid page caching.
	 *
	 * @since 6.2.2
	 * @return string
	 */
	private function show_form_placeholder() {
		return '<div id="wpbdp-submit-listing" class="wpbdp-submit-page wpbdp-page">
    	<form action="" method="post" data-ajax-url="' . esc_url( wpbdp_ajax_url() ) . '" enctype="multipart/form-data">
		</form>
		</div>';
	}

	/**
	 * @since 6.2.2
	 * @return string
	 */
	private function show_form() {
		return wpbdp_render(
			'submit-listing',
			array(
				'listing'  => $this->listing,
				'sections' => $this->sections,
				'messages' => $this->prepare_messages(),
				'is_admin' => current_user_can( 'administrator' ),
				'editing'  => $this->editing,
				'submit'   => $this,
			),
			false
		);
	}

	/**
	 * @since 6.2.2
	 * @return array
	 */
	private function prepare_messages() {
		if ( current_user_can( 'administrator' ) ) {
			$this->messages( _x( 'You\'re logged in as admin, payment will be skipped.', 'submit listing', 'business-directory-plugin' ), 'notice', 'general' );
		}

		if ( $this->current_section === reset( $this->sections_keys ) ) {
			// Show message on the first page.
			$instructions = trim( wpbdp_get_option( 'submit-instructions' ) );
			if ( $instructions ) {
				$this->messages( $instructions, 'tip', 'general' );
			}
		}

		/**
		 * Add custom validation when a listing form is submitted.
		 *
		 * @since 5.15
		 */
		$this->messages = apply_filters( 'wpbdp_submit_validation_errors', $this->messages, compact( 'this' ) );

		// Prepare $messages for template.
		$messages = array();
		foreach ( $this->messages as $context => $items ) {
			$messages[ $context ] = '';

			foreach ( $items as $i ) {
				$messages[ $context ] .= sprintf( '<div class="wpbdp-msg %s">%s</div>', $i[1], $i[0] );
			}
		}

		return $messages;
	}

	/**
	 * Handle "Clear Form" request.
	 *
	 * @since 6.2.2
	 */
	private function maybe_reset_form() {
		$reset = wpbdp_get_var( array( 'param' => 'reset' ), 'post' );
		if ( 'reset' !== $reset ) {
			return;
		}

		if ( $this->editing ) {
			$url = wpbdp_url( 'edit_listing', $this->listing->get_id() );
		} else {
			$check_page = wpbdp_get_option( 'disable-submit-listing' );
			wp_delete_post( $this->listing->get_id(), true );
			if ( $check_page ) {
				// If submit listing page is turned off, we need to know where to redirect.
				$url = wpbdp_get_var( array( 'param' => '_wp_http_referer' ), 'post' );
			} else {
				$url = wpbdp_url( 'submit_listing' );
			}
		}

		$this->_redirect(
			$url,
			array( 'doing_ajax' => $this->is_ajax )
		);
	}

	/**
	 * Check if the user has permission to view the receipt.
	 *
	 * @since 5.9.1
	 *
	 * @return bool
	 */
	private function can_view_receipt() {
		if ( current_user_can( 'administrator' ) ) {
			return true;
		}

		$listing_id = $this->listing->get_id();
		$listing    = get_post( $listing_id );

		$user_id = get_current_user_id();
		if ( $user_id ) {
			$is_author = (int) $listing->post_author === $user_id;
			if ( $is_author ) {
				return true;
			}
		}

		return apply_filters( 'wpbdp_can_view_receipt', false, array( 'post' => $listing ) );
	}

    public function ajax_reset_plan() {
        $res = new WPBDP_AJAX_Response();

        if ( ! $this->can_submit( $msg ) || empty( $_POST['listing_id'] ) ) {
            wp_die();
        }

		$this->find_or_create_listing();

        if ( ! $this->listing->has_fee_plan() ) {
            wp_die();
        }

        if ( ! $this->editing ) {
            // Store previous values before clearing.
            $plan                              = $this->listing->get_fee_plan();
            $this->data['previous_plan']       = $plan ? $plan->fee_id : 0;
            $this->data['previous_categories'] = wp_get_post_terms( $this->listing->get_id(), WPBDP_CATEGORY_TAX, array( 'fields' => 'ids' ) );

            // Clear plan and categories.
            $this->listing->set_fee_plan( null );
        }

        wp_set_post_terms( $this->listing->get_id(), array(), WPBDP_CATEGORY_TAX, false );

        $this->ajax_sections();
    }

    /**
     * Additional configuration for the submit process, prior to the sections being called.
     *
     * @since 5.1.2
     */
    private function configure() {
        $this->set_skip_plan_payment();

        $this->category_specific_fields = $this->category_specific_fields();

        // Maybe skip plan selection?
        if ( $this->skip_plan_payment ) {
            $this->skip_plan_selection = ( 1 === count( $this->get_available_plans() ) );
        }

        $this->current_section = wpbdp_get_var( array( 'param' => 'current_section', 'default' => '' ), 'post' );
    }

	/**
	 * Show "Complete Listing" instead of "Continue to Payment" if the selected plan is free.
	 *
	 * @since 5.10
	 */
	private function set_skip_plan_payment() {
		$this->skip_plan_payment = true;

		$plans = $this->get_selected_or_available_plans();
		foreach ( $plans as $plan ) {
			if ( 'flat' !== $plan->pricing_model || 0.0 !== $plan->amount ) {
				$this->skip_plan_payment = false;
			}
		}
	}

	/**
	 * Avoid page caching by loading the form with ajax.
	 * This is called when the page includes a placeholder.
	 *
	 * @since 6.2.2
	 */
	public function ajax_load_form() {
		$response = array(
			'form' => $this->dispatch( 'ajax' ),
		);

		wp_send_json_success( $response );
	}

    public function ajax_sections() {
        $res = new WPBDP_AJAX_Response();

        if ( ! $this->can_submit( $msg ) || empty( $_POST['listing_id'] ) ) {
            $res->send_error( $msg );
        }

		$this->find_or_create_listing();

        // Ignore 'save_listing' for AJAX requests in order to leave it as the final POST with all the data.
        if ( $this->saving() ) {
            unset( $_POST['save_listing'] );
        }

        $this->configure();
        $this->sections = $this->submit_sections();
        $this->sections_keys = array_keys( $this->sections );
        $this->prepare_sections();

        $sections = array();
        foreach ( $this->sections as $section ) {
            $messages = ( ! empty( $this->messages[ $section['id'] ] ) ) ? $this->messages[ $section['id'] ] : array();

            $messages_html = '';
            foreach ( $messages as $i ) {
                $messages_html .= sprintf( '<div class="wpbdp-msg %s">%s</div>', $i[1], $i[0] );
            }

            $sections[ $section['id'] ]         = $section;
            $sections[ $section['id'] ]['html'] = wpbdp_render(
                'submit-listing-section',
                array(
                    'listing'  => $this->listing,
                    'section'  => $section,
                    'messages' => $messages_html,
                    'is_admin' => current_user_can( 'administrator' ),
                    'submit'   => $this,
                    'editing'  => $this->editing,
                )
            );
        }

        $res->add( 'listing_id', $this->listing->get_id() );
        $res->add( 'messages', $this->messages );
        $res->add( 'sections', $sections );
        $res->send();
    }

    public function messages( $msg, $type = 'notice', $context = 'general' ) {
		$this->get_parent_section( $context );

        if ( ! isset( $this->messages[ $context ] ) ) {
            $this->messages[ $context ] = array();
        }

        foreach ( (array) $msg as $msg_ ) {
            $this->messages[ $context ][] = array( $msg_, $type );
        }

        if ( isset( $this->sections[ $context ] ) ) {
            $this->sections[ $context ]['flags'][] = 'has-message';

            if ( 'error' === $type ) {
                $this->sections[ $context ]['flags'][] = 'has-error';
            }
        }
    }

	private function get_parent_section( &$context ) {
		if ( isset( $this->sections[ $context ] ) ) {
			return;
		}

		$parent = $context;
		foreach ( $this->sections as $id => $section ) {
			if ( isset( $section['include'][ $context ] ) ) {
				$context = $id;
			}
		}
	}

    private function can_submit( &$msg = null ) {
        // TODO: Can we use get_post()?
        $post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

        if ( is_object( $post ) ) {
            // Submit shortcode is exempt from restrictions.
            $submit_shortcodes = array( 'businessdirectory-submit-listing', 'businessdirectory-submitlisting', 'business-directory-submitlisting', 'business-directory-submit-listing', 'WPBUSDIRMANADDLISTING' );

            foreach ( $submit_shortcodes as $test_shortcode ) {
                if ( has_shortcode( $post->post_content, $test_shortcode ) ) {
                    return true;
                }
            }
        }

        if ( 'submit_listing' === wpbdp_current_view() && wpbdp_get_option( 'disable-submit-listing' ) ) {
            if ( current_user_can( 'administrator' ) ) {
                $msg = _x( '<b>View not available</b>. Do you have the "Disable Frontend Listing Submission?" setting checked?', 'templates', 'business-directory-plugin' );
            } else {
                $msg = _x( 'Listing submission has been disabled. Contact the administrator for details.', 'templates', 'business-directory-plugin' );
            }

            return false;
        }

        return true;
    }

	/**
	 * Check if we should be editing, when not in edit mode. This happens
	 * when the url to submit a new listing includes a listing id.
	 *
	 * @since 6.2.2
	 * @return void
	 */
	private function maybe_set_editing() {
		if ( $this->editing ) {
			return;
		}

		$listing_id = wpbdp_get_var( array( 'param' => 'listing_id', 'sanitize' => 'absint', 'default' => 0 ), 'request' );
		$editing_id = wpbdp_get_var( array( 'param' => 'editing', 'sanitize' => 'absint', 'default' => 0 ), 'post' );

		$this->editing = $listing_id && $editing_id;
	}

	/**
	 * @return void
	 */
	private function find_or_create_listing() {
		$listing_id = wpbdp_get_var( array( 'param' => 'listing_id', 'sanitize' => 'absint', 'default' => 0 ), 'request' );

		// Check if the same listing should be retrieved.
		$editing = $this->editing || ( ! empty( $_POST ) && ! $this->is_ajax );

		if ( $listing_id && $editing && false !== get_post_status( $listing_id ) ) {
			$this->listing = wpbdp_get_listing( $listing_id );
		} else {
			$this->create_listing();
		}
		$this->is_listing_allowed( $listing_id );
	}

	/**
	 * @since 6.2.2
	 */
	private function create_listing() {
		$listing_id = wp_insert_post(
			array(
				'post_author' => $this->default_author(),
				'post_type'   => WPBDP_POST_TYPE,
				'post_status' => 'auto-draft',
				'post_title'  => '(no title)',
			)
		);

		if ( ! $listing_id ) {
            die();
        }

		$listing = wpbdp_get_listing( $listing_id );
		$listing->set_fee_plan( $this->single_plan() );

		$this->set_fixed_category_id();
		if ( $this->fixed_category ) {
			wp_set_post_terms( $listing_id, $this->fixed_category, WPBDP_CATEGORY_TAX, false );
		}

		$this->listing = $listing;
	}

	/**
	 * Check if the user has permission to edit the listing in url.
	 *
	 * @since 6.2.2
	 * @return void
	 */
	private function is_listing_allowed( $listing_id ) {
		$auth_parameters = array( 'wpbdp_view' => 'submit_listing' );

		if ( $this->editing ) {
			$auth_parameters = array(
				'wpbdp_view'          => 'edit_listing',
				'redirect_query_args' => array( 'listing_id' => $listing_id ),
			);
		}
		$auth_parameters['doing_ajax'] = $this->is_ajax;

		// Perform auth.
		$this->_auth_required( $auth_parameters );
	}

	/**
	 * Get the author id if logged in, or the default by id or login.
	 *
	 * @since 5.9.2
	 */
	private function default_author() {
        $post_author = get_current_user_id();
		if ( $post_author ) {
			return $post_author;
		}

		$post_author = wpbdp_get_option( 'default-listing-author' );
		if ( empty( $post_author ) ) {
			$post_author = $this->get_default_admin_user();
		}

		if ( is_numeric( $post_author ) ) {
			return $post_author;
		}

		// Check if a user login was used.
		$author = get_user_by( 'login', $post_author );
		if ( $author ) {
			$post_author = $author->ID;
		}

		return $post_author;
	}

	/**
	 * Find the ID of the admin email for this site.
	 * If not found, get the first admin user as default.
	 *
	 * @since 6.2.5
	 * @return int
	 */
	private function get_default_admin_user() {
		$admin = get_user_by( 'email', get_option( 'new_admin_email' ) );
		if ( $admin ) {
			return $admin->ID;
		}

		$admin_users = get_users(
			array(
				'fields' => array( 'ID' ),
				'role'   => 'administrator',
				'number' => 1,
			)
		);

		if ( $admin_users ) {
			$admin = $admin_users[0];
		}

		return $admin ? $admin->ID : 1;
	}

    public function get_listing() {
        return $this->listing;
    }

    public function prevent_save() {
        $this->prevent_save = true;
    }

    private function submit_sections() {
        $sections = array();

		$this->set_fixed_category_id();

        if ( $this->can_edit_plan_or_categories() && ! $this->skip_plan_and_category() ) {
            $sections['plan_selection'] = array(
                'title' => $this->skip_plan_selection ? _x( 'Category selection', 'submit listing', 'business-directory-plugin' ) : _x( 'Category & plan selection', 'submit listing', 'business-directory-plugin' ),
            );
        }

        $sections['listing_fields'] = array(
            'title'               => __( 'Listing Information', 'business-directory-plugin' ),
            'content_css_classes' => 'wpbdp-grid',
		);

        $this->add_images_page( $sections );

        $sections = apply_filters( 'wpbdp_submit_sections', $sections, $this );

		$this->add_account_page( $sections );

		if ( ! $this->editing ) {
			if ( wpbdp_get_option( 'display-terms-and-conditions' ) ) {
				$sections['terms_and_conditions'] = array(
					'title' => __( 'Terms and Conditions', 'business-directory-plugin' ),
				);
			}
		}

		$this->merge_sections( $sections );

		if ( ! $this->editing ) {
			// Add clear button last
			$sections['listing_fields']['include']['clear_form'] = array(
				'title'  => '',
			);
		}

        foreach ( $sections as $section_id => &$s ) {
            $s['id']    = $section_id;
            $s['html']  = '';
            $s['flags'] = array();
        }

        return $sections;
    }

	/**
	 * Add images page in listing form.
	 */
	private function add_images_page( &$sections ) {
		if ( ! wpbdp_get_option( 'allow-images' ) ) {
			return;
		}

		if ( $this->plan_allows_images() ) {
			$sections['listing_images'] = array(
				'title' => __( 'Listing Images', 'business-directory-plugin' ),
			);
		}
	}

	/**
	 * Check if the plan is selected and has images allowed.
	 *
	 * @since 5.11
	 */
	private function plan_allows_images() {
		$listing = $this->listing;
		$plan    = $listing->get_fee_plan();
		return $plan ? absint( $plan->fee_images ) : 1;
	}

	/**
	 * Since the plan is checked after the sections are added, check the plan
	 * to see if the images section should be removed.
	 *
	 * @since 5.11
	 */
	private function maybe_remove_images_section() {
		if ( isset( $this->sections['listing_images'] ) && ! $this->plan_allows_images() ) {
			unset( $this->sections['listing_images'] );
		}
	}

	private function merge_sections( &$sections ) {
		$this->combine_image_pages( $sections );
		$this->add_maps_to_listing( $sections );
		$this->maybe_merge_terms( $sections );
		$this->add_recaptcha_last( $sections );
	}

	private function combine_image_pages( &$sections ) {
		if ( isset( $sections['listing_images'] ) && isset( $sections['attachments'] ) ) {
			$this->add_to_section( 'listing_images', 'attachments', $sections );
		}
	}

	/**
	 * Move terms to listing page if no recaptcha.
	 */
	private function maybe_merge_terms( &$sections ) {
		if ( isset( $sections['terms_and_conditions'] ) && ! isset( $sections['recaptcha'] ) ) {
			$this->add_to_section( 'listing_fields', 'terms_and_conditions', $sections );
		}
	}

	private function add_to_section( $parent, $child, &$sections ) {
		if ( empty( $sections[ $parent ]['include'] ) ) {
			$sections[ $parent ]['include'] = array();
		}
		$sections[ $parent ]['include'][ $child ] = $sections[ $child ];
		unset( $sections[ $child ] );
	}

	/**
	 * Add account page in listing form.
	 */
	private function add_account_page( &$sections ) {
		if ( $this->editing || is_user_logged_in() ) {
			return;
		}

		if ( ! wpbdp_get_option( 'require-login' ) && 'disabled' !== wpbdp_get_option( 'create-account-during-submit-mode' ) ) {
			$sections['account_creation'] = array(
				'title' => __( 'Account Creation', 'business-directory-plugin' ),
			);
		}
	}

	/**
	 * Include map location in listing section.
	 *
	 * @since 5.11
	 */
	private function add_maps_to_listing( &$sections ) {
		if ( isset( $sections['googlemaps_place_chooser'] ) ) {
			$this->add_to_section( 'listing_fields', 'googlemaps_place_chooser', $sections );
		}
	}

	private function add_recaptcha_last( &$sections ) {
		if ( ! isset( $sections['recaptcha'] ) ) {
			return;
		}

		$last = key( array_slice( $sections, -1, 1, true ) );
		if ( $last === 'recaptcha' ) {
			$last = array_slice( array_keys( $sections ), -2, 1 );
			$last = $last[0];
		}

		$this->add_to_section( $last, 'recaptcha', $sections );
	}

    private function can_edit_plan_or_categories() {
		if ( ! $this->editing || ! $this->listing->has_fee_plan() ) {
            return true;
        }

        $plan = $this->listing->get_fee_plan();
        if ( ! $plan->fee ) {
            return false;
        }

        if ( 'flat' === $plan->fee->pricing_model ) {
            return true;
        }

        return false;
    }

	/**
	 * If there are no categories and only one plan, skip page 1.
	 *
	 * @since 5.10
	 *
	 * @return bool
	 */
	private function skip_plan_and_category() {
		$skip = $this->skip_plan_selection && $this->category_count === 1;
		return apply_filters( 'wpbdp_skip_page_1', $skip, compact( 'this' ) );
	}

    private function prepare_sections() {
		$section_ids  = array_keys( $this->sections );
		$first_key    = reset( $section_ids );
		$next_section = $this->current_section ? '' : $first_key;

		foreach ( $this->sections as $k => &$section ) {
			$previous_section = $this->find_prev_section( $section['id'] );

			$this->prepare_single_section( $section, $next_section, $previous_section );

			if ( empty( $section ) ) {
				unset( $this->sections[ $k ] );
				$this->sections_keys = array_keys( $this->sections );

				// If a section was removed, reset the previous one.
				if ( $previous_section ) {
					$this->prepare_single_section( $this->sections[ $previous_section ], $next_section );
				}
			}
		}

        if ( $next_section ) {
            $this->current_section = $next_section;
            $this->prevent_save    = true;
        }

        $this->sections = apply_filters( 'wpbdp_submit_prepare_sections', $this->sections, $this );
    }

	/**
	 * @since 5.11.2
	 */
	private function prepare_single_section( &$section, &$next_section, $previous_section = '' ) {
		$this->add_html_to_section( $section );

		// Exclude any disabled sections.
		if ( is_array( $section['flags'] ) && in_array( 'disabled', $section['flags'], true ) ) {
			$section = array();
			return;
		}

		if ( $section['id'] === 'plan_selection' ) {
			$this->maybe_remove_images_section();
		}

		$section['flags'][]         = $section['state'];
        // If there is no previous section, we should not set it to the next section. It should be blank by default, only allowing next.
		$section['prev_section']    = $previous_section ? $previous_section : '';
		$section['next_section']    = $this->find_next_section( $section['id'] );

		$same_page = array_intersect( array( 'has-error', 'has-message' ), $this->sections[ $section['id'] ]['flags'] );
		if ( ! $next_section && ! empty( $same_page ) ) {
			$next_section = $section['id'];
			return;
		}

		if ( $section['id'] === $this->current_section ) {
			// Compatibility with attachments module.
			$file_upload = wpbdp_get_var( array( 'param' => 'attachment-upload' ), 'post' );
			if ( in_array( $section['id'], array( 'attachments', 'listing_images' ) ) && ! empty( $file_upload ) ) {
				return;
			}
			$next_section = $section['next_section'];
		}

		if ( ! $next_section || $next_section !== $section['id'] ) {
			$section['flags'][] = 'hidden';
		}
	}

	private function add_html_to_section( &$section, $level = 1 ) {
		$callback = WPBDP_Utils::normalize( $section['id'] );

		$section['state'] = 'disabled';
		$section['html']  = '';

		if ( ! method_exists( $this, $callback ) ) {
			$section = apply_filters( 'wpbdp_submit_section_' . $section['id'], $section, $this );
			return;
		}

		$res     = call_user_func( array( $this, $callback ) );
		$html    = '';
		$enabled = false;

		if ( is_array( $res ) ) {
			$enabled = $res[0];
			$html    = $res[1];
		} elseif ( is_string( $res ) && ! empty( $res ) ) {
			$enabled = true;
			$html    = $res;
		} elseif ( false === $res ) {
			$section['flags'][] = 'hidden';
		}

		$section['state'] = $enabled ? 'enabled' : 'disabled';
		$section['html']  .= $html;

		$section = apply_filters( 'wpbdp_submit_section_' . $section['id'], $section, $this );

		if ( ! isset( $section['include'] ) ) {
			return;
		}

		++ $level;
		foreach ( $section['include'] as $id => $sub_section ) {
			$sub_section['id'] = $id;
			$this->add_html_to_section( $sub_section, $level );
			if ( ! empty( $sub_section['title'] ) && ! empty( $sub_section['html'] ) ) {
				$section['html'] .= '<h3>' . esc_html( $sub_section['title'] ) . '</h3>';
			}
			$section['html'] .= $sub_section['html'];
		}
	}

    private function section_render( $template, $vars = array(), $result = true ) {
        $vars['listing'] = $this->listing;
        $vars['editing'] = $this->editing;
        $vars['_submit'] = $this;
        $output          = wpbdp_render( $template, $vars, false );

        return array( $result, $output );
    }

    private function plan_selection() {
        global $wpbdp;

        $plans = $this->get_available_plans();

        if ( ! $plans && ! $this->editing ) {
            $msg = _x( 'Can not submit a listing at this moment. Please try again later.', 'submit listing', 'business-directory-plugin' );
            if ( current_user_can( 'administrator' ) ) {
                $msg .= '<br><br>';
                $msg .= _x( '<b>There are no Plans available</b>, without a plan site users can\'t submit a listing. %s to create a plan', 'templates', 'business-directory-plugin' );

                $msg = sprintf(
                    $msg,
                    sprintf(
                        '<a href="%s">%s</a>',
                        esc_url( admin_url( 'admin.php?page=wpbdp-admin-fees' ) ),
                        esc_html__( 'Go to "Plans"', 'business-directory-plugin' )
                    )
                );
            }
            wp_die( wp_kses_post( $msg ) );
        }

        $msg = _x( 'Listing submission is not available at the moment. Contact the administrator for details.', 'templates', 'business-directory-plugin' );

        if ( current_user_can( 'administrator' ) ) {
            $msg = _x( '<b>View not available</b>, there is no "Category" association field. %s and create a new field with this association, or assign this association to an existing field', 'templates', 'business-directory-plugin' );

            $msg = sprintf(
                $msg,
                sprintf(
                    '<a href="%s">%s</a>',
                    esc_url( admin_url( 'admin.php?page=wpbdp_admin_formfields' ) ),
                    esc_html__( 'Go to "Form Fields"', 'business-directory-plugin' )
                )
            );
        }

		$category_field = wpbdp_get_form_fields( 'association=category&unique=1' );

		if ( empty( $category_field ) ) {
			wp_die( wp_kses_post( $msg ) );
		}

		// Returns null if value isn't posted.
		$categories      = $category_field->value_from_POST();
		$should_validate = ! empty( $_POST ) && ( ! empty( $categories ) || $categories !== null );

        if ( $this->editing ) {
            $this->data['previous_categories'] = $this->listing->get_categories( 'ids' );

            $plan_id = $this->listing->get_fee_plan()->fee_id;

            if ( ! $categories && ! empty( $_POST ) ) {
                $this->data['previous_categories'] = array();
                $this->messages( _x( 'Please select a category.', 'submit listing', 'business-directory-plugin' ), 'error', 'plan_selection' );
            }
        } else {
			$plan_id = $this->new_listing_plan( $categories, $should_validate );
        }

        $errors = array();

        if ( $should_validate && ! $category_field->validate( $categories, $errors ) ) {
			/** @phpstan-ignore-next-line */
            foreach ( $errors as $e ) {
				if ( ! isset( $this->messages['plan_selection'] ) ) {
					$this->messages( $e, 'error', 'plan_selection' );
				}
            }

            $this->prevent_save = true;
		} elseif ( $categories && ! $plan_id ) {
			$this->messages( __( 'Please choose a plan.', 'business-directory-plugin' ), 'error', 'plan_selection' );
		} elseif ( $categories ) {
            $plan = wpbdp_get_fee_plan( $plan_id );

            if ( ! $plan || ! $plan->enabled || ! $plan->supports_category_selection( $categories ) ) {
                if ( $this->editing ) {
					if ( ! $plan->enabled ) {
						$this->messages( _x( 'Current active plan is disabled. Please select another plan.', 'submit listing', 'business-directory-plugin' ), 'error', 'plan_selection' );
					} else {
						$this->messages( _x( 'Please choose a valid category for your plan.', 'submit listing', 'business-directory-plugin' ), 'error', 'plan_selection' );
					}
                } else {
                    $this->messages( _x( 'Please choose a valid plan for your category selection.', 'submit listing', 'business-directory-plugin' ), 'error', 'plan_selection' );
                }

                $this->prevent_save = true;
            } else {
                // Set categories.
                wp_set_post_terms( $this->listing->get_id(), $categories, WPBDP_CATEGORY_TAX, false );

                if ( ! $this->editing ) {
                    // Set plan.
                    $this->listing->set_fee_plan( $plan );
                }
            }
		} elseif ( $this->skip_plan_selection ) {
            $current_categories = $this->listing->get_categories( 'ids' );

            wp_set_post_terms( $this->listing->get_id(), $current_categories, WPBDP_CATEGORY_TAX, false );
        }

        if ( $this->editing ) {
            if ( ! $categories ) {
                $this->prevent_save = true;
            }
		} else {

			if ( $this->skip_plan_selection && ! $this->category_specific_fields ) {
				$this->data['previous_categories'] = $this->listing->get_categories( 'ids' );
			} else {
				$has_categories = $categories || $this->listing->get_categories( 'ids' );
				if ( $this->listing->get_fee_plan() && $has_categories ) {
					return $this->section_render( 'submit-listing-plan-selection-complete' );
				}

				$this->prevent_save = true;
			}
		}

		$selected_plan = $this->get_selected_plan( $plan_id );

		$this->set_fixed_category_id();
		$category_count      = $this->category_count;
		$selected_categories = $this->fixed_category ? $this->fixed_category : $this->get_selected_category( $categories );
        return $this->section_render( 'submit-listing-plan-selection', compact( 'category_field', 'category_count', 'plans', 'selected_categories', 'selected_plan' ) );
    }

	/**
	 * @since 5.10
	 *
	 * @return void
	 */
	private function set_fixed_category_id() {
		if ( $this->category_count !== false ) {
			return;
		}

		$this->category_count = (int) wp_count_terms( WPBDP_CATEGORY_TAX, array( 'hide_empty' => false ) );

		if ( 1 !== $this->category_count ) {
			return;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => WPBDP_CATEGORY_TAX,
				'hide_empty' => false,
			)
		);
		$term = reset( $terms );
		$this->fixed_category = $term->term_id;
	}

	/**
	 * Select the category for the listing or from the POST values.
	 */
	private function get_selected_category( $posted_categories ) {
		$selected_categories = ! empty( $this->data['previous_categories'] ) ? $this->data['previous_categories'] : array();
		return $posted_categories ? $posted_categories : $selected_categories;
	}

	/**
	 * Get the plan from the new listing form.
	 *
	 * @return int
	 */
	private function new_listing_plan( $categories, $should_validate ) {
		if ( $this->skip_plan_selection && ! $this->category_specific_fields ) {
			$plan_id = $this->fixed_plan_id;
			if ( ! $plan_id ) {
				$this->single_plan();
				$plan_id = $this->fixed_plan_id;
			}

			if ( $plan_id && ! $this->listing->get_fee_plan() ) {
				$this->listing->set_fee_plan( $plan_id );
			}

			if ( $this->saving() && ! $categories && $should_validate ) {
				$this->messages( _x( 'Please select a category.', 'submit listing', 'business-directory-plugin' ), 'error', 'plan_selection' );
				$this->prevent_save = true;
			}
		} else {
			$plan_id = absint( wpbdp_get_var( array( 'param' => 'listing_plan', 'default' => 0 ), 'post' ) );
		}

		return $plan_id;
	}

	private function get_selected_plan( $plan_id ) {
		$selected_plan = $plan_id;

		if ( ! $this->editing && ! $this->skip_plan_selection ) {
			$selected_plan = ! empty( $this->data['previous_plan'] ) ? $this->data['previous_plan'] : 0;
		}

		return $selected_plan;
	}

    /**
     * Called dynamically from prepare_sections when the section id is set to
     * 'listing_fields'.
     */
    private function listing_fields( $preview = false ) {
        $form_fields         = wpbdp_get_form_fields( array( 'association' => '-category' ) );
        $form_fields         = apply_filters_ref_array( 'wpbdp_listing_submit_fields', array( &$form_fields, &$this->listing ) );
        $field_values        = array();

        $validation_errors = array();
        $fields            = array();

        foreach ( $form_fields as $field ) {
            if ( ! $preview && ! $field->validate_categories( $this->listing->get_categories( 'ids' ) ) ) {
                continue;
            }

            $value = ! empty( $field_values[ $field->get_id() ] ) ? $field_values[ $field->get_id() ] : $field->value( $this->listing->get_id() );

            if ( 'title' === $field->get_association() && '(no title)' === $value ) {
                $value = '';
            }

            $posted_value = $field->value_from_POST();

            if ( null !== $posted_value ) {
                $value = $posted_value;
            }

            $field_values[ $field->get_id() ] = $value;

            if ( $this->should_validate_section( 'listing_fields' ) ) {
                $field_errors = null;
                $validate_res = apply_filters_ref_array(
                    'wpbdp_listing_submit_validate_field',
                    array(
                        $field->validate( $value, $field_errors ),
                        &$field_errors,
                        &$field,
                        $value,
                        &$this->listing,
                    )
                );

                if ( ! $validate_res ) {
                    $validation_errors[ $field->get_id() ] = $field_errors;
					$field->add_validation_error( $field_errors );
                } else {
                    $field->store_value( $this->listing->get_id(), $value );
                }
            }

            $fields[] = $field;
        }

        // FIXME: fake this (for compatibility with modules) until we move everything to wpbdp_save_listing() and
        // friends. See #2945.
		// phpcs:ignore WordPress.NamingConventions.ValidHookName
        do_action_ref_array( 'WPBDP_Listing::set_field_values', array( &$this->listing, $field_values ) );

        if ( $validation_errors ) {
            $this->messages( __( 'Please check the form for errors, correct them and submit again.', 'business-directory-plugin' ), 'error', 'listing_fields' );
            $this->prevent_save = true;
        }

        return $this->section_render( 'submit-listing-fields', compact( 'fields', 'field_values', 'validation_errors' ) );
    }

    /**
     * @param array $images_  An array of images.
     * @param array $meta     An of metadata for images.
     */
    public function sort_images( $images_, $meta ) {
        // Sort inside $meta first.
		WPBDP_Utils::sort_by_property( $meta, 'order' );
		$meta = array_reverse( $meta, true );

        // Sort $images_ considering $meta.
        $images = array();

        foreach ( array_keys( $meta ) as $img_id ) {
            if ( in_array( $img_id, $images_, true ) ) {
                $images[] = $img_id;
            }
        }

        foreach ( $images_ as $img_id ) {
            if ( in_array( $img_id, $images, true ) ) {
                continue;
            }

            $images[] = $img_id;
        }

        return $images;
	}

    private function listing_images() {
        if ( ! wpbdp_get_option( 'allow-images' ) ) {
            return false;
        }

		$plan = $this->get_plan_for_listing();
        if ( ! $plan ) {
            return false;
        }

        $image_slots = absint( $plan->fee_images );

        if ( ! $image_slots ) {
            return false;
        }

        $validation_error = '';
		$listing          = $this->listing;

		$thumbnail_id = absint( wpbdp_get_var( array( 'param' => '_thumbnail_id', 'default' => 0 ), 'post' ) );

		if ( $thumbnail_id ) {
			$listing->set_thumbnail_id( $thumbnail_id );
		}

		$images = $this->listing->get_images( 'ids', true );

        $images_meta = $this->listing->get_images_meta();

		$should_validate = $this->should_validate_section( 'listing_images' );

        // Maybe update meta.
		if ( $should_validate && ! empty( $_POST['images_meta'] ) ) {
			$order = 0;
            foreach ( $images as $img_id ) {
				$updated_meta = wpbdp_get_var( array( 'param' => 'images_meta' ), 'post' );
				$updated_meta = ! empty( $updated_meta[ $img_id ] ) ? (array) $updated_meta[ $img_id ] : array();

				$new_order = ! empty( $updated_meta['order'] ) ? absint( $updated_meta['order'] ) : $order;
				update_post_meta( $img_id, '_wpbdp_image_weight', $new_order );
				update_post_meta( $img_id, '_wpbdp_image_caption', ! empty( $updated_meta['caption'] ) ? trim( $updated_meta['caption'] ) : '' );
				$order = $new_order + 1;

                $images_meta[ $img_id ] = $updated_meta;
            }
        }

		if ( $should_validate && ! count( $images_meta ) && wpbdp_get_option( 'enforce-image-upload' ) ) {
            $this->prevent_save = true;
            $this->messages( _x( 'Image upload is required, please provide at least one image and submit again.', 'listing submit', 'business-directory-plugin' ), 'error', 'listing_images' );
        }

        $image_slots_remaining = $image_slots - count( $images );

        $image_min_file_size = intval( wpbdp_get_option( 'image-min-filesize' ) );
        $image_min_file_size = $image_min_file_size ? size_format( $image_min_file_size * 1024 ) : '0';

        $image_max_file_size = intval( wpbdp_get_option( 'image-max-filesize' ) );
        $image_max_file_size = $image_max_file_size ? size_format( $image_max_file_size * 1024 ) : '0';

        $image_min_width = intval( wpbdp_get_option( 'image-min-width' ) );
        $image_max_width = intval( wpbdp_get_option( 'image-max-width' ) );
        $image_min_height = intval( wpbdp_get_option( 'image-min-height' ) );
        $image_max_height = intval( wpbdp_get_option( 'image-max-height' ) );

		return $this->section_render(
			'submit-listing-images',
			compact(
				'image_max_file_size',
				'image_min_file_size',
				'image_min_width',
				'image_max_width',
				'image_min_height',
				'image_max_height',
				'images',
				'images_meta',
				'image_slots',
				'image_slots_remaining',
				'thumbnail_id',
				'listing'
			)
		);
    }

    private function account_creation() {
        $mode = wpbdp_get_option( 'create-account-during-submit-mode' );
        $form_create   = 'create-account' === wpbdp_get_var( array( 'param' => 'create-account' ), 'post' );
        $form_username = trim( wpbdp_get_var( array( 'param' => 'user_username' ), 'post' ) );
        $form_email    = trim( wpbdp_get_var( array( 'param' => 'user_email', 'sanitize' => 'sanitize_email' ), 'post' ) );

        if ( $this->should_validate_section( 'account_creation' ) && ( $this->saving() && 'required' == $mode ) || $form_create ) {
            $error = false;

            if ( ! $form_username ) {
                $this->messages( _x( 'Please enter your desired username.', 'submit listing', 'business-directory-plugin' ), 'error', 'account_creation' );
                $error = true;
            }

            if ( ! $error && ! $form_email ) {
                $this->messages( _x( 'Please enter the e-mail for your new account.', 'submit listing', 'business-directory-plugin' ), 'error', 'account_creation' );
                $error = true;
            }

            if ( ! $error && $form_username && username_exists( $form_username ) ) {
                $this->messages( _x( 'The username you chose is already in use. Please use a different one.', 'submit listing', 'business-directory-plugin' ), 'error', 'account_creation' );
                $error = true;
            }

            if ( ! $error && $form_email && email_exists( $form_email ) ) {
                $this->messages( _x( 'The e-mail address you chose for your account is already in use.', 'submit listing', 'business-directory-plugin' ), 'error', 'account_creation' );
                $error = true;
            }

            if ( $error ) {
                $this->prevent_save = true;
            } else {
                $this->data['account_details'] = array( 'username' => $form_username, 'email' => $form_email );
            }
        }

        $html  = '';

        if ( 'optional' == $mode ) {
            $html .= '<input id="wpbdp-submit-listing-create_account" type="checkbox" name="create-account" value="create-account" ' . checked( true, $form_create, false ) . '/>';
            $html .= ' <label for="wpbdp-submit-listing-create_account">' . esc_html__( 'Create a user account on this site', 'business-directory-plugin' ) . '</label>';
        }

        $html .= '<div id="wpbdp-submit-listing-account-details" class="' . ( ( 'optional' == $mode && ! $form_create ) ? 'wpbdp-hidden' : '' ) . '">';

        $html .= '<div class="wpbdp-form-field wpbdp-form-field-type-textfield">';
        $html .= '<div class="wpbdp-form-field-label">';
        $html .= '<label for="wpbdp-submit-listing-user_username">' . esc_html__( 'Username', 'business-directory-plugin' ) . '</label>';
        $html .= '</div>';
        $html .= '<div class="wpbdp-form-field-inner">';
		$html .= '<input id="wpbdp-submit-listing-user_username" type="text" name="user_username" value="' . esc_attr( $form_username ) . '" />';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="wpbdp-form-field wpbdp-form-field-type-textfield">';
        $html .= '<div class="wpbdp-form-field-label">';
        $html .= '<label for="wpbdp-submit-listing-user_email">' . esc_html__( 'Email', 'business-directory-plugin' ) . '</label>';
        $html .= '</div>';
        $html .= '<div class="wpbdp-form-field-inner">';
        $html .= '<input id="wpbdp-submit-listing-user_email" type="text" name="user_email" value="' . esc_attr( $form_email ) . '" />';
        $html .= '</div>';
        $html .= '</div>';

		$html .= '</div>';

        return $html;
    }

	private function clear_form() {
		$html = '<a class="reset wpbdp-full" href="#">' . esc_html__( 'Clear Form', 'business-directory-plugin' ) . '</a>';
		return array( true, $html );
	}

    private function terms_and_conditions() {
        if ( ! wpbdp_get_option( 'display-terms-and-conditions' ) ) {
            return false;
        }

        $tos = trim( wpbdp_get_option( 'terms-and-conditions' ) );

        if ( empty( $tos ) ) {
            return false;
        }

        $html = '';

        $is_url = wpbdp_starts_with( $tos, 'http://', false ) || wpbdp_starts_with( $tos, 'https://', false );
        $accepted = ! empty( $_POST['terms-and-conditions-agreement'] ) && 1 == $_POST['terms-and-conditions-agreement'];

        if ( $this->should_validate_section( 'terms_and_conditions' ) && ! $accepted ) {
            $this->messages( _x( 'Please agree to the Terms and Conditions.', 'templates', 'business-directory-plugin' ), 'error', 'terms_and_conditions' );
            $this->prevent_save = true;
        }

        if ( $this->saving() && ! $this->prevent_save && $accepted ) {
            $this->data['tos_acceptance'] = date( 'Y-m-d H:i:s' );
        }

        if ( ! $is_url ) {
            $html .= sprintf( '<div id="wpbdp-terms-and-conditions" class="wpbdp-submit-listing-tos wpbdp-scroll-box">%s</div>', wp_kses_post( $tos ) );
        }

        $html .= '<label for="wpbdp-terms-and-conditions-agreement">';
        $html .= '<input id="wpbdp-terms-and-conditions-agreement" type="checkbox" name="terms-and-conditions-agreement" value="1" ' . ( $accepted ? 'checked="checked"' : '' ) . ' /> ';
        $label = _x( 'I agree to the <a>Terms and Conditions</a>', 'templates', 'business-directory-plugin' );

		if ( $is_url ) {
            $label = str_replace( '<a>', '<a href="' . esc_url( $tos ) . '" target="_blank" rel="noopener">', $label );
		} else {
            $label = str_replace( array( '<a>', '</a>' ), '', $label );
		}

        $html .= $label;
        $html .= '</label>';

        return array( true, $html );
    }

    public function render_rootline() {
        $params = array(
            'listing'  => $this->listing,
            'editing'  => $this->editing,
            'sections' => $this->sections,
            'submit'   => $this,
            'echo'     => true
        );

        wpbdp_render( 'submit-listing-rootline', $params );
    }

    public function load_css() {

    }

    private function find_prev_section( $section_id = null ) {
        if ( ! $section_id || empty( $this->sections_keys || ! in_array( $section_id, $this->sections_keys ) ) ) {
            return '';
        }

        $section_pos = array_search( $section_id, $this->sections_keys, true );

        if ( ! $section_pos ) {
            return '';
        }

		return $this->sections_keys[ $section_pos - 1 ];
    }

    private function find_next_section( $section_id = null ) {
        if ( ! $section_id || empty( $this->sections_keys || ! in_array( $section_id, $this->sections_keys ) ) ) {
            return '';
        }

        $sections_count = count( $this->sections_keys );
        $section_pos    = array_search( $section_id, $this->sections_keys, true );

        if ( false === $section_pos || $sections_count - 1 === $section_pos ) {
            return '';
        }

		return $this->sections_keys[ $section_pos + 1 ];
    }

    public function should_validate_section( $section_id ) {
		$this->get_parent_section( $section_id );
        $current_section_pos = array_search( $this->current_section, $this->sections_keys );
        $section_pos         = array_search( $section_id, $this->sections_keys );

        if ( false === $current_section_pos || false === $section_pos || $section_pos > $current_section_pos ) {
            return false;
        }

        return true;
    }

    private function save_listing() {
        if ( ! $this->editing ) {
            $this->listing->set_status( 'incomplete' );

            if ( ! empty( $this->data['account_details'] ) ) {
				// Prevent conflicts with other plugins checking the captcha.
				remove_action( 'registration_errors', 'gglcptch_register_check' );
				add_filter( 'wordfence_ls_require_captcha', '__return_false' );

                $user_id = register_new_user( $this->data['account_details']['username'], $this->data['account_details']['email'] );

                if ( is_wp_error( $user_id ) )
                    return $user_id;

                wp_update_post( array( 'ID' => $this->listing->get_id(), 'post_author' => $user_id ) );
            }

			$this->accept_terms();

            // XXX: what to do with this?
            // $extra = wpbdp_capture_action_array( 'wpbdp_listing_form_extra_sections', array( &$this->state ) );
            // return $this->render( 'extra-sections', array( 'output' => $extra ) );
            // do_action_ref_array( 'wpbdp_listing_form_extra_sections_save', array( &$this->state ) );
            $this->listing->set_status( 'pending_payment' );
            $payment = $this->listing->generate_or_retrieve_payment();

            if ( ! $payment )
                die();

            $payment->context = is_admin() ? 'admin-submit' : 'submit';
            $payment->save();
            if ( current_user_can( 'administrator' ) ) {
                $payment->process_as_admin();
                $this->listing->set_flag( 'admin-posted' );
            }
        }

        $listing_status = get_post_status( $this->listing->get_id() );
        $this->listing->set_post_status( $this->editing ? ( 'publish' !== $listing_status ? $listing_status : wpbdp_get_option( 'edit-post-status' ) ) : wpbdp_get_option( 'new-post-status' ) );
        $this->listing->_after_save( 'submit-' . ( $this->editing ? 'edit' : 'new' ) );
		if ( ! $this->editing && 'completed' != $payment->status ) {
			$checkout_url = $payment->get_checkout_url();
			return $this->_redirect(
				$checkout_url,
				array( 'doing_ajax' => $this->is_ajax )
			);
		}

        delete_post_meta( $this->listing->get_id(), '_wpbdp_temp_listingfields' );

        return $this->done();
    }

	/**
	 * Save saved terms to the listing.
	 */
	private function accept_terms() {
		if ( empty( $this->data['tos_acceptance'] ) ) {
			return;
		}

		update_post_meta( $this->listing->get_id(), '_wpbdp_tos_acceptance_date', $this->data['tos_acceptance'] );
		wpbdp_insert_log(
			array(
				'log_type'   => 'listing.terms_and_conditions_accepted',
				'object_id'  => $this->listing->get_id(),
				'created_at' => $this->data['tos_acceptance']
			)
		);
	}

    private function done() {
        $params = array(
            'listing' => $this->listing,
            'editing' => $this->editing,
            'payment' => $this->editing ? false : $this->listing->generate_or_retrieve_payment(),
        );

        return wpbdp_render( 'submit-listing-done', $params );
    }

    public static function preview_form( $listing ) {
        $view = new self();
        $view->listing = $listing;

        // $view->enqueue_resources();
        list( $success, $html ) = $view->listing_fields( true );

        return $html;
    }

	/**
	 * Show fields based on the selected category.
	 */
    public function category_specific_fields() {
        $form_fields = wpbdp_get_form_fields( array( 'association' => '-category' ) );
        $form_fields = apply_filters_ref_array( 'wpbdp_listing_submit_fields', array( &$form_fields, &$this->listing ) );

        foreach ( $form_fields as $field ) {
            $field_allowed_categories = $field->data( 'supported_categories', 'all' );
            if ( 'all' !== $field_allowed_categories ) {
                return true;
            }
        }

        return false;
    }

	/**
	 * Get the plan that has been selected, or the default if there is only one.
	 *
	 * @since 5.10
	 *
	 * @return null|object
	 */
	private function get_plan_for_listing() {
		$listing = $this->listing;
		return $listing->get_fee_plan();
	}

	/**
	 * Change plan if not the same as for listing.
	 * Update the plan in the payment.
	 *
	 * @param object $new_plan The new selected plan to assign to the listing.
	 *
	 * @since 5.17
	 */
	private function maybe_update_listing_plan( $new_plan ) {
		$current_plan = $this->get_plan_for_listing();
		if ( $current_plan && $current_plan->fee_id !== $new_plan->id ) {
			$this->listing->set_fee_plan_with_payment( $new_plan );
		}
	}

	/**
	 * If there's only one plan, get it.
	 *
	 * @since 5.10
	 *
	 * @return null|object
	 */
	private function single_plan() {
		$plans = $this->get_available_plans();
		if ( count( $plans ) === 1 ) {
			$this->fixed_plan_id = $plans[0]->id;
			return $plans[0];
		}

		return null;
	}

	/**
	 * @since 5.10
	 */
	private function set_available_plans() {
		$this->available_plans = array();
		foreach ( wpbdp_get_fee_plans() as $plan ) {
			$this->available_plans[] = $plan;
		}
	}

	public function get_available_plans() {
		if ( $this->available_plans === false ) {
			$this->set_available_plans();
		}
		return $this->available_plans;
	}

	/**
	 * If a plan is selected, return it. If not, return all.
	 *
	 * @since 5.10
	 */
	private function get_selected_or_available_plans() {
		$all_plans = $this->get_available_plans();

		$selected_plan = $this->get_plan_for_listing();
		if ( ! $selected_plan ) {
			return $all_plans;
		}

		$plans = array();
		foreach ( $all_plans as $plan ) {
			if ( $selected_plan->fee_id === $plan->id ) {
				$plans[] = $plan;
			}
		}

		return $plans;
	}
}
