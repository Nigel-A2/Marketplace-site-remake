<div class="fl-builder-admin">
	<?php if ( FLBuilderUserAccess::current_user_can( 'builder_access' ) ) : ?>
	<div class="fl-builder-admin-tabs">
		<a href="javascript:void(0);" onclick="return false;" class="fl-enable-editor<?php echo ( ! $enabled ) ? ' fl-active' : ''; ?>"><?php _e( 'Text Editor', 'fl-builder' ); ?></a>
		<a href="javascript:void(0);" onclick="return false;" class="fl-enable-builder<?php echo ( $enabled ) ? ' fl-active' : ''; ?>"><?php echo FLBuilderModel::get_branding(); ?></a>
	</div>
	<?php endif; ?>
	<div class="fl-builder-admin-ui">
		<?php /* translators: 1: branded builder name: 2: post type name */ ?>
		<h3><?php printf( _x( '%1$s is currently active for this %2$s.', 'The first %s stands for custom branded "Page Builder" name. The second %s stands for the post type name.', 'fl-builder' ), FLBuilderModel::get_branding(), $post_type_name ); ?></h3>
		<?php if ( FLBuilderUserAccess::current_user_can( 'builder_access' ) ) : ?>
			<?php /* translators: %s: branded builder name */ ?>
			<a href="<?php echo FLBuilderModel::get_edit_url(); ?>" class="fl-launch-builder button button-primary button-large"><?php printf( _x( 'Launch %s', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding() ); ?></a>
		<?php else : ?>
			<?php /* translators: %s: post type name */ ?>
			<a href="<?php echo get_permalink(); ?>" class="button button-large"><?php printf( _x( 'View %s', '%s stands the post type name.', 'fl-builder' ), $post_type_name ); ?></a>
		<?php endif; ?>
	</div>
	<div class="fl-builder-loading"></div>
</div>
<script type="text/javascript">

FLBuilderAdminPostsStrings = {
	<?php /* translators: 1: branded builder name */ ?>
	switchToEditor: "<?php printf( esc_attr_x( 'Switching to Text Editor mode will disable your %1$s layout until it is enabled again. Any edits made while in Text Editor mode will not be made on your %1$s layout. Do you want to continue?', '%s stands for custom branded \"Page Builder\" name.', 'fl-builder' ), FLBuilderModel::get_branding() ); ?>"
};

</script>
