<?php

/**
 * @class FLNumbersModule
 */
class FLNumbersModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Number Counter', 'fl-builder' ),
			'description'     => __( 'Renders an animated number counter.', 'fl-builder' ),
			'category'        => __( 'Info', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'chart-bar.svg',
		));

		$this->add_js( 'jquery-waypoints' );
	}

	/**
	 * @method update
	 * @param $settings {object}
	 * @return object
	 */
	public function update( $settings ) {
		// remove old settings values
		if ( isset( $settings->number_size ) ) {
			unset( $settings->number_size );
			unset( $settings->number_size_unit );
		}

		if ( isset( $settings->number_size_medium ) ) {
			unset( $settings->number_size_medium );
			unset( $settings->number_size_medium_unit );
		}

		if ( isset( $settings->number_size_responsive ) ) {
			unset( $settings->number_size_responsive );
			unset( $settings->number_size_responsive_unit );
		}

		return $settings;
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.4.1
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {
		// migrate old font size
		if ( isset( $settings->number_size ) && ! empty( $settings->number_size ) && isset( $settings->number_size_unit ) ) {
			$settings->number_typography = array_merge(
				is_array( $settings->number_typography ) ? $settings->number_typography : array(),
				array(
					'font_size' => array(
						'unit'   => $settings->number_size_unit,
						'length' => $settings->number_size,
					),
				)
			);
		}

		if ( isset( $settings->number_size_medium ) && ! empty( $settings->number_size_medium ) && isset( $settings->number_size_medium_unit ) ) {
			$settings->number_typography_medium = array_merge(
				is_array( $settings->number_typography_medium ) ? $settings->number_typography_medium : array(),
				array(
					'font_size' => array(
						'unit'   => $settings->number_size_medium_unit,
						'length' => $settings->number_size_medium,
					),
				)
			);
		}

		if ( isset( $settings->number_size_responsive ) && ! empty( $settings->number_size_responsive ) && isset( $settings->number_size_responsive_unit ) ) {
			$settings->number_typography_responsive = array_merge(
				is_array( $settings->number_typography_responsive ) ? $settings->number_typography_responsive : array(),
				array(
					'font_size' => array(
						'unit'   => $settings->number_size_responsive_unit,
						'length' => $settings->number_size_responsive,
					),
				)
			);
		}

		return $settings;
	}

	public function render_number() {

		$number      = isset( $this->settings->number ) && is_numeric( $this->settings->number ) ? $this->settings->number : 100;
		$max         = isset( $this->settings->max_number ) && is_numeric( $this->settings->max_number ) ? $this->settings->max_number : $number;
		$layout      = $this->settings->layout ? $this->settings->layout : 'default';
		$type        = $this->settings->number_type ? $this->settings->number_type : 'percent';
		$prefix      = 'percent' == $type ? '' : $this->settings->number_prefix;
		$suffix      = 'percent' == $type ? '%' : $this->settings->number_suffix;
		$start       = 'jQuery( ".fl-node-' . $this->node . ' .fl-number-int" ).html( "0" );';
		$nojs        = '<noscript>' . number_format( $number ) . '</noscript>';
		$number_data = 'data-number="' . $number . '" data-total="' . $max . '"';

		wp_add_inline_script( 'jquery-waypoints', $start, 'after' );
		wp_localize_script( 'jquery-waypoints', 'number_module_' . $this->node, array(
			'number' => $number,
			'max'    => $max,
		) );

		echo '<div class="fl-number-string">' . $prefix . '<span class="fl-number-int" ' . $number_data . '>' . $nojs . '</span>' . $suffix . '</div>';
	}

	public function render_circle_bar() {

		$width  = ! empty( $this->settings->circle_width ) ? $this->settings->circle_width : 100;
		$pos    = ( $width / 2 );
		$radius = $pos - 10;
		$dash   = number_format( ( ( M_PI * 2 ) * $radius ), 2, '.', '' );

		$html  = '<div class="svg-container">';
		$html .= '<svg class="svg" viewBox="0 0 ' . $width . ' ' . $width . '" version="1.1" preserveAspectRatio="xMinYMin meet">
					<circle class="fl-bar-bg" r="' . $radius . '" cx="' . $pos . '" cy="' . $pos . '" fill="transparent" stroke-dasharray="' . $dash . '" stroke-dashoffset="0"></circle>
					';

		if ( 0 != $this->settings->number ) {
			$html .= '<circle class="fl-bar" r="' . $radius . '" cx="' . $pos . '" cy="' . $pos . '" fill="transparent" stroke-dasharray="' . $dash . '" stroke-dashoffset="' . $dash . '" transform="rotate(-90 ' . $pos . ' ' . $pos . ')" data-bbtest="sample-lang"></circle>';
		}

		$html .= '</svg></div>';

		echo $html;
	}

	public function get_i18n_number_format() {
		global $wp_locale;

		$format_decimal   = '.';
		$format_thousands = ',';

		if ( $wp_locale ) {
			$i18n_decimal = $wp_locale->number_format['decimal_point'];

			// French and Norwegian uses SPACE (&nbsp;) as thousands separator. Deutsch(Schweiz) uses single quote.
			$i18n_thousand = str_replace( array( '&nbsp;', "'" ), array( ' ', "\\'" ), $wp_locale->number_format['thousands_sep'] );

			if ( ! empty( $i18n_decimal ) ) {
				$format_decimal = $i18n_decimal;
			}

			if ( ! empty( $i18n_thousand ) ) {
				$format_thousands = $i18n_thousand;
			}
		}

		return array(
			'decimal'   => $format_decimal,
			'thousands' => $format_thousands,
		);
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLNumbersModule', array(
	'general' => array( // Tab
		'title'    => __( 'General', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'layout'             => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Only Numbers', 'fl-builder' ),
							'circle'  => __( 'Circle Counter', 'fl-builder' ),
							'bars'    => __( 'Bars Counter', 'fl-builder' ),
						),
						'toggle'  => array(
							'circle' => array(
								'sections' => array( 'circle_bar_style' ),
							),
							'bars'   => array(
								'sections' => array( 'bar_style' ),
								'fields'   => array( 'number_position' ),
							),
						),
					),
					'number_position'    => array(
						'type'    => 'select',
						'label'   => __( 'Number Position', 'fl-builder' ),
						'size'    => '5',
						'help'    => __( 'Where to display the number in relation to the bar.', 'fl-builder' ),
						'options' => array(
							'default' => __( 'Inside Bar', 'fl-builder' ),
							'above'   => __( 'Above Bar', 'fl-builder' ),
							'below'   => __( 'Below Bar', 'fl-builder' ),
							'hidden'  => __( 'Hidden', 'fl-builder' ),
						),
					),
					'number_type'        => array(
						'type'    => 'select',
						'label'   => __( 'Number Type', 'fl-builder' ),
						'default' => 'percent',
						'options' => array(
							'percent'  => __( 'Percent', 'fl-builder' ),
							'standard' => __( 'Standard', 'fl-builder' ),
						),
						'toggle'  => array(
							'standard' => array(
								'fields' => array( 'number_prefix', 'number_suffix' ),
							),
						),
					),
					'number'             => array(
						'type'        => 'unit',
						'label'       => __( 'Number', 'fl-builder' ),
						'size'        => '5',
						'default'     => '100',
						'placeholder' => '100',
						'connections' => array( 'custom_field' ),
						'preview'     => array(
							'type' => 'refresh',
						),
					),
					'max_number'         => array(
						'type'        => 'unit',
						'label'       => __( 'Total', 'fl-builder' ),
						'size'        => '5',
						'connections' => array( 'custom_field' ),
						'preview'     => array(
							'type' => 'refresh',
						),
						'help'        => __( 'The total number of units for this counter. For example, if the Number is set to 250 and the Total is set to 500, the counter will animate to 50%.', 'fl-builder' ),
					),
					'before_number_text' => array(
						'type'        => 'text',
						'label'       => __( 'Text Before Number', 'fl-builder' ),
						'help'        => __( 'Text to appear above the number. Leave it empty for none.', 'fl-builder' ),
						'connections' => array( 'custom_field' ),
						'preview'     => array(
							'type'     => 'text',
							'selector' => '.fl-number-before-text',
						),
					),
					'after_number_text'  => array(
						'type'        => 'text',
						'label'       => __( 'Text After Number', 'fl-builder' ),
						'help'        => __( 'Text to appear after the number. Leave it empty for none.', 'fl-builder' ),
						'connections' => array( 'custom_field' ),
						'preview'     => array(
							'type'     => 'text',
							'selector' => '.fl-number-after-text',
						),
					),
					'number_prefix'      => array(
						'type'  => 'text',
						'label' => __( 'Number Prefix', 'fl-builder' ),
						'help'  => __( 'For example, if your number is US$ 10, your prefix would be "US$ ".', 'fl-builder' ),
					),
					'number_suffix'      => array(
						'type'  => 'text',
						'label' => __( 'Number Suffix', 'fl-builder' ),
						'help'  => __( 'For example, if your number is 10%, your suffix would be "%".', 'fl-builder' ),
					),
					'animation_speed'    => array(
						'type'        => 'unit',
						'label'       => __( 'Animation Speed', 'fl-builder' ),
						'default'     => '1',
						'placeholder' => '1',
						'units'       => array( 'seconds' ),
						'slider'      => array(
							'step' => .5,
							'max'  => 5,
						),
						'help'        => __( 'Number of seconds to complete the animation.', 'fl-builder' ),
					),
					'delay'              => array(
						'type'        => 'unit',
						'label'       => __( 'Animation Delay', 'fl-builder' ),
						'default'     => '1',
						'placeholder' => '1',
						'units'       => array( 'seconds' ),
						'slider'      => array(
							'step' => .5,
							'max'  => 5,
						),
					),
				),
			),
		),
	),
	'style'   => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'text_style'       => array(
				'fields' => array(
					'text_color'        => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_alpha'  => true,
						'show_reset'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-number .fl-number-text .fl-number-before-text, .fl-number .fl-number-text .fl-number-after-text',
							'property' => 'color',
						),
					),
					'text_typography'   => array(
						'type'       => 'typography',
						'label'      => __( 'Text Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-number .fl-number-text .fl-number-before-text, .fl-number .fl-number-text .fl-number-after-text',
						),
					),
					'number_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Number Color', 'fl-builder' ),
						'show_alpha'  => true,
						'show_reset'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-number .fl-number-text .fl-number-string, .fl-number .fl-number-text .fl-number-string span',
							'property' => 'color',
						),
					),
					'number_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Number Typography', 'fl-builder' ),
						'responsive' => true,
						'default'    => array(
							'font_size' => array(
								'unit'   => 'px',
								'length' => '32',
							),
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-number .fl-number-text .fl-number-string, .fl-number .fl-number-text .fl-number-string span',
						),
					),
				),
			),
			'circle_bar_style' => array(
				'title'  => __( 'Circle Bar Styles', 'fl-builder' ),
				'fields' => array(
					'circle_width'      => array(
						'type'    => 'unit',
						'label'   => __( 'Circle Size', 'fl-builder' ),
						'default' => '200',
						'units'   => array( 'px' ),
						'slider'  => array(
							'max' => 300,
						),
						'preview' => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '.fl-number-circle-container',
									'property' => 'max-width',
									'unit'     => 'px',
								),
								array(
									'selector' => '.fl-number-circle-container',
									'property' => 'max-height',
									'unit'     => 'px',
								),
							),
						),

					),
					'circle_dash_width' => array(
						'type'    => 'unit',
						'label'   => __( 'Circle Stroke Size', 'fl-builder' ),
						'default' => '10',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.svg circle',
							'property' => 'stroke-width',
							'unit'     => 'px',
						),
					),
					'circle_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Circle Foreground Color', 'fl-builder' ),
						'default'     => 'f7951e',
						'show_alpha'  => true,
						'show_reset'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.svg .fl-bar',
							'property' => 'stroke',
						),
					),
					'circle_bg_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Circle Background Color', 'fl-builder' ),
						'default'     => 'eaeaea',
						'show_alpha'  => true,
						'show_reset'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.svg .fl-bar-bg',
							'property' => 'stroke',
						),
					),
				),
			),
			'bar_style'        => array(
				'title'  => __( 'Bar Styles', 'fl-builder' ),
				'fields' => array(
					'bar_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Bar Foreground Color', 'fl-builder' ),
						'default'     => 'f7951e',
						'show_alpha'  => true,
						'show_reset'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-number-bar',
							'property' => 'background-color',
						),
					),
					'bar_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Bar Background Color', 'fl-builder' ),
						'default'     => 'eaeaea',
						'show_alpha'  => true,
						'show_reset'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-number-bars-container',
							'property' => 'background-color',
						),
					),
					'bar_height'   => array(
						'type'       => 'unit',
						'label'      => __( 'Bar Height', 'fl-builder' ),
						'units'      => array( 'px' ),
						'default'    => '42',
						'slider'     => true,
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-number-bars-container, {node} .fl-number-bar',
							'property' => 'height',
						),
					),
				),
			),
		),
	),
));
