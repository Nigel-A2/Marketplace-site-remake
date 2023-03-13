<div class="fl-code-field">
	<#

	var editorId = 'flcode' + new Date().getTime() + '_' + data.name,
		value    = 'object' === typeof data.value ? JSON.stringify( data.value ) : data.value;

	value.replace( '&', '&amp;' )
		 .replace( '"', '&quot;' )
		 .replace( "'", '&#039;' )
		 .replace( '<', '&lt;' )
		 .replace( '>', '&gt;' );

	#>
	<textarea
		id="{{editorId}}"
		name="{{data.name}}"
		data-editor="{{data.field.editor}}"
		data-wrap="<# if ( data.field.wrap ) { #>1<# } else { #>0<# } #>"
		<# if ( data.field.className ) { #>class="{{data.field.className}}" <# } #>
		<# if ( data.field.rows ) { #>rows="{{data.field.rows}}" <# } #>
	>{{data.value}}</textarea>
</div>
