<?php
/**
 * Related Products section modification
 *
 * This will make the Related Products section only display products from the current product's vendor
 *
 * The more products tab was removed by code in this child-theme's functions.php file.
 * 
 * This override uses code taken from plugins/dokan-lite/includes/wc-functions.php line 1100 function dokan_get_more_products_from_seller
 * 
 * Special thanks to this documentation site: https://hooks.wbcomdesigns.com/reference
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( $related_products ) : ?>

	<section class="related products">

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
		<?php woocommerce_product_loop_start(); ?>

			<?php
			$seller_id = 0;
			$posts_per_page = 6;
			global $product, $post;
			if ( $seller_id === 0 || 'more_seller_product' === $seller_id ) {
				$seller_id = $post->post_author;
			}
		
			if ( ! is_int( $posts_per_page ) ) {
				$posts_per_page = apply_filters( 'dokan_get_more_products_per_page', 6 );
			}
		
			$args = [
				'post_type'      => 'product',
				'posts_per_page' => $posts_per_page,
				'orderby'        => 'rand',
				'post__not_in'   => [ $post->ID ],
				'author'         => $seller_id,
			];
		
			$products = new WP_Query( $args );
		
			if ( $products->have_posts() ) {
				woocommerce_product_loop_start();
		
				while ( $products->have_posts() ) {
					$products->the_post();
					wc_get_template_part( 'content', 'product' );
				}
		
				woocommerce_product_loop_end();
			} else {
				esc_html_e( 'No product has been found!', 'dokan-lite' );
			}
		
			wp_reset_postdata();
			?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
	<?php
endif;

wp_reset_postdata();

/*
// old code that was replaced. location: plugins/woocommerce/templates/single-product/related.php
	<?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

			<?php endforeach; ?>
*/
