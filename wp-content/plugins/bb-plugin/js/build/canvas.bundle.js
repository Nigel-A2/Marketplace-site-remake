/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/canvas/api/index.js":
/*!*********************************!*\
  !*** ./src/canvas/api/index.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getActions": () => (/* binding */ getActions),
/* harmony export */   "getConfig": () => (/* binding */ getConfig)
/* harmony export */ });
/* harmony import */ var _nodes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./nodes */ "./src/canvas/api/nodes.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }


var getConfig = function getConfig() {
  return window.FLBuilderCanvasConfig;
};
var getActions = function getActions() {
  return _objectSpread({}, _nodes__WEBPACK_IMPORTED_MODULE_0__);
};

/***/ }),

/***/ "./src/canvas/api/nodes.js":
/*!*********************************!*\
  !*** ./src/canvas/api/nodes.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "moveNode": () => (/* binding */ moveNode)
/* harmony export */ });
/* harmony import */ var _dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../dom */ "./src/canvas/dom/index.js");

var moveNode = function moveNode(id) {
  var position = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  var parent = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
  var nodeElement = (0,_dom__WEBPACK_IMPORTED_MODULE_0__.getNodeElement)(id);
  var parentElement = null;
  var contentElement = null;
  var isColumnGroup = false;
  var previousParentElement = nodeElement.parentElement.closest('[data-node]'); // Move within the same parent

  if (!parent) {
    parentElement = nodeElement.parentElement;
    contentElement = parentElement;
  } // Move to a different parent


  if (parent) {
    parentElement = (0,_dom__WEBPACK_IMPORTED_MODULE_0__.getNodeElement)(parent);
    contentElement = parentElement.querySelector('.fl-node-content');
    isColumnGroup = parentElement.classList.contains('fl-col-group');

    if (isColumnGroup) {
      contentElement = parentElement;
    }
  } // Only move if the element isn't already in position


  if (nodeElement !== contentElement.children[position]) {
    nodeElement.remove();

    if (position > contentElement.children.length - 1) {
      contentElement.appendChild(nodeElement);
    } else {
      contentElement.insertBefore(nodeElement, contentElement.children[position]);
    } // Reset col widths when reparenting to a new column group


    if (isColumnGroup && parent) {
      FLBuilder._resetColumnWidths(parentElement);

      FLBuilder._resetColumnWidths(previousParentElement);
    }
  }

  FLBuilder._highlightEmptyCols();
};

/***/ }),

/***/ "./src/canvas/dom/index.js":
/*!*********************************!*\
  !*** ./src/canvas/dom/index.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getLayoutRoot": () => (/* binding */ getLayoutRoot),
/* harmony export */   "getNodeElement": () => (/* binding */ getNodeElement),
/* harmony export */   "scrollToNode": () => (/* binding */ scrollToNode)
/* harmony export */ });
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../api */ "./src/canvas/api/index.js");

/**
 * Get the root layout element.
 *
 * @param string postId
 * @return HTMLElement | null
 */

var getLayoutRoot = function getLayoutRoot(postId) {
  if (!postId) {
    return null;
  }

  return document.querySelector(".fl-builder-content-".concat(postId));
};
/**
 * Get a reference to a node's dom element from an id
 *
 * @param string id
 * @return HTMLElement | null
 */

var getNodeElement = function getNodeElement(id) {
  var _getConfig = (0,_api__WEBPACK_IMPORTED_MODULE_0__.getConfig)(),
      postId = _getConfig.postId;

  var root = getLayoutRoot(postId);

  if (!root) {
    return null;
  }

  return root.querySelector("[data-node=\"".concat(id, "\"]"));
};
/**
 * Scroll the root element of a particular node onto screen if it is not.
 *
 * @param string id
 * @return void
 */

var scrollToNode = function scrollToNode(id) {
  var el = getNodeElement(id);

  if (el) {
    el.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
  }
};

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
/*!*****************************!*\
  !*** ./src/canvas/index.js ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./api */ "./src/canvas/api/index.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

 // Setup public API - window.FL.Builder.__canvas

var api = window.FL || {};
var existing = api.Builder || {};

var Builder = _objectSpread(_objectSpread({}, existing), {}, {
  /**
   * Canvas API is what will ultimately be the FL.Builder public API __INSIDE__ the iframe canvas.
   */
  __canvas: _objectSpread({}, _api__WEBPACK_IMPORTED_MODULE_0__)
});

window.FL = _objectSpread(_objectSpread({}, api), {}, {
  Builder: Builder
});
})();

/******/ })()
;
//# sourceMappingURL=canvas.bundle.js.map