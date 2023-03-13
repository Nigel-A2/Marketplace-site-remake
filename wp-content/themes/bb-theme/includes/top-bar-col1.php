<?php

if ( 'none' !== $layout ) {

	if ( '1-col' === $layout ) {
		echo '<div class="' . FLLayout::get_col_classes( array( 'md' => 12 ) ) . ' text-center clearfix">';
	} else {
		echo '<div class="' . FLLayout::get_col_classes( array( 'sm' => 6, 'md' => 6 ) ) . ' text-left clearfix">'; // @codingStandardsIgnoreLine
	}

	do_action( 'fl_top_bar_col1_open' );

	if ( 'social' === $col_layout || 'text-social' === $col_layout || 'menu-social' === $col_layout ) {
		self::social_icons( false );
	}
	if ( 'text' === $col_layout || 'text-social' === $col_layout ) {
		echo '<div class="fl-page-bar-text fl-page-bar-text-1">' . do_shortcode( $col_text ) . '</div>';
	}
	if ( 'menu' === $col_layout || 'menu-social' === $col_layout ) {
		?>
		<nav class="top-bar-nav" aria-label="<?php echo esc_attr( FLTheme::get_nav_locations( 'bar' ) ); ?>"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"' ); ?>
			role="navigation">
			<?php
			wp_nav_menu(array(
				'theme_location' => 'bar',
				'items_wrap'     => '<ul id="%1$s" class="fl-page-bar-nav nav navbar-nav %2$s">%3$s</ul>',
				'container'      => false,
				'fallback_cb'    => 'FLTheme::nav_menu_fallback',
			));
			echo '</nav>';
	}

	do_action( 'fl_top_bar_col1_close' );

	echo '</div>';
}
