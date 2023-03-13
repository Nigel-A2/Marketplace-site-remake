<select name="{{data.name}}">
	<?php
	foreach ( FLBuilderPhoto::sizes() as $size => $atts ) :
			$label = ucwords( str_replace( array( '_', '-' ), ' ', $size ) ) . ' (' . implode( 'x', $atts ) . ')';
		?>
	<option value="<?php echo $size; ?>"<# if ( data.value === '<?php echo $size; ?>' ) { #> selected="selected"<# } #>><?php echo $label; ?></option>
	<?php endforeach; ?>
	<option value="full"<# if ( data.value === 'full' ) { #> selected="selected"<# } #>><?php _e( 'Full Size', 'fl-builder' ); ?></option>
</select>
