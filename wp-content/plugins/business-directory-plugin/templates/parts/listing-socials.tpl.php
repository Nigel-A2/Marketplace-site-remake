<?php
/**
 * Social list template
 *
 * @package BDP/Templates/parts
 */

$social_fields = $fields->filter( 'social' );
if ( ! $social_fields ) {
	return;
}

$html = $social_fields->html;
if ( ! empty( $html ) ) {
	?>
	<div class="social-fields cf">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $html;
		?>
	</div>
	<?php
}
