<?php
if(!defined('ABSPATH')){
    exit;//Exit if accessed directly
}

class AIOWPSecurity_Brute_Force_Menu extends AIOWPSecurity_Admin_Menu
{
    var $menu_page_slug = AIOWPSEC_BRUTE_FORCE_MENU_SLUG;

    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs;

	/**
     * Tab slugs and respective render functions.
     *
	 * @var string[]
	 */
    private $menu_tabs_handler = array(
        'tab1' => 'render_tab1',
        'tab2' => 'render_tab2',
        'tab3' => 'render_tab3',
        'tab4' => 'render_tab4',
        'tab5' => 'render_tab5',
    );

    public function __construct() {
        $this->render_menu_page();
    }

    function set_menu_tabs()
    {
        $this->menu_tabs = array(
        'tab1' => __('Rename login page','all-in-one-wp-security-and-firewall'),
        'tab2' => __('Cookie Based Brute Force Prevention', 'all-in-one-wp-security-and-firewall'),
		'tab3' => __('Login CAPTCHA', 'all-in-one-wp-security-and-firewall'),
        'tab4' => __('Login whitelist', 'all-in-one-wp-security-and-firewall'),
        'tab5' => __('Honeypot', 'all-in-one-wp-security-and-firewall'),
        );
    }

    /*
     * Renders our tabs of this menu as nav items
     */
    function render_menu_tabs()
    {
        $current_tab = $this->get_current_tab();

        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->menu_tabs as $tab_key => $tab_caption )
        {
			if (is_multisite() && get_current_blog_id() != 1 && stristr($tab_caption, 'Rename login page') === false && stristr($tab_caption, 'Login CAPTCHA') === false) {
				// Suppress the all Brute Force menu tabs if site is a multi site AND not the main site except "rename login" and "CAPTCHA"
			} else {
                $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
                echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
            }
        }
        echo '</h2>';
    }

    /*
     * The menu rendering goes here
     */
    function render_menu_page()
    {
        echo '<div class="wrap">';
        echo '<h2>'.__('Brute Force','all-in-one-wp-security-and-firewall').'</h2>';//Interface title
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

    function render_tab1()
    {
        global $wpdb, $aio_wp_security;
        global $aiowps_feature_mgr;
        $aiowps_login_page_slug = '';

        if (get_option('permalink_structure')){
			$home_url = trailingslashit(home_url());
        }else{
			$home_url = trailingslashit(home_url()) . '?';
        }

        if(isset($_POST['aiowps_save_rename_login_page_settings']))//Do form submission tasks
        {
            $error = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-rename-login-page-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for rename login page save.",4);
                die("Nonce check failed for rename login page save.");
            }

            if (empty($_POST['aiowps_login_page_slug']) && isset($_POST["aiowps_enable_rename_login_page"])){
                $error .= '<br />'.__('Please enter a value for your login page slug.','all-in-one-wp-security-and-firewall');
            }else if (!empty($_POST['aiowps_login_page_slug'])){
                $aiowps_login_page_slug = sanitize_text_field($_POST['aiowps_login_page_slug']);
                if($aiowps_login_page_slug == 'wp-admin'){
                    $error .= '<br />'.__('You cannot use the value "wp-admin" for your login page slug.','all-in-one-wp-security-and-firewall');
                }elseif(preg_match('/[^a-z_\-0-9]/i', $aiowps_login_page_slug)){
                    $error .= '<br />'.__('You must use alpha numeric characters for your login page slug.','all-in-one-wp-security-and-firewall');
                }
            }

            if($error){
                $this->show_msg_error(__('Attention!','all-in-one-wp-security-and-firewall').$error);
            }else{
                $htaccess_res = '';
                $cookie_feature_active = false;
                //Save all the form values to the options
                if (isset($_POST["aiowps_enable_rename_login_page"])){
                    $aio_wp_security->configs->set_value('aiowps_enable_rename_login_page', '1');
                }else{
                    $aio_wp_security->configs->set_value('aiowps_enable_rename_login_page', '');
                }
                $aio_wp_security->configs->set_value('aiowps_login_page_slug',$aiowps_login_page_slug);
                $aio_wp_security->configs->save_config();


                //Recalculate points after the feature status/options have been altered
                $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
                if ($htaccess_res === false) {
                    $this->show_msg_error(__('Could not delete the Cookie-based directives from the .htaccess file. Please check the file permissions.', 'all-in-one-wp-security-and-firewall'));
                }
                else {
                    $this->show_msg_settings_updated();
                }

                /** The following is a fix/workaround for the following issue:
                 * https://wordpress.org/support/topic/applying-brute-force-rename-login-page-not-working/
                 * ie, when saving the rename login config, the logout link does not update on the first page load after the $_POST submit to reflect the new rename login setting.
                 * Added a page refresh to fix this for now until I figure out a better solution.
                 *
                **/
                $cur_url = "admin.php?page=".AIOWPSEC_BRUTE_FORCE_MENU_SLUG."&tab=tab1";
                AIOWPSecurity_Utility::redirect_to_url($cur_url);

            }
        }

        ?>
        <div class="aio_blue_box">
            <?php
            $cookie_based_feature_url = '<a href="admin.php?page='.AIOWPSEC_BRUTE_FORCE_MENU_SLUG.'&tab=tab2" target="_blank">'.__('Cookie Based Brute Force Prevention', 'all-in-one-wp-security-and-firewall').'</a>';
            $white_list_feature_url = '<a href="admin.php?page='.AIOWPSEC_BRUTE_FORCE_MENU_SLUG.'&tab=tab4" target="_blank">'.__('Login Page White List', 'all-in-one-wp-security-and-firewall').'</a>';
            echo '<p>'.__('An effective Brute Force prevention technique is to change the default WordPress login page URL.', 'all-in-one-wp-security-and-firewall').'</p>'.
            '<p>'.__('Normally if you wanted to login to WordPress you would type your site\'s home URL followed by wp-login.php.', 'all-in-one-wp-security-and-firewall').'</p>'.
            '<p>'.__('This feature allows you to change the login URL by setting your own slug and renaming the last portion of the login URL which contains the <strong>wp-login.php</strong> to any string that you like.', 'all-in-one-wp-security-and-firewall').'</p>'.
            '<p>'.__('By doing this, malicious bots and hackers will not be able to access your login page because they will not know the correct login page URL.', 'all-in-one-wp-security-and-firewall').'</p>'.
            '<div class="aio_section_separator_1"></div>'.
            '<p>'.__('You may also be interested in the following alternative brute force prevention features:', 'all-in-one-wp-security-and-firewall').'</p>'.
            '<p>'.$cookie_based_feature_url.'</p>'.
            '<p>'.$white_list_feature_url.'</p>';
            ?>
        </div>
        <?php
        //Show the user the new login URL if this feature is active
        if ($aio_wp_security->configs->get_value('aiowps_enable_rename_login_page')=='1')
        {
        ?>
            <div class="aio_yellow_box">
                <p><?php _e('Your WordPress login page URL has been renamed.', 'all-in-one-wp-security-and-firewall'); ?></p>
                <p><?php _e('Your current login URL is:', 'all-in-one-wp-security-and-firewall'); ?></p>
                <p><strong><?php echo $home_url.$aio_wp_security->configs->get_value('aiowps_login_page_slug'); ?></strong></p>
            </div>

        <?php
        }
        ?>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Rename login page settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("bf-rename-login-page");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-rename-login-page-nonce'); ?>
        <div class="aio_orange_box">
            <?php
            $read_link = '<a href="https://www.tipsandtricks-hq.com/wordpress-security-and-firewall-plugin#advanced_features_note" target="_blank">'.__('must read this message', 'all-in-one-wp-security-and-firewall').'</a>';
            echo '<p>'.sprintf(__('This feature can lock you out of admin if it doesn\'t work correctly on your site. You %s before activating this feature.', 'all-in-one-wp-security-and-firewall'), $read_link).'</p>';
            echo '<p>'.__("NOTE: If you are hosting your site on WPEngine or a provider which performs server caching, you will need to ask the host support people to NOT cache your renamed login page.", "all-in-one-wp-security-and-firewall").'</p>';
            ?>
        </div>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable rename login page feature', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_rename_login_page" name="aiowps_enable_rename_login_page" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_rename_login_page')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_enable_rename_login_page" class="description"><?php _e('Check this if you want to enable the rename login page feature', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
			<tr valign="top">
				<th scope="row"><label for="aiowps_login_page_slug"><?php _e('Login page URL', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
				<td><code><?php echo $home_url; ?></code><input id="aiowps_login_page_slug" type="text" size="15" name="aiowps_login_page_slug" value="<?php echo $aio_wp_security->configs->get_value('aiowps_login_page_slug'); ?>">
                <span class="description"><?php _e('Enter a string which will represent your secure login page slug. You are encouraged to choose something which is hard to guess and only you will remember.', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td>
            </tr>
        </table>
		<input type="submit" name="aiowps_save_rename_login_page_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>

        <?php
    }

	/**
	 * Render content of the cookie based brute force prevention tab.
     *
     * @return Void
	 */
    private function render_tab2() {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        $error = false;
        $msg = '';

        //Save settings for brute force cookie method
        if (isset($_POST['aiowps_apply_cookie_based_bruteforce_firewall'])) {
            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'aiowpsec-enable-cookie-based-brute-force-prevention')) {
                $aio_wp_security->debug_logger->log_debug('Nonce check failed on enable cookie based brute force prevention feature.', 4);
                die('Nonce check failed on enable cookie based brute force prevention feature.');
            }

            if (isset($_POST['aiowps_enable_brute_force_attack_prevention'])) {
                $brute_force_feature_secret_word = sanitize_text_field($_POST['aiowps_brute_force_secret_word']);
                if (empty($brute_force_feature_secret_word)) {
                    $brute_force_feature_secret_word = AIOS_DEFAULT_BRUTE_FORCE_FEATURE_SECRET_WORD;
                } elseif (!ctype_alnum($brute_force_feature_secret_word)) {
                    $msg = '<p>'.__('Settings have not been saved - your secret word must consist only of alphanumeric characters, ie, letters and/or numbers only.', 'all-in-one-wp-security-and-firewall').'</p>';
                    $error = true;
                }

                if (filter_var($_POST['aiowps_cookie_based_brute_force_redirect_url'], FILTER_VALIDATE_URL)) {
                    $aio_wp_security->configs->set_value('aiowps_cookie_based_brute_force_redirect_url', esc_url_raw($_POST['aiowps_cookie_based_brute_force_redirect_url']));
                } else {
                    $aio_wp_security->configs->set_value('aiowps_cookie_based_brute_force_redirect_url', 'http://127.0.0.1');
                }

                if (!$error) {
					$aio_wp_security->configs->set_value('aiowps_enable_brute_force_attack_prevention', '1');
					$aio_wp_security->configs->set_value('aiowps_brute_force_secret_word', $brute_force_feature_secret_word);

                    $msg = '<p>'.__('You have successfully enabled the cookie based brute force prevention feature', 'all-in-one-wp-security-and-firewall').'</p>';
                    $msg .= '<p>'.__('From now on you will need to log into your WP Admin using the following URL:', 'all-in-one-wp-security-and-firewall').'</p>';
                    $msg .= '<p><strong>'.AIOWPSEC_WP_URL.'/?'.$brute_force_feature_secret_word.'=1</strong></p>';
                    $msg .= '<p>'.__('It is important that you save this URL value somewhere in case you forget it, OR,', 'all-in-one-wp-security-and-firewall').'</p>';
                    $msg .= '<p>'.sprintf( __('simply remember to add a "?%s=1" to your current site URL address.', 'all-in-one-wp-security-and-firewall'), $brute_force_feature_secret_word).'</p>';
                }
            } else {
                $aio_wp_security->configs->set_value('aiowps_enable_brute_force_attack_prevention', '');
                $msg = __('You have successfully saved cookie based brute force prevention feature settings.', 'all-in-one-wp-security-and-firewall');
            }

            if (isset($_POST['aiowps_brute_force_attack_prevention_pw_protected_exception'])) {
                $aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_pw_protected_exception', '1');
            } else {
                $aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_pw_protected_exception', '');
            }

            if (isset($_POST['aiowps_brute_force_attack_prevention_ajax_exception'])) {
                $aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_ajax_exception', '1');
            } else {
                $aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_ajax_exception', '');
            }

            if (!$error) {
                $aio_wp_security->configs->save_config();//save the value

                //Recalculate points after the feature status/options have been altered
                $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
                if ('' != $msg) {
	                echo '<div id="message" class="updated fade"><p>';
	                echo $msg;
	                echo '</p></div>';
                }
            } else {
                $this->show_msg_error($msg);
            }
        }
        ?>
		<h2><?php _e('Brute force prevention firewall settings', 'all-in-one-wp-security-and-firewall'); ?></h2>

        <div class="aio_blue_box">
            <?php
            //TODO - need to fix the following message
            echo '<p>'.__('A Brute Force Attack is when a hacker tries many combinations of usernames and passwords until they succeed in guessing the right combination.', 'all-in-one-wp-security-and-firewall').
            '<br />'.__('Due to the fact that at any one time there may be many concurrent login attempts occurring on your site via malicious automated robots, this also has a negative impact on your server\'s memory and performance.', 'all-in-one-wp-security-and-firewall').
            '<br />'.__('The features in this tab will stop the majority of brute force login attacks thus providing even better protection for your WP login page.', 'all-in-one-wp-security-and-firewall').'</p>';
            ?>
        </div>
        <div class="aio_yellow_box">
            <?php
            $backup_tab_link = '<a href="admin.php?page='.AIOWPSEC_SETTINGS_MENU_SLUG.'&tab=tab2" target="_blank">'.__('backup', 'all-in-one-wp-security-and-firewall').'</a>';
            $video_link = '<a href="https://www.tipsandtricks-hq.com/all-in-one-wp-security-plugin-cookie-based-brute-force-login-attack-prevention-feature-5994" target="_blank">'.__('video tutorial', 'all-in-one-wp-security-and-firewall').'</a>';
            $info_msg = sprintf( __('To learn more about how to use this feature, please watch the following %s.', 'all-in-one-wp-security-and-firewall'), $video_link);
            $brute_force_login_feature_link = '<a href="admin.php?page='.AIOWPSEC_FIREWALL_MENU_SLUG.'&tab=tab4" target="_blank">'.__('Cookie-Based Brute Force Login Prevention', 'all-in-one-wp-security-and-firewall').'</a>';
            echo '<p>' . $info_msg . '</p>';
            ?>
		</div>
		<?php
		if (defined('AIOS_DISABLE_COOKIE_BRUTE_FORCE_PREVENTION') && AIOS_DISABLE_COOKIE_BRUTE_FORCE_PREVENTION) {
			$aio_wp_security->include_template('notices/cookie-based-brute-force-prevention-disabled.php');
		}
		?>
		<div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Cookie based brute force login prevention', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("firewall-enable-brute-force-attack-prevention");
        ?>
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-enable-cookie-based-brute-force-prevention'); ?>
        <div class="aio_orange_box">
            <p>
            <?php _e('This feature can lock you out of admin if it doesn\'t work correctly on your site. You <a href="https://www.tipsandtricks-hq.com/wordpress-security-and-firewall-plugin#advanced_features_note" target="_blank">'.__('must read this message', 'all-in-one-wp-security-and-firewall').'</a> before activating this feature.', 'all-in-one-wp-security-and-firewall'); ?>
            </p>
        </div>
        <?php
        $cookie_test_value = $aio_wp_security->configs->get_value('aiowps_cookie_test_success');

        $disable_brute_force_fetaure_input = true;
        // If the cookie test is successful or if the feature is already enabled then go ahead as normal
        if ('1' == $cookie_test_value || '1' == $aio_wp_security->configs->get_value('aiowps_enable_brute_force_attack_prevention')) {
            if (isset($_POST['aiowps_cookie_test'])) {//Cookie test was just performed and the test succeded
                echo '<div class="aio_green_box"><p>';
                _e('The cookie test was successful. You can now enable this feature.', 'all-in-one-wp-security-and-firewall');
                echo '</p></div>';
            }
			$disable_brute_force_fetaure_input = false;
        } else {
            //Cookie test needs to be performed
        	if (isset($_POST['aiowps_cookie_test']) && '1' != $cookie_test_value) {//Test failed
            	echo '<div class="aio_red_box"><p>';
				_e('The cookie test failed on this server. Consequently, this feature cannot be used on this site.', 'all-in-one-wp-security-and-firewall');
				echo '</p></div>';
            }
            ?>
            <div class="aio_yellow_box">
                <p>
                    <?php
                    _e('Before using this feature, you must perform a cookie test first.', 'all-in-one-wp-security-and-firewall');
                    echo ' ';
                    echo htmlspecialchars(__("This ensures that your browser cookie is working correctly and that you won't lock yourself out.", 'all-in-one-wp-security-and-firewall'));
                    ?>
                </p>
            </div>
            <?php
			submit_button(__('Perform cookie test', 'all-in-one-wp-security-and-firewall'), 'primary' , 'aiowps_do_cookie_test_for_bfla');
		}
		$disable_brute_force_sub_fields = !$aio_wp_security->configs->get_value('aiowps_enable_brute_force_attack_prevention');
        ?>
        <table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Enable brute force attack prevention', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_brute_force_attack_prevention" name="aiowps_enable_brute_force_attack_prevention" type="checkbox"<?php checked($aio_wp_security->configs->get_value('aiowps_enable_brute_force_attack_prevention'));?> value="1"<?php disabled($disable_brute_force_fetaure_input); ?>/>
                    <label for="aiowps_enable_brute_force_attack_prevention" class="description"><?php _e('Check this if you want to protect your login page from Brute Force Attack.', 'all-in-one-wp-security-and-firewall'); ?></label>
				<span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
                <div class="aiowps_more_info_body">
                    <p class="description">
                        <?php
                        _e('This feature will deny access to your WordPress login page for all people except those who have a special cookie in their browser.', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        _e('To use this feature do the following:', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        _e('1) Enable the checkbox.', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        _e('2) Enter a secret word consisting of alphanumeric characters which will be difficult to guess. This secret word will be useful whenever you need to know the special URL which you will use to access the login page (see point below).', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        _e('3) You will then be provided with a special login URL. You will need to use this URL to login to your WordPress site instead of the usual login URL. NOTE: The system will deposit a special cookie in your browser which will allow you access to the WordPress administration login page.', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        _e('Any person trying to access your login page who does not have the special cookie in their browser will be automatically blocked.', 'all-in-one-wp-security-and-firewall');
                        ?>
                    </p>
                </div>
                </td>
            </tr>
			<tr valign="top">
				<th scope="row"><label for="aiowps_brute_force_secret_word"><?php _e('Secret word', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
				<td><input id="aiowps_brute_force_secret_word" type="text" size="40" name="aiowps_brute_force_secret_word" value="<?php echo $aio_wp_security->configs->get_value('aiowps_brute_force_secret_word'); ?>"<?php disabled($disable_brute_force_sub_fields); ?>>
                <span class="description"><?php _e('Choose a secret word consisting of alphanumeric characters which you can use to access your special URL. Your are highly encouraged to choose a word which will be difficult to guess.', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="aiowps_cookie_based_brute_force_redirect_url"><?php _e('Re-direct URL', 'all-in-one-wp-security-and-firewall')?>:</label></th>
                <td><input id="aiowps_cookie_based_brute_force_redirect_url" type="text" size="40" name="aiowps_cookie_based_brute_force_redirect_url" value="<?php echo $aio_wp_security->configs->get_value('aiowps_cookie_based_brute_force_redirect_url'); ?>" <?php disabled($disable_brute_force_sub_fields); ?> />
                <span class="description">
                    <?php
                    _e('Specify a URL to redirect a hacker to when they try to access your WordPress login page.', 'all-in-one-wp-security-and-firewall');
                    ?>
                </span>
				<span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
                <div class="aiowps_more_info_body">
                    <p class="description">
                        <?php
                    _e('The URL specified here can be any site\'s URL and does not have to be your own. For example you can be as creative as you like and send hackers to the CIA or NSA home page.', 'all-in-one-wp-security-and-firewall');
                    echo '<br />';
                    _e('This field will default to: http://127.0.0.1 if you do not enter a value.', 'all-in-one-wp-security-and-firewall');
                    echo '<br />';
                    _e('Useful Tip:', 'all-in-one-wp-security-and-firewall');
                    echo '<br />';
                    _e('It\'s a good idea to not redirect attempted brute force login attempts to your site because it increases the load on your server.', 'all-in-one-wp-security-and-firewall');
                    echo '<br />';
                    _e('Redirecting a hacker or malicious bot back to "http://127.0.0.1" is ideal because it deflects them back to their own local host and puts the load on their server instead of yours.', 'all-in-one-wp-security-and-firewall');
                        ?>
                    </p>
                </div>
                </td>
            </tr>
			<tr valign="top">
				<th scope="row"><?php _e('My site has posts or pages which are password protected', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_brute_force_attack_prevention_pw_protected_exception" name="aiowps_brute_force_attack_prevention_pw_protected_exception" type="checkbox"<?php checked($aio_wp_security->configs->get_value('aiowps_brute_force_attack_prevention_pw_protected_exception')); ?> value="1"<?php disabled($disable_brute_force_sub_fields); ?> />
                <label for="aiowps_brute_force_attack_prevention_pw_protected_exception" class="description"><?php _e('Check this if you are using the native WordPress password protection feature for some or all of your blog posts or pages.', 'all-in-one-wp-security-and-firewall'); ?></label>
				<span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
                <div class="aiowps_more_info_body">
                    <p class="description">
                        <?php
                        _e('In the cases where you are protecting some of your posts or pages using the in-built WordPress password protection feature, a few extra lines of directives and exceptions need to be added so that people trying to access pages are not automatically blocked.', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        _e('By enabling this checkbox, the plugin will add the necessary rules and exceptions so that people trying to access these pages are not automatically blocked.', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        echo "<strong>".__('Helpful Tip:', 'all-in-one-wp-security-and-firewall')."</strong>";
                        echo '<br />';
                        _e('If you do not use the WordPress password protection feature for your posts or pages then it is highly recommended that you leave this checkbox disabled.', 'all-in-one-wp-security-and-firewall');
                        ?>
                    </p>
                </div>
                </td>
            </tr>
			<tr valign="top">
				<th scope="row"><?php _e('My site has a theme or plugins which use AJAX', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_brute_force_attack_prevention_ajax_exception" name="aiowps_brute_force_attack_prevention_ajax_exception" type="checkbox"<?php checked($aio_wp_security->configs->get_value('aiowps_brute_force_attack_prevention_ajax_exception')); ?> value="1"<?php disabled($disable_brute_force_sub_fields); ?>/>
                <label for="aiowps_brute_force_attack_prevention_ajax_exception" class="description"><?php _e('Check this if your site uses AJAX functionality.', 'all-in-one-wp-security-and-firewall'); ?></label>
				<span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
                <div class="aiowps_more_info_body">
                    <p class="description">
                        <?php
                        _e('In the cases where your WordPress installation has a theme or plugin that uses AJAX, a few extra lines of directives and exceptions need to be added to prevent AJAX requests from being automatically blocked by the brute force prevention feature.', 'all-in-one-wp-security-and-firewall');
                        echo '<br />';
                        _e('By enabling this checkbox, the plugin will add the necessary rules and exceptions so that AJAX operations will work as expected.', 'all-in-one-wp-security-and-firewall');
                        ?>
                    </p>
                </div>
                </td>
            </tr>
        </table>
		<?php
		$other_attributes = $disable_brute_force_fetaure_input ? array('disabled' => 'disabled') : array();
		submit_button(__('Save feature settings', 'all-in-one-wp-security-and-firewall'), 'primary', 'aiowps_apply_cookie_based_bruteforce_firewall', false, $other_attributes);
		?>
        </form>
        </div></div>
        <?php
    }

    function render_tab3()
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;

		if (isset($_POST['aiowpsec_save_captcha_settings'])) { // Do form submission tasks
			$error = '';
			if (!wp_verify_nonce($_POST['_wpnonce'], 'aiowpsec-captcha-settings-nonce')) {
				$aio_wp_security->debug_logger->log_debug('Nonce check failed on CAPTCHA settings save.', 4);
				die('Nonce check failed on CAPTCHA settings save.');
            }


            //Save all the form values to the options
			$random_20_digit_string = AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20); // Generate random 20 char string for use during CAPTCHA encode/decode
            $aio_wp_security->configs->set_value('aiowps_captcha_secret_key', $random_20_digit_string);
            $aio_wp_security->configs->set_value('aiowps_enable_login_captcha',isset($_POST["aiowps_enable_login_captcha"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_woo_login_captcha',isset($_POST["aiowps_enable_woo_login_captcha"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_woo_register_captcha',isset($_POST["aiowps_enable_woo_register_captcha"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_woo_lostpassword_captcha',isset($_POST["aiowps_enable_woo_lostpassword_captcha"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_custom_login_captcha',isset($_POST["aiowps_enable_custom_login_captcha"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_lost_password_captcha',isset($_POST["aiowps_enable_lost_password_captcha"])?'1':'');

            // if secret key is masked then don't resave it or the site key
            $secret_key = sanitize_text_field($_POST["aiowps_recaptcha_secret_key"]);
            if(strpos($secret_key, '********') === false){
                $aio_wp_security->configs->set_value('aiowps_recaptcha_site_key',sanitize_text_field($_POST["aiowps_recaptcha_site_key"]));
                $aio_wp_security->configs->set_value('aiowps_recaptcha_secret_key',sanitize_text_field($_POST["aiowps_recaptcha_secret_key"]));

				if ($aio_wp_security->google_recaptcha_sitekey_verification(stripslashes($_POST['aiowps_recaptcha_site_key'])) && $aio_wp_security->configs->get_value('aios_is_google_recaptcha_wrong_site_key')) {
                    $aio_wp_security->configs->delete_value('aios_is_google_recaptcha_wrong_site_key');
				}
            }

            $aio_wp_security->configs->set_value('aiowps_default_recaptcha', isset($_POST["aiowps_default_recaptcha"])? '1' : '');//Checkbox
            $aio_wp_security->configs->save_config();

            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();

            $this->show_msg_settings_updated();
        }
        if (0 === $aio_wp_security->configs->get_value('aios_is_google_recaptcha_wrong_site_key')) {
			echo '<div class="notice notice-warning aio_red_box"><p>'.__('Google reCAPTCHA site key is wrong. Please enter the correct reCAPTCHA keys below to use the reCAPTCHA feature.').'</p></div>';
		}

        $secret_key_masked = AIOWPSecurity_Utility::mask_string($aio_wp_security->configs->get_value('aiowps_recaptcha_secret_key'));
        ?>
        <div class="aio_blue_box">
            <?php
            $recaptcha_link = '<a href="https://www.google.com/recaptcha" target="_blank">Google reCAPTCHA v2</a>';
			echo sprintf('<p>'.__('This feature allows you to add a CAPTCHA form on various WordPress login pages and forms.', 'all-in-one-wp-security-and-firewall').
			'<br>'.__('Adding a CAPTCHA form on a login page or form is another effective yet simple "Brute Force" prevention technique.', 'all-in-one-wp-security-and-firewall').
			'<br>'.__('You have the option of using either %s or a plain maths CAPTCHA form.', 'all-in-one-wp-security-and-firewall').
			'<br>'.__('If you enable Google reCAPTCHA the reCAPTCHA widget will be displayed for all forms the CAPTCHA settings below.', 'all-in-one-wp-security-and-firewall').
			'<br>'.__('If Google reCAPTCHA is disabled the simple maths CAPTCHA form will apply and users will need to enter the answer to a simple mathematical question.', 'all-in-one-wp-security-and-firewall').'</p>', $recaptcha_link);
            ?>
        </div>
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-captcha-settings-nonce'); ?>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Google reCAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <div class="aio_orange_box">
            <p>
            <?php
			echo __('By enabling these settings the Google reCAPTCHA v2 widget will be applied by default for all forms with CAPTCHA enabled.', 'all-in-one-wp-security-and-firewall');
            ?>
            </p>
        </div>

        <?php if ($aio_wp_security->is_login_lockdown_by_const()) { ?>
            <div class="aio_blue_box">
                <p>
                <?php
				echo __('reCAPTCHA will not work because you have disabled login lockdown by activating the AIOWPS_DISABLE_LOGIN_LOCKDOWN constant value in a configuration file.', 'all-in-one-wp-security-and-firewall').
				'<br>'.__('To enable it, define the AIOWPS_DISABLE_LOGIN_LOCKDOWN constant value as false or remove it.', 'all-in-one-wp-security-and-firewall');
                ?>
                </p>
            </div>
        <?php } ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Use Google reCAPTCHA as default', 'all-in-one-wp-security-and-firewall')?>:</th>
                <td>
                <input id="aiowps_default_recaptcha" name="aiowps_default_recaptcha" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_default_recaptcha')=='1') echo ' checked="checked"'; ?> value="1"/>
				<label for="aiowps_default_recaptcha" class="description"><?php _e('Check this if you want to default to Google reCAPTCHA for all settings below. (If this is left unchecked, all CAPTCHA forms will revert to the plain maths CAPTCHA)', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
            <tr valign="top">
				<th scope="row"><label for="aiowps_recaptcha_site_key"><?php _e('Site key', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
                <td><input id="aiowps_recaptcha_site_key" type="text" size="50" name="aiowps_recaptcha_site_key" value="<?php echo esc_html( $aio_wp_security->configs->get_value('aiowps_recaptcha_site_key') ); ?>" />
                </td>
            </tr>
			<tr valign="top">
				<th scope="row">
					<label for="aiowps_recaptcha_secret_key"><?php _e('Secret key', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				</th>
				<td>
					<input id="aiowps_recaptcha_secret_key" type="text" size="50" name="aiowps_recaptcha_secret_key" value="<?php echo esc_html($secret_key_masked); ?>">
				</td>
            </tr>
        </table>
        </div></div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Login form CAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("user-login-captcha");
        ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable CAPTCHA on login page', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_login_captcha" name="aiowps_enable_login_captcha" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_login_captcha')=='1') echo ' checked="checked"'; ?> value="1"/>
				<label for="aiowps_enable_login_captcha" class="description"><?php _e('Check this if you want to insert a CAPTCHA form on the login page.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
        </div></div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Lost password form CAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("lost-password-captcha");
        ?>

        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable CAPTCHA on lost password page', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_lost_password_captcha" name="aiowps_enable_lost_password_captcha" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_lost_password_captcha')=='1') echo ' checked="checked"'; ?> value="1"/>
				<label for="aiowps_enable_lost_password_captcha" class="description"><?php _e('Check this if you want to insert a CAPTCHA form on the lost password page.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
        </div></div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Custom login form CAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("custom-login-captcha");
        ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable CAPTCHA on custom login form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_custom_login_captcha" name="aiowps_enable_custom_login_captcha" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_custom_login_captcha')=='1') echo ' checked="checked"'; ?> value="1"/>
				<label for="aiowps_enable_custom_login_captcha" class="description"><?php _e('Check this if you want to insert CAPTCHA on a custom login form generated by the following WP function: wp_login_form()', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
        </div></div>
        <?php
		// Only display WooCommerce CAPTCHA settings if woo is active
        if (AIOWPSecurity_Utility::is_woocommerce_plugin_active()) {
        ?>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('WooCommerce forms CAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("woo-login-captcha");
        ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable CAPTCHA on WooCommerce login form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_woo_login_captcha" name="aiowps_enable_woo_login_captcha" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_woo_login_captcha')=='1') echo ' checked="checked"'; ?> value="1"/>
				<label for="aiowps_enable_woo_login_captcha" class="description"><?php _e('Check this if you want to insert CAPTCHA on a WooCommerce login form.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
            <hr>
        <?php
        $aiowps_feature_mgr->output_feature_details_badge("woo-lostpassword-captcha");
        ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable CAPTCHA on WooCommerce lost password form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_woo_lostpassword_captcha" name="aiowps_enable_woo_lostpassword_captcha" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_woo_lostpassword_captcha')=='1') echo ' checked="checked"'; ?> value="1"/>
				<label for="aiowps_enable_woo_lostpassword_captcha" class="description"><?php _e('Check this if you want to insert CAPTCHA on a WooCommerce lost password form.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
            <hr>
        <?php
        $aiowps_feature_mgr->output_feature_details_badge("woo-register-captcha");
        ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable CAPTCHA on WooCommerce registration form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_woo_register_captcha" name="aiowps_enable_woo_register_captcha" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_woo_register_captcha')=='1') echo ' checked="checked"'; ?> value="1"/>
				<label for="aiowps_enable_woo_register_captcha" class="description"><?php _e('Check this if you want to insert CAPTCHA on a WooCommerce registration form.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
        </div></div>
        <?php
        }
        ?>

		<input type="submit" name="aiowpsec_save_captcha_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        <?php
    }

	/**
     * Render login whitelist tab.
     *
	 * @return Void
	 */
    private function render_tab4() {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        $result = 0;
        $your_ip_address = AIOWPSecurity_Utility_IP::get_user_ip_address();
        if (isset($_POST['aiowps_save_whitelist_settings'])) {
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-whitelist-settings-nonce')) {
                $aio_wp_security->debug_logger->log_debug('Nonce check failed for save whitelist settings.', 4);
                die('Nonce check failed for save whitelist settings.');
            }

            if (isset($_POST["aiowps_enable_whitelisting"]) && empty($_POST['aiowps_allowed_ip_addresses'])) {
                $this->show_msg_error('You must submit at least one IP address!','all-in-one-wp-security-and-firewall');
            } else {
                if (!empty($_POST['aiowps_allowed_ip_addresses'])) {
                    $ip_addresses = $_POST['aiowps_allowed_ip_addresses'];
                    $ip_list_array = AIOWPSecurity_Utility_IP::create_ip_list_array_from_string_with_newline($ip_addresses);
                    $payload = AIOWPSecurity_Utility_IP::validate_ip_list($ip_list_array, 'whitelist');
                    if (1 == $payload[0]) {
                        //success case
                        $result = 1;
                        $list = $payload[1];
                        $whitelist_ip_data = implode(PHP_EOL, $list);
                        $aio_wp_security->configs->set_value('aiowps_allowed_ip_addresses', $whitelist_ip_data);
                        $_POST['aiowps_allowed_ip_addresses'] = ''; //Clear the post variable for the banned address list
                    } else {
                        $result = -1;
                        $error_msg = htmlspecialchars($payload[1][0]);
                        $this->show_msg_error($error_msg);
                    }
                } else {
                    $aio_wp_security->configs->set_value('aiowps_allowed_ip_addresses', ''); //Clear the IP address config value
                }

                if (1 == $result) {
                    $aio_wp_security->configs->set_value('aiowps_enable_whitelisting', isset($_POST["aiowps_enable_whitelisting"]) ? '1' : '');
					if ('1' == $aio_wp_security->configs->get_value('aiowps_is_login_whitelist_disabled_on_upgrade')) {
						$aio_wp_security->configs->delete_value('aiowps_is_login_whitelist_disabled_on_upgrade');
					}
                    $aio_wp_security->configs->save_config(); //Save the configuration

                    //Recalculate points after the feature status/options have been altered
                    $aiowps_feature_mgr->check_feature_status_and_recalculate_points();

                    $this->show_msg_settings_updated();
                }
            }
        }
        ?>
		<h2><?php _e('Login whitelist', 'all-in-one-wp-security-and-firewall'); ?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('The All In One WP Security Whitelist feature gives you the option of only allowing certain IP addresses or ranges to have access to your WordPress login page.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('This feature will deny login access for all IP addresses which are not in your whitelist as configured in the settings below.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('The plugin achieves this by writing the appropriate directives to your .htaccess file.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('By allowing/blocking IP addresses, you are using the most secure first line of defence because login access will only be granted to whitelisted IP addresses and other addresses will be blocked as soon as they try to access your login page.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <div class="aio_yellow_box">
            <?php
            $brute_force_login_feature_link = '<a href="admin.php?page='.AIOWPSEC_BRUTE_FORCE_MENU_SLUG.'&tab=tab2" target="_blank">'.__('Cookie-Based Brute Force Login Prevention', 'all-in-one-wp-security-and-firewall').'</a>';
            $rename_login_feature_link = '<a href="admin.php?page='.AIOWPSEC_BRUTE_FORCE_MENU_SLUG.'&tab=tab1" target="_blank">'.__('Rename login page', 'all-in-one-wp-security-and-firewall').'</a>';
            echo '<p>'.sprintf( __('Attention: If in addition to enabling the white list feature, you also have one of the %s or %s features enabled, <strong>you will still need to use your secret word or special slug in the URL when trying to access your WordPress login page</strong>.', 'all-in-one-wp-security-and-firewall'), $brute_force_login_feature_link, $rename_login_feature_link).'</p>
            <p>'.__('These features are NOT functionally related. Having both of them enabled on your site means you are creating 2 layers of security.', 'all-in-one-wp-security-and-firewall').'</p>';
            ?>
        </div>

		<?php
		if (defined('AIOS_DISABLE_LOGIN_WHITELIST') && AIOS_DISABLE_LOGIN_WHITELIST) {
			$aio_wp_security->include_template('notices/disable-login-whitelist.php');
		}
		?>

        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Login IP whitelist settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("whitelist-manager-ip-login-whitelisting");
        ?>
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-whitelist-settings-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable IP whitelisting', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_whitelisting" name="aiowps_enable_whitelisting" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_whitelisting')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_enable_whitelisting" class="description"><?php _e('Check this if you want to enable the whitelisting of selected IP addresses specified in the settings below', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
            <tr valign="top">
				<th scope="row"><label for="aiowps_user_ip"><?php _e('Your current IP address', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
                <td>
                <input id="aiowps_user_ip" size="40" name="aiowps_user_ip" type="text" value="<?php echo $your_ip_address; ?>" readonly="readonly"/>
                <span class="description"><?php _e('You can copy and paste this address in the text box below if you want to include it in your login whitelist.', 'all-in-one-wp-security-and-firewall'); ?></span>
                </td>
            </tr>
            <tr valign="top">
				<th scope="row"><label for="aiowps_allowed_ip_addresses"><?php _e('Enter whitelisted IP addresses:', 'all-in-one-wp-security-and-firewall'); ?></label></th>
                <td>
                    <textarea id="aiowps_allowed_ip_addresses" name="aiowps_allowed_ip_addresses" rows="5" cols="50"><?php echo esc_textarea(wp_unslash(-1 == $result ? $_POST['aiowps_allowed_ip_addresses'] : $aio_wp_security->configs->get_value('aiowps_allowed_ip_addresses'))); ?></textarea>
					<br>
					<span class="description"><?php echo __('Enter one or more IP addresses or IP ranges you wish to include in your whitelist.', 'all-in-one-wp-security-and-firewall') . ' ' . __('Only the addresses specified here will have access to the WordPress login page.', 'all-in-one-wp-security-and-firewall'); ?></span>
					<?php $aio_wp_security->include_template('info/ip-address-ip-range-info.php'); ?>
                </td>
            </tr>
        </table>
		<input type="submit" name="aiowps_save_whitelist_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>
        <?php
    }

    function render_tab5()
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;

        if(isset($_POST['aiowpsec_save_honeypot_settings']))//Do form submission tasks
        {
            $error = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-honeypot-settings-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on honeypot settings save.",4);
                die("Nonce check failed on honeypot settings save.");
            }

            //Save all the form values to the options
            $aio_wp_security->configs->set_value('aiowps_enable_login_honeypot',isset($_POST["aiowps_enable_login_honeypot"])?'1':'');
            $aio_wp_security->configs->save_config();

            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();

            $this->show_msg_settings_updated();
        }
        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This feature allows you to add a special hidden "honeypot" field on the WordPress login page. This will only be visible to robots and not humans.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('Since robots usually fill in every input field from a login form, they will also submit a value for the special hidden honeypot field.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('The way honeypots work is that a hidden field is placed somewhere inside a form which only robots will submit. If that field contains a value when the form is submitted then a robot has most likely submitted the form and it is consequently dealt with.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('Therefore, if the plugin detects that this field has a value when the login form is submitted, then the robot which is attempting to login to your site will be redirected to its localhost address - http://127.0.0.1.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <form action="" method="POST">
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Login form honeypot settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("login-honeypot");
        ?>

        <?php wp_nonce_field('aiowpsec-honeypot-settings-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e('Enable honeypot on login page', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                <td>
                <input id="aiowps_enable_login_honeypot" name="aiowps_enable_login_honeypot" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_login_honeypot')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_enable_login_honeypot" class="description"><?php _e('Check this if you want to enable the honeypot feature for the login page', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>
        </div></div>

		<input type="submit" name="aiowpsec_save_honeypot_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        <?php
    }
	 
} //end class
