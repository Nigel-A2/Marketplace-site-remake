<?php
/**
 * @package WPBDP/Templates/Admin/Form Fields Add or Edit.
 */

wpbdp_admin_header(
	array(
        'title' => esc_html__( 'Add Form Field', 'business-directory-plugin' ),
        'id' => 'field-form',
        'echo' => true,
    )
);

wpbdp_admin_notices();

?>

<form id="wpbdp-formfield-form" action="" method="post">
    <input type="hidden" name="field[id]" value="<?php echo esc_attr( $field->get_id() ); ?>" />
    <input type="hidden" name="field[tag]" value="<?php echo esc_attr( $field->get_tag() ); ?>" />
    <input type="hidden" name="field[weight]" value="<?php echo esc_attr( $field->get_weight() ); ?>" />
	<?php wp_nonce_field( 'editfield' ); ?>

    <table class="form-table">
        <tbody>
            <!-- association -->
            <tr>
                <th scope="row">
                    <label> <?php _ex( 'Field Association', 'form-fields admin', 'business-directory-plugin' ); ?> <span class="description">(<?php _ex( 'required', 'form-fields admin', 'business-directory-plugin' ); ?>)</span></label>
                </th>
                <td>
                    <?php $field_association_info = $field_associations[ $field->get_association() ]; ?>
                    <?php if ( in_array( 'private', $field_association_info->flags, true ) ) : ?>
                    <select name="field[association]" id="field-association">
                        <option value="<?php echo $field_association_info->id; ?>"><?php echo $field_association_info->label; ?></option>
                    </select>
                    <?php else : ?>
                    <select name="field[association]" id="field-association">
                    <?php foreach ( $field_associations as &$association ) : ?>
                        <?php
                        if ( in_array( 'private', $association->flags, true ) ) {
							continue;}
?>
                        <option value="<?php echo $association->id; ?>" <?php echo $field->get_association() == $association->id ? ' selected="selected"' : ''; ?> ><?php echo $association->label; ?></option>
                    <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- field type -->
            <tr class="form-field form-required">
                <th scope="row">
                    <label> <?php _ex( 'Field Type', 'form-fields admin', 'business-directory-plugin' ); ?> <span class="description">(<?php _ex( 'required', 'form-fields admin', 'business-directory-plugin' ); ?>)</span></label>
                </th>
                <td>
					<select name="field[field_type]" id="field-type">
						<?php if ( 'custom' === $field->get_association() ) : ?>
                        <option value="<?php echo $field->get_field_type_id(); ?>"><?php echo $field->get_field_type()->get_name(); ?></option>
						<?php else : ?>
                        <?php foreach ( $field_types as $key => &$field_type ) : ?>
                            <?php if ( ! in_array( $field->get_association(), $field_type->get_supported_associations() ) ) : ?>
                            <option value="<?php echo $key; ?>" disabled="disabled"><?php echo $field_type->get_name(); ?></option>
                            <?php else : ?>
                            <option value="<?php echo $key; ?>" <?php echo $field->get_field_type() == $field_type ? 'selected="true"' : ''; ?>><?php echo $field_type->get_name(); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
						<?php endif; ?>
					</select>
					<?php WPBDP_Admin_Education::show_tip( 'ratings' ); ?>
                </td>
            </tr>

            <!-- label -->
            <tr class="form-field form-required">
                <th scope="row">
                    <label> <?php _ex( 'Field Label', 'form-fields admin', 'business-directory-plugin' ); ?> <span class="description">(<?php _ex( 'required', 'form-fields admin', 'business-directory-plugin' ); ?>)</span></label>
                </th>
                <td>
                    <input name="field[label]" type="text" aria-required="true" value="<?php echo esc_attr( $field->get_label() ); ?>" />
                </td>
            </tr>

            <!-- description -->
            <tr class="form-field">
                <th scope="row">
                    <label> <?php _ex( 'Field description', 'form-fields admin', 'business-directory-plugin' ); ?> <span class="description">(<?php _ex( 'optional', 'form-fields admin', 'business-directory-plugin' ); ?>)</span></label>
                </th>
                <td>
                    <input name="field[description]" type="text" value="<?php echo esc_attr( $field->get_description() ); ?> " />
                </td>
            </tr>
    </table>

    <!-- field-specific settings -->
    <?php
    $field_settings = $field->get_field_type()->render_field_settings( $field, $field->get_association() );
    ob_start();
    do_action_ref_array( 'wpbdp_form_field_settings', array( &$field, $field->get_association() ) );
    $field_settings .= ob_get_contents();
    ob_end_clean();
    ?>
    <div id="wpbdp-fieldsettings" style="<?php echo $field_settings ? '' : 'display: none;'; ?>">
    <h2><?php _ex( 'Field-specific settings', 'form-fields admin', 'business-directory-plugin' ); ?></h2>
    <div id="wpbdp-fieldsettings-html">
        <?php echo $field_settings; ?>
    </div>
    </div>
    <!-- /field-specific settings -->

    <!-- validation -->
    <?php if ( ! $field->has_behavior_flag( 'no-validation' ) ) : ?>
    <h2><?php _ex( 'Field validation options', 'form-fields admin', 'business-directory-plugin' ); ?></h2>
    <table class="form-table">
            <tr>
                <th scope="row">
                    <label> <?php _ex( 'Field Validator', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <select name="field[validators][]" id="field-validator" <?php echo ( 'social-twitter' == $field->get_field_type_id() ? 'disabled="disabled"' : '' ); ?> ?>>
                        <option value=""><?php _ex( 'No validation', 'form-fields admin', 'business-directory-plugin' ); ?></option>
                        <?php foreach ( $validators as $key => $name ) : ?>
                        <?php
                        $disable_validator = ( 'url' == $field->get_field_type_id() && 'url' != $key ) ? true : false;
                        ?>
                        <option value="<?php echo $key; ?>" <?php echo in_array( $key, $field->get_validators(), true ) ? 'selected="selected"' : ''; ?> <?php echo $disable_validator ? 'disabled="disabled"' : ''; ?> ><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
			<tr id="wpbdp_word_count" style="<?php echo ( in_array( 'word_number', $field->get_validators() ) && in_array( $field->get_field_type()->get_id(), array( 'textfield', 'textarea' ) ) ) ? '' : 'display: none'; ?>">
                <th scope="row">
                    <label><?php esc_html_e( 'Maximum number of words', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input name="field[word_count]" value="<?php echo $field->data( 'word_count' ) ? $field->data( 'word_count' ) : 0; ?>" type="number">
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label> <?php _ex( 'Is field required?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input name="field[validators][]"
                               value="required"
                               type="checkbox" <?php echo $field->is_required() ? 'checked="checked"' : ''; ?>/> <?php _ex( 'This field is required.', 'form-fields admin', 'business-directory-plugin' ); ?>
                    </label>
                </td>
            </tr>
    </table>
    <?php endif; ?>

    <!-- display options -->
    <h2><?php _ex( 'Field display options', 'form-fields admin', 'business-directory-plugin' ); ?></h2>
    <table class="form-table">
		<tr class="form-field limit-categories <?php echo in_array( 'limit_categories', $hidden_fields ) ? 'wpbdp-hidden' : ''; ?>">
                <th scope="row">
                    <label for="wpbdp-field-category-policy"><?php _ex( 'Field Category Policy:', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <select id="wpbdp-field-category-policy"
                            name="limit_categories">
                        <option value="0"><?php _ex( 'Field applies to all categories', 'form-fields admin', 'business-directory-plugin' ); ?></option>
                        <option value="1" <?php selected( is_array( $field->data( 'supported_categories' ) ), true ); ?> ><?php _ex( 'Field applies to only certain categories', 'form-fields admin', 'business-directory-plugin' ); ?></option>
                    </select>

                    <div id="limit-categories-list" class="<?php echo is_array( $field->data( 'supported_categories' ) ) ? '' : 'hidden'; ?>">
                        <p><span class="description"><?php _ex( 'Limit field to the following categories:', 'form-fields admin', 'business-directory-plugin' ); ?></span></p>
                        <?php
                        $all_categories       = get_terms(
                            array(
								'taxonomy'     => WPBDP_CATEGORY_TAX,
								'hide_empty'   => false,
								'hierarchical' => true,
                            )
                        );
                        $supported_categories = is_array( $field->data( 'supported_categories' ) ) ? array_map( 'absint', $field->data( 'supported_categories' ) ) : array();

                        if ( count( $all_categories ) <= 30 ) :
                            foreach ( $all_categories as $category ) :
                                ?>
                                <div class="wpbdp-category-item">
                                    <label>
                                        <input type="checkbox" name="field[supported_categories][]" value="<?php echo $category->term_id; ?>" <?php checked( in_array( (int) $category->term_id, $supported_categories ) ); ?>>
                                        <?php echo esc_html( $category->name ); ?>
                                    </label>
                                </div>
                            <?php
                            endforeach;
                        else :
                            ?>
                            <select name="field[supported_categories][]" multiple="multiple" placeholder="<?php _ex( 'Click to add categories to the selection.', 'form-fields admin', 'business-directory-plugin' ); ?>">
                                <?php foreach ( $all_categories as $category ) : ?>
                                    <option value="<?php echo $category->term_id; ?>" <?php selected( in_array( (int) $category->term_id, $supported_categories ) ); ?>><?php echo esc_html( $category->name ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php
                        endif;
                        ?>
                    </div>
                </td>
            </tr>
			<tr id="wpbdp_private_field" class="<?php echo in_array( 'private_field', $hidden_fields, true ) ? 'wpbdp-hidden' : ''; ?>">
                <th scope="row">
                    <label> <?php _ex( 'Show this field to admin users only?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input name="field[display_flags][]"
                               value="private"
                               type="checkbox" <?php echo $field->display_in( 'private' ) ? 'checked="checked"' : ''; ?>/> <?php _ex( 'Display this field to admin users only in the edit listing view.', 'form-fields admin', 'business-directory-plugin' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label> <?php _ex( 'Show this value in excerpt view?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input name="field[display_flags][]"
                               value="excerpt"
                               type="checkbox" <?php echo $field->display_in( 'excerpt' ) ? 'checked="checked"' : ''; ?>/> <?php _ex( 'Display this value in post excerpt view.', 'form-fields admin', 'business-directory-plugin' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label> <?php _ex( 'Show this value in listing view?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input name="field[display_flags][]"
                               value="listing"
                               type="checkbox" <?php echo $field->display_in( 'listing' ) ? 'checked="checked"' : ''; ?>/> <?php _ex( 'Display this value in the listing view.', 'form-fields admin', 'business-directory-plugin' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label> <?php _ex( 'Include this field in the search form?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input name="field[display_flags][]"
                               value="search"
                               type="checkbox" <?php echo $field->display_in( 'search' ) ? 'checked="checked"' : ''; ?>/> <?php _ex( 'Include this field in the search form.', 'form-fields admin', 'business-directory-plugin' ); ?>
                    </label>
                </td>
            </tr>
			<tr class="if-display-in-search <?php echo in_array( 'search', $hidden_fields, true ) ? 'wpbdp-hidden' : ''; ?>">
                <th scope="row">
                    <label> <?php _ex( 'Is this field required for searching?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input name="field[validators][]"
                               value="required-in-search"
                               type="checkbox" <?php echo in_array( 'required-in-search', $field->get_validators() ) ? 'checked="checked"' : ''; ?>/>
						<?php esc_html_e( 'Require this field on the Advanced Search screen.', 'business-directory-plugin' ); ?>
                    </label>
                </td>
            </tr>
            <?php
			if ( has_action( 'wpbdp_admin_listing_field_section_visibility' ) ) {
				do_action( 'wpbdp_admin_listing_field_section_visibility', $field, $hidden_fields );
			} else {
				?>
				<tr class="<?php echo in_array( 'nolabel', $hidden_fields, true ) ? 'wpbdp-hidden' : ''; ?>">
					<th scope="row">
						<label> <?php _ex( 'Hide this field\'s label?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
					</th>
					<td>
						<label>
							<input name="field[display_flags][]"
								value="nolabel"
								type="checkbox" <?php echo $field->has_display_flag( 'nolabel' ) ? 'checked="checked"' : ''; ?>/> <?php _ex( 'Hide this field\'s label when displaying it.', 'form-fields admin', 'business-directory-plugin' ); ?>
						</label>
					</td>
				</tr>
			<?php } ?>
    </table>

    <!-- display options -->
    <h2><?php _ex( 'Field privacy options', 'form-fields admin', 'business-directory-plugin' ); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label> <?php _ex( 'This field contains sensitive or private information?', 'form-fields admin', 'business-directory-plugin' ); ?></label>
            </th>
            <td>
                <label>
                    <input name="field[display_flags][]"
                           value="privacy"
                           type="checkbox" <?php echo ( $field->is_privacy_field() || $field->has_display_flag( 'privacy' ) ) ? 'checked="checked"' : ''; ?>
                           <?php echo $field->is_privacy_field() ? 'disabled' : '' ?>
                    /> <?php _ex( 'Add this field when exporting or deleting user\'s personal data.', 'form-fields admin', 'business-directory-plugin' ); ?>
                </label>
            </td>
        </tr>
    </table>

	<?php do_action( 'wpbdp_admin_listing_field_after_settings', $field, $hidden_fields ); ?>

    <?php if ( $field->get_id() ) : ?>
        <?php echo submit_button( _x( 'Update Field', 'form-fields admin', 'business-directory-plugin' ) ); ?>
    <?php else : ?>
        <?php echo submit_button( _x( 'Add Field', 'form-fields admin', 'business-directory-plugin' ) ); ?>
    <?php endif; ?>
</form>

<script>
document.addEventListener( 'DOMContentLoaded', function () {
    WPBDP_associations_fieldtypes = <?php echo json_encode( $association_field_types ); ?>
}, false );
</script>

<?php echo wpbdp_admin_footer(); ?>
