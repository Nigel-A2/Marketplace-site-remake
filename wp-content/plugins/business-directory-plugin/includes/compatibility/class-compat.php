<?php
/**
 * @package WPBDP/Compatibility
 */

require_once WPBDP_PATH . 'includes/compatibility/deprecated.php';
class WPBDP_Compat {

    public function __construct() {
        $this->workarounds_for_wp_bugs();
        $this->load_integrations();

        if ( wpbdp_get_option( 'disable-cpt' ) ) {
			self::cpt_compat_mode();
        } else {
            require_once WPBDP_PATH . 'includes/compatibility/class-themes-compat.php';
            new WPBDP__Themes_Compat();
        }
    }

    public function load_integrations() {
        if ( isset( $GLOBALS['sitepress'] ) ) {
            require_once WPBDP_PATH . 'includes/compatibility/class-wpml-compat.php';
			new WPBDP_WPML_Compat();
        }

        if ( function_exists( 'bcn_display' ) ) {
            require_once WPBDP_PATH . 'includes/compatibility/class-navxt-integration.php';
			new WPBDP_NavXT_Integration();
        }

        if ( class_exists( 'Advanced_Excerpt' ) ) {
            require_once WPBDP_PATH . 'includes/compatibility/class-advanced-excerpt-integration.php';
			new WPBDP_Advanced_Excerpt_Integration();
        }

        if ( defined( 'CUSTOM_PERMALINKS_PLUGIN_VERSION' ) ) {
            require_once WPBDP_PATH . 'includes/compatibility/class-custom-permalinks-integration.php';
			new WPBDP_Custom_Permalink_Integration();
        }

        if ( class_exists( 'acf' ) && 'Bold Move' === wp_get_theme()->Name ) {
            require_once WPBDP_PATH . 'includes/compatibility/class-acf-boldmove-compat.php';
			new WPBDP_ACF_Compat();
        }

        if ( class_exists( 'Cornerstone_Plugin' ) ) {
            require_once WPBDP_PATH . 'includes/compatibility/class-cornerstone-compat.php';
			new WPBDP_Cornerstone_Compat();
        }

        if ( class_exists( 'FLTheme' ) ) {
            require_once WPBDP_PATH . 'includes/compatibility/class-beaver-themer-compat.php';
			new WPBDP_Beaver_Themer_Compat();
        }

		// Yoast SEO.
		if ( defined( 'WPSEO_VERSION' ) ) {
			add_action( 'wp_head', array( &$this, 'yoast_maybe_force_taxonomy' ), 0 );
		}
    }

    public function cpt_compat_mode() {
        require_once WPBDP_PATH . 'includes/compatibility/class-cpt-compat-mode.php';
        $nocpt = new WPBDP__CPT_Compat_Mode();
    }

	/**
	 * If the category page is using a page template for the current theme,
	 * remove the singular flag momentarily.
	 *
	 * @since 6.2.8
	 */
	public function yoast_maybe_force_taxonomy() {
		global $wp_query;
		if ( wpbdp_is_taxonomy() && $wp_query->is_singular ) {
			$wp_query->is_singular = false;
			add_action( 'wpseo_head', array( &$this, 'yoast_force_page' ), 9999 );
		}
	}

	/**
	 * Switch back to singular, since the current theme needs it.
	 *
	 * @since 6.2.8
	 */
	public function yoast_force_page() {
		global $wp_query;
		$wp_query->is_singular = true;
	}

    // Work around WP bugs. {{{
    public function workarounds_for_wp_bugs() {
        // #1466 (related to https://core.trac.wordpress.org/ticket/28081).
        add_filter( 'wpbdp_query_clauses', array( &$this, '_fix_pagination_issue' ), 10, 2 );
    }

    public function _fix_pagination_issue( $clauses, $query ) {
        $posts_per_page = intval( $query->get( 'posts_per_page' ) );
        $paged          = intval( $query->get( 'paged' ) );

        if ( -1 != $posts_per_page || $paged <= 1 ) {
            return $clauses;
        }

        // Force no results for pages outside of the scope of the query.
        $clauses['where'] .= ' AND 1=0 ';

        return $clauses;
    }

}
