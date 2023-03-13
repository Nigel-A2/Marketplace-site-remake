<<?php echo $settings->tag; ?> class="fl-heading">
	<?php if ( ! empty( $settings->link ) ) : ?>
	<a href="<?php echo $settings->link; ?>" title="<?php echo esc_attr( $settings->heading ); ?>" target="<?php echo $settings->link_target; ?>"<?php echo $module->get_rel(); ?>>
	<?php endif; ?>
	<span class="fl-heading-text"><?php echo $settings->heading; ?></span>
	<?php if ( ! empty( $settings->link ) ) : ?>
	</a>
	<?php endif; ?>
</<?php echo $settings->tag; ?>>
