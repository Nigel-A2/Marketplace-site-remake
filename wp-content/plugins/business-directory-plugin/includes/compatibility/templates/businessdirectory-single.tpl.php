<?php get_header(); ?>

<?php the_post(); ?>

<div id="content">
    <?php // Customize the output of this function using the template "businessdirectory-listing.tpl.php"; ?>
    <?php wpbdp_render_listing( null, 'single', true ); ?>
</div>

<?php get_footer(); ?>
