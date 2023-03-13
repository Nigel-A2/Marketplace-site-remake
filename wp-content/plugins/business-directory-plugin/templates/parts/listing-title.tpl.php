<?php
/**
 * Listing title
 *
 * @package BDP/Templates/parts
 */
?>
<?php if ( $title_type !== 'h1' ) : ?>
	<div class="listing-title">
		<<?php echo esc_attr( $title_type ); ?>><?php echo esc_html( $title ); ?></<?php echo esc_attr( $title_type ); ?>>
<?php endif; ?>
<?php if ( in_array( 'single', wpbdp_get_option( 'display-sticky-badge' ), true ) ) : ?>
	<?php echo $sticky_tag; ?>
<?php endif; ?>
<?php if ( $title_type !== 'h1' ) : ?>
	</div>
<?php endif; ?>
