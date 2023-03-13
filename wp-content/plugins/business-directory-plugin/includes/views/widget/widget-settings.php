<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
		<?php esc_html_e( 'Title:', 'business-directory-plugin' ); ?>
	</label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $this->get_field_value( $instance, 'title' ) ); ?>" />
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_listings' ) ); ?>">
		<?php esc_html_e( 'Number of listings to display:', 'business-directory-plugin' ); ?>
	</label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_of_listings' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_listings' ) ); ?>" type="text" value="<?php echo esc_attr( $this->get_field_value( $instance, 'number_of_listings' ) ); ?>" />
</p>
<?php $this->_form( $instance ); ?>

<?php if ( in_array( 'images', $this->supports ) ) : ?>

	<?php $class_name = $this->get_field_value( $instance, 'show_images' ) ? '' : 'hidden'; ?>
	<h4><?php esc_html_e( 'Thumbnails', 'business-directory-plugin' ); ?></h4>
	<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'show_images' ) ); ?>" class="wpbdp-toggle-images" name="<?php echo esc_attr( $this->get_field_name( 'show_images' ) ); ?>" type="checkbox" value="1" <?php checked( $this->get_field_value( $instance, 'show_images' ), true ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'show_images' ) ); ?>">
			<?php esc_html_e( 'Show thumbnails', 'business-directory-plugin' ); ?>
		</label>
	</p>
	<div class="thumbnail-width-config <?php echo esc_attr( $class_name ); ?>">
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'default_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'default_image' ) ); ?>" type="checkbox" value="1" <?php checked( $this->get_field_value( $instance, 'default_image' ), true ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'default_image' ) ); ?>">
				<?php esc_html_e( 'Use "Coming Soon" photo for listings without an image?', 'business-directory-plugin' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbnail_width' ) ); ?>">
				<?php esc_html_e( 'Image width (in px):', 'business-directory-plugin' ); ?>
			</label>
			<input type="number" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail_width' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'thumbnail_width' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( $instance, 'thumbnail_width' ) ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Automatic', 'business-directory-plugin' ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbnail_height' ) ); ?>">
				<?php esc_html_e( 'Image height (in px):', 'business-directory-plugin' ); ?>
			</label>
			<input type="number" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail_height' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'thumbnail_height' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( $instance, 'thumbnail_height' ) ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Automatic', 'business-directory-plugin' ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbnail_desktop' ) ); ?>">
				<?php esc_html_e( 'Thumbnail Position (Desktop):', 'business-directory-plugin' ); ?>
			</label>
			<br/>
			<select name="<?php echo esc_attr( $this->get_field_name( 'thumbnail_desktop' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'thumbnail_desktop' ) ); ?>">
				<option value="left" <?php selected( $this->get_field_value( $instance, 'thumbnail_desktop' ), 'left' ); ?>>
					<?php esc_html_e( 'Left', 'business-directory-plugin' ); ?>
				</option>
				<option value="right" <?php selected( $this->get_field_value( $instance, 'thumbnail_desktop' ), 'right' ); ?>>
					<?php esc_html_e( 'Right', 'business-directory-plugin' ); ?>
				</option>
				<option value="above" <?php selected( $this->get_field_value( $instance, 'thumbnail_desktop' ), 'above' ); ?>>
					<?php esc_html_e( 'Top', 'business-directory-plugin' ); ?>
				</option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbnail_mobile' ) ); ?>">
				<?php esc_html_e( 'Thumbnail Position (Mobile):', 'business-directory-plugin' ); ?>
			</label>
			<br/>
			<select name="<?php echo esc_attr( $this->get_field_name( 'thumbnail_mobile' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'thumbnail_mobile' ) ); ?>">
				<option value="left" <?php selected( $this->get_field_value( $instance, 'thumbnail_mobile' ), 'left' ); ?>>
					<?php esc_html_e( 'Left', 'business-directory-plugin' ); ?>
				</option>
				<option value="right" <?php selected( $this->get_field_value( $instance, 'thumbnail_mobile' ), 'right' ); ?>>
					<?php esc_html_e( 'Right', 'business-directory-plugin' ); ?>
				</option>
				<option value="above" <?php selected( $this->get_field_value( $instance, 'thumbnail_mobile' ), 'above' ); ?>>
					<?php esc_html_e( 'Top', 'business-directory-plugin' ); ?>
				</option>
			</select>
		</p>
	</div>

<?php endif; ?>
<h4><?php esc_html_e( 'Fields To Show', 'business-directory-plugin' ); ?></h4>
<div class="wpbdp-widget-listing-fields wpbdp-scrollbox">
	<ul class="wpbdp-plain-list">
	<?php foreach ( wpbdp_get_form_fields( array( 'association' => array( 'meta', 'custom' ) ) ) as $field ) : ?>
		<li>
			<input id="<?php echo esc_attr( $this->get_field_id( $field->get_id() ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'fields' ) ); ?>[]" type="checkbox" value="<?php echo esc_attr( $field->get_id() ); ?>" <?php echo ( in_array( $field->get_id(), $this->get_field_value( $instance, 'fields' ) ) ) ? 'checked="checked"' : ''; ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( $field->get_id() ) ); ?>">
				<?php echo esc_attr( $field->get_label() ); ?>
			</label>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
