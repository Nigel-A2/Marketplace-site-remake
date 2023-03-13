<?php
/**
 * Asset component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Asset component class.
 *
 * @class Asset
 */
final class Asset extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Set content width.
		add_action( 'init', [ $this, 'set_content_width' ] );

		// Add image sizes.
		add_action( 'after_setup_theme', [ $this, 'add_image_sizes' ] );

		// Enqueue styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_init', [ $this, 'enqueue_editor_styles' ] );

		// Enqueue scripts.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// Add script attributes.
		add_filter( 'script_loader_tag', [ $this, 'add_script_attributes' ], 10, 2 );

		// Wrap embeds.
		add_filter( 'embed_oembed_html', [ $this, 'wrap_embeds' ], 100, 4 );

		parent::__construct( $args );
	}

	/**
	 * Sets content width.
	 */
	public function set_content_width() {
		if ( ! isset( $GLOBALS['content_width'] ) ) {
			$GLOBALS['content_width'] = 749;
		}
	}

	/**
	 * Adds image sizes.
	 */
	public function add_image_sizes() {
		foreach ( hivetheme()->get_config( 'image_sizes' ) as $name => $args ) {
			add_image_size( ht\prefix( $name ), $args['width'], ht\get_array_value( $args, 'height', 0 ), ht\get_array_value( $args, 'crop', false ) );
		}
	}

	/**
	 * Enqueues styles.
	 */
	public function enqueue_styles() {

		// Get styles.
		$styles = hivetheme()->get_config( 'styles' );

		// Filter styles.
		$styles = array_filter(
			$styles,
			function( $style ) {
				$scope = (array) ht\get_array_value( $style, 'scope' );

				return ! array_diff( [ 'frontend', 'backend' ], $scope ) || ( ! is_admin() xor in_array( 'backend', $scope, true ) );
			}
		);

		// Enqueue styles.
		foreach ( $styles as $style ) {
			wp_enqueue_style( $style['handle'], $style['src'], ht\get_array_value( $style, 'deps', [] ), ht\get_array_value( $style, 'version', hivetheme()->get_version() ) );
		}
	}

	/**
	 * Enqueues editor styles.
	 */
	public function enqueue_editor_styles() {
		foreach ( hivetheme()->get_config( 'styles' ) as $style ) {
			if ( in_array( 'editor', (array) ht\get_array_value( $style, 'scope' ), true ) ) {
				add_editor_style( $style['src'] );
			}
		}
	}

	/**
	 * Enqueues scripts.
	 */
	public function enqueue_scripts() {

		// Get scripts.
		$scripts = hivetheme()->get_config( 'scripts' );

		// Filter scripts.
		$scripts = array_filter(
			$scripts,
			function( $script ) {
				$scope = (array) ht\get_array_value( $script, 'scope' );

				return ! array_diff( [ 'frontend', 'backend' ], $scope ) || ( ! is_admin() xor in_array( 'backend', $scope, true ) );
			}
		);

		// Enqueue scripts.
		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script['handle'], $script['src'], ht\get_array_value( $script, 'deps', [] ), ht\get_array_value( $script, 'version', hivetheme()->get_version() ), ht\get_array_value( $script, 'in_footer', true ) );

			// Add script data.
			if ( isset( $script['data'] ) ) {
				wp_localize_script( $script['handle'], lcfirst( str_replace( ' ', '', ucwords( str_replace( '-', ' ', $script['handle'] ) ) ) ) . 'Data', $script['data'] );
			}
		}
	}

	/**
	 * Adds script attributes.
	 *
	 * @param string $tag Script tag.
	 * @param string $handle Script handle.
	 * @return string
	 */
	public function add_script_attributes( $tag, $handle ) {

		// Set attributes.
		$attributes = [ 'async', 'defer', 'crossorigin' ];

		// Filter HTML.
		foreach ( $attributes as $attribute ) {
			$value = wp_scripts()->get_data( $handle, $attribute );

			if ( $value ) {
				$output = ' ' . $attribute;

				if ( strpos( $tag, $output . '>' ) === false && strpos( $tag, $output . ' ' ) === false && strpos( $tag, $output . '="' ) === false ) {
					if ( ! is_bool( $value ) ) {
						$output .= '="' . esc_attr( $value ) . '"';
					}

					$tag = str_replace( '></', $output . '></', $tag );
				}
			}
		}

		return $tag;
	}

	/**
	 * Wraps responsive embeds.
	 *
	 * @param string $html Embed HTML.
	 * @param string $url Embed URL.
	 * @param array  $attr HTML attributes.
	 * @param int    $post_id Post ID.
	 * @return string
	 */
	public function wrap_embeds( $html, $url, $attr, $post_id ) {
		if ( ! has_blocks( $post_id ) ) {
			preg_match_all( '/\s(width|height)="(\d+)"/', $html, $matches );

			// Get embed size.
			$size = ht\get_last_array_value( $matches );

			if ( is_array( $size ) && count( $size ) === 2 ) {
				$width  = absint( ht\get_first_array_value( $size ) );
				$height = absint( ht\get_last_array_value( $size ) );

				if ( $width && $height ) {

					// Get aspect ratio.
					$ratio = array_search(
						round( $width / $height, 1 ),
						[
							'21-9' => 2.3,
							'18-9' => 2.0,
							'16-9' => 1.8,
							'4-3'  => 1.3,
							'1-1'  => 1.0,
							'9-16' => 0.6,
							'1-2'  => 0.5,
						]
					);

					// Add responsive wrapper.
					if ( $ratio ) {
						$html = '<figure class="wp-block-embed wp-has-aspect-ratio wp-embed-aspect-' . esc_attr( $ratio ) . '"><div class="wp-block-embed__wrapper">' . $html . '</div></figure>';
					}
				}
			}
		}

		return $html;
	}
}
