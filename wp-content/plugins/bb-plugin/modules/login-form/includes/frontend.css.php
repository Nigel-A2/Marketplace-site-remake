<?php

$input_selector   = ".fl-node-$id .fl-login-form .fl-form-field input[type=text],.fl-node-$id .fl-login-form .fl-form-field input[type=password]";
$buttons_selector = ".fl-node-$id .fl-form-button a.fl-button";
$logout_settings  = $module->get_button_settings( 'lo_btn_' );
$login_settings   = $module->get_button_settings( 'btn_' );

// Default input styles
FLBuilderCSS::rule( array(
	'selector' => $input_selector,
	'props'    => array(
		'border-radius' => '4px',
		'font-size'     => '16px',
		//	'line-height'   => '16px',
			'padding'   => '10px 24px',
	),
) );

FLBuilderCSS::rule( array(
	'selector' => '.fl-remember-forget',
	'enabled'  => 'yes' === $settings->forget && 'yes' === $settings->remember,
	'props'    => array(
		'float' => 'right',
	),
) );

FLBuilderCSS::rule( array(
	'selector' => '.fl-remember-checkbox',
	'enabled'  => 'no' === $settings->forget && 'yes' === $settings->remember,
	'props'    => array(
		'float' => 'left',
	),
) );


// Button typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'btn_typography',
	'selector'     => $buttons_selector,
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_typography',
	'selector'     => $input_selector,
) );

// Input padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_padding',
	'selector'     => $input_selector,
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'input_padding_top',
		'padding-right'  => 'input_padding_right',
		'padding-bottom' => 'input_padding_bottom',
		'padding-left'   => 'input_padding_left',
	),
) );

// Button padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'btn_padding',
	'selector'     => $buttons_selector,
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'btn_padding_top',
		'padding-right'  => 'btn_padding_right',
		'padding-bottom' => 'btn_padding_bottom',
		'padding-left'   => 'btn_padding_left',
	),
) );
// We only need border radius for inputs.
if ( isset( $settings->btn_border ) && is_array( $settings->btn_border ) ) {
	$settings->input_border           = $settings->btn_border;
	$settings->input_border['style']  = '';
	$settings->input_border['color']  = '';
	$settings->input_border['shadow'] = '';
}
if ( isset( $settings->btn_border_medium ) && is_array( $settings->btn_border_medium ) ) {
	$settings->input_border_medium           = $settings->btn_border_medium;
	$settings->input_border_medium['style']  = '';
	$settings->input_border_medium['color']  = '';
	$settings->input_border_medium['shadow'] = '';
}
if ( isset( $settings->btn_border_responsive ) && is_array( $settings->btn_border_responsive ) ) {
	$settings->input_border_responsive           = $settings->btn_border_responsive;
	$settings->input_border_responsive['style']  = '';
	$settings->input_border_responsive['color']  = '';
	$settings->input_border_responsive['shadow'] = '';
}

// Input border radius
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_border',
	'selector'     => $input_selector,
) );

// Button CSS
FLBuilder::render_module_css( 'button', $id, $login_settings );


// css fopr logout
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-form-button.log-out a",
	'enabled'  => $logout_settings['bg_color'],
	'props'    => array(
		'background-color' => $logout_settings['bg_color'],
		'border-color'     => $logout_settings['bg_color'],
	),
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-form-button.log-out a:hover",
	'enabled'  => $logout_settings['bg_color_hover'],
	'props'    => array(
		'background-color' => $logout_settings['bg_color_hover'],
	),
) );
