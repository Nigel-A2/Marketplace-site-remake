<#

// Make sure we have an options array.
if ( '' === data.field.options ) {
	data.field.options = [];
}

// JSON parse if needed.
if ( '' !== data.value && 'string' === typeof data.value ) {
	data.value = JSON.parse( data.value );
}

// Set the default value if we do not have one.
if ( '' === data.value ) {
	data.value = Object.keys( data.field.options )[0];
}

// Make sure any new options are added to the value.
for ( var key in data.field.options ) {
	if ( jQuery.inArray( key, data.value ) === -1 ) {
		data.value.push( key );
	}
}

var encodedValue = JSON.stringify( data.value );

#>
<div class="fl-ordering-field-options<# if ( data.field.className ) { #> {{data.field.className}}<# } #>">
	<# for ( var i in data.value ) { #>
	<div class="fl-ordering-field-option" data-key="{{data.value[ i ]}}">{{data.field.options[ data.value[ i ] ]}}<i class="fas fa-arrows-alt"></i></div>
	<# } #>
</div>
<input type="hidden" name="{{data.name}}" value='{{{encodedValue}}}' />
