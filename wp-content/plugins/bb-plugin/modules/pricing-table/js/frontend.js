var FLBuilderPricingTable;

(function($) {

	/**
	 * Class for Pricing Table Module
	 *
	 * @since 2.5
	 */
	FLBuilderPricingTable = function( settings ){

		// set params
        this.settings = settings;
		this.nodeClass = '.fl-node-' + settings.id;
        this.wrapperClass = this.nodeClass + ' .fl-pricing-table';

		// initialize
		this._initPricingTable();

	};

	FLBuilderPricingTable.prototype = {
		nodeClass               : '',
		wrapperClass            : '',

		_initPricingTable: function(){

			var self = this;

            /* Tooltips */
			$(this.nodeClass + ' .fl-builder-tooltip-icon').on('click', $.proxy(this._showHelpTooltip, this));
			$('body').on('click', self._hideHelpTooltip);

			// Switch between the two Billing Options.
			$(this.nodeClass + ' .switch-button').on('click', $.proxy(this._switchPrice, this));
		},

		/**
		 * Shows a help tooltip.
		 *
		 * @since 2.5
		 * @access private
		 * @method _showHelpTooltip
		 */
		_showHelpTooltip: function( e )
		{
			this._hideHelpTooltip();
			$(e.target).closest('.fl-builder-tooltip').find('.fl-builder-tooltip-text').fadeIn();
			e.stopPropagation();
		},

		/**
		 * Hides a help tooltip.
		 *
		 * @since 2.5
		 * @access private
		 * @method _hideHelpTooltip
		 */
		_hideHelpTooltip: function( evt )
		{
			$('.fl-module-pricing-table .fl-builder-tooltip-text').fadeOut();
		},

		/**
		 * Toggle between the two Billing Options.
		 * 
		 * @since 2.5
		 * @access private
		 * @method _switchPrice
		 */
		_switchPrice: function (event) {
			var nodeClass = this.nodeClass,
			    issecond_option = $(nodeClass + ' .switch-button').prop('checked');

			if (issecond_option) {
				$(nodeClass + ' .first_option-price').hide();
				$(nodeClass + ' .second_option-price').show();
				$(nodeClass + ' .slider').removeClass('first_option');
				$(nodeClass + ' .slider').addClass( 'second_option');
			} else {
				$(nodeClass + ' .first_option-price').show();
				$(nodeClass + ' .second_option-price').hide();
				$(nodeClass + ' .slider').removeClass('second_option');
				$(nodeClass + ' .slider').addClass( 'first_option');
			}

			event.stopPropagation();
		}
	};

})(jQuery);
