<?php

/**
 * @class FLAudioModule
 */
class FLAudioModule extends FLBuilderModule {

	/**
	 * @property $data
	 */
	public $data = null;

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Audio', 'fl-builder' ),
			'description'     => __( 'Render a WordPress audio shortcode.', 'fl-builder' ),
			'category'        => __( 'Basic', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'format-audio.svg',
		));
	}

	/**
	 * @method get_data
	 */
	public function get_data() {
		if ( ! $this->data ) {

			// Get audio data if user selected only one audio file
			if ( is_array( $this->settings->audios ) && count( $this->settings->audios ) == 1 ) {
				$this->data = FLBuilderPhoto::get_attachment_data( $this->settings->audios[0] );

				if ( ! $this->data && isset( $this->settings->data ) ) {
					$this->data = $this->settings->data;
				}
			}
		}

		return $this->data;
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		// Cache the attachment data.
		if ( 'media_library' == $settings->audio_type ) {

			// Get audio data if user selected only one audio file
			if ( is_array( $settings->audios ) && count( $settings->audios ) == 1 ) {
				$audios = FLBuilderPhoto::get_attachment_data( $settings->audios[0] );

				if ( $audios ) {
					$settings->data = $audios;
				}
			}
		}

		return $settings;
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLAudioModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'audio_type'   => array(
						'type'    => 'select',
						'label'   => __( 'Audio Type', 'fl-builder' ),
						'default' => 'wordpress',
						'options' => array(
							'media_library' => __( 'Media Library', 'fl-builder' ),
							'link'          => __( 'Link', 'fl-builder' ),
						),
						'toggle'  => array(
							'link'          => array(
								'fields' => array( 'link' ),
							),
							'media_library' => array(
								'fields' => array( 'audios' ),
							),
						),
					),
					'audios'       => array(
						'type'   => 'multiple-audios',
						'label'  => __( 'Audio', 'fl-builder' ),
						'toggle' => array(
							'playlist'     => array(
								'fields' => array( 'style', 'tracklist', 'tracknumbers', 'images', 'artists' ),
							),
							'single_audio' => array(
								'fields' => array( 'autoplay', 'loop' ),
							),
						),
					),
					'link'         => array(
						'type'        => 'text',
						'label'       => __( 'Link', 'fl-builder' ),
						'connections' => array( 'url' ),
					),

					/**
					 * Single audio options
					 */
					'autoplay'     => array(
						'type'    => 'select',
						'label'   => __( 'Auto Play', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'loop'         => array(
						'type'    => 'select',
						'label'   => __( 'Loop', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),

					/**
					 * Playlist options - show only if user selected more than one files
					 */
					'style'        => array(
						'type'    => 'select',
						'label'   => __( 'Style', 'fl-builder' ),
						'default' => 'light',
						'options' => array(
							'light' => __( 'Light', 'fl-builder' ),
							'dark'  => __( 'Dark', 'fl-builder' ),
						),
					),
					'tracklist'    => array(
						'type'    => 'select',
						'label'   => __( 'Show Playlist', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'tracknumbers' ),
							),
						),
					),
					'tracknumbers' => array(
						'type'    => 'select',
						'label'   => __( 'Show Track Numbers', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'images'       => array(
						'type'    => 'select',
						'label'   => __( 'Show Thumbnail', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
					'artists'      => array(
						'type'    => 'select',
						'label'   => __( 'Show Artist Name', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
));
