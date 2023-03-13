<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

class AIOWPSecurity_Block_Userini extends AIOWPSecurity_Block_File {

	/**
	 * Inserts our directive into the user.ini file
	 *
	 * @return boolean|WP_Error  true if inserted; false if failed
	 */
	public function insert_contents() {
		$home_path = AIOWPSecurity_Utility_File::get_home_path();

		if (!is_writable($home_path)) {
			return new WP_Error(
				'file_directory_not_writable',
				'The directory has incorrect write permissions. Please double check its permissions and try again.',
				$home_path
			);
		}

		return (false !== @file_put_contents($this->file_path, $this->get_contents(), FILE_APPEND)); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this
	}

	/**
	 * Checks whether the user.ini file contents are valid
	 *
	 * @param string $contents
	 * @return boolean
	 */
	protected function is_content_valid($contents) {
		
		$regex = '/auto_prepend_file=\'(.*)\'/isU';
		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();

		$match = array();
		$result = preg_match($regex, $contents, $match);

		if (empty($match[1]) || false === $result) {
			return false;
		}

		if ($bootstrap_path !== $match[1]) {
			return false;
		}

		return true;

	}

	/**
	 * Our regex pattern that demarcates our contents
	 *
	 * @return string
	 */
	protected function get_regex_pattern() {
		return '/\r?\n# Begin AIOWPSEC Firewall(.*?)# End AIOWPSEC Firewall/is';
	}

	/**
	 * Directives inserted into user.ini
	 *
	 * @return string
	 */
	public function get_contents() {
		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();
		
		$directive  = "\n# Begin AIOWPSEC Firewall\n";
		$directive .= "auto_prepend_file='{$bootstrap_path}'\n";
		$directive .= "# End AIOWPSEC Firewall";

		return $directive;
	}

	/**
	 * Extends the contains_contents function to check for already set directives
	 *
	 * @return boolean|WP_Error
	 */
	public function contains_contents() {
		$contains = parent::contains_contents();

		if (false === $contains) {
			$directive_userini = AIOWPSecurity_Utility_Firewall::get_already_set_directive($this->file_path);
			$directive = AIOWPSecurity_Utility_Firewall::get_already_set_directive();

			if ((AIOWPSecurity_Utility_Firewall::get_bootstrap_path() === $directive) || (AIOWPSecurity_Utility_Firewall::get_bootstrap_path() === $directive_userini)) {
				return true;
			}
		}

		return $contains;
	}
}
