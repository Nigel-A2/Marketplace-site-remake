( function( $ ) {
	FLBuilderSettingsConfig = 'undefined' === typeof FLBuilderSettingsConfig ? {} : FLBuilderSettingsConfig;
	$.extend( FLBuilderSettingsConfig, <?php echo FLBuilderUtils::json_encode( $settings ); ?> );
	if ( 'undefined' !== typeof FLBuilder ) {
		FLBuilder.triggerHook( 'settingsConfigLoaded' );
	}
} )( jQuery );
