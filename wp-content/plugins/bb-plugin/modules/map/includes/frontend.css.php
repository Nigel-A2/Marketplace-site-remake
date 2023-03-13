<?php

// Height
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'height',
	'selector'     => ".fl-node-$id .fl-map iframe",
	'prop'         => 'height',
) );

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id .fl-map iframe",
) );
