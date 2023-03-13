<?php
/**
 * Form fields API.
 *
 * @package BDP/Form Fields API
 */
if ( ! class_exists( 'WPBDP_FormFields' ) ) {

    require_once WPBDP_PATH . 'includes/fields/class-form-field.php';
    require_once WPBDP_PATH . 'includes/fields/form-fields-types.php';

    class WPBDP_FormFields {

        private $associations            = array();
        private $association_flags       = array();
        private $association_field_types = array();

        private $field_types = array();

        private static $instance = null;

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        private function __construct() {
            // register core associations
            $this->register_association( 'title', __( 'Post Title', 'business-directory-plugin' ), array( 'required', 'unique' ) );
            $this->register_association( 'content', __( 'Post Content', 'business-directory-plugin' ), array( 'required', 'unique', 'optional' ) );
            $this->register_association( 'excerpt', __( 'Post Excerpt', 'business-directory-plugin' ), array( 'unique' ) );
            $this->register_association( 'category', __( 'Post Category', 'business-directory-plugin' ), array( 'required', 'unique' ) );
            $this->register_association( 'tags', __( 'Post Tags', 'business-directory-plugin' ), array( 'unique' ) );
            $this->register_association( 'meta', __( 'Post Metadata', 'business-directory-plugin' ) );

            $this->register_association( 'custom', __( 'Custom', 'business-directory-plugin' ), array( 'private' ) );

            // register core field types
            $this->register_field_type( 'WPBDP_FieldTypes_TextField', 'textfield' );
            $this->register_field_type( 'WPBDP_FieldTypes_Select', 'select' );
            $this->register_field_type( 'WPBDP_FieldTypes_URL', 'url' );
            $this->register_field_type( 'WPBDP_FieldTypes_TextArea', 'textarea' );
            $this->register_field_type( 'WPBDP_FieldTypes_RadioButton', 'radio' );
            $this->register_field_type( 'WPBDP_FieldTypes_MultiSelect', 'multiselect' );
            $this->register_field_type( 'WPBDP_FieldTypes_Checkbox', 'checkbox' );
            $this->register_field_type( 'WPBDP_FieldTypes_Twitter', 'social-twitter' );
            $this->register_field_type( 'WPBDP_FieldTypes_Facebook', 'social-facebook' );
            $this->register_field_type( 'WPBDP_FieldTypes_LinkedIn', 'social-linkedin' );
            $this->register_field_type( 'WPBDP_FieldTypes_Social', 'social-network' );
            $this->register_field_type( 'WPBDP_FieldTypes_Image', 'image' );
            $this->register_field_type( 'WPBDP_FieldTypes_Date', 'date' );
            $this->register_field_type( 'WPBDP_FieldTypes_Phone_Number' );
        }

        /**
         * Registers a new association within the form fields API.
         *
         * @param string $association association id
         * @param string $name human-readable name
         * @param array  $flags association flags
         */
        public function register_association( $association, $name = '', $flags = array() ) {
            if ( isset( $this->associations[ $association ] ) ) {
                return false;
            }

            $this->associations[ $association ]      = $name ? $name : $association;
			$this->association_flags[ $association ] = $flags;

            if ( ! isset( $this->association_field_types[ $association ] ) ) {
                $this->association_field_types[ $association ] = array();
            }
        }

        /**
         * Returns the known form field associations.
         *
         * @return array associative array with key/name pairs
         */
        public function &get_associations() {
            return $this->associations;
        }

        public function get_association_field_types( $association = null ) {
            if ( $association ) {
                if ( in_array( $association, array_keys( $this->associations ), true ) ) {
                    return $this->association_field_types[ $association ];
                } else {
                    return null;
                }
            }

            return $this->association_field_types;
        }

        public function get_association_flags( $association ) {
            if ( array_key_exists( $association, $this->associations ) ) {
                return $this->association_flags[ $association ];
            }

            return array();
        }

        /**
         * Returns associations marked with the given flags.
         *
         * @param string|array $flags flags to be checked
         * @param boolean      $any if True associations marked with any (and not all) of the flags will also be returned
         * @return array
         */
        public function &get_associations_with_flag( $flags, $any = false ) {
            if ( is_string( $flags ) ) {
                $flags = array( $flags );
            }

            $res = array();

            foreach ( $this->association_flags as $association => $association_flags ) {
                $intersection = array_intersect( $flags, $association_flags );

                if ( ( $any && ( count( $intersection ) > 0 ) ) || ( ! $any && ( count( $intersection ) == count( $flags ) ) ) ) {
                    $res[] = $association;
                }
            }

            return $res;
        }

        /**
         * Get associations with their flags at the same time.
         *
         * @since 3.4
         */
        public function &get_associations_with_flags() {
            $res = array();

            foreach ( $this->associations as $assoc_id => $assoc_label ) {
                $flags            = $this->association_flags[ $assoc_id ];
                $res[ $assoc_id ] = (object) array(
					'id'    => $assoc_id,
					'label' => $assoc_label,
					'flags' => $flags,
				);
            }

            return $res;
        }

        public function &get_required_field_associations() {
            return $this->get_associations_with_flag( 'required' );
        }

        public function &get_field_type( $field_type ) {
            $field_type_obj = wpbdp_getv( $this->field_types, $field_type, null );
            return $field_type_obj;
        }

        public function &get_field_types() {
            return $this->field_types;
        }

        public function get_validators() {
            $validators = WPBDP_FieldValidation::instance()->get_validators();
            return $validators;
        }

        public function register_field_type( $field_type_class, $alias = null ) {
            $field_type = new $field_type_class();

            if ( ! $alias ) {
                $alias = $field_type->get_id();
            }

            if ( ! $alias ) {
                $alias = $field_type_class;
            }

            $this->field_types[ $alias ? $alias : $field_type_class ] = $field_type;

            foreach ( $field_type->get_supported_associations() as $association ) {
                $this->association_field_types[ $association ] = array_merge( isset( $this->association_field_types[ $association ] ) ? $this->association_field_types[ $association ] : array(), array( $alias ? $alias : $field_type_class ) );
            }
        }

        public function &get_field( $id = 0 ) {
            $field = WPBDP_Form_Field::get( $id );
            return $field;
        }

        public function &get_fields( $lightweight = false ) {
            global $wpdb;

            if ( $lightweight ) {
				$sql     = "SELECT * FROM {$wpdb->prefix}wpbdp_form_fields ORDER BY weight DESC";
				$results = WPBDP_Utils::check_cache(
					array(
						'cache_key' => 'get_fields_light',
						'group'     => 'wpbdp_form_fields',
						'query'     => $sql,
						'type'      => 'get_results',
					)
				);
                return $results;
            }

            $res       = array();
			$sql       = "SELECT ID FROM {$wpdb->prefix}wpbdp_form_fields ORDER BY weight DESC";
			$field_ids = WPBDP_Utils::check_cache(
				array(
					'cache_key' => 'get_field_ids',
					'group'     => 'wpbdp_form_fields',
					'query'     => $sql,
					'type'      => 'get_col',
				)
			);

            foreach ( $field_ids as $field_id ) {
				$field = WPBDP_Form_Field::get( $field_id );
                if ( $field ) {
                    $res[] = $field;
                }
            }

            return $res;
        }

        public function &find_fields( $args = array(), $one = false ) {
            global $wpdb;
            $res = array();

            $args = wp_parse_args(
				$args,
				array(
					'association'   => null,
					'field_type'    => null,
					'validators'    => null,
					'display_flags' => null,
					'output'        => 'object',
					'unique'        => false,
                )
            );

            if ( $one == true ) {
                $args['unique'] = true;
            }

            extract( $args );

            $validators    = $validators ? ( ! is_array( $validators ) ? array( $validators ) : $validators ) : array();
            $display_flags = $display_flags ? ( ! is_array( $display_flags ) ? array( $display_flags ) : $display_flags ) : array();

            $where = '';
            if ( $args['association'] ) {
                $associations_in     = array();
                $associations_not_in = array();

                $association = ! is_array( $association ) ? explode( ',', $association ) : $association;

                foreach ( $association as &$assoc ) {
                    if ( wpbdp_starts_with( $assoc, '-' ) ) {
                        $associations_not_in[] = substr( $assoc, 1 );
                    } else {
                        $associations_in[] = $assoc;
                    }
                }

                if ( $associations_in ) {
                    $format = implode( ', ', array_fill( 0, count( $associations_in ), '%s' ) );
                    $where .= $wpdb->prepare( " AND ( association IN ( $format ) ) ", $associations_in );
                }

                if ( $associations_not_in ) {
                    $format = implode( ', ', array_fill( 0, count( $associations_not_in ), '%s' ) );
                    $where .= $wpdb->prepare( " AND ( association NOT IN ( $format ) ) ", $associations_not_in );
                }

                // $where .= $wpdb->prepare( " AND ( association = %s ) ", $args['association'] );
            }

            if ( $args['field_type'] ) {
                $field_types_in     = array();
                $field_types_not_in = array();

                $field_type = ! is_array( $field_type ) ? array( $field_type ) : $field_type;

                foreach ( $field_type as $f ) {
                    if ( wpbdp_starts_with( $f, '-' ) ) {
                        $field_types_not_in[] = substr( $f, 1 );
                    } else {
                        $field_types_in[] = $f;
                    }
                }

                if ( $field_types_in ) {
                    $format = implode( ', ', array_fill( 0, count( $field_types_in ), '%s' ) );
                    $where .= $wpdb->prepare( " AND ( field_type IN ( $format ) ) ", $field_types_in );
                }

                if ( $field_types_not_in ) {
                    $format = implode( ', ', array_fill( 0, count( $field_types_not_in ), '%s' ) );
                    $where .= $wpdb->prepare( " AND ( field_type NOT IN ( $format ) ) ", $field_types_not_in );
                }
            }

            foreach ( $display_flags as $f ) {
                if ( substr( $f, 0, 1 ) == '-' ) {
					$where .= $wpdb->prepare( ' AND ( display_flags IS NULL OR display_flags NOT LIKE %s )', '%%' . $wpdb->esc_like( substr( $f, 1 ) ) . '%%' );
                } else {
					$where .= $wpdb->prepare( ' AND ( display_flags LIKE %s )', '%%' . $wpdb->esc_like( $f ) . '%%' );
                }
            }

            foreach ( $validators as $v ) {
                if ( substr( $v, 0, 1 ) == '-' ) {
					$where .= $wpdb->prepare( ' AND ( validators IS NULL OR validators NOT LIKE %s )', '%%' . $wpdb->esc_like( substr( $v, 1 ) ) . '%%' );
                } else {
					$where .= $wpdb->prepare( ' AND ( validators LIKE %s )', '%%' . $wpdb->esc_like( $v ) . '%%' );
                }
            }

            if ( $where ) {
                $sql = "SELECT id FROM {$wpdb->prefix}wpbdp_form_fields WHERE 1=1 {$where} ORDER BY weight DESC";
            } else {
				$sql = "SELECT id FROM {$wpdb->prefix}wpbdp_form_fields ORDER BY weight DESC";
            }

			$ids = WPBDP_Utils::check_cache(
				array(
					'cache_key' => json_encode( array_filter( $args ) ) . '.' . $one,
					'group'     => 'wpbdp_form_fields',
					'query'     => $sql,
					'type'      => 'get_col',
				)
			);

            if ( 'ids' == $output ) {
                return $ids;
            }

            foreach ( $ids as $id ) {
				$field = WPBDP_Form_Field::get( $id );
                if ( $field ) {
                    if ( ! in_array( $field->get_association(), array_keys( $this->associations ), true ) ) {
                        continue;
                    }

                    $res[] = $field;
                }
            }

            $res = $unique ? ( $res ? $res[0] : null ) : $res;

            return $res;
        }

        public function get_missing_required_fields() {
            global $wpdb;

            $missing = $this->get_required_field_associations();

            $sql_in = '(\'' . implode( '\',\'', $missing ) . '\')';
            $res    = $wpdb->get_col( "SELECT association FROM {$wpdb->prefix}wpbdp_form_fields WHERE association IN {$sql_in} GROUP BY association" );

            return array_diff( $missing, $res );
        }

        /**
         * @since 3.6.9
         */
        public function get_default_fields( $id = '' ) {
            $default_fields = array(
                'title'    => array(
					'label'         => __( 'Listing Title', 'business-directory-plugin' ),
					'field_type'    => 'textfield',
					'association'   => 'title',
					'weight'        => 9,
					'validators'    => array( 'required' ),
					'display_flags' => array( 'excerpt', 'listing', 'search', 'privacy' ),
					'tag'           => 'title',
				),
                'category' => array(
					'label'         => __( 'Listing Category', 'business-directory-plugin' ),
					'field_type'    => 'select',
					'association'   => 'category',
					'weight'        => 8,
					'validators'    => array( 'required' ),
					'display_flags' => array( 'excerpt', 'listing', 'search' ),
					'tag'           => 'category',
				),
                'excerpt'  => array(
					'label'         => __( 'Short Description', 'business-directory-plugin' ),
					'field_type'    => 'textarea',
					'association'   => 'excerpt',
					'weight'        => 7,
					'display_flags' => array( 'excerpt', 'listing', 'search' ),
					'tag'           => 'excerpt',
				),
                'content'  => array(
					'label'         => __( 'Description', 'business-directory-plugin' ),
					'field_type'    => 'textarea',
					'association'   => 'content',
					'weight'        => 6,
					'validators'    => array( 'required' ),
					'display_flags' => array( 'listing', 'search' ),
					'tag'           => 'content',
				),
                'website'  => array(
					'label'         => __( 'Website', 'business-directory-plugin' ),
					'field_type'    => 'url',
					'association'   => 'meta',
					'weight'        => 5,
					'validators'    => array( 'url' ),
					'display_flags' => array( 'excerpt', 'listing', 'search',  'privacy' ),
					'tag'           => 'website',
				),
                'phone'    => array(
					'label'         => __( 'Phone', 'business-directory-plugin' ),
					'field_type'    => 'textfield',
					'association'   => 'meta',
					'weight'        => 4,
					'display_flags' => array( 'excerpt', 'listing', 'search',  'privacy' ),
					'tag'           => 'phone',
				),
                'email'    => array(
					'label'         => __( 'Email', 'business-directory-plugin' ),
					'field_type'    => 'textfield',
					'association'   => 'meta',
					'weight'        => 2,
					'validators'    => array( 'email', 'required' ),
					'display_flags' => array( 'excerpt', 'listing',  'privacy' ),
					'tag'           => 'email',
				),
                'tags'     => array(
					'label'         => __( 'Listing Tags', 'business-directory-plugin' ),
					'field_type'    => 'textfield',
					'association'   => 'tags',
					'weight'        => 1,
					'display_flags' => array( 'excerpt', 'listing', 'search' ),
					'tag'           => 'tags',
				),
                'address'  => array(
					'label'         => __( 'Address', 'business-directory-plugin' ),
					'field_type'    => 'textarea',
					'association'   => 'meta',
					'weight'        => 1,
					'display_flags' => array( 'excerpt', 'listing', 'search',  'privacy' ),
					'tag'           => 'address',
				),
                'zip'      => array(
					'label'         => __( 'ZIP Code', 'business-directory-plugin' ),
					'field_type'    => 'textfield',
					'association'   => 'meta',
					'weight'        => 1,
					'display_flags' => array( 'excerpt', 'listing', 'search',  'privacy' ),
					'tag'           => 'zip',
				),
            );

            if ( $id ) {
                if ( isset( $default_fields[ $id ] ) ) {
                    return $default_fields[ $id ];
                } else {
					return null;
                }
            }

            return $default_fields;
        }

        public function create_default_fields( $identifiers = array() ) {
            $default_fields   = $this->get_default_fields();
            $fields_to_create = $identifiers ? array_intersect_key( $default_fields, array_flip( $identifiers ) ) : $default_fields;

            foreach ( $fields_to_create as &$f ) {
                $field = new WPBDP_Form_Field( $f );
                $field->save();
            }
        }

        /**
         * @deprecated since 4.0.
         */
        public function get_short_names( $fieldid = null ) {
			//_deprecated_function( __FUNCTION__, '4.0' );

            $fields     = $this->get_fields();
            $shortnames = array();

            foreach ( $fields as $f ) {
                $shortnames[ $f->get_id() ] = $f->get_shortname();
            }

            if ( $fieldid ) {
                return isset( $shortnames[ $fieldid ] ) ? $shortnames[ $fieldid ] : null;
            }

            return $shortnames;
        }

        public function _calculate_short_names() {
            $fields = $this->get_fields();
            $names  = array();

            foreach ( $fields as $field ) {
                $name = WPBDP_Form_Field_Type::normalize_name( $field->get_label() );

                if ( $name == 'images' || $name == 'image' || $name == 'username' || $name == 'featured_level' || $name == 'expires_on' || $name == 'sequence_id' || in_array( $name, $names, true ) ) {
                    $name = $name . '-' . $field->get_id();
                }

                $names[ $field->get_id() ] = $name;
            }

            update_option( 'wpbdp-field-short-names', $names, 'no' );

            return $names;
        }

        public function set_fields_order( $fields_order = array() ) {
            if ( ! $fields_order ) {
                return false;
            }

            global $wpdb;

            $total = count( $fields_order );

            foreach ( $fields_order as $i => $field_id ) {
                $wpdb->update(
                    $wpdb->prefix . 'wpbdp_form_fields',
                    array( 'weight' => ( $total - $i ) ),
                    array( 'id' => $field_id )
                );
            }
			WPBDP_Utils::cache_delete_group( 'wpbdp_form_fields' );

            return true;
        }

        /**
         * @since 4.0
         */
        public function maybe_correct_tags() {
            $fields = wpbdp_get_form_fields();

            foreach ( $fields as $f ) {
                if ( $f->get_tag() ) {
                    continue;
                }

                $f->save();
            }
        }
    }
}

if ( ! class_exists( 'WPBDP_FieldValidation' ) ) {

    class WPBDP_FieldValidation {

        private static $instance = null;

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Get the set of publicly available validators.
         *
         * @return array associative array with validator name as key and display name as value
         */
        public function get_validators() {
            $validators = array(
                'email'          => __( 'Email Validator', 'business-directory-plugin' ),
                'url'            => __( 'URL Validator', 'business-directory-plugin' ),
				'no_url'         => __( 'Don\'t Allow Urls', 'business-directory-plugin' ),
                'integer_number' => __( 'Whole Number Validator', 'business-directory-plugin' ),
                'decimal_number' => __( 'Decimal Number Validator', 'business-directory-plugin' ),
                'date_'          => __( 'Date Validator', 'business-directory-plugin' ),
                'word_number'    => __( 'Word Count Validator', 'business-directory-plugin' ),
                'tel'            => __( 'Telephone Number Validator', 'business-directory-plugin' ),
            );

            return $validators;
        }

        public function validate_field( $field, $value, $validator, $args = array() ) {
			$args['field-label'] = is_object( $field ) ? apply_filters( 'wpbdp_render_field_label', $field->get_label(), $field ) : __( 'Field', 'business-directory-plugin' );
            $args['field']       = $field;

            return call_user_func( array( $this, $validator ), $value, $args );
        }

        public function validate_value( $value, $validator, $args = array() ) {
            return ! is_wp_error( $this->validate_field( null, $value, $validator, $args ) );
        }

        /* Required validator */
        private function required( $value, $args = array() ) {
            $args = wp_parse_args(
                $args, array(
					'allow_whitespace' => false,
					'field'            => null,
                )
            );

            if ( $args['field'] && $args['field']->get_association() == 'category' ) {
                if ( is_array( $value ) && count( $value ) == 1 && ! $value[0] ) {
					return WPBDP_ValidationError(
                        sprintf(
                            /* translators: %s: field label */
                            esc_html__( '%s is required.', 'business-directory-plugin' ),
                            esc_html( $args['field-label'] )
                        )
                    );
                }
            }

            if ( ( $args['field'] && $args['field']->is_empty_value( $value ) ) || ! $value || ( is_string( $value ) && ! $args['allow_whitespace'] && ! trim( $value ) ) ) {
				return WPBDP_ValidationError(
                    sprintf(
                        /* translators: %s: field label */
                        esc_html__( '%s is required.', 'business-directory-plugin' ),
                        esc_attr( $args['field-label'] )
                    )
                );
            }
        }

        /* URL Validator */
        private function url( $value, $args = array() ) {
            if ( is_array( $value ) ) {
                $value = $value[0];
            }

            if ( esc_url_raw( $value ) !== $value ) {
                return WPBDP_ValidationError(
                    sprintf(
                        /* translators: %s: field label */
                        esc_html__( '%s is badly formatted. Valid URL format required. Include http://', 'business-directory-plugin' ),
                        esc_attr( $args['field-label'] )
                    )
                );
            }
        }

		/**
		 * Don't allow URLS that include http, www., or .com.
		 *
		 * @since 5.12.1
		 */
		private function no_url( $value, $args = array() ) {
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}

			$has_url = preg_match( '/http(s)?:/s', $value ) || preg_match( '/\.com(\w)?/s', $value );
			$has_url = $has_url || strpos( $value, 'www.' ) !== false;
			if ( $has_url ) {
				return WPBDP_ValidationError( esc_html__( 'URLs are not allowed.', 'business-directory-plugin' ) );
			}
		}

        /* EmailValidator */
        private function email( $value, $args = array() ) {
			if ( '' === $value ) {
				// Don't check formatting on an empty value.
				return;
			}

            $valid = false;

            if ( function_exists( 'filter_var' ) ) {
                $valid = filter_var( $value, FILTER_VALIDATE_EMAIL );
            } else {
                $valid = (bool) preg_match( '/^(?!(?>\x22?(?>\x22\x40|\x5C?[\x00-\x7F])\x22?){255,})(?!(?>\x22?\x5C?[\x00-\x7F]\x22?){65,}@)(?>[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+|(?>\x22(?>[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|\x5C[\x00-\x7F])*\x22))(?>\.(?>[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+|(?>\x22(?>[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|\x5C[\x00-\x7F])*\x22)))*@(?>(?>(?!.*[^.]{64,})(?>(?>xn--)?[a-z0-9]+(?>-[a-z0-9]+)*\.){0,126}(?>xn--)?[a-z0-9]+(?>-[a-z0-9]+)*)|(?:\[(?>(?>IPv6:(?>(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){7})|(?>(?!(?:.*[a-f0-9][:\]]){8,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?::(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?)))|(?>(?>IPv6:(?>(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){5}:)|(?>(?!(?:.*[a-f0-9]:){6,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4})?::(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4}:)?)))?(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}))\]))$/isD', $value );
            }

            if ( ! $valid ) {
                return WPBDP_ValidationError(
                    sprintf(
                        /* translators: %s: field label */
                        __( '%s is badly formatted. Valid Email format required.', 'business-directory-plugin' ),
                        esc_attr( $args['field-label'] )
                    )
                );
            }
        }

        /* IntegerNumberValidator */
        private function integer_number( $value, $args = array() ) {
            if ( ! ctype_digit( $value ) ) {
                return WPBDP_ValidationError(
                    sprintf(
                        /* translators: %s: field label */
                        esc_html__( '%s must be a number. Decimal values are not allowed.', 'business-directory-plugin' ),
                        esc_attr( $args['field-label'] )
                    )
                );
            }
        }

        /* DecimalNumberValidator */
        private function decimal_number( $value, $args = array() ) {
            if ( ! is_numeric( $value ) ) {
                return WPBDP_ValidationError(
                    sprintf(
                        /* translators: %s: field label */
                        __( '%s must be a number.', 'business-directory-plugin' ),
                        esc_attr( $args['field-label'] )
                    )
                );
            }
        }

        /* DateValidator */
        private function date_( $value, $args = array() ) {
            $args   = wp_parse_args(
                $args, array(
					'format'   => 'dd/mm/yyyy',
					'messages' => array(),
                )
            );
            $format = $args['format'];

            // Normalize separators.
            $format_ = str_replace( array( '/', '.', '-' ), '', $format );
            $value_  = str_replace( array( '/', '.', '-' ), '', $value );

            if ( strlen( $format_ ) != strlen( $value_ ) ) {
                /* translators: %1$s: field label, %2$s: format */
                return WPBDP_ValidationError( ( ! empty( $args['messages']['incorrect_format'] ) ) ? $args['messages']['incorrect_format'] : sprintf( esc_html__( '%1$s must be in the format %2$s.', 'business-directory-plugin' ), esc_html( $args['field-label'] ), esc_html( $format ) ) );
            }

            $d = '0';
            $m = '0';
            $y = '0';

            switch ( $format_ ) {
                case 'ddmmyy':
                    $d = substr( $value_, 0, 2 );
                    $m = substr( $value_, 2, 2 );
                    $y = substr( $value_, 4, 2 );
                    break;
                case 'ddmmyyyy':
                    $d = substr( $value_, 0, 2 );
                    $m = substr( $value_, 2, 2 );
                    $y = substr( $value_, 4, 4 );
                    break;
                case 'mmddyy':
                    $m = substr( $value_, 0, 2 );
                    $d = substr( $value_, 2, 2 );
                    $y = substr( $value_, 4, 2 );
                    break;
                case 'mmddyyyy':
                    $m = substr( $value_, 0, 2 );
                    $d = substr( $value_, 2, 2 );
                    $y = substr( $value_, 4, 4 );
                    break;
                case 'yyyymmdd':
                    $m = substr( $value_, 4, 2 );
                    $d = substr( $value_, 6, 2 );
                    $y = substr( $value_, 0, 4 );
                    break;
                default:
                    break;
            }

			if ( ! ctype_digit( $m ) || ! ctype_digit( $d ) || ! ctype_digit( $y ) || ! checkdate( (int) $m, (int) $d, (int) $y ) ) {
                /* translators: %s: field label */
                return WPBDP_ValidationError( ( ! empty( $args['messages']['invalid'] ) ) ? $args['messages']['invalid'] : sprintf( esc_html__( '%s must be a valid date.', 'business-directory-plugin' ), esc_html( $args['field-label'] ) ) );
            }
        }

        /* Image Caption Validator */
        private function caption_( $value, $args = array() ) {
            if ( $args['caption_required'] && empty( $value[1] ) ) {
                /* translators: %s: field label */
                return WPBDP_ValidationError( ! empty( $args['messages']['caption_required'] ) ? $args['messages']['caption_required'] : sprintf( esc_html__( 'Caption for %s is required.', 'business-directory-plugin' ), esc_html( $args['field-label'] ) ) );
            }
        }

        /* Word Number Validator */
        private function word_number( $value, $args = array() ) {
            $word_count = $args['field']->data( 'word_count' );

			if ( empty( $word_count ) ) {
                return;
            }

            $no_html_text = preg_replace( '/(<[^>]+>)/i', '', $value );
			$input_array  = preg_split( '/[\s,]+/', $no_html_text );

			if ( $word_count < count( $input_array ) ) {
                /* translators: %1$s: field label, %2$d: max word count */
                return WPBDP_ValidationError( sprintf( esc_html__( '%1$s must have less than %2$d words.', 'business-directory-plugin' ), esc_attr( $args['field-label'] ), $word_count ) );
            }

        }

        private function any_of( $value, $args = array() ) {
            $args = wp_parse_args(
                $args, array(
					'values'    => array(),
					'formatter' => function( $x ) {
						return join( ',', $x );
					},
                )
            );
            extract( $args, EXTR_SKIP );

            if ( is_string( $values ) ) {
                $values = explode( ',', $values );
            }

            if ( ! in_array( $value, $values ) ) {
                /* translators: %1$s: field label, %2$s allowed values */
                return WPBDP_ValidationError( sprintf( __( '%1$s is invalid. Value most be one of %2$s.', 'business-directory-plugin' ), esc_attr( $args['field-label'] ), esc_html( call_user_func( $formatter, $values ) ) ) );
            }
        }

        /**
         * Telephone number validator
         */
        private function tel( $value, $args = array() ) {
            if ( '' === $value ) {
				// Don't check formatting on an empty value.
				return;
			}
            $valid = (bool) preg_match( '/^((\+\d{1,3}(-|.| )?\(?\d\)?(-| |.)?\d{1,5})|(\(?\d{2,6}\)?))(-|.| )?(\d{3,4})(-|.| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/', $value );
            if ( ! $valid ) {
                return WPBDP_ValidationError(
                    sprintf(
                        /* translators: %s: field label */
                        __( '%s is badly formatted. Valid Phone Number format required.', 'business-directory-plugin' ),
                        esc_attr( $args['field-label'] )
                    )
                );
            }
        }

    }

}


// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
function WPBDP_ValidationError( $msg, $stop_validation = false ) {
    if ( $stop_validation ) {
        return new WP_Error( 'wpbdp-validation-error-stop', $msg );
    }

    return new WP_Error( 'wpbdp-validation-error', $msg );
}



/**
 * @since 2.3
 * @see WPBDP_FormFields::find_fields()
 */
function &wpbdp_get_form_fields( $args = array() ) {
    global $wpdb;
    global $wpbdp;

    $fields = array();

    if ( $wpbdp->get_db_version() ) {
        $fields = $wpbdp->formfields->find_fields( $args );
    }

    if ( ! $fields ) {
        $fields = array();
    }

    return $fields;
}

/**
 * @since 2.3
 * @see WPBDP_FormFields::get_field()
 */
function wpbdp_get_form_field( $id ) {
    global $wpbdp;
    return $wpbdp->formfields->get_field( $id );
}

/**
 * Validates a value against a given validator.
 *
 * @param mixed  $value
 * @param string $validator one of the registered validators.
 * @param array  $args optional arguments to be passed to the validator.
 * @return boolean True if value validates, False otherwise.
 * @since 2.3
 * @see WPBDP_FieldValidation::validate_value()
 */
function wpbdp_validate_value( $value, $validator, $args = array() ) {
    $validation = WPBDP_FieldValidation::instance();
    return $validation->validate_value( $value, $validator, $args );
}
