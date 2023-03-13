<?php

$layout = $module->get_layout_slug();
$file   = $module->dir . 'includes/post-' . $layout;
$custom = isset( $settings->post_layout ) && 'custom' == $settings->post_layout;

if ( fl_builder_filesystem()->file_exists( $file . '-common.css.php' ) ) {
	include $file . '-common.css.php';
}
if ( ! $custom && fl_builder_filesystem()->file_exists( $file . '.css.php' ) ) {
	include $file . '.css.php';
}

if ( 'load_more' == $settings->pagination ) {
	FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() );
}
