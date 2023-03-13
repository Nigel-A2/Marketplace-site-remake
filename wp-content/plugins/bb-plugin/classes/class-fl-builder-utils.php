<?php

/**
 * Misc helper methods.
 *
 * @since 1.0
 */

final class FLBuilderUtils {

	/**
	 * Get an instance of WP_Filesystem_Direct.
	 *
	 * @since 1.4.6
	 * @deprecated 2.0.6
	 * @return object A WP_Filesystem_Direct instance.
	 */
	static public function get_filesystem() {

		_deprecated_function( __METHOD__, '2.0.6', 'fl_builder_filesystem()->get_filesystem()' );

		return fl_builder_filesystem()->get_filesystem();
	}

	/**
	 * Sets the filesystem method to direct.
	 *
	 * @since 1.4.6
	 * @deprecated 2.0.6
	 * @return string
	 */
	static public function filesystem_method() {
		_deprecated_function( __METHOD__, '2.0.6', 'fl_builder_filesystem()->filesystem_method()' );
		return 'direct';
	}

	/**
	 * Return a snippet without punctuation at the end.
	 *
	 * @since 1.2.3
	 * @param string $text The text to truncate.
	 * @param int $length The number of characters to return.
	 * @param string $tail The trailing characters to append.
	 * @return string
	 */
	static public function snippetwop( $text, $length = 64, $tail = '...' ) {
		$text = trim( $text );
		$txtl = function_exists( 'mb_strlen' ) ? mb_strlen( $text ) : strlen( $text );

		if ( $txtl > $length ) {

			for ( $i = 1;' ' != $text[ $length -$i ];$i++ ) { // @codingStandardsIgnoreLine

				if ( $i == $length ) {

					if ( function_exists( 'mb_substr' ) ) {
						return mb_substr( $text, 0, $length ) . $tail;
					}

					return substr( $text, 0, $length ) . $tail;
				}
			}

			for ( ;',' == $text[ $length -$i ] || '.' == $text[ $length -$i ] || ' ' == $text[ $length -$i ]; // @codingStandardsIgnoreLine
			$i++ ) {;} // @codingStandardsIgnoreLine

			if ( function_exists( 'mb_substr' ) ) {
				return mb_substr( $text,0,$length -$i + 1 ) . $tail; // @codingStandardsIgnoreLine
			}

			return substr( $text,0,$length -$i + 1 ) . $tail; // @codingStandardsIgnoreLine
		}

		return $text;
	}

	/**
	 * JSON decode multidimensional array values or object properties.
	 *
	 * @since 1.5.6
	 * @param mixed $data The data to decode.
	 * @return mixed The decoded data.
	 */
	static public function json_decode_deep( $data ) {
		// First check if we have a string and try to decode that.
		if ( is_string( $data ) ) {
			$data = json_decode( $data );
		}

		// Decode object properties or array values.
		if ( is_object( $data ) || is_array( $data ) ) {

			foreach ( $data as $key => $val ) {

				$new_val = null;

				if ( is_string( $val ) ) {

					$decoded = json_decode( $val );

					if ( is_object( $decoded ) || is_array( $decoded ) ) {
						$new_val = $decoded;
					}
				} elseif ( is_object( $val ) || is_array( $val ) ) {
					$new_val = self::json_decode_deep( $val );
				}

				if ( $new_val ) {

					if ( is_object( $data ) ) {
						$data->{$key} = $new_val;
					} elseif ( is_array( $data ) ) {
						$data[ $key ] = $new_val;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Base64 decode settings if our ModSecurity fix is enabled.
	 *
	 * @since 1.8.4
	 * @return array
	 */
	static public function modsec_fix_decode( $settings ) {
		if ( defined( 'FL_BUILDER_MODSEC_FIX' ) && FL_BUILDER_MODSEC_FIX ) {

			if ( is_string( $settings ) ) {
				$settings = wp_slash( base64_decode( $settings ) );
			} else {

				foreach ( $settings as $key => $value ) {

					if ( is_string( $settings[ $key ] ) ) {
						$settings[ $key ] = wp_slash( base64_decode( $value ) );
					} elseif ( is_array( $settings[ $key ] ) ) {
						$settings[ $key ] = self::modsec_fix_decode( $settings[ $key ] );
					}
				}
			}
		}

		return $settings;
	}

	/**
	 * Get video type and ID from a given URL
	 *
	 * @since 1.9
	 * @param string $url   The URL to check for video type
	 * @param string $type  The type of video to check
	 * @return array
	 */
	static public function get_video_data( $url, $type = '' ) {
		if ( empty( $url ) ) {
			return false;
		}

		$y_matches  = array();
		$vm_matches = array();
		$yt_pattern = '/^(?:(?:(?:https?:)?\/\/)?(?:www.)?(?:youtu(?:be.com|.be))\/(?:watch\?v\=|v\/|embed\/)?([\w\-]+))/is';
		$vm_pattern = '#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#';
		$video_data = array(
			'type'     => 'mp4',
			'video_id' => '',
		);

		preg_match( $yt_pattern, $url, $yt_matches );
		preg_match( $vm_pattern, $url, $vm_matches );

		if ( isset( $yt_matches[1] ) ) {
			$video_data['type']     = 'youtube';
			$video_data['video_id'] = $yt_matches[1];

			parse_str( parse_url( $url, PHP_URL_QUERY ), $yt_params );
			if ( ! empty( $yt_params ) ) {

				// If start time is specified, make sure to convert it into seconds.
				if ( isset( $yt_params['t'] ) ) {
					$minutes         = 0;
					$seconds         = 0;
					$time_in_seconds = 0;

					// Check for minutes.
					if ( strpos( $yt_params['t'], 'm' ) !== false ) {
						$start_mins = preg_split( '([0-9]+[s])', $yt_params['t'] );
						if ( $start_mins ) {
							$minutes = (int) substr( $start_mins[0], 0, -1 ) * 60;
						}
					}

					if ( strpos( $yt_params['t'], 's' ) !== false ) {
						$start_secs = preg_split( '([0-9]+[m])', $yt_params['t'] );

						// Triggered when: &t=1m2s
						if ( isset( $start_secs[1] ) ) {
							$seconds = substr( $start_secs[1], 0, -1 );

							// Triggered when: &t=1s
						} elseif ( isset( $start_secs[0] ) && ! empty( $start_secs[0] ) ) {
							$seconds = substr( $start_secs[0], 0, -1 );
						}
					}

					$time_in_seconds = $minutes + $seconds;
					if ( $time_in_seconds > 0 ) {
						$yt_params['t'] = $time_in_seconds;
					}
				}

				$video_data['params'] = $yt_params;
			}
		} elseif ( isset( $vm_matches[1] ) ) {
			$video_data['type']     = 'vimeo';
			$video_data['video_id'] = $vm_matches[1];
		}

		if ( ! empty( $type ) ) {
			if ( $type === $video_data['type'] ) {
				return $video_data['video_id'];
			} else {
				return false;
			}
		}

		return $video_data;
	}

	/**
	 * Use mb_strtolower() if available.s
	 * @since 2.0.2
	 */
	static public function strtolower( $text, $encoding = 'UTF-8' ) {

		if ( function_exists( 'mb_strtolower' ) ) {
			return mb_strtolower( $text, $encoding );
		}
		return strtolower( $text );
	}

	/**
	 * Sanitize a value for js
	 * @since 2.0.7
	 */
	static public function sanitize_number( $value ) {

		if ( is_numeric( $value ) ) {
			return $value;
		}

		return 0;
	}

	/**
	 * Sanitize a value for js
	 * @since 2.1.3
	 */
	static public function sanitize_non_negative_number( $value ) {

		if ( is_numeric( $value ) && floatval( $value ) >= 0 ) {
			return $value;
		}

		return 0;
	}

	/**
	 * Version safe json_encode
	 * @since 2.2.4
	 */
	static public function json_encode( $data ) {
		if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
			return json_encode( $data );
		} else {
			return json_encode( $data, JSON_PARTIAL_OUTPUT_ON_ERROR );
		}
	}

	/**
	 * @since 2.4
	 */
	public static function get_safe_url( $post_id ) {

		global $post;

		$_original = $post;

		setup_postdata( $post_id );

		$post->post_status = 'draft';

		$url = get_permalink( $post );

		$post = $_original;

		return $url;
	}

	/**
	 * @since 2.4
	 */
	public static function img_lazyload( $loading = 'load' ) {
		return apply_filters( 'fl_lazyload', "loading='$loading'" );
	}

	/**
	 * @since 2.4.1
	 */
	public static function get_current_user_role() {
		if ( is_user_logged_in() ) {
			global $wp_roles;
			$user = wp_get_current_user();
			$role = (array) $user->roles;
			if ( isset( $role[0] ) && isset( $wp_roles->roles[ $role[0] ] ) ) {
				return esc_attr( $wp_roles->roles[ $role[0] ]['name'] );
			}
			if ( isset( $role[0] ) ) {
				return $role[0];
			}
			return 'Unknown';
		}
	}

	/**
	 * @since 2.5
	 */
	public static function update_option( $option, $value, $autoload = false ) {
		return update_option( $option, $value, $autoload );
	}
}
