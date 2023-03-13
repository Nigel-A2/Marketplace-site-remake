<header class="fl-page-header fl-page-header-fixed fl-page-nav-right fl-page-nav-toggle-<?php echo FLTheme::get_setting( 'fl-mobile-nav-toggle' ); ?> fl-page-nav-toggle-visible-<?php echo FLTheme::get_setting( 'fl-nav-breakpoint' ); ?>"  role="banner">
	<div class="fl-page-header-wrap">
		<div class="fl-page-header-container <?php FLLayout::container_class(); ?>">
			<div class="fl-page-header-row <?php FLLayout::row_class(); ?>">
				<div class="<?php FLLayout::col_classes( array( 'sm' => 12, 'md' => 3 ) ); // @codingStandardsIgnoreLine ?> fl-page-logo-wrap">
					<div class="fl-page-header-logo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php FLTheme::fixed_header_logo(); ?></a>
					</div>
				</div>
				<div class="<?php FLLayout::col_classes( array( 'sm' => 12, 'md' => 9 ) ); // @codingStandardsIgnoreLine ?> fl-page-fixed-nav-wrap">
					<div class="fl-page-nav-wrap">
						<nav class="fl-page-nav fl-nav navbar navbar-default navbar-expand-md" aria-label="<?php echo esc_attr( FLTheme::get_nav_locations( 'header' ) ); ?>" role="navigation">
							<button type="button" class="navbar-toggle navbar-toggler" data-toggle="collapse" data-target=".fl-page-nav-collapse">
								<span><?php FLTheme::nav_toggle_text(); ?></span>
							</button>
							<div class="fl-page-nav-collapse collapse navbar-collapse">
								<?php

								wp_nav_menu(array(
									'theme_location' => 'header',
									'items_wrap'     => '<ul id="%1$s" class="nav navbar-nav navbar-right %2$s">%3$s</ul>',
									'container'      => false,
									'fallback_cb'    => 'FLTheme::nav_menu_fallback',
									'menu_class'     => 'menu fl-theme-menu',
								));

								?>
							</div>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
</header><!-- .fl-page-header-fixed -->
