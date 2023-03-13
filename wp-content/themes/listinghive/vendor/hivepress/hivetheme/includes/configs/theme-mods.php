<?php
/**
 * Theme mods configuration.
 *
 * @package HiveTheme\Configs
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'title_tagline'     => [
		'fields' => [
			'copyright_notice' => [
				'label' => esc_html__( 'Copyright Notice', 'listinghive' ),
				'type'  => 'textarea',
			],
		],
	],

	'static_front_page' => [
		'fields' => [
			'page_loader' => [
				'label'   => esc_html__( 'Enable page loading screen', 'listinghive' ),
				'type'    => 'checkbox',
				'default' => true,
			],
		],
	],

	'colors'            => [
		'title'  => esc_html__( 'Colors', 'listinghive' ),

		'fields' => [
			'primary_color'   => [
				'label' => esc_html__( 'Primary Color', 'listinghive' ),
				'type'  => 'color',
			],

			'secondary_color' => [
				'label' => esc_html__( 'Secondary Color', 'listinghive' ),
				'type'  => 'color',
			],
		],
	],

	'fonts'             => [
		'title'  => esc_html__( 'Fonts', 'listinghive' ),

		'fields' => [
			'heading_font'        => [
				'label' => esc_html__( 'Heading Font', 'listinghive' ),
				'type'  => 'font',
			],

			'heading_font_weight' => [
				'type' => 'hidden',
			],

			'body_font'           => [
				'label' => esc_html__( 'Body Font', 'listinghive' ),
				'type'  => 'font',
			],

			'body_font_weight'    => [
				'type' => 'hidden',
			],
		],
	],
];
