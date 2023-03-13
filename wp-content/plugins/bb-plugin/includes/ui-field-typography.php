<#

var defaults = {
	font_family: 'Default',
	font_weight: 'default',
	font_size: {
		length: '',
		unit: 'px',
	},
	line_height: {
		length: '',
		unit: '',
	},
	text_align: '',
	letter_spacing: {
		length: '',
		unit: 'px',
	},
	text_transform: '',
	text_decoration: '',
	font_style: '',
	font_variant: '',
	text_shadow: {
		color: '',
		horizontal: '',
		vertical: '',
		blur: '',
	},
};

disabled_defaults = {
	default: {},
	medium: {},
	responsive: {}
}

var value           = '' === data.value ? defaults : jQuery.extend( true, defaults, data.value );
var device          = data.device ? data.device : 'default';
var disabled_fields = {}
var disabled        = []



if (typeof data.field.disabled !== 'undefined') {
	disabled_fields = jQuery.extend( true, disabled_defaults, data.field.disabled )
} else {
	disabled_fields = disabled_defaults
}

jQuery.each(disabled_fields[device], function(i,v){
	disabled.push(v)
})

/**
 * Helper function to check if a field is enabled.
 */
var fl_typography_enabled = function( field ) {

	if( jQuery.inArray(field, disabled ) !== -1 ) {
		return false
	}
	return true;
}

var fontFamily = wp.template( 'fl-builder-field-font' )( {
	names: {
		family: data.name + '[][font_family]',
		weight: data.name + '[][font_weight]',
	},
	value: {
		family: value.font_family,
		weight: value.font_weight,
	},
	field: {
		show_labels: true,
	},
} );

var fontSize = wp.template( 'fl-builder-field-unit' )( {
	name: data.name + '[][font_size][length]',
	value: value.font_size.length,
	unit_name: data.name + '[][font_size][unit]',
	unit_value: value.font_size.unit,
	field: {
		units: [ 'px', 'em', 'rem', 'vw' ],
		slider: true,
	},
} );

var lineHeight = wp.template( 'fl-builder-field-unit' )( {
	name: data.name + '[][line_height][length]',
	value: value.line_height.length,
	unit_name: data.name + '[][line_height][unit]',
	unit_value: value.line_height.unit,
	field: {
		units: [ '', 'px', 'em' ],
		slider: true,
	},
} );

var textAlign = wp.template( 'fl-builder-field-align' )( {
	name: data.name + '[][text_align]',
	value: value.text_align,
	field: {},
} );

var letterSpacing = wp.template( 'fl-builder-field-unit' )( {
	name: data.name + '[][letter_spacing][length]',
	value: value.letter_spacing.length,
	unit_name: data.name + '[][letter_spacing][unit]',
	unit_value: value.letter_spacing.unit,
	field: {
		units: [ 'px' ],
		slider: {
			min: -10,
			max: 10,
			step: .1,
		},
	},
} );

var textTransform = wp.template( 'fl-builder-field-button-group' )( {
	name: data.name + '[][text_transform]',
	value: value.text_transform,
	field: {
		options: {
			none: 'Normal',
			capitalize: 'Tt',
			uppercase: 'TT',
			lowercase: 'tt',
		},
	},
} );

var textDecoration = wp.template( 'fl-builder-field-select' )( {
	name: data.name + '[][text_decoration]',
	value: value.text_decoration,
	field: {
		options: {
			'': '<?php esc_attr_e( 'Default', 'fl-builder' ); ?>',
			'none': '<?php esc_attr_e( 'None', 'fl-builder' ); ?>',
			'underline': '<?php esc_attr_e( 'Underline', 'fl-builder' ); ?>',
			'overline': '<?php esc_attr_e( 'Overline', 'fl-builder' ); ?>',
			'line-through': '<?php esc_attr_e( 'Line Through', 'fl-builder' ); ?>',
		},
	},
} );

var fontStyle = wp.template( 'fl-builder-field-select' )( {
	name: data.name + '[][font_style]',
	value: value.font_style,
	field: {
		options: {
			'': '<?php esc_attr_e( 'Default', 'fl-builder' ); ?>',
			'normal': '<?php esc_attr_e( 'Normal', 'fl-builder' ); ?>',
			'italic': '<?php esc_attr_e( 'Italic', 'fl-builder' ); ?>',
			'oblique': '<?php esc_attr_e( 'Oblique', 'fl-builder' ); ?>',
		},
	},
} );

var fontVariant = wp.template( 'fl-builder-field-select' )( {
	name: data.name + '[][font_variant]',
	value: value.font_variant,
	field: {
		options: {
			'': '<?php esc_attr_e( 'Default', 'fl-builder' ); ?>',
			'normal': '<?php esc_attr_e( 'Normal', 'fl-builder' ); ?>',
			'small-caps': '<?php esc_attr_e( 'Small Caps', 'fl-builder' ); ?>',
		},
	},
} );

var textShadow = wp.template( 'fl-builder-field-shadow' )( {
	name: data.name + '[][text_shadow]',
	value: value.text_shadow,
	field: {
		show_spread: false,
	},
} );

#>
<div class="fl-compound-field fl-typography-field">
	<div class="fl-compound-field-section fl-typography-field-section-general">
		<div class="fl-compound-field-section-toggle">
			<i class="dashicons dashicons-arrow-right-alt2"></i>
			<?php _e( 'Font', 'fl-builder' ); ?>
		</div>
		<# if ( 'default' === device ) { #>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-typography-field-family" data-property="font-family">
				<# if ( fl_typography_enabled( 'font_family' ) ) { #>
				{{{fontFamily}}}
				<# } #>
			</div>
		</div>
		<# } #>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-typography-field-size" data-property="font-size">
				<# if ( fl_typography_enabled( 'font_size' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Size', 'fl-builder' ); ?>
				</label>
				{{{fontSize}}}
				<# } #>
			</div>
			<div class="fl-compound-field-setting fl-typography-field-line-height" data-property="line-height">
			<# if ( fl_typography_enabled( 'line_height' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Line Height', 'fl-builder' ); ?>
				</label>
				{{{lineHeight}}}
				<# } #>
			</div>
			<div class="fl-compound-field-setting fl-typography-field-align" data-property="text-align">
			<# if ( fl_typography_enabled( 'text_align' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Align', 'fl-builder' ); ?>
				</label>
				{{{textAlign}}}
				<# } #>
			</div>
		</div>
	</div>
	<div class="fl-compound-field-section fl-compound-field-section-style">
		<div class="fl-compound-field-section-toggle">
			<i class="dashicons dashicons-arrow-right-alt2"></i>
			<?php _e( 'Style &amp Spacing', 'fl-builder' ); ?>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-typography-field-spacing" data-property="letter-spacing">
				<# if ( fl_typography_enabled( 'letter_spacing' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Spacing', 'fl-builder' ); ?>
				</label>
				{{{letterSpacing}}}
				<# } #>
			</div>
			<div class="fl-compound-field-setting fl-typography-field-transform" data-property="text-transform">
			<# if ( fl_typography_enabled( 'text_transform' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Transform', 'fl-builder' ); ?>
				</label>
				{{{textTransform}}}
				<# } #>
			</div>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-typography-field-decoration" data-property="text-decoration">
			<# if ( fl_typography_enabled( 'text_decoration' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Decoration', 'fl-builder' ); ?>
				</label>
				{{{textDecoration}}}
				<# } #>
			</div>
			<div class="fl-compound-field-setting fl-typography-field-style" data-property="font-style">
			<# if ( fl_typography_enabled( 'font_style' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Style', 'fl-builder' ); ?>
				</label>
				{{{fontStyle}}}
				<# } #>
			</div>
			<div class="fl-compound-field-setting fl-typography-field-variant" data-property="font-variant">
				<# if ( fl_typography_enabled( 'font_variant' ) ) { #>
				<label class="fl-compound-field-label">
					<?php _e( 'Variant', 'fl-builder' ); ?>
				</label>
				{{{fontVariant}}}
				<# } #>
			</div>
		</div>
	</div>
	<div class="fl-compound-field-section fl-compound-field-section-shadow">
		<# if ( fl_typography_enabled( 'text_shadow' ) ) { #>
		<div class="fl-compound-field-section-toggle">
			<i class="dashicons dashicons-arrow-right-alt2"></i>
			<?php _e( 'Text Shadow', 'fl-builder' ); ?>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-typography-field-shadow" data-property="text-shadow">
				{{{textShadow}}}
			</div>
		</div>
		<# } #>
	</div>
</div>
