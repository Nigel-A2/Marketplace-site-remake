<div class="<?php echo $module->get_form_classes(); ?>"
	<?php
	if ( isset( $module->template_id ) ) {
		echo 'data-template-id="' . $module->template_id . '" data-template-node-id="' . $module->template_node_id . '"';}
	?>
>
	<div class="fl-search-form-wrap">
		<div class="fl-search-form-fields">
			<div class="fl-search-form-input-wrap">
				<?php

				// Renders search template
				include $module->dir . 'includes/wp-search.php';

				?>
			</div>
			<?php $module->render_button(); ?>
		</div>
	</div>
</div>
