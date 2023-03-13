<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

class AIOWPSecurity_Block_WpConfig extends AIOWPSecurity_Block_File {

	/**
	 * Attempts to insert our code contents into wp-config file
	 *
	 * @return boolean|WP_Error true if success; false if unsuccessful
	 */
	public function insert_contents() {

		if (!is_readable($this->file_path) || !is_writable($this->file_path)) {
			return new WP_Error(
				'file_wrong_permissions',
				'The file has incorrect read or write permissions. Please double check its permissions and try again.',
				$this->file_path
			);
		}

		$wp_config = file($this->file_path, FILE_IGNORE_NEW_LINES);

		if (false === $wp_config) {
			return new WP_Error(
				'file_no_contents',
				'Unable to access the file\'s contents',
				$this->file_path
			);
		}

		array_shift($wp_config);
		array_unshift($wp_config, $this->get_contents());

		return (false !== @file_put_contents($this->file_path, implode(PHP_EOL, $wp_config), LOCK_EX)); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this
	}

	/**
	 * Checks the validity of the content
	 *
	 * @param string $contents - contents we're checking
	 * @return boolean true if content is valid; false if invalid
	 */
	protected function is_content_valid($contents) {
		//The regexes we extract the paths from
		$regexes = array('/file_exists\(\'(.*)\'\)/isU', '/include_once\(\'(.*)\'\)/isU');
		$regex = '';
		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();

		foreach ($regexes as $regex) {
			$matches = array();
			$result  = preg_match($regex, $contents, $matches);

			if (empty($matches[1]) || false === $result) {
				continue;
			}
			
			if ($bootstrap_path !== $matches[1]) {
				return false;
			}
		}

		return true;
	}

	/**
	 * The particular regex that demarcates our contents
	 *
	 * @return string
	 */
	protected function get_regex_pattern() {
		return '#\r?\n// Begin AIOWPSEC Firewall(.*?)// End AIOWPSEC Firewall#is';
	}

	/**
	 * Our firewall code to insert
	 *
	 * @return string
	 */
	public function get_contents() {
		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();
		
		$code  = "<?php\n";
		$code .= "// Begin AIOWPSEC Firewall\n";
		$code .= "if (file_exists('{$bootstrap_path}')) {\n";
		$code .= "\tinclude_once('{$bootstrap_path}');\n";
		$code .= "}\n";
		$code .= "// End AIOWPSEC Firewall";

		return $code;
	}

}
