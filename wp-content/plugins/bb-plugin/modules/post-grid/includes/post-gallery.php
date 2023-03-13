<<?php echo $module->get_posts_container(); ?> <?php $module->render_post_class(); ?><?php FLPostGridModule::print_schema( ' itemscope itemtype="' . FLPostGridModule::schema_itemtype() . '"' ); ?>>

	<?php FLPostGridModule::schema_meta(); ?>

	<a class="fl-post-gallery-link" href="<?php the_permalink(); ?>" alt="<?php the_title_attribute(); ?>">

		<?php

		$image_data = wp_get_attachment_metadata( get_post_thumbnail_id() );
		$class_name = 'fl-post-gallery-img';

		if ( $image_data ) {
			if ( $image_data['width'] > $image_data['height'] ) {
				$class_name .= ' fl-post-gallery-img-horiz';
			} else {
				$class_name .= ' fl-post-gallery-img-vert';
			}
		}

		global $post;
		$image = get_the_post_thumbnail( $post, 'large', array(
			'class' => $class_name,
		) );

		/**
		 * Either display the thumbnail if available or show default no-image.
		 */
		if ( '' !== $image ) {
			echo $image;
		} else {
			if ( '' !== $settings->image_fallback_src ) {
				printf( '<img src="%s" class="fl-post-gallery-img fl-post-gallery-img-horiz wp-post-image" />', $settings->image_fallback_src );
			} else {
				echo FLBuilder::default_image_html( 'fl-post-gallery-img fl-post-gallery-img-horiz wp-post-image' );
			}
		}

		?>
		<div class="fl-post-gallery-text-wrap">
			<div class="fl-post-gallery-text">

				<?php if ( 'yes' == $settings->has_icon && 'above' == $settings->icon_position ) : ?>
					<span class="fl-gallery-icon">
						<i class="<?php echo $settings->icon; ?>"></i>
					</span>
				<?php endif; ?>

				<h2 class="fl-post-gallery-title" itemprop="headline"><?php the_title(); ?></h2>

				<?php do_action( 'fl_builder_post_gallery_before_meta', $settings, $module ); ?>

				<?php if ( $settings->show_date ) : ?>
				<span class="fl-post-gallery-date">
					<?php FLBuilderLoop::post_date( $settings->date_format ); ?>
				</span>
				<?php endif; ?>

				<?php do_action( 'fl_builder_post_gallery_after_meta', $settings, $module ); ?>

				<?php if ( 'yes' == $settings->has_icon && 'below' == $settings->icon_position ) : ?>
					<span class="fl-gallery-icon">
						<i class="<?php echo $settings->icon; ?>"></i>
					</span>
				<?php endif; ?>

			</div>
		</div>
	</a>
</<?php echo $module->get_posts_container(); ?>>
