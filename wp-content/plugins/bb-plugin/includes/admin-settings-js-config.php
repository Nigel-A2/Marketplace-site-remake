<script type="text/javascript">

FLBuilderAdminSettingsConfig = {
	roles: <?php echo json_encode( FLBuilderUserAccess::get_all_roles() ); ?>,
	userAccess: <?php echo json_encode( FLBuilderUserAccess::get_saved_settings() ); ?>
};

FLBuilderAdminSettingsStrings = {
	deselectAll: '<?php esc_attr_e( 'Deselect All', 'fl-builder' ); ?>',
	noneSelected: '<?php esc_attr_e( 'None Selected', 'fl-builder' ); ?>',
	select: '<?php esc_attr_e( 'Select...', 'fl-builder' ); ?>',
	selected: '<?php esc_attr_e( 'Selected', 'fl-builder' ); ?>',
	selectAll: '<?php esc_attr_e( 'Select All', 'fl-builder' ); ?>',
	selectFile: '<?php esc_attr_e( 'Select File', 'fl-builder' ); ?>',
	uninstall: '<?php esc_attr_e( 'Please type "uninstall" in the box below to confirm that you really want to uninstall the page builder and all of its data.', 'fl-builder' ); ?>'
};

</script>
