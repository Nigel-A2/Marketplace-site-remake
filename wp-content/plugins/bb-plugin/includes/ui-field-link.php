<div class="fl-link-field">
	<div class="fl-link-field-input-wrap">
		<input type="text" name="{{data.name}}" value="{{{data.value}}}" class="text fl-link-field-input" placeholder="<# if ( data.field.placeholder ) { #>{{data.field.placeholder}}<# } else { #><?php _ex( 'http://www.example.com', 'Link placeholder', 'fl-builder' ); ?><# } #>" />
		<button class="fl-link-field-select fl-builder-button fl-builder-button-small" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select', 'fl-builder' ); ?></button>
	</div>

	<div class="fl-link-field-options-wrap">
		<# if ( data.field.show_target ) {
			var value = data.settings[ data.name + '_target' ];
			var checked = '_blank' === value ? 'checked' : '';
		#>
		<label>
			<input type="checkbox" class="fl-link-field-target-cb" {{checked}} />
			<input type="hidden" name="{{data.name}}_target" value="{{value}}" />
			<span><?php _e( 'New Window', 'fl-builder' ); ?></span>
		</label>
		<# } #>
		<# if ( data.field.show_nofollow ) {
			var value = data.settings[ data.name + '_nofollow' ];
			var checked = 'yes' === value ? 'checked' : '';
		#>
		<label>
			<input type="checkbox" class="fl-link-field-nofollow-cb" {{checked}} />
			<input type="hidden" name="{{data.name}}_nofollow" value="{{value}}" />
			<span><?php _e( 'No Follow', 'fl-builder' ); ?></span>
		</label>
		<# } #>

		<# if ( data.field.show_download ) {
			var value = data.settings[ data.name + '_download' ];
			var checked = 'yes' === value ? 'checked' : '';
		#>
		<label>
			<input type="checkbox" class="fl-link-field-download-cb" {{checked}} />
			<input type="hidden" name="{{data.name}}_download" value="{{value}}" />
			<span><?php _e( 'Force Download', 'fl-builder' ); ?></span>
		</label>
		<# } #>

		<# if ( ! ( data.field.show_target && data.field.show_nofollow ) ) { #>
				<label></label>
		<# } #>
	</div>

	<div class="fl-link-field-search">
		<span class="fl-link-field-search-title"><?php _e( 'Enter a post title to search.', 'fl-builder' ); ?></span>
		<input type="text" name="{{data.name}}-search" class="text text-full fl-link-field-search-input" placeholder="<?php esc_attr_e( 'Start typing...', 'fl-builder' ); ?>" />
		<button class="fl-link-field-search-cancel fl-builder-button fl-builder-button-small" href="javascript:void(0);" onclick="return false;"><?php _e( 'Cancel', 'fl-builder' ); ?></button>
	</div>
</div>
