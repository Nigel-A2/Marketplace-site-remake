<?php

/**
 * Helper class for builder updates.
 *
 * @since 1.2.8
 */
final class FLBuilderUpdate {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		add_action( 'init', __CLASS__ . '::maybe_run', 11 );
		add_action( 'fl_site_url_changed', array( __CLASS__, 'maybe_reregister_license' ), 10, 2 );
	}

	public static function maybe_reregister_license( $current, $saved ) {
		$license = FLUpdater::get_subscription_license();
		if ( '' !== $license ) {
			FLUpdater::save_subscription_license( '' );
			FLUpdater::save_subscription_license( $license );
		}
	}

	/**
	 * Checks to see if an update should be run. If it should,
	 * the appropriate update method is run and the version
	 * number is updated in the database.
	 *
	 * @since 1.2.8
	 * @return void
	 */
	static public function maybe_run() {
		// Make sure the user is logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Don't update for dev copies.
		if ( FL_BUILDER_VERSION == '{FL_BUILDER_VERSION}' ) {
			return;
		}

		// Only run on the main site for multisite installs.
		if ( is_multisite() && ! is_main_site() ) {
			return;
		}

		// Get the saved version.
		$saved_version = get_site_option( '_fl_builder_version' );

		// No saved version number. This must be a fresh install.
		if ( ! $saved_version ) {
			update_site_option( '_fl_builder_version', FL_BUILDER_VERSION );
			return;
		} elseif ( ! version_compare( $saved_version, FL_BUILDER_VERSION, '=' ) ) {

			if ( is_multisite() ) {
				self::run_multisite( $saved_version );
			} else {
				self::run( $saved_version );
			}

			do_action( 'fl_builder_cache_cleared' );

			update_site_option( '_fl_builder_version', FL_BUILDER_VERSION );

			update_site_option( '_fl_builder_update_info', array(
				'from' => $saved_version,
				'to'   => FL_BUILDER_VERSION,
			) );
		}
	}

	/**
	 * Runs the update for a specific version.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function run( $saved_version ) {
		// Update to 1.2.8 or greater.
		if ( version_compare( $saved_version, '1.2.8', '<' ) ) {
			self::v_1_2_8();
		}

		// Update to 1.4.6 or greater.
		if ( version_compare( $saved_version, '1.4.6', '<' ) ) {
			self::v_1_4_6();
		}

		// Update to 1.6.3 or greater.
		if ( version_compare( $saved_version, '1.6.3', '<' ) ) {
			self::v_1_6_3();
		}

		// Update to 1.10 or greater.
		if ( version_compare( $saved_version, '1.10', '<' ) ) {
			self::v_1_10();
		}

		// Update to 1.10 or greater.
		if ( version_compare( $saved_version, '2.2.2.6', '<' ) ) {
			self::v_2226();
		}

		// Clear all asset cache.
		FLBuilderModel::delete_asset_cache_for_all_posts();

		// Flush the rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Runs the update for all sites on a network install.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function run_multisite( $saved_version ) {
		global $blog_id;
		global $wpdb;

		// Network update to 1.10 or greater.
		if ( version_compare( $saved_version, '1.10', '<' ) ) {
			self::v_1_10( true );
		}

		// Save the original blog id.
		$original_blog_id = $blog_id;

		// Get all blog ids.
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		// Loop through the blog ids and run the update.
		foreach ( $blog_ids as $id ) {
			switch_to_blog( $id );
			self::run( $saved_version );
		}

		// Revert to the original blog.
		switch_to_blog( $original_blog_id );
	}

	/**
	 * Check for the fl_builder_nodes table that existed before 1.2.8.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return bool
	 */
	static private function pre_1_2_8_table_exists() {
		global $wpdb;

		$table   = $wpdb->prefix . 'fl_builder_nodes';
		$results = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

		return count( $results ) > 0;
	}

	/**
	 * Check to see if the fl_builder_nodes table that existed before 1.2.8
	 * is empty or not.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return bool
	 */
	static private function pre_1_2_8_table_is_empty() {
		global $wpdb;

		if ( self::pre_1_2_8_table_exists() ) {

			$table = $wpdb->prefix . 'fl_builder_nodes';
			$nodes = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %s', $table ) );

			return count( $nodes ) === 0;
		}

		return true;
	}

	/**
	 * Saves a backup of the pre 1.2.8 database table.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function pre_1_2_8_backup() {
		global $wpdb;

		if ( self::pre_1_2_8_table_exists() ) {

			$cache_dir = FLBuilderModel::get_cache_dir();
			$table     = $wpdb->prefix . 'fl_builder_nodes';

			// Get the data to backup.
			$nodes = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %s', $table ) );
			$meta  = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_fl_builder_layout'" );

			// Build the export object.
			$data          = new StdClass();
			$data->version = FL_BUILDER_VERSION;
			$data->nodes   = $nodes;
			$data->meta    = $meta;

			// Save the backup.
			fl_builder_filesystem()->file_put_contents( $cache_dir['path'] . 'backup.dat', serialize( $data ) );
		}
	}

	/**
	 * Restores a site to pre 1.2.8.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function pre_1_2_8_restore() {
		global $wpdb;

		if ( ! self::pre_1_2_8_table_exists() || self::pre_1_2_8_table_is_empty() ) {

			$cache_dir   = FLBuilderModel::get_cache_dir();
			$backup_path = $cache_dir['path'] . 'backup.dat';

			// Install the database.
			FLBuilderModel::install_database();

			// Check for the backup file.
			if ( file_exists( $backup_path ) ) {

				// Get the backup data.
				$backup = unserialize( file_get_contents( $backup_path ) );

				// Check for the correct backup data.
				if ( ! isset( $backup->nodes ) || ! isset( $backup->meta ) ) {
					return;
				}

				// Restore the nodes.
				foreach ( $backup->nodes as $node ) {

					$wpdb->insert("{$wpdb->prefix}fl_builder_nodes",
						array(
							'node'     => $node->node,
							'type'     => $node->type,
							'layout'   => $node->layout,
							'parent'   => $node->parent,
							'position' => $node->position,
							'settings' => $node->settings,
							'status'   => $node->status,
						),
						array( '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
					);
				}

				// Restore the meta.
				foreach ( $backup->meta as $meta ) {
					update_post_meta( $meta->post_id, '_fl_builder_layout', $meta->meta_value );
				}
			}
		}
	}

	/**
	 * Update to version 1.2.8 or later.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function v_1_2_8() {
		global $wpdb;

		if ( self::pre_1_2_8_table_exists() ) {

			$table     = $wpdb->prefix . 'fl_builder_nodes';
			$metas     = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_fl_builder_layout'" );
			$cache_dir = FLBuilderModel::get_cache_dir();

			// Loop through the layout ids for each post.
			foreach ( $metas as $meta ) {

				// Get the old layout nodes from the database.
				$published = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %s WHERE layout = %s AND status = 'published'", $table, $meta->meta_value ) );
				$draft     = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %s WHERE layout = %s AND status = 'draft'", $table, $meta->meta_value ) );

				// Convert the old nodes to new ones.
				$published = self::v_1_2_8_convert_nodes( $published );
				$draft     = self::v_1_2_8_convert_nodes( $draft );

				// Add the new layout post meta.
				update_post_meta( $meta->post_id, '_fl_builder_data', $published );
				update_post_meta( $meta->post_id, '_fl_builder_draft', $draft );
			}

			// Backup the old builder table.
			self::pre_1_2_8_backup();

			// Drop the old builder table.
			if ( file_exists( $cache_dir['path'] . 'backup.dat' ) ) {
				$wpdb->query( "DROP TABLE {$wpdb->prefix}fl_builder_nodes" );
			}

			// Delete old post meta.
			delete_post_meta_by_key( '_fl_builder_layout' );
			delete_post_meta_by_key( '_fl_builder_layout_export' );
			delete_post_meta_by_key( '_fl_builder_css' );
			delete_post_meta_by_key( '_fl_builder_css-draft' );
			delete_post_meta_by_key( '_fl_builder_js' );
			delete_post_meta_by_key( '_fl_builder_js-draft' );

			// Convert global settings.
			self::v_1_2_8_convert_global_settings();

			// Delete all asset cache.
			$css = glob( $cache_dir['path'] . '*.css' );
			$js  = glob( $cache_dir['path'] . '*.js' );

			if ( is_array( $css ) ) {
				array_map( array( fl_builder_filesystem(), 'unlink' ), $css );
			}
			if ( is_array( $js ) ) {
				array_map( array( fl_builder_filesystem(), 'unlink' ), $js );
			}
		}
	}

	/**
	 * Convert the global settings for 1.2.8 or later.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function v_1_2_8_convert_global_settings() {
		$settings = get_option( '_fl_builder_settings' );

		if ( $settings && is_string( $settings ) ) {
			FLBuilderUtils::update_option( '_fl_builder_settings', json_decode( $settings ) );
		}
	}

	/**
	 * Convert the nodes for 1.2.8 or earlier.
	 *
	 * @since 1.2.8
	 * @access private
	 * @param array $nodes An array of node data.
	 * @return array
	 */
	static private function v_1_2_8_convert_nodes( $nodes ) {
		$new_nodes = array();

		foreach ( $nodes as $node ) {

			unset( $node->id );
			unset( $node->layout );
			unset( $node->status );

			if ( 'row' == $node->type ) {
				$node->parent = null;
			}

			$node->settings           = self::v_1_2_8_json_decode_settings( $node->settings );
			$new_nodes[ $node->node ] = $node;
		}

		return $new_nodes;
	}

	/**
	 * Convert a JSON encoded settings string for 1.2.8 or earlier.
	 *
	 * @since 1.2.8
	 * @access private
	 * @param object $settings The settings object.
	 * @return object
	 */
	static private function v_1_2_8_json_decode_settings( $settings ) {
		if ( ! $settings || empty( $settings ) ) {
			return null;
		}

		$settings = json_decode( $settings );

		foreach ( $settings as $key => $val ) {

			if ( is_string( $val ) ) {

				$decoded = json_decode( $val );

				if ( is_object( $decoded ) || is_array( $decoded ) ) {

					$settings->{$key} = $decoded;
				}
			} elseif ( is_array( $val ) ) {

				foreach ( $val as $sub_key => $sub_val ) {

					if ( is_string( $sub_val ) ) {

						$decoded = json_decode( $sub_val );

						if ( is_object( $decoded ) || is_array( $decoded ) ) {

							$settings->{$key}[ $sub_key ] = $decoded;
						}
					}
				}
			}
		}

		return $settings;
	}

	/**
	 * Update to version 1.4.6 or later.
	 *
	 * @since 1.4.6
	 * @access private
	 * @return void
	 */
	static private function v_1_4_6() {
		// Remove the old fl-builder uploads folder.
		$upload_dir = wp_upload_dir( null, false );
		$path       = trailingslashit( $upload_dir['basedir'] ) . 'fl-builder';

		if ( file_exists( $path ) ) {
			fl_builder_filesystem()->rmdir( $path, true );
		}
	}

	/**
	 * Update to version 1.6.3 or later.
	 *
	 * @since 1.6.3
	 * @access private
	 * @return void
	 */
	static private function v_1_6_3() {
		$posts = get_posts( array(
			'post_type'      => 'fl-builder-template',
			'posts_per_page' => '-1',
		) );

		foreach ( $posts as $post ) {

			$type = wp_get_post_terms( $post->ID, 'fl-builder-template-type' );

			if ( 0 === count( $type ) ) {
				wp_set_post_terms( $post->ID, 'layout', 'fl-builder-template-type' );
			}
		}
	}

	/**
	 * Update to version 1.10 or later.
	 *
	 * @since 1.10
	 * @access private
	 * @return void
	 */
	static private function v_1_10( $network = false ) {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
		}

		$roles             = get_editable_roles();
		$user_access       = array();
		$unrestricted      = self::v_1_10_convert_cap_to_roles( '_fl_builder_editing_capability', $roles, $network );
		$global_templates  = self::v_1_10_convert_cap_to_roles( '_fl_builder_global_templates_editing_capability', $roles, $network );
		$builder_admin     = self::v_1_10_convert_option_to_roles( '_fl_builder_user_templates_admin', $roles, $network );
		$template_exporter = self::v_1_10_convert_option_to_roles( '_fl_builder_template_data_exporter', $roles, $network );

		if ( ! empty( $unrestricted ) ) {
			$user_access['unrestricted_editing'] = $unrestricted;
		}

		if ( ! empty( $global_templates ) ) {
			$user_access['global_node_editing'] = $global_templates;
		}

		if ( ! empty( $builder_admin ) ) {
			$user_access['builder_admin'] = $builder_admin;
		}

		if ( ! empty( $template_exporter ) ) {
			$user_access['template_data_exporter'] = $template_exporter;
		}

		if ( ! empty( $user_access ) ) {

			if ( $network ) {
				update_site_option( '_fl_builder_user_access', $user_access );
			} else {
				FLBuilderUtils::update_option( '_fl_builder_user_access', $user_access );
			}
		}
	}

	/**
	 * Convert an old editing capability to a role settings.
	 *
	 * @since 1.10
	 * @access private
	 * @return array
	 */
	static private function v_1_10_convert_cap_to_roles( $key, $roles, $network = false ) {
		$option = $network ? get_site_option( $key ) : get_option( $key );
		$data   = array();

		if ( ! empty( $option ) ) {

			if ( $network ) {
				delete_site_option( $key );
			} else {
				delete_option( $key );
			}

			$option = explode( ',', $option );

			foreach ( $roles as $role_key => $role_data ) {

				if ( ! isset( $role_data['capabilities']['edit_posts'] ) ) {
					continue;
				}

				$data[ $role_key ] = false;

				foreach ( $option as $cap ) {

					if ( isset( $role_data['capabilities'][ trim( $cap ) ] ) ) {
						$data[ $role_key ] = true;
						break;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Convert old options to user access roles.
	 *
	 * @since 1.10
	 * @access private
	 * @return array
	 */
	static private function v_1_10_convert_option_to_roles( $key, $roles, $network = false ) {
		$option  = $network ? get_site_option( $key ) : get_option( $key );
		$enabled = ! empty( $option ) && $option;
		$data    = array();

		if ( ! empty( $option ) ) {

			if ( $network ) {
				delete_site_option( $key );
			} else {
				delete_option( $key );
			}

			foreach ( $roles as $role_key => $role_data ) {

				if ( ! isset( $role_data['capabilities']['edit_posts'] ) ) {
					continue;
				}

				$data[ $role_key ] = $enabled;
			}
		}

		return $data;
	}

	static private function v_2226() {

		if ( false !== get_option( 'fl_debug_mode', false ) ) {
			$current = get_option( 'fl_debug_mode' );
			set_transient( 'fl_debug_mode', $current, 172800 ); // 48 hours
			delete_option( 'fl_debug_mode' );
		}
	}
}

FLBuilderUpdate::init();
