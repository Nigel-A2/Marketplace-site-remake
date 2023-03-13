<?php
// Display playlist if user selected more than one audio files
if ( ( 'media_library' == $settings->audio_type ) && ( is_array( $settings->audios ) && count( $settings->audios ) > 1 ) ) {

	$playlist_settings  = ( isset( $settings->style ) && $settings->style ) ? ' style="' . $settings->style . '"' : '';
	$playlist_settings .= isset( $settings->tracklist ) ? ' tracklist="' . $settings->tracklist . '"' : '';
	$playlist_settings .= isset( $settings->tracknumbers ) ? ' tracknumbers="' . $settings->tracknumbers . '"' : '';
	$playlist_settings .= isset( $settings->images ) ? ' images="' . $settings->images . '"' : '';
	$playlist_settings .= isset( $settings->artists ) ? ' artists="' . $settings->artists . '"' : '';
	?>
	<div class="fl-audio fl-wp-audio">
		<?php echo '[playlist ids="' . implode( ',', $settings->audios ) . '"' . $playlist_settings . ']'; ?>
	</div>

	<?php

} else {

	?>
	<div class="fl-audio fl-wp-audio"<?php FLBuilder::print_schema( ' itemscope itemtype="https://schema.org/AudioObject"' ); ?>>
		<?php

			$audio_data = $module->get_data();

			$loop     = isset( $settings->loop ) && $settings->loop ? ' loop="yes"' : '';
			$autoplay = isset( $settings->autoplay ) && $settings->autoplay ? ' autoplay="yes"' : '';

		if ( 'media_library' == $settings->audio_type && $audio_data ) {
			$audio_url = $audio_data->url;
		} else {
			$audio_url = $settings->link;
		}

			echo '<meta itemprop="url" content="' . $audio_url . '" />';
			echo '[audio src="' . preg_replace( '/\/?\?.*/', '', $audio_url ) . '"' . $autoplay . $loop . ']';
		?>

	</div>
	<?php
}

if ( FLBuilderModel::is_builder_active() ) {
	wp_underscore_playlist_templates();
}
