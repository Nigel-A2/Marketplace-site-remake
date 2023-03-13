<?php

$source = $module->get_source();

if ( ! empty( $source ) ) :

	?>
YUI({'logExclude': { 'yui': true } }).use('fl-slideshow', function(Y){

	if( null === Y.one('.fl-node-<?php echo $id; ?> .fl-slideshow-container') ) {
		return;
	}

	var oldSlideshow = Y.one('.fl-node-<?php echo $id; ?> .fl-slideshow-container .fl-slideshow'),
		newSlideshow = new Y.FL.Slideshow({
			autoPlay: <?php echo $settings->auto_play; ?>,
			<?php if ( 'url' == $settings->click_action ) : ?>
			clickAction: 'url',
			clickActionUrl: '<?php echo $settings->click_action_url; ?>',
			<?php endif; ?>
			color: '<?php echo $settings->color; ?>',
			<?php if ( $settings->crop ) : ?>
			crop: true,
			<?php endif; ?>
			height: <?php echo $settings->height; ?>,
			imageNavEnabled: <?php echo $settings->image_nav; ?>,
			likeButtonEnabled: <?php echo $settings->facebook; ?>,
			<?php if ( 'none' != $settings->nav_type ) : ?>
			navButtons: [<?php $module->get_nav_buttons(); ?>],
			navButtonsLeft: [<?php $module->get_nav_buttons_left(); ?>],
			navButtonsRight: [<?php $module->get_nav_buttons_right(); ?>],
			<?php endif; ?>
			<?php if ( $settings->nav_overlay ) : ?>
			navOverlay: true,
			<?php endif; ?>
			navPosition: '<?php echo $settings->nav_position; ?>',
			navType: '<?php echo $settings->nav_type; ?>',
			<?php if ( $settings->nav_overlay ) : ?>
			overlayHideDelay: <?php echo $settings->overlay_hide_delay * 1000; ?>,
			overlayHideOnMousemove: <?php echo $settings->overlay_hide; ?>,
			<?php endif; ?>
			pinterestButtonEnabled: <?php echo $settings->pinterest; ?>,
			protect: <?php echo $settings->protect; ?>,
			randomize: <?php echo $settings->randomize; ?>,
			<?php if ( $global_settings->responsive_enabled ) : ?>
			responsiveThreshold: <?php echo $global_settings->responsive_breakpoint; ?>,
			<?php endif; ?>
			source: [{<?php echo $source; ?>}],
			speed: <?php echo $settings->speed * 1000; ?>,
			tweetButtonEnabled: <?php echo $settings->twitter; ?>,
			thumbsImageHeight: <?php echo $settings->thumbs_size; ?>,
			thumbsImageWidth: <?php echo $settings->thumbs_size; ?>,
			transition: '<?php echo $settings->transition; ?>',
			transitionDuration: <?php echo $settings->transitionDuration; // @codingStandardsIgnoreLine ?>
		});

	if(oldSlideshow) {
		oldSlideshow.remove(true);
	}

	newSlideshow.render('.fl-node-<?php echo $id; ?> .fl-slideshow-container');

	Y.one('.fl-node-<?php echo $id; ?> .fl-slideshow-container').setStyle( 'height', 'auto' );
});
<?php endif; ?>
