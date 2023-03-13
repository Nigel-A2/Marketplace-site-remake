<?php
/**
 * @deprecated 5.0  Try not to use this template. Redirect users to the login
 *                  view (?wpbdp_view=login) instead which is more convenient: it
 *                  automatically redirects user to the configured "Login URL" (if any),
 *                  or displays the login form and even handles access key access.
 */

_deprecated_file( esc_html( basename( __FILE__ ) ), '5.0', 'Redirect to ?wpbdp_view-login' );

$show_message = isset( $show_message ) ? $show_message : true;
?>

<div class="wpbdp-login-form">
<?php if ( $show_message ) : ?>
    <?php wpbdp_render_msg( esc_html__( "You are not currently logged in. Please login or register first. When registering, you will receive an activation email. Be sure to check your spam if you don't see it in your email within 60 minutes.", 'business-directory-plugin' ), 'status', true ); ?>
<?php endif; ?>

<h2><?php esc_html_e( 'Login', 'business-directory-plugin' ); ?></h2>
<?php wp_login_form(); ?>

<?php
$registration_url = trim( wpbdp_get_option( 'registration-url', '' ) );

if ( ! $registration_url && get_option( 'users_can_register' ) ) {
    if ( function_exists( 'wp_registration_url' ) ) {
        $registration_url = wp_registration_url();
    } else {
        $registration_url = site_url( 'wp-login.php?action=register', 'login' );
    }
}

$current_url = ( is_ssl() ? 'https://' : 'http://' ) . wpbdp_get_server_value( 'HTTP_HOST' ) . wpbdp_get_server_value( 'REQUEST_URI' );
$registration_url = $registration_url ? add_query_arg( array( 'redirect_to' => rawurlencode( $current_url ) ), $registration_url ) : '';
$lost_password_url = add_query_arg( 'redirect_to', rawurlencode( $current_url ), wp_lostpassword_url() );
?>

<p class="wpbdp-login-form-extra-links">
	<?php if ( $registration_url ) : ?>
    <a href="<?php echo esc_url( $registration_url ); ?>" rel="nofollow"><?php esc_html_e( 'Not yet registered?', 'business-directory-plugin' ); ?></a> |
    <?php endif; ?>
    <a href="<?php echo esc_url( $lost_password_url ); ?>" rel="nofollow"><?php esc_html_e( 'Lost your password?', 'business-directory-plugin' ); ?></a>
</p>
</div>
