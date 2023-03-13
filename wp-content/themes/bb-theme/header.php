<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php do_action( 'fl_head_open' ); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php echo apply_filters( 'fl_theme_viewport', "<meta name='viewport' content='width=device-width, initial-scale=1.0' />\n" ); ?>
<?php echo apply_filters( 'fl_theme_xua_compatible', "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n" ); ?>
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php

wp_head();

FLTheme::head();

?>
</head>
<body <?php body_class(); ?><?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/WebPage"' ); ?>>
<?php

FLTheme::header_code();

do_action( 'fl_body_open' );

?>
<div class="fl-page">
	<?php

	do_action( 'fl_page_open' );

	FLTheme::fixed_header();

	do_action( 'fl_before_top_bar' );

	FLTheme::top_bar();

	do_action( 'fl_after_top_bar' );
	do_action( 'fl_before_header' );

	FLTheme::header_layout();

	do_action( 'fl_after_header' );
	do_action( 'fl_before_content' );

	?>
	<div id="fl-main-content" class="fl-page-content" itemprop="mainContentOfPage" role="main">

		<?php do_action( 'fl_content_open' ); ?>
