<div id="wpbdmentry">
	<div id="lco">
		<?php wpbdp_the_main_links(); ?>
		<?php wpbdp_the_search_form(); ?>
	</div>

	<div id="wpbusdirmancats">
		<div style="clear:both;"></div>
		<ul>
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            print wpbusdirman_post_list_categories();
            ?>
		</ul>
	</div>
	<br style="clear: both;" />
</div>
