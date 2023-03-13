<div class="fl-post-<?php echo $layout; ?>-image">

	<?php do_action( 'fl_builder_post_' . $layout . '_before_image', $settings, $this ); ?>

	<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
		<?php the_post_thumbnail( $settings->image_size ); ?>
	</a>

	<?php do_action( 'fl_builder_post_' . $layout . '_after_image', $settings, $this ); ?>

</div>
