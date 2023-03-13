<?php
$link_target = ' target="' . esc_attr( true === $settings['fl-social-link-new-tab'] ? '_blank' : '_self' ) . '"';
$link_rel    = ' rel="noopener noreferrer"';
if ( 'custom' === $settings['fl-social-icons-color'] ) {
	?>
	<div class="fl-social-icons-stacked">
	<?php
	$shape = $settings['fl-social-icons-bg-shape'];
	$size  = $settings['fl-social-icons-size'];
	foreach ( $icons as $icon ) {

		if ( ! empty( $settings[ 'fl-social-' . $icon ] ) ) {
			$setting            = $settings[ 'fl-social-' . $icon ];
			$icon_screen_reader = '<span class="sr-only">' . ucfirst( $icon ) . '</span>';
			if ( 'email' === $icon ) {
				$setting     = 'mailto:' . $setting;
				$link_target = '';
				$icon        = 'envelope';
			}
			$pre  = ( 'envelope' === $icon || 'rss' === $icon || 'google-maps' === $icon ) ? 'fas' : 'fab';
			$icon = ( 'facebook' === $icon ) ? 'facebook-f' : $icon;
			$icon = ( 'google-maps' === $icon ) ? 'map-marker-alt' : $icon;

			printf( '<a href="%s" class="fa-stack fa-%sx icon-%s"%s>%s
			<i aria-hidden="true" class="fas fa-%s fa-stack-2x"></i>
			<i aria-hidden="true" class="%s fa-%s fa-stack-1x fa-inverse"></i>
			</a>',
				$setting,
				$size,
				$icon,
				$link_target . $link_rel,
				$icon_screen_reader,
				$shape,
				$pre,
				$icon
			);
		}
	}
} else {
	?>
	<div class="fl-social-icons">
	<?php
	foreach ( $icons as $icon ) {


		if ( ! empty( $settings[ 'fl-social-' . $icon ] ) ) {

			$setting            = $settings[ 'fl-social-' . $icon ];
			$icon_screen_reader = '<span class="sr-only">' . ucfirst( $icon ) . '</span>';
			if ( 'email' === $icon ) {
				$setting     = 'mailto:' . $setting;
				$link_target = '';
				$icon        = 'envelope';
			}

			$pre  = ( 'envelope' === $icon || 'rss' === $icon || 'google-maps' === $icon ) ? 'fas' : 'fab';
			$icon = ( 'facebook' === $icon ) ? 'facebook-f' : $icon;
			$icon = ( 'google-maps' === $icon ) ? 'map-marker-alt' : $icon;

			if ( ! $circle ) {
					printf( '<a href="%s"%s>%s<i aria-hidden="true" class="%s fa-%s %s"></i></a>', $setting, $link_target . $link_rel, $icon_screen_reader, $pre, $icon, $settings['fl-social-icons-color'] );
			} else {
					printf( '<a href="%s" class="fa-stack icon-%s"%s>%s
					<i aria-hidden="true" class="fas fa-circle fa-stack-2x %s"></i>
					<i aria-hidden="true" class="%s fa-%s %s fa-stack-1x fa-inverse"></i>
					</a>',
						$setting,
						( 'map-marker-alt' === $icon ) ? 'google-maps' : $icon,
						$link_target . $link_rel,
						$icon_screen_reader,
						$settings['fl-social-icons-color'],
						$pre,
						$icon,
						$settings['fl-social-icons-color']
					);
			}
		}
	}
}

?>
</div>
