<script type="text/html" id="tmpl-fl-builder-field">
	<# if ( ! data.field.label ) { #>
	<td class="fl-field-control" colspan="2">
	<# } else { #>
	<th class="fl-field-label">
		<label for="{{data.name}}">

			<# if ( 'button' === data.field.type ) { #>
			&nbsp;
			<# } else { #>
			{{{data.field.label}}}
				<# if ( undefined !== data.index ) { #>
				<# var index = data.index + 1; #>
				<span class="fl-builder-field-index">{{index}}</span>
				<# } #>
			<# } #>

			<# if ( data.responsive ) { #>
			<i class="fl-field-responsive-toggle dashicons dashicons-desktop" data-mode="default"></i>
			<# } #>

			<# if ( data.field.help ) { #>
			<span class="fl-help-tooltip">
				<i class="fl-help-tooltip-icon fas fa-question-circle"></i>
				<span class="fl-help-tooltip-text">{{{data.field.help}}}</span>
			</span>
			<# } #>

		</label>
	</th>
	<td class="fl-field-control">
	<# } #>
	<div class="fl-field-control-wrapper">
		<# if ( data.responsive ) { #>
		<i class="fl-field-responsive-toggle dashicons dashicons-desktop" data-mode="default"></i>
		<# } #>
		<# var devices = [ 'default', 'medium', 'responsive' ];

		for ( var i = 0; i < devices.length; i++ ) {

			data.device = devices[ i ];

			if ( 'default' !== devices[ i ] && ! data.responsive ) {
				continue;
			}

			if ( data.responsive ) {
				data.name  = 'default' === devices[ i ] ? data.rootName : data.rootName + '_' + devices[ i ];
				data.value = data.settings[ data.name ] ? data.settings[ data.name ] : '';

				if ( 'object' === typeof data.responsive ) {
					for ( var key in data.responsive ) {
						if ( 'object' === typeof data.responsive[ key ] && undefined !== data.responsive[ key ][ devices[ i ] ] ) {
							data.field[ key ] = data.responsive[ key ][ devices[ i ] ];
						}
					}
				}
			#>
			<div class="fl-field-responsive-setting fl-field-responsive-setting-{{devices[ i ]}}" data-device="{{devices[ i ]}}">
			<# } #>
			<# if ( data.template.length ) {
				var template = wp.template( 'fl-builder-field-' + data.field.type ),
					field    = template( data ),
					before   = data.field.html_before ? data.field.html_before : '',
					after    = data.field.html_after ? data.field.html_after : '';
			#>
			{{{before}}}{{{field}}}{{{after}}}
			<# } else {
				var name  = data.name.replace( '[]', '' );
			#>
			<div class="fl-legacy-field" data-field="{{name}}" />
			<# } #>
			<# if ( data.responsive ) { #>
			</div>
			<# } #>
		<# } #>
		<# if ( data.field.description ) { #>
		<span class="fl-field-description">{{{data.field.description}}}</span>
		<# } #>
	</div>
	</td>
</script>
