.fl-node-<?php echo $id; ?> .fl-content-slider-wrapper {
	opacity: 0;
}
.fl-node-<?php echo $id; ?> .fl-content-slider,
.fl-node-<?php echo $id; ?> .fl-slide {
	min-height: <?php echo $settings->height; ?>px;
}
.fl-node-<?php echo $id; ?> .fl-slide-foreground {
	margin: 0 auto;
	max-width: <?php echo $settings->max_width; ?>px;
}
<?php
if ( $settings->arrows ) :
	if ( isset( $settings->arrows_bg_color ) && ! empty( $settings->arrows_bg_color ) ) :
		?>
	.fl-node-<?php echo $id; ?> .fl-content-slider-svg-container {
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->arrows_bg_color ); ?>;
		width: 40px;
		height: 40px;

		<?php if ( isset( $settings->arrows_bg_style ) && 'circle' == $settings->arrows_bg_style ) : ?>
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
		-ms-border-radius: 50%;
		-o-border-radius: 50%;
		border-radius: 50%;
		<?php endif; ?>
	}
	.fl-node-<?php echo $id; ?> .fl-content-slider-navigation svg {
		height: 100%;
		width: 100%;
		padding: 5px;
	}
		<?php
	endif;

	if ( isset( $settings->arrows_text_color ) && ! empty( $settings->arrows_text_color ) ) :
		?>
	.fl-node-<?php echo $id; ?> .fl-content-slider-navigation path {
		fill: <?php echo FLBuilderColor::hex_or_rgb( $settings->arrows_text_color ); ?>;
	}
		<?php
	endif;
endif;

for ( $i = 0; $i < count( $settings->slides ); $i++ ) {

	// Make sure we have a slide.
	if ( ! is_object( $settings->slides[ $i ] ) ) {
		continue;
	}

	// Slide Settings
	$slide = $settings->slides[ $i ];

	// Slide Background Photo
	if ( ! empty( $slide->bg_photo_src ) ) {
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-bg-photo';
		echo '{';
		echo '   background-image: url("' . $slide->bg_photo_src . '");';
		echo '}';
	}

	// Slide Background Photo Color Overlay
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-bg-photo:after",
		'enabled'  => 'photo' === $slide->bg_layout,
		'props'    => array(
			'background-color' => $slide->bg_photo_overlay_color,
			'content'          => '" "',
			'display'          => 'block',
			'position'         => 'absolute',
			'top'              => '0',
			'left'             => '0',
			'right'            => '0',
			'bottom'           => '0',
		),
	) );

	// Slide Background Color
	if ( 'color' == $slide->bg_layout && ! empty( $slide->bg_color ) ) {
		echo '.fl-node-' . $id . ' .fl-slide-' . $i;
		echo ' { background-color: ' . FLBuilderColor::hex_or_rgb( $slide->bg_color ) . '; }';
	}

	// Foreground Photo/Video
	if ( 'photo' == $slide->content_layout || 'video' == $slide->content_layout ) {

		$photo_width = 100 - $slide->text_width;

		// Foreground Photo/Video Width
		if ( 'center' != $slide->text_position ) {
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-photo-wrap ';
			echo '{ width: ' . $photo_width . '%; }';
		}

		// Foreground Photo/Video Margins
		if ( 'left' == $slide->text_position ) {
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-photo ';
			echo '{ margin-right: ' . $slide->text_margin_left . 'px; ';
			echo 'margin-top: ' . $slide->text_margin_top . 'px; ';
			echo 'margin-bottom: ' . $slide->text_margin_bottom . 'px; }';
		} elseif ( 'center' == $slide->text_position ) {
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-photo ';
			echo '{ margin-left: ' . $slide->text_margin_left . 'px; ';
			echo 'margin-right: ' . $slide->text_margin_right . 'px; ';
			echo 'margin-bottom: ' . $slide->text_margin_bottom . 'px; }';
		} elseif ( 'right' == $slide->text_position ) {
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-photo ';
			echo '{ margin-left: ' . $slide->text_margin_right . 'px; ';
			echo 'margin-top: ' . $slide->text_margin_top . 'px; ';
			echo 'margin-bottom: ' . $slide->text_margin_bottom . 'px; }';
		}
	}

	// Text Width and Margins
	if ( 'none' != $slide->content_layout ) {

		// Content wrap width
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-content-wrap ';
		echo '{ width: ' . $slide->text_width . '%; }';

		// Margins
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-content ';
		echo '{ margin-right: ' . $slide->text_margin_right . 'px; ';
		echo 'margin-left: ' . $slide->text_margin_left . 'px; ';

		// 100% height, don't use top/bottom margins
		if ( '100%' == $slide->text_bg_height && ! empty( $slide->text_bg_color ) ) {

			// Content height
			echo ' min-height: ' . $settings->height . 'px; }';

			// Content wrap height
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-content-wrap ';
			echo '{ min-height: ' . $settings->height . 'px; }';
		} else {
			echo 'margin-top: ' . $slide->text_margin_top . 'px; ';
			echo 'margin-bottom: ' . $slide->text_margin_bottom . 'px; }';
		}
	}

	// Text Styles
	if ( 'custom' == $slide->title_size ) {
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-title ';
		echo '{ font-size: ' . $slide->title_custom_size . 'px; }';
	}

	// Text Color
	if ( ! empty( $slide->text_color ) ) {
		echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-title, ';
		echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text, ';
		echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text * ';
		echo '{ color: ' . FLBuilderColor::hex_or_rgb( $slide->text_color ) . '; }';
		echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text strong ';
		echo '{ color: inherit; }';
	}

	// Text BG Color
	if ( ! empty( $slide->text_bg_color ) ) {
		echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-content ';
		echo '{ background-color: ' . FLBuilderColor::hex_or_rgb( $slide->text_bg_color ) . ';';
		echo 'padding-top: ' . $slide->text_padding_top . 'px;';
		echo 'padding-right: ' . $slide->text_padding_right . 'px;';
		echo 'padding-bottom: ' . $slide->text_padding_bottom . 'px;';
		echo 'padding-left: ' . $slide->text_padding_left . 'px;}';
	}

	// Text Shadow
	if ( $slide->text_shadow ) {
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-title, ';
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text ';
		echo '{ text-shadow: 0 0 5px rgba(0,0,0,0.3); }';
	}

	// Responsive Text Styles
	if ( $global_settings->responsive_enabled ) {
		echo '@media (max-width: ' . $global_settings->responsive_breakpoint . 'px) { ';

		// Responsive Text Color
		if ( ! empty( $slide->r_text_color ) ) {
			echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-title, ';
			echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text, ';
			echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text * ';
			echo '{ color: ' . FLBuilderColor::hex_or_rgb( $slide->r_text_color ) . '; }';
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text strong ';
			echo '{ color: inherit; }';
		} else {
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-title, ';
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text, ';
			echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text * ';
			echo '{ color: inherit; }';
		}

		// Responsive Text BG Color
		if ( ! empty( $slide->r_text_bg_color ) ) {
			echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-content ';
			echo '{ background-color: ' . FLBuilderColor::hex_or_rgb( $slide->r_text_bg_color ) . '; }';
		} else {
			echo '.fl-builder-content .fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-content ';
			echo '{ background-color: transparent; }';
		}

		// Responsive Text Shadow
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-title, ';
		echo '.fl-node-' . $id . ' .fl-slide-' . $i . ' .fl-slide-text ';
		echo '{ text-shadow: none; }';

		echo ' }';
	}

	// Button Styles
	if ( 'button' == $slide->cta_type ) :

		if ( ! isset( $slide->btn_style ) ) {
			$slide->btn_style = 'flat';
		}

		FLBuilderCSS::dimension_field_rule( array(
			'settings'     => $slide,
			'unit'         => 'px',
			'setting_name' => 'btn_padding',
			'selector'     => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button",
			'props'        => array(
				'padding-top'    => 'btn_padding_top',
				'padding-right'  => 'btn_padding_right',
				'padding-bottom' => 'btn_padding_bottom',
				'padding-left'   => 'btn_padding_left',
			),
		) );

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button",
			'enabled'  => ! empty( $slide->btn_bg_color ),
			'props'    => array(
				'background-color' => $slide->btn_bg_color,
			),
		) );

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button:hover",
			'enabled'  => ! empty( $slide->btn_bg_hover_color ),
			'props'    => array(
				'background-color' => $slide->btn_bg_hover_color,
			),
		) );

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button span.fl-button-text",
			'enabled'  => ! empty( $slide->btn_text_color ),
			'props'    => array(
				'color' => $slide->btn_text_color,
			),
		) );

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button:hover span.fl-button-text",
			'enabled'  => ! empty( $slide->btn_text_hover_color ),
			'props'    => array(
				'color' => $slide->btn_text_hover_color,
			),
		) );

		if ( 'gradient' == $slide->btn_style ) {
			$bg_grad_start       = '';
			$bg_hover_grad_start = '';
			if ( ! empty( $slide->btn_bg_color ) ) {
				$bg_grad_start = FLBuilderColor::adjust_brightness( $slide->btn_bg_color, 30, 'lighten' );
			}
			if ( ! empty( $slide->btn_bg_hover_color ) ) {
				$bg_hover_grad_start = FLBuilderColor::adjust_brightness( $slide->btn_bg_hover_color, 30, 'lighten' );
			}

			FLBuilderCSS::rule( array(
				'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button",
				'enabled'  => isset( $bg_grad_start ),
				'props'    => array(
					'background' => 'linear-gradient(to bottom, ' . FLBuilderColor::hex_or_rgb( $bg_grad_start ) . ' 0%,' . FLBuilderColor::hex_or_rgb( $slide->btn_bg_color ) . ' 100%)',
				),
			) );

			FLBuilderCSS::rule( array(
				'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button:hover",
				'enabled'  => isset( $bg_hover_grad_start ),
				'props'    => array(
					'background' => 'linear-gradient(to bottom, ' . FLBuilderColor::hex_or_rgb( $bg_hover_grad_start ) . ' 0%,' . FLBuilderColor::hex_or_rgb( $slide->btn_bg_color ) . ' 100%)',
				),
			) );

		}

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button *",
			'enabled'  => ( isset( $slide->btn_button_transition ) && 'enable' == $slide->btn_button_transition ),
			'props'    => array(
				'transition'         => 'all 0.2s linear !important',
				'-moz-transition'    => 'all 0.2s linear !important',
				'-webkit-transition' => 'all 0.2s linear !important',
				'-o-transition'      => 'all 0.2s linear !important',
			),
		) );

		FLBuilderCSS::typography_field_rule( array(
			'settings'     => $slide,
			'setting_name' => 'btn_typography',
			'selector'     => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap",
		) );

		FLBuilderCSS::typography_field_rule( array(
			'settings'     => $slide,
			'setting_name' => 'btn_typography',
			'selector'     => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button",
		) );

		FLBuilderCSS::border_field_rule( array(
			'settings'     => $slide,
			'setting_name' => 'btn_border',
			'selector'     => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button, .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button:hover",
		) );


		FLBuilderCSS::rule( array(
			'enabled'  => ! empty( $slide->btn_border_hover_color ),
			'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button:hover",
			'props'    => array(
				'border-color' => $slide->btn_border_hover_color,
			),
		) );

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-slide-$i .fl-slide-cta-button .fl-button-wrap a.fl-button i.fl-button-icon",
			'enabled'  => ! empty( $slide->btn_text_color ),
			'props'    => array(
				'color' => $slide->btn_text_color,
			),
		) );

		if ( $slide->btn_duo_color1 && false !== strpos( $slide->btn_icon, 'fad fa' ) ) :
			?>
			.fl-node-<?php echo $id; ?> .fl-slide-<?php echo $i; ?> .fl-slide-cta-button .fl-button-wrap a.fl-button i.fl-button-icon.fad:before {
				color: <?php echo FLBuilderColor::hex_or_rgb( $slide->btn_duo_color1 ); ?>;
			}
			<?php
		endif;

		if ( $slide->btn_duo_color2 && false !== strpos( $slide->btn_icon, 'fad fa' ) ) :
			?>
			.fl-node-<?php echo $id; ?> .fl-slide-<?php echo $i; ?> .fl-slide-cta-button .fl-button-wrap a.fl-button i.fl-button-icon.fad:after {
				color: <?php echo FLBuilderColor::hex_or_rgb( $slide->btn_duo_color2 ); ?>;
				opacity: 1;
			}
			<?php
		endif;
	endif; // End Button Style
}
