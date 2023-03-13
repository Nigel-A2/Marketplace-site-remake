<?php
/**
 * CSV Import admin pages.
 *
 * @package BDP/Includes/Admin
 */

require_once WPBDP_INC . 'admin/helpers/csv/class-csv-import.php';
/**
 * CSV Import admin pages.
 *
 * @since 2.1
 */
class WPBDP_CSVImportAdmin {

	private $files = array(
		'images' => '',
		'csv'    => '',
	);

    function __construct() {
        global $wpbdp;

        add_action( 'wpbdp_enqueue_admin_scripts', array( &$this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_wpbdp-csv-import', array( &$this, 'ajax_csv_import' ) );
        add_action( 'wp_ajax_wpbdp-autocomplete-user', array( &$this, 'ajax_autocomplete_user' ) );
		add_action( 'wp_ajax_wpbdp-example-csv', array( &$this, 'download_example_csv' ) );
    }

    function enqueue_scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        wp_enqueue_script(
            'wpbdp-admin-import-js',
			WPBDP_ASSETS_URL . 'js/admin-csv-import' . $min . '.js',
            array( 'wpbdp-admin-js', 'jquery-ui-autocomplete' ),
            WPBDP_VERSION,
			true
        );

        wp_enqueue_style(
            'wpbdp-admin-import-css',
            WPBDP_ASSETS_URL . 'css/admin-csv-import.min.css',
            array(),
            WPBDP_VERSION
        );
    }

    function ajax_csv_import() {
        global $wpbdp;

        if ( ! current_user_can( 'administrator' ) ) {
            die();
        }

        $import_id = wpbdp_get_var( array( 'param' => 'import_id', 'default' => 0 ), 'post' );

        if ( ! $import_id ) {
            die();
        }

        $res = new WPBDP_AJAX_Response();

        try {
            $import = new WPBDP_CSV_Import( $import_id );
        } catch ( Exception $e ) {
            $res->send_error( $e->getMessage() );
        }

        if ( ! empty( $_POST['cleanup'] ) ) {
            $import->cleanup();
            $res->send();
        }

        $wpbdp->_importing_csv          = true;
        $wpbdp->_importing_csv_no_email = (bool) $import->get_setting( 'disable-email-notifications' );

        wp_defer_term_counting( true );
        $import->do_work();
        wp_defer_term_counting( false );

        unset( $wpbdp->_importing_csv );
		unset( $wpbdp->_importing_csv_no_email );

        $res->add( 'done', $import->done() );
        $res->add( 'progress', $import->get_progress( 'n' ) );
        $res->add( 'total', $import->get_import_rows_count() );
        $res->add( 'imported', $import->get_imported_rows_count() );
        $res->add( 'rejected', $import->get_rejected_rows_count() );

        if ( $import->done() ) {
            $res->add( 'warnings', $import->get_errors() );
            $import->cleanup();
        }

        $res->send();
    }

    public function ajax_autocomplete_user() {
		$term  = wpbdp_get_var( array( 'param' => 'term' ), 'request' );
        $users = get_users( array( 'search' => "*{$term}*" ) );

        foreach ( $users as $user ) {
            $return[] = array(
                'label' => "{$user->display_name} ({$user->user_login})",
                'value' => $user->ID,
            );
        }

        wp_die( wp_json_encode( $return ) );
    }

    function dispatch() {
        $action = wpbdp_get_var( array( 'param' => 'action' ), 'request' );

        switch ( $action ) {
            case 'example-csv':
                $this->example_csv();
                break;
            case 'do-import':
                $this->import();
                break;
            default:
                $this->import_settings();
                break;
        }
    }

    private function example_data_for_field( $field = null, $shortname = null ) {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ( $field ) {
			$assoc = $field->get_association();
			if ( $assoc === 'title' ) {
				/* translators: %s: Sample business name */
                return sprintf( esc_html__( 'Business %s', 'business-directory-plugin' ), $letters[ rand( 0, strlen( $letters ) - 1 ) ] );
            }

			if ( $assoc === 'category' || $assoc === 'tags' ) {
				$term_args = array(
					'taxonomy'   => $assoc === 'category' ? WPBDP_CATEGORY_TAX : WPBDP_TAGS_TAX,
					'hide_empty' => 0,
					'number'     => 5,
				);

				$terms = get_terms( $term_args );
				if ( $terms ) {
					return $terms[ array_rand( $terms ) ]->name;
				}
				return '';
			}

			if ( $field->has_validator( 'url' ) ) {
                return get_site_url();
            } elseif ( $field->has_validator( 'email' ) ) {
                return get_option( 'admin_email' );
            } elseif ( $field->has_validator( 'integer_number' ) ) {
                return rand( 0, 100 );
            } elseif ( $field->has_validator( 'decimal_number' ) ) {
                return rand( 0, 100 ) / 100.0;
            } elseif ( $field->has_validator( 'date_' ) ) {
                return date( 'd/m/Y' );
            } elseif ( $field->get_field_type()->get_id() == 'multiselect' || $field->get_field_type()->get_id() == 'checkbox' ) {
                if ( $field->data( 'options' ) ) {
                    $options = $field->data( 'options' );
                    return $options[ array_rand( $options ) ];
                }

                return '';
            }
        }

        if ( $shortname == 'user' ) {
            $users = get_users();
            return $users[ array_rand( $users ) ]->user_login;
        }

        return _x( 'Whatever', 'admin csv-import', 'business-directory-plugin' );
    }

    private function example_csv() {
		echo '<p class="alignright"><a class="wpbdp-button-secondary wpbdp-example-csv">' .
			esc_html__( 'Download Example', 'business-directory-plugin' ) .
			'</a></p>';
		echo '<h3 style="margin-top:1em">' . __( 'Example CSV Import File', 'business-directory-plugin' ) . '</h3>';

        echo '<textarea class="wpbdp-csv-import-example" rows="20">';
        echo $this->example_csv_content();
        echo '</textarea>';

        echo wpbdp_admin_footer();
    }

	/**
	 * Generate a sample CS file for download.
	 *
	 * @since 5.3
	 * @return void
	 */
	public function download_example_csv() {
		check_ajax_referer( 'wpbdp_ajax', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error();
		}

		$filename = 'bd-example.csv';
		$filepath = get_temp_dir() . $filename;
		$charset  = get_option( 'blog_charset' );

		header( 'Content-Type: text/csv; charset=' . $charset );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$f = fopen( 'php://output', 'w' );

		fwrite( $f, $this->example_csv_content() );

		exit;
	}

	/**
	 * @since 5.3
	 * @return string
	 */
	private function example_csv_content() {
		$content = '';
		$fields  = wpbdp_get_form_fields( array( 'field_type' => '-ratings' ) );

		foreach ( $fields as $f ) {
			$content .= $f->get_short_name() . ',';
		}
		$content .= 'username,fee_id';
		$content .= "\n";

		$posts = get_posts(
			array(
				'post_type'        => WPBDP_POST_TYPE,
				'post_status'      => 'publish',
				'numberposts'      => 3,
				'suppress_filters' => false,
			)
		);

		if ( count( $posts ) >= 1 ) {
			foreach ( $posts as $post ) {
				foreach ( $fields as $f ) {
					$value = $f->csv_value( $post->ID );
					$content .= str_replace( array( ',', '"' ), array( ';', '""' ), $value );
					$content .= ',';
				}
				$content .= get_the_author_meta( 'user_login', (int) $post->post_author );
				$fee = wpbdp_get_listing( $post->ID )->get_fee_plan();
				$content .= ',';
				$content .= $fee ? $fee->fee_id : '';

				$content .= "\n";
			}
		} else {
			for ( $i = 0; $i < 5; $i++ ) {
				foreach ( $fields as $f ) {
					$content .= sprintf( '"%s"', $this->example_data_for_field( $f, $f->get_short_name() ) );
					$content .= ',';
				}

				$content .= sprintf( '"%s"', $this->example_data_for_field( null, 'user' ) );
				$content .= "\n";
			}
		}
		return $content;
	}

    private function get_imports_dir() {
        $upload_dir = wp_upload_dir();

        if ( $upload_dir['error'] ) {
            return false;
        }

        $imports_dir = rtrim( $upload_dir['basedir'], DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'wpbdp-csv-imports';
        return $imports_dir;
    }

    private function find_uploaded_files() {
        $base_dir = $this->get_imports_dir();

        $res = array(
			'images' => array(),
			'csv'    => array(),
		);

        if ( is_dir( $base_dir ) ) {
            $files = wpbdp_scandir( $base_dir );

            foreach ( $files as $f_ ) {
                $f = $base_dir . DIRECTORY_SEPARATOR . $f_;

                if ( ! is_file( $f ) || ! is_readable( $f ) ) {
                    continue;
                }

                switch ( strtolower( substr( $f, -4 ) ) ) {
                    case '.csv':
                        $res['csv'][] = $f;
                        break;
                    case '.zip':
                        $res['images'][] = $f;
                        break;
                    default:
                        break;
                }
            }
        }

        return $res;
    }

    private function import_settings() {
        $import_dir = $this->get_imports_dir();

        if ( $import_dir && ! is_dir( $import_dir ) ) {
            @mkdir( $import_dir, 0777 );
        }

        $files = array();

        if ( ! $import_dir || ! is_dir( $import_dir ) || ! is_writable( $import_dir ) ) {
            wpbdp_admin_message(
                sprintf(
                    _x(
                        'A valid temporary directory with write permissions is required for CSV imports to function properly. Your server is using "%s" but this path does not seem to be writable. Please consult with your host.',
                        'csv import',
                        'business-directory-plugin'
                    ),
                    $import_dir
                )
            );
        }

        $files = $this->find_uploaded_files();

        // Retrieve last used settings to use as defaults.
        $defaults = get_user_option( 'wpbdp-csv-import-settings' );
        if ( ! $defaults || ! is_array( $defaults ) ) {
            $defaults = array();
        }

        echo wpbdp_render_page(
            WPBDP_PATH . 'templates/admin/csv-import.tpl.php',
            array(
				'files'    => $files,
				'defaults' => $defaults,
            )
        );
    }

    private function import() {
		$nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'do-import' ) ) {
			wp_die( esc_html__( 'You are not allowed to do that.', 'business-directory-plugin' ) );
		}

		$sources = array();
		$error   = '';

        // CSV file.
		$error = $this->add_file_to_sources( 'csv', $sources );
		if ( $error ) {
			$this->show_error( $error );
			return;
		}

		if ( ! $this->files['csv'] ) {
			$this->show_error( _x( 'Please upload or select a CSV file.', 'admin csv-import', 'business-directory-plugin' ) );
			return;
		}

        // Images file.
		$error = $this->add_file_to_sources( 'images', $sources );

		if ( $error ) {
			$this->show_error( $error );
			return;
		}

        // Store settings to use as defaults next time.
		$settings = wpbdp_get_var( array( 'param' => 'settings' ), 'post' );
		update_user_option( get_current_user_id(), 'wpbdp-csv-import-settings', $settings, false );

        $import = null;
        try {
            $import = new WPBDP_CSV_Import(
                '',
				$this->files['csv'],
				$this->files['images'],
				array_merge( $settings, array( 'test-import' => ! empty( $_POST['test-import'] ) ) )
            );
        } catch ( Exception $e ) {
            $error  = _x( 'An error was detected while validating the CSV file for import. Please fix this before proceeding.', 'admin csv-import', 'business-directory-plugin' );
            $error .= '<br />';
			$error .= '<b>' . esc_html( $e->getMessage() ) . '</b>';

			$this->show_error( $error );
			return;
        }

        if ( $import->in_test_mode() ) {
            wpbdp_admin_message( _x( 'Import is in "test mode". Nothing will be inserted into the database.', 'admin csv-import', 'business-directory-plugin' ) );
        }

        echo wpbdp_render_page(
            WPBDP_PATH . 'templates/admin/csv-import-progress.tpl.php',
            array(
				'import'  => $import,
				'sources' => $sources,
            )
        );
    }

	/**
	 * @since 5.11
	 */
	private function show_error( $error ) {
		wpbdp_admin_message( $error, 'error' );
		$this->import_settings();
	}

	/**
	 * @param string $type   'csv' or 'image'
	 * @param array $sources
	 *
	 * @since 5.11
	 */
	private function add_file_to_sources( $type, &$sources ) {
		$file = wpbdp_get_var( array( 'param' => $type . '-file-local' ), 'post' );

		if ( $file && $this->is_correct_type( $type, $file ) ) {
			$this->files[ $type ] = $this->get_imports_dir() . DIRECTORY_SEPARATOR . basename( $file );

			$sources[] = basename( $this->files[ $type ] );
			return;
		}

		$file = $this->get_file_name( $type . '-file', 'tmp' );
		if ( empty( $_FILES[ $type . '-file' ] ) || empty( $file ) ) {
			return;
		}

		$this->files[ $type ] = $file;

		$no_file  = UPLOAD_ERR_NO_FILE == $_FILES[ $type . '-file' ]['error'] || ! is_uploaded_file( $this->files[ $type ] );
		if ( $no_file ) {
			return __( 'There was an error uploading the file:', 'business-directory-plugin' ) . ' ' . $type;
		}

		$filename = $this->get_file_name( $type . '-file' );
		if ( ! $this->is_correct_type( $type, $filename ) ) {
			return __( 'Please upload the correct file type.', 'business-directory-plugin' );
		}

		$sources[] = $filename;
	}

	/**
	 * @since 5.11
	 */
	private function is_correct_type( $type, $filename ) {
		$allowed_type = array(
			'images' => 'zip',
			'csv'    => 'csv',
		);

		$uploaded_type = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		return $uploaded_type === $allowed_type[ $type ];
	}

	/**
	 * Unslashing causes issues in Windows.
	 *
	 * @since 5.11
	 */
	private function get_file_name( $name, $temp = false ) {
		$value = $temp ? 'tmp_name' : 'name';

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		return isset( $_FILES[ $name ][ $value ] ) ? sanitize_option( 'upload_path', $_FILES[ $name ][ $value ] ) : '';
	}
}
