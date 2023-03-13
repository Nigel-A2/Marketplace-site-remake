<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
/**
 * Review template.
 * Used to request for reviews.
 *
 * @since 5.14.3
 */
?>
<div class="wpbdp-notice notice notice-info is-dismissible wpbdp-review-notice">
	<div class="wpbdp-satisfied">
		<p>
			<?php echo esc_html( $title ); ?>
			<br/>
			<?php esc_html_e( 'Are you enjoying Business Directory Plugin?', 'business-directory-plugin' ); ?>
		</p>
		<a href="#" class="wpbdp_reverse_button wpbdp_animate_bg show-wpbdp-feedback wpbdp-button-secondary" data-link="feedback">
			<?php esc_html_e( 'Not Really', 'business-directory-plugin' ); ?>
		</a>
		<a href="#" class="wpbdp_animate_bg show-wpbdp-feedback wpbdp-button-primary" data-link="review">
			<?php esc_html_e( 'Yes!', 'business-directory-plugin' ); ?>
		</a>
	</div>
	<div class="wpbdp-review-request hidden">
		<p><?php esc_html_e( 'Awesome! Could you do me a BIG favor and give Business Directory Plugin a review to help me grow my little business and boost our motivation?', 'business-directory-plugin' ); ?></p>
		<p>- Steph Wells<br/>
			<span><?php esc_html_e( 'Co-Founder and CTO of Business Directory Plugin', 'business-directory-plugin' ); ?><span>
		</p>
		<a href="#" class="wpbdp-dismiss-review-notice wpbdp_reverse_button wpbdp-button-secondary" data-link="no" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'No thanks, maybe later', 'business-directory-plugin' ); ?>
		</a>
		<a href="https://wordpress.org/support/plugin/business-directory-plugin/reviews/?filter=5#new-post" class="wpbdp-dismiss-review-notice wpbdp-review-out wpbdp-button-primary" data-link="yes" target="_blank" rel="noopener">
			<?php esc_html_e( 'Ok, you deserve it', 'business-directory-plugin' ); ?>
		</a>
		<br/>
		<a href="#" class="wpbdp-dismiss-review-notice" data-link="done" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'I already did', 'business-directory-plugin' ); ?>
		</a>
	</div>
	<div class="wpbdp-feedback-request hidden">
		<p><?php esc_html_e( 'Sorry to hear you aren\'t enjoying building with Business Directory Plugin. We would love a chance to improve. Could you take a minute and let us know what we can do better?', 'business-directory-plugin' ); ?></p>

		<div id="wpbdpapi-feedback" class="wpbdpapi-form" data-url="https://services.strategy11.com/wp-json/frm/v2/forms/bd-feedback?return=html&exclude_script=jquery">
			<span class="wpbdp-wait wpbdp_visible_spinner"></span>
		</div>
	</div>
</div>
<script>
	jQuery( document ).ready( function( $ ) {
		$( document ).on( 'click', '.wpbdp-dismiss-review-notice, .wpbdp-review-notice .notice-dismiss', function( event ) {

			if ( ! $( this ).hasClass( 'wpbdp-review-out' ) ) {
				event.preventDefault();
			}
			var link = $( this ).data( 'link' );
			if ( typeof link === 'undefined' ) {
				link = 'no';
			}

			wpbdpDismissReview( link );
			$( '.wpbdp-review-notice' ).remove();
		} );

		$( document ).on( 'click', '.wpbdp-feedback-request button', function() {
			wpbdpDismissReview( 'done' );
		} );

		$( document ).on( 'click', '.show-wpbdp-feedback', function( e ) {
			e.preventDefault();
			var link = $( this ).data( 'link' );
			var className = '.wpbdp-' + link + '-request';
			jQuery( '.wpbdp-satisfied' ).hide();
			jQuery( className ).show();
			if ( className === '.wpbdp-feedback-request' ) {
				var wpbdpapi = $('#wpbdpapi-feedback');
				wpbdpapiGetData( wpbdpapi );
			}
		} );
	} );

	function wpbdpDismissReview( link ) {
		jQuery.post( ajaxurl, {
			action: 'wpbdp_dismiss_review',
			link: link,
			nonce: '<?php echo esc_html( wp_create_nonce( 'wpbdp_dismiss_review' ) ); ?>'
		} );
	}
	function wpbdpapiGetData( frmcont ) {
		jQuery.ajax( {
			dataType:'json',
			url:frmcont.data( 'url' ),
			success:function( json ) {
				var form = json.renderedHtml;
				frmcont.html( form );
			}
		} );
	}
</script>
