<?php

// Button Styles
FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() );

// Background Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-module-content",
	'props'    => array(
		'background-color' => $settings->bg_color,
	),
) );

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id .fl-module-content",
) );

// Wrapper Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'wrap_padding',
	'selector'     => ".fl-node-$id .fl-module-content",
	'props'        => array(
		'padding-top'    => 'wrap_padding_top',
		'padding-right'  => 'wrap_padding_right',
		'padding-bottom' => 'wrap_padding_bottom',
		'padding-left'   => 'wrap_padding_left',
	),
) );

// Title Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-builder-content .fl-node-$id .fl-cta-title",
	'props'    => array(
		'color' => $settings->title_color,
	),
) );

// Title Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-builder-content .fl-node-$id .fl-cta-title",
	'setting_name' => 'title_typography',
	'settings'     => $settings,
) );

// Content Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-builder-content .fl-node-$id .fl-cta-wrap .fl-cta-text-content *",
	'props'    => array(
		'color' => $settings->text_color,
	),
) );

// Content Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-builder-content .fl-node-$id .fl-cta-text-content",
	'setting_name' => 'text_typography',
	'settings'     => $settings,
) );
