<?php

/**
 * Generic settings compatibility helper for all node types.
 *
 * @since 2.2
 */
class FLBuilderSettingsCompatGeneric extends FLBuilderSettingsCompatHelper {

	/**
	 * Filter settings for all node types.
	 *
	 * @since 2.2
	 * @param object $settings
	 * @return object
	 */
	public function filter_settings( $settings ) {
		return $settings;
	}
}
