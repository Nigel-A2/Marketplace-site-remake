<?php

$menu_classes = 'fl-menu';

if ( $settings->collapse && 'accordion' == $settings->menu_layout ) {
	$menu_classes .= ' fl-menu-accordion-collapse';
}
if ( $settings->mobile_breakpoint && 'expanded' != $settings->mobile_toggle ) {
	$menu_classes .= ' fl-menu-responsive-toggle-' . $settings->mobile_breakpoint;
}
if ( $module->is_responsive_menu_flyout() ) {
	$menu_classes .= ' fl-menu-responsive-' . $settings->mobile_full_width;
	$menu_classes .= ' fl-flyout-' . $settings->flyout_position;
}
if ( isset( $settings->menu_search ) && 'show' == $settings->menu_search ) {
	$menu_classes .= ' fl-menu-search-enabled';
}

?>
<div class="<?php echo $menu_classes; ?>">
	<?php $module->render_toggle_button(); ?>
	<div class="fl-clear"></div>
	<?php

	if ( ! empty( $settings->menu ) ) {

		if ( isset( $settings->menu_layout ) ) {
			if ( in_array( $settings->menu_layout, array( 'vertical', 'horizontal' ) ) && isset( $settings->submenu_hover_toggle ) ) {
				$toggle = ' fl-toggle-' . $settings->submenu_hover_toggle;
			} elseif ( 'accordion' == $settings->menu_layout && isset( $settings->submenu_click_toggle ) ) {
				$toggle = ' fl-toggle-' . $settings->submenu_click_toggle;
			} else {
				$toggle = ' fl-toggle-arrows';
			}
		} else {
			$toggle = ' fl-toggle-arrows';
		}

		$layout = isset( $settings->menu_layout ) ? 'fl-menu-' . $settings->menu_layout : 'fl-menu-horizontal';

		printf( '<nav aria-label="%s"%s>', esc_attr( $module->get_menu_label() ), FLBuilder::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"', false ) );

		$defaults = array(
			'menu'                => $settings->menu,
			'container'           => false,
			'menu_class'          => 'menu ' . $layout . $toggle,
			'walker'              => new FL_Menu_Module_Walker(),
			'item_spacing'        => 'discard',
			'total_top_lvl_items' => $module->get_total_top_lvl_items(),
		);

		if ( 'horizontal' == $settings->menu_layout && ! empty( $settings->menu_logo_image ) ) {
			$defaults = array_merge( array(
				'menu_logo_image_src'    => $settings->menu_logo_image_src,
				'menu_logo_odd_position' => $settings->menu_logo_odd_position,
				'menu_logo_link'         => esc_url( home_url( '/' ) ),
				'menu_logo_image_alt'    => get_post_meta( $settings->menu_logo_image, '_wp_attachment_image_alt', true ),
			), $defaults );
		}

		do_action( 'fl_builder_menu_module_before_render', $defaults, $settings );

		add_filter( 'wp_nav_menu_' . $settings->menu . '_items', array( $module, 'filter_nav_menu_items' ), 10 );

		add_filter( 'wp_nav_menu_objects', 'FLMenuModule::sort_nav_objects', 10, 2 );
		wp_nav_menu( $defaults );
		remove_filter( 'wp_nav_menu_objects', 'FLMenuModule::sort_nav_objects' );

		remove_filter( 'wp_nav_menu_' . $settings->menu . '_items', array( $module, 'filter_nav_menu_items' ), 10 );

		do_action( 'fl_builder_menu_module_after_render', $defaults, $settings );
		echo '</nav>';
	} else {
		printf( '<nav aria-label="%s"%s>', esc_attr( $module->get_menu_label() ), FLBuilder::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"', false ) );
		?>
		<ul class="menu fl-menu-horizontal">
			<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home">
				<?php printf( '<a href="%s">%s</a>', esc_url( home_url( '/' ) ), __( 'Home', 'fl-builder' ) ); ?>
			</li>
			<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
			<li class="menu-item menu-item-type-custom menu-item-object-custom">
				<?php printf( '<a href="%s" target="_blank">%s</a>', admin_url( 'nav-menus.php' ), __( 'Add a menu', 'fl-builder' ) ); ?>
			</li>
			<?php endif; ?>
		</ul>
		<?php
		echo '</nav>';
	}
	?>
</div>
