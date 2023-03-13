<?php global $wp_embed; ?>
<div class="fl-accordion fl-accordion-<?php echo $settings->label_size; ?><?php echo ( $settings->collapse ) ? ' fl-accordion-collapse' : ''; ?>" role="tablist"<?php echo ( ! $settings->collapse ) ? ' multiselectable="true"' : ''; ?>>
	<?php
	for ( $i = 0; $i < count( $settings->items ); $i++ ) :
		if ( empty( $settings->items[ $i ] ) ) {
			continue;
		}
		$label_id   = 'fl-accordion-' . $module->node . '-label-' . $i;
		$icon_id    = 'fl-accordion-' . $module->node . '-icon-' . $i;
		$content_id = 'fl-accordion-' . $module->node . '-panel-' . $i;

		$item_tab_class      = ( 0 === $i && '1' === $settings->open_first ) ? 'fl-accordion-item-active' : '';
		$item_tab_icon_class = ( 0 === $i && '1' === $settings->open_first ) ? $settings->label_active_icon : $settings->label_icon;
		?>
	<div class="fl-accordion-item <?php echo $item_tab_class; ?>"<?php echo ( ! empty( $settings->id ) ) ? ' id="' . sanitize_html_class( $settings->id ) . '-' . $i . '"' : ''; ?> >
		<div class="fl-accordion-button"  id="<?php echo 'fl-accordion-' . $module->node . '-tab-' . $i; ?>" aria-selected="false" aria-controls="<?php echo 'fl-accordion-' . $module->node . '-panel-' . $i; ?>" aria-expanded="<?php echo ( $i > 0 || ! $settings->open_first ) ? 'false' : 'true'; ?>" role="tab" tabindex="0">

			<?php if ( 'left' === $settings->label_icon_position ) : ?>
			<i class="fl-accordion-button-icon fl-accordion-button-icon-left <?php echo $item_tab_icon_class; ?>"></i>
			<?php endif; ?>

			<a href="#" id="<?php echo $label_id; ?>" class="fl-accordion-button-label" tabindex="0" aria-controls="<?php echo $content_id; ?>"><?php echo $settings->items[ $i ]->label; ?></a>

			<?php if ( 'right' === $settings->label_icon_position ) : ?>
				<a href="#" id="<?php echo $icon_id; ?>" class="fl-accordion-button-icon" tabindex="0" aria-controls="<?php echo $content_id; ?>"><i class="fl-accordion-button-icon fl-accordion-button-icon-right <?php echo $item_tab_icon_class; ?>"><span class="sr-only"><?php echo ( $i > 0 || ! $settings->open_first ) ? 'Expand' : 'Collapse'; ?></span></i></a>
			<?php endif; ?>

		</div>
		<div class="fl-accordion-content fl-clearfix" id="<?php echo $content_id; ?>" aria-labelledby="<?php echo 'fl-accordion-' . $module->node . '-tab-' . $i; ?>" aria-hidden="<?php echo ( $i > 0 || ! $settings->open_first ) ? 'true' : 'false'; ?>" role="tabpanel" aria-live="polite"><?php echo wpautop( $wp_embed->autoembed( $settings->items[ $i ]->content ) ); ?></div>
	</div>
	<?php endfor; ?>
</div>
