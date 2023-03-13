<?php
    _ex( 'A new listing has been submitted to the directory. Listing details can be found below.', 'emails', 'business-directory-plugin' );
?>

----

<?php esc_html_e( 'ID', 'business-directory-plugin' ); ?>: <?php echo esc_html( $listing->get_id() ); ?>


<?php esc_html_e( 'Title', 'business-directory-plugin' ); ?>: <?php echo esc_html( $listing->get_title() ); ?>


<?php esc_html_e( 'URL', 'business-directory-plugin' ); ?>: <?php echo esc_html( $listing->is_published() ? $listing->get_permalink() : get_preview_post_link( $listing->get_id() ) ); ?>

<?php _ex( 'Admin URL', 'notify email', 'business-directory-plugin' ); ?>: <?php echo wpbdp_get_edit_post_link( $listing->get_id() ); ?>

<?php $categories = array();
foreach ( $listing->get_categories() as $category ) :
    $categories[] = $category->name;
endforeach; ?>
<?php echo esc_html( _n( 'Category', 'Categories', count( $listing->get_categories() ), 'business-directory-plugin' ) ); ?>: <?php echo esc_html( implode( ' / ', $categories ) ); ?>


<?php
$name = $listing->get_author_meta( 'user_login' );
$email = $listing->get_author_meta( 'user_email' );
$author_text = _x( 'Posted By', 'notify email', 'business-directory-plugin' ) . ': ';

if ( $name && $email ) :
    echo $author_text . $name . ' &lt;' . $email . '&gt;';
elseif ( $name ) :
    echo $author_text . $name;
elseif ( $email ) :
    echo $author_text . '&lt;' . $email . '&gt;';
else :
    echo $author_text . _x( 'Annonymous User', 'notify email', 'business-directory-plugin' );
endif;
?>
