<?php

/**
 * Settings compatibility helper for column nodes.
 *
 * @since 2.2
 */
class FLBuilderSettingsCompatColumn extends FLBuilderSettingsCompatHelper {

	/**
	 * Filter settings for columns.
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
		$this->handle_responsive_widths( $settings );
		return $settings;
	}

	/**
	 * Updates old responsive width settings to the new
	 * responsive width settings with live preview.
	 *
	 * @since 2.2
	 * @param object $settings
	 * @return object
	 */
	public function handle_responsive_widths( &$settings ) {
		if ( isset( $settings->medium_size ) && 'custom' === $settings->medium_size ) {
			$settings->size_medium = $settings->custom_medium_size;
			unset( $settings->medium_size );
			unset( $settings->custom_medium_size );
		}
		if ( isset( $settings->responsive_size ) && 'custom' === $settings->responsive_size ) {
			$settings->size_responsive = $settings->custom_responsive_size;
			unset( $settings->responsive_size );
			unset( $settings->custom_responsive_size );
		}
	}
}
