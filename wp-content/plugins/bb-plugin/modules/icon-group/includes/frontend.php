<div class="fl-icon-group">
<?php

foreach ( $settings->icons as $icon ) {

	if ( ! is_object( $icon ) ) {
		continue;
	}

	$duo = false;
	if ( false !== strpos( $icon->icon, 'fad fa' ) ) {
		$duo = true;
	}

	$icon_settings = array(
		'bg_color'        => $settings->bg_color,
		'bg_hover_color'  => $settings->bg_hover_color,
		'color'           => ! $duo ? $settings->color : '',
		'exclude_wrapper' => true,
		'hover_color'     => ! $duo ? $settings->hover_color : '',
		'icon'            => $icon->icon,
		'link'            => $icon->link,
		'link_target'     => isset( $icon->link_target ) ? $icon->link_target : '_blank',
		'link_nofollow'   => isset( $icon->link_nofollow ) ? $icon->link_nofollow : 'nofollow',
		'size'            => $settings->size,
		'text'            => false,
		'three_d'         => ! $duo ? $settings->three_d : '',
		'duo_color1'      => $icon->duo_color1,
		'duo_color2'      => $icon->duo_color2,
		'sr_text'         => $icon->sr_text,
	);

	FLBuilder::render_module_html( 'icon', $icon_settings );
}

?>
</div>
