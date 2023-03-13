<?php

	// Feature Text Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'feature_text_typography',
		'selector'     => ".fl-node-$id .fl-feature-text",
	) );

	// Feature Text Color
	if ( ! empty( $settings->feature_text_color ) ) :
		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-pricing-table-features .fl-pricing-table-feature-item .fl-feature-text",
			'props'    => array(
				'color' => $settings->feature_text_color,
			),
		) );
	endif;

	// Tooltip Text Color
	if ( ! empty( $settings->tooltip_text_color ) ) :
		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-pricing-table-features .fl-pricing-table-feature-item .fl-builder-tooltip .fl-builder-tooltip-text",
			'props'    => array(
				'color' => $settings->tooltip_text_color,
			),
		) );
	endif;

	// Tooltip Background Color
	if ( ! empty( $settings->tooltip_bg_color ) ) :
		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-pricing-table-features .fl-pricing-table-feature-item .fl-builder-tooltip-text",
			'props'    => array(
				'background-color' => $settings->tooltip_bg_color,
			),
		) );
	endif;

	// Tooltip Icon Style - Size
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'tooltip_icon_size',
		'enabled'      => ! empty( $settings->tooltip_icon_size ),
		'selector'     => ".fl-node-$id .fl-pricing-table-features .fl-pricing-table-feature-item .fl-builder-tooltip-icon",
		'prop'         => 'font-size',
		'unit'         => 'px',
	) );

	// Tooltip Icon Style - Color
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-pricing-table-features .fl-pricing-table-feature-item .fl-builder-tooltip-icon",
		'enabled'  => ! empty( $settings->tooltip_icon_color ),
		'props'    => array(
			'color' => $settings->tooltip_icon_color,
		),
	));

	// Feature Icon Style - Size
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'feature_icon_size',
		'enabled'      => ! empty( $settings->feature_icon_size ),
		'selector'     => ".fl-node-$id .fl-feature-icon",
		'prop'         => 'font-size',
		'unit'         => 'px',
	) );

	// Feature Icon Style - Color
	if ( ! empty( $settings->feature_icon_color ) ) :
		FLBuilderCSS::rule( array(
			'selector'  => ".fl-node-$id .fl-pricing-table-features .fl-pricing-table-feature-item .fl-feature-icon",
			'important' => true,
			'props'     => array(
				'color' => $settings->feature_icon_color,
			),
		));
	endif;

	if ( 'no' !== $settings->dual_billing ) :
		?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-payment-frequency {
			margin: 30px auto;
			text-align: center;
		}
		<?php
	endif;

	if ( 'yes' === $settings->show_list_separator && ! empty( $settings->list_separator_line_color ) ) :
		$line_color = empty( $settings->list_separator_line_color ) ? 'rgba(0,0,0,0.15)' : $settings->list_separator_line_color;
		?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-features li {
			border-bottom-style: solid;
			border-bottom-width: 1px;
			border-bottom-color: <?php echo $line_color; ?>;
		}
		<?php
	endif;

	if ( 'equalize' === $settings->column_height ) :
		?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column,
		.fl-node-<?php echo $id; ?> .fl-pricing-table-inner-wrap {
			display: flex;
			flex-direction: column;
			height: 100%;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table-features {
			flex: 1;
		}
		<?php
	endif;

	// Spacing
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-pricing-table .fl-pricing-table-wrap",
		'media'    => 'default',
		'enabled'  => ! empty( $settings->advanced_spacing_right ) || ! empty( $settings->advanced_spacing_left ),
		'props'    => array(
			'padding-top'    => '0',
			'padding-right'  => ! empty( $settings->advanced_spacing_right ) ? $settings->advanced_spacing_right . 'px' : '0',
			'padding-bottom' => '0',
			'padding-left'   => ! empty( $settings->advanced_spacing_left ) ? $settings->advanced_spacing_left . 'px' : '0',
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-pricing-table .fl-pricing-table-wrap",
		'media'    => 'medium',
		'enabled'  => ! empty( $settings->advanced_spacing_right_medium ) || ! empty( $settings->advanced_spacing_left_medium ),
		'props'    => array(
			'padding-top'    => '0',
			'padding-right'  => ! empty( $settings->advanced_spacing_right_medium ) ? $settings->advanced_spacing_right_medium . 'px' : '0',
			'padding-bottom' => '0',
			'padding-left'   => ! empty( $settings->advanced_spacing_left_medium ) ? $settings->advanced_spacing_left_medium . 'px' : '0',
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-pricing-table .fl-pricing-table-wrap",
		'media'    => 'responsive',
		'enabled'  => ! empty( $settings->advanced_spacing_right_responsive ) || ! empty( $settings->advanced_spacing_left_responsive ),
		'props'    => array(
			'padding-top'    => '0',
			'padding-right'  => ! empty( $settings->advanced_spacing_right_responsive ) ? $settings->advanced_spacing_right_responsive . 'px' : '0',
			'padding-bottom' => '0',
			'padding-left'   => ! empty( $settings->advanced_spacing_left_responsive ) ? $settings->advanced_spacing_left_responsive . 'px' : '0',
		),
	) );
	?>
	<?php
	// Legacy Border
	if ( empty( $settings->border_type ) || 'legacy' === $settings->border_type ) :
		?>

		/*Curvy Boxes*/
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-rounded .fl-pricing-table-column {
			-webkit-border-radius: 6px;
			-moz-border-radius: 6px;
			border-radius: 6px;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-rounded .fl-pricing-table-inner-wrap {
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
		}

		/*Large*/
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-large .fl-pricing-table-inner-wrap {
			margin: 12px;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-large.fl-pricing-table-column-height-equalize .fl-pricing-table-column {
			/* padding-bottom: 24px; */
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-large .fl-pricing-table-column .fl-pricing-table-price {
			margin: 0 -15px;
		}
		/*adjust for no spacing*/
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-large.fl-pricing-table-spacing-none .fl-pricing-table-column .fl-pricing-table-price {
			margin: 0 -14px;
		}

		/*Medium*/
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-medium .fl-pricing-table-inner-wrap {
			margin: 6px;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-medium.fl-pricing-table-column-height-equalize .fl-pricing-table-column {
			/* padding-bottom: 12px; */
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-medium .fl-pricing-table-column .fl-pricing-table-price {
			margin: 0 -9px;
		}

		/*Small Border*/
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-small .fl-pricing-table-column {
			border: 0 !important;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-small .fl-pricing-table-inner-wrap {
			margin: 0px;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table.fl-pricing-table-border-small .fl-pricing-table-column .fl-pricing-table-price {
			margin: 0 -1px;
		}
	<?php elseif ( 'standard' === $settings->border_type ) : ?>

		.fl-node-<?php echo $id; ?> .fl-pricing-table .fl-pricing-table-inner-wrap {
			margin: 0;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-box {
			display: flex;
			flex-direction: column;
			height: 100%;
		}
		<?php
		FLBuilderCSS::border_field_rule( array(
			'settings'     => $settings,
			'setting_name' => 'standard_border',
			'selector'     => ".fl-node-$id .fl-pricing-table .fl-pricing-table-inner-wrap",
		) );

		// Border Width
		if ( ! empty( $settings->standard_border['width'] ) ) :
			$border_width          = $settings->standard_border['width'];
			$price_bar_margin_left = '';
			if ( ! empty( $border_width['left'] ) ) {
				$price_bar_margin_left = 'margin-left: -' . ( $border_width['left'] ) . 'px;';
			}

			$price_bar_margin_right = '';
			if ( ! empty( $border_width['right'] ) ) {
				$price_bar_margin_right = 'margin-right: -' . ( $border_width['right'] ) . 'px;';
			}
			?>
			.fl-node-<?php echo $id; ?> .fl-pricing-table .fl-pricing-table-column .fl-pricing-table-price {
				<?php
				echo $price_bar_margin_left;
				echo $price_bar_margin_right;
				?>
			}
			<?php
		endif;

		// Border Radius
		if ( ! empty( $settings->standard_border['radius'] ) ) :
			$standard_border_rad = $settings->standard_border['radius'];
			$border_radius       = array(
				'top_left'     => empty( $standard_border_rad['top_left'] ) ? '0' : ( 'border-top-left-radius: ' . $standard_border_rad['top_left'] . 'px;' ),
				'top_right'    => empty( $standard_border_rad['top_right'] ) ? '0' : ( 'border-top-right-radius: ' . $standard_border_rad['top_right'] . 'px;' ),
				'bottom_left'  => empty( $standard_border_rad['bottom_left'] ) ? '0' : ( 'border-bottom-left-radius: ' . $standard_border_rad['bottom_left'] . 'px;' ),
				'bottom_right' => empty( $standard_border_rad['bottom_right'] ) ? '0' : ( 'border-bottom-right-radius: ' . $standard_border_rad['bottom_right'] . 'px;' ),
			);
			?>
			.fl-node-<?php echo $id; ?> .fl-pricing-table .fl-pricing-table-column {
			<?php
			foreach ( $border_radius as $br ) {
				echo $br;
			}
			?>
			}
			<?php
		endif;

		if ( 'equalize' === $settings->column_height ) :
			?>
			.fl-node-<?php echo $id; ?> .fl-pricing-table .fl-pricing-table-features {
				padding-bottom: 30px;
			}
			<?php
		endif;

	endif;
	?>

/* Features Min Height */
<?php if ( 'auto' === $settings->column_height && ! empty( $settings->min_height ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-pricing-table-features  {
	min-height: <?php echo $settings->min_height; ?>px;
}
<?php endif; ?>

<?php if ( 'no' != $settings->dual_billing && ! empty( $settings->billing_option_1_btn_color ) ) : ?>
.fl-node-<?php echo $id; ?> .slider.first_option {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->billing_option_1_btn_color ); ?>;
}
<?php endif; ?>

<?php if ( 'no' != $settings->dual_billing && ! empty( $settings->billing_option_2_btn_color ) ) : ?>
.fl-node-<?php echo $id; ?> .slider.second_option {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->billing_option_2_btn_color ); ?>;
}
<?php endif; ?>
<?php
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'switch_typography',
	'enabled'      => 'no' !== $settings->dual_billing,
	'selector'     => ".fl-node-$id span.first_option, .fl-node-$id span.second_option",
) );
?>
<?php if ( 'no' !== $settings->dual_billing && ! empty( $settings->switch_label_color ) ) : ?>
	.fl-node-<?php echo $id; ?> span.first_option,
	.fl-node-<?php echo $id; ?> span.second_option {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->switch_label_color ); ?>;
	}
<?php endif; ?>

<?php
// Loop through and style each pricing box
$total_pricing_cols = count( $settings->pricing_columns );
for ( $i = 0; $i < $total_pricing_cols; $i++ ) :

	if ( ! is_object( $settings->pricing_columns[ $i ] ) ) {
		continue;
	}

	// Pricing Box Settings
	$pricing_column = $settings->pricing_columns[ $i ];

	?>

	/*Pricing Box Style*/
	<?php
	$box_border_color = empty( $pricing_column->background ) ? '#f2f2f2' : $pricing_column->background;
	if ( ! empty( $settings->border_type ) && 'legacy' === $settings->border_type ) :
		$box_border_color = empty( $pricing_column->background ) ? '#f2f2f2' : $pricing_column->background;

		FLBuilderCSS::responsive_rule( array(
			'settings'     => $pricing_column,
			'setting_name' => 'margin',
			'selector'     => ".fl-node-$id .fl-pricing-table-column-$i",
			'prop'         => 'margin-top',
			'unit'         => 'px',
		) );
		?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> {
			border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $box_border_color, 30, 'darken' ) ); ?>;
			background: <?php echo FLBuilderColor::hex_or_rgb( $box_border_color ); ?>;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-inner-wrap {
			border-width: 1px;
			border-style: solid;
			border-color: <?php echo FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $box_border_color, 30, 'darken' ) ); ?>;
		}
	<?php endif; ?>

	.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-inner-wrap {
		background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->foreground ); ?>;
	}

	<?php if ( ! empty( $pricing_column->title_color ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> h2 {
			color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->title_color ); ?>;
		}
	<?php endif; ?>

	<?php if ( empty( $pricing_column->title_typography->font_size->length ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> h2 {
			font-size: <?php echo ( empty( $pricing_column->title_size ) ? '24' : $pricing_column->title_size ); ?>px;
		}
	<?php endif; ?>
	<?php
		FLBuilderCSS::typography_field_rule( array(
			'settings'     => $pricing_column,
			'setting_name' => 'title_typography',
			'selector'     => ".fl-node-$id .fl-pricing-table-column-$i h2.fl-pricing-table-title",
		) );
	?>
	<?php if ( empty( $pricing_column->price_typography->font_size->length ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
			font-size: <?php echo ( empty( $pricing_column->price_size ) ? '31' : $pricing_column->price_size ); ?>px;
		}
	<?php endif; ?>
	<?php
		FLBuilderCSS::typography_field_rule( array(
			'settings'     => $pricing_column,
			'setting_name' => 'price_typography',
			'selector'     => ".fl-node-$id .fl-pricing-table-column-$i .fl-pricing-table-price",
		) );
	?>

	/*Pricing Box Highlight*/
	<?php if ( 'price' == $settings->highlight ) : ?>
	.fl-node-<?php echo $id; ?> .fl-pricing-table .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
		background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?>;
		color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_color ); ?>;
	}
	<?php elseif ( 'title' == $settings->highlight ) : ?>

	.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-title {
		background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?>;
		color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_color ); ?>;
	}
	<?php endif; ?>

	/*Fix when price is NOT highlighted*/
	<?php if ( 'title' == $settings->highlight || 'none' == $settings->highlight ) : ?>
	.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
		margin-bottom: 0;
		padding-bottom: 0;
	}
	.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-features {
		margin-top: 10px;
	}
	<?php endif; ?>

	/*Fix when NOTHING is highlighted*/
	<?php if ( 'none' == $settings->highlight ) : ?>
	.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-title {
		padding-bottom: 0;
	}
	.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
		padding-top: 0;
	}
	<?php endif; ?>

	/* Button CSS */
	.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> a.fl-button {

		<?php if ( empty( $pricing_column->btn_bg_color ) ) : ?>
			background-color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?> !important;
			border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?> !important;
		<?php endif; ?>

		<?php if ( empty( $pricing_column->btn_width ) ) : ?>
			display:block;
			margin: 0 30px 5px;
		<?php endif; ?>
	}

	<?php if ( 'yes' === $pricing_column->show_ribbon ) : ?>
		<?php
			$ribbon_content_height = intval( $pricing_column->ribbon_height );
			$ribbon_side_offset    = intval( $pricing_column->ribbon_side_offset );
		?>
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon-box {
			overflow: <?php echo ( 'top' === $pricing_column->ribbon_position ) ? 'initial' : 'hidden'; ?>;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon {
			width: 100%;
			height: 160px;
			position: absolute;
		}
		/* Ribbon CSS */
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon .fl-pricing-ribbon-content {
			color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->ribbon_text_color ); ?>;
			background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->ribbon_bg_color ); ?>;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 16px;
			position: absolute;
			z-index: 3;
			text-align: center;
			width: 100%;
			/* Make height adjustable */
			height: <?php echo $ribbon_content_height; ?>px;
			max-height: <?php echo $ribbon_content_height; ?>px;
			/* Half of the height */
			margin-top: -<?php echo ceil( $ribbon_content_height / 2 ); ?>px;
		}
		.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon .fl-pricing-ribbon-content span {
			display: inline-block;
			max-width: 150px;
			overflow: hidden;
		}
		<?php if ( 'top' === $pricing_column->ribbon_position ) : ?>
			/* Riboon Position: Top */
			.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon-top .fl-pricing-ribbon-content {
				position: relative;
				width: 50%;
				margin-top: <?php echo intval( $pricing_column->ribbon_top_margin ); ?>px;
				margin-left: auto;
				margin-right: auto;
			}
			<?php
				$top_ribbon_padding           = array();
				$top_ribbon_padding['top']    = empty( $pricing_column->top_ribbon_padding_top ) ? '0' : $pricing_column->top_ribbon_padding_top;
				$top_ribbon_padding['bottom'] = empty( $pricing_column->top_ribbon_padding_bottom ) ? '0' : $pricing_column->top_ribbon_padding_bottom;
				$top_ribbon_padding['left']   = empty( $pricing_column->top_ribbon_padding_left ) ? '0' : $pricing_column->top_ribbon_padding_left;
				$top_ribbon_padding['right']  = empty( $pricing_column->top_ribbon_padding_right ) ? '0' : $pricing_column->top_ribbon_padding_right;
				$top_ribbon_padding_unit      = empty( $pricing_column->top_ribbon_padding_unit ) ? 'px' : $pricing_column->top_ribbon_padding_unit;
			?>
			.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon-content {
				padding-top: <?php echo $top_ribbon_padding['top'] . $top_ribbon_padding_unit; ?>;
				padding-bottom: <?php echo $top_ribbon_padding['bottom'] . $top_ribbon_padding_unit; ?>;
				padding-left: <?php echo $top_ribbon_padding['left'] . $top_ribbon_padding_unit; ?>;
				padding-right: <?php echo $top_ribbon_padding['right'] . $top_ribbon_padding_unit; ?>;
			}
			<?php
				FLBuilderCSS::border_field_rule( array(
					'settings'     => $pricing_column,
					'setting_name' => 'top_ribbon_border',
					'selector'     => ".fl-node-$id .fl-pricing-table-column-$i .fl-pricing-ribbon-content",
				) );
			?>
		<?php endif; ?>
		<?php if ( 'top-left' === $pricing_column->ribbon_position ) : ?>
			/* Ribbon Position: Top Left */
			.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon-top-left {
				top: 0px;
				left: 0px;
				margin-top: <?php echo $ribbon_side_offset; ?>px;
				margin-left: <?php echo $ribbon_side_offset; ?>px;
			}
			.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon-top-left .fl-pricing-ribbon-content {
				transform: translate(-50%) rotate(-45deg);
				left: 0px;
				top: 0px;
			}
		<?php endif; ?>
		<?php if ( 'top-right' === $pricing_column->ribbon_position ) : ?>
			/* Ribbon Position: Top Right */
			.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon-top-right {
				top: 0px;
				right: 0px;
				margin-top: <?php echo $ribbon_side_offset; ?>px;
				margin-right: <?php echo $ribbon_side_offset; ?>px;
			}
			.fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-ribbon-top-right .fl-pricing-ribbon-content {
				transform: translate(50%) rotate(45deg);
				right: 0px;
				top: 0px;
			}
		<?php endif; ?>
	<?php endif; ?>
	<?php

	FLBuilder::render_module_css( 'button', $id . ' .fl-pricing-table-column-' . $i, $module->get_button_settings( $pricing_column ) );

	// Check each feature in the column to see if there's something to override.
	$list_index = 0;

	if ( ! empty( $pricing_column->extended_features ) ) {

		foreach ( $pricing_column->extended_features as $feature ) :

			// Feature Item Text Color
			if ( ! empty( $pricing_column->feature_item_text_color ) ) :
				FLBuilderCSS::rule( array(
					'selector' => ".fl-node-$id .fl-pricing-table-column-$i .feature-item-$list_index .fl-feature-text",
					'props'    => array(
						'color' => $pricing_column->feature_item_text_color,
					),
				) );
			endif;

			// Feature Item Icon Color
			if ( ! empty( $pricing_column->feature_item_icon_color ) ) :
				FLBuilderCSS::rule( array(
					'selector' => ".fl-node-$id .fl-pricing-table-column-$i .feature-item-$list_index .fl-feature-icon",
					'props'    => array(
						'color' => $pricing_column->feature_item_icon_color,
					),
				));
			endif;

			// Feature Item Tooltip Icon Color
			if ( ! empty( $pricing_column->pbox_tooltip_icon_color ) ) :
				FLBuilderCSS::rule( array(
					'selector' => ".fl-node-$id .fl-pricing-table-column-$i .feature-item-$list_index .fl-builder-tooltip-icon",
					'props'    => array(
						'color' => $pricing_column->pbox_tooltip_icon_color,
					),
				));
			endif;

			// Tooltip Text Color
			if ( ! empty( $pricing_column->tooltip_text_color ) ) :
				FLBuilderCSS::rule( array(
					'selector' => ".fl-node-$id .fl-pricing-table-column-$i .feature-item-$list_index .fl-builder-tooltip .fl-builder-tooltip-text",
					'props'    => array(
						'color' => $pricing_column->tooltip_text_color,
					),
				) );
			endif;

			// Tooltip Background Color
			if ( ! empty( $pricing_column->tooltip_bg_color ) ) :
				FLBuilderCSS::rule( array(
					'selector' => ".fl-node-$id .fl-pricing-table-column-$i .feature-item-$list_index .fl-builder-tooltip-text",
					'props'    => array(
						'background-color' => $pricing_column->tooltip_bg_color,
					),
				) );
			endif;

			$list_index++;

		endforeach;

	}

	?>

<?php endfor; ?>
