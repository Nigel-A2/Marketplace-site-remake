(function($){

	/**
	 * Helper class for dealing with the post edit screen.
	 *
	 * @class FLBuilderAdminPostsListCount
	 * @since 2.2.1
	 * @static
	 */
	FLBuilderAdminPostsListCount = {

		/**
		 * Initializes the builder enabled count for the post edit screen.
		 *
		 * @since 2.2.1
		 * @method init
		 */
		init: function()
		{
			this._setupLink();
		},
		_setupLink: function() {
			var ul = $('ul.subsubsub')

			var count = window.fl_builder_enabled_count.count;
			var brand = window.fl_builder_enabled_count.brand;
			var clicked = window.fl_builder_enabled_count.clicked;
			var type = window.fl_builder_enabled_count.type;
			var bb_class = '';

			if ( clicked ) {
				bb_class += 'current'
			}
			ul.append( '|&nbsp;<li class="bb"><a class="' + bb_class + '" href="edit.php?post_type=' + type + '&bbsort">' + brand + ' <span class="count">(' + count +')</span></a></li>');
		}

	};

	$(function(){
		FLBuilderAdminPostsListCount.init()
	});
})(jQuery);
