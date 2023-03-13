<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpswing.com/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_Woocommerce
 * @subpackage Subscriptions_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace subscriptions_for_woocommerce_public.
 *
 * @package    Subscriptions_For_Woocommerce
 * @subpackage Subscriptions_For_Woocommerce/public
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Subscriptions_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wps_sfw_public_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, SUBSCRIPTIONS_FOR_WOOCOMMERCE_DIR_URL . 'public/css/subscriptions-for-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wps_sfw_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, SUBSCRIPTIONS_FOR_WOOCOMMERCE_DIR_URL . 'public/js/subscriptions-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'sfw_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );

	}

	/**
	 * This function is used to show subscription price on single product page.
	 *
	 * @name wps_sfw_price_html_subscription_product
	 * @param string $price product price.
	 * @param object $product Product.
	 * @since    1.0.0
	 */
	public function wps_sfw_price_html_subscription_product( $price, $product ) {

		if ( ! wps_sfw_check_product_is_subscription( $product ) ) {
			return $price;
		}
		$price = apply_filters( 'wps_rbpfw_price', $price, $product );
		$price = $this->wps_sfw_subscription_product_get_price_html( $price, $product );
		do_action( 'wps_sfw_show_start_date_frontend', $product );
		return $price;
	}

	/**
	 * This function is used to show subscription price and interval on subscription product page.
	 *
	 * @name wps_sfw_subscription_product_get_price_html
	 * @param object $price price.
	 * @param string $product product.
	 * @param array  $cart_item cart_item.
	 * @since    1.0.0
	 */
	public function wps_sfw_subscription_product_get_price_html( $price, $product, $cart_item = array() ) {

		if ( is_object( $product ) ) {
			$product_id = $product->get_id();
			$wps_sfw_subscription_number = get_post_meta( $product_id, 'wps_sfw_subscription_number', true );
			$wps_sfw_subscription_expiry_number = get_post_meta( $product_id, 'wps_sfw_subscription_expiry_number', true );
			$wps_sfw_subscription_interval = get_post_meta( $product_id, 'wps_sfw_subscription_interval', true );

			if ( isset( $wps_sfw_subscription_expiry_number ) && ! empty( $wps_sfw_subscription_expiry_number ) ) {

				$wps_sfw_subscription_expiry_interval = get_post_meta( $product_id, 'wps_sfw_subscription_expiry_interval', true );

				$wps_price_html = wps_sfw_get_time_interval( $wps_sfw_subscription_expiry_number, $wps_sfw_subscription_expiry_interval );
				// Show interval html.

				$wps_price_html = apply_filters( 'wps_sfw_show_time_interval', $wps_price_html, $product_id, $cart_item );
				$wps_price = wps_sfw_get_time_interval_for_price( $wps_sfw_subscription_number, $wps_sfw_subscription_interval );

				/* translators: %s: susbcription interval */
				$wps_sfw_price_html = '<span class="wps_sfw_interval">' . sprintf( esc_html( ' / %s ' ), $wps_price ) . '</span>';

				$price .= apply_filters( 'wps_sfw_show_sync_interval', $wps_sfw_price_html, $product_id );

				/* translators: %s: susbcription interval */
				$price .= '<span class="wps_sfw_expiry_interval">' . sprintf( esc_html__( ' For %s ', 'subscriptions-for-woocommerce' ), $wps_price_html ) . '</span>';
				$price = $this->wps_sfw_get_free_trial_period_html( $product_id, $price );
				$price = $this->wps_sfw_get_initial_signup_fee_html( $product_id, $price );

				$price = apply_filters( 'wps_sfw_show_one_time_subscription_price', $price, $product_id );

			} elseif ( isset( $wps_sfw_subscription_number ) && ! empty( $wps_sfw_subscription_number ) ) {

					$wps_price_html = wps_sfw_get_time_interval_for_price( $wps_sfw_subscription_number, $wps_sfw_subscription_interval );

					/* translators: %s: susbcription interval */
					$wps_sfw_price_html = '<span class="wps_sfw_interval">' . sprintf( esc_html( ' / %s ' ), $wps_price_html ) . '</span>';

					$price .= apply_filters( 'wps_sfw_show_sync_interval', $wps_sfw_price_html, $product_id );

					$price = $this->wps_sfw_get_free_trial_period_html( $product_id, $price );
					$price = $this->wps_sfw_get_initial_signup_fee_html( $product_id, $price );

					$price = apply_filters( 'wps_sfw_show_one_time_subscription_price', $price, $product_id );

			}
		}
		return apply_filters( 'wps_sfw_price_html', $price, $wps_price_html, $product_id );
	}



	/**
	 * This function is used to show initial signup fee on subscription product page.
	 *
	 * @name wps_sfw_get_initial_signup_fee_html
	 * @param int    $product_id Product ID.
	 * @param string $price Product Price.
	 * @since    1.0.0
	 */
	public function wps_sfw_get_initial_signup_fee_html( $product_id, $price ) {
		$wps_sfw_subscription_initial_signup_price = get_post_meta( $product_id, 'wps_sfw_subscription_initial_signup_price', true );
		if ( isset( $wps_sfw_subscription_initial_signup_price ) && ! empty( $wps_sfw_subscription_initial_signup_price ) ) {
			if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) && ! is_admin() ) {

				if ( WC()->session->__isset( 's_selected_currency' ) ) {
					$to_currency = WC()->session->get( 's_selected_currency' );
				} else {
					$to_currency = get_woocommerce_currency();
				}

				$wps_sfw_subscription_initial_signup_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( $to_currency, $wps_sfw_subscription_initial_signup_price );
			}
			/* translators: %s: signup fee */

			$price .= '<span class="wps_sfw_signup_fee">' . sprintf( esc_html__( ' and %s  Sign up fee', 'subscriptions-for-woocommerce' ), wc_price( $wps_sfw_subscription_initial_signup_price ) ) . '</span>';
		}
		return $price;
	}

	/**
	 * This function is used to show free trial period on subscription product page.
	 *
	 * @name wps_sfw_get_free_trial_period_html
	 * @param int    $product_id Product ID.
	 * @param string $price Product Price.
	 * @since    1.0.0
	 */
	public function wps_sfw_get_free_trial_period_html( $product_id, $price ) {

		$wps_sfw_subscription_free_trial_number = get_post_meta( $product_id, 'wps_sfw_subscription_free_trial_number', true );
		$wps_sfw_subscription_free_trial_interval = get_post_meta( $product_id, 'wps_sfw_subscription_free_trial_interval', true );
		if ( isset( $wps_sfw_subscription_free_trial_number ) && ! empty( $wps_sfw_subscription_free_trial_number ) ) {
			$wps_price_html = wps_sfw_get_time_interval( $wps_sfw_subscription_free_trial_number, $wps_sfw_subscription_free_trial_interval );
			/* translators: %s: free trial number */

			$price .= '<span class="wps_sfw_free_trial">' . sprintf( esc_html__( ' and %s  free trial', 'subscriptions-for-woocommerce' ), $wps_price_html ) . '</span>';
		}
		return $price;
	}

	/**
	 * This function is used to change Add to cart button text.
	 *
	 * @name wps_sfw_product_add_to_cart_text
	 * @param object $text Add to cart text.
	 * @param string $product Product..
	 * @since    1.0.0
	 */
	public function wps_sfw_product_add_to_cart_text( $text, $product ) {

		if ( wps_sfw_check_product_is_subscription( $product ) ) {
			$wps_add_to_cart_text = $this->wps_sfw_get_add_to_cart_button_text();

			if ( isset( $wps_add_to_cart_text ) && ! empty( $wps_add_to_cart_text ) ) {
				$text = $wps_add_to_cart_text;
			}
		}

		return $text;
	}

	/**
	 * This function is used to get add to cart button text.
	 *
	 * @name wps_sfw_get_add_to_cart_button_text
	 * @since    1.0.0
	 */
	public function wps_sfw_get_add_to_cart_button_text() {

		$wps_add_to_cart_text = get_option( 'wps_sfw_add_to_cart_text', '' );
		return $wps_add_to_cart_text;
	}

	/**
	 * This function is used to change place order button text.
	 *
	 * @name wps_sfw_woocommerce_order_button_text
	 * @param string $text Place order text.
	 * @since    1.0.0
	 */
	public function wps_sfw_woocommerce_order_button_text( $text ) {
		$wps_sfw_place_order_button_text = $this->wps_sfw_get_place_order_button_text();
		if ( isset( $wps_sfw_place_order_button_text ) && ! empty( $wps_sfw_place_order_button_text ) && $this->wps_sfw_check_cart_has_subscription_product() ) {
			$text = $wps_sfw_place_order_button_text;
		}

		return $text;
	}

	/**
	 * This function is used to get order button text.
	 *
	 * @name wps_sfw_get_place_order_button_text
	 * @since    1.0.0
	 */
	public function wps_sfw_get_place_order_button_text() {

		$wps_sfw_place_order_button_text = get_option( 'wps_sfw_place_order_button_text', '' );
		return $wps_sfw_place_order_button_text;
	}

	/**
	 * This function is used to check cart have subscription product.
	 *
	 * @name wps_sfw_check_cart_has_subscription_product
	 * @since    1.0.0
	 */
	public function wps_sfw_check_cart_has_subscription_product() {
		$wps_has_subscription = false;

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				if ( wps_sfw_check_product_is_subscription( $cart_item['data'] ) ) {
					$wps_has_subscription = true;
					break;
				}
			}
		}
		return $wps_has_subscription;
	}

	/**
	 * This function is used to subscription price on in cart.
	 *
	 * @name wps_sfw_show_subscription_price_on_cart
	 * @param string $product_price Product price.
	 * @param object $cart_item cart item.
	 * @param int    $cart_item_key cart_item_key.
	 * @since    1.0.0
	 */
	public function wps_sfw_show_subscription_price_on_cart( $product_price, $cart_item, $cart_item_key ) {

		if ( wps_sfw_check_product_is_subscription( $cart_item['data'] ) ) {

			if ( $cart_item['data']->is_on_sale() ) {
				$price = $cart_item['data']->get_sale_price();
			} else {
				$price = $cart_item['data']->get_regular_price();
			}
			if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
				$price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $price );
			}
			$product_price = wc_price( wc_get_price_to_display( $cart_item['data'], array( 'price' => $price ) ) );
			// Use for role base pricing.
			$product_price = apply_filters( 'wps_rbpfw_cart_price', $product_price, $cart_item );
			$product_price = $this->wps_sfw_subscription_product_get_price_html( $product_price, $cart_item['data'], $cart_item );
		}
		return $product_price;
	}



	/**
	 * This function is used to add susbcription price.
	 *
	 * @name wps_sfw_add_subscription_price_and_sigup_fee
	 * @param object $cart cart.
	 * @since    1.0.0
	 */
	public function wps_sfw_add_subscription_price_and_sigup_fee( $cart ) {

		if ( isset( $cart ) && ! empty( $cart ) ) {

			foreach ( $cart->cart_contents as $key => $cart_data ) {
				if ( wps_sfw_check_product_is_subscription( $cart_data['data'] ) ) {

					$product_id = $cart_data['data']->get_id();
					$wps_sfw_free_trial_number = $this->wps_sfw_get_subscription_trial_period_number( $product_id );

					$wps_sfw_signup_fee = $this->wps_sfw_get_subscription_initial_signup_price( $product_id );
					$wps_sfw_signup_fee = is_numeric( $wps_sfw_signup_fee ) ? (float) $wps_sfw_signup_fee : 0;

					if ( isset( $wps_sfw_free_trial_number ) && ! empty( $wps_sfw_free_trial_number ) ) {
						if ( 0 != $wps_sfw_signup_fee ) {
							// Cart price.
							$wps_sfw_signup_fee = apply_filters( 'wps_sfw_cart_price_subscription', $wps_sfw_signup_fee, $cart_data );
							$cart_data['data']->set_price( $wps_sfw_signup_fee );
						} else {
							// Cart price.
							$wps_cart_price = apply_filters( 'wps_sfw_cart_price_subscription', 0, $cart_data );
							$cart_data['data']->set_price( $wps_cart_price );
						}
					} else {
						$product_price = $cart_data['data']->get_price();
						// Cart price.
						$product_price = apply_filters( 'wps_sfw_cart_price_subscription', $product_price, $cart_data );
						$product_price += $wps_sfw_signup_fee;
						$cart_data['data']->set_price( $product_price );
					}
				}
			}
		}

	}

	/**
	 * This function is used to add susbcription price.
	 *
	 * @name wps_sfw_get_subscription_trial_period_number
	 * @param int $product_id product_id.
	 * @since    1.0.0
	 */
	public function wps_sfw_get_subscription_trial_period_number( $product_id ) {
		$wps_sfw_subscription_free_trial_number = get_post_meta( $product_id, 'wps_sfw_subscription_free_trial_number', true );
		return $wps_sfw_subscription_free_trial_number;
	}

	/**
	 * This function is used to add initial singup price.
	 *
	 * @name wps_sfw_get_subscription_initial_signup_price
	 * @param int $product_id product_id.
	 * @since    1.0.0
	 */
	public function wps_sfw_get_subscription_initial_signup_price( $product_id ) {
		$wps_sfw_subscription_initial_signup_price = get_post_meta( $product_id, 'wps_sfw_subscription_initial_signup_price', true );
		return $wps_sfw_subscription_initial_signup_price;
	}

	/**
	 * This function is used to process checkout.
	 *
	 * @name wps_sfw_process_checkout
	 * @param int   $order_id order_id.
	 * @param array $posted_data posted_data.
	 * @since    1.0.0
	 * @throws \Exception Return error.
	 */
	public function wps_sfw_process_checkout( $order_id, $posted_data ) {

		if ( ! $this->wps_sfw_check_cart_has_subscription_product() ) {
			return;
		}
		$order = wc_get_order( $order_id );
		/*delete failed order subscription*/
		wps_sfw_delete_failed_subscription( $order->get_id() );

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$wps_skip_creating_subscription = apply_filters( 'wps_skip_creating_subscription', true, $cart_item );
				if ( wps_sfw_check_product_is_subscription( $cart_item['data'] ) && $wps_skip_creating_subscription ) {

					if ( $cart_item['data']->is_on_sale() ) {
						$price = $cart_item['data']->get_sale_price();
					} else {
						$price = $cart_item['data']->get_regular_price();
					}
					$wps_recurring_total = $price * $cart_item['quantity'];

					$product_id = $cart_item['data']->get_id();

					$wps_recurring_data = $this->wps_sfw_get_subscription_recurring_data( $product_id );
					$wps_recurring_data['wps_recurring_total'] = $wps_recurring_total;
					$wps_recurring_data['product_id'] = $product_id;
					$wps_recurring_data['product_name'] = $cart_item['data']->get_name();
					$wps_recurring_data['product_qty'] = $cart_item['quantity'];

					$wps_recurring_data['line_tax_data'] = $cart_item['line_tax_data'];
					$wps_recurring_data['line_subtotal'] = $cart_item['line_subtotal'];
					$wps_recurring_data['line_subtotal_tax'] = $cart_item['line_subtotal_tax'];
					$wps_recurring_data['line_total'] = $cart_item['line_total'];
					$wps_recurring_data['line_tax'] = $cart_item['line_tax'];

					// $wps_recurring_data['cart_price'] = $price;

					$wps_recurring_data = apply_filters( 'wps_sfw_cart_data_for_susbcription', $wps_recurring_data, $cart_item );

					if ( apply_filters( 'wps_sfw_is_upgrade_downgrade_order', false, $wps_recurring_data, $order, $posted_data, $cart_item ) ) {
						return;
					}

					$subscription = $this->wps_sfw_create_subscription( $order, $posted_data, $wps_recurring_data );
					if ( is_wp_error( $subscription ) ) {
						throw new Exception( $subscription->get_error_message() );
					} else {
						$wps_has_susbcription = get_post_meta( $order_id, 'wps_sfw_order_has_subscription', true );
						if ( 'yes' != $wps_has_susbcription ) {
							update_post_meta( $order_id, 'wps_sfw_order_has_subscription', 'yes' );
						}
						do_action( 'wps_sfw_subscription_process_checkout', $order_id, $posted_data, $subscription );
					}
				}
			}
			$wps_has_susbcription = get_post_meta( $order_id, 'wps_sfw_order_has_subscription', true );

			if ( 'yes' == $wps_has_susbcription ) {
				// phpcs:disable WordPress.Security.NonceVerification.Missing
				// After process checkout.
				do_action( 'wps_sfw_subscription_process_checkout_payment_method', $order_id, $posted_data );
				// phpcs:enable WordPress.Security.NonceVerification.Missing
			}
		}
	}

	/**
	 * This function is used to get ruccuring data.
	 *
	 * @name wps_sfw_get_subscription_recurring_data
	 * @param int $product_id product_id.
	 * @since    1.0.0
	 */
	public function wps_sfw_get_subscription_recurring_data( $product_id ) {

		$wps_recurring_data = array();

		$wps_sfw_subscription_number = get_post_meta( $product_id, 'wps_sfw_subscription_number', true );
		$wps_sfw_subscription_interval = get_post_meta( $product_id, 'wps_sfw_subscription_interval', true );

		$wps_recurring_data['wps_sfw_subscription_number'] = $wps_sfw_subscription_number;
		$wps_recurring_data['wps_sfw_subscription_interval'] = $wps_sfw_subscription_interval;
		$wps_sfw_subscription_expiry_number = get_post_meta( $product_id, 'wps_sfw_subscription_expiry_number', true );

		if ( isset( $wps_sfw_subscription_expiry_number ) && ! empty( $wps_sfw_subscription_expiry_number ) ) {
			$wps_recurring_data['wps_sfw_subscription_expiry_number'] = $wps_sfw_subscription_expiry_number;
		}

		$wps_sfw_subscription_expiry_interval = get_post_meta( $product_id, 'wps_sfw_subscription_expiry_interval', true );

		if ( isset( $wps_sfw_subscription_expiry_interval ) && ! empty( $wps_sfw_subscription_expiry_interval ) ) {
			$wps_recurring_data['wps_sfw_subscription_expiry_interval'] = $wps_sfw_subscription_expiry_interval;
		}
		$wps_sfw_subscription_initial_signup_price = get_post_meta( $product_id, 'wps_sfw_subscription_initial_signup_price', true );

		if ( isset( $wps_sfw_subscription_expiry_interval ) && ! empty( $wps_sfw_subscription_expiry_interval ) ) {
			$wps_recurring_data['wps_sfw_subscription_initial_signup_price'] = $wps_sfw_subscription_initial_signup_price;
		}

		$wps_sfw_subscription_free_trial_number = get_post_meta( $product_id, 'wps_sfw_subscription_free_trial_number', true );

		if ( isset( $wps_sfw_subscription_free_trial_number ) && ! empty( $wps_sfw_subscription_free_trial_number ) ) {
			$wps_recurring_data['wps_sfw_subscription_free_trial_number'] = $wps_sfw_subscription_free_trial_number;
		}
		$wps_sfw_subscription_free_trial_interval = get_post_meta( $product_id, 'wps_sfw_subscription_free_trial_interval', true );
		if ( isset( $wps_sfw_subscription_free_trial_interval ) && ! empty( $wps_sfw_subscription_free_trial_interval ) ) {
			$wps_recurring_data['wps_sfw_subscription_free_trial_interval'] = $wps_sfw_subscription_free_trial_interval;
		}
		$wps_recurring_data = apply_filters( 'wps_sfw_recurring_data', $wps_recurring_data, $product_id );
		return $wps_recurring_data;
	}


	/**
	 * This function is used to create susbcription post.
	 *
	 * @name wps_sfw_create_subscription
	 * @param object $order order.
	 * @param array  $posted_data posted_data.
	 * @param array  $wps_recurring_data wps_recurring_data.
	 * @since    1.0.0
	 */
	public function wps_sfw_create_subscription( $order, $posted_data, $wps_recurring_data ) {
		if ( ! empty( $order ) ) {
			$order_id = $order->get_id();
			$current_date  = current_time( 'timestamp' );

			$wps_default_args = array(
				'wps_parent_order'   => $order_id,
				'wps_customer_id'    => $order->get_user_id(),
				'wps_schedule_start' => $current_date,
			);

			$wps_args              = wp_parse_args( $wps_recurring_data, $wps_default_args );
			if ( isset( $posted_data['payment_method'] ) && $posted_data['payment_method'] ) {
				$wps_enabled_gateways = WC()->payment_gateways->get_available_payment_gateways();

				if ( isset( $wps_enabled_gateways[ $posted_data['payment_method'] ] ) ) {
					$wps_payment_method = $wps_enabled_gateways[ $posted_data['payment_method'] ];
					$wps_payment_method->validate_fields();
					$wps_args['_payment_method']       = $wps_payment_method->id;
					$wps_args['_payment_method_title'] = $wps_payment_method->get_title();
				}
			}
			$wps_args['wps_order_currency'] = $order->get_currency();
			$wps_args['wps_subscription_status'] = 'pending';

			$wps_args = apply_filters( 'wps_sfw_new_subscriptions_data', $wps_args );
			// translators: post title date parsed by strftime.
			$post_title_date = gmdate( _x( '%1$b %2$d, %Y @ %I:%M %p', 'subscription post title. "Subscriptions order - <this>"', 'subscriptions-for-woocommerce' ) );
			$wps_subscription_data = array();
			$wps_subscription_data['post_type']     = 'wps_subscriptions';

			$wps_subscription_data['post_status']   = 'wc-wps_renewal';
			$wps_subscription_data['post_author']   = 1;
			$wps_subscription_data['post_parent']   = $order_id;
			/* translators: %s: post title date */
			$wps_subscription_data['post_title']    = sprintf( _x( 'WPS Subscription &ndash; %s', 'Subscription post title', 'subscriptions-for-woocommerce' ), $post_title_date );
			$wps_subscription_data['post_date_gmt'] = $order->get_date_created()->date( 'Y-m-d H:i:s' );
			$wps_subscription_data['post_date_gmt'] = $order->get_date_created()->date( 'Y-m-d H:i:s' );

			$subscription_id = wp_insert_post( $wps_subscription_data, true );

			if ( is_wp_error( $subscription_id ) ) {
				return $subscription_id;
			}
			update_post_meta( $order_id, 'wps_subscription_id', $subscription_id );
			update_post_meta( $subscription_id, 'wps_susbcription_trial_end', 0 );
			update_post_meta( $subscription_id, 'wps_susbcription_end', 0 );
			update_post_meta( $subscription_id, 'wps_next_payment_date', 0 );
			update_post_meta( $subscription_id, '_order_key', wc_generate_order_key() );

			/*if free trial*/

			$new_order = new WC_Order( $subscription_id );

			$billing_details = $order->get_address( 'billing' );
			$shipping_details = $order->get_address( 'shipping' );

			$new_order->set_address( $billing_details, 'billing' );
			$new_order->set_address( $shipping_details, 'shipping' );

			// If initial fee available.
			if ( isset( $wps_args['wps_sfw_subscription_initial_signup_price'] ) && ! empty( $wps_args['wps_sfw_subscription_initial_signup_price'] ) && empty( $wps_args['wps_sfw_subscription_free_trial_number'] ) ) {
				$initial_signup_price = $wps_args['wps_sfw_subscription_initial_signup_price'];
				// Currency switchers.
				if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
					$initial_signup_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( $wps_args['wps_order_currency'], $initial_signup_price );
				}
				$include_tax = get_option( 'woocommerce_prices_include_tax' );
				if ( 'yes' == $include_tax ) {

					// calulate subtotal_tax%.
					$order_total = $wps_args['line_total'] + $wps_args['line_subtotal_tax'];
					$line_subtotal_tax = $wps_args['line_subtotal_tax'];
					$line_subtotal_tax = ( 100 * $line_subtotal_tax ) / $order_total;

					// subtotal calculation.
					$line_subtotal = $wps_args['line_subtotal'] + $wps_args['line_subtotal_tax'] - $initial_signup_price;
					$line_subtotal = $line_subtotal - ( ( $line_subtotal * $line_subtotal_tax ) / 100 );
					$line_subtotal = $line_subtotal + $wps_args['line_subtotal_tax'];

					// calulate total_tax%.
					$line_total_tax = $wps_args['line_tax'];
					$line_total_tax = ( 100 * $line_total_tax ) / $order_total;

					// total calculation.
					$line_total = $wps_args['line_total'] + $wps_args['line_tax'] - $initial_signup_price;
					$line_total = $line_total - ( ( $line_total * $line_total_tax ) / 100 );
					$line_total = $line_total + $wps_args['line_tax'];

					$wps_args['line_subtotal'] = $line_subtotal;
					$wps_args['line_total'] = $line_total;

				} else {
					$line_subtotal = $wps_args['line_subtotal'] + $wps_args['line_subtotal_tax'] - $initial_signup_price;
					$line_total = $wps_args['line_total'] + $wps_args['line_tax'] - $initial_signup_price;
					$wps_args['line_subtotal'] = $line_subtotal - $wps_args['line_subtotal_tax'];
					$wps_args['line_total'] = $line_total - $wps_args['line_subtotal_tax'];
				}
			} elseif ( isset( $wps_args['wps_sfw_subscription_free_trial_number'] ) && ! empty( $wps_args['wps_sfw_subscription_free_trial_number'] ) ) {
				// Currency switchers.
				if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
					$wps_args['line_subtotal'] = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( $wps_args['wps_order_currency'], $wps_args['wps_recurring_total'] );
					$wps_args['line_total'] = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( $wps_args['wps_order_currency'], $wps_args['wps_recurring_total'] );
				} else {
					$wps_args['line_subtotal'] = $wps_args['wps_recurring_total'];
					$wps_args['line_total'] = $wps_args['wps_recurring_total'];
				}
				$line_subtotal = $wps_args['line_subtotal'];
				$line_total = $wps_args['line_total'];
			} else {
				$wps_args['line_subtotal'] = $wps_args['wps_recurring_total'];
				$wps_args['line_total'] = $wps_args['wps_recurring_total'];
				$line_subtotal = $wps_args['line_subtotal'];
				$line_total = $wps_args['line_total'];
			}

			$_product = wc_get_product( $wps_args['product_id'] );

			$include_tax = get_option( 'woocommerce_prices_include_tax' );
			// if inculsie tax is applicable.
			if ( 'yes' == $include_tax || ( $initial_signup_price > 0 && 'no' == $include_tax ) ) {
				$wps_pro_args = array(
					'variation' => array(),
					'totals'    => array(
						'subtotal'     => $line_subtotal - $wps_args['line_subtotal_tax'],
						'subtotal_tax' => $wps_args['line_subtotal_tax'],
						'total'        => $line_total - $wps_args['line_subtotal_tax'],
						'tax'          => $wps_args['line_tax'],
						'tax_data'     => $wps_args['line_tax_data'],
					),
				);

			} else {
				// if tax is disable or exculsive tax is applicable.

				$wps_pro_args = array(
					'variation' => array(),
					'totals'    => array(
						'subtotal'     => $line_subtotal,
						'subtotal_tax' => $wps_args['line_subtotal_tax'],
						'total'        => $line_total,
						'tax'          => $wps_args['line_tax'],
						'tax_data'     => $wps_args['line_tax_data'],
					),
				);
			}
			$wps_pro_args = apply_filters( 'wps_product_args_for_order', $wps_pro_args );

			$item_id = $new_order->add_product(
				$_product,
				$wps_args['product_qty'],
				$wps_pro_args
			);

			$new_order->update_taxes();
			$new_order->calculate_totals();
			$new_order->save();
			// After susbcription order created.
			do_action( 'wps_sfw_subscription_order', $new_order, $order_id );
			wps_sfw_update_meta_key_for_susbcription( $subscription_id, $wps_args );
			// After susbcription order created.
			do_action( 'wps_sfw_after_created_subscription', $subscription_id, $order_id );
			// After susbcription created.
			return apply_filters( 'wps_sfw_created_subscription', $subscription_id, $order_id );

		}

	}

	/**
	 * This function is used to add payment method form.
	 *
	 * @name wps_sfw_after_woocommerce_pay
	 * @since    1.0.0
	 */
	public function wps_sfw_after_woocommerce_pay() {
		global $wp;
		$wps_valid_request = false;

		if ( ! isset( $wp->query_vars['order-pay'] ) || ! wps_sfw_check_valid_subscription( absint( $wp->query_vars['order-pay'] ) ) ) {
			return;
		}

		ob_clean();
		echo '<div class="woocommerce">';
		if ( ! isset( $_GET['wps_add_payment_method'] ) && empty( $_GET['wps_add_payment_method'] ) ) {
			return;
		}
		$wps_subscription  = wc_get_order( absint( $_GET['wps_add_payment_method'] ) );
		$wps_valid_request = wps_sfw_validate_payment_request( $wps_subscription );

		if ( $wps_valid_request ) {
			$this->wps_sfw_set_customer_address( $wps_subscription );

			wc_get_template( 'myaccount/wps-add-new-payment-details.php', array( 'wps_subscription' => $wps_subscription ), '', SUBSCRIPTIONS_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/templates/' );
		}
	}

	/**
	 * This function is used to set customer address.
	 *
	 * @name wps_sfw_set_customer_address
	 * @param object $wps_subscription wps_subscription.
	 * @since    1.0.1
	 */
	public function wps_sfw_set_customer_address( $wps_subscription ) {
		$wps_sfw_billing_country = $wps_subscription->get_billing_country();
		$wps_sfw_billing_state = $wps_subscription->get_billing_state();
		$wps_sfw_billing_postcode = $wps_subscription->get_billing_postcode();
		if ( $wps_sfw_billing_country ) {
			WC()->customer->set_billing_country( $wps_sfw_billing_country );
		}
		if ( $wps_sfw_billing_state ) {
			WC()->customer->set_billing_state( $wps_sfw_billing_state );
		}
		if ( $wps_sfw_billing_postcode ) {
			WC()->customer->set_billing_postcode( $wps_sfw_billing_postcode );
		}

	}

	/**
	 * This function is used to set customer address.
	 *
	 * @name wps_sfw_set_customer_address_for_payment
	 * @param object $wps_subscription wps_subscription.
	 * @since    1.0.1
	 */
	public function wps_sfw_set_customer_address_for_payment( $wps_subscription ) {
		$wps_subscription_billing_country  = $wps_subscription->get_billing_country();
		$wps_subscription_billing_state  = $wps_subscription->get_billing_state();
		$wps_subscription_billing_postcode = $wps_subscription->get_billing_postcode();
		$wps_subscription_billing_city     = $wps_subscription->get_billing_postcode();

		if ( $wps_subscription_billing_country ) {
			WC()->customer->set_billing_country( $wps_subscription_billing_country );
		}
		if ( $wps_subscription_billing_state ) {
			WC()->customer->set_billing_state( $wps_subscription_billing_state );
		}
		if ( $wps_subscription_billing_postcode ) {
			WC()->customer->set_billing_postcode( $wps_subscription_billing_postcode );
		}
		if ( $wps_subscription_billing_city ) {
			WC()->customer->set_billing_city( $wps_subscription_billing_city );
		}

	}

	/**
	 * This function is used to process payment method form.
	 *
	 * @name wps_sfw_change_payment_method_form
	 * @since    1.0.0
	 */
	public function wps_sfw_change_payment_method_form() {
		if ( ! isset( $_POST['_wps_sfw_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wps_sfw_nonce'] ) ), 'wps_sfw__change_payment_method' ) ) {
			return;
		}

		if ( ! isset( $_POST['wps_change_change_payment'] ) && empty( $_POST['wps_change_change_payment'] ) ) {
			return;
		}
		$subscription_id = absint( $_POST['wps_change_change_payment'] );
		$wps_subscription = wc_get_order( $subscription_id );

		ob_start();
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		if ( $wps_subscription->get_order_key() == $order_key ) {

			$this->wps_sfw_set_customer_address_for_payment( $wps_subscription );
			// Update payment method.
			$new_payment_method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '';
			if ( empty( $new_payment_method ) ) {

				$wps_notice = __( 'Please enable payment method', 'subscriptions-for-woocommerce' );
				wc_add_notice( $wps_notice, 'error' );
				$result_redirect = wc_get_endpoint_url( 'show-subscription', $wps_subscription->get_id(), wc_get_page_permalink( 'myaccount' ) );
				wp_safe_redirect( $result_redirect );
				exit;
			}
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			$available_gateways[ $new_payment_method ]->validate_fields();
			$payment_method_title = $available_gateways[ $new_payment_method ]->get_title();

			if ( wc_notice_count( 'error' ) == 0 ) {

				$result = $available_gateways[ $new_payment_method ]->process_payment( $wps_subscription->get_id(), false, true );

				if ( 'success' == $result['result'] ) {
					$result['redirect'] = wc_get_endpoint_url( 'show-subscription', $wps_subscription->get_id(), wc_get_page_permalink( 'myaccount' ) );
						update_post_meta( $wps_subscription->get_id(), '_payment_method', $new_payment_method );
						update_post_meta( $wps_subscription->get_id(), '_payment_method_title', $payment_method_title );
				}

				if ( 'success' != $result['result'] ) {
					return;
				}
				$wps_subscription->save();

				$wps_notice = __( 'Payment Method Added Successfully', 'subscriptions-for-woocommerce' );
				wc_add_notice( $wps_notice );
				wp_safe_redirect( $result['redirect'] );
				exit;
			}
		}
		ob_get_clean();
	}

	/**
	 * This function is used to process payment method form.
	 *
	 * @name wps_sfw_set_susbcription_total.
	 * @param int    $total total.
	 * @param object $wps_subscription wps_subscription.
	 * @since    1.0.0
	 */
	public function wps_sfw_set_susbcription_total( $total, $wps_subscription ) {

		global $wp;
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		if ( ! empty( $_POST['_wps_sfw_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wps_sfw_nonce'] ) ), 'wps_sfw__change_payment_method' ) && isset( $_POST['wps_change_change_payment'] ) && $wps_subscription->get_order_key() == $order_key && $wps_subscription->get_id() == absint( $_POST['wps_change_change_payment'] ) ) {
			$total = 0;
		} elseif ( isset( $wp->query_vars['order-pay'] ) && wps_sfw_check_valid_subscription( absint( $wp->query_vars['order-pay'] ) ) ) {

			$total = 0;
		}

		return $total;
	}

	/**
	 * This function is used to hide offline payment gateway for subscription product.
	 *
	 * @name wps_sfw_unset_offline_payment_gateway_for_subscription
	 * @param array $available_gateways available_gateways.
	 * @since    1.0.0
	 */
	public function wps_sfw_unset_offline_payment_gateway_for_subscription( $available_gateways ) {
		if ( is_admin() || ! is_checkout() ) {
			return $available_gateways;
		}
		$wps_has_subscription = false;

		foreach ( WC()->cart->get_cart_contents() as $key => $values ) {

			if ( wps_sfw_check_product_is_subscription( $values['data'] ) ) {
				$wps_has_subscription = true;
				break;
			}
		}
		if ( $wps_has_subscription ) {
			if ( isset( $available_gateways ) && ! empty( $available_gateways ) && is_array( $available_gateways ) ) {
				foreach ( $available_gateways as $key => $gateways ) {
					$wps_supported_method = array( 'stripe' );
					// Supported paymnet gateway.
					$wps_payment_method = apply_filters( 'wps_sfw_supported_payment_gateway_for_woocommerce', $wps_supported_method, $key );

					if ( ! in_array( $key, $wps_payment_method ) ) {
						unset( $available_gateways[ $key ] );
					}
				}
			}
		}
		return $available_gateways;
	}

	/**
	 * Register the endpoints on my_account page.
	 *
	 * @name wps_sfw_add_subscription_tab_on_myaccount_page
	 * @since    1.0.0
	 */
	public function wps_sfw_add_subscription_tab_on_myaccount_page() {
		add_rewrite_endpoint( 'wps_subscriptions', EP_PAGES );
		add_rewrite_endpoint( 'show-subscription', EP_PAGES );
		add_rewrite_endpoint( 'wps-add-payment-method', EP_PAGES );
	}

	/**
	 * Register the endpoints on my_account page.
	 *
	 * @name wps_sfw_custom_endpoint_query_vars.
	 * @param array $vars vars.
	 * @since    1.0.0
	 */
	public function wps_sfw_custom_endpoint_query_vars( $vars ) {
		$vars[] = 'wps_subscriptions';
		$vars[] = 'show-subscription';
		$vars[] = 'wps-add-payment-method';
		return $vars;
	}

	/**
	 * This function is used to add Wps susbcriptions Tab in MY ACCOUNT Page
	 *
	 * @name wps_sfw_add_subscription_dashboard_on_myaccount_page
	 * @since 1.0.0
	 * @param array $items items.
	 */
	public function wps_sfw_add_subscription_dashboard_on_myaccount_page( $items ) {

		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		$items['wps_subscriptions'] = __( 'Subscriptions', 'subscriptions-for-woocommerce' );
		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * This function is used to add my account page template
	 *
	 * @name wps_sfw_subscription_dashboard_content.
	 * @param int $wps_current_page current page.
	 * @since 1.0.0
	 */
	public function wps_sfw_subscription_dashboard_content( $wps_current_page = 1 ) {

		$user_id = get_current_user_id();

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wps_subscriptions',
			'post_status' => 'wc-wps_renewal',
			'meta_query' => array(
				array(
					'key'   => 'wps_customer_id',
					'value' => $user_id,
				),
			),

		);
		$wps_subscriptions = get_posts( $args );

		$wps_per_page = get_option( 'posts_per_page', 10 );
		$wps_current_page = empty( $wps_current_page ) ? 1 : absint( $wps_current_page );
		$wps_num_pages = ceil( count( $wps_subscriptions ) / $wps_per_page );
		$subscriptions = array_slice( $wps_subscriptions, ( $wps_current_page - 1 ) * $wps_per_page, $wps_per_page );
		wc_get_template(
			'myaccount/wps-susbcrptions.php',
			array(
				'wps_subscriptions' => $subscriptions,
				'wps_current_page'  => $wps_current_page,
				'wps_num_pages' => $wps_num_pages,
				'paginate'      => true,
			),
			'',
			SUBSCRIPTIONS_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/templates/'
		);
	}

	/**
	 * This function is used to restrict guest user for subscription product.
	 *
	 * @name wps_sfw_subscription_before_checkout_form
	 * @since 1.0.0
	 * @param object $checkout checkout.
	 */
	public function wps_sfw_subscription_before_checkout_form( $checkout = '' ) {

		if ( ! is_user_logged_in() ) {
			if ( $this->wps_sfw_check_cart_has_subscription_product() ) {
				if ( true === $checkout->enable_guest_checkout ) {
					$checkout->enable_guest_checkout = false;
				}
			}
		}
	}

	/**
	 * This function is used to show recurring price on account page.
	 *
	 * @name wps_sfw_display_susbcription_recerring_total_account_page_callback
	 * @since 1.0.0
	 * @param int $subscription_id subscription_id.
	 */
	public function wps_sfw_display_susbcription_recerring_total_account_page_callback( $subscription_id ) {
		$susbcription = wc_get_order( $subscription_id );

		if ( isset( $susbcription ) && ! empty( $susbcription ) ) {
			$price = $susbcription->get_total();
			$wps_curr_args = array(
				'currency' => $susbcription->get_currency(),
			);
		} else {
			$price = get_post_meta( $subscription_id, 'wps_recurring_total', true );
		}

		$wps_curr_args = array();

		$price = wc_price( $price, $wps_curr_args );
		$wps_recurring_number = get_post_meta( $subscription_id, 'wps_sfw_subscription_number', true );
		$wps_recurring_interval = get_post_meta( $subscription_id, 'wps_sfw_subscription_interval', true );
		$wps_price_html = wps_sfw_get_time_interval_for_price( $wps_recurring_number, $wps_recurring_interval );

		/* translators: %s: subscription interval */
		$price .= sprintf( esc_html( ' / %s ' ), $wps_price_html );
		$wps_subscription_status = get_post_meta( $subscription_id, 'wps_subscription_status', true );
		if ( 'cancelled' === $wps_subscription_status ) {
			$price = '---';
		}
		echo wp_kses_post( $price );
	}


	/**
	 * This function is used to include subscription details template on account page.
	 *
	 * @name wps_sfw_shwo_subscription_details
	 * @since 1.0.0
	 * @param int $wps_subscription_id wps_subscription_id.
	 */
	public function wps_sfw_shwo_subscription_details( $wps_subscription_id ) {

		if ( ! wps_sfw_check_valid_subscription( $wps_subscription_id ) ) {
			echo '<div class="woocommerce-error wps_sfw_invalid_subscription">' . esc_html__( 'Not a valid subscription', 'subscriptions-for-woocommerce' ) . '</div>';
			return;
		}

		wc_get_template( 'myaccount/wps-show-subscription-details.php', array( 'wps_subscription_id' => $wps_subscription_id ), '', SUBSCRIPTIONS_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/templates/' );

	}


	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name wps_sfw_cancel_susbcription
	 * @since 1.0.0
	 */
	public function wps_sfw_cancel_susbcription() {

		if ( isset( $_GET['wps_subscription_status'] ) && isset( $_GET['wps_subscription_id'] ) && isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			$user_id      = get_current_user_id();

			$wps_status   = sanitize_text_field( wp_unslash( $_GET['wps_subscription_status'] ) );
			$wps_subscription_id = sanitize_text_field( wp_unslash( $_GET['wps_subscription_id'] ) );
			if ( wps_sfw_check_valid_subscription( $wps_subscription_id ) ) {
				$this->wps_sfw_cancel_susbcription_order_by_customer( $wps_subscription_id, $wps_status, $user_id );
			}
		}
	}

	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name wps_sfw_cancel_susbcription_order_by_customer
	 * @param int    $wps_subscription_id wps_subscription_id.
	 * @param string $wps_status wps_status.
	 * @param int    $user_id user_id.
	 * @since 1.0.0
	 */
	public function wps_sfw_cancel_susbcription_order_by_customer( $wps_subscription_id, $wps_status, $user_id ) {

		$wps_customer_id = get_post_meta( $wps_subscription_id, 'wps_customer_id', true );
		if ( 'active' == $wps_status && $wps_customer_id == $user_id ) {

			do_action( 'wps_sfw_subscription_cancel', $wps_subscription_id, 'Cancel' );

			do_action( 'wps_sfw_cancel_susbcription', $wps_subscription_id, $user_id );
			wc_add_notice( __( 'Subscription Cancelled Successfully', 'subscriptions-for-woocommerce' ), 'success' );
			$redirect_url = wc_get_endpoint_url( 'show-subscription', $wps_subscription_id, wc_get_page_permalink( 'myaccount' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * This function is used to update susbcription.
	 *
	 * @name wps_sfw_woocommerce_order_status_changed
	 * @param int    $order_id order_id.
	 * @param string $old_status old_status.
	 * @param string $new_status new_status.
	 * @since 1.0.0
	 */
	public function wps_sfw_woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {

		$is_activated = get_post_meta( $order_id, 'wps_sfw_subscription_activated', true );

		if ( 'yes' == $is_activated ) {
			return;
		}
		if ( $old_status != $new_status ) {
			if ( 'completed' == $new_status || 'processing' == $new_status ) {
				$wps_has_susbcription = get_post_meta( $order_id, 'wps_sfw_order_has_subscription', true );

				if ( 'yes' == $wps_has_susbcription ) {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wps_subscriptions',
						'post_status'   => 'wc-wps_renewal',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wps_parent_order',
								'value' => $order_id,
							),
							array(
								'key'   => 'wps_subscription_status',
								'value' => 'pending',
							),

						),
					);
					$wps_subscriptions = get_posts( $args );

					if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
						foreach ( $wps_subscriptions as $key => $subscription ) {

							$status = 'active';
							$status = apply_filters( 'wps_sfw_set_subscription_status', $status, $subscription->ID );
							$current_time = apply_filters( 'wps_sfw_subs_curent_time', current_time( 'timestamp' ), $subscription->ID );

							update_post_meta( $subscription->ID, 'wps_subscription_status', $status );
							update_post_meta( $subscription->ID, 'wps_schedule_start', $current_time );

							$wps_susbcription_trial_end = wps_sfw_susbcription_trial_date( $subscription->ID, $current_time );
							update_post_meta( $subscription->ID, 'wps_susbcription_trial_end', $wps_susbcription_trial_end );

							$wps_next_payment_date = wps_sfw_next_payment_date( $subscription->ID, $current_time, $wps_susbcription_trial_end );

							$wps_next_payment_date = apply_filters( 'wps_sfw_next_payment_date', $wps_next_payment_date, $subscription->ID );

							update_post_meta( $subscription->ID, 'wps_next_payment_date', $wps_next_payment_date );

							$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription->ID, $current_time, $wps_susbcription_trial_end );
							$wps_susbcription_end = apply_filters( 'wps_sfw_susbcription_end_date', $wps_susbcription_end, $subscription->ID );
							update_post_meta( $subscription->ID, 'wps_susbcription_end', $wps_susbcription_end );

							// Set billing id.
							$billing_agreement_id = get_post_meta( $order_id, '_ppec_billing_agreement_id', true );
							if ( isset( $billing_agreement_id ) && ! empty( $billing_agreement_id ) ) {
								update_post_meta( $subscription->ID, '_wps_paypal_subscription_id', $billing_agreement_id );
							}
							do_action( 'wps_sfw_order_status_changed', $order_id, $subscription->ID );
						}
						update_post_meta( $order_id, 'wps_sfw_subscription_activated', 'yes' );
					}
				}
			}
		}
	}

	/**
	 * This function is used to set next payment date.
	 *
	 * @name wps_sfw_check_next_payment_date
	 * @param int    $subscription_id subscription_id.
	 * @param string $wps_next_payment_date wps_next_payment_date.
	 * @since 1.0.0
	 */
	public function wps_sfw_check_next_payment_date( $subscription_id, $wps_next_payment_date ) {
		$wps_sfw_subscription_number = get_post_meta( $subscription_id, 'wps_sfw_subscription_number', true );
		$wps_sfw_subscription_expiry_number = get_post_meta( $subscription_id, 'wps_sfw_subscription_expiry_number', true );
		$wps_sfw_subscription_free_trial_number = get_post_meta( $subscription_id, 'wps_sfw_subscription_free_trial_number', true );

		if ( empty( $wps_sfw_subscription_free_trial_number ) ) {
			if ( ! empty( $wps_sfw_subscription_number ) && ! empty( $wps_sfw_subscription_expiry_number ) ) {
				if ( $wps_sfw_subscription_number == $wps_sfw_subscription_expiry_number ) {
					$wps_next_payment_date = 0;
				}
			}
		}
		return $wps_next_payment_date;
	}
	/**
	 * This function is used to set single quantity for susbcription product.
	 *
	 * @name wps_sfw_hide_quantity_fields_for_subscription
	 * @param bool   $return return.
	 * @param object $product product.
	 * @since 1.0.0
	 */
	public function wps_sfw_hide_quantity_fields_for_subscription( $return, $product ) {

		if ( wps_sfw_check_plugin_enable() && wps_sfw_check_product_is_subscription( $product ) ) {
			$return = true;
		}
		return apply_filters( 'wps_sfw_show_quantity_fields_for_susbcriptions', $return, $product );
	}

	/**
	 * This function is used to restrict guest user susbcription product.
	 *
	 * @name wps_sfw_woocommerce_add_to_cart_validation
	 * @param bool $validate validate.
	 * @param int  $product_id product_id.
	 * @param int  $quantity quantity.
	 * @param int  $variation_id as variation_id.
	 * @param bool $variations as variations.
	 * @since 1.0.0
	 */
	public function wps_sfw_woocommerce_add_to_cart_validation( $validate, $product_id, $quantity, $variation_id = 0, $variations = null ) {

		$product = wc_get_product( $product_id );
		if ( is_object( $product ) && 'variable' === $product->get_type() ) {
			$product    = wc_get_product( $variation_id );
			$product_id = $variation_id;
		}
		if ( $this->wps_sfw_check_cart_has_subscription_product() && wps_sfw_check_product_is_subscription( $product ) ) {

			$validate = apply_filters( 'wps_sfw_add_to_cart_validation', false, $product_id, $quantity );

			if ( ! $validate ) {
				wc_add_notice( __( 'You can not add multiple subscription products in cart', 'subscriptions-for-woocommerce' ), 'error' );
			}
		}
		return apply_filters( 'wps_sfw_expiry_add_to_cart_validation', $validate, $product_id, $quantity );
	}

	/**
	 * This function is used to set payment options.
	 *
	 * @name wps_sfw_woocommerce_cart_needs_payment
	 * @param bool   $wps_needs_payment wps_needs_payment.
	 * @param object $cart cart.
	 * @since 1.0.0
	 */
	public function wps_sfw_woocommerce_cart_needs_payment( $wps_needs_payment, $cart ) {
		$wps_is_payment = false;
		$wps_cart_has_subscription = false;
		if ( $wps_needs_payment ) {
			return $wps_needs_payment;
		}

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {

				if ( wps_sfw_check_product_is_subscription( $cart_item['data'] ) ) {
					$wps_cart_has_subscription = true;
					$product_id = $cart_item['data']->get_id();
					$wps_free_trial_length = get_post_meta( $product_id, 'wps_sfw_subscription_free_trial_number', true );
					if ( $wps_free_trial_length > 0 ) {
						$wps_is_payment = true;
						break;
					}
				}
			}
		}
		if ( $wps_is_payment && 0 == $cart->total ) {
			$wps_needs_payment = true;
		} elseif ( $wps_cart_has_subscription && 0 == $cart->total ) {
			$wps_needs_payment = true;
		}

		return apply_filters( 'wps_sfw_needs_payment', $wps_needs_payment, $cart );
	}

	/**
	 * This function is used to update susbcription.
	 *
	 * @name wps_sfw_woocommerce_order_status_changed
	 * @param int    $order_id order_id.
	 * @param string $old_status old_status.
	 * @param string $new_status new_status.
	 * @since 1.0.0
	 */
	public function wps_sfw__cancel_subs_woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {

		if ( $old_status != $new_status ) {

			if ( 'cancelled' === $new_status ) {
				$wps_has_susbcription = get_post_meta( $order_id, 'wps_sfw_order_has_subscription', true );

				if ( 'yes' == $wps_has_susbcription ) {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wps_subscriptions',
						'post_status'   => 'wc-wps_renewal',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wps_parent_order',
								'value' => $order_id,
							),
							array(
								'key'   => 'wps_subscription_status',
								'value' => array( 'active', 'pending' ),
							),
						),
					);
					$wps_subscriptions = get_posts( $args );
					if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
						foreach ( $wps_subscriptions as $key => $subscription ) {
							wps_sfw_send_email_for_cancel_susbcription( $subscription->ID );
							update_post_meta( $subscription->ID, 'wps_subscription_status', 'cancelled' );
						}
					}
				}
			} elseif ( 'failed' === $new_status ) {
				$this->wps_sfw_hold_subscription( $order_id );
			} elseif ( 'completed' == $new_status || 'processing' == $new_status ) {
				$this->wps_sfw_active_after_on_hold( $order_id );
			}
		}
	}

	/**
	 * This function is used to hold the subscription when order failed.
	 *
	 * @param int $order_id order_id.
	 * @return void
	 */
	public function wps_sfw_hold_subscription( $order_id ) {

		$wps_has_susbcription = get_post_meta( $order_id, 'wps_sfw_renewal_order', true );
		if ( 'yes' == $wps_has_susbcription ) {
			$parent_order = get_post_meta( $order_id, 'wps_sfw_parent_order_id', true );
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'wps_subscriptions',
				'post_status'   => 'wc-wps_renewal',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'   => 'wps_parent_order',
						'value' => $parent_order,
					),
					array(
						'key'   => 'wps_subscription_status',
						'value' => array( 'active', 'pending' ),
					),
				),
			);
			$wps_subscriptions = get_posts( $args );
			if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
				foreach ( $wps_subscriptions as $key => $subscription ) {

					update_post_meta( $subscription->ID, 'wps_subscription_status', 'on-hold' );
					do_action( 'wps_sfw_subscription_on_hold_renewal', $subscription->ID );
				}
			}
		} else {
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'wps_subscriptions',
				'post_status'   => 'wc-wps_renewal',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'   => 'wps_parent_order',
						'value' => $order_id,
					),
					array(
						'key'   => 'wps_subscription_status',
						'value' => array( 'active', 'pending' ),
					),
				),
			);

			$wps_subscriptions = get_posts( $args );
			if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
				foreach ( $wps_subscriptions as $key => $subscription ) {
					update_post_meta( $subscription->ID, 'wps_subscription_status', 'on-hold' );
					do_action( 'wps_sfw_subscription_on_hold_renewal', $subscription->ID );
				}
			}
		}

	}

	/**
	 * This function is used to activate subscription after on hold.
	 *
	 * @param int $order_id order_id.
	 * @return void
	 */
	public function wps_sfw_active_after_on_hold( $order_id ) {

		$wps_has_susbcription = get_post_meta( $order_id, 'wps_sfw_renewal_order', true );
		if ( 'yes' == $wps_has_susbcription ) {
			$parent_order = get_post_meta( $order_id, 'wps_sfw_parent_order_id', true );
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'wps_subscriptions',
				'post_status'   => 'wc-wps_renewal',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'   => 'wps_parent_order',
						'value' => $parent_order,
					),
					array(
						'key'   => 'wps_subscription_status',
						'value' => 'on-hold',
					),
				),
			);

			$wps_subscriptions = get_posts( $args );
			if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
				foreach ( $wps_subscriptions as $key => $subscription ) {
					$wps_next_payment_date = wps_sfw_next_payment_date( $subscription->ID, current_time( 'timestamp' ), 0 );
					update_post_meta( $subscription->ID, 'wps_subscription_status', 'active' );
					update_post_meta( $subscription->ID, 'wps_next_payment_date', $wps_next_payment_date );
					do_action( 'wps_sfw_subscription_active_renewal', $subscription->ID );

				}
			}
		}
	}

	/**
	 * Registration required if have subscription products for guest user.
	 *
	 * @param boolean $registration_required .
	 */
	public function wps_sfw_registration_required( $registration_required ) {
		$wps_has_subscription = wps_sfw_is_cart_has_subscription_product();
		if ( $wps_has_subscription && ! $registration_required ) {
			$registration_required = true;
		}
		return $registration_required;
	}

	/**
	 * Show the notice for stripe payment description.
	 *
	 * @param string  $description .
	 * @param integer $gateway_id .
	 */
	public function wps_sfw_change_payment_gateway_description( $description, $gateway_id ) {
		$available_gateways   = WC()->payment_gateways->get_available_payment_gateways();
		$experimental_feature = 'no';
		if ( isset( $available_gateways['stripe'] ) && isset( $available_gateways['stripe']->settings['upe_checkout_experience_enabled'] ) ) {
			$experimental_feature = $available_gateways['stripe']->settings['upe_checkout_experience_enabled'];
		}
		$wps_has_subscription = wps_sfw_is_cart_has_subscription_product();

		if ( 'stripe' === $gateway_id && $wps_has_subscription && 'yes' === $experimental_feature ) {
			$description .= '<i><span class="wps_sfw_experimental_feature_notice">' . esc_html__( 'Only the Card is supported for the recurring payment', 'subscriptions-for-woocommerce' ) . '</span><i><br>';
		}
		return $description;

	}
}
