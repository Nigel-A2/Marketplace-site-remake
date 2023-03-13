<div class="fl-search-results"<?php FLBuilder::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/Blog"' ); ?>>
	<?php
	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :

			$query->the_post();
			?>

			<div class="fl-search-post-item">
				<?php if ( $settings->show_image ) : ?>
				<div class="fl-search-post-image">
					<?php $module->render_featured_image( get_the_id() ); ?>
				</div>
				<?php endif; ?>

				<div class="fl-search-post-text">

					<div class="fl-search-post-title" itemprop="headline">
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
					</div>

					<?php if ( $settings->show_content ) : ?>
					<div class="fl-search-post-content">
						<?php FLBuilderLoop::the_excerpt(); ?>
					</div>
					<?php endif; ?>

				</div>

			</div>

			<?php

		endwhile;

	else :
		?>
		<div class="fl-search-no-posts">
			<p><?php echo $settings->no_results_message; ?></p>
		</div>
		<?php

	endif;

	?>
</div>
<div class="fl-clear"></div>

<?php wp_reset_postdata(); ?>
