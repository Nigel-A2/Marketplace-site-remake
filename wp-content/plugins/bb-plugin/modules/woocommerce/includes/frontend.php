<?php

// Opening Wrapper
echo '<div class="fl-woocommerce-' . $settings->layout . '">';

// Shortcodes
$pages = array(
	'cart'     => '[woocommerce_cart]',
	'checkout' => '[woocommerce_checkout]',
	'tracking' => '[woocommerce_order_tracking]',
	'account'  => '[woocommerce_my_account]',
);

if ( 'categories' === $module->settings->layout && 'true' === $module->settings->autoparent ) {
	$term_id                         = get_queried_object_id();
	$module->settings->parent_cat_id = $term_id;
}

// WooCommerce Pages
if ( isset( $pages[ $settings->layout ] ) ) {
	echo $pages[ $settings->layout ];
} elseif ( 'product' == $settings->layout ) {
	add_filter( 'post_class', array( $module, 'single_product_post_class' ) );
	echo '[product id="' . $settings->product_id . '" columns="1"]';
	remove_filter( 'post_class', array( $module, 'single_product_post_class' ) );
} elseif ( 'product_page' == $settings->layout ) { // Single Product Page
	add_filter( 'post_class', array( $module, 'single_product_post_class' ) );
	echo '[product_page id="' . $settings->product_id . '"]';
	remove_filter( 'post_class', array( $module, 'single_product_post_class' ) );
} elseif ( 'add-cart' == $settings->layout ) { // Add to Cart Button
	echo '[add_to_cart id="' . $settings->product_id . '" style=""]';
} elseif ( 'categories' == $settings->layout ) { // Categories
	$cat_ids = '';
	if ( ! empty( $settings->product_category_ids ) ) {
		$cat_ids = 'ids = "' . $settings->product_category_ids . '"';
	}
	echo '[product_categories ' . $cat_ids . ' parent="' . $settings->parent_cat_id . '" columns="' . $settings->cat_columns . '" orderby="' . $settings->category_orderby . '" order="' . $settings->category_order . '"]';
} elseif ( 'products' == $settings->layout ) { // Multiple Products
	add_filter( 'post_class', array( $module, 'products_post_class' ) );

	// Product IDs
	if ( 'ids' == $settings->products_source ) {
		echo '[products ids="' . $settings->product_ids . '" columns="' . $settings->columns . '" orderby="' . $settings->orderby . '" order="' . $settings->order . '"]';
	} elseif ( 'category' == $settings->products_source ) {
		echo '[product_category category="' . $settings->category_slug . '" per_page="' . $settings->num_products . '" columns="' . $settings->columns . '" orderby="' . $settings->orderby . '" order="' . $settings->order . '"]';
	} elseif ( 'tags' == $settings->products_source ) {
		echo '[products tag="' . $settings->tags_slug . '" per_page="' . $settings->num_products . '" columns="' . $settings->columns . '" orderby="' . $settings->orderby . '" order="' . $settings->order . '"]';
	} elseif ( 'recent' == $settings->products_source ) { // Recent Products
		echo '[recent_products per_page="' . $settings->num_products . '" columns="' . $settings->columns . '" orderby="' . $settings->orderby . '" order="' . $settings->order . '"]';
	} elseif ( 'featured' == $settings->products_source ) { // Featured Products
		echo '[featured_products per_page="' . $settings->num_products . '" columns="' . $settings->columns . '" orderby="' . $settings->orderby . '" order="' . $settings->order . '"]';
	} elseif ( 'sale' == $settings->products_source ) { // Sale Products
		echo '[sale_products per_page="' . $settings->num_products . '" columns="' . $settings->columns . '" orderby="' . $settings->orderby . '" order="' . $settings->order . '"]';
	} elseif ( 'best-selling' == $settings->products_source ) { // Best Selling Products
		echo '[best_selling_products per_page="' . $settings->num_products . '" columns="' . $settings->columns . '"]';
	} elseif ( 'top-rated' == $settings->products_source ) { // Top Rated Products
		echo '[top_rated_products per_page="' . $settings->num_products . '" columns="' . $settings->columns . '" orderby="' . $settings->orderby . '" order="' . $settings->order . '"]';
	}

	remove_filter( 'post_class', array( $module, 'products_post_class' ) );
}

// Closing Wrapper
echo '</div>';
