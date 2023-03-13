<?php
/**
 * Exporter for Listings personal data.
 *
 * @package BDP/Admin
 * @since 5.5
 */

/**
 * Class WPBDP_ListingsPersonalDataProvider
 */
class WPBDP_ListingsPersonalDataProvider implements WPBDP_PersonalDataProviderInterface {

    /**
     * @var WPBDP_DataFormatter $data_formatter
     */
    private $data_formatter;

    /**
     * WPBDP_ListingsPersonalDataProvider constructor.
     */
    public function __construct( $data_formatter ) {
        $this->data_formatter = $data_formatter;
    }

    /**
     * @return int
     *
     * @since 5.4
     */
    public function get_page_size() {
        return 10;
    }

    /**
     * @param WP_User $user
     * @param string  $email_address
     * @param int     $page
     * @return array
     */
    public function get_objects( $user, $email_address, $page ) {
        $items_per_page = $this->get_page_size();
        return wpbdp_get_listings_by_email(
            $email_address,
            $items_per_page,
            ( $page - 1 ) * $items_per_page
        );
    }



    /**
     * @return array
     */
    private function get_privacy_fields_items() {
        $default_tags = WPBDP_Form_Field::$default_tags;

        $items = array( 'ID' => __( 'Listing ID', 'business-directory-plugin' ) );

        foreach ( $default_tags as $tag ) {
            $items[ $tag ] = WPBDP_Form_Field::find_by_tag( $tag )->get_label();
        }

        $privacy_items = array();

        foreach ( wpbdp_get_form_fields( array( 'display_flags' => 'privacy' ) ) as $field ) {
            $tag = $field->get_tag();
            $privacy_items[ $tag ? $tag : $field->get_short_name() ] = $field->get_label();
        }

        return array_merge( $items, $privacy_items );
    }

    /**
     * @param int $listing_id
     * @return mixed
     */
    private function get_listing_properties( $listing_id ) {
        $default_tags = WPBDP_Form_Field::$default_tags;

        $properties = array( 'ID' => $listing_id );

        foreach ( $default_tags as $tag ) {
            $properties[ $tag ] = WPBDP_Form_Field::find_by_tag( $tag )->plain_value( $listing_id );
        }

        $data   = array();
        $fields = wpbdp_get_form_fields( array( 'display_flags' => 'privacy' ) );

        foreach ( $fields as $field ) {
            $tag = $field->get_tag();
            $data[ $tag ? $tag : $field->get_short_name() ] = $field->plain_value( $listing_id );

        }

        return array_merge( $properties, $data );
    }


    /**
     * @param array $listing_ids
     * @return array
     */
    public function export_objects( $listing_ids ) {
        // TODO: Let premium modules define additional properties.
        $items = $this->get_privacy_fields_items();

        $media_items = array(
            'URL' => __( 'Image URL', 'business-directory-plugin' ),
        );

        $export_items = array();

        foreach ( $listing_ids as $listing_id ) {
            $data = $this->data_formatter->format_data( $items, $this->get_listing_properties( $listing_id ) );

            foreach ( wpbdp_get_listing( $listing_id )->get_images( 'ids' ) as $image ) {
                $data = array_merge( $data, $this->data_formatter->format_data( $media_items, $this->get_media_properties( $image ) ) );
            }

            $export_items[] = array(
                'group_id'    => 'wpbdp-listings',
                'group_label' => __( 'Business Directory Listings', 'business-directory-plugin' ),
                'item_id'     => "wpbdp-listing-{$listing_id}",
                'data'        => apply_filters( 'wpbdp_export_listing_objects', $data, $listing_id, $this->data_formatter )
            );

        }

        return $export_items;
    }

    /**
     * @param int $media_id
     * @return array
     */
    private function get_media_properties( $media_id ) {
        return array(
            'URL' => wp_get_attachment_url( $media_id ),
        );
    }

    /**
     * @param array $listings
     * @return array
     */
    public function erase_objects( $listings ) {
        $items_removed  = false;
        $items_retained = false;
        $messages       = array();
        foreach ( $listings as $listing ) {
            if ( wp_delete_post( $listing ) ) {
                $items_removed = true;
                continue;
            }
            $items_retained = true;
            $message = __( 'An unknown error occurred while trying to delete information for listing {listing_id}.', 'business-directory-plugin' );
            $message = str_replace( '{listing_id}', $listing, $message );
            $messages[] = $message;
        }
        return compact( 'items_removed', 'items_retained', 'messages' );
    }
}
