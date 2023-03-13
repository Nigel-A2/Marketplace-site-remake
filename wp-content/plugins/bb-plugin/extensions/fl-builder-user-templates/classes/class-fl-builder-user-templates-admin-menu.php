<?php

/**
 * Logic for the user templates admin menu.
 *
 * @since 1.10
 */
final class FLBuilderUserTemplatesAdminMenu {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'admin_menu', __CLASS__ . '::register' );

		/* Filters */
		add_filter( 'submenu_file', __CLASS__ . '::submenu_file', 999, 2 );
	}

	/**
	 * Registers the builder admin menu for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function register() {
		global $submenu, $_registered_pages;

		$parent       = 'edit.php?post_type=fl-builder-template';
		$cap          = 'edit_posts';
		$list_url     = 'edit.php?post_type=fl-builder-template&fl-builder-template-type=';
		$add_url      = 'post-new.php?post_type=fl-builder-template';
		$cats_url     = 'edit-tags.php?taxonomy=fl-builder-template-category&post_type=fl-builder-template';
		$add_new_hook = 'fl-builder-template_page_fl-builder-add-new';

		$submenu[ $parent ]      = array();
		$submenu[ $parent ][100] = array( __( 'Templates', 'fl-builder' ), $cap, $list_url . 'layout' );
		$submenu[ $parent ][200] = array( __( 'Saved Rows', 'fl-builder' ), $cap, $list_url . 'row' );
		$submenu[ $parent ][300] = array( __( 'Saved Columns', 'fl-builder' ), $cap, $list_url . 'column' );
		$submenu[ $parent ][400] = array( __( 'Saved Modules', 'fl-builder' ), $cap, $list_url . 'module' );
		$submenu[ $parent ][500] = array( __( 'Categories', 'fl-builder' ), $cap, $cats_url );
		$submenu[ $parent ][700] = array( __( 'Add New', 'fl-builder' ), $cap, 'fl-builder-add-new', '' );

		if ( current_user_can( $cap ) ) {
			add_action( $add_new_hook, 'FLBuilderUserTemplatesAdminAdd::render' );
			$_registered_pages[ $add_new_hook ] = true;
		}

		$submenu[ $parent ] = apply_filters( 'fl_builder_user_templates_admin_menu', $submenu[ $parent ] );
	}

	/**
	 * Sets the active menu item for the builder admin submenu.
	 *
	 * @since 1.10
	 * @param string $submenu_file
	 * @param string $parent_file
	 * @return string
	 */
	static public function submenu_file( $submenu_file, $parent_file ) {
		global $pagenow;
		global $post;

		$screen   = get_current_screen();
		$list_url = 'edit.php?post_type=fl-builder-template';
		$new_url  = 'post-new.php?post_type=fl-builder-template';

		if ( isset( $_GET['page'] ) && 'fl-builder-add-new' == $_GET['page'] ) {
			$submenu_file = 'fl-builder-add-new';
		} elseif ( isset( $_GET['fl-builder-template-type'] ) && $list_url == $parent_file ) {
			$type         = sanitize_text_field( $_GET['fl-builder-template-type'] );
			$submenu_file = $parent_file . '&fl-builder-template-type=' . $type;
		} elseif ( 'post.php' == $pagenow && 'fl-builder-template' == $screen->post_type ) {
			$type         = FLBuilderModel::get_user_template_type( $post->ID );
			$submenu_file = 'edit.php?post_type=fl-builder-template&fl-builder-template-type=' . $type;
		} elseif ( 'edit-tags.php' == $pagenow && 'fl-builder-template-category' == $screen->taxonomy ) {
			$submenu_file = 'edit-tags.php?taxonomy=fl-builder-template-category&post_type=fl-builder-template';
		} elseif ( 'term.php' == $pagenow && 'fl-builder-template-category' == $screen->taxonomy ) {
			$submenu_file = 'edit-tags.php?taxonomy=fl-builder-template-category&post_type=fl-builder-template';
		}
		return $submenu_file;
	}
}

FLBuilderUserTemplatesAdminMenu::init();
