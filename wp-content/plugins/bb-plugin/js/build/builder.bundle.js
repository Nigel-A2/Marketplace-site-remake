/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/builder/api/index.js":
/*!**********************************!*\
  !*** ./src/builder/api/index.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getActions": () => (/* binding */ getActions),
/* harmony export */   "getConfig": () => (/* binding */ getConfig),
/* harmony export */   "getStrings": () => (/* binding */ getStrings)
/* harmony export */ });
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");
/* harmony import */ var _nodes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./nodes */ "./src/builder/api/nodes.js");
/* harmony import */ var _settings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./settings */ "./src/builder/api/settings.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }




var getActions = function getActions() {
  var _getSystemActions = (0,data__WEBPACK_IMPORTED_MODULE_0__.getSystemActions)(),
      registerPanel = _getSystemActions.registerPanel,
      displayPanel = _getSystemActions.displayPanel,
      togglePanel = _getSystemActions.togglePanel;
  /**
   * Being very selective about what we expose via the public API here.
   */


  var systemAPI = {
    registerPanel: registerPanel,
    displayPanel: displayPanel,
    togglePanel: togglePanel
  };
  return _objectSpread(_objectSpread(_objectSpread({}, systemAPI), _nodes__WEBPACK_IMPORTED_MODULE_1__), _settings__WEBPACK_IMPORTED_MODULE_2__);
};
var getConfig = function getConfig() {
  return window.FLBuilderConfig;
};
var getStrings = function getStrings() {
  return window.FLBuilderStrings;
};

/***/ }),

/***/ "./src/builder/api/nodes.js":
/*!**********************************!*\
  !*** ./src/builder/api/nodes.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "copyNode": () => (/* binding */ copyNode),
/* harmony export */   "deleteNode": () => (/* binding */ deleteNode),
/* harmony export */   "moveNode": () => (/* binding */ moveNode),
/* harmony export */   "scrollToNode": () => (/* binding */ scrollToNode)
/* harmony export */ });
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");


var getDeleteConfirmationMessage = function getDeleteConfirmationMessage(type) {
  var _window$FLBuilderStri = window.FLBuilderStrings,
      deleteRowMessage = _window$FLBuilderStri.deleteRowMessage,
      deleteColumnMessage = _window$FLBuilderStri.deleteColumnMessage,
      deleteModuleMessage = _window$FLBuilderStri.deleteModuleMessage;

  switch (type) {
    case 'row':
      return deleteRowMessage;

    case 'column':
      return deleteColumnMessage;

    default:
      return deleteModuleMessage;
  }
};
/**
 * Handles deleting any type of node and gets confirmation if needed
 */


var deleteNode = function deleteNode(id) {
  var _getLayoutActions = (0,data__WEBPACK_IMPORTED_MODULE_0__.getLayoutActions)(),
      deleteNode = _getLayoutActions.deleteNode;

  if (!id) {
    return;
  }

  var shouldDelete = true;
  var node = (0,data__WEBPACK_IMPORTED_MODULE_0__.getNode)(id); // Handle confirmation if needed

  if (FLBuilder._needsDeleteConfirmation(node)) {
    var message = getDeleteConfirmationMessage(node.type);
    shouldDelete = confirm(message);
  }

  if (shouldDelete) {
    var el = FLBuilder._getJQueryElement(id); // Node may not exists on the canvas but still exist in data store


    if (!el.length && (0,data__WEBPACK_IMPORTED_MODULE_0__.nodeExists)(id)) {
      deleteNode(id);
    } else if ('module' === node.type) {
      FLBuilder._deleteModule(el);
    } else if ('column' === node.type) {
      var col = FLBuilder._getColToDelete(el);

      FLBuilder._deleteCol(col);
    } else if ('row' === node.type) {
      FLBuilder._deleteRow(el);
    }

    FLBuilder._highlightEmptyCols();

    FLBuilder._resizeLayout();

    FLBuilder._removeAllOverlays();
  }
};
var copyNode = function copyNode(id) {
  if (!id) {
    return;
  }

  var node = (0,data__WEBPACK_IMPORTED_MODULE_0__.getNode)(id);

  if (!node || 'undefined' === typeof node.type) {
    return;
  }

  if ('module' === node.type) {
    FLBuilder._copyModule(id);
  } else if ('column' === node.type) {
    FLBuilder._copyColumn(id);
  } else if ('row' === node.type) {
    FLBuilder._copyRow(id);
  }
};
var scrollToNode = function scrollToNode(id) {
  var el = document.querySelector("".concat(FLBuilder._contentClass, " [data-node=\"").concat(id, "\"]"));

  if (el) {
    el.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
  }
};
/**
 * Generic API for causing node reordering and reparenting.
 * This updates the layout store and triggers canvas updates.
 *
 * @param String id - node id
 * @param Int position
 * @param String | Null parent - parent node id
 * @return void
 */

var moveNode = function moveNode(id, position) {
  var parent = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
  var resize = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];

  var _getLayoutActions2 = (0,data__WEBPACK_IMPORTED_MODULE_0__.getLayoutActions)(),
      reorderNode = _getLayoutActions2.reorderNode,
      moveNode = _getLayoutActions2.moveNode,
      reorderColumn = _getLayoutActions2.reorderColumn,
      moveColumn = _getLayoutActions2.moveColumn;

  var _getNode = (0,data__WEBPACK_IMPORTED_MODULE_0__.getNode)(id),
      type = _getNode.type,
      currentParent = _getNode.parent,
      currentPosition = _getNode.position;

  var isColumn = 'column' === type; // Reorder or Reparent

  if (parent === currentParent || null === parent) {
    if (position === currentPosition) {
      return;
    }

    isColumn ? reorderColumn(id, position) : reorderNode(id, position);
  } else {
    // Reparent
    isColumn ? moveColumn(id, parent, position, resize) : moveNode(id, parent, position);
  }
};

/***/ }),

/***/ "./src/builder/api/settings.js":
/*!*************************************!*\
  !*** ./src/builder/api/settings.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "openSettings": () => (/* binding */ openSettings)
/* harmony export */ });
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");


var isSettingsPinnedRight = function isSettingsPinnedRight() {
  return document.body.classList.contains('fl-builder-ui-is-pinned-right');
};

var openSettings = function openSettings(id) {
  var _getLayoutActions = (0,data__WEBPACK_IMPORTED_MODULE_0__.getLayoutActions)(),
      displaySettings = _getLayoutActions.displaySettings;

  var _getSystemActions = (0,data__WEBPACK_IMPORTED_MODULE_0__.getSystemActions)(),
      hideCurrentPanel = _getSystemActions.hideCurrentPanel;

  displaySettings(id); // Hide outline panel if needed

  if (isSettingsPinnedRight()) {
    hideCurrentPanel();
  }
};

/***/ }),

/***/ "./src/builder/data/index.js":
/*!***********************************!*\
  !*** ./src/builder/data/index.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getChildren": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.getChildren),
/* harmony export */   "getLayoutActions": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.getLayoutActions),
/* harmony export */   "getLayoutHooks": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.getLayoutHooks),
/* harmony export */   "getLayoutState": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.getLayoutState),
/* harmony export */   "getLayoutStore": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.getLayoutStore),
/* harmony export */   "getNode": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.getNode),
/* harmony export */   "getSystemActions": () => (/* reexport safe */ _system__WEBPACK_IMPORTED_MODULE_0__.getSystemActions),
/* harmony export */   "getSystemConfig": () => (/* reexport safe */ _system__WEBPACK_IMPORTED_MODULE_0__.getSystemConfig),
/* harmony export */   "getSystemSelectors": () => (/* reexport safe */ _system__WEBPACK_IMPORTED_MODULE_0__.getSystemSelectors),
/* harmony export */   "getSystemState": () => (/* reexport safe */ _system__WEBPACK_IMPORTED_MODULE_0__.getSystemState),
/* harmony export */   "getSystemStore": () => (/* reexport safe */ _system__WEBPACK_IMPORTED_MODULE_0__.getSystemStore),
/* harmony export */   "nodeExists": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.nodeExists),
/* harmony export */   "useLayoutState": () => (/* reexport safe */ _layout__WEBPACK_IMPORTED_MODULE_1__.useLayoutState),
/* harmony export */   "useSystemState": () => (/* reexport safe */ _system__WEBPACK_IMPORTED_MODULE_0__.useSystemState)
/* harmony export */ });
/* harmony import */ var _system__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./system */ "./src/builder/data/system/index.js");
/* harmony import */ var _layout__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./layout */ "./src/builder/data/layout/index.js");



/***/ }),

/***/ "./src/builder/data/layout/actions.js":
/*!********************************************!*\
  !*** ./src/builder/data/layout/actions.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "addColumnGroup": () => (/* binding */ addColumnGroup),
/* harmony export */   "addColumnTemplate": () => (/* binding */ addColumnTemplate),
/* harmony export */   "addColumns": () => (/* binding */ addColumns),
/* harmony export */   "addModule": () => (/* binding */ addModule),
/* harmony export */   "addNodeTemplate": () => (/* binding */ addNodeTemplate),
/* harmony export */   "addRow": () => (/* binding */ addRow),
/* harmony export */   "addRowTemplate": () => (/* binding */ addRowTemplate),
/* harmony export */   "applyTemplate": () => (/* binding */ applyTemplate),
/* harmony export */   "cancelDisplaySettings": () => (/* binding */ cancelDisplaySettings),
/* harmony export */   "clearHistoryStates": () => (/* binding */ clearHistoryStates),
/* harmony export */   "copyColumn": () => (/* binding */ copyColumn),
/* harmony export */   "copyModule": () => (/* binding */ copyModule),
/* harmony export */   "copyRow": () => (/* binding */ copyRow),
/* harmony export */   "deleteColumn": () => (/* binding */ deleteColumn),
/* harmony export */   "deleteNode": () => (/* binding */ deleteNode),
/* harmony export */   "deleteNodeTemplate": () => (/* binding */ deleteNodeTemplate),
/* harmony export */   "deleteUserTemplate": () => (/* binding */ deleteUserTemplate),
/* harmony export */   "discardDraft": () => (/* binding */ discardDraft),
/* harmony export */   "displaySettings": () => (/* binding */ displaySettings),
/* harmony export */   "fetchLayout": () => (/* binding */ fetchLayout),
/* harmony export */   "insertFreeformNode": () => (/* binding */ insertFreeformNode),
/* harmony export */   "insertNode": () => (/* binding */ insertNode),
/* harmony export */   "insertNodes": () => (/* binding */ insertNodes),
/* harmony export */   "moveColumn": () => (/* binding */ moveColumn),
/* harmony export */   "moveNode": () => (/* binding */ moveNode),
/* harmony export */   "redo": () => (/* binding */ redo),
/* harmony export */   "renderHistoryState": () => (/* binding */ renderHistoryState),
/* harmony export */   "renderLayout": () => (/* binding */ renderLayout),
/* harmony export */   "renderNode": () => (/* binding */ renderNode),
/* harmony export */   "reorderColumn": () => (/* binding */ reorderColumn),
/* harmony export */   "reorderNode": () => (/* binding */ reorderNode),
/* harmony export */   "resetColWidths": () => (/* binding */ resetColWidths),
/* harmony export */   "resetRowWidth": () => (/* binding */ resetRowWidth),
/* harmony export */   "resizeColumn": () => (/* binding */ resizeColumn),
/* harmony export */   "resizeRowContent": () => (/* binding */ resizeRowContent),
/* harmony export */   "resizingComplete": () => (/* binding */ resizingComplete),
/* harmony export */   "saveDraft": () => (/* binding */ saveDraft),
/* harmony export */   "saveGlobalSettings": () => (/* binding */ saveGlobalSettings),
/* harmony export */   "saveHistoryState": () => (/* binding */ saveHistoryState),
/* harmony export */   "saveLayout": () => (/* binding */ saveLayout),
/* harmony export */   "saveLayoutSettings": () => (/* binding */ saveLayoutSettings),
/* harmony export */   "saveNodeTemplate": () => (/* binding */ saveNodeTemplate),
/* harmony export */   "saveUserTemplateSettings": () => (/* binding */ saveUserTemplateSettings),
/* harmony export */   "setLayout": () => (/* binding */ setLayout),
/* harmony export */   "undo": () => (/* binding */ undo),
/* harmony export */   "updateNode": () => (/* binding */ updateNode),
/* harmony export */   "updateNodeSetting": () => (/* binding */ updateNodeSetting),
/* harmony export */   "updateNodeSettings": () => (/* binding */ updateNodeSettings)
/* harmony export */ });
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var undo = function undo() {
  return {
    type: 'UNDO'
  };
};
var redo = function redo() {
  return {
    type: 'REDO'
  };
};
/**
* Generic Nodes
*/

var insertNode = function insertNode(id) {
  var parent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var nodeType = arguments.length > 2 ? arguments[2] : undefined;
  var position = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 0;
  var settings = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};
  var global = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : false;

  for (var _len = arguments.length, rest = new Array(_len > 6 ? _len - 6 : 0), _key = 6; _key < _len; _key++) {
    rest[_key - 6] = arguments[_key];
  }

  return _objectSpread({
    type: 'INSERT_NODE',
    id: id,
    parent: parent,
    position: position,
    nodeType: nodeType,
    settings: settings,
    global: global
  }, rest);
};
var insertFreeformNode = function insertFreeformNode(id) {
  var node = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return {
    type: 'INSERT_FREEFORM_NODE',
    id: id,
    node: node
  };
};
var insertNodes = function insertNodes() {
  var nodes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return {
    type: 'INSERT_NODES',
    nodes: nodes
  };
};
var reorderNode = function reorderNode(id, position) {
  return {
    type: 'REORDER_NODE',
    id: id,
    position: position
  };
};
var moveNode = function moveNode(id, parent, position) {
  return {
    type: 'REPARENT_NODE',
    id: id,
    parent: parent,
    position: position
  };
};
var renderNode = function renderNode(id) {
  var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : function () {};
  return {
    type: 'RENDER_NODE',
    id: id,
    callback: callback
  };
};
var updateNode = function updateNode(id) {
  var node = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return {
    type: 'UPDATE_NODE',
    id: id,
    node: node
  };
};
var updateNodeSettings = function updateNodeSettings(id) {
  var settings = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function () {};
  return {
    type: 'UPDATE_NODE_SETTINGS',
    id: id,
    settings: settings,
    callback: callback
  };
};
var updateNodeSetting = function updateNodeSetting(id, key, value) {
  return {
    type: 'UPDATE_NODE_SETTING',
    id: id,
    key: key,
    value: value
  };
};
var deleteNode = function deleteNode(id) {
  return {
    type: 'DELETE_NODE',
    id: id
  };
};
/**
* Modules
*/

var addModule = function addModule(moduleType, parent, position) {
  var config = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
  return {
    type: 'ADD_MODULE',
    moduleType: moduleType,
    parent: parent,
    position: position,
    config: config
  };
};
var copyModule = function copyModule(id) {
  var settings = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function () {};
  return {
    type: 'COPY_MODULE',
    id: id,
    settings: settings,
    callback: callback
  };
};
/**
* Columns
*/

var addColumns = function addColumns(id, insert) {
  var colType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '1-col';
  var nested = arguments.length > 3 ? arguments[3] : undefined;
  var module = arguments.length > 4 ? arguments[4] : undefined;
  return {
    type: 'ADD_COLUMNS',
    id: id,
    insert: insert,
    colType: colType,
    nested: nested,
    module: module
  };
};
var reorderColumn = function reorderColumn(id, position) {
  return {
    type: 'REORDER_COLUMN',
    id: id,
    position: position
  };
};
var moveColumn = function moveColumn(id, parent, position) {
  var resize = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
  return {
    type: 'REPARENT_COLUMN',
    id: id,
    parent: parent,
    position: position,
    resize: resize
  };
};
var copyColumn = function copyColumn(id, settings, settingsId) {
  var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function () {};
  return {
    type: 'COPY_COLUMN',
    id: id,
    settings: settings,
    settingsId: settingsId,
    callback: callback
  };
};
var resizeColumn = function resizeColumn(id, width, siblingId, siblingWidth) {
  var shouldPersist = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
  return {
    type: 'RESIZE_COLUMN',
    id: id,
    width: parseInt(width),
    siblingId: siblingId,
    siblingWidth: parseInt(siblingWidth),
    shouldPersist: shouldPersist
  };
};
var deleteColumn = function deleteColumn(id, width) {
  return {
    type: 'DELETE_COLUMN',
    id: id,
    width: width
  };
};
var resetColWidths = function resetColWidths() {
  var groupIds = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  return {
    type: 'RESET_COLUMN_WIDTHS',
    groupIds: groupIds
  };
};
/**
* Column Groups
*/

var addColumnGroup = function addColumnGroup(id, cols, position, module) {
  return {
    type: 'ADD_COLUMN_GROUP',
    id: id,
    cols: cols,
    position: position,
    module: module
  };
};
/**
* Rows
*/

var addRow = function addRow() {
  var cols = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
  var position = arguments.length > 1 ? arguments[1] : undefined;
  var module = arguments.length > 2 ? arguments[2] : undefined;
  return {
    type: 'ADD_ROW',
    cols: cols,
    position: position,
    module: module
  };
};
var copyRow = function copyRow(id) {
  var settings = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var settingsId = arguments.length > 2 ? arguments[2] : undefined;
  var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function () {};
  return {
    type: 'COPY_ROW',
    id: id,
    settings: settings,
    settingsId: settingsId,
    callback: callback
  };
};
var resizeRowContent = function resizeRowContent(id, width) {
  var shouldPersist = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
  return {
    type: 'RESIZE_ROW_CONTENT',
    id: id,
    width: width,
    shouldPersist: shouldPersist
  };
};
var resetRowWidth = function resetRowWidth(id) {
  return {
    type: 'RESET_ROW_WIDTH',
    id: id
  };
};
/**
* Templates
*/

var applyTemplate = function applyTemplate(id) {
  var append = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '0';
  var templateType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'core';
  return {
    type: 'APPLY_TEMPLATE',
    id: id,
    append: append,
    templateType: templateType
  };
};
var addNodeTemplate = function addNodeTemplate(nodeType, templateId, templateType, parent, position) {
  var callback = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : function () {};
  return {
    type: 'ADD_NODE_TEMPLATE',
    nodeType: nodeType,
    templateId: templateId,
    templateType: templateType,
    parent: parent,
    position: position,
    callback: callback
  };
};
var saveNodeTemplate = function saveNodeTemplate(id) {
  var settings = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return {
    type: 'SAVE_NODE_TEMPLATE',
    id: id,
    settings: settings
  };
};
var deleteNodeTemplate = function deleteNodeTemplate(id) {
  var global = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  return {
    type: 'DELETE_NODE_TEMPLATE',
    id: id,
    global: global
  };
};
var saveUserTemplateSettings = function saveUserTemplateSettings() {
  var settings = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return {
    type: 'SAVE_USER_TEMPLATE_SETTINGS',
    settings: settings
  };
};
var deleteUserTemplate = function deleteUserTemplate(id) {
  return {
    type: 'DELETE_USER_TEMPLATE',
    id: id
  };
};
var addColumnTemplate = function addColumnTemplate() {
  return {
    type: 'ADD_COLUMN_TEMPLATE'
  };
};
var addRowTemplate = function addRowTemplate() {
  return {
    type: 'ADD_ROW_TEMPLATE'
  };
};
/**
* Full Layout
*/

var fetchLayout = function fetchLayout() {
  return {
    type: 'FETCH_LAYOUT'
  };
};
var setLayout = function setLayout() {
  var nodes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var attachments = arguments.length > 1 ? arguments[1] : undefined;
  return {
    type: 'SET_LAYOUT',
    nodes: nodes,
    attachments: attachments
  };
};
var renderLayout = function renderLayout() {
  return {
    type: 'RENDER_LAYOUT'
  };
};
var saveLayoutSettings = function saveLayoutSettings() {
  var settings = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return {
    type: 'SAVE_LAYOUT_SETTINGS',
    settings: settings
  };
};
var saveGlobalSettings = function saveGlobalSettings() {
  var settings = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return {
    type: 'SAVE_GLOBAL_SETTINGS',
    settings: settings
  };
};
/**
 * Publish/Save Actions
 */

var saveLayout = function saveLayout() {
  var shouldPublish = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var shouldExit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function () {};
  return {
    type: 'SAVE_LAYOUT',
    shouldPublish: shouldPublish,
    shouldExit: shouldExit,
    callback: callback
  };
};
var saveDraft = function saveDraft() {
  return {
    type: 'SAVE_DRAFT'
  };
};
var discardDraft = function discardDraft() {
  return {
    type: 'DISCARD_DRAFT'
  };
};
/**
* History States
*/

var saveHistoryState = function saveHistoryState(label, moduleType) {
  return {
    type: 'SAVE_HISTORY_STATE',
    label: label,
    moduleType: moduleType
  };
};
var clearHistoryStates = function clearHistoryStates(postId) {
  var shouldExit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  return {
    type: 'CLEAR_HISTORY_STATES',
    postId: postId,
    shouldExit: shouldExit
  };
};
var renderHistoryState = function renderHistoryState(position) {
  var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : function () {};
  return {
    type: 'RENDER_HISTORY_STATE',
    position: position,
    callback: callback
  };
};
/**
* Settings Panels
*/

var displaySettings = function displaySettings(id) {
  return {
    type: 'DISPLAY_SETTINGS',
    id: id
  };
};
var cancelDisplaySettings = function cancelDisplaySettings() {
  return {
    type: 'CANCEL_DISPLAY_SETTINGS'
  };
};
/**
 * Misc
 */

var resizingComplete = function resizingComplete() {
  return {
    type: 'RESIZING_COMPLETE'
  };
};

/***/ }),

/***/ "./src/builder/data/layout/effects.js":
/*!********************************************!*\
  !*** ./src/builder/data/layout/effects.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "after": () => (/* binding */ after),
/* harmony export */   "before": () => (/* binding */ before)
/* harmony export */ });
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./actions */ "./src/builder/data/layout/actions.js");
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }


/**
 * Shorthand function for handling server responses with newNodes and/or updatedNodes
 */

var mergeNewAndUpdatedNodes = function mergeNewAndUpdatedNodes(response, store) {
  var _FLBuilder$_jsonParse = FLBuilder._jsonParse(response),
      _FLBuilder$_jsonParse2 = _FLBuilder$_jsonParse.newNodes,
      newNodes = _FLBuilder$_jsonParse2 === void 0 ? {} : _FLBuilder$_jsonParse2,
      _FLBuilder$_jsonParse3 = _FLBuilder$_jsonParse.updatedNodes,
      updatedNodes = _FLBuilder$_jsonParse3 === void 0 ? {} : _FLBuilder$_jsonParse3; // Insert all affected nodes


  if (0 < Object.keys(newNodes).length) {
    store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.insertNodes(newNodes));
  } // Update positions on sibling nodes


  Object.entries(updatedNodes).map(function (_ref) {
    var _ref2 = _slicedToArray(_ref, 2),
        id = _ref2[0],
        node = _ref2[1];

    store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.updateNode(id, node));
  });
};

var before = {};
var after = {
  UNDO: function UNDO() {
    window.FLBuilderHistoryManager.renderState('prev');
  },
  REDO: function REDO() {
    window.FLBuilderHistoryManager.renderState('next');
  },

  /**
  * Generic Nodes
  */
  UPDATE_NODE_SETTINGS: function UPDATE_NODE_SETTINGS(_ref3) {
    var node_id = _ref3.id,
        settings = _ref3.settings,
        callback = _ref3.callback;
    FLBuilder.ajax({
      action: 'save_settings',
      node_id: node_id,
      settings: settings
    }, callback);
  },
  REORDER_NODE: function REORDER_NODE(_ref4, store) {
    var node_id = _ref4.id,
        position = _ref4.position;

    // Move it on the canvas if it hasn't already
    var _FL$Builder$__canvas$ = FL.Builder.__canvas.getActions(),
        moveNode = _FL$Builder$__canvas$.moveNode;

    moveNode(node_id, position);
    FLBuilder.ajax({
      action: 'reorder_node',
      node_id: node_id,
      position: position
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);

      FLBuilder._reorderNodeComplete(response);
    });
  },
  REPARENT_NODE: function REPARENT_NODE(_ref5, store) {
    var id = _ref5.id,
        parent = _ref5.parent,
        position = _ref5.position;

    // Move it on the canvas if it hasn't already
    var _FL$Builder$__canvas$2 = FL.Builder.__canvas.getActions(),
        moveNode = _FL$Builder$__canvas$2.moveNode;

    moveNode(id, position, parent);
    FLBuilder.ajax({
      action: 'move_node',
      new_parent: parent,
      node_id: id,
      position: position
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);

      FLBuilder._moveNodeComplete(response);
    });
  },
  RENDER_NODE: function RENDER_NODE(_ref6) {
    var id = _ref6.id,
        callback = _ref6.callback;
    FLBuilder.ajax({
      action: 'render_node',
      node_id: id
    }, function (response) {
      var data = FLBuilder._jsonParse(response);

      FLBuilder._renderLayout(data, callback);
    });
  },
  DELETE_NODE: function DELETE_NODE(_ref7) {
    var id = _ref7.id;
    FLBuilder.ajax({
      action: 'delete_node',
      node_id: id
    });
  },

  /**
  * Modules
  */
  ADD_MODULE: function ADD_MODULE(_ref8, store) {
    var moduleType = _ref8.moduleType,
        parent = _ref8.parent,
        position = _ref8.position,
        config = _ref8.config;
    FLBuilder.ajax({
      action: 'render_new_module',
      parent_id: parent,
      type: moduleType,
      position: position,
      node_preview: config.nodePreview,
      widget: config.widget,
      alias: config.alias
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);

      FLBuilder._addModuleComplete(response);
    });
  },
  COPY_MODULE: function COPY_MODULE(_ref9, store) {
    var id = _ref9.id,
        settings = _ref9.settings,
        callback = _ref9.callback;
    FLBuilder.ajax({
      action: 'copy_module',
      node_id: id,
      settings: settings
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);
      callback(response);
    });
  },

  /**
  * Columns
  */
  ADD_COLUMNS: function ADD_COLUMNS(_ref10, store) {
    var id = _ref10.id,
        insert = _ref10.insert,
        colType = _ref10.colType,
        nested = _ref10.nested,
        module = _ref10.module;
    FLBuilder.ajax({
      action: 'render_new_columns',
      node_id: id,
      insert: insert,
      type: colType,
      nested: nested,
      module: module
    }, function (response) {
      // newNodes here actually includes siblings with position updates
      // see server-side handler for the reason
      mergeNewAndUpdatedNodes(response, store);

      FLBuilder._addColsComplete(response);
    });
  },
  RESIZE_COLUMN: function RESIZE_COLUMN(_ref11, store) {
    var id = _ref11.id,
        width = _ref11.width,
        siblingId = _ref11.siblingId,
        siblingWidth = _ref11.siblingWidth,
        shouldPersist = _ref11.shouldPersist;

    if (shouldPersist) {
      // Clear the resizing ids
      store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.resizingComplete());
      FLBuilder.ajax({
        action: 'resize_cols',
        col_id: id,
        col_width: width,
        sibling_id: siblingId,
        sibling_width: siblingWidth
      }, function (response) {
        return mergeNewAndUpdatedNodes(response, store);
      });
    }
  },
  RESET_COLUMN_WIDTHS: function RESET_COLUMN_WIDTHS(_ref12) {
    var group_id = _ref12.groupIds;
    FLBuilder.ajax({
      action: 'reset_col_widths',
      group_id: group_id
    }, function () {
      return FLBuilder.triggerHook('didResetColumnWidthsComplete');
    });
  },
  DELETE_COLUMN: function DELETE_COLUMN(_ref13) {
    var id = _ref13.id,
        width = _ref13.width;
    FLBuilder.ajax({
      action: 'delete_col',
      node_id: id,
      new_width: width
    });
  },
  REORDER_COLUMN: function REORDER_COLUMN(_ref14) {
    var id = _ref14.id,
        position = _ref14.position;

    // Move it on the canvas if it hasn't already
    var _FL$Builder$__canvas$3 = FL.Builder.__canvas.getActions(),
        moveNode = _FL$Builder$__canvas$3.moveNode;

    moveNode(id, position);
    FLBuilder.ajax({
      action: 'reorder_col',
      node_id: id,
      position: position
    }, function () {
      return FLBuilder.triggerHook('didMoveColumn');
    });
  },
  REPARENT_COLUMN: function REPARENT_COLUMN(_ref15) {
    var id = _ref15.id,
        parent = _ref15.parent,
        position = _ref15.position,
        resize = _ref15.resize;

    // Move it on the canvas if it hasn't already
    var _FL$Builder$__canvas$4 = FL.Builder.__canvas.getActions(),
        moveNode = _FL$Builder$__canvas$4.moveNode;

    moveNode(id, position, parent);
    FLBuilder.ajax({
      action: 'move_col',
      node_id: id,
      new_parent: parent,
      position: position,
      resize: resize
    }, function () {
      return FLBuilder.triggerHook('didMoveColumn');
    });
  },
  COPY_COLUMN: function COPY_COLUMN(_ref16, store) {
    var id = _ref16.id,
        settings = _ref16.settings,
        settingsId = _ref16.settingsId,
        callback = _ref16.callback;
    FLBuilder.ajax({
      action: 'copy_col',
      node_id: id,
      settings: settings,
      settings_id: settingsId
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);
      callback(response);
    });
  },

  /**
  * Column Groups
  */
  ADD_COLUMN_GROUP: function ADD_COLUMN_GROUP(_ref17, store) {
    var id = _ref17.id,
        cols = _ref17.cols,
        position = _ref17.position,
        module = _ref17.module;
    FLBuilder.ajax({
      action: 'render_new_column_group',
      node_id: id,
      cols: cols,
      position: position,
      module: module
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);

      FLBuilder._addColGroupComplete(response);
    });
  },

  /**
  * Rows
  */
  ADD_ROW: function ADD_ROW(_ref18, store) {
    var cols = _ref18.cols,
        position = _ref18.position,
        module = _ref18.module;
    FLBuilder.ajax({
      action: 'render_new_row',
      cols: cols,
      position: position,
      module: module
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);

      FLBuilder._addRowComplete(response);
    });
  },
  COPY_ROW: function COPY_ROW(_ref19, store) {
    var id = _ref19.id,
        settings = _ref19.settings,
        settingsId = _ref19.settingsId,
        callback = _ref19.callback;
    FLBuilder.ajax({
      action: 'copy_row',
      node_id: id,
      settings: settings,
      settings_id: settingsId
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);
      callback(response);
    });
  },
  RESIZE_ROW_CONTENT: function RESIZE_ROW_CONTENT(_ref20, store) {
    var node = _ref20.id,
        width = _ref20.width,
        shouldPersist = _ref20.shouldPersist;

    if (shouldPersist) {
      FLBuilder.ajax({
        action: 'resize_row_content',
        node: node,
        width: width
      }); // Clear the resizing ids

      store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.resizingComplete());
    }
  },
  RESET_ROW_WIDTH: function RESET_ROW_WIDTH(_ref21) {
    var id = _ref21.id;
    FLBuilder.ajax({
      action: 'resize_row_content',
      node: id,
      width: ''
    });
  },

  /**
  * Templates
  */
  APPLY_TEMPLATE: function APPLY_TEMPLATE(_ref22, store) {
    var template_id = _ref22.id,
        append = _ref22.append,
        templateType = _ref22.templateType;
    var callback = 'core' === templateType ? FLBuilder._applyTemplateComplete : FLBuilder._applyUserTemplateComplete;
    FLBuilder.ajax({
      action: 'core' === templateType ? 'apply_template' : 'apply_user_template',
      template_id: template_id,
      append: append
    }, function (response) {
      var data = FLBuilder._jsonParse(response);

      store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.setLayout(data.newNodes, []));
      callback(response);
    });
  },
  ADD_NODE_TEMPLATE: function ADD_NODE_TEMPLATE(_ref23, store) {
    var nodeType = _ref23.nodeType,
        templateId = _ref23.templateId,
        templateType = _ref23.templateType,
        parent = _ref23.parent,
        position = _ref23.position,
        callback = _ref23.callback;
    var action = '';

    switch (nodeType) {
      case 'row':
        action = 'render_new_row_template';
        break;

      case 'column':
        action = 'render_new_col_template';
        break;

      default:
        action = 'render_new_module';
    }

    FLBuilder.ajax({
      action: action,
      template_id: templateId,
      template_type: templateType,
      parent_id: parent,
      position: position
    }, function (response) {
      mergeNewAndUpdatedNodes(response, store);
      callback(response);
    });
  },
  SAVE_NODE_TEMPLATE: function SAVE_NODE_TEMPLATE(_ref24, store) {
    var id = _ref24.id,
        settings = _ref24.settings;
    FLBuilder.ajax({
      action: 'save_node_template',
      node_id: id,
      settings: settings
    }, function (response) {
      store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.fetchLayout());

      FLBuilder._saveNodeTemplateComplete(response);

      FLBuilder._hideNodeLoading(id);
    });
  },
  DELETE_NODE_TEMPLATE: function DELETE_NODE_TEMPLATE(_ref25) {
    var id = _ref25.id,
        global = _ref25.global;
    FLBuilder.ajax({
      action: 'delete_node_template',
      template_id: id
    }, function () {
      if (global) {
        FLBuilder._updateLayout();
      }
    });
  },
  SAVE_USER_TEMPLATE_SETTINGS: function SAVE_USER_TEMPLATE_SETTINGS(_ref26) {
    var settings = _ref26.settings;
    FLBuilder.ajax({
      action: 'save_user_template',
      settings: settings
    }, FLBuilder._saveUserTemplateSettingsComplete);
  },
  DELETE_USER_TEMPLATE: function DELETE_USER_TEMPLATE(_ref27) {
    var id = _ref27.id;
    FLBuilder.ajax({
      action: 'delete_user_template',
      template_id: id
    });
  },
  RENDER_LAYOUT: function RENDER_LAYOUT() {
    FLBuilder.ajax({
      action: 'render_layout'
    }, FLBuilder._renderLayout);
  },
  FETCH_LAYOUT: function FETCH_LAYOUT(action, store) {
    FLBuilder.ajax({
      action: 'get_layout'
    }, function (response) {
      var _FLBuilder$_jsonParse4 = FLBuilder._jsonParse(response),
          nodes = _FLBuilder$_jsonParse4.nodes,
          attachments = _FLBuilder$_jsonParse4.attachments;

      store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.setLayout(nodes, attachments));
    });
  },
  SAVE_LAYOUT: function SAVE_LAYOUT(_ref28) {
    var shouldPublish = _ref28.shouldPublish,
        shouldExit = _ref28.shouldExit,
        callback = _ref28.callback;
    FLBuilder.ajax({
      action: 'save_layout',
      publish: shouldPublish,
      exit: shouldExit ? 1 : 0
    }, callback);
  },
  SAVE_DRAFT: function SAVE_DRAFT() {
    FLBuilder.ajax({
      action: 'save_draft'
    }, FLBuilder._exit);
  },
  DISCARD_DRAFT: function DISCARD_DRAFT() {
    FLBuilder.ajax({
      action: 'clear_draft_layout'
    }, function () {
      FLBuilder.triggerHook('didDiscardChanges');

      FLBuilder._exit();
    });
  },
  SAVE_LAYOUT_SETTINGS: function SAVE_LAYOUT_SETTINGS(_ref29) {
    var settings = _ref29.settings;
    FLBuilder.ajax({
      action: 'save_layout_settings',
      settings: settings
    }, function () {
      return FLBuilder._saveLayoutSettingsComplete(settings);
    });
  },
  SAVE_GLOBAL_SETTINGS: function SAVE_GLOBAL_SETTINGS(_ref30) {
    var settings = _ref30.settings;
    FLBuilder.ajax({
      action: 'save_global_settings',
      settings: settings
    }, FLBuilder._saveGlobalSettingsComplete);
  },

  /**
  * History States
  */
  SAVE_HISTORY_STATE: function SAVE_HISTORY_STATE(_ref31) {
    var label = _ref31.label,
        moduleType = _ref31.moduleType;
    FLBuilder.ajax({
      action: 'save_history_state',
      label: label,
      module_type: moduleType
    }, function (response) {
      var data = FLBuilder._jsonParse(response);

      FLBuilderHistoryManager.states = data.states;
      FLBuilderHistoryManager.position = parseInt(data.position);
      FLBuilderHistoryManager.setupMainMenuData();
    });
  },
  CLEAR_HISTORY_STATES: function CLEAR_HISTORY_STATES(_ref32) {
    var postId = _ref32.postId,
        shouldExit = _ref32.shouldExit;
    FLBuilder.ajax({
      action: 'clear_history_states',
      post_id: postId
    }, function () {
      if (!shouldExit) {
        FLBuilderHistoryManager.saveCurrentState('draft_created');
      }
    });
  },
  RENDER_HISTORY_STATE: function RENDER_HISTORY_STATE(_ref33, store) {
    var position = _ref33.position,
        callback = _ref33.callback;
    FLBuilder.ajax({
      action: 'render_history_state',
      position: position
    }, function (response) {
      var _FLBuilder$_jsonParse5 = FLBuilder._jsonParse(response),
          newNodes = _FLBuilder$_jsonParse5.newNodes,
          config = _FLBuilder$_jsonParse5.config;

      store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_0__.setLayout(newNodes, config.attachments));
      callback(response);
    });
  },

  /**
  * Settings Panels
  */
  DISPLAY_SETTINGS: function DISPLAY_SETTINGS(_ref34, store) {
    var id = _ref34.id;
    var nodes = store.getState().layout.present.nodes;

    if ('global' === id) {
      FLBuilder._globalSettingsClicked();

      return;
    } else if ('layout' === id) {
      FLBuilder._layoutSettingsClicked();

      return;
    }

    if (undefined !== nodes[id]) {
      var _nodes$id = nodes[id],
          type = _nodes$id.type,
          settings = _nodes$id.settings,
          parent = _nodes$id.parent,
          global = _nodes$id.global,
          template_id = _nodes$id.template_id;

      switch (type) {
        case 'column-group':
          break;

        case 'row':
          FLBuilder._showRowSettings(id, global);

          break;

        case 'column':
          var isNodeTemplate = 'column' !== FLBuilderConfig.userTemplateType && undefined !== template_id;

          FLBuilder._showColSettings(id, global, isNodeTemplate);

          break;

        default:
          FLBuilder._showModuleSettings({
            nodeId: id,
            parentId: parent,
            type: settings.type,
            global: global
          });

      }
    }
  }
};

/***/ }),

/***/ "./src/builder/data/layout/index.js":
/*!******************************************!*\
  !*** ./src/builder/data/layout/index.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getChildren": () => (/* binding */ getChildren),
/* harmony export */   "getLayoutActions": () => (/* binding */ getLayoutActions),
/* harmony export */   "getLayoutHooks": () => (/* binding */ getLayoutHooks),
/* harmony export */   "getLayoutState": () => (/* binding */ getLayoutState),
/* harmony export */   "getLayoutStore": () => (/* binding */ getLayoutStore),
/* harmony export */   "getNode": () => (/* binding */ getNode),
/* harmony export */   "nodeExists": () => (/* binding */ nodeExists),
/* harmony export */   "useLayoutState": () => (/* binding */ useLayoutState)
/* harmony export */ });
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store */ "./src/builder/data/layout/store/index.js");
/* harmony import */ var _reducers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./reducers */ "./src/builder/data/layout/reducers.js");
/* harmony import */ var _effects__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./effects */ "./src/builder/data/layout/effects.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./actions */ "./src/builder/data/layout/actions.js");
/* harmony import */ var _tests__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./tests */ "./src/builder/data/layout/tests.js");





var _FLBuilderConfig = FLBuilderConfig,
    global = _FLBuilderConfig.global;
var state = {
  layout: {
    present: {
      nodes: {},
      globalSettings: global
    }
  }
};

var _createLayoutStore = (0,_store__WEBPACK_IMPORTED_MODULE_0__["default"])({
  state: state,
  reducers: _reducers__WEBPACK_IMPORTED_MODULE_1__,
  effects: _effects__WEBPACK_IMPORTED_MODULE_2__,
  actions: _actions__WEBPACK_IMPORTED_MODULE_3__,
  tests: _tests__WEBPACK_IMPORTED_MODULE_4__["default"]
}),
    store = _createLayoutStore.store,
    actionCreators = _createLayoutStore.actions,
    hooks = _createLayoutStore.hooks;

var getLayoutStore = function getLayoutStore() {
  return store;
};
var getLayoutState = function getLayoutState() {
  return store.getState();
};
var getLayoutActions = function getLayoutActions() {
  return actionCreators;
};
var getLayoutHooks = function getLayoutHooks() {
  return hooks;
};
var useLayoutState = hooks.useLayoutStore;
var getNode = function getNode(id) {
  var nodes = getLayoutState().layout.present.nodes;

  if (id && id in nodes) {
    return nodes[id];
  }

  return nodes;
};
var getChildren = function getChildren(id) {
  var nodes = getLayoutState().layout.present.nodes;
  return Object.values(nodes).filter(function (node) {
    return id === node.parent;
  });
};
var nodeExists = function nodeExists(id) {
  var nodes = getLayoutState().layout.present.nodes;
  return 'undefined' !== nodes[id];
}; // Initialize the data

store.dispatch(_actions__WEBPACK_IMPORTED_MODULE_3__.fetchLayout());

/***/ }),

/***/ "./src/builder/data/layout/reducers.js":
/*!*********************************************!*\
  !*** ./src/builder/data/layout/reducers.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "editing": () => (/* binding */ editing),
/* harmony export */   "layout": () => (/* binding */ layout),
/* harmony export */   "resizing": () => (/* binding */ resizing)
/* harmony export */ });
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux */ "redux");
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(redux__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _undoable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./undoable */ "./src/builder/data/layout/undoable.js");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./src/builder/data/layout/utils/index.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }





var nodes = function nodes() {
  var _objectSpread6;

  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'INSERT_NODE':
      return _objectSpread(_objectSpread({}, state), (0,_utils__WEBPACK_IMPORTED_MODULE_2__.insertNewNodeAndResolvePositions)(state, action.id, {
        node: action.id,
        parent: action.parent,
        type: action.nodeType,
        position: action.position,
        settings: action.settings,
        global: action.global
      }));

    case 'INSERT_FREEFORM_NODE':
      return _objectSpread(_objectSpread({}, state), (0,_utils__WEBPACK_IMPORTED_MODULE_2__.insertNewNodeAndResolvePositions)(state, action.id, action.node));

    case 'INSERT_NODES':
      return _objectSpread(_objectSpread({}, state), action.nodes);

    case 'REORDER_NODE':
    case 'REORDER_COLUMN':
      return _objectSpread(_objectSpread({}, state), (0,_utils__WEBPACK_IMPORTED_MODULE_2__.insertExistingNodeAndResolvePositions)(action.id, state[action.id].parent, action.position, state));

    case 'REPARENT_NODE':
    case 'REPARENT_COLUMN':
      return _objectSpread(_objectSpread({}, state), (0,_utils__WEBPACK_IMPORTED_MODULE_2__.insertExistingNodeAndResolvePositions)(action.id, action.parent, action.position, state));

    case 'UPDATE_NODE':
      return _objectSpread(_objectSpread({}, state), {}, _defineProperty({}, action.id, (0,_utils__WEBPACK_IMPORTED_MODULE_2__.mergeNode)(state[action.id], action.node)));

    case 'UPDATE_NODE_SETTING':
      return _objectSpread(_objectSpread({}, state), {}, _defineProperty({}, action.id, (0,_utils__WEBPACK_IMPORTED_MODULE_2__.mergeNode)(state[action.id], {
        settings: _objectSpread(_objectSpread({}, state[action.id].settings), {}, _defineProperty({}, action.key, action.value))
      })));

    case 'UPDATE_NODE_SETTINGS':
      if (undefined === state[action.id]) {
        return state;
      }

      return _objectSpread(_objectSpread({}, state), {}, _defineProperty({}, action.id, (0,_utils__WEBPACK_IMPORTED_MODULE_2__.mergeNode)(state[action.id], {
        settings: _objectSpread(_objectSpread(_objectSpread({}, state[action.id].settings), action.settings), {}, {
          type: 'module' === state[action.id].type ? state[action.id].settings.type : undefined
        })
      })));

    case 'DELETE_NODE':
      return (0,_utils__WEBPACK_IMPORTED_MODULE_2__.deleteNodeAndResolvePositions)(action.id, state);

    /**
     * Modules
     *
     * ADD_MODULE has no reducer implementation. Causes Ajax -> INSERT_NODE.
     * COPY_MODULE has no reducer implementation. Causes Ajax -> INSERT_NODE.
     * For delete see DELETE_NODE
     */

    /**
     * Columns
     *
     * ADD_COLUMNS has no reducer implementation. Causes Ajax.
     * COPY_COLUMN has no reducer implementation. Causes Ajax.
     * REORDER_COLUMN shares implementation with REORDER_NODE - see above.
     * REPARENT_COLUMN shares implementation with REPARENT_NODE - see above.
     */

    case 'RESIZE_COLUMN':
      return _objectSpread(_objectSpread({}, state), {}, (_objectSpread6 = {}, _defineProperty(_objectSpread6, action.id, (0,_utils__WEBPACK_IMPORTED_MODULE_2__.mergeNode)(state[action.id], {
        settings: _objectSpread(_objectSpread({}, state[action.id].settings), {}, {
          size: action.width
        })
      })), _defineProperty(_objectSpread6, action.siblingId, (0,_utils__WEBPACK_IMPORTED_MODULE_2__.mergeNode)(state[action.siblingId], {
        settings: _objectSpread(_objectSpread({}, state[action.siblingId].settings), {}, {
          size: action.siblingWidth
        })
      })), _objectSpread6));

    case 'DELETE_COLUMN':
      return (0,_utils__WEBPACK_IMPORTED_MODULE_2__.deleteNodeAndResolvePositions)(action.id, state);

    case 'RESET_COLUMN_WIDTHS':
      return _objectSpread(_objectSpread({}, state), (0,_utils__WEBPACK_IMPORTED_MODULE_2__.resetColumnWidths)(action.groupIds, state));

    /**
     * Column Groups
     *
     * ADD_COLUMN_GROUP has no implementation. Causes Ajax.
     */

    /**
     * Rows
     *
     * ADD_ROW has no implementation. Causes Ajax.
     * COPY_ROW has no implementation. Causes Ajax.
     */

    case 'RESIZE_ROW_CONTENT':
      return _objectSpread(_objectSpread({}, state), {}, _defineProperty({}, action.id, (0,_utils__WEBPACK_IMPORTED_MODULE_2__.mergeNode)(state[action.id], {
        settings: _objectSpread(_objectSpread({}, state[action.id].settings), {}, {
          'max_content_width': action.width
        })
      })));

    case 'RESET_ROW_WIDTH':
      return _objectSpread(_objectSpread({}, state), {}, _defineProperty({}, action.id, (0,_utils__WEBPACK_IMPORTED_MODULE_2__.mergeNode)(state[action.id], {
        settings: _objectSpread(_objectSpread({}, state[action.id].settings), {}, {
          'max_content_width': ''
        })
      })));

    /**
     * Templates
     *
     * APPLY_TEMPLATE has no reducer implementation. Causes Ajax.
     * SAVE_NODE_TEMPLATE has no reducer implementation. Causes Ajax.
     * DELETE_NODE_TEMPLATE has no reducer implementation. Causes Ajax.
     * SAVE_USER_TEMPLATE_SETTINGS has no reducer implementation. Causes Ajax.
     * DELETE_USER_TEMPLATE has no reducer implementation. Causes Ajax.
     */

    case 'ADD_COLUMN_TEMPLATE':
    case 'ADD_ROW_TEMPLATE':
      console.log(action.type, 'Needs node reducer implementation?');
      return state;

    /**
     * Layout
     *
     * RENDER_LAYOUT has no reducer implementation. Causes Ajax.
     * FETCH_LAYOUT has no reducer implementation. Causes Ajax.
     */

    case 'SET_LAYOUT':
      return action.nodes;

    /**
     * Publish/Save Actions
     *
     * SAVE_LAYOUT, SAVE_DRAFT and DISCARD DRAFT trigger ajax effects.
     */

    /**
     * History State
     *
     * SAVE_HISTORY_STATE has no reducer implementation. Causes Ajax.
     * CLEAR_HISTORY_STATES has no reducer implementation. Causes Ajax.
     * RENDER_HISTORY_STATE has no reducer implementation. Causes Ajax.
     */

    /**
     * Default Pass-through
     */

    default:
      return state;
  }
};
/**
 * Tracks an array of document attachment urls.
 *
 * @var state Array
 * @var action Object
 * @return Array
 */


var attachments = function attachments() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_LAYOUT':
      if (undefined !== action.attachments) {
        return action.attachments;
      }

      return state;

    default:
      return state;
  }
};
/**
 * Tracks the settings object for global settings
 *
 * @var state Object
 * @var action Object
 * @return Object
 */


var globalSettings = function globalSettings() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SAVE_GLOBAL_SETTINGS':
      return action.settings;

    default:
      return state;
  }
};
/**
 * Layout reducer joins attachments, nodes, globalSettings into a single object reducer
 * It's wrapped in the Higher-Order Reducer undoable for undo/redo support
 */


var layout = (0,_undoable__WEBPACK_IMPORTED_MODULE_1__["default"])((0,redux__WEBPACK_IMPORTED_MODULE_0__.combineReducers)({
  attachments: attachments,
  nodes: nodes,
  globalSettings: globalSettings
}));
/**
 * Tracks the id (or name - global, layout) of the settings form being edited currently.
 * returns null when inactive
 *
 * @var state null|string
 * @var action Object
 * @return null|string
 */

var editing = function editing() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'DISPLAY_SETTINGS':
      return action.id;

    case 'UPDATE_NODE_SETTINGS':
    case 'CANCEL_DISPLAY_SETTINGS':
      return null;

    default:
      return state;
  }
};
/**
 * Manages an array of any node ids (col or row) that are currently undergoing resize. \
 * Returns false when not active.
 *
 * @var state BOOL|Array
 * @var action Object
 * @return BOOL|Array
 */

var resizing = function resizing() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RESIZING_COMPLETE':
      return false;

    case 'RESIZE_ROW_CONTENT':
      return [action.id];

    case 'RESIZE_COLUMN':
      return [action.id, action.siblingId];

    default:
      return state;
  }
};

/***/ }),

/***/ "./src/builder/data/layout/store/hooks.js":
/*!************************************************!*\
  !*** ./src/builder/data/layout/store/hooks.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }



var stripNodeSettings = function stripNodeSettings(nodes) {
  var updated = {};
  Object.values(nodes).map(function (node) {
    var newNode = _objectSpread({}, node);

    delete newNode.settings;
    updated[node.node] = newNode;
  });
  return updated;
};

var getChildNodes = function getChildNodes() {
  var nodes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var parent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var found = {};
  var children = Object.values(nodes).filter(function (node) {
    return parent === node.parent;
  });
  children.map(function (node) {
    return found[node.node] = node;
  });
  return found;
};

var getNodeSettings = function getNodeSettings() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var id = arguments.length > 1 ? arguments[1] : undefined;
  var nodes = state.layout.present.nodes;

  if (undefined === nodes[id]) {
    return {};
  }

  return nodes[id].settings;
};

var someNodesHaveChanged = function someNodesHaveChanged(newState, oldState) {
  // have the total number of nodes changed?
  if (Object.keys(newState).length !== Object.keys(oldState).length) {
    return true;
  } // have any properties changed - excluding settings


  return Object.values(newState).some(function (node) {
    var old = oldState[node.node];

    if (undefined === node || undefined === old) {
      return true;
    }

    return node.type !== old.type || node.parent !== old.parent || node.position !== old.position || node.global !== old.global;
  });
};

var someSettingsHaveChanged = function someSettingsHaveChanged(a, b) {
  if (Object.keys(a).length !== Object.keys(b).length) {
    return true;
  }

  return Object.keys(a).some(function (key) {
    return a[key] !== b[key];
  });
};

var createStoreHooks = function createStoreHooks(store) {
  /**
   * Generic hook for observing the entire redux store.
   * This is usually one to avoid as it exposes all of the undo/redo history.
   */
  var useLayoutStore = function useLayoutStore() {
    var needsRender = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : function () {
      return true;
    };
    var initial = store.getState();
    var prevState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(initial);

    var _useState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initial),
        _useState2 = _slicedToArray(_useState, 2),
        state = _useState2[0],
        setState = _useState2[1];

    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
      setState(store.getState());
      return store.subscribe(function () {
        var newState = store.getState();

        if (needsRender(prevState.current, newState)) {
          setState(_objectSpread({}, newState));
        }

        prevState.current = newState;
      });
    }, []);
    return state;
  };

  var useNodeSettings = function useNodeSettings(id) {
    var initial = getNodeSettings(store.getState(), id);
    var prevState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(initial);

    var _useState3 = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initial),
        _useState4 = _slicedToArray(_useState3, 2),
        state = _useState4[0],
        setState = _useState4[1];

    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
      // On mount, check if anything has changed since initial
      var newState = getNodeSettings(store.getState(), id);

      if (someSettingsHaveChanged(newState, prevState.current)) {
        setState(newState);
      }

      prevState.current = newState; // Subscribe to store updates

      return store.subscribe(function () {
        var latest = getNodeSettings(store.getState(), id);

        if (someSettingsHaveChanged(latest, prevState.current)) {
          setState(latest);
        }

        prevState.current = latest;
      });
    }, [id]);
    return state;
  };

  var getNodesWithoutSettings = function getNodesWithoutSettings() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var parent = arguments.length > 1 ? arguments[1] : undefined;
    var nodes = state.layout.present.nodes;

    if (1 === Object.keys(nodes).length) {
      if (undefined !== parent && null !== parent) {
        nodes = getChildNodes(nodes, parent);
      }
    } else {
      if (undefined !== parent) {
        nodes = getChildNodes(nodes, parent);
      }
    }

    return stripNodeSettings(nodes);
  };

  var useNodesWithoutSettings = function useNodesWithoutSettings(parent) {
    var initial = getNodesWithoutSettings(store.getState(), parent);
    var prevState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(initial);

    var _useState5 = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initial),
        _useState6 = _slicedToArray(_useState5, 2),
        state = _useState6[0],
        setState = _useState6[1];

    (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
      // On mount, check if anything has changed since initial
      var newState = getNodesWithoutSettings(store.getState(), parent);

      if (someNodesHaveChanged(newState, prevState.current)) {
        setState(newState);
      }

      prevState.current = newState; // Subscribe to store updates

      return store.subscribe(function () {
        var newState = getNodesWithoutSettings(store.getState(), parent);

        if (someNodesHaveChanged(newState, prevState.current)) {
          setState(newState);
        }

        prevState.current = newState;
      });
    }, []);
    return state;
  };

  return {
    useLayoutStore: useLayoutStore,
    // Full store
    useNodeSettings: useNodeSettings,
    useNodesWithoutSettings: useNodesWithoutSettings
  };
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createStoreHooks);

/***/ }),

/***/ "./src/builder/data/layout/store/index.js":
/*!************************************************!*\
  !*** ./src/builder/data/layout/store/index.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux */ "redux");
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(redux__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _middleware__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./middleware */ "./src/builder/data/layout/store/middleware.js");
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hooks */ "./src/builder/data/layout/store/hooks.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }




var defaultState = {
  layout: {
    past: [],
    present: {
      attachments: [],
      nodes: {}
    },
    future: []
  }
};
var defaultConfig = {
  state: {},
  reducers: {},
  actions: {},
  effects: {},
  tests: {}
};

var createLayoutStore = function createLayoutStore() {
  var initialConfig = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultConfig;
  var name = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'fl-builder/layout';

  var config = _objectSpread(_objectSpread({}, defaultConfig), initialConfig);

  var state = _objectSpread(_objectSpread({}, defaultState), config.state);

  var reducer = (0,redux__WEBPACK_IMPORTED_MODULE_0__.combineReducers)(config.reducers);
  var middleware = (0,_middleware__WEBPACK_IMPORTED_MODULE_1__["default"])(name, config.effects, config.tests);
  var store = (0,redux__WEBPACK_IMPORTED_MODULE_0__.createStore)(reducer, state, middleware);
  return {
    store: store,
    actions: (0,redux__WEBPACK_IMPORTED_MODULE_0__.bindActionCreators)(_objectSpread({}, config.actions), store.dispatch),
    hooks: (0,_hooks__WEBPACK_IMPORTED_MODULE_2__["default"])(store)
  };
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createLayoutStore);

/***/ }),

/***/ "./src/builder/data/layout/store/middleware.js":
/*!*****************************************************!*\
  !*** ./src/builder/data/layout/store/middleware.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux */ "redux");
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(redux__WEBPACK_IMPORTED_MODULE_0__);

var INCLUDE_TESTS = true;

var applyTests = function applyTests() {
  var tests = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return function (_ref) {
    var getState = _ref.getState;
    return function (next) {
      return function (action) {
        var result = next(action);
        var state = getState();

        if (undefined !== tests[action.type]) {
          tests[action.type](state, action);
        }

        return result;
      };
    };
  };
};
/**
 * Applys before and after effects to store actions.
 */


var applyEffects = function applyEffects(effects) {
  var before = effects.before,
      after = effects.after;
  return function (store) {
    return function (next) {
      return function (action) {
        if (before && before[action.type]) {
          before[action.type](action, store);
        }

        var result = next(action);

        if (after && after[action.type]) {
          after[action.type](action, store);
        }

        return result;
      };
    };
  };
};
/**
 * Creates all enhancers for a new store with support
 * for redux dev tools.
 */


var createEnhancers = function createEnhancers(name) {
  var effects = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var tests = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  // Add Dev Tools Extension Support
  var devToolsCompose = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__;
  var composeEnhancers = devToolsCompose ? devToolsCompose({
    name: name
  }) : redux__WEBPACK_IMPORTED_MODULE_0__.compose;

  if (INCLUDE_TESTS) {
    return composeEnhancers((0,redux__WEBPACK_IMPORTED_MODULE_0__.applyMiddleware)(applyTests(tests), applyEffects(effects)));
  }

  return composeEnhancers((0,redux__WEBPACK_IMPORTED_MODULE_0__.applyMiddleware)(applyEffects(effects)));
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createEnhancers);

/***/ }),

/***/ "./src/builder/data/layout/tests.js":
/*!******************************************!*\
  !*** ./src/builder/data/layout/tests.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils */ "./src/builder/data/layout/utils/index.js");

var tests = {
  // Test functions receive ( newState, action, prevState )
  SET_LAYOUT: function SET_LAYOUT(state) {
    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNodes)(state.layout.present.nodes);
  },

  /**
   * Generic Nodes
   */
  INSERT_NODE: function INSERT_NODE(state, action) {
    var nodes = state.layout.present.nodes; // Check node exists

    console.assert((0,_utils__WEBPACK_IMPORTED_MODULE_0__.nodeExists)(action.id, nodes), 'Node should exist after insert.'); // Is it well formed?

    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNodeShape)(nodes[action.id]); // does the position match the action?

    console.assert(nodes[action.id].position === action.position, 'Node position should be consistent after insert.');
    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNoOrphans)(nodes);
  },
  REORDER_NODE: function REORDER_NODE(state, action) {
    var nodes = state.layout.present.nodes; // Check node exists

    console.assert((0,_utils__WEBPACK_IMPORTED_MODULE_0__.nodeExists)(action.id, nodes), 'Node no longer exists after reorder.'); // Is it well formed?

    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNodeShape)(nodes[action.id]); // Check position

    var node = nodes[action.id];
    console.assert(action.position === node.position, 'Node position should match action.position');
  },
  REPARENT_NODE: function REPARENT_NODE(state, action) {
    var nodes = state.layout.present.nodes;
    console.assert((0,_utils__WEBPACK_IMPORTED_MODULE_0__.nodeExists)(action.id, nodes), 'Node should not exist after reparent'); // Is it well formed?

    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNodeShape)(nodes[action.id]);
    console.assert(action.parent === nodes[action.id].parent, 'Node should have correct parent after reparent');
  },
  DELETE_NODE: function DELETE_NODE(state, action) {
    var nodes = state.layout.present.nodes; // Shouldn't exist anymore

    console.assert(!(0,_utils__WEBPACK_IMPORTED_MODULE_0__.nodeExists)(action.id, nodes), 'Node should not exist after delete');
    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNoOrphans)(nodes);
  },

  /**
   * Modules
   */
  COPY_MODULE: function COPY_MODULE(state, action) {
    var nodes = state.layout.present.nodes; // Check node exists

    var exists = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.nodeExists)(action.id, nodes);
    console.assert(exists, 'Node no longer exists after duplicate.');

    if (!exists) {
      return;
    } // Is it well formed?


    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNodeShape)(nodes[action.id]);
  },

  /**
   * Columns
   */
  DELETE_COLUMN: function DELETE_COLUMN(state) {
    var nodes = state.layout.present.nodes;
    (0,_utils__WEBPACK_IMPORTED_MODULE_0__.verifyNoOrphans)(nodes);
  },
  REPARENT_COLUMN: function REPARENT_COLUMN() {
    console.warn('REPARENT_COLUMN needs a test.');
  },
  RESET_COLUMN_WIDTHS: function RESET_COLUMN_WIDTHS(state, action) {
    var nodes = state.layout.present.nodes;
    action.groupIds.map(function (id) {
      var cols = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.getChildNodes)(nodes, id);
      var size = 100 / cols.length;
      var matches = cols.every(function (col) {
        return col.settings.size === size.toPrecision(5);
      });
      console.assert(matches, 'Column sizes should match after reset');
    });
  }
  /**
   * Rows
   */

};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (tests);

/***/ }),

/***/ "./src/builder/data/layout/undoable.js":
/*!*********************************************!*\
  !*** ./src/builder/data/layout/undoable.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * Higher-order reducer to add undo/redo functionality.
 */
var undoable = function undoable(reducer) {
  // Call the reducer with empty action to populate the initial state
  var defaultState = {
    past: [],
    present: reducer(undefined, {}),
    future: []
  }; // Return a reducer that handles undo and redo

  return function () {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultState;
    var action = arguments.length > 1 ? arguments[1] : undefined;
    var _state$past = state.past,
        past = _state$past === void 0 ? [] : _state$past,
        present = state.present,
        _state$future = state.future,
        future = _state$future === void 0 ? [] : _state$future;

    switch (action.type) {
      /*
      case 'UNDO':
      	const previous = past[ past.length - 1 ] // eslint-disable-line
      	const newPast = past.slice( 0, past.length - 1 ) // eslint-disable-line
      	return {
      		past: newPast,
      		present: previous,
      		future: [ present, ...future ]
      	}
      case 'REDO':
      	const next = future[0] // eslint-disable-line
      	const newFuture = future.slice( 1 ) // eslint-disable-line
      	return {
      		past: [ ...past, present ],
      		present: next,
      		future: newFuture
      	}
      */

      /**
       * Any high precision actions should be excluded from creating undo states.
       */
      case 'RESIZE_COLUMN':
        if (!action.persist) {
          return {
            past: past,
            present: reducer(present, action),
            future: future
          };
        }

        return {
          past: past,
          present: reducer(present, action),
          future: future
        };

      default:
        return {
          past: past,
          present: reducer(present, action),
          future: future
        };
    }
  };
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (undoable);

/***/ }),

/***/ "./src/builder/data/layout/utils/index.js":
/*!************************************************!*\
  !*** ./src/builder/data/layout/utils/index.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "defaultNode": () => (/* binding */ defaultNode),
/* harmony export */   "deleteChildren": () => (/* binding */ deleteChildren),
/* harmony export */   "deleteNodeAndResolvePositions": () => (/* binding */ deleteNodeAndResolvePositions),
/* harmony export */   "getChildNodes": () => (/* binding */ getChildNodes),
/* harmony export */   "getOrphans": () => (/* binding */ getOrphans),
/* harmony export */   "getSiblingNodes": () => (/* binding */ getSiblingNodes),
/* harmony export */   "insertExistingNodeAndResolvePositions": () => (/* binding */ insertExistingNodeAndResolvePositions),
/* harmony export */   "insertNewNodeAndResolvePositions": () => (/* binding */ insertNewNodeAndResolvePositions),
/* harmony export */   "isNodeEmpty": () => (/* binding */ isNodeEmpty),
/* harmony export */   "mergeNode": () => (/* binding */ mergeNode),
/* harmony export */   "nodeExists": () => (/* binding */ nodeExists),
/* harmony export */   "resetColumnWidths": () => (/* binding */ resetColumnWidths),
/* harmony export */   "sortNodes": () => (/* binding */ sortNodes),
/* harmony export */   "verifyNoOrphans": () => (/* binding */ verifyNoOrphans),
/* harmony export */   "verifyNodeShape": () => (/* binding */ verifyNodeShape),
/* harmony export */   "verifyNodes": () => (/* binding */ verifyNodes)
/* harmony export */ });
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var defaultNode = {
  node: '',
  type: '',
  parent: '',
  position: 0,
  global: false,
  settings: {}
};
/**
 * Sort an array of nodes by position. Used in Array.sort()
 */

var sortNodes = function sortNodes(a, b) {
  if (a.position > b.position) {
    return 1;
  } else if (a.position < b.position) {
    return -1;
  }

  return 0;
};
var nodeExists = function nodeExists(id) {
  var nodes = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return undefined !== nodes[id];
};
/**
 * Get all immediate children of a particular parent node
 */

var getChildNodes = function getChildNodes() {
  var nodes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var parent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  return Object.values(nodes).filter(function (node) {
    return parent === node.parent;
  }).sort(sortNodes);
};
/**
 * Get all siblings of a particular node
 */

var getSiblingNodes = function getSiblingNodes() {
  var nodes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var id = arguments.length > 1 ? arguments[1] : undefined;
  var target = nodes[id];
  return Object.values(nodes).filter(function (node) {
    return target.parent === node.parent && id !== node.node;
  }).sort(sortNodes);
};
/**
 * Format Node
 */

var mergeNode = function mergeNode() {
  var prevNode = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var node = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

  var newNode = _objectSpread(_objectSpread(_objectSpread({}, defaultNode), prevNode), node);

  if ('module' === newNode.type && undefined === newNode.settings.type) {
    newNode.settings.type = prevNode.settings.type;
  }

  return newNode;
};
/**
 * Set the position of a given node, and increment the position of any trailing siblings.
 *
 * @return Object - all nodes of the same parent, including the target node.
 */

var insertExistingNodeAndResolvePositions = function insertExistingNodeAndResolvePositions(id, parent, position) {
  var nodes = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
  var updated = {};

  if (undefined === nodes[id]) {
    console.error('Undefined node', id);
    return updated;
  } // Grab all nodes of the same parent and reset positions


  var siblings = getChildNodes(nodes, parent).filter(function (sibling) {
    return id !== sibling.node;
  });
  var ids = siblings.map(function (sibling) {
    return sibling.node;
  }); // Insert target id into array

  ids.splice(position, 0, id); // Reset positions for all

  ids.map(function (nodeId, i) {
    updated[nodeId] = mergeNode(nodes[nodeId], {
      position: i,
      parent: parent
    });
  }); // Returns all affected nodes

  return updated;
};
var insertNewNodeAndResolvePositions = function insertNewNodeAndResolvePositions() {
  var nodes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var id = arguments.length > 1 ? arguments[1] : undefined;
  var newNode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

  var newState = _objectSpread(_objectSpread({}, nodes), {}, _defineProperty({}, id, mergeNode(nodes[id], newNode))); // Returns all affected nodes


  return insertExistingNodeAndResolvePositions(newNode.node, newNode.parent, newNode.position, newState);
};
var deleteChildren = function deleteChildren(id, nodes) {
  var toDelete = [];

  var newNodes = _objectSpread({}, nodes);

  Object.values(newNodes).map(function (node) {
    if (id === node.parent) {
      toDelete.push(node.node);
    }
  });
  toDelete.map(function (key) {
    delete newNodes[key];
  });
  return newNodes;
};
var deleteNodeAndResolvePositions = function deleteNodeAndResolvePositions(id, nodes) {
  var updated = {};

  if (undefined === nodes[id]) {
    console.warn('Node to be deleted is undefined', id);
    return nodes;
  }

  var parent = nodes[id].parent;
  var type = nodes[id].type; // Delete the target node

  var newState = _objectSpread({}, nodes);

  delete newState[id]; // Column nodes check for empty column-groups to delete too

  if ('column' === type && isNodeEmpty(parent, newState)) {
    delete newState[parent];
  } // Delete all child nodes


  deleteChildren(id, newState); // Reset sibling positions
  // Handle col size reset

  var siblings = getChildNodes(newState, parent);
  siblings.map(function (node, i) {
    node.position = i;

    if ('column' === node.type) {
      var size = 100 / siblings.length;
      size = size.toPrecision(5); // 3 decimal places

      node.settings.size = size;
    }

    updated[node.node] = node;
  });
  return _objectSpread({}, newState);
};
var resetColumnWidths = function resetColumnWidths() {
  var groupIds = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var state = arguments.length > 1 ? arguments[1] : undefined;
  var updated = {};
  groupIds.map(function (id) {
    var cols = Object.values(state).filter(function (node) {
      return id === node.parent;
    });
    var size = (100 / cols.length).toPrecision(5); // 3 decimal places

    cols.map(function (node) {
      updated[node.node] = mergeNode(node, {
        settings: _objectSpread(_objectSpread({}, node.settings), {}, {
          size: size
        })
      });
    });
  }); // Returns all affected nodes

  return updated;
};
var isNodeEmpty = function isNodeEmpty(id, state) {
  var children = Object.values(state).filter(function (node) {
    return node.parent === id;
  });
  return 0 === children.length;
};
var getOrphans = function getOrphans(nodes) {
  var keys = Object.keys(nodes);
  return Object.values(nodes).filter(function (node) {
    return null !== node.parent && !keys.includes(node.node);
  });
};
/**
 * Testing Utils
 */

var verifyNodeShape = function verifyNodeShape(node) {
  if (undefined === node) {
    return;
  } // Ensure properties


  console.assert('node' in node, 'Node has no id property');
  console.assert('type' in node, 'Node has no type property');
  console.assert('parent' in node, 'Node has no parent property');
  console.assert('position' in node, 'Node has no position property');
  console.assert('settings' in node, 'Node has no settings property');
  console.assert('global' in node, 'Node has no global property');

  if ('module' === node.type) {
    console.assert('type' in node.settings, 'Module settings should contain type property.');
  }

  if ('row' !== node.type) {
    console.assert(null !== node.parent, 'Only rows should have a null parent property');
  }
};
var verifyNodes = function verifyNodes(nodes) {
  Object.values(nodes).map(verifyNodeShape);
};
var verifyNoOrphans = function verifyNoOrphans(nodes) {
  var orphans = getOrphans(nodes);
  console.assert(0 === orphans.length, 'There should be no orphaned nodes', orphans);
};

/***/ }),

/***/ "./src/builder/data/registry/index.js":
/*!********************************************!*\
  !*** ./src/builder/data/registry/index.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getDispatch": () => (/* binding */ getDispatch),
/* harmony export */   "getSelectors": () => (/* binding */ getSelectors),
/* harmony export */   "getStore": () => (/* binding */ getStore),
/* harmony export */   "registerStore": () => (/* binding */ registerStore),
/* harmony export */   "useStore": () => (/* binding */ useStore)
/* harmony export */ });
/* harmony import */ var _beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @beaverbuilder/app-core */ "@beaverbuilder/app-core");
/* harmony import */ var _beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_0__);


var _createStoreRegistry = (0,_beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_0__.createStoreRegistry)(),
    registerStore = _createStoreRegistry.registerStore,
    useStore = _createStoreRegistry.useStore,
    getStore = _createStoreRegistry.getStore,
    getDispatch = _createStoreRegistry.getDispatch,
    getSelectors = _createStoreRegistry.getSelectors;



/***/ }),

/***/ "./src/builder/data/system/actions.js":
/*!********************************************!*\
  !*** ./src/builder/data/system/actions.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "displayPanel": () => (/* binding */ displayPanel),
/* harmony export */   "hideCurrentPanel": () => (/* binding */ hideCurrentPanel),
/* harmony export */   "registerPanel": () => (/* binding */ registerPanel),
/* harmony export */   "setColorScheme": () => (/* binding */ setColorScheme),
/* harmony export */   "setIsEditing": () => (/* binding */ setIsEditing),
/* harmony export */   "setShouldShowShortcuts": () => (/* binding */ setShouldShowShortcuts),
/* harmony export */   "togglePanel": () => (/* binding */ togglePanel)
/* harmony export */ });
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var setShouldShowShortcuts = function setShouldShowShortcuts(value) {
  return {
    type: 'SET_SHOULD_SHOW_SHORTCUTS',
    value: value
  };
};
var registerPanel = function registerPanel() {
  var handle = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'fl/untitled';
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var defaults = {
    label: '',
    root: null,
    render: function render() {
      return null;
    },

    /* Legacy Prop */
    className: null,
    routerProps: {},
    onHistoryChanged: function onHistoryChanged() {}
  };
  return {
    type: 'REGISTER_PANEL',
    handle: handle,
    options: _objectSpread(_objectSpread({}, defaults), options)
  };
};
var displayPanel = function displayPanel() {
  var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  return {
    type: 'SET_CURRENT_PANEL',
    name: name
  };
};
var togglePanel = function togglePanel() {
  var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  return {
    type: 'TOGGLE_PANEL',
    name: name
  };
};
var hideCurrentPanel = function hideCurrentPanel() {
  return {
    type: 'HIDE_CURRENT_PANEL'
  };
};
var setIsEditing = function setIsEditing() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  return {
    type: 'SET_IS_EDITING',
    value: value
  };
};
var setColorScheme = function setColorScheme() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'light';
  return {
    type: 'SET_COLOR_SCHEME',
    value: value
  };
};

/***/ }),

/***/ "./src/builder/data/system/effects.js":
/*!********************************************!*\
  !*** ./src/builder/data/system/effects.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "after": () => (/* binding */ after),
/* harmony export */   "before": () => (/* binding */ before)
/* harmony export */ });
/**
 * Effects that fire before an action.
 */
var before = {};
/**
 * Effects that fire after an action.
 */

var after = {
  TOGGLE_PANEL: function TOGGLE_PANEL(action, store) {
    var _store$getState = store.getState(),
        currentPanel = _store$getState.currentPanel;

    var html = document.querySelector('html');

    if (currentPanel) {
      FLBuilder._closePanel();
    }

    if ('assistant' === currentPanel) {
      html.classList.add('fl-builder-assistant-visible');
    } else {
      html.classList.remove('fl-builder-assistant-visible');
    }
  },
  HIDE_CURRENT_PANEL: function HIDE_CURRENT_PANEL() {
    var html = document.querySelector('html');
    html.classList.remove('fl-builder-assistant-visible');
  }
};

/***/ }),

/***/ "./src/builder/data/system/index.js":
/*!******************************************!*\
  !*** ./src/builder/data/system/index.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getSystemActions": () => (/* binding */ getSystemActions),
/* harmony export */   "getSystemConfig": () => (/* binding */ getSystemConfig),
/* harmony export */   "getSystemSelectors": () => (/* binding */ getSystemSelectors),
/* harmony export */   "getSystemState": () => (/* binding */ getSystemState),
/* harmony export */   "getSystemStore": () => (/* binding */ getSystemStore),
/* harmony export */   "useSystemState": () => (/* binding */ useSystemState)
/* harmony export */ });
/* harmony import */ var _registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../registry */ "./src/builder/data/registry/index.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./actions */ "./src/builder/data/system/actions.js");
/* harmony import */ var _reducers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./reducers */ "./src/builder/data/system/reducers.js");
/* harmony import */ var _effects__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./effects */ "./src/builder/data/system/effects.js");




var key = 'fl-builder/system';
(0,_registry__WEBPACK_IMPORTED_MODULE_0__.registerStore)(key, {
  actions: _actions__WEBPACK_IMPORTED_MODULE_1__,
  reducers: _reducers__WEBPACK_IMPORTED_MODULE_2__,
  effects: _effects__WEBPACK_IMPORTED_MODULE_3__,
  state: {
    isEditing: true,
    currentPanel: null,
    shouldShowShortcuts: false,
    colorScheme: FLBuilderConfig.userSettings.skin,
    panels: {}
  }
});
var useSystemState = function useSystemState() {
  return (0,_registry__WEBPACK_IMPORTED_MODULE_0__.useStore)(key);
};
var getSystemStore = function getSystemStore() {
  return (0,_registry__WEBPACK_IMPORTED_MODULE_0__.getStore)(key);
};
var getSystemState = function getSystemState() {
  return (0,_registry__WEBPACK_IMPORTED_MODULE_0__.getStore)(key).getState();
};
var getSystemActions = function getSystemActions() {
  return (0,_registry__WEBPACK_IMPORTED_MODULE_0__.getDispatch)(key);
};
var getSystemSelectors = function getSystemSelectors() {
  return (0,_registry__WEBPACK_IMPORTED_MODULE_0__.getSelectors)(key);
};
var getSystemConfig = function getSystemConfig() {
  return window.FLBuilderConfig;
};

/***/ }),

/***/ "./src/builder/data/system/reducers.js":
/*!*********************************************!*\
  !*** ./src/builder/data/system/reducers.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "colorScheme": () => (/* binding */ colorScheme),
/* harmony export */   "currentPanel": () => (/* binding */ currentPanel),
/* harmony export */   "isEditing": () => (/* binding */ isEditing),
/* harmony export */   "panels": () => (/* binding */ panels),
/* harmony export */   "shouldShowShortcuts": () => (/* binding */ shouldShowShortcuts)
/* harmony export */ });
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var shouldShowShortcuts = function shouldShowShortcuts() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_SHOULD_SHOW_SHORTCUTS':
      return action.value ? true : false;

    default:
      return state;
  }
};
var panels = function panels() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REGISTER_PANEL':
      return _objectSpread(_objectSpread({}, state), {}, _defineProperty({}, action.handle, action.options));

    default:
      return state;
  }
};
var currentPanel = function currentPanel() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_CURRENT_PANEL':
      return action.name;

    case 'HIDE_CURRENT_PANEL':
      return null;

    case 'TOGGLE_PANEL':
      return action.name === state ? null : action.name;

    default:
      return state;
  }
};
var isEditing = function isEditing() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_EDITING':
      return action.value ? true : false;

    default:
      return state;
  }
};
var colorScheme = function colorScheme() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'light';
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_COLOR_SCHEME':
      return 'dark' === action.value ? 'dark' : 'light';

    default:
      return state;
  }
};

/***/ }),

/***/ "./src/builder/ui/3rd-party/index.js":
/*!*******************************************!*\
  !*** ./src/builder/ui/3rd-party/index.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _query_monitor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./query-monitor */ "./src/builder/ui/3rd-party/query-monitor/index.js");

(0,_query_monitor__WEBPACK_IMPORTED_MODULE_0__.querymonitor)();

/***/ }),

/***/ "./src/builder/ui/3rd-party/query-monitor/index.js":
/*!*********************************************************!*\
  !*** ./src/builder/ui/3rd-party/query-monitor/index.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "querymonitor": () => (/* binding */ querymonitor)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/3rd-party/query-monitor/style.scss");


var querymonitor = function querymonitor() {
  FLBuilder.addHook('didInitUI', function () {
    var actions = document.querySelector('.fl-builder-bar-actions');
    var saving = actions.querySelector('.fl-builder--saving-indicator');
    var btn = document.createElement('button');
    btn.classList.add('fl-builder-button', 'fl-builder-button-silent');
    btn.innerHTML = '<svg width="20px" x="0px" y="0px" viewBox="0 0 238 238" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><g id="EMF-by-Xara-X" serif:id="EMF by Xara X"><path d="M170.734,183.655l-32.623,-35.483c-19.089,5.332 -40.624,6.6 -61.992,2.707c-54.118,-9.858 -86.793,-49.103 -72.935,-87.598c13.859,-38.494 69.029,-61.737 123.148,-51.878c7.553,1.375 14.687,3.324 21.334,5.762l-6.462,12.195c-5.266,-1.731 -10.922,-3.111 -16.913,-4.082c-42.267,-6.842 -85.356,9.291 -96.18,36.014c-10.824,26.722 14.695,53.963 56.963,60.808c41.535,6.724 83.865,-8.739 95.589,-34.637l13.176,22.511l0.064,0.113c-4.574,7.429 -10.792,14.102 -18.26,19.85l27.897,25.151c15.605,14.906 -15.073,47.906 -32.806,28.567Zm9.595,-26.401l-34.191,-30.641c-36.604,14.182 -70.685,6.876 -98.296,-5.59c23.399,17.456 58.534,24.774 94.542,14.959l30.526,29.179c1.989,-3.304 4.559,-6.305 7.419,-7.907Z"/><path d="M42.292,90.276l27.528,0l16.45,-26.827l34.049,52.376l44.83,-68.557l34.474,58.905l37.878,0l-29.225,-7.239l-43.694,-98.934l-43.696,82.469l-33.055,-51.241l-24.826,54.79l-20.713,4.258Z" style="fill:#1195d0;"/></g></svg>';

    btn.onclick = function () {
      return onClick();
    };

    btn.title = 'Query Monitor';

    if (jQuery('#query-monitor-main').length > 0) {
      actions.insertBefore(btn, saving);
    }
  });

  var onClick = function onClick() {
    var el = document.getElementById("query-monitor-main");

    if (!isHidden(el)) {
      var elem = document.getElementsByClassName("qm-button-container-close");
      elem[0].click();
    } else {
      var menu = document.getElementById("wp-admin-bar-query-monitor");
      elem = menu.getElementsByTagName('a');
      elem[0].click();
    }
  };

  var isHidden = function isHidden(el) {
    var style = window.getComputedStyle(el);
    return style.display === 'none';
  };
};

/***/ }),

/***/ "./src/builder/ui/art/index.js":
/*!*************************************!*\
  !*** ./src/builder/ui/art/index.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Icon": () => (/* binding */ Icon),
/* harmony export */   "SVGSymbols": () => (/* binding */ SVGSymbols)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/art/style.scss");


var SVGSymbols = function SVGSymbols() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    id: "fl-symbol-container",
    version: "1.1",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("symbol", {
    id: "fl-down-caret",
    viewBox: "0 0 11 6"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("polygon", {
    points: "0 0 2.05697559 0 5.49235478 3.74058411 8.93443824 0 11 0 5.5 6"
  })));
};
var Icon = function Icon() {};

Icon.Close = function () {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "14px",
    height: "14px",
    viewBox: "0 0 14 14",
    version: "1.1",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("g", {
    stroke: "currentColor",
    strokeWidth: "2",
    fill: "none",
    fillRule: "evenodd",
    strokeLinecap: "round"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M13,1 L1,13"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M1,1 L13,13"
  })));
};

/***/ }),

/***/ "./src/builder/ui/context-menu/context.js":
/*!************************************************!*\
  !*** ./src/builder/ui/context-menu/context.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ContextMenuContext": () => (/* binding */ ContextMenuContext),
/* harmony export */   "useContextMenu": () => (/* binding */ useContextMenu)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

var defaults = {};
var ContextMenuContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(defaults);
var useContextMenu = function useContextMenu() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(ContextMenuContext);
};

/***/ }),

/***/ "./src/builder/ui/context-menu/index.js":
/*!**********************************************!*\
  !*** ./src/builder/ui/context-menu/index.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ContextMenuProvider": () => (/* binding */ ContextMenuProvider),
/* harmony export */   "useContextMenu": () => (/* reexport safe */ _context__WEBPACK_IMPORTED_MODULE_1__.useContextMenu)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./context */ "./src/builder/ui/context-menu/context.js");
/* harmony import */ var _menu__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./menu */ "./src/builder/ui/context-menu/menu/index.js");
function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }





var ContextMenuProvider = function ContextMenuProvider(_ref) {
  var children = _ref.children;

  var _useState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false),
      _useState2 = _slicedToArray(_useState, 2),
      contextMenu = _useState2[0],
      setContextMenu = _useState2[1];

  var clearContextMenu = function clearContextMenu() {
    return setContextMenu(false);
  };

  var context = {
    setContextMenu: setContextMenu,
    clearContextMenu: clearContextMenu,
    showContextMenu: false !== contextMenu,
    contextMenu: contextMenu
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_context__WEBPACK_IMPORTED_MODULE_1__.ContextMenuContext.Provider, {
    value: context
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(DismissListener, null), children, false !== contextMenu && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_menu__WEBPACK_IMPORTED_MODULE_2__["default"], _extends({}, contextMenu, {
    clear: clearContextMenu
  })));
};

var DismissListener = function DismissListener() {
  var _useContextMenu = (0,_context__WEBPACK_IMPORTED_MODULE_1__.useContextMenu)(),
      clearContextMenu = _useContextMenu.clearContextMenu;

  var maybeDismissOnClick = function maybeDismissOnClick(e) {
    // This is a menu and you are not clicking within it.
    if (document.querySelector('.fl-builder-context-menu') && !e.target.closest('.fl-builder-context-menu')) {
      clearContextMenu();
      e.stopPropagation();
    }
  };

  var dismissMenuOnScroll = function dismissMenuOnScroll() {
    if (document.querySelector('.fl-builder-context-menu')) {
      clearContextMenu();
    }
  };

  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
    window.addEventListener('click', maybeDismissOnClick, {
      capture: true
    });
    window.addEventListener('scroll', dismissMenuOnScroll, {
      capture: true
    }); // Return a remover fn

    return function () {
      window.removeEventListener('click', maybeDismissOnClick, {
        capture: true
      });
      window.removeEventListener('scroll', dismissMenuOnScroll, {
        capture: true
      });
    };
  }, []);
  return null;
};



/***/ }),

/***/ "./src/builder/ui/context-menu/menu/index.js":
/*!***************************************************!*\
  !*** ./src/builder/ui/context-menu/menu/index.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _beaverbuilder_fluid__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @beaverbuilder/fluid */ "@beaverbuilder/fluid");
/* harmony import */ var _beaverbuilder_fluid__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_beaverbuilder_fluid__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/context-menu/menu/style.scss");
var _excluded = ["onClick", "label"];

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }





var ContextMenu = function ContextMenu(_ref) {
  var x = _ref.x,
      y = _ref.y,
      _ref$items = _ref.items,
      items = _ref$items === void 0 ? {} : _ref$items,
      _ref$clear = _ref.clear,
      clear = _ref$clear === void 0 ? function () {} : _ref$clear;
  var menuWidth = 180;
  var edgeBuffer = 10;
  var maxX = window.innerWidth - (menuWidth + edgeBuffer); // Reduce to just the items that are enabled

  var enabledItems = {};
  Object.entries(items).map(function (_ref2) {
    var _ref3 = _slicedToArray(_ref2, 2),
        key = _ref3[0],
        item = _ref3[1];

    if (false === item.isEnabled) {
      return;
    }

    enabledItems[key] = item;
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: "fl-builder-context-menu",
    style: {
      top: y,

      /* Subtract the top bar */
      left: x < maxX ? x : maxX,
      width: menuWidth
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("ul", null, Object.keys(enabledItems).map(function (key) {
    var _items$key = items[key],
        _onClick = _items$key.onClick,
        label = _items$key.label,
        rest = _objectWithoutProperties(_items$key, _excluded);

    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("li", {
      key: key
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_beaverbuilder_fluid__WEBPACK_IMPORTED_MODULE_1__.Button, _extends({
      onClick: function onClick(e) {
        _onClick(e);

        clear();
        e.stopPropagation();
      },
      size: "sm"
    }, rest), label));
  })));
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ContextMenu);

/***/ }),

/***/ "./src/builder/ui/index.js":
/*!*********************************!*\
  !*** ./src/builder/ui/index.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   "registerPanels": () => (/* binding */ registerPanels)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");
/* harmony import */ var _context_menu__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./context-menu */ "./src/builder/ui/context-menu/index.js");
/* harmony import */ var _notifications__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./notifications */ "./src/builder/ui/notifications/index.js");
/* harmony import */ var _inline_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./inline-editor */ "./src/builder/ui/inline-editor/index.js");
/* harmony import */ var _shortcuts_panel__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./shortcuts-panel */ "./src/builder/ui/shortcuts-panel/index.js");
/* harmony import */ var _art__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./art */ "./src/builder/ui/art/index.js");
/* harmony import */ var _outline_panel__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./outline-panel */ "./src/builder/ui/outline-panel/index.js");
/* harmony import */ var _panel_manager__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./panel-manager */ "./src/builder/ui/panel-manager/index.js");
/* harmony import */ var _3rd_party__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./3rd-party */ "./src/builder/ui/3rd-party/index.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/style.scss");
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }













/**
 * Builder React-based UI Root
 *
 * Gets rendered onto the page and remains.
 */

var BeaverBuilderUI = function BeaverBuilderUI() {
  var _useSystemState = (0,data__WEBPACK_IMPORTED_MODULE_2__.useSystemState)(),
      isEditing = _useSystemState.isEditing,
      shouldShowShortcuts = _useSystemState.shouldShowShortcuts,
      colorScheme = _useSystemState.colorScheme;

  var wrap = classnames__WEBPACK_IMPORTED_MODULE_1___default()(_defineProperty({}, "fluid-color-scheme-".concat(colorScheme), colorScheme));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: wrap
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_context_menu__WEBPACK_IMPORTED_MODULE_3__.ContextMenuProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_inline_editor__WEBPACK_IMPORTED_MODULE_5__["default"], null), isEditing && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement((react__WEBPACK_IMPORTED_MODULE_0___default().Fragment), null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_art__WEBPACK_IMPORTED_MODULE_7__.SVGSymbols, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_notifications__WEBPACK_IMPORTED_MODULE_4__.NotificationsManager, null), shouldShowShortcuts && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_shortcuts_panel__WEBPACK_IMPORTED_MODULE_6__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_panel_manager__WEBPACK_IMPORTED_MODULE_9__["default"], null))));
};

var registerPanels = function registerPanels() {
  var _getSystemConfig = (0,data__WEBPACK_IMPORTED_MODULE_2__.getSystemConfig)(),
      _getSystemConfig$show = _getSystemConfig.showOutlinePanel,
      showOutlinePanel = _getSystemConfig$show === void 0 ? true : _getSystemConfig$show;

  if (showOutlinePanel) {
    (0,_outline_panel__WEBPACK_IMPORTED_MODULE_8__.registerOutlinePanel)();
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (BeaverBuilderUI);

/***/ }),

/***/ "./src/builder/ui/inline-editor/index.js":
/*!***********************************************!*\
  !*** ./src/builder/ui/inline-editor/index.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/inline-editor/style.scss");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



/**
 * Handles inline editing for builder layouts.
 *
 * @since 2.1
 * @class InlineEditor
 */

var InlineEditor = /*#__PURE__*/function (_Component) {
  _inherits(InlineEditor, _Component);

  var _super = _createSuper(InlineEditor);

  function InlineEditor(props) {
    var _this;

    _classCallCheck(this, InlineEditor);

    _this = _super.call(this, props);
    var postId = _this.props.postId;
    _this.layoutClass = ".fl-builder-content-".concat(postId ? postId : FLBuilderConfig.postId);
    return _this;
  }

  _createClass(InlineEditor, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.setupHooks = this.setupHooks.bind(this);
      this.hooked = false;
      jQuery(document).on('tinymce-editor-init', this.setupHooks);
      this.setupHooks();
    }
  }, {
    key: "setupHooks",
    value: function setupHooks() {
      if ('ontouchstart' in document) {
        return;
      }

      if (!window.tinymce || this.hooked || !FLBuilderConfig.inlineEnabled) {
        return;
      }

      var initEditables = this.initEditables.bind(this);
      var refreshEditables = this.refreshEditables.bind(this); //const destroyEditables = this.destroyEditables.bind( this )

      var destroyAllEditables = this.destroyAllEditables.bind(this);
      var destroyLoadingEditables = this.destroyLoadingEditables.bind(this);

      if (FLBuilder) {
        // Init actions
        FLBuilder.addHook('settingsConfigLoaded', initEditables);
        FLBuilder.addHook('restartEditingSession', initEditables); // Destroy actions

        FLBuilder.addHook('endEditingSession', destroyAllEditables);
        FLBuilder.addHook('didStartNodeLoading', destroyLoadingEditables); // Refresh actions

        FLBuilder.addHook('didRenderLayoutComplete', refreshEditables);
        FLBuilder.addHook('didDeleteRow', refreshEditables);
        FLBuilder.addHook('didDeleteColumn', refreshEditables);
        FLBuilder.addHook('didDeleteModule', refreshEditables);
      }

      this.initEditables();
      this.hooked = true;
    }
  }, {
    key: "initEditables",
    value: function initEditables() {
      var _this2 = this;

      var _FLBuilderSettingsCon = FLBuilderSettingsConfig,
          editables = _FLBuilderSettingsCon.editables;
      var content = jQuery(this.layoutClass);

      if (content.length) {
        for (var key in editables) {
          var selector = ".fl-module[data-type=\"".concat(key, "\"]:not(.fl-editable):not(.fl-node-global)");
          content.find(selector).each(function (index, module) {
            module = jQuery(module);
            module.addClass('fl-editable');
            module.delegate('.fl-block-overlay', 'click.fl-inline-editing-init', function (e) {
              return _this2.initEditable(e, module);
            });
          });
        }
      }
    }
  }, {
    key: "initEditable",
    value: function initEditable(e, module) {
      var _this3 = this;

      var _FLBuilder = FLBuilder,
          preview = _FLBuilder.preview; // Don't setup if we have a parent that needs to save.

      if (preview) {
        var isParent = module.parents(".fl-node-".concat(preview.nodeId)).length;

        if (isParent && preview._settingsHaveChanged()) {
          return;
        }
      }

      this.setupEditable(module, function () {
        _this3.onModuleOverlayClick(e);
      });
      module.undelegate('.fl-block-overlay', 'click.fl-inline-editing-init');
    }
  }, {
    key: "setupEditable",
    value: function setupEditable(module) {
      var _this4 = this;

      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : function () {};
      var nodeId = module.data('node');
      var settings = FLBuilderSettingsConfig.nodes[nodeId];

      if ('undefined' === typeof settings) {
        return false;
      }

      var type = module.data('type');
      var config = FLBuilderSettingsConfig.editables[type];
      var nodeClass = ".fl-node-".concat(nodeId, " ");
      var editorId = "fl-inline-editor-".concat(nodeId);
      var overlay = jQuery("<div id=\"".concat(editorId, "\" class=\"fl-inline-editor\"></div>"));
      var form = jQuery(".fl-builder-settings[data-node=".concat(nodeId, "]"));
      var connections = settings.connections;
      module.append(overlay);
      module.on('click', '.fl-block-overlay', this.onModuleOverlayClick.bind(this));
      module.on('mouseleave', this.onModuleMouseleave.bind(this));

      var _loop = function _loop(key) {
        var data = config[key];
        var selector = FLBuilderPreview.getFormattedSelector(nodeClass, data.selector);
        var editable = jQuery(selector);
        var editableHTML = editable.html();
        var connection = form.find("#fl-field-".concat(key, " .fl-field-connection-value"));

        if (!editable.length) {
          return "continue";
        } else if (connection.length && '' !== connection.val()) {
          return "continue";
        } else if (!connection.length && connections && connections[key]) {
          return "continue";
        }

        if (editable.hasClass('mce-content-body')) {
          tinymce.execCommand('mceRemoveEditor', true, editable.attr('id'));
        } else {
          editable.data('field', data.field);
          editable.on('drop', _this4.onEditorDrop.bind(_this4));
        }

        tinymce.init({
          selector: selector,
          inline: true,
          menubar: false,
          paste_as_text: true,
          relative_urls: false,
          convert_urls: false,
          skin: FLBuilder ? false : 'lightgray',
          skin_url: FLBuilder ? false : "".concat(tinyMCEPreInit.baseURL, "/skins/lightgray/"),
          theme: 'modern',
          theme_url: "".concat(tinyMCEPreInit.baseURL, "/themes/modern/"),
          fixed_toolbar_container: "#".concat(editorId),
          plugins: _this4.getEditorPluginConfig(data.field.type),
          toolbar: 'string' === typeof data.field.toolbar ? data.field.toolbar : _this4.getEditorToolbarConfig(data.field.type),
          init_instance_callback: function init_instance_callback(editor) {
            _this4.onEditorInit(editor);
            /**
             * TinyMCE can change the editable's HTML which changes the visual
             * appearance. To prevent this from happening, we reinsert the original
             * HTML after the editable has been initialized.
             */


            editable.html(editableHTML);
            callback();
          }
        });
      };

      for (var key in config) {
        var _ret = _loop(key);

        if (_ret === "continue") continue;
      }
    }
  }, {
    key: "getEditorPluginConfig",
    value: function getEditorPluginConfig(type) {
      switch (type) {
        case 'editor':
          return 'wordpress, wplink, lists, paste';

        default:
          return 'paste';
      }
    }
  }, {
    key: "getEditorToolbarConfig",
    value: function getEditorToolbarConfig(type) {
      switch (type) {
        case 'editor':
          return 'bold italic strikethrough link underline | alignleft aligncenter alignright';

        case 'unit':
          return false;

        default:
          return 'bold italic strikethrough underline';
      }
    }
  }, {
    key: "destroyEditables",
    value: function destroyEditables(modules) {
      var editables = modules.find('.mce-content-body');
      var overlays = modules.find('.fl-inline-editor');
      var extras = jQuery('.wplink-autocomplete, .ui-helper-hidden-accessible');
      editables.removeAttr('contenteditable');
      modules.undelegate('.fl-block-overlay', 'click');
      modules.off('mouseleave');
      modules.removeClass('fl-editable');
      overlays.remove();
      extras.remove();
    }
  }, {
    key: "destroyAllEditables",
    value: function destroyAllEditables() {
      var content = jQuery(this.layoutClass);
      var modules = content.find('.fl-editable');
      this.destroyEditables(modules);
    }
  }, {
    key: "destroyLoadingEditables",
    value: function destroyLoadingEditables(e, node) {
      var modules = jQuery(node);

      if (!modules.hasClass('fl-module')) {
        modules = modules.find('.fl-module');
      }

      this.destroyEditables(modules);
    }
  }, {
    key: "refreshEditables",
    value: function refreshEditables() {
      this.initEditables();
      tinymce.editors.map(function (editor) {
        if (editor.inline && !jQuery("#".concat(editor.id)).length) {
          setTimeout(function () {
            return tinymce.execCommand('mceRemoveEditor', true, editor.id);
          }, 1);
        }
      });
    }
  }, {
    key: "getEditorEventVars",
    value: function getEditorEventVars(target) {
      var editable = jQuery(target).closest('.mce-content-body');
      var editor = tinymce.get(editable.attr('id'));
      var field = editable.data('field');
      var module = editable.closest('.fl-module');
      var nodeId = module.data('node');
      return {
        editable: editable,
        module: module,
        editor: editor,
        field: field,
        nodeId: nodeId
      };
    }
  }, {
    key: "onEditorInit",
    value: function onEditorInit(editor) {
      editor.on('change', this.onEditorChange.bind(this));
      editor.on('keyup', this.onEditorChange.bind(this));
      editor.on('undo', this.onEditorChange.bind(this));
      editor.on('redo', this.onEditorChange.bind(this));
      editor.on('focus', this.onEditorFocus.bind(this));
      editor.on('blur', this.onEditorBlur.bind(this));
      editor.on('mousedown', this.onEditorMousedown.bind(this));
    }
  }, {
    key: "onEditorChange",
    value: function onEditorChange(e) {
      var target = e.target.bodyElement ? e.target.bodyElement : e.target;

      var _this$getEditorEventV = this.getEditorEventVars(target),
          editor = _this$getEditorEventV.editor,
          field = _this$getEditorEventV.field,
          nodeId = _this$getEditorEventV.nodeId;

      var settings = jQuery(".fl-builder-settings[data-node=\"".concat(nodeId, "\"]"));
      var content = editor.getContent();

      if (!settings.length || !field) {
        return;
      } else if ('editor' === field.type) {
        var textarea = settings.find("#fl-field-".concat(field.name, " textarea.wp-editor-area"));
        var editorId = textarea.attr('id');

        if (textarea.closest('.tmce-active').length) {
          tinymce.get(editorId).setContent(content);
        } else {
          textarea.val(content);
        }
      } else {
        var _textarea = document.createElement('textarea');

        _textarea.innerHTML = content;
        settings.find("[name=\"".concat(field.name, "\"]")).val(_textarea.value);
      }
    }
  }, {
    key: "onEditorFocus",
    value: function onEditorFocus(e) {
      var _this$getEditorEventV2 = this.getEditorEventVars(e.target.bodyElement),
          editable = _this$getEditorEventV2.editable,
          editor = _this$getEditorEventV2.editor,
          module = _this$getEditorEventV2.module,
          field = _this$getEditorEventV2.field,
          nodeId = _this$getEditorEventV2.nodeId;

      var overlay = module.find('.fl-inline-editor');
      var settingHTML = this.getSettingHTML(nodeId, field);

      if (!this.matchHTML(editor.getContent(), settingHTML)) {
        editable.data('original', {
          settingHTML: settingHTML,
          editableHTML: editable.html()
        });
        editable.css('min-height', editable.height());
        editor.setContent(settingHTML);
        editor.selection.select(editor.getBody(), true);
        editor.selection.collapse(false);
      }

      if (editor.settings.toolbar) {
        overlay.removeClass('fl-inline-editor-no-toolbar');
      } else {
        overlay.addClass('fl-inline-editor-no-toolbar');
      }

      module.addClass('fl-editable-focused');
      this.showEditorOverlay(module);
      this.showModuleSettings(module);
    }
  }, {
    key: "onEditorBlur",
    value: function onEditorBlur(e) {
      var _this$getEditorEventV3 = this.getEditorEventVars(e.target.bodyElement),
          editable = _this$getEditorEventV3.editable,
          editor = _this$getEditorEventV3.editor,
          module = _this$getEditorEventV3.module;

      var overlay = module.find('.fl-inline-editor');
      var original = editable.data('original');
      overlay.removeClass('fl-inline-editor-no-toolbar');
      module.removeClass('fl-editable-focused');

      if (original && this.matchHTML(editor.getContent(), original.settingHTML)) {
        editable.html(original.editableHTML);
        editable.css('min-height', '');
      }
    }
  }, {
    key: "onEditorMousedown",
    value: function onEditorMousedown(e) {
      var _this$getEditorEventV4 = this.getEditorEventVars(e.target),
          module = _this$getEditorEventV4.module;

      this.showEditorOverlay(module);
    }
  }, {
    key: "onEditorDrop",
    value: function onEditorDrop(e) {
      e.preventDefault();
      return false;
    }
  }, {
    key: "onModuleOverlayClick",
    value: function onModuleOverlayClick(e) {
      var actions = jQuery(e.target).closest('.fl-block-overlay-actions');
      var module = jQuery(e.currentTarget).closest('.fl-module');
      var editorId = module.find('.mce-content-body').first().attr('id');

      if (actions.length || FLBuilder._colResizing) {
        return;
      }

      if (editorId) {
        tinymce.get(editorId).focus();
        module.addClass('fl-editable-focused');
      }
    }
  }, {
    key: "onModuleMouseleave",
    value: function onModuleMouseleave() {
      var panels = jQuery('.mce-inline-toolbar-grp:visible, .mce-floatpanel:visible');

      if (!panels.length) {
        this.hideEditorOverlays();
        this.showNodeOverlays();
      }
    }
  }, {
    key: "showEditorOverlay",
    value: function showEditorOverlay(module) {
      var overlay = module.find('.fl-inline-editor');
      this.hideNodeOverlays();
      this.hideEditorOverlays();
      overlay.show();
      var active = jQuery('.fl-inline-editor-active-toolbar');
      active.removeClass('fl-inline-editor-active-toolbar');
      var toolbar = overlay.find('> .mce-panel:visible');
      toolbar.addClass('fl-inline-editor-active-toolbar');
    }
  }, {
    key: "hideEditorOverlays",
    value: function hideEditorOverlays() {
      jQuery('.fl-inline-editor, .mce-floatpanel').hide();
    }
  }, {
    key: "showNodeOverlays",
    value: function showNodeOverlays() {
      jQuery('.fl-block-overlay').show();
    }
  }, {
    key: "hideNodeOverlays",
    value: function hideNodeOverlays() {
      jQuery('.fl-block-overlay').hide();
    }
  }, {
    key: "showModuleSettings",
    value: function showModuleSettings(module) {
      var type = module.data('type');
      var nodeId = module.data('node');
      var parentId = module.closest('.fl-col').data('node');
      var global = module.hasClass('fl-node-global');
      var settings = jQuery(".fl-builder-settings[data-node=\"".concat(nodeId, "\"]"));

      if (!settings.length) {
        FLBuilder._showModuleSettings({
          type: type,
          nodeId: nodeId,
          parentId: parentId,
          global: global
        });
      }
    }
  }, {
    key: "getSettingValue",
    value: function getSettingValue(nodeId, name) {
      var form = jQuery(".fl-builder-settings[data-node=\"".concat(nodeId, "\"]"));
      var settings = {};

      if (form.length) {
        settings = FLBuilder._getSettings(form);
      } else {
        settings = FLBuilderSettingsConfig.nodes[nodeId];
      }

      return settings[name];
    }
  }, {
    key: "getSettingHTML",
    value: function getSettingHTML(nodeId, field) {
      var html = this.getSettingValue(nodeId, field.name);

      if ('editor' === field.type && '' !== html) {
        return wp.editor.autop(html);
      }

      return html;
    }
  }, {
    key: "matchHTML",
    value: function matchHTML(a, b) {
      return this.cleanHTML(a) === this.cleanHTML(b);
    }
  }, {
    key: "cleanHTML",
    value: function cleanHTML(html) {
      var re = /(\r\n|\n|\r)/gm;
      return jQuery("<div>".concat(html, "</div>")).html().trim().replace(re, '');
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return InlineEditor;
}(react__WEBPACK_IMPORTED_MODULE_0__.Component);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (InlineEditor);

/***/ }),

/***/ "./src/builder/ui/notifications/index.js":
/*!***********************************************!*\
  !*** ./src/builder/ui/notifications/index.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "NotificationsManager": () => (/* binding */ NotificationsManager)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/notifications/style.scss");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }





var renderHTML = function renderHTML(rawHTML) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement('div', {
    dangerouslySetInnerHTML: {
      __html: rawHTML
    }
  });
};

var lite = FLBuilderConfig.lite;

var Post = function Post(props) {
  var html = {
    __html: props.children
  },
      date = new Date(props.date).toDateString();
  var post;

  if ('string' === typeof props.url && '' !== props.url) {
    var url = lite ? props.url + '?utm_medium=bb-lite&utm_source=builder-ui&utm_campaign=notification-center' : props.url + '?utm_medium=bb-pro&utm_source=builder-ui&utm_campaign=notification-center';
    post = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("a", {
      className: "fl-builder-ui-post",
      href: url,
      target: "_blank",
      rel: "noopener noreferrer"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      className: "fl-builder-ui-post-date"
    }, date), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      className: "fl-builder-ui-post-title"
    }, props.title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      className: "fl-builder-ui-post-content",
      dangerouslySetInnerHTML: html
    }));
  } else {
    post = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", {
      className: "fl-builder-ui-post"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      className: "fl-builder-ui-post-date"
    }, date), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      className: "fl-builder-ui-post-title"
    }, props.title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      className: "fl-builder-ui-post-content",
      dangerouslySetInnerHTML: html
    }));
  }

  return post;
};
/**
 * Notifications Sidebar Panel
 * Displayed when toggleNotifications hook is fired
 */


var NotificationsPanel = /*#__PURE__*/function (_Component) {
  _inherits(NotificationsPanel, _Component);

  var _super = _createSuper(NotificationsPanel);

  function NotificationsPanel() {
    _classCallCheck(this, NotificationsPanel);

    return _super.apply(this, arguments);
  }

  _createClass(NotificationsPanel, [{
    key: "getPosts",
    value: function getPosts(posts) {
      var view,
          renderedPosts,
          strings = FLBuilderStrings.notifications;

      if (0 < posts.length) {
        renderedPosts = posts.map(function (item) {
          return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Post, {
            key: item.id,
            title: renderHTML(item.title.rendered),
            date: item.date,
            url: item.meta._fl_notification[0]
          }, item.content.rendered);
        });
        view = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement((react__WEBPACK_IMPORTED_MODULE_0___default().Fragment), null, renderedPosts);
      } else {
        view = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
          className: "fl-panel-no-message"
        }, strings.none);
      }

      return view;
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      FLBuilder._initScrollbars();
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate() {
      FLBuilder._initScrollbars();
    }
  }, {
    key: "render",
    value: function render() {
      var content = this.getPosts(this.props.posts),
          strings = FLBuilderStrings.notifications;
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
        className: "fl-notifications-panel"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
        className: "fl-panel-title"
      }, strings.title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
        className: "fl-nanoscroller",
        ref: this.setupScroller
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
        className: "fl-nanoscroller-content"
      }, content)));
    }
  }]);

  return NotificationsPanel;
}(react__WEBPACK_IMPORTED_MODULE_0__.Component);
/**
* Non-UI Manager Object. Handles state for the notifications system
*/


var NotificationsManager = /*#__PURE__*/function (_Component2) {
  _inherits(NotificationsManager, _Component2);

  var _super2 = _createSuper(NotificationsManager);

  function NotificationsManager(props) {
    var _this;

    _classCallCheck(this, NotificationsManager);

    _this = _super2.call(this, props);
    var out = {};
    var data = FLBuilderConfig.notifications.data; // make sure we have valid json.

    try {
      out = JSON.parse(data);
    } catch (e) {
      out = {};
    }

    _this.state = {
      shouldShowNotifications: false,
      posts: out
    };
    FLBuilder.addHook('toggleNotifications', _this.onToggleNotifications.bind(_assertThisInitialized(_this)));
    return _this;
  }

  _createClass(NotificationsManager, [{
    key: "onToggleNotifications",
    value: function onToggleNotifications() {
      var _getSystemActions = (0,data__WEBPACK_IMPORTED_MODULE_1__.getSystemActions)(),
          hideCurrentPanel = _getSystemActions.hideCurrentPanel;

      this.setState({
        shouldShowNotifications: !this.state.shouldShowNotifications
      });
      hideCurrentPanel();

      if (true === this.state.shouldShowNotifications) {
        FLBuilder.triggerHook('notificationsLoaded');
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$state = this.state,
          shouldShowNotifications = _this$state.shouldShowNotifications,
          posts = _this$state.posts;
      return shouldShowNotifications && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(NotificationsPanel, {
        posts: posts
      });
    }
  }]);

  return NotificationsManager;
}(react__WEBPACK_IMPORTED_MODULE_0__.Component);

/***/ }),

/***/ "./src/builder/ui/outline-panel/index.js":
/*!***********************************************!*\
  !*** ./src/builder/ui/outline-panel/index.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "registerOutlinePanel": () => (/* binding */ registerOutlinePanel)
/* harmony export */ });
/* harmony import */ var _outline__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./outline */ "./src/builder/ui/outline-panel/outline/index.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/outline-panel/style.scss");


var registerOutlinePanel = function registerOutlinePanel() {
  var _window$FL$Builder = window.FL.Builder,
      registerPanel = _window$FL$Builder.registerPanel,
      togglePanel = _window$FL$Builder.togglePanel;
  registerPanel('outline', {
    label: 'Outline',
    render: _outline__WEBPACK_IMPORTED_MODULE_0__["default"],
    // legacy
    root: _outline__WEBPACK_IMPORTED_MODULE_0__["default"] // asst compat branch changes to root

  });
  FLBuilder.addHook('didInitUI', function () {
    var actions = document.querySelector('.fl-builder-bar-actions');
    var saving = actions.querySelector('.fl-builder--saving-indicator');
    var btn = document.createElement('button');
    btn.classList.add('fl-builder-button', 'fl-builder-button-silent');
    btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.38672 5.33984C2.1582 5.33984 2.77344 4.72461 2.77344 3.95312C2.77344 3.19141 2.1582 2.56641 1.38672 2.56641C0.625 2.56641 0 3.19141 0 3.95312C0 4.72461 0.625 5.33984 1.38672 5.33984ZM5.97656 4.89062H14.0565C14.5838 4.89062 15.0038 4.48047 15.0038 3.95312C15.0038 3.42578 14.5936 3.01562 14.0565 3.01562H5.97656C5.45898 3.01562 5.03906 3.42578 5.03906 3.95312C5.03906 4.48047 5.44922 4.89062 5.97656 4.89062ZM3.88672 11.3457C4.64844 11.3457 5.27344 10.7305 5.27344 9.95898C5.27344 9.19727 4.64844 8.57227 3.88672 8.57227C3.11523 8.57227 2.49023 9.19727 2.49023 9.95898C2.49023 10.7305 3.11523 11.3457 3.88672 11.3457ZM8.47656 10.8965H16.5794C17.1068 10.8965 17.5169 10.4863 17.5169 9.95898C17.5169 9.43164 17.1068 9.02148 16.5794 9.02148H8.47656C7.94922 9.02148 7.53906 9.43164 7.53906 9.95898C7.53906 10.4863 7.94922 10.8965 8.47656 10.8965ZM6.37695 17.3516C7.14844 17.3516 7.76367 16.7363 7.76367 15.9648C7.76367 15.2031 7.14844 14.5781 6.37695 14.5781C5.61523 14.5781 4.99023 15.2031 4.99023 15.9648C4.99023 16.7363 5.61523 17.3516 6.37695 17.3516ZM10.9668 16.9023H19.0251C19.5524 16.9023 19.9626 16.4922 19.9626 15.9648C19.9626 15.4375 19.5524 15.0273 19.0251 15.0273H10.9668C10.4395 15.0273 10.0293 15.4375 10.0293 15.9648C10.0293 16.4922 10.4395 16.9023 10.9668 16.9023Z" fill="currentColor"/></svg>';

    btn.onclick = function () {
      return togglePanel('outline');
    };

    btn.title = 'Outline';
    actions.insertBefore(btn, saving);
  });
};

/***/ }),

/***/ "./src/builder/ui/outline-panel/outline/context.js":
/*!*********************************************************!*\
  !*** ./src/builder/ui/outline-panel/outline/context.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

var OutlineContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)({});

OutlineContext.use = function () {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(OutlineContext);
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (OutlineContext);

/***/ }),

/***/ "./src/builder/ui/outline-panel/outline/index.js":
/*!*******************************************************!*\
  !*** ./src/builder/ui/outline-panel/outline/index.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var ui_context_menu__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ui/context-menu */ "./src/builder/ui/context-menu/index.js");
/* harmony import */ var api__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! api */ "./src/builder/api/index.js");
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");
/* harmony import */ var data_layout_utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! data/layout/utils */ "./src/builder/data/layout/utils/index.js");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./context */ "./src/builder/ui/outline-panel/outline/context.js");
/* harmony import */ var _tiny_icons__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./tiny-icons */ "./src/builder/ui/outline-panel/outline/tiny-icons/index.js");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./utils */ "./src/builder/ui/outline-panel/outline/utils/index.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/outline-panel/outline/style.scss");
var _excluded = ["id", "type"],
    _excluded2 = ["children", "style"];

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }












/**
 * Root Outline Component
 */

var Outline = function Outline() {
  /**
   * Get the top-level nodes to map over
   */
  var _getLayoutHooks = (0,data__WEBPACK_IMPORTED_MODULE_5__.getLayoutHooks)(),
      useNodesWithoutSettings = _getLayoutHooks.useNodesWithoutSettings;

  var topLevelNodes = useNodesWithoutSettings(null);
  var nodes = Object.values(topLevelNodes).sort(data_layout_utils__WEBPACK_IMPORTED_MODULE_6__.sortNodes);
  /**
   * Keep track of any node being dragged currently.
   */

  var _useState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false),
      _useState2 = _slicedToArray(_useState, 2),
      draggingItem = _useState2[0],
      _setDraggingItem = _useState2[1];

  var isDraggingItem = false !== draggingItem;
  /**
   * Expose dragging item via OutlineContext
   */

  var context = {
    draggingItem: draggingItem,
    isDraggingItem: isDraggingItem,
    clearDraggingItem: function clearDraggingItem() {
      return _setDraggingItem(false);
    },
    setDraggingItem: function setDraggingItem(item) {
      return _setDraggingItem(item);
    }
  };
  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()('fl-builder-node-outline', _defineProperty({
    'is-dragging': isDraggingItem
  }, "is-dragging-type-".concat(draggingItem.type), draggingItem));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_context__WEBPACK_IMPORTED_MODULE_7__["default"].Provider, {
    value: context
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("ul", {
    className: classes
  }, nodes.map(function (node, i) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Item, _extends({
      key: node.node,
      level: 1,
      index: i
    }, node));
  })));
};
/**
 * Generic Outline Item
 * Represents any kind of node.
 */


var Item = function Item(_ref) {
  var _c2;

  var id = _ref.node,
      index = _ref.index,
      level = _ref.level,
      type = _ref.type,
      _ref$global = _ref.global,
      global = _ref$global === void 0 ? false : _ref$global,
      parent = _ref.parent;

  var _getActions = (0,api__WEBPACK_IMPORTED_MODULE_4__.getActions)(),
      moveNode = _getActions.moveNode;

  var _getLayoutHooks2 = (0,data__WEBPACK_IMPORTED_MODULE_5__.getLayoutHooks)(),
      useNodesWithoutSettings = _getLayoutHooks2.useNodesWithoutSettings;

  var children = useNodesWithoutSettings(id);
  var hasChildren = 0 < Object.keys(children).length;
  /**
   * Drag info
   */

  var _OutlineContext$use = _context__WEBPACK_IMPORTED_MODULE_7__["default"].use(),
      draggingItem = _OutlineContext$use.draggingItem,
      setDraggingItem = _OutlineContext$use.setDraggingItem,
      clearDraggingItem = _OutlineContext$use.clearDraggingItem;

  var _useState3 = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false),
      _useState4 = _slicedToArray(_useState3, 2),
      isDraggingOver = _useState4[0],
      setIsDraggingOver = _useState4[1];

  var _useState5 = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true),
      _useState6 = _slicedToArray(_useState5, 2),
      showContent = _useState6[0],
      setShowContent = _useState6[1];

  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()('fl-builder-node-outline-item', (_c2 = {}, _defineProperty(_c2, "fl-builder-node-type-".concat(type), type), _defineProperty(_c2, 'show-drop-before', 'before' === isDraggingOver), _defineProperty(_c2, 'show-drop-after', 'after' === isDraggingOver), _defineProperty(_c2, 'is-dragging-self', id === (draggingItem === null || draggingItem === void 0 ? void 0 : draggingItem.id)), _c2));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("li", {
    className: classes,
    style: {
      '--level': level
    },
    draggable: (0,_utils__WEBPACK_IMPORTED_MODULE_9__.isDraggable)(type),
    onDragStart: function onDragStart(e) {
      // Required for draggable DOM elements
      e.stopPropagation(); // Setup drag data

      e.dataTransfer.setDragImage((0,_utils__WEBPACK_IMPORTED_MODULE_9__.getTransparentImg)(), 0, 0);
      e.dataTransfer.setData(type, id);
      e.dataTransfer.setData('node-id', id);
      e.dataTransfer.setData('node-type', type); // Set the item data on the root OutlineContext

      setDraggingItem({
        id: id,
        type: type
      });
    },
    onDragEnd: function onDragEnd() {
      return clearDraggingItem();
    },
    onDragOver: function onDragOver(e) {
      e.preventDefault();
      e.stopPropagation();
      e.dataTransfer.dropEffect = 'move'; // Double check we have the right element

      if (!e.currentTarget.classList.contains('fl-builder-node-outline-item')) {
        console.warn('Problem: Something other than fl-builder-node-outline-item-content');
        return;
      } // Abort if we're not dragging a type that can be dropped here.


      if (!(0,_utils__WEBPACK_IMPORTED_MODULE_9__.shouldAllowDrop)(e.dataTransfer.types, type)) {
        return;
      }
      /**
       * Determine if we need a drop zone before or after the element.
       */


      var _e$currentTarget$getB = e.currentTarget.getBoundingClientRect(),
          y = _e$currentTarget$getB.y,
          height = _e$currentTarget$getB.height;

      if ((0,_utils__WEBPACK_IMPORTED_MODULE_9__.isHoveringBefore)(e.clientY, y, height) && 'before' !== isDraggingOver) {
        setIsDraggingOver('before');
      } else if (!(0,_utils__WEBPACK_IMPORTED_MODULE_9__.isHoveringBefore)(e.clientY, y, height) && 'after' !== isDraggingOver) {
        setIsDraggingOver('after');
      }
    },
    onDragLeave: function onDragLeave() {
      isDraggingOver && setIsDraggingOver(false);
    },
    onDrop: function onDrop(e) {
      isDraggingOver && setIsDraggingOver(false);
      clearDraggingItem();

      if ((0,_utils__WEBPACK_IMPORTED_MODULE_9__.shouldAllowDrop)(e.dataTransfer.types, type)) {
        // Determine which zone
        var _e$currentTarget$getB2 = e.currentTarget.getBoundingClientRect(),
            y = _e$currentTarget$getB2.y,
            height = _e$currentTarget$getB2.height;

        var zone = (0,_utils__WEBPACK_IMPORTED_MODULE_9__.isHoveringBefore)(e.clientY, y, height) ? 'before' : 'after'; // Node to be moved

        var nodeID = e.dataTransfer.getData('node-id');

        var _getNode = (0,data__WEBPACK_IMPORTED_MODULE_5__.getNode)(nodeID),
            currentPos = _getNode.position,
            currentParent = _getNode.parent;

        var pos = index;

        if (parent === currentParent) {
          // Reorder nodes within the same parent.
          if ('before' === zone) {
            if (currentPos === index - 1) {
              return;
            } else {
              pos = currentPos > index ? index : Math.max(0, index - 1);
            }
          } else if ('after' === zone) {
            if (currentPos === index + 1) {
              return;
            } else {
              pos = currentPos > index ? index + 1 : index;
            }
          }
        } else {
          // Move nodes to a new parent.
          pos = 'after' === zone ? index + 1 : index;
        } // Drop zone node - position comes from prop


        moveNode(nodeID, pos, parent, [parent, currentParent]);
      }
    }
  }, 'column-group' !== type && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(ItemContent, {
    id: id,
    type: type,
    global: global,
    position: index,
    level: level,
    toggleContent: function toggleContent() {
      return setShowContent(!showContent);
    },
    isShowingContent: showContent
  }), 'module' !== type && !hasChildren && !global && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(EmptyDropArea, {
    id: id,
    type: type
  }), 0 < Object.keys(children).length && showContent && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("ul", null, Object.values(children).sort(data_layout_utils__WEBPACK_IMPORTED_MODULE_6__.sortNodes).map(function (node, i) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Item, _extends({
      key: node.node,
      level: 'column-group' === type ? level : level + 1,
      index: i
    }, node));
  })));
};

var EmptyDropArea = function EmptyDropArea(_ref2) {
  var id = _ref2.id,
      type = _ref2.type,
      rest = _objectWithoutProperties(_ref2, _excluded);

  var _useState7 = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false),
      _useState8 = _slicedToArray(_useState7, 2),
      isOver = _useState8[0],
      setIsOver = _useState8[1];

  var _getActions2 = (0,api__WEBPACK_IMPORTED_MODULE_4__.getActions)(),
      moveNode = _getActions2.moveNode;

  var _OutlineContext$use2 = _context__WEBPACK_IMPORTED_MODULE_7__["default"].use(),
      clearDraggingItem = _OutlineContext$use2.clearDraggingItem;

  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()('fl-builder-node-empty-drop-area', {
    'is-over': isOver
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", _extends({
    className: classes,
    onDragOver: function onDragOver(e) {
      if ('column' !== type) {
        return;
      }

      if ((0,_utils__WEBPACK_IMPORTED_MODULE_9__.shouldAllowDrop)(e.dataTransfer.types, 'module')) {
        setIsOver(true);
      }
    },
    onDragLeave: function onDragLeave() {
      return isOver && setIsOver(false);
    },
    onDrop: function onDrop(e) {
      setIsOver(false);
      clearDraggingItem();

      if ('column' !== type) {
        return;
      }

      if ((0,_utils__WEBPACK_IMPORTED_MODULE_9__.shouldAllowDrop)(e.dataTransfer.types, 'module')) {
        // Node to be moved
        var nodeID = e.dataTransfer.getData('node-id'); // Set the node to the first position in this parent.

        moveNode(nodeID, 0, id);
      }
    }
  }, rest), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: "drop-area"
  }));
};

var ItemContent = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.memo)(function (_ref3) {
  var id = _ref3.id,
      type = _ref3.type,
      global = _ref3.global,
      level = _ref3.level,
      toggleContent = _ref3.toggleContent,
      _ref3$isShowingConten = _ref3.isShowingContent,
      isShowingContent = _ref3$isShowingConten === void 0 ? true : _ref3$isShowingConten;

  var _getLayoutHooks3 = (0,data__WEBPACK_IMPORTED_MODULE_5__.getLayoutHooks)(),
      useNodeSettings = _getLayoutHooks3.useNodeSettings;

  var settings = useNodeSettings(id);

  var _useContextMenu = (0,ui_context_menu__WEBPACK_IMPORTED_MODULE_3__.useContextMenu)(),
      setContextMenu = _useContextMenu.setContextMenu,
      contextMenu = _useContextMenu.contextMenu,
      clearContextMenu = _useContextMenu.clearContextMenu;

  var _getActions3 = (0,api__WEBPACK_IMPORTED_MODULE_4__.getActions)(),
      openSettings = _getActions3.openSettings,
      deleteNode = _getActions3.deleteNode,
      copyNode = _getActions3.copyNode,
      scrollToNode = _getActions3.scrollToNode;

  var _getConfig = (0,api__WEBPACK_IMPORTED_MODULE_4__.getConfig)(),
      simpleUi = _getConfig.simpleUi;

  var hasVisibilitySettings = (0,_utils__WEBPACK_IMPORTED_MODULE_9__.hasVisibility)(settings);
  var showDisclosureTriangle = 'row' === type && !global; // Check if modules have a registered definition

  var hasDefinition = true;

  if ('module' === type) {
    hasDefinition = (0,_utils__WEBPACK_IMPORTED_MODULE_9__.moduleHasDefinition)(settings.type);
  }

  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()('fl-builder-node-outline-item-content', {
    'has-context-menu': false !== contextMenu && id === contextMenu.id,
    'is-global-node': global,
    'is-missing-definition': !hasDefinition
  }); // Allows delaying clicks long enough to check if its a doubleclick

  var _useSingleAndDoubleCl = (0,_utils__WEBPACK_IMPORTED_MODULE_9__.useSingleAndDoubleClick)({
    onClick: function onClick() {
      if (!hasDefinition) {
        return;
      }

      scrollToNode(id);
    },
    onDoubleClick: function onDoubleClick() {
      if (!hasDefinition) {
        return;
      }

      scrollToNode(id);
      openSettings(id);
    }
  }),
      _useSingleAndDoubleCl2 = _slicedToArray(_useSingleAndDoubleCl, 2),
      onClick = _useSingleAndDoubleCl2[0],
      onDoubleClick = _useSingleAndDoubleCl2[1];

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: classes,
    onClick: onClick,
    onDoubleClick: onDoubleClick,
    onPointerEnter: function onPointerEnter() {
      var el = document.querySelector(".fl-node-".concat(id));

      if (el) {
        el.style.boxShadow = "inset 0 0 0 2px var(--fl-builder-".concat(global ? 'orange' : 'blue', " ), 0 0 0 1px hsla( 210, 0%, 0%, .5 )");
      }
    },
    onPointerLeave: function onPointerLeave() {
      var el = document.querySelector(".fl-node-".concat(id));

      if (el) {
        el.style.boxShadow = '';
      }
    },
    onContextMenu: function onContextMenu(e) {
      // Already showing custom context menu, so show default browser menu.
      if (false !== contextMenu && id === contextMenu.id) {
        clearContextMenu();
        return;
      }

      var items = {
        settings: {
          label: 'Open Settings',
          isEnabled: hasDefinition,
          onClick: function onClick() {
            scrollToNode(id);
            openSettings(id);
          }
        },
        clone: {
          label: 'Duplicate',
          isEnabled: hasDefinition && !simpleUi,
          onClick: function onClick() {
            return copyNode(id);
          }
        },
        "delete": {
          label: FLBuilderStrings.remove,
          isEnabled: !simpleUi,
          status: 'destructive',
          onClick: function onClick() {
            return deleteNode(id);
          }
        }
      };
      setContextMenu({
        id: id,
        items: items,
        type: type,
        global: global,
        x: e.clientX,
        y: e.clientY
      }); // Otherwise, show custom context menu

      e.preventDefault();
    }
  }, showDisclosureTriangle && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", {
    className: "fl-builder-outline-item-gutter"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("button", {
    className: !isShowingContent && 'is-hiding-content',
    onClick: function onClick(e) {
      toggleContent(e);
      e.preventDefault();
      e.stopPropagation();
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(DisclosureArrow, null))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", {
    className: "fl-builder-outline-item-icon-wrap"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Icon, {
    type: type,
    moduleType: settings.type,
    settings: settings,
    hasDefinition: hasDefinition
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", {
    className: "fl-builder-outline-item-label-wrap"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(ItemLabel, {
    type: type,
    settings: settings,
    level: level
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Size, {
    id: id,
    type: type,
    size: parseFloat(settings.size),
    width: settings['max_content_width'],
    widthUnit: settings['max_content_width_unit']
  }), hasVisibilitySettings && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", {
    className: "fl-builder-outline-item-icon-wrap"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_tiny_icons__WEBPACK_IMPORTED_MODULE_8__.Visibility, null)));
});

var ItemLabel = function ItemLabel(_ref4) {
  var type = _ref4.type,
      _ref4$settings = _ref4.settings,
      settings = _ref4$settings === void 0 ? {} : _ref4$settings,
      level = _ref4.level;
  var typeLabel = undefined !== settings.type ? (0,_utils__WEBPACK_IMPORTED_MODULE_9__.getModuleTypeLabel)(settings.type) : type;
  var description = '';

  if ('column' === type && 3 <= level) {
    typeLabel = FLBuilderStrings.childColumn;
  }

  if ('row' === typeLabel) {
    typeLabel = FLBuilderStrings.row;
  }

  if ('column' === typeLabel) {
    typeLabel = FLBuilderStrings.column;
  }

  if ('module' === type && 'type' in settings) {
    switch (settings.type) {
      case 'heading':
        typeLabel = settings.tag;
        description = settings.heading;
        break;

      case 'html':
        description = settings.html;
        break;

      case 'rich-text':
      case 'icon':
      case 'button':
        description = settings.text;
        break;

      case 'callout':
        description = settings.title;
        break;
    }
  }

  if ('node_label' in settings && '' !== settings.node_label) {
    var nodeLabel = settings.node_label;

    if (!description || '' === description) {
      description = nodeLabel;
    } else {
      description = nodeLabel + ' - ' + description;
    }
  }

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement((react__WEBPACK_IMPORTED_MODULE_0___default().Fragment), null, typeLabel, description && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", {
    style: {
      opacity: .6
    }
  }, ': ' + (0,_utils__WEBPACK_IMPORTED_MODULE_9__.sanitizeString)(description)));
}; // Generic badge container


var PillBox = function PillBox(_ref5) {
  var children = _ref5.children,
      style = _ref5.style,
      rest = _objectWithoutProperties(_ref5, _excluded2);

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", _extends({
    style: _objectSpread({
      textTransform: 'lowercase',
      background: 'rgba(0,0,0,.05)',
      color: '#828282',
      fontSize: 11,
      flex: '0 0 auto',
      display: 'inline-flex',
      padding: '2px 6px',
      borderRadius: 25,
      whiteSpace: 'nowrap',
      overflow: 'hidden',
      textOverflow: 'ellipsis',
      fontFamily: 'monospace'
    }, style),
    title: children
  }, rest), children);
};

var Icon = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.memo)(function (_ref6) {
  var type = _ref6.type,
      moduleType = _ref6.moduleType,
      settings = _ref6.settings,
      hasDefinition = _ref6.hasDefinition;

  switch (type) {
    case 'row':
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_tiny_icons__WEBPACK_IMPORTED_MODULE_8__.Row, null);

    case 'column':
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_tiny_icons__WEBPACK_IMPORTED_MODULE_8__.Column, null);

    case 'module':
      var Component = (0,_tiny_icons__WEBPACK_IMPORTED_MODULE_8__.getModuleIconComponent)(moduleType, settings, hasDefinition);
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Component, null);
  }
});

var Size = function Size(_ref7) {
  var id = _ref7.id,
      type = _ref7.type,
      size = _ref7.size,
      width = _ref7.width,
      widthUnit = _ref7.widthUnit;

  var _useLayoutState = (0,data__WEBPACK_IMPORTED_MODULE_5__.useLayoutState)(),
      resizing = _useLayoutState.resizing;

  var string = '';

  if (resizing && resizing.includes(id)) {
    if ('column' === type) {
      string += " ".concat(size, "% ");
    } else if ('row' === type) {
      string += "Max: ".concat(width + widthUnit);
    }
  }

  if ('' === string) {
    return null;
  }

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(PillBox, {
    style: {
      background: 'var(--fl-builder-blue)',
      color: 'white'
    }
  }, string);
};

var DisclosureArrow = function DisclosureArrow() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "10",
    height: "10",
    viewBox: "0 0 10 10",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M2.79289 1.29289C3.18342 0.902369 3.81658 0.902369 4.20711 1.29289L7.20711 4.29289C7.59763 4.68342 7.59763 5.31658 7.20711 5.70711L4.20711 8.70711C3.81658 9.09763 3.18342 9.09763 2.79289 8.70711C2.40237 8.31658 2.40237 7.68342 2.79289 7.29289L5.08579 5L2.79289 2.70711C2.40237 2.31658 2.40237 1.68342 2.79289 1.29289Z",
    fill: "currentColor"
  }));
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Outline);

/***/ }),

/***/ "./src/builder/ui/outline-panel/outline/tiny-icons/index.js":
/*!******************************************************************!*\
  !*** ./src/builder/ui/outline-panel/outline/tiny-icons/index.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Alert": () => (/* binding */ Alert),
/* harmony export */   "Column": () => (/* binding */ Column),
/* harmony export */   "DefaultIcon": () => (/* binding */ DefaultIcon),
/* harmony export */   "Photo": () => (/* binding */ Photo),
/* harmony export */   "Row": () => (/* binding */ Row),
/* harmony export */   "Text": () => (/* binding */ Text),
/* harmony export */   "Visibility": () => (/* binding */ Visibility),
/* harmony export */   "getModuleIconComponent": () => (/* binding */ getModuleIconComponent)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
 // Swiped these from Figma so we'll be doing our own before they ship

var getModuleIconComponent = function getModuleIconComponent(type, settings) {
  var hasDefinition = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
  var moduleDefs = FLBuilderConfig.contentItems.module;

  if (!hasDefinition) {
    return Alert;
  }

  switch (type) {
    case 'rich-text':
      return Text;

    case 'photo':
      return Photo;

    case 'icon':
      if ('icon' in settings) {
        return function () {
          return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("i", {
            className: settings.icon,
            "aria-hidden": "true"
          });
        };
      }

      break;

    default:
      var def = moduleDefs.find(function (def) {
        return type === def.slug;
      });

      if (def && 'icon' in def) {
        return function () {
          return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
            style: {
              flexShrink: 0,
              width: 16,
              height: 16,
              fill: 'currentColor'
            },
            dangerouslySetInnerHTML: {
              __html: def.icon
            }
          });
        };
      }

      return DefaultIcon;
  }
};
var DefaultIcon = function DefaultIcon() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("rect", {
    x: "2.5",
    y: "2.5",
    width: "11",
    height: "11",
    rx: "1.5",
    stroke: "currentColor"
  }));
};
var Column = function Column() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("rect", {
    x: "9.5",
    y: "13.5",
    width: "11",
    height: "4",
    rx: "0.5",
    transform: "rotate(-90 9.5 13.5)",
    stroke: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("rect", {
    x: "2.5",
    y: "13.5",
    width: "11",
    height: "4",
    rx: "0.5",
    transform: "rotate(-90 2.5 13.5)",
    stroke: "currentColor"
  }));
};
var Row = function Row() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("rect", {
    x: "2.5",
    y: "9.5",
    width: "11",
    height: "4",
    rx: "0.5",
    stroke: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("rect", {
    x: "2.5",
    y: "2.5",
    width: "11",
    height: "4",
    rx: "0.5",
    stroke: "currentColor"
  }));
};
var Photo = function Photo() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    className: "svg",
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M12 6c0 1.105-.895 2-2 2-1.105 0-2-.895-2-2 0-1.105.895-2 2-2 1.105 0 2 .895 2 2zm-1 0c0 .552-.448 1-1 1-.552 0-1-.448-1-1 0-.552.448-1 1-1 .552 0 1 .448 1 1zM3 2c-.552 0-1 .448-1 1v10c0 .552.448 1 1 1h10c.552 0 1-.448 1-1V3c0-.552-.448-1-1-1H3zm10 1H3v6.293l2.5-2.5L11.707 13H13V3zM3 13v-2.293l2.5-2.5L10.293 13H3z",
    fillRule: "evenodd",
    fillOpacity: "1",
    fill: "currentCOlor",
    stroke: "none"
  }));
};
var Text = function Text() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M3.48254 7.83023L3.05006 9.22531H5.78441L5.35194 7.83023C5.19383 7.31871 5.03572 6.81183 4.87761 6.3096C4.72881 5.79807 4.58465 5.27724 4.44514 4.74711H4.38934C4.24053 5.27724 4.09172 5.79807 3.94291 6.3096C3.7941 6.81183 3.64065 7.31871 3.48254 7.83023ZM0.692383 12.9502L3.7755 3.79846H5.08688L8.17 12.9502H6.94233L6.07738 10.16H2.7571L1.8782 12.9502H0.692383Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M11.5546 13.1176C11.2384 13.1176 10.9407 13.0711 10.6617 12.9781C10.3827 12.8944 10.1363 12.7735 9.92234 12.6153C9.71773 12.4479 9.55497 12.248 9.43407 12.0155C9.31316 11.7737 9.25271 11.4993 9.25271 11.1924C9.25271 10.811 9.34106 10.4809 9.51777 10.2019C9.70378 9.91355 9.9921 9.66709 10.3827 9.46247C10.7826 9.25786 11.2942 9.09045 11.9173 8.96025C12.5404 8.83004 13.2891 8.72773 14.1634 8.65333C14.1541 8.42082 14.1169 8.20225 14.0518 7.99764C13.9867 7.79303 13.8844 7.61632 13.7449 7.46751C13.6053 7.30941 13.4193 7.18385 13.1868 7.09084C12.9636 6.99784 12.6892 6.95134 12.3637 6.95134C11.9173 6.95134 11.4895 7.03969 11.0803 7.2164C10.671 7.38381 10.3083 7.56517 9.9921 7.76048L9.54567 6.97924C9.71308 6.86763 9.90839 6.75603 10.1316 6.64442C10.3641 6.52351 10.6059 6.41656 10.857 6.32355C11.1175 6.23055 11.3918 6.15614 11.6801 6.10034C11.9685 6.03523 12.2614 6.00268 12.559 6.00268C13.4891 6.00268 14.1773 6.25845 14.6238 6.76998C15.0795 7.2722 15.3073 7.94649 15.3073 8.79284V12.9502H14.3726L14.275 12.0294H14.2331C13.8518 12.327 13.4286 12.5828 12.9636 12.7967C12.5079 13.0106 12.0382 13.1176 11.5546 13.1176ZM11.8615 12.1968C12.2521 12.1968 12.6334 12.1131 13.0055 11.9457C13.3868 11.769 13.7728 11.5179 14.1634 11.1924V9.40667C13.4379 9.46248 12.8288 9.54153 12.3358 9.64383C11.8522 9.74614 11.4616 9.8717 11.164 10.0205C10.8756 10.16 10.671 10.3228 10.5501 10.5088C10.4292 10.6855 10.3688 10.8855 10.3688 11.1087C10.3688 11.304 10.4106 11.4714 10.4943 11.6109C10.578 11.7411 10.685 11.8527 10.8152 11.9457C10.9547 12.0294 11.1128 12.0945 11.2895 12.141C11.4755 12.1782 11.6662 12.1968 11.8615 12.1968Z",
    fill: "currentColor"
  }));
};
var Alert = function Alert() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M2.375 15.0203H13.625C14.9067 15.0203 15.7271 14.0754 15.7271 12.9109C15.7271 12.5593 15.6392 12.2004 15.4487 11.8708L9.80908 2.04175C9.41357 1.33862 8.71045 0.979736 8 0.979736C7.28955 0.979736 6.5791 1.34595 6.18359 2.04175L0.543945 11.8782C0.353516 12.2078 0.265625 12.5593 0.265625 12.9109C0.265625 14.0754 1.09326 15.0203 2.375 15.0203ZM2.51416 13.614C2.06006 13.614 1.75977 13.2405 1.75977 12.8376C1.75977 12.7205 1.77441 12.574 1.84766 12.4421L7.34082 2.82544C7.4873 2.57642 7.74365 2.45923 8 2.45923C8.25635 2.45923 8.50537 2.57642 8.64453 2.82544L14.145 12.4568C14.2109 12.5813 14.2402 12.7205 14.2402 12.8376C14.2402 13.2405 13.9326 13.614 13.4785 13.614H2.51416ZM8 9.93726C8.40283 9.93726 8.63721 9.71021 8.64453 9.2854L8.75439 5.76245C8.76904 5.33032 8.44678 5.02271 7.99268 5.02271C7.53857 5.02271 7.22363 5.323 7.23828 5.75513L7.34082 9.29272C7.35547 9.71021 7.58984 9.93726 8 9.93726ZM8 12.3689C8.47607 12.3689 8.86426 12.0247 8.86426 11.5632C8.86426 11.0945 8.4834 10.7576 8 10.7576C7.52393 10.7576 7.13574 11.1018 7.13574 11.5632C7.13574 12.0247 7.53125 12.3689 8 12.3689Z",
    fill: "currentColor"
  }));
};
var Visibility = function Visibility() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", {
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M7.99634 15.5732C12.1492 15.5732 15.5769 12.1455 15.5769 8C15.5769 3.85449 12.1418 0.426758 7.98901 0.426758C3.84351 0.426758 0.423096 3.85449 0.423096 8C0.423096 12.1455 3.85083 15.5732 7.99634 15.5732ZM7.99634 14.0645C4.63452 14.0645 1.94653 11.3618 1.94653 8C1.94653 4.63818 4.63452 1.94287 7.98901 1.94287C11.3508 1.94287 14.0535 4.63818 14.0608 8C14.0681 11.3618 11.3582 14.0645 7.99634 14.0645ZM7.99634 11.1787C10.9919 11.1787 13.0281 8.76172 13.0281 8.00732C13.0281 7.26025 10.9919 4.83594 7.99634 4.83594C5.00806 4.83594 2.94995 7.26025 2.94995 8.00732C2.94995 8.76172 5.02271 11.1787 7.99634 11.1787ZM7.99634 10.0654C6.85376 10.0654 5.93091 9.12793 5.92358 8.00732C5.92358 6.86475 6.85376 5.94922 7.99634 5.94922C9.13159 5.94922 10.0544 6.86475 10.0544 8.00732C10.0544 9.12793 9.13159 10.0654 7.99634 10.0654ZM8.00366 8.9082C8.48706 8.9082 8.89722 8.49072 8.89722 8.00732C8.89722 7.52393 8.48706 7.10645 8.00366 7.10645C7.49829 7.10645 7.08813 7.52393 7.08813 8.00732C7.08813 8.49072 7.49829 8.9082 8.00366 8.9082Z",
    fill: "currentColor"
  }));
};

/***/ }),

/***/ "./src/builder/ui/outline-panel/outline/utils/index.js":
/*!*************************************************************!*\
  !*** ./src/builder/ui/outline-panel/outline/utils/index.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getChildNodes": () => (/* binding */ getChildNodes),
/* harmony export */   "getModuleTypeLabel": () => (/* binding */ getModuleTypeLabel),
/* harmony export */   "getNodeTree": () => (/* binding */ getNodeTree),
/* harmony export */   "getTransparentImg": () => (/* binding */ getTransparentImg),
/* harmony export */   "hasVisibility": () => (/* binding */ hasVisibility),
/* harmony export */   "isDraggable": () => (/* binding */ isDraggable),
/* harmony export */   "isHoveringBefore": () => (/* binding */ isHoveringBefore),
/* harmony export */   "moduleHasDefinition": () => (/* binding */ moduleHasDefinition),
/* harmony export */   "sanitizeString": () => (/* binding */ sanitizeString),
/* harmony export */   "shouldAllowDrop": () => (/* binding */ shouldAllowDrop),
/* harmony export */   "useSingleAndDoubleClick": () => (/* reexport safe */ _use_single_and_double_click__WEBPACK_IMPORTED_MODULE_2__["default"])
/* harmony export */ });
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! dompurify */ "./node_modules/dompurify/dist/purify.js");
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(dompurify__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! api */ "./src/builder/api/index.js");
/* harmony import */ var _use_single_and_double_click__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-single-and-double-click */ "./src/builder/ui/outline-panel/outline/utils/use-single-and-double-click.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }





var sanitizeString = function sanitizeString() {
  var string = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  return (0,dompurify__WEBPACK_IMPORTED_MODULE_0__.sanitize)(string, {
    ALLOWED_TAGS: []
  }).substring(0, 48);
};

var getModuleDefinition = function getModuleDefinition(type) {
  var _getConfig = (0,api__WEBPACK_IMPORTED_MODULE_1__.getConfig)(),
      contentItems = _getConfig.contentItems;

  var def = contentItems.module.find(function (item) {
    return item.slug === type;
  });
  return 'object' === _typeof(def) ? def : false;
};

var getModuleTypeLabel = function getModuleTypeLabel(type) {
  var def = getModuleDefinition(type);
  return def ? def.name : type;
};
var hasVisibility = function hasVisibility(settings) {
  var _settings$responsive_ = settings.responsive_display,
      responsive_display = _settings$responsive_ === void 0 ? '' : _settings$responsive_,
      _settings$visibility_ = settings.visibility_display,
      visibility_display = _settings$visibility_ === void 0 ? '' : _settings$visibility_;
  return '' !== responsive_display || '' !== visibility_display;
};
var getChildNodes = function getChildNodes(id, nodes) {
  return Object.values(nodes).filter(function (node) {
    return id === node.parent;
  });
};
var getNodeTree = function getNodeTree(nodes) {
  var flat = Object.values(nodes);
  var tree = [];
  flat.forEach(function (node) {
    if (null === node.parent) {
      return tree.push(node);
    }

    var parentIndex = flat.findIndex(function (item) {
      return item.node === node.parent;
    });

    if (!flat[parentIndex].children) {
      return flat[parentIndex].children = [node];
    }

    flat[parentIndex].children.push(node);
  });
  return tree;
};
/**
 * Check if the mouse is hovering in the before (top half) or after (bottom half) area of an element.
 */

var isHoveringBefore = function isHoveringBefore(mouseY, y, height) {
  var half = height / 2;
  var threshold = y + half;
  return mouseY <= threshold;
};
var draggableTypes = ['module', 'row', 'column'];
var isDraggable = function isDraggable(type) {
  var _getConfig2 = (0,api__WEBPACK_IMPORTED_MODULE_1__.getConfig)(),
      simpleUi = _getConfig2.simpleUi;

  return !simpleUi && draggableTypes.includes(type);
};
var shouldAllowDrop = function shouldAllowDrop() {
  var itemTypes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var zoneType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (!draggableTypes.includes(zoneType)) {
    return false;
  }

  if (zoneType === itemTypes || itemTypes.includes(zoneType)) {
    return true;
  }

  return false;
};
var getTransparentImg = function getTransparentImg() {
  var img = new Image();
  img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
  img.style.opacity = 0;
  return img;
};
/**
 * Cache the module type slugs
 */

var moduleTypeKeys = [];

var getModuleTypeKeys = function getModuleTypeKeys() {
  if (0 >= moduleTypeKeys.length) {
    var _getConfig3 = (0,api__WEBPACK_IMPORTED_MODULE_1__.getConfig)(),
        contentItems = _getConfig3.contentItems; // FLBuilderConfig


    moduleTypeKeys = contentItems.module.map(function (type) {
      return type.slug;
    });
  }

  return moduleTypeKeys;
};

var moduleHasDefinition = function moduleHasDefinition(key) {
  var keys = getModuleTypeKeys();
  return keys.includes(key);
};

/***/ }),

/***/ "./src/builder/ui/outline-panel/outline/utils/use-single-and-double-click.js":
/*!***********************************************************************************!*\
  !*** ./src/builder/ui/outline-panel/outline/utils/use-single-and-double-click.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }



var noop = function noop() {};

var requestTimeout = function requestTimeout(fn, delay, registerCancel) {
  var start = new Date().getTime();

  var loop = function loop() {
    var delta = new Date().getTime() - start;

    if (delta >= delay) {
      fn();
      registerCancel(noop);
      return;
    }

    var raf = requestAnimationFrame(loop);
    registerCancel(function () {
      return cancelAnimationFrame(raf);
    });
  };

  var raf = requestAnimationFrame(loop);
  registerCancel(function () {
    return cancelAnimationFrame(raf);
  });
};

var useCancelableScheduledWork = function useCancelableScheduledWork() {
  var cancelCallback = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(noop);

  var registerCancel = function registerCancel(fn) {
    return cancelCallback.current = fn;
  };

  var cancelScheduledWork = function cancelScheduledWork() {
    return cancelCallback.current();
  }; // Cancels the current scheduled work before the "unmount"


  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
    return cancelScheduledWork;
  }, []);
  return [registerCancel, cancelScheduledWork];
};

var useSingleAndDoubleClick = function useSingleAndDoubleClick(_ref) {
  var onClick = _ref.onClick,
      onDoubleClick = _ref.onDoubleClick,
      _ref$delay = _ref.delay,
      delay = _ref$delay === void 0 ? 300 : _ref$delay;

  var _useCancelableSchedul = useCancelableScheduledWork(),
      _useCancelableSchedul2 = _slicedToArray(_useCancelableSchedul, 2),
      registerCancel = _useCancelableSchedul2[0],
      cancelScheduledRaf = _useCancelableSchedul2[1];

  var handleClick = function handleClick() {
    cancelScheduledRaf();
    requestTimeout(onClick, delay, registerCancel);
  };

  var handleDoubleClick = function handleDoubleClick() {
    cancelScheduledRaf();
    onDoubleClick();
  };

  return [handleClick, handleDoubleClick];
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (useSingleAndDoubleClick);

/***/ }),

/***/ "./src/builder/ui/panel-manager/frame/index.js":
/*!*****************************************************!*\
  !*** ./src/builder/ui/panel-manager/frame/index.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
var _excluded = ["className"];

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }




var Frame = function Frame(_ref) {
  var className = _ref.className,
      rest = _objectWithoutProperties(_ref, _excluded);

  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()('fl-builder-workspace-panel', className);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", _extends({
    className: classes
  }, rest));
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Frame);

/***/ }),

/***/ "./src/builder/ui/panel-manager/index.js":
/*!***********************************************!*\
  !*** ./src/builder/ui/panel-manager/index.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @beaverbuilder/app-core */ "@beaverbuilder/app-core");
/* harmony import */ var _beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");
/* harmony import */ var _frame__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./frame */ "./src/builder/ui/panel-manager/frame/index.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/panel-manager/style.scss");






var handleObjectOrFunction = function handleObjectOrFunction(obj) {
  return 'function' === typeof obj ? obj() : obj;
};

var PanelManager = function PanelManager() {
  var _useSystemState = (0,data__WEBPACK_IMPORTED_MODULE_2__.useSystemState)(),
      currentPanel = _useSystemState.currentPanel,
      panels = _useSystemState.panels;

  var panel = null;

  if (currentPanel in panels) {
    panel = panels[currentPanel];
  } else {
    return null;
  }

  var _panel = panel,
      routerProps = _panel.routerProps,
      onHistoryChanged = _panel.onHistoryChanged,
      root = _panel.root,
      render = _panel.render,
      _panel$frame = _panel.frame,
      frame = _panel$frame === void 0 ? _frame__WEBPACK_IMPORTED_MODULE_3__["default"] : _panel$frame,
      panelClassName = _panel.className,
      wrapClassName = _panel.wrapClassName;
  var Frame = false === frame ? react__WEBPACK_IMPORTED_MODULE_0__.Fragment : frame;
  var PanelContent = root ? root : render;
  /* support legacy render prop */

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: wrapClassName
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Frame, {
    className: false !== frame && panelClassName
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_beaverbuilder_app_core__WEBPACK_IMPORTED_MODULE_1__.Root, {
    routerProps: handleObjectOrFunction(routerProps),
    onHistoryChanged: onHistoryChanged
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(PanelContent, null))));
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (PanelManager);

/***/ }),

/***/ "./src/builder/ui/shortcuts-panel/index.js":
/*!*************************************************!*\
  !*** ./src/builder/ui/shortcuts-panel/index.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./panel */ "./src/builder/ui/shortcuts-panel/panel/index.js");
/* harmony import */ var data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! data */ "./src/builder/data/index.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/shortcuts-panel/style.scss");





var ShortcutsList = function ShortcutsList(_ref) {
  var shortcuts = _ref.shortcuts;

  if (0 === Object.keys(shortcuts).length) {
    return null;
  }

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("ul", {
    className: "fl-ui-shortcut-list"
  }, Object.values(shortcuts).map(function (item, i) {
    var label = item.label,
        keyLabel = item.keyLabel;
    var key = {
      __html: keyLabel
    };
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("li", {
      key: i
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", null, label), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("span", {
      className: "fl-ui-shortcut-item-keycode",
      dangerouslySetInnerHTML: key
    }));
  }));
};

var ShortcutsPanel = function ShortcutsPanel() {
  var _getSystemActions = (0,data__WEBPACK_IMPORTED_MODULE_2__.getSystemActions)(),
      setShouldShowShortcuts = _getSystemActions.setShouldShowShortcuts;

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_panel__WEBPACK_IMPORTED_MODULE_1__.Panel, {
    title: "Keyboard Shortcuts",
    onClose: function onClose() {
      return setShouldShowShortcuts(false);
    },
    className: "fl-ui-help",
    style: {
      width: 360,
      maxWidth: '95vw'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(ShortcutsList, {
    shortcuts: FLBuilderConfig.keyboardShortcuts
  }));
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ShortcutsPanel);

/***/ }),

/***/ "./src/builder/ui/shortcuts-panel/panel/index.js":
/*!*******************************************************!*\
  !*** ./src/builder/ui/shortcuts-panel/panel/index.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Panel": () => (/* binding */ Panel)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _art__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../art */ "./src/builder/ui/art/index.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./style.scss */ "./src/builder/ui/shortcuts-panel/panel/style.scss");
var _excluded = ["className", "children", "title", "actions", "showCloseButton", "onClose"];

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }





var Panel = function Panel(_ref) {
  var className = _ref.className,
      children = _ref.children,
      title = _ref.title,
      actions = _ref.actions,
      _ref$showCloseButton = _ref.showCloseButton,
      showCloseButton = _ref$showCloseButton === void 0 ? true : _ref$showCloseButton,
      _ref$onClose = _ref.onClose,
      onClose = _ref$onClose === void 0 ? function () {} : _ref$onClose,
      rest = _objectWithoutProperties(_ref, _excluded);

  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()({
    'fl-ui-panel-area': true
  }, className);

  var TrailingActions = function TrailingActions() {
    if (!actions && !showCloseButton) {
      return null;
    }

    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      className: "fl-ui-panel-trailing-actions"
    }, actions, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("button", {
      onClick: onClose,
      className: "fl-ui-button"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_art__WEBPACK_IMPORTED_MODULE_2__.Icon.Close, null)));
  };

  var stopProp = function stopProp(e) {
    return e.stopPropagation();
  };

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: classes,
    onClick: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", _extends({
    className: "fl-ui-panel"
  }, rest, {
    onClick: stopProp
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: "fl-ui-panel-topbar"
  }, title && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: "fl-ui-panel-title"
  }, title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(TrailingActions, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: "fl-ui-panel-content"
  }, children)));
};

/***/ }),

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/***/ ((module, exports) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "./node_modules/dompurify/dist/purify.js":
/*!***********************************************!*\
  !*** ./node_modules/dompurify/dist/purify.js ***!
  \***********************************************/
/***/ (function(module) {

/*! @license DOMPurify 2.3.6 | (c) Cure53 and other contributors | Released under the Apache license 2.0 and Mozilla Public License 2.0 | github.com/cure53/DOMPurify/blob/2.3.6/LICENSE */

(function (global, factory) {
   true ? module.exports = factory() :
  0;
}(this, function () { 'use strict';

  function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

  var hasOwnProperty = Object.hasOwnProperty,
      setPrototypeOf = Object.setPrototypeOf,
      isFrozen = Object.isFrozen,
      getPrototypeOf = Object.getPrototypeOf,
      getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
  var freeze = Object.freeze,
      seal = Object.seal,
      create = Object.create; // eslint-disable-line import/no-mutable-exports

  var _ref = typeof Reflect !== 'undefined' && Reflect,
      apply = _ref.apply,
      construct = _ref.construct;

  if (!apply) {
    apply = function apply(fun, thisValue, args) {
      return fun.apply(thisValue, args);
    };
  }

  if (!freeze) {
    freeze = function freeze(x) {
      return x;
    };
  }

  if (!seal) {
    seal = function seal(x) {
      return x;
    };
  }

  if (!construct) {
    construct = function construct(Func, args) {
      return new (Function.prototype.bind.apply(Func, [null].concat(_toConsumableArray(args))))();
    };
  }

  var arrayForEach = unapply(Array.prototype.forEach);
  var arrayPop = unapply(Array.prototype.pop);
  var arrayPush = unapply(Array.prototype.push);

  var stringToLowerCase = unapply(String.prototype.toLowerCase);
  var stringMatch = unapply(String.prototype.match);
  var stringReplace = unapply(String.prototype.replace);
  var stringIndexOf = unapply(String.prototype.indexOf);
  var stringTrim = unapply(String.prototype.trim);

  var regExpTest = unapply(RegExp.prototype.test);

  var typeErrorCreate = unconstruct(TypeError);

  function unapply(func) {
    return function (thisArg) {
      for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }

      return apply(func, thisArg, args);
    };
  }

  function unconstruct(func) {
    return function () {
      for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        args[_key2] = arguments[_key2];
      }

      return construct(func, args);
    };
  }

  /* Add properties to a lookup table */
  function addToSet(set, array) {
    if (setPrototypeOf) {
      // Make 'in' and truthy checks like Boolean(set.constructor)
      // independent of any properties defined on Object.prototype.
      // Prevent prototype setters from intercepting set as a this value.
      setPrototypeOf(set, null);
    }

    var l = array.length;
    while (l--) {
      var element = array[l];
      if (typeof element === 'string') {
        var lcElement = stringToLowerCase(element);
        if (lcElement !== element) {
          // Config presets (e.g. tags.js, attrs.js) are immutable.
          if (!isFrozen(array)) {
            array[l] = lcElement;
          }

          element = lcElement;
        }
      }

      set[element] = true;
    }

    return set;
  }

  /* Shallow clone an object */
  function clone(object) {
    var newObject = create(null);

    var property = void 0;
    for (property in object) {
      if (apply(hasOwnProperty, object, [property])) {
        newObject[property] = object[property];
      }
    }

    return newObject;
  }

  /* IE10 doesn't support __lookupGetter__ so lets'
   * simulate it. It also automatically checks
   * if the prop is function or getter and behaves
   * accordingly. */
  function lookupGetter(object, prop) {
    while (object !== null) {
      var desc = getOwnPropertyDescriptor(object, prop);
      if (desc) {
        if (desc.get) {
          return unapply(desc.get);
        }

        if (typeof desc.value === 'function') {
          return unapply(desc.value);
        }
      }

      object = getPrototypeOf(object);
    }

    function fallbackValue(element) {
      console.warn('fallback value for', element);
      return null;
    }

    return fallbackValue;
  }

  var html = freeze(['a', 'abbr', 'acronym', 'address', 'area', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'big', 'blink', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'content', 'data', 'datalist', 'dd', 'decorator', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt', 'element', 'em', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'main', 'map', 'mark', 'marquee', 'menu', 'menuitem', 'meter', 'nav', 'nobr', 'ol', 'optgroup', 'option', 'output', 'p', 'picture', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'section', 'select', 'shadow', 'small', 'source', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr']);

  // SVG
  var svg = freeze(['svg', 'a', 'altglyph', 'altglyphdef', 'altglyphitem', 'animatecolor', 'animatemotion', 'animatetransform', 'circle', 'clippath', 'defs', 'desc', 'ellipse', 'filter', 'font', 'g', 'glyph', 'glyphref', 'hkern', 'image', 'line', 'lineargradient', 'marker', 'mask', 'metadata', 'mpath', 'path', 'pattern', 'polygon', 'polyline', 'radialgradient', 'rect', 'stop', 'style', 'switch', 'symbol', 'text', 'textpath', 'title', 'tref', 'tspan', 'view', 'vkern']);

  var svgFilters = freeze(['feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix', 'feDiffuseLighting', 'feDisplacementMap', 'feDistantLight', 'feFlood', 'feFuncA', 'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feImage', 'feMerge', 'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting', 'feSpotLight', 'feTile', 'feTurbulence']);

  // List of SVG elements that are disallowed by default.
  // We still need to know them so that we can do namespace
  // checks properly in case one wants to add them to
  // allow-list.
  var svgDisallowed = freeze(['animate', 'color-profile', 'cursor', 'discard', 'fedropshadow', 'font-face', 'font-face-format', 'font-face-name', 'font-face-src', 'font-face-uri', 'foreignobject', 'hatch', 'hatchpath', 'mesh', 'meshgradient', 'meshpatch', 'meshrow', 'missing-glyph', 'script', 'set', 'solidcolor', 'unknown', 'use']);

  var mathMl = freeze(['math', 'menclose', 'merror', 'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mmultiscripts', 'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mspace', 'msqrt', 'mstyle', 'msub', 'msup', 'msubsup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover']);

  // Similarly to SVG, we want to know all MathML elements,
  // even those that we disallow by default.
  var mathMlDisallowed = freeze(['maction', 'maligngroup', 'malignmark', 'mlongdiv', 'mscarries', 'mscarry', 'msgroup', 'mstack', 'msline', 'msrow', 'semantics', 'annotation', 'annotation-xml', 'mprescripts', 'none']);

  var text = freeze(['#text']);

  var html$1 = freeze(['accept', 'action', 'align', 'alt', 'autocapitalize', 'autocomplete', 'autopictureinpicture', 'autoplay', 'background', 'bgcolor', 'border', 'capture', 'cellpadding', 'cellspacing', 'checked', 'cite', 'class', 'clear', 'color', 'cols', 'colspan', 'controls', 'controlslist', 'coords', 'crossorigin', 'datetime', 'decoding', 'default', 'dir', 'disabled', 'disablepictureinpicture', 'disableremoteplayback', 'download', 'draggable', 'enctype', 'enterkeyhint', 'face', 'for', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang', 'id', 'inputmode', 'integrity', 'ismap', 'kind', 'label', 'lang', 'list', 'loading', 'loop', 'low', 'max', 'maxlength', 'media', 'method', 'min', 'minlength', 'multiple', 'muted', 'name', 'nonce', 'noshade', 'novalidate', 'nowrap', 'open', 'optimum', 'pattern', 'placeholder', 'playsinline', 'poster', 'preload', 'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'rev', 'reversed', 'role', 'rows', 'rowspan', 'spellcheck', 'scope', 'selected', 'shape', 'size', 'sizes', 'span', 'srclang', 'start', 'src', 'srcset', 'step', 'style', 'summary', 'tabindex', 'title', 'translate', 'type', 'usemap', 'valign', 'value', 'width', 'xmlns', 'slot']);

  var svg$1 = freeze(['accent-height', 'accumulate', 'additive', 'alignment-baseline', 'ascent', 'attributename', 'attributetype', 'azimuth', 'basefrequency', 'baseline-shift', 'begin', 'bias', 'by', 'class', 'clip', 'clippathunits', 'clip-path', 'clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cx', 'cy', 'd', 'dx', 'dy', 'diffuseconstant', 'direction', 'display', 'divisor', 'dur', 'edgemode', 'elevation', 'end', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'filterunits', 'flood-color', 'flood-opacity', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'fx', 'fy', 'g1', 'g2', 'glyph-name', 'glyphref', 'gradientunits', 'gradienttransform', 'height', 'href', 'id', 'image-rendering', 'in', 'in2', 'k', 'k1', 'k2', 'k3', 'k4', 'kerning', 'keypoints', 'keysplines', 'keytimes', 'lang', 'lengthadjust', 'letter-spacing', 'kernelmatrix', 'kernelunitlength', 'lighting-color', 'local', 'marker-end', 'marker-mid', 'marker-start', 'markerheight', 'markerunits', 'markerwidth', 'maskcontentunits', 'maskunits', 'max', 'mask', 'media', 'method', 'mode', 'min', 'name', 'numoctaves', 'offset', 'operator', 'opacity', 'order', 'orient', 'orientation', 'origin', 'overflow', 'paint-order', 'path', 'pathlength', 'patterncontentunits', 'patterntransform', 'patternunits', 'points', 'preservealpha', 'preserveaspectratio', 'primitiveunits', 'r', 'rx', 'ry', 'radius', 'refx', 'refy', 'repeatcount', 'repeatdur', 'restart', 'result', 'rotate', 'scale', 'seed', 'shape-rendering', 'specularconstant', 'specularexponent', 'spreadmethod', 'startoffset', 'stddeviation', 'stitchtiles', 'stop-color', 'stop-opacity', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke', 'stroke-width', 'style', 'surfacescale', 'systemlanguage', 'tabindex', 'targetx', 'targety', 'transform', 'transform-origin', 'text-anchor', 'text-decoration', 'text-rendering', 'textlength', 'type', 'u1', 'u2', 'unicode', 'values', 'viewbox', 'visibility', 'version', 'vert-adv-y', 'vert-origin-x', 'vert-origin-y', 'width', 'word-spacing', 'wrap', 'writing-mode', 'xchannelselector', 'ychannelselector', 'x', 'x1', 'x2', 'xmlns', 'y', 'y1', 'y2', 'z', 'zoomandpan']);

  var mathMl$1 = freeze(['accent', 'accentunder', 'align', 'bevelled', 'close', 'columnsalign', 'columnlines', 'columnspan', 'denomalign', 'depth', 'dir', 'display', 'displaystyle', 'encoding', 'fence', 'frame', 'height', 'href', 'id', 'largeop', 'length', 'linethickness', 'lspace', 'lquote', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'movablelimits', 'notation', 'numalign', 'open', 'rowalign', 'rowlines', 'rowspacing', 'rowspan', 'rspace', 'rquote', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier', 'selection', 'separator', 'separators', 'stretchy', 'subscriptshift', 'supscriptshift', 'symmetric', 'voffset', 'width', 'xmlns']);

  var xml = freeze(['xlink:href', 'xml:id', 'xlink:title', 'xml:space', 'xmlns:xlink']);

  // eslint-disable-next-line unicorn/better-regex
  var MUSTACHE_EXPR = seal(/\{\{[\s\S]*|[\s\S]*\}\}/gm); // Specify template detection regex for SAFE_FOR_TEMPLATES mode
  var ERB_EXPR = seal(/<%[\s\S]*|[\s\S]*%>/gm);
  var DATA_ATTR = seal(/^data-[\-\w.\u00B7-\uFFFF]/); // eslint-disable-line no-useless-escape
  var ARIA_ATTR = seal(/^aria-[\-\w]+$/); // eslint-disable-line no-useless-escape
  var IS_ALLOWED_URI = seal(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i // eslint-disable-line no-useless-escape
  );
  var IS_SCRIPT_OR_DATA = seal(/^(?:\w+script|data):/i);
  var ATTR_WHITESPACE = seal(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g // eslint-disable-line no-control-regex
  );
  var DOCTYPE_NAME = seal(/^html$/i);

  var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

  function _toConsumableArray$1(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

  var getGlobal = function getGlobal() {
    return typeof window === 'undefined' ? null : window;
  };

  /**
   * Creates a no-op policy for internal use only.
   * Don't export this function outside this module!
   * @param {?TrustedTypePolicyFactory} trustedTypes The policy factory.
   * @param {Document} document The document object (to determine policy name suffix)
   * @return {?TrustedTypePolicy} The policy created (or null, if Trusted Types
   * are not supported).
   */
  var _createTrustedTypesPolicy = function _createTrustedTypesPolicy(trustedTypes, document) {
    if ((typeof trustedTypes === 'undefined' ? 'undefined' : _typeof(trustedTypes)) !== 'object' || typeof trustedTypes.createPolicy !== 'function') {
      return null;
    }

    // Allow the callers to control the unique policy name
    // by adding a data-tt-policy-suffix to the script element with the DOMPurify.
    // Policy creation with duplicate names throws in Trusted Types.
    var suffix = null;
    var ATTR_NAME = 'data-tt-policy-suffix';
    if (document.currentScript && document.currentScript.hasAttribute(ATTR_NAME)) {
      suffix = document.currentScript.getAttribute(ATTR_NAME);
    }

    var policyName = 'dompurify' + (suffix ? '#' + suffix : '');

    try {
      return trustedTypes.createPolicy(policyName, {
        createHTML: function createHTML(html$$1) {
          return html$$1;
        }
      });
    } catch (_) {
      // Policy creation failed (most likely another DOMPurify script has
      // already run). Skip creating the policy, as this will only cause errors
      // if TT are enforced.
      console.warn('TrustedTypes policy ' + policyName + ' could not be created.');
      return null;
    }
  };

  function createDOMPurify() {
    var window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : getGlobal();

    var DOMPurify = function DOMPurify(root) {
      return createDOMPurify(root);
    };

    /**
     * Version label, exposed for easier checks
     * if DOMPurify is up to date or not
     */
    DOMPurify.version = '2.3.6';

    /**
     * Array of elements that DOMPurify removed during sanitation.
     * Empty if nothing was removed.
     */
    DOMPurify.removed = [];

    if (!window || !window.document || window.document.nodeType !== 9) {
      // Not running in a browser, provide a factory function
      // so that you can pass your own Window
      DOMPurify.isSupported = false;

      return DOMPurify;
    }

    var originalDocument = window.document;

    var document = window.document;
    var DocumentFragment = window.DocumentFragment,
        HTMLTemplateElement = window.HTMLTemplateElement,
        Node = window.Node,
        Element = window.Element,
        NodeFilter = window.NodeFilter,
        _window$NamedNodeMap = window.NamedNodeMap,
        NamedNodeMap = _window$NamedNodeMap === undefined ? window.NamedNodeMap || window.MozNamedAttrMap : _window$NamedNodeMap,
        HTMLFormElement = window.HTMLFormElement,
        DOMParser = window.DOMParser,
        trustedTypes = window.trustedTypes;


    var ElementPrototype = Element.prototype;

    var cloneNode = lookupGetter(ElementPrototype, 'cloneNode');
    var getNextSibling = lookupGetter(ElementPrototype, 'nextSibling');
    var getChildNodes = lookupGetter(ElementPrototype, 'childNodes');
    var getParentNode = lookupGetter(ElementPrototype, 'parentNode');

    // As per issue #47, the web-components registry is inherited by a
    // new document created via createHTMLDocument. As per the spec
    // (http://w3c.github.io/webcomponents/spec/custom/#creating-and-passing-registries)
    // a new empty registry is used when creating a template contents owner
    // document, so we use that as our parent document to ensure nothing
    // is inherited.
    if (typeof HTMLTemplateElement === 'function') {
      var template = document.createElement('template');
      if (template.content && template.content.ownerDocument) {
        document = template.content.ownerDocument;
      }
    }

    var trustedTypesPolicy = _createTrustedTypesPolicy(trustedTypes, originalDocument);
    var emptyHTML = trustedTypesPolicy ? trustedTypesPolicy.createHTML('') : '';

    var _document = document,
        implementation = _document.implementation,
        createNodeIterator = _document.createNodeIterator,
        createDocumentFragment = _document.createDocumentFragment,
        getElementsByTagName = _document.getElementsByTagName;
    var importNode = originalDocument.importNode;


    var documentMode = {};
    try {
      documentMode = clone(document).documentMode ? document.documentMode : {};
    } catch (_) {}

    var hooks = {};

    /**
     * Expose whether this browser supports running the full DOMPurify.
     */
    DOMPurify.isSupported = typeof getParentNode === 'function' && implementation && typeof implementation.createHTMLDocument !== 'undefined' && documentMode !== 9;

    var MUSTACHE_EXPR$$1 = MUSTACHE_EXPR,
        ERB_EXPR$$1 = ERB_EXPR,
        DATA_ATTR$$1 = DATA_ATTR,
        ARIA_ATTR$$1 = ARIA_ATTR,
        IS_SCRIPT_OR_DATA$$1 = IS_SCRIPT_OR_DATA,
        ATTR_WHITESPACE$$1 = ATTR_WHITESPACE;
    var IS_ALLOWED_URI$$1 = IS_ALLOWED_URI;

    /**
     * We consider the elements and attributes below to be safe. Ideally
     * don't add any new ones but feel free to remove unwanted ones.
     */

    /* allowed element names */

    var ALLOWED_TAGS = null;
    var DEFAULT_ALLOWED_TAGS = addToSet({}, [].concat(_toConsumableArray$1(html), _toConsumableArray$1(svg), _toConsumableArray$1(svgFilters), _toConsumableArray$1(mathMl), _toConsumableArray$1(text)));

    /* Allowed attribute names */
    var ALLOWED_ATTR = null;
    var DEFAULT_ALLOWED_ATTR = addToSet({}, [].concat(_toConsumableArray$1(html$1), _toConsumableArray$1(svg$1), _toConsumableArray$1(mathMl$1), _toConsumableArray$1(xml)));

    /*
     * Configure how DOMPUrify should handle custom elements and their attributes as well as customized built-in elements.
     * @property {RegExp|Function|null} tagNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any custom elements)
     * @property {RegExp|Function|null} attributeNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any attributes not on the allow list)
     * @property {boolean} allowCustomizedBuiltInElements allow custom elements derived from built-ins if they pass CUSTOM_ELEMENT_HANDLING.tagNameCheck. Default: `false`.
     */
    var CUSTOM_ELEMENT_HANDLING = Object.seal(Object.create(null, {
      tagNameCheck: {
        writable: true,
        configurable: false,
        enumerable: true,
        value: null
      },
      attributeNameCheck: {
        writable: true,
        configurable: false,
        enumerable: true,
        value: null
      },
      allowCustomizedBuiltInElements: {
        writable: true,
        configurable: false,
        enumerable: true,
        value: false
      }
    }));

    /* Explicitly forbidden tags (overrides ALLOWED_TAGS/ADD_TAGS) */
    var FORBID_TAGS = null;

    /* Explicitly forbidden attributes (overrides ALLOWED_ATTR/ADD_ATTR) */
    var FORBID_ATTR = null;

    /* Decide if ARIA attributes are okay */
    var ALLOW_ARIA_ATTR = true;

    /* Decide if custom data attributes are okay */
    var ALLOW_DATA_ATTR = true;

    /* Decide if unknown protocols are okay */
    var ALLOW_UNKNOWN_PROTOCOLS = false;

    /* Output should be safe for common template engines.
     * This means, DOMPurify removes data attributes, mustaches and ERB
     */
    var SAFE_FOR_TEMPLATES = false;

    /* Decide if document with <html>... should be returned */
    var WHOLE_DOCUMENT = false;

    /* Track whether config is already set on this instance of DOMPurify. */
    var SET_CONFIG = false;

    /* Decide if all elements (e.g. style, script) must be children of
     * document.body. By default, browsers might move them to document.head */
    var FORCE_BODY = false;

    /* Decide if a DOM `HTMLBodyElement` should be returned, instead of a html
     * string (or a TrustedHTML object if Trusted Types are supported).
     * If `WHOLE_DOCUMENT` is enabled a `HTMLHtmlElement` will be returned instead
     */
    var RETURN_DOM = false;

    /* Decide if a DOM `DocumentFragment` should be returned, instead of a html
     * string  (or a TrustedHTML object if Trusted Types are supported) */
    var RETURN_DOM_FRAGMENT = false;

    /* Try to return a Trusted Type object instead of a string, return a string in
     * case Trusted Types are not supported  */
    var RETURN_TRUSTED_TYPE = false;

    /* Output should be free from DOM clobbering attacks? */
    var SANITIZE_DOM = true;

    /* Keep element content when removing element? */
    var KEEP_CONTENT = true;

    /* If a `Node` is passed to sanitize(), then performs sanitization in-place instead
     * of importing it into a new Document and returning a sanitized copy */
    var IN_PLACE = false;

    /* Allow usage of profiles like html, svg and mathMl */
    var USE_PROFILES = {};

    /* Tags to ignore content of when KEEP_CONTENT is true */
    var FORBID_CONTENTS = null;
    var DEFAULT_FORBID_CONTENTS = addToSet({}, ['annotation-xml', 'audio', 'colgroup', 'desc', 'foreignobject', 'head', 'iframe', 'math', 'mi', 'mn', 'mo', 'ms', 'mtext', 'noembed', 'noframes', 'noscript', 'plaintext', 'script', 'style', 'svg', 'template', 'thead', 'title', 'video', 'xmp']);

    /* Tags that are safe for data: URIs */
    var DATA_URI_TAGS = null;
    var DEFAULT_DATA_URI_TAGS = addToSet({}, ['audio', 'video', 'img', 'source', 'image', 'track']);

    /* Attributes safe for values like "javascript:" */
    var URI_SAFE_ATTRIBUTES = null;
    var DEFAULT_URI_SAFE_ATTRIBUTES = addToSet({}, ['alt', 'class', 'for', 'id', 'label', 'name', 'pattern', 'placeholder', 'role', 'summary', 'title', 'value', 'style', 'xmlns']);

    var MATHML_NAMESPACE = 'http://www.w3.org/1998/Math/MathML';
    var SVG_NAMESPACE = 'http://www.w3.org/2000/svg';
    var HTML_NAMESPACE = 'http://www.w3.org/1999/xhtml';
    /* Document namespace */
    var NAMESPACE = HTML_NAMESPACE;
    var IS_EMPTY_INPUT = false;

    /* Parsing of strict XHTML documents */
    var PARSER_MEDIA_TYPE = void 0;
    var SUPPORTED_PARSER_MEDIA_TYPES = ['application/xhtml+xml', 'text/html'];
    var DEFAULT_PARSER_MEDIA_TYPE = 'text/html';
    var transformCaseFunc = void 0;

    /* Keep a reference to config to pass to hooks */
    var CONFIG = null;

    /* Ideally, do not touch anything below this line */
    /* ______________________________________________ */

    var formElement = document.createElement('form');

    var isRegexOrFunction = function isRegexOrFunction(testValue) {
      return testValue instanceof RegExp || testValue instanceof Function;
    };

    /**
     * _parseConfig
     *
     * @param  {Object} cfg optional config literal
     */
    // eslint-disable-next-line complexity
    var _parseConfig = function _parseConfig(cfg) {
      if (CONFIG && CONFIG === cfg) {
        return;
      }

      /* Shield configuration object from tampering */
      if (!cfg || (typeof cfg === 'undefined' ? 'undefined' : _typeof(cfg)) !== 'object') {
        cfg = {};
      }

      /* Shield configuration object from prototype pollution */
      cfg = clone(cfg);

      /* Set configuration parameters */
      ALLOWED_TAGS = 'ALLOWED_TAGS' in cfg ? addToSet({}, cfg.ALLOWED_TAGS) : DEFAULT_ALLOWED_TAGS;
      ALLOWED_ATTR = 'ALLOWED_ATTR' in cfg ? addToSet({}, cfg.ALLOWED_ATTR) : DEFAULT_ALLOWED_ATTR;
      URI_SAFE_ATTRIBUTES = 'ADD_URI_SAFE_ATTR' in cfg ? addToSet(clone(DEFAULT_URI_SAFE_ATTRIBUTES), cfg.ADD_URI_SAFE_ATTR) : DEFAULT_URI_SAFE_ATTRIBUTES;
      DATA_URI_TAGS = 'ADD_DATA_URI_TAGS' in cfg ? addToSet(clone(DEFAULT_DATA_URI_TAGS), cfg.ADD_DATA_URI_TAGS) : DEFAULT_DATA_URI_TAGS;
      FORBID_CONTENTS = 'FORBID_CONTENTS' in cfg ? addToSet({}, cfg.FORBID_CONTENTS) : DEFAULT_FORBID_CONTENTS;
      FORBID_TAGS = 'FORBID_TAGS' in cfg ? addToSet({}, cfg.FORBID_TAGS) : {};
      FORBID_ATTR = 'FORBID_ATTR' in cfg ? addToSet({}, cfg.FORBID_ATTR) : {};
      USE_PROFILES = 'USE_PROFILES' in cfg ? cfg.USE_PROFILES : false;
      ALLOW_ARIA_ATTR = cfg.ALLOW_ARIA_ATTR !== false; // Default true
      ALLOW_DATA_ATTR = cfg.ALLOW_DATA_ATTR !== false; // Default true
      ALLOW_UNKNOWN_PROTOCOLS = cfg.ALLOW_UNKNOWN_PROTOCOLS || false; // Default false
      SAFE_FOR_TEMPLATES = cfg.SAFE_FOR_TEMPLATES || false; // Default false
      WHOLE_DOCUMENT = cfg.WHOLE_DOCUMENT || false; // Default false
      RETURN_DOM = cfg.RETURN_DOM || false; // Default false
      RETURN_DOM_FRAGMENT = cfg.RETURN_DOM_FRAGMENT || false; // Default false
      RETURN_TRUSTED_TYPE = cfg.RETURN_TRUSTED_TYPE || false; // Default false
      FORCE_BODY = cfg.FORCE_BODY || false; // Default false
      SANITIZE_DOM = cfg.SANITIZE_DOM !== false; // Default true
      KEEP_CONTENT = cfg.KEEP_CONTENT !== false; // Default true
      IN_PLACE = cfg.IN_PLACE || false; // Default false
      IS_ALLOWED_URI$$1 = cfg.ALLOWED_URI_REGEXP || IS_ALLOWED_URI$$1;
      NAMESPACE = cfg.NAMESPACE || HTML_NAMESPACE;
      if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck)) {
        CUSTOM_ELEMENT_HANDLING.tagNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck;
      }

      if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck)) {
        CUSTOM_ELEMENT_HANDLING.attributeNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck;
      }

      if (cfg.CUSTOM_ELEMENT_HANDLING && typeof cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements === 'boolean') {
        CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements = cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements;
      }

      PARSER_MEDIA_TYPE =
      // eslint-disable-next-line unicorn/prefer-includes
      SUPPORTED_PARSER_MEDIA_TYPES.indexOf(cfg.PARSER_MEDIA_TYPE) === -1 ? PARSER_MEDIA_TYPE = DEFAULT_PARSER_MEDIA_TYPE : PARSER_MEDIA_TYPE = cfg.PARSER_MEDIA_TYPE;

      // HTML tags and attributes are not case-sensitive, converting to lowercase. Keeping XHTML as is.
      transformCaseFunc = PARSER_MEDIA_TYPE === 'application/xhtml+xml' ? function (x) {
        return x;
      } : stringToLowerCase;

      if (SAFE_FOR_TEMPLATES) {
        ALLOW_DATA_ATTR = false;
      }

      if (RETURN_DOM_FRAGMENT) {
        RETURN_DOM = true;
      }

      /* Parse profile info */
      if (USE_PROFILES) {
        ALLOWED_TAGS = addToSet({}, [].concat(_toConsumableArray$1(text)));
        ALLOWED_ATTR = [];
        if (USE_PROFILES.html === true) {
          addToSet(ALLOWED_TAGS, html);
          addToSet(ALLOWED_ATTR, html$1);
        }

        if (USE_PROFILES.svg === true) {
          addToSet(ALLOWED_TAGS, svg);
          addToSet(ALLOWED_ATTR, svg$1);
          addToSet(ALLOWED_ATTR, xml);
        }

        if (USE_PROFILES.svgFilters === true) {
          addToSet(ALLOWED_TAGS, svgFilters);
          addToSet(ALLOWED_ATTR, svg$1);
          addToSet(ALLOWED_ATTR, xml);
        }

        if (USE_PROFILES.mathMl === true) {
          addToSet(ALLOWED_TAGS, mathMl);
          addToSet(ALLOWED_ATTR, mathMl$1);
          addToSet(ALLOWED_ATTR, xml);
        }
      }

      /* Merge configuration parameters */
      if (cfg.ADD_TAGS) {
        if (ALLOWED_TAGS === DEFAULT_ALLOWED_TAGS) {
          ALLOWED_TAGS = clone(ALLOWED_TAGS);
        }

        addToSet(ALLOWED_TAGS, cfg.ADD_TAGS);
      }

      if (cfg.ADD_ATTR) {
        if (ALLOWED_ATTR === DEFAULT_ALLOWED_ATTR) {
          ALLOWED_ATTR = clone(ALLOWED_ATTR);
        }

        addToSet(ALLOWED_ATTR, cfg.ADD_ATTR);
      }

      if (cfg.ADD_URI_SAFE_ATTR) {
        addToSet(URI_SAFE_ATTRIBUTES, cfg.ADD_URI_SAFE_ATTR);
      }

      if (cfg.FORBID_CONTENTS) {
        if (FORBID_CONTENTS === DEFAULT_FORBID_CONTENTS) {
          FORBID_CONTENTS = clone(FORBID_CONTENTS);
        }

        addToSet(FORBID_CONTENTS, cfg.FORBID_CONTENTS);
      }

      /* Add #text in case KEEP_CONTENT is set to true */
      if (KEEP_CONTENT) {
        ALLOWED_TAGS['#text'] = true;
      }

      /* Add html, head and body to ALLOWED_TAGS in case WHOLE_DOCUMENT is true */
      if (WHOLE_DOCUMENT) {
        addToSet(ALLOWED_TAGS, ['html', 'head', 'body']);
      }

      /* Add tbody to ALLOWED_TAGS in case tables are permitted, see #286, #365 */
      if (ALLOWED_TAGS.table) {
        addToSet(ALLOWED_TAGS, ['tbody']);
        delete FORBID_TAGS.tbody;
      }

      // Prevent further manipulation of configuration.
      // Not available in IE8, Safari 5, etc.
      if (freeze) {
        freeze(cfg);
      }

      CONFIG = cfg;
    };

    var MATHML_TEXT_INTEGRATION_POINTS = addToSet({}, ['mi', 'mo', 'mn', 'ms', 'mtext']);

    var HTML_INTEGRATION_POINTS = addToSet({}, ['foreignobject', 'desc', 'title', 'annotation-xml']);

    /* Keep track of all possible SVG and MathML tags
     * so that we can perform the namespace checks
     * correctly. */
    var ALL_SVG_TAGS = addToSet({}, svg);
    addToSet(ALL_SVG_TAGS, svgFilters);
    addToSet(ALL_SVG_TAGS, svgDisallowed);

    var ALL_MATHML_TAGS = addToSet({}, mathMl);
    addToSet(ALL_MATHML_TAGS, mathMlDisallowed);

    /**
     *
     *
     * @param  {Element} element a DOM element whose namespace is being checked
     * @returns {boolean} Return false if the element has a
     *  namespace that a spec-compliant parser would never
     *  return. Return true otherwise.
     */
    var _checkValidNamespace = function _checkValidNamespace(element) {
      var parent = getParentNode(element);

      // In JSDOM, if we're inside shadow DOM, then parentNode
      // can be null. We just simulate parent in this case.
      if (!parent || !parent.tagName) {
        parent = {
          namespaceURI: HTML_NAMESPACE,
          tagName: 'template'
        };
      }

      var tagName = stringToLowerCase(element.tagName);
      var parentTagName = stringToLowerCase(parent.tagName);

      if (element.namespaceURI === SVG_NAMESPACE) {
        // The only way to switch from HTML namespace to SVG
        // is via <svg>. If it happens via any other tag, then
        // it should be killed.
        if (parent.namespaceURI === HTML_NAMESPACE) {
          return tagName === 'svg';
        }

        // The only way to switch from MathML to SVG is via
        // svg if parent is either <annotation-xml> or MathML
        // text integration points.
        if (parent.namespaceURI === MATHML_NAMESPACE) {
          return tagName === 'svg' && (parentTagName === 'annotation-xml' || MATHML_TEXT_INTEGRATION_POINTS[parentTagName]);
        }

        // We only allow elements that are defined in SVG
        // spec. All others are disallowed in SVG namespace.
        return Boolean(ALL_SVG_TAGS[tagName]);
      }

      if (element.namespaceURI === MATHML_NAMESPACE) {
        // The only way to switch from HTML namespace to MathML
        // is via <math>. If it happens via any other tag, then
        // it should be killed.
        if (parent.namespaceURI === HTML_NAMESPACE) {
          return tagName === 'math';
        }

        // The only way to switch from SVG to MathML is via
        // <math> and HTML integration points
        if (parent.namespaceURI === SVG_NAMESPACE) {
          return tagName === 'math' && HTML_INTEGRATION_POINTS[parentTagName];
        }

        // We only allow elements that are defined in MathML
        // spec. All others are disallowed in MathML namespace.
        return Boolean(ALL_MATHML_TAGS[tagName]);
      }

      if (element.namespaceURI === HTML_NAMESPACE) {
        // The only way to switch from SVG to HTML is via
        // HTML integration points, and from MathML to HTML
        // is via MathML text integration points
        if (parent.namespaceURI === SVG_NAMESPACE && !HTML_INTEGRATION_POINTS[parentTagName]) {
          return false;
        }

        if (parent.namespaceURI === MATHML_NAMESPACE && !MATHML_TEXT_INTEGRATION_POINTS[parentTagName]) {
          return false;
        }

        // Certain elements are allowed in both SVG and HTML
        // namespace. We need to specify them explicitly
        // so that they don't get erronously deleted from
        // HTML namespace.
        var commonSvgAndHTMLElements = addToSet({}, ['title', 'style', 'font', 'a', 'script']);

        // We disallow tags that are specific for MathML
        // or SVG and should never appear in HTML namespace
        return !ALL_MATHML_TAGS[tagName] && (commonSvgAndHTMLElements[tagName] || !ALL_SVG_TAGS[tagName]);
      }

      // The code should never reach this place (this means
      // that the element somehow got namespace that is not
      // HTML, SVG or MathML). Return false just in case.
      return false;
    };

    /**
     * _forceRemove
     *
     * @param  {Node} node a DOM node
     */
    var _forceRemove = function _forceRemove(node) {
      arrayPush(DOMPurify.removed, { element: node });
      try {
        // eslint-disable-next-line unicorn/prefer-dom-node-remove
        node.parentNode.removeChild(node);
      } catch (_) {
        try {
          node.outerHTML = emptyHTML;
        } catch (_) {
          node.remove();
        }
      }
    };

    /**
     * _removeAttribute
     *
     * @param  {String} name an Attribute name
     * @param  {Node} node a DOM node
     */
    var _removeAttribute = function _removeAttribute(name, node) {
      try {
        arrayPush(DOMPurify.removed, {
          attribute: node.getAttributeNode(name),
          from: node
        });
      } catch (_) {
        arrayPush(DOMPurify.removed, {
          attribute: null,
          from: node
        });
      }

      node.removeAttribute(name);

      // We void attribute values for unremovable "is"" attributes
      if (name === 'is' && !ALLOWED_ATTR[name]) {
        if (RETURN_DOM || RETURN_DOM_FRAGMENT) {
          try {
            _forceRemove(node);
          } catch (_) {}
        } else {
          try {
            node.setAttribute(name, '');
          } catch (_) {}
        }
      }
    };

    /**
     * _initDocument
     *
     * @param  {String} dirty a string of dirty markup
     * @return {Document} a DOM, filled with the dirty markup
     */
    var _initDocument = function _initDocument(dirty) {
      /* Create a HTML document */
      var doc = void 0;
      var leadingWhitespace = void 0;

      if (FORCE_BODY) {
        dirty = '<remove></remove>' + dirty;
      } else {
        /* If FORCE_BODY isn't used, leading whitespace needs to be preserved manually */
        var matches = stringMatch(dirty, /^[\r\n\t ]+/);
        leadingWhitespace = matches && matches[0];
      }

      if (PARSER_MEDIA_TYPE === 'application/xhtml+xml') {
        // Root of XHTML doc must contain xmlns declaration (see https://www.w3.org/TR/xhtml1/normative.html#strict)
        dirty = '<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>' + dirty + '</body></html>';
      }

      var dirtyPayload = trustedTypesPolicy ? trustedTypesPolicy.createHTML(dirty) : dirty;
      /*
       * Use the DOMParser API by default, fallback later if needs be
       * DOMParser not work for svg when has multiple root element.
       */
      if (NAMESPACE === HTML_NAMESPACE) {
        try {
          doc = new DOMParser().parseFromString(dirtyPayload, PARSER_MEDIA_TYPE);
        } catch (_) {}
      }

      /* Use createHTMLDocument in case DOMParser is not available */
      if (!doc || !doc.documentElement) {
        doc = implementation.createDocument(NAMESPACE, 'template', null);
        try {
          doc.documentElement.innerHTML = IS_EMPTY_INPUT ? '' : dirtyPayload;
        } catch (_) {
          // Syntax error if dirtyPayload is invalid xml
        }
      }

      var body = doc.body || doc.documentElement;

      if (dirty && leadingWhitespace) {
        body.insertBefore(document.createTextNode(leadingWhitespace), body.childNodes[0] || null);
      }

      /* Work on whole document or just its body */
      if (NAMESPACE === HTML_NAMESPACE) {
        return getElementsByTagName.call(doc, WHOLE_DOCUMENT ? 'html' : 'body')[0];
      }

      return WHOLE_DOCUMENT ? doc.documentElement : body;
    };

    /**
     * _createIterator
     *
     * @param  {Document} root document/fragment to create iterator for
     * @return {Iterator} iterator instance
     */
    var _createIterator = function _createIterator(root) {
      return createNodeIterator.call(root.ownerDocument || root, root,
      // eslint-disable-next-line no-bitwise
      NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT, null, false);
    };

    /**
     * _isClobbered
     *
     * @param  {Node} elm element to check for clobbering attacks
     * @return {Boolean} true if clobbered, false if safe
     */
    var _isClobbered = function _isClobbered(elm) {
      return elm instanceof HTMLFormElement && (typeof elm.nodeName !== 'string' || typeof elm.textContent !== 'string' || typeof elm.removeChild !== 'function' || !(elm.attributes instanceof NamedNodeMap) || typeof elm.removeAttribute !== 'function' || typeof elm.setAttribute !== 'function' || typeof elm.namespaceURI !== 'string' || typeof elm.insertBefore !== 'function');
    };

    /**
     * _isNode
     *
     * @param  {Node} obj object to check whether it's a DOM node
     * @return {Boolean} true is object is a DOM node
     */
    var _isNode = function _isNode(object) {
      return (typeof Node === 'undefined' ? 'undefined' : _typeof(Node)) === 'object' ? object instanceof Node : object && (typeof object === 'undefined' ? 'undefined' : _typeof(object)) === 'object' && typeof object.nodeType === 'number' && typeof object.nodeName === 'string';
    };

    /**
     * _executeHook
     * Execute user configurable hooks
     *
     * @param  {String} entryPoint  Name of the hook's entry point
     * @param  {Node} currentNode node to work on with the hook
     * @param  {Object} data additional hook parameters
     */
    var _executeHook = function _executeHook(entryPoint, currentNode, data) {
      if (!hooks[entryPoint]) {
        return;
      }

      arrayForEach(hooks[entryPoint], function (hook) {
        hook.call(DOMPurify, currentNode, data, CONFIG);
      });
    };

    /**
     * _sanitizeElements
     *
     * @protect nodeName
     * @protect textContent
     * @protect removeChild
     *
     * @param   {Node} currentNode to check for permission to exist
     * @return  {Boolean} true if node was killed, false if left alive
     */
    var _sanitizeElements = function _sanitizeElements(currentNode) {
      var content = void 0;

      /* Execute a hook if present */
      _executeHook('beforeSanitizeElements', currentNode, null);

      /* Check if element is clobbered or can clobber */
      if (_isClobbered(currentNode)) {
        _forceRemove(currentNode);
        return true;
      }

      /* Check if tagname contains Unicode */
      if (stringMatch(currentNode.nodeName, /[\u0080-\uFFFF]/)) {
        _forceRemove(currentNode);
        return true;
      }

      /* Now let's check the element's type and name */
      var tagName = transformCaseFunc(currentNode.nodeName);

      /* Execute a hook if present */
      _executeHook('uponSanitizeElement', currentNode, {
        tagName: tagName,
        allowedTags: ALLOWED_TAGS
      });

      /* Detect mXSS attempts abusing namespace confusion */
      if (!_isNode(currentNode.firstElementChild) && (!_isNode(currentNode.content) || !_isNode(currentNode.content.firstElementChild)) && regExpTest(/<[/\w]/g, currentNode.innerHTML) && regExpTest(/<[/\w]/g, currentNode.textContent)) {
        _forceRemove(currentNode);
        return true;
      }

      /* Mitigate a problem with templates inside select */
      if (tagName === 'select' && regExpTest(/<template/i, currentNode.innerHTML)) {
        _forceRemove(currentNode);
        return true;
      }

      /* Remove element if anything forbids its presence */
      if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
        /* Check if we have a custom element to handle */
        if (!FORBID_TAGS[tagName] && _basicCustomElementTest(tagName)) {
          if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, tagName)) return false;
          if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(tagName)) return false;
        }

        /* Keep content except for bad-listed elements */
        if (KEEP_CONTENT && !FORBID_CONTENTS[tagName]) {
          var parentNode = getParentNode(currentNode) || currentNode.parentNode;
          var childNodes = getChildNodes(currentNode) || currentNode.childNodes;

          if (childNodes && parentNode) {
            var childCount = childNodes.length;

            for (var i = childCount - 1; i >= 0; --i) {
              parentNode.insertBefore(cloneNode(childNodes[i], true), getNextSibling(currentNode));
            }
          }
        }

        _forceRemove(currentNode);
        return true;
      }

      /* Check whether element has a valid namespace */
      if (currentNode instanceof Element && !_checkValidNamespace(currentNode)) {
        _forceRemove(currentNode);
        return true;
      }

      if ((tagName === 'noscript' || tagName === 'noembed') && regExpTest(/<\/no(script|embed)/i, currentNode.innerHTML)) {
        _forceRemove(currentNode);
        return true;
      }

      /* Sanitize element content to be template-safe */
      if (SAFE_FOR_TEMPLATES && currentNode.nodeType === 3) {
        /* Get the element's text content */
        content = currentNode.textContent;
        content = stringReplace(content, MUSTACHE_EXPR$$1, ' ');
        content = stringReplace(content, ERB_EXPR$$1, ' ');
        if (currentNode.textContent !== content) {
          arrayPush(DOMPurify.removed, { element: currentNode.cloneNode() });
          currentNode.textContent = content;
        }
      }

      /* Execute a hook if present */
      _executeHook('afterSanitizeElements', currentNode, null);

      return false;
    };

    /**
     * _isValidAttribute
     *
     * @param  {string} lcTag Lowercase tag name of containing element.
     * @param  {string} lcName Lowercase attribute name.
     * @param  {string} value Attribute value.
     * @return {Boolean} Returns true if `value` is valid, otherwise false.
     */
    // eslint-disable-next-line complexity
    var _isValidAttribute = function _isValidAttribute(lcTag, lcName, value) {
      /* Make sure attribute cannot clobber */
      if (SANITIZE_DOM && (lcName === 'id' || lcName === 'name') && (value in document || value in formElement)) {
        return false;
      }

      /* Allow valid data-* attributes: At least one character after "-"
          (https://html.spec.whatwg.org/multipage/dom.html#embedding-custom-non-visible-data-with-the-data-*-attributes)
          XML-compatible (https://html.spec.whatwg.org/multipage/infrastructure.html#xml-compatible and http://www.w3.org/TR/xml/#d0e804)
          We don't need to check the value; it's always URI safe. */
      if (ALLOW_DATA_ATTR && !FORBID_ATTR[lcName] && regExpTest(DATA_ATTR$$1, lcName)) ; else if (ALLOW_ARIA_ATTR && regExpTest(ARIA_ATTR$$1, lcName)) ; else if (!ALLOWED_ATTR[lcName] || FORBID_ATTR[lcName]) {
        if (
        // First condition does a very basic check if a) it's basically a valid custom element tagname AND
        // b) if the tagName passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
        // and c) if the attribute name passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.attributeNameCheck
        _basicCustomElementTest(lcTag) && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, lcTag) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(lcTag)) && (CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.attributeNameCheck, lcName) || CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.attributeNameCheck(lcName)) ||
        // Alternative, second condition checks if it's an `is`-attribute, AND
        // the value passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
        lcName === 'is' && CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, value) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(value))) ; else {
          return false;
        }
        /* Check value is safe. First, is attr inert? If so, is safe */
      } else if (URI_SAFE_ATTRIBUTES[lcName]) ; else if (regExpTest(IS_ALLOWED_URI$$1, stringReplace(value, ATTR_WHITESPACE$$1, ''))) ; else if ((lcName === 'src' || lcName === 'xlink:href' || lcName === 'href') && lcTag !== 'script' && stringIndexOf(value, 'data:') === 0 && DATA_URI_TAGS[lcTag]) ; else if (ALLOW_UNKNOWN_PROTOCOLS && !regExpTest(IS_SCRIPT_OR_DATA$$1, stringReplace(value, ATTR_WHITESPACE$$1, ''))) ; else if (!value) ; else {
        return false;
      }

      return true;
    };

    /**
     * _basicCustomElementCheck
     * checks if at least one dash is included in tagName, and it's not the first char
     * for more sophisticated checking see https://github.com/sindresorhus/validate-element-name
     * @param {string} tagName name of the tag of the node to sanitize
     */
    var _basicCustomElementTest = function _basicCustomElementTest(tagName) {
      return tagName.indexOf('-') > 0;
    };

    /**
     * _sanitizeAttributes
     *
     * @protect attributes
     * @protect nodeName
     * @protect removeAttribute
     * @protect setAttribute
     *
     * @param  {Node} currentNode to sanitize
     */
    var _sanitizeAttributes = function _sanitizeAttributes(currentNode) {
      var attr = void 0;
      var value = void 0;
      var lcName = void 0;
      var l = void 0;
      /* Execute a hook if present */
      _executeHook('beforeSanitizeAttributes', currentNode, null);

      var attributes = currentNode.attributes;

      /* Check if we have attributes; if not we might have a text node */

      if (!attributes) {
        return;
      }

      var hookEvent = {
        attrName: '',
        attrValue: '',
        keepAttr: true,
        allowedAttributes: ALLOWED_ATTR
      };
      l = attributes.length;

      /* Go backwards over all attributes; safely remove bad ones */
      while (l--) {
        attr = attributes[l];
        var _attr = attr,
            name = _attr.name,
            namespaceURI = _attr.namespaceURI;

        value = stringTrim(attr.value);
        lcName = transformCaseFunc(name);

        /* Execute a hook if present */
        hookEvent.attrName = lcName;
        hookEvent.attrValue = value;
        hookEvent.keepAttr = true;
        hookEvent.forceKeepAttr = undefined; // Allows developers to see this is a property they can set
        _executeHook('uponSanitizeAttribute', currentNode, hookEvent);
        value = hookEvent.attrValue;
        /* Did the hooks approve of the attribute? */
        if (hookEvent.forceKeepAttr) {
          continue;
        }

        /* Remove attribute */
        _removeAttribute(name, currentNode);

        /* Did the hooks approve of the attribute? */
        if (!hookEvent.keepAttr) {
          continue;
        }

        /* Work around a security issue in jQuery 3.0 */
        if (regExpTest(/\/>/i, value)) {
          _removeAttribute(name, currentNode);
          continue;
        }

        /* Sanitize attribute content to be template-safe */
        if (SAFE_FOR_TEMPLATES) {
          value = stringReplace(value, MUSTACHE_EXPR$$1, ' ');
          value = stringReplace(value, ERB_EXPR$$1, ' ');
        }

        /* Is `value` valid for this attribute? */
        var lcTag = transformCaseFunc(currentNode.nodeName);
        if (!_isValidAttribute(lcTag, lcName, value)) {
          continue;
        }

        /* Handle invalid data-* attribute set by try-catching it */
        try {
          if (namespaceURI) {
            currentNode.setAttributeNS(namespaceURI, name, value);
          } else {
            /* Fallback to setAttribute() for browser-unrecognized namespaces e.g. "x-schema". */
            currentNode.setAttribute(name, value);
          }

          arrayPop(DOMPurify.removed);
        } catch (_) {}
      }

      /* Execute a hook if present */
      _executeHook('afterSanitizeAttributes', currentNode, null);
    };

    /**
     * _sanitizeShadowDOM
     *
     * @param  {DocumentFragment} fragment to iterate over recursively
     */
    var _sanitizeShadowDOM = function _sanitizeShadowDOM(fragment) {
      var shadowNode = void 0;
      var shadowIterator = _createIterator(fragment);

      /* Execute a hook if present */
      _executeHook('beforeSanitizeShadowDOM', fragment, null);

      while (shadowNode = shadowIterator.nextNode()) {
        /* Execute a hook if present */
        _executeHook('uponSanitizeShadowNode', shadowNode, null);

        /* Sanitize tags and elements */
        if (_sanitizeElements(shadowNode)) {
          continue;
        }

        /* Deep shadow DOM detected */
        if (shadowNode.content instanceof DocumentFragment) {
          _sanitizeShadowDOM(shadowNode.content);
        }

        /* Check attributes, sanitize if necessary */
        _sanitizeAttributes(shadowNode);
      }

      /* Execute a hook if present */
      _executeHook('afterSanitizeShadowDOM', fragment, null);
    };

    /**
     * Sanitize
     * Public method providing core sanitation functionality
     *
     * @param {String|Node} dirty string or DOM node
     * @param {Object} configuration object
     */
    // eslint-disable-next-line complexity
    DOMPurify.sanitize = function (dirty, cfg) {
      var body = void 0;
      var importedNode = void 0;
      var currentNode = void 0;
      var oldNode = void 0;
      var returnNode = void 0;
      /* Make sure we have a string to sanitize.
        DO NOT return early, as this will return the wrong type if
        the user has requested a DOM object rather than a string */
      IS_EMPTY_INPUT = !dirty;
      if (IS_EMPTY_INPUT) {
        dirty = '<!-->';
      }

      /* Stringify, in case dirty is an object */
      if (typeof dirty !== 'string' && !_isNode(dirty)) {
        // eslint-disable-next-line no-negated-condition
        if (typeof dirty.toString !== 'function') {
          throw typeErrorCreate('toString is not a function');
        } else {
          dirty = dirty.toString();
          if (typeof dirty !== 'string') {
            throw typeErrorCreate('dirty is not a string, aborting');
          }
        }
      }

      /* Check we can run. Otherwise fall back or ignore */
      if (!DOMPurify.isSupported) {
        if (_typeof(window.toStaticHTML) === 'object' || typeof window.toStaticHTML === 'function') {
          if (typeof dirty === 'string') {
            return window.toStaticHTML(dirty);
          }

          if (_isNode(dirty)) {
            return window.toStaticHTML(dirty.outerHTML);
          }
        }

        return dirty;
      }

      /* Assign config vars */
      if (!SET_CONFIG) {
        _parseConfig(cfg);
      }

      /* Clean up removed elements */
      DOMPurify.removed = [];

      /* Check if dirty is correctly typed for IN_PLACE */
      if (typeof dirty === 'string') {
        IN_PLACE = false;
      }

      if (IN_PLACE) {
        /* Do some early pre-sanitization to avoid unsafe root nodes */
        if (dirty.nodeName) {
          var tagName = transformCaseFunc(dirty.nodeName);
          if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
            throw typeErrorCreate('root node is forbidden and cannot be sanitized in-place');
          }
        }
      } else if (dirty instanceof Node) {
        /* If dirty is a DOM element, append to an empty document to avoid
           elements being stripped by the parser */
        body = _initDocument('<!---->');
        importedNode = body.ownerDocument.importNode(dirty, true);
        if (importedNode.nodeType === 1 && importedNode.nodeName === 'BODY') {
          /* Node is already a body, use as is */
          body = importedNode;
        } else if (importedNode.nodeName === 'HTML') {
          body = importedNode;
        } else {
          // eslint-disable-next-line unicorn/prefer-dom-node-append
          body.appendChild(importedNode);
        }
      } else {
        /* Exit directly if we have nothing to do */
        if (!RETURN_DOM && !SAFE_FOR_TEMPLATES && !WHOLE_DOCUMENT &&
        // eslint-disable-next-line unicorn/prefer-includes
        dirty.indexOf('<') === -1) {
          return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(dirty) : dirty;
        }

        /* Initialize the document to work on */
        body = _initDocument(dirty);

        /* Check we have a DOM node from the data */
        if (!body) {
          return RETURN_DOM ? null : RETURN_TRUSTED_TYPE ? emptyHTML : '';
        }
      }

      /* Remove first element node (ours) if FORCE_BODY is set */
      if (body && FORCE_BODY) {
        _forceRemove(body.firstChild);
      }

      /* Get node iterator */
      var nodeIterator = _createIterator(IN_PLACE ? dirty : body);

      /* Now start iterating over the created document */
      while (currentNode = nodeIterator.nextNode()) {
        /* Fix IE's strange behavior with manipulated textNodes #89 */
        if (currentNode.nodeType === 3 && currentNode === oldNode) {
          continue;
        }

        /* Sanitize tags and elements */
        if (_sanitizeElements(currentNode)) {
          continue;
        }

        /* Shadow DOM detected, sanitize it */
        if (currentNode.content instanceof DocumentFragment) {
          _sanitizeShadowDOM(currentNode.content);
        }

        /* Check attributes, sanitize if necessary */
        _sanitizeAttributes(currentNode);

        oldNode = currentNode;
      }

      oldNode = null;

      /* If we sanitized `dirty` in-place, return it. */
      if (IN_PLACE) {
        return dirty;
      }

      /* Return sanitized string or DOM */
      if (RETURN_DOM) {
        if (RETURN_DOM_FRAGMENT) {
          returnNode = createDocumentFragment.call(body.ownerDocument);

          while (body.firstChild) {
            // eslint-disable-next-line unicorn/prefer-dom-node-append
            returnNode.appendChild(body.firstChild);
          }
        } else {
          returnNode = body;
        }

        if (ALLOWED_ATTR.shadowroot) {
          /*
            AdoptNode() is not used because internal state is not reset
            (e.g. the past names map of a HTMLFormElement), this is safe
            in theory but we would rather not risk another attack vector.
            The state that is cloned by importNode() is explicitly defined
            by the specs.
          */
          returnNode = importNode.call(originalDocument, returnNode, true);
        }

        return returnNode;
      }

      var serializedHTML = WHOLE_DOCUMENT ? body.outerHTML : body.innerHTML;

      /* Serialize doctype if allowed */
      if (WHOLE_DOCUMENT && ALLOWED_TAGS['!doctype'] && body.ownerDocument && body.ownerDocument.doctype && body.ownerDocument.doctype.name && regExpTest(DOCTYPE_NAME, body.ownerDocument.doctype.name)) {
        serializedHTML = '<!DOCTYPE ' + body.ownerDocument.doctype.name + '>\n' + serializedHTML;
      }

      /* Sanitize final string template-safe */
      if (SAFE_FOR_TEMPLATES) {
        serializedHTML = stringReplace(serializedHTML, MUSTACHE_EXPR$$1, ' ');
        serializedHTML = stringReplace(serializedHTML, ERB_EXPR$$1, ' ');
      }

      return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(serializedHTML) : serializedHTML;
    };

    /**
     * Public method to set the configuration once
     * setConfig
     *
     * @param {Object} cfg configuration object
     */
    DOMPurify.setConfig = function (cfg) {
      _parseConfig(cfg);
      SET_CONFIG = true;
    };

    /**
     * Public method to remove the configuration
     * clearConfig
     *
     */
    DOMPurify.clearConfig = function () {
      CONFIG = null;
      SET_CONFIG = false;
    };

    /**
     * Public method to check if an attribute value is valid.
     * Uses last set config, if any. Otherwise, uses config defaults.
     * isValidAttribute
     *
     * @param  {string} tag Tag name of containing element.
     * @param  {string} attr Attribute name.
     * @param  {string} value Attribute value.
     * @return {Boolean} Returns true if `value` is valid. Otherwise, returns false.
     */
    DOMPurify.isValidAttribute = function (tag, attr, value) {
      /* Initialize shared config vars if necessary. */
      if (!CONFIG) {
        _parseConfig({});
      }

      var lcTag = transformCaseFunc(tag);
      var lcName = transformCaseFunc(attr);
      return _isValidAttribute(lcTag, lcName, value);
    };

    /**
     * AddHook
     * Public method to add DOMPurify hooks
     *
     * @param {String} entryPoint entry point for the hook to add
     * @param {Function} hookFunction function to execute
     */
    DOMPurify.addHook = function (entryPoint, hookFunction) {
      if (typeof hookFunction !== 'function') {
        return;
      }

      hooks[entryPoint] = hooks[entryPoint] || [];
      arrayPush(hooks[entryPoint], hookFunction);
    };

    /**
     * RemoveHook
     * Public method to remove a DOMPurify hook at a given entryPoint
     * (pops it from the stack of hooks if more are present)
     *
     * @param {String} entryPoint entry point for the hook to remove
     */
    DOMPurify.removeHook = function (entryPoint) {
      if (hooks[entryPoint]) {
        arrayPop(hooks[entryPoint]);
      }
    };

    /**
     * RemoveHooks
     * Public method to remove all DOMPurify hooks at a given entryPoint
     *
     * @param  {String} entryPoint entry point for the hooks to remove
     */
    DOMPurify.removeHooks = function (entryPoint) {
      if (hooks[entryPoint]) {
        hooks[entryPoint] = [];
      }
    };

    /**
     * RemoveAllHooks
     * Public method to remove all DOMPurify hooks
     *
     */
    DOMPurify.removeAllHooks = function () {
      hooks = {};
    };

    return DOMPurify;
  }

  var purify = createDOMPurify();

  return purify;

}));
//# sourceMappingURL=purify.js.map


/***/ }),

/***/ "./src/builder/ui/3rd-party/query-monitor/style.scss":
/*!***********************************************************!*\
  !*** ./src/builder/ui/3rd-party/query-monitor/style.scss ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/art/style.scss":
/*!***************************************!*\
  !*** ./src/builder/ui/art/style.scss ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/context-menu/menu/style.scss":
/*!*****************************************************!*\
  !*** ./src/builder/ui/context-menu/menu/style.scss ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/inline-editor/style.scss":
/*!*************************************************!*\
  !*** ./src/builder/ui/inline-editor/style.scss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/notifications/style.scss":
/*!*************************************************!*\
  !*** ./src/builder/ui/notifications/style.scss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/outline-panel/outline/style.scss":
/*!*********************************************************!*\
  !*** ./src/builder/ui/outline-panel/outline/style.scss ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/outline-panel/style.scss":
/*!*************************************************!*\
  !*** ./src/builder/ui/outline-panel/style.scss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/panel-manager/style.scss":
/*!*************************************************!*\
  !*** ./src/builder/ui/panel-manager/style.scss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/shortcuts-panel/panel/style.scss":
/*!*********************************************************!*\
  !*** ./src/builder/ui/shortcuts-panel/panel/style.scss ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/shortcuts-panel/style.scss":
/*!***************************************************!*\
  !*** ./src/builder/ui/shortcuts-panel/style.scss ***!
  \***************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/builder/ui/style.scss":
/*!***********************************!*\
  !*** ./src/builder/ui/style.scss ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@beaverbuilder/app-core":
/*!***************************************!*\
  !*** external "FL.vendors.BBAppCore" ***!
  \***************************************/
/***/ ((module) => {

"use strict";
module.exports = FL.vendors.BBAppCore;

/***/ }),

/***/ "@beaverbuilder/fluid":
/*!*************************************!*\
  !*** external "FL.vendors.BBFluid" ***!
  \*************************************/
/***/ ((module) => {

"use strict";
module.exports = FL.vendors.BBFluid;

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = React;

/***/ }),

/***/ "react-dom":
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/***/ ((module) => {

"use strict";
module.exports = ReactDOM;

/***/ }),

/***/ "redux":
/*!************************!*\
  !*** external "Redux" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = Redux;

/***/ }),

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ ((module) => {

"use strict";
module.exports = wp.i18n;

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
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!******************************!*\
  !*** ./src/builder/index.js ***!
  \******************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./data */ "./src/builder/data/index.js");
/* harmony import */ var _ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ui */ "./src/builder/ui/index.js");
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./api */ "./src/builder/api/index.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }







var _publicAPI$getActions = _api__WEBPACK_IMPORTED_MODULE_4__.getActions(),
    registerPanel = _publicAPI$getActions.registerPanel,
    displayPanel = _publicAPI$getActions.displayPanel,
    togglePanel = _publicAPI$getActions.togglePanel; // Setup public API - window.FL.Builder


var api = window.FL || {};
var existing = api.Builder || {};

var Builder = _objectSpread(_objectSpread(_objectSpread({}, existing), _api__WEBPACK_IMPORTED_MODULE_4__), {}, {
  data: _data__WEBPACK_IMPORTED_MODULE_2__,
  registerPanel: registerPanel,
  displayPanel: displayPanel,
  togglePanel: togglePanel
});

window.FL = _objectSpread(_objectSpread({}, api), {}, {
  Builder: Builder
}); // Needs to happen after FL.Builder.data API is available

(0,_ui__WEBPACK_IMPORTED_MODULE_3__.registerPanels)(); // Render UI

var root = document.getElementById('fl-ui-root');
root.classList.add('fluid', 'fl', 'uid');
(0,react_dom__WEBPACK_IMPORTED_MODULE_1__.render)( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_ui__WEBPACK_IMPORTED_MODULE_3__["default"], null), root);
})();

/******/ })()
;
//# sourceMappingURL=builder.bundle.js.map