<#

var defaults = {
	color: '',
	horizontal: '',
	vertical: '',
	blur: '',
	spread: '',
};

var value = '' === data.value ? defaults : data.value;

var picker = wp.template( 'fl-builder-field-color' )( {
	name: data.name + '[][color]',
	value: value.color,
	field: {
		className: 'fl-shadow-field-color',
		show_reset: true,
		show_alpha: true,
	},
} );

var dimensions = {
	horizontal: {
		label: 'X',
		min: -100,
		max: 100,
	},
	vertical: {
		label: 'Y',
		min: -100,
		max: 100,
	},
	blur: {
		label: '<?php _e( 'Blur', 'fl-builder' ); ?>',
		min: 0,
		max: 100,
	},
	spread: {
		label: '<?php _e( 'Spread', 'fl-builder' ); ?>',
		min: -100,
		max: 100,
	},
};

if ( false === data.field.show_spread ) {
	delete dimensions.spread;
}

#>
<div class="fl-shadow-field">
	{{{picker}}}
	<div class="fl-dimension-field-units">
		<# for ( var key in dimensions ) {
			var slider = JSON.stringify( {
				min: dimensions[ key ].min,
				max: dimensions[ key ].max,
			} );
		#>
		<div class="fl-dimension-field-unit fl-shadow-field-{{key}}">
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
		<div class="fl-dimension-field-unit-select">
			<div class="fl-field-unit-select">px</div>
		</div>
	</div>
</div>
