<?php

/**
 * @class FLIconModule
 */
class FLIconModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Icon', 'fl-builder' ),
			'description'     => __( 'Display an icon and optional title.', 'fl-builder' ),
			'category'        => true === FL_BUILDER_LITE ? __( 'Basic', 'fl-builder' ) : __( 'Media', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'star-filled.svg',
		));
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {
		if ( isset( $settings->r_align ) && 'custom' === $settings->r_align ) {
			$settings->align_responsive = $settings->r_custom_align;
			unset( $settings->r_align );
			unset( $settings->r_custom_align );
		}
		return $settings;
	}

	/**
	 * Returns link rel based on settings.
	 * @since 2.2
	 * @return string
	 */
	public function get_rel() {
		$rel = array();
		if ( '_blank' == $this->settings->link_target ) {
			$rel[] = 'noopener';
		}
		if ( isset( $this->settings->link_nofollow ) && 'yes' == $this->settings->link_nofollow ) {
			$rel[] = 'nofollow';
		}
		$rel = implode( ' ', $rel );
		if ( $rel ) {
			$rel = ' rel="' . $rel . '" ';
		}
		return $rel;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLIconModule', array(
	'general' => array( // Tab
		'title'    => __( 'General', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'icon'    => array(
						'type'    => 'icon',
						'label'   => __( 'Icon', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),

					'link'    => array(
						'type'          => 'link',
						'label'         => __( 'Link', 'fl-builder' ),
						'show_target'   => true,
						'show_nofollow' => true,
						'connections'   => array( 'url' ),
					),
					'sr_text' => array(
						'type'    => 'text',
						'label'   => __( 'Screen Reader Text', 'fl-builder' ),
						'default' => '',
					),
				),
			),
			'text'    => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'text' => array(
						'type'          => 'editor',
						'label'         => '',
						'media_buttons' => false,
						'inline_editor' => false,
						'connections'   => array( 'string' ),
						'preview'       => array(
							'type'     => 'text',
							'selector' => '.fl-icon-text-wrap',
						),
					),
				),
			),
		),
	),
	'style'   => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'structure' => array( // Section
				'title'  => __( 'Icon', 'fl-builder' ), // Section Title
				'fields' => array( // Section Fields
					'size'  => array(
						'type'       => 'unit',
						'label'      => __( 'Size', 'fl-builder' ),
						'default'    => '30',
						'sanitize'   => 'floatval',
						'responsive' => true,
						'units'      => array( 'px', 'em', 'rem' ),
						'slider'     => true,
						'preview'    => array(
							'type' => 'refresh',
						),
					),
					'align' => array(
						'type'       => 'align',
						'label'      => __( 'Alignment', 'fl-builder' ),
						'default'    => 'left',
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-icon',
							'property' => 'text-align',
						),
					),
				),
			),
			'colors'    => array( // Section
				'title'  => __( 'Icon Colors', 'fl-builder' ), // Section Title
				'fields' => array( // Section Fields
					'color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-icon i, .fl-icon i::before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-icon i:hover, .fl-icon i:hover::before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'duo_color1'     => array(
						'label'      => __( 'DuoTone Icon Primary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-icon i.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'duo_color2'     => array(
						'label'      => __( 'DuoTone Icon Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-icon i.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
					'bg_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
					'bg_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'three_d'        => array(
						'type'    => 'select',
						'label'   => __( 'Gradient', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
				),
			),
			'text'      => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'text_spacing'    => array(
						'type'    => 'unit',
						'label'   => __( 'Text Spacing', 'fl-builder' ),
						'slider'  => true,
						'units'   => array( 'px' ),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '.fl-icon-text',
							'property'  => 'padding-left',
							'unit'      => 'px',
							'important' => true,
						),
					),
					'text_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-icon-wrap .fl-icon-text, .fl-icon-wrap .fl-icon-text *, .fl-icon-wrap .fl-icon-text-link',
							'property'  => 'color',
							'important' => true,
						),
					),
					'text_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Text Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-icon-text, .fl-icon-text-link',
						),
					),
				),
			),
		),
	),
));
