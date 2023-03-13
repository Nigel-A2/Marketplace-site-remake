<?php

/**
 * The content-wrapper.php template is responsible for rendering everything that goes between
 * get_header() and get_footer() in the theme.
 *
 * @var string $content The contents of the page to render inside of the wrapper
 */
?>
<div class="<?php FLLayout::container_class(); ?>">
	<div class="<?php FLLayout::row_class(); ?>">
		<div class="fl-content <?php FLLayout::content_class( 'bigcommerce' ); ?>">
			<?php echo $content; ?>
		</div>
	</div>
</div>
