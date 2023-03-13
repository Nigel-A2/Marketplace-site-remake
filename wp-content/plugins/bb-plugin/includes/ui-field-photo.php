<#

var url = '';
var selectName = '';

if ( data.isMultiple ) {
	if ( data.settings[ data.rootName + '_src' ] ) {
		url = data.settings[ data.rootName + '_src' ][ data.index ];
	}
	selectName = data.rootName + '_src[]';
} else {
	url = data.settings[ data.name + '_src' ];
	selectName = data.name + '_src';
}

var photo = null;

if ( FLBuilderSettingsConfig.attachments[ data.value ] ) {
	photo = FLBuilderSettingsConfig.attachments[ data.value ];
	photo.isAttachment = true;
} else if ( typeof data.value !== 'undefined' && '' !== data.value && false !== data.value ) {
	if ( data.settings[ data.rootName + '_src' ] ) {
		photo = {
			id: data.value,
			url: url,
			filename: url.split( '/' ).pop(),
			isAttachment: false
		};
	} else {
		photo = {
			id: 0,
			url: data.value,
			filename: data.value.split( '/' ).pop(),
			isAttachment: false
		};
	}
}

var field = data.field;
var className = 'fl-photo-field fl-builder-custom-field';

if ( ! data.value || ! photo ) {
	className += ' fl-photo-empty';
} else if ( photo ) {
	className += photo.isAttachment ? ' fl-photo-has-attachment' : ' fl-photo-no-attachment';
}

if ( field.className ) {
	className += ' ' + field.className;
}

var show = '';

if ( field.show ) {
	show = "data-show='" + JSON.stringify( field.show ) + "'";
}

if ( photo && photo.url && photo.url.endsWith( '.svg' ) ) {
	photo.sizes = {
		full: {
			url: url,
			filename: url.split( '/' ).pop(),
			height: '',
			width: ''
		}
	}
}

#>
<div class="{{className}}">
	<a class="fl-photo-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Photo', 'fl-builder' ); ?></a>
	<div class="fl-photo-preview">
		<div class="fl-photo-preview-img">
			<img src="<# if ( photo ) { var src = FLBuilder._getPhotoSrc( photo ); #>{{{src}}}<# } #>" />
		</div>
		<div class="fl-photo-preview-controls">
			<select name="{{selectName}}" {{{show}}}>
				<# if ( photo && url ) {
					var sizes = FLBuilder._getPhotoSizeOptions( photo, url );
				#>
				{{{sizes}}}
				<# } #>
			</select>
			<div class="fl-photo-preview-filename">
				<# if ( photo ) { #>{{{photo.filename}}}<# } #>
			</div>
			<br />
			<a class="fl-photo-edit" href="javascript:void(0);" onclick="return false;"><?php _e( 'Edit', 'fl-builder' ); ?></a>
			<# if ( data.field.show_remove ) { #>
			<a class="fl-photo-remove" href="javascript:void(0);" onclick="return false;"><?php _e( 'Remove', 'fl-builder' ); ?></a>
			<# } else { #>
			<a class="fl-photo-replace" href="javascript:void(0);" onclick="return false;"><?php _e( 'Replace', 'fl-builder' ); ?></a>
			<# } #>
		</div>
	</div>
	<input name="{{data.name}}" type="hidden" value='{{data.value}}' />
</div>
