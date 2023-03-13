<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">

		<div class="comment-meta">
			<span class="comment-avatar">
				<?php echo get_avatar( $comment, 80 ); ?>
			</span>
			<?php
				/* translators: 1: comment author name, 2: date, 3: time */
				printf( __( '<span class="comment-author-link">%1$s</span> <span class="comment-date">on %2$s at %3$s</span>', 'fl-automator' ), get_comment_author_link(), get_comment_date(), get_comment_time() );
			?>

		</div><!-- .comment-meta -->

		<div class="comment-content clearfix">
			<?php if ( '0' === $comment->comment_approved ) : ?>
				<p class="comment-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'fl-automator' ); ?></p>
			<?php endif; ?>
			<?php comment_text(); ?>
			<?php edit_comment_link( esc_html_x( '(Edit)', 'Comment edit link text.', 'fl-automator' ), ' ' ); ?>
		</div><!-- .comment-content -->

		<?php

		$comment_reply_link = get_comment_reply_link(array_merge($args, array(
			'reply_text' => esc_attr__( 'Reply', 'fl-automator' ),
			'depth'      => (int) $depth,
			'max_depth'  => (int) $args['max_depth'],
		)));

		if ( $comment_reply_link ) {
			echo '<div class="comment-reply-link">' . $comment_reply_link . '</div>';
		} else {
			echo '<br /><br />';
		}

		?>

	</div><!-- .comment-body -->
