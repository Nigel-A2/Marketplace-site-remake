<?php

/**
 * @class FLSidebarModule
 */
class FLSidebarModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Sidebar', 'fl-builder' ),
			'description'     => __( 'Display a WordPress sidebar that has been registered by the current theme.', 'fl-builder' ),
			'category'        => true === FL_BUILDER_LITE ? __( 'Basic', 'fl-builder' ) : __( 'Layout', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'layout.svg',
		));
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLSidebarModule', array(
	'general' => array( // Tab
		'title' => __( 'General', 'fl-builder' ), // Tab title
		'file'  => FL_BUILDER_DIR . 'modules/sidebar/includes/settings-general.php',
	),
));
