<ul id="wpbdp-listing-metabox-tab-selector" class="wpbdp-admin-tab-nav wpbdp-sub-menu subsubsub wpbdp-small-tabs">
	<?php foreach ( $tabs as $tab ) : ?>
	<li>
		<a href="#wpbdp-listing-metabox-<?php echo esc_attr( $tab['id'] ); ?>">
			<?php echo esc_html( $tab['label'] ); ?>
		</a>
	</li>
	<?php endforeach; ?>
</ul>

<?php foreach ( $tabs as $tab ) : ?>
	<?php echo $tab['content']; ?>
<?php endforeach; ?>
