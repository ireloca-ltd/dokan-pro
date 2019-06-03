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
/******/ 	return __webpack_require__(__webpack_require__.s = 95);
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

/***/ 29:
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

var ListTable = dokan_get_lib('ListTable');
var Switches = dokan_get_lib('Switches');
var Search = dokan_get_lib('Search');

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'WholesaleCustomer',

    components: {
        ListTable: ListTable,
        Switches: Switches,
        Search: Search
    },

    data: function data() {
        return {
            showCb: true,
            counts: {
                deactive: 0,
                active: 0,
                all: 0
            },

            totalItems: 0,
            perPage: 20,
            totalPages: 1,
            loading: false,

            columns: {
                'full_name': {
                    label: this.__('Name', 'dokan')
                },
                'email': {
                    label: this.__('E-mail', 'dokan')
                },
                'username': {
                    label: this.__('Username', 'dokan')
                },
                'role': {
                    label: this.__('Roles', 'dokan')
                },
                'registered': {
                    label: this.__('Registered', 'dokan'),
                    sortable: true
                },
                'wholesale_status': {
                    label: this.__('Status', 'dokan')
                }
            },
            actionColumn: 'full_name',
            actions: [{
                key: 'edit',
                label: this.__('Edit', 'dokan')
            }, {
                key: 'orders',
                label: this.__('Orders', 'dokan')
            }, {
                key: 'delete',
                label: this.__('Remove', 'dokan')
            }],
            bulkActions: [{
                key: 'activate',
                label: this.__('Active', 'dokan')
            }, {
                key: 'deactivate',
                label: this.__('Deactive', 'dokan')
            }],
            customers: []
        };
    },


    watch: {
        '$route.query.status': function $routeQueryStatus() {
            this.fetchWholesaleCustomers();
        },
        '$route.query.page': function $routeQueryPage() {
            this.fetchWholesaleCustomers();
        },
        '$route.query.orderby': function $routeQueryOrderby() {
            this.fetchWholesaleCustomers();
        },
        '$route.query.order': function $routeQueryOrder() {
            this.fetchWholesaleCustomers();
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

        this.fetchWholesaleCustomers();
    },


    methods: {
        getFullName: function getFullName(row) {
            return row.first_name + ' ' + row.last_name;
        },
        doSearch: function doSearch(payload) {
            var _this = this;

            var self = this;
            self.loading = true;

            dokan.api.get('/wholesale/customers/?search=' + payload, {
                page: this.currentPage,
                orderby: this.sortBy,
                order: this.sortOrder
            }).done(function (response, status, xhr) {
                self.customers = response;
                self.loading = false;

                _this.updatePagination(xhr);
            });
        },
        updatedCounts: function updatedCounts(xhr) {
            this.counts.deactive = parseInt(xhr.getResponseHeader('X-Status-Deactive'));
            this.counts.active = parseInt(xhr.getResponseHeader('X-Status-Active'));
            this.counts.all = parseInt(xhr.getResponseHeader('X-Status-All'));
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        fetchWholesaleCustomers: function fetchWholesaleCustomers() {
            var _this2 = this;

            var self = this;
            self.loading = true;

            // dokan.api.get('/stores?per_page=' + this.perPage + '&page=' + this.currentPage + '&status=' + this.currentStatus)
            dokan.api.get('/wholesale/customers', {
                per_page: this.perPage,
                page: this.currentPage,
                status: this.currentStatus,
                orderby: this.sortBy,
                order: this.sortOrder
            }).done(function (response, status, xhr) {
                // console.log(response, status, xhr);
                self.customers = response;
                self.loading = false;

                _this2.updatedCounts(xhr);
                _this2.updatePagination(xhr);
            });
        },
        onActionClick: function onActionClick(action, row) {
            var _this3 = this;

            if ('delete' === action) {
                if (confirm('Are you sure to delete?')) {
                    dokan.api.put('/wholesale/customer/' + row.id, {
                        status: 'delete'
                    }).done(function (response) {
                        _this3.$notify({
                            title: _this3.__('Success!', 'dokan'),
                            type: 'success',
                            text: _this3.__('Successfully removed from wholesale customer lists', 'dokan')
                        });

                        _this3.fetchWholesaleCustomers();
                    });
                }
            }
        },
        onSwitch: function onSwitch(status, customer_id) {
            var _this4 = this;

            var message = status === false ? this.__('The customer has been disabled for wholesale.', 'dokan') : this.__('Wholesale capability activate', 'dokan');

            dokan.api.put('/wholesale/customer/' + customer_id, {
                status: status === false ? 'deactivate' : 'activate'
            }).done(function (response) {
                _this4.$notify({
                    title: _this4.__('Success!', 'dokan'),
                    type: 'success',
                    text: message
                });

                if (_this4.currentStatus !== 'all') {
                    _this4.fetchWholesaleCustomers();
                }
            });
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
            this.$router.push({
                name: 'WholesaleCustomer',
                query: {
                    status: this.currentStatus,
                    page: page
                }
            });
        },
        onBulkAction: function onBulkAction(action, items) {
            var _this5 = this;

            var jsonData = {};
            jsonData[action] = items;

            this.loading = true;

            dokan.api.put('/wholesale/customers/batch', jsonData).done(function (response) {
                _this5.bulkLocal = -1;
                _this5.checkedItems = [];
                _this5.loading = false;
                _this5.fetchWholesaleCustomers();
            });
        },
        sortCallback: function sortCallback(column, order) {
            this.$router.push({
                name: 'WholesaleCustomer',
                query: {
                    status: this.currentStatus,
                    page: 1,
                    orderby: column,
                    order: order
                }
            });
        },
        ordersUrl: function ordersUrl(id) {
            return dokan.urls.adminRoot + 'edit.php?post_type=shop_order&_customer_user=' + id;
        },
        editUrl: function editUrl(id) {
            return dokan.urls.adminRoot + 'user-edit.php?user_id=' + id;
        }
    }
});

/***/ }),

/***/ 95:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _WholesaleCustomer = __webpack_require__(96);

var _WholesaleCustomer2 = _interopRequireDefault(_WholesaleCustomer);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

dokan_add_route(_WholesaleCustomer2.default);

/***/ }),

/***/ 96:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_WholesaleCustomer_vue__ = __webpack_require__(29);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5706824d_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_WholesaleCustomer_vue__ = __webpack_require__(98);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(97)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_WholesaleCustomer_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5706824d_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_WholesaleCustomer_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "includes/modules/wholesale/src/admin/components/WholesaleCustomer.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5706824d", Component.options)
  } else {
    hotAPI.reload("data-v-5706824d", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 97:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 98:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "wholesale-customer-list" },
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [
        _vm._v(_vm._s(_vm.__("Wholesale Customers", "dokan")))
      ]),
      _vm._v(" "),
      _c("hr", { staticClass: "wp-header-end" }),
      _vm._v(" "),
      _c("ul", { staticClass: "subsubsub" }, [
        _c(
          "li",
          [
            _c("router-link", {
              attrs: {
                to: { name: "WholesaleCustomer", query: { status: "all" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__("All <span class='count'>(%s)</span>", "dokan"),
                    _vm.counts.all
                  )
                )
              }
            }),
            _vm._v(" | ")
          ],
          1
        ),
        _vm._v(" "),
        _c(
          "li",
          [
            _c("router-link", {
              attrs: {
                to: { name: "WholesaleCustomer", query: { status: "active" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__("Active <span class='count'>(%s)</span>", "dokan"),
                    _vm.counts.active
                  )
                )
              }
            }),
            _vm._v(" | ")
          ],
          1
        ),
        _vm._v(" "),
        _c(
          "li",
          [
            _c("router-link", {
              attrs: {
                to: {
                  name: "WholesaleCustomer",
                  query: { status: "deactive" }
                },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__("Deactive <span class='count'>(%s)</span>", "dokan"),
                    _vm.counts.deactive
                  )
                )
              }
            })
          ],
          1
        )
      ]),
      _vm._v(" "),
      _c("search", {
        attrs: { title: "Search Customer" },
        on: { searched: _vm.doSearch }
      }),
      _vm._v(" "),
      _c("list-table", {
        attrs: {
          columns: _vm.columns,
          loading: _vm.loading,
          rows: _vm.customers,
          actions: _vm.actions,
          actionColumn: "full_name",
          "show-cb": _vm.showCb,
          "total-items": _vm.totalItems,
          "bulk-actions": _vm.bulkActions,
          "total-pages": _vm.totalPages,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage,
          "action-column": _vm.actionColumn,
          "not-found": "No customers found.",
          "sort-by": _vm.sortBy,
          "sort-order": _vm.sortOrder
        },
        on: {
          sort: _vm.sortCallback,
          pagination: _vm.goToPage,
          "bulk:click": _vm.onBulkAction,
          searched: _vm.doSearch
        },
        scopedSlots: _vm._u([
          {
            key: "full_name",
            fn: function(data) {
              return [
                _c("img", {
                  attrs: {
                    src: data.row.avatar,
                    alt: _vm.getFullName(data.row),
                    width: "50"
                  }
                }),
                _vm._v(" "),
                _c("strong", [
                  _c("a", { attrs: { href: _vm.editUrl(data.row.id) } }, [
                    _vm._v(
                      _vm._s(
                        _vm.getFullName(data.row)
                          ? _vm.getFullName(data.row)
                          : _vm.__("(no name)", "dokan")
                      )
                    )
                  ])
                ])
              ]
            }
          },
          {
            key: "email",
            fn: function(data) {
              return [
                _c("a", { attrs: { href: "mailto:" + data.row.email } }, [
                  _vm._v(_vm._s(data.row.email))
                ])
              ]
            }
          },
          {
            key: "registered",
            fn: function(data) {
              return [
                _vm._v(
                  "\n            " +
                    _vm._s(
                      _vm.moment(data.row.registered).format("MMM D, YYYY")
                    ) +
                    "\n        "
                )
              ]
            }
          },
          {
            key: "wholesale_status",
            fn: function(data) {
              return [
                _c("switches", {
                  attrs: {
                    enabled: data.row.wholesale_status == "active",
                    value: data.row.id
                  },
                  on: { input: _vm.onSwitch }
                })
              ]
            }
          },
          {
            key: "row-actions",
            fn: function(data) {
              return _vm._l(_vm.actions, function(action, index) {
                return _c(
                  "span",
                  { class: action.key },
                  [
                    action.key == "edit"
                      ? _c("a", { attrs: { href: _vm.editUrl(data.row.id) } }, [
                          _vm._v(_vm._s(action.label))
                        ])
                      : action.key == "orders"
                        ? _c(
                            "a",
                            { attrs: { href: _vm.ordersUrl(data.row.id) } },
                            [_vm._v(_vm._s(action.label))]
                          )
                        : _c(
                            "a",
                            {
                              attrs: { href: "#" },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  _vm.onActionClick(action.key, data.row)
                                }
                              }
                            },
                            [_vm._v(_vm._s(action.label))]
                          ),
                    _vm._v(" "),
                    index !== _vm.actions.length - 1
                      ? [_vm._v(" | ")]
                      : _vm._e()
                  ],
                  2
                )
              })
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
    require("vue-hot-reload-api")      .rerender("data-v-5706824d", esExports)
  }
}

/***/ })

/******/ });