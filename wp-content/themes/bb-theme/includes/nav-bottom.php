<header class="fl-page-header fl-page-header-primary<?php FLTheme::header_classes(); ?>"<?php FLTheme::header_data_attrs(); ?><?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/WPHeader"' ); ?> role="banner">
	<div class="fl-page-header-wrap">
		<div class="fl-page-header-container <?php FLLayout::container_class(); ?>">
			<div class="fl-page-header-row <?php FLLayout::row_class(); ?>">
				<div class="<?php FLLayout::col_classes( array( 'sm' => 6, 'md' => 6 ) ); // @codingStandardsIgnoreLine ?> fl-page-header-logo-col">
					<div class="fl-page-header-logo"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/Organization"' ); ?>>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url"><?php FLTheme::logo(); ?></a>
						<?php echo FLTheme::get_tagline(); ?>
					</div>
				</div>
				<div class="<?php FLLayout::col_classes( array( 'sm' => 6, 'md' => 6 ) ); // @codingStandardsIgnoreLine ?> fl-page-nav-col">
					<div class="fl-page-header-content">
						<?php FLTheme::header_content(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="fl-page-nav-wrap">
		<div class="fl-page-nav-container <?php FLLayout::container_class(); ?>">
			<nav class="fl-page-nav navbar navbar-default navbar-expand-md" aria-label="<?php echo esc_attr( FLTheme::get_nav_locations( 'header' ) ); ?>"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"' ); ?> role="navigation">
				<button type="button" class="navbar-toggle navbar-toggler" data-toggle="collapse" data-target=".fl-page-nav-collapse">
					<span><?php FLTheme::nav_toggle_text(); ?></span>
				</button>
				<div class="fl-page-nav-collapse collapse navbar-collapse">
					<?php

					wp_nav_menu(array(
						'theme_location' => 'header',
						'items_wrap'     => '<ul id="%1$s" class="nav navbar-nav %2$s">%3$s</ul>',
						'container'      => false,
						'fallback_cb'    => 'FLTheme::nav_menu_fallback',
						'menu_class'     => 'menu fl-theme-menu',
					));

					FLTheme::nav_search();

					?>
				</div>
			</nav>
		</div>
	</div>
</header><!-- .fl-page-header -->
