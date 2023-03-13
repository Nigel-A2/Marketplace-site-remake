<#

var field = data.field;
var className = 'fl-icon-field fl-builder-custom-field';

if ( '' === data.value ) {
	className += ' fl-icon-empty';
}
if ( field.className ) {
	className += ' ' + field.className;
}

var show = '';

if ( field.show ) {
	show = "data-show='" + JSON.stringify( field.show ) + "'";
}

#>
<div class="{{className}}">
	<a class="fl-icon-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Icon', 'fl-builder' ); ?></a>
	<div class="fl-icon-preview">
		<i class="{{{data.value}}}" data-icon="{{{data.value}}}"></i>
		<a class="fl-icon-replace" href="javascript:void(0);" onclick="return false;"><?php _e( 'Replace', 'fl-builder' ); ?></a>
		<# if ( data.field.show_remove ) { #>
		<a class="fl-icon-remove" href="javascript:void(0);" onclick="return false;"><?php _e( 'Remove', 'fl-builder' ); ?></a>
		<# } #>
	</div>
	<input name="{{data.name}}" type="hidden" value="{{{data.value}}}" {{{show}}} />
</div>
