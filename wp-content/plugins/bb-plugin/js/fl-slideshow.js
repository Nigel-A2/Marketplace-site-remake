
/**
 * Slideshow JS Bundle
 */
YUI.add('fl-event-move', function(Y) {

/**
 * Adds gesturemovevertical, gesturemoveverticalend, gesturemovehorizontal
 * and gesturemovehorizontalend events.
 *
 * @module fl-event-move
 */
var _eventBase = {

	_isEndEvent: false,

	on: function(node, subscriber, ce)
	{
    	if(this.type.indexOf('end') > -1) {
    		this._isEndEvent = true;
    	}

		subscriber._direction = this.type.replace('gesturemove', '').replace('end', '');

		if(window.navigator.msPointerEnabled) {
    		subscriber._startHandle = node.on('MSPointerDown', this._onStart, this, node, subscriber, ce);
        	subscriber._moveHandle = node.on('MSPointerMove', this._onMove, this, node, subscriber, ce);
        	subscriber._endHandle = node.on('MSPointerUp', this._onEnd, this, node, subscriber, ce);
		}
		else {
    	    subscriber._startHandle = node.on('gesturemovestart', this._onStart, null, this, node, subscriber, ce);
        	subscriber._moveHandle = node.on('gesturemove', this._onMove, null, this, node, subscriber, ce);
        	subscriber._endHandle = node.on('gesturemoveend', this._onEnd, { standAlone: true }, this, node, subscriber, ce);
		}
    },

    detach: function(node, subscriber, ce)
    {
        subscriber._startHandle.detach();
        subscriber._startHandle = null;
		subscriber._moveHandle.detach();
        subscriber._moveHandle = null;
		subscriber._endHandle.detach();
		subscriber._endHandle = null;
    },

    _onStart: function(e, node, subscriber, ce)
    {
    	subscriber._doMove = null;
		subscriber._startX = e.pageX;
		subscriber._startY = e.pageY;
	},

	_onMove: function(e, node, subscriber, ce)
	{
		if(this._checkDirection(e, subscriber)) {
			subscriber._doMove = true;
		}
		else {
			subscriber._doMove = false;
		}
		if(subscriber._doMove && !this._isEndEvent) {
			ce.fire(e);
		}
	},

    _onEnd: function(e, node, subscriber, ce)
    {
    	if(subscriber._doMove && this._isEndEvent) {
    		e.startPageX = subscriber._startX;
    		e.startPageY = subscriber._startY;
    		ce.fire(e);
    	}

    	subscriber._doMove = null;
	},

	_checkDirection: function(e, subscriber)
	{
		var xDelta = Math.abs(subscriber._startX - e.pageX),
			yDelta = Math.abs(subscriber._startY - e.pageY);

		if(yDelta > xDelta && subscriber._startY > e.pageY && subscriber._direction == 'vertical') {
			return true;
		}
		else if(yDelta > xDelta && subscriber._startY < e.pageY && subscriber._direction == 'vertical') {
			return true;
		}
		else if(yDelta < xDelta && subscriber._startX > e.pageX && subscriber._direction == 'horizontal') {
			return true;
		}
		else if(yDelta < xDelta && subscriber._startX < e.pageX && subscriber._direction == 'horizontal') {
			return true;
		}

		return false;
	}
};

/**
 * @event gesturemovevertical
 * @param type {String} "gesturemovevertical"
 * @param fn {Function} The method the event invokes.
 * @param ctx {Object} Context for the method the event invokes.
 */
Y.Event.define('gesturemovevertical', _eventBase);

/**
 * @event gesturemoveverticalend
 * @param type {String} "gesturemoveverticalend"
 * @param fn {Function} The method the event invokes.
 * @param ctx {Object} Context for the method the event invokes.
 */
Y.Event.define('gesturemoveverticalend', _eventBase);

/**
 * @event gesturemovehorizontal
 * @param type {String} "gesturemovehorizontal"
 * @param fn {Function} The method the event invokes.
 * @param ctx {Object} Context for the method the event invokes.
 */
Y.Event.define('gesturemovehorizontal', _eventBase);

/**
 * @event gesturemovehorizontalend
 * @param type {String} "gesturemovehorizontalend"
 * @param fn {Function} The method the event invokes.
 * @param ctx {Object} Context for the method the event invokes.
 */
Y.Event.define('gesturemovehorizontalend', _eventBase);


}, '2.0.0' ,{requires:['event-move']});


YUI.add('fl-slideshow', function(Y) {

/**
 * @module fl-slideshow
 */

/**
 * Caption widget used in slideshows.
 *
 * @namespace FL
 * @class SlideshowCaption
 * @constructor
 * @param config {Object} Configuration object
 * @extends Widget
 */
Y.namespace('FL').SlideshowCaption = Y.Base.create('fl-slideshow-caption', Y.Widget, [Y.WidgetChild], {

	/**
	 * Flag for whether the text has been
	 * toggled or not.
	 *
	 * @property _textToggled
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_textToggled: false,

	/**
	 * An anchor node used for the toggle link.
	 *
	 * @property _textToggleLink
	 * @type Object
	 * @default null
	 * @protected
	 */
	_textToggleLink: null,

	/**
	 * @method renderUI
	 * @protected
	 */
	renderUI: function()
	{
        var root = this.get('root'),
		    bb   = this.get('boundingBox');

		this._textToggleLink = Y.Node.create('<a href="javascript:void(0);"></a>');
		this._textToggleLink.addClass('fl-slideshow-caption-toggle');
		this._textToggleLink.set('innerHTML', root.get('captionMoreLinkText'));

		bb.appendChild(this._textToggleLink);
	},

	/**
	 * @method bindUI
	 * @protected
	 */
	bindUI: function()
	{
		this.get('root').on('imageLoadComplete', Y.bind(this._setText, this));
		this._textToggleLink.on('click', Y.bind(this._toggleText, this));
	},

	/**
	 * Sets the caption text and displays the
	 * toggle link if necessary.
	 *
	 * @method _setText
	 * @protected
	 */

	_setText: function()
	{
		var root 		= this.get('root'),
			text 		= root.imageInfo.caption,
			textLength	= root.get('captionTextLength'),
			cb			= this.get('contentBox');

		if(!root.imageInfo.caption || root.imageInfo.caption === '') {
			cb.set('innerHTML', '');
			this._textToggleLink.setStyle('display', 'none');
			return;
		}
		else if(textLength > -1) {
			if(!this._textToggled && textLength < text.length) {
				text = this._shortenText(text);
				this._textToggleLink.setStyle('display', 'inline-block');
			}
			else if(this._textToggled && textLength < text.length) {
				text = this._stripTags(text);
				this._textToggleLink.setStyle('display', 'inline-block');
			}
			else {
				text = this._stripTags(text);
				this._textToggleLink.setStyle('display', 'none');
			}
		}
		else {
			text = this._stripTags(text);
		}

		cb.set('innerHTML', text);
	},

	/**
	 * Shows or hides the full text when the
	 * toggle link is clicked.
	 *
	 * @method _toggleText
	 * @protected
	 */
	_toggleText: function()
	{
		var root 		= this.get('root'),
			text 		= root.imageInfo.caption,
			cb			= this.get('contentBox');

		if(this._textToggled) {
			text = this._shortenText(text);
			this._textToggleLink.set('innerHTML', root.get('captionMoreLinkText'));
			this._textToggled = false;
		}
		else {
			text = this._stripTags(text);
			this._textToggleLink.set('innerHTML', root.get('captionLessLinkText'));
			this._textToggled = true;
		}

		cb.set('innerHTML', text);
	},

	/**
	 * Strips out HTML tags from the caption text.
	 *
	 * @method _stripTags
	 * @param text {String} The text to strip HTML tags from.
	 * @param ignoreSettings {Boolean} If true, will strip tags even if
	 * the stripTags attribute is set to false.
	 * @protected
	 */
	_stripTags: function(text, ignoreSettings)
	{
        var root = this.get('root'), textDiv;

		if(ignoreSettings || root.get('captionStripTags')) {
			textDiv = document.createElement('div');
			textDiv.innerHTML = text;
			text = textDiv.textContent || textDiv.innerText;
		}

		return text;
	},

	/**
	 * Shortens the caption text to the length of
	 * the textLength attribute.
	 *
	 * @method _shortenText
	 * @protected
	 */
	_shortenText: function(text)
	{
        var root = this.get('root');

		text = this._stripTags(text, true).substring(0, root.get('captionTextLength'));

		return Y.Lang.trim(text.substring(0, text.lastIndexOf(' '))) + ' ...';
	}

}, {

	/**
	 * Custom CSS class name for the widget.
	 *
	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow-caption',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

	}
});

/**
 * A widget for loading and transitioning between SlideshowImage
 * instances. Each SlideshowImage instance is a child widget of
 * SlideshowFrame. SlideshowFrame is a child widget of the main
 * slideshow widget.
 *
 * @namespace FL
 * @class SlideshowFrame
 * @constructor
 * @param config {Object} Configuration object
 * @extends Widget
 */
Y.namespace('FL').SlideshowFrame = Y.Base.create('fl-slideshow-frame', Y.Widget, [Y.WidgetParent, Y.WidgetChild], {

	/**
	 * The imageInfo object used to load the active image.
	 *
	 * @property info
	 * @type Object
	 * @default null
	 * @protected
	 */
	_imageInfo: null,

	/**
	 * The active FL.SlideshowImage instance in the frame.
	 *
	 * @property _activeImage
	 * @type FL.SlideshowImage
	 * @default null
	 * @protected
	 */
	_activeImage: null,

	/**
	 * A FL.SlideshowImage instance used to load the
	 * next image and transition it into the frame.
	 *
	 * @property _nextImage
	 * @type FL.SlideshowImage
	 * @default null
	 * @protected
	 */
	_nextImage: null,

	/**
	 * Used to store imageInfo if a load request is
	 * made while the frame is transitioning. If not null
	 * when the transition completes, a new image will
	 * be loaded using the imageInfo.
	 *
	 * @property _loadQueue
	 * @type Object
	 * @default false
	 * @protected
	 */
	_loadQueue: null,

	/**
	 * An instance of FL.SlideshowTransition used for
	 * the current transition in progress.
	 *
	 * @property _transition
	 * @type FL.SlideshowTransition
	 * @default null
	 * @protected
	 */
	_transition: null,

	/**
	 * A flag for whether the frame is currently transitioning or not.
	 *
	 * @property _transitioning
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_transitioning: false,

	/**
	 * Flag for whether to resize when the current transition
	 * completes. Set to true when a resize request is made
	 * during a transition.
	 *
	 * @property _resizeAfterTransition
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_resizeAfterTransition: false,

	/**
	 * Provides functionality for gesture based transitions
 	 * between the active and next images.
	 *
	 * @property _gestures
	 * @type FL.SlideshowGestures
	 * @default null
	 * @protected
	 */
	_gestures: null,

	/**
	 * Creates new instances of FL.SlideshowImage used in the frame.
	 *
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		var imageConfig = this.get('imageConfig');

		this._activeImage = new Y.FL.SlideshowImage(imageConfig);
		this._nextImage = new Y.FL.SlideshowImage(imageConfig);
	},

	/**
	 * Renders the FL.SlideshowImage instances used in the frame.
	 *
	 * @method renderUI
	 * @protected
	 */
	renderUI: function()
	{
		this.add(this._activeImage);
		this.add(this._nextImage);
	},

	/**
	 * @method bindUI
	 * @protected
	 */
	bindUI: function()
	{
		var activeBB 	= this._activeImage.get('boundingBox'),
			nextBB 		= this._nextImage.get('boundingBox'),
			transition	= this.get('transition');

		if(('ontouchstart' in window || window.navigator.msPointerEnabled) && this.get('touchSupport')) {

			this._gestures = new Y.FL.SlideshowGestures({
				direction: transition == 'slideVertical' ? 'vertical' : 'horizontal',
				activeItem: activeBB,
				nextItem: nextBB
			});

			this._gestures.on('moveStart', this._gesturesMoveStart, this);
			this._gestures.on('endComplete', this._gesturesEndComplete, this);
		}
	},

	/**
	 * Functional styles for the UI.
	 *
	 * @method syncUI
	 * @protected
	 */
	syncUI: function()
	{
		var activeBB 	= this._activeImage.get('boundingBox'),
			nextBB	 	= this._nextImage.get('boundingBox'),
			cb			= this.get('contentBox');

		activeBB.setStyle('position', 'absolute');
		activeBB.setStyle('top', '0px');
		activeBB.setStyle('left', '-9999px');

		nextBB.setStyle('position', 'absolute');
		nextBB.setStyle('top', '0px');
		nextBB.setStyle('left', '-9999px');

		cb.setStyle('position', 'relative');
		cb.setStyle('overflow', 'hidden');
	},

	/**
	 * Checks whether the imageInfo should be loaded or queued.
	 * Initializes a new transition if loading is ok.
	 *
	 * @method load
	 * @param imageInfo {Object} The image info to load.
	 */
	load: function(imageInfo)
	{
		var activeInfo = this._activeImage._imageInfo;

		if(this._transitioning) {
			this._loadQueue = imageInfo;
			return;
		}
		else if(activeInfo && activeInfo.largeURL == imageInfo.largeURL) {
			return;
		}

		this._imageInfo = imageInfo;
		this._transitionInit(imageInfo);
	},

	/**
	 * Preloads the next image using the provided imageInfo.
	 *
	 * @method preload
	 * @param imageInfo {Object} The imageInfo to preload.
	 * @param width {Number} The width to preload.
	 * @param height {Number} The height to preload.
	 */
	preload: function(imageInfo, width, height)
	{
		this._imageInfo = imageInfo;
		this._nextImage.preload(imageInfo, width, height);
	},

	/**
	 * Unloads the active and next image instances.
	 *
	 * @method unload
	 */
	unload: function()
	{
		this._imageInfo = null;
		this._loadQueue = null;
		this._transitioning = false;
		this._transition = null;

		this._activeImage.detachAll();
		this._activeImage.unload();
		this._activeImage.get('boundingBox').setStyle('left', '-9999px');

		this._nextImage.detachAll();
		this._nextImage.unload();
		this._nextImage.get('boundingBox').setStyle('left', '-9999px');
	},

	/**
	 * Resizes the bounding box and active image.
	 *
	 * @method resize
	 * @param width {Number} The width value.
	 * @param height {Number} The height value.
	 */
	resize: function(width, height)
	{
		if(!width || !height) {
			return;
		}

		var bb 		= this.get('boundingBox'),
			padding = [
				parseInt(bb.getComputedStyle('paddingTop'), 10),
				parseInt(bb.getComputedStyle('paddingRight'), 10),
				parseInt(bb.getComputedStyle('paddingBottom'), 10),
				parseInt(bb.getComputedStyle('paddingLeft'), 10)
			];

		width = width - padding[1] - padding[3];
		height = height - padding[0] - padding[2];

		this.set('width', width);
		this.set('height', height);

		if(this._transitioning) {
			this._resizeAfterTransition = true;
		}
		else {
			this._activeImage.resize(width, height);
			this._nextImage.resize(width, height);
		}
	},

	/**
	 * Gets the current transition to use.
	 *
	 * @method _getTransition
	 * @protected
	 */
	_getTransition: function()
	{
		var root            = this.get('root'),
            lastIndex 		= root.albumInfo.images.length - 1,
			direction		= 'next',
			transition 		= root.get('transition');

		if(root.lastImageIndex === null) {
			direction = '';
		}
		else if(root.imageIndex == lastIndex && root.lastImageIndex === 0) {
			direction = 'prev';
		}
		else if(root.imageIndex === 0 && root.lastImageIndex == lastIndex) {
			direction = 'next';
		}
		else if(root.lastImageIndex > root.imageIndex) {
			direction = 'prev';
		}
		else if(root.lastImageIndex < root.imageIndex) {
			direction = 'next';
		}

		if(direction == 'next') {
			transition = transition.replace('slideHorizontal', 'slideLeft');
			transition = transition.replace('slideVertical', 'slideUp');
		}
		else if(direction == 'prev') {
			transition = transition.replace('slideHorizontal', 'slideRight');
			transition = transition.replace('slideVertical', 'slideDown');
		}

		return transition;
	},

	/**
	 * Fires the transitionInit event and loads the next image.
	 * The transition starts when the image's loadComplete
	 * event is fired.
	 *
	 * @method _transitionInit
	 * @param imageInfo {Object} The imageInfo to load before transitioning.
	 * @protected
	 */
	_transitionInit: function(imageInfo)
	{
		this._transitioning = true;

		// Disable gestures if set.
		if(this._gestures) {
			this._gestures.disable();
		}

		/**
		 * Fires when the next image is loading before a new transition.
		 *
		 * @event transitionInit
		 */
		this.fire('transitionInit');

		if(imageInfo) {
			this._nextImage.once('loadComplete', this._transitionStart, this);
			this._nextImage.load(imageInfo);
		}
		else {
			this._transitionStart();
		}
	},

	/**
	 * Fires the transitionStart event and starts the transition
	 * using a new instance of FL.SlideshowTransition.
	 *
	 * @method _transitionStart
	 * @protected
	 */
	_transitionStart: function()
	{
        var root = this.get('root');

		/**
		 * Fires when the next image has finished loading
		 * and a new transition starts.
		 *
		 * @event transitionStart
		 */
		this.fire('transitionStart');

		this._transition = new Y.FL.SlideshowTransition({
			itemIn: this._nextImage._imageInfo ? this._nextImage.get('boundingBox') : null,
			itemOut: this._activeImage._imageInfo ? this._activeImage.get('boundingBox') : null,
			type: this._getTransition(),
			duration: root.get('transitionDuration'),
			easing: root.get('transitionEasing'),
			kenBurnsDuration: root.get('speed')/1000,
			kenBurnsZoom: root.get('kenBurnsZoom')
		});

		if(this._nextImage._imageInfo) {
			this._nextImage.get('boundingBox').setStyle('left', '0px');
		}

		this._transition.once('complete', this._transitionComplete, this);
		this._transition.run();
	},

	/**
	 * Switches the next and active image variables, unloads the
	 * last image, fires the transitionComplete event and loads
	 * or resizes if appropriate.
	 *
	 * @method _transitionComplete
	 * @protected
	 */
	_transitionComplete: function()
	{
        var root = this.get('root');

        // Swap image container references.
		this._swapImageRefs();

		/**
		 * Fired when the current transition completes.
		 *
		 * @event transitionComplete
		 */
		this.fire('transitionComplete');
		this._transition = null;
		this._transitioning = false;

		// Enable gestures if set.
		if(this._gestures) {
            if(root && root.albumInfo.images.length <= 1) {
                this._gestures.disable();
            }
            else {
                this._gestures.enable();
            }
		}

		// Load from the queue?
		if(this._loadQueue) {
			this.load(this._loadQueue);
			this._loadQueue = null;
		}
		// Resize the active image?
		else if(this._resizeAfterTransition) {
			this._resizeAfterTransition = false;
			this._activeImage.resize(this.get('width'), this.get('height'));
			this._nextImage.resize(this.get('width'), this.get('height'));
		}
	},

	/**
	 * @method _gesturesMoveStart
	 * @param e {Object} The event object.
	 * @protected
	 */
	_gesturesMoveStart: function(e)
	{
		var index 	= 0,
			root 	= this.get('root');

		index = e.direction == 'next' ? root.imageIndex + 1 : root.imageIndex - 1;
        index = index < 0 ? root.albumInfo.images.length - 1 : index;
		index = index >= root.albumInfo.images.length ? 0 : index;

		root.pause();
		root._hideLoadingImage();
		root._showLoadingImageWithDelay();

		Y.FL.SlideshowImageLoader.removeGroup(this._nextImage.get('loadGroup'));

		this._nextImage.once('loadComplete', root._hideLoadingImage, root);
		this._nextImage.load(root.albumInfo.images[index]);
	},

	/**
	 * @method _gesturesEndComplete
	 * @protected
	 */
	_gesturesEndComplete: function()
	{
		var root	= this.get('root'),
			index	= 0;

        if(this._nextImage._imageInfo){
        	index = this._nextImage._imageInfo.index;
        	this._swapImageRefs();
			this._imageInfo = root.albumInfo.images[index];
			root.loadImage(index);
        }
	},

	/**
	 * @method _swapImageRefs
	 * @protected
	 */
	_swapImageRefs: function()
	{
		var active = this._activeImage;
		this._activeImage = this._nextImage;
		this._nextImage = active;

		if(this._nextImage._imageInfo) {
			this._nextImage.unload();
			this._nextImage.get('boundingBox').setStyle('left', '-9999px');
		}
		if(this._gestures) {
			this._gestures.set('activeItem', this._activeImage.get('boundingBox'));
			this._gestures.set('nextItem', this._nextImage.get('boundingBox'));
		}
	}

}, {

	/**
	 * Custom CSS class name for the widget.

	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow-frame',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * The configuration object used to create new instances of
		 * FL.SlideshowImage. See the API docs for {@link FL.SlideshowImage}
		 * for a complete list of configuration attributes.
		 *
		 * @attribute imageConfig
		 * @type Object
		 * @default null
		 */
		imageConfig: {
			value: null
		},

		/**
		 * Whether to use touch gestures, when available,
		 * to transition between images or not.
		 *
		 * @attribute touchSupport
		 * @type Boolean
		 * @default false
		 */
		touchSupport: {
			value: false
		}
	}
});

/**
 * A plugin for fullscreen slideshow functionality.
 *
 * @namespace FL
 * @class SlideshowFullscreen
 * @constructor
 * @param config {Object} Configuration object
 * @extends Plugin.Base
 */
Y.namespace('FL').SlideshowFullscreen = Y.Base.create('fl-slideshow-fullscreen', Y.Plugin.Base, [], {

	/**
	 * Flag for whether the slideshow is in
	 * fullscreen mode.
	 *
	 * @property active
	 * @type Boolean
	 * @default false
	 */
	active: false,

	/**
	 * A div containing the close message.
	 *
	 * @property _closeMessage
	 * @type Node
	 * @default null
	 * @protected
	 */
	_closeMessage: null,

	/**
	 * A timer for hiding the close message.
	 *
	 * @property _closeMessageTimer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_closeMessageTimer: null,

	/**
	 * The initial styles of the host's bounding box
	 * before entering fullscreen mode.
	 *
	 * @property _initialStyles
	 * @type Object
	 * @protected
	 */
	_initialStyles: {
		position: 'static',
		top: '0px',
		left: '0px'
	},

	/**
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		var host 	= this.get('host'),
			bb 		= host.get('boundingBox'),
			self 	= this;

		bb.addClass('fl-fullscreen-enabled');

		if(Y.FL.SlideshowFullscreen.OS_SUPPORT) {
			document.addEventListener('fullscreenchange', function(){ self._osChange(); }, false);
			document.addEventListener('mozfullscreenchange', function(){ self._osChange(); }, false);
			document.addEventListener('webkitfullscreenchange', function(){ self._osChange(); }, false);
		}
		else {
			this._renderCloseMessage();
		}
    },

	/**
	 * Exits fullscreen if it is currently active
	 * otherwise it enters fullscreen.
	 *
	 * @method toggle
	 */
	toggle: function()
	{
		if(this.active) {
			this.exit();
		}
		else {
			this.enter();
		}
	},

	/**
	 * Enters OS fullscreen mode if supported, otherwise
	 * the slideshow takes over the browser window.
	 *
	 * @method enter
	 */
	enter: function()
	{
		if(Y.FL.SlideshowFullscreen.OS_SUPPORT) {
			this._osEnter();
		}
		else {
			this._browserEnter();
		}
	},

	/**
	 * Exits fullscreen mode.
	 *
	 * @method exit
	 */
	exit: function()
	{
		if(Y.FL.SlideshowFullscreen.OS_SUPPORT) {
			this._osExit();
		}
		else {
			this._browserExit();
		}
	},

	/**
	 * Enters OS fullscreen mode.
	 *
	 * @method _osEnter
	 * @protected
	 */
	_osEnter: function()
	{
		var bbNode = this.get('host').get('boundingBox')._node;

		if(bbNode.webkitRequestFullScreen) {
			bbNode.webkitRequestFullScreen();
		}
		else if(bbNode.mozRequestFullScreen) {
			bbNode.mozRequestFullScreen();
		}
		else if(bbNode.requestFullScreen) {
			bbNode.requestFullScreen();
		}
	},

	/**
	 * Exits OS fullscreen mode.
	 *
	 * @method _osExit
	 * @protected
	 */
	_osExit: function()
	{
		if(document.exitFullscreen) {
            document.exitFullscreen();
        }
        else if(document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        }
        else if(document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
	},

	/**
	 * Called when the OS fullscreenchange event fires and enters
	 * or exits standard fullscreen mode which positions and
	 * resizes the slideshow.
	 *
	 * @method _osChange
	 * @protected
	 */
	_osChange: function()
	{
		var host = this.get('host');

		// Transitions break on Safari while entering and
		// exiting fullscreen. This fixes them!
		if(host.frame && host.frame._transitioning) {
			host.frame._transitionComplete();
		}

		if(this.active) {
			this._exit();
		}
		else {
			this._enter();
		}
	},

	/**
	 * Enter browser fullscreen mode.
	 *
	 * @method _browserEnter
	 * @protected
	 */
	_browserEnter: function()
	{
		var bb = this.get('host').get('boundingBox');

		this._initialStyles = {
    		position: bb.getStyle('position'),
    		top: bb.getStyle('top'),
    		left: bb.getStyle('left'),
    		zIndex: bb.getStyle('zIndex')
    	};

		bb.setStyles({
			position: 'fixed',
			top: '0px',
			left: '0px',
			zIndex: 10000
		});

		Y.Node.one('body').on('fl-fullscreen|keydown', Y.bind(this._onKey, this));

		this._showCloseMessage();
		this._enter();
	},

	/**
	 * Exit browser fullscreen mode.
	 *
	 * @method _browserExit
	 * @protected
	 */
	_browserExit: function()
	{
		var bb = this.get('host').get('boundingBox');

		bb.setStyles({
			position: this._initialStyles.position,
			top: this._initialStyles.top,
			left: this._initialStyles.left,
			zIndex: this._initialStyles.zIndex
		});

		Y.Node.one('body').detach('fl-fullscreen|keydown');

		this._hideCloseMessage();
		this._exit();
	},

	/**
	 * Enters fullscreen mode.
	 *
	 * @method _enter
	 * @protected
	 */
	_enter: function()
	{
		var host 	= this.get('host'),
			bb 		= host.get('boundingBox');

		bb.addClass('fl-fullscreen-active');

		this.active = true;

		host.resize();
	},

	/**
	 * Exits fullscreen mode.
	 *
	 * @method _exit
	 * @protected
	 */
	_exit: function()
	{
		var host 	= this.get('host'),
			bb 		= host.get('boundingBox');

		bb.removeClass('fl-fullscreen-active');

		this.active = false;

		host.resize();
	},

	/**
	 * Keyboard input for the esc button.
	 *
	 * @method _onKey
	 * @protected
	 */
	_onKey: function(e)
	{
		if(e.keyCode == 27) {
			this.exit();
			return false;
		}
	},

	/**
	 * Creates the close message if one is
	 * not already available in the document.
	 *
	 * @method _initCloseMessage
	 * @protected
	 */
	_renderCloseMessage: function()
	{
		this._closeMessage = Y.Node.create('<div class="fl-fullscreen-close-message"></div>');
		this._closeMessage.set('innerHTML', '<span>Press the "esc" button to exit fullscreen mode.</span>');
		this._closeMessage.setStyle('display', 'none');
		this.get('host').get('boundingBox').insert(this._closeMessage);
	},

	/**
	 * Shows the close message.
	 *
	 * @method _showCloseMessage
	 * @protected
	 */
	_showCloseMessage: function()
	{
		if(this._closeMessageTimer) {
			this._closeMessageTimer.cancel();
			this._closeMessageTimer = null;
		}

		this._closeMessage.show(true);
		this._closeMessageTimer = Y.later(4000, this, this._hideCloseMessage);
	},

	/**
	 * Hides the close message.
	 *
	 * @method _hideCloseMessage
	 * @protected
	 */
	_hideCloseMessage: function()
	{
		if(this._closeMessageTimer) {
			this._closeMessageTimer.cancel();
			this._closeMessageTimer = null;
		}

		this._closeMessage.hide(true);
	}

},	{

	/**
	 * Namespace for the plugin.
	 *
	 * @property NS
	 * @type String
	 * @protected
	 * @static
	 */
	NS: 'fullscreen',

	OS_SUPPORT: (function(){

		var doc = document.documentElement;

		return doc.webkitRequestFullScreen || doc.mozRequestFullScreen || doc.requestFullScreen;
	})()
});

/**
 * Provides functionality for gesture based transitions
 * between two slideshow components.
 *
 * @namespace FL
 * @class SlideshowGestures
 * @constructor
 * @param config {Object} Configuration object
 * @extends Base
 */
Y.namespace('FL').SlideshowGestures = Y.Base.create('fl-slideshow-gestures', Y.Base, [], {

	/**
	 * The x coordinate for where a gesture event starts.
	 *
	 * @property _startX
	 * @type Number
	 * @default null
	 * @protected
	 */
	_startX: null,

	/**
	 * The y coordinate for where a gesture event starts.
	 *
	 * @property _startY
	 * @type Number
	 * @default null
	 * @protected
	 */
	_startY: null,

	/**
	 * A flag for whether a gesture is moving or not.
	 *
	 * @property _moving
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_touchMoving: false,

	/**
	 * Whether the gesture is moving or not.
	 *
	 * @property _moving
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_moving: false,

	/**
	 * The direction the current gesture event
	 * is moving in (either next or prev).
	 *
	 * @property _movingDirection
	 * @type String
	 * @default null
	 * @protected
	 */
	_movingDirection: null,

	/**
	 * A flag for whether a gesture gesture is currently
	 * transitioning or not.
	 *
	 * @property _transitioning
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_transitioning: false,

	/**
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		this.enable();
	},

	/**
	 * @method enable
	 */
	enable: function()
	{
		var id 			= this.get('id'),
			direction 	= this.get('direction'),
			active 		= this.get('activeItem'),
			next 		= this.get('nextItem');

		active.on(id + '|gesturemovestart', Y.bind(this._onStart, this));
		next.on(id + '|gesturemovestart', Y.bind(this._onStart, this));
		next.on(id + '|transitionend', Y.bind(this._onEndComplete, this) );
		next.on(id + '|oTransitionEnd', Y.bind(this._onEndComplete, this) );
		next.on(id + '|webkitTransitionEnd', Y.bind(this._onEndComplete, this) );

		if(direction == 'horizontal') {
			active.on(id + '|gesturemovehorizontal', Y.bind(this._onMoveHorizontal, this));
			active.on(id + '|gesturemovehorizontalend', Y.bind(this._onEndHorizontal, this));
			next.on(id + '|gesturemovehorizontal', Y.bind(this._onMoveHorizontal, this));
			next.on(id + '|gesturemovehorizontalend', Y.bind(this._onEndHorizontal, this));
		}
		else {
			active.on(id + '|gesturemovevertical', Y.bind(this._onMoveVertical, this));
			active.on(id + '|gesturemoveverticalend', Y.bind(this._onEndVertical, this));
			next.on(id + '|gesturemovevertical', Y.bind(this._onMoveVertical, this));
			next.on(id + '|gesturemoveverticalend', Y.bind(this._onEndVertical, this));
		}
	},

	/**
	 * @method disable
	 */
	disable: function()
	{
		var id 		= this.get('id'),
			active 	= this.get('activeItem'),
			next 	= this.get('nextItem');

		active.detach(id + '|*');
		next.detach(id + '|*');
	},

	/**
	 * @method _onStart
	 * @param e {Object} The event object.
	 * @protected
	 */
	_onStart: function(e)
	{
		var direction = this.get('direction');

		if(this._transitioning) {
			this._onEndComplete();
		}

		if(direction == 'horizontal') {
			this._startX = e.pageX;
		}
		else {
			this._startY = e.pageY;
		}

		/**
		 * @event start
		 */
		this.fire('start');
	},

	/**
	 * @method _onMoveHorizontal
	 * @param e {Object} The event object.
	 * @protected
	 */
	_onMoveHorizontal: function(e)
	{
		var x 			= this._startX - e.pageX,
			active 		= this.get('activeItem'),
			next 		= this.get('nextItem'),
			width		= parseInt(active.getComputedStyle('width'), 10),
			translate 	= x < 0 ? Math.abs(x) : -x,
			direction	= x < 0 ? 'prev' : 'next';

		e.preventDefault();

		if(!this._moving || this._movingDirection != direction) {

        	active.setStyle('left', 0);

	        next.setStyles({
	        	'opacity': 1,
	        	'left': x < 0 ? -width : width
	        });

	        this._moving = true;
        	this._movingDirection = direction;

			/**
			 * @event moveStart
			 */
			this.fire('moveStart', { direction: direction });
		}

		active.setStyle('-webkit-transform', 'translate('+ translate +'px, 0px) translateZ(0px)');
		active.setStyle('-ms-transform', 'translate('+ translate +'px, 0px) translateZ(0px)');
        next.setStyle('-webkit-transform', 'translate('+ translate +'px, 0px) translateZ(0px)');
        next.setStyle('-ms-transform', 'translate('+ translate +'px, 0px) translateZ(0px)');

		/**
		 * @event move
		 */
		this.fire('move');
	},

	/**
	 * @method _onMoveVertical
	 * @param e {Object} The event object.
	 * @protected
	 */
	_onMoveVertical: function(e)
	{
		var y 			= this._startY - e.pageY,
			active 		= this.get('activeItem'),
			next 		= this.get('nextItem'),
			height		= parseInt(active.getComputedStyle('height'), 10),
			translate 	= y < 0 ? Math.abs(y) : -y,
			direction	= y < 0 ? 'prev' : 'next';

		e.preventDefault();

		if(!this._moving || this._movingDirection != direction) {

        	active.setStyle('top', 0);

	        next.setStyles({
	        	'opacity': 1,
	        	'left' : 'auto',
	        	'top': y < 0 ? -height : height
	        });

	        this._moving = true;
        	this._movingDirection = direction;

			/**
			 * @event moveStart
			 */
			this.fire('moveStart', { direction: direction });
		}

		active.setStyle('-webkit-transform', 'translate(0px, '+ translate +'px) translateZ(0px)');
		active.setStyle('-ms-transform', 'translate(0px, '+ translate +'px) translateZ(0px)');
        next.setStyle('-webkit-transform', 'translate(0px, '+ translate +'px) translateZ(0px)');
        next.setStyle('-ms-transform', 'translate(0px, '+ translate +'px) translateZ(0px)');

		/**
		 * @event move
		 */
		this.fire('move');
	},

	/**
	 * @method _onEndHorizontal
	 * @param e {Object} The event object.
	 * @protected
	 */
	_onEndHorizontal: function(e)
	{
		if(!this._moving) {
			return;
		}

		var x 			= this._startX - e.pageX,
			active 		= this.get('activeItem'),
			next 		= this.get('nextItem'),
			width		= parseInt(next.getComputedStyle('width'), 10),
			translate 	= x < 0 ? width : -width;

		active.transition({
			'transform': 'translate('+ translate +'px, 0px)'
		});

		next.transition({
			'transform': 'translate('+ translate +'px, 0px)'
		});

		this._transitioning = true;

		/**
		 * @event end
		 */
		this.fire('end');
	},

	/**
	 * @method _onEndVertical
	 * @param e {Object} The event object.
	 * @protected
	 */
	_onEndVertical: function(e)
	{
		if(!this._moving) {
			return;
		}

		var y 			= this._startY - e.pageY,
			active 		= this.get('activeItem'),
			next 		= this.get('nextItem'),
			height		= parseInt(next.getComputedStyle('height'), 10),
			translate 	= y < 0 ? height : -height;

		active.transition({
			'transform': 'translate(0px, '+ translate +'px)'
		});

		next.transition({
			'transform': 'translate(0px, '+ translate +'px)'
		});

		this._transitioning = true;

		/**
		 * @event end
		 */
		this.fire('end');
	},

	/**
	 * @method _onEndComplete
	 * @protected
	 */
	_onEndComplete: function()
	{
		var direction 	= this.get('direction'),
			active 		= this.get('activeItem'),
			next 		= this.get('nextItem');

		active.setStyles({
        	'opacity': 0,
        	'-webkit-transform': '',
        	'-webkit-transition': '',
        	'-ms-transform': '',
        	'-ms-transition': ''
        });

		next.setStyles({
        	'-webkit-transform': '',
        	'-webkit-transition': '',
        	'-ms-transform': '',
        	'-ms-transition': ''
        });

        if(direction == 'horizontal') {
        	active.setStyle('left', '-9999px');
        	next.setStyle('left', '0px');
        }
        else {
        	active.setStyle('top', '-9999px');
        	next.setStyle('top', '0px');
        }

		this.set('activeItem', next);
		this.set('nextItem', active);
		this._moving = false;
		this._movingDirection = null;
		this._transitioning = false;

		/**
		 * @event endComplete
		 */
		this.fire('endComplete');
	}

}, {

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * The gesture direction to use. Possible values are
		 * horizontal and vertical.
		 *
		 * @attribute direction
		 * @type String
		 * @default horizontal
		 */
		direction: {
			value: 'horizontal'
		},

		/**
		 * The Node that is currently visible.
		 *
		 * @attribute activeItem
		 * @type Node
		 * @default null
		 */
		activeItem: {
			value: null
		},

		/**
		 * The Node that will be transitioned in.
		 *
		 * @attribute nextItem
		 * @type Node
		 * @default null
		 */
		nextItem: {
			value: null
		}
	}
});

/**
 * A load queue for slideshow images.
 *
 * @namespace FL
 * @class SlideshowImageLoader
 * @static
 */
Y.namespace('FL').SlideshowImageLoader = {

	/**
	 * Whether an image is being loaded or not.
	 *
	 * @property _loading
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_loading: false,

	/**
	 * An node for loading the next image.
	 *
	 * @property _currentImage
	 * @type Node
	 * @default null
	 * @protected
	 */
	_currentImage: null,

	/**
	 * An object containing the group, src and callback
	 * for the current image that is being loaded.
	 *
	 * @property _currentImageData
	 * @type Object
	 * @default null
	 * @protected
	 */
	_currentImageData: null,

	/**
	 * An array of image data objects that contain the group,
	 * src and callback for each image that will be loaded.
	 *
	 * @property _queue
	 * @type Array
	 * @default []
	 * @protected
	 */
	_queue: [],

	/**
	 * Adds an image to the queue.
	 *
	 * @method add
	 * @param group {String} The group this image is associated with.
	 * Used to remove images in bulk.
	 * @param src {String} The image url to load.
	 * @param callback {Function} A function to call when the image
	 * has finished loading.
	 * @param bump {Boolean} If true, the image will be added to
	 * the first position in the queue.
	 */
	add: function(group, src, callback, bump)
	{
		var imgData = {
			group		: group,
			src			: src,
			callback	: callback
		};

		if(bump) {
			this._queue.unshift(imgData);
		}
		else {
			this._queue.push(imgData);
		}

		if(!this._loading) {
			this._load();
		}
	},

	/**
	 * Removes a group of images from the queue.
	 *
	 * @method removeGroup
	 * @param group {String} The group to remove.
	 */
	removeGroup: function(group)
	{
		var i = this._queue.length - 1;

		for( ; i > -1 ; i--) {
			if(this._queue[i].group == group) {
				this._queue.splice(i, 1);
			}
		}

		if(this._currentImageData && this._currentImageData.group == group) {
			this._currentImage.detachAll();
			this._currentImage = null;
			this._currentImageData = null;

			if(this._queue.length > 0) {
				this._load();
			}
			else {
				this._loading = false;
			}
		}
	},

	/**
	 * Loads the next image in the queue.
	 *
	 * @method _load
	 * @protected
	 */
	_load: function()
	{
		this._loading = true;
		this._currentImageData = this._queue.shift();
		this._currentImage = Y.Node.create('<img />');
		this._currentImage.on('error', Y.bind(this._loadComplete, this));
		this._currentImage.on('load', Y.bind(this._loadComplete, this));
		this._currentImage.set('src', this._currentImageData.src);
	},

	/**
	 * Calls the current image's callback function if set
	 * and loads the next image if the queue is not empty.
	 *
	 * @method _loadComplete
	 * @protected
	 */
	_loadComplete: function()
	{
		if(this._currentImageData.callback) {
			this._currentImageData.callback(this._currentImage);
		}

		if(this._queue.length > 0) {
			this._load();
		}
		else {
			this._loading = false;
			this._currentImage = null;
			this._currentImageData = null;
		}
	}
};

/**
 * Loads an image or video using the provided imageInfo object.
 *
 * @namespace FL
 * @class SlideshowImage
 * @constructor
 * @param config {Object} Configuration object
 * @extends Widget
 */
Y.namespace('FL').SlideshowImage = Y.Base.create('fl-slideshow-image', Y.Widget, [Y.WidgetChild], {

	/**
	 * The imageInfo object used to load the image and
	 * its various sizes.
	 *
	 * @property info
	 * @type Object
	 * @default null
	 * @protected
	 */
	_imageInfo: null,

	/**
	 * A reference to the current image node in the bounding box.
	 *
	 * @property _image
	 * @type Node
	 * @default null
	 * @protected
	 */
	_image: null,

	/**
	 * Whether or not new imageInfo is loading.
	 *
	 * @property _loading
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_loading: false,

	/**
	 * The URL that is currently being loaded.
	 *
	 * @property _loadingURL
	 * @type Boolean
	 * @default null
	 * @protected
	 */
	_loadingURL: null,

	/**
	 * An anchor node used for the video play button.
	 *
	 * @property _videoButton
	 * @type Node
	 * @default null
	 * @protected
	 */
	_videoButton: null,

	/**
	 * A div node used to hold the video iframe.
	 *
	 * @property _videoBox
	 * @type Node
	 * @default null
	 * @protected
	 */
	_videoBox: null,

	/**
	 * An iframe node used to render the video.
	 *
	 * @property _video
	 * @type Node
	 * @default null
	 * @protected
	 */
	_video: null,

	/**
	 * The default content template for the image
	 * inherited from Y.Widget. Set to null since
	 * only the bounding box is needed.
	 *
	 * @property CONTENT_TEMPLATE
	 * @type String
	 * @default null
	 * @protected
	 */
	CONTENT_TEMPLATE: null,

	/**
	 * Initial styling for the bounding box.
	 *
	 * @method syncUI
	 * @protected
	 */
	syncUI: function()
	{
		var bb = this.get('boundingBox');

		if(this.get('crop')) {
			bb.setStyle('overflow', 'hidden');
			bb.addClass('fl-slideshow-image-cropped');
		}
	},

	/**
	 * Sets the imageInfo object and
	 * loads the appropriate image size.
	 *
	 * @method load
	 * @param imageInfo {Object} The imageInfo object.
	 */
	load: function(imageInfo)
	{
		this._imageInfo = imageInfo;
		this._loading = true;
		this._load();
	},

	/**
	 * Sets the width and height of the bounding box and
	 * preloads an image using the provided imageInfo object.
	 *
	 * @method preload
	 * @param imageInfo {Object} The imageInfo to preload.
	 * @param width {Number} The width to preload.
	 * @param height {Number} The height to preload.
	 */
	preload: function(imageInfo, width, height)
	{
		var isVideo			= this._isVideo(),
			loadVideos 		= this.get('loadVideos'),
			showVideoButton = this.get('showVideoButton');

		this.unload();
		this.set('width', width);
		this.set('height', height);
		this._imageInfo = imageInfo;

		if(!isVideo || !loadVideos || (isVideo && loadVideos && showVideoButton)) {
			Y.FL.SlideshowImageLoader.add(
				this.get('loadGroup'),
				this._getImageURL(),
				Y.bind(this._imagePreloaded, this),
				this.get('loadPriority')
			);
		}
	},

	/**
	 * Called when preloading completes.
	 *
	 * @method _imagePreloaded
	 * @param img {Object} The image that was preloaded.
	 * @protected
	 */
	_imagePreloaded: function(img)
	{
		this._image = img;
	},

	/**
	 * Unloads the image if there is one loaded
	 * and sets the imageInfo object to null.
	 *
	 * @method unload
	 */
	unload: function()
	{
		if(this._image) {
			this._image.remove();
			this._image.detachAll();
			this._image.set('src', '');
			this._image = null;
		}
		if(this._video) {
			this._video.remove();
			this._video = null;
		}
		if(this._videoButton) {
			this._videoButton.remove();
			this._videoButton = null;
		}
		if(this._videoBox) {
			this._removeVideoBox();
		}

		this._imageInfo = null;
		this._loading = false;
		this._loadingURL = null;
	},

	/**
	 * Resizes the bounding box and loads the
	 * appropriate image size if necessary.
	 *
	 * @method resize
	 * @param width {Number} The width value.
	 * @param height {Number} The height value.
	 */
	resize: function(width, height)
	{
		var borderWidth = parseInt(this.get('boundingBox').getComputedStyle('borderTopWidth'), 10) * 2,
			bb			= this.get('boundingBox');

		this.set('width', width - borderWidth);
		this.set('height', height - borderWidth);
		bb.setStyle('width', width - borderWidth + 'px');
		bb.setStyle('height', height - borderWidth + 'px');

		if(this._videoButton) {
			this._positionVideoButton();
		}
		if(this._videoBox) {
			this._loadVideo();
		}
		if(!this._loading) {
			if(this._imageInfo) {
				this._load();
			}
			if(this._image) {
				this._positionImage();
			}
		}
	},

	/**
	 * Loads (or reloads) the image or video.
	 *
	 * @method _load
	 * @protected
	 */
	_load: function()
	{
		var loadVideos 		= this.get('loadVideos'),
			showVideoButton = this.get('showVideoButton');

		if(this._isVideo() && loadVideos && !showVideoButton && !('ontouchstart' in window)) {
			this._loadVideo();
		}
		else {
			this._loadImage();
		}
	},

	/**
	 * Loads the appropriate image size if
	 * it is not already loading.
	 *
	 * @method _loadImage
	 * @protected
	 */
	_loadImage: function()
	{
		var url 		= this._getImageURL(),
			loadVideos 	= this.get('loadVideos');

		// Already loading.
		if(url == this._loadingURL) {
			return;
		}

		// New URL to load.
		this._loadingURL = url;

		// Load the new image.
		Y.FL.SlideshowImageLoader.add(
			this.get('loadGroup'),
			this._loadingURL,
			Y.bind(this._loadImageComplete, this),
			this.get('loadPriority')
		);

		// Initial load?
		if(this._loading) {

			if(this._isVideo() && loadVideos) {
				this._insertVideoButton();
			}

			/**
			 * Only fires when a new image is being
			 * loaded, not a different size.
			 *
			 * @event loadStart
			 */
			this.fire('loadStart');
		}
	},

	/**
	 * Fires when the image has finished loading.
	 *
	 * @method _loadImageComplete
	 * @protected
	 */
	_loadImageComplete: function(img)
	{
		var bb            = this.get('boundingBox'),
			showVideoButton = this.get('showVideoButton'),
			showAria        = this.get('root').get('bgslideshow')

		this._image = img;
		this._image.setStyle('visibility', 'hidden');
		this._image.addClass('fl-slideshow-image-img');

		if( showAria ) {
			this._image.set( 'aria-hidden', 'true')
			this._image.set( 'alt', "")
		}
		else {
			this._image.set( 'alt', this._imageInfo.alt )
		}

		// Remove load events.
		this._image.detachAll();

		// Remove previous videos.
		if(this._video && !showVideoButton) {
			this._video.remove();
			this._video = null;
		}

		// Remove the old image.
		bb.all('img').remove();

		// Append the new image.
		bb.append(this._image);

		// Setup, scale and position the new image.
		this._setupImage();
		this._resizeImage();
		this._positionImage();
		this._image.setStyle('visibility', 'visible');

		// Clear the loading url.
		this._loadingURL = null;

		// Finish an initial load?
		if(this._loading) {

			this._loading = false;

			/**
			 * Only fires when a new image is being
			 * loaded, not a different size.
			 *
			 * @event loadComplete
			 */
			this.fire('loadComplete');
		}
	},

	/**
	 * UI setup for the new image.
	 *
	 * @method _setupImage
	 * @protected
	 */
	_setupImage: function()
	{
        var bb = this.get('boundingBox');

		// IE interpolation
		if(typeof this._image._node.style.msInterpolationMode != 'undefined') {
			this._image._node.style.msInterpolationMode = 'bicubic';
		}

		// Protection
		if(this.get('protect')) {
            bb.delegate('contextmenu', this._protectImage, 'img');
            bb.delegate('mousedown', this._protectImage, 'img');
		}
	},

	/**
	 * Fires on contextmenu or mousedown in attempt
	 * to keep the image from being copied.
	 *
	 * @method _protectImage
	 * @return {Boolean} Returns false to prevent the default event.
	 * @protected
	 */
	_protectImage: function(e)
	{
		e.preventDefault();
		return false;
	},

	/**
	 * Resizes the image node.
	 *
	 * @method _resizeImage
	 * @protected
	 */
	_resizeImage: function()
	{
		var borderWidth 		= parseInt(this._image.getComputedStyle('borderTopWidth'), 10) * 2,
			imageWidth			= this._image.get('width'),
			imageHeight			= this._image.get('height'),
			targetWidth			= parseInt(this.get('boundingBox').getComputedStyle('width'), 10),
			targetHeight		= parseInt(this.get('boundingBox').getComputedStyle('height'), 10),
			newWidth 			= 0,
			newHeight  			= 0,
			xScale				= 0,
			yScale				= 0,
			cropHorizontalsOnly = this.get('cropHorizontalsOnly'),
			isHorizontal		= imageHeight > imageWidth,
			noCrop				= false;

		if(this._imageInfo && this.get('checkFilenamesForNoCrop')) {
			noCrop = this._imageInfo.filename.indexOf('nocrop') > -1;
		}

		if(this.get('crop') && !(cropHorizontalsOnly && isHorizontal) && !noCrop) {
			newWidth = targetWidth;
			newHeight = Math.round(imageHeight * targetWidth/imageWidth);

			if(newHeight < targetHeight) {
				newHeight = targetHeight;
				newWidth = Math.round(imageWidth * targetHeight/imageHeight);
			}
		}
		else {
			xScale = imageWidth/targetWidth;
			yScale = imageHeight/targetHeight;

			if (yScale > xScale){
				newWidth = Math.round(imageWidth * (1/yScale));
				newHeight = Math.round(imageHeight * (1/yScale));
			}
			else {
				newWidth = Math.round(imageWidth * (1/xScale));
				newHeight = Math.round(imageHeight * (1/xScale));
			}
		}

		// Don't resize past the original size?
		if(!this.get('crop') && !this.get('upsize') && (newWidth > imageWidth || newHeight > imageHeight)) {
			newWidth = imageWidth;
			newHeight = imageHeight;
		}

		// Compensate for borders.
		newWidth -= borderWidth;
		newHeight -= borderWidth;

		// Resize the image.
		this._image.setStyle('width', newWidth + 'px');
		this._image.setStyle('height', newHeight + 'px');

		// Constrain bounding box to image size.
		if(!this.get('crop') && this.get('constrainWidth')) {
			this.set('width', newWidth + 'px');
		}
		if(!this.get('crop') && this.get('constrainHeight')) {
			this.set('height', newHeight + 'px');
		}
	},

	/**
	 * Positions the image within the bounding box.
	 *
	 * @method _positionImage
	 * @protected
	 */
	_positionImage: function()
	{
		var pos 			= this.get('position').split(' '),
			x 				= pos[0] === '' ? 'center' : pos[0],
			y 				= pos[1] === '' ? 'center' : pos[1],
			newX 			= 0,
			newY			= 0,
			bbWidth			= parseInt(this.get('boundingBox').getComputedStyle('width'), 10),
			bbHeight		= parseInt(this.get('boundingBox').getComputedStyle('height'), 10),
			borderWidth 	= parseInt(this._image.getComputedStyle('borderTopWidth'), 10) * 2,
			imageWidth		= parseInt(this._image.getComputedStyle('width'), 10) + borderWidth,
			imageHeight		= parseInt(this._image.getComputedStyle('height'), 10) + borderWidth;

		if(isNaN(imageWidth) && isNaN(imageHeight)) {
			return;
		}
		if(x == 'left') {
			newX = 0;
		}
		if(x == 'center') {
			newX = 	(bbWidth - imageWidth)/2;
		}
		if(x == 'right') {
			newX = bbWidth - imageWidth;
		}

		if(y == 'top') {
			newY = 0;
		}
		if(y == 'center') {
			newY = (bbHeight - imageHeight)/2;
		}
		if(y == 'bottom') {
			newY = bbHeight - imageHeight;
		}

		this._image.setStyles({
			'left': newX,
			'top': newY
		});
	},

	/**
	 * Gets the appropriate image url based
	 * on the size of the bounding box.
	 *
	 * @method _getImageURL
	 * @return {String} The url to load.
	 * @protected
	 */
	_getImageURL: function()
	{
		var imageWidth 		= 0,
			imageHeight 	= 0,
			size 			= 0,
			targetWidth		= this.get('width'),
			targetHeight	= this.get('height'),
			useThumbSizes	= this.get('useThumbSizes'),
			i 				= this._imageInfo,
			sizes = [
				i.tinyURL 		|| i.thumbURL 	|| i.largeURL,
				i.thumbURL 		|| i.largeURL,
				i.smallURL 		|| i.largeURL,
				i.mediumURL 	|| i.largeURL 	|| i.smallURL,
				i.largeURL 		|| i.mediumURL 	|| i.smallURL,
				i.xlargeURL 	|| i.largeURL 	|| i.mediumURL 	|| i.smallURL,
				i.x2largeURL 	|| i.largeURL 	|| i.mediumURL 	|| i.smallURL,
				i.x3largeURL 	|| i.x2largeURL || i.largeURL 	|| i.mediumURL || i.smallURL
			];

		// Width
		if(useThumbSizes && targetWidth <= 100) {
			imageWidth = 0;
		}
		else if(useThumbSizes && targetWidth <= 150) {
			imageWidth = 1;
		}
		else if(targetWidth <= 400) {
			imageWidth = 2;
		}
		else if(targetWidth >= 400 && targetWidth <= 600) {
			imageWidth = 3;
		}
		else if(targetWidth >= 600 && targetWidth <= 800) {
			imageWidth = 4;
		}
		else if(targetWidth >= 800 && targetWidth <= 1024) {
			imageWidth = 5;
		}
		else if(targetWidth >= 1024 && targetWidth <= 1280) {
			imageWidth = 6;
		}
		else {
			imageWidth = 7;
		}

		// Height
		if(useThumbSizes && targetHeight <= 100) {
			imageHeight = 0;
		}
		else if(useThumbSizes && targetHeight <= 150) {
			imageHeight = 1;
		}
		else if(targetHeight <= 300) {
			imageHeight = 2;
		}
		else if(targetHeight >= 300 && targetHeight <= 450) {
			imageHeight = 3;
		}
		else if(targetHeight >= 450 && targetHeight <= 600) {
			imageHeight = 4;
		}
		else if(targetHeight >= 600 && targetHeight <= 768) {
			imageHeight = 5;
		}
		else if(targetHeight >= 768 && targetHeight <= 960) {
			imageHeight = 6;
		}
		else {
			imageHeight = 7;
		}

		// Get the size number.
		size = Math.max(imageWidth, imageHeight);

		return sizes[size];
	},

	/**
	 * Checks whether this is a video or not.
	 *
	 * @method _isVideo
	 * @protected
	 */
	_isVideo: function()
	{
		if(!this._imageInfo) {
			return false;
		}
		else if(this._imageInfo.format == 'mp4' && this._imageInfo.sourceType == 'smugmug') {
			return true;
		}
		else if(this._imageInfo.iframe !== '') {
			return true;
		}

		return false;
	},

	/**
	 * @method _loadVideo
	 * @protected
	 */
	_loadVideo: function()
	{
		var bb 				= this.get('boundingBox'),
			showVideoButton = this.get('showVideoButton'),
			autoPlay 		= showVideoButton ? true : false;

		// Remove previous videos
		if(this._video) {
			this._video.remove();
			this._video = null;
		}

		// Get the video code
		if(this._imageInfo.format == 'mp4' && this._imageInfo.sourceType == 'smugmug') {
			this._video = this._getSmugMugVideoEmbed(this._imageInfo, autoPlay);
		}
		else if(this._imageInfo.iframe !== '') {
			this._video = this._getIframeVideoEmbed(this._imageInfo, autoPlay);
		}

		// Insert the video
		if(this._videoBox) {
			this._videoBox.one('.fl-slideshow-video-wrap').insert(this._video);
		}
		else {
			bb.all('img').remove();
			bb.append(this._video);
		}

		// Finish an initial load?
		if(this._loading) {
			this._loading = false;
			this.fire('loadComplete');
		}
	},

	/**
	 * @method _insertVideoButton
	 * @protected
	 */
	_insertVideoButton: function()
	{
		var bb 		= this.get('boundingBox'),
			event 	= 'ontouchstart' in window ? 'touchstart' : 'click';

		this._videoButton = Y.Node.create('<a class="fl-slideshow-video-button" href="javascript:void(0);"></a>');
		this._videoButton.on(event, Y.bind(this._showVideoBox, this));
		bb.insert(this._videoButton);
		this._positionVideoButton();
	},

	/**
	 * @method _positionVideoButton
	 * @protected
	 */
	_positionVideoButton: function()
	{
		var bbWidth			= this.get('width'),
			bbHeight		= this.get('height'),
			buttonWidth		= parseInt(this._videoButton.getStyle('width'), 10),
			buttonHeight 	= parseInt(this._videoButton.getStyle('height'), 10);

		this._videoButton.setStyles({
			left: (bbWidth - buttonWidth)/2,
			top: (bbHeight - buttonHeight)/2
		});
	},

	/**
	 * @method _showVideoBox
	 * @protected
	 */
	_showVideoBox: function()
	{
		var root 	= this.get('root'),
			wrap 	= Y.Node.create('<div class="fl-slideshow-video-wrap"></div>'),
			close 	= Y.Node.create('<a class="fl-slideshow-video-close" href="javascript:void(0);"></a>'),
			event 	= 'ontouchstart' in window ? 'touchstart' : 'click';

		this._videoBox = Y.Node.create('<div class="fl-slideshow-video"></div>');
		this._videoBox.setStyle('padding', root.get('boundingBox').getStyle('padding'));
		this._videoBox.insert(wrap);
		this._videoBox.insert(close);
		this._videoBox.on(event, Y.bind(this._removeVideoBox, this));
		close.on(event, Y.bind(this._removeVideoBox, this));

		if(typeof YUI.Env.mods['sm-fonticon'] !== 'undefined') {
            close.addClass('sm-fonticon sm-fonticon-XCrossEncircled sm-button-skin-default sm-button-nochrome');
        }

		Y.one('body').insert(this._videoBox);
		this._loadVideo();

		Y.one('body').on('fl-slideshow-image|keydown', this._onKey, this);
	},

	/**
	 * Get the embed code for a SmugMug video.
	 *
	 * @method _getSmugMugVideoEmbed
	 * @param imageInfo {Object} The image info for the embed.
	 * @param autoPlay {Boolean} Whether to auto play videos or not.
	 * @protected
	 */
	_getSmugMugVideoEmbed: function(imageInfo, autoPlay)
	{
		var test		= document.createElement('video'),
			width		= 0,
			mp4 		= '',
			vars		= '',
			code 		= '';

		if(Y.UA.mobile !== null && !!test.canPlayType && test.canPlayType('video/mp4')) {
			width = this.get('width');
			mp4 = 'https://www.smugmug.com/photos/' + imageInfo.id + '_' + imageInfo.key + '-' + width + '.mp4';
			code += '<video width="100%" height="100%" poster="'+ this._getImageURL() +'" controls preload="none"';

			if(autoPlay) {
				code += ' autoplay';
			}

			code += '>';
			code += '<source src="'+ mp4 +'" type="video/mp4" />';
			code += '</video>';
		}
		else {
			vars = 'imageId=' + imageInfo.id;
			vars += '&amp;imageKey=' + imageInfo.key;
			vars += '&amp;albumId=' + imageInfo.albumId;
			vars += '&amp;albumKey=' + imageInfo.albumKey;
			vars += '&amp;apiURL=https://api.smugmug.com/&amp;hostLevel=live&amp;isPro=true';

			if(autoPlay) {
				vars += '&amp;autoPlay=true';
			}
			else {
				vars += '&amp;autoPlay=false';
			}

			code += '<object type="application/x-shockwave-flash" width="100%" height="100%" data="https://cdn.smugmug.com/img/ria/SmugPlayer/2012102601.swf">';
			code += '<param name="movie" value="https://cdn.smugmug.com/img/ria/SmugPlayer/2012102601.swf">';
			code += '<param name="allowFullScreen" value="true">';
			code += '<param name="wmode" value="transparent">';
			code += '<param name="flashVars" value="' + vars + '">';
			code += '<embed src="https://cdn.smugmug.com/img/ria/SmugPlayer/2012102601.swf" flashvars="'+ vars +'" width="100%" height="100%" type="application/x-shockwave-flash" allowfullscreen="true" wmode="transparent">';
			code += '</object>';
		}

		return Y.Node.create(code);
	},

	/**
	 * Get the iframe video embed code.
	 *
	 * @method _getIframeVideoEmbed
	 * @param imageInfo {Object} The image info for the embed.
	 * @param autoPlay {Boolean} Whether to auto play videos or not.
	 * @protected
	 */
	_getIframeVideoEmbed: function(imageInfo, autoPlay)
	{
		var code 	= '<iframe width="100%" height="100%" allowfullscreen ',
			url	 	= imageInfo.iframe;

		if(autoPlay) {
			url += url.indexOf('?') > -1 ? '&autoplay=1' : '?autoplay=1';
		}

		code += 'src="'+ url +'"></iframe>';

		return Y.Node.create(code);
	},

	/**
	 * @method _removeVideoBox
	 * @protected
	 */
	_removeVideoBox: function(e)
	{
		if(typeof e !== 'undefined' && e.target) {
			if(e.target.get('className').indexOf('fl-slideshow-video') < 0) {
				return;
			}
		}

		if(this._videoBox !== null) {
    		this._videoBox.remove();
    		this._videoBox = null;
    		this._video = null;
        }

		Y.one('body').detach('fl-slideshow-image|keydown', this._onKey);
	},

	/**
	 * Keyboard input for the esc button.
	 *
	 * @method _onKey
	 * @protected
	 */
	_onKey: function(e)
	{
		if(e.keyCode == 27) {
			this._removeVideoBox();
			return false;
		}
	}

}, {

	/**
	 * Custom CSS class name for the widget.

	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow-image',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * @attribute loadGroup
		 * @type String
		 * @default none
		 */
		loadGroup: {
			value: 'none'
		},

		/**
		 * @attribute loadPriority
		 * @type Boolean
		 * @default false
		 */
		loadPriority: {
			value: false
		},

		/**
		 * Whether to crop the image.
		 *
		 * @attribute crop
		 * @type Boolean
		 * @default false
		 */
		crop: {
			value: false
		},

		/**
		 * Checks whether the filename has nocrop in it or not.
		 * If it does, the image will not be cropped.
		 *
		 * @attribute checkFilenamesForNoCrop
		 * @type Boolean
		 * @default true
		 */
		checkFilenamesForNoCrop: {
			value: true
		},

		/**
		 * Whether to only crop horizontal images or not.
		 *
		 * @attribute cropHorizontalsOnly
		 * @type Boolean
		 * @default false
		 */
		cropHorizontalsOnly: {
			value: false
		},

		/**
		 * The x and y position of the image
		 * within the bounding box.
		 *
		 * @attribute position
		 * @type String
		 * @default center center
		 */
		position: {
			value: 'center center'
		},

		/**
		 * Whether to right click protect the image.
		 *
		 * @attribute protect
		 * @type Boolean
		 * @default true
		 */
		protect: {
			value: true
		},

		/**
		 * Whether to resize the image past
		 * its original width and height.
		 *
		 * @attribute upsize
		 * @type Boolean
		 * @default true
		 */
        upsize: {
			value: true
		},

		/**
		 * Whether to load thumb sizes. Defaults
		 * to false since thumb sizes are square.
		 *
		 * @attribute useThumbSizes
		 * @type Boolean
		 * @default false
		 */
		useThumbSizes: {
			value: false
		},

		/**
		 * Whether to constrain the width of the
		 * bounding box to the width of the image.
		 *
		 * @attribute constrainWidth
		 * @type Boolean
		 * @default false
		 */
		constrainWidth: {
			value: false
		},

		/**
		 * Whether to constrain the height of the
		 * bounding box to the height of the image.
		 *
		 * @attribute constrainHeight
		 * @type Boolean
		 * @default false
		 */
		constrainHeight: {
			value: false
		},

		/**
		 * Whether to load videos or not. The poster
		 * image will be loaded if set to false.
		 *
		 * @attribute loadVideos
		 * @type Boolean
		 * @default true
		 */
		loadVideos: {
			value: true
		},

		/**
		 * Whether to show the video play button or not.
		 * When clicked, videos will be displayed in a
		 * lightbox instead of the slideshow itself.
		 *
		 * @attribute showVideoButton
		 * @type Boolean
		 * @default true
		 */
		showVideoButton: {
			value: true
		}
	}
});

/**
 * A plugin that turns the cursor into a prev or next arrow when
 * it is over the left or right side of the slideshow.
 *
 * @namespace FL
 * @class SlideshowMouseNav
 * @constructor
 * @param config {Object} Configuration object
 * @extends Plugin.Base
 */
Y.namespace('FL').SlideshowMouseNav = Y.Base.create('fl-slideshow-mouse-nav', Y.Plugin.Base, [], {

	/**
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		var trigger = this.get('trigger');

		trigger.on('click', this._triggerClick, this);
		trigger.on('mousemove', this._showArrow, this);
		trigger.on('mouseleave', this._hideArrow, this);
    },

	/**
	 * @method _triggerClick
	 * @protected
	 */
    _triggerClick: function(e)
    {
    	var host 			= this.get('host'),
    		trigger 		= this.get('trigger'),
    		triggerWidth	= parseInt(trigger.getStyle('width'), 10),
    		triggerRegion 	= trigger.get('region'),
    		layerX 			= e.pageX - triggerRegion.left + 5;

    	if(layerX >= triggerWidth/2) {
    		host.nextImage();
    	}
    	else {
    		host.prevImage();
    	}
    },

	/**
	 * @method _showArrow
	 * @protected
	 */
    _showArrow: function(e)
    {
    	var host 			= this.get('host'),
    	    trigger 		= this.get('trigger'),
    		triggerWidth	= parseInt(trigger.getStyle('width'), 10),
    		triggerRegion 	= trigger.get('region'),
    		layerX 			= e.pageX - triggerRegion.left + 5;

    	if(host.albumInfo !== null && host.albumInfo.images.length > 1) {
        	if(layerX >= triggerWidth/2) {
        		trigger.removeClass('fl-slideshow-mouse-nav-prev');
        		trigger.addClass('fl-slideshow-mouse-nav-next');
        	}
        	else {
        		trigger.removeClass('fl-slideshow-mouse-nav-next');
        		trigger.addClass('fl-slideshow-mouse-nav-prev');
        	}
        }
    },

	/**
	 * @method _hideArrow
	 * @protected
	 */
    _hideArrow: function()
    {
    	var trigger = this.get('trigger');

    	trigger.removeClass('fl-slideshow-mouse-nav-next');
    	trigger.removeClass('fl-slideshow-mouse-nav-prev');
    }

},	{

	/**
	 * Namespace for the plugin.
	 *
	 * @property NS
	 * @type String
	 * @protected
	 * @static
	 */
	NS: 'mouseNav',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Plugin.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * A Node that triggers the arrows.
		 *
		 * @attribute trigger
		 * @type Node
		 * @default null
		 */
		trigger: {
			value: null
		}
	}
});

/**
 * Ken Burns effect for slideshow images.
 *
 * @namespace FL
 * @class SlideshowKenBurns
 * @constructor
 * @param config {Object} Configuration object
 * @extends Base
 */
Y.namespace('FL').SlideshowKenBurns = Y.Base.create('fl-slideshow-ken-burns', Y.Base, [], {

	/**
	 * Runs the Ken Burns effect.
	 *
	 * @method run
	 */
	run: function()
	{
        var imageNode = null,
            transform = null;

		if(Y.FL.Utils.cssSupport('transform')) {

            // Image node
            imageNode = this.get('image').one('img');

            // Transform object
            transform = this._getTransform();

            // Apply the start transform
            imageNode.setStyles({
                '-webkit-transform-origin': transform.origin,
                '-moz-transform-origin': transform.origin,
                '-ms-transform-origin': transform.origin,
                'transform-origin': transform.origin,
                'transform': transform.start
            });

            // Transition to the end transform
            imageNode.transition({
                easing: 'ease-out',
                duration : this.get('duration'),
                'transform' : transform.end
            });
        }
	},

	/**
	 * @method _getTransform
	 * @protected
	 */
	_getTransform: function()
	{
        var zoom            = this.get('zoom'),
            image           = this.get('image'),
            i               = 0,
            zoomDirection   = null,
            transform       = null;

        // Random zoom direction
        i = Math.floor(Math.random() * Y.FL.SlideshowKenBurns.ZOOM_DIRECTIONS.length);
        zoomDirection = Y.FL.SlideshowKenBurns.ZOOM_DIRECTIONS[i];

        // Random transform
        i = Math.floor(Math.random() * Y.FL.SlideshowKenBurns.TRANSFORMS.length);
        transform = Y.FL.SlideshowKenBurns.TRANSFORMS[i];

        // Get the start and end transforms
        if(!image.hasClass('fl-slideshow-image-cropped') && zoomDirection == 'in') {
            i = Math.floor(Math.random() * 2);
            transform.start = i === 0 ? 'scale(1) translate(100px, 0)' : 'scale(1) translate(-100px, 0)';
            transform.end = 'scale(' + zoom + ') translate(0, 0)';
            transform.origin = 'center center';
        }
        else if(zoomDirection == 'out') {
            transform.start = 'scale(' + zoom + ') ' + transform.translate;
            transform.end = 'scale(1) translate(0, 0)';
        }
        else {
            transform.start = 'scale(1) translate(0, 0)';
            transform.end = 'scale(' + zoom + ') ' + transform.translate;
        }

        return transform;
	}

}, {

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * An instance of FL.Slideshow image to apply the
		 * Ken Burns effect on.
		 *
		 * @attribute image
		 * @type FL.Slideshow
		 * @default null
		 */
		image: {
			value: null
		},

		/**
		 * The amount to zoom the image. Zooming
		 * in our out is done randomly by this class.
		 *
		 * @attribute scale
		 * @type Number
		 * @default 1.2
		 */
		zoom: {
		  value: 1.2
		},

		/**
		 * The duration of the effect in seconds.
		 *
		 * @attribute duration
		 * @type Number
		 * @default 2
		 */
		duration: {
			value: 2
		}
	},

	/**
	 * The zoom directions that can be applied to an image.
	 *
	 * @property ZOOM_DIRECTIONS
	 * @type Object
	 * @readOnly
	 * @protected
	 * @static
	 */
	ZOOM_DIRECTIONS: [
        'in',
        'out'
    ],

	/**
	 * The types of transforms that can be applied to an image.
	 *
	 * @property TRANSFORMS
	 * @type Object
	 * @readOnly
	 * @protected
	 * @static
	 */
    TRANSFORMS: [
        {
            origin    : 'left top',
            translate : 'translate(-30px, -15px)'
        },{
            origin    : 'left center',
            translate : 'translate(-30px, 0)'
        },{
            origin    : 'left bottom',
            translate : 'translate(-30px, 15px)'
        },{
            origin    : 'right top',
            translate : 'translate(30px, -15px)'
        },{
            origin    : 'right center',
            translate : 'translate(30px, 0)'
        },{
            origin    : 'right bottom',
            translate : 'translate(30px, 15px)'
        }
    ]
});

/**
 * Navigation buttons widget for controlling a slideshow instance
 * and its child widgets.
 *
 * @namespace FL
 * @class SlideshowNav
 * @constructor
 * @param config {Object} Configuration object
 * @extends Widget
 */
Y.namespace('FL').SlideshowNav = Y.Base.create('fl-slideshow-nav', Y.Widget, [Y.WidgetChild], {

	/**
	 * An object containing the anchor nodes for all buttons.
	 *
	 * @property _buttons
	 * @type Object
	 * @default null
	 * @protected
	 */
	_buttons: null,

	/**
	 * An div node containing the anchor nodes for the main buttons.
	 *
	 * @property _buttonsContainer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_buttonsContainer: null,

	/**
	 * An div node containing the anchor nodes for the left buttons.
	 *
	 * @property _buttonsLeftContainer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_buttonsLeftContainer: null,

	/**
	 * An div node containing the anchor nodes for the right buttons.
	 *
	 * @property _buttonsRightContainer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_buttonsRightContainer: null,

	/**
	 * Property map for rendering SmugMug font icons.
	 *
	 * @property _fontIcons
	 * @type Object
	 * @protected
	 */
	_fontIcons: {
        buy: 'Cart',
        caption: 'InfoEncircled',
        close: 'XCrossEncircled',
        fullscreen: 'ScreenExpand',
        next: 'ArrowRight',
        nextPage: 'ArrowRight',
        pause: 'PlayerPause',
        play: 'PlayerPlay',
        prev: 'ArrowLeft',
        prevPage: 'ArrowLeft',
        social: 'Heart',
        thumbs: 'ViewThumbGrid'
    },

	/**
	 * The default content template for the nav
	 * inherited from Y.Widget. Set to null since
	 * only the bounding box is needed.
	 *
	 * @property CONTENT_TEMPLATE
	 * @type String
	 * @default null
	 * @protected
	 */
	CONTENT_TEMPLATE: null,

	/**
	 * Renders the buttons.
	 *
	 * @method renderUI
	 * @protected
	 */
	renderUI: function()
	{
		this._renderContainers();
		this._renderButtons();
		this._renderFontIcons();
	},

	/**
	 * Binds events to the root slideshow for each button.
	 *
	 * @method bindUI
	 * @protected
	 */
	bindUI: function()
	{
		var root 		= this.get('root'),
			id 			= this.get('id');

		if(this._buttons.prev) {
			this._buttons.prev.on('click', root.prevImage, root);
		}
		if(this._buttons.next) {
			this._buttons.next.on('click', root.nextImage, root);
		}
		if(this._buttons.play) {
			this._buttons.play.on('click', this._playClicked, this);
			root.on(id + '|played', this._showPauseButton, this);
			root.on(id + '|paused', this._showPlayButton, this);

			if(root._playing) {
				this._showPauseButton();
			}
			else {
				this._showPlayButton();
			}
		}
		if(this._buttons.buy) {

			root.on(id + '|albumLoadComplete', this._updateBuy, this);

			if(root.albumInfo !== null) {
				this._updateBuy();
			}
		}
		if(this._buttons.count) {
			root.on(id + '|imageLoadComplete', this._updateCount, this);
		}
		if(this._buttons.thumbs) {
			this._buttons.thumbs.on('click', root._toggleThumbs, root);
		}
		if(this._buttons.caption) {
			root.on(id + '|imageLoadComplete', this._updateCaption, this);
			this._updateCaption();
		}
		if(this._buttons.social) {
			this._buttons.social.on('click', root._toggleSocial, root);
		}
		if(this._buttons.fullscreen && root.fullscreen) {
			this._buttons.fullscreen.on('click', root.fullscreen.toggle, root.fullscreen);
		}
		if(this._buttons.close) {
			this._buttons.close.on('click', root.hide, root);
		}
	},

	/**
	 * @method destructor
	 * @protected
	 */
	destructor: function()
	{
		var root 	= this.get('root'),
			id 		= this.get('id');

		root.detach(id + '|*');
	},

	/**
	 * Renders the button left, right and main button containers.
	 *
	 * @method _renderContainers
	 * @protected
	 */
	_renderContainers: function()
	{
		var cb				= this.get('contentBox'),
			buttonsLeft		= this.get('buttonsLeft'),
			buttonsRight	= this.get('buttonsRight');

		this._buttonsContainer = Y.Node.create('<div></div>');
		this._buttonsContainer.addClass('fl-slideshow-nav-buttons');
		cb.appendChild(this._buttonsContainer);

		if(buttonsLeft.length > 0) {
			this._buttonsLeftContainer = Y.Node.create('<div></div>');
			this._buttonsLeftContainer.addClass('fl-slideshow-nav-buttons-left');
			cb.appendChild(this._buttonsLeftContainer);
		}
		if(buttonsRight.length > 0) {
			this._buttonsRightContainer = Y.Node.create('<div></div>');
			this._buttonsRightContainer.addClass('fl-slideshow-nav-buttons-right');
			cb.appendChild(this._buttonsRightContainer);
		}
	},

	/**
	 * Renders the buttons based on the buttons array
	 * passed in the configuration object.
	 *
	 * @method _renderButtons
	 * @protected
	 */
	_renderButtons: function()
	{
		var name 		= '',
			i 			= 0,
			k			= 0,
			b = [
			{
				names: this.get('buttons'),
				container: this._buttonsContainer
			},
			{
				names: this.get('buttonsLeft'),
				container: this._buttonsLeftContainer
			},
			{
				names: this.get('buttonsRight'),
				container: this._buttonsRightContainer
			}
		];

		this._buttons = {};

		for( ; i < b.length; i++) {
			for(k = 0; k < b[i].names.length; k++) {

				name = b[i].names[k];

				if(name.indexOf('count') > -1) {
					this._buttons[name] = Y.Node.create('<span></span>');
					this._updateCount();
				}
				else {
					this._buttons[name] = Y.Node.create('<a href="javascript:void(0);"></a>');
				}

				if(name.indexOf('buy') > -1) {
					this._buttons[name].setStyle('display', 'none');
				}

				this._buttons[name].set('name', name);
				this._buttons[name].set('aria-label', name);
				this._buttons[name].addClass('fl-slideshow-nav-' + name);
				b[i].container.appendChild(this._buttons[name]);
			}
		}
	},

	/**
	 * Renders SmugMug font icons for each button.
	 *
	 * @method _renderFontIcons
	 * @protected
	 */
	_renderFontIcons: function()
	{
        var name = null;

        if(this.get('useFontIcons') && typeof YUI.Env.mods['sm-fonticon'] !== 'undefined') {
            for(name in this._buttons) {
                if(typeof this._buttons[name] !== 'undefined' && typeof this._fontIcons[name] !== 'undefined') {
                    this._buttons[name].addClass('sm-fonticon-' + this._fontIcons[name]);
                    this._buttons[name].addClass('sm-fonticon sm-button-skin-default sm-button-nochrome');
                }
                else if(name.indexOf('count') > -1) {
                    this._buttons[name].addClass('fonticons-enabled');
                }
            }
        }
	},

	/**
	 * Updates the image count.
	 *
	 * @method _updateCount
	 * @protected
	 */
	_updateCount: function()
	{
		var html 		= '',
			countText	= Y.FL.SlideshowNav.COUNT_TEXT,
			current 	= 1,
			total 		= 1;

		if(this.get('root').albumInfo) {
			current 	= this.get('root').imageInfo.index + 1;
			total 		= this.get('root').albumInfo.images.length;
		}

		html = countText.replace('{current}', current).replace('{total}', total);
		this._buttons.count.set('innerHTML', html);
	},

	/**
	 * Shows the caption button if the current image
	 * has a caption, hides it if the image does not
	 * have a caption.
	 *
	 * @method _updateCaption
	 * @protected
	 */
	_updateCaption: function()
	{
		var root		= this.get('root'),
			imageInfo 	= root.imageInfo;

		if(imageInfo && imageInfo.caption === '') {
			root.caption.slideshowOverlay.enable();
			root.caption.slideshowOverlay.hide();
			this._buttons.caption.detach('click');
			this._buttons.caption.addClass('fl-slideshow-nav-caption-disabled');
		}
		else {
			this._buttons.caption.on('click', root._toggleCaption, root);
			this._buttons.caption.removeClass('fl-slideshow-nav-caption-disabled');
		}
	},

	/**
	 * Checks if buying has been enabled for the current album.
	 *
	 * @method _updateBuy
	 * @protected
	 */
	_updateBuy: function()
	{
		var sm 				= null,
			root			= this.get('root'),
			rootSource 		= root.get('source')[root.albumIndex],
			albumIndex		= root.albumIndex,
			source 			= root.get('source')[albumIndex];

		if(rootSource && rootSource.type == 'smugmug') {
			if(typeof root.albumInfo.printable !== 'undefined') {
				this._updateBuyComplete();
			}
			else {
				sm = new Y.FL.SmugMugAPI();
				sm.addParam('method', 'smugmug.albums.getInfo');
				sm.addParam('AlbumID', source.id);
				sm.addParam('AlbumKey', source.key);
				sm.on('complete', this._updateBuyComplete, this);
				sm.request();
			}
		}
	},

	/**
	 * Shows the buy button and updates the buy url
	 * if buying has been enabled.
	 *
	 * @method _updateBuyComplete
	 * @param e {Object} The custom event object passed to this function.
	 * @protected
	 */
	_updateBuyComplete: function(e)
	{
		var root		= this.get('root'),
			printable 	= typeof e == 'undefined' ? root.albumInfo.printable : e.Album.Printable,
			link 		= root.albumInfo.link;

		if(printable) {
			root.albumInfo.printable = true;
			this._buttons.buy.set('href', 'https://secure.smugmug.com/cart/batchadd/?url=' + encodeURIComponent(link));
			this._buttons.buy.setStyle('display', 'inline-block');
		}
		else {
			root.albumInfo.printable = false;
			this._buttons.buy.setStyle('display', 'none');
		}

		this.fire('resize');
	},

	/**
	 * Pauses the slideshow if it is playing and
	 * plays the slideshow if it is paused.
	 *
	 * @method _playClicked
	 * @protected
	 */
	_playClicked: function()
	{
		var root = this.get('root');

		if(root._playing) {
			root.pause();
		}
		else {
			root.play();
		}
	},

	/**
	 * Toggles the button class for the play button
	 * so pause is hidden and play is shown.
	 *
	 * @method _showPlayButton
	 * @protected
	 */
	_showPlayButton: function()
	{
		this._buttons.play.removeClass('fl-slideshow-nav-pause');
		this._buttons.play.addClass('fl-slideshow-nav-play');

		if(this.get('useFontIcons') && typeof YUI.Env.mods['sm-fonticon'] !== 'undefined') {
            this._buttons.play.removeClass('sm-fonticon-PlayerPause');
            this._buttons.play.addClass('sm-fonticon-PlayerPlay');
		}
	},

	/**
	 * Toggles the button class for the play button
	 * so pause is shown and play is hidden.
	 *
	 * @method _showPauseButton
	 * @protected
	 */
	_showPauseButton: function()
	{
		this._buttons.play.removeClass('fl-slideshow-nav-play');
		this._buttons.play.addClass('fl-slideshow-nav-pause');

		if(this.get('useFontIcons') && typeof YUI.Env.mods['sm-fonticon'] !== 'undefined') {
            this._buttons.play.removeClass('sm-fonticon-PlayerPlay');
            this._buttons.play.addClass('sm-fonticon-PlayerPause');
		}
	}

}, {

	/**
	 * Custom CSS class name for the widget.
	 *
	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow-nav',

	/**
	 * Static string used for displaying the image count. Use {current}
	 * for the current image and {total} for the total number of images.
	 * Those placeholders will be replaced when the count node is created.
	 *
	 * @property COUNT_TEXT
	 * @type String
	 * @protected
	 * @static
	 */
	COUNT_TEXT: '{current} of {total}',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * An array of button names that is used to render the main buttons.
		 *
		 * @attribute buttons
		 * @type Array
		 * @default []
		 * @writeOnce
		 */
		buttons: {
			value: [],
			writeOnce: true
		},

		/**
		 * An array of button names that is used to render the left buttons.
		 *
		 * @attribute buttonsLeft
		 * @type Array
		 * @default []
		 * @writeOnce
		 */
		buttonsLeft: {
			value: [],
			writeOnce: true
		},

		/**
		 * An array of button names that is used to render the right buttons.
		 *
		 * @attribute buttonsRight
		 * @type Array
		 * @default []
		 * @writeOnce
		 */
		buttonsRight: {
			value: [],
			writeOnce: true
		},

		/**
		 * Whether to use font icons when available.
		 *
		 * @attribute useFontIcons
		 * @type Boolean
		 * @default true
		 * @writeOnce
		 */
		useFontIcons: {
			value: true,
			writeOnce: true
		}
	}
});

/**
 * A plugin for overlaying widgets in a slideshow
 * with specialized show and hide functionality.
 *
 * @namespace FL
 * @class SlideshowOverlay
 * @constructor
 * @param config {Object} Configuration object
 * @extends Plugin.Base
 */
Y.namespace('FL').SlideshowOverlay = Y.Base.create('fl-slideshow-overlay', Y.Plugin.Base, [], {

	/**
	 * Flag for whether the mouse has entered
	 * the host's bounding box.
	 *
	 * @property _focus
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_focus: false,

	/**
	 * Flag for whether the host's bounding box is visible.
	 *
	 * @property _visible
	 * @type Boolean
	 * @default true
	 * @protected
	 */
	_visible: true,

	/**
	 * Flag for whether show and hide functionality
	 * has been disabled.
	 *
	 * @property _disabled
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_disabled: false,

	/**
	 * An object containing properties for the show transition.
	 *
	 * @property _showProps
	 * @type Object
	 * @protected
	 */
	_showProps: {
	    duration: 0.5,
	    easing: 'ease-out',
	    opacity: 1
	},

	/**
	 * An object containing properties for the hide transition.
	 *
	 * @property _hideProps
	 * @type Object
	 * @protected
	 */
	_hideProps: {
	    duration: 0.5,
	    easing: 'ease-out',
	    opacity: 0
	},

	/**
	 * A timer object for delaying the hide transition.
	 *
	 * @property _hideTimer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_hideTimer: null,

	/**
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		var bb = this.get('host').get('boundingBox');

		this.afterHostEvent('render', this._initFocus);
		this.afterHostEvent('render', this._initVisibility);

		if(this.get('closeButton')) {
			this._initCloseButton();
		}

		bb.addClass('fl-slideshow-overlay');
    },

	/**
	 * @method destructor
	 * @protected
	 */
	destructor: function()
	{
		this._hideTimerCancel();
	},

	/**
	 * Binds the mouseenter and mouseleave events for setting focus.
	 *
	 * @method _initFocus
	 * @protected
	 */
	_initFocus: function()
	{
		var bb = this.get('host').get('boundingBox');
		bb.on('mouseenter', Y.bind(this._setFocusOnMouseenter, this));
		bb.on('mouseleave', Y.bind(this._setFocusOnMouseleave, this));
	},

	/**
	 * Sets the initial visibility of the host's bounding box.
	 *
	 * @method _initVisibility
	 * @protected
	 */
	_initVisibility: function()
	{
		var bb 			= this.get('host').get('boundingBox'),
			hideStyle 	= this.get('hideStyle');

		if(!this.get('visible')) {

			if(hideStyle == 'display') {
				bb.setStyle('display', 'none');
			}
			else if(hideStyle == 'left') {
				bb.setStyle('left', '-99999px');
			}

			bb.setStyle('opacity', '0');
			this._visible = false;
		}
	},

	/**
	 * Creates and inserts the close button.
	 *
	 * @method _initCloseButton
	 * @protected
	 */
	_initCloseButton: function()
	{
		var bb 			= this.get('host').get('boundingBox'),
			closeButton = null;

		closeButton = Y.Node.create('<a class="fl-slideshow-overlay-close" href="javascript:void(0);"></a>');
		closeButton.on('click', Y.bind(this._closeButtonClick, this));

		if(typeof YUI.Env.mods['sm-fonticon'] !== 'undefined') {
            closeButton.addClass('sm-fonticon sm-fonticon-XCrossEncircled sm-button-skin-default sm-button-nochrome');
        }

		bb.insert(closeButton);
	},

	/**
	 * Hides the overlay when the close button is clicked.
	 *
	 * @method _closeButtonClick
	 * @protected
	 */
	_closeButtonClick: function()
	{
		var bb = this.get('host').get('boundingBox');
		bb.transition(this._hideProps, Y.bind(this._hideComplete, this));
	},

	/**
	 * Sets the focus flag to true.
	 *
	 * @method _setFocusOnMouseenter
	 * @protected
	 */
	_setFocusOnMouseenter: function()
	{
		this._focus = true;
	},

	/**
	 * Sets the focus flag to false.
	 *
	 * @method _setFocusOnMouseleave
	 * @protected
	 */
	_setFocusOnMouseleave: function()
	{
		this._focus = false;
	},

	/**
	 * Disables show and hide functionality.
	 *
	 * @method disable
	 * @public
	 */
	disable: function()
	{
		this._disabled = true;
	},

	/**
	 * Enables show and hide functionality.
	 *
	 * @method enable
	 * @public
	 */
	enable: function()
	{
		this._disabled = false;
	},

	/**
	 * Shows the host's bounding box with a fade in transition.
	 *
	 * @method show
	 * @public
	 */
	show: function()
	{
		var bb 			= this.get('host').get('boundingBox'),
			hideStyle 	= this.get('hideStyle');

		if(this._disabled) {
			return;
		}

		if(hideStyle == 'display') {
			bb.setStyle('display', 'block');
		}
		else if(hideStyle == 'left') {
			bb.setStyle('left', 'auto');
		}

		bb.transition(this._showProps, Y.bind(this._showComplete, this));

		/**
		 * @event hideStart
		 */
		this.fire('showStart');
	},

	/**
	 * @method _showComplete
	 * @protected
	 */
	_showComplete: function()
	{
		this._visible = true;
		this.hideWithTimer();

		/**
		 * @event showComplete
		 */
		this.fire('showComplete');
	},

	/**
	 * Hides the host's bounding box with a fade out transition.
	 *
	 * @method hide
	 * @public
	 */
	hide: function()
	{
		if(this._focus || this._disabled) {
			return;
		}

		var bb = this.get('host').get('boundingBox');
		bb.transition(this._hideProps, Y.bind(this._hideComplete, this));

		/**
		 * @event hideStart
		 */
		this.fire('hideStart');
	},

	/**
	 * Hides the host's bounding box with a fade out transition
	 * after a timer completes.
	 *
	 * @method hideWithTimer
	 * @public
	 */
	hideWithTimer: function()
	{
		this._hideTimerCancel();
		this._hideTimer = Y.later(this.get('hideDelay'), this, this.hide);
	},

	/**
	 * Cancels the hide timer.
	 *
	 * @method _hideTimerCancel
	 * @protected
	 */
	_hideTimerCancel: function()
	{
		if(this._hideTimer) {
			this._hideTimer.cancel();
			this._hideTimer = null;
		}
	},

	/**
	 * @method _hideComplete
	 * @protected
	 */
	_hideComplete: function()
	{
		var bb 			= this.get('host').get('boundingBox'),
			hideStyle 	= this.get('hideStyle');

		if(hideStyle == 'display') {
			bb.setStyle('display', 'none');
		}
		else if(hideStyle == 'left') {
			bb.setStyle('left', '-99999px');
		}

		this._visible = false;

		/**
		 * @event hideComplete
		 */
		this.fire('hideComplete');
	}

},	{

	/**
	 * Namespace for the plugin.
	 *
	 * @property NS
	 * @type String
	 * @protected
	 * @static
	 */
	NS: 'slideshowOverlay',

	/**
	 * Static property used to define the default attribute configuration of
	 * the plugin.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * Whether to use the close button or not.
		 *
		 * @attribute closeButton
		 * @type Boolean
		 * @default false
		 * @writeOnce
		 */
		closeButton: {
			value: false,
			writeOnce: true
		},

		/**
		 * The time to wait before hiding the host's bounding box.
		 * Measured in milliseconds.
		 *
		 * @attribute hideDelay
		 * @type Number
		 * @default 3000
		 * @writeOnce
		 */
		hideDelay: {
			value: 3000,
			writeOnce: true
		},

		/**
		 * The style to use for hiding the image. Possible
		 * values are display and left.
		 *
		 * @attribute hideStyle
		 * @type String
		 * @default display
		 * @writeOnce
		 */
		hideStyle: {
			value: 'display',
			writeOnce: true
		},

		/**
		 * Sets the initial visibility of the host's boudning box.
		 *
		 * @attribute visible
		 * @type Boolean
		 * @default true
		 * @writeOnce
		 */
		visible: {
			value: true,
			writeOnce: true
		}
	}
});

/**
 * Social buttons widget used in slideshows.
 *
 * @namespace FL
 * @class SlideshowSocial
 * @constructor
 * @param config {Object} Configuration object
 * @extends Widget
 */

Y.namespace('FL').SlideshowSocial = Y.Base.create('fl-slideshow-social', Y.Widget, [Y.WidgetChild], {

	/**
	 * An object containing the social button nodes.
	 *
	 * @property _buttons
	 * @type Object
	 * @default null
	 * @protected
	 */
	_buttons: null,

	/**
	 * @method renderUI
	 * @protected
	 */
	renderUI: function()
	{
		this._buttons = {};
	},

	/**
	 * @method bindUI
	 * @protected
	 */
	bindUI: function()
	{
		var root = this.get('root');

		if(root.get('likeButtonEnabled')) {
			root.on('imageLoadComplete', Y.bind(this._updateLikeButton, this));
		}
		if(root.get('tweetButtonEnabled')) {
			root.on('imageLoadComplete', Y.bind(this._updateTweetButton, this));
		}
		if(root.get('pinterestButtonEnabled')) {
			root.on('imageLoadComplete', Y.bind(this._updatePinterestButton, this));
		}
	},

	/**
	 * @method _updateLikeButton
	 * @protected
	 */
	_updateLikeButton: function()
	{
		var src				= null,
			cb				= this.get('contentBox'),
			root			= this.get('root'),
			albumIndex		= root.albumIndex,
			rootSource 		= root.get('source')[albumIndex],
			imageInfo 		= root.imageInfo;

		if(this._buttons.like) {
			this._buttons.like.remove();
			this._buttons.like = null;
		}

		if(rootSource.type == 'smugmug') {
			src = 'https://www.facebook.com/plugins/like.php?';
			src += 'href=' + 'https://www.smugmug.com/services/graph/gallery/';
			src += rootSource.id + '_' + rootSource.key +'/' + imageInfo.id + '_' + imageInfo.key;
		}
		else {
			src = 'https://www.facebook.com/plugins/like.php?';
			src += 'href=' + encodeURIComponent(imageInfo.largeURL);
		}

		src += '&send=false';
		src += '&layout=button_count';
		src += '&width=90';
		src += '&show_faces=false';
		src += '&action=like';
		src += '&colorscheme=light';
		src += '&height=21';

		this._buttons.like = Y.Node.create('<iframe src="'+ src +'" scrolling="no" allowTransparency="true"></iframe>');

		this._buttons.like.setStyles({
			overflow: 'hidden',
			width: '90px',
			height: '21px'
		});

		cb.appendChild(this._buttons.like);
	},

	/**
	 * @method _updateTweetButton
	 * @protected
	 */
	_updateTweetButton: function()
	{
		var src			= null,
			imageInfo 	= this.get('root').imageInfo,
			cb			= this.get('contentBox');

		if(this._buttons.tweet) {
			this._buttons.tweet.remove();
			this._buttons.tweet = null;
		}

		src = 'https://platform.twitter.com/widgets/tweet_button.html?';
		src += 'url=' + encodeURIComponent(imageInfo.largeURL);
		src += '&count=none';

		this._buttons.tweet = Y.Node.create('<iframe src="'+ src +'" scrolling="no" allowTransparency="true"></iframe>');

		this._buttons.tweet.setStyles({
			overflow: 'hidden',
			width: '90px',
			height: '21px'
		});

		cb.appendChild(this._buttons.tweet);
	},

	/**
	 * @method _updatePinterestButton
	 * @protected
	 */
	_updatePinterestButton: function()
	{
		var href		= 'https://pinterest.com/pin/create/button/',
			imageInfo 	= this.get('root').imageInfo,
			cb			= this.get('contentBox');

		if(this._buttons.pin) {
			this._buttons.pin.remove();
			this._buttons.pin = null;
		}

		href += '?url=' + encodeURIComponent(window.location.href);
		href += '&media='+ encodeURIComponent(imageInfo.mediumURL);
		href += '&description='+ encodeURIComponent(imageInfo.caption);

		this._buttons.pin = Y.Node.create('<a></a>');
		this._buttons.pin.setAttribute('data-pin-config', 'none');
		this._buttons.pin.setAttribute('data-pin-do', 'buttonPin');
		this._buttons.pin.setAttribute('href', href);
		this._buttons.pin.setAttribute('target', '_blank');
		this._buttons.pin.set('innerHTML', '<img src="https://assets.pinterest.com/images/pidgets/pin_it_button.png" border="0" />');

		cb.appendChild(this._buttons.pin);
	}

}, {

	/**
	 * Custom CSS class name for the widget.
	 *
	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow-social',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

	}
});

/**
 * Creates a grid of FL.SlideshowImage instances.
 *
 * @namespace FL
 * @class SlideshowThumbs
 * @constructor
 * @param config {Object} Configuration object
 * @extends Widget
 */
Y.namespace('FL').SlideshowThumbs = Y.Base.create('fl-slideshow-thumbs', Y.Widget, [Y.WidgetParent, Y.WidgetChild], {

	/**
	 * A div node used to hide the overflow when
	 * transitioning between pages.
	 *
	 * @property _clipBox
	 * @type Object
	 * @default null
	 * @protected
	 */
	_clipBox: null,

	/**
	 * A div node used to hold the pages.
	 *
	 * @property _pagesBox
	 * @type Object
	 * @default null
	 * @protected
	 */
	_pagesBox: null,

	/**
	 * A reference to the active page div node. Holds a grid
	 * of FL.SlideshowImage instances.
	 *
	 * @property _activePageBox
	 * @type Object
	 * @default null
	 * @protected
	 */
	_activePageBox: null,

	/**
	 * The index of the active page of thumbs.
	 *
	 * @property _activePageIndex
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_activePageIndex: 0,

	/**
	 * A reference to the next page div node. Holds a grid
	 * of FL.SlideshowImage instances.
	 *
	 * @property _nextPageBox
	 * @type Object
	 * @default null
	 * @protected
	 */
	_nextPageBox: null,

	/**
	 * An array of FL.SlideshowImage instances in the active page.
	 *
	 * @property _activeImages
	 * @type Array
	 * @default null
	 * @protected
	 */
	_activeImages: null,

	/**
	 * An array of FL.SlideshowImage instances used to
	 * preload the next page of images.
	 *
	 * @property _nextImages
	 * @type Array
	 * @default null
	 * @protected
	 */
	_nextImages: null,

	/**
	 * An array of FL.SlideshowImage instances used to
	 * preload the previous page of images.
	 *
	 * @property _prevImages
	 * @type Array
	 * @default null
	 * @protected
	 */
	_prevImages: null,

	/**
	 * An instance of FL.SlideshowNav used for the left nav.
	 *
	 * @property _leftNav
	 * @type Object
	 * @default null
	 * @protected
	 */
	_leftNav: null,

	/**
	 * An instance of FL.SlideshowNav used for the right nav.
	 *
	 * @property _rightNav
	 * @type Object
	 * @default null
	 * @protected
	 */
	_rightNav: null,

	/**
	 * An instance of FL.SlideshowNav used for the top nav.
	 *
	 * @property _topNav
	 * @type Object
	 * @default null
	 * @protected
	 */
	_topNav: null,

	/**
	 * An instance of FL.SlideshowNav used for the bottom nav.
	 *
	 * @property _bottomNav
	 * @type Object
	 * @default null
	 * @protected
	 */
	_bottomNav: null,

	/**
	 * Height of the bounding box.
	 *
	 * @property _bbHeight
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_bbHeight: 0,

	/**
	 * Width of the bounding box.
	 *
	 * @property _bbWidth
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_bbWidth: 0,

	/**
	 * Width of the content box.
	 *
	 * @property _cbWidth
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_cbWidth: 0,

	/**
	 * Left margin of the clip box.
	 *
	 * @property _clipBoxMarginLeft
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_clipBoxMarginLeft: 0,

	/**
	 * Top position of the clip box.
	 *
	 * @property _clipBoxTop
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_clipBoxTop: 0,

	/**
	 * The number of columns per page.
	 *
	 * @property _colsPerPage
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_colsPerPage: 0,

	/**
	 * The number of rows per page.
	 *
	 * @property _rowsPerPage
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_rowsPerPage: 0,

	/**
	 * The number of images per page.
	 *
	 * @property _imagesPerPage
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_imagesPerPage: 0,

	/**
	 * The number of pages.
	 *
	 * @property _numPages
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_numPages: 0,

	/**
	 * Height of the pages.
	 *
	 * @property _pageHeight
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_pageHeight: 0,

	/**
	 * Width of the pages.
	 *
	 * @property _pageWidth
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_pageWidth: 0,

	/**
	 * The horizontal spacing between thumbs.
	 *
	 * @property _horizontalSpacing
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_horizontalSpacing: 0,

	/**
	 * The vertical spacing between thumbs.
	 *
	 * @property _verticalSpacing
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_verticalSpacing: 0,

	/**
	 * Width of the left nav.
	 *
	 * @property _leftNavWidth
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_leftNavWidth: 0,

	/**
	 * Width of the right nav.
	 *
	 * @property _rightNavWidth
	 * @type Number
	 * @default 0
	 * @protected
	 */
	_rightNavWidth: 0,

	/**
	 * An instance of FL.SlideshowTransition for the current transition.
	 *
	 * @property _transition
	 * @type FL.SlideshowTransition
	 * @default null
	 * @protected
	 */
	_transition: null,

	/**
	 * Whether the pages are currently transitioning or not.
	 *
	 * @property _verticalSpacing
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_transitioning: false,

	/**
	 * Direction of the current transition.
	 *
	 * @property _transitionDirection
	 * @type String
	 * @default next
	 * @protected
	 */
	_transitionDirection: 'next',

	/**
	 * Provides functionality for gesture based transitions
 	 * between the active and next pages.
	 *
	 * @property _gestures
	 * @type FL.SlideshowGestures
	 * @default null
	 * @protected
	 */
	_gestures: null,

	/**
	 * Initialize image vars.
	 *
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		this._activeImages = [];
		this._nextImages = [];
		this._prevImages = [];
	},

	/**
	 * Renders the UI boxes.
	 *
	 * @method renderUI
	 * @protected
	 */
	renderUI: function()
	{
		this._renderBoxes();
		this._renderNavs();
	},

	/**
	 * Binds the UI events.
	 *
	 * @method bindUI
	 * @protected
	 */
	bindUI: function()
	{
		var root 		= this.get('root'),
			id 			= this.get('id'),
			transition 	= this.get('transition');

		root.on(id + '|albumLoadComplete', this._albumLoadComplete, this);

		if('ontouchstart' in window && this.get('touchSupport')) {

			this._gestures = new Y.FL.SlideshowGestures({
				direction: transition == 'slideVertical' ? 'vertical' : 'horizontal',
				activeItem: this._activePageBox,
				nextItem: this._nextPageBox
			});

			this._gestures.on('moveStart', this._gesturesMoveStart, this);
			this._gestures.on('endComplete', this._gesturesEndComplete, this);
		}
	},

	/**
	 * Syncs the UI boxes.
	 *
	 * @method syncUI
	 * @protected
	 */
	syncUI: function()
	{
		this._syncBoxes();
		this._syncNavs();
	},

	/**
	 * @method destructor
	 * @protected
	 */
	destructor: function()
	{
		var root 	= this.get('root'),
			id 		= this.get('id');

		root.detach(id + '|*');

		Y.FL.SlideshowImageLoader.removeGroup('thumbs');
	},

	/**
	 * Unload all images.
	 *
	 * @method unload
	 */
	unload: function()
	{
		var root 	= this.get('root'),
			id 		= this.get('id'),
			i 		= 0;

		root.detach(id + '|imageLoadComplete');

		Y.FL.SlideshowImageLoader.removeGroup('thumbs');

		for( ; i < this._activeImages.length; i++) {
			this._activeImages[i].unload();
		}
	},

	/**
	 * Resizes the UI boxes.
	 *
	 * @method resize
	 */
	resize: function()
	{
		this._setSizeInfo();
		this._togglePageButtons();
		this._resizeBoxes();
		this._resizeNavs();

		if(this.get('root').albumInfo) {
			Y.FL.SlideshowImageLoader.removeGroup('thumbs');
			this._renderActivePage();
			this._preloadNextPage();
			this._preloadPrevPage();
		}

		// Enable or disable gestures.
		if(this._gestures && this._numPages < 2) {
			this._gestures.disable();
		}
		else if(this._gestures) {
			this._gestures.enable();
		}
	},

	/**
	 * Transitions to the previous page.
	 *
	 * @method prevPage
	 * @protected
	 */
	prevPage: function()
	{
		if(this._transitioning) {
			return;
		}

		this._transitionStart('prev');
	},

	/**
	 * Transitions to the next page.
	 *
	 * @method nextPage
	 * @protected
	 */
	nextPage: function()
	{
		if(this._transitioning) {
			return;
		}

		this._transitionStart('next');
	},

	/**
	 * Called when an album is loaded into the root slideshow widget.
	 *
	 * @method _albumLoadComplete
	 * @protected
	 */
	_albumLoadComplete: function()
	{
		var root 	= this.get('root'),
			id 		= this.get('id');

		root.once(id + '|imageLoadComplete', this.resize, this);
		root.on(id + '|imageLoadComplete', this._imageLoadComplete, this);
	},

	/**
	 * Called when an image is loaded into the root slideshow widget.
	 *
	 * @method _imageLoadComplete
	 * @protected
	 */
	_imageLoadComplete: function()
	{
		var albumInfo 	= this.get('root').albumInfo,
			lastActive 	= Y.one('.fl-slideshow-image-active'),
			lastInfo	= lastActive ? lastActive._imageInfo : null,
			nextActive 	= null,
			nextInfo	= this.get('root').imageInfo;

		this._setActiveImage(this._activeImages);

		nextActive = Y.one('.fl-slideshow-image-active');

		if(lastActive && !nextActive) {
			if(nextInfo.index === 0 && lastInfo.index === albumInfo.images.length - 1) {
				this.nextPage();
			}
			else if(lastInfo.index === 0 && nextInfo.index === albumInfo.images.length - 1) {
				this.prevPage();
			}
			else if(lastInfo.index < nextInfo.index) {
				this.nextPage();
			}
			else if(lastInfo.index > nextInfo.index) {
				this.prevPage();
			}
		}
	},

	/**
	 * Renders the boxes.
	 *
	 * @method _renderBoxes
	 * @protected
	 */
	_renderBoxes: function()
	{
		// Clip box
		this._clipBox = Y.Node.create('<div></div>');
		this._clipBox.addClass('fl-slideshow-thumbs-clip');
		this.get('contentBox').insert(this._clipBox);

		// Pages box
		this._pagesBox = Y.Node.create('<div></div>');
		this._pagesBox.addClass('fl-slideshow-thumbs-pages');
		this._clipBox.insert(this._pagesBox);

		// Active page box
		this._activePageBox = Y.Node.create('<div></div>');
		this._activePageBox.addClass('fl-slideshow-thumbs-page');
		this._pagesBox.insert(this._activePageBox);

		// Next page box
		this._nextPageBox = Y.Node.create('<div></div>');
		this._nextPageBox.addClass('fl-slideshow-thumbs-page');
		this._pagesBox.insert(this._nextPageBox);
	},

	/**
	 * Syncs the boxes.
	 *
	 * @method _syncBoxes
	 * @protected
	 */
	_syncBoxes: function()
	{
		// Active page box
		this._activePageBox.setStyle('left', '0');

		// Next page box
		this._nextPageBox.setStyle('left', '-9999px');
	},

	/**
	 * Resizes the boxes.
	 *
	 * @method _resizeBoxes
	 * @protected
	 */
	_resizeBoxes: function()
	{
		this.set('width', this._bbWidth);
		this.set('height', this._bbHeight);

		this.get('contentBox').setStyle('width', this._cbWidth + 'px');

		this._clipBox.setStyle('width', this._pageWidth + 'px');
		this._clipBox.setStyle('height', this._pageHeight + 'px');
		this._clipBox.setStyle('padding', this._verticalSpacing + 'px 0 0 ' + this._horizontalSpacing + 'px ');
		this._clipBox.setStyle('margin',  '0 0 0 ' + this._clipBoxMarginLeft + 'px');
		this._clipBox.setStyle('top', this._clipBoxTop);

		this._pagesBox.setStyle('width', this._pageWidth + 'px');
		this._pagesBox.setStyle('height', this._pageHeight + 'px');

		this._activePageBox.setStyle('width', this._pageWidth + 'px');
		this._activePageBox.setStyle('height', this._pageHeight + 'px');

		this._nextPageBox.setStyle('width', this._pageWidth + 'px');
		this._nextPageBox.setStyle('height', this._pageHeight + 'px');
	},

	/**
	 * Renders the active page of images.
	 *
	 * @method _renderActivePage
	 * @protected
	 */
	_renderActivePage: function()
	{
		var i 			= 0,
			root		= this.get('root'),
			imageIndex 	= this._imagesPerPage * this._activePageIndex,
			endIndex	= imageIndex + this._imagesPerPage,
			images 		= root.albumInfo.images;

		this._clearActiveImage();

		// Remove current images
		for( ; i < this._activeImages.length; i++) {
			this._activeImages[i].remove();
			this._activeImages[i].unload();
			this._activeImages[i].get('boundingBox')._imageInfo = null;
			this._activeImages[i].get('boundingBox').remove();
		}

		// Draw images
		for(i = 0; imageIndex < endIndex; imageIndex++) {

			if(!images[imageIndex]) {
				break;
			}

			this._renderImage(this._activeImages, i, this._activePageBox, images[imageIndex]);
			i++;
		}

		this._setActiveImage(this._activeImages);
	},

	/**
	 * Renders the next page of images.
	 *
	 * @method _renderNextPage
	 * @protected
	 */
	_renderNextPage: function()
	{
		var i 			= 0,
			imageArray 	= this._transitionDirection == 'next' ? this._nextImages : this._prevImages;

		this._nextPageBox.get('children').remove();

		for( ; i < imageArray.length; i++) {
			if(imageArray[i]._imageInfo) {
				this._renderImage(imageArray, i, this._nextPageBox, imageArray[i]._imageInfo);
			}
			else {
				break;
			}
		}

		this._setActiveImage(imageArray);
	},

	/**
	 * Preloads the next page of images.
	 *
	 * @method _preloadNextPage
	 * @protected
	 */
	_preloadNextPage: function()
	{
		var pageIndex = this._activePageIndex + 1 >= this._numPages ? 0 : this._activePageIndex + 1;

		this._preloadPage(pageIndex, this._nextImages);
	},

	/**
	 * Preloads the previous page of images.
	 *
	 * @method _preloadPrevPage
	 * @protected
	 */
	_preloadPrevPage: function()
	{
		var pageIndex = this._activePageIndex - 1 < 0 ? this._numPages - 1 : this._activePageIndex - 1;

		this._preloadPage(pageIndex, this._prevImages);
	},

	/**
	 * Preloads a page of images.
	 *
	 * @method _preloadPage
	 * @param imageIndex {Number} The image index to start preloading from.
	 * @param imageArray {Array} The array to store the preloaded images.
	 * @protected
	 */
	_preloadPage: function(pageIndex, imageArray)
	{
		var i 			= 0,
			root		= this.get('root'),
			images 		= root.albumInfo.images,
			imageIndex	= pageIndex * this._imagesPerPage,
			endIndex	= imageIndex + this._imagesPerPage,
			imageConfig = this.get('imageConfig'),
			width 		= imageConfig.width,
			height 		= imageConfig.height;

		if(this._numPages > 1) {

			// Unload existing images
			for( ; i < imageArray.length; i++) {
				imageArray[i].remove();
				imageArray[i].unload();
			}

			// Preload the images
			for(i = 0; imageIndex < endIndex; imageIndex++) {

				if(!images[imageIndex]) {
					continue;
				}

				this._renderImage(imageArray, i);
				imageArray[i].preload(images[imageIndex], width, height);
				i++;
			}
		}
	},

	/**
	 * Renders an image.
	 *
	 * @method _renderImage
	 * @protected
	 */
	_renderImage: function(imageArray, i, page, imageInfo)
	{
		var imageBB		= null,
			imageConfig = this.get('imageConfig');

		// Create the image?
		if(typeof imageArray[i] == 'undefined') {
			imageConfig.loadGroup = 'thumbs';
			imageConfig.useThumbSizes = true;
			imageConfig.loadVideos = false;
			imageArray[i] = new Y.FL.SlideshowImage(imageConfig);
			imageBB = imageArray[i].get('boundingBox');
			imageBB.on('click', this._imageClick, this);
			imageBB.on('mouseover', this._imageMouseover, this);
			imageBB.on('mouseout', this._imageMouseout, this);
		}

		// Image bounding box
		imageBB = imageArray[i].get('boundingBox');
		imageBB.setStyle('margin', '0 ' + this._horizontalSpacing + 'px ' + this._verticalSpacing + 'px 0');

		// Add the image to a page?
		if(page) {
			this._childrenContainer = page;
			this.add(imageArray[i]);
			imageArray[i].resize(imageConfig.width, imageConfig.height);
		}

		// Load the image?
		if(imageInfo) {
			imageArray[i].load(imageInfo);
			imageBB._imageInfo = imageInfo;
		}
	},

	/**
     * Overrides the WidgetParent _uiAddChild method so _renderImage
     * will render to the appropriate page.
     *
     * @method _uiAddChild
     * @protected
     * @param child {Widget} The child Widget instance to render.
     * @param parentNode {Object} The Node under which the
     * child Widget is to be rendered. Set to the appropriate page
     * in the _renderImage method by setting _childrenContainer.
     */
    _uiAddChild: function (child, parentNode)
    {
        child.render(parentNode);
        parentNode.appendChild(child.get('boundingBox'));
    },

	/**
	 * Called when an image is clicked.
	 *
	 * @method _imageClick
	 * @protected
	 */
	_imageClick: function(e)
	{
		var root = this.get('root');

		if(this.get('pauseOnClick')) {
			root.pause();
		}

		root.loadImage(e.currentTarget._imageInfo.index);

		/**
		 * Fires when an image is clicked.
		 *
		 * @event imageClick
		 */
		this.fire('imageClick');
	},

	/**
	 * Sets the active image.
	 *
	 * @method _setActiveImage
	 * @param imageArray {Array} The image array to check for the active image.
	 * @protected
	 */
	_setActiveImage: function(imageArray)
	{
		var i = 0;

		this._clearActiveImage();

		for( ; i < imageArray.length; i++) {
			if(imageArray[i]._imageInfo) {
				if(imageArray[i]._imageInfo.index == this.get('root').imageInfo.index) {
					imageArray[i].get('boundingBox').addClass('fl-slideshow-image-active');
					break;
				}
			}
		}
	},

	/**
	 * Removes the class name 'fl-slideshow-image-active'
	 * from the active image.
	 *
	 * @method _clearActiveImage
	 * @protected
	 */
	_clearActiveImage: function()
	{
		var active = Y.one('.fl-slideshow-image-active');

		if(active) {
			active.removeClass('fl-slideshow-image-active');
		}
	},

	/**
	 * Gets the transition type.
	 *
	 * @method _getTransition
	 * @protected
	 */
	_getTransition: function()
	{
		var transition = this.get('transition');

		if(transition == 'slideHorizontal' && this._transitionDirection == 'next') {
			return 'slideLeft';
		}
		else if(transition == 'slideHorizontal' && this._transitionDirection == 'prev') {
			return 'slideRight';
		}
		else if(transition == 'slideVertical' && this._transitionDirection == 'next') {
			return 'slideUp';
		}
		else if(transition == 'slideVertical' && this._transitionDirection == 'prev') {
			return 'slideDown';
		}

		return transition;
	},

	/**
	 * Starts the transition, moving in the provided direction.
	 *
	 * @method _transitionStart
	 * @param direction {String} The direction to transition.
	 * @protected
	 */
	_transitionStart: function(direction)
	{
		if(this._numPages > 1) {

			Y.FL.SlideshowImageLoader.removeGroup('thumbs');

			this._transitionDirection = direction;
			this._transitioning = true;
			this._nextPageBox.setStyle('left', '0px');
			this._renderNextPage();

			this._transition = new Y.FL.SlideshowTransition({
				itemIn: this._nextPageBox,
				itemOut: this._activePageBox,
				type: this._getTransition(),
				duration: this.get('transitionDuration'),
				easing: this.get('transitionEasing')
			});

			this._transition.once('complete', this._transitionComplete, this);
			this._transition.run();

			// Disable gestures if set.
			if(this._gestures) {
				this._gestures.disable();
			}
		}
	},

	/**
	 * Transition cleanup called when the current transition ends.
	 *
	 * @method _transitionComplete
	 * @protected
	 */
	_transitionComplete: function()
	{
		this._swapPageRefs();
		this._transitioning = false;
		this._transitionDirection = '';
		this._transition = null;

		// Enable gestures if set.
		if(this._gestures) {
			this._gestures.enable();
		}

		/**
		 * Fires when a page transition completes.
		 *
		 * @event transitionComplete
		 */
		this.fire('transitionComplete');
	},

	/**
	 * @method _gesturesMoveStart
	 * @param e {Object} The event object.
	 * @protected
	 */
	_gesturesMoveStart: function(e)
	{
		Y.FL.SlideshowImageLoader.removeGroup('thumbs');

		this._transitionDirection = e.direction;
		this._renderNextPage();
	},

	/**
	 * @method _gesturesEndComplete
	 * @protected
	 */
	_gesturesEndComplete: function()
	{
		this._swapPageRefs();
		this._transitionDirection = '';
		this.fire('transitionComplete');
	},

	/**
	 * Swaps the active page and next page references when
	 * a transition completes and sets the active page index.
	 *
	 * @method _swapPageRefs
	 * @protected
	 */
	_swapPageRefs: function()
	{
		var lastBox 	= this._activePageBox,
			lastImages 	= this._activeImages;

		this._activePageBox = this._nextPageBox;
		this._nextPageBox = lastBox;
		this._nextPageBox.setStyle('left', '-9999px');

		if(this._transitionDirection == 'next') {
			this._activeImages = this._nextImages;
			this._nextImages = lastImages;
		}
		else {
			this._activeImages = this._prevImages;
			this._prevImages = lastImages;
		}

		// Active page index
		if(this._transitionDirection == 'next' && this._activePageIndex + 1 < this._numPages) {
			this._activePageIndex++;
		}
		else if(this._transitionDirection == 'next') {
			this._activePageIndex = 0;
		}
		else if(this._transitionDirection == 'prev' && this._activePageIndex - 1 > -1) {
			this._activePageIndex--;
		}
		else if(this._transitionDirection == 'prev') {
			this._activePageIndex = this._numPages - 1;
		}

		// Swap gesture refs
		if(this._gestures) {
			this._gestures.set('activeItem', this._activePageBox);
			this._gestures.set('nextItem', this._nextPageBox);
		}

		this._preloadNextPage();
		this._preloadPrevPage();
	},

	/**
	 * Renders the enabled navs.
	 *
	 * @method _renderNavs
	 * @protected
	 */
	_renderNavs: function()
	{
		var topNavButtons 		= this.get('topNavButtons'),
			rightNavButtons 	= this.get('rightNavButtons'),
			bottomNavButtons 	= this.get('bottomNavButtons'),
			leftNavButtons 		= this.get('leftNavButtons');

		if(this.get('topNavEnabled') && topNavButtons.length > 0) {
			this._topNav = new Y.FL.SlideshowNav({ buttons: topNavButtons });
			this._topNav.get('boundingBox').addClass('fl-slideshow-thumbs-top-nav');
			this.add(this._topNav);
			this._topNav.render(this.get('contentBox'));
			this._clipBox.insert(this._topNav.get('boundingBox'), 'before');
			this._bindNavEvents(this._topNav);
		}
		if(this.get('rightNavEnabled') && rightNavButtons.length > 0) {
			this._rightNav = new Y.FL.SlideshowNav({ buttons: rightNavButtons });
			this._rightNav.get('boundingBox').addClass('fl-slideshow-thumbs-right-nav');
			this.add(this._rightNav);
			this._rightNav.render(this.get('contentBox'));
			this._bindNavEvents(this._rightNav);
		}
		if(this.get('bottomNavEnabled') && bottomNavButtons.length > 0) {
			this._bottomNav = new Y.FL.SlideshowNav({ buttons: bottomNavButtons });
			this._bottomNav.get('boundingBox').addClass('fl-slideshow-thumbs-bottom-nav');
			this.add(this._bottomNav);
			this._bottomNav.render(this.get('contentBox'));
			this._bindNavEvents(this._bottomNav);
		}
		if(this.get('leftNavEnabled') && leftNavButtons.length > 0) {
			this._leftNav = new Y.FL.SlideshowNav({ buttons: leftNavButtons });
			this._leftNav.get('boundingBox').addClass('fl-slideshow-thumbs-left-nav');
			this.add(this._leftNav);
			this._leftNav.render(this.get('contentBox'));
			this._bindNavEvents(this._leftNav);
		}
	},

	/**
	 * Syncs the navs.
	 *
	 * @method _syncNavs
	 * @protected
	 */
	_syncNavs: function()
	{
		var rightNavBB, bottomNavBB, leftNavBB;

		if(this._rightNav) {
			rightNavBB = this._rightNav.get('boundingBox');
			rightNavBB.setStyle('position', 'absolute');
			rightNavBB.setStyle('top', '0px');
			rightNavBB.setStyle('right', '0px');
		}
		if(this._bottomNav) {
			bottomNavBB = this._bottomNav.get('boundingBox');
			bottomNavBB.setStyle('position', 'absolute');
			bottomNavBB.setStyle('bottom', '0px');
			bottomNavBB.setStyle('width', '100%');
		}
		if(this._leftNav) {
			leftNavBB = this._leftNav.get('boundingBox');
			leftNavBB.setStyle('position', 'absolute');
			leftNavBB.setStyle('top', '0px');
			leftNavBB.setStyle('left', '0px');
		}
	},

	/**
	 * Resizes the navs.
	 *
	 * @method _resizeNavs
	 * @protected
	 */
	_resizeNavs: function()
	{
		var rightNavBB,
			leftNavBB,
			marginTop;

		if(this._rightNav) {
			rightNavBB = this._rightNav.get('boundingBox');
			marginTop = this._bbHeight/2 - parseInt(rightNavBB.getComputedStyle('height'), 10)/2;
			rightNavBB.setStyle('marginTop', marginTop + 'px');
		}
		if(this._leftNav) {
			leftNavBB = this._leftNav.get('boundingBox');
			marginTop = this._bbHeight/2 - parseInt(leftNavBB.getComputedStyle('height'), 10)/2;
			leftNavBB.setStyle('marginTop', marginTop + 'px');
		}
	},

	/**
	 * Binds events to the provided nav.
	 *
	 * @method _bindNavEvents
	 * @param nav {Object} The nav to bind to.
	 * @protected
	 */
	_bindNavEvents: function(nav)
	{
		if(nav._buttons.prevPage) {
			nav._buttons.prevPage.on('click', this.prevPage, this);
		}
		if(nav._buttons.nextPage) {
			nav._buttons.nextPage.on('click', this.nextPage, this);
		}

		nav.on('resize', this.resize, this);
	},

	/**
	 * Hides the prev page and next page buttons
	 * if there is only one page of thumbs.
	 *
	 * @method _togglePageButtons
	 * @protected
	 */
	_togglePageButtons: function()
	{
		var buttons = this.get('boundingBox').all('.fl-slideshow-nav-prevPage, .fl-slideshow-nav-nextPage'),
			display = buttons.getStyle('display')[0];

		if(this._numPages == 1 && display == 'inline-block') {
			buttons.setStyle('display', 'none');
			this._setSizeInfo();
		}
		else if(this._numPages > 1 && display == 'none') {
			buttons.setStyle('display', 'inline-block');
			this._setSizeInfo();
		}
	},

	/**
	 * Sets the size info used when resizing and loading pages.
	 *
	 * @method _setSizeInfo
	 * @protected
	 */
	_setSizeInfo: function()
	{
		var root					= this.get('root'),
			bb						= this.get('boundingBox'),
			bbPosition              = bb.getStyle('position'),
			bbLeftMargin			= parseInt(bb.getStyle('marginLeft'), 10),
			bbRightMargin			= parseInt(bb.getStyle('marginRight'), 10),
			bbTopMargin				= parseInt(bb.getStyle('marginTop'), 10),
			bbBottomMargin			= parseInt(bb.getStyle('marginBottom'), 10),
			bbLeftPadding			= parseInt(bb.getStyle('paddingLeft'), 10),
			bbRightPadding			= parseInt(bb.getStyle('paddingRight'), 10),
			bbTopPadding			= parseInt(bb.getStyle('paddingTop'), 10),
			bbBottomPadding			= parseInt(bb.getStyle('paddingBottom'), 10),
			parent 					= bb.get('parentNode'),
			parentWidth 			= parseInt(parent.getComputedStyle('width'), 10),
			parentHeight 			= parseInt(parent.getComputedStyle('height'), 10),
			bbWidth 				= parentWidth - bbLeftPadding - bbRightPadding - bbLeftMargin - bbRightMargin,
			bbHeight 				= parentHeight - bbTopPadding - bbBottomPadding - bbTopMargin - bbBottomMargin,
			cbWidth					= bbWidth,
			pageWidth 				= bbWidth,
			pageHeight 				= bbHeight,
			columns 				= this.get('columns'),
			rows 					= this.get('rows'),
			imageConfig				= this.get('imageConfig'),
			horizontalSpacing		= this.get('horizontalSpacing'),
			verticalSpacing			= this.get('verticalSpacing'),
			spaceEvenly				= this.get('spaceEvenly'),
			centerSinglePage		= this.get('centerSinglePage'),
			leftNavWidth			= 0,
			rightNavWidth			= 0,
			topNavHeight			= 0,
			bottomNavHeight			= 0,
			colsPerPage 			= columns,
			rowsPerPage				= rows,
			imagesPerPage			= 0,
			numPages				= 1,
			clipBoxMarginLeft		= 0,
			clipBoxTop				= 0,
			availHorizSpace			= 0,
			availVerticalSpace		= 0;

        // Position absolute causes some resizing bugs.
        bb.setStyle('position', 'relative');

		// Bounding box width
		if(!isNaN(columns)) {
			bbWidth = pageWidth = columns * (imageConfig.width + horizontalSpacing) + horizontalSpacing;
		}

		// Bounding box height
		if(!isNaN(rows)) {
			bbHeight = pageHeight = rows * (imageConfig.height + verticalSpacing) + verticalSpacing;
		}

		// Compensate for the navs
		if(this._leftNav) {

			leftNavWidth = parseInt(this._leftNav.get('boundingBox').getComputedStyle('width'), 10);

			if(isNaN(columns)) {
				pageWidth -= leftNavWidth;
			}
			else {
				bbWidth += leftNavWidth;
			}
		}
		if(this._rightNav) {

			rightNavWidth = parseInt(this._rightNav.get('boundingBox').getComputedStyle('width'), 10);

			if(isNaN(columns)) {
				pageWidth -= rightNavWidth;
			}
			else {
				bbWidth += rightNavWidth;
			}
		}
		if(this._topNav) {

			topNavHeight = parseInt(this._topNav.get('boundingBox').getComputedStyle('height'), 10);

			if(isNaN(rows)) {
				pageHeight -= topNavHeight;
			}
			else {
				bbHeight += topNavHeight;
			}
		}
		if(this._bottomNav) {

			bottomNavHeight = parseInt(this._bottomNav.get('boundingBox').getComputedStyle('height'), 10);

			if(isNaN(rows)) {
				pageHeight -= bottomNavHeight;
			}
			else {
				bbHeight += bottomNavHeight;
			}
		}

		// Columns per page
		if(isNaN(columns)) {
			colsPerPage = Math.floor(pageWidth/(imageConfig.width + horizontalSpacing));
			colsPerPage = colsPerPage < 1 ? 1 : colsPerPage;
		}

		// Rows per page
		if(isNaN(rows)) {
			rowsPerPage = Math.floor(pageHeight/(imageConfig.height + verticalSpacing));
			rowsPerPage = rowsPerPage < 1 ? 1 : rowsPerPage;
		}

		// Images per page
		imagesPerPage = colsPerPage * rowsPerPage;

		// Number of pages
		if(root.albumInfo) {
			numPages = Math.ceil(root.albumInfo.images.length/imagesPerPage);
		}

		// Horizontal spacing
		if(isNaN(columns) && spaceEvenly) {
			horizontalSpacing = Math.floor((pageWidth - (imageConfig.width * colsPerPage))/(colsPerPage + 1));
		}

		// Vertical spacing
		if(isNaN(rows) && spaceEvenly) {
			verticalSpacing = Math.floor((pageHeight - (imageConfig.height * rowsPerPage))/(rowsPerPage + 1));
		}

		// Content container width
		if(root.albumInfo && centerSinglePage && numPages == 1 && rowsPerPage == 1) {

			cbWidth = root.albumInfo.images.length * imageConfig.width;
			cbWidth += horizontalSpacing * (root.albumInfo.images.length + 1);

			if(this._leftNav) {
				cbWidth += leftNavWidth;
			}
			if(this._rightNav) {
				cbWidth += rightNavWidth;
			}
		}
		else {
			cbWidth = bbWidth;
		}

		// Final page width and height
		if(root.albumInfo && centerSinglePage && numPages == 1 && rowsPerPage == 1) {
			pageWidth = root.albumInfo.images.length * imageConfig.width;
			pageWidth += horizontalSpacing * root.albumInfo.images.length;
		}
		else {
			pageWidth = colsPerPage * (imageConfig.width + horizontalSpacing);
		}

		pageHeight = rowsPerPage * (imageConfig.height + verticalSpacing);

		// Clip box margin left
		if(numPages < 2) {
			clipBoxMarginLeft = leftNavWidth;
		}
		else {
			availHorizSpace = bbWidth;

			if(this._rightNav) {
				availHorizSpace -= rightNavWidth;
			}
			if(this._leftNav) {
				availHorizSpace -= leftNavWidth;
				clipBoxMarginLeft = leftNavWidth + (availHorizSpace - pageWidth - horizontalSpacing)/2;
			}
			else {
				clipBoxMarginLeft = (availHorizSpace - pageWidth - horizontalSpacing)/2;
			}
		}

		// Clip box margin top
		if(numPages > 1 && !spaceEvenly) {

			availVerticalSpace = bbHeight;

			if(this._topNav) {
				availVerticalSpace -= topNavHeight;
			}
			if(this._bottomNav) {
				availVerticalSpace -= bottomNavHeight;
			}

			clipBoxTop = (availVerticalSpace - (verticalSpacing + pageHeight))/2;
		}

		// Set the info
		this._bbHeight = bbHeight;
		this._bbWidth = bbWidth;
		this._cbWidth = cbWidth;
		this._clipBoxMarginLeft = clipBoxMarginLeft;
		this._clipBoxTop = clipBoxTop;
		this._colsPerPage = colsPerPage;
		this._rowsPerPage = rowsPerPage;
		this._imagesPerPage = imagesPerPage;
		this._numPages = numPages;
		this._pageHeight = pageHeight;
		this._pageWidth = pageWidth;
		this._leftNavWidth = leftNavWidth;
		this._rightNavWidth = rightNavWidth;
		this._horizontalSpacing = horizontalSpacing;
		this._verticalSpacing = verticalSpacing;
		this._activePageIndex = Math.floor(root.imageIndex/this._imagesPerPage);

		// Set back to the initial position.
        bb.setStyle('position', bbPosition);
	}

}, {

	/**
	 * Custom CSS class name for the widget.
	 *
	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow-thumbs',

	/**
	* Static property used to define the default attribute configuration of
	* the Widget.
	*
	* @property ATTRS
	* @type Object
	* @protected
	* @static
	*/
	ATTRS: {

		/**
		 * The number of thumbnail columns. If set to auto, the number of
		 * columns will be calculated based on the width of the parent node.
		 *
		 * @attribute columns
		 * @type String or Number
		 * @default auto
		 */
		columns: {
			value: 'auto'
		},

		/**
		 * The number of thumbnail rows. If set to auto, the number of
		 * rows will be calculated based on the height of the parent node.
		 *
		 * @attribute rows
		 * @type String or Number
		 * @default auto
		 */
		rows: {
			value: 'auto'
		},

		/**
		 * The horizontal spacing between thumbs.
		 *
		 * @attribute horizontalSpacing
		 * @type Number
		 * @default 15
		 */
		horizontalSpacing: {
			value: 15
		},

		/**
		 * The vertical spacing between thumbs.
		 *
		 * @attribute verticalSpacing
		 * @type Number
		 * @default 15
		 */
		verticalSpacing: {
			value: 15
		},

		/**
		 * Whether to space the thumbs evenly within a page.
		 *
		 * @attribute spaceEvenly
		 * @type Boolean
		 * @default true
		 */
		spaceEvenly: {
			value: true
		},

		/**
		 * Whether to center single pages of thumbs.
		 *
		 * @attribute centerSinglePage
		 * @type Boolean
		 * @default false
		 */
		centerSinglePage: {
			value: true
		},

		/**
		 * Whether to pause the parent slideshow when a thumb is clicked.
		 *
		 * @attribute pauseOnClick
		 * @type Boolean
		 * @default false
		 */
		pauseOnClick: {
			value: false
		},

		/**
		 * The type of transition to use between pages.
		 *
		 * @attribute transition
		 * @type String
		 * @default slideHorizontal
		 */
		transition: {
			value: 'slideHorizontal'
		},

		/**
		 * The duration of the transition between pages.
		 *
		 * @attribute transitionDuration
		 * @type Number
		 * @default 0.8
		 */
		transitionDuration: {
			value: 0.8
		},

		/**
		 * The type of transition easing to use between pages.
		 *
		 * @attribute transitionEasing
		 * @type String
		 * @default ease-out
		 */
		transitionEasing: {
			value: 'ease-out'
		},

		/**
		 * The configuration object used to create new instances of
		 * FL.SlideshowImage. See the API docs for {@link FL.SlideshowImage}
		 * for a complete list of configuration attributes.
		 *
		 * @attribute imageConfig
		 * @type Object
		 * @default {}
		 */
		imageConfig: {
			value: {
				crop: true,
				width: 50,
				height: 50
			}
		},

		/**
		 * Whether to use the top nav or not.
		 *
		 * @attribute topNavEnabled
		 * @type Boolean
		 * @default false
		 */
		topNavEnabled: {
			value: false
		},

		/**
		 * An array of button names used to render the top nav buttons.
		 *
		 * @attribute topNavButtons
		 * @type Array
		 * @default prevPage, nextPage
		 */
		topNavButtons: {
			value: ['prevPage', 'nextPage']
		},

		/**
		 * Whether to use the right nav or not.
		 *
		 * @attribute rightNavEnabled
		 * @type Boolean
		 * @default true
		 */
		rightNavEnabled: {
			value: true
		},

		/**
		 * An array of button names used to render the right nav buttons.
		 *
		 * @attribute rightNavButtons
		 * @type Array
		 * @default nextPage
		 */
		rightNavButtons: {
			value: ['nextPage']
		},

		/**
		 * Whether to use the bottom nav or not.
		 *
		 * @attribute bottomNavEnabled
		 * @type Boolean
		 * @default false
		 */
		bottomNavEnabled: {
			value: false
		},

		/**
		 * An array of button names used to render the bottom nav buttons.
		 *
		 * @attribute bottomNavButtons
		 * @type Array
		 * @default prevPage, nextPage
		 */
		bottomNavButtons:{
			value: ['prevPage', 'nextPage']
		},

		/**
		 * Whether to use the left nav or not.
		 *
		 * @attribute leftNavEnabled
		 * @type Boolean
		 * @default true
		 */
		leftNavEnabled: {
			value: true
		},

		/**
		 * An array of button names used to render the left nav buttons.
		 *
		 * @attribute leftNavButtons
		 * @type Array
		 * @default prevPage
		 */
		leftNavButtons:{
			value: ['prevPage']
		},

		/**
		 * Whether to use touch gestures, when available,
		 * to transition between pages or not.
		 *
		 * @attribute touchSupport
		 * @type Boolean
		 * @default false
		 */
		touchSupport: {
			value: false
		}
	}
});

/**
 * Provides functionality for transitions between slideshow components.
 *
 * @namespace FL
 * @class SlideshowTransition
 * @constructor
 * @param config {Object} Configuration object
 * @extends Base
 */
Y.namespace('FL').SlideshowTransition = Y.Base.create('fl-slideshow-transition', Y.Base, [], {

	/**
	 * The transition function to use when run is called.
	 *
	 * @property _transitionFunction
	 * @type String
	 * @default _transitionFade
	 * @protected
	 */
	_transitionFunction: '_transitionFade',

	/**
	 * The current transition type.
	 *
	 * @property _type
	 * @type String
	 * @default fade
	 * @protected
	 */
	_type: 'fade',

	/**
	 * Parses the transition type and sets the _transitionFunction
	 * used when run is called.
	 *
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		var type 		               = this.get('type'),
			typeArray	               = [],
			types                      = Y.FL.SlideshowTransition.TYPES,
			slideshowImageTypes        = Y.FL.SlideshowTransition.SLIDESHOW_IMAGE_TYPES,
			isSlideshowImageTransition = Y.Array.indexOf(slideshowImageTypes, type) > -1,
			isSlideshowImage           = this._isSlideshowImage(),
			itemIn 				       = this.get('itemIn'),
			itemOut 			       = this.get('itemOut');

        // Check for random transitions.
		if(type.indexOf(',') > -1) {
			typeArray = type.split(',');
			typeArray.sort(function() { return 0.5 - Math.random(); });
			type = typeArray[0];
		}

		// Make sure we can run this transition, otherwise set a fallback.
		if(!isSlideshowImage && isSlideshowImageTransition) {
            type = 'fade';
		}
		else if(isSlideshowImage) {
            if((itemIn && itemIn.one('img') === null) || (itemOut && itemOut.one('img') === null)) {
                type = 'none';
            }
            else if(isSlideshowImageTransition) {
                if((Y.UA.gecko && Y.UA.gecko < 5) || Y.UA.opera > 0 || (Y.UA.ie > 0 && Y.UA.ie < 9)) {
                    type = 'fade';
                }
            }
        }

		// Set the transition function and type.
		if(Y.FL.SlideshowTransition.TYPES[type]) {
			this._transitionFunction = types[type];
			this._type = type;
		}

		// Setup the items.
		this._setupItems();
	},

	/**
	 * Fires the start event and calls the transition function.
	 *
	 * @method run
	 */
	run: function()
	{
		/**
		 * Fires when the transition starts.
		 *
		 * @event start
		 */
		this.fire('start');

		this[this._transitionFunction].call(this);
	},

	/**
	 * Set initial styles for the items.
	 *
	 * @method _setupItems
	 * @protected
	 */
	_setupItems: function()
	{
        var itemIn  = this.get('itemIn'),
			itemOut = this.get('itemOut');

		if(itemIn) {
			itemIn.setStyle('zIndex', 2);
			itemIn.setStyle('opacity', 1);

			if(Y.FL.Utils.cssSupport('transform')) {
    			itemIn.setStyle('transform', 'translate(0, 0)');
    		}
    		else {
    			itemIn.setStyle('top', '0');
    			itemIn.setStyle('left', '0');
    		}
		}
		if(itemOut) {
			itemOut.setStyle('zIndex', 1);
		}
	},

	/**
	 * Checks if the transition is being run
	 * on an instance of FL.SlideshowImage or not.
	 *
	 * @method _isSlideshowImage
	 * @protected
	 */
	_isSlideshowImage: function()
	{
        var itemIn 	= this.get('itemIn'),
			itemOut = this.get('itemOut');

        if(itemIn && itemIn.hasClass('fl-slideshow-image')) {
			return true;
		}
		else if(itemOut && itemOut.hasClass('fl-slideshow-image')) {
			return true;
		}

		return false;
	},

	/**
	 * Starts the transtion using the provided property objects.
	 *
	 * @method _transitionStart
	 * @param propsIn {Object} The properties to animate in.
	 * @param propsOut {Object} The properties to animate out.
	 * @protected
	 */
	_transitionStart: function(propsIn, propsOut)
	{
		var itemIn 				= this.get('itemIn'),
			itemOut 			= this.get('itemOut'),
			itemInCallback		= Y.bind(this._transitionComplete, this),
			itemOutCallback 	= !itemIn ? itemInCallback : null,
			duration 			= this.get('duration'),
			easing 				= this.get('easing');

		if(itemIn) {
			propsIn.duration = propsIn.duration || duration;
			propsIn.easing = propsIn.easing || easing;
			itemIn.transition(propsIn);
		}
		if(itemOut) {
			propsOut.duration = propsOut.duration || duration;
			propsOut.easing = propsOut.easing || easing;
			itemOut.transition(propsOut);
		}

		if(itemInCallback) {
			Y.later(propsIn.duration * 1000 + 100, null, itemInCallback);
		}
		else if(itemOutCallback) {
			Y.later(propsOut.duration * 1000 + 100, null, itemOutCallback);
		}
	},

	/**
	 * Clean up method called when the transition completes.
	 *
	 * @method _transitionComplete
	 * @protected
	 */
	_transitionComplete: function()
	{
		this._set('itemIn', null);
		this._set('itemOut', null);

		/**
		 * Fires when the transition completes.
		 *
		 * @event complete
		 */
		this.fire('complete');
	},

	/**
	 * No transition.
	 *
	 * @method _transitionNone
	 * @protected
	 */
	_transitionNone: function()
	{
		var itemIn  = this.get('itemIn'),
			itemOut = this.get('itemOut');

		if(itemIn) {
			itemIn.setStyle('opacity', 1);
		}
		if(itemOut) {
			itemOut.setStyle('opacity', 0);
		}

		this._transitionComplete();
	},

	/**
	 * Fade transition.
	 *
	 * @method _transitionFade
	 * @protected
	 */
	_transitionFade: function()
	{
		var itemIn = this.get('itemIn');

		if(itemIn) {
			itemIn.setStyle('opacity', 0);
		}

		this._transitionStart({ opacity: 1 },{ opacity: 0 });
	},

	/**
	 * Slide left transition.
	 *
	 * @method _transitionSlideLeft
	 * @protected
	 */
	_transitionSlideLeft: function()
	{
		if(Y.FL.Utils.cssSupport('transform')) {
			this._cssTransitionSlide({
				inStart: 'translate(100%, 0)',
				inEnd: 'translate(0, 0)',
				outStart: 'translate(0, 0)',
				outEnd: 'translate(-100%, 0)'
			});
		}
		else {
			this._jsTransitionSlide('left');
		}
	},

	/**
	 * Slide right transition.
	 *
	 * @method _transitionSlideRight
	 * @protected
	 */
	_transitionSlideRight: function()
	{
		if(Y.FL.Utils.cssSupport('transform')) {
			this._cssTransitionSlide({
				inStart: 'translate(-100%, 0)',
				inEnd: 'translate(0, 0)',
				outStart: 'translate(0, 0)',
				outEnd: 'translate(100%, 0)'
			});
		}
		else {
			this._jsTransitionSlide('right');
		}
	},

	/**
	 * Slide up transition.
	 *
	 * @method _transitionSlideUp
	 * @protected
	 */
	_transitionSlideUp: function()
	{
		if(Y.FL.Utils.cssSupport('transform')) {
			this._cssTransitionSlide({
				inStart: 'translate(0, 100%)',
				inEnd: 'translate(0, 0)',
				outStart: 'translate(0, 0)',
				outEnd: 'translate(0, -100%)'
			});
		}
		else {
			this._jsTransitionSlide('up');
		}
	},

	/**
	 * Slide down transition.
	 *
	 * @method _transitionSlideDown
	 * @protected
	 */
	_transitionSlideDown: function()
	{
		if(Y.FL.Utils.cssSupport('transform')) {
			this._cssTransitionSlide({
				inStart: 'translate(0, -100%)',
				inEnd: 'translate(0, 0)',
				outStart: 'translate(0, 0)',
				outEnd: 'translate(0, 100%)'
			});
		}
		else {
			this._jsTransitionSlide('down');
		}
	},

	/**
	 * JavaScript slide transition.
	 *
	 * @method _jsTransitionSlide
	 * @protected
	 */
	_jsTransitionSlide: function(direction)
	{
		var itemIn 		= this.get('itemIn'),
			itemOut 	= this.get('itemOut'),
			itemOutEnd	= 0;

		// Item Out
		if(itemOut && direction == 'left') {
			itemOutEnd = -parseInt(itemOut.getStyle('width'), 10);
		}
		if(itemOut && direction == 'right') {
			itemOutEnd = parseInt(itemOut.getStyle('width'), 10);
		}
		if(itemOut && direction == 'up') {
			itemOutEnd = -parseInt(itemOut.getStyle('height'), 10);
		}
		if(itemOut && direction == 'down') {
			itemOutEnd = parseInt(itemOut.getStyle('height'), 10);
		}

		// Item In
		if(itemIn) {
			itemIn.setStyle('opacity', 1);
		}
		if(itemIn && direction == 'left') {
			itemIn.setStyle('left', itemIn.getStyle('width'));
		}
		if(itemIn && direction == 'right') {
			itemIn.setStyle('left', '-' + itemIn.getStyle('width'));
		}
		if(itemIn && direction == 'up') {
			itemIn.setStyle('top', itemIn.getStyle('height'));
		}
		if(itemIn && direction == 'down') {
			itemIn.setStyle('top', '-' + itemIn.getStyle('height'));
		}

		// Transition Start
		if(direction == 'left' || direction == 'right') {
			this._transitionStart({ left: 0 },{ left: itemOutEnd });
		}
		else {
			this._transitionStart({ top: 0 },{ top: itemOutEnd });
		}
	},

	/**
	 * CSS slide transition.
	 *
	 * @method _cssTransitionSlide
	 * @protected
	 */
	_cssTransitionSlide: function(props)
	{
		var itemIn 	        = this.get('itemIn'),
			itemOut         = this.get('itemOut'),
			transformProp   = Y.UA.chrome < 36 ? 'transform' : '-webkit-transform',
			inProps         = {},
			outProps        = {};

        inProps[transformProp] = props.inEnd;
        outProps[transformProp] = props.outEnd;

		if(itemIn) {
			itemIn.setStyle('transition', '');
			itemIn.setStyle('opacity', 1);
			itemIn.setStyle(transformProp, props.inStart);
		}
		if(itemOut) {
			itemOut.setStyle('transition', '');
			itemOut.setStyle(transformProp, props.outStart);
		}

		this._transitionStart(inProps, outProps);
	},

	/**
	 * Bars and blinds transition.
	 *
	 * @method _transitionBars
	 * @protected
	 */
	_transitionBars: function()
	{
        // Hide the image until the slices have transitioned in.
        this.get('itemIn').one('.fl-slideshow-image-img').setStyle('opacity', 0);

        var numBars   = this.get('bars'),
            slices    = this._renderSlices(1, numBars),
            duration  = this.get('duration'),
            delay     = 0,
            increment = 100,
            last      = false,
            i         = 0,
            clone     = null,
            props     = {
                duration: duration,
                opacity: 1
            };

        // barsRandom
        if(this._type == 'barsRandom') {
            slices = this._randomizeSlices(slices);
        }

        // Transition the slices.
        for( ; i < slices.length; i++) {

            // Make a clone of our transition properties.
            clone = Y.clone(props);

            // blinds
            if(this._type == 'blinds') {
                clone.width = parseFloat(slices[i].getComputedStyle('width'), 10) + 'px';
                slices[i].setStyle('width', '0px');
                increment = 50;
            }

            // Run the transition.
            last = i == slices.length - 1 ? true : false;
            Y.later(delay, this, this._transitionSlice, [slices[i], clone, last]);
            delay += increment;
        }

        this._transitionSlicesFadeLast(delay);
	},

	/**
	 * Boxes transition.
	 *
	 * @method _transitionBoxes
	 * @protected
	 */
	_transitionBoxes: function()
	{
        // Hide the image until the slices have transitioned in.
        this.get('itemIn').one('.fl-slideshow-image-img').setStyle('opacity', 0);

        var numCols     = this.get('boxCols'),
            numRows     = this.get('boxRows'),
            numSlices   = numCols * numRows,
            multi       = this._type != 'boxesRandom',
            slices      = this._renderSlices(numRows, numCols, multi),
            duration    = this.get('duration'),
            delay       = 0,
            increment   = 150,
            last      = false,
            i           = 0,
            row         = 0,
            col         = 0,
            startCol    = -1,
            clone       = null,
            props       = {
                duration: duration,
                opacity: 1
            };

        // boxesRandom
        if(!multi) {

            slices = this._randomizeSlices(slices);
            increment = 30;

            for( ; i < slices.length; i++) {
                clone = Y.clone(props);
                last = i == slices.length - 1 ? true : false;
                Y.later(delay, this, this._transitionSlice, [slices[i], clone, last]);
                delay += increment;
            }
        }
        // boxes
        else {
            while(i < numSlices) {
                for(row = 0; row < numRows; row++) {
                    if(row === 0) {
                        startCol++;
                        col = startCol;
                    }
                    if(col > -1 && col < numCols) {
                        i++;
                        clone = Y.clone(props);

                        // boxesGrow
                        if(this._type == 'boxesGrow') {
                            clone.height = parseFloat(slices[row][col].getComputedStyle('height'), 10) + 'px';
                            clone.width = parseFloat(slices[row][col].getComputedStyle('width'), 10) + 'px';
                            slices[row][col].setStyle('height', '0px');
                            slices[row][col].setStyle('width', '0px');
                            increment = 50;
                        }

                        last = i == numSlices - 1 ? true : false;
                        Y.later(delay, this, this._transitionSlice, [slices[row][col], clone, last]);
                    }
                    col--;
                }
                delay += increment;
            }
        }

        this._transitionSlicesFadeLast(delay);
	},

	/**
	 * Renders the divs for slice based transitions.
	 *
	 * @method _renderSlices
	 * @protected
	 */
	_renderSlices: function(numRows, numCols, multidimensional)
	{
        var itemIn      = this.get('itemIn'),
            itemHeight  = parseFloat(itemIn.getComputedStyle('height'), 10),
            itemWidth   = parseFloat(itemIn.getComputedStyle('width'), 10),
            img         = itemIn.one('img'),
            imgSrc      = img.get('src'),
            imgHeight   = parseFloat(img.getComputedStyle('height'), 10),
            imgWidth    = parseFloat(img.getComputedStyle('width'), 10),
            imgLeft     = parseFloat(img.getComputedStyle('left'), 10),
            imgTop      = parseFloat(img.getComputedStyle('top'), 10),
            col         = 0,
            row         = 0,
            sliceHeight = Math.round(itemHeight/numRows),
            sliceWidth  = Math.round(itemWidth/numCols),
            slice       = null,
            sliceImg    = null,
            slices      = [];

        for( ; row < numRows; row++) {

            if(typeof multidimensional !== 'undefined' && multidimensional) {
                slices[row] = [];
            }
            for(col = 0; col < numCols; col++) {

                slice = Y.Node.create('<div class="fl-slideshow-transition-slice"></div>');
                sliceImg = Y.Node.create('<img src="'+ imgSrc +'" />');

                slice.setStyles({
                    left: (sliceWidth * col) + 'px',
                    top: (sliceHeight * row) + 'px',
                    width: col == numCols - 1 ? (itemWidth - (sliceWidth * col)) + 'px' : sliceWidth + 'px',
                    height: row == numRows - 1 ? (itemHeight - (sliceHeight * row)) + 'px' : sliceHeight + 'px',
                    opacity: 0
                });

                sliceImg.setStyles({
                    height: imgHeight + 'px',
                    width: imgWidth + 'px',
                    top: imgTop - ((sliceHeight + (row * sliceHeight)) - sliceHeight) + 'px',
                    left: imgLeft - ((sliceWidth + (col * sliceWidth)) - sliceWidth) + 'px'
                });

                slice.append(sliceImg);
                itemIn.append(slice);

                if(typeof multidimensional !== 'undefined' && multidimensional) {
                    slices[row].push(slice);
                }
                else {
                    slices.push(slice);
                }
            }
        }

        return slices;
	},

	/**
	 * Fade the itemOut node.
	 *
	 * @method _transitionSlicesFadeLast
	 * @protected
	 */
	_transitionSlicesFadeLast: function(delay)
	{
        var itemOut = this.get('itemOut');

        if(itemOut && !itemOut.hasClass('fl-slideshow-image-cropped')) {
			itemOut.transition({
                duration: delay/1000 + this.get('duration'),
                opacity: 0
			});
		}
	},

	/**
	 * Transitions a single slice.
	 *
	 * @method _transitionSlice
	 * @protected
	 */
	_transitionSlice: function(slice, props, last)
	{
        var callback = last ? Y.bind(this._transitionSlicesComplete, this) : null;

        slice.transition(props, callback);
	},

	/**
	 * Complete callback for slice based transitions.
	 *
	 * @method _transitionSlicesComplete
	 * @protected
	 */
	_transitionSlicesComplete: function()
	{
        var itemIn = this.get('itemIn');

        itemIn.all('.fl-slideshow-transition-slice').remove();
        itemIn.one('.fl-slideshow-image-img').setStyle('opacity', 1);
        this._transitionComplete();
	},

	/**
	 * Randomizes a slices array.
	 *
	 * @method _radomizeSlices
	 * @protected
	 */
	_randomizeSlices: function(slices)
	{
        var i = slices.length, j, temp;

        if(i === 0) {
            return;
        }
        while(--i) {
            j = Math.floor( Math.random() * ( i + 1 ) );
            temp = slices[i];
            slices[i] = slices[j];
            slices[j] = temp;
        }

        return slices;
	},

	_transitionKenBurns: function()
	{
        var kbDuration  = this.get('kenBurnsDuration'),
            duration    = this.get('duration'),
            itemIn      = this.get('itemIn'),
            zoom        = this.get('kenBurnsZoom');

        this._transitionFade();

        (new Y.FL.SlideshowKenBurns({
            duration: kbDuration + duration + 4,
            image: itemIn,
            zoom: zoom
        })).run();
	}

}, {

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * The Node to transition in.
		 *
		 * @attribute itemIn
		 * @type Node
		 * @default null
		 */
		itemIn: {
			value: null
		},

		/**
		 * The Node to transition out.
		 *
		 * @attribute itemOut
		 * @type Node
		 * @default null
		 */
		itemOut: {
			value: null
		},

		/**
		 * The duration of the transition in seconds.
		 *
		 * @attribute duration
		 * @type Number
		 * @default 0.5
		 */
		duration: {
			value: 0.5
		},

		/**
		 * The type of easing to use.
		 *
		 * @attribute easing
		 * @type String
		 * @default ease-out
		 */
		easing: {
			value: 'ease-out'
		},

		/**
		 * The type of transition to use.
		 *
		 * @attribute type
		 * @type String
		 * @default fade
		 */
		type: {
			value: 'fade'
		},

		/**
		 * The number of bars to use for
		 * transitions such as blinds.
		 *
		 * @attribute bars
		 * @type Number
		 * @default 15
		 */
		bars: {
			value: 15
		},

		/**
		 * The number of columns to use for
		 * transitions such as boxes.
		 *
		 * @attribute boxCols
		 * @type Number
		 * @default 8
		 */
		boxCols: {
			value: 8
		},

		/**
		 * The number of rows to use for
		 * transitions such as boxes.
		 *
		 * @attribute boxRows
		 * @type Number
		 * @default 4
		 */
		boxRows: {
			value: 4
		},

		/**
		 * The duration the ken burns effect will
		 * last, measured in seconds.
		 *
		 * @attribute kenBurnsDuration
		 * @type Number
		 * @default 4
		 */
		kenBurnsDuration: {
            value: 4
		},

		/**
		 * The amount of zoom to use for the Ken Burns effect.
		 *
		 * @attribute kenBurnsZoom
		 * @type Number
		 * @default 1.2
		 */
		kenBurnsZoom: {
            value: 1.2
		}
	},

	/**
	 * The types of transitions and associated functions.
	 *
	 * @property TYPES
	 * @type Object
	 * @readOnly
	 * @protected
	 * @static
	 */
	TYPES: {
		fade: '_transitionFade',
		none: '_transitionNone',
		slideLeft: '_transitionSlideLeft',
		slideRight: '_transitionSlideRight',
		slideUp: '_transitionSlideUp',
		slideDown: '_transitionSlideDown',
		blinds: '_transitionBars',
		bars: '_transitionBars',
		barsRandom: '_transitionBars',
		boxes: '_transitionBoxes',
		boxesRandom: '_transitionBoxes',
		boxesGrow: '_transitionBoxes',
		kenBurns: '_transitionKenBurns'
	},

	/**
	 * The types of transitions that can only be
	 * run on FL.SlideshowImage widgets.
	 *
	 * @property SLIDESHOW_IMAGE_TYPES
	 * @type Object
	 * @readOnly
	 * @protected
	 * @static
	 */
	SLIDESHOW_IMAGE_TYPES: [
        'blinds',
        'bars',
        'barsRandom',
        'boxes',
        'boxesRandom',
        'boxesGrow',
        'kenBurns'
	]
});

/**
 * A highly configurable slideshow widget.
 *
 * @namespace FL
 * @class Slideshow
 * @constructor
 * @param config {Object} Configuration object
 * @extends FL.SlideshowBase
 */
Y.namespace('FL').Slideshow = Y.Base.create('fl-slideshow', Y.FL.SlideshowBase, [], {

	/**
	 * A FL.SlideshowFrame instance used for the main image.
	 *
	 * @property frame
	 * @type FL.SlideshowFrame
	 * @default null
	 */
	frame: null,

	/**
	 * A FL.SlideshowNav instance used for the main nav.
	 *
	 * @property nav
	 * @type FL.SlideshowNav
	 * @default null
	 */
	nav: null,

	/**
	 * A FL.SlideshowNav instance used for the image nav's left button.
	 *
	 * @property imageNavLeft
	 * @type FL.SlideshowNav
	 * @default null
	 */
	imageNavLeft: null,

	/**
	 * A FL.SlideshowNav instance used for the image nav's right button.
	 *
	 * @property imageNavRight
	 * @type FL.SlideshowNav
	 * @default null
	 */
	imageNavRight: null,

	/**
	 * A FL.SlideshowThumbs instance used for the thumbnail grid.
	 *
	 * @property thumbs
	 * @type FL.SlideshowThumbs
	 * @default null
	 */
	thumbs: null,

	/**
	 * A FL.SlideshowThumbs instance used for the vertical thumbnail grid.
	 *
	 * @property verticalThumbs
	 * @type FL.SlideshowThumbs
	 * @default null
	 */
	verticalThumbs: null,

	/**
	 * A FL.SlideshowCaption instance.
	 *
	 * @property caption
	 * @type FL.SlideshowCaption
	 * @default null
	 */
	caption: null,

	/**
	 * A FL.SlideshowSocial instance.
	 *
	 * @property social
	 * @type FL.SlideshowSocial
	 * @default null
	 */
	social: null,

	/**
	 * A FL.SlideshowImage instance used to preload
	 * the next image.
	 *
	 * @property _nextImagePreloader
	 * @type FL.SlideshowImage
	 * @default null
	 * @protected
	 */
	_nextImagePreloader: null,

	/**
	 * An object that holds the initial nav settings
	 * when the mini nav has been enabled for a responsive layout.
	 *
	 * @property _initialNavSettings
	 * @type Object
	 * @default null
	 * @protected
	 */
	_initialNavSettings: null,

	/**
	 * Initializes the preloaders, nav buttons, fullscreen and captions.
	 *
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		// Preloader config
		var imageConfig = {
			loadGroup: 'main-preload',
			crop: this.get('crop'),
			position: this.get('position'),
			protect: this.get('protect'),
			upsize: this.get('upsize')
		};

		// Preloader
		this._nextImagePreloader = new Y.FL.SlideshowImage(imageConfig);

		// Nav buttons not needed for touch
		if(this._isMobile()) {
			this._removeNavButton('prevPage');
			this._removeNavButton('nextPage');
			this._removeNavButton('fullscreen');
		}

		// Fullscreen
		if(this._hasNavButton('fullscreen')) {
			if(Y.FL.SlideshowFullscreen.OS_SUPPORT) {
				this.plug(Y.FL.SlideshowFullscreen);
			}
			else {
				this._removeNavButton('fullscreen');
			}
		}
	},

	/**
	 * Calls the FL.SlideshowBase superclass renderUI method
	 * and renders the child widgets.
	 *
	 * @method renderUI
	 * @protected
	 */
	renderUI: function()
	{
		Y.FL.Slideshow.superclass.renderUI.apply(this, arguments);

		this._renderFrame();
		this._renderVerticalThumbs();
		this._renderNavAndThumbs();
		this._renderImageNav();
		this._renderMouseNav();
		this._renderCaption();
		this._renderSocial();
	},

	/**
	 * Calls the FL.SlideshowBase superclass bindUI method, binds
	 * _resizeChildWidgets to fire after the resize method inherited
	 * from FL.SlideshowBase, shows the loading image, binds overlay events
	 * and binds an event to load an image into the frame.
	 *
	 * @method bindUI
	 * @protected
	 */
	bindUI: function()
	{
		var ssBB 			= this.get('boundingBox'),
			frameBB         = this.frame.get('boundingBox'),
			navOverlay		= this.get('navOverlay'),
			navType			= this.get('navType'),
			nav 			= this._getNav(),
			clickAction     = this.get('clickAction');

		// Call superclass bindUI
		Y.FL.Slideshow.superclass.bindUI.apply(this, arguments);

		// Resize child widgets after the superclass resize method.
		Y.Do.after(this._resizeChildWidgets, this, 'resize');

		// Loading events
		this.on('albumLoadStart', this._albumLoadStart, this);
		this.on('albumLoadComplete', this._albumLoadComplete, this);
		this.on('imageLoadComplete', this._loadFrame, this);

		// Loading image
		if(this.get('loadingImageAlwaysEnabled')) {
			this.frame.on('transitionInit', Y.bind(this._showLoadingImageWithDelay, this));
			this.frame.on('transitionStart', Y.bind(this._hideLoadingImage, this));
		}

		// Overlay events
		if(this.get('overlayHideOnMousemove')) {
			if(nav && navOverlay) {
				this.frame.once('transitionComplete', nav.slideshowOverlay.hideWithTimer, nav.slideshowOverlay);
				ssBB.on('mousemove', Y.bind(this._toggleNav, this));
			}
			if(navType == 'buttons' || navType == 'thumbs' || navType == 'custom') {
				ssBB.on('mouseenter', Y.bind(this._checkOverlaysOnMouseenter, this));
				ssBB.on('mouseleave', Y.bind(this._hideAllOverlays, this));
			}
		}

		ssBB.delegate('click', Y.bind(this._overlayCloseClick, this), '.fl-slideshow-overlay-close');

		// Click action
		if(clickAction == 'gallery' || clickAction == 'url') {
            frameBB.delegate('click', Y.bind(this._frameClick, this), '.fl-slideshow-image-img');
		}
	},

	/**
	 * Calls the FL.SlideshowBase superclass syncUI method
	 * and makes the bounding box unselectable.
	 *
	 * @method syncUI
	 * @protected
	 */
	syncUI: function()
	{
		var bb = this.get('boundingBox');

		Y.FL.Slideshow.superclass.syncUI.apply(this, arguments);

		bb._node.onselectstart = function() { return false; };
		bb._node.unselectable = "on";
		bb._node.style.MozUserSelect = "none";

		if(this.get('clickAction') != 'none') {
            this.frame.get('boundingBox').addClass('fl-click-action-enabled');
		}
	},

	/**
	 * Checks to see if the current device is mobile.
	 *
	 * @since 1.9.3
	 * @access private
	 * @method _isMobile
	 * @return {Boolean}
	 */
	_isMobile: function()
	{
	    return /Mobile|Android|Silk\/|Kindle|BlackBerry|Opera Mini|Opera Mobi|webOS/i.test( navigator.userAgent );
	},

	/**
	 * Unload all slideshow images and pause
	 * the slideshow.
	 *
	 * @method unload
	 */
	unload: function()
	{
		this.pause();
		this.frame.unload();

		if(this.thumbs !== null) {
			this.thumbs.unload();
		}
	},

	/**
	 * @method _albumLoadStart
	 * @protected
	 */
	_albumLoadStart: function()
	{
		this._showLoadingImage();
	},

	/**
	 * @method _albumLoadComplete
	 * @protected
	 */
	_albumLoadComplete: function()
	{
		this.frame.once('transitionStart', Y.bind(this._hideLoadingImage, this));
	},

	/**
	 * Resizes all enabled child widgets.
	 *
	 * @method _resizeChildWidgets
	 * @protected
	 */
	_resizeChildWidgets: function()
	{
		var bb				= this.get('boundingBox'),
			cb				= this.get('contentBox'),
			imageNavEnabled = this.get('imageNavEnabled');

		this._renderNavAndThumbs();

		if(this.get('verticalThumbsOverlay')) {
			this._resizeFrame(cb.get('offsetWidth'), bb.get('offsetHeight'));
			this._resizeVerticalThumbs();
		}
		else {
			this._resizeVerticalThumbs();
			this._resizeFrame(cb.get('offsetWidth'), bb.get('offsetHeight'));
		}

		if(imageNavEnabled) {
			this._positionImageNav();
		}

		this._positionLoadingImage();
	},

	/**
	 * @method _renderVerticalThumbs
	 * @protected
	 */
	_renderVerticalThumbs: function()
	{
		var threshold 	= this.get('responsiveThreshold'),
			ssBB 		= this.get('boundingBox'),
			bbWidth 	= ssBB.get('offsetWidth'),
			vtBB;

		if(this.get('verticalThumbsEnabled') && bbWidth > threshold) {

			this.verticalThumbs = new Y.FL.SlideshowThumbs(this._getVerticalThumbsConfig());
			this.add(this.verticalThumbs);
			this.verticalThumbs.render(ssBB);

			vtBB = this.verticalThumbs.get('boundingBox');
			vtBB.addClass('fl-slideshow-vertical-thumbs');
			vtBB.setStyle(this.get('verticalThumbsPosition'), 0);
			ssBB.append(vtBB);

			if(this.get('verticalThumbsOverlay')) {
				this.verticalThumbs.plug(Y.FL.SlideshowOverlay, {
					hideDelay: this.get('overlayHideDelay'),
					hideStyle: 'left'
				});

				this.frame.get('boundingBox').append(vtBB);
				this.verticalThumbs.resize();
			}
			else {
				this.verticalThumbs.resize();
				this._adjustContentForVerticalThumbs();
			}

			this._bindVerticalThumbs();
		}
	},

	/**
	 * Prepares and returns the vertical thumbs config object.
	 *
	 * @method _getVerticalThumbsConfig
	 * @protected
	 * @returns Object
	 */
	_getVerticalThumbsConfig: function()
	{
		var attrs = this.getAttrs(),
			config = {
				columns: 				attrs.verticalThumbsColumns,
				rows: 					'auto',
				centerSinglePage: 		false,
				horizontalSpacing: 		attrs.verticalThumbsHorizontalSpacing,
				verticalSpacing:	 	attrs.verticalThumbsVerticalSpacing,
				spaceEvenly: 			attrs.verticalThumbsSpaceEvenly,
				rightNavEnabled: 		false,
				leftNavEnabled: 		false,
				topNavEnabled: 			attrs.verticalThumbsTopNavEnabled,
				topNavButtons:			attrs.verticalThumbsTopNavButtons,
				bottomNavEnabled: 		attrs.verticalThumbsBottomNavEnabled,
				bottomNavButtons:		attrs.verticalThumbsBottomNavButtons,
				pauseOnClick: 			attrs.verticalThumbsPauseOnClick,
				transition:				attrs.verticalThumbsTransition,
				transitionDirection: 	attrs.verticalThumbsTransitionDirection,
				transitionEasing:		attrs.verticalThumbsTransitionEasing,
				touchSupport:			true,
				imageConfig: {
					crop: 				attrs.verticalThumbsImageCrop,
					width: 				attrs.verticalThumbsImageWidth,
					height: 			attrs.verticalThumbsImageHeight
				}
			};

		return config;
	},

	_bindVerticalThumbs: function()
	{
		var ssBB 		= this.get('boundingBox'),
			hideOnMouse = this.get('overlayHideOnMousemove'),
			vtOverlay 	= this.get('verticalThumbsOverlay'),
			vt			= this.verticalThumbs;

		if(vt && hideOnMouse && vtOverlay) {
			this.frame.once('transitionComplete', vt.slideshowOverlay.hideWithTimer, vt.slideshowOverlay);
			ssBB.on('mousemove', Y.bind(this._toggleVerticalThumbs, this));
			ssBB.on('mouseenter', Y.bind(this._toggleVerticalThumbs, this));
		}
	},

	/**
	 * Resizes the vertical thumbs.
	 *
	 * @method _resizeVerticalThumbs
	 * @protected
	 */
	_resizeVerticalThumbs: function()
	{
		var vtEnabled = this.get('verticalThumbsEnabled'),
			vtOverlay,
			threshold,
			ssBB,
			bbWidth,
			navOverlay,
			navType,
			nav,
			navBB;

		if(vtEnabled) {
			vtOverlay	= this.get('verticalThumbsOverlay');
			threshold 	= this.get('responsiveThreshold');
			ssBB 		= this.get('boundingBox');
			bbWidth 	= ssBB.get('offsetWidth');
			navOverlay	= this.get('navOverlay');
			navType		= this.get('navType');
			nav			= this._getNav();

			if(this.verticalThumbs && bbWidth > threshold) {

				this.verticalThumbs.get('boundingBox').setStyle('display', 'block');
				this.verticalThumbs.resize();

				if(!vtOverlay) {
					this._adjustContentForVerticalThumbs();
				}
				else if(nav && navOverlay) {

					navBB = nav.get('boundingBox');

					if(navType == 'thumbs') {
						this._adjustOverlayForVerticalThumbs(navBB, true);
						this.thumbs.resize();
					}
					else {
						this._adjustOverlayForVerticalThumbs(navBB);
					}
				}
			}
			else if(!this.verticalThumbs && bbWidth > threshold) {
				this._renderVerticalThumbs();
			}
			else if(this.verticalThumbs && bbWidth <= threshold) {

				this.verticalThumbs.get('boundingBox').setStyle('display', 'none');

				if(!vtOverlay) {
					this.get('contentBox').setStyles({
						left: 'auto',
						position: 'relative',
						right: 'auto',
						width: 'auto'
					});
				}
			}
		}
	},

	/**
	 * Toggles the visibility of the vertical thumbs.
	 *
	 * @method _toggleVerticalThumbs
	 * @protected
	 */
	_toggleVerticalThumbs: function()
	{
		if(this.verticalThumbs) {
			if(this.verticalThumbs.slideshowOverlay._visible) {
				this.verticalThumbs.slideshowOverlay.hideWithTimer();
			}
			else {
				this.verticalThumbs.slideshowOverlay.show();
			}
		}
	},

	/**
	 * Adjusts the content position and width
	 * for the vertical thumbs.
	 *
	 * @method _adjustContentForVerticalThumbs
	 * @protected
	 */
	_adjustContentForVerticalThumbs: function()
	{
		var ssBB 	= this.get('boundingBox'),
			vtBB 	= this.verticalThumbs.get('boundingBox'),
			vtPos 	= this.get('verticalThumbsPosition'),
			ssCB 	= this.get('contentBox'),
			cbPos 	= vtPos == 'left' ? 'right' : 'left',
			cbWidth = ssBB.get('offsetWidth') - vtBB.get('offsetWidth');

		ssCB.setStyle('position', 'absolute');
		ssCB.setStyle(cbPos, 0);
		ssCB.setStyle('width', cbWidth);
	},

	/**
	 * Adjusts an overlay's position for the vertical
	 * thumbs when they are overlaid as well.
	 *
	 * @method _adjustOverlayForVerticalThumbs
	 * @protected
	 */
	_adjustOverlayForVerticalThumbs: function(node, useMargin)
	{
		var vtEnabled 	= this.get('verticalThumbsEnabled'),
			vtOverlay 	= this.get('verticalThumbsOverlay'),
			vtBB		= null,
			vtPos		= null,
			margin		= typeof useMargin === 'undefined' ? '' : 'margin-',
			vtWidth		= 0;

		if(this.verticalThumbs && vtEnabled && vtOverlay) {
			vtBB = this.verticalThumbs.get('boundingBox');
			vtWidth = vtBB.get('offsetWidth');
			vtPos = this.get('verticalThumbsPosition');

			if(vtPos == 'left') {
				node.setStyle(margin + 'left', vtWidth + 'px');
			}
			else {
				node.setStyle(margin + 'right', vtWidth + 'px');
			}
		}
	},

	/**
	 * Creates and renders a new instance of FL.SlideshowFrame
	 * used for the main image.
	 *
	 * @method _renderFrame
	 * @protected
	 */
	_renderFrame: function()
	{
		this.frame = new Y.FL.SlideshowFrame({
			imageConfig: {
				loadGroup: 'main',
				loadPriority: true,
				crop: this.get('crop'),
				cropHorizontalsOnly: this.get('cropHorizontalsOnly'),
				position: this.get('position'),
				protect: this.get('protect'),
				upsize: this.get('upsize'),
				showVideoButton: this.get('navOverlay')
			},
			touchSupport: this.get('touchSupport')
		});

		this.add(this.frame);
		this.frame.render(this.get('contentBox'));
		this.frame.get('boundingBox').addClass('fl-slideshow-main-image');
		this._setPlayingTimerEvent(this.frame, 'transitionComplete');
		this._loadingImageContainer = this.frame.get('contentBox');
	},

	/**
	 * Resizes the frame used for the main image.
	 *
	 * @method _resizeMainImage
	 * @param width {Number} The width to resize to.
	 * @param height {Number} The height to resize to.
	 * @protected
	 */
	_resizeFrame: function(width, height)
	{
		var navOverlay	= this.get('navOverlay'),
			nav 		= this._getNav();

		if(nav && !navOverlay) {
			height -= parseInt(nav.get('boundingBox').getComputedStyle('height'), 10);
		}

		this.frame.resize(width, height);
	},

	/**
	 * Called when the imageLoadComplete event fires.
	 * Loads an image into the frame and preloads the next image.
	 *
	 * @method _loadFrame
	 * @param e {Object} Event object containing the image info.
	 * @protected
	 */
	_loadFrame: function(e)
	{
		var activeIndex		= this.imageInfo.index,
			images			= this.albumInfo.images,
			nextIndex 		= activeIndex + 1 >= images.length ? 0 : activeIndex + 1,
			width			= this.frame.get('width'),
			height			= this.frame.get('height');

		// Load the frame.
		this.frame.load(e.imageInfo);

		// Remove main preload images from the load queue.
		Y.FL.SlideshowImageLoader.removeGroup('main-preload');

		// Preload the next image.
		this._nextImagePreloader.preload(images[nextIndex], width, height);
	},

	/**
	 * Fired when the frame img tag is clicked.
	 *
	 * @method _frameClick
	 * @protected
	 */
	_frameClick: function()
	{
        var clickAction    = this.get('clickAction'),
            clickActionUrl = this.get('clickActionUrl');

        if(clickAction == 'url') {
            window.location.href = clickActionUrl;
        }
        else if(clickAction == 'gallery') {
            window.location.href = this.imageInfo.link;
        }
	},

	/**
	 * Sets attributes to display a compact nav
	 * for responsive layouts.
	 *
	 * @method _initMiniNav
	 * @protected
	 */
	_initMiniNav: function()
	{
		var buttons = [];

		if(this._hasNavButton('prev')) {
			buttons.push('prev');
		}
		if(this._hasNavButton('thumbs') || this.get('navType') == 'thumbs') {
			buttons.push('thumbs');
		}
		if(this._hasNavButton('caption')) {
			buttons.push('caption');
		}
		if(this._hasNavButton('social')) {
			buttons.push('social');
		}
		if(this._hasNavButton('buy')) {
			buttons.push('buy');
		}
		if(this._hasNavButton('play')) {
			buttons.push('play');
		}
		if(this._hasNavButton('fullscreen') && !('ontouchstart' in window)) {
			buttons.push('fullscreen');
		}
		if(this._hasNavButton('next')) {
			buttons.push('next');
		}

		this._initialNavSettings = {
			buttons: this.get('navButtons'),
			buttonsLeft: this.get('navButtonsLeft'),
			buttonsRight: this.get('navButtonsRight'),
			type: this.get('navType')
		};

		this._set('navButtons', buttons);
		this._set('navButtonsLeft', []);
		this._set('navButtonsRight', []);
		this._set('navType', 'buttons');
	},

	/**
	 * Renders the nav and thumbs layout based on the
	 * current window size.
	 *
	 * @method _renderNavAndThumbs
	 * @protected
	 */
	_renderNavAndThumbs: function()
	{
		var navType		= this.get('navType'),
			renderNav	= false,
			bbWidth,
			threshold;

		if(navType == 'buttons' || navType == 'thumbs') {
			bbWidth		= this.get('boundingBox').get('offsetWidth');
			threshold	= this.get('responsiveThreshold');

			if(bbWidth <= threshold && this._initialNavSettings === null) {
				this._initMiniNav();
				renderNav = true;
			}
			else if(bbWidth > threshold && this._initialNavSettings !== null) {
				this._set('navButtons', this._initialNavSettings.buttons);
				this._set('navButtonsLeft', this._initialNavSettings.buttonsLeft);
				this._set('navButtonsRight', this._initialNavSettings.buttonsRight);
				this._set('navType', this._initialNavSettings.type);
				this._initialNavSettings = null;
				renderNav = true;
			}

			// Button nav
			if(renderNav || this.nav === null) {
				this._renderNav();
			}

			// Thumbs nav
			if(renderNav || this.thumbs === null) {
				this._renderThumbs();
			}
			else if(this._thumbsEnabled()) {
				this._resizeThumbs();
			}

			// Caption
			if(renderNav && this.caption !== null) {
				this._syncCaption();
			}

			// Social
			if(renderNav && this.social !== null) {
				this._syncSocial();
			}
		}
	},

	/**
	 * Creates and renders a new instance of FL.SlideshowNav
	 * used for the main nav.
	 *
	 * @method _renderNav
	 * @protected
	 */
	_renderNav: function()
	{
		var frameBB		= this.frame.get('boundingBox'),
			navBB 		= null,
			navOverlay	= this.get('navOverlay'),
			navPosition = this.get('navPosition');

		// Destroy old instances
		this._destroyNav();

		// Create a new instance
		if(this.get('navType') == 'buttons') {

			// Create the nav
			this.nav = new Y.FL.SlideshowNav({
				buttons: this.get('navButtons'),
				buttonsLeft: this.get('navButtonsLeft'),
				buttonsRight: this.get('navButtonsRight')
			});

			// Add to widget parent and render
			this.add(this.nav);
			this.nav.render(this.get('contentBox'));
			navBB = this.nav.get('boundingBox');

			// Plug overlay?
			if(navOverlay) {
				this.nav.plug(Y.FL.SlideshowOverlay, {
					hideDelay: this.get('overlayHideDelay')
				});

				navBB.setStyle('position', 'absolute');
				navBB.setStyle(navPosition, '0px');
			}

			// Insert
			if(navPosition == 'top') {
				frameBB.insert(navBB, 'before');
			}
			else {
				frameBB.insert(navBB, 'after');
			}

			// CSS class name
			navBB.addClass('fl-slideshow-main-nav');
		}
	},

	/**
	 * Destroy the current nav instance.
	 *
	 * @method _destroyNav
	 * @protected
	 */
	_destroyNav: function()
	{
        if(this.nav !== null) {
			if(this.nav.slideshowOverlay) {
				this.nav.slideshowOverlay.destroy();
			}

            this.nav.get('boundingBox').remove();
			this.remove(this.nav);
			try { this.nav.destroy(true); } catch(e) {}
			this.nav = null;
		}
	},

	/**
	 * Returns the nav object or null if navType is
	 * set to none or custom.
	 *
	 * @method _getNav
	 * @protected
	 */
	_getNav: function()
	{
		var navType = this.get('navType');

		if(navType == 'buttons') {
			return this.nav;
		}
		else if(navType == 'thumbs') {
			return this.thumbs;
		}
		else {
			return null;
		}
	},

	/**
	 * Toggles the visibility of the nav or thumbs nav
	 * if navOverlay is set to true.
	 *
	 * @method _toggleNav
	 * @protected
	 */
	_toggleNav: function()
	{
		var nav = this._getNav();

		if(nav.slideshowOverlay) {
			if(nav.slideshowOverlay._visible) {
				nav.slideshowOverlay.hideWithTimer();
			}
			else {
				nav.slideshowOverlay.show();
			}
		}
	},

	/**
	 * Creates and renders two instances of FL.SlideshowNav for the
	 * prev and next button that will be overlaid on the main image.
	 *
	 * @method _renderImageNav
	 * @protected
	 */
	_renderImageNav: function()
	{
		var ssBB;

		if(this.get('imageNavEnabled')) {
			if(this._isMobile()) {
				this._set('imageNavEnabled', false);
			}
			else {
				ssBB = this.get('boundingBox');

				this.imageNavLeft = new Y.FL.SlideshowNav({
				    buttons: ['prev'],
				    useFontIcons: false
				});

				this.imageNavRight = new Y.FL.SlideshowNav({
				    buttons: ['next'],
				    useFontIcons: false
				});

				this.add(this.imageNavLeft);
				this.add(this.imageNavRight);

				this.imageNavLeft.render(this.frame.get('boundingBox'));
				this.imageNavRight.render(this.frame.get('boundingBox'));

				this.imageNavLeft.plug(Y.FL.SlideshowOverlay, { hideDelay: this.get('overlayHideDelay') });
				this.imageNavRight.plug(Y.FL.SlideshowOverlay, { hideDelay: this.get('overlayHideDelay') });

				if(this.get('overlayHideOnMousemove')) {
					this.frame.once('transitionComplete', this.imageNavLeft.slideshowOverlay.hideWithTimer, this.imageNavLeft.slideshowOverlay);
					this.frame.once('transitionComplete', this.imageNavRight.slideshowOverlay.hideWithTimer, this.imageNavRight.slideshowOverlay);
					ssBB.on('mousemove', Y.bind(this._toggleImageNav, this));
					ssBB.on('mouseenter', Y.bind(this._toggleImageNav, this));
				}

				this.imageNavLeft.get('boundingBox').addClass('fl-slideshow-image-nav-left');
				this.imageNavRight.get('boundingBox').addClass('fl-slideshow-image-nav-right');
			}
		}
	},

	/**
	 * @method _positionImageNav
	 * @protected
	 */
	_positionImageNav: function()
	{
		var leftBB			= this.imageNavLeft.get('boundingBox'),
			rightBB			= this.imageNavRight.get('boundingBox'),
			imageNavHeight 	= leftBB.get('offsetHeight'),
			frameHeight 	= this.frame.get('boundingBox').get('offsetHeight'),
			top 			= frameHeight/2 - imageNavHeight/2,
			styles          = {
                top: top + 'px',
                display: 'block'
			};

		leftBB.setStyles(styles);
		rightBB.setStyles(styles);

		this._adjustOverlayForVerticalThumbs(leftBB);
		this._adjustOverlayForVerticalThumbs(rightBB);
	},

	/**
	 * Toggles the visibility of the image nav buttons.
	 *
	 * @method _toggleImageNav
	 * @protected
	 */
	_toggleImageNav: function()
	{
		if(this.imageNavLeft.slideshowOverlay._visible) {
			this.imageNavLeft.slideshowOverlay.hideWithTimer();
		}
		else {
			this.imageNavLeft.slideshowOverlay.show();
		}

		if(this.imageNavRight.slideshowOverlay._visible) {
			this.imageNavRight.slideshowOverlay.hideWithTimer();
		}
		else {
			this.imageNavRight.slideshowOverlay.show();
		}
	},

	/**
	 * @method _renderMouseNav
	 * @protected
	 */
	_renderMouseNav: function()
	{
		if(this.get('mouseNavEnabled') && !('ontouchstart' in window) && !window.navigator.msPointerEnabled) {
			this.plug(Y.FL.SlideshowMouseNav, {
				trigger: this.frame.get('boundingBox')
			});
		}
	},

	/**
	 * Checks whether the thumbs are enabled.
	 *
	 * @method _thumbsEnabled
	 * @protected
	 * @returns Boolean
	 */
	_thumbsEnabled: function()
	{
		var navType = this.get('navType');

		if(navType == 'thumbs') {
			return true;
		}
		if((navType == 'buttons' || navType == 'custom') && this._hasNavButton('thumbs')) {
			return true;
		}
		else {
			return false;
		}
	},

	/**
	 * Creates and renders a new instance of FL.SlideshowThumbs.
	 *
	 * @method _renderThumbs
	 * @protected
	 */
	_renderThumbs: function()
	{
		var frameBB, navOverlay, navPosition, navType;

		// Destroy old instances
		this._destroyThumbs();

		// Create a new instance
		if(this._thumbsEnabled()) {
			frameBB			= this.frame.get('boundingBox');
			navOverlay		= this.get('navOverlay');
			navPosition 	= this.get('navPosition');
			navType			= this.get('navType');

			// Create the thumbs
			this.thumbs = new Y.FL.SlideshowThumbs(this._getThumbsConfig());

			// This breaks sometimes on SM Next. Try/catch bandaid for now.
			try { this.add(this.thumbs); } catch(e) {}

			// Overlay setup
			if(navType == 'buttons' || navType == 'custom') {
				this.thumbs.plug(Y.FL.SlideshowOverlay, {
					hideDelay: this.get('overlayHideDelay'),
					hideStyle: 'left',
					visible: false
				});
			}
			else if(navType == 'thumbs' && navOverlay) {
				this.thumbs.plug(Y.FL.SlideshowOverlay, {
					hideDelay: this.get('overlayHideDelay'),
					hideStyle: 'left'
				});
			}

			// Insert
			this.thumbs.render(this.get('contentBox'));

			if(navPosition == 'top') {
				frameBB.insert(this.thumbs.get('boundingBox'), 'before');
			}
			else {
				frameBB.insert(this.thumbs.get('boundingBox'), 'after');
			}

			// Hide overlay thumbs on click
			if(this.get('thumbsHideOnClick') && navType != 'thumbs') {
				this.thumbs.on('imageClick', Y.bind(this._hideThumbsOnImageClick, this));
			}

			this._syncThumbs();
		}
	},

	/**
	 * Destroy the current thumbs instance.
	 *
	 * @method _destroyThumbs
	 * @protected
	 */
	_destroyThumbs: function()
	{
        if(this.thumbs !== null) {
			if(this.thumbs.slideshowOverlay) {
				this.thumbs.slideshowOverlay.destroy();
			}

            this.thumbs.get('boundingBox').remove();
			this.remove(this.thumbs);
			try { this.thumbs.destroy(true); } catch(e) {}
			this.thumbs = null;
		}
	},

	/**
	 * Syncs the thumbs UI styles.
	 *
	 * @method _syncThumbs
	 * @protected
	 */
	_syncThumbs: function()
	{
		var thumbsBB			= this.thumbs.get('boundingBox'),
			navOverlay			= this.get('navOverlay'),
			navPosition 		= this.get('navPosition'),
			navType				= this.get('navType'),
			paddingType			= 'padding' + navPosition.charAt(0).toUpperCase() + navPosition.slice(1),
			navHeight			= 0;

		if(navType == 'buttons') {
			navHeight = parseInt(this.nav.get('boundingBox').getComputedStyle('height'), 10);
			thumbsBB.setStyle('position', 'absolute');

			if(navOverlay) {
				thumbsBB.setStyle(paddingType, navHeight + 'px');
				thumbsBB.setStyle(navPosition, '0px');
			}
			else {
				thumbsBB.setStyle(navPosition, navHeight + 'px');
			}
		}
		if(navType == 'custom' || (navType == 'thumbs' && navOverlay)) {
			thumbsBB.setStyle('position', 'absolute');
			thumbsBB.setStyle(navPosition, '0px');
		}

		this.thumbs.resize();
	},

	/**
	 * Prepares and returns the thumbs config object.
	 *
	 * @method _getThumbsConfig
	 * @protected
	 * @returns Object
	 */
	_getThumbsConfig: function()
	{
		var attrs 					= this.getAttrs(),
			navType					= this.get('navType'),
			imageConfig 			= {
				crop: attrs.thumbsImageCrop,
				width: attrs.thumbsImageWidth,
				height: attrs.thumbsImageHeight
			},
			config 					= {
				columns: 				'auto',
				rows: 					1,
				horizontalSpacing: 		attrs.thumbsHorizontalSpacing,
				verticalSpacing:	 	attrs.thumbsVerticalSpacing,
				spaceEvenly: 			attrs.thumbsSpaceEvenly,
				centerSinglePage:	 	attrs.thumbsCenterSinglePage,
				pauseOnClick: 			attrs.thumbsPauseOnClick,
				transition:				attrs.thumbsTransition,
				transitionDirection: 	attrs.thumbsTransitionDirection,
				transitionEasing:		attrs.thumbsTransitionEasing,
				leftNavButtons: 		attrs.navButtonsLeft,
				rightNavButtons: 		attrs.navButtonsRight,
				imageConfig:			imageConfig,
				touchSupport:			true
			};

			if(navType == 'buttons' || navType == 'custom') {
				if('ontouchstart' in window) {
					config.leftNavEnabled = false;
					config.rightNavEnabled = false;
				}
				else {
					config.centerSinglePage = false;
					config.leftNavButtons 	= ['prevPage'];
					config.rightNavButtons 	= ['nextPage'];
				}
			}

			return config;
	},

	/**
	 * Resizes the thumbs.
	 *
	 * @method _resizeThumbs
	 * @protected
	 */
	_resizeThumbs: function()
	{
		if(this.thumbs) {
			this.thumbs.resize();
		}
	},

	/**
	 * Shows or hides the thumbs.
	 *
	 * @method _toggleThumbs
	 * @protected
	 */
	_toggleThumbs: function()
	{
		this._toggleOverlay(this.thumbs.slideshowOverlay);
	},

	/**
	 * Hides the thumbs when a thumb image is clicked.
	 *
	 * @method _hideThumbsOnImageClick
	 * @protected
	 */
	_hideThumbsOnImageClick: function()
	{
		if(this.thumbs.slideshowOverlay) {
			this.thumbs.slideshowOverlay._focus = false;
			this.thumbs.slideshowOverlay.enable();
			this.thumbs.slideshowOverlay.hide();

			if(this.nav && this.nav.slideshowOverlay) {
				this.nav.slideshowOverlay.enable();
			}
		}
	},

	/**
	 * Creates and renders a new instance of FL.SlideshowCaption.
	 *
	 * @method _renderCaption
	 * @protected
	 */
	_renderCaption: function()
	{
		if(this._hasNavButton('caption')) {

			this.caption = new Y.FL.SlideshowCaption({
				lessLinkText: this.get('captionLessLinkText'),
				moreLinkText: this.get('captionMoreLinkText'),
				textLength: this.get('captionTextLength'),
				stripTags: this.get('captionStripTags')
			});

			this.add(this.caption);

			this.caption.plug(Y.FL.SlideshowOverlay, {
				hideDelay: this.get('overlayHideDelay'),
				visible: false,
				closeButton: true
			});

			this._syncCaption();
		}
	},

	/**
	 * Syncs the caption UI styles.
	 *
	 * @method _syncCaption
	 * @protected
	 */
	_syncCaption: function()
	{
		var captionBB		= this.caption.get('boundingBox'),
			navOverlay		= this.get('navOverlay'),
			navPosition 	= this.get('navPosition'),
			nav 			= this._getNav(),
			paddingType		= 'padding' + navPosition.charAt(0).toUpperCase() + navPosition.slice(1),
			navHeight		= 0;

		captionBB.setStyle('position', 'absolute');

		if(nav) {
			navHeight = parseInt(nav.get('boundingBox').getComputedStyle('height'), 10);
		}

		if(nav && navOverlay) {
			captionBB.setStyle(paddingType, navHeight + 'px');
			captionBB.setStyle(navPosition, '0px');
		}
		else {
			captionBB.setStyle(navPosition, navHeight + 'px');
		}
	},

	/**
	 * Shows or hides the caption.
	 *
	 * @method _toggleCaption
	 * @protected
	 */
	_toggleCaption: function()
	{
		this._toggleOverlay(this.caption.slideshowOverlay);
	},

	/**
	 * Creates and renders a new instance of FL.SlideshowSocial.
	 *
	 * @method _renderSocial
	 * @protected
	 */
	_renderSocial: function()
	{
		if(this._hasNavButton('social')) {
			this.social = new Y.FL.SlideshowSocial();
			this.add(this.social);

			this.social.plug(Y.FL.SlideshowOverlay, {
				hideDelay: this.get('overlayHideDelay'),
				visible: false,
				closeButton: true
			});

			this._syncSocial();
		}
	},

	/**
	 * Syncs the social UI styles.
	 *
	 * @method _syncSocial
	 * @protected
	 */
	_syncSocial: function()
	{
		var socialBB		= this.social.get('boundingBox'),
			navOverlay		= this.get('navOverlay'),
			navPosition 	= this.get('navPosition'),
			nav 			= this._getNav(),
			paddingType		= 'padding' + navPosition.charAt(0).toUpperCase() + navPosition.slice(1),
			navHeight		= 0;

		socialBB.setStyle('position', 'absolute');

		if(nav) {
			navHeight = parseInt(nav.get('boundingBox').getComputedStyle('height'), 10);
		}
		if(nav && navOverlay) {
			socialBB.setStyle(paddingType, navHeight + 'px');
			socialBB.setStyle(navPosition, '0px');
		}
		else {
			socialBB.setStyle(navPosition, navHeight + 'px');
		}
	},

	/**
	 * Shows or hides the social buttons.
	 *
	 * @method _toggleSocial
	 * @protected
	 */
	_toggleSocial: function()
	{
		this._toggleOverlay(this.social.slideshowOverlay);
		// Refresh iframe to fix tweet button issue visibility inside hidden elements
		var iFrame = jQuery('.fl-slideshow-social-content').find('iframe');
		iFrame.remove();
		jQuery('.fl-slideshow-social-content').prepend(iFrame);
	},

	/**
	 * Shows or hides an overlaid widget based
	 * on its current visibility.
	 *
	 * @method _toggleOverlay
	 * @param overlay {Object} The overlay to toggle.
	 * @protected
	 */
	_toggleOverlay: function(overlay)
	{
		var navType	= this.get('navType'),
			nav 	= this._getNav();

		if(overlay._visible) {
			if(nav && nav.slideshowOverlay) {
				nav.slideshowOverlay.enable();
			}
			overlay.enable();
			overlay.hide();
		}
		else {
			if(nav && nav.slideshowOverlay) {
				nav.slideshowOverlay.disable();
			}
			overlay.show();
			overlay.disable();
		}

		if(this.thumbs && navType != 'thumbs' && this.thumbs.slideshowOverlay !== overlay) {
			this.thumbs.slideshowOverlay.enable();
			this.thumbs.slideshowOverlay.hide();
		}
		if(this.caption && this.caption.slideshowOverlay !== overlay) {
			this.caption.slideshowOverlay.enable();
			this.caption.slideshowOverlay.hide();
		}
		if(this.social && this.social.slideshowOverlay !== overlay) {
			this.social.slideshowOverlay.enable();
			this.social.slideshowOverlay.hide();
		}
	},

	/**
	 * Called when an overlay's close button is clicked.
	 *
	 * @method _overlayCloseClick
	 * @protected
	 */
	_overlayCloseClick: function()
	{
		if(this.nav && this.nav.slideshowOverlay) {
			this.nav.slideshowOverlay.enable();
		}
		if(this.thumbs && this.thumbs.slideshowOverlay) {
			this.thumbs.slideshowOverlay.enable();
		}
		if(this.caption) {
			this.caption.slideshowOverlay.enable();
		}
		if(this.social) {
			this.social.slideshowOverlay.enable();
		}
		if(this.imageNavLeft) {
			this.imageNavLeft.slideshowOverlay.enable();
			this.imageNavRight.slideshowOverlay.enable();
		}
	},

	/**
	 * Hides all overlaid widgets.
	 *
	 * @method _hideAllOverlays
	 * @protected
	 */
	_hideAllOverlays: function()
	{
		if(this.nav && this.nav.slideshowOverlay && this.nav.slideshowOverlay._visible) {
			this.nav.slideshowOverlay.enable();
			this.nav.slideshowOverlay.hideWithTimer();
		}
		if(this.thumbs && this.thumbs.slideshowOverlay && this.thumbs.slideshowOverlay._visible) {
			this.thumbs.slideshowOverlay.enable();
			this.thumbs.slideshowOverlay.hideWithTimer();
		}
		if(this.caption && this.caption.slideshowOverlay._visible) {
			this.caption.slideshowOverlay.enable();
			this.caption.slideshowOverlay.hideWithTimer();
		}
		if(this.social && this.social.slideshowOverlay._visible) {
			this.social.slideshowOverlay.enable();
			this.social.slideshowOverlay.hideWithTimer();
		}
		if(this.imageNavLeft) {
			this.imageNavLeft.slideshowOverlay.enable();
			this.imageNavLeft.slideshowOverlay.hideWithTimer();
			this.imageNavRight.slideshowOverlay.enable();
			this.imageNavRight.slideshowOverlay.hideWithTimer();
		}
	},

	/**
	 * Checks if overlays are still visible when the mouse enters
	 * the bounding box. If they are, overlay functionality is disabled
	 * until the overlays are closed by a button or the mouse leaves
	 * the bounding box. If only the nav overlay is visible, this
	 * function does nothing.
	 *
	 * @method _checkOverlaysOnMouseenter
	 * @protected
	 */
	_checkOverlaysOnMouseenter: function()
	{
		var navType			= this.get('navType'),
			navOverlay		= this.get('navOverlay'),
			nav 			= this._getNav(),
			overlayVisible 	= false;

		if(this.thumbs && navType != 'thumbs' && this.thumbs.slideshowOverlay._visible) {
			overlayVisible = true;
			this.thumbs.slideshowOverlay.disable();
		}
		else if(this.caption && this.caption.slideshowOverlay._visible) {
			overlayVisible = true;
			this.caption.slideshowOverlay.disable();
		}
		else if(this.social && this.social.slideshowOverlay._visible) {
			overlayVisible = true;
			this.social.slideshowOverlay.disable();
		}

		if(nav && overlayVisible && navOverlay) {
			nav.slideshowOverlay.disable();
		}
	},

	/**
	 * Checks whether a nav button is set or not.
	 *
	 * @method _hasNavButton
	 * @protected
	 * @param button {String} The button to look for.
	 * @returns Boolean
	 */
	_hasNavButton: function(button)
	{
		var navType = this.get('navType');

		if(navType == 'buttons' || navType == 'thumbs' || navType == 'custom') {
			if(Y.Array.indexOf(this.get('navButtons'), button) > -1) {
				return true;
			}
			else if(Y.Array.indexOf(this.get('navButtonsLeft'), button) > -1) {
				return true;
			}
			else if(Y.Array.indexOf(this.get('navButtonsRight'), button) > -1) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	},

	/**
	 * @method _removeNavButton
	 * @param button {String} The name of the button to remove.
	 * @protected
	 */
	_removeNavButton: function(button)
	{
		var buttons 			= this.get('navButtons'),
			buttonsLeft 		= this.get('navButtonsLeft'),
			buttonsRight 		= this.get('navButtonsRight'),
			vtTopNavButtons 	= this.get('verticalThumbsTopNavButtons'),
			vtBottomNavButtons 	= this.get('verticalThumbsBottomNavButtons');

		if(Y.Array.indexOf(buttons, button) > -1) {
			buttons.splice(Y.Array.indexOf(buttons, button), 1);
		}
		if(Y.Array.indexOf(buttonsLeft, button) > -1) {
			buttonsLeft.splice(Y.Array.indexOf(buttonsLeft, button), 1);
		}
		if(Y.Array.indexOf(buttonsRight, button) > -1) {
			buttonsRight.splice(Y.Array.indexOf(buttonsRight, button), 1);
		}
		if(Y.Array.indexOf(vtTopNavButtons, button) > -1) {
			vtTopNavButtons.splice(Y.Array.indexOf(vtTopNavButtons, button), 1);
		}
		if(Y.Array.indexOf(vtBottomNavButtons, button) > -1) {
			vtBottomNavButtons.splice(Y.Array.indexOf(vtBottomNavButtons, button), 1);
		}

		this._set('navButtons', buttons);
		this._set('navButtonsLeft', buttonsLeft);
		this._set('navButtonsRight', buttonsRight);
		this._set('verticalThumbsTopNavButtons', vtTopNavButtons);
		this._set('verticalThumbsBottomNavButtons', vtBottomNavButtons);
	}

}, {

	/**
	 * Custom CSS class name for the widget.
	 *
	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * What should happen when the main image is clicked.
		 * Options are none, gallery and url. If url is chosen,
		 * clickActionUrl must be set.
		 *
		 * @attribute clickAction
		 * @type String
		 * @default none
		 */
		clickAction: {
			value: 'none'
		},

		/**
		 * The redirect url to use when clickAction is set to url.
		 *
		 * @attribute clickActionUrl
		 * @type String
		 * @default none
		 */
		clickActionUrl: {
			value: ''
		},

		/**
		 * Whether to crop the main image.
		 *
		 * @attribute crop
		 * @type Boolean
		 * @default false
		 */
		crop: {
			value: false
		},

		/**
		 * Whether to only crop horizontal images or not.
		 *
		 * @attribute cropHorizontalsOnly
		 * @type Boolean
		 * @default false
		 */
		cropHorizontalsOnly: {
			value: false
		},

		/**
		 * Whether to always use the loading image between images
		 * or to only use it between albums.
		 *
		 * @attribute loadingImageAlwaysEnabled
		 * @type Boolean
		 * @default true
		 */
		loadingImageAlwaysEnabled: {
			value: true
		},

		/**
		 * The x and y position of the main image
		 * within the bounding box.
		 *
		 * @attribute position
		 * @type String
		 * @default center center
		 */
		position: {
			value: 'center center'
		},

		/**
		 * Whether to right click protect the main image.
		 *
		 * @attribute protect
		 * @type Boolean
		 * @default true
		 */
		protect: {
			value: true
		},

		/**
		 * Whether to resize the main image past
		 * its original width and height.
		 *
		 * @attribute upsize
		 * @type Boolean
		 * @default true
		 */
        upsize: {
			value: true
		},

		/**
		 * The type of transition to use. Possible values are
		 * none, fade, slideHorizontal and slideVertical. The
		 * value can also be a common seperated string of transitions
		 * that will be randomly chosen for each image.
		 *
		 * @attribute transition
		 * @type String
		 * @default fade
		 */
		transition: {
			value: 'fade'
		},

		/**
		 * The duration of the transition, measured in seconds.
		 *
		 * @attribute transitionDuration
		 * @type Number
		 * @default 1
		 */
		transitionDuration: {
			value: 1
		},

		/**
		 * The type of transition easing to use.
		 *
		 * @attribute transitionEasing
		 * @type String
		 * @default ease-out
		 */
		transitionEasing: {
			value: 'ease-out'
		},

		/**
		 * The amount of zoom to use for the Ken Burns effect.
		 *
		 * @attribute kenBurnsZoom
		 * @type Number
		 * @default 1.2
		 */
		kenBurnsZoom: {
            value: 1.2
		},

		/**
		 * The type of navigation to use. Possible values are
		 * buttons, thumbs, custon and none.
		 *
		 * @attribute navType
		 * @type String
		 * @default none
		 */
		navType: {
			value: 'none'
		},

		/**
		 * The position of the main nav. Possible values are top and bottom.
		 *
		 * @attribute navPosition
		 * @type String
		 * @default bottom
		 */
		navPosition: {
			value: 'bottom'
		},

		/**
		 * Whether to overlay the nav on top of the main image.
		 *
		 * @attribute navOverlay
		 * @type Boolean
		 * @default false
		 */
		navOverlay: {
			value: false
		},

		/**
		 * An array of button names used to render the main nav's buttons.
		 *
		 * @attribute navButtons
		 * @type Array
		 * @default []
		 */
		navButtons: {
			value: []
		},

		/**
		 * An array of button names used to render the main nav's left buttons.
		 *
		 * @attribute navButtonsLeft
		 * @type Array
		 * @default []
		 */
		navButtonsLeft: {
			value: []
		},

		/**
		 * An array of button names used to render the main nav's right buttons.
		 *
		 * @attribute navButtonsRight
		 * @type Array
		 * @default []
		 */
		navButtonsRight: {
			value: []
		},

		/**
		 * Whether to hide the overlays when the mouse moves or not.
		 *
		 * @attribute overlayHideOnMousemove
		 * @type String
		 * @default mouseover
		 */
		overlayHideOnMousemove: {
			value: true
		},

		/**
		 * How long to wait before hiding the overlays.
		 * Measured in milliseconds.
		 *
		 * @attribute overlayHideDelay
		 * @type Number
		 * @default false
		 */
		overlayHideDelay: {
			value: 3000
		},

		/**
		 * Whether to use the image nav or not. If true, a prev
		 * and next button will be overlaid on the main image.
		 *
		 * @attribute imageNavEnabled
		 * @type Boolean
		 * @default false
		 */
		imageNavEnabled: {
			value: false
		},

		/**
		 * Whether to use the mouse nav or not. If true, the cursor
		 * will turn into a prev or next button when over the slideshow.
		 *
		 * @attribute mouseNavEnabled
		 * @type Boolean
		 * @default false
		 */
		mouseNavEnabled: {
			value: false
		},

		/**
		 * Whether to hide the thumbs when clicking on a thumbnail
		 * image or not. Thumbs always hide navType is set to buttons.
		 *
		 * @attribute thumbsHideOnClick
		 * @type Boolean
		 * @default false
		 */
		thumbsHideOnClick: {
			value: true
		},

		/**
		 * The horizontal spacing between thumbs.
		 *
		 * @attribute thumbsHorizontalSpacing
		 * @type Number
		 * @default 15
		 */
		thumbsHorizontalSpacing: {
			value: 15
		},

		/**
		 * The vertical spacing between thumbs.
		 *
		 * @attribute thumbsVerticalSpacing
		 * @type Number
		 * @default 15
		 */
		thumbsVerticalSpacing: {
			value: 15
		},

		/**
		 * Whether to space the thumbs evenly within a page.
		 *
		 * @attribute thumbsSpaceEvenly
		 * @type Boolean
		 * @default true
		 */
		thumbsSpaceEvenly: {
			value: true
		},

		/**
		 * Whether to center single pages of thumbs.
		 *
		 * @attribute thumbsCenterSinglePage
		 * @type Boolean
		 * @default false
		 */
		thumbsCenterSinglePage: {
			value: true
		},

		/**
		 * Whether to pause the slideshow when a thumb is clicked.
		 *
		 * @attribute thumbsPauseOnClick
		 * @type Boolean
		 * @default false
		 */
		thumbsPauseOnClick: {
			value: false
		},

		/**
		 * The type of transition to use between pages of thumbs.
		 *
		 * @attribute thumbsTransition
		 * @type String
		 * @default slideHorizontal
		 */
		thumbsTransition: {
			value: 'slideHorizontal'
		},

		/**
		 * The duration of the transition between pages of thumbs.
		 *
		 * @attribute thumbsTransitionDuration
		 * @type Number
		 * @default 0.8
		 */
		thumbsTransitionDuration: {
			value: 0.8
		},

		/**
		 * The type of transition easing to use between pages of thumbs.
		 *
		 * @attribute thumbsTransitionEasing
		 * @type String
		 * @default ease-out
		 */
		thumbsTransitionEasing: {
			value: 'ease-out'
		},

		/**
		 * Whether to crop the thumbnails.
		 *
		 * @attribute thumbsImageCrop
		 * @type Boolean
		 * @default true
		 */
		thumbsImageCrop: {
			value: true
		},

		/**
		 * The width of each thumbnail.
		 *
		 * @attribute thumbsImageWidth
		 * @type Number
		 * @default 50
		 */
		thumbsImageWidth: {
			value: 50
		},

		/**
		 * The height of each thumbnail.
		 *
		 * @attribute thumbsImageHeight
		 * @type Number
		 * @default 50
		 */
		thumbsImageHeight: {
			value: 50
		},

		/**
		 * The text to use for the "read less" toggle link.
		 *
		 * @attribute captionLessLinkText
		 * @type String
		 * @default Read Less
		 */
		captionLessLinkText: {
			value: 'Read Less'
		},

		/**
		 * The text to use for the "read more" toggle link.
		 *
		 * @attribute captionMoreLinkText
		 * @type String
		 * @default Read More
		 */
		captionMoreLinkText: {
			value: 'Read More'
		},

		/**
		 * The length of the caption to show. If greater than -1,
		 * the text will be truncated and a read more link will
		 * be displayed. If set to -1, the entire caption will be shown.
		 *
		 * @attribute captionTextLength
		 * @type Number
		 * @default 200
		 */
		captionTextLength: {
			value: 200
		},

		/**
		 * Whether to strip out HTML tags in the caption
		 * text or not.
		 *
		 * @attribute captionStripTags
		 * @type Boolean
		 * @default false
		 */
		captionStripTags: {
			value: false
		},

		/**
		 * Whether to use the vertical thumbs or not.
		 *
		 * @attribute verticalThumbsEnabled
		 * @type Boolean
		 * @default false
		 */
		verticalThumbsEnabled: {
			value: false
		},

		/**
		 * Position of the vertical thumbs. Possible values
		 * are either left or right.
		 *
		 * @attribute verticalThumbsPosition
		 * @type String
		 * @default left
		 */
		verticalThumbsPosition: {
			value: 'left'
		},

		/**
		 * Whether to overlay the vertical thumbs
		 * on the main image or not.
		 *
		 * @attribute verticalThumbsOverlay
		 * @type Boolean
		 * @default false
		 */
		verticalThumbsOverlay: {
			value: false
		},

		/**
		 * The number of columns for the vertical thumbs.
		 *
		 * @attribute verticalThumbsColumns
		 * @type Number
		 * @default 1
		 */
		verticalThumbsColumns: {
			value: 1
		},

		/**
		 * Whether to use the vertical thumbs top nav or not.
		 *
		 * @attribute verticalThumbsTopNavEnabled
		 * @type Boolean
		 * @default false
		 */
		verticalThumbsTopNavEnabled: {
			value: false
		},

		/**
		 * An array of button names used to render
		 * the vertical thumbs top nav buttons.
		 *
		 * @attribute verticalThumbsTopNavButtons
		 * @type Array
		 * @default prevPage, nextPage
		 */
		verticalThumbsTopNavButtons: {
			value: ['prevPage', 'nextPage']
		},

		/**
		 * Whether to use the vertical thumbs bottom nav or not.
		 *
		 * @attribute verticalThumbsBottomNavEnabled
		 * @type Boolean
		 * @default false
		 */
		verticalThumbsBottomNavEnabled: {
			value: true
		},

		/**
		 * An array of button names used to render
		 * the vertical thumbs top nav buttons.
		 *
		 * @attribute verticalThumbsBottomNavButtons
		 * @type Array
		 * @default prevPage, nextPage
		 */
		verticalThumbsBottomNavButtons: {
			value: ['prevPage', 'nextPage']
		},

		/**
		 * The horizontal spacing between vertical thumbs.
		 *
		 * @attribute verticalThumbsHorizontalSpacing
		 * @type Number
		 * @default 15
		 */
		verticalThumbsHorizontalSpacing: {
			value: 15
		},

		/**
		 * The vertical spacing between vertical thumbs.
		 *
		 * @attribute verticalThumbsVerticalSpacing
		 * @type Number
		 * @default 15
		 */
		verticalThumbsVerticalSpacing: {
			value: 15
		},

		/**
		 * Whether to space the vertical thumbs evenly within a page.
		 *
		 * @attribute verticalThumbsSpaceEvenly
		 * @type Boolean
		 * @default false
		 */
		verticalThumbsSpaceEvenly: {
			value: false
		},

		/**
		 * Whether to pause the slideshow when a vertical thumb is clicked.
		 *
		 * @attribute verticalThumbsPauseOnClick
		 * @type Boolean
		 * @default false
		 */
		verticalThumbsPauseOnClick: {
			value: false
		},

		/**
		 * Whether to crop the vertical thumbs or not.
		 *
		 * @attribute verticalThumbsImageCrop
		 * @type Boolean
		 * @default true
		 */
		verticalThumbsImageCrop: {
			value: true
		},

		/**
		 * The width of each vertical thumbnail.
		 *
		 * @attribute verticalThumbsImageWidth
		 * @type Number
		 * @default 75
		 */
		verticalThumbsImageWidth: {
			value: 75
		},

		/**
		 * The height of each vertical thumbnail.
		 *
		 * @attribute verticalThumbsImageHeight
		 * @type Number
		 * @default 75
		 */
		verticalThumbsImageHeight: {
			value: 75
		},

		/**
		 * The type of transition to use between pages of vertical thumbs.
		 *
		 * @attribute verticalThumbsTransition
		 * @type String
		 * @default slideVertical
		 */
		verticalThumbsTransition: {
			value: 'slideVertical'
		},

		/**
		 * The duration of the transition between pages of vertical thumbs.
		 *
		 * @attribute verticalThumbsTransitionDuration
		 * @type Number
		 * @default 0.8
		 */
		verticalThumbsTransitionDuration: {
			value: 0.8
		},

		/**
		 * The type of transition easing to use between pages of vertical thumbs.
		 *
		 * @attribute verticalThumbsTransitionEasing
		 * @type String
		 * @default ease-out
		 */
		verticalThumbsTransitionEasing: {
			value: 'ease-out'
		},

		/**
		 * Whether to use the Facebook like button or not.
		 *
		 * @attribute likeButtonEnabled
		 * @type Boolean
		 * @default true
		 */
		likeButtonEnabled: {
			value: true
		},

		/**
		 * Whether to use the Pinterest button or not.
		 *
		 * @attribute pinterestButtonEnabled
		 * @type Boolean
		 * @default true
		 */
		pinterestButtonEnabled: {
			value: true
		},

		/**
		 * Whether to use the Tweet button or not.
		 *
		 * @attribute tweetButtonEnabled
		 * @type Boolean
		 * @default true
		 */
		tweetButtonEnabled: {
			value: true
		},

		/**
		 * Whether to use touch gestures, when available,
		 * to transition between images or not.
		 *
		 * @attribute touchSupport
		 * @type Boolean
		 * @default true
		 */
		touchSupport: {
			value: true
		}
	}
});


}, '2.0.0' ,{requires:['anim', 'event-mouseenter', 'plugin', 'transition', 'fl-event-move', 'fl-slideshow-css', 'fl-slideshow-base', 'fl-utils', 'sm-fonticon']});


YUI.add('fl-slideshow-album-loader', function(Y) {

/**
 * @module fl-slideshow-album-loader
 */

/**
 * Loads slideshow albums using a provided source object.
 *
 * @namespace FL
 * @class SlideshowAlbumLoader
 * @constructor
 * @param config {Object} Configuration object
 * @extends Base
 */
Y.namespace('FL').SlideshowAlbumLoader = Y.Base.create('fl-slideshow-album-loader', Y.Base, [], {

	/**
	* The source object used for loading.
	*
	* @property _source
	* @type Object
	* @default null
	* @protected
	*/
	_source: null,

	/**
	 * Loads slideshow album data using the provided source object.
	 *
	 * @method load
	 * @param source {Object} The source object to use for loading.
	 */
	load: function(source)
	{
		this._source = source;

		/**
		 * Fires before a new load request is made.
		 *
		 * @event start
		 */
		this.fire('start');

		this[Y.FL.SlideshowAlbumLoader.TYPES[source.type]].call(this);
	},

	 /**
	 * Called when a source type completes loading
	 * and fires the complete event.
	 *
	 * @method _loadComplete
	 * @param o {Object} Passed to complete event subscribers.
	 * @protected
	 */
	_loadComplete: function(o)
	{
		o = this._randomize(o);

		/**
		 * Fires after a new load request is made.
		 *
		 * @event complete
		 */
		this.fire('complete', o);
	},

	/**
	 * Randomizes images in an album.
	 *
	 * @method _randomize
	 * @param album {Object} The album to randomize.
	 * @protected
	 */
	_randomize: function(o)
	{
		var i;

		if(this.get('randomize')) {
			o.albumInfo.images.sort(function() { return 0.5 - Math.random(); });

			for(i = 0; i < o.albumInfo.images.length; i++) {
				o.albumInfo.images[i].index = i;
			}
		}

		return o;
	},

	/**
	 * Loads slideshow album data from SmugMug.
	 *
	 * @method _loadSmugMug
	 * @protected
	 */
	_loadSmugMug: function()
	{
		var sm = new Y.FL.SmugMugAPI();

		sm.on('complete', this._loadSmugMugSuccess, this);
		sm.addParam('method', 'smugmug.images.get');
		sm.addParam('AlbumID', this._source.id);
		sm.addParam('AlbumKey', this._source.key);
		sm.addParam('Extras', 'Caption,Format,FileName');

		// Gallery password
		if(this._source.password) {
			sm.addParam('Password', this._source.password);
		}

		// Site-wide password
		if(this._source.sp) {
			sm.addParam('SitePassword', this._source.sp);
		}

		sm.request();
	},

	/**
	 * Processes slideshow album data loaded from SmugMug.
	 *
	 * @method _loadSmugMugSuccess
	 * @param e {Object} The custom event object passed to this function.
	 * @protected
	 */
	_loadSmugMugSuccess: function(e)
	{
		var images 		= e.Album.Images,
			album 		= {},
			proxy       = typeof this._source.proxy !== 'undefined' ? this._source.proxy : '',
			buyBase		= '',
			baseURL 	= '',
			ext 		= '',
			format		= '',
			i			= 0,
			temp		= null,
			iframe		= null;

		album.index = this._source.index;
		album.id = e.Album.id;
		album.key = e.Album.Key;
		album.link = e.Album.URL;
		album.title = this._source.title ? this._source.title : '';
		album.images = [];
		buyBase = album.link.replace('https://', '').split('/').shift();
		buyBase = 'https://' + buyBase + '/buy/' + e.Album.id + '_' + e.Album.Key + '/';

		for(i = 0; i < images.length; i++)
		{
			baseURL = proxy + e.Album.URL + '/' + images[i].id + '_' + images[i].Key;
			format = images[i].Format.toLowerCase();
			ext = format == 'mp4' ? '.jpg' : '.' + format;

			album.images[i] = {};
			album.images[i].index = i;
			album.images[i].sourceType = 'smugmug';
			album.images[i].albumId = e.Album.id;
			album.images[i].albumKey = e.Album.Key;
			album.images[i].id = images[i].id;
			album.images[i].key = images[i].Key;
			album.images[i].filename = images[i].FileName;
			album.images[i].format = format;
			album.images[i].caption = images[i].Caption || '';
			album.images[i].link = e.Album.URL + '#' + images[i].id + '_' + images[i].Key;
			album.images[i].tinyURL = baseURL + '-Ti' + ext;
			album.images[i].thumbURL = baseURL + '-Th' + ext;
			album.images[i].smallURL = baseURL + '-S' + ext;
			album.images[i].mediumURL = baseURL + '-M' + ext;
			album.images[i].largeURL = baseURL + '-L' + ext;
			album.images[i].xlargeURL = baseURL + '-XL' + ext;
			album.images[i].x2largeURL = baseURL + '-X2' + ext;
			album.images[i].x3largeURL = baseURL + '-X3' + ext;
			album.images[i].buyURL = buyBase + images[i].id + '_' + images[i].Key;
			album.images[i].iframe = '';

			if(album.images[i].caption.indexOf('iframe')) {
				temp = Y.Node.create('<div>'+ album.images[i].caption +'</div>');
				iframe = temp.one('iframe');

				if(iframe) {
					album.images[i].iframe = iframe.getAttribute('src');
					album.images[i].caption = album.images[i].caption.replace(/<iframe.*>.*<\/iframe>/gi, '');
				}
			}
		}

		this._loadComplete({ 'albumInfo': album });
	},

	/**
	 * Loads slideshow album data from an array of urls.
	 *
	 * NOTE: You must have a large URL.
	 *
	 * @method _loadUrls
	 * @protected
	 */
	_loadUrls: function()
	{
		var album 	= {},
			i		= 0;

		album.index = this._source.index;
		album.title = this._source.title ? this._source.title : '';
		album.images = [];

		for( ; i < this._source.urls.length; i++)
		{
			album.images[i] = {};
			album.images[i].index = i;
			album.images[i].sourceType = 'urls';
			album.images[i].filename = this._source.urls[i].largeURL.split('/').pop();
			album.images[i].format = '';
			album.images[i].caption = this._source.urls[i].caption || '';
			album.images[i].alt = this._source.urls[i].alt || '';
			album.images[i].link = this._source.urls[i].largeURL;
			album.images[i].thumbURL = this._source.urls[i].thumbURL || this._source.urls[i].largeURL;
			album.images[i].smallURL = this._source.urls[i].smallURL || this._source.urls[i].largeURL;
			album.images[i].mediumURL = this._source.urls[i].mediumURL || this._source.urls[i].largeURL;
			album.images[i].largeURL = this._source.urls[i].largeURL; // Must have a large URL
			album.images[i].xlargeURL = this._source.urls[i].xlargeURL || this._source.urls[i].largeURL;
			album.images[i].x2largeURL = this._source.urls[i].x2largeURL || this._source.urls[i].largeURL;
			album.images[i].x3largeURL = this._source.urls[i].x3largeURL || this._source.urls[i].largeURL;
			album.images[i].buyURL = this._source.urls[i].buyURL || '';
			album.images[i].iframe = this._source.urls[i].iframe || '';
		}

		this._loadComplete({ 'albumInfo': album });
	}

}, {

	/**
	* Static property used to define the default attribute configuration of
	* the Widget.
	*
	* @property ATTRS
	* @type Object
	* @protected
	* @static
	*/
	ATTRS: {

		/**
		 * If true, the images will be randomized after loading.
		 *
		 * @attribute randomize
		 * @type Boolean
		 * @default false
		 */
		randomize: {
			value: false
		}
	},

	/**
	* The types of source data that can be loaded
	* and associated functions.
	*
	* @property TYPES
	* @type Object
	* @readOnly
	* @protected
	* @static
	*/
	TYPES: {
		'smugmug': '_loadSmugMug',
		'flickr': '_loadFlickr',
		'picasa': '_loadPicasa',
		'urls': '_loadUrls',
		'html': '_loadHtml'
	}
});


}, '2.0.0' ,{requires:['base', 'fl-smugmug-api']});


YUI.add('fl-slideshow-base', function(Y) {

/**
 * @module fl-slideshow-base
 */

/**
 * The base class that gets extended when creating new
 * slideshow widgets. Manages loading, playing, and resizing.
 * <p>
 * While SlideshowBase can be instantiated, it is only meant to
 * be extended and does not display any images.
 *
 * @namespace FL
 * @class SlideshowBase
 * @constructor
 * @param config {Object} Configuration object
 * @extends Widget
 */
Y.namespace('FL').SlideshowBase = Y.Base.create('fl-slideshow-base', Y.Widget, [Y.WidgetParent], {

	/**
	 * FL.SlideshowAlbumLoader instance used to load albums.
	 *
	 * @property _loader
	 * @type FL.SlideshowAlbumLoader
	 * @default null
	 * @protected
	 */
	_albumLoader: null,

	/**
	* An array of albums loaded from the source attribute.
	* Each album is an array of objects containing image info.
	*
	* @property albums
	* @type Array
	* @default []
	*/
	albums: [],

	/**
	 * Info for the active album.
	 *
	 * @property albumInfo
	 * @type Object
	 * @default null
	 */
	albumInfo: null,

	/**
	 * A number that represents the index of the active
	 * album in the albums array.
	 *
	 * @property albumIndex
	 * @type Number
	 * @default null
	 */
	albumIndex: null,

	/**
	 * Info for the active image.
	 *
	 * @property imageInfo
	 * @type Object
	 * @default null
	 */
	imageInfo: null,

	/**
	 * A number that represents the index of the active
	 * image in the albumInfo array.
	 *
	 * @property imageIndex
	 * @type Number
	 * @default null
	 */
	imageIndex: null,

	/**
	 * A number that represents the index of the last
	 * image that was loaded in the albumInfo array.
	 *
	 * @property lastImageIndex
	 * @type Number
	 * @default null
	 */
	lastImageIndex: null,

	/**
	 * Timer for the delay before resizing if one is set.
	 *
	 * @property _resizeTimer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_resizeTimer: null,

	/**
	 * Flag for whether the slideshow is currently playing or not.
	 *
	 * @property playing
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_playing: false,

	/**
	 * Timer for the break in between images when
	 * the slideshow is playing.
	 *
	 * @property _playingTimer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_playingTimer: null,

	/**
	 * If set, the slideshow will only auto start when
	 * this event is fired.
	 *
	 * @property _playingTimerEvent
	 * @type Object
	 * @default null
	 * @protected
	 */
	_playingTimerEvent: null,

	/**
	 * An instance of FL.Spinner that is shown and hidden
	 * using _showLoadingImage and _hideLoadingImage when
	 * a loading activity occurs.
	 *
	 * @property _loadingImage
	 * @type FL.Spinner
	 * @default null
	 * @protected
	 */
	_loadingImage: null,

	/**
	 * An div node that wraps the loading image.
	 *
	 * @property _loadingImageWrap
	 * @type Node
	 * @default null
	 * @protected
	 */
	_loadingImageWrap: null,

	/**
	 * Whether the loading image is visible or not.
	 *
	 * @property _loadingImageVisible
	 * @type Boolean
	 * @default false
	 * @protected
	 */
	_loadingImageVisible: false,

	/**
	 * A timer to delay the display of the loading image.
	 *
	 * @property _loadingImageTimer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_loadingImageTimer: null,

	/**
	 * The container to insert the loading image into. If
	 * no container is set, the loading image will be inserted
	 * into the widget's bounding box.
	 *
	 * @property _loadingImageContainer
	 * @type Object
	 * @default null
	 * @protected
	 */
	_loadingImageContainer: null,

	/**
	 * The intial height of the slideshow. Used to resize
	 * back to the starting height when exiting stretchy.
	 *
	 * @property _initialHeight
	 * @type Number
	 * @default null
	 * @protected
	 */
	_initialHeight: null,

	/**
	 * The intial width of the slideshow. Used to resize
	 * back to the starting width when exiting stretchy.
	 *
	 * @property _initialWidth
	 * @type Number
	 * @default null
	 * @protected
	 */
	_initialWidth: null,

	/**
	 * @method initializer
	 * @protected
	 */
	initializer: function()
	{
		// Loader
		this._albumLoader = new Y.FL.SlideshowAlbumLoader({
			randomize: this.get('randomize')
		});
	},

	/**
	 * @method renderUI
	 * @protected
	 */
	renderUI: function()
	{
		this._renderLoadingImage();
	},

	/**
	 * @method bindUI
	 * @protected
	 */
	bindUI: function()
	{
		// Album load complete
		this._albumLoader.on('complete', this._loadAlbumComplete, this);

		// Resize Events
		Y.one(window).on('fl-slideshow-base|resize', this._delayResize, this);
		Y.one(window).on('fl-slideshow-base|orientationchange', this._delayResize, this);

		// Key Events
		Y.Node.one('body').on('keydown', Y.bind(this._onKey, this));
	},

	/**
	 * @method syncUI
	 * @protected
	 */
	syncUI: function()
	{
		this.get('boundingBox').addClass('fl-slideshow-' + this.get('color'));

		this.resize();

		if(this.get('loadOnRender')) {
			this.loadAlbum(this.get('defaultAlbum'), this.get('defaultImage'));
		}
	},

	/**
	 * Add album data to the source object.
	 *
	 * @method addAlbum
	 * @protected
	 */
	addAlbum: function(data)
	{
		var source = this.get('source'),
            i      = source.length;

		source[i] = data;
		source[i].index = i;

		this.set('source', source);
	},

	/**
	 * Loads an album from the source array with the provided albumIndex.
	 * If no albumIndex is provided, the first album in the array will be loaded.
	 * An image to load can also be specified using imageIndex.
	 *
	 * @method loadAlbum
	 * @param albumIndex {Number} The album index to load from the source array.
	 * @param imageIndex {Number} The image index to load from the album array.
	 */
	loadAlbum: function(albumIndex, imageIndex)
	{
		var source 			= this.get('source'),
			loadImageIndex 	= typeof imageIndex == 'undefined' ? 0 : imageIndex;

		// Reset internal image indexes.
		this.imageIndex = null;
		this.lastImageIndex = null;

		/**
		 * Fires before a new album request is made.
		 *
		 * @event albumLoadStart
		 */
		this.fire('albumLoadStart');

		// Load an image after the album.
		this.once('albumLoadComplete', Y.bind(this.loadImage, this, loadImageIndex));

		// Load data passed from another slideshow instance.
		if(source[albumIndex] && source[albumIndex].type == 'album-data') {
			this.albums[albumIndex] = source[albumIndex].data;
			this._loadAlbumComplete({albumInfo: this.albums[albumIndex]});
		}
		// Load the album from the albums array.
		else if(source[albumIndex] && this.albums[albumIndex]) {
			this._loadAlbumComplete({albumInfo: this.albums[albumIndex]});
		}
		// Load the album using the album loader.
		else {
			this._albumLoader.load(source[albumIndex] || source[0]);
		}
	},

	/**
	 * Processes the loaded album and fires the albumLoadComplete event.
	 *
	 * @method _loadAlbumComplete
	 * @param o {Object} The custom event object passed to this method.
	 * @protected
	 */
	_loadAlbumComplete: function(o)
	{
		this.albums[o.albumInfo.index] = o.albumInfo;
		this.albumInfo = o.albumInfo;
		this.albumIndex = o.albumInfo.index;

		/**
		 * Fires after a new album request is made.
		 *
		 * @event albumLoadComplete
		 */
		this.fire('albumLoadComplete');

		// Auto Play
		if(this.get('autoPlay')) {
			this._playingTimerStart();
			this.fire('played');
			this._playing = true;
		}
	},

	/**
	 * Sets the active image index and fires the imageLoadComplete event.
	 *
	 * @method loadImage
	 * @param index {Number} The image index to load.
	 */
	loadImage: function(index)
	{
		if(this._playing) {
			this._playingTimerStart();
		}

		index = index < 0 ? this.albumInfo.images.length - 1 : index;
		index = index >= this.albumInfo.images.length ? 0 : index;

		this.lastImageIndex = this.imageIndex;
		this.imageIndex = index;
		this.imageInfo = this.albumInfo.images[index];

		/**
		 * Fires after a new image index is set.
		 *
		 * @event imageLoadComplete
		 */
		this.fire('imageLoadComplete', { 'imageInfo': this.imageInfo });
	},

	/**
	 * Loads the previous image.
	 *
	 * @method prevImage
	 */
	prevImage: function()
	{
		if(this.get('pauseOnNextOrPrev')) {
			this.pause();
		}

		this.loadImage(this.imageIndex - 1);

		/**
		 * Fires when the previous image is loaded.
		 *
		 * @event prevImage
		 */
		this.fire('prevImage');
	},

	/**
	 * Loads the next image.
	 *
	 * @method nextImage
	 */
	nextImage: function()
	{
		if(this.get('pauseOnNextOrPrev')) {
			this.pause();
		}

		this.loadImage(this.imageIndex + 1);

		/**
		 * Fires when the next image is loaded.
		 *
		 * @event nextImage
		 */
		this.fire('nextImage');
	},

	/**
	 * Keyboard navigation for the next and prev images.
	 *
	 * @method _onKey
	 * @protected
	 */
	_onKey: function(e)
	{
		switch(e.keyCode) {

			case 37:
			this.prevImage();
			break;

			case 39:
			this.nextImage();
			break;
		}
	},

	/**
	 * Resizes the slideshow using either the
	 * stretchy or standard functions.
	 *
	 * @method resize
	 */
	resize: function()
	{
		var stretchy 		= this.get('stretchy'),
			stretchyType 	= this.get('stretchyType'),
			width 			= parseInt(Y.one('body').get('winWidth'), 10),
			threshold		= this.get('responsiveThreshold');

        // Stretchy resize to the window only if the parent width is greater
        // than the responsive threshold and stretchyType is set to window.
		if(width > threshold && stretchy && stretchyType == 'window') {
			this._stretchyWindowResize();
		}

		// Ratio resize if the parent width is less than the responsive
		// threshold or if stretchyType is set to ratio.
		else if((width <= threshold) || (stretchy && stretchyType == 'ratio')) {
			this._stretchyRatioResize();
		}

		// Do a standard resize based on the height and
		// width passed to the constructor function.
		else {
			this._standardResize();
		}

		/**
		 * Fires when the slideshow is resized.
		 *
		 * @event resize
		 */
		this.fire('resize');
	},

	/**
	 * @method _standardResize
	 * @protected
	 */
	_standardResize: function()
	{
		var stretchy 		= this.get('stretchy'),
		    stretchyType 	= this.get('stretchyType'),
		    bb		        = this.get('boundingBox'),
			parent	        = bb.get('parentNode'),
			parentHeight 	= parseInt(parent.getComputedStyle('height'), 10),
			parentWidth 	= parseInt(parent.getComputedStyle('width'), 10),
			height 	        = this.get('height'),
			width 	        = this.get('width');

        // Window resize if we are in fullscreen.
        if(bb.hasClass('fl-fullscreen-active')) {
			this._stretchyWindowResize();
			return;
		}

		// Resize to the width and height of the parent.
		else if(stretchy && stretchyType == 'contain') {
			bb.setStyle('height', parentHeight + 'px');
    		bb.setStyle('width', parentWidth + 'px');
		}

		// Ratio resize if we don't have a height defined.
		else if(!Y.Lang.isNumber(height)) {
			this._stretchyRatioResize();
			return;
		}

		// Resize to the defined width and height.
		else {

    		bb.setStyle('height', height + 'px');

    		if(width) {
    			bb.setStyle('width', width + 'px');
    		}
    		else {
        		bb.setStyle('width', parentWidth + 'px');
    		}
		}
	},

	/**
	 * Resizes to the height of the window, compensating
	 * for any padding.
	 *
	 * @method _stretchyWindowResize
	 * @protected
	 */
	_stretchyWindowResize: function()
	{
		var bb				= this.get('boundingBox'),
			verticalSpace	= this.get('stretchyVerticalSpace'),
			paddingTop 		= parseInt(bb.getStyle('paddingTop'), 10),
			paddingBottom 	= parseInt(bb.getStyle('paddingBottom'), 10),
			height 			= parseInt(Y.one('body').get('winHeight'), 10),
			width			= '';

		// Set the vertical space to 0 and width to the
		// window's width if we are in fullscreen mode.
		if(bb.hasClass('fl-fullscreen-active')) {
			verticalSpace = 0;
			width = parseInt(Y.one('body').get('winWidth'), 10) + 'px';
		}

		height =  (height - paddingTop - paddingBottom - verticalSpace) + 'px';

		bb.setStyle('height', height);
		bb.setStyle('width', width);
	},

	/**
	 * Resizes the height by multiplying the width and stretchyRatio value.
	 *
	 * @method _stretchyRatioResize
	 * @protected
	 */
	_stretchyRatioResize: function()
	{
		var bb				= this.get('boundingBox'),
			parent			= bb.get('parentNode'),
			verticalSpace	= 0,
			stretchyRatio	= this.get('stretchyRatio'),
			paddingTop 		= parseInt(bb.getStyle('paddingTop'), 10),
			paddingBottom 	= parseInt(bb.getStyle('paddingBottom'), 10),
			computedWidth 	= parseInt(parent.getComputedStyle('width'), 10),
			winHeight		= parseInt(Y.one('body').get('winHeight'), 10),
			winWidth		= parseInt(Y.one('body').get('winWidth'), 10),
			height 			= computedWidth * stretchyRatio,
			width			= '';

		// Use the window's height and width if we are in fullscreen mode.
		if(bb.hasClass('fl-fullscreen-active')) {
			height = winHeight;
			width = winWidth;
		}

		height = (height - paddingTop - paddingBottom - verticalSpace) + 'px';

		bb.setStyle('height', height);
		bb.setStyle('width', width);
	},

	/**
	 * Resizes the slideshow after the resize timer completes.
	 *
	 * @method _delayResize
	 * @protected
	 */
	_delayResize: function()
	{
		if(this._resizeTimer) {
			this._resizeTimer.cancel();
		}

		this._resizeTimer = Y.later(300, this, this.resize);
	},

	/**
	 * Starts a new playing timer and fires the played event.
	 *
	 * @method play
	 */
	play: function()
	{
		this._playingTimer = Y.later(this.get('speed'), this, this._playingTimerComplete);

		/**
		 * Fires when the playing timer starts.
		 *
		 * @event played
		 */
		this.fire('played');
		this._playing = true;
	},

	/**
	 * Cancels the current playing timer and fires the paused event.
	 *
	 * @method pause
	 */
	pause: function()
	{
		this._playingTimerCancel();

		/**
		 * Fires when the playing timer is canceled.
		 *
		 * @event paused
		 */
		this.fire('paused');
		this._playing = false;
	},

	/**
	 * A new playing timer will start when this event is fired.
	 *
	 * @method _setPlayingTimerEvent
	 * @param obj {Object} The event's host object.
	 * @param e {String} The event to fire on the host object.
	 * @protected
	 */
	_setPlayingTimerEvent: function(obj, e)
	{
		this._playingTimerEvent = {
			'obj': obj,
			'e': e
		};
	},

	/**
	 * Cancels the playing timer if it is running and starts a new one.
	 * The next image is loaded when the timer completes.
	 *
	 * @method _playingTimerStart
	 * @protected
	 */
	_playingTimerStart: function(e)
	{
		this._playingTimerCancel();

		if(!e && this._playingTimerEvent !== null) {
			this._playingTimerEvent.obj.once('fl-slideshow-base|' + this._playingTimerEvent.e, Y.bind(this._playingTimerStart, this));
		}
		else {
			this._playingTimer = Y.later(this.get('speed'), this, this._playingTimerComplete);
		}
	},

	/**
	 * Fires when the playing timer completes, starts a
	 * new timer and loads the next image.
	 *
	 * @method _playingTimerComplete
	 * @protected
	 */
	_playingTimerComplete: function()
	{
		this.loadImage(this.imageIndex + 1);

		/**
		 * Fires when the playing timer completes.
		 *
		 * @event albumLoadStart
		 */
		this.fire('playingTimerComplete');
	},

	/**
	 * Cancels the playing timer.
	 *
	 * @method _playingTimerCancel
	 * @protected
	 */
	_playingTimerCancel: function()
	{
		if(this._playingTimer) {
			this._playingTimer.cancel();
		}
		if(this._playingTimerEvent) {
			this._playingTimerEvent.obj.detach('fl-slideshow-base|' + this._playingTimerEvent.e);
		}
	},

	/**
	 * Creates the loading image.
	 *
	 * @method _renderLoadingImage
	 * @protected
	 */
	_renderLoadingImage: function()
	{
        var defaults = {
                lines: 11, // The number of lines to draw
                length: 6, // The length of each line
                width: 2, // The line thickness
                radius: 7, // The radius of the inner circle
                color: '', // #rbg or #rrggbb
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false // Whether to render a shadow
            },
            settings = Y.merge(defaults, this.get('loadingImageSettings'));

		if(this.get('loadingImageEnabled')) {

            // Loading image
            if(settings.color === '') {
                settings.color = this._colorToHex(Y.one('body').getStyle('color'));
            }

            this._loadingImage = new Y.FL.Spinner(settings);

            // Loading image wrap
            this._loadingImageWrap = Y.Node.create('<div class="fl-loading-image"></div>');

            this._loadingImageWrap.setStyles({
                position    : 'absolute',
                'z-index'   : '1000'
			});
		}
	},

	/**
	 * Inserts the loading image.
	 *
	 * @method _showLoadingImage
	 * @protected
	 */
	_showLoadingImage: function()
	{
		if(this._loadingImage && !this._loadingImageVisible) {

            this._loadingImageVisible = true;
            this._loadingImage.spin();
			this._loadingImageWrap.insert(this._loadingImage.el);

			if(this._loadingImageContainer !== null) {
                this._loadingImageContainer.insert(this._loadingImageWrap);
			}
			else {
                this.get('contentBox').insert(this._loadingImageWrap);
			}

			this._positionLoadingImage();
		}
	},

	/**
	 * Inserts the loading image div node after
	 * a timer completes.
	 *
	 * @method _showLoadingImageWithDelay
	 * @protected
	 */
	_showLoadingImageWithDelay: function()
	{
		if(this._loadingImage) {
			this._loadingImageTimer = Y.later(1000, this, this._showLoadingImage);
		}
	},

	/**
	 * Removes the loading image div node.
	 *
	 * @method _hideLoadingImage
	 * @protected
	 */
	_hideLoadingImage: function()
	{
		if(this._loadingImageTimer) {
			this._loadingImageTimer.cancel();
			this._loadingImageTimer = null;
		}
		if(this._loadingImage && this._loadingImageVisible) {
            this._loadingImageVisible = false;
			this._loadingImage.stop();
			this._loadingImageWrap.remove();
		}
	},

	/**
	 * Centers the loading image in the content box.
	 *
	 * @method _positionLoadingImage
	 * @protected
	 */
	_positionLoadingImage: function()
	{
		if(this._loadingImage && this._loadingImageVisible) {

            var wrap            = this._loadingImageWrap,
        		wrapHeight      = parseInt(wrap.getComputedStyle('height'), 10),
                wrapWidth       = parseInt(wrap.getComputedStyle('width'), 10),
        		parent          = wrap.get('parentNode'),
        		parentHeight    = parseInt(parent.getComputedStyle('height'), 10),
                parentWidth     = parseInt(parent.getComputedStyle('width'), 10),
        		left            = (parentWidth - wrapWidth)/2,
        		top             = (parentHeight - wrapHeight)/2;

			wrap.setStyles({
                left        : left + 'px',
                top         : top + 'px'
			});

			Y.one(this._loadingImage.el).setStyles({
                left        : '50%',
                top         : '50%'
			});
		}
	},

	/**
	 * Convert RGB color value to a hex value.
	 *
	 * @method _colorToHex
	 * @protected
	 */
	_colorToHex: function(color)
	{
        var digits, red, green, blue, rgb;

        if(color.substr(0, 1) === '#') {
            return color;
        }

        digits = /(.*?)rgb\((\d+), (\d+), (\d+)\)/.exec(color);

        if ( null === digits ) {
	        return '#000';
        }

        red = parseInt(digits[2], 10);
        green = parseInt(digits[3], 10);
        blue = parseInt(digits[4], 10);
        rgb = blue | (green << 8) | (red << 16);
        rgb = rgb.toString(16);

        if(rgb === '0') {
            rgb = '000';
        }

        return digits[1] + '#' + rgb;
    }

}, {

	/**
	 * Custom CSS class name for the widget.
	 *
	 * @property CSS_PREFIX
	 * @type String
	 * @protected
	 * @static
	 */
	CSS_PREFIX: 'fl-slideshow-base',

	/**
	 * Static property used to define the default attribute configuration of
	 * the Widget.
	 *
	 * @property ATTRS
	 * @type Object
	 * @protected
	 * @static
	 */
	ATTRS: {

		/**
		 * Used to create the color class that gets added to the bounding box
		 * when the widget is rendered. The color class is used to create new
		 * CSS color themes. The default CSS provided includes dark and light themes.
		 *
		 * @attribute color
		 * @type String
		 * @default dark
		 * @writeOnce
		 */
		color: {
			value: 'dark',
			writeOnce: true
		},

		/**
		 * An array of source objects used to load albums. Each object must have
		 * a type property and can have a title property as well.
		 * <p>
		 * In addition to those properties, each object has additional required
		 * properties specific to its type. The types currently supported are
		 * smugmug and urls with planned support for flickr and picasa.
		 * See the user guide for information on loading different types.
		 *
		 * @attribute source
		 * @type Array
		 * @default []
		 * @writeOnce
		 */
		source: {
			value: [],
			setter: function(source) {

				if(source.constructor == Object) {
					source = [source];
				}

				for(var i = 0; i < source.length; i++) {
					source[i].index = i;
				}

				return source;
			}
		},

		/**
		 * The default album index to load.
		 *
		 * @attribute defaultAlbum
		 * @type Number
		 * @default 0
		 */
		defaultAlbum: {
			value: 0
		},

		/**
		 * The default image index to load.
		 *
		 * @attribute defaultImage
		 * @type Number
		 * @default 0
		 */
		defaultImage: {
			value: 0
		},

		/**
		 * If true, the slideshow will be loaded after rendering.
		 *
		 * @attribute loadOnRender
		 * @type Boolean
		 * @default true
		 */
		loadOnRender: {
			value: true
		},

		/**
		 * If true, the slideshow will start playing after loading.
		 *
		 * @attribute autoPlay
		 * @type Boolean
		 * @default true
		 */
		autoPlay: {
			value: true
		},

		/**
		 * Whether to pause when the next or previous image is loaded
		 * using nextImage or prevImage. The slideshow will not be paused
		 * if the next or previous image is loaded using loadImage as is the
		 * case when the slideshow is playing.
		 *
		 * @attribute pauseOnNextOrPrev
		 * @type Boolean
		 * @default true
		 */
		pauseOnNextOrPrev: {
			value: true
		},

		/**
		 * If true, the images will be randomized after loading.
		 *
		 * @attribute randomize
		 * @type Boolean
		 * @default false
		 */
		randomize: {
			value: false
		},

		/**
		 * The time between images when playing, measured in milliseconds.
		 *
		 * @attribute speed
		 * @type Number
		 * @default 4000
		 */
		speed: {
			value: 4000
		},

		/**
		 * The minimum width of the parent node at which
		 * responsive features are enabled. Set to 0 to
		 * disable responsive features as they are enabled
		 * whether stretchy is set to true or not.
		 *
		 * @attribute responsiveThreshold
		 * @type Number
		 * @default 600
		 */
		responsiveThreshold: {
			value: 600
		},

		/**
		 * Whether stretchy resizing should be enabled.
		 *
		 * @attribute stretchy
		 * @type Boolean
		 * @default false
		 */
		stretchy: {
			value: false
		},

		/**
		 * The type of stretchy logic to use. Possible values are
		 * window and ratio. Both types resize the width of the
		 * slideshow to the width of its parent node. With window, the
		 * height of the slideshow is resized to the height of the window.
		 * With ratio, the height of the slideshow is resized based
		 * on the ratio set with stretchyRatio or the height of the window
		 * if the ratio height is greater than the window height.
		 *
		 * @attribute stretchyType
		 * @type String
		 * @default ratio
		 */
		stretchyType: {
			value: 'ratio'
		},

		/**
		 * The number of pixels to subtract from the height of
		 * the slideshow when stretchy is set to true.
		 *
		 * @attribute stretchyVerticalSpace
		 * @type Number
		 * @default 0
		 */
		stretchyVerticalSpace: {
			value: 0
		},

		/**
		 * Used to calculate the height of the slideshow when stretchyType
		 * is set to ratio.
		 *
		 * @attribute stretchyRatio
		 * @type Number
		 * @default 0.7
		 */
		stretchyRatio: {
			value: 0.7
		},

		/**
		 * Whether to use the loading image or not.
		 *
		 * @attribute loadingImageEnabled
		 * @type Boolean
		 * @default true
		 */
		loadingImageEnabled: {
			value: true
		},

		/**
		 * Property object for setting up the spin.js loading image.
		 * For a complete list of properties see:
		 * http://effinroot.eiremedia.netdna-cdn.com/repo/plugins/misc/spin.js/index.html
		 *
		 * @attribute loadingImageSettings
		 * @type Object
		 */
		loadingImageSettings: {
			value: {}
		}
	}
});


}, '2.0.0' ,{requires:['node', 'base', 'widget', 'widget-parent', 'widget-child', 'fl-slideshow-album-loader', 'fl-spinner']});


YUI.add('fl-smugmug-api', function(Y) {

/**
 * @module fl-smugmug-api
 */

/**
 * SmugMug API wrapper.
 *
 * NOTE: Only anonymous logins are currently supported.
 *
 * @namespace FL
 * @class SmugMugAPI
 * @constructor
 * @param config {Object} Configuration object
 * @extends Base
 */
Y.namespace('FL').SmugMugAPI = Y.Base.create('fl-smugmug-api', Y.Base, [], {

	/**
	 * ID for the current session.
	 *
	 * @property _sessionID
	 * @type String
	 * @default null
	 * @protected
	 */
	_sessionID: null,

	/**
	 * URL with parameters for the next API request.
	 * Reset after each request.
	 *
	 * @property _requestURL
	 * @type String
	 * @default null
	 * @protected
	 */
	_requestURL: null,

	/**
	 * Lifecycle method. Initializes the request url.
     *
     * @method initializer
     * @protected
     */
	initializer: function()
	{
		this._resetRequestURL();
	},

	/**
	 * Adds a key/value pair to the request url.
	 *
	 * @method addParam
	 * @param key {String} The name of the parameter (example: key=val).
	 * @param val {String} The value of the parameter (example: key=val).
	 */
	addParam: function(key, val)
	{
		this._requestURL = this._requestURL + '&' + key + '=' + val;
	},

	/**
	 * Requests an anonymous login session.
	 *
	 * @method loginAnon
	 */
	loginAnon: function()
	{
	    this.addParam('method', 'smugmug.login.anonymously');
	    this.once('complete', this._loginAnonComplete);
	    this.request();
	},

	/**
	 * Anonymous login success handler.
	 *
	 * @method _loginAnonComplete
	 * @param data {Object} A jsonp data object.
	 * @protected
	 */
	_loginAnonComplete: function(data)
	{
    	if(data.Login) {
    		this._sessionID = data.Login.Session.id;
    	}
	},

	/**
	 * Sends an API request using the request url.
	 *
	 * @method request
	 */
	request: function()
	{
		this.addParam('Callback', '{callback}');

	   	Y.jsonp(this._requestURL, {
		    on: {
		        success: this._requestComplete,
		        timeout: function(){} // TODO: Handle timeouts
		    },
		    context: this,
		    timeout: 60000,
		    args: []
		});
	},

	/**
	 * API request complete handler.
	 *
	 * @method _requestComplete
	 * @param data {Object} A jsonp data object.
	 * @protected
	 */
	_requestComplete: function(data)
	{
		this._resetRequestURL();

		/**
		 * Fires when a request is complete.
		 *
		 * @event complete
		 */
		this.fire('complete', data);
	},

	/**
	 * Clears all parameters on the request url except
	 * the API key and session ID.
	 *
	 * @method _resetRequestURL
	 * @protected
	 */
	_resetRequestURL: function()
	{
		this._requestURL = this.get('apiURL') + '?APIKey=' + this.get('apiKey');

		if(this._sessionID) {
	        this.addParam('SessionID', this._sessionID);
	    }
	}

}, {

	/**
	* Static property used to define the default attribute configuration of
	* the Widget.
	*
	* @property ATTRS
	* @type Object
	* @protected
	* @static
	*/
	ATTRS: {

        /**
		* SmugMug API url to use for requests.
		*
		* @attribute apiUrl
		* @type String
		* @default https://api.smugmug.com/services/api/json/1.3.0/
		*/
		apiURL: {
        	value: 'https://api.smugmug.com/services/api/json/1.3.0/'
        },

        /**
		* SmugMug API key.
		*
		* @attribute apiKey
		* @type String
		* @default 7w6kuU5Ee6KSgRRExf2KLgppdkez9JD2
		*/
		apiKey: {
        	value: '7w6kuU5Ee6KSgRRExf2KLgppdkez9JD2'
        }
	}
});


}, '2.0.0' ,{requires:['base', 'jsonp']});


YUI.add('fl-spinner', function(Y) {

(function(window,document,undefined){var width="width",length="length",radius="radius",lines="lines",trail="trail",color="color",opacity="opacity",speed="speed",shadow="shadow",style="style",height="height",left="left",top="top",px="px",childNodes="childNodes",firstChild="firstChild",parentNode="parentNode",position="position",relative="relative",absolute="absolute",animation="animation",transform="transform",Origin="Origin",Timeout="Timeout",coord="coord",black="#000",styleSheets=style+"Sheets",prefixes="webkit0Moz0ms0O".split(0),animations={},useCssAnimations;function eachPair(args,it){var end=~~((args[length]-1)/2);for(var i=1;i<=end;i++){it(args[i*2-1],args[i*2])}}function createEl(tag){var el=document.createElement(tag||"div");eachPair(arguments,function(prop,val){el[prop]=val});return el}function ins(parent,child1,child2){if(child2&&!child2[parentNode]){ins(parent,child2)}parent.insertBefore(child1,child2||null);return parent}ins(document.getElementsByTagName("head")[0],createEl(style));var sheet=document[styleSheets][document[styleSheets][length]-1];function addAnimation(to,end){var name=[opacity,end,~~(to*100)].join("-"),dest="{"+opacity+":"+to+"}",i;if(!animations[name]){for(i=0;i<prefixes[length];i++){try{sheet.insertRule("@"+(prefixes[i]&&"-"+prefixes[i].toLowerCase()+"-"||"")+"keyframes "+name+"{0%{"+opacity+":1}"+end+"%"+dest+"to"+dest+"}",sheet.cssRules[length])}catch(err){}}animations[name]=1}return name}function vendor(el,prop){var s=el[style],pp,i;if(s[prop]!==undefined){return prop}prop=prop.charAt(0).toUpperCase()+prop.slice(1);for(i=0;i<prefixes[length];i++){pp=prefixes[i]+prop;if(s[pp]!==undefined){return pp}}}function css(el){eachPair(arguments,function(n,val){el[style][vendor(el,n)||n]=val});return el}function defaults(obj){eachPair(arguments,function(prop,val){if(obj[prop]===undefined){obj[prop]=val}});return obj}var Spinner=function Spinner(o){this.opts=defaults(o||{},lines,12,trail,100,length,7,width,5,radius,10,color,black,opacity,1/4,speed,1)},proto=Spinner.prototype={spin:function(target){var self=this,el=self.el=self[lines](self.opts);if(target){ins(target,css(el,left,~~(target.offsetWidth/2)+px,top,~~(target.offsetHeight/2)+px),target[firstChild])}if(!useCssAnimations){var o=self.opts,i=0,f=20/o[speed],ostep=(1-o[opacity])/(f*o[trail]/100),astep=f/o[lines];(function anim(){i++;for(var s=o[lines];s;s--){var alpha=Math.max(1-(i+s*astep)%f*ostep,o[opacity]);self[opacity](el,o[lines]-s,alpha,o)}self[Timeout]=self.el&&window["set"+Timeout](anim,50)})()}return self},stop:function(){var self=this,el=self.el;window["clear"+Timeout](self[Timeout]);if(el&&el[parentNode]){el[parentNode].removeChild(el)}self.el=undefined;return self}};proto[lines]=function(o){var el=css(createEl(),position,relative),animationName=addAnimation(o[opacity],o[trail]),i=0,seg;function fill(color,shadow){return css(createEl(),position,absolute,width,(o[length]+o[width])+px,height,o[width]+px,"background",color,"boxShadow",shadow,transform+Origin,left,transform,"rotate("+~~(360/o[lines]*i)+"deg) translate("+o[radius]+px+",0)","borderRadius","100em")}for(;i<o[lines];i++){seg=css(createEl(),position,absolute,top,1+~(o[width]/2)+px,transform,"translate3d(0,0,0)",animation,animationName+" "+1/o[speed]+"s linear infinite "+(1/o[lines]/o[speed]*i-1/o[speed])+"s");if(o[shadow]){ins(seg,css(fill(black,"0 0 4px "+black),top,2+px))}ins(el,ins(seg,fill(o[color],"0 0 1px rgba(0,0,0,.1)")))}return el};proto[opacity]=function(el,i,val){el[childNodes][i][style][opacity]=val};var behavior="behavior",URL_VML="url(#default#VML)",tag="group0roundrect0fill0stroke".split(0);(function(){var s=css(createEl(tag[0]),behavior,URL_VML),i;if(!vendor(s,transform)&&s.adj){for(i=0;i<tag[length];i++){sheet.addRule(tag[i],behavior+":"+URL_VML)}proto[lines]=function(){var o=this.opts,r=o[length]+o[width],s=2*r;function grp(){return css(createEl(tag[0],coord+"size",s+" "+s,coord+Origin,-r+" "+-r),width,s,height,s)}var g=grp(),margin=~(o[length]+o[radius]+o[width])+px,i;function seg(i,dx,filter){ins(g,ins(css(grp(),"rotation",360/o[lines]*i+"deg",left,~~dx),ins(css(createEl(tag[1],"arcsize",1),width,r,height,o[width],left,o[radius],top,-o[width]/2,"filter",filter),createEl(tag[2],color,o[color],opacity,o[opacity]),createEl(tag[3],opacity,0))))}if(o[shadow]){for(i=1;i<=o[lines];i++){seg(i,-2,"progid:DXImage"+transform+".Microsoft.Blur(pixel"+radius+"=2,make"+shadow+"=1,"+shadow+opacity+"=.3)")}}for(i=1;i<=o[lines];i++){seg(i)}return ins(css(createEl(),"margin",margin+" 0 0 "+margin,position,relative),g)};proto[opacity]=function(el,i,val,o){o=o[shadow]&&o[lines]||0;el[firstChild][childNodes][i+o][firstChild][firstChild][opacity]=val}}else{useCssAnimations=vendor(s,animation)}})();Y.namespace('FL').Spinner=Spinner})(window,document);


}, '2.0.0' );


YUI.add('fl-utils', function(Y) {

/**
 * @module fl-utils
 */

/**
 * General helper functions for all FastLine modules.
 *
 * @namespace FL
 * @class Utils
 * @constructor
 * @static
 */
Y.namespace('FL').Utils = {

    /**
	 * Checks for support of the provided CSS property.
	 * Method adapted from: https://gist.github.com/556448
	 *
	 * @method cssSupport
	 * @param p {String} The property to check.
	 * @returns Boolean
	 */
	cssSupport: function(p)
	{
		var b = document.body || document.documentElement,
	    	s = b.style,
	    	v = ['Moz', 'Webkit', 'Khtml', 'O', 'ms', 'Icab'],
	    	i = 0;

	    // Transform not working well in these browsers
	    if(p == 'transform' && Y.UA.gecko && Y.UA.gecko < 4) { return false; }
	    if(p == 'transform' && Y.UA.opera > 0) { return false; }
	    if(p == 'transform' && Y.UA.ie > 0 && Y.UA.ie < 10) { return false; }
	    if(p == 'transform' && navigator.userAgent.match(/Trident/)) { return false; }

	    // No css support detected
	    if(typeof s == 'undefined') { return false; }

	    // Tests for standard prop
	    if(typeof s[p] == 'string') { return true; }

	    // Tests for vendor specific prop
	    p = p.charAt(0).toUpperCase() + p.substr(1);

	    for( ; i < v.length; i++) {
			if(typeof s[v[i] + p] == 'string') { return true; }
	    }
	}
};


}, '2.0.0' );
