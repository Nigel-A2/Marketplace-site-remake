<input
type="text"
name="{{data.name}}"
value="{{data.value}}"
class="text <# if ( data.field.className ) { #> {{data.field.className}}<# } #><# if ( ! data.field.size ) { #> text-full<# } #>"
<# if ( data.field.placeholder ) { #>placeholder="{{data.field.placeholder}}" <# } #>
<# if ( data.field.maxlength ) { #>maxlength="{{data.field.maxlength}}" <# } #>
<# if ( data.field.size ) { #>size="{{data.field.size}}" <# } #>
/>

<# var textOptions = data.field.options; #>
<# if ( 'object' == typeof textOptions ) { #>
<br>
<select class="fl-text-field-add-value" data-target="{{data.name}}">
	<#

	for ( var option in textOptions ) {
		if (
			'object' == typeof textOptions[option]
			&& 'object' == typeof textOptions[option].options
			&& textOptions[option].label
		) {
			#>
			<optgroup label="{{textOptions[option].label}}">
				<# for ( var groupOption in textOptions[option].options ) { #>
				<option value="{{groupOption}}">{{textOptions[option].options[groupOption]}}</option>
				<# } #>
			</optgroup>
			<#
		} else {
			#>
			<option value="{{option}}">{{textOptions[option]}}</option>
			<#
		}
	}

	#>
</select>
<# } #>
