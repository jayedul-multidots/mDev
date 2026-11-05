/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/blocks/common.js"
/*!*********************************!*\
  !*** ./src/js/blocks/common.js ***!
  \*********************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundPositions: () => (/* binding */ backgroundPositions),
/* harmony export */   backgroundRepeats: () => (/* binding */ backgroundRepeats),
/* harmony export */   backgroundSizes: () => (/* binding */ backgroundSizes),
/* harmony export */   backgroundblendmode: () => (/* binding */ backgroundblendmode),
/* harmony export */   headingOptions: () => (/* binding */ headingOptions),
/* harmony export */   prjdpBtnStyles: () => (/* binding */ prjdpBtnStyles),
/* harmony export */   prjdpColors: () => (/* binding */ prjdpColors)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);


// background image sizes
const backgroundSizes = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select Background Size', 'vaultrix'),
  value: ''
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Auto', 'vaultrix'),
  value: 'auto'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Cover', 'vaultrix'),
  value: 'cover'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Contain', 'vaultrix'),
  value: 'contain'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Initial', 'vaultrix'),
  value: 'initial'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Inherit', 'vaultrix'),
  value: 'inherit'
}];

// background image positions
const backgroundPositions = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select Position', 'vaultrix'),
  value: ''
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Inherit', 'vaultrix'),
  value: 'inherit'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Initial', 'vaultrix'),
  value: 'initial'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Bottom', 'vaultrix'),
  value: 'bottom'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Center', 'vaultrix'),
  value: 'center'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Left', 'vaultrix'),
  value: 'left'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Right', 'vaultrix'),
  value: 'right'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Top', 'vaultrix'),
  value: 'top'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Unset', 'vaultrix'),
  value: 'unset'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Center center', 'vaultrix'),
  value: 'center center'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Left top', 'vaultrix'),
  value: 'left top'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Left center', 'vaultrix'),
  value: 'left center'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Left bottom', 'vaultrix'),
  value: 'left bottom'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Right top', 'vaultrix'),
  value: 'right top'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Right center', 'vaultrix'),
  value: 'right center'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Right bottom', 'vaultrix'),
  value: 'right bottom'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Center top', 'vaultrix'),
  value: 'center top'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Center bottom', 'vaultrix'),
  value: 'center bottom'
}];

// background image repeats
const backgroundRepeats = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select Background Repeat', 'vaultrix'),
  value: ''
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Inherit', 'vaultrix'),
  value: 'inherit'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No-repeat', 'vaultrix'),
  value: 'no-repeat'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Repeat', 'vaultrix'),
  value: 'repeat'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Repeat-x', 'vaultrix'),
  value: 'repeat-x'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Repeat-x', 'vaultrix'),
  value: 'repeat-y'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Revert', 'vaultrix'),
  value: 'revert'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Round', 'vaultrix'),
  value: 'round'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Space', 'vaultrix'),
  value: 'space'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Unset', 'vaultrix'),
  value: 'unset'
}];
const backgroundblendmode = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select Background Blend Mode', 'vaultrix'),
  value: ''
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Normal', 'vaultrix'),
  value: 'normal'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Multiply', 'vaultrix'),
  value: 'multiply'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Screen', 'vaultrix'),
  value: 'screen'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Overlay', 'vaultrix'),
  value: 'overlay'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Darken', 'vaultrix'),
  value: 'darken'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Lighten', 'vaultrix'),
  value: 'lighten'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Color Dodge', 'vaultrix'),
  value: 'color-dodge'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Color Burn', 'vaultrix'),
  value: 'color-burn'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Hard Light', 'vaultrix'),
  value: 'hard-light'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Soft Light', 'vaultrix'),
  value: 'soft-light'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Difference', 'vaultrix'),
  value: 'difference'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Exclusion', 'vaultrix'),
  value: 'exclusion'
}];
const headingOptions = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('H1', 'vaultrix'),
  value: 'h1'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('H2', 'vaultrix'),
  value: 'h2'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('H3', 'vaultrix'),
  value: 'h3'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('H4', 'vaultrix'),
  value: 'h4'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('H5', 'vaultrix'),
  value: 'h5'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('H6', 'vaultrix'),
  value: 'h6'
}];
const prjdpColors = [{
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary', 'vaultrix'),
  color: '#001e4e'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Secondary Black', 'vaultrix'),
  color: '#1F242B'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Light Black', 'vaultrix'),
  color: '#07101d'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Black', 'vaultrix'),
  color: '#001e4e'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Red', 'vaultrix'),
  color: '#DD262C'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('White', 'vaultrix'),
  color: '#ffffff'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Light Gray', 'vaultrix'),
  color: 'rgba(255, 255, 255, 0.60)'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Gray', 'vaultrix'),
  color: 'rgba(255, 255, 255, 0.80)'
}, {
  name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Transparent', 'vaultrix'),
  color: 'transparent'
}];
const prjdpBtnStyles = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Button', 'vaultrix'),
  value: 'btn-main'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Transparent', 'vaultrix'),
  value: 'btn-main btn-transparent'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Arrow-Before', 'vaultrix'),
  value: 'btn-main btn-arrow-before'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Transparent Arrow-Before', 'vaultrix'),
  value: 'btn-main btn-transparent btn-arrow-before'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Arrow-After', 'vaultrix'),
  value: 'btn-main btn-arrow-after'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Transparent Arrow-After', 'vaultrix'),
  value: 'btn-main btn-transparent btn-arrow-after'
}];

/***/ },

/***/ "./src/js/blocks/icons.js"
/*!********************************!*\
  !*** ./src/js/blocks/icons.js ***!
  \********************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   bottomAlign: () => (/* binding */ bottomAlign),
/* harmony export */   centerAlign: () => (/* binding */ centerAlign),
/* harmony export */   clickHere: () => (/* binding */ clickHere),
/* harmony export */   iconBan: () => (/* binding */ iconBan),
/* harmony export */   infrastructureIcon: () => (/* binding */ infrastructureIcon),
/* harmony export */   leftAlign: () => (/* binding */ leftAlign),
/* harmony export */   middleAlign: () => (/* binding */ middleAlign),
/* harmony export */   multipleBlockIcon: () => (/* binding */ multipleBlockIcon),
/* harmony export */   rightAlign: () => (/* binding */ rightAlign),
/* harmony export */   topAlign: () => (/* binding */ topAlign)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const multipleBlockIcon = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20px",
  height: "20px",
  viewBox: "0 0 612 612"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  className: "ant-block-icon",
  d: "M1.659,484.737L1.001,206.595c-0.032-13.686,13.95-22.938,26.534-17.559l253.206,108.241c6.997,2.991,11.542,9.859,11.56,17.468l0.658,278.142c0.032,13.687-13.95,22.939-26.534,17.56L13.219,502.206C6.222,499.215,1.676,492.347,1.659,484.737z M581.805,219.687L348.142,320.883l0.608,257.406l233.664-101.196L581.805,219.687 M591.26,186.131c10.043-0.025,19.056,8.054,19.081,19.022l0.658,278.142c0.018,7.609-4.495,14.5-11.478,17.523l-252.69,109.438c-2.493,1.079-5.047,1.583-7.534,1.59c-10.044,0.023-19.058-8.055-19.083-19.022l-0.658-278.143c-0.019-7.609,4.495-14.5,11.479-17.523l252.69-109.437C586.218,186.64,588.771,186.137,591.26,186.131L591.26,186.131z M304.152,29.466L61.767,137.691l242.894,107.075l242.386-108.224L304.152,29.466 M304.083,0c2.632-0.006,5.266,0.533,7.728,1.618l266.403,117.439c15.112,6.663,15.163,28.088,0.082,34.821L312.451,272.577c-2.456,1.097-5.088,1.648-7.721,1.655c-2.632,0.006-5.266-0.533-7.728-1.618L30.6,155.175c-15.113-6.662-15.163-28.088-0.083-34.821L296.361,1.655C298.818,0.558,301.449,0.006,304.083,0L304.083,0z"
})));
const leftAlign = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  width: "21",
  height: "18",
  viewBox: "0 0 21 18",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
  transform: "translate(-29 -4) translate(29 4)",
  fill: "none"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M1 .708v15.851",
  className: "inspector-svg-stroke",
  strokeLinecap: "square"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("rect", {
  className: "inspector-svg-fill",
  x: "5",
  y: "5",
  width: "16",
  height: "7",
  rx: "1"
})));
const centerAlign = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  width: "16",
  height: "18",
  viewBox: "0 0 16 18",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
  transform: "translate(-115 -4) translate(115 4)",
  fill: "none"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M8 .708v15.851",
  className: "inspector-svg-stroke",
  strokeLinecap: "square"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("rect", {
  className: "inspector-svg-fill",
  y: "5",
  width: "16",
  height: "7",
  rx: "1"
})));
const rightAlign = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  width: "21",
  height: "18",
  viewBox: "0 0 21 18",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
  transform: "translate(0 1) rotate(-180 10.5 8.5)",
  fill: "none"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M1 .708v15.851",
  className: "inspector-svg-stroke",
  strokeLinecap: "square"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("rect", {
  className: "inspector-svg-fill",
  fillRule: "nonzero",
  x: "5",
  y: "5",
  width: "16",
  height: "7",
  rx: "1"
})));
const topAlign = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  className: "inspector-svg-fill",
  d: "M9 20h6V9H9v11zM4 4v1.5h16V4H4z"
}));
const middleAlign = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  className: "inspector-svg-fill",
  d: "M20 11h-5V4H9v7H4v1.5h5V20h6v-7.5h5z"
}));
const bottomAlign = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  className: "inspector-svg-fill",
  d: "M15 4H9v11h6V4zM4 18.5V20h16v-1.5H4z"
}));
const iconBan = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  version: "1.0",
  xmlns: "http://www.w3.org/2000/svg",
  width: "15",
  height: "15",
  viewBox: "0 0 1280.000000 1280.000000"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
  transform: "translate(0.000000,1280.000000) scale(0.100000,-0.100000)",
  fill: "#000000",
  stroke: "none"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M6035 12789 c-1890 -112 -3621 -1041 -4757 -2554 -1087 -1447 -1509 -3313 -1153 -5090 405 -2014 1749 -3711 3615 -4565 1872 -856 4051 -757 5837 266 1606 920 2734 2485 3098 4299 303 1511 46 3094 -721 4432 -971 1696 -2665 2858 -4589 3148 -444 67 -908 89 -1330 64z m815 -1355 c785 -76 1522 -319 2184 -720 129 -78 328 -213 405 -274 l32 -25 -3544 -3543 -3544 -3544 -65 89 c-480 657 -800 1447 -922 2278 -49 331 -65 816 -37 1110 63 670 227 1256 516 1840 494 999 1281 1786 2280 2280 648 320 1319 493 2050 529 130 6 489 -5 645 -20z m3697 -2143 c438 -622 741 -1396 857 -2186 49 -331 65 -816 37 -1110 -63 -670 -227 -1256 -516 -1840 -643 -1303 -1793 -2245 -3215 -2636 -659 -181 -1365 -221 -2050 -118 -812 123 -1596 443 -2243 917 l-89 65 3544 3544 3543 3544 25 -32 c14 -17 62 -83 107 -148z"
})));
const clickHere = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  version: "1.0",
  xmlns: "http://www.w3.org/2000/svg",
  width: "305.000000pt",
  height: "144.000000pt",
  viewBox: "0 0 305.000000 144.000000",
  preserveAspectRatio: "xMidYMid meet"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
  transform: "translate(0.000000,144.000000) scale(0.100000,-0.100000)",
  fill: "currentColor",
  stroke: "none"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M1251 1097 c-5 -12 -15 -58 -22 -102 -7 -44 -19 -90 -26 -102 -8 -12 -12 -30 -9 -41 5 -15 -5 -22 -55 -40 -47 -17 -65 -19 -75 -11 -23 19 -16 47 21 75 40 30 45 54 12 54 -52 0 -108 -98 -86 -152 15 -36 48 -42 111 -18 63 24 68 24 68 6 0 -18 30 -29 35 -13 2 6 6 24 9 40 4 15 11 27 16 27 6 0 38 -14 71 -31 48 -24 64 -28 75 -18 21 17 9 28 -74 66 -87 39 -86 50 11 77 55 16 71 31 44 42 -8 3 -35 -2 -61 -11 -26 -9 -50 -14 -53 -11 -3 2 0 28 6 56 14 64 14 130 1 130 -6 0 -14 -10 -19 -23z"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M594 1086 c-72 -32 -154 -130 -169 -202 -9 -45 16 -99 57 -120 72 -39 183 -15 281 60 l47 36 0 -49 c0 -30 5 -53 14 -60 23 -19 36 17 36 96 0 37 7 103 15 145 14 74 12 108 -5 108 -17 0 -30 -30 -41 -98 -12 -72 -12 -73 -73 -121 -84 -66 -116 -81 -179 -81 -44 0 -59 5 -81 25 -31 29 -32 44 -6 95 24 47 66 81 144 115 42 19 57 30 54 43 -4 22 -53 26 -94 8z"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M1674 1054 c-17 -41 -54 -228 -54 -275 0 -15 6 -29 14 -32 16 -6 32 10 67 68 l24 40 7 -43 c9 -55 14 -62 44 -62 27 0 29 3 12 33 -6 12 -14 51 -17 87 -5 56 -9 65 -26 65 -13 0 -28 -15 -45 -45 -34 -62 -38 -33 -10 87 16 68 19 98 11 106 -7 7 -15 -1 -27 -29z"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M953 1023 c-23 -9 -15 -35 10 -31 12 2 22 9 22 17 0 16 -14 22 -32 14z"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M937 933 c-12 -21 -30 -174 -21 -183 18 -18 35 7 44 66 14 86 13 124 -4 124 -8 0 -17 -3 -19 -7z"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M1873 930 c-29 -12 -43 -40 -43 -87 0 -51 44 -97 93 -97 39 0 91 34 117 77 l18 30 7 -46 c11 -74 29 -78 60 -13 15 31 45 72 71 94 24 22 44 42 44 46 0 3 -12 6 -27 6 -18 0 -38 -13 -64 -40 l-39 -40 -6 25 c-8 31 -30 33 -38 3 -9 -30 -104 -108 -132 -108 -12 0 -27 6 -33 14 -10 12 -7 18 18 30 35 19 46 47 30 78 -20 38 -36 44 -76 28z m43 -45 c7 -18 -12 -45 -32 -45 -14 0 -20 41 -7 53 11 12 33 7 39 -8z"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M2293 930 c-29 -12 -43 -40 -43 -87 0 -52 44 -97 94 -97 40 0 99 41 121 85 21 39 12 37 -42 -11 -48 -42 -82 -50 -102 -26 -10 12 -7 18 18 30 35 19 46 47 30 78 -20 38 -36 44 -76 28z m43 -45 c7 -18 -12 -45 -32 -45 -14 0 -20 41 -7 53 11 12 33 7 39 -8z"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M2088 609 c-14 -6 -29 -16 -32 -24 -10 -27 21 -42 101 -49 91 -7 142 -19 126 -29 -18 -11 -250 -64 -343 -79 -113 -17 -526 -17 -640 0 -141 22 -307 61 -454 108 -141 45 -184 47 -131 6 60 -47 310 -124 530 -163 301 -53 768 -28 1028 54 42 14 77 23 77 21 0 -14 -355 -204 -381 -204 -23 0 -31 -26 -13 -41 22 -18 70 -6 177 44 123 58 357 196 364 215 10 24 -10 45 -71 75 -104 51 -284 86 -338 66z"
})));
const infrastructureIcon = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  version: "1.0",
  xmlns: "http://www.w3.org/2000/svg",
  width: "512.000000pt",
  height: "512.000000pt",
  viewBox: "0 0 512.000000 512.000000",
  className: "ant-block-icon"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
  transform: "translate(0.000000,512.000000) scale(0.100000,-0.100000)",
  stroke: "none"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M0 2560 l0 -2560 2560 0 2560 0 0 2560 0 2560 -2560 0 -2560 0 0 -2560z m4800 1600 l0 -640 -2240 0 -2240 0 0 640 0 640 2240 0 2240 0 0 -640z m-3200 -1600 l0 -640 -640 0 -640 0 0 640 0 640 640 0 640 0 0 -640z m1600 0 l0 -640 -640 0 -640 0 0 640 0 640 640 0 640 0 0 -640z m1600 0 l0 -640 -640 0 -640 0 0 640 0 640 640 0 640 0 0 -640z m-3200 -1600 l0 -640 -640 0 -640 0 0 640 0 640 640 0 640 0 0 -640z m1600 0 l0 -640 -640 0 -640 0 0 640 0 640 640 0 640 0 0 -640z m1600 0 l0 -640 -640 0 -640 0 0 640 0 640 640 0 640 0 0 -640z"
})));

/***/ },

/***/ "./src/js/blocks/membership-form/edit.js"
/*!***********************************************!*\
  !*** ./src/js/blocks/membership-form/edit.js ***!
  \***********************************************/
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
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _common__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../common */ "./src/js/blocks/common.js");
/* harmony import */ var _icons__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../icons */ "./src/js/blocks/icons.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_6__);

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

function Edit({
  attributes: {
    blockID,
    bgImage,
    rightImage,
    heading,
    description,
    bgColor,
    headingColor,
    descriptionColor,
    button,
    btnStyle,
    contentAlignment,
    showheading,
    showdesc
  },
  setAttributes,
  clientId,
  className
}) {
  setAttributes({
    blockID: `membership-form-block-unique-${clientId}`
  });
  const headingStyle = {};
  headingColor && (headingStyle.color = headingColor);
  const descStyle = {};
  descriptionColor && (descStyle.color = descriptionColor);
  const blockStyle = {};
  bgColor && (blockStyle.backgroundColor = bgColor);
  bgImage && bgImage.url && (blockStyle.backgroundImage = `url(${bgImage.url})`);
  const classes = classnames__WEBPACK_IMPORTED_MODULE_6___default()(className, 'membership-form', contentAlignment);
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)({
    className: classes,
    id: blockID
  });
  const btnClasses = classnames__WEBPACK_IMPORTED_MODULE_6___default()(btnStyle);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "prj-dp-block-sidebar"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Settings', 'vaultrix')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show Heading', 'vaultrix'),
    checked: showheading,
    onChange: value => setAttributes({
      showheading: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show Description', 'vaultrix'),
    checked: showdesc,
    onChange: value => setAttributes({
      showdesc: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select Button Style', 'vaultrix'),
    value: btnStyle,
    options: _common__WEBPACK_IMPORTED_MODULE_4__.prjdpBtnStyles,
    onChange: value => setAttributes({
      btnStyle: value
    })
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Content Alignment', 'vaultrix'),
    initialOpen: false
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "inspector-field-alignment inspector-field inspector-responsive"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "inspector-field-button-list inspector-field-button-list-fluid"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: `inspector-button ${'left' === contentAlignment ? 'active' : ''}`,
    onClick: () => setAttributes({
      contentAlignment: 'left'
    })
  }, _icons__WEBPACK_IMPORTED_MODULE_5__.leftAlign), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: `inspector-button ${'center' === contentAlignment ? 'active' : ''}`,
    onClick: () => setAttributes({
      contentAlignment: 'center'
    })
  }, _icons__WEBPACK_IMPORTED_MODULE_5__.centerAlign), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: `inspector-button ${'right' === contentAlignment ? 'active' : ''}`,
    onClick: () => setAttributes({
      contentAlignment: 'right'
    })
  }, _icons__WEBPACK_IMPORTED_MODULE_5__.rightAlign)))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Background Settings', 'vaultrix'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "setting-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    htmlFor: "bgImage"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Background Image', 'vaultrix')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, !bgImage.url ? (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
    allowedTypes: ['image'],
    onSelect: image => {
      const newImage = {};
      newImage.id = image.id;
      newImage.url = image.url;
      newImage.alt = image.alt;
      newImage.width = image.width;
      newImage.height = image.height;
      setAttributes({
        bgImage: newImage
      });
    },
    value: bgImage.id,
    render: ({
      open
    }) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      className: "button",
      onClick: open
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select Image', 'vaultrix'))
  })) : (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "image-preview"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: bgImage.url,
    alt: "Preview"
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Replace image', 'vaultrix'),
    value: bgImage.id,
    onSelect: image => {
      const newImage = {};
      newImage.id = image.id;
      newImage.url = image.url;
      newImage.alt = image.alt;
      newImage.width = image.width;
      newImage.height = image.height;
      setAttributes({
        bgImage: newImage
      });
    },
    allowedTypes: ['image'],
    render: ({
      open
    }) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      className: "button",
      onClick: open
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Replace image', 'vaultrix'))
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
    className: "is-link is-destructive",
    onClick: () => {
      setAttributes({
        bgImage: ''
      });
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Remove image', 'vaultrix'))))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Color Settings', 'vaultrix'),
    initialOpen: false
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.PanelColorSettings, {
    colorSettings: [{
      value: headingColor,
      onChange: value => {
        setAttributes({
          headingColor: value
        });
      },
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Heading Color', 'vaultrix')
    }, {
      value: descriptionColor,
      onChange: value => {
        setAttributes({
          descriptionColor: value
        });
      },
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Description Color', 'vaultrix')
    }, {
      value: bgColor,
      onChange: value => {
        setAttributes({
          bgColor: value
        });
      },
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Background Color', 'vaultrix')
    }],
    colors: _common__WEBPACK_IMPORTED_MODULE_4__.prjdpColors
  })))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...blockProps
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "container",
    style: blockStyle
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "membership-form__left-section"
  }, showheading && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.RichText, {
    tagName: "h3",
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enter Heading', 'vaultrix'),
    onChange: value => {
      setAttributes({
        heading: value
      });
    },
    value: heading,
    className: "membership-form__heading",
    style: headingStyle
  }), showdesc && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.RichText, {
    tagName: "p",
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enter Description', 'vaultrix'),
    onChange: value => {
      setAttributes({
        description: value
      });
    },
    value: description,
    className: "membership-form__description",
    style: descStyle
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.RichText, {
    className: btnClasses,
    tagName: "p",
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enter Button Text', 'vaultrix'),
    value: button,
    onChange: value => {
      setAttributes({
        button: value
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "membership-form__right-section"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "image-preview image-controle-visible-hover"
  }, !rightImage.url ? (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
    allowedTypes: ['image'],
    onSelect: image => {
      const newImage = {};
      newImage.id = image.id;
      newImage.url = image.url;
      newImage.alt = image.alt;
      newImage.width = image.width;
      newImage.height = image.height;
      setAttributes({
        rightImage: newImage
      });
    },
    value: rightImage.id,
    render: ({
      open
    }) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "upload-wrap"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
      text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Upload Image of size 310 x 385px', 'vaultrix')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      onClick: open,
      className: "button media_and_text__upload_btn"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
      className: "dashicons dashicons-upload upload-image"
    }))))
  })) : (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "image-preview"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: rightImage.url,
    width: rightImage.width,
    height: rightImage.height,
    alt: rightImage.alt
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "image-controls small-icons icon-center-fixed"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Replace image', 'vaultrix'),
    value: rightImage.id,
    onSelect: image => {
      const newImage = {};
      newImage.id = image.id;
      newImage.url = image.url;
      newImage.alt = image.alt;
      newImage.width = image.width;
      newImage.height = image.height;
      setAttributes({
        rightImage: newImage
      });
    },
    allowedTypes: ['image'],
    render: ({
      open
    }) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
      text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Edit Image', 'vaultrix')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
      className: "dashicons dashicons-edit edit-item",
      onClick: open,
      "aria-hidden": "true"
    }))
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Remove Image', 'vaultrix')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", {
    className: "dashicons dashicons-no-alt remove-image",
    onClick: () => {
      setAttributes({
        rightImage: ''
      });
    },
    "aria-hidden": "true"
  })))))))))));
}

/***/ },

/***/ "./src/js/blocks/membership-form/index.js"
/*!************************************************!*\
  !*** ./src/js/blocks/membership-form/index.js ***!
  \************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./src/js/blocks/membership-form/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./save */ "./src/js/blocks/membership-form/save.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./src/js/blocks/membership-form/block.json");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./style.scss */ "./src/js/blocks/membership-form/style.scss");
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
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  /**
   * @see ./save.js
   */
  save: _save__WEBPACK_IMPORTED_MODULE_2__["default"]
});

/***/ },

/***/ "./src/js/blocks/membership-form/save.js"
/*!***********************************************!*\
  !*** ./src/js/blocks/membership-form/save.js ***!
  \***********************************************/
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
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */


// eslint-disable-next-line import/no-extraneous-dependencies

function save(props) {
  const {
    attributes,
    className
  } = props;
  const {
    blockID,
    bgImage,
    rightImage,
    heading,
    description,
    bgColor,
    headingColor,
    descriptionColor,
    button,
    btnStyle,
    contentAlignment,
    showheading,
    showdesc
  } = attributes;
  const classes = classnames__WEBPACK_IMPORTED_MODULE_2___default()(className, 'membership-form', contentAlignment);
  const headingStyle = {};
  headingColor && (headingStyle.color = headingColor);
  const descStyle = {};
  descriptionColor && (descStyle.color = descriptionColor);
  const btnClasses = classnames__WEBPACK_IMPORTED_MODULE_2___default()(btnStyle);
  const blockStyle = {};
  bgColor && (blockStyle.backgroundColor = bgColor);
  bgImage && bgImage.url && (blockStyle.backgroundImage = `url(${bgImage.url})`);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ..._wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps.save({
      className: classes,
      id: blockID
    })
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "container",
    style: blockStyle
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "membership-form__left-section"
  }, showheading && heading && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.RichText.Content, {
    tagName: "h3",
    value: heading,
    style: headingStyle,
    className: "membership-form__heading"
  }), showdesc && description && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.RichText.Content, {
    className: "membership-form__description",
    tagName: "p",
    value: description,
    style: descStyle
  }), button && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.RichText.Content, {
    tagName: "p",
    className: btnClasses,
    value: button
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "membership-form__right-section"
  }, rightImage.url && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: rightImage.url,
    width: rightImage.width,
    height: rightImage.height,
    alt: rightImage.alt
  }))));
}

/***/ },

/***/ "./src/js/blocks/membership-form/style.scss"
/*!**************************************************!*\
  !*** ./src/js/blocks/membership-form/style.scss ***!
  \**************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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

/***/ "./src/js/blocks/membership-form/block.json"
/*!**************************************************!*\
  !*** ./src/js/blocks/membership-form/block.json ***!
  \**************************************************/
(module) {

"use strict";
module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","name":"vaultrix/membership-form","version":"0.1.0","title":"CTA Section","category":"vaultrix","icon":"welcome-write-blog dp-block-icon","description":"This is a CTA Section static block, which is used to display the subscribe button with editable element settings.","keywords":["CTA","Section","deep pockets"],"supports":{"html":false},"textdomain":"vaultrix","attributes":{"blockID":{"type":"string","default":""},"bgImage":{"type":"object","default":{"id":"","url":"","alt":"","width":"","height":""}},"rightImage":{"type":"object","default":{"id":"","url":"","alt":"","width":"","height":""}},"heading":{"type":"string","default":""},"description":{"type":"string","default":""},"bgColor":{"type":"string","default":""},"headingColor":{"type":"string","default":""},"descriptionColor":{"type":"string","default":""},"btnStyle":{"type":"string","default":"btn-main"},"button":{"type":"string","default":""},"contentAlignment":{"type":"string","default":"left"},"showheading":{"type":"boolean","default":true},"showdesc":{"type":"boolean","default":true}},"editorScript":"file:./index.js","style":"file:./style-index.css"}');

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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		const deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			let notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				let [chunkIds, fn, priority] = deferred[i];
/******/ 				let fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					const r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
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
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		const installedChunks = {
/******/ 			"js/blocks/membership-form/index": 0,
/******/ 			"js/blocks/membership-form/style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		const webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			let [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		const chunkLoadingGlobal = globalThis["webpackChunkvaultrix"] ||= [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	let __webpack_exports__ = __webpack_require__.O(undefined, ["js/blocks/membership-form/style-index"], () => (__webpack_require__("./src/js/blocks/membership-form/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map