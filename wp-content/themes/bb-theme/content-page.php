<?php do_action( 'fl_before_post' ); ?>
<article <?php post_class( 'fl-post' ); ?> id="fl-post-<?php the_ID(); ?>"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/CreativeWork"' ); ?>>

	<?php if ( FLTheme::show_post_header() ) : ?>
	<header class="fl-post-header">
		<h1 class="fl-post-title" itemprop="headline"><?php the_title(); ?></h1>
		<?php edit_post_link( _x( 'Edit', 'Edit page link text.', 'fl-automator' ) ); ?>
	</header><!-- .fl-post-header -->
	<?php endif; ?>
	<?php do_action( 'fl_before_post_content' ); ?>
	<div class="fl-post-content clearfix" itemprop="text">
		<?php
			the_content();

			wp_link_pages( array(
				'before'         => '<div class="fl-post-page-nav">' . _x( 'Pages:', 'Text before page links on paginated post.', 'fl-automator' ),
				'after'          => '</div>',
				'next_or_number' => 'number',
			) );
			?>
	</div><!-- .fl-post-content -->
	<?php do_action( 'fl_after_post_content' ); ?>

</article>

<?php comments_template(); ?>
<?php do_action( 'fl_after_post' ); ?>
<!-- .fl-post -->
