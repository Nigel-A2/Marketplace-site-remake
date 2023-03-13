<input
	type="date"
	name="{{data.name}}"
	value="{{data.value}}"
	<# if ( data.field.min ) { #>min="{{data.field.min}}" <# } #>
	<# if ( data.field.max ) { #>max="{{data.field.max}}" <# } #>
/>
