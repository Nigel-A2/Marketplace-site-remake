<?php
$is_thumbnail = isset( $is_thumbnail ) ? $is_thumbnail : false;

if ( isset( $image ) && is_object( $image ) ) {
    $image_id = $image->id;
    $weight = $image->weight;
    $caption = $image->caption;
}

$delete_link = add_query_arg(
	array(
		'action'     => 'wpbdp-listing-submit-image-delete',
		'image_id'   => $image_id,
		'listing_id' => $listing_id,
	),
	admin_url( 'admin-ajax.php' )
);
$delete_link = wp_nonce_url( $delete_link, 'delete-listing-' . $listing_id . '-image-' . $image_id );
?>

<div class="wpbdp-image" data-imageid="<?php echo esc_attr( $image_id ); ?>">
	<span class="wpbdp-drag-handle"></span>
	<input type="hidden" name="images_meta[<?php echo esc_attr( $image_id ); ?>][order]" value="<?php echo esc_attr( isset( $weight ) ? $weight : 0 ); ?>" />

    <div class="wpbdp-image-img">
        <?php echo wp_get_attachment_image( $image_id, 'wpbdp-thumb' ); ?>
    </div>

    <div class="wpbdp-image-extra">
        <input type="text" name="images_meta[<?php echo esc_attr( $image_id ); ?>][caption]" value="<?php echo ( isset( $caption ) ? esc_attr( $caption ) : '' ); ?>" placeholder="<?php esc_attr_e( 'Image caption or description', 'business-directory-plugin' ); ?>" />
		<a href="<?php echo esc_url( $delete_link ); ?>" class="wpbdp-image-delete-link">
			<?php esc_html_e( 'Delete image', 'business-directory-plugin' ); ?>
		</a>
		<div style="clear:both"></div>
		<span class="wpbdp_thumbnail_indicator">
			<?php esc_html_e( 'Thumbnail image', 'business-directory-plugin' ); ?>
		</span>
    </div>
</div>
