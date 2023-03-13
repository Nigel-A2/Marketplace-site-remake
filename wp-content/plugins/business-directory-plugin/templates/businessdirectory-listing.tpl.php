<div class="listing-title">
    <h1><?php echo $title; ?></h1>
	<?php echo $is_sticky ? $sticky_tag : ''; ?>
</div>

<?php if ( $actions ) : ?>
    <?php echo $actions; ?>
<?php endif; ?>

<?php if ( $main_image ) : ?>
    <div class="main-image"><?php echo $main_image; ?></div>
<?php endif; ?>

<div class="listing-details cf <?php if ( $main_image ) : ?>with-image<?php endif; ?>">
    <?php echo $listing_fields; ?>
</div>

<?php
wpbdp_x_part(
	'parts/listing-images',
	array(
		'extra_images' => $extra_images,
	)
);
?>
