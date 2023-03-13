<div class="fl-content-slider">
	<div class="fl-content-slider-wrapper">
		<?php
		for ( $i = 0; $i < count( $settings->slides ); $i++ ) :

			if ( ! is_object( $settings->slides[ $i ] ) ) {
				continue;
			} else {
				$slide = $settings->slides[ $i ];
			}
			?>
		<div class="fl-slide fl-slide-<?php echo $i; ?> fl-slide-text-<?php echo $slide->text_position; ?>">
			<?php

			// Mobile photo or video
			$module->render_mobile_media( $slide );

			// Background photo or video
			$module->render_background( $slide );

			?>
			<div class="fl-slide-foreground clearfix">
				<?php

				// Content
				$module->render_content( $slide );

				// Foreground photo or video
				$module->render_media( $slide );

				?>
			</div>
		</div>
	<?php endfor; ?>
	</div>
		<?php

		// Render the navigation.
		if ( $settings->arrows && count( $settings->slides ) > 0 ) :
			?>
			<div class="fl-content-slider-navigation" aria-label="content slider buttons">
				<a class="slider-prev" href="#" aria-label="previous" role="button"><div class="fl-content-slider-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-left.svg'; ?></div></a>
				<a class="slider-next" href="#" aria-label="next" role="button"><div class="fl-content-slider-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-right.svg'; ?></div></a>
			</div>
		<?php endif; ?>
		<div class="fl-clear"></div>
</div>
