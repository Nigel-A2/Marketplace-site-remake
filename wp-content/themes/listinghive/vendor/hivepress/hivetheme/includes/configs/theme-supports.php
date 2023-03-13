<?php
/**
 * Theme supports configuration.
 *
 * @package HiveTheme\Configs
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'custom-logo',
	'post-thumbnails',
	'editor-styles',
	'wp-block-styles',
	'responsive-embeds',
	'align-wide',
	'hivepress',
	'wc-product-gallery-lightbox',
	'wc-product-gallery-slider',

	'html5'       => [
		'comment-list',
		'comment-form',
		'search-form',
		'gallery',
		'caption',
		'style',
		'script',
	],

	'woocommerce' => [
		'thumbnail_image_width' => 400,
		'single_image_width'    => 600,

		'product_grid'          => [
			'default_columns' => 2,
			'min_columns'     => 2,
			'max_columns'     => 2,
		],
	],
];
