<?php

/**
 * PLEASE NOTE: This file is only around for backwards compatibility
 * with third party settings forms that are still being rendered via
 * AJAX. Going forward, all settings forms should be rendered on the
 * frontend using FLBuilderSettingsForms.render.
 */

if ( isset( $field['class'] ) ) {
	$field['className'] = $field['class'];
}

ob_start();
do_action( 'fl_builder_before_control', $name, $value, $field, $settings );
do_action( 'fl_builder_before_control_' . $field['type'], $name, $value, $field, $settings );
$field['html_before'] = ob_get_clean();

ob_start();
do_action( 'fl_builder_after_control_' . $field['type'], $name, $value, $field, $settings );
do_action( 'fl_builder_after_control', $name, $value, $field, $settings );
$field['html_after'] = ob_get_clean();

?>
<tr id="fl-field-<?php echo $name; ?>"></tr>
<script>

var html   = null,
	fields = {
		'<?php echo $name; ?>' : <?php echo json_encode( $field ); ?>
	};

html = FLBuilderSettingsForms.renderFields( fields, <?php echo json_encode( $settings ); ?> );

jQuery( '#fl-field-<?php echo $name; ?>' ).after( html ).remove();

</script>
