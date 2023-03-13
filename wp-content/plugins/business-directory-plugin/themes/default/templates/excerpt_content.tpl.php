<?php
/**
 * Listings except content template
 *
 * @package BDP/Themes/Default/Templates/Excerpt Content
 */

?>

<div class="listing-title">
    <h3><?php echo $fields->t_title->value; ?></h3>
</div>

<div class="excerpt-content wpbdp-hide-title">
	<?php include WPBDP_PATH . 'templates/excerpt_content.tpl.php'; ?>
</div>
