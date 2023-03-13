<?php
$args = array( 'listing' => $listing );

foreach ( $fields as $field ) :
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $field->render( $field_values[ $field->get_id() ], 'submit', $listing );
endforeach;

do_action( 'wpbdp_view_submit_listing-after_fields', $listing );
