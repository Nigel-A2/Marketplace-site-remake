<?php

$toggle_spacing       = $settings->link_spacing_right > 10 ? $settings->link_spacing_right : 10;
$toggle_padding       = ! empty( $settings->link_spacing_right ) ? $settings->link_spacing_right : 0;
$toggle_width         = ( $toggle_padding + 14 );
$toggle_height        = ceil( ( ( $toggle_padding * 2 ) + 14 ) * 0.65 );
$submenu_selector     = ".fl-node-$id .fl-menu .sub-menu";
$submenu_container    = $submenu_selector . ' .fl-has-submenu-container';
$sub_hover_selectors  = "$submenu_selector > li > a:hover,";
$sub_hover_selectors .= "$submenu_selector > li > a:focus,";
$sub_hover_selectors .= "$submenu_selector > li > .fl-has-submenu-container:hover > a,";
$sub_hover_selectors .= "$submenu_selector > li > .fl-has-submenu-container:focus > a,";
$sub_hover_selectors .= "$submenu_selector > li.current-menu-item > a,";
$sub_hover_selectors .= "$submenu_selector > li.current-menu-item > .fl-has-submenu-container > a";

/**
 * Overall menu styling
 */
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'typography',
	'selector'     => ".fl-node-$id .fl-menu .menu, .fl-node-$id .fl-menu .menu > li",
) );

?>
.fl-node-<?php echo $id; ?> .fl-menu .menu {
	<?php

	if ( ! empty( $settings->menu_bg_color ) ) {
		echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->menu_bg_color ) . ';';
	}

	?>
}
<?php

/**
 * Overall menu alignment (horizontal only)
 */
if ( 'horizontal' === $settings->menu_layout ) {

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'menu_align',
		'selector'     => ".fl-node-$id .fl-menu",
		'prop'         => 'text-align',
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'menu_align',
		'selector'     => ".fl-node-$id .fl-menu .menu",
		'prop'         => 'float',
		'ignore'       => array( 'center' ),
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'menu_align',
		'selector'     => ".fl-node-$id .fl-menu .menu",
		'props'        => array(
			'float'          => 'none',
			'display'        => 'inline-block',
			'vertical-align' => 'top',
		),
		'ignore'       => array( 'left', 'right' ),
	) );
}


/**
 * Links
 */
?>
.fl-node-<?php echo $id; ?> .menu a{
	padding-left: <?php echo ! empty( $settings->link_spacing_left ) ? $settings->link_spacing_left . $settings->link_spacing_unit : '0'; ?>;
	padding-right: <?php echo ! empty( $settings->link_spacing_right ) ? $settings->link_spacing_right . $settings->link_spacing_unit : '0'; ?>;
	padding-top: <?php echo ! empty( $settings->link_spacing_top ) ? $settings->link_spacing_top . $settings->link_spacing_unit : '0'; ?>;
	padding-bottom: <?php echo ! empty( $settings->link_spacing_bottom ) ? $settings->link_spacing_bottom . $settings->link_spacing_unit : '0'; ?>;
}

<?php if ( ! empty( $settings->link_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .menu > li > a,
.fl-builder-content .fl-node-<?php echo $id; ?> .menu > li > .fl-has-submenu-container > a,
.fl-builder-content .fl-node-<?php echo $id; ?> .sub-menu > li > a,
.fl-builder-content .fl-node-<?php echo $id; ?> .sub-menu > li > .fl-has-submenu-container > a{
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
	<?php if ( ! empty( $settings->link_bg_color ) ) : ?>
		background-color: #<?php echo $settings->link_bg_color; ?>;
	<?php endif; ?>
}

	<?php if ( isset( $settings->link_color ) ) : ?>

		<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none .fl-menu-toggle:before {
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
		}
	<?php elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-menu-toggle:after{
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
		}
		<?php endif; ?>
	<?php endif; ?>

	<?php
endif;

/**
 * Links - hover / active
 */
if ( ! empty( $settings->link_hover_bg_color ) || $settings->link_hover_color ) :
	?>
.fl-node-<?php echo $id; ?> .menu > li > a:hover,
.fl-node-<?php echo $id; ?> .menu > li > a:focus,
.fl-node-<?php echo $id; ?> .menu > li > .fl-has-submenu-container:hover > a,
.fl-node-<?php echo $id; ?> .menu > li > .fl-has-submenu-container.focus > a,
.fl-node-<?php echo $id; ?> .menu > li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .menu > li.current-menu-item > .fl-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .sub-menu > li > .fl-has-submenu-container:hover > a,
.fl-node-<?php echo $id; ?> .sub-menu > li > .fl-has-submenu-container.focus > a,
.fl-node-<?php echo $id; ?> .sub-menu > li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .sub-menu > li.current-menu-item > .fl-has-submenu-container > a{
	<?php
	if ( ! empty( $settings->link_hover_bg_color ) ) {
		echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_bg_color ) . ';';
	}
	if ( ! empty( $settings->link_hover_color ) ) {
		echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_color ) . ';';
	}
	?>
}
<?php endif ?>

<?php if ( ! empty( $settings->link_hover_color ) ) : ?>
		<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows .fl-has-submenu-container:hover > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows .fl-has-submenu-container.focus > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows li.current-menu-item >.fl-has-submenu-container > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none .fl-has-submenu-container:hover > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none .fl-has-submenu-container.focus > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none li.current-menu-item >.fl-has-submenu-container > .fl-menu-toggle:before{
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_hover_color ); ?>;
		}
	<?php elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container:hover > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container.focus > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus li.current-menu-item > .fl-has-submenu-container > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container:hover > .fl-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container.focus > .fl-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus li.current-menu-item > .fl-has-submenu-container > .fl-menu-toggle:after{
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_hover_color ); ?>;
		}
	<?php endif; ?>

	<?php
endif;

/**
 * Overall submenu styling
 */
if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) :
	?>
	.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .sub-menu{
		display: none;
	}
	<?php
endif;

if ( ! empty( $settings->submenu_bg_color ) || 'yes' == $settings->drop_shadow ) :
	?>
.fl-node-<?php echo $id; ?> .fl-menu .sub-menu {
	<?php

	if ( ! empty( $settings->submenu_bg_color ) ) {
		echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->submenu_bg_color ) . ';';
	}
	if ( 'yes' == $settings->drop_shadow ) {
		echo '-webkit-box-shadow: 0 1px 20px rgba(0,0,0,0.1);';
		echo '-ms-box-shadow: 0 1px 20px rgba(0,0,0,0.1);';
		echo 'box-shadow: 0 1px 20px rgba(0,0,0,0.1);';
	}

	?>
}
	<?php
endif;

/**
 * Submenu links
 */
if ( isset( $settings->submenu_link_color ) && ! empty( $settings->submenu_link_color ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu .sub-menu > li > a,
	.fl-node-<?php echo $id; ?> .fl-menu .sub-menu > li > .fl-has-submenu-container > a {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->submenu_link_color ); ?>;
	}

	<?php
	FLBuilderCSS::rule( array(
		'selector' => "$submenu_container .fl-menu-toggle:before, $submenu_selector .fl-toggle-plus .fl-menu-toggle:after",
		'props'    => array(
			'border-color' => $settings->submenu_link_color,
		),
	) );

endif;

if ( isset( $settings->submenu_link_hover_color ) && ( ! empty( $settings->submenu_link_hover_color ) || ! empty( $settings->submenu_link_hover_bg_color ) ) ) :

	FLBuilderCSS::rule( array(
		'selector' => $sub_hover_selectors,
		'enabled'  => ! empty( $settings->submenu_link_hover_bg_color ),
		'props'    => array(
			'background-color' => $settings->submenu_link_hover_bg_color,
		),
	) );
	FLBuilderCSS::rule( array(
		'selector' => $sub_hover_selectors,
		'enabled'  => ! empty( $settings->submenu_link_hover_color ),
		'props'    => array(
			'color' => $settings->submenu_link_hover_color,
		),
	) );

	// Submenu icons
	FLBuilderCSS::rule( array(
		'selector' => "$submenu_container:hover > .fl-menu-toggle:before, $submenu_container:focus > .fl-menu-toggle:before, $submenu_selector .fl-toggle-plus fl-has-submenu-container:hover > .fl-menu-toggle:after, $submenu_selector .fl-toggle-plus fl-has-submenu-container:focus > .fl-menu-toggle:after",
		'props'    => array(
			'border-color' => $settings->submenu_link_hover_color,
		),
	) );
endif;

if ( isset( $settings->submenu_border ) && ! empty( $settings->submenu_border ) ) :
	// Border - Settings
	FLBuilderCSS::border_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'submenu_border',
		'selector'     => "$submenu_selector",
	) );

	if ( is_array( $settings->submenu_border ) && isset( $settings->submenu_border['radius'] ) ) :
		$border_radius = $settings->submenu_border['radius'];
		foreach ( $border_radius as $pos => $radius ) {
			if ( (int) $radius > 1 ) {
				$border_radius[ $pos ] = floor( $radius / 2 );
			}
		}

		if ( array_filter( $border_radius ) ) :
			FLBuilderCSS::rule( array(
				'selector' => "$submenu_selector li:first-child a",
				'props'    => array(
					'-moz-border-radius-topleft'      => $border_radius['top_left'] . 'px',
					'-moz-border-radius-topright'     => $border_radius['top_right'] . 'px',
					'-webkit-border-top-left-radius'  => $border_radius['top_left'] . 'px',
					'-webkit-border-top-right-radius' => $border_radius['top_right'] . 'px',
					'border-top-left-radius'          => $border_radius['top_left'] . 'px',
					'border-top-right-radius'         => $border_radius['top_right'] . 'px',
				),
			) );

			FLBuilderCSS::rule( array(
				'selector' => "$submenu_selector li:last-child a",
				'props'    => array(
					'-moz-border-radius-bottomleft'      => $border_radius['bottom_left'] . 'px',
					'-moz-border-radius-bottomright'     => $border_radius['bottom_right'] . 'px',
					'-webkit-border-bottom-left-radius'  => $border_radius['bottom_left'] . 'px',
					'-webkit-border-bottom-right-radius' => $border_radius['bottom_right'] . 'px',
					'border-bottom-left-radius'          => $border_radius['bottom_left'] . 'px',
					'border-bottom-right-radius'         => $border_radius['bottom_right'] . 'px',
				),
			) );
		endif;
	endif;

	// Border - Hover Settings
	if ( ! empty( $settings->submenu_border_hover_color ) && is_array( $settings->submenu_border ) ) :
		$settings->submenu_border['color'] = $settings->submenu_border_hover_color;
	endif;

	FLBuilderCSS::border_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'submenu_border',
		'selector'     => "$submenu_selector:hover",
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'submenu_typography',
		'selector'     => "$submenu_selector",
	) );
endif;

/**
 * Toggle - Arrows / None
 */
if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'arrows' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:before{
		content: '';
		position: absolute;
		right: 50%;
		top: 50%;
		z-index: 1;
		display: block;
		width: 9px;
		height: 9px;
		margin: -5px -5px 0 0;
		border-right: 2px solid;
		border-bottom: 2px solid;
		-webkit-transform-origin: right bottom;
			-ms-transform-origin: right bottom;
				transform-origin: right bottom;
		-webkit-transform: translateX( -5px ) rotate( 45deg );
			-ms-transform: translateX( -5px ) rotate( 45deg );
				transform: translateX( -5px ) rotate( 45deg );
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.fl-active > .fl-has-submenu-container .fl-menu-toggle{
		-webkit-transform: rotate( -180deg );
			-ms-transform: rotate( -180deg );
				transform: rotate( -180deg );
	}
	<?php

	/**
	 * Toggle - Plus
	 */
elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:after{
		content: '';
		position: absolute;
		z-index: 1;
		display: block;
		border-color: #333;
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:before{
		left: 50%;
		top: 50%;
		width: 12px;
		border-top: 3px solid;
		-webkit-transform: translate( -50%, -50% );
			-ms-transform: translate( -50%, -50% );
				transform: translate( -50%, -50% );
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:after{
		left: 50%;
		top: 50%;
		border-left: 3px solid;
		height: 12px;
		-webkit-transform: translate( -50%, -50% );
			-ms-transform: translate( -50%, -50% );
				transform: translate( -50%, -50% );
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.fl-active > .fl-has-submenu-container .fl-menu-toggle:after{
		display: none;
	}
	<?php
endif;

/**
 * Submenu toggle
 */
if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-arrows .fl-has-submenu-container a{
		padding-right: <?php echo $toggle_width; ?>px;
	}
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-arrows .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-none .fl-menu-toggle{
		width: <?php echo $toggle_height; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
	.fl-node-<?php echo $id; ?> .fl-menu-horizontal.fl-toggle-arrows .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-horizontal.fl-toggle-none .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-vertical.fl-toggle-arrows .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-vertical.fl-toggle-none .fl-menu-toggle{
		width: <?php echo $toggle_width; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
<?php elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-plus .fl-has-submenu-container a{
		padding-right: <?php echo $toggle_width; ?>px;
	}

	.fl-node-<?php echo $id; ?> .fl-menu-accordion.fl-toggle-plus .fl-menu-toggle{
		width: <?php echo $toggle_height; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
	.fl-node-<?php echo $id; ?> .fl-menu-horizontal.fl-toggle-plus .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-vertical.fl-toggle-plus .fl-menu-toggle{
		width: <?php echo $toggle_width; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
	<?php
endif;

/**
 * Separators
 */
?>
.fl-node-<?php echo $id; ?> .fl-menu li{
	border-top: 1px solid transparent;
}
.fl-node-<?php echo $id; ?> .fl-menu li:first-child{
	border-top: none;
}
<?php if ( isset( $settings->show_separator ) && 'yes' == $settings->show_separator && ! empty( $settings->separator_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .menu.fl-menu-<?php echo $settings->menu_layout; ?> li,
	.fl-node-<?php echo $id; ?> .menu.fl-menu-horizontal li li{
		border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->separator_color ); ?>;
	}
	<?php
endif;

/**
 * Responsive Layout
 */
if ( 'always' != $module->get_media_breakpoint() ) :
	?>
	@media ( max-width: <?php echo $module->get_media_breakpoint(); ?>px ) {
<?php endif; ?>

	<?php if ( $module->is_responsive_menu_flyout() ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout {
			<?php if ( ! empty( $settings->mobile_menu_bg ) ) : ?>
				background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->mobile_menu_bg ); ?>;
			<?php else : ?>
				background-color: #fff;
			<?php endif; ?>

			<?php if ( 'right' == $settings->flyout_position ) : ?>
				right: -267px;
			<?php elseif ( 'left' == $settings->flyout_position ) : ?>
				left: -267px;
			<?php endif; ?>
			height: 0px;
			overflow-y: auto;
			padding: 0 5px;
			position: fixed;
			top: 0;
			transition-property: left, right;
			transition-duration: .2s;
			-moz-box-shadow: 0 0 4px #4e3c3c;
			-webkit-box-shadow: 0 0 4px #4e3c3c;
			box-shadow: 0 0 4px #4e3c3c;
			z-index: 999999;
			width: 250px;
		}
		.fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout ul {
			margin: 0 auto;
		}
		.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-mobile-flyout .menu {
			display: block !important;
			float: none;
		}
		.admin-bar .fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout {
			top: 32px;
		}

		<?php if ( 'flyout-push-opacity' == $settings->mobile_full_width ) : ?>
		.fl-menu-mobile-opacity {
			display: none;
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(0,0,0,0.4);
			z-index: 100;
			cursor: pointer;
		}
		<?php endif; ?>

		.fl-menu-mobile-close {
			display: block;
		}
		.fl-flyout-right .fl-menu-mobile-close {
			float: left;
		}
		.fl-flyout-left .fl-menu-mobile-close {
			float: right;
		}

	<?php endif; ?>

	<?php if ( ( isset( $settings->mobile_full_width ) && 'no' != $settings->mobile_full_width ) && ( isset( $settings->mobile_toggle ) && 'expanded' != $settings->mobile_toggle ) ) : ?>

		<?php if ( 'yes' == $settings->mobile_full_width ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .menu {
				position: absolute;
				left: <?php echo empty( $settings->margin_left ) ? $global_settings->module_margins : $settings->margin_left; ?>px;
				right: <?php echo empty( $settings->margin_right ) ? $global_settings->module_margins : $settings->margin_right; ?>px;
				z-index: 1500;
			}
		<?php endif; ?>

		<?php if ( ! empty( $settings->mobile_menu_bg ) ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .menu {
				background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->mobile_menu_bg ); ?>;
			}
		<?php endif; ?>

	<?php endif; ?>

	<?php if ( 'expanded' != $settings->mobile_toggle ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu ul.menu {
			display: none;

			<?php if ( 'horizontal' == $settings->menu_layout ) : ?>
			float: none;
			<?php endif; ?>
		}
	<?php endif; ?>

	<?php
	if ( 'horizontal' == $settings->menu_layout && isset( $settings->mobile_toggle ) && 'expanded' === $settings->mobile_toggle && isset( $settings->mobile_stacked ) && 'no' == $settings->mobile_stacked ) :
		?>
		.fl-node-<?php echo $id; ?> .fl-menu .menu > li{ display: inline-block; }

		.fl-node-<?php echo $id; ?> .menu li {
			border-left: 1px solid transparent;
			border-top: none;
		}

		.fl-node-<?php echo $id; ?> .menu li.fl-active > .sub-menu {
			display: block;
			visibility: visible;
			opacity: 1;
		}

		.fl-node-<?php echo $id; ?> .menu li:first-child{
			border: none;
		}
		.fl-node-<?php echo $id; ?> .menu li li{
			border-top: 1px solid transparent;
			border-left: none;
		}

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .sub-menu{
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 10;
			visibility: hidden;
			opacity: 0;
			text-align:left;
		}

		.fl-node-<?php echo $id; ?> .fl-has-submenu .fl-has-submenu .sub-menu{
			top: 0;
			left: 100%;
		}

		<?php
	else :
		?>
	.fl-node-<?php echo $id; ?> .fl-menu .sub-menu {
		-webkit-box-shadow: none;
		-ms-box-shadow: none;
		box-shadow: none;
	}
		<?php
	endif;

	?>
	<?php
	if ( 'medium-mobile' == $settings->mobile_breakpoint ) {
		$media = 'medium';
	} elseif ( 'mobile' == $settings->mobile_breakpoint ) {
		$media = 'responsive';
	} else {
		$media = 'default';
	}

	// Indent sub-menu on responsive view.
	if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) :
		FLBuilderCSS::rule( array(
			'media'    => $media,
			'selector' => ".fl-node-$id .menu li.fl-has-submenu ul.sub-menu",
			'props'    => array(
				'padding-left' => '15px',
			),
		) );
	endif;

	$flyout_selector  = "body.fl-builder-edit .fl-node-$id .fl-menu-responsive-flyout-overlay nav,";
	$flyout_selector .= "body.fl-builder-edit .fl-node-$id .fl-menu-responsive-flyout-push nav,";
	$flyout_selector .= "body.fl-builder-edit .fl-node-$id .fl-menu-responsive-flyout-push-opacity nav";
	FLBuilderCSS::rule( array(
		'media'    => $media,
		'enabled'  => FLBuilderModel::is_builder_active(),
		'selector' => $flyout_selector,
		'props'    => array(
			'display' => 'none',
		),
	) );

	FLBuilderCSS::rule( array(
		'media'    => $media,
		'selector' => $submenu_selector,
		'props'    => array(
			'background-color' => ! empty( $settings->mobile_submenu_bg_color ) ? $settings->mobile_submenu_bg_color : 'transparent',
		),
	) );

	FLBuilderCSS::rule( array(
		'media'    => $media,
		'enabled'  => ! empty( $settings->mobile_submenu_link_color ),
		'selector' => "$submenu_selector > li > a, $submenu_selector > li > .fl-has-submenu-container > a",
		'props'    => array(
			'color' => $settings->mobile_submenu_link_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'media'    => $media,
		'enabled'  => ! empty( $settings->mobile_submenu_link_color ),
		'selector' => "$submenu_container .fl-menu-toggle:before, $submenu_selector .fl-toggle-plus .fl-menu-toggle:after",
		'props'    => array(
			'border-color' => $settings->mobile_submenu_link_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'media'    => $media,
		'enabled'  => ! empty( $settings->mobile_submenu_link_hover_color ),
		'selector' => $sub_hover_selectors,
		'props'    => array(
			'color' => $settings->mobile_submenu_link_hover_color,
		),
	) );
	FLBuilderCSS::rule( array(
		'media'    => $media,
		'enabled'  => ! empty( $settings->mobile_submenu_link_hover_bg_color ),
		'selector' => $sub_hover_selectors,
		'props'    => array(
			'background-color' => $settings->mobile_submenu_link_hover_bg_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'media'    => $media,
		'enabled'  => ! empty( $settings->mobile_submenu_link_hover_color ),
		'selector' => "$submenu_container:hover > .fl-menu-toggle:before, $submenu_container:focus > .fl-menu-toggle:before, $submenu_selector .fl-toggle-plus fl-has-submenu-container:hover > .fl-menu-toggle:after, $submenu_selector .fl-toggle-plus fl-has-submenu-container:focus > .fl-menu-toggle:after",
		'props'    => array(
			'border-color' => $settings->mobile_submenu_link_hover_color,
		),
	) );
	?>

	.fl-node-<?php echo $id; ?> .mega-menu.fl-active .hide-heading > .sub-menu,
	.fl-node-<?php echo $id; ?> .mega-menu-disabled.fl-active .hide-heading > .sub-menu {
		display: block !important;
	}

	.fl-node-<?php echo $id; ?> .fl-menu-logo,
	.fl-node-<?php echo $id; ?> .fl-menu-search-item {
		display: none;
	}

<?php if ( ! empty( $settings->mobile_toggle_submenu_item_icon ) && 'r_arrow' === $settings->mobile_toggle_submenu_item_icon ) : ?>
	.fl-node-<?php echo $id; ?> .sub-menu .menu-item a::before {
		font-family: 'Font Awesome 5 <?php echo FLBuilder::fa5_pro_enabled() ? 'Pro' : 'Free'; ?>';
		content: '\f105';
		font-weight: 900;
		margin-right: 10px;
	}
<?php endif; ?>
<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
	} <?php // close media max-width ?>

	<?php if ( $module->is_responsive_menu_flyout() ) : ?>
		@media ( max-width: 782px ) {
			.admin-bar .fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout {
				top: 46px;
			}
		}
	<?php endif; ?>
<?php endif; ?>

<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
@media ( min-width: <?php echo ( $module->get_media_breakpoint() ) + 1; ?>px ) {

	<?php // if menu is horizontal ?>
	<?php if ( 'horizontal' == $settings->menu_layout ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .menu > li{ display: inline-block; }

		.fl-node-<?php echo $id; ?> .menu li{
			border-left: 1px solid transparent;
			border-top: none;
		}

		.fl-node-<?php echo $id; ?> .menu li:first-child{
			border: none;
		}
		.fl-node-<?php echo $id; ?> .menu li li{
			border-top: 1px solid transparent;
			border-left: none;
		}

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .sub-menu{
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 10;
			visibility: hidden;
			opacity: 0;
			text-align:left;
		}

		.fl-node-<?php echo $id; ?> .fl-has-submenu .fl-has-submenu .sub-menu{
			top: 0;
			left: 100%;
		}

		<?php // if menu is vertical ?>
	<?php elseif ( 'vertical' == $settings->menu_layout ) : ?>

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .sub-menu{
			position: absolute;
			top: 0;
			left: 100%;
			z-index: 10;
			visibility: hidden;
			opacity: 0;
		}

	<?php endif; ?>

	<?php // if menu is horizontal or vertical ?>
	<?php if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) : ?>

		.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu:hover > .sub-menu,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.focus > .sub-menu{
			display: block;
			visibility: visible;
			opacity: 1;
		}

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu.fl-menu-submenu-right .sub-menu{
			left: inherit;
			right: 0;
		}

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .fl-has-submenu.fl-menu-submenu-right .sub-menu{
			top: 0;
			left: inherit;
			right: 100%;
		}

		.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.fl-active > .fl-has-submenu-container .fl-menu-toggle{
			-webkit-transform: none;
				-ms-transform: none;
					transform: none;
		}

		<?php //change selector depending on layout ?>
		<?php if ( 'arrows' == $settings->submenu_hover_toggle ) : ?>
			<?php if ( 'horizontal' == $settings->menu_layout ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu .fl-has-submenu .fl-menu-toggle:before{
			<?php elseif ( 'vertical' == $settings->menu_layout ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu .fl-menu-toggle:before{
			<?php endif; ?>
				-webkit-transform: translateY( -5px ) rotate( -45deg );
					-ms-transform: translateY( -5px ) rotate( -45deg );
						transform: translateY( -5px ) rotate( -45deg );
			}
		<?php endif; ?>

		<?php if ( 'none' == $settings->submenu_hover_toggle ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle{
				display: none;
			}
		<?php endif; ?>

		.fl-node-<?php echo $id; ?> ul.sub-menu {
			<?php if ( '' !== $settings->submenu_spacing_top ) : ?>
			padding-top: <?php echo $settings->submenu_spacing_top . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_spacing_right ) : ?>
			padding-right: <?php echo $settings->submenu_spacing_right . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_spacing_bottom ) : ?>
			padding-bottom: <?php echo $settings->submenu_spacing_bottom . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_spacing_left ) : ?>
			padding-left: <?php echo $settings->submenu_spacing_left . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
		}

		.fl-node-<?php echo $id; ?> ul.sub-menu a {
			<?php if ( '' !== $settings->submenu_link_spacing_top ) : ?>
			padding-top: <?php echo $settings->submenu_link_spacing_top . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_link_spacing_right ) : ?>
			padding-right: <?php echo $settings->submenu_link_spacing_right . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_link_spacing_bottom ) : ?>
			padding-bottom: <?php echo $settings->submenu_link_spacing_bottom . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_link_spacing_left ) : ?>
			padding-left: <?php echo $settings->submenu_link_spacing_left . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
		}

	<?php endif; ?>

	<?php if ( 'expanded' != $settings->mobile_toggle ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle{
			display: none;
		}
	<?php endif; ?>
}
<?php endif; ?>

<?php
/**
 * Mobile toggle button
 */
if ( isset( $settings->mobile_toggle ) && 'expanded' != $settings->mobile_toggle ) :
	?>
	<?php if ( 'horizontal' == $settings->menu_layout && ! empty( $settings->menu_align ) ) : ?>
		<?php
		FLBuilderCSS::responsive_rule( array(
			'settings'     => $settings,
			'setting_name' => 'menu_align',
			'selector'     => ".fl-node-$id .fl-menu-mobile-toggle",
			'prop'         => 'float',
			'ignore'       => array( 'center' ),
		) );

		FLBuilderCSS::responsive_rule( array(
			'settings'     => $settings,
			'setting_name' => 'menu_align',
			'selector'     => ".fl-node-$id .fl-menu-mobile-toggle",
			'props'        => array(
				'float' => 'none',
			),
			'ignore'       => array( 'left', 'right' ),
		) );

		?>
	<?php endif; ?>

	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle{
		<?php

		if ( ! empty( $settings->mobile_toggle_color ) ) {
			echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->mobile_toggle_color ) . ';';
		} elseif ( ! empty( $settings->link_color ) ) {
			echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->link_color ) . ';';
		}

		if ( ! empty( $settings->mobile_toggle_bg_color ) ) {
			echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->mobile_toggle_bg_color ) . ';';
		} elseif ( ! empty( $settings->menu_bg_color ) ) {
			echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->menu_bg_color ) . ';';
		}

		$toggle_size      = 16;
		$toggle_size_unit = 'px';
		if ( ! empty( $settings->mobile_toggle_size ) ) {
			$toggle_size = $settings->mobile_toggle_size;

			if ( ! empty( $settings->mobile_toggle_size_unit ) ) {
				$toggle_size_unit = $settings->mobile_toggle_size_unit;
			}
		} elseif ( ! empty( $settings->typography['font_size']['length'] ) ) {
			$toggle_size = $settings->typography['font_size']['length'];

			if ( ! empty( $settings->typography['font_size']['unit'] ) ) {
				$toggle_size_unit = $settings->typography['font_size']['unit'];
			}
		}

		?>
		font-size: <?php echo $toggle_size . $toggle_size_unit; ?>;
		text-transform: <?php echo ( ! empty( $settings->typography['text_transform'] ) ? $settings->typography['text_transform'] : 'none' ); ?>;
		padding-left: <?php echo ! empty( $settings->link_spacing_left ) ? $settings->link_spacing_left . $settings->link_spacing_unit : '0'; ?>;
		padding-right: <?php echo ! empty( $settings->link_spacing_right ) ? $settings->link_spacing_right . $settings->link_spacing_unit : '0'; ?>;
		padding-top: <?php echo ! empty( $settings->link_spacing_top ) ? $settings->link_spacing_top . $settings->link_spacing_unit : '0'; ?>;
		padding-bottom: <?php echo ! empty( $settings->link_spacing_bottom ) ? $settings->link_spacing_bottom . $settings->link_spacing_unit : '0'; ?>;
		border-color: rgba( 0,0,0,0.1 );
	}
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle rect{
		<?php
		if ( ! empty( $settings->mobile_toggle_color ) ) {
			echo 'fill: ' . FLBuilderColor::hex_or_rgb( $settings->mobile_toggle_color ) . ';';
		} elseif ( ! empty( $settings->link_color ) ) {
			echo 'fill: ' . FLBuilderColor::hex_or_rgb( $settings->link_color ) . ';';
		}
		?>
	}
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle:hover,
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle.fl-active{
		<?php
		if ( ! empty( $settings->mobile_toggle_hover_color ) ) {
			echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->mobile_toggle_hover_color ) . ';';
		} elseif ( ! empty( $settings->link_hover_color ) ) {
			echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_color ) . ';';
		}
		if ( ! empty( $settings->mobile_toggle_hover_bg_color ) ) {
			echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->mobile_toggle_hover_bg_color ) . ';';
		} elseif ( ! empty( $settings->link_hover_bg_color ) ) {
			echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_bg_color ) . ';';
		}
		?>
	}

	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle:hover rect,
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle.fl-active rect{
		<?php
		if ( ! empty( $settings->mobile_toggle_hover_color ) ) {
			echo 'fill: ' . FLBuilderColor::hex_or_rgb( $settings->mobile_toggle_hover_color ) . ';';
		} elseif ( ! empty( $settings->link_hover_color ) ) {
			echo 'fill: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_color ) . ';';
		}
		?>
	}
	<?php
	if ( ! empty( $settings->mobile_toggle_border ) ) :
		FLBuilderCSS::border_field_rule( array(
			'settings'     => $settings,
			'setting_name' => 'mobile_toggle_border',
			'selector'     => ".fl-node-$id .fl-menu-mobile-toggle",
		) );
	endif;

endif;

if ( isset( $settings->mobile_button_label ) && 'no' == $settings->mobile_button_label ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-mobile-toggle.hamburger .fl-menu-mobile-toggle-label{
		display: none;
	}
	<?php
endif;

/**
 * Mega menus
 */
?>
.fl-node-<?php echo $id; ?> ul.fl-menu-horizontal li.mega-menu > ul.sub-menu > li > .fl-has-submenu-container a:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
}
<?php
/**
 * Centered + Inline Logo
 */
if ( 'horizontal' == $settings->menu_layout && ! empty( $settings->menu_logo_image ) ) :
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .menu li.fl-menu-logo a, .fl-node-$id .menu li.fl-menu-logo a:hover",
		'props'    => array(
			'background'     => 'none',
			'padding-top'    => '0px',
			'padding-bottom' => '0px',
		),
	) );
endif;

/**
 * Woo Menu Cart
 */
if ( class_exists( 'WooCommerce' ) && isset( $settings->woo_menu_cart ) && 'show' == $settings->woo_menu_cart ) :
	if ( ! empty( $settings->cart_icon ) ) :
		$cart_padding_right = ! empty( $settings->link_spacing_right ) ? (int) $settings->link_spacing_right - 4 : 10;
		$cart_padding_unit  = ! empty( $settings->link_spacing_unit ) ? $settings->link_spacing_unit : 'px';

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id li.fl-menu-cart-item .fl-menu-cart-icon",
			'props'    => array(
				'padding-right' => $cart_padding_right . $cart_padding_unit,
			),
		) );
	endif;

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-menu li.fl-menu-cart-item-hidden",
		'enabled'  => ! empty( $settings->show_menu_cart_checkout ) && 'no' == $settings->show_menu_cart_checkout,
		'props'    => array(
			'display' => 'none',
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id li.fl-menu-cart-item > a.fl-menu-cart-contents",
		'enabled'  => ! empty( $settings->menu_cart_bg_color ),
		'props'    => array(
			'background-color' => $settings->menu_cart_bg_color,
		),
	) );
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id li.fl-menu-cart-item:hover > a.fl-menu-cart-contents",
		'enabled'  => ! empty( $settings->menu_cart_hover_bg_color ),
		'props'    => array(
			'background-color' => $settings->menu_cart_hover_bg_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id li.fl-menu-cart-item > a.fl-menu-cart-contents",
		'enabled'  => ! empty( $settings->menu_cart_color ),
		'props'    => array(
			'color' => $settings->menu_cart_color,
		),
	) );
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id li.fl-menu-cart-item:hover > a.fl-menu-cart-contents",
		'enabled'  => ! empty( $settings->menu_cart_hover_color ),
		'props'    => array(
			'color' => $settings->menu_cart_hover_color,
		),
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'menu_cart_typography',
		'selector'     => ".fl-node-$id li.fl-menu-cart-item > a.fl-menu-cart-contents",
	) );
endif;

/**
 * Search Menu
 */
if ( isset( $settings->menu_search ) && 'show' == $settings->menu_search ) :

	if ( isset( $settings->show_separator ) && 'yes' == $settings->show_separator && ! empty( $settings->separator_color ) ) {
		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id li.fl-menu-search-item",
			'enabled'  => 'horizontal' == $settings->menu_layout,
			'props'    => array(
				'border-left' => 'none',
			),
		) );

		FLBuilderCSS::rule( array(
			'selector' => ".fl-node-$id .fl-menu-search-enabled .menu-item:nth-last-child(2)",
			'enabled'  => 'horizontal' == $settings->menu_layout,
			'props'    => array(
				'border-right' => '1px solid ' . FLBuilderColor::hex_or_rgb( $settings->separator_color ),
			),
		) );
	}

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-menu-search-item a.fl-button, .fl-node-$id .fl-menu-search-item a.fl-button:hover",
		'props'    => array(
			'background' => 'none',
			'border'     => '0 none',
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-menu-search-item a.fl-button, .fl-node-$id .fl-menu-search-item a.fl-button:visited",
		'enabled'  => empty( $settings->search_icon_size ),
		'props'    => array(
			'font-size' => '16px',
		),
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'search_icon_size',
		'selector'     => ".fl-node-$id .fl-menu-search-item a.fl-button, .fl-node-$id .fl-menu-search-item a.fl-button:visited",
		'prop'         => 'font-size',
		'enabled'      => ! empty( $settings->search_icon_size ),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-menu-search-item .fl-search-form-wrap",
		'props'    => array(
			'padding' => '0px',
		),
	) );
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-menu-search-item .fl-form-field",
		'props'    => array(
			'margin' => '0px',
		),
	) );

	if ( 'horizontal' === $settings->menu_layout ) {
		$position        = is_rtl() ? 'left' : 'right';
		$search_position = array(
			"$position" => '0',
		);
	} else {
		$position        = is_rtl() ? 'right' : 'left';
		$search_position = array(
			"$position" => '0',
		);
	}

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-menu-search-item .fl-search-form-input-wrap",
		'props'    => array_merge( array(
			'display'  => 'none',
			'position' => 'absolute',
			'z-index'  => '10',
			'width'    => array(
				'value' => $settings->form_width,
				'unit'  => $settings->form_width_unit,
			),
			'top'      => '100%',
		), $search_position ),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-menu-search-item .fl-search-form-fields",
		'props'    => array(
			'display'         => 'flex',
			'flex-direction'  => 'row-reverse',
			'justify-content' => is_rtl() ? 'right' : 'left',
		),
	) );

	$form_selector_wrap = ".fl-node-$id .fl-search-form-input-wrap";

	// Default form styles
	FLBuilderCSS::rule( array(
		'selector' => $form_selector_wrap,
		'props'    => array(
			'padding' => '10px',
		),
	) );

	// Form background color
	FLBuilderCSS::rule( array(
		'selector' => $form_selector_wrap,
		'props'    => array(
			'background-color' => $settings->search_form_bg_color,
		),
	) );

	// Form hover background
	FLBuilderCSS::rule( array(
		'selector' => $form_selector_wrap . ':hover',
		'props'    => array(
			'background-color' => $settings->search_form_bg_hover_color,
		),
	) );

	// Form Border - Settings
	FLBuilderCSS::border_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'search_form_border',
		'selector'     => $form_selector_wrap,
	) );

	// Form Border - Hover Settings
	if ( ! empty( $settings->search_form_border_hover ) && is_array( $settings->search_form_border ) ) {
		$settings->search_form_border['color'] = $settings->search_form_border_hover;
	}

	FLBuilderCSS::border_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'search_form_border_hover',
		'selector'     => $form_selector_wrap . ':hover',
	) );

	// Form padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'search_form_padding',
		'selector'     => $form_selector_wrap,
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'search_form_padding_top',
			'padding-right'  => 'search_form_padding_right',
			'padding-bottom' => 'search_form_padding_bottom',
			'padding-left'   => 'search_form_padding_left',
		),
	) );

	FLBuilder::render_module_css( 'search', $id, $module->menu_search_settings() );
endif;
