<?php
/**
 * Login form template
 *
 * @package BDP/Templates/Login
 */

$show_message = isset( $show_message ) ? $show_message : true;

$registration_url = trim( wpbdp_get_option( 'registration-url', '' ) );

if ( ! $registration_url && get_option( 'users_can_register' ) ) {
    if ( function_exists( 'wp_registration_url' ) ) {
        $registration_url = wp_registration_url();
    } else {
        $registration_url = site_url( 'wp-login.php?action=register', 'login' );
    }
}

$registration_url  = $registration_url ? add_query_arg( array( 'redirect_to' => rawurlencode( $redirect_to ) ), $registration_url ) : '';
$lost_password_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), wp_lostpassword_url() );

$login_args             = isset( $login_args ) ? $login_args : array();
$login_args['redirect'] = $redirect_to;

// Allow login page protection from other plugins.
do_action( 'login_enqueue_scripts' );
?>

<div id="wpbdp-login-view">

<?php if ( $show_message ) : ?>
    <?php wpbdp_render_msg( esc_html__( "You are not currently logged in. Please login or register first. When registering, you will receive an activation email. Be sure to check your spam if you don't see it in your email within 60 minutes.", 'business-directory-plugin' ), 'status', true ); ?>
<?php endif; ?>

    <div class="wpbdp-login-options <?php echo $access_key_enabled ? 'options-2' : 'options-1'; ?>">

        <div id="wpbdp-login-form" class="wpbdp-login-option">
            <h4><?php esc_html_e( 'Login', 'business-directory-plugin' ); ?></h4>
            <?php wp_login_form( $login_args ); ?>

            <p class="wpbdp-login-form-extra-links">
                <?php if ( $registration_url ) : ?>
                <a class="register-link" href="<?php echo esc_url( $registration_url ); ?>" rel="nofollow"><?php esc_html_e( 'Not yet registered?', 'business-directory-plugin' ); ?></a> |
                <?php endif; ?>
                <a href="<?php echo esc_url( $lost_password_url ); ?>" rel="nofollow"><?php esc_html_e( 'Lost your password?', 'business-directory-plugin' ); ?></a>
            </p>
        </div>

        <?php if ( $access_key_enabled ) : ?>
            <div id="wpbdp-login-access-key-form" class="wpbdp-login-option">
                <h4><?php esc_html_e( '... or use an Access Key', 'business-directory-plugin' ); ?></h4>
                <?php if ( $errors ) : ?>
                    <div class="wpbdp-submit-listing-section-messages">
                    <?php foreach ( $errors as $error ) : ?>
                        <div class="wpbdp-msg error"><?php echo esc_html( $error ); ?></div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <p class="access-key-message"><?php esc_html_e( 'Please enter your access key and email address.', 'business-directory-plugin' ); ?></p>

                <form action="" method="post">
                    <input type="hidden" name="method" value="access_key" />
                    <p>
                        <label for="wpbdp-access-key-email">
                            <?php esc_html_e( 'Email Address', 'business-directory-plugin' ); ?>
                        </label>
                        <input id="wpbdp-access-key-email" type="text" name="email" value="" placeholder="<?php esc_attr_e( 'Email Address', 'business-directory-plugin' ); ?>" required/>
                    </p>
                    <p>
                        <label for="wpbdp-access-key-value">
                            <?php esc_html_e( 'Access Key', 'business-directory-plugin' ); ?>
                        </label>
                        <input id="wpbdp-access-key-value" type="text" name="access_key" value="" placeholder="<?php esc_attr_e( 'Access Key', 'business-directory-plugin' ); ?>" required/>
                    </p>
                    <p><input type="submit" value="<?php esc_attr_e( 'Use Access Key', 'business-directory-plugin' ); ?>" /></p>
                    <p><a href="<?php echo esc_url( $request_access_key_url ); ?>"><?php esc_html_e( 'Request access key?', 'business-directory-plugin' ); ?></a></p>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
