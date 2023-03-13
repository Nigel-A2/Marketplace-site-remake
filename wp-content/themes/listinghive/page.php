<?php
the_post();

get_header();
?>
<div class="page__text">
	<?php the_content( null, true ); ?>
</div>
<?php
wp_link_pages(
	[
		'before'      => '<nav class="pagination"><div class="nav-links">',
		'after'       => '</div></nav>',
		'link_before' => '<span class="page-numbers">',
		'link_after'  => '</span>',
	]
);

comments_template();

get_footer();
