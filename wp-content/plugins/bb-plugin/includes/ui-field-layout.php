<div class="fl-layout-field">
	<# for ( var key in data.field.options ) { #>
	<div class="fl-layout-field-option<# if ( key == data.value ) { #> fl-layout-field-option-selected<# } #>" data-value="{{key}}">
		<img src="{{{data.field.options[ key ]}}}" />
	</div>
	<# } #>
	<div class="fl-clear"></div>
	<input name="{{data.name}}" type="hidden" value='{{{data.value}}}' />
</div>
