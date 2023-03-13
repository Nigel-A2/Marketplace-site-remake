<?php
$action = isset( $action ) ? $action : '';

$reasons = array(
    '1' => _x( 'It doesn\'t work with my theme/plugins/site', 'uninstall', 'business-directory-plugin' ),
    '2' => _x( 'I can\'t set it up/Too complicated', 'uninstall', 'business-directory-plugin' ),
    '3' => _x( 'Doesn\'t solve my problem', 'uninstall', 'business-directory-plugin' ),
    '4' => _x( 'Don\'t need it anymore/Not using it', 'uninstall', 'business-directory-plugin' ),
    '0' => _x( 'Other', 'uninstall', 'business-directory-plugin' )
);
?>

<form id="wpbdp-uninstall-capture-form" action="<?php echo esc_attr( $action ); ?>" method="post">
    <?php wp_nonce_field( 'uninstall bd' ); ?>

    <p><?php esc_html_e( 'We\'re sorry to see you leave. Could you take 10 seconds and answer one question for us to help us make the product better for everyone in the future?', 'business-directory-plugin' ); ?></p>
	<p><b><?php esc_html_e( 'Why are you deleting Business Directory Plugin?', 'business-directory-plugin' ); ?></b></p>

	<?php foreach ( $reasons as $r => $l ) : ?>
	<div class="reason">
		<label>
			<input type="radio" name="uninstall[reason_id]" value="<?php echo esc_attr( (string) $r ); ?>" /> <?php echo esc_html( $l ); ?>
		</label>

		<?php if ( 0 == $r ) : ?>
		<div class="custom-reason">
			<textarea name="uninstall[reason_text]" placeholder="<?php esc_attr_e( 'Please tell us why are you deleting Business Directory Plugin.', 'business-directory-plugin' ); ?>"></textarea>
		</div>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>

	<p>
		<input type="submit" value="<?php esc_attr_e( 'Uninstall Plugin', 'business-directory-plugin' ); ?>" class="button button-primary" />
    </p>
</form>
