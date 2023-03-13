<?php

/* Code Panel */
FLCustomizer::add_panel('fl-code', array(
	'title'    => _x( 'Code', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* CSS Section */
		'fl-css-code-section'    => array(
			'title'   => _x( 'CSS Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* CSS */
				'fl-css-code' => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'description' => __( 'CSS entered in the box below will be rendered within &lt;style&gt; tags.', 'fl-automator' ),
						'class'       => 'FLCustomizerControl',
						'type'        => 'code',
						'mode'        => 'css',
					),
				),
			),
		),

		/* JavaScript Section */
		'fl-js-code-section'     => array(
			'title'   => _x( 'JavaScript Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* JavaScript */
				'fl-js-code' => array(
					'setting' => array(
						'default'           => '',
						'transport'         => 'postMessage',
						'validate_callback' => 'fl_theme_validate_js',
					),
					'control' => array(
						'description'    => __( 'JavaScript entered in the box below will be rendered within &lt;script&gt; tags.', 'fl-automator' ),
						'class'          => 'FLCustomizerControl',
						'type'           => 'code',
						'mode'           => 'javascript',
						'preview_button' => true,
					),
				),
			),
		),

		/* Head Section */
		'fl-head-code-section'   => array(
			'title'   => _x( 'Head Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Head */
				'fl-head-code' => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'description'    => __( 'Code entered in the box below will be rendered within the page &lt;head&gt; tag.', 'fl-automator' ),
						'class'          => 'FLCustomizerControl',
						'type'           => 'code',
						'preview_button' => true,
					),
				),
			),
		),

		/* Header Section */
		'fl-header-code-section' => array(
			'title'   => _x( 'Header Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer */
				'fl-header-code' => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'description'    => __( 'Code entered in the box below will be rendered directly after the opening &lt;body&gt; tag.', 'fl-automator' ),
						'class'          => 'FLCustomizerControl',
						'type'           => 'code',
						'preview_button' => true,
					),
				),
			),
		),

		/* Footer Section */
		'fl-footer-code-section' => array(
			'title'   => _x( 'Footer Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer */
				'fl-footer-code' => array(
					'setting' => array(
						'default'   => '',
						'transport' => 'postMessage',
					),
					'control' => array(
						'description'    => __( 'Code entered in the box below will be rendered directly before the closing &lt;body&gt; tag.', 'fl-automator' ),
						'class'          => 'FLCustomizerControl',
						'type'           => 'code',
						'preview_button' => true,
					),
				),
			),
		),
	),
));

/**
 * @since 1.7.9
 * Catch HTML tracking code added to the pure JS setting.
 */
function fl_theme_validate_js( $validity, $value ) {
	if ( preg_match( '#^<(.*)>.*?|<(.*) \/>#m', $value, $matches ) ) {
		$validity->add( 'js_error', __( 'HTML detected, the "JavaScript Code" setting only accepts Javascript.', 'fl-automator' ) );
	}
	return $validity;
}
