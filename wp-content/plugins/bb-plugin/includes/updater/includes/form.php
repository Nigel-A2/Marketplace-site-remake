<?php // @codingStandardsIgnoreFile ?>
<div class="wrap">

	<?php if ( isset( $subscription->error ) && 'connection' == $subscription->error ) : ?>
	<p class="fl-license-error" style="padding:10px 20px; background: #d54e21; color: #fff;">
		<?php _e( 'ERROR! We were unable to connect to the update server. If the issue persists, please contact your host and let them know your website cannot connect to updates.wpbeaverbuilder.com.', 'fl-builder' ); ?>
	</p>
	<?php elseif ( isset( $subscription->error ) || ! $subscription->active ) : ?>
	<p class="fl-license-error" style="padding:10px 20px; background: #d54e21; color: #fff;">
		<?php _e( 'UPDATES UNAVAILABLE! Please subscribe or enter your license key below to enable automatic updates.', 'fl-builder' ); ?>
		&nbsp;<a style="color: #fff;" href="<?php echo FLBuilderModel::get_store_url( '', array(
			'utm_medium' => 'bb-pro',
			'utm_source' => 'license-settings-page',
			'utm_campaign' => 'license-expired',
		) ); ?>" target="_blank"><?php _e( 'Subscribe Now', 'fl-builder' ); ?> &raquo;</a>
	</p>
	<?php elseif ( ! $subscription->domain->active ) : ?>
	<p class="fl-license-error" style="padding:10px 20px; background: #d54e21; color: #fff;">
		<?php _e( 'UPDATES UNAVAILABLE! Your subscription is active but this domain has been deactivated. Please reactivate this domain in your account to enable automatic updates.', 'fl-builder' ); ?>
		&nbsp;<a style="color: #fff;" href="<?php echo FLBuilderModel::get_store_url( 'my-account', array(
			'utm_medium' => 'bb-pro',
			'utm_source' => 'license-settings-page',
			'utm_campaign' => 'license-deactivated',
		) ); ?>" target="_blank"><?php _e( 'Visit Account', 'fl-builder' ); ?> &raquo;</a>
	</p>
	<?php endif; ?>

	<h3 class="fl-settings-form-header">
		<?php _e( 'Updates &amp; Support Subscription', 'fl-builder' ); ?>
		<span> &mdash; </span>
		<?php if ( isset( $subscription->error ) || ! $subscription->active ) : ?>
		<i style="color:#ae5842;"><?php _e( 'Not Active!', 'fl-builder' ); ?></i>
		<?php elseif ( ! $subscription->domain->active ) : ?>
		<i style="color:#ae5842;"><?php _e( 'Deactivated!', 'fl-builder' ); ?></i>
		<?php else : ?>
		<i style="color:#3cb341;"><?php _e( 'Active!', 'fl-builder' ); ?></i>
		<?php endif; ?>
	</h3>

	<?php if ( isset( $_POST['fl-updater-nonce'] ) ) : ?>
	<div class="updated">
		<p><?php _e( 'License key saved!', 'fl-builder' ); ?></p>
	</div>
	<?php endif; ?>

	<p>
		<?php echo sprintf( __( 'Enter your <a%s>license key</a> to enable remote updates and support.', 'fl-builder' ), ' href="' . FLBuilderModel::get_store_url( 'my-account', array(
			'utm_medium' => 'bb-pro',
			'utm_source' => 'license-settings-page',
			'utm_campaign' => 'license-key-link',
		) ) . '" target="_blank"' ) ?>
	</p>

	<?php if ( is_multisite() ) : ?>
	<p>
		<strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e( 'This applies to all sites on the network.', 'fl-builder' ); ?>
	</p>
	<?php endif; ?>

	<form class="fl-license-form" action="" method="post" <?php if ( ! empty( $license ) ) { echo 'style="display:none;"';} ?>>

		<input type="password" name="license" value="" class="regular-text" />

		<p class="submit">
			<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save License Key', 'fl-builder' ); ?>">
			<?php wp_nonce_field( 'updater-nonce', 'fl-updater-nonce' ); ?>
		</p>
	</form>

	<div class="fl-new-license-form" <?php if ( empty( $license ) ) { echo 'style="display:none;"';} ?>>
		<p class="submit">
			<input type="button" class="button button-primary" value="<?php esc_attr_e( 'Enter License Key', 'fl-builder' ); ?>">
		</p>
	</div>
	<?php do_action( 'fl_after_license_form'); ?>
	<?php FLUpdater::render_subscriptions( $subscription ); ?>

</div>
