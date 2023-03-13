<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

class AIOWPSecurity_Block_Litespeed extends AIOWPSecurity_Block_Htaccess {

	/**
	 * Get the directives needed for litespeed server
	 *
	 * @return string
	 */
	public function get_contents() {
		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();

		$directives  = "\n# Begin AIOWPSEC Firewall\n";
		$directives .= "\t<IfModule LiteSpeed>\n";
		$directives .= "\t\tphp_value auto_prepend_file '{$bootstrap_path}'\n";
		$directives .= "\t</IfModule>\n";
		$directives .= "\t<IfModule lsapi_module>\n";
		$directives .= "\t\tphp_value auto_prepend_file '{$bootstrap_path}'\n";
		$directives .= "\t</IfModule>\n";
		$directives .= "# End AIOWPSEC Firewall";
		
		return $directives;
	}


}
