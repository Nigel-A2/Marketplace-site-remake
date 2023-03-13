<?php
$plan = $listing->get_fee_plan()->fee;
$categories = wp_get_post_terms( $listing->get_id(), WPBDP_CATEGORY_TAX, array( 'fields' => 'ids' ) );
?>

<?php if ( $categories ) : ?>
    <ul class="category-list">
    <?php foreach ( $categories as $cat_id ) : ?>
        <?php $category = get_term( $cat_id, WPBDP_CATEGORY_TAX ); ?>
        <li><?php echo $category->name; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="wpbdp-plan-selection-wrapper" data-breakpoints='{"tiny": [0,410], "small": [410,560], "medium": [560,710], "large": [710,999999]}' data-breakpoints-class-prefix="wpbdp-size">
    <div class="wpbdp-plan-selection">
        <div class="wpbdp-plan-selection-list">
            <?php echo wpbdp_render( 'plan-selection-plan', array( 'plan' => $plan, 'categories' => $categories, 'display_only' => true, 'extra' ) ); ?>
        </div>
    </div>
</div>

<div id="change-plan-link" class="wpbdp-clearfix">
    <span class="dashicons dashicons-update"></span>
    <a href="#"><?php _ex( 'Change category/plan', 'listing submit', 'business-directory-plugin' ); ?></a>
</div>

<script>
jQuery(function($) {
    var amount = <?php echo $plan->calculate_amount( $categories ); ?>;

    if ( wpbdpSubmitListingL10n.isAdmin || amount == 0.0 ) {
        $( '#wpbdp-submit-listing-submit-btn' ).val( wpbdpSubmitListingL10n.completeListingTxt );
    } else {
        $( '#wpbdp-submit-listing-submit-btn' ).val( wpbdpSubmitListingL10n.continueToPaymentTxt );
    }
});
</script>
