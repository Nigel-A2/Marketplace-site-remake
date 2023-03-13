<div id="fl-upgrade-form" class="fl-settings-form fl-upgrade-page-content">

	<h3 class="fl-settings-form-header"><?php _e( 'Get More Features', 'fl-builder' ); ?></h3>

	<p><?php _e( 'Along with access to our expert support team, the premium versions of Beaver Builder are packed with more features to save you time and make building websites easier!', 'fl-builder' ); ?></p>

	<h4><?php _e( 'Premium Features', 'fl-builder' ); ?></h4>

	<ul>
		<li><?php _e( 'Additional modules: Contact Form, Tabs, Slider, Pricing Table, Map, Blog Posts, Subscribe Form, Social Icons, and many more.', 'fl-builder' ); ?></li>
		<li><?php _e( 'Expert support from our world-class support team.', 'fl-builder' ); ?></li>
		<li><?php _e( 'Beautiful pre-made layout templates.', 'fl-builder' ); ?></li>
		<li><?php _e( 'Save, export, and reuse full-page layouts, rows, and modules.', 'fl-builder' ); ?></li>
		<li><?php _e( 'Build your own custom modules.', 'fl-builder' ); ?></li>
	</ul>

	<p><?php _e( 'Come by the Beaver Builder Homepage to learn more about what our premium features can do for you!', 'fl-builder' ); ?></p>

	<?php
	$upgrade_url = FLBuilderModel::get_upgrade_url( array(
		'utm_medium'   => 'bb-lite',
		'utm_source'   => 'upgrade-settings-page',
		'utm_campaign' => 'settings-upgrade-button',
	) );
	?>
	<input type="button" class="button button-primary" value="<?php _e( 'Learn More', 'fl-builder' ); ?>" onclick="window.open('<?php echo $upgrade_url; ?>');" style="margin-right: 10px;">

</div>
