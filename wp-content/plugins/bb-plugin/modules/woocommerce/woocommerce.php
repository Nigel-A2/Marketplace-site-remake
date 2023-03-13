<?php

/**
 * @class FLWooCommerceModule
 */
class FLWooCommerceModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		$enabled = class_exists( 'Woocommerce' );

		parent::__construct(array(
			'name'            => __( 'WooCommerce', 'fl-builder' ),
			'description'     => __( 'Display products or categories from your WooCommerce store.', 'fl-builder' ),
			'category'        => __( 'WooCommerce', 'fl-builder' ),
			'icon'            => 'shopping-cart.svg',
			'enabled'         => $enabled,
			'partial_refresh' => true,
		));
	}

	/**
	 * @method products_post_class
	 */
	public function products_post_class( $classes ) {
		$classes[] = 'product';

		return $classes;
	}

	/**
	 * @method single_product_post_class
	 */
	public function single_product_post_class( $classes ) {
		$classes[] = 'product';
		$classes[] = 'single-product';

		return $classes;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLWooCommerceModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general'           => array(
				'title'  => '',
				'fields' => array(
					'layout'               => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => '',
						'options' => array(
							''             => __( 'Choose...', 'fl-builder' ),
							'product'      => __( 'Single Product', 'fl-builder' ),
							'product_page' => __( 'Product Page', 'fl-builder' ),
							'products'     => __( 'Multiple Products', 'fl-builder' ),
							'add-cart'     => __( '"Add to Cart" Button', 'fl-builder' ),
							'categories'   => __( 'Categories', 'fl-builder' ),
							'cart'         => __( 'Cart', 'fl-builder' ),
							'checkout'     => __( 'Checkout', 'fl-builder' ),
							'tracking'     => __( 'Order Tracking', 'fl-builder' ),
							'account'      => __( 'My Account', 'fl-builder' ),
						),
						'toggle'  => array(
							'product'      => array(
								'fields' => array( 'product_id' ),
							),
							'product_page' => array(
								'fields' => array( 'product_id' ),
							),
							'products'     => array(
								'sections' => array( 'multiple_products' ),
							),
							'add-cart'     => array(
								'fields' => array( 'product_id' ),
							),
							'categories'   => array(
								'fields' => array( 'autoparent', 'product_category_ids', 'parent_cat_id', 'cat_columns', 'category_orderby', 'category_order' ),
							),
						),
					),
					'product_category_ids' => array(
						'type'    => 'text',
						'label'   => __( 'Product Category IDs to include', 'fl-builder' ),
						'default' => '',
						'help'    => __( 'A comma-separated list of Product Category IDs to include. Leave blank to include all Product Categories.', 'fl-builder' ),
					),
					'autoparent'           => array(
						'type'    => 'select',
						'label'   => __( 'Autoselect Parent', 'fl-builder' ),
						'default' => 'false',
						'options' => array(
							'true'  => __( 'Yes', 'fl-builder' ),
							'false' => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'false' => array(
								'fields' => array( 'parent_cat_id' ),
							),
						),
					),
					'product_id'           => array(
						'type'    => 'text',
						'label'   => __( 'Product ID', 'fl-builder' ),
						'default' => '',
						'size'    => '4',
						'help'    => __( 'As you add products in the WooCommerce Products area, each will be assigned a unique ID. You can find this unique product ID by visiting the Products area and rolling over the product. The unique ID will be the first attribute.', 'fl-builder' ),
					),
					'parent_cat_id'        => array(
						'type'    => 'text',
						'label'   => __( 'Parent Category ID', 'fl-builder' ),
						'default' => '0',
						'size'    => '4',
						'help'    => __( 'As you add product categories in the WooCommerce Products area, each will be assigned a unique ID. This ID can be found by hovering on the category in the categories area under Products and looking in the URL that is displayed in your browser. The ID will be the only number value in the URL.', 'fl-builder' ),
					),
					'category_orderby'     => array(
						'type'    => 'select',
						'label'   => __( 'Sort Product Category By', 'fl-builder' ),
						'default' => 'name',
						'options' => array(
							'name'       => _x( 'Name', 'Sort by.', 'fl-builder' ),
							'id'         => __( 'Category ID', 'fl-builder' ),
							'slug'       => __( 'Category Slug', 'fl-builder' ),
							'menu_order' => __( 'Menu Order', 'fl-builder' ),
						),
					),
					'category_order'       => array(
						'type'    => 'select',
						'label'   => __( 'Product Category Sort Direction', 'fl-builder' ),
						'default' => 'asc',
						'options' => array(
							'asc'  => __( 'Ascending', 'fl-builder' ),
							'desc' => __( 'Descending', 'fl-builder' ),
						),
					),
					'cat_columns'          => array(
						'type'    => 'select',
						'label'   => __( 'Columns', 'fl-builder' ),
						'default' => '4',
						'options' => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
						),
					),
				),
			),
			'multiple_products' => array(
				'title'  => __( 'Multiple Products', 'fl-builder' ),
				'fields' => array(
					'products_source' => array(
						'type'    => 'select',
						'label'   => __( 'Products Source', 'fl-builder' ),
						'default' => 'ids',
						'options' => array(
							'ids'          => __( 'Products IDs', 'fl-builder' ),
							'category'     => __( 'Product Category', 'fl-builder' ),
							'tags'         => __( 'Product Tags', 'fl-builder' ),
							'recent'       => __( 'Recent Products', 'fl-builder' ),
							'featured'     => __( 'Featured Products', 'fl-builder' ),
							'sale'         => __( 'Sale Products', 'fl-builder' ),
							'best-selling' => __( 'Best Selling Products', 'fl-builder' ),
							'top-rated'    => __( 'Top Rated Products', 'fl-builder' ),
						),
						'toggle'  => array(
							'ids'          => array(
								'fields' => array( 'product_ids', 'columns', 'orderby', 'order' ),
							),
							'category'     => array(
								'fields' => array( 'category_slug', 'num_products', 'columns', 'orderby', 'order' ),
							),
							'tags'         => array(
								'fields' => array( 'tags_slug', 'num_products', 'columns', 'orderby', 'order' ),
							),
							'recent'       => array(
								'fields' => array( 'num_products', 'columns', 'orderby', 'order' ),
							),
							'featured'     => array(
								'fields' => array( 'num_products', 'columns', 'orderby', 'order' ),
							),
							'sale'         => array(
								'fields' => array( 'num_products', 'columns', 'orderby', 'order' ),
							),
							'best-selling' => array(
								'fields' => array( 'num_products', 'columns' ),
							),
							'top-rated'    => array(
								'fields' => array( 'num_products', 'columns', 'orderby', 'order' ),
							),
						),
					),
					'product_ids'     => array(
						'type'    => 'text',
						'label'   => __( 'Product IDs', 'fl-builder' ),
						'default' => '',
						'help'    => __( 'As you add products in the WooCommerce Products area, each will be assigned a unique ID. You can find this unique product ID by visiting the Products area and rolling over the product. The unique ID will be the first attribute and you can add several here separated by a comma.', 'fl-builder' ),
					),
					'category_slug'   => array(
						'type'    => 'text',
						'label'   => __( 'Category Slug', 'fl-builder' ),
						'default' => '',
						'help'    => __( 'As you add product categories in the WooCommerce Products area, each will be assigned a unique slug or you can edit and add your own. These slugs can be found in the Categories area under WooCommerce Products. Several can be added here separated by a comma.', 'fl-builder' ),
					),
					'tags_slug'       => array(
						'type'    => 'text',
						'label'   => __( 'Tags Slug', 'fl-builder' ),
						'default' => '',
						'help'    => __( 'As you add product tags in the WooCommerce Products area, each will be assigned a unique slug or you can edit and add your own. These slugs can be found in the Tags area under WooCommerce Products. Several can be added here separated by a comma.', 'fl-builder' ),
					),
					'num_products'    => array(
						'type'    => 'text',
						'label'   => __( 'Number of Products', 'fl-builder' ),
						'default' => '12',
						'size'    => '4',
					),
					'columns'         => array(
						'type'    => 'select',
						'label'   => __( 'Columns', 'fl-builder' ),
						'default' => '4',
						'options' => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
							'5' => '5',
							'6' => '6',
						),
					),
					'orderby'         => array(
						'type'    => 'select',
						'label'   => __( 'Sort By', 'fl-builder' ),
						'default' => 'menu_order',
						'options' => array(
							'menu_order' => _x( 'Default', 'Sort by.', 'fl-builder' ),
							'popularity' => __( 'Popularity', 'fl-builder' ),
							'rating'     => __( 'Rating', 'fl-builder' ),
							'date'       => __( 'Date', 'fl-builder' ),
							'price'      => __( 'Price', 'fl-builder' ),
							'id'         => __( 'Product ID', 'fl-builder' ),
						),
					),
					'order'           => array(
						'type'    => 'select',
						'label'   => __( 'Sort Direction', 'fl-builder' ),
						'default' => 'menu_order',
						'options' => array(
							'ASC'  => __( 'Ascending', 'fl-builder' ),
							'DESC' => __( 'Descending', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
));
