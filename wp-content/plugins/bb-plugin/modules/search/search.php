<?php

/**
 * @class FLSearchModule
 */
class FLSearchModule extends FLBuilderModule {


	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Search', 'fl-builder' ),
				'description'     => __( 'Display a grid of your WordPress posts.', 'fl-builder' ),
				'category'        => __( 'Actions', 'fl-builder' ),
				'editor_export'   => false,
				'partial_refresh' => true,
				'icon'            => 'search.svg',
			)
		);

		// Actions
		add_action( 'wp_ajax_fl_search_query', array( $this, 'search_query' ) );
		add_action( 'wp_ajax_nopriv_fl_search_query', array( $this, 'search_query' ) );

		// Filters
		add_filter( 'fl_builder_loop_query_args', array( $this, 'loop_query_args' ) );
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		if ( $this->settings && 'button' == $this->settings->layout && 'fullscreen' == $this->settings->btn_action ) {
			$this->add_js( 'jquery-magnificpopup' );
			$this->add_css( 'font-awesome-5' );
			$this->add_css( 'jquery-magnificpopup' );
		}
	}

	/**
	 * @method search_query
	 */
	public function search_query() {
		$post_id          = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
		$node_id          = isset( $_POST['node_id'] ) ? sanitize_text_field( $_POST['node_id'] ) : false;
		$template_id      = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : false;
		$template_node_id = isset( $_POST['template_node_id'] ) ? sanitize_text_field( $_POST['template_node_id'] ) : false;
		$keyword          = $_POST['keyword'];
		$args             = new stdClass();
		$query            = null;
		$html             = '';

		// Get the module settings.
		if ( $template_id ) {
			$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
			$data     = FLBuilderModel::get_layout_data( 'published', $post_id );
			$module   = FLBuilderModel::get_module( $data[ $template_node_id ] );
			$settings = $data[ $template_node_id ]->settings;
		} else {
			$module   = FLBuilderModel::get_module( $node_id );
			$settings = $module->settings;
		}

		$s = stripcslashes( $keyword );
		$s = trim( $s );
		$s = preg_replace( '/\s+/', ' ', $s );

		$args->keyword     = $s;
		$args->post_type   = 'any';
		$args->post_status = 'publish';
		$args->settings    = $settings;

		// Remove paged & offset parameters
		add_filter( 'fl_builder_loop_query_args', array( $this, 'remove_pagination_args' ), 10 );

		$query = FLBuilderLoop::custom_query( $args );

		// Reset paged & offset parameters to prevent breaking other modules
		remove_filter( 'fl_builder_loop_query_args', array( $this, 'remove_pagination_args' ), 10 );

		ob_start();
		include $this->dir . '/includes/results.php';
		$html = ob_get_clean();

		echo $html;
		die();
	}

	/**
	 * Removes orderby parameter to use the WP core search terms ordering.
	 *
	 * @param array $query_args The query parameters.
	 */
	public function loop_query_args( $query_args ) {
		if ( isset( $query_args['s'] ) ) {
			unset( $query_args['orderby'] );
		}

		return $query_args;
	}

	/**
	 * Remove pagination parameters
	 *
	 * @param  array $query_args Generated query args to override
	 * @return array                Updated query args
	 */
	public function remove_pagination_args( $query_args ) {
		$query_args['paged']  = 0;
		$query_args['offset'] = isset( $this->settings->offset ) ? $this->settings->offset : 0;
		return $query_args;
	}

	/**
	 * Render thumbnail for a post.
	 *
	 * Gets the post ID and renders the html markup for the featured image
	 * in the desired cropped size.
	 *
	 * @param  int $id The post ID.
	 * @return void
	 */
	public function render_featured_image( $id = null ) {

		if ( isset( $this->settings->show_image ) && 1 == $this->settings->show_image ) {

			// get image source and data
			$src        = $this->_get_uncropped_url( $id );
			$photo_data = $this->_get_img_data( $id );

			// set params
			$photo_settings = array(
				'align'        => 'center',
				'link_type'    => 'url',
				'crop'         => $this->settings->crop,
				'photo'        => $photo_data,
				'photo_src'    => $src,
				'photo_source' => 'library',
				'attributes'   => array(
					'data-no-lazy' => 1,
				),
			);

			// if link id is provided, set link_url param
			if ( $id ) {
				$photo_settings['link_url'] = get_the_permalink( $id );
			}

			if ( has_post_thumbnail() ) {
				// Render image
				FLBuilder::render_module_html( 'photo', $photo_settings );

			} elseif ( ! empty( $this->settings->image_fallback ) ) {
				// Render fallback
				printf(
					'<a href="%s" rel="bookmark" title="%s">%s</a>',
					get_the_permalink(),
					the_title_attribute( 'echo=0' ),
					wp_get_attachment_image( $this->settings->image_fallback, $this->settings->image_size )
				);
			}
		}

	}

	/**
	 * Full attachment image url.
	 *
	 * Gets a post ID and returns the url for the 'full' size of the attachment
	 * set as featured image.
	 *
	 * @param  int $id The post ID.
	 * @return string    The featured image url for the 'full' size.
	 */
	protected function _get_uncropped_url( $id ) {
		$thumb_id = get_post_thumbnail_id( $id );
		$size     = isset( $this->settings->image_size ) ? $this->settings->image_size : 'medium';
		$img      = wp_get_attachment_image_src( $thumb_id, $size );
		return is_array( $img ) ? $img[0] : '';
	}

	/**
	 * Get the featured image data.
	 *
	 * Gets a post ID and returns an array containing the featured image data.
	 *
	 * @param  int $id The post ID.
	 * @return array    The image data.
	 */
	protected function _get_img_data( $id ) {
		$thumb_id = get_post_thumbnail_id( $id );

		return FLBuilderPhoto::get_attachment_data( $thumb_id );
	}

	public function get_object_taxonomies() {

	}

	public function get_post_types() {
		return FLBuilderLoop::post_types();
	}

	public function get_form_source_slug() {

		return $this->settings->source;
	}

	public function get_form_classes() {
		$classname = 'fl-search-form';

		if ( ! empty( $this->settings->layout ) ) {
			$classname .= ' fl-search-form-' . $this->settings->layout;

			if ( 'button' == $this->settings->layout ) {
				$classname .= ' fl-search-button-' . $this->settings->btn_action;
				$classname .= ' fl-search-button-' . $this->settings->btn_align;

				if ( 'expand' == $this->settings->btn_action ) {
					$classname .= ' fl-search-button-expand-' . $this->settings->expand_position;
				}
			}
		}

		if ( ! empty( $this->settings->width ) ) {
			$classname .= ' fl-search-form-width-' . $this->settings->width;
		}

		if ( ! empty( $this->settings->form_align ) && 'full' != $this->settings->width ) {
			$classname .= ' fl-search-form-' . $this->settings->form_align;
		}

		return $classname;
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @return array
	 */
	public function get_button_settings() {
		$settings = array(
			'link'        => '#',
			'link_target' => '_self',
			'width'       => 'auto',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'btn_' ) ) {
				$key              = str_replace( 'btn_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Renders button.
	 */
	public function render_button() {
		if ( 'input' != $this->settings->layout ) {
			FLBuilder::render_module_html( 'button', $this->get_button_settings() );
		}
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLSearchModule', array(
	'layout'  => array(
		'title'    => __( 'Layout', 'fl-builder' ),
		'sections' => array(
			'general'     => array(
				'title'  => '',
				'fields' => array(
					'layout'          => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'inline',
						'options' => array(
							'input'   => __( 'Input Text Only', 'fl-builder' ),
							'inline'  => __( 'Inline', 'fl-builder' ),
							'button'  => __( 'Button Only', 'fl-builder' ),
							'stacked' => __( 'Stacked', 'fl-builder' ),

							//  TODO:
							// 'combine' => __( 'Combine', 'fl-builder' ),
						),
						'toggle'  => array(
							'input'   => array(
								'fields'   => array( 'placeholder' ),
								'sections' => array( 'form_style' ),
							),
							'inline'  => array(
								'fields'   => array( 'placeholder', 'btn_text' ),
								'sections' => array( 'button_icon', 'form_style', 'button_style', 'button_icon_color' ),
							),
							'stacked' => array(
								'fields'   => array( 'placeholder', 'btn_text', 'btn_align', 'btn_width' ),
								'sections' => array( 'button_icon', 'form_style', 'button_style', 'button_icon_color' ),
							),
							// 'combine' => array(
							// 	'fields'   => array( 'placeholder', 'btn_text' ),
							// 	'sections' => array( 'form_style', 'button_style' ),
							// ),
							'button'  => array(
								'fields'   => array( 'placeholder', 'btn_action', 'btn_text', 'btn_align', 'btn_width' ),
								'sections' => array( 'button_icon', 'button_style', 'form_style', 'button_icon_color' ),
							),
						),
					),
					'placeholder'     => array(
						'type'    => 'text',
						'label'   => __( 'Placeholder Text', 'fl-builder' ),
						'default' => __( 'Search...', 'fl-builder' ),
						'preview' => array(
							'type'      => 'attribute',
							'attribute' => 'placeholder',
							'selector'  => '.fl-search-text',
						),
					),
					'btn_text'        => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'fl-builder' ),
						'default' => __( 'Search', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-button-text',
						),
					),
					'btn_action'      => array(
						'type'    => 'select',
						'label'   => __( 'Action', 'fl-builder' ),
						'default' => 'expand',
						'options' => array(
							'expand'     => __( 'Expand on click', 'fl-builder' ),
							'fullscreen' => __( 'Full Screen', 'fl-builder' ),

							// TODO:
							// 'reveal'     => __( 'Reveal', 'fl-builder' ),
						),
						'toggle'  => array(
							'expand'     => array(
								'fields' => array( 'expand_position' ),
							),
							'fullscreen' => array(
								'sections' => array( 'fullscreen_style' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),

					),
					'expand_position' => array(
						'type'    => 'select',
						'label'   => __( 'Expand Position', 'fl-builder' ),
						'default' => 'left',
						'options' => array(
							'left'  => __( 'Left', 'fl-builder' ),
							'right' => __( 'Right', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'button_icon' => array(
				'title'  => __( 'Button Icon', 'fl-builder' ),
				'fields' => array(
					'btn_icon'          => array(
						'type'        => 'icon',
						'label'       => __( 'Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields'   => array( 'btn_icon_position' ),
							'sections' => array( 'button_icon_color' ),
						),
					),
					'btn_duo_color1'    => array(
						'label'      => __( 'DuoTone Primary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-button-icon.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_duo_color2'    => array(
						'label'      => __( 'DuoTone Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-button-icon.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_icon_position' => array(
						'type'    => 'select',
						'label'   => __( 'Icon Position', 'fl-builder' ),
						'default' => 'before',
						'options' => array(
							'before' => __( 'Before Text', 'fl-builder' ),
							'after'  => __( 'After Text', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
	'style'   => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'general_style'     => array(
				'title'  => '',
				'fields' => array(
					'width'               => array(
						'type'    => 'select',
						'label'   => __( 'Width', 'fl-builder' ),
						'default' => 'full',
						'options' => array(
							'auto'   => _x( 'Auto', 'Width.', 'fl-builder' ),
							'full'   => __( 'Full Width', 'fl-builder' ),
							'custom' => __( 'Custom', 'fl-builder' ),
						),
						'toggle'  => array(
							'auto'   => array(
								'fields' => array( 'form_align' ),
							),
							'full'   => array(),
							'custom' => array(
								'fields' => array( 'form_align', 'custom_width' ),
							),
						),
					),
					'custom_width'        => array(
						'type'     => 'unit',
						'label'    => __( 'Custom Width', 'fl-builder' ),
						'default'  => '1100',
						'sanitize' => 'absint',
						'units'    => array( 'px', '%' ),
						'slider'   => array(
							'min'  => 0,
							'max'  => 1100,
							'step' => 10,
						),
						'help'     => __( 'The max width of the search form container.', 'fl-builder' ),
						'preview'  => array(
							'type'     => 'css',
							'selector' => '{node} .fl-search-form-wrap',
							'property' => 'width',
						),
					),
					'form_height'         => array(
						'type'       => 'unit',
						'label'      => __( 'Height', 'fl-builder' ),
						'default'    => '0',
						'responsive' => true,
						'sanitize'   => 'absint',
						'units'      => array(
							'px',
							'vw',
							'vh',
						),
						'slider'     => array(
							'max'  => 600,
							'step' => 10,
						),
						'help'       => __( 'This setting is the minimum height of the form. Content will expand the height automatically.', 'fl-builder' ),
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-search-form-wrap',
							'property'  => 'min-height',
							'important' => true,
						),
					),
					'form_align'          => array(
						'type'       => 'align',
						'label'      => __( 'Alignment', 'fl-builder' ),
						'default'    => 'center',
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-search-form',
							'property' => 'text-align',
						),
					),
					'form_bg_color'       => array(
						'type'        => 'color',
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-search .fl-search-form-wrap',
							'property' => 'background-color',
						),
					),
					'form_bg_hover_color' => array(
						'type'        => 'color',
						'label'       => __( 'Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'form_border'         => array(
						'type'    => 'border',
						'label'   => __( 'Border', 'fl-builder' ),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '{node}.fl-module-search .fl-search-form-wrap',
							'important' => true,
						),
					),
					'form_border_hover'   => array(
						'type'    => 'border',
						'label'   => __( 'Border Hover', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'form_padding'        => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'default'    => '10',
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-search .fl-search-form-wrap',
							'property' => 'padding',
						),
					),
				),
			),
			'input_style'       => array(
				'title'  => __( 'Input Text', 'fl-builder' ),
				'fields' => array(
					'input_color'          => array(
						'type'        => 'color',
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-search .fl-search-text, {node}.fl-module-search .fl-search-text::placeholder',
							'property' => 'color',
						),
					),
					'input_hover_color'    => array(
						'type'        => 'color',
						'label'       => __( 'Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-search .fl-search-text:hover, {node}.fl-module-search .fl-search-text:focus, {node}.fl-module-search .fl-search-text:hover::placeholder, {node}.fl-module-search .fl-search-text:focus::placeholder',
							'property' => 'color',
						),
					),
					'input_bg_color'       => array(
						'type'        => 'color',
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-search .fl-search-text',
							'property' => 'background-color',
						),
					),
					'input_bg_hover_color' => array(
						'type'        => 'color',
						'label'       => __( 'Background Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-search .fl-search-text:hover, {node}.fl-module-search .fl-search-text:focus',
							'property' => 'background-color',
						),
					),
					'input_typography'     => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '{node}.fl-module-search .fl-search-text',
							'important' => true,
						),
					),
					'input_border'         => array(
						'type'    => 'border',
						'label'   => __( 'Border', 'fl-builder' ),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '{node}.fl-module-search .fl-search-text',
							'important' => true,
						),
					),
					'input_border_hover'   => array(
						'type'    => 'border',
						'label'   => __( 'Border Hover', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'input_padding'        => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'default'    => '12',
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node}.fl-module-search .fl-search-text',
							'property' => 'padding',
						),
					),
				),
			),
			'button_style'      => array(
				'title'  => 'Button',
				'fields' => array(
					'btn_align'            => array(
						'type'    => 'align',
						'label'   => __( 'Alignment', 'fl-builder' ),
						'default' => 'center',
						'preview' => array(
							'type' => 'none',
						),
					),
					'btn_width'            => array(
						'type'    => 'select',
						'label'   => __( 'Width', 'fl-builder' ),
						'default' => 'auto',
						'options' => array(
							'auto'   => _x( 'Auto', 'Width.', 'fl-builder' ),
							'custom' => __( 'Custom', 'fl-builder' ),
						),
						'toggle'  => array(
							'auto'   => array(
								'fields' => array( 'btn_align' ),
							),
							'full'   => array(),
							'custom' => array(
								'fields' => array( 'btn_align', 'btn_custom_width' ),
							),
						),
					),
					'btn_custom_width'     => array(
						'type'    => 'unit',
						'label'   => __( 'Custom Width', 'fl-builder' ),
						'default' => '200',
						'slider'  => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
						),
						'units'   => array(
							'px',
							'vw',
							'%',
						),
						'preview' => array(
							'type'     => 'css',
							'selector' => 'a.fl-button',
							'property' => 'width',
						),
					),
					'btn_padding'          => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button, .fl-form-field input[type=search]',
							'property' => 'padding',
						),
					),
					'btn_text_color'       => array(
						'type'       => 'color',
						'label'      => __( 'Text Color', 'fl-builder' ),
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button, a.fl-button *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_text_hover_color' => array(
						'type'       => 'color',
						'label'      => __( 'Text Hover Color', 'fl-builder' ),
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
					'btn_typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button',
							// 'important' => true,
						),
					),
					'btn_bg_color'         => array(
						'type'       => 'color',
						'label'      => __( 'Button Background Color', 'fl-builder' ),
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
					'btn_bg_hover_color'   => array(
						'type'       => 'color',
						'label'      => __( 'Button Background Hover Color', 'fl-builder' ),
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
					'btn_style'            => array(
						'type'    => 'select',
						'label'   => __( 'Button Background Style', 'fl-builder' ),
						'default' => 'flat',
						'options' => array(
							'flat'     => __( 'Flat', 'fl-builder' ),
							'gradient' => __( 'Gradient', 'fl-builder' ),
						),
					),
					'btn_border'           => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '{node}.fl-module-search a.fl-button',
							'important' => true,
						),
					),
					'btn_border_hover'     => array(
						'type'       => 'border',
						'label'      => __( 'Border Hover', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
				),
			),
			'button_icon_color' => array(
				'title'  => 'Button Icon Colors',
				'fields' => array(
					'btn_icon_color'       => array(
						'type'       => 'color',
						'default'    => '',
						'label'      => __( 'Icon Color', 'fl-builder' ),
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'      => 'css',
							'property'  => 'color',
							'selector'  => 'i.fl-button-icon.fas:before',
							'important' => true,
						),
					),
					'btn_icon_color_hover' => array(
						'type'       => 'color',
						'label'      => __( 'Icon Hover Color', 'fl-builder' ),
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
				),
			),
			'fullscreen_style'  => array(
				'title'  => 'Fullscreen',
				'fields' => array(
					'fs_input_width'  => array(
						'type'     => 'unit',
						'label'    => __( 'Input Width', 'fl-builder' ),
						'default'  => '600',
						'sanitize' => 'absint',
						'units'    => array( 'px', '%' ),
						'slider'   => array(
							'min'  => 0,
							'max'  => 1100,
							'step' => 10,
						),
						'help'     => __( 'The max width of the input field inside the lightbox.', 'fl-builder' ),
						'preview'  => array(
							'type' => 'none',
						),
					),
					'fs_overlay_bg'   => array(
						'type'        => 'color',
						'label'       => __( 'Overlay Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'fs_close_button' => array(
						'type'    => 'select',
						'label'   => __( 'Close Button', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'hide' => __( 'Hide', 'fl-builder' ),
							'show' => __( 'Show', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
	'content' => array(
		'title'    => __( 'Content', 'fl-builder' ),
		'sections' => array(
			'general'     => array(
				'title'  => '',
				'fields' => array(
					'result' => array(
						'type'    => 'select',
						'label'   => __( 'Results', 'fl-builder' ),
						'default' => 'redirect',
						'options' => array(
							'redirect' => __( 'Redirect to search page', 'fl-builder' ),
							'ajax'     => __( 'Display results below via Ajax', 'fl-builder' ),
						),
						'toggle'  => array(
							'ajax' => array(
								'sections' => array( 'ajax_result' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'ajax_result' => array(
				'title'  => __( 'Ajax Result', 'fl-builder' ),
				'fields' => array(
					'result_width'        => array(
						'type'    => 'select',
						'label'   => __( 'Width', 'fl-builder' ),
						'default' => 'full',
						'options' => array(
							'full'   => __( 'Full Width', 'fl-builder' ),
							'custom' => __( 'Custom', 'fl-builder' ),
						),
						'toggle'  => array(
							'full'   => array(),
							'custom' => array(
								'fields' => array( 'custom_result_width' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'custom_result_width' => array(
						'type'     => 'unit',
						'label'    => __( 'Custom Width', 'fl-builder' ),
						'default'  => '1100',
						'sanitize' => 'absint',
						'units'    => array( 'px', '%' ),
						'slider'   => array(
							'min'  => 0,
							'max'  => 1100,
							'step' => 10,
						),
						'help'     => __( 'The max width of the ajax result container.', 'fl-builder' ),
						'preview'  => array(
							'type' => 'none',
						),
					),
					'show_image'          => array(
						'type'    => 'select',
						'label'   => __( 'Featured Image', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'image_size', 'crop', 'image_fallback' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'image_size'          => array(
						'type'    => 'photo-sizes',
						'label'   => __( 'Size', 'fl-builder' ),
						'default' => 'medium',
						'preview' => array(
							'type' => 'none',
						),
					),
					'crop'                => array(
						'type'    => 'select',
						'label'   => __( 'Crop', 'fl-builder' ),
						'default' => 'landscape',
						'options' => array(
							''       => _x( 'None', 'Photo Crop.', 'fl-builder' ),
							'square' => __( 'Square', 'fl-builder' ),
							'circle' => __( 'Circle', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'image_fallback'      => array(
						'default'     => '',
						'type'        => 'photo',
						'show_remove' => true,
						'label'       => __( 'Fallback Image', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'show_content'        => array(
						'type'    => 'select',
						'label'   => __( 'Content', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'no_results_message'  => array(
						'type'    => 'textarea',
						'label'   => __( 'No Results Message', 'fl-builder' ),
						'default' => __( "Sorry, we couldn't find any posts. Please try a different search.", 'fl-builder' ),
						'rows'    => 6,
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			// 'advanced_filter'   => array(
			// 	'title'    => '',
			// 	'services' => 'autoresponder',
			// 	'template' => array(
			// 		'id'   => 'fl-builder-service-settings',
			// 		'file' => FL_BUILDER_DIR . 'includes/ui-service-settings.php',
			// 	),
			// ),
		),
	),
));
