<?php

if (!defined('ABSPATH')) die('No direct access.');

class AIOWPSecurity_Settings_Menu extends AIOWPSecurity_Admin_Menu {

    private $menu_page_slug = AIOWPSEC_SETTINGS_MENU_SLUG;

    /* Specify all the tabs of this menu in the following array */
    public $menu_tabs;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->render_menu_page();
    }

    /**
     * Sets the menu_tabs class variable
     */
    public function set_menu_tabs() {
        $menu_tabs = array(
                        'tab1' => array(
                            'title' => __('General Settings', 'all-in-one-wp-security-and-firewall'),
                            'render_callback' => array($this, 'render_tab1'),
                        ),
                        'tab2' => array(
                            'title' => '.htaccess '.__('File', 'all-in-one-wp-security-and-firewall'),
                            'render_callback' => array($this, 'render_tab2'),
                        ),
                        'tab3' =>  array(
                            'title' => 'wp-config.php '.__('File', 'all-in-one-wp-security-and-firewall'),
                            'render_callback' => array($this, 'render_tab3'),
                        ),
                        'delete-plugin-settings' =>  array(
                            'title' => __('Delete Plugin Settings', 'all-in-one-wp-security-and-firewall'),
                            'render_callback' => array($this, 'render_delete_plugin_settings_tab'),
                        ),
                        'tab4' =>  array(
                            'title' => __('WP Version Info', 'all-in-one-wp-security-and-firewall'),
                            'render_callback' => array($this, 'render_tab4'),
                        ),
                        'tab5' =>  array(
                            'title' => __('Import/Export', 'all-in-one-wp-security-and-firewall'),
                            'render_callback' => array($this, 'render_tab5'),
                        ),
                );

		if (is_main_site()) {
			$menu_tabs['advanced-settings'] =  array(
                                            'title' => __('Advanced settings', 'all-in-one-wp-security-and-firewall'),
                                            'render_callback' => array($this, 'render_advanced_settings'),
                                        );
        }


		$menu_tabs = apply_filters('aiowpsecurity_setting_tabs', $menu_tabs);
		$this->menu_tabs = array_filter($menu_tabs, array($this, 'should_display_tab'));
    }

    /*
     * Renders our tabs of this menu as nav items
     */
    public function render_menu_tabs() {
        $current_tab = $this->get_current_tab();

        echo '<h2 class="nav-tab-wrapper">';
        foreach ($this->menu_tabs as $tab_key => $tab_info) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . esc_html($tab_info['title']) . '</a>';
        }
        echo '</h2>';
    }

	/**
	 * Decide whether to display the tab for the given tab information.
	 *
	 * @param array $tab_info tab information array cotaining element keys like title, render_callback and display_condition_callback etc..
	 * @return boolean The tab information array contains element keys such as title, render_callback, and display_condition_callback, among others.
	 */
	private function should_display_tab($tab_info) {
		if (!empty($tab_info['display_condition_callback']) && is_callable($tab_info['display_condition_callback'])) {
			return call_user_func($tab_info['display_condition_callback']);
		} else {
			return true;
		}
	}

    /*
     * The menu rendering goes here
     */
    public function render_menu_page() {
        echo '<div class="wrap">';
        echo '<h2>'.__('Settings','all-in-one-wp-security-and-firewall').'</h2>';//Interface title
        $this->set_menu_tabs();
        $tab = $this->get_current_tab();
        $this->render_menu_tabs();
        ?>
        <div id="poststuff"><div id="post-body">
                <?php
                call_user_func($this->menu_tabs[$tab]['render_callback']);
                ?>
            </div></div>
        </div><!-- end of wrap -->
        <?php
    }

    public function render_tab1() {
        global $aio_wp_security;
        if(isset($_POST['aiowpsec_disable_all_features']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-disable-all-features'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on disable all security features!",4);
                die("Nonce check failed on disable all security features!");
            }
            AIOWPSecurity_Configure_Settings::turn_off_all_security_features();
            //Now let's clear the applicable rules from the .htaccess file
            $res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();

            //Now let's revert the disable editing setting in the wp-config.php file if necessary
            $res2 = AIOWPSecurity_Utility::enable_file_edits();

            if ($res)
            {
                $this->show_msg_updated(__('All the security features have been disabled successfully!', 'all-in-one-wp-security-and-firewall'));
            }
            else
            {
                $this->show_msg_error(__('Could not write to the .htaccess file. Please restore your .htaccess file manually using the restore functionality in the ".htaccess File".', 'all-in-one-wp-security-and-firewall'));
            }

            if(!$res2)
            {
                $this->show_msg_error(__('Could not write to the wp-config.php. Please restore your wp-config.php file manually using the restore functionality in the "wp-config.php File".', 'all-in-one-wp-security-and-firewall'));
            }
        }

        if(isset($_POST['aiowpsec_disable_all_firewall_rules']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-disable-all-firewall-rules'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on disable all firewall rules!",4);
                die("Nonce check failed on disable all firewall rules!");
            }
            AIOWPSecurity_Configure_Settings::turn_off_all_firewall_rules();
            //Now let's clear the applicable rules from the .htaccess file
            $res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();

            if ($res)
            {
                $this->show_msg_updated(__('All firewall rules have been disabled successfully!', 'all-in-one-wp-security-and-firewall'));
            }
            else
            {
                $this->show_msg_error(__('Could not write to the .htaccess file. Please restore your .htaccess file manually using the restore functionality in the ".htaccess File".', 'all-in-one-wp-security-and-firewall'));
            }
        }

        if (isset($_POST['aiowps_reset_settings'])) { // Do form submission tasks
            if (!wp_verify_nonce($_POST['_wpnonce'], 'aiowps-reset-settings-nonce')) {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for reset settings.", 4);
                die("Nonce check failed for reset settings.");
            }

            if (!class_exists('AIOWPSecurity_Reset_Settings')) {
                require(AIO_WP_SECURITY_PATH . '/admin/wp-security-reset-settings.php' );
            }
            $reset_option_res = AIOWPSecurity_Reset_Settings::reset_options();
            $delete_htaccess = AIOWPSecurity_Reset_Settings::delete_htaccess();
            $truncate_db_tables = AIOWPSecurity_Reset_Settings::reset_db_tables();

            if (false === $reset_option_res && false === $delete_htaccess) {
                $this->show_msg_error(__('Deletion of aio_wp_security_configs option and .htaccess directives failed.', 'all-in-one-wp-security-and-firewall'));
            } elseif (false === $reset_option_res) {
                $this->show_msg_error(__('Reset of aio_wp_security_configs option failed.', 'all-in-one-wp-security-and-firewall'));
            } elseif (false === $delete_htaccess) {
                $this->show_msg_error(__('Deletion of .htaccess directives failed.', 'all-in-one-wp-security-and-firewall'));
            } else {
                $this->show_msg_updated(__('All settings have been successfully reset.', 'all-in-one-wp-security-and-firewall'));
            }
        }

        if(isset($_POST['aiowps_save_debug_settings']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-save-debug-settings'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on save debug settings!",4);
                die("Nonce check failed on save debug settings!");
            }

            $aio_wp_security->configs->set_value('aiowps_enable_debug',isset($_POST["aiowps_enable_debug"])?'1':'');
            $aio_wp_security->configs->save_config();
            $this->show_msg_settings_updated();
        }

        ?>
        <div class="aio_grey_box">
			<p><?php _e('For information, updates and documentation, please visit the', 'all-in-one-wp-security-and-firewall'); ?> <a href="https://www.tipsandtricks-hq.com/wordpress-security-and-firewall-plugin" target="_blank"><?php echo htmlspecialchars('All In One WP Security & Firewall Plugin'); ?></a> <?php _e('Page', 'all-in-one-wp-security-and-firewall'); ?>.</p>
            <p><a href="https://www.tipsandtricks-hq.com/development-center" target="_blank"><?php _e('Follow us', 'all-in-one-wp-security-and-firewall'); ?></a> <?php _e('on Twitter, Google+ or via Email to stay up to date about the new security features of this plugin.', 'all-in-one-wp-security-and-firewall'); ?></p>
        </div>

        <div class="postbox">
			<h3 class="hndle"><label for="title"><?php _e('WP Security plugin', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
            <div class="inside">
                <p>
                    <?php
					_e('Thank you for using the AIOS security plugin.', 'all-in-one-wp-security-and-firewall');
                    ?>
                    &nbsp;
                    <?php
					_e('There are a lot of security features in this plugin.', 'all-in-one-wp-security-and-firewall');
                    ?>
                </p>
                <p>
                    <?php
                    _e('To start, go through each security option and enable the "basic" options.', 'all-in-one-wp-security-and-firewall');
                    ?>
                    &nbsp;
                    <?php
					_e('The more features you enable, the more security points you will achieve.', 'all-in-one-wp-security-and-firewall');
                    ?>
                </p>
                <p><?php _e('Before doing anything we advise taking a backup of your .htaccess file, database and wp-config.php.', 'all-in-one-wp-security-and-firewall'); ?></p>
                <p>
                <ul class="aiowps_admin_ul_grp1">
                    <li><a href="admin.php?page=aiowpsec_database&tab=tab2" target="_blank"><?php _e('Backup your database', 'all-in-one-wp-security-and-firewall'); ?></a></li>
                    <li><a href="admin.php?page=aiowpsec_settings&tab=tab2" target="_blank"><?php _e('Backup .htaccess file', 'all-in-one-wp-security-and-firewall'); ?></a></li>
                    <li><a href="admin.php?page=aiowpsec_settings&tab=tab3" target="_blank"><?php _e('Backup wp-config.php file', 'all-in-one-wp-security-and-firewall'); ?></a></li>
                </ul>
                </p>
            </div>
        </div> <!-- end postbox-->

        <div class="postbox">
			<h3 class="hndle"><label for="title"><?php _e('Disable security features', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
            <div class="inside">
                <form method="post" action="">
                    <?php wp_nonce_field('aiowpsec-disable-all-features'); ?>
                    <div class="aio_blue_box">
                        <?php
                        echo '<p>'.__('If you think that some plugin functionality on your site is broken due to a security feature you enabled in this plugin, then use the following option to turn off all the security features of this plugin.', 'all-in-one-wp-security-and-firewall').'</p>';
                        ?>
                    </div>
                    <div class="submit">
						<input type="submit" class="button" name="aiowpsec_disable_all_features" value="<?php _e('Disable all security features', 'all-in-one-wp-security-and-firewall'); ?>">
                    </div>
                </form>
            </div>
        </div> <!-- end postbox-->

        <div class="postbox">
			<h3 class="hndle"><label for="title"><?php _e('Disable all firewall rules', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
            <div class="inside">
                <form method="post" action="">
                    <?php wp_nonce_field('aiowpsec-disable-all-firewall-rules'); ?>
                    <div class="aio_blue_box">
                        <?php
                        echo '<p>'.__('This feature will disable all firewall rules which are currently active in this plugin and it will also delete these rules from your .htacess file. Use it if you think one of the firewall rules is causing an issue on your site.', 'all-in-one-wp-security-and-firewall').'</p>';
                        ?>
                    </div>
                    <div class="submit">
						<input type="submit" class="button" name="aiowpsec_disable_all_firewall_rules" value="<?php _e('Disable all firewall rules', 'all-in-one-wp-security-and-firewall'); ?>">
                    </div>
                </form>
            </div>
        </div> <!-- end postbox-->

        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Reset settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form method="post" action="">
        <div class="aio_blue_box">
            <?php
            echo '<p>'.htmlspecialchars(__('This button click will delete all of your settings related to the All In One WP Security & Firewall Plugin.', 'all-in-one-wp-security-and-firewall')).'</p>';
            echo '<p'.__('This button click will reset/empty all the database tables of the security plugin also.', 'all-in-one-wp-security-and-firewall').'</p>';
            echo '<p>'.htmlspecialchars(__('Use this plugin if you were locked out by the All In One WP Security & Firewall Plugin and/or you are having issues logging in when that plugin is activated.', 'all-in-one-wp-security-and-firewall')).'</p>';
			echo '<p>'.htmlspecialchars(__('In addition to the settings it will also delete any directives which were added to the .htaccess file by the All In One WP Security & Firewall Plugin.', 'all-in-one-wp-security-and-firewall')).'</p>';
			echo '<p>'.sprintf(htmlspecialchars(__('%1$sNOTE: %2$sAfter deleting the settings you will need to re-configure the All In One WP Security & Firewall Plugin.', 'all-in-one-wp-security-and-firewall')), '<strong>', '</strong>').'</p>';
            ?>
        </div>
        <div class="submit">
			<input type="submit" name="aiowps_reset_settings" value="<?php _e('Reset settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button">
        </div>
        <?php wp_nonce_field('aiowps-reset-settings-nonce'); ?>
        </form>
        </div>
        </div> <!-- end postbox-->

        <div class="postbox">
			<h3 class="hndle"><label for="title"><?php _e('Debug settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
            <div class="inside">
                <form method="post" action="">
                    <?php wp_nonce_field('aiowpsec-save-debug-settings'); ?>
                    <div class="aio_blue_box">
                        <?php
                        echo '<p>'.__('This setting allows you to enable/disable debug for this plugin.', 'all-in-one-wp-security-and-firewall').'</p>';
                        ?>
                    </div>

                    <table class="form-table">
                        <tr valign="top">
							<th scope="row"><?php _e('Enable debug', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                            <td>
                                <input id="aiowps_enable_debug" name="aiowps_enable_debug" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_debug')=='1') echo ' checked="checked"'; ?> value="1"/>
                                <label for="aiowps_enable_debug" class="description"><?php _e('Check this if you want to enable debug. You should keep this option disabled after you have finished debugging the issue.', 'all-in-one-wp-security-and-firewall'); ?></label>
                            </td>
                        </tr>
                    </table>
					<input type="submit" name="aiowps_save_debug_settings" value="<?php _e('Save debug settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button">
                </form>
            </div>
        </div> <!-- end postbox-->
        <?php
    }

	/**
     * Render tab 2 content.
     *
	 * @return void
	 */
    private function render_tab2() {
        global $aio_wp_security;

        $home_path = AIOWPSecurity_Utility_File::get_home_path();
        $htaccess_path = $home_path . '.htaccess';

        if(isset($_POST['aiowps_save_htaccess']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-save-htaccess-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on htaccess file save!",4);
                die("Nonce check failed on htaccess file save!");
            }

            $result = AIOWPSecurity_Utility_File::backup_and_rename_htaccess($htaccess_path); //Backup the htaccess file

            if ($result)
            {
                $random_prefix = AIOWPSecurity_Utility::generate_alpha_numeric_random_string(10);
                $aiowps_backup_dir = WP_CONTENT_DIR.'/'.AIO_WP_SECURITY_BACKUPS_DIR_NAME;
                if (rename($aiowps_backup_dir.'/'.'.htaccess.backup', $aiowps_backup_dir.'/'.$random_prefix.'_htaccess_backup.txt'))
                {
                    echo '<div id="message" class="updated fade"><p>';
                    _e('Your .htaccess file was successfully backed up! Using an FTP program go to the "/wp-content/aiowps_backups" directory to save a copy of the file to your computer.','all-in-one-wp-security-and-firewall');
                    echo '</p></div>';
                }
                else
                {
                    $aio_wp_security->debug_logger->log_debug("htaccess file rename failed during backup!",4);
                    $this->show_msg_error(__('htaccess file rename failed during backup. Please check your root directory for the backup file using FTP.','all-in-one-wp-security-and-firewall'));
                }
            }
            else
            {
                $aio_wp_security->debug_logger->log_debug("htaccess - Backup operation failed!",4);
                $this->show_msg_error(__('htaccess backup failed.','all-in-one-wp-security-and-firewall'));
            }
        }

        if(isset($_POST['aiowps_restore_htaccess_button']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-restore-htaccess-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on htaccess file restore!",4);
                die("Nonce check failed on htaccess file restore!");
            }

            if (empty($_POST['aiowps_htaccess_file']))
            {
                $this->show_msg_error(__('Please choose a .htaccess to restore from.', 'all-in-one-wp-security-and-firewall'));
            }
            else
            {
                //Let's copy the uploaded .htaccess file into the active root file
                $new_htaccess_file_path = trim($_POST['aiowps_htaccess_file']);
                //TODO
                //Verify that file chosen has contents which are relevant to .htaccess file
                $is_htaccess = AIOWPSecurity_Utility_Htaccess::check_if_htaccess_contents($new_htaccess_file_path);
                if ($is_htaccess == 1)
                {
                    if (!copy($new_htaccess_file_path, $htaccess_path))
                    {
                        //Failed to make a backup copy
                        $aio_wp_security->debug_logger->log_debug("htaccess - Restore from .htaccess operation failed!",4);
                        $this->show_msg_error(__('htaccess file restore failed. Please attempt to restore the .htaccess manually using FTP.','all-in-one-wp-security-and-firewall'));
                    }
                    else
                    {
                        $this->show_msg_updated(__('Your .htaccess file has successfully been restored!', 'all-in-one-wp-security-and-firewall'));
                    }
                }
                else
                {
                    $aio_wp_security->debug_logger->log_debug("htaccess restore failed - Contents of restore file appear invalid!",4);
                    $this->show_msg_error(__('htaccess Restore operation failed! Please check the contents of the file you are trying to restore from.','all-in-one-wp-security-and-firewall'));
                }
            }
        }

        ?>
		<h2><?php _e('.htaccess file operations', 'all-in-one-wp-security-and-firewall'); ?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Your ".htaccess" file is a key component of your website\'s security and it can be modified to implement various levels of protection mechanisms.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('This feature allows you to backup and save your currently active .htaccess file should you need to re-use the the backed up file in the future.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('You can also restore your site\'s .htaccess settings using a backed up .htaccess file.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <?php
        $blog_id = get_current_blog_id();
        if (is_multisite() && !is_main_site( $blog_id ))
        {
            //Hide config settings if MS and not main site
            AIOWPSecurity_Utility::display_multisite_message();
        }
        else
        {
            ?>
            <div class="postbox">
                <h3 class="hndle"><label for="title"><?php _e('Save the current .htaccess file', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
                <div class="inside">
                    <form action="" method="POST">
                        <?php wp_nonce_field('aiowpsec-save-htaccess-nonce'); ?>
                        <p class="description"><?php _e('Click the button below to backup and save the currently active .htaccess file.', 'all-in-one-wp-security-and-firewall'); ?></p>
						<input type="submit" name="aiowps_save_htaccess" value="<?php _e('Backup .htaccess file', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
                    </form>
                </div></div>
            <div class="postbox">
                <h3 class="hndle"><label for="title"><?php _e('Restore from a backed up .htaccess file', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
                <div class="inside">
                    <form action="" method="POST">
                        <?php wp_nonce_field('aiowpsec-restore-htaccess-nonce'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label for="aiowps_htaccess_file_button"><?php _e('.htaccess file to restore from', 'all-in-one-wp-security-and-firewall')?></label>:</th>
                                <td>
                                    <input type="button" id="aiowps_htaccess_file_button" name="aiowps_htaccess_file_button" class="button rbutton" value="<?php _e('Select Your htaccess File', 'all-in-one-wp-security-and-firewall'); ?>" />
                                    <input name="aiowps_htaccess_file" type="text" id="aiowps_htaccess_file" value="" size="80" />
                                    <p class="description">
                                        <?php
                                        _e('After selecting your file, click the button below to restore your site using the backed up htaccess file (htaccess_backup.txt).', 'all-in-one-wp-security-and-firewall');
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
						<input type="submit" name="aiowps_restore_htaccess_button" value="<?php _e('Restore .htaccess file', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
                    </form>
                </div></div>
            <?php
        } // End if statement
    }

    function render_tab3()
    {
        global $aio_wp_security;

        if(isset($_POST['aiowps_restore_wp_config_button']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-restore-wp-config-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on wp-config file restore!",4);
                die("Nonce check failed on wp-config file restore!");
            }

            if (empty($_POST['aiowps_wp_config_file']))
            {
                $this->show_msg_error(__('Please choose a wp-config.php file to restore from.', 'all-in-one-wp-security-and-firewall'));
            }
            else
            {
                //Let's copy the uploaded wp-config.php file into the active root file
                $new_wp_config_file_path = trim($_POST['aiowps_wp_config_file']);

                //Verify that file chosen is a wp-config.file
                $is_wp_config = $this->check_if_wp_config_contents($new_wp_config_file_path);
                if ($is_wp_config == 1)
                {
                    $active_root_wp_config = AIOWPSecurity_Utility_File::get_wp_config_file_path();
                    if (!copy($new_wp_config_file_path, $active_root_wp_config))
                    {
                        //Failed to make a backup copy
                        $aio_wp_security->debug_logger->log_debug("wp-config.php - Restore from backed up wp-config operation failed!",4);
                        $this->show_msg_error(__('wp-config.php file restore failed. Please attempt to restore this file manually using FTP.','all-in-one-wp-security-and-firewall'));
                    }
                    else
                    {
                        $this->show_msg_updated(__('Your wp-config.php file has successfully been restored!', 'all-in-one-wp-security-and-firewall'));
                    }
                }
                else
                {
                    $aio_wp_security->debug_logger->log_debug("wp-config.php restore failed - Contents of restore file appear invalid!",4);
                    $this->show_msg_error(__('wp-config.php Restore operation failed! Please check the contents of the file you are trying to restore from.','all-in-one-wp-security-and-firewall'));
                }
            }
        }

        ?>
		<h2><?php _e('wp-config.php file operations', 'all-in-one-wp-security-and-firewall'); ?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Your "wp-config.php" file is one of the most important in your WordPress installation. It is a primary configuration file and contains crucial things such as details of your database and other critical components.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('This feature allows you to backup and save your currently active wp-config.php file should you need to re-use the the backed up file in the future.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('You can also restore your site\'s wp-config.php settings using a backed up wp-config.php file.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
        <?php
        $blog_id = get_current_blog_id();
        if (is_multisite() && !is_main_site( $blog_id ))
        {
            //Hide config settings if MS and not main site
            AIOWPSecurity_Utility::display_multisite_message();
        }
        else
        {
            ?>
            <div class="postbox">
                <h3 class="hndle"><label for="title"><?php _e('Save the current wp-config.php file', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
                <div class="inside">
                    <form action="" method="POST">
                        <?php wp_nonce_field('aiowpsec-save-wp-config-nonce'); ?>
                        <p class="description"><?php _e('Click the button below to backup and download the contents of the currently active wp-config.php file.', 'all-in-one-wp-security-and-firewall'); ?></p>
						<input type="submit" name="aiowps_save_wp_config" value="<?php _e('Backup wp-config.php file', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">

                    </form>
                </div></div>
            <div class="postbox">
                <h3 class="hndle"><label for="title"><?php _e('Restore from a backed up wp-config file', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
                <div class="inside">
                    <form action="" method="POST">
                        <?php wp_nonce_field('aiowpsec-restore-wp-config-nonce'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label for="aiowps_wp_config_file_button"><?php _e('wp-config file to restore from', 'all-in-one-wp-security-and-firewall')?></label>:</th>
                                <td>
                                    <input type="button" id="aiowps_wp_config_file_button" name="aiowps_wp_config_file_button" class="button rbutton" value="<?php _e('Select Your wp-config File', 'all-in-one-wp-security-and-firewall'); ?>" />
                                    <input name="aiowps_wp_config_file" type="text" id="aiowps_wp_config_file" value="" size="80" />
                                    <p class="description">
                                        <?php
                                        _e('After selecting your file click the button below to restore your site using the backed up wp-config file (wp-config.php.backup.txt).', 'all-in-one-wp-security-and-firewall');
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
						<input type="submit" name="aiowps_restore_wp_config_button" value="<?php _e('Restore wp-config file', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
                    </form>
                </div></div>
            <!--        <div class="postbox">-->
            <!--        <h3 class="hndle"><label for="title">--><?php //_e('View Contents of the currently active wp-config.php file', 'all-in-one-wp-security-and-firewall'); ?><!--</label></h3>-->
            <!--        <div class="inside">-->
            <!--            --><?php
//            $wp_config_file = AIOWPSecurity_Utility_File::get_wp_config_file_path();
//            $wp_config_contents = AIOWPSecurity_Utility_File::get_file_contents($wp_config_file);
//            ?>
            <!--            <textarea class="aio_text_area_file_output aio_width_80 aio_spacer_10_tb" rows="20" readonly>--><?php //echo $wp_config_contents; ?><!--</textarea>-->
            <!--        </div></div>-->

            <?php
        } //End if statement
    }

	public function render_delete_plugin_settings_tab() {
		global $aio_wp_security;

        if (isset($_POST['aiowpsec_save_delete_plugin_settings']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-delete-plugin-settings'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on manage delete plugin settings save.",4);
                die("Nonce check failed on manage delete plugin settings save.");
            }

            //Save settings
            $aio_wp_security->configs->set_value('aiowps_on_uninstall_delete_db_tables', isset($_POST['aiowps_on_uninstall_delete_db_tables']) ? '1' : '');
            $aio_wp_security->configs->set_value('aiowps_on_uninstall_delete_configs', isset($_POST['aiowps_on_uninstall_delete_configs']) ? '1' : '');
            $aio_wp_security->configs->save_config();

            $this->show_msg_updated(__('Manage delete plugin settings saved.', 'all-in-one-wp-security-and-firewall'));

        }
        ?>
        <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('Manage delete plugin tasks', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-delete-plugin-settings'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Delete database tables', 'all-in-one-wp-security-and-firewall')?>:</th>
                <td>
                <input id="aiowps_on_uninstall_delete_db_tables" name="aiowps_on_uninstall_delete_db_tables" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_on_uninstall_delete_db_tables')=='1') echo ' checked="checked"'; ?> value="1"/>
                <label for="aiowps_on_uninstall_delete_db_tables" class="description"><?php _e('Check this if you want to remove database tables when the plugin is uninstalled.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Delete settings', 'all-in-one-wp-security-and-firewall')?>:</th>
                <td>
                    <input id="aiowps_on_uninstall_delete_configs" name="aiowps_on_uninstall_delete_configs" type="checkbox"<?php checked($aio_wp_security->configs->get_value('aiowps_on_uninstall_delete_configs'), '1'); ?> value="1"/>
                    <label for="aiowps_on_uninstall_delete_configs" class="description"><?php echo __('Check this if you want to remove all plugin settings when uninstalling the plugin.', 'all-in-one-wp-security-and-firewall').' '.__('It will also remove all custom htaccess rules that were added by this plugin.', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
        </table>

        <div class="submit">
			<input type="submit" class="button-primary" name="aiowpsec_save_delete_plugin_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>">
        </div>
        </form>
        </div></div>
        <?php
	}

    public function render_tab4() {
        global $aio_wp_security;
        global $aiowps_feature_mgr;

        if(isset($_POST['aiowps_save_remove_wp_meta_info']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-remove-wp-meta-info-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on remove wp meta info options save!",4);
                die("Nonce check failed on remove wp meta info options save!");
            }
            $aio_wp_security->configs->set_value('aiowps_remove_wp_generator_meta_info',isset($_POST["aiowps_remove_wp_generator_meta_info"])?'1':'');
            $aio_wp_security->configs->save_config();

            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();

            $this->show_msg_settings_updated();
    }
        ?>
		<h2><?php _e('WP generator meta tag and version info', 'all-in-one-wp-security-and-firewall'); ?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Wordpress generator automatically adds some meta information inside the "head" tags of every page on your site\'s front end. Below is an example of this:', 'all-in-one-wp-security-and-firewall');
            echo '<br /><strong>&lt;meta name="generator" content="WordPress 3.5.1" /&gt;</strong>';
            echo '<br />'.__('The above meta information shows which version of WordPress your site is currently running and thus can help hackers or crawlers scan your site to see if you have an older version of WordPress or one with a known exploit.', 'all-in-one-wp-security-and-firewall').'
            <br /><br />'.__('There are also other ways wordpress reveals version info such as during style and script loading. An example of this is:', 'all-in-one-wp-security-and-firewall').'
            <br /><strong>&lt;link rel="stylesheet" id="jquery-ui-style-css"  href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css?ver=4.5.2" type="text/css" media="all" /&gt;</strong>
            <br /><br />'.__('This feature will allow you to remove the WP generator meta info and other version info from your site\'s pages.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>

        <div class="postbox">
			<h3 class="hndle"><label for="title"><?php _e('WP generator meta info', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
            <div class="inside">
                <?php
                //Display security info badge
                global $aiowps_feature_mgr;
                $aiowps_feature_mgr->output_feature_details_badge("wp-generator-meta-tag");
                ?>

                <form action="" method="POST">
                    <?php wp_nonce_field('aiowpsec-remove-wp-meta-info-nonce'); ?>
                    <table class="form-table">
                        <tr valign="top">
							<th scope="row"><?php _e('Remove WP generator meta info', 'all-in-one-wp-security-and-firewall'); ?>:</th>
                            <td>
                                <input id="aiowps_remove_wp_generator_meta_info" name="aiowps_remove_wp_generator_meta_info" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_remove_wp_generator_meta_info')=='1') echo ' checked="checked"'; ?> value="1"/>
                                <label for="aiowps_remove_wp_generator_meta_info" class="description"><?php _e('Check this if you want to remove the version and meta info produced by WP from all pages', 'all-in-one-wp-security-and-firewall'); ?></label>
                            </td>
                        </tr>
                    </table>
					<input type="submit" name="aiowps_save_remove_wp_meta_info" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
                </form>
            </div></div>
        <?php
    }

    public function render_tab5() {
        global $aio_wp_security;

        global $wpdb;

        $events_table_name = AIOWPSEC_TBL_EVENTS;
        AIOWPSecurity_Utility::cleanup_table($events_table_name, 500);
        if(isset($_POST['aiowps_import_settings']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-import-settings-nonce'))
            {
				$aio_wp_security->debug_logger->log_debug('Nonce check failed on import AIOS settings.', 4);
				die('Nonce check failed on import AIOS settings.');
            }

            if (empty($_POST['aiowps_import_settings_file']) && empty($_POST['aiowps_import_settings_text']))
            {
                $this->show_msg_error(__('Please choose a file to import your settings from.', 'all-in-one-wp-security-and-firewall'));
            }
            else
            {
                if (empty($_POST['aiowps_import_settings_file'])) {
                    $import_from = "text";
                } else {
                    $import_from = "file";
                }

                if ($import_from == "file") {
                    //Let's get the uploaded import file path
                    $submitted_import_file_path = trim($_POST['aiowps_import_settings_file']);
                    $attachment_id = AIOWPSecurity_Utility_File::get_attachment_id_from_url($submitted_import_file_path); //we'll need this later for deleting

					// Verify that file chosen has valid AIOS settings contents
                    $aiowps_settings_file_contents = $this->check_if_valid_aiowps_settings_file($submitted_import_file_path);
                } else {
                    //Get the string right from the textarea. Still confirm it's in the expected format.
                    $aiowps_settings_file_contents = $this->check_if_valid_aiowps_settings_text($_POST['aiowps_import_settings_text']);
                }

                if ($aiowps_settings_file_contents != -1)
                {
                    //Apply the settings and delete the file (if applicable)
                    $settings_array = json_decode($aiowps_settings_file_contents, true);
                    $aiowps_settings_applied = update_option('aio_wp_security_configs', $settings_array);

                    if (!$aiowps_settings_applied)
                    {
                        //Failed to import settings
						$aio_wp_security->debug_logger->log_debug('Import AIOS settings from ' . $import_from . ' operation failed.', 4);
						$this->show_msg_error(__('Import AIOS settings from ' . $import_from . ' operation failed!', 'all-in-one-wp-security-and-firewall'));

                        if ($import_from == "file") {
                            //Delete the uploaded settings file for security purposes
                            wp_delete_attachment( $attachment_id, true );
                            if ( false === wp_delete_attachment( $attachment_id, true ) ){
                                $this->show_msg_error(__('The deletion of the import file failed. Please delete this file manually via the media menu for security purposes.', 'all-in-one-wp-security-and-firewall'));
                            }else{
                                $this->show_msg_updated(__('The file you uploaded was also deleted for security purposes because it contains security settings details.', 'all-in-one-wp-security-and-firewall'));
                            }
                        }
                    }
                    else
                    {
                        $aio_wp_security->configs->configs = $settings_array; //Refresh the configs global variable

                        //Just in case user submits partial config settings
                        //Run add_option_values to make sure any missing config items are at least set to default
                        AIOWPSecurity_Configure_Settings::add_option_values();
                        if ($import_from == "file") {
                            //Delete the uploaded settings file for security purposes
                            wp_delete_attachment( $attachment_id, true );
                            if ( false === wp_delete_attachment( $attachment_id, true ) ){
								$this->show_msg_updated(__('Your AIOS settings were successfully imported via file input.', 'all-in-one-wp-security-and-firewall'));
                                $this->show_msg_error(__('The deletion of the import file failed. Please delete this file manually via the media menu for security purposes because it contains security settings details.', 'all-in-one-wp-security-and-firewall'));
                            }else{
								$this->show_msg_updated(__('Your AIOS settings were successfully imported. The file you uploaded was also deleted for security purposes because it contains security settings details.', 'all-in-one-wp-security-and-firewall'));
                            }
                        } else {
							$this->show_msg_updated(__('Your AIOS settings were successfully imported via text entry.', 'all-in-one-wp-security-and-firewall'));
                        }
                        //Now let's refresh the .htaccess file with any modified rules if applicable
                        $res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();

                        if( !$res )
                        {
                            $this->show_msg_error(__('Could not write to the .htaccess file. Please check the file permissions.', 'all-in-one-wp-security-and-firewall'));
                        }
                    }
                }
                else
                {
                    //Invalid settings file
                    $aio_wp_security->debug_logger->log_debug("The contents of your settings file appear invalid!",4);
                    $this->show_msg_error(__('The contents of your settings file appear invalid. Please check the contents of the file you are trying to import settings from.','all-in-one-wp-security-and-firewall'));

                    if ($import_from == "file") {
                        //Let's also delete the uploaded settings file for security purposes
                        wp_delete_attachment( $attachment_id, true );
                        if ( false === wp_delete_attachment( $attachment_id, true ) ){
                            $this->show_msg_error(__('The deletion of the import file failed. Please delete this file manually via the media menu for security purposes.', 'all-in-one-wp-security-and-firewall'));
                        }else{
                            $this->show_msg_updated(__('The file you uploaded was also deleted for security purposes because it contains security settings details.', 'all-in-one-wp-security-and-firewall'));
                        }
                    }

                }
            }
        }

        ?>
		<h2><?php _e('Export or import your AIOS settings', 'all-in-one-wp-security-and-firewall'); ?></h2>
        <div class="aio_blue_box">
            <?php
			echo '<p>'.htmlspecialchars(__('This section allows you to export or import your All In One WP Security & Firewall settings.', 'all-in-one-wp-security-and-firewall'));
            echo '<br />'.__('This can be handy if you wanted to save time by applying the settings from one site to another site.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('NOTE: Before importing, it is your responsibility to know what settings you are trying to import. Importing settings blindly can cause you to be locked out of your site.', 'all-in-one-wp-security-and-firewall').'
            <br />'.__('For Example: If a settings item relies on the domain URL then it may not work correctly when imported into a site with a different domain.','all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>

        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Export AIOS settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-export-settings-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
			<span class="description"><?php echo htmlspecialchars(__('To export your All In One WP Security & Firewall settings click the button below.', 'all-in-one-wp-security-and-firewall')); ?></span>
            </tr>
        </table>
		<input type="submit" name="aiowps_export_settings" value="<?php _e('Export AIOS settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>
        <div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e('Import AIOS settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-import-settings-nonce'); ?>
        <table class="form-table">
			<tr valign="top">
				<span class="description"><?php echo htmlspecialchars(__('Use this section to import your All In One WP Security & Firewall settings from a file. Alternatively, copy/paste the contents of your import file into the textarea below.', 'all-in-one-wp-security-and-firewall')); ?></span>
				<th scope="row">
					<label for="aiowps_import_settings_file_button"><?php _e('Import file', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				</th>
                <td>
                    <input type="button" id="aiowps_import_settings_file_button" name="aiowps_import_settings_file_button" class="button rbutton" value="<?php _e('Select Your Import Settings File', 'all-in-one-wp-security-and-firewall'); ?>" />
                    <input name="aiowps_import_settings_file" type="text" id="aiowps_import_settings_file" value="" size="80" />
                    <p class="description">
                        <?php
                        _e('After selecting your file, click the button below to apply the settings to your site.', 'all-in-one-wp-security-and-firewall');
                        ?>
                    </p>
                </td>
            </tr>
			<tr valign="top">
				<th scope="row">
					<label for="aiowps_import_settings_text"><?php _e('Copy/Paste import data', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				</th>
                <td>
                    <textarea name="aiowps_import_settings_text" id="aiowps_import_settings_text" style="width:80%;height:140px;"></textarea>
                </td>
            </tr>
        </table>
		<input type="submit" name="aiowps_import_settings" value="<?php _e('Import AIOS settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
        </form>
        </div></div>
    <?php
    }

	/**
     * Renders advanced settings tab.
     *
	 * @return void
	 */
	public function render_advanced_settings() {
		if (!is_main_site()) {
            return;
		}

		global $aio_wp_security;

		if (isset($_POST['aiowps_save_advanced_settings'])) {
			if (empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'aiowpsec-ip-settings-nonce')) {
				$aio_wp_security->debug_logger->log_debug('Nonce check failed for save advanced settings.', 4);
				die('Nonce check failed for save advanced settings.');
			}

			$ip_retrieve_method_id = sanitize_text_field($_POST["aiowps_ip_retrieve_method"]);

            if (in_array($ip_retrieve_method_id, array_keys(AIOS_Abstracted_Ids::get_ip_retrieve_methods()))) {
				$aio_wp_security->configs->set_value('aiowps_ip_retrieve_method', $ip_retrieve_method_id);
				$aio_wp_security->configs->save_config(); //Save the configuration

				//Clear logged in list because it might be showing wrong addresses
				if (AIOWPSecurity_Utility::is_multisite_install()) {
					delete_site_transient('users_online');
				} else {
					delete_transient('users_online');
				}

				$this->show_msg_settings_updated();
			}
		}

		$ip_retrieve_methods_postfixes = array(
                'REMOTE_ADDR' =>  __('Default - if correct, then this is the best option', 'all-in-one-wp-security-and-firewall'),
                'HTTP_CF_CONNECTING_IP' => __("Only use if you're using Cloudflare.", 'all-in-one-wp-security-and-firewall'),
		);

		$ip_retrieve_methods = array();
        foreach (AIOS_Abstracted_Ids::get_ip_retrieve_methods() as $id => $ip_method) {
            $ip_retrieve_methods[$id]['ip_method'] = $ip_method;

			if (isset($_SERVER[$ip_method])) {
				$ip_retrieve_methods[$id]['ip_method'] .= ' '.sprintf(__('(current value: %s)', 'all-in-one-wp-security-and-firewall'), $_SERVER[$ip_method]);
                $ip_retrieve_methods[$id]['is_enabled'] = true;
			} else {
				$ip_retrieve_methods[$id]['ip_method'] .= '  (' . __('no value (i.e. empty) on your server', 'all-in-one-wp-security-and-firewall') . ')';
				$ip_retrieve_methods[$id]['is_enabled'] = false;
			}

			if (!empty($ip_retrieve_methods_postfixes[$ip_method])) {
				$ip_retrieve_methods[$id]['ip_method'] .= ' (' . $ip_retrieve_methods_postfixes[$ip_method] . ')';
			}
		}

		$aio_wp_security->include_template('menus/settings/advanced-settings.php', false, array(
			'is_localhost' => AIOWPSecurity_Utility::is_localhost(),
			'ip_retrieve_methods' => $ip_retrieve_methods,
			'server_suitable_ip_methods' => AIOWPSecurity_Utility_IP::get_server_suitable_ip_methods(),
        ));
	}

    private function check_if_wp_config_contents($wp_file)
    {
        $is_wp_config = false;

        $file_contents = file($wp_file);

        if ($file_contents == '' || $file_contents == NULL || $file_contents == false)
        {
            return -1;
        }
        foreach ($file_contents as $line)
        {
            if ((strpos($line, "define('DB_NAME'") !== false))
            {
                $is_wp_config = true; //It appears that we have some sort of wp-config.php file
                break;
            }
            else
            {
                //see if we're at the end of the section
                $is_wp_config = false;
            }
        }
        
        return $is_wp_config ? 1 : -1;

    }

    function check_if_valid_aiowps_settings_text($strText) {
        if ($this->check_is_aiopws_settings($strText)) {
            return stripcslashes($strText);
        } else {
            return -1;
        }
    }

    private function check_is_aiopws_settings($strText) {
        if (false === strpos($strText, 'aiowps_enable_login_lockdown')) {
            return false;
        }
        
        return true;
    }

	// Checks if valid AIOS settings file and returns contents as string
	private function check_if_valid_aiowps_settings_file($wp_file) {
        $is_aiopws_settings = false;

        $file_contents = file_get_contents($wp_file);

        if ($file_contents == '' || $file_contents == NULL || $file_contents == false)
        {
            return -1;
        }

		// Check a known AIOS config strings to see if it is contained within this file
        $is_aiopws_settings = $this->check_is_aiopws_settings($file_contents);

        if ($is_aiopws_settings)
        {
            return $file_contents;
        }
        else
        {
            return -1;
        }

    }

} //end class
