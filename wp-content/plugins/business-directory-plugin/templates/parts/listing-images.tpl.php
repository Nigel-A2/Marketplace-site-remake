<?php
/**
 * Extra images template
 *
 * @package BDP/Templates/parts
 */

if ( ! isset( $extra_images ) ) {
	$extra_images = ( isset( $images ) && $images->extra ) ? $images->extra : false;
}

if ( ! $extra_images ) {
	return;
}

?>
<div class="extra-images">
	<ul>
		<?php foreach ( $extra_images as $img ) : ?>
			<li>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $img->html;
				?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
