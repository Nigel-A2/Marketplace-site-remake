(function ($) {

	FLBuilder.registerModuleHelper('callout', {

		init: function () {
			var form = $('.fl-builder-settings'),
				iconSize = form.find('#fl-field-icon_size input[type=number]'),
				buttonBgColor = form.find('input[name=btn_bg_color]'),
				icon = form.find('input[name=icon]'),
				icon2 = form.find('input[name=btn_icon]'),
				photoCrop = form.find('select[name=photo_crop]');

			this._flipSettings();
			icon.on('change', this._flipSettings);
			icon2.on('change', this._flipSettings);
			photoCrop.on('change', this._photoCropChanged);

			// Preview events.
			iconSize.on('input', this._previewIconSize);
			buttonBgColor.on('change', this._previewButtonBackground);
		},

		_flipSettings: function () {
			var form = $('.fl-builder-settings'),
				icon = form.find('input[name=icon]'),
				icon2 = form.find('input[name=btn_icon]');
			if (-1 !== icon.val().indexOf('fad fa')) {
				$('#fl-field-icon_duo_color1').show();
				$('#fl-field-icon_duo_color2').show();
				$('#fl-field-icon_color').hide();
				$('#fl-field-icon_hover_color').hide();
			} else {
				$('#fl-field-icon_duo_color1').hide();
				$('#fl-field-icon_duo_color2').hide();
				$('#fl-field-icon_color').show();
				$('#fl-field-icon_hover_color').show();
			}
			if (-1 !== icon2.val().indexOf('fad fa')) {
				$('#fl-field-btn_duo_color1').show();
				$('#fl-field-btn_duo_color2').show();
			} else {
				$('#fl-field-btn_duo_color1').hide();
				$('#fl-field-btn_duo_color2').hide();
			}
		},

		_previewIconSize: function () {
			var preview = FLBuilder.preview,
				iconSelector = preview._getPreviewSelector(preview.classes.node, '.fl-icon i'),
				beforeSelector = preview._getPreviewSelector(preview.classes.node, '.fl-icon i::before'),
				form = $('.fl-builder-settings'),
				field = form.find('#fl-field-icon_size .fl-field-responsive-setting:visible'),
				size = field.find('input[type=number]').val(),
				unit = field.find('select').val(),
				bgColor = form.find('input[name=icon_bg_color]').val(),
				value = '' === size ? '' : size + unit + ' !important',
				height = '' === size ? '' : (size * 1.75) + unit + ' !important';

			preview.updateCSSRule(iconSelector, 'font-size', value, true);
			preview.updateCSSRule(beforeSelector, 'font-size', value, true);

			if ('' === bgColor) {
				preview.updateCSSRule(iconSelector, {
					'line-height': '1',
					'height': 'auto !important',
					'width': 'auto !important',
				}, undefined, true);
			} else {
				preview.updateCSSRule(iconSelector, {
					'line-height': height,
					'height': height,
					'width': height,
				}, undefined, true);
			}
		},

		_previewButtonBackground: function (e) {
			var preview = FLBuilder.preview,
				selector = preview.classes.node + ' a.fl-button, ' + preview.classes.node + ' a.fl-button:visited',
				form = $('.fl-builder-settings:visible'),
				style = form.find('select[name=btn_style]').val(),
				bgColor = form.find('input[name=btn_bg_color]').val();

			if ('flat' === style) {
				if ('' !== bgColor && bgColor.indexOf('rgb') < 0) {
					bgColor = '#' + bgColor;
				}
				preview.updateCSSRule(selector, 'background-color', bgColor);
				preview.updateCSSRule(selector, 'border-color', bgColor);
			} else {
				preview.delayPreview(e);
			}
		},

		_photoCropChanged: function () {
			var form = $('.fl-builder-settings'),
				crop = form.find('select[name=photo_crop]'),
				radius = form.find('.fl-border-field-radius');

			if ('circle' === crop.val()) {
				radius.hide();
			} else {
				radius.show();
			}
		},
	});

})(jQuery);
