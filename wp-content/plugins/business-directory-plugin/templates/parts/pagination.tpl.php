<div class="wpbdp-pagination">
	<?php
	if ( function_exists( 'wp_pagenavi' ) ) :
		wp_pagenavi( array( 'query' => $query ) );
	else :
		?>
		<span class="prev"><?php previous_posts_link( __( '&larr; Previous ', 'business-directory-plugin' ) ); ?></span>
		<span class="next"><?php next_posts_link( __( 'Next &rarr;', 'business-directory-plugin' ), $query->max_num_pages ); ?></span>
	<?php endif; ?>
</div>
