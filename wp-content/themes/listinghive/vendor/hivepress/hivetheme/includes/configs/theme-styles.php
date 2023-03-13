<?php
/**
 * Theme styles configuration.
 *
 * @package HiveTheme\Configs
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'heading_font'               => [
		'selector'   => '
			h1,
			h2,
			h3,
			h4,
			h5,
			h6,
			fieldset legend,
			.header-logo__name,
			.comment__author,
			.hp-review__author,
			.hp-message--view-block hp-message__sender,
			.woocommerce ul.product_list_widget li .product-title,
			.editor-post-title__block,
			.editor-post-title__input
		',

		'properties' => [
			[
				'name'      => 'font-family',
				'theme_mod' => 'heading_font',
			],

			[
				'name'      => 'font-weight',
				'theme_mod' => 'heading_font_weight',
			],
		],
	],

	'body_font'                  => [
		'selector'   => '
			body
		',

		'properties' => [
			[
				'name'      => 'font-family',
				'theme_mod' => 'body_font',
			],
		],
	],

	'primary_color'              => [
		'selector'   => '
			.header-navbar__menu ul li.active > a,
			.header-navbar__menu ul li.current-menu-item > a,
			.header-navbar__menu ul li a:hover,
			.footer-navbar__menu ul li a:hover,
			.hp-menu--tabbed .hp-menu__item a:hover,
			.hp-menu--tabbed .hp-menu__item--current a,
			.widget_archive li a:hover,
			.widget_categories li a:hover,
			.widget_categories li.current-cat > a,
			.widget_categories li.current-cat::before,
			.widget_product_categories li a:hover,
			.widget_product_categories li.current-cat > a,
			.widget_product_categories li.current-cat::before,
			.widget_meta li a:hover,
			.widget_nav_menu li a:hover,
			.widget_nav_menu li.current-menu-item > a,
			.widget_nav_menu li.current-menu-item::before,
			.woocommerce-MyAccount-navigation li a:hover,
			.woocommerce-MyAccount-navigation li.current-menu-item > a,
			.woocommerce-MyAccount-navigation li.current-menu-item::before,
			.widget_pages li a:hover,
			.widget_recent_entries li a:hover,
			.wp-block-archives li a:hover,
			.wp-block-categories li a:hover,
			.wp-block-latest-posts li a:hover,
			.wp-block-rss li a:hover,
			.widget_archive li:hover > a,
			.widget_categories li:hover > a,
			.widget_product_categories li:hover > a,
			.widget_meta li:hover > a,
			.widget_nav_menu li:hover > a,
			.woocommerce-MyAccount-navigation li:hover > a,
			.widget_pages li:hover > a,
			.widget_recent_entries li:hover > a,
			.wp-block-archives li:hover > a,
			.wp-block-categories li:hover > a,
			.wp-block-latest-posts li:hover > a,
			.wp-block-rss li:hover > a,
			.widget_archive li:hover::before,
			.widget_categories li:hover::before,
			.widget_product_categories li:hover::before,
			.widget_meta li:hover::before,
			.widget_nav_menu li:hover::before,
			.woocommerce-MyAccount-navigation li:hover::before,
			.widget_pages li:hover::before,
			.widget_recent_entries li:hover::before,
			.wp-block-archives li:hover::before,
			.wp-block-categories li:hover::before,
			.wp-block-latest-posts li:hover::before,
			.wp-block-rss li:hover::before,
			.post-navbar__link:hover i,
			.pagination > a:hover,
			.pagination .nav-links > a:hover,
			.post__details a:hover,
			.tagcloud a:hover,
			.wp-block-tag-cloud a:hover,
			.comment__details a:hover,
			.comment-respond .comment-reply-title a:hover,
			.hp-link:hover,
			.hp-link:hover i,
			.pac-item:hover .pac-item-query,
			.woocommerce nav.woocommerce-pagination ul li a:hover,
			.woocommerce nav.woocommerce-pagination ul li a:focus
		',

		'properties' => [
			[
				'name'      => 'color',
				'theme_mod' => 'primary_color',
			],
		],
	],

	'primary_background_color'   => [
		'selector'   => '
			.button--primary,
			button[type="submit"],
			input[type=submit],
			.header-navbar__menu > ul > li.current-menu-item::before,
			.header-navbar__burger > ul > li.current-menu-item::before,
			.hp-menu--tabbed .hp-menu__item--current::before,
			.woocommerce #respond input#submit.alt,
			.woocommerce button[type=submit],
			.woocommerce input[type=submit],
			.woocommerce button[type=submit]:hover,
			.woocommerce input[type=submit]:hover,
			.woocommerce a.button.alt,
			.woocommerce button.button.alt,
			.woocommerce input.button.alt,
			.woocommerce #respond input#submit.alt:hover,
			.woocommerce a.button.alt:hover,
			.woocommerce button.button.alt:hover,
			.woocommerce input.button.alt:hover
		',

		'properties' => [
			[
				'name'      => 'background-color',
				'theme_mod' => 'primary_color',
			],
		],
	],

	'primary_border_color'       => [
		'selector'   => '
			blockquote,
			.wp-block-quote,
			.comment.bypostauthor .comment__image img
		',

		'properties' => [
			[
				'name'      => 'border-color',
				'theme_mod' => 'primary_color',
			],
		],
	],

	'secondary_color'            => [
		'selector'   => '
			.hp-listing__location i
		',

		'properties' => [
			[
				'name'      => 'color',
				'theme_mod' => 'secondary_color',
			],
		],
	],

	'secondary_background_color' => [
		'selector'   => '
			.button--secondary,
			.wp-block-file .wp-block-file__button,
			.hp-field--number-range .ui-slider-range,
			.hp-field input[type=checkbox]:checked + span::before,
			.hp-field input[type=radio]:checked + span::after,
			.woocommerce a.button--secondary,
			.woocommerce button.button--secondary,
			.woocommerce input.button--secondary,
			.woocommerce a.button--secondary:hover,
			.woocommerce button.button--secondary:hover,
			.woocommerce input.button--secondary:hover,
			.woocommerce span.onsale,
			.woocommerce .widget_price_filter .price_slider_wrapper .ui-slider-range
		',

		'properties' => [
			[
				'name'      => 'background-color',
				'theme_mod' => 'secondary_color',
			],
		],
	],

	'secondary_border_color'     => [
		'selector'   => '
			.hp-field input[type=radio]:checked + span::before,
			.hp-field input[type=checkbox]:checked + span::before
		',

		'properties' => [
			[
				'name'      => 'border-color',
				'theme_mod' => 'secondary_color',
			],
		],
	],
];
