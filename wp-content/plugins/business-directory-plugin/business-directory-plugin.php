<?php
/**
 * Plugin Name: Business Directory Plugin
 * Plugin URI: https://businessdirectoryplugin.com
 * Description: Provides the ability to maintain a free or paid business directory on your WordPress powered site.
 * Version: 6.3.1
 * Author: Business Directory Team
 * Author URI: https://businessdirectoryplugin.com
 * Text Domain: business-directory-plugin
 * Domain Path: /languages/
 * License: GPLv2 or any later version
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or later, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package WPBDP
 */

// Do not allow direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WPBDP_PLUGIN_FILE' ) ) {
    define( 'WPBDP_PLUGIN_FILE', __FILE__ );
}

if ( ! class_exists( 'WPBDP' ) ) {
    require_once dirname( WPBDP_PLUGIN_FILE ) . '/includes/class-wpbdp.php';
}

/**
 * Returns the main instance of Business Directory.
 *
 * @return WPBDP
 */
function wpbdp() {
    static $instance = null;

    if ( is_null( $instance ) ) {
        $instance = new WPBDP();
    }

    return $instance;
}


// For backwards compatibility.
$GLOBALS['wpbdp'] = wpbdp();
