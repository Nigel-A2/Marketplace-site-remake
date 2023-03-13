<script type="text/html" id="tmpl-fl-row-overlay">
	<div class="fl-row-overlay fl-block-overlay<# if ( data.global ) { #> fl-block-overlay-global<# } #>">
		<div class="fl-block-overlay-header">
			<div class="fl-block-overlay-actions">
				<# if ( data.global && ! FLBuilderConfig.userCanEditGlobalTemplates ) { #>
				<i class="fas fa-lock fl-tip" title="<?php _e( 'Locked', 'fl-builder' ); ?>"></i>
				<# } else { #>
				<?php if ( ! FLBuilderModel::is_post_user_template( 'row' ) && ! $simple_ui ) : ?>
				<i class="fl-block-move fas fa-arrows-alt fl-tip" title="<?php _e( 'Move', 'fl-builder' ); ?>"></i>
				<?php endif; ?>
				<i class="fl-block-settings fas fa-wrench fl-tip" title="<?php _e( 'Row Settings', 'fl-builder' ); ?>"></i>
				<?php if ( ! FLBuilderModel::is_post_user_template( 'row' ) && ! $simple_ui ) : ?>
				<i class="fl-block-copy far fa-clone fl-tip" title="<?php _e( 'Duplicate', 'fl-builder' ); ?>"></i>
				<?php endif; ?>
				<?php if ( ! $simple_ui ) : ?>
				<# if ( ! data.global || ( data.global && 'row' == FLBuilderConfig.userTemplateType ) ) { #>
				<span class="fl-builder-has-submenu">
					<i class="fl-block-col-settings fas fa-bars fl-tip" title="<?php _e( 'Row Actions', 'fl-builder' ); ?>"></i>
					<ul class="fl-builder-submenu fl-block-col-submenu">
						<li><a class="fl-block-col-reset" href="javascript:void(0);"><?php _e( 'Reset Column Widths', 'fl-builder' ); ?></a></li>
						<li><a class="fl-block-row-reset" href="javascript:void(0);"><?php _e( 'Reset Row Width', 'fl-builder' ); ?></a></li>
					</ul>
				</span>
				<# } #>
				<?php endif; ?>
				<?php if ( ! FLBuilderModel::is_post_user_template( 'row' ) && ! $simple_ui ) : ?>
				<i class="fl-block-remove fas fa-times fl-tip" title="<?php _e( 'Remove', 'fl-builder' ); ?>"></i>
				<?php endif; ?>
				<# } #>
			</div>
			<div class="fl-clear"></div>
		</div>
		<# if ( data.hasRules ) { #>
		<i class="fas fa-eye fl-tip fl-block-has-rules {{data.rulesTypeRow}}" title="<?php _e( 'This row has visibility rules', 'fl-builder' ); ?>: {{data.rulesTextRow}}"></i>
		<# } #>
	</div>
</script>
<!-- #tmpl-fl-row-overlay -->

<script type="text/html" id="tmpl-fl-col-overlay">
	<div class="fl-col-overlay fl-block-overlay<# if ( data.global ) { #> fl-block-overlay-global<# } #>">
		<div class="fl-block-overlay-header">
			<div class="fl-block-overlay-actions">
				<# if ( data.global && ! FLBuilderConfig.userCanEditGlobalTemplates ) { #>
				<i class="fas fa-lock fl-tip" title="<?php _e( 'Locked', 'fl-builder' ); ?>"></i>
				<# } else { #>
				<?php if ( ! $simple_ui ) : ?>
				<# if ( ! data.isRootCol ) { #>
					<i class="fl-block-move fas fa-arrows-alt fl-tip" title="<?php _e( 'Move', 'fl-builder' ); ?>"></i>
					<# if ( ( ! data.hasParentCol && data.numCols < 12 ) || ( data.hasParentCol && data.numCols < 4 ) ) { #>
					<i class="fl-block-copy fl-block-col-copy far fa-clone fl-tip" title="<?php _e( 'Duplicate', 'fl-builder' ); ?>"></i>
					<# } #>
				<# } #>
				<?php endif; ?>
				<span class="fl-builder-has-submenu">
					<i class="fl-block-settings fas fa-columns fl-tip" title="<?php _e( 'Edit Column', 'fl-builder' ); ?>"></i>
					<?php if ( ! $simple_ui ) : ?>
					<# if ( ! data.global || ( data.global && FLBuilderConfig.userTemplateType ) ) { #>
					<ul class="fl-builder-submenu fl-block-col-submenu">
						<li><a class="fl-block-col-edit" href="javascript:void(0);"><?php _e( 'Column Settings', 'fl-builder' ); ?></a></li>
						<# if ( data.numCols > 1 || ( data.hasParentCol && data.numParentCols > 1 ) ) { #>
						<li><a class="fl-block-col-reset" href="javascript:void(0);"><?php _e( 'Reset Column Widths', 'fl-builder' ); ?></a></li>
						<# } #>
						<# if ( data.rowIsFixedWidth ) { #>
						<li><a class="fl-block-row-reset" href="javascript:void(0);"><?php _e( 'Reset Row Width', 'fl-builder' ); ?></a></li>
						<# } #>
						<# if ( data.hasParentCol ) { #>
						<li class="fl-builder-submenu-sep"><div></div></li>
						<# if ( 'column' != FLBuilderConfig.userTemplateType ) { #>
						<li><a class="fl-block-col-move-parent" href="javascript:void(0);"><?php _e( 'Move Parent', 'fl-builder' ); ?><i class="fas fa-arrows-alt"></i></a></li>
						<# } #>
						<li><a class="fl-block-col-edit-parent" href="javascript:void(0);"><?php _e( 'Parent Settings', 'fl-builder' ); ?></a></li>
						<# } #>
					</ul>
					<# } #>
					<?php endif; ?>
				</span>
				<?php if ( ! $simple_ui ) : ?>
				<# if ( ! data.isRootCol ) { #>
				<i class="fl-block-remove fas fa-times fl-tip" title="<?php _e( 'Remove', 'fl-builder' ); ?>"></i>
				<# } #>
				<?php endif; ?>
				<# } #>
			</div>
			<div class="fl-clear"></div>
		</div>
		<# if ( data.hasRules ) { #>
		<i class="fas fa-eye fl-tip fl-block-has-rules" title="<?php _e( 'This column has visibility rules.', 'fl-builder' ); ?>"></i>
		<# } #>
		<?php if ( ! $simple_ui ) : ?>
		<# if ( ! data.groupLoading ) { #>
			<# if ( ! data.first || ( data.hasParentCol && data.first && ! data.parentFirst ) ) { #>
			<div class="fl-block-col-resize fl-block-col-resize-w<# if ( data.hasParentCol && data.first && ! data.parentFirst ) { #> fl-block-col-resize-parent<# } #>">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
			<# if ( ! data.last || ( data.hasParentCol && data.last && ! data.parentLast ) ) { #>
			<div class="fl-block-col-resize fl-block-col-resize-e<# if ( data.hasParentCol && data.last && ! data.parentLast ) { #> fl-block-col-resize-parent<# } #>">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
		<# } #>
		<# if ( data.userCanResizeRows ) { #>
			<# if ( ( ( data.first && ! data.hasParentCol ) || ( data.first && data.parentFirst ) ) && data.rowIsFixedWidth ) { #>
			<div class="fl-block-row-resize fl-block-col-resize fl-block-col-resize-w">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
			<# if ( ( ( data.last && ! data.hasParentCol ) || ( data.last && data.parentLast ) ) && data.rowIsFixedWidth ) { #>
			<div class=" fl-block-row-resize fl-block-col-resize fl-block-col-resize-e">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
		<# } #>
		<?php endif; ?>
	</div>
</script>
<!-- #tmpl-fl-col-overlay -->

<script type="text/html" id="tmpl-fl-module-overlay">
	<div class="fl-module-overlay fl-block-overlay<# if ( data.global ) { #> fl-block-overlay-global<# } #>">
		<div class="fl-block-overlay-header">
			<div class="fl-block-overlay-actions">
				<# if ( data.global && ! FLBuilderConfig.userCanEditGlobalTemplates ) { #>
				<i class="fas fa-lock fl-tip" title="<?php _e( 'Locked', 'fl-builder' ); ?>"></i>
				<# } else { #>
				<?php if ( ! FLBuilderModel::is_post_user_template( 'module' ) && ! $simple_ui ) : ?>
				<i class="fl-block-move fas fa-arrows-alt fl-tip" title="<?php _e( 'Move', 'fl-builder' ); ?>"></i>
				<?php endif; ?>
				<?php /* translators: %s: module name */ ?>
				<i class="fl-block-settings fas fa-wrench fl-tip" title="<?php printf( __( '%s Settings', 'fl-builder' ), '{{data.moduleName}}' ); ?>"></i>
				<?php if ( ! FLBuilderModel::is_post_user_template( 'module' ) && ! $simple_ui ) : ?>
				<i class="fl-block-copy far fa-clone fl-tip" title="<?php _e( 'Duplicate', 'fl-builder' ); ?>"></i>
				<span class="fl-builder-has-submenu">
					<i class="fl-block-col-settings fas fa-columns fl-tip" title="<?php _e( 'Edit Column', 'fl-builder' ); ?>"></i>
					<# if ( ! data.isRootCol ) { #>
					<ul class="fl-builder-submenu fl-block-col-submenu">
						<li><a class="fl-block-col-edit" href="javascript:void(0);"><?php _e( 'Column Settings', 'fl-builder' ); ?></a></li>
						<li><a class="fl-block-col-move" href="javascript:void(0);"><?php _e( 'Move Column', 'fl-builder' ); ?><i class="fas fa-arrows-alt"></i></a></li>
						<# if ( ( ! data.hasParentCol && data.numCols < 12 ) || ( data.hasParentCol && data.numCols < 4 ) ) { #>
						<li><a class="fl-block-col-copy" href="javascript:void(0);"><?php _e( 'Duplicate Column', 'fl-builder' ); ?></a></li>
						<# } #>
						<li><a class="fl-block-col-delete" href="javascript:void(0);"><?php _e( 'Delete Column', 'fl-builder' ); ?></a></li>
						<# if ( data.numCols > 1 || ( data.hasParentCol && data.numParentCols > 1 ) ) { #>
						<li><a class="fl-block-col-reset" href="javascript:void(0);"><?php _e( 'Reset Column Widths', 'fl-builder' ); ?></a></li>
						<# } #>
						<# if ( data.rowIsFixedWidth ) { #>
						<li><a class="fl-block-row-reset" href="javascript:void(0);"><?php _e( 'Reset Row Width', 'fl-builder' ); ?></a></li>
						<# } #>
						<# if ( data.hasParentCol ) { #>
						<li class="fl-builder-submenu-sep"><div></div></li>
						<# if ( 'column' != FLBuilderConfig.userTemplateType ) { #>
						<li><a class="fl-block-col-move-parent" href="javascript:void(0);"><?php _e( 'Move Parent', 'fl-builder' ); ?><i class="fas fa-arrows-alt"></i></a></li>
						<# } #>
						<li><a class="fl-block-col-edit-parent" href="javascript:void(0);"><?php _e( 'Parent Settings', 'fl-builder' ); ?></a></li>
						<# } #>
					</ul>
					<# } #>
				</span>
				<i class="fl-block-remove fas fa-times fl-tip" title="<?php _e( 'Remove', 'fl-builder' ); ?>"></i>
				<?php endif; ?>
				<# } #>
			</div>
			<div class="fl-clear"></div>
		</div>
		<# if ( data.colHasRules ) { #>
		<i class="fas fa-eye fl-tip fl-block-has-rules {{data.rulesTypeCol}}" title="<?php _e( 'This column has visibility rules', 'fl-builder' ); ?>: {{data.rulesTextCol}}"></i>
		<# } else if ( data.hasRules ) { #>
		<i class="fas fa-eye fl-tip fl-block-has-rules {{data.rulesTypeModule}}" title="<?php _e( 'This module has visibility rules', 'fl-builder' ); ?>: {{data.rulesTextModule}}"></i>
		<# } #>
		<?php if ( ! FLBuilderModel::is_post_user_template( 'module' ) && ! $simple_ui ) : ?>
		<# if ( ! data.groupLoading && ! data.isRootCol ) { #>
			<# if ( ! data.colFirst || ( data.hasParentCol && data.colFirst && ! data.parentFirst ) ) { #>
			<div class="fl-block-col-resize fl-block-col-resize-w<# if ( data.hasParentCol && data.colFirst && ! data.parentFirst ) { #> fl-block-col-resize-parent<# } #>">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
			<# if ( ! data.colLast || ( data.hasParentCol && data.colLast && ! data.parentLast ) ) { #>
			<div class="fl-block-col-resize fl-block-col-resize-e<# if ( data.hasParentCol && data.colLast && ! data.parentLast ) { #> fl-block-col-resize-parent<# } #>">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
		<# } #>
		<# if ( data.userCanResizeRows ) { #>
			<# if ( ( ( data.colFirst && ! data.hasParentCol ) || ( data.colFirst && data.parentFirst ) ) && data.rowIsFixedWidth ) { #>
			<div class="fl-block-row-resize fl-block-col-resize fl-block-col-resize-w">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
			<# if ( ( ( data.colLast && ! data.hasParentCol ) || ( data.colLast && data.parentLast ) ) && data.rowIsFixedWidth ) { #>
			<div class="fl-block-row-resize fl-block-col-resize fl-block-col-resize-e">
				<div class="fl-block-col-resize-handle-wrap">
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-left"></div>
					<div class="fl-block-col-resize-handle"></div>
					<div class="fl-block-col-resize-feedback fl-block-col-resize-feedback-right"></div>
				</div>
			</div>
			<# } #>
		<# } #>
		<?php endif; ?>
	</div>
</script>
<!-- #tmpl-fl-module-overlay -->

<script type="text/html" id="tmpl-fl-overlay-overflow-menu">
	<span class="fl-builder-has-submenu">
		<i class="fl-block-overflow-menu fas fa-bars fl-tip" title="<?php _e( 'More', 'fl-builder' ); ?>"></i>
		<ul class="fl-builder-submenu">
			<# for( var i = 0; i < data.length; i++ ) { #>
				<# if ( 'submenu' == data[ i ].type ) { #>
				<li class="fl-builder-has-submenu"><a href="javascript:void(0);">{{data[ i ].label}}<i class="fas fa-caret-right"></i></a>
					{{{data[ i ].submenu}}}
				</li>
				<# } else { #>
				<li><a class="{{data[ i ].className}}" href="javascript:void(0);">{{data[ i ].label}}<# if ( data[ i ].className.indexOf( 'fl-block-move' ) > -1 ) { #><i class="fas fa-arrows-alt"></i><# } #></a>
				<# } #>
			<# } #>
		</ul>
	</span>
</script>
<!-- #tmpl-fl-overlay-overflow-menu -->

<script type="text/html" id="tmpl-fl-actions-lightbox">
	<div class="fl-builder-actions {{data.className}}">
		<span class="fl-builder-actions-title">{{data.title}}</span>
		<# for( var i in data.buttons ) { #>
		<span class="fl-builder-{{data.buttons[ i ].key}}-button fl-builder-button fl-builder-button-large">{{data.buttons[ i ].label}}</span>
		<# } #>
		<span class="fl-builder-cancel-button fl-builder-button fl-builder-button-primary fl-builder-button-large"><?php _e( 'Cancel', 'fl-builder' ); ?></span>
	</div>
</script>
<!-- #tmpl-fl-actions-lightbox -->

<script type="text/html" id="tmpl-fl-alert-lightbox">
	<div class="fl-lightbox-message">{{{data.message}}}</div>
	<div class="fl-lightbox-footer">
		<span class="fl-builder-alert-close fl-builder-button fl-builder-button-large fl-builder-button-primary" href="javascript:void(0);"><?php _e( 'OK', 'fl-builder' ); ?></span>
	</div>
</script>
<!-- #tmpl-fl-alert-lightbox -->

<script type="text/html" id="tmpl-fl-pro-lightbox">
	<span class="dashicons dashicons-no" onclick="FLLightbox.closeParent( this )"></span>
	<div class="fl-pro-message-badge">
		<span>PRO</span>
	</div>
	<div class="fl-pro-message-title">{{data.feature}} is a Pro Feature</div>
	<div class="fl-pro-message-content">We're sorry, {{data.feature}} is not available on your plan. Please upgrade to unlock all these awesome features.</div>
	<div class="fl-pro-message-button">
		<button class="fl-builder-upgrade-button fl-builder-button" onclick="FLBuilder._upgradeClicked()">
			<?php _ex( 'Upgrade', 'Link to learn more about premium Beaver Builder', 'fl-builder' ); ?>
		</button>
	</div>
</script>
<!-- #tmpl-fl-pro-lightbox -->

<script type="text/html" id="tmpl-fl-crash-lightbox">
	<div class="fl-lightbox-message">{{{data.message}}}</div>
	<# if ( data.debug ) { #>
		<div class="fl-lightbox-message-info">Here is the message reported in your browser’s JavaScript console.<pre>{{{data.debug}}}</pre></div>
	<# } #>
	<div class="fl-lightbox-message-info">{{{data.info}}}</div>
	<div class="fl-lightbox-footer">
		<span class="fl-builder-alert-close fl-builder-button fl-builder-button-large fl-builder-button-primary" href=2"javascript:void(0);"><?php _e( 'OK', 'fl-builder' ); ?></span>
	</div>
</script>
<!-- #tmpl-fl-crash-lightbox -->

<script type="text/html" id="tmpl-fl-confirm-lightbox">
	<div class="fl-lightbox-message">{{{data.message}}}</div>
	<div class="fl-lightbox-footer">
		<span class="fl-builder-confirm-cancel fl-builder-alert-close fl-builder-button fl-builder-button-large" href="javascript:void(0);">{{data.strings.cancel}}</span>
		<span class="fl-builder-confirm-ok fl-builder-alert-close fl-builder-button fl-builder-button-large fl-builder-button-primary" href="javascript:void(0);">{{data.strings.ok}}</span>
	</div>
</script>
<!-- #tmpl-fl-confirm-lightbox -->

<script type="text/html" id="tmpl-fl-tour-lightbox">
	<div class="fl-builder-actions fl-builder-tour-actions">
		<span class="fl-builder-actions-title"><?php _e( 'Welcome! It looks like this might be your first time using the builder. Would you like to take a tour?', 'fl-builder' ); ?></span>
		<span class="fl-builder-no-tour-button fl-builder-button fl-builder-button-large"><?php _e( 'No Thanks', 'fl-builder' ); ?></span>
		<span class="fl-builder-yes-tour-button fl-builder-button fl-builder-button-primary fl-builder-button-large"><?php _e( 'Yes Please!', 'fl-builder' ); ?></span>
	</div>
</script>
<!-- #tmpl-fl-tour-lightbox -->

<script type="text/html" id="tmpl-fl-video-lightbox">
	<div class="fl-lightbox-header">
		<h1><?php _e( 'Getting Started Video', 'fl-builder' ); ?></h1>
	</div>
	<div class="fl-builder-getting-started-video">{{{data.video}}}</div>
	<div class="fl-lightbox-footer">
		<span class="fl-builder-settings-cancel fl-builder-button fl-builder-button-large fl-builder-button-primary" href="javascript:void(0);"><?php _e( 'Done', 'fl-builder' ); ?></span>
	</div>
</script>
<!-- #tmpl-fl-video-lightbox -->

<script type="text/html" id="tmpl-fl-responsive-preview">
	<div class="fl-responsive-preview-mask"></div>
	<div class="fl-responsive-preview">
		<div class="fl-responsive-preview-message">
			<span>
				<?php _e( 'Responsive Editing', 'fl-builder' ); ?>
			</span>
			<button class="fl-builder-button fl-builder-button-large" data-mode="responsive">
				<i class="dashicons dashicons-smartphone"></i>
			</button>
			<button class="fl-builder-button fl-builder-button-large" data-mode="medium">
				<i class="dashicons dashicons-tablet"></i>
			</button>
			<button class="fl-builder-button fl-builder-button-large" data-mode="default">
				<?php _e( 'Exit', 'fl-builder' ); ?>
			</button>
			<span class="size"></span>
		</div>
		<div class="fl-responsive-preview-content"></div>
	</div>
</script>
<!-- #tmpl-fl-responsive-preview -->

<script type="text/html" id="tmpl-fl-search-results-panel">
	<div class="fl-builder--search-results">
		<#
		var grouped = data.grouped;
		for( var groupSlug in grouped) {
			var cats = grouped[groupSlug];
			#>
			<div class="fl-builder-blocks-group">
				<# if ( _.isUndefined( FLBuilderConfig.moduleGroups[ groupSlug ] ) ) { #>
				<span class="fl-builder-blocks-section-group-name"><?php _e( 'Standard Modules', 'fl-builder' ); ?></span>
				<# } else { #>
				<span class="fl-builder-blocks-section-group-name">{{FLBuilderConfig.moduleGroups[ groupSlug ]}}</span>
				<# } #>
			<#
			for( var catName in cats) {
				var modules = cats[catName];

				modules.sort(function(a, b) {
					if (a.name < b.name)
						return -1;
					if (a.name > b.name)
						return 1;
					return 0;
				});
				#>
				<div class="fl-builder-blocks-section">
					<span class="fl-builder-blocks-section-title">{{catName}}</span>
					<div class="fl-builder-blocks-section-content fl-builder-modules">
					<#
					for( var i in modules ) {
						var module 	= modules[i],
							type 	= module.isWidget ? 'widget' : module.slug,
							alias 	= module.isAlias ? ' data-alias="' + module.alias + '"' : '',
							widget 	= module.isWidget ? ' data-widget="' + module.class + '"' : '',
							name 	= module.name;
						#>
						<span class="fl-builder-block fl-builder-block-module" data-type="{{type}}"{{{alias}}}{{{widget}}}>
							<span class="fl-builder-block-content">
								<span class="fl-builder-block-icon">{{{module.icon}}}</span>
								<span class="fl-builder-block-title" title="{{name}}">{{name}}</span>
							</span>
						</span>
					<# } #>
					</div>
				</div>
			<# } #>
			</div>
		<# } #>
	</div>
</script>
<!-- #tmpl-fl-search-results-panel -->

<script type="text/html" id="tmpl-fl-search-no-results">
	<div class="fl-builder--no-results"><?php _ex( 'No Results Found', 'No content panel search results found', 'fl-builder' ); ?></div>
</script>
<!-- #tmpl-fl-search-no-results -->

<script type="text/html" id="tmpl-fl-main-menu-panel">
	<div class="fl-builder--main-menu-panel-mask"></div>
	<div class="fl-builder--main-menu-panel">
		<div class="fl-builder--main-menu-panel-views"></div>
	</div>
</script>
<!-- #tmpl-fl-main-menu-panel -->

<script type="text/html" id="tmpl-fl-main-menu-panel-view">
	<#
	var viewClasses = [],
		backItem;

	if (data.isShowing) {
		viewClasses.push('is-showing');
	}
	if (data.isRootView) {
		viewClasses.push('is-root-view');
	}

	viewClasses = viewClasses.join(' ');

	if (!data.isRootView) {
		backItem = '<button class="pop-view">&larr;</button>';
	}
	#>
	<div class="fl-builder--main-menu-panel-view {{viewClasses}}" data-name="{{data.handle}}">
		<div class="fl-builder--main-menu-panel-view-title">{{{backItem}}}{{{data.name}}}</div>

		<div class="fl-builder--menu">
			<# for (var key in data.items) {
				var item  = data.items[key];
				var extra = '';
				if ( 'revisions' === item.view && FLBuilderConfig.revisions_count > 0 ) {
					extra = '[' + FLBuilderConfig.revisions_count + ']';
				}
				if ( 'event' === item.type && 'showLayoutSettings' === item.eventName ) {
					if( FLBuilderConfig.layout_css_js ) {
						extra = '&bull;';
					}
				}
				if ( 'event' === item.type && 'showGlobalSettings' === item.eventName ) {
					if( '' !== FLBuilderConfig.global.css || '' !== FLBuilderConfig.global.js ) {
						extra = '&bull;';
					}
				}
				switch(item.type) {
					case "separator":
						#><hr><#
						break;
					case "event":
						#>
						<button class="fl-builder--menu-item" data-type="event" data-event="{{item.eventName}}">{{{item.label}}}<span class="menu-event event-{{item.eventName}}">{{{extra}}}</span><span class="fl-builder--menu-item-accessory">{{{item.accessory}}}</span></button>
						<#
						break;
					case "link":
						#>
						<a class="fl-builder--menu-item" href="{{{item.url}}}" data-type="link" target="_blank">{{{item.label}}} <span class="fl-builder--menu-item-accessory"><i class="fas fa-external-link-alt"></i></span></a>
						<#
						break;
					case "view":
						#>
						<button class="fl-builder--menu-item" data-type="view" data-view="{{item.view}}">{{{item.label}}}<span class="menu-view view-{{item.view}}">{{extra}}</span><span class="fl-builder--menu-item-accessory">&rarr;</span></button>
						<#
						break;
					case "video":
						#>
						<div class="fl-builder-video-wrap">
							{{{item.embed}}}
						</div>
						<#
						break;
					default:
				}
			}
			#>
		</div>
	</div>
</script>
<!-- #tmpl-fl-main-menu-panel-view -->

<script type="text/html" id="tmpl-fl-toolbar">
<?php include FL_BUILDER_DIR . 'includes/ui-bar.php'; ?>
</script>
<!-- #tmpl-fl-toolbar -->

<script type="text/html" id="tmpl-fl-content-panel-base">
	<div class="fl-builder--content-library-panel fl-builder-panel">
		<div class="fl-builder--panel-arrow">
			<svg width="29px" height="15px" viewBox="0 0 29 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<g transform="translate(-260.000000, -14.000000)">
					<polygon transform="translate(274.142136, 28.142136) rotate(-315.000000) translate(-274.142136, -28.142136) " points="264.142136 18.1421356 284.142136 18.1421356 264.142136 38.1421356"></polygon>
				</g>
			</svg>
		</div>
		<div class="fl-builder--panel-header">
			<div class="fl-builder-panel-drag-handle">
				<svg viewBox="0 0 6 30" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g fill-rule="nonzero" >
						<polygon points="0 2 6 2 6 0 0 0"></polygon>
						<polygon points="0 6 6 6 6 4 0 4"></polygon>
						<polygon points="0 10 6 10 6 8 0 8"></polygon>
						<polygon points="0 14 6 14 6 12 0 12"></polygon>
						<polygon points="0 18 6 18 6 16 0 16"></polygon>
						<polygon points="0 22 6 22 6 20 0 20"></polygon>
						<polygon points="0 26 6 26 6 24 0 24"></polygon>
						<polygon points="0 30 6 30 6 28 0 28"></polygon>
					</g>
				</svg>
			</div>
			<div class="fl-builder--tabs">
				<div class="fl-builder--tab-wrap">
				<# for (var handle in data.tabs) {
					var tab = data.tabs[handle];
					if (!tab.shouldShowTabItem || "" == tab.name ) {
						continue;
					}
					var isShowingClass = (tab.isShowing) ? 'is-showing' : '' ;
					#>
					<button data-tab="{{tab.handle}}" class="fl-builder--tab-button {{isShowingClass}}">{{tab.name}}</button>
					<#
				}
				#>
				</div>
			</div>
			<div class="fl-builder--panel-controls">
				<div class="fl-builder-content-group-select"></div>
				<div class="fl-builder-panel-search">
					<button class="fl-builder-toggle-panel-search">
						<svg viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<g class="filled-shape" transform="translate(-528.000000, -17.000000)">
									<path d="M539.435106,27.0628931 L538.707833,27.0628931 L538.456261,26.8113208 C539.352773,25.7730132 539.89251,24.4236707 539.89251,22.946255 C539.89251,19.6620926 537.230417,17 533.946255,17 C530.662093,17 528,19.6620926 528,22.946255 C528,26.2304174 530.662093,28.89251 533.946255,28.89251 C535.423671,28.89251 536.773013,28.352773 537.811321,27.4608348 L538.062893,27.7124071 L538.062893,28.4351058 L542.636935,33 L544,31.6369354 L539.435106,27.0628931 Z M534,27 C531.791111,27 530,25.2088889 530,23 C530,20.7911111 531.791111,19 534,19 C536.208889,19 538,20.7911111 538,23 C538,25.2088889 536.208889,27 534,27 Z"></path>
								</g>
							</g>
						</svg>
					</button>
					<div class="fl-builder-panel-search-input">
						<input name="search-term" placeholder="<?php _e( 'Search Modules', 'fl-builder' ); ?>" />
						<button class="fl-builder-dismiss-panel-search">
							<svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
								<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<polygon class="filled-shape" points="20 2.02142857 17.9785714 0 10 7.97857143 2.02142857 0 0 2.02142857 7.97857143 10 0 17.9785714 2.02142857 20 10 12.0214286 17.9785714 20 20 17.9785714 12.0214286 10"></polygon>
								</g>
							</svg>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="fl-builder--panel-content">
			<# for (var handle in data.tabs) {
				var tab = data.tabs[handle];
				if (!tab.shouldShowTabItem) {
					continue;
				}
				var isShowingClass = (tab.isShowing) ? 'is-showing' : '' ;
				#>
			<div data-tab="{{tab.handle}}" class="fl-builder--panel-view fl-nanoscroller {{isShowingClass}}">
				<div class="fl-nanoscroller-content"></div>
			</div>
			<# } #>
		</div>
		<div class="fl-builder--search-results-panel"></div>
		<button class="fl-builder-ui-pinned-collapse fl-builder-ui-pinned-left-collapse">
			<i data-toggle="show" data-position="left">
				<svg width="15px" height="15px" viewBox="0 0 15 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<g transform="translate(-150.000000, -812.000000)">
							<path d="M150,813.7625 L156.194332,819.5 L150,825.2375 L151.902834,827 L160,819.5 L151.902834,812 L150,813.7625 Z M162,812 L165,812 L165,827 L162,827 L162,812 Z"></path>
						</g>
					</g>
				</svg>
			</i>
			<i data-toggle="hide" data-position="left">
				<svg width="15px" height="15px" viewBox="0 0 15 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<g transform="translate(-150.000000, -812.000000)">
							<path d="M150,813.7625 L156.194332,819.5 L150,825.2375 L151.902834,827 L160,819.5 L151.902834,812 L150,813.7625 Z M162,812 L165,812 L165,827 L162,827 L162,812 Z"></path>
						</g>
					</g>
				</svg>
			</i>
		</button>
		<button class="fl-builder-ui-pinned-collapse fl-builder-ui-pinned-right-collapse">
			<i data-toggle="hide" data-position="right">
				<svg width="15px" height="15px" viewBox="0 0 15 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<g transform="translate(-150.000000, -812.000000)">
							<path d="M150,813.7625 L156.194332,819.5 L150,825.2375 L151.902834,827 L160,819.5 L151.902834,812 L150,813.7625 Z M162,812 L165,812 L165,827 L162,827 L162,812 Z"></path>
						</g>
					</g>
				</svg>
			</i>
			<i data-toggle="show" data-position="right">
				<svg width="15px" height="15px" viewBox="0 0 15 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<g transform="translate(-150.000000, -812.000000)">
							<path d="M150,813.7625 L156.194332,819.5 L150,825.2375 L151.902834,827 L160,819.5 L151.902834,812 L150,813.7625 Z M162,812 L165,812 L165,827 L162,827 L162,812 Z"></path>
						</g>
					</g>
				</svg>
			</i>
		</button>
		<div class="fl-builder--panel-no-settings">
			<div><?php _e( 'No settings selected.', 'fl-builder' ); ?></div>
		</div>
	</div>
</script>
<!-- #tmpl-fl-content-panel-base -->

<script type="text/html" id="tmpl-fl-content-panel-category-selector">
	<#
	var activeViewName = data.tab.activeView.name,
		views = data.items;
	#>
	<div class="fl-builder--category-select">
		<div class="fl-builder--selector-display">
			<button class="fl-builder--selector-display-label">
				<span class="fl-builder--group-label"><?php _e( 'Group', 'fl-builder' ); ?></span>
				<span class="fl-builder--current-view-name">{{{activeViewName}}}</span>
			</button>
		</div>
		<div class="fl-builder--selector-menu">
			<div class="fl-builder--menu">
				<# for(var i in views) {
					var view = views[i];
					if (view.type === 'separator') {
						#><hr><#
					} else {
					var parent = view.parent ? 'data-parent="' + view.parent + '"' : '';
					var hasChildrenClass = view.hasChildren ? ' fl-has-children' : '';
					var hasChildrenOpenClass = view.hasChildrenOpen ? ' fl-has-children-showing' : '';
					var insetClass = view.isSubItem ? ' fl-inset' : '';
					var display = '';

					if ( view.parent && views[ view.parent ] && views[ view.parent ].hasChildrenOpen ) {
						display = ' style="display:block;"';
					}
					#>
					<button data-view="{{view.handle}}" {{{parent}}} {{{display}}} class="fl-builder--menu-item{{insetClass}}{{hasChildrenClass}}{{hasChildrenOpenClass}}">
						{{{view.name}}}
						<# if ( view.hasChildren ) { #>
						<svg class="fl-symbol">
							<use xlink:href="#fl-down-caret" />
						</svg>
						<# } #>
					</button>
				<# } } #>
			</div>
		</div>
	</div>
</script>
<!-- #tmpl-fl-content-panel-category-selector -->

<script type="text/html" id="tmpl-fl-content-panel-modules-view">
	<#
	if (!_.isUndefined(data.queryResults)) {
		var groupedModules = data.queryResults.library.module,
			groupedTemplates = data.queryResults.library.template;
	}

	if (!_.isUndefined(groupedModules) && groupedModules.hasOwnProperty('categorized')) {

		// Check if there are any ordered sections before looping over everything
		if (!_.isUndefined(data.orderedSectionNames)) {

			for( var i = 0; i < data.orderedSectionNames.length; i++ ) {
				var title = data.orderedSectionNames[i],
					modules = groupedModules.categorized[title],
					slug = title.replace(/\s+/g, '-').toLowerCase();

					if ( _.isUndefined(modules) ) { continue; }
				#>
				<div id="fl-builder-blocks-{{slug}}" class="fl-builder-blocks-section">
					<div class="fl-builder-blocks-section-header">
						<span class="fl-builder-blocks-section-title">{{title}}</span>
					</div>
					<div class="fl-builder-blocks-section-content fl-builder-modules">
						<# for( var k in modules) {
							var module 	= modules[ k ],
								type 	= module.isWidget ? 'widget' : module.slug,
								alias 	= module.isAlias ? ' data-alias="' + module.alias + '"' : '',
								widget 	= module.isWidget ? ' data-widget="' + module.class + '"' : '';
						#>
						<span class="fl-builder-block fl-builder-block-module" data-type="{{type}}"{{{alias}}}{{{widget}}}>
							<span class="fl-builder-block-content">
								<span class="fl-builder-block-icon">{{{module.icon}}}</span>
								<span class="fl-builder-block-title">{{module.name}}</span>
							</span>
						</span>
						<# } #>
					</div>
				</div>
				<#
				delete groupedModules.categorized[title];
			}
		}

		// Sort categorized modules in alphabetical order before render.
		Object.keys(groupedModules.categorized).sort().forEach(function(key) {
			var value = groupedModules.categorized[key];
			delete groupedModules.categorized[key];
			groupedModules.categorized[key] = value;
		});

		// Render any sections that were not already rendered in the ordered set
		for( var title in groupedModules.categorized) {
			var modules = groupedModules.categorized[title],
				slug = title.replace(/\s+/g, '-').toLowerCase();

				modules.sort(function(a, b) {
					if (a.name < b.name)
						return -1;
					if (a.name > b.name)
						return 1;
					return 0;
				});
			#>
			<div id="fl-builder-blocks-{{slug}}" class="fl-builder-blocks-section">
				<div class="fl-builder-blocks-section-header">
					<span class="fl-builder-blocks-section-title">{{title}}</span>
				</div>
				<div class="fl-builder-blocks-section-content fl-builder-modules">
					<# for( var i in modules) {
						var module 	= modules[i],
							type 	= module.isWidget ? 'widget' : module.slug,
							alias 	= module.isAlias ? ' data-alias="' + module.alias + '"' : '',
							widget 	= module.isWidget ? ' data-widget="' + module.class + '"' : '';
					#>
					<span class="fl-builder-block fl-builder-block-module" data-type="{{type}}"{{{alias}}}{{{widget}}}>
						<span class="fl-builder-block-content">
							<span class="fl-builder-block-icon">{{{module.icon}}}</span>
							<span class="fl-builder-block-title">{{module.name}}</span>
						</span>
					</span>
					<# } #>
				</div>
			</div>
			<#
		}
	}

	if (!_.isUndefined(groupedTemplates) && groupedTemplates.hasOwnProperty('categorized')) {

		var uncategorizedKey = FLBuilderStrings.uncategorized;
		if (!_.isUndefined(groupedTemplates.categorized[uncategorizedKey])) {
			var uncategorized = groupedTemplates.categorized[uncategorizedKey];
		}
		for( var title in groupedTemplates.categorized) {
			var templates = groupedTemplates.categorized[title];
			#>
			<div class="fl-builder-blocks-section">
				<# if (title !== '') { #>
				<div class="fl-builder-blocks-section-header">
					<span class="fl-builder-blocks-section-title">{{title}}</span>
				</div>
				<# } #>
				<div class="fl-builder-blocks-section-content fl-builder-module-templates">
					<#
					for( var i in templates) {
						var template = templates[i],
							image = template.image,
							id = _.isNumber( template.postId ) ? template.postId : template.id,
							hasImage = image && !image.endsWith('blank.jpg'),
							hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '' ;
					#>
					<span class="fl-builder-block fl-builder-block-template fl-builder-block-module-template {{hasImageClass}}" data-id="{{id}}" data-type="{{template.type}}">
						<span class="fl-builder-block-content">
							<# if ( hasImage ) { #>
							<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
							<# } #>
							<span class="fl-builder-block-title">{{template.name}}</span>
						</span>
					</span>
					<# } #>
				</div>
			</div>
			<#
		}
	}
	if ( FLBuilderConfig.lite ) {

	#>
	<div id="fl-builder-blocks-pro" class="fl-builder-blocks-section fl-builder-blocks-pro-closed">
		<div class="fl-builder-blocks-section-header">
			<span class="fl-builder-blocks-section-title">Pro</span>
		</div>
		<div class="fl-builder-blocks-section-content fl-builder-modules">
			<#

			var modules = FLBuilderConfig.contentItems.module;
			var moduleSlugs = [];
			var proModules = FLBuilderConfig.contentItems.pro;

			for ( var i in modules ) {
				moduleSlugs.push( modules[i].slug );
			}

			for( var slug in proModules ) {
				var module 	= proModules[ slug ];

				if ( jQuery.inArray( slug, moduleSlugs ) >= 0 ) {
					continue;
				}
			#>
			<span class="fl-builder-block fl-builder-block-module fl-builder-block-disabled" data-type="{{slug}}" onclick="FLBuilder._showProMessage('{{module.name}}')">
				<span class="fl-builder-block-content">
					<span class="fl-builder-block-icon">{{{module.icon}}}</span>
					<span class="fl-builder-block-title">{{module.name}}</span>
					<span class="fl-builder-pro-badge">PRO</span>
				</span>
			</span>
			<# } #>
		</div>
		<div class="fl-builder-blocks-pro-overlay"></div>
	</div>
	<button class="fl-builder-button fl-builder-button-silent fl-builder-blocks-pro-expand" onclick="FLBuilder._toggleProModules()">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="20px" width="20px">
			<path d="M5 6l5 5 5-5 2 1-7 7-7-7z"></path>
		</svg>
	</button>
	<div class="fl-builder--panel-cta">
		<img src="<?php echo FL_BUILDER_URL; ?>img/beaver.png" />
		<a href="https://www.wpbeaverbuilder.com/?utm_medium=bb-lite&amp;utm_source=builder-ui&amp;utm_campaign=modules-panel-cta" target="_blank">
			<?php _e( 'Get more time-saving features, modules, and expert support.', 'fl-builder' ); ?>
		</a>
		<button class="fl-builder-upgrade-button fl-builder-button" onclick="FLBuilder._upgradeClicked()">
			<?php _ex( 'Learn More', 'Link to learn more about premium Beaver Builder', 'fl-builder' ); ?>
		</button>
	</div>
	<# } #>
</script>
<!-- #tmpl-fl-content-panel-modules-view -->

<script type="text/html" id="tmpl-fl-content-panel-col-groups-view">
	<#
	if (_.isUndefined(data.queryResults)) return;
	var colGroups = data.queryResults.library.colGroup.items;
	#>
	<div id="fl-builder-blocks-rows" class="fl-builder-blocks-section">
		<# if (typeof colGroups !== 'undefined') { #>
		<div class="fl-builder-blocks-section-content fl-builder-rows">
			<# for( var i in colGroups) {
				var group = colGroups[i],
					id = group.id,
					name = group.name;
			#>
			<span class="fl-builder-block fl-builder-block-row fl-builder-block-col-group" data-cols="{{id}}" title="{{name}}">
				<span class="fl-builder-block-content">
					<span class="fl-builder-block-visual fl-cols-visual {{id}}">
						<# for ( i = 0; i < group.count; i++ ) { #>
						<span class="fl-cols-visual-col"></span>
						<# } #>
					</span>
					<span class="fl-builder-block-title">{{name}}</span>
				</span>
			</span>
			<# } #>
		</div>
		<# } #>

		<# if (FLBuilderConfig.lite) { #>
		<div class="fl-builder--panel-cta">
			<img src="<?php echo FL_BUILDER_URL; ?>img/beaver.png" />
			<a href="https://www.wpbeaverbuilder.com/?utm_medium=bb-lite&amp;utm_source=builder-ui&amp;utm_campaign=modules-panel-cta" target="_blank">
				<?php _e( 'Get more time-saving features, modules, and expert support.', 'fl-builder' ); ?>
			</a>
			<button class="fl-builder-upgrade-button fl-builder-button" onclick="FLBuilder._upgradeClicked()">
				<?php _ex( 'Learn More', 'Link to learn more about premium Beaver Builder', 'fl-builder' ); ?>
			</button>
		</div>
		<# } #>
	</div>
</script>
<!-- #tmpl-fl-content-panel-col-groups-view -->

<script type="text/html" id="tmpl-fl-content-panel-templates-view">
	<#
	var categories;
	if (!_.isUndefined(data.queryResults)) {
		categories = data.queryResults.library.template.categorized;
	}
	#>
	<div class="fl-builder--template-collection">
		<#
		if (categories !== undefined) {
			// treat as collection
			for( var catHandle in categories) {
				var templates = categories[catHandle];
				var categoryName;
				if (!_.isUndefined(FLBuilderStrings.categoryMeta[catHandle])) {
					categoryName = FLBuilderStrings.categoryMeta[catHandle].name;
				} else {
					categoryName = catHandle;
				}
				#>
				<div class="fl-builder--template-collection-section">
					<# if (catHandle !== 'uncategorized' && catHandle !== FLBuilderStrings.undefined && Object.keys(categories).length > 1) { #>
					<div class="fl-builder--template-collection-section-header">
						<div class="fl-builder--template-collection-section-name">{{categoryName}}</div>
					</div>
					<# } #>
					<div class="fl-builder--template-collection-section-content">
						<#
						for( var i in templates) {
							var template = templates[i];
							var background = template.image;
							var id = _.isNumber( template.postId ) ? template.postId : template.id;
						#>
						<div class="fl-builder--template-collection-item" data-id="{{id}}" data-type="{{template.type}}" data-subtype="{{template.subtype}}" data-premium="{{template.premium}}">
							<div class="fl-builder--template-thumbnail" style="background-image:url({{background}})">
								<# if ( FLBuilderConfig.lite && template.premium ) { #>
								<span class="fl-builder-pro-badge">PRO</span>
								<# } #>
							</div>
							<div class="fl-builder--template-name">{{template.name}}</div>
						</div>
						<# } #>
					</div>
				</div>
				<#
			}
		} else {
			// treat as category
			for( var i in data.templates) {
				var template = data.templates[i];
				var background = template.image;
			#>
			<div class="fl-builder--template-collection-item" data-id="{{template.id}}">
				<div class="fl-builder--template-thumbnail" style="background-image:url({{background}})"></div>
				<div class="fl-builder--template-name">{{template.name}}</div>
			</div>
			<#
			}
		}
		#>
	</div>
	<# if (FLBuilderConfig.lite) { #>
	<div class="fl-builder--panel-cta">
		<img src="<?php echo FL_BUILDER_URL; ?>img/beaver.png" />
		<a href="https://www.wpbeaverbuilder.com/?utm_medium=bb-lite&amp;utm_source=builder-ui&amp;utm_campaign=modules-panel-cta" target="_blank">
			<?php _ex( 'Save and reuse your layouts or kick-start your creativity with dozens of professionally designed templates.', 'Upgrade message that displays in the templates tab in lite installs.', 'fl-builder' ); ?>
		</a>
		<button class="fl-builder-upgrade-button fl-builder-button" onclick="FLBuilder._upgradeClicked()">
			<?php _ex( 'Learn More', 'Link to learn more about premium Beaver Builder', 'fl-builder' ); ?>
		</button>
	</div>
	<# } #>
</script>
<!-- #tmpl-fl-content-panel-templates-view -->

<script type="text/html" id="tmpl-fl-content-panel-row-templates-view">
	<#
	var categories;
	if (!_.isUndefined(data.queryResults)) {
		categories = data.queryResults.library.template.categorized;
	}
	#>
	<div>
		<#
		if (!_.isUndefined(categories)) {
			for( var catHandle in categories) {
				var templates = categories[catHandle];
				var categoryName;
				if (!_.isUndefined(FLBuilderStrings.categoryMeta[catHandle])) {
					categoryName = FLBuilderStrings.categoryMeta[catHandle].name;
				} else {
					categoryName = catHandle;
				}
				#>
				<div class="fl-builder-blocks-section">
					<# if (catHandle !== 'uncategorized' && catHandle !== FLBuilderStrings.undefined && Object.keys(categories).length > 1) { #>
					<div class="fl-builder-blocks-section-header">
						<span class="fl-builder-blocks-section-title">{{categoryName}}</span>
					</div>
					<# } #>
					<div class="fl-builder-blocks-section-content fl-builder-row-templates">
						<#
						for( var i in templates) {
							var template = templates[i],
								image = template.image,
								id = _.isNumber( template.postId ) ? template.postId : template.id,
								hasImage = image && !image.endsWith('blank.jpg'),
								hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '',
								isPremium = FLBuilderConfig.lite && template.premium,
								disabledClass = isPremium ? 'fl-builder-block-disabled' : '';
						#>
						<span onclick="FLBuilder._showProMessage('{{template.name}}')" class="fl-builder-block fl-builder-block-template fl-builder-block-row-template {{hasImageClass}} {{disabledClass}}" data-id="{{id}}" data-type="{{template.type}}">
							<span class="fl-builder-block-content">
								<# if (hasImage) { #>
								<div class="fl-builder-block-thumbnail" style="background-image:url({{image}})"></div>
								<# } #>
								<span class="fl-builder-block-title">{{template.name}}</span>
							</span>
							<# if ( FLBuilderConfig.lite && template.premium ) { #>
							<span class="fl-builder-pro-badge">PRO</span>
							<# } #>
						</span>
						<# } #>
					</div>
				</div>
				<#
			}
		}
		#>
	</div>
</script>
<!-- #tmpl-fl-content-panel-row-templates-view -->

<script type="text/html" id="tmpl-fl-content-panel-module-templates-view">
	<#
	var categories;
	if (!_.isUndefined(data.queryResults)) {
		categories = data.queryResults.library.template.categorized;
	}
	#>
	<div class="fl-builder-module-templates-view">
		<#
		if (!_.isUndefined(categories)) {
			for( var catHandle in categories) {
				var templates = categories[catHandle],
					categoryName;
				if (!_.isUndefined(FLBuilderStrings.categoryMeta[catHandle])) {
					categoryName = FLBuilderStrings.categoryMeta[catHandle].name;
				} else {
					categoryName = catHandle;
				}
				#>
				<div class="fl-builder-blocks-section">
					<# if (catHandle !== 'uncategorized' && catHandle !== FLBuilderStrings.undefined && Object.keys(categories).length > 1) { #>
					<div class="fl-builder-blocks-section-header">
						<span class="fl-builder-blocks-section-title">{{categoryName}}</span>
					</div>
					<# } #>
					<div class="fl-builder-blocks-section-content fl-builder-module-templates">
						<#
						for( var i in templates) {
							var template = templates[i],
								image = template.image,
								id = _.isNumber( template.postId ) ? template.postId : template.id,
								hasImage = image && !image.endsWith('blank.jpg'),
								hasImageClass = hasImage ? 'fl-builder-block-has-thumbnail' : '';
						#>
						<span class="fl-builder-block fl-builder-block-template fl-builder-block-module-template {{hasImageClass}}" data-id="{{id}}" data-type="{{template.type}}">
							<span class="fl-builder-block-content">
								<# if ( hasImage ) { #>
								<img class="fl-builder-block-template-image" src="{{image}}" />
								<# } #>
								<span class="fl-builder-block-title">{{template.name}}</span>
							</span>
						</span>
						<# } #>
					</div>
				</div><#
			}
		}
		#>
	</div>
</script>
<!-- #tmpl-fl-content-panel-module-templates-view -->

<script type="text/html" id="tmpl-fl-content-panel-no-view">
	<div class="fl-builder--panel-message">
		<?php _ex( 'Sorry, no content was found!', 'Message that displays when a panel tab has no view to display', 'fl-builder' ); ?>
	</div>
</script>
<!-- #tmpl-fl-content-panel-no-view -->

<script type="text/html" id="tmpl-fl-content-panel-no-templates-view">
	<div class="fl-builder--panel-message">
		<?php _ex( 'Sorry, no templates were found!', 'Message that displays when there are no templates to display', 'fl-builder' ); ?>
	</div>
</script>
<!-- #tmpl-fl-content-panel-no-templates-view -->

<script type="text/html" id="tmpl-fl-content-lite-templates-upgrade-view">
	<div class="fl-builder--panel-message">
		<p><?php _ex( 'Save and reuse your layouts or kick-start your creativity with dozens of professionally designed templates.', 'Upgrade message that displays in the templates tab in lite installs.', 'fl-builder' ); ?></p>
		<a class="fl-builder-upgrade-button fl-builder-button" href="{{FLBuilderConfig.upgradeUrl}}" target="_blank"><?php _ex( 'Learn More', 'Link to learn more about premium Beaver Builder', 'fl-builder' ); ?> <i class="fas fa-external-link-alt"></i></a>
	</div>
</script>
<!-- #tmpl-fl-content-lite-templates-upgrade-view -->

<script type="text/html" id="tmpl-fl-revision-list-item">
	<div class="fl-revision-list-item" data-revision-id="{{data.id}}">
		<div class="fl-revision-list-item-avatar">
			{{{data.avatar}}}
		</div>
		<div class="fl-revision-list-item-text">
			<div class="fl-revision-list-item-date">
			{{data.date}}
			</div>
			<div class="fl-revision-list-item-author">
			{{data.author}}
			</div>
		</div>
	</div>
</script>
<!-- #tmpl-fl-revision-list-item -->

<script type="text/html" id="tmpl-fl-no-revisions-message">
	<div class="fl-no-revisions-message">
		<div class="fl-no-revisions-message-title">
			<?php _e( 'No Revisions Found', 'fl-builder' ); ?>
		</div>
		<?php if ( defined( 'WP_POST_REVISIONS' ) && ! WP_POST_REVISIONS ) : ?>
			<div class="fl-no-revisions-message-text">
				<?php _e( "Revisions are disabled for this site. Please contact your host if you aren't sure how to enable revisions.", 'fl-builder' ); ?>
			</div>
		<?php else : ?>
			<div class="fl-no-revisions-message-text">
				<?php _e( "You haven't saved any revisions yet. Each time you publish a new revision will be saved here.", 'fl-builder' ); ?>
			</div>
		<?php endif; ?>
	</div>
</script>
<!-- #tmpl-fl-no-revisions-message -->

<script type="text/html" id="tmpl-fl-history-list-item">
	<div class="fl-history-list-item" data-position="{{data.position}}" data-current="{{data.current}}">
		<div class="fl-history-list-item-label">
			{{{data.label}}}
		</div>
		<i class="fas fa-check-circle"></i>
	</div>
</script>
<!-- #tmpl-fl-history-list-item -->

<script type="text/html" id="tmpl-fl-keyboard-shortcuts">
	<div class="fl-builder-ui-keyboard-shortcuts">
		<div class="fl-builder-ui-keyboard-shortcuts-content">
			<# for( var i in data ) {
				var item = data[i];
			#>
			<div class="fl-builder-ui-keyboard-shortcut-item">{{ item.label }} <span class="fl-builder-ui-shortcut-keycode">{{{ item.keyLabel }}}</span></div>
			<# } #>

			<div class="fl-builder-ui-keyboard-shortcust-footer">
				<button class="dismiss-shortcut-ui"><?php _e( 'Close', 'fl-builder' ); ?></button>
			</div>
		</div>
	</div>
</script>
<!-- #tmpl-fl-keyboard-shortcuts -->
