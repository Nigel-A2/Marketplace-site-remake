<#
var position = data.field.preview.position;
var defaults = {
	translateX: '0',
	translateY: '0',
	skew: '',
	rotate: '',
	scaleX: '1',
	scaleXSign: '',
	scaleYSign: ''
};

var value = '' === data.value ? defaults : data.value;

var dimensions = {
	skewX: {
		label: '<?php _e( 'Skew X', 'fl-builder' ); ?>',
		min: -60,
		max: 60,
	},
	skewY: {
		label: '<?php _e( 'Skew Y', 'fl-builder' ); ?>',
		min: -60,
		max: 60,
	},
	scaleX: {
		label: '<?php _e( 'Scale X', 'fl-builder' ); ?>',
		min: 1,
		max: 10,
		step: .1,
	},
	rotate: {
		label: '<?php _e( 'Rotate', 'fl-builder' ); ?>',
		min: 0,
		max: 360,
	},
};

var xOrientation = wp.template( 'fl-builder-field-button-group' )( {
	name: data.name + '[][scaleXSign]',
	value: value.scaleXSign,
	field: {
		label: '<?php _e( 'Horizontal Orientation', 'fl-builder' ); ?>',
		options: {
			'invert': '<i class="dashicons dashicons-image-flip-horizontal"></i>',
		},
	},
} );

var yOrientation = wp.template( 'fl-builder-field-button-group' )( {
	name: data.name + '[][scaleYSign]',
	value: value.scaleYSign,
	field: {
		label: '<?php _e( 'Vertical Orientation', 'fl-builder' ); ?>',
		options: {
			'invert': '<i class="dashicons dashicons-image-flip-vertical"></i>',
		},
	},
} );

#>
<div class="fl-shape-transform-field">
	<div class="fl-compound-field-section-visible">
		<div class="fl-compound-field-row">
			<span class="fl-compound-field-cell fl-shape-orientation-cell">
				<span class="fl-shape-orientation-controls">
					{{{xOrientation}}}
					{{{yOrientation}}}
				</span>
				<label><?php _e( 'Orientation', 'fl-builder' ); ?></label>
			</span>
			<span class="fl-compound-field-cell">
				<div class="fl-dimension-field-units">
					<# for ( var key in dimensions ) {
						var slider = JSON.stringify( {
							min: dimensions[ key ].min,
							max: dimensions[ key ].max,
							step: dimensions[ key ].step ? dimensions[ key ].step : 1,
						} );
					#>
					<div class="fl-dimension-field-unit fl-shape-transform-field-{{key}}">
						<input
							type="number"
							name="{{data.name}}[][{{key}}]"
							value="{{value[ key ]}}"
							autocomplete="off"
						/>
						<div
							class="fl-field-popup-slider"
							data-input="{{data.name}}[][{{key}}]"
							data-slider="{{slider}}"
						>
							<div class="fl-field-popup-slider-arrow"></div>
							<div class="fl-field-popup-slider-input"></div>
						</div>
						<label>{{dimensions[ key ].label}}</label>
					</div>
					<# } #>

					<input type="hidden" name="{{data.name}}[][scaleY]" value="1" />
				</div>
			</span>
		</div>
	</div>
</div>
