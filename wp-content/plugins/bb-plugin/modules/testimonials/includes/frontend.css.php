<?php

FLBuilderCSS::rule( array(
	'selector' => ".fl-builder-content .fl-node-$id .fl-testimonials .fl-testimonial, .fl-builder-content .fl-node-$id .fl-testimonials .fl-testimonial *",
	'props'    => array(
		'color' => $settings->text_color,
	),
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'text_typography',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-testimonials .fl-testimonial",
) );

?>
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap.compact h3 {
	font-size: <?php echo $settings->heading_size; ?>px;
}
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager a,
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager a.active {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->dot_color ); ?>;
	opacity: 1;
}
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager a {
	opacity: 0.2;
}
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .fas:hover,
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .fas {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->arrow_color ); ?>;
}
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap.fl-testimonials-no-heading {
	padding-top: 25px;
}
