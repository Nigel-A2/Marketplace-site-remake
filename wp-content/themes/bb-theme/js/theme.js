(function($){

	/**
	 * Helper class for frontend theme logic.
	 *
	 * @since 1.0
	 * @class FLTheme
	 */
	FLTheme = {

		/**
		 * Initializes all frontend theme logic.
		 *
		 * @since 1.0
		 * @method init
		 */
		init: function()
		{
			this._bind();
		},

		/**
		 * Initializes and binds all frontend events.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			var self = this;

			// Nav Toggles
			$('.navbar-toggle').on('click', this.navbarToggleClick);

			// Top Nav Drop Downs
			if($('.fl-page-bar-nav ul.sub-menu').length != 0) {
				this._setupDropDowns();
				this._enableTopNavDropDowns();
			}

			// Page Nav Drop downs
			if($('.fl-page-nav ul.sub-menu').length != 0) {
				$(window).on('resize.fl-page-nav-sub-menu', $.throttle(500, this._enablePageNavDropDowns));
				this._setupDropDowns();
				this._enablePageNavDropDowns();
			}

			// Current menu item initializes or click
			if($('.fl-page-nav ul.menu').length != 0) {
				$('.fl-page-nav ul.menu').find('.menu-item').on('click', '> a[href*="#"]:not([href="#"])', this._setupCurrentNavItem);
				this._setupCurrentNavItem();
			}

			// Nav Search
			if($('.fl-page-nav-search').length != 0) {
				$('.fl-page-nav-search a.fa-search').on('click', this._toggleNavSearch);
			}

			// Nav vertical
			if($('.fl-nav-vertical').length != 0) {
				$(window).on('resize', $.throttle(500, this._navVertical));
				this._navVertical();
			}

			// Nav vertical right & boxed layout
			if($('.fl-fixed-width.fl-nav-vertical-right').length != 0) {
				$(window).on('resize', $.throttle(500, this._updateVerticalRightPos));
				this._updateVerticalRightPos();
			}

			// Centered inline logo
			if($('.fl-page-nav-centered-inline-logo').length != 0) {
				$(window).on('resize', $.throttle(500, this._centeredInlineLogo));
				this._centeredInlineLogo();
			}

			// Nav Left
			if($('body.fl-nav-left').length != 0) {
				$(window).on('resize', $.throttle(500, this._navLeft));
				this._navLeft();
			}

			// Shrink Header
			if( ($('body.fl-shrink').length != 0) && !($('html.fl-builder-edit').length != 0) ) {
				$(window).on('resize', $.throttle(500, this._shrinkHeaderEnable));
				this._shrinkHeaderInit();
				this._shrinkHeaderEnable();
			}

			// Fixed Header (Fade In)
			if($('.fl-page-header-fixed').length != 0) {
				$(window).on('resize.fl-page-header-fixed', $.throttle(500, this._enableFixedHeader));
				this._enableFixedHeader();
			}

			// Fixed Header (Fixed)
			if( ($('body.fl-fixed-header').length != 0) && !($('html.fl-builder-edit').length != 0) ) {
				$(window).on('resize', $.throttle(500, this._fixedHeader));
				this._fixedHeader();
			}

			// Hide Header Until Scroll
			if( ($('body.fl-scroll-header').length != 0) && !($('html.fl-builder-edit').length != 0) ) {
				$(window).on('resize', $.throttle(500, this._scrollHeader));
				this._scrollHeader();
			}

			// Mega Menu (Primary Nav)
			if($('.fl-page-header-primary').find( 'li.mega-menu' ).length != 0) {
				$(window).on('resize', $.throttle(500, this._megaMenu));
				this._megaMenu();
			}

			// Mega Menu (Fixed)
			if($('.fl-page-header-fixed').length != 0) {
				$(window).on('scroll.fl-mega-menu-on-scroll', $.throttle(500, this._megaMenuOnScroll));
				$(window).on('resize.fl-mega-menu-on-scroll', $.throttle(500, this._megaMenuOnScroll));
			}

			// Headers not be fixed when the builder is active
			if($('html.fl-builder-edit').length != 0) {
				this._fixedHeadersWhenBuilderActive();
			}

			// Responsive Nav Layout
			if ( $( 'body.fl-nav-mobile-offcanvas' ).length != 0 && ! $( 'html.fl-builder-edit' ).length != 0 ) {
				$(window).on('resize', $.throttle(500, this._setupMobileNavLayout));
				this._setupMobileNavLayout();
				this._toggleMobileNavLayout();
			}

			// Close Dropdown or Flyout Menu
			$( 'body' ).on( 'click', this.closeMenu );

			// Close Dropdown or Flyout Menu when tabbing out from the last menu item.
			$( '.fl-theme-menu > li:last-child' ).on( 'focusout', function (e) {
				if ( undefined === $(e.relatedTarget)[0] || 'nav-link' !== $(e.relatedTarget)[0].className) {
					self.closeMenu( e );
				}
			});

			// Footer parallax effect
			if($('.fl-full-width.fl-footer-effect').length != 0) {
				$(window).on('resize', $.throttle(500, this._footerEffect));
				this._footerEffect();
			}

			// Go to Top
			if($('body.fl-scroll-to-top').length != 0) {
				this._toTop();
			}

			// Lightbox
			if(typeof $('body').magnificPopup != 'undefined') {
				this._enableLightbox();
			}

			// FitVids
			if(typeof $.fn.fitVids != 'undefined' && !$('body').hasClass('fl-builder')) {
				this._enableFitVids();
			}
			FLTheme._navBackiosFix();

			// Smooth scrolling.
			this._initSmoothScroll()
		},

		/**
		 * Checks to see if the current device is mobile.
		 *
		 * @since 1.5.1
		 * @access private
		 * @method _isMobile
		 * @return {Boolean}
		 */
		_isMobile: function()
		{
			return /Mobile|Android|Silk\/|Kindle|BlackBerry|Opera Mini|Opera Mobi|webOS/i.test( navigator.userAgent );
		},

		/**
		 * Initializes retina images.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initRetinaImages
		 */
		_initRetinaImages: function()
		{
			var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;

			if ( pixelRatio > 1 ) {
				$( 'img[data-retina]' ).each( FLTheme._convertImageToRetina );
			}
		},

		/**
		 * Converts an image to retina.
		 *
		 * @since 1.0
		 * @access private
		 * @method _convertImageToRetina
		 */
		_convertImageToRetina: function()
		{
			var image       = $( this ),
				tmpImage    = new Image(),
				src         = image.attr( 'src' ),
				retinaSrc   = image.data( 'retina' )

			// check for cloudflare
			if( typeof src == 'undefined') {
				src = image.data( 'cfsrc' )
			}

			// still no src, bail.
			if( typeof src == 'undefined') {
				return false;
			}

			var type = src.split('.').pop();

			if ( '' != retinaSrc  ) {

				tmpImage.onload = function() {
					var width = tmpImage.width,
						height = tmpImage.height;

					if ( 'svg' == type ) {
						width = image.width();
						height = image.height();
					}

				//image.css( 'max-height', height );
					image.width( width );
					image.attr( 'src', retinaSrc );
				};

				tmpImage.src = src;
			}
		},

		/**
		 * Mobile Header Logo.
		 *
		 * @since 1.7
		 * @access private
		 * @method _initMobileHeaderLogo
		 */
		_initMobileHeaderLogo: function()
		{
			this._enableMobileLogo();
			$( window ).on( 'resize', $.proxy( this._enableMobileLogo, this ) );
		},

		/**
		 * Enable Mobile Logo.
		 *
		 * @since 1.7
		 * @access private
		 * @method _enableMobileLogo
		 */
		_enableMobileLogo: function()
		{
			var win       = $( window ),
				logoWrap  = $( '.fl-page-header-logo' ),
				logos     = logoWrap.find( 'img[data-mobile]' ),
				image     = null,
				mobileSrc = null,
				tmpImage  = null;

			if ( 0 === logos.length ) {
				return;
			}

			$( logos ).each( function(){
				tmpImage  = new Image();
				image     = $( this );
				src       = image.attr( 'src' );
				mobileSrc = image.data( 'mobile' );

				image.attr( 'src', '' );
				image.attr( 'data-src', src );

				if ( win.width() < window.themeopts.mobile_breakpoint ) {

					if ( '' != mobileSrc ) {
						tmpImage.onload = function() {
							image.attr( 'src', mobileSrc );
						};
						tmpImage.src = src;
						image.show();
					}
				}
				else {
					if ( 'undefined' !== typeof image.data('src') ) {
						image.attr( 'src', image.data('src') );
						image.css( 'width', '' );
					}
				}
			});
		},

		/**
		 * Toggles a collapsed navbar when the toggle button is
		 * clicked and the current framework is the bootstrap base.
		 *
		 * @since 1.7
		 * @param {Object} e
		 */
		navbarToggleClick: function( e ) {
			var menuType = $('body').hasClass('fl-nav-mobile-offcanvas') ? 'flyout' : 'dropdown';

			if ( 'dropdown' === menuType ) {
				var	navBar = $(e.target).closest('.fl-page-nav'),
					targetPanel = navBar.find('.fl-page-nav-collapse');

				targetPanel.toggleClass('collapse');
				targetPanel.toggleClass('in');

			} else if ( 'flyout' === menuType ) {

				$( '.fl-page' ).toggleClass( 'fl-nav-offcanvas-active' );

			}

			e.stopPropagation();
		},

		/**
		 * Closes Dropdown or Flyout Menu when clicked anywhere on the page.
		 *
		 * @since 1.7.9
		 * @param {Object} e
		 */
		closeMenu: function (e) {
			var menuType = $( 'body' ).hasClass( 'fl-nav-mobile-offcanvas' ) ? 'flyout' : 'dropdown',
				isDropdownMenuActive = $( '.fl-page-nav-collapse' ).hasClass( 'in' ),
				isFlyoutMenuActive = $( '.fl-page' ).hasClass( 'fl-nav-offcanvas-active' ),
				pageNav;

			if ( undefined === e || undefined === e.target ) {
				return;
			}

			if ( 'dropdown' === menuType && isDropdownMenuActive ) {
				pageNav = $( '.navbar-collapse.in' ).closest( '.fl-page-nav' );
				pageNav.find( '.navbar-toggle' ).trigger( 'click' );
			} else if ( 'flyout' === menuType && isFlyoutMenuActive) {
				$( '.fl-offcanvas-close' ).trigger( 'click' );
			}
		},

		/**
		 * Initializes drop down nav logic.
		 *
		 * @since 1.0
		 * @access private
		 * @method _setupDropDowns
		 */
		_setupDropDowns: function()
		{
			$('ul.sub-menu').each(function(){
				$(this).closest('li').attr('aria-haspopup', 'true');
			});
		},

		/**
		 * Initializes drop down menu logic for top bar navs.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableTopNavDropDowns
		 */
		_enableTopNavDropDowns: function()
		{
			var nav        = $('.fl-page-bar-nav'),
				navItems   = nav.find(' > li'),
				subToggles = nav.find('> li').has('> ul.sub-menu').find('.fl-submenu-toggle-icon');

			if ( FLTheme._isMobile() ) {
				if( false !== /iPhone|iPad/i.test( navigator.userAgent ) ) {
					navItems.hover(FLTheme._navItemMouseover, FLTheme._navItemMouseout);
				}
				else {
					navItems.hover(function(){}, FLTheme._navItemMouseout);
					subToggles.on('click', FLTheme._navSubMenuToggleClick);
				}
			}
			else {
				navItems.hover(FLTheme._navItemMouseover, FLTheme._navItemMouseout);
			}
		},

		/**
		 * Fixes an ios quirk, if we have a dropdown menu and a user clicks 'back'
		 * the menu will stay open, ios does not reparse js when you click back.
		 * This method detects that back has been clicked by looking for event.persisted
		 *
		 * @since 1.6.2
		 * @method _navBackiosFix
		 */
		_navBackiosFix: function() {
			ipad = (navigator.userAgent.match('iPhone|iPad') !== null && $('.menu-item-has-children').length > 0) ? true : false;
			if( false !== ipad ) {
				window.onpageshow = function(event) {
					if (event.persisted) {
						window.location.reload()
					}
				}
			}
		},

		/**
		 * Initializes builder smooth scrolling.
		 *
		 * @since 1.7.6
		 * @return void
		 */
		_initSmoothScroll: function() {
			// Bail if builder doesn't exist.
			if ('undefined' === typeof FLBuilderLayout) {
				return;
			}

			// if scroll is disabled globally via filter do not scroll.
			if ('undefined' !== typeof window.themeopts.smooth && 'disabled' === window.themeopts.smooth) {
				return;
			}

			if (location.hash && $( location.hash ).length) {
				setTimeout(function() {
					window.scrollTo(0, 0);
					FLBuilderLayout._scrollToElement( $(location.hash) );
				}, 1);
			}
		},

		/**
		 * Initializes drop down menu logic for the main nav for primary and fixed header.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enablePageNavDropDowns
		 */
		_enablePageNavDropDowns: function()
		{
			var pageNav	   = $('.fl-page-header');

			pageNav.each( FLTheme._enablePageNavDropDown );
		},

		/**
		 * Callback logic for each main header nav dropdown
		 *
		 * @since 1.6
		 * @access private
		 * @method _enablePageNavDropDown
		 */
		_enablePageNavDropDown: function()
		{
			var pageNav	   = $(this),
				nav        = pageNav.find('.fl-page-nav .fl-page-nav-collapse'),
				navItems   = nav.find('ul li'),
				subToggles = nav.find('li').has('> ul.sub-menu').find('> a'),
				toggleIcon = nav.find('li').has('> ul.sub-menu').find('.fl-submenu-toggle-icon'),
				subMenus   = nav.find('> ul > li').has('ul.sub-menu');

			if( $( '.fl-page-nav .navbar-toggle' ).is( ':visible' ) ) {
				navItems.off('mouseenter mouseleave');

				// Toggle submenu
				if ( $( 'body' ).hasClass( 'fl-submenu-toggle' ) ) {
					subMenus = nav.find('> ul li').has('ul.sub-menu');
				}
				subMenus.find('> a').off().on('click', FLTheme._navItemClickMobile);
				subMenus.find('.fl-submenu-toggle-icon').off().on('click', FLTheme._navItemClickMobile);

				nav.find('.menu').on('click', '.menu-item > a[href*="#"]', FLTheme._toggleForMobile);
				subToggles.off('click', FLTheme._navSubMenuToggleClick);
			}
			else {
				nav.find('a').off('click', FLTheme._navItemClickMobile);
				nav.find('a').off('click', FLTheme._toggleForMobile);
				nav.find('.fl-submenu-toggle-icon').off('click', FLTheme._navItemClickMobile);
				nav.removeClass('in').addClass('collapse');
				navItems.removeClass('fl-mobile-sub-menu-open');
				navItems.find('a').width(0).width('auto');

				if ( FLTheme._isMobile() ) {
					navItems.hover(function(){}, FLTheme._navItemMouseout);
					subToggles.on('click', FLTheme._navSubMenuToggleClick);
				}
				else {
					navItems.keydown( function(e){
						if ( 9 === e.keyCode ) {
							el = $(this)
							focused = el.find(':focus')
							if( focused.parent().is(':last-child') ) {
								sub  = focused.parent().find('ul.sub-menu').first()
								mega = focused.parent().parent().parent().parent().parent().hasClass('mega-menu')
								mega_last = focused.parent().parent().parent().is(':last-child' )

								if ( sub.length > 0 ) {
									sub.trigger('mouseenter')
								} else {
									if ( ! mega || mega_last ) {
										el.trigger('mouseleave')
									}
								}
							}
							parent = focused.closest('ul.sub-menu').parent()
							if ( ! parent.hasClass('fl-sub-menu-open') ) {
								focused.trigger('mouseenter')
							}
						}
					} );
					navItems.hover(FLTheme._navItemMouseover, FLTheme._navItemMouseout);
				}
			}
		},

		/**
		 * Callback for when an item in a nav is clicked on mobile.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemClickMobile
		 * @param {Object} e The event object.
		 */
		_navItemClickMobile: function(e)
		{
			var nav     = $(this).closest('.fl-page-nav-collapse'),
				parent  = $(this).closest('li'),
				href    = $(this).attr('href'),
				subMenu = parent.find( 'ul.sub-menu' ),
				toggle  = $(e.target).hasClass('fl-submenu-toggle-icon'),
				subChildren = null;

			if( href && '#' !== href ) {
				var targetId = href.split('#')[1];
				if ( $('body').find('#'+  targetId).length > 0 && parent.hasClass( 'fl-mobile-sub-menu-open' ) ) {
					el = $(this).parent().closest('nav').find( '.navbar-toggle')
					el.trigger('click');
					if ( 'undefined' !== typeof FLBuilderLayout && ( 'undefined' === typeof window.themeopts.smooth && 'disabled' !== window.themeopts.smooth ) ) {
						setTimeout(function() {
							window.scrollTo(0, 0);
							FLBuilderLayout._scrollToElement( $('#'+  targetId) );
						}, 1);
					}
				}
			}

			if( ( '#' == href || toggle ) && parent.hasClass( 'fl-mobile-sub-menu-open' ) ) {
				e.preventDefault();
				parent.removeClass('fl-mobile-sub-menu-open');
				subMenu.hide();
			}
			else if(!parent.hasClass('fl-mobile-sub-menu-open')) {

				e.preventDefault();
				parent.addClass('fl-mobile-sub-menu-open');

				if ( toggle && 0 === $('.fl-submenu-toggle').length ) {
					subChildren = subMenu.find( 'li.menu-item-has-children' );
					subChildren.addClass('fl-mobile-sub-menu-open');
				}

				subMenu.fadeIn(200);
			}

			if ( $( '.fl-nav-collapse-menu' ).length != 0 ) {
				nav.find( 'li.fl-mobile-sub-menu-open' )
					.not( $(this).parents( '.fl-mobile-sub-menu-open' ) )
					.not( subChildren )
					.removeClass( 'fl-mobile-sub-menu-open' )
					.find( 'ul.sub-menu' ).hide();
			}

			e.stopPropagation();
		},

		/**
		 * Setup and callback for nav item link that exists on a page.
		 *
		 * @since 1.5.3
		 * @access private
		 * @method _setupCurrentNavItem
		 * @param {Object|Null} e The event object.
		 */
		_setupCurrentNavItem: function(e)
		{
			var nav 		= $('.fl-page-nav .navbar-nav'),
				targetId 	= typeof e !== 'undefined' ? $(e.target).prop('hash') : window.location.hash,
				targetId  = targetId.replace( /(:|\.|\[|\]|,|=|@|\/)/g, "\\$1" ),
				currentLink = targetId.length ? nav.find('a[href*=\\' + targetId + ']:not([href=\\#])') : null,
				closeButton = nav.closest('.fl-page-nav').find('.fl-offcanvas-close');

			if ( currentLink != null && $('body').find(targetId).length > 0 ) {
				$( '.current-menu-item' ).removeClass( 'current-menu-item' );
				currentLink.parent().addClass('current-menu-item');

				if ( closeButton ) {
					closeButton.trigger( 'click' );
				}
			}
		},


		/**
		 * Callback for when the mouse leaves an item
		 * in a nav at desktop sizes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemMouseover
		 */
		_navItemMouseover: function()
		{
			if($(this).find('ul.sub-menu').length === 0) {
				return;
			}

			var li              = $(this),
				parent          = li.parent(),
				subMenu         = li.find('ul.sub-menu'),
				subMenuWidth    = subMenu.width(),
				subMenuPos      = 0,
				winWidth        = $(window).width(),
				spacerPos       = 0,
				subMenuTopPos   = 0;

			if(li.closest('.fl-sub-menu-right').length !== 0) {
				li.addClass('fl-sub-menu-right');
			}
			else if($('body').hasClass('rtl')) {

				subMenuPos = parent.is('ul.sub-menu') ?
							 parent.offset().left - subMenuWidth:
							 li.offset().left - subMenuWidth;

				if(subMenuPos <= 0) {
					li.addClass('fl-sub-menu-right');
				}
			}
			else {

				subMenuPos = parent.is('ul.sub-menu') ?
							 parent.offset().left + (subMenuWidth * 2) :
							 li.offset().left + subMenuWidth;

				if(subMenuPos > winWidth) {
					li.addClass('fl-sub-menu-right');
				}
			}

			li.addClass('fl-sub-menu-open');

			if ( ! li.hasClass('hide-heading') ) {
				subMenu.hide();
				subMenu.stop().fadeIn(200);
			}

			FLTheme._hideNavSearch();

			// Mega menu hover fix
			if ( li.closest( '.fl-page-nav-collapse' ).length !== 0 && li.hasClass( 'mega-menu' ) ) {

				if ( li.find( '.mega-menu-spacer' ).length > 0 ) {
					return;
				}

				subMenu.first().before( '<div class="mega-menu-spacer"></div>' );
				spacerPos = li.find( '.mega-menu-spacer' ).offset().top;
				subMenuTopPos = subMenu.first().offset().top;
				li.find( '.mega-menu-spacer' ).css('padding-top', Math.floor( parseInt(subMenuTopPos - spacerPos) ) + 'px' );
			}
		},

		/**
		 * Callback for when the mouse leaves an item
		 * in a nav at desktop sizes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemMouseout
		 */
		_navItemMouseout: function()
		{
			var li      = $(this),
				subMenu = li.find('ul.sub-menu');


			if ( ! li.hasClass('hide-heading') ) {
				subMenu.stop().fadeOut({
					duration: 200,
					done: FLTheme._navItemMouseoutComplete
				});
			}
			else {
				FLTheme._navItemMouseoutComplete();
			}
		},

		/**
		 * Callback for when the mouse finishes leaving an item
		 * in a nav at desktop sizes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemMouseoutComplete
		 */
		_navItemMouseoutComplete: function()
		{
			var li = $(this).parent();

			li.removeClass('fl-sub-menu-open');
			li.removeClass('fl-sub-menu-right');

			if ( li.find( '.mega-menu-spacer' ).length > 0 ) {
				li.find( '.mega-menu-spacer' ).remove();
			}

			$(this).show();
		},

		/**
		 * Callback for when a submenu toggle is clicked on mobile.
		 *
		 * @since 1.5.1
		 * @access private
		 * @method _navSubToggleClick
		 * @param {Object} e The event object.
		 */
		_navSubMenuToggleClick: function( e )
		{
			var li = $( this ).closest( 'li' ).eq( 0 );

			if ( ! li.hasClass( 'fl-sub-menu-open' ) ) {
				FLTheme._navItemMouseover.apply( li[0] );

				e.preventDefault();
			}
		},

		/**
		 * Logic for the menu item  when clicked on mobile.
		 *
		 * @since  1.5.3
		 * @return void
		 */
		 _toggleForMobile: function( e ){
 			var nav 	= $('.fl-page-nav .fl-page-nav-collapse'),
 				href 	= $(this).attr('href'),
				hasSubmenu = $(this).closest('li').hasClass( 'menu-item-has-children' );

 			if ( href !== '#' ) {
				var targetId = href.split('#')[1];

				if ( $('body').find('#'+  targetId).length > 0 && !hasSubmenu ) {
 					/**
 					 * Make sure bootstrap collapse is available before using it.
 					 */
 					if ( ! $.isFunction(nav.collapse) ) {
 						el = $(this).parent().closest('nav').find( '.navbar-toggle')
 						el.trigger('click')
 					} else {
 						nav.collapse('hide');
 					}
 				}
 			}
 		},

		/**
		 * Shows or hides the nav search form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _toggleNavSearch
		 */
		_toggleNavSearch: function(e)
		{
			var form = $('.fl-page-nav-search form');

			e.preventDefault();

			if(form.is(':visible')) {
				form.stop().fadeOut(200);
			}
			else {
				form.stop().fadeIn(200);
				$('body').on('click.fl-page-nav-search', FLTheme._hideNavSearch);
				$('.fl-page-nav-search .fl-search-input').focus();
			}
		},

		/**
		 * Hides the nav search form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _hideNavSearch
		 * @param {Object} e (Optional) An event object.
		 */
		_hideNavSearch: function(e)
		{
			var form = $('.fl-page-nav-search form');

			if(e !== undefined) {
				if($(e.target).closest('.fl-page-nav-search').length > 0) {
					return;
				}
			}

			form.stop().fadeOut(200);

			$('body').off('click.fl-page-nav-search');
		},

		/**
		 * Nav Vertical
		 *
		 * @since 1.5
		 * @access private
		 * @method _navVertical
		 */
		_navVertical: function()
		{
			var win = $(window);


			if(win.width() >= window.themeopts.medium_breakpoint && $('.fl-page-header-primary').hasClass('fl-page-nav-toggle-visible-always')){
				$('body').toggleClass('fl-nav-vertical');

				if( $('body').hasClass('fl-nav-vertical-left') ) {
					$('body').toggleClass('fl-nav-vertical-left');
				}

				if( $('body').hasClass('fl-nav-vertical-right') ) {
					$('body').toggleClass('fl-nav-vertical-right');
				}
			}
		},

		/**
		 * Right position fix for right navigation on boxed layout
		 *
		 * @since 1.5
		 * @access private
		 * @method _updateVerticalRightPos
		 */
		_updateVerticalRightPos: function()
		{
			var win             = $(window).width(),
				flpage          = $('.fl-page').width(),
				vericalRightPos = ( (win - flpage) / 2 );

			$('.fl-page-header-vertical').css('right', vericalRightPos);
		},

		/**
		 * Nav Left
		 *
		 * @since 1.5
		 * @access private
		 * @method _navLeft
		 */
		_navLeft: function()
		{
			var win = $(window);

			if(win.width() < window.themeopts.medium_breakpoint || $('.fl-page-header-primary').hasClass('fl-page-nav-toggle-visible-always')) {
				$('.fl-page-header-primary .fl-page-logo-wrap').insertBefore('.fl-page-header-primary .fl-page-nav-col');
			}
			if(win.width() >= window.themeopts.medium_breakpoint && !$('.fl-page-header-primary').hasClass('fl-page-nav-toggle-visible-always')) {
				$('.fl-page-header-primary .fl-page-nav-col').insertBefore('.fl-page-header-primary .fl-page-logo-wrap');
			}

			if($('.fl-page-header-fixed').length != 0 && ! $('.fl-page-header-fixed').hasClass('fl-page-nav-toggle-visible-always') ) {
				$('.fl-page-header-fixed .fl-page-fixed-nav-wrap').insertBefore('.fl-page-header-fixed .fl-page-logo-wrap');
			}
		},

		/**
		 * Initialize header shrinking.
		 *
		 * @since 1.5.2
		 * @access private
		 * @method _shrinkHeaderInit
		 */
		_shrinkHeaderInit: function()
		{
			var distanceY = $(window).scrollTop(),
				shrinkOn  = 250,
				header    = $( '.fl-page-header' );

			$( 'body' ).addClass( 'fl-shrink-header-enabled' );

			if ('scrollRestoration' in history) {
				history.scrollRestoration = 'manual';
			}

			$('.fl-page-header-logo').imagesLoaded(function(){
				var logo       = $( '.fl-logo-img' ),
					logoHeight = logo.height();

				// Check to see if original height is set on scroll while the page is reloading.
				if ( 'undefined' !== typeof logo.data('origHeight') ) {
					logoHeight = parseInt( logo.data('origHeight') );
				}

				logo.css( 'max-height', logoHeight );

				setTimeout( function() {
					$('.fl-page-header').addClass( 'fl-shrink-header-transition' );

					// Shrink on page load
					if ( distanceY > shrinkOn ) {
						header.addClass( 'fl-shrink-header' );
					}
					else {
						header.removeClass( 'fl-shrink-header' );
					}
				}, 100 );
			});
		},

		/**
		 * Enable or disable header shrinking.
		 *
		 * @since 1.5
		 * @access private
		 * @method _shrinkHeaderEnable
		 */
		_shrinkHeaderEnable: function()
		{
			var win = $( window );

			if ( win.width() >= window.themeopts.medium_breakpoint ) {

				var header             = $('.fl-page-header'),
					headerHeight       = header.outerHeight(),
					topbar             = $('.fl-page-bar'),
					topbarHeight       = 0,
					totalHeaderHeight  = 0;

				if( topbar.length != 0 ) {
					topbarHeight      += topbar.outerHeight();
					totalHeaderHeight  = topbarHeight + headerHeight;

					if ( $( 'body.admin-bar' ).length != 0 ) {
						topbarHeight += 32;
					}
					header.css( 'top' , topbarHeight );
				}
				else {
					totalHeaderHeight = headerHeight;
				}

				// Fix: Themer parts layout inserted before header.
				if ( header.prevAll( '.fl-builder-content' ).length > 0 ) {
					FLTheme._initThemerLayoutFix();

					totalHeaderHeight = topbar.outerHeight();
				}

				if ( $('.fl-header-padding-top-custom').length === 0 ) {
					$( '.fl-page' ).css( 'padding-top', totalHeaderHeight );
				}

				$( win ).on( 'scroll.fl-shrink-header', FLTheme._shrinkHeader );
			}
			else {
				$('.fl-page-header').css('top', 0);
				$( '.fl-page' ).css( 'padding-top', 0 );
				$( win ).off( 'scroll.fl-shrink-header' );
			}
		},

		/**
		 * Shrink the header.
		 *
		 * @since 1.5.1
		 * @access private
		 * @method _shrinkHeader
		 */
		_shrinkHeader: function()
		{
			var distanceY = $( this ).scrollTop(),
				shrinkOn  = 250,
				header    = $( '.fl-page-header' ),
				logo      = null;

			$('.fl-page-header-logo').imagesLoaded(function(){
				logo = $( '.fl-logo-img' );

				if ( 'undefined' === typeof logo.data('origHeight') ) {
					logo.data( 'origHeight', logo.height() );
				}

				if ( distanceY > shrinkOn ) {
					header.addClass( 'fl-shrink-header' );
				}
				else {
					header.removeClass( 'fl-shrink-header' );
				}

				if ( 'undefined' !== typeof header.data( 'original-top' ) ) {
					FLTheme._fixThemerLayoutOnScroll();
				}
			});
		},

		/**
		 * Fixed Header (Fixed)
		 *
		 * @since 1.5
		 * @access private
		 * @method _fixedHeader
		 */
		_fixedHeader: function()
		{
		 	var win               = $(window),
		 		header            = $('.fl-page-header'),
		 		headerHeight      = 0,
		 		totalHeaderHeight = 0,
		 		bar               = $('.fl-page-bar'),
		 		barHeight         = 0;

		 	if(win.width() >= window.themeopts.medium_breakpoint) {

		 		headerHeight = header.outerHeight();

		 		if( bar.length != 0 ) {

		 			barHeight         = bar.outerHeight();
		 			totalHeaderHeight = barHeight + headerHeight;

		 			if($('body.admin-bar').length != 0) {
		 				barHeight += 32;
		 			}

		 			if($('html.fl-builder-edit').length != 0) {
		 				var topbarHeight = topbarHeight+11;
		 			}

		 			header.css('top', barHeight);
		 		}
		 		else {
		 			totalHeaderHeight = headerHeight;
		 		}

				// Fix: Themer parts layout.
				if ( header.prevAll( '.fl-builder-content' ).length > 0 ) {
					FLTheme._initThemerLayoutFix();
					totalHeaderHeight = bar.outerHeight();

					$( win ).on( 'scroll.fl-fixed-header', FLTheme._fixThemerLayoutOnScroll );
				}

				if($('body.fl-scroll-header').length === 0 && $('.fl-header-padding-top-custom').length === 0 ) {
					$('.fl-page').css('padding-top', totalHeaderHeight);
				}

				$( win ).trigger( 'scroll' );
		 	}
		 	else {
		 		$('.fl-page-header').css('top', 0);
		 		$('.fl-page').css('padding-top', 0);
				$( win ).off( 'scroll.fl-fixed-header' );
		 	}
		},

		/**
		 * Fixed Header (Fade In)
		 *
		 * Enables the fixed header if the window is wide enough.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableFixedHeader
		 */
		_enableFixedHeader: function()
		{
			var win = $(window);

			if(win.width() < window.themeopts.medium_breakpoint) {
				win.off('scroll.fl-page-header-fixed');
				$('.fl-page-header-fixed').hide();
			}
			else {
				win.on('scroll.fl-page-header-fixed', FLTheme._toggleFixedHeader);
			}
		},

		/**
		 * Fix the Themer parts layout that are hooks into `fl_page_open`.
		 *
		 * @since 1.7
		 * @access private
		 * @method _fixThemerLayout
		 */
		_initThemerLayoutFix: function() {
			var header             = $('.fl-page-header'),
				themerLayouts      = header.prevAll( '.fl-builder-content' ),
				themerlayoutHeight = 0;

			if ( ! themerLayouts.length ) {
				return;
			}

			header.css('position', 'initial');

			$.each( themerLayouts, function() {
				themerlayoutHeight += $( this ).outerHeight();
			});

			header.data( 'original-top', themerlayoutHeight );
		},

		/**
		 * Fix the Themer parts layout on scroll.
		 *
		 * @since 1.7
		 * @access private
		 * @method _fixThemerLayout
		 */
		_fixThemerLayoutOnScroll: function() {
			var distanceY     = $( window ).scrollTop(),
				header        = $( '.fl-page-header' ),
				headerTop     = header.data( 'original-top' );

			if ( 'undefined' === typeof headerTop ) {
				return;
			}

			if ( distanceY >= headerTop ) {
				header.css( 'position', 'fixed' );
			}
			else {
				header.css( 'position', 'initial' );
			}

			// Fix animations.
			if ( 'undefined' != typeof Waypoint ) {
				Waypoint.refreshAll();
			}
		},

		/**
		 * Shows or hides the fixed header based on the
		 * window's scroll position.
		 *
		 * @since 1.0
		 * @access private
		 * @method _toggleFixedHeader
		 */
		_toggleFixedHeader: function()
		{
			var win             = $(window),
				fixed           = $('.fl-page-header-fixed'),
				fixedVisible    = fixed.is(':visible'),
				header          = $('.fl-page-header-primary'),
				headerHidden    = false;

			if ( 0 === header.length ) {
				headerHidden = win.scrollTop() > 200;
			}
			else {
				headerHidden = win.scrollTop() > header.height() + header.offset().top;
			}

			if(headerHidden && !fixedVisible) {
				fixed.stop().fadeIn(200);
			}
			else if(!headerHidden && fixedVisible) {
				fixed.stop().hide();
			}
		},

		/**
		 * Adds logo as nav item for "centered inline logo" header layout.
		 *
		 * @since 1.5
		 * @access private
		 * @method _centeredInlineLogo
		 */
		_centeredInlineLogo: function()
		{
			var win               = $(window),
				$logo             = $( '.fl-page-nav-centered-inline-logo .fl-page-header-logo' ),
				$inline_logo      = $( '.fl-logo-centered-inline > .fl-page-header-logo' ),
				$nav              = $( '.fl-page-nav-centered-inline-logo .fl-page-nav .navbar-nav' ),
				nav_li_length     = $nav.children('li').length,
				logo_li_location  = Math.round( nav_li_length / 2 ) - 1;

			if(win.width() >= window.themeopts.medium_breakpoint && $inline_logo.length < 1 && !$('.fl-page-header-primary').hasClass('fl-page-nav-toggle-visible-always')) {

				if( $logo.hasClass( 'fl-inline-logo-left' ) && nav_li_length % 2 != 0 ) {
					$nav.children( 'li:nth( '+logo_li_location+' )' ).before( '<li class="fl-logo-centered-inline"></li>' );
				} else {
					$nav.children( 'li:nth( '+logo_li_location+' )' ).after( '<li class="fl-logo-centered-inline"></li>' );
				}

				$nav.children( '.fl-logo-centered-inline' ).append( $logo );
		 	}

		 	if(win.width() < window.themeopts.medium_breakpoint) {
		 		$( '.fl-page-nav-centered-inline-logo .fl-page-header-row' ).prepend( $inline_logo );
		 		$( '.fl-logo-centered-inline' ).remove();
		 	}
		},

		/**
		 * Hide Header Until Scroll
		 *
		 * @since 1.5
		 * @access private
		 * @method _scrollHeader
		 */
		_scrollHeader: function()
		{
			var win      = $(window),
				header   = null,
				distance = $('.fl-page-header-primary').data('fl-distance'),
				headerHeight = 0;

			if($('.fl-page-bar').length != 0 ) {
				header = $('.fl-page-header-primary, .fl-page-bar');
			}
			else {
				header = $('.fl-page-header-primary');
			}

			if(win.width() >= window.themeopts.medium_breakpoint) {
				win.on('scroll.fl-show-header-on-scroll', function () {
					if ($(this).scrollTop() > distance) {
						header.addClass('fl-show');
					}
					else {
						header.removeClass('fl-show');

						// Offcanvas nav layout fix.
						if ( $('.fl-responsive-nav-enabled').length ) {
							headerHeight = $('.fl-page-header-primary').height() * 2;

							if ( $('.fl-page-bar').length != 0 ) {
								headerHeight += $('.fl-page-bar').height();
							}

							if ( 'undefined' !== typeof $( '.fl-nav-offcanvas-collapse' ).css('top') ) {
								headerHeight += parseInt( $( '.fl-nav-offcanvas-collapse' ).css('top') );
							}
						}

						if ( $( '.fl-nav-offcanvas-active' ).length && headerHeight > 0 ) {
							$( '.fl-nav-offcanvas-collapse' ).css({
								'transform' 		: 'translateY(' + headerHeight + 'px)',
								'-ms-transform' 	: 'translateY(' + headerHeight + 'px)',
								'-webkit-transform' : 'translateY(' + headerHeight + 'px)'
							});
						}
					}
				});
			}
			else {
				win.off('scroll.fl-show-header-on-scroll');
				$( '.fl-nav-offcanvas-collapse' ).css('transform', '');
			}
		},

		/**
		 * Mega Menu
		 *
		 * @see _isResponsiveNavEnabled
		 * @since 1.5
		 * @access private
		 * @method _megaMenu
		 */
		_megaMenu: function()
		{
			var win      			= $(window),
				pageHeaderMenu 		= $('.fl-page-header'),
				menuContainer 		= pageHeaderMenu.find('.fl-page-header-container'),
				menuWidthLimit 		= menuContainer.outerWidth(),
				megaItem			= null,
				megaItems 			= null,
				megaContentWidth 	= 0;

			pageHeaderMenu.find( 'li.mega-menu, li.mega-menu-disabled' ).each(function(){
				megaItem 			= $(this);
				megaContentWidth 	= megaItem.find('> ul.sub-menu').outerWidth();

				if ( typeof megaItem.data('megamenu-width') !== 'undefined' ) {
					megaContentWidth = megaItem.data('megamenu-width');
				}

				if( ( megaItem.hasClass('mega-menu') && menuWidthLimit < megaContentWidth ) || FLTheme._isResponsiveNavEnabled() ) {
					megaItem.data('megamenu-width', megaContentWidth);

					// Fixed width issue on window resize
					if ( FLTheme._isResponsiveNavEnabled() ) {
						megaItem.find('> ul.sub-menu').css('display', 'block');
					}

					megaItem.removeClass('mega-menu');
					if (!megaItem.hasClass('mega-menu-disabled')) {
						megaItem.addClass('mega-menu-disabled');
					}
				}
				else if ( megaItem.hasClass('mega-menu-disabled') && menuWidthLimit >= megaContentWidth ) {
					// Reset sub-menu display style
					megaItem.find('> ul.sub-menu').css('display', '');

					megaItem.removeClass('mega-menu-disabled');
					if (!megaItem.hasClass('mega-menu')) {
						megaItem.addClass('mega-menu');
					}
					megaItem.addClass( 'mega-menu-items-' + megaItem.children( 'ul' ).children( 'li' ).length );
				}
			});


		},

		/**
		 * Mega Menu - Fixed Header
		 *
		 * @since 1.5
		 * @access private
		 * @method _megaMenuOnScroll
		 */
		_megaMenuOnScroll: function()
		{
			var win      		= $(window),
				pageHeaderFixed = $('.fl-page-header-fixed'),
				menuContainer 	= pageHeaderFixed.find('.fl-page-header-container'),
				fixedVisible 	= pageHeaderFixed.is(':visible'),
				megaItem		= null,
				megaMenuContent = null;

			if ( fixedVisible ) {
				pageHeaderFixed.find( 'li.mega-menu' ).each(function(){
					megaItem 		= $(this);
					megaMenuContent = megaItem.find('> ul.sub-menu');

					// Disable mega menu if it's off screen
					if(menuContainer.outerWidth() < megaMenuContent.outerWidth()) {
						megaItem.removeClass('mega-menu');
						if (!megaItem.hasClass('mega-menu-disabled')) {
							megaItem.addClass('mega-menu-disabled');
						}
					}
					else {
						megaItem.removeClass('mega-menu-disabled');
						if (!megaItem.hasClass('mega-menu')) {
							megaItem.addClass('mega-menu');
						}

						megaItem.addClass( 'mega-menu-items-' + megaItem.children( 'ul' ).children( 'li' ).length );
					}
				});

				win.off('scroll.fl-mega-menu-on-scroll');
				win.off('resize.fl-mega-menu-on-scroll');
			}
		},

		/**
		 * Fixed headers not be fixed when the builder is active
		 *
		 * @since 1.5.2
		 * @access private
		 * @method _fixedHeadersWhenBuilderActive
		 */
		_fixedHeadersWhenBuilderActive: function()
		{
			if($('body.fl-shrink').length != 0) {
				$('body').removeClass('fl-shrink');
			}

			if($('body.fl-fixed-header').length != 0) {
				$('body').removeClass('fl-fixed-header');
			}

			if($('body.fl-scroll-header').length != 0) {
				$('body').removeClass('fl-scroll-header');
			}
		},

		/**
		 * Responsive Nav Layout
		 *
		 * @since 1.7
		 * @access private
		 * @method _setupMobileNavLayout
		 */
		_setupMobileNavLayout: function()
		{
			var win       = $( window ),
			    button    = $( 'button.navbar-toggle' ),
				header    = $( '.fl-page-header:not(.fl-page-header-fixed)' ),
				navBar    = header.find( '.fl-page-nav-collapse' ),
				pageWrap  = $( '.fl-page' ),
				navBarTop = 0,
				navHeight = win.height(),
				pushOpacity = $( 'body').hasClass( 'fl-offcanvas-push-opacity-left' ) || $( 'body').hasClass( 'fl-offcanvas-push-opacity-right' ),
				logoPos   = header.find( '.fl-page-header-logo' ).offset();

			if ( FLTheme._isResponsiveNavEnabled() && button.is( ':visible' ) ) {
				$( 'body' ).addClass( 'fl-responsive-nav-enabled' );

				button.attr( 'data-toggle', 'offcanvas');
				navBar.addClass( 'fl-nav-offcanvas-collapse');

				if ( 0 === navBar.find( '.fl-button-close' ).length ) {
					navBar.prepend( '<div class="fl-button-close"><button class="fl-offcanvas-close" aria-label="Close Menu"><i class="fas fa-times"></i></button></div>' );
				}

				if ( pushOpacity && 0 === $( '.fl-offcanvas-opacity' ).length ) {
					pageWrap.append( '<div class="fl-offcanvas-opacity"></div>' );
				}

				if ( pageWrap.height() > win.height() ) {
					navHeight = $( document ).height();

					if ( $( 'body.fl-shrink' ).length != 0 ) {
						navHeight = navHeight - header.height();
					}
				}

				if ( $( 'body.admin-bar' ).length != 0 ) {
					navBarTop = $( '#wpadminbar' ).height();
					navHeight = navHeight - navBarTop;
				}

				if ( $( '.fl-page-bar' ).length != 0 && ! $( '.fl-page-header' ).hasClass( 'fl-page-nav-toggle-button' ) ) {
					navBarTop = navBarTop + ($( '.fl-page-bar' ).height() + 1);
				}

				if ( $('.fl-scroll-header').length && win.width() >= window.themeopts.medium_breakpoint ) {
					navBar.css('top', pageWrap.offset().top - navBarTop + 'px' );
				}
				else {
					navBar.css('top', '');
				}

			}
			else {
				button.attr( 'data-toggle', 'collapse');
				navBar.removeClass( 'fl-nav-offcanvas-collapse');
				navBar.find( '.fl-button-close' ).remove();
				navBar.css( 'height', '' );
				navBar.css( 'top', '' );
				pageWrap.removeClass( 'fl-nav-offcanvas-active' );
				$( 'body' ).find( '.fl-offcanvas-opacity' ).remove();
				$( 'body' ).removeClass( 'fl-responsive-nav-enabled' );
			}
		},

		/**
		 * Toggle the Responsive Nav Layout
		 *
		 * @since 1.7
		 * @access private
		 * @method _toggleMobileNavLayout
		 */
		_toggleMobileNavLayout: function()
		{

			$( '.fl-page-nav' ).on( 'click', '.fl-offcanvas-close', function(e){
				$( '.fl-page' ).toggleClass( 'fl-nav-offcanvas-active' );
				e.stopPropagation();
			});

		},

		/**
		 * Apply footer height as margin-bottom value for fl-page class
		 *
		 * @since 1.5
		 * @access private
		 * @method _footerEffect
		 */
		_footerEffect: function()
		{
			if ( $( window ).width() >= window.themeopts.mobile_breakpoint ) {
				$( '.fl-page' ).css( 'margin-bottom', $( '.fl-page-footer-wrap' ).height() );
			}
			else {
				$( '.fl-page' ).css( 'margin-bottom', 0 );
			}
		},

		/**
		 * Go to Top
		 *
		 * @since 1.5
		 * @access private
		 * @method _toTop
		 */
		_toTop: function()
		{
			var buttons = $('#fl-to-top');

			buttons.each(function(){
				$(this).click(function(){
					$('html,body').animate({ scrollTop: 0 }, 'linear');
					return false;
				});
			});

			$(window).scroll(function(){
				if($(this).scrollTop() > 800) {
					buttons.fadeIn();
				} else {
					buttons.fadeOut();
				}
			});
		},

		/**
		 * Initializes the lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableLightbox
		 */
		_enableLightbox: function()
		{
			var body = $('body');

			// Only works on NON bb pages/posts
			if( !body.hasClass('fl-builder') && !body.hasClass('woocommerce')) {

				$('.fl-content a').filter(function() {
					return /\.(png|jpg|jpeg|gif)(\?.*)?$/i.test(this.href);
				}).magnificPopup({
					closeBtnInside: false,
					type: 'image',
					gallery: {
						enabled: true
					}
				});
			}

			if( ( body.hasClass('fl-builder') || body.hasClass( 'fl-theme-builder-singular' ) ) && !body.hasClass('woocommerce') ) {
				$('.fl-rich-text a, .fl-module-fl-post-content a').filter(function() {
					return /\.(png|jpg|jpeg|gif)(\?.*)?$/i.test(this.href);
				}).magnificPopup({
					closeBtnInside: false,
					type: 'image',
					gallery: {
						enabled: true
					}
				});
			}
		},

		/**
		 * Initializes the fitVids
		 *
		 * @since 1.5.2
		 * @access private
		 * @method _enableFitVids
		 */
		_enableFitVids: function()
		{
			$('.fl-post-content').fitVids();
		},

		/**
		 * Check to see if responsive nav is enabled for the current window width
		 *
		 * @since 1.6.2
		 * @access private
		 * @method _isResponsiveNavEnabled
		 */
		_isResponsiveNavEnabled: function()
		{
			var win 	= $(window);
				enabled = false;

			if ( ( $( '.fl-page-nav-toggle-visible-always' ).length > 0 )
				|| ( $( '.fl-page-nav-toggle-visible-medium-mobile' ).length > 0 && win.width() < window.themeopts.medium_breakpoint )
				|| ( $( '.fl-page-nav-toggle-visible-mobile' ).length > 0 && win.width() < window.themeopts.mobile_breakpoint )
				) {
				enabled = true;
			}

			return enabled;
		}
	};

	$(function(){
		FLTheme.init();
	});

	// Mobile Logo
	if ( ! ( $( 'html.fl-builder-edit' ).length !== 0 ) ) {
		FLTheme._initMobileHeaderLogo();
	}
	FLTheme._initRetinaImages();

})(jQuery);
