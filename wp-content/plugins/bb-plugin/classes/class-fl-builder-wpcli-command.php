<?php

/**
 * WP Cli commands for Beaver Builder.
 */
class FLbuilder_WPCLI_Command extends WP_CLI_Command {

	/**
	 * Deletes preview, draft and live CSS/JS asset cache for all posts.
	 *
	 * ## OPTIONS
	 *
	 * [--network]
	 * Clears the page builder cache for all sites on a network.
	 *
	 * [--all]
	 * Clears plugin and bb-theme cache.
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver clearcache
	 *      - Clears the page builder cache for all the posts on the site.
	 * 2. wp beaver clearcache --network
	 *      - Clears the page builder cache for all the posts on a network.
	*/
	public function clearcache( $args, $assoc_args ) {

		$network = false;
		$all     = false;

		if ( isset( $assoc_args['network'] ) && true == $assoc_args['network'] && is_multisite() ) {
			$network = true;
		}

		if ( isset( $assoc_args['all'] ) ) {

			// make sure theme functions are loaded.
			if ( class_exists( 'FLCustomizer' ) ) {
				$all = true;
			} else {
				WP_CLI::error( __( '--all switch used but bb-theme is not active. If using multisite bb-theme must be active on the root site.', 'fl-builder' ) );
			}
		}

		if ( class_exists( 'FLBuilderModel' ) ) {

			if ( true == $network ) {

				$blogs = get_sites();

				foreach ( $blogs as $keys => $blog ) {

					// Cast $blog as an array instead of WP_Site object
					if ( is_object( $blog ) ) {
						$blog = (array) $blog;
					}

					$blog_id = $blog['blog_id'];
					switch_to_blog( $blog_id );
					FLBuilderModel::delete_asset_cache_for_all_posts();
					/* translators: %s: current blog name */
					WP_CLI::success( sprintf( _x( 'Cleared the Beaver Builder cache for blog %s', 'current blog name', 'fl-builder' ), get_option( 'home' ) ) );
					if ( $all ) {
						FLCustomizer::refresh_css();
						/* translators: %s: current blog name */
						WP_CLI::success( sprintf( _x( 'Rebuilt the theme cache for blog %s', 'current blog name', 'fl-builder' ), get_option( 'home' ) ) );
					}
					restore_current_blog();
				}
			} else {
				FLBuilderModel::delete_asset_cache_for_all_posts();
				WP_CLI::success( __( 'Cleared the Beaver Builder cache', 'fl-builder' ) );
				if ( $all ) {
					FLCustomizer::refresh_css();
					WP_CLI::success( __( 'Rebuilt the theme cache', 'fl-builder' ) );
				}
			}
			/**
			 * After cache is cleared.
			 * @see fl_builder_cache_cleared
			 */
			do_action( 'fl_builder_cache_cleared' );
		}
	}


	/**
	 * Activate domain using Beaver Builder license key.
	 *
	 * ## OPTIONS
	 *
	 * [--deactivate]
	 * Deactivate this domain and remove license.
	 *
	 * [--license]
	 * License key to use.
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver register --license=01234567890
	 *      - Register this domain using license 01234567890
	 * 2. wp beaver register --deactivate
	 *      - Removes domain from domain manager and clears saved license info.
	 * 3. wp beaver register
	 *    - If license is defined in wp-config.php using FL_LICENSE_KEY global.
	*/
	public function register( $args, $assoc_args ) {

		$license = '';

		if ( isset( $assoc_args['deactivate'] ) ) {
			FLUpdater::save_subscription_license( '' );
			WP_CLI::success( __( 'Deactivated', 'fl-builder' ) );
			return false;
		}

		if ( defined( 'FL_LICENSE_KEY' ) ) {
			$license = FL_LICENSE_KEY;
			WP_CLI::log( __( 'Found license using FL_LICENSE_KEY global.', 'fl-builder' ) );
		}
		if ( isset( $assoc_args['license'] ) && '' != $assoc_args['license'] ) {
			$license = $assoc_args['license'];
		}

		if ( ! $license ) {
			WP_CLI::error( __( 'No license info found.', 'fl-builder' ) );
		}
		/* translators: %1$s: license : %2$s: domain */
		WP_CLI::log( sprintf( __( 'Using license [ %1$s ] to register %2$s', 'fl-builder' ), $license, network_home_url() ) );

		$response = FLUpdater::save_subscription_license( $license );

		if ( is_object( $response ) && isset( $response->error ) ) {
			WP_CLI::error( $response->error );
		} else {
			WP_CLI::success( $response->success );
		}
	}

	/**
	 * List all global options.
	 *
	 * ## EXAMPLE
	 *
	 * 1. wp beaver global list
	 *      - Returns list of Beaver Builder global options.
	 * @subcommand global list
	*/
	public function global_list( $args, $assoc_args ) {

		$settings = FLBuilderModel::get_global_settings();
		$results  = array();

		$fields = array(
			'setting',
			'value',
		);

		foreach ( $settings as $k => $setting ) {

			if ( ! is_array( $setting ) ) {
				$results[] = array(
					'setting' => $k,
					'value'   => $setting,
				);
			}
		}

		$formatter = new WP_CLI\Formatter( $assoc_args, $fields );
		$formatter->display_items( $results );
	}

	/**
	 * Update a single global option.
	 *
	 * ## EXAMPLE
	 *
	 * 1. wp beaver global-update --id=default_heading_selector --value=.fl-post-header
	 *      - Update a single global option
	 * @subcommand global-update
	*/
	public function global_update( $args, $assoc_args ) {

		if ( ! isset( $assoc_args['id'] ) ) {
			WP_CLI::error( 'No id use --id=' );
			exit;
		}
		if ( ! isset( $assoc_args['value'] ) ) {
			WP_CLI::error( 'No value use --value=' );
			exit;
		}

		$current_options = get_option( '_fl_builder_settings' );

		$current_options->{$assoc_args['id']} = $assoc_args['value'];

		$result = FLBuilderUtils::update_option( '_fl_builder_settings', $current_options );
		if ( $result ) {
			/* translators: %s: global option key */
			WP_CLI::success( sprintf( __( "Global option '%s' updated", 'fl-builder' ), $assoc_args['id'] ) );
		}
	}

	/**
	 * Duplicate a layout/page/post
	 *
	 * ## OPTIONS
	 *
	 * [--id]
	 * Post ID to duplicate
	 *
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver duplicate --id=123
	 *      - duplicate id 123
	 */
	public function duplicate( $args, $assoc_args ) {

		if ( ! isset( $assoc_args['id'] ) || ! is_numeric( $assoc_args['id'] ) ) {
			WP_CLI::error( 'Provide a valid ID --id=' );
			exit;
		}
		$id = $assoc_args['id'];

		$post_id = FLBuilderModel::duplicate_post( $id );
		$url     = FLBuilderModel::get_edit_url( $post_id );

		WP_CLI::line( $post_id );
		WP_CLI::success( __( 'Layout duplicated', 'fl-builder' ) );
	}
}

/**
 * WP Cli commands for Beaver Themer.
 */
class FLThemer_List_WPCLI_Command extends WP_CLI_Command {

	var $fields = array(
		'id',
		'name',
		'status',
		'type',
		'hook',
		'locations',
	);

	/**
	 * Set status for Themer layout.
	 *
	 * ## OPTIONS
	 *
	 * [--id]
	 * Post ID of Themer layout
	 *
	 * [--status]
	 * Status to use, publish or draft
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver themer set-status --id=123 --status=publish
	 *      - Set status for id 123 to publish
	 * 2. wp beaver themer set-status --id=456 --status=draft
	 *      - Set status for id 456 to draft
	 * @subcommand set-status
	 */
	public function set_status( $args, $assoc_args ) {

		$this->is_themer();

		if ( ! isset( $assoc_args['status'] ) ) {
			WP_CLI::error( 'No status use --status=' );
			exit;
		}

		if ( ! isset( $assoc_args['id'] ) ) {
			WP_CLI::error( 'No id use --id=' );
			exit;
		}
		$status = $assoc_args['status'];
		$id     = $assoc_args['id'];

		if ( ! in_array( $status, array( 'publish', 'draft' ) ) ) {
			WP_CLI::error( __( 'Status must be either draft or publish', 'fl-builder' ) );
			exit;
		}
		$args = array(
			'ID'          => $id,
			'post_status' => $status,
		);

		if ( 'fl-theme-layout' == get_post_type( $id ) ) {
			wp_update_post( $args );
			WP_CLI::success( __( 'Layout status updated', 'fl-builder' ) );
		} else {
			WPCLI::error( __( 'Post was not valid Themer layout.', 'fl-builder' ) );
		}
	}

	/**
	 * Set type for Themer layout.
	 *
	 * ## OPTIONS
	 *
	 * [--id]
	 * Post ID of Themer layout
	 *
	 * [--type]
	 * Type to use, header, footer, archive, 404 or part
	 *
	 * [--hook] ( If using part )
	 * Part hook to use
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver themer set-type --id=123 --type=archive
	 *      - Set typw for id 123 to archive
	 * 2. wp beaver themer set-type --id=456 --type=part --hook=fl_before_content
	 *      - Set type for id 456 to part and set hook to fl_before_content
	 * @subcommand set-type
	 */
	public function set_type( $args, $assoc_args ) {

		$this->is_themer();

		if ( ! isset( $assoc_args['type'] ) ) {
			WP_CLI::error( 'No type use --type=' );
			exit;
		}

		if ( ! isset( $assoc_args['id'] ) ) {
			WP_CLI::error( 'No id use --id=' );
			exit;
		}
		$type = $assoc_args['type'];
		$id   = $assoc_args['id'];
		$hook = false;

		if ( 'part' == $type ) {
			if ( ! isset( $assoc_args['hook'] ) ) {
				WP_CLI::error( 'No hook use --hook=' );
				exit;
			}
			$hook = $assoc_args['hook'];
		}

		if ( ! in_array( $type, array( 'archive', 'single', 'part', 'header', 'footer', '404' ) ) ) {
			WP_CLI::error( __( 'Incorrect type.', 'fl-builder' ) );
			exit;
		}

		if ( 'fl-theme-layout' == get_post_type( $id ) ) {
			update_post_meta( $id, '_fl_theme_layout_type', $type );
			update_post_meta( $id, '_fl_theme_layout_hook', '' );
			WP_CLI::success( __( 'Layout type updated', 'fl-builder' ) );
			if ( false !== $hook && 'part' == $type ) {
				update_post_meta( $id, '_fl_theme_layout_hook', $hook );
				WP_CLI::success( __( 'Hook updated', 'fl-builder' ) );
			}
		} else {
			WPCLI::error( __( 'Not valid Themer layout.', 'fl-builder' ) );
		}
	}

	/**
	 * List all Themer layouts.
	 *
	 * ## EXAMPLE
	 *
	 * 1. wp beaver themer list
	 *      - Returns list of all themer layouts.
	 *
	 * @subcommand list
	 */
	public function themer_list( $args, $assoc_args ) {

		$this->is_themer();

		$results = array();
		$args    = array(
			'post_type'      => 'fl-theme-layout',
			'post_status'    => array( 'publish', 'draft' ),
			'meta_key'       => '_fl_builder_enabled',
			'meta_value'     => '1',
			'posts_per_page' => -1,
		);
		$query   = new WP_Query( $args );
		$posts   = $query->posts;
		foreach ( $posts as $post ) {
			$type      = get_post_meta( $post->ID, '_fl_theme_layout_type', true );
			$locations = get_post_meta( $post->ID, '_fl_theme_builder_locations', true );
			$hook      = get_post_meta( $post->ID, '_fl_theme_layout_hook', true );
			$results[] = array(
				'id'        => $post->ID,
				'status'    => $post->post_status,
				'name'      => $post->post_title,
				'type'      => $type,
				'hook'      => $hook,
				'locations' => $this->format_locations( $locations ),
			);
		}

		$formatter = new WP_CLI\Formatter( $assoc_args, $this->fields );
		$formatter->display_items( $results );
	}

	/**
	 * List all possible Themer Part Hooks.
	 *
	 * ## EXAMPLE
	 *
	 * 1. wp beaver themer list-hooks
	 *      - Returns list of all possible themer part hooks.
	 * @subcommand list-hooks
	*/
	public function list_hooks( $args, $assoc_args ) {

		$this->is_themer();

		$hooks   = FLThemeBuilderLayoutData::get_part_hooks();
		$results = array();

		$fields = array(
			'name',
			'hook',
		);

		foreach ( $hooks as $hook ) {

			if ( is_array( $hook ) ) {
				foreach ( $hook['hooks'] as $k => $location ) {
					$results[] = array(
						'name' => $location,
						'hook' => $k,
					);
				}
			}
		}

		$formatter = new WP_CLI\Formatter( $assoc_args, $fields );
		$formatter->display_items( $results );
	}

	/**
	 * Add location to themer layout.
	 *
	 * ## OPTIONS
	 *
	 * [--id]
	 * Post ID of Themer layout
	 *
	 * [--location]
	 * Status to use, publish or draft
	 *
	 * [--postion] (Optional)
	 * Position in location array to insert new location
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver themer add-location --id=123 --location=general:single
	 *      - Add location general:site to Themer layout 123
	 * 2. wp beaver themer add-location --id=123 --location=general:single --position=0
	 *      - Add location general:site to Themer layout 123 in first element of array
	 * @subcommand add-location
	 */
	public function add_location( $args, $assoc_args ) {

		$this->is_themer();

		if ( ! isset( $assoc_args['location'] ) ) {
			WP_CLI::error( 'No location use --location=' );
			exit;
		}
		if ( ! isset( $assoc_args['id'] ) ) {
			WP_CLI::error( 'No id use --id=' );
			exit;
		}
		$location = $assoc_args['location'];
		$id       = $assoc_args['id'];
		$position = isset( $assoc_args['position'] ) ? $assoc_args['position'] : false;

		$locations = (array) get_post_meta( $id, '_fl_theme_builder_locations', true );

		if ( false !== $position ) {
			array_splice( $locations, $position, 0, $location );
		} else {
			$locations[] = $location;
		}

		update_post_meta( $id, '_fl_theme_builder_locations', array_unique( $locations ) );

		$post      = get_post( $id );
		$results   = array();
		$type      = get_post_meta( $post->ID, '_fl_theme_layout_type', true );
		$locations = get_post_meta( $post->ID, '_fl_theme_builder_locations', true );
		$hook      = get_post_meta( $post->ID, '_fl_theme_layout_hook', true );
		$results[] = array(
			'id'        => $post->ID,
			'status'    => $post->post_status,
			'name'      => $post->post_title,
			'type'      => $type,
			'hook'      => $hook,
			'locations' => $this->format_locations( $locations ),
		);

		$formatter = new WP_CLI\Formatter( $assoc_args, $this->fields );
		$formatter->display_items( $results );
	}

	/**
	 * Delete location from themer layout.
	 *
	 * ## OPTIONS
	 *
	 * [--id]
	 * Post ID of Themer layout
	 *
	 * [--location]
	 * Status to use, publish or draft
	 *
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver themer del-location --id=123 --location=general:single
	 *      - Remove location general:site from Themer layout 123
	 * @subcommand del-location
	 */
	public function del_location( $args, $assoc_args ) {

		$this->is_themer();

		if ( ! isset( $assoc_args['location'] ) ) {
			WP_CLI::error( 'No location use --location=' );
			exit;
		}
		if ( ! isset( $assoc_args['id'] ) ) {
			WP_CLI::error( 'No id use --id=' );
			exit;
		}
		$location = $assoc_args['location'];
		$id       = $assoc_args['id'];

		$locations = (array) get_post_meta( $id, '_fl_theme_builder_locations', true );

		$find = array_search( $location, $locations );

		if ( false !== $find ) {
			unset( $locations[ $find ] );
		} else {
			WP_CLI::error( 'Location not found' );
		}

		update_post_meta( $id, '_fl_theme_builder_locations', array_unique( $locations ) );

		$post      = get_post( $id );
		$results   = array();
		$type      = get_post_meta( $post->ID, '_fl_theme_layout_type', true );
		$locations = get_post_meta( $post->ID, '_fl_theme_builder_locations', true );
		$hook      = get_post_meta( $post->ID, '_fl_theme_layout_hook', true );
		$results[] = array(
			'id'        => $post->ID,
			'status'    => $post->post_status,
			'name'      => $post->post_title,
			'type'      => $type,
			'hook'      => $hook,
			'locations' => $this->format_locations( $locations ),
		);

		$formatter = new WP_CLI\Formatter( $assoc_args, $this->fields );
		$formatter->display_items( $results );
	}

	protected function is_themer() {
		if ( ! class_exists( 'FLThemeBuilder' ) ) {
			WP_CLI::error( __( 'Unable to find Themer, is it installed and activated?', 'fl-builder' ) );
			exit;
		}
	}

	protected function format_locations( $data ) {
		return implode( ', ', $data );
	}
}
/**
 * Add WPCLI commands
 */
WP_CLI::add_command( 'beaver', 'FLbuilder_WPCLI_Command' );
WP_CLI::add_command( 'beaver themer', 'FLThemer_List_WPCLI_Command' );
