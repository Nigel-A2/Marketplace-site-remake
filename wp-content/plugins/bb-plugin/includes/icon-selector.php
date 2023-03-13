<div class="fl-icons-filter">
	<input type="text" class="fl-icons-filter-text-live" placeholder="<?php _e( 'Search Icons', 'fl-builder' ); ?>" />
</div>
<div class="fl-icons-list">
	<div class="fl-icons-section results">
	</div>
	<div class="fl-icons-section recent">
		<h2 class="recent"><?php _e( 'Recently used icons', 'fl-builder' ); ?></h2>
		<div class="recent-icons"></div>
	</div>
	<div class="fl-icons-section all-icons">

		<?php foreach ( $icon_sets as $set_key => $set_data ) : ?>
		<div class="fl-icons-section fl-<?php echo $set_key; ?>">
			<h2><?php echo $set_data['name']; ?></h2>
			<?php foreach ( $set_data['icons'] as $icon ) : ?>
				<?php if ( ! empty( $set_data['prefix'] ) ) : ?>
				<i class="<?php echo $set_data['prefix'] . ' ' . $icon; ?>" title="<?php echo $icon; ?>"></i>
				<?php else : ?>
				<i class="<?php echo $icon; ?>" title="<?php echo $icon; ?>"></i>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<div class="fl-lightbox-footer fl-icon-selector-footer">
	<a class="fl-icon-selector-cancel fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;"><?php _e( 'Cancel', 'fl-builder' ); ?></a>
</div>
