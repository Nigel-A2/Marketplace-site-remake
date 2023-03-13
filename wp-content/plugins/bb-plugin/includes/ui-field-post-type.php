<#
var atts  = '',
	field = data.field,
	name  = data.name,
	value = data.value;

// Multiselect
if ( field['multi-select'] ) {
	atts += ' multiple';
	name += '[]';
}
#>
<select name="{{{name}}}"<# if ( field.className ) { #> {{field.className}}<# } #>{{{atts}}}>
	<?php foreach ( FLBuilderLoop::post_types() as $slug => $type ) : ?>
		<#
		// Is selected?
		var selected = '';
		if ( 'object' === typeof value && jQuery.inArray( '<?php echo $slug; ?>', value ) != -1 ) {
			// Multi select
			selected = ' selected="selected"';
		} else if ( '<?php echo $slug; ?>' == value ) {
			// Single select
			selected = ' selected="selected"';
		}
		#>
		<option value="<?php echo $slug; ?>"{{{selected}}}><?php echo $type->labels->name; ?></option>
	<?php endforeach; ?>
</select>
