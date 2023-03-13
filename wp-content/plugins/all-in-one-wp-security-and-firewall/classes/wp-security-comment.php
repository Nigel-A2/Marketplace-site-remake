<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

/**
 * Handles Comment related hooks.
 */
class AIOWPSecurity_Comment {

	/**
	 * Class constructor. Add action hooks.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter('pre_comment_user_ip', array($this, 'pre_comment_user_ip'));
		add_action('comment_spam_to_approved', array($this, 'comment_spam_status_change'));
		add_action('comment_spam_to_unapproved', array($this, 'comment_spam_status_change'));
	}

	/**
	 * Set comment user IP for local server setup.
	 *
	 * @param string $comment_user_ip comment user IP.
	 * @return string Comment user IP.
	 */
	public function pre_comment_user_ip($comment_user_ip) {
		if (in_array($comment_user_ip, array('', '127.0.0.1', '::1'))) {
			$comment_user_ip = AIOWPSecurity_Utility_IP::get_external_ip_address();
		}
		return $comment_user_ip;
	}

	/**
	 * Move spam comments to trash.
	 */
	public static function trash_spam_comments() {
		global $aio_wp_security;
		if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_trash_spam_comments') && absint($aio_wp_security->configs->get_value('aiowps_trash_spam_comments_after_days'))) {
			$date_before = absint($aio_wp_security->configs->get_value('aiowps_trash_spam_comments_after_days')).' days ago';
			$comment_ids = get_comments(array(
				'fields' => 'ids',
				'status' => 'spam',
				'date_query' => array(
					array(
						'before' => $date_before,
						'inclusive' => true,
					),
				)
			));
			
			if (!empty($comment_ids)) {
				foreach ($comment_ids as $comment_id) {
					wp_trash_comment($comment_id);
				}
			}
		}
	}

	/**
	 * Delete ip from aiowps_permanent_block table once the comment's spam status changed.
	 *
	 * @param object $comment_data comment object.
	 */
	public function comment_spam_status_change($comment_data) {
		global $wpdb, $aio_wp_security;
		$comment_ip = $comment_data->comment_author_IP;
		$sql = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_author_IP = %s AND comment_approved = 'spam'", $comment_ip);
		$total_spam_comment = $wpdb->get_var($sql);
		$min_comment_before_block = $aio_wp_security->configs->get_value('aiowps_spam_ip_min_comments_block');
		if ($total_spam_comment < $min_comment_before_block) {
			$where = array('blocked_ip' => $comment_ip, 'block_reason' => 'spam');
			$wpdb->delete(AIOWPSEC_TBL_PERM_BLOCK, $where, array('%s'));
		}
	}
}
