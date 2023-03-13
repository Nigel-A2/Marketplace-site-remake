jQuery(function($) {

    var $wpbdp_debugging = $('#wpbdp-debugging');
    var $tab_selector = $('.tab-selector', $wpbdp_debugging);

	$('#wpbody .wrap').before('<div id="wpbdp-debugging-placeholder"></div>');
	$('#wpbdp-debugging-placeholder').replaceWith($('#wpbdp-debugging'));

    $tab_selector.find('li a').click(function(e) {
        e.preventDefault();

        var dest = '#wpbdp-debugging-tab-' + $(this).attr('href').replace('#', '');

        $tab_selector.find('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $wpbdp_debugging.find('.tab').hide();
        $(dest).show();
    }).first().click();

    $wpbdp_debugging.find('table tr').click(function(e) {
        var $extradata = $(this).find('.extradata');

        if ( $extradata.length > 0 )
            $extradata.toggle();
    });

});
