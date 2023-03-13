<?php

$list_tag_open     = $module->get_list_opening_tag( $settings->list_type );
$list_tag_close    = $module->get_list_closing_tag( $settings->list_type );
$li_div            = $module->get_list_item_tag( $settings->list_type );
$list_icon_default = '<span class="fl-list-item-icon"></span>';

if ( 'div' === $settings->list_type && ! empty( $settings->div_icon ) ) {
	$list_icon_default = '<i class="fl-list-item-icon ' . $settings->div_icon . '" aria-hidden="true"></i>';
}

// List opening tag -- div, ul, ol.
echo $list_tag_open;

foreach ( $settings->list_items as $k => $item ) :
	if ( ! is_object( $item ) ) {
		continue;
	}

	$list_icon    = $module->get_list_icon( $item->list_item_icon, $list_icon_default );
	$heading_icon = $module->get_heading_icon( $list_icon, $settings->list_icon_placement );
	$content_icon = $module->get_content_icon( $list_icon, $settings->list_icon_placement );
	$heading_html = $module->get_heading_html( $settings->heading_tag, $item->heading, $heading_icon, $settings->list_icon_placement );
	$content_html = $module->get_content_html( $settings->content_tag, $item->content, $content_icon, $settings->list_icon_placement );

	$list_item_role  = ( 'div' == $li_div ) ? ' role="listitem"' : '';
	$list_item_class = ' class="fl-list-item fl-list-item-' . $k . '"';

	/**
	 *
	 * Render list item tag, role and class.
	 * - tag: 'li' or 'div'.
	 * - role: 'listitem' if tag is div.
	 * - class: 'fl-list-item fl-list-item-NN'; where NN = list item number.
	 *
	 */
	?>
	<<?php echo $li_div . $list_item_role . $list_item_class; ?>>
		<div class="fl-list-item-wrapper">
			<?php
				echo $heading_html;
				echo $content_html;
			?>
		</div>
	</<?php echo $li_div; ?>>
	<?php
endforeach;

// List closing tag -- div, ul, ol.
echo $list_tag_close;
