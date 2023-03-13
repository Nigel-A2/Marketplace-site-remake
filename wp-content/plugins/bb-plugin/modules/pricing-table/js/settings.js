(function($){

	FLBuilder.registerModuleHelper('pricing-table', {

		init: function () {
			var form = $('.fl-builder-settings'),
				billingOptions = form.find('select[name=dual_billing]')
				billingOption1 = form.find('input[name=billing_option_1]'),
				billingOption2 = form.find('input[name=billing_option_2]');

			billingOptions.on('change', this._updateLabels);
			billingOption1.on('keyup', this._updateLabels);
			billingOption2.on('keyup', this._updateLabels);

			this._updateLabels();
			this._limitAdvancedSpacing( form );
		},

		_updateLabels: function ( event ) {
			var form = $('.fl-builder-settings'),
				billingOptions = form.find('select[name=dual_billing]').val(),
				firstOptionText = form.find('input[name=billing_option_1]').val().trim(),
				secondOptionText = form.find('input[name=billing_option_2]').val().trim(),
				firstOptionPriceButtonColor = form.find('#fl-field-billing_option_1_btn_color label'),
				secondOptionPriceButtonColor = form.find('#fl-field-billing_option_2_btn_color label');

			if ( 'yes' === billingOptions ) {
				$(firstOptionPriceButtonColor).text( '' === firstOptionText ? 'Monthly' : firstOptionText );
				$(secondOptionPriceButtonColor).text( '' === secondOptionText ? 'Yearly' : secondOptionText );
			}

		},

		_limitAdvancedSpacing: function ( form ) {
			var spacingTop              = form.find('input[name=advanced_spacing_top]'),
				spacingTopMedium        = form.find('input[name=advanced_spacing_top_medium]'),
				spacingTopResponsive    = form.find('input[name=advanced_spacing_top_responsive]'),
				spacingBottom           = form.find('input[name=advanced_spacing_bottom]'),
				spacingBottomMedium     = form.find('input[name=advanced_spacing_bottom_medium]'),
				spacingBottomResponsive = form.find('input[name=advanced_spacing_bottom_responsive]');

			spacingTop.closest('.fl-dimension-field-unit ').hide();
			spacingTopMedium.closest('.fl-dimension-field-unit ').hide();
			spacingTopResponsive.closest('.fl-dimension-field-unit ').hide();
			spacingBottom.closest('.fl-dimension-field-unit ').hide();
			spacingBottomMedium.closest('.fl-dimension-field-unit ').hide();
			spacingBottomResponsive.closest('.fl-dimension-field-unit ').hide();
		},
	});

	FLBuilder.registerModuleHelper('pricing_column_form', {

		init: function () {
			var form 				= $('.fl-builder-settings[data-type=pricing_column_form]'),
				icon 				= form.find('input[name=btn_icon]'),
				moduleSettingsForm 	= $('form.fl-builder-pricing-table-settings'),
				featuresSection		= $( '#fl-builder-settings-section-features' ),
				featureToggleButton	= '.fl-builder-price-feature-toggle-button';
			
			featuresSection.on('click', featureToggleButton, this._togglePricesFeaturesClicked);
			icon.on('change', this._flipSettings);
			this._flipSettings( form );
			this._toggleFields( form, moduleSettingsForm );
		},

		_toggleFields: function (form, moduleSettingsForm ) {
			var billingLabel1 = form.find('#fl-field-price label'),
				billingLabel2 = form.find('#fl-field-price_option_2 label'),
				billingOptions = moduleSettingsForm.find('select[name=dual_billing]').val(),
				firstOptionText = moduleSettingsForm.find('input[name=billing_option_1]').val().trim(),
				secondOptionText = moduleSettingsForm.find('input[name=billing_option_2]').val().trim(),
				borderType = moduleSettingsForm.find('select[name=border_type]').val();

			if ( 'no' === billingOptions ) {
				$('#fl-field-price').show();
				$('#fl-field-duration').show();
				$('#fl-field-price_option_1').hide();
				$('#fl-field-price_option_2').hide();
			} else if ('yes' === billingOptions ) {
				$('#fl-field-duration').hide();
				$('#fl-field-price_option_1').show();
				$('#fl-field-price_option_2').show();

				firstOptionText = '' === firstOptionText ? 'Monthly' : firstOptionText;
				secondOptionText = '' === secondOptionText ? 'Yearly' : secondOptionText;

				$(billingLabel1).text( firstOptionText );
				$(billingLabel2).text( secondOptionText );
			}

			// If using Standard Border, hide the Box Border field ( ID = 'fl-field-background' ).
			if ( 'standard' === borderType ) {
				$('#fl-field-background').hide();
			} else {
				$('#fl-field-background').show();
			}
		},

		_flipSettings: function( form ) {
			var icon = form.find( 'input[name=btn_icon]' );
			
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-btn_duo_color1').show();
				$('#fl-field-btn_duo_color2').show();
			} else {
				$('#fl-field-btn_duo_color1').hide();
				$('#fl-field-btn_duo_color2').hide();
			}
		},

		_togglePricesFeaturesClicked: function () {
			var form = $('.fl-builder-settings[data-type=pricing_column_form]'),
			priceFeatureButtons = form.find('.fl-builder-price-feature-toggle-button');
				button = $(this);

			form.find('.fl-price-feature-icon-row').hide();
			form.find('.fl-price-feature-tooltip-row').hide();

			if (button.hasClass('down')) {
				button.closest('.fl-price-feature-field').find('.fl-price-feature-icon-row').show();
				button.closest('.fl-price-feature-field').find('.fl-price-feature-tooltip-row').show();

				form.find('.fl-builder-price-feature-toggle-button').removeClass('up');
				form.find('.fl-builder-price-feature-toggle-button').addClass('down');

				button.removeClass('down');
				button.addClass('up');

			} else {

				button.removeClass('up');
				button.addClass('down');
			}

		},

	});

})(jQuery);
