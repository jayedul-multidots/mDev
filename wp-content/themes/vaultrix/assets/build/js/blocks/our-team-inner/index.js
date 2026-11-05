/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/blocks/our-team-inner/edit.js"
/*!**********************************************!*\
  !*** ./src/js/blocks/our-team-inner/edit.js ***!
  \**********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_5__);

/* eslint-disable no-unused-expressions */
/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */


/**
 * React hook that is used to mark the block wrapper element.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/
 */



/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */


// eslint-disable-next-line import/no-extraneous-dependencies

function Edit(props) {
  const {
    attributes,
    setAttributes,
    clientId,
    className
  } = props;
  const {
    blockID,
    listItems,
    columns
  } = attributes;
  const addNewItem = () => {
    let listItemsArr = listItems;
    const newObject = [...listItems, {
      title: '',
      subTitle: '',
      imageID: '',
      imageURL: '',
      imageAlt: '',
      imageWidth: '',
      imageHeight: ''
    }];
    listItemsArr = newObject;
    setAttributes({
      listItems: listItemsArr
    });
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useEffect)(() => {
    if (0 === listItems.length) {
      initListItems();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  const initListItems = () => {
    setAttributes({
      listItems: [...listItems, {
        title: '',
        subTitle: '',
        imageID: '',
        imageURL: '',
        imageAlt: '',
        imageWidth: '',
        imageHeight: ''
      }]
    });
  };
  const moveItem = (oldIndex, newIndex) => {
    const arrayCopy = [...listItems];
    arrayCopy[oldIndex] = listItems[newIndex];
    arrayCopy[newIndex] = listItems[oldIndex];
    setAttributes({
      listItems: arrayCopy
    });
  };
  if (!blockID) {
    setAttributes({
      blockID: `our-team-${clientId}`
    });
  }
  const classes = classnames__WEBPACK_IMPORTED_MODULE_5___default()(className, 'our-team-inner', `has-${columns}-columns`);
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)({
    className: classes,
    id: blockID
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "dp-block-sidebar"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Settings', 'vaultrix'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.RangeControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Columns', 'vaultrix'),
    min: 3,
    max: 4,
    onChange: newColumns => {
      setAttributes({
        columns: newColumns
      });
    },
    value: columns
  })))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...blockProps
  }, 0 < listItems.length && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, 0 < listItems.length && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: `our-team__boxes`
  }, listItems.map((data, index) => {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      key: index,
      className: "our-team__box show-items-hover-wrap"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: `item-action-wrap u-margin-b10`
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "move-item"
    }, 0 < index && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
      text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Move Left', 'vaultrix')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: "dashicons dashicons-arrow-left-alt move-left",
      "aria-hidden": "true",
      onClick: () => moveItem(index, index - 1)
    })), index + 1 < listItems.length && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
      text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Move Right', 'vaultrix')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      onKeyUp: () => {},
      role: "button",
      tabIndex: 0,
      className: "dashicons dashicons-arrow-right-alt move-right",
      onClick: () => moveItem(index, index + 1)
    }))), 1 < listItems.length && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
      text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Remove Item', 'vaultrix')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
      className: "remove-item dashicons dashicons-no-alt",
      onKeyUp: () => {},
      role: "button",
      tabIndex: 0,
      onClick: () => {
        const toDelete =
        // eslint-disable-next-line no-alert
        confirm((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Are you sure you want to delete this item?', 'vaultrix'));
        if (true === toDelete) {
          const arrayCopy = [...listItems];
          arrayCopy.splice(index, 1);
          setAttributes({
            listItems: arrayCopy
          });
        }
      }
    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: `our-team__image`
    }, data.imageURL && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "dp-block-control image-preview image-controle-visible-hover"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: `image-controls small-icons icon-center-fixed`
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
      onSelect: item => {
        const arrayCopy = [...listItems];
        arrayCopy[index].imageID = item.id;
        arrayCopy[index].imageURL = item.url;
        arrayCopy[index].imageAlt = item.alt;
        arrayCopy[index].imageWidth = item.width;
        arrayCopy[index].imageHeight = item.height;
        setAttributes({
          listItems: arrayCopy
        });
      },
      allowedTypes: ['image'],
      value: data.imageID,
      render: ({
        open
      }) => {
        return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
          text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Edit Image', 'vaultrix')
        }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
          onKeyUp: () => {},
          role: "button",
          tabIndex: 0,
          className: "dashicons dashicons-edit edit-image",
          onClick: open
        }));
      }
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
      text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Remove Image', 'vaultrix')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
      onKeyUp: () => {},
      role: "button",
      tabIndex: 0,
      className: "dashicons dashicons-no-alt remove-image",
      onClick: () => {
        const arrayCopy = [...listItems];
        arrayCopy[index].imageID = '';
        arrayCopy[index].imageURL = '';
        arrayCopy[index].imageAlt = '';
        arrayCopy[index].imageWidth = '';
        arrayCopy[index].imageHeight = '';
        setAttributes({
          listItems: arrayCopy
        });
      }
    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
      width: data.imageWidth,
      height: data.imageHeight,
      src: data.imageURL,
      alt: data.imageAlt
    })), !data.imageURL && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
      onSelect: item => {
        const arrayCopy = [...listItems];
        arrayCopy[index].imageID = item.id;
        arrayCopy[index].imageURL = item.url;
        arrayCopy[index].imageAlt = item.alt;
        arrayCopy[index].imageWidth = item.width;
        arrayCopy[index].imageHeight = item.height;
        setAttributes({
          listItems: arrayCopy
        });
      },
      allowedTypes: ['image'],
      value: data.imageURL,
      render: ({
        open
      }) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: "upload-wrap"
      }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Upload Image of size 365 x 320px', 'vaultrix')
      }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, {
        onClick: open,
        className: "button media_and_text__upload_btn"
      }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
        className: "dashicons dashicons-upload"
      }))))
    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: `our-team__box_content`
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.RichText, {
      tagName: "h3",
      placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enter Member Name', 'vaultrix'),
      value: data.title,
      onChange: value => {
        const arrayCopy = [...listItems];
        arrayCopy[index].title = value;
        setAttributes({
          listItems: arrayCopy
        });
      },
      className: "our-team__title"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.RichText, {
      tagName: "p",
      placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enter Member Designation', 'vaultrix'),
      value: data.subTitle,
      onChange: value => {
        const arrayCopy = [...listItems];
        arrayCopy[index].subTitle = value;
        setAttributes({
          listItems: arrayCopy
        });
      },
      className: "our-team__subtitle"
    })));
  }))), listItems.length < 5 && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    onKeyUp: () => {},
    role: "button",
    tabIndex: 0,
    className: "add-item-wrap",
    onClick: () => {
      addNewItem();
    }
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
    text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Add New Item', 'vaultrix')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
    className: "add-new-item dashicons dashicons-plus",
    "aria-hidden": "true"
  })))));
}

/***/ },

/***/ "./src/js/blocks/our-team-inner/save.js"
/*!**********************************************!*\
  !*** ./src/js/blocks/our-team-inner/save.js ***!
  \**********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ save)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);

/* eslint-disable no-unused-expressions */
/**
 * React hook that is used to mark the block wrapper element.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/
 */

// eslint-disable-next-line import/no-extraneous-dependencies


/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 * @param {Object} props
 * @return {WPElement} Element to render.
 */
function save(props) {
  const {
    attributes,
    className
  } = props;
  const {
    blockID,
    listItems,
    columns
  } = attributes;
  const classes = classnames__WEBPACK_IMPORTED_MODULE_2___default()(className, 'our-team-inner', `has-${columns}-columns`);
  const blockProps = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps.save({
    className: classes,
    id: blockID
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...blockProps
  }, 0 < listItems.length && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: `our-team__boxes`
  }, listItems.map((data, index) => {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "our-team__box",
      key: index
    }, data.imageURL && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: `our-team__image`
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
      width: data.imageWidth,
      height: data.imageHeight,
      src: data.imageURL,
      alt: data.imageAlt
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "our-team__box_content"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.RichText.Content, {
      tagName: "h3",
      value: data.title,
      className: "our-team__title"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.RichText.Content, {
      tagName: "p",
      value: data.subTitle,
      className: "our-team__subtitle"
    })));
  })));
}

/***/ },

/***/ "react"
/*!************************!*\
  !*** external "React" ***!
  \************************/
(module) {

"use strict";
module.exports = window["React"];

/***/ },

/***/ "@wordpress/block-editor"
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
(module) {

"use strict";
module.exports = window["wp"]["blockEditor"];

/***/ },

/***/ "@wordpress/blocks"
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
(module) {

"use strict";
module.exports = window["wp"]["blocks"];

/***/ },

/***/ "@wordpress/components"
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
(module) {

"use strict";
module.exports = window["wp"]["components"];

/***/ },

/***/ "@wordpress/element"
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
(module) {

"use strict";
module.exports = window["wp"]["element"];

/***/ },

/***/ "@wordpress/i18n"
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
(module) {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ },

/***/ "./node_modules/classnames/index.js"
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
(module, exports) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = '';

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (arg) {
				classes = appendClass(classes, parseValue(arg));
			}
		}

		return classes;
	}

	function parseValue (arg) {
		if (typeof arg === 'string' || typeof arg === 'number') {
			return arg;
		}

		if (typeof arg !== 'object') {
			return '';
		}

		if (Array.isArray(arg)) {
			return classNames.apply(null, arg);
		}

		if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
			return arg.toString();
		}

		var classes = '';

		for (var key in arg) {
			if (hasOwn.call(arg, key) && arg[key]) {
				classes = appendClass(classes, key);
			}
		}

		return classes;
	}

	function appendClass (value, newClass) {
		if (!newClass) {
			return value;
		}
	
		if (value) {
			return value + ' ' + newClass;
		}
	
		return value + newClass;
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
	} else // removed by dead control flow
{}
}());


/***/ },

/***/ "./src/js/blocks/our-team-inner/block.json"
/*!*************************************************!*\
  !*** ./src/js/blocks/our-team-inner/block.json ***!
  \*************************************************/
(module) {

"use strict";
module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","name":"vaultrix/our-team-inner","version":"0.1.0","title":"Our Team Inner","category":"vaultrix","icon":"dashicons dashicons-admin-users dp-block-icon","description":"This block describes information about team member of Vaultrix.","keywords":["our","team"],"supports":{"html":false},"textdomain":"vaultrix","attributes":{"blockID":{"type":"string","default":""},"listItems":{"type":"array","default":[]},"columns":{"type":"number","default":3}},"editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css"}');

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	const __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		const cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		const module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			const e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
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
/******/ 			const getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter/value functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			if(Array.isArray(definition)) {
/******/ 				var i = 0;
/******/ 				while(i < definition.length) {
/******/ 					var key = definition[i++];
/******/ 					var binding = definition[i++];
/******/ 					if(!__webpack_require__.o(exports, key)) {
/******/ 						if(binding === 0) {
/******/ 							Object.defineProperty(exports, key, { enumerable: true, value: definition[i++] });
/******/ 						} else {
/******/ 							Object.defineProperty(exports, key, { enumerable: true, get: binding });
/******/ 						}
/******/ 					} else if(binding === 0) { i++; }
/******/ 				}
/******/ 			} else {
/******/ 				for(var key in definition) {
/******/ 					if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 						Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.hasOwn(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
let __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!***********************************************!*\
  !*** ./src/js/blocks/our-team-inner/index.js ***!
  \***********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./src/js/blocks/our-team-inner/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./save */ "./src/js/blocks/our-team-inner/save.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./src/js/blocks/our-team-inner/block.json");
/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */


/**
 * Internal dependencies
 */




/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__, {
  parent: ['vaultrix/our-team'],
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  /**
   * @see ./save.js
   */
  save: _save__WEBPACK_IMPORTED_MODULE_2__["default"]
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map