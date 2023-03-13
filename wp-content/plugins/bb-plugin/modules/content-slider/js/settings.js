(function($){

	FLBuilder.registerModuleHelper('content-slider', {
		init: function() {
			var form   = $('.fl-builder-settings'),
				slides = form.find('#fl-field-slides');

			slides.on('click', '.fl-form-field-edit', this._editSlide);
			slides.on('click', '.fl-builder-field-add', this._reloadSlide);
		},
		submit: function() {
			var form       = $('.fl-builder-settings'),
			    transition = parseFloat( form.find('input[name=speed]').val() ) * 1000,
			    delay      = parseFloat( form.find('input[name=delay]').val() ) * 1000;

			if ( transition >= delay ) {
				FLBuilder.alert( FLBuilderStrings.contentSliderTransitionWarn )
				return false;
			}
			return true;
		},
		_editSlide: function() {
			var slide = $(this).closest('.fl-builder-field-multiple');

			FLBuilder.setSandbox('sliderIndex', slide.index());

			setTimeout(function () {
				FLBuilder.preview.preview();
			}, 500);
		},
		_reloadSlide: function() {
			setTimeout(function() {
				FLBuilder.preview.preview();
			}, 500);
		}
	})

	FLBuilder.registerModuleHelper('content_slider_slide', {
		init: function()
		{
			var form        = $('.fl-form-field-settings'),
				bgLayout      = form.find('select[name=bg_layout]'),
				contentLayout = form.find('select[name=content_layout]'),
				icon          = form.find( 'input[name=btn_icon]' );

			bgLayout.on('change', this._toggleMobileTab);
			bgLayout.on('change', this._toggleTextAndCtaTabs);
			contentLayout.on('change', this._toggleMobileTab);
			contentLayout.on('change', this._toggleTextAndCtaTabs);
			contentLayout.trigger('change');
			this._flipSettings();
			icon.on( 'change', this._flipSettings );
			form.on('click', '.fl-builder-settings-save', this._endEditSlideOnSave);
			form.on('click', '.fl-builder-settings-cancel', this._endEditSlideOnCancel);
		},
		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=btn_icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-btn_duo_color1').show();
				$('#fl-field-btn_duo_color2').show();
			} else {
				$('#fl-field-btn_duo_color1').hide();
				$('#fl-field-btn_duo_color2').hide();
			}
		},
		submit: function()
		{
			var form          = $('.fl-builder-settings'),
				bgLayout      = form.find('select[name=bg_layout]').val(),
				contentLayout = form.find('select[name=content_layout]').val();

			if(bgLayout == 'none' && contentLayout == 'none') {
				FLBuilder.alert(FLBuilderStrings.contentSliderSelectLayout);
				return false;
			}

			return true;
		},
		_toggleTextAndCtaTabs: function()
		{
			var form          = $('.fl-builder-settings'),
				bgLayout      = form.find('select[name=bg_layout]').val(),
				contentLayout = form.find('select[name=content_layout]').val(),
				show          = true;

			if(bgLayout == 'video' || contentLayout == 'none') {
				show = false;
			}

			if(show) {
				$('[data-form-id=content_slider_slide] a[href*=fl-builder-settings-tab-style]').show();
				$('a[href*=fl-builder-settings-tab-cta]').show();
			}
			else {
				$('[data-form-id=content_slider_slide] a[href*=fl-builder-settings-tab-style]').hide();
				$('a[href*=fl-builder-settings-tab-cta]').hide();
			}
		},
		_toggleMobileTab: function()
		{
			var form          = $('.fl-builder-settings'),
				bgLayout      = form.find('select[name=bg_layout]').val(),
				contentLayout = form.find('select[name=content_layout]').val(),
				show          = true,
				showPhoto     = true,
				showText      = true;

			// Hide or show tab.
			if(bgLayout == 'video' || (bgLayout != 'photo' && contentLayout == 'none')) {
				show = false;
			}

			if(show) {
				$('a[href*=fl-builder-settings-tab-mobile]').show();
			}
			else {
				$('a[href*=fl-builder-settings-tab-mobile]').hide();
			}

			// Hide or show text.
			if(contentLayout == 'none') {
				showText = false;
			}

			if(showText) {
				$('#fl-builder-settings-section-r_text_style').show();
			}
			else {
				$('#fl-builder-settings-section-r_text_style').hide();
			}

			// Hide or show photos.
			if(bgLayout != 'photo' && contentLayout != 'photo') {
				showPhoto = false;
			}

			if(showPhoto) {
				$('#fl-builder-settings-section-r_photo').show();
			}
			else {
				$('#fl-builder-settings-section-r_photo').hide();
			}
		},
		_endEditSlideOnSave: function() {
			FLBuilder.deleteSandbox('sliderIndex');
		},
		_endEditSlideOnCancel: function() {
			FLBuilder.deleteSandbox('sliderIndex');
			FLBuilder.preview.preview();
		},
	});

})(jQuery);
