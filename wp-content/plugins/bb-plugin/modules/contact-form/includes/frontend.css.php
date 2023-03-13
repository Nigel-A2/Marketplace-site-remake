<?php FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() ); ?>

<?php if ( 'right' == $settings->btn_align ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-send-error,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-none,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-msg {
	float: right;
}
<?php endif; ?>

<?php if ( 'center' == $settings->btn_align ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-send-error,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-none,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-msg {
	display: block;
	text-align: center;
}
<?php endif; ?>
