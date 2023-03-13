<?php

/*

WARNING! DO NOT EDIT THEME FILES IF YOU PLAN ON UPDATING!

Theme files will be overwritten and your changes will be lost
when updating. Instead, add custom code in the admin under
Appearance > Theme Settings > Code or create a child theme.

*/

// Defines
define( 'FL_THEME_VERSION', '1.7.11' );
define( 'FL_THEME_DIR', get_template_directory() );
define( 'FL_THEME_URL', get_template_directory_uri() );

// Classes
if ( ! class_exists( 'FL_Filesystem' ) ) {
	require_once 'classes/class-fl-filesystem.php';
}
require_once 'classes/class-fl-color.php';
require_once 'classes/class-fl-css.php';
require_once 'classes/class-fl-customizer.php';
require_once 'classes/class-fl-fonts.php';
require_once 'classes/class-fl-layout.php';
require_once 'classes/class-fl-theme.php';
require_once 'classes/class-fl-theme-update.php';
require_once 'classes/class-fl-compat.php';
require_once 'classes/class-fl-shortcodes.php';
require_once 'classes/class-fl-wp-editor.php';

/* WP CLI Commands */
if ( defined( 'WP_CLI' ) ) {
	require 'classes/class-fl-wpcli-command.php';
}

// Theme Actions
add_action( 'after_switch_theme', 'FLCustomizer::refresh_css' );
add_action( 'after_setup_theme', 'FLTheme::setup' );
add_action( 'init', 'FLTheme::init_woocommerce' );
add_action( 'wp_enqueue_scripts', 'FLTheme::enqueue_scripts', 999 );
add_action( 'widgets_init', 'FLTheme::widgets_init' );
add_action( 'wp_footer', 'FLTheme::go_to_top' );
add_action( 'fl_after_post', 'FLTheme::after_post_widget', 10 );
add_action( 'fl_after_post_content', 'FLTheme::post_author_box', 10 );
// Header Actions
add_action( 'wp_head', 'FLTheme::pingback_url' );
add_action( 'fl_head_open', 'FLTheme::title' );
add_action( 'fl_head_open', 'FLTheme::favicon' );
add_action( 'fl_head_open', 'FLTheme::fonts' );
add_action( 'fl_body_open', 'FLTheme::skip_to_link' );

// Added in WP 5.2
if ( function_exists( 'wp_body_open' ) ) {
	add_action( 'fl_body_open', 'wp_body_open' );
}

// Theme Filters
add_filter( 'body_class', 'FLTheme::body_class' );
add_filter( 'excerpt_more', 'FLTheme::excerpt_more' );
add_filter( 'loop_shop_columns', 'FLTheme::woocommerce_columns' );
add_filter( 'loop_shop_per_page', 'FLTheme::woocommerce_shop_products_per_page' );
add_filter( 'comment_form_default_fields', 'FLTheme::comment_form_default_fields' );
add_filter( 'woocommerce_style_smallscreen_breakpoint', 'FLTheme::woo_mobile_breakpoint' );
add_filter( 'walker_nav_menu_start_el', 'FLTheme::nav_menu_start_el', 10, 4 );
add_filter( 'comments_popup_link_attributes', 'FLTheme::comments_popup_link_attributes' );
add_filter( 'comment_form_defaults', 'FLTheme::comment_form_defaults' );

// Theme Updates
add_action( 'init', 'FLThemeUpdate::init' );

// Admin Actions
add_action( 'admin_head', 'FLTheme::favicon' );

// Customizer
add_action( 'customize_preview_init', 'FLCustomizer::preview_init' );
add_action( 'customize_controls_enqueue_scripts', 'FLCustomizer::controls_enqueue_scripts' );
add_action( 'customize_controls_print_footer_scripts', 'FLCustomizer::controls_print_footer_scripts' );
add_action( 'customize_controls_print_styles', 'FLCustomizer::controls_print_styles' );
add_action( 'customize_register', 'FLCustomizer::register' );
add_action( 'customize_save_after', 'FLCustomizer::save' );

// Compatibility
FLThemeCompat::init();
