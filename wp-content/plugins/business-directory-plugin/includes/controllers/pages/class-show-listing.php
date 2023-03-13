<?php

class WPBDP__Views__Show_Listing extends WPBDP__View {

    public function dispatch() {
        if ( ! wpbdp_user_can( 'view', null ) ) {
            $this->_http_404();
        }

        $html = '';
        if ( 'publish' != get_post_status( get_the_ID() ) && current_user_can( 'edit_posts' ) ) {
			$html .= wpbdp_render_msg( __( 'This is just a preview. The listing has not been published yet.', 'business-directory-plugin' ) );
        }

/*        // Handle ?v=viewname argument for alternative views (other than 'single').
        $view = '';
        if ( isset( $_GET['v'] ) )
            $view = wpbdp_capture_action_array( 'wpbdp_listing_view_' . trim( $_GET['v'] ), array( $listing_id ) );*/

		$html .= wpbdp_render_listing( null, 'single', false );

        return $html;
    }

}
