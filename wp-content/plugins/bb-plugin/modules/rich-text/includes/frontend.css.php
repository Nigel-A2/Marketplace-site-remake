<?php

if ( ! empty( $settings->color ) ) : ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-module-content .fl-rich-text,
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-module-content .fl-rich-text * {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->color ); ?>;
	}
	<?php
endif;

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'typography',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-rich-text, .fl-builder-content .fl-node-$id .fl-rich-text *:not(b, strong)",
) );
