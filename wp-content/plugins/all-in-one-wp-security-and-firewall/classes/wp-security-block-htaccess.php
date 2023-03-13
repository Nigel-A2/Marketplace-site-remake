<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

class AIOWPSecurity_Block_Htaccess extends AIOWPSecurity_Block_File {

	/**
	 * Attempts to insert our apache directives into the htaccess file
	 *
	 * @return boolean|WP_Error   true if success; false if failed
	 */
	public function insert_contents() {
		$home_path = AIOWPSecurity_Utility_File::get_home_path();

		if (!is_writable($home_path)) {
			return new WP_Error(
				'file_wrong_permissions',
				'The file has incorrect write permissions. Please double check its permissions and try again.',
				$this->file_path
			);
		}

		return (false !== @file_put_contents($this->file_path, $this->get_contents(), FILE_APPEND)); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this
	}

	/**
	 * Checks whether the file's contents are valid
	 *
	 * @param string $contents
	 * @return boolean
	 */
	protected function is_content_valid($contents) {

		$regex = '/php_value auto_prepend_file \'(.*)\'/isU';
		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();

		$matches = array();

		if (preg_match_all($regex, $contents, $matches)) {
			$match = '';
			foreach ($matches[1] as $match) {

				if ($bootstrap_path !== $match) {
					return false;
				}
			}

		} else {
			return false;
		}

		return true;
	}

	/**
	 * The regex pattern that demarcates our contents
	 *
	 * @return string
	 */
	protected function get_regex_pattern() {
		return '/\r?\n# Begin AIOWPSEC Firewall(.*?)# End AIOWPSEC Firewall/is';
	}

	/**
	 * Our contents; the required apache directives for auto prepending a file
	 *
	 * @return string
	 */
	public function get_contents() {
		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();

		$directives  = "\n# Begin AIOWPSEC Firewall\n";
		$directives .= "\t<IfModule mod_php5.c>\n";
		$directives .= "\t\tphp_value auto_prepend_file '{$bootstrap_path}'\n";
		$directives .= "\t</IfModule>\n";
		$directives .= "\t<IfModule mod_php7.c>\n";
		$directives .= "\t\tphp_value auto_prepend_file '{$bootstrap_path}'\n";
		$directives .= "\t</IfModule>\n";
		$directives .= "\t<IfModule mod_php.c>\n";
		$directives .= "\t\tphp_value auto_prepend_file '{$bootstrap_path}'\n";
		$directives .= "\t</IfModule>\n";
		$directives .= "# End AIOWPSEC Firewall";

		return $directives;
	}


} //end of class
