<?php

$settings = wp_parse_args( (array) $settings, array(
	'title' => '',
	'text'  => '',
) );
$title    = sanitize_text_field( $settings['title'] );

?>
<p><label for="widget-text--title"><?php _e( 'Title:', 'fl-builder' ); ?></label>
<input class="widefat" id="widget-text--title" name="widget-text[][title]" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

<p><label for="widget-text--text"><?php _e( 'Content:', 'fl-builder' ); ?></label>
<textarea class="widefat" rows="16" cols="20" id="widget-text--text" name="widget-text[][text]"><?php echo esc_textarea( $settings['text'] ); ?></textarea></p>
