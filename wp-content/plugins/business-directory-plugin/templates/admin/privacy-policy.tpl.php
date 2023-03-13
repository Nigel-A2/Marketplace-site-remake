<?php
/**
 * Privacy Policy
 *
 * @package BDP/Templates/Admin/
 * @since 5.5
 */

?>
<div class="wp-suggested-text">
    <h3><?php esc_html_e( 'Business Directory Plugin', 'business-directory-plugin' ); ?></h3>
    <p><strong class="privacy-policy-tutorial"><?php esc_html_e( 'Suggested text:', 'business-directory-plugin' ); ?> </strong><?php esc_html_e( 'When you submit a directory listing, the content of the listing and its metadata are retained indefinitely. All users can see, edit or delete the personal information included on their listings at any time. Website administrators can also see and edit that information.', 'business-directory-plugin' ); ?></p>
    <p><?php esc_html_e( 'Website visitors can see the contact name, website URL, phone number, address and other information included in your submission to describe the directory listing.', 'business-directory-plugin' ); ?></p>
    <h4><?php esc_html_e( 'Payment Information', 'business-directory-plugin' ); ?></h4>
    <p>
    <?php
    $url = home_url();
    echo sprintf(
        // translators: %s is a link with the URL of the current site.
        esc_html__( 'If you pay to post a directory listing entering your credit card and billing information directly on %s, the credit card information won\'t be stored but it will be shared through a secure connection with the following payment gateways to process the payment:', 'business-directory-plugin' ),
        '<a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a>'
    )
    ?>
    </p>
    <ul>
        <li> PayPal &mdash; <a href="https://www.paypal.com/webapps/mpp/ua/privacy-full">https://www.paypal.com/webapps/mpp/ua/privacy-full</a></li>
        <li> Authorize.Net &mdash; <a href="https://www.authorize.net/company/privacy/">https://www.authorize.net/company/privacy/</a></li>
        <li> Stripe &mdash; <a href="https://stripe.com/us/privacy/">https://stripe.com/us/privacy/</a></li>
        <li> Payfast &mdash; <a href="https://www.payfast.co.za/privacy-policy/">https://www.payfast.co.za/privacy-policy/</a></li>
    </ul>
    <?php do_action( 'wpbdp_privacy_policy_content' ); ?>
</div>
