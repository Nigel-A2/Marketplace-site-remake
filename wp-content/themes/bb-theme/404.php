<?php get_header(); ?>

<div class="container">
	<div class="row">
		<div class="fl-content col-md-12">
			<?php do_action( 'fl_before_post' ); ?>
			<article class="fl-post fl-404">
				<header class="fl-post-header">
					<h2 class="fl-post-title"><?php _e( "Sorry! That page doesn't seem to exist.", 'fl-automator' ); ?></h2>
				</header><!-- .fl-post-header -->
				<?php do_action( 'fl_before_post_content' ); ?>
				<div class="fl-post-content clearfix">
					<?php get_search_form(); ?>
				</div><!-- .fl-post-content -->
				<?php do_action( 'fl_after_post_content' ); ?>
			</article>
			<?php do_action( 'fl_after_post' ); ?>
			<!-- .fl-post -->
		</div>
	</div>
</div>

<?php get_footer(); ?>
