<?php

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id .fl-post-grid-post",
) );

// Title Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'title_typography',
	'selector'     => ".fl-node-$id .fl-post-grid-title",
) );

// Info Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'info_typography',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-post-grid-meta, .fl-builder-content .fl-node-$id .fl-post-grid-meta a",
) );

// Content Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'content_typography',
	'selector'     => ".fl-node-$id .fl-post-grid-content, .fl-node-$id .fl-post-grid-content p",
) );

?>

.fl-node-<?php echo $id; ?> .fl-post-grid-post {

	<?php if ( ! empty( $settings->bg_color ) ) : ?>
	background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?>;
	<?php endif; ?>

	<?php if ( ! empty( $settings->post_align ) && 'default' != $settings->post_align ) : ?>
	text-align: <?php echo $settings->post_align; ?>;
	<?php endif; ?>
}

.fl-node-<?php echo $id; ?> .fl-post-grid-text {
	padding: <?php echo empty( $settings->post_padding ) ? 20 : intval( $settings->post_padding ); ?>px;
}

<?php if ( ! empty( $settings->title_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-title a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->title_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->info_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-meta,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-meta span,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-meta span a, 
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-meta-terms,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-meta-terms span,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-meta-terms a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->info_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->info_font_size ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-post-grid-meta,
.fl-node-<?php echo $id; ?> .fl-post-grid-meta a {
	font-size: <?php echo $settings->info_font_size . $settings->info_font_size_unit; ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->content_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-content,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-content p,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-post .fl-post-grid-content .fl-post-grid-more,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-builder-pagination ul.page-numbers li span,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-builder-pagination ul.page-numbers li a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->content_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->content_font_size ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-content,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-content p {
	font-size: <?php echo $settings->content_font_size . $settings->content_font_size_unit; ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->link_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-content a,
.fl-builder-content .fl-node-<?php echo $id; ?> .page-numbers {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->link_hover_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-grid-content a:hover,
.fl-builder-content .fl-node-<?php echo $id; ?> .page-numbers:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_hover_color ); ?>;
}
<?php endif; ?>

<?php if ( $settings->show_image && ! empty( $settings->grid_image_spacing ) ) : ?>
	<?php if ( 'above' == $settings->grid_image_position ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-grid-image {
		padding: 0 <?php echo $settings->grid_image_spacing; ?>px;
	}
	<?php elseif ( 'above-title' == $settings->grid_image_position ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-grid-image {
		padding: <?php echo $settings->grid_image_spacing; ?>px <?php echo $settings->grid_image_spacing; ?>px 0 <?php echo $settings->grid_image_spacing; ?>px;
	}
	<?php endif; ?>
<?php endif; ?>
