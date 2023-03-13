(function($){

	FLBuilder.addHook( 'didRenderLayoutJSComplete', function() {
		FLBuilder._moduleHelpers.accordion._previewContent();
	} );

	FLBuilder.registerModuleHelper('accordion', {

		init: function()
		{
			var form      = $('.fl-builder-settings'),
				labelSize   = form.find('select[name=label_size]'),
				itemSpacing = form.find('input[name=item_spacing]'),
				icon1        = form.find( 'input[name=label_active_icon]' ),
				icon2        = form.find( 'input[name=label_icon]' );

			this._flipSettings();

			icon1.on( 'change', this._flipSettings );
			icon2.on( 'change', this._flipSettings );
			labelSize.on('change', this._previewLabelSize);
			itemSpacing.on('input', this._previewItemSpacing);

			this._previewContent();
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon1 = form.find( 'input[name=label_icon]' ),
					icon2 = form.find( 'input[name=label_active_icon]' );

			if ( -1 !== icon1.val().indexOf( 'fad fa') || -1 !== icon2.val().indexOf( 'fad fa') ) {
				$('#fl-field-duo_color1').show();
				$('#fl-field-duo_color2').show();
			} else {
				$('#fl-field-duo_color1').hide();
				$('#fl-field-duo_color2').hide();
			}
		},

		_previewContent: function()
		{
			var form = $( '.fl-builder-accordion-settings:visible' );
			var preview = FLBuilder.preview;

			if ( ! form.length || ! preview || ! preview.elements.node ) {
				return;
			}

			var settings = FLBuilder._getSettings( form );
			var content = preview.elements.node.find( '.fl-accordion-content' ).eq( 0 )

			if ( 1 != settings.open_first && ! content.is( ':visible' ) ) {
				preview.elements.node.find( '.fl-accordion-button' ).eq( 0 ).trigger( 'click' );
			}
		},

		_previewLabelSize: function()
		{
			var size  = $('.fl-builder-settings select[name=label_size]').val(),
				wrap  = FLBuilder.preview.elements.node.find('.fl-accordion');

			wrap.removeClass('fl-accordion-small');
			wrap.removeClass('fl-accordion-medium');
			wrap.removeClass('fl-accordion-large');
			wrap.addClass('fl-accordion-' + size);
		},

		_previewItemSpacing: function()
		{
			var spacing = parseInt($('.fl-builder-settings input[name=item_spacing]').val(), 10),
				items   = FLBuilder.preview.elements.node.find('.fl-accordion-item');

			items.attr('style', '');

			if(isNaN(spacing) || spacing === 0) {
				items.not(':last-child').css({
					'border-bottom': 'none',
					'border-bottom-left-radius': '0',
					'border-bottom-right-radius': '0',
				});
				items.not(':first-child').css({
					'border-top-left-radius': '0',
					'border-top-right-radius': '0',
				});
			}
		}
	});

})(jQuery);
