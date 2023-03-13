<?php
/**
 * CSV import settings template
 *
 * @package BDP/Templates/Admin
 */

/**
 * @param array  $defs
 * @param string $key
 * @param mixed  $val
 * @return mixed
 */
function _defaults_or( $defs, $key, $val ) {
    if ( array_key_exists( $key, $defs ) )
        return $defs[ $key ];

    return $val;
}
?>
<div class="wpbdp-page-csv-import wpbdp-clearfix wpbdp-admin-page-settings">

<?php WPBDP_Admin_Education::show_tip( 'migrator' ); ?>

<p class="howto wpbdp-settings-subtab-description wpbdp-setting-description">
<?php
esc_html_e( 'Here, you can import data into your directory using the CSV format.', 'business-directory-plugin' );
?><br />
<?php
printf(
	// translators: %1$s is a opening <a> tag, %2$s is a closing </a> tag.
	esc_html__( 'We strongly recommend reading our %1$sCSV import documentation%2$s first to help you do things in the right order.', 'business-directory-plugin' ),
	'<a href="https://businessdirectoryplugin.com/knowledge-base/csv-import-export/" target="_blank" rel="noopener">',
	'</a>'
);
?>
</p>

<form id="wpbdp-csv-import-form" action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="do-import" />
	<?php wp_nonce_field( 'do-import' ); ?>

	<div class="wpbdp-settings-form-title">
		<h3><?php esc_html_e( 'Import Files', 'business-directory-plugin' ); ?></h3>
	</div>
	<div class="wpbdp-settings-form wpbdp-grid">
		<div class="wpbdp-setting-row wpbdp-grid">
			<div class="wpbdp-setting-label wpbdp6">
				<label> <?php esc_html_e( 'CSV File', 'business-directory-plugin' ); ?> *</label>
			</div>
			<div class="wpbdp6">
				<input name="csv-file" type="file" aria-required="true" />

				<?php if ( $files['csv'] ) : ?>
				<div class="file-local-selection">
					<?php
					echo str_replace(
						'<a>',
						'<a href="#" class="toggle-selection">',
						_x( '... or <a>select a file uploaded to the imports folder</a>', 'admin csv-import', 'business-directory-plugin' )
					);
					?>

					<ul>
						<?php foreach ( $files['csv'] as $f ) : ?>
						<li><label>
							<input type="radio" name="csv-file-local" value="<?php echo esc_attr( basename( $f ) ); ?>" /> <?php echo esc_html( basename( $f ) ); ?>
						</label></li>
						<?php endforeach; ?>
						<li>
							<label><input type="radio" name="csv-file-local" value="" class="dismiss" /> <?php _ex( '(Upload new file)', 'admin csv-import', 'business-directory-plugin' ); ?></label>
						</li>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="wpbdp-setting-row wpbdp-grid">
			<div class="wpbdp-setting-label wpbdp6">
				<label> <?php esc_html_e( 'ZIP file containing images', 'business-directory-plugin' ); ?></label>
			</div>
			<div class="wpbdp6">
				<input name="images-file" type="file" aria-required="true" />

				<?php if ( $files['images'] ) : ?>
				<div class="file-local-selection">
					<?php
					echo str_replace(
						'<a>',
						'<a href="#" class="toggle-selection">',
						_x( '... or <a>select a file uploaded to the imports folder</a>', 'admin csv-import', 'business-directory-plugin' )
					);
					?>

					<ul>
						<?php foreach ( $files['images'] as $f ) : ?>
						<li><label>
							<input type="radio" name="images-file-local" value="<?php echo esc_attr( basename( $f ) ); ?>" />
							<?php echo esc_html( basename( $f ) ); ?>
						</label></li>
						<?php endforeach; ?>
						<li>
							<label><input type="radio" name="images-file-local" value="" class="dismiss" /> <?php _ex( '(Upload new file)', 'admin csv-import', 'business-directory-plugin' ); ?></label>
						</li>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</div>
    </div>

	<div class="wpbdp-settings-form-title">
		<h3><?php esc_html_e( 'CSV File Settings', 'business-directory-plugin' ); ?></h3>
	</div>
	<div class="wpbdp-settings-form wpbdp-grid">
		<div class="wpbdp-setting-row">
			<div class="wpbdp-setting-label">
				<label>
					<?php esc_html_e( 'Column Separator', 'business-directory-plugin' ); ?> *
				</label>
			</div>
				<?php $column_separator = _defaults_or( $defaults, 'csv-file-separator', ',' ); ?>
				<label><input name="settings[csv-file-separator]"
						type="radio"
						aria-required="true"
						value=","
						<?php echo $column_separator == ',' ? 'checked="checked"' : ''; ?>/>
					<?php _ex( 'Comma (,)', 'admin csv-import', 'business-directory-plugin' ); ?></label>
				<br />
				<label><input name="settings[csv-file-separator]"
						type="radio"
						aria-required="true"
						value=";"
						<?php echo $column_separator == ';' ? 'checked="checked"' : ''; ?>/>
					<?php _ex( 'Semicolon (;)', 'admin csv-import', 'business-directory-plugin' ); ?></label>
				<br />
				<label><input name="settings[csv-file-separator]"
						type="radio"
						aria-required="true"
						value="tab"
						<?php echo $column_separator === 'tab' ? 'checked="checked"' : ''; ?>/>
					<?php esc_html_e( 'TAB', 'business-directory-plugin' ); ?>
				</label>
				<br />
		</div>
		<div class="wpbdp-setting-row wpbdp6">
			<div class="wpbdp-setting-label">
				<label>
					<?php esc_html_e( 'Image Separator', 'business-directory-plugin' ); ?> *
				</label>
			</div>
			<input name="settings[images-separator]"
				type="text"
				aria-required="true"
				value="<?php echo esc_attr( _defaults_or( $defaults, 'images-separator', ';' ) ); ?>" />
		</div>
		<div class="wpbdp-setting-row wpbdp6">
			<div class="wpbdp-setting-label">
				<label>
					<?php esc_html_e( 'Category Separator', 'business-directory-plugin' ); ?> *
				</label>
			</div>
			<input name="settings[category-separator]"
				type="text"
				aria-required="true"
				value="<?php echo esc_attr( _defaults_or( $defaults, 'category-separator', ';' ) ); ?>" />
		</div>
    </div>

	<div class="wpbdp-settings-form-title">
		<h3><?php esc_html_e( 'Import settings', 'business-directory-plugin' ); ?></h3>
	</div>
	<div class="wpbdp-settings-form wpbdp-grid">
		<div class="wpbdp-setting-row wpbdp6">
			<div class="wpbdp-setting-label">
				<label> <?php esc_html_e( 'Post status of new imported listings', 'business-directory-plugin' ); ?></label>
			</div>
			<select name="settings[post-status]">
				<?php
				foreach ( get_post_statuses() as $post_status => $post_status_label ) :
					if ( ! in_array( $post_status, array( 'publish', 'pending' ), true ) ) :
						continue;
					endif;
					?>
					<option value="<?php echo esc_attr( $post_status ); ?>" <?php echo _defaults_or( $defaults, 'post-status', 'publish' ) == $post_status ? 'selected="selected"' : ''; ?>>
						<?php echo esc_html( $post_status_label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="wpbdp-setting-row wpbdp6">
			<div class="wpbdp-setting-label">
				<label> <?php esc_html_e( 'Post status of existing imported listings', 'business-directory-plugin' ); ?></label>
			</div>
			<select name="settings[existing-post-status]">
				<option value="preserve_status" <?php echo _defaults_or( $defaults, 'existing-post-status', 'preserve_status' ) == 'preserve_status' ? 'selected="selected"' : ''; ?>><?php _ex( 'Preserve existing status', 'admin csv-import', 'business-directory-plugin' ); ?></option>
				<?php
				foreach ( get_post_statuses() as $post_status => $post_status_label ) :
					if ( ! in_array( $post_status, array( 'publish', 'pending' ), true ) ) :
						continue;
					endif;
					?>
					<option value="<?php echo esc_attr( $post_status ); ?>" <?php echo _defaults_or( $defaults, 'existing-post-status', 'preserve_status' ) == $post_status ? 'selected="selected"' : ''; ?>>
						<?php echo esc_html( $post_status_label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="wpbdp-setting-row">
			<div class="wpbdp-setting-label">
				<label>
					<?php esc_html_e( 'Missing categories handling', 'business-directory-plugin' ); ?> *
				</label>
			</div>
				<label><input name="settings[create-missing-categories]"
						type="radio"
						value="1" <?php echo ( _defaults_or( $defaults, 'create-missing-categories', 1 ) == 1 ) ? 'checked="checked"' : ''; ?> />
					<?php esc_html_e( 'Auto-create categories', 'business-directory-plugin' ); ?>
				</label><br/>
				<label><input name="settings[create-missing-categories]"
						type="radio"
						value="0" <?php echo ( _defaults_or( $defaults, 'create-missing-categories', 1 ) == 0 ) ? 'checked="checked"' : ''; ?> />
					<?php esc_html_e( 'Generate errors when a category is not found', 'business-directory-plugin' ); ?>
				</label>
		</div>
		<div class="wpbdp-setting-row wpdb-checkbox">
			<label>
				<input name="settings[append-images]"
					type="checkbox"
					value="1" checked="checked" />
				<?php esc_html_e( 'Keep existing images', 'business-directory-plugin' ); ?>
			</label>
			<div class="wpbdp-setting-description">
				<?php esc_html_e( 'Appends new images while keeping current ones.', 'business-directory-plugin' ); ?>
			</div>
		</div>
		<div class="wpbdp-setting-row wpdb-checkbox">
			<label><input name="settings[assign-listings-to-user]"
					type="checkbox"
					class="assign-listings-to-user"
					value="1" <?php echo _defaults_or( $defaults, 'assign-listings-to-user', 1 ) ? 'checked="checked"' : ''; ?> />
				<?php esc_html_e( 'Assign listings to a user', 'business-directory-plugin' ); ?>
			</label>
		</div>
		<div class="wpbdp-setting-row default-user-selection wpdb-checkbox">
			<label><input
					type="checkbox"
					class="use-default-listing-user"
					value="1" <?php echo _defaults_or( $defaults, 'default-user', '' ) ? 'checked="checked"' : ''; ?> /> <?php _ex( 'Select a default user to be used if the username column is not present in the CSV file.', 'admin csv-import', 'business-directory-plugin' ); ?>
			</label>
		</div>
		<div class="wpbdp-setting-row default-user-selection">
			<div class="wpbdp-setting-label">
				<label> <?php esc_html_e( 'Default listing user', 'business-directory-plugin' ); ?></label>
			</div>
			<span class="wpbdp-setting-description"><?php esc_html_e( 'This user will be used if the username column is not present in the CSV file.', 'business-directory-plugin' ); ?></span>
			<label>
				<?php echo wpbdp_render_user_field( array( 'class' => 'default-user', 'name' => 'settings[default-user]', 'value' => _defaults_or( $defaults, 'default-user', '' ) ) ); ?>
			</label>
		</div>
		<div class="wpbdp-setting-row">
			<div class="wpbdp-setting-label">
				<label> <?php esc_html_e( 'Number of listings imported on every cycle', 'business-directory-plugin' ); ?></label>
			</div>
			<div class="wpbdp-setting-description"><?php esc_html_e( 'If you are having trouble importing listings due to memory problems, try reducing the import batch size to 5 or 1 and then re-attempt. This will result in a longer batch import time, but will increase the chance of success on shared hosting platforms and other resource-constrained servers.', 'business-directory-plugin' ); ?></div>
			<select name="settings[batch-size]">
				<?php foreach ( array( 40, 30, 20, 15, 10, 5, 1 ) as $batch_size ) : ?>
					<option value="<?php echo $batch_size; ?>" <?php echo _defaults_or( $defaults, 'batch-size', 40 ) == $batch_size ? 'selected="selected"' : ''; ?>><?php echo $batch_size; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="wpbdp-setting-row">
			<label>
				<input name="settings[disable-email-notifications]"
					type="checkbox"
					value="1" checked="checked" />
				<?php esc_html_e( 'Disable email notifications during import', 'business-directory-plugin' ); ?>
			</label>
		</div>
    </div>

    <p class="submit">
		<?php submit_button( _x( 'Test Import', 'admin csv-import', 'business-directory-plugin' ), 'secondary', 'test-import', false ); ?>
		<?php submit_button( _x( 'Import Listings', 'admin csv-import', 'business-directory-plugin' ), 'primary', 'do-import', false ); ?>
    </p>
</form>

<hr />

<div class="wpbdp-settings-form-title">
	<h3 id="help"><?php esc_html_e( 'CSV File Formatting', 'business-directory-plugin' ); ?></h3>
</div>

<p>
	<?php
	esc_html_e( 'The following are the header names to use in your CSV file for your current setup. The headers in your CSV file must EXACTLY match (case and punctuation) the names listed here. Fields such as categories or tags can appear multiple times in the file.', 'business-directory-plugin' );
	?>
</p>

<p>
	<?php
	printf(
		__( '<a href="%s">See an example CSV import file</a> to see how your file should be formatted.', 'business-directory-plugin' ),
		esc_url( admin_url( 'admin.php?page=wpbdp_admin_csv&action=example-csv' ) )
	);
	?>
</p>

<table class="wpbdp-csv-import-headers wp-list-table widefat striped fixed">
    <thead>
        <tr>
			<th class="header-name"><?php esc_html_e( 'Header name/label', 'business-directory-plugin' ); ?></th>
			<th class="field-label"><?php esc_html_e( 'Field', 'business-directory-plugin' ); ?></th>
			<th class="field-type"><?php esc_html_e( 'Type', 'business-directory-plugin' ); ?></th>
			<th class="field-is-required"><?php esc_html_e( 'Required?', 'business-directory-plugin' ); ?></th>
			<th class="field-is-multivalued"><?php esc_html_e( 'Multivalued?', 'business-directory-plugin' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php $i = 0; foreach ( wpbdp_get_form_fields() as $field ) : ?>
        <?php
		if ( 'custom' === $field->get_association() ) {
			continue;
		}
        ?>
        <tr class="<?php echo $i % 2 == 0 ? 'alt' : ''; ?>">
            <td class="header-name"><?php echo esc_html( $field->get_short_name() ); ?></td>
            <td class="field-label"><?php echo esc_html( $field->get_label() ); ?></td>
            <td class="field-type"><?php echo esc_html( $field->get_field_type()->get_name() ); ?></td>
            <td class="field-is-required"><?php echo $field->is_required() ? 'X' : ''; ?></td>
            <td class="field-is-multivalued">
				<?php echo ( $field->get_association() === 'category' || $field->get_association() === 'tags' ) || ( $field->get_field_type_id() === 'checkbox' || $field->get_field_type_id() === 'multiselect' ) ? 'X' : ''; ?>
            </td>
        </tr>
		<?php
		$i++;
	endforeach;
	?>
        <tr class="<?php echo $i % 2 == 0 ? 'alt' : ''; ?>">
            <td class="header-name">fee_id</td>
            <td class="field-label"><?php _ex( 'Fee ID (integer) associated to a listing. Use this column when adding or updating listings from external sources.', 'admin csv-import', 'business-directory-plugin' ); ?></td>
            <td class="field-type">-</td>
            <td class="field-is-required"></td>
            <td class="field-is-multivalued"></td>
        </tr>
		<tr class="<?php echo ( $i + 1 ) % 2 == 0 ? 'alt' : ''; ?>">
            <td class="header-name">images</td>
            <td class="field-label"><?php esc_html_e( 'Semicolon separated list of listing images (from the ZIP file)', 'business-directory-plugin' ); ?></td>
            <td class="field-type">-</td>
            <td class="field-is-required"></td>
            <td class="field-is-multivalued">X</td>
        </tr>
		<tr class="<?php echo ( $i + 2 ) % 2 == 0 ? 'alt' : ''; ?>">
            <td class="header-name">username</td>
			<td class="field-label"><?php esc_html_e( 'Listing owner\'s username', 'business-directory-plugin' ); ?></td>
            <td class="field-type">-</td>
            <td class="field-is-required"></td>
            <td class="field-is-multivalued"></td>
        </tr>
		<tr class="<?php echo ( $i + 3 ) % 2 == 0 ? 'alt' : ''; ?>">
            <td class="header-name">sequence_id</td>
            <td class="field-label"><?php _ex( 'Internal Sequence ID used to allow listing updates from external sources.', 'admin csv-import', 'business-directory-plugin' ); ?></td>
            <td class="field-type">-</td>
            <td class="field-is-required"></td>
            <td class="field-is-multivalued"></td>
        </tr>
		<tr class="<?php echo ( $i + 4 ) % 2 == 0 ? 'alt' : ''; ?>">
            <td class="header-name">expires_on</td>
            <td class="field-label"><?php _ex( 'Date of listing expiration formatted as YYYY-MM-DD. Use this column when adding or updating listings from external sources.', 'admin csv-import', 'business-directory-plugin' ); ?></td>
            <td class="field-type">-</td>
            <td class="field-is-required"></td>
            <td class="field-is-multivalued"></td>
        </tr>
    </tbody>
</table>

</div>
