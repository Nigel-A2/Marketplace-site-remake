<?php
/**
 * @package WPBDP
 */

/**
 * @since 5.0
 */
class WPBDP__Listing_Email_Notification {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'transition_post_status', array( $this, 'listing_published_notification' ), 10, 3 );
        add_action( 'wpbdp_listing_status_change', array( $this, 'status_change_notifications' ), 10, 3 );
        add_action( 'wpbdp_edit_listing', array( $this, 'edit_listing_admin_email' ) );

        add_action( 'wpbdp_listing_renewed', array( $this, 'listing_renewal_email' ), 10, 3 );

        add_action( 'wpbdp_listing_maybe_send_notices', array( $this, 'send_notices' ), 10, 3 );

        add_action( 'wpbdp_listing_maybe_flagging_notice', array( $this, 'reported_listing_email' ), 10, 2 );

        if ( ! is_admin() ) {
            add_action( 'WPBDP_Payment::status_change', array( $this, 'payment_completed_email' ), 10, 3 );
        }
    }

    /**
     * Sent when a listing is published either by the admin or automatically.
     *
     * @param string $new_status    The new listing status.
     * @param string $old_status    The previous listing status.
     * @param object $post          An instance of WP_Post.
     */
    public function listing_published_notification( $new_status, $old_status, $post ) {
        if ( ! in_array( 'listing-published', wpbdp_get_option( 'user-notifications' ), true ) ) {
            return;
        }

        if ( WPBDP_POST_TYPE !== get_post_type( $post ) ) {
            return;
        }

        if ( $new_status === $old_status || 'publish' !== $new_status || ! in_array( $old_status, array( 'pending', 'draft', 'auto-draft' ), true ) ) {
            return;
        }

        global $wpbdp;

        if ( isset( $wpbdp->_importing_csv_no_email ) && $wpbdp->_importing_csv_no_email ) {
            return;
        }

        if ( isset( $_POST['original_post_status'] ) && 'auto-draft' === $_POST['original_post_status'] ) {
            add_action( 'save_post', array( $this, 'send_listing_published_notification' ), PHP_INT_MAX, 2 );
            add_action( 'save_post', array( $this, 'try_to_remove_listing_published_notification_action' ), PHP_INT_MAX );
            return;
        }

        $this->send_listing_published_notification( $post->ID, $post );
    }

    /**
     * @param int    $post_id    The ID of the published post.
     * @param object $post       An instance of WP_Post.
     */
    public function send_listing_published_notification( $post_id, $post ) {
        if ( ! isset( $post->post_type ) || WPBDP_POST_TYPE !== $post->post_type ) {
            return;
        }

        if ( 'publish' !== get_post_status( $post_id ) ) {
            return;
        }

        $email = wpbdp_email_from_template(
            'email-templates-listing-published',
            array(
                'listing'     => get_the_title( $post_id ),
                'listing-url' => get_permalink( $post_id ),
                'access_key'  => wpbdp_get_listing( $post_id )->get_access_key(),
            )
        );

        $email->to[]     = wpbusdirman_get_the_business_email( $post_id );
        $email->template = 'businessdirectory-email';

        $email->send();
    }

    /**
     * Remove action handlers used to send listing published notification.
     */
    public function try_to_remove_listing_published_notification_action() {
		remove_action( 'save_post', array( $this, 'send_listing_published_notification' ), PHP_INT_MAX );
        remove_action( 'save_post', array( $this, 'try_to_remove_listing_published_notification_action' ), PHP_INT_MAX );
    }

    /**
     * Used to handle notifications related to listing status changes (i.e. expired, etc.)
     *
     * @param object $listing       An instance of WPBDP_Listing.
     * @param string $old_status    The previous listing status.
     * @param string $new_status    The new listing status.
     */
    public function status_change_notifications( $listing, $old_status, $new_status ) {
        // Expiration notice.
        if ( 'expired' === $new_status && $this->should_send_expiration_notifications() ) {
            $this->send_notices( 'expiration', '0 days', $listing );
        }

        // When a listing is submitted.
        if ( 'incomplete' === $old_status && in_array( $new_status, array( 'complete', 'pending_payment' ) ) ) {
            $this->send_new_listing_email( $listing );
        }
    }

    /**
     * @since 5.1.10
     */
    private function should_send_expiration_notifications() {
        if ( ! wpbdp_get_option( 'listing-renewal' ) ) {
            return false;
        }

        $user_notifications = wpbdp_get_option( 'user-notifications' );

        if ( ! in_array( 'listing-expires', (array) $user_notifications, true ) ) {
            return false;
        }

        return true;
    }

	/**
	 * @param string     $event         Event identifier.
	 * @param string     $relative_time Number of days before or after the event occurred.
	 * @param object|int $listing       An instance of WPBDP_Listing.
	 * @param bool       $force_resend  Whether to resend already sent notifications or not.
	 */
    public function send_notices( $event, $relative_time, $listing, $force_resend = false ) {
        $sent    = false;
        $listing = is_object( $listing ) ? $listing : wpbdp_get_listing( absint( $listing ) );

        if ( ! $listing ) {
            return $sent;
        }

        $post_status = get_post_status( $listing->get_id() );

		if ( ! $post_status || in_array( $post_status, array( 'trash', 'auto-draft', 'draft', 'pending' ), true ) ) {
			// Don't send for pending, but allow renewal emails after auto switched to draft.
			$is_renewal = $post_status === 'draft' && $event === 'expiration';
			if ( ! $is_renewal ) {
				return $sent;
			}
		}

        $all_notices = wpbdp_get_option( 'expiration-notices' );

        foreach ( $all_notices as $notice_key => $notice ) {
            if ( $notice['event'] !== $event || $notice['relative_time'] !== $relative_time ) {
                continue;
            }

            if ( ( 'non-recurring' === $notice['listings'] && $listing->is_recurring() ) || ( 'recurring' === $notice['listings'] && ! $listing->is_recurring() ) ) {
                continue;
            }

            $already_sent = (int) get_post_meta( $listing->get_id(), '_wpbdp_notice_sent_' . $notice_key, true );

            if ( $already_sent && ! $force_resend ) {
                continue;
            }

            $payments = $listing->get_latest_payments();
            $payment  = $payments ? array_shift( $payments ) : array();

            $expiration_date = date_i18n( get_option( 'date_format' ), strtotime( $listing->get_expiration_date() ) );
            $payment_date    = date_i18n( get_option( 'date_format' ), $payment ? strtotime( implode( '/', $payment->get_created_at_date() ) ) : time() );

            $email = wpbdp_email_from_template(
                $notice,
                array(
                    'site'         => sprintf( '<a href="%s">%s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
                    'author'       => $listing->get_author_meta( 'display_name' ),
                    'listing'      => sprintf( '<a href="%s">%s</a>', $listing->get_permalink(), esc_attr( $listing->get_title() ) ),
                    'expiration'   => $expiration_date,
                    'link'         => sprintf( '<a href="%1$s">%1$s</a>', $listing->get_renewal_url() ),
                    'link-raw'     => $listing->get_renewal_url(),
                    'category'     => get_the_term_list( $listing->get_id(), WPBDP_CATEGORY_TAX, '', ', ' ),
                    'date'         => $expiration_date,
                    'payment_date' => $payment_date,
                    'access_key'   => $listing->get_access_key(),
                )
            );

            $email->template = 'businessdirectory-email';
            $email->to[]     = wpbusdirman_get_the_business_email( $listing->get_id() );

            if ( 'expiration' === $event && in_array( 'renewal', wpbdp_get_option( 'admin-notifications' ), true ) ) {
                $email->cc = get_option( 'admin_email' );

                if ( wpbdp_get_option( 'admin-notifications-cc' ) ) {
                    $email->cc = wpbdp_get_option( 'admin-notifications-cc' );
                }
            }

            if ( $email->send() ) {
                // TODO: Why was the line below commented out?
                // See: https://github.com/drodenbaugh/BusinessDirectoryPlugin/commit/0420174dd3f93089e8088b942f3ca08d82c13d62
                // update_post_meta( $listing->get_id(), '_wpbdp_notice_sent_' . $notice_key, current_time( 'timestamp' ) );
                $sent = true;
            }
        }

        return $sent;
    }

    /**
     * @param object $listing   An instance of WPBDP_Listing.
     */
    private function send_new_listing_email( $listing ) {
        global $wpbdp;

        if ( isset( $wpbdp->_importing_csv_no_email ) && $wpbdp->_importing_csv_no_email ) {
            return;
        }

        // Notify the admin.
        if ( in_array( 'new-listing', wpbdp_get_option( 'admin-notifications' ), true ) ) {
            $admin_email = new WPBDP_Email();

            // translators: [%s] is the name of the blog.
            $admin_email->subject = sprintf( _x( '[%s] New listing notification', 'notify email', 'business-directory-plugin' ), get_bloginfo( 'name' ) );
            $admin_email->to[]    = get_bloginfo( 'admin_email' );

            if ( wpbdp_get_option( 'admin-notifications-cc' ) ) {
                $admin_email->cc = wpbdp_get_option( 'admin-notifications-cc' );
            }

            $admin_email->body = wpbdp_render( 'email/listing-added', array( 'listing' => $listing ), false );
            $admin_email->send();
        }

        // Notify the submitter.
        if ( in_array( 'new-listing', wpbdp_get_option( 'user-notifications' ), true ) ) {
            if ( 'publish' === wpbdp_get_option( 'new-post-status' ) ) {
                // add_action( 'save_post', array( $this, 'send_listing_published_notification' ), PHP_INT_MAX, 2 );
                add_action( 'save_post', array( $this, 'try_to_remove_listing_published_notification_action' ), PHP_INT_MAX );
                return;
            }

            $plan = $listing->get_fee_plan();

            $plan_features = '';

            foreach ( $plan->fee->get_feature_list() as $feature ) {
                $plan_features .= '<li>' . $feature . '</li>';
            }

            if ( $plan_features ) {
                $plan_features = sprintf( '<ul class="wpbdp-plan-feature-list">%s</ul>', $plan_features );
            }

            $email           = wpbdp_email_from_template(
                'email-confirmation-message',
                array(
                    'listing'         => $listing->get_title(),
                    'fee_name'        => $plan->fee_label,
                    'fee_description' => $plan->fee->description,
                    'fee_details'     => $plan_features,
                )
            );
            $email->to[]     = wpbusdirman_get_the_business_email( $listing->get_id() );
            $email->template = 'businessdirectory-email';

            $email->send();
        }
    }

    /**
     * Sent when a listing is edited.
     *
     * @param int $listing_id   The ID of the listing that was edited.
     */
    public function edit_listing_admin_email( $listing_id ) {
        global $wpbdp;

        if ( isset( $wpbdp->_importing_csv_no_email ) && $wpbdp->_importing_csv_no_email ) {
            return;
        }

        if ( ! in_array( 'listing-edit', wpbdp_get_option( 'admin-notifications' ), true ) ) {
            return;
        }

        $listing = wpbdp_get_listing( $listing_id );

        $email = new WPBDP_Email();

        // translators: [%s] is the name of the blog.
        $email->subject = sprintf( _x( '[%s] Listing edit notification', 'notify email', 'business-directory-plugin' ), get_bloginfo( 'name' ) );
        $email->to[]    = get_bloginfo( 'admin_email' );

        if ( wpbdp_get_option( 'admin-notifications-cc' ) ) {
            $email->cc = wpbdp_get_option( 'admin-notifications-cc' );
        }

        $email->body = wpbdp_render( 'email/listing-edited', array( 'listing' => $listing ), false );

        $email->send();
    }

    /**
     * Sent when a listing is renewed.
     *
     * @param object $listing   An instance of WPBDP_Listing.
     * @param object $payment   A payment object.
     * @param string $context   This parameter is not used.
     * @since 5.0.6
     */
    public function listing_renewal_email( $listing, $payment = null, $context = '' ) {
        // Notify admin.
        if ( in_array( 'after_renewal', wpbdp_get_option( 'admin-notifications' ), true ) ) {
            $email = new WPBDP_Email();

            $email->to[] = get_bloginfo( 'admin_email' );
            // translators: [%s] is the name of the blog, "%s" is the title of the listing.
            $email->subject = sprintf( '[%s] Listing "%s" has renewed', get_bloginfo( 'name' ), $listing->get_title() );

            $cc = wpbdp_get_option( 'admin-notifications-cc' );

            if ( $cc ) {
                $email->cc = $cc;
            }

            $owner = wpbusdirman_get_the_business_email( $listing->get_id() );
			if ( $payment ) {
                $amount = $payment->amount;
            } else {
                $plan   = $listing->get_fee_plan();
                $amount = $plan->fee_price;
            }

            $amount = wpbdp_currency_format( $amount );

            $email->body = sprintf(
                'The listing "%s" has just renewed for %s from %s.',
                '<a href="' . $listing->get_admin_edit_link() . '">' . $listing->get_title() . '</a>',
                $amount,
                $owner
            );

            $email->send();
        }

        // Notify users.
        if ( in_array( 'listing-expires', wpbdp_get_option( 'user-notifications' ), true ) ) {
            do_action( 'wpbdp_listing_maybe_send_notices', 'renewal', '0 days', $listing );
        }
    }

    /**
     * @param object $listing   An instance of WPBDP_Listing.
     * @param array  $report    An array with information about the report.
     */
    public function reported_listing_email( $listing, $report ) {
        // Notify the admin.
        if ( in_array( 'flagging_listing', wpbdp_get_option( 'admin-notifications' ), true ) ) {
            $admin_email = new WPBDP_Email();

            // translators: %s is the name of the blog.
            $admin_email->subject = sprintf( _x( '[%s] Reported listing notification', 'notify email', 'business-directory-plugin' ), get_bloginfo( 'name' ) );
            $admin_email->to[]    = get_bloginfo( 'admin_email' );

            if ( wpbdp_get_option( 'admin-notifications-cc' ) ) {
                $admin_email->cc = wpbdp_get_option( 'admin-notifications-cc' );
            }

            if ( empty( $report['email'] ) && 0 !== $report['user_id'] ) {
                $user            = get_userdata( $report['user_id'] );
                $report['email'] = $user->user_email;
                $report['name']  = $user->user_login;
            }

            $admin_email->body = wpbdp_render(
                'email/listing-reported',
                array(
                    'listing' => $listing,
                    'report'  => $report,
                ),
                false
            );
            $admin_email->send();
        }
    }

    public function payment_completed_email( $payment, $old_status, $new_status ) {
        $listing = wpbdp_get_listing( $payment->listing_id );

		if ( ! $listing || $old_status === $new_status ) {
            return;
        }

        if ( 1 === count( $payment->payment_items ) && 0.0 === $payment->amount ) {
            return;
        }

        $plan = $listing->get_fee_plan();

		if ( ! $plan ) {
            return;
        }

        // Notify the admin.
        if ( in_array( 'payment-completed', wpbdp_get_option( 'admin-notifications' ), true ) ) {
            $admin_email = new WPBDP_Email();

            // translators: [%s] is the name of the blog.
            $admin_email->subject = sprintf( _x( '[%s] New payment notification', 'notify email', 'business-directory-plugin' ), get_bloginfo( 'name' ) );
            $admin_email->to[]    = get_bloginfo( 'admin_email' );

            if ( wpbdp_get_option( 'admin-notifications-cc' ) ) {
                $admin_email->cc = wpbdp_get_option( 'admin-notifications-cc' );
            }

            $admin_email->body = wpbdp_render(
                'email/listing-payment-completed',
                array(
                    'payment'         => $payment,
                    'listing'         => $listing,
                    'plan'            => $plan,
					'payment_datails' => preg_replace( "/\r|\n/", '', wpbdp()->payments->render_invoice( $payment ) ),
				),
				false
            );
            $admin_email->send();
        }

        // Notify the submitter.
        if ( in_array( 'payment-completed', wpbdp_get_option( 'user-notifications' ), true ) ) {
            $plan_features = '';

            foreach ( $plan->fee->get_feature_list() as $feature ) {
                $plan_features .= '<li>' . $feature . '</li>';
            }

            if ( $plan_features ) {
                $plan_features = sprintf( '<ul class="wpbdp-plan-feature-list">%s</ul>', $plan_features );
            }

            $email           = wpbdp_email_from_template(
                'email-templates-payment-completed',
                array(
                    'listing'         => $listing->get_title(),
                    'fee_name'        => $plan->fee_label,
                    'fee_description' => $plan->fee->description,
                    'fee_details'     => $plan_features,
					'payment_details' => preg_replace( "/\r|\n/", '', wpbdp()->payments->render_invoice( $payment ) ),
                    'receipt_url'     => $payment->get_checkout_url(),
                    'gateway'         => $payment->gateway,
                )
            );

			if ( $payment->payer_email ) {
				$email->to[] = $payment->payer_email;
			} else {
				$email->to[] = wpbusdirman_get_the_business_email( $listing->get_id() );
			}

            $email->template = 'businessdirectory-email';

            $email->send();
        }
    }
}
