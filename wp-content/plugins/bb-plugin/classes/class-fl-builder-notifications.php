<?php
/**
 * Notifications
 * @since 2.1
 */
final class FLBuilderNotifications {

	protected static $url = 'https://www.wpbeaverbuilder.com/wp-json/wp/v2/fl_notification';

	protected static $option = 'fl_notifications';

	protected static $seconds = 172800; // 48 hours

	public static function init() {

		if ( FLBuilderModel::is_white_labeled() || true == apply_filters( 'fl_disable_notifications', false ) ) {
			return false;
		}
		add_action( 'init', array( 'FLBuilderNotifications', 'set_schedule' ) );
		add_action( 'fl_builder_notifications_event', array( 'FLBuilderNotifications', 'fetch_notifications' ) );
		FLBuilderAJAX::add_action( 'fl_builder_notifications', array( 'FLBuilderNotifications', 'notications_ajax' ), array( 'read' ) );
	}

	/**
	 * Add scheduled event
	 * @since 2.2.1
	 */
	public static function set_schedule() {

		if ( ! wp_next_scheduled( 'fl_builder_notifications_event' ) ) {
			wp_schedule_single_event( time() + self::$seconds, 'fl_builder_notifications_event' );
		}
	}

	/**
	 * Transient is passed by reference here, lets not mess with it and just trigger our fetch.
	 * @deprecated 2.2.1
	 */
	public static function fetch_notifications_trigger( $transient ) {
		if ( ! did_action( 'fl_fetch_notifications' ) ) {
			do_action( 'fl_fetch_notifications' );
		}
		return $transient;
	}

	/**
	 * Notification AJAX callback.
	 *
	 * @since 2.1
	 */
	public static function notications_ajax( $read ) {

		if ( $read ) {
			self::update_state( true );
		} else {
			self::update_state( false );
		}
		wp_send_json_success();
	}

	/**
	 * Fetch notifications from remote.
	 *
	 * @since 2.1
	 */
	public static function fetch_notifications() {

		$defaults = array(
			'read'     => false,
			'checksum' => '',
			'data'     => '{}',
		);

		$url         = self::$url;
		$stored_data = get_option( self::$option, $defaults );
		$response    = wp_remote_get( $url );

		$response_code = wp_remote_retrieve_response_code( $response );
		$body          = wp_remote_retrieve_body( $response );

		if ( 200 === $response_code ) {

			$body = json_decode( $body );

			// No post 0
			if ( ! isset( $body[0] ) || ! isset( $body[0]->date ) ) {
				return $stored_data;
			}

			// Generate checksum data
			$latest_checksum = self::get_checksum( $body );
			$stored_checksum = $stored_data['checksum'];

			// check if we have any unread posts by comparing checksums
			$unread = self::compare_checksums( $stored_checksum, $latest_checksum );

			$stored_data = array(
				'read'     => true,
				'checksum' => $latest_checksum,
				'data'     => wp_json_encode( $body ),
			);

			if ( $unread ) {
				$stored_data['read'] = false;
			}

			FLBuilderUtils::update_option( self::$option, $stored_data, false );

		} else {
			error_log( 'response was not a 200' );
		}
		return $stored_data;

	}

	/**
	 * Compare locally stored checksums against new data.
	 * @since 2.1
	 * @return bool true if new posts detected
	 */
	public static function compare_checksums( $stored_checksum, $latest_checksum ) {

		if ( ! is_array( $stored_checksum ) ) {
			return true;
		}

		foreach ( $stored_checksum as $id => $date ) {

			// if a post has been deleted, then remove it from local checksum
			if ( ! isset( $latest_checksum[ $id ] ) ) {
				unset( $stored_checksum[ $id ] );
			}
		}

		$diff = array_diff_assoc( $latest_checksum, $stored_checksum );
		return ( ! empty( $diff ) ) ? true : false;
	}

	/**
	 * Prepare checksum array from rest data.
	 *
	 * @since 2.1
	 */
	public static function get_checksum( $body ) {
		$checksum = array();
		foreach ( $body as $post ) {
			$checksum[ $post->id ] = crc32( $post->content->rendered );
		}
		return (array) $checksum;
	}

	/**
	 * Return notifications from the db or fetch from remote
	 *
	 * @since 2.1
	 */
	public static function get_notifications() {

		$defaults      = array(
			'read'     => false,
			'checksum' => '',
			'data'     => '{}',
		);
		$notifications = get_option( self::$option, $defaults );

		if ( '{}' == $notifications['data'] ) {
			return self::fetch_notifications();
		}
		return $notifications;
	}

	/**
	 * Mark notifications read/unread
	 *
	 * @since 2.1
	 */
	public static function update_state( $state ) {
		$defaults              = array(
			'read'     => false,
			'checksum' => '',
			'data'     => '{}',
		);
		$notifications         = get_option( self::$option, $defaults );
		$notifications['read'] = $state;
		FLBuilderUtils::update_option( self::$option, $notifications );
	}
}
FLBuilderNotifications::init();
