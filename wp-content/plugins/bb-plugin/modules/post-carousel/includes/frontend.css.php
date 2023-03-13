<?php

	$layout         = isset( $settings->layout ) ? $settings->layout : 'grid';
	$posts_per_view = ! empty( $settings->posts_per_view ) ? $settings->posts_per_view : 3;
	$icon_position  = isset( $settings->post_icon_position ) ? $settings->post_icon_position : 'above';
	$parent_row     = FLBuilderModel::get_node_parent_by_type( $id, 'row' );

?>

<?php if ( isset( $settings->equal_height ) && 'yes' == $settings->equal_height && 'grid' == $layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-carousel-wrapper{
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		height: 100%;
	}

	<?php
	if ( ! empty( $parent_row->settings->full_height ) && 'default' != $parent_row->settings->full_height ) {
		if ( 'fixed' == $parent_row->settings->content_width ) {
			echo '.fl-node-' . $parent_row->node . ' .fl-row-content {';
			echo '	 width: 100%;';
			echo '}';
		}
	}
	?>

<?php endif; ?>

.fl-node-<?php echo $id; ?> .fl-post-carousel .fl-post-carousel-post {
	width: <?php echo round( ( 100 / $posts_per_view ), 2 ); ?>%;
}

.fl-node-<?php echo $id; ?> .fl-post-carousel .fl-post-carousel-post:nth-child(-n+<?php echo $posts_per_view; ?>) {
	position: relative;
}

<?php
if ( 'grid' == $layout ) {
	// title
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-title a",
		'enabled'  => ! empty( $settings->title_color ),
		'props'    => array(
			'color' => $settings->title_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-title a:hover",
		'enabled'  => ! empty( $settings->title_hover_color ),
		'props'    => array(
			'color' => $settings->title_hover_color,
		),
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'title_typography',
		'selector'     => ".fl-node-$id .fl-post-carousel-title",
	) );

	// meta
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-meta",
		'enabled'  => ! empty( $settings->meta_color ),
		'props'    => array(
			'color' => $settings->meta_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-meta a",
		'enabled'  => ! empty( $settings->meta_link_color ),
		'props'    => array(
			'color' => $settings->meta_link_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-meta a:hover",
		'enabled'  => ! empty( $settings->meta_link_hover_color ),
		'props'    => array(
			'color' => $settings->meta_link_hover_color,
		),
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'meta_typography',
		'selector'     => ".fl-node-$id .fl-post-carousel-meta",
	) );

	// content
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-content",
		'enabled'  => '1' == $settings->show_content && ! empty( $settings->content_color ),
		'props'    => array(
			'color' => $settings->content_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-content a:not(.fl-post-carousel-more)",
		'enabled'  => '1' == $settings->show_content && ! empty( $settings->content_link_color ),
		'props'    => array(
			'color' => $settings->content_link_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-content a:not(.fl-post-carousel-more):hover",
		'enabled'  => '1' == $settings->show_content && ! empty( $settings->content_link_hover_color ),
		'props'    => array(
			'color' => $settings->content_link_hover_color,
		),
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'content_typography',
		'selector'     => ".fl-node-$id .fl-post-carousel-content :not(.fl-post-carousel-more)",
	) );

	// more link
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-more",
		'enabled'  => '1' == $settings->show_more_link && ! empty( $settings->more_link_color ),
		'props'    => array(
			'color' => $settings->more_link_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-more:hover",
		'enabled'  => '1' == $settings->show_more_link && ! empty( $settings->more_link_hover_color ),
		'props'    => array(
			'color' => $settings->more_link_hover_color,
		),
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'more_link_typography',
		'selector'     => ".fl-node-$id .fl-post-carousel-more",
	) );
} elseif ( 'gallery' == $layout ) {
	// title
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-title",
		'enabled'  => ! empty( $settings->title_color ),
		'props'    => array(
			'color' => $settings->title_color,
		),
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'title_typography',
		'selector'     => ".fl-node-$id .fl-post-carousel-title",
	) );

	// meta
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-post-carousel-meta",
		'enabled'  => ! empty( $settings->meta_color ),
		'props'    => array(
			'color' => $settings->meta_color,
		),
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'meta_typography',
		'selector'     => ".fl-node-$id .fl-post-carousel-meta",
	) );
}
?>

<?php if ( 'grid' == $layout ) : ?>

	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-carousel-post{
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_bg_color ); ?>;
	}

<?php elseif ( 'gallery' == $layout ) : ?>

	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-post-carousel-text-wrap{
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_bg_color ); ?>;
	}

<?php endif; ?>

<?php if ( isset( $settings->navigation ) && 'yes' == $settings->navigation ) : ?>

	<?php if ( 'grid' == $layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-carousel {
		padding: 0 48px;
	}
	<?php endif; ?>

	.fl-node-<?php echo $id; ?> .fl-post-carousel-navigation path{
	<?php if ( isset( $settings->arrows_text_color ) && ! empty( $settings->arrows_text_color ) ) : ?>
		fill: <?php echo FLBuilderColor::hex_or_rgb( $settings->arrows_text_color ); ?>;
	<?php else : ?>
		fill: currentColor;
	<?php endif; ?>
	}

	<?php if ( isset( $settings->arrows_bg_color ) && ! empty( $settings->arrows_bg_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-carousel-svg-container {
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->arrows_bg_color ); ?>;
		width: 40px;
		height: 40px;

		<?php if ( isset( $settings->arrows_bg_style ) && 'circle' == $settings->arrows_bg_style ) : ?>
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
		-ms-border-radius: 50%;
		-o-border-radius: 50%;
		border-radius: 50%;
		<?php endif; ?>
	}
	.fl-node-<?php echo $id; ?> .fl-post-carousel-navigation svg {
		height: 100%;
		width: 100%;
		padding: 5px;
	}
	<?php endif; ?>

<?php endif; ?>

<?php if ( isset( $settings->post_has_icon ) && 'yes' == $settings->post_has_icon ) : ?>

	<?php if ( 'gallery' == $layout ) : ?>

		.fl-node-<?php echo $id; ?> .fl-post-carousel-gallery .fl-carousel-icon{
		<?php if ( 'above' == $icon_position ) : ?>
			margin-bottom: 10px;
		<?php else : ?>
			margin-top: 10px;
		<?php endif; ?>
		}

		<?php if ( ! empty( $settings->post_icon_size ) || ! empty( $settings->post_icon_color ) ) : ?>
			.fl-node-<?php echo $id; ?> .fl-post-carousel-gallery .fl-carousel-icon i,
			.fl-node-<?php echo $id; ?> .fl-post-carousel-gallery .fl-carousel-icon i:before {
			<?php if ( ! empty( $settings->post_icon_size ) ) : ?>
				width: <?php echo $settings->post_icon_size; ?>px;
				height: <?php echo $settings->post_icon_size; ?>px;
				font-size: <?php echo $settings->post_icon_size; ?>px;
			<?php endif; ?>
			<?php if ( ! empty( $settings->post_icon_color ) ) : ?>
				color: <?php echo FLBuilderColor::hex_or_rgb( $settings->post_icon_color ); ?>;
			<?php endif; ?>
			}
		<?php endif; ?>

		<?php if ( $settings->duo_color1 && false !== strpos( $settings->post_icon, 'fad fa' ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-carousel-gallery .fl-carousel-icon i.fad:before {
			color: <?php echo FLBuilderColor::hex_or_rgb( $settings->duo_color1 ); ?>;
		}
		<?php endif; ?>

		<?php if ( $settings->duo_color2 && false !== strpos( $settings->post_icon, 'fad fa' ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-post-carousel-gallery .fl-carousel-icon i.fad::after {
			color: <?php echo FLBuilderColor::hex_or_rgb( $settings->duo_color2 ); ?>;
			opacity: 1;
		}
		<?php endif; ?>

	<?php endif; ?>

<?php endif; ?>

<?php if ( isset( $settings->hover_transition ) && 'fade' != $settings->hover_transition && 'gallery' == $layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-carousel-gallery .fl-post-carousel-text{
	<?php if ( 'slide-up' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-30%,0);
			-moz-transform: translate3d(-50%,-30%,0);
			-ms-transform: translate(-50%,-30%);
				transform: translate3d(-50%,-30%,0);
	<?php elseif ( 'slide-down' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-70%,0);
			-moz-transform: translate3d(-50%,-70%,0);
			-ms-transform: translate(-50%,-70%);
				transform: translate3d(-50%,-70%,0);
	<?php elseif ( 'scale-up' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-50%,0) scale(.7);
			-moz-transform: translate3d(-50%,-50%,0) scale(.7);
			-ms-transform: translate(-50%,-50%) scale(.7);
				transform: translate3d(-50%,-50%,0) scale(.7);
	<?php elseif ( 'scale-down' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-50%,0) scale(1.3);
			-moz-transform: translate3d(-50%,-50%,0) scale(1.3);
			-ms-transform: translate(-50%,-50%) scale(1.3);
				transform: translate3d(-50%,-50%,0) scale(1.3);
	<?php endif; ?>
	}

<?php endif; ?>
