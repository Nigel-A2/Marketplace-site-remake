<?php
/**
 * Listings display template
 *
 * @package BDP/Templates/Listings
 */

wpbdp_the_listing_sort_options();
?>

<div id="wpbdp-listings-list" class="listings wpbdp-listings-list list wpbdp-grid <?php echo esc_attr( apply_filters( 'wpbdp_listings_class', '' ) ); ?>">
	<?php
	wpbdp_x_part(
		'parts/listings-loop',
		array(
			'query' => $query,
		)
	);
	?>
</div>
