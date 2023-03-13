(function ($) {
	FLBuilder.registerModuleHelper("countdown", {
		submit: function () {
			var form = $(".fl-builder-settings"),
				date = form.find('select[name="ui_date"]').val();

			if (Date.parse(date) <= Date.now()) {
				FLBuilder.alert(FLBuilderStrings.countdownDateisInThePast);
				return false;
			}
			return true;
		},
	});
})(jQuery);
