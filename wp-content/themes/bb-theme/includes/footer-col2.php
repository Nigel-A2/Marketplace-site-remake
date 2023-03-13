<?php

if ( '2-cols' === $layout ) {

	echo '<div class="' . FLLayout::get_col_classes( array( 'sm' => 6, 'md' => 6 ) ) . ' text-right clearfix">'; // @codingStandardsIgnoreLine

	do_action( 'fl_footer_col2_open' );

	if ( 'text' === $col_layout || 'social-text' === $col_layout ) {
		echo '<div class="fl-page-footer-text fl-page-footer-text-2">' . do_shortcode( $col_text ) . '</div>';
	}
	if ( 'social' === $col_layout || 'social-text' === $col_layout ) {
		self::social_icons();
	}
	if ( 'menu' === $col_layout ) {
		wp_nav_menu(array(
			'theme_location' => 'footer',
			'items_wrap'     => '<ul id="%1$s" class="fl-page-footer-nav nav navbar-nav %2$s">%3$s</ul>',
			'container'      => false,
			'fallback_cb'    => 'FLTheme::nav_menu_fallback',
		));
	}

	do_action( 'fl_footer_col2_close' );

	echo '</div>';
}
