<?php
/**
 * Class WPBDP_Admin_Listing_Fields_Metabox
 *
 * @package BDP/Includes/Admin
 */

/**
 * Class WPBDP_Admin_Listing_Fields_Metabox
 */
class WPBDP_Admin_Listing_Fields_Metabox {
    private $listing = null;

    public function __construct( &$listing ) {
        $this->listing = $listing;
    }

    public function render() {
        $image_count = count( $this->listing->get_images( 'ids' ) );

        echo '<div id="wpbdp-submit-listing">';

        echo '<ul class="wpbdp-admin-tab-nav subsubsub">';
        echo '<li><a href="#wpbdp-listing-fields-fields">' . _x( 'Fields', 'admin', 'business-directory-plugin' ) . '</a> | </li>';
        echo '<li><a href="#wpbdp-listing-fields-images">';
        echo '<span class="with-image-count ' . ( $image_count > 0 ? '' : ' hidden' ) . '">' . sprintf( _x( 'Images (%s)', 'admin', 'business-directory-plugin' ), '<span>' . $image_count . '</span>' ) . '</span>';
        echo '<span class="no-image-count' . ( $image_count > 0 ? ' hidden' : '' ) . '">' . _x( 'Images', 'admin', 'business-directory-plugin' ) . '</span>';
        echo '</a></li>';
        echo '</ul>';

		echo '<div id="wpbdp-listing-fields-fields" class="wpbdp-admin-tab-content wpbdp-grid" tabindex="1">';
        $this->listing_fields();
        echo '</div>';

        echo '<div id="wpbdp-listing-fields-images" class="wpbdp-admin-tab-content" tabindex="2">';
        $this->listing_images();
        echo '</div>';

        echo '</div>';
    }

    private function listing_fields() {
        foreach ( wpbdp_get_form_fields( array( 'association' => 'meta' ) ) as $field ) {
            $value = $field->value( $this->listing->get_id() );

            if ( ! empty( $_POST['listingfields'][ $field->get_id() ] ) ) {
				$value = $field->value_from_POST();
            }

            $args = array( 'listing_id' => $this->listing->get_id() );
            echo $field->render( $value, 'admin-submit', $args );
        }

        wp_nonce_field( 'save listing fields', 'wpbdp-admin-listing-fields-nonce', false );
    }

    private function listing_images() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        $images       = $this->listing->get_images( 'all', true );
        $thumbnail_id = $this->listing->get_thumbnail_id();

        echo '<div class="wpbdp-submit-listing-section-listing_images">';
        echo wpbdp_render(
            'submit-listing-images',
            array(
				'admin'        => true,
				'thumbnail_id' => $thumbnail_id,
				'listing'      => $this->listing,
				'images'       => $images,
            )
        );
        echo '</div>';
    }

    public static function metabox_callback( $post ) {
        $listing = WPBDP_Listing::get( $post->ID );

        if ( ! $listing ) {
            return '';
        }

        $instance = new self( $listing );
        return $instance->render();
    }
}

