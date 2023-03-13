.fl-node-<?php echo $id; ?> .fl-post-feed-post {
	margin-bottom: <?php echo empty( $settings->feed_post_spacing ) ? 40 : intval( $settings->feed_post_spacing ); ?>px;
}
.fl-node-<?php echo $id; ?> .fl-post-feed-post:last-child {
	margin-bottom: 0 !important;
}
