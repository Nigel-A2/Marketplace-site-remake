<div class="fl-button-group fl-button-group-layout-<?php echo $settings->layout; ?> fl-button-group-width-<?php echo $settings->width; ?>">
	<div class="fl-button-group-buttons" role="group" aria-label="<?php echo esc_attr( $settings->button_group_label ); ?>">
		<?php
		$button_group_node = "fl-node-$id";

		for ( $i = 0; $i < count( $settings->items ); $i++ ) :
			if ( ! is_object( $settings->items[ $i ] ) ) {
				continue;
			}

			$button_settings = array(
				'id'                    => "fl-node-$id-$i",
				'width'                 => empty( $settings->width ) ? 'full' : $settings->width,
				'align'                 => isset( $settings->align ) ? $settings->align : 'left',
				'text'                  => isset( $settings->items[ $i ]->text ) ? $settings->items[ $i ]->text : '',
				'icon'                  => isset( $settings->items[ $i ]->icon ) ? $settings->items[ $i ]->icon : '',
				'icon_position'         => isset( $settings->items[ $i ]->icon_position ) ? $settings->items[ $i ]->icon_position : 'before',
				'icon_animation'        => isset( $settings->items[ $i ]->icon_animation ) ? $settings->items[ $i ]->icon_animation : 'disable',
				'click_action'          => isset( $settings->items[ $i ]->click_action ) ? $settings->items[ $i ]->click_action : 'link',
				'link'                  => isset( $settings->items[ $i ]->link ) ? $settings->items[ $i ]->link : '',
				'link_target'           => isset( $settings->items[ $i ]->link_target ) ? $settings->items[ $i ]->link_target : '',
				'link_nofollow'         => isset( $settings->items[ $i ]->link_nofollow ) ? $settings->items[ $i ]->link_nofollow : '',
				'link_download'         => isset( $settings->items[ $i ]->link_download ) ? $settings->items[ $i ]->link_download : '',
				'lightbox_content_type' => isset( $settings->items[ $i ]->lightbox_content_type ) ? $settings->items[ $i ]->lightbox_content_type : 'html',
				'lightbox_content_html' => isset( $settings->items[ $i ]->lightbox_content_html ) ? $settings->items[ $i ]->lightbox_content_html : '',
				'lightbox_video_link'   => isset( $settings->items[ $i ]->lightbox_video_link ) ? $settings->items[ $i ]->lightbox_video_link : '',
				'custom_width'          => isset( $settings->items[ $i ]->custom_width ) ? $settings->items[ $i ]->custom_width : '200',
				'padding'               => isset( $settings->items[ $i ]->padding ) ? $settings->items[ $i ]->padding : '',
				'text_color'            => isset( $settings->items[ $i ]->button_item_text_color ) ? $settings->items[ $i ]->button_item_text_color : '',
				'text_hover_coler'      => isset( $settings->items[ $i ]->button_item_text_hover_color ) ? $settings->items[ $i ]->button_item_text_hover_color : '',
				'typography'            => isset( $settings->items[ $i ]->button_item_typography ) ? $settings->items[ $i ]->button_item_typography : '',
				'bg_color'              => isset( $settings->items[ $i ]->button_item_bg_color ) ? $settings->items[ $i ]->button_item_bg_color : '',
				'bg_hover_color'        => isset( $settings->items[ $i ]->button_item_bg_hover_color ) ? $settings->items[ $i ]->button_item_bg_hover_color : '',
				'style'                 => isset( $settings->items[ $i ]->button_item_style ) ? $settings->items[ $i ]->button_item_style : '',
				'button_transition'     => isset( $settings->items[ $i ]->button_transition ) ? $settings->items[ $i ]->button_transition : '',
				'border'                => isset( $settings->items[ $i ]->button_item_border ) ? $settings->items[ $i ]->button_item_border : '',
				'border_hover_color'    => isset( $settings->items[ $i ]->button_item_border_hover_color ) ? $settings->items[ $i ]->button_item_border_hover_color : '',
			);

			echo '<div id="fl-button-group-button-' . "$id-$i" . '" class="fl-button-group-button fl-button-group-button-' . "$id-$i" . '">';
			FLBuilder::render_module_html( 'button', $button_settings );
			echo '</div>';

		endfor;
		?>
	</div>
</div>
