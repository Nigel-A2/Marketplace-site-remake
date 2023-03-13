<#
var atts = "",
	field = data.field;

// Toggle data
if ( field.toggle ) {
	atts += " data-toggle='" + JSON.stringify( field.toggle ) + "'";
}

// Hide data
if ( field.hide ) {
	atts += " data-hide='" + JSON.stringify( field.hide ) + "'";
}
#>
<div class="fl-button-group-field">
	<div class="fl-button-group-field-options">
		<# for ( var option in data.field.options ) {
			var selected = option === data.value ? 1 : 0;
		#>
		<button
			class="fl-button-group-field-option"
			data-value="{{option}}"
			data-selected="{{selected}}"
		>
			{{{data.field.options[ option ]}}}
		</button>
		<# } #>
	</div>
	<input type="hidden" name="{{data.name}}" value="{{data.value}}" {{{atts}}} />
	<div class="fl-clear"></div>
</div>
