<div class="wrap fl-builder-template-data-exporter">
	<h1><?php _e( 'Template Data Exporter', 'fl-builder' ); ?></h1>
	<p><?php _e( 'This tool exports a special data file that can be used by developers to include templates within their themes and plugins.', 'fl-builder' ); ?></p>
	<?php /* translators: %s: admin export url */ ?>
	<p><?php printf( __( 'If you need to export templates for import into another site, please use the exporter at <a href="%s">Tools > Export</a>.', 'fl-builder' ), admin_url( '/export.php' ) ); ?></p>
	<form method="POST">

		<?php if ( defined( 'FL_THEME_BUILDER_VERSION' ) ) : ?>
		<div class="fl-builder-template-data-section fl-builder-template-data-theme">

			<h2><?php _e( 'Themer Layouts', 'fl-builder' ); ?></h2>

			<?php if ( 0 === count( $theme ) ) : ?>
			<p><?php _e( 'No Themer Layouts Found', 'fl-builder' ); ?></p>
			<?php else : ?>
			<p><label><input type="checkbox" name="fl-builder-template-data-exporter-all" value="1" /><?php _e( 'Select All', 'fl-builder' ); ?></label></p>
			<?php endif; ?>

			<?php foreach ( $theme as $layout ) : ?>
			<p><label><input type="checkbox" class="fl-builder-template-data-checkbox" name="fl-builder-export-theme[]" value="<?php echo $layout['id']; ?>" /> <?php echo $layout['name']; ?></label></p>
			<?php endforeach; ?>

		</div>
		<?php endif; ?>

		<div class="fl-builder-template-data-section fl-builder-template-data-layouts">

			<h2><?php _e( 'Layout Templates', 'fl-builder' ); ?></h2>

			<?php if ( 0 === count( $layouts ) ) : ?>
			<p><?php _e( 'No Layout Templates Found', 'fl-builder' ); ?></p>
			<?php else : ?>
			<p><label><input type="checkbox" name="fl-builder-template-data-exporter-all" value="1" /><?php _e( 'Select All', 'fl-builder' ); ?></label></p>
			<?php endif; ?>

			<?php foreach ( $layouts as $layout ) : ?>
			<p><label><input type="checkbox" class="fl-builder-template-data-checkbox" name="fl-builder-export-layout[]" value="<?php echo $layout['id']; ?>" /> <?php echo $layout['name']; ?></label></p>
			<?php endforeach; ?>

		</div>

		<div class="fl-builder-template-data-section fl-builder-template-data-rows">

			<h2><?php _e( 'Rows', 'fl-builder' ); ?></h2>

			<?php if ( 0 === count( $rows ) ) : ?>
			<p><?php _e( 'No Rows Found', 'fl-builder' ); ?></p>
			<?php else : ?>
			<p><label><input type="checkbox" name="fl-builder-template-data-exporter-all" value="1" /><?php _e( 'Select All', 'fl-builder' ); ?></label></p>
			<?php endif; ?>

			<?php foreach ( $rows as $row ) : ?>
			<p><label><input type="checkbox" class="fl-builder-template-data-checkbox" name="fl-builder-export-row[]" value="<?php echo $row['id']; ?>" /> <?php echo $row['name']; ?></label></p>
			<?php endforeach; ?>

		</div>

		<div class="fl-builder-template-data-section fl-builder-template-data-columns">

			<h2><?php _e( 'Columns', 'fl-builder' ); ?></h2>

			<?php if ( 0 === count( $columns ) ) : ?>
			<p><?php _e( 'No Columns Found', 'fl-builder' ); ?></p>
			<?php else : ?>
			<p><label><input type="checkbox" name="fl-builder-template-data-exporter-all" value="1" /><?php _e( 'Select All', 'fl-builder' ); ?></label></p>
			<?php endif; ?>

			<?php foreach ( $columns as $column ) : ?>
			<p><label><input type="checkbox" class="fl-builder-template-data-checkbox" name="fl-builder-export-column[]" value="<?php echo $column['id']; ?>" /> <?php echo $column['name']; ?></label></p>
			<?php endforeach; ?>

		</div>

		<div class="fl-builder-template-data-section fl-builder-template-data-modules">

			<h2><?php _e( 'Modules', 'fl-builder' ); ?></h2>

			<?php if ( 0 === count( $modules ) ) : ?>
			<p><?php _e( 'No Modules Found', 'fl-builder' ); ?></p>
			<?php else : ?>
			<p><label><input type="checkbox" name="fl-builder-template-data-exporter-all" value="1" /><?php _e( 'Select All', 'fl-builder' ); ?></label></p>
			<?php endif; ?>

			<?php foreach ( $modules as $module ) : ?>
			<p><label><input type="checkbox" class="fl-builder-template-data-checkbox" name="fl-builder-export-module[]" value="<?php echo $module['id']; ?>" /> <?php echo $module['name']; ?></label></p>
			<?php endforeach; ?>

		</div>

		<?php foreach ( $other as $section ) : ?>
		<div class="fl-builder-template-data-section fl-builder-template-data-<?php echo $section['key']; ?>">

			<h2><?php echo $section['title']; ?></h2>

			<?php if ( 0 === count( $section['posts'] ) ) : ?>
				<?php /* translators: %s: Data export section title. */ ?>
			<p><?php echo sprintf( _x( 'No %s Found', 'Data export section title.', 'fl-builder' ), $section['title'] ); ?></p>
			<?php else : ?>
			<p><label><input type="checkbox" name="fl-builder-template-data-exporter-all" value="1" /><?php _e( 'Select All', 'fl-builder' ); ?></label></p>
			<?php endif; ?>

			<?php foreach ( $section['posts'] as $post ) : ?>
			<p><label><input type="checkbox" class="fl-builder-template-data-checkbox" name="fl-builder-export-<?php echo $section['key']; ?>[]" value="<?php echo $post->ID; ?>" /> <?php echo $post->post_title; ?></label></p>
			<?php endforeach; ?>

		</div>
		<?php endforeach; ?>

		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php _e( 'Export Template Data', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'fl-builder-template-data-exporter', 'fl-builder-template-data-exporter-nonce' ); ?>
		</p>
	</form>
</div>
