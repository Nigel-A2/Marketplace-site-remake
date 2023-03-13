<?php
/*
Plugin Name:  WPBeginner Plugin Tutorial
Plugin URI:   https://www.wpbeginner.com 
Description:  A short little description of the plugin. It will be displayed on the Plugins page in WordPress admin area. 
Version:      2.0
Author:       WPBeginner 
Author URI:   https://www.wpbeginner.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wpb-tutorial
Domain Path:  /languages
*/

function wpb_follow_us($content) {
 
    // Only do this when a single post is displayed
    if ( is_single() ) { 
     
    // Message you want to display after the post
    // Adds URLs to Twitter and Facebook profiles
     
    $content .= '<p class="follow-us">If you liked this article, then please follow us on <a href="http://twitter.com/wpbeginner" title="WPBeginner on Twitter" target="_blank" rel="nofollow">Twitter</a> and <a href="https://www.facebook.com/wpbeginner" title="WPBeginner on Facebook" target="_blank" rel="nofollow">Facebook</a>.</p>';
     
    } 
    // Display the content
    return $content; 
     
    }
    // Hook our function to WordPress the_content filter
    add_filter('the_content', 'wpb_follow_us'); 
    // <?php has the closing tag ? > (without a space), but it seems to be optional


    // function that runs when the shortcode is called
function wpb_demo_shortcode() { 
  
    // Things that you want to do.
    $message = 'Hello world!'; 
      
    // Output needs to be return
    return $message;
    }
    // register shortcode into the plugin, making it usable for the site as long as the plugin is active
    add_shortcode('greetings', 'wpb_demo_shortcode'); // I think this shortcode broke my site because I put it in the theme's functions.php

function fizzbuzz() {
    // variables start with $ in PHP
    $i;

    // for (starting value; restriction; number change)
    // if x % y = 0, then x is divisible by y
    // if x % y != 0, find the nearest number below x that is divisible by y and then subtract that number from x.
    // 50 % 15; 45 % 15 = 0; 50 - 45 = 5; 50 % 15 = 5
    // remember: the nearest number below x can be equal to y; 50 % 48 = 2 because 48 % 48 = 0 and 50 - 48 = 2
    for ($i = 1; $i <= 100; $i++)
    {
        if ($i % 35 == 0)
            echo "BuzzWoof";

        else if ($i % 21 == 0)
            echo "FizzWoof";

        // number divisible by 3 and
        // 5 will always be divisible
        // by 15, print 'FizzBuzz' in
        // place of the number
        else if ($i % 15 == 0)
            echo "FizzBuzz"; // . is used instead of plus it seems
 
        // number divisible by 3? print
        // 'Fizz' in place of the number
        else if (($i % 3) == 0) // what if I use multiple if statements instead of else if?
            echo "Fizz";            
 
        // number divisible by 5, print
        // 'Buzz' in place of the number
        else if (($i % 5) == 0)                
            echo "Buzz";
            
        // take it a step further, add "woof" to the mix for every number divisible by 7
        else if (($i % 7) == 0)
            echo "Woof";

        else // print the number        
            //echo $i,"  " ;  // old code, adds an empty space after printing $i
            echo $i;
        
        echo " ";
    }
}
// add_shortcode (the shortcode's name, the name of the function the shortcode uses)
add_shortcode( 'fizzbuzz', 'fizzbuzz' );
// make an optimized version here based on what you saw in that website. You can do it!
// I should look around at the WooCommerce code too

// Akshually, it's Fibonacci
function Fibbonacci(){
    $a = 0;
    echo $a . " ";
    $b = 1;
    echo $b . " ";
    for ($c = 3; $c <= 12; $c++){
        $a += $b;
        echo $a . " ";
        $b += $a;
        echo $b . " ";
    }
}
// got it right on my first try, cool
add_shortcode('Fibbonacci', 'Fibbonacci');

function fizzbuzz_optimized(){
    $c3 = 0;
    $c5 = 0;
    for ($i = 1; $i <= 100; $i++){
        $c3 += 1;
        $c5 += 1;
        // = assigns a value, == compares two values, === compares the values AND types
        if ($c5 < 5 && $c3 < 3){
            echo $i;
        }
        if ($c3 == 3){
            echo "fizz";
            $c3 = 0;
        }
        // string + string is a syntax error in PHP 
        if ($c5 == 5){
            echo "buzz";
            $c5 = 0;
        }
        
        echo " ";
    }
}
    
add_shortcode('fizzbuzz2', 'fizzbuzz_optimized');


// The admin menu of the plugin that uses the WordPress data layer and /src/index.js
function my_admin_menu() {
    // Create a new admin page for our app.
    add_menu_page(
        __( 'My first Gutenberg app', 'gutenberg' ),
        __( 'My first Gutenberg app', 'gutenberg' ),
        'manage_options',
        'my-first-gutenberg-app',
        function () {
            echo '
            <h2>Pages</h2>
            <div id="my-first-gutenberg-app"></div>
        ';
        },
        'dashicons-schedule',
        3
    );
}
 
add_action( 'admin_menu', 'my_admin_menu' );
 
function load_custom_wp_admin_scripts( $hook ) {
    // Load only on ?page=my-first-gutenberg-app.
    if ( 'toplevel_page_my-first-gutenberg-app' !== $hook ) {
        return;
    }
 
    // Load the required WordPress packages.
 
    // Automatically load imported dependencies and assets version.
    $asset_file = include plugin_dir_path( __FILE__ ) . 'build/index.asset.php';
 
    // Enqueue CSS dependencies.
    foreach ( $asset_file['dependencies'] as $style ) {
        wp_enqueue_style( $style );
    }
 
    // Load our app.js.
    wp_register_script(
        'my-first-gutenberg-app',
        plugins_url( 'build/index.js', __FILE__ ),
        $asset_file['dependencies'],
        $asset_file['version']
    );
    wp_enqueue_script( 'my-first-gutenberg-app' );
 
    // Load our style.css.
    wp_register_style(
        'my-first-gutenberg-app',
        plugins_url( 'style.css', __FILE__ ),
        array(),
        $asset_file['version']
    );
    wp_enqueue_style( 'my-first-gutenberg-app' );
}
 
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_scripts' );