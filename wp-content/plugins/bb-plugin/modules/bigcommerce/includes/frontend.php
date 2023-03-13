<?php

$attr = array(
	'paged'    => $settings->paged,
	'featured' => 0,
	'sale'     => 0,
	'recent'   => 0,
);

if ( $settings->paged ) {
	$attr['per_page'] = (string) $settings->per_page;
}

if ( $settings->featured ) {
	$attr['featured'] = '1';
}
if ( $settings->sale ) {
	$attr['sale'] = '1';
}
if ( $settings->recent ) {
	$attr['recent'] = '1';
}

$shortcode = 'bigcommerce_product';
foreach ( $attr as $key => $value ) {
	$shortcode .= sprintf( ' %s="%s"', $key, $value );
}

echo sprintf( '[%s]', $shortcode );


