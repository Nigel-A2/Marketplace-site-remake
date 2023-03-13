<?php
/**
 * Personal Data Privacy
 *
 * @package BDP/Includes/Admin/Personal Data Privacy
 * @since 5.5
 */

require_once WPBDP_INC . 'admin/interface-personal-data-provider.php';
require_once WPBDP_INC . 'admin/helpers/class-data-formatter.php';
require_once WPBDP_INC . 'admin/class-personal-data-exporter.php';
require_once WPBDP_INC . 'admin/class-personal-data-eraser.php';
require_once WPBDP_INC . 'admin/class-listings-personal-data-provider.php';
require_once WPBDP_INC . 'admin/class-payment-personal-data-provider.php';

/**
 * Class WPBDP_Personal_Data_Privacy
 */
class WPBDP_Personal_Data_Privacy {

    /**
     * @var int
     */
    public $items_per_page = 10;

    /**
     * WPBDP_Personal_Data_Privacy constructor.
     */
    public function __construct() {
        add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
        add_action( 'wp_privacy_personal_data_exporters', array( $this, 'register_personal_data_exporters' ) );
        add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_personal_data_erasers' ) );
    }

    /**
     * Creates suggested policy content for Business Directory Plugin
     */
    public function add_privacy_policy_content() {
        if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
            return;
        }
        wp_add_privacy_policy_content( 'Business Directory Plugin', $this->get_privacy_policy_content() );
    }

    /**
     * @return string
     */
    private function get_privacy_policy_content() {
        $content = wpbdp_render_page( WPBDP_PATH . 'templates/admin/privacy-policy.tpl.php', array() );
        return wp_kses_post( $content );
    }

    /**
     * @param array $exporters
     * @return mixed
     */
    public function register_personal_data_exporters( $exporters ) {
        $data_formatter = new WPBDP_DataFormatter();

        $exporters['business-directory-plugin-listings'] = array(
            'exporter_friendly_name' => __( 'Business Directory Plugin', 'business-directory-plugin' ),
            'callback'               => array(
                new WPBDP_PersonalDataExporter(
                    new WPBDP_ListingsPersonalDataProvider(
                        $data_formatter
                    )
                ),
                'export_personal_data',
            ),
        );

        $exporters['business-directory-plugin-payments'] = array(
            'exporter_friendly_name' => __( 'Business Directory Plugin', 'business-directory-plugin' ),
            'callback'               => array(
                new WPBDP_PersonalDataExporter(
                    new WPBDP_PaymentPersonalDataProvider( $data_formatter )
                ),
                'export_personal_data',
            ),
        );

        return apply_filters( 'wpbdp_modules_personal_data_exporters', $exporters );

    }

    /**
     * @param array $erasers
     * @return array
     */
    public function register_personal_data_erasers( $erasers ) {
        $erasers['business-directory-plugin-listings'] = array(
            'eraser_friendly_name' => __( 'Business Directory Plugin', 'business-directory-plugin' ),
            'callback' => array(
                new WPBDP_PersonalDataEraser( $this->get_listings_personal_data_provider() ),
                'erase_personal_data',
            ),
        );
        $erasers['business-directory-plugin-payments'] = array(
            'eraser_friendly_name' => __( 'Business Directory Plugin', 'business-directory-plugin' ),
            'callback' => array(
                new WPBDP_PersonalDataEraser( $this->get_payment_personal_data_provider() ),
                'erase_personal_data',
            ),
        );
        return $erasers;
    }

    /**
     * @return WPBDP_ListingsPersonalDataProvider
     */
    public function get_listings_personal_data_provider() {
        static $instance;
        if ( is_null( $instance ) ) {
            $instance = new WPBDP_ListingsPersonalDataProvider( $this->get_data_formatter() );
        }
        return $instance;
    }

    /**
     * @return WPBDP_PaymentPersonalDataProvider
     */
    public function get_payment_personal_data_provider() {
        static $instance;
        if ( is_null( $instance ) ) {
            $instance = new WPBDP_PaymentPersonalDataProvider( $this->get_data_formatter() );
        }
        return $instance;
    }

    /**
     * @return WPBDP_DataFormatter
     */
    public function get_data_formatter() {
        static $instance;
        if ( is_null( $instance ) ) {
            $instance = new WPBDP_DataFormatter();
        }
        return $instance;
    }
}
