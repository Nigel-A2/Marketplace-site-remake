<?php

$form_selector      = ".fl-node-$id .fl-search-form";
$form_selector_wrap = ".fl-node-$id .fl-search-form-wrap";
$input_selector     = ".fl-node-$id .fl-form-field input[type=search]";
$input_placeholder  = $input_selector . '::placeholder';

// Default form styles
FLBuilderCSS::rule( array(
	'selector' => $form_selector_wrap,
	'props'    => array(
		'font-size' => '16px',
		'padding'   => '10px',
	),
) );

// Form width
FLBuilderCSS::rule( array(
	'selector' => $form_selector_wrap,
	'enabled'  => 'custom' == $settings->width,
	'props'    => array(
		'width' => array(
			'value' => $settings->custom_width,
			'unit'  => $settings->custom_width_unit,
		),
	),
) );

// Overall Alignment
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'form_align',
	'selector'     => $form_selector,
	'prop'         => 'text-align',
) );

// Form height
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'unit'         => 'px',
	'setting_name' => 'form_height',
	'selector'     => $form_selector_wrap,
	'prop'         => 'min-height',
) );

// Form background color
FLBuilderCSS::rule( array(
	'selector' => $form_selector_wrap,
	'props'    => array(
		'background-color' => $settings->form_bg_color,
	),
) );

// Form hover background
FLBuilderCSS::rule( array(
	'selector' => $form_selector_wrap . ':hover',
	'props'    => array(
		'background-color' => $settings->form_bg_hover_color,
	),
) );

// Form Border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'form_border',
	'selector'     => $form_selector_wrap,
) );

// Form Border - Hover Settings
if ( ! empty( $settings->form_border_hover ) && is_array( $settings->form_border ) ) {
	$settings->form_border['color'] = $settings->form_border_hover;
}

FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'form_border_hover',
	'selector'     => $form_selector_wrap . ':hover',
) );


// Form padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'form_padding',
	'selector'     => $form_selector_wrap,
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'form_padding_top',
		'padding-right'  => 'form_padding_right',
		'padding-bottom' => 'form_padding_bottom',
		'padding-left'   => 'form_padding_left',
	),
) );

// Default input styles
FLBuilderCSS::rule( array(
	'selector' => $input_selector,
	'props'    => array(
		'border-radius' => '4px',
		'font-size'     => '16px',
		'line-height'   => '16px',
		'padding'       => '12px 24px',
	),
) );

// Input color
FLBuilderCSS::rule( array(
	'selector' => $input_selector . ',' . $input_placeholder,
	'enabled'  => ! empty( $settings->input_color ),
	'props'    => array(
		'color' => $settings->input_color,
	),
) );

// Input hover color
FLBuilderCSS::rule( array(
	'selector' => $input_selector . ':hover,' . $input_selector . ':focus,' . $input_selector . ':hover::placeholder, ' . $input_selector . ':focus::placeholder',
	'enabled'  => ! empty( $settings->input_hover_color ),
	'props'    => array(
		'color' => $settings->input_hover_color,
	),
) );

// Input background
FLBuilderCSS::rule( array(
	'selector' => $input_selector,
	'enabled'  => ! empty( $settings->input_bg_color ),
	'props'    => array(
		'background-color' => $settings->input_bg_color,
	),
) );

// Input hover background
FLBuilderCSS::rule( array(
	'selector' => $input_selector . ':hover, ' . $input_selector . ':focus',
	'enabled'  => ! empty( $settings->input_bg_hover_color ),
	'props'    => array(
		'background-color' => $settings->input_bg_hover_color,
	),
) );

// Input typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_typography',
	'selector'     => $input_selector,
) );

// Input Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_border',
	'selector'     => $input_selector,
) );

FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_border_hover',
	'selector'     => $input_selector . ':hover,' . $input_selector . ':focus',
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


// Button CSS
FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() );

// Button layout style
if ( 'button' == $settings->layout ) {

	if ( 'expand' == $settings->btn_action ) {
		// Default text field styles.
		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-search-text, .fl-node-$id .fl-search-text:focus",
			'enabled'  => empty( $settings->text_bg_color ),
			'props'    => array(
				'background-color' => $settings->btn_bg_color,
			),
		) );
	}

	if ( 'fullscreen' == $settings->btn_action ) {
		// Input width
		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .mfp-container .fl-search-form-input-wrap",
			'props'    => array(
				'width'  => array(
					'value' => $settings->fs_input_width,
					'unit'  => $settings->fs_input_width_unit,
				),
				'margin' => '74px auto',
			),
		) );

		// Overlay Background
		FLBuilderCSS::rule( array(
			'selector' => ".mfp-bg.fl-node-$id",
			'enabled'  => ! empty( $settings->fs_overlay_bg ),
			'props'    => array(
				'background-color' => $settings->fs_overlay_bg,
				'opacity'          => 1,
				'filter'           => 'none',
			),
		) );

		// Close Button
		FLBuilderCSS::rule( array(
			'selector' => ".mfp-wrap.fl-node-$id button.mfp-close",
			'enabled'  => 'show' === $settings->fs_close_button,
			'props'    => array(
				'background-color' => '595454 !important',
				'border-radius'    => '50%',
				'top'              => '33px !important',
				'right'            => '33px',
				'height'           => '32px',
				'width'            => '32px',
				'line-height'      => '33px',
			),
		) );

		// Close Button Hover
		FLBuilderCSS::rule( array(
			'selector' => ".mfp-wrap.fl-node-$id button.mfp-close:hover",
			'enabled'  => 'show' === $settings->fs_close_button,
			'props'    => array(
				'top' => '33px !important',
			),
		) );
	}
}

// Button Icon
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-button-icon:before",
	'enabled'  => ! empty( $settings->btn_icon_color ),
	'props'    => array(
		'color' => $settings->btn_icon_color,
	),
) );

$btn_icon_color_hover = $settings->btn_icon_color_hover;

// Button Icon Hover
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-button:hover .fl-button-icon:before",
	'enabled'  => ! empty( $settings->btn_icon_color ),
	'props'    => array(
		'color' => ! empty( $btn_icon_color_hover ) ? $btn_icon_color_hover : $settings->btn_text_hover_color,
	),
) );

// Ajax Results
if ( 'ajax' == $settings->result ) {
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-search-results-content",
		'enabled'  => 'custom' == $settings->result_width,
		'props'    => array(
			'width' => array(
				'value' => $settings->custom_result_width,
				'unit'  => $settings->custom_result_width_unit,
			),
		),
	) );
}
