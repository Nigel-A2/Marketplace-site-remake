<?php do_action( 'fl_content_close' ); ?>

	</div><!-- .fl-page-content -->
	<?php

	do_action( 'fl_after_content' );

	if ( FLTheme::has_footer() ) :

		?>
	<footer class="fl-page-footer-wrap"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/WPFooter"' ); ?>  role="contentinfo">
		<?php

		do_action( 'fl_footer_wrap_open' );
		do_action( 'fl_before_footer_widgets' );

		FLTheme::footer_widgets();

		do_action( 'fl_after_footer_widgets' );
		do_action( 'fl_before_footer' );

		FLTheme::footer();

		do_action( 'fl_after_footer' );
		do_action( 'fl_footer_wrap_close' );

		?>
	</footer>
	<?php endif; ?>
	<?php do_action( 'fl_page_close' ); ?>
</div><!-- .fl-page -->
<?php

wp_footer();

do_action( 'fl_body_close' );

FLTheme::footer_code();

?>
</body>
</html>
