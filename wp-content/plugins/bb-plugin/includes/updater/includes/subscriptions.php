<hr />
<?php
// first check we have a download for the current version.
$plugin_data = get_plugin_data( FL_BUILDER_FILE );
$plugin_name = $plugin_data['Name'];

if ( '{FL_BUILDER_NAME}' !== $plugin_data['Name'] && ! in_array( $plugin_name, $subscription->downloads, true ) ) {

	$show_warning = false;
	$version      = '';

	// find available plugin Version
	foreach ( $subscription->downloads as $ver ) {
		if ( stristr( $ver, 'Beaver Builder Plugin' ) ) {
			preg_match( '#\((.*)\sVersion\)$#', $ver, $match );
			$version = ( isset( $match[1] ) ) ? $match[1] : false;
			break;
		}
	}

	switch ( $plugin_data['Name'] ) {
		// pro - show warning if standard is pnly available version
		case 'Beaver Builder Plugin (Pro Version)':
			$show_warning = ( 'Standard' === $version ) ? true : false;
			break;
		// agency show warning if available is NOT agency
		case 'Beaver Builder Plugin (Agency Version)':
			$show_warning = ( 'Agency' !== $version ) ? true : false;
			break;
	}

	if ( ! $version ) {
		$show_warning = true;
	}

	if ( $show_warning ) {
		$header_txt = __( 'Beaver Builder updates issue!!', 'fl-builder' );
		// translators: %s: Product name
		$txt = sprintf( __( 'Updates for Beaver Builder will not work as you appear to have %s activated but it is not in your available downloads.', 'fl-builder' ), '<strong>' . $plugin_name . '</strong>' );
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong></p><p>%s</p></div>',
			$header_txt,
			$txt
		);
	}
}
?>
<h3><?php _e( 'Available Downloads', 'fl-builder' ); ?></h3>
<p><?php _e( 'The following downloads are currently available for remote update with the subscription(s) associated with this license.', 'fl-builder' ); ?></p>
<ul>
	<?php
	foreach ( $subscription->downloads as $download ) {

		if ( stristr( $download, 'child theme' ) ) {
			continue;
		}
		echo '<li>' . $download . '</li>';
	}
	?>
</ul>
