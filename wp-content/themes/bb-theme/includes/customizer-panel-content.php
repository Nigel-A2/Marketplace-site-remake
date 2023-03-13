<?php

/* Content Panel */
FLCustomizer::add_panel('fl-content', array(
	'title'    => _x( 'Content', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* Content Background Section */
		'fl-content-bg'             => array(
			'title'   => _x( 'Content Background', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Content Background Color */
				'fl-content-bg-color'      => array(
					'setting' => array(
						'default' => '#ffffff',
					),
					'control' => array(
						'class' => 'WP_Customize_Color_Control',
						'label' => __( 'Background Color', 'fl-automator' ),
					),
				),

				/* Content Background Opacity */
				'fl-content-bg-opacity'    => array(
					'setting' => array(
						'default' => '100',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Background Opacity (%)', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
				),

				/* Content Background Image */
				'fl-content-bg-image'      => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class' => 'WP_Customize_Image_Control',
						'label' => __( 'Background Image', 'fl-automator' ),
					),
				),

				/* Content Background Repeat */
				'fl-content-bg-repeat'     => array(
					'setting' => array(
						'default'   => 'no-repeat',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Repeat', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'no-repeat' => __( 'None', 'fl-automator' ),
							'repeat'    => __( 'Tile', 'fl-automator' ),
							'repeat-x'  => __( 'Horizontal', 'fl-automator' ),
							'repeat-y'  => __( 'Vertical', 'fl-automator' ),
						),
					),
				),

				/* Content Background Position */
				'fl-content-bg-position'   => array(
					'setting' => array(
						'default'   => 'center top',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Position', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'left top'      => __( 'Left Top', 'fl-automator' ),
							'left center'   => __( 'Left Center', 'fl-automator' ),
							'left bottom'   => __( 'Left Bottom', 'fl-automator' ),
							'right top'     => __( 'Right Top', 'fl-automator' ),
							'right center'  => __( 'Right Center', 'fl-automator' ),
							'right bottom'  => __( 'Right Bottom', 'fl-automator' ),
							'center top'    => __( 'Center Top', 'fl-automator' ),
							'center center' => __( 'Center', 'fl-automator' ),
							'center bottom' => __( 'Center Bottom', 'fl-automator' ),
						),
					),
				),

				/* Content Background Attachment */
				'fl-content-bg-attachment' => array(
					'setting' => array(
						'default'   => 'scroll',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Attachment', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'scroll' => __( 'Scroll', 'fl-automator' ),
							'fixed'  => __( 'Fixed', 'fl-automator' ),
						),
					),
				),

				/* Content Background Size */
				'fl-content-bg-size'       => array(
					'setting' => array(
						'default'   => 'auto',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Background Scale', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'auto'    => __( 'None', 'fl-automator' ),
							'contain' => __( 'Fit', 'fl-automator' ),
							'cover'   => __( 'Fill', 'fl-automator' ),
						),
					),
				),
			),
		),

		/* Blog Section */
		'fl-content-blog'           => array(
			'title'   => _x( 'Blog Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Blog Layout */
				'fl-blog-layout'                      => array(
					'setting' => array(
						'default' => 'sidebar-right',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Sidebar Position', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'sidebar-right' => __( 'Sidebar Right', 'fl-automator' ),
							'sidebar-left'  => __( 'Sidebar Left', 'fl-automator' ),
							'no-sidebar'    => __( 'No Sidebar', 'fl-automator' ),
						),
					),
				),

				/* Blog Sidebar Size */
				'fl-blog-sidebar-size'                => array(
					'setting' => array(
						'default' => '4',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Sidebar Size', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'4'      => _x( 'Large', 'Sidebar size.', 'fl-automator' ),
							'3'      => _x( 'Medium', 'Sidebar size.', 'fl-automator' ),
							'2'      => _x( 'Small', 'Sidebar size.', 'fl-automator' ),
							'custom' => _x( 'Custom', 'Sidebar size.', 'fl-automator' ),
						),
					),
				),

				/* Custom Blog Sidebar Size */
				'fl-blog-custom-sidebar-size'         => array(
					'setting' => array(
						'default'           => '25',
						'sanitize_callback' => 'FLCustomizer::sanitize_number',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Custom Sidebar Width', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 10,
							'max'  => 50,
							'step' => 1,
						),
					),
				),

				/* Blog Sidebar Display */
				'fl-blog-sidebar-display'             => array(
					'setting' => array(
						'default' => 'desktop',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Sidebar Display', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'desktop' => __( 'Desktop Only', 'fl-automator' ),
							'always'  => __( 'Always', 'fl-automator' ),
						),
					),
				),

				/* Blog Sidebar Location */
				'fl-blog-sidebar-location'            => array(
					'setting' => array(
						'default'           => 'single,blog,search,archive',
						'sanitize_callback' => 'FLCustomizer::sanitize_checkbox_multiple',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Sidebar Location', 'fl-automator' ),
						'type'    => 'checkbox-multiple',
						'choices' => array(
							'blog'    => __( 'Blog', 'fl-automator' ),
							'single'  => __( 'Single Post', 'fl-automator' ),
							'search'  => __( 'Search page', 'fl-automator' ),
							'archive' => __( 'Archives', 'fl-automator' ),
						),
					),
				),

				/* Enable / Disable Sidebar for Post Types */
				'fl-blog-sidebar-location-post-types' => array(
					'setting' => array(
						'default'           => 'all',
						'sanitize_callback' => 'FLCustomizer::sanitize_checkbox_multiple',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Apply Sidebar To Post Types', 'fl-automator' ),
						'type'    => 'checkbox-multiple',
						'choices' => array(
							'custom' => 'post_types',
						),
					),
				),

				/* Line */
				'fl-blog-line1'                       => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Post Author */
				'fl-blog-post-author'                 => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Post Author', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Post Date */
				'fl-blog-post-date'                   => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Post Date', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Comment Count */
				'fl-blog-comment-count'               => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Comment Count', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),
			),
		),

		/* Archive Pages Section */
		'fl-content-archives'       => array(
			'title'   => _x( 'Archive Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Show Full Text */
				'fl-archive-show-full'     => array(
					'setting' => array(
						'default' => '0',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Show Full Text', 'fl-automator' ),
						'description' => __( 'Whether or not to show the full post. If no, the excerpt will be shown.', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'1' => __( 'Yes', 'fl-automator' ),
							'0' => __( 'No', 'fl-automator' ),
						),
					),
				),

				/* Read More Text */
				'fl-archive-readmore-text' => array(
					'setting' => array(
						'default' => __( 'Read More', 'fl-automator' ),
					),
					'control' => array(
						'class' => 'WP_Customize_Control',
						'label' => __( '"Read More" Text', 'fl-automator' ),
						'type'  => 'text',
					),
				),

				/* Featured Image */
				'fl-archive-show-thumbs'   => array(
					'setting' => array(
						'default' => 'beside',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Featured Image', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							''            => __( 'Hidden', 'fl-automator' ),
							'above-title' => __( 'Above Titles', 'fl-automator' ),
							'above'       => __( 'Above Posts', 'fl-automator' ),
							'beside'      => __( 'Beside Posts', 'fl-automator' ),
						),
					),
				),

				/* Featured Image Size */
				'fl-archive-thumb-size'    => array(
					'setting' => array(
						'default' => 'large',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Image Size', 'fl-automator' ),
						'type'    => 'select',
						'choices' => archive_post_image_sizes(),
					),
				),

			),
		),

		/* Search Results Page Section */
		'fl-content-search-results' => array(
			'title'   => _x( 'Search Results Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(
				/* Post Info: Author */
				'fl-search-results-author'        => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Post Author', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Post Info: Date */
				'fl-search-results-date'          => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Post Date', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Post Info: Comment Count */
				'fl-search-results-comment-count' => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Comment Count', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),
			),
		),

		/* Post Pages Section */
		'fl-content-posts'          => array(
			'title'   => _x( 'Post Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Featured Image */
				'fl-posts-show-thumbs' => array(
					'setting' => array(
						'default' => '',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Featured Image', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							''            => __( 'Hidden', 'fl-automator' ),
							'above-title' => __( 'Above Title', 'fl-automator' ),
							'above'       => __( 'Above Post', 'fl-automator' ),
							'beside'      => __( 'Beside Post', 'fl-automator' ),
						),
					),
				),

				/* Featured Image Size */
				'fl-posts-thumb-size'  => array(
					'setting' => array(
						'default' => 'thumbnail',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Image Size', 'fl-automator' ),
						'type'    => 'select',
						'choices' => single_post_image_sizes(),
					),
				),

				/* Post Categories */
				'fl-posts-show-cats'   => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Post Categories', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Post Tags */
				'fl-posts-show-tags'   => array(
					'setting' => array(
						'default' => 'visible',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Post Tags', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Prev/Next Post Links */
				'fl-posts-show-nav'    => array(
					'setting' => array(
						'default' => 'hidden',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Prev/Next Post Links', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Author Box */
				'fl-post-author-box'   => array(
					'setting' => array(
						'default' => 'hidden',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Author Box', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

			),
		),

		/* WooCommerce Section */
		'fl-content-woo'            => array(
			'title'   => _x( 'WooCommerce Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* WooCommerce Layout */
				'fl-woo-layout'              => array(
					'setting' => array(
						'default' => 'no-sidebar',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Sidebar Position', 'fl-automator' ),
						'description' => __( 'The location of the WooCommerce sidebar.', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'sidebar-right' => __( 'Sidebar Right', 'fl-automator' ),
							'sidebar-left'  => __( 'Sidebar Left', 'fl-automator' ),
							'no-sidebar'    => __( 'No Sidebar', 'fl-automator' ),
						),
					),
				),

				/* WooCommerce Sidebar Size */
				'fl-woo-sidebar-size'        => array(
					'setting' => array(
						'default' => '4',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Sidebar Size', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'4'      => _x( 'Large', 'Sidebar size.', 'fl-automator' ),
							'3'      => _x( 'Medium', 'Sidebar size.', 'fl-automator' ),
							'2'      => _x( 'Small', 'Sidebar size.', 'fl-automator' ),
							'custom' => _x( 'Custom', 'Sidebar size.', 'fl-automator' ),
						),
					),
				),

				/* Custom WooCommerce Sidebar Size */
				'fl-woo-custom-sidebar-size' => array(
					'setting' => array(
						'default'           => '25',
						'sanitize_callback' => 'FLCustomizer::sanitize_number',
					),
					'control' => array(
						'class'   => 'FLCustomizerControl',
						'label'   => __( 'Custom Sidebar Width', 'fl-automator' ),
						'type'    => 'slider',
						'choices' => array(
							'min'  => 10,
							'max'  => 50,
							'step' => 1,
						),
					),
				),

				/* WooCommerce Sidebar Display */
				'fl-woo-sidebar-display'     => array(
					'setting' => array(
						'default' => 'desktop',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Sidebar Display', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'desktop' => __( 'Desktop Only', 'fl-automator' ),
							'always'  => __( 'Always', 'fl-automator' ),
						),
					),
				),

				/* WooCommerce Sidebar Location */
				'fl-woo-sidebar-location'    => array(
					'setting' => array(
						'default'           => 'single,shop',
						'sanitize_callback' => 'FLCustomizer::sanitize_checkbox_multiple',
					),
					'control' => array(
						'class'       => 'FLCustomizerControl',
						'label'       => __( 'Sidebar Location', 'fl-automator' ),
						'description' => __( 'WooCommerce pages that you want sidebar to appear.', 'fl-automator' ),
						'type'        => 'checkbox-multiple',
						'choices'     => array(
							'single'  => __( 'Single Product', 'fl-automator' ),
							'shop'    => __( 'Shop Page', 'fl-automator' ),
							'archive' => __( 'Categories', 'fl-automator' ),
						),
					),
				),

				/* Line */
				'fl-woo-line1'               => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Number of Columns */
				'fl-woo-columns'             => array(
					'setting' => array(
						'default' => '4',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Number of Columns/Products per row', 'fl-automator' ),
						'description' => __( 'How many columns/products per row to display on the page?', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'1' => __( '1 Column', 'fl-automator' ),
							'2' => __( '2 Columns', 'fl-automator' ),
							'3' => __( '3 Columns', 'fl-automator' ),
							'4' => __( '4 Columns', 'fl-automator' ),
							'5' => __( '5 Columns', 'fl-automator' ),
							'6' => __( '6 Columns', 'fl-automator' ),
						),
					),
				),

				/* Number of Products Per Page */
				'fl-woo-products-per-page'   => array(
					'setting' => array(
						'default'           => '16',
						'sanitize_callback' => 'FLCustomizer::sanitize_number',
					),
					'control' => array(
						'class'       => 'FLCustomizerControl',
						'type'        => 'slider',
						'label'       => __( 'Products Per Page', 'fl-automator' ),
						'description' => __( 'How many products to display per page?', 'fl-automator' ),
						'choices'     => array(
							'min'  => 1,
							'max'  => 200,
							'step' => 1,
						),
					),
				),

				/* Line */
				'fl-woo-line2'               => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Number of Columns */
				'fl-woo-gallery'             => array(
					'setting' => array(
						'default'   => 'enabled',
						'transport' => 'postMessage',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'Product Gallery', 'fl-automator' ),
						'description' => __( 'Select how product galleries are handled.', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'enabled' => __( 'Use WooCommerce 3.x Gallery (default)', 'fl-automator' ),
							'none'    => __( 'Disabled', 'fl-automator' ),
						),
					),
				),

				/* Line */
				'fl-woo-line3'               => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* Add to Cart Button */
				'fl-woo-cart-button'         => array(
					'setting' => array(
						'default' => 'hidden',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( '"Add to Cart" Button', 'fl-automator' ),
						'description' => __( 'Show the "Add to Cart" button on product category pages?', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'visible' => __( 'Visible', 'fl-automator' ),
							'hidden'  => __( 'Hidden', 'fl-automator' ),
						),
					),
				),

				/* Line */
				'fl-woo-line4'               => array(
					'control' => array(
						'class' => 'FLCustomizerControl',
						'type'  => 'line',
					),
				),

				/* WooCommerce CSS */
				'fl-woo-css'                 => array(
					'setting' => array(
						'default' => 'enabled',
					),
					'control' => array(
						'class'       => 'WP_Customize_Control',
						'label'       => __( 'WooCommerce Styling', 'fl-automator' ),
						'description' => __( 'Enable or disable the theme’s custom WooCommerce styles.', 'fl-automator' ),
						'type'        => 'select',
						'choices'     => array(
							'enabled'  => __( 'Enabled', 'fl-automator' ),
							'disabled' => __( 'Disabled', 'fl-automator' ),
						),
					),
				),
			),
		),

		/* Lightbox Section */
		'fl-lightbox-layout'        => array(
			'title'   => _x( 'Lightbox', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Lightbox */
				'fl-lightbox' => array(
					'setting' => array(
						'default' => 'enabled',
					),
					'control' => array(
						'class'   => 'WP_Customize_Control',
						'label'   => __( 'Lightbox', 'fl-automator' ),
						'type'    => 'select',
						'choices' => array(
							'enabled'  => __( 'Enabled', 'fl-automator' ),
							'disabled' => __( 'Disabled', 'fl-automator' ),
						),
					),
				),
			),
		),
	),
));
