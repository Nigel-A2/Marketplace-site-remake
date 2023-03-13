<div class="fl-builder-buttons">
	<?php if ( 'fl-builder-template' === get_post_type() ) : ?>
		<input type="hidden" name="fl-builder-template-redirect" value="<?php echo esc_attr( $_GET['post'] ); ?>">
		<input id="fl-builder-launch" type="hidden" name="fl-builder-launch" value="">
		<input id="fl-builder-launch-button" type="submit" name="fl-builder-template-save" value="<?php echo esc_attr( $edit ); ?>" class="fl-launch-builder button button-primary button-large">
	<?php else : ?>
		<a href="<?php echo FLBuilderModel::get_edit_url(); ?>" class="fl-launch-builder button button-primary button-large"><?php echo $edit; ?></a>
	<?php endif; ?>

	<a href="<?php echo get_permalink(); ?>" class="fl-view-template button button-large"><?php echo $view; ?></a>
</div>
