<?php
/**
 * Template parameters:
 *  $query - The WP_Query object for this page. Do not call query_posts() in this template.
 */
$query = isset( $query ) ? $query : wpbdp_current_query();
?>
<div id="wpbdp-view-listings-page" class="wpbdp-view-listings-page wpbdp-page <?php echo esc_attr( join( ' ', $__page__['class'] ) ); ?>">

	<?php if ( ! isset( $stickies ) ) $stickies = null; ?>
	<?php if ( ! isset( $excludebuttons ) ) $excludebuttons = true; ?>

	<?php if ( ! $excludebuttons ) : ?>
        <div class="wpbdp-bar cf">
            <?php wpbdp_the_main_links(); ?>
            <?php wpbdp_the_search_form(); ?>
        </div>
    <?php endif; ?>

    <?php
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $__page__['before_content'];
    ?>

	<div class="wpbdp-page-content <?php echo esc_attr( join( ' ', $__page__['content_class'] ) ); ?>">

        <?php wpbdp_the_listing_sort_options(); ?>

		<?php
		if ( $query->have_posts() ) :
			?>
            <div class="listings wpbdp-listings-list">
				<?php
				/** @phpstan-ignore-next-line */
				while ( $query->have_posts() ) {
					$query->the_post();
					wpbdp_render_listing( null, 'excerpt', 'echo' );
				}

				/** @phpstan-ignore-next-line */
				wpbdp_x_part(
					'parts/pagination',
					array(
						'query' => $query,
					)
				);
				?>
            </div>
        	<?php
		else :
			esc_html_e( 'No listings found.', 'business-directory-plugin' );
		endif;
		?>

    </div>

</div>
