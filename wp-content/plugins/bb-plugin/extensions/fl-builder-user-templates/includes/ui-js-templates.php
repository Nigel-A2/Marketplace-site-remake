<script type="text/html" id="tmpl-fl-node-template-block">
	<span class="fl-builder-block fl-builder-block-saved-{{data.type}}<# if ( data.global ) { #> fl-builder-block-global<# } #>" data-id="{{data.id}}">
		<span class="fl-builder-block-content">
			<div class="fl-builder-block-title">{{data.name}}</div>
			<# if ( data.global ) { #>
			<div class="fl-builder-badge fl-builder-badge-global">
				<?php _ex( 'Global', 'Indicator for global node templates.', 'fl-builder' ); ?>
			</div>
			<# } #>
			<# if ( data.global && FLBuilderConfig.userCanEditGlobalTemplates ) { #>
			<span class="fl-builder-node-template-actions">
				<a class="fl-builder-node-template-edit" href="{{data.link}}" target="_blank">
					<i class="fas fa-wrench"></i>
				</a>
				<a class="fl-builder-node-template-delete" href="javascript:void(0);">
					<i class="fas fa-times"></i>
				</a>
			</span>
			<# } #>
		</span>
	</span>
</script>
<!-- #tmpl-fl-node-template-block -->

<script type="text/html" id="tmpl-fl-content-panel-saved-view">
	<div>
		<#
		var templates = data.queryResults.library.template.items;
		var rows = _.filter(templates, function(item) {
			return item.content === 'row';
		});
		var columns = _.filter(templates, function(item) {
			return item.content === 'column';
		});
		var modules = _.filter(templates, function(item) {
			return item.content === 'module';
		});
		#>
		<?php if ( ! FLBuilderModel::is_post_user_template( 'row' ) && ! FLBuilderModel::is_post_user_template( 'column' ) ) : ?>
		<div id="fl-builder-blocks-saved-rows" class="fl-builder-blocks-section fl-builder-blocks-node-template">

			<div class="fl-builder-blocks-section-header">
				<span class="fl-builder-blocks-section-title"><?php _e( 'Saved Rows', 'fl-builder' ); ?></span>
			</div>
			<div class="fl-builder-blocks-section-content fl-builder-saved-rows">
			<# if (rows.length === 0) { #>
				<span class="fl-builder-block-no-node-templates"><?php _e( 'No saved rows found.', 'fl-builder' ); ?></span>
			<# } else { #>
				<# for( var i in rows) {
					var row = rows[i];
					image = row.image,
					hasImage = image && !image.endsWith('blank.jpg'),
					hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '' ;
					var globalClass = row.isGlobal ? ' fl-builder-block-global' : '';
				#>
				<span class="fl-builder-block fl-builder-block-saved-row{{globalClass}} {{hasImageClass}}" data-id="{{row.id}}">
					<span class="fl-builder-block-content">
						<# if ( hasImage ) { #>
						<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
						<# } #>
						<div class="fl-builder-block-details">
							<div class="fl-builder-block-title" title="{{row.name}}">{{row.name}}</div>
							<# if (row.isGlobal) { #>
							<div class="fl-builder-badge fl-builder-badge-global">
								<?php _ex( 'Global', 'Indicator for global node templates.', 'fl-builder' ); ?>
							</div>
							<# } #>
							<span class="fl-builder-node-template-actions">
								<a class="fl-builder-node-template-edit" href="{{row.link}}" target="_blank">
									<i class="fas fa-wrench"></i>
								</a>
								<a class="fl-builder-node-template-delete" href="javascript:void(0);">
									<i class="fas fa-times"></i>
								</a>
							</span>
						</div>
					</span>
				</span>
				<# } #>
			<# } #>
			</div>
		</div>
		<?php endif; ?>
		<?php if ( ! FLBuilderModel::is_post_user_template( 'column' ) ) : ?>
		<div id="fl-builder-blocks-saved-columns" class="fl-builder-blocks-section fl-builder-blocks-node-template">

			<div class="fl-builder-blocks-section-header">
				<span class="fl-builder-blocks-section-title"><?php _e( 'Saved Columns', 'fl-builder' ); ?></span>
			</div>
			<div class="fl-builder-blocks-section-content fl-builder-saved-columns">
				<# if (columns.length === 0) { #>
					<span class="fl-builder-block-no-node-templates"><?php _e( 'No saved columns found.', 'fl-builder' ); ?></span>
				<# } else { #>
					<# for( var i in columns) {
						var column = columns[i];
						image = column.image,
						hasImage = image && !image.endsWith('blank.jpg'),
						hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '' ;
						var globalClass = column.isGlobal ? ' fl-builder-block-global' : '';
					#>
					<span class="fl-builder-block fl-builder-block-saved-column{{globalClass}} {{hasImageClass}}" data-id="{{column.id}}">
						<span class="fl-builder-block-content">
							<# if ( hasImage ) { #>
							<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
							<# } #>
							<div class="fl-builder-block-details">
								<div class="fl-builder-block-title" title="{{column.name}}">{{column.name}}</div>
								<# if (column.isGlobal) { #>
								<div class="fl-builder-badge fl-builder-badge-global">
									<?php _ex( 'Global', 'Indicator for global node templates.', 'fl-builder' ); ?>
								</div>
								<# } #>
								<span class="fl-builder-node-template-actions">
									<a class="fl-builder-node-template-edit" href="{{column.link}}" target="_blank">
										<i class="fas fa-wrench"></i>
									</a>
									<a class="fl-builder-node-template-delete" href="javascript:void(0);">
										<i class="fas fa-times"></i>
									</a>
								</span>
							</div>
						</span>
					</span>
					<# } #>
				<# } #>
			</div>
		</div>
		<?php endif; ?>
		<div id="fl-builder-blocks-saved-modules" class="fl-builder-blocks-section fl-builder-blocks-node-template">

			<div class="fl-builder-blocks-section-header">
				<span class="fl-builder-blocks-section-title"><?php _e( 'Saved Modules', 'fl-builder' ); ?></span>
			</div>
			<div class="fl-builder-blocks-section-content fl-builder-saved-modules">
			<# if (modules.length === 0) { #>
			<span class="fl-builder-block-no-node-templates"><?php _e( 'No saved modules found.', 'fl-builder' ); ?></span>
			<# } else { #>
				<# for( var i in modules) {
					var module = modules[i];
					image = module.image,
					hasImage = image && !image.endsWith('blank.jpg'),
					hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '' ;
					var globalClass = module.isGlobal ? ' fl-builder-block-global' : '';
				#>
				<span class="fl-builder-block fl-builder-block-saved-module{{globalClass}} {{hasImageClass}}" data-id="{{module.id}}">
					<span class="fl-builder-block-content">
						<# if ( hasImage ) { #>
						<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
						<# } #>
						<div class="fl-builder-block-details">
							<div class="fl-builder-block-title" title="{{module.name}}">{{module.name}}</div>
							<# if (module.isGlobal) { #>
							<div class="fl-builder-badge fl-builder-badge-global">
								<?php _ex( 'Global', 'Indicator for global node templates.', 'fl-builder' ); ?>
							</div>
							<# } #>
							<span class="fl-builder-node-template-actions">
								<a class="fl-builder-node-template-edit" href="{{module.link}}" target="_blank">
									<i class="fas fa-wrench"></i>
								</a>
								<a class="fl-builder-node-template-delete" href="javascript:void(0);">
									<i class="fas fa-times"></i>
								</a>
							</span>
						</div>
					</span>
				</span>
				<# } #>
			<# } #>
		</div>
	</div>

</script>
<!-- #tmpl-fl-content-panel-saved-view -->

<script type="text/html" id="tmpl-fl-content-panel-saved-modules">
	<#
	var modules = data.queryResults.library.template.items;
	#>
	<div id="fl-builder-blocks-saved-modules" class="fl-builder-blocks-section fl-builder-blocks-node-template">
		<div class="fl-builder-blocks-section-content fl-builder-saved-modules">
		<# if (modules.length === 0) { #>
		<span class="fl-builder-block-no-node-templates"><?php _e( 'No saved modules found.', 'fl-builder' ); ?></span>
		<# } else { #>
			<# for( var i in modules) {
				var module = modules[i],
					image = module.image,
					globalClass = module.isGlobal ? ' fl-builder-block-global' : '',
					image = module.image,
					hasImage = image && !image.endsWith( 'blank.jpg' ),
					hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '' ;
			#>
			<span class="fl-builder-block fl-builder-block-saved-module {{globalClass}} {{hasImageClass}}" data-id="{{module.id}}">
				<span class="fl-builder-block-content">
					<# if (hasImage) { #>
					<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
					<# } #>
					<div class="fl-builder-block-details">
						<div class="fl-builder-block-title" title="{{module.name}}">{{module.name}}</div>
						<# if (module.isGlobal) { #>
						<div class="fl-builder-badge fl-builder-badge-global">
							<?php _ex( 'Global', 'Indicator for global node templates.', 'fl-builder' ); ?>
						</div>
						<# } #>
						<span class="fl-builder-node-template-actions">
							<a class="fl-builder-node-template-edit" href="{{module.link}}" target="_blank">
								<i class="fas fa-wrench"></i>
							</a>
							<a class="fl-builder-node-template-delete" href="javascript:void(0);">
								<i class="fas fa-times"></i>
							</a>
						</span>
					</div>
				</span>
			</span>
			<# } #>
		<# } #>
	</div>
</script>
<!-- #tmpl-fl-content-panel-saved-modules -->

<script type="text/html" id="tmpl-fl-content-panel-saved-columns">
	<#
	var columns = data.queryResults.library.template.items;
	#>
	<div id="fl-builder-blocks-saved-columns" class="fl-builder-blocks-section fl-builder-blocks-node-template">
		<div class="fl-builder-blocks-section-content fl-builder-saved-columns">
		<# if (columns.length === 0) { #>
		<span class="fl-builder-block-no-node-templates"><?php _e( 'No saved columns found.', 'fl-builder' ); ?></span>
		<# } else { #>
			<# for( var i in columns) {
				var column = columns[i],
					image = column.image,
					globalClass = column.isGlobal ? ' fl-builder-block-global' : '',
					image = column.image,
					hasImage = image && !image.endsWith( 'blank.jpg' ),
					hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '' ;
			#>
			<span class="fl-builder-block fl-builder-block-saved-column {{globalClass}} {{hasImageClass}}" data-id="{{column.id}}">
				<span class="fl-builder-block-content">
					<# if (hasImage) { #>
					<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
					<# } #>
					<div class="fl-builder-block-details">
						<div class="fl-builder-block-title" title="{{column.name}}">{{column.name}}</div>
						<# if (column.isGlobal) { #>
						<div class="fl-builder-badge fl-builder-badge-global">
							<?php _ex( 'Global', 'Indicator for global node templates.', 'fl-builder' ); ?>
						</div>
						<# } #>
						<span class="fl-builder-node-template-actions">
							<a class="fl-builder-node-template-edit" href="{{column.link}}" target="_blank">
								<i class="fas fa-wrench"></i>
							</a>
							<a class="fl-builder-node-template-delete" href="javascript:void(0);">
								<i class="fas fa-times"></i>
							</a>
						</span>
					</div>
				</span>
			</span>
			<# } #>
		<# } #>
	</div>
</script>
<!-- #tmpl-fl-content-panel-saved-columns -->

<script type="text/html" id="tmpl-fl-content-panel-saved-rows">
	<#
	var rows = data.queryResults.library.template.items;
	#>
	<div id="fl-builder-blocks-saved-rows" class="fl-builder-blocks-section fl-builder-blocks-node-template">
		<div class="fl-builder-blocks-section-content fl-builder-saved-rows">
		<# if (rows.length === 0) { #>
			<span class="fl-builder-block-no-node-templates"><?php _e( 'No saved rows found.', 'fl-builder' ); ?></span>
		<# } else { #>
			<# for( var i in rows) {
				var row = rows[i],
					globalClass = row.isGlobal ? 'fl-builder-block-global' : '',
					image = row.image,
					hasImage = image && !image.endsWith( 'blank.jpg' ),
					hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '' ;
			#>
			<span class="fl-builder-block fl-builder-block-saved-row {{globalClass}} {{hasImageClass}}" data-id="{{row.id}}">
				<span class="fl-builder-block-content">
					<# if (image && !image.endsWith('blank.jpg')) { #>
					<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
					<# } #>
					<div class="fl-builder-block-details">
						<div class="fl-builder-block-title" title="{{row.name}}">{{row.name}}</div>
						<# if (row.isGlobal) { #>
						<div class="fl-builder-badge fl-builder-badge-global">
							<?php _ex( 'Global', 'Indicator for global node templates.', 'fl-builder' ); ?>
						</div>
						<# } #>
						<span class="fl-builder-node-template-actions">
							<a class="fl-builder-node-template-edit" href="{{row.link}}" target="_blank">
								<i class="fas fa-wrench"></i>
							</a>
							<a class="fl-builder-node-template-delete" href="javascript:void(0);">
								<i class="fas fa-times"></i>
							</a>
						</span>
					</div>
				</span>
			</span>
			<# } #>
		<# } #>
		</div>
	</div>
</script>
<!-- #tmpl-fl-content-panel-saved-rows -->

<script type="text/html" id="tmpl-fl-content-panel-saved-templates">
	<div class="fl-user-templates">
		<div class="fl-builder--user-templates-section-content">
			<div class="fl-user-template" data-id="blank">
				<div class="fl-user-template-thumbnail">
					<div class="fl-builder--template-thumbnail"></div>
				</div>
				<span class="fl-user-template-name"><?php _ex( 'Blank', 'Template name.', 'fl-builder' ); ?></span>
				<div class="fl-clear"></div>
			</div>
		</div>
		<#

		var queryResults = data.queryResults.library.template,
			templates 			= null,
			categories			= {},
			showCategoryName	= false,
			categoryName 		= '';

		if ( _.isUndefined( queryResults.categorized ) ) {
			for ( var slug in queryResults.items[0].category ) {
				categoryName = queryResults.items[0].category[ slug ];
				break;
			}
			categories[ categoryName ] = queryResults.items;
		} else {
			categories 			= data.queryResults.library.template.categorized,
			showCategoryName 	= true !== ( Object.keys( categories ).length <= 1 );
		}

		for ( var categoryHandle in categories ) {

			templates = categories[ categoryHandle ]

			if ( showCategoryName ) { #>
			<div class="fl-builder--user-templates-section-name">{{categoryHandle}}</div>
			<# } #>
			<div class="fl-builder--user-templates-section-content">
				<# for( var i in templates ) {
					var template = templates[ i ];
				#>
				<div class="fl-user-template" data-id="{{template.postId}}">
					<div class="fl-user-template-actions">
						<a class="fl-user-template-edit" href="{{template.link}}"><i class="fas fa-wrench"></i></a>
						<a class="fl-user-template-delete" href="javascript:void(0);" onclick="return false;"><i class="fas fa-times"></i></a>
					</div>
					<div class="fl-user-template-thumbnail">
						<div class="fl-builder--template-thumbnail" style="background-image:url({{template.image}})"></div>
					</div>
					<span class="fl-user-template-name">{{template.name}}</span>
					<div class="fl-clear"></div>
				</div>
				<# } /* #>
				<div class="fl-builder--save-new-user-template">
					<div class="fl-user-template-thumbnail">
						<div class="fl-builder--template-thumbnail"></div>
					</div>
					<div class="fl-save-control">
						<input name="template-name" placeholder="<?php _e( 'Save New Template', 'fl-builder' ); ?>" type="text">
						<button class="fl-button"><?php _e( 'Save', 'fl-builder' ); ?></button>
						<input type="hidden" name="template-category" value="{{categoryHandle}}" >
					</div>
				</div>
				<div class="fl-save-control-mask"></div>
				<# */ #>
			</div>

		<# } #>
	</div>
</script>
<!-- #tmpl-fl-content-panel-saved-templates -->
