<?php
/**
 * Represents a single field from the database. This class can not be instantiated directly.
 *
 * @package WPBDP/Views/Includes/Admin/Form Fields
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class WPBDP_FormFieldsTable
 */
class WPBDP_FormFieldsTable extends WP_List_Table {

    public function __construct() {
        parent::__construct(
            array(
                'singular' => _x( 'form field', 'form-fields admin', 'business-directory-plugin' ),
                'plural'   => _x( 'form fields', 'form-fields admin', 'business-directory-plugin' ),
                'ajax'     => false,
            )
        );
    }

    public function get_columns() {
        return array(
            'order'     => _x( 'Order', 'form-fields admin', 'business-directory-plugin' ),
            'label'     => _x( 'Label / Association', 'form-fields admin', 'business-directory-plugin' ),
            'type'      => __( 'Type', 'business-directory-plugin' ),
            'validator' => _x( 'Validator', 'form-fields admin', 'business-directory-plugin' ),
            'tags'      => _x( 'Field Attributes', 'form-fields admin', 'business-directory-plugin' ),
        );
    }

    public function prepare_items() {
        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

        $formfields_api = WPBDP_FormFields::instance();
        $this->items    = $formfields_api->get_fields();
    }

    /* Rows */
    public function column_order( $field ) {
        $form_fields_url = admin_url( 'admin.php?page=wpbdp_admin_formfields' );
        return sprintf(
            '<span class="wpbdp-drag-handle" data-field-id="%s"></span> <a href="%s"><strong>↑</strong></a> | <a href="%s"><strong>↓</strong></a>',
            $field->get_id(),
			wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'fieldup',
                        'id'     => $field->get_id(),
                    ),
                    $form_fields_url
				),
				'movefield'
            ),
			wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'fielddown',
                        'id'     => $field->get_id(),
                    ),
                    $form_fields_url
				),
				'movefield'
            )
        );
    }

    public function column_label( $field ) {
        $form_fields_url = admin_url( 'admin.php?page=wpbdp_admin_formfields' );
        $actions         = array();
        $actions['edit'] = sprintf(
            '<a href="%s">%s</a>',
			wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'editfield',
                        'id'     => $field->get_id(),
                    ),
                    $form_fields_url
				),
				'editfield'
            ),
			esc_html__( 'Edit', 'business-directory-plugin' )
        );

        if ( ! $field->has_behavior_flag( 'no-delete' ) ) {
            $actions['delete'] = sprintf(
				'<a href="%1$s" data-bdconfirm="%2$s">%3$s</a>',
				wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'deletefield',
                            'id'     => $field->get_id(),
                        ),
                        $form_fields_url
					),
					'deletefield'
                ),
				esc_attr__( 'Are you sure you want to delete that field?', 'business-directory-plugin' ),
                esc_html__( 'Delete', 'business-directory-plugin' )
            );
        }

        $html  = '';
        $html .= sprintf(
            '<strong><a href="%s">%s</a></strong> (as <i>%s</i>)',
			wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'editfield',
                        'id'     => $field->get_id(),
                    ),
                    $form_fields_url
				),
				'editfield'
            ),
            esc_attr( $field->get_label() ),
            $field->get_association()
        );
		$html .= $field->is_required() ? ' *' : '';
        $html .= '<br/>';
        $html .= sprintf(
            '%s: %d',
            __( 'ID', 'business-directory-plugin' ),
            $field->get_id()
        );
        $html .= '<br/>';
        $html .= sprintf(
            '%s: %s',
            _x( 'Shortname', 'form-fields admin', 'business-directory-plugin' ),
            $field->get_shortname()
        );
        $html .= $this->row_actions( $actions );

        return $html;
    }

    public function column_type( $field ) {
        return esc_html( $field->get_field_type()->get_name() );
    }

    public function column_validator( $field ) {
        return esc_html( implode( ',', $field->get_validators() ) );
    }

    public function column_tags( $field ) {
        $html = '';

        if ( $field->has_display_flag( 'private' ) ) {
            $html .= sprintf(
                '<span class="tag %s">%s</span>',
                'private',
                _x( 'Private', 'form-fields admin', 'business-directory-plugin' )
            );
        }

        if ( $field->display_in( 'excerpt' ) ) {
            $html .= sprintf(
                '<span class="tag in-excerpt" title="%s">%s</span>',
                _x( 'This field value is shown in the excerpt view of a listing.', 'form-fields admin', 'business-directory-plugin' ),
                _x( 'In Excerpt', 'form-fields admin', 'business-directory-plugin' )
            );
        }

        if ( $field->display_in( 'listing' ) ) {
            $html .= sprintf(
                '<span class="tag in-listing" title="%s">%s</span>',
                _x( 'This field value is shown in the single view of a listing.', 'form-fields admin', 'business-directory-plugin' ),
                _x( 'In Listing', 'form-fields admin', 'business-directory-plugin' )
            );
        }

        return $html;
    }

}
