<?php

if ( 'slideshow' == $settings->bg_type ) :

	$source = FLBuilderModel::get_row_slideshow_source( $row );

	if ( ! empty( $source ) ) :

		?>
YUI({'logExclude': { 'yui': true } }).use('fl-slideshow', function(Y) {

	if( null === Y.one('.fl-node-<?php echo $id; ?> .fl-bg-slideshow') ) {
		return;
	}

	var oldSlideshow = Y.one('.fl-node-<?php echo $id; ?> .fl-bg-slideshow .fl-slideshow'),
		newSlideshow = new Y.FL.Slideshow({
			autoPlay            : true,
			bgslideshow         : true,
			crop                : true,
			loadingImageEnabled : false,
			randomize           : <?php echo $settings->ss_randomize; ?>,
			responsiveThreshold : 0,
			touchSupport        : false,
			source              : [{<?php echo $source; ?>}],
			speed               : <?php echo $settings->ss_speed * 1000; ?>,
			stretchy            : true,
			stretchyType        : 'contain',
			transition          : '<?php echo $settings->ss_transition; ?>',
			transitionDuration  : <?php echo ! empty( $settings->ss_transitionDuration ) ? $settings->ss_transitionDuration : 1; // @codingStandardsIgnoreLine ?>
		});

	if(oldSlideshow) {
		oldSlideshow.remove(true);
	}

	jQuery( '.fl-node-<?php echo $id; ?>' ).imagesLoaded( function(){
		newSlideshow.render('.fl-node-<?php echo $id; ?> .fl-bg-slideshow');
	} );
});
		<?php

	endif;

endif;

?>
