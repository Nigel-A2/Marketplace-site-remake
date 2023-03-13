<?php
wpbdp_admin_header(
    array(
        'id'      => 'themes',
        'buttons' => array(
            'theme-install' => array(
                'label' => __( 'Upload Directory Theme', 'business-directory-plugin' ),
                'url'   => admin_url( 'admin.php?page=wpbdp-themes&action=theme-install' ),
            ),
            'updatetags'    => array(
                'label' => __( 'Manage Theme Tags', 'business-directory-plugin' ),
                'url'   => admin_url( 'admin.php?page=wpbdp_admin_formfields&action=updatetags' )
            ),
        ),
		'sidebar' => false,
        'echo'    => true,
    )
);
wpbdp_admin_notices();
?>

<p class="howto">
<?php

echo sprintf(
    // translators: %1$s is opening <a> tag, %2$s is closing </a> tag
    esc_html__( '%1$sDirectory Themes%2$s change the look of the directory. A number of them are %1$savailable for purchase%2$s.', 'business-directory-plugin' ),
    '<a href="https://businessdirectoryplugin.com/premium-themes/" target="_blank" rel="noopener">',
    '</a>'
);
echo ' ';
esc_html_e( 'They are not a replacement for regular WordPress themes.', 'business-directory-plugin' );
?>
</p>
<br/>

<div id="wpbdp-theme-selection" class="wpbdp-addons wpbdp-theme-selection">
<?php foreach ( $themes as &$t ) : ?>
    <?php
    wpbdp_render_page(
        WPBDP_PATH . 'templates/admin/themes-item.tpl.php',
        array(
            'theme' => $t,
            'is_outdated' => in_array( $t->id, $outdated_themes ),
        ),
        true
    );
    ?>
<?php endforeach; ?>
</div>

<?php wpbdp_admin_footer( 'echo' ); ?>
