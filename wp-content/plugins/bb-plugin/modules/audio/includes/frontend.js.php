<?php if ( is_array( $module->settings->audios ) && count( $module->settings->audios ) > 1 ) : ?>
(function($) {

	$(function(){
		var playlists = $( '.wp-playlist:not(:has(.mejs-container))' );

		if ( playlists.length > 0 ) {
			window.wp.playlist.initialize();
		}
	});

})(jQuery);
<?php endif; ?>
