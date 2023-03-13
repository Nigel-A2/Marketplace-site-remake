<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );


/**
 * Remove More products from seller tab
 * On Single Product Page
 *
 * @param array $tabs
 *
 * @since 2.5
 * @return int
 */
add_action( 'woocommerce_product_tabs', 'dokan_remove_more_from_seller_tab', 0 );
function dokan_remove_more_from_seller_tab( $tabs ) {
    remove_action( 'woocommerce_product_tabs', 'dokan_set_more_from_seller_tab', 10 );
}

// adds a shortcode from a plugin directly into the entire site as long as the theme is active. 
// WARNING: BE VERY CAREFUL WHEN ADDING do_shortcode TO functions.php IT COULD BREAK STUFF
//echo do_shortcode("[greetings]"); ?>




