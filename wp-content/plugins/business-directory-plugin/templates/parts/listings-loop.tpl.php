<?php
if ( $query->have_posts() ) :
	global $wp_query;
	$wp_query->wpbdp_in_the_loop = true;

	/** @phpstan-ignore-next-line */
	while ( $query->have_posts() ) {
		$query->the_post();
		wpbdp_render_listing( null, 'excerpt', 'echo' );
	}

	/** @phpstan-ignore-next-line */
	$wp_query->wpbdp_in_the_loop = false;

	/** @phpstan-ignore-next-line */
	wpbdp_x_part(
		'parts/pagination',
		array(
			'query' => $query,
		)
	);
else :
	?>
	<span class="no-listings">
		<?php esc_html_e( 'No listings found.', 'business-directory-plugin' ); ?>
	</span>
	<?php
endif;
