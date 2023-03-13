<div class="wpbdp-page-csv-export wpbdp-admin-page-settings">

<div class="error" id="exporterror" style="display: none;"><p>
<?php
    esc_html_e( 'An unknown error occurred during the export. Please make sure you have enough free disk space and memory available to PHP. Check your error logs for details.', 'business-directory-plugin' ); ?>
</p></div>

<div class="step-1">

<p class="howto wpbdp-settings-subtab-description wpbdp-setting-description">
<?php
$notice = _x( 'Please note that the export process is a resource intensive task. If your export does not succeed try disabling other plugins first and/or increasing the values of the \'memory_limit\' and \'max_execution_time\' directives in your server\'s php.ini configuration file.', 'admin csv-export', 'business-directory-plugin' );
echo str_replace(
	array( 'memory_limit', 'max_execution_time' ),
	array(
		'<a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank" rel="noopener">memory_limit</a>',
		'<a href="http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time" target="_blank" rel="noopener">max_execution_time</a>',
	),
	$notice
);
?>
</p>

<form id="wpbdp-csv-export-form" action="" method="POST">

	<div class="wpbdp-settings-form-title">
		<h3><?php _ex( 'Export settings', 'admin csv-export', 'business-directory-plugin' ); ?></h3>
	</div>
	<div class="form-table wpbdp-settings-form wpbdp-grid">
		<div class="wpbdp-setting-row">
			<div class="wpbdp-setting-label">
				<label for="wpbdp-listing-status">
					<?php _ex( 'Which listings to export?', 'admin csv-export', 'business-directory-plugin' ); ?>
				</label>
			</div>
			<select name="settings[listing_status]" id="wpbdp-listing-status">
				<option value="all"><?php esc_html_e( 'All', 'business-directory-plugin' ); ?></option>
				<option value="publish"><?php _ex( 'Active Only', 'admin csv-export', 'business-directory-plugin' ); ?></option>
				<option value="publish+draft"><?php _ex( 'Active + Pending Renewal', 'admin csv-export', 'business-directory-plugin' ); ?></option>
			</select>
		</div>
		<div class="wpbdp-setting-row wpdb-checkbox">
			<label>
				<input name="settings[export-images]"
					type="checkbox"
					value="1" />
				<?php _ex( 'Export images', 'admin csv-export', 'business-directory-plugin' ); ?>
			</label>
			<div class="wpbdp-setting-description">
				<?php esc_html_e( 'Create a ZIP file with both a CSV file and listing images.', 'business-directory-plugin' ); ?>
			</div>
		</div>
		<div class="wpbdp-setting-row wpbdp-settings-multicheck-options">
			<div class="wpbdp-setting-label">
				<label><?php esc_html_e( 'Additional metadata to export', 'business-directory-plugin' ); ?></label>
			</div>
			<div class="wpbdp-settings-multicheck-option">
				<label>
					<input name="settings[generate-sequence-ids]"
						type="checkbox"
						value="1" />
					<?php _ex( 'Include unique IDs for each listing (sequence_id column).', 'admin csv-export', 'business-directory-plugin' ); ?>
				</label>
				<span class="wpbdp-setting-description">
					<?php esc_html_e( 'If you plan to re-import the listings into your directory and don\'t want new ones created, select this option!', 'business-directory-plugin' ); ?>
				</span>
			</div>

			<div class="wpbdp-settings-multicheck-option">
				<label>
					<input name="settings[include-users]"
						type="checkbox"
						value="1"
						checked="checked" />
					<?php _ex( 'Author information (username)', 'admin csv-export', 'business-directory-plugin' ); ?>
				</label>
			</div>

			<div class="wpbdp-settings-multicheck-option">
				<label>
					<input name="settings[include-expiration-date]"
						type="checkbox"
						value="1"
						checked="checked" />
					<?php _ex( 'Listing expiration date', 'admin csv-export', 'business-directory-plugin' ); ?>
				</label>
			</div>

			<div class="wpbdp-settings-multicheck-option">
				<label>
					<input name="settings[include-created-date]"
						type="checkbox"
						value="1" />
						<?php _ex( 'Listing created date', 'admin csv-export', 'business-directory-plugin' ); ?>
				</label>
			</div>

			<div class="wpbdp-settings-multicheck-option">
				<label>
					<input name="settings[include-modified-date]"
						type="checkbox"
						value="1" />
					<?php _ex( 'Listing last updated date', 'admin csv-export', 'business-directory-plugin' ); ?>
				</label>
			</div>

			<div class="wpbdp-settings-multicheck-option">
				<label>
					<input name="settings[include-tos-acceptance-date]"
						type="checkbox"
						value="1" />
					<?php _ex( 'Listing T&C acceptance date', 'admin csv-export', 'business-directory-plugin' ); ?>
				</label>
			</div>
		</div>
	</div>

	<div class="wpbdp-settings-form-title">
		<h3><?php esc_html_e( 'CSV File Settings', 'business-directory-plugin' ); ?></h3>
	</div>
	<div class="form-table wpbdp-settings-form wpbdp-grid">
		<div class="wpbdp-setting-row form-required">
			<div class="wpbdp-setting-label">
				<label for="settings[target-os]">
					<?php _ex( 'What operating system will you use to edit the CSV file?', 'admin csv-export', 'business-directory-plugin' ); ?> *
				</label>
			</div>
			<div class="wpbdp-setting-description">
				<?php esc_html_e( 'Windows and macOS versions of MS Excel handle CSV files differently. To make sure all your listings information is displayed properly when you view or edit the CSV file, we need to generate different versions of the file for each operating system.', 'business-directory-plugin' ); ?>
			</div>
			<label>
				<input name="settings[target-os]"
					type="radio"
					aria-required="true"
					value="windows"
					checked="checked" />
				<?php _ex( 'Windows', 'admin csv-export', 'business-directory-plugin' ); ?>
			</label>
			<br />
			<label>
				<input name="settings[target-os]"
					type="radio"
					aria-required="true"
					value="macos" />
				<?php _ex( 'macOS', 'admin csv-export', 'business-directory-plugin' ); ?>
			</label>
		</div>
		<div class="wpbdp-setting-row form-required wpbdp6">
			<div class="wpbdp-setting-label">
				<label><?php esc_html_e( 'Image Separator', 'business-directory-plugin' ); ?> *</label>
			</div>
			<input name="settings[images-separator]"
				type="text"
				aria-required="true"
				value=";" />
		</div>
		<div class="wpbdp-setting-row form-required wpbdp6">
			<div class="wpbdp-setting-label">
				<label><?php _ex( 'Category Separator', 'admin csv-export', 'business-directory-plugin' ); ?> *</label>
			</div>
			<input name="settings[category-separator]"
				type="text"
				aria-required="true"
				value=";" />
		</div>
	</div>

	<p class="submit">
		<?php submit_button( _x( 'Export Listings', 'admin csv-export', 'business-directory-plugin' ), 'primary', 'do-export', false ); ?>
	</p>
</form>
</div>

<div class="step-2">
	<h2><?php _ex( 'Export in Progress...', 'admin csv-export', 'business-directory-plugin' ); ?></h2>
	<p><?php _ex( 'Your export file is being prepared. Please <u>do not leave</u> this page until the export finishes.', 'admin csv-export', 'business-directory-plugin' ); ?></p>

	<dl>
		<dt><?php _ex( 'No. of listings:', 'admin csv-export', 'business-directory-plugin' ); ?></dt>
		<dd class="listings">?</dd>
		<dt><?php _ex( 'Approximate export file size:', 'admin csv-export', 'business-directory-plugin' ); ?></dt>
		<dd class="size">?</dd> 
	</dl>

	<div class="export-progress"></div>

	<p class="submit">
		<a href="#" class="cancel-import button"><?php _ex( 'Cancel Export', 'admin csv-export', 'business-directory-plugin' ); ?></a>
	</p>
</div>

<div class="step-3">
	<h2><?php _ex( 'Export Complete', 'admin csv-export', 'business-directory-plugin' ) ?></h2>
	<p><?php _ex( 'Your export file has been successfully created and it is now ready for download.', 'admin csv-export', 'business-directory-plugin' ); ?></p>
	<div class="download-link">
		<a href="" class="button button-primary">
			<?php
			echo sprintf(
				_x( 'Download %1$s (%2$s)', 'admin csv-export', 'business-directory-plugin' ),
				'<span class="filename"></span>',
				'<span class="filesize"></span>'
			);
			?>
		</a>
	</div>
	<div class="cleanup-link wpbdp-note">
		<p><?php _ex( 'Click "Cleanup" once the file has been downloaded in order to remove all temporary data created by Business Directory during the export process.', 'admin csv-export', 'business-directory-plugin' ); ?><br />
		<a href="" class="button"><?php _ex( 'Cleanup', 'admin csv-export', 'business-directory-plugin' ); ?></a></p>
	</div>    
</div>

<div class="canceled-export">
	<h2><?php _ex( 'Export Canceled', 'admin csv-export', 'business-directory-plugin' ) ?></h2>
	<p><?php _ex( 'The export has been canceled.', 'admin csv-export', 'business-directory-plugin' ); ?></p>
	<p><a href="" class="button"><?php _ex( '← Return to CSV Export', 'admin csv-export', 'business-directory-plugin' ); ?></a></p>
</div>

</div>
