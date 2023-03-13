<?php
class FLBuilderSettingsPresets {

	static private $presets = array();

	static public function init() {

		add_filter( 'fl_builder_shared_option_sets', 'FLBuilderSettingsPresets::filter_shared_option_sets' );
		add_filter( 'fl_builder_register_settings_form', 'FLBuilderSettingsPresets::filter_settings_form' );

		/**
		 * Register presets action.
		 * @see fl_register_presets
		 */
		do_action( 'fl_register_presets' );
	}

	/**
	* Register a new preset for a given type string
	*
	* @param String $type - A string identifier of what kind of preset this will be.
	* @param Array $args - The meta and settings for the preset.
	* @return void
	*/
	static public function register( $type = '', $args = array() ) {
		$defaults                        = array(
			'name'     => '',
			'label'    => __( 'Untitled Preset', 'fl-builder' ),
			'type'     => $type,
			'settings' => array(), /* the settings to set when preset is selected */
			'data'     => array(), /* arbitrary data to pass along to the frontend */
		);
		$args                            = wp_parse_args( $args, $defaults );
		$name                            = $args['name'];
		self::$presets[ $type ][ $name ] = $args;
	}

	/**
	* Getter method for self::$presets;
	*
	* @return Array
	*/
	static public function get_presets() {
		return self::$presets;
	}

	/**
	* Create option sets for a specified preview $type
	*
	* @param String $type
	* @return Array
	*/
	static public function get_preset_options( $type ) {
		$options = array(
			'' => __( 'Select A Preset', 'fl-builder' ),
		);
		if ( $type ) {
			$presets = self::$presets[ $type ];
			foreach ( $presets as $preset ) {
				$handle             = $preset['name'];
				$label              = $preset['label'];
				$options[ $handle ] = $label;
			}
		}
		return $options;
	}

	/**
	* Create option sets for each preset type and add to FLBuilderConfig.optionSets
	*
	* @param Array $option_sets - previously set option sets
	* @return Array
	*/
	static public function filter_shared_option_sets( $option_sets ) {
		foreach ( self::$presets as $type => $set ) {
			$option_sets[ $type . '-presets' ] = self::get_preset_options( $type );
		}

		return $option_sets;
	}

	/**
	* Filter settings forms and set the refresh type for preset fields to 'none'
	*
	* @param Array $form
	* @return Array
	*/
	static public function filter_settings_form( $form ) {

		if ( isset( $form['tabs'] ) ) {
			foreach ( $form['tabs'] as $i => $tab ) {
				if ( ! isset( $tab['sections'] ) ) {
					continue;
				}

				foreach ( $tab['sections'] as $j => $section ) {
					if ( ! isset( $section['fields'] ) ) {
						continue;
					}

					foreach ( $section['fields'] as $k => $field ) {
						if ( 'preset' === $field['type'] ) {
							$form['tabs'][ $i ]['sections'][ $j ]['fields'][ $k ]['preview'] = array(
								'type' => 'none',
							);
						}
					}
				}
			}
		}
		return $form;
	}
}
FLBuilderSettingsPresets::init();
