
<div<?php FLBuilder::render_column_group_attributes( $group ); ?>>
	<?php
	// $cols received as a magic variable from template loader
	foreach ( $cols as $col ) :
		?>
		<?php FLBuilder::render_column( $col ); ?>
	<?php endforeach; ?>
</div>
