<?php

$show_thumbs = FLTheme::get_setting( 'fl-archive-show-thumbs' );
$show_full   = apply_filters( 'fl_archive_show_full', FLTheme::get_setting( 'fl-archive-show-full' ) );
$more_text   = FLTheme::get_setting( 'fl-archive-readmore-text' );
$thumb_size  = FLTheme::get_setting( 'fl-archive-thumb-size', 'large' );


do_action( 'fl_before_post' ); ?>
<article <?php post_class( 'fl-post' ); ?> id="fl-post-<?php the_ID(); ?>"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/BlogPosting"' ); ?>>

	<?php if ( has_post_thumbnail() && ! empty( $show_thumbs ) ) : ?>
		<?php if ( 'above-title' === $show_thumbs ) : ?>
		<div class="fl-post-thumb">
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
				<?php
				the_post_thumbnail( $thumb_size, array(
					'itemprop' => 'image',
				) );
				?>
			</a>
		</div>
		<?php endif; ?>
	<?php endif; ?>

	<header class="fl-post-header">
		<h2 class="fl-post-title" itemprop="headline">
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
			<?php edit_post_link( _x( 'Edit', 'Edit post link text.', 'fl-automator' ) ); ?>
		</h2>
		<?php FLTheme::post_top_meta(); ?>
	</header><!-- .fl-post-header -->

	<?php if ( has_post_thumbnail() && ! empty( $show_thumbs ) ) : ?>
		<?php if ( 'above' === $show_thumbs ) : ?>
		<div class="fl-post-thumb">
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
				<?php the_post_thumbnail( $thumb_size ); ?>
			</a>
		</div>
		<?php endif; ?>

		<?php if ( 'beside' === $show_thumbs ) : ?>
		<div class="row fl-post-image-<?php echo $show_thumbs; ?>-wrap">
			<div class="fl-post-image-<?php echo $show_thumbs; ?>">
				<div class="fl-post-thumb">
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( $thumb_size, array( 'aria-label' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
					</a>
				</div>
			</div>
			<div class="fl-post-content-<?php echo $show_thumbs; ?>">
		<?php endif; ?>
	<?php endif; ?>
	<?php do_action( 'fl_before_post_content' ); ?>
	<div class="fl-post-content clearfix" itemprop="text">
		<?php

		if ( is_search() || ! $show_full ) {
			the_excerpt();
			echo '<a class="fl-post-more-link" href="' . get_permalink() . '">' . $more_text . '</a>';
		} else {
			the_content( '<span class="fl-post-more-link">' . $more_text . '</span>' );
		}

		?>
	</div><!-- .fl-post-content -->

	<?php FLTheme::post_bottom_meta(); ?>
	<?php do_action( 'fl_after_post_content' ); ?>
	<?php if ( has_post_thumbnail() && 'beside' === $show_thumbs ) : ?>
		</div>
	</div>
	<?php endif; ?>

</article>
<?php do_action( 'fl_after_post' ); ?>
<!-- .fl-post -->
