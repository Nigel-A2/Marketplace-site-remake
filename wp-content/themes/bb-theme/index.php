<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}
?>
<?php get_header(); ?>

<div class="fl-archive <?php FLLayout::container_class(); ?>">
	<div class="<?php FLLayout::row_class(); ?>">

		<?php FLTheme::sidebar( 'left' ); ?>

		<div class="fl-content <?php FLLayout::content_class(); ?>"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/Blog"' ); ?>>

			<?php FLTheme::archive_page_header(); ?>

			<?php if ( have_posts() ) : ?>

				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php get_template_part( 'content', get_post_format() ); ?>
				<?php endwhile; ?>

				<?php FLTheme::archive_nav(); ?>

			<?php else : ?>

				<?php get_template_part( 'content', 'no-results' ); ?>

			<?php endif; ?>

		</div>

		<?php FLTheme::sidebar( 'right' ); ?>

	</div>
</div>

<?php get_footer(); ?>
