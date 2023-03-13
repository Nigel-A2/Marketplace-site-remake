<?php

	$counters = array(
		'days'    => array(
			'singular' => __( 'Day', 'fl-builder' ),
			'plural'   => __( 'Days', 'fl-builder' ),
		),
		'hours'   => array(
			'singular' => __( 'Hour', 'fl-builder' ),
			'plural'   => __( 'Hours', 'fl-builder' ),
		),
		'minutes' => array(
			'singular' => __( 'Minute', 'fl-builder' ),
			'plural'   => __( 'Minutes', 'fl-builder' ),
		),
		'seconds' => array(
			'singular' => __( 'Second', 'fl-builder' ),
			'plural'   => __( 'Seconds', 'fl-builder' ),
		),
	);

	?>

<div class="fl-countdown<?php echo ( 'default' == $settings->layout && 'yes' == $settings->show_separator && isset( $settings->separator_type ) ) ? ' fl-countdown-separator-' . $settings->separator_type : ''; ?>">

	<?php foreach ( $counters as $class => $label ) : ?>
		<div class="fl-countdown-number fl-countdown-<?php echo $class; ?>">
			<div class="fl-countdown-unit">
				<span class="fl-countdown-unit-number"></span>
				<div class="fl-countdown-unit-label" data-label='<?php echo json_encode( $label ); ?>'><?php echo $label['singular']; ?></div>
			</div>
			<?php if ( 'circle' == $settings->layout ) : ?>
				<div class="fl-countdown-circle-container">
					<?php $module->render_circle(); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
