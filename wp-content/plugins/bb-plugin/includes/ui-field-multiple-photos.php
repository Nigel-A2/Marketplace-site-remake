<#

// Normalize the value so we have an array.
if ( '' !== data.value && 'string' === typeof data.value ) {

	data.value = JSON.parse( data.value );

	// Older versions might be double encoded.
	if ( 'string' === typeof data.value ) {
		data.value = JSON.parse( data.value );
	}

} else if ( '' === data.value ) {
	data.value = [];
}

if ( 1 === data.value.length ) {
	var selectedText = FLBuilderStrings.photoSelectedNum.replace( '%d', 1 );
} else {
	var selectedText = FLBuilderStrings.photoSelectedNum.replace( '%d', data.value.length );
}

var encodedValue = '' !== data.value && data.value.length ? JSON.stringify( data.value ) : '';

#>
<div class="fl-multiple-photos-field fl-builder-custom-field<# if ( '' === data.value ) { #> fl-multiple-photos-empty<# } #><# if ( data.field.className ) { #> {{data.field.className}}<# } #>">
	<div class="fl-multiple-photos-count">{{selectedText}}</div>
	<a class="fl-multiple-photos-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Create Gallery', 'fl-builder' ); ?></a>
	<a class="fl-multiple-photos-edit" href="javascript:void(0);" onclick="return false;"><?php _e( 'Edit Gallery', 'fl-builder' ); ?></a>
	<a class="fl-multiple-photos-add" href="javascript:void(0);" onclick="return false;"><?php _e( 'Add Photos', 'fl-builder' ); ?></a>
	<input name="{{data.name}}" type="hidden" value='{{encodedValue}}' />
</div>
