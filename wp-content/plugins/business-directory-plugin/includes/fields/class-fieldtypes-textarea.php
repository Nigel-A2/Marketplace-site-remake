<?php
/**
 * @package WPBDP\FieldTypes\TextArea
 */

class WPBDP_FieldTypes_TextArea extends WPBDP_Form_Field_Type {

    private $tinymce_settings = array();
    private $quicktags_settings = array();

    public function __construct() {
		parent::__construct( _x( 'Textarea', 'form-fields api', 'business-directory-plugin' ) );

        add_filter( 'wpbdp_form_field_html_value', array( $this, 'maybe_shorten_output_in_excerpt' ), 10, 4 );
        add_filter( 'wpbdp_render_listing_fields', array( $this, 'maybe_hide_excerpt_field' ), 10, 3 );
    }

    public function get_id() {
        return 'textarea';
    }

	public function render_field_inner( &$field, $value, $context, &$extra = null, $field_settings = array() ) {
        // render textareas as textfields when searching
        if ( $context == 'search' ) {
            global $wpbdp;
            return $wpbdp->formfields->get_field_type( 'textfield' )->render_field_inner( $field, $value, $context, $extra, $field_settings );
        }

        // @since 5.5.12
        $value = apply_filters_ref_array( 'wpbdp_fields_text_value_for_rendering', array( $value, null,  $field ) );

        if ( $this->should_show_wysiwyg_editor( $field ) && $this->can_show_wysiwyg_editor() ) {
            $html = $this->render_wysiwyg_editor( $field, $value );
        } else {
            $html = sprintf(
                '<textarea id="%s" name="%s">%s</textarea>',
                'wpbdp-field-' . $field->get_id(),
                apply_filters( 'wpbdp_fields_text_input_name', 'listingfields[' . $field->get_id() . ']', $field, $context, $extra, $field_settings ),
                $value ? esc_attr( $value ) : ''
            );
        }

        return $html;
    }

    private function should_show_wysiwyg_editor( $field ) {
        return 'content' == $field->get_association() && $field->data( 'allow_html' ) && $field->data( 'wysiwyg_editor' );
    }

    private function can_show_wysiwyg_editor() {
        if ( ! function_exists( 'wp_enqueue_editor' ) ) {
            return false;
        }

        $wp_editor_path = ABSPATH . WPINC . '/class-wp-editor.php';

        if ( ! file_exists( $wp_editor_path ) ) {
            return false;
        }

        require_once $wp_editor_path;

        if ( ! class_exists( '_WP_Editors' ) ) {
            return false;
        }

        if ( ! method_exists( '_WP_Editors', 'parse_settings' ) || ! method_exists( '_WP_Editors', 'editor_settings' ) ) {
            return false;
        }

        return true;
    }

    private function render_wysiwyg_editor( $field, $value ) {
        wp_enqueue_editor();

        // _WP_Editors::editor_settings() schedules editor_js() and enqueue_scripts()
        // static class methods to print initialization for the core editor.
        //
        // We call editor_settings() to get the same settings used in the core editor,
        // but we don't want the initialization code. If editor_js() and
        // enqueue_scripts() are not already scheduled, we will remove them as
        // action handlers a few lines below.
        if ( is_admin() ) {
            $action_name = 'admin_print_footer_scripts';
        } else {
            $action_name = 'wp_print_footer_scripts';
        }

        $is_editor_js_scheduled = has_action( $action_name, array( '_WP_Editors', 'editor_js' ) );
        $is_enqueue_scripts_scheduled = has_action( $action_name, array( '_WP_Editors', 'enqueue_scripts' ) );

        // _WP_Editors does not offers direct access to the TinyMCE and QuickTags
        // arrays of settings.
        add_filter( 'tiny_mce_before_init', array( $this, 'capture_tinymce_settings' ), 100, 2 );
        add_filter( 'quicktags_settings', array( $this, 'capture_quicktag_settings' ), 100, 2 );

        $settings = array(
            'drag_drop_upload' => false,
            'media_buttons' => false,
            'quicktags' => ( (bool) $field->data( 'wysiwyg_images' ) ) ? true : false,
        );

        // Trick _WP_Editors into generating the array of TinyMCE and QuickTags
        // settings for us, as it would be generated for the core editor.
        $settings = _WP_Editors::parse_settings( 'wpbdp-field-' . $field->get_id(), $settings );
        _WP_Editors::editor_settings( 'wpbdp-field-' . $field->get_id(), $settings );

        // We are interested in this editor's settings only.
		remove_filter( 'tiny_mce_before_init', array( $this, 'capture_tinymce_settings' ), 100 );
		remove_filter( 'quicktags_settings', array( $this, 'capture_quicktag_settings' ), 100 );

        // Removing _WP_Editors::editor_js if it was not previously configured
        // as a handler.
        if ( ! $is_editor_js_scheduled ) {
            remove_action( $action_name, array( '_WP_Editors', 'editor_js' ), 50 );
        }

        // Removing _WP_Editors::enqueue_scripts() if it was not previously
        // configured as a handler.
        if ( ! $is_enqueue_scripts_scheduled ) {
            remove_action( $action_name, array( '_WP_Editors', 'enqueue_scripts' ), 1 );
        }

        $html = sprintf(
            '<textarea id="%s" class="wpbdp-editor-area" name="%s">%s</textarea>',
            'wpbdp-field-' . $field->get_id(),
            'listingfields[' . $field->get_id() . ']',
            $value ? esc_attr( $value ) : ''
        );

		$html .= sprintf(
            '<script>
                var WPBDPTinyMCESettings = WPBDPTinyMCESettings || {};

                WPBDPTinyMCESettings[ \'%s\' ] = {
                    \'tinymce\': %s,
                    \'quicktags\': %s
                };
            </script>',
            'wpbdp-field-' . $field->get_id(),
            $this->parse_tinymce_settings( $this->tinymce_settings ),
            $this->parse_tinymce_settings( $this->quicktags_settings )
        );

        return $html;
    }

    public function capture_tinymce_settings( $settings, $editor_id ) {
        $this->tinymce_settings = $settings;
        return $settings;
    }

    public function capture_quicktag_settings( $settings, $editor_id ) {
        $this->quicktags_settings = $settings;
        return $settings;
    }

    /**
     * A copy of _WP_Editors::_parse_init().
     *
     * @since 5.0
     */
    private function parse_tinymce_settings( $init ) {
        $options = '';

        foreach ( $init as $key => $value ) {
            if ( is_bool( $value ) ) {
                $val = $value ? 'true' : 'false';
                $options .= $key . ':' . $val . ',';
                continue;
            } elseif ( ! empty( $value ) && is_string( $value ) && (
				( '{' == $value[0] && '}' == $value[ strlen( $value ) - 1 ] ) ||
				( '[' == $value[0] && ']' == $value[ strlen( $value ) - 1 ] ) ||
                preg_match( '/^\(?function ?\(/', $value ) ) ) {

                $options .= $key . ':' . $value . ',';
                continue;
            }
            $options .= $key . ':"' . $value . '",';
        }

        return '{' . trim( $options, ' ,' ) . '}';
    }

    public function get_supported_associations() {
        return array( 'title', 'excerpt', 'content', 'meta' );
    }

	public function render_field_settings( &$field = null, $association = null ) {
        $settings = array();

        $settings['allow_html'][] = _x( 'Allow HTML input for this field?', 'form-fields admin', 'business-directory-plugin' );
        $settings['allow_html'][] = '<input type="checkbox" value="1" name="field[allow_html]" ' . ( $field && $field->data( 'allow_html' ) ? ' checked="checked"' : '' ) . ' />';

        $settings['allow_iframes'][] = _x( 'Allow IFRAME tags in content?', 'form-fields admin', 'business-directory-plugin' );
        $settings['allow_iframes'][] =
            '<div class="iframe-confirm wpbdp-note warning">' .
            '<p>' . _x( 'Enabling iframe support in your listings can allow users to execute arbitrary scripts on a page if they want, which can possibly infect your site with malware. We do NOT recommend using this setting UNLESS you are posting the listings yourself and have sole control over the content. Are you sure you want to enable this?', 'admin form-fields', 'business-directory-plugin' ) . '</p>' .
            '<a href="#" class="button no">' . esc_html__( 'No', 'business-directory-plugin' ) . '</a> ' .
            '<a href="#" class="button button-primary yes">' . esc_html__( 'Yes', 'business-directory-plugin' ) . '</a>' .
            '</div>' .
            '<input type="checkbox" value="1" name="field[allow_iframes]" ' . ( $field && $field->data( 'allow_iframes' ) ? ' checked="checked"' : '' ) . ' />';

        if ( ( $field && in_array( $field->get_association(), array( 'content', 'excerpt' ) ) ) || ( in_array( $association, array( 'content', 'excerpt' ) ) ) ) {
            $settings['allow_shortcodes'][] = _x( 'Allow WordPress shortcodes in this field?', 'form-fields admin', 'business-directory-plugin' );
            $settings['allow_shortcodes'][] = '<input type="checkbox" value="1" name="field[allow_shortcodes]" ' . ( $field && $field->data( 'allow_shortcodes' ) ? ' checked="checked"' : '' ) . ' />';
        }

        if ( ( $field && $field->get_association() == 'content' ) || ( $association == 'content' ) ) {
            $settings['wysiwyg_editor'][] = _x( 'Display a WYSIWYG editor on the frontend?', 'form-fields admin', 'business-directory-plugin' );
            $settings['wysiwyg_editor'][] = '<input type="checkbox" value="1" name="field[wysiwyg_editor]" ' . ( $field && $field->data( 'wysiwyg_editor' ) ? ' checked="checked"' : '' ) . ' />';

            $desc                         = __( 'Warning: Users can use this feature to get around your image limits in plans.', 'business-directory-plugin' );
            $settings['wysiwyg_images'][] = esc_html__( 'Allow images in HTML?', 'business-directory-plugin' );
            $settings['wysiwyg_images'][] = '<input type="checkbox" value="1" name="field[wysiwyg_images]" ' . ( $field && $field->data( 'wysiwyg_images' ) ? ' checked="checked"' : '' ) . ' /> <span class="description">' . esc_html( $desc ) . '</span>';

            $desc                        = _x( '<b>Advanced users only!</b> Unless you\'ve been told to change this, don\'t switch it unless you know what you\'re doing.', 'form-fields admin', 'business-directory-plugin' );
            $settings['allow_filters'][] = _x( 'Apply "the_content" filter before displaying this field?', 'form-fields admin', 'business-directory-plugin' );
            $settings['allow_filters'][] = '<input type="checkbox" value="1" name="field[allow_filters]" ' . ( $field && $field->data( 'allow_filters' ) ? ' checked="checked"' : '' ) . ' /> <span class="description">' . $desc . '</span>';

            $settings['excerpt_override'][] = _x( 'Use shortened version of Description field as excerpt', 'form-fields admin', 'business-directory-plugin' );
            $settings['excerpt_override'][] = '<input type="radio" value="1" name="field[excerpt_override]" ' . ( $field && 1 === $field->data( 'excerpt_override' ) ? ' checked="checked"' : '' ) . '/>' . _x( 'Enable always (override the Short Description given with a shortened Long Description)', 'form-fields admin', 'business-directory-plugin' ) . '<br/>
                                               <input type="radio" value="2" name="field[excerpt_override]" ' . ( $field && 2 === $field->data( 'excerpt_override' ) ? ' checked="checked"' : '' ) . '/>' . _x( 'Enable conditionally (override ONLY when Short Description is empty with a shortened Long Description)', 'form-fields admin', 'business-directory-plugin' ) . '<br/>
                                               <input type="radio" value="0" name="field[excerpt_override]" ' . ( $field && ! in_array( $field->data( 'excerpt_override' ), array( 1, 2 ) ) ? ' checked="checked"' : '' ) . '/>' . _x( 'Disable (use the Short Description all the time, empty or not)', 'form-fields admin', 'business-directory-plugin' );

            $desc                     = _x( 'Truncates the description field to the value set here. To display all of the description, set to 0.', 'form-fields admin', 'business-directory-plugin' );
            $settings['max_length'][] = _x( 'Number of Characters from Short Description/Excerpt to Display in List View (only)', 'form-fields admin', 'business-directory-plugin' );
            $settings['max_length'][] = '<input type="number" value="' . ( $field && $field->data( 'max_length' ) ? $field->data( 'max_length' ) : 0 ) . '" name="field[max_length]" /> <span class="wpbdp-setting-description">' . $desc . '</span>';
        }

        if ( ( $field && $field->get_association() == 'excerpt' ) || ( $association == 'excerpt' ) ) {
            $settings['auto_excerpt'][] = _x( 'Automatically generate excerpt from content field?', 'form-fields admin', 'business-directory-plugin' );
            $settings['auto_excerpt'][] = '<input type="checkbox" value="1" name="field[auto_excerpt]" ' . ( $field && $field->data( 'auto_excerpt' ) ? ' checked="checked"' : '' ) . ' /> ';
        }

        return self::render_admin_settings( $settings );
    }

    public function process_field_settings( &$field ) {
        $field->set_data( 'allow_html', isset( $_POST['field']['allow_html'] ) ? (bool) intval( $_POST['field']['allow_html'] ) : false );
        $field->set_data( 'allow_iframes', isset( $_POST['field']['allow_iframes'] ) ? (bool) intval( $_POST['field']['allow_iframes'] ) : false );
        $field->set_data( 'allow_filters', isset( $_POST['field']['allow_filters'] ) ? (bool) intval( $_POST['field']['allow_filters'] ) : false );
        $field->set_data( 'allow_shortcodes', isset( $_POST['field']['allow_shortcodes'] ) ? (bool) intval( $_POST['field']['allow_shortcodes'] ) : false );
        $field->set_data( 'max_length', isset( $_POST['field']['max_length'] ) ? intval( $_POST['field']['max_length'] ) : 0 );
        $field->set_data( 'wysiwyg_editor', isset( $_POST['field']['wysiwyg_editor'] ) ? (bool) intval( $_POST['field']['wysiwyg_editor'] ) : false );
        $field->set_data( 'wysiwyg_images', isset( $_POST['field']['wysiwyg_images'] ) ? (bool) intval( $_POST['field']['wysiwyg_images'] ) : false );
        $field->set_data( 'excerpt_override', isset( $_POST['field']['excerpt_override'] ) ? intval( $_POST['field']['excerpt_override'] ) : 0 ); // Input var okay.
        $field->set_data( 'auto_excerpt', isset( $_POST['field']['auto_excerpt'] ) ? (bool) intval( $_POST['field']['auto_excerpt'] ) : false );
		$field->set_data( 'word_count', ( in_array( 'word_number', $field->get_validators() ) && isset( $_POST['field']['word_count'] ) ) ? intval( $_POST['field']['word_count'] ) : 0 );
    }

    public function store_field_value( &$field, $post_id, $value ) {

		$tags  = $this->allowed_html_tags( $field );
		$value = wp_kses( $value, $tags );

        return parent::store_field_value( $field, $post_id, $value );
    }

	/**
	 * @return array
	 */
	private function allowed_html_tags( $field ) {

		$allow_html = $field->data( 'allow_html' ) || $field->data( 'allow_iframes' ) || $field->data( 'wysiwyg_editor' );
		if ( ! $allow_html ) {
			return array();
		}

		$tags = wp_kses_allowed_html( 'post' );

		if ( isset( $tags['img'] ) && 'content' === $field->get_association() && ! $field->data( 'wysiwyg_images' ) ) {
			// Don't allow images when the setting is available.
			unset( $tags['img'] );
		}

		if ( $field->data( 'allow_iframes' ) ) {
			$tags = self::allow_iframe( $tags );
			add_filter( 'wp_kses_allowed_html', array( $this, 'allow_iframe' ), 1 );
		}

		return $tags;
	}

	/**
	 * @since 5.8.2
	 */
	public function allow_iframe( $tags ) {
		$tags['iframe'] = array(
			'align'        => true,
			'width'        => true,
			'height'       => true,
			'frameborder'  => true,
			'name'         => true,
			'src'          => true,
			'id'           => true,
			'class'        => true,
			'style'        => true,
			'scrolling'    => true,
			'marginwidth'  => true,
			'marginheight' => true,
		);

		return $tags;
	}

    public function get_field_value( &$field, $post_id ) {
        $value = parent::get_field_value( $field, $post_id );

        // Only return auto-generated excerpt if there's no value at all.
		if ( 'excerpt' === $field->get_association() && $field->data( 'auto_excerpt' ) && ! $value ) {
            $value = $this->get_excerpt_value_from_post( $post_id );
		}

        return $value;
    }

    public function get_field_html_value( &$field, $post_id ) {
        $value = apply_filters( 'wpbdp_form_field_html_value', $field->value( $post_id ), $post_id, $field );

		$allowed_tags = self::allowed_html_tags( $field );

        $value = wp_kses( $value, $allowed_tags );

        if ( 'content' == $field->get_association() ) {
            if ( $field->data( 'allow_filters' ) ) {
                $value = apply_filters( 'the_content', $value );
            } else {
				$value = wpautop( $value );
				if ( $field->data( 'allow_shortcodes' ) ) {
					$value = do_shortcode( shortcode_unautop( $value ) );
				}
            }
        } elseif ( 'excerpt' == $field->get_association() ) {
            if ( $field->data( 'auto_excerpt' ) ) {
                $value = $this->get_excerpt_value_from_post( $post_id );
            }

			if ( $field->data( 'allow_shortcodes' ) ) {
				$value = wpautop( $value );
				$value = do_shortcode( shortcode_unautop( $value ) );
			} elseif ( $field->data( 'allow_html' ) ) {
				$value = wpautop( $value );
			}

            if ( ! $field->data( 'allow_html' ) ) {
                $value = nl2br( $value );
            }
        } else {
            if ( $field->data( 'allow_html' ) ) {
                $value = wpautop( $value );
            } else {
                $value = nl2br( $value );
            }
        }

        return $value;
    }

    private function get_excerpt_value_from_post( $post_id ) {
        global $post;

        $current_post = $post;
        $post = get_post( $post_id );
		$value = apply_filters( 'get_the_excerpt', '', $post );
        $post = $current_post;

        return $value;
    }

    public function get_field_csv_value( &$field, $post_id ) {
        $value = parent::get_field_csv_value( $field, $post_id );
        $value = str_replace( "\r\n", "\n", $value );
        $value = str_replace( "\n", "\\n", $value );

        return $value;
    }

    /**
     * Truncate content fields in excerpt (if needed).
     */
    public function maybe_shorten_output_in_excerpt( $value, $post_id, $field, $display_context = 'listing' ) {
        if ( 'excerpt' != $display_context ) {
            return $value;
        }

        if ( 'content' != $field->get_association() ) {
            return $value;
        }

		if ( $field->data( 'max_length' ) < 1 ) {
            return $value;
        }

        $excerpt_field    = wpbdp_get_form_fields( array( 'association' => 'excerpt') );
        $excerpt_val      = $excerpt_field ? $excerpt_field[0]->value( $post_id ) : NULL;
        $excerpt_override = $field->data( 'excerpt_override' );

        if ( 1 == $excerpt_override || ( 2 == $excerpt_override && empty( $excerpt_val ) ) ) {
            return wpautop( wp_html_excerpt( $field->value( $post_id ), $field->data( 'max_length' ), '...' ) );
        }

        return $value;
    }

    public function maybe_hide_excerpt_field( &$fields, $listing_id, $display ) {
        if ( 'excerpt' != $display ) {
            return $fields;
        }

		$content_field = array_filter(
			$fields,
			function( $f ) {
				return 'content' === $f->get_association();
			}
		);

        $content_field = ! empty( $content_field ) ? $content_field : wpbdp_get_form_fields( array( 'association' => 'content') );

        if ( empty( $content_field ) ) {
            return $fields;
        }

        $content_field    = is_array( $content_field ) ? array_shift( $content_field ) : $content_field;
        $excerpt_override = $content_field->data( 'excerpt_override' );

        if ( ! in_array( $excerpt_override, array( 1, 2 ) ) ) {
            return $fields;
        }

        foreach ( $fields as $k => $f ) {
            if ( 'excerpt' == $f->get_association() ) {
                if ( 1 == $excerpt_override || ( 2 == $excerpt_override && empty( $f->value( $listing_id ) ) ) ) {
					unset( $fields[ $k ] );
                }
            }
        }

        return $fields;
    }

}

