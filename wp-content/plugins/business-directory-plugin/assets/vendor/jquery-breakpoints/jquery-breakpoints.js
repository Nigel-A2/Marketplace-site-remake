/*
 * Breakpoints.js 0.0.11
 * https://github.com/wvega/breakpoints.js
 *
 * Copyright 2014, Joshua Stoutenburg <jehoshua02@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

var Reusables = Reusables || {};
Reusables.Breakpoints = (function ($) {

  var generateKey = (function () {
    var nextKey = 1;
    return function () {
      var key = 'breakpoint-' + nextKey;
      nextKey++;
      return key;
    };
  })();

  var Queue = function () {
    var callbacks = [];

    this.push = function (callback) {
      callbacks.push(callback);
    };

    this.process = function () {
      while (callbacks.length !== 0) {
        (callbacks.pop())();
      }
    };
  };

  var enterQueue = new Queue();

  var Breakpoint = function ($elements, range, options) {
    this.$elements = $elements;
    this.range = range;
    this.options = options;
    this.key = generateKey();

    // set elements
    this.elements = (function () {
      var isFunction = typeof $elements === 'function';
      var isString = typeof $elements === 'string';
      var isJQuery = false;
      var elements;

      if ( $elements && $elements instanceof jQuery ) {
          isJQuery = true;
      } else if ( $elements && $elements.constructor.prototype.jquery ) {
          isJQuery = true;
      }

      var hasSelector = isJQuery && !!$elements.selector;

      if (isFunction) {
        elements = $elements;
      } else if (isString) {
        elements = function () { return $($elements); };
      } else if (isJQuery && hasSelector) {
        elements = function () { return $($elements.selector); };
      } else if (isJQuery) {
        elements = function () { return $elements; };
      } else {
        // ...
      }

      return elements;
    })();

    // set range
    this.min = range[0] || 0;
    this.max = range[1] || Infinity;

    // set name
    this.name = (function (name, min, max) {
      if (name) {
        return name;
      }

      // default to breakpoint-{min}-{max}
      max = max === Infinity ? 'up' : max;
      return ['breakpoint', min, max].join('-');
    })(options.name, this.min, this.max);

    // set enter
    if (typeof options.enter === 'function') {
      this.enter = options.enter;
    } else {
      this.enter = function () {};
    }

    // set exit
    if (typeof options.exit === 'function') {
      this.exit = options.exit;
    } else {
      this.exit = function () {};
    }

    return this;
  };

  Breakpoint.prototype.evaluate = function () {
    var breakpoint = this;
    breakpoint.elements().each(function (index, element) {
      var $element = $(element);
      var width = $element.outerWidth();
      var matchNow = breakpoint.min <= width && width < breakpoint.max;
      var matchBefore = $element.data(breakpoint.key) || false;
      var change = matchNow !== matchBefore;
      if (!change) { return; }
      $element.data(breakpoint.key, matchNow);
      var entering = change && matchNow;
      var exiting = change && !matchNow;
      if (entering) {
        enterQueue.push(function () {
          $element.addClass(breakpoint.name);
          breakpoint.enter($element);
        });
      } else if (exiting) {
        $element.removeClass(breakpoint.name);
        breakpoint.exit($element);
      }
    });
  };



  /* PUBLIC */

  var Breakpoints = {};

  var Builder = function ($elements) {
    this.$elements = $elements;
  };

  Breakpoints.on = function ($elements) {
    return new Builder($elements);
  };

  /* functions that rely on private breakpoints array - want to keep that isolated */
  (function () {
    var breakpoints = [];
    Builder.prototype.define = function (range, options) {
      breakpoints.push(new Breakpoint(this.$elements, range, options));
      return this;
    };

    Breakpoints.evaluate = function () {
      var length = breakpoints.length;
      for (var i = 0; i < length; i++) {
        breakpoints[i].evaluate();
      }
      enterQueue.process();
    };

  })();

  Breakpoints.scan = function($element) {
    $element.find('[data-breakpoints]').each(function() {
        Breakpoints.register($(this))
    });

    Reusables.Breakpoints.evaluate();
  };

  Breakpoints.register = function($element) {
    var builder = Reusables.Breakpoints.on($element);
    var prefix = $element.attr('data-breakpoints-class-prefix') || 'breakpoint';
    var breakpoints = JSON.parse($element.attr('data-breakpoints'));

    if (!$.isPlainObject(breakpoints)) {
        return;
    }

    $.each(breakpoints, function(name, range) {
        builder.define(range, { name: prefix + '-' + name });
    });

    $element.removeClass( prefix + '-no-bp' );
  };

  /* bind events */
  // $(document).on('ready.reusables.breakpoints', Breakpoints.evaluate);
  $(window).on('resize.reusables.breakpoints', Breakpoints.evaluate);

  return Breakpoints;

})(jQuery);

if ( typeof jQuery !== 'undefined' ) {
    jQuery(function ($) {
        Reusables.Breakpoints.scan($('body'));
    });
}
