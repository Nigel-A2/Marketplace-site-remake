<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

class AIOWPSecurity_Block_Muplugin extends AIOWPSecurity_Block_File {

	/**
	 * Inserts our code into our mu-plugin.
	 *
	 * The mu-plugin and the mu-plugin directory will be created if they don't already exists
	 *
	 * @return boolean|WP_Error
	 */
	public function insert_contents() {
		$info = pathinfo($this->file_path);
	
		if (!isset($info['dirname'])) {
			return new WP_Error(
				'file_no_directory',
				'No directory has been set',
				$this->file_path
			);
		}

		if (false === wp_mkdir_p($info['dirname'])) {
			return new WP_Error(
				'file_no_directory_created',
				'Unable to create the directory',
				$info['dirname']
			);
		}

		return (false !== @file_put_contents($this->file_path, $this->get_contents())); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this
	}

	/**
	 * Checks whether the mu-plugin contents are valid
	 *
	 * @param string $contents
	 * @return boolean
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
	 * The regex pattern that demarcates our contents
	 *
	 * @return string
	 */
	protected function get_regex_pattern() {
		 return '#// Begin AIOWPSEC Firewall(.*)// End AIOWPSEC Firewall#isU';
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
		$code .= "// End AIOWPSEC Firewall\n";

		return $code;
	}
}
