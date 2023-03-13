<?php
/**
 * HivePress component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;
use HivePress\Helpers as hp;
use HivePress\Blocks;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * HivePress component class.
 *
 * @class HivePress
 */
final class HivePress extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Check HivePress status.
		if ( ! ht\is_plugin_active( 'hivepress' ) ) {
			return;
		}

		if ( is_admin() ) {

			// Add admin notices.
			add_filter( 'hivepress/v1/admin_notices', [ $this, 'add_admin_notices' ] );
		} else {

			// Render site header.
			add_filter( 'hivetheme/v1/areas/site_header', [ $this, 'render_site_header' ] );

			if ( ht\is_plugin_active( 'woocommerce' ) ) {

				// Hide page header.
				add_filter( 'hivetheme/v1/areas/site_hero', [ $this, 'hide_page_header' ], 100 );

				// Render page title.
				add_action( 'woocommerce_account_content', [ $this, 'render_page_title' ], 1 );
			}

			// Alter templates.
			add_filter( 'hivepress/v1/templates/page_sidebar_left', [ $this, 'alter_page_sidebar_left' ] );
		}

		parent::__construct( $args );
	}

	/**
	 * Gets translation string.
	 *
	 * @param string $key String key.
	 * @return mixed
	 */
	public function get_string( $key ) {
		$string = '';

		if ( ht\is_plugin_active( 'hivepress' ) ) {
			$string = hivepress()->translator->get_string( $key );
		}

		return $string;
	}

	/**
	 * Adds admin notices.
	 *
	 * @param array $notices Notice arguments.
	 * @return array
	 */
	public function add_admin_notices( $notices ) {

		// Get listing count.
		$count = wp_count_posts( 'hp_listing' );

		// Add import notice.
		if ( isset( $count->publish ) && ! $count->publish ) {
			$notices['demo_import'] = [
				'type'        => 'info',
				'dismissible' => true,
				'text'        => sprintf(
					/* translators: 1: theme name, 2: link URL. */
					hp\sanitize_html( __( 'If you want to start with the %1$s demo content, please follow <a href="%2$s" target="_blank">this screencast</a> to import it.', 'listinghive' ) ),
					hivetheme()->get_name( 'parent' ),
					esc_url( 'https://hivepress.io/docs/themes/' . get_template() . '/?article=import' )
				),
			];
		}

		return $notices;
	}

	/**
	 * Renders site header.
	 *
	 * @param string $output HTML output.
	 * @return string
	 */
	public function render_site_header( $output ) {
		$output .= ( new Blocks\Template( [ 'template' => 'site_header_block' ] ) )->render();

		return $output;
	}

	/**
	 * Hides page header.
	 *
	 * @param string $output HTML output.
	 * @return string
	 */
	public function hide_page_header( $output ) {
		if ( is_wc_endpoint_url( 'orders' ) || is_wc_endpoint_url( 'view-order' ) ) {
			$output = '';
		}

		return $output;
	}

	/**
	 * Renders page title.
	 */
	public function render_page_title() {
		if ( is_wc_endpoint_url( 'orders' ) || is_wc_endpoint_url( 'view-order' ) ) {
			echo ( new Blocks\Part(
				[
					'path'    => 'page/page-title',

					'context' => [
						'page_title' => get_the_title(),
					],
				]
			) )->render();
		}
	}

	/**
	 * Alters page sidebar left.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_page_sidebar_left( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'page_sidebar' => [
						'attributes' => [
							'class' => [ 'site-sidebar' ],
						],
					],
				],
			]
		);
	}
}
