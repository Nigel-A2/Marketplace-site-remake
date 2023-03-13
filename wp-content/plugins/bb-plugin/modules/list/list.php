<?php

/**
 * @class FLListModule
 */
class FLListModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'List', 'fl-builder' ),
				'description'     => __( 'A simple list of items.', 'fl-builder' ),
				'category'        => __( 'Basic', 'fl-builder' ),
				'partial_refresh' => true,
				'icon'            => 'list.svg',
			)
		);
	}

	function get_list_opening_tag( $list_type ) {
		$list_tag_open = '';

		if ( 'ul' === $list_type ) {
			$list_tag_open = '<ul class="fl-list fl-list-unordered">';
		} elseif ( 'ol' === $list_type ) {
			$list_tag_open = '<ol class="fl-list fl-list-ordered">';
		} elseif ( 'div' === $list_type ) {
			$list_tag_open = '<div class="fl-list fl-list-regular" role="list">';
		}

		return $list_tag_open;
	}

	function get_list_closing_tag( $list_type ) {
		$list_tag_close = '';

		if ( 'ul' === $list_type ) {
			$list_tag_close = '</ul>';
		} elseif ( 'ol' === $list_type ) {
			$list_tag_close = '</ol>';
		} elseif ( 'div' === $list_type ) {
			$list_tag_close = '</div>';
		}

		return $list_tag_close;
	}

	function get_list_item_tag( $list_type ) {
		$li_div = '';

		if ( 'ul' === $list_type || 'ol' === $list_type ) {
			$li_div = 'li';
		} elseif ( 'div' === $list_type ) {
			$li_div = 'div';
		}

		return $li_div;
	}

	function get_list_icon( $list_item_icon, $list_icon_default ) {
		$list_icon = '';

		if ( ! empty( $list_item_icon ) ) {
			$list_icon = '<i class="fl-list-item-icon ' . $list_item_icon . '" aria-hidden="true"></i>';
		} else {
			$list_icon = $list_icon_default;
		}

		return $list_icon;
	}

	function get_heading_icon( $list_icon, $list_icon_placement ) {
		$heading_icon = '';

		if ( 'heading_left' == $list_icon_placement || 'heading' == $list_icon_placement ) {
			$heading_icon = '<span class="fl-list-item-heading-icon fl-list-item-heading-left">' . $list_icon . '</span>';
		} elseif ( 'heading_right' == $list_icon_placement ) {
			$heading_icon = '<span class="fl-list-item-heading-icon fl-list-item-heading-icon-right">' . $list_icon . '</span>';
		}

		return $heading_icon;
	}

	function get_content_icon( $list_icon, $list_icon_placement ) {
		$content_icon = '';

		if ( 'content_left' == $list_icon_placement || 'content' == $list_icon_placement ) {
			$content_icon = '<span class="fl-list-item-content-icon">' . $list_icon . '</span>';
		} elseif ( 'content_right' == $list_icon_placement ) {
			$content_icon = '<span class="fl-list-item-content-icon fl-list-item-content-icon-right">' . $list_icon . '</span>';
		}

		return $content_icon;
	}

	function get_heading_html( $heading_tag, $heading_text, $heading_icon, $icon_placement ) {
		$heading_html = '';

		if ( empty( $heading_icon ) && empty( $heading_text ) ) {
			return $heading_html;
		}

		if ( ! empty( $heading_tag ) ) {
			$wrapped_heading_text = '<span class="fl-list-item-heading-text">' . esc_html( $heading_text ) . '</span>';

			if ( 'heading_right' == $icon_placement ) {
				$heading_html = "<$heading_tag class=\"fl-list-item-heading\">$wrapped_heading_text $heading_icon</$heading_tag>";
			} else {
				$heading_html = "<$heading_tag class=\"fl-list-item-heading\">$heading_icon $wrapped_heading_text</$heading_tag>";
			}
		}

		return $heading_html;
	}

	function get_content_html( $content_tag, $content_text, $content_icon, $icon_placement ) {
		$content_html = '';

		if ( empty( $content_icon ) && empty( $content_text ) ) {
			return $content_html;
		}

		if ( ! empty( $content_text ) ) {
			$wrapped_content_text = '<div class="fl-list-item-content-text">' . $content_text . '</div>';

			if ( 'content_right' == $icon_placement ) {
				$content_html = "<$content_tag class=\"fl-list-item-content\">$wrapped_content_text  $content_icon</$content_tag>";
			} else {
				$content_html = "<$content_tag class=\"fl-list-item-content\">$content_icon $wrapped_content_text</$content_tag>";
			}
		}

		return $content_html;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module(
	'FLListModule', array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'list_items_section'     => array(
					'title'  => __( 'List Items', 'fl-builder' ),
					'fields' => array(
						'list_items' => array(
							'type'         => 'form',
							'label'        => __( 'List Item', 'fl-builder' ),
							'form'         => 'list_item_form', // ID from registered form below
							'preview_text' => 'label', // Name of a field to use for the preview text
							'multiple'     => true,
						),
					),
				),
				'display'                => array(
					'title'     => __( 'Display', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'list_type'           => array(
							'type'    => 'select',
							'label'   => __( 'List Type', 'fl-builder' ),
							'default' => 'div',
							'options' => array(
								'div' => __( 'Generic List (div)', 'fl-builder' ),
								'ul'  => __( 'Unordered List (ul)', 'fl-builder' ),
								'ol'  => __( 'Ordered List (ol)', 'fl-builder' ),
							),
							'toggle'  => array(
								'ul'  => array(
									'fields' => array( 'ul_icon' ),
								),
								'ol'  => array(
									'fields' => array( 'ol_icon' ),
								),
								'div' => array(
									'fields' => array( 'div_icon' ),
								),
							),
							'help'    => __( 'The type of list to generate. Each type has a corresponding set of icons available. See List Icon field below.', 'fl-builder' ),
						),
						'ul_icon'             => array(
							'type'    => 'select',
							'label'   => __( 'List Icon', 'fl-builder' ),
							'default' => 'disc',
							'options' => array(
								'square' => __( 'Square ( &#9632; )', 'fl-builder' ),
								'circle' => __( 'Circle ( &cir; )', 'fl-builder' ),
								'disc'   => __( 'Disc ( &#9679; )', 'fl-builder' ),
							),
							'help'    => __( 'Select an icon for the Unordered List (ul).', 'fl-builder' ),
						),
						'ol_icon'             => array(
							'type'    => 'select',
							'label'   => __( 'List Icon', 'fl-builder' ),
							'default' => 'decimal',
							'options' => array(
								'decimal'              => __( 'Numeric', 'fl-builder' ),
								'decimal-leading-zero' => __( 'Numeric With Leading Zeros', 'fl-builder' ),
								'upper-alpha'          => __( 'Alphabetic (Upper)', 'fl-builder' ),
								'lower-alpha'          => __( 'Alphabetic (Lower)', 'fl-builder' ),
								'upper-roman'          => __( 'Roman Numerals (Upper)', 'fl-builder' ),
								'lower-roman'          => __( 'Roman Numerals (Lower)', 'fl-builder' ),
								'hebrew'               => __( 'Hebrew Numerals', 'fl-builder' ),
								'lower-armenian'       => __( 'Armenian Numerals (Lower)', 'fl-builder' ),
								'upper-armenian'       => __( 'Armenian Numerals (Upper)', 'fl-builder' ),
								'lower-greek'          => __( 'Greek Numerals (Lower)', 'fl-builder' ),
							),
							'help'    => __( 'Select an icon for the Ordered List (ol).', 'fl-builder' ),
						),
						'div_icon'            => array(
							'type'        => 'icon',
							'label'       => __( 'List Icon', 'fl-builder' ),
							'show_remove' => true,
							'help'        => __( 'Generic List icon. You can override this in the individual List Item icon.', 'fl-builder' ),
						),
						'list_icon_placement' => array(
							'type'    => 'select',
							'label'   => __( 'List Icon Placement', 'fl-builder' ),
							'default' => 'content_left',
							'options' => array(
								'heading_left'  => __( 'Left of Heading', 'fl-builder' ),
								'content_left'  => __( 'Left of Content', 'fl-builder' ),
								'heading_right' => __( 'Right of Heading', 'fl-builder' ),
								'content_right' => __( 'Right of Content', 'fl-builder' ),
							),
						),
					),
				),
				'list_item_tags_section' => array(
					'title'     => __( 'List Item Tags', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'heading_tag' => array(
							'type'    => 'select',
							'label'   => __( 'List Item Heading Tag', 'fl-builder' ),
							'default' => 'h3',
							'options' => array(
								'h1'   => __( 'h1', 'fl-builder' ),
								'h2'   => __( 'h2', 'fl-builder' ),
								'h3'   => __( 'h3', 'fl-builder' ),
								'h4'   => __( 'h4', 'fl-builder' ),
								'h5'   => __( 'h5', 'fl-builder' ),
								'h6'   => __( 'h6', 'fl-builder' ),
								'span' => __( 'span', 'fl-builder' ),
								'div'  => __( 'div', 'fl-builder' ),
							),
							'help'    => __( 'The wrapper tag for the heading of each list item. Heading appears above the text content.', 'fl-builder' ),
						),
						'content_tag' => array(
							'type'    => 'select',
							'label'   => __( 'List Item Content Tag', 'fl-builder' ),
							'default' => 'div',
							'options' => array(
								'div'     => __( 'div', 'fl-builder' ),
								'aside'   => __( 'aside', 'fl-builder' ),
								'section' => __( 'section', 'fl-builder' ),
							),
							'help'    => __( 'The wrapper tag for the text content of each list item. Text content is right below the list item heading.', 'fl-builder' ),
						),
					),
				),
			),
		),
		'style'   => array( // Tab
			'title'    => __( 'Style', 'fl-builder' ), // Tab title
			'sections' => array( // Tab Sections
				'general_style_section'  => array( // Section
					'title'  => '', // Section Title
					'fields' => array( // Section Fields
						'list_bg_color'            => array(
							'type'        => 'color',
							'label'       => __( 'List Background Color', 'fl-builder' ),
							'connections' => array( 'color' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => '.fl-module-content',
								'property'  => 'background-color',
								'important' => true,
							),
						),
						'list_padding'             => array(
							'type'       => 'dimension',
							'label'      => __( 'List Padding', 'fl-builder' ),
							'default'    => '0',
							'responsive' => true,
							'slider'     => true,
							'units'      => array(
								'px',
								'em',
								'%',
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-module-content',
								'property' => 'padding',
							),
						),
						'common_list_item_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'List Item Padding', 'fl-builder' ),
							'default'    => '0',
							'responsive' => true,
							'slider'     => true,
							'units'      => array(
								'px',
								'em',
								'%',
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-module-content .fl-list .fl-list-item',
								'property' => 'padding',
							),
							'help'       => __( 'This applies to all list items, but can be overridden individually.', 'fl-builder' ),
						),
						'list_border'              => array(
							'type'       => 'border',
							'label'      => __( 'Border Around List', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-module-content',
							),
						),
					),
				),
				'icon_style_section'     => array(
					'title'     => __( 'Icon Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'icon_color'   => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Icon Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => '.fl-module-content .fl-list-item-icon',
								'property'  => 'color',
								'important' => true,
							),
						),
						'icon_size'    => array(
							'type'      => 'unit',
							'label'     => __( 'Icon Size', 'fl-builder' ),
							'default'   => '10',
							'maxlength' => '2',
							'size'      => '3',
							'sanitize'  => 'absint',
							'slider'    => true,
							'units'     => array(
								'px',
							),
							'slider'    => array(
								'px' => array(
									'min'  => 10,
									'max'  => 100,
									'step' => 1,
								),
							),
							'preview'   => array(
								'type'      => 'css',
								'selector'  => '{node}.fl-module-list .fl-list-item-icon',
								'property'  => 'font-size',
								'important' => true,
							),
						),
						'icon_width'   => array(
							'type'         => 'unit',
							'label'        => __( 'Icon Width', 'fl-builder' ),
							'maxlength'    => '5',
							'size'         => '5',
							'sanitize'     => 'absint',
							'slider'       => true,
							'units'        => array(
								'px',
							),
							'default_unit' => 'px',
							'slider'       => array(
								'min'  => 30,
								'max'  => 400,
								'step' => 10,
							),
							'preview'      => array(
								'type'      => 'css',
								'selector'  => '{node}.fl-module-list .fl-list-item-icon',
								'property'  => 'width',
								'important' => true,
							),
						),
						'icon_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Icon Padding', 'fl-builder' ),
							'default'    => '0',
							'responsive' => true,
							'slider'     => true,
							'units'      => array(
								'px',
								'em',
								'%',
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-module-content .fl-list-item-icon',
								'property' => 'padding',
							),
						),
					),
				),
				'heading_style_section'  => array(
					'title'     => __( 'Heading Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'heading_color'      => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Heading Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => '.fl-module-content .fl-list-item-heading',
								'property'  => 'color',
								'important' => true,
							),
						),
						'heading_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'      => 'css',
								'selector'  => '{node}.fl-module-list .fl-list-item-heading',
								'important' => true,
							),
						),
					),
				),
				'content_style_section'  => array(
					'title'     => __( 'Content Style', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'content_color'      => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Content Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => '.fl-module-content .fl-list-item-content .fl-list-item-content-text, .fl-module-content .fl-list-item-content .fl-list-item-content-text *',
								'property'  => 'color',
								'important' => true,
							),
						),
						'content_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'      => 'css',
								'selector'  => '{node}.fl-module-list .fl-list-item-content .fl-list-item-content-text, {node}.fl-module-list .fl-list-item-content .fl-list-item-content-text *',
								'important' => true,
							),
						),
					),
				),
				'item_separator_section' => array(
					'title'     => 'Item Separator',
					'collapsed' => true,
					'fields'    => array(
						'separator_style' => array(
							'type'    => 'select',
							'label'   => __( 'Line Separator Style', 'fl-builder' ),
							'default' => 'none',
							'options' => array(
								'none'   => __( 'None (No Separator)', 'fl-builder' ),
								'solid'  => __( 'Solid', 'fl-builder' ),
								'dashed' => __( 'Dashed', 'fl-builder' ),
								'dotted' => __( 'Dotted', 'fl-builder' ),
								'double' => __( 'Double', 'fl-builder' ),
							),
							'preview' => array(
								'type'      => 'css',
								'selector'  => '{node}.fl-module-list .fl-list-item ~ .fl-list-item',
								'property'  => 'border-top-style',
								'important' => true,
							),
						),
						'separator_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Line Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'     => 'css',
								'selector' => '{node}.fl-module-list .fl-list-item ~ .fl-list-item',
								'property' => 'border-top-color',
							),
							'help'        => __( 'Hint: Set to transparent color for SPACE separator.', 'fl-builder' ),
						),
						'separator_size'  => array(
							'type'       => 'unit',
							'label'      => __( 'Separator Size', 'fl-builder' ),
							'default'    => '0',
							'maxlength'  => '2',
							'size'       => '3',
							'sanitize'   => 'absint',
							'slider'     => true,
							'units'      => array(
								'px',
							),
							'slider'     => array(
								'px' => array(
									'min'  => 0,
									'max'  => 50,
									'step' => 1,
								),
							),
							'responsive' => true,
							'preview'    => array(
								'type'      => 'css',
								'selector'  => '{node}.fl-module-list .fl-list-item ~ .fl-list-item',
								'property'  => 'border-top-width',
								'important' => true,
							),
						),
					),
				),
			),
		),
	)
);

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form(
	'list_item_form', array(
		'title' => __( 'Add List Item', 'fl-builder' ),
		'tabs'  => array(
			'general' => array(
				'title'    => __( 'General', 'fl-builder' ),
				'sections' => array(
					'list_item_icon_section'    => array(
						'title'  => 'List Item Icon',
						'fields' => array(
							'list_item_icon' => array(
								'type'        => 'icon',
								'label'       => __( 'Icon', 'fl-builder' ),
								'show_remove' => true,
								'help'        => __( 'Overrides the Icon applied to the module settings.', 'fl-builder' ),
							),
						),
					),
					'list_item_heading_section' => array(
						'title'  => 'List Item Heading',
						'fields' => array(
							'heading' => array(
								'type'  => 'text',
								'label' => __( 'Heading Text', 'fl-builder' ),
								'help'  => __( 'Leave empty if you don\'t want to include a heading.', 'fl-builder' ),
							),
						),
					),
					'list_item_content_section' => array(
						'title'  => 'List Item Content',
						'fields' => array(
							'content' => array(
								'type'  => 'editor',
								'label' => __( 'Content', 'fl-builder' ),
							),
						),
					),
				),
			),
			'style'   => array(
				'title'    => __( 'Style', 'fl-builder' ),
				'sections' => array(
					'list_item_section' => array(
						'title'  => __( 'List Item', 'fl-builder' ),
						'fields' => array(
							'heading_text_color' => array(
								'type'        => 'color',
								'connections' => array( 'color' ),
								'label'       => __( 'Heading Text Color', 'fl-builder' ),
								'show_reset'  => true,
								'show_alpha'  => true,
							),
							'content_text_color' => array(
								'type'        => 'color',
								'connections' => array( 'color' ),
								'label'       => __( 'Content Text Color', 'fl-builder' ),
								'show_reset'  => true,
								'show_alpha'  => true,
							),
							'bg_color'           => array(
								'type'        => 'color',
								'connections' => array( 'color' ),
								'label'       => __( 'Background Color', 'fl-builder' ),
								'show_reset'  => true,
								'show_alpha'  => true,
							),
							'icon_color'         => array(
								'type'        => 'color',
								'connections' => array( 'color' ),
								'label'       => __( 'Icon Color', 'fl-builder' ),
								'show_reset'  => true,
								'show_alpha'  => true,
							),
							'list_item_padding'  => array(
								'type'       => 'dimension',
								'label'      => __( 'List Item Padding', 'fl-builder' ),
								'default'    => '',
								'responsive' => true,
								'slider'     => true,
								'units'      => array(
									'px',
									'em',
									'%',
								),
								'help'       => __( 'This overrides the setting that applies to all list items.', 'fl-builder' ),
							),
						),
					),
				),
			),
		),
	)
);
