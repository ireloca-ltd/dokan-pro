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
/******/ 	return __webpack_require__(__webpack_require__.s = 100);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
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

/***/ 1:
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),

/***/ 100:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _AbuseReports = __webpack_require__(101);

var _AbuseReports2 = _interopRequireDefault(_AbuseReports);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

dokan_add_route(_AbuseReports2.default);

/***/ }),

/***/ 101:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_AbuseReports_vue__ = __webpack_require__(30);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_23efc86a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_AbuseReports_vue__ = __webpack_require__(104);
var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_AbuseReports_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_23efc86a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_AbuseReports_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "includes/modules/report-abuse/src/js/admin/pages/AbuseReports.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-23efc86a", Component.options)
  } else {
    hotAPI.reload("data-v-23efc86a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 102:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_AbuseReasonsDropdown_vue__ = __webpack_require__(31);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_41faa61c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_AbuseReasonsDropdown_vue__ = __webpack_require__(103);
var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_AbuseReasonsDropdown_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_41faa61c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_AbuseReasonsDropdown_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "includes/modules/report-abuse/src/js/components/AbuseReasonsDropdown.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-41faa61c", Component.options)
  } else {
    hotAPI.reload("data-v-41faa61c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 103:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "select",
    {
      directives: [
        {
          name: "model",
          rawName: "v-model",
          value: _vm.selectedReason,
          expression: "selectedReason"
        }
      ],
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
          _vm.selectedReason = $event.target.multiple
            ? $$selectedVal
            : $$selectedVal[0]
        }
      }
    },
    [
      _c("option", { attrs: { value: "" } }, [_vm._v(_vm._s(_vm.noneText))]),
      _vm._v(" "),
      _vm._l(_vm.abuseReasons, function(reason) {
        return _c("option", {
          key: reason.id,
          domProps: { textContent: _vm._s(reason.value) }
        })
      })
    ],
    2
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-41faa61c", esExports)
  }
}

/***/ }),

/***/ 104:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [
        _vm._v(_vm._s(_vm.__("Abuse Reports", "dokan")))
      ]),
      _vm._v(" "),
      _c("hr", { staticClass: "wp-header-end" }),
      _vm._v(" "),
      _c(
        "list-table",
        {
          attrs: {
            columns: _vm.columns,
            loading: _vm.loading,
            rows: _vm.reports,
            actions: _vm.actions,
            "bulk-actions": _vm.bulkActions,
            "total-items": _vm.totalItems,
            "total-pages": _vm.totalPages,
            "per-page": _vm.perPage,
            "current-page": _vm.currentPage
          },
          on: { pagination: _vm.goToPage, "bulk:click": _vm.onBulkAction },
          scopedSlots: _vm._u([
            {
              key: "reason",
              fn: function(ref) {
                var row = ref.row
                return [
                  _c("strong", [
                    _c(
                      "a",
                      {
                        attrs: { href: "#view-report" },
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            _vm.showReport(row)
                          }
                        }
                      },
                      [_vm._v(_vm._s(row.reason))]
                    )
                  ])
                ]
              }
            },
            {
              key: "product",
              fn: function(ref) {
                var row = ref.row
                return [
                  _c("a", { attrs: { href: row.product.admin_url } }, [
                    _vm._v(_vm._s(row.product.title))
                  ])
                ]
              }
            },
            {
              key: "vendor",
              fn: function(ref) {
                var row = ref.row
                return [
                  _c(
                    "router-link",
                    { attrs: { to: "/vendors/" + row.vendor.id } },
                    [
                      _vm._v(
                        "\n                " +
                          _vm._s(
                            row.vendor.name
                              ? row.vendor.name
                              : _vm.__("(no name)", "dokan")
                          ) +
                          "\n            "
                      )
                    ]
                  )
                ]
              }
            },
            {
              key: "reported_by",
              fn: function(ref) {
                var row = ref.row
                return [
                  row.reported_by.admin_url
                    ? _c("a", {
                        attrs: {
                          href: row.reported_by.admin_url,
                          target: "_blank"
                        },
                        domProps: { textContent: _vm._s(row.reported_by.name) }
                      })
                    : [
                        _vm._v(
                          "\n                " +
                            _vm._s(row.reported_by.name) +
                            " <" +
                            _vm._s(row.reported_by.email) +
                            ">\n            "
                        )
                      ]
                ]
              }
            },
            {
              key: "reported_at",
              fn: function(ref) {
                var row = ref.row
                return [
                  _vm._v(
                    "\n            " +
                      _vm._s(
                        _vm
                          .moment(row.reported_at)
                          .format("MMM D, YYYY h:mm:ss a")
                      ) +
                      "\n        "
                  )
                ]
              }
            }
          ])
        },
        [
          _c(
            "template",
            { slot: "filters" },
            [
              _c("abuse-reasons-dropdown", {
                attrs: {
                  placeholder: _vm.__("Filter by abuse reason", "dokan")
                },
                model: {
                  value: _vm.filter.reason,
                  callback: function($$v) {
                    _vm.$set(_vm.filter, "reason", $$v)
                  },
                  expression: "filter.reason"
                }
              }),
              _vm._v(" "),
              _vm.filter.reason
                ? _c(
                    "button",
                    {
                      staticClass: "button",
                      attrs: { type: "button" },
                      on: {
                        click: function($event) {
                          _vm.filter.reason = ""
                        }
                      }
                    },
                    [_vm._v("×")]
                  )
                : _vm._e(),
              _vm._v(" "),
              _c("select", {
                staticStyle: { width: "190px" },
                attrs: {
                  id: "filter-products",
                  "data-placeholder": _vm.__("Filter by product", "dokan")
                }
              }),
              _vm._v(" "),
              _vm.filter.product_id
                ? _c(
                    "button",
                    {
                      staticClass: "button",
                      attrs: { type: "button" },
                      on: {
                        click: function($event) {
                          _vm.filter.product_id = 0
                        }
                      }
                    },
                    [_vm._v("×")]
                  )
                : _vm._e(),
              _vm._v(" "),
              _c("select", {
                staticStyle: { width: "190px" },
                attrs: {
                  id: "filter-vendors",
                  "data-placeholder": _vm.__("Filter by vendor", "dokan")
                }
              }),
              _vm._v(" "),
              _vm.filter.vendor_id
                ? _c(
                    "button",
                    {
                      staticClass: "button",
                      attrs: { type: "button" },
                      on: {
                        click: function($event) {
                          _vm.filter.vendor_id = 0
                        }
                      }
                    },
                    [_vm._v("×")]
                  )
                : _vm._e()
            ],
            1
          )
        ],
        2
      ),
      _vm._v(" "),
      _vm.showModal
        ? _c(
            "modal",
            {
              attrs: {
                title: _vm.__("Product Abuse Report", "dokan"),
                footer: false
              },
              on: { close: _vm.hideReport }
            },
            [
              _c("template", { slot: "body" }, [
                _c("p", { staticStyle: { "margin-top": "0" } }, [
                  _c("strong", [
                    _vm._v(_vm._s(_vm.__("Reported Product", "dokan")) + ":")
                  ]),
                  _vm._v(" "),
                  _c("a", { attrs: { href: _vm.report.product.admin_url } }, [
                    _vm._v(_vm._s(_vm.report.product.title))
                  ])
                ]),
                _vm._v(" "),
                _c("p", [
                  _c("strong", [
                    _vm._v(_vm._s(_vm.__("Reason", "dokan")) + ":")
                  ]),
                  _vm._v(" " + _vm._s(_vm.report.reason))
                ]),
                _vm._v(" "),
                _c("p", [
                  _c("strong", [
                    _vm._v(_vm._s(_vm.__("Description", "dokan")) + ":")
                  ]),
                  _vm._v(" " + _vm._s(_vm.report.description || "―"))
                ]),
                _vm._v(" "),
                _c(
                  "p",
                  [
                    _c("strong", [
                      _vm._v(_vm._s(_vm.__("Reported by", "dokan")) + ":")
                    ]),
                    _vm._v(" "),
                    _vm.report.reported_by.admin_url
                      ? _c("a", {
                          attrs: {
                            href: _vm.report.reported_by.admin_url,
                            target: "_blank"
                          },
                          domProps: {
                            textContent: _vm._s(_vm.report.reported_by.name)
                          }
                        })
                      : [
                          _vm._v(
                            "\n                    " +
                              _vm._s(_vm.report.reported_by.name) +
                              " <" +
                              _vm._s(_vm.report.reported_by.email) +
                              ">\n                "
                          )
                        ]
                  ],
                  2
                ),
                _vm._v(" "),
                _c("p", [
                  _c("strong", [
                    _vm._v(_vm._s(_vm.__("Reported At", "dokan")) + ":")
                  ]),
                  _vm._v(
                    " " +
                      _vm._s(
                        _vm
                          .moment(_vm.report.reported_at)
                          .format("MMM D, YYYY h:mm:ss a")
                      )
                  )
                ]),
                _vm._v(" "),
                _c(
                  "p",
                  [
                    _c("strong", [
                      _vm._v(_vm._s(_vm.__("Product Vendor", "dokan")) + ":")
                    ]),
                    _vm._v(" "),
                    _vm.report.vendor.admin_url
                      ? _c("a", {
                          attrs: { href: _vm.report.vendor.admin_url },
                          domProps: {
                            textContent: _vm._s(_vm.report.vendor.name)
                          }
                        })
                      : [
                          _vm._v(
                            "\n                    " +
                              _vm._s(_vm.report.reported_by.name) +
                              " <" +
                              _vm._s(_vm.report.reported_by.email) +
                              ">\n                "
                          )
                        ]
                  ],
                  2
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
    require("vue-hot-reload-api")      .rerender("data-v-23efc86a", esExports)
  }
}

/***/ }),

/***/ 30:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__components_AbuseReasonsDropdown_vue__ = __webpack_require__(102);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


var ListTable = dokan_get_lib('ListTable');
var Modal = dokan_get_lib('Modal');
var Multiselect = dokan_get_lib('Multiselect');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'AbuseReports',

    components: {
        AbuseReasonsDropdown: __WEBPACK_IMPORTED_MODULE_0__components_AbuseReasonsDropdown_vue__["a" /* default */],
        ListTable: ListTable,
        Modal: Modal,
        Multiselect: Multiselect
    },

    data: function data() {
        return {
            columns: {
                reason: {
                    label: this.__('Reason', 'dokan')
                },

                product: {
                    label: this.__('Product', 'dokan')
                },

                vendor: {
                    label: this.__('Vendor', 'dokan')
                },

                reported_by: {
                    label: this.__('Reported by', 'dokan')
                },

                reported_at: {
                    label: this.__('Reported at', 'dokan')
                }
            },
            loading: false,
            reports: [],
            actions: [],
            bulkActions: [{
                key: 'delete',
                label: this.__('Delete', 'dokan')
            }],
            totalItems: 0,
            totalPages: 1,
            perPage: 10,
            showModal: false,
            report: {},
            query: {},
            filter: {
                reason: '',
                vendor_id: 0,
                product_id: 0
            }
        };
    },


    computed: {
        currentPage: function currentPage() {
            var page = this.$route.query.page || 1;
            return parseInt(page);
        },
        queryFilterReason: function queryFilterReason() {
            return this.$route.query.reason || '';
        }
    },

    created: function created() {
        if (this.queryFilterReason) {
            this.filter.reason = this.queryFilterReason;
            this.query.reason = this.queryFilterReason;
        }

        // @todo: Filter by product and vendor on page load

        this.fetchReports();
    },
    mounted: function mounted() {
        var self = this;

        $('#filter-products').selectWoo({
            ajax: {
                url: dokan.rest.root + 'wc/v3/products',
                dataType: 'json',
                headers: {
                    "X-WP-Nonce": dokan.rest.nonce
                },
                data: function data(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function processResults(data) {
                    return {
                        results: data.map(function (product) {
                            return {
                                id: product.id,
                                text: product.name
                            };
                        })
                    };
                }
            }
        });

        $('#filter-products').on('select2:select', function (e) {
            self.filter.product_id = e.params.data.id;
        });

        $('#filter-vendors').selectWoo({
            ajax: {
                url: dokan.rest.root + 'dokan/v1/stores',
                dataType: 'json',
                headers: {
                    "X-WP-Nonce": dokan.rest.nonce
                },
                data: function data(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function processResults(data) {
                    return {
                        results: data.map(function (store) {
                            return {
                                id: store.id,
                                text: store.store_name
                            };
                        })
                    };
                }
            }
        });

        $('#filter-vendors').on('select2:select', function (e) {
            self.filter.vendor_id = e.params.data.id;
        });
    },


    watch: {
        '$route.query.page': function $routeQueryPage() {
            this.fetchReports();
        },
        '$route.query.reason': function $routeQueryReason() {
            this.fetchReports();
        },
        '$route.query.product_id': function $routeQueryProduct_id() {
            this.fetchReports();
        },
        '$route.query.vendor_id': function $routeQueryVendor_id() {
            this.fetchReports();
        },
        'filter.reason': function filterReason(reason) {
            this.query = {};

            if (reason) {
                this.query = {
                    reason: reason
                };
            }

            this.goTo(this.query);
        },
        'filter.product_id': function filterProduct_id(product_id) {
            if (product_id) {
                this.query.product_id = product_id;
            } else if (this.query.product_id) {
                delete this.query.product_id;
                this.clearSelection('#filter-products');
            }

            this.goTo(this.query);
        },
        'filter.vendor_id': function filterVendor_id(vendor_id) {
            if (vendor_id) {
                this.query.vendor_id = vendor_id;
            } else if (this.query.vendor_id) {
                delete this.query.vendor_id;
                this.clearSelection('#filter-vendors');
            }

            this.goTo(this.query);
        }
    },

    methods: {
        fetchReports: function fetchReports() {
            var self = this;

            self.loading = true;

            if (self.currentPage > 1) {
                self.query.page = self.currentPage;
            }

            dokan.api.get('/abuse-reports', self.query).done(function (response, status, xhr) {
                self.reports = response;
                self.loading = false;

                self.updatePagination(xhr);
            });
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-Dokan-AbuseReports-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-Dokan-AbuseReports-Total'));
        },
        moment: function (_moment) {
            function moment(_x) {
                return _moment.apply(this, arguments);
            }

            moment.toString = function () {
                return _moment.toString();
            };

            return moment;
        }(function (date) {
            return moment(date);
        }),
        goToPage: function goToPage(page) {
            this.query.page = page;
            this.goTo(this.query);
        },
        goTo: function goTo(query) {
            this.$router.push({
                name: 'AbuseReports',
                query: query
            });
        },
        showReport: function showReport(report) {
            this.report = report;
            this.showModal = true;
        },
        hideReport: function hideReport() {
            this.report = {};
            this.showModal = false;
        },
        clearSelection: function clearSelection(element) {
            $(element).val(null).trigger('change');
        },
        onBulkAction: function onBulkAction(action, items) {
            var self = this;

            if (!confirm(this.__('Are you sure you want to delete this report', 'dokan'))) {
                return;
            }

            dokan.api.delete('/abuse-reports/batch', { items: items }).done(function (response) {
                self.fetchReports();
            });
        }
    }
});
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(1)))

/***/ }),

/***/ 31:
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

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'AbuseReasonsDropdown',

    props: {
        value: {
            type: String,
            required: true
        },

        placeholder: {
            type: String,
            required: false,
            default: ''
        }
    },

    data: function data() {
        return {
            abuseReasons: []
        };
    },


    computed: {
        selectedReason: {
            get: function get() {
                var _this = this;

                var reason = this.abuseReasons.filter(function (reason) {
                    return _this.value === reason.value;
                });

                if (reason.length) {
                    return reason[0].value;
                }

                return '';
            },
            set: function set(reason) {
                this.$emit('input', reason || '');
            }
        },

        noneText: function noneText() {
            return this.placeholder || this.__('Select a reason', 'dokan');
        }
    },

    created: function created() {
        this.fetchAbuseReasons();
    },


    methods: {
        fetchAbuseReasons: function fetchAbuseReasons() {
            var self = this;

            dokan.api.get('/abuse-reports/abuse-reasons').done(function (response) {
                self.abuseReasons = response;
            });
        }
    }
});

/***/ })

/******/ });