<?php if ( FLBuilderModel::is_builder_active() ) : ?>

if(typeof FB != 'undefined') {
		FB.XFBML.parse();
}

jQuery('#twitter-wjs').remove();
jQuery('#fl-gplus-button').remove();

<?php endif; ?>

<?php if ( $settings->show_facebook ) : ?>

(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=525994720806655";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

<?php endif; if ( $settings->show_twitter ) : ?>

!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");

<?php endif; if ( $settings->show_gplus ) : ?>

(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.id = 'fl-gplus-button';
		if (document.getElementById('fl-gplus-button')) return;
		po.src = 'https://apis.google.com/js/platform.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();

<?php endif; ?>
