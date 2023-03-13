<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

abstract class AIOWPSecurity_Block_File {

	/**
	 * Full path to the file we're managing
	 *
	 * @var string
	 */
	protected $file_path;

	/**
	 * Receives the full file path
	 *
	 * @param string $file_path
	 */
	public function __construct($file_path) {
		$this->file_path = $file_path;
	}

	/**
	 * Insert the contents to the managed file
	 *
	 * @return boolean|WP_Error   true if success; false if failed
	 */
	abstract public function insert_contents();

	/**
	 * Returns the contents to be inserted into the managed file
	 *
	 * @return string
	 */
	abstract public function get_contents();

	/**
	 * Returns the regex pattern that separates our contents from others the file may contain
	 *
	 * @return string
	 */
	abstract protected function get_regex_pattern();

	/**
	 * Checks whether the file's contents are valid
	 *
	 * @param string $contents
	 * @return boolean
	 */
	abstract protected function is_content_valid($contents);

	/**
	 * Updates the contents of the managed file
	 *
	 * @return boolean|WP_Error   true if updated; false if not updated
	 */
	public function update_contents() {

		if (!is_readable($this->file_path) || !is_writable($this->file_path)) {
			return new WP_Error(
				'file_wrong_permissions',
				'The file has incorrect read or write permissions. Please double check its permissions and try again.',
				$this->file_path
			);
		}

		$contents = @file_get_contents($this->file_path); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this

		if (false === $contents) {
			return new WP_Error(
				'file_unable_to_read',
				'Unable to read the file. Please double check its permissions and try again.',
				$this->file_path
			);
		}

		$matches = array();
		$match_count = preg_match_all($this->get_regex_pattern(), $contents, $matches);

		if (empty($matches[1]) || false === $match_count) {
			return false;
		}

		//checks whether an update is required
		$requires_update = false;
		$match = '';
		foreach ($matches[1] as $match) {

			$requires_update = !$this->is_content_valid($match);

			if (true === $requires_update) {
				break;
			}
		}

		//perform the update
		if ($requires_update) {

			$block_removed  = $this->remove_contents();
			$block_inserted = $this->insert_contents();

			return (true === $block_removed && true === $block_inserted);
		}

		return false;
	}

	/**
	 * Checks whether the file contains our contents
	 *
	 * @return boolean|WP_Error     true if found; false if not found
	 */
	public function contains_contents() {

		if (!is_readable($this->file_path)) {
			return new WP_Error(
				'file_wrong_permissions',
				'The file has incorrect read permissions. Please double check its permissions and try again.',
				$this->file_path
			);
		}

		$contents = @file_get_contents($this->file_path); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this

		if (false === $contents) {
			return new WP_Error(
				'file_unable_to_read',
				'Unable to read the file. Please double check its permissions and try again.',
				$this->file_path
			);
		}

	   return (1 === preg_match($this->get_regex_pattern(), $contents));
	}

	 /**
	  * Removes our contents from the file
	  *
	  * @return boolean|WP_Error
	  */
	public function remove_contents() {

		if (!is_readable($this->file_path) || !is_writable($this->file_path)) {
			return new WP_Error(
				'file_wrong_permissions',
				'The file has incorrect read or write permissions. Please double check its permissions and try again.',
				$this->file_path
			);
		}

		$contents = @file_get_contents($this->file_path); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this

		if (false === $contents) {
			return new WP_Error(
				'file_unable_to_read',
				'Unable to read the file. Please double check its permissions and try again.',
				$this->file_path
			);
		}

		$removed = 0;
		$contents = preg_replace($this->get_regex_pattern(), "", $contents, -1, $removed);
		
		if (null === $contents) {
			return new WP_Error(
				'file_unable_to_alter',
				'Unable to alter the file.',
				$this->file_path
			);
		}

		if (false === @file_put_contents($this->file_path, $contents, LOCK_EX)) { // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this
			return new WP_Error(
				'file_unable_to_write',
				'Unable to write to the file. Please double check its permissions and try again.',
				$this->file_path
			);
		}
		
		return $removed > 0;
	}

	/**
	 * By default returns the full path to the file being managed
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->file_path;
	}

} //end of class
