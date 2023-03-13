<div class="fl-builder-service-settings">
	<table class="fl-form-table">
		<#

		var service_type = null,
			services     = {},
			options 	 = { '' : '<?php esc_html_e( 'Choose...', 'fl-builder' ); ?>' },
			key			 = '',
			fields		 = {},
			html		 = '';

		if ( data.section.services && 'all' !== data.section.services ) {
			service_type = data.section.services;
		}

		if ( ! service_type ) {
			services = FLBuilderConfig.services;
		}
		else {
			for ( key in FLBuilderConfig.services ) {
				if ( FLBuilderConfig.services[ key ].type == service_type ) {
					services[ key ] = FLBuilderConfig.services[ key ];
				}
			}
		}

		for ( key in services ) {
			options[ key ] = services[ key ].name;
		}

		var fields = {
			service: {
				row_class : 'fl-builder-service-select-row',
				className : 'fl-builder-service-select',
				type      : 'select',
				label     : '<?php esc_html_e( 'Service', 'fl-builder' ); ?>',
				options   : options,
				preview   : {
					type 	: 'none'
				}
			}
		};

		html = FLBuilderSettingsForms.renderFields( fields, data.settings );

		#>
		{{{html}}}
	</table>
</div>
