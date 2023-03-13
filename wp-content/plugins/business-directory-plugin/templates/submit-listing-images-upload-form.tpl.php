<?php
$admin = isset( $admin ) ? $admin : false;
$listing_id = isset( $listing_id ) ? $listing_id : 0;

$action = add_query_arg(
    array(
        'action' => 'wpbdp-listing-submit-image-upload',
        'listing_id' => $listing_id
    ),
    admin_url( 'admin-ajax.php' )
);

$media_action = add_query_arg(
    array(
        'action' => 'wpbdp-listing-media-image',
        'listing_id' => $listing_id
    ),
    admin_url( 'admin-ajax.php' )
);
?>


<div class="image-upload-wrapper">
    <label class="image-upload-header" for="uploaded-images">
        <?php esc_html_e( 'Upload Images', 'business-directory-plugin' ); ?>
    </label>

    <?php if ( ! $admin ) : ?>
        <div id="image-upload-conditions">
            <span id="image-upload-general-conditions"><?php echo esc_html( implode( '; ', $conditions ) ); ?></span>
            <span id="image-slots-total" class="wpbdp-hidden"><?php echo esc_html( $slots ); ?></span>
            <span id="image-slots-available"><?php echo esc_html_x( 'Image slots available', 'templates', 'business-directory-plugin' ); ?>: <span id="image-slots-remaining"><?php echo esc_html( $slots_available ); ?></span></span>
        </div>
    <?php endif; ?>
    <?php if ( is_admin() && ! wpbdp_is_request( 'ajax' ) ) : ?>
        <div class="media-area-and-conditions cf">
            <div class="wpbdp_media_images_wrapper">
                <input type='button' class="button" value="<?php esc_attr_e( 'Select Media', 'business-directory-plugin' ); ?>" id="wpbdp_media_manager" data-action="<?php echo esc_url( wp_nonce_url( $media_action, 'listing-' . $listing_id . '-image-from-media' ) ); ?>" data-admin-nonce="<?php echo $admin ? '1' : ''; ?>"/>
            </div>
            <p><?php esc_html_e( 'or', 'business-directory-plugin' ); ?></p>
        </div>
    <?php endif; ?>
    <div class="area-and-conditions cf">
    <div id="image-upload-dnd-area" class="wpbdp-dnd-area <?php echo $admin ? 'no-conditions' : ''; ?>" data-action="<?php echo esc_url( wp_nonce_url( $action, 'listing-' . $listing_id . '-image-upload' ) ); ?>" data-admin-nonce="<?php echo $admin ? '1' : ''; ?>" >
            <div class="dnd-area-inside">
                <p class="dnd-message"><?php esc_html_e( 'Drop files here', 'business-directory-plugin' ); ?></p>
                <p><?php esc_html_e( 'or', 'business-directory-plugin' ); ?></p>
                <p class="dnd-buttons"><label for="uploaded-images" class="upload-button"><a><?php esc_html_e( 'Select images from your hard drive', 'business-directory-plugin' ); ?></a><input id="uploaded-images" type="file" name="images[]" multiple="multiple" /></label></p>
            </div>
            <div class="dnd-area-inside-working" style="display: none;">
                <p>
                <?php
                echo sprintf(
                    // translators: %s is the number of uploaded files.
                    esc_html__( 'Uploading %s file(s)... Please wait.', 'business-directory-plugin' ), '<span>0</span>' );
                ?>
                </p>
            </div>
            <div class="dnd-area-inside-error" style="display: none;">
                <p id="noslots-message" style="display: none;"><?php esc_html_e( 'Your image slots are all full. You may click "Delete Image" to upload a new image.', 'business-directory-plugin' ); ?></p>
            </div>
        </div>
    </div>
</div>
