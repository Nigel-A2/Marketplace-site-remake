<?php
if ( ! defined( 'WPBDP_VERSION' ) ) die; // This page should not be called directly.

/**
 * @package admin
 */

if ( ! class_exists( 'WPBDP_SiteTracking' ) ) {

/**
 * Class used for anonymously tracking of users setups.
 *
 * @since 3.2
 */
class WPBDP_SiteTracking {

    const TRACKING_URL = 'https://data.businessdirectoryplugin.com/tr/';

    public function __construct() {
        if ( ! wpbdp_get_option( 'tracking-on', false ) )
            return;

		if ( ! wp_next_scheduled( 'wpbdp_site_tracking' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'wpbdp_site_tracking' );
        }

        add_action( 'wpbdp_site_tracking', array( $this, 'tracking' ) );
        // do_action( 'wpbdp_site_tracking' );
    }

    public function site_hash() {
        $hash = get_option( 'wpbdp-site_tracking_hash', '' );

		if ( ! $hash ) {
            $hash = sha1( site_url() );
			update_option( 'wpbdp-site_tracking_hash', $hash, 'no' );
        }

        return $hash;
    }

    public function tracking() {
        global $wpdb;

        wpbdp_log( 'Performing (scheduled) site tracking.' );

        $site_hash = $this->site_hash();

            wpbdp_log( 'Gathering site tracking metrics.' );

            $data = array();

            // General site info.
            $data['hash'] = $site_hash;
            $data['site-info'] = array(
                'title' => get_bloginfo( 'name' ),
                'wp-version' => get_bloginfo( 'version' ),
                'bd-version' => WPBDP_VERSION,
                /*'url' => site_url()*/
                'lang' => get_locale(),
                'users' => count( get_users() )
            );

            // Plugins info.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/admin.php';
			}

            $data['payments'] = $this->add_payment_info();

            $data['plugins'] = array();
            foreach ( get_option( 'active_plugins' ) as $path ) {
                $plugin = get_plugin_data( WP_PLUGIN_DIR . '/' . $path );

                $data['plugins'][] = array(
					'id'      => str_replace( '/' . basename( $path ), '', $path ),
					'name'       => wpbdp_getv( $plugin, 'Name', '' ),
                    'version' => wpbdp_getv( $plugin, 'Version', '' ),
                    'plugin_uri' => wpbdp_getv( $plugin, 'PluginURI', '' ),
                    'author' => wpbdp_getv( $plugin, 'AuthorName', '' ),
                    'author_uri' => wpbdp_getv( $plugin, 'AuthorURI', '' )
                );
            }

            // Theme info.
            $data['theme'] = array();

                $theme = wp_get_theme();

                foreach ( array( 'Name', 'ThemeURI', 'Version', 'Author', 'AuthorURI' ) as $k ) {
                    $data['theme'][ strtolower( $k ) ] = $theme->display( $k, false, false );
                }

                $data['theme']['parent'] = array();
                if ( $theme_parent = $theme->parent() ) {
                    foreach ( array( 'Name', 'ThemeURI', 'Version', 'Author', 'AuthorURI' ) as $k ) {
                        $data['theme']['parent'][ strtolower( $k ) ] = $theme_parent->display( $k, false, false );
                    }
                } else {
                    $data['theme']['parent'] = null;
                }

            // Posts.
            $data['posts'] = array();

            foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
                $count = wp_count_posts( $post_type );
                $data['posts'][ $post_type ] = intval( $count->publish );
            }

            // Taxonomies.
            $data['taxonomies']  = array();

            foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
                $data['taxonomies'][ $tax->name ] = array(
                    'name' => $tax->name,
                    'label' => $tax->label,
                    'terms' => intval( wp_count_terms( $tax->name, array( 'hide_empty' => 0 ) ) )
                );
            }

            // Environment.
            $data['environment'] = array();
            $data['environment']['os'] = php_uname( 's' ) . ' ' . php_uname( 'r' ) . ' ' . php_uname( 'm' );
            $data['environment']['php'] = phpversion();
            $data['environment']['mysql'] = $wpdb->get_var( 'SELECT @@version' );
            $data['environment']['server-software'] = wpbdp_get_server_value( 'SERVER_SOFTWARE' );

			wp_remote_post(
				self::TRACKING_URL,
				array(
					'method'   => 'POST',
					'blocking' => false,
					'body'     => $data
				)
			);
    }

	/**
	 * @since 6.2.8
	 */
	private function add_payment_info() {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT gateway, SUM(amount) as amount, currency_code
			FROM {$wpdb->prefix}wpbdp_payments
			WHERE gateway_tx_id IS NOT NULL AND status = 'completed' AND is_test != 1
			GROUP BY currency_code, gateway",
			ARRAY_A
		);
	}

    /**
     * @since 3.5.2
     */
    public function track_uninstall( $data = array() ) {
        $data = is_array( $data ) ? $data : null;
        $hash = $this->site_hash();

        if ( ! isset( $data['reason_id'] ) )
            return;

        $reason = $data['reason_id'];
        $text = isset( $data['reason_text'] ) ? trim( $data['reason_text'] ) : '';

        if ( $reason < 0 || $reason > 4 )
            return;

		wp_remote_post(
			self::TRACKING_URL,
			array(
				'method'   => 'POST',
				'blocking' => true,
				'body'     => array(
					'uninstall' => '1',
					'hash'      => $hash,
					'reason'    => $reason,
					'text'      => $text,
				)
			)
		);
    }

	public static function handle_ajax_response() {
		if ( ! wp_verify_nonce( wpbdp_get_var( array( 'param' => 'nonce' ), 'post' ), 'wpbdp-set_site_tracking' ) ) {
			exit();
		}

		$params = array(
			'param'    => 'enable_tracking',
			'sanitize' => 'intval',
			'default'  => null,
		);
		$tracking = wpbdp_get_var( $params, 'post' );
		if ( $tracking !== null ) {
			update_option( 'wpbdp-show-tracking-pointer', 0, 'no' );

			if ( $tracking ) {
				wpbdp_set_option( 'tracking-on', true );
			}
		}
	}

    public static function request_js() {
        $content  = '';
        $content .= '<h3>' . _x( 'Help Improve Business Directory', 'tracking', 'business-directory-plugin' ) . '</h3>';
        $content .= '<p>';
        $content .= _x( 'Can Business Directory keep track of your theme, plugins, and other non-personal, non-identifying information to help us in testing the plugin for future releases?', 'tracking', 'business-directory-plugin' );
        $content .= '<br />';
        $content .= '&#149; ' . sprintf( '<a href="%s" target="_blank" rel="noopener">%s</a>', 'http://businessdirectoryplugin.com/what-we-track', _x( 'What do you track?', 'tracking', 'business-directory-plugin' ) );
        $content .= '</p>';
?>
    <script>
        //<![CDATA[
        jQuery(function($){
            function WPBDP_SiteTracking_answer(enable) {
                var args = {
                    action: "wpbdp-set_site_tracking",
                    enable_tracking: enable ? 1 : 0,
                    nonce: "<?php echo esc_attr( wp_create_nonce( 'wpbdp-set_site_tracking' ) ); ?>"
                };

                $.post(ajaxurl, args, function() {
                    $('#wp-pointer-0').remove();
                });
            }

            $('#wpadminbar').pointer({
                'content': <?php echo json_encode( $content ); ?>,
                'position': { 'edge': 'top', 'align': 'center' },
                'buttons': function(event, t) {
                    var do_not_track = $('<a id="wpbdp-pointer-b2" class="button" style="margin-right: 5px;"><?php esc_html_e( 'No, thanks', 'business-directory-plugin' ); ?></a>');
                    do_not_track.bind('click.pointer', function() { t.element.pointer('close'); });

                    return do_not_track;
                }
            }).pointer('open');

            $('#wpbdp-pointer-b2').before('<a id="wpbdp-pointer-b1" class="button button-primary"><?php esc_html_e( 'Allow Tracking', 'business-directory-plugin' ); ?></a>');

            $('#wpbdp-pointer-b1').click(function(){
                WPBDP_SiteTracking_answer( true );
            });

            $('#wpbdp-pointer-b2').click(function(){
                WPBDP_SiteTracking_answer( false );
            });
        });
        //]]>
    </script>
<?php
    }

}

}
