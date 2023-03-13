<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Model to handle reviews
 * This checks if the admin has addeda certain number of listings before requesting for a review.
 *
 * @since 5.14.3
 */
class WPBDP_Reviews {

	/**
	 * The obtion name used to check the review status per user.
	 *
	 * @var string
	 */
	private $option_name = 'wpbdp_reviewed';

	/**
	 * The review status
	 *
	 * @var array
	 */
	private $review_status = array();

	private static $instance = null;

	/**
	 * Get the instance of the class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add admin notices as needed for reviews.
	 *
	 * @since 5.14.3
	 */
	public function review_request() {

		// Only show the review request to high-level users on business directory pages.
		if ( ! WPBDP_App_Helper::is_bd_page() ) {
			return;
		}

		add_filter( 'admin_footer_text', array( &$this, 'set_footer_text' ) );

		// Verify that we can do a check for reviews.
		$this->set_review_status();

		// Check if it has been dismissed or if we can ask later.
		$dismissed = $this->review_status['dismissed'];
		$asked     = $this->review_status['asked'];
		if ( 'later' === $dismissed && $asked < 5 ) {
			$dismissed = false;
		}

		$week_ago = ( $this->review_status['time'] + WEEK_IN_SECONDS ) <= time();

		if ( empty( $dismissed ) && $week_ago ) {
			$this->review();
		}
	}

	/**
	 * When was the review request last dismissed?
	 *
	 * @since 5.14.3
	 */
	private function set_review_status() {
		$user_id = get_current_user_id();
		$review  = $this->get_user_meta( $user_id );
		$default = array(
			'time'      => time(),
			'dismissed' => false,
			'asked'     => 0,
		);

		if ( ! $review || ! is_array( $review ) ) {
			// Set the review request to show in a week.
			$this->update_user_meta( $user_id, $default );
		}

		$review              = array_merge( $default, (array) $review );
		$review['asked']     = (int) $review['asked'];
		$this->review_status = $review;
	}

	/**
	 * Maybe show review request.
	 * Include the review html file.
	 *
	 * @since 5.14.3
	 */
	private function review() {

		// show the review request 5 times, depending on the number of entries.
		$show_intervals = array( 25, 50, 100, 200, 500 );
		$asked          = $this->review_status['asked'];

		if ( ! isset( $show_intervals[ $asked ] ) ) {
			return;
		}

		$entries = WPBDP_Listing::count_listings();
		$count   = $show_intervals[ $asked ];
		$user    = wp_get_current_user();

		// Only show review request if the site has collected enough entries.
		if ( $entries < $count ) {
			// check the entry count again in a week.
			$this->review_status['time'] = time();
			$this->update_user_meta( $user->ID, $this->review_status );
			return;
		}

		$entries = $this->calculate_entries( $entries );
		$name    = $user->first_name;

		$title   = sprintf(
			/* translators: %s: User name, %2$d: number of entries */
			esc_html__( 'Congratulations %1$s! You have collected %2$d listings.', 'business-directory-plugin' ),
			esc_html( $name ),
			absint( $entries )
		);

		include WPBDP_PATH . 'includes/admin/views/review.php';
	}

	/**
	 * Save the request to hide the review.
	 *
	 * @since 5.14.3
	 */
	public function dismiss_review() {
		$user_id = get_current_user_id();
		$review  = $this->get_user_meta( $user_id );
		if ( ! $review || ! is_array( $review ) ) {
			$review = array();
		}

		if ( isset( $review['dismissed'] ) && 'done' === $review['dismissed'] ) {
			// if feedback was submitted, don't update it again when the review is dismissed.
			$this->update_user_meta( $user_id, $review );
			wp_die();
		}

		$dismissed           = wpbdp_get_var(
			array(
				'param'   => 'link',
				'default' => 'no',
			),
			'post'
		);

		$review['time']      = time();
		$review['dismissed'] = ( 'done' === $dismissed ) ? true : 'later';
		$review['asked']     = isset( $review['asked'] ) ? $review['asked'] + 1 : 1;

		$this->update_user_meta( $user_id, $review );
		wp_die();
	}

	/**
	 * Update user meta.
	 *
	 * @param int   $user_id The user id.
	 * @param array $review The review.
	 *
	 * @since 5.14.3
	 */
	private function update_user_meta( $user_id, $review ) {
		update_user_meta( $user_id, $this->option_name, $review );
	}

	/**
	 * Get user meta.
	 *
	 * @param int $user_id The user id.
	 *
	 * @since 5.14.3
	 *
	 * @return bool|array
	 */
	private function get_user_meta( $user_id ) {
		return get_user_meta( $user_id, $this->option_name, true );
	}

	/**
	 * Calculate and round off the entries to whole numbers.
	 *
	 * @param int $entries The total number of listings.
	 *
	 * @since 5.14.3
	 *
	 * @return float $entries
	 */
	private function calculate_entries( $entries ) {
		if ( $entries <= 100 ) {
			// round to the nearest 10.
			$entries = floor( $entries / 10 ) * 10;
		} else {
			// round to the nearest 50.
			$entries = floor( $entries / 50 ) * 50;
		}
		return $entries;
	}

	/**
	 * On BD pages, request a review in the footer.
	 *
	 * @since 5.14.3
	 *
	 * @return string
	 */
	public function set_footer_text() {
		return 'Please rate <strong>Business Directory Plugin</strong> <a href="https://wordpress.org/support/plugin/business-directory-plugin/reviews/?filter=5#new-post" target="_blank" rel="noopener">★★★★★ on WordPress.org</a> to help us spread the word. Thank you from our team!';
	}
}
