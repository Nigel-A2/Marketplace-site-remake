<?php
/**
 * Button texts
 */
$discard     = apply_filters( 'fl_builder_ui_bar_discard', __( 'Discard', 'fl-builder' ) );
$discard_alt = apply_filters( 'fl_builder_ui_bar_discard_alt', __( 'Discard changes and exit', 'fl-builder' ) );
$draft       = apply_filters( 'fl_builder_ui_bar_draft', __( 'Save Draft', 'fl-builder' ) );
$draft_alt   = apply_filters( 'fl_builder_ui_bar_draft_alt', __( 'Keep changes drafted and exit', 'fl-builder' ) );
$review      = apply_filters( 'fl_builder_ui_bar_review', __( 'Submit for Review', 'fl-builder' ) );
$review_alt  = apply_filters( 'fl_builder_ui_bar_review_alt', __( 'Submit changes for review and exit', 'fl-builder' ) );
$publish     = apply_filters( 'fl_builder_ui_bar_publish', __( 'Publish', 'fl-builder' ) );
$publish_alt = apply_filters( 'fl_builder_ui_bar_publish_alt', __( 'Publish changes and exit', 'fl-builder' ) );
$cancel      = apply_filters( 'fl_builder_ui_bar_cancel', __( 'Cancel', 'fl-builder' ) );
?>
<div class="fl-builder-bar">
	<div class="fl-builder-bar-content">
		<?php FLBuilder::render_ui_bar_title(); ?>
		<?php FLBuilder::render_ui_bar_buttons(); ?>
		<div class="fl-clear"></div>
		<div class="fl-builder-publish-actions-click-away-mask"></div>
		<div class="fl-builder-publish-actions is-hidden">
			<span class="fl-builder-button-group">
				<span class="fl-builder-button fl-builder-button-primary" data-action="discard" title="<?php echo esc_attr( $discard_alt ); ?>"><?php echo esc_attr( $discard ); ?></span>
				<span class="fl-builder-button fl-builder-button-primary" data-action="draft" title="<?php echo esc_attr( $draft_alt ); ?>"><?php echo esc_attr( $draft ); ?></span>
				<# if( 'publish' !== FLBuilderConfig.postStatus && ! FLBuilderConfig.userCanPublish ) { #>
				<span class="fl-builder-button fl-builder-button-primary" data-action="publish" title="<?php echo esc_attr( $review_alt ); ?>"><?php echo esc_attr( $review ); ?></span>
				<# } else { #>
				<span class="fl-builder-button fl-builder-button-primary" data-action="publish" title="<?php echo esc_attr( $publish_alt ); ?>"><?php echo esc_attr( $publish ); ?></span>
				<# } #>
			</span>
			<span class="fl-builder-button" data-action="dismiss"><?php echo esc_attr( $cancel ); ?></span>
		</div>
	</div>
</div>
<div class="fl-builder--preview-actions">
	<span class="size"></span>
	<span class="title-accessory device-icons">
		<i class="dashicons dashicons-smartphone" data-mode="responsive"></i>
		<i class="dashicons dashicons-tablet" data-mode="medium"></i>
		<i class="dashicons dashicons-desktop" data-mode="default"></i>
	</span>

	<button class="fl-builder-button fl-builder-button-primary end-preview-btn"><?php _e( 'Continue Editing', 'fl-builder' ); ?></button>
</div>
<div class="fl-builder--revision-actions">
	<select></select>
	<button class="fl-builder-button fl-cancel-revision-preview"><?php _e( 'Cancel', 'fl-builder' ); ?></button>
	<button class="fl-builder-button fl-builder-button-primary fl-apply-revision-preview"><?php _e( 'Apply', 'fl-builder' ); ?></button>
</div>
