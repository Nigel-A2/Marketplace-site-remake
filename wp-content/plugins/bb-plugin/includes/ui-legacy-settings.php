<?php

/**
 * PLEASE NOTE: This file is only around for backwards compatibility
 * with third party settings forms that are still being rendered via
 * AJAX. Going forward, all settings forms should be rendered on the
 * frontend using FLBuilderSettingsForms.render.
 */

$id = FLBuilderModel::uniqid( 'fl-lightbox-content-placeholder' );

?>
<div id="<?php echo $id; ?>"></div>
<script class="fl-legacy-settings">

var config = <?php echo json_encode( $form ); ?>,
	wrap   = jQuery( '#<?php echo $id; ?>' ).closest( '.fl-builder-lightbox' ),
	id     = wrap.attr( 'data-instance-id' );

config.lightbox = FLLightbox._instances[ id ];

FLBuilderSettingsForms.render( config );

</script>
