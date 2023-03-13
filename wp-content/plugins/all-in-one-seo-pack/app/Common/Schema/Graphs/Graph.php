<?php
namespace AIOSEO\Plugin\Common\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The base graph class.
 *
 * @since 4.0.0
 */
abstract class Graph {
	use Traits\SocialProfiles;

	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.0
	 */
	abstract public function get();

	/**
	 * Builds the graph data for a given image with a given schema ID.
	 *
	 * @since 4.0.0
	 *
	 * @param int    $imageId The image ID.
	 * @param string $graphId The graph ID.
	 * @return array $data    The image graph data.
	 */
	protected function image( $imageId, $graphId ) {
		$attachmentId = is_string( $imageId ) && ! is_numeric( $imageId ) ? aioseo()->helpers->attachmentUrlToPostId( $imageId ) : $imageId;
		$imageUrl     = wp_get_attachment_image_url( $attachmentId, 'full' );

		$data = [
			'@type' => 'ImageObject',
			'@id'   => trailingslashit( home_url() ) . '#' . $graphId,
			'url'   => $imageUrl ? $imageUrl : $imageId,
		];

		if ( ! $attachmentId ) {
			return $data;
		}

		$metaData = wp_get_attachment_metadata( $attachmentId );
		if ( $metaData ) {
			$data['width']  = (int) $metaData['width'];
			$data['height'] = (int) $metaData['height'];
		}

		$caption = $this->getImageCaption( $attachmentId );
		if ( ! empty( $caption ) ) {
			$data['caption'] = $caption;
		}

		return $data;
	}

	/**
	 * Get the image caption.
	 *
	 * @since 4.1.4
	 *
	 * @param  int    $attachmentId The attachment ID.
	 * @return string               The caption.
	 */
	private function getImageCaption( $attachmentId ) {
		$caption = wp_get_attachment_caption( $attachmentId );
		if ( ! empty( $caption ) ) {
			return $caption;
		}

		return get_post_meta( $attachmentId, '_wp_attachment_image_alt', true );
	}

	/**
	 * Returns the graph data for the avatar of a given user.
	 *
	 * @since 4.0.0
	 *
	 * @param  int    $userId  The user ID.
	 * @param  string $graphId The graph ID.
	 * @return array           The graph data.
	 */
	protected function avatar( $userId, $graphId ) {
		if ( ! get_option( 'show_avatars' ) ) {
			return [];
		}

		$avatar = get_avatar_data( $userId );
		if ( ! $avatar['found_avatar'] ) {
			return [];
		}

		return array_filter( [
			'@type'   => 'ImageObject',
			'@id'     => aioseo()->schema->context['url'] . "#$graphId",
			'url'     => $avatar['url'],
			'width'   => $avatar['width'],
			'height'  => $avatar['height'],
			'caption' => get_the_author_meta( 'display_name', $userId )
		] );
	}

	/**
	 * Iterates over a list of functions and sets the results as graph data.
	 *
	 * @since 4.0.13
	 *
	 * @param  array $data          The graph data to add to.
	 * @param  array $dataFunctions List of functions to loop over, associated with a graph property.
	 * @return array $data          The graph data with the results added.
	 */
	protected function getData( $data, $dataFunctions ) {
		foreach ( $dataFunctions as $k => $f ) {
			if ( ! method_exists( $this, $f ) ) {
				continue;
			}

			$value = $this->$f();
			if ( $value || in_array( $k, aioseo()->schema->nullableFields, true ) ) {
				$data[ $k ] = $value;
			}
		}

		return $data;
	}
}