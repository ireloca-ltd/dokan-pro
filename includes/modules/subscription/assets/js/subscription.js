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
/******/ 	return __webpack_require__(__webpack_require__.s = 91);
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

/***/ 28:
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

var ListTable = dokan_get_lib('ListTable');

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'Subscriptions',

    components: {
        ListTable: ListTable
    },

    data: function data() {
        return {
            showCb: true,

            counts: {
                all: 0
            },

            totalItems: 0,
            perPage: 10,
            totalPages: 1,
            loading: false,

            columns: {
                'user_name': {
                    label: this.__('User Name', 'dokan')
                },
                'subscription_title': {
                    label: this.__('Subscription Pack', 'dokan')
                },
                'start_date': {
                    label: this.__('Start Date', 'dokan')
                },
                'end_date': {
                    label: this.__('End Date', 'dokan')
                },
                'status': {
                    label: this.__('Status', 'dokan')
                },
                'action': {
                    label: this.__('Action', 'dokan')
                }
            },
            actions: [],
            bulkActions: [{
                key: 'cancel',
                label: this.__('Cancel Subscription', 'dokan')
            }],
            vendors: []
        };
    },


    watch: {
        '$route.query.status': function $routeQueryStatus() {
            this.fetchSubscription();
        },
        '$route.query.page': function $routeQueryPage() {
            this.fetchSubscription();
        },
        '$route.query.orderby': function $routeQueryOrderby() {
            this.fetchSubscription();
        },
        '$route.query.order': function $routeQueryOrder() {
            this.fetchSubscription();
        }
    },

    computed: {
        currentStatus: function currentStatus() {
            return this.$route.query.status || 'all';
        },
        currentPage: function currentPage() {
            var page = this.$route.query.page || 1;

            return parseInt(page);
        },
        sortBy: function sortBy() {
            return this.$route.query.orderby || 'registered';
        },
        sortOrder: function sortOrder() {
            return this.$route.query.order || 'desc';
        }
    },

    created: function created() {
        this.fetchSubscription();
    },


    methods: {
        cancelSubscription: function cancelSubscription(id) {
            if (confirm(this.__('Are you sure to cancel the subscription?', 'dokan'))) {
                this.deleteSubscripton(id);
            }
        },
        updatedCounts: function updatedCounts(xhr) {
            this.counts.all = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        deleteSubscripton: function deleteSubscripton(id) {
            var self = this;

            self.loading = true;

            dokan.api.delete('/subscription/' + id).done(function (response, status, xhr) {
                location.reload();
            });
        },
        fetchSubscription: function fetchSubscription() {
            var _this = this;

            var self = this;

            self.loading = true;

            // dokan.api.get('/subscription?number=' + this.perPage + '&paged=' + this.currentPage)
            dokan.api.get('/subscription', {
                per_page: self.perPage,
                paged: self.currentPage,
                order: this.sortOrder
            }).done(function (response, status, xhr) {
                if (response.code == 'no_subscription') {
                    return self.loading = false;
                }

                self.vendors = response;
                self.loading = false;

                _this.updatedCounts(xhr);
                _this.updatePagination(xhr);
            });
        },
        goToPage: function goToPage(page) {
            this.$router.push({
                name: 'Subscriptions',
                query: {
                    page: page
                }
            });
        },
        onBulkAction: function onBulkAction(action, items) {
            if (!confirm(this.__('Are you sure to cancel the subscription?', 'dokan'))) {
                return;
            }

            var jsonData = {};
            jsonData[action] = items;

            this.loading = true;

            dokan.api.delete('/subscription/batch', jsonData).done(function (response) {
                location.reload();
            });
        },
        subscriptionUrl: function subscriptionUrl(id) {
            return dokan.urls.adminRoot + 'post.php?post=' + id + '&action=edit';
        }
    }
});

/***/ }),

/***/ 91:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _Subscriptions = __webpack_require__(92);

var _Subscriptions2 = _interopRequireDefault(_Subscriptions);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

dokan_add_route(_Subscriptions2.default);

/***/ }),

/***/ 92:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Subscriptions_vue__ = __webpack_require__(28);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a9fa2248_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Subscriptions_vue__ = __webpack_require__(94);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(93)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Subscriptions_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a9fa2248_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Subscriptions_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "includes/modules/subscription/src/admin/components/Subscriptions.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-a9fa2248", Component.options)
  } else {
    hotAPI.reload("data-v-a9fa2248", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 93:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 94:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "subscription-list" },
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [
        _vm._v(_vm._s(_vm.__("Subscription User List", "dokan")))
      ]),
      _vm._v(" "),
      _c("hr", { staticClass: "wp-header-end" }),
      _vm._v(" "),
      _c("ul", { staticClass: "subsubsub" }, [
        _c(
          "li",
          [
            _c("router-link", {
              attrs: { to: "", "active-class": "current", exact: "" },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Total Subscribed Vendors <span class='count'>(%s)</span>",
                      "dokan"
                    ),
                    _vm.counts.all
                  )
                )
              }
            })
          ],
          1
        )
      ]),
      _vm._v(" "),
      _c("list-table", {
        attrs: {
          columns: _vm.columns,
          loading: _vm.loading,
          rows: _vm.vendors,
          actions: _vm.actions,
          "show-cb": _vm.showCb,
          "total-items": _vm.totalItems,
          "bulk-actions": _vm.bulkActions,
          "total-pages": _vm.totalPages,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage,
          "not-found": "No vendors found.",
          "sort-order": _vm.sortOrder
        },
        on: { pagination: _vm.goToPage, "bulk:click": _vm.onBulkAction },
        scopedSlots: _vm._u([
          {
            key: "user_name",
            fn: function(data) {
              return [
                _c("strong", [
                  _c("a", { attrs: { href: data.row.user_link } }, [
                    _vm._v(
                      _vm._s(
                        data.row.user_name
                          ? data.row.user_name
                          : _vm.__("(no name)", "dokan")
                      )
                    )
                  ])
                ])
              ]
            }
          },
          {
            key: "subscription_title",
            fn: function(data) {
              return [
                _c("strong", [
                  _c(
                    "a",
                    {
                      attrs: {
                        href: _vm.subscriptionUrl(data.row.subscription_id)
                      }
                    },
                    [
                      _vm._v(
                        _vm._s(
                          data.row.subscription_title
                            ? data.row.subscription_title
                            : _vm.__("(no name)", "dokan")
                        )
                      )
                    ]
                  )
                ])
              ]
            }
          },
          {
            key: "status",
            fn: function(data) {
              return [
                _vm._v(
                  "\n            " +
                    _vm._s(
                      data.row.status == 1
                        ? _vm.__("Active", "dokan")
                        : _vm.__("Inactive", "dokan")
                    ) +
                    "\n        "
                )
              ]
            }
          },
          {
            key: "action",
            fn: function(data) {
              return [
                _c(
                  "button",
                  {
                    staticClass: "button button-primary",
                    on: {
                      click: function($event) {
                        _vm.cancelSubscription(data.row.id)
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Cancel", "dokan")))]
                )
              ]
            }
          }
        ])
      })
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
    require("vue-hot-reload-api")      .rerender("data-v-a9fa2248", esExports)
  }
}

/***/ })

/******/ });