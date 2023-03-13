<?php
/**
 * @package WPBDP/Views/Show Category
 */

class WPBDP__Views__Show_Category extends WPBDP__View {

    public function dispatch() {
        global $wp_query;

        wpbdp_push_query( $wp_query );

        $term = get_queried_object();

        $html = '';

        if ( is_object( $term ) ) {
			if ( is_callable( 'WPBDP__Themes_Compat::is_block_theme' ) && WPBDP__Themes_Compat::is_block_theme() ) {
				// This isn't loaded when disable-cpt is on.
				global $wpbdp;
				$wpbdp->template_integration->prep_tax_head();
			}
			$html = $this->get_taxonomy_html( $term );
        }

        wpbdp_pop_query();

        return $html;
    }

	/**
	 * @since 6.2.2
	 * @return string
	 */
	protected function get_taxonomy_html( $term ) {
		global $wp_query;

		$searching    = ( ! empty( $_GET ) && ! empty( $_GET['kw'] ) );
		$term->is_tag = false;

		return $this->_render(
			'category',
			array(
				'title'        => $term->name,
				'category'     => $term,
				'query'        => $wp_query,
				'in_shortcode' => false,
				'is_tag'       => false,
				'searching'    => $searching,
			),
			$searching ? '' : 'page'
		);
	}
}
