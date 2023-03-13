<?php // @codingStandardsIgnoreFile

$bg_video_wrapper_classes = implode( ' ', apply_filters( 'fl_row_bg_video_wrapper_class', array( 'fl-bg-video' ), $row ) );

if ( 'wordpress' == $row->settings->bg_video_source ) :

		$bg_video_data_video_mobile	= isset( $row->settings->bg_video_mobile ) ? $row->settings->bg_video_mobile : 'no';
		$bg_video_data_mp4			= isset( $vid_data['mp4']->url ) ?  $vid_data['mp4']->url : '';
		$bg_video_data_mp4_type		= isset( $vid_data['mp4']->extension) ? 'video/mp4' : '';
		$bg_video_data_webm			= isset( $vid_data['webm']->url ) ?  $vid_data['webm']->url : '';
		$bg_video_data_webm_type	= isset( $vid_data['webm']->extension ) ? 'video/webm' : '';

		$bg_video_data_width		= '';
		if ( !empty( $vid_data['mp4']->width ) ){
			$bg_video_data_width	= $vid_data['mp4']->width;
		}
		if ( !empty( $vid_data['webm']->width ) ){
			$bg_video_data_width	= $vid_data['webm']->width;
		}


		$bg_video_data_height		= "";
		if ( !empty( $vid_data['mp4']->height ) ){
			$bg_video_data_height	= $vid_data['mp4']->height;
		}
		if ( !empty( $vid_data['webm']->height ) ){
			$bg_video_data_height	= $vid_data['webm']->height;
		}


		$bg_video_data_fallback		= "";
		if ( !empty( $vid_data['mp4']->fallback ) ){
			$bg_video_data_fallback	= $vid_data['mp4']->fallback;
		}
		if ( !empty( $vid_data['webm']->fallback ) ){
			$bg_video_data_fallback	= $vid_data['webm']->fallback;
		}

		// Also Check 'data-enable-audio' attribute
		$data_mobile_attr		= !empty( $bg_video_data_video_mobile ) ? ('data-video-mobile="' . $bg_video_data_video_mobile) . '"': '' ;
		$data_mp4_attr 			= !empty( $bg_video_data_mp4 ) ? ( 'data-mp4="' . $bg_video_data_mp4 . '"' ) : '' ;
		$data_mp4_type_attr 	= !empty( $bg_video_data_mp4_type ) ? ( 'data-mp4-type="' . $bg_video_data_mp4_type . '"' ) : '' ;

		$data_webm_attr 		= !empty( $bg_video_data_webm ) ? ( 'data-webm="' . $bg_video_data_webm . '"' ) : '' ;
		$data_webm_type_attr 	= !empty( $bg_video_data_webm_type ) ? ( 'data-webm-type="' . $bg_video_data_webm_type . '"' ) : '' ;

		$data_width_attr 		= !empty( $bg_video_data_width ) ? ( 'data-width="' . $bg_video_data_width . '"' ) : '' ;
		$data_height_attr 		= !empty( $bg_video_data_height ) ? ( 'data-height="' . $bg_video_data_height . '"' ) : '' ;
		$data_fallback_attr 	= !empty( $bg_video_data_fallback ) ? ( 'data-fallback="' . $bg_video_data_fallback . '"' ) : '' ;


		if ( !empty ($bg_video_data_mp4) || !empty($bg_video_data_webm) ):
			?>
			<div class="<?php echo $bg_video_wrapper_classes; ?>" <?php echo "$data_mobile_attr $data_width_attr $data_height_attr $data_fallback_attr $data_mp4_attr $data_mp4_type_attr $data_webm_attr $data_webm_type_attr"; ?> >
			</div>
			<?php
		endif;

	endif
?>

<?php if ( 'video_url' == $row->settings->bg_video_source ) { ?>
<div class="<?php echo $bg_video_wrapper_classes; ?>"
data-video-mobile="<?php if ( isset( $row->settings->bg_video_mobile ) ) { echo $row->settings->bg_video_mobile;} ?>"
data-fallback="<?php if ( isset( $row->settings->bg_video_fallback_src ) ) { echo $row->settings->bg_video_fallback_src;} ?>"
<?php if ( isset( $row->settings->bg_video_url_mp4 ) ) : ?>
data-mp4="<?php echo $row->settings->bg_video_url_mp4; ?>"
data-mp4-type="video/mp4"
<?php endif; ?>
<?php if ( isset( $row->settings->bg_video_url_webm ) ) : ?>
data-webm="<?php echo $row->settings->bg_video_url_webm; ?>"
data-webm-type="video/webm"
<?php endif; ?>></div>
<?php } ?>

<?php if ( 'video_service' == $row->settings->bg_video_source ) {
	$video_data = FLBuilderUtils::get_video_data( do_shortcode( $row->settings->bg_video_service_url ) ); ?>
<div class="<?php echo $bg_video_wrapper_classes; ?>"
data-fallback="<?php if ( isset( $row->settings->bg_video_fallback_src ) ) { echo $row->settings->bg_video_fallback_src;} ?>"
<?php if ( isset( $row->settings->bg_video_service_url ) && isset( $video_data['type'] ) ) : ?>
data-<?php echo $video_data['type']; ?>="<?php echo do_shortcode( $row->settings->bg_video_service_url );  ?>"
data-video-id="<?php echo $video_data['video_id']; ?>"
data-enable-audio="<?php echo $row->settings->bg_video_audio; ?>"
data-video-mobile="<?php if ( isset( $row->settings->bg_video_mobile ) ) { echo $row->settings->bg_video_mobile;} ?>"
<?php if ( isset( $video_data['params'] ) ) : ?>
	<?php foreach ( $video_data['params'] as $key => $val ) : ?>
		data-<?php echo $key . '="' . $val . '"'; ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>>
<div class="fl-bg-video-player"></div>
<?php if ( $row->settings->bg_video_audio ) : ?>
<div class="fl-bg-video-audio"><span>
	<i class="fas fl-audio-control fa-volume-off"></i>
	<i class="fas fa-times"></i>
</span></div>
<?php endif; ?>
</div>
<?php } ?>
