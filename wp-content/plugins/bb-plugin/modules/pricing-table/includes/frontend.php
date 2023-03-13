<?php

$pricing_box_start = '';
$pricing_box_end   = '';
if ( 'standard' === $settings->border_type ) {
	$pricing_box_start = '<div class="fl-pricing-box">';
	$pricing_box_end   = '</div>';
}

$module->render_toggle_pricing_button();
$pricing_table_class = $module->get_pricing_table_class();

?>
<div class="<?php echo $pricing_table_class; ?>">
	<?php

	$columns = count( $settings->pricing_columns );

	for ( $i = 0; $i < $columns; $i++ ) :

		if ( ! is_object( $settings->pricing_columns[ $i ] ) ) {
			continue;
		}

		$pricing_column = $settings->pricing_columns[ $i ];

		?>

		<div class="fl-pricing-table-col-<?php echo $columns; ?> fl-pricing-table-wrap">
			<div class="fl-pricing-table-column fl-pricing-table-column-<?php echo $i; ?>">
				<div class="fl-pricing-table-inner-wrap fl-pricing-ribbon-box">
					<?php
						echo $pricing_box_start;
						$module->render_ribbon( $i );
						$module->render_title( $i );
						$module->render_price( $i );
						$module->render_features( $i );
						$module->render_button( $i );
						echo $pricing_box_end;
					?>
				</div>
			</div>
		</div>
		<?php
	endfor;
	?>
</div>
