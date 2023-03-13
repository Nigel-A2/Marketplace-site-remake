<?php
/**
 * Erases all personal data the plugin has for the given email address.
 *
 * @package BDP\Admin
 * @since 5.5
 */

/**
 * Class WPBDP_PersonalDataEraser
 */
class WPBDP_PersonalDataEraser {

    /**
     * @param object $data_eraser
     */
    public function __construct( $data_eraser ) {
        $this->data_eraser = $data_eraser;
    }

    /**
     * @param string $email_address
     * @param int    $page
     * @return array
     */
    public function erase_personal_data( $email_address, $page = 1 ) {
        $user    = get_user_by( 'email', $email_address );
        $objects = $this->data_eraser->get_objects( $user, $email_address, $page );
        $result  = $this->erase_objects( $objects );
        return array(
            'items_removed'  => $result['items_removed'],
            'items_retained' => $result['items_retained'],
            'messages'       => $result['messages'],
            'done'           => count( $objects ) < $this->data_eraser->get_page_size(),
        );
    }

    /**
     * @return array
     */
    private function erase_objects( $objects ) {
        if ( empty( $objects ) ) {
            return array(
                'items_removed'  => false,
                'items_retained' => false,
                'messages'       => array(),
            );
        }
        return $this->data_eraser->erase_objects( $objects );
    }
}
