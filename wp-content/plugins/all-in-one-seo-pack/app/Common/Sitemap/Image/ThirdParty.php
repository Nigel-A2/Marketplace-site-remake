<?php
namespace AIOSEO\Plugin\Common\Sitemap\Image;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Holds all code to extract images from third-party content.
 *
 * @since 4.2.2
 */
class ThirdParty {
	/**
	 * The image URLs and IDs.
	 *
	 * @since 4.2.2
	 *
	 * @var array[mixed]
	 */
	private $images = [];

	/**
	 * Class constructor.
	 *
	 * @since 4.2.2
	 *
	 * @param WP_Post $post The post object.
	 */
	public function __construct( $post ) {
		$this->post = $post;
	}

	/**
	 * Extracts the images from third-party content.
	 *
	 * @since 4.2.2
	 *
	 * @return array[mixed] The image URLs and IDs.
	 */
	public function extract() {
		$integrations = [
			'acf',
			'divi',
			'wooCommerce'
		];

		foreach ( $integrations as $integration ) {
			$this->{$integration}();
		}

		return $this->images;
	}

	/**
	 * Extracts image URLs from ACF fields.
	 *
	 * @since 4.2.2
	 *
	 * @return void
	 */
	private function acf() {
		if ( ! class_exists( 'ACF' ) || ! function_exists( 'get_fields' ) ) {
			return;
		}

		$fields = get_fields( $this->post->ID );
		if ( ! $fields ) {
			return;
		}

		$images       = $this->acfHelper( $fields );
		$this->images = array_merge( $this->images, $images );
	}

	/**
	 * Helper function for acf().
	 *
	 * @since 4.2.2
	 *
	 * @param  array         $fields The ACF fields.
	 * @return array[string]         The image URLs or IDs.
	 */
	private function acfHelper( $fields ) {
		$images = [];
		foreach ( $fields as $value ) {
			if ( is_array( $value ) ) {
				// Recursively loop over grouped fields.
				// We continue on since arrays aren't necessarily groups and might also simply aLready contain the value we're looking for.
				$images = array_merge( $images, $this->acfHelper( $value ) );

				if ( isset( $value['type'] ) && 'image' !== strtolower( $value['type'] ) ) {
					$images[] = $value['url'];
				}

				continue;
			}

			// Capture the value if it's an image URL, but not the default thumbnail from ACF.
			if ( preg_match( aioseo()->sitemap->image->getImageExtensionRegexPattern(), $value ) && ! preg_match( '/media\/default\.png$/i', $value ) ) {
				$images[] = $value;
				continue;
			}

			// Capture the value if it's a numeric image ID, but make sure it's not an array of random field object properties.
			if (
				is_numeric( $value ) &&
				! isset( $fields['ID'] ) &&
				! isset( $fields['thumbnail'] )
			) {
				$images[] = $value;
			}
		}

		return $images;
	}

	/**
	 * Extracts images from Divi shortcodes.
	 *
	 * @since 4.1.8
	 *
	 * @return void
	 */
	private function divi() {
		if ( ! defined( 'ET_BUILDER_VERSION' ) ) {
			return;
		}

		$urls  = [];
		$regex = get_shortcode_regex( [ 'et_pb_image', 'et_pb_gallery' ] );
		preg_match_all( "/$regex/i", $this->post->post_content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $shortcode ) {
			$attributes = shortcode_parse_atts( $shortcode[3] );
			if ( ! empty( $attributes['src'] ) ) {
				$urls[] = $attributes['src'];
			}

			if ( ! empty( $attributes['gallery_ids'] ) ) {
				$attachmentIds = explode( ',', $attributes['gallery_ids'] );
				foreach ( $attachmentIds as $attachmentId ) {
					$urls[] = wp_get_attachment_url( $attachmentId );
				}
			}
		}

		$this->images = array_merge( $this->images, $urls );
	}

	/**
	 * Extracts the image IDs of WooCommerce product galleries.
	 *
	 * @since 4.1.2
	 *
	 * @return void
	 */
	private function wooCommerce() {
		if ( ! aioseo()->helpers->isWooCommerceActive() || 'product' !== $this->post->post_type ) {
			return;
		}

		$productImageIds = get_post_meta( $this->post->ID, '_product_image_gallery', true );
		if ( ! $productImageIds ) {
			return;
		}

		$productImageIds = explode( ',', $productImageIds );
		$this->images    = array_merge( $this->images, $productImageIds );
	}
}