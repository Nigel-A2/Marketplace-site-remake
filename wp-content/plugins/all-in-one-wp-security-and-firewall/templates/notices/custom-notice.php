<?php if (!defined('AIO_WP_SECURITY_PATH')) die('No direct access allowed'); ?>

<div class="aiowps_ad_container error">
	<div class="aiowps_notice_container">
		<div class="aiowps_advert_content_right">
			<h3 class="aiowps_advert_heading">
				<?php echo $title; ?>
				<div class="aiowps_advert_dismiss">
				<?php if (!empty($dismiss_time)) { ?>
					<a href="#" onclick="jQuery(this).closest('.aiowps_ad_container').slideUp(); jQuery.post(ajaxurl, {action: 'aiowps_ajax', subaction: '<?php echo $dismiss_time;?>', nonce: '<?php echo wp_create_nonce('wp-security-ajax-nonce');?>' });"><?php _e('Dismiss', 'all-in-one-wp-security-and-firewall'); ?></a>
				<?php } else { ?>
					<a href="#" onclick="jQuery(this).closest('.aiowps_ad_container').slideUp();"><?php _e('Dismiss', 'all-in-one-wp-security-and-firewall'); ?></a>
				<?php } ?>
				</div>
			</h3>
			<p>
				<?php echo $text; ?>
			</p>
			<?php
			if (!empty($button_link) && !empty($button_meta)) {
			?>
			<p>
				<a class="aiowps_notice_link button button-primary" href="<?php esc_attr_e($button_link);?>">
					<?php echo $button_meta; ?>
				</a>
				<a class="aiowps_notice_link button button-secondary" style="margin-left: 8px;" href="#" onclick="jQuery(this).closest('.aiowps_ad_container').slideUp(); jQuery.post(ajaxurl, {action: 'aiowps_ajax', subaction: '<?php echo $dismiss_time;?>', nonce: '<?php echo wp_create_nonce('wp-security-ajax-nonce');?>', dismiss_forever: '1' });">
					<?php _e('No', 'all-in-one-wp-security-and-firewall'); ?>
				</a>
			</p>
			<?php
			}
			?>

		</div>
	</div>
	<div class="clear"></div>
</div>