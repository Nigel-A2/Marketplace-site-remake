<?php
    _x( 'A listing has been reported as inappropriate. Listing details can be found below.', 'emails', 'business-directory-plugin' );
?>

----

<?php esc_html_e( 'Listing Information', 'business-directory-plugin' ); ?>:

<?php esc_html_e( 'ID', 'business-directory-plugin' ); ?>: <?php echo esc_html( $listing->get_id() ); ?>

<?php esc_html_e( 'Title', 'business-directory-plugin' ); ?>: <?php echo esc_html( $listing->get_title() ); ?>

<?php esc_html_e( 'URL', 'business-directory-plugin' ); ?>: <?php echo esc_html( $listing->is_published() ? $listing->get_permalink() : __( '(not published yet)', 'business-directory-plugin' ) ); ?>

<?php _ex( 'Admin URL', 'notify email', 'business-directory-plugin' ); ?>: <?php echo wpbdp_get_edit_post_link( $listing->get_id() ); ?>

<?php _ex( 'Categories', 'notify email', 'business-directory-plugin' ); ?>: <?php foreach ( $listing->get_categories() as $category ) : ?><?php echo $category->name; ?> / <?php endforeach; ?>

<?php _ex( 'Posted By', 'notify email', 'business-directory-plugin' ); ?>: <?php echo $listing->get_author_meta( 'user_login' ); ?> (<?php echo $listing->get_author_meta( 'user_email' ); ?>)

<?php _ex( 'Report Information', 'notify email', 'business-directory-plugin' ); ?>:

<?php if ( ! empty( $report['name'] ) ) : ?>
    <?php _ex( 'User name', 'notify email', 'business-directory-plugin' ); ?>: <?php echo $report['name'] ?>

<?php endif; ?>
<?php if ( ! empty( $report['email'] ) ) : ?>
    <?php _ex( 'User Email', 'notify email', 'business-directory-plugin' ); ?>: <?php echo $report['email'] ?>

<?php endif; ?>
<?php _ex( 'Report IP', 'notify email', 'business-directory-plugin' ); ?>: <?php echo $report['ip']; ?>

<?php _ex( 'Report selected option', 'notify email', 'business-directory-plugin' ); ?>: <?php echo $report['reason']; ?>

<?php echo isset( $report['comments'] ) && '' != $report['comments'] ? _x( 'Report additional info', 'notify email', 'business-directory-plugin' ) . ': ' . $report['comments'] : ''; ?>
