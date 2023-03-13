<?php
$original_uri = wpbdp_get_server_value( 'REQUEST_URI' );
$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'tab', 'subtab' ) );

WPBDP_Admin_Pages::show_tabs(
    array(
		'title'      => __( 'Directory Settings', 'business-directory-plugin' ),
		'echo'       => true,
		'tabs'       => $tabs,
		'active_tab' => $active_tab,
    )
);

$this_tab   = isset( $tabs[ $active_tab ] ) ? $tabs[ $active_tab ] : $active_tab;
$page_title = is_array( $this_tab ) && ! empty( $this_tab['title'] ) ? $this_tab['title'] : ucfirst( $active_tab );
?>
	<?php if ( ! $custom_form ) : ?>
	<form action="options.php" method="post">
	<?php endif; ?>
	<div class="wpbdp-content-area-header">
		<h2 class="wpbdp-sub-section-title"><?php echo esc_html( $page_title ); ?></h2>

		<div class="wpbdp-content-area-header-actions">
			<?php
			if ( ! $custom_form ) :
				// Submit button shouldn't use 'submit' as name to avoid conflicts with
				// actual properties of the parent form.
				//
				// See http://kangax.github.io/domlint/
				submit_button( null, 'primary', 'save-changes' );
			endif;
			?>
		</div>
	</div>
	<div class="wpbdp-content-area-body">
		<?php if ( count( $subtabs ) > 1 ) : ?>
		<div class="wpbdp-settings-tab-subtabs wpbdp-clearfix">
			<ul class="subsubsub wpbdp-sub-menu">
				<?php
				$n = 0;
				foreach ( $subtabs as $subtab_id => $subtab ) :
					$n++;

					$subtab_url = add_query_arg( 'tab', $active_tab );
					$subtab_url = add_query_arg( 'subtab', $subtab_id, $subtab_url );
					?>
					<li>
						<a class="<?php echo $active_subtab == $subtab_id ? 'current' : ''; ?>" href="<?php echo esc_url( $subtab_url ); ?>"><?php echo esc_html( $subtab['title'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<?php WPBDP_Admin_Notices::settings_errors(); ?>

		<?php if ( $active_subtab_description ) : ?>
		<p class="wpbdp-settings-subtab-description wpbdp-setting-description"><?php echo wp_kses_post( $active_subtab_description ); ?></p>
		<?php endif; ?>

		<?php
		$_SERVER['REQUEST_URI'] = $original_uri;

		if ( ! $custom_form ) :
			settings_fields( 'wpbdp_settings' );
		endif;

		if ( $active_subtab ) {
			WPBDP_Admin_Pages::render_settings_sections( 'wpbdp_settings_subtab_' . $active_subtab );
			do_action( 'wpbdp_settings_subtab_' . $active_subtab );
		} else {
			WPBDP_Admin_Pages::render_settings_sections( 'wpbdp_settings_subtab_' . $active_tab );
		}

		if ( ! $custom_form ) :
			// Submit button shouldn't use 'submit'
			submit_button( null, 'primary', 'save-changes' );
			echo '</form>';
		endif;
		?>
	</div>

<?php WPBDP_Admin_Pages::show_tabs_footer(); ?>
