/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@emotion/is-prop-valid/dist/is-prop-valid.browser.esm.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@emotion/is-prop-valid/dist/is-prop-valid.browser.esm.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _emotion_memoize__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @emotion/memoize */ "./node_modules/@emotion/memoize/dist/memoize.browser.esm.js");


var reactPropsRegex = /^((children|dangerouslySetInnerHTML|key|ref|autoFocus|defaultValue|defaultChecked|innerHTML|suppressContentEditableWarning|suppressHydrationWarning|valueLink|accept|acceptCharset|accessKey|action|allow|allowUserMedia|allowPaymentRequest|allowFullScreen|allowTransparency|alt|async|autoComplete|autoPlay|capture|cellPadding|cellSpacing|challenge|charSet|checked|cite|classID|className|cols|colSpan|content|contentEditable|contextMenu|controls|controlsList|coords|crossOrigin|data|dateTime|decoding|default|defer|dir|disabled|disablePictureInPicture|download|draggable|encType|form|formAction|formEncType|formMethod|formNoValidate|formTarget|frameBorder|headers|height|hidden|high|href|hrefLang|htmlFor|httpEquiv|id|inputMode|integrity|is|keyParams|keyType|kind|label|lang|list|loading|loop|low|marginHeight|marginWidth|max|maxLength|media|mediaGroup|method|min|minLength|multiple|muted|name|nonce|noValidate|open|optimum|pattern|placeholder|playsInline|poster|preload|profile|radioGroup|readOnly|referrerPolicy|rel|required|reversed|role|rows|rowSpan|sandbox|scope|scoped|scrolling|seamless|selected|shape|size|sizes|slot|span|spellCheck|src|srcDoc|srcLang|srcSet|start|step|style|summary|tabIndex|target|title|type|useMap|value|width|wmode|wrap|about|datatype|inlist|prefix|property|resource|typeof|vocab|autoCapitalize|autoCorrect|autoSave|color|inert|itemProp|itemScope|itemType|itemID|itemRef|on|results|security|unselectable|accentHeight|accumulate|additive|alignmentBaseline|allowReorder|alphabetic|amplitude|arabicForm|ascent|attributeName|attributeType|autoReverse|azimuth|baseFrequency|baselineShift|baseProfile|bbox|begin|bias|by|calcMode|capHeight|clip|clipPathUnits|clipPath|clipRule|colorInterpolation|colorInterpolationFilters|colorProfile|colorRendering|contentScriptType|contentStyleType|cursor|cx|cy|d|decelerate|descent|diffuseConstant|direction|display|divisor|dominantBaseline|dur|dx|dy|edgeMode|elevation|enableBackground|end|exponent|externalResourcesRequired|fill|fillOpacity|fillRule|filter|filterRes|filterUnits|floodColor|floodOpacity|focusable|fontFamily|fontSize|fontSizeAdjust|fontStretch|fontStyle|fontVariant|fontWeight|format|from|fr|fx|fy|g1|g2|glyphName|glyphOrientationHorizontal|glyphOrientationVertical|glyphRef|gradientTransform|gradientUnits|hanging|horizAdvX|horizOriginX|ideographic|imageRendering|in|in2|intercept|k|k1|k2|k3|k4|kernelMatrix|kernelUnitLength|kerning|keyPoints|keySplines|keyTimes|lengthAdjust|letterSpacing|lightingColor|limitingConeAngle|local|markerEnd|markerMid|markerStart|markerHeight|markerUnits|markerWidth|mask|maskContentUnits|maskUnits|mathematical|mode|numOctaves|offset|opacity|operator|order|orient|orientation|origin|overflow|overlinePosition|overlineThickness|panose1|paintOrder|pathLength|patternContentUnits|patternTransform|patternUnits|pointerEvents|points|pointsAtX|pointsAtY|pointsAtZ|preserveAlpha|preserveAspectRatio|primitiveUnits|r|radius|refX|refY|renderingIntent|repeatCount|repeatDur|requiredExtensions|requiredFeatures|restart|result|rotate|rx|ry|scale|seed|shapeRendering|slope|spacing|specularConstant|specularExponent|speed|spreadMethod|startOffset|stdDeviation|stemh|stemv|stitchTiles|stopColor|stopOpacity|strikethroughPosition|strikethroughThickness|string|stroke|strokeDasharray|strokeDashoffset|strokeLinecap|strokeLinejoin|strokeMiterlimit|strokeOpacity|strokeWidth|surfaceScale|systemLanguage|tableValues|targetX|targetY|textAnchor|textDecoration|textRendering|textLength|to|transform|u1|u2|underlinePosition|underlineThickness|unicode|unicodeBidi|unicodeRange|unitsPerEm|vAlphabetic|vHanging|vIdeographic|vMathematical|values|vectorEffect|version|vertAdvY|vertOriginX|vertOriginY|viewBox|viewTarget|visibility|widths|wordSpacing|writingMode|x|xHeight|x1|x2|xChannelSelector|xlinkActuate|xlinkArcrole|xlinkHref|xlinkRole|xlinkShow|xlinkTitle|xlinkType|xmlBase|xmlns|xmlnsXlink|xmlLang|xmlSpace|y|y1|y2|yChannelSelector|z|zoomAndPan|for|class|autofocus)|(([Dd][Aa][Tt][Aa]|[Aa][Rr][Ii][Aa]|x)-.*))$/; // https://esbench.com/bench/5bfee68a4cd7e6009ef61d23

var index = (0,_emotion_memoize__WEBPACK_IMPORTED_MODULE_0__["default"])(function (prop) {
  return reactPropsRegex.test(prop) || prop.charCodeAt(0) === 111
  /* o */
  && prop.charCodeAt(1) === 110
  /* n */
  && prop.charCodeAt(2) < 91;
}
/* Z+1 */
);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (index);


/***/ }),

/***/ "./node_modules/@emotion/memoize/dist/memoize.browser.esm.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@emotion/memoize/dist/memoize.browser.esm.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function memoize(fn) {
  var cache = {};
  return function (arg) {
    if (cache[arg] === undefined) cache[arg] = fn(arg);
    return cache[arg];
  };
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (memoize);


/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/animate.js":
/*!*****************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/animate.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "animate": () => (/* binding */ animate)
/* harmony export */ });
/* harmony import */ var _utils_transitions_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/transitions.js */ "./node_modules/framer-motion/dist/es/animation/utils/transitions.js");
/* harmony import */ var _value_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../value/index.js */ "./node_modules/framer-motion/dist/es/value/index.js");
/* harmony import */ var _value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../value/utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");




/**
 * Animate a single value or a `MotionValue`.
 *
 * The first argument is either a `MotionValue` to animate, or an initial animation value.
 *
 * The second is either a value to animate to, or an array of keyframes to animate through.
 *
 * The third argument can be either tween or spring options, and optional lifecycle methods: `onUpdate`, `onPlay`, `onComplete`, `onRepeat` and `onStop`.
 *
 * Returns `AnimationPlaybackControls`, currently just a `stop` method.
 *
 * ```javascript
 * const x = useMotionValue(0)
 *
 * useEffect(() => {
 *   const controls = animate(x, 100, {
 *     type: "spring",
 *     stiffness: 2000,
 *     onComplete: v => {}
 *   })
 *
 *   return controls.stop
 * })
 * ```
 *
 * @public
 */
function animate(from, to, transition) {
    if (transition === void 0) { transition = {}; }
    var value = (0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_0__.isMotionValue)(from) ? from : (0,_value_index_js__WEBPACK_IMPORTED_MODULE_1__.motionValue)(from);
    (0,_utils_transitions_js__WEBPACK_IMPORTED_MODULE_2__.startAnimation)("", value, to, transition);
    return {
        stop: function () { return value.stop(); },
    };
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/animation-controls.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/animation-controls.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "animationControls": () => (/* binding */ animationControls),
/* harmony export */   "isAnimationControls": () => (/* binding */ isAnimationControls)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var _render_utils_setters_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../render/utils/setters.js */ "./node_modules/framer-motion/dist/es/render/utils/setters.js");
/* harmony import */ var _render_utils_animation_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../render/utils/animation.js */ "./node_modules/framer-motion/dist/es/render/utils/animation.js");





function animationControls() {
    /**
     * Track whether the host component has mounted.
     */
    var hasMounted = false;
    /**
     * Pending animations that are started before a component is mounted.
     * TODO: Remove this as animations should only run in effects
     */
    var pendingAnimations = [];
    /**
     * A collection of linked component animation controls.
     */
    var subscribers = new Set();
    var controls = {
        subscribe: function (visualElement) {
            subscribers.add(visualElement);
            return function () { return void subscribers.delete(visualElement); };
        },
        start: function (definition, transitionOverride) {
            /**
             * TODO: We only perform this hasMounted check because in Framer we used to
             * encourage the ability to start an animation within the render phase. This
             * isn't behaviour concurrent-safe so when we make Framer concurrent-safe
             * we can ditch this.
             */
            if (hasMounted) {
                var animations_1 = [];
                subscribers.forEach(function (visualElement) {
                    animations_1.push((0,_render_utils_animation_js__WEBPACK_IMPORTED_MODULE_1__.animateVisualElement)(visualElement, definition, {
                        transitionOverride: transitionOverride,
                    }));
                });
                return Promise.all(animations_1);
            }
            else {
                return new Promise(function (resolve) {
                    pendingAnimations.push({
                        animation: [definition, transitionOverride],
                        resolve: resolve,
                    });
                });
            }
        },
        set: function (definition) {
            (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(hasMounted, "controls.set() should only be called after a component has mounted. Consider calling within a useEffect hook.");
            return subscribers.forEach(function (visualElement) {
                (0,_render_utils_setters_js__WEBPACK_IMPORTED_MODULE_2__.setValues)(visualElement, definition);
            });
        },
        stop: function () {
            subscribers.forEach(function (visualElement) {
                (0,_render_utils_animation_js__WEBPACK_IMPORTED_MODULE_1__.stopAnimation)(visualElement);
            });
        },
        mount: function () {
            hasMounted = true;
            pendingAnimations.forEach(function (_a) {
                var animation = _a.animation, resolve = _a.resolve;
                controls.start.apply(controls, (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__spread)(animation)).then(resolve);
            });
            return function () {
                hasMounted = false;
                controls.stop();
            };
        },
    };
    return controls;
}
function isAnimationControls(v) {
    return typeof v === "object" && typeof v.start === "function";
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/use-animated-state.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/use-animated-state.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useAnimatedState": () => (/* binding */ useAnimatedState)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/geometry/index.js */ "./node_modules/framer-motion/dist/es/utils/geometry/index.js");
/* harmony import */ var _render_utils_setters_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../render/utils/setters.js */ "./node_modules/framer-motion/dist/es/render/utils/setters.js");
/* harmony import */ var _render_utils_animation_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../render/utils/animation.js */ "./node_modules/framer-motion/dist/es/render/utils/animation.js");
/* harmony import */ var _render_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../render/index.js */ "./node_modules/framer-motion/dist/es/render/index.js");
/* harmony import */ var _motion_utils_use_visual_state_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../motion/utils/use-visual-state.js */ "./node_modules/framer-motion/dist/es/motion/utils/use-visual-state.js");









var createObject = function () { return ({}); };
var stateVisualElement = (0,_render_index_js__WEBPACK_IMPORTED_MODULE_1__.visualElement)({
    build: function () { },
    measureViewportBox: _utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_2__.axisBox,
    resetTransform: function () { },
    restoreTransform: function () { },
    removeValueFromRenderState: function () { },
    render: function () { },
    scrapeMotionValuesFromProps: createObject,
    readValueFromInstance: function (_state, key, options) {
        return options.initialState[key] || 0;
    },
    makeTargetAnimatable: function (element, _a) {
        var transition = _a.transition, transitionEnd = _a.transitionEnd, target = (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__rest)(_a, ["transition", "transitionEnd"]);
        var origin = (0,_render_utils_setters_js__WEBPACK_IMPORTED_MODULE_4__.getOrigin)(target, transition || {}, element);
        (0,_render_utils_setters_js__WEBPACK_IMPORTED_MODULE_4__.checkTargetForNewValues)(element, target, origin);
        return (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({ transition: transition, transitionEnd: transitionEnd }, target);
    },
});
var useVisualState = (0,_motion_utils_use_visual_state_js__WEBPACK_IMPORTED_MODULE_5__.makeUseVisualState)({
    scrapeMotionValuesFromProps: createObject,
    createRenderState: createObject,
});
/**
 * This is not an officially supported API and may be removed
 * on any version.
 * @internal
 */
function useAnimatedState(initialState) {
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__read)((0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialState), 2), animationState = _a[0], setAnimationState = _a[1];
    var visualState = useVisualState({}, false);
    var element = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_6__.useConstant)(function () {
        return stateVisualElement({ props: {}, visualState: visualState }, { initialState: initialState });
    });
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        element.mount({});
        return element.unmount();
    }, []);
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        element.setProps({
            onUpdate: function (v) { return setAnimationState((0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({}, v)); },
        });
    });
    var startAnimation = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_6__.useConstant)(function () { return function (animationDefinition) {
        return (0,_render_utils_animation_js__WEBPACK_IMPORTED_MODULE_7__.animateVisualElement)(element, animationDefinition);
    }; });
    return [animationState, startAnimation];
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/use-animation.js":
/*!***********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/use-animation.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useAnimation": () => (/* binding */ useAnimation)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _animation_controls_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./animation-controls.js */ "./node_modules/framer-motion/dist/es/animation/animation-controls.js");




/**
 * Creates `AnimationControls`, which can be used to manually start, stop
 * and sequence animations on one or more components.
 *
 * The returned `AnimationControls` should be passed to the `animate` property
 * of the components you want to animate.
 *
 * These components can then be animated with the `start` method.
 *
 * @library
 *
 * ```jsx
 * import * as React from 'react'
 * import { Frame, useAnimation } from 'framer'
 *
 * export function MyComponent(props) {
 *    const controls = useAnimation()
 *
 *    controls.start({
 *        x: 100,
 *        transition: { duration: 0.5 },
 *    })
 *
 *    return <Frame animate={controls} />
 * }
 * ```
 *
 * @motion
 *
 * ```jsx
 * import * as React from 'react'
 * import { motion, useAnimation } from 'framer-motion'
 *
 * export function MyComponent(props) {
 *    const controls = useAnimation()
 *
 *    controls.start({
 *        x: 100,
 *        transition: { duration: 0.5 },
 *    })
 *
 *    return <motion.div animate={controls} />
 * }
 * ```
 *
 * @returns Animation controller with `start` and `stop` methods
 *
 * @public
 */
function useAnimation() {
    var controls = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__.useConstant)(_animation_controls_js__WEBPACK_IMPORTED_MODULE_2__.animationControls);
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(controls.mount, []);
    return controls;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/utils/default-transitions.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/utils/default-transitions.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "criticallyDampedSpring": () => (/* binding */ criticallyDampedSpring),
/* harmony export */   "getDefaultTransition": () => (/* binding */ getDefaultTransition),
/* harmony export */   "linearTween": () => (/* binding */ linearTween),
/* harmony export */   "underDampedSpring": () => (/* binding */ underDampedSpring)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-keyframes-target.js */ "./node_modules/framer-motion/dist/es/animation/utils/is-keyframes-target.js");



var underDampedSpring = function () { return ({
    type: "spring",
    stiffness: 500,
    damping: 25,
    restDelta: 0.5,
    restSpeed: 10,
}); };
var criticallyDampedSpring = function (to) { return ({
    type: "spring",
    stiffness: 550,
    damping: to === 0 ? 2 * Math.sqrt(550) : 30,
    restDelta: 0.01,
    restSpeed: 10,
}); };
var linearTween = function () { return ({
    type: "keyframes",
    ease: "linear",
    duration: 0.3,
}); };
var keyframes = function (values) { return ({
    type: "keyframes",
    duration: 0.8,
    values: values,
}); };
var defaultTransitions = {
    x: underDampedSpring,
    y: underDampedSpring,
    z: underDampedSpring,
    rotate: underDampedSpring,
    rotateX: underDampedSpring,
    rotateY: underDampedSpring,
    rotateZ: underDampedSpring,
    scaleX: criticallyDampedSpring,
    scaleY: criticallyDampedSpring,
    scale: criticallyDampedSpring,
    opacity: linearTween,
    backgroundColor: linearTween,
    color: linearTween,
    default: criticallyDampedSpring,
};
var getDefaultTransition = function (valueKey, to) {
    var transitionFactory;
    if ((0,_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_0__.isKeyframesTarget)(to)) {
        transitionFactory = keyframes;
    }
    else {
        transitionFactory =
            defaultTransitions[valueKey] || defaultTransitions.default;
    }
    return (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({ to: to }, transitionFactory(to));
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/utils/easing.js":
/*!**********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/utils/easing.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "easingDefinitionToFunction": () => (/* binding */ easingDefinitionToFunction),
/* harmony export */   "isEasingArray": () => (/* binding */ isEasingArray)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/easing/index.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/easing/cubic-bezier.js");




var easingLookup = {
    linear: popmotion__WEBPACK_IMPORTED_MODULE_1__.linear,
    easeIn: popmotion__WEBPACK_IMPORTED_MODULE_1__.easeIn,
    easeInOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.easeInOut,
    easeOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.easeOut,
    circIn: popmotion__WEBPACK_IMPORTED_MODULE_1__.circIn,
    circInOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.circInOut,
    circOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.circOut,
    backIn: popmotion__WEBPACK_IMPORTED_MODULE_1__.backIn,
    backInOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.backInOut,
    backOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.backOut,
    anticipate: popmotion__WEBPACK_IMPORTED_MODULE_1__.anticipate,
    bounceIn: popmotion__WEBPACK_IMPORTED_MODULE_1__.bounceIn,
    bounceInOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.bounceInOut,
    bounceOut: popmotion__WEBPACK_IMPORTED_MODULE_1__.bounceOut,
};
var easingDefinitionToFunction = function (definition) {
    if (Array.isArray(definition)) {
        // If cubic bezier definition, create bezier curve
        (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(definition.length === 4, "Cubic bezier arrays must contain four numerical values.");
        var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__read)(definition, 4), x1 = _a[0], y1 = _a[1], x2 = _a[2], y2 = _a[3];
        return (0,popmotion__WEBPACK_IMPORTED_MODULE_3__.cubicBezier)(x1, y1, x2, y2);
    }
    else if (typeof definition === "string") {
        // Else lookup from table
        (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(easingLookup[definition] !== undefined, "Invalid easing type '" + definition + "'");
        return easingLookup[definition];
    }
    return definition;
};
var isEasingArray = function (ease) {
    return Array.isArray(ease) && typeof ease[0] !== "number";
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/utils/is-animatable.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/utils/is-animatable.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isAnimatable": () => (/* binding */ isAnimatable)
/* harmony export */ });
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/complex/index.js");


/**
 * Check if a value is animatable. Examples:
 *
 * ✅: 100, "100px", "#fff"
 * ❌: "block", "url(2.jpg)"
 * @param value
 *
 * @internal
 */
var isAnimatable = function (key, value) {
    // If the list of keys tat might be non-animatable grows, replace with Set
    if (key === "zIndex")
        return false;
    // If it's a number or a keyframes array, we can animate it. We might at some point
    // need to do a deep isAnimatable check of keyframes, or let Popmotion handle this,
    // but for now lets leave it like this for performance reasons
    if (typeof value === "number" || Array.isArray(value))
        return true;
    if (typeof value === "string" && // It's animatable if we have a string
        style_value_types__WEBPACK_IMPORTED_MODULE_0__.complex.test(value) && // And it contains numbers and/or colors
        !value.startsWith("url(") // Unless it starts with "url("
    ) {
        return true;
    }
    return false;
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/utils/is-keyframes-target.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/utils/is-keyframes-target.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isKeyframesTarget": () => (/* binding */ isKeyframesTarget)
/* harmony export */ });
var isKeyframesTarget = function (v) {
    return Array.isArray(v);
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/animation/utils/transitions.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/animation/utils/transitions.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "convertTransitionToAnimationOptions": () => (/* binding */ convertTransitionToAnimationOptions),
/* harmony export */   "getDelayFromTransition": () => (/* binding */ getDelayFromTransition),
/* harmony export */   "getPopmotionAnimationOptions": () => (/* binding */ getPopmotionAnimationOptions),
/* harmony export */   "getValueTransition": () => (/* binding */ getValueTransition),
/* harmony export */   "hydrateKeyframes": () => (/* binding */ hydrateKeyframes),
/* harmony export */   "isTransitionDefined": () => (/* binding */ isTransitionDefined),
/* harmony export */   "startAnimation": () => (/* binding */ startAnimation)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/animations/inertia.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/animations/index.js");
/* harmony import */ var _utils_time_conversion_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/time-conversion.js */ "./node_modules/framer-motion/dist/es/utils/time-conversion.js");
/* harmony import */ var _easing_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./easing.js */ "./node_modules/framer-motion/dist/es/animation/utils/easing.js");
/* harmony import */ var _is_animatable_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./is-animatable.js */ "./node_modules/framer-motion/dist/es/animation/utils/is-animatable.js");
/* harmony import */ var _default_transitions_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./default-transitions.js */ "./node_modules/framer-motion/dist/es/animation/utils/default-transitions.js");
/* harmony import */ var _render_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../render/dom/utils/value-types.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js");









/**
 * Decide whether a transition is defined on a given Transition.
 * This filters out orchestration options and returns true
 * if any options are left.
 */
function isTransitionDefined(_a) {
    var when = _a.when, delay = _a.delay, delayChildren = _a.delayChildren, staggerChildren = _a.staggerChildren, staggerDirection = _a.staggerDirection, repeat = _a.repeat, repeatType = _a.repeatType, repeatDelay = _a.repeatDelay, from = _a.from, transition = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, ["when", "delay", "delayChildren", "staggerChildren", "staggerDirection", "repeat", "repeatType", "repeatDelay", "from"]);
    return !!Object.keys(transition).length;
}
var legacyRepeatWarning = false;
/**
 * Convert Framer Motion's Transition type into Popmotion-compatible options.
 */
function convertTransitionToAnimationOptions(_a) {
    var ease = _a.ease, times = _a.times, yoyo = _a.yoyo, flip = _a.flip, loop = _a.loop, transition = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, ["ease", "times", "yoyo", "flip", "loop"]);
    var options = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, transition);
    if (times)
        options["offset"] = times;
    /**
     * Convert any existing durations from seconds to milliseconds
     */
    if (transition.duration)
        options["duration"] = (0,_utils_time_conversion_js__WEBPACK_IMPORTED_MODULE_2__.secondsToMilliseconds)(transition.duration);
    if (transition.repeatDelay)
        options.repeatDelay = (0,_utils_time_conversion_js__WEBPACK_IMPORTED_MODULE_2__.secondsToMilliseconds)(transition.repeatDelay);
    /**
     * Map easing names to Popmotion's easing functions
     */
    if (ease) {
        options["ease"] = (0,_easing_js__WEBPACK_IMPORTED_MODULE_3__.isEasingArray)(ease)
            ? ease.map(_easing_js__WEBPACK_IMPORTED_MODULE_3__.easingDefinitionToFunction)
            : (0,_easing_js__WEBPACK_IMPORTED_MODULE_3__.easingDefinitionToFunction)(ease);
    }
    /**
     * Support legacy transition API
     */
    if (transition.type === "tween")
        options.type = "keyframes";
    /**
     * TODO: These options are officially removed from the API.
     */
    if (yoyo || loop || flip) {
        (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.warning)(!legacyRepeatWarning, "yoyo, loop and flip have been removed from the API. Replace with repeat and repeatType options.");
        legacyRepeatWarning = true;
        if (yoyo) {
            options.repeatType = "reverse";
        }
        else if (loop) {
            options.repeatType = "loop";
        }
        else if (flip) {
            options.repeatType = "mirror";
        }
        options.repeat = loop || yoyo || flip || transition.repeat;
    }
    /**
     * TODO: Popmotion 9 has the ability to automatically detect whether to use
     * a keyframes or spring animation, but does so by detecting velocity and other spring options.
     * It'd be good to introduce a similar thing here.
     */
    if (transition.type !== "spring")
        options.type = "keyframes";
    return options;
}
/**
 * Get the delay for a value by checking Transition with decreasing specificity.
 */
function getDelayFromTransition(transition, key) {
    var _a;
    var valueTransition = getValueTransition(transition, key) || {};
    return (_a = valueTransition.delay) !== null && _a !== void 0 ? _a : 0;
}
function hydrateKeyframes(options) {
    if (Array.isArray(options.to) && options.to[0] === null) {
        options.to = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__spread)(options.to);
        options.to[0] = options.from;
    }
    return options;
}
function getPopmotionAnimationOptions(transition, options, key) {
    var _a;
    if (Array.isArray(options.to)) {
        (_a = transition.duration) !== null && _a !== void 0 ? _a : (transition.duration = 0.8);
    }
    hydrateKeyframes(options);
    /**
     * Get a default transition if none is determined to be defined.
     */
    if (!isTransitionDefined(transition)) {
        transition = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, transition), (0,_default_transitions_js__WEBPACK_IMPORTED_MODULE_4__.getDefaultTransition)(key, options.to));
    }
    return (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, options), convertTransitionToAnimationOptions(transition));
}
/**
 *
 */
function getAnimation(key, value, target, transition, onComplete) {
    var _a;
    var valueTransition = getValueTransition(transition, key);
    var origin = (_a = valueTransition.from) !== null && _a !== void 0 ? _a : value.get();
    var isTargetAnimatable = (0,_is_animatable_js__WEBPACK_IMPORTED_MODULE_5__.isAnimatable)(key, target);
    /**
     * If we're trying to animate from "none", try and get an animatable version
     * of the target. This could be improved to work both ways.
     */
    if (origin === "none" && isTargetAnimatable && typeof target === "string") {
        origin = (0,_render_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_6__.getAnimatableNone)(key, target);
    }
    var isOriginAnimatable = (0,_is_animatable_js__WEBPACK_IMPORTED_MODULE_5__.isAnimatable)(key, origin);
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.warning)(isOriginAnimatable === isTargetAnimatable, "You are trying to animate " + key + " from \"" + origin + "\" to \"" + target + "\". " + origin + " is not an animatable value - to enable this animation set " + origin + " to a value animatable to " + target + " via the `style` property.");
    function start() {
        var options = {
            from: origin,
            to: target,
            velocity: value.getVelocity(),
            onComplete: onComplete,
            onUpdate: function (v) { return value.set(v); },
        };
        return valueTransition.type === "inertia" ||
            valueTransition.type === "decay"
            ? (0,popmotion__WEBPACK_IMPORTED_MODULE_7__.inertia)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, options), valueTransition))
            : (0,popmotion__WEBPACK_IMPORTED_MODULE_8__.animate)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, getPopmotionAnimationOptions(valueTransition, options, key)), { onUpdate: function (v) {
                    var _a;
                    options.onUpdate(v);
                    (_a = valueTransition.onUpdate) === null || _a === void 0 ? void 0 : _a.call(valueTransition, v);
                }, onComplete: function () {
                    var _a;
                    options.onComplete();
                    (_a = valueTransition.onComplete) === null || _a === void 0 ? void 0 : _a.call(valueTransition);
                } }));
    }
    function set() {
        var _a;
        value.set(target);
        onComplete();
        (_a = valueTransition === null || valueTransition === void 0 ? void 0 : valueTransition.onComplete) === null || _a === void 0 ? void 0 : _a.call(valueTransition);
        return { stop: function () { } };
    }
    return !isOriginAnimatable ||
        !isTargetAnimatable ||
        valueTransition.type === false
        ? set
        : start;
}
function getValueTransition(transition, key) {
    return transition[key] || transition["default"] || transition;
}
/**
 * Start animation on a MotionValue. This function is an interface between
 * Framer Motion and Popmotion
 *
 * @internal
 */
function startAnimation(key, value, target, transition) {
    if (transition === void 0) { transition = {}; }
    return value.start(function (onComplete) {
        var delayTimer;
        var controls;
        var animation = getAnimation(key, value, target, transition, onComplete);
        var delay = getDelayFromTransition(transition, key);
        var start = function () { return (controls = animation()); };
        if (delay) {
            delayTimer = setTimeout(start, (0,_utils_time_conversion_js__WEBPACK_IMPORTED_MODULE_2__.secondsToMilliseconds)(delay));
        }
        else {
            start();
        }
        return function () {
            clearTimeout(delayTimer);
            controls === null || controls === void 0 ? void 0 : controls.stop();
        };
    });
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimatePresence/PresenceChild.js":
/*!****************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimatePresence/PresenceChild.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "PresenceChild": () => (/* binding */ PresenceChild)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../context/PresenceContext.js */ "./node_modules/framer-motion/dist/es/context/PresenceContext.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");




var presenceId = 0;
function getPresenceId() {
    var id = presenceId;
    presenceId++;
    return id;
}
var PresenceChild = function (_a) {
    var children = _a.children, initial = _a.initial, isPresent = _a.isPresent, onExitComplete = _a.onExitComplete, custom = _a.custom, presenceAffectsLayout = _a.presenceAffectsLayout;
    var presenceChildren = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__.useConstant)(newChildrenMap);
    var id = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__.useConstant)(getPresenceId);
    var context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () { return ({
        id: id,
        initial: initial,
        isPresent: isPresent,
        custom: custom,
        onExitComplete: function (childId) {
            presenceChildren.set(childId, true);
            var allComplete = true;
            presenceChildren.forEach(function (isComplete) {
                if (!isComplete)
                    allComplete = false;
            });
            allComplete && (onExitComplete === null || onExitComplete === void 0 ? void 0 : onExitComplete());
        },
        register: function (childId) {
            presenceChildren.set(childId, false);
            return function () { return presenceChildren.delete(childId); };
        },
    }); }, 
    /**
     * If the presence of a child affects the layout of the components around it,
     * we want to make a new context value to ensure they get re-rendered
     * so they can detect that layout change.
     */
    presenceAffectsLayout ? undefined : [isPresent]);
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () {
        presenceChildren.forEach(function (_, key) { return presenceChildren.set(key, false); });
    }, [isPresent]);
    /**
     * If there's no `motion` components to fire exit animations, we want to remove this
     * component immediately.
     */
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        !isPresent && !presenceChildren.size && (onExitComplete === null || onExitComplete === void 0 ? void 0 : onExitComplete());
    }, [isPresent]);
    return ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_2__.PresenceContext.Provider, { value: context }, children));
};
function newChildrenMap() {
    return new Map();
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimatePresence/index.js":
/*!********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimatePresence/index.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "AnimatePresence": () => (/* binding */ AnimatePresence)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../context/SharedLayoutContext.js */ "./node_modules/framer-motion/dist/es/context/SharedLayoutContext.js");
/* harmony import */ var _utils_use_force_update_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/use-force-update.js */ "./node_modules/framer-motion/dist/es/utils/use-force-update.js");
/* harmony import */ var _PresenceChild_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./PresenceChild.js */ "./node_modules/framer-motion/dist/es/components/AnimatePresence/PresenceChild.js");






function getChildKey(child) {
    return child.key || "";
}
function updateChildLookup(children, allChildren) {
    var seenChildren =  true ? new Set() : 0;
    children.forEach(function (child) {
        var key = getChildKey(child);
        if ( true && seenChildren) {
            if (seenChildren.has(key)) {
                console.warn("Children of AnimatePresence require unique keys. \"" + key + "\" is a duplicate.");
            }
            seenChildren.add(key);
        }
        allChildren.set(key, child);
    });
}
function onlyElements(children) {
    var filtered = [];
    // We use forEach here instead of map as map mutates the component key by preprending `.$`
    react__WEBPACK_IMPORTED_MODULE_0__.Children.forEach(children, function (child) {
        if ((0,react__WEBPACK_IMPORTED_MODULE_0__.isValidElement)(child))
            filtered.push(child);
    });
    return filtered;
}
/**
 * `AnimatePresence` enables the animation of components that have been removed from the tree.
 *
 * When adding/removing more than a single child, every child **must** be given a unique `key` prop.
 *
 * @library
 *
 * Any `Frame` components that have an `exit` property defined will animate out when removed from
 * the tree.
 *
 * ```jsx
 * import { Frame, AnimatePresence } from 'framer'
 *
 * // As items are added and removed from `items`
 * export function Items({ items }) {
 *   return (
 *     <AnimatePresence>
 *       {items.map(item => (
 *         <Frame
 *           key={item.id}
 *           initial={{ opacity: 0 }}
 *           animate={{ opacity: 1 }}
 *           exit={{ opacity: 0 }}
 *         />
 *       ))}
 *     </AnimatePresence>
 *   )
 * }
 * ```
 *
 * You can sequence exit animations throughout a tree using variants.
 *
 * @motion
 *
 * Any `motion` components that have an `exit` property defined will animate out when removed from
 * the tree.
 *
 * ```jsx
 * import { motion, AnimatePresence } from 'framer-motion'
 *
 * export const Items = ({ items }) => (
 *   <AnimatePresence>
 *     {items.map(item => (
 *       <motion.div
 *         key={item.id}
 *         initial={{ opacity: 0 }}
 *         animate={{ opacity: 1 }}
 *         exit={{ opacity: 0 }}
 *       />
 *     ))}
 *   </AnimatePresence>
 * )
 * ```
 *
 * You can sequence exit animations throughout a tree using variants.
 *
 * If a child contains multiple `motion` components with `exit` props, it will only unmount the child
 * once all `motion` components have finished animating out. Likewise, any components using
 * `usePresence` all need to call `safeToRemove`.
 *
 * @public
 */
var AnimatePresence = function (_a) {
    var children = _a.children, custom = _a.custom, _b = _a.initial, initial = _b === void 0 ? true : _b, onExitComplete = _a.onExitComplete, exitBeforeEnter = _a.exitBeforeEnter, _c = _a.presenceAffectsLayout, presenceAffectsLayout = _c === void 0 ? true : _c;
    // We want to force a re-render once all exiting animations have finished. We
    // either use a local forceRender function, or one from a parent context if it exists.
    var forceRender = (0,_utils_use_force_update_js__WEBPACK_IMPORTED_MODULE_1__.useForceUpdate)();
    var layoutContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.SharedLayoutContext);
    if ((0,_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.isSharedLayout)(layoutContext)) {
        forceRender = layoutContext.forceUpdate;
    }
    var isInitialRender = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(true);
    // Filter out any children that aren't ReactElements. We can only track ReactElements with a props.key
    var filteredChildren = onlyElements(children);
    // Keep a living record of the children we're actually rendering so we
    // can diff to figure out which are entering and exiting
    var presentChildren = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(filteredChildren);
    // A lookup table to quickly reference components by key
    var allChildren = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(new Map())
        .current;
    // A living record of all currently exiting components.
    var exiting = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(new Set()).current;
    updateChildLookup(filteredChildren, allChildren);
    // If this is the initial component render, just deal with logic surrounding whether
    // we play onMount animations or not.
    if (isInitialRender.current) {
        isInitialRender.current = false;
        return ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, filteredChildren.map(function (child) { return ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_PresenceChild_js__WEBPACK_IMPORTED_MODULE_3__.PresenceChild, { key: getChildKey(child), isPresent: true, initial: initial ? undefined : false, presenceAffectsLayout: presenceAffectsLayout }, child)); })));
    }
    // If this is a subsequent render, deal with entering and exiting children
    var childrenToRender = (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__spread)(filteredChildren);
    // Diff the keys of the currently-present and target children to update our
    // exiting list.
    var presentKeys = presentChildren.current.map(getChildKey);
    var targetKeys = filteredChildren.map(getChildKey);
    // Diff the present children with our target children and mark those that are exiting
    var numPresent = presentKeys.length;
    for (var i = 0; i < numPresent; i++) {
        var key = presentKeys[i];
        if (targetKeys.indexOf(key) === -1) {
            exiting.add(key);
        }
        else {
            // In case this key has re-entered, remove from the exiting list
            exiting.delete(key);
        }
    }
    // If we currently have exiting children, and we're deferring rendering incoming children
    // until after all current children have exiting, empty the childrenToRender array
    if (exitBeforeEnter && exiting.size) {
        childrenToRender = [];
    }
    // Loop through all currently exiting components and clone them to overwrite `animate`
    // with any `exit` prop they might have defined.
    exiting.forEach(function (key) {
        // If this component is actually entering again, early return
        if (targetKeys.indexOf(key) !== -1)
            return;
        var child = allChildren.get(key);
        if (!child)
            return;
        var insertionIndex = presentKeys.indexOf(key);
        var onExit = function () {
            allChildren.delete(key);
            exiting.delete(key);
            // Remove this child from the present children
            var removeIndex = presentChildren.current.findIndex(function (presentChild) { return presentChild.key === key; });
            presentChildren.current.splice(removeIndex, 1);
            // Defer re-rendering until all exiting children have indeed left
            if (!exiting.size) {
                presentChildren.current = filteredChildren;
                forceRender();
                onExitComplete && onExitComplete();
            }
        };
        childrenToRender.splice(insertionIndex, 0, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_PresenceChild_js__WEBPACK_IMPORTED_MODULE_3__.PresenceChild, { key: getChildKey(child), isPresent: false, onExitComplete: onExit, custom: custom, presenceAffectsLayout: presenceAffectsLayout }, child));
    });
    // Add `MotionContext` even to children that don't need it to ensure we're rendering
    // the same tree between renders
    childrenToRender = childrenToRender.map(function (child) {
        var key = child.key;
        return exiting.has(key) ? (child) : ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_PresenceChild_js__WEBPACK_IMPORTED_MODULE_3__.PresenceChild, { key: getChildKey(child), isPresent: true, presenceAffectsLayout: presenceAffectsLayout }, child));
    });
    presentChildren.current = childrenToRender;
    if ( true &&
        exitBeforeEnter &&
        childrenToRender.length > 1) {
        console.warn("You're attempting to animate multiple children within AnimatePresence, but its exitBeforeEnter prop is set to true. This will lead to odd visual behaviour.");
    }
    return ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, exiting.size
        ? childrenToRender
        : childrenToRender.map(function (child) { return (0,react__WEBPACK_IMPORTED_MODULE_0__.cloneElement)(child); })));
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimatePresence/use-presence.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimatePresence/use-presence.js ***!
  \***************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isPresent": () => (/* binding */ isPresent),
/* harmony export */   "useIsPresent": () => (/* binding */ useIsPresent),
/* harmony export */   "usePresence": () => (/* binding */ usePresence)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../context/PresenceContext.js */ "./node_modules/framer-motion/dist/es/context/PresenceContext.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");




/**
 * When a component is the child of `AnimatePresence`, it can use `usePresence`
 * to access information about whether it's still present in the React tree.
 *
 * ```jsx
 * import { usePresence } from "framer-motion"
 *
 * export const Component = () => {
 *   const [isPresent, safeToRemove] = usePresence()
 *
 *   useEffect(() => {
 *     !isPresent && setTimeout(safeToRemove, 1000)
 *   }, [isPresent])
 *
 *   return <div />
 * }
 * ```
 *
 * If `isPresent` is `false`, it means that a component has been removed the tree, but
 * `AnimatePresence` won't really remove it until `safeToRemove` has been called.
 *
 * @public
 */
function usePresence() {
    var context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_1__.PresenceContext);
    if (context === null)
        return [true, null];
    var isPresent = context.isPresent, onExitComplete = context.onExitComplete, register = context.register;
    // It's safe to call the following hooks conditionally (after an early return) because the context will always
    // either be null or non-null for the lifespan of the component.
    // Replace with useOpaqueId when released in React
    var id = useUniqueId();
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () { return register(id); }, []);
    var safeToRemove = function () { return onExitComplete === null || onExitComplete === void 0 ? void 0 : onExitComplete(id); };
    return !isPresent && onExitComplete ? [false, safeToRemove] : [true];
}
/**
 * Similar to `usePresence`, except `useIsPresent` simply returns whether or not the component is present.
 * There is no `safeToRemove` function.
 *
 * ```jsx
 * import { useIsPresent } from "framer-motion"
 *
 * export const Component = () => {
 *   const isPresent = useIsPresent()
 *
 *   useEffect(() => {
 *     !isPresent && console.log("I've been removed!")
 *   }, [isPresent])
 *
 *   return <div />
 * }
 * ```
 *
 * @public
 */
function useIsPresent() {
    return isPresent((0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_1__.PresenceContext));
}
function isPresent(context) {
    return context === null ? true : context.isPresent;
}
var counter = 0;
var incrementId = function () { return counter++; };
var useUniqueId = function () { return (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_2__.useConstant)(incrementId); };




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/index.js":
/*!************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/index.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "AnimateSharedLayout": () => (/* binding */ AnimateSharedLayout)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../context/MotionContext/index.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/index.js");
/* harmony import */ var _types_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./types.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js");
/* harmony import */ var _utils_batcher_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/batcher.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/batcher.js");
/* harmony import */ var _context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../context/SharedLayoutContext.js */ "./node_modules/framer-motion/dist/es/context/SharedLayoutContext.js");
/* harmony import */ var _utils_stack_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/stack.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/stack.js");
/* harmony import */ var _utils_rotate_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/rotate.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/rotate.js");









/**
 * @public
 */
var AnimateSharedLayout = /** @class */ (function (_super) {
    (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__extends)(AnimateSharedLayout, _super);
    function AnimateSharedLayout() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        /**
         * A list of all the children in the shared layout
         */
        _this.children = new Set();
        /**
         * As animate components with a defined `layoutId` are added/removed to the tree,
         * we store them in order. When one is added, it will animate out from the
         * previous one, and when it's removed, it'll animate to the previous one.
         */
        _this.stacks = new Map();
        /**
         * Track whether the component has mounted. If it hasn't, the presence of added children
         * are set to Present, whereas if it has they're considered Entering
         */
        _this.hasMounted = false;
        /**
         * Track whether we already have an update scheduled. If we don't, we'll run snapshots
         * and schedule one.
         */
        _this.updateScheduled = false;
        /**
         * Tracks whether we already have a render scheduled. If we don't, we'll force one with this.forceRender
         */
        _this.renderScheduled = false;
        /**
         * The methods provided to all children in the shared layout tree.
         */
        _this.syncContext = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, (0,_utils_batcher_js__WEBPACK_IMPORTED_MODULE_2__.createBatcher)()), { syncUpdate: function (force) { return _this.scheduleUpdate(force); }, forceUpdate: function () {
                // By copying syncContext to itself, when this component re-renders it'll also re-render
                // all children subscribed to the SharedLayout context.
                _this.syncContext = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, _this.syncContext);
                _this.scheduleUpdate(true);
            }, register: function (child) { return _this.addChild(child); }, remove: function (child) { return _this.removeChild(child); } });
        return _this;
    }
    AnimateSharedLayout.prototype.componentDidMount = function () {
        this.hasMounted = true;
    };
    AnimateSharedLayout.prototype.componentDidUpdate = function () {
        this.startLayoutAnimation();
    };
    AnimateSharedLayout.prototype.shouldComponentUpdate = function () {
        this.renderScheduled = true;
        return true;
    };
    AnimateSharedLayout.prototype.startLayoutAnimation = function () {
        var _this = this;
        /**
         * Reset update and render scheduled status
         */
        this.renderScheduled = this.updateScheduled = false;
        var type = this.props.type;
        /**
         * Update presence metadata based on the latest AnimatePresence status.
         * This is a kind of goofy way of dealing with this, perhaps there's a better model to find.
         */
        this.children.forEach(function (child) {
            if (!child.isPresent) {
                child.presence = _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Exiting;
            }
            else if (child.presence !== _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Entering) {
                child.presence =
                    child.presence === _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Exiting
                        ? _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Entering
                        : _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Present;
            }
        });
        this.updateStacks();
        /**
         * Create a handler which we can use to flush the children animations
         */
        var handler = {
            measureLayout: function (child) { return child.updateLayoutMeasurement(); },
            layoutReady: function (child) {
                if (child.getLayoutId() !== undefined) {
                    var stack = _this.getStack(child);
                    stack.animate(child, type === "crossfade");
                }
                else {
                    child.notifyLayoutReady();
                }
            },
            parent: this.context.visualElement,
        };
        /**
         * Shared layout animations can be used without the AnimateSharedLayout wrapping component.
         * This requires some co-ordination across components to stop layout thrashing
         * and ensure measurements are taken at the correct time.
         *
         * Here we use that same mechanism of schedule/flush.
         */
        this.children.forEach(function (child) { return _this.syncContext.add(child); });
        this.syncContext.flush(handler);
        /**
         * Clear snapshots so subsequent rerenders don't retain memory of outgoing components
         */
        this.stacks.forEach(function (stack) { return stack.clearSnapshot(); });
    };
    AnimateSharedLayout.prototype.updateStacks = function () {
        this.stacks.forEach(function (stack) { return stack.updateLeadAndFollow(); });
    };
    AnimateSharedLayout.prototype.scheduleUpdate = function (force) {
        if (force === void 0) { force = false; }
        if (!(force || !this.updateScheduled))
            return;
        /**
         * Flag we've scheduled an update
         */
        this.updateScheduled = true;
        /**
         * Write: Reset rotation transforms so bounding boxes can be accurately measured.
         */
        this.children.forEach(function (child) { return (0,_utils_rotate_js__WEBPACK_IMPORTED_MODULE_4__.resetRotate)(child); });
        /**
         * Read: Snapshot children
         */
        this.children.forEach(function (child) { return child.snapshotViewportBox(); });
        /**
         * Every child keeps a local snapshot, but we also want to record
         * snapshots of the visible children as, if they're are being removed
         * in this render, we can still access them.
         *
         * TODO: What would be better here is doing a single loop where we
         * only snapshotViewportBoxes of undefined layoutIds and then one for each stack
         */
        this.stacks.forEach(function (stack) { return stack.updateSnapshot(); });
        /**
         * Force a rerender by setting state if we aren't already going to render.
         */
        if (force || !this.renderScheduled) {
            this.renderScheduled = true;
            this.forceUpdate();
        }
    };
    AnimateSharedLayout.prototype.addChild = function (child) {
        this.children.add(child);
        this.addToStack(child);
        child.presence = this.hasMounted ? _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Entering : _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Present;
    };
    AnimateSharedLayout.prototype.removeChild = function (child) {
        this.scheduleUpdate();
        this.children.delete(child);
        this.removeFromStack(child);
    };
    AnimateSharedLayout.prototype.addToStack = function (child) {
        var stack = this.getStack(child);
        stack === null || stack === void 0 ? void 0 : stack.add(child);
    };
    AnimateSharedLayout.prototype.removeFromStack = function (child) {
        var stack = this.getStack(child);
        stack === null || stack === void 0 ? void 0 : stack.remove(child);
    };
    /**
     * Return a stack of animate children based on the provided layoutId.
     * Will create a stack if none currently exists with that layoutId.
     */
    AnimateSharedLayout.prototype.getStack = function (child) {
        var id = child.getLayoutId();
        if (id === undefined)
            return;
        // Create stack if it doesn't already exist
        !this.stacks.has(id) && this.stacks.set(id, (0,_utils_stack_js__WEBPACK_IMPORTED_MODULE_5__.layoutStack)());
        return this.stacks.get(id);
    };
    AnimateSharedLayout.prototype.render = function () {
        return ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_6__.SharedLayoutContext.Provider, { value: this.syncContext }, this.props.children));
    };
    AnimateSharedLayout.contextType = _context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_7__.MotionContext;
    return AnimateSharedLayout;
}(react__WEBPACK_IMPORTED_MODULE_0__.Component));




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js":
/*!************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Presence": () => (/* binding */ Presence),
/* harmony export */   "VisibilityAction": () => (/* binding */ VisibilityAction)
/* harmony export */ });
var Presence;
(function (Presence) {
    Presence[Presence["Entering"] = 0] = "Entering";
    Presence[Presence["Present"] = 1] = "Present";
    Presence[Presence["Exiting"] = 2] = "Exiting";
})(Presence || (Presence = {}));
var VisibilityAction;
(function (VisibilityAction) {
    VisibilityAction[VisibilityAction["Hide"] = 0] = "Hide";
    VisibilityAction[VisibilityAction["Show"] = 1] = "Show";
})(VisibilityAction || (VisibilityAction = {}));




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/batcher.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/batcher.js ***!
  \********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createBatcher": () => (/* binding */ createBatcher)
/* harmony export */ });
/* harmony import */ var _types_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../types.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js");


/**
 * Default handlers for batching VisualElements
 */
var defaultHandler = {
    measureLayout: function (child) { return child.updateLayoutMeasurement(); },
    layoutReady: function (child) { return child.notifyLayoutReady(); },
};
/**
 * Create a batcher to process VisualElements
 */
function createBatcher() {
    var queue = new Set();
    return {
        add: function (child) { return queue.add(child); },
        flush: function (_a) {
            var _b = _a === void 0 ? defaultHandler : _a, measureLayout = _b.measureLayout, layoutReady = _b.layoutReady, parent = _b.parent;
            var order = Array.from(queue).sort(function (a, b) { return a.depth - b.depth; });
            var resetAndMeasure = function () {
                /**
                 * Write: Reset any transforms on children elements so we can read their actual layout
                 */
                order.forEach(function (child) { return child.resetTransform(); });
                /**
                 * Read: Measure the actual layout
                 */
                order.forEach(measureLayout);
            };
            parent
                ? parent.withoutTransform(resetAndMeasure)
                : resetAndMeasure();
            /**
             * Write: Notify the VisualElements they're ready for further write operations.
             */
            order.forEach(layoutReady);
            /**
             * After all children have started animating, ensure any Entering components are set to Present.
             * If we add deferred animations (set up all animations and then start them in two loops) this
             * could be moved to the start loop. But it needs to happen after all the animations configs
             * are generated in AnimateSharedLayout as this relies on presence data
             */
            order.forEach(function (child) {
                if (child.isPresent)
                    child.presence = _types_js__WEBPACK_IMPORTED_MODULE_0__.Presence.Present;
            });
            queue.clear();
        },
    };
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/crossfader.js":
/*!***********************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/crossfader.js ***!
  \***********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createCrossfader": () => (/* binding */ createCrossfader)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/mix.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/easing/index.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/progress.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/mix-color.js");
/* harmony import */ var framesync__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! framesync */ "./node_modules/framesync/dist/es/index.js");
/* harmony import */ var _value_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../value/index.js */ "./node_modules/framer-motion/dist/es/value/index.js");
/* harmony import */ var _animation_animate_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../animation/animate.js */ "./node_modules/framer-motion/dist/es/animation/animate.js");






function createCrossfader() {
    /**
     * The current state of the crossfade as a value between 0 and 1
     */
    var progress = (0,_value_index_js__WEBPACK_IMPORTED_MODULE_1__.motionValue)(1);
    var options = {
        lead: undefined,
        follow: undefined,
        crossfadeOpacity: false,
        preserveFollowOpacity: false,
    };
    var leadState = {};
    var followState = {};
    /**
     *
     */
    var isActive = false;
    /**
     *
     */
    var finalCrossfadeFrame = null;
    /**
     * Framestamp of the last frame we updated values at.
     */
    var prevUpdate = 0;
    function startCrossfadeAnimation(target, transition) {
        var lead = options.lead, follow = options.follow;
        isActive = true;
        finalCrossfadeFrame = null;
        return (0,_animation_animate_js__WEBPACK_IMPORTED_MODULE_2__.animate)(progress, target, (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({}, transition), { onUpdate: function () {
                lead && lead.scheduleRender();
                follow && follow.scheduleRender();
            }, onComplete: function () {
                isActive = false;
                /**
                 * If the crossfade animation is no longer active, flag a frame
                 * that we're still allowed to crossfade
                 */
                finalCrossfadeFrame = (0,framesync__WEBPACK_IMPORTED_MODULE_0__.getFrameData)().timestamp;
            } }));
    }
    function updateCrossfade() {
        var _a, _b;
        /**
         * We only want to compute the crossfade once per frame, so we
         * compare the previous update framestamp with the current frame
         * and early return if they're the same.
         */
        var timestamp = (0,framesync__WEBPACK_IMPORTED_MODULE_0__.getFrameData)().timestamp;
        var lead = options.lead, follow = options.follow;
        if (timestamp === prevUpdate || !lead)
            return;
        prevUpdate = timestamp;
        /**
         * Merge each component's latest values into our crossfaded state
         * before crossfading.
         */
        var latestLeadValues = lead.getLatestValues();
        Object.assign(leadState, latestLeadValues);
        var latestFollowValues = follow
            ? follow.getLatestValues()
            : options.prevValues;
        Object.assign(followState, latestFollowValues);
        var p = progress.get();
        /**
         * Crossfade the opacity between the two components. This will result
         * in a different opacity for each component.
         */
        var leadTargetOpacity = (_a = latestLeadValues.opacity) !== null && _a !== void 0 ? _a : 1;
        var followTargetOpacity = (_b = latestFollowValues === null || latestFollowValues === void 0 ? void 0 : latestFollowValues.opacity) !== null && _b !== void 0 ? _b : 1;
        if (options.crossfadeOpacity && follow) {
            leadState.opacity = (0,popmotion__WEBPACK_IMPORTED_MODULE_4__.mix)(0, leadTargetOpacity, easeCrossfadeIn(p));
            followState.opacity = options.preserveFollowOpacity
                ? followTargetOpacity
                : (0,popmotion__WEBPACK_IMPORTED_MODULE_4__.mix)(followTargetOpacity, 0, easeCrossfadeOut(p));
        }
        else if (!follow) {
            leadState.opacity = (0,popmotion__WEBPACK_IMPORTED_MODULE_4__.mix)(followTargetOpacity, leadTargetOpacity, p);
        }
        mixValues(leadState, followState, latestLeadValues, latestFollowValues || {}, Boolean(follow), p);
    }
    return {
        isActive: function () {
            return leadState &&
                (isActive || (0,framesync__WEBPACK_IMPORTED_MODULE_0__.getFrameData)().timestamp === finalCrossfadeFrame);
        },
        fromLead: function (transition) {
            return startCrossfadeAnimation(0, transition);
        },
        toLead: function (transition) {
            progress.set(options.follow ? 1 - progress.get() : 0);
            return startCrossfadeAnimation(1, transition);
        },
        reset: function () { return progress.set(1); },
        stop: function () { return progress.stop(); },
        getCrossfadeState: function (element) {
            updateCrossfade();
            if (element === options.lead) {
                return leadState;
            }
            else if (element === options.follow) {
                return followState;
            }
        },
        setOptions: function (newOptions) {
            options = newOptions;
            leadState = {};
            followState = {};
        },
        getLatestValues: function () {
            return leadState;
        },
    };
}
var easeCrossfadeIn = compress(0, 0.5, popmotion__WEBPACK_IMPORTED_MODULE_5__.circOut);
var easeCrossfadeOut = compress(0.5, 0.95, popmotion__WEBPACK_IMPORTED_MODULE_5__.linear);
function compress(min, max, easing) {
    return function (p) {
        // Could replace ifs with clamp
        if (p < min)
            return 0;
        if (p > max)
            return 1;
        return easing((0,popmotion__WEBPACK_IMPORTED_MODULE_6__.progress)(min, max, p));
    };
}
var borders = ["TopLeft", "TopRight", "BottomLeft", "BottomRight"];
var numBorders = borders.length;
function mixValues(leadState, followState, latestLeadValues, latestFollowValues, hasFollowElement, p) {
    /**
     * Mix border radius
     */
    for (var i = 0; i < numBorders; i++) {
        var borderLabel = "border" + borders[i] + "Radius";
        var followRadius = getRadius(latestFollowValues, borderLabel);
        var leadRadius = getRadius(latestLeadValues, borderLabel);
        if (followRadius === undefined && leadRadius === undefined)
            continue;
        followRadius || (followRadius = 0);
        leadRadius || (leadRadius = 0);
        /**
         * Currently we're only crossfading between numerical border radius.
         * It would be possible to crossfade between percentages for a little
         * extra bundle size.
         */
        if (typeof followRadius === "number" &&
            typeof leadRadius === "number") {
            var radius = (0,popmotion__WEBPACK_IMPORTED_MODULE_4__.mix)(followRadius, leadRadius, p);
            leadState[borderLabel] = followState[borderLabel] = radius;
        }
    }
    /**
     * Mix rotation
     */
    if (latestFollowValues.rotate || latestLeadValues.rotate) {
        var rotate = (0,popmotion__WEBPACK_IMPORTED_MODULE_4__.mix)(latestFollowValues.rotate || 0, latestLeadValues.rotate || 0, p);
        leadState.rotate = followState.rotate = rotate;
    }
    /**
     * We only want to mix the background color if there's a follow element
     * that we're not crossfading opacity between. For instance with switch
     * AnimateSharedLayout animations, this helps the illusion of a continuous
     * element being animated but also cuts down on the number of paints triggered
     * for elements where opacity is doing that work for us.
     */
    if (!hasFollowElement &&
        latestLeadValues.backgroundColor &&
        latestFollowValues.backgroundColor) {
        /**
         * This isn't ideal performance-wise as mixColor is creating a new function every frame.
         * We could probably create a mixer that runs at the start of the animation but
         * the idea behind the crossfader is that it runs dynamically between two potentially
         * changing targets (ie opacity or borderRadius may be animating independently via variants)
         */
        leadState.backgroundColor = followState.backgroundColor = (0,popmotion__WEBPACK_IMPORTED_MODULE_7__.mixColor)(latestFollowValues.backgroundColor, latestLeadValues.backgroundColor)(p);
    }
}
function getRadius(values, radiusName) {
    var _a;
    return (_a = values[radiusName]) !== null && _a !== void 0 ? _a : values.borderRadius;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/rotate.js":
/*!*******************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/rotate.js ***!
  \*******************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "resetRotate": () => (/* binding */ resetRotate)
/* harmony export */ });
/* harmony import */ var _render_html_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../render/html/utils/transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js");


function resetRotate(child) {
    // If there's no detected rotation values, we can early return without a forced render.
    var hasRotate = false;
    // Keep a record of all the values we've reset
    var resetValues = {};
    // Check the rotate value of all axes and reset to 0
    for (var i = 0; i < _render_html_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__.transformAxes.length; i++) {
        var axis = _render_html_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__.transformAxes[i];
        var key = "rotate" + axis;
        // If this rotation doesn't exist as a motion value, then we don't
        // need to reset it
        if (!child.hasValue(key) || child.getStaticValue(key) === 0)
            continue;
        hasRotate = true;
        // Record the rotation and then temporarily set it to 0
        resetValues[key] = child.getStaticValue(key);
        child.setStaticValue(key, 0);
    }
    // If there's no rotation values, we don't need to do any more.
    if (!hasRotate)
        return;
    // Force a render of this element to apply the transform with all rotations
    // set to 0.
    child.syncRender();
    // Put back all the values we reset
    for (var key in resetValues) {
        child.setStaticValue(key, resetValues[key]);
    }
    // Schedule a render for the next frame. This ensures we won't visually
    // see the element with the reset rotate value applied.
    child.scheduleRender();
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/stack.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/stack.js ***!
  \******************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "layoutStack": () => (/* binding */ layoutStack)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _types_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../types.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js");
/* harmony import */ var _gestures_drag_VisualElementDragControls_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../gestures/drag/VisualElementDragControls.js */ "./node_modules/framer-motion/dist/es/gestures/drag/VisualElementDragControls.js");
/* harmony import */ var _crossfader_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./crossfader.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/crossfader.js");





function layoutStack() {
    var stack = new Set();
    var state = { leadIsExiting: false };
    var prevState = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({}, state);
    var prevValues;
    var prevViewportBox;
    var prevDragCursor;
    var crossfader = (0,_crossfader_js__WEBPACK_IMPORTED_MODULE_1__.createCrossfader)();
    var needsCrossfadeAnimation = false;
    function getFollowViewportBox() {
        return state.follow ? state.follow.prevViewportBox : prevViewportBox;
    }
    function getFollowLayout() {
        var _a;
        return (_a = state.follow) === null || _a === void 0 ? void 0 : _a.getLayoutState().layout;
    }
    return {
        add: function (element) {
            element.setCrossfader(crossfader);
            stack.add(element);
            /**
             * Hydrate new element with previous drag position if we have one
             */
            if (prevDragCursor)
                element.prevDragCursor = prevDragCursor;
            if (!state.lead)
                state.lead = element;
        },
        remove: function (element) {
            stack.delete(element);
        },
        getLead: function () { return state.lead; },
        updateSnapshot: function () {
            if (!state.lead)
                return;
            prevValues = crossfader.isActive()
                ? crossfader.getLatestValues()
                : state.lead.getLatestValues();
            prevViewportBox = state.lead.prevViewportBox;
            var dragControls = _gestures_drag_VisualElementDragControls_js__WEBPACK_IMPORTED_MODULE_2__.elementDragControls.get(state.lead);
            if (dragControls && dragControls.isDragging) {
                prevDragCursor = dragControls.cursorProgress;
            }
        },
        clearSnapshot: function () {
            prevDragCursor = prevViewportBox = undefined;
        },
        updateLeadAndFollow: function () {
            var _a;
            prevState = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({}, state);
            var lead;
            var follow;
            var order = Array.from(stack);
            for (var i = order.length; i--; i >= 0) {
                var element = order[i];
                if (lead)
                    follow !== null && follow !== void 0 ? follow : (follow = element);
                lead !== null && lead !== void 0 ? lead : (lead = element);
                if (lead && follow)
                    break;
            }
            state.lead = lead;
            state.follow = follow;
            state.leadIsExiting = ((_a = state.lead) === null || _a === void 0 ? void 0 : _a.presence) === _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Exiting;
            crossfader.setOptions({
                lead: lead,
                follow: follow,
                prevValues: prevValues,
                crossfadeOpacity: (follow === null || follow === void 0 ? void 0 : follow.isPresenceRoot) || (lead === null || lead === void 0 ? void 0 : lead.isPresenceRoot),
            });
            if (prevState.lead !== state.lead ||
                prevState.leadIsExiting !== state.leadIsExiting) {
                needsCrossfadeAnimation = true;
            }
        },
        animate: function (child, shouldCrossfade) {
            if (shouldCrossfade === void 0) { shouldCrossfade = false; }
            if (child === state.lead) {
                if (shouldCrossfade) {
                    /**
                     * Point a lead to itself in case it was previously pointing
                     * to a different visual element
                     */
                    child.pointTo(state.lead);
                }
                else {
                    child.setVisibility(true);
                }
                var config = {};
                if (child.presence === _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Entering) {
                    config.originBox = getFollowViewportBox();
                }
                else if (child.presence === _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Exiting) {
                    config.targetBox = getFollowLayout();
                }
                if (needsCrossfadeAnimation) {
                    needsCrossfadeAnimation = false;
                    var transition = child.getDefaultTransition();
                    child.presence === _types_js__WEBPACK_IMPORTED_MODULE_3__.Presence.Entering
                        ? crossfader.toLead(transition)
                        : crossfader.fromLead(transition);
                }
                child.notifyLayoutReady(config);
            }
            else {
                if (shouldCrossfade) {
                    state.lead && child.pointTo(state.lead);
                }
                else {
                    child.setVisibility(false);
                }
            }
        },
    };
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/context/LayoutGroupContext.js":
/*!**************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/context/LayoutGroupContext.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "LayoutGroupContext": () => (/* binding */ LayoutGroupContext)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


/**
 * @internal
 */
var LayoutGroupContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/context/MotionConfigContext.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MotionConfig": () => (/* binding */ MotionConfig),
/* harmony export */   "MotionConfigContext": () => (/* binding */ MotionConfigContext)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");



/**
 * @public
 */
var MotionConfigContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)({
    transformPagePoint: function (p) { return p; },
    features: [],
    isStatic: false,
});
/**
 * MotionConfig can be used in combination with the `m` component to cut bundle size
 * and dynamically load only the features you use.
 *
 * ```jsx
 * import {
 *   m as motion,
 *   AnimationFeature,
 *   MotionConfig
 * } from "framer-motion"
 *
 * export function App() {
 *   return (
 *     <MotionConfig features={[AnimationFeature]}>
 *       <motion.div animate={{ x: 100 }} />
 *     </MotionConfig>
 *   )
 * }
 * ```
 *
 * @public
 */
function MotionConfig(_a) {
    var children = _a.children, _b = _a.features, features = _b === void 0 ? [] : _b, transition = _a.transition, props = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, ["children", "features", "transition"]);
    var pluginContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(MotionConfigContext);
    var loadedFeatures = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__spread)(new Set((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__spread)(pluginContext.features, features)));
    // We do want to rerender children when the number of loaded features changes
    var value = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () { return ({
        features: loadedFeatures,
        transition: transition || pluginContext.transition,
    }); }, [loadedFeatures.length, transition]);
    // Mutative to prevent triggering rerenders in all listening
    // components every time this component renders
    for (var key in props) {
        value[key] = props[key];
    }
    return ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(MotionConfigContext.Provider, { value: value }, children));
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/context/MotionContext/create.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/context/MotionContext/create.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useCreateMotionContext": () => (/* binding */ useCreateMotionContext)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./index.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/index.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/utils.js");




function useCreateMotionContext(props, isStatic) {
    var _a = (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.getCurrentTreeVariants)(props, (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_index_js__WEBPACK_IMPORTED_MODULE_2__.MotionContext)), initial = _a.initial, animate = _a.animate;
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () { return ({ initial: initial, animate: animate }); }, 
    /**
     * Only break memoisation in static mode
     */
    isStatic
        ? [
            variantLabelsAsDependency(initial),
            variantLabelsAsDependency(animate),
        ]
        : []);
}
function variantLabelsAsDependency(prop) {
    return Array.isArray(prop) ? prop.join(" ") : prop;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/context/MotionContext/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/context/MotionContext/index.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MotionContext": () => (/* binding */ MotionContext),
/* harmony export */   "useVisualElementContext": () => (/* binding */ useVisualElementContext)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


var MotionContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)({});
function useVisualElementContext() {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(MotionContext).visualElement;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/context/MotionContext/utils.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/context/MotionContext/utils.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getCurrentTreeVariants": () => (/* binding */ getCurrentTreeVariants)
/* harmony export */ });
/* harmony import */ var _render_utils_variants_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../render/utils/variants.js */ "./node_modules/framer-motion/dist/es/render/utils/variants.js");


function getCurrentTreeVariants(props, context) {
    if ((0,_render_utils_variants_js__WEBPACK_IMPORTED_MODULE_0__.checkIfControllingVariants)(props)) {
        var initial = props.initial, animate = props.animate;
        return {
            initial: initial === false || (0,_render_utils_variants_js__WEBPACK_IMPORTED_MODULE_0__.isVariantLabel)(initial)
                ? initial
                : undefined,
            animate: (0,_render_utils_variants_js__WEBPACK_IMPORTED_MODULE_0__.isVariantLabel)(animate) ? animate : undefined,
        };
    }
    return props.inherit !== false ? context : {};
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/context/PresenceContext.js":
/*!***********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/context/PresenceContext.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "PresenceContext": () => (/* binding */ PresenceContext)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


/**
 * @public
 */
var PresenceContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/context/SharedLayoutContext.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/context/SharedLayoutContext.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "FramerTreeLayoutContext": () => (/* binding */ FramerTreeLayoutContext),
/* harmony export */   "SharedLayoutContext": () => (/* binding */ SharedLayoutContext),
/* harmony export */   "isSharedLayout": () => (/* binding */ isSharedLayout)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_AnimateSharedLayout_utils_batcher_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../components/AnimateSharedLayout/utils/batcher.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/batcher.js");



var SharedLayoutContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)((0,_components_AnimateSharedLayout_utils_batcher_js__WEBPACK_IMPORTED_MODULE_1__.createBatcher)());
/**
 * @internal
 */
var FramerTreeLayoutContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)((0,_components_AnimateSharedLayout_utils_batcher_js__WEBPACK_IMPORTED_MODULE_1__.createBatcher)());
function isSharedLayout(context) {
    return !!context.forceUpdate;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/events/event-info.js":
/*!*****************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/events/event-info.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "extractEventInfo": () => (/* binding */ extractEventInfo),
/* harmony export */   "getViewportPointFromEvent": () => (/* binding */ getViewportPointFromEvent),
/* harmony export */   "wrapHandler": () => (/* binding */ wrapHandler)
/* harmony export */ });
/* harmony import */ var _gestures_utils_event_type_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../gestures/utils/event-type.js */ "./node_modules/framer-motion/dist/es/gestures/utils/event-type.js");


/**
 * Filters out events not attached to the primary pointer (currently left mouse button)
 * @param eventHandler
 */
function filterPrimaryPointer(eventHandler) {
    return function (event) {
        var isMouseEvent = event instanceof MouseEvent;
        var isPrimaryPointer = !isMouseEvent ||
            (isMouseEvent && event.button === 0);
        if (isPrimaryPointer) {
            eventHandler(event);
        }
    };
}
var defaultPagePoint = { pageX: 0, pageY: 0 };
function pointFromTouch(e, pointType) {
    if (pointType === void 0) { pointType = "page"; }
    var primaryTouch = e.touches[0] || e.changedTouches[0];
    var point = primaryTouch || defaultPagePoint;
    return {
        x: point[pointType + "X"],
        y: point[pointType + "Y"],
    };
}
function pointFromMouse(point, pointType) {
    if (pointType === void 0) { pointType = "page"; }
    return {
        x: point[pointType + "X"],
        y: point[pointType + "Y"],
    };
}
function extractEventInfo(event, pointType) {
    if (pointType === void 0) { pointType = "page"; }
    return {
        point: (0,_gestures_utils_event_type_js__WEBPACK_IMPORTED_MODULE_0__.isTouchEvent)(event)
            ? pointFromTouch(event, pointType)
            : pointFromMouse(event, pointType),
    };
}
function getViewportPointFromEvent(event) {
    return extractEventInfo(event, "client");
}
var wrapHandler = function (handler, shouldFilterPrimaryPointer) {
    if (shouldFilterPrimaryPointer === void 0) { shouldFilterPrimaryPointer = false; }
    var listener = function (event) {
        return handler(event, extractEventInfo(event));
    };
    return shouldFilterPrimaryPointer
        ? filterPrimaryPointer(listener)
        : listener;
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/events/use-dom-event.js":
/*!********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/events/use-dom-event.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "addDomEvent": () => (/* binding */ addDomEvent),
/* harmony export */   "useDomEvent": () => (/* binding */ useDomEvent)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


function addDomEvent(target, eventName, handler, options) {
    target.addEventListener(eventName, handler, options);
    return function () { return target.removeEventListener(eventName, handler, options); };
}
/**
 * Attaches an event listener directly to the provided DOM element.
 *
 * Bypassing React's event system can be desirable, for instance when attaching non-passive
 * event handlers.
 *
 * ```jsx
 * const ref = useRef(null)
 *
 * useDomEvent(ref, 'wheel', onWheel, { passive: false })
 *
 * return <div ref={ref} />
 * ```
 *
 * @param ref - React.RefObject that's been provided to the element you want to bind the listener to.
 * @param eventName - Name of the event you want listen for.
 * @param handler - Function to fire when receiving the event.
 * @param options - Options to pass to `Event.addEventListener`.
 *
 * @public
 */
function useDomEvent(ref, eventName, handler, options) {
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        var element = ref.current;
        if (handler && element) {
            return addDomEvent(element, eventName, handler, options);
        }
    }, [ref, eventName, handler, options]);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/events/use-pointer-event.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/events/use-pointer-event.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "addPointerEvent": () => (/* binding */ addPointerEvent),
/* harmony export */   "usePointerEvent": () => (/* binding */ usePointerEvent)
/* harmony export */ });
/* harmony import */ var _event_info_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./event-info.js */ "./node_modules/framer-motion/dist/es/events/event-info.js");
/* harmony import */ var _use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-dom-event.js */ "./node_modules/framer-motion/dist/es/events/use-dom-event.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils.js */ "./node_modules/framer-motion/dist/es/events/utils.js");




var mouseEventNames = {
    pointerdown: "mousedown",
    pointermove: "mousemove",
    pointerup: "mouseup",
    pointercancel: "mousecancel",
    pointerover: "mouseover",
    pointerout: "mouseout",
    pointerenter: "mouseenter",
    pointerleave: "mouseleave",
};
var touchEventNames = {
    pointerdown: "touchstart",
    pointermove: "touchmove",
    pointerup: "touchend",
    pointercancel: "touchcancel",
};
function getPointerEventName(name) {
    if ((0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.supportsPointerEvents)()) {
        return name;
    }
    else if ((0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.supportsTouchEvents)()) {
        return touchEventNames[name];
    }
    else if ((0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.supportsMouseEvents)()) {
        return mouseEventNames[name];
    }
    return name;
}
function addPointerEvent(target, eventName, handler, options) {
    return (0,_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__.addDomEvent)(target, getPointerEventName(eventName), (0,_event_info_js__WEBPACK_IMPORTED_MODULE_2__.wrapHandler)(handler, eventName === "pointerdown"), options);
}
function usePointerEvent(ref, eventName, handler, options) {
    return (0,_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__.useDomEvent)(ref, getPointerEventName(eventName), handler && (0,_event_info_js__WEBPACK_IMPORTED_MODULE_2__.wrapHandler)(handler, eventName === "pointerdown"), options);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/events/utils.js":
/*!************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/events/utils.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "supportsMouseEvents": () => (/* binding */ supportsMouseEvents),
/* harmony export */   "supportsPointerEvents": () => (/* binding */ supportsPointerEvents),
/* harmony export */   "supportsTouchEvents": () => (/* binding */ supportsTouchEvents)
/* harmony export */ });
var isBrowser = typeof window !== "undefined";
// We check for event support via functions in case they've been mocked by a testing suite.
var supportsPointerEvents = function () {
    return isBrowser && window.onpointerdown === null;
};
var supportsTouchEvents = function () {
    return isBrowser && window.ontouchstart === null;
};
var supportsMouseEvents = function () {
    return isBrowser && window.onmousedown === null;
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/PanSession.js":
/*!*******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/PanSession.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "PanSession": () => (/* binding */ PanSession)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/distance.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/pipe.js");
/* harmony import */ var _utils_event_type_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/event-type.js */ "./node_modules/framer-motion/dist/es/gestures/utils/event-type.js");
/* harmony import */ var _events_event_info_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../events/event-info.js */ "./node_modules/framer-motion/dist/es/events/event-info.js");
/* harmony import */ var framesync__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! framesync */ "./node_modules/framesync/dist/es/index.js");
/* harmony import */ var _utils_time_conversion_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../utils/time-conversion.js */ "./node_modules/framer-motion/dist/es/utils/time-conversion.js");
/* harmony import */ var _events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../events/use-pointer-event.js */ "./node_modules/framer-motion/dist/es/events/use-pointer-event.js");








/**
 * @internal
 */
var PanSession = /** @class */ (function () {
    function PanSession(event, handlers, _a) {
        var _this = this;
        var transformPagePoint = (_a === void 0 ? {} : _a).transformPagePoint;
        /**
         * @internal
         */
        this.startEvent = null;
        /**
         * @internal
         */
        this.lastMoveEvent = null;
        /**
         * @internal
         */
        this.lastMoveEventInfo = null;
        /**
         * @internal
         */
        this.handlers = {};
        this.updatePoint = function () {
            if (!(_this.lastMoveEvent && _this.lastMoveEventInfo))
                return;
            var info = getPanInfo(_this.lastMoveEventInfo, _this.history);
            var isPanStarted = _this.startEvent !== null;
            // Only start panning if the offset is larger than 3 pixels. If we make it
            // any larger than this we'll want to reset the pointer history
            // on the first update to avoid visual snapping to the cursoe.
            var isDistancePastThreshold = (0,popmotion__WEBPACK_IMPORTED_MODULE_1__.distance)(info.offset, { x: 0, y: 0 }) >= 3;
            if (!isPanStarted && !isDistancePastThreshold)
                return;
            var point = info.point;
            var timestamp = (0,framesync__WEBPACK_IMPORTED_MODULE_0__.getFrameData)().timestamp;
            _this.history.push((0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)({}, point), { timestamp: timestamp }));
            var _a = _this.handlers, onStart = _a.onStart, onMove = _a.onMove;
            if (!isPanStarted) {
                onStart && onStart(_this.lastMoveEvent, info);
                _this.startEvent = _this.lastMoveEvent;
            }
            onMove && onMove(_this.lastMoveEvent, info);
        };
        this.handlePointerMove = function (event, info) {
            _this.lastMoveEvent = event;
            _this.lastMoveEventInfo = transformPoint(info, _this.transformPagePoint);
            // Because Safari doesn't trigger mouseup events when it's above a `<select>`
            if ((0,_utils_event_type_js__WEBPACK_IMPORTED_MODULE_3__.isMouseEvent)(event) && event.buttons === 0) {
                _this.handlePointerUp(event, info);
                return;
            }
            // Throttle mouse move event to once per frame
            framesync__WEBPACK_IMPORTED_MODULE_0__["default"].update(_this.updatePoint, true);
        };
        this.handlePointerUp = function (event, info) {
            _this.end();
            var onEnd = _this.handlers.onEnd;
            if (!onEnd || !_this.startEvent)
                return;
            var panInfo = getPanInfo(transformPoint(info, _this.transformPagePoint), _this.history);
            onEnd && onEnd(event, panInfo);
        };
        // If we have more than one touch, don't start detecting this gesture
        if ((0,_utils_event_type_js__WEBPACK_IMPORTED_MODULE_3__.isTouchEvent)(event) && event.touches.length > 1)
            return;
        this.handlers = handlers;
        this.transformPagePoint = transformPagePoint;
        var info = (0,_events_event_info_js__WEBPACK_IMPORTED_MODULE_4__.extractEventInfo)(event);
        var initialInfo = transformPoint(info, this.transformPagePoint);
        var point = initialInfo.point;
        var timestamp = (0,framesync__WEBPACK_IMPORTED_MODULE_0__.getFrameData)().timestamp;
        this.history = [(0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)({}, point), { timestamp: timestamp })];
        var onSessionStart = handlers.onSessionStart;
        onSessionStart &&
            onSessionStart(event, getPanInfo(initialInfo, this.history));
        this.removeListeners = (0,popmotion__WEBPACK_IMPORTED_MODULE_5__.pipe)((0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_6__.addPointerEvent)(window, "pointermove", this.handlePointerMove), (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_6__.addPointerEvent)(window, "pointerup", this.handlePointerUp), (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_6__.addPointerEvent)(window, "pointercancel", this.handlePointerUp));
    }
    PanSession.prototype.updateHandlers = function (handlers) {
        this.handlers = handlers;
    };
    PanSession.prototype.end = function () {
        this.removeListeners && this.removeListeners();
        framesync__WEBPACK_IMPORTED_MODULE_0__.cancelSync.update(this.updatePoint);
    };
    return PanSession;
}());
function transformPoint(info, transformPagePoint) {
    return transformPagePoint ? { point: transformPagePoint(info.point) } : info;
}
function subtractPoint(a, b) {
    return { x: a.x - b.x, y: a.y - b.y };
}
function getPanInfo(_a, history) {
    var point = _a.point;
    return {
        point: point,
        delta: subtractPoint(point, lastDevicePoint(history)),
        offset: subtractPoint(point, startDevicePoint(history)),
        velocity: getVelocity(history, 0.1),
    };
}
function startDevicePoint(history) {
    return history[0];
}
function lastDevicePoint(history) {
    return history[history.length - 1];
}
function getVelocity(history, timeDelta) {
    if (history.length < 2) {
        return { x: 0, y: 0 };
    }
    var i = history.length - 1;
    var timestampedPoint = null;
    var lastPoint = lastDevicePoint(history);
    while (i >= 0) {
        timestampedPoint = history[i];
        if (lastPoint.timestamp - timestampedPoint.timestamp >
            (0,_utils_time_conversion_js__WEBPACK_IMPORTED_MODULE_7__.secondsToMilliseconds)(timeDelta)) {
            break;
        }
        i--;
    }
    if (!timestampedPoint) {
        return { x: 0, y: 0 };
    }
    var time = (lastPoint.timestamp - timestampedPoint.timestamp) / 1000;
    if (time === 0) {
        return { x: 0, y: 0 };
    }
    var currentVelocity = {
        x: (lastPoint.x - timestampedPoint.x) / time,
        y: (lastPoint.y - timestampedPoint.y) / time,
    };
    if (currentVelocity.x === Infinity) {
        currentVelocity.x = 0;
    }
    if (currentVelocity.y === Infinity) {
        currentVelocity.y = 0;
    }
    return currentVelocity;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/drag/VisualElementDragControls.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/drag/VisualElementDragControls.js ***!
  \***************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "VisualElementDragControls": () => (/* binding */ VisualElementDragControls),
/* harmony export */   "elementDragControls": () => (/* binding */ elementDragControls)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_is_ref_object_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../utils/is-ref-object.js */ "./node_modules/framer-motion/dist/es/utils/is-ref-object.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/progress.js");
/* harmony import */ var _events_event_info_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../events/event-info.js */ "./node_modules/framer-motion/dist/es/events/event-info.js");
/* harmony import */ var _events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../../events/use-dom-event.js */ "./node_modules/framer-motion/dist/es/events/use-dom-event.js");
/* harmony import */ var _events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../events/use-pointer-event.js */ "./node_modules/framer-motion/dist/es/events/use-pointer-event.js");
/* harmony import */ var _PanSession_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../PanSession.js */ "./node_modules/framer-motion/dist/es/gestures/PanSession.js");
/* harmony import */ var _utils_lock_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/lock.js */ "./node_modules/framer-motion/dist/es/gestures/drag/utils/lock.js");
/* harmony import */ var _utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/geometry/index.js */ "./node_modules/framer-motion/dist/es/utils/geometry/index.js");
/* harmony import */ var _utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/each-axis.js */ "./node_modules/framer-motion/dist/es/utils/each-axis.js");
/* harmony import */ var _utils_geometry_delta_calc_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../utils/geometry/delta-calc.js */ "./node_modules/framer-motion/dist/es/utils/geometry/delta-calc.js");
/* harmony import */ var _utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./utils/constraints.js */ "./node_modules/framer-motion/dist/es/gestures/drag/utils/constraints.js");
/* harmony import */ var _render_dom_projection_measure_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../render/dom/projection/measure.js */ "./node_modules/framer-motion/dist/es/render/dom/projection/measure.js");
/* harmony import */ var _animation_utils_transitions_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../animation/utils/transitions.js */ "./node_modules/framer-motion/dist/es/animation/utils/transitions.js");
/* harmony import */ var _render_utils_types_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../render/utils/types.js */ "./node_modules/framer-motion/dist/es/render/utils/types.js");

















var elementDragControls = new WeakMap();
/**
 *
 */
var lastPointerEvent;
var VisualElementDragControls = /** @class */ (function () {
    function VisualElementDragControls(_a) {
        var visualElement = _a.visualElement;
        /**
         * Track whether we're currently dragging.
         *
         * @internal
         */
        this.isDragging = false;
        /**
         * The current direction of drag, or `null` if both.
         *
         * @internal
         */
        this.currentDirection = null;
        /**
         * The permitted boundaries of travel, in pixels.
         *
         * @internal
         */
        this.constraints = false;
        /**
         * The per-axis resolved elastic values.
         *
         * @internal
         */
        this.elastic = (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_1__.axisBox)();
        /**
         * A reference to the host component's latest props.
         *
         * @internal
         */
        this.props = {};
        /**
         * @internal
         */
        this.hasMutatedConstraints = false;
        /**
         * Track the initial position of the cursor relative to the dragging element
         * when dragging starts as a value of 0-1 on each axis. We then use this to calculate
         * an ideal bounding box for the VisualElement renderer to project into every frame.
         *
         * @internal
         */
        this.cursorProgress = {
            x: 0.5,
            y: 0.5,
        };
        // When updating _dragX, or _dragY instead of the VisualElement,
        // persist their values between drag gestures.
        this.originPoint = {};
        // This is a reference to the global drag gesture lock, ensuring only one component
        // can "capture" the drag of one or both axes.
        // TODO: Look into moving this into pansession?
        this.openGlobalLock = null;
        /**
         * @internal
         */
        this.panSession = null;
        this.visualElement = visualElement;
        this.visualElement.enableLayoutProjection();
        elementDragControls.set(visualElement, this);
    }
    /**
     * Instantiate a PanSession for the drag gesture
     *
     * @public
     */
    VisualElementDragControls.prototype.start = function (originEvent, _a) {
        var _this = this;
        var _b = _a === void 0 ? {} : _a, _c = _b.snapToCursor, snapToCursor = _c === void 0 ? false : _c, cursorProgress = _b.cursorProgress;
        /**
         * If this drag session has been manually triggered by the user, it might be from an event
         * outside the draggable element. If snapToCursor is set to true, we need to measure the position
         * of the element and snap it to the cursor.
         */
        snapToCursor && this.snapToCursor(originEvent);
        var onSessionStart = function () {
            // Stop any animations on both axis values immediately. This allows the user to throw and catch
            // the component.
            _this.stopMotion();
        };
        var onStart = function (event, info) {
            var _a, _b, _c;
            // Attempt to grab the global drag gesture lock - maybe make this part of PanSession
            var _d = _this.props, drag = _d.drag, dragPropagation = _d.dragPropagation;
            if (drag && !dragPropagation) {
                if (_this.openGlobalLock)
                    _this.openGlobalLock();
                _this.openGlobalLock = (0,_utils_lock_js__WEBPACK_IMPORTED_MODULE_2__.getGlobalLock)(drag);
                // If we don 't have the lock, don't start dragging
                if (!_this.openGlobalLock)
                    return;
            }
            /**
             * Record the progress of the mouse within the draggable element on each axis.
             * onPan, we're going to use this to calculate a new bounding box for the element to
             * project into. This will ensure that even if the DOM element moves via a relayout, it'll
             * stick to the correct place under the pointer.
             */
            _this.prepareBoundingBox();
            _this.visualElement.lockProjectionTarget();
            /**
             * Resolve the drag constraints. These are either set as top/right/bottom/left constraints
             * relative to the element's layout, or a ref to another element. Both need converting to
             * viewport coordinates.
             */
            _this.resolveDragConstraints();
            /**
             * When dragging starts, we want to find where the cursor is relative to the bounding box
             * of the element. Every frame, we calculate a new bounding box using this relative position
             * and let the visualElement renderer figure out how to reproject the element into this bounding
             * box.
             *
             * By doing it this way, rather than applying an x/y transform directly to the element,
             * we can ensure the component always visually sticks to the cursor as we'd expect, even
             * if the DOM element itself changes layout as a result of React updates the user might
             * make based on the drag position.
             */
            var point = (0,_events_event_info_js__WEBPACK_IMPORTED_MODULE_3__.getViewportPointFromEvent)(event).point;
            (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__.eachAxis)(function (axis) {
                var _a = _this.visualElement.projection.target[axis], min = _a.min, max = _a.max;
                _this.cursorProgress[axis] = cursorProgress
                    ? cursorProgress[axis]
                    : (0,popmotion__WEBPACK_IMPORTED_MODULE_5__.progress)(min, max, point[axis]);
                /**
                 * If we have external drag MotionValues, record their origin point. On pointermove
                 * we'll apply the pan gesture offset directly to this value.
                 */
                var axisValue = _this.getAxisMotionValue(axis);
                if (axisValue) {
                    _this.originPoint[axis] = axisValue.get();
                }
            });
            // Set current drag status
            _this.isDragging = true;
            _this.currentDirection = null;
            // Fire onDragStart event
            (_b = (_a = _this.props).onDragStart) === null || _b === void 0 ? void 0 : _b.call(_a, event, info);
            (_c = _this.visualElement.animationState) === null || _c === void 0 ? void 0 : _c.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_6__.AnimationType.Drag, true);
        };
        var onMove = function (event, info) {
            var _a, _b, _c, _d;
            var _e = _this.props, dragPropagation = _e.dragPropagation, dragDirectionLock = _e.dragDirectionLock;
            // If we didn't successfully receive the gesture lock, early return.
            if (!dragPropagation && !_this.openGlobalLock)
                return;
            var offset = info.offset;
            // Attempt to detect drag direction if directionLock is true
            if (dragDirectionLock && _this.currentDirection === null) {
                _this.currentDirection = getCurrentDirection(offset);
                // If we've successfully set a direction, notify listener
                if (_this.currentDirection !== null) {
                    (_b = (_a = _this.props).onDirectionLock) === null || _b === void 0 ? void 0 : _b.call(_a, _this.currentDirection);
                }
                return;
            }
            // Update each point with the latest position
            _this.updateAxis("x", event, offset);
            _this.updateAxis("y", event, offset);
            // Fire onDrag event
            (_d = (_c = _this.props).onDrag) === null || _d === void 0 ? void 0 : _d.call(_c, event, info);
            // Update the last pointer event
            lastPointerEvent = event;
        };
        var onEnd = function (event, info) { return _this.stop(event, info); };
        var transformPagePoint = this.props.transformPagePoint;
        this.panSession = new _PanSession_js__WEBPACK_IMPORTED_MODULE_7__.PanSession(originEvent, {
            onSessionStart: onSessionStart,
            onStart: onStart,
            onMove: onMove,
            onEnd: onEnd,
        }, { transformPagePoint: transformPagePoint });
    };
    /**
     * Ensure the component's layout and target bounding boxes are up-to-date.
     */
    VisualElementDragControls.prototype.prepareBoundingBox = function () {
        var visualElement = this.visualElement;
        visualElement.withoutTransform(function () {
            visualElement.updateLayoutMeasurement();
        });
        visualElement.rebaseProjectionTarget(true, visualElement.measureViewportBox(false));
    };
    VisualElementDragControls.prototype.resolveDragConstraints = function () {
        var _this = this;
        var _a = this.props, dragConstraints = _a.dragConstraints, dragElastic = _a.dragElastic;
        if (dragConstraints) {
            this.constraints = (0,_utils_is_ref_object_js__WEBPACK_IMPORTED_MODULE_8__.isRefObject)(dragConstraints)
                ? this.resolveRefConstraints(this.visualElement.getLayoutState().layout, dragConstraints)
                : (0,_utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.calcRelativeConstraints)(this.visualElement.getLayoutState().layout, dragConstraints);
        }
        else {
            this.constraints = false;
        }
        this.elastic = (0,_utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.resolveDragElastic)(dragElastic);
        /**
         * If we're outputting to external MotionValues, we want to rebase the measured constraints
         * from viewport-relative to component-relative.
         */
        if (this.constraints && !this.hasMutatedConstraints) {
            (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__.eachAxis)(function (axis) {
                if (_this.getAxisMotionValue(axis)) {
                    _this.constraints[axis] = (0,_utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.rebaseAxisConstraints)(_this.visualElement.getLayoutState().layout[axis], _this.constraints[axis]);
                }
            });
        }
    };
    VisualElementDragControls.prototype.resolveRefConstraints = function (layoutBox, constraints) {
        var _a = this.props, onMeasureDragConstraints = _a.onMeasureDragConstraints, transformPagePoint = _a.transformPagePoint;
        var constraintsElement = constraints.current;
        (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(constraintsElement !== null, "If `dragConstraints` is set as a React ref, that ref must be passed to another component's `ref` prop.");
        this.constraintsBox = (0,_render_dom_projection_measure_js__WEBPACK_IMPORTED_MODULE_10__.getBoundingBox)(constraintsElement, transformPagePoint);
        var measuredConstraints = (0,_utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.calcViewportConstraints)(layoutBox, this.constraintsBox);
        /**
         * If there's an onMeasureDragConstraints listener we call it and
         * if different constraints are returned, set constraints to that
         */
        if (onMeasureDragConstraints) {
            var userConstraints = onMeasureDragConstraints((0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_1__.convertAxisBoxToBoundingBox)(measuredConstraints));
            this.hasMutatedConstraints = !!userConstraints;
            if (userConstraints) {
                measuredConstraints = (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_1__.convertBoundingBoxToAxisBox)(userConstraints);
            }
        }
        return measuredConstraints;
    };
    VisualElementDragControls.prototype.cancelDrag = function () {
        var _a;
        this.isDragging = false;
        this.panSession && this.panSession.end();
        this.panSession = null;
        if (!this.props.dragPropagation && this.openGlobalLock) {
            this.openGlobalLock();
            this.openGlobalLock = null;
        }
        (_a = this.visualElement.animationState) === null || _a === void 0 ? void 0 : _a.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_6__.AnimationType.Drag, false);
    };
    VisualElementDragControls.prototype.stop = function (event, info) {
        var _a;
        this.visualElement.unlockProjectionTarget();
        (_a = this.panSession) === null || _a === void 0 ? void 0 : _a.end();
        this.panSession = null;
        var isDragging = this.isDragging;
        this.cancelDrag();
        if (!isDragging)
            return;
        var _b = this.props, dragMomentum = _b.dragMomentum, onDragEnd = _b.onDragEnd;
        if (dragMomentum || this.elastic) {
            var velocity = info.velocity;
            this.animateDragEnd(velocity);
        }
        onDragEnd === null || onDragEnd === void 0 ? void 0 : onDragEnd(event, info);
    };
    VisualElementDragControls.prototype.snapToCursor = function (event) {
        var _this = this;
        this.prepareBoundingBox();
        (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__.eachAxis)(function (axis) {
            var drag = _this.props.drag;
            // If we're not dragging this axis, do an early return.
            if (!shouldDrag(axis, drag, _this.currentDirection))
                return;
            var axisValue = _this.getAxisMotionValue(axis);
            if (axisValue) {
                var point = (0,_events_event_info_js__WEBPACK_IMPORTED_MODULE_3__.getViewportPointFromEvent)(event).point;
                var box = _this.visualElement.getLayoutState().layout;
                var length_1 = box[axis].max - box[axis].min;
                var center = box[axis].min + length_1 / 2;
                var offset = point[axis] - center;
                _this.originPoint[axis] = point[axis];
                axisValue.set(offset);
            }
            else {
                _this.cursorProgress[axis] = 0.5;
                _this.updateVisualElementAxis(axis, event);
            }
        });
    };
    /**
     * Update the specified axis with the latest pointer information.
     */
    VisualElementDragControls.prototype.updateAxis = function (axis, event, offset) {
        var drag = this.props.drag;
        // If we're not dragging this axis, do an early return.
        if (!shouldDrag(axis, drag, this.currentDirection))
            return;
        return this.getAxisMotionValue(axis)
            ? this.updateAxisMotionValue(axis, offset)
            : this.updateVisualElementAxis(axis, event);
    };
    VisualElementDragControls.prototype.updateAxisMotionValue = function (axis, offset) {
        var axisValue = this.getAxisMotionValue(axis);
        if (!offset || !axisValue)
            return;
        var nextValue = this.originPoint[axis] + offset[axis];
        var update = this.constraints
            ? (0,_utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.applyConstraints)(nextValue, this.constraints[axis], this.elastic[axis])
            : nextValue;
        axisValue.set(update);
    };
    VisualElementDragControls.prototype.updateVisualElementAxis = function (axis, event) {
        var _a;
        // Get the actual layout bounding box of the element
        var axisLayout = this.visualElement.getLayoutState().layout[axis];
        // Calculate its current length. In the future we might want to lerp this to animate
        // between lengths if the layout changes as we change the DOM
        var axisLength = axisLayout.max - axisLayout.min;
        // Get the initial progress that the pointer sat on this axis on gesture start.
        var axisProgress = this.cursorProgress[axis];
        var point = (0,_events_event_info_js__WEBPACK_IMPORTED_MODULE_3__.getViewportPointFromEvent)(event).point;
        // Calculate a new min point based on the latest pointer position, constraints and elastic
        var min = (0,_utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.calcConstrainedMinPoint)(point[axis], axisLength, axisProgress, (_a = this.constraints) === null || _a === void 0 ? void 0 : _a[axis], this.elastic[axis]);
        // Update the axis viewport target with this new min and the length
        this.visualElement.setProjectionTargetAxis(axis, min, min + axisLength);
    };
    VisualElementDragControls.prototype.setProps = function (_a) {
        var _b = _a.drag, drag = _b === void 0 ? false : _b, _c = _a.dragDirectionLock, dragDirectionLock = _c === void 0 ? false : _c, _d = _a.dragPropagation, dragPropagation = _d === void 0 ? false : _d, _e = _a.dragConstraints, dragConstraints = _e === void 0 ? false : _e, _f = _a.dragElastic, dragElastic = _f === void 0 ? _utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.defaultElastic : _f, _g = _a.dragMomentum, dragMomentum = _g === void 0 ? true : _g, remainingProps = (0,tslib__WEBPACK_IMPORTED_MODULE_11__.__rest)(_a, ["drag", "dragDirectionLock", "dragPropagation", "dragConstraints", "dragElastic", "dragMomentum"]);
        this.props = (0,tslib__WEBPACK_IMPORTED_MODULE_11__.__assign)({ drag: drag,
            dragDirectionLock: dragDirectionLock,
            dragPropagation: dragPropagation,
            dragConstraints: dragConstraints,
            dragElastic: dragElastic,
            dragMomentum: dragMomentum }, remainingProps);
    };
    /**
     * Drag works differently depending on which props are provided.
     *
     * - If _dragX and _dragY are provided, we output the gesture delta directly to those motion values.
     * - If the component will perform layout animations, we output the gesture to the component's
     *      visual bounding box
     * - Otherwise, we apply the delta to the x/y motion values.
     */
    VisualElementDragControls.prototype.getAxisMotionValue = function (axis) {
        var _a = this.props, layout = _a.layout, layoutId = _a.layoutId;
        var dragKey = "_drag" + axis.toUpperCase();
        if (this.props[dragKey]) {
            return this.props[dragKey];
        }
        else if (!layout && layoutId === undefined) {
            return this.visualElement.getValue(axis, 0);
        }
    };
    VisualElementDragControls.prototype.animateDragEnd = function (velocity) {
        var _this = this;
        var _a = this.props, drag = _a.drag, dragMomentum = _a.dragMomentum, dragElastic = _a.dragElastic, dragTransition = _a.dragTransition;
        var momentumAnimations = (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__.eachAxis)(function (axis) {
            if (!shouldDrag(axis, drag, _this.currentDirection)) {
                return;
            }
            var transition = _this.constraints ? _this.constraints[axis] : {};
            /**
             * Overdamp the boundary spring if `dragElastic` is disabled. There's still a frame
             * of spring animations so we should look into adding a disable spring option to `inertia`.
             * We could do something here where we affect the `bounceStiffness` and `bounceDamping`
             * using the value of `dragElastic`.
             */
            var bounceStiffness = dragElastic ? 200 : 1000000;
            var bounceDamping = dragElastic ? 40 : 10000000;
            var inertia = (0,tslib__WEBPACK_IMPORTED_MODULE_11__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_11__.__assign)({ type: "inertia", velocity: dragMomentum ? velocity[axis] : 0, bounceStiffness: bounceStiffness,
                bounceDamping: bounceDamping, timeConstant: 750, restDelta: 1, restSpeed: 10 }, dragTransition), transition);
            // If we're not animating on an externally-provided `MotionValue` we can use the
            // component's animation controls which will handle interactions with whileHover (etc),
            // otherwise we just have to animate the `MotionValue` itself.
            return _this.getAxisMotionValue(axis)
                ? _this.startAxisValueAnimation(axis, inertia)
                : _this.visualElement.startLayoutAnimation(axis, inertia);
        });
        // Run all animations and then resolve the new drag constraints.
        return Promise.all(momentumAnimations).then(function () {
            var _a, _b;
            (_b = (_a = _this.props).onDragTransitionEnd) === null || _b === void 0 ? void 0 : _b.call(_a);
        });
    };
    VisualElementDragControls.prototype.stopMotion = function () {
        var _this = this;
        (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__.eachAxis)(function (axis) {
            var axisValue = _this.getAxisMotionValue(axis);
            axisValue
                ? axisValue.stop()
                : _this.visualElement.stopLayoutAnimation();
        });
    };
    VisualElementDragControls.prototype.startAxisValueAnimation = function (axis, transition) {
        var axisValue = this.getAxisMotionValue(axis);
        if (!axisValue)
            return;
        var currentValue = axisValue.get();
        axisValue.set(currentValue);
        axisValue.set(currentValue); // Set twice to hard-reset velocity
        return (0,_animation_utils_transitions_js__WEBPACK_IMPORTED_MODULE_12__.startAnimation)(axis, axisValue, 0, transition);
    };
    VisualElementDragControls.prototype.scalePoint = function () {
        var _this = this;
        var _a = this.props, drag = _a.drag, dragConstraints = _a.dragConstraints;
        if (!(0,_utils_is_ref_object_js__WEBPACK_IMPORTED_MODULE_8__.isRefObject)(dragConstraints) || !this.constraintsBox)
            return;
        // Stop any current animations as there can be some visual glitching if we resize mid animation
        this.stopMotion();
        // Record the relative progress of the targetBox relative to the constraintsBox
        var boxProgress = { x: 0, y: 0 };
        (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__.eachAxis)(function (axis) {
            boxProgress[axis] = (0,_utils_geometry_delta_calc_js__WEBPACK_IMPORTED_MODULE_13__.calcOrigin)(_this.visualElement.projection.target[axis], _this.constraintsBox[axis]);
        });
        /**
         * For each axis, calculate the current progress of the layout axis within the constraints.
         * Then, using the latest layout and constraints measurements, reposition the new layout axis
         * proportionally within the constraints.
         */
        this.prepareBoundingBox();
        this.resolveDragConstraints();
        (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_4__.eachAxis)(function (axis) {
            if (!shouldDrag(axis, drag, null))
                return;
            // Calculate the position of the targetBox relative to the constraintsBox using the
            // previously calculated progress
            var _a = (0,_utils_constraints_js__WEBPACK_IMPORTED_MODULE_9__.calcPositionFromProgress)(_this.visualElement.projection.target[axis], _this.constraintsBox[axis], boxProgress[axis]), min = _a.min, max = _a.max;
            _this.visualElement.setProjectionTargetAxis(axis, min, max);
        });
    };
    VisualElementDragControls.prototype.mount = function (visualElement) {
        var _this = this;
        var element = visualElement.getInstance();
        /**
         * Attach a pointerdown event listener on this DOM element to initiate drag tracking.
         */
        var stopPointerListener = (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_14__.addPointerEvent)(element, "pointerdown", function (event) {
            var _a = _this.props, drag = _a.drag, _b = _a.dragListener, dragListener = _b === void 0 ? true : _b;
            drag && dragListener && _this.start(event);
        });
        /**
         * Attach a window resize listener to scale the draggable target within its defined
         * constraints as the window resizes.
         */
        var stopResizeListener = (0,_events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_15__.addDomEvent)(window, "resize", function () {
            _this.scalePoint();
        });
        /**
         * Ensure drag constraints are resolved correctly relative to the dragging element
         * whenever its layout changes.
         */
        var stopLayoutUpdateListener = visualElement.onLayoutUpdate(function () {
            if (_this.isDragging)
                _this.resolveDragConstraints();
        });
        /**
         * If the previous component with this same layoutId was dragging at the time
         * it was unmounted, we want to continue the same gesture on this component.
         */
        var prevDragCursor = visualElement.prevDragCursor;
        if (prevDragCursor) {
            this.start(lastPointerEvent, { cursorProgress: prevDragCursor });
        }
        /**
         * Return a function that will teardown the drag gesture
         */
        return function () {
            stopPointerListener === null || stopPointerListener === void 0 ? void 0 : stopPointerListener();
            stopResizeListener === null || stopResizeListener === void 0 ? void 0 : stopResizeListener();
            stopLayoutUpdateListener === null || stopLayoutUpdateListener === void 0 ? void 0 : stopLayoutUpdateListener();
            _this.cancelDrag();
        };
    };
    return VisualElementDragControls;
}());
function shouldDrag(direction, drag, currentDirection) {
    return ((drag === true || drag === direction) &&
        (currentDirection === null || currentDirection === direction));
}
/**
 * Based on an x/y offset determine the current drag direction. If both axis' offsets are lower
 * than the provided threshold, return `null`.
 *
 * @param offset - The x/y offset from origin.
 * @param lockThreshold - (Optional) - the minimum absolute offset before we can determine a drag direction.
 */
function getCurrentDirection(offset, lockThreshold) {
    if (lockThreshold === void 0) { lockThreshold = 10; }
    var direction = null;
    if (Math.abs(offset.y) > lockThreshold) {
        direction = "y";
    }
    else if (Math.abs(offset.x) > lockThreshold) {
        direction = "x";
    }
    return direction;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/drag/use-drag-controls.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/drag/use-drag-controls.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "DragControls": () => (/* binding */ DragControls),
/* harmony export */   "useDragControls": () => (/* binding */ useDragControls)
/* harmony export */ });
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");


/**
 * Can manually trigger a drag gesture on one or more `drag`-enabled `motion` components.
 *
 * @library
 *
 * ```jsx
 * const dragControls = useDragControls()
 *
 * function startDrag(event) {
 *   dragControls.start(event, { snapToCursor: true })
 * }
 *
 * return (
 *   <>
 *     <Frame onTapStart={startDrag} />
 *     <Frame drag="x" dragControls={dragControls} />
 *   </>
 * )
 * ```
 *
 * @motion
 *
 * ```jsx
 * const dragControls = useDragControls()
 *
 * function startDrag(event) {
 *   dragControls.start(event, { snapToCursor: true })
 * }
 *
 * return (
 *   <>
 *     <div onPointerDown={startDrag} />
 *     <motion.div drag="x" dragControls={dragControls} />
 *   </>
 * )
 * ```
 *
 * @public
 */
var DragControls = /** @class */ (function () {
    function DragControls() {
        this.componentControls = new Set();
    }
    /**
     * Subscribe a component's internal `VisualElementDragControls` to the user-facing API.
     *
     * @internal
     */
    DragControls.prototype.subscribe = function (controls) {
        var _this = this;
        this.componentControls.add(controls);
        return function () { return _this.componentControls.delete(controls); };
    };
    /**
     * Start a drag gesture on every `motion` component that has this set of drag controls
     * passed into it via the `dragControls` prop.
     *
     * ```jsx
     * dragControls.start(e, {
     *   snapToCursor: true
     * })
     * ```
     *
     * @param event - PointerEvent
     * @param options - Options
     *
     * @public
     */
    DragControls.prototype.start = function (event, options) {
        this.componentControls.forEach(function (controls) {
            controls.start(event.nativeEvent || event, options);
        });
    };
    DragControls.prototype.updateConstraints = function () {
        this.componentControls.forEach(function (controls) {
            controls.prepareBoundingBox();
            controls.resolveDragConstraints();
        });
    };
    return DragControls;
}());
var createDragControls = function () { return new DragControls(); };
/**
 * Usually, dragging is initiated by pressing down on a `motion` component with a `drag` prop
 * and moving it. For some use-cases, for instance clicking at an arbitrary point on a video scrubber, we
 * might want to initiate that dragging from a different component than the draggable one.
 *
 * By creating a `dragControls` using the `useDragControls` hook, we can pass this into
 * the draggable component's `dragControls` prop. It exposes a `start` method
 * that can start dragging from pointer events on other components.
 *
 * @library
 *
 * ```jsx
 * const dragControls = useDragControls()
 *
 * function startDrag(event) {
 *   dragControls.start(event, { snapToCursor: true })
 * }
 *
 * return (
 *   <>
 *     <Frame onTapStart={startDrag} />
 *     <Frame drag="x" dragControls={dragControls} />
 *   </>
 * )
 * ```
 *
 * @motion
 *
 * ```jsx
 * const dragControls = useDragControls()
 *
 * function startDrag(event) {
 *   dragControls.start(event, { snapToCursor: true })
 * }
 *
 * return (
 *   <>
 *     <div onPointerDown={startDrag} />
 *     <motion.div drag="x" dragControls={dragControls} />
 *   </>
 * )
 * ```
 *
 * @public
 */
function useDragControls() {
    return (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_0__.useConstant)(createDragControls);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/drag/use-drag.js":
/*!**********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/drag/use-drag.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useDrag": () => (/* binding */ useDrag)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _VisualElementDragControls_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./VisualElementDragControls.js */ "./node_modules/framer-motion/dist/es/gestures/drag/VisualElementDragControls.js");






/**
 * A hook that allows an element to be dragged.
 *
 * @internal
 */
function useDrag(props, visualElement) {
    var groupDragControls = props.dragControls;
    var transformPagePoint = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__.MotionConfigContext).transformPagePoint;
    var dragControls = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_2__.useConstant)(function () {
        return new _VisualElementDragControls_js__WEBPACK_IMPORTED_MODULE_3__.VisualElementDragControls({
            visualElement: visualElement,
        });
    });
    dragControls.setProps((0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)({}, props), { transformPagePoint: transformPagePoint }));
    // If we've been provided a DragControls for manual control over the drag gesture,
    // subscribe this component to it on mount.
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () { return groupDragControls && groupDragControls.subscribe(dragControls); }, [dragControls]);
    // Mount the drag controls with the visualElement
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () { return dragControls.mount(visualElement); }, []);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/drag/utils/constraints.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/drag/utils/constraints.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "applyConstraints": () => (/* binding */ applyConstraints),
/* harmony export */   "calcConstrainedMinPoint": () => (/* binding */ calcConstrainedMinPoint),
/* harmony export */   "calcPositionFromProgress": () => (/* binding */ calcPositionFromProgress),
/* harmony export */   "calcRelativeAxisConstraints": () => (/* binding */ calcRelativeAxisConstraints),
/* harmony export */   "calcRelativeConstraints": () => (/* binding */ calcRelativeConstraints),
/* harmony export */   "calcViewportAxisConstraints": () => (/* binding */ calcViewportAxisConstraints),
/* harmony export */   "calcViewportConstraints": () => (/* binding */ calcViewportConstraints),
/* harmony export */   "defaultElastic": () => (/* binding */ defaultElastic),
/* harmony export */   "rebaseAxisConstraints": () => (/* binding */ rebaseAxisConstraints),
/* harmony export */   "resolveAxisElastic": () => (/* binding */ resolveAxisElastic),
/* harmony export */   "resolveDragElastic": () => (/* binding */ resolveDragElastic),
/* harmony export */   "resolvePointElastic": () => (/* binding */ resolvePointElastic)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/mix.js");



/**
 * Apply constraints to a point. These constraints are both physical along an
 * axis, and an elastic factor that determines how much to constrain the point
 * by if it does lie outside the defined parameters.
 */
function applyConstraints(point, _a, elastic) {
    var min = _a.min, max = _a.max;
    if (min !== undefined && point < min) {
        // If we have a min point defined, and this is outside of that, constrain
        point = elastic ? (0,popmotion__WEBPACK_IMPORTED_MODULE_0__.mix)(min, point, elastic.min) : Math.max(point, min);
    }
    else if (max !== undefined && point > max) {
        // If we have a max point defined, and this is outside of that, constrain
        point = elastic ? (0,popmotion__WEBPACK_IMPORTED_MODULE_0__.mix)(max, point, elastic.max) : Math.min(point, max);
    }
    return point;
}
/**
 * Calculates a min projection point based on a pointer, pointer progress
 * within the drag target, and constraints.
 *
 * For instance if an element was 100px width, we were dragging from 0.25
 * along this axis, the pointer is at 200px, and there were no constraints,
 * we would calculate a min projection point of 175px.
 */
function calcConstrainedMinPoint(point, length, progress, constraints, elastic) {
    // Calculate a min point for this axis and apply it to the current pointer
    var min = point - length * progress;
    return constraints ? applyConstraints(min, constraints, elastic) : min;
}
/**
 * Calculate constraints in terms of the viewport when defined relatively to the
 * measured axis. This is measured from the nearest edge, so a max constraint of 200
 * on an axis with a max value of 300 would return a constraint of 500 - axis length
 */
function calcRelativeAxisConstraints(axis, min, max) {
    return {
        min: min !== undefined ? axis.min + min : undefined,
        max: max !== undefined
            ? axis.max + max - (axis.max - axis.min)
            : undefined,
    };
}
/**
 * Calculate constraints in terms of the viewport when
 * defined relatively to the measured bounding box.
 */
function calcRelativeConstraints(layoutBox, _a) {
    var top = _a.top, left = _a.left, bottom = _a.bottom, right = _a.right;
    return {
        x: calcRelativeAxisConstraints(layoutBox.x, left, right),
        y: calcRelativeAxisConstraints(layoutBox.y, top, bottom),
    };
}
/**
 * Calculate viewport constraints when defined as another viewport-relative axis
 */
function calcViewportAxisConstraints(layoutAxis, constraintsAxis) {
    var _a;
    var min = constraintsAxis.min - layoutAxis.min;
    var max = constraintsAxis.max - layoutAxis.max;
    // If the constraints axis is actually smaller than the layout axis then we can
    // flip the constraints
    if (constraintsAxis.max - constraintsAxis.min <
        layoutAxis.max - layoutAxis.min) {
        _a = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)([max, min], 2), min = _a[0], max = _a[1];
    }
    return {
        min: layoutAxis.min + min,
        max: layoutAxis.min + max,
    };
}
/**
 * Calculate viewport constraints when defined as another viewport-relative box
 */
function calcViewportConstraints(layoutBox, constraintsBox) {
    return {
        x: calcViewportAxisConstraints(layoutBox.x, constraintsBox.x),
        y: calcViewportAxisConstraints(layoutBox.y, constraintsBox.y),
    };
}
/**
 * Calculate the an axis position based on two axes and a progress value.
 */
function calcPositionFromProgress(axis, constraints, progress) {
    var axisLength = axis.max - axis.min;
    var min = (0,popmotion__WEBPACK_IMPORTED_MODULE_0__.mix)(constraints.min, constraints.max - axisLength, progress);
    return { min: min, max: min + axisLength };
}
/**
 * Rebase the calculated viewport constraints relative to the layout.min point.
 */
function rebaseAxisConstraints(layout, constraints) {
    var relativeConstraints = {};
    if (constraints.min !== undefined) {
        relativeConstraints.min = constraints.min - layout.min;
    }
    if (constraints.max !== undefined) {
        relativeConstraints.max = constraints.max - layout.min;
    }
    return relativeConstraints;
}
var defaultElastic = 0.35;
/**
 * Accepts a dragElastic prop and returns resolved elastic values for each axis.
 */
function resolveDragElastic(dragElastic) {
    if (dragElastic === false) {
        dragElastic = 0;
    }
    else if (dragElastic === true) {
        dragElastic = defaultElastic;
    }
    return {
        x: resolveAxisElastic(dragElastic, "left", "right"),
        y: resolveAxisElastic(dragElastic, "top", "bottom"),
    };
}
function resolveAxisElastic(dragElastic, minLabel, maxLabel) {
    return {
        min: resolvePointElastic(dragElastic, minLabel),
        max: resolvePointElastic(dragElastic, maxLabel),
    };
}
function resolvePointElastic(dragElastic, label) {
    var _a;
    return typeof dragElastic === "number"
        ? dragElastic
        : (_a = dragElastic[label]) !== null && _a !== void 0 ? _a : 0;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/drag/utils/lock.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/drag/utils/lock.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createLock": () => (/* binding */ createLock),
/* harmony export */   "getGlobalLock": () => (/* binding */ getGlobalLock),
/* harmony export */   "isDragActive": () => (/* binding */ isDragActive)
/* harmony export */ });
function createLock(name) {
    var lock = null;
    return function () {
        var openLock = function () {
            lock = null;
        };
        if (lock === null) {
            lock = name;
            return openLock;
        }
        return false;
    };
}
var globalHorizontalLock = createLock("dragHorizontal");
var globalVerticalLock = createLock("dragVertical");
function getGlobalLock(drag) {
    var lock = false;
    if (drag === "y") {
        lock = globalVerticalLock();
    }
    else if (drag === "x") {
        lock = globalHorizontalLock();
    }
    else {
        var openHorizontal_1 = globalHorizontalLock();
        var openVertical_1 = globalVerticalLock();
        if (openHorizontal_1 && openVertical_1) {
            lock = function () {
                openHorizontal_1();
                openVertical_1();
            };
        }
        else {
            // Release the locks because we don't use them
            if (openHorizontal_1)
                openHorizontal_1();
            if (openVertical_1)
                openVertical_1();
        }
    }
    return lock;
}
function isDragActive() {
    // Check the gesture lock - if we get it, it means no drag gesture is active
    // and we can safely fire the tap gesture.
    var openGestureLock = getGlobalLock(true);
    if (!openGestureLock)
        return true;
    openGestureLock();
    return false;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/types.js":
/*!**************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/types.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "gestureProps": () => (/* binding */ gestureProps)
/* harmony export */ });
/**
 * @internal
 */
var gestureProps = [
    "onPan",
    "onPanStart",
    "onPanEnd",
    "onPanSessionStart",
    "onTap",
    "onTapStart",
    "onTapCancel",
    "onHoverStart",
    "onHoverEnd",
    "whileFocus",
    "whileTap",
    "whileHover",
];




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/use-focus-gesture.js":
/*!**************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/use-focus-gesture.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useFocusGesture": () => (/* binding */ useFocusGesture)
/* harmony export */ });
/* harmony import */ var _events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../events/use-dom-event.js */ "./node_modules/framer-motion/dist/es/events/use-dom-event.js");
/* harmony import */ var _render_utils_types_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../render/utils/types.js */ "./node_modules/framer-motion/dist/es/render/utils/types.js");



/**
 *
 * @param props
 * @param ref
 * @internal
 */
function useFocusGesture(_a, visualElement) {
    var whileFocus = _a.whileFocus;
    var onFocus = function () {
        var _a;
        (_a = visualElement.animationState) === null || _a === void 0 ? void 0 : _a.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Focus, true);
    };
    var onBlur = function () {
        var _a;
        (_a = visualElement.animationState) === null || _a === void 0 ? void 0 : _a.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Focus, false);
    };
    (0,_events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__.useDomEvent)(visualElement, "focus", whileFocus ? onFocus : undefined);
    (0,_events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__.useDomEvent)(visualElement, "blur", whileFocus ? onBlur : undefined);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/use-gestures.js":
/*!*********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/use-gestures.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useGestures": () => (/* binding */ useGestures)
/* harmony export */ });
/* harmony import */ var _use_pan_gesture_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-pan-gesture.js */ "./node_modules/framer-motion/dist/es/gestures/use-pan-gesture.js");
/* harmony import */ var _use_tap_gesture_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-tap-gesture.js */ "./node_modules/framer-motion/dist/es/gestures/use-tap-gesture.js");
/* harmony import */ var _use_hover_gesture_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-hover-gesture.js */ "./node_modules/framer-motion/dist/es/gestures/use-hover-gesture.js");
/* harmony import */ var _use_focus_gesture_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-focus-gesture.js */ "./node_modules/framer-motion/dist/es/gestures/use-focus-gesture.js");





/**
 * Add pan and tap gesture recognition to an element.
 *
 * @param props - Gesture event handlers
 * @param ref - React `ref` containing a DOM `Element`
 * @public
 */
function useGestures(props, visualElement) {
    (0,_use_pan_gesture_js__WEBPACK_IMPORTED_MODULE_0__.usePanGesture)(props, visualElement);
    (0,_use_tap_gesture_js__WEBPACK_IMPORTED_MODULE_1__.useTapGesture)(props, visualElement);
    (0,_use_hover_gesture_js__WEBPACK_IMPORTED_MODULE_2__.useHoverGesture)(props, visualElement);
    (0,_use_focus_gesture_js__WEBPACK_IMPORTED_MODULE_3__.useFocusGesture)(props, visualElement);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/use-hover-gesture.js":
/*!**************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/use-hover-gesture.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useHoverGesture": () => (/* binding */ useHoverGesture)
/* harmony export */ });
/* harmony import */ var _utils_event_type_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/event-type.js */ "./node_modules/framer-motion/dist/es/gestures/utils/event-type.js");
/* harmony import */ var _events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../events/use-pointer-event.js */ "./node_modules/framer-motion/dist/es/events/use-pointer-event.js");
/* harmony import */ var _render_utils_types_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../render/utils/types.js */ "./node_modules/framer-motion/dist/es/render/utils/types.js");




function createHoverEvent(visualElement, isActive, callback) {
    return function (event, info) {
        var _a;
        if (!(0,_utils_event_type_js__WEBPACK_IMPORTED_MODULE_0__.isMouseEvent)(event) || !visualElement.isHoverEventsEnabled)
            return;
        callback === null || callback === void 0 ? void 0 : callback(event, info);
        (_a = visualElement.animationState) === null || _a === void 0 ? void 0 : _a.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_1__.AnimationType.Hover, isActive);
    };
}
function useHoverGesture(_a, visualElement) {
    var onHoverStart = _a.onHoverStart, onHoverEnd = _a.onHoverEnd, whileHover = _a.whileHover;
    (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_2__.usePointerEvent)(visualElement, "pointerenter", onHoverStart || whileHover
        ? createHoverEvent(visualElement, true, onHoverStart)
        : undefined);
    (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_2__.usePointerEvent)(visualElement, "pointerleave", onHoverEnd || whileHover
        ? createHoverEvent(visualElement, false, onHoverEnd)
        : undefined);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/use-pan-gesture.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/use-pan-gesture.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "usePanGesture": () => (/* binding */ usePanGesture)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");
/* harmony import */ var _events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../events/use-pointer-event.js */ "./node_modules/framer-motion/dist/es/events/use-pointer-event.js");
/* harmony import */ var _PanSession_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./PanSession.js */ "./node_modules/framer-motion/dist/es/gestures/PanSession.js");
/* harmony import */ var _utils_use_unmount_effect_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/use-unmount-effect.js */ "./node_modules/framer-motion/dist/es/utils/use-unmount-effect.js");






/**
 *
 * @param handlers -
 * @param ref -
 *
 * @internalremarks
 * Currently this sets new pan gesture functions every render. The memo route has been explored
 * in the past but ultimately we're still creating new functions every render. An optimisation
 * to explore is creating the pan gestures and loading them into a `ref`.
 *
 * @internal
 */
function usePanGesture(_a, ref) {
    var onPan = _a.onPan, onPanStart = _a.onPanStart, onPanEnd = _a.onPanEnd, onPanSessionStart = _a.onPanSessionStart;
    var hasPanEvents = onPan || onPanStart || onPanEnd || onPanSessionStart;
    var panSession = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
    var transformPagePoint = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__.MotionConfigContext).transformPagePoint;
    var handlers = {
        onSessionStart: onPanSessionStart,
        onStart: onPanStart,
        onMove: onPan,
        onEnd: function (event, info) {
            panSession.current = null;
            onPanEnd && onPanEnd(event, info);
        },
    };
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        if (panSession.current !== null) {
            panSession.current.updateHandlers(handlers);
        }
    });
    function onPointerDown(event) {
        panSession.current = new _PanSession_js__WEBPACK_IMPORTED_MODULE_2__.PanSession(event, handlers, {
            transformPagePoint: transformPagePoint,
        });
    }
    (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_3__.usePointerEvent)(ref, "pointerdown", hasPanEvents && onPointerDown);
    (0,_utils_use_unmount_effect_js__WEBPACK_IMPORTED_MODULE_4__.useUnmountEffect)(function () { return panSession.current && panSession.current.end(); });
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/use-tap-gesture.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/use-tap-gesture.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useTapGesture": () => (/* binding */ useTapGesture)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/pipe.js");
/* harmony import */ var _events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../events/use-pointer-event.js */ "./node_modules/framer-motion/dist/es/events/use-pointer-event.js");
/* harmony import */ var _drag_utils_lock_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./drag/utils/lock.js */ "./node_modules/framer-motion/dist/es/gestures/drag/utils/lock.js");
/* harmony import */ var _render_utils_types_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../render/utils/types.js */ "./node_modules/framer-motion/dist/es/render/utils/types.js");
/* harmony import */ var _utils_use_unmount_effect_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/use-unmount-effect.js */ "./node_modules/framer-motion/dist/es/utils/use-unmount-effect.js");
/* harmony import */ var _utils_is_node_or_child_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/is-node-or-child.js */ "./node_modules/framer-motion/dist/es/gestures/utils/is-node-or-child.js");








/**
 * @param handlers -
 * @internal
 */
function useTapGesture(_a, visualElement) {
    var onTap = _a.onTap, onTapStart = _a.onTapStart, onTapCancel = _a.onTapCancel, whileTap = _a.whileTap;
    var hasPressListeners = onTap || onTapStart || onTapCancel || whileTap;
    var isPressing = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
    var cancelPointerEndListeners = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
    function removePointerEndListener() {
        var _a;
        (_a = cancelPointerEndListeners.current) === null || _a === void 0 ? void 0 : _a.call(cancelPointerEndListeners);
        cancelPointerEndListeners.current = null;
    }
    function checkPointerEnd() {
        var _a;
        removePointerEndListener();
        isPressing.current = false;
        (_a = visualElement.animationState) === null || _a === void 0 ? void 0 : _a.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_1__.AnimationType.Tap, false);
        return !(0,_drag_utils_lock_js__WEBPACK_IMPORTED_MODULE_2__.isDragActive)();
    }
    function onPointerUp(event, info) {
        if (!checkPointerEnd())
            return;
        /**
         * We only count this as a tap gesture if the event.target is the same
         * as, or a child of, this component's element
         */
        !(0,_utils_is_node_or_child_js__WEBPACK_IMPORTED_MODULE_3__.isNodeOrChild)(visualElement.getInstance(), event.target)
            ? onTapCancel === null || onTapCancel === void 0 ? void 0 : onTapCancel(event, info) : onTap === null || onTap === void 0 ? void 0 : onTap(event, info);
    }
    function onPointerCancel(event, info) {
        if (!checkPointerEnd())
            return;
        onTapCancel === null || onTapCancel === void 0 ? void 0 : onTapCancel(event, info);
    }
    function onPointerDown(event, info) {
        var _a;
        removePointerEndListener();
        if (isPressing.current)
            return;
        isPressing.current = true;
        cancelPointerEndListeners.current = (0,popmotion__WEBPACK_IMPORTED_MODULE_4__.pipe)((0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_5__.addPointerEvent)(window, "pointerup", onPointerUp), (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_5__.addPointerEvent)(window, "pointercancel", onPointerCancel));
        onTapStart === null || onTapStart === void 0 ? void 0 : onTapStart(event, info);
        (_a = visualElement.animationState) === null || _a === void 0 ? void 0 : _a.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_1__.AnimationType.Tap, true);
    }
    (0,_events_use_pointer_event_js__WEBPACK_IMPORTED_MODULE_5__.usePointerEvent)(visualElement, "pointerdown", hasPressListeners ? onPointerDown : undefined);
    (0,_utils_use_unmount_effect_js__WEBPACK_IMPORTED_MODULE_6__.useUnmountEffect)(removePointerEndListener);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/utils/event-type.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/utils/event-type.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isMouseEvent": () => (/* binding */ isMouseEvent),
/* harmony export */   "isTouchEvent": () => (/* binding */ isTouchEvent)
/* harmony export */ });
function isMouseEvent(event) {
    // PointerEvent inherits from MouseEvent so we can't use a straight instanceof check.
    if (typeof PointerEvent !== "undefined" && event instanceof PointerEvent) {
        return !!(event.pointerType === "mouse");
    }
    return event instanceof MouseEvent;
}
function isTouchEvent(event) {
    var hasTouches = !!event.touches;
    return hasTouches;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/gestures/utils/is-node-or-child.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/gestures/utils/is-node-or-child.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isNodeOrChild": () => (/* binding */ isNodeOrChild)
/* harmony export */ });
/**
 * Recursively traverse up the tree to check whether the provided child node
 * is the parent or a descendant of it.
 *
 * @param parent - Element to find
 * @param child - Element to test against parent
 */
var isNodeOrChild = function (parent, child) {
    if (!child) {
        return false;
    }
    else if (parent === child) {
        return true;
    }
    else {
        return isNodeOrChild(parent, child.parentElement);
    }
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/index.js":
/*!*****************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/index.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "AnimateLayoutFeature": () => (/* reexport safe */ _motion_features_layout_Animate_js__WEBPACK_IMPORTED_MODULE_19__.AnimateLayout),
/* harmony export */   "AnimatePresence": () => (/* reexport safe */ _components_AnimatePresence_index_js__WEBPACK_IMPORTED_MODULE_26__.AnimatePresence),
/* harmony export */   "AnimateSharedLayout": () => (/* reexport safe */ _components_AnimateSharedLayout_index_js__WEBPACK_IMPORTED_MODULE_29__.AnimateSharedLayout),
/* harmony export */   "AnimationFeature": () => (/* reexport safe */ _motion_features_animation_js__WEBPACK_IMPORTED_MODULE_18__.Animation),
/* harmony export */   "DragControls": () => (/* reexport safe */ _gestures_drag_use_drag_controls_js__WEBPACK_IMPORTED_MODULE_41__.DragControls),
/* harmony export */   "DragFeature": () => (/* reexport safe */ _motion_features_drag_js__WEBPACK_IMPORTED_MODULE_9__.Drag),
/* harmony export */   "ExitFeature": () => (/* reexport safe */ _motion_features_exit_js__WEBPACK_IMPORTED_MODULE_14__.Exit),
/* harmony export */   "FramerTreeLayoutContext": () => (/* reexport safe */ _context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_6__.FramerTreeLayoutContext),
/* harmony export */   "GesturesFeature": () => (/* reexport safe */ _motion_features_gestures_js__WEBPACK_IMPORTED_MODULE_13__.Gestures),
/* harmony export */   "LayoutGroupContext": () => (/* reexport safe */ _context_LayoutGroupContext_js__WEBPACK_IMPORTED_MODULE_3__.LayoutGroupContext),
/* harmony export */   "MotionConfig": () => (/* reexport safe */ _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_0__.MotionConfig),
/* harmony export */   "MotionConfigContext": () => (/* reexport safe */ _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_0__.MotionConfigContext),
/* harmony export */   "MotionValue": () => (/* reexport safe */ _value_index_js__WEBPACK_IMPORTED_MODULE_15__.MotionValue),
/* harmony export */   "PresenceContext": () => (/* reexport safe */ _context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_1__.PresenceContext),
/* harmony export */   "SharedLayoutContext": () => (/* reexport safe */ _context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_6__.SharedLayoutContext),
/* harmony export */   "VisibilityAction": () => (/* reexport safe */ _components_AnimateSharedLayout_types_js__WEBPACK_IMPORTED_MODULE_4__.VisibilityAction),
/* harmony export */   "addScaleCorrection": () => (/* reexport safe */ _render_dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_20__.addScaleCorrection),
/* harmony export */   "animate": () => (/* reexport safe */ _animation_animate_js__WEBPACK_IMPORTED_MODULE_27__.animate),
/* harmony export */   "animateVisualElement": () => (/* reexport safe */ _render_utils_animation_js__WEBPACK_IMPORTED_MODULE_16__.animateVisualElement),
/* harmony export */   "animationControls": () => (/* reexport safe */ _animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_17__.animationControls),
/* harmony export */   "createBatcher": () => (/* reexport safe */ _components_AnimateSharedLayout_utils_batcher_js__WEBPACK_IMPORTED_MODULE_5__.createBatcher),
/* harmony export */   "createCrossfader": () => (/* reexport safe */ _components_AnimateSharedLayout_utils_crossfader_js__WEBPACK_IMPORTED_MODULE_28__.createCrossfader),
/* harmony export */   "createDomMotionComponent": () => (/* reexport safe */ _render_dom_motion_js__WEBPACK_IMPORTED_MODULE_24__.createDomMotionComponent),
/* harmony export */   "createMotionComponent": () => (/* reexport safe */ _motion_index_js__WEBPACK_IMPORTED_MODULE_7__.createMotionComponent),
/* harmony export */   "isValidMotionProp": () => (/* reexport safe */ _motion_utils_valid_prop_js__WEBPACK_IMPORTED_MODULE_21__.isValidMotionProp),
/* harmony export */   "m": () => (/* reexport safe */ _render_dom_motion_minimal_js__WEBPACK_IMPORTED_MODULE_25__.m),
/* harmony export */   "motion": () => (/* reexport safe */ _render_dom_motion_js__WEBPACK_IMPORTED_MODULE_24__.motion),
/* harmony export */   "motionValue": () => (/* reexport safe */ _value_index_js__WEBPACK_IMPORTED_MODULE_15__.motionValue),
/* harmony export */   "resolveMotionValue": () => (/* reexport safe */ _value_utils_resolve_motion_value_js__WEBPACK_IMPORTED_MODULE_23__.resolveMotionValue),
/* harmony export */   "transform": () => (/* reexport safe */ _utils_transform_js__WEBPACK_IMPORTED_MODULE_32__.transform),
/* harmony export */   "useAnimation": () => (/* reexport safe */ _animation_use_animation_js__WEBPACK_IMPORTED_MODULE_39__.useAnimation),
/* harmony export */   "useCycle": () => (/* reexport safe */ _utils_use_cycle_js__WEBPACK_IMPORTED_MODULE_40__.useCycle),
/* harmony export */   "useDeprecatedAnimatedState": () => (/* reexport safe */ _animation_use_animated_state_js__WEBPACK_IMPORTED_MODULE_42__.useAnimatedState),
/* harmony export */   "useDeprecatedInvertedScale": () => (/* reexport safe */ _value_use_inverted_scale_js__WEBPACK_IMPORTED_MODULE_43__.useInvertedScale),
/* harmony export */   "useDomEvent": () => (/* reexport safe */ _events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_8__.useDomEvent),
/* harmony export */   "useDragControls": () => (/* reexport safe */ _gestures_drag_use_drag_controls_js__WEBPACK_IMPORTED_MODULE_41__.useDragControls),
/* harmony export */   "useElementScroll": () => (/* reexport safe */ _value_scroll_use_element_scroll_js__WEBPACK_IMPORTED_MODULE_36__.useElementScroll),
/* harmony export */   "useGestures": () => (/* reexport safe */ _gestures_use_gestures_js__WEBPACK_IMPORTED_MODULE_12__.useGestures),
/* harmony export */   "useIsPresent": () => (/* reexport safe */ _components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_2__.useIsPresent),
/* harmony export */   "useMotionTemplate": () => (/* reexport safe */ _value_use_motion_template_js__WEBPACK_IMPORTED_MODULE_31__.useMotionTemplate),
/* harmony export */   "useMotionValue": () => (/* reexport safe */ _value_use_motion_value_js__WEBPACK_IMPORTED_MODULE_30__.useMotionValue),
/* harmony export */   "usePanGesture": () => (/* reexport safe */ _gestures_use_pan_gesture_js__WEBPACK_IMPORTED_MODULE_10__.usePanGesture),
/* harmony export */   "usePresence": () => (/* reexport safe */ _components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_2__.usePresence),
/* harmony export */   "useReducedMotion": () => (/* reexport safe */ _utils_use_reduced_motion_js__WEBPACK_IMPORTED_MODULE_38__.useReducedMotion),
/* harmony export */   "useSpring": () => (/* reexport safe */ _value_use_spring_js__WEBPACK_IMPORTED_MODULE_34__.useSpring),
/* harmony export */   "useTapGesture": () => (/* reexport safe */ _gestures_use_tap_gesture_js__WEBPACK_IMPORTED_MODULE_11__.useTapGesture),
/* harmony export */   "useTransform": () => (/* reexport safe */ _value_use_transform_js__WEBPACK_IMPORTED_MODULE_33__.useTransform),
/* harmony export */   "useVelocity": () => (/* reexport safe */ _value_use_velocity_js__WEBPACK_IMPORTED_MODULE_35__.useVelocity),
/* harmony export */   "useViewportScroll": () => (/* reexport safe */ _value_scroll_use_viewport_scroll_js__WEBPACK_IMPORTED_MODULE_37__.useViewportScroll),
/* harmony export */   "visualElement": () => (/* reexport safe */ _render_index_js__WEBPACK_IMPORTED_MODULE_22__.visualElement)
/* harmony export */ });
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");
/* harmony import */ var _context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./context/PresenceContext.js */ "./node_modules/framer-motion/dist/es/context/PresenceContext.js");
/* harmony import */ var _components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/AnimatePresence/use-presence.js */ "./node_modules/framer-motion/dist/es/components/AnimatePresence/use-presence.js");
/* harmony import */ var _context_LayoutGroupContext_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./context/LayoutGroupContext.js */ "./node_modules/framer-motion/dist/es/context/LayoutGroupContext.js");
/* harmony import */ var _components_AnimateSharedLayout_types_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/AnimateSharedLayout/types.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js");
/* harmony import */ var _components_AnimateSharedLayout_utils_batcher_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/AnimateSharedLayout/utils/batcher.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/batcher.js");
/* harmony import */ var _context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./context/SharedLayoutContext.js */ "./node_modules/framer-motion/dist/es/context/SharedLayoutContext.js");
/* harmony import */ var _motion_index_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./motion/index.js */ "./node_modules/framer-motion/dist/es/motion/index.js");
/* harmony import */ var _events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./events/use-dom-event.js */ "./node_modules/framer-motion/dist/es/events/use-dom-event.js");
/* harmony import */ var _motion_features_drag_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./motion/features/drag.js */ "./node_modules/framer-motion/dist/es/motion/features/drag.js");
/* harmony import */ var _gestures_use_pan_gesture_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./gestures/use-pan-gesture.js */ "./node_modules/framer-motion/dist/es/gestures/use-pan-gesture.js");
/* harmony import */ var _gestures_use_tap_gesture_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./gestures/use-tap-gesture.js */ "./node_modules/framer-motion/dist/es/gestures/use-tap-gesture.js");
/* harmony import */ var _gestures_use_gestures_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./gestures/use-gestures.js */ "./node_modules/framer-motion/dist/es/gestures/use-gestures.js");
/* harmony import */ var _motion_features_gestures_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./motion/features/gestures.js */ "./node_modules/framer-motion/dist/es/motion/features/gestures.js");
/* harmony import */ var _motion_features_exit_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./motion/features/exit.js */ "./node_modules/framer-motion/dist/es/motion/features/exit.js");
/* harmony import */ var _value_index_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./value/index.js */ "./node_modules/framer-motion/dist/es/value/index.js");
/* harmony import */ var _render_utils_animation_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./render/utils/animation.js */ "./node_modules/framer-motion/dist/es/render/utils/animation.js");
/* harmony import */ var _animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./animation/animation-controls.js */ "./node_modules/framer-motion/dist/es/animation/animation-controls.js");
/* harmony import */ var _motion_features_animation_js__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./motion/features/animation.js */ "./node_modules/framer-motion/dist/es/motion/features/animation.js");
/* harmony import */ var _motion_features_layout_Animate_js__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./motion/features/layout/Animate.js */ "./node_modules/framer-motion/dist/es/motion/features/layout/Animate.js");
/* harmony import */ var _render_dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./render/dom/projection/scale-correction.js */ "./node_modules/framer-motion/dist/es/render/dom/projection/scale-correction.js");
/* harmony import */ var _motion_utils_valid_prop_js__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./motion/utils/valid-prop.js */ "./node_modules/framer-motion/dist/es/motion/utils/valid-prop.js");
/* harmony import */ var _render_index_js__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./render/index.js */ "./node_modules/framer-motion/dist/es/render/index.js");
/* harmony import */ var _value_utils_resolve_motion_value_js__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./value/utils/resolve-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/resolve-motion-value.js");
/* harmony import */ var _render_dom_motion_js__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./render/dom/motion.js */ "./node_modules/framer-motion/dist/es/render/dom/motion.js");
/* harmony import */ var _render_dom_motion_minimal_js__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./render/dom/motion-minimal.js */ "./node_modules/framer-motion/dist/es/render/dom/motion-minimal.js");
/* harmony import */ var _components_AnimatePresence_index_js__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./components/AnimatePresence/index.js */ "./node_modules/framer-motion/dist/es/components/AnimatePresence/index.js");
/* harmony import */ var _animation_animate_js__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./animation/animate.js */ "./node_modules/framer-motion/dist/es/animation/animate.js");
/* harmony import */ var _components_AnimateSharedLayout_utils_crossfader_js__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./components/AnimateSharedLayout/utils/crossfader.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/utils/crossfader.js");
/* harmony import */ var _components_AnimateSharedLayout_index_js__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./components/AnimateSharedLayout/index.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/index.js");
/* harmony import */ var _value_use_motion_value_js__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./value/use-motion-value.js */ "./node_modules/framer-motion/dist/es/value/use-motion-value.js");
/* harmony import */ var _value_use_motion_template_js__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./value/use-motion-template.js */ "./node_modules/framer-motion/dist/es/value/use-motion-template.js");
/* harmony import */ var _utils_transform_js__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./utils/transform.js */ "./node_modules/framer-motion/dist/es/utils/transform.js");
/* harmony import */ var _value_use_transform_js__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./value/use-transform.js */ "./node_modules/framer-motion/dist/es/value/use-transform.js");
/* harmony import */ var _value_use_spring_js__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./value/use-spring.js */ "./node_modules/framer-motion/dist/es/value/use-spring.js");
/* harmony import */ var _value_use_velocity_js__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./value/use-velocity.js */ "./node_modules/framer-motion/dist/es/value/use-velocity.js");
/* harmony import */ var _value_scroll_use_element_scroll_js__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./value/scroll/use-element-scroll.js */ "./node_modules/framer-motion/dist/es/value/scroll/use-element-scroll.js");
/* harmony import */ var _value_scroll_use_viewport_scroll_js__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./value/scroll/use-viewport-scroll.js */ "./node_modules/framer-motion/dist/es/value/scroll/use-viewport-scroll.js");
/* harmony import */ var _utils_use_reduced_motion_js__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./utils/use-reduced-motion.js */ "./node_modules/framer-motion/dist/es/utils/use-reduced-motion.js");
/* harmony import */ var _animation_use_animation_js__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./animation/use-animation.js */ "./node_modules/framer-motion/dist/es/animation/use-animation.js");
/* harmony import */ var _utils_use_cycle_js__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! ./utils/use-cycle.js */ "./node_modules/framer-motion/dist/es/utils/use-cycle.js");
/* harmony import */ var _gestures_drag_use_drag_controls_js__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! ./gestures/drag/use-drag-controls.js */ "./node_modules/framer-motion/dist/es/gestures/drag/use-drag-controls.js");
/* harmony import */ var _animation_use_animated_state_js__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! ./animation/use-animated-state.js */ "./node_modules/framer-motion/dist/es/animation/use-animated-state.js");
/* harmony import */ var _value_use_inverted_scale_js__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! ./value/use-inverted-scale.js */ "./node_modules/framer-motion/dist/es/value/use-inverted-scale.js");














































/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/animation.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/animation.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Animation": () => (/* binding */ Animation)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/make-renderless-component.js */ "./node_modules/framer-motion/dist/es/motion/utils/make-renderless-component.js");
/* harmony import */ var _animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../animation/animation-controls.js */ "./node_modules/framer-motion/dist/es/animation/animation-controls.js");
/* harmony import */ var _render_utils_animation_state_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../render/utils/animation-state.js */ "./node_modules/framer-motion/dist/es/render/utils/animation-state.js");





var AnimationState = (0,_utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_1__.makeRenderlessComponent)(function (props) {
    var visualElement = props.visualElement, animate = props.animate;
    /**
     * We dynamically generate the AnimationState manager as it contains a reference
     * to the underlying animation library. We only want to load that if we load this,
     * so people can optionally code split it out using the `m` component.
     */
    visualElement.animationState || (visualElement.animationState = (0,_render_utils_animation_state_js__WEBPACK_IMPORTED_MODULE_2__.createAnimationState)(visualElement));
    /**
     * Subscribe any provided AnimationControls to the component's VisualElement
     */
    if ((0,_animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_3__.isAnimationControls)(animate)) {
        (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () { return animate.subscribe(visualElement); }, [animate]);
    }
});
/**
 * @public
 */
var Animation = {
    key: "animation",
    shouldRender: function () { return true; },
    getComponent: function (_a) {
        var animate = _a.animate, whileHover = _a.whileHover, whileFocus = _a.whileFocus, whileTap = _a.whileTap, whileDrag = _a.whileDrag, exit = _a.exit, variants = _a.variants;
        return animate ||
            whileHover ||
            whileFocus ||
            whileTap ||
            whileDrag ||
            exit ||
            variants
            ? AnimationState
            : undefined;
    },
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/drag.js":
/*!********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/drag.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Drag": () => (/* binding */ Drag)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _gestures_drag_use_drag_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../gestures/drag/use-drag.js */ "./node_modules/framer-motion/dist/es/gestures/drag/use-drag.js");
/* harmony import */ var _utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/make-renderless-component.js */ "./node_modules/framer-motion/dist/es/motion/utils/make-renderless-component.js");




var Component = (0,_utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_0__.makeRenderlessComponent)(function (_a) {
    var visualElement = _a.visualElement, props = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, ["visualElement"]);
    return (0,_gestures_drag_use_drag_js__WEBPACK_IMPORTED_MODULE_2__.useDrag)(props, visualElement);
});
/**
 * @public
 */
var Drag = {
    key: "drag",
    shouldRender: function (props) { return !!props.drag || !!props.dragControls; },
    getComponent: function () { return Component; },
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/exit.js":
/*!********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/exit.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Exit": () => (/* binding */ Exit)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../context/PresenceContext.js */ "./node_modules/framer-motion/dist/es/context/PresenceContext.js");
/* harmony import */ var _components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/AnimatePresence/use-presence.js */ "./node_modules/framer-motion/dist/es/components/AnimatePresence/use-presence.js");
/* harmony import */ var _render_utils_types_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../render/utils/types.js */ "./node_modules/framer-motion/dist/es/render/utils/types.js");
/* harmony import */ var _utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/make-renderless-component.js */ "./node_modules/framer-motion/dist/es/motion/utils/make-renderless-component.js");
/* harmony import */ var _utils_should_inherit_variant_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/should-inherit-variant.js */ "./node_modules/framer-motion/dist/es/motion/utils/should-inherit-variant.js");








/**
 * TODO: This component is quite small and no longer directly imports animation code.
 * It could be a candidate for folding back into the main `motion` component.
 */
var ExitComponent = (0,_utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_1__.makeRenderlessComponent)(function (props) {
    var custom = props.custom, visualElement = props.visualElement;
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__read)((0,_components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_3__.usePresence)(), 2), isPresent = _a[0], onExitComplete = _a[1];
    var presenceContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_4__.PresenceContext);
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        var _a, _b;
        var animation = (_a = visualElement.animationState) === null || _a === void 0 ? void 0 : _a.setActive(_render_utils_types_js__WEBPACK_IMPORTED_MODULE_5__.AnimationType.Exit, !isPresent, { custom: (_b = presenceContext === null || presenceContext === void 0 ? void 0 : presenceContext.custom) !== null && _b !== void 0 ? _b : custom });
        !isPresent && (animation === null || animation === void 0 ? void 0 : animation.then(onExitComplete));
    }, [isPresent]);
});
/**
 * @public
 */
var Exit = {
    key: "exit",
    shouldRender: function (props) { return !!props.exit && !(0,_utils_should_inherit_variant_js__WEBPACK_IMPORTED_MODULE_6__.checkShouldInheritVariant)(props); },
    getComponent: function () { return ExitComponent; },
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/gestures.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/gestures.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Gestures": () => (/* binding */ Gestures)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/make-renderless-component.js */ "./node_modules/framer-motion/dist/es/motion/utils/make-renderless-component.js");
/* harmony import */ var _gestures_use_gestures_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../gestures/use-gestures.js */ "./node_modules/framer-motion/dist/es/gestures/use-gestures.js");
/* harmony import */ var _gestures_types_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../gestures/types.js */ "./node_modules/framer-motion/dist/es/gestures/types.js");





var GestureComponent = (0,_utils_make_renderless_component_js__WEBPACK_IMPORTED_MODULE_0__.makeRenderlessComponent)(function (_a) {
    var visualElement = _a.visualElement, props = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, ["visualElement"]);
    (0,_gestures_use_gestures_js__WEBPACK_IMPORTED_MODULE_2__.useGestures)(props, visualElement);
});
/**
 * @public
 */
var Gestures = {
    key: "gestures",
    shouldRender: function (props) {
        return _gestures_types_js__WEBPACK_IMPORTED_MODULE_3__.gestureProps.some(function (key) { return props.hasOwnProperty(key); });
    },
    getComponent: function () { return GestureComponent; },
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/layout/Animate.js":
/*!******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/layout/Animate.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "AnimateLayout": () => (/* binding */ AnimateLayout)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../components/AnimatePresence/use-presence.js */ "./node_modules/framer-motion/dist/es/components/AnimatePresence/use-presence.js");
/* harmony import */ var _components_AnimateSharedLayout_types_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../components/AnimateSharedLayout/types.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js");
/* harmony import */ var _utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../utils/geometry/index.js */ "./node_modules/framer-motion/dist/es/utils/geometry/index.js");
/* harmony import */ var _utils_each_axis_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../utils/each-axis.js */ "./node_modules/framer-motion/dist/es/utils/each-axis.js");
/* harmony import */ var _animation_utils_transitions_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../animation/utils/transitions.js */ "./node_modules/framer-motion/dist/es/animation/utils/transitions.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils.js */ "./node_modules/framer-motion/dist/es/motion/features/layout/utils.js");









var progressTarget = 1000;
var Animate = /** @class */ (function (_super) {
    (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__extends)(Animate, _super);
    function Animate() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        /**
         * A mutable object that tracks the target viewport box
         * for the current animation frame.
         */
        _this.frameTarget = (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_2__.axisBox)();
        /**
         * The current animation target, we use this to check whether to start
         * a new animation or continue the existing one.
         */
        _this.currentAnimationTarget = (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_2__.axisBox)();
        /**
         * Track whether we're animating this axis.
         */
        _this.isAnimating = {
            x: false,
            y: false,
        };
        _this.stopAxisAnimation = {
            x: undefined,
            y: undefined,
        };
        _this.isAnimatingTree = false;
        _this.animate = function (target, origin, _a) {
            if (_a === void 0) { _a = {}; }
            var originBox = _a.originBox, targetBox = _a.targetBox, visibilityAction = _a.visibilityAction, shouldStackAnimate = _a.shouldStackAnimate, onComplete = _a.onComplete, config = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, ["originBox", "targetBox", "visibilityAction", "shouldStackAnimate", "onComplete"]);
            var _b = _this.props, visualElement = _b.visualElement, layout = _b.layout;
            /**
             * Early return if we've been instructed not to animate this render.
             */
            if (shouldStackAnimate === false) {
                _this.isAnimatingTree = false;
                return _this.safeToRemove();
            }
            /**
             * Prioritise tree animations
             */
            if (_this.isAnimatingTree && shouldStackAnimate !== true) {
                return;
            }
            else if (shouldStackAnimate) {
                _this.isAnimatingTree = true;
            }
            /**
             * Allow the measured origin (prev bounding box) and target (actual layout) to be
             * overridden by the provided config.
             */
            origin = originBox || origin;
            target = targetBox || target;
            var boxHasMoved = hasMoved(origin, target);
            var animations = (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_3__.eachAxis)(function (axis) {
                /**
                 * If layout is set to "position", we can resize the origin box based on the target
                 * box and only animate its position.
                 */
                if (layout === "position") {
                    var targetLength = target[axis].max - target[axis].min;
                    origin[axis].max = origin[axis].min + targetLength;
                }
                if (visualElement.projection.isTargetLocked) {
                    return;
                }
                else if (visibilityAction !== undefined) {
                    visualElement.setVisibility(visibilityAction === _components_AnimateSharedLayout_types_js__WEBPACK_IMPORTED_MODULE_4__.VisibilityAction.Show);
                }
                else if (boxHasMoved) {
                    // If the box has moved, animate between it's current visual state and its
                    // final state
                    return _this.animateAxis(axis, target[axis], origin[axis], config);
                }
                else {
                    // If the box has remained in the same place, immediately set the axis target
                    // to the final desired state
                    return visualElement.setProjectionTargetAxis(axis, target[axis].min, target[axis].max);
                }
            });
            // Force a render to ensure there's no flash of uncorrected bounding box.
            visualElement.syncRender();
            /**
             * If this visualElement isn't present (ie it's been removed from the tree by the user but
             * kept in by the tree by AnimatePresence) then call safeToRemove when all axis animations
             * have successfully finished.
             */
            return Promise.all(animations).then(function () {
                _this.isAnimatingTree = false;
                onComplete && onComplete();
                visualElement.notifyLayoutAnimationComplete();
            });
        };
        return _this;
    }
    Animate.prototype.componentDidMount = function () {
        var _this = this;
        var visualElement = this.props.visualElement;
        visualElement.animateMotionValue = _animation_utils_transitions_js__WEBPACK_IMPORTED_MODULE_5__.startAnimation;
        visualElement.enableLayoutProjection();
        this.unsubLayoutReady = visualElement.onLayoutUpdate(this.animate);
        visualElement.layoutSafeToRemove = function () { return _this.safeToRemove(); };
    };
    Animate.prototype.componentWillUnmount = function () {
        var _this = this;
        this.unsubLayoutReady();
        (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_3__.eachAxis)(function (axis) { var _a, _b; return (_b = (_a = _this.stopAxisAnimation)[axis]) === null || _b === void 0 ? void 0 : _b.call(_a); });
    };
    /**
     * TODO: This manually performs animations on the visualElement's layout progress
     * values. It'd be preferable to amend the startLayoutAxisAnimation
     * API to accept more custom animations like this.
     */
    Animate.prototype.animateAxis = function (axis, target, origin, _a) {
        var _this = this;
        var _b, _c;
        var transition = (_a === void 0 ? {} : _a).transition;
        /**
         * If we're not animating to a new target, don't run this animation
         */
        if (this.isAnimating[axis] &&
            axisIsEqual(target, this.currentAnimationTarget[axis])) {
            return;
        }
        (_c = (_b = this.stopAxisAnimation)[axis]) === null || _c === void 0 ? void 0 : _c.call(_b);
        this.isAnimating[axis] = true;
        var visualElement = this.props.visualElement;
        var frameTarget = this.frameTarget[axis];
        var layoutProgress = visualElement.getProjectionAnimationProgress()[axis];
        /**
         * Set layout progress back to 0. We set it twice to hard-reset any velocity that might
         * be re-incoporated into a subsequent spring animation.
         */
        layoutProgress.clearListeners();
        layoutProgress.set(0);
        layoutProgress.set(0);
        /**
         * Create an animation function to run once per frame. This will tween the visual bounding box from
         * origin to target using the latest progress value.
         */
        var frame = function () {
            // Convert the latest layoutProgress, which is a value from 0-1000, into a 0-1 progress
            var p = layoutProgress.get() / progressTarget;
            // Tween the axis and update the visualElement with the latest values
            (0,_utils_js__WEBPACK_IMPORTED_MODULE_6__.tweenAxis)(frameTarget, origin, target, p);
            visualElement.setProjectionTargetAxis(axis, frameTarget.min, frameTarget.max);
        };
        // Synchronously run a frame to ensure there's no flash of the uncorrected bounding box.
        frame();
        // Ensure that the layout delta is updated for this frame.
        visualElement.updateLayoutProjection();
        // Create a function to stop animation on this specific axis
        var unsubscribeProgress = layoutProgress.onChange(frame);
        this.stopAxisAnimation[axis] = function () {
            _this.isAnimating[axis] = false;
            layoutProgress.stop();
            unsubscribeProgress();
        };
        this.currentAnimationTarget[axis] = target;
        // Start the animation on this axis
        var animation = (0,_animation_utils_transitions_js__WEBPACK_IMPORTED_MODULE_5__.startAnimation)(axis === "x" ? "layoutX" : "layoutY", layoutProgress, progressTarget, transition || this.props.transition || defaultTransition).then(this.stopAxisAnimation[axis]);
        return animation;
    };
    Animate.prototype.safeToRemove = function () {
        var _a, _b;
        (_b = (_a = this.props).safeToRemove) === null || _b === void 0 ? void 0 : _b.call(_a);
    };
    Animate.prototype.render = function () {
        return null;
    };
    return Animate;
}(react__WEBPACK_IMPORTED_MODULE_0__.Component));
function AnimateLayoutContextProvider(props) {
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)((0,_components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_7__.usePresence)(), 2), safeToRemove = _a[1];
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Animate, (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, props, { safeToRemove: safeToRemove }));
}
function hasMoved(a, b) {
    return (!isZeroBox(a) &&
        !isZeroBox(b) &&
        (!axisIsEqual(a.x, b.x) || !axisIsEqual(a.y, b.y)));
}
var zeroAxis = { min: 0, max: 0 };
function isZeroBox(a) {
    return axisIsEqual(a.x, zeroAxis) && axisIsEqual(a.y, zeroAxis);
}
function axisIsEqual(a, b) {
    return a.min === b.min && a.max === b.max;
}
var defaultTransition = {
    duration: 0.45,
    ease: [0.4, 0, 0.1, 1],
};
/**
 * @public
 */
var AnimateLayout = {
    key: "animate-layout",
    shouldRender: function (props) {
        return !!props.layout || props.layoutId !== undefined;
    },
    getComponent: function () { return AnimateLayoutContextProvider; },
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/layout/Measure.js":
/*!******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/layout/Measure.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MeasureLayout": () => (/* binding */ MeasureLayout)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../context/SharedLayoutContext.js */ "./node_modules/framer-motion/dist/es/context/SharedLayoutContext.js");




/**
 * This component is responsible for scheduling the measuring of the motion component
 */
var Measure = /** @class */ (function (_super) {
    (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__extends)(Measure, _super);
    function Measure() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    /**
     * If this is a child of a SyncContext, register the VisualElement with it on mount.
     */
    Measure.prototype.componentDidMount = function () {
        var _a = this.props, syncLayout = _a.syncLayout, framerSyncLayout = _a.framerSyncLayout, visualElement = _a.visualElement;
        (0,_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.isSharedLayout)(syncLayout) && syncLayout.register(visualElement);
        (0,_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.isSharedLayout)(framerSyncLayout) &&
            framerSyncLayout.register(visualElement);
    };
    /**
     * If this is a child of a SyncContext, notify it that it needs to re-render. It will then
     * handle the snapshotting.
     *
     * If it is stand-alone component, add it to the batcher.
     */
    Measure.prototype.getSnapshotBeforeUpdate = function () {
        var _a = this.props, syncLayout = _a.syncLayout, visualElement = _a.visualElement;
        if ((0,_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.isSharedLayout)(syncLayout)) {
            syncLayout.syncUpdate();
        }
        else {
            visualElement.snapshotViewportBox();
            syncLayout.add(visualElement);
        }
        return null;
    };
    Measure.prototype.componentDidUpdate = function () {
        var _a = this.props, syncLayout = _a.syncLayout, visualElement = _a.visualElement;
        if (!(0,_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.isSharedLayout)(syncLayout))
            syncLayout.flush();
        /**
         * If this axis isn't animating as a result of this render we want to reset the targetBox
         * to the measured box
         */
        visualElement.rebaseProjectionTarget();
    };
    Measure.prototype.render = function () {
        return null;
    };
    return Measure;
}((react__WEBPACK_IMPORTED_MODULE_0___default().Component)));
function MeasureContextProvider(props) {
    var syncLayout = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.SharedLayoutContext);
    var framerSyncLayout = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_2__.FramerTreeLayoutContext);
    return (react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Measure, (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, props, { syncLayout: syncLayout, framerSyncLayout: framerSyncLayout })));
}
var MeasureLayout = {
    key: "measure-layout",
    shouldRender: function (props) {
        return !!props.drag || !!props.layout || props.layoutId !== undefined;
    },
    getComponent: function () { return MeasureContextProvider; },
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/layout/use-snapshot-on-unmount.js":
/*!**********************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/layout/use-snapshot-on-unmount.js ***!
  \**********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useSnapshotOnUnmount": () => (/* binding */ useSnapshotOnUnmount)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../context/SharedLayoutContext.js */ "./node_modules/framer-motion/dist/es/context/SharedLayoutContext.js");
/* harmony import */ var _utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../utils/use-isomorphic-effect.js */ "./node_modules/framer-motion/dist/es/utils/use-isomorphic-effect.js");




function useSnapshotOnUnmount(visualElement) {
    var syncLayout = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_1__.SharedLayoutContext);
    var framerSyncLayout = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_1__.FramerTreeLayoutContext);
    (0,_utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_2__.useIsomorphicLayoutEffect)(function () { return function () {
        if ((0,_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_1__.isSharedLayout)(syncLayout)) {
            syncLayout.remove(visualElement);
        }
        if ((0,_context_SharedLayoutContext_js__WEBPACK_IMPORTED_MODULE_1__.isSharedLayout)(framerSyncLayout)) {
            framerSyncLayout.remove(visualElement);
        }
    }; }, []);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/layout/utils.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/layout/utils.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "tweenAxis": () => (/* binding */ tweenAxis)
/* harmony export */ });
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/mix.js");


function tweenAxis(target, prev, next, p) {
    target.min = (0,popmotion__WEBPACK_IMPORTED_MODULE_0__.mix)(prev.min, next.min, p);
    target.max = (0,popmotion__WEBPACK_IMPORTED_MODULE_0__.mix)(prev.max, next.max, p);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/features/use-features.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/features/use-features.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useFeatures": () => (/* binding */ useFeatures)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");




/**
 * Load features via renderless components based on the provided MotionProps.
 * TODO: Look into porting this to a component-less appraoch.
 */
function useFeatures(defaultFeatures, visualElement, props) {
    var plugins = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__.MotionConfigContext);
    var allFeatures = (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__spread)(defaultFeatures, plugins.features);
    var numFeatures = allFeatures.length;
    var features = [];
    // Decide which features we should render and add them to the returned array
    for (var i = 0; i < numFeatures; i++) {
        var _a = allFeatures[i], shouldRender = _a.shouldRender, key = _a.key, getComponent = _a.getComponent;
        if (shouldRender(props)) {
            var Component = getComponent(props);
            Component &&
                features.push((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Component, (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)({ key: key }, props, { visualElement: visualElement })));
        }
    }
    return features;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/index.js":
/*!************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/index.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createMotionComponent": () => (/* binding */ createMotionComponent)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");
/* harmony import */ var _features_use_features_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./features/use-features.js */ "./node_modules/framer-motion/dist/es/motion/features/use-features.js");
/* harmony import */ var _context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../context/MotionContext/index.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/index.js");
/* harmony import */ var _utils_use_visual_element_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/use-visual-element.js */ "./node_modules/framer-motion/dist/es/motion/utils/use-visual-element.js");
/* harmony import */ var _utils_use_motion_ref_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/use-motion-ref.js */ "./node_modules/framer-motion/dist/es/motion/utils/use-motion-ref.js");
/* harmony import */ var _context_MotionContext_create_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../context/MotionContext/create.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/create.js");








/**
 * Create a `motion` component.
 *
 * This function accepts a Component argument, which can be either a string (ie "div"
 * for `motion.div`), or an actual React component.
 *
 * Alongside this is a config option which provides a way of rendering the provided
 * component "offline", or outside the React render cycle.
 *
 * @internal
 */
function createMotionComponent(_a) {
    var defaultFeatures = _a.defaultFeatures, createVisualElement = _a.createVisualElement, useRender = _a.useRender, useVisualState = _a.useVisualState;
    function MotionComponent(props, externalRef) {
        /**
         * If we're rendering in a static environment, we only visually update the component
         * as a result of a React-rerender rather than interactions or animations. This
         * means we don't need to load additional memory structures like VisualElement,
         * or any gesture/animation features.
         */
        var isStatic = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__.MotionConfigContext).isStatic;
        var features = null;
        /**
         * Create the tree context. This is memoized and will only trigger renders
         * when the current tree variant changes in static mode.
         */
        var context = (0,_context_MotionContext_create_js__WEBPACK_IMPORTED_MODULE_2__.useCreateMotionContext)(props, isStatic);
        /**
         *
         */
        var visualState = useVisualState(props, isStatic);
        if (!isStatic && typeof window !== "undefined") {
            /**
             * Create a VisualElement for this component. A VisualElement provides a common
             * interface to renderer-specific APIs (ie DOM/Three.js etc) as well as
             * providing a way of rendering to these APIs outside of the React render loop
             * for more performant animations and interactions
             */
            context.visualElement = (0,_utils_use_visual_element_js__WEBPACK_IMPORTED_MODULE_3__.useVisualElement)(isStatic, visualState, createVisualElement, props);
            /**
             * Load Motion gesture and animation features. These are rendered as renderless
             * components so each feature can optionally make use of React lifecycle methods.
             *
             * TODO: The intention is to move these away from a React-centric to a
             * VisualElement-centric lifecycle scheme.
             */
            features = (0,_features_use_features_js__WEBPACK_IMPORTED_MODULE_4__.useFeatures)(defaultFeatures, context.visualElement, props);
        }
        /**
         * The mount order and hierarchy is specific to ensure our element ref
         * is hydrated by the time features fire their effects.
         */
        return ((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null,
            (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_5__.MotionContext.Provider, { value: context }, useRender(props, (0,_utils_use_motion_ref_js__WEBPACK_IMPORTED_MODULE_6__.useMotionRef)(visualState, context.visualElement, externalRef), visualState, isStatic)),
            features));
    }
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(MotionComponent);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/utils/is-forced-motion-value.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/utils/is-forced-motion-value.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isForcedMotionValue": () => (/* binding */ isForcedMotionValue)
/* harmony export */ });
/* harmony import */ var _render_dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../render/dom/projection/scale-correction.js */ "./node_modules/framer-motion/dist/es/render/dom/projection/scale-correction.js");
/* harmony import */ var _render_html_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../render/html/utils/transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js");



function isForcedMotionValue(key, _a) {
    var layout = _a.layout, layoutId = _a.layoutId;
    return ((0,_render_html_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__.isTransformProp)(key) ||
        (0,_render_html_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__.isTransformOriginProp)(key) ||
        ((layout || layoutId !== undefined) && !!_render_dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_1__.valueScaleCorrection[key]));
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/utils/make-renderless-component.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/utils/make-renderless-component.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "makeRenderlessComponent": () => (/* binding */ makeRenderlessComponent)
/* harmony export */ });
var makeRenderlessComponent = function (hook) { return function (props) {
    hook(props);
    return null;
}; };




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/utils/should-inherit-variant.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/utils/should-inherit-variant.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "checkShouldInheritVariant": () => (/* binding */ checkShouldInheritVariant)
/* harmony export */ });
function checkShouldInheritVariant(_a) {
    var animate = _a.animate, variants = _a.variants, inherit = _a.inherit;
    return inherit !== null && inherit !== void 0 ? inherit : (!!variants && !animate);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/utils/use-motion-ref.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/utils/use-motion-ref.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useMotionRef": () => (/* binding */ useMotionRef)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_is_ref_object_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/is-ref-object.js */ "./node_modules/framer-motion/dist/es/utils/is-ref-object.js");



/**
 * Creates a ref function that, when called, hydrates the provided
 * external ref and VisualElement.
 */
function useMotionRef(visualState, visualElement, externalRef) {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function (instance) {
        var _a;
        instance && ((_a = visualState.mount) === null || _a === void 0 ? void 0 : _a.call(visualState, instance));
        if (visualElement) {
            instance ? visualElement.mount(instance) : visualElement.unmount();
        }
        if (externalRef) {
            if (typeof externalRef === "function") {
                externalRef(instance);
            }
            else if ((0,_utils_is_ref_object_js__WEBPACK_IMPORTED_MODULE_1__.isRefObject)(externalRef)) {
                externalRef.current = instance;
            }
        }
    }, []);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/utils/use-visual-element.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/utils/use-visual-element.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useVisualElement": () => (/* binding */ useVisualElement)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");
/* harmony import */ var _context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../context/MotionContext/index.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/index.js");
/* harmony import */ var _context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../context/PresenceContext.js */ "./node_modules/framer-motion/dist/es/context/PresenceContext.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../components/AnimatePresence/use-presence.js */ "./node_modules/framer-motion/dist/es/components/AnimatePresence/use-presence.js");
/* harmony import */ var _context_LayoutGroupContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../context/LayoutGroupContext.js */ "./node_modules/framer-motion/dist/es/context/LayoutGroupContext.js");
/* harmony import */ var _utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../utils/use-isomorphic-effect.js */ "./node_modules/framer-motion/dist/es/utils/use-isomorphic-effect.js");
/* harmony import */ var _features_layout_use_snapshot_on_unmount_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../features/layout/use-snapshot-on-unmount.js */ "./node_modules/framer-motion/dist/es/motion/features/layout/use-snapshot-on-unmount.js");











function useLayoutId(_a) {
    var layoutId = _a.layoutId;
    var layoutGroupId = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_LayoutGroupContext_js__WEBPACK_IMPORTED_MODULE_1__.LayoutGroupContext);
    return layoutGroupId && layoutId !== undefined
        ? layoutGroupId + "-" + layoutId
        : layoutId;
}
function useVisualElement(isStatic, visualState, createVisualElement, props) {
    var config = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_2__.MotionConfigContext);
    var parent = (0,_context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_3__.useVisualElementContext)();
    var presenceContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_4__.PresenceContext);
    var layoutId = useLayoutId(props);
    var visualElement = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_5__.useConstant)(function () {
        return createVisualElement(isStatic, {
            visualState: visualState,
            parent: parent,
            props: (0,tslib__WEBPACK_IMPORTED_MODULE_6__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_6__.__assign)({}, props), { layoutId: layoutId }),
            presenceId: presenceContext === null || presenceContext === void 0 ? void 0 : presenceContext.id,
            blockInitialAnimation: (presenceContext === null || presenceContext === void 0 ? void 0 : presenceContext.initial) === false,
        });
    });
    (0,_utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_7__.useIsomorphicLayoutEffect)(function () {
        visualElement.setProps((0,tslib__WEBPACK_IMPORTED_MODULE_6__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_6__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_6__.__assign)({}, config), props), { layoutId: layoutId }));
        visualElement.isPresent = (0,_components_AnimatePresence_use_presence_js__WEBPACK_IMPORTED_MODULE_8__.isPresent)(presenceContext);
        visualElement.isPresenceRoot =
            !parent || parent.presenceId !== (presenceContext === null || presenceContext === void 0 ? void 0 : presenceContext.id);
        /**
         * Fire a render to ensure the latest state is reflected on-screen.
         */
        visualElement.syncRender();
    });
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        var _a;
        /**
         * In a future refactor we can replace the features-as-components and
         * have this loop through them all firing "effect" listeners
         */
        (_a = visualElement.animationState) === null || _a === void 0 ? void 0 : _a.animateChanges();
    });
    /**
     * If this component is a child of AnimateSharedLayout, we need to snapshot the component
     * before it's unmounted. This lives here rather than in features/layout/Measure because
     * as a child component its unmount effect runs after this component has been unmounted.
     */
    (0,_features_layout_use_snapshot_on_unmount_js__WEBPACK_IMPORTED_MODULE_9__.useSnapshotOnUnmount)(visualElement);
    return visualElement;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/utils/use-visual-state.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/utils/use-visual-state.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "makeUseVisualState": () => (/* binding */ makeUseVisualState)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../context/MotionContext/index.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/index.js");
/* harmony import */ var _context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../context/PresenceContext.js */ "./node_modules/framer-motion/dist/es/context/PresenceContext.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _render_utils_variants_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../render/utils/variants.js */ "./node_modules/framer-motion/dist/es/render/utils/variants.js");
/* harmony import */ var _animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../animation/animation-controls.js */ "./node_modules/framer-motion/dist/es/animation/animation-controls.js");
/* harmony import */ var _value_utils_resolve_motion_value_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../value/utils/resolve-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/resolve-motion-value.js");









function makeState(_a, props, context, presenceContext) {
    var scrapeMotionValuesFromProps = _a.scrapeMotionValuesFromProps, createRenderState = _a.createRenderState, onMount = _a.onMount;
    var state = {
        latestValues: makeLatestValues(props, context, presenceContext, scrapeMotionValuesFromProps),
        renderState: createRenderState(),
    };
    if (onMount) {
        state.mount = function (instance) { return onMount(props, instance, state); };
    }
    return state;
}
var makeUseVisualState = function (config) { return function (props, isStatic) {
    var context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_1__.MotionContext);
    var presenceContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_PresenceContext_js__WEBPACK_IMPORTED_MODULE_2__.PresenceContext);
    return isStatic
        ? makeState(config, props, context, presenceContext)
        : (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_3__.useConstant)(function () { return makeState(config, props, context, presenceContext); });
}; };
function makeLatestValues(props, context, presenceContext, scrapeMotionValues) {
    var values = {};
    var blockInitialAnimation = (presenceContext === null || presenceContext === void 0 ? void 0 : presenceContext.initial) === false;
    var motionValues = scrapeMotionValues(props);
    for (var key in motionValues) {
        values[key] = (0,_value_utils_resolve_motion_value_js__WEBPACK_IMPORTED_MODULE_4__.resolveMotionValue)(motionValues[key]);
    }
    var initial = props.initial, animate = props.animate;
    var isControllingVariants = (0,_render_utils_variants_js__WEBPACK_IMPORTED_MODULE_5__.checkIfControllingVariants)(props);
    var isVariantNode = (0,_render_utils_variants_js__WEBPACK_IMPORTED_MODULE_5__.checkIfVariantNode)(props);
    if (context &&
        isVariantNode &&
        !isControllingVariants &&
        props.inherit !== false) {
        initial !== null && initial !== void 0 ? initial : (initial = context.initial);
        animate !== null && animate !== void 0 ? animate : (animate = context.animate);
    }
    var variantToSet = blockInitialAnimation || initial === false ? animate : initial;
    if (variantToSet &&
        typeof variantToSet !== "boolean" &&
        !(0,_animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_6__.isAnimationControls)(variantToSet)) {
        var list = Array.isArray(variantToSet) ? variantToSet : [variantToSet];
        list.forEach(function (definition) {
            var resolved = (0,_render_utils_variants_js__WEBPACK_IMPORTED_MODULE_5__.resolveVariantFromProps)(props, definition);
            if (!resolved)
                return;
            var transitionEnd = resolved.transitionEnd, transition = resolved.transition, target = (0,tslib__WEBPACK_IMPORTED_MODULE_7__.__rest)(resolved, ["transitionEnd", "transition"]);
            for (var key in target)
                values[key] = target[key];
            for (var key in transitionEnd)
                values[key] = transitionEnd[key];
        });
    }
    return values;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/motion/utils/valid-prop.js":
/*!***********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/motion/utils/valid-prop.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isValidMotionProp": () => (/* binding */ isValidMotionProp)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _gestures_types_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../gestures/types.js */ "./node_modules/framer-motion/dist/es/gestures/types.js");



/**
 * A list of all valid MotionProps.
 *
 * @internalremarks
 * This doesn't throw if a `MotionProp` name is missing - it should.
 */
var validMotionProps = new Set((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__spread)([
    "initial",
    "animate",
    "exit",
    "style",
    "variants",
    "transition",
    "transformTemplate",
    "transformValues",
    "custom",
    "inherit",
    "layout",
    "layoutId",
    "onLayoutAnimationComplete",
    "onViewportBoxUpdate",
    "onLayoutMeasure",
    "onBeforeLayoutMeasure",
    "onAnimationStart",
    "onAnimationComplete",
    "onUpdate",
    "onDragStart",
    "onDrag",
    "onDragEnd",
    "onMeasureDragConstraints",
    "onDirectionLock",
    "onDragTransitionEnd",
    "drag",
    "dragControls",
    "dragListener",
    "dragConstraints",
    "dragDirectionLock",
    "_dragX",
    "_dragY",
    "dragElastic",
    "dragMomentum",
    "dragPropagation",
    "dragTransition",
    "whileDrag"
], _gestures_types_js__WEBPACK_IMPORTED_MODULE_1__.gestureProps));
/**
 * Check whether a prop name is a valid `MotionProp` key.
 *
 * @param key - Name of the property to check
 * @returns `true` is key is a valid `MotionProp`.
 *
 * @public
 */
function isValidMotionProp(key) {
    return validMotionProps.has(key);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/motion-minimal.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/motion-minimal.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "m": () => (/* binding */ m)
/* harmony export */ });
/* harmony import */ var _motion_features_layout_Measure_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../motion/features/layout/Measure.js */ "./node_modules/framer-motion/dist/es/motion/features/layout/Measure.js");
/* harmony import */ var _motion_proxy_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./motion-proxy.js */ "./node_modules/framer-motion/dist/es/render/dom/motion-proxy.js");



/**
 * @public
 */
var m = (0,_motion_proxy_js__WEBPACK_IMPORTED_MODULE_0__.createMotionProxy)([_motion_features_layout_Measure_js__WEBPACK_IMPORTED_MODULE_1__.MeasureLayout]);




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/motion-proxy.js":
/*!***********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/motion-proxy.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createMotionProxy": () => (/* binding */ createMotionProxy)
/* harmony export */ });
/* harmony import */ var _motion_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../motion/index.js */ "./node_modules/framer-motion/dist/es/motion/index.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var _utils_create_config_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/create-config.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/create-config.js");




/**
 * Convert any React component into a `motion` component. The provided component
 * **must** use `React.forwardRef` to the underlying DOM component you want to animate.
 *
 * ```jsx
 * const Component = React.forwardRef((props, ref) => {
 *   return <div ref={ref} />
 * })
 *
 * const MotionComponent = motion(Component)
 * ```
 *
 * @public
 */
function createMotionProxy(defaultFeatures) {
    function custom(Component, _a) {
        var _b = (_a === void 0 ? {} : _a).forwardMotionProps, forwardMotionProps = _b === void 0 ? false : _b;
        return (0,_motion_index_js__WEBPACK_IMPORTED_MODULE_1__.createMotionComponent)((0,_utils_create_config_js__WEBPACK_IMPORTED_MODULE_2__.createDomMotionConfig)(defaultFeatures, Component, forwardMotionProps));
    }
    function deprecatedCustom(Component) {
        (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.warning)(false, "motion.custom() is deprecated. Use motion() instead.");
        return custom(Component, { forwardMotionProps: true });
    }
    /**
     * A cache of generated `motion` components, e.g `motion.div`, `motion.input` etc.
     * Rather than generating them anew every render.
     */
    var componentCache = new Map();
    return new Proxy(custom, {
        /**
         * Called when `motion` is referenced with a prop: `motion.div`, `motion.input` etc.
         * The prop name is passed through as `key` and we can use that to generate a `motion`
         * DOM component with that name.
         */
        get: function (_target, key) {
            /**
             * Can be removed in 4.0
             */
            if (key === "custom")
                return deprecatedCustom;
            /**
             * If this element doesn't exist in the component cache, create it and cache.
             */
            if (!componentCache.has(key)) {
                componentCache.set(key, custom(key));
            }
            return componentCache.get(key);
        },
    });
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/motion.js":
/*!*****************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/motion.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createDomMotionComponent": () => (/* binding */ createDomMotionComponent),
/* harmony export */   "motion": () => (/* binding */ motion)
/* harmony export */ });
/* harmony import */ var _motion_index_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../motion/index.js */ "./node_modules/framer-motion/dist/es/motion/index.js");
/* harmony import */ var _motion_features_drag_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../motion/features/drag.js */ "./node_modules/framer-motion/dist/es/motion/features/drag.js");
/* harmony import */ var _motion_features_gestures_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../motion/features/gestures.js */ "./node_modules/framer-motion/dist/es/motion/features/gestures.js");
/* harmony import */ var _motion_features_exit_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../motion/features/exit.js */ "./node_modules/framer-motion/dist/es/motion/features/exit.js");
/* harmony import */ var _motion_features_animation_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../motion/features/animation.js */ "./node_modules/framer-motion/dist/es/motion/features/animation.js");
/* harmony import */ var _motion_features_layout_Animate_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../motion/features/layout/Animate.js */ "./node_modules/framer-motion/dist/es/motion/features/layout/Animate.js");
/* harmony import */ var _motion_features_layout_Measure_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../motion/features/layout/Measure.js */ "./node_modules/framer-motion/dist/es/motion/features/layout/Measure.js");
/* harmony import */ var _utils_create_config_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/create-config.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/create-config.js");
/* harmony import */ var _motion_proxy_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./motion-proxy.js */ "./node_modules/framer-motion/dist/es/render/dom/motion-proxy.js");










var allMotionFeatures = [
    _motion_features_layout_Measure_js__WEBPACK_IMPORTED_MODULE_0__.MeasureLayout,
    _motion_features_animation_js__WEBPACK_IMPORTED_MODULE_1__.Animation,
    _motion_features_drag_js__WEBPACK_IMPORTED_MODULE_2__.Drag,
    _motion_features_gestures_js__WEBPACK_IMPORTED_MODULE_3__.Gestures,
    _motion_features_exit_js__WEBPACK_IMPORTED_MODULE_4__.Exit,
    _motion_features_layout_Animate_js__WEBPACK_IMPORTED_MODULE_5__.AnimateLayout,
];
/**
 * HTML & SVG components, optimised for use with gestures and animation. These can be used as
 * drop-in replacements for any HTML & SVG component, all CSS & SVG properties are supported.
 *
 * @public
 */
var motion = /*@__PURE__*/ (0,_motion_proxy_js__WEBPACK_IMPORTED_MODULE_6__.createMotionProxy)(allMotionFeatures);
/**
 * Create a DOM `motion` component with the provided string. This is primarily intended
 * as a full alternative to `motion` for consumers who have to support environments that don't
 * support `Proxy`.
 *
 * ```javascript
 * import { createDomMotionComponent } from "framer-motion"
 *
 * const motion = {
 *   div: createDomMotionComponent('div')
 * }
 * ```
 *
 * @public
 */
function createDomMotionComponent(key) {
    return (0,_motion_index_js__WEBPACK_IMPORTED_MODULE_7__.createMotionComponent)((0,_utils_create_config_js__WEBPACK_IMPORTED_MODULE_8__.createDomMotionConfig)(allMotionFeatures, key, false));
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/projection/measure.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/projection/measure.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getBoundingBox": () => (/* binding */ getBoundingBox)
/* harmony export */ });
/* harmony import */ var _utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../utils/geometry/index.js */ "./node_modules/framer-motion/dist/es/utils/geometry/index.js");


/**
 * Measure and return the element bounding box.
 *
 * We convert the box into an AxisBox2D to make it easier to work with each axis
 * individually and programmatically.
 *
 * This function optionally accepts a transformPagePoint function which allows us to compensate
 * for, for instance, measuring the element within a scaled plane like a Framer devivce preview component.
 */
function getBoundingBox(element, transformPagePoint) {
    var box = element.getBoundingClientRect();
    return (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.convertBoundingBoxToAxisBox)((0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.transformBoundingBox)(box, transformPagePoint));
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/projection/scale-correction.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/projection/scale-correction.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "addScaleCorrection": () => (/* binding */ addScaleCorrection),
/* harmony export */   "correctBorderRadius": () => (/* binding */ correctBorderRadius),
/* harmony export */   "correctBoxShadow": () => (/* binding */ correctBoxShadow),
/* harmony export */   "pixelsToPercent": () => (/* binding */ pixelsToPercent),
/* harmony export */   "valueScaleCorrection": () => (/* binding */ valueScaleCorrection)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/mix.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/numbers/units.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/complex/index.js");
/* harmony import */ var _utils_css_variables_conversion_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/css-variables-conversion.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/css-variables-conversion.js");





function pixelsToPercent(pixels, axis) {
    return (pixels / (axis.max - axis.min)) * 100;
}
/**
 * We always correct borderRadius as a percentage rather than pixels to reduce paints.
 * For example, if you are projecting a box that is 100px wide with a 10px borderRadius
 * into a box that is 200px wide with a 20px borderRadius, that is actually a 10%
 * borderRadius in both states. If we animate between the two in pixels that will trigger
 * a paint each time. If we animate between the two in percentage we'll avoid a paint.
 */
function correctBorderRadius(latest, _layoutState, _a) {
    var target = _a.target;
    /**
     * If latest is a string, if it's a percentage we can return immediately as it's
     * going to be stretched appropriately. Otherwise, if it's a pixel, convert it to a number.
     */
    if (typeof latest === "string") {
        if (style_value_types__WEBPACK_IMPORTED_MODULE_0__.px.test(latest)) {
            latest = parseFloat(latest);
        }
        else {
            return latest;
        }
    }
    /**
     * If latest is a number, it's a pixel value. We use the current viewportBox to calculate that
     * pixel value as a percentage of each axis
     */
    var x = pixelsToPercent(latest, target.x);
    var y = pixelsToPercent(latest, target.y);
    return x + "% " + y + "%";
}
var varToken = "_$css";
function correctBoxShadow(latest, _a) {
    var delta = _a.delta, treeScale = _a.treeScale;
    var original = latest;
    /**
     * We need to first strip and store CSS variables from the string.
     */
    var containsCSSVariables = latest.includes("var(");
    var cssVariables = [];
    if (containsCSSVariables) {
        latest = latest.replace(_utils_css_variables_conversion_js__WEBPACK_IMPORTED_MODULE_1__.cssVariableRegex, function (match) {
            cssVariables.push(match);
            return varToken;
        });
    }
    var shadow = style_value_types__WEBPACK_IMPORTED_MODULE_2__.complex.parse(latest);
    // TODO: Doesn't support multiple shadows
    if (shadow.length > 5)
        return original;
    var template = style_value_types__WEBPACK_IMPORTED_MODULE_2__.complex.createTransformer(latest);
    var offset = typeof shadow[0] !== "number" ? 1 : 0;
    // Calculate the overall context scale
    var xScale = delta.x.scale * treeScale.x;
    var yScale = delta.y.scale * treeScale.y;
    shadow[0 + offset] /= xScale;
    shadow[1 + offset] /= yScale;
    /**
     * Ideally we'd correct x and y scales individually, but because blur and
     * spread apply to both we have to take a scale average and apply that instead.
     * We could potentially improve the outcome of this by incorporating the ratio between
     * the two scales.
     */
    var averageScale = (0,popmotion__WEBPACK_IMPORTED_MODULE_3__.mix)(xScale, yScale, 0.5);
    // Blur
    if (typeof shadow[2 + offset] === "number")
        shadow[2 + offset] /= averageScale;
    // Spread
    if (typeof shadow[3 + offset] === "number")
        shadow[3 + offset] /= averageScale;
    var output = template(shadow);
    if (containsCSSVariables) {
        var i_1 = 0;
        output = output.replace(varToken, function () {
            var cssVariable = cssVariables[i_1];
            i_1++;
            return cssVariable;
        });
    }
    return output;
}
var borderCorrectionDefinition = {
    process: correctBorderRadius,
};
var valueScaleCorrection = {
    borderRadius: (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)({}, borderCorrectionDefinition), { applyTo: [
            "borderTopLeftRadius",
            "borderTopRightRadius",
            "borderBottomLeftRadius",
            "borderBottomRightRadius",
        ] }),
    borderTopLeftRadius: borderCorrectionDefinition,
    borderTopRightRadius: borderCorrectionDefinition,
    borderBottomLeftRadius: borderCorrectionDefinition,
    borderBottomRightRadius: borderCorrectionDefinition,
    boxShadow: {
        process: correctBoxShadow,
    },
};
/**
 * @internal
 */
function addScaleCorrection(correctors) {
    for (var key in correctors) {
        valueScaleCorrection[key] = correctors[key];
    }
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/use-render.js":
/*!*********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/use-render.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createUseRender": () => (/* binding */ createUseRender)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_is_svg_component_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/is-svg-component.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/is-svg-component.js");
/* harmony import */ var _html_use_props_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../html/use-props.js */ "./node_modules/framer-motion/dist/es/render/html/use-props.js");
/* harmony import */ var _utils_filter_props_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/filter-props.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/filter-props.js");
/* harmony import */ var _svg_use_props_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../svg/use-props.js */ "./node_modules/framer-motion/dist/es/render/svg/use-props.js");







function createUseRender(Component, forwardMotionProps) {
    if (forwardMotionProps === void 0) { forwardMotionProps = false; }
    var useRender = function (props, ref, _a, isStatic) {
        var latestValues = _a.latestValues;
        var useVisualProps = (0,_utils_is_svg_component_js__WEBPACK_IMPORTED_MODULE_1__.isSVGComponent)(Component)
            ? _svg_use_props_js__WEBPACK_IMPORTED_MODULE_2__.useSVGProps
            : _html_use_props_js__WEBPACK_IMPORTED_MODULE_3__.useHTMLProps;
        var visualProps = useVisualProps(props, latestValues, isStatic);
        var filteredProps = (0,_utils_filter_props_js__WEBPACK_IMPORTED_MODULE_4__.filterProps)(props, typeof Component === "string", forwardMotionProps);
        var elementProps = (0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)({}, filteredProps), visualProps), { ref: ref });
        return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Component, elementProps);
    };
    return useRender;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/camel-to-dash.js":
/*!******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/camel-to-dash.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "camelToDash": () => (/* binding */ camelToDash)
/* harmony export */ });
var CAMEL_CASE_PATTERN = /([a-z])([A-Z])/g;
var REPLACE_TEMPLATE = "$1-$2";
/**
 * Convert camelCase to dash-case properties.
 */
var camelToDash = function (str) {
    return str.replace(CAMEL_CASE_PATTERN, REPLACE_TEMPLATE).toLowerCase();
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/create-config.js":
/*!******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/create-config.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createDomMotionConfig": () => (/* binding */ createDomMotionConfig)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _is_svg_component_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-svg-component.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/is-svg-component.js");
/* harmony import */ var _use_render_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../use-render.js */ "./node_modules/framer-motion/dist/es/render/dom/use-render.js");
/* harmony import */ var _svg_config_motion_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../svg/config-motion.js */ "./node_modules/framer-motion/dist/es/render/svg/config-motion.js");
/* harmony import */ var _html_config_motion_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../html/config-motion.js */ "./node_modules/framer-motion/dist/es/render/html/config-motion.js");






function createDomMotionConfig(defaultFeatures, Component, forwardMotionProps) {
    var baseConfig = (0,_is_svg_component_js__WEBPACK_IMPORTED_MODULE_0__.isSVGComponent)(Component)
        ? _svg_config_motion_js__WEBPACK_IMPORTED_MODULE_1__.svgMotionConfig
        : _html_config_motion_js__WEBPACK_IMPORTED_MODULE_2__.htmlMotionConfig;
    return (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({}, baseConfig), { defaultFeatures: defaultFeatures, useRender: (0,_use_render_js__WEBPACK_IMPORTED_MODULE_4__.createUseRender)(Component, forwardMotionProps) });
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/css-variables-conversion.js":
/*!*****************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/css-variables-conversion.js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "cssVariableRegex": () => (/* binding */ cssVariableRegex),
/* harmony export */   "parseCSSVariable": () => (/* binding */ parseCSSVariable),
/* harmony export */   "resolveCSSVariables": () => (/* binding */ resolveCSSVariables)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");



function isCSSVariable(value) {
    return typeof value === "string" && value.startsWith("var(--");
}
/**
 * Parse Framer's special CSS variable format into a CSS token and a fallback.
 *
 * ```
 * `var(--foo, #fff)` => [`--foo`, '#fff']
 * ```
 *
 * @param current
 */
var cssVariableRegex = /var\((--[a-zA-Z0-9-_]+),? ?([a-zA-Z0-9 ()%#.,-]+)?\)/;
function parseCSSVariable(current) {
    var match = cssVariableRegex.exec(current);
    if (!match)
        return [,];
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)(match, 3), token = _a[1], fallback = _a[2];
    return [token, fallback];
}
var maxDepth = 4;
function getVariableValue(current, element, depth) {
    if (depth === void 0) { depth = 1; }
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(depth <= maxDepth, "Max CSS variable fallback depth detected in property \"" + current + "\". This may indicate a circular fallback dependency.");
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)(parseCSSVariable(current), 2), token = _a[0], fallback = _a[1];
    // No CSS variable detected
    if (!token)
        return;
    // Attempt to read this CSS variable off the element
    var resolved = window.getComputedStyle(element).getPropertyValue(token);
    if (resolved) {
        return resolved.trim();
    }
    else if (isCSSVariable(fallback)) {
        // The fallback might itself be a CSS variable, in which case we attempt to resolve it too.
        return getVariableValue(fallback, element, depth + 1);
    }
    else {
        return fallback;
    }
}
/**
 * Resolve CSS variables from
 *
 * @internal
 */
function resolveCSSVariables(visualElement, _a, transitionEnd) {
    var _b;
    var target = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, []);
    var element = visualElement.getInstance();
    if (!(element instanceof HTMLElement))
        return { target: target, transitionEnd: transitionEnd };
    // If `transitionEnd` isn't `undefined`, clone it. We could clone `target` and `transitionEnd`
    // only if they change but I think this reads clearer and this isn't a performance-critical path.
    if (transitionEnd) {
        transitionEnd = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, transitionEnd);
    }
    // Go through existing `MotionValue`s and ensure any existing CSS variables are resolved
    visualElement.forEachValue(function (value) {
        var current = value.get();
        if (!isCSSVariable(current))
            return;
        var resolved = getVariableValue(current, element);
        if (resolved)
            value.set(resolved);
    });
    // Cycle through every target property and resolve CSS variables. Currently
    // we only read single-var properties like `var(--foo)`, not `calc(var(--foo) + 20px)`
    for (var key in target) {
        var current = target[key];
        if (!isCSSVariable(current))
            continue;
        var resolved = getVariableValue(current, element);
        if (!resolved)
            continue;
        // Clone target if it hasn't already been
        target[key] = resolved;
        // If the user hasn't already set this key on `transitionEnd`, set it to the unresolved
        // CSS variable. This will ensure that after the animation the component will reflect
        // changes in the value of the CSS variable.
        if (transitionEnd)
            (_b = transitionEnd[key]) !== null && _b !== void 0 ? _b : (transitionEnd[key] = current);
    }
    return { target: target, transitionEnd: transitionEnd };
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/filter-props.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/filter-props.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "filterProps": () => (/* binding */ filterProps)
/* harmony export */ });
/* harmony import */ var _motion_utils_valid_prop_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../motion/utils/valid-prop.js */ "./node_modules/framer-motion/dist/es/motion/utils/valid-prop.js");


var shouldForward = function (key) { return !(0,_motion_utils_valid_prop_js__WEBPACK_IMPORTED_MODULE_0__.isValidMotionProp)(key); };
/**
 * Emotion and Styled Components both allow users to pass through arbitrary props to their components
 * to dynamically generate CSS. They both use the `@emotion/is-prop-valid` package to determine which
 * of these should be passed to the underlying DOM node.
 *
 * However, when styling a Motion component `styled(motion.div)`, both packages pass through *all* props
 * as it's seen as an arbitrary component rather than a DOM node. Motion only allows arbitrary props
 * passed through the `custom` prop so it doesn't *need* the payload or computational overhead of
 * `@emotion/is-prop-valid`, however to fix this problem we need to use it.
 *
 * By making it an optionalDependency we can offer this functionality only in the situations where it's
 * actually required.
 */
try {
    var emotionIsPropValid_1 = (__webpack_require__(/*! @emotion/is-prop-valid */ "./node_modules/@emotion/is-prop-valid/dist/is-prop-valid.browser.esm.js")["default"]);
    shouldForward = function (key) {
        // Handle events explicitly as Emotion validates them all as true
        if (key.startsWith("on")) {
            return !(0,_motion_utils_valid_prop_js__WEBPACK_IMPORTED_MODULE_0__.isValidMotionProp)(key);
        }
        else {
            return emotionIsPropValid_1(key);
        }
    };
}
catch (_a) {
    // We don't need to actually do anything here - the fallback is the existing `isPropValid`.
}
function filterProps(props, isDom, forwardMotionProps) {
    var filteredProps = {};
    for (var key in props) {
        if (shouldForward(key) ||
            (forwardMotionProps === true && (0,_motion_utils_valid_prop_js__WEBPACK_IMPORTED_MODULE_0__.isValidMotionProp)(key)) ||
            (!isDom && !(0,_motion_utils_valid_prop_js__WEBPACK_IMPORTED_MODULE_0__.isValidMotionProp)(key))) {
            filteredProps[key] = props[key];
        }
    }
    return filteredProps;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/is-css-variable.js":
/*!********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/is-css-variable.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isCSSVariable": () => (/* binding */ isCSSVariable)
/* harmony export */ });
/**
 * Returns true if the provided key is a CSS variable
 */
function isCSSVariable(key) {
    return key.startsWith("--");
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/is-svg-component.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/is-svg-component.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isSVGComponent": () => (/* binding */ isSVGComponent)
/* harmony export */ });
/* harmony import */ var _svg_supported_elements_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../svg/supported-elements.js */ "./node_modules/framer-motion/dist/es/render/svg/supported-elements.js");


function isSVGComponent(Component) {
    /**
     * If it's not a string, it's a custom React component. Currently we only support
     * HTML custom React components.
     */
    if (typeof Component !== "string")
        return false;
    /**
     * If it contains a dash, the element is a custom HTML webcomponent.
     */
    if (Component.includes("-"))
        return false;
    /**
     * If it's in our list of lowercase SVG tags, it's an SVG component
     */
    if (_svg_supported_elements_js__WEBPACK_IMPORTED_MODULE_0__.lowercaseSVGElements.indexOf(Component) > -1)
        return true;
    /**
     * If it contains a capital letter, it's an SVG component
     */
    if (/[A-Z]/.test(Component))
        return true;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/parse-dom-variant.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/parse-dom-variant.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "parseDomVariant": () => (/* binding */ parseDomVariant)
/* harmony export */ });
/* harmony import */ var _css_variables_conversion_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./css-variables-conversion.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/css-variables-conversion.js");
/* harmony import */ var _unit_conversion_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./unit-conversion.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/unit-conversion.js");



/**
 * Parse a DOM variant to make it animatable. This involves resolving CSS variables
 * and ensuring animations like "20%" => "calc(50vw)" are performed in pixels.
 */
var parseDomVariant = function (visualElement, target, origin, transitionEnd) {
    var resolved = (0,_css_variables_conversion_js__WEBPACK_IMPORTED_MODULE_0__.resolveCSSVariables)(visualElement, target, transitionEnd);
    target = resolved.target;
    transitionEnd = resolved.transitionEnd;
    return (0,_unit_conversion_js__WEBPACK_IMPORTED_MODULE_1__.unitConversion)(visualElement, target, origin, transitionEnd);
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/unit-conversion.js":
/*!********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/unit-conversion.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "BoundingBoxDimension": () => (/* binding */ BoundingBoxDimension),
/* harmony export */   "unitConversion": () => (/* binding */ unitConversion)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/numbers/index.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/numbers/units.js");
/* harmony import */ var _animation_utils_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../animation/utils/is-keyframes-target.js */ "./node_modules/framer-motion/dist/es/animation/utils/is-keyframes-target.js");
/* harmony import */ var _value_types_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./value-types.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js");
/* harmony import */ var _html_utils_transform_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../html/utils/transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js");







var positionalKeys = new Set([
    "width",
    "height",
    "top",
    "left",
    "right",
    "bottom",
    "x",
    "y",
]);
var isPositionalKey = function (key) { return positionalKeys.has(key); };
var hasPositionalKey = function (target) {
    return Object.keys(target).some(isPositionalKey);
};
var setAndResetVelocity = function (value, to) {
    // Looks odd but setting it twice doesn't render, it'll just
    // set both prev and current to the latest value
    value.set(to, false);
    value.set(to);
};
var isNumOrPxType = function (v) {
    return v === style_value_types__WEBPACK_IMPORTED_MODULE_1__.number || v === style_value_types__WEBPACK_IMPORTED_MODULE_2__.px;
};
var BoundingBoxDimension;
(function (BoundingBoxDimension) {
    BoundingBoxDimension["width"] = "width";
    BoundingBoxDimension["height"] = "height";
    BoundingBoxDimension["left"] = "left";
    BoundingBoxDimension["right"] = "right";
    BoundingBoxDimension["top"] = "top";
    BoundingBoxDimension["bottom"] = "bottom";
})(BoundingBoxDimension || (BoundingBoxDimension = {}));
var getPosFromMatrix = function (matrix, pos) {
    return parseFloat(matrix.split(", ")[pos]);
};
var getTranslateFromMatrix = function (pos2, pos3) { return function (_bbox, _a) {
    var transform = _a.transform;
    if (transform === "none" || !transform)
        return 0;
    var matrix3d = transform.match(/^matrix3d\((.+)\)$/);
    if (matrix3d) {
        return getPosFromMatrix(matrix3d[1], pos3);
    }
    else {
        var matrix = transform.match(/^matrix\((.+)\)$/);
        if (matrix) {
            return getPosFromMatrix(matrix[1], pos2);
        }
        else {
            return 0;
        }
    }
}; };
var transformKeys = new Set(["x", "y", "z"]);
var nonTranslationalTransformKeys = _html_utils_transform_js__WEBPACK_IMPORTED_MODULE_3__.transformProps.filter(function (key) { return !transformKeys.has(key); });
function removeNonTranslationalTransform(visualElement) {
    var removedTransforms = [];
    nonTranslationalTransformKeys.forEach(function (key) {
        var value = visualElement.getValue(key);
        if (value !== undefined) {
            removedTransforms.push([key, value.get()]);
            value.set(key.startsWith("scale") ? 1 : 0);
        }
    });
    // Apply changes to element before measurement
    if (removedTransforms.length)
        visualElement.syncRender();
    return removedTransforms;
}
var positionalValues = {
    // Dimensions
    width: function (_a) {
        var x = _a.x;
        return x.max - x.min;
    },
    height: function (_a) {
        var y = _a.y;
        return y.max - y.min;
    },
    top: function (_bbox, _a) {
        var top = _a.top;
        return parseFloat(top);
    },
    left: function (_bbox, _a) {
        var left = _a.left;
        return parseFloat(left);
    },
    bottom: function (_a, _b) {
        var y = _a.y;
        var top = _b.top;
        return parseFloat(top) + (y.max - y.min);
    },
    right: function (_a, _b) {
        var x = _a.x;
        var left = _b.left;
        return parseFloat(left) + (x.max - x.min);
    },
    // Transform
    x: getTranslateFromMatrix(4, 13),
    y: getTranslateFromMatrix(5, 14),
};
var convertChangedValueTypes = function (target, visualElement, changedKeys) {
    var originBbox = visualElement.measureViewportBox();
    var element = visualElement.getInstance();
    var elementComputedStyle = getComputedStyle(element);
    var display = elementComputedStyle.display, top = elementComputedStyle.top, left = elementComputedStyle.left, bottom = elementComputedStyle.bottom, right = elementComputedStyle.right, transform = elementComputedStyle.transform;
    var originComputedStyle = { top: top, left: left, bottom: bottom, right: right, transform: transform };
    // If the element is currently set to display: "none", make it visible before
    // measuring the target bounding box
    if (display === "none") {
        visualElement.setStaticValue("display", target.display || "block");
    }
    // Apply the latest values (as set in checkAndConvertChangedValueTypes)
    visualElement.syncRender();
    var targetBbox = visualElement.measureViewportBox();
    changedKeys.forEach(function (key) {
        // Restore styles to their **calculated computed style**, not their actual
        // originally set style. This allows us to animate between equivalent pixel units.
        var value = visualElement.getValue(key);
        setAndResetVelocity(value, positionalValues[key](originBbox, originComputedStyle));
        target[key] = positionalValues[key](targetBbox, elementComputedStyle);
    });
    return target;
};
var checkAndConvertChangedValueTypes = function (visualElement, target, origin, transitionEnd) {
    if (origin === void 0) { origin = {}; }
    if (transitionEnd === void 0) { transitionEnd = {}; }
    target = (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)({}, target);
    transitionEnd = (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)({}, transitionEnd);
    var targetPositionalKeys = Object.keys(target).filter(isPositionalKey);
    // We want to remove any transform values that could affect the element's bounding box before
    // it's measured. We'll reapply these later.
    var removedTransformValues = [];
    var hasAttemptedToRemoveTransformValues = false;
    var changedValueTypeKeys = [];
    targetPositionalKeys.forEach(function (key) {
        var value = visualElement.getValue(key);
        if (!visualElement.hasValue(key))
            return;
        var from = origin[key];
        var to = target[key];
        var fromType = (0,_value_types_js__WEBPACK_IMPORTED_MODULE_5__.findDimensionValueType)(from);
        var toType;
        // TODO: The current implementation of this basically throws an error
        // if you try and do value conversion via keyframes. There's probably
        // a way of doing this but the performance implications would need greater scrutiny,
        // as it'd be doing multiple resize-remeasure operations.
        if ((0,_animation_utils_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_6__.isKeyframesTarget)(to)) {
            var numKeyframes = to.length;
            for (var i = to[0] === null ? 1 : 0; i < numKeyframes; i++) {
                if (!toType) {
                    toType = (0,_value_types_js__WEBPACK_IMPORTED_MODULE_5__.findDimensionValueType)(to[i]);
                    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(toType === fromType ||
                        (isNumOrPxType(fromType) && isNumOrPxType(toType)), "Keyframes must be of the same dimension as the current value");
                }
                else {
                    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)((0,_value_types_js__WEBPACK_IMPORTED_MODULE_5__.findDimensionValueType)(to[i]) === toType, "All keyframes must be of the same type");
                }
            }
        }
        else {
            toType = (0,_value_types_js__WEBPACK_IMPORTED_MODULE_5__.findDimensionValueType)(to);
        }
        if (fromType !== toType) {
            // If they're both just number or px, convert them both to numbers rather than
            // relying on resize/remeasure to convert (which is wasteful in this situation)
            if (isNumOrPxType(fromType) && isNumOrPxType(toType)) {
                var current = value.get();
                if (typeof current === "string") {
                    value.set(parseFloat(current));
                }
                if (typeof to === "string") {
                    target[key] = parseFloat(to);
                }
                else if (Array.isArray(to) && toType === style_value_types__WEBPACK_IMPORTED_MODULE_2__.px) {
                    target[key] = to.map(parseFloat);
                }
            }
            else if ((fromType === null || fromType === void 0 ? void 0 : fromType.transform) && (toType === null || toType === void 0 ? void 0 : toType.transform) &&
                (from === 0 || to === 0)) {
                // If one or the other value is 0, it's safe to coerce it to the
                // type of the other without measurement
                if (from === 0) {
                    value.set(toType.transform(from));
                }
                else {
                    target[key] = fromType.transform(to);
                }
            }
            else {
                // If we're going to do value conversion via DOM measurements, we first
                // need to remove non-positional transform values that could affect the bbox measurements.
                if (!hasAttemptedToRemoveTransformValues) {
                    removedTransformValues = removeNonTranslationalTransform(visualElement);
                    hasAttemptedToRemoveTransformValues = true;
                }
                changedValueTypeKeys.push(key);
                transitionEnd[key] =
                    transitionEnd[key] !== undefined
                        ? transitionEnd[key]
                        : target[key];
                setAndResetVelocity(value, to);
            }
        }
    });
    if (changedValueTypeKeys.length) {
        var convertedTarget = convertChangedValueTypes(target, visualElement, changedValueTypeKeys);
        // If we removed transform values, reapply them before the next render
        if (removedTransformValues.length) {
            removedTransformValues.forEach(function (_a) {
                var _b = (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__read)(_a, 2), key = _b[0], value = _b[1];
                visualElement.getValue(key).set(value);
            });
        }
        // Reapply original values
        visualElement.syncRender();
        return { target: convertedTarget, transitionEnd: transitionEnd };
    }
    else {
        return { target: target, transitionEnd: transitionEnd };
    }
};
/**
 * Convert value types for x/y/width/height/top/left/bottom/right
 *
 * Allows animation between `'auto'` -> `'100%'` or `0` -> `'calc(50% - 10vw)'`
 *
 * @internal
 */
function unitConversion(visualElement, target, origin, transitionEnd) {
    return hasPositionalKey(target)
        ? checkAndConvertChangedValueTypes(visualElement, target, origin, transitionEnd)
        : { target: target, transitionEnd: transitionEnd };
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "auto": () => (/* binding */ auto),
/* harmony export */   "findDimensionValueType": () => (/* binding */ findDimensionValueType),
/* harmony export */   "findValueType": () => (/* binding */ findValueType),
/* harmony export */   "getAnimatableNone": () => (/* binding */ getAnimatableNone),
/* harmony export */   "getDefaultValueType": () => (/* binding */ getDefaultValueType),
/* harmony export */   "getValueAsType": () => (/* binding */ getValueAsType)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/numbers/index.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/color/index.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/numbers/units.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/complex/filter.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/complex/index.js");



/**
 * ValueType for "auto"
 */
var auto = {
    test: function (v) { return v === "auto"; },
    parse: function (v) { return v; },
};
/**
 * ValueType for ints
 */
var int = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({}, style_value_types__WEBPACK_IMPORTED_MODULE_1__.number), { transform: Math.round });
/**
 * A map of default value types for common values
 */
var defaultValueTypes = {
    // Color props
    color: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    backgroundColor: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    outlineColor: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    fill: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    stroke: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    // Border props
    borderColor: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    borderTopColor: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    borderRightColor: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    borderBottomColor: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    borderLeftColor: style_value_types__WEBPACK_IMPORTED_MODULE_2__.color,
    borderWidth: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderTopWidth: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderRightWidth: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderBottomWidth: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderLeftWidth: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderRadius: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    radius: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderTopLeftRadius: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderTopRightRadius: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderBottomRightRadius: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    borderBottomLeftRadius: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    // Positioning props
    width: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    maxWidth: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    height: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    maxHeight: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    size: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    top: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    right: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    bottom: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    left: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    // Spacing props
    padding: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    paddingTop: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    paddingRight: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    paddingBottom: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    paddingLeft: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    margin: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    marginTop: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    marginRight: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    marginBottom: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    marginLeft: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    // Transform props
    rotate: style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees,
    rotateX: style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees,
    rotateY: style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees,
    rotateZ: style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees,
    scale: style_value_types__WEBPACK_IMPORTED_MODULE_1__.scale,
    scaleX: style_value_types__WEBPACK_IMPORTED_MODULE_1__.scale,
    scaleY: style_value_types__WEBPACK_IMPORTED_MODULE_1__.scale,
    scaleZ: style_value_types__WEBPACK_IMPORTED_MODULE_1__.scale,
    skew: style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees,
    skewX: style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees,
    skewY: style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees,
    distance: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    translateX: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    translateY: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    translateZ: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    x: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    y: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    z: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    perspective: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    transformPerspective: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    opacity: style_value_types__WEBPACK_IMPORTED_MODULE_1__.alpha,
    originX: style_value_types__WEBPACK_IMPORTED_MODULE_3__.progressPercentage,
    originY: style_value_types__WEBPACK_IMPORTED_MODULE_3__.progressPercentage,
    originZ: style_value_types__WEBPACK_IMPORTED_MODULE_3__.px,
    // Misc
    zIndex: int,
    filter: style_value_types__WEBPACK_IMPORTED_MODULE_4__.filter,
    WebkitFilter: style_value_types__WEBPACK_IMPORTED_MODULE_4__.filter,
    // SVG
    fillOpacity: style_value_types__WEBPACK_IMPORTED_MODULE_1__.alpha,
    strokeOpacity: style_value_types__WEBPACK_IMPORTED_MODULE_1__.alpha,
    numOctaves: int,
};
/**
 * A list of value types commonly used for dimensions
 */
var dimensionValueTypes = [style_value_types__WEBPACK_IMPORTED_MODULE_1__.number, style_value_types__WEBPACK_IMPORTED_MODULE_3__.px, style_value_types__WEBPACK_IMPORTED_MODULE_3__.percent, style_value_types__WEBPACK_IMPORTED_MODULE_3__.degrees, style_value_types__WEBPACK_IMPORTED_MODULE_3__.vw, style_value_types__WEBPACK_IMPORTED_MODULE_3__.vh, auto];
/**
 * Tests a provided value against a ValueType
 */
var testValueType = function (v) { return function (type) { return type.test(v); }; };
/**
 * Tests a dimensional value against the list of dimension ValueTypes
 */
var findDimensionValueType = function (v) {
    return dimensionValueTypes.find(testValueType(v));
};
/**
 * A list of all ValueTypes
 */
var valueTypes = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__spread)(dimensionValueTypes, [style_value_types__WEBPACK_IMPORTED_MODULE_2__.color, style_value_types__WEBPACK_IMPORTED_MODULE_5__.complex]);
/**
 * Tests a value against the list of ValueTypes
 */
var findValueType = function (v) { return valueTypes.find(testValueType(v)); };
/**
 * Gets the default ValueType for the provided value key
 */
var getDefaultValueType = function (key) { return defaultValueTypes[key]; };
/**
 * Provided a value and a ValueType, returns the value as that value type.
 */
var getValueAsType = function (value, type) {
    return type && typeof value === "number"
        ? type.transform(value)
        : value;
};
function getAnimatableNone(key, value) {
    var _a;
    var defaultValueType = getDefaultValueType(key);
    if (defaultValueType !== style_value_types__WEBPACK_IMPORTED_MODULE_4__.filter)
        defaultValueType = style_value_types__WEBPACK_IMPORTED_MODULE_5__.complex;
    // If value is not recognised as animatable, ie "none", create an animatable version origin based on the target
    return (_a = defaultValueType.getAnimatableNone) === null || _a === void 0 ? void 0 : _a.call(defaultValueType, value);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/config-motion.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/config-motion.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "htmlMotionConfig": () => (/* binding */ htmlMotionConfig)
/* harmony export */ });
/* harmony import */ var _utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/create-render-state.js */ "./node_modules/framer-motion/dist/es/render/html/utils/create-render-state.js");
/* harmony import */ var _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/scrape-motion-values.js */ "./node_modules/framer-motion/dist/es/render/html/utils/scrape-motion-values.js");
/* harmony import */ var _visual_element_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./visual-element.js */ "./node_modules/framer-motion/dist/es/render/html/visual-element.js");
/* harmony import */ var _motion_utils_use_visual_state_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../motion/utils/use-visual-state.js */ "./node_modules/framer-motion/dist/es/motion/utils/use-visual-state.js");





var htmlMotionConfig = {
    createVisualElement: function (isStatic, options) {
        return (0,_visual_element_js__WEBPACK_IMPORTED_MODULE_0__.htmlVisualElement)(options, { enableHardwareAcceleration: !isStatic });
    },
    useVisualState: (0,_motion_utils_use_visual_state_js__WEBPACK_IMPORTED_MODULE_1__.makeUseVisualState)({
        scrapeMotionValuesFromProps: _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_2__.scrapeMotionValuesFromProps,
        createRenderState: _utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_3__.createHtmlRenderState,
    }),
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/use-props.js":
/*!*********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/use-props.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "copyRawValuesOnly": () => (/* binding */ copyRawValuesOnly),
/* harmony export */   "useHTMLProps": () => (/* binding */ useHTMLProps),
/* harmony export */   "useStyle": () => (/* binding */ useStyle)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _motion_utils_is_forced_motion_value_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../motion/utils/is-forced-motion-value.js */ "./node_modules/framer-motion/dist/es/motion/utils/is-forced-motion-value.js");
/* harmony import */ var _value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../value/utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");
/* harmony import */ var _utils_build_styles_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/build-styles.js */ "./node_modules/framer-motion/dist/es/render/html/utils/build-styles.js");
/* harmony import */ var _utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/create-render-state.js */ "./node_modules/framer-motion/dist/es/render/html/utils/create-render-state.js");







function copyRawValuesOnly(target, source, props) {
    for (var key in source) {
        if (!(0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.isMotionValue)(source[key]) && !(0,_motion_utils_is_forced_motion_value_js__WEBPACK_IMPORTED_MODULE_2__.isForcedMotionValue)(key, props)) {
            target[key] = source[key];
        }
    }
}
function useInitialMotionValues(_a, visualState, isStatic) {
    var transformTemplate = _a.transformTemplate;
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () {
        var state = (0,_utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_3__.createHtmlRenderState)();
        (0,_utils_build_styles_js__WEBPACK_IMPORTED_MODULE_4__.buildHTMLStyles)(state, visualState, undefined, undefined, { enableHardwareAcceleration: !isStatic }, transformTemplate);
        var vars = state.vars, style = state.style;
        return (0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)({}, vars), style);
    }, [visualState]);
}
function useStyle(props, visualState, isStatic) {
    var styleProp = props.style || {};
    var style = {};
    /**
     * Copy non-Motion Values straight into style
     */
    copyRawValuesOnly(style, styleProp, props);
    Object.assign(style, useInitialMotionValues(props, visualState, isStatic));
    if (props.transformValues) {
        style = props.transformValues(style);
    }
    return style;
}
function useHTMLProps(props, visualState, isStatic) {
    // The `any` isn't ideal but it is the type of createElement props argument
    var htmlProps = {};
    var style = useStyle(props, visualState, isStatic);
    if (Boolean(props.drag)) {
        // Disable the ghost element when a user drags
        htmlProps.draggable = false;
        // Disable text selection
        style.userSelect = style.WebkitUserSelect = style.WebkitTouchCallout =
            "none";
        // Disable scrolling on the draggable direction
        style.touchAction =
            props.drag === true
                ? "none"
                : "pan-" + (props.drag === "x" ? "y" : "x");
    }
    htmlProps.style = style;
    return htmlProps;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/utils/build-styles.js":
/*!******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/utils/build-styles.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "buildHTMLStyles": () => (/* binding */ buildHTMLStyles)
/* harmony export */ });
/* harmony import */ var _dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../dom/utils/value-types.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js");
/* harmony import */ var _dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../dom/projection/scale-correction.js */ "./node_modules/framer-motion/dist/es/render/dom/projection/scale-correction.js");
/* harmony import */ var _transform_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js");
/* harmony import */ var _build_transform_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./build-transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/build-transform.js");
/* harmony import */ var _dom_utils_is_css_variable_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../dom/utils/is-css-variable.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/is-css-variable.js");






function buildHTMLStyles(state, latestValues, projection, layoutState, options, transformTemplate) {
    var _a;
    var style = state.style, vars = state.vars, transform = state.transform, transformKeys = state.transformKeys, transformOrigin = state.transformOrigin;
    // Empty the transformKeys array. As we're throwing out refs to its items
    // this might not be as cheap as suspected. Maybe using the array as a buffer
    // with a manual incrementation would be better.
    transformKeys.length = 0;
    // Track whether we encounter any transform or transformOrigin values.
    var hasTransform = false;
    var hasTransformOrigin = false;
    // Does the calculated transform essentially equal "none"?
    var transformIsNone = true;
    /**
     * Loop over all our latest animated values and decide whether to handle them
     * as a style or CSS variable.
     *
     * Transforms and transform origins are kept seperately for further processing.
     */
    for (var key in latestValues) {
        var value = latestValues[key];
        /**
         * If this is a CSS variable we don't do any further processing.
         */
        if ((0,_dom_utils_is_css_variable_js__WEBPACK_IMPORTED_MODULE_0__.isCSSVariable)(key)) {
            vars[key] = value;
            continue;
        }
        // Convert the value to its default value type, ie 0 -> "0px"
        var valueType = (0,_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_1__.getDefaultValueType)(key);
        var valueAsType = (0,_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_1__.getValueAsType)(value, valueType);
        if ((0,_transform_js__WEBPACK_IMPORTED_MODULE_2__.isTransformProp)(key)) {
            // If this is a transform, flag to enable further transform processing
            hasTransform = true;
            transform[key] = valueAsType;
            transformKeys.push(key);
            // If we already know we have a non-default transform, early return
            if (!transformIsNone)
                continue;
            // Otherwise check to see if this is a default transform
            if (value !== ((_a = valueType.default) !== null && _a !== void 0 ? _a : 0))
                transformIsNone = false;
        }
        else if ((0,_transform_js__WEBPACK_IMPORTED_MODULE_2__.isTransformOriginProp)(key)) {
            transformOrigin[key] = valueAsType;
            // If this is a transform origin, flag and enable further transform-origin processing
            hasTransformOrigin = true;
        }
        else {
            /**
             * If layout projection is on, and we need to perform scale correction for this
             * value type, perform it.
             */
            if (layoutState &&
                projection &&
                layoutState.isHydrated &&
                _dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_3__.valueScaleCorrection[key]) {
                var correctedValue = _dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_3__.valueScaleCorrection[key].process(value, layoutState, projection);
                /**
                 * Scale-correctable values can define a number of other values to break
                 * down into. For instance borderRadius needs applying to borderBottomLeftRadius etc
                 */
                var applyTo = _dom_projection_scale_correction_js__WEBPACK_IMPORTED_MODULE_3__.valueScaleCorrection[key].applyTo;
                if (applyTo) {
                    var num = applyTo.length;
                    for (var i = 0; i < num; i++) {
                        style[applyTo[i]] = correctedValue;
                    }
                }
                else {
                    style[key] = correctedValue;
                }
            }
            else {
                style[key] = valueAsType;
            }
        }
    }
    if (layoutState &&
        projection &&
        projection.isEnabled &&
        layoutState.isHydrated) {
        style.transform = (0,_build_transform_js__WEBPACK_IMPORTED_MODULE_4__.buildLayoutProjectionTransform)(layoutState.deltaFinal, layoutState.treeScale, hasTransform ? transform : undefined);
        if (transformTemplate) {
            style.transform = transformTemplate(transform, style.transform);
        }
        style.transformOrigin = (0,_build_transform_js__WEBPACK_IMPORTED_MODULE_4__.buildLayoutProjectionTransformOrigin)(layoutState);
    }
    else {
        if (hasTransform) {
            style.transform = (0,_build_transform_js__WEBPACK_IMPORTED_MODULE_4__.buildTransform)(state, options, transformIsNone, transformTemplate);
        }
        if (hasTransformOrigin) {
            style.transformOrigin = (0,_build_transform_js__WEBPACK_IMPORTED_MODULE_4__.buildTransformOrigin)(transformOrigin);
        }
    }
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/utils/build-transform.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/utils/build-transform.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "buildLayoutProjectionTransform": () => (/* binding */ buildLayoutProjectionTransform),
/* harmony export */   "buildLayoutProjectionTransformOrigin": () => (/* binding */ buildLayoutProjectionTransformOrigin),
/* harmony export */   "buildTransform": () => (/* binding */ buildTransform),
/* harmony export */   "buildTransformOrigin": () => (/* binding */ buildTransformOrigin),
/* harmony export */   "identityProjection": () => (/* binding */ identityProjection)
/* harmony export */ });
/* harmony import */ var _transform_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js");
/* harmony import */ var _utils_state_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/state.js */ "./node_modules/framer-motion/dist/es/render/utils/state.js");



var translateAlias = {
    x: "translateX",
    y: "translateY",
    z: "translateZ",
    transformPerspective: "perspective",
};
/**
 * Build a CSS transform style from individual x/y/scale etc properties.
 *
 * This outputs with a default order of transforms/scales/rotations, this can be customised by
 * providing a transformTemplate function.
 */
function buildTransform(_a, _b, transformIsDefault, transformTemplate) {
    var transform = _a.transform, transformKeys = _a.transformKeys;
    var _c = _b.enableHardwareAcceleration, enableHardwareAcceleration = _c === void 0 ? true : _c, _d = _b.allowTransformNone, allowTransformNone = _d === void 0 ? true : _d;
    // The transform string we're going to build into.
    var transformString = "";
    // Transform keys into their default order - this will determine the output order.
    transformKeys.sort(_transform_js__WEBPACK_IMPORTED_MODULE_0__.sortTransformProps);
    // Track whether the defined transform has a defined z so we don't add a
    // second to enable hardware acceleration
    var transformHasZ = false;
    // Loop over each transform and build them into transformString
    var numTransformKeys = transformKeys.length;
    for (var i = 0; i < numTransformKeys; i++) {
        var key = transformKeys[i];
        transformString += (translateAlias[key] || key) + "(" + transform[key] + ") ";
        if (key === "z")
            transformHasZ = true;
    }
    if (!transformHasZ && enableHardwareAcceleration) {
        transformString += "translateZ(0)";
    }
    else {
        transformString = transformString.trim();
    }
    // If we have a custom `transform` template, pass our transform values and
    // generated transformString to that before returning
    if (transformTemplate) {
        transformString = transformTemplate(transform, transformIsDefault ? "" : transformString);
    }
    else if (allowTransformNone && transformIsDefault) {
        transformString = "none";
    }
    return transformString;
}
/**
 * Build a transformOrigin style. Uses the same defaults as the browser for
 * undefined origins.
 */
function buildTransformOrigin(_a) {
    var _b = _a.originX, originX = _b === void 0 ? "50%" : _b, _c = _a.originY, originY = _c === void 0 ? "50%" : _c, _d = _a.originZ, originZ = _d === void 0 ? 0 : _d;
    return originX + " " + originY + " " + originZ;
}
/**
 * Build a transform style that takes a calculated delta between the element's current
 * space on screen and projects it into the desired space.
 */
function buildLayoutProjectionTransform(_a, treeScale, latestTransform) {
    var x = _a.x, y = _a.y;
    /**
     * The translations we use to calculate are always relative to the viewport coordinate space.
     * But when we apply scales, we also scale the coordinate space of an element and its children.
     * For instance if we have a treeScale (the culmination of all parent scales) of 0.5 and we need
     * to move an element 100 pixels, we actually need to move it 200 in within that scaled space.
     */
    var xTranslate = x.translate / treeScale.x;
    var yTranslate = y.translate / treeScale.y;
    var transform = "translate3d(" + xTranslate + "px, " + yTranslate + "px, 0) ";
    if (latestTransform) {
        var rotate = latestTransform.rotate, rotateX = latestTransform.rotateX, rotateY = latestTransform.rotateY;
        if (rotate)
            transform += "rotate(" + rotate + ") ";
        if (rotateX)
            transform += "rotateX(" + rotateX + ") ";
        if (rotateY)
            transform += "rotateY(" + rotateY + ") ";
    }
    transform += "scale(" + x.scale + ", " + y.scale + ")";
    return !latestTransform && transform === identityProjection ? "" : transform;
}
/**
 * Take the calculated delta origin and apply it as a transform string.
 */
function buildLayoutProjectionTransformOrigin(_a) {
    var deltaFinal = _a.deltaFinal;
    return deltaFinal.x.origin * 100 + "% " + deltaFinal.y.origin * 100 + "% 0";
}
var identityProjection = buildLayoutProjectionTransform(_utils_state_js__WEBPACK_IMPORTED_MODULE_1__.zeroLayout.delta, _utils_state_js__WEBPACK_IMPORTED_MODULE_1__.zeroLayout.treeScale, { x: 1, y: 1 });




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/utils/create-render-state.js":
/*!*************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/utils/create-render-state.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createHtmlRenderState": () => (/* binding */ createHtmlRenderState)
/* harmony export */ });
var createHtmlRenderState = function () { return ({
    style: {},
    transform: {},
    transformKeys: [],
    transformOrigin: {},
    vars: {},
}); };




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/utils/scrape-motion-values.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/utils/scrape-motion-values.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "scrapeMotionValuesFromProps": () => (/* binding */ scrapeMotionValuesFromProps)
/* harmony export */ });
/* harmony import */ var _motion_utils_is_forced_motion_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../motion/utils/is-forced-motion-value.js */ "./node_modules/framer-motion/dist/es/motion/utils/is-forced-motion-value.js");
/* harmony import */ var _value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../value/utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");



function scrapeMotionValuesFromProps(props) {
    var style = props.style;
    var newValues = {};
    for (var key in style) {
        if ((0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_0__.isMotionValue)(style[key]) || (0,_motion_utils_is_forced_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.isForcedMotionValue)(key, props)) {
            newValues[key] = style[key];
        }
    }
    return newValues;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/utils/transform.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isTransformOriginProp": () => (/* binding */ isTransformOriginProp),
/* harmony export */   "isTransformProp": () => (/* binding */ isTransformProp),
/* harmony export */   "sortTransformProps": () => (/* binding */ sortTransformProps),
/* harmony export */   "transformAxes": () => (/* binding */ transformAxes),
/* harmony export */   "transformProps": () => (/* binding */ transformProps)
/* harmony export */ });
/**
 * A list of all transformable axes. We'll use this list to generated a version
 * of each axes for each transform.
 */
var transformAxes = ["", "X", "Y", "Z"];
/**
 * An ordered array of each transformable value. By default, transform values
 * will be sorted to this order.
 */
var order = ["translate", "scale", "rotate", "skew"];
/**
 * Generate a list of every possible transform key.
 */
var transformProps = ["transformPerspective", "x", "y", "z"];
order.forEach(function (operationKey) {
    return transformAxes.forEach(function (axesKey) {
        return transformProps.push(operationKey + axesKey);
    });
});
/**
 * A function to use with Array.sort to sort transform keys by their default order.
 */
function sortTransformProps(a, b) {
    return transformProps.indexOf(a) - transformProps.indexOf(b);
}
/**
 * A quick lookup for transform props.
 */
var transformPropSet = new Set(transformProps);
function isTransformProp(key) {
    return transformPropSet.has(key);
}
/**
 * A quick lookup for transform origin props
 */
var transformOriginProps = new Set(["originX", "originY", "originZ"]);
function isTransformOriginProp(key) {
    return transformOriginProps.has(key);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/html/visual-element.js":
/*!**************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/html/visual-element.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getComputedStyle": () => (/* binding */ getComputedStyle),
/* harmony export */   "htmlConfig": () => (/* binding */ htmlConfig),
/* harmony export */   "htmlVisualElement": () => (/* binding */ htmlVisualElement)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _dom_projection_measure_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../dom/projection/measure.js */ "./node_modules/framer-motion/dist/es/render/dom/projection/measure.js");
/* harmony import */ var _dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../dom/utils/value-types.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js");
/* harmony import */ var _utils_setters_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/setters.js */ "./node_modules/framer-motion/dist/es/render/utils/setters.js");
/* harmony import */ var _utils_transform_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js");
/* harmony import */ var _dom_utils_is_css_variable_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../dom/utils/is-css-variable.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/is-css-variable.js");
/* harmony import */ var _utils_build_styles_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/build-styles.js */ "./node_modules/framer-motion/dist/es/render/html/utils/build-styles.js");
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../index.js */ "./node_modules/framer-motion/dist/es/render/index.js");
/* harmony import */ var _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/scrape-motion-values.js */ "./node_modules/framer-motion/dist/es/render/html/utils/scrape-motion-values.js");
/* harmony import */ var _dom_utils_parse_dom_variant_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../dom/utils/parse-dom-variant.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/parse-dom-variant.js");











function getComputedStyle(element) {
    return window.getComputedStyle(element);
}
var htmlConfig = {
    treeType: "dom",
    readValueFromInstance: function (domElement, key) {
        if ((0,_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__.isTransformProp)(key)) {
            var defaultType = (0,_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_1__.getDefaultValueType)(key);
            return defaultType ? defaultType.default || 0 : 0;
        }
        else {
            var computedStyle = getComputedStyle(domElement);
            return (((0,_dom_utils_is_css_variable_js__WEBPACK_IMPORTED_MODULE_2__.isCSSVariable)(key)
                ? computedStyle.getPropertyValue(key)
                : computedStyle[key]) || 0);
        }
    },
    sortNodePosition: function (a, b) {
        /**
         * compareDocumentPosition returns a bitmask, by using the bitwise &
         * we're returning true if 2 in that bitmask is set to true. 2 is set
         * to true if b preceeds a.
         */
        return a.compareDocumentPosition(b) & 2 ? 1 : -1;
    },
    getBaseTarget: function (props, key) {
        var _a;
        return (_a = props.style) === null || _a === void 0 ? void 0 : _a[key];
    },
    measureViewportBox: function (element, _a) {
        var transformPagePoint = _a.transformPagePoint;
        return (0,_dom_projection_measure_js__WEBPACK_IMPORTED_MODULE_3__.getBoundingBox)(element, transformPagePoint);
    },
    /**
     * Reset the transform on the current Element. This is called as part
     * of a batched process across the entire layout tree. To remove this write
     * cycle it'd be interesting to see if it's possible to "undo" all the current
     * layout transforms up the tree in the same way this.getBoundingBoxWithoutTransforms
     * works
     */
    resetTransform: function (element, domElement, props) {
        /**
         * When we reset the transform of an element, there's a fair possibility that
         * the element will visually move from underneath the pointer, triggering attached
         * pointerenter/leave events. We temporarily suspend these while measurement takes place.
         */
        element.suspendHoverEvents();
        var transformTemplate = props.transformTemplate;
        domElement.style.transform = transformTemplate
            ? transformTemplate({}, "")
            : "none";
        // Ensure that whatever happens next, we restore our transform on the next frame
        element.scheduleRender();
    },
    restoreTransform: function (instance, mutableState) {
        instance.style.transform = mutableState.style.transform;
    },
    removeValueFromRenderState: function (key, _a) {
        var vars = _a.vars, style = _a.style;
        delete vars[key];
        delete style[key];
    },
    /**
     * Ensure that HTML and Framer-specific value types like `px`->`%` and `Color`
     * can be animated by Motion.
     */
    makeTargetAnimatable: function (element, _a, _b, isMounted) {
        var transformValues = _b.transformValues;
        if (isMounted === void 0) { isMounted = true; }
        var transition = _a.transition, transitionEnd = _a.transitionEnd, target = (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__rest)(_a, ["transition", "transitionEnd"]);
        var origin = (0,_utils_setters_js__WEBPACK_IMPORTED_MODULE_5__.getOrigin)(target, transition || {}, element);
        /**
         * If Framer has provided a function to convert `Color` etc value types, convert them
         */
        if (transformValues) {
            if (transitionEnd)
                transitionEnd = transformValues(transitionEnd);
            if (target)
                target = transformValues(target);
            if (origin)
                origin = transformValues(origin);
        }
        if (isMounted) {
            (0,_utils_setters_js__WEBPACK_IMPORTED_MODULE_5__.checkTargetForNewValues)(element, target, origin);
            var parsed = (0,_dom_utils_parse_dom_variant_js__WEBPACK_IMPORTED_MODULE_6__.parseDomVariant)(element, target, origin, transitionEnd);
            transitionEnd = parsed.transitionEnd;
            target = parsed.target;
        }
        return (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)({ transition: transition,
            transitionEnd: transitionEnd }, target);
    },
    scrapeMotionValuesFromProps: _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_7__.scrapeMotionValuesFromProps,
    build: function (element, renderState, latestValues, projection, layoutState, options, props) {
        if (element.isVisible !== undefined) {
            renderState.style.visibility = element.isVisible
                ? "visible"
                : "hidden";
        }
        (0,_utils_build_styles_js__WEBPACK_IMPORTED_MODULE_8__.buildHTMLStyles)(renderState, latestValues, projection, layoutState, options, props.transformTemplate);
    },
    render: function (element, _a) {
        var style = _a.style, vars = _a.vars;
        // Directly assign style into the Element's style prop. In tests Object.assign is the
        // fastest way to assign styles.
        Object.assign(element.style, style);
        // Loop over any CSS variables and assign those.
        for (var key in vars) {
            element.style.setProperty(key, vars[key]);
        }
    },
};
var htmlVisualElement = (0,_index_js__WEBPACK_IMPORTED_MODULE_9__.visualElement)(htmlConfig);




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/index.js":
/*!************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/index.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "visualElement": () => (/* binding */ visualElement)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _components_AnimateSharedLayout_types_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../components/AnimateSharedLayout/types.js */ "./node_modules/framer-motion/dist/es/components/AnimateSharedLayout/types.js");
/* harmony import */ var _utils_variants_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./utils/variants.js */ "./node_modules/framer-motion/dist/es/render/utils/variants.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/pipe.js");
/* harmony import */ var framesync__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! framesync */ "./node_modules/framesync/dist/es/index.js");
/* harmony import */ var _utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../utils/geometry/index.js */ "./node_modules/framer-motion/dist/es/utils/geometry/index.js");
/* harmony import */ var _utils_each_axis_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../utils/each-axis.js */ "./node_modules/framer-motion/dist/es/utils/each-axis.js");
/* harmony import */ var _utils_geometry_delta_calc_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/geometry/delta-calc.js */ "./node_modules/framer-motion/dist/es/utils/geometry/delta-calc.js");
/* harmony import */ var _value_index_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../value/index.js */ "./node_modules/framer-motion/dist/es/value/index.js");
/* harmony import */ var _utils_animation_state_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./utils/animation-state.js */ "./node_modules/framer-motion/dist/es/render/utils/animation-state.js");
/* harmony import */ var _value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../value/utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");
/* harmony import */ var _utils_state_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/state.js */ "./node_modules/framer-motion/dist/es/render/utils/state.js");
/* harmony import */ var _html_utils_build_transform_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./html/utils/build-transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/build-transform.js");
/* harmony import */ var _utils_geometry_delta_apply_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/geometry/delta-apply.js */ "./node_modules/framer-motion/dist/es/utils/geometry/delta-apply.js");
/* harmony import */ var _utils_lifecycles_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/lifecycles.js */ "./node_modules/framer-motion/dist/es/render/utils/lifecycles.js");
/* harmony import */ var _utils_motion_values_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./utils/motion-values.js */ "./node_modules/framer-motion/dist/es/render/utils/motion-values.js");
/* harmony import */ var _utils_projection_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/projection.js */ "./node_modules/framer-motion/dist/es/render/utils/projection.js");


















var visualElement = function (_a) {
    var _b = _a.treeType, treeType = _b === void 0 ? "" : _b, build = _a.build, getBaseTarget = _a.getBaseTarget, makeTargetAnimatable = _a.makeTargetAnimatable, measureViewportBox = _a.measureViewportBox, renderInstance = _a.render, readValueFromInstance = _a.readValueFromInstance, resetTransform = _a.resetTransform, restoreTransform = _a.restoreTransform, removeValueFromRenderState = _a.removeValueFromRenderState, sortNodePosition = _a.sortNodePosition, scrapeMotionValuesFromProps = _a.scrapeMotionValuesFromProps;
    return function (_a, options) {
        var parent = _a.parent, props = _a.props, presenceId = _a.presenceId, blockInitialAnimation = _a.blockInitialAnimation, visualState = _a.visualState;
        if (options === void 0) { options = {}; }
        var latestValues = visualState.latestValues, renderState = visualState.renderState;
        /**
         * The instance of the render-specific node that will be hydrated by the
         * exposed React ref. So for example, this visual element can host a
         * HTMLElement, plain object, or Three.js object. The functions provided
         * in VisualElementConfig allow us to interface with this instance.
         */
        var instance;
        /**
         * A set of all children of this visual element. We use this to traverse
         * the tree when updating layout projections.
         */
        var children = new Set();
        /**
         * Manages the subscriptions for a visual element's lifecycle, for instance
         * onRender and onViewportBoxUpdate.
         */
        var lifecycles = (0,_utils_lifecycles_js__WEBPACK_IMPORTED_MODULE_1__.createLifecycles)();
        /**
         *
         */
        var projection = (0,_utils_state_js__WEBPACK_IMPORTED_MODULE_2__.createProjectionState)();
        /**
         * This is a reference to the visual state of the "lead" visual element.
         * Usually, this will be this visual element. But if it shares a layoutId
         * with other visual elements, only one of them will be designated lead by
         * AnimateSharedLayout. All the other visual elements will take on the visual
         * appearance of the lead while they crossfade to it.
         */
        var leadProjection = projection;
        var leadLatestValues = latestValues;
        var unsubscribeFromLeadVisualElement;
        /**
         * The latest layout measurements and calculated projections. This
         * is seperate from the target projection data in visualState as
         * many visual elements might point to the same piece of visualState as
         * a target, whereas they might each have different layouts and thus
         * projection calculations needed to project into the same viewport box.
         */
        var layoutState = (0,_utils_state_js__WEBPACK_IMPORTED_MODULE_2__.createLayoutState)();
        /**
         *
         */
        var crossfader;
        /**
         * Keep track of whether the viewport box has been updated since the
         * last time the layout projection was re-calculated.
         */
        var hasViewportBoxUpdated = false;
        /**
         * A map of all motion values attached to this visual element. Motion
         * values are source of truth for any given animated value. A motion
         * value might be provided externally by the component via props.
         */
        var values = new Map();
        /**
         * A map of every subscription that binds the provided or generated
         * motion values onChange listeners to this visual element.
         */
        var valueSubscriptions = new Map();
        /**
         * A reference to the previously-provided motion values as returned
         * from scrapeMotionValuesFromProps. We use the keys in here to determine
         * if any motion values need to be removed after props are updated.
         */
        var prevMotionValues = {};
        /**
         * x/y motion values that track the progress of initiated layout
         * animations.
         *
         * TODO: Target for removal
         */
        var projectionTargetProgress;
        /**
         * When values are removed from all animation props we need to search
         * for a fallback value to animate to. These values are tracked in baseTarget.
         */
        var baseTarget = (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({}, latestValues);
        // Internal methods ========================
        /**
         * On mount, this will be hydrated with a callback to disconnect
         * this visual element from its parent on unmount.
         */
        var removeFromMotionTree;
        var removeFromVariantTree;
        /**
         *
         */
        function isProjecting() {
            return projection.isEnabled && layoutState.isHydrated;
        }
        /**
         *
         */
        function render() {
            if (!instance)
                return;
            if (isProjecting()) {
                /**
                 * Apply the latest user-set transforms to the targetBox to produce the targetBoxFinal.
                 * This is the final box that we will then project into by calculating a transform delta and
                 * applying it to the corrected box.
                 */
                (0,_utils_geometry_delta_apply_js__WEBPACK_IMPORTED_MODULE_4__.applyBoxTransforms)(leadProjection.targetFinal, leadProjection.target, leadLatestValues);
                /**
                 * Update the delta between the corrected box and the final target box, after
                 * user-set transforms are applied to it. This will be used by the renderer to
                 * create a transform style that will reproject the element from its actual layout
                 * into the desired bounding box.
                 */
                (0,_utils_geometry_delta_calc_js__WEBPACK_IMPORTED_MODULE_5__.updateBoxDelta)(layoutState.deltaFinal, layoutState.layoutCorrected, leadProjection.targetFinal, latestValues);
            }
            triggerBuild();
            renderInstance(instance, renderState);
        }
        function triggerBuild() {
            var valuesToRender = latestValues;
            if (crossfader && crossfader.isActive()) {
                var crossfadedValues = crossfader.getCrossfadeState(element);
                if (crossfadedValues)
                    valuesToRender = crossfadedValues;
            }
            build(element, renderState, valuesToRender, leadProjection, layoutState, options, props);
        }
        function update() {
            lifecycles.notifyUpdate(latestValues);
        }
        function updateLayoutProjection() {
            var delta = layoutState.delta, treeScale = layoutState.treeScale;
            var prevTreeScaleX = treeScale.x;
            var prevTreeScaleY = treeScale.x;
            var prevDeltaTransform = layoutState.deltaTransform;
            (0,_utils_projection_js__WEBPACK_IMPORTED_MODULE_6__.updateLayoutDeltas)(layoutState, leadProjection, element.path, latestValues);
            hasViewportBoxUpdated &&
                element.notifyViewportBoxUpdate(leadProjection.target, delta);
            hasViewportBoxUpdated = false;
            var deltaTransform = (0,_html_utils_build_transform_js__WEBPACK_IMPORTED_MODULE_7__.buildLayoutProjectionTransform)(delta, treeScale);
            if (deltaTransform !== prevDeltaTransform ||
                // Also compare calculated treeScale, for values that rely on this only for scale correction
                prevTreeScaleX !== treeScale.x ||
                prevTreeScaleY !== treeScale.y) {
                element.scheduleRender();
            }
            layoutState.deltaTransform = deltaTransform;
        }
        /**
         *
         */
        function bindToMotionValue(key, value) {
            var removeOnChange = value.onChange(function (latestValue) {
                latestValues[key] = latestValue;
                props.onUpdate && framesync__WEBPACK_IMPORTED_MODULE_0__["default"].update(update, false, true);
            });
            var removeOnRenderRequest = value.onRenderRequest(element.scheduleRender);
            valueSubscriptions.set(key, function () {
                removeOnChange();
                removeOnRenderRequest();
            });
        }
        /**
         * Any motion values that are provided to the element when created
         * aren't yet bound to the element, as this would technically be impure.
         * However, we iterate through the motion values and set them to the
         * initial values for this component.
         *
         * TODO: This is impure and we should look at changing this to run on mount.
         * Doing so will break some tests but this isn't neccessarily a breaking change,
         * more a reflection of the test.
         */
        var initialMotionValues = scrapeMotionValuesFromProps(props);
        for (var key in initialMotionValues) {
            var value = initialMotionValues[key];
            if (latestValues[key] !== undefined && (0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_8__.isMotionValue)(value)) {
                value.set(latestValues[key], false);
            }
        }
        /**
         * Determine what role this visual element should take in the variant tree.
         */
        var isControllingVariants = (0,_utils_variants_js__WEBPACK_IMPORTED_MODULE_9__.checkIfControllingVariants)(props);
        var isVariantNode = (0,_utils_variants_js__WEBPACK_IMPORTED_MODULE_9__.checkIfVariantNode)(props);
        var element = (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({ treeType: treeType, 
            /**
             * This is a mirror of the internal instance prop, which keeps
             * VisualElement type-compatible with React's RefObject.
             */
            current: null, 
            /**
             * The depth of this visual element within the visual element tree.
             */
            depth: parent ? parent.depth + 1 : 0, 
            /**
             * An ancestor path back to the root visual element. This is used
             * by layout projection to quickly recurse back up the tree.
             */
            path: parent ? (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__spread)(parent.path, [parent]) : [], 
            /**
             *
             */
            presenceId: presenceId,
            projection: projection, 
            /**
             * If this component is part of the variant tree, it should track
             * any children that are also part of the tree. This is essentially
             * a shadow tree to simplify logic around how to stagger over children.
             */
            variantChildren: isVariantNode ? new Set() : undefined, 
            /**
             * Whether this instance is visible. This can be changed imperatively
             * by AnimateSharedLayout, is analogous to CSS's visibility in that
             * hidden elements should take up layout, and needs enacting by the configured
             * render function.
             */
            isVisible: undefined, 
            /**
             * Normally, if a component is controlled by a parent's variants, it can
             * rely on that ancestor to trigger animations further down the tree.
             * However, if a component is created after its parent is mounted, the parent
             * won't trigger that mount animation so the child needs to.
             *
             * TODO: This might be better replaced with a method isParentMounted
             */
            manuallyAnimateOnMount: Boolean(parent === null || parent === void 0 ? void 0 : parent.isMounted()), 
            /**
             * This can be set by AnimatePresence to force components that mount
             * at the same time as it to mount as if they have initial={false} set.
             */
            blockInitialAnimation: blockInitialAnimation, 
            /**
             * A boolean that can be used to determine whether to respect hover events.
             * For layout measurements we often have to reposition the instance by
             * removing its transform. This can trigger hover events, which is
             * undesired.
             */
            isHoverEventsEnabled: true, 
            /**
             * Determine whether this component has mounted yet. This is mostly used
             * by variant children to determine whether they need to trigger their
             * own animations on mount.
             */
            isMounted: function () { return Boolean(instance); }, mount: function (newInstance) {
                instance = element.current = newInstance;
                element.pointTo(element);
                removeFromMotionTree = parent === null || parent === void 0 ? void 0 : parent.addChild(element);
                if (isVariantNode && parent && !isControllingVariants) {
                    removeFromVariantTree = parent === null || parent === void 0 ? void 0 : parent.addVariantChild(element);
                }
            },
            /**
             *
             */
            unmount: function () {
                framesync__WEBPACK_IMPORTED_MODULE_0__.cancelSync.update(update);
                framesync__WEBPACK_IMPORTED_MODULE_0__.cancelSync.render(render);
                framesync__WEBPACK_IMPORTED_MODULE_0__.cancelSync.preRender(element.updateLayoutProjection);
                valueSubscriptions.forEach(function (remove) { return remove(); });
                element.stopLayoutAnimation();
                removeFromMotionTree === null || removeFromMotionTree === void 0 ? void 0 : removeFromMotionTree();
                removeFromVariantTree === null || removeFromVariantTree === void 0 ? void 0 : removeFromVariantTree();
                unsubscribeFromLeadVisualElement === null || unsubscribeFromLeadVisualElement === void 0 ? void 0 : unsubscribeFromLeadVisualElement();
                lifecycles.clearAllListeners();
            },
            /**
             * Add a child visual element to our set of children.
             */
            addChild: function (child) {
                children.add(child);
                return function () { return children.delete(child); };
            },
            /**
             * Add a child visual element to our set of children.
             */
            addVariantChild: function (child) {
                var _a;
                var closestVariantNode = element.getClosestVariantNode();
                if (closestVariantNode) {
                    (_a = closestVariantNode.variantChildren) === null || _a === void 0 ? void 0 : _a.add(child);
                    return function () { return closestVariantNode.variantChildren.delete(child); };
                }
            },
            sortNodePosition: function (other) {
                /**
                 * If these nodes aren't even of the same type we can't compare their depth.
                 */
                if (!sortNodePosition || treeType !== other.treeType)
                    return 0;
                return sortNodePosition(element.getInstance(), other.getInstance());
            }, 
            /**
             * Returns the closest variant node in the tree starting from
             * this visual element.
             */
            getClosestVariantNode: function () {
                return isVariantNode ? element : parent === null || parent === void 0 ? void 0 : parent.getClosestVariantNode();
            }, 
            /**
             * A method that schedules an update to layout projections throughout
             * the tree. We inherit from the parent so there's only ever one
             * job scheduled on the next frame - that of the root visual element.
             */
            scheduleUpdateLayoutProjection: parent
                ? parent.scheduleUpdateLayoutProjection
                : function () { return framesync__WEBPACK_IMPORTED_MODULE_0__["default"].preRender(element.updateLayoutProjection, false, true); }, 
            /**
             * Expose the latest layoutId prop.
             */
            getLayoutId: function () { return props.layoutId; }, 
            /**
             * Returns the current instance.
             */
            getInstance: function () { return instance; }, 
            /**
             * Get/set the latest static values.
             */
            getStaticValue: function (key) { return latestValues[key]; }, setStaticValue: function (key, value) { return (latestValues[key] = value); }, 
            /**
             * Returns the latest motion value state. Currently only used to take
             * a snapshot of the visual element - perhaps this can return the whole
             * visual state
             */
            getLatestValues: function () { return latestValues; }, 
            /**
             * Set the visiblity of the visual element. If it's changed, schedule
             * a render to reflect these changes.
             */
            setVisibility: function (visibility) {
                if (element.isVisible === visibility)
                    return;
                element.isVisible = visibility;
                element.scheduleRender();
            },
            /**
             * Make a target animatable by Popmotion. For instance, if we're
             * trying to animate width from 100px to 100vw we need to measure 100vw
             * in pixels to determine what we really need to animate to. This is also
             * pluggable to support Framer's custom value types like Color,
             * and CSS variables.
             */
            makeTargetAnimatable: function (target, canMutate) {
                if (canMutate === void 0) { canMutate = true; }
                return makeTargetAnimatable(element, target, props, canMutate);
            },
            /**
             * Temporarily suspend hover events while we remove transforms in order to measure the layout.
             *
             * This seems like an odd bit of scheduling but what we're doing is saying after
             * the next render, wait 10 milliseconds before reenabling hover events. Waiting until
             * the next frame results in missed, valid hover events. But triggering on the postRender
             * frame is too soon to avoid triggering events with layout measurements.
             *
             * Note: If we figure out a way of measuring layout while transforms remain applied, this can be removed.
             */
            suspendHoverEvents: function () {
                element.isHoverEventsEnabled = false;
                framesync__WEBPACK_IMPORTED_MODULE_0__["default"].postRender(function () {
                    return setTimeout(function () { return (element.isHoverEventsEnabled = true); }, 10);
                });
            },
            // Motion values ========================
            /**
             * Add a motion value and bind it to this visual element.
             */
            addValue: function (key, value) {
                // Remove existing value if it exists
                if (element.hasValue(key))
                    element.removeValue(key);
                values.set(key, value);
                latestValues[key] = value.get();
                bindToMotionValue(key, value);
            },
            /**
             * Remove a motion value and unbind any active subscriptions.
             */
            removeValue: function (key) {
                var _a;
                values.delete(key);
                (_a = valueSubscriptions.get(key)) === null || _a === void 0 ? void 0 : _a();
                valueSubscriptions.delete(key);
                delete latestValues[key];
                removeValueFromRenderState(key, renderState);
            }, 
            /**
             * Check whether we have a motion value for this key
             */
            hasValue: function (key) { return values.has(key); }, 
            /**
             * Get a motion value for this key. If called with a default
             * value, we'll create one if none exists.
             */
            getValue: function (key, defaultValue) {
                var value = values.get(key);
                if (value === undefined && defaultValue !== undefined) {
                    value = (0,_value_index_js__WEBPACK_IMPORTED_MODULE_10__.motionValue)(defaultValue);
                    element.addValue(key, value);
                }
                return value;
            }, 
            /**
             * Iterate over our motion values.
             */
            forEachValue: function (callback) { return values.forEach(callback); }, 
            /**
             * If we're trying to animate to a previously unencountered value,
             * we need to check for it in our state and as a last resort read it
             * directly from the instance (which might have performance implications).
             */
            readValue: function (key) { var _a; return (_a = latestValues[key]) !== null && _a !== void 0 ? _a : readValueFromInstance(instance, key, options); }, 
            /**
             * Set the base target to later animate back to. This is currently
             * only hydrated on creation and when we first read a value.
             */
            setBaseTarget: function (key, value) {
                baseTarget[key] = value;
            },
            /**
             * Find the base target for a value thats been removed from all animation
             * props.
             */
            getBaseTarget: function (key) {
                if (getBaseTarget) {
                    var target = getBaseTarget(props, key);
                    if (target !== undefined && !(0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_8__.isMotionValue)(target))
                        return target;
                }
                return baseTarget[key];
            } }, lifecycles), { 
            /**
             * Build the renderer state based on the latest visual state.
             */
            build: function () {
                triggerBuild();
                return renderState;
            },
            /**
             * Schedule a render on the next animation frame.
             */
            scheduleRender: function () {
                framesync__WEBPACK_IMPORTED_MODULE_0__["default"].render(render, false, true);
            }, 
            /**
             * Synchronously fire render. It's prefered that we batch renders but
             * in many circumstances, like layout measurement, we need to run this
             * synchronously. However in those instances other measures should be taken
             * to batch reads/writes.
             */
            syncRender: render, 
            /**
             * Update the provided props. Ensure any newly-added motion values are
             * added to our map, old ones removed, and listeners updated.
             */
            setProps: function (newProps) {
                props = newProps;
                lifecycles.updatePropListeners(newProps);
                prevMotionValues = (0,_utils_motion_values_js__WEBPACK_IMPORTED_MODULE_11__.updateMotionValuesFromProps)(element, scrapeMotionValuesFromProps(props), prevMotionValues);
            }, getProps: function () { return props; }, 
            // Variants ==============================
            /**
             * Returns the variant definition with a given name.
             */
            getVariant: function (name) { var _a; return (_a = props.variants) === null || _a === void 0 ? void 0 : _a[name]; }, 
            /**
             * Returns the defined default transition on this component.
             */
            getDefaultTransition: function () { return props.transition; }, 
            /**
             * Used by child variant nodes to get the closest ancestor variant props.
             */
            getVariantContext: function (startAtParent) {
                if (startAtParent === void 0) { startAtParent = false; }
                if (startAtParent)
                    return parent === null || parent === void 0 ? void 0 : parent.getVariantContext();
                if (!isControllingVariants) {
                    var context_1 = (parent === null || parent === void 0 ? void 0 : parent.getVariantContext()) || {};
                    if (props.initial !== undefined) {
                        context_1.initial = props.initial;
                    }
                    return context_1;
                }
                var context = {};
                for (var i = 0; i < numVariantProps; i++) {
                    var name_1 = variantProps[i];
                    var prop = props[name_1];
                    if ((0,_utils_variants_js__WEBPACK_IMPORTED_MODULE_9__.isVariantLabel)(prop) || prop === false) {
                        context[name_1] = prop;
                    }
                }
                return context;
            },
            // Layout projection ==============================
            /**
             * Enable layout projection for this visual element. Won't actually
             * occur until we also have hydrated layout measurements.
             */
            enableLayoutProjection: function () {
                projection.isEnabled = true;
            },
            /**
             * Lock the projection target, for instance when dragging, so
             * nothing else can try and animate it.
             */
            lockProjectionTarget: function () {
                projection.isTargetLocked = true;
            },
            unlockProjectionTarget: function () {
                element.stopLayoutAnimation();
                projection.isTargetLocked = false;
            },
            /**
             * Record the viewport box as it was before an expected mutation/re-render
             */
            snapshotViewportBox: function () {
                // TODO: Store this snapshot in LayoutState
                element.prevViewportBox = element.measureViewportBox(false);
                /**
                 * Update targetBox to match the prevViewportBox. This is just to ensure
                 * that targetBox is affected by scroll in the same way as the measured box
                 */
                element.rebaseProjectionTarget(false, element.prevViewportBox);
            }, getLayoutState: function () { return layoutState; }, setCrossfader: function (newCrossfader) {
                crossfader = newCrossfader;
            },
            /**
             * Start a layout animation on a given axis.
             * TODO: This could be better.
             */
            startLayoutAnimation: function (axis, transition) {
                var progress = element.getProjectionAnimationProgress()[axis];
                var _a = projection.target[axis], min = _a.min, max = _a.max;
                var length = max - min;
                progress.clearListeners();
                progress.set(min);
                progress.set(min); // Set twice to hard-reset velocity
                progress.onChange(function (v) {
                    return element.setProjectionTargetAxis(axis, v, v + length);
                });
                return element.animateMotionValue(axis, progress, 0, transition);
            },
            /**
             * Stop layout animations.
             */
            stopLayoutAnimation: function () {
                (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_12__.eachAxis)(function (axis) {
                    return element.getProjectionAnimationProgress()[axis].stop();
                });
            },
            /**
             * Measure the current viewport box with or without transforms.
             * Only measures axis-aligned boxes, rotate and skew must be manually
             * removed with a re-render to work.
             */
            measureViewportBox: function (withTransform) {
                if (withTransform === void 0) { withTransform = true; }
                var viewportBox = measureViewportBox(instance, options);
                if (!withTransform)
                    (0,_utils_geometry_delta_apply_js__WEBPACK_IMPORTED_MODULE_4__.removeBoxTransforms)(viewportBox, latestValues);
                return viewportBox;
            },
            /**
             * Update the layoutState by measuring the DOM layout. This
             * should be called after resetting any layout-affecting transforms.
             */
            updateLayoutMeasurement: function () {
                element.notifyBeforeLayoutMeasure(layoutState.layout);
                layoutState.isHydrated = true;
                layoutState.layout = element.measureViewportBox();
                layoutState.layoutCorrected = (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_13__.copyAxisBox)(layoutState.layout);
                element.notifyLayoutMeasure(layoutState.layout, element.prevViewportBox || layoutState.layout);
                framesync__WEBPACK_IMPORTED_MODULE_0__["default"].update(function () { return element.rebaseProjectionTarget(); });
            },
            /**
             * Get the motion values tracking the layout animations on each
             * axis. Lazy init if not already created.
             */
            getProjectionAnimationProgress: function () {
                projectionTargetProgress || (projectionTargetProgress = {
                    x: (0,_value_index_js__WEBPACK_IMPORTED_MODULE_10__.motionValue)(0),
                    y: (0,_value_index_js__WEBPACK_IMPORTED_MODULE_10__.motionValue)(0),
                });
                return projectionTargetProgress;
            },
            /**
             * Update the projection of a single axis. Schedule an update to
             * the tree layout projection.
             */
            setProjectionTargetAxis: function (axis, min, max) {
                var target = projection.target[axis];
                target.min = min;
                target.max = max;
                // Flag that we want to fire the onViewportBoxUpdate event handler
                hasViewportBoxUpdated = true;
                lifecycles.notifySetAxisTarget();
            },
            /**
             * Rebase the projection target on top of the provided viewport box
             * or the measured layout. This ensures that non-animating elements
             * don't fall out of sync differences in measurements vs projections
             * after a page scroll or other relayout.
             */
            rebaseProjectionTarget: function (force, box) {
                if (box === void 0) { box = layoutState.layout; }
                var _a = element.getProjectionAnimationProgress(), x = _a.x, y = _a.y;
                var shouldRebase = !projection.isTargetLocked &&
                    !x.isAnimating() &&
                    !y.isAnimating();
                if (force || shouldRebase) {
                    (0,_utils_each_axis_js__WEBPACK_IMPORTED_MODULE_12__.eachAxis)(function (axis) {
                        var _a = box[axis], min = _a.min, max = _a.max;
                        element.setProjectionTargetAxis(axis, min, max);
                    });
                }
            },
            /**
             * Notify the visual element that its layout is up-to-date.
             * Currently Animate.tsx uses this to check whether a layout animation
             * needs to be performed.
             */
            notifyLayoutReady: function (config) {
                element.notifyLayoutUpdate(layoutState.layout, element.prevViewportBox || layoutState.layout, config);
            }, 
            /**
             * Temporarily reset the transform of the instance.
             */
            resetTransform: function () { return resetTransform(element, instance, props); }, 
            /**
             * Perform the callback after temporarily unapplying the transform
             * upwards through the tree.
             */
            withoutTransform: function (callback) {
                var isEnabled = projection.isEnabled;
                isEnabled && element.resetTransform();
                parent ? parent.withoutTransform(callback) : callback();
                isEnabled && restoreTransform(instance, renderState);
            },
            updateLayoutProjection: function () {
                isProjecting() && updateLayoutProjection();
                children.forEach(fireUpdateLayoutProjection);
            },
            /**
             *
             */
            pointTo: function (newLead) {
                leadProjection = newLead.projection;
                leadLatestValues = newLead.getLatestValues();
                /**
                 * Subscribe to lead component's layout animations
                 */
                unsubscribeFromLeadVisualElement === null || unsubscribeFromLeadVisualElement === void 0 ? void 0 : unsubscribeFromLeadVisualElement();
                unsubscribeFromLeadVisualElement = (0,popmotion__WEBPACK_IMPORTED_MODULE_14__.pipe)(newLead.onSetAxisTarget(element.scheduleUpdateLayoutProjection), newLead.onLayoutAnimationComplete(function () {
                    var _a;
                    if (element.isPresent) {
                        element.presence = _components_AnimateSharedLayout_types_js__WEBPACK_IMPORTED_MODULE_15__.Presence.Present;
                    }
                    else {
                        (_a = element.layoutSafeToRemove) === null || _a === void 0 ? void 0 : _a.call(element);
                    }
                }));
            }, 
            // TODO: Clean this up
            isPresent: true, presence: _components_AnimateSharedLayout_types_js__WEBPACK_IMPORTED_MODULE_15__.Presence.Entering });
        return element;
    };
};
function fireUpdateLayoutProjection(child) {
    child.updateLayoutProjection();
}
var variantProps = (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__spread)(["initial"], _utils_animation_state_js__WEBPACK_IMPORTED_MODULE_16__.variantPriorityOrder);
var numVariantProps = variantProps.length;




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/config-motion.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/config-motion.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "svgMotionConfig": () => (/* binding */ svgMotionConfig)
/* harmony export */ });
/* harmony import */ var _utils_build_attrs_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/build-attrs.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/build-attrs.js");
/* harmony import */ var _utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/create-render-state.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/create-render-state.js");
/* harmony import */ var _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/scrape-motion-values.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/scrape-motion-values.js");
/* harmony import */ var _visual_element_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./visual-element.js */ "./node_modules/framer-motion/dist/es/render/svg/visual-element.js");
/* harmony import */ var _motion_utils_use_visual_state_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../motion/utils/use-visual-state.js */ "./node_modules/framer-motion/dist/es/motion/utils/use-visual-state.js");






var svgMotionConfig = {
    createVisualElement: function (_, options) {
        return (0,_visual_element_js__WEBPACK_IMPORTED_MODULE_0__.svgVisualElement)(options, { enableHardwareAcceleration: false });
    },
    useVisualState: (0,_motion_utils_use_visual_state_js__WEBPACK_IMPORTED_MODULE_1__.makeUseVisualState)({
        scrapeMotionValuesFromProps: _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_2__.scrapeMotionValuesFromProps,
        createRenderState: _utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_3__.createSvgRenderState,
        onMount: function (props, instance, _a) {
            var renderState = _a.renderState, latestValues = _a.latestValues;
            try {
                renderState.dimensions =
                    typeof instance.getBBox ===
                        "function"
                        ? instance.getBBox()
                        : instance.getBoundingClientRect();
            }
            catch (e) {
                // Most likely trying to measure an unrendered element under Firefox
                renderState.dimensions = {
                    x: 0,
                    y: 0,
                    width: 0,
                    height: 0,
                };
            }
            if (isPath(instance)) {
                renderState.totalPathLength = instance.getTotalLength();
            }
            (0,_utils_build_attrs_js__WEBPACK_IMPORTED_MODULE_4__.buildSVGAttrs)(renderState, latestValues, undefined, undefined, { enableHardwareAcceleration: false }, props.transformTemplate);
            (0,_visual_element_js__WEBPACK_IMPORTED_MODULE_0__.renderSVG)(instance, renderState);
        },
    }),
};
function isPath(element) {
    return element.tagName === "path";
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/supported-elements.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/supported-elements.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "lowercaseSVGElements": () => (/* binding */ lowercaseSVGElements)
/* harmony export */ });
/**
 * We keep these listed seperately as we use the lowercase tag names as part
 * of the runtime bundle to detect SVG components
 */
var lowercaseSVGElements = [
    "animate",
    "circle",
    "defs",
    "desc",
    "ellipse",
    "g",
    "image",
    "line",
    "filter",
    "marker",
    "mask",
    "metadata",
    "path",
    "pattern",
    "polygon",
    "polyline",
    "rect",
    "stop",
    "svg",
    "switch",
    "symbol",
    "text",
    "tspan",
    "use",
    "view",
];




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/use-props.js":
/*!********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/use-props.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useSVGProps": () => (/* binding */ useSVGProps)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _html_use_props_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../html/use-props.js */ "./node_modules/framer-motion/dist/es/render/html/use-props.js");
/* harmony import */ var _utils_build_attrs_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/build-attrs.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/build-attrs.js");
/* harmony import */ var _utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/create-render-state.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/create-render-state.js");






function useSVGProps(props, visualState) {
    var visualProps = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () {
        var state = (0,_utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_1__.createSvgRenderState)();
        (0,_utils_build_attrs_js__WEBPACK_IMPORTED_MODULE_2__.buildSVGAttrs)(state, visualState, undefined, undefined, { enableHardwareAcceleration: false }, props.transformTemplate);
        return (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({}, state.attrs), { style: (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({}, state.style) });
    }, [visualState]);
    if (props.style) {
        var rawStyles = {};
        (0,_html_use_props_js__WEBPACK_IMPORTED_MODULE_4__.copyRawValuesOnly)(rawStyles, props.style, props);
        visualProps.style = (0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_3__.__assign)({}, rawStyles), visualProps.style);
    }
    return visualProps;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/utils/build-attrs.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/utils/build-attrs.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "buildSVGAttrs": () => (/* binding */ buildSVGAttrs)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _html_utils_build_styles_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../html/utils/build-styles.js */ "./node_modules/framer-motion/dist/es/render/html/utils/build-styles.js");
/* harmony import */ var _transform_origin_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./transform-origin.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/transform-origin.js");
/* harmony import */ var _path_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./path.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/path.js");





/**
 * Build SVG visual attrbutes, like cx and style.transform
 */
function buildSVGAttrs(state, _a, projection, layoutState, options, transformTemplate) {
    var attrX = _a.attrX, attrY = _a.attrY, originX = _a.originX, originY = _a.originY, pathLength = _a.pathLength, _b = _a.pathSpacing, pathSpacing = _b === void 0 ? 1 : _b, _c = _a.pathOffset, pathOffset = _c === void 0 ? 0 : _c, 
    // This is object creation, which we try to avoid per-frame.
    latest = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__rest)(_a, ["attrX", "attrY", "originX", "originY", "pathLength", "pathSpacing", "pathOffset"]);
    (0,_html_utils_build_styles_js__WEBPACK_IMPORTED_MODULE_1__.buildHTMLStyles)(state, latest, projection, layoutState, options, transformTemplate);
    state.attrs = state.style;
    state.style = {};
    var attrs = state.attrs, style = state.style, dimensions = state.dimensions, totalPathLength = state.totalPathLength;
    /**
     * However, we apply transforms as CSS transforms. So if we detect a transform we take it from attrs
     * and copy it into style.
     */
    if (attrs.transform) {
        if (dimensions)
            style.transform = attrs.transform;
        delete attrs.transform;
    }
    // Parse transformOrigin
    if (dimensions &&
        (originX !== undefined || originY !== undefined || style.transform)) {
        style.transformOrigin = (0,_transform_origin_js__WEBPACK_IMPORTED_MODULE_2__.calcSVGTransformOrigin)(dimensions, originX !== undefined ? originX : 0.5, originY !== undefined ? originY : 0.5);
    }
    // Treat x/y not as shortcuts but as actual attributes
    if (attrX !== undefined)
        attrs.x = attrX;
    if (attrY !== undefined)
        attrs.y = attrY;
    // Build SVG path if one has been measured
    if (totalPathLength !== undefined && pathLength !== undefined) {
        (0,_path_js__WEBPACK_IMPORTED_MODULE_3__.buildSVGPath)(attrs, totalPathLength, pathLength, pathSpacing, pathOffset, false);
    }
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/utils/camel-case-attrs.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/utils/camel-case-attrs.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "camelCaseAttributes": () => (/* binding */ camelCaseAttributes)
/* harmony export */ });
/**
 * A set of attribute names that are always read/written as camel case.
 */
var camelCaseAttributes = new Set([
    "baseFrequency",
    "diffuseConstant",
    "kernelMatrix",
    "kernelUnitLength",
    "keySplines",
    "keyTimes",
    "limitingConeAngle",
    "markerHeight",
    "markerWidth",
    "numOctaves",
    "targetX",
    "targetY",
    "surfaceScale",
    "specularConstant",
    "specularExponent",
    "stdDeviation",
    "tableValues",
    "viewBox",
]);




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/utils/create-render-state.js":
/*!************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/utils/create-render-state.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createSvgRenderState": () => (/* binding */ createSvgRenderState)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _html_utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../html/utils/create-render-state.js */ "./node_modules/framer-motion/dist/es/render/html/utils/create-render-state.js");



var createSvgRenderState = function () { return ((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({}, (0,_html_utils_create_render_state_js__WEBPACK_IMPORTED_MODULE_1__.createHtmlRenderState)()), { attrs: {} })); };




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/utils/path.js":
/*!*********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/utils/path.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "buildSVGPath": () => (/* binding */ buildSVGPath)
/* harmony export */ });
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/numbers/units.js");


// Convert a progress 0-1 to a pixels value based on the provided length
var progressToPixels = function (progress, length) {
    return style_value_types__WEBPACK_IMPORTED_MODULE_0__.px.transform(progress * length);
};
var dashKeys = {
    offset: "stroke-dashoffset",
    array: "stroke-dasharray",
};
var camelKeys = {
    offset: "strokeDashoffset",
    array: "strokeDasharray",
};
/**
 * Build SVG path properties. Uses the path's measured length to convert
 * our custom pathLength, pathSpacing and pathOffset into stroke-dashoffset
 * and stroke-dasharray attributes.
 *
 * This function is mutative to reduce per-frame GC.
 */
function buildSVGPath(attrs, totalLength, length, spacing, offset, useDashCase) {
    if (spacing === void 0) { spacing = 1; }
    if (offset === void 0) { offset = 0; }
    if (useDashCase === void 0) { useDashCase = true; }
    // We use dash case when setting attributes directly to the DOM node and camel case
    // when defining props on a React component.
    var keys = useDashCase ? dashKeys : camelKeys;
    // Build the dash offset
    attrs[keys.offset] = progressToPixels(-offset, totalLength);
    // Build the dash array
    var pathLength = progressToPixels(length, totalLength);
    var pathSpacing = progressToPixels(spacing, totalLength);
    attrs[keys.array] = pathLength + " " + pathSpacing;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/utils/scrape-motion-values.js":
/*!*************************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/utils/scrape-motion-values.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "scrapeMotionValuesFromProps": () => (/* binding */ scrapeMotionValuesFromProps)
/* harmony export */ });
/* harmony import */ var _value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../value/utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");
/* harmony import */ var _html_utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../html/utils/scrape-motion-values.js */ "./node_modules/framer-motion/dist/es/render/html/utils/scrape-motion-values.js");



function scrapeMotionValuesFromProps(props) {
    var newValues = (0,_html_utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_0__.scrapeMotionValuesFromProps)(props);
    for (var key in props) {
        if ((0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.isMotionValue)(props[key])) {
            var targetKey = key === "x" || key === "y" ? "attr" + key.toUpperCase() : key;
            newValues[targetKey] = props[key];
        }
    }
    return newValues;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/utils/transform-origin.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/utils/transform-origin.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "calcSVGTransformOrigin": () => (/* binding */ calcSVGTransformOrigin)
/* harmony export */ });
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/numbers/units.js");


function calcOrigin(origin, offset, size) {
    return typeof origin === "string"
        ? origin
        : style_value_types__WEBPACK_IMPORTED_MODULE_0__.px.transform(offset + size * origin);
}
/**
 * The SVG transform origin defaults are different to CSS and is less intuitive,
 * so we use the measured dimensions of the SVG to reconcile these.
 */
function calcSVGTransformOrigin(dimensions, originX, originY) {
    var pxOriginX = calcOrigin(originX, dimensions.x, dimensions.width);
    var pxOriginY = calcOrigin(originY, dimensions.y, dimensions.height);
    return pxOriginX + " " + pxOriginY;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/svg/visual-element.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/svg/visual-element.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "renderSVG": () => (/* binding */ renderSVG),
/* harmony export */   "svgVisualElement": () => (/* binding */ svgVisualElement)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../dom/utils/value-types.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js");
/* harmony import */ var _html_utils_transform_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../html/utils/transform.js */ "./node_modules/framer-motion/dist/es/render/html/utils/transform.js");
/* harmony import */ var _utils_build_attrs_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/build-attrs.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/build-attrs.js");
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../index.js */ "./node_modules/framer-motion/dist/es/render/index.js");
/* harmony import */ var _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/scrape-motion-values.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/scrape-motion-values.js");
/* harmony import */ var _html_visual_element_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../html/visual-element.js */ "./node_modules/framer-motion/dist/es/render/html/visual-element.js");
/* harmony import */ var _dom_utils_camel_to_dash_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../dom/utils/camel-to-dash.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/camel-to-dash.js");
/* harmony import */ var _utils_camel_case_attrs_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/camel-case-attrs.js */ "./node_modules/framer-motion/dist/es/render/svg/utils/camel-case-attrs.js");










function renderSVG(element, renderState) {
    _html_visual_element_js__WEBPACK_IMPORTED_MODULE_0__.htmlConfig.render(element, renderState);
    for (var key in renderState.attrs) {
        element.setAttribute(!_utils_camel_case_attrs_js__WEBPACK_IMPORTED_MODULE_1__.camelCaseAttributes.has(key) ? (0,_dom_utils_camel_to_dash_js__WEBPACK_IMPORTED_MODULE_2__.camelToDash)(key) : key, renderState.attrs[key]);
    }
}
var svgVisualElement = (0,_index_js__WEBPACK_IMPORTED_MODULE_3__.visualElement)((0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)({}, _html_visual_element_js__WEBPACK_IMPORTED_MODULE_0__.htmlConfig), { getBaseTarget: function (props, key) {
        return props[key];
    },
    readValueFromInstance: function (domElement, key) {
        var _a;
        if ((0,_html_utils_transform_js__WEBPACK_IMPORTED_MODULE_5__.isTransformProp)(key)) {
            return ((_a = (0,_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_6__.getDefaultValueType)(key)) === null || _a === void 0 ? void 0 : _a.default) || 0;
        }
        key = !_utils_camel_case_attrs_js__WEBPACK_IMPORTED_MODULE_1__.camelCaseAttributes.has(key) ? (0,_dom_utils_camel_to_dash_js__WEBPACK_IMPORTED_MODULE_2__.camelToDash)(key) : key;
        return domElement.getAttribute(key);
    },
    scrapeMotionValuesFromProps: _utils_scrape_motion_values_js__WEBPACK_IMPORTED_MODULE_7__.scrapeMotionValuesFromProps,
    build: function (_element, renderState, latestValues, projection, layoutState, options, props) {
        (0,_utils_build_attrs_js__WEBPACK_IMPORTED_MODULE_8__.buildSVGAttrs)(renderState, latestValues, projection, layoutState, options, props.transformTemplate);
    }, render: renderSVG }));




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/animation-state.js":
/*!****************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/animation-state.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createAnimationState": () => (/* binding */ createAnimationState),
/* harmony export */   "variantPriorityOrder": () => (/* binding */ variantPriorityOrder),
/* harmony export */   "variantsHaveChanged": () => (/* binding */ variantsHaveChanged)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _variants_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./variants.js */ "./node_modules/framer-motion/dist/es/render/utils/variants.js");
/* harmony import */ var _animation_utils_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../animation/utils/is-keyframes-target.js */ "./node_modules/framer-motion/dist/es/animation/utils/is-keyframes-target.js");
/* harmony import */ var _types_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./types.js */ "./node_modules/framer-motion/dist/es/render/utils/types.js");
/* harmony import */ var _animation_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./animation.js */ "./node_modules/framer-motion/dist/es/render/utils/animation.js");
/* harmony import */ var _animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../animation/animation-controls.js */ "./node_modules/framer-motion/dist/es/animation/animation-controls.js");
/* harmony import */ var _utils_shallow_compare_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../utils/shallow-compare.js */ "./node_modules/framer-motion/dist/es/utils/shallow-compare.js");








var variantPriorityOrder = [
    _types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Animate,
    _types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Hover,
    _types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Tap,
    _types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Drag,
    _types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Focus,
    _types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Exit,
];
var reversePriorityOrder = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__spread)(variantPriorityOrder).reverse();
var numAnimationTypes = variantPriorityOrder.length;
function animateList(visualElement) {
    return function (animations) {
        return Promise.all(animations.map(function (_a) {
            var animation = _a.animation, options = _a.options;
            return (0,_animation_js__WEBPACK_IMPORTED_MODULE_2__.animateVisualElement)(visualElement, animation, options);
        }));
    };
}
function createAnimationState(visualElement) {
    var animate = animateList(visualElement);
    var state = createState();
    var allAnimatedKeys = {};
    var isInitialRender = true;
    /**
     * This function will be used to reduce the animation definitions for
     * each active animation type into an object of resolved values for it.
     */
    var buildResolvedTypeValues = function (acc, definition) {
        var resolved = (0,_variants_js__WEBPACK_IMPORTED_MODULE_3__.resolveVariant)(visualElement, definition);
        if (resolved) {
            var transition = resolved.transition, transitionEnd = resolved.transitionEnd, target = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(resolved, ["transition", "transitionEnd"]);
            acc = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, acc), target), transitionEnd);
        }
        return acc;
    };
    function isAnimated(key) {
        return allAnimatedKeys[key] !== undefined;
    }
    /**
     * This just allows us to inject mocked animation functions
     * @internal
     */
    function setAnimateFunction(makeAnimator) {
        animate = makeAnimator(visualElement);
    }
    /**
     * When we receive new props, we need to:
     * 1. Create a list of protected keys for each type. This is a directory of
     *    value keys that are currently being "handled" by types of a higher priority
     *    so that whenever an animation is played of a given type, these values are
     *    protected from being animated.
     * 2. Determine if an animation type needs animating.
     * 3. Determine if any values have been removed from a type and figure out
     *    what to animate those to.
     */
    function animateChanges(options, changedActiveType) {
        var props = visualElement.getProps();
        var context = visualElement.getVariantContext(true) || {};
        /**
         * A list of animations that we'll build into as we iterate through the animation
         * types. This will get executed at the end of the function.
         */
        var animations = [];
        /**
         * Keep track of which values have been removed. Then, as we hit lower priority
         * animation types, we can check if they contain removed values and animate to that.
         */
        var removedKeys = new Set();
        /**
         * A dictionary of all encountered keys. This is an object to let us build into and
         * copy it without iteration. Each time we hit an animation type we set its protected
         * keys - the keys its not allowed to animate - to the latest version of this object.
         */
        var encounteredKeys = {};
        /**
         * If a variant has been removed at a given index, and this component is controlling
         * variant animations, we want to ensure lower-priority variants are forced to animate.
         */
        var removedVariantIndex = Infinity;
        var _loop_1 = function (i) {
            var type = reversePriorityOrder[i];
            var typeState = state[type];
            var prop = (_a = props[type]) !== null && _a !== void 0 ? _a : context[type];
            var propIsVariant = (0,_variants_js__WEBPACK_IMPORTED_MODULE_3__.isVariantLabel)(prop);
            /**
             * If this type has *just* changed isActive status, set activeDelta
             * to that status. Otherwise set to null.
             */
            var activeDelta = type === changedActiveType ? typeState.isActive : null;
            if (activeDelta === false)
                removedVariantIndex = i;
            /**
             * If this prop is an inherited variant, rather than been set directly on the
             * component itself, we want to make sure we allow the parent to trigger animations.
             *
             * TODO: Can probably change this to a !isControllingVariants check
             */
            var isInherited = prop === context[type] && prop !== props[type] && propIsVariant;
            /**
             *
             */
            if (isInherited &&
                isInitialRender &&
                visualElement.manuallyAnimateOnMount) {
                isInherited = false;
            }
            /**
             * Set all encountered keys so far as the protected keys for this type. This will
             * be any key that has been animated or otherwise handled by active, higher-priortiy types.
             */
            typeState.protectedKeys = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, encounteredKeys);
            // Check if we can skip analysing this prop early
            if (
            // If it isn't active and hasn't *just* been set as inactive
            (!typeState.isActive && activeDelta === null) ||
                // If we didn't and don't have any defined prop for this animation type
                (!prop && !typeState.prevProp) ||
                // Or if the prop doesn't define an animation
                (0,_animation_animation_controls_js__WEBPACK_IMPORTED_MODULE_4__.isAnimationControls)(prop) ||
                typeof prop === "boolean") {
                return "continue";
            }
            /**
             * As we go look through the values defined on this type, if we detect
             * a changed value or a value that was removed in a higher priority, we set
             * this to true and add this prop to the animation list.
             */
            var shouldAnimateType = variantsHaveChanged(typeState.prevProp, prop) ||
                // If we're making this variant active, we want to always make it active
                (type === changedActiveType &&
                    typeState.isActive &&
                    !isInherited &&
                    propIsVariant) ||
                // If we removed a higher-priority variant (i is in reverse order)
                (i > removedVariantIndex && propIsVariant);
            /**
             * As animations can be set as variant lists, variants or target objects, we
             * coerce everything to an array if it isn't one already
             */
            var definitionList = Array.isArray(prop) ? prop : [prop];
            /**
             * Build an object of all the resolved values. We'll use this in the subsequent
             * animateChanges calls to determine whether a value has changed.
             */
            var resolvedValues = definitionList.reduce(buildResolvedTypeValues, {});
            if (activeDelta === false)
                resolvedValues = {};
            /**
             * Now we need to loop through all the keys in the prev prop and this prop,
             * and decide:
             * 1. If the value has changed, and needs animating
             * 2. If it has been removed, and needs adding to the removedKeys set
             * 3. If it has been removed in a higher priority type and needs animating
             * 4. If it hasn't been removed in a higher priority but hasn't changed, and
             *    needs adding to the type's protectedKeys list.
             */
            var _a = typeState.prevResolvedValues, prevResolvedValues = _a === void 0 ? {} : _a;
            var allKeys = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, prevResolvedValues), resolvedValues);
            var markToAnimate = function (key) {
                shouldAnimateType = true;
                removedKeys.delete(key);
                typeState.needsAnimating[key] = true;
            };
            for (var key in allKeys) {
                var next = resolvedValues[key];
                var prev = prevResolvedValues[key];
                // If we've already handled this we can just skip ahead
                if (encounteredKeys.hasOwnProperty(key))
                    continue;
                /**
                 * If the value has changed, we probably want to animate it.
                 */
                if (next !== prev) {
                    /**
                     * If both values are keyframes, we need to shallow compare them to
                     * detect whether any value has changed. If it has, we animate it.
                     */
                    if ((0,_animation_utils_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_5__.isKeyframesTarget)(next) && (0,_animation_utils_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_5__.isKeyframesTarget)(prev)) {
                        if (!(0,_utils_shallow_compare_js__WEBPACK_IMPORTED_MODULE_6__.shallowCompare)(next, prev)) {
                            markToAnimate(key);
                        }
                        else {
                            /**
                             * If it hasn't changed, we want to ensure it doesn't animate by
                             * adding it to the list of protected keys.
                             */
                            typeState.protectedKeys[key] = true;
                        }
                    }
                    else if (next !== undefined) {
                        // If next is defined and doesn't equal prev, it needs animating
                        markToAnimate(key);
                    }
                    else {
                        // If it's undefined, it's been removed.
                        removedKeys.add(key);
                    }
                }
                else if (next !== undefined && removedKeys.has(key)) {
                    /**
                     * If next hasn't changed and it isn't undefined, we want to check if it's
                     * been removed by a higher priority
                     */
                    markToAnimate(key);
                }
                else {
                    /**
                     * If it hasn't changed, we add it to the list of protected values
                     * to ensure it doesn't get animated.
                     */
                    typeState.protectedKeys[key] = true;
                }
            }
            /**
             * Update the typeState so next time animateChanges is called we can compare the
             * latest prop and resolvedValues to these.
             */
            typeState.prevProp = prop;
            typeState.prevResolvedValues = resolvedValues;
            /**
             *
             */
            if (typeState.isActive) {
                encounteredKeys = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, encounteredKeys), resolvedValues);
            }
            if (isInitialRender && visualElement.blockInitialAnimation) {
                shouldAnimateType = false;
            }
            /**
             * If this is an inherited prop we want to hard-block animations
             * TODO: Test as this should probably still handle animations triggered
             * by removed values?
             */
            if (shouldAnimateType && !isInherited) {
                animations.push.apply(animations, (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__spread)(definitionList.map(function (animation) { return ({
                    animation: animation,
                    options: (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({ type: type }, options),
                }); })));
            }
        };
        /**
         * Iterate through all animation types in reverse priority order. For each, we want to
         * detect which values it's handling and whether or not they've changed (and therefore
         * need to be animated). If any values have been removed, we want to detect those in
         * lower priority props and flag for animation.
         */
        for (var i = 0; i < numAnimationTypes; i++) {
            _loop_1(i);
        }
        allAnimatedKeys = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, encounteredKeys);
        /**
         * If there are some removed value that haven't been dealt with,
         * we need to create a new animation that falls back either to the value
         * defined in the style prop, or the last read value.
         */
        if (removedKeys.size) {
            var fallbackAnimation_1 = {};
            removedKeys.forEach(function (key) {
                var fallbackTarget = visualElement.getBaseTarget(key);
                if (fallbackTarget !== undefined) {
                    fallbackAnimation_1[key] = fallbackTarget;
                }
            });
            animations.push({ animation: fallbackAnimation_1 });
        }
        var shouldAnimate = Boolean(animations.length);
        if (isInitialRender &&
            props.initial === false &&
            !visualElement.manuallyAnimateOnMount) {
            shouldAnimate = false;
        }
        isInitialRender = false;
        return shouldAnimate ? animate(animations) : Promise.resolve();
    }
    /**
     * Change whether a certain animation type is active.
     */
    function setActive(type, isActive, options) {
        var _a;
        // If the active state hasn't changed, we can safely do nothing here
        if (state[type].isActive === isActive)
            return Promise.resolve();
        // Propagate active change to children
        (_a = visualElement.variantChildren) === null || _a === void 0 ? void 0 : _a.forEach(function (child) { var _a; return (_a = child.animationState) === null || _a === void 0 ? void 0 : _a.setActive(type, isActive); });
        state[type].isActive = isActive;
        return animateChanges(options, type);
    }
    return {
        isAnimated: isAnimated,
        animateChanges: animateChanges,
        setActive: setActive,
        setAnimateFunction: setAnimateFunction,
        getState: function () { return state; },
    };
}
function variantsHaveChanged(prev, next) {
    if (typeof next === "string") {
        return next !== prev;
    }
    else if ((0,_variants_js__WEBPACK_IMPORTED_MODULE_3__.isVariantLabels)(next)) {
        return !(0,_utils_shallow_compare_js__WEBPACK_IMPORTED_MODULE_6__.shallowCompare)(next, prev);
    }
    return false;
}
function createTypeState(isActive) {
    if (isActive === void 0) { isActive = false; }
    return {
        isActive: isActive,
        protectedKeys: {},
        needsAnimating: {},
        prevResolvedValues: {},
    };
}
function createState() {
    var _a;
    return _a = {},
        _a[_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Animate] = createTypeState(true),
        _a[_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Hover] = createTypeState(),
        _a[_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Tap] = createTypeState(),
        _a[_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Drag] = createTypeState(),
        _a[_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Focus] = createTypeState(),
        _a[_types_js__WEBPACK_IMPORTED_MODULE_0__.AnimationType.Exit] = createTypeState(),
        _a;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/animation.js":
/*!**********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/animation.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "animateVisualElement": () => (/* binding */ animateVisualElement),
/* harmony export */   "sortByTreeOrder": () => (/* binding */ sortByTreeOrder),
/* harmony export */   "stopAnimation": () => (/* binding */ stopAnimation)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _variants_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./variants.js */ "./node_modules/framer-motion/dist/es/render/utils/variants.js");
/* harmony import */ var _animation_utils_transitions_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../animation/utils/transitions.js */ "./node_modules/framer-motion/dist/es/animation/utils/transitions.js");
/* harmony import */ var _setters_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./setters.js */ "./node_modules/framer-motion/dist/es/render/utils/setters.js");





/**
 * @internal
 */
function animateVisualElement(visualElement, definition, options) {
    if (options === void 0) { options = {}; }
    visualElement.notifyAnimationStart();
    var animation;
    if (Array.isArray(definition)) {
        var animations = definition.map(function (variant) {
            return animateVariant(visualElement, variant, options);
        });
        animation = Promise.all(animations);
    }
    else if (typeof definition === "string") {
        animation = animateVariant(visualElement, definition, options);
    }
    else {
        var resolvedDefinition = typeof definition === "function"
            ? (0,_variants_js__WEBPACK_IMPORTED_MODULE_0__.resolveVariant)(visualElement, definition, options.custom)
            : definition;
        animation = animateTarget(visualElement, resolvedDefinition, options);
    }
    return animation.then(function () {
        return visualElement.notifyAnimationComplete(definition);
    });
}
function animateVariant(visualElement, variant, options) {
    var _a;
    if (options === void 0) { options = {}; }
    var resolved = (0,_variants_js__WEBPACK_IMPORTED_MODULE_0__.resolveVariant)(visualElement, variant, options.custom);
    var _b = (resolved || {}).transition, transition = _b === void 0 ? visualElement.getDefaultTransition() || {} : _b;
    if (options.transitionOverride) {
        transition = options.transitionOverride;
    }
    /**
     * If we have a variant, create a callback that runs it as an animation.
     * Otherwise, we resolve a Promise immediately for a composable no-op.
     */
    var getAnimation = resolved
        ? function () { return animateTarget(visualElement, resolved, options); }
        : function () { return Promise.resolve(); };
    /**
     * If we have children, create a callback that runs all their animations.
     * Otherwise, we resolve a Promise immediately for a composable no-op.
     */
    var getChildAnimations = ((_a = visualElement.variantChildren) === null || _a === void 0 ? void 0 : _a.size) ? function (forwardDelay) {
        if (forwardDelay === void 0) { forwardDelay = 0; }
        var _a = transition.delayChildren, delayChildren = _a === void 0 ? 0 : _a, staggerChildren = transition.staggerChildren, staggerDirection = transition.staggerDirection;
        return animateChildren(visualElement, variant, delayChildren + forwardDelay, staggerChildren, staggerDirection, options);
    }
        : function () { return Promise.resolve(); };
    /**
     * If the transition explicitly defines a "when" option, we need to resolve either
     * this animation or all children animations before playing the other.
     */
    var when = transition.when;
    if (when) {
        var _c = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)(when === "beforeChildren"
            ? [getAnimation, getChildAnimations]
            : [getChildAnimations, getAnimation], 2), first = _c[0], last = _c[1];
        return first().then(last);
    }
    else {
        return Promise.all([getAnimation(), getChildAnimations(options.delay)]);
    }
}
/**
 * @internal
 */
function animateTarget(visualElement, definition, _a) {
    var _b;
    var _c = _a === void 0 ? {} : _a, _d = _c.delay, delay = _d === void 0 ? 0 : _d, transitionOverride = _c.transitionOverride, type = _c.type;
    var _e = visualElement.makeTargetAnimatable(definition), _f = _e.transition, transition = _f === void 0 ? visualElement.getDefaultTransition() : _f, transitionEnd = _e.transitionEnd, target = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_e, ["transition", "transitionEnd"]);
    if (transitionOverride)
        transition = transitionOverride;
    var animations = [];
    var animationTypeState = type && ((_b = visualElement.animationState) === null || _b === void 0 ? void 0 : _b.getState()[type]);
    for (var key in target) {
        var value = visualElement.getValue(key);
        var valueTarget = target[key];
        if (!value ||
            valueTarget === undefined ||
            (animationTypeState &&
                shouldBlockAnimation(animationTypeState, key))) {
            continue;
        }
        var animation = (0,_animation_utils_transitions_js__WEBPACK_IMPORTED_MODULE_2__.startAnimation)(key, value, valueTarget, (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({ delay: delay }, transition));
        animations.push(animation);
    }
    return Promise.all(animations).then(function () {
        transitionEnd && (0,_setters_js__WEBPACK_IMPORTED_MODULE_3__.setTarget)(visualElement, transitionEnd);
    });
}
function animateChildren(visualElement, variant, delayChildren, staggerChildren, staggerDirection, options) {
    if (delayChildren === void 0) { delayChildren = 0; }
    if (staggerChildren === void 0) { staggerChildren = 0; }
    if (staggerDirection === void 0) { staggerDirection = 1; }
    var animations = [];
    var maxStaggerDuration = (visualElement.variantChildren.size - 1) * staggerChildren;
    var generateStaggerDuration = staggerDirection === 1
        ? function (i) {
            if (i === void 0) { i = 0; }
            return i * staggerChildren;
        }
        : function (i) {
            if (i === void 0) { i = 0; }
            return maxStaggerDuration - i * staggerChildren;
        };
    Array.from(visualElement.variantChildren)
        .sort(sortByTreeOrder)
        .forEach(function (child, i) {
        animations.push(animateVariant(child, variant, (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, options), { delay: delayChildren + generateStaggerDuration(i) })).then(function () { return child.notifyAnimationComplete(variant); }));
    });
    return Promise.all(animations);
}
function stopAnimation(visualElement) {
    visualElement.forEachValue(function (value) { return value.stop(); });
}
function sortByTreeOrder(a, b) {
    return a.sortNodePosition(b);
}
/**
 * Decide whether we should block this animation. Previously, we achieved this
 * just by checking whether the key was listed in protectedKeys, but this
 * posed problems if an animation was triggered by afterChildren and protectedKeys
 * had been set to true in the meantime.
 */
function shouldBlockAnimation(_a, key) {
    var protectedKeys = _a.protectedKeys, needsAnimating = _a.needsAnimating;
    var shouldBlock = protectedKeys.hasOwnProperty(key) && needsAnimating[key] !== true;
    needsAnimating[key] = false;
    return shouldBlock;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/lifecycles.js":
/*!***********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/lifecycles.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createLifecycles": () => (/* binding */ createLifecycles)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_subscription_manager_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/subscription-manager.js */ "./node_modules/framer-motion/dist/es/utils/subscription-manager.js");



var names = [
    "LayoutMeasure",
    "BeforeLayoutMeasure",
    "LayoutUpdate",
    "ViewportBoxUpdate",
    "Update",
    "Render",
    "AnimationComplete",
    "LayoutAnimationComplete",
    "AnimationStart",
    "SetAxisTarget",
];
function createLifecycles() {
    var managers = names.map(function () { return new _utils_subscription_manager_js__WEBPACK_IMPORTED_MODULE_0__.SubscriptionManager(); });
    var propSubscriptions = {};
    var lifecycles = {
        clearAllListeners: function () { return managers.forEach(function (manager) { return manager.clear(); }); },
        updatePropListeners: function (props) {
            return names.forEach(function (name) {
                var _a;
                (_a = propSubscriptions[name]) === null || _a === void 0 ? void 0 : _a.call(propSubscriptions);
                var on = "on" + name;
                var propListener = props[on];
                if (propListener) {
                    propSubscriptions[name] = lifecycles[on](propListener);
                }
            });
        },
    };
    managers.forEach(function (manager, i) {
        lifecycles["on" + names[i]] = function (handler) { return manager.add(handler); };
        lifecycles["notify" + names[i]] = function () {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            return manager.notify.apply(manager, (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__spread)(args));
        };
    });
    return lifecycles;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/motion-values.js":
/*!**************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/motion-values.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "updateMotionValuesFromProps": () => (/* binding */ updateMotionValuesFromProps)
/* harmony export */ });
/* harmony import */ var _value_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../value/index.js */ "./node_modules/framer-motion/dist/es/value/index.js");
/* harmony import */ var _value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../value/utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");



function updateMotionValuesFromProps(element, next, prev) {
    var _a;
    for (var key in next) {
        var nextValue = next[key];
        var prevValue = prev[key];
        if ((0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_0__.isMotionValue)(nextValue)) {
            /**
             * If this is a motion value found in props or style, we want to add it
             * to our visual element's motion value map.
             */
            element.addValue(key, nextValue);
        }
        else if ((0,_value_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_0__.isMotionValue)(prevValue)) {
            /**
             * If we're swapping to a new motion value, create a new motion value
             * from that
             */
            element.addValue(key, (0,_value_index_js__WEBPACK_IMPORTED_MODULE_1__.motionValue)(nextValue));
        }
        else if (prevValue !== nextValue) {
            /**
             * If this is a flat value that has changed, update the motion value
             * or create one if it doesn't exist. We only want to do this if we're
             * not handling the value with our animation state.
             */
            if (element.hasValue(key)) {
                var existingValue = element.getValue(key);
                // TODO: Only update values that aren't being animated or even looked at
                !existingValue.hasAnimated && existingValue.set(nextValue);
            }
            else {
                element.addValue(key, (0,_value_index_js__WEBPACK_IMPORTED_MODULE_1__.motionValue)((_a = element.getStaticValue(key)) !== null && _a !== void 0 ? _a : nextValue));
            }
        }
    }
    // Handle removed values
    for (var key in prev) {
        if (next[key] === undefined)
            element.removeValue(key);
    }
    return next;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/projection.js":
/*!***********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/projection.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "updateLayoutDeltas": () => (/* binding */ updateLayoutDeltas)
/* harmony export */ });
/* harmony import */ var _utils_geometry_delta_calc_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/geometry/delta-calc.js */ "./node_modules/framer-motion/dist/es/utils/geometry/delta-calc.js");
/* harmony import */ var _utils_geometry_delta_apply_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/geometry/delta-apply.js */ "./node_modules/framer-motion/dist/es/utils/geometry/delta-apply.js");



function updateLayoutDeltas(_a, _b, treePath, transformOrigin) {
    var delta = _a.delta, layout = _a.layout, layoutCorrected = _a.layoutCorrected, treeScale = _a.treeScale;
    var target = _b.target;
    /**
     * Reset the corrected box with the latest values from box, as we're then going
     * to perform mutative operations on it.
     */
    (0,_utils_geometry_delta_apply_js__WEBPACK_IMPORTED_MODULE_0__.resetBox)(layoutCorrected, layout);
    /**
     * Apply all the parent deltas to this box to produce the corrected box. This
     * is the layout box, as it will appear on screen as a result of the transforms of its parents.
     */
    (0,_utils_geometry_delta_apply_js__WEBPACK_IMPORTED_MODULE_0__.applyTreeDeltas)(layoutCorrected, treeScale, treePath);
    /**
     * Update the delta between the corrected box and the target box before user-set transforms were applied.
     * This will allow us to calculate the corrected borderRadius and boxShadow to compensate
     * for our layout reprojection, but still allow them to be scaled correctly by the user.
     * It might be that to simplify this we may want to accept that user-set scale1 is also corrected
     * and we wouldn't have to keep and calc both deltas, OR we could support a user setting
     * to allow people to choose whether these styles are corrected based on just the
     * layout reprojection or the final bounding box.
     */
    (0,_utils_geometry_delta_calc_js__WEBPACK_IMPORTED_MODULE_1__.updateBoxDelta)(delta, layoutCorrected, target, transformOrigin);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/setters.js":
/*!********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/setters.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "checkTargetForNewValues": () => (/* binding */ checkTargetForNewValues),
/* harmony export */   "getOrigin": () => (/* binding */ getOrigin),
/* harmony export */   "getOriginFromTransition": () => (/* binding */ getOriginFromTransition),
/* harmony export */   "setTarget": () => (/* binding */ setTarget),
/* harmony export */   "setValues": () => (/* binding */ setValues)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _variants_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./variants.js */ "./node_modules/framer-motion/dist/es/render/utils/variants.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/complex/index.js");
/* harmony import */ var _dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../dom/utils/value-types.js */ "./node_modules/framer-motion/dist/es/render/dom/utils/value-types.js");
/* harmony import */ var _utils_is_numerical_string_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/is-numerical-string.js */ "./node_modules/framer-motion/dist/es/utils/is-numerical-string.js");
/* harmony import */ var _utils_resolve_value_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/resolve-value.js */ "./node_modules/framer-motion/dist/es/utils/resolve-value.js");
/* harmony import */ var _value_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../value/index.js */ "./node_modules/framer-motion/dist/es/value/index.js");








/**
 * Set VisualElement's MotionValue, creating a new MotionValue for it if
 * it doesn't exist.
 */
function setMotionValue(visualElement, key, value) {
    if (visualElement.hasValue(key)) {
        visualElement.getValue(key).set(value);
    }
    else {
        visualElement.addValue(key, (0,_value_index_js__WEBPACK_IMPORTED_MODULE_0__.motionValue)(value));
    }
}
function setTarget(visualElement, definition) {
    var resolved = (0,_variants_js__WEBPACK_IMPORTED_MODULE_1__.resolveVariant)(visualElement, definition);
    var _a = resolved
        ? visualElement.makeTargetAnimatable(resolved, false)
        : {}, _b = _a.transitionEnd, transitionEnd = _b === void 0 ? {} : _b, _c = _a.transition, target = (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__rest)(_a, ["transitionEnd", "transition"]);
    target = (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)({}, target), transitionEnd);
    for (var key in target) {
        var value = (0,_utils_resolve_value_js__WEBPACK_IMPORTED_MODULE_3__.resolveFinalValueInKeyframes)(target[key]);
        setMotionValue(visualElement, key, value);
    }
}
function setVariants(visualElement, variantLabels) {
    var reversedLabels = (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__spread)(variantLabels).reverse();
    reversedLabels.forEach(function (key) {
        var _a;
        var variant = visualElement.getVariant(key);
        variant && setTarget(visualElement, variant);
        (_a = visualElement.variantChildren) === null || _a === void 0 ? void 0 : _a.forEach(function (child) {
            setVariants(child, variantLabels);
        });
    });
}
function setValues(visualElement, definition) {
    if (Array.isArray(definition)) {
        return setVariants(visualElement, definition);
    }
    else if (typeof definition === "string") {
        return setVariants(visualElement, [definition]);
    }
    else {
        setTarget(visualElement, definition);
    }
}
function checkTargetForNewValues(visualElement, target, origin) {
    var _a, _b, _c;
    var _d;
    var newValueKeys = Object.keys(target).filter(function (key) { return !visualElement.hasValue(key); });
    var numNewValues = newValueKeys.length;
    if (!numNewValues)
        return;
    for (var i = 0; i < numNewValues; i++) {
        var key = newValueKeys[i];
        var targetValue = target[key];
        var value = null;
        /**
         * If the target is a series of keyframes, we can use the first value
         * in the array. If this first value is null, we'll still need to read from the DOM.
         */
        if (Array.isArray(targetValue)) {
            value = targetValue[0];
        }
        /**
         * If the target isn't keyframes, or the first keyframe was null, we need to
         * first check if an origin value was explicitly defined in the transition as "from",
         * if not read the value from the DOM. As an absolute fallback, take the defined target value.
         */
        if (value === null) {
            value = (_b = (_a = origin[key]) !== null && _a !== void 0 ? _a : visualElement.readValue(key)) !== null && _b !== void 0 ? _b : target[key];
        }
        /**
         * If value is still undefined or null, ignore it. Preferably this would throw,
         * but this was causing issues in Framer.
         */
        if (value === undefined || value === null)
            continue;
        if (typeof value === "string" && (0,_utils_is_numerical_string_js__WEBPACK_IMPORTED_MODULE_4__.isNumericalString)(value)) {
            // If this is a number read as a string, ie "0" or "200", convert it to a number
            value = parseFloat(value);
        }
        else if (!(0,_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_5__.findValueType)(value) && style_value_types__WEBPACK_IMPORTED_MODULE_6__.complex.test(targetValue)) {
            value = (0,_dom_utils_value_types_js__WEBPACK_IMPORTED_MODULE_5__.getAnimatableNone)(key, targetValue);
        }
        visualElement.addValue(key, (0,_value_index_js__WEBPACK_IMPORTED_MODULE_0__.motionValue)(value));
        (_c = (_d = origin)[key]) !== null && _c !== void 0 ? _c : (_d[key] = value);
        visualElement.setBaseTarget(key, value);
    }
}
function getOriginFromTransition(key, transition) {
    if (!transition)
        return;
    var valueTransition = transition[key] || transition["default"] || transition;
    return valueTransition.from;
}
function getOrigin(target, transition, visualElement) {
    var _a, _b;
    var origin = {};
    for (var key in target) {
        origin[key] = (_a = getOriginFromTransition(key, transition)) !== null && _a !== void 0 ? _a : (_b = visualElement.getValue(key)) === null || _b === void 0 ? void 0 : _b.get();
    }
    return origin;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/state.js":
/*!******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/state.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createLayoutState": () => (/* binding */ createLayoutState),
/* harmony export */   "createProjectionState": () => (/* binding */ createProjectionState),
/* harmony export */   "zeroLayout": () => (/* binding */ zeroLayout)
/* harmony export */ });
/* harmony import */ var _utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/geometry/index.js */ "./node_modules/framer-motion/dist/es/utils/geometry/index.js");


var createProjectionState = function () { return ({
    isEnabled: false,
    isTargetLocked: false,
    target: (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.axisBox)(),
    targetFinal: (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.axisBox)(),
}); };
function createLayoutState() {
    return {
        isHydrated: false,
        layout: (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.axisBox)(),
        layoutCorrected: (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.axisBox)(),
        treeScale: { x: 1, y: 1 },
        delta: (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.delta)(),
        deltaFinal: (0,_utils_geometry_index_js__WEBPACK_IMPORTED_MODULE_0__.delta)(),
        deltaTransform: "",
    };
}
var zeroLayout = createLayoutState();




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/types.js":
/*!******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/types.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "AnimationType": () => (/* binding */ AnimationType)
/* harmony export */ });
var AnimationType;
(function (AnimationType) {
    AnimationType["Animate"] = "animate";
    AnimationType["Hover"] = "whileHover";
    AnimationType["Tap"] = "whileTap";
    AnimationType["Drag"] = "whileDrag";
    AnimationType["Focus"] = "whileFocus";
    AnimationType["Exit"] = "exit";
})(AnimationType || (AnimationType = {}));




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/render/utils/variants.js":
/*!*********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/render/utils/variants.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "checkIfControllingVariants": () => (/* binding */ checkIfControllingVariants),
/* harmony export */   "checkIfVariantNode": () => (/* binding */ checkIfVariantNode),
/* harmony export */   "isVariantLabel": () => (/* binding */ isVariantLabel),
/* harmony export */   "isVariantLabels": () => (/* binding */ isVariantLabels),
/* harmony export */   "resolveVariant": () => (/* binding */ resolveVariant),
/* harmony export */   "resolveVariantFromProps": () => (/* binding */ resolveVariantFromProps)
/* harmony export */ });
/**
 * Decides if the supplied variable is an array of variant labels
 */
function isVariantLabels(v) {
    return Array.isArray(v);
}
/**
 * Decides if the supplied variable is variant label
 */
function isVariantLabel(v) {
    return typeof v === "string" || isVariantLabels(v);
}
/**
 * Creates an object containing the latest state of every MotionValue on a VisualElement
 */
function getCurrent(visualElement) {
    var current = {};
    visualElement.forEachValue(function (value, key) { return (current[key] = value.get()); });
    return current;
}
/**
 * Creates an object containing the latest velocity of every MotionValue on a VisualElement
 */
function getVelocity(visualElement) {
    var velocity = {};
    visualElement.forEachValue(function (value, key) { return (velocity[key] = value.getVelocity()); });
    return velocity;
}
function resolveVariantFromProps(props, definition, custom, currentValues, currentVelocity) {
    var _a;
    if (currentValues === void 0) { currentValues = {}; }
    if (currentVelocity === void 0) { currentVelocity = {}; }
    if (typeof definition === "string") {
        definition = (_a = props.variants) === null || _a === void 0 ? void 0 : _a[definition];
    }
    return typeof definition === "function"
        ? definition(custom !== null && custom !== void 0 ? custom : props.custom, currentValues, currentVelocity)
        : definition;
}
function resolveVariant(visualElement, definition, custom) {
    var props = visualElement.getProps();
    return resolveVariantFromProps(props, definition, custom !== null && custom !== void 0 ? custom : props.custom, getCurrent(visualElement), getVelocity(visualElement));
}
function checkIfControllingVariants(props) {
    var _a;
    return (typeof ((_a = props.animate) === null || _a === void 0 ? void 0 : _a.start) === "function" ||
        isVariantLabel(props.initial) ||
        isVariantLabel(props.animate) ||
        isVariantLabel(props.whileHover) ||
        isVariantLabel(props.whileDrag) ||
        isVariantLabel(props.whileTap) ||
        isVariantLabel(props.whileFocus) ||
        isVariantLabel(props.exit));
}
function checkIfVariantNode(props) {
    return Boolean(checkIfControllingVariants(props) || props.variants);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/array.js":
/*!***********************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/array.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "addUniqueItem": () => (/* binding */ addUniqueItem),
/* harmony export */   "removeItem": () => (/* binding */ removeItem)
/* harmony export */ });
function addUniqueItem(arr, item) {
    arr.indexOf(item) === -1 && arr.push(item);
}
function removeItem(arr, item) {
    var index = arr.indexOf(item);
    index > -1 && arr.splice(index, 1);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/each-axis.js":
/*!***************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/each-axis.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "eachAxis": () => (/* binding */ eachAxis)
/* harmony export */ });
// Call a handler once for each axis
function eachAxis(handler) {
    return [handler("x"), handler("y")];
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/geometry/delta-apply.js":
/*!**************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/geometry/delta-apply.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "applyAxisDelta": () => (/* binding */ applyAxisDelta),
/* harmony export */   "applyAxisTransforms": () => (/* binding */ applyAxisTransforms),
/* harmony export */   "applyBoxDelta": () => (/* binding */ applyBoxDelta),
/* harmony export */   "applyBoxTransforms": () => (/* binding */ applyBoxTransforms),
/* harmony export */   "applyPointDelta": () => (/* binding */ applyPointDelta),
/* harmony export */   "applyTreeDeltas": () => (/* binding */ applyTreeDeltas),
/* harmony export */   "removeAxisDelta": () => (/* binding */ removeAxisDelta),
/* harmony export */   "removeAxisTransforms": () => (/* binding */ removeAxisTransforms),
/* harmony export */   "removeBoxTransforms": () => (/* binding */ removeBoxTransforms),
/* harmony export */   "removePointDelta": () => (/* binding */ removePointDelta),
/* harmony export */   "resetAxis": () => (/* binding */ resetAxis),
/* harmony export */   "resetBox": () => (/* binding */ resetBox),
/* harmony export */   "scalePoint": () => (/* binding */ scalePoint)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/mix.js");



/**
 * Reset an axis to the provided origin box.
 *
 * This is a mutative operation.
 */
function resetAxis(axis, originAxis) {
    axis.min = originAxis.min;
    axis.max = originAxis.max;
}
/**
 * Reset a box to the provided origin box.
 *
 * This is a mutative operation.
 */
function resetBox(box, originBox) {
    resetAxis(box.x, originBox.x);
    resetAxis(box.y, originBox.y);
}
/**
 * Scales a point based on a factor and an originPoint
 */
function scalePoint(point, scale, originPoint) {
    var distanceFromOrigin = point - originPoint;
    var scaled = scale * distanceFromOrigin;
    return originPoint + scaled;
}
/**
 * Applies a translate/scale delta to a point
 */
function applyPointDelta(point, translate, scale, originPoint, boxScale) {
    if (boxScale !== undefined) {
        point = scalePoint(point, boxScale, originPoint);
    }
    return scalePoint(point, scale, originPoint) + translate;
}
/**
 * Applies a translate/scale delta to an axis
 */
function applyAxisDelta(axis, translate, scale, originPoint, boxScale) {
    if (translate === void 0) { translate = 0; }
    if (scale === void 0) { scale = 1; }
    axis.min = applyPointDelta(axis.min, translate, scale, originPoint, boxScale);
    axis.max = applyPointDelta(axis.max, translate, scale, originPoint, boxScale);
}
/**
 * Applies a translate/scale delta to a box
 */
function applyBoxDelta(box, _a) {
    var x = _a.x, y = _a.y;
    applyAxisDelta(box.x, x.translate, x.scale, x.originPoint);
    applyAxisDelta(box.y, y.translate, y.scale, y.originPoint);
}
/**
 * Apply a transform to an axis from the latest resolved motion values.
 * This function basically acts as a bridge between a flat motion value map
 * and applyAxisDelta
 */
function applyAxisTransforms(final, axis, transforms, _a) {
    var _b = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__read)(_a, 3), key = _b[0], scaleKey = _b[1], originKey = _b[2];
    // Copy the current axis to the final axis before mutation
    final.min = axis.min;
    final.max = axis.max;
    var axisOrigin = transforms[originKey] !== undefined ? transforms[originKey] : 0.5;
    var originPoint = (0,popmotion__WEBPACK_IMPORTED_MODULE_1__.mix)(axis.min, axis.max, axisOrigin);
    // Apply the axis delta to the final axis
    applyAxisDelta(final, transforms[key], transforms[scaleKey], originPoint, transforms.scale);
}
/**
 * The names of the motion values we want to apply as translation, scale and origin.
 */
var xKeys = ["x", "scaleX", "originX"];
var yKeys = ["y", "scaleY", "originY"];
/**
 * Apply a transform to a box from the latest resolved motion values.
 */
function applyBoxTransforms(finalBox, box, transforms) {
    applyAxisTransforms(finalBox.x, box.x, transforms, xKeys);
    applyAxisTransforms(finalBox.y, box.y, transforms, yKeys);
}
/**
 * Remove a delta from a point. This is essentially the steps of applyPointDelta in reverse
 */
function removePointDelta(point, translate, scale, originPoint, boxScale) {
    point -= translate;
    point = scalePoint(point, 1 / scale, originPoint);
    if (boxScale !== undefined) {
        point = scalePoint(point, 1 / boxScale, originPoint);
    }
    return point;
}
/**
 * Remove a delta from an axis. This is essentially the steps of applyAxisDelta in reverse
 */
function removeAxisDelta(axis, translate, scale, origin, boxScale) {
    if (translate === void 0) { translate = 0; }
    if (scale === void 0) { scale = 1; }
    if (origin === void 0) { origin = 0.5; }
    var originPoint = (0,popmotion__WEBPACK_IMPORTED_MODULE_1__.mix)(axis.min, axis.max, origin) - translate;
    axis.min = removePointDelta(axis.min, translate, scale, originPoint, boxScale);
    axis.max = removePointDelta(axis.max, translate, scale, originPoint, boxScale);
}
/**
 * Remove a transforms from an axis. This is essentially the steps of applyAxisTransforms in reverse
 * and acts as a bridge between motion values and removeAxisDelta
 */
function removeAxisTransforms(axis, transforms, _a) {
    var _b = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__read)(_a, 3), key = _b[0], scaleKey = _b[1], originKey = _b[2];
    removeAxisDelta(axis, transforms[key], transforms[scaleKey], transforms[originKey], transforms.scale);
}
/**
 * Remove a transforms from an box. This is essentially the steps of applyAxisBox in reverse
 * and acts as a bridge between motion values and removeAxisDelta
 */
function removeBoxTransforms(box, transforms) {
    removeAxisTransforms(box.x, transforms, xKeys);
    removeAxisTransforms(box.y, transforms, yKeys);
}
/**
 * Apply a tree of deltas to a box. We do this to calculate the effect of all the transforms
 * in a tree upon our box before then calculating how to project it into our desired viewport-relative box
 *
 * This is the final nested loop within updateLayoutDelta for future refactoring
 */
function applyTreeDeltas(box, treeScale, treePath) {
    var treeLength = treePath.length;
    if (!treeLength)
        return;
    // Reset the treeScale
    treeScale.x = treeScale.y = 1;
    for (var i = 0; i < treeLength; i++) {
        var delta = treePath[i].getLayoutState().delta;
        // Incoporate each ancestor's scale into a culmulative treeScale for this component
        treeScale.x *= delta.x.scale;
        treeScale.y *= delta.y.scale;
        // Apply each ancestor's calculated delta into this component's recorded layout box
        applyBoxDelta(box, delta);
    }
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/geometry/delta-calc.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/geometry/delta-calc.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "calcOrigin": () => (/* binding */ calcOrigin),
/* harmony export */   "isNear": () => (/* binding */ isNear),
/* harmony export */   "updateAxisDelta": () => (/* binding */ updateAxisDelta),
/* harmony export */   "updateBoxDelta": () => (/* binding */ updateBoxDelta)
/* harmony export */ });
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/clamp.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/distance.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/progress.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/mix.js");


var clampProgress = function (v) { return (0,popmotion__WEBPACK_IMPORTED_MODULE_0__.clamp)(0, 1, v); };
/**
 * Returns true if the provided value is within maxDistance of the provided target
 */
function isNear(value, target, maxDistance) {
    if (target === void 0) { target = 0; }
    if (maxDistance === void 0) { maxDistance = 0.01; }
    return (0,popmotion__WEBPACK_IMPORTED_MODULE_1__.distance)(value, target) < maxDistance;
}
function calcLength(axis) {
    return axis.max - axis.min;
}
/**
 * Calculate a transform origin relative to the source axis, between 0-1, that results
 * in an asthetically pleasing scale/transform needed to project from source to target.
 */
function calcOrigin(source, target) {
    var origin = 0.5;
    var sourceLength = calcLength(source);
    var targetLength = calcLength(target);
    if (targetLength > sourceLength) {
        origin = (0,popmotion__WEBPACK_IMPORTED_MODULE_2__.progress)(target.min, target.max - sourceLength, source.min);
    }
    else if (sourceLength > targetLength) {
        origin = (0,popmotion__WEBPACK_IMPORTED_MODULE_2__.progress)(source.min, source.max - targetLength, target.min);
    }
    return clampProgress(origin);
}
/**
 * Update the AxisDelta with a transform that projects source into target.
 *
 * The transform `origin` is optional. If not provided, it'll be automatically
 * calculated based on the relative positions of the two bounding boxes.
 */
function updateAxisDelta(delta, source, target, origin) {
    if (origin === void 0) { origin = 0.5; }
    delta.origin = origin;
    delta.originPoint = (0,popmotion__WEBPACK_IMPORTED_MODULE_3__.mix)(source.min, source.max, delta.origin);
    delta.scale = calcLength(target) / calcLength(source);
    if (isNear(delta.scale, 1, 0.0001))
        delta.scale = 1;
    delta.translate =
        (0,popmotion__WEBPACK_IMPORTED_MODULE_3__.mix)(target.min, target.max, delta.origin) - delta.originPoint;
    if (isNear(delta.translate))
        delta.translate = 0;
}
/**
 * Update the BoxDelta with a transform that projects the source into the target.
 *
 * The transform `origin` is optional. If not provided, it'll be automatically
 * calculated based on the relative positions of the two bounding boxes.
 */
function updateBoxDelta(delta, source, target, origin) {
    updateAxisDelta(delta.x, source.x, target.x, defaultOrigin(origin.originX));
    updateAxisDelta(delta.y, source.y, target.y, defaultOrigin(origin.originY));
}
/**
 * Currently this only accepts numerical origins, measured as 0-1, but could
 * accept pixel values by comparing to the target axis.
 */
function defaultOrigin(origin) {
    return typeof origin === "number" ? origin : 0.5;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/geometry/index.js":
/*!********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/geometry/index.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "axisBox": () => (/* binding */ axisBox),
/* harmony export */   "convertAxisBoxToBoundingBox": () => (/* binding */ convertAxisBoxToBoundingBox),
/* harmony export */   "convertBoundingBoxToAxisBox": () => (/* binding */ convertBoundingBoxToAxisBox),
/* harmony export */   "copyAxisBox": () => (/* binding */ copyAxisBox),
/* harmony export */   "delta": () => (/* binding */ delta),
/* harmony export */   "transformBoundingBox": () => (/* binding */ transformBoundingBox)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _noop_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../noop.js */ "./node_modules/framer-motion/dist/es/utils/noop.js");



/**
 * Bounding boxes tend to be defined as top, left, right, bottom. For various operations
 * it's easier to consider each axis individually. This function returns a bounding box
 * as a map of single-axis min/max values.
 */
function convertBoundingBoxToAxisBox(_a) {
    var top = _a.top, left = _a.left, right = _a.right, bottom = _a.bottom;
    return {
        x: { min: left, max: right },
        y: { min: top, max: bottom },
    };
}
function convertAxisBoxToBoundingBox(_a) {
    var x = _a.x, y = _a.y;
    return {
        top: y.min,
        bottom: y.max,
        left: x.min,
        right: x.max,
    };
}
/**
 * Applies a TransformPoint function to a bounding box. TransformPoint is usually a function
 * provided by Framer to allow measured points to be corrected for device scaling. This is used
 * when measuring DOM elements and DOM event points.
 */
function transformBoundingBox(_a, transformPoint) {
    var top = _a.top, left = _a.left, bottom = _a.bottom, right = _a.right;
    if (transformPoint === void 0) { transformPoint = _noop_js__WEBPACK_IMPORTED_MODULE_0__.noop; }
    var topLeft = transformPoint({ x: left, y: top });
    var bottomRight = transformPoint({ x: right, y: bottom });
    return {
        top: topLeft.y,
        left: topLeft.x,
        bottom: bottomRight.y,
        right: bottomRight.x,
    };
}
/**
 * Create an empty axis box of zero size
 */
function axisBox() {
    return { x: { min: 0, max: 1 }, y: { min: 0, max: 1 } };
}
function copyAxisBox(box) {
    return {
        x: (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, box.x),
        y: (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, box.y),
    };
}
/**
 * Create an empty box delta
 */
var zeroDelta = {
    translate: 0,
    scale: 1,
    origin: 0,
    originPoint: 0,
};
function delta() {
    return {
        x: (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, zeroDelta),
        y: (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, zeroDelta),
    };
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/is-numerical-string.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/is-numerical-string.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isNumericalString": () => (/* binding */ isNumericalString)
/* harmony export */ });
/**
 * Check if value is a numerical string, ie a string that is purely a number eg "100" or "-100.1"
 */
var isNumericalString = function (v) { return /^\-?\d*\.?\d+$/.test(v); };




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/is-ref-object.js":
/*!*******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/is-ref-object.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isRefObject": () => (/* binding */ isRefObject)
/* harmony export */ });
function isRefObject(ref) {
    return (typeof ref === "object" &&
        Object.prototype.hasOwnProperty.call(ref, "current"));
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/noop.js":
/*!**********************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/noop.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "noop": () => (/* binding */ noop)
/* harmony export */ });
function noop(any) {
    return any;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/resolve-value.js":
/*!*******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/resolve-value.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isCustomValue": () => (/* binding */ isCustomValue),
/* harmony export */   "resolveFinalValueInKeyframes": () => (/* binding */ resolveFinalValueInKeyframes)
/* harmony export */ });
/* harmony import */ var _animation_utils_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../animation/utils/is-keyframes-target.js */ "./node_modules/framer-motion/dist/es/animation/utils/is-keyframes-target.js");


var isCustomValue = function (v) {
    return Boolean(v && typeof v === "object" && v.mix && v.toValue);
};
var resolveFinalValueInKeyframes = function (v) {
    // TODO maybe throw if v.length - 1 is placeholder token?
    return (0,_animation_utils_is_keyframes_target_js__WEBPACK_IMPORTED_MODULE_0__.isKeyframesTarget)(v) ? v[v.length - 1] || 0 : v;
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/shallow-compare.js":
/*!*********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/shallow-compare.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "shallowCompare": () => (/* binding */ shallowCompare)
/* harmony export */ });
function shallowCompare(next, prev) {
    if (!Array.isArray(prev))
        return false;
    var prevLength = prev.length;
    if (prevLength !== next.length)
        return false;
    for (var i = 0; i < prevLength; i++) {
        if (prev[i] !== next[i])
            return false;
    }
    return true;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/subscription-manager.js":
/*!**************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/subscription-manager.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "SubscriptionManager": () => (/* binding */ SubscriptionManager)
/* harmony export */ });
/* harmony import */ var _array_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./array.js */ "./node_modules/framer-motion/dist/es/utils/array.js");


var SubscriptionManager = /** @class */ (function () {
    function SubscriptionManager() {
        this.subscriptions = [];
    }
    SubscriptionManager.prototype.add = function (handler) {
        var _this = this;
        (0,_array_js__WEBPACK_IMPORTED_MODULE_0__.addUniqueItem)(this.subscriptions, handler);
        return function () { return (0,_array_js__WEBPACK_IMPORTED_MODULE_0__.removeItem)(_this.subscriptions, handler); };
    };
    SubscriptionManager.prototype.notify = function (a, b, c) {
        var numSubscriptions = this.subscriptions.length;
        if (!numSubscriptions)
            return;
        if (numSubscriptions === 1) {
            /**
             * If there's only a single handler we can just call it without invoking a loop.
             */
            this.subscriptions[0](a, b, c);
        }
        else {
            for (var i = 0; i < numSubscriptions; i++) {
                /**
                 * Check whether the handler exists before firing as it's possible
                 * the subscriptions were modified during this loop running.
                 */
                var handler = this.subscriptions[i];
                handler && handler(a, b, c);
            }
        }
    };
    SubscriptionManager.prototype.getSize = function () {
        return this.subscriptions.length;
    };
    SubscriptionManager.prototype.clear = function () {
        this.subscriptions.length = 0;
    };
    return SubscriptionManager;
}());




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/time-conversion.js":
/*!*********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/time-conversion.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "secondsToMilliseconds": () => (/* binding */ secondsToMilliseconds)
/* harmony export */ });
/**
 * Converts seconds to milliseconds
 *
 * @param seconds - Time in seconds.
 * @return milliseconds - Converted time in milliseconds.
 */
var secondsToMilliseconds = function (seconds) { return seconds * 1000; };




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/transform.js":
/*!***************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/transform.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "transform": () => (/* binding */ transform)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/interpolate.js");



var isCustomValueType = function (v) {
    return typeof v === "object" && v.mix;
};
var getMixer = function (v) { return (isCustomValueType(v) ? v.mix : undefined); };
function transform() {
    var args = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        args[_i] = arguments[_i];
    }
    var useImmediate = !Array.isArray(args[0]);
    var argOffset = useImmediate ? 0 : -1;
    var inputValue = args[0 + argOffset];
    var inputRange = args[1 + argOffset];
    var outputRange = args[2 + argOffset];
    var options = args[3 + argOffset];
    var interpolator = (0,popmotion__WEBPACK_IMPORTED_MODULE_0__.interpolate)(inputRange, outputRange, (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({ mixer: getMixer(outputRange[0]) }, options));
    return useImmediate ? interpolator(inputValue) : interpolator;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/use-constant.js":
/*!******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/use-constant.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useConstant": () => (/* binding */ useConstant)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Creates a constant value over the lifecycle of a component.
 *
 * Even if `useMemo` is provided an empty array as its final argument, it doesn't offer
 * a guarantee that it won't re-run for performance reasons later on. By using `useConstant`
 * you can ensure that initialisers don't execute twice or more.
 */
function useConstant(init) {
    var ref = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
    if (ref.current === null) {
        ref.current = init();
    }
    return ref.current;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/use-cycle.js":
/*!***************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/use-cycle.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useCycle": () => (/* binding */ useCycle)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/wrap.js");




/**
 * Cycles through a series of visual properties. Can be used to toggle between or cycle through animations. It works similar to `useState` in React. It is provided an initial array of possible states, and returns an array of two arguments.
 *
 * @library
 *
 * ```jsx
 * import * as React from "react"
 * import { Frame, useCycle } from "framer"
 *
 * export function MyComponent() {
 *   const [x, cycleX] = useCycle(0, 50, 100)
 *
 *   return (
 *     <Frame
 *       animate={{ x: x }}
 *       onTap={() => cycleX()}
 *      />
 *    )
 * }
 * ```
 *
 * @motion
 *
 * An index value can be passed to the returned `cycle` function to cycle to a specific index.
 *
 * ```jsx
 * import * as React from "react"
 * import { motion, useCycle } from "framer-motion"
 *
 * export const MyComponent = () => {
 *   const [x, cycleX] = useCycle(0, 50, 100)
 *
 *   return (
 *     <motion.div
 *       animate={{ x: x }}
 *       onTap={() => cycleX()}
 *      />
 *    )
 * }
 * ```
 *
 * @param items - items to cycle through
 * @returns [currentState, cycleState]
 *
 * @public
 */
function useCycle() {
    var items = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        items[_i] = arguments[_i];
    }
    var index = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(0);
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)((0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(items[index.current]), 2), item = _a[0], setItem = _a[1];
    return [
        item,
        function (next) {
            index.current =
                typeof next !== "number"
                    ? (0,popmotion__WEBPACK_IMPORTED_MODULE_2__.wrap)(0, items.length, index.current + 1)
                    : next;
            setItem(items[index.current]);
        },
    ];
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/use-force-update.js":
/*!**********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/use-force-update.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useForceUpdate": () => (/* binding */ useForceUpdate)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _use_unmount_effect_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-unmount-effect.js */ "./node_modules/framer-motion/dist/es/utils/use-unmount-effect.js");




function useForceUpdate() {
    var unloadingRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)((0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(0), 2), forcedRenderCount = _a[0], setForcedRenderCount = _a[1];
    (0,_use_unmount_effect_js__WEBPACK_IMPORTED_MODULE_2__.useUnmountEffect)(function () { return (unloadingRef.current = true); });
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function () {
        !unloadingRef.current && setForcedRenderCount(forcedRenderCount + 1);
    }, [forcedRenderCount]);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/use-isomorphic-effect.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/use-isomorphic-effect.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useIsomorphicLayoutEffect": () => (/* binding */ useIsomorphicLayoutEffect)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


var isBrowser = typeof window !== "undefined";
var useIsomorphicLayoutEffect = isBrowser ? react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect : react__WEBPACK_IMPORTED_MODULE_0__.useEffect;




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/use-reduced-motion.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/use-reduced-motion.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useReducedMotion": () => (/* binding */ useReducedMotion)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _value_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../value/index.js */ "./node_modules/framer-motion/dist/es/value/index.js");
/* harmony import */ var _value_use_on_change_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../value/use-on-change.js */ "./node_modules/framer-motion/dist/es/value/use-on-change.js");





// Does this device prefer reduced motion? Returns `null` server-side.
var prefersReducedMotion;
function initPrefersReducedMotion() {
    prefersReducedMotion = (0,_value_index_js__WEBPACK_IMPORTED_MODULE_1__.motionValue)(null);
    if (typeof window === "undefined")
        return;
    if (window.matchMedia) {
        var motionMediaQuery_1 = window.matchMedia("(prefers-reduced-motion)");
        var setReducedMotionPreferences = function () {
            return prefersReducedMotion.set(motionMediaQuery_1.matches);
        };
        motionMediaQuery_1.addListener(setReducedMotionPreferences);
        setReducedMotionPreferences();
    }
    else {
        prefersReducedMotion.set(false);
    }
}
/**
 * A hook that returns `true` if we should be using reduced motion based on the current device's Reduced Motion setting.
 *
 * This can be used to implement changes to your UI based on Reduced Motion. For instance, replacing motion-sickness inducing
 * `x`/`y` animations with `opacity`, disabling the autoplay of background videos, or turning off parallax motion.
 *
 * It will actively respond to changes and re-render your components with the latest setting.
 *
 * ```jsx
 * export function Sidebar({ isOpen }) {
 *   const shouldReduceMotion = useReducedMotion()
 *   const closedX = shouldReduceMotion ? 0 : "-100%"
 *
 *   return (
 *     <motion.div animate={{
 *       opacity: isOpen ? 1 : 0,
 *       x: isOpen ? 0 : closedX
 *     }} />
 *   )
 * }
 * ```
 *
 * @return boolean
 *
 * @public
 */
function useReducedMotion() {
    /**
     * Lazy initialisation of prefersReducedMotion
     */
    !prefersReducedMotion && initPrefersReducedMotion();
    var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_2__.__read)((0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(prefersReducedMotion.get()), 2), shouldReduceMotion = _a[0], setShouldReduceMotion = _a[1];
    (0,_value_use_on_change_js__WEBPACK_IMPORTED_MODULE_3__.useOnChange)(prefersReducedMotion, setShouldReduceMotion);
    return shouldReduceMotion;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/utils/use-unmount-effect.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/utils/use-unmount-effect.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useUnmountEffect": () => (/* binding */ useUnmountEffect)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


function useUnmountEffect(callback) {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () { return function () { return callback(); }; }, []);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/index.js":
/*!***********************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/index.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MotionValue": () => (/* binding */ MotionValue),
/* harmony export */   "motionValue": () => (/* binding */ motionValue)
/* harmony export */ });
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/utils/velocity-per-second.js");
/* harmony import */ var framesync__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! framesync */ "./node_modules/framesync/dist/es/index.js");
/* harmony import */ var _utils_subscription_manager_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/subscription-manager.js */ "./node_modules/framer-motion/dist/es/utils/subscription-manager.js");




var isFloat = function (value) {
    return !isNaN(parseFloat(value));
};
/**
 * `MotionValue` is used to track the state and velocity of motion values.
 *
 * @public
 */
var MotionValue = /** @class */ (function () {
    /**
     * @param init - The initiating value
     * @param config - Optional configuration options
     *
     * -  `transformer`: A function to transform incoming values with.
     *
     * @internal
     */
    function MotionValue(init) {
        var _this = this;
        /**
         * Duration, in milliseconds, since last updating frame.
         *
         * @internal
         */
        this.timeDelta = 0;
        /**
         * Timestamp of the last time this `MotionValue` was updated.
         *
         * @internal
         */
        this.lastUpdated = 0;
        /**
         * Functions to notify when the `MotionValue` updates.
         *
         * @internal
         */
        this.updateSubscribers = new _utils_subscription_manager_js__WEBPACK_IMPORTED_MODULE_1__.SubscriptionManager();
        /**
         * Functions to notify when the velocity updates.
         *
         * @internal
         */
        this.velocityUpdateSubscribers = new _utils_subscription_manager_js__WEBPACK_IMPORTED_MODULE_1__.SubscriptionManager();
        /**
         * Functions to notify when the `MotionValue` updates and `render` is set to `true`.
         *
         * @internal
         */
        this.renderSubscribers = new _utils_subscription_manager_js__WEBPACK_IMPORTED_MODULE_1__.SubscriptionManager();
        /**
         * Tracks whether this value can output a velocity. Currently this is only true
         * if the value is numerical, but we might be able to widen the scope here and support
         * other value types.
         *
         * @internal
         */
        this.canTrackVelocity = false;
        this.updateAndNotify = function (v, render) {
            if (render === void 0) { render = true; }
            _this.prev = _this.current;
            _this.current = v;
            // Update timestamp
            var _a = (0,framesync__WEBPACK_IMPORTED_MODULE_0__.getFrameData)(), delta = _a.delta, timestamp = _a.timestamp;
            if (_this.lastUpdated !== timestamp) {
                _this.timeDelta = delta;
                _this.lastUpdated = timestamp;
                framesync__WEBPACK_IMPORTED_MODULE_0__["default"].postRender(_this.scheduleVelocityCheck);
            }
            // Update update subscribers
            if (_this.prev !== _this.current) {
                _this.updateSubscribers.notify(_this.current);
            }
            // Update velocity subscribers
            if (_this.velocityUpdateSubscribers.getSize()) {
                _this.velocityUpdateSubscribers.notify(_this.getVelocity());
            }
            // Update render subscribers
            if (render) {
                _this.renderSubscribers.notify(_this.current);
            }
        };
        /**
         * Schedule a velocity check for the next frame.
         *
         * This is an instanced and bound function to prevent generating a new
         * function once per frame.
         *
         * @internal
         */
        this.scheduleVelocityCheck = function () { return framesync__WEBPACK_IMPORTED_MODULE_0__["default"].postRender(_this.velocityCheck); };
        /**
         * Updates `prev` with `current` if the value hasn't been updated this frame.
         * This ensures velocity calculations return `0`.
         *
         * This is an instanced and bound function to prevent generating a new
         * function once per frame.
         *
         * @internal
         */
        this.velocityCheck = function (_a) {
            var timestamp = _a.timestamp;
            if (timestamp !== _this.lastUpdated) {
                _this.prev = _this.current;
                _this.velocityUpdateSubscribers.notify(_this.getVelocity());
            }
        };
        this.hasAnimated = false;
        this.prev = this.current = init;
        this.canTrackVelocity = isFloat(this.current);
    }
    /**
     * Adds a function that will be notified when the `MotionValue` is updated.
     *
     * It returns a function that, when called, will cancel the subscription.
     *
     * When calling `onChange` inside a React component, it should be wrapped with the
     * `useEffect` hook. As it returns an unsubscribe function, this should be returned
     * from the `useEffect` function to ensure you don't add duplicate subscribers..
     *
     * @library
     *
     * ```jsx
     * function MyComponent() {
     *   const x = useMotionValue(0)
     *   const y = useMotionValue(0)
     *   const opacity = useMotionValue(1)
     *
     *   useEffect(() => {
     *     function updateOpacity() {
     *       const maxXY = Math.max(x.get(), y.get())
     *       const newOpacity = transform(maxXY, [0, 100], [1, 0])
     *       opacity.set(newOpacity)
     *     }
     *
     *     const unsubscribeX = x.onChange(updateOpacity)
     *     const unsubscribeY = y.onChange(updateOpacity)
     *
     *     return () => {
     *       unsubscribeX()
     *       unsubscribeY()
     *     }
     *   }, [])
     *
     *   return <Frame x={x} />
     * }
     * ```
     *
     * @motion
     *
     * ```jsx
     * export const MyComponent = () => {
     *   const x = useMotionValue(0)
     *   const y = useMotionValue(0)
     *   const opacity = useMotionValue(1)
     *
     *   useEffect(() => {
     *     function updateOpacity() {
     *       const maxXY = Math.max(x.get(), y.get())
     *       const newOpacity = transform(maxXY, [0, 100], [1, 0])
     *       opacity.set(newOpacity)
     *     }
     *
     *     const unsubscribeX = x.onChange(updateOpacity)
     *     const unsubscribeY = y.onChange(updateOpacity)
     *
     *     return () => {
     *       unsubscribeX()
     *       unsubscribeY()
     *     }
     *   }, [])
     *
     *   return <motion.div style={{ x }} />
     * }
     * ```
     *
     * @internalremarks
     *
     * We could look into a `useOnChange` hook if the above lifecycle management proves confusing.
     *
     * ```jsx
     * useOnChange(x, () => {})
     * ```
     *
     * @param subscriber - A function that receives the latest value.
     * @returns A function that, when called, will cancel this subscription.
     *
     * @public
     */
    MotionValue.prototype.onChange = function (subscription) {
        return this.updateSubscribers.add(subscription);
    };
    MotionValue.prototype.clearListeners = function () {
        this.updateSubscribers.clear();
    };
    /**
     * Adds a function that will be notified when the `MotionValue` requests a render.
     *
     * @param subscriber - A function that's provided the latest value.
     * @returns A function that, when called, will cancel this subscription.
     *
     * @internal
     */
    MotionValue.prototype.onRenderRequest = function (subscription) {
        // Render immediately
        subscription(this.get());
        return this.renderSubscribers.add(subscription);
    };
    /**
     * Attaches a passive effect to the `MotionValue`.
     *
     * @internal
     */
    MotionValue.prototype.attach = function (passiveEffect) {
        this.passiveEffect = passiveEffect;
    };
    /**
     * Sets the state of the `MotionValue`.
     *
     * @remarks
     *
     * ```jsx
     * const x = useMotionValue(0)
     * x.set(10)
     * ```
     *
     * @param latest - Latest value to set.
     * @param render - Whether to notify render subscribers. Defaults to `true`
     *
     * @public
     */
    MotionValue.prototype.set = function (v, render) {
        if (render === void 0) { render = true; }
        if (!render || !this.passiveEffect) {
            this.updateAndNotify(v, render);
        }
        else {
            this.passiveEffect(v, this.updateAndNotify);
        }
    };
    /**
     * Returns the latest state of `MotionValue`
     *
     * @returns - The latest state of `MotionValue`
     *
     * @public
     */
    MotionValue.prototype.get = function () {
        return this.current;
    };
    /**
     * @public
     */
    MotionValue.prototype.getPrevious = function () {
        return this.prev;
    };
    /**
     * Returns the latest velocity of `MotionValue`
     *
     * @returns - The latest velocity of `MotionValue`. Returns `0` if the state is non-numerical.
     *
     * @public
     */
    MotionValue.prototype.getVelocity = function () {
        // This could be isFloat(this.prev) && isFloat(this.current), but that would be wasteful
        return this.canTrackVelocity
            ? // These casts could be avoided if parseFloat would be typed better
                (0,popmotion__WEBPACK_IMPORTED_MODULE_2__.velocityPerSecond)(parseFloat(this.current) -
                    parseFloat(this.prev), this.timeDelta)
            : 0;
    };
    /**
     * Registers a new animation to control this `MotionValue`. Only one
     * animation can drive a `MotionValue` at one time.
     *
     * ```jsx
     * value.start()
     * ```
     *
     * @param animation - A function that starts the provided animation
     *
     * @internal
     */
    MotionValue.prototype.start = function (animation) {
        var _this = this;
        this.stop();
        return new Promise(function (resolve) {
            _this.hasAnimated = true;
            _this.stopAnimation = animation(resolve);
        }).then(function () { return _this.clearAnimation(); });
    };
    /**
     * Stop the currently active animation.
     *
     * @public
     */
    MotionValue.prototype.stop = function () {
        if (this.stopAnimation)
            this.stopAnimation();
        this.clearAnimation();
    };
    /**
     * Returns `true` if this value is currently animating.
     *
     * @public
     */
    MotionValue.prototype.isAnimating = function () {
        return !!this.stopAnimation;
    };
    MotionValue.prototype.clearAnimation = function () {
        this.stopAnimation = null;
    };
    /**
     * Destroy and clean up subscribers to this `MotionValue`.
     *
     * The `MotionValue` hooks like `useMotionValue` and `useTransform` automatically
     * handle the lifecycle of the returned `MotionValue`, so this method is only necessary if you've manually
     * created a `MotionValue` via the `motionValue` function.
     *
     * @public
     */
    MotionValue.prototype.destroy = function () {
        this.updateSubscribers.clear();
        this.renderSubscribers.clear();
        this.stop();
    };
    return MotionValue;
}());
/**
 * @internal
 */
function motionValue(init) {
    return new MotionValue(init);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/scroll/use-element-scroll.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/scroll/use-element-scroll.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useElementScroll": () => (/* binding */ useElementScroll)
/* harmony export */ });
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/use-isomorphic-effect.js */ "./node_modules/framer-motion/dist/es/utils/use-isomorphic-effect.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var _events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../events/use-dom-event.js */ "./node_modules/framer-motion/dist/es/events/use-dom-event.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils.js */ "./node_modules/framer-motion/dist/es/value/scroll/utils.js");






var getElementScrollOffsets = function (element) { return function () {
    return {
        xOffset: element.scrollLeft,
        yOffset: element.scrollTop,
        xMaxOffset: element.scrollWidth - element.offsetWidth,
        yMaxOffset: element.scrollHeight - element.offsetHeight,
    };
}; };
/**
 * Returns MotionValues that update when the provided element scrolls:
 *
 * - `scrollX` — Horizontal scroll distance in pixels.
 * - `scrollY` — Vertical scroll distance in pixels.
 * - `scrollXProgress` — Horizontal scroll progress between `0` and `1`.
 * - `scrollYProgress` — Vertical scroll progress between `0` and `1`.
 *
 * This element must be set to `overflow: scroll` on either or both axes to report scroll offset.
 *
 * @library
 *
 * ```jsx
 * import * as React from "react"
 * import {
 *   Frame,
 *   useElementScroll,
 *   useTransform
 * } from "framer"
 *
 * export function MyComponent() {
 *   const ref = React.useRef()
 *   const { scrollYProgress } = useElementScroll(ref)
 *
 *   return (
 *     <Frame ref={ref}>
 *       <Frame scaleX={scrollYProgress} />
 *     </Frame>
 *   )
 * }
 * ```
 *
 * @motion
 *
 * ```jsx
 * export const MyComponent = () => {
 *   const ref = useRef()
 *   const { scrollYProgress } = useElementScroll(ref)
 *
 *   return (
 *     <div ref={ref}>
 *       <motion.div style={{ scaleX: scrollYProgress }} />
 *     </div>
 *   )
 * }
 * ```
 *
 * @public
 */
function useElementScroll(ref) {
    var values = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__.useConstant)(_utils_js__WEBPACK_IMPORTED_MODULE_2__.createScrollMotionValues);
    (0,_utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_3__.useIsomorphicLayoutEffect)(function () {
        var element = ref.current;
        (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(!!element, "ref provided to useScroll must be passed into a HTML element.");
        if (!element)
            return;
        var updateScrollValues = (0,_utils_js__WEBPACK_IMPORTED_MODULE_2__.createScrollUpdater)(values, getElementScrollOffsets(element));
        var scrollListener = (0,_events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_4__.addDomEvent)(element, "scroll", updateScrollValues, { passive: true });
        var resizeListener = (0,_events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_4__.addDomEvent)(element, "resize", updateScrollValues);
        return function () {
            scrollListener && scrollListener();
            resizeListener && resizeListener();
        };
    }, []);
    return values;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/scroll/use-viewport-scroll.js":
/*!********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/scroll/use-viewport-scroll.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useViewportScroll": () => (/* binding */ useViewportScroll)
/* harmony export */ });
/* harmony import */ var _utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/use-isomorphic-effect.js */ "./node_modules/framer-motion/dist/es/utils/use-isomorphic-effect.js");
/* harmony import */ var _events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../events/use-dom-event.js */ "./node_modules/framer-motion/dist/es/events/use-dom-event.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils.js */ "./node_modules/framer-motion/dist/es/value/scroll/utils.js");




var viewportScrollValues;
function getViewportScrollOffsets() {
    return {
        xOffset: window.pageXOffset,
        yOffset: window.pageYOffset,
        xMaxOffset: document.body.clientWidth - window.innerWidth,
        yMaxOffset: document.body.clientHeight - window.innerHeight,
    };
}
var hasListeners = false;
function addEventListeners() {
    hasListeners = true;
    if (typeof window === "undefined")
        return;
    var updateScrollValues = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.createScrollUpdater)(viewportScrollValues, getViewportScrollOffsets);
    (0,_events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__.addDomEvent)(window, "scroll", updateScrollValues, { passive: true });
    (0,_events_use_dom_event_js__WEBPACK_IMPORTED_MODULE_1__.addDomEvent)(window, "resize", updateScrollValues);
}
/**
 * Returns MotionValues that update when the viewport scrolls:
 *
 * - `scrollX` — Horizontal scroll distance in pixels.
 * - `scrollY` — Vertical scroll distance in pixels.
 * - `scrollXProgress` — Horizontal scroll progress between `0` and `1`.
 * - `scrollYProgress` — Vertical scroll progress between `0` and `1`.
 *
 * **Warning:** Setting `body` or `html` to `height: 100%` or similar will break the `Progress`
 * values as this breaks the browser's capability to accurately measure the page length.
 *
 * @library
 *
 * ```jsx
 * import * as React from "react"
 * import {
 *   Frame,
 *   useViewportScroll,
 *   useTransform
 * } from "framer"
 *
 * export function MyComponent() {
 *   const { scrollYProgress } = useViewportScroll()
 *   return <Frame scaleX={scrollYProgress} />
 * }
 * ```
 *
 * @motion
 *
 * ```jsx
 * export const MyComponent = () => {
 *   const { scrollYProgress } = useViewportScroll()
 *   return <motion.div style={{ scaleX: scrollYProgress }} />
 * }
 * ```
 *
 * @public
 */
function useViewportScroll() {
    /**
     * Lazy-initialise the viewport scroll values
     */
    if (!viewportScrollValues) {
        viewportScrollValues = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.createScrollMotionValues)();
    }
    (0,_utils_use_isomorphic_effect_js__WEBPACK_IMPORTED_MODULE_2__.useIsomorphicLayoutEffect)(function () {
        !hasListeners && addEventListeners();
    }, []);
    return viewportScrollValues;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/scroll/utils.js":
/*!******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/scroll/utils.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createScrollMotionValues": () => (/* binding */ createScrollMotionValues),
/* harmony export */   "createScrollUpdater": () => (/* binding */ createScrollUpdater)
/* harmony export */ });
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../index.js */ "./node_modules/framer-motion/dist/es/value/index.js");


function createScrollMotionValues() {
    return {
        scrollX: (0,_index_js__WEBPACK_IMPORTED_MODULE_0__.motionValue)(0),
        scrollY: (0,_index_js__WEBPACK_IMPORTED_MODULE_0__.motionValue)(0),
        scrollXProgress: (0,_index_js__WEBPACK_IMPORTED_MODULE_0__.motionValue)(0),
        scrollYProgress: (0,_index_js__WEBPACK_IMPORTED_MODULE_0__.motionValue)(0),
    };
}
function setProgress(offset, maxOffset, value) {
    value.set(!offset || !maxOffset ? 0 : offset / maxOffset);
}
function createScrollUpdater(values, getOffsets) {
    var update = function () {
        var _a = getOffsets(), xOffset = _a.xOffset, yOffset = _a.yOffset, xMaxOffset = _a.xMaxOffset, yMaxOffset = _a.yMaxOffset;
        // Set absolute positions
        values.scrollX.set(xOffset);
        values.scrollY.set(yOffset);
        // Set 0-1 progress
        setProgress(xOffset, xMaxOffset, values.scrollXProgress);
        setProgress(yOffset, yMaxOffset, values.scrollYProgress);
    };
    update();
    return update;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-combine-values.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-combine-values.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useCombineMotionValues": () => (/* binding */ useCombineMotionValues)
/* harmony export */ });
/* harmony import */ var framesync__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! framesync */ "./node_modules/framesync/dist/es/index.js");
/* harmony import */ var _use_motion_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-motion-value.js */ "./node_modules/framer-motion/dist/es/value/use-motion-value.js");
/* harmony import */ var _use_on_change_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-on-change.js */ "./node_modules/framer-motion/dist/es/value/use-on-change.js");




function useCombineMotionValues(values, combineValues) {
    /**
     * Initialise the returned motion value. This remains the same between renders.
     */
    var value = (0,_use_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.useMotionValue)(combineValues());
    /**
     * Create a function that will update the template motion value with the latest values.
     * This is pre-bound so whenever a motion value updates it can schedule its
     * execution in Framesync. If it's already been scheduled it won't be fired twice
     * in a single frame.
     */
    var updateValue = function () { return value.set(combineValues()); };
    /**
     * Synchronously update the motion value with the latest values during the render.
     * This ensures that within a React render, the styles applied to the DOM are up-to-date.
     */
    updateValue();
    /**
     * Subscribe to all motion values found within the template. Whenever any of them change,
     * schedule an update.
     */
    (0,_use_on_change_js__WEBPACK_IMPORTED_MODULE_2__.useMultiOnChange)(values, function () { return framesync__WEBPACK_IMPORTED_MODULE_0__["default"].update(updateValue, false, true); });
    return value;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-inverted-scale.js":
/*!************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-inverted-scale.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "invertScale": () => (/* binding */ invertScale),
/* harmony export */   "useInvertedScale": () => (/* binding */ useInvertedScale)
/* harmony export */ });
/* harmony import */ var _context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../context/MotionContext/index.js */ "./node_modules/framer-motion/dist/es/context/MotionContext/index.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var _use_motion_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-motion-value.js */ "./node_modules/framer-motion/dist/es/value/use-motion-value.js");
/* harmony import */ var _use_transform_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-transform.js */ "./node_modules/framer-motion/dist/es/value/use-transform.js");





// Keep things reasonable and avoid scale: Infinity. In practise we might need
// to add another value, opacity, that could interpolate scaleX/Y [0,0.01] => [0,1]
// to simply hide content at unreasonable scales.
var maxScale = 100000;
var invertScale = function (scale) {
    return scale > 0.001 ? 1 / scale : maxScale;
};
var hasWarned = false;
/**
 * Returns a `MotionValue` each for `scaleX` and `scaleY` that update with the inverse
 * of their respective parent scales.
 *
 * This is useful for undoing the distortion of content when scaling a parent component.
 *
 * By default, `useInvertedScale` will automatically fetch `scaleX` and `scaleY` from the nearest parent.
 * By passing other `MotionValue`s in as `useInvertedScale({ scaleX, scaleY })`, it will invert the output
 * of those instead.
 *
 * @motion
 *
 * ```jsx
 * const MyComponent = () => {
 *   const { scaleX, scaleY } = useInvertedScale()
 *   return <motion.div style={{ scaleX, scaleY }} />
 * }
 * ```
 *
 * @library
 *
 * ```jsx
 * function MyComponent() {
 *   const { scaleX, scaleY } = useInvertedScale()
 *   return <Frame scaleX={scaleX} scaleY={scaleY} />
 * }
 * ```
 *
 * @deprecated
 */
function useInvertedScale(scale) {
    var parentScaleX = (0,_use_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.useMotionValue)(1);
    var parentScaleY = (0,_use_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.useMotionValue)(1);
    var visualElement = (0,_context_MotionContext_index_js__WEBPACK_IMPORTED_MODULE_2__.useVisualElementContext)();
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(!!(scale || visualElement), "If no scale values are provided, useInvertedScale must be used within a child of another motion component.");
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.warning)(hasWarned, "useInvertedScale is deprecated and will be removed in 3.0. Use the layout prop instead.");
    hasWarned = true;
    if (scale) {
        parentScaleX = scale.scaleX || parentScaleX;
        parentScaleY = scale.scaleY || parentScaleY;
    }
    else if (visualElement) {
        parentScaleX = visualElement.getValue("scaleX", 1);
        parentScaleY = visualElement.getValue("scaleY", 1);
    }
    var scaleX = (0,_use_transform_js__WEBPACK_IMPORTED_MODULE_3__.useTransform)(parentScaleX, invertScale);
    var scaleY = (0,_use_transform_js__WEBPACK_IMPORTED_MODULE_3__.useTransform)(parentScaleY, invertScale);
    return { scaleX: scaleX, scaleY: scaleY };
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-motion-template.js":
/*!*************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-motion-template.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useMotionTemplate": () => (/* binding */ useMotionTemplate)
/* harmony export */ });
/* harmony import */ var _use_combine_values_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-combine-values.js */ "./node_modules/framer-motion/dist/es/value/use-combine-values.js");


/**
 * Combine multiple motion values into a new one using a string template literal.
 *
 * ```jsx
 * import {
 *   motion,
 *   useSpring,
 *   useMotionValue,
 *   useMotionTemplate
 * } from "framer-motion"
 *
 * function Component() {
 *   const shadowX = useSpring(0)
 *   const shadowY = useMotionValue(0)
 *   const shadow = useMotionTemplate`drop-shadow(${shadowX}px ${shadowY}px 20px rgba(0,0,0,0.3))`
 *
 *   return <motion.div style={{ filter: shadow }} />
 * }
 * ```
 *
 * @public
 */
function useMotionTemplate(fragments) {
    var values = [];
    for (var _i = 1; _i < arguments.length; _i++) {
        values[_i - 1] = arguments[_i];
    }
    /**
     * Create a function that will build a string from the latest motion values.
     */
    var numFragments = fragments.length;
    function buildValue() {
        var output = "";
        for (var i = 0; i < numFragments; i++) {
            output += fragments[i];
            var value = values[i];
            if (value)
                output += values[i].get();
        }
        return output;
    }
    return (0,_use_combine_values_js__WEBPACK_IMPORTED_MODULE_0__.useCombineMotionValues)(values, buildValue);
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-motion-value.js":
/*!**********************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-motion-value.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useMotionValue": () => (/* binding */ useMotionValue)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./index.js */ "./node_modules/framer-motion/dist/es/value/index.js");






/**
 * Creates a `MotionValue` to track the state and velocity of a value.
 *
 * Usually, these are created automatically. For advanced use-cases, like use with `useTransform`, you can create `MotionValue`s externally and pass them into the animated component via the `style` prop.
 *
 * @library
 *
 * ```jsx
 * export function MyComponent() {
 *   const scale = useMotionValue(1)
 *
 *   return <Frame scale={scale} />
 * }
 * ```
 *
 * @motion
 *
 * ```jsx
 * export const MyComponent = () => {
 *   const scale = useMotionValue(1)
 *
 *   return <motion.div style={{ scale }} />
 * }
 * ```
 *
 * @param initial - The initial state.
 *
 * @public
 */
function useMotionValue(initial) {
    var value = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_1__.useConstant)(function () { return (0,_index_js__WEBPACK_IMPORTED_MODULE_2__.motionValue)(initial); });
    /**
     * If this motion value is being used in static mode, like on
     * the Framer canvas, force components to rerender when the motion
     * value is updated.
     */
    var isStatic = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_3__.MotionConfigContext).isStatic;
    if (isStatic) {
        var _a = (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__read)((0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initial), 2), setLatest_1 = _a[1];
        (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () { return value.onChange(setLatest_1); }, []);
    }
    return value;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-on-change.js":
/*!*******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-on-change.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useMultiOnChange": () => (/* binding */ useMultiOnChange),
/* harmony export */   "useOnChange": () => (/* binding */ useOnChange)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");



function useOnChange(value, callback) {
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        if ((0,_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.isMotionValue)(value))
            return value.onChange(callback);
    }, [callback]);
}
function useMultiOnChange(values, handler) {
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        var subscriptions = values.map(function (value) { return value.onChange(handler); });
        return function () { return subscriptions.forEach(function (unsubscribe) { return unsubscribe(); }); };
    });
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-spring.js":
/*!****************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-spring.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useSpring": () => (/* binding */ useSpring)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../context/MotionConfigContext.js */ "./node_modules/framer-motion/dist/es/context/MotionConfigContext.js");
/* harmony import */ var popmotion__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! popmotion */ "./node_modules/popmotion/dist/es/animations/index.js");
/* harmony import */ var _utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/is-motion-value.js */ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js");
/* harmony import */ var _use_motion_value_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-motion-value.js */ "./node_modules/framer-motion/dist/es/value/use-motion-value.js");
/* harmony import */ var _use_on_change_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./use-on-change.js */ "./node_modules/framer-motion/dist/es/value/use-on-change.js");








/**
 * Creates a `MotionValue` that, when `set`, will use a spring animation to animate to its new state.
 *
 * It can either work as a stand-alone `MotionValue` by initialising it with a value, or as a subscriber
 * to another `MotionValue`.
 *
 * @remarks
 *
 * ```jsx
 * const x = useSpring(0, { stiffness: 300 })
 * const y = useSpring(x, { damping: 10 })
 * ```
 *
 * @param inputValue - `MotionValue` or number. If provided a `MotionValue`, when the input `MotionValue` changes, the created `MotionValue` will spring towards that value.
 * @param springConfig - Configuration options for the spring.
 * @returns `MotionValue`
 *
 * @public
 */
function useSpring(source, config) {
    if (config === void 0) { config = {}; }
    var isStatic = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_MotionConfigContext_js__WEBPACK_IMPORTED_MODULE_1__.MotionConfigContext).isStatic;
    var activeSpringAnimation = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
    var value = (0,_use_motion_value_js__WEBPACK_IMPORTED_MODULE_2__.useMotionValue)((0,_utils_is_motion_value_js__WEBPACK_IMPORTED_MODULE_3__.isMotionValue)(source) ? source.get() : source);
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () {
        return value.attach(function (v, set) {
            /**
             * A more hollistic approach to this might be to use isStatic to fix VisualElement animations
             * at that level, but this will work for now
             */
            if (isStatic)
                return set(v);
            if (activeSpringAnimation.current) {
                activeSpringAnimation.current.stop();
            }
            activeSpringAnimation.current = (0,popmotion__WEBPACK_IMPORTED_MODULE_4__.animate)((0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)({ from: value.get(), to: v, velocity: value.getVelocity() }, config), { onUpdate: set }));
            return value.get();
        });
    }, Object.values(config));
    (0,_use_on_change_js__WEBPACK_IMPORTED_MODULE_6__.useOnChange)(source, function (v) { return value.set(parseFloat(v)); });
    return value;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-transform.js":
/*!*******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-transform.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useTransform": () => (/* binding */ useTransform)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_use_constant_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/use-constant.js */ "./node_modules/framer-motion/dist/es/utils/use-constant.js");
/* harmony import */ var _use_combine_values_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-combine-values.js */ "./node_modules/framer-motion/dist/es/value/use-combine-values.js");
/* harmony import */ var _utils_transform_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/transform.js */ "./node_modules/framer-motion/dist/es/utils/transform.js");





function useTransform(input, inputRangeOrTransformer, outputRange, options) {
    var transformer = typeof inputRangeOrTransformer === "function"
        ? inputRangeOrTransformer
        : (0,_utils_transform_js__WEBPACK_IMPORTED_MODULE_0__.transform)(inputRangeOrTransformer, outputRange, options);
    return Array.isArray(input)
        ? useListTransform(input, transformer)
        : useListTransform([input], function (_a) {
            var _b = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__read)(_a, 1), latest = _b[0];
            return transformer(latest);
        });
}
function useListTransform(values, transformer) {
    var latest = (0,_utils_use_constant_js__WEBPACK_IMPORTED_MODULE_2__.useConstant)(function () { return []; });
    return (0,_use_combine_values_js__WEBPACK_IMPORTED_MODULE_3__.useCombineMotionValues)(values, function () {
        latest.length = 0;
        var numValues = values.length;
        for (var i = 0; i < numValues; i++) {
            latest[i] = values[i].get();
        }
        return transformer(latest);
    });
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/use-velocity.js":
/*!******************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/use-velocity.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "useVelocity": () => (/* binding */ useVelocity)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _use_motion_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-motion-value.js */ "./node_modules/framer-motion/dist/es/value/use-motion-value.js");



/**
 * Creates a `MotionValue` that updates when the velocity of the provided `MotionValue` changes.
 *
 * ```javascript
 * const x = useMotionValue(0)
 * const xVelocity = useVelocity(x)
 * const xAcceleration = useVelocity(xVelocity)
 * ```
 *
 * @public
 */
function useVelocity(value) {
    var velocity = (0,_use_motion_value_js__WEBPACK_IMPORTED_MODULE_1__.useMotionValue)(value.getVelocity());
    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
        return value.velocityUpdateSubscribers.add(function (newVelocity) {
            velocity.set(newVelocity);
        });
    }, [value]);
    return velocity;
}




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js":
/*!***************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/utils/is-motion-value.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isMotionValue": () => (/* binding */ isMotionValue)
/* harmony export */ });
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../index.js */ "./node_modules/framer-motion/dist/es/value/index.js");


var isMotionValue = function (value) {
    return value instanceof _index_js__WEBPACK_IMPORTED_MODULE_0__.MotionValue;
};




/***/ }),

/***/ "./node_modules/framer-motion/dist/es/value/utils/resolve-motion-value.js":
/*!********************************************************************************!*\
  !*** ./node_modules/framer-motion/dist/es/value/utils/resolve-motion-value.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "resolveMotionValue": () => (/* binding */ resolveMotionValue)
/* harmony export */ });
/* harmony import */ var _utils_resolve_value_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/resolve-value.js */ "./node_modules/framer-motion/dist/es/utils/resolve-value.js");
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../index.js */ "./node_modules/framer-motion/dist/es/value/index.js");



/**
 * If the provided value is a MotionValue, this returns the actual value, otherwise just the value itself
 *
 * TODO: Remove and move to library
 *
 * @internal
 */
function resolveMotionValue(value) {
    var unwrappedValue = value instanceof _index_js__WEBPACK_IMPORTED_MODULE_0__.MotionValue ? value.get() : value;
    return (0,_utils_resolve_value_js__WEBPACK_IMPORTED_MODULE_1__.isCustomValue)(unwrappedValue)
        ? unwrappedValue.toValue()
        : unwrappedValue;
}




/***/ }),

/***/ "./node_modules/framesync/dist/es/create-render-step.js":
/*!**************************************************************!*\
  !*** ./node_modules/framesync/dist/es/create-render-step.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createRenderStep": () => (/* binding */ createRenderStep)
/* harmony export */ });
function createRenderStep(runNextFrame) {
    var toRun = [];
    var toRunNextFrame = [];
    var numToRun = 0;
    var isProcessing = false;
    var toKeepAlive = new WeakSet();
    var step = {
        schedule: function (callback, keepAlive, immediate) {
            if (keepAlive === void 0) { keepAlive = false; }
            if (immediate === void 0) { immediate = false; }
            var addToCurrentFrame = immediate && isProcessing;
            var buffer = addToCurrentFrame ? toRun : toRunNextFrame;
            if (keepAlive)
                toKeepAlive.add(callback);
            if (buffer.indexOf(callback) === -1) {
                buffer.push(callback);
                if (addToCurrentFrame && isProcessing)
                    numToRun = toRun.length;
            }
            return callback;
        },
        cancel: function (callback) {
            var index = toRunNextFrame.indexOf(callback);
            if (index !== -1)
                toRunNextFrame.splice(index, 1);
            toKeepAlive.delete(callback);
        },
        process: function (frameData) {
            var _a;
            isProcessing = true;
            _a = [toRunNextFrame, toRun], toRun = _a[0], toRunNextFrame = _a[1];
            toRunNextFrame.length = 0;
            numToRun = toRun.length;
            if (numToRun) {
                for (var i = 0; i < numToRun; i++) {
                    var callback = toRun[i];
                    callback(frameData);
                    if (toKeepAlive.has(callback)) {
                        step.schedule(callback);
                        runNextFrame();
                    }
                }
            }
            isProcessing = false;
        },
    };
    return step;
}




/***/ }),

/***/ "./node_modules/framesync/dist/es/index.js":
/*!*************************************************!*\
  !*** ./node_modules/framesync/dist/es/index.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "cancelSync": () => (/* binding */ cancelSync),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   "getFrameData": () => (/* binding */ getFrameData)
/* harmony export */ });
/* harmony import */ var _on_next_frame_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./on-next-frame.js */ "./node_modules/framesync/dist/es/on-next-frame.js");
/* harmony import */ var _create_render_step_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./create-render-step.js */ "./node_modules/framesync/dist/es/create-render-step.js");



var maxElapsed = 40;
var useDefaultElapsed = true;
var runNextFrame = false;
var isProcessing = false;
var frame = {
    delta: 0,
    timestamp: 0
};
var stepsOrder = ["read", "update", "preRender", "render", "postRender"];
var steps = /*#__PURE__*/stepsOrder.reduce(function (acc, key) {
    acc[key] = (0,_create_render_step_js__WEBPACK_IMPORTED_MODULE_1__.createRenderStep)(function () {
        return runNextFrame = true;
    });
    return acc;
}, {});
var sync = /*#__PURE__*/stepsOrder.reduce(function (acc, key) {
    var step = steps[key];
    acc[key] = function (process, keepAlive, immediate) {
        if (keepAlive === void 0) {
            keepAlive = false;
        }
        if (immediate === void 0) {
            immediate = false;
        }
        if (!runNextFrame) startLoop();
        return step.schedule(process, keepAlive, immediate);
    };
    return acc;
}, {});
var cancelSync = /*#__PURE__*/stepsOrder.reduce(function (acc, key) {
    acc[key] = steps[key].cancel;
    return acc;
}, {});
var processStep = function (stepId) {
    return steps[stepId].process(frame);
};
var processFrame = function (timestamp) {
    runNextFrame = false;
    frame.delta = useDefaultElapsed ? _on_next_frame_js__WEBPACK_IMPORTED_MODULE_0__.defaultTimestep : Math.max(Math.min(timestamp - frame.timestamp, maxElapsed), 1);
    frame.timestamp = timestamp;
    isProcessing = true;
    stepsOrder.forEach(processStep);
    isProcessing = false;
    if (runNextFrame) {
        useDefaultElapsed = false;
        (0,_on_next_frame_js__WEBPACK_IMPORTED_MODULE_0__.onNextFrame)(processFrame);
    }
};
var startLoop = function () {
    runNextFrame = true;
    useDefaultElapsed = true;
    if (!isProcessing) (0,_on_next_frame_js__WEBPACK_IMPORTED_MODULE_0__.onNextFrame)(processFrame);
};
var getFrameData = function () {
    return frame;
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (sync);



/***/ }),

/***/ "./node_modules/framesync/dist/es/on-next-frame.js":
/*!*********************************************************!*\
  !*** ./node_modules/framesync/dist/es/on-next-frame.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "defaultTimestep": () => (/* binding */ defaultTimestep),
/* harmony export */   "onNextFrame": () => (/* binding */ onNextFrame)
/* harmony export */ });
var defaultTimestep = (1 / 60) * 1000;
var getCurrentTime = typeof performance !== "undefined"
    ? function () { return performance.now(); }
    : function () { return Date.now(); };
var onNextFrame = typeof window !== "undefined"
    ? function (callback) {
        return window.requestAnimationFrame(callback);
    }
    : function (callback) {
        return setTimeout(function () { return callback(getCurrentTime()); }, defaultTimestep);
    };




/***/ }),

/***/ "./node_modules/hey-listen/dist/hey-listen.es.js":
/*!*******************************************************!*\
  !*** ./node_modules/hey-listen/dist/hey-listen.es.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "invariant": () => (/* binding */ invariant),
/* harmony export */   "warning": () => (/* binding */ warning)
/* harmony export */ });
var warning = function () { };
var invariant = function () { };
if (true) {
    warning = function (check, message) {
        if (!check && typeof console !== 'undefined') {
            console.warn(message);
        }
    };
    invariant = function (check, message) {
        if (!check) {
            throw new Error(message);
        }
    };
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/generators/decay.js":
/*!***********************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/generators/decay.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "decay": () => (/* binding */ decay)
/* harmony export */ });
function decay(_a) {
    var _b = _a.velocity, velocity = _b === void 0 ? 0 : _b, _c = _a.from, from = _c === void 0 ? 0 : _c, _d = _a.power, power = _d === void 0 ? 0.8 : _d, _e = _a.timeConstant, timeConstant = _e === void 0 ? 350 : _e, _f = _a.restDelta, restDelta = _f === void 0 ? 0.5 : _f, modifyTarget = _a.modifyTarget;
    var state = { done: false, value: from };
    var amplitude = power * velocity;
    var ideal = from + amplitude;
    var target = modifyTarget === undefined ? ideal : modifyTarget(ideal);
    if (target !== ideal)
        amplitude = target - from;
    return {
        next: function (t) {
            var delta = -amplitude * Math.exp(-t / timeConstant);
            state.done = !(delta > restDelta || delta < -restDelta);
            state.value = state.done ? target : target + delta;
            return state;
        },
        flipTarget: function () { },
    };
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/generators/keyframes.js":
/*!***************************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/generators/keyframes.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "convertOffsetToTimes": () => (/* binding */ convertOffsetToTimes),
/* harmony export */   "defaultEasing": () => (/* binding */ defaultEasing),
/* harmony export */   "defaultOffset": () => (/* binding */ defaultOffset),
/* harmony export */   "keyframes": () => (/* binding */ keyframes)
/* harmony export */ });
/* harmony import */ var _utils_interpolate_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/interpolate.js */ "./node_modules/popmotion/dist/es/utils/interpolate.js");
/* harmony import */ var _easing_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../easing/index.js */ "./node_modules/popmotion/dist/es/easing/index.js");



function defaultEasing(values, easing) {
    return values.map(function () { return easing || _easing_index_js__WEBPACK_IMPORTED_MODULE_0__.easeInOut; }).splice(0, values.length - 1);
}
function defaultOffset(values) {
    var numValues = values.length;
    return values.map(function (_value, i) {
        return i !== 0 ? i / (numValues - 1) : 0;
    });
}
function convertOffsetToTimes(offset, duration) {
    return offset.map(function (o) { return o * duration; });
}
function keyframes(_a) {
    var _b = _a.from, from = _b === void 0 ? 0 : _b, _c = _a.to, to = _c === void 0 ? 1 : _c, ease = _a.ease, offset = _a.offset, _d = _a.duration, duration = _d === void 0 ? 300 : _d;
    var state = { done: false, value: from };
    var values = Array.isArray(to) ? to : [from, to];
    var times = convertOffsetToTimes(offset && offset.length === values.length
        ? offset
        : defaultOffset(values), duration);
    function createInterpolator() {
        return (0,_utils_interpolate_js__WEBPACK_IMPORTED_MODULE_1__.interpolate)(times, values, {
            ease: Array.isArray(ease) ? ease : defaultEasing(values, ease),
        });
    }
    var interpolator = createInterpolator();
    return {
        next: function (t) {
            state.value = interpolator(t);
            state.done = t >= duration;
            return state;
        },
        flipTarget: function () {
            values.reverse();
            interpolator = createInterpolator();
        },
    };
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/generators/spring.js":
/*!************************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/generators/spring.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "spring": () => (/* binding */ spring)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_find_spring_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/find-spring.js */ "./node_modules/popmotion/dist/es/animations/utils/find-spring.js");



var durationKeys = ["duration", "bounce"];
var physicsKeys = ["stiffness", "damping", "mass"];
function isSpringType(options, keys) {
    return keys.some(function (key) { return options[key] !== undefined; });
}
function getSpringOptions(options) {
    var springOptions = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({ velocity: 0.0, stiffness: 100, damping: 10, mass: 1.0, isResolvedFromDuration: false }, options);
    if (!isSpringType(options, physicsKeys) &&
        isSpringType(options, durationKeys)) {
        var derived = (0,_utils_find_spring_js__WEBPACK_IMPORTED_MODULE_1__.findSpring)(options);
        springOptions = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({}, springOptions), derived), { velocity: 0.0, mass: 1.0 });
        springOptions.isResolvedFromDuration = true;
    }
    return springOptions;
}
function spring(_a) {
    var _b = _a.from, from = _b === void 0 ? 0.0 : _b, _c = _a.to, to = _c === void 0 ? 1.0 : _c, _d = _a.restSpeed, restSpeed = _d === void 0 ? 2 : _d, restDelta = _a.restDelta, options = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__rest)(_a, ["from", "to", "restSpeed", "restDelta"]);
    var state = { done: false, value: from };
    var _e = getSpringOptions(options), stiffness = _e.stiffness, damping = _e.damping, mass = _e.mass, velocity = _e.velocity, isResolvedFromDuration = _e.isResolvedFromDuration;
    var resolveSpring = zero;
    var resolveVelocity = zero;
    function createSpring() {
        var initialVelocity = velocity ? -(velocity / 1000) : 0.0;
        var initialDelta = to - from;
        var dampingRatio = damping / (2 * Math.sqrt(stiffness * mass));
        var undampedAngularFreq = Math.sqrt(stiffness / mass) / 1000;
        restDelta !== null && restDelta !== void 0 ? restDelta : (restDelta = Math.abs(to - from) <= 1 ? 0.01 : 0.4);
        if (dampingRatio < 1) {
            var angularFreq_1 = (0,_utils_find_spring_js__WEBPACK_IMPORTED_MODULE_1__.calcAngularFreq)(undampedAngularFreq, dampingRatio);
            resolveSpring = function (t) {
                var envelope = Math.exp(-dampingRatio * undampedAngularFreq * t);
                return (to -
                    envelope *
                        (((initialVelocity +
                            dampingRatio * undampedAngularFreq * initialDelta) /
                            angularFreq_1) *
                            Math.sin(angularFreq_1 * t) +
                            initialDelta * Math.cos(angularFreq_1 * t)));
            };
            resolveVelocity = function (t) {
                var envelope = Math.exp(-dampingRatio * undampedAngularFreq * t);
                return (dampingRatio *
                    undampedAngularFreq *
                    envelope *
                    ((Math.sin(angularFreq_1 * t) *
                        (initialVelocity +
                            dampingRatio *
                                undampedAngularFreq *
                                initialDelta)) /
                        angularFreq_1 +
                        initialDelta * Math.cos(angularFreq_1 * t)) -
                    envelope *
                        (Math.cos(angularFreq_1 * t) *
                            (initialVelocity +
                                dampingRatio *
                                    undampedAngularFreq *
                                    initialDelta) -
                            angularFreq_1 *
                                initialDelta *
                                Math.sin(angularFreq_1 * t)));
            };
        }
        else if (dampingRatio === 1) {
            resolveSpring = function (t) {
                return to -
                    Math.exp(-undampedAngularFreq * t) *
                        (initialDelta +
                            (initialVelocity + undampedAngularFreq * initialDelta) *
                                t);
            };
        }
        else {
            var dampedAngularFreq_1 = undampedAngularFreq * Math.sqrt(dampingRatio * dampingRatio - 1);
            resolveSpring = function (t) {
                var envelope = Math.exp(-dampingRatio * undampedAngularFreq * t);
                var freqForT = Math.min(dampedAngularFreq_1 * t, 300);
                return (to -
                    (envelope *
                        ((initialVelocity +
                            dampingRatio * undampedAngularFreq * initialDelta) *
                            Math.sinh(freqForT) +
                            dampedAngularFreq_1 *
                                initialDelta *
                                Math.cosh(freqForT))) /
                        dampedAngularFreq_1);
            };
        }
    }
    createSpring();
    return {
        next: function (t) {
            var current = resolveSpring(t);
            if (!isResolvedFromDuration) {
                var currentVelocity = resolveVelocity(t) * 1000;
                var isBelowVelocityThreshold = Math.abs(currentVelocity) <= restSpeed;
                var isBelowDisplacementThreshold = Math.abs(to - current) <= restDelta;
                state.done =
                    isBelowVelocityThreshold && isBelowDisplacementThreshold;
            }
            else {
                state.done = t >= options.duration;
            }
            state.value = state.done ? to : current;
            return state;
        },
        flipTarget: function () {
            var _a;
            velocity = -velocity;
            _a = [to, from], from = _a[0], to = _a[1];
            createSpring();
        },
    };
}
spring.needsInterpolation = function (a, b) {
    return typeof a === "string" || typeof b === "string";
};
var zero = function (_t) { return 0; };




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/index.js":
/*!************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/index.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "animate": () => (/* binding */ animate)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_detect_animation_from_options_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/detect-animation-from-options.js */ "./node_modules/popmotion/dist/es/animations/utils/detect-animation-from-options.js");
/* harmony import */ var framesync__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! framesync */ "./node_modules/framesync/dist/es/index.js");
/* harmony import */ var _utils_interpolate_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/interpolate.js */ "./node_modules/popmotion/dist/es/utils/interpolate.js");
/* harmony import */ var _utils_elapsed_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/elapsed.js */ "./node_modules/popmotion/dist/es/animations/utils/elapsed.js");






var framesync = function (update) {
    var passTimestamp = function (_a) {
        var delta = _a.delta;
        return update(delta);
    };
    return {
        start: function () { return framesync__WEBPACK_IMPORTED_MODULE_0__["default"].update(passTimestamp, true); },
        stop: function () { return framesync__WEBPACK_IMPORTED_MODULE_0__.cancelSync.update(passTimestamp); },
    };
};
function animate(_a) {
    var _b, _c;
    var from = _a.from, _d = _a.autoplay, autoplay = _d === void 0 ? true : _d, _e = _a.driver, driver = _e === void 0 ? framesync : _e, _f = _a.elapsed, elapsed = _f === void 0 ? 0 : _f, _g = _a.repeat, repeatMax = _g === void 0 ? 0 : _g, _h = _a.repeatType, repeatType = _h === void 0 ? "loop" : _h, _j = _a.repeatDelay, repeatDelay = _j === void 0 ? 0 : _j, onPlay = _a.onPlay, onStop = _a.onStop, onComplete = _a.onComplete, onRepeat = _a.onRepeat, onUpdate = _a.onUpdate, options = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__rest)(_a, ["from", "autoplay", "driver", "elapsed", "repeat", "repeatType", "repeatDelay", "onPlay", "onStop", "onComplete", "onRepeat", "onUpdate"]);
    var to = options.to;
    var driverControls;
    var repeatCount = 0;
    var computedDuration = options.duration;
    var latest;
    var isComplete = false;
    var isForwardPlayback = true;
    var interpolateFromNumber;
    var animator = (0,_utils_detect_animation_from_options_js__WEBPACK_IMPORTED_MODULE_2__.detectAnimationFromOptions)(options);
    if ((_c = (_b = animator).needsInterpolation) === null || _c === void 0 ? void 0 : _c.call(_b, from, to)) {
        interpolateFromNumber = (0,_utils_interpolate_js__WEBPACK_IMPORTED_MODULE_3__.interpolate)([0, 100], [from, to], {
            clamp: false,
        });
        from = 0;
        to = 100;
    }
    var animation = animator((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, options), { from: from, to: to }));
    function repeat() {
        repeatCount++;
        if (repeatType === "reverse") {
            isForwardPlayback = repeatCount % 2 === 0;
            elapsed = (0,_utils_elapsed_js__WEBPACK_IMPORTED_MODULE_4__.reverseElapsed)(elapsed, computedDuration, repeatDelay, isForwardPlayback);
        }
        else {
            elapsed = (0,_utils_elapsed_js__WEBPACK_IMPORTED_MODULE_4__.loopElapsed)(elapsed, computedDuration, repeatDelay);
            if (repeatType === "mirror")
                animation.flipTarget();
        }
        isComplete = false;
        onRepeat && onRepeat();
    }
    function complete() {
        driverControls.stop();
        onComplete && onComplete();
    }
    function update(delta) {
        if (!isForwardPlayback)
            delta = -delta;
        elapsed += delta;
        if (!isComplete) {
            var state = animation.next(Math.max(0, elapsed));
            latest = state.value;
            if (interpolateFromNumber)
                latest = interpolateFromNumber(latest);
            isComplete = isForwardPlayback ? state.done : elapsed <= 0;
        }
        onUpdate === null || onUpdate === void 0 ? void 0 : onUpdate(latest);
        if (isComplete) {
            if (repeatCount === 0)
                computedDuration !== null && computedDuration !== void 0 ? computedDuration : (computedDuration = elapsed);
            if (repeatCount < repeatMax) {
                (0,_utils_elapsed_js__WEBPACK_IMPORTED_MODULE_4__.hasRepeatDelayElapsed)(elapsed, computedDuration, repeatDelay, isForwardPlayback) && repeat();
            }
            else {
                complete();
            }
        }
    }
    function play() {
        onPlay === null || onPlay === void 0 ? void 0 : onPlay();
        driverControls = driver(update);
        driverControls.start();
    }
    autoplay && play();
    return {
        stop: function () {
            onStop === null || onStop === void 0 ? void 0 : onStop();
            driverControls.stop();
        },
    };
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/inertia.js":
/*!**************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/inertia.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "inertia": () => (/* binding */ inertia)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.js */ "./node_modules/popmotion/dist/es/animations/index.js");
/* harmony import */ var _utils_velocity_per_second_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/velocity-per-second.js */ "./node_modules/popmotion/dist/es/utils/velocity-per-second.js");
/* harmony import */ var framesync__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! framesync */ "./node_modules/framesync/dist/es/index.js");





function inertia(_a) {
    var _b = _a.from, from = _b === void 0 ? 0 : _b, _c = _a.velocity, velocity = _c === void 0 ? 0 : _c, min = _a.min, max = _a.max, _d = _a.power, power = _d === void 0 ? 0.8 : _d, _e = _a.timeConstant, timeConstant = _e === void 0 ? 750 : _e, _f = _a.bounceStiffness, bounceStiffness = _f === void 0 ? 500 : _f, _g = _a.bounceDamping, bounceDamping = _g === void 0 ? 10 : _g, _h = _a.restDelta, restDelta = _h === void 0 ? 1 : _h, modifyTarget = _a.modifyTarget, driver = _a.driver, onUpdate = _a.onUpdate, onComplete = _a.onComplete;
    var currentAnimation;
    function isOutOfBounds(v) {
        return (min !== undefined && v < min) || (max !== undefined && v > max);
    }
    function boundaryNearest(v) {
        if (min === undefined)
            return max;
        if (max === undefined)
            return min;
        return Math.abs(min - v) < Math.abs(max - v) ? min : max;
    }
    function startAnimation(options) {
        currentAnimation === null || currentAnimation === void 0 ? void 0 : currentAnimation.stop();
        currentAnimation = (0,_index_js__WEBPACK_IMPORTED_MODULE_1__.animate)((0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)({}, options), { driver: driver, onUpdate: function (v) {
                var _a;
                onUpdate === null || onUpdate === void 0 ? void 0 : onUpdate(v);
                (_a = options.onUpdate) === null || _a === void 0 ? void 0 : _a.call(options, v);
            }, onComplete: onComplete }));
    }
    function startSpring(options) {
        startAnimation((0,tslib__WEBPACK_IMPORTED_MODULE_2__.__assign)({ type: "spring", stiffness: bounceStiffness, damping: bounceDamping, restDelta: restDelta }, options));
    }
    if (isOutOfBounds(from)) {
        startSpring({ from: from, velocity: velocity, to: boundaryNearest(from) });
    }
    else {
        var target = power * velocity + from;
        if (typeof modifyTarget !== "undefined")
            target = modifyTarget(target);
        var boundary_1 = boundaryNearest(target);
        var heading_1 = boundary_1 === min ? -1 : 1;
        var prev_1;
        var current_1;
        var checkBoundary = function (v) {
            prev_1 = current_1;
            current_1 = v;
            velocity = (0,_utils_velocity_per_second_js__WEBPACK_IMPORTED_MODULE_3__.velocityPerSecond)(v - prev_1, (0,framesync__WEBPACK_IMPORTED_MODULE_0__.getFrameData)().delta);
            if ((heading_1 === 1 && v > boundary_1) ||
                (heading_1 === -1 && v < boundary_1)) {
                startSpring({ from: v, to: boundary_1, velocity: velocity });
            }
        };
        startAnimation({
            type: "decay",
            from: from,
            velocity: velocity,
            timeConstant: timeConstant,
            power: power,
            restDelta: restDelta,
            modifyTarget: modifyTarget,
            onUpdate: isOutOfBounds(target) ? checkBoundary : undefined,
        });
    }
    return {
        stop: function () { return currentAnimation === null || currentAnimation === void 0 ? void 0 : currentAnimation.stop(); },
    };
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/utils/detect-animation-from-options.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/utils/detect-animation-from-options.js ***!
  \******************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "detectAnimationFromOptions": () => (/* binding */ detectAnimationFromOptions)
/* harmony export */ });
/* harmony import */ var _generators_spring_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../generators/spring.js */ "./node_modules/popmotion/dist/es/animations/generators/spring.js");
/* harmony import */ var _generators_keyframes_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../generators/keyframes.js */ "./node_modules/popmotion/dist/es/animations/generators/keyframes.js");
/* harmony import */ var _generators_decay_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../generators/decay.js */ "./node_modules/popmotion/dist/es/animations/generators/decay.js");




var types = { keyframes: _generators_keyframes_js__WEBPACK_IMPORTED_MODULE_0__.keyframes, spring: _generators_spring_js__WEBPACK_IMPORTED_MODULE_1__.spring, decay: _generators_decay_js__WEBPACK_IMPORTED_MODULE_2__.decay };
function detectAnimationFromOptions(config) {
    if (Array.isArray(config.to)) {
        return _generators_keyframes_js__WEBPACK_IMPORTED_MODULE_0__.keyframes;
    }
    else if (types[config.type]) {
        return types[config.type];
    }
    var keys = new Set(Object.keys(config));
    if (keys.has("ease") ||
        (keys.has("duration") && !keys.has("dampingRatio"))) {
        return _generators_keyframes_js__WEBPACK_IMPORTED_MODULE_0__.keyframes;
    }
    else if (keys.has("dampingRatio") ||
        keys.has("stiffness") ||
        keys.has("mass") ||
        keys.has("damping") ||
        keys.has("restSpeed") ||
        keys.has("restDelta")) {
        return _generators_spring_js__WEBPACK_IMPORTED_MODULE_1__.spring;
    }
    return _generators_keyframes_js__WEBPACK_IMPORTED_MODULE_0__.keyframes;
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/utils/elapsed.js":
/*!********************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/utils/elapsed.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "hasRepeatDelayElapsed": () => (/* binding */ hasRepeatDelayElapsed),
/* harmony export */   "loopElapsed": () => (/* binding */ loopElapsed),
/* harmony export */   "reverseElapsed": () => (/* binding */ reverseElapsed)
/* harmony export */ });
function loopElapsed(elapsed, duration, delay) {
    if (delay === void 0) { delay = 0; }
    return elapsed - duration - delay;
}
function reverseElapsed(elapsed, duration, delay, isForwardPlayback) {
    if (delay === void 0) { delay = 0; }
    if (isForwardPlayback === void 0) { isForwardPlayback = true; }
    return isForwardPlayback
        ? loopElapsed(duration + -elapsed, duration, delay)
        : duration - (elapsed - duration) + delay;
}
function hasRepeatDelayElapsed(elapsed, duration, delay, isForwardPlayback) {
    return isForwardPlayback ? elapsed >= duration + delay : elapsed <= -delay;
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/animations/utils/find-spring.js":
/*!************************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/animations/utils/find-spring.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "calcAngularFreq": () => (/* binding */ calcAngularFreq),
/* harmony export */   "findSpring": () => (/* binding */ findSpring),
/* harmony export */   "maxDamping": () => (/* binding */ maxDamping),
/* harmony export */   "maxDuration": () => (/* binding */ maxDuration),
/* harmony export */   "minDamping": () => (/* binding */ minDamping),
/* harmony export */   "minDuration": () => (/* binding */ minDuration)
/* harmony export */ });
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");
/* harmony import */ var _utils_clamp_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/clamp.js */ "./node_modules/popmotion/dist/es/utils/clamp.js");



var safeMin = 0.001;
var minDuration = 0.01;
var maxDuration = 10.0;
var minDamping = 0.05;
var maxDamping = 1;
function findSpring(_a) {
    var _b = _a.duration, duration = _b === void 0 ? 800 : _b, _c = _a.bounce, bounce = _c === void 0 ? 0.25 : _c, _d = _a.velocity, velocity = _d === void 0 ? 0 : _d, _e = _a.mass, mass = _e === void 0 ? 1 : _e;
    var envelope;
    var derivative;
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.warning)(duration <= maxDuration * 1000, "Spring duration must be 10 seconds or less");
    var dampingRatio = 1 - bounce;
    dampingRatio = (0,_utils_clamp_js__WEBPACK_IMPORTED_MODULE_1__.clamp)(minDamping, maxDamping, dampingRatio);
    duration = (0,_utils_clamp_js__WEBPACK_IMPORTED_MODULE_1__.clamp)(minDuration, maxDuration, duration / 1000);
    if (dampingRatio < 1) {
        envelope = function (undampedFreq) {
            var exponentialDecay = undampedFreq * dampingRatio;
            var delta = exponentialDecay * duration;
            var a = exponentialDecay - velocity;
            var b = calcAngularFreq(undampedFreq, dampingRatio);
            var c = Math.exp(-delta);
            return safeMin - (a / b) * c;
        };
        derivative = function (undampedFreq) {
            var exponentialDecay = undampedFreq * dampingRatio;
            var delta = exponentialDecay * duration;
            var d = delta * velocity + velocity;
            var e = Math.pow(dampingRatio, 2) * Math.pow(undampedFreq, 2) * duration;
            var f = Math.exp(-delta);
            var g = calcAngularFreq(Math.pow(undampedFreq, 2), dampingRatio);
            var factor = -envelope(undampedFreq) + safeMin > 0 ? -1 : 1;
            return (factor * ((d - e) * f)) / g;
        };
    }
    else {
        envelope = function (undampedFreq) {
            var a = Math.exp(-undampedFreq * duration);
            var b = (undampedFreq - velocity) * duration + 1;
            return -safeMin + a * b;
        };
        derivative = function (undampedFreq) {
            var a = Math.exp(-undampedFreq * duration);
            var b = (velocity - undampedFreq) * (duration * duration);
            return a * b;
        };
    }
    var initialGuess = 5 / duration;
    var undampedFreq = approximateRoot(envelope, derivative, initialGuess);
    if (isNaN(undampedFreq)) {
        return {
            stiffness: 100,
            damping: 10,
        };
    }
    else {
        var stiffness = Math.pow(undampedFreq, 2) * mass;
        return {
            stiffness: stiffness,
            damping: dampingRatio * 2 * Math.sqrt(mass * stiffness),
        };
    }
}
var rootIterations = 12;
function approximateRoot(envelope, derivative, initialGuess) {
    var result = initialGuess;
    for (var i = 1; i < rootIterations; i++) {
        result = result - envelope(result) / derivative(result);
    }
    return result;
}
function calcAngularFreq(undampedFreq, dampingRatio) {
    return undampedFreq * Math.sqrt(1 - dampingRatio * dampingRatio);
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/easing/cubic-bezier.js":
/*!***************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/easing/cubic-bezier.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "cubicBezier": () => (/* binding */ cubicBezier)
/* harmony export */ });
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index.js */ "./node_modules/popmotion/dist/es/easing/index.js");


var a = function (a1, a2) { return 1.0 - 3.0 * a2 + 3.0 * a1; };
var b = function (a1, a2) { return 3.0 * a2 - 6.0 * a1; };
var c = function (a1) { return 3.0 * a1; };
var calcBezier = function (t, a1, a2) {
    return ((a(a1, a2) * t + b(a1, a2)) * t + c(a1)) * t;
};
var getSlope = function (t, a1, a2) {
    return 3.0 * a(a1, a2) * t * t + 2.0 * b(a1, a2) * t + c(a1);
};
var subdivisionPrecision = 0.0000001;
var subdivisionMaxIterations = 10;
function binarySubdivide(aX, aA, aB, mX1, mX2) {
    var currentX;
    var currentT;
    var i = 0;
    do {
        currentT = aA + (aB - aA) / 2.0;
        currentX = calcBezier(currentT, mX1, mX2) - aX;
        if (currentX > 0.0) {
            aB = currentT;
        }
        else {
            aA = currentT;
        }
    } while (Math.abs(currentX) > subdivisionPrecision &&
        ++i < subdivisionMaxIterations);
    return currentT;
}
var newtonIterations = 8;
var newtonMinSlope = 0.001;
function newtonRaphsonIterate(aX, aGuessT, mX1, mX2) {
    for (var i = 0; i < newtonIterations; ++i) {
        var currentSlope = getSlope(aGuessT, mX1, mX2);
        if (currentSlope === 0.0) {
            return aGuessT;
        }
        var currentX = calcBezier(aGuessT, mX1, mX2) - aX;
        aGuessT -= currentX / currentSlope;
    }
    return aGuessT;
}
var kSplineTableSize = 11;
var kSampleStepSize = 1.0 / (kSplineTableSize - 1.0);
function cubicBezier(mX1, mY1, mX2, mY2) {
    if (mX1 === mY1 && mX2 === mY2)
        return _index_js__WEBPACK_IMPORTED_MODULE_0__.linear;
    var sampleValues = new Float32Array(kSplineTableSize);
    for (var i = 0; i < kSplineTableSize; ++i) {
        sampleValues[i] = calcBezier(i * kSampleStepSize, mX1, mX2);
    }
    function getTForX(aX) {
        var intervalStart = 0.0;
        var currentSample = 1;
        var lastSample = kSplineTableSize - 1;
        for (; currentSample !== lastSample && sampleValues[currentSample] <= aX; ++currentSample) {
            intervalStart += kSampleStepSize;
        }
        --currentSample;
        var dist = (aX - sampleValues[currentSample]) /
            (sampleValues[currentSample + 1] - sampleValues[currentSample]);
        var guessForT = intervalStart + dist * kSampleStepSize;
        var initialSlope = getSlope(guessForT, mX1, mX2);
        if (initialSlope >= newtonMinSlope) {
            return newtonRaphsonIterate(aX, guessForT, mX1, mX2);
        }
        else if (initialSlope === 0.0) {
            return guessForT;
        }
        else {
            return binarySubdivide(aX, intervalStart, intervalStart + kSampleStepSize, mX1, mX2);
        }
    }
    return function (t) {
        return t === 0 || t === 1 ? t : calcBezier(getTForX(t), mY1, mY2);
    };
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/easing/index.js":
/*!********************************************************!*\
  !*** ./node_modules/popmotion/dist/es/easing/index.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "anticipate": () => (/* binding */ anticipate),
/* harmony export */   "backIn": () => (/* binding */ backIn),
/* harmony export */   "backInOut": () => (/* binding */ backInOut),
/* harmony export */   "backOut": () => (/* binding */ backOut),
/* harmony export */   "bounceIn": () => (/* binding */ bounceIn),
/* harmony export */   "bounceInOut": () => (/* binding */ bounceInOut),
/* harmony export */   "bounceOut": () => (/* binding */ bounceOut),
/* harmony export */   "circIn": () => (/* binding */ circIn),
/* harmony export */   "circInOut": () => (/* binding */ circInOut),
/* harmony export */   "circOut": () => (/* binding */ circOut),
/* harmony export */   "easeIn": () => (/* binding */ easeIn),
/* harmony export */   "easeInOut": () => (/* binding */ easeInOut),
/* harmony export */   "easeOut": () => (/* binding */ easeOut),
/* harmony export */   "linear": () => (/* binding */ linear)
/* harmony export */ });
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils.js */ "./node_modules/popmotion/dist/es/easing/utils.js");


var DEFAULT_OVERSHOOT_STRENGTH = 1.525;
var BOUNCE_FIRST_THRESHOLD = 4.0 / 11.0;
var BOUNCE_SECOND_THRESHOLD = 8.0 / 11.0;
var BOUNCE_THIRD_THRESHOLD = 9.0 / 10.0;
var linear = function (p) { return p; };
var easeIn = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.createExpoIn)(2);
var easeOut = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.reverseEasing)(easeIn);
var easeInOut = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.mirrorEasing)(easeIn);
var circIn = function (p) { return 1 - Math.sin(Math.acos(p)); };
var circOut = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.reverseEasing)(circIn);
var circInOut = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.mirrorEasing)(circOut);
var backIn = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.createBackIn)(DEFAULT_OVERSHOOT_STRENGTH);
var backOut = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.reverseEasing)(backIn);
var backInOut = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.mirrorEasing)(backIn);
var anticipate = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.createAnticipate)(DEFAULT_OVERSHOOT_STRENGTH);
var ca = 4356.0 / 361.0;
var cb = 35442.0 / 1805.0;
var cc = 16061.0 / 1805.0;
var bounceOut = function (p) {
    if (p === 1 || p === 0)
        return p;
    var p2 = p * p;
    return p < BOUNCE_FIRST_THRESHOLD
        ? 7.5625 * p2
        : p < BOUNCE_SECOND_THRESHOLD
            ? 9.075 * p2 - 9.9 * p + 3.4
            : p < BOUNCE_THIRD_THRESHOLD
                ? ca * p2 - cb * p + cc
                : 10.8 * p * p - 20.52 * p + 10.72;
};
var bounceIn = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.reverseEasing)(bounceOut);
var bounceInOut = function (p) {
    return p < 0.5
        ? 0.5 * (1.0 - bounceOut(1.0 - p * 2.0))
        : 0.5 * bounceOut(p * 2.0 - 1.0) + 0.5;
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/easing/utils.js":
/*!********************************************************!*\
  !*** ./node_modules/popmotion/dist/es/easing/utils.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "createAnticipate": () => (/* binding */ createAnticipate),
/* harmony export */   "createBackIn": () => (/* binding */ createBackIn),
/* harmony export */   "createExpoIn": () => (/* binding */ createExpoIn),
/* harmony export */   "mirrorEasing": () => (/* binding */ mirrorEasing),
/* harmony export */   "reverseEasing": () => (/* binding */ reverseEasing)
/* harmony export */ });
var reverseEasing = function (easing) { return function (p) { return 1 - easing(1 - p); }; };
var mirrorEasing = function (easing) { return function (p) {
    return p <= 0.5 ? easing(2 * p) / 2 : (2 - easing(2 * (1 - p))) / 2;
}; };
var createExpoIn = function (power) { return function (p) { return Math.pow(p, power); }; };
var createBackIn = function (power) { return function (p) {
    return p * p * ((power + 1) * p - power);
}; };
var createAnticipate = function (power) {
    var backEasing = createBackIn(power);
    return function (p) {
        return (p *= 2) < 1
            ? 0.5 * backEasing(p)
            : 0.5 * (2 - Math.pow(2, -10 * (p - 1)));
    };
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/clamp.js":
/*!*******************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/clamp.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "clamp": () => (/* binding */ clamp)
/* harmony export */ });
var clamp = function (min, max, v) {
    return Math.min(Math.max(v, min), max);
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/distance.js":
/*!**********************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/distance.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "distance": () => (/* binding */ distance)
/* harmony export */ });
/* harmony import */ var _is_point_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./is-point.js */ "./node_modules/popmotion/dist/es/utils/is-point.js");
/* harmony import */ var _is_point_3d_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./is-point-3d.js */ "./node_modules/popmotion/dist/es/utils/is-point-3d.js");
/* harmony import */ var _inc_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./inc.js */ "./node_modules/popmotion/dist/es/utils/inc.js");




var distance1D = function (a, b) { return Math.abs(a - b); };
function distance(a, b) {
    if ((0,_inc_js__WEBPACK_IMPORTED_MODULE_0__.isNum)(a) && (0,_inc_js__WEBPACK_IMPORTED_MODULE_0__.isNum)(b)) {
        return distance1D(a, b);
    }
    else if ((0,_is_point_js__WEBPACK_IMPORTED_MODULE_1__.isPoint)(a) && (0,_is_point_js__WEBPACK_IMPORTED_MODULE_1__.isPoint)(b)) {
        var xDelta = distance1D(a.x, b.x);
        var yDelta = distance1D(a.y, b.y);
        var zDelta = (0,_is_point_3d_js__WEBPACK_IMPORTED_MODULE_2__.isPoint3D)(a) && (0,_is_point_3d_js__WEBPACK_IMPORTED_MODULE_2__.isPoint3D)(b) ? distance1D(a.z, b.z) : 0;
        return Math.sqrt(Math.pow(xDelta, 2) + Math.pow(yDelta, 2) + Math.pow(zDelta, 2));
    }
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/inc.js":
/*!*****************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/inc.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isNum": () => (/* binding */ isNum),
/* harmony export */   "zeroPoint": () => (/* binding */ zeroPoint)
/* harmony export */ });
var zeroPoint = {
    x: 0,
    y: 0,
    z: 0
};
var isNum = function (v) { return typeof v === 'number'; };




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/interpolate.js":
/*!*************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/interpolate.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "interpolate": () => (/* binding */ interpolate)
/* harmony export */ });
/* harmony import */ var _progress_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./progress.js */ "./node_modules/popmotion/dist/es/utils/progress.js");
/* harmony import */ var _mix_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./mix.js */ "./node_modules/popmotion/dist/es/utils/mix.js");
/* harmony import */ var _mix_color_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./mix-color.js */ "./node_modules/popmotion/dist/es/utils/mix-color.js");
/* harmony import */ var _mix_complex_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./mix-complex.js */ "./node_modules/popmotion/dist/es/utils/mix-complex.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/color/index.js");
/* harmony import */ var _clamp_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./clamp.js */ "./node_modules/popmotion/dist/es/utils/clamp.js");
/* harmony import */ var _pipe_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./pipe.js */ "./node_modules/popmotion/dist/es/utils/pipe.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");









var mixNumber = function (from, to) { return function (p) { return (0,_mix_js__WEBPACK_IMPORTED_MODULE_1__.mix)(from, to, p); }; };
function detectMixerFactory(v) {
    if (typeof v === 'number') {
        return mixNumber;
    }
    else if (typeof v === 'string') {
        if (style_value_types__WEBPACK_IMPORTED_MODULE_2__.color.test(v)) {
            return _mix_color_js__WEBPACK_IMPORTED_MODULE_3__.mixColor;
        }
        else {
            return _mix_complex_js__WEBPACK_IMPORTED_MODULE_4__.mixComplex;
        }
    }
    else if (Array.isArray(v)) {
        return _mix_complex_js__WEBPACK_IMPORTED_MODULE_4__.mixArray;
    }
    else if (typeof v === 'object') {
        return _mix_complex_js__WEBPACK_IMPORTED_MODULE_4__.mixObject;
    }
}
function createMixers(output, ease, customMixer) {
    var mixers = [];
    var mixerFactory = customMixer || detectMixerFactory(output[0]);
    var numMixers = output.length - 1;
    for (var i = 0; i < numMixers; i++) {
        var mixer = mixerFactory(output[i], output[i + 1]);
        if (ease) {
            var easingFunction = Array.isArray(ease) ? ease[i] : ease;
            mixer = (0,_pipe_js__WEBPACK_IMPORTED_MODULE_5__.pipe)(easingFunction, mixer);
        }
        mixers.push(mixer);
    }
    return mixers;
}
function fastInterpolate(_a, _b) {
    var from = _a[0], to = _a[1];
    var mixer = _b[0];
    return function (v) { return mixer((0,_progress_js__WEBPACK_IMPORTED_MODULE_6__.progress)(from, to, v)); };
}
function slowInterpolate(input, mixers) {
    var inputLength = input.length;
    var lastInputIndex = inputLength - 1;
    return function (v) {
        var mixerIndex = 0;
        var foundMixerIndex = false;
        if (v <= input[0]) {
            foundMixerIndex = true;
        }
        else if (v >= input[lastInputIndex]) {
            mixerIndex = lastInputIndex - 1;
            foundMixerIndex = true;
        }
        if (!foundMixerIndex) {
            var i = 1;
            for (; i < inputLength; i++) {
                if (input[i] > v || i === lastInputIndex) {
                    break;
                }
            }
            mixerIndex = i - 1;
        }
        var progressInRange = (0,_progress_js__WEBPACK_IMPORTED_MODULE_6__.progress)(input[mixerIndex], input[mixerIndex + 1], v);
        return mixers[mixerIndex](progressInRange);
    };
}
function interpolate(input, output, _a) {
    var _b = _a === void 0 ? {} : _a, _c = _b.clamp, isClamp = _c === void 0 ? true : _c, ease = _b.ease, mixer = _b.mixer;
    var inputLength = input.length;
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(inputLength === output.length, 'Both input and output ranges must be the same length');
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(!ease || !Array.isArray(ease) || ease.length === inputLength - 1, 'Array of easing functions must be of length `input.length - 1`, as it applies to the transitions **between** the defined values.');
    if (input[0] > input[inputLength - 1]) {
        input = [].concat(input);
        output = [].concat(output);
        input.reverse();
        output.reverse();
    }
    var mixers = createMixers(output, ease, mixer);
    var interpolator = inputLength === 2
        ? fastInterpolate(input, mixers)
        : slowInterpolate(input, mixers);
    return isClamp
        ? function (v) { return interpolator((0,_clamp_js__WEBPACK_IMPORTED_MODULE_7__.clamp)(input[0], input[inputLength - 1], v)); }
        : interpolator;
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/is-point-3d.js":
/*!*************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/is-point-3d.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isPoint3D": () => (/* binding */ isPoint3D)
/* harmony export */ });
/* harmony import */ var _is_point_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-point.js */ "./node_modules/popmotion/dist/es/utils/is-point.js");


var isPoint3D = function (point) {
    return (0,_is_point_js__WEBPACK_IMPORTED_MODULE_0__.isPoint)(point) && point.hasOwnProperty('z');
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/is-point.js":
/*!**********************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/is-point.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isPoint": () => (/* binding */ isPoint)
/* harmony export */ });
var isPoint = function (point) {
    return point.hasOwnProperty('x') && point.hasOwnProperty('y');
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/mix-color.js":
/*!***********************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/mix-color.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "mixColor": () => (/* binding */ mixColor),
/* harmony export */   "mixLinearColor": () => (/* binding */ mixLinearColor)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _mix_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./mix.js */ "./node_modules/popmotion/dist/es/utils/mix.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/color/hex.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/color/rgba.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/color/hsla.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");





var mixLinearColor = function (from, to, v) {
    var fromExpo = from * from;
    var toExpo = to * to;
    return Math.sqrt(Math.max(0, v * (toExpo - fromExpo) + fromExpo));
};
var colorTypes = [style_value_types__WEBPACK_IMPORTED_MODULE_1__.hex, style_value_types__WEBPACK_IMPORTED_MODULE_2__.rgba, style_value_types__WEBPACK_IMPORTED_MODULE_3__.hsla];
var getColorType = function (v) {
    return colorTypes.find(function (type) { return type.test(v); });
};
var notAnimatable = function (color) {
    return "'" + color + "' is not an animatable color. Use the equivalent color code instead.";
};
var mixColor = function (from, to) {
    var fromColorType = getColorType(from);
    var toColorType = getColorType(to);
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(!!fromColorType, notAnimatable(from));
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(!!toColorType, notAnimatable(to));
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(fromColorType.transform === toColorType.transform, "Both colors must be hex/RGBA, OR both must be HSLA.");
    var fromColor = fromColorType.parse(from);
    var toColor = toColorType.parse(to);
    var blended = (0,tslib__WEBPACK_IMPORTED_MODULE_4__.__assign)({}, fromColor);
    var mixFunc = fromColorType === style_value_types__WEBPACK_IMPORTED_MODULE_3__.hsla ? _mix_js__WEBPACK_IMPORTED_MODULE_5__.mix : mixLinearColor;
    return function (v) {
        for (var key in blended) {
            if (key !== "alpha") {
                blended[key] = mixFunc(fromColor[key], toColor[key], v);
            }
        }
        blended.alpha = (0,_mix_js__WEBPACK_IMPORTED_MODULE_5__.mix)(fromColor.alpha, toColor.alpha, v);
        return fromColorType.transform(blended);
    };
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/mix-complex.js":
/*!*************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/mix-complex.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "mixArray": () => (/* binding */ mixArray),
/* harmony export */   "mixComplex": () => (/* binding */ mixComplex),
/* harmony export */   "mixObject": () => (/* binding */ mixObject)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/color/index.js");
/* harmony import */ var style_value_types__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! style-value-types */ "./node_modules/style-value-types/dist/es/complex/index.js");
/* harmony import */ var _mix_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./mix.js */ "./node_modules/popmotion/dist/es/utils/mix.js");
/* harmony import */ var _mix_color_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./mix-color.js */ "./node_modules/popmotion/dist/es/utils/mix-color.js");
/* harmony import */ var _inc_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./inc.js */ "./node_modules/popmotion/dist/es/utils/inc.js");
/* harmony import */ var _pipe_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./pipe.js */ "./node_modules/popmotion/dist/es/utils/pipe.js");
/* harmony import */ var hey_listen__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hey-listen */ "./node_modules/hey-listen/dist/hey-listen.es.js");








function getMixer(origin, target) {
    if ((0,_inc_js__WEBPACK_IMPORTED_MODULE_1__.isNum)(origin)) {
        return function (v) { return (0,_mix_js__WEBPACK_IMPORTED_MODULE_2__.mix)(origin, target, v); };
    }
    else if (style_value_types__WEBPACK_IMPORTED_MODULE_3__.color.test(origin)) {
        return (0,_mix_color_js__WEBPACK_IMPORTED_MODULE_4__.mixColor)(origin, target);
    }
    else {
        return mixComplex(origin, target);
    }
}
var mixArray = function (from, to) {
    var output = (0,tslib__WEBPACK_IMPORTED_MODULE_5__.__spreadArrays)(from);
    var numValues = output.length;
    var blendValue = from.map(function (fromThis, i) { return getMixer(fromThis, to[i]); });
    return function (v) {
        for (var i = 0; i < numValues; i++) {
            output[i] = blendValue[i](v);
        }
        return output;
    };
};
var mixObject = function (origin, target) {
    var output = (0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_5__.__assign)({}, origin), target);
    var blendValue = {};
    for (var key in output) {
        if (origin[key] !== undefined && target[key] !== undefined) {
            blendValue[key] = getMixer(origin[key], target[key]);
        }
    }
    return function (v) {
        for (var key in blendValue) {
            output[key] = blendValue[key](v);
        }
        return output;
    };
};
function analyse(value) {
    var parsed = style_value_types__WEBPACK_IMPORTED_MODULE_6__.complex.parse(value);
    var numValues = parsed.length;
    var numNumbers = 0;
    var numRGB = 0;
    var numHSL = 0;
    for (var i = 0; i < numValues; i++) {
        if (numNumbers || typeof parsed[i] === "number") {
            numNumbers++;
        }
        else {
            if (parsed[i].hue !== undefined) {
                numHSL++;
            }
            else {
                numRGB++;
            }
        }
    }
    return { parsed: parsed, numNumbers: numNumbers, numRGB: numRGB, numHSL: numHSL };
}
var mixComplex = function (origin, target) {
    var template = style_value_types__WEBPACK_IMPORTED_MODULE_6__.complex.createTransformer(target);
    var originStats = analyse(origin);
    var targetStats = analyse(target);
    (0,hey_listen__WEBPACK_IMPORTED_MODULE_0__.invariant)(originStats.numHSL === targetStats.numHSL &&
        originStats.numRGB === targetStats.numRGB &&
        originStats.numNumbers >= targetStats.numNumbers, "Complex values '" + origin + "' and '" + target + "' too different to mix. Ensure all colors are of the same type.");
    return (0,_pipe_js__WEBPACK_IMPORTED_MODULE_7__.pipe)(mixArray(originStats.parsed, targetStats.parsed), template);
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/mix.js":
/*!*****************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/mix.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "mix": () => (/* binding */ mix)
/* harmony export */ });
var mix = function (from, to, progress) {
    return -progress * from + progress * to + from;
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/pipe.js":
/*!******************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/pipe.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "pipe": () => (/* binding */ pipe)
/* harmony export */ });
var combineFunctions = function (a, b) { return function (v) { return b(a(v)); }; };
var pipe = function () {
    var transformers = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        transformers[_i] = arguments[_i];
    }
    return transformers.reduce(combineFunctions);
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/progress.js":
/*!**********************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/progress.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "progress": () => (/* binding */ progress)
/* harmony export */ });
var progress = function (from, to, value) {
    var toFromDifference = to - from;
    return toFromDifference === 0 ? 1 : (value - from) / toFromDifference;
};




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/velocity-per-second.js":
/*!*********************************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/velocity-per-second.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "velocityPerSecond": () => (/* binding */ velocityPerSecond)
/* harmony export */ });
function velocityPerSecond(velocity, frameDuration) {
    return frameDuration ? velocity * (1000 / frameDuration) : 0;
}




/***/ }),

/***/ "./node_modules/popmotion/dist/es/utils/wrap.js":
/*!******************************************************!*\
  !*** ./node_modules/popmotion/dist/es/utils/wrap.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "wrap": () => (/* binding */ wrap)
/* harmony export */ });
var wrap = function (min, max, v) {
    var rangeSize = max - min;
    return ((((v - min) % rangeSize) + rangeSize) % rangeSize) + min;
};




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/color/hex.js":
/*!*************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/color/hex.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "hex": () => (/* binding */ hex)
/* harmony export */ });
/* harmony import */ var _rgba_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./rgba.js */ "./node_modules/style-value-types/dist/es/color/rgba.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils.js */ "./node_modules/style-value-types/dist/es/color/utils.js");



function parseHex(v) {
    var r = '';
    var g = '';
    var b = '';
    var a = '';
    if (v.length > 5) {
        r = v.substr(1, 2);
        g = v.substr(3, 2);
        b = v.substr(5, 2);
        a = v.substr(7, 2);
    }
    else {
        r = v.substr(1, 1);
        g = v.substr(2, 1);
        b = v.substr(3, 1);
        a = v.substr(4, 1);
        r += r;
        g += g;
        b += b;
        a += a;
    }
    return {
        red: parseInt(r, 16),
        green: parseInt(g, 16),
        blue: parseInt(b, 16),
        alpha: a ? parseInt(a, 16) / 255 : 1,
    };
}
var hex = {
    test: (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.isColorString)('#'),
    parse: parseHex,
    transform: _rgba_js__WEBPACK_IMPORTED_MODULE_1__.rgba.transform,
};




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/color/hsla.js":
/*!**************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/color/hsla.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "hsla": () => (/* binding */ hsla)
/* harmony export */ });
/* harmony import */ var _numbers_index_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../numbers/index.js */ "./node_modules/style-value-types/dist/es/numbers/index.js");
/* harmony import */ var _numbers_units_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../numbers/units.js */ "./node_modules/style-value-types/dist/es/numbers/units.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils.js */ "./node_modules/style-value-types/dist/es/color/utils.js");





var hsla = {
    test: (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.isColorString)('hsl', 'hue'),
    parse: (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.splitColor)('hue', 'saturation', 'lightness'),
    transform: function (_a) {
        var hue = _a.hue, saturation = _a.saturation, lightness = _a.lightness, _b = _a.alpha, alpha$1 = _b === void 0 ? 1 : _b;
        return ('hsla(' +
            Math.round(hue) +
            ', ' +
            _numbers_units_js__WEBPACK_IMPORTED_MODULE_1__.percent.transform((0,_utils_js__WEBPACK_IMPORTED_MODULE_2__.sanitize)(saturation)) +
            ', ' +
            _numbers_units_js__WEBPACK_IMPORTED_MODULE_1__.percent.transform((0,_utils_js__WEBPACK_IMPORTED_MODULE_2__.sanitize)(lightness)) +
            ', ' +
            (0,_utils_js__WEBPACK_IMPORTED_MODULE_2__.sanitize)(_numbers_index_js__WEBPACK_IMPORTED_MODULE_3__.alpha.transform(alpha$1)) +
            ')');
    },
};




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/color/index.js":
/*!***************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/color/index.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "color": () => (/* binding */ color)
/* harmony export */ });
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");
/* harmony import */ var _hex_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hex.js */ "./node_modules/style-value-types/dist/es/color/hex.js");
/* harmony import */ var _hsla_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hsla.js */ "./node_modules/style-value-types/dist/es/color/hsla.js");
/* harmony import */ var _rgba_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./rgba.js */ "./node_modules/style-value-types/dist/es/color/rgba.js");





var color = {
    test: function (v) { return _rgba_js__WEBPACK_IMPORTED_MODULE_0__.rgba.test(v) || _hex_js__WEBPACK_IMPORTED_MODULE_1__.hex.test(v) || _hsla_js__WEBPACK_IMPORTED_MODULE_2__.hsla.test(v); },
    parse: function (v) {
        if (_rgba_js__WEBPACK_IMPORTED_MODULE_0__.rgba.test(v)) {
            return _rgba_js__WEBPACK_IMPORTED_MODULE_0__.rgba.parse(v);
        }
        else if (_hsla_js__WEBPACK_IMPORTED_MODULE_2__.hsla.test(v)) {
            return _hsla_js__WEBPACK_IMPORTED_MODULE_2__.hsla.parse(v);
        }
        else {
            return _hex_js__WEBPACK_IMPORTED_MODULE_1__.hex.parse(v);
        }
    },
    transform: function (v) {
        return (0,_utils_js__WEBPACK_IMPORTED_MODULE_3__.isString)(v)
            ? v
            : v.hasOwnProperty('red')
                ? _rgba_js__WEBPACK_IMPORTED_MODULE_0__.rgba.transform(v)
                : _hsla_js__WEBPACK_IMPORTED_MODULE_2__.hsla.transform(v);
    },
};




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/color/rgba.js":
/*!**************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/color/rgba.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "rgbUnit": () => (/* binding */ rgbUnit),
/* harmony export */   "rgba": () => (/* binding */ rgba)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _numbers_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../numbers/index.js */ "./node_modules/style-value-types/dist/es/numbers/index.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils.js */ "./node_modules/style-value-types/dist/es/color/utils.js");





var clampRgbUnit = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.clamp)(0, 255);
var rgbUnit = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, _numbers_index_js__WEBPACK_IMPORTED_MODULE_2__.number), { transform: function (v) { return Math.round(clampRgbUnit(v)); } });
var rgba = {
    test: (0,_utils_js__WEBPACK_IMPORTED_MODULE_3__.isColorString)('rgb', 'red'),
    parse: (0,_utils_js__WEBPACK_IMPORTED_MODULE_3__.splitColor)('red', 'green', 'blue'),
    transform: function (_a) {
        var red = _a.red, green = _a.green, blue = _a.blue, _b = _a.alpha, alpha$1 = _b === void 0 ? 1 : _b;
        return 'rgba(' +
            rgbUnit.transform(red) +
            ', ' +
            rgbUnit.transform(green) +
            ', ' +
            rgbUnit.transform(blue) +
            ', ' +
            (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.sanitize)(_numbers_index_js__WEBPACK_IMPORTED_MODULE_2__.alpha.transform(alpha$1)) +
            ')';
    },
};




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/color/utils.js":
/*!***************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/color/utils.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isColorString": () => (/* binding */ isColorString),
/* harmony export */   "splitColor": () => (/* binding */ splitColor)
/* harmony export */ });
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");


var isColorString = function (type, testProp) { return function (v) {
    return (((0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.isString)(v) && _utils_js__WEBPACK_IMPORTED_MODULE_0__.singleColorRegex.test(v) && v.startsWith(type)) ||
        (testProp && Object.prototype.hasOwnProperty.call(v, testProp)));
}; };
var splitColor = function (aName, bName, cName) { return function (v) {
    var _a;
    if (!(0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.isString)(v))
        return v;
    var _b = v.match(_utils_js__WEBPACK_IMPORTED_MODULE_0__.floatRegex), a = _b[0], b = _b[1], c = _b[2], alpha = _b[3];
    return _a = {},
        _a[aName] = parseFloat(a),
        _a[bName] = parseFloat(b),
        _a[cName] = parseFloat(c),
        _a.alpha = alpha !== undefined ? parseFloat(alpha) : 1,
        _a;
}; };




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/complex/filter.js":
/*!******************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/complex/filter.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "filter": () => (/* binding */ filter)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./index.js */ "./node_modules/style-value-types/dist/es/complex/index.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");




var maxDefaults = new Set(['brightness', 'contrast', 'saturate', 'opacity']);
function applyDefaultFilter(v) {
    var _a = v.slice(0, -1).split('('), name = _a[0], value = _a[1];
    if (name === 'drop-shadow')
        return v;
    var number = (value.match(_utils_js__WEBPACK_IMPORTED_MODULE_0__.floatRegex) || [])[0];
    if (!number)
        return v;
    var unit = value.replace(number, '');
    var defaultValue = maxDefaults.has(name) ? 1 : 0;
    if (number !== value)
        defaultValue *= 100;
    return name + '(' + defaultValue + unit + ')';
}
var functionRegex = /([a-z-]*)\(.*?\)/g;
var filter = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, _index_js__WEBPACK_IMPORTED_MODULE_2__.complex), { getAnimatableNone: function (v) {
        var functions = v.match(functionRegex);
        return functions ? functions.map(applyDefaultFilter).join(' ') : v;
    } });




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/complex/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/complex/index.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "complex": () => (/* binding */ complex)
/* harmony export */ });
/* harmony import */ var _color_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../color/index.js */ "./node_modules/style-value-types/dist/es/color/index.js");
/* harmony import */ var _numbers_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../numbers/index.js */ "./node_modules/style-value-types/dist/es/numbers/index.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");




var colorToken = '${c}';
var numberToken = '${n}';
function test(v) {
    var _a, _b, _c, _d;
    return (isNaN(v) &&
        (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.isString)(v) &&
        ((_b = (_a = v.match(_utils_js__WEBPACK_IMPORTED_MODULE_0__.floatRegex)) === null || _a === void 0 ? void 0 : _a.length) !== null && _b !== void 0 ? _b : 0) + ((_d = (_c = v.match(_utils_js__WEBPACK_IMPORTED_MODULE_0__.colorRegex)) === null || _c === void 0 ? void 0 : _c.length) !== null && _d !== void 0 ? _d : 0) > 0);
}
function analyse(v) {
    var values = [];
    var numColors = 0;
    var colors = v.match(_utils_js__WEBPACK_IMPORTED_MODULE_0__.colorRegex);
    if (colors) {
        numColors = colors.length;
        v = v.replace(_utils_js__WEBPACK_IMPORTED_MODULE_0__.colorRegex, colorToken);
        values.push.apply(values, colors.map(_color_index_js__WEBPACK_IMPORTED_MODULE_1__.color.parse));
    }
    var numbers = v.match(_utils_js__WEBPACK_IMPORTED_MODULE_0__.floatRegex);
    if (numbers) {
        v = v.replace(_utils_js__WEBPACK_IMPORTED_MODULE_0__.floatRegex, numberToken);
        values.push.apply(values, numbers.map(_numbers_index_js__WEBPACK_IMPORTED_MODULE_2__.number.parse));
    }
    return { values: values, numColors: numColors, tokenised: v };
}
function parse(v) {
    return analyse(v).values;
}
function createTransformer(v) {
    var _a = analyse(v), values = _a.values, numColors = _a.numColors, tokenised = _a.tokenised;
    var numValues = values.length;
    return function (v) {
        var output = tokenised;
        for (var i = 0; i < numValues; i++) {
            output = output.replace(i < numColors ? colorToken : numberToken, i < numColors ? _color_index_js__WEBPACK_IMPORTED_MODULE_1__.color.transform(v[i]) : (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.sanitize)(v[i]));
        }
        return output;
    };
}
var convertNumbersToZero = function (v) {
    return typeof v === 'number' ? 0 : v;
};
function getAnimatableNone(v) {
    var parsed = parse(v);
    var transformer = createTransformer(v);
    return transformer(parsed.map(convertNumbersToZero));
}
var complex = { test: test, parse: parse, createTransformer: createTransformer, getAnimatableNone: getAnimatableNone };




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/numbers/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/numbers/index.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "alpha": () => (/* binding */ alpha),
/* harmony export */   "number": () => (/* binding */ number),
/* harmony export */   "scale": () => (/* binding */ scale)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");



var number = {
    test: function (v) { return typeof v === 'number'; },
    parse: parseFloat,
    transform: function (v) { return v; },
};
var alpha = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({}, number), { transform: (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.clamp)(0, 1) });
var scale = (0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_0__.__assign)({}, number), { default: 1 });




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/numbers/units.js":
/*!*****************************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/numbers/units.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "degrees": () => (/* binding */ degrees),
/* harmony export */   "percent": () => (/* binding */ percent),
/* harmony export */   "progressPercentage": () => (/* binding */ progressPercentage),
/* harmony export */   "px": () => (/* binding */ px),
/* harmony export */   "vh": () => (/* binding */ vh),
/* harmony export */   "vw": () => (/* binding */ vw)
/* harmony export */ });
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tslib */ "./node_modules/tslib/tslib.es6.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils.js */ "./node_modules/style-value-types/dist/es/utils.js");



var createUnitType = function (unit) { return ({
    test: function (v) {
        return (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.isString)(v) && v.endsWith(unit) && v.split(' ').length === 1;
    },
    parse: parseFloat,
    transform: function (v) { return "" + v + unit; },
}); };
var degrees = createUnitType('deg');
var percent = createUnitType('%');
var px = createUnitType('px');
var vh = createUnitType('vh');
var vw = createUnitType('vw');
var progressPercentage = (0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)((0,tslib__WEBPACK_IMPORTED_MODULE_1__.__assign)({}, percent), { parse: function (v) { return percent.parse(v) / 100; }, transform: function (v) { return percent.transform(v * 100); } });




/***/ }),

/***/ "./node_modules/style-value-types/dist/es/utils.js":
/*!*********************************************************!*\
  !*** ./node_modules/style-value-types/dist/es/utils.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "clamp": () => (/* binding */ clamp),
/* harmony export */   "colorRegex": () => (/* binding */ colorRegex),
/* harmony export */   "floatRegex": () => (/* binding */ floatRegex),
/* harmony export */   "isString": () => (/* binding */ isString),
/* harmony export */   "sanitize": () => (/* binding */ sanitize),
/* harmony export */   "singleColorRegex": () => (/* binding */ singleColorRegex)
/* harmony export */ });
var clamp = function (min, max) { return function (v) {
    return Math.max(Math.min(v, max), min);
}; };
var sanitize = function (v) { return (v % 1 ? Number(v.toFixed(5)) : v); };
var floatRegex = /(-)?([\d]*\.?[\d])+/g;
var colorRegex = /(#[0-9a-f]{6}|#[0-9a-f]{3}|#(?:[0-9a-f]{2}){2,4}|(rgb|hsl)a?\((-?[\d\.]+%?[,\s]+){2,3}\s*\/*\s*[\d\.]+%?\))/gi;
var singleColorRegex = /^(#[0-9a-f]{3}|#(?:[0-9a-f]{2}){2,4}|(rgb|hsl)a?\((-?[\d\.]+%?[,\s]+){2,3}\s*\/*\s*[\d\.]+%?\))$/i;
function isString(v) {
    return typeof v === 'string';
}




/***/ }),

/***/ "./node_modules/tslib/tslib.es6.js":
/*!*****************************************!*\
  !*** ./node_modules/tslib/tslib.es6.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "__assign": () => (/* binding */ __assign),
/* harmony export */   "__asyncDelegator": () => (/* binding */ __asyncDelegator),
/* harmony export */   "__asyncGenerator": () => (/* binding */ __asyncGenerator),
/* harmony export */   "__asyncValues": () => (/* binding */ __asyncValues),
/* harmony export */   "__await": () => (/* binding */ __await),
/* harmony export */   "__awaiter": () => (/* binding */ __awaiter),
/* harmony export */   "__classPrivateFieldGet": () => (/* binding */ __classPrivateFieldGet),
/* harmony export */   "__classPrivateFieldSet": () => (/* binding */ __classPrivateFieldSet),
/* harmony export */   "__createBinding": () => (/* binding */ __createBinding),
/* harmony export */   "__decorate": () => (/* binding */ __decorate),
/* harmony export */   "__exportStar": () => (/* binding */ __exportStar),
/* harmony export */   "__extends": () => (/* binding */ __extends),
/* harmony export */   "__generator": () => (/* binding */ __generator),
/* harmony export */   "__importDefault": () => (/* binding */ __importDefault),
/* harmony export */   "__importStar": () => (/* binding */ __importStar),
/* harmony export */   "__makeTemplateObject": () => (/* binding */ __makeTemplateObject),
/* harmony export */   "__metadata": () => (/* binding */ __metadata),
/* harmony export */   "__param": () => (/* binding */ __param),
/* harmony export */   "__read": () => (/* binding */ __read),
/* harmony export */   "__rest": () => (/* binding */ __rest),
/* harmony export */   "__spread": () => (/* binding */ __spread),
/* harmony export */   "__spreadArrays": () => (/* binding */ __spreadArrays),
/* harmony export */   "__values": () => (/* binding */ __values)
/* harmony export */ });
/*! *****************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
/* global Reflect, Promise */

var extendStatics = function(d, b) {
    extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return extendStatics(d, b);
};

function __extends(d, b) {
    extendStatics(d, b);
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var __assign = function() {
    __assign = Object.assign || function __assign(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
        }
        return t;
    }
    return __assign.apply(this, arguments);
}

function __rest(s, e) {
    var t = {};
    for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
        t[p] = s[p];
    if (s != null && typeof Object.getOwnPropertySymbols === "function")
        for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
            if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
                t[p[i]] = s[p[i]];
        }
    return t;
}

function __decorate(decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
}

function __param(paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
}

function __metadata(metadataKey, metadataValue) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
}

function __generator(thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
}

function __createBinding(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}

function __exportStar(m, exports) {
    for (var p in m) if (p !== "default" && !exports.hasOwnProperty(p)) exports[p] = m[p];
}

function __values(o) {
    var s = typeof Symbol === "function" && Symbol.iterator, m = s && o[s], i = 0;
    if (m) return m.call(o);
    if (o && typeof o.length === "number") return {
        next: function () {
            if (o && i >= o.length) o = void 0;
            return { value: o && o[i++], done: !o };
        }
    };
    throw new TypeError(s ? "Object is not iterable." : "Symbol.iterator is not defined.");
}

function __read(o, n) {
    var m = typeof Symbol === "function" && o[Symbol.iterator];
    if (!m) return o;
    var i = m.call(o), r, ar = [], e;
    try {
        while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
    }
    catch (error) { e = { error: error }; }
    finally {
        try {
            if (r && !r.done && (m = i["return"])) m.call(i);
        }
        finally { if (e) throw e.error; }
    }
    return ar;
}

function __spread() {
    for (var ar = [], i = 0; i < arguments.length; i++)
        ar = ar.concat(__read(arguments[i]));
    return ar;
}

function __spreadArrays() {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};

function __await(v) {
    return this instanceof __await ? (this.v = v, this) : new __await(v);
}

function __asyncGenerator(thisArg, _arguments, generator) {
    if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
    var g = generator.apply(thisArg, _arguments || []), i, q = [];
    return i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i;
    function verb(n) { if (g[n]) i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; }
    function resume(n, v) { try { step(g[n](v)); } catch (e) { settle(q[0][3], e); } }
    function step(r) { r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r); }
    function fulfill(value) { resume("next", value); }
    function reject(value) { resume("throw", value); }
    function settle(f, v) { if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]); }
}

function __asyncDelegator(o) {
    var i, p;
    return i = {}, verb("next"), verb("throw", function (e) { throw e; }), verb("return"), i[Symbol.iterator] = function () { return this; }, i;
    function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: n === "return" } : f ? f(v) : v; } : f; }
}

function __asyncValues(o) {
    if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
    var m = o[Symbol.asyncIterator], i;
    return m ? m.call(o) : (o = typeof __values === "function" ? __values(o) : o[Symbol.iterator](), i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i);
    function verb(n) { i[n] = o[n] && function (v) { return new Promise(function (resolve, reject) { v = o[n](v), settle(resolve, reject, v.done, v.value); }); }; }
    function settle(resolve, reject, d, v) { Promise.resolve(v).then(function(v) { resolve({ value: v, done: d }); }, reject); }
}

function __makeTemplateObject(cooked, raw) {
    if (Object.defineProperty) { Object.defineProperty(cooked, "raw", { value: raw }); } else { cooked.raw = raw; }
    return cooked;
};

function __importStar(mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (Object.hasOwnProperty.call(mod, k)) result[k] = mod[k];
    result.default = mod;
    return result;
}

function __importDefault(mod) {
    return (mod && mod.__esModule) ? mod : { default: mod };
}

function __classPrivateFieldGet(receiver, privateMap) {
    if (!privateMap.has(receiver)) {
        throw new TypeError("attempted to get private field on non-instance");
    }
    return privateMap.get(receiver);
}

function __classPrivateFieldSet(receiver, privateMap, value) {
    if (!privateMap.has(receiver)) {
        throw new TypeError("attempted to set private field on non-instance");
    }
    privateMap.set(receiver, value);
    return value;
}


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = React;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**************************************!*\
  !*** ./src/vendors/framer-motion.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vendor_framer_motion__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vendor-framer-motion */ "./node_modules/framer-motion/dist/es/index.js");

window.FramerMotion = window.FramerMotion || vendor_framer_motion__WEBPACK_IMPORTED_MODULE_0__;
})();

/******/ })()
;
//# sourceMappingURL=vendor-framer-motion.bundle.js.map