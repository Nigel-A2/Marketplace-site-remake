<?php

/**
 * Post module alias for products on archive layouts.
 *
 * @since 1.0
 */
class FLBCProductsModule extends FLBuilderModule {

	/**
	 * @return void
	 */
	public function __construct() {
		$enabled = class_exists( '\BigCommerce\Plugin' );
		parent::__construct( array(
			'name'            => __( 'BigCommerce Products', 'fl-builder' ),
			'description'     => __( 'Displays the BigCommerce product gallery.', 'fl-builder' ),
			'category'        => __( 'BigCommerce', 'fl-builder' ),
			'icon'            => 'shopping-cart.svg',
			'partial_refresh' => false,
			'enabled'         => $enabled,
		) );
	}
}

/**
 * [bigcommerce_product]
 *
 * id - A comma delimited list of BigCommerce product IDs
 * post_id - A comma delimited list of WordPress product post IDs
 * sku - A comma delimited list of BigCommerce product SKUs
 * category - A comma delimited list of Product Category slugs
 * brand - A comma delimited list of Brand slugs
 * x- featured - Set to 1 to limit the query to featured products
 * x- sale - Set to 1 to limit the query to sale products
 * x- recent - Set to 1 to limit the query to products imported in the last 2 days (filter the duration with the bigcommerce/query/recent_days filter)
 * search - A search string to match against product titles, BigCommerce product IDs, or SKUs
 * x- paged - Set to 0 to disable pagination
 * x- per_page - The number of products to show per page. Defaults to the value set in the theme customizer.
 * order - Whether to sort products in “ASC” or “DESC” order
 * orderby - Which field to use for sorting. Accepts any field that WP_Query accepts (e.g., title, date)
 */
FLBuilder::register_module( 'FLBCProductsModule', array(

	'general' => array(
		'title'    => __( 'Content', 'fl-builder' ),
		'sections' => array(
			'pagination' => array(
				'title'  => __( 'Pagination', 'fl-builder' ),
				'fields' => array(
					'paged'    => array(
						'type'    => 'select',
						'label'   => __( 'Use Pagination', 'fl-builder' ),
						'default' => 0,
						'options' => array(
							0 => __( 'No', 'fl-builder' ),
							1 => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							0 => array(),
							1 => array(
								'fields' => array( 'per_page' ),
							),
						),
					),
					'per_page' => array(
						'type'    => 'unit',
						'label'   => __( 'Products per page', 'fl-builder' ),
						'units'   => array( 'products' ),
						'default' => 8,
						'slider'  => array(
							'products' => array(
								'min'  => 0,
								'max'  => 100,
								'step' => 5,
							),
						),
					),
				),
			),
			'filters'    => array(
				'title'  => __( 'Filters', 'fl-builder' ),
				'fields' => array(
					'featured' => array(
						'type'    => 'select',
						'label'   => __( 'Show only featured products?', 'fl-builder' ),
						'default' => 0,
						'options' => array(
							0 => __( 'No', 'fl-builder' ),
							1 => __( 'Yes', 'fl-builder' ),
						),
					),
					'sale'     => array(
						'type'    => 'select',
						'label'   => __( 'Show only on-sale products?', 'fl-builder' ),
						'default' => 0,
						'options' => array(
							0 => __( 'No', 'fl-builder' ),
							1 => __( 'Yes', 'fl-builder' ),
						),
					),
					'recent'   => array(
						'type'    => 'select',
						'label'   => __( 'Show only recent products?', 'fl-builder' ),
						'default' => 0,
						'options' => array(
							0 => __( 'No', 'fl-builder' ),
							1 => __( 'Yes', 'fl-builder' ),
						),
					),

				),
			),
		),
	),
) );
