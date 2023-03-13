<?php
	$schema = $module->get_structured_data();
?>

<div class="fl-video fl-<?php echo ( 'media_library' == $settings->video_type ) ? 'wp' : 'embed'; ?>-video"<?php $schema ? FLBuilder::print_schema( ' itemscope itemtype="https://schema.org/VideoObject"' ) : ''; ?>>
	<?php

	if ( $schema ) {
		echo $schema;
	}

	$module->render_poster_html();
	$module->render_video_html( $schema );

	?>
</div>
