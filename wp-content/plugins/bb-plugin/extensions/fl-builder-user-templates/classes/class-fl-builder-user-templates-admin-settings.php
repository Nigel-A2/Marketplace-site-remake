<?php

/**
 * Admin settings for user defined templates in the builder.
 *
 * @since 1.8
 */
final class FLBuilderUserTemplatesAdminSettings {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		if ( is_admin() && isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], array( 'fl-builder-settings', 'fl-builder-multisite-settings' ) ) ) {
			add_filter( 'fl_builder_admin_settings_nav_items', __CLASS__ . '::admin_settings_nav_items' );
			add_action( 'fl_builder_admin_settings_render_forms', __CLASS__ . '::admin_settings_render_form' );
			add_action( 'fl_builder_admin_settings_save', __CLASS__ . '::save_settings' );
		}
	}

	/**
	 * Adds the Templates nav item to the admin settings.
	 *
	 * @since 1.8
	 * @param array $nav_items
	 * @return array
	 */
	static public function admin_settings_nav_items( $nav_items ) {
		$nav_items['templates'] = array(
			'title'    => __( 'Templates', 'fl-builder' ),
			'show'     => true,
			'priority' => 450,
		);

		return $nav_items;
	}

	/**
	 * Renders the admin settings templates form.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function admin_settings_render_form() {
		$enabled_templates = FLBuilderModel::get_enabled_templates();

		include FL_BUILDER_USER_TEMPLATES_DIR . 'includes/admin-settings-templates.php';
	}

	/**
	 * Saves the template settings.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function save_settings() {
		if ( isset( $_POST['fl-templates-nonce'] ) && wp_verify_nonce( $_POST['fl-templates-nonce'], 'templates' ) ) {

			$enabled_templates = sanitize_text_field( $_POST['fl-template-settings'] );

			FLBuilderModel::update_admin_settings_option( '_fl_builder_enabled_templates', $enabled_templates, true );
		}
	}
}

FLBuilderUserTemplatesAdminSettings::init();
