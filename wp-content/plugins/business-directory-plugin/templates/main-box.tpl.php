<?php
/**
 * BD Main Box
 *
 * @package BDP/Templates/Main Box
 */

?>
<div id="wpbdp-main-box" class="wpbdp-main-box" data-breakpoints='{"tiny": [0,360], "small": [360,560], "medium": [560,710], "large": [710,999999]}' data-breakpoints-class-prefix="wpbdp-main-box">

<?php if ( wpbdp_get_option( 'show-search-listings' ) || $in_shortcode ) : ?>
<div class="main-fields box-row cols-2">
    <form action="<?php echo esc_url( $search_url ); ?>" method="get">
        <input type="hidden" name="wpbdp_view" value="search" />
        <?php echo $hidden_fields; ?>
        <?php if ( ! wpbdp_rewrite_on() ) : ?>
        <input type="hidden" name="page_id" value="<?php echo wpbdp_get_page_id(); ?>" />
        <?php endif; ?>
        <div class="box-col search-fields">
            <div class="box-row cols-<?php echo $no_cols; ?>">
                <div class="box-col main-input">
                    <label for="wpbdp-main-box-keyword-field" style="display:none;">Keywords:</label>
					<input type="text" id="wpbdp-main-box-keyword-field" title="Quick search keywords" class="keywords-field" name="kw" placeholder="<?php esc_attr_e( 'Search Listings', 'business-directory-plugin' ); ?>" />
                </div>
                <?php echo $extra_fields; ?>
            </div>
        </div>
        <div class="box-col submit-btn">
            <input type="submit" value="<?php echo esc_attr_x( 'Find Listings', 'main box', 'business-directory-plugin' ); ?>" /><br />
			<a class="advanced-search-link" href="<?php echo esc_url( $search_url ); ?>"><?php echo esc_attr_x( 'Advanced Search', 'main box', 'business-directory-plugin' ); ?></a>
        </div>
    </form>
</div>

<div class="box-row separator"></div>
<?php endif; ?>

<?php $main_links = wpbdp_main_links( $buttons ); ?>
<?php if ( $main_links ) : ?>
<div class="box-row"><?php echo $main_links; ?></div>
<?php endif; ?>

</div>
