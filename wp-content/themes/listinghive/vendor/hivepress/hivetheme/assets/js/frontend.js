var hivetheme = {

	/**
	 * Gets component selector.
	 */
	getSelector: function(name) {
		return '[data-component="' + name + '"]';
	},

	/**
	 * Gets component object.
	 */
	getComponent: function(name) {
		return jQuery(this.getSelector(name));
	},
};

(function($) {
	'use strict';

	$(document).ready(function() {

		// Menu
		hivetheme.getComponent('menu').each(function() {
			var menu = $(this).children('ul');

			$(this).find('li').each(function() {
				var item = $(this);

				if (item.children('ul').length) {
					item.addClass('parent');

					item.hoverIntent(
						function() {
							if (item.parent('ul').parent('li').hasClass('parent')) {
								var menu = item.parent(),
									offset = menu.offset().left + menu.outerWidth() * 2;

								item.children('ul').removeClass('left').removeClass('right');

								if (offset > $(window).width()) {
									item.children('ul').addClass('left').css('left', -menu.outerWidth());
								} else {
									item.children('ul').addClass('right');
								}
							}

							item.addClass('active');
							item.children('ul').slideDown(150);
						},
						function() {
							item.children('ul').slideUp(150, function() {
								item.removeClass('active');
							});
						}
					);
				}

				item.children('a').on('click', function(e) {
					if ($(this).attr('href') === '#') {
						e.preventDefault();
					}
				});
			});

			menu.children('li').each(function() {
				if ($(this).offset().top > menu.offset().top) {
					menu.addClass('wrap');

					return false;
				}
			});
		});

		// Burger
		hivetheme.getComponent('burger').each(function() {
			var menu = $(this).children('ul');

			menu.css('top', $('#wpadminbar').height());

			$(this).children('a').on('click', function(e) {
				$('body').css('overflow-y', 'hidden');

				menu.fadeIn(150);

				e.preventDefault();
			});

			menu.on('click', function(e) {
				if (!$(e.target).is('a') && !$(e.target).is('li.parent')) {
					$('body').css('overflow-y', 'auto');

					menu.fadeOut(150);
				}
			});

			menu.find('li').each(function() {
				var item = $(this);

				if (item.children('ul').length) {
					item.addClass('parent');

					item.on('click', function(e) {
						if ($(e.target).is(item)) {
							item.toggleClass('active');
							item.children('ul').slideToggle(150);
						}
					});
				}

				item.children('a').on('click', function(e) {
					if ($(this).attr('href') === '#') {
						e.preventDefault();
					}
				});
			});
		});
	});

	$('body').imagesLoaded(function() {

		// Loader
		hivetheme.getComponent('loader').fadeOut();
	});
})(jQuery);
