<?php

$photo    = $module->get_data();
$classes  = $module->get_classes();
$src      = $module->get_src();
$link     = $module->get_link();
$alt      = $module->get_alt();
$attrs    = $module->get_attributes();
$filetype = pathinfo( $src, PATHINFO_EXTENSION );
$rel      = $module->get_rel();
$caption  = $module->get_caption();

?>
<div class="fl-photo<?php echo ( ! empty( $settings->crop ) ) ? ' fl-photo-crop-' . $settings->crop : ''; ?> fl-photo-align-<?php echo $settings->align; ?>"<?php FLBuilder::print_schema( ' itemscope itemtype="https://schema.org/ImageObject"' ); ?>>
	<div class="fl-photo-content fl-photo-img-<?php echo $filetype; ?>">
		<?php if ( ! empty( $link ) ) : ?>
		<a href="<?php echo $link; ?>" target="<?php echo $settings->link_url_target; ?>"<?php echo $rel; ?> itemprop="url">
		<?php endif; ?>
		<img class="<?php echo $classes; ?>" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" itemprop="image" <?php echo $attrs; ?> />
		<?php if ( ! empty( $link ) ) : ?>
		</a>
		<?php endif; ?>
		<?php if ( 'hover' === $settings->show_caption ) : ?>
		<div class="fl-photo-caption fl-photo-caption-hover" itemprop="caption"><?php echo $caption; ?></div>
		<?php endif; ?>
	</div>
	<?php if ( 'below' === $settings->show_caption ) : ?>
	<div class="fl-photo-caption fl-photo-caption-below" itemprop="caption"><?php echo $caption; ?></div>
	<?php endif; ?>
</div>
