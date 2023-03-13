(function($){

	/**
	 * Helper class for dealing with the post edit screen.
	 *
	 * @class FLBuilderAdminPosts
	 * @since 1.0
	 * @static
	 */
	FLBuilderAdminPosts = {

		/**
		 * Initializes the builder for the post edit screen.
		 *
		 * @since 1.0
		 * @method init
		 */
		init: function()
		{
			$('.fl-enable-editor').on('click', this._enableEditorClicked);
			$('.fl-enable-builder').on('click', this._enableBuilderClicked);
			$('.fl-launch-builder').on('click', this._launchBuilderClicked);

			/* WPML Support */
			$('#icl_cfo').on('click', this._wpmlCopyClicked);

			this._hideFLBuilderAdminButtons();
			this._hideBlockEditorInserter();
		},

		/**
		 * Fires when the text editor button is clicked
		 * and switches the current post to use that
		 * instead of the builder.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableEditorClicked
		 */
		_enableEditorClicked: function()
		{
			if ( ! $( 'body' ).hasClass( 'fl-builder-enabled' ) ) {
				return;
			}
			if ( confirm( FLBuilderAdminPostsStrings.switchToEditor ) ) {

				$('.fl-builder-admin-tabs a').removeClass('fl-active');
				$(this).addClass('fl-active');

				FLBuilderAdminPosts.ajax({
					action: 'fl_builder_disable',
				}, FLBuilderAdminPosts._enableEditorComplete);
			}
		},

		/**
		 * Callback for enabling the editor.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableEditorComplete
		 */
		_enableEditorComplete: function()
		{
			$('body').removeClass('fl-builder-enabled');
			$(window).resize();
		},

		/**
		 * Callback for enabling the editor.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableBuilderClicked
		 */
		_enableBuilderClicked: function()
		{
			if($('body').hasClass('fl-builder-enabled')) {
				return;
			}
			else {
				$('.fl-builder-admin-tabs a').removeClass('fl-active');
				$(this).addClass('fl-active');
				FLBuilderAdminPosts._launchBuilder();
			}
		},

		/**
		 * Fires when the page builder button is clicked
		 * and switches the current post to use that
		 * instead of the text editor.
		 *
		 * @since 1.0
		 * @access private
		 * @method _launchBuilderClicked
		 * @param {Object} e An event object.
		 */
		_launchBuilderClicked: function(e)
		{
			e.preventDefault();

			FLBuilderAdminPosts._launchBuilder();
		},

		/**
		 * Callback for enabling the builder.
		 *
		 * @since 1.0
		 * @access private
		 * @method _launchBuilder
		 */
		_launchBuilder: function()
		{
			var postId = $('#post_ID').val(),
			    title  = $('#title');

			if(typeof title !== 'undefined' && title.val() === '') {
			    title.val('Post #' + postId);
			}

			$(window).off('beforeunload');
			$('body').addClass('fl-builder-enabled');
			$('.fl-builder-loading').show();
			$('form#post').append('<input type="hidden" name="fl-builder-redirect" value="' + postId + '" />');
			$('form#post').submit();
		},

		/**
		 * Fires when the WPML copy button is clicked.
		 *
		 * @since 1.1.7
		 * @access private
		 * @method _wpmlCopyClicked
		 * @param {Object} e An event object.
		 */
		_wpmlCopyClicked: function(e)
		{
			var originalPostId = $('#icl_translation_of').val();

			if(typeof originalPostId !== 'undefined') {

				$('.fl-builder-loading').show();

				FLBuilderAdminPosts.ajax({
					action: 'fl_builder_duplicate_wpml_layout',
					original_post_id: originalPostId
				}, FLBuilderAdminPosts._wpmlCopyComplete);
			}
		},

		/**
		 * Callback for when the WPML copy button is clicked.
		 *
		 * @since 1.1.7
		 * @access private
		 * @method _wpmlCopyComplete
		 * @param {String} response The JSON encoded response.
		 */
		_wpmlCopyComplete: function(response)
		{
			response = JSON.parse(response);

			$('.fl-builder-loading').hide();

			if(response.has_layout && response.enabled) {
				$('body').addClass('fl-builder-enabled');
			}
		},

		/**
		 * Hide the Page Builder Admin Buttons if Content Editor is hidden in the ACF Field Settings.
		 *
		 * @since 2.1.7
		 * @access private
		 * @method _hideFLBuilderAdminButtons
		 */
		_hideFLBuilderAdminButtons: function()
		{
			if ( $( '.acf-postbox' ).is( ':visible' ) && $( '#postdivrich' ).is( ':hidden' ) && ! $( '.fl-enable-builder' ).hasClass('fl-active') ){
				$( '.fl-builder-admin' ).hide();
			}
		},

		/**
		 * Hide the Gutenberg Block Editor Inserter button.
		 *
		 * @since 2.4
		 * @access private
		 * @method _hideBlockEditorInserter
		 */
		_hideBlockEditorInserter: function()
		{
			setTimeout( function(){
				if ( $( 'body' ).hasClass( 'fl-builder-enabled' ) ) {
					$( '.block-editor-inserter' ).hide();
					$( '.wp-block-paragraph' ).parent().remove();
                    $( '.wp-block[data-type="core/paragraph"]' ).hide();
				}
			}, 100 );
		},

		/**
		 * Makes an AJAX request.
		 *
		 * @since 1.0
		 * @method ajax
		 * @param {Object} data An object with data to send in the request.
		 * @param {Function} callback A function to call when the request is complete.
		 */
		ajax: function(data, callback)
		{
			// Add the post ID to the data.
			data.post_id = $('#post_ID').val();

			// Show the loader.
			$('.fl-builder-loading').show();

			// Send the request.
			$.post(ajaxurl, data, function(response) {

				FLBuilderAdminPosts._ajaxComplete();

				if(typeof callback !== 'undefined') {
					callback.call(this, response);
				}
			});
		},

		/**
		 * Generic callback for when an AJAX request is complete.
		 *
		 * @since 1.0
		 * @access private
		 * @method _ajaxComplete
		 */
		_ajaxComplete: function()
		{
			$('.fl-builder-loading').hide();
		}
	};

	/* Initializes the post edit screen. */
	$(function(){
		FLBuilderAdminPosts.init();
	});

})(jQuery);
