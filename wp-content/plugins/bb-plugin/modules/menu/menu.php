<?php

/**
 * @class FLMenuModule
 */
class FLMenuModule extends FLBuilderModule {

	/**
	 * @property $fl_builder_page_id
	 */
	public static $fl_builder_page_id;

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Menu', 'fl-builder' ),
			'description'     => __( 'Renders a WordPress menu.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'partial_refresh' => true,
			'editor_export'   => false,
			'icon'            => 'menu.svg',
		));

		// Actions
		add_action( 'pre_get_posts', __CLASS__ . '::set_pre_get_posts_query', 10, 2 );

		// Filters
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'woo_menu_cart_ajax_fragments' ) );
		}
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		if ( ! FLBuilderModel::is_builder_active() && $this->is_responsive_menu_flyout() ) {
			wp_add_inline_script( 'fl-builder-layout-' . FLBuilderModel::get_post_id(), sprintf( 'var fl_responsive_close="%s"', __( 'Close', 'fl-builder' ) ) );
			$this->add_css( 'font-awesome-5' );
		}
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {

		// We need to double the old opacity inputs because the bg color used
		// to be applied to the menu and the list items which layers the color.
		if ( isset( $settings->menu_bg_opacity ) && is_numeric( $settings->menu_bg_opacity ) ) {
			$settings->menu_bg_opacity        = $settings->menu_bg_opacity * 1.5;
			$settings->mobile_menu_bg_opacity = $settings->menu_bg_opacity;
		}

		// Handle old opacity inputs.
		$helper->handle_opacity_inputs( $settings, 'menu_bg_opacity', 'menu_bg_color' );
		$helper->handle_opacity_inputs( $settings, 'mobile_menu_bg_opacity', 'mobile_menu_bg' );
		$helper->handle_opacity_inputs( $settings, 'submenu_bg_opacity', 'submenu_bg_color' );
		$helper->handle_opacity_inputs( $settings, 'separator_opacity', 'separator_color' );

		// Remove old align default.
		if ( 'default' === $settings->menu_align ) {
			$settings->menu_align = '';
		}

		// Handle old horizontal_spacing.
		if ( isset( $settings->horizontal_spacing ) ) {
			$settings->link_spacing_left  = $settings->horizontal_spacing;
			$settings->link_spacing_right = $settings->horizontal_spacing;
			unset( $settings->horizontal_spacing );
		}

		// Handle old vertical_spacing.
		if ( isset( $settings->vertical_spacing ) ) {
			$settings->link_spacing_top    = $settings->vertical_spacing;
			$settings->link_spacing_bottom = $settings->vertical_spacing;
			unset( $settings->vertical_spacing );
		}

		// Make sure we have a typography array.
		if ( ! isset( $settings->typography ) || ! is_array( $settings->typography ) ) {
			$settings->typography            = array();
			$settings->typography_medium     = array();
			$settings->typography_responsive = array();
		}

		// Handle old font setting.
		if ( isset( $settings->font ) ) {
			$settings->typography['font_family'] = $settings->font['family'];
			$settings->typography['font_weight'] = $settings->font['weight'];
			unset( $settings->font );
		}

		// Handle old font size setting.
		if ( isset( $settings->text_size ) ) {
			$settings->typography['font_size'] = array(
				'length' => $settings->text_size,
				'unit'   => 'px',
			);
			unset( $settings->text_size );
		}

		// Handle old text transform setting.
		if ( isset( $settings->text_transform ) ) {
			$settings->typography['text_transform'] = $settings->text_transform;
			unset( $settings->text_transform );
		}

		// Handle old submenu spacing.
		if ( isset( $settings->submenu_spacing ) ) {
			$settings->submenu_spacing_top    = $settings->submenu_spacing;
			$settings->submenu_spacing_right  = $settings->submenu_spacing;
			$settings->submenu_spacing_bottom = $settings->submenu_spacing;
			$settings->submenu_spacing_left   = $settings->submenu_spacing;
			unset( $settings->submenu_spacing );
		}

		// Return the filtered settings.
		return $settings;
	}

	/**
	 * Get the WordPress menu options.
	 *
	 * @return array
	 */
	public static function _get_menus() {
		$get_menus = get_terms( 'nav_menu', array(
			'hide_empty' => true,
		) );
		$fields    = array(
			'label' => __( 'Menu', 'fl-builder' ),
			'help'  => __( 'Select a WordPress menu that you created in the admin under Appearance > Menus.', 'fl-builder' ),
		);

		if ( $get_menus ) {

			$fields['type'] = 'select';

			foreach ( $get_menus as $key => $menu ) {

				if ( 0 == $key ) {
					$fields['default'] = $menu->name;
				}

				$menus[ $menu->slug ] = $menu->name;
			}

			$fields['options'] = $menus;

		} else {

			$url  = current_user_can( 'edit_theme_options' ) ? admin_url( 'nav-menus.php' ) : esc_url( home_url( '/' ) );
			$text = current_user_can( 'edit_theme_options' ) ? __( 'Add a menu', 'fl-builder' ) : __( 'Home', 'fl-builder' );

			$fields['type']    = 'raw';
			$fields['content'] = sprintf( '<p class="fl-builder-settings-tab-description">%s&nbsp;<a target="_blank" href="%s">%s</a></p>', __( 'No Menus Found.', 'fl-builder' ), $url, $text );
		}

		return $fields;

	}

	public function get_menu_label() {
		return isset( $this->settings->mobile_title ) && '' !== $this->settings->mobile_title ? $this->settings->mobile_title : __( 'Menu', 'fl-builder' );
	}

	public function render_toggle_button() {

		$toggle = $this->settings->mobile_toggle;

		$menu_title = $this->get_menu_label();

		if ( isset( $toggle ) && 'expanded' != $toggle ) {

			if ( in_array( $toggle, array( 'hamburger', 'hamburger-label' ) ) ) {
				$menu_icon = apply_filters( 'fl_builder_mobile_menu_icon', file_get_contents( FL_BUILDER_DIR . 'img/svg/hamburger-menu.svg' ) );
				echo '<button class="fl-menu-mobile-toggle ' . $toggle . '" aria-label="' . esc_attr( $menu_title ) . '">';
				echo '<span class="fl-menu-icon svg-container">';
				echo $menu_icon;
				echo '</span>';

				if ( 'hamburger-label' == $toggle ) {
					echo '<span class="fl-menu-mobile-toggle-label">' . esc_attr( $menu_title ) . '</span>';
				}

				echo '</button>';

			} elseif ( 'text' == $toggle ) {

				echo '<button class="fl-menu-mobile-toggle text"><span class="fl-menu-mobile-toggle-label" aria-label="' . esc_attr( $menu_title ) . '">' . esc_attr( $menu_title ) . '</span></button>';

			}
		}
	}

	public static function set_pre_get_posts_query( $query ) {
		if ( ! is_admin() && $query->is_main_query() ) {

			if ( $query->queried_object_id ) {

				self::$fl_builder_page_id = $query->queried_object_id;

				// Fix when menu module is rendered via hook
			} elseif ( isset( $query->query_vars['page_id'] ) && 0 != $query->query_vars['page_id'] ) {

				self::$fl_builder_page_id = $query->query_vars['page_id'];

			}
		}
	}

	public static function sort_nav_objects( $sorted_menu_items, $args ) {
		$menu_items   = array();
		$parent_items = array();
		foreach ( $sorted_menu_items as $key => $menu_item ) {
			$classes = (array) $menu_item->classes;

			// Setup classes for current menu item.
			if ( $menu_item->ID == self::$fl_builder_page_id || self::$fl_builder_page_id == $menu_item->object_id ) {
				$parent_items[ $menu_item->object_id ] = $menu_item->menu_item_parent;

				if ( ! in_array( 'current-menu-item', $classes ) ) {
					$classes[] = 'current-menu-item';

					if ( 'page' == $menu_item->object ) {
						$classes[] = 'current_page_item';
					}
				}
			}
			$menu_item->classes = $classes;
			$menu_items[ $key ] = $menu_item;
		}

		// Setup classes for parent's current item.
		foreach ( $menu_items as $key => $sorted_item ) {
			if ( in_array( $sorted_item->db_id, $parent_items ) && ! in_array( 'current-menu-parent', (array) $sorted_item->classes ) ) {
				$menu_items[ $key ]->classes[] = 'current-menu-ancestor';
				$menu_items[ $key ]->classes[] = 'current-menu-parent';
			}
		}

		return $menu_items;
	}

	public function get_media_breakpoint() {
		$global_settings   = FLBuilderModel::get_global_settings();
		$media_width       = $global_settings->responsive_breakpoint;
		$mobile_breakpoint = $this->settings->mobile_breakpoint;

		if ( isset( $mobile_breakpoint ) && 'expanded' != $this->settings->mobile_toggle ) {
			if ( 'medium-mobile' == $mobile_breakpoint ) {
				$media_width = $global_settings->medium_breakpoint;
			} elseif ( 'mobile' == $this->settings->mobile_breakpoint ) {
				$media_width = $global_settings->responsive_breakpoint;
			} elseif ( 'always' == $this->settings->mobile_breakpoint ) {
				$media_width = 'always';
			}
		}

		return $media_width;
	}

	/**
	 * Checks to see if responsive menu style is flyout.
	 *
	 * @since 2.2
	 * @return bool
	 */
	public function is_responsive_menu_flyout() {
		return strpos( $this->settings->mobile_full_width, 'flyout-' ) !== false;
	}

	/**
	 * Gets the total number of top level menu items.
	 *
	 * @since 2.5
	 * @return int
	 */
	public function get_total_top_lvl_items() {
		$settings = $this->settings;
		$count    = count( wp_list_filter( wp_get_nav_menu_items( $this->settings->menu ), array( 'menu_item_parent' => 0 ) ) );

		if ( isset( $settings->woo_menu_cart ) && 'show' == $settings->woo_menu_cart ) {
			$count++;
		}

		if ( isset( $settings->menu_search ) && 'show' == $settings->menu_search ) {
			$count++;
		}

		return $count;
	}

	/**
	 * Filters nav menu items.
	 *
	 * @return string
	 */
	public function filter_nav_menu_items( $items ) {
		$settings = $this->settings;

		if ( isset( $settings->woo_menu_cart ) && 'show' == $settings->woo_menu_cart ) {
			$items = $this->render_menu_woo_cart( $items );
		}

		if ( isset( $settings->menu_search ) && 'show' == $settings->menu_search ) {
			$items = $this->render_menu_search( $items );
		}

		return $items;
	}

	/**
	 * Add Woo cart to menu.
	 *
	 * @return string
	 */
	public function render_menu_woo_cart( $items ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $items;
		}

		// Bail out if no data to load.
		if ( empty( WC()->cart ) ) {
			return $items;
		}

		$settings = $this->settings;
		$classes  = 'menu-item fl-menu-cart-item';

		$show_on_checkout = isset( $settings->show_menu_cart_checkout ) && 'yes' == $settings->show_menu_cart_checkout;

		if ( ! $show_on_checkout && ( is_checkout() || is_cart() ) ) {
			$classes .= ' fl-menu-cart-item-hidden';
		}

		$menu_cart_content = $this->woo_menu_cart_content();
		$menu_item_li      = "<li class='$classes'>$menu_cart_content</li>";
		$items            .= $menu_item_li;

		return $items;
	}

	/**
	 * Add search icon as menu item.
	 *
	 * @return string
	 */
	public function render_menu_search( $items ) {
		$settings = $this->menu_search_settings();

		ob_start();
		FLBuilder::render_module_html( 'search', $settings );
		$search_content = ob_get_clean();

		$items .= "<li class='menu-item fl-menu-search-item'>$search_content</li>";

		return $items;
	}

	/**
	 * Returns an array of settings used to render a button module in the search module.
	 *
	 * @return array
	 */
	public function menu_search_settings() {
		$settings = array(
			'layout'     => 'button',
			'btn_text'   => '',
			'btn_action' => 'reveal',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'search_btn_' ) ) {
				$key              = str_replace( 'search_btn_', 'btn_', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Enable Woo ajax cart.
	 *
	 * @return array
	 */
	public function woo_menu_cart_ajax_fragments( $fragments ) {
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		$menu_fragment = $this->woo_menu_cart_content();
		if ( ! empty( $menu_fragment ) ) {
			$fragments['a.fl-menu-cart-contents'] = $menu_fragment;
		}

		return $fragments;
	}

	/**
	 * Woo cart menu content.
	 *
	 * @return string
	 */
	public function woo_menu_cart_content() {
		$cart_count   = WC()->cart->cart_contents_count;
		$settings     = null;
		$item_content = '';

		if ( 0 == $cart_count ) {
			$menu_item_title   = __( 'Start shopping', 'fl-builder' );
			$menu_item_classes = 'fl-menu-cart-contents empty-fl-menu-cart-visible';
			$cart_url          = wc_get_page_permalink( 'shop' );
		} else {
			$menu_item_title   = __( 'View your shopping cart', 'fl-builder' );
			$menu_item_classes = 'fl-menu-cart-contents';
			$cart_url          = wc_get_cart_url();
		}

		if ( isset( $this->settings ) ) {
			$settings = $this->settings;
		} elseif ( $_REQUEST && isset( $_REQUEST['fl-menu-node'] ) ) {
			$menu_node = $_REQUEST['fl-menu-node'];
			$post_id   = (int) $_REQUEST['post-id'];

			$data = FLBuilderModel::get_layout_data( 'published', $post_id );
			if ( isset( $data[ $menu_node ] ) ) {
				$menu = $data[ $menu_node ];

				if ( $menu && isset( $menu->settings->woo_menu_cart ) && 'show' == $menu->settings->woo_menu_cart ) {
					$settings = $menu->settings;
				}
			}
		}

		if ( $settings ) {
			$display_type = isset( $settings->cart_display_type ) ? $settings->cart_display_type : 'count';
			/* translators: %d: item count */
			$items_count  = sprintf( _n( '%d item', '%d items', $cart_count, 'fl-builder' ), $cart_count );
			$cart_total   = $this->get_woo_cart_total();
			$cart_content = '<span class="fl-menu-cart-count">' . $items_count . '</span>';
			$icon         = '';

			if ( isset( $settings->cart_icon ) && ! empty( $settings->cart_icon ) ) {
				$icon = '<i class="fl-menu-cart-icon ' . $settings->cart_icon . '" role="img" aria-label="' . __( 'Cart', 'fl-builder' ) . '"></i>';
			}

			if ( in_array( $display_type, array( 'total', 'count-total' ) ) ) {
				$total_content = '<span class="fl-menu-cart-total">' . $cart_total . '</span>';
				if ( 'count-total' == $display_type ) {
					$cart_content .= ' &ndash; ' . $total_content;
				} else {
					$cart_content = $total_content;
				}
			}

			$menu_item_classes .= ' fl-menu-cart-type-' . $display_type;

			$item_content  = '<a class="' . $menu_item_classes . '" href="' . $cart_url . '" title="' . $menu_item_title . '">';
			$item_content .= $icon . $cart_content;
			$item_content .= '</a>';
		}

		return $item_content;
	}

	/**
	 * Get Woo cart total price.
	 */
	public function get_woo_cart_total() {
		$cart_total_type     = 'subtotal'; // subtotal | checkout_total
		$cart_contents_total = 0;
		if ( 'subtotal' == $cart_total_type ) {
			if ( WC()->cart->display_prices_including_tax() ) {
				$cart_contents_total = wc_price( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
			} else {
				$cart_contents_total = wc_price( WC()->cart->get_subtotal() );
			}
		} elseif ( 'checkout_total' == $cart_total_type ) {
			$cart_contents_total = wc_price( WC()->cart->get_total( 'edit' ) );
		} else {
			if ( WC()->cart->display_prices_including_tax() ) {
				$cart_contents_total = wc_price( WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() );
			} else {
				$cart_contents_total = wc_price( WC()->cart->get_cart_contents_total() );
			}
		}

		return $cart_contents_total;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLMenuModule', array(
	'general' => array( // Tab
		'title'    => __( 'General', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general'              => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'menu'                 => FLMenuModule::_get_menus(),
					'menu_layout'          => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'horizontal',
						'options' => array(
							'horizontal' => __( 'Horizontal', 'fl-builder' ),
							'vertical'   => __( 'Vertical', 'fl-builder' ),
							'accordion'  => __( 'Accordion', 'fl-builder' ),
							'expanded'   => __( 'Expanded', 'fl-builder' ),
						),
						'toggle'  => array(
							'horizontal' => array(
								'fields'   => array( 'submenu_hover_toggle', 'menu_align' ),
								'sections' => array( 'centered_inline_logo', 'search' ),
							),
							'vertical'   => array(
								'fields'   => array( 'submenu_hover_toggle' ),
								'sections' => array( 'search' ),
							),
							'accordion'  => array(
								'fields' => array( 'submenu_click_toggle', 'collapse' ),
							),
						),
					),
					'submenu_hover_toggle' => array(
						'type'    => 'select',
						'label'   => __( 'Submenu Icon', 'fl-builder' ),
						'default' => 'none',
						'options' => array(
							'arrows' => __( 'Arrows', 'fl-builder' ),
							'plus'   => __( 'Plus sign', 'fl-builder' ),
							'none'   => __( 'None', 'fl-builder' ),
						),
					),
					'submenu_click_toggle' => array(
						'type'    => 'select',
						'label'   => __( 'Submenu Icon click', 'fl-builder' ),
						'default' => 'arrows',
						'options' => array(
							'arrows' => __( 'Arrows', 'fl-builder' ),
							'plus'   => __( 'Plus sign', 'fl-builder' ),
						),
					),
					'collapse'             => array(
						'type'    => 'select',
						'label'   => __( 'Collapse Inactive', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
						'help'    => __( 'Choosing yes will keep only one item open at a time. Choosing no will allow multiple items to be open at the same time.', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'mobile_title'         => array(
						'label'   => __( 'Menu Name', 'fl-builder' ),
						'type'    => 'text',
						'help'    => __( 'This is used as the menu aria attribute for accessibility and label for responsive menus.', 'fl-builder' ),
						'default' => __( 'Menu', 'fl-builder' ),
					),
				),
			),
			'centered_inline_logo' => array(
				'title'  => __( 'Centered + Inline Logo', 'fl-builder' ),
				'fields' => array(
					'menu_logo_image'        => array(
						'type'        => 'photo',
						'label'       => __( 'Logo Image', 'fl-builder' ),
						'show_remove' => true,
					),
					'menu_logo_odd_position' => array(
						'type'    => 'select',
						'label'   => __( 'Inline Logo Position', 'fl-builder' ),
						'default' => 'left',
						'help'    => __( 'The inline logo will appear on the left or right side of odd menu items.', 'fl-builder' ),
						'options' => array(
							'left'  => __( 'Left', 'fl-builder' ),
							'right' => __( 'Right', 'fl-builder' ),
						),
					),
				),
			),
			'search'               => array(
				'title'  => __( 'Search', 'fl-builder' ),
				'fields' => array(
					'menu_search'     => array(
						'type'    => 'select',
						'label'   => __( 'Search Menu', 'fl-builder' ),
						'default' => 'hide',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields'   => array( 'search_btn_icon' ),
								'sections' => array( 'search_style' ),
							),
						),
					),
					'search_btn_icon' => array(
						'type'        => 'icon',
						'default'     => 'fas fa-search',
						'label'       => __( 'Icon', 'fl-builder' ),
						'show_remove' => true,
					),
				),
			),
			'mobile'               => array(
				'title'  => __( 'Responsive', 'fl-builder' ),
				'fields' => array(
					'mobile_toggle'                   => array(
						'type'    => 'select',
						'label'   => __( 'Responsive Toggle', 'fl-builder' ),
						'default' => 'hamburger',
						'options' => array(
							'hamburger'       => __( 'Hamburger Icon', 'fl-builder' ),
							'hamburger-label' => __( 'Hamburger Icon + Label', 'fl-builder' ),
							'text'            => __( 'Menu Button', 'fl-builder' ),
							'expanded'        => __( 'None', 'fl-builder' ),
						),
						'toggle'  => array(
							'hamburger'       => array(
								'fields'   => array( 'mobile_full_width', 'mobile_breakpoint' ),
								'sections' => array( 'mobile_toggle_style' ),
							),
							'hamburger-label' => array(
								'fields'   => array( 'mobile_full_width', 'mobile_breakpoint' ),
								'sections' => array( 'mobile_toggle_style' ),
							),
							'text'            => array(
								'fields'   => array( 'mobile_full_width', 'mobile_breakpoint' ),
								'sections' => array( 'mobile_toggle_style' ),
							),
							'expanded'        => array(
								'fields' => array( 'mobile_stacked' ),
							),
						),
					),
					'mobile_full_width'               => array(
						'type'    => 'select',
						'label'   => __( 'Responsive Style', 'fl-builder' ),
						'default' => 'no',
						'preview' => array(
							'type' => 'refresh',
						),
						'options' => array(
							'no'                  => __( 'Inline', 'fl-builder' ),
							'below'               => __( 'Below Row', 'fl-builder' ),
							'yes'                 => __( 'Overlay', 'fl-builder' ),
							'flyout-overlay'      => __( 'Flyout Overlay', 'fl-builder' ),
							'flyout-push'         => __( 'Flyout Push', 'fl-builder' ),
							'flyout-push-opacity' => __( 'Flyout Push with Opacity', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes'                 => array(
								'fields' => array( 'mobile_menu_bg' ),
							),
							'below'               => array(
								'fields' => array( 'mobile_menu_bg' ),
							),
							'flyout-overlay'      => array(
								'fields' => array( 'mobile_menu_bg', 'flyout_position' ),
							),
							'flyout-push'         => array(
								'fields' => array( 'mobile_menu_bg', 'flyout_position' ),
							),
							'flyout-push-opacity' => array(
								'fields' => array( 'mobile_menu_bg', 'flyout_position' ),
							),
						),
					),
					'flyout_position'                 => array(
						'type'    => 'select',
						'label'   => __( 'Flyout Position', 'fl-builder' ),
						'default' => 'left',
						'options' => array(
							'left'  => __( 'Left', 'fl-builder' ),
							'right' => __( 'Right', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'mobile_breakpoint'               => array(
						'type'    => 'select',
						'label'   => __( 'Responsive Breakpoint', 'fl-builder' ),
						'default' => 'mobile',
						'options' => array(
							'always'        => __( 'Always', 'fl-builder' ),
							'medium-mobile' => __( 'Medium &amp; Small Devices Only', 'fl-builder' ),
							'mobile'        => __( 'Small Devices Only', 'fl-builder' ),
						),
					),
					'mobile_stacked'                  => array(
						'type'    => 'select',
						'label'   => __( 'Stacked Layout', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
					),
					'mobile_toggle_submenu_item_icon' => array(
						'type'    => 'button-group',
						'label'   => 'Sub-menu Item Icon',
						'default' => '',
						'options' => array(
							''        => 'None',
							'r_arrow' => 'Arrow',
						),
					),
				),
			),
		),
	),
	'woo_tab' => array( // Section
		'title'    => 'WooCommerce', // Section Title
		'sections' => array( // Tab Sections
			'general_woo' => array(
				'title'  => '',
				'fields' => array( // Section Fields
					'woo_menu_cart'           => array(
						'type'    => 'select',
						'label'   => __( 'Menu Cart', 'fl-builder' ),
						'default' => 'hide',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields'   => array( 'cart_icon', 'show_menu_cart_checkout', 'cart_display_type' ),
								'sections' => array( 'woo_menu_cart_style' ),
							),
						),
					),
					'cart_icon'               => array(
						'type'        => 'icon',
						'label'       => __( 'Cart Icon', 'fl-builder' ),
						'show_remove' => true,
					),
					'show_menu_cart_checkout' => array(
						'type'    => 'select',
						'label'   => __( 'Show on Checkout', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'cart_display_type'       => array(
						'type'    => 'select',
						'label'   => __( 'Display Type', 'fl-builder' ),
						'default' => 'count',
						'options' => array(
							'count'       => __( 'Items Count', 'fl-builder' ),
							'total'       => __( 'Total Amount', 'fl-builder' ),
							'count-total' => __( 'Items Count and Total Amount', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
	'style'   => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general_style'        => array(
				'title'  => __( 'Menu', 'fl-builder' ),
				'fields' => array(
					'menu_align'     => array(
						'type'       => 'align',
						'label'      => __( 'Menu Alignment', 'fl-builder' ),
						'default'    => '',
						'responsive' => true,
					),
					'menu_bg_color'  => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Menu Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.menu',
							'property' => 'background-color',
						),
					),
					'mobile_menu_bg' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Menu Background Color (Mobile)', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
				),
			),
			'text_style'           => array(
				'title'     => __( 'Links', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'link_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '.fl-menu a, .menu > li > a, .menu > li > .fl-has-submenu-container > a, .sub-menu > li > a',
									'property' => 'color',
								),
								array(
									'selector' => '.menu .fl-menu-toggle:before, .menu .fl-menu-toggle:after',
									'property' => 'border-color',
								),
							),
						),
					),
					'link_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-menu a, .menu > li.current-menu-item > a, .menu > li.current-menu-item > .fl-has-submenu-container > a, .sub-menu > li.current-menu-item > a',
							'property' => 'color',
						),
					),
					'link_hover_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.menu > li.current-menu-item > a, .menu > li.current-menu-item > .fl-has-submenu-container > a, .sub-menu > li.current-menu-item > a, .sub-menu > li.current-menu-item > .fl-has-submenu-container > a',
							'property' => 'background-color',
						),
					),
					'link_spacing'        => array(
						'type'    => 'dimension',
						'label'   => __( 'Link Padding', 'fl-builder' ),
						'default' => '14',
						'units'   => array( 'px', 'em' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.menu a',
							'property' => 'padding',
						),
					),
					'typography'          => array(
						'type'       => 'typography',
						'label'      => __( 'Link Typography', 'fl-builder' ),
						'responsive' => array(
							'default'    => array(
								'default' => array(
									'font_size'   => array(
										'length' => '16',
										'unit'   => 'px',
									),
									'line_height' => array(
										'length' => '1',
									),
								),
							),
							'medium'     => array(),
							'responsive' => array(),
						),
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-menu .menu, .fl-menu .menu > li',
							'important' => true,
						),
					),
				),
			),
			'separator_style'      => array(
				'title'     => __( 'Separators', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'show_separator'  => array(
						'type'    => 'select',
						'label'   => __( 'Show Separators', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'separator_color', 'separator_opacity' ),
							),
						),
					),
					'separator_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Separator Color', 'fl-builder' ),
						'default'     => '000000',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.menu.fl-menu-horizontal li, .menu.fl-menu-horizontal li li, .menu.fl-menu-vertical li, .menu.fl-menu-accordion li, .menu.fl-menu-expanded li',
							'property' => 'border-color',
						),
					),
				),
			),
			'submenu_style'        => array(
				'title'     => __( 'Dropdowns', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'submenu_link_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '.fl-menu .sub-menu > li > .fl-has-submenu-container > a, .fl-menu .sub-menu > li > a',
									'property' => 'color',
								),
								array(
									'selector' => '.fl-menu .sub-menu .fl-has-submenu-container .fl-menu-toggle:before, .fl-menu .sub-menu .fl-has-submenu-container .fl-menu-toggle:after',
									'property' => 'border-color',
								),
							),
						),
					),
					'submenu_link_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.sub-menu > li.current-menu-item > .fl-has-submenu-container > a, .sub-menu > li.current-menu-item > a',
							'property' => 'color',
						),
					),
					'submenu_link_hover_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.sub-menu > li.current-menu-item > a, .sub-menu > li.current-menu-item > .fl-has-submenu-container > a',
							'property' => 'background-color',
						),
					),
					'submenu_bg_color'            => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Dropdown Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => 'ffffff',
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-menu .sub-menu',
							'property' => 'background-color',
						),
					),
					'drop_shadow'                 => array(
						'type'    => 'select',
						'label'   => __( 'Dropdown Shadow', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
					),
					'submenu_spacing'             => array(
						'type'    => 'dimension',
						'label'   => __( 'Dropdown Padding', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px', 'em' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => 'ul.sub-menu',
							'property' => 'padding',
						),
					),
					'submenu_link_spacing'        => array(
						'type'    => 'dimension',
						'label'   => __( 'Dropdown Link Padding', 'fl-builder' ),
						'default' => '',
						'units'   => array( 'px', 'em' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => 'ul.sub-menu a',
							'property' => 'padding',
						),
					),
					'submenu_border'              => array(
						'type'       => 'border',
						'label'      => __( 'Dropdown Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-menu .sub-menu',
							'important' => true,
						),
					),
					'submenu_border_hover_color'  => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Dropdown Border Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'submenu_typography'          => array(
						'type'       => 'typography',
						'label'      => __( 'Dropdown Typography', 'fl-builder' ),
						'responsive' => array(
							'default'    => array(
								'default' => array(
									'font_size'   => array(
										'length' => '16',
										'unit'   => 'px',
									),
									'line_height' => array(
										'length' => '1',
									),
								),
							),
							'medium'     => array(),
							'responsive' => array(),
						),
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-menu .sub-menu',
							'important' => true,
						),
					),
				),
			),
			'mobile_submenu_style' => array(
				'title'     => __( 'Responsive Dropdowns', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'mobile_submenu_link_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'mobile_submenu_link_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'mobile_submenu_link_hover_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'mobile_submenu_bg_color'            => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Dropdown Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => '',
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
			'mobile_toggle_style'  => array(
				'title'     => __( 'Responsive Toggle', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'mobile_toggle_size'           => array(
						'type'     => 'unit',
						'label'    => __( 'Size', 'fl-builder' ),
						'default'  => '16',
						'sanitize' => 'floatval',
						'units'    => array( 'px', 'em', 'rem' ),
						'slider'   => true,
						'preview'  => array(
							'type'     => 'css',
							'selector' => '.fl-menu-mobile-toggle',
							'property' => 'font-size',
						),
					),
					'mobile_toggle_bg_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-menu-mobile-toggle',
							'property' => 'background-color',
						),
					),
					'mobile_toggle_hover_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Hover Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'mobile_toggle_color'          => array(
						'label'       => __( 'Color', 'fl-builder' ),
						'type'        => 'color',
						'connections' => array( 'color' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-menu-mobile-toggle',
							'property' => 'color',
						),
					),
					'mobile_toggle_hover_color'    => array(
						'label'       => __( 'Hover Color', 'fl-builder' ),
						'type'        => 'color',
						'connections' => array( 'color' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'mobile_toggle_border'         => array(
						'type'    => 'border',
						'label'   => __( 'Border', 'fl-builder' ),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '.fl-menu-mobile-toggle',
							'important' => true,
						),
					),
				),
			),
			'search_style'         => array(
				'title'     => __( 'Search Menu', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'search_icon_size'            => array(
						'type'       => 'unit',
						'label'      => __( 'Icon Size', 'fl-builder' ),
						'default'    => '16',
						'sanitize'   => 'floatval',
						'responsive' => true,
						'units'      => array( 'px', 'em', 'rem' ),
						'slider'     => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-menu-search-item a.fl-button, .fl-menu-search-item a.fl-button:visited',
							'property' => 'font-size',
						),
					),
					'search_btn_icon_color'       => array(
						'type'       => 'color',
						'default'    => '808080',
						'label'      => __( 'Icon Color', 'fl-builder' ),
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'property'  => 'color',
							'selector'  => 'i.fl-button-icon.fas:before',
							'important' => true,
						),
					),
					'search_btn_icon_color_hover' => array(
						'type'       => 'color',
						'label'      => __( 'Icon Hover Color', 'fl-builder' ),
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
					'form_width'                  => array(
						'type'     => 'unit',
						'label'    => __( 'Form Width', 'fl-builder' ),
						'default'  => '400',
						'sanitize' => 'absint',
						'units'    => array( 'px', '%' ),
						'slider'   => array(
							'min'  => 0,
							'max'  => 1100,
							'step' => 10,
						),
						'preview'  => array(
							'type'     => 'css',
							'selector' => '.fl-menu-search-item .fl-search-form-input-wrap',
							'property' => 'width',
						),
					),
					'search_form_bg_color'        => array(
						'type'        => 'color',
						'label'       => __( 'Form Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-menu-search-item .fl-search-form-input-wrap',
							'property' => 'background-color',
						),
					),
					'search_form_bg_hover_color'  => array(
						'type'        => 'color',
						'label'       => __( 'Form Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'search_form_border'          => array(
						'type'    => 'border',
						'label'   => __( 'Form Border', 'fl-builder' ),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '.fl-menu-search-item .fl-search-form-input-wrap',
							'important' => true,
						),
					),
					'search_form_border_hover'    => array(
						'type'    => 'border',
						'label'   => __( 'Form Border Hover', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'search_form_padding'         => array(
						'type'       => 'dimension',
						'label'      => __( 'Form Padding', 'fl-builder' ),
						'default'    => '10',
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-menu-search-item .fl-search-form-input-wrap',
							'property' => 'padding',
						),
					),
				),
			),
			'woo_menu_cart_style'  => array(
				'title'     => __( 'WooCommerce Menu Cart', 'fl-builder' ),
				'collapsed' => true,
				'fields'    => array(
					'menu_cart_bg_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'li.fl-menu-cart-item',
							'important' => true,
							'property'  => 'background-color',
						),
					),
					'menu_cart_hover_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Hover Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'menu_cart_color'          => array(
						'label'       => __( 'Color', 'fl-builder' ),
						'type'        => 'color',
						'connections' => array( 'color' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => 'li.fl-menu-cart-item, .fl-menu-cart-item > a.fl-menu-cart-contents',
							'property' => 'color',
						),
					),
					'menu_cart_hover_color'    => array(
						'label'       => __( 'Hover Color', 'fl-builder' ),
						'type'        => 'color',
						'connections' => array( 'color' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'menu_cart_typography'     => array(
						'type'    => 'typography',
						'label'   => __( 'Typography', 'fl-builder' ),
						'preview' => array(
							'type'      => 'css',
							'selector'  => 'li.fl-menu-cart-item > a.fl-menu-cart-contents',
							'important' => true,
						),
					),
				),
			),
		),
	),
));


class FL_Menu_Module_Walker extends Walker_Nav_Menu {

	protected $walk_counter = 0;

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$args   = (object) $args;

		$class_names = '';
		$value       = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$submenu = $args->has_children ? ' fl-has-submenu' : '';

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = ' class="' . esc_attr( $class_names ) . $submenu . '"';

		$item_id = apply_filters( 'fl_builder_menu_item_id', 'menu-item-' . $item->ID, $item, $depth );
		$output .= $indent . '<li id="' . $item_id . '"' . $value . $class_names . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

		$item_output  = $args->has_children ? '<div class="fl-has-submenu-container">' : '';
		$item_output .= $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';

		if ( $args->has_children ) {
			$item_output .= '<span class="fl-menu-toggle"></span>';
		}

		$item_output .= $args->after;
		$item_output .= $args->has_children ? '</div>' : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( isset( $args->menu_logo_image_src ) && 0 == (int) $item->menu_item_parent ) {
			$this->walk_counter++;

			$total_menu_items = $args->total_top_lvl_items;
			$logo_position    = ceil( $total_menu_items / 2 );

			if ( 'left' == $args->menu_logo_odd_position && 0 != $total_menu_items % 2 ) {
				$logo_position = $logo_position - 1;
			}

			if ( $this->walk_counter == $logo_position ) {
				$alt     = $args->menu_logo_image_alt ? "alt='$args->menu_logo_image_alt' " : '';
				$output .= "</li><li class='fl-menu-logo'><a href='$args->menu_logo_link' itemprop='url'>";
				$output .= "<img data-no-lazy='1' class='fl-logo-img' src='$args->menu_logo_image_src' $alt/>";
				$output .= '</a></li>';
			} else {
				$output .= '</li>';
			}
		} else {
			$output .= '</li>';
		}
	}

	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
