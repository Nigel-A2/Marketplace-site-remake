<?php

// Default Settings
$defaults = array(
	'data_source' => 'custom_query',
	'post_type'   => 'post',
	'order_by'    => 'date',
	'order'       => 'DESC',
	'offset'      => 0,
	'users'       => '',
);

$tab_defaults = isset( $tab['defaults'] ) ? $tab['defaults'] : array();
$settings     = (object) array_merge( $defaults, $tab_defaults, (array) $settings );
/**
 * Allow extension of default Values
 * @see fl_builder_loop_settings
 */
$settings = apply_filters( 'fl_builder_loop_settings', $settings );

/**
 * e.g Add custom FLBuilder::render_settings_field()
 * @see fl_builder_loop_settings_before_form
 */
do_action( 'fl_builder_loop_settings_before_form', $settings );

?>
<div id="fl-builder-settings-section-source" class="fl-loop-data-source-select fl-builder-settings-section">
	<table class="fl-form-table">
	<?php

	// Data Source
	FLBuilder::render_settings_field('data_source', array(
		'type'    => 'select',
		'label'   => __( 'Source', 'fl-builder' ),
		'default' => 'custom_query',
		'options' => array(
			'custom_query' => __( 'Custom Query', 'fl-builder' ),
			'main_query'   => __( 'Main Query', 'fl-builder' ),
		),
		'toggle'  => array(
			'custom_query' => array(
				'fields' => array( 'posts_per_page' ),
			),
		),
	), $settings);

	?>
	</table>
</div>
<div class="fl-custom-query fl-loop-data-source" data-source="custom_query">
	<div id="fl-builder-settings-section-general" class="fl-builder-settings-section">
		<h3 class="fl-builder-settings-title">
			<span class="fl-builder-settings-title-text-wrap"><?php _e( 'Custom Query', 'fl-builder' ); ?></span>
		</h3>
		<table class="fl-form-table">
		<?php

		// Post type
		FLBuilder::render_settings_field('post_type', array(
			'type'  => 'post-type',
			'label' => __( 'Post Type', 'fl-builder' ),
		), $settings);

		// Order
		FLBuilder::render_settings_field('order', array(
			'type'    => 'select',
			'label'   => __( 'Order', 'fl-builder' ),
			'options' => array(
				'DESC' => __( 'Descending', 'fl-builder' ),
				'ASC'  => __( 'Ascending', 'fl-builder' ),
			),
		), $settings);

		// Order by
		FLBuilder::render_settings_field('order_by', array(
			'type'    => 'select',
			'label'   => __( 'Order By', 'fl-builder' ),
			'options' => array(
				'author'         => __( 'Author', 'fl-builder' ),
				'comment_count'  => __( 'Comment Count', 'fl-builder' ),
				'date'           => __( 'Date', 'fl-builder' ),
				'modified'       => __( 'Date Last Modified', 'fl-builder' ),
				'ID'             => __( 'ID', 'fl-builder' ),
				'menu_order'     => __( 'Menu Order', 'fl-builder' ),
				'meta_value'     => __( 'Meta Value (Alphabetical)', 'fl-builder' ),
				'meta_value_num' => __( 'Meta Value (Numeric)', 'fl-builder' ),
				'rand'           => __( 'Random', 'fl-builder' ),
				'title'          => __( 'Title', 'fl-builder' ),
				'post__in'       => __( 'Selection Order', 'fl-builder' ),
			),
			'toggle'  => array(
				'meta_value'     => array(
					'fields' => array( 'order_by_meta_key' ),
				),
				'meta_value_num' => array(
					'fields' => array( 'order_by_meta_key' ),
				),
			),
		), $settings);

		// Meta Key
		FLBuilder::render_settings_field('order_by_meta_key', array(
			'type'  => 'text',
			'label' => __( 'Meta Key', 'fl-builder' ),
		), $settings);

		// Offset
		FLBuilder::render_settings_field('offset', array(
			'type'        => 'unit',
			'label'       => _x( 'Offset', 'How many posts to skip.', 'fl-builder' ),
			'default'     => '0',
			'placeholder' => '0',
			'sanitize'    => 'absint',
			'slider'      => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 2,
			),
			'help'        => __( 'Skip this many posts that match the specified criteria.', 'fl-builder' ),
		), $settings);

		FLBuilder::render_settings_field('exclude_self', array(
			'type'    => 'select',
			'label'   => __( 'Exclude Current Post', 'fl-builder' ),
			'default' => 'no',
			'help'    => __( 'Exclude the current post from the query.', 'fl-builder' ),
			'options' => array(
				'yes' => __( 'Yes', 'fl-builder' ),
				'no'  => __( 'No', 'fl-builder' ),
			),
		), $settings);

		?>
		</table>
	</div>
	<div id="fl-builder-settings-section-filter" class="fl-builder-settings-section">
		<h3 class="fl-builder-settings-title">
			<span class="fl-builder-settings-title-text-wrap"><?php _e( 'Filter', 'fl-builder' ); ?></span>
		</h3>
		<?php foreach ( FLBuilderLoop::post_types() as $slug => $type ) : ?>
			<table class="fl-form-table fl-custom-query-filter fl-custom-query-<?php echo $slug; ?>-filter"<?php echo ( $slug == $settings->post_type ) ? ' style="display:table;"' : ''; ?>>
			<?php

			// Posts
			FLBuilder::render_settings_field( 'posts_' . $slug, array(
				'type'     => 'suggest',
				'action'   => 'fl_as_posts',
				'data'     => $slug,
				'label'    => $type->label,
				/* translators: %s: type label */
				'help'     => sprintf( __( 'Enter a list of %1$s.', 'fl-builder' ), $type->label ),
				'matching' => true,
			), $settings );

			// Taxonomies
			$taxonomies = FLBuilderLoop::taxonomies( $slug );

			foreach ( $taxonomies as $tax_slug => $tax ) {

				FLBuilder::render_settings_field( 'tax_' . $slug . '_' . $tax_slug, array(
					'type'     => 'suggest',
					'action'   => 'fl_as_terms',
					'data'     => $tax_slug,
					'label'    => $tax->label,
					/* translators: %s: tax label */
					'help'     => sprintf( __( 'Enter a list of %1$s.', 'fl-builder' ), $tax->label ),
					'matching' => true,
				), $settings );
			}

			?>
			</table>
		<?php endforeach; ?>
		<table class="fl-form-table">
		<?php

		// Author
		FLBuilder::render_settings_field('users', array(
			'type'     => 'suggest',
			'action'   => 'fl_as_users',
			'label'    => __( 'Authors', 'fl-builder' ),
			'help'     => __( 'Enter a list of authors usernames.', 'fl-builder' ),
			'matching' => true,
		), $settings);

		?>
		</table>
	</div>
</div>
<?php
do_action( 'fl_builder_loop_settings_after_form', $settings ); // e.g Add custom FLBuilder::render_settings_field()
