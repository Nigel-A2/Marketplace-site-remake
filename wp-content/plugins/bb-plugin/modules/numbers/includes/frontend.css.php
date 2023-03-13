<?php

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'text_typography',
	'selector'     => ".fl-node-$id .fl-number .fl-number-text .fl-number-before-text, .fl-node-$id .fl-number .fl-number-text .fl-number-after-text",
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'number_typography',
	'selector'     => ".fl-node-$id .fl-number .fl-number-text .fl-number-string, .fl-node-$id .fl-number .fl-number-text .fl-number-string span",
) );

?>

<?php if ( ! empty( $settings->number_color ) ) : ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-module-content .fl-number-int,
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-module-content .fl-number-string {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->number_color ); ?>;
	}
<?php endif; ?>

<?php if ( ! empty( $settings->text_color ) ) : ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-module-content .fl-number-before-text,
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-module-content .fl-number-after-text {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
	}
<?php endif; ?>


<?php if ( isset( $settings->layout ) && 'circle' == $settings->layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-number .fl-number-text{
		position: absolute;
		top: 50%;
		left: 50%;
		-webkit-transform: translate(-50%,-50%);
			-moz-transform: translate(-50%,-50%);
			-ms-transform: translate(-50%,-50%);
				transform: translate(-50%,-50%);
	}
	.fl-node-<?php echo $id; ?> .fl-number-circle-container{
		<?php
		if ( ! empty( $settings->circle_width ) ) {
			echo 'max-width: ' . $settings->circle_width . 'px;';
			echo 'max-height: ' . $settings->circle_width . 'px;';
		} else {
			echo 'max-width: 100px;';
			echo 'max-height: 100px;';
		}
		?>
	}

	.fl-node-<?php echo $id; ?> .svg circle{
	<?php
	if ( ! empty( $settings->circle_dash_width ) ) {
		echo 'stroke-width: ' . $settings->circle_dash_width . 'px;';
	}
	?>
	}

	.fl-node-<?php echo $id; ?> .svg .fl-bar-bg{
	<?php
	if ( ! empty( $settings->circle_bg_color ) ) {
		echo 'stroke: ' . FLBuilderColor::hex_or_rgb( $settings->circle_bg_color ) . ';';
	} else {
		echo 'stroke: transparent;';
	}
	?>
	}

	.fl-node-<?php echo $id; ?> .svg .fl-bar{
	<?php
	if ( ! empty( $settings->circle_color ) ) {
		echo 'stroke: ' . FLBuilderColor::hex_or_rgb( $settings->circle_color ) . ';';
	} else {
		echo 'stroke: transparent;';
	}
	?>
	}
<?php endif; ?>

<?php

if ( isset( $settings->layout ) && 'bars' == $settings->layout ) {
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-number-bars-container",
		'enabled'  => ! empty( $settings->bar_bg_color ),
		'props'    => array(
			'background-color' => $settings->bar_bg_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-number-bar",
		'enabled'  => ! empty( $settings->bar_color ),
		'props'    => array(
			'background-color' => $settings->bar_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-number-bar",
		'enabled'  => empty( $settings->number ),
		'props'    => array(
			'padding-left'  => 0,
			'padding-right' => 0,
		),
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'bar_height',
		'selector'     => ".fl-node-$id .fl-number-bars-container, .fl-node-$id .fl-number-bar",
		'prop'         => 'height',
		'unit'         => 'px',
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-number-bar .fl-number-string",
		'enabled'  => ! empty( $settings->number_position ) && 'hidden' === $settings->number_position,
		'props'    => array(
			'display' => 'none',
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-number-bar",
		'enabled'  => ! empty( $settings->number_position ) && 'hidden' === $settings->number_position,
		'props'    => array(
			'padding' => '0px',
		),
	) );
}
