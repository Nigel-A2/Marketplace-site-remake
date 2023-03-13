<?php
/**
 * Scripts configuration.
 *
 * @package HiveTheme\Configs
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'focus_visible' => [
		'handle' => 'focus-visible',
		'src'    => hivetheme()->get_url() . '/assets/js/focus-visible.min.js',
	],

	'core_frontend' => [
		'handle' => 'hivetheme-core-frontend',
		'src'    => hivetheme()->get_url() . '/assets/js/frontend.min.js',
		'deps'   => [ 'jquery', 'hoverIntent', 'imagesloaded', 'comment-reply' ],
	],
];
