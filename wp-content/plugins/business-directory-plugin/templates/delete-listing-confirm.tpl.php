<div id="wpbdp-delete-listing-page">

<h3><?php esc_html_e( 'Delete Listing', 'business-directory-plugin' ); ?></h3>

<?php if ( $has_recurring ) : ?>
<div class="wpbdp-msg error">
<?php esc_html_e( 'Your listing is associated to a recurring payment. If you don\'t cancel the recurring payment before deleting the listing, you might be charged for additional periods even though your listing won\'t be available.', 'business-directory-plugin' ); ?>
<br />
<b><?php
echo str_replace(
	'<a>',
	'<a href="' . esc_url( add_query_arg( 'wpbdp_view', 'manage_recurring', wpbdp_get_page_link( 'main' ) ) ) . '">',
	_x( 'Please visit <a>Manage recurring payments</a> to review your current recurring payments.', 'delete listing', 'business-directory-plugin' )
);
?></b>
</div>
<?php endif; ?>

<form class="confirm-form" action="" method="post">
<p>
<?php printf( _x( 'You are about to remove your listing "%s" from the directory.', 'delete listing', 'business-directory-plugin' ), $listing->get_title() ); ?><br />
<b><?php esc_html_e( 'Are you sure you want to do this?', 'business-directory-plugin' ); ?></b>
</p>

<?php wp_nonce_field( 'delete listing ' . $listing->get_id() ); ?>

<input class="delete-listing-confirm wpbdp-submit button wpbdp-button" type="submit" value="<?php esc_attr_e( 'Yes. Delete my listing.', 'business-directory-plugin' ); ?>" />
<a href="<?php echo esc_url( wpbdp_get_page_link( 'main' ) ); ?>"><?php esc_html_e( 'No. Take me back to the directory.', 'business-directory-plugin' ); ?></a>
</form>

</div>
