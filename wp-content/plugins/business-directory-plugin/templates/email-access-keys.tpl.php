<?php esc_html_e( 'Below you\'ll find the access keys for all the listings registered with your e-mail address on our site.', 'business-directory-plugin' ); ?>

<?php foreach ( $listings as $l ) : ?>
	<?php echo esc_html( $l->get_title() ); ?>
	<?php esc_html_e( 'Access Key', 'business-directory-plugin' ); ?>: <?php echo esc_html( $l->get_access_key() ); ?>
	<?php esc_html_e( 'URL', 'business-directory-plugin' ); ?>: <?php echo esc_html( $l->get_permalink() ); ?>

<?php endforeach; ?>

<?php echo esc_html( $site_title ); ?>
