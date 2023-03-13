<?php
/**
 * Flag Listings
 *
 * @package WPBDP/Includes/Views
 */

/**
 * @since 5.1.6
 */
class WPBDP__Views__Flag_Listing extends WPBDP__View {

    private $listing_id = 0;
    private $listing    = null;
    private $errors     = array();


    public function dispatch() {
        if ( ! wpbdp_get_option( 'enable-listing-flagging' ) ) {
            exit;
        }

        $this->listing_id = absint( wpbdp_get_var( array( 'param' => 'listing_id' ), 'request' ) );
        $this->listing    = wpbdp_get_listing( $this->listing_id );

        if ( ! $this->listing ) {
            exit;
        }

        if ( ! wpbdp_user_can( 'flagging', $this->listing->get_id() ) ) {
            $this->_auth_required(
                array(
                    'wpbdp_view' => 'flag_listing',
                    'redirect_query_args' => array(
                        'listing_id' => $this->listing_id,
                    ),
                )
            );
        }

        $nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'request' );
        $html  = '';

        if ( wp_verify_nonce( $nonce, 'flag listing report ' . $this->listing_id ) ) {
            // Try to add report.
            $report = $this->sanitize_report();

            if ( $report ) {
                $result = WPBDP__Listing_Flagging::add_flagging( $this->listing_id, $report );

                if ( is_wp_error( $result ) ) {
                    $this->errors[] = $result->get_error_message();
                } else {
					$flagging_msg  = sprintf(
						/* translators: %1$s: listing name, %2$s: open link html, %3$s close link html */
						esc_html__( 'The listing %1$s has been reported. %2$sReturn to directory%3$s', 'business-directory-plugin' ),
						'<strong>' . esc_html( $this->listing->get_title() ) . '</strong>',
						'</p><p><a href="' . esc_url( wpbdp_url( 'main' ) ) . '">',
						'</a>'
					);

					return '<p>' . wp_kses_post( $flagging_msg ) . '</p>';
                }
            }
        //} elseif ( wp_verify_nonce( $nonce, 'flag listing unreport ' . $this->listing_id ) ) {
            // Remove report.
            // $flagging_pos = WPBDP__Listing_Flagging::user_has_flagged( $listing_id, $current_user );
            // WPBDP__Listing_Flagging::remove_flagging( $listing_id, $flagging_pos );
            //
            // $flagging_msg = _x( 'The listing <i>%s</i> has been unreported. <a>Return to listing</a>', 'flag listing', 'business-directory-plugin' );
            // $flagging_msg = sprintf( $flagging_msg, $this->listing->get_title() );
            // $flagging_msg = str_replace( '<a>', '<a href="' . $this->listing->get_permalink() . '">', $flagging_msg );
            //
            // return wpbdp_render_msg( $flagging_msg );
        }

        foreach ( $this->errors as $err_msg ) {
            $html .= wpbdp_render_msg( $err_msg, 'error' );
        }

        $current_user = get_current_user_id();

        $html .= wpbdp_render(
            'listing-flagging-form',
            array(
                'listing'      => $this->listing,
                'recaptcha'    => wpbdp_get_option( 'recaptcha-for-flagging' ) ? wpbdp_recaptcha( 'wpbdp-listing-flagging-recaptcha' ) : '',
                'current_user' => $current_user ? get_userdata( $current_user ) : '',
            )
        );

        return $html;
    }

    public function sanitize_report() {
        $this->errors = array();
        $current_user = is_user_logged_in() ? wp_get_current_user() : null;

        $report             = array();
        $report['user_id']  = get_current_user_id();
        $report['ip']       = wpbdp_get_client_ip_address();
        $report['date']     = time();
        $report['reason']   = trim( wpbdp_get_var( array( 'param' => 'flagging_option' ), 'post' ) );
        $report['comments'] = trim( wpbdp_get_var( array( 'param' => 'flagging_more_info', 'sanitize' => 'sanitize_textarea_field' ), 'post' ) );
        $report['name']     = $current_user ? $current_user->data->user_login : trim( wpbdp_get_var( array( 'param' => 'reportauthorname' ), 'post' ) );
        $report['email']    = $current_user ? $current_user->data->user_email : trim( wpbdp_get_var( array( 'param' => 'reportauthoremail', 'sanitize' => 'sanitize_email' ), 'post' ) );

        if ( false !== WPBDP__Listing_Flagging::ip_has_flagged( $this->listing_id, $report['ip'] ) ) {
            $this->errors[] = esc_html__( 'Your current IP address already reported this listing.', 'business-directory-plugin' );
        }

        $error_msg = '';

        if ( wpbdp_get_option( 'recaptcha-for-flagging' ) && ! wpbdp_recaptcha_check_answer( $error_msg ) ) {
            $this->errors[] = $error_msg;
        }

        $flagging_options = WPBDP__Listing_Flagging::get_flagging_options();

        if ( ! empty( $flagging_options ) ) {
            if ( ! $report['reason'] ) {
                $this->errors[] = esc_html__( 'You must select the reason to report this listing as inappropriate.', 'business-directory-plugin' );
            }
        } else {
            if ( ! $report['comments'] ) {
                $this->errors[] = esc_html__( 'You must enter the reason to report this listing as inappropriate.', 'business-directory-plugin' );
            }
        }

        if ( ! $report['name'] ) {
            $this->errors[] = esc_html__( 'Please enter your name.', 'business-directory-plugin' );
        }

        if ( ! $report['email'] ) {
            $this->errors[] = esc_html__( 'Please enter your email.', 'business-directory-plugin' );
        }

        if ( $this->errors ) {
            return false;
        }

        return $report;
    }

}
