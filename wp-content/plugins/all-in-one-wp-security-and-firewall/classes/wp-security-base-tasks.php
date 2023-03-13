<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

abstract class AIOWPSecurity_Base_Tasks {
	/**
	 * Runs intended various tasks
	 * Handles single and multi-site (NW activation) cases
	 *
	 * @global type $wpdb
	 */
	public static function run() {
		if (is_multisite()) {
			global $wpdb;
			// check if it is a network activation
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);
				static::run_for_a_site();
				restore_current_blog();
			}
		} else {
			static::run_for_a_site();
		}
	}

	/**
	 * Run uninstallation task for a single site.
	 *
	 * @return void
	 */
	abstract protected static function run_for_a_site();
}
