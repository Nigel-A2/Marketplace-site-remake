<?php

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id .fl-post-feed-post",
) );

// Title Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'title_typography',
	'selector'     => ".fl-node-$id .fl-post-feed-title",
) );

// Info Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'info_typography',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-post-feed-meta, .fl-builder-content .fl-node-$id .fl-post-feed-meta a",
) );

// Content Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'content_typography',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-post-feed .fl-post-feed-content, .fl-builder-content .fl-node-$id .fl-post-feed .fl-post-feed-content p",
) );

?>


.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-feed-post {
	<?php if ( ! empty( $settings->bg_color ) ) : ?>
	background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?>;
	<?php endif; ?>

	<?php if ( ! empty( $settings->post_align ) && 'default' != $settings->post_align ) : ?>
	text-align: <?php echo $settings->post_align; ?>;
	<?php endif; ?>
}

<?php if ( ! empty( $settings->feed_post_padding ) ) : ?>
	<?php if ( 'above' == $settings->image_position || 'above-title' == $settings->image_position ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-feed-text {
		padding: <?php echo $settings->feed_post_padding; ?>px;
	}
	.fl-node-<?php echo $id; ?> .fl-post-feed-image,
	.fl-node-<?php echo $id; ?> .fl-post-feed-image-above .fl-post-feed-header {
		margin-bottom: 0;
	}
	.fl-node-<?php echo $id; ?> .fl-post-feed-post {
		padding-bottom: 0;
	}
	<?php else : ?>
	.fl-node-<?php echo $id; ?> .fl-post-feed-post {
		padding: <?php echo $settings->feed_post_padding; ?>px;
	}
	<?php endif; ?>
<?php endif; ?>

<?php if ( $settings->show_image ) : ?>
	<?php if ( ! empty( $settings->image_spacing ) ) : ?>
		<?php if ( 'above' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image {
			padding: 0 <?php echo $settings->image_spacing; ?>px;
		}
		<?php elseif ( 'above-title' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image {
			padding: <?php echo $settings->image_spacing; ?>px <?php echo $settings->image_spacing; ?>px 0 <?php echo $settings->image_spacing; ?>px;
		}
		<?php elseif ( 'beside' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside .fl-post-feed-text {
			padding-left: <?php echo $settings->image_spacing; ?>px;
		}
		<?php elseif ( 'beside-content' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside-content .fl-post-feed-text {
			padding-left: <?php echo $settings->image_spacing; ?>px;
		}
		<?php elseif ( 'beside-right' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside-right .fl-post-feed-text {
			padding-right: <?php echo $settings->image_spacing; ?>px;
		}
		<?php elseif ( 'beside-content-right' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside-content-right .fl-post-feed-text {
			padding-right: <?php echo $settings->image_spacing; ?>px;
		}
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( ! empty( $settings->image_width ) && in_array( $settings->image_position, array( 'beside', 'beside-right', 'beside-content', 'beside-content-right' ) ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image {
			width: <?php echo $settings->image_width; ?>%;
		}
		<?php if ( 'beside' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside .fl-post-feed-text {
			margin-left: <?php echo empty( $settings->image_spacing ) ? $settings->image_width + 4 : $settings->image_width; ?>%;
		}
		<?php elseif ( 'beside-content' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside-content .fl-post-feed-text {
			margin-left: <?php echo empty( $settings->image_spacing ) ? $settings->image_width + 4 : $settings->image_width; ?>%;
		}
		<?php elseif ( 'beside-right' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside-right .fl-post-feed-text {
			margin-right: <?php echo empty( $settings->image_spacing ) ? $settings->image_width + 4 : $settings->image_width; ?>%;
		}
		<?php elseif ( 'beside-content-right' == $settings->image_position ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-feed-image-beside-content-right .fl-post-feed-text {
			margin-right: <?php echo empty( $settings->image_spacing ) ? $settings->image_width + 4 : $settings->image_width; ?>%;
		}
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<?php if ( ! empty( $settings->title_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-feed-title a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->title_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->info_color ) ) : ?>
.fl-builder-content .fl-module-post-grid.fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-header .fl-post-feed-meta,
.fl-builder-content .fl-module-post-grid.fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-header .fl-post-feed-meta span,
.fl-builder-content .fl-module-post-grid.fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-header .fl-post-feed-meta span a,
.fl-builder-content .fl-module-post-grid.fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-header .fl-post-feed-meta-terms,
.fl-builder-content .fl-module-post-grid.fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-header .fl-post-feed-meta-terms span,
.fl-builder-content .fl-module-post-grid.fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-header .fl-post-feed-meta-terms a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->info_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->content_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-content,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-content p,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-content .fl-post-feed-more,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-builder-pagination ul.page-numbers li span, 
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-builder-pagination ul.page-numbers li a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->content_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->link_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-feed .fl-post-feed-content a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->link_hover_color ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-post-feed-content a:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_hover_color ); ?>;
}
<?php endif; ?>
