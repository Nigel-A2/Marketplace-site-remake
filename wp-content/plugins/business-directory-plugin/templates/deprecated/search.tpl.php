<?php
_deprecated_file( esc_html( basename( __FILE__ ) ), 'Unknown' );

$api = wpbdp_formfields_api();
?>
<div id="wpbdp-search-page" class="wpbdp-search-page businessdirectory-search businessdirectory wpbdp-page">
    <div class="wpbdp-bar cf"><?php wpbdp_the_main_links(); ?></div>
    <h2 class="title"><?php esc_html_e( 'Search', 'business-directory-plugin' ); ?></h2>

    <?php if ( 'none' === $search_form_position || 'above' === $search_form_position ) : ?>
        <?php echo $search_form; ?>
    <?php endif; ?>

    <!-- Results -->
    <?php if ( $searching ) : ?>    
        <h3><?php esc_html_e( 'Search Results', 'business-directory-plugin' ); ?></h3>

        <?php do_action( 'wpbdp_before_search_results' ); ?>
        <div class="search-results">
		<?php if ( have_posts() ) : ?>
            <?php wpbdp_render( 'businessdirectory-listings', array( 'echo' => true ) ); ?>
		<?php else : ?>
            <?php esc_html_e( 'No listings found.', 'business-directory-plugin' ); ?>
            <br />
            <?php
            printf(
                '<a href="%s">%s</a>.',
                esc_url( wpbdp_get_page_link( 'main' ) ),
                esc_html__( 'Return to directory', 'business-directory-plugin' )
            );
            ?>
        <?php endif; ?>
        </div>
        <?php do_action( 'wpbdp_after_search_results' ); ?>
    <?php endif; ?>

	<?php if ( 'below' === $search_form_position ) : ?>
        <?php echo $search_form; ?>
    <?php endif; ?>

</div>
