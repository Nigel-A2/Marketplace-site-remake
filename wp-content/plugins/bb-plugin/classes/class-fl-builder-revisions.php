<?php

/**
 * Handles the revisions UI for the builder.
 *
 * @since 2.0
 */
final class FLBuilderRevisions {

	/**
	 * Initialize hooks.
	 *
	 * @since 2.0
	 * @return void
	 */
	static public function init() {
		add_filter( 'fl_builder_ui_js_config', __CLASS__ . '::ui_js_config' );
		add_filter( 'fl_builder_main_menu', __CLASS__ . '::main_menu_config' );
	}

	/**
	 * Adds revision data to the UI JS config.
	 *
	 * @since 2.0
	 * @param array $config
	 * @return array
	 */
	static public function ui_js_config( $config ) {
		$config['revisions']       = self::get_config( $config['postId'] );
		$config['revisions_count'] = isset( $config['revisions']['posts'] ) && is_array( $config['revisions']['posts'] ) ? count( $config['revisions']['posts'] ) : 0;
		return $config;
	}

	/**
	 * Gets the revision config for a post.
	 *
	 * @since 2.0
	 * @param int $post_id
	 * @return array
	 */
	static public function get_config( $post_id ) {
		global $wp_version;

		$revisions = wp_get_post_revisions( $post_id, array(
			'numberposts' => apply_filters( 'fl_builder_revisions_number', 25 ),
		) );

		if ( version_compare( $wp_version, '5.3.0', '<' ) ) {
			$tz = get_option( 'timezone_string' );

			if ( empty( $tz ) ) {
				$offset  = (float) get_option( 'gmt_offset' );
				$hours   = (int) $offset;
				$minutes = ( $offset - $hours );

				$sign     = ( $offset < 0 ) ? '-' : '+';
				$abs_hour = abs( $hours );
				$abs_mins = abs( $minutes * 60 );
				$tz       = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
			}

			$local_time = new DateTimeImmutable( 'now', new DateTimeZone( $tz ) );
		} else {
			$local_time = current_datetime();
		}

		$current_time = $local_time->getTimestamp() + $local_time->getOffset();

		$config = array(
			'posts'   => array(),
			'authors' => array(),
		);

		$current_data = serialize( get_post_meta( $post_id, '_fl_builder_data', true ) );

		if ( count( $revisions ) > 1 ) {

			foreach ( $revisions as $revision ) {

				$revision_data = serialize( get_post_meta( $revision->ID, '_fl_builder_data', true ) );

				if ( ! current_user_can( 'read_post', $revision->ID ) ) {
					continue;
				}
				if ( wp_is_post_autosave( $revision ) ) {
					continue;
				}

				if ( $revision_data == $current_data ) {
					continue;
				}

				$timestamp = strtotime( $revision->post_date );

				$config['posts'][] = array(
					'id'     => $revision->ID,
					'author' => $revision->post_author,
					'date'   => array(
						'published' => gmdate( 'F j', $timestamp ),
						'diff'      => human_time_diff( $timestamp, $current_time ),
					),
				);

				if ( ! isset( $config['authors'][ $revision->post_author ] ) ) {
					$config['authors'][ $revision->post_author ] = array(
						'name'   => get_the_author_meta( 'display_name', $revision->post_author ),
						'avatar' => sprintf( '<img height="30" width="30" class="avatar avatar-30 photo" src="%s" />', esc_url( get_avatar_url( $revision->post_author, 30 ) ) ),
					);
				}
			}
		}

		return $config;
	}

	/**
	 * Adds revision data to the main menu config.
	 *
	 * @since 2.0
	 * @param array $config
	 * @return array
	 */
	static public function main_menu_config( $config ) {
		$config['main']['items'][35] = array(
			'label' => __( 'Revisions', 'fl-builder' ),
			'type'  => 'view',
			'view'  => 'revisions',
		);

		$config['revisions'] = array(
			'name'       => __( 'Revisions', 'fl-builder' ),
			'isShowing'  => false,
			'isRootView' => false,
			'items'      => array(),
		);

		return $config;
	}

	/**
	 * Renders the layout for a revision preview in the builder.
	 *
	 * @since 2.0
	 * @param int $revision_id
	 * @return array
	 */
	static public function render_preview( $revision_id ) {
		FLBuilderModel::set_post_id( $revision_id );

		return FLBuilderAJAXLayout::render();
	}

	/**
	 * Restores the current layout to a revision with the specified ID.
	 *
	 * @since 2.0
	 * @param int $revision_id
	 * @return array
	 */
	static public function restore( $revision_id ) {
		$data = FLBuilderModel::get_layout_data( 'published', $revision_id );

		FLBuilderModel::update_layout_data( $data );
		$settings = get_post_meta( $revision_id, '_fl_builder_data_settings', true );
		update_post_meta( FLBuilderModel::get_post_id(), '_fl_builder_draft_settings', $settings );
		return array(
			'layout'   => FLBuilderAJAXLayout::render(),
			'config'   => FLBuilderUISettingsForms::get_node_js_config(),
			'settings' => $settings,
		);
	}
}

FLBuilderRevisions::init();
