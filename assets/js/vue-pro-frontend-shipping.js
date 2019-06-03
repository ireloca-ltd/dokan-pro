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
/******/ 	return __webpack_require__(__webpack_require__.s = 32);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 1 */,
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["a"] = ({

  name: 'shipping',

  data: function data() {
    return {};
  }
});

/***/ }),
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'Main',

    data: function data() {
        return {
            shippingZone: {}
        };
    },


    methods: {
        getShippingMethod: function getShippingMethod(methods) {
            var shippingMethods = [];

            Object.keys(methods).forEach(function (method) {
                var className = methods[method].enabled == 'yes' ? 'is-enabled' : 'is-disabled';
                var formattedMethod = '<span class="' + className + '">' + methods[method].title + '</span>';

                shippingMethods.push(formattedMethod);
            });

            return shippingMethods.join(', ');
        },
        fetchShippingZone: function fetchShippingZone() {
            var self = this,
                data = {
                action: 'dokan-get-shipping-zone',
                nonce: dokan.nonce
            };
            jQuery('#dokan-shipping-zone').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.shippingZone = resp.data;
                    jQuery('#dokan-shipping-zone').unblock();
                } else {
                    jQuery('#dokan-shipping-zone').unblock();
                    alert(resp.data);
                }
            });
        }
    },

    created: function created() {
        this.fetchShippingZone();
    }
});

/***/ }),
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

var Switches = dokan_get_lib('Switches');
var Modal = dokan_get_lib('Modal');
var Multiselect = dokan_get_lib('Multiselect');

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'Zone',

    components: {
        Switches: Switches,
        Modal: Modal,
        Multiselect: Multiselect
    },

    data: function data() {
        return {
            successMessage: '',
            showAddShippingMethodModal: false,
            editShippingMethodModal: false,
            wantToLimitLocation: false,

            zone: {
                data: {
                    zone_name: ''
                },
                formatted_zone_location: ''
            },

            state: [],
            country: [],
            postcode: [],

            showCountryList: false,
            showStateList: false,
            showPostCodeList: false,

            stateList: [],
            countryList: [],

            zoneShippingMethod: {},
            shipping_method: '',
            editShippingMethodData: {
                method_id: '',
                instance_id: '0',
                settings: {}
            },
            cost_description: this.__('Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>. Use <code>[qty]</code> for the number of items, <code>[cost]</code> for the total cost of items, and <code>[fee percent=\'10\' min_fee=\'20\' max_fee=\'\']</code> for percentage based fees.', 'dokan'),
            editShippingMethodTitle: this.__('Edit Shipping Method', 'dokan')
        };
    },


    computed: {
        zoneLocation: function zoneLocation() {
            return _.groupBy(this.zone.data.zone_locations, 'type');
        },
        showLimitLocationLink: function showLimitLocationLink() {
            return this.zoneLocation.postcode === undefined;
        },
        shippingClasses: function shippingClasses() {
            return dokanShipping.shipping_class;
        }
    },

    methods: {
        wantToSetLocation: function wantToSetLocation() {
            this.wantToLimitLocation = !this.wantToLimitLocation;
        },
        onSwitch: function onSwitch(checked, value) {
            var self = this,
                data = {
                action: 'dokan-toggle-shipping-method-enabled',
                zoneID: self.$route.params.zoneID,
                instance_id: value,
                checked: checked,
                nonce: dokan.nonce
            };

            jQuery('.zone-method-wrapper').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.successMessage = resp.data;
                    jQuery('.zone-method-wrapper').unblock();
                    setTimeout(function () {
                        self.successMessage = '';
                    }, 2000);
                } else {
                    jQuery('.zone-method-wrapper').unblock();
                    alert(resp.data);
                }
            });
        },
        saveZoneSettings: function saveZoneSettings() {
            var self = this,
                data = {
                action: 'dokan-save-zone-settings',
                country: self.wantToLimitLocation ? self.country : [],
                state: self.wantToLimitLocation ? self.state : [],
                postcode: self.wantToLimitLocation ? self.postcode : '',
                zoneID: self.$route.params.zoneID,
                nonce: dokan.nonce
            };

            jQuery('#dokan-shipping-zone').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.successMessage = resp.data;
                    jQuery('#dokan-shipping-zone').unblock();
                } else {
                    jQuery('#dokan-shipping-zone').unblock();
                    alert(resp.data);
                }
            });
        },
        editShippingMethod: function editShippingMethod(method) {
            this.editShippingMethodData = {
                instance_id: method.instance_id,
                method_id: method.id,
                settings: method.settings
            };

            this.editShippingMethodModal = true;
        },
        deleteShippingMethod: function deleteShippingMethod(method) {
            var self = this,
                data = {
                action: 'dokan-delete-shipping-method',
                zoneID: self.$route.params.zoneID,
                instance_id: method.instance_id,
                nonce: dokan.nonce
            };

            jQuery('.zone-method-wrapper').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.fetchZone();
                    self.successMessage = resp.data;
                    jQuery('.zone-method-wrapper').unblock();
                } else {
                    jQuery('.zone-method-wrapper').unblock();
                    alert(resp.data);
                }
            });
        },
        updateShippingMethodSettings: function updateShippingMethodSettings() {
            var self = this,
                data = {
                action: 'dokan-update-shipping-method-settings',
                data: self.editShippingMethodData,
                zoneID: self.$route.params.zoneID,
                nonce: dokan.nonce
            };

            jQuery('.zone-method-wrapper').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.fetchZone();
                    self.editShippingMethodModal = false;
                    jQuery('.zone-method-wrapper').unblock();
                } else {
                    jQuery('.zone-method-wrapper').unblock();
                    alert(resp.data);
                }
            });
        },
        getStatesFromCountry: function getStatesFromCountry(country) {
            var states = [];

            _.each(country, function (code) {

                if (dokanShipping.states[code] === undefined) {
                    return;
                }

                var stateArray = Object.keys(dokanShipping.states[code]).map(function (statecode) {
                    return {
                        code: code + ':' + statecode,
                        name: dokanShipping.states[code][statecode]
                    };
                });

                states = states.concat(states, stateArray);
            });

            return states;
        },
        getCountryFromContinent: function getCountryFromContinent(continent) {
            var country = [];

            _.each(continent, function (code) {
                country = country.concat(dokanShipping.continents[code].countries);
            });

            return country.map(function (val) {
                return {
                    code: val,
                    name: dokanShipping.allowed_countries[val]
                };
            });
        },
        setStateForChosenCountry: function setStateForChosenCountry(value, id) {
            var country = _.pluck(value, 'code');
            this.stateList = this.getStatesFromCountry(country);
        },
        fetchZone: function fetchZone() {
            var self = this,
                data = {
                action: 'dokan-get-shipping-zone',
                zoneID: self.$route.params.zoneID,
                nonce: dokan.nonce
            };

            jQuery('#dokan-shipping-zone').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.zone = resp.data;
                    self.zoneShippingMethod = resp.data.shipping_methods;

                    if (self.zone.locations.length < 1) {
                        self.zone.locations = resp.data.data.zone_locations;
                    }

                    var zoneLocationTypes = Object.keys(self.zoneLocation);
                    if (zoneLocationTypes.indexOf('postcode') < 0) {
                        if (zoneLocationTypes.indexOf('state') >= 0) {
                            self.showPostCodeList = true;
                        } else if (zoneLocationTypes.indexOf('country') >= 0) {
                            self.showStateList = true;
                            self.showPostCodeList = true;
                            var country = _.pluck(self.zoneLocation['country'], 'code');
                            self.stateList = self.getStatesFromCountry(country);
                        } else if (zoneLocationTypes.indexOf('continent') >= 0) {
                            self.showCountryList = true;
                            self.showStateList = true;
                            self.showPostCodeList = true;
                            var continent = _.pluck(self.zoneLocation['continent'], 'code');
                            self.countryList = self.getCountryFromContinent(continent);
                        }
                    }

                    if (resp.data.locations.length > 0) {
                        self.wantToLimitLocation = true;
                        var locationResp = _.groupBy(resp.data.locations, 'type');

                        Object.keys(locationResp).forEach(function (key) {
                            if ('country' == key) {
                                self.country = locationResp[key].map(function (countrydata) {
                                    return {
                                        code: countrydata.code,
                                        name: dokanShipping.allowed_countries[countrydata.code]
                                    };
                                });
                            } else if ('state' == key) {
                                self.state = locationResp[key].map(function (statedata) {
                                    var stateCode = statedata.code.split(':');
                                    return {
                                        code: statedata.code,
                                        name: dokanShipping.states[stateCode[0]][stateCode[1]]
                                    };
                                });
                            } else if ('postcode' == key) {
                                // Render comma string from postcode location array
                                self.postcode = _.pluck(locationResp[key], 'code').join(',');

                                if (self.zoneLocation.postcode !== undefined) {
                                    self.postcode = _.pluck(self.zoneLocation.postcode, 'code').join(',');
                                }
                            }
                        });
                    }

                    jQuery('#dokan-shipping-zone').unblock();
                } else {
                    jQuery('#dokan-shipping-zone').unblock();
                    alert(resp.data);
                }
            });
        },
        addNewMethod: function addNewMethod() {
            var self = this,
                data = {
                action: 'dokan-add-shipping-method',
                zoneID: self.$route.params.zoneID,
                nonce: dokan.nonce,
                method: self.shipping_method
            };

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.fetchZone();
                    self.showAddShippingMethodModal = false;
                    self.successMessage = resp.data;
                } else {
                    alert(resp.data);
                }
            });
        }
    },

    created: function created() {
        this.fetchZone();
    }
});

/***/ }),
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'Settings',

    data: function data() {
        return {
            successMessage: '',
            shippingSettings: {
                shipping_policy: '',
                refund_policy: '',
                processing_time: ''
            }
        };
    },


    methods: {
        getProcessingTimes: function getProcessingTimes() {
            return dokanShipping.processing_time;
        },
        saveSettings: function saveSettings() {
            var self = this,
                data = {
                action: 'dokan-save-shipping-settings',
                settings: self.shippingSettings,
                nonce: dokan.nonce
            };

            jQuery('#shipping-settings').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.successMessage = resp.data;
                    jQuery('#shipping-settings').unblock();
                } else {
                    jQuery('#shipping-settings').unblock();
                    alert(resp.data);
                }
            });
        },
        getSettings: function getSettings() {
            var self = this,
                data = {
                action: 'dokan-get-shipping-settings',
                nonce: dokan.nonce
            };

            jQuery('#shipping-settings').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            jQuery.post(dokan.ajaxurl, data, function (resp) {
                if (resp.success) {
                    self.shippingSettings = resp.data;
                    jQuery('#shipping-settings').unblock();
                } else {
                    jQuery('#shipping-settings').unblock();
                    alert(resp.data);
                }
            });
        }
    },

    created: function created() {
        this.getSettings();
    }
});

/***/ }),
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _shipping = __webpack_require__(33);

var _shipping2 = _interopRequireDefault(_shipping);

var _router = __webpack_require__(36);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Vue = dokan_get_lib('Vue');

new Vue({
    el: '#dokan-vue-shipping',
    router: _router2.default,
    render: function render(h) {
        return h(_shipping2.default);
    },

    created: function created() {
        this.setLocaleData(dokanShipping.i18n['dokan']);
    }
});

/***/ }),
/* 33 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_shipping_vue__ = __webpack_require__(2);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2f887a63_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_shipping_vue__ = __webpack_require__(35);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(34)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-2f887a63"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_shipping_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2f887a63_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_shipping_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/frontend/components/shipping.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2f887a63", Component.options)
  } else {
    hotAPI.reload("data-v-2f887a63", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 34 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 35 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "dokan-shipping-zone" } },
    [_c("router-view")],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-2f887a63", esExports)
  }
}

/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _Main = __webpack_require__(37);

var _Main2 = _interopRequireDefault(_Main);

var _Zone = __webpack_require__(40);

var _Zone2 = _interopRequireDefault(_Zone);

var _Settings = __webpack_require__(43);

var _Settings2 = _interopRequireDefault(_Settings);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Vue = dokan_get_lib('Vue');
var Router = dokan_get_lib('Router');

Vue.use(Router);

var routes = [{ path: '/', component: _Main2.default, name: 'Main' }, { path: '/settings', component: _Settings2.default, name: 'Settings' }, { path: '/zone/:zoneID', component: _Zone2.default, name: 'Zone' }];

exports.default = new Router({
    routes: routes
});

/***/ }),
/* 37 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Main_vue__ = __webpack_require__(3);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7b991f8e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Main_vue__ = __webpack_require__(39);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(38)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Main_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7b991f8e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Main_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/frontend/components/Main.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7b991f8e", Component.options)
  } else {
    hotAPI.reload("data-v-7b991f8e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 38 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 39 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "shipping-zone" }, [
    _c("table", { staticClass: "dokan-table shipping-zone-table" }, [
      _c("thead", [
        _c("tr", [
          _c("th", [_vm._v(_vm._s(_vm.__("Zone Name", "dokan")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Region(s)", "dokan")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Shipping Method", "dokan")))])
        ])
      ]),
      _vm._v(" "),
      _c(
        "tbody",
        [
          Object.keys(_vm.shippingZone).length > 0
            ? _vm._l(_vm.shippingZone, function(zone) {
                return _c("tr", [
                  _c(
                    "td",
                    [
                      _c(
                        "router-link",
                        {
                          attrs: {
                            to: {
                              name: "Zone",
                              params: { zoneID: zone.zone_id }
                            }
                          }
                        },
                        [_vm._v(_vm._s(zone.zone_name))]
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "row-actions" },
                        [
                          _c(
                            "router-link",
                            {
                              attrs: {
                                to: {
                                  name: "Zone",
                                  params: { zoneID: zone.zone_id }
                                }
                              }
                            },
                            [_vm._v(_vm._s(_vm.__("Edit", "dokan")))]
                          )
                        ],
                        1
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c("td", [
                    _vm._v(
                      "\n                        " +
                        _vm._s(zone.formatted_zone_location) +
                        "\n                    "
                    )
                  ]),
                  _vm._v(" "),
                  _c("td", [
                    _vm.getShippingMethod(zone.shipping_methods)
                      ? _c("p", {
                          domProps: {
                            innerHTML: _vm._s(
                              _vm.getShippingMethod(zone.shipping_methods)
                            )
                          }
                        })
                      : _c(
                          "p",
                          [
                            _c("span", [
                              _vm._v(
                                _vm._s(_vm.__("No method found", "dokan")) + " "
                              )
                            ]),
                            _vm._v(" "),
                            _c(
                              "router-link",
                              {
                                attrs: {
                                  to: {
                                    name: "Zone",
                                    params: { zoneID: zone.zone_id }
                                  }
                                }
                              },
                              [
                                _vm._v(
                                  " " +
                                    _vm._s(
                                      _vm.__("Add Shipping Method", "dokan")
                                    )
                                )
                              ]
                            )
                          ],
                          1
                        )
                  ])
                ])
              })
            : [
                _c("tr", [
                  _c("td", { attrs: { colspan: "3" } }, [
                    _vm._v(
                      _vm._s(
                        _vm.__(
                          "No shipping zone found for configuration. Please contact with admin for manage your store shipping",
                          "dokan"
                        )
                      )
                    )
                  ])
                ])
              ]
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-7b991f8e", esExports)
  }
}

/***/ }),
/* 40 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Zone_vue__ = __webpack_require__(4);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a2ade7fe_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Zone_vue__ = __webpack_require__(42);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(41)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Zone_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a2ade7fe_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Zone_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/frontend/components/Zone.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-a2ade7fe", Component.options)
  } else {
    hotAPI.reload("data-v-a2ade7fe", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 41 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 42 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "zone-component" },
    [
      _vm.successMessage
        ? _c("div", { staticClass: "dokan-alert dokan-alert-success" }, [
            _c("span", { domProps: { innerHTML: _vm._s(_vm.successMessage) } }),
            _vm._v(" "),
            _c(
              "a",
              {
                staticClass: "dokan-close",
                attrs: { "data-dismiss": "alert" }
              },
              [_vm._v("×")]
            )
          ])
        : _vm._e(),
      _vm._v(" "),
      _c("form", { attrs: { action: "", method: "post" } }, [
        _c("div", { staticClass: "dokan-form-group dokan-clearfix" }, [
          _c(
            "label",
            {
              staticClass: "dokan-w4 dokan-control-label dokan-text-right",
              attrs: { for: "" }
            },
            [
              _vm._v(
                "\n                " +
                  _vm._s(_vm.__("Zone Name", "dokan")) +
                  " :\n            "
              )
            ]
          ),
          _vm._v(" "),
          _c("div", { staticClass: "dokan-w5 dokan-text-left" }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.zone.data.zone_name) +
                "\n            "
            )
          ])
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "dokan-form-group dokan-clearfix" }, [
          _c(
            "label",
            {
              staticClass: "dokan-w4 dokan-control-label dokan-text-right",
              attrs: { for: "" }
            },
            [
              _vm._v(
                "\n                " +
                  _vm._s(_vm.__("Zone Location", "dokan")) +
                  " :\n            "
              )
            ]
          ),
          _vm._v(" "),
          _c("div", { staticClass: "dokan-w5 dokan-text-left" }, [
            _c("p", [_vm._v(_vm._s(_vm.zone.formatted_zone_location))]),
            _vm._v(" "),
            _vm.showLimitLocationLink && this.$route.params.zoneID != 0
              ? _c(
                  "a",
                  {
                    staticClass: "limit-location-link",
                    attrs: { href: "#" },
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.wantToSetLocation($event)
                      }
                    }
                  },
                  [
                    _c("switches", {
                      attrs: { enabled: _vm.wantToLimitLocation },
                      on: { input: _vm.wantToSetLocation }
                    }),
                    _vm._v(" "),
                    _c("span", [
                      _vm._v(
                        _vm._s(_vm.__("Limit your zone location", "dokan"))
                      )
                    ])
                  ],
                  1
                )
              : _vm._e()
          ])
        ]),
        _vm._v(" "),
        _vm.wantToLimitLocation && _vm.showCountryList
          ? _c("div", { staticClass: "dokan-form-group dokan-clearfix" }, [
              _c(
                "label",
                {
                  staticClass: "dokan-w4 dokan-control-label dokan-text-right"
                },
                [
                  _vm._v(
                    "\n                " +
                      _vm._s(_vm.__("Select Country", "dokan")) +
                      "\n            "
                  )
                ]
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "dokan-w5 dokan-text-left" },
                [
                  _c("multiselect", {
                    attrs: {
                      options: _vm.countryList,
                      placeholder: _vm.__("Select Country", "dokan"),
                      multiple: true,
                      label: "name",
                      "track-by": "code"
                    },
                    on: { input: _vm.setStateForChosenCountry },
                    scopedSlots: _vm._u([
                      {
                        key: "option",
                        fn: function(props) {
                          return [
                            _c("span", {
                              domProps: { innerHTML: _vm._s(props.option.name) }
                            })
                          ]
                        }
                      },
                      {
                        key: "tag",
                        fn: function(props) {
                          return [
                            _c("span", { staticClass: "multiselect__tag" }, [
                              _c("span", {
                                domProps: {
                                  innerHTML: _vm._s(props.option.name)
                                }
                              }),
                              _vm._v(" "),
                              _c("i", {
                                staticClass: "multiselect__tag-icon",
                                attrs: { "aria-hidden": "true", tabindex: "1" },
                                on: {
                                  keydown: function($event) {
                                    if (
                                      !("button" in $event) &&
                                      _vm._k(
                                        $event.keyCode,
                                        "enter",
                                        13,
                                        $event.key,
                                        "Enter"
                                      )
                                    ) {
                                      return null
                                    }
                                    $event.preventDefault()
                                    props.remove(props.option)
                                  },
                                  mousedown: function($event) {
                                    $event.preventDefault()
                                    props.remove(props.option)
                                  }
                                }
                              })
                            ])
                          ]
                        }
                      }
                    ]),
                    model: {
                      value: _vm.country,
                      callback: function($$v) {
                        _vm.country = $$v
                      },
                      expression: "country"
                    }
                  })
                ],
                1
              )
            ])
          : _vm._e(),
        _vm._v(" "),
        _vm.wantToLimitLocation && _vm.showStateList && _vm.stateList.length
          ? _c("div", { staticClass: "dokan-form-group dokan-clearfix" }, [
              _c(
                "label",
                {
                  staticClass: "dokan-w4 dokan-control-label dokan-text-right",
                  attrs: { for: "" }
                },
                [
                  _vm._v(
                    "\n                " +
                      _vm._s(_vm.__("Select States", "dokan")) +
                      "\n            "
                  )
                ]
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "dokan-w5 dokan-text-left" },
                [
                  _c("multiselect", {
                    attrs: {
                      options: _vm.stateList,
                      placeholder: _vm.__("Select States", "dokan"),
                      multiple: true,
                      label: "name",
                      "track-by": "code"
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "option",
                        fn: function(props) {
                          return [
                            _c("span", {
                              domProps: { innerHTML: _vm._s(props.option.name) }
                            })
                          ]
                        }
                      },
                      {
                        key: "tag",
                        fn: function(props) {
                          return [
                            _c("span", { staticClass: "multiselect__tag" }, [
                              _c("span", {
                                domProps: {
                                  innerHTML: _vm._s(props.option.name)
                                }
                              }),
                              _vm._v(" "),
                              _c("i", {
                                staticClass: "multiselect__tag-icon",
                                attrs: { "aria-hidden": "true", tabindex: "1" },
                                on: {
                                  keydown: function($event) {
                                    if (
                                      !("button" in $event) &&
                                      _vm._k(
                                        $event.keyCode,
                                        "enter",
                                        13,
                                        $event.key,
                                        "Enter"
                                      )
                                    ) {
                                      return null
                                    }
                                    $event.preventDefault()
                                    props.remove(props.option)
                                  },
                                  mousedown: function($event) {
                                    $event.preventDefault()
                                    props.remove(props.option)
                                  }
                                }
                              })
                            ])
                          ]
                        }
                      }
                    ]),
                    model: {
                      value: _vm.state,
                      callback: function($$v) {
                        _vm.state = $$v
                      },
                      expression: "state"
                    }
                  })
                ],
                1
              )
            ])
          : _vm._e(),
        _vm._v(" "),
        _vm.wantToLimitLocation && _vm.showPostCodeList
          ? _c("div", { staticClass: "dokan-form-group dokan-clearfix" }, [
              _c(
                "label",
                {
                  staticClass: "dokan-w4 dokan-control-label dokan-text-right",
                  attrs: { for: "" }
                },
                [
                  _vm._v(
                    "\n                " +
                      _vm._s(_vm.__("Set your postcode", "dokan")) +
                      " "
                  ),
                  _c("i", {
                    directives: [{ name: "tooltip", rawName: "v-tooltip" }],
                    staticClass: "fa fa-question-circle",
                    attrs: {
                      title: _vm.__(
                        "Postcodes need to be comma separated",
                        "dokan"
                      )
                    }
                  })
                ]
              ),
              _vm._v(" "),
              _c("div", { staticClass: "dokan-w5 dokan-text-left" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.postcode,
                      expression: "postcode"
                    }
                  ],
                  staticClass: "dokan-form-control",
                  attrs: { name: "zone_postcode", id: "zone_postcode" },
                  domProps: { value: _vm.postcode },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.postcode = $event.target.value
                    }
                  }
                })
              ])
            ])
          : _vm._e(),
        _vm._v(" "),
        _c("div", { staticClass: "dokan-edit-row zone-method-wrapper" }, [
          _c(
            "div",
            {
              staticClass: "dokan-section-heading",
              attrs: { "data-togglehandler": "dokan_product_inventory" }
            },
            [
              _c("h2", [
                _c("i", {
                  staticClass: "fa fa-truck",
                  attrs: { "aria-hidden": "true" }
                }),
                _vm._v(" " + _vm._s(_vm.__("Shipping Method", "dokan")))
              ]),
              _vm._v(" "),
              _c("p", [
                _vm._v(
                  _vm._s(
                    _vm.__(
                      "Add your shipping method for appropiate zone",
                      "dokan"
                    )
                  )
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "dokan-clearfix" })
            ]
          ),
          _vm._v(" "),
          _c("div", { staticClass: "dokan-section-content" }, [
            _c("table", { staticClass: "dokan-table zone-method-table" }, [
              _c("thead", [
                _c("tr", [
                  _c("th", { staticClass: "title" }, [
                    _vm._v(_vm._s(_vm.__("Method Title", "dokan")))
                  ]),
                  _vm._v(" "),
                  _c("th", { staticClass: "enabled" }, [
                    _vm._v(_vm._s(_vm.__("Status", "dokan")))
                  ]),
                  _vm._v(" "),
                  _c("th", { staticClass: "description" }, [
                    _vm._v(_vm._s(_vm.__("Description", "dokan")))
                  ])
                ])
              ]),
              _vm._v(" "),
              _c(
                "tbody",
                [
                  Object.keys(_vm.zoneShippingMethod).length
                    ? _vm._l(_vm.zoneShippingMethod, function(method) {
                        return _c("tr", [
                          _c("td", [
                            _vm._v(
                              "\n                                    " +
                                _vm._s(method.title) +
                                "\n                                    "
                            ),
                            _c("div", { staticClass: "row-actions" }, [
                              _c("span", { staticClass: "edit" }, [
                                _c(
                                  "a",
                                  {
                                    attrs: { href: "#" },
                                    on: {
                                      click: function($event) {
                                        $event.preventDefault()
                                        _vm.editShippingMethod(method)
                                      }
                                    }
                                  },
                                  [_vm._v(_vm._s(_vm.__("Edit", "dokan")))]
                                ),
                                _vm._v(" | ")
                              ]),
                              _vm._v(" "),
                              _c("span", { staticClass: "delete" }, [
                                _c(
                                  "a",
                                  {
                                    attrs: { href: "#" },
                                    on: {
                                      click: function($event) {
                                        $event.preventDefault()
                                        _vm.deleteShippingMethod(method)
                                      }
                                    }
                                  },
                                  [_vm._v(_vm._s(_vm.__("Delete", "dokan")))]
                                )
                              ])
                            ])
                          ]),
                          _vm._v(" "),
                          _c(
                            "td",
                            [
                              _c("switches", {
                                attrs: {
                                  enabled: method.enabled == "yes",
                                  value: method.instance_id
                                },
                                on: { input: _vm.onSwitch }
                              })
                            ],
                            1
                          ),
                          _vm._v(" "),
                          _c("td", [
                            _vm._v(
                              "\n                                    " +
                                _vm._s(method.settings.description) +
                                "\n                                "
                            )
                          ])
                        ])
                      })
                    : [
                        _c("tr", [
                          _c("td", { attrs: { colspan: "3" } }, [
                            _vm._v(
                              "\n                                    " +
                                _vm._s(_vm.__("No method found", "dokan")) +
                                "\n                                "
                            )
                          ])
                        ])
                      ]
                ],
                2
              )
            ])
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "dokan-section-footer" }, [
            _c(
              "a",
              {
                staticClass: "dokan-btn dokan-btn-theme",
                attrs: { href: "#" },
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    _vm.showAddShippingMethodModal = true
                  }
                }
              },
              [
                _c("i", { staticClass: "fa fa-plus" }),
                _vm._v(" " + _vm._s(_vm.__("Add Shipping Method", "dokan")))
              ]
            )
          ])
        ]),
        _vm._v(" "),
        _c(
          "div",
          { staticClass: "dokan-form-group" },
          [
            _c("router-link", { attrs: { to: { name: "Main" } } }, [
              _vm._v(_vm._s(_vm.__("← Back to Zone List", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              staticClass: "dokan-btn dokan-btn-theme dokan-right",
              attrs: { type: "submit" },
              domProps: { value: _vm.__("Save Changes", "dokan") },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.saveZoneSettings($event)
                }
              }
            })
          ],
          1
        ),
        _vm._v(" "),
        _c("div", { staticClass: "dokan-clearfix" })
      ]),
      _vm._v(" "),
      _vm.showAddShippingMethodModal
        ? _c(
            "modal",
            {
              attrs: { title: _vm.__("Add Shipping Method", "dokan") },
              on: {
                close: function($event) {
                  _vm.showAddShippingMethodModal = false
                }
              }
            },
            [
              _c("template", { slot: "body" }, [
                _c("p", [
                  _vm._v(
                    _vm._s(
                      _vm.__(
                        "Choose the shipping method you wish to add. Only shipping methods which support zones are listed.",
                        "dokan"
                      )
                    )
                  )
                ]),
                _vm._v(" "),
                _c(
                  "select",
                  {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.shipping_method,
                        expression: "shipping_method"
                      }
                    ],
                    staticClass: "dokan-form-control",
                    attrs: { model: "shipping_method", id: "shipping_method" },
                    on: {
                      change: function($event) {
                        var $$selectedVal = Array.prototype.filter
                          .call($event.target.options, function(o) {
                            return o.selected
                          })
                          .map(function(o) {
                            var val = "_value" in o ? o._value : o.value
                            return val
                          })
                        _vm.shipping_method = $event.target.multiple
                          ? $$selectedVal
                          : $$selectedVal[0]
                      }
                    }
                  },
                  [
                    _c("option", { attrs: { value: "" } }, [
                      _vm._v(
                        "‐ " + _vm._s(_vm.__("Select a Method", "dokan")) + " ‐"
                      )
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "flat_rate" } }, [
                      _vm._v(_vm._s(_vm.__("Flat Rate", "dokan")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "local_pickup" } }, [
                      _vm._v(_vm._s(_vm.__("Local Pickup", "dokan")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "free_shipping" } }, [
                      _vm._v(_vm._s(_vm.__("Free Shipping", "dokan")))
                    ])
                  ]
                )
              ]),
              _vm._v(" "),
              _c("template", { slot: "footer" }, [
                _c(
                  "button",
                  {
                    staticClass: "button button-primary button-large",
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.addNewMethod($event)
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Add Shipping Method", "dokan")))]
                )
              ])
            ],
            2
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.editShippingMethodModal
        ? _c(
            "modal",
            {
              attrs: { title: _vm.editShippingMethodTitle },
              on: {
                close: function($event) {
                  _vm.editShippingMethodModal = false
                }
              }
            },
            [
              _vm.editShippingMethodData.method_id != "free_shipping"
                ? _c(
                    "template",
                    { slot: "body" },
                    [
                      _c("div", { staticClass: "dokan-form-group" }, [
                        _c("label", { attrs: { for: "method_title" } }, [
                          _vm._v(_vm._s(_vm.__("Title", "dokan")))
                        ]),
                        _vm._v(" "),
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.editShippingMethodData.settings.title,
                              expression:
                                "editShippingMethodData.settings.title"
                            }
                          ],
                          staticClass: "dokan-form-control",
                          attrs: {
                            type: "text",
                            id: "method_title",
                            placeholder: _vm.__("Enter method title", "dokan")
                          },
                          domProps: {
                            value: _vm.editShippingMethodData.settings.title
                          },
                          on: {
                            input: function($event) {
                              if ($event.target.composing) {
                                return
                              }
                              _vm.$set(
                                _vm.editShippingMethodData.settings,
                                "title",
                                $event.target.value
                              )
                            }
                          }
                        })
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "dokan-form-group" }, [
                        _c("label", { attrs: { for: "method_cost" } }, [
                          _vm._v(_vm._s(_vm.__("Cost", "dokan")))
                        ]),
                        _vm._v(" "),
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.editShippingMethodData.settings.cost,
                              expression: "editShippingMethodData.settings.cost"
                            }
                          ],
                          staticClass: "dokan-form-control",
                          attrs: {
                            type: "text",
                            id: "method_cost",
                            placeholder: "0.00"
                          },
                          domProps: {
                            value: _vm.editShippingMethodData.settings.cost
                          },
                          on: {
                            input: function($event) {
                              if ($event.target.composing) {
                                return
                              }
                              _vm.$set(
                                _vm.editShippingMethodData.settings,
                                "cost",
                                $event.target.value
                              )
                            }
                          }
                        }),
                        _vm._v(" "),
                        _vm.editShippingMethodData.method_id == "flat_rate"
                          ? _c("span", {
                              staticClass: "description",
                              domProps: {
                                innerHTML: _vm._s(_vm.cost_description)
                              }
                            })
                          : _vm._e()
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "dokan-form-group" }, [
                        _c("label", { attrs: { for: "method_tax_status" } }, [
                          _vm._v(_vm._s(_vm.__("Tax Status", "dokan")))
                        ]),
                        _vm._v(" "),
                        _c(
                          "select",
                          {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value:
                                  _vm.editShippingMethodData.settings
                                    .tax_status,
                                expression:
                                  "editShippingMethodData.settings.tax_status"
                              }
                            ],
                            staticClass: "dokan-form-control",
                            attrs: { id: "method_tax_status" },
                            on: {
                              change: function($event) {
                                var $$selectedVal = Array.prototype.filter
                                  .call($event.target.options, function(o) {
                                    return o.selected
                                  })
                                  .map(function(o) {
                                    var val = "_value" in o ? o._value : o.value
                                    return val
                                  })
                                _vm.$set(
                                  _vm.editShippingMethodData.settings,
                                  "tax_status",
                                  $event.target.multiple
                                    ? $$selectedVal
                                    : $$selectedVal[0]
                                )
                              }
                            }
                          },
                          [
                            _c("option", { attrs: { value: "none" } }, [
                              _vm._v(_vm._s(_vm.__("None", "dokan")))
                            ]),
                            _vm._v(" "),
                            _c("option", { attrs: { value: "taxable" } }, [
                              _vm._v(_vm._s(_vm.__("Taxable", "dokan")))
                            ])
                          ]
                        )
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "dokan-form-group" }, [
                        _c("label", { attrs: { for: "method_description" } }, [
                          _vm._v(_vm._s(_vm.__("Description", "dokan")))
                        ]),
                        _vm._v(" "),
                        _c(
                          "textarea",
                          {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value:
                                  _vm.editShippingMethodData.settings
                                    .description,
                                expression:
                                  "editShippingMethodData.settings.description"
                              }
                            ],
                            staticClass: "dokan-form-control",
                            attrs: { id: "method_description" },
                            domProps: {
                              value:
                                _vm.editShippingMethodData.settings.description
                            },
                            on: {
                              input: function($event) {
                                if ($event.target.composing) {
                                  return
                                }
                                _vm.$set(
                                  _vm.editShippingMethodData.settings,
                                  "description",
                                  $event.target.value
                                )
                              }
                            }
                          },
                          [
                            _vm._v(
                              _vm._s(
                                _vm.editShippingMethodData.settings.description
                              )
                            )
                          ]
                        )
                      ]),
                      _vm._v(" "),
                      "flat_rate" == _vm.editShippingMethodData.method_id
                        ? [
                            _c("hr"),
                            _vm._v(" "),
                            _c("div", { staticClass: "dokan-form-group" }, [
                              _c("h3", [
                                _vm._v(
                                  _vm._s(_vm.__("Shipping Class Cost", "dokan"))
                                )
                              ]),
                              _vm._v(" "),
                              _c("span", { staticClass: "description" }, [
                                _vm._v(
                                  _vm._s(
                                    _vm.__(
                                      "These costs can optionally be added based on the product shipping class",
                                      "dokan"
                                    )
                                  )
                                )
                              ])
                            ]),
                            _vm._v(" "),
                            _vm._l(_vm.shippingClasses, function(
                              shippingClass
                            ) {
                              return [
                                _c("div", { staticClass: "dokan-form-group" }, [
                                  _c(
                                    "label",
                                    { attrs: { for: shippingClass.slug } },
                                    [
                                      _vm._v(
                                        '"' +
                                          _vm._s(shippingClass.name) +
                                          '" ' +
                                          _vm._s(
                                            _vm.__(
                                              "shipping class cost",
                                              "dokan"
                                            )
                                          )
                                      )
                                    ]
                                  ),
                                  _vm._v(" "),
                                  _c("input", {
                                    directives: [
                                      {
                                        name: "model",
                                        rawName: "v-model",
                                        value:
                                          _vm.editShippingMethodData.settings[
                                            "class_cost_" +
                                              shippingClass.term_id
                                          ],
                                        expression:
                                          "editShippingMethodData.settings['class_cost_' + shippingClass.term_id]"
                                      }
                                    ],
                                    staticClass: "dokan-form-control",
                                    attrs: {
                                      type: "text",
                                      id: shippingClass.slug,
                                      placeholder: "N\\A"
                                    },
                                    domProps: {
                                      value:
                                        _vm.editShippingMethodData.settings[
                                          "class_cost_" + shippingClass.term_id
                                        ]
                                    },
                                    on: {
                                      input: function($event) {
                                        if ($event.target.composing) {
                                          return
                                        }
                                        _vm.$set(
                                          _vm.editShippingMethodData.settings,
                                          "class_cost_" + shippingClass.term_id,
                                          $event.target.value
                                        )
                                      }
                                    }
                                  }),
                                  _vm._v(" "),
                                  _vm.editShippingMethodData.method_id ==
                                  "flat_rate"
                                    ? _c("span", {
                                        staticClass: "description",
                                        domProps: {
                                          innerHTML: _vm._s(
                                            _vm.cost_description
                                          )
                                        }
                                      })
                                    : _vm._e()
                                ])
                              ]
                            }),
                            _vm._v(" "),
                            _c("div", { staticClass: "dokan-form-group" }, [
                              _c("label", { attrs: { for: "no_class_cost" } }, [
                                _vm._v(
                                  _vm._s(
                                    _vm.__("No shipping class cost", "dokan")
                                  )
                                )
                              ]),
                              _vm._v(" "),
                              _c("input", {
                                directives: [
                                  {
                                    name: "model",
                                    rawName: "v-model",
                                    value:
                                      _vm.editShippingMethodData.settings
                                        .no_class_cost,
                                    expression:
                                      "editShippingMethodData.settings.no_class_cost"
                                  }
                                ],
                                staticClass: "dokan-form-control",
                                attrs: {
                                  type: "text",
                                  id: "no_class_cost",
                                  placeholder: "N\\A"
                                },
                                domProps: {
                                  value:
                                    _vm.editShippingMethodData.settings
                                      .no_class_cost
                                },
                                on: {
                                  input: function($event) {
                                    if ($event.target.composing) {
                                      return
                                    }
                                    _vm.$set(
                                      _vm.editShippingMethodData.settings,
                                      "no_class_cost",
                                      $event.target.value
                                    )
                                  }
                                }
                              }),
                              _vm._v(" "),
                              _vm.editShippingMethodData.method_id ==
                              "flat_rate"
                                ? _c("span", {
                                    staticClass: "description",
                                    domProps: {
                                      innerHTML: _vm._s(_vm.cost_description)
                                    }
                                  })
                                : _vm._e()
                            ]),
                            _vm._v(" "),
                            _c("div", { staticClass: "dokan-form-group" }, [
                              _c(
                                "label",
                                { attrs: { for: "calculation_type" } },
                                [
                                  _vm._v(
                                    _vm._s(_vm.__("Calculation type", "dokan"))
                                  )
                                ]
                              ),
                              _vm._v(" "),
                              _c(
                                "select",
                                {
                                  directives: [
                                    {
                                      name: "model",
                                      rawName: "v-model",
                                      value:
                                        _vm.editShippingMethodData.settings
                                          .calculation_type,
                                      expression:
                                        "editShippingMethodData.settings.calculation_type"
                                    }
                                  ],
                                  staticClass: "dokan-form-control",
                                  attrs: { id: "calculation_type" },
                                  on: {
                                    change: function($event) {
                                      var $$selectedVal = Array.prototype.filter
                                        .call($event.target.options, function(
                                          o
                                        ) {
                                          return o.selected
                                        })
                                        .map(function(o) {
                                          var val =
                                            "_value" in o ? o._value : o.value
                                          return val
                                        })
                                      _vm.$set(
                                        _vm.editShippingMethodData.settings,
                                        "calculation_type",
                                        $event.target.multiple
                                          ? $$selectedVal
                                          : $$selectedVal[0]
                                      )
                                    }
                                  }
                                },
                                [
                                  _c("option", { attrs: { value: "class" } }, [
                                    _vm._v(
                                      _vm._s(
                                        _vm.__(
                                          "Per class: Charge shipping for each shipping class individually",
                                          "dokan"
                                        )
                                      )
                                    )
                                  ]),
                                  _vm._v(" "),
                                  _c(
                                    "option",
                                    {
                                      attrs: { value: "order" },
                                      domProps: { selected: true }
                                    },
                                    [
                                      _vm._v(
                                        _vm._s(
                                          _vm.__(
                                            "Per order: Charge shipping for the most expensive shipping class",
                                            "dokan"
                                          )
                                        )
                                      )
                                    ]
                                  )
                                ]
                              )
                            ])
                          ]
                        : _vm._e()
                    ],
                    2
                  )
                : _c("template", { slot: "body" }, [
                    _c("div", { staticClass: "dokan-form-group" }, [
                      _c("label", { attrs: { for: "method_title" } }, [
                        _vm._v(_vm._s(_vm.__("Method Title", "dokan")))
                      ]),
                      _vm._v(" "),
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.editShippingMethodData.settings.title,
                            expression: "editShippingMethodData.settings.title"
                          }
                        ],
                        staticClass: "dokan-form-control",
                        attrs: {
                          type: "text",
                          id: "method_title",
                          placeholder: _vm.__("Enter method title", "dokan")
                        },
                        domProps: {
                          value: _vm.editShippingMethodData.settings.title
                        },
                        on: {
                          input: function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.editShippingMethodData.settings,
                              "title",
                              $event.target.value
                            )
                          }
                        }
                      })
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "dokan-form-group" }, [
                      _c("label", { attrs: { for: "minimum_order_amount" } }, [
                        _vm._v(
                          _vm._s(
                            _vm.__(
                              "Minimum order amount for free shipping",
                              "dokan"
                            )
                          )
                        )
                      ]),
                      _vm._v(" "),
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value:
                              _vm.editShippingMethodData.settings.min_amount,
                            expression:
                              "editShippingMethodData.settings.min_amount"
                          }
                        ],
                        staticClass: "dokan-form-control",
                        attrs: {
                          type: "text",
                          id: "minimum_order_amount",
                          placeholder: "0.00"
                        },
                        domProps: {
                          value: _vm.editShippingMethodData.settings.min_amount
                        },
                        on: {
                          input: function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.editShippingMethodData.settings,
                              "min_amount",
                              $event.target.value
                            )
                          }
                        }
                      }),
                      _vm._v(" "),
                      _vm.editShippingMethodData.method_id == "flat_rate"
                        ? _c("span", {
                            staticClass: "description",
                            domProps: {
                              innerHTML: _vm._s(_vm.cost_description)
                            }
                          })
                        : _vm._e()
                    ])
                  ]),
              _vm._v(" "),
              _c("template", { slot: "footer" }, [
                _c(
                  "button",
                  {
                    staticClass: "button button-primary button-large",
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.updateShippingMethodSettings($event)
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Save Settings", "dokan")))]
                )
              ])
            ],
            2
          )
        : _vm._e()
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-a2ade7fe", esExports)
  }
}

/***/ }),
/* 43 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Settings_vue__ = __webpack_require__(5);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_70ed6a78_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Settings_vue__ = __webpack_require__(45);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(44)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Settings_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_70ed6a78_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Settings_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/frontend/components/Settings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-70ed6a78", Component.options)
  } else {
    hotAPI.reload("data-v-70ed6a78", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 44 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 45 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "dokan-shipping-settings",
      attrs: { id: "shipping-settings" }
    },
    [
      _vm.successMessage
        ? _c("div", { staticClass: "dokan-alert dokan-alert-success" }, [
            _c("span", { domProps: { innerHTML: _vm._s(_vm.successMessage) } }),
            _vm._v(" "),
            _c(
              "a",
              {
                staticClass: "dokan-close",
                attrs: { "data-dismiss": "alert" }
              },
              [_vm._v("×")]
            )
          ])
        : _vm._e(),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "back-link" },
        [
          _c("router-link", { attrs: { to: { name: "Main" } } }, [
            _vm._v(_vm._s(_vm.__("← Back to Zone List", "dokan")))
          ])
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "form",
        {
          attrs: { method: "post", id: "shipping-settings" },
          on: {
            submit: function($event) {
              $event.preventDefault()
              return _vm.saveSettings($event)
            }
          }
        },
        [
          _c("div", { staticClass: "dokan-shipping-wrapper" }, [
            _c("div", { staticClass: "dokan-form-group" }, [
              _c(
                "label",
                { staticClass: "dokan-w3", attrs: { for: "dps_pt" } },
                [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Processing Time", "dokan")) +
                      "\n                    "
                  ),
                  _c(
                    "span",
                    {
                      directives: [{ name: "tooltip", rawName: "v-tooltip" }],
                      staticClass: "dokan-tooltips-help tips",
                      attrs: {
                        title: _vm.__(
                          "Write your terms, conditions and instructions about shipping",
                          "dokan"
                        )
                      }
                    },
                    [_c("i", { staticClass: "fa fa-question-circle" })]
                  )
                ]
              ),
              _vm._v(" "),
              _c("div", { staticClass: "dokan-w6 dokan-text-left" }, [
                _c(
                  "select",
                  {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.shippingSettings.processing_time,
                        expression: "shippingSettings.processing_time"
                      }
                    ],
                    staticClass: "dokan-form-control",
                    attrs: { name: "dps_pt", id: "dps_pt" },
                    on: {
                      change: function($event) {
                        var $$selectedVal = Array.prototype.filter
                          .call($event.target.options, function(o) {
                            return o.selected
                          })
                          .map(function(o) {
                            var val = "_value" in o ? o._value : o.value
                            return val
                          })
                        _vm.$set(
                          _vm.shippingSettings,
                          "processing_time",
                          $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        )
                      }
                    }
                  },
                  _vm._l(_vm.getProcessingTimes(), function(
                    processingTime,
                    index
                  ) {
                    return _c("option", { domProps: { value: index } }, [
                      _vm._v(_vm._s(processingTime))
                    ])
                  })
                )
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "dokan-form-group" }, [
              _c(
                "label",
                {
                  staticClass: "dokan-w3",
                  attrs: { for: "_dps_shipping_policy" }
                },
                [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Shipping Policy", "dokan")) +
                      "\n                    "
                  ),
                  _c(
                    "span",
                    {
                      directives: [{ name: "tooltip", rawName: "v-tooltip" }],
                      staticClass: "dokan-tooltips-help tips",
                      attrs: {
                        title: _vm.__(
                          "Write your terms, conditions and instructions about shipping",
                          "dokan"
                        )
                      }
                    },
                    [_c("i", { staticClass: "fa fa-question-circle" })]
                  )
                ]
              ),
              _vm._v(" "),
              _c("div", { staticClass: "dokan-w6 dokan-text-left" }, [
                _c("textarea", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.shippingSettings.shipping_policy,
                      expression: "shippingSettings.shipping_policy"
                    }
                  ],
                  staticClass: "dokan-form-control",
                  attrs: { id: "_dps_shipping_policy", rows: "6" },
                  domProps: { value: _vm.shippingSettings.shipping_policy },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.shippingSettings,
                        "shipping_policy",
                        $event.target.value
                      )
                    }
                  }
                })
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "dokan-form-group" }, [
              _c(
                "label",
                {
                  staticClass: "dokan-w3",
                  attrs: { for: "_dps_refund_policy" }
                },
                [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Refund Policy", "dokan")) +
                      "\n                    "
                  ),
                  _c(
                    "span",
                    {
                      directives: [{ name: "tooltip", rawName: "v-tooltip" }],
                      staticClass: "dokan-tooltips-help tips",
                      attrs: {
                        title: _vm.__(
                          "Write your terms, conditions and instructions about refund",
                          "dokan"
                        )
                      }
                    },
                    [_c("i", { staticClass: "fa fa-question-circle" })]
                  )
                ]
              ),
              _vm._v(" "),
              _c("div", { staticClass: "dokan-w6 dokan-text-left" }, [
                _c("textarea", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.shippingSettings.refund_policy,
                      expression: "shippingSettings.refund_policy"
                    }
                  ],
                  staticClass: "dokan-form-control",
                  attrs: { rows: "6" },
                  domProps: { value: _vm.shippingSettings.refund_policy },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.shippingSettings,
                        "refund_policy",
                        $event.target.value
                      )
                    }
                  }
                })
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "dokan-form-group" }, [
              _c(
                "div",
                {
                  staticClass: "dokan-w4",
                  staticStyle: { "margin-left": "27%" }
                },
                [
                  _c("input", {
                    staticClass: "dokan-btn dokan-btn-danger dokan-btn-theme",
                    attrs: { type: "submit", name: "update_shipping_settings" },
                    domProps: { value: _vm.__("Save Settings", "dokan") }
                  })
                ]
              )
            ])
          ])
        ]
      )
    ]
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-70ed6a78", esExports)
  }
}

/***/ })
/******/ ]);