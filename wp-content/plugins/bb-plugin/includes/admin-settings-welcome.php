<?php

function fl_welcome_utm( $campaign ) {
	return array(
		'utm_medium'   => true === FL_BUILDER_LITE ? 'bb-lite' : 'bb-pro',
		'utm_source'   => 'welcome-settings-page',
		'utm_campaign' => $campaign,
	);
}
$blog_post_url   = FLBuilderModel::get_store_url( 'beaver-builder-2-5-contra', fl_welcome_utm( 'settings-welcome-blog-post' ) );
$change_logs_url = FLBuilderModel::get_store_url( 'change-logs', fl_welcome_utm( 'settings-welcome-change-logs' ) );
$upgrade_url     = FLBuilderModel::get_upgrade_url( fl_welcome_utm( 'settings-welcome-upgrade' ) );
$support_url     = FLBuilderModel::get_store_url( 'beaver-builder-support', fl_welcome_utm( 'settings-welcome-support' ) );
$faqs_url        = FLBuilderModel::get_store_url( 'frequently-asked-questions', fl_welcome_utm( 'settings-welcome-faqs' ) );
$forums_url      = FLBuilderModel::get_store_url( 'go/forum', fl_welcome_utm( 'settings-welcome-forums' ) );
$docs_url        = FLBuilderModel::get_store_url( 'go/docs', fl_welcome_utm( 'settings-welcome-docs' ) );
$fb_url          = 'https://www.facebook.com/groups/beaverbuilders/';
$release_ver     = '2.5';
$release_name    = '&#8220;Contra&#8221;';
?>
<div id="fl-welcome-form" class="fl-settings-form">

	<h2 class="fl-settings-form-header"><?php _e( 'Welcome to Beaver Builder!', 'fl-builder' ); ?></h2>

	<div class="fl-settings-form-content fl-welcome-page-content">

		<p><?php _e( 'Thank you for choosing Beaver Builder and welcome to the colony! Find some helpful information below. Also, to the left are the Page Builder settings options.', 'fl-builder' ); ?>

			<?php if ( true === FL_BUILDER_LITE ) : ?>
				<?php /* translators: %s: upgrade url */ ?>
				<?php printf( __( 'For more time-saving features and access to our expert support team, <a href="%s" target="_blank">upgrade today</a>.', 'fl-builder' ), $upgrade_url ); ?>
			<?php else : ?>
				<?php _e( 'Be sure to add your license key for access to updates and new features.', 'fl-builder' ); ?>
			<?php endif; ?>

		</p>

		<h2><?php _e( 'Getting Started - Building your first page.', 'fl-builder' ); ?></h2>

		<div class="fl-welcome-col-wrap">

			<div class="fl-welcome-col">

				<p><a href="<?php echo admin_url(); ?>post-new.php?post_type=page" class="fl-welcome-big-link"><?php _e( 'Pages &rarr; Add New', 'fl-builder' ); ?></a></p>

				<p><?php _e( 'Ready to start building? Add a new page and jump into Beaver Builder by clicking the Launch Beaver Builder button shown on the image.', 'fl-builder' ); ?></p>

				<h3><?php _e( 'Join the Community', 'fl-builder' ); ?></h3>

				<p><?php _e( 'There\'s a wonderful community of "Beaver Builders" out there and we\'d love it if <em>you</em> joined us!', 'fl-builder' ); ?></p>

				<ul>
					<li><?php _e( '<a href="https://www.wpbeaverbuilder.com/go/bb-facebook" target="_blank">Join the Beaver Builder\'s Group on Facebook</a>', 'fl-builder' ); ?></li>
					<li><?php _e( '<a href="https://www.wpbeaverbuilder.com/go/bb-slack" target="_blank">Join the Beaver Builder\'s Group on Slack</a>', 'fl-builder' ); ?></li>
					<li><?php _e( '<a href="https://www.wpbeaverbuilder.com/go/forum" target="_blank">Join the Beaver Builder Forums</a>', 'fl-builder' ); ?></li>
				</ul>

				<p><?php _e( 'Come by and share a project, ask a question, or just say hi! For news about new features and updates, like our <a href="https://www.facebook.com/wpbeaverbuilder/" target="_blank">Facebook Page</a> or follow us <a href="https://twitter.com/beaverbuilder" target="_blank">on Twitter</a>.', 'fl-builder' ); ?></p>

				<?php if ( true === FL_BUILDER_LITE && '1' !== get_user_meta( get_current_user_id(), '_fl_welcome_subscribed', true ) ) : ?>
				<div class="subscription-form">
						<h4>Get the Latest News First</h4>
						<p>Our newsletter is personally written and sent out about once a month. It's not the least bit annoying or spammy. We promise.</p>
						<div class="input-group">
							<input class="input-group-field name" type="name" placeholder="Your Name" required />
							<input class="input-group-field email" type="email" placeholder="Your Email" required />
							<?php wp_nonce_field( 'welcome_submit' ); ?>
						</div>
						<span class="error"></span>
						<button class="subscribe-button">Get News & Updates</button><span class="dashicons dashicons-update"></span>
				</div>
			<?php endif; ?>
			</div>

			<div class="fl-welcome-col">
				<img role="presentation" class="fl-welcome-img" src="<?php echo FL_BUILDER_URL; ?>img/screenshot-getting-started.jpg" alt="" />
			</div>

		</div>

		<hr>

		<div class="fl-welcome-col-wrap">

			<div class="fl-welcome-col">

				<?php /* translators: %s: builder name and version */ ?>
				<h4><?php printf( __( "What's New in Beaver Builder", 'fl-builder' ) . ' %1$s %2$s', $release_ver, $release_name ); ?></h4>

				<?php /* translators: %1$s: version, %2$s: release name */ ?>
				<p><?php printf( __( 'We\'re thrilled to announce Beaver Builder %1$s %2$s. Beaver Builder %1$s brings a number of workflow enhancements.', 'fl-builder' ), $release_ver, $release_name ); ?></p>

				<ul>
					<li><?php _e( 'NEW: We\'ve added an Outline Panel where you can view and edit the structure of a page from a streamlined interface.', 'fl-builder' ); ?></li>
					<li><?php _e( 'The Menu Module received new improvements: center logo, add search to the menu and more!', 'fl-builder' ); ?></li>
					<li><?php _e( 'The Pricing Table Module now has the ability to show dual pricing, ribbons on the boxes and we\'ve added new styling options.', 'fl-builder' ); ?></li>
					<li><?php _e( 'Icons: Add integration with the FontAwesome plugin.', 'fl-builder' ); ?></li>
				</ul>
				<?php /* translators: 1: blog post url: 2: changelog url */ ?>
				<p><?php printf( __( 'There\'s a whole lot more, too! Read about everything else on our <a href="%1$s" target="_blank">update post</a> or <a href="%2$s" target="_blank">change logs</a>.', 'fl-builder' ), $blog_post_url, $change_logs_url ); ?></p>

			</div>

			<div class="fl-welcome-col">

				<h4><?php _e( 'Need Some Help?', 'fl-builder' ); ?></h4>

				<p><?php _e( 'We take pride in offering outstanding support.', 'fl-builder' ); ?></p>

				<p><?php _e( 'The fastest way to find an answer to a question is to see if someone\'s already answered it!', 'fl-builder' ); ?></p>

				<?php /* translators: 1: docs url: 2: facebook url */ ?>
				<p><?php printf( __( 'For that, check our <a href="%1$s" target="_blank">Knowledge Base</a> or try searching <a href="%2$s" target="_blank">the Beaver Builders Facebook group</a> or our <a href="%3$s" target="_blank">Forums</a>.', 'fl-builder' ), $docs_url, $fb_url, $forums_url ); ?></p>

				<?php if ( true === FL_BUILDER_LITE ) : ?>
					<?php /* translators: %s: upgrade url */ ?>
				<p><?php printf( __( 'If you can\'t find an answer, consider upgrading to a premium version of Beaver Builder. Our expert support team is waiting to answer your questions and help you build your website. <a href="%s" target="_blank">Learn More</a>.', 'fl-builder' ), $upgrade_url ); ?></p>
				<?php else : ?>
					<?php /* translators: %s: support url */ ?>
					<p><?php printf( __( 'If you can\'t find an answer, feel free to <a href="%s" target="_blank">send us a message with your question.</a>', 'fl-builder' ), $support_url ); ?></p>
				<?php endif; ?>
			</div>

		</div>

	</div>
</div>
