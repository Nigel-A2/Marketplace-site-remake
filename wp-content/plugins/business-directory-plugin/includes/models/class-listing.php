<?php
/**
 * @package WPBDP\Listing
 * @since 3.4
 */
require_once WPBDP_PATH . 'includes/models/class-payment.php';
require_once WPBDP_PATH . 'includes/models/class-listing-subscription.php';
require_once WPBDP_PATH . 'includes/helpers/class-listing-image.php';
class WPBDP_Listing {

    private $id = 0;

    public function __construct( $id ) {
        $this->id = intval( $id );
    }

    public function get_field_value( $id ) {
        $field = null;

        if ( is_numeric( $id ) ) {
            $field = wpbdp_get_form_field( $id );
        } else {
            $field = wpbdp_get_form_fields( array( 'association' => $id, 'unique' => true ) );
			if ( ! $field ) {
				// Get the field by key.
				$field = wpbdp_get_form_field( $id );
			}
        }

        return $field ? $field->html_value( $this->id ) : '';
    }

    public function get_modified_date() {
        if ( ! $this->id )
            return '';

        return wpbdp_date( get_post_modified_time( 'U', false, $this->id ) );
    }

    public function get_images( $fields = 'all', $sorted = false ) {
		$q = array(
			'numberposts' => 50,
			'post_type'   => 'attachment',
			'post_parent' => $this->id,
			'fields'      => 'ids',
		);

		$attachments = WPBDP_Utils::check_cache(
			array(
				'cache_key' => __FUNCTION__ . $this->id,
				'group'     => 'wpbdp_listings',
				'query'     => $q,
				'type'      => 'get_posts',
			)
		);
		$images = get_post_meta( $this->id, '_wpbdp[images]', true );
		$images = array_merge( is_array( $images ) ? $images : array( $images ), (array) $attachments );

		$get_ids = 'id' === $fields || 'ids' === $fields;
		$result = array();
        foreach ( array_unique( $images ) as $attachment_id ) {
            $attachment = get_post( $attachment_id );
            if ( ! $attachment || ! wp_attachment_is_image( $attachment->ID ) )
                continue;

            if ( ! $sorted && $get_ids ) {
                $result[] = $attachment->ID;
			} else {
				$img = WPBDP_Listing_Image::get( $attachment->ID );
				if ( $img ) {
					$result[] = $img;
				}
            }
        }

        if ( $result && $sorted ) {
			uasort(
				$result,
				function( $x, $y ) {
	            	return $y->weight - $x->weight;
	            }
	        );

	        if ( $get_ids ) {
	            foreach ( $result as $i => $img ) {
					$result[ $i ] = $img->id;
	            }
	        }

	        $this->prepend_thumbnail( $result, $fields );
        }

        return $result;
    }

    /**
     * @since 3.6.11
     */
    public function get_images_meta() {
        $images = $this->get_images( 'ids' );
        $meta = array();

        foreach ( $images as $img_id ) {
			$meta[ $img_id ] = array(
				'order'   => (int) get_post_meta( $img_id, '_wpbdp_image_weight', true ),
				'caption' => strval( get_post_meta( $img_id, '_wpbdp_image_caption', true ) )
			);
        }

        return $meta;
    }

    /**
	 * Sets listing images.
	 *
     * @param array $images array of image IDs.
     * @param boolean $append if TRUE images will be appended without clearing previous ones.
     */
    public function set_images( $images = array(), $append = false ) {
        if ( ! $append ) {
            $current = $this->get_images( 'ids' );

            foreach ( $current as $img_id ) {
                if ( ! in_array( $img_id, $images, true ) && wp_attachment_is_image( $img_id ) )
                    wp_delete_attachment( $img_id, true );
            }
        }

		foreach ( $images as $image_id ) {
			wp_update_post( array( 'ID' => $image_id, 'post_parent' => $this->id ) );
		}

		WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );
    }

	/**
	 * Remove an image from a listing. If the image belongs to the listing,
	 * clear the post parent or assign it to another listing. This will only
	 * delete images from the media library if the post parent is this listing,
	 * and no other listings are using it.
	 *
	 * @since 5.12
	 */
	public function remove_image( $image_id ) {
		$current = $this->get_images( 'ids' );

		$keep_images = array();
		foreach ( $current as $current_img_id ) {
			if ( $image_id === $current_img_id ) {
				// Remove post parent.
				$parent_id = (int) wp_get_post_parent_id( $image_id );
				if ( $parent_id === $this->id ) {
					WPBDP_Listing_Image::clear_post_parent( $image_id );
					WPBDP_Listing_Image::maybe_delete_image( $image_id, $this->id );
				}
			} else {
				$keep_images[] = $current_img_id;
			}
		}

		update_post_meta( $this->id, '_wpbdp[images]', $keep_images );
		WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );
		clean_post_cache( $this->id );
	}

	public function set_thumbnail_id( $image_id ) {
		delete_post_meta( $this->id, '_wpbdp[thumbnail_id]' );

		if ( ! $image_id ) {
			delete_post_thumbnail( $this->id );
			return;
		}

		set_post_thumbnail( $this->id, $image_id );
	}

    /**
     * Gets the attachment object that representes this listing's thumbnail.
     *
     * @since 5.1.7
     *
     * @return null|object Post     An attachment of this listing.
     */
    public function get_thumbnail() {
		$thumbnail = $this->get_saved_thumbnail();

        if ( $thumbnail ) {
            return $thumbnail;
        }

		// If no thumbnail is saved, use the first image.
        $images = $this->get_images( 'ids' );

        if ( ! $images ) {
			// Clear out previous value.
			$this->set_thumbnail_id( 0 );
            return null;
        }

        $this->set_thumbnail_id( $images[0] );

		return WPBDP_Utils::check_cache(
			array(
				'cache_key' => __FUNCTION__ . $this->id,
				'group'     => 'wpbdp_listings',
				'query'     => $images[0],
				'type'      => 'get_post',
			)
		);
    }

	/**
	 * Get saved thumbnail image.
	 *
	 * @since v5.9
	 */
	private function get_saved_thumbnail() {
		$thumbnail_id = get_post_meta( $this->id, '_thumbnail_id', true );

		if ( ! $thumbnail_id ) {
			$thumbnail_id = get_post_meta( $this->id, '_wpbdp[thumbnail_id]', true );
		}

		return $thumbnail_id ? get_post( $thumbnail_id ) : null;
	}

	/**
	 * Add thumbnail as first image.
	 *
	 * @since v5.9
	 */
	private function prepend_thumbnail( &$images, $fields = 'ids' ) {
		$thumbnail = $this->get_saved_thumbnail();
		if ( ! $thumbnail ) {
			return;
		}

		if ( $fields === 'ids' || $fields === 'id' ) {
			$thumbnail = $thumbnail->ID;
			$find = array_search( $thumbnail, $images, true );
		} else {
			foreach ( $images as $k => $image ) {
				if ( $image->id === $thumbnail->ID ) {
					$thumbnail = $image;
					$find = $k;
					break;
				}
			}
		}

		if ( isset( $find ) ) {
			unset( $images[ $find ] );
		}

		array_unshift( $images, $thumbnail );
	}

    /**
     * Get the ID of the attachment that represents this listing's thumbnail.
     *
     * @return int  An ID or 0.
     */
    public function get_thumbnail_id() {
        $thumbnail = $this->get_thumbnail();

        if ( ! $thumbnail ) {
            return 0;
        }

        return $thumbnail->ID;
    }

    public function set_title( $title ) {
        wp_update_post( array( 'ID' => $this->id, 'post_title' => $title ) );
    }

    public function get_title() {
        return get_the_title( $this->id );
    }

    public function get_id() {
        return $this->id;
    }

    public function calculate_expiration_date( $time, &$fee ) {
        if ( is_array( $fee ) ) {
            $days = isset( $fee['days'] ) ? $fee['days'] : $fee['fee_days'];
        } else if ( is_a( $fee, 'WPBDP__Fee_Plan' ) ) {
            $days = $fee->days;
        } elseif ( is_object( $fee ) && isset( $fee->fee_days ) ) {
            $days = $fee->fee_days;
        } else {
            $days = 0;
        }

        if ( 0 == $days )
            return null;

        $expire_time = strtotime( sprintf( '+%d days', $days ), $time );
        return date( 'Y-m-d H:i:s', $expire_time );
    }

	public function get_categories( $fields = 'all' ) {
        $args = array();
        $args['fields'] = $fields;

        return wp_get_post_terms( $this->id, WPBDP_CATEGORY_TAX, $args );
    }

    public function set_categories( $categories ) {
        $category_ids = array_map( 'intval', $categories );
        wp_set_post_terms( $this->id, $category_ids, WPBDP_CATEGORY_TAX, false );
    }

    /**
     * @since 5.0
     */
    public function is_recurring() {
        if ( $plan = $this->get_fee_plan() ) {
            return $plan->is_recurring;
        }

        return false;
    }

    /**
     * @since 5.0
     */
    public function get_subscription() {
        try {
            $subscription = new WPBDP__Listing_Subscription( $this->id );
        } catch ( Exception $e ) {
            $subscription = null;
        }
        return $subscription;
    }

    /**
     * @since 5.0
     */
    public function has_subscription() {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d AND is_recurring = %d", $this->id, 1 );
		$total = WPBDP_Utils::check_cache(
			array(
				'cache_key' => 'listing_subscription_' . $this->id,
				'group'     => 'wpbdp_listings',
				'query'     => $query,
				'type'      => 'get_var',
			)
		);
		return absint( $total ) > 0;
    }

    public function is_published() {
        return 'publish' == get_post_status( $this->id );
    }

    public function get_permalink() {
        if ( ! $this->id )
            return '';

        return get_permalink( $this->id );
    }

    /**
     * @since 5.0
     */
    public function get_admin_edit_link() {
        return admin_url( 'post.php?post=' . $this->id . '&action=edit' );
    }

    public function get_payment_status() {
        $status = 'ok';

        if ( WPBDP_Payment::objects()->filter( array( 'listing_id' => $this->id, 'status' => 'pending' ) )->count() > 0 )
            $status = 'pending';

		// phpcs:ignore WordPress.NamingConventions.ValidHookName
        return apply_filters( 'WPBDP_Listing::get_payment_status', $status, $this->id );
    }

    /**
     * @since 5.1.9
     */
    public function get_renewal_date() {
        $filters = array(
            'listing_id' => $this->id,
            'status' => 'completed',
            'payment_type' => 'renewal',
        );

        $payments = WPBDP_Payment::objects()->filter( $filters )->order_by( '-id' )->to_array();

        if ( ! isset( $payments[0] ) ) {
            return null;
        }

        return wpbdp_date_full_format( strtotime( $payments[0]->created_at ) );
    }

    /**
     * @since 5.0
     */
    public function get_payments() {
        $payments = WPBDP_Payment::objects()->filter( array( 'listing_id' => $this->id ) );
        return $payments;
    }

    public function get_latest_payments() {
        return WPBDP_Payment::objects()->filter( array( 'listing_id' => $this->id ) )->order_by( '-id' )->to_array();
    }

    /**
     * @since 5.1.9
     */
    public function get_latest_payment() {
        $payments = $this->get_latest_payments();

        return count( $payments ) ? $payments[0] : null;
    }

    public function delete_payment_history() {
        $payments = $this->get_latest_payments();

        if ( ! $payments ) {
			return new WP_Error( 'No listing payments', _x( 'Listing has no registered payments', 'listing', 'business-directory-plugin' ) );
        }

        foreach ( $payments as $payment ) {
            if ( ! $payment->delete() ) {
                return new WP_Error(
                    'payment delete error',
					sprintf(
						'%s: %s',
                        _x( "Can't delete payment", 'listing', 'business-directory-plugin' ),
                        $payment->id
                    )
                );
            }
        }

        return true;

    }

    public function publish() {
        if ( ! $this->id )
            return;

        wp_update_post( array( 'post_status' => 'publish', 'ID' => $this->id ) );
    }

    /**
     * @since 5.0
     */
    public function set_status( $status ) {
        global $wpdb;

        $old_status = $this->get_status( false, false );
        $new_status = $status;

        if ( $old_status == $new_status || ! in_array( $new_status, array_keys( self::get_stati() ), true ) )
            return;

        $wpdb->update( $wpdb->prefix . 'wpbdp_listings', array( 'listing_status' => $new_status ), array( 'listing_id' => $this->id ) );

        switch ( $new_status ) {
        case 'expired':
            if ( 'trash' != get_post_status( $this->id ) ) {
                $this->set_post_status( 'draft' );
            }

            wpbdp_insert_log( array( 'log_type' => 'listing.expired', 'object_id' => $this->id, 'message' => _x( 'Listing expired', 'listing', 'business-directory-plugin' ) ) );
            do_action( 'wpbdp_listing_expired', $this );
            break;
        default:
            break;
        }

        do_action( 'wpbdp_listing_status_change', $this, $old_status, $new_status );
    }

    public function set_post_status( $status ) {
        if ( ! $this->id )
            return;

        $status = apply_filters( 'wpbdp_listing_post_status', $status, $this );

        wp_update_post( array( 'post_status' => $status, 'ID' => $this->id ) );
    }

    public function delete() {
        global $wpdb;
		$status = apply_filters( 'wpbdp_delete_post_status', wpbdp_get_option( 'deleted-status' ) );
        $wpdb->update( $wpdb->posts, array( 'post_status' => $status ), array( 'ID' => $this->id ) );
        clean_post_cache( $this->id );

        return true;
    }

    public function notify( $kind = 'save', &$extra = null ) {
        // if ( in_array( $kind, array( 'save', 'edit', 'new' ), true ) )
        //     $this->save();
        //
        // switch ( $kind ) {
        //     case 'save':
        //         break;
        //
        //     case 'edit':
        //         do_action_ref_array( 'wpbdp_edit_listing', array( &$this, &$extra ) );
        //         break;
        //
        //     default:
        //         break;
        // }
    }

    /**
     * @since 3.5.3
     */
    public function get_renewal_hash( $deprecated = 0 ) {
        $hash = base64_encode( 'listing_id=' . $this->id . '&category_id=' . $deprecated );
        return $hash;
    }

    /**
     * @since 5.0
     */
    public function renew() {
        $plan = $this->get_fee_plan();

        if ( ! $plan )
            return false;

        global $wpdb;

        $row = array();

        $listing_expiration_time = $this->get_expiration_time();
        $current_time            = current_time( 'timestamp' );
        $expiration_base_time    = $current_time > $listing_expiration_time ? $current_time : $listing_expiration_time;
        $expiration              = $this->calculate_expiration_date( $expiration_base_time, $plan );

        if ( $expiration ) {
            $row['expiration_date'] = $expiration;
        }

        if ( ! empty( $row ) ) {
            $wpdb->update( $wpdb->prefix . 'wpbdp_listings', $row, array( 'listing_id' => $this->id ) );
        } else {
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_listings SET expiration_date = NULL WHERE listing_id = %d", $this->id ) );
        }

        $this->set_status( 'complete' );
        $this->set_post_status( 'publish' );

        do_action( 'wpbdp_listing_renewed', $this, false, 'admin' );
    }

    public function get_renewal_url( $deprecated = 0 ) {
        // TODO: we should probably encode the ID somehow using info that only we have so external users can't
        // start checking renewal for all listings just by changing the ID.
        return wpbdp_url( 'renew_listing', $this->id );
    }

	/**
	 * Get the payment url
	 *
	 * @since 5.15
	 *
	 * @return string
	 */
	public function get_payment_url() {
		$payment = $this->get_latest_payment();
		return $payment->get_checkout_url();
	}

	/**
	 * @since 5.9.2
	 */
	public function owned_by_user( $user_id = 'current' ) {
		if ( $user_id === 'current' ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $user_id ) || ! $this->id ) {
			// This function is currently intended for logged in users.
			return true;
		}

		$post = get_post( $this->id );
		return $user_id === absint( $post->post_author );
	}

    /**
     * @since 4.0
     */
    public function get_access_key() {
        if ( $key = get_post_meta( $this->id, '_wpbdp[access_key]', true ) )
            return $key;

        // Generate access key.
        $new_key = sha1( sprintf( '%s%s%d', $this->get_auth_key(), uniqid( '', true ), rand( 1, 1000 ) ) );
        if ( update_post_meta( $this->id, '_wpbdp[access_key]', $new_key ) )
            return $new_key;
    }

    /**
     * @since 5.0
     */
    public function validate_access_key_hash( $hash ) {
        $key = $this->get_access_key();
        return sha1( $this->get_auth_key() . $key ) == $hash;
    }

	/**
	 * @since 6.0.1
	 */
	private function get_auth_key() {
		return defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
	}

    public function get_author_meta( $meta ) {
        if ( ! $this->id )
            return '';

        $post = get_post( $this->id );
        return get_the_author_meta( $meta, (int) $post->post_author );
    }

    /**
     * @since 3.6.9
     */
    public function get_sticky_status( $consider_plans = true ) {
        global $wpdb;
        $is_sticky = (bool) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT 1 AS x FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d AND is_sticky = %d",
                $this->id,
                1 )
        );

        return $is_sticky ? 'sticky' : 'normal';
    }

    /**
     * @since 5.0
     */
    public function has_fee_plan( $fee = false ) {
        $current = $this->get_fee_plan();
        return ( ! $fee && ! empty( $current ) ) || ( $fee && $current && $current->fee_id == $fee );
    }

    /**
     * @since 5.0
     *
     * @return false|object
     */
    public function get_fee_plan() {
        global $wpdb;

		$sql = $wpdb->prepare( "SELECT listing_id, fee_id, fee_price, fee_days, fee_images, expiration_date, is_recurring, is_sticky FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d LIMIT 1", $this->id );
		$res = WPBDP_Utils::check_cache(
			array(
				'cache_key' => 'listing_fee_plan' . $this->id,
				'group'     => 'wpbdp_listings',
				'query'     => $sql,
				'type'      => 'get_row',
			)
		);
		if ( ! $res ) {
			return false;
		}

		if ( $res->fee_id ) {
			$fee = wpbdp_get_fee_plan( $res->fee_id );
		} else {
			$fee = null;
		}

        $res->fee = $fee;
        $res->fee_label = $fee ? $fee->label : _x( '(Unavailable Plan)', 'listing', 'business-directory-plugin' );
        $res->expired = $res->expiration_date ? strtotime( $res->expiration_date ) <= current_time( 'timestamp' ) : false;

        return $res;
    }

    /**
     * @since 5.0
     */
    public function update_plan( $plan = null, $args = array() ) {
        global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'clear'       => 0, /* Whether to use old values (if available). */
				'recalculate' => 1 /* Whether to recalculate the expiration or not */
			)
		);

        $row = array();

        if ( is_numeric( $plan ) || ( is_array( $plan ) && ! empty( $plan['fee_id'] ) ) ) {
            $plan_id = is_numeric( $plan ) ? absint( $plan ) : absint( $plan['fee_id'] );

            if ( $plan_ = wpbdp_get_fee_plan( $plan_id ) ) {
                $row['fee_id'] = $plan_id;
                $row['fee_images'] = $plan_->images;
                $row['fee_days'] = $plan_->days;
                $row['is_sticky'] = $plan_->sticky;
                $row['fee_price'] = $plan_->amount;
                $row['is_recurring'] = $plan_->recurring;
            }
        }

        if ( is_array( $plan ) ) {
            foreach ( array( 'fee_days', 'fee_images', 'fee_price', 'is_sticky', 'expiration_date', 'is_recurring', 'subscription_id', 'subscription_data' ) as $key ) {
                if ( array_key_exists( $key, $plan ) ) {
                    $row[ $key ] = $plan[ $key ];
                }
            }

            if ( ! empty( $plan['amount'] ) ) {
                $row['fee_price'] = $plan['amount'];
            }
        }

        if ( ! $args['clear'] ) {
            $old_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d", $this->id ), ARRAY_A );

            if ( $old_row ) {
                $row = array_merge( $old_row, $row );
            }
        }

        if ( empty( $row ) )
            return false;

        $row['listing_id'] = $this->id;
        $row['is_sticky'] = (int) $row['is_sticky'];

        if ( $args['recalculate'] ) {
            if ( ! $plan || ! array_key_exists( 'expiration_date', $plan ) ) {
                $expiration = $this->calculate_expiration_date( current_time( 'timestamp' ), $row );

                if ( $expiration ) {
                    $row['expiration_date'] = $expiration;
                }
            }
        }

        if ( is_null( $row['expiration_date'] ) || empty( $row['expiration_date'] ) ) {
            unset( $row['expiration_date'] );
		} elseif ( strtotime( $row['expiration_date'] ) < current_time( 'timestamp' ) ) {
			$row['listing_status'] = 'expired';
		}

        if ( ! empty( $row['recurring_data'] ) ) {
            $row['recurring_data'] = maybe_serialize( $row['recurring_data'] );
        }

		WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );
        return $wpdb->replace( "{$wpdb->prefix}wpbdp_listings", $row );
    }

    /**
     * @since 5.0
     */
    public function set_fee_plan( $fee, $recurring_data = array() ) {
        global $wpdb;

		WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );

        if ( is_null( $fee ) ) {
            $wpdb->delete( $wpdb->prefix . 'wpbdp_listings', array( 'listing_id' => $this->id ) );
            // $wpdb->replace( $wpdb->prefix . 'wpbdp_listings', array( 'listing_id' => $this->id, 'fee_id' => null, 'fee_days' => 0, 'fee_images' => 0, 'is_sticky' => 0, 'expiration_date' => null ) );
            return true;
        }

        $fee = is_numeric( $fee ) ? wpbdp_get_fee_plan( $fee ) : $fee;

        if ( ! $fee )
            return false;

		$row = array(
			'listing_id'   => $this->id,
			'fee_id'       => $fee->id,
			'fee_days'     => $fee->days,
			'fee_images'   => $fee->images,
			'fee_price'    => $fee->calculate_amount( wp_get_post_terms( $this->id, WPBDP_CATEGORY_TAX, array( 'fields' => 'ids' ) ) ),
			'is_recurring' => $fee->recurring || ! empty( $recurring_data ),
			'is_sticky'    => (int) $fee->sticky,
		);

        if ( $expiration = $this->calculate_expiration_date( current_time( 'timestamp' ), $fee ) )
            $row['expiration_date'] = $expiration;

        if ( ! empty( $recurring_data ) ) {
            $row['subscription_id']   = ! empty( $recurring_data['subscription_id'] ) ? $recurring_data['subscription_id'] : '';
            $row['subscription_data'] = ! empty( $recurring_data['subscription_data'] ) ? serialize( $recurring_data['subscription_data'] ) : '';
        }

        return $wpdb->replace( $wpdb->prefix . 'wpbdp_listings', $row );
    }

    /**
     * @since 5.0
     */
    public function set_fee_plan_with_payment( $fee, $recurring = false ) {
        $previous_plan = $this->get_fee_plan();
        $fee = is_numeric( $fee ) ? wpbdp_get_fee_plan( $fee ) : $fee;
        $this->set_fee_plan( $fee );
        $plan = $this->get_fee_plan();

        if ( $previous_plan && $fee->id == $previous_plan->fee_id ) {
            return null;
        }

        $payment_type = $previous_plan ? 'plan_change' : 'initial';

        return $this->create_payment_from_plan( $payment_type, $plan );
    }

    public function generate_or_retrieve_payment() {
        $plan = $this->get_fee_plan();

        if ( ! $plan )
            return false;

		$existing_payment = $this->get_existing_payment_for_plan( $plan );
		if ( $existing_payment ) {
			return $existing_payment;
		}

        return $this->create_payment_from_plan( 'initial', $plan );
    }

	/**
	 * Search the fees in the payments if the current payment of the plan has been made or exists.
	 * This prevents generating duplicate payments of the same fee in situations where a user will
	 * go back to correct something.
	 *
	 * @since 5.17
	 */
	private function get_existing_payment_for_plan( $plan ) {
		$existing_payment = WPBDP_Payment::objects()->filter(
			array(
				'listing_id'   => $this->id,
				'payment_type' => 'initial',
			)
		)->get();

		if ( ! $existing_payment ) {
			return false;
		}

		// Get the current fee ids for this payment, and check if the current plan is included.
		$plan_ids    = array_column( $existing_payment->payment_items, 'fee_id' );
		$is_for_plan = in_array( $plan->fee_id, $plan_ids, true );

		return $is_for_plan ? $existing_payment : false;
	}

    /**
     * @since 5.1.9
     */
    private function create_payment_from_plan( $payment_type, $plan ) {
		$payment = new WPBDP_Payment(
			array(
				'listing_id' => $this->id,
				'payment_type' => $payment_type,
			)
		);

        if ( $plan->is_recurring ) {
            $item_description = sprintf( _x( 'Plan "%s" (recurring)', 'listing', 'business-directory-plugin' ), $plan->fee_label );
        } else {
            $item_description = sprintf( _x( 'Plan "%s"', 'listing', 'business-directory-plugin' ), $plan->fee_label );
        }

        $payment->payment_items[] = array(
            'type' => $plan->is_recurring ? 'recurring_plan' : 'plan',
            'description' => $item_description,
            'amount' => $plan->fee_price,
            'fee_id' => $plan->fee_id,
            'fee_days' => $plan->fee_days,
            'fee_images' => $plan->fee_images,
        );

        $payment->save();

        return $payment;
    }

    /**
     * @since 5.0
     */
    public function get_expiration_date() {
        $plan = $this->get_fee_plan();
        return $plan ? $plan->expiration_date : null;
    }

    /**
     * @since 5.0
     */
    public function get_expiration_time() {
        return strtotime( $this->get_expiration_date() );
    }

    /**
     * @since 5.0
     */
    public function get_status( $force_refresh = false, $calculate = true ) {
        global $wpdb;

        $status_ = $wpdb->get_var( $wpdb->prepare( "SELECT listing_status FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d", $this->id ) );

        if ( 'unknown' == $status_ || $force_refresh ) {
            if ( $calculate ) {
                $status = $this->calculate_status();
            } else {
                $status = 'unknown';
            }
        } else if ( ! $status_ ) {
            $status = 'incomplete';
        } else {
            $status = $status_;
        }

        $status = apply_filters( 'wpbdp_listing_status', $status, $this->id );

        if ( ! $status_ || $status_ != $status || $force_refresh )
            $wpdb->update( $wpdb->prefix . 'wpbdp_listings', array( 'listing_status' => $status ), array( 'listing_id' => $this->id ) );

        return $status;
    }

    /**
     * @since 5.0
     */
    public function get_status_label() {
        $stati = self::get_stati();

        return $stati[ $this->get_status() ];
    }

    /**
     * @since 5.0
     */
    private function calculate_status() {
        global $wpdb;

        $is_expired = (bool) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT 1 AS x FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d AND expiration_date IS NOT NULL AND expiration_date < %s",
                $this->id,
                current_time( 'mysql' )
            )
        );
        $pending_payment = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wpbdp_payments WHERE listing_id = %d AND status = %s ORDER BY id DESC LIMIT 1",
                $this->id,
                'pending'
            )
        );

        if ( ! $pending_payment || ! in_array( $pending_payment->payment_type, array( 'initial', 'renewal' ), true ) )
            return $is_expired ? 'expired' : 'complete';

        return ( 'initial' == $pending_payment->payment_type ? 'pending_payment' : 'pending_renewal' );
    }

    /**
     * @since 5.0
     */
    public static function get_stati() {
        $stati = array(
            'unknown' => _x( 'Unknown', 'listing status', 'business-directory-plugin' ),
            'legacy' => _x( 'Legacy', 'listing status', 'business-directory-plugin' ),
            'incomplete' => _x( 'Incomplete', 'listing status', 'business-directory-plugin' ),
            'pending_payment' => _x( 'Pending Payment', 'listing status', 'business-directory-plugin' ),
            'complete' => _x( 'Complete', 'listing status', 'business-directory-plugin' ),
            'pending_upgrade' => _x( 'Pending Upgrade', 'listing status', 'business-directory-plugin' ),
            'expired' => _x( 'Expired', 'listing status', 'business-directory-plugin' ),
            'pending_renewal' => _x( 'Pending Renewal', 'listing status', 'business-directory-plugin' ),
            'abandoned' => _x( 'Abandoned', 'listing status', 'business-directory-plugin' ),
        );
        $stati = apply_filters( 'wpbdp_listing_stati', $stati );

        return $stati;
    }

    /**
     * @since next-release
     */
    public static function count_listings( $args = array() ) {
        global $wpdb;

        $args = self::parse_count_args( $args );
        extract( $args );

        $query_post_statuses = "'" . implode( "','", $post_status ) . "'";
        $query_listing_statuses = "'" . implode( "','", $status ) . "'";
        $query = "SELECT COUNT(*) FROM {$wpdb->posts} p JOIN {$wpdb->prefix}wpbdp_listings l ON p.ID = l.listing_id WHERE p.post_type = %s AND p.post_status IN ({$query_post_statuses}) AND l.listing_status IN ({$query_listing_statuses})";
        $query = $wpdb->prepare( $query, WPBDP_POST_TYPE );

        return absint( $wpdb->get_var( $query ) );
    }

    private static function parse_count_args( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'post_status' => 'all',
				'status'      => 'all',
			)
		);

        if ( ! is_array( $args['post_status'] ) ) {
            if ( 'all' == $args['post_status'] ) {
                $args['post_status'] = array_keys( get_post_statuses() );
            } else {
				$args['post_status'] = explode( ',', $args['post_status'] );
            }
        }

        if ( ! is_array( $args['status'] ) ) {
            if ( 'all' == $args['status'] ) {
				$args['status'] = array_keys( self::get_stati() );
            } else {
                $args['status'] = explode( ',', $args['status'] );
            }
        }

        return $args;
    }

    public static function count_listings_with_no_fee_plan( $args = array() ) {
        global $wpdb;

        $args = self::parse_count_args( $args );

        $query_post_statuses = "'" . implode( "','", $args['post_status'] ) . "'";

        $query = "SELECT COUNT(*) FROM {$wpdb->posts} p ";
		$query .= "LEFT JOIN {$wpdb->prefix}wpbdp_listings l ON ( p.ID = l.listing_id ) ";
		$query .= 'WHERE p.post_type = %s ';
		$query .= "AND post_status IN ({$query_post_statuses}) ";
		$query .= 'AND l.listing_id IS NULL ';

        return absint( $wpdb->get_var( $wpdb->prepare( $query, WPBDP_POST_TYPE ) ) );
    }

    /**
     * @since 5.0
     */
    public static function validate_access_key( $key, $email = '' ) {
        if ( ! $key )
            return false;

        global $wpdb;

        $post_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
				'_wpbdp[access_key]',
				$key
			)
        );

        if ( ! $post_id ) {
            return false;
        }

		return intval(
			$wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_value = %s",
					$post_id,
					$email
				)
			)
		) > 0;
    }

    /**
     * @since 5.0
     */
    public function get_sequence_id() {
        $sequence_id = get_post_meta( $this->id, '_wpbdp[import_sequence_id]', true );

        if ( ! $sequence_id ) {
            global $wpdb;

			$candidate = intval(
				$wpdb->get_var(
					$wpdb->prepare(
						"SELECT MAX(CAST(meta_value AS UNSIGNED INTEGER )) FROM {$wpdb->postmeta} WHERE meta_key = %s",
						'_wpbdp[import_sequence_id]'
					)
				)
			);
            $candidate++;

			if ( false == add_post_meta( $this->id, '_wpbdp[import_sequence_id]', $candidate, true ) ) {
                $sequence_id = 0;
			} else {
                $sequence_id = $candidate;
			}
        }

        return $sequence_id;
    }

    /**
     * @since 5.0
     */
    public function get_flags() {
        global $wpdb;

        $flags = trim( $wpdb->get_var( $wpdb->prepare( "SELECT flags FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d", $this->id ) ) );

        if ( ! $flags )
            return array();

        return explode( ',', $flags );
    }

    /**
     * @since 5.0
     */
    public function set_flag( $flag ) {
        global $wpdb;

        $flags = $this->get_flags();

        if ( ! in_array( $flag, $flags, true ) )
            $flags[] = $flag;

        $wpdb->update( $wpdb->prefix . 'wpbdp_listings', array( 'flags' => implode( ',', $flags ) ), array( 'listing_id' => $this->id ) );
    }

    /**
     * @since 5.0
     */
    public function _after_save( $context = '' ) {
        if ( 'submit-new' == $context ) {
            do_action( 'WPBDP_Listing::listing_created', $this->id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName
            do_action( 'wpbdp_add_listing', $this->id );
        } elseif ( 'submit-edit' == $context ) {
            do_action( 'wpbdp_edit_listing', $this->id );
            do_action( 'WPBDP_Listing::listing_edited', $this->id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName
        }

        do_action( 'wpbdp_save_listing', $this->id, 'submit-new' == $context );

        $this->get_status(); // This forces a status refresh if there's no status.

        // Do not let expired listings be public.
        if ( $this->get_status() && in_array( $this->get_status(), array( 'expired', 'pending_renewal' ) ) && 'publish' == get_post_status( $this->id ) ) {
            $this->set_post_status( 'draft' );
        }
    }

    /**
     * @since 5.0
     */
    public function after_delete( $context = '' ) {
        global $wpdb;

        // Remove attachments.
        $attachments = get_posts( array( 'post_type' => 'attachment', 'post_parent' => $this->id, 'numberposts' => -1, 'fields' => 'ids' ) );
        foreach ( $attachments as $attachment_id )
            wp_delete_attachment( $attachment_id, true );

        // Remove listing fees.
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d", $this->id ) );

        // Delete logs.
        $wpdb->delete( $wpdb->prefix . 'wpbdp_logs', array( 'object_type' => 'listing', 'object_id' => $this->id ) );

        // Remove payment information.
        foreach ( $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wpbdp_payments WHERE listing_id = %d", $this->id ) ) as $payment_id ) {
            $payment = WPBDP_Payment::objects()->get( $payment_id );
			if ( $payment ) {
				$payment->delete();
			}
        }
    }

    /**
     * @since 5.0
     */
    public static function insert_or_update( $args = array(), $error = false ) {
    }

    public static function get( $id ) {
        if ( WPBDP_POST_TYPE !== get_post_type( $id ) )
            return null;

        $l = new self( $id );
        return $l;
    }
}
