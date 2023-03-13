<?php $module->add_fb_root(); ?>
<?php $settings = $module->update( $settings ); ?>
<div class="fl-social-buttons fl-social-buttons-<?php echo $settings->align; ?>">

	<?php if ( $settings->show_facebook ) : ?>
		<div class="fl-social-button fl-fb">
			<div class="fb-like"
					data-href="<?php echo $settings->the_url; ?>"
					data-layout="button_count"
					data-action="like"
					data-show-faces="false"
					data-share="false">
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $settings->show_twitter ) : ?>
		<div class="fl-social-button fl-twitter">
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $settings->the_url; ?>" data-lang="en">Tweet</a>
		</div>
	<?php endif; ?>

	<?php if ( $settings->show_gplus ) : ?>
		<div class="fl-social-button fl-gplus">
			<div class="g-plusone"
					data-annotation="inline"
					data-size="medium"
					data-width="120"
					data-href="<?php echo $settings->the_url; ?>">
			</div>
		</div>
	<?php endif; ?>

</div>
