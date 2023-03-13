<?php if ( $module->video_aspect_ratio() ) : ?>
	.fl-node-<?php echo $id; ?> .fl-wp-video {
		padding-bottom: <?php echo $module->video_aspect_ratio(); ?>%;
	}
<?php endif; ?>

<?php if ( 'hide' == $settings->play_pause ) : ?>
	.fl-node-<?php echo $id; ?> .mejs-playpause-button {
		display: none !important;
	}	
<?php endif; ?>

<?php if ( 'hide' == $settings->timer ) : ?>
	.fl-node-<?php echo $id; ?> .mejs-currenttime-container {
		display: none !important;
	}		
<?php endif; ?>

<?php if ( 'hide' == $settings->time_rail ) : ?>
	.fl-node-<?php echo $id; ?> .mejs-time-rail {
		display: none !important;
	}		
<?php endif; ?>

<?php if ( 'hide' == $settings->duration ) : ?>
	.fl-node-<?php echo $id; ?> .mejs-duration-container {
		display: none !important;
	}		
<?php endif; ?>

<?php if ( 'hide' == $settings->volume ) : ?>
	.fl-node-<?php echo $id; ?> .mejs-volume-button {
		display: none !important;
	}		
<?php endif; ?>

<?php if ( 'hide' == $settings->full_screen ) : ?>
	.fl-node-<?php echo $id; ?> .mejs-fullscreen-button {
		display: none !important;
	}		
<?php endif; ?>

<?php
	$hide_video_control_bar = ( 'hide' == $settings->play_pause
		&& 'hide' == $settings->timer
		&& 'hide' == $settings->time_rail
		&& 'hide' == $settings->duration
		&& 'hide' == $settings->volume
		&& 'hide' == $settings->full_screen );

	if ( $hide_video_control_bar ) :
		?>
		.fl-node-<?php echo $id; ?> .mejs-controls {
			display: none !important;
		}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .fl-video-poster {
	display: <?php echo ( 'yes' === $settings->video_lightbox ? 'block' : 'none' ); ?>;
}

<?php if ( 'media_library' === $settings->video_type ) : ?>
	.fl-node-<?php echo $id; ?> .fl-wp-video .mejs-overlay-loading {
		display: none;
	}
<?php endif; ?>

<?php
// Click action - lightbox
if ( isset( $settings->video_lightbox ) && 'yes' == $settings->video_lightbox ) :
	?>
	.fl-video-lightbox-wrap .mfp-content {
		background: #fff;
	}
	.fl-video-lightbox-wrap .mfp-iframe-scaler iframe {
		left: 2%;
		height: 94%;
		top: 3%;
		width: 96%;
	}

	.mfp-wrap.fl-video-lightbox-wrap .mfp-close,
	.mfp-wrap.fl-video-lightbox-wrap .mfp-close:hover {
		color: #333!important;
		right: -4px;
		top: -10px!important;
	}
	<?php
endif;
?>
