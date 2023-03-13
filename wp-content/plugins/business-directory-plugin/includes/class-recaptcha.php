<?php
/**
 * @package WPBDP/includes/Recaptcha
 */

/**
 * @since 3.6.8
 */
class WPBDP_reCAPTCHA {

    private $public_key  = '';
    private $private_key = '';

    private $current_id = 1;
    private $threshold;
    private $version = '';

    private $error_message = '';
    private $comment_error = null;


    function __construct() {
		$this->public_key  = wpbdp_get_option( 'recaptcha-public-key' );
		$this->private_key = wpbdp_get_option( 'recaptcha-private-key' );

        if ( empty( $this->public_key ) || empty( $this->private_key ) ) {
            return;
        }

        $this->version       = wpbdp_get_option( 'recaptcha-version', 'v2' );
        $this->threshold     = wpbdp_get_option( 'recaptcha-threshold', 0.5 );
        $this->error_message = 'v2' === $this->version ? _x( 'The reCAPTCHA wasn\'t entered correctly.', 'recaptcha', 'business-directory-plugin' ) : _x( 'The reCAPTCHA validation score failed.', 'recaptcha', 'business-directory-plugin' );

        add_action( 'wp_enqueue_scripts', array( &$this, '_enqueue_js_api' ) );

        if ( wpbdp_get_option( 'recaptcha-for-comments' ) ) {
            add_filter( 'comment_form_field_comment', array( &$this, '_recaptcha_in_comments' ) );
            add_filter( 'preprocess_comment', array( &$this, '_check_comment_recaptcha' ), 0 );
            add_action( 'comment_post_redirect', array( &$this, '_comment_relative_redirect' ), 0, 2 );
        }

        if ( wpbdp_get_option( 'recaptcha-for-submits' ) || wpbdp_get_option( 'recaptcha-for-edits' ) ) {
            add_filter( 'wpbdp_submit_prepare_sections', array( $this, 'maybe_hide_recaptcha_section'), 10, 2 );
            add_filter( 'wpbdp_submit_sections', array( $this, 'add_recaptcha_to_submit' ), 20, 2 );
            add_filter( 'wpbdp_submit_section_recaptcha', array( $this, 'submit_recaptcha_html' ), 10, 2 );
        }
    }

    function _enqueue_js_api() {
        global $wpbdp;

        if ( ! $wpbdp->is_plugin_page() || ! $this->should_enqueue_scripts() ) {
            return;
        }

        wp_enqueue_script(
            'wpbdp-recaptcha',
            WPBDP_URL . 'assets/js/recaptcha.min.js',
            array(),
            WPBDP_VERSION,
            true
        );

        $url = add_query_arg(
            array(
				'onload' => 'wpbdp_recaptcha_callback',
				'render' => 'v2' === $this->version ? 'explicit' : $this->public_key,
            ),
            'https://www.google.com/recaptcha/api.js'
        );

        wp_enqueue_script(
            'google-recaptcha',
            $url,
            array(),
            'v2' === $this->version ? '2.0' : '3.0',
            true
        );
    }

    function render( $name = '' ) {
        if ( empty( $this->public_key ) || empty( $this->private_key ) ) {
            return '';
        }

        $hide_recaptcha = wpbdp_get_option( 'hide-recaptcha-loggedin' );
        if ( is_user_logged_in() && $hide_recaptcha ) {
            return '';
        }

        $html = '';

        if ( $name ) {
            $html .= '<div id="' . $name . '">';
        }

        $html .= sprintf(
            '<div id="wpbdp_recaptcha_%d" class="wpbdp-recaptcha" data-key="%s" data-version="%s">',
            $this->current_id,
            $this->public_key,
            $this->version
        );

        if ( 'v3' === $this->version ) {
            $html .= '<input type="hidden" name="g-recaptcha-response" value="" />';
        }

        $html .= '</div>';

        if ( $name ) {
            $html .= '</div>';
        }

        $this->current_id++;

        return $html;
    }

    public function verify( &$error_msg = null ) {
        global $wpbdp;

        if ( empty( $this->public_key ) || empty( $this->private_key ) ) {
            return true;
        }

        $hide_recaptcha = wpbdp_get_option( 'hide-recaptcha-loggedin' );
        if ( is_user_logged_in() && $hide_recaptcha ) {
            return true;
        }

        $error_msg = $this->error_message;

        if ( empty( $_REQUEST['g-recaptcha-response'] ) ) {
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $res = wp_remote_post(
            $url,
            array(
				'body' => array(
					'secret'   => $this->private_key,
					'response' => wpbdp_get_var( array( 'param' => 'g-recaptcha-response' ), 'request' ),
					'remoteip' => wpbdp_get_server_value( 'REMOTE_ADDR' ),
				),
            )
        );

        if ( is_wp_error( $res ) ) {
            return false;
        }

        $js = json_decode( $res['body'] );

        if ( $js && isset( $js->success ) && $js->success ) {
            if ( 'v2' === $this->version ) {
                return true;
            }

            if ( isset( $js->score ) && $this->threshold < $js->score ) {
                return true;
            }
        }

        return false;
    }

    function _recaptcha_in_comments( $field ) {
        global $wpbdp;

        if ( ! wpbdp_current_view() ) {
            return $field;
        }

        $html  = '';
        $html .= $field;

        if ( ! empty( $_GET['wre'] ) ) {
            $html .= '<p class="wpbdp-recaptcha-error">';
            $html .= $this->error_message;
            $html .= '</p>';

            add_action( 'wp_footer', array( &$this, '_restore_comment_fields' ) );
        }

        $html .= $this->render();
        return $html;
    }

    function _check_comment_recaptcha( $comment_data ) {
        $post_id = isset( $comment_data['comment_post_ID'] ) ? $comment_data['comment_post_ID'] : 0;

        if ( WPBDP_POST_TYPE != get_post_type( $post_id ) ) {
            return $comment_data;
        }

        if ( ! $this->verify() ) {
            $this->comment_error = true;
			add_filter(
				'pre_comment_approved',
				function( $a ) {
					return 'spam';
				}
			);
        }

        return $comment_data;
    }

    function _comment_relative_redirect( $location, $comment ) {
        if ( is_null( $this->comment_error ) ) {
            return $location;
        }

        $location  = substr( $location, 0, strpos( $location, '#' ) );
        $location  = add_query_arg( 'wre', urlencode( base64_encode( $comment->comment_ID ) ), $location );
        $location .= '#commentform';

        return $location;
    }

    function _restore_comment_fields() {
		$wre        = wpbdp_get_var( array( 'param' => 'wre' ) );
		$comment_id = $wre ? absint( base64_decode( urldecode( $wre ) ) ) : 0;

        if ( ! $comment_id ) {
            return;
        }

        $comment = get_comment( $comment_id );
        if ( ! $comment ) {
            return;
        }

        echo <<<JS
        <script>//<![CDATA[
            jQuery( '#comment' ).val( "{$comment->comment_content}" );
        //}}>
        </script>
JS;
    }

    /**
     * @since 5.1.1
     */
    public function add_recaptcha_to_submit( $submit_sections, $submit ) {
		$current = wpbdp_current_view();
        if ( 'submit_listing' === $current && ! wpbdp_get_option( 'recaptcha-for-submits' ) ) {
            return $submit_sections;
        }

        if ( 'edit_listing' === $current && ! wpbdp_get_option( 'recaptcha-for-edits' ) ) {
            return $submit_sections;
        }

        $submit_sections['recaptcha'] = array( 'title' => '' );

        return $submit_sections;
    }

    /**
     * @since 5.1.1
     */
    public function submit_recaptcha_html( $section, $submit ) {
        if ( $submit->saving() && $submit->should_validate_section( 'recaptcha' ) ) {
            if ( ! $this->verify( $error_msg ) ) {
                $submit->messages( $error_msg, 'error', 'v2' === $this->version ? 'recaptcha' : 'general' );
                $submit->prevent_save();
            }
        }

        if ( $recaptcha = $this->render() ) {
            $section['html']  = $recaptcha;
            $section['state'] = 'enabled';
        } else {
            $section['flags'][] = 'hidden';
        }

        return $section;
    }

    public function maybe_hide_recaptcha_section( $submit_sections, $submit ) {
        if ( 'v3' === $this->version && in_array( 'recaptcha', array_keys( $submit_sections ) ) ) {
            $submit_sections['recaptcha']['flags'][] = 'hidden';
        }

        return $submit_sections;
    }

    private function should_enqueue_scripts() {
        if ( is_user_logged_in() && wpbdp_get_option( 'hide-recaptcha-loggedin' ) ) {
            return false;
        }

		if ( ! ( wpbdp_get_option( 'recaptcha-on' ) || wpbdp_get_option( 'recaptcha-for-submits' ) || wpbdp_get_option( 'recaptcha-for-edits' ) || wpbdp_get_option( 'recaptcha-for-flagging' ) || wpbdp_get_option( 'recaptcha-for-comments' ) ) ) {
            return false;
        }

        return true;

    }

}


/**
 * Displays a reCAPTCHA field using the configured settings.
 *
 * @return string HTML for the reCAPTCHA field.
 * @since 3.4.2
 */
function wpbdp_recaptcha( $name = '' ) {
    return wpbdp()->recaptcha->render( $name );
}

/**
 * Validates reCAPTCHA input.
 *
 * @return boolean TRUE if validation succeeded, FALSE otherwise.
 * @since 3.4.2
 */
function wpbdp_recaptcha_check_answer( &$error_msg = null ) {
    return wpbdp()->recaptcha->verify( $error_msg );
}

