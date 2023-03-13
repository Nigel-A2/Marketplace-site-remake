<?php do_action( 'fl_before_post' ); ?>
<article class="fl-post">

	<header class="fl-post-header">
		<h2 class="fl-post-title"><?php _e( 'Nothing Found', 'fl-automator' ); ?></h2>
	</header><!-- .fl-post-header -->
<?php do_action( 'fl_before_post_content' ); ?>
	<div class="fl-post-content clearfix">
		<?php if ( is_search() ) : ?>

			<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'fl-automator' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php _e( "It seems we can't find what you're looking for. Perhaps searching can help.", 'fl-automator' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .fl-post-content -->
<?php do_action( 'fl_after_post_content' ); ?>
</article>
<?php do_action( 'fl_after_post' ); ?>
<!-- .fl-post -->
