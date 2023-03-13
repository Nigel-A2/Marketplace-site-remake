<?php
/**
 * Listing Contact Form template
 *
 * @package BDP/Templates/Listing Contact Form
 */

if ( $validation_errors ) :
?>
<div class="wpbdp-msg wpbdp-error">
    <ul>
        <?php foreach ( $validation_errors as $error_msg ) : ?>
            <li>
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $error_msg;
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo esc_url( wpbdp_url( 'listing_contact', $listing_id ) ); ?>">
    <?php wp_nonce_field( 'contact-form-' . $listing_id ); ?>

	<div class="wpbdp-grid">
    <?php if ( ! $current_user ) : ?>
        <p class="wpbdp-form-field wpbdp-half">
            <label for="wpbdp-contact-form-name"><?php esc_html_e( 'Name', 'business-directory-plugin' ); ?></label> <input id="wpbdp-contact-form-name" type="text" class="intextbox" name="commentauthorname" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 'commentauthorname' ), 'post' ) ); ?>" required />
        </p>
        <p class="wpbdp-form-field wpbdp-half">
            <label for="wpbdp-contact-form-email"><?php esc_html_e( 'Email', 'business-directory-plugin' ); ?></label> <input id="wpbdp-contact-form-email" type="text" class="intextbox" name="commentauthoremail" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 'commentauthoremail' ), 'post' ) ); ?>" required />
		</p>
    <?php endif; ?>

    <p class="wpbdp-form-field">
        <label for="wpbdp-contact-form-phone"><?php esc_html_e( 'Phone Number', 'business-directory-plugin' ); ?></label> <input id="wpbdp-contact-form-phone" type="tel" class="intextbox" name="commentauthorphone" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 'commentauthorphone' ), 'post' ) ); ?>" />
    </p>

    <p class="wpbdp-form-field">
        <label for="wpbdp-contact-form-message"><?php esc_html_e( 'Message', 'business-directory-plugin' ); ?></label> <textarea id="wpbdp-contact-form-message" name="commentauthormessage" rows="4" class="intextarea"><?php echo esc_textarea( wpbdp_get_var( array( 'param' => 'commentauthormessage', 'sanitize' => 'sanitize_textarea_field' ), 'post' ) ); ?></textarea>
    </p>

    <?php do_action( 'wpbdp_contact_form_extra_fields' ); ?>
	</div>

    <?php
	if ( $recaptcha ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $recaptcha;
	}
	?>

    <input type="submit" class="wpbdp-button button wpbdp-submit submit" value="<?php esc_attr_e( 'Send', 'business-directory-plugin' ); ?>" />
</form>
