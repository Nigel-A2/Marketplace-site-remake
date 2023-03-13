<?php
class WPBDP__Views__Main extends WPBDP__View {

    private function warnings() {
        $html = '';

		$term_args = array(
			'taxonomy'   => WPBDP_CATEGORY_TAX,
			'hide_empty' => 0,
		);
		$cat_count = wp_count_terms( $term_args );
		if ( (int) $cat_count === 0 ) {
            if ( is_user_logged_in() && current_user_can( 'install_plugins' ) ) {
                $html .= wpbdp_render_msg( _x( 'There are no categories assigned to the business directory yet. You need to assign some categories to the business directory. Only admins can see this message. Regular users are seeing a message that there are currently no listings in the directory. Listings cannot be added until you assign categories to the business directory.', 'templates', 'business-directory-plugin' ), 'error' );
            } else {
                $html .= '<p>' . _x( 'There are currently no listings in the directory.', 'templates', 'business-directory-plugin' ) . '</p>';
            }
        }

        if ( current_user_can( 'administrator' ) && wpbdp_get_option( 'hide-empty-categories' ) ) {
			$has_cats  = (float) $cat_count > 0;
			$empty_cat = (float) wp_count_terms( WPBDP_CATEGORY_TAX, 'hide_empty=1' ) == 0;

			if ( ! $has_cats || ! $empty_cat ) {
				return;
			}

			$msg = _x( 'You have "Hide Empty Categories" on and some categories that don\'t have listings in them. That means they won\'t show up on the front end of your site. If you didn\'t want that, click <a>here</a> to change the setting.', 'templates', 'business-directory-plugin' );
			$msg = str_replace(
				'<a>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_settings&tab=listings#hide-empty-categories' ) ) . '">',
				$msg
			);
            $html .= wpbdp_render_msg( $msg );
        }
    }

    public function dispatch() {
        global $wpbdp;

        $html = '';

        // Warnings and messages for admins.
        $html .= $this->warnings();

        // Listings under categories?
        if ( wpbdp_get_option( 'show-listings-under-categories' ) ) {
            require_once WPBDP_INC . 'controllers/pages/class-all-listings.php';
            $v        = new WPBDP__Views__All_Listings( array( 'menu' => false ) );
            $listings = $v->dispatch();
        } else {
            $listings = '';
        }

        $html = $this->_render_page( 'main_page', array( 'listings' => $listings ) );

        return $html;
    }

}
