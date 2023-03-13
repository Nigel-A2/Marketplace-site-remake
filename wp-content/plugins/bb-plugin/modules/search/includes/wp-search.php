<?php
/* Prevent direct access */
defined( 'ABSPATH' ) or die( "You can't access this file directly." );

?>
<form role="search" aria-label="Search form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="fl-form-field">
		<input type="search" aria-label="Search input" class="fl-search-text" placeholder="<?php echo esc_attr( $settings->placeholder ); ?>" value="<?php echo get_search_query(); ?>" name="s" />

		<?php if ( 'ajax' == $settings->result ) : ?>
		<div class="fl-search-loader-wrap">
			<div class="fl-search-loader">
				<svg class="spinner" viewBox="0 0 50 50">
					<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
				</svg>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php if ( 'ajax' == $settings->result ) : ?>
	<div class="fl-search-results-content"></div>
	<?php endif; ?>
</form>
