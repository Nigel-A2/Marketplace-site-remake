( function( $ ) {

	/* Internal shorthand */
	var api = wp.customize;

	/**
	 * Helper class that contains data for showing and hiding controls.
	 *
	 * @since 1.2.0
	 * @class FLCustomizerToggles
	 */
	FLCustomizerToggles = {

		'fl-layout-width': [{
			controls: [ 'fl-layout-shadow-size', 'fl-layout-shadow-color' ],
			callback: function( val ) { return 'boxed' == val; }
		},{
			controls: [ 'fl-content-width' ],
			callback: function( val ) {

				var headerLayout = api( 'fl-header-layout' ).get();

				if ( 'boxed' == val && ( 'vertical-right' == headerLayout || 'vertical-left' == headerLayout ) ) {
					return false;
				}

                return true;
			}
		},{
			controls: [ 'fl-layout-spacing' ],
			callback: function( val ) {

				var headerLayout = api( 'fl-header-layout' ).get();

				if ( 'boxed' == val && ( 'vertical-right' == headerLayout || 'vertical-left' == headerLayout ) ) {
					return false;
				}

                return 'boxed' == val;
			}
		},{
			controls: [ 'fl-footer-parallax-effect' ],
			callback: function( val ) { return 'full-width' == val; }
		}],

		'fl-body-bg-image': [{
			controls: [ 'fl-body-bg-repeat', 'fl-body-bg-position', 'fl-body-bg-attachment', 'fl-body-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-topbar-layout': [{
			controls: [ 'fl-topbar-line1', 'fl-topbar-col1-layout' ],
			callback: function( val ) {

				var col1Layout = api( 'fl-topbar-col1-layout' ).get(),
					col1Text   = api.control.has( 'fl-topbar-col1-text' ) ? api.control( 'fl-topbar-col1-text' ).container : null,
					col2Layout = api( 'fl-topbar-col2-layout' ).get(),
					col2Text   = api.control.has( 'fl-topbar-col2-text' ) ? api.control( 'fl-topbar-col2-text' ).container : null;

				if ( col1Text ) {
					col1Text.toggle( 'none' != val && 'text' == col1Layout );
				}
				if ( col2Text ) {
					col2Text.toggle( '2-cols' == val && 'text' == col2Layout );
				}

				return '1-col' == val || '2-cols' == val;
			}
		},{
			controls: [ 'fl-topbar-line2', 'fl-topbar-col2-layout' ],
			callback: function( val ) { return '2-cols' == val;  }
		}],

		'fl-topbar-col1-layout': [{
			controls: [ 'fl-topbar-col1-text' ],
			callback: function( val ) { return 'none' != api( 'fl-topbar-layout' ).get() && ('text' == val || 'text-social' == val); }
		}],

		'fl-topbar-col2-layout': [{
			controls: [ 'fl-topbar-col2-text' ],
			callback: function( val ) { return '2-cols' == api( 'fl-topbar-layout' ).get() && ('text' == val || 'text-social' == val); }
		}],

		'fl-topbar-bg-color': [{
			controls: [ 'fl-topbar-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-topbar-bg-image': [{
			controls: [ 'fl-topbar-bg-repeat', 'fl-topbar-bg-position', 'fl-topbar-bg-attachment', 'fl-topbar-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-theme-tagline': [{
			controls: [ 'fl-logo-tagline-color' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-header-bg-color': [{
			controls: [ 'fl-header-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-header-bg-image': [{
			controls: [ 'fl-header-bg-repeat', 'fl-header-bg-position', 'fl-header-bg-attachment', 'fl-header-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-logo-type': [{
			controls: [ 'fl-logo-text', 'fl-theme-tagline', 'fl-logo-font-family', 'fl-logo-font-weight', 'fl-logo-font-size' ],
			callback: function( val ) { return 'text' == val; }
		},{
			controls: [ 'fl-logo-image', 'fl-logo-image-retina', 'fl-sticky-header-logo', 'fl-mobile-header-logo', 'fl-logo-max-height' ],
			callback: function( val ) {
				var fixedHeader  = api( 'fl-fixed-header' ).get(),
					sitckyLogo   = api.control.has( 'fl-sticky-header-logo' ) ? api.control( 'fl-sticky-header-logo' ).container : null,
					stickyretina = api.control.has( 'fl-sticky-header-logo-retina' ) ? api.control( 'fl-sticky-header-logo-retina' ).container : null;

				if ( sitckyLogo ) {
					sitckyLogo.toggle( 'image' === val && 'fadein' == fixedHeader );
				}
				if ( stickyretina ) {
					stickyretina.toggle( 'image' === val && 'fadein' == fixedHeader );
				}

				return 'image' == val;
			}
		}],

		'fl-header-layout': [{
			controls: [ 'fl-header-padding' ],
			callback: function( val ) { return 'none' != val; }
		},{
			controls: [ 'fl-nav-bg-color', 'fl-nav-bg-gradient', 'fl-nav-bg-image', 'fl-nav-bg-repeat', 'fl-nav-bg-position', 'fl-nav-bg-attachment', 'fl-nav-bg-size', 'fl-nav-link-color', 'fl-nav-hover-color', 'fl-nav-bg-opacity' ],
			callback: function( val ) {
				return 'none' != val }
		},{
			controls: [ 'fl-fixed-header' ],
			callback: function( val ) { return 'vertical-left' != val && 'vertical-right' != val; }
		},{
			controls: [ 'fl-hide-until-scroll-header' ],
			callback: function( val ) {

			    var fixedHeader = api( 'fl-fixed-header' ).get();

			    return 'hidden' == fixedHeader && 'vertical-right' != val && 'vertical-left' != val;
			}
		},{
			controls: [ 'fl-scroll-distance' ],
			callback: function( val ) {

			    var fixedHeader   = api( 'fl-fixed-header' ).get(),
			        scrollHeader  = api( 'fl-hide-until-scroll-header' ).get();

			    return 'hidden' == fixedHeader && 'enable' == scrollHeader && 'vertical-right' != val && 'vertical-left' != val;
			}
		},{
			controls: [ 'fl-vertical-header-width', 'fl-header-logo-top-spacing', 'fl-nav-shadow-size', 'fl-nav-shadow-color', 'fl-nav-menu-top-spacing', 'fl-nav-item-align' ],
			callback: function( val ) { return 'none' != val && 'right' != val && 'bottom' != val && 'centered' != val && 'centered-inline-logo' != val && 'left' != val; }
		},{
			controls: [ 'fl-header-line1', 'fl-header-content-layout' ],
			callback: function( val ) {

				var layout = api( 'fl-header-content-layout' ).get(),
					text   = api.control.has( 'fl-header-content-text' ) ? api.control( 'fl-header-content-text' ).container : null;

				if ( text ) {
					text.toggle( 'bottom' == val && ('text' == layout || 'social-text' == layout) );
				}

				return 'bottom' == val;
			}
		},{
			controls: [ 'fl-content-width' ],
			callback: function( val ) {

				if ( 'boxed' == api( 'fl-layout-width' ).get() && ( 'vertical-right' == val || 'vertical-left' == val ) ) {
					return false;
				}

                return true;
			}
		},{
			controls: [ 'fl-layout-spacing' ],
			callback: function( val ) {

				var layoutWidth  = api( 'fl-layout-width' ).get(),
					headerLayout = api( 'fl-header-layout' ).get();

				if ( 'boxed' == layoutWidth && ( 'vertical-right' == headerLayout || 'vertical-left' == headerLayout ) ) {
					return false;
				}

                return 'boxed' == layoutWidth;
			}
		},{
			controls: [ 'fl-inline-logo-side' ],
			callback: function( val ) { return 'centered-inline-logo' == val; }
		}],

		'fl-fixed-header': [{
			controls: [ 'fl-hide-until-scroll-header', 'fl-scroll-distance' ],
			callback: function( val ) { return 'hidden' == val; }
		},{
			controls: [ 'fl-logo-max-height' ],
			callback: function( val ) {

			    var fixedHeader = api( 'fl-fixed-header' ).get();

				return ( ('fadein' == fixedHeader) || ('shrink' == fixedHeader) );
			}
		},{
			controls: [ 'fl-fixed-header-padding-top' ],
			callback: function( val ) {
				return ( ('shrink' == val) || ('fixed' == val) );
			}
		},{
			controls: [ 'fl-layout-spacing' ],
			callback: function( val ) {

				var layoutWidth  = api( 'fl-layout-width' ).get(),
					headerLayout = api( 'fl-header-layout' ).get();

				if ( 'boxed' == layoutWidth && ( 'vertical-right' == headerLayout || 'vertical-left' == headerLayout ) ) {
					return false;
				}

                return 'boxed' == layoutWidth && 'shrink' != val && 'fixed' != val;
			}
		},{
			controls: [ 'fl-sticky-header-logo', 'fl-sticky-header-logo-retina' ],
			callback: function( val ) {
				var logoType = api( 'fl-logo-type' ).get();
                return 'fadein' === val && 'image' === logoType;
			}
		}],

		'fl-fixed-header-padding-top': [{
			controls: [ 'fl-fixed-header-padding-top-custom' ],
			callback: function( val ) { return 'custom' == val; }
		}],

		'fl-hide-until-scroll-header': [{
			controls: [ 'fl-scroll-distance' ],
			callback: function( val ) { return 'enable' == val; }
		}],

		'fl-header-content-layout': [{
			controls: [ 'fl-header-content-text' ],
			callback: function( val ) {
				return 'bottom' == api( 'fl-header-layout' ).get() && ('text' == val || 'social-text' == val);
			}
		}],

		'fl-nav-mobile-layout': [{
			controls: [ 'fl-nav-mobile-layout-position' ],
			callback: function( val ) {
				return 'dropdown' != val;
			}
		}],

		'fl-nav-bg-color': [{
			controls: [ 'fl-nav-bg-gradient' ],
			callback: function( val ) {
				return 'right' != api( 'fl-header-layout' ).get() && '' != val && 'vertical-left' != api( 'fl-header-layout' ).get() && '' != val && 'vertical-right' != api( 'fl-header-layout' ).get() && '' != val;
			}
		}],

		'fl-nav-bg-image': [{
			controls: [ 'fl-nav-bg-repeat', 'fl-nav-bg-position', 'fl-nav-bg-attachment', 'fl-nav-bg-size' ],
			callback: function( val ) { return 'right' != api( 'fl-header-layout' ).get() && '' != val; }
		}],

		'fl-content-bg-image': [{
			controls: [ 'fl-content-bg-repeat', 'fl-content-bg-position', 'fl-content-bg-attachment', 'fl-content-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-blog-layout': [{
			controls: [ 'fl-blog-sidebar-size', 'fl-blog-sidebar-display', 'fl-blog-custom-sidebar-size', 'fl-blog-sidebar-location', 'fl-blog-sidebar-location-post-types' ],
			callback: function( val ) { return 'no-sidebar' != val; }
		}],

		'fl-blog-sidebar-size': [{
			controls: [ 'fl-blog-custom-sidebar-size' ],
			callback: function( val ) { return 'custom' == val; }
		}],

		'fl-woo-sidebar-size': [{
			controls: [ 'fl-woo-custom-sidebar-size' ],
			callback: function( val ) { return 'custom' == val; }
		}],

		'fl-archive-show-full': [{
			controls: [ 'fl-archive-readmore-text' ],
			callback: function( val ) { return '0' == val; }
		}],

		'fl-woo-layout': [{
			controls: [ 'fl-woo-sidebar-size', 'fl-woo-sidebar-display', 'fl-woo-custom-sidebar-size', 'fl-woo-sidebar-location' ],
			callback: function( val ) { return 'no-sidebar' != val; }
		}],

		'fl-footer-widgets-bg-color': [{
			controls: [ 'fl-footer-widgets-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-footer-widgets-bg-image': [{
			controls: [ 'fl-footer-widgets-bg-repeat', 'fl-footer-widgets-bg-position', 'fl-footer-widgets-bg-attachment', 'fl-footer-widgets-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-footer-layout': [{
			controls: [ 'fl-footer-line1', 'fl-footer-col1-layout' ],
			callback: function( val ) {

				var col1Layout = api( 'fl-footer-col1-layout' ).get(),
					col1Text   = api.control.has( 'fl-footer-col1-text' ) ? api.control( 'fl-footer-col1-text' ).container : null,
					col2Layout = api( 'fl-footer-col2-layout' ).get(),
					col2Text   = api.control.has( 'fl-footer-col2-text' ) ? api.control( 'fl-footer-col2-text' ).container : null;

				if ( col1Text )	{
					col1Text.toggle( 'none' != val && ('text' == col1Layout || 'social-text' == col1Layout) );
				}
				if ( col2Text ) {
					col2Text.toggle( '2-cols' == val && ('text' == col2Layout || 'social-text' == col2Layout) );
				}

				return '1-col' == val || '2-cols' == val;
			}
		},{
			controls: [ 'fl-footer-line2', 'fl-footer-col2-layout' ],
			callback: function( val ) { return '2-cols' == val;  }
		}],

		'fl-footer-col1-layout': [{
			controls: [ 'fl-footer-col1-text' ],
			callback: function( val ) {
				return 'none' != api( 'fl-footer-layout' ).get() && ('text' == val || 'social-text' == val);
			}
		}],

		'fl-footer-col2-layout': [{
			controls: [ 'fl-footer-col2-text' ],
			callback: function( val ) {
				return '2-cols' == api( 'fl-footer-layout' ).get() && ('text' == val || 'social-text' == val);
			}
		}],

		'fl-footer-bg-color': [{
			controls: [ 'fl-footer-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-footer-bg-image': [{
			controls: [ 'fl-footer-bg-repeat', 'fl-footer-bg-position', 'fl-footer-bg-attachment', 'fl-footer-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],

		'fl-posts-show-thumbs': [{
			controls: [ 'fl-posts-thumb-size' ],
			callback: function( val ) {
				if( 'beside' == val ) {
					return true;
				}
                return false;
			}
		}],

		'fl-button-style': [{
			controls: [
				'fl-button-color-line',
				'fl-button-color',
				'fl-button-hover-color',
				'fl-button-background-color',
				'fl-button-background-hover-color',

				'fl-button-font-line',
				'fl-button-font-family',
				'fl-button-font-weight',
				'fl-button-font-size',
				'fl-button-line-height',
				'fl-button-text-transform',

				'fl-button-border-line',
				'fl-button-border-style',

				'fl-button-border-radius',
			],
			callback: function( val ) {
				return 'custom' === val;
			}
		},{
			controls: [ 'fl-button-border-width', 'fl-button-border-color', 'fl-button-border-hover-color' ],
			callback: function( val ) {
				return ( 'custom' === val ) && ( 'none' !== api( 'fl-button-border-style' ).get() );
			}
		},{
			controls: [ 'bigcommerce_button_color', 'bigcommerce_button_text_color' ],
			callback: function( val ) {
					return '' === val;
				},
		}],

		'fl-button-border-style': [{
			controls: [ 'fl-button-border-width', 'fl-button-border-color', 'fl-button-border-hover-color' ],
			callback: function( val ) {
				return ( 'none' !== val ) && ( 'custom' === api( 'fl-button-style' ).get() );
			}
		}],

		'fl-mobile-nav-toggle': [{
			controls: [ 'fl-mobile-nav-text'],
			callback: function( val ) {
				return ( 'button' === val )
			}
		},{
			controls: [ 'fl-hamburger-icon-top-position'],
			callback: function( val ) {
				return ( 'icon' === val )
			}
		}],

		'fl-heading-style': [{
			controls: [
				'fl-title-text-color',
				'fl-title-heading-line',
				'fl-title-font-family',
				'fl-title-font-weight',
				'fl-title-font-format'
			],
			callback: function( val ) {
				return 'title' === val;
			}
		}],
		'fl-social-icons-color': [{
			controls: [
				'fl-social-icons-bg-shape',
				'fl-social-icons-bg-color',
				'fl-social-icons-fg-color',
				'fl-social-icons-hover-color',
				'fl-social-icons-size'
			],
			callback: function( val ) {
				return 'custom' === val;
			}
		}]
	};

})( jQuery );
