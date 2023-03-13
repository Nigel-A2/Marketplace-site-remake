<?php
// Defines
define( 'FL_BUILDER_CACHE_HELPER_DIR', FL_BUILDER_DIR . 'extensions/fl-builder-cache-helper/' );
define( 'FL_BUILDER_CACHE_HELPER_URL', FL_BUILDER_URL . 'extensions/fl-builder-cache-helper/' );

// Classes
if ( version_compare( PHP_VERSION, '5.3.0', '>' ) ) {
	require_once FL_BUILDER_CACHE_HELPER_DIR . 'classes/class-fl-builder-cache-helper.php';
}
