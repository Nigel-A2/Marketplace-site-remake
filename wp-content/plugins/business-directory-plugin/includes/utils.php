<?php
/**
 * @package WPBDP
 */

require_once WPBDP_INC . 'debugging.php';
require_once WPBDP_INC . 'helpers/class-database-helper.php';
require_once WPBDP_INC . 'helpers/class-email.php';
require_once WPBDP_INC . 'compatibility/class-ajax-response.php';
require_once WPBDP_INC . 'helpers/class-fs.php';

class WPBDP_Utils {

    /**
     * @since 5.2.1
     */
    public static $property = null;

    /**
     * @since 3.6.10
     */
    public static function normalize( $val = '', $opts = array() ) {
        $res = strtolower( $val );
        $res = remove_accents( $res );
        $res = preg_replace( '/\s+/', '_', $res );
        $res = preg_replace( '/[^a-zA-Z0-9_-]+/', '', $res );

        return $res;
    }

    /**
     * @since 5.0
     */
    public static function sort_by_property( &$array, $prop ) {
        self::$property = $prop;

        uasort( $array, array( 'WPBDP_Utils', 'sort_by_property_callback' ) );

        self::$property = null;
    }

	/**
	 * @param array $left   Entry to compare.
	 * @param array $right  Entry to compare.
	 * @since 5.2.1
	 */
	public static function sort_by_property_callback( $left, $right ) {
		self::get_sort_value( $left );
		self::get_sort_value( $right );

		return $left - $right;
	}

	private static function get_sort_value( &$side ) {
		$side = (array) $side;
		$side = isset( $side[ self::$property ] ) ? $side[ self::$property ] : 0;
	}

    /**
     * @since 5.0
     */
    public static function table_exists( $table ) {
        global $wpdb;

		$res = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
        return count( $res ) > 0;
    }

    /**
     * @since 5.0
     */
    public static function table_has_col( $table, $col ) {
        if ( ! self::table_exists( $table ) )
            return false;

        global $wpdb;
        $columns = wp_filter_object_list( $wpdb->get_results( "DESCRIBE {$table}" ), array(), 'and', 'Field' );
        return in_array( $col, $columns, true );
    }

    /**
     * @since 5.0
     */
    public static function table_drop_col( $table, $col ) {
        if ( ! self::table_has_col( $table, $col ) )
            return false;

        global $wpdb;
        $wpdb->query( "ALTER TABLE {$table} DROP COLUMN {$col}" );
    }

	/**
	 * Check cache before fetching values and saving to cache
	 *
	 * @since v5.9
	 *
	 * @param array  $args {
	 *     @type string $cache_key The unique name for this cache
	 *     @type string $group The name of the cache group
	 *     @type string $query If blank, don't run a db call
	 *     @type string $type The wpdb function to use with this query
	 *     @type int    $time When the cahce should expire
     * }
	 *
	 * @return mixed $results The cache or query results
	 */
	public static function check_cache( $args ) {
		$defaults = array(
			'cache_key' => '',
			'group'     => '',
			'query'     => '',
			'type'      => 'get_var',
			'time'      => 300,
			'return'    => 'object',
		);
		$args = array_merge( $defaults, $args );

		$type  = $args['type'];
		$query = $args['query'];

		$results = wp_cache_get( $args['cache_key'], $args['group'] );
		if ( $results !== false || empty( $query ) ) {
			return $results;
		}

		if ( 'get_posts' === $type ) {
			$results = get_posts( $query );
		} elseif ( 'get_post' === $type ) {
			$results = get_post( $query );
		} elseif ( 'all' === $type ) {
			$results = self::get_all_ids( $args );
		} elseif ( 'get_associative_results' === $type ) {
			global $wpdb;
			$results = $wpdb->get_results( $query, OBJECT_K ); // WPCS: unprepared SQL ok.
		} else {
			global $wpdb;
			if ( $args['return'] === 'array' ) {
				$results = $wpdb->{$type}( $query, ARRAY_A );
			} else {
				$results = $wpdb->{$type}( $query );
			}
		}

		self::set_cache( $args['cache_key'], $results, $args['group'], $args['time'] );

		return $results;
	}

	/**
	 * Reduce database calls by getting all rows at once.
	 *
	 * @since 5.11
	 */
	private static function get_all_ids( $args ) {
		global $wpdb;

		$table = $args['group'];
		$keys  = $args['query'];
		if ( empty( $keys ) ) {
			$keys = array( 'id' );
		}

		$query = 'SELECT * FROM ' . $wpdb->prefix . $table;
		if ( $args['return'] === 'array' ) {
			$results = $wpdb->get_results( $query, ARRAY_A );
		} else {
			$results = $wpdb->get_results( $query );
		}

		if ( ! $results ) {
			return array();
		}

		$all_rows = array();
		foreach ( $results as $row ) {
			foreach ( $keys as $key ) {
				$all_rows[ $row->$key ] = $row;
			}
		}

		return $all_rows;
	}

	/**
	 * @since v5.9
	 */
	public static function set_cache( $cache_key, $results, $group = '', $time = 300 ) {
		self::add_key_to_group_cache( $cache_key, $group );
		wp_cache_set( $cache_key, $results, $group, $time );
	}

	/**
	 * Keep track of the keys cached in each group so they can be deleted
	 * in Redis and Memcache
	 *
	 * @since v5.9
	 */
	public static function add_key_to_group_cache( $key, $group ) {
		$cached         = self::get_group_cached_keys( $group );
		$cached[ $key ] = $key;
		wp_cache_set( 'cached_keys', $cached, $group, 300 );
	}

	/**
	 * @since v5.9
	 */
	public static function get_group_cached_keys( $group ) {
		$cached = wp_cache_get( 'cached_keys', $group );
		if ( ! $cached || ! is_array( $cached ) ) {
			$cached = array();
		}

		return $cached;
	}

	/**
	 * Delete all caching in a single group
	 *
	 * @since v5.9
	 *
	 * @param string $group The name of the cache group
	 */
	public static function cache_delete_group( $group ) {
		$cached_keys = self::get_group_cached_keys( $group );

		if ( ! empty( $cached_keys ) ) {
			foreach ( $cached_keys as $key ) {
				wp_cache_delete( $key, $group );
			}

			wp_cache_delete( 'cached_keys', $group );
		}
	}

	/**
	 * Check if value contains blank value or empty array
	 *
	 * @since 5.9
	 *
	 * @param mixed  $value The value to check
	 * @param string $empty
	 *
	 * @return boolean
	 */
	public static function is_empty_value( $value, $empty = '' ) {
		return ( is_array( $value ) && empty( $value ) ) || $value === $empty;
	}

	public static function media_upload( $file_, $use_media_library = true, $check_image = false, $constraints = array(), &$error_msg = null, $sideload = false ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$sideload = is_string( $file_ ) && file_exists( $file_ );

		if ( $sideload ) {
			$mime_type = self::get_mimetype( $file_ );

			$file = array(
				'name' => basename( $file_ ),
				'tmp_name' => $file_,
				'type' => $mime_type,
				'error' => 0,
				'size' => filesize( $file_ )
			);
		} else {
			$file = $file_;
		}

		if ( ! self::is_valid_upload( $file, $constraints, $error_msg ) ) {
            return false;
		}

		if ( ! $use_media_library ) {
			$upload = $sideload ? wp_handle_sideload( $file, array( 'test_form' => false ) ) : wp_handle_upload( $file, array( 'test_form' => false ) );

			if ( ! $upload || ! is_array( $upload ) || isset( $upload['error'] ) ) {
				$error_msg = isset( $upload['error'] ) ? $upload['error'] : _x( 'Unkown error while uploading file.', 'utils', 'business-directory-plugin' );
				return false;
			}

			return $upload;
		}

		$file_id = self::get_file_id( $file_ );
		if ( ! $sideload && ! empty( $_FILES[ $file_id ]['name'] ) && is_array( $_FILES[ $file_id ]['name'] ) ) {
			// Force an array of files to a single file.
			$file_id = substr( sha1( (string) rand() ), 0, 5 );
			$_FILES[ $file_id ] = $file;
		}

		$attachment_id = $sideload ? media_handle_sideload( $file, 0 ) : media_handle_upload( $file_id, 0 );

		if ( is_wp_error( $attachment_id ) ) {
			$error_msg = $attachment_id->get_error_message();
			return false;
		}

		if ( ! is_numeric( $attachment_id ) ) {
			$error_msg = $attachment_id;
			return false;
		}

		return $attachment_id;
	}

	/**
	 * Attach an image to a media library after upload from `wp_handle_upload` or `wp_handle_sideload`.
	 * This is used to include an image into the media library and does not resize the image after import.
	 *
	 * @param array $file_data (
	 *     @type string $file Filename of the newly-uploaded file.
 	 *     @type string $url  URL of the newly-uploaded file.
 	 *     @type string $type Mime type of the newly-uploaded file.
 	 * )
	 * @param int $post_id The optional post id to attatch the image to
	 *
	 * @since 5.18
	 *
	 * @return int|false The attachement id
	 */
	public static function attach_image_to_media_library( $file_data, $post_id = 0 ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$file       = $file_data['file'];
		$ext        = pathinfo( $file, PATHINFO_EXTENSION );
		$name       = wp_basename( $file, ".$ext" );
		$title      = sanitize_file_name( $name );
		$excerpt    = '';
		$image_meta = wp_read_image_metadata( $file );
		if ( $image_meta ) {
			$image_title = sanitize_title( $image_meta['title'] );
			if ( ! is_numeric( $image_title ) ) {
				$title = $image_meta['title'];
			}

			if ( trim( $image_meta['caption'] ) ) {
				$excerpt = $image_meta['caption'];
			}
		}
		$attachment = array(
			'post_mime_type' => $file_data['type'],
			'guid'           => $file_data['url'],
			'post_parent'    => $post_id,
			'post_title'     => $title,
			'post_excerpt'   => $excerpt,
		);
		$attachment_id = wp_insert_attachment( $attachment, $file, $post_id, true );
		if ( is_wp_error( $attachment_id ) ) {
			return false;
		}
		wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
		return $attachment_id;
	}

	/**
	 * Attempts to get the mimetype of a file.
	 *
	 * @param string $file The path to a file.
	 *
	 * @since 5.16
	 */
	public static function get_mimetype( $file ) {
		$mime_type = null;

		if ( function_exists( 'mime_content_type' ) ) {
			$mime_type = mime_content_type( $file );
		}

		if ( null === $mime_type ) {
			$type_info = wp_check_filetype( $file, wp_get_mime_types() );
			$mime_type = $type_info['type'];
		}

		wpbdp_sanitize_value( 'sanitize_text_field', $mime_type );
		return $mime_type;
	}

	/**
	 * @since 5.16
	 */
	private static function is_valid_upload( $file, $constraints, &$error_msg ) {
		self::get_file_contrstraints( $constraints );

	    if ( $file['error'] !== 0 ) {
			$error_msg = _x( 'Error while uploading file', 'utils', 'business-directory-plugin' );
			return false;
		}

		if ( $constraints['max-size'] > 0 && $file['size'] > $constraints['max-size'] ) {
			$error_msg = sprintf(
				__( 'File size (%1$s) exceeds maximum file size of %2$s', 'business-directory-plugin' ),
				size_format( $file['size'], 2 ),
				size_format( $constraints['max-size'], 2 )
			);
			return false;
		}

		if ( $constraints['min-size'] > 0 && $file['size'] < $constraints['min-size'] ) {
			$error_msg = sprintf(
				__( 'File size (%1$s) is smaller than the minimum file size of %2$s', 'business-directory-plugin' ),
				size_format( $file['size'], 2 ),
				size_format( $constraints['min-size'], 2 )
			);
			return false;
		}

		if ( is_array( $constraints['mimetypes'] ) && ! self::is_valid_file_type( $file, $constraints['mimetypes'] ) ) {
			$error_msg = sprintf( _x( 'File type "%s" is not allowed', 'utils', 'business-directory-plugin' ), $file['type'] );
			return false;
		}

		// We do not accept TIFF format. Compatibility issues.
		if ( in_array( strtolower( $file['type'] ), array('image/tiff') ) ) {
			$error_msg = sprintf( _x( 'File type "%s" is not allowed', 'utils', 'business-directory-plugin' ), $file['type'] );
			return false;
		}

		return true;
	}

	/**
	 * @since 5.16
	 */
	private static function get_file_contrstraints( &$constraints ) {
		$constraints = array_merge(
			array(
				'image' => false,
				'min-size' => 0,
				'max-size' => 0,
				'min-width' => 0,
				'min-height' => 0,
				'max-width' => 0,
				'max-height' => 0,
				'mimetypes'  => null,
			),
			$constraints
		);

		foreach ( array( 'min-size', 'max-size', 'min-width', 'min-height', 'max-width', 'max-height' ) as $k ) {
			$constraints[ $k ] = absint( $constraints[ $k ] );
		}
	}

	/**
	 * Check the file type and extension.
	 *
	 * @param array $file
	 * @param array $mimetypes
	 *
	 * @since 6.0
	 *
	 * @return bool
	 */
	private static function is_valid_file_type( $file, $mimetypes ) {
		// If this is a multidimensional array, flatten it.
		if ( is_array( reset( $mimetypes ) ) ) {
			$mimetypes = array_values( $mimetypes );
			$mimetypes = call_user_func_array( 'array_merge', $mimetypes );
		}

		$mime_allowed = in_array( strtolower( $file['type'] ), $mimetypes, true );
		if ( ! $mime_allowed ) {
			return false;
		}

		// If the keys are numeric, we don't have the extensions to check.
		$check_extension = array_filter( array_keys( $mimetypes ), 'is_string' );
		if ( empty( $check_extension ) ) {
			return true;
		}

		$filename = sanitize_file_name( (string) wp_unslash( $file['name'] ) );
		$matches  = wp_check_filetype( $filename, $mimetypes );
		return ! empty( $matches['ext'] );
	}

	/**
	 * We have the file info, but WP needs the file id.
	 *
	 * @since 5.16
	 */
	private static function get_file_id( $_file ) {
		if ( empty( $_FILES ) ) {
			return '';
		}
		foreach ( $_FILES as $id => $file ) {
			if ( $file === $_file ) {
				return esc_attr( $id );
			}
		}
		reset( $_FILES );
		return esc_attr( key( $_FILES ) );
	}
}

/**
 * @deprecated Use {@link WPBDP_Utils} instead.
 */
class WPBDP__Utils extends WPBDP_Utils {
	public function __construct() {
		_deprecated_constructor( __CLASS__, '', 'WPBDP_Utils' );
	}
}


/**
 * Restructures multidimensional $_FILES arrays into one key-based array per file.
 * Single-file arrays are returned as an array of one item for consistency.
 *
 * @since 3.4
 *
 * @param array $files $_FILES array
 * @return array
 */
function wpbdp_flatten_files_array( $files = array() ) {
    if ( ! isset( $files['tmp_name'] ) )
        return $files;

    if ( ! is_array( $files['tmp_name'] ) )
        return array( $files );

    $res = array();
    foreach ( $files as $k1 => $v1 ) {
        foreach ( $v1 as $k2 => $v2 ) {
            $res[ $k2 ][ $k1 ] = $v2;
        }
    }

    return $res;
}


/**
 * Returns properties and array values from objects or arrays, resp.
 *
 * @param array|object $dict
 * @param string|int   $key Property name or array key.
 * @param mixed        $default Optional. Defaults to `false`.
 */
function wpbdp_getv( $dict, $key, $default = false ) {
	$_dict = is_object( $dict ) ? (array) $dict : $dict;

	if ( is_array( $_dict ) && isset( $_dict[ $key ] ) ) {
		return $_dict[ $key ];
	}

    return $default;
}

/**
 * Get any value from the $_SERVER
 *
 * @since 5.7.6
 *
 * @param string $value
 *
 * @return string
 */
function wpbdp_get_server_value( $value ) {
	return isset( $_SERVER[ $value ] ) ? wp_strip_all_tags( wp_unslash( $_SERVER[ $value ] ) ) : '';
}

/**
 * @since 5.7.6
 *
 * @param array $args - Includes 'param' and 'sanitize'.
 *
 * @return array|string|int|float|mixed
 */
function wpbdp_get_var( $args, $type = 'get' ) {
    $defaults = array(
        'sanitize' => 'sanitize_text_field',
        'default'  => '',
    );
    $args     = wp_parse_args( $args, $defaults );
    $value    = $args['default'];
    if ( $type === 'get' ) {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $value = isset( $_GET[ $args['param'] ] ) ? wp_unslash( $_GET[ $args['param'] ] ) : $value;
    } elseif ( $type === 'post' ) {
        // phpcs:ignore Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $value = isset( $_POST[ $args['param'] ] ) ? wp_unslash( $_POST[ $args['param'] ] ) : $value;
    } else {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $value = isset( $_REQUEST[ $args['param'] ] ) ? wp_unslash( $_REQUEST[ $args['param'] ] ) : $value;
    }

    wpbdp_sanitize_value( $args['sanitize'], $value );

    return $value;
}

/**
 * @since 5.7.6
 *
 * @param string $sanitize
 * @param array|string $value
 */
function wpbdp_sanitize_value( $sanitize, &$value ) {
    if ( empty( $sanitize ) ) {
        return;
    }
    if ( is_array( $value ) ) {
        $temp_values = $value;
        foreach ( $temp_values as $k => $v ) {
            wpbdp_sanitize_value( $sanitize, $value[ $k ] );
        }
    } else {
        $value = call_user_func( $sanitize, $value );
    }
}

function wpbdp_capture_action( $hook ) {
    $output = '';

    $args = func_get_args();
	if ( count( $args ) > 1 ) {
		$args = array_slice( $args, 1 );
    } else {
        $args = array();
    }

    ob_start();
	do_action_ref_array( $hook, $args );
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}

function wpbdp_capture_action_array( $hook, $args = array() ) {
    $output = '';

    ob_start();
	do_action_ref_array( $hook, $args );
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}

function wpbdp_php_ini_size_to_bytes( $val ) {
    $val = trim( $val );
    $size = intval( $val );
	$unit = strtoupper( $val[ strlen( $val ) - 1 ] );

    switch ( $unit ) {
        case 'G':
            $size *= 1024;
			// no break
        case 'M':
            $size *= 1024;
			// no break
        case 'K':
            $size *= 1024;
    }

    return $size;
}

function wpbdp_media_upload_check_env( &$error ) {
    if ( empty( $_FILES ) && empty( $_POST ) && isset( $_SERVER['REQUEST_METHOD'] ) &&
        strtolower( wpbdp_get_server_value( 'REQUEST_METHOD' ) ) === 'post' ) {
        $post_max    = wpbdp_php_ini_size_to_bytes( ini_get( 'post_max_size' ) );
        $posted_size = intval( wpbdp_get_server_value( 'CONTENT_LENGTH' ) );

        if ( $posted_size > $post_max ) {
            $error = _x( 'POSTed data exceeds PHP config. maximum. See "post_max_size" directive.', 'utils', 'business-directory-plugin' );
            return false;
        }
    }

    return true;
}

/**
 * @since 2.1.6
 */
function wpbdp_media_upload( $file_, $use_media_library = true, $check_image = false, $constraints = array(), &$error_msg = null, $sideload = false ) {
	return WPBDP_Utils::media_upload( $file_, $use_media_library, $check_image, $constraints, $error_msg, $sideload );
}

/**
 * Attempts to get the mimetype of a file.
 *
 * @param string $file The path to a file.
 *
 * @since 5.0.5
 */
function wpbdp_get_mimetype( $file ) {
    return WPBDP_Utils::get_mimetype( $file );
}

/**
 * Returns the domain used in the current request, optionally stripping
 * the www part of the domain.
 *
 * @since 2.1.5
 * @param boolean $www true to include the 'www' part.
 */
function wpbdp_get_current_domain( $www = true, $prefix = '' ) {
    $domain = wpbdp_get_server_value( 'HTTP_HOST' );
	if ( empty( $domain ) ) {
		$domain = wpbdp_get_server_value( 'SERVER_NAME' );
	}

	if ( ! $www && substr( $domain, 0, 4 ) === 'www.' ) {
		$domain = $prefix . substr( $domain, 4 );
	}

    return $domain;
}

/**
 * Prepare an external link with utm parameters.
 *
 * @since 5.7.5
 *
 * @param array|string $args
 * @param string $page
 */
function wpbdp_admin_upgrade_link( $args, $page = '' ) {
    if ( empty( $page ) ) {
        $page = 'https://businessdirectoryplugin.com/lite-upgrade/';
    } else {
        $page = str_replace( 'https://businessdirectoryplugin.com/', '', $page );
        $page = 'https://businessdirectoryplugin.com/' . $page;
    }

    $anchor = '';
    if ( is_array( $args ) ) {
        $medium  = $args['medium'];
        $content = $args['content'];
        if ( isset( $args['anchor'] ) ) {
            $anchor = '#' . $args['anchor'];
        }
    } else {
        $medium = $args;
    }

    $query_args = array(
        'utm_source'   => 'WordPress',
        'utm_medium'   => $medium,
        'utm_campaign' => 'liteplugin',
    );

    if ( isset( $content ) ) {
        $query_args['utm_content'] = $content;
    }

    if ( is_array( $args ) && isset( $args['param'] ) ) {
        $query_args['f'] = $args['param'];
    }

    $link = add_query_arg( $query_args, $page ) . $anchor;
    return $link;
}

/**
 * Bulds WordPress ajax URL using the same domain used in the current request.
 *
 * @since 2.1.5
 */
function wpbdp_ajaxurl( $overwrite = false ) {
    static $ajaxurl = false;

	if ( $overwrite || $ajaxurl === false ) {
		$url   = admin_url( 'admin-ajax.php' );
		$parts = parse_url( $url );

        $domain = wpbdp_get_current_domain();

        // Since $domain already contains the port remove it.
        if ( isset( $parts['port'] ) && $parts['port'] )
            $domain = str_replace( ':' . $parts['port'], '', $domain );

		$ajaxurl = str_replace( $parts['host'], $domain, $url );
    }

    return $ajaxurl;
}

/**
 * Removes a value from an array.
 *
 * @since 2.3
 */
function wpbdp_array_remove_value( &$array_, &$value_ ) {
    $key = array_search( $value_, $array_ );

    if ( $key !== false ) {
		unset( $array_[ $key ] );
    }

    return true;
}

/**
 * Checks if a given string starts with another string.
 *
 * @param string $str the string to be searched
 * @param string $prefix the prefix to search for
 * @return bool  true if $str starts with $prefix or FALSE otherwise
 * @since 3.0.3
 */
function wpbdp_starts_with( $str, $prefix, $case_sensitive = true ) {
	if ( ! $case_sensitive ) {
        return stripos( $str, $prefix, 0 ) === 0;
	}

    return strpos( $str, $prefix, 0 ) === 0;
}

/**
 * @since 3.1
 */
function wpbdp_format_time( $time = null, $format = 'mysql', $time_is_date = false ) {
	if ( $format === 'mysql' ) {
		$time = date( 'Y-m-d H:i:s', $time );
	}

    return $time;
}

/**
 * Returns the contents of a directory (ignoring . and .. special files).
 *
 * @param string $path a directory.
 * @return array list of files within the directory.
 * @since 3.3
 */
function wpbdp_scandir( $path, $args = array() ) {
	if ( ! is_dir( $path ) ) {
        return array();
	}
/*
    $defaults = array(
        'filter' => false
    );
    $args = wp_parse_args( $args, $defaults );
    extract( $args );

    $filter = is_array( $filter ) ? $filter : ( $filter ? (array) $filter : false );*/
    $res = array_diff( scandir( $path ), array( '.', '..' ) );

/*    foreach ( $res as $i => &$r ) {
        $r = untrailingslashit( $path ) . '/' . $r;

        if ( $filter && ( ( ! in_array( 'dir', $filter ) && is_dir( $r ) ) || ( ! in_array( 'file', $filter ) && is_file( $r ) ) ) )
            unset( $res[ $i ] );
    }
*/
    return $res;
//    return array_diff( scandir( $path ), array( '.', '..' ) );
}

/**
 * Returns the name of a term.
 *
 * @param int|string $id_or_slug The term ID or slug (see `$field`).
 * @param string     $taxonomy Taxonomy name. Defaults to `WPBDP_CATEGORY_TAX` (BD's category taxonomy).
 * @param string     $field Field used for the term lookup. Defaults to "id".
 * @param boolean    $escape Whether to escape the name before returning or not. Defaults to `True`.
 *
 * @return string The term name (if found) or an empty string otherwise.
 * @since 3.3
 */
function wpbdp_get_term_name( $id_or_slug, $taxonomy = WPBDP_CATEGORY_TAX, $field = 'id', $escape = true ) {
	$term = get_term_by(
		$field,
		'id' == $field ? intval( $id_or_slug ) : $id_or_slug,
		$taxonomy
	);

    if ( ! $term )
        return '';

    return $term->name;
}

function wpbdp_has_shortcode( &$content, $shortcode ) {
    $check = has_shortcode( $content, $shortcode );

    if ( ! $check ) {
        // Sometimes has_shortcode() fails so we try another approach.
        if ( false !== stripos( $content, '[' . $shortcode . ']' ) )
            $check = true;
    }

    return $check;
}

/**
 * TODO: dodoc.
 *
 * @since 3.4.2
 */
function wpbdp_text_from_template( $setting_name, $replacements = array() ) {
    $setting = wpbdp()->settings->get_setting( $setting_name );

    if ( ! $setting )
        return false;

    $text = wpbdp_get_option( $setting_name );

    if ( ! $text )
        return false;

    $placeholders = isset( $setting['placeholders'] ) ? array_keys( $setting['placeholders'] ) : array();

    foreach ( $replacements as $pholder => $repl ) {
        if ( ! in_array( $pholder, $placeholders, true ) )
            continue;

        $text = str_replace( '[' . $pholder . ']', $repl, $text );
    }

    return $text;
}

/**
 * @since 3.5.4
 */
function wpbdp_email_from_template( $setting_or_file, $replacements = array(), $args = array() ) {
    $setting = null;
    $file = null;
    $object = null;

    if ( is_string( $setting_or_file ) && is_file( $setting_or_file ) && is_readable( $setting_or_file ) ) {
        $file = $setting_or_file;
    } else if ( is_array( $setting_or_file ) || is_object( $setting_or_file ) ) {
        $object = $setting_or_file;
    } else {
        $setting = wpbdp()->settings->get_setting( $setting_or_file );
    }

    if ( ( ! $setting && ! $file && ! $object ) || ( $setting && 'email_template' != $setting['type'] ) )
        return false;

	if ( ! class_exists( 'WPBDP_Email' ) ) {
		require_once WPBDP_PATH . 'includes/helpers/class-email.php';
	}

    $placeholders = $setting ? ( isset( $setting['placeholders'] ) && is_array( $setting['placeholders'] ) ? $setting['placeholders'] : array() ) : array();

    // Add core replacements.
	$replacements = array_merge(
		$replacements,
		array(
			'site-title'    => get_bloginfo( 'name' ),
			'site-link'     => sprintf( '<a href="%s">%s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
			'site-url'      => sprintf( '<a href="%s">%s</a>', get_bloginfo( 'url' ), get_bloginfo( 'url' ) ),
			'directory-url' => sprintf( '<a href="%1$s">%1$s</a>', wpbdp_get_page_link( 'main' ) ),
			'today'         => date_i18n( get_option( 'date_format' ) ),
			'now'           => date_i18n( get_option( 'time_format' ) )
		)
	);

    if ( $file ) {
        $keys = array_keys( $replacements );
        $values = array_values( $replacements );

        // Normalize keys for PHP usage.
        foreach ( $keys as &$k )
            $k = str_replace( '-', '_', $k );

        $replacements = array_combine( $keys, $values );
    }

    $subject = '';
    $body = '';

    if ( $setting || $object ) {
        $value = $setting ? wpbdp_get_option( $setting['id'] ) : (array) $object;

        // Support old-style settings.
        if ( ! is_array( $value ) ) {
            $subject = $setting['default']['subject'];
            $body = $setting['default']['body'];
        } else {
            $subject = $value['subject'];
            $body = $value['body'];
        }

        $placeholders = $replacements; // XXX: does this work ok?
        foreach ( array_keys( $placeholders ) as $placeholder ) {
            if ( ! isset( $replacements[ $placeholder ] ) )
                continue;

            $subject = str_replace( '[' . $placeholder . ']', $replacements[ $placeholder ], $subject );
            $body = str_replace( '[' . $placeholder . ']', $replacements[ $placeholder ], $body );
        }
    } elseif ( $file ) {
        $body = wpbdp_render_page( $file, $replacements );
    }

    $email = new WPBDP_Email();

    if ( $subject )
        $email->subject = $subject;

    $email->body = $body;

    return $email;
}

function wpbdp_admin_pointer( $selector, $title, $content_ = '',
                              $primary_button = false, $primary_action = '',
                              $secondary_button = false, $secondary_action = '',
                              $options = array() ) {
    if ( ! current_user_can( 'administrator' ) || ( get_bloginfo( 'version' ) < '3.3' ) )
        return;

    $content  = '';
    $content .= '<h3>' . $title . '</h3>';
    $content .= '<p>' . $content_ . '</p>';
?>
<script>
//<![CDATA[
jQuery(function( $ ) {
        var wpbdp_pointer = $( '<?php echo $selector; ?>' ).pointer({
            'content': <?php echo json_encode( $content ); ?>,
            'position': { 'edge': '<?php echo isset( $options['edge'] ) ? $options['edge'] : 'top'; ?>',
                          'align': '<?php echo isset( $options['align'] ) ? $options['align'] : 'center'; ?>' },
            'buttons': function( e, t ) {
				<?php if ( ! $secondary_button ) : ?>
                var b = $( '<a id="wpbdp-pointer-b1" class="button button-primary">' + '<?php echo $primary_button; ?>' + '</a>' );
				<?php else : ?>
                var b = $( '<a id="wpbdp-pointer-b2" class="button" style="margin-right: 15px;">' + '<?php echo $secondary_button; ?>' + '</a>' );
                <?php endif; ?>
                return b;
            }
        }).pointer('open');

		<?php if ( $secondary_button ) : ?>
        $( '#wpbdp-pointer-b2' ).before( '<a id="wpbdp-pointer-b1" class="button button-primary">' + '<?php echo $primary_button; ?>' + '</a>' );
        $( '#wpbdp-pointer-b2' ).click(function(e) {
            e.preventDefault();
			<?php if ( $secondary_action ) : ?>
            <?php echo $secondary_action; ?>
            <?php endif; ?>
            wpbdp_pointer.pointer( 'close' );
        });
        <?php endif; ?>

        $( '#wpbdp-pointer-b1' ).click(function(e) {
            e.preventDefault();
			<?php if ( $primary_action ) : ?>
            <?php echo $primary_action; ?>
            <?php endif; ?>
            wpbdp_pointer.pointer( 'close' );
        });

});
//]]>
</script>
<?php
}

/**
 * No op object used to prevent modules from breaking a site while performing a manual upgrade
 * or something similar.
 * Instances of this class allow accessing any property or calling any function without side effects (errors).
 *
 * @since 3.4dev
 */
class WPBDP_NoopObject {

    public function __construct() {
    }

    public function __set( $k, $v ) { }

    public function __get( $k ) {
		return null;
	}

	public function __isset( $k ) {
		return false;
	}

    public function __unset( $k ) { }

    public function __call( $name, $args = array() ) {
        return false;
    }

}

// For compat with PHP < 5.3
if ( ! function_exists( 'str_getcsv' ) ) {

    function str_getcsv( $input, $delimiter = ',', $enclosure = '"' ) {
        $file = tmpfile();

        fwrite( $file, $input );
        fseek( $file, 0 );

        $res = fgetcsv( $file, 0, $delimiter, $enclosure );

        fclose( $file );

        return $res;
    }
}

/**
 * @since 4.0.5dev
 */
function wpbdp_detect_encoding( $content ) {
    static $encodings = array(
        'UTF-8', 'UTF-16LE', 'ASCII',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
        'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
        'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'Windows-1251', 'Windows-1252', 'Windows-1254',
    );

    if ( function_exists( 'mb_detect_encoding' ) ) {
        // XXX: mb_detect_encoding() can't detect UTF-16* encodings
        // See documentation for mb_detect_order()
        return mb_detect_encoding( $content, $encodings, true );
    } else {
		if ( ! function_exists( 'iconv' ) ) {
			return 'UTF-8';
		} else {
			return wpbdp_mb_detect_encoding( $content, $encodings );
		}
    }
}

/**
 * Taken from http://php.net/manual/en/function.mb-detect-encoding.php#113983
 *
 * @since 4.0.5dev
 */
function wpbdp_mb_detect_encoding( $content, $encodings ) {
   foreach ( $encodings as $encoding ) {
        $sample = iconv( $encoding, $encoding, $content );
        if ( md5( $sample ) == md5( $content ) ) {
            return $encoding;
        }
    }

    return false;
}

function wpbdp_render_user_field( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class' => '',
			'name'  => 'user',
			'value' => null,
		)
	);

    $users_query = new WP_User_Query( array( 'count_total' => true, 'fields' => 'ID', 'number' => 200 ) );

    if ( $users_query->get_total() <= 200 ) {
        $output = '<select class="' . esc_attr( $args['class'] ) . '" name="' . esc_attr( $args['name'] ) . '">';

        foreach ( get_users( array( 'orderby' => 'display_name' ) ) as $user ) {
            $selected = $args['value'] == $user->ID ? ' selected="selected"' : '';

            $output .= '<option value="' . $user->ID . '"' . $selected . '>';
            $output .= "{$user->display_name} ({$user->user_login})";
            $output .= '</option>';
        }

        $output .= '</select>';
    } else {
        if ( $args['value'] ) {
            $user = get_user_by( 'ID', $args['value'] );
            $text_value = "{$user->display_name} ({$user->user_login})";
            $hidden_value = $user->ID;
        } else {
            $text_value = '';
            $hidden_value = 0;
        }

        $hidden_field_id = 'autocomplete-value-' . uniqid();

        $output = '<input class="wpbdp-user-autocomplete ' . esc_attr( $args['class'] ) . '" type="text" value="' . esc_attr( $text_value ) . '" data-hidden-field="' . $hidden_field_id . '" />';
		$output .= '<input id="' . esc_attr( $hidden_field_id ) . '" name="' . esc_attr( $args['name'] ) . '" type="hidden" value="' . absint( $hidden_value ) . '">';
    }

    return $output;
}

function wpbdp_enqueue_jquery_ui_style() {
	WPBDP__Assets::load_datepicker();
}

function wpbdp_buckwalter_arabic_transliteration( $content ) {
    $arabic_characters = array(
        'ء',
        'آ',
        'أ',
        'ؤ',
        'إ',
        'ئ',
        'ا',
        'ب',
        'ة',
        'ت',
        'ث',
        'ج',
        'ح',
        'خ',
        'د',
        'ذ',
        'ر',
        'ز',
        'س',
        'ش',
        'ص',
        'ض',
        'ط',
        'ظ',
        'ع',
        'غ',
        'ـ',
        'ف',
        'ق',
        'ك',
        'ل',
        'م',
        'ن',
        'ه',
        'و',
        'ى',
        'ي',
        'ً',
        'ٌ',
        'ٍ',
        'َ',
        'ُ',
        'ِ',
        'ّ',
        'ْ',
        'ٰ',
        'ٱ',
    );

    $english_characters = array(
        '\'',
        '|',
        'O', // replaced '>' with 'O' as suggested in http://www.qamus.org/transliteration.htm
        'W', // replaced '&' with 'W'
        'I', // replaced '<' with 'I'
        '}',
        'A',
        'b',
        'p',
        't',
        'v',
        'j',
        'H',
        'x',
        'd',
        '*',
        'r',
        'z',
        's',
        '$',
        'S',
        'D',
        'T',
        'Z',
        'E',
        'g',
        '_',
        'f',
        'q',
        'k',
        'l',
        'm',
        'n',
        'h',
        'w',
        'Y',
        'y',
        'F',
        'N',
        'K',
        'a',
        'u',
        'i',
        '~',
        'o',
        '`',
        '{',
    );

    return str_replace( $arabic_characters, $english_characters, $content );
}

/**
 * The function was originally developed as an static method in
 * WPBDP_Form_Field_Type. It has always rendered values as given, indirectly
 * expecting them to be already escaped with `esc_attr`.
 *
 * If you decide to use `esc_attr` inside the function, make sure to check
 * all places where the function is called, to avoid scaping values twice.
 *
 * @since 4.1.10
 */
function wpbdp_html_attributes( $attrs, $exceptions = array(), $echo = false ) {
    $html = '';

    foreach ( $attrs as $k => $v ) {
        if ( in_array( $k, $exceptions, true ) ) {
            continue;
        }

        $html .= sprintf( '%s="%s" ', esc_attr( $k ), esc_attr( $v ) );
    }

	if ( ! $echo ) {
		return $html;
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $html;
}

/**
 * @since 4.1.11
 */
function wpbdp_table_exists( $table_name ) {
    global $wpdb;

    $result = $wpdb->get_var( "SHOW TABLES LIKE '" . $table_name . "'" );

    return strcasecmp( $result, $table_name ) === 0;
}

/**
 * @since 5.0.5
 */
function wpbdp_column_exists( $table_name, $column_name ) {
    global $wpdb;

    $display_errors = $wpdb->hide_errors();
    $result = $wpdb->get_col( sprintf( 'SELECT %s FROM %s LIMIT 1', $column_name, $table_name ) );
    $wpdb->show_errors( $display_errors );

    return empty( $wpdb->last_error );
}

/**
 * @since 5.0
 */
function wpbdp_is_request( $type ) {
    switch ( $type ) {
    case 'admin':
        return is_admin();
    case 'ajax':
        return defined( 'DOING_AJAX' ) && DOING_AJAX;
    case 'cron':
        return defined( 'DOING_CRON' ) && DOING_CRON;
    case 'frontend':
        return ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) );
    }
}

/**
 * @since 5.0
 */
function wpbdp_deprecation_warning( $msg = '' ) {
    global $wpbdp_deprecation_warnings;

    if ( ! isset( $wpbdp_deprecation_warnings ) ) {
        $wpbdp_deprecation_warnings = array();
    }

    $wpbdp_deprecation_warnings[] = $msg;
}
