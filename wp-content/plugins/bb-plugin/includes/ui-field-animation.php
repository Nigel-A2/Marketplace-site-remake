<#

var defaults = {
	style: '',
	delay: 0.0,
	duration: 1.0,
};

var value = '' === data.value ? defaults : jQuery.extend( true, defaults, data.value );

#>
<?php

$styles = array(
	''       => _x( 'None', 'Animation style.', 'fl-builder' ),
	'fade'   => array(
		'label'   => _x( 'Fade', 'Animation style.', 'fl-builder' ),
		'options' => array(
			'fade-in'    => _x( 'Fade In', 'Animation style.', 'fl-builder' ),
			'fade-left'  => _x( 'Fade Left', 'Animation style.', 'fl-builder' ),
			'fade-right' => _x( 'Fade Right', 'Animation style.', 'fl-builder' ),
			'fade-up'    => _x( 'Fade Up', 'Animation style.', 'fl-builder' ),
			'fade-down'  => _x( 'Fade Down', 'Animation style.', 'fl-builder' ),
		),
	),
	'slide'  => array(
		'label'   => _x( 'Slide', 'Animation style.', 'fl-builder' ),
		'options' => array(
			'slide-in-left'  => _x( 'Slide Left', 'Animation style.', 'fl-builder' ),
			'slide-in-right' => _x( 'Slide Right', 'Animation style.', 'fl-builder' ),
			'slide-in-up'    => _x( 'Slide Up', 'Animation style.', 'fl-builder' ),
			'slide-in-down'  => _x( 'Slide Down', 'Animation style.', 'fl-builder' ),
		),
	),
	'zoom'   => array(
		'label'   => _x( 'Zoom', 'Animation style.', 'fl-builder' ),
		'options' => array(
			'zoom-in'    => _x( 'Zoom In', 'Animation style.', 'fl-builder' ),
			'zoom-left'  => _x( 'Zoom Left', 'Animation style.', 'fl-builder' ),
			'zoom-right' => _x( 'Zoom Right', 'Animation style.', 'fl-builder' ),
			'zoom-up'    => _x( 'Zoom Up', 'Animation style.', 'fl-builder' ),
			'zoom-down'  => _x( 'Zoom Down', 'Animation style.', 'fl-builder' ),
		),
	),
	'bounce' => array(
		'label'   => _x( 'Bounce', 'Animation style.', 'fl-builder' ),
		'options' => array(
			'bounce'       => _x( 'Bounce', 'Animation style.', 'fl-builder' ),
			'bounce-in'    => _x( 'Bounce In', 'Animation style.', 'fl-builder' ),
			'bounce-left'  => _x( 'Bounce Left', 'Animation style.', 'fl-builder' ),
			'bounce-right' => _x( 'Bounce Right', 'Animation style.', 'fl-builder' ),
			'bounce-up'    => _x( 'Bounce Up', 'Animation style.', 'fl-builder' ),
			'bounce-down'  => _x( 'Bounce Down', 'Animation style.', 'fl-builder' ),
		),
	),
	'rotate' => array(
		'label'   => _x( 'Rotate', 'Animation style.', 'fl-builder' ),
		'options' => array(
			'rotate-in'         => _x( 'Rotate In', 'Animation style.', 'fl-builder' ),
			'rotate-down-left'  => _x( 'Rotate Down Left', 'Animation style.', 'fl-builder' ),
			'rotate-down-right' => _x( 'Rotate Down Right', 'Animation style.', 'fl-builder' ),
			'rotate-up-left'    => _x( 'Rotate Up Left', 'Animation style.', 'fl-builder' ),
			'rotate-up-right'   => _x( 'Rotate Up Right', 'Animation style.', 'fl-builder' ),
		),
	),
	'flip'   => array(
		'label'   => _x( 'Flip', 'Animation style.', 'fl-builder' ),
		'options' => array(
			'flip-vertical'   => _x( 'Flip Vertical', 'Animation style.', 'fl-builder' ),
			'flip-horizontal' => _x( 'Flip Horizontal', 'Animation style.', 'fl-builder' ),
		),
	),
	'fancy'  => array(
		'label'   => _x( 'Fancy', 'Animation style.', 'fl-builder' ),
		'options' => array(
			'fancy-flash'       => _x( 'Flash', 'Animation style.', 'fl-builder' ),
			'fancy-pulse'       => _x( 'Pulse', 'Animation style.', 'fl-builder' ),
			'fancy-rubber-band' => _x( 'Rubber Band', 'Animation style.', 'fl-builder' ),
			'fancy-shake'       => _x( 'Shake', 'Animation style.', 'fl-builder' ),
			'fancy-swing'       => _x( 'Swing', 'Animation style.', 'fl-builder' ),
			'fancy-tada'        => _x( 'Tada', 'Animation style.', 'fl-builder' ),
			'fancy-wobble'      => _x( 'Wobble', 'Animation style.', 'fl-builder' ),
			'fancy-jello'       => _x( 'Jello', 'Animation style.', 'fl-builder' ),
			'fancy-light-speed' => _x( 'Light Speed', 'Animation style.', 'fl-builder' ),
			'fancy-jack-box'    => _x( 'Jack in the Box', 'Animation style.', 'fl-builder' ),
			'fancy-roll-in'     => _x( 'Roll In', 'Animation style.', 'fl-builder' ),
		),
	),
);

?>
<#

var style = wp.template( 'fl-builder-field-select' )( {
	name: data.name + '[][style]',
	value: value.style,
	field: {
		options: <?php echo json_encode( $styles ); ?>,
	},
} );

var delay = wp.template( 'fl-builder-field-unit' )( {
	name: data.name + '[][delay]',
	value: value.delay,
	field: {
		units: [ 'seconds' ],
		slider: true,
	},
} );

var duration = wp.template( 'fl-builder-field-unit' )( {
	name: data.name + '[][duration]',
	value: value.duration,
	field: {
		units: [ 'seconds' ],
		slider: true,
	},
} );

#>
<div class="fl-compound-field fl-animation-field">
	<div class="fl-compound-field-section">
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-animation-field-style">
				{{{style}}}
			</div>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-animation-field-delay">
				{{{delay}}}
				<label class="fl-compound-field-label fl-compound-field-label-bottom">
					<?php _e( 'Delay', 'fl-builder' ); ?>
				</label>
			</div>
			<div class="fl-compound-field-setting fl-animation-field-duration">
				{{{duration}}}
				<label class="fl-compound-field-label fl-compound-field-label-bottom">
					<?php _e( 'Duration', 'fl-builder' ); ?>
				</label>
			</div>
		</div>
	</div>
</div>
