<?php
WPBDP_Admin_Pages::show_tabs(
	array(
		'id'      => 'debug-info',
		'sub'     => __( 'Debug', 'business-directory-plugin' ),
		'buttons' => array(
			'addfield'    => array(
				'label' => __( 'Download Debug Information', 'business-directory-plugin' ),
				'url'   => admin_url( 'admin.php?page=wpbdp-debug-info&download=1' ),
			),
		),
	)
);
?>

<div id="wpbdp-admin-debug-info-page" class="wpbdp-admin-debug-info-page">
<p>
	<?php esc_html_e( 'The following information can help our team debug possible problems with your setup.', 'business-directory-plugin' ); ?>
</p>

<?php if ( count( $debug_info ) > 1 ) : ?>
<div class="wpbdp-settings-tab-subtabs wpbdp-clearfix">
	<ul class="subsubsub wpbdp-sub-menu">
		<?php foreach ( $debug_info as $section_id => &$section ) : ?>
			<li><a class="current-nav" href="<?php echo esc_attr( $section_id ); ?>"><?php echo esc_html( $section['_title'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<?php foreach ( $debug_info as $section_id => &$section ) : ?>
<table class="wpbdp-debug-section wp-list-table striped widefat fixed" data-id="<?php echo esc_attr( $section_id ); ?>" style="<?php echo count( $debug_info ) > 1 ? 'display: none;' : ''; ?>">
	<tbody>
		<?php
		foreach ( $section as $k => $v ) :
			if ( wpbdp_starts_with( $k, '_' ) ) {
				continue;
			}
            ?>
		<tr>
			<th scope="row"><?php echo esc_attr( $k ); ?></th>
            <td>
				<?php
				if ( is_array( $v ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo isset( $v['html'] ) ? $v['html'] : esc_attr( $v['value'] );
				} else {
					echo esc_attr( $v );
				}
				?>
            </td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endforeach; ?>
</div>

<?php echo wpbdp_admin_footer(); ?>
