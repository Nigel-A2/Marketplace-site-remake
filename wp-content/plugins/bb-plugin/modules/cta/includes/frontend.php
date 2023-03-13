<?php global $wp_embed; ?>
<div class="<?php echo $module->get_classname(); ?>">
	<div class="fl-cta-text">
		<<?php echo $settings->title_tag; ?> class="fl-cta-title"><?php echo $settings->title; ?></<?php echo $settings->title_tag; ?>>
		<div class="fl-cta-text-content"><?php echo wpautop( $wp_embed->autoembed( $settings->text ) ); ?></div>
	</div>
	<div class="fl-cta-button">
		<?php $module->render_button(); ?>
	</div>
</div>
