<?php
$entries = WPBDP__Listing_Flagging::get_flagging_meta( $listing->get_id() );
$is_reported = WPBDP__Listing_Flagging::is_flagged( $listing->get_id() );
?>
<table class="widefat fixed" cellspacing="0">
    <tbody>
		<tr class="no-items" style="<?php echo ( $is_reported ? 'display : none;' : '' ); ?>">
            <td colspan="2"><?php echo _x( 'This listing has not been reported.', 'admin listings', 'business-directory-plugin' ); ?></td>
        </tr>
        <?php if ( $is_reported ) : ?>
            <?php
            foreach ( $entries as $key => $value ) :
                echo wpbdp_render_page(
                    WPBDP_PATH . 'templates/admin/metaboxes-listing-flagging-row.tpl.php',
                    array(
                        'listing'   => $listing,
                        'key'       => $key,
                        'value'     => $value,
                    )
                );
            endforeach;
            ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if ( $is_reported ) : ?>
<div class="wpbdp-remove-listing-reports">
    <a class="button button-small" href="<?php echo esc_url( add_query_arg( array( 'wpbdmaction' => 'delete-flagging', 'listing_id' => $listing->get_id(), 'meta_pos' => 'all' ) ) ); ?>">
        <?php echo _ex( 'Clear listing reports.', 'admin listings', 'business-directory-plugin' ); ?>
    </a>
</div>
<?php endif; ?>
