<?php

require_once FL_BUILDER_DIR . 'includes/vendor/recaptcha/autoload.php';

if ( function_exists( 'curl_exec' ) ) {
	$recaptcha_api = new \ReCaptcha\ReCaptcha( $settings->recaptcha_secret_key, new \ReCaptcha\RequestMethod\CurlPost() );
} else {
	$recaptcha_api = new \ReCaptcha\ReCaptcha( $settings->recaptcha_secret_key );
}

if ( isset( $settings->recaptcha_action ) && ! empty( $settings->recaptcha_action ) ) {
	// @codingStandardsIgnoreStart
	// V3
	$re_response = $recaptcha_api->setExpectedHostname( $_SERVER['SERVER_NAME'] )
					  ->setExpectedAction( $settings->recaptcha_action )
					  ->setScoreThreshold( 0.5 )
					  ->verify( $recaptcha, $_SERVER['REMOTE_ADDR'] );
	// @codingStandardsIgnoreEnd
} else {
	// V2
	$re_response = $recaptcha_api->verify( $recaptcha, $_SERVER['REMOTE_ADDR'] );
}

if ( $re_response->isSuccess() ) {
	$result['error'] = false;
} else {
	$result['error'] = __( 'reCAPTCHA Error: ', 'fl-builder' );
	$error_codes     = array();
	foreach ( $re_response->getErrorCodes() as $code ) {
		$error_codes[] = $code;
	}
	$result['error'] .= implode( ' | ', $error_codes );
}
