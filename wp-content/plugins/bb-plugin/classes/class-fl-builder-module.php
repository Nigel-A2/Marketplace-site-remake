<?php

/**
 * Base class that gets extended by all module classes.
 *
 * @since 1.0
 */
class FLBuilderModule {

	/**
	 * A unique ID for the module.
	 *
	 * @since 1.0
	 * @var string $node
	 */
	public $node;

	/**
	 * A unique ID for the module's parent.
	 *
	 * @since 1.0
	 * @var int $parent
	 */
	public $parent;

	/**
	 * The sort order for this module.
	 *
	 * @since 1.0
	 * @var int $position
	 */
	public $position;

	/**
	 * A display name for the module.
	 *
	 * @since 1.0
	 * @var string $name
	 */
	public $name;

	/**
	 * A description to display for the module.
	 *
	 * @since 1.0
	 * @var string $description
	 */
	public $description;

	/**
	 * The category this module belongs to.
	 *
	 * @since 1.0
	 * @var string $category
	 */
	public $category;

	/**
	 * Must be the module's folder name.
	 *
	 * @since 1.0
	 * @var string $slug
	 */
	public $slug;

	/**
	 * The module's directory path.
	 *
	 * @since 1.0
	 * @var string $dir
	 */
	public $dir;

	/**
	 * The module's directory url.
	 *
	 * @since 1.0
	 * @var string $url
	 */
	public $url;

	/**
	 * An array of form settings.
	 *
	 * @since 1.0
	 * @var array $form
	 */
	public $form = array();

	/**
	 * Whether this module is enabled on the
	 * frontend or not.
	 *
	 * @since 1.0
	 * @var boolean $enabled
	 */
	public $enabled = true;

	/**
	 * Whether this module's content should
	 * be exported to the WP editor or not.
	 *
	 * @since 1.0
	 * @var boolean $editor_export
	 */
	public $editor_export = true;

	/**
	 * Whether partial refresh should be enabled
	 * for this module or not.
	 *
	 * @since 1.7
	 * @var boolean $partial_refresh
	 */
	public $partial_refresh = false;

	/**
	 * The module settings object.
	 *
	 * @since 1.0
	 * @var object $settings
	 */
	public $settings;

	/**
	 * Additional CSS to enqueue.
	 *
	 * @since 1.0
	 * @var array $css
	 */
	public $css = array();

	/**
	 * Additional JS to enqueue.
	 *
	 * @since 1.0
	 * @var array $js
	 */
	public $js = array();

	/**
	 * The class of the font icon for this module.
	 *
	 * @since 2.0
	 */
	public $icon = '';

	/**
	 * Module constructor.
	 *
	 * @since 1.0
	 */
	public function __construct( $params ) {
		$class_info            = new ReflectionClass( $this );
		$class_path            = $class_info->getFileName();
		$dir_path              = dirname( $class_path );
		$this->slug            = basename( $class_path, '.php' );
		$this->enabled         = isset( $params['enabled'] ) ? $params['enabled'] : true;
		$this->editor_export   = isset( $params['editor_export'] ) ? $params['editor_export'] : true;
		$this->partial_refresh = isset( $params['partial_refresh'] ) ? $params['partial_refresh'] : false;

		// We need to normalize the paths here since path comparisons
		// break on Windows because they use backslashes.
		$abspath                  = str_replace( '\\', '/', ABSPATH );
		$fl_builder_dir           = str_replace( '\\', '/', FL_BUILDER_DIR );
		$dir_path                 = str_replace( '\\', '/', $dir_path );
		$stylesheet_directory     = str_replace( '\\', '/', get_stylesheet_directory() );
		$stylesheet_directory_uri = str_replace( '\\', '/', get_stylesheet_directory_uri() );
		$template_directory       = str_replace( '\\', '/', get_template_directory() );
		$template_directory_uri   = str_replace( '\\', '/', get_template_directory_uri() );

		// Find the right paths.
		if ( is_child_theme() && stristr( $dir_path, $stylesheet_directory ) ) {
			$this->url = trailingslashit( str_replace( $stylesheet_directory, $stylesheet_directory_uri, $dir_path ) );
			$this->dir = trailingslashit( $dir_path );
		} elseif ( stristr( $dir_path, $template_directory ) ) {
			$this->url = trailingslashit( str_replace( $template_directory, $template_directory_uri, $dir_path ) );
			$this->dir = trailingslashit( $dir_path );
		} elseif ( isset( $params['url'] ) && isset( $params['dir'] ) ) {
			$this->url = trailingslashit( $params['url'] );
			$this->dir = trailingslashit( $params['dir'] );
		} elseif ( ! stristr( $dir_path, $fl_builder_dir ) ) {
			$this->url = trailingslashit( str_replace( trailingslashit( $abspath ), trailingslashit( home_url() ), $dir_path ) );
			$this->dir = trailingslashit( $dir_path );
		} else {
			$this->url = trailingslashit( FL_BUILDER_URL . 'modules/' . $this->slug );
			$this->dir = trailingslashit( FL_BUILDER_DIR . 'modules/' . $this->slug );
		}
		// Icon requires dir be defined before calling get_icon()
		$this->icon = isset( $params['icon'] ) ? $this->get_icon( $params['icon'] ) : $this->get_icon();

		$details = apply_filters( 'fl_builder_module_details', array(
			'name'        => $params['name'],
			'description' => $params['description'],
			'category'    => $this->normalize_category_name( $params['category'] ),
			'group'       => isset( $params['group'] ) ? $params['group'] : false,
			'icon'        => $this->icon,
		), $this->slug );

		$this->name        = $details['name'];
		$this->description = $details['description'];
		$this->category    = $details['category'];
		$this->group       = $details['group'];
		$this->icon        = $details['icon'];
	}

	/**
	 * Used to enqueue additional frontend styles. Do not enqueue
	 * frontend.css or frontend.responsive.css as those will be
	 * enqueued automatically. Params are the same as those used in
	 * WordPress' wp_enqueue_style function.
	 *
	 * @since 1.0
	 * @param string $handle
	 * @param string $src
	 * @param array $deps
	 * @param string $ver
	 * @param string $media
	 * @return void
	 */
	public function add_css( $handle, $src = null, $deps = null, $ver = null, $media = null ) {
		$this->css[ $handle ] = array( $src, $deps, $ver, $media );
	}

	/**
	 * Used to enqueue additional frontend scripts. Do not enqueue
	 * frontend.js as that will be enqueued automatically. Params
	 * are the same as those used in WordPress' wp_enqueue_script function.
	 *
	 * @since 1.0
	 * @param string $handle
	 * @param string $src
	 * @param array $deps
	 * @param string $ver
	 * @param bool $in_footer
	 * @return void
	 */
	public function add_js( $handle, $src = null, $deps = null, $ver = null, $in_footer = null ) {
		$this->js[ $handle ] = array( $src, $deps, $ver, $in_footer );
	}

	/**
	 * Enqueues the needed styles for any icon fields
	 * in this module.
	 *
	 * @since 1.4.6
	 * @return void
	 */
	public function enqueue_icon_styles() {
		FLBuilderIcons::enqueue_styles_for_module( $this );
	}

	/**
	 * Enqueues the needed styles for any font fields
	 * in this module.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	public function enqueue_font_styles() {
		FLBuilderFonts::add_fonts_for_module( $this );
	}

	/**
	 * Should be overridden by subclasses to enqueue
	 * additional css/js using the add_css and add_js methods.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function enqueue_scripts() {

	}

	/**
	 * Should be overridden by subclasses to
	 * work with settings data _before it is saved_.
	 *
	 * @since 1.0
	 * @param object $settings A settings object that is going to be saved.
	 * @return object
	 */
	public function update( $settings ) {
		return $settings;
	}

	/**
	 * Should be overridden by subclasses to
	 * work with settings data _before it is used to display a module_.
	 *
	 * @since 2.0.3
	 * @param object $settings A settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {
		return $settings;
	}

	/**
	 * Should be overridden by subclasses to work with a module before
	 * it is deleted. Please note, this method is called when a module
	 * is updated and when it's actually removed from the page and should
	 * be used for things like clearing photo cache from the builder's
	 * cache directory. If only need to run logic when a module is
	 * actually removed from the page, use the remove method instead.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function delete() {

	}

	/**
	 * Should be overridden by subclasses to work with a module when
	 * it is actually removed from the page.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function remove() {

	}

	/**
	 * Get svg icon string
	 *
	 * @since 2.0
	 * @return String
	 */
	public function get_icon( $icon = '' ) {

		// check if $icon is referencing an included icon.
		if ( '' != $icon && file_exists( FL_BUILDER_DIR . 'img/svg/' . $icon ) ) {
			$path = FL_BUILDER_DIR . 'img/svg/' . $icon;

			// check if module directory includes an icon.svg file
		} elseif ( file_exists( $this->dir . 'icon.svg' ) ) {
			$path = $this->dir . 'icon.svg';

			// default to included icon
		} else {
			$path = FL_BUILDER_DIR . 'img/svg/insert.svg';
		}
		if ( file_exists( $path ) ) {
			return file_get_contents( $path );
		} else {
			return '';
		}
	}

	/**
	 * Normalizes category names to support 2.0 since the default
	 * category names changed.
	 *
	 * @since 2.0
	 * @access private
	 * @param string $cat
	 * @return string
	 */
	private function normalize_category_name( $cat ) {
		if ( __( 'Basic Modules', 'fl-builder' ) === $cat ) {
			$cat = __( 'Basic', 'fl-builder' );
		} elseif ( __( 'Advanced Modules', 'fl-builder' ) === $cat ) {
			$cat = __( 'Advanced', 'fl-builder' );
		}
		return $cat;
	}

	/**
	 * Get the default svg icon
	 *
	 * @since 2.0
	 * @return String
	 */
	static public function get_default_icon() {
		$path = FL_BUILDER_DIR . 'img/svg/insert.svg';
		return file_get_contents( $path );
	}

	/**
	 * Get the widget icon
	 *
	 * @since 2.0
	 * @return String
	 */
	static public function get_widget_icon() {
		$path = FL_BUILDER_DIR . 'img/svg/wordpress-alt.svg';
		return file_get_contents( $path );
	}
}
