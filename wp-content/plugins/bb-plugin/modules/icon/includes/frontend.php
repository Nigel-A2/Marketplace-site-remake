<?php

$text_class = 'fl-icon-text';

if ( empty( $settings->link ) ) {
	$text_class .= ' fl-icon-text-wrap';
}
if ( empty( $settings->text ) ) {
	$text_class .= ' fl-icon-text-empty';
}

$text_link_class = 'fl-icon-text-link';

if ( ! empty( $settings->link ) ) {
	$text_link_class .= ' fl-icon-text-wrap';
}
?>
<?php if ( ! isset( $settings->exclude_wrapper ) || ( isset( $settings->exclude_wrapper ) && ! $settings->exclude_wrapper ) ) : ?>
<div class="fl-icon-wrap">
<?php endif; ?>
	<span class="fl-icon">
		<?php if ( ! empty( $settings->link ) ) : ?>
			<?php if ( ! empty( $settings->text ) ) : ?>
			<a href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>" tabindex="-1" aria-hidden="true" aria-labelledby="fl-icon-text-<?php echo ( isset( $module->node ) ? $module->node : $settings->id ); ?>"<?php echo $module->get_rel(); ?>>
			<?php else : ?>
			<a href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>"<?php echo $module->get_rel(); ?>>
			<?php endif; ?>
		<?php endif; ?>
		<i class="<?php echo $settings->icon; ?>" aria-hidden="true"></i>
		<?php if ( isset( $settings->sr_text ) && '' !== $settings->sr_text ) : ?>
		<span class="sr-only"><?php echo $settings->sr_text; ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $settings->link ) ) : ?>
		</a>
		<?php endif; ?>
	</span>
	<?php if ( ! empty( $settings->text ) ) : ?>
		<div id="fl-icon-text-<?php echo ( isset( $module->node ) ? $module->node : $settings->id ); ?>" class="<?php echo $text_class; ?>">
			<?php if ( ! empty( $settings->link ) ) : ?>
			<a href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>" class="<?php echo $text_link_class; ?>"<?php echo $module->get_rel(); ?>>
			<?php endif; ?>
			<?php echo $settings->text; ?>
			<?php if ( ! empty( $settings->link ) ) : ?>
			</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php if ( ! isset( $settings->exclude_wrapper ) || ( isset( $settings->exclude_wrapper ) && ! $settings->exclude_wrapper ) ) : ?>
</div>
<?php endif; ?>
