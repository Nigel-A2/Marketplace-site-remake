(function($) {

	FLBuilderPostGrid = function(settings)
	{
		this.settings    = settings;
		this.nodeClass   = '.fl-node-' + settings.id;
		this.matchHeight = settings.matchHeight;

		if ( 'columns' == this.settings.layout ) {
			this.wrapperClass = this.nodeClass + ' .fl-post-grid';
			this.postClass    = this.nodeClass + ' .fl-post-column';
		}
		else {
			this.wrapperClass = this.nodeClass + ' .fl-post-' + this.settings.layout;
			this.postClass    = this.wrapperClass + '-post';
		}

		if(this._hasPosts()) {
			this._initLayout();
			this._initInfiniteScroll();
		}
	};

	FLBuilderPostGrid.prototype = {

		settings        : {},
		nodeClass       : '',
		wrapperClass    : '',
		postClass       : '',
		gallery         : null,
		currPage		: 1,
		totalPages		: 1,

		_hasPosts: function()
		{
			return $(this.postClass).length > 0;
		},

		_initLayout: function()
		{
			switch(this.settings.layout) {

				case 'columns':
				this._columnsLayout();
				break;

				case 'grid':
				this._gridLayout();
				break;

				case 'gallery':
				this._galleryLayout();
				break;
			}

			$(this.postClass).css('visibility', 'visible');

			FLBuilderLayout._scrollToElement( $( this.nodeClass + ' .fl-paged-scroll-to' ) );
		},

		_columnsLayout: function()
		{
			$(this.wrapperClass).imagesLoaded( $.proxy( function() {
				this._gridLayoutMatchHeight();
			}, this ) );

			$( window ).on( 'resize', $.proxy( function(){
				$(this.wrapperClass).imagesLoaded( $.proxy( function() {
					this._gridLayoutMatchHeight();
				}, this ) );
			}, this ) );
		},

		_gridLayout: function()
		{
			var wrap = $(this.wrapperClass);

			wrap.masonry({
				columnWidth         : this.nodeClass + ' .fl-post-grid-sizer',
				gutter              : parseInt(this.settings.postSpacing),
				isFitWidth          : true,
				itemSelector        : this.postClass,
				transitionDuration  : 0,
				isRTL               : this.settings.isRTL
			});

			wrap.imagesLoaded( $.proxy( function() {
				this._gridLayoutMatchHeight();
				wrap.masonry();
			}, this ) );

			$(window).scroll($.debounce( 25, function(){
				wrap.masonry()
			}));

		},

		_gridLayoutMatchHeight: function()
		{
			var highestBox = 0;

			if ( ! this._isMatchHeight() ) {
				$(this.nodeClass + ' .fl-post-grid-post').css('height', '');
				return;
			}

            $(this.nodeClass + ' .fl-post-grid-post').css('height', '').each(function(){

                if($(this).height() > highestBox) {
                	highestBox = $(this).height();
                }
            });

            $(this.nodeClass + ' .fl-post-grid-post').height(highestBox);
		},

		_isMatchHeight: function(){
			var width 		= $( window ).width(),
				breakpoints = FLBuilderLayoutConfig.breakpoints,
				matchMedium = '' != this.matchHeight.medium ? this.matchHeight.medium : this.matchHeight.default;
				matchSmall  = '' != this.matchHeight.responsive ? this.matchHeight.responsive : this.matchHeight.default;

			return (width > breakpoints.medium && 1 == this.matchHeight.default)
				   || (width > breakpoints.small && width <= breakpoints.medium && 1 == matchMedium)
				   || (width <= breakpoints.small && 1 == matchSmall);
		},

		_galleryLayout: function()
		{
			this.gallery = new FLBuilderGalleryGrid({
				'wrapSelector' : this.wrapperClass,
				'itemSelector' : '.fl-post-gallery-post',
				'isRTL'        : this.settings.isRTL
			});
		},

		_initInfiniteScroll: function()
		{
			var isScroll = 'scroll' == this.settings.pagination || 'load_more' == this.settings.pagination,
				pages	 = $( this.nodeClass + ' .fl-builder-pagination' ).find( 'li .page-numbers:not(.next)' );

			if( pages.length > 1) {
				total = pages.last().text().replace( /\D/g, '' )
				this.totalPages = parseInt( total );
			}

			if( isScroll && this.totalPages > 1 && 'undefined' === typeof FLBuilder ) {
				this._infiniteScroll();

				if( 'load_more' == this.settings.pagination ) {
					this._infiniteScrollLoadMore();
				}
			}
		},

		_infiniteScroll: function(settings)
		{
			var path 		= $(this.nodeClass + ' .fl-builder-pagination a.next').attr('href'),
				pagePattern = /(.*?(\/|\&|\?)paged-[0-9]{1,}(\/|=))([0-9]{1,})+(.*)/,
				wpPattern   = /^(.*?\/?page\/?)(?:\d+)(.*?$)/,
				pageMatched = null,
				scrollData	= {
					navSelector     : this.nodeClass + ' .fl-builder-pagination',
					nextSelector    : this.nodeClass + ' .fl-builder-pagination a.next',
					itemSelector    : this.postClass,
					prefill         : true,
					bufferPx        : 200,
					loading         : {
						msgText         : 'Loading',
						finishedMsg     : '',
						img             : FLBuilderLayoutConfig.paths.pluginUrl + 'img/ajax-loader-grey.gif',
						speed           : 1
					}
				};

			// Define path since Infinitescroll incremented our custom pagination '/paged-2/2/' to '/paged-3/2/'.
			if ( pagePattern.test( path ) ) {
				scrollData.path = function( currPage ){
					pageMatched = path.match( pagePattern );
					path = pageMatched[1] + currPage + pageMatched[5];
					return path;
				}
			}
			else if ( wpPattern.test( path ) ) {
				scrollData.path = path.match( wpPattern ).slice( 1 );
			}

			$(this.wrapperClass).infinitescroll( scrollData, $.proxy(this._infiniteScrollComplete, this) );

			setTimeout(function(){
				$(window).trigger('resize');
			}, 100);
		},

		_infiniteScrollComplete: function(elements)
		{
			var wrap = $(this.wrapperClass);

			elements = $(elements);

			if(this.settings.layout == 'columns') {
				wrap.imagesLoaded( $.proxy( function() {
					this._gridLayoutMatchHeight();
					elements.css('visibility', 'visible');
				}, this ) );
			}
			else if(this.settings.layout == 'grid') {
				wrap.imagesLoaded( $.proxy( function() {
					this._gridLayoutMatchHeight();
					wrap.masonry('appended', elements);
					wrap.masonry();
					elements.css('visibility', 'visible');
				}, this ) );
			}
			else if(this.settings.layout == 'gallery') {
				this.gallery.resize();
				elements.css('visibility', 'visible');
			}

			if( 'load_more' == this.settings.pagination ) {
				$( this.wrapperClass + ' .fl-post-grid-sizer.masonry-brick' ).appendTo( this.wrapperClass );
				$( '#infscr-loading' ).appendTo( this.wrapperClass );
			}

			elements.find( 'img[srcset]' ).each( function( index, img ) {
				img.outerHTML = img.outerHTML;
			});
			
			this.currPage++;

			this._removeLoadMoreButton();
		},

		_infiniteScrollLoadMore: function()
		{
			var wrap = $( this.wrapperClass );

			$( window ).unbind( '.infscr' );

			$(this.nodeClass + ' .fl-builder-pagination-load-more .fl-button').on( 'click', function(){
				wrap.infinitescroll( 'retrieve' );
				return false;
			});
		},

		_removeLoadMoreButton: function()
		{
			if ( 'load_more' == this.settings.pagination && this.totalPages == this.currPage ) {
				$( this.nodeClass + ' .fl-builder-pagination-load-more' ).remove();
			}
		}
	};

})(jQuery);
