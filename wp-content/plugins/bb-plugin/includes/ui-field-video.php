<#

var video = null;

if ( FLBuilderSettingsConfig.attachments[ data.value ] ) {
	video = FLBuilderSettingsConfig.attachments[ data.value ];
} else if ( ! _.isEmpty( data.value ) ) {
	video = {
		id: data.value,
		url: data.value,
		filename: data.value
	};
}

var className = data.field.className ? ' ' + data.field.className : '';

if ( ! data.value || ! video ) {
	className += ' fl-video-empty';
}

#>
<div class="fl-video-field fl-builder-custom-field{{className}}">
	<a class="fl-video-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Video', 'fl-builder' ); ?></a>
	<div class="fl-video-preview">
		<# if ( data.value && video ) { #>
		<div class="fl-video-preview-img">
			<span class="dashicons dashicons-media-video"></span>
		</div>
		<span class="fl-video-preview-filename">{{{video.filename}}}</span>
		<# } else { #>
		<div class="fl-video-preview-img">
			<img src="<?php echo FL_BUILDER_URL; ?>img/spacer.png" />
		</div>
		<span class="fl-video-preview-filename"></span>
		<# } #>
		<br />
		<a class="fl-video-replace" href="javascript:void(0);" onclick="return false;"><?php _e( 'Replace Video', 'fl-builder' ); ?></a>
		<# if ( data.field.show_remove ) { #>
		<a class="fl-video-remove" href="javascript:void(0);" onclick="return false;"><?php _e( 'Remove Video', 'fl-builder' ); ?></a>
		<# } #>
		<div class="fl-clear"></div>
	</div>
	<input name="{{data.name}}" type="hidden" value='{{{data.value}}}' />
</div>
