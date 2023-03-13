<?php
/**
 * Listing contact page.
 *
 * @since 3.4
 * @package WPBDP/Views/Listing contact
 */

/**
 * Class WPBDP__Views__Listing_Contact
 */
class WPBDP__Views__Listing_Contact extends WPBDP__View {

    private $errors = array();

    private $name    = '';
    private $email   = '';
    private $phone   = '';
    private $message = '';


    private function prepare_input() {
        $this->name    = trim( wpbdp_get_var( array( 'param' => 'commentauthorname' ), 'post' ) );
        $this->email   = trim( wpbdp_get_var( array( 'param' => 'commentauthoremail', 'sanitize' => 'sanitize_email' ), 'post' ) );
        $this->phone   = trim( wpbdp_get_var( array( 'param' => 'commentauthorphone' ), 'post' ) );
		$message       = wpbdp_get_var( array( 'param' => 'commentauthormessage', 'sanitize' => 'sanitize_textarea_field' ), 'post' );
		$this->message = trim( wp_kses( $message, array() ) );

        $current_user = is_user_logged_in() ? wp_get_current_user() : null;

        if ( $current_user && $current_user->exists() ) {
            $this->name  = $current_user->user_firstname ? $current_user->user_firstname . ( $current_user->user_lastname ? ' ' . $current_user->user_lastname : '' ) : $current_user->data->user_login;
            $this->email = $current_user->data->user_email;
        }
    }

    private function validate() {
        $this->errors = array();

        if ( ! isset( $_REQUEST['listing_id'] ) ) {
            die();
        }

        $listing_id = wpbdp_get_var( array( 'param' => 'listing_id' ), 'request' );
        $nonce      = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );

        // Verify nonce.
        if ( ! $nonce || ! isset( $_POST['_wp_http_referer'] ) || ! wp_verify_nonce( $nonce, 'contact-form-' . $listing_id ) ) {
            die();
        }

        if ( ! $this->name ) {
            $this->errors[] = _x( 'Please enter your name.', 'contact-message', 'business-directory-plugin' );
        }

        if ( ! $this->email || ! wpbdp_validate_value( $this->email, 'email' ) ) {
            $this->errors[] = _x( 'Please enter a valid email.', 'contact-message', 'business-directory-plugin' );
        }

        if ( ! wpbdp_validate_value( $this->phone, 'tel' ) ) {
            $this->errors[] = _x( 'Please enter a valid phone number.', 'contact-message', 'business-directory-plugin' );
        }

        if ( ! $this->message ) {
            $this->errors[] = _x( 'You did not enter a message.', 'contact-message', 'business-directory-plugin' );
        }

        $error_msg = '';

        if ( wpbdp_get_option( 'recaptcha-on' ) && ! wpbdp_recaptcha_check_answer( $error_msg ) ) {
            $this->errors[] = $error_msg;
        }

        $this->errors = apply_filters( 'wpbdp_contact_form_validation_errors', $this->errors );

        return empty( $this->errors );
    }

    private function can_submit( $listing_id = 0, &$error_msg = '' ) {
        if ( wpbdp_get_option( 'contact-form-require-login' ) && ! is_user_logged_in() ) {
            $error_msg = str_replace(
                '<a>',
                '<a href="' . esc_url( add_query_arg( 'redirect_to', urlencode( apply_filters( 'the_permalink', get_permalink() ) ), wpbdp_url( 'login' ) ) ) . '">',
                _x( 'Please <a>log in</a> to be able to send messages to the listing owner.', 'contact form', 'business-directory-plugin' )
            );
            return false;
        }

        if ( ! $this->listing_can_submit( $listing_id, $error_msg ) ) {
            return false;
        }

        if ( wpbdp_get_option( 'contact-form-require-login' ) && ! $this->user_can_submit( $listing_id, $error_msg ) ) {
            return false;
        }

        return true;
    }

    private function update_contacts( $listing_id ) {
        $today = date( 'Ymd', current_time( 'timestamp' ) );
        if ( max( 0, intval( wpbdp_get_option( 'contact-form-daily-limit' ) ) ) ) {
            $data  = get_post_meta( $listing_id, '_wpbdp_contact_limit', true );

            if ( ! $data || ! is_array( $data ) ) {
                $data = array(
                    'last_date' => $today,
                    'count'     => 0,
                );
            }

            if ( $today != $data['last_date'] ) {
                $data['count'] = 0;
            }

            $data['count']     = $data['count'] + 1;
            $data['last_date'] = $today;

            update_post_meta( $listing_id, '_wpbdp_contact_limit', $data );
        }

        if ( max( 0, intval( wpbdp_get_option( 'contact-form-registered-users-limit' ) ) ) ) {
            $user_id = get_current_user_id();
            $data    = get_user_meta( $user_id, '_wpbdp_contact_limit', true );

            if ( ! $data || ! is_array( $data ) ) {
                $data = array(
                    'last_date' => $today,
                    'count'     => 0,
                );
            }

            if ( $today != $data['last_date'] ) {
                $data['count'] = 0;
            }

            $data['count']     = $data['count'] + 1;
            $data['last_date'] = $today;

            update_user_meta( $user_id, '_wpbdp_contact_limit', $data );
        }
    }

    private function listing_can_submit( $listing_id = 0, &$error_msg = '' ) {
        $daily_limit = max( 0, intval( wpbdp_get_option( 'contact-form-registered-users-limit' ) ) );

        if ( ! $daily_limit ) {
            return true;
        }

        $data  = get_post_meta( $listing_id, '_wpbdp_contact_limit', true );

        return $this->validate_submit_status( $data, $daily_limit, $error_msg );
    }

    private function user_can_submit( $listing_id = 0, &$error_msg = '' ) {
        $daily_limit = max( 0, intval( wpbdp_get_option( 'contact-form-registered-users-limit' ) ) );

        if ( ! $daily_limit ) {
            return true;
        }

        $data = get_user_meta( get_current_user_id(), '_wpbdp_contact_limit', true );

        return $this->validate_submit_status( $data, $daily_limit, $error_msg );
    }

    private function validate_submit_status( $data, $daily_limit, &$error_msg ) {
        $today = date( 'Ymd', current_time( 'timestamp' ) );

        if ( ! $data || ! is_array( $data ) ) {
            $data = array(
				'last_date' => $today,
				'count'     => 0,
			);
        }

        if ( $today != $data['last_date'] ) {
            $data['count'] = 0;
        }

        if ( $data['count'] >= $daily_limit ) {
            $error_msg = _x( 'This contact form is temporarily disabled. Please try again later.', 'contact form', 'business-directory-plugin' );
            return false;
        }

        return true;
    }

    public function render_form( $listing_id = 0, $validation_errors = array() ) {
        $listing_id = absint( $listing_id );

        if ( ! $listing_id || ! apply_filters( 'wpbdp_show_contact_form', wpbdp_get_option( 'show-contact-form' ), $listing_id ) ) {
            return '';
        }

        $html = '';

        $html .= '<div class="wpbdp-listing-contact-form">';

        if ( ! $_POST ) {
            $html .= '<div><a href="#wpbdp-contact-me" id="wpbdp-contact-me" class="wpbdp-show-on-mobile send-message-button wpbdp-button button" rel="nofollow">' . _x( 'Contact listing owner', 'templates', 'business-directory-plugin' ) . '</a></div>';
            $html .= '<div class="wpbdp-hide-on-mobile contact-form-wrapper">';
        }

		$html .= '<h3>' . esc_html_x( 'Send Message to listing owner', 'templates', 'business-directory-plugin' ) . '</h3>';

        $form = '';

        if ( ! $this->can_submit( $listing_id, $error_msg ) ) {
            $form = wpbdp_render_msg( $error_msg );
        } else {
            $form = wpbdp_render(
                'listing-contactform', array(
					'validation_errors' => $validation_errors,
					'listing_id'        => $listing_id,
					'current_user'      => is_user_logged_in() ? wp_get_current_user() : null,
					'recaptcha'         => wpbdp_get_option( 'recaptcha-on' ) ? wpbdp_recaptcha( 'wpbdp-contact-form-recaptcha' ) : '',
					false,
                )
            );

			$form = apply_filters( 'wpbdp_contact_form_output', $form, compact( 'listing_id' ) );
        }

        $html .= $form;

        if ( ! $_POST ) {
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function dispatch() {
        $listing_id = intval( isset( $_REQUEST['listing_id'] ) ? $_REQUEST['listing_id'] : 0 );

        if ( ! $listing_id ) {
            return '';
        }

        if ( ! $this->can_submit( $listing_id, $error_msg ) ) {
            return wpbdp_render_msg( $error_msg, 'error' );
        }

        $this->listing_id = $listing_id;
        $this->prepare_input();

        if ( ! $this->validate() ) {
            return $this->render_form( $listing_id, $this->errors );
        }

        // Compose e-mail message.
        $replacements    = array(
            'listing-url' => get_permalink( $listing_id ),
            'listing'     => get_the_title( $listing_id ),
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'message'     => $this->message,
            'date'        => date_i18n( __( 'l F j, Y \a\t g:i a', 'business-directory-plugin' ), current_time( 'timestamp' ) ),
            'access_key'  => wpbdp_get_listing( $listing_id )->get_access_key(),
        );
        $email           = wpbdp_email_from_template( 'email-templates-contact', $replacements );
        $email->body     = apply_filters( 'wpbdp_contact_form_email_body', $email->body );
        $email->to       = wpbusdirman_get_the_business_email( $listing_id );
        $email->reply_to = "{$this->name} <{$this->email}>";
        $email->template = 'businessdirectory-email';

        $html = '';

        if ( $email->send() ) {
            $html .= wpbdp_render_msg( _x( 'Your message has been sent.', 'contact-message', 'business-directory-plugin' ) );
            $this->update_contacts( $listing_id );

            // Notify admin.
            if ( in_array( 'listing-contact', wpbdp_get_option( 'admin-notifications' ), true ) ) {
                // $replacements[ 'listing-url' ] = sprintf( _x( '%s (admin: %s)', 'contact-message', 'business-directory-plugin' ),
                // $replacements['listing-url'],
                // get_edit_post_link( $listing_id ) );
                // $admin_email = wpbdp_email_from_template( 'email-templates-contact', $replacements );
                $admin_email          = new WPBDP_Email();
                $admin_email->subject = $email->subject;
                $admin_email->body    = $email->body;
                $admin_email->to      = get_bloginfo( 'admin_email' );

                if ( wpbdp_get_option( 'admin-notifications-cc' ) ) {
                    $admin_email->cc = wpbdp_get_option( 'admin-notifications-cc' );
                }

                $admin_email->template = 'businessdirectory-email';
                $admin_email->send();
            }
        } else {
            $html .= wpbdp_render_msg( _x( 'There was a problem encountered. Your message has not been sent', 'contact-message', 'business-directory-plugin' ), 'error' );
        }

        $html .= sprintf( '<p><a href="%s">%s</a></p>', get_permalink( $listing_id ), _x( 'Return to listing.', 'contact-message', 'business-directory-plugin' ) );
        return $html;
    }

}
