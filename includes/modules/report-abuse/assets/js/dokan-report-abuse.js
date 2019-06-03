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
/******/ 	return __webpack_require__(__webpack_require__.s = 99);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),

/***/ 99:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

dokan.reportAbuse = {
    button: null,
    form_html: '',
    flashMessage: '',

    init: function init() {
        var self = this;

        $('.dokan-report-abuse-button').on('click', function (e) {
            e.preventDefault();

            self.button = this;

            if (dokanReportAbuse.reported_by_logged_in_users_only === 'on' && !dokanReportAbuse.is_user_logged_in) {
                return $('body').trigger('dokan:login_form_popup:show');
            }

            self.getForm();
        });

        $('body').on('dokan:login_form_popup:fetching_form', function () {
            self.showLoadingAnim();
        });

        $('body').on('dokan:login_form_popup:fetched_form', function () {
            self.stopLoadingAnim();
        });

        $('body').on('dokan:login_form_popup:logged_in', function (e, response) {
            dokanReportAbuse.is_user_logged_in = true;
            dokanReportAbuse.nonce = response.data.dokan_report_abuse_nonce;
            self.getForm();
        });

        $('body').on('submit', '#dokan-report-abuse-form-popup form', function (e) {
            e.preventDefault();
            self.submitForm(this);
        });
    },
    showLoadingAnim: function showLoadingAnim() {
        $(this.button).addClass('working').children('i').removeClass('fa-flag').addClass('fa-spin fa-refresh');
    },
    stopLoadingAnim: function stopLoadingAnim() {
        $(this.button).removeClass('working').children('i').removeClass('fa-spin fa-refresh').addClass('fa-flag');
    },
    submittingForm: function submittingForm() {
        $('#dokan-report-abuse-form-popup fieldset').prop('disabled', true);
        $('#dokan-report-abuse-form-submit-btn').addClass('dokan-hide');
        $('#dokan-report-abuse-form-working-btn').removeClass('dokan-hide');
    },
    submittedForm: function submittedForm() {
        $('#dokan-report-abuse-form-popup fieldset').prop('disabled', false);
        $('#dokan-report-abuse-form-submit-btn').removeClass('dokan-hide');
        $('#dokan-report-abuse-form-working-btn').addClass('dokan-hide');
    },
    getForm: function getForm() {
        var self = this;

        if (self.form_html) {
            self.showPopup();
            return;
        }

        self.showLoadingAnim();

        $.ajax({
            url: dokan.ajaxurl,
            method: 'get',
            dataType: 'json',
            data: {
                _wpnonce: dokanReportAbuse.nonce,
                action: 'dokan_report_abuse_get_form'
            }
        }).done(function (response) {
            self.form_html = response.data;
            self.showPopup();
        }).always(function () {
            self.stopLoadingAnim();
        });
    },
    showPopup: function showPopup() {
        var self = this;

        $.magnificPopup.open({
            items: {
                src: self.form_html,
                type: 'inline'
            },

            callbacks: {
                afterClose: function afterClose() {
                    self.afterPopupClose();
                }
            }
        });
    },
    afterPopupClose: function afterPopupClose() {
        if (this.flashMessage) {
            alert(this.flashMessage);
            this.flashMessage = '';
        }
    },
    submitForm: function submitForm(form) {
        var self = this;

        var form_data = $(form).serialize();
        var error_section = $('.dokan-popup-error', '#dokan-report-abuse-form-popup');

        error_section.removeClass('has-error').text('');

        self.submittingForm();

        $.ajax({
            url: dokan.ajaxurl,
            method: 'post',
            dataType: 'json',
            data: {
                _wpnonce: dokanReportAbuse.nonce,
                action: 'dokan_report_abuse_submit_form',
                form_data: {
                    reason: $(form).find('[name="reason"]:checked').val(),
                    product_id: dokanReportAbuse.product_id,
                    customer_name: $(form).find('[name="customer_name"]').val(),
                    customer_email: $(form).find('[name="customer_email"]').val(),
                    description: $(form).find('[name="description"]').val()
                }
            }
        }).done(function (response) {
            self.flashMessage = response.data.message;
            $.magnificPopup.close();
        }).always(function () {
            self.submittedForm();
        }).fail(function (jqXHR) {
            if (jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message) {
                error_section.addClass('has-error').text(jqXHR.responseJSON.data.message);
            }
        });
    }
};

dokan.reportAbuse.init();
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ })

/******/ });