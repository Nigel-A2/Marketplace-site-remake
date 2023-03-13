<?php

if ( ! comments_open() && '0' === get_comments_number() ) {
	return;
}
if ( post_password_required() ) {
	return;
}

?>
<div class="fl-comments">

	<?php do_action( 'fl_comments_open' ); ?>

	<?php if ( have_comments() ) : ?>
	<div class="fl-comments-list">

		<h3 class="fl-comments-list-title">
			<?php

			$num_comments = get_comments_number();

			if ( $num_comments ) {

				printf(
					/* translators: 1: Coments list title */
					esc_html( _nx( '%1$s Comment', '%1$s Comments', get_comments_number(), 'Comments list title.', 'fl-automator' ) ),
					number_format_i18n( $num_comments )
				);

			} else {

				_e( 'No Comments', 'fl-automator' );

			}

			?>
		</h3>

		<ol id="comments">
		<?php
		wp_list_comments( array(
			'callback' => 'FLTheme::display_comment',
		) );
		?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 ) : ?>
		<nav class="fl-comments-list-nav clearfix" role="navigation">
			<div class="fl-comments-list-prev"><?php previous_comments_link(); ?></div>
			<div class="fl-comments-list-next"><?php next_comments_link(); ?></div>
		</nav>
		<?php endif; ?>

	</div>
	<?php endif; ?>
	<?php

	comment_form();

	?>
	<?php do_action( 'fl_comments_close' ); ?>
</div>
