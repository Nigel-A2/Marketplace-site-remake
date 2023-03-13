<?php
wpbdp_admin_header(
	array(
		'title' => __( 'Add Plan', 'business-directory-plugin' ),
		'echo'  => true,
	)
);
wpbdp_admin_notices();
wpbdp_render_page( WPBDP_PATH . 'templates/admin/fees-form.tpl.php', array( 'fee' => $fee ), true );
wpbdp_admin_footer( 'echo' );
