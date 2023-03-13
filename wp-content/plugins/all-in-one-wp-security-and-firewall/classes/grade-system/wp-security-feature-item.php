<?php

class AIOWPSecurity_Feature_Item {
	
	public $feature_id;//Example "user-accounts-tab1-change-admin-user"

	public $feature_name;

	public $item_points;

	public $security_level;//1, 2 or 3
	
	public $feature_status;//active, inactive, partial
	
	public function __construct($feature_id, $feature_name, $item_points, $security_level) {
		$this->feature_id = $feature_id;
		$this->feature_name = $feature_name;
		$this->item_points = $item_points;
		$this->security_level = $security_level;
	}
	
	public function set_feature_status($status) {
		$this->feature_status = $status;
	}
	
	public function get_security_level_string($level) {
		$level_string = "";
		if ("1" == $level) {
			$level_string = __('Basic', 'all-in-one-wp-security-and-firewall');
		} elseif ("2" == $level) {
			$level_string = __('Intermediate', 'all-in-one-wp-security-and-firewall');
		} elseif ("3" == $level) {
			$level_string = __('Advanced', 'all-in-one-wp-security-and-firewall');
		}
		return $level_string;
	}

}
