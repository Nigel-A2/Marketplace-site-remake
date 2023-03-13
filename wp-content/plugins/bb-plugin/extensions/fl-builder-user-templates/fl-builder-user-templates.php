<?php

// Defines
define( 'FL_BUILDER_USER_TEMPLATES_DIR', FL_BUILDER_DIR . 'extensions/fl-builder-user-templates/' );
define( 'FL_BUILDER_USER_TEMPLATES_URL', FL_BUILDER_URL . 'extensions/fl-builder-user-templates/' );

// Classes
require_once FL_BUILDER_USER_TEMPLATES_DIR . 'classes/class-fl-builder-user-templates.php';
require_once FL_BUILDER_USER_TEMPLATES_DIR . 'classes/class-fl-builder-user-templates-post-type.php';

// Admin Classes
if ( is_admin() ) {
	require_once FL_BUILDER_USER_TEMPLATES_DIR . 'classes/class-fl-builder-user-templates-admin-add.php';
	require_once FL_BUILDER_USER_TEMPLATES_DIR . 'classes/class-fl-builder-user-templates-admin-edit.php';
	require_once FL_BUILDER_USER_TEMPLATES_DIR . 'classes/class-fl-builder-user-templates-admin-list.php';
	require_once FL_BUILDER_USER_TEMPLATES_DIR . 'classes/class-fl-builder-user-templates-admin-menu.php';
	require_once FL_BUILDER_USER_TEMPLATES_DIR . 'classes/class-fl-builder-user-templates-admin-settings.php';
}
