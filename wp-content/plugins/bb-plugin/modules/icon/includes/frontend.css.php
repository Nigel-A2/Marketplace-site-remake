<?php

$size_value               = array();
$size_value['']           = empty( $settings->size ) ? 30 : $settings->size;
$size_value['medium']     = empty( $settings->size_medium ) ? $size_value[''] : $settings->size_medium;
$size_value['responsive'] = empty( $settings->size_responsive ) ? $size_value['medium'] : $settings->size_responsive;

foreach ( array( '', 'medium', 'responsive' ) as $device ) {

	$key      = empty( $device ) ? 'size' : "size_{$device}";
	$unit_key = "{$key}_unit";

	$size_unit = $settings->{ $unit_key };

	// Font Size
	FLBuilderCSS::rule( array(
		'media'    => $device,
		'selector' => ".fl-node-$id .fl-icon i, .fl-node-$id .fl-icon i:before",
		'props'    => array(
			'font-size' => $size_value[ $device ] . $size_unit,
		),
	) );

	FLBuilderCSS::rule( array(
		'media'    => $device,
		'selector' => ".fl-node-$id .fl-icon-wrap .fl-icon-text",
		'props'    => array(
			'height' => array(
				'value' => $size_value[ $device ] * 1.75,
				'unit'  => $size_unit,
			),
		),
	) );

	if ( $settings->bg_color || $settings->bg_hover_color ) {
		FLBuilderCSS::rule( array(
			'media'    => $device,
			'selector' => ".fl-node-$id .fl-icon i",
			'props'    => array(
				'line-height' => array(
					'value' => $size_value[ $device ] * 1.75,
					'unit'  => $size_unit,
				),
				'width'       => array(
					'value' => $size_value[ $device ] * 1.75,
					'unit'  => $size_unit,
				),
			),
		) );
		FLBuilderCSS::rule( array(
			'media'    => $device,
			'selector' => ".fl-node-$id .fl-icon i::before",
			'props'    => array(
				'line-height' => array(
					'value' => $size_value[ $device ] * 1.75,
					'unit'  => $size_unit,
				),
			),
		) );
	}
}

// Overall Alignment
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'align',
	'selector'     => ".fl-node-$id.fl-module-icon",
	'prop'         => 'text-align',
) );

// Text Spacing
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-icon-text",
	'props'    => array(
		'padding-left' => array(
			'value' => $settings->text_spacing,
			'unit'  => 'px',
		),
	),
) );

// Text Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-builder-content .fl-node-$id .fl-icon-wrap .fl-icon-text, .fl-builder-content .fl-node-$id .fl-icon-wrap .fl-icon-text-link *",
	'props'    => array(
		'color' => $settings->text_color,
	),
) );

if ( ! empty( $settings->text_color ) ) : ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-icon-wrap .fl-icon-text,
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-icon-wrap .fl-icon-text * {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
	}
	<?php
endif;

// Text Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-icon-text, .fl-node-$id .fl-icon-text-link",
	'setting_name' => 'text_typography',
	'settings'     => $settings,
) );

// Background and border colors
if ( $settings->three_d ) {
	$bg_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_color, 30, 'lighten' );
	$border_color  = FLBuilderColor::adjust_brightness( $settings->bg_color, 20, 'darken' );
}
if ( $settings->three_d && ! empty( $settings->bg_hover_color ) ) {
	$bg_hover_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_hover_color, 30, 'lighten' );
	$border_hover_color  = FLBuilderColor::adjust_brightness( $settings->bg_hover_color, 20, 'darken' );
}

?>
<?php if ( $settings->color && false === strpos( $settings->icon, 'fad fa' ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-icon i,
.fl-node-<?php echo $id; ?> .fl-icon i:before {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->color ); ?>;
}
<?php endif; ?>

<?php if ( $settings->duo_color1 && false !== strpos( $settings->icon, 'fad fa' ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-icon i,
.fl-node-<?php echo $id; ?> .fl-icon i:before {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->duo_color1 ); ?>;
}
<?php endif; ?>

<?php if ( $settings->duo_color2 && false !== strpos( $settings->icon, 'fad fa' ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-icon i:after {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->duo_color2 ); ?>;
	opacity: 1;
}
<?php endif; ?>

<?php if ( $settings->bg_color ) : ?>
.fl-node-<?php echo $id; ?> .fl-icon i {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?>;
	<?php if ( $settings->three_d ) : ?>
	background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $bg_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?> 100%);
	border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( $border_color ); ?>;
	<?php endif; ?>
}
<?php endif; ?>
<?php if ( ! empty( $settings->hover_color ) && false === strpos( $settings->icon, 'fad fa' ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-icon i:hover,
.fl-node-<?php echo $id; ?> .fl-icon i:hover:before,
.fl-node-<?php echo $id; ?> .fl-icon a:hover i,
.fl-node-<?php echo $id; ?> .fl-icon a:hover i:before {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->hover_color ); ?>;
}
<?php endif; ?>
<?php if ( ! empty( $settings->bg_hover_color ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-icon i:hover,
.fl-node-<?php echo $id; ?> .fl-icon a:hover i {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_hover_color ); ?>;
	<?php if ( $settings->three_d ) : ?>
	background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $bg_hover_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_hover_color ); ?> 100%);
	border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( $border_hover_color ); ?>;
	<?php endif; ?>
}
<?php endif; ?>

<?php if ( ! empty( $settings->bg_color ) || ! empty( $settings->bg_hover_color ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-icon i {
	border-radius: 100%;
	-moz-border-radius: 100%;
	-webkit-border-radius: 100%;
	text-align: center;
}
<?php endif; ?>
