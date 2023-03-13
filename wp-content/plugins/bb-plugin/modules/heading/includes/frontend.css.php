<?php if ( ! empty( $settings->color ) ) : ?>
.fl-row .fl-col .fl-node-<?php echo $id; ?> <?php echo $settings->tag; ?>.fl-heading a,
.fl-row .fl-col .fl-node-<?php echo $id; ?> <?php echo $settings->tag; ?>.fl-heading .fl-heading-text,
.fl-row .fl-col .fl-node-<?php echo $id; ?> <?php echo $settings->tag; ?>.fl-heading .fl-heading-text *,
.fl-node-<?php echo $id; ?> <?php echo $settings->tag; ?>.fl-heading .fl-heading-text {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->color ); ?>;
}
<?php endif; ?>
<?php

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'typography',
	'selector'     => ".fl-node-$id.fl-module-heading .fl-heading",
) );
