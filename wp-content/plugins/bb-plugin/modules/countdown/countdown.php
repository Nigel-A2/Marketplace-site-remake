<?php

/**
 * @class FLCountdownModule
 */
class FLCountdownModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Countdown', 'fl-builder' ),
			'description'     => __( 'Render a Countdown module.', 'fl-builder' ),
			'category'        => __( 'Info', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'clock.svg',
		));
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

		if ( isset( $settings->label_size ) ) {
			unset( $settings->label_size );
			unset( $settings->label_size_unit );
		}

		if ( isset( $settings->label_size_medium ) ) {
			unset( $settings->label_size_medium );
			unset( $settings->label_size_medium_unit );
		}

		if ( isset( $settings->label_size_responsive ) ) {
			unset( $settings->label_size_responsive );
			unset( $settings->label_size_responsive_unit );
		}

		return $settings;
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {
		// migrate old font size
		if ( isset( $settings->number_size ) && ! empty( $settings->number_size ) ) {
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

		if ( isset( $settings->number_size_medium ) && ! empty( $settings->number_size_medium ) ) {
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

		if ( isset( $settings->number_size_responsive ) && ! empty( $settings->number_size_responsive ) ) {
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

		if ( isset( $settings->label_size ) && ! empty( $settings->label_size ) ) {
			$settings->label_typography = array_merge(
				is_array( $settings->label_typography ) ? $settings->label_typography : array(),
				array(
					'font_size' => array(
						'unit'   => $settings->label_size_unit,
						'length' => $settings->label_size,
					),
				)
			);
		}

		if ( isset( $settings->label_size_medium ) && ! empty( $settings->label_size_medium ) ) {
			$settings->label_typography_medium = array_merge(
				is_array( $settings->label_typography_medium ) ? $settings->label_typography_medium : array(),
				array(
					'font_size' => array(
						'unit'   => $settings->label_size_medium_unit,
						'length' => $settings->label_size_medium,
					),
				)
			);
		}

		if ( isset( $settings->label_size_responsive ) && ! empty( $settings->label_size_responsive ) ) {
			$settings->label_typography_responsive = array_merge(
				is_array( $settings->label_typography_responsive ) ? $settings->label_typography_responsive : array(),
				array(
					'font_size' => array(
						'unit'   => $settings->label_size_responsive_unit,
						'length' => $settings->label_size_responsive,
					),
				)
			);
		}

		// Old opacity setting.
		$helper->handle_opacity_inputs( $settings, 'number_bg_opacity', 'number_bg_color' );

		// if set convert.
		if ( isset( $settings->day ) ) {

			$date = sprintf( '%s/%s/%s', $settings->year, $settings->month, $settings->day );

			$settings->ui_date = gmdate( 'Y-m-d', strtotime( $date ) );

			unset( $settings->day );
			unset( $settings->month );
			unset( $settings->year );
		}

		return $settings;
	}

	/**
	 * Builds an string with the respective ISO formatted time.
	 *
	 * @since 1.6.4
	 * @return string The current timestamp in an ISO format.
	 */
	public function get_time() {

		$date       = gmdate( 'Y-m-d', strtotime( $this->settings->ui_date ) );
		$hours      = isset( $this->settings->time['hours'] ) ? str_pad( $this->settings->time['hours'], 2, '0', STR_PAD_LEFT ) : '00';
		$minutes    = isset( $this->settings->time['minutes'] ) ? str_pad( $this->settings->time['minutes'], 2, '0', STR_PAD_LEFT ) : '00';
		$day_period = isset( $this->settings->time['day_period'] ) ? $this->settings->time['day_period'] : 'AM';
		$zone       = isset( $this->settings->time_zone ) && '' != $this->settings->time_zone ? $this->settings->time_zone : date( 'e', current_time( 'timestamp', 1 ) ); //phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date,WordPress.DateTime.CurrentTimeTimestamp.RequestedUTC
		$time       = date( 'H:i:s', strtotime( $hours . ':' . $minutes . ':00 ' . strtoupper( $day_period ) ) ); //phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date

		$timestamp = $date . ' ' . $time;
		$timezone  = new DateTimeZone( $zone );
		$date      = new DateTime( $timestamp, $timezone );

		return $date->format( 'c' );

	}

	/**
	 * Renders a svg circle for the current number.
	 *
	 * @since 1.6.4
	 * @return void
	 */
	public function render_circle() {

		$width  = ! empty( $this->settings->circle_width ) ? $this->settings->circle_width : 100;
		$pos    = ( $width / 2 );
		$radius = $pos - 10;
		$dash   = number_format( ( ( M_PI * 2 ) * $radius ), 2, '.', '' );

		$html  = '<div class="svg-container">';
		$html .= '<svg class="svg" viewBox="0 0 ' . $width . ' ' . $width . '" version="1.1" preserveAspectRatio="xMinYMin meet">
			<circle class="fl-number-bg" r="' . $radius . '" cx="' . $pos . '" cy="' . $pos . '" fill="transparent" stroke-dasharray="' . $dash . '" stroke-dashoffset="0"></circle>
			<circle class="fl-number" r="' . $radius . '" cx="' . $pos . '" cy="' . $pos . '" fill="transparent" stroke-dasharray="' . $dash . '" stroke-dashoffset="' . $dash . '" transform="rotate(-90 ' . $pos . ' ' . $pos . ')"></circle>
		</svg>';
		$html .= '</div>';

		echo $html;
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLCountdownModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'date' => array(
				'title'  => __( 'Date', 'fl-builder' ),
				'fields' => array(
					'ui_date' => array(
						'type'        => 'date',
						'label'       => __( 'Date', 'fl-builder' ),
						'default'     => gmdate( 'Y/m/d', strtotime( '+7 days' ) ),
						'preview'     => array(
							'type' => 'none',
						),
						'connections' => array( 'custom_field' ),
					),
				),
			),
			'time' => array(
				'title'  => __( 'Time', 'fl-builder' ),
				'fields' => array(
					'time'      => array(
						'type'    => 'time',
						'label'   => __( 'Time', 'fl-builder' ),
						'default' => array(
							'hours'      => '01',
							'minutes'    => '00',
							'day_period' => 'am',
						),
					),
					'time_zone' => array(
						'type'    => 'timezone',
						'label'   => __( 'Time Zone', 'fl-builder' ),
						'default' => 'UTC',
					),
				),
			),

		),
	),
	'style'   => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general'          => array(
				'title'  => '',
				'fields' => array(
					'layout'    => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Numbers', 'fl-builder' ),
							'circle'  => __( 'Numbers + Circles', 'fl-builder' ),
						),
						'toggle'  => array(
							'circle'  => array(
								'sections' => array( 'circle_bar_style' ),
								'fields'   => array( 'after_number_text' ),
							),
							'default' => array(
								'sections' => array( 'numbers_style', 'separator_style' ),
								'fields'   => array( 'horizontal_padding', 'vertical_padding' ),
							),
						),
					),
					'alignment' => array(
						'type'       => 'align',
						'label'      => __( 'Alignment', 'fl-builder' ),
						'default'    => 'center',
						'responsive' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
				),
			),
			'text_style'       => array(
				'title'  => __( 'Numbers and Text', 'fl-builder' ),
				'fields' => array(
					'number_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Number Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-countdown .fl-countdown-unit-number',
							'property' => 'color',
						),
					),
					'number_typography'  => array(
						'type'       => 'typography',
						'label'      => __( 'Number Typography', 'fl-builder' ),
						'responsive' => true,
						'default'    => array(
							'font_size'  => array(
								'unit'   => 'px',
								'length' => '24',
							),
							'text_align' => 'center',
						),
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'label_color'        => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-countdown .fl-countdown-unit-label',
							'property' => 'color',
						),
					),
					'label_typography'   => array(
						'type'       => 'typography',
						'label'      => __( 'Text Typography', 'fl-builder' ),
						'responsive' => true,
						'default'    => array(
							'font_size'  => array(
								'unit'   => 'px',
								'length' => '13',
							),
							'text_align' => 'center',
						),
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'horizontal_padding' => array(
						'type'    => 'unit',
						'label'   => __( 'Horizontal Padding', 'fl-builder' ),
						'default' => '10',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '.fl-countdown .fl-countdown-unit',
									'property' => 'padding-left',
									'unit'     => 'px',
								),
								array(
									'selector' => '.fl-countdown .fl-countdown-unit',
									'property' => 'padding-right',
									'unit'     => 'px',
								),
							),
						),
					),
					'vertical_padding'   => array(
						'type'    => 'unit',
						'label'   => __( 'Vertical Padding', 'fl-builder' ),
						'default' => '10',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '.fl-countdown .fl-countdown-unit',
									'property' => 'padding-top',
									'unit'     => 'px',
								),
								array(
									'selector' => '.fl-countdown .fl-countdown-unit',
									'property' => 'padding-bottom',
									'unit'     => 'px',
								),
							),
						),
					),
					'number_spacing'     => array(
						'type'    => 'unit',
						'label'   => __( 'Number Spacing', 'fl-builder' ),
						'default' => '10',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '.fl-countdown .fl-countdown-number',
									'property' => 'margin-left',
									'unit'     => 'px',
								),
								array(
									'selector' => '.fl-countdown .fl-countdown-number',
									'property' => 'margin-right',
									'unit'     => 'px',
								),
							),
						),
					),
				),
			),
			'numbers_style'    => array(
				'title'  => __( 'Backgrounds', 'fl-builder' ),
				'fields' => array(
					'number_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Number Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-countdown .fl-countdown-unit',
							'property' => 'background-color',
						),
					),
					'border_radius'   => array(
						'type'    => 'unit',
						'label'   => __( 'Number Border Radius', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.fl-countdown .fl-countdown-unit',
							'property' => 'border-radius',
							'unit'     => 'px',
						),
					),
				),
			),
			'separator_style'  => array(
				'title'  => __( 'Separator', 'fl-builder' ),
				'fields' => array(
					'show_separator'  => array(
						'type'    => 'select',
						'label'   => __( 'Show Time Separators', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'separator_type', 'separator_color' ),
							),
						),
					),
					'separator_type'  => array(
						'type'    => 'select',
						'label'   => __( 'Separator Type', 'fl-builder' ),
						'default' => 'line',
						'options' => array(
							'colon' => __( 'Colon', 'fl-builder' ),
							'line'  => __( 'Line', 'fl-builder' ),
						),
						'toggle'  => array(
							'colon' => array(
								'fields' => array( 'separator_size' ),
							),
						),
					),
					'separator_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Separator Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'separator_size'  => array(
						'type'    => 'unit',
						'label'   => __( 'Separator Size', 'fl-builder' ),
						'default' => '15',
						'units'   => array( 'px' ),
						'slider'  => true,
					),
				),
			),
			'circle_bar_style' => array(
				'title'  => __( 'Circle Styles', 'fl-builder' ),
				'fields' => array(
					'circle_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Circle Foreground Color', 'fl-builder' ),
						'default'     => 'f7951e',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-countdown .svg .fl-number',
							'property' => 'stroke',
						),
					),
					'circle_bg_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Circle Background Color', 'fl-builder' ),
						'default'     => 'eaeaea',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-countdown .svg .fl-number-bg',
							'property' => 'stroke',
						),
					),
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
									'selector' => '.fl-countdown-number',
									'property' => 'width',
									'unit'     => 'px',
								),
								array(
									'selector' => '.fl-countdown-number',
									'property' => 'height',
									'unit'     => 'px',
								),
								array(
									'selector' => '.fl-countdown-circle-container',
									'property' => 'max-width',
									'unit'     => 'px',
								),
								array(
									'selector' => '.fl-countdown-circle-container',
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
				),
			),
		),
	),

));
