<?php
if(!defined('ABSPATH')){
    exit;//Exit if accessed directly
}

class AIOWPSecurity_User_Login_Menu extends AIOWPSecurity_Admin_Menu {
    var $menu_page_slug = AIOWPSEC_USER_LOGIN_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs;
    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1', 
        'tab2' => 'render_tab2',
        'tab3' => 'render_tab3',
        'tab4' => 'render_tab4',
        'tab5' => 'render_tab5',
        'additional' => 'render_additional_tab',
        );
    
    /**
    * Class constructor
    */ 
    public function __construct() 
    {
        $this->render_menu_page();
    }
    
    function set_menu_tabs() {
        $this->menu_tabs = array(
        'tab1' => __('Login Lockdown', 'all-in-one-wp-security-and-firewall'),
        'tab2' => __('Failed Login Records', 'all-in-one-wp-security-and-firewall'),
        'tab3' => __('Force Logout', 'all-in-one-wp-security-and-firewall'),
        'tab4' => __('Account Activity Logs', 'all-in-one-wp-security-and-firewall'),
        'tab5' => __('Logged In Users', 'all-in-one-wp-security-and-firewall'),
        'additional' => __('Additional Settings', 'all-in-one-wp-security-and-firewall'),        
        );
    }

    /*
     * Renders our tabs of this menu as nav items
     */
    function render_menu_tabs() {
        $current_tab = $this->get_current_tab();

        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->menu_tabs as $tab_key => $tab_caption ) 
        {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
        }
        echo '</h2>';
    }
    
    /*
     * The menu rendering goes here
     */
    function render_menu_page() {
        echo '<div class="wrap">';
        echo '<h2>'.__('User Login','all-in-one-wp-security-and-firewall').'</h2>';//Interface title
        $this->set_menu_tabs();
        $tab = $this->get_current_tab();
        $this->render_menu_tabs();
        ?>        
        <div id="poststuff"><div id="post-body">
        <?php  
        //$tab_keys = array_keys($this->menu_tabs);
        call_user_func(array($this, $this->menu_tabs_handler[$tab]));
        ?>
        </div></div>
        </div><!-- end of wrap -->
        <?php
    }

	/**
     * Displays the Login Lockdown tab.
     *
	 * @return Void
	 */
    private function render_tab1() {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        include_once 'wp-security-list-locked-ip.php'; //For rendering the AIOWPSecurity_List_Table in tab1
        $locked_ip_list = new AIOWPSecurity_List_Locked_IP(); //For rendering the AIOWPSecurity_List_Table in tab1

        if(isset($_POST['aiowps_login_lockdown']))//Do form submission tasks
        {
            $error = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-login-lockdown-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on login lockdown options save!",4);
                die("Nonce check failed on login lockdown options save!");
            }

            $max_login_attempt_val = sanitize_text_field($_POST['aiowps_max_login_attempts']);
            if (!is_numeric($max_login_attempt_val)) {
                $error .= '<br />'.__('You entered a non-numeric value for the max login attempts field. It has been set to the default value.','all-in-one-wp-security-and-firewall');
                $max_login_attempt_val = '3';//Set it to the default value for this field
            }
            
            $login_retry_time_period = sanitize_text_field($_POST['aiowps_retry_time_period']);
            if(!is_numeric($login_retry_time_period))
            {
                $error .= '<br />'.__('You entered a non numeric value for the login retry time period field. It has been set to the default value.','all-in-one-wp-security-and-firewall');
                $login_retry_time_period = '5';//Set it to the default value for this field
            }

            $lockout_time_length = sanitize_text_field($_POST['aiowps_lockout_time_length']);
            if(!is_numeric($lockout_time_length))
            {
                $error .= '<br />'.__('You entered a non numeric value for the lockout time length field. It has been set to the default value.','all-in-one-wp-security-and-firewall');
                $lockout_time_length = '5'; //Set it to the default value for this field
            }

            $max_lockout_time_length = sanitize_text_field($_POST['aiowps_max_lockout_time_length']);
            if (!is_numeric($max_lockout_time_length)) {
                $error .= '<br />'.__('You entered a non numeric value for the maximim lockout time length field. It has been set to the default value.','all-in-one-wp-security-and-firewall');
                $max_lockout_time_length = '60'; //Set it to the default value for this field
            }
            
            $email_addresses = isset($_POST['aiowps_email_address']) ? stripslashes($_POST['aiowps_email_address']) : get_bloginfo('admin_email');
            $email_addresses_trimmed =  AIOWPSecurity_Utility::explode_trim_filter_empty($email_addresses, "\n");
            // Read into array, sanitize, filter empty and keep only unique usernames.
            $email_address_list
                = array_unique(
                	array_filter(
	                    array_map(
	                    	'sanitize_email',
	                        $email_addresses_trimmed
	                    ),
	                	'is_email'
	                )
				);
			if (isset($_POST['aiowps_enable_email_notify']) && 1 == $_POST['aiowps_enable_email_notify'] && 0 == count($email_addresses_trimmed)) {
                $error .= '<br />' . __('Please fill in one or more email addresses to notify.', 'all-in-one-wp-security-and-firewall');
            } else if (isset($_POST['aiowps_enable_email_notify']) && 1 == $_POST['aiowps_enable_email_notify'] && (0 == count($email_address_list) || count($email_address_list) != count($email_addresses_trimmed))) {
                $error .= '<br />' . __('You have entered one or more invalid email addresses.', 'all-in-one-wp-security-and-firewall');
            }
			if (0 == count($email_address_list)) {
				$error .= ' ' . __('It has been set to your WordPress admin email as default.', 'all-in-one-wp-security-and-firewall');
				$email_address_list[] = get_bloginfo('admin_email');
			}

            // Instantly lockout specific usernames
            $instantly_lockout_specific_usernames = isset($_POST['aiowps_instantly_lockout_specific_usernames']) ? $_POST['aiowps_instantly_lockout_specific_usernames'] : '';
            // Read into array, sanitize, filter empty and keep only unique usernames.
            $instantly_lockout_specific_usernames
                = array_unique(
                    array_filter(
                        array_map(
                            'sanitize_user',
                            AIOWPSecurity_Utility::explode_trim_filter_empty($instantly_lockout_specific_usernames)
                        ),
                        'strlen'
                    )
                )
            ;

            if($error)
            {
                $this->show_msg_error(__('Attention!','all-in-one-wp-security-and-firewall').$error);
            }

            //Save all the form values to the options
			$random_20_digit_string = AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20); // Generate random 20 char string for use during CAPTCHA encode/decode
            $aio_wp_security->configs->set_value('aiowps_unlock_request_secret_key', $random_20_digit_string);
            
            $aio_wp_security->configs->set_value('aiowps_enable_login_lockdown',isset($_POST["aiowps_enable_login_lockdown"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_allow_unlock_requests',isset($_POST["aiowps_allow_unlock_requests"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_max_login_attempts',absint($max_login_attempt_val));
            $aio_wp_security->configs->set_value('aiowps_retry_time_period',absint($login_retry_time_period));
            $aio_wp_security->configs->set_value('aiowps_lockout_time_length',absint($lockout_time_length));
            $aio_wp_security->configs->set_value('aiowps_max_lockout_time_length', absint($max_lockout_time_length));
            $aio_wp_security->configs->set_value('aiowps_set_generic_login_msg',isset($_POST["aiowps_set_generic_login_msg"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_invalid_username_lockdown',isset($_POST["aiowps_enable_invalid_username_lockdown"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_instantly_lockout_specific_usernames', $instantly_lockout_specific_usernames);
            $aio_wp_security->configs->set_value('aiowps_enable_email_notify',isset($_POST["aiowps_enable_email_notify"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_php_backtrace_in_email', isset($_POST['aiowps_enable_php_backtrace_in_email']) ? '1' : '');
            $aio_wp_security->configs->set_value('aiowps_email_address', $email_address_list);
            $aio_wp_security->configs->save_config();
            
            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
            
            $this->show_msg_settings_updated();
        }

        //login lockdown whitelist settings
        $result = 1;
        if (isset($_POST['aiowps_save_lockdown_whitelist_settings']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-lockdown-whitelist-settings-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for save lockdown whitelist settings!",4);
				die(__('Nonce check failed for save lockdown whitelist settings.', 'all-in-one-wp-security-and-firewall'));
            }
            
            if (isset($_POST["aiowps_lockdown_enable_whitelisting"]) && empty($_POST['aiowps_lockdown_allowed_ip_addresses']))
            {
				$this->show_msg_error('You must submit at least one IP address.', 'all-in-one-wp-security-and-firewall');
            }
            else
            {
                if (!empty($_POST['aiowps_lockdown_allowed_ip_addresses']))
                {
                    $ip_addresses = stripslashes($_POST['aiowps_lockdown_allowed_ip_addresses']);
                    $ip_list_array = AIOWPSecurity_Utility_IP::create_ip_list_array_from_string_with_newline($ip_addresses);
                    $payload = AIOWPSecurity_Utility_IP::validate_ip_list($ip_list_array, 'whitelist');
                    if($payload[0] == 1){
                        //success case
                        $result = 1;
                        $list = $payload[1];
                        $allowed_ip_data = implode(PHP_EOL, $list);
                        $aio_wp_security->configs->set_value('aiowps_lockdown_allowed_ip_addresses', $allowed_ip_data);
                        $_POST['aiowps_lockdown_allowed_ip_addresses'] = ''; //Clear the post variable for the allowed address list
                    }
                    else{
                        $result = -1;
                        $error_msg = $payload[1][0];
                        $this->show_msg_error($error_msg);
                    }
                }
                else
                {
                    $aio_wp_security->configs->set_value('aiowps_lockdown_allowed_ip_addresses',''); //Clear the IP address config value
                }

                if ($result == 1)
                {
                    $aio_wp_security->configs->set_value('aiowps_lockdown_enable_whitelisting',isset($_POST["aiowps_lockdown_enable_whitelisting"])?'1':'');
                    $aio_wp_security->configs->save_config(); //Save the configuration
                    
                    $this->show_msg_settings_updated();
                }
            }
        }        
        ?>
		<h2><?php _e('Login lockdown configuration', 'all-in-one-wp-security-and-firewall'); ?></h2>
        <div class="aio_blue_box">
            <?php
            $brute_force_login_feature_link = '<a href="admin.php?page='.AIOWPSEC_BRUTE_FORCE_MENU_SLUG.'&tab=tab2">'.__('Cookie-Based Brute Force Login Prevention', 'all-in-one-wp-security-and-firewall').'</a>';
            echo '<p>'.__('One of the ways hackers try to compromise sites is via a ', 'all-in-one-wp-security-and-firewall').'<strong>'.__('Brute Force Login Attack', 'all-in-one-wp-security-and-firewall').'</strong>. '.__('This is where attackers use repeated login attempts until they guess the password.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('Apart from choosing strong passwords, monitoring and blocking IP addresses which are involved in repeated login failures in a short period of time is a very effective way to stop these types of attacks.', 'all-in-one-wp-security-and-firewall').
            '<p>'.sprintf( esc_html(__('You may also want to checkout our %s feature for another secure way to protect against these types of attacks.', 'all-in-one-wp-security-and-firewall')), $brute_force_login_feature_link).'</p>';
            ?>
        </div>

        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Login lockdown options', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        $aiowps_feature_mgr->output_feature_details_badge("user-login-login-lockdown");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-login-lockdown-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable login lockdown feature', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_login_lockdown" name="aiowps_enable_login_lockdown" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_login_lockdown')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_enable_login_lockdown" class="description"><?php _e('Check this if you want to enable the login lockdown feature and apply the settings below', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>            
            <tr valign="top">
				<th scope="row"><?php _e('Allow unlock requests', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_allow_unlock_requests" name="aiowps_allow_unlock_requests" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_allow_unlock_requests')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_allow_unlock_requests" class="description"><?php _e('Check this if you want to allow users to generate an automated unlock request link which will unlock their account', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>            
			<tr valign="top">
				<th scope="row"><label for="aiowps_max_login_attempts"><?php _e('Max login attempts', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
                <td><input id="aiowps_max_login_attempts" type="text" size="5" name="aiowps_max_login_attempts" value="<?php echo esc_html($aio_wp_security->configs->get_value('aiowps_max_login_attempts')); ?>" />
                <span class="description"><?php _e('Set the value for the maximum login retries before IP address is locked out', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td> 
			</tr>
			<tr valign="top">
				<th scope="row"><label for="aiowps_retry_time_period"><?php _e('Login retry time period (min)', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
                <td><input id="aiowps_retry_time_period" type="text" size="5" name="aiowps_retry_time_period" value="<?php echo esc_html($aio_wp_security->configs->get_value('aiowps_retry_time_period')); ?>" />
                <span class="description"><?php _e('If the maximum number of failed login attempts for a particular IP address occur within this time period the plugin will lock out that address', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td> 
            </tr>
			<tr valign="top">
				<th scope="row">
					<label for="aiowps_lockout_time_length"><?php _e('Minimum lockout time length', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				</th>
				<td>
					<input type="text" size="5" name="aiowps_lockout_time_length" id="aiowps_lockout_time_length" value="<?php echo esc_attr($aio_wp_security->configs->get_value('aiowps_lockout_time_length')); ?>">
					<span class="description">
						<?php
						echo __('Set the minimum time period in minutes of lockout.', 'all-in-one-wp-security-and-firewall').' '.
								__('This failed login lockout time will be tripled on each failed login.', 'all-in-one-wp-security-and-firewall');
						?>
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="aiowps_max_lockout_time_length"><?php _e('Maximum lockout time length', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				</th>
				<td>
					<input type="text" size="5" name="aiowps_max_lockout_time_length" id="aiowps_max_lockout_time_length" value="<?php echo esc_attr($aio_wp_security->configs->get_value('aiowps_max_lockout_time_length')); ?>">
					<span class="description">
						<?php
						echo __('Set the maximum time period in minutes of lockout.', 'all-in-one-wp-security-and-firewall').' '.
							__('No IP address will be blocked for more than this time period after making a failed login attempt.', 'all-in-one-wp-security-and-firewall')
						?>
					</span>
				</td>
            </tr>
            <tr valign="top">
				<th scope="row"><?php _e('Display generic error message', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_set_generic_login_msg" name="aiowps_set_generic_login_msg" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_set_generic_login_msg')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_set_generic_login_msg" class="description"><?php _e('Check this if you want to show a generic error message when a login attempt fails', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
            <tr valign="top">
				<th scope="row"><?php _e('Instantly lockout invalid usernames', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_invalid_username_lockdown" name="aiowps_enable_invalid_username_lockdown" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_invalid_username_lockdown')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_enable_invalid_username_lockdown" class="description"><?php _e('Check this if you want to instantly lockout login attempts with usernames which do not exist on your system', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>            
			<tr valign="top">
				<th scope="row">
					<label for="aiowps_instantly_lockout_specific_usernames"><?php _e('Instantly lockout specific usernames', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				</th>
                <td>
                    <?php
                    $instant_lockout_users_list = $aio_wp_security->configs->get_value('aiowps_instantly_lockout_specific_usernames');
                    if(empty($instant_lockout_users_list)){
                        $instant_lockout_users_list = array();
                    }
                    ?>
                    <textarea id="aiowps_instantly_lockout_specific_usernames" name="aiowps_instantly_lockout_specific_usernames" cols="50" rows="5"><?php echo esc_textarea(implode(PHP_EOL, $instant_lockout_users_list)); ?></textarea><br>
                    <span class="description"><?php _e('Insert one username per line. Existing usernames are not blocked even if present in the list.', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td>
            </tr>
			<tr valign="top">
				<th scope="row">
					<label for="aiowps_email_address"><?php _e('Notify by email', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				</th>
                <td>
                    <input id="aiowps_enable_email_notify" name="aiowps_enable_email_notify" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_email_notify')=='1') echo ' checked="checked"'; ?> value="1"/>
                    <label for="aiowps_enable_email_notify" class="description"><?php _e('Check this if you want to receive an email when someone has been locked out due to maximum failed login attempts', 'all-in-one-wp-security-and-firewall'); ?></span></label>
                    <br />
                    <textarea id="aiowps_email_address" name="aiowps_email_address" cols="50" rows="5"><?php echo esc_textarea(AIOWPSecurity_Utility::get_textarea_str_val($aio_wp_security->configs->get_value('aiowps_email_address'))); ?></textarea><br>
                    <span class="description"><?php _e('Fill in one email address per line.', 'all-in-one-wp-security-and-firewall'); ?></span>
                    <span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More Info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
                    <div class="aiowps_more_info_body">
                            <?php
                            echo '<p class="description">'.__('Each email address must be on a new line.', 'all-in-one-wp-security-and-firewall').'</p>';
                            echo '<p class="description">'.__('If a valid email address has not been filled in, it will not be saved.', 'all-in-one-wp-security-and-firewall').'</p>';
                            echo '<p class="description">'.__('The valid email address format is userid@example.com', 'all-in-one-wp-security-and-firewall').'</p>';
                            echo '<p class="description">'.sprintf(__('Example: %s', 'all-in-one-wp-security-and-firewall'), 'rick@wordpress.org').'</p>';
                            ?>
                    </div>
                </td>
            </tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Enable PHP backtrace in email', 'all-in-one-wp-security-and-firewall'); ?>:
				</th>
                <td>
                    <input name="aiowps_enable_php_backtrace_in_email" id="aiowps_enable_php_backtrace_in_email" type="checkbox"<?php checked($aio_wp_security->configs->get_value('aiowps_enable_php_backtrace_in_email'), '1'); ?> value="1"/>
                    <label for="aiowps_enable_php_backtrace_in_email"><?php _e('Check this if you want to include the PHP backtrace in notification emails.', 'all-in-one-wp-security-and-firewall'); ?> <?php _e('This is internal coding information which makes it easier to investigate where an issued occurred.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
		<input type="submit" name="aiowps_login_lockdown" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Currently locked out IP address ranges', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
            <div class="aio_blue_box aio_width_80">
                <?php
                $locked_ips_link = '<a href="admin.php?page='.AIOWPSEC_MAIN_MENU_SLUG.'&tab=tab2">Locked IP Addresses</a>';
                echo '<p>'.sprintf( __('To see a list of all locked IP addresses and ranges go to the %s tab in the dashboard menu.', 'all-in-one-wp-security-and-firewall'), $locked_ips_link).'</p>';
                ?>
            </div>
        </div></div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Login lockdown IP whitelist settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-lockdown-whitelist-settings-nonce'); ?>            
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><label for="aiowps_lockdown_enable_whitelisting"><?php _e('Enable login lockdown IP whitelist', 'all-in-one-wp-security-and-firewall'); ?></label>:</th>                
                <td>
                <input id="aiowps_lockdown_enable_whitelisting" name="aiowps_lockdown_enable_whitelisting" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_lockdown_enable_whitelisting')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want to enable the whitelisting of selected IP addresses specified in the settings below', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td>
            </tr>            
            <tr valign="top">
				<th scope="row"><label for="aiowps_lockdown_allowed_ip_addresses"><?php _e('Enter whitelisted IP addresses:', 'all-in-one-wp-security-and-firewall'); ?></label></th>
                <td>
                    <textarea id="aiowps_lockdown_allowed_ip_addresses" name="aiowps_lockdown_allowed_ip_addresses" rows="5" cols="50"><?php echo esc_textarea(wp_unslash(-1 == $result ? $_POST['aiowps_lockdown_allowed_ip_addresses'] : $aio_wp_security->configs->get_value('aiowps_lockdown_allowed_ip_addresses'))); ?></textarea>
					<br>
					<span class="description"><?php echo __('Enter one or more IP addresses or IP ranges you wish to include in your whitelist.', 'all-in-one-wp-security-and-firewall') . ' ' . __('The addresses specified here will never be blocked by the login lockdown feature.', 'all-in-one-wp-security-and-firewall'); ?></span>
					<?php $aio_wp_security->include_template('info/ip-address-ip-range-info.php'); ?>
                </td>
            </tr>
        </table>
		<input type="submit" name="aiowps_save_lockdown_whitelist_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>
        
        <?php
    }

	/**
	 * Renders the submenu's tab2 tab body.
	 *
	 * @return Void
	 */
	public function render_tab2() {
        global $aio_wp_security, $wpdb;
        if (isset($_POST['aiowps_delete_failed_login_records']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-delete-failed-login-records-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for delete all failed login records operation!",4);
                die(__('Nonce check failed for delete all failed login records operation!','all-in-one-wp-security-and-firewall'));
            }
            $failed_logins_table = AIOWPSEC_TBL_FAILED_LOGINS;
            //Delete all records from the failed logins table
            $result = $wpdb->query("truncate $failed_logins_table");
                    
            if ($result === FALSE)
            {
                $aio_wp_security->debug_logger->log_debug("User Login Feature - Delete all failed login records operation failed!",4);
                $this->show_msg_error(__('User Login Feature - Delete all failed login records operation failed!','all-in-one-wp-security-and-firewall'));
            } 
            else
            {
                $this->show_msg_updated(__('All records from the Failed Logins table were deleted successfully.','all-in-one-wp-security-and-firewall'));
            }
        }

        include_once 'wp-security-list-login-fails.php'; //For rendering the AIOWPSecurity_List_Table in tab2
        $failed_login_list = new AIOWPSecurity_List_Login_Failed_Attempts(); //For rendering the AIOWPSecurity_List_Table in tab2
        if(isset($_REQUEST['action'])) //Do row action tasks for list table form for failed logins
        {
            if($_REQUEST['action'] == 'delete_failed_login_rec'){ //Delete link was clicked for a row in list table
                $failed_login_list->delete_login_failed_records(strip_tags($_REQUEST['failed_login_id']));
            }
        }

        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This tab displays the failed login attempts for your site.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('The information below can be handy if you need to do security investigations because it will show you the IP range, username and ID (if applicable) and the time/date of the failed login attempt.', 'all-in-one-wp-security-and-firewall').'
            <br /><strong>'.sprintf(__('Failed login records that are older than %1$d days are purged automatically.', 'all-in-one-wp-security-and-firewall'), apply_filters('aiowps_purge_failed_login_records_after_days', AIOWPSEC_PURGE_FAILED_LOGIN_RECORDS_AFTER_DAYS)).'</strong>
            </p>';
            ?>
        </div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Failed login records', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
            <?php 
            //Fetch, prepare, sort, and filter our data...
            $failed_login_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php
            $failed_login_list->search_box(__('Search', 'all-in-one-wp-security-and-firewall'), 'search_failed_login');
            if (isset($_REQUEST["tab"])) {
                echo '<input type="hidden" name="tab" value="' . esc_attr($_REQUEST["tab"]) . '" />';
            }
            ?>
            <!-- Now we can render the completed list table -->
            <?php $failed_login_list->display(); ?>
            </form>
        </div></div>
        <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('Export to CSV', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-export-failed-login-records-to-csv-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
            <span class="description"><?php _e('Click this button if you wish to download this log in CSV format.', 'all-in-one-wp-security-and-firewall'); ?></span>
            </tr>            
        </table>
        <input type="submit" name="aiowps_export_failed_login_records_to_csv" value="<?php _e('Export to CSV', 'all-in-one-wp-security-and-firewall')?>" class="button-primary"/>
        </form>
        </div></div>  
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Delete all failed login records', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-delete-failed-login-records-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
            <span class="description"><?php _e('Click this button if you wish to delete all failed login records in one go.', 'all-in-one-wp-security-and-firewall'); ?></span>
            </tr>            
        </table>
		<input type="submit" name="aiowps_delete_failed_login_records" value="<?php _e('Delete all failed login records', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary" onclick="return confirm('Are you sure you want to delete all records?')">
        </form>
        </div></div>

        <?php
    }

    function render_tab3()
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        
        if(isset($_POST['aiowpsec_save_force_logout_settings']))//Do form submission tasks
        {
            $error = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-force-logout-settings-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on force logout options save!",4);
                die("Nonce check failed on force logout options save!");
            }

            $logout_time_period = sanitize_text_field($_POST['aiowps_logout_time_period']);
            if(!is_numeric($logout_time_period))
            {
                $error .= '<br />'.__('You entered a non numeric value for the logout time period field. It has been set to the default value.','all-in-one-wp-security-and-firewall');
                $logout_time_period = '1';//Set it to the default value for this field
            }
            else
            {
                if($logout_time_period < 1){
                    $logout_time_period = '1';
                }
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','all-in-one-wp-security-and-firewall').$error);
            }

            //Save all the form values to the options
            $aio_wp_security->configs->set_value('aiowps_logout_time_period',absint($logout_time_period));
            $aio_wp_security->configs->set_value('aiowps_enable_forced_logout',isset($_POST["aiowps_enable_forced_logout"])?'1':'');
            $aio_wp_security->configs->save_config();
            
            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
            
            $this->show_msg_settings_updated();
        }
        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Setting an expiry period for your WP administration session is a simple way to protect against unauthorized access to your site from your computer.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('This feature allows you to specify a time period in minutes after which the admin session will expire and the user will be forced to log back in.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Force user logout options', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("user-login-force-logout");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-force-logout-settings-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable force WP user logout', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_forced_logout" name="aiowps_enable_forced_logout" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_forced_logout')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_enable_forced_logout" class="description"><?php _e('Check this if you want to force a wp user to be logged out after a configured amount of time', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>            
            <tr valign="top">
				<th scope="row"><label for="aiowps_logout_time_period"><?php _e('Logout the WP user after XX minutes', 'all-in-one-wp-security-and-firewall'); ?></label>:</th>
                <td><input id="aiowps_logout_time_period" type="text" size="5" name="aiowps_logout_time_period" value="<?php echo $aio_wp_security->configs->get_value('aiowps_logout_time_period'); ?>" />
                <span class="description"><?php _e('(Minutes) The user will be forced to log back in after this time period has elapased.', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td> 
            </tr>
        </table>
		<input type="submit" name="aiowpsec_save_force_logout_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>        
        <?php
    }

	/**
	 * Renders the submenu's tab4 tab body.
	 *
	 * @return Void
	 */
	public function render_tab4() {
        include_once 'wp-security-list-acct-activity.php'; //For rendering the AIOWPSecurity_List_Table in tab4
        $acct_activity_list = new AIOWPSecurity_List_Account_Activity(); //For rendering the AIOWPSecurity_List_Table in tab2
        if(isset($_REQUEST['action'])) //Do row action tasks for list table form for login activity display
        {
            if($_REQUEST['action'] == 'delete_acct_activity_rec'){ //Delete link was clicked for a row in list table
                $acct_activity_list->delete_login_activity_records(strip_tags($_REQUEST['activity_login_rec']));
            }
        }

        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This tab displays the activity for accounts registered with your site that have logged in using the WordPress login form.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('The information below can be handy if you need to do security investigations because it will show you the last 100 recent login events by username, IP address and time/date.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Account activity logs', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
            <?php 
            //Fetch, prepare, sort, and filter our data...
            $acct_activity_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php
            $acct_activity_list->search_box(__('Search', 'all-in-one-wp-security-and-firewall'), 'search_login_activity');
            if (isset($_REQUEST["tab"])) {
                echo '<input type="hidden" name="tab" value="' . esc_attr($_REQUEST["tab"]) . '" />';
            }
            ?>
            <!-- Now we can render the completed list table -->
            <?php $acct_activity_list->display(); ?>
            </form>
        </div></div>
        <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('Export to CSV', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-export-acct-activity-logs-to-csv-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
            <span class="description"><?php _e('Click this button if you wish to download this log in CSV format.', 'all-in-one-wp-security-and-firewall'); ?></span>
            </tr>            
        </table>
        <input type="submit" name="aiowpsec_export_acct_activity_logs_to_csv" value="<?php _e('Export to CSV', 'all-in-one-wp-security-and-firewall')?>" class="button-primary"/>
        </form>
        </div></div>  
        <?php
    }

	/**
	 * Renders the submenu's tab5 tab body.
	 *
	 * @return Void
	 */
	public function render_tab5() {
        global $aio_wp_security;
        $logged_in_users = (is_multisite() ? get_site_transient('users_online') : get_transient('users_online'));
        
        include_once 'wp-security-list-logged-in-users.php'; //For rendering the AIOWPSecurity_List_Table
        $user_list = new AIOWPSecurity_List_Logged_In_Users();
        if(isset($_REQUEST['action'])) //Do row action tasks for list table form for login activity display
        {
            if($_REQUEST['action'] == 'force_user_logout'){ //Force Logout link was clicked for a row in list table
                $user_list->force_user_logout(strip_tags($_REQUEST['logged_in_id']), strip_tags($_REQUEST['ip_address']));
            }
        }
        
        if (isset($_POST['aiowps_refresh_logged_in_user_list'])) {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-logged-in-users-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for users logged in list!",4);
                die(__('Nonce check failed for users logged in list!','all-in-one-wp-security-and-firewall'));
            }
            
            $user_list->prepare_items();
        }

        ?>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Refresh logged in user data', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-logged-in-users-nonce'); ?>
		<input type="submit" name="aiowps_refresh_logged_in_user_list" value="<?php _e('Refresh data', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>
		<div class="aio_blue_box">
            <?php
            echo '<p>'.__('This tab displays all users who are currently logged into your site.', 'all-in-one-wp-security-and-firewall').'
                <br />'.__('If you suspect there is a user or users who are logged in which should not be, you can block them by inspecting the IP addresses from the data below and adding them to your blacklist.', 'all-in-one-wp-security-and-firewall').'
                <br />'.__('You can also instantly log them out by clicking on the "Force Logout" link when you hover over the row in the User Id column.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Currently logged in users', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
            <?php
            //Fetch, prepare, sort, and filter our data...
            $user_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
			<form id="tables-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <input type="hidden" name="tab" value="<?php echo esc_attr($_REQUEST['tab']); ?>" />
            <!-- Now we can render the completed list table -->
            <?php $user_list->display(); ?>
            </form>
        </div></div>
        <?php

    }
    
    /**
	 * Shows additional tab and field for the disable application password and saves on submit.
	 *
	 * @global AIO_WP_Security $aio_wp_security
	 * @global AIOWPSecurity_Feature_Item_Manager $aiowps_feature_mgr
	 * @return void
	 */
    public function render_additional_tab() {
        global $aio_wp_security;
        global $aiowps_feature_mgr;

        if(isset($_POST['aiowpsec_save_additonal_settings'])) {
            if (!wp_verify_nonce($_POST['_wpnonce'], 'aiowpsec-additonal-settings-nonce')) {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on additonal settings save.", 4);
                die("Nonce check failed on additonal settings save.");
            }

            //Save all the form values to the options
            $aio_wp_security->configs->set_value('aiowps_disable_application_password', isset($_POST['aiowps_disable_application_password']) ? '1' : '');
            $aio_wp_security->configs->save_config();

            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();

            $this->show_msg_settings_updated();
        }
        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('WordPress 5.6 introduced a new feature  called "Application Passwords".', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('This  allows you to create a token from the WordPress dashboard which then can be used in the authorization header.', 'all-in-one-wp-security-and-firewall').'
            <br /><br />'.__('This feature allows you to disable Application Passwords as they can leave your site vulnerable to social engineering and  phishing scams.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <form action="" method="POST">
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Additional settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("disable-application-password");
        ?>

        <?php wp_nonce_field('aiowpsec-additonal-settings-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Disable application password', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input name="aiowps_disable_application_password" id="aiowps_disable_application_password" type="checkbox" <?php checked($aio_wp_security->configs->get_value('aiowps_disable_application_password'), '1'); ?> value="1"/>
                <label for="aiowps_disable_application_password"><?php _e('Check this if you want to disable the application password.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
        </div></div>
		<input type="submit" name="aiowpsec_save_additonal_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        <?php
    }

} //end class
