<?php do_action( 'fl_builder_before_ui_bar_title' ); ?>
<span class="<?php echo implode( ' ', $wrapper_classes ); ?>">
	<?php if ( '' != $icon_url ) { ?>
	<div class="fl-builder-bar-title-icon">
		<img src="<?php echo $icon_url; ?>" />
	</div>
	<?php } ?>
	<div class="fl-builder-bar-title-area">
		<div class="fl-builder-layout-pretitle"><?php echo $pretitle; ?></div>
		<div class="fl-builder-layout-title" title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></div>
	</div>
	<?php if ( ! $simple_ui ) { ?>
	<button class="fl-builder-button fl-builder-button-silent fl-builder-bar-title-caret" title="<?php _e( 'Toggle Main Menu', 'fl-builder' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="30px" width="30px">
			<path d="M5 6l5 5 5-5 2 1-7 7-7-7z"/>
		</svg>
	</button>
	<?php } ?>
</span>
<?php do_action( 'fl_builder_after_ui_bar_title' ); ?>
