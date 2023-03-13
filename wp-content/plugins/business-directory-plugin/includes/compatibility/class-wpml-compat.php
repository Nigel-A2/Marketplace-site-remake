<?php
/**
 * WPML compatibility
 *
 * @package WPBDP/Includes/Compatibility/WPML
 */

/**
 * Class WPBDP_WPML_Compat
 */
class WPBDP_WPML_Compat {

    private $wpml;

    public function __construct() {
        $this->wpml = $GLOBALS['sitepress'];

        if ( ! is_admin() || $this->is_doing_ajax() ) {
            add_filter( 'wpbdp_get_page_id', array( &$this, 'page_id' ), 10, 2 );

            add_filter( 'wpbdp_listing_link', array( &$this, 'add_lang_to_link' ) );
            add_filter( 'wpbdp_category_link', array( &$this, 'add_lang_to_link' ) );
            add_filter( 'wpbdp_tag_link', array( &$this, 'add_lang_to_link' ) );
            add_filter( 'wpbdp_url_base_url', array( &$this, 'fix_get_page_link' ), 10, 2 );
            add_filter( 'wpbdp_url', array( &$this, 'correct_page_link' ), 10, 3 );
            add_filter( 'wpbdp_ajax_url', array( $this, 'filter_ajax_url' ) );
            add_filter( 'wpbdp_listing_images_listing_id', array( $this, 'get_images_listing_id' ), 10, 1 );

            add_filter( 'wpbdp_render_field_label', array( &$this, 'translate_form_field_label' ), 10, 2 );
            add_filter( 'wpbdp_render_field_description', array( &$this, 'translate_form_field_description' ), 10, 2 );
            add_filter( 'wpbdp_display_field_label', array( &$this, 'translate_form_field_label' ), 10, 2 );

            add_filter( 'wpbdp_form_field_data', array( &$this, 'translate_form_field_option_data' ), 10, 3 );

            add_filter( 'wpbdp_category_fee_selection_label', array( &$this, 'translate_fee_label' ), 10, 2 );
            add_filter( 'wpbdp_plan_description_for_display', array( $this, 'translate_fee_description' ), 10, 2 );

            add_filter( 'icl_ls_languages', array( &$this, 'language_switcher' ) );

            // Regions.
            add_filter( 'wpbdp_region_link', array( &$this, 'add_lang_to_link' ) );

            // Work around non-unique slugs for pages.
            add_action( 'wpbdp_query_flags', array( $this, 'maybe_change_query' ) );

            add_action( 'wpbdp_before_ajax_dispatch', array( $this, 'before_ajax_dispatch' ) );
        }

        add_action( 'admin_footer', array( $this, 'maybe_register_some_strings' ) );

        // Regions.
        add_filter( 'wpbdp_regions__get_hierarchy_option', array( &$this, 'use_cache_per_lang' ) );
        add_action( 'wpbdp_regions_clean_cache', array( &$this, 'clean_cache_per_lang' ) );

        add_action( 'wpbdp_main_box_hidden_fields', array( $this, 'search_lang_field' ) );
    }

	protected function is_doing_ajax() {
		return wp_doing_ajax();
	}

    public function get_current_language() {
        return apply_filters( 'wpml_current_language', null );
    }

    public function fix_get_page_link( $link, $post_id ) {
        if ( ! wpbdp_rewrite_on() ) {
            return $link;
        }

        $page_ids = wpbdp_get_page_ids( 'main' );
        if ( ! in_array( $post_id, $page_ids ) ) {
            return $link;
        }

        $link = preg_replace( '/\?.*/', '', $link );
        return $link;
    }

    public function page_id( $id, $page_name = '' ) {
        $lang = $this->get_current_language();

        if ( ! $lang ) {
            return $id;
        }

        $trans_id = icl_object_id( $id, 'page', false, $lang );
        if ( ! $trans_id ) {
            return $id;
        }

        return $trans_id;
    }

    public function add_lang_to_link( $link ) {
        $lang = '';

        $index = strpos( $link, '?' );
        if ( false !== $index ) {
            // We honor the ?lang argument from the link itself (if present).
            $data = array();
            wp_parse_str( substr( $link, $index + 1 ), $data );

            if ( ! empty( $data['lang'] ) ) {
                $lang = $data['lang'];
            }
        } else {
            $lang = $this->get_current_language();
        }

        if ( ! $lang ) {
            return $link;
        }

        if ( 1 === $this->get_lang_url_type() ) {
            return $link;
        }

        $link = add_query_arg( 'lang', $lang, $link );
        return $link;
    }

    /**
     * Add current language to the URL used in BD's ajax requests.
     *
     * @param string $ajax_url Default value for Ajax URL.
     *
     * @since 5.0.3
     */
    public function filter_ajax_url( $ajax_url ) {
        $lang = $this->get_current_language();

        if ( ! $lang ) {
            return $ajax_url;
        }

        return add_query_arg( 'lang', $lang, $ajax_url );
    }

    public function correct_page_link( $link, $name = '', $arg0 = '' ) {
        $lang = $this->get_current_language();

        if ( ! $lang ) {
            return $link;
        }

        switch ( $name ) {
            case 'main':
            case 'edit_listing':
            case 'upgrade_listing':
            case 'delete_listing':
            case 'all_listings':
            case 'view_listings':
            case 'submit_listing':
                $link = $this->maybe_add_lang_query_arg( $link, $lang );
                break;

            default:
                break;
        }

        return $link;
    }

    private function maybe_add_lang_query_arg( $link, $lang ) {
        if ( 3 === $this->get_lang_url_type() ) {
            $link = add_query_arg( 'lang', $lang, $link );
        }

        return $link;
    }

	/**
	 * The language_negotiation_type sets the URL structure for a translated page.
	 * 3 = ?lang=en
	 */
	private function get_lang_url_type() {
		return intval( $this->wpml->get_setting( 'language_negotiation_type' ) );
	}

    public function translate_link( $link, $lang = null ) {
        $lang = $lang ? $lang : $this->get_current_language();

        if ( ! $lang ) {
            return $link;
        }

		$use_query_arg = 3 === $this->get_lang_url_type() || ! wpbdp_rewrite_on();

		if ( $use_query_arg ) {
			// Replace the the query arg with current language code.
			$link = add_query_arg( 'lang', $lang, $link );
		} else {
            $main_id         = wpbdp_get_page_id( 'main' );
            $main_link       = $this->fix_get_page_link( get_page_link( $main_id ), $main_id );
            $main_trans_link = apply_filters( 'wpml_permalink', $main_link, $lang );

            $link = str_replace( $main_link, $main_trans_link, $link );
            $link = $this->maybe_add_lang_query_arg( $link, $lang );
		}

        return $link;
    }

    public function language_switcher( $languages ) {
        global $wpbdp;

        $action = wpbdp_current_view();
        $this->workaround_autoids();

        switch ( $action ) {
			case 'show_category':
				$this->add_category_languages( $languages );
				break;

			case 'show_listing':
				$this->add_listing_languages( $languages );
				break;

			case 'show_tag':
                $this->add_tag_languages( $languages );
				break;
        }

        $this->workaround_autoids();

        return $languages;
    }

    public function workaround_autoids() {
        global $sitepress_settings;

        if ( ! $this->wpml->get_setting( 'auto_adjust_ids' ) || ! isset( $sitepress_settings ) ) {
            return;
        }

        if ( ! isset( $this->workaround ) ) {
            $this->workaround = true;
        } else {
            $this->workaround = ! $this->workaround;
        }

        if ( $this->workaround ) {
            // Magic here.
            $sitepress_settings['auto_adjust_ids'] = 0;
        } else {
            // Undo magic.
            $sitepress_settings['auto_adjust_ids'] = 1;
        }
    }

	/**
	 * Get a link to the listing in the current language.
	 *
	 * @param array $languages
	 */
	private function add_listing_languages( &$languages ) {
		$listing_id = get_the_ID();

		if ( ! $listing_id ) {
			global $wp_query;

			$listing_id = $wp_query->get_queried_object()->ID;
			if ( ! $listing_id ) {
				return;
			}
		}

		$trid         = apply_filters( 'wpml_element_trid', null, $listing_id, 'post_' . WPBDP_POST_TYPE );
		$translations = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_' . WPBDP_POST_TYPE );

		foreach ( $languages as $l_code => $l ) {
			if ( ! isset( $translations[ $l_code ] ) ) {
				unset( $languages[ $l_code ] );
				continue;
			}

			$languages[ $l_code ]['url'] = apply_filters( 'wpml_permalink', get_permalink( $translations[ $l_code ]->element_id ), $l_code );
		}
	}

	/**
	 * Check for a translated category page.
	 *
	 * @param array $languages
	 */
	private function add_category_languages( &$languages ) {
        $category_id = wpbdp_current_category_id();
        if ( $category_id ) {
            $this->add_term_link( $category_id, WPBDP_CATEGORY_TAX, $languages );
        }
	}

	/**
	 * Check for a translated tag page.
	 *
	 * @param array $languages
	 */
	private function add_tag_languages( &$languages ) {
		$tag_id = wpbdp_current_tag_id();
		if ( $tag_id ) {
			$this->add_term_link( $tag_id, WPBDP_TAGS_TAX, $languages );
		}
	}

	/**
	 * Get translated link for a term.
	 *
	 * @param int    $term_id The tag or category ID.
	 * @param string $tax The name of the taxonomy
	 * @param array  $languages
	 */
	private function add_term_link( $term_id, $tax, &$languages ) {
		foreach ( $languages as $l_code => $l ) {
			$trans_id = (int) apply_filters( 'wpml_object_id', $term_id, $tax, false, $l['language_code'] );
			$link     = get_term_link( $trans_id, $tax );

			if ( ! $trans_id || is_wp_error( $link ) ) {
				unset( $languages[ $l_code ] );
				continue;
			}

			$languages[ $l_code ]['url'] = $this->translate_link( $link, $l['language_code'] );
		}
	}

	/**
	 * @param WP_Query $query
	 */
    public function maybe_change_query( $query ) {
        if ( ! $query->wpbdp_is_main_page || empty( $query->query['page_id'] ) ) {
            return;
        }

        $lang     = $this->get_current_language();
        $page_id  = $query->query['page_id'];
        $trans_id = icl_object_id( $page_id, 'page', false, $lang );

        $query->set( 'page_id', $trans_id );
    }

    public function before_ajax_dispatch( $handler ) {
        $lang = wpbdp_get_var( array( 'param' => 'lang' ) );
        if ( empty( $lang ) ) {
            return;
        }

        do_action( 'wpml_switch_language', $lang );
    }

    // {{{ Form Fields integration.
    public function register_form_fields_strings() {
        if ( isset( $_GET['action'] ) || ! function_exists( 'icl_register_string' ) ) {
            return;
        }

        $fields = wpbdp_get_form_fields();

        foreach ( $fields as &$f ) {
            icl_register_string(
                'Business Directory Plugin',
                sprintf( 'Field #%d - label', $f->get_id() ),
                $f->get_label()
            );

            if ( $f->get_description() ) {
                icl_register_string(
                    'Business Directory Plugin',
                    sprintf( 'Field #%d - description', $f->get_id() ),
                    $f->get_description()
                );
            }

            if ( in_array( $f->get_association(), array( 'meta', 'tags' ) ) && in_array( $f->get_field_type_id(), array( 'select', 'multiselect', 'checkbox', 'radio' ) ) ) {
                if ( $f->data( 'options' ) ) {
                    icl_register_string(
                        'Business Directory Plugin',
                        sprintf( 'Field #%d - options', $f->get_id() ),
                        implode( "\n", $f->data( 'options' ) )
                    );
                }
            }
        }
    }

    public function translate_form_field_label( $label, $field ) {
        if ( ! is_object( $field ) || ! function_exists( 'icl_t' ) ) {
            return $label;
        }

        return icl_t(
            'Business Directory Plugin',
            sprintf( 'Field #%d - label', $field->get_id() ),
            $field->get_label()
        );
    }

    public function translate_form_field_description( $description, $field ) {
        if ( ! is_object( $field ) || ! function_exists( 'icl_t' ) ) {
            return $description;
        }

        return icl_t(
            'Business Directory Plugin',
            sprintf( 'Field #%d - description', $field->get_id() ),
            $field->get_description()
        );
    }

    public function translate_form_field_option_data( $value, $key, $field ) {
        if ( ! is_object( $field ) || empty( $value ) || 'options' !== $key || ! function_exists( 'icl_t' ) || ! is_array( $value ) ) {
            return $value;
        }

        if ( ! in_array( $field->get_association(), array( 'meta', 'tags' ) ) || ! in_array( $field->get_field_type_id(), array( 'select', 'multiselect', 'checkbox', 'radio' ) ) ) {
            return $value;
        }

        $options = icl_t(
            'Business Directory Plugin',
            sprintf( 'Field #%d - options', $field->get_id() ),
            implode( "\n", $value )
        );

        $options = array_map( 'trim', explode( "\n", $options ) );

		if ( count( $value ) !== count( $options ) ) {
            return $value;
        }

        return array_combine( array_keys( $value ), $options );
    }

    // }}}
    public function maybe_register_some_strings() {
        $admin_page = wpbdp_get_var( array( 'param' => 'page' ) );

        switch ( $admin_page ) {
            case 'wpbdp-admin-fees':
                $this->register_fees_strings();
                break;
            case 'wpbdp_admin_formfields':
                $this->register_form_fields_strings();
                break;
            default:
                break;
        }
    }

    // {{{ Fees API integration.
    public function register_fees_strings() {
        if ( isset( $_GET['action'] ) || ! function_exists( 'icl_register_string' ) ) {
            return;
        }

        $fees = wpbdp_get_fee_plans(
            array(
                'enabled' => 'all',
            )
        );

        foreach ( $fees as &$f ) {
            icl_register_string(
                'Business Directory Plugin',
                sprintf( 'Plan label (#%d)', $f->id ),
                $f->label
            );
            icl_register_string(
                'Business Directory Plugin',
                sprintf( 'Plan description (#%d)', $f->id ),
                $f->description
            );
        }
    }

    public function translate_fee_label( $label, $fee ) {
        if ( ! function_exists( 'icl_t' ) ) {
            return $label;
        }

        return icl_t(
            'Business Directory Plugin',
            sprintf( 'Plan label (#%d)', $fee->id ),
            $fee->label
        );
    }

    public function translate_fee_description( $desc, $fee ) {
        if ( ! function_exists( 'icl_t' ) ) {
            return $desc;
        }

        return icl_t(
            'Business Directory Plugin',
            sprintf( 'Plan description (#%d)', $fee->id ),
            $fee->description
        );
    }

    // }}}
    // Regions. {{{
    public function use_cache_per_lang( $option ) {
        $lang = $this->get_current_language();

        if ( ! $lang ) {
            return $option;
        }

        return $option . '-' . $lang;
    }

    public function clean_cache_per_lang( $opt ) {
        $langs = icl_get_languages( 'skip_missing=0' );

        if ( ! $langs ) {
            return;
        }

        foreach ( $langs as $l ) {
            $code = $l['language_code'];

            delete_option( $opt . '-' . $code );
        }
    }

	/**
	 * Listing thumbnail and images.
	 */
    public function get_images_listing_id( $listing_id ) {
        if ( 1 != apply_filters( 'wpml_element_translation_type', null, $listing_id, 'post_' . WPBDP_POST_TYPE ) ) {
            return $listing_id;
        }

        $trid         = apply_filters( 'wpml_element_trid', null, $listing_id, 'post_' . WPBDP_POST_TYPE );
        $translations = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_' . WPBDP_POST_TYPE );

        foreach ( $translations as $lang => $translate ) {

            if ( $translate->original ) {
                return $translate->element_id;
            }
        }

        return $listing_id;
    }

    public function search_lang_field() {
        $lang = $this->get_current_language();

        if ( ! $lang ) {
            return;
        }

        echo '<input type="hidden" name="lang" value="' . esc_attr( $lang ) . '" />';
    }
}
