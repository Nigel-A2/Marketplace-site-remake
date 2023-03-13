<select
name="{{data.name}}"
data-value="{{{data.value}}}"
<# if ( data.field.className ) { #>class="{{data.field.className}}" <# } #>
<# if ( data.field.toggle ) { data.field.toggle = JSON.stringify( data.field.toggle ); #>data-toggle="{{{data.field.toggle}}}" <# } #>
<# if ( data.field.hide ) { data.field.hide = JSON.stringify( data.field.hide ); #>data-hide="{{{data.field.hide}}}" <# } #>
<# if ( data.field.trigger ) { data.field.trigger = JSON.stringify( data.field.trigger ); #>data-trigger="{{{data.field.trigger}}}" <# } #>>
<?php echo FLBuilderTimezones::build_timezones( '' ); ?>
</select>
