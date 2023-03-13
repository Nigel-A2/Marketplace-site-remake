/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/core-data":
/*!**********************************!*\
  !*** external ["wp","coreData"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["coreData"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/html-entities":
/*!**************************************!*\
  !*** external ["wp","htmlEntities"] ***!
  \**************************************/
/***/ (function(module) {

module.exports = window["wp"]["htmlEntities"];

/***/ }),

/***/ "@wordpress/notices":
/*!*********************************!*\
  !*** external ["wp","notices"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["notices"];

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
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "CreatePageForm": function() { return /* binding */ CreatePageForm; },
/* harmony export */   "EditPageForm": function() { return /* binding */ EditPageForm; },
/* harmony export */   "PageForm": function() { return /* binding */ PageForm; },
/* harmony export */   "VanillaReactForm": function() { return /* binding */ VanillaReactForm; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_notices__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/notices */ "@wordpress/notices");
/* harmony import */ var _wordpress_notices__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_notices__WEBPACK_IMPORTED_MODULE_5__);







/*
    This file gives the plugin the full range of CRUD (Create, Read, Update, Delete) operations for WordPress pages
    inside the WordPress dashboard by adding a new section to it.

    Main file as defined in package.json
*/

// create edit form
function EditPageForm(_ref) {
  let {
    pageId,
    onCancel,
    onSaveFinished
  } = _ref;
  const {
    page,
    lastError,
    isSaving,
    hasEdits
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => ({
    page: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).getEditedEntityRecord('postType', 'page', pageId),
    lastError: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).getLastEntitySaveError('postType', 'page', pageId),
    isSaving: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).isSavingEntityRecord('postType', 'page', pageId),
    hasEdits: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).hasEditsForEntityRecord('postType', 'page', pageId)
  }), [pageId]);
  const {
    saveEditedEntityRecord,
    editEntityRecord
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store);
  const handleSave = async () => {
    const savedRecord = await saveEditedEntityRecord('postType', 'page', pageId);
    if (savedRecord) {
      onSaveFinished();
    }
  };
  const handleChange = title => editEntityRecord('postType', 'page', page.id, {
    title
  });
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(PageForm, {
    title: page.title,
    onChangeTitle: handleChange,
    hasEdits: hasEdits,
    lastError: lastError,
    isSaving: isSaving,
    onCancel: onCancel,
    onSave: handleSave
  });
}

// Adds the UI for the edit form
function PageForm(_ref2) {
  let {
    title,
    onChangeTitle,
    hasEdits,
    lastError,
    isSaving,
    onCancel,
    onSave
  } = _ref2;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "my-gutenberg-form"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextControl, {
    label: "Page title:",
    value: title,
    onChange: onChangeTitle
  }), lastError ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "form-error"
  }, "Error: ", lastError.message) : false, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "form-buttons"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, {
    onClick: onSave,
    variant: "primary",
    disabled: !hasEdits || isSaving
  }, isSaving ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Spinner, null), "Saving") : 'Save'), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, {
    onClick: onCancel,
    variant: "tertiary",
    disabled: isSaving
  }, "Cancel")));
}
function VanillaReactForm(_ref3) {
  let {
    initialTitle
  } = _ref3;
  const [title, setTitle] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialTitle);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextControl, {
    value: title,
    onChange: setTitle
  });
}

// grant the ability to create a new page with this plugin
function CreatePageButton() {
  const [isOpen, setOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const openModal = () => setOpen(true);
  const closeModal = () => setOpen(false);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, {
    onClick: openModal,
    variant: "primary"
  }, "Create a new Page"), isOpen && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Modal, {
    onRequestClose: closeModal,
    title: "Create a new page"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CreatePageForm, {
    onCancel: closeModal,
    onSaveFinished: closeModal
  })));
}
function CreatePageForm(_ref4) {
  let {
    onCancel,
    onSaveFinished
  } = _ref4;
  const [title, setTitle] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)();
  const handleChange = title => setTitle(title);
  const {
    saveEntityRecord
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store);
  const {
    lastError,
    isSaving
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => ({
    // Notice the missing pageId argument:
    lastError: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).getLastEntitySaveError('postType', 'page'),
    // Notice the missing pageId argument
    isSaving: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).isSavingEntityRecord('postType', 'page')
  }), []);
  const handleSave = async () => {
    const savedRecord = await saveEntityRecord('postType', 'page', {
      title,
      status: 'publish'
    });
    if (savedRecord) {
      onSaveFinished();
    }
  };
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(PageForm, {
    title: title,
    onChangeTitle: setTitle,
    hasEdits: !!title,
    onSave: handleSave,
    onCancel: onCancel,
    lastError: lastError,
    isSaving: isSaving
  });
}
function PageEditButton(_ref5) {
  let {
    pageId
  } = _ref5;
  const [isOpen, setOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const openModal = () => setOpen(true);
  const closeModal = () => setOpen(false);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, {
    onClick: openModal,
    variant: "primary"
  }, "Edit"), isOpen && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Modal, {
    onRequestClose: closeModal,
    title: "Edit page"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(EditPageForm, {
    pageId: pageId,
    onCancel: closeModal,
    onSaveFinished: closeModal
  })));
}

// Adds the searchbar, and page list
function MyFirstApp() {
  const [searchTerm, setSearchTerm] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const {
    pages,
    hasResolved
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => {
    const query = {};
    if (searchTerm) {
      query.search = searchTerm;
    }
    const selectorArgs = ['postType', 'page', query];
    return {
      pages: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).getEntityRecords(...selectorArgs),
      hasResolved: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).hasFinishedResolution('getEntityRecords', selectorArgs)
    };
  }, [searchTerm]);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "list-controls"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.SearchControl, {
    onChange: setSearchTerm,
    value: searchTerm
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CreatePageButton, null)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(PagesList, {
    hasResolved: hasResolved,
    pages: pages
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Notifications, null));
}
window.addEventListener('load', function () {
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(MyFirstApp, null), document.querySelector('#my-first-gutenberg-app'));
}, false);

// adds the notification system
function Notifications() {
  const notices = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => select(_wordpress_notices__WEBPACK_IMPORTED_MODULE_5__.store).getNotices(), []);
  const {
    removeNotice
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)(_wordpress_notices__WEBPACK_IMPORTED_MODULE_5__.store);
  const snackbarNotices = notices.filter(_ref6 => {
    let {
      type
    } = _ref6;
    return type === 'snackbar';
  });
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.SnackbarList, {
    notices: snackbarNotices,
    className: "components-editor-notices__snackbar",
    onRemove: removeNotice
  });
}

// adds a delete button to the pages list
function DeletePageButton(_ref7) {
  let {
    pageId
  } = _ref7;
  const {
    deleteEntityRecord
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store);
  const {
    createSuccessNotice,
    createErrorNotice
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)(_wordpress_notices__WEBPACK_IMPORTED_MODULE_5__.store);
  // useSelect returns a list of selectors if you pass the store handle
  // instead of a callback:
  const {
    getLastEntityDeleteError
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store);
  const handleDelete = async () => {
    const success = await deleteEntityRecord('postType', 'page', pageId);
    if (success) {
      // Tell the user the delete succeeded
      createSuccessNotice("The page was deleted!", {
        type: 'snackbar'
      });
    } else {
      // We use the selector directly to get the fresh error *after* the deleteEntityRecord
      // have failed.
      const lastError = getLastEntityDeleteError('postType', 'page', pageId);
      const message = ((lastError === null || lastError === void 0 ? void 0 : lastError.message) || 'There was an error.') + ' Please refresh the page and try again.';
      // Tell the user how exactly the delete has failed
      createErrorNotice(message, {
        type: 'snackbar'
      });
    }
  };
  const {
    isDeleting
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => ({
    isDeleting: select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.store).isDeletingEntityRecord('postType', 'page', pageId)
  }), [pageId]);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, {
    variant: "primary",
    onClick: handleDelete,
    disabled: isDeleting
  }, isDeleting ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Spinner, null), "Deleting...") : 'Delete');
}

// loads the list of pages
function PagesList(_ref8) {
  let {
    hasResolved,
    pages
  } = _ref8;
  if (!hasResolved) {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Spinner, null);
  }
  if (!(pages !== null && pages !== void 0 && pages.length)) {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, "No results");
  }
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("table", {
    className: "wp-list-table widefat fixed striped table-view-list"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("thead", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("tr", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("td", null, "Title"))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("tbody", null, pages === null || pages === void 0 ? void 0 : pages.map(page => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("tr", {
    key: page.id
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("td", null, (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(page.title.rendered)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("td", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(PageEditButton, {
    pageId: page.id
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(DeletePageButton, {
    pageId: page.id
  }))))));
}
}();
/******/ })()
;
//# sourceMappingURL=index.js.map