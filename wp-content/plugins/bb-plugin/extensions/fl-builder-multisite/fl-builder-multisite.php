<?php

// Only load for multisite installs.
if ( ! is_multisite() ) {
	return;
}

// Defines
define( 'FL_BUILDER_MULTISITE_DIR', FL_BUILDER_DIR . 'extensions/fl-builder-multisite/' );
define( 'FL_BUILDER_MULTISITE_URL', FL_BUILDER_URL . 'extensions/fl-builder-multisite/' );

// Classes
require_once FL_BUILDER_MULTISITE_DIR . 'classes/class-fl-builder-multisite.php';
