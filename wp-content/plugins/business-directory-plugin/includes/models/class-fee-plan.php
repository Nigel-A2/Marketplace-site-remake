<?php
/**
 * Class Plan Creates, Updates and Deletes Directory Plans
 *
 * @package BDP/Includes
 */
/**
 * @since 5.0
 */
final class WPBDP__Fee_Plan {

    public $id = 0;

    private $label       = '';
    private $description = '';
    private $enabled     = true;

    public $amount     = 0.0;
    public $days       = 0;
    public $images     = 0;
    public $sticky     = false;
    private $recurring = false;

    private $pricing_model   = 'flat';

	/**
	 * @var array $pricing_details Includes category id => price array for variable plans.
	 */
    private $pricing_details = array();

    private $supported_categories = 'all';

    private $weight     = 0;
    private $tag        = '';
    private $extra_data = array();


    public function __construct( $data = array() ) {
        if ( $data ) {
            $this->setup_plan( $data );
        }
    }

    public function &__get( $key ) {
        if ( method_exists( $this, 'get_' . $key ) ) {
            $value = call_user_func( array( $this, 'get_' . $key ) );
        } else {
            $value = &$this->{$key};
        }

        return $value;
    }

    public function __set( $key, $value ) {
        $this->{$key} = $value;
    }

    public function __isset( $key ) {
        if ( property_exists( $this, $key ) ) {
            return false === empty( $this->{$key} );
        } else {
            return false;
        }
    }

    public function exists() {
        return ! empty( $this->id );
    }

    public function save( $fire_hooks = true ) {
        global $wpdb;

        // Validate.
        $validation_errors = $this->validate();

        if ( ! empty( $validation_errors ) ) {
            $error = new WP_Error();

            foreach ( $validation_errors as $col => $msg ) {
                $error->add( 'validation_error', $msg, array( 'field' => $col ) );
            }

            return $error;
        }

        if ( $fire_hooks ) {
            do_action_ref_array( 'wpbdp_fee_before_save', array( $this ) );
        }

        $row = array();
        foreach ( get_object_vars( $this ) as $key => $value ) {
            $row[ $key ] = $value;
        }

        if ( ! $this->exists() ) {
            unset( $row['id'] );
        }

        $row['pricing_details'] = serialize( $row['pricing_details'] );

        if ( 'all' !== $row['supported_categories'] ) {
            $row['supported_categories'] = implode( ',', $row['supported_categories'] );
        }

        if ( empty( $row['extra_data'] ) ) {
            unset( $row['extra_data'] );
        } else {
            $row['extra_data'] = serialize( $row['extra_data'] );
        }

        $saved  = false;
        $update = $this->exists();
        if ( $update ) {
            $saved = $wpdb->update( $wpdb->prefix . 'wpbdp_plans', $row, array( 'id' => $this->id ) );
        } else {
            $saved = $wpdb->insert( $wpdb->prefix . 'wpbdp_plans', $row );

            if ( $saved ) {
                $this->id = $wpdb->insert_id;
            }
        }

        if ( $saved ) {
			WPBDP_Utils::cache_delete_group( 'wpbdp_plans' );
            if ( $fire_hooks ) {
                do_action( 'wpbdp_fee_save', $this, $update );
            }

            $wpdb->update(
                $wpdb->prefix . 'wpbdp_listings',
                array( 'is_sticky' => $this->sticky ? 1 : 0 ),
                array(
                    'fee_id' => $this->id,
                )
            );
        }

        return $saved;
    }

    public function update( $data ) {
        unset( $data['id'] );
        $this->setup_plan( $data );
        return $this->save();
    }

	public function delete() {
		global $wpdb;
		$deleted = $wpdb->delete( $wpdb->prefix . 'wpbdp_plans', array( 'id' => $this->id ) );
		WPBDP_Utils::cache_delete_group( 'wpbdp_plans' );
		return $deleted;
	}

    public function supports_category( $category_id ) {
        return $this->supports_category_selection( array( $category_id ) );
    }

    /**
     * @since 5.0
     */
    public function get_feature_list() {
        $items = array();

        if ( wpbdp_get_option( 'allow-images' ) ) {
            if ( ! $this->images ) {
                $items['images'] = _x( 'No images allowed.', 'fee plan', 'business-directory-plugin' );
            } else {
				$items['images'] = sprintf( _nx( '%d image allowed.', '%d images allowed.', $this->images, 'fee plan', 'business-directory-plugin' ), $this->images );
            }
        }

        $items = apply_filters( 'wpbdp_plan_feature_list', $items, $this );
        return $items;
    }

    /**
     * @since 5.0
     */
    public function calculate_amount( $categories = array() ) {
		$amount       = $this->amount;
        $pricing_info = $this->pricing_details;

		if ( $this->pricing_model === 'variable' ) {
			$amount = array_sum( wp_array_slice_assoc( $pricing_info, $categories ) );
		} elseif ( $this->pricing_model === 'extra' ) {
			$amount = $this->amount + ( $pricing_info['extra'] * count( $categories ) );
		}

        return $amount;
    }

	/**
	 * @param array $categories
	 * @since 5.0
	 */
	public function supports_category_selection( $categories = array() ) {
		if ( ! $categories ) {
			return true;
		}

		if ( is_string( $this->supported_categories ) && 'all' === $this->supported_categories ) {
			return true;
		}

		if ( array_diff( (array) $categories, (array) $this->supported_categories ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @return false|WPBDP__Fee_Plan
	 */
    public static function get_instance( $fee_id ) {
        global $wpdb;

		$all_plans = WPBDP_Utils::check_cache(
			array(
				'cache_key' => 'all',
				'group'     => 'wpbdp_plans',
				'type'      => 'all',
				'return'    => 'array',
			)
		);

		if ( $all_plans && isset( $all_plans[ $fee_id ] ) ) {
			$row = $all_plans[ $fee_id ];
		} else {
			$query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_plans WHERE id = %d", $fee_id );
			$row = WPBDP_Utils::check_cache(
				array(
					'cache_key' => $fee_id,
					'group'     => 'wpbdp_plans',
					'query'     => $query,
					'type'      => 'get_row',
					'return'    => 'array',
				)
			);
		}

        if ( ! $row ) {
            return false;
        }

        if ( 'all' !== $row['supported_categories'] ) {
            $row['supported_categories'] = array_map( 'absint', explode( ',', $row['supported_categories'] ) );
        }

        $row['pricing_details'] = maybe_unserialize( $row['pricing_details'] );
        $row['extra_data']      = maybe_unserialize( $row['extra_data'] );

        $instance = new self( $row );
        return $instance;
    }

    /**
     * @since 5.0
     */
    public function calculate_expiration_time( $base_time = null ) {
        if ( ! $base_time ) {
            $base_time = current_time( 'timestamp' );
        }

        if ( 0 === $this->days ) {
            return null;
        }

        $expire_time = strtotime( sprintf( '+%d days', $this->days ), $base_time );
        return date( 'Y-m-d H:i:s', $expire_time );
    }

	/**
	 * Count total listings in current plan.
	 *
	 * @since 5.15.3
	 *
	 * @return int
	 */
	public function count_listings() {
		global $wpdb;
		$query   = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_listings WHERE fee_id = %d", $this->id );
		$total   = WPBDP_Utils::check_cache(
			array(
				'cache_key' => 'listing_count_' . $this->id,
				'group'     => 'wpbdp_plans',
				'query'     => $query,
				'type'      => 'get_var',
			)
		);
		return $total ? $total : 0;
	}

	/**
	 * Get the total revenue for this plan. This isn't totally accurate since the fee id
	 * is coming from the setting in the listing.
	 *
	 * TODO: Update the DB structure to include the fee_id in the payments table.
	 *
	 * @since 5.15.3
	 */
	public function total_revenue() {
		if ( 0.0 === $this->amount || ! $this->amount ) {
			return 0;
		}

		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT SUM(amount) FROM {$wpdb->prefix}wpbdp_payments p
			LEFT JOIN {$wpdb->prefix}wpbdp_listings l ON (l.listing_id = p.listing_id)
			WHERE p.status = %s AND l.fee_id = %d AND l.flags != %s AND p.is_test != %d",
			'completed',
			$this->id,
			'admin-posted',
			1
		);
		$total = WPBDP_Utils::check_cache(
			array(
				'cache_key' => 'payments_complete_plan_' . $this->id,
				'group'     => 'wpbdp_payments',
				'query'     => $query,
				'type'      => 'get_var',
			)
		);
		return $total;
	}

	/**
	 * Get the plan type.
	 * This checks if the amount of the plan has been set or the pricing type.
	 * For variable plans, we check and ensure the price total is greater than 0 to classify as paid.
	 *
	 * @since 5.18
	 *
	 * @return bool
	 */
	public function is_paid_plan() {
		$is_variable = ( 'variable' === $this->pricing_model && array_sum( $this->pricing_details ) > 0 );
		return ( $is_variable || $this->amount > 0.0 );
	}

    private function setup_plan( $data ) {
        if ( is_object( $data ) ) {
            $data = get_object_vars( $data );
        }

        foreach ( $data as $key => $value ) {
            $this->{$key} = $value;
        }

        $this->sanitize();
    }

    private function sanitize() {
        $this->label         = trim( $this->label );
        $this->amount        = floatval( trim( $this->amount ) );
        $this->days          = absint( $this->days );
        $this->images        = absint( $this->images );
        $this->sticky        = (bool) $this->sticky;
        $this->recurring     = (bool) $this->recurring;
        $this->pricing_model = empty( $this->pricing_model ) ? 'flat' : $this->pricing_model;
        $this->tag           = strtolower( trim( $this->tag ) );

        if ( 'all' !== $this->supported_categories ) {
            $this->supported_categories = array_filter( array_map( 'absint', (array) $this->supported_categories ), array( $this, 'sanitize_category' ) );
        }

        if ( empty( $this->supported_categories ) ) {
            $this->supported_categories = 'all';
        }

        if ( 'extra' === $this->pricing_model ) {
            $this->pricing_details = array(
                'extra' => floatval( $this->pricing_details['extra'] ),
            );
        } elseif ( isset( $this->pricing_details['extra'] ) ) {
            unset( $this->pricing_details['extra'] );
        }

        // Unset details for categories that are not supported.
        if ( 'variable' === $this->pricing_model ) {
            $this->amount = 0.0;

            if ( 'all' !== $this->supported_categories ) {
                $this->pricing_details = wp_array_slice_assoc( $this->pricing_details, $this->supported_categories );
            }
        }

        if ( 'flat' === $this->pricing_model ) {
            $this->pricing_details = array();
        }

        // Free plan is special.
        if ( 'free' === $this->tag ) {
            $this->pricing_model        = 'flat';
            $this->amount               = 0.0;
            $this->sticky               = false;
            $this->recurring            = false;
            $this->supported_categories = 'all';
        }
    }

    private function validate() {
        $this->sanitize();

        $errors = array();

        if ( ! $this->label ) {
            $errors['label'] = _x( 'Plan label is required.', 'fees-api', 'business-directory-plugin' );
        }

        // limit 'duration' because of TIMESTAMP limited range (issue #157).
        // FIXME: this is not a long-term fix. we should move to DATETIME to avoid this entirely.
        if ( $this->days > 3650 ) {
            $errors['days'] = _x( 'Fee listing duration must be a number less than 10 years (3650 days).', 'fees-api', 'business-directory-plugin' );
        }

        if ( 1 == $this->recurring ) {
            if ( 0 === $this->days ) {
                $errors[] = str_replace( '<a>', '<a href="#wpbdp-fee-form-days">', _x( 'To set this plan as "Recurring" you must have a time for the listing to renew (e.g. 30 days). To avoid issues with the listing, please edit the <a>plan</a> appropriately.', 'fees-api', 'business-directory-plugin' ) );
            }

            $error_message = _x( 'To set this plan as "Recurring" you must set a price for your plan. To avoid issues with the listing, please edit the <a>plan</a> appropriately.', 'fees-api', 'business-directory-plugin' );

            if ( 'variable' === $this->pricing_model && 0 === array_sum( $this->pricing_details ) ) {
                $errors[] = str_replace( '<a>', '<a href="#wpbdp-fee-form-fee-category">', $error_message );
            }

            if ( 'extra' === $this->pricing_model && 0 === $this->amount + $this->pricing_details['extra'] ) {
                $errors[] = str_replace( '<a>', '<a href="#wpbdp-fee-form-fee-price">', $error_message );
            }
        }

        return $errors;
    }

    private function sanitize_category( $category_id ) {
        $category = get_term( absint( $category_id ), WPBDP_CATEGORY_TAX );
        return $category && ! is_wp_error( $category );

    }
}

require_once WPBDP_INC . 'compatibility/deprecated/class-fee-plan.php';
