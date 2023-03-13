<div class="fl-rich-text">
	<?php

	global $wp_embed;

	echo wpautop( $wp_embed->autoembed( $settings->text ) );

	?>
</div>
