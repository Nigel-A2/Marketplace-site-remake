<?php

/**
 * @class FLPhotoModule
 */
class FLPhotoModule extends FLBuilderModule {

	/**
	 * @property $data
	 */
	public $data = null;

	/**
	 * @property $_editor
	 * @protected
	 */
	protected $_editor = null;

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Photo', 'fl-builder' ),
			'description'     => __( 'Upload a photo or display one from the media library.', 'fl-builder' ),
			'category'        => __( 'Basic', 'fl-builder' ),
			'icon'            => 'format-image.svg',
			'partial_refresh' => true,
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

		// Handle old link fields.
		if ( isset( $settings->link_target ) ) {
			$settings->link_url_target = $settings->link_target;
			unset( $settings->link_target );
		}
		if ( isset( $settings->link_nofollow ) ) {
			$settings->link_url_nofollow = $settings->link_nofollow;
			unset( $settings->link_nofollow );
		}

		return $settings;
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		$override_lightbox = apply_filters( 'fl_builder_override_lightbox', false );

		if ( $this->settings && 'lightbox' == $this->settings->link_type ) {
			if ( ! $override_lightbox ) {
				$this->add_js( 'jquery-magnificpopup' );
				$this->add_css( 'font-awesome-5' );
				$this->add_css( 'jquery-magnificpopup' );
			} else {
				wp_dequeue_script( 'jquery-magnificpopup' );
				wp_dequeue_style( 'jquery-magnificpopup' );
			}
		}
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		// Make sure we have a photo_src property.
		if ( ! isset( $settings->photo_src ) ) {
			$settings->photo_src = '';
		}

		// Cache the attachment data.
		$settings->data = FLBuilderPhoto::get_attachment_data( $settings->photo );

		// Save a crop if necessary.
		$this->crop();

		return $settings;
	}

	/**
	 * @method delete
	 */
	public function delete() {
		$cropped_path = $this->_get_cropped_path();

		if ( fl_builder_filesystem()->file_exists( $cropped_path['path'] ) ) {
			fl_builder_filesystem()->unlink( $cropped_path['path'] );
		}
	}

	/**
	 * @method crop
	 */
	public function crop() {
		// Delete an existing crop if it exists.
		$this->delete();

		// Do a crop.
		if ( ! empty( $this->settings->crop ) ) {

			$editor = $this->_get_editor();

			if ( ! $editor || is_wp_error( $editor ) ) {
				return false;
			}

			$cropped_path = $this->_get_cropped_path();
			$size         = $editor->get_size();
			$new_width    = $size['width'];
			$new_height   = $size['height'];

			// Get the crop ratios.
			if ( 'landscape' == $this->settings->crop ) {
				$ratio_1 = 1.43;
				$ratio_2 = .7;
			} elseif ( 'panorama' == $this->settings->crop ) {
				$ratio_1 = 2;
				$ratio_2 = .5;
			} elseif ( 'portrait' == $this->settings->crop ) {
				$ratio_1 = .7;
				$ratio_2 = 1.43;
			} elseif ( 'square' == $this->settings->crop ) {
				$ratio_1 = 1;
				$ratio_2 = 1;
			} elseif ( 'circle' == $this->settings->crop ) {
				$ratio_1 = 1;
				$ratio_2 = 1;
			}

			// Get the new width or height.
			if ( $size['width'] / $size['height'] < $ratio_1 ) {
				$new_height = $size['width'] * $ratio_2;
			} else {
				$new_width = $size['height'] * $ratio_1;
			}

			// Make sure we have enough memory to crop.
			try {
				ini_set( 'memory_limit', '300M' );
			} catch ( Exception $e ) {
				//
			}

			// Crop the photo.
			$editor->resize( $new_width, $new_height, true );

			// Save the photo.
			$editor->save( $cropped_path['path'] );

			/**
			 * Let third party media plugins hook in.
			 * @see fl_builder_photo_cropped
			 */
			do_action( 'fl_builder_photo_cropped', $cropped_path, $editor );

			// Return the new url.
			return $cropped_path['url'];
		}

		return false;
	}

	/**
	 * @method get_data
	 */
	public function get_data() {
		if ( ! $this->data ) {

			// Photo source is set to "url".
			if ( 'url' == $this->settings->photo_source ) {
				$this->data                = new stdClass();
				$this->data->alt           = $this->settings->caption;
				$this->data->caption       = $this->settings->caption;
				$this->data->link          = $this->settings->photo_url;
				$this->data->url           = $this->settings->photo_url;
				$this->settings->photo_src = $this->settings->photo_url;
				$this->data->title         = ( '' !== $this->settings->url_title ) ? $this->settings->url_title : basename( $this->settings->photo_url );
			} elseif ( is_object( $this->settings->photo ) ) {
				$this->data = $this->settings->photo;
			} else {
				$this->data = FLBuilderPhoto::get_attachment_data( $this->settings->photo );
			}

			// Data object is empty, use the settings cache.
			if ( ! $this->data && isset( $this->settings->data ) ) {
				$this->data = $this->settings->data;
			}
		}
		/**
		 * Make photo data filterable.
		 * @since 2.2.6
		 * @see fl_builder_photo_data
		 */
		return apply_filters( 'fl_builder_photo_data', $this->data, $this->settings, $this->node );
	}

	/**
	 * @method get_classes
	 */
	public function get_classes() {
		$classes = array( 'fl-photo-img' );

		if ( 'library' == $this->settings->photo_source && ! empty( $this->settings->photo ) ) {

			$data = self::get_data();

			if ( is_object( $data ) ) {

				if ( isset( $data->id ) ) {
					$classes[] = 'wp-image-' . $data->id;
				}

				$is_svg = ! empty( $data->mime ) && 'image/svg+xml' === $data->mime;

				if ( $is_svg ) {
					$classes[] = 'size-full';
				}

				if ( isset( $data->sizes ) && ! $is_svg ) {
					foreach ( $data->sizes as $key => $size ) {
						if ( $size->url == $this->settings->photo_src ) {
							$classes[] = 'size-' . $key;
							break;
						}
					}
				}
			}
		}

		return implode( ' ', $classes );
	}

	/**
	 * @method get_src
	 */
	public function get_src() {
		$src = $this->_get_uncropped_url();

		// Return a cropped photo.
		if ( $this->_has_source() && ! empty( $this->settings->crop ) ) {

			$cropped_path = $this->_get_cropped_path();

			if ( fl_builder_filesystem()->file_exists( $cropped_path['path'] ) ) {
				// An existing cropped photo exists.
				$src = $cropped_path['url'];
			} else {

				// A cropped photo doesn't exist, check demo sites then try to create one.
				$post_data    = FLBuilderModel::get_post_data();
				$editing_node = isset( $post_data['node_id'] );
				$demo_domain  = FL_BUILDER_DEMO_DOMAIN;

				if ( ! $editing_node && stristr( $src, $demo_domain ) && ! stristr( $_SERVER['HTTP_HOST'], $demo_domain ) ) {
					$src = $this->_get_cropped_demo_url();
				} elseif ( ! $editing_node && stristr( $src, FL_BUILDER_OLD_DEMO_URL ) ) {
					$src = $this->_get_cropped_demo_url();
				} else {
					$url = $this->crop();
					$src = $url ? $url : $src;
				}
			}
		}
		return $src;
	}

	/**
	 * @method get_link
	 */
	public function get_link() {
		$photo = $this->get_data();

		if ( 'url' == $this->settings->link_type ) {
			$link = $this->settings->link_url;
		} elseif ( isset( $photo ) && 'lightbox' == $this->settings->link_type ) {
			$link = $photo->url;
		} elseif ( isset( $photo ) && 'file' == $this->settings->link_type ) {
			$link = $photo->url;
		} elseif ( isset( $photo ) && 'page' == $this->settings->link_type ) {
			$link = $photo->link;
		} else {
			$link = '';
		}

		return $link;
	}

	/**
	 * @method get_alt
	 */
	public function get_alt() {
		$photo = $this->get_data();

		if ( ! empty( $photo->alt ) ) {
			return htmlspecialchars( $photo->alt );
		} elseif ( ! empty( $photo->description ) ) {
			return htmlspecialchars( $photo->description );
		} elseif ( ! empty( $photo->caption ) ) {
			return htmlspecialchars( $photo->caption );
		} elseif ( ! empty( $photo->title ) ) {
			return htmlspecialchars( $photo->title );
		}
	}

	/**
	 * @method get_caption
	 */
	public function get_caption() {
		$photo   = $this->get_data();
		$caption = '';

		if ( $photo && ! empty( $this->settings->show_caption ) && ! empty( $photo->caption ) ) {
			if ( ! empty( $photo->data_source ) && 'smugmug' === $photo->data_source ) {
				$caption = esc_html( $photo->caption );
			} elseif ( isset( $photo->id ) ) {
				$caption = wp_kses_post( wp_get_attachment_caption( $photo->id ) );
			} else {
				$caption = esc_html( $photo->caption );
			}
		}

		return $caption;
	}

	/**
	 * @method get_attributes
	 */
	public function get_attributes() {
		$photo = $this->get_data();
		$attrs = '';

		if ( isset( $this->settings->attributes ) ) {
			foreach ( $this->settings->attributes as $key => $val ) {
				$attrs .= $key . '="' . $val . '" ';
			}
		}

		$is_svg = ! empty( $photo->mime ) && 'image/svg+xml' === $photo->mime;

		if ( $is_svg && isset( $photo->sizes ) ) {
			if ( $photo->sizes->full->height && $photo->sizes->full->width ) {
				$attrs .= 'height="' . $photo->sizes->full->height . '" width="' . $photo->sizes->full->width . '" ';
			}
		}

		if ( is_object( $photo ) && isset( $photo->sizes ) && ! $is_svg ) {
			foreach ( $photo->sizes as $size ) {
				if ( $size->url == $this->settings->photo_src && isset( $size->width ) && isset( $size->height ) ) {
					$attrs .= 'height="' . $size->height . '" width="' . $size->width . '" ';
				}
			}
		}

		if ( ! empty( $photo->title ) ) {
			$attrs .= 'title="' . htmlspecialchars( $photo->title ) . '" ';
		}

		if ( FLBuilderModel::is_builder_active() ) {
			$attrs .= 'onerror="this.style.display=\'none\'" ';
		}

		/**
		 * Filter image attributes as a string.
		 * @since 2.2.3
		 * @see fl_builder_photo_attributes
		 */
		return apply_filters( 'fl_builder_photo_attributes', $attrs );
	}

	/**
	 * @method _has_source
	 * @protected
	 */
	protected function _has_source() {
		if ( 'url' == $this->settings->photo_source && ! empty( $this->settings->photo_url ) ) {
			return true;
		} elseif ( 'library' == $this->settings->photo_source && ! empty( $this->settings->photo_src ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @method _get_editor
	 * @protected
	 */
	protected function _get_editor() {
		if ( $this->_has_source() && null === $this->_editor ) {

			$url_path  = $this->_get_uncropped_url();
			$file_path = $this->_get_file_path( $url_path );

			if ( is_multisite() && ! is_subdomain_install() ) {
				// take the original url_path and make a cleaner one, then rebuild file_path

				$subsite_path          = get_blog_details()->path;
				$url_parsed_path       = wp_parse_url( $url_path, PHP_URL_PATH );
				$url_parsed_path_parts = explode( '/', $url_parsed_path );

				if ( isset( $url_parsed_path_parts[1] ) && "/{$url_parsed_path_parts[1]}/" === $subsite_path ) {

					$path_right_half  = wp_make_link_relative( $url_path );
					$path_left_half   = str_replace( $path_right_half, '', $url_path );
					$path_right_half2 = str_replace( $subsite_path, '', $path_right_half );

					// rebuild file_path using a cleaner URL as input
					$url_path2 = $path_left_half . '/' . $path_right_half2;
					$file_path = $this->_get_file_path( $url_path2 );
				}
			}

			if ( file_exists( $file_path ) ) {
				$this->_editor = wp_get_image_editor( $file_path );
			} else {
				if ( ! is_wp_error( wp_safe_remote_head( $url_path, array( 'timeout' => 5 ) ) ) ) {
					$this->_editor = wp_get_image_editor( $url_path );
				}
			}
		}
		return $this->_editor;
	}

	/**
	 * Make path filterable.
	 * @since 2.5
	 */
	public static function _get_file_path( $url_path ) {
		return apply_filters( 'fl_builder_photo_crop_path', str_ireplace( home_url(), ABSPATH, $url_path ), $url_path );
	}

	/**
	 * @method _get_cropped_path
	 * @protected
	 */
	protected function _get_cropped_path() {
		$crop      = empty( $this->settings->crop ) ? 'none' : $this->settings->crop;
		$url       = $this->_get_uncropped_url();
		$cache_dir = FLBuilderModel::get_cache_dir();

		if ( empty( $url ) ) {
			$filename = FLBuilderModel::uniqid(); // Return a file that doesn't exist.
		} else {

			if ( stristr( $url, '?' ) ) {
				$parts = explode( '?', $url );
				$url   = $parts[0];
			}

			$pathinfo = pathinfo( $url );

			if ( isset( $pathinfo['extension'] ) ) {
				$dir      = $pathinfo['dirname'];
				$ext      = $pathinfo['extension'];
				$name     = wp_basename( $url, ".$ext" );
				$new_ext  = strtolower( $ext );
				$filename = "{$name}-{$crop}.{$new_ext}";
			} else {
				$filename = $pathinfo['filename'] . "-{$crop}.png";
			}
		}

		return array(
			'filename' => $filename,
			'path'     => $cache_dir['path'] . $filename,
			'url'      => $cache_dir['url'] . $filename,
		);
	}

	/**
	 * @method _get_uncropped_url
	 * @protected
	 */
	protected function _get_uncropped_url() {
		if ( 'url' == $this->settings->photo_source ) {
			$url = $this->settings->photo_url;
		} elseif ( ! empty( $this->settings->photo_src ) ) {
			$url = $this->settings->photo_src;
		} else {
			$url = apply_filters( 'fl_builder_photo_noimage', FL_BUILDER_URL . 'img/pixel.png' );
		}

		return $url;
	}

	/**
	 * @method _get_cropped_demo_url
	 * @protected
	 */
	protected function _get_cropped_demo_url() {
		$info = $this->_get_cropped_path();
		$src  = $this->settings->photo_src;

		// Pull from a demo subsite.
		if ( stristr( $src, '/uploads/sites/' ) ) {
			$url_parts  = explode( '/uploads/sites/', $src );
			$site_parts = explode( '/', $url_parts[1] );
			return $url_parts[0] . '/uploads/sites/' . $site_parts[0] . '/bb-plugin/cache/' . $info['filename'];
		}

		// Pull from the demo main site.
		return FL_BUILDER_DEMO_CACHE_URL . $info['filename'];
	}

	/**
	 * Returns link rel
	 * @since 2.0.6
	 */
	public function get_rel() {
		$rel = array();
		if ( '_blank' == $this->settings->link_url_target ) {
			$rel[] = 'noopener';
		}
		if ( isset( $this->settings->link_url_nofollow ) && 'yes' == $this->settings->link_url_nofollow ) {
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
FLBuilder::register_module('FLPhotoModule', array(
	'general' => array( // Tab
		'title'    => __( 'General', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'photo_source' => array(
						'type'    => 'select',
						'label'   => __( 'Photo Source', 'fl-builder' ),
						'default' => 'library',
						'options' => array(
							'library' => __( 'Media Library', 'fl-builder' ),
							'url'     => __( 'URL', 'fl-builder' ),
						),
						'toggle'  => array(
							'library' => array(
								'fields' => array( 'photo' ),
							),
							'url'     => array(
								'fields' => array( 'photo_url', 'caption', 'url_title' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'photo'        => array(
						'type'        => 'photo',
						'label'       => __( 'Photo', 'fl-builder' ),
						'connections' => array( 'photo' ),
						'show_remove' => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'photo_url'    => array(
						'type'        => 'text',
						'label'       => __( 'Photo URL', 'fl-builder' ),
						'placeholder' => __( 'http://www.example.com/my-photo.jpg', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'title_hover'  => array(
						'type'    => 'select',
						'label'   => __( 'Show title attribute on mouse hover', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
					),
					'url_title'    => array(
						'type'        => 'text',
						'label'       => __( 'Image title attribute', 'fl-builder' ),
						'default'     => '',
						'placeholder' => __( 'Use image filename if left blank', 'fl-builder' ),
					),
				),
			),
			'caption' => array(
				'title'  => __( 'Caption', 'fl-builder' ),
				'fields' => array(
					'show_caption' => array(
						'type'    => 'select',
						'label'   => __( 'Show Caption', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0'     => __( 'Never', 'fl-builder' ),
							'hover' => __( 'On Hover', 'fl-builder' ),
							'below' => __( 'Below Photo', 'fl-builder' ),
						),

						'toggle'  => array(
							''      => array(),
							'hover' => array(
								'fields' => array( 'caption_typography' ),
							),

							'below' => array(
								'fields' => array( 'caption_typography' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'caption'      => array(
						'type'    => 'text',
						'label'   => __( 'Caption', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'link'    => array(
				'title'  => __( 'Link', 'fl-builder' ),
				'fields' => array(
					'link_type' => array(
						'type'    => 'select',
						'label'   => __( 'Link Type', 'fl-builder' ),
						'options' => array(
							''         => _x( 'None', 'Link type.', 'fl-builder' ),
							'url'      => __( 'URL', 'fl-builder' ),
							'lightbox' => __( 'Lightbox', 'fl-builder' ),
							'file'     => __( 'Photo File', 'fl-builder' ),
							'page'     => __( 'Photo Page', 'fl-builder' ),
						),
						'toggle'  => array(
							''     => array(),
							'url'  => array(
								'fields' => array( 'link_url' ),
							),
							'file' => array(),
							'page' => array(),
						),
						'help'    => __( 'Link type applies to how the image should be linked on click. You can choose a specific URL, the individual photo or a separate page with the photo.', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'link_url'  => array(
						'type'          => 'link',
						'label'         => __( 'Link URL', 'fl-builder' ),
						'show_target'   => true,
						'show_nofollow' => true,
						'preview'       => array(
							'type' => 'none',
						),
						'connections'   => array( 'url' ),
					),
				),
			),
		),
	),
	'style'   => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'crop'               => array(
						'type'    => 'select',
						'label'   => __( 'Crop', 'fl-builder' ),
						'default' => '',
						'options' => array(
							''          => _x( 'None', 'Photo Crop.', 'fl-builder' ),
							'landscape' => __( 'Landscape', 'fl-builder' ),
							'panorama'  => __( 'Panorama', 'fl-builder' ),
							'portrait'  => __( 'Portrait', 'fl-builder' ),
							'square'    => __( 'Square', 'fl-builder' ),
							'circle'    => __( 'Circle', 'fl-builder' ),
						),
					),
					'width'              => array(
						'type'       => 'unit',
						'label'      => __( 'Width', 'fl-builder' ),
						'responsive' => true,
						'units'      => array(
							'px',
							'vw',
							'%',
						),
						'slider'     => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 10,
							),
						),
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-photo-img',
							'property'  => 'width',
							'important' => true,
						),
					),
					'align'              => array(
						'type'       => 'align',
						'label'      => __( 'Align', 'fl-builder' ),
						'default'    => 'center',
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-photo',
							'property'  => 'text-align',
							'important' => true,
						),
					),
					'border'             => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-photo-img',
						),
					),

					'caption_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Caption Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '{node}.fl-module-photo .fl-photo-caption',
							'important' => true,
						),
					),
				),
			),
		),
	),
));
