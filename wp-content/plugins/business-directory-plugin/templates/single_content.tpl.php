<?php
/**
 * Listing detail view rendering template
 *
 * @package BDP/Templates/Single Content
 */

?>
<div class="listing-columns">
<?php if ( $images->main || $images->thumbnail ) : ?>
	<div class="main-image">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $images->main ? $images->main->html : $images->thumbnail->html;
		?>
	</div>
<?php endif; ?>

<div class="listing-details cf<?php echo esc_attr( ( $images->main || $images->thumbnail ) ? '' : ' wpbdp-no-thumb' ); ?>">
    <?php
	foreach ( $fields->not( 'social' ) as $field ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field->html;
	}

	wpbdp_x_part( 'parts/listing-socials' );

	wpbdp_x_part( 'parts/listing-images' );
	?>
</div>
</div>
