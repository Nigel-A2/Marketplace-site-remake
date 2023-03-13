<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

class AIOWPSecurity_Firewall_Setup_Notice {

	/**
	 * Holds reference to an instance of itself
	 *
	 * @var AIOWPSecurity_Firewall_Setup_Notice
	 */
	private static $instance = null;

	/**
	 * Holds our wp-config file wrapped in our manager class
	 *
	 * @var AIOWPSecurity_Block_WpConfig
	 */
	private $wpconfig;

	/**
	 * Holds our mu-plugin file wrapped in our manager class
	 *
	 * @var AIOWPSecurity_Block_Muplugin
	 */
	private $muplugin;

	/**
	 * Holds our bootstrap file wrapped in our manager class
	 *
	 * @var AIOWPSecurity_Block_Bootstrap
	 */
	private $bootstrap;

	/**
	 * Constants for the different notice types
	 * 
	 * @var string
	 */
	const NOTICE_BOOTSTRAP     = 'manual_bootstrap';
	const NOTICE_MANUAL 	   = 'manual';
	const NOTICE_INSTALLED     = 'success';
	const NOTICE_DIRECTIVE_SET = 'userini_directive';

	/**
	 * Constructs our object by setting up our essential files
	 */
	private function __construct() {
		$this->bootstrap = AIOWPSecurity_Utility_Firewall::get_bootstrap_file();
		$this->wpconfig  = AIOWPSecurity_Utility_Firewall::get_wpconfig_file();
		$this->muplugin  = AIOWPSecurity_Utility_Firewall::get_muplugin_file();
		AIOWPSecurity_Utility_Firewall::get_firewall_rules_path(); //creates the needed directories for the first time
	}

	/**
	 * Entry point for the dashboard notice
	 *
	 * @return void
	*/
	public function start_firewall_setup() {

		global $aio_wp_security;

		$firewall_files = array(
			'server' => AIOWPSecurity_Utility_Firewall::get_server_file(),
			'bootstrap' => $this->bootstrap,
			'wpconfig' => $this->wpconfig,
			'muplugin' => $this->muplugin,
		);

		//Check each file and update the contents if necessary
		foreach ($firewall_files as $name => $file) {
			${'is_firewall_in_'.$name} = false;

			if (AIOWPSecurity_Utility_Firewall::MANUAL_SETUP === $file) {
				continue;
			}

			${'is_firewall_in_'.$name} = $file->contains_contents();

			if (true === ${'is_firewall_in_'.$name}) {
				$file->update_contents();
			}
		}
		
		if (!$aio_wp_security->is_aiowps_admin_page()) {
			return;
		}

		if (true !== $is_firewall_in_server || true !== $is_firewall_in_bootstrap) {

			if (true !== $is_firewall_in_wpconfig) {
				$this->render_automatic_setup_notice(); //Show notice to setup firewall, if the firewall is not present in our required files
			}elseif (true === $is_firewall_in_wpconfig) {
				$this->render_upgrade_protection_notice();
			}
		}

		$this->render_notices();
	}

	/**
	 * Will execute when the user presses 'Set up now' button
	 *
	 * @return void
	 */
	private function do_setup() {

		$is_inserted_firewall_file = false;
		
		$is_inserted_bootstrap_file = $this->bootstrap->contains_contents();

		if (true !== $is_inserted_bootstrap_file) {

			$is_inserted_bootstrap_file = $this->bootstrap->insert_contents();

			if (true !== $is_inserted_bootstrap_file) {
				$this->log_wp_error($is_inserted_bootstrap_file);
				$this->show_notice(self::NOTICE_BOOTSTRAP);
				return;
			}

		}

		$firewall_file = AIOWPSecurity_Utility_Firewall::get_server_file();

		if ($firewall_file instanceof AIOWPSecurity_Block_Userini) {

			$directive = AIOWPSecurity_Utility_Firewall::get_already_set_directive($firewall_file);
			
			if (!empty($directive)) {
				
				if (AIOWPSecurity_Utility_Firewall::get_bootstrap_path() === $directive) {
					$is_inserted_firewall_file = true;
				} else {
					$this->show_notice(self::NOTICE_DIRECTIVE_SET, array('directive'=>$directive));
				}

			} else {
				$is_inserted_firewall_file = $firewall_file->insert_contents();
			}

		} else {

			if (AIOWPSecurity_Utility_Firewall::MANUAL_SETUP !== $firewall_file) {
				$is_inserted_firewall_file = $firewall_file->insert_contents(); // attempts to insert firewall into required file
			}
		}

		$is_inserted_wpconfig = $this->wpconfig->contains_contents();
		if (true !== $is_inserted_wpconfig) {
			$is_inserted_wpconfig = $this->wpconfig->insert_contents();
		}

		if (true === $is_inserted_firewall_file) { 
			$this->show_notice(self::NOTICE_INSTALLED);
		}

		if (true !== $is_inserted_firewall_file) { 

			if (true !== $is_inserted_wpconfig) {
				$is_inserted_muplugin = $this->muplugin->insert_contents();
				$this->log_wp_error($is_inserted_muplugin);
				$this->log_wp_error($is_inserted_wpconfig);
			}

			$this->log_wp_error($is_inserted_firewall_file);
			$this->show_notice(self::NOTICE_MANUAL);
			
		}

	}

	/**
	 * Dismisses the notice 
	 *
	 * @return void
	 */
	private function do_dismiss() {
		global $aio_wp_security;

		$aio_wp_security->configs->set_value('aios_firewall_dismiss', true);
		$aio_wp_security->configs->save_config();
	}

	/**
	 * Checks whether the notice is dismissed
	 *
	 * @return boolean
	 */
	private function is_dismissed() {
		global $aio_wp_security;
		return (true === $aio_wp_security->configs->get_value('aios_firewall_dismiss'));
		
	}

	/**
	 * Handles the form submission for the 'Set up now' notice
	 *
	 * @return void
	 */
	public function handle_setup_form() {
		if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'aiowpsec-firewall-setup')) {
			$this->do_setup();
			AIOWPSecurity_Utility::redirect_to_url(admin_url('admin.php?page=aiowpsec'));
		}
	}

	/**
	 * Handles the dismiss form
	 *
	 * @return void
	 */
	public function handle_dismiss_form() {
		if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'aiowpsec-firewall-setup-dismiss')) {
			$this->do_dismiss();
			AIOWPSecurity_Utility::redirect_to_url(admin_url('admin.php?page=aiowpsec'));
		}
	}

	/**
	 * Wrapper function to log WP_Errors to debug log
	 *
	 * @param WP_Error $wp_error - Our error which gets logged
	 * @return void
	 */
	private function log_wp_error($wp_error) {

		if (is_wp_error($wp_error)) {
			global $aio_wp_security;

			$error_message = $wp_error->get_error_message();
			$error_message .= ' - ';
			$error_message .= $wp_error->get_error_data();
			$aio_wp_security->debug_logger->log_debug($error_message, 4);
		}
	}

	/**
	 * Sets the flags to show notices
	 *
	 * @param string $type - the type of notice we want to set
	 * @param array $values - any values that need to be passed
	 * @return void
	 */
	private function show_notice($type, $values = array()) {
		global $aio_wp_security;

		$aio_wp_security->configs->set_value('firewall_notice_'.$type, true);

		if (!empty($values)) {
			$aio_wp_security->configs->set_value('firewall_notice_values', $values);
		}
		
		$aio_wp_security->configs->save_config();
	}

	/**
	 * Renders any necessary notices
	 *
	 * @return void
	 */
	private function render_notices() {
		global $aio_wp_security;

		$notices = array(
			self::NOTICE_BOOTSTRAP, 
			self::NOTICE_MANUAL, 
			self::NOTICE_INSTALLED, 
			self::NOTICE_DIRECTIVE_SET,
		);

		foreach($notices as $notice) {
			if ($aio_wp_security->configs->get_value('firewall_notice_'.$notice)) {
				
				switch($notice) {
					case self::NOTICE_BOOTSTRAP:
						$this->render_bootstrap_notice(); break;

					case self::NOTICE_MANUAL:
						if (!$this->any_pending_notices(self::NOTICE_MANUAL)) {
							$this->render_manual_setup_notice();
						}
						break;
					case self::NOTICE_INSTALLED:
						$this->render_firewall_installed_notice(); break;

					case self::NOTICE_DIRECTIVE_SET:
						$values = $aio_wp_security->configs->get_value('firewall_notice_values');
						$this->render_userini_directive_set_notice($values['directive']);
						$aio_wp_security->configs->delete_value('firewall_notice_values');
						break;
				}

				$aio_wp_security->configs->delete_value('firewall_notice_'.$notice);
			}
		}

		$aio_wp_security->configs->save_config();
	}

	/**
	 * Detects if we have any notices pending to display
	 *
	 * @param string $exclude - do not check the status of these notices
	 * @return boolean
	 */
	private function any_pending_notices(...$exclude) {
		global $aio_wp_security;

		$notices = array(
			self::NOTICE_BOOTSTRAP,
			self::NOTICE_MANUAL,
			self::NOTICE_INSTALLED,
			self::NOTICE_DIRECTIVE_SET,
		);
		$notices = array_diff($notices, $exclude);
		
		foreach($notices as $notice) {
			if (true === $aio_wp_security->configs->get_value('firewall_notice_'.$notice)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Notice is shown if we are unable to write to the bootstrap file
	 *
	 * @return void
	 */
	private function render_bootstrap_notice() {
		?>
			<div class="notice notice-error is-dismissible">
				<p>
					<strong><?php _e('All In One WP Security and Firewall', 'all-in-one-wp-security-and-firewall'); ?></strong>
				</p>
				<p><?php _e('We were unable to create the file necessary to give you the highest level of protection.', 'all-in-one-wp-security-and-firewall');?></p>
				<p><?php _e('Your firewall will have reduced protection which means some of your firewall\'s functionality will be unavailable.', 'all-in-one-wp-security-and-firewall');?></p>
				<p><?php _e('If you would like to manually set up the necessary file, please follow these steps:', 'all-in-one-wp-security-and-firewall');?></p>
				<p>
				    <?php
				    /* translators: %s Boostrap file name. */
				    printf(__('1. Create a file with the name %s in the same directory as your WordPress install is in, i.e.:', 'all-in-one-wp-security-and-firewall'), pathinfo($this->bootstrap, PATHINFO_BASENAME));
                    ?>
                </p>
				<pre style='max-width: 100%;background-color: #f0f0f0;border:#ccc solid 1px;padding: 10px;white-space:pre-wrap;'><?php echo esc_html($this->bootstrap); ?></pre>
				<p><?php _e('2. Paste in the following code:', 'all-in-one-wp-security-and-firewall');?></p>
				<pre style='max-width: 100%;background-color: #f0f0f0;border:#ccc solid 1px;padding: 10px;white-space:pre-wrap;'><?php echo htmlentities($this->bootstrap->get_contents()); ?></pre>
				<p><?php _e('3. Save the file and press the \'Try again\' button below:', 'all-in-one-wp-security-and-firewall');?></p>
		<?php
		$this->render_try_again_button();
		$this->render_manual_notice_footer();
	}

	/**
	 * Notice is shown if auto_prepend_file directive is already set in user.ini
	 *
	 * @param string $directive_value
	 * @return void
	 */
	private function render_userini_directive_set_notice($directive_value) {

		$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();
		$firewall_file = AIOWPSecurity_Utility_Firewall::get_server_file();

		$this->render_manual_notice_header();
		?>
			<p>
				<?php _e('1. Open the following file:', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
			<p><code><?php echo esc_html($firewall_file); ?></code></p>

			<?php if (empty($directive_value)) {?>
				<p>
					<?php _e('2. Look for the auto_prepend_file directive.', 'all-in-one-wp-security-and-firewall'); ?>
				</p>
			<?php } else {?>
				<p>
					<?php _e('2. Look for the following:', 'all-in-one-wp-security-and-firewall'); ?>
				</p>
				<pre style='max-width: 100%;background-color: #f0f0f0;border:#ccc solid 1px;padding: 10px;white-space:pre-wrap;'><?php echo "auto_prepend_file='".esc_html($directive_value)."'";?></pre>
			<?php } ?>
			
			<p>
				<?php _e('3. Change it to the following:', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
				<pre style='max-width: 100%;background-color: #f0f0f0;border:#ccc solid 1px;padding: 10px;white-space:pre-wrap;'><?php echo "auto_prepend_file='".esc_html($bootstrap_path)."'";?></pre>
			<p>
				<?php echo __('4. Save the file  and press the \'Try again\' button below:', 'all-in-one-wp-security-and-firewall').' '.__('You may have to wait up to 5 minutes before the settings take effect.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
		<?php
		$this->render_try_again_button();
		$this->render_manual_notice_footer();
	}

	/**
	 * Shows when the firewall has successfully installed
	 *
	 * @return void
	 */
	private function render_firewall_installed_notice() {
		?>
			<div class='notice notice-success is-dismissible'>
				<p><strong><?php _e('All In One WP Security and Firewall', 'all-in-one-wp-security-and-firewall'); ?></strong></p>
				<p>
					<?php
						echo __('Your firewall has been installed with the highest level of protection.', 'all-in-one-wp-security-and-firewall').' '.
							 __('You may have to wait 5 minutes for the changes to take effect.', 'all-in-one-wp-security-and-firewall');
					?>
				</p>
			</div>
		<?php
	}

	/**
	 * Renders the 'manual setup' dashboard notice
	 *
	 * @return void
	 */
	private function render_manual_setup_notice() {

		$firewall_file = AIOWPSecurity_Utility_Firewall::get_server_file();
		
		if (AIOWPSecurity_Utility_Firewall::MANUAL_SETUP === $firewall_file) {
			//Show users how to manually add the firewall via php.ini if we can't detect their server
			$bootstrap_path = AIOWPSecurity_Utility_Firewall::get_bootstrap_path();

			$this->render_manual_notice_header();
			?>
				<p>
					<?php _e('1. Open your php.ini file.', 'all-in-one-wp-security-and-firewall'); ?>
				</p>
				<p>
					<?php _e('2. Set the auto_prepend_file directive like below:', 'all-in-one-wp-security-and-firewall'); ?>
				</p>
					<pre style='max-width: 100%;background-color: #f0f0f0;border:#ccc solid 1px;padding: 10px;white-space:pre-wrap;'><?php echo "auto_prepend_file='".esc_html($bootstrap_path)."'";?></pre>
				<p>
					<?php echo __('3. Restart the webserver and refresh the page', 'all-in-one-wp-security-and-firewall').' '.__('You may have to wait up to 5 minutes before the settings take effect.', 'all-in-one-wp-security-and-firewall'); ?>
				</p>
			<?php
			$this->render_manual_notice_footer();
		} else {
			//Show users how to manually add the firewall via their own server file
			$this->render_manual_notice_header();
            $firewall_file_name = pathinfo($firewall_file, PATHINFO_BASENAME);
			?>
				<p>
                    <?php
                    /* translators: %s Firewall file name. */
                    printf(__('1. Create a file with the name %s in the same directory as your WordPress install is in, i.e.:', 'all-in-one-wp-security-and-firewall'), $firewall_file_name);
                    ?>
					<p><code><?php echo esc_html($firewall_file); ?></code></p>
				</p>
				<p>
					<?php _e('2. Paste in the following directives:', 'all-in-one-wp-security-and-firewall'); ?>
				</p>
					<pre style='max-width: 100%;background-color: #f0f0f0;border:#ccc solid 1px;padding: 10px;white-space:pre-wrap;'><?php echo htmlentities($firewall_file->get_contents()); ?></pre>
				<p>
					<?php echo __('3. Save the file and press the \'Try again\' button below:', 'all-in-one-wp-security-and-firewall'); ?>
				</p>
			<?php
			$this->render_try_again_button();
			$this->render_manual_notice_footer();
		}
	}

	/**
	 * The header for notices that require manual intervention
	 *
	 * @return void
	 */
	private function render_manual_notice_header() {
		?>
			<div class="notice notice-warning is-dismissible">
			<p>
				<strong><?php _e('All In One WP Security and Firewall', 'all-in-one-wp-security-and-firewall'); ?></strong>
			</p>
			<p>
				<?php echo __('We were unable to set up your firewall with the highest level of protection.', 'all-in-one-wp-security-and-firewall').' '.
						   __('Your firewall will have reduced functionality.', 'all-in-one-wp-security-and-firewall');
				?>
			</p>
			<p>
				<?php _e('To give your site the highest level of protection, please follow these steps:', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
		<?php
	}

	/**
	 * The footer for notices that require manual intervention
	 *
	 * @return void
	 */
	private function render_manual_notice_footer() {
		?>
			<p>
				<strong><?php _e('Note: if you\'re unable to perform any of the aforementioned steps, please ask your web hosting provider for further assistance.', 'all-in-one-wp-security-and-firewall'); ?></strong>
			</p>
			</div>
		<?php
	}

    /**
     * Render Try again button.
     *
     * @return void
     */
    private function render_try_again_button() {
        ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
            <?php wp_nonce_field('aiowpsec-firewall-setup'); ?>
            <input type="hidden" name="action" value="aiowps_firewall_setup">
            <div style="padding-top: 10px; padding-bottom: 10px;">
                    <input class="button button-primary" type="submit" name="btn_try_again" value="<?php _e('Try again', 'all-in-one-wp-security-and-firewall'); ?>">
            </div>
        </form>
        <?php
    }

	/**
	 * Renders the warning that users do not have the highest level of protection
	 *
	 * @return void
	 */
	private function render_upgrade_protection_notice() {

		if ($this->should_not_show_notice()) {
			return;
		}
		?>
			<div class="notice notice-warning">
				<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
					<?php wp_nonce_field('aiowpsec-firewall-setup'); ?>
					<input type="hidden" name="action" value="aiowps_firewall_setup">
					<p>
						<?php _e('We have detected that your AIOS firewall is not fully installed, and therefore does not have the highest level of protection. ', 'all-in-one-wp-security-and-firewall'); ?>
						<?php _e('Your firewall will have reduced functionality until it has been upgraded. ', 'all-in-one-wp-security-and-firewall');?>
						<div style="padding-top: 10px;">
						    <input class="button button-primary" type="submit" name="btn_upgrade_now" value="<?php _e('Upgrade your protection now', 'all-in-one-wp-security-and-firewall'); ?>">
						</div>
					</p>
				</form>

			</div>

		<?php
	}

    /**
     * Whether the firewall notice should not be shown.
     *
     * return boolean True if the firewall notice should not be shown otherwise false.
     */
    private function should_not_show_notice() {
        if (!is_main_site()) {
            return true;
        }

        if (!current_user_can(AIOWPSEC_MANAGEMENT_PERMISSION)) {
			return true;
		}

		if ($this->is_dismissed() && !AIOWPSecurity_Utility_Firewall::is_firewall_page()) {
			return true;
		}
		
		if ($this->any_pending_notices()) {
			return true; //only display if there are no other notices waiting to be displayed
		}

        return false;
    }

	/**
	 * Renders the 'Set up now' dashboard notice
	 *
	 * @return void
	 */
	private function render_automatic_setup_notice() {
		
		if ($this->should_not_show_notice()) {
			return;
		}

		?>
			<div class="notice notice-information">

				<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
					<?php wp_nonce_field('aiowpsec-firewall-setup'); ?>
					<input type="hidden" name="action" value="aiowps_firewall_setup">
					<p>
						<strong><?php _e('All In One WP Security and Firewall', 'all-in-one-wp-security-and-firewall'); ?></strong>
					</p>
					<p>
						<?php echo __('Our PHP-based firewall has been created to give you even greater protection.', 'all-in-one-wp-security-and-firewall').' '.
								   __('To ensure the PHP-based firewall runs before any potentially vulnerable code in your WordPress site can be reached, it will need to be set up.');?>
					</p>
					<p>
						<?php _e('If you already have our .htaccess-based firewall enabled, you will still need to set up the PHP-based firewall to benefit from its protection.', 'all-in-one-wp-security-and-firewall'); ?>
					</p>
					<p>
						<?php _e('To set up the PHP-based firewall, press the \'Set up now\' button below:', 'all-in-one-wp-security-and-firewall'); ?>
					</p>
					<div style='padding-bottom: 10px; padding-top:10px;'>
						<input class="button button-primary" type="submit" name="btn_setup_now" value="<?php _e('Set up now', 'all-in-one-wp-security-and-firewall'); ?>">
				</form>
						<?php if (!AIOWPSecurity_Utility_Firewall::is_firewall_page()) { ?>
							<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" style='display:inline;'>
								<?php wp_nonce_field('aiowpsec-firewall-setup-dismiss'); ?>
								<input type="hidden" name="action" value="aiowps_firewall_setup_dismiss">
								<input class="button button-secondary" type="submit" name="btn_dismiss_setup_now" value="<?php _e('Dismiss', 'all-in-one-wp-security-and-firewall'); ?>">
							</form>
						<?php } ?>
					</div>
			</div>

		<?php
	}

	/**
	 * Ensures only one instance of the class can be created (singleton)
	 *
	 * @return void
	 */
	public static function get_instance() {

		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
