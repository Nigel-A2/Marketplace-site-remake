<#

// Normalize the value so we have an array.
if ( '' !== data.value && 'string' === typeof data.value ) {

	data.value = JSON.parse( data.value );

	// Older versions might be double encoded.
	if ( 'string' === typeof data.value ) {
		data.value = JSON.parse( data.value );
	}
	if ( 'number' === typeof data.value ) {
		data.value = [ data.value ];
	}

} else if ( '' === data.value ) {
	data.value = [];
}

if ( 1 === data.value.length ) {
	var selectedText = FLBuilderStrings.audioSelectedNum.replace( '%d', 1 );
} else {
	var selectedText = FLBuilderStrings.audiosSelectedNum.replace( '%d', data.value.length );
}

var encodedValue = '' !== data.value && data.value.length ? JSON.stringify( data.value ) : '';

#>
<div class="fl-multiple-audios-field fl-builder-custom-field
	<# if ( '' === data.value ) { #> fl-multiple-audios-empty<# } #>
	<# if ( data.field.className ) { #> {{data.field.className}}<# } #>"
	<# if ( data.field.toggle ) { data.field.toggle = JSON.stringify( data.field.toggle ); #>data-toggle='{{{data.field.toggle}}}'<# } #>>
	<div class="fl-multiple-audios-count">{{selectedText}}</div>
	<a class="fl-multiple-audios-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Audio', 'fl-builder' ); ?></a>
	<a class="fl-multiple-audios-edit" href="javascript:void(0);" onclick="return false;"><?php _e( 'Edit Playlist', 'fl-builder' ); ?></a>
	<a class="fl-multiple-audios-add" href="javascript:void(0);" onclick="return false;"><?php _e( 'Add Audio Files', 'fl-builder' ); ?></a>
	<input name="{{data.name}}" type="hidden" value='{{{encodedValue}}}' />
</div>
