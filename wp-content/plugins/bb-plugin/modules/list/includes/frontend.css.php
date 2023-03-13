<?php
// Heading Style - Color
if ( ! empty( $settings->heading_color ) ) : ?>
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-list-item-heading-text {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->heading_color ); ?>;
	}
	<?php
endif;

// Heading Style - Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'heading_typography',
	'selector'     => ".fl-node-$id.fl-module-list .fl-list-item-heading",
) );

// Content Style - Color
if ( ! empty( $settings->content_color ) ) :
	?>
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-list-item-content .fl-list-item-content-text,
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-list-item-content .fl-list-item-content-text * {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->content_color ); ?>;
	}
	<?php
endif;

// Content Style - Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'content_typography',
	'selector'     => ".fl-node-$id.fl-module-list .fl-list-item-content .fl-list-item-content-text, .fl-node-$id.fl-module-list .fl-list-item-content .fl-list-item-content-text *",
) );

// List Style - Background Color
if ( ! empty( $settings->list_bg_color ) ) :
	?>
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-module-content {
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->list_bg_color ); ?>;
	}
	<?php
endif;

// List Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'list_padding',
	'selector'     => ".fl-node-$id .fl-module-content",
	'props'        => array(
		'padding-top'    => 'list_padding_top',
		'padding-right'  => 'list_padding_right',
		'padding-bottom' => 'list_padding_bottom',
		'padding-left'   => 'list_padding_left',
	),
) );

// List Item Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'common_list_item_padding',
	'selector'     => ".fl-node-$id .fl-module-content .fl-list .fl-list-item",
	'props'        => array(
		'padding-top'    => 'common_list_item_padding_top',
		'padding-right'  => 'common_list_item_padding_right',
		'padding-bottom' => 'common_list_item_padding_bottom',
		'padding-left'   => 'common_list_item_padding_left',
	),
) );

// List Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'list_border',
	'selector'     => '.fl-node-' . $id . ' .fl-module-content',
) );

// Icon Style - Color
if ( ! empty( $settings->icon_color ) ) :
	?>
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-list-item-heading-icon .fl-list-item-icon,
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-list-item-content-icon .fl-list-item-icon {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->icon_color ); ?>;
	}
	<?php
endif;

// Icon Style - Size
if ( ! empty( $settings->icon_size ) ) :
	?>
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-list-item-heading-icon .fl-list-item-icon,
	.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-list-item-content-icon .fl-list-item-icon {
		font-size: <?php echo $settings->icon_size . 'px'; ?>;
	}
	<?php
endif;

// Icon Style - Width
if ( ! empty( $settings->icon_width ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-list-item-heading-icon .fl-list-item-icon,
	.fl-node-<?php echo $id; ?> .fl-list-item-content-icon .fl-list-item-icon {
		width: <?php echo $settings->icon_width . 'px'; ?>;
		text-align: center;
	}
	<?php
endif;

// Icon Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'icon_padding',
	'selector'     => ".fl-node-$id .fl-module-content .fl-list-item-icon",
	'props'        => array(
		'padding-top'    => 'icon_padding_top',
		'padding-right'  => 'icon_padding_right',
		'padding-bottom' => 'icon_padding_bottom',
		'padding-left'   => 'icon_padding_left',
	),
) );

?>

.fl-node-<?php echo $id; ?> .fl-module-content ul.fl-list,
.fl-node-<?php echo $id; ?> .fl-module-content ol.fl-list {
	list-style-type: none;
}

<?php

// List Item Line Separator
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'separator_size',
	'selector'     => ".fl-node-$id .fl-module-content .fl-list-item ~ .fl-list-item",
	'prop'         => 'border-top-width',
	'unit'         => 'px',
) );
?>

.fl-node-<?php echo $id; ?> .fl-module-content .fl-list-item ~ .fl-list-item {
	border-top-style: <?php echo $settings->separator_style; ?>;
	border-top-color: <?php echo ( empty( $settings->separator_color ) ? 'transparent' : FLBuilderColor::hex_or_rgb( $settings->separator_color ) ); ?>;
}

<?php
$section      = 'section-' . $id;
$item_counter = 0;
foreach ( $settings->list_items as $k => $item ) :

	if ( ! is_object( $item ) ) {
		$item_counter++;
		continue;
	}

	// Item Heading Text Color
	if ( ! empty( $item->heading_text_color ) ) :
		?>
		.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-module-content .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-heading-text {
			color: <?php echo FLBuilderColor::hex_or_rgb( $item->heading_text_color ); ?>;
		}
		<?php
	endif;

	// Item Content Text Color
	if ( ! empty( $item->content_text_color ) ) :
		?>
		.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-module-content .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-content-text * {
			color: <?php echo FLBuilderColor::hex_or_rgb( $item->content_text_color ); ?>;
		}
		<?php
	endif;

	// Item Background Color
	if ( ! empty( $item->bg_color ) ) :
		?>
		.fl-node-<?php echo $id; ?> .fl-module-content .fl-list-item-<?php echo $item_counter; ?> {
			background-color: <?php echo FLBuilderColor::hex_or_rgb( $item->bg_color ); ?>;
		}
		<?php
	endif;

	// Item Icon Style - Color
	if ( ! empty( $item->icon_color ) ) :
		?>
		.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-module-content .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-heading-icon .fl-list-item-icon,
		.fl-row .fl-col .fl-node-<?php echo $id; ?> .fl-module-content .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-content-icon .fl-list-item-icon {
			color: <?php echo FLBuilderColor::hex_or_rgb( $item->icon_color ); ?>;
		}
		<?php
	endif;

	// List Item Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings->list_items[ $k ],
		'setting_name' => 'list_item_padding',
		'selector'     => ".fl-node-$id .fl-module-content .fl-list .fl-list-item-$item_counter",
		'props'        => array(
			'padding-top'    => 'list_item_padding_top',
			'padding-right'  => 'list_item_padding_right',
			'padding-bottom' => 'list_item_padding_bottom',
			'padding-left'   => 'list_item_padding_left',
		),
	) );

	// Item Icons
	// For the Ordered List, Icons will be a numeric sequence unless overridden
	// in the individual list item.
	if ( 'ol' === $settings->list_type ) :
		?>
		.fl-node-<?php echo $id; ?> {
			counter-reset: <?php echo $section; ?>;
		}

		.fl-node-<?php echo $id; ?> .fl-list .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-icon::before {
			counter-increment: <?php echo $section; ?>;
			content: counter( <?php echo $section; ?>,  <?php echo $settings->ol_icon; ?>);
		}
		<?php
		// For the Unordered List, icons are determined from the predefined icons ( square, circle, disc )  in the Settings Form.
	elseif ( 'ul' === $settings->list_type ) :
		if ( 'square' === $settings->ul_icon ) {
			$item_icon = '\25A0';
		} elseif ( 'circle' === $settings->ul_icon ) {
			$item_icon = '\25CB';
		} elseif ( 'disc' === $settings->ul_icon ) {
			$item_icon = '\25cf';
		} else {
			$item_icon = '\25cf';
		}
		?>
		.fl-node-<?php echo $id; ?> .fl-list .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-icon::before {
			content: '<?php echo $item_icon; ?>';
		}
		<?php
	endif;

	$item_counter++;

endforeach;
