<?php
/**
 * Database Helper
 *
 * Originally copied from Another WordPress Classifieds Plugin
 *
 * @link https://github.com/drodenbaugh/awpcp/blob/0fac103e4b6761860653677eef5d2825693c4ba9/another-wordpress-classifieds-plugin/includes/db/class-database-helper.php
 *
 * @since 4.1.8
 */

function wpbdp_database_helper() {
    return new WPBDP_Database_Helper( $GLOBALS['wpdb'] );
}

class WPBDP_Database_Helper {

    private $db;

    public function __construct( $db ) {
        $this->db = $db;
    }

    public function replace_charset_and_collate( $table_defintion ) {
        $table_defintion = str_replace( '<charset>', $this->get_charset(), $table_defintion );
        $table_defintion = str_replace( '<collate>', $this->get_collate(), $table_defintion );
        return $table_defintion;
    }

    public function get_charset() {
        if ( $this->db->charset === 'utf8mb4' && $this->db->has_cap( 'utf8mb4' ) ) {
            return 'utf8mb4';
        }

        return 'utf8';
    }

    public function get_collate() {
        $collate = '';

        if ( $this->db->charset === 'utf8mb4' && $this->db->has_cap( 'utf8mb4' ) ) {
            $collate = $this->db->collate;
        }

        if ( $this->db->charset === 'utf8' ) {
            $collate = $this->db->collate;
        }

        return $collate ? $collate : 'utf8_general_ci';
    }
}

