<?php
$uid = ! empty( $uid ) ? $uid : uniqid( 'wpbdp-settings-email-' );

$editor_only = isset( $editor_only ) ? (bool) $editor_only : false;

$container_class = ! empty( $container_class ) ? $container_class : '';
$setting_name = ! empty( $setting_name ) ? $setting_name : '';
$email_subject = ! empty( $email_subject ) ? $email_subject : __( 'Untitled', 'business-directory-plugin' );
$email_body = ! empty( $email_body ) ? $email_body : '';
$email_body_display = strip_tags( $email_body );
if ( strlen( $email_body_display ) > 200 ) {
    $email_body_display = substr( $email_body_display, 0, 200 ) . '...';
}

$placeholders = ! empty( $placeholders ) ? $placeholders : array();
$before_container = ! empty( $before_container ) ? $before_container : '';
$after_container = ! empty( $after_container ) ? $after_container : '';
$before_preview = ! empty( $before_preview ) ? $before_preview : '';
$after_preview = ! empty( $after_preview ) ? $after_preview : '';
$extra_fields = ! empty( $extra_fields ) ? $extra_fields : '';

$before_buttons = isset( $before_buttons ) ? $before_buttons : '';
$after_buttons = isset( $after_buttons ) ? $after_buttons : '';
?>

<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_container;
?>
<div class="wpbdp-settings-email <?php echo esc_attr( $container_class ); ?>">
	<?php if ( ! $editor_only ) : ?>
    <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $before_preview;
    ?>
    <div class="wpbdp-settings-email-preview" title="<?php esc_attr_e( 'Click to edit email', 'business-directory-plugin' ); ?>">
        <a href="#" class="wpbdp-settings-email-edit-btn wpbdp-tag"><?php esc_html_e( 'Click to edit', 'business-directory-plugin' ); ?></a>
        <h4><?php echo esc_html( $email_subject ); ?></h4>
		<?php echo wp_kses_post( $email_body_display ); ?>
    </div>
    <?php
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $after_preview;
    ?>
    <?php endif; ?>

    <div class="wpbdp-settings-email-editor">
        <input type="hidden" value="<?php echo esc_attr( $email_subject ); ?>" class="stored-email-subject" />
        <input type="hidden" value="<?php echo esc_attr( $email_body ); ?>" class="stored-email-body" />

        <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label for="<?php echo esc_attr( $uid ); ?>-subject"><?php esc_html_e( 'Email Subject', 'business-directory-plugin' ); ?></label></th>
                <td>
                    <input name="<?php echo esc_attr( $setting_name ); ?>[subject]" value="<?php echo esc_attr( $email_subject ); ?>" type="text" value="<?php echo esc_attr( $email_subject ); ?>" id="<?php echo esc_attr( $uid ); ?>-subject" class="email-subject" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="<?php echo esc_attr( $uid ); ?>-body"><?php esc_html_e( 'Email Body', 'business-directory-plugin' ); ?></label></th>
                <td>
                    <textarea name="<?php echo esc_attr( $setting_name ); ?>[body]" id="<?php echo esc_attr( $uid ); ?>-body" class="email-body" placeholder="<?php esc_attr_e( 'Email body text', 'business-directory-plugin' ); ?>"><?php echo esc_textarea( $email_body ); ?></textarea>

					<?php if ( $placeholders ) : ?>
                    <div class="placeholders">
                        <p><?php esc_html_e( 'You can use the following placeholders:', 'business-directory-plugin' ); ?></p>

                        <?php
                        $added_sep = false;

						foreach ( $placeholders as $placeholder => $placeholder_data ) :
                            $description = is_array( $placeholder_data ) ? $placeholder_data[0] : $placeholder_data;
                            $is_core_placeholder = is_array( $placeholder_data ) && isset( $placeholder_data[2] ) && $placeholder_data[2];

							if ( $is_core_placeholder && ! $added_sep ) :
                        ?>
                            <div class="placeholder-separator"></div>
                        <?php
                                $added_sep = true;
                            endif;
                        ?>
                            <div class="placeholder" data-placeholder="<?php echo esc_attr( $placeholder ); ?>"><span class="placeholder-code">[<?php echo esc_html( $placeholder ); ?>]</span> - <span class="placeholder-description"><?php echo esc_html( $description ); ?></span></div>
                        <?php
                        endforeach;
                        ?>
                    </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $extra_fields;
            ?>
        </tbody>
        </table>

        <div class="buttons">
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $before_buttons;
            ?>
            <!-- <a href="#" class="button preview-email"><?php esc_attr_e( 'Preview email', 'business-directory-plugin' ); ?></a> -->
            <a href="#" class="button cancel"><?php esc_html_e( 'Cancel', 'business-directory-plugin' ); ?></a> 
            <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'business-directory-plugin' ); ?>" />
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $after_buttons;
            ?>
        </div>
    </div>
</div>
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $after_container;
?>
