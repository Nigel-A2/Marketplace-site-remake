<?php

define( 'FL_PRICING_TABLE_URL', FL_BUILDER_URL . 'modules/pricing-table/' );

/**
 * @class FLPricingTableModule
 */
class FLPricingTableModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Pricing Table', 'fl-builder' ),
			'description'     => __( 'A simple pricing table generator.', 'fl-builder' ),
			'category'        => __( 'Layout', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'editor-table.svg',
		));

		// Register custom fields.
		add_filter( 'fl_builder_custom_fields', function( $fields ) {
			$fields['fl-price-feature'] = __DIR__ . '/fields/fl-price-feature.php';
			return $fields;
		} );

	}

	/**
	 * since TBD
	 * @method register_features_field
	 */

	/**
	 * since TBD
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
			wp_enqueue_style( 'fl-price-feature-field', FL_PRICING_TABLE_URL . 'css/price-feature.css', array(), '' );
		}
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

		// For Pricing Modules made before version 2.5, set the Border Type = 'legacy'.
		if ( empty( $settings->border_type ) ) {
			$settings->border_type = 'legacy';
		}

		// For PT Modules made before version 2.5, set the Column Height = 'auto'.
		if ( empty( $settings->column_height ) ) {
			$settings->column_height = 'auto';
		}

		// Convert 'Spacing' (a select field) to 'Advanced Spacing' (a dimension field).
		if ( ! empty( $settings->spacing ) && ! isset( $settings->advanced_spacing_left ) && ! isset( $settings->advanced_spacing_right ) ) {
			if ( 'large' === $settings->spacing ) {
				$space = '12';
			} elseif ( 'medium' === $settings->spacing ) {
				$space = '6';
			} else {
				$space = '0';
			}
			$settings->advanced_spacing_left  = $space;
			$settings->advanced_spacing_right = $space;
		}
		// Uncomment below to remove the 'spacing' field. For now, just keep it.
		// unset( $settings->spacing );

		// Handle pricing column settings.
		$col_count = count( $settings->pricing_columns );
		for ( $i = 0; $i < $col_count; $i++ ) {

			if ( ! is_object( $settings->pricing_columns[ $i ] ) ) {
				continue;
			}

			$pricing_column = $settings->pricing_columns[ $i ];

			// Rename column field 'tooltip_icon_color' to 'pbox_tooltip_icon_color'
			if ( isset( $settings->pricing_columns[ $i ]->tooltip_icon_color ) ) {
				$settings->pricing_columns[ $i ]->pbox_tooltip_icon_color = $settings->pricing_columns[ $i ]->tooltip_icon_color;
				unset( $settings->pricing_columns[ $i ]->tooltip_icon_color );
			}

			// Handle old link fields.
			if ( isset( $settings->pricing_columns[ $i ]->btn_link_target ) ) {
				$settings->pricing_columns[ $i ]->button_url_target = $settings->pricing_columns[ $i ]->btn_link_target;
				unset( $settings->pricing_columns[ $i ]->btn_link_target );
			}
			if ( isset( $settings->pricing_columns[ $i ]->btn_link_nofollow ) ) {
				$settings->pricing_columns[ $i ]->button_url_nofollow = $settings->pricing_columns[ $i ]->btn_link_nofollow;
				unset( $settings->pricing_columns[ $i ]->btn_link_nofollow );
			}

			// Handle old button module settings.
			$helper->filter_child_module_settings( 'button', $settings->pricing_columns[ $i ], array(
				'btn_3d'                 => 'three_d',
				'btn_style'              => 'style',
				'btn_padding'            => 'padding',
				'btn_padding_top'        => 'padding_top',
				'btn_padding_bottom'     => 'padding_bottom',
				'btn_padding_left'       => 'padding_left',
				'btn_padding_right'      => 'padding_right',
				'btn_mobile_align'       => 'mobile_align',
				'btn_align_responsive'   => 'align_responsive',
				'btn_font_size'          => 'font_size',
				'btn_font_size_unit'     => 'font_size_unit',
				'btn_typography'         => 'typography',
				'btn_bg_color'           => 'bg_color',
				'btn_bg_hover_color'     => 'bg_hover_color',
				'btn_bg_opacity'         => 'bg_opacity',
				'btn_bg_hover_opacity'   => 'bg_hover_opacity',
				'btn_border'             => 'border',
				'btn_border_hover_color' => 'border_hover_color',
				'btn_border_radius'      => 'border_radius',
				'btn_border_size'        => 'border_size',
			) );

			// Convert Price Size to Price Typography.
			if ( ! empty( $pricing_column->price_size ) && empty( $pricing_column->price_typography->font_size->length ) ) {
				if ( ! empty( $pricing_column->price_typography ) ) {
					$settings->pricing_columns[ $i ]->price_typography->font_size->length = empty( $pricing_column->price_size ) ? '31' : $pricing_column->price_size;
					$settings->pricing_columns[ $i ]->price_typography->font_size->unit   = empty( $pricing_column->price_size_unit ) ? 'px' : $pricing_column->price_size_unit;
				}
			}

			// Convert Title Size to Title Typography
			if ( ! empty( $pricing_column->title_size ) && empty( $pricing_column->title_typography->font_size->length ) ) {
				if ( ! empty( $pricing_column->price_typography ) ) {
					$settings->pricing_columns[ $i ]->title_typography->font_size->length = empty( $pricing_column->title_size ) ? '24' : $pricing_column->title_size;
					$settings->pricing_columns[ $i ]->title_typography->font_size->unit   = empty( $pricing_column->title_size_unit ) ? 'px' : $pricing_column->title_size_unit;
				}
			}

			// Convert 'features' field to 'extended_features'.
			$features_empty          = $this->is_features_empty( $pricing_column, 'features' );
			$extended_features_empty = $this->is_features_empty( $pricing_column, 'extended_features' );

			if ( ! $features_empty && $extended_features_empty ) {

				$extended_features = array();

				foreach ( $pricing_column->features as $feature ) {
					$feature_obj              = new stdClass;
					$feature_obj->description = $feature;
					$feature_obj->icon        = '';
					$feature_obj->tooltip     = '';

					$extended_features[] = $feature_obj;
				}

				$settings->pricing_columns[ $i ]->extended_features = $extended_features;

			}
		}

		return $settings;
	}

	/**
	 * Check if the Price Column's 'features' or 'extended_features' is empty.
	 * This field was available prior to version 2.5 and was replaced by 'extended_features'.
	 *
	 * @since 2.5
	 * @method update
	 * @param object $pricing_column
	 * @method string is_features_empty
	 */
	private function is_features_empty( $pricing_column, $key = 'features' ) {
		$is_empty = true;

		if ( ! empty( $pricing_column->{ $key } ) && 'array' === gettype( $pricing_column->{ $key } ) ) {
			$is_empty = ( 1 === count( $pricing_column->{ $key } ) && empty( $pricing_column->{ $key }[0] ) );
		} else {
			$is_empty = empty( $pricing_column->{ $key } );
		}

		return $is_empty;
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @param object $pricing_column
	 * @return array
	 */
	public function get_button_settings( $pricing_column ) {
		$settings = array(
			'link'          => $pricing_column->button_url,
			'link_nofollow' => $pricing_column->button_url_nofollow,
			'link_target'   => $pricing_column->button_url_target,
			'text'          => $pricing_column->button_text,
		);

		foreach ( $pricing_column as $key => $value ) {
			if ( strstr( $key, 'btn_' ) ) {
				$key              = str_replace( 'btn_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Render the CTA button.
	 *
	 * @method render_button
	 */
	public function render_button( $column ) {
		$pricing_column = $this->settings->pricing_columns[ $column ];

		FLBuilder::render_module_html( 'button', $this->get_button_settings( $pricing_column ) );
	}

	/**
	 * Render the Price toggle switch.
	 *
	 * @since 2.5
	 * @method render_toggle_pricing_button
	 */
	public function render_toggle_pricing_button() {
		$settings = $this->settings;
		$html     = '';

		if ( 'yes' === $settings->dual_billing ) {
			$options = array(
				0 => '' === $settings->billing_option_1 ? __( 'Monthly', 'fl-builder' ) : $settings->billing_option_1,
				1 => '' === $settings->billing_option_2 ? __( 'Yearly', 'fl-builder' ) : $settings->billing_option_2,
			);

			$html =
			'<div class="fl-pricing-table-payment-frequency">
				<span class="first_option">' . $options[0] . '</span>
				<label class="fl-builder-switch">
					<input class="switch-button" type="checkbox" aria-label="' . __( 'Switch', 'fl-builder' ) . '">
					<span class="slider round first_option"><i>Switch Billing Option</i></span>
				</label>
				<span class="second_option">' . $options[1] . '</span>
			</div>';
		}

		echo $html;
	}

	/**
	 * Returns the main container class.
	 *
	 * @since 2.5
	 * @method get_pricing_table_class
	 * @param object $settings
	 * @return string
	 */
	public function get_pricing_table_class() {
		$settings = $this->settings;

		$pricing_table_class   = array();
		$pricing_table_class[] = 'fl-pricing-table';

		if ( 'legacy' === $settings->border_type ) {
			$pricing_table_class[] = 'fl-pricing-table-border-' . $settings->border_size;
			$pricing_table_class[] = 'fl-pricing-table-border-type-legacy';
		} elseif ( 'standard' === $settings->border_type ) {
			$pricing_table_class[] = 'fl-pricing-table-border-type-standard';
		}

		$pricing_table_class[] = 'fl-pricing-table-column-height-' . $settings->column_height;
		$pricing_table_class[] = 'fl-pricing-table-' . $settings->border_radius;

		return implode( ' ', $pricing_table_class );
	}


	/**
	 * Render the ribbon HTML markup.
	 *
	 * @since 2.5
	 * @method render_ribbon
	 * @param int $col_index
	 */
	public function render_ribbon( $col_index ) {
		$settings       = $this->settings;
		$pricing_column = $settings->pricing_columns[ $col_index ];

		$html = '';

		if ( 'yes' === $pricing_column->show_ribbon && ! empty( $pricing_column->ribbon_text ) ) {
			$html .= '<div class="fl-pricing-ribbon fl-ribbon-' . $col_index . ' fl-pricing-ribbon-' . $pricing_column->ribbon_position . '">';
			$html .= '<div class="fl-pricing-ribbon-content">';
			$html .= '<span>' . $pricing_column->ribbon_text . '</span>';
			$html .= '</div>';
			$html .= '</div>';
		}

		echo $html;
	}

	/**
	 * Render the title.
	 *
	 * @since 2.5
	 * @method render_title
	 * @param int $col_index
	 */
	public function render_title( $col_index ) {
		$settings       = $this->settings;
		$pricing_column = $settings->pricing_columns[ $col_index ];
		echo '<h2 class="fl-pricing-table-title">' . $pricing_column->title . '</h2>';
	}

	/**
	 * Render the price.
	 *
	 * @since 2.5
	 * @method render_price
	 * @param int $col_index
	 */
	public function render_price( $col_index ) {
		$settings       = $this->settings;
		$pricing_column = $settings->pricing_columns[ $col_index ];

		$html = '<div class="fl-pricing-table-price">';
		if ( 'no' === $settings->dual_billing ) {
			$html .= ' ' . $pricing_column->price . ' ';
			$html .= '<span class="fl-pricing-table-duration">' . $pricing_column->duration . '</span>';
		} elseif ( 'yes' === $settings->dual_billing ) {
			$html .= '<span class="first_option-price">' . $pricing_column->price . '</span>';
			$html .= '<span class="second_option-price">' . $pricing_column->price_option_2 . '</span>';
		}
		$html .= '</div>';

		echo $html;
	}


	/**
	 * Render the HTML markup (ul/li) of the features.
	 *
	 * @since 2.5
	 * @method render_features
	 * @param int $col_index
	 */
	public function render_features( $col_index ) {
		$html  = '<ul class="fl-pricing-table-features" role="list">';
		$html .= $this->get_extended_features_list( $col_index );
		$html .= '</ul>';

		echo $html;
	}

	/**
	 * Return the Price Feature List.
	 *
	 * @since 2.5
	 * @method get_extended_features_list
	 * @param int $col_index
	 * @return string
	 */
	private function get_extended_features_list( $col_index ) {
		$settings       = $this->settings;
		$pricing_column = $settings->pricing_columns[ $col_index ];
		$html           = '';
		$list_index     = 0;

		$extended_features = (array) $pricing_column->extended_features;

		foreach ( $extended_features as $key => $ext_feature ) :

			$feature = (array) $ext_feature;

			$icon = '';
			// Default feature icon
			if ( ! empty( $settings->default_feature_icon ) ) {
				FLBuilderIcons::enqueue_styles_for_icon( $settings->default_feature_icon );
				$icon = '<div class="fl-feature-icon-wrapper"><i class="fl-feature-icon ' . $settings->default_feature_icon . '" aria-hidden="true"></i></div>';
			}

			// Override default feature icon?
			if ( ! empty( $feature['icon'] ) ) {
				FLBuilderIcons::enqueue_styles_for_icon( $feature['icon'] );
				$icon = '<div class="fl-feature-icon-wrapper"><i class="fl-feature-icon ' . $feature['icon'] . '" aria-hidden="true"></i></div>';
			}

			$description = '';
			if ( ! empty( $feature['description'] ) ) {
				$description = '<div class="fl-feature-text">' . $feature['description'] . '</div>';
			}

			$tooltip_icon = empty( $settings->default_feature_tooltip_icon ) ? 'fas fa-question-circle' : $settings->default_feature_tooltip_icon;

			$tooltip = '';
			if ( ! empty( $feature['tooltip'] ) ) {
				FLBuilderIcons::enqueue_styles_for_icon( $feature['tooltip'] );
				$tooltip  = '<div class="fl-builder-tooltip"><i class="fl-builder-tooltip-icon ' . esc_attr( $tooltip_icon ) . '" aria-hidden="true"></i>';
				$tooltip .= '<div class="fl-builder-tooltip-text" style="display: none;">';
				$tooltip .= esc_html( $feature['tooltip'] );
				$tooltip .= '</div></div>';
			}

			// $feature = $icon . $description . $tooltip;
			$html .= '<li role="listitem" class="feature-item-' . $list_index . '"><div class="fl-pricing-table-feature-item">' . $icon . $description . $tooltip . '</div></li>';

			$list_index++;

		endforeach;

		return $html;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLPricingTableModule', array(
	'columns' => array(
		'title'    => __( 'Pricing Boxes', 'fl-builder' ),
		'sections' => array(
			'pricing_options_section' => array(
				'title'  => __( 'Pricing Options', 'fl-builder' ),
				'fields' => array(
					'pricing_columns' => array(
						'type'         => 'form',
						'label'        => __( 'Pricing Box', 'fl-builder' ),
						'form'         => 'pricing_column_form',
						'preview_text' => 'title',
						'multiple'     => true,
					),
				),
			),
			'general'                 => array(
				'title'  => 'General',
				'fields' => array(
					'dual_billing'     => array(
						'type'    => 'select',
						'label'   => __( 'Enable Dual Billing?', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields'   => array( 'billing_option_1', 'billing_option_2' ),
								'sections' => array( 'switch_button_style' ),
							),
						),
					),
					'billing_option_1' => array(
						'type'        => 'text',
						'label'       => __( 'Billing Option 1', 'fl-builder' ),
						'default'     => __( 'Monthly', 'fl-builder' ),
						'placeholder' => __( 'Monthly', 'fl-builder' ),
					),
					'billing_option_2' => array(
						'type'        => 'text',
						'label'       => __( 'Billing Option 2', 'fl-builder' ),
						'default'     => __( 'Yearly', 'fl-builder' ),
						'placeholder' => __( 'Yearly', 'fl-builder' ),
					),
				),
			),
			'icons_section'           => array(
				'title'     => __( 'Icons', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'default_feature_icon'         => array(
						'type'        => 'icon',
						'label'       => __( 'Default Feature Icon', 'fl-builder' ),
						'show_remove' => true,
						'help'        => __( 'Icon can be overridden in the individual feature options.', 'fl-builder' ),
					),
					'default_feature_tooltip_icon' => array(
						'type'        => 'icon',
						'label'       => __( 'Feature Tooltip Icon', 'fl-builder' ),
						'default'     => 'fas fa-question-circle',
						'show_remove' => true,
						'help'        => __( 'If not specified, the "Question Mark" icon will be used.', 'fl-builder' ),
					),
				),
			),
		),
	),
	'style'   => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'general'              => array(
				'title'  => 'General Style',
				'fields' => array(
					'highlight'        => array(
						'type'    => 'select',
						'label'   => __( 'Highlight', 'fl-builder' ),
						'default' => 'price',
						'options' => array(
							'price' => __( 'Price', 'fl-builder' ),
							'title' => __( 'Title', 'fl-builder' ),
							'none'  => __( 'None', 'fl-builder' ),
						),
					),
					'column_height'    => array(
						'type'    => 'select',
						'label'   => __( 'Column Height', 'fl-builder' ),
						'default' => '',    // See filter_settings() method.
						'options' => array(
							'equalize' => __( 'Equalize', 'fl-builder' ),
							'auto'     => __( 'Auto', 'fl-builder' ),
						),
						'toggle'  => array(
							'auto' => array(
								'fields' => array(
									'min_height',
								),
							),
						),
						'help'    => __( '"Equalize" sets the columns to have the same height as the largest column.', 'fl-builder' ),
					),
					'min_height'       => array(
						'type'    => 'unit',
						'label'   => __( 'Features Min Height', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px' ),
						'slider'  => array(
							'max'  => 1000,
							'step' => 10,
						),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '.fl-pricing-table-features',
							'property'  => 'min-height',
							'unit'      => 'px',
							'important' => true,
						),
						'help'    => __( 'Use this to normalize the height of your boxes when they have different numbers of features.', 'fl-builder' ),
					),
					'advanced_spacing' => array(
						'type'       => 'dimension',
						'label'      => __( 'Advanced Spacing', 'fl-builder' ),
						'default'    => '12',
						'units'      => array( 'px' ),
						'slider'     => true,
						'responsive' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
				),
			),
			'border_style_section' => array(
				'title'     => 'Border',
				'collapsed' => true,
				'fields'    => array(
					'border_type'     => array(
						'type'    => 'select',
						'label'   => __( 'Border Type', 'fl-builder' ),
						'default' => '',    // See filter_setting() method.
						'options' => array(
							'standard' => __( 'Standard', 'fl-builder' ),
							'legacy'   => __( 'Legacy', 'fl-builder' ),
						),
						'toggle'  => array(
							'legacy'   => array(
								'fields' => array( 'border_radius', 'border_size' ),
							),
							'standard' => array(
								'fields' => array( 'standard_border' ),
							),
						),
					),
					'border_radius'   => array(
						'type'    => 'select',
						'label'   => __( 'Border Style', 'fl-builder' ),
						'default' => 'rounded',
						'options' => array(
							'rounded'  => __( 'Rounded', 'fl-builder' ),
							'straight' => __( 'Straight', 'fl-builder' ),
						),
					),
					'border_size'     => array(
						'type'    => 'select',
						'label'   => __( 'Border Size', 'fl-builder' ),
						'default' => 'wide',
						'options' => array(
							'large'  => _x( 'Large', 'Border size.', 'fl-builder' ),
							'medium' => _x( 'Medium', 'Border size.', 'fl-builder' ),
							'small'  => _x( 'Small', 'Border size.', 'fl-builder' ),
						),
					),
					'standard_border' => array(
						'type'    => 'border',
						'label'   => 'Standard Border',
						'default' => array(
							'style'  => 'solid',
							'color'  => 'f2f2f2',
							'width'  => array(
								'top'    => '1',
								'bottom' => '1',
								'left'   => '1',
								'right'  => '1',
							),
							'radius' => array(
								'top_left'     => '6',
								'top_right'    => '6',
								'bottom_left'  => '6',
								'bottom_right' => '6',
							),
						),
						'preview' => array(
							'type' => 'refresh',
						),
					),
				),
			),
			'feature_list_section' => array(
				'title'     => __( 'Feature List Style', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'show_list_separator'       => array(
						'type'    => 'select',
						'label'   => __( 'Show List Separator', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'list_separator_line_color' ),
							),
						),
					),
					'list_separator_line_color' => array(
						'type'       => 'color',
						'label'      => __( 'Separator Line Color', 'fl-builder' ),
						'default'    => 'rgba(0,0,0,0.15)',
						'show_reset' => true,
						'show_alpha' => true,
					),
					'feature_icon_size'         => array(
						'type'       => 'unit',
						'label'      => __( 'Feature Icon Size', 'fl-builder' ),
						'default'    => '',
						'maxlength'  => '2',
						'size'       => '3',
						'sanitize'   => 'absint',
						'slider'     => true,
						'units'      => array(
							'px',
						),
						'slider'     => array(
							'px' => array(
								'min'  => 10,
								'max'  => 100,
								'step' => 1,
							),
						),
						'responsive' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'feature_icon_color'        => array(
						'type'       => 'color',
						'label'      => __( 'Feature Icon Color', 'fl-builder' ),
						'default'    => '808080',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'feature_text_color'        => array(
						'type'       => 'color',
						'label'      => __( 'Feature Text Color', 'fl-builder' ),
						'default'    => '808080',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'feature_text_typography'   => array(
						'type'       => 'typography',
						'label'      => __( 'Feature List Text Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-feature-text',
						),
						'disabled'   => array( 'default' => array( 'text_align' ) ),
					),
					'tooltip_icon_size'         => array(
						'type'       => 'unit',
						'label'      => __( 'Tooltip Icon Size', 'fl-builder' ),
						'default'    => '',
						'maxlength'  => '2',
						'size'       => '3',
						'sanitize'   => 'absint',
						'slider'     => true,
						'units'      => array(
							'px',
						),
						'slider'     => array(
							'px' => array(
								'min'  => 10,
								'max'  => 100,
								'step' => 1,
							),
						),
						'responsive' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'tooltip_icon_color'        => array(
						'type'       => 'color',
						'label'      => __( 'Tooltip Icon Color', 'fl-builder' ),
						'default'    => '808080',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'tooltip_text_color'        => array(
						'type'       => 'color',
						'label'      => __( 'Tooltip Text Color', 'fl-builder' ),
						'default'    => '333333',
						'show_reset' => true,
						'show_alpha' => true,
					),
					'tooltip_bg_color'          => array(
						'type'       => 'color',
						'label'      => __( 'Tooltip Background Color', 'fl-builder' ),
						'show_reset' => true,
						'show_alpha' => true,
					),
				),
			),
			'switch_button_style'  => array(
				'title'     => 'Toggle Price Button',
				'collapsed' => true,
				'fields'    => array(
					'billing_option_1_btn_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'first_option Price Button Color', 'fl-builder' ),
						'default'     => '#d5d5d5',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.slider.first_option',
							'property'  => 'background',
							'important' => true,
						),
					),
					'billing_option_2_btn_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'second_option Price Button Color', 'fl-builder' ),
						'default'     => '#919293',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.slider.second_option',
							'property'  => 'background',
							'important' => true,
						),
					),
					'switch_label_color'         => array(
						'type'       => 'color',
						'label'      => __( 'Toggle Price Label Color', 'fl-builder' ),
						'default'    => '333333',
						'show_reset' => true,
						'show_alpha' => true,
					),
					'switch_typography'          => array(
						'type'       => 'typography',
						'label'      => __( 'Toggle Price Button Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} span.first_option, {node} span.second_option',
						),
					),
				),
			),
		),
	),
));

FLBuilder::register_settings_form('pricing_column_form', array(
	'title' => __( 'Add Pricing Box', 'fl-builder' ),
	'tabs'  => array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'title'     => array(
					'title'  => __( 'Title', 'fl-builder' ),
					'fields' => array(
						'title' => array(
							'type'  => 'text',
							'label' => __( 'Title', 'fl-builder' ),
						),
					),
				),
				'ribbon'    => array(
					'title'  => __( 'Ribbon', 'fl-builder' ),
					'fields' => array(
						'show_ribbon'     => array(
							'type'    => 'select',
							'label'   => __( 'Show Ribbon', 'fl-builder' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'fl-builder' ),
								'no'  => __( 'No', 'fl-builder' ),
							),
							'toggle'  => array(
								'yes' => array(
									'sections' => array( 'ribbon_style_section' ),
									'fields'   => array( 'ribbon_text', 'ribbon_position' ),
								),
							),
						),
						'ribbon_text'     => array(
							'type'        => 'text',
							'label'       => __( 'Ribbon Text', 'fl-builder' ),
							'default'     => '',
							'connections' => array( 'string' ),
							'placeholder' => __( 'Enter Ribbon Text here.', 'fl-builder' ),
							'help'        => __( 'Nothing will display if left empty.', 'fl-builder' ),
						),
						'ribbon_position' => array(
							'type'    => 'select',
							'label'   => __( 'Ribbon Position', 'fl-builder' ),
							'default' => 'top',
							'options' => array(
								'top'       => __( 'Top', 'fl-builder' ),
								'top-right' => __( 'Top Right', 'fl-builder' ),
								'top-left'  => __( 'Top Left', 'fl-builder' ),
							),
							'toggle'  => array(
								'top'       => array(
									'fields' => array(
										'ribbon_top_margin',
										'top_ribbon_padding',
										'top_ribbon_border',
									),
								),
								'top-right' => array(
									'fields' => array(
										'ribbon_side_offset',
									),
								),
								'top-left'  => array(
									'fields' => array(
										'ribbon_side_offset',
									),
								),
							),
						),
					),
				),
				'price-box' => array(
					'title'  => __( 'Price Box', 'fl-builder' ),
					'fields' => array(
						'price'          => array(
							'type'        => 'text',
							'label'       => __( 'Price', 'fl-builder' ),
							'default'     => '$ 0.00',
							'placeholder' => __( '$ 0.00', 'fl-builder' ),
						),
						'duration'       => array(
							'type'        => 'text',
							'label'       => __( 'Duration', 'fl-builder' ),
							'placeholder' => __( 'per Year', 'fl-builder' ),
						),

						'price_option_2' => array(
							'type'        => 'text',
							'label'       => __( 'Price Option 2', 'fl-builder' ),
							'default'     => '$ 0.00',
							'placeholder' => __( '$ 0.00', 'fl-builder' ),
						),
					),
				),
				'features'  => array(
					'title'  => _x( 'Features', 'Price features displayed in pricing box.', 'fl-builder' ),
					'fields' => array(
						'extended_features' => array(
							'type'                    => 'fl-price-feature',
							'label'                   => __( 'Price Features', 'fl-builder' ),
							'description_placeholder' => __( 'Enter description here.', 'fl-builder' ),
							'tooltip_placeholder'     => __( 'Enter tooltip here.', 'fl-builder' ),
							'multiple'                => true,
						),
					),
				),
			),
		),
		'button'  => array(
			'title'    => __( 'Button', 'fl-builder' ),
			'sections' => array(
				'default'    => array(
					'title'  => '',
					'fields' => array(
						'button_text' => array(
							'type'    => 'text',
							'label'   => __( 'Button Text', 'fl-builder' ),
							'default' => __( 'Get Started', 'fl-builder' ),
						),
						'button_url'  => array(
							'type'          => 'link',
							'label'         => __( 'Button URL', 'fl-builder' ),
							'show_target'   => true,
							'show_nofollow' => true,
							'connections'   => array( 'url' ),
						),
					),
				),
				'btn_icon'   => array(
					'title'     => __( 'Button Icon', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'btn_icon'           => array(
							'type'        => 'icon',
							'label'       => __( 'Button Icon', 'fl-builder' ),
							'show_remove' => true,
							'show'        => array(
								'fields' => array( 'btn_icon_position', 'btn_icon_animation' ),
							),
						),
						'btn_duo_color1'     => array(
							'label'      => __( 'DuoTone Primary Color', 'fl-builder' ),
							'type'       => 'color',
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type'      => 'css',
								'selector'  => '.fl-button-icon.fad:before',
								'property'  => 'color',
								'important' => true,
							),
						),
						'btn_duo_color2'     => array(
							'label'      => __( 'DuoTone Secondary Color', 'fl-builder' ),
							'type'       => 'color',
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type'      => 'css',
								'selector'  => '.fl-button-icon.fad:after',
								'property'  => 'color',
								'important' => true,
							),
						),
						'btn_icon_position'  => array(
							'type'    => 'select',
							'label'   => __( 'Button Icon Position', 'fl-builder' ),
							'default' => 'before',
							'options' => array(
								'before' => __( 'Before Text', 'fl-builder' ),
								'after'  => __( 'After Text', 'fl-builder' ),
							),
						),
						'btn_icon_animation' => array(
							'type'    => 'select',
							'label'   => __( 'Button Icon Visibility', 'fl-builder' ),
							'default' => 'disable',
							'options' => array(
								'disable' => __( 'Always Visible', 'fl-builder' ),
								'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
							),
						),
					),
				),
				'btn_style'  => array(
					'title'     => __( 'Button Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'btn_width'   => array(
							'type'    => 'select',
							'label'   => __( 'Button Width', 'fl-builder' ),
							'default' => 'full',
							'options' => array(
								'auto' => _x( 'Auto', 'Width.', 'fl-builder' ),
								'full' => __( 'Full Width', 'fl-builder' ),
							),
							'toggle'  => array(
								'auto' => array(
									'fields' => array( 'btn_align' ),
								),
							),
						),
						'btn_align'   => array(
							'type'       => 'align',
							'label'      => __( 'Button Align', 'fl-builder' ),
							'default'    => 'center',
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-button-wrap',
								'property' => 'text-align',
							),
						),
						'btn_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Button Padding', 'fl-builder' ),
							'responsive' => true,
							'slider'     => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => 'a.fl-button',
								'property' => 'padding',
							),
						),
					),
				),
				'btn_text'   => array(
					'title'     => __( 'Button Text', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'btn_text_color'       => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Text Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => 'a.fl-button, a.fl-button *',
								'property'  => 'color',
								'important' => true,
							),
						),
						'btn_text_hover_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Text Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => 'a.fl-button:hover, a.fl-button:hover *, a.fl-button:focus, a.fl-button:focus *',
								'property'  => 'color',
								'important' => true,
							),
						),
						'btn_typography'       => array(
							'type'       => 'typography',
							'label'      => __( 'Button Typography', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => 'a.fl-button',
							),
						),
					),
				),
				'btn_colors' => array(
					'title'     => __( 'Button Background', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'btn_bg_color'          => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Background Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'btn_bg_hover_color'    => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Background Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'btn_style'             => array(
							'type'    => 'select',
							'label'   => __( 'Button Background Style', 'fl-builder' ),
							'default' => 'flat',
							'options' => array(
								'flat'     => __( 'Flat', 'fl-builder' ),
								'gradient' => __( 'Gradient', 'fl-builder' ),
							),
						),
						'btn_button_transition' => array(
							'type'    => 'select',
							'label'   => __( 'Button Background Animation', 'fl-builder' ),
							'default' => 'disable',
							'options' => array(
								'disable' => __( 'Disabled', 'fl-builder' ),
								'enable'  => __( 'Enabled', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
				'btn_border' => array(
					'title'     => __( 'Button Border', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'btn_border'             => array(
							'type'       => 'border',
							'label'      => __( 'Button Border', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'      => 'css',
								'selector'  => 'a.fl-button',
								'important' => true,
							),
						),
						'btn_border_hover_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Border Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
		'style'   => array(
			'title'    => __( 'Style', 'fl-builder' ),
			'sections' => array(
				'general_style_section' => array(
					'title'  => __( 'General Style', 'fl-builder' ),
					'fields' => array(
						'background'        => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Box Border', 'fl-builder' ),
							'default'     => 'F2F2F2',
							'show_reset'  => true,
							'show_alpha'  => true,
						),
						'foreground'        => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Box Color', 'fl-builder' ),
							'default'     => 'ffffff',
							'show_reset'  => true,
							'show_alpha'  => true,
						),
						'column_background' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'default'     => '66686b',
							'label'       => __( 'Accent Color', 'fl-builder' ),
							'show_alpha'  => true,
						),
						'column_color'      => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'default'     => 'ffffff',
							'label'       => __( 'Accent Text Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
						),
						'margin'            => array(
							'type'       => 'unit',
							'label'      => __( 'Box Top Margin', 'fl-builder' ),
							'default'    => '0',
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
						),
					),
				),
				'title_style_section'   => array(
					'title'     => __( 'Title Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'title_color'      => array(
							'type'       => 'color',
							'label'      => __( 'Title Color', 'fl-builder' ),
							'default'    => '333333',
							'show_reset' => true,
							'show_alpha' => true,
						),
						'title_typography' => array(
							'type'       => 'typography',
							'label'      => 'Title Typography',
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
					),
				),
				'price_style_section'   => array(
					'title'     => __( 'Price Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'price_typography' => array(
							'type'       => 'typography',
							'label'      => 'Typography',
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
					),
				),
				'ribbon_style_section'  => array(
					'title'     => __( 'Ribbon Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'ribbon_text_color'  => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Ribbon Text Color', 'fl-builder' ),
							'default'     => 'FFFFFF',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'ribbon_bg_color'    => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Ribbon Background Color', 'fl-builder' ),
							'default'     => 'F8463F',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'ribbon_height'      => array(
							'type'    => 'unit',
							'label'   => 'Ribbon Height',
							'default' => '30',
							'units'   => array( 'px' ),
							'slider'  => array(
								'px' => array(
									'min'  => 30,
									'max'  => 200,
									'step' => 5,
								),
							),
						),
						// Show if Ribbon position is 'top'.
						'ribbon_top_margin'  => array(
							'type'    => 'unit',
							'label'   => 'Ribbon Top Margin',
							'default' => '-15',
							'units'   => array( 'px' ),
						),
						'top_ribbon_padding' => array(
							'type'    => 'dimension',
							'label'   => __( 'Top Ribbon Padding', 'fl-builder' ),
							'default' => '0',
							'slider'  => true,
							'units'   => array( 'px' ),
						),
						'top_ribbon_border'  => array(
							'type'    => 'border',
							'label'   => 'Top Ribbon Border',
							'default' => array(
								'style'  => 'solid',
								'color'  => 'd4d4d4',
								'width'  => array(
									'top'    => '1',
									'bottom' => '1',
									'left'   => '1',
									'right'  => '1',
								),
								'radius' => array(
									'top_left'     => '15',
									'top_right'    => '15',
									'bottom_left'  => '15',
									'bottom_right' => '15',
								),
							),
						),
						// Show if Ribbon position is 'top-left' or 'top-right'.
						'ribbon_side_offset' => array(
							'type'    => 'unit',
							'label'   => 'Ribbon Side Offset',
							'default' => '40',
							'units'   => array( 'px' ),
							'slider'  => array(
								'px' => array(
									'min'  => 40,
									'max'  => 200,
									'step' => 5,
								),
							),
						),
					),
				),
				'feature_list_section'  => array(
					'title'     => __( 'Feature List Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'feature_item_icon_color' => array(
							'type'       => 'color',
							'label'      => __( 'Feature Icon Color', 'fl-builder' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type' => 'refresh',
							),
						),
						'feature_item_text_color' => array(
							'type'       => 'color',
							'label'      => __( 'Feature Text Color', 'fl-builder' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type' => 'refresh',
							),
						),
						'pbox_tooltip_icon_color' => array(
							'type'       => 'color',
							'label'      => __( 'Tooltip Icon Color', 'fl-builder' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type' => 'refresh',
							),
						),
						'tooltip_text_color'      => array(
							'type'       => 'color',
							'label'      => __( 'Tooltip Text Color', 'fl-builder' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
						'tooltip_bg_color'        => array(
							'type'       => 'color',
							'default'    => '',
							'label'      => __( 'Tooltip Background Color', 'fl-builder' ),
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
	),
));
