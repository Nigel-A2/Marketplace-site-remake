<?php

$global_settings = FLBuilderModel::get_global_settings();

$row_settings = array(
	'title' => __( 'Row Settings', 'fl-builder' ),
	'tabs'  => array(

		'style'    => array(
			'title'    => __( 'Style', 'fl-builder' ),
			'sections' => array(
				'general'          => array(
					'title'  => '',
					'fields' => array(
						'width'             => array(
							'type'    => 'select',
							'label'   => __( 'Width', 'fl-builder' ),
							'default' => $global_settings->row_width_default,
							'options' => array(
								'fixed' => __( 'Fixed', 'fl-builder' ),
								'full'  => __( 'Full Width', 'fl-builder' ),
							),
							'toggle'  => array(
								'full' => array(
									'fields' => array( 'content_width' ),
								),
							),
							'help'    => __( 'Full width rows span the width of the page from edge to edge. Fixed rows are no wider than the Row Max Width set in the Global Settings.', 'fl-builder' ),
							'preview' => array(
								'type' => 'refresh',
							),
						),
						'content_width'     => array(
							'type'    => 'select',
							'label'   => __( 'Content Width', 'fl-builder' ),
							'default' => $global_settings->row_content_width_default,
							'options' => array(
								'fixed' => __( 'Fixed', 'fl-builder' ),
								'full'  => __( 'Full Width', 'fl-builder' ),
							),
							'help'    => __( 'Full width content spans the width of the page from edge to edge. Fixed content is no wider than the Row Max Width set in the Global Settings.', 'fl-builder' ),
							'preview' => array(
								'type' => 'refresh',
							),
						),
						'max_content_width' => array(
							'type'         => 'unit',
							'label'        => __( 'Fixed Width', 'fl-builder' ),
							'placeholder'  => $global_settings->row_width,
							'default_unit' => $global_settings->row_width_unit,
							'units'        => array(
								'px',
								'vw',
								'%',
							),
							'slider'       => array(
								'px' => array(
									'min'  => 0,
									'max'  => $global_settings->row_width,
									'step' => 10,
								),
							),
							'preview'      => array(
								'type' => 'refresh',
							),
						),
						'full_height'       => array(
							'type'    => 'select',
							'label'   => __( 'Height', 'fl-builder' ),
							'default' => 'default',
							'options' => array(
								'default' => __( 'Default', 'fl-builder' ),
								'full'    => __( 'Full Height', 'fl-builder' ),
								'custom'  => __( 'Minimum Height', 'fl-builder' ),
							),
							'help'    => __( 'Full height rows fill the height of the browser window. Minimum height rows are at least as tall as the value entered.', 'fl-builder' ),
							'toggle'  => array(
								'full'   => array(
									'fields' => array( 'content_alignment' ),
								),
								'custom' => array(
									'fields' => array( 'content_alignment', 'min_height' ),
								),
							),
							'preview' => array(
								'type' => 'refresh',
							),
						),
						'min_height'        => array(
							'type'       => 'unit',
							'label'      => __( 'Minimum Height', 'fl-builder' ),
							'responsive' => true,
							'units'      => array(
								'px',
								'vw',
								'vh',
							),
							'slider'     => array(
								'px' => array(
									'min'  => 0,
									'max'  => 1000,
									'step' => 10,
								),
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-row-content-wrap',
								'property' => 'min-height',
							),
						),
						'content_alignment' => array(
							'type'    => 'select',
							'label'   => __( 'Vertical Alignment', 'fl-builder' ),
							'default' => 'center',
							'options' => array(
								'top'    => __( 'Top', 'fl-builder' ),
								'center' => __( 'Center', 'fl-builder' ),
								'bottom' => __( 'Bottom', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'refresh',
							),
						),
					),
				),
				'colors'           => array(
					'title'  => __( 'Colors', 'fl-builder' ),
					'fields' => array(
						'text_color'    => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Text Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'link_color'    => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Link Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'hover_color'   => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Link Hover Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'heading_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Heading Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
					),
				),
				'background'       => array(
					'title'  => __( 'Background', 'fl-builder' ),
					'fields' => array(
						'bg_type' => array(
							'type'    => 'select',
							'label'   => __( 'Type', 'fl-builder' ),
							'default' => 'none',
							'options' => array(
								'none'      => _x( 'None', 'Background type.', 'fl-builder' ),
								'color'     => _x( 'Color', 'Background type.', 'fl-builder' ),
								'gradient'  => _x( 'Gradient', 'Background type.', 'fl-builder' ),
								'photo'     => _x( 'Photo', 'Background type.', 'fl-builder' ),
								'video'     => _x( 'Video', 'Background type.', 'fl-builder' ),
								'embed'     => _x( 'Embedded Code', 'Background type.', 'fl-builder' ),
								'slideshow' => array(
									'label'   => _x( 'Slideshow', 'Background type.', 'fl-builder' ),
									'premium' => true,
								),
								'parallax'  => array(
									'label'   => _x( 'Parallax', 'Background type.', 'fl-builder' ),
									'premium' => true,
								),
							),
							'toggle'  => array(
								'color'     => array(
									'sections' => array( 'bg_color' ),
								),
								'gradient'  => array(
									'sections' => array( 'bg_gradient' ),
								),
								'photo'     => array(
									'sections' => array( 'bg_color', 'bg_photo', 'bg_overlay' ),
								),
								'video'     => array(
									'sections' => array( 'bg_color', 'bg_video', 'bg_overlay' ),
								),
								'slideshow' => array(
									'sections' => array( 'bg_color', 'bg_slideshow', 'bg_overlay' ),
								),
								'parallax'  => array(
									'sections' => array( 'bg_color', 'bg_parallax', 'bg_overlay' ),
								),
								'pattern'   => array(
									'sections' => array( 'bg_pattern', 'bg_color', 'bg_overlay' ),
								),
								'embed'     => array(
									'sections' => array( 'bg_embed_section' ),
								),
							),
							'preview' => array(
								'type' => 'refresh',
							),
						),
					),
				),
				'bg_photo'         => array(
					'title'  => __( 'Background Photo', 'fl-builder' ),
					'fields' => array(
						'bg_image_source' => array(
							'type'    => 'select',
							'label'   => __( 'Photo Source', 'fl-builder' ),
							'default' => 'library',
							'options' => array(
								'library' => __( 'Media Library', 'fl-builder' ),
								'url'     => __( 'URL', 'fl-builder' ),
							),
							'toggle'  => array(
								'library' => array(
									'fields' => array( 'bg_image' ),
								),
								'url'     => array(
									'fields' => array( 'bg_image_url', 'caption' ),
								),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'bg_image_url'    => array(
							'type'        => 'text',
							'label'       => __( 'Photo URL', 'fl-builder' ),
							'placeholder' => __( 'https://www.example.com/my-photo.jpg', 'fl-builder' ),
							'connections' => array( 'photo' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-image',
							),
						),
						'bg_image'        => array(
							'type'        => 'photo',
							'show_remove' => true,
							'label'       => __( 'Photo', 'fl-builder' ),
							'responsive'  => true,
							'connections' => array( 'photo' ),
							'preview'     => array(
								'type' => 'refresh',
							),
						),
						'bg_repeat'       => array(
							'type'       => 'select',
							'label'      => __( 'Repeat', 'fl-builder' ),
							'default'    => 'none',
							'responsive' => true,
							'options'    => array(
								'no-repeat' => _x( 'None', 'Background repeat.', 'fl-builder' ),
								'repeat'    => _x( 'Tile', 'Background repeat.', 'fl-builder' ),
								'repeat-x'  => _x( 'Horizontal', 'Background repeat.', 'fl-builder' ),
								'repeat-y'  => _x( 'Vertical', 'Background repeat.', 'fl-builder' ),
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-repeat',
							),
						),
						'bg_position'     => array(
							'type'       => 'select',
							'label'      => __( 'Position', 'fl-builder' ),
							'default'    => 'center center',
							'responsive' => true,
							'options'    => array(
								'left top'      => __( 'Left Top', 'fl-builder' ),
								'left center'   => __( 'Left Center', 'fl-builder' ),
								'left bottom'   => __( 'Left Bottom', 'fl-builder' ),
								'right top'     => __( 'Right Top', 'fl-builder' ),
								'right center'  => __( 'Right Center', 'fl-builder' ),
								'right bottom'  => __( 'Right Bottom', 'fl-builder' ),
								'center top'    => __( 'Center Top', 'fl-builder' ),
								'center center' => __( 'Center', 'fl-builder' ),
								'center bottom' => __( 'Center Bottom', 'fl-builder' ),
								'custom_pos'    => __( 'Custom Position', 'fl-builder' ),
							),
							'toggle'     => array(
								'custom_pos' => array(
									'fields' => array(
										'bg_x_position',
										'bg_y_position',
									),
								),
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-position',
							),
						),
						'bg_x_position'   => array(
							'type'         => 'unit',
							'label'        => __( 'X Position', 'fl-builder' ),
							'units'        => array( 'px', '%' ),
							'default_unit' => '%',
							'responsive'   => true,
							'slider'       => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
							'preview'      => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-position-x',
							),
						),
						'bg_y_position'   => array(
							'type'         => 'unit',
							'label'        => __( 'Y Position', 'fl-builder' ),
							'units'        => array( 'px', '%' ),
							'default_unit' => '%',
							'responsive'   => true,
							'slider'       => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
							'preview'      => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-position-y',
							),
						),
						'bg_attachment'   => array(
							'type'       => 'select',
							'label'      => __( 'Attachment', 'fl-builder' ),
							'default'    => 'scroll',
							'responsive' => true,
							'options'    => array(
								'scroll' => __( 'Scroll', 'fl-builder' ),
								'fixed'  => __( 'Fixed', 'fl-builder' ),
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-attachment',
							),
						),
						'bg_size'         => array(
							'type'       => 'select',
							'label'      => __( 'Scale', 'fl-builder' ),
							'default'    => 'cover',
							'responsive' => true,
							'options'    => array(
								'auto'    => _x( 'None', 'Background scale.', 'fl-builder' ),
								'contain' => __( 'Fit', 'fl-builder' ),
								'cover'   => __( 'Fill', 'fl-builder' ),
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-size',
							),
						),
					),
				),
				'bg_video'         => array(
					'title'  => __( 'Background Video', 'fl-builder' ),
					'fields' => array(
						'bg_video_source'      => array(
							'type'    => 'select',
							'label'   => __( 'Source', 'fl-builder' ),
							'default' => 'wordpress',
							'options' => array(
								'wordpress'     => __( 'Media Library', 'fl-builder' ),
								'video_url'     => 'URL',
								'video_service' => __( 'YouTube or Vimeo', 'fl-builder' ),
							),
							'toggle'  => array(
								'wordpress'     => array(
									'fields' => array( 'bg_video', 'bg_video_webm' ),
								),
								'video_url'     => array(
									'fields' => array( 'bg_video_url_mp4', 'bg_video_url_webm' ),
								),
								'video_service' => array(
									'fields' => array( 'bg_video_service_url' ),
								),
							),
							'preview' => array(
								'type' => 'refresh',
							),
						),
						'bg_video'             => array(
							'type'        => 'video',
							'show_remove' => true,
							'label'       => __( 'Video (MP4)', 'fl-builder' ),
							'help'        => __( 'A video in the MP4 format to use as the background of this row. Most modern browsers support this format.', 'fl-builder' ),
							'preview'     => array(
								'type' => 'refresh',
							),
						),
						'bg_video_webm'        => array(
							'type'        => 'video',
							'show_remove' => true,
							'label'       => __( 'Video (WebM)', 'fl-builder' ),
							'help'        => __( 'A video in the WebM format to use as the background of this row. This format is required to support browsers such as FireFox and Opera.', 'fl-builder' ),
							'preview'     => array(
								'type' => 'refresh',
							),
						),
						'bg_video_url_mp4'     => array(
							'type'        => 'text',
							'label'       => __( 'Video URL (MP4)', 'fl-builder' ),
							'help'        => __( 'A video in the MP4 to use as the background of this row. Most modern browsers support this format.', 'fl-builder' ),
							'preview'     => array(
								'type' => 'refresh',
							),
							'connections' => array( 'custom_field' ),
						),
						'bg_video_url_webm'    => array(
							'type'        => 'text',
							'label'       => __( 'Video URL (WebM)', 'fl-builder' ),
							'help'        => __( 'A video in the WebM format to use as the background of this row. This format is required to support browsers such as FireFox and Opera.', 'fl-builder' ),
							'preview'     => array(
								'type' => 'refresh',
							),
							'connections' => array( 'custom_field' ),
						),
						'bg_video_service_url' => array(
							'type'        => 'text',
							'label'       => __( 'YouTube Or Vimeo URL', 'fl-builder' ),
							'help'        => __( 'A video from YouTube or Vimeo to use as the background of this row. Most modern browsers support this format.', 'fl-builder' ),
							'preview'     => array(
								'type' => 'refresh',
							),
							'connections' => array( 'custom_field' ),
						),
						'bg_video_audio'       => array(
							'type'    => 'select',
							'label'   => __( 'Enable Audio', 'fl-builder' ),
							'default' => 'no',
							'options' => array(
								'no'  => __( 'No', 'fl-builder' ),
								'yes' => __( 'Yes', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'refresh',
							),
						),
						'bg_video_mobile'      => array(
							'type'    => 'select',
							'label'   => __( 'Enable Video in Mobile', 'fl-builder' ),
							'help'    => __( 'If set to "Yes", audio is disabled on mobile devices.', 'fl-builder' ),
							'default' => 'no',
							'options' => array(
								'no'  => __( 'No', 'fl-builder' ),
								'yes' => __( 'Yes', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'bg_video_fallback'    => array(
							'type'        => 'photo',
							'show_remove' => true,
							'label'       => __( 'Fallback Photo', 'fl-builder' ),
							'help'        => __( 'A photo that will be displayed if the video fails to load.', 'fl-builder' ),
							'preview'     => array(
								'type' => 'refresh',
							),
							'connections' => array( 'photo' ),
						),
					),
				),
				'bg_slideshow'     => array(
					'title'  => __( 'Background Slideshow', 'fl-builder' ),
					'fields' => array(
						'ss_source'             => array(
							'type'    => 'select',
							'label'   => __( 'Source', 'fl-builder' ),
							'default' => 'wordpress',
							'options' => array(
								'wordpress' => __( 'Media Library', 'fl-builder' ),
								'smugmug'   => 'SmugMug',
							),
							'help'    => __( 'Pull images from the WordPress media library or a gallery on your SmugMug site by inserting the RSS feed URL from SmugMug. The RSS feed URL can be accessed by using the get a link function in your SmugMug gallery.', 'fl-builder' ),
							'toggle'  => array(
								'wordpress' => array(
									'fields' => array( 'ss_photos' ),
								),
								'smugmug'   => array(
									'fields' => array( 'ss_feed_url' ),
								),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'ss_photos'             => array(
							'type'        => 'multiple-photos',
							'label'       => __( 'Photos', 'fl-builder' ),
							'preview'     => array(
								'type' => 'none',
							),
							'connections' => array( 'multiple-photos' ),
						),
						'ss_feed_url'           => array(
							'type'        => 'text',
							'label'       => __( 'Feed URL', 'fl-builder' ),
							'preview'     => array(
								'type' => 'none',
							),
							'connections' => array( 'custom_field' ),
						),
						'ss_speed'              => array(
							'type'        => 'unit',
							'label'       => __( 'Speed', 'fl-builder' ),
							'default'     => '3',
							'size'        => '5',
							'sanitize'    => 'FLBuilderUtils::sanitize_non_negative_number',
							'slider'      => true,
							'description' => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'fl-builder' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'ss_transition'         => array(
							'type'    => 'select',
							'label'   => __( 'Transition', 'fl-builder' ),
							'default' => 'fade',
							'options' => array(
								'none'            => _x( 'None', 'Slideshow transition type.', 'fl-builder' ),
								'fade'            => __( 'Fade', 'fl-builder' ),
								'kenBurns'        => __( 'Ken Burns', 'fl-builder' ),
								'slideHorizontal' => __( 'Slide Horizontal', 'fl-builder' ),
								'slideVertical'   => __( 'Slide Vertical', 'fl-builder' ),
								'blinds'          => __( 'Blinds', 'fl-builder' ),
								'bars'            => __( 'Bars', 'fl-builder' ),
								'barsRandom'      => __( 'Random Bars', 'fl-builder' ),
								'boxes'           => __( 'Boxes', 'fl-builder' ),
								'boxesRandom'     => __( 'Random Boxes', 'fl-builder' ),
								'boxesGrow'       => __( 'Boxes Grow', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'ss_transitionDuration' => array(
							'type'        => 'unit',
							'label'       => __( 'Transition Speed', 'fl-builder' ),
							'default'     => '1',
							'size'        => '5',
							'sanitize'    => 'FLBuilderUtils::sanitize_non_negative_number',
							'slider'      => true,
							'description' => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'fl-builder' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'ss_randomize'          => array(
							'type'    => 'select',
							'label'   => __( 'Randomize Photos', 'fl-builder' ),
							'default' => 'false',
							'options' => array(
								'false' => __( 'No', 'fl-builder' ),
								'true'  => __( 'Yes', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
				'bg_parallax'      => array(
					'title'  => __( 'Background Parallax', 'fl-builder' ),
					'fields' => array(
						'bg_parallax_image'  => array(
							'type'        => 'photo',
							'show_remove' => true,
							'label'       => __( 'Photo', 'fl-builder' ),
							'responsive'  => true,
							'preview'     => array(
								'type' => 'refresh',
							),
							'connections' => array( 'photo' ),
						),
						'bg_parallax_speed'  => array(
							'type'    => 'select',
							'label'   => __( 'Speed', 'fl-builder' ),
							'default' => 'fast',
							'options' => array(
								'2' => __( 'Fast', 'fl-builder' ),
								'5' => _x( 'Medium', 'Speed.', 'fl-builder' ),
								'8' => __( 'Slow', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'bg_parallax_offset' => array(
							'type'        => 'unit',
							'label'       => __( 'Image Offset', 'fl-builder' ),
							'responsive'  => true,
							'placeholder' => '0',
							'default'     => 0,
							'slider'      => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
							'preview'     => array(
								'type' => 'refresh',
							),
						),
					),
				),
				'bg_overlay'       => array(
					'title'  => __( 'Background Overlay', 'fl-builder' ),
					'fields' => array(
						'bg_overlay_type'     => array(
							'type'    => 'select',
							'label'   => __( 'Overlay Type', 'fl-builder' ),
							'default' => 'color',
							'options' => array(
								'none'     => __( 'None', 'fl-builder' ),
								'color'    => __( 'Color', 'fl-builder' ),
								'gradient' => __( 'Gradient', 'fl-builder' ),
							),
							'toggle'  => array(
								'color'    => array(
									'fields' => array( 'bg_overlay_color' ),
								),
								'gradient' => array(
									'fields' => array( 'bg_overlay_gradient' ),
								),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'bg_overlay_color'    => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Overlay Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'bg_overlay_gradient' => array(
							'type'    => 'gradient',
							'label'   => __( 'Overlay Gradient', 'fl-builder' ),
							'preview' => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap:after',
								'property' => 'background-image',
							),
						),
					),
				),
				'bg_color'         => array(
					'title'  => __( 'Background Color', 'fl-builder' ),
					'fields' => array(
						'bg_color' => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'refresh',
							),
							'connections' => array( 'color' ),
						),
					),
				),
				'bg_gradient'      => array(
					'title'  => __( 'Background Gradient', 'fl-builder' ),
					'fields' => array(
						'bg_gradient' => array(
							'type'    => 'gradient',
							'label'   => __( 'Gradient', 'fl-builder' ),
							'preview' => array(
								'type'     => 'css',
								'selector' => '> .fl-row-content-wrap',
								'property' => 'background-image',
							),
						),
					),
				),
				'bg_embed_section' => array(
					'title'  => __( 'Background Embedded Code', 'fl-builder' ),
					'fields' => array(
						'bg_embed_code' => array(
							'type'        => 'code',
							'editor'      => 'html',
							'rows'        => '8',
							'preview'     => array(
								'type' => 'refresh',
							),
							'connections' => array( 'string' ),
						),
					),
				),
				'border'           => array(
					'title'  => __( 'Border', 'fl-builder' ),
					'fields' => array(
						'border' => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-row-content-wrap',
							),
						),
					),
				),
			),
		),
		'advanced' => array(
			'title'    => __( 'Advanced', 'fl-builder' ),
			'sections' => array(
				'margins'       => array(
					'title'  => __( 'Spacing', 'fl-builder' ),
					'fields' => array(
						'margin'  => array(
							'type'       => 'dimension',
							'label'      => __( 'Margins', 'fl-builder' ),
							'slider'     => true,
							'units'      => array(
								'px',
								'%',
								'vw',
								'vh',
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-row-content-wrap',
								'property' => 'margin',
							),
							'responsive' => array(
								'default_unit' => array(
									'default'    => $global_settings->row_margins_unit,
									'medium'     => $global_settings->row_margins_medium_unit,
									'responsive' => $global_settings->row_margins_responsive_unit,
								),
								'placeholder'  => array(
									'default'    => empty( $global_settings->row_margins ) ? '0' : $global_settings->row_margins,
									'medium'     => empty( $global_settings->row_margins_medium ) ? '0' : $global_settings->row_margins_medium,
									'responsive' => empty( $global_settings->row_margins_responsive ) ? '0' : $global_settings->row_margins_responsive,
								),
							),
						),
						'padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'fl-builder' ),
							'slider'     => true,
							'units'      => array(
								'px',
								'em',
								'%',
								'vw',
								'vh',
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-row-content-wrap',
								'property' => 'padding',
							),
							'responsive' => array(
								'default_unit' => array(
									'default'    => $global_settings->row_padding_unit,
									'medium'     => $global_settings->row_padding_medium_unit,
									'responsive' => $global_settings->row_padding_responsive_unit,
								),
								'placeholder'  => array(
									'default'    => empty( $global_settings->row_padding ) ? '0' : $global_settings->row_padding,
									'medium'     => empty( $global_settings->row_padding_medium ) ? '0' : $global_settings->row_padding_medium,
									'responsive' => empty( $global_settings->row_padding_responsive ) ? '0' : $global_settings->row_padding_responsive,
								),
							),
						),
					),
				),
				'visibility'    => array(
					'title'  => __( 'Visibility', 'fl-builder' ),
					'fields' => array(
						'responsive_display'         => array(
							'type'    => 'select',
							'label'   => __( 'Breakpoint', 'fl-builder' ),
							'options' => array(
								''               => __( 'All', 'fl-builder' ),
								'desktop'        => __( 'Large Devices Only', 'fl-builder' ),
								'desktop-medium' => __( 'Large &amp; Medium Devices Only', 'fl-builder' ),
								'medium'         => __( 'Medium Devices Only', 'fl-builder' ),
								'medium-mobile'  => __( 'Medium &amp; Small Devices Only', 'fl-builder' ),
								'mobile'         => __( 'Small Devices Only', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'visibility_display'         => array(
							'type'    => 'select',
							'label'   => __( 'Display', 'fl-builder' ),
							'options' => array(
								''           => __( 'Always', 'fl-builder' ),
								'logged_out' => __( 'Logged Out User', 'fl-builder' ),
								'logged_in'  => __( 'Logged In User', 'fl-builder' ),
								'0'          => __( 'Never', 'fl-builder' ),
							),
							'toggle'  => array(
								'logged_in' => array(
									'fields' => array( 'visibility_user_capability' ),
								),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'visibility_user_capability' => array(
							'type'        => 'text',
							'label'       => __( 'User Capability', 'fl-builder' ),
							/* translators: %s: wporg docs link */
							'description' => sprintf( __( 'Optional. Set the <a%s>capability</a> required for users to view this row.', 'fl-builder' ), ' href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table" target="_blank"' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
					),
				),
				'animation'     => array(
					'title'  => __( 'Animation', 'fl-builder' ),
					'fields' => array(
						'animation' => array(
							'type'    => 'animation',
							'label'   => __( 'Animation', 'fl-builder' ),
							'preview' => array(
								'type'     => 'animation',
								'selector' => '{node}',
							),
						),
					),
				),
				'css_selectors' => array(
					'title'  => __( 'HTML Element', 'fl-builder' ),
					'fields' => array(
						'container_element' => array(
							'type'    => 'select',
							'label'   => __( 'Container Element', 'fl-builder' ),
							'default' => apply_filters( 'fl_builder_row_container_element_default', 'div' ),
							/**
							 * Filter to add/remove container types.
							 * @see fl_builder_node_container_element_options
							 */
							'options' => apply_filters( 'fl_builder_node_container_element_options', array(
								'div'     => '&lt;div&gt;',
								'section' => '&lt;section&gt;',
								'article' => '&lt;article&gt;',
								'aside'   => '&lt;aside&gt;',
								'main'    => '&lt;main&gt;',
								'header'  => '&lt;header&gt;',
								'footer'  => '&lt;footer&gt;',
							)),
							'help'    => __( 'Optional. Choose an appropriate HTML5 content sectioning element to use for this row to improve accessibility and machine-readability.', 'fl-builder' ),
							'preview' => array(
								'type' => 'none',
							),
						),
						'id'                => array(
							'type'    => 'text',
							'label'   => __( 'ID', 'fl-builder' ),
							'help'    => __( "A unique ID that will be applied to this row's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. No spaces.", 'fl-builder' ),
							'preview' => array(
								'type' => 'none',
							),
						),
						'class'             => array(
							'type'    => 'text',
							'label'   => __( 'Class', 'fl-builder' ),
							'help'    => __( "A class that will be applied to this row's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. Separate multiple classes with spaces.", 'fl-builder' ),
							'preview' => array(
								'type' => 'none',
							),
						),
						'node_label'        => array(
							'type'     => 'text',
							'label'    => __( 'Label', 'fl-builder' ),
							'help'     => __( 'A label that will applied and used in the UI for easy identification.', 'fl-builder' ),
							'sanitize' => 'strip_tags',
							'preview'  => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
	),
);

// Merge Shape Layer Sections
$style_sections                            = $row_settings['tabs']['style']['sections'];
$shape_sections                            = FLBuilderArt::get_shape_settings_sections();
$row_settings['tabs']['style']['sections'] = array_merge( $style_sections, $shape_sections );

// Register
FLBuilder::register_settings_form( 'row', $row_settings );
