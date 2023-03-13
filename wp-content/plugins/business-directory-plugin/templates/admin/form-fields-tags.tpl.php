<?php
function _fields_dropdown( $name, $field_id, $fixed = false ) {
	?>
    <select name="<?php echo $name; ?>" <?php echo ( $fixed ? 'disabled="disabled"' : '' ); ?> >
        <option value=""><?php _ex( '-- None --', 'form-fields admin', 'business-directory-plugin' ); ?></option>
        <?php foreach ( wpbdp_get_form_fields() as $f ) : ?>
            <option value="<?php echo $f->get_id(); ?>" <?php selected( $field_id, $f->get_id() ); ?> ><?php echo esc_attr( $f->get_label() ); ?></option>
        <?php endforeach; ?>
    </select>
	<?php
}
?>

<?php echo wpbdp_admin_header( _x( 'Theme Tags', 'form-fields admin', 'business-directory-plugin' ) ); ?>
<?php wpbdp_admin_notices(); ?>

<?php if ( $missing_fields ) : ?>
<div class="wpbdp-note">
<?php esc_html_e( 'Before you create fields, make sure you\'ve mapped all of your EXISTING ones first, otherwise you\'ll appear to be "missing data" on your listings.', 'business-directory-plugin' ); ?>
<br /><br />
<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpbdp-themes&wpbdp-action=create-theme-suggested-fields' ), 'create_suggested_fields' ); ?>" class="button"><?php _ex( 'Create Missing Fields', 'form-fields admin', 'business-directory-plugin' ); ?></a>
</div>
<?php endif; ?>

<form action="" method="post">
	<?php wp_nonce_field( 'fieldtags' ); ?>
    <table class="form-table">
        <tbody>
            <?php foreach ( $field_tags as $ft ) : ?>
            <tr>
                <th scope="row">
                    <?php echo $ft['description']; ?>
                </th>
                <td>
                    <?php _fields_dropdown( 'field_tags[' . $ft['tag'] . ']', $ft['field_id'], $ft['fixed'] ); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php submit_button(); ?>
</form>

<?php echo wpbdp_admin_footer(); ?>
