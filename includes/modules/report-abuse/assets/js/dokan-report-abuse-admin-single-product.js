/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 105);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),

/***/ 105:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

__webpack_require__(106);

$('.dokan-report-abuse-admin-single-product-delete-item').on('click', function (e) {
    e.preventDefault();

    var button = $(this);
    var id = button.data('id');
    var tr = button.parents('tr');
    var fieldset = button.parents('fieldset');

    if (!confirm('Are you sure you want to delete this report')) {
        return;
    }

    fieldset.prop('disabled', true);

    button.html('<i class="fa fa-refresh fa-spin"></i> ' + dokanReportAbuse.i18n.deleting + '...');

    $.ajax({
        url: dokanReportAbuse.rest.root + 'dokan/v1/abuse-reports/' + id,
        method: 'post',
        beforeSend: function beforeSend(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', dokanReportAbuse.rest.nonce);
            xhr.setRequestHeader('X-HTTP-Method-Override', 'DELETE');
        }
    }).done(function (response) {
        tr.remove();
        alert(dokanReportAbuse.i18n.deletedSuccessfully);
    }).fail(function (jqXHR) {
        button.html('<i class="fa fa-trash"></i> ' + dokanReportAbuse.i18n.delete);

        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
            alert(jqXHR.responseJSON.message);
        }
    }).always(function () {
        fieldset.prop('disabled', false);
    });
});
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 106:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });