<?php _deprecated_file( esc_html( basename( __FILE__ ) ), 'Unknown' ); ?>

<div id="wpbdp-main-page" class="wpbdp-main-page wpbdp-main businessdirectory wpbdp-page <?php echo esc_attr( join( ' ', $__page__['class'] ) ); ?>">

    <?php wpbdp_the_main_box(); ?>

    <?php echo $__page__['before_content']; ?>

	<div class="wpbdp-page-content <?php echo esc_attr( join( ' ', $__page__['content_class'] ) ); ?>">
        <div id="wpbdp-categories" class="cf">
            <?php wpbdp_the_directory_categories(); ?>
        </div>

        <?php if ($listings) echo $listings; ?>
    </div>

</div>
