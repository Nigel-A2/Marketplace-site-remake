<?php
/**
 * CSV import class
 *
 * @package Includes/Admin/Helpers/CSV Import
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * Replaces `WPBDP_CSVImporter` (from 2.1) and adds support for sequential imports.
 *
 * @since 3.5.8
 */
class WPBDP_CSV_Import {

    const UTF8_BOM     = "\xEF\xBB\xBF";
    const UTF16_LE_BOM = "\xFF\xFE";

    private static $PERSISTENT = array( 'settings', 'header', 'total_lines', 'processed_lines', 'current_line', 'imported', 'rejected', 'errors', 'done' );

    private $state_id    = '';
    private $working_dir = '';

    private $state_file = '';
    private $csv_file   = '';
    private $images_dir = '';

    private $settings = array();

    private $header = array();
    private $fields = array();

    private $total_lines     = 0;
    private $processed_lines = 0;
    private $current_line    = 0;

    private $imported = 0;
    private $rejected = 0;
    private $errors   = array();
    private $done     = false;


    public function __construct( $state_id = '', $csv_file = '', $images_file = '', $settings = array() ) {

        if ( $state_id ) {
            $this->restore_state( $state_id );
        } else {
            if ( ! is_readable( $csv_file ) ) {
                throw new Exception( 'Invalid CSV file.' );
            }

            $this->setup_working_dir( $csv_file, $images_file );

			$this->setup_settings( $settings );

            $file = $this->get_csv_file();
            $file->seek( PHP_INT_MAX );
            $this->total_lines = absint( $file->key() );
            $file              = null;
        }

        if ( ! $this->header ) {
            $this->read_header();
        }
    }

	/**
	 * @since 5.11
	 */
	private function setup_settings( $settings ) {
		$defaults = array(
			'allow-partial-imports'       => true,
			'csv-file-separator'          => ',',
			'images-separator'            => ';',
			'category-separator'          => ';',
			'create-missing-categories'   => true,
			'assign-listings-to-user'     => false,
			'default-user'                => '0',
			'post-status'                 => 'publish',
			'existing-post-status'        => 'preserve_status',
			'disable-email-notifications' => false,
			'append-images'               => false,
			'test-import'                 => false,
			'batch-size'                  => 40,
		);

		if ( $settings['csv-file-separator'] === 'tab' ) {
			$settings['csv-file-separator'] = "\t";
		}

		$this->settings = array_merge( $defaults, $settings );
	}

    public function do_work() {
        if ( $this->done ) {
            return;
        }

        $file = $this->get_csv_file();
        $file->seek( $this->current_line );

        $n = 0;
		while ( $n < (int) $this->settings['batch-size'] ) {
            if ( $file->eof() && empty( $file->current() ) ) {
                $this->done = true;
                break;
            }

            $line = $this->get_current_line( $file );

            // We can't use fgetcsv() directly due to https://bugs.php.net/bug.php?id=46569.
            $line_data = str_getcsv( $line, $this->settings['csv-file-separator'] );

            $file->next();
            $n++;
            $this->current_line = $file->key();
            $this->processed_lines++;

            if ( count( $line_data ) < 2 && empty( $line_data[0] ) ) {
                continue;
            }

            list( $listing_data, $errors ) = $this->sanitize_and_validate_row( $line_data );

            if ( $errors ) {
                foreach ( $errors as $e ) {
                    $this->errors[] = array(
						'line'    => $this->current_line,
						'content' => $line,
						'error'   => $e,
					);
                }

                $this->rejected++;
                continue;
            }

            $result = $this->import_row( $listing_data );
            @set_time_limit( 0 );

            if ( is_wp_error( $result ) ) {
                foreach ( $result->get_error_messages() as $e ) {
                    $this->errors[] = array(
						'line'    => $this->current_line,
						'content' => $line,
						'error'   => $e,
					);
                }

                $this->rejected++;
                continue;
            }

            $this->imported++;
        }

        $file = null;
        $this->state_persist();
    }

    private function get_csv_file() {
        $file = new SplFileObject( $this->csv_file );

        return $file;
    }
    private function get_current_line( $file ) {
        $line = $file->current();

        if ( empty( $line ) ) {
            return '';
        }

        $converted_line = $this->maybe_convert_encoding( $line );

        // Some code to circumvent limitations in str_getcsv() while PHP #46569 is fixed.
        return str_replace( '\n', "\n", $converted_line );
    }

    private function maybe_convert_encoding( $line ) {
        // Some UTF16-LE string may end with a '\n' character, encoded
        // as \xOA, instead of \x0A\x00 (the last byte is missing)
        // making it impossible for iconv to convert the encoding of the
        // string
        $line = rtrim( $line, "\n" );
        // The last byte (\x00) ends up at the beginning of the next line,
        // so me remove that too.
        $line = ltrim( $line, "\x00" );

        if ( isset( $this->settings['encoding'] ) ) {
            $encoding = $this->settings['encoding'];
        } else {
            $encoding = wpbdp_detect_encoding( $line );
        }

        if ( 'UTF-8' != $encoding ) {
            $converted_line = iconv( $encoding, 'UTF-8', $line );
        } else {
            $converted_line = $line;
        }

        return $converted_line;
    }

    public function get_import_id() {
        return $this->state_id;
    }

    public function get_import_rows_count() {
        return max( 0, $this->total_lines );
    }

    public function get_imported_rows_count() {
        return $this->imported;
    }

    public function get_rejected_rows_count() {
        return $this->rejected;
    }

    public function get_setting( $k ) {
		return isset( $this->settings[ $k ] ) ? $this->settings[ $k ] : null;
    }

    public function get_settings() {
        return $this->settings;
    }

    public function get_errors() {
        return $this->errors;
    }

    public function get_progress( $format = 'n' ) {
        $total = $this->get_import_rows_count();
        $done  = min( $total, $this->processed_lines );

        switch ( $format ) {
            case '%': // As a percentage.
                return round( 100 * $this->get_progress( 'f' ) );

            case 'f': // As a fraction.
                return round( $done / $total, 3 );

            case 'n': // As # of items read.
                return $done;

            case 'r': // As # of items remaining.
                return max( 0, $total - $done );
        }
    }

    public function in_test_mode() {
        return (bool) $this->settings['test-import'];
    }

    public function done() {
        return $this->done;
    }

    public function cleanup() {
		WPBDP_FS::rmdir( $this->working_dir );
    }

    private function restore_state( $state_id ) {
        $csv_imports_dir = $this->directory_path() . DIRECTORY_SEPARATOR . $state_id;

        // TODO: validate $state_id is really an uniqid() string and does not contain other chars (maybe someone is
        // trying to access parts that it shouldn't in the FS).
        if ( ! is_dir( $csv_imports_dir ) ) {
            throw new Exception( 'Invalid state ID' );
        }

        $this->working_dir = $csv_imports_dir;
        $this->state_id    = basename( $this->working_dir );
        $this->csv_file    = $this->working_dir . DIRECTORY_SEPARATOR . 'data.csv';
        $this->images_dir  = is_dir( $this->working_dir . DIRECTORY_SEPARATOR . 'images' ) ? $this->working_dir . DIRECTORY_SEPARATOR . 'images' : '';

        $state_file       = $this->working_dir . DIRECTORY_SEPARATOR . 'import.state';
        $this->state_file = $state_file;

        $this->state_load();
    }

    private function setup_working_dir( $csv_file, $images_file = '' ) {

        $csv_imports_dir = $this->directory_path();
		require_once dirname( WPBDP_PLUGIN_FILE ) . '/includes/helpers/class-create-file.php';

		$this->create_folders();

        if ( ! $this->working_dir ) {
            throw new Exception( 'Could not set working dir' );
        }

        if ( ! copy( $csv_file, $this->working_dir . DIRECTORY_SEPARATOR . 'data.csv' ) ) {
            throw new Exception( 'Could not copy CSV file to working directory' );
        }

        if ( $images_file && file_exists( $images_file ) ) {
            $dest = $this->working_dir . DIRECTORY_SEPARATOR . 'images.zip';
            if ( ! copy( $images_file, $dest ) ) { // XXX: maybe move?
                throw new Exception( 'Could not copy images ZIP file to working directory' );
            }

			$image_folder = $this->working_dir . DIRECTORY_SEPARATOR . 'images';

            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
            $zip = new PclZip( $dest );
			$files = $zip->extract( PCLZIP_OPT_PATH, $image_folder, PCLZIP_OPT_REMOVE_ALL_PATH );
			if ( $files ) {
				$this->images_dir = $image_folder;

                @unlink( $dest );
				$this->delete_non_images( $files );
            } else {
                throw new Exception( 'Images ZIP file could not be uncompressed' );
            }
        }

        $this->state_id   = basename( $this->working_dir );
        $this->csv_file   = $this->working_dir . DIRECTORY_SEPARATOR . 'data.csv';
        $this->state_file = $this->working_dir . DIRECTORY_SEPARATOR . 'import.state';

        $this->state_persist();
    }

	/**
	 * Create folders and index.php
	 *
	 * @since 5.11.2
	 */
	private function create_folders() {
		require_once dirname( WPBDP_PLUGIN_FILE ) . '/includes/helpers/class-create-file.php';

		$id           = uniqid();
		$import_dir   = rtrim( $this->directory_path(), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $id;
		$create_index = new WPBDP_Create_File(
			array(
				'file_name'   => 'index.php',
				'folder_name' => 'wpbdp-csv-imports/' . $id
			)
		);
		$create_index->create_index( 'force' );

		if ( is_dir( $import_dir ) ) {
			$this->working_dir = $import_dir;
		}
	}

	/**
	 * Check the file types after the zip is unzipped.
	 *
	 * @since 5.11
	 */
	private function delete_non_images( $files ) {
		$allowed = array( 'pdf', 'png', 'jpg', 'jpeg', 'gif', 'txt', 'rtf' );
		foreach ( $files as $file ) {
			$uploaded_type = strtolower( pathinfo( $file['filename'], PATHINFO_EXTENSION ) );
			if ( ! in_array( $uploaded_type, $allowed, true ) ) {
				 @unlink( $file['filename'] );
			}
		}
	}

	/**
	 * @since 5.11
	 */
	private function directory_path() {
		$upload_dir = wp_upload_dir();

		if ( $upload_dir['error'] ) {
			throw new Exception();
		}

		return rtrim( $upload_dir['basedir'], DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'wpbdp-csv-imports';
	}

    private function read_header() {
        $file = new SplFileObject( $this->csv_file );

        $this->detect_encoding_from_header( $file );
        $this->parse_header( $file );

        $file = null;

        $this->state_persist();
    }

    private function detect_encoding_from_header( $file ) {
        $line = $file->current();

        if ( substr( $line, 0, 3 ) == self::UTF8_BOM ) {
            $this->settings['encoding'] = 'UTF-8';
        }

        if ( substr( $line, 0, 2 ) == self::UTF16_LE_BOM ) {
            $this->settings['encoding'] = 'UTF-16LE';
        }
    }

    private function parse_header( $file ) {
        $header_line = $this->remove_bom( $file->current() );
        $header_line = $this->maybe_convert_encoding( $header_line );

        $this->set_header( str_getcsv( $header_line, $this->settings['csv-file-separator'] ) );

        $file->next();
        $this->current_line = $file->key();
    }

    private function remove_bom( $str ) {
        if ( substr( $str, 0, 3 ) == self::UTF8_BOM ) {
            $str = substr( $str, 3 );
        }

        if ( substr( $str, 0, 2 ) == self::UTF16_LE_BOM ) {
            $str = substr( $str, 2 );
        }

        return $str;
    }

    private function set_header( $header ) {
        if ( ! $header || ( count( $header ) == 1 && is_null( $header[0] ) ) ) {
            throw new Exception( 'Invalid header' );
        }

        $required_fields  = wpbdp_get_form_fields( 'validators=required' );
        $fields_in_header = array_map( 'trim', $header );

        foreach ( $required_fields as $rf ) {
            if ( ! in_array( $rf->get_short_name(), $fields_in_header, true ) ) {
                throw new Exception( sprintf( 'Required header column "%s" missing', $rf->get_short_name() ) );
            }
        }

        $this->header = array();

        global $wpbdp;
        $short_names = $wpbdp->formfields->get_short_names();
        foreach ( $fields_in_header as $short_name ) {
            $field_id = 0;

            $key = array_search( $short_name, $short_names, true );

            if ( false === $key ) {
                $field_id = 0;
            }

            if ( $f = wpbdp_get_form_field( $key ) ) {
                $field_id = $f->get_id();
            }

            $this->header[] = array(
				'short_name' => $short_name,
				'field_id'   => $field_id,
			);
        }
    }

    private function state_load() {
        if ( ! file_exists( $this->state_file ) ) {
            return;
        }

        if ( ! is_readable( $this->state_file ) ) {
            throw new Exception( 'XXX' );
        }

        $state = unserialize( file_get_contents( $this->state_file ) );

        foreach ( self::$PERSISTENT as $key ) {
            $this->{$key} = $state[ $key ];
        }
    }

    private function state_persist() {
        $state                 = array();
        $state['settings']     = $this->settings;
        $state['header']       = $this->header;
        $state['current_line'] = $this->current_line;
        $state['imported']     = $this->imported;
        $state['errors']       = $this->errors;
        $state['done']         = $this->done;

        foreach ( self::$PERSISTENT as $key ) {
            $state[ $key ] = $this->{$key};
        }

        if ( false === file_put_contents( $this->state_file, serialize( $state ) ) ) {
            throw new Exception( 'Could not write persistent data' );
        }
    }

    private function import_row( $data ) {
        global $wpdb;
        global $wpbdp;

        if ( $this->settings['test-import'] ) {
            return;
        }

        extract( $data );

        $state  = (object) array(
			'fields'     => array(),
			'images'     => array(),
			'categories' => array(),
		);
        $errors = array();

        // Create categories.
        foreach ( $categories as &$c ) {
            if ( $c['term_id'] ) {
                $state->categories[] = intval( $c['term_id'] );
                continue;
            }

			$term_name = str_replace( '&', '&amp;', $c['name'] );
			$t         = term_exists( $term_name, WPBDP_CATEGORY_TAX );
			if ( $t ) {
                $c['term_id'] = $t['term_id'];
            } else {
				$t = wp_insert_term( $term_name, WPBDP_CATEGORY_TAX );

				if ( is_array( $t ) ) {
                    $c['term_id'] = $t['term_id'];
                } elseif ( is_wp_error( $t ) ) {
                    $message = _x( 'Could not create listing category "<category-name>". The operation failed with the following error: <error-message>.', 'admin csv-import', 'business-directory-plugin' );
                    $message = str_replace( '<category-name>', $c['name'], $message );
                    $message = str_replace( '<error-message>', $t->get_error_message(), $message );

                    $errors[] = $message;
                } else {
                    $errors[] = sprintf( _x( 'Could not create listing category "%s"', 'admin csv-import', 'business-directory-plugin' ), $c['name'] );
                }
            }

            if ( $c['term_id'] ) {
                $state->categories[] = intval( $c['term_id'] );
            }
        }

        $listing_id = 0;

        // Support sequence_id.
        if ( $meta['sequence_id'] ) {
            $listing_id = intval(
                $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
                        '_wpbdp[import_sequence_id]', $meta['sequence_id']
                    )
                )
            );
            if ( WPBDP_POST_TYPE != get_post_type( $listing_id ) ) {
                $listing_id = 0;
            }
        }

        // Handle fields.
        foreach ( $fields as $field_id => $field_data ) {
            $f = wpbdp_get_form_field( $field_id );

            if ( 'image' != $f->get_field_type_id() ) {
                continue;
            }

            $img = array_pop( $field_data );

            if ( ! $img ) {
                $field_data[] = $img;
                continue;
            }

            $media_id = $this->upload_image( $img );

            if ( $media_id ) {
                $field_data[0] = $media_id;
            }

            $fields[ $field_id ] = $media_id ? $field_data : array();
        }

        $state->fields = $fields;

        // Handle images.
        foreach ( $data['images'] as $filename ) {
			$img_id = $this->upload_image( $filename );
			if ( $img_id ) {
                $state->images[] = $img_id;
            }
        }

        // Insert or update listing.
        $listing_data                  = (array) $state;
        $listing_data['listing_id']    = $listing_id;
        $listing_data['append_images'] = $this->settings['append-images'];
        $listing_data['post_status']   = $this->settings['post-status'];

        if ( $listing_id ) {
            $listing_data['post_status'] = $this->settings['existing-post-status'];

            if ( 'preserve_status' === $this->settings['existing-post-status'] ) {
                $listing_data['post_status'] = get_post_status( $listing_id );
            }
        }

        if ( ! empty( $data['plan_id'] ) ) {
            $listing_data['plan_id'] = $data['plan_id'];
        }

        if ( $data['expires_on'] ) {
            $listing_data['expires_on'] = $data['expires_on'];
        }

        if ( $meta['sequence_id'] ) {
            $listing_data['sequence_id'] = $meta['sequence_id'];
        }

        if ( $u = get_user_by( 'login', $meta['username'] ) ) {
            $listing_data['user_id'] = $u->ID;
        }

        $listing = wpbdp_save_listing( $listing_data, true, 'csv-import' );

        if ( is_wp_error( $listing ) ) {
            $errors = array_merge( $errors, $listing->get_error_messages() );
        }

        if ( $errors ) {
            $error = new WP_Error();

            foreach ( $errors as $e ) {
                $error->add( 'listing-add-error', $e );
            }

            return $error;
        }

        if ( ! empty( $data['terms_and_conditions_acceptance_date'] ) ) {
            update_post_meta( $listing->get_id(), '_wpbdp_tos_acceptance_date', $data['terms_and_conditions_acceptance_date'] );
            if ( empty( $meta['sequence_id'] ) ) {
                wpbdp_insert_log(
                    array(
                        'log_type'   => 'listing.terms_and_conditions_accepted',
                        'object_id'  => $listing->get_id(),
                        'created_at' => $data['terms_and_conditions_acceptance_date']
                    )
                );
            }
        }

        if ( $state->images ) {
            $listing->set_thumbnail_id( $state->images[0] );
        }

        $payment = $listing->get_latest_payment();

        // A payment record created in the last minute means the plan of an existing
        // listing changed or was just assigned for a new listing.
        if ( $payment && current_time( 'timestamp' ) - strtotime( $payment->created_at ) < 60 ) {
            $payment->status  = 'completed';
            $payment->context = 'csv-import';
            $payment->save();

            wpbdp_insert_log(
                array(
					'log_type'  => 'payment.note',
					'object_id' => $payment->id,
					'actor'     => is_admin() ? 'user:' . get_current_user_id() : 'system',
					'message'   => __( 'Listing imported by admin. Payment skipped.', 'business-directory-plugin' ),
                )
            );
        }

        return $listing->get_id();
    }

    private function sanitize_and_validate_row( $data ) {
        global $wpbdp;

        $errors = array();

        $categories = array();
        $fields     = array();
        $images     = array();
        $expires_on = '';
        $plan_id    = 0;

        $terms_and_conditions_acceptance_date = '';

        $meta                = array();
        $meta['sequence_id'] = 0;
        $meta['username']    = '';

        if ( $this->settings['assign-listings-to-user'] && $this->settings['default-user'] ) {
			$u = get_user_by( 'id', $this->settings['default-user'] );
			if ( $u ) {
                $meta['username'] = $u->user_login;
            }
        }

        foreach ( $this->get_header() as $i => $col_info ) {
            $column = $col_info['short_name'];
            $field  = $col_info['field_id'] ? wpbdp_get_form_field( $col_info['field_id'] ) : null;
            $value  = stripslashes( trim( isset( $data[ $i ] ) ? $data[ $i ] : '' ) );

            switch ( $column ) {
                case 'image':
                case 'images':
                    $file_names = explode( $this->settings['images-separator'], $value );

                    foreach ( $file_names as $f ) {
                        $f = trim( $f );

                        if ( $f ) {
                            $images[] = $f;
                        }
                    }

                    break;

                case 'username':
                    if ( $this->settings['assign-listings-to-user'] && $value ) {
                        if ( ! username_exists( $value ) ) {
                            $errors[] = sprintf( _x( 'Username "%s" does not exist', 'admin csv-import', 'business-directory-plugin' ), $value );
                        } else {
                            $meta['username'] = $value;
                        }
                    }

                    break;

                case 'expires_on':
                    $expires = $this->convert_to_date( $value, $errors );

                    if ( $expires ) {
                        $expires_on = $expires;
                    }

                    break;

                case 'fee_id':
                    $submitted_fee_id = absint( $value );

                    if ( ! $submitted_fee_id ) {
                        break;
                    }

                    $plan = wpbdp_get_fee_plan( $submitted_fee_id );

                    if ( ! $plan ) {
                        /* translators: %d: the plan id */
                        $message = sprintf(
                            __( 'There is no Plan with ID = %d', 'business-directory-plugin' ),
                            $submitted_fee_id
                        );

                        $errors[] = $message;

                        break;
                    }

                    $plan_id = $plan->id;

                    break;

                case 'terms_and_conditions_acceptance_date':
                    $tos_date = $this->convert_to_date( $value, $errors );

                    if ( $tos_date ) {
                        $terms_and_conditions_acceptance_date = $tos_date;
                    }
                    break;

                case 'sequence_id':
                    $meta['sequence_id'] = absint( $value );

                    break;

                default:
                    if ( ! $field ) {
                        break;
                    }

                    if ( $field->is_required() && $field->is_empty_value( $value ) ) {
                        $errors[] = sprintf( _x( 'Missing required field: %s', 'admin csv-import', 'business-directory-plugin' ), $column );
                        break;
                    }

                    if ( 'category' == $field->get_association() ) {
                        $this->prepare_categories( $value, $categories, $errors );

                    } else {
                        $fields[ $field->get_id() ] = $field->convert_csv_input( $value, $this->settings );
                    }

                    break;
            }
        }

        return array( compact( 'categories', 'fields', 'images', 'meta', 'expires_on', 'plan_id', 'terms_and_conditions_acceptance_date' ), $errors );
    }

	/**
	 * @since 5.11
	 */
	private function prepare_categories( $value, &$categories, &$errors ) {
		$csv_categories = $this->split_categories( $value );

		foreach ( $csv_categories as $csv_category_ ) {
			$csv_category = $this->prepare_category_name( $csv_category_ );
			if ( ! $csv_category ) {
				continue;
			}

			$term = term_exists( $csv_category, WPBDP_CATEGORY_TAX );

			if ( $term ) {
				$categories[] = array(
					'name'    => $csv_category,
					'term_id' => is_array( $term ) ? $term['term_id'] : $term,
				);
				continue;
			}

			if ( ! $this->settings['create-missing-categories'] ) {
				$errors[] = sprintf( _x( 'Listing category "%s" does not exist', 'admin csv-import', 'business-directory-plugin' ), $csv_category );
				continue;
			}

			if ( ! $this->settings['test-import'] ) {
				$categories[] = array(
					'name'    => $csv_category,
					'term_id' => 0,
				);
			}
		}
	}

	/**
	 * Get rid of entities so ; can be used to separate.
	 *
	 * @since 5.11
	 */
	private function split_categories( $value ) {
		$decoded_value = html_entity_decode( $value );
		return array_map( 'trim', explode( $this->settings['category-separator'], $decoded_value ) );
	}

	/**
	 * Get some entities back after removing them.
	 *
	 * @since 5.11
	 */
	private function prepare_category_name( $csv_category ) {
		$csv_category = str_replace( "\n", '-', $csv_category );
		$csv_category = strip_tags( $csv_category );
		$csv_category = str_replace( array( '"', "'" ), '', $csv_category );
		return str_replace( '& ', '&amp; ', $csv_category );
	}

    private function get_header() {
        return $this->header;
    }

    private function upload_image( $filename ) {
        $filepath = $this->images_dir . DIRECTORY_SEPARATOR . $filename;
        if ( ! $this->images_dir || ! file_exists( $filepath ) ) {
            return false;
        }

        // Make a copy of the file because wpbdp_media_upload() moves the original file.
        copy( $filepath, $filepath . '.backup' );
		$upload = wpbdp_media_upload( $filepath, false, true );
		if ( ! $upload ) {
			return false;
		}
		$media_id = WPBDP_Utils::attach_image_to_media_library( $upload );
        rename( $filepath . '.backup', $filepath );
        return $media_id;
    }

    private function convert_to_date( $value, &$errors ) {
        $trimmed_value = trim( $value, "/ \t\n\r\0\x0B" );

        if ( empty( $trimmed_value ) ) {
            return false;
        }

        if ( preg_match( '#^(\d{1,4}/\d{1,2}/\d{1,4})(\s([0-1]?[0-9]|[2][0-3]):([0-5][0-9])(:[0-5][0-9])?)?$#', $trimmed_value ) ) {
            $date = strtotime( $trimmed_value );
        } else {
            $dates = explode( '/', $trimmed_value );
            $dates = array_map( 'strtotime', $dates );
            $dates = array_filter( $dates );

            $date = array_shift( $dates );
        }

        if ( ! $date ) {
            $message = _x( "The string <string> couldn't be converted into a valid date.", 'admin csv-import', 'business-directory-plugin' );
            $message = str_replace( '<string>', '"' . $value . '"', $message );

            $errors[] = $message;
            return false;
        }

        return date( 'Y-m-d H:i:s', $date );
    }
}
