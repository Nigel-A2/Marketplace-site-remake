<div class="fl-page-nav-search">
	<a href="#" class="fas fa-search" aria-label="<?php esc_attr_e( 'Search', 'fl-automator' ); ?>" aria-expanded="false" aria-haspopup="true" id='flsearchform'></a>
	<form method="get" role="search" aria-label="<?php esc_attr_e( 'Search', 'fl-automator' ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Type and press Enter to search.', 'fl-automator' ); ?>">
		<input type="search" class="fl-search-input form-control" name="s" placeholder="<?php esc_attr_e( 'Search', 'fl-automator' ); ?>" value="<?php echo get_search_query(); ?>" aria-labelledby="flsearchform" />
	</form>
</div>
