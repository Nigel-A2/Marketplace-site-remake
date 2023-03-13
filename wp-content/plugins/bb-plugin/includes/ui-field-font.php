<#

var names = data.names;

if ( ! names ) {
	names = {
		family: data.name + '[][family]',
		weight: data.name + '[][weight]',
	};
}

data.value = JSON.stringify( data.value );

#>
<div class="fl-font-field" data-value='{{{data.value}}}'>
	<div class="fl-font-field-font-wrapper">
		<# if ( data.field.show_labels ) { #>
		<label for="{{names.family}}"><?php _e( 'Family', 'fl-builder' ); ?></label>
		<# } #>
		<select name="{{names.family}}" class="fl-font-field-font">
			<?php FLBuilderFonts::display_select_font( 'Default' ); ?>
		</select>
	</div>
	<div class="fl-font-field-weight-wrapper">
		<# if ( data.field.show_labels ) { #>
		<label for="{{names.weight}}"><?php _e( 'Weight', 'fl-builder' ); ?></label>
		<# } #>
		<select name="{{names.weight}}" class="fl-font-field-weight">
			<?php FLBuilderFonts::display_select_weight( 'Default', '' ); ?>
		</select>
	</div>
</div>
