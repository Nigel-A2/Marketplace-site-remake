<?php
/**
 * Formats data from a list of properties in format expected by the Data Exporter API.
 *
 * @package BDP/Admin
 * @since 5.5
 */

/**
 * Class WPBDP_DataFormatter
 */
class WPBDP_DataFormatter {
    /**
     * @param array $items
     * @param array $properties
     * @return array
     */
    public function format_data( $items, $properties ) {
        $data = array();
        foreach ( $items as $key => $name ) {
            if ( empty( $properties[ $key ] ) ) {
                continue;
            }
            $data[] = array(
                'name'  => $name,
                'value' => $properties[ $key ],
            );
        }
        return $data;
    }
}
