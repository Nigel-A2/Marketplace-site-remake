<?php
/**
 * @since 4.0
 */
class _WPBDP_Template_Sections {

	public function __construct() {
        add_action( 'wpbdp_template_variables', array( &$this, 'add_contact_form' ), 10, 2 );
        add_action( 'wpbdp_template_variables', array( &$this, 'add_comments' ), 999, 2 );
    }

	public function add_contact_form( $vars, $template ) {
        if ( 'single' != $template )
            return $vars;

        $vars['#contact_form'] = array( 'callback' => array( &$this, 'listing_contact_form' ),
                                        'position' => 'after',
                                        'weight' => 10 );
        return $vars;
    }

	public function listing_contact_form( $vars ) {
        if ( ! class_exists( 'WPBDP__Views__Listing_Contact' ) )
            require_once WPBDP_INC . 'controllers/pages/class-listing-contact.php';

        $v = new WPBDP__Views__Listing_Contact();
        return $v->render_form( $vars['listing_id'] );
    }

	public function add_comments( $vars, $template ) {
        if ( 'single' != $template )
            return $vars;

        $vars['#comments'] = array( 'callback' => array( $this, 'listing_comments' ),
                                    'position' => 'after',
                                    'weight' => 100 );
        return $vars;
    }

	public function listing_comments( $listing_id ) {
        if ( wpbdp_get_option( 'allow-comments-in-listings' ) != 'allow-comments-and-insert-template' ) {
            return;
        }

        $html = '<div class="comments">';

        ob_start();
        comments_template( '', true );

        $html .= ob_get_clean();

        $html .= '</div>';

        return $html;
    }
}

new _WPBDP_Template_Sections();
