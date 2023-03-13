<?php
// Save the current post, so that it can be restored later (see the end of this file).
global $post;
$initial_current_post = $post;

// Get the query data.
$query = FLBuilderLoop::query( $settings );

$themer_archive_404 = false;
if ( FLBuilderModel::is_builder_active() && class_exists( 'FLThemeBuilder' ) && 1 === $query->found_posts ) {
	$current_url = get_permalink( get_queried_object_id() );

	$themer_archive_404 = ( 'fl-theme-layout' === $query->posts[0]->post_type && stripos( $current_url, 'fl-theme-layout' ) > 0 );

	if ( $themer_archive_404 ) {
		$module->render_404();
	}
}

// Render the posts.
if ( ! $themer_archive_404 && $query->have_posts() ) :

	do_action( 'fl_builder_posts_module_before_posts', $settings, $query );

	$data_source = isset( $settings->data_source ) ? $settings->data_source : 'custom_query';
	$post_type   = isset( $settings->post_type ) ? $settings->post_type : 'post';
	$paged       = ( FLBuilderLoop::get_paged() > 0 ) ? ' fl-paged-scroll-to' : '';
	?>
	<div class="fl-post-<?php echo $module->get_layout_slug() . $paged; ?>"<?php echo FLPostGridModule::print_schema( ' itemscope="itemscope" itemtype="' . FLPostGridModule::schema_collection_type( $data_source, $post_type ) . '"' ); ?>>
	<?php

	if ( 'li' == $module->get_posts_container() ) :
		if ( '' != $module->settings->posts_container_ul_class ) {
			echo '<ul class="' . $module->settings->posts_container_ul_class . '">';
		} else {
			echo '<ul>';
		}
	endif;


	while ( $query->have_posts() ) {

		$query->the_post();

		ob_start();

		include apply_filters( 'fl_builder_posts_module_layout_path', $module->dir . 'includes/post-' . $module->get_layout_slug() . '.php', $settings->layout, $settings );

		// Do shortcodes here so they are parsed in context of the current post.
		echo do_shortcode( ob_get_clean() );
	}

	if ( 'li' == $module->get_posts_container() ) :
		echo '</ul>';
	endif;

	?>
	<?php if ( 'grid' == $settings->layout ) : ?>
	<div class="fl-post-grid-sizer"></div>
	<?php endif; ?>
</div>
<div class="fl-clear"></div>
<?php endif; ?>
<?php

do_action( 'fl_builder_posts_module_after_posts', $settings, $query );

// Render the pagination.
if ( 'none' != $settings->pagination && $query->have_posts() && $query->max_num_pages > 1 ) :

	?>
	<div class="fl-builder-pagination"<?php echo ( in_array( $settings->pagination, array( 'scroll', 'load_more' ) ) ) ? ' style="display:none;"' : ''; ?>>
	<?php FLBuilderLoop::pagination( $query ); ?>
	</div>
	<?php if ( 'load_more' == $settings->pagination && $query->max_num_pages > 1 ) : ?>
		<div class="fl-builder-pagination-load-more">
			<?php

			FLBuilder::render_module_html( 'button', $module->get_button_settings() );

			?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php

do_action( 'fl_builder_posts_module_after_pagination', $settings, $query );

// Render the empty message.
if ( ! $query->have_posts() ) {
	$module->render_404();
}

wp_reset_postdata();

// Restore the original current post.
//
// Note that wp_reset_postdata() isn't enough because it resets the current post by using the main
// query, but it doesn't take into account the possibility that it might have been overridden by a
// third-party plugin in the meantime.
//
// Specifically, this used to cause problems with Toolset Views, when its Content Templates were used.
$post = $initial_current_post;
setup_postdata( $initial_current_post );
