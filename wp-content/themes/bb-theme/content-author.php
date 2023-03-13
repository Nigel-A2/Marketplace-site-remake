<div class="fl-author-box">
	<div class="fl-author-avatar">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'author_box_avatar_size', 68 ) ); ?>
	</div> <!-- /.author-avatar -->
	<div class="fl-author-description">
		<?php /* translators: %s: Author name */ ?>
		<h4><?php printf( esc_html__( 'About %s', 'fl-automator' ), get_the_author() ); ?></h4>
		<p><?php the_author_meta( 'description' ); ?></p>
		<div class="fl-author-link">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php
				/* translators: %s: Author name */
				printf( wp_kses( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'fl-automator' ), array(
					'span' => array(
						'class' => array(),
					),
				) ), get_the_author() );
				?>
			</a>
		</div> <!-- /.author-link	-->
	</div> <!-- /.author-description -->
</div> <!-- /.author-info -->
