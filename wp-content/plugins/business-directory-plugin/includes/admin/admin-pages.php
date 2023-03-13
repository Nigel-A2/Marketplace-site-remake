<?php

/*
 * @since 6.0
 */
class WPBDP_Admin_Pages {

	/*
	 * Register hooks for the CPT, category and tags page.
	 *
	 * @since 6.0
	 */
	public static function load_hooks() {
		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'views_edit-wpbdp_listing', __CLASS__ . '::add_listings_nav' );

		add_action( 'init', __CLASS__ . '::load_taxonomy_menus', 30 );

		// Add search form.
		add_action( 'wpbdp_admin_pages_show_tabs', __CLASS__ . '::taxonomy_search_form', 10, 2 );

		add_action( 'wpbdp_category_add_form_fields', __CLASS__ . '::add_category_info', 9999 );
	}

	/**
	 * @since 6.0.1
	 */
	public static function load_taxonomy_menus() {
		foreach ( self::get_tax_types() as $tax_type ) {
			// Listing page.
			add_filter( 'views_edit-' . $tax_type, __CLASS__ . '::add_taxonomy_nav', 1 );

			// Edit page.
			add_filter( $tax_type . '_pre_edit_form', __CLASS__ . '::edit_tag_nav' );
		}
	}

	/**
	 * @since 6.0.1
	 */
	private static function get_tax_types() {
		/**
		 * @since 6.0.1
		 */
		return apply_filters( 'wpbdp_tax_types', array( 'wpbdp_tag', 'wpbdp_category', 'wpbdm-region' ) );
	}

	/**
	 * Add listing nav header to the post listing page.
	 *
	 * @since 6.0
	 */
	public static function add_listings_nav( $views ) {
		global $post_type_object;
		add_action( 'admin_footer', 'WPBDP_Admin_Pages::show_full_footer' );

		$args = array(
			'sub'        => __( 'Listings', 'business-directory-plugin' ),
			'active_tab' => 'edit.php?post_type=wpbdp_listing',
		);

		if ( current_user_can( $post_type_object->cap->create_posts ) ) {
			$args['buttons'] = array(
				'add_listing' => array(
					'label' => __( 'Add New Listing', 'business-directory-plugin' ),
					'url'   => esc_url( admin_url( 'post-new.php?post_type=wpbdp_listing' ) ),
				),
			);
		}

		self::show_tabs( $args );

		return $views;
	}

	/**
	 * Add listing tags nav.
	 *
	 * @since 6.0
	 */
	public static function edit_tag_nav( $views ) {
		$atts = array(
			'edit'  => true,
		);
		return self::add_taxonomy_nav( $views, $atts );
	}


	/**
	 * Add taxonomy navigation.
	 * Public function to be used in addons that have custom tags.
	 *
	 * @since 6.0
	 */
	public static function add_taxonomy_nav( $views, $params = array() ) {
		global $tax;

		add_action( 'admin_footer', 'WPBDP_Admin_Pages::show_full_footer' );

		// Prevent this from running a second time.
		remove_filter( 'views_edit-' . $tax->name, __CLASS__ . '::add_taxonomy_nav', 1 );
		remove_filter( $tax->name . '_pre_edit_form', __CLASS__ . '::edit_tag_nav' );

		$editing    = isset( $params['edit'] );
		$tax_name   = str_replace( 'Directory ', '', $tax->labels->all_items );
		$all_url    = 'edit-tags.php?taxonomy=' . $tax->name . '&amp;post_type=wpbdp_listing';

		if ( $editing ) {
			$title      = $tax->labels->edit_item;
			$button     = sprintf( __( 'Back to %s', 'business-directory-plugin' ), $tax_name );
			$button_url = admin_url( $all_url );
			$button_class = '';
		} else {
			$title      = $tax_name;
			$button     = $tax->labels->add_new_item;
			$button_url = '#';
			$button_class = 'wpbdp-add-taxonomy-form';
		}

		$args = array(
			'sub'        => $title,
			'active_tab' => $all_url,
			'id'         => $tax->name,
		);

		if ( current_user_can( $tax->cap->edit_terms ) ) {
			$args['buttons'] = array(
				'add_new' => array(
					'label' => $button,
					'url'   => $button_url,
					'class' => $button_class,
				),
			);
		}

		self::show_tabs( $args );

		return $views;
	}

	/**
	 * Search form for taxonomies.
	 *
	 * @since 6.0
	 */
	public static function taxonomy_search_form( $active_tab, $id ) {
		$active_screens = array();
		foreach ( self::get_tax_types() as $tax_type ) {
			$active_screens[] = 'edit-' . $tax_type;
		}

		$current_screen = get_current_screen();
		if ( ! in_array( $current_screen->id, $active_screens, true ) ) {
			return;
		}

		$tag_id = wpbdp_get_var( array( 'param' => 'tag_ID' ), 'get' );
		if ( $tag_id ) {
			return;
		}

		global $post_type, $taxonomy, $tax, $wp_list_table;
		$search_param = wpbdp_get_var( array( 'param' => 's' ), 'request' );
		if ( $search_param ) {
			echo '<span class="wpbdp-taxonomy-search-results">';
			printf(
				/* translators: %s: Search query. */
				__( 'Search results for: %s', 'business-directory-plugin' ),
				'<strong>' . esc_html( $search_param ) . '</strong>'
			);
			echo '</span>';
		}
		?>
		<form class="search-form wp-clearfix" method="get">
			<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />

			<?php $wp_list_table->search_box( $tax->labels->search_items, 'tag' ); ?>
		</form>
		<?php
	}

	/**
	 * Add info about category images.
	 *
	 * @since 6.0
	 */
	public static function add_category_info() {
		echo '<div class="form-field">';
		WPBDP_Admin_Education::show_tip( 'categories' );
		echo '</div>';
	}

	/**
	 * Admin header.
	 *
	 * @since 6.0
	 */
	public static function show_tabs( $args = array() ) {

		$defaults = array(
			'title'        => 'Business Directory', // Don't translate this.
			'id'           => wpbdp_get_var( array( 'param' => 'page' ) ),
			'tabs'         => array(),
			'buttons'      => array(),
			'active_tab'   => self::get_active_tab(),
			'show_nav'     => true,
			'tabbed_title' => false,
			'titles'       => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$id = str_replace( array( 'wpbdp_', 'wpbdp-' ), '', $args['id'] );
		$id = str_replace( array( 'admin-', 'admin_' ), '', $id );

		$active_tab = $args['active_tab'];
		$tabs       = $args['tabs'];
		if ( $tabs === 'settings' ) {
			$tabs = self::get_settings_tabs();
		} elseif ( $tabs === 'content' || empty( $tabs ) ) {
			$tabs = self::get_content_tabs();
		}

		/**
		 * @since 6.0.1
		 */
		$tabs = apply_filters( 'wpbdp_tab_content', $tabs, array( 'settings' => ! empty( $args['tabs'] ) ) );
		if ( empty( $tabs ) ) {
			return;
		}
		self::add_icon_url( $tabs );

		$title = $args['title'];
	?>
	<div class="wrap wpbdp-admin wpbdp-admin-layout wpbdp-admin-page wpbdp-admin-page-<?php echo esc_attr( $id ); ?> <?php echo ! $args['show_nav'] ? 'wpbdp-admin-page-full-width' : ''; ?>" id="wpbdp-admin-page-<?php echo esc_attr( $id ); ?>">
		<div class="wpbdp-admin-row">
			<?php
			if ( $args['show_nav'] ) {
				if ( isset( $tabs['wpbdp-admin-fees'] ) ) {
					// This is a content page.
					echo '<script>var wpbdpSelectNav = 1;</script>';
				}
				include WPBDP_PATH . 'templates/admin/_admin-menu.php';
			}
			?>
			<div class="wpbdp-content-area">
			<?php
			wpbdp_admin_notices();
			if ( ! isset( $args['sub'] ) ) {
				return;
			}
			?>
			<div class="wpbdp-content-area-header <?php echo $args['tabbed_title'] ? 'wpbdp-content-area-header-tabbed' : '' ?>">
				<?php if ( $args['tabbed_title'] ) :
					$current_tab = isset( $args['current_tab'] ) ? $args['current_tab'] : '';
					self::show_tabbed_title( $args['titles'], $current_tab );
				else : ?>
				<h2 class="wpbdp-sub-section-title"><?php echo esc_html( $args['sub'] ); ?></h2>
				<?php endif; ?>
				<div class="wpbdp-content-area-header-actions">
					<?php self::show_buttons( $args['buttons'] ); ?>
				</div>
			</div>
			<div class="wpbdp-content-area-body">
			<?php
			do_action( 'wpbdp_admin_pages_show_tabs', $active_tab, $id );
	}

	/**
	 * Show action buttons at the top of the page.
	 *
	 * @since 6.0
	 */
	private static function show_buttons( $buttons ) {
		$button_class = 'wpbdp-button-primary';
		foreach ( $buttons as $id => $button ) {
			if ( ! is_array( $button ) ) {
				$button = array(
					'url'   => $button,
					'label' => $id,
				);
			}
			?>
			<a href="<?php echo esc_url( $button['url'] ); ?>" class="<?php echo esc_attr( $button_class . ( isset( $button['class'] ) ? ' ' . $button['class'] : '' ) ); ?>">
				<?php echo esc_html( $button['label'] ); ?>
			</a>
			<?php
			$button_class = 'wpbdp-button-secondary';
		}
	}

	/**
	 * Includes the end div for the tabs section.
	 *
	 * @since 6.0
	 */
	public static function show_full_footer() {
		self::show_tabs_footer( array( 'sub' => true ) );
	}

	/**
	 * @since 6.0
	 */
	public static function show_tabs_footer( $args = array() ) {
		if ( isset( $args['sub'] ) ) {
			echo '</div>'; // end wpbdp-content-area-body
		}
		echo '</div>'; // end wpbdp-content-area
		echo '</div></div>'; // end wpbdp-admin & wpbdp-admin-row
	}

	/**
	 * Admin title.
	 *
	 * @since 6.0
	 */
	public static function show_title( $args = array() ) {
		$title = isset( $args['title'] ) ? $args['title'] : '';
		$title = self::get_title( $title );

		$buttons = isset( $args['buttons'] ) ? $args['buttons'] : array();

		?>
		<h1 class="wpbdp-page-title">
			<?php
			WPBDP_App_Helper::show_logo(
				array(
					'round' => true,
					'class' => 'wpbdp-logo-center',
					'size'  => 35,
				)
			);
			?>
			<span class="title-text"><?php echo esc_html( $title ); ?></span>

			<?php foreach ( $buttons as $label => $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="add-new-h2">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</h1>
		<?php
	}

	/**
	 * Admin tabbed title.
	 *
	 * @param array $titles The titles as an array.
	 * @param string $current_tab The current selected tab.
	 *
	 * @since 6.0
	 */
	public static function show_tabbed_title( $titles, $current_tab = '' ) {
		?>
		<div class="wpbdp-content-area-header-tabs">
		<?php
		foreach ( $titles as $key => $title ) : ?>
			<a class="wpbdp-header-tab <?php echo $key === $current_tab ? 'wpbdp-header-tab-active' : ''; ?>" href="<?php echo esc_url( $title['url'] ); ?>"><?php echo esc_html( $title['name'] ); ?></a>
		<?php endforeach;
		?>
		</div>
		<?php
	}

	/**
	 * @since 6.0
	 */
	private static function get_title( $title = '' ) {
		if ( $title ) {
			return $title;
		}

		if ( empty( $GLOBALS['title'] ) ) {
			$title = get_admin_page_title();
		} else {
			$title = $GLOBALS['title'];
		}
		return $title;
	}

	/**
	 * Prints out all settings sections added to a particular settings page.
	 *
	 * @link https://developer.wordpress.org/reference/functions/do_settings_sections/
	 *
	 * @param string $page
	 *
	 * @since 6.0
	 */
	public static function render_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			if ( $section['title'] ) {
				echo '<div class="wpbdp-settings-form-title">';
				echo '<h3>' . esc_html( $section['title'] ) . '</h3>';
				echo '</div>';
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}
			echo '<div class="form-table wpbdp-settings-form wpbdp-grid">';
			self::render_settings_fields( $page, $section['id'] );
			echo '</div>';
		}
	}

	/**
	 * Print out the settings fields for a particular settings section.
	 *
	 * @link https://developer.wordpress.org/reference/functions/do_settings_fields/
	 *
	 * @param string $page
	 * @param string $section
	 *
	 * @since 6.0
	 */
	public static function render_settings_fields( $page, $section ) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
			$class = 'wpbdp-setting-row';
			if ( ! empty( $field['args']['class'] ) ) {
				$class .= ' ' . $field['args']['class'];
			}

			echo '<div class="' . esc_attr( $class ) . '">';
			echo '<div class="wpbdp-setting-content">';
			call_user_func( $field['callback'], $field['args'] );
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Use the settings groups as tabs.
	 */
	private static function get_settings_tabs() {
		$all_groups = wpbdp()->settings->get_registered_groups();

		// Filter out empty groups.
		$all_groups = wp_list_filter( $all_groups, array( 'count' => 0 ), 'NOT' );

		return wp_list_filter( $all_groups, array( 'type' => 'tab' ) );
	}

	private static function get_content_tabs() {
		global $wpbdp;

		$menu = $wpbdp->admin->get_menu();
		$tabs = array();

		if ( empty( $menu ) ) {
			return $tabs;
		}

		$exclude = $wpbdp->admin->top_level_nav();

		foreach ( $menu as $id => $menu_item ) {
			$requires = empty( $menu_item['capability'] ) ? 'administrator' : $menu_item['capability'];
			if ( ! current_user_can( $requires ) || in_array( $id, $exclude ) ) {
				continue;
			}

			$title = strip_tags( $menu_item['title'] );

			// change_menu_name() changes the name here. This changes it back.
			if ( $title === __( 'Directory Content', 'business-directory-plugin' ) ) {
				$title = __( 'Listings', 'business-directory-plugin' );
			}

			$tabs[ $id ] = array(
				'title' => str_replace(
					__( 'Directory', 'business-directory-plugin' ) . ' ',
					'',
					$title
				),
				'icon'  => self::get_admin_menu_icon( $id, $menu_item ),
			);
		}

		return $tabs;
	}

	/**
	 * Set the admin menu icon with their corresponding inner locations.
	 *
	 * @param int   $menu_id The menu id.
	 * @param array $menu_item The menu item as an array.
	 *
	 * @return string
	 */
	private static function get_admin_menu_icon( $menu_id, $menu_item ) {
		$menu_icons = apply_filters(
			'wpbdp_admin_menu_icons',
			array(
				'edit.php?post_type=wpbdp_listing' => 'list',
				'edit-tags.php?taxonomy=wpbdp_category&amp;post_type=wpbdp_listing' => 'folder',
				'edit-tags.php?taxonomy=wpbdp_tag&amp;post_type=wpbdp_listing' => 'tag',
				'wpbdp-admin-fees'                 => 'money',
				'wpbdp_admin_formfields'           => 'clipboard',
				'wpbdp_admin_csv'                  => 'import',
			)
		);
		if ( isset( $menu_icons[ $menu_id ] ) ) {
			return $menu_icons[ $menu_id ];
		}
		return isset( $menu_item['icon'] ) ? $menu_item['icon'] : 'archive';
	}

	/**
	 * Use the icon string to get the icon url.
	 *
	 * @since 6.1
	 */
	private static function add_icon_url( &$tabs ) {
		foreach ( $tabs as $k => $tab ) {
			if ( ! empty( $tab['icon'] ) && empty( $tab['icon_url'] ) ) {
				$tabs[ $k ]['icon_url'] = WPBDP_ASSETS_URL . 'images/icons/' . $tab['icon'] . '.svg';
			}
		}
	}

	/**
	 * Get the active tab
	 *
	 * @return string
	 */
	private static function get_active_tab() {
		if ( ! WPBDP_App_Helper::is_bd_post_page() ) {
			return wpbdp_get_var( array( 'param' => 'page' ) );
		}

		$taxonomy = wpbdp_get_var( array( 'param' => 'taxonomy' ) );
		if ( ! $taxonomy ) {
			return 'edit.php?post_type=wpbdp_listing';
		}

		return add_query_arg(
			array(
				'taxonomy'  => $taxonomy,
				'post_type' => 'wpbdp_listing'
			),
			'edit-tags.php'
		);
	}
}

WPBDP_Admin_Pages::load_hooks();

/**
 * @deprecated 6.0
 */
function wpbdp_admin_sidebar( $echo = false ) {
	$page = wpbdp_render_page( WPBDP_PATH . 'templates/admin/sidebar.tpl.php', array(), $echo );

	if ( ! $echo ) {
		return $page;
	}

	return ! empty( $page );
}

function wpbdp_admin_header( $args_or_title = null, $id = null, $h2items = array(), $sidebar = true ) {
	// For backwards compatibility.
	if ( ! $args_or_title || ! is_array( $args_or_title ) ) {
		$buttons = array();
		if ( $h2items ) {
			foreach ( $h2items as $item ) {
				if ( isset( $item['label'] ) ) {
					$buttons[ $item['label'] ] = $item['url'];
				} else {
					$buttons[ $item[0] ] = $item[1];
				}
			}
		}

		$args_or_title = array(
			'title'   => $args_or_title,
			'id'      => $id,
			'buttons' => $buttons,
			'sidebar' => $sidebar,
		);

		if ( empty( $args_or_title['title'] ) ) {
			unset( $args_or_title['title'] );
		}

		if ( empty( $args_or_title['id'] ) ) {
			unset( $args_or_title['id'] );
		}

		if ( is_null( $args_or_title['sidebar'] ) ) {
			unset( $args_or_title['sidebar'] );
		}
	}

	$default_title = '';
	if ( empty( $GLOBALS['title'] ) ) {
		$default_title = get_admin_page_title();
	} else {
		$default_title = $GLOBALS['title'];
	}

	$defaults = array(
		'title'        => $default_title,
		'id'           => wpbdp_get_var( array( 'param' => 'page' ) ),
		'buttons'      => array(),
		'sidebar'      => true,
		'echo'         => false,
		'tabbed_title' => false,
		'titles'       => array(),
		'current_tab'  => '',
		'full_width'   => false,
	);

	$args = wp_parse_args( $args_or_title, $defaults );

	$id = str_replace( array( 'wpbdp_', 'wpbdp-' ), '', $args['id'] );
	$id = str_replace( array( 'admin-', 'admin_' ), '', $id );

	if ( empty( $args['echo'] ) ) {
		ob_start();
	}

	WPBDP_Admin_Pages::show_tabs(
		array(
			'id'           => $id,
			'sub'          => $args['title'],
			'buttons'      => isset( $args['buttons'] ) ? $args['buttons'] : array(),
			'show_nav'     => $args['sidebar'],
			'tabbed_title' => $args['tabbed_title'],
			'titles'       => $args['titles'],
			'current_tab'  => $args['current_tab'],
			'full_width'   => $args['full_width'],
		)
	);

	if ( empty( $args['echo'] ) ) {
		return ob_get_clean();
	}
}

/*
 * @param bool|string Use 'echo' or true to show the footer.
 */
function wpbdp_admin_footer( $echo = false ) {
	if ( ! $echo ) {
		ob_start();
	}

	WPBDP_Admin_Pages::show_tabs_footer();

	if ( ! $echo ) {
		return ob_get_clean();
	}
}
