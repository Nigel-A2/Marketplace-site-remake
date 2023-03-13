<?php

if ( 'none' !== $layout ) {

	if ( '1-col' === $layout ) {
		echo '<div class="' . FLLayout::get_col_classes( array( 'md' => 12 ) ) . ' text-center clearfix">';
	} else {
		echo '<div class="' . FLLayout::get_col_classes( array( 'sm' => 6, 'md' => 6 ) ) . ' text-left clearfix">'; // @codingStandardsIgnoreLine
	}

	do_action( 'fl_footer_col1_open' );

	if ( 'text' === $col_layout || 'social-text' === $col_layout ) {
		if ( empty( $col_text ) ) {
			get_template_part( 'includes/copyright' );
		} else {
			echo '<div class="fl-page-footer-text fl-page-footer-text-1">' . do_shortcode( $col_text ) . '</div>';
		}
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

	do_action( 'fl_footer_col1_close' );

	echo '</div>';
}
