<?php

include_once 'class-show-category.php';

class WPBDP__Views__Show_Tag extends WPBDP__Views__Show_Category {

	/**
	 * @since 6.2.2
	 * @return string
	 */
	protected function get_taxonomy_html( $term ) {
		global $wp_query;

		$term->is_tag = true;

		return $this->_render(
			'tag',
			array(
				'title'        => $term->name,
				'term'         => $term,
				'query'        => $wp_query,
				'in_shortcode' => false,
			),
			'page'
		);
	}
}
