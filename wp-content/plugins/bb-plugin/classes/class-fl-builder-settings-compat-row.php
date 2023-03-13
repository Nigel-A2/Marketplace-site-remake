<?php

/**
 * Settings compatibility helper for row nodes.
 *
 * @since 2.2
 */
class FLBuilderSettingsCompatRow extends FLBuilderSettingsCompatHelper {

	/**
	 * Filter settings for rows.
	 *
	 * @since 2.2
	 * @param object $settings
	 * @return object
	 */
	public function filter_settings( $settings ) {
		$this->handle_opacity_inputs( $settings, 'bg_opacity', 'bg_color' );
		$this->handle_opacity_inputs( $settings, 'bg_overlay_opacity', 'bg_overlay_color' );
		$this->handle_opacity_inputs( $settings, 'border_opacity', 'border_color' );
		$this->handle_border_inputs( $settings );
		return $settings;
	}
}
