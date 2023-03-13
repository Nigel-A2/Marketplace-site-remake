<#

var defaults = {
	left: '<i class="dashicons dashicons-editor-alignleft"></i>',
	center: '<i class="dashicons dashicons-editor-aligncenter"></i>',
	right: '<i class="dashicons dashicons-editor-alignright"></i>',
};

var values = data.field.values;
var options = {};

if ( values ) {
	for ( var option in defaults ) {
		if ( values[ option ] ) {
			options[ values[ option ] ] = defaults[ option ];
		}
	}
} else {
	options = defaults;
}

var field = wp.template( 'fl-builder-field-button-group' )( {
	name: data.name,
	value: data.value,
	field: {
		options: options,
	},
} );

#>
{{{field}}}
