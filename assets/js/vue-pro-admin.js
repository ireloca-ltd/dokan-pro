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
/******/ 	return __webpack_require__(__webpack_require__.s = 46);
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
/* 1 */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_admin_components_AddVendor_vue__ = __webpack_require__(49);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

    name: 'Vendors',

    components: {
        ListTable: ListTable,
        Switches: Switches,
        Search: Search,
        AddVendor: __WEBPACK_IMPORTED_MODULE_0_admin_components_AddVendor_vue__["a" /* default */]
    },

    data: function data() {
        return {
            showCb: true,
            counts: {
                pending: 0,
                approved: 0,
                all: 0
            },
            vendorId: 0,
            totalItems: 0,
            perPage: 20,
            totalPages: 1,
            loading: false,

            columns: {
                'store_name': {
                    label: this.__('Store', 'dokan'),
                    sortable: true
                },
                'email': {
                    label: this.__('E-mail', 'dokan')
                },
                'phone': {
                    label: this.__('Phone', 'dokan')
                },
                'registered': {
                    label: this.__('Registered', 'dokan'),
                    sortable: true
                },
                'enabled': {
                    label: this.__('Status', 'dokan')
                }
            },
            actionColumn: 'title',
            actions: [{
                key: 'edit',
                label: this.__('Edit', 'dokan')
            }, {
                key: 'products',
                label: this.__('Products', 'dokan')
            }, {
                key: 'orders',
                label: this.__('Orders', 'dokan')
            }],
            bulkActions: [{
                key: 'approved',
                label: this.__('Approve Vendors', 'dokan')
            }, {
                key: 'pending',
                label: this.__('Disable Selling', 'dokan')
            }],
            vendors: [],
            loadAddVendor: false,
            categories: [],
            isCategoryMultiple: false,
            storeCategoryType: dokan.store_category_type
        };
    },


    watch: {
        '$route.query.status': function $routeQueryStatus() {
            this.fetchVendors();
        },
        '$route.query.page': function $routeQueryPage() {
            this.fetchVendors();
        },
        '$route.query.orderby': function $routeQueryOrderby() {
            this.fetchVendors();
        },
        '$route.query.order': function $routeQueryOrder() {
            this.fetchVendors();
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
        },
        storeCategory: function storeCategory() {
            return this.$route.query.store_category || null;
        }
    },

    created: function created() {
        var _this = this;

        this.$root.$on('modalClosed', function () {
            _this.loadAddVendor = false;
            _this.vendorId = 0;
        });

        this.fetchVendors();

        if (this.storeCategoryType !== 'none') {
            this.fetchCategories();
        }

        this.$root.$on('vendorAdded', function (payload) {
            _this.vendors.unshift(payload);
        });

        this.$root.$on('addAnotherVendor', function () {
            _this.loadAddVendor = true;
        });
    },


    methods: {
        addNew: function addNew() {
            this.loadAddVendor = true;
        },
        doSearch: function doSearch(payload) {
            var _this2 = this;

            var self = this;
            self.loading = true;

            dokan.api.get('/stores?search=' + payload, {
                page: this.currentPage,
                orderby: this.sortBy,
                order: this.sortOrder
            }).done(function (response, status, xhr) {
                self.vendors = response;
                self.loading = false;

                _this2.updatedCounts(xhr);
                _this2.updatePagination(xhr);
            });
        },
        updatedCounts: function updatedCounts(xhr) {
            this.counts.pending = parseInt(xhr.getResponseHeader('X-Status-Pending'));
            this.counts.approved = parseInt(xhr.getResponseHeader('X-Status-Approved'));
            this.counts.all = parseInt(xhr.getResponseHeader('X-Status-All'));
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        fetchVendors: function fetchVendors() {

            var self = this;

            self.loading = true;

            var data = {
                per_page: self.perPage,
                page: self.currentPage,
                status: self.currentStatus,
                orderby: self.sortBy,
                order: self.sortOrder,
                store_category: self.storeCategory
            };

            dokan.api.get('/stores', data).done(function (response, status, xhr) {
                self.vendors = response;
                self.loading = false;

                self.updatedCounts(xhr);
                self.updatePagination(xhr);
            });
        },
        fetchCategories: function fetchCategories() {
            var _this3 = this;

            var self = this;

            dokan.api.get('/store-categories').done(function (response, status, xhr) {
                self.categories = response;
                self.isCategoryMultiple = 'multiple' === xhr.getResponseHeader('X-WP-Store-Category-Type');

                self.columns = {
                    'store_name': {
                        label: _this3.__('Store', 'dokan'),
                        sortable: true
                    },
                    'email': {
                        label: _this3.__('E-mail', 'dokan')
                    },
                    'categories': {
                        label: self.isCategoryMultiple ? _this3.__('Categories', 'dokan') : _this3.__('Category', 'dokan')
                    },
                    'phone': {
                        label: _this3.__('Phone', 'dokan')
                    },
                    'registered': {
                        label: _this3.__('Registered', 'dokan'),
                        sortable: true
                    },
                    'enabled': {
                        label: _this3.__('Status', 'dokan')
                    }
                };
            });
        },
        onActionClick: function onActionClick(action, row) {
            if ('trash' === action) {
                if (confirm('Are you sure to delete?')) {
                    alert('deleted: ' + row.title);
                }
            }
        },
        onSwitch: function onSwitch(status, vendor_id) {
            var _this4 = this;

            var message = status === false ? this.__('The vendor has been disabled.', 'dokan') : this.__('Selling has been enabled', 'dokan');

            dokan.api.put('/stores/' + vendor_id + '/status', {
                status: status === false ? 'inactive' : 'active'
            }).done(function (response) {
                _this4.$notify({
                    title: _this4.__('Success!', 'dokan'),
                    type: 'success',
                    text: message
                });

                if (_this4.currentStatus !== 'all') {
                    _this4.fetchVendors();
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
                name: 'Vendors',
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

            dokan.api.put('/stores/batch', jsonData).done(function (response) {
                _this5.loading = false;
                _this5.fetchVendors();
            });
        },
        sortCallback: function sortCallback(column, order) {
            this.$router.push({
                name: 'Vendors',
                query: {
                    status: this.currentStatus,
                    page: 1,
                    orderby: column,
                    order: order
                }
            });
        },
        productUrl: function productUrl(id) {
            return dokan.urls.adminRoot + 'edit.php?post_type=product&author=' + id;
        },
        ordersUrl: function ordersUrl(id) {
            return dokan.urls.adminRoot + 'edit.php?post_type=shop_order&vendor_id=' + id;
        },
        editUrl: function editUrl(id) {
            return dokan.urls.adminRoot + 'user-edit.php?user_id=' + id;
        }
    }
});

/***/ }),
/* 7 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__VendorAccountFields_vue__ = __webpack_require__(8);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__VendorAddressFields_vue__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__VendorPaymentFields_vue__ = __webpack_require__(15);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


var Modal = dokan_get_lib('Modal');
var Loading = dokan_get_lib('Loading');





/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'AddVendor',

    props: ['vendorId'],

    components: {
        Modal: Modal,
        Loading: Loading,
        VendorAccountFields: __WEBPACK_IMPORTED_MODULE_0__VendorAccountFields_vue__["a" /* default */],
        VendorAddressFields: __WEBPACK_IMPORTED_MODULE_1__VendorAddressFields_vue__["a" /* default */],
        VendorPaymentFields: __WEBPACK_IMPORTED_MODULE_2__VendorPaymentFields_vue__["a" /* default */]
    },

    data: function data() {
        return {
            isLoading: false,
            storeId: '',
            nextBtn: this.__('Next', 'dokan'),
            title: this.__('Add New Vendor', 'dokan'),
            tabs: {
                VendorAccountFields: {
                    label: this.__('Account Info', 'dokan'),
                    name: 'VendorAccountFields',
                    icon: 'dashicons dashicons-admin-users'
                },
                VendorAddressFields: {
                    label: this.__('Address', 'dokan'),
                    name: 'VendorAddressFields',
                    icon: 'dashicons dashicons-admin-home'
                },
                VendorPaymentFields: {
                    label: this.__('Payment Options', 'dokan'),
                    name: 'VendorPaymentFields',
                    icon: 'dashicons dashicons-money'
                }
            },
            currentTab: 'VendorAccountFields',
            store: {
                store_name: '',
                user_pass: '',
                store_url: '',
                user_login: '',
                user_email: '',
                user_nicename: '',
                notify_vendor: true,
                phone: '',
                banner: '',
                banner_id: '',
                gravatar: '',
                gravatar_id: '',
                social: {
                    fb: '',
                    gplus: '',
                    youtube: '',
                    twitter: '',
                    linkedin: '',
                    pinterest: '',
                    instagram: ''
                },
                payment: {
                    bank: {
                        ac_name: '',
                        ac_number: '',
                        bank_name: '',
                        bank_addr: '',
                        routing_number: '',
                        iban: '',
                        swift: ''
                    },
                    paypal: {
                        email: ''
                    }
                },
                address: {
                    street_1: '',
                    street_2: '',
                    city: '',
                    zip: '',
                    state: '',
                    country: ''
                }
            },
            requiredFields: ['store_name', 'user_login', 'user_email'],
            errors: [],
            storeAvailable: false,
            userNameAvailable: false,
            emailAvailable: false
        };
    },
    created: function created() {
        var _this = this;

        this.$root.$on('vendorInfoChecked', function (payload) {
            _this.storeAvailable = payload.storeAvailable;
            _this.userNameAvailable = payload.userNameAvailable;
            _this.emailAvailable = payload.emailAvailable;
        });
    },


    methods: {
        getId: function getId() {
            return this.$route.params.id;
        },
        showAlert: function showAlert($title, $des, $status) {
            this.$swal($title, $des, $status);
        },
        createVendor: function createVendor() {
            var _this2 = this;

            if (!this.formIsValid()) {
                return;
            }

            if ('VendorPaymentFields' === this.currentTab) {
                this.isLoading = true;

                dokan.api.post('/stores/', this.store).done(function (response) {
                    _this2.$root.$emit('vendorAdded', response);

                    _this2.$swal({
                        type: 'success',
                        title: _this2.__('Vendor Created', 'dokan'),
                        text: _this2.__('A vendor has been created successfully!', 'dokan'),
                        showCloseButton: true,
                        showCancelButton: true,
                        confirmButtonText: _this2.__('Add Another', 'dokan'),
                        cancelButtonText: _this2.__('Edit Vendor', 'dokan'),
                        focusConfirm: false
                    }).then(function (result) {
                        if (result.value) {
                            _this2.$root.$emit('addAnotherVendor');
                        } else if (result.dismiss === _this2.$swal.DismissReason.cancel) {
                            _this2.$router.push({ path: 'vendors/' + response.id, query: { edit: 'true' } });
                        }
                    });
                }).fail(function (response) {
                    _this2.showAlert(_this2.__(response.responseJSON.message, 'dokan'), '', 'error');
                }).always(function () {
                    _this2.$root.$emit('modalClosed');
                });
            }

            // move next tab
            this.currentTab = 'VendorPaymentFields' === this.currentTab ? 'VendorPaymentFields' : this.nextTab(this.tabs, this.currentTab);
        },
        nextTab: function nextTab(tabs, currentTab) {
            var keys = Object.keys(tabs);
            var nextIndex = keys.indexOf(currentTab) + 1;
            var nextTab = keys[nextIndex];

            return nextTab;
        },
        closeModal: function closeModal() {
            this.$root.$emit('modalClosed');
        },
        formIsValid: function formIsValid() {
            var _this3 = this;

            var requiredFields = this.requiredFields;
            var allFields = this.store;

            // empty the errors array on new form submit
            this.errors = [];

            requiredFields.forEach(function (field) {
                if (field in allFields && allFields[field].length < 1) {
                    _this3.errors.push(field);
                }
            });

            // if no error && store_slug & username is available, return true
            if (this.errors.length < 1 && this.storeAvailable && this.userNameAvailable && this.emailAvailable) {
                return true;
            }

            // go back to first tab, if there are errors
            this.currentTab = 'VendorAccountFields';

            return false;
        }
    }
});

/***/ }),
/* 8 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorAccountFields_vue__ = __webpack_require__(9);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4e4447de_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorAccountFields_vue__ = __webpack_require__(55);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorAccountFields_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4e4447de_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorAccountFields_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/VendorAccountFields.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4e4447de", Component.options)
  } else {
    hotAPI.reload("data-v-4e4447de", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 9 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_admin_components_UploadImage_vue__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_admin_components_passwordGenerator_vue__ = __webpack_require__(53);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




var debounce = dokan_get_lib('debounce');
var Switches = dokan_get_lib('Switches');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'VendorAccountFields',

    components: {
        Switches: Switches,
        UploadImage: __WEBPACK_IMPORTED_MODULE_0_admin_components_UploadImage_vue__["a" /* default */],
        PasswordGenerator: __WEBPACK_IMPORTED_MODULE_1_admin_components_passwordGenerator_vue__["a" /* default */]
    },

    props: {
        vendorInfo: {
            type: Object
        },
        errors: {
            type: Array,
            required: false
        }
    },

    data: function data() {
        return {
            showStoreUrl: true,
            showPassword: false,
            otherStoreUrl: null,
            banner: '',
            defaultUrl: dokan.urls.siteUrl + dokan.urls.storePrefix + '/',
            showButton: true,
            placeholderData: '',
            delay: 500,
            storeAvailable: null,
            userNameAvailable: null,
            emailAvailable: null,
            storeAvailabilityText: '',
            userNameAvailabilityText: '',
            emailAvailabilityText: '',
            getAccountFields: dokan.hooks.applyFilters('getVendorAccountFields', [])
        };
    },


    watch: {
        'vendorInfo.store_name': function vendorInfoStore_name(value) {
            this.showStoreUrl = true;
        },
        'vendorInfo.user_nicename': function vendorInfoUser_nicename(newValue) {
            if (typeof newValue !== 'undefined') {
                this.showStoreUrl = false;
                this.otherStoreUrl = this.defaultUrl + newValue.trim().split(' ').join('-');
                this.vendorInfo.user_nicename = newValue.split(' ').join('-');

                // check if the typed url is available
                this.checkStoreName();
            }
        },
        'vendorInfo.user_login': function vendorInfoUser_login(value) {
            this.checkUsername();
        },
        'vendorInfo.user_email': function vendorInfoUser_email(value) {
            this.checkEmail();
        }
    },

    computed: {
        storeUrl: function storeUrl() {
            var storeUrl = this.vendorInfo.store_name.trim().split(' ').join('-');
            this.vendorInfo.user_nicename = storeUrl;
            this.otherStoreUrl = this.defaultUrl + storeUrl;

            return this.defaultUrl + storeUrl;
        }
    },

    created: function created() {
        var _this = this;

        this.checkStoreName = debounce(this.checkStore, this.delay);
        this.checkUsername = debounce(this.searchUsername, this.delay);
        this.checkEmail = debounce(this.searchEmail, this.delay);
        this.$root.$on('passwordCancelled', function () {
            _this.showPassword = false;
        });
    },


    methods: {
        uploadBanner: function uploadBanner(image) {
            this.vendorInfo.banner_id = image.id;

            // hide button and footer text after uploading banner
            this.showButton = false;
        },
        uploadGravatar: function uploadGravatar(image) {
            this.vendorInfo.gravatar_id = image.id;
        },


        // getId function has been used to identify whether is it vendor edit page or not
        getId: function getId() {
            return this.$route.params.id;
        },
        onSelectBanner: function onSelectBanner(image) {
            this.banner = image.url;
            this.vendorInfo.banner_id = image.id;
        },
        getError: function getError(key) {
            var errors = this.errors;

            if (!errors || typeof errors === 'undefined') {
                return false;
            }

            if (errors.length < 1) {
                return false;
            }

            if (errors.includes(key)) {
                return key;
            }
        },
        checkStore: function checkStore() {
            var _this2 = this;

            var storeName = this.vendorInfo.user_nicename;

            if (!storeName) {
                return;
            }

            this.storeAvailabilityText = this.__('Searching...', 'dokan');

            dokan.api.get('/stores/check', {
                store_slug: storeName
            }).then(function (response) {
                if (response.available) {
                    _this2.storeAvailable = true;
                    _this2.$root.$emit('vendorInfoChecked', {
                        userNameAvailable: _this2.userNameAvailable,
                        storeAvailable: _this2.storeAvailable,
                        emailAvailable: _this2.emailAvailable
                    });
                    _this2.storeAvailabilityText = _this2.__('Available', 'dokan');
                } else {
                    _this2.storeAvailable = false;
                    _this2.$root.$emit('vendorInfoChecked', {
                        userNameAvailable: _this2.userNameAvailable,
                        storeAvailable: _this2.storeAvailable,
                        emailAvailable: _this2.emailAvailable
                    });
                    _this2.storeAvailabilityText = _this2.__('Not Available', 'dokan');
                }
            });
        },
        searchUsername: function searchUsername() {
            var _this3 = this;

            var userName = this.vendorInfo.user_login;

            if (!userName) {
                return;
            }

            this.userNameAvailabilityText = this.__('Searching...', 'dokan');

            dokan.api.get('/stores/check', {
                username: userName
            }).then(function (response) {
                if (response.available) {
                    _this3.userNameAvailable = true;
                    _this3.$root.$emit('vendorInfoChecked', {
                        userNameAvailable: _this3.userNameAvailable,
                        storeAvailable: _this3.storeAvailable,
                        emailAvailable: _this3.emailAvailable
                    });
                    _this3.userNameAvailabilityText = _this3.__('Available', 'dokan');
                } else {
                    _this3.userNameAvailable = false;
                    _this3.$root.$emit('vendorInfoChecked', {
                        userNameAvailable: _this3.userNameAvailable,
                        storeAvailable: _this3.storeAvailable,
                        emailAvailable: _this3.emailAvailable
                    });
                    _this3.userNameAvailabilityText = _this3.__('Not Available', 'dokan');
                }
            });
        },
        searchEmail: function searchEmail() {
            var _this4 = this;

            var userEmail = this.vendorInfo.user_email;

            if (!userEmail) {
                return;
            }

            this.emailAvailabilityText = this.__('Searching...', 'dokan');

            dokan.api.get('/stores/check', {
                user_email: userEmail
            }).then(function (response) {
                if (response.available) {
                    _this4.emailAvailable = true;
                    _this4.$root.$emit('vendorInfoChecked', {
                        userNameAvailable: _this4.userNameAvailable,
                        storeAvailable: _this4.storeAvailable,
                        emailAvailable: _this4.emailAvailable
                    });
                    _this4.emailAvailabilityText = _this4.__('Available', 'dokan');
                } else {
                    _this4.emailAvailable = false;
                    _this4.$root.$emit('vendorInfoChecked', {
                        userNameAvailable: _this4.userNameAvailable,
                        storeAvailable: _this4.storeAvailable,
                        emailAvailable: _this4.emailAvailable
                    });
                    _this4.emailAvailabilityText = response.message ? response.message : _this4.__('This email is already registered, please choose another one.', 'dokan');
                }
            });
        },
        setPassword: function setPassword(password) {
            this.showPassword = true;
            this.vendorInfo.user_pass = password;
        },
        sendEmail: function sendEmail(status, key) {
            if ('notify_vendor' !== key) {
                return;
            }

            this.vendorInfo.notify_vendor = status;
        },
        getUploadBannerText: function getUploadBannerText() {
            var width = dokan.store_banner_dimension.width;
            var height = dokan.store_banner_dimension.height;

            return this.__('Upload banner for your store. Banner size is (' + width + 'x' + height + ') pixels.', 'dokan');
        }
    }
});

/***/ }),
/* 10 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_UploadImage_vue__ = __webpack_require__(11);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_40b3524c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_UploadImage_vue__ = __webpack_require__(52);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(51)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_UploadImage_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_40b3524c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_UploadImage_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/UploadImage.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-40b3524c", Component.options)
  } else {
    hotAPI.reload("data-v-40b3524c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 11 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'UploadImage',

    inheritAttrs: false,

    props: {
        src: {
            default: dokan.urls.proAssetsUrl + '/images/store-pic.png'
        },
        showButton: {
            type: Boolean,
            default: false
        },
        buttonLabel: {
            type: String,
            default: 'Upload Image'
        },
        croppingWidth: {
            type: Number
        },
        croppingHeight: {
            type: Number
        }
    },

    data: function data() {
        return {
            image: {
                src: '',
                id: ''
            }
        };
    },


    methods: {
        uploadImage: function uploadImage() {
            this.openMediaManager(this.onSelectImage);
        },
        onSelectImage: function onSelectImage(image) {
            this.image.src = image.url;
            this.image.id = image.id;
            this.$emit('uploadedImage', this.image);
        },


        /**
         * Open Image Media Uploader
         *
         * @param  function callback
         *
         * @return callback
         */
        openMediaManager: function openMediaManager(callback) {
            var self = this;

            if (self.fileFrame) {
                self.fileFrame.open();
                return;
            }

            var fileStatesOptions = {
                library: wp.media.query(),
                multiple: false, // set it true for multiple image
                title: this.__('Select & Crop Image', 'dokan'),
                priority: 20,
                filterable: 'uploaded',
                autoSelect: true,
                suggestedWidth: 500,
                suggestedHeight: 300
            };

            var cropControl = {
                id: "control-id",
                params: {
                    width: this.croppingWidth ? parseInt(this.croppingWidth, 10) : parseInt(dokan.store_banner_dimension.width, 10),
                    height: this.croppingHeight ? parseInt(this.croppingHeight, 10) : parseInt(dokan.store_banner_dimension.height, 10),
                    flex_width: !!parseInt(dokan.store_banner_dimension['flex-width'], 10),
                    flex_height: !!parseInt(dokan.store_banner_dimension['flex-height'], 10)
                }
            };

            cropControl.mustBeCropped = function (flexW, flexH, dstW, dstH, imgW, imgH) {

                // If the width and height are both flexible
                // then the user does not need to crop the image.
                if (true === flexW && true === flexH) {
                    return false;
                }

                // If the width is flexible and the cropped image height matches the current image height,
                // then the user does not need to crop the image.
                if (true === flexW && dstH === imgH) {
                    return false;
                }

                // If the height is flexible and the cropped image width matches the current image width,
                // then the user does not need to crop the image.
                if (true === flexH && dstW === imgW) {
                    return false;
                }

                // If the cropped image width matches the current image width,
                // and the cropped image height matches the current image height
                // then the user does not need to crop the image.
                if (dstW === imgW && dstH === imgH) {
                    return false;
                }

                // If the destination width is equal to or greater than the cropped image width
                // then the user does not need to crop the image...
                if (imgW <= dstW) {
                    return false;
                }

                return true;
            };

            var fileStates = [new wp.media.controller.Library(fileStatesOptions), new wp.media.controller.CustomizeImageCropper({
                imgSelectOptions: self.calculateImageSelectOptions,
                control: cropControl
            })];

            var mediaOptions = {
                title: this.__('Select Image', 'dokan'),
                button: {
                    text: this.__('Select Image', 'dokan'),
                    close: false
                },
                multiple: false
            };

            mediaOptions.states = fileStates;

            self.fileFrame = wp.media(mediaOptions);

            self.fileFrame.on('select', function () {
                self.fileFrame.setState('cropper');
            });

            self.fileFrame.on('cropped', function (croppedImage) {
                callback(croppedImage);
                self.fileFrame = null;
            });

            self.fileFrame.on('skippedcrop', function () {
                var selection = self.fileFrame.state().get('selection');

                var files = selection.map(function (attachment) {
                    return attachment.toJSON();
                });

                var file = files.pop();

                callback(file);

                self.fileFrame = null;
            });

            self.fileFrame.on('close', function () {
                self.fileFrame = null;
            });

            self.fileFrame.on('ready', function () {
                self.fileFrame.uploader.options.uploader.params = {
                    type: 'dokan-vendor-option-media'
                };
            });

            self.fileFrame.open();
        },


        /**
         * Calculate image section options
         *
         * @param  object attachment
         * @param  object controller
         *
         * @return object
         */
        calculateImageSelectOptions: function calculateImageSelectOptions(attachment, controller) {
            var xInit = this.croppingWidth ? parseInt(this.croppingWidth, 10) : parseInt(dokan.store_banner_dimension.width, 10);
            var yInit = this.croppingHeight ? parseInt(this.croppingHeight, 10) : parseInt(dokan.store_banner_dimension.height, 10);
            var flexWidth = !!parseInt(dokan.store_banner_dimension['flex-width'], 10);
            var flexHeight = !!parseInt(dokan.store_banner_dimension['flex-height'], 10);

            var ratio = void 0,
                xImg = void 0,
                yImg = void 0,
                realHeight = void 0,
                realWidth = void 0,
                imgSelectOptions = void 0;

            realWidth = attachment.get('width');
            realHeight = attachment.get('height');

            var control = controller.get('control');

            controller.set('canSkipCrop', !control.mustBeCropped(flexWidth, flexHeight, xInit, yInit, realWidth, realHeight));

            ratio = xInit / yInit;
            xImg = realWidth;
            yImg = realHeight;

            if (xImg / yImg > ratio) {
                yInit = yImg;
                xInit = yInit * ratio;
            } else {
                xInit = xImg;
                yInit = xInit / ratio;
            }

            imgSelectOptions = {
                handles: true,
                keys: true,
                instance: true,
                persistent: true,
                imageWidth: realWidth,
                imageHeight: realHeight,
                x1: 0,
                y1: 0,
                x2: xInit,
                y2: yInit
            };

            if (flexHeight === false && flexWidth === false) {
                imgSelectOptions.aspectRatio = xInit + ':' + yInit;
            }
            if (flexHeight === false) {
                imgSelectOptions.maxHeight = yInit;
            }
            if (flexWidth === false) {
                imgSelectOptions.maxWidth = xInit;
            }

            return imgSelectOptions;
        }
    }
});

/***/ }),
/* 12 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'PasswordGenerate',

    props: {
        title: {
            type: String,
            default: 'Generate Password'
        },

        cancelTitle: {
            type: String,
            default: 'Cancel'
        },

        regenrateTitle: {
            type: String,
            default: 'Regenrate'
        },

        length: {
            type: Number,
            default: 25
        }
    },

    data: function data() {
        return {
            password: '',
            hideGenerateButton: false,
            showCancelButton: false
        };
    },


    methods: {
        generatePassword: function generatePassword() {
            this.password = this.makePassword(this.length);

            this.$emit('passwordGenerated', this.password);

            this.hideGenerateButton = true;
            this.showCancelButton = true;
        },
        makePassword: function makePassword() {
            var len = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 25;

            var lowerCaseChars = 'abcdefghijklmnopqurstuvwxyz';
            var upperCaseChars = 'ABCDEFGHIJKLMNOPQURSTUVWXYZ';
            var specialChars = '!@#$%^&*()';
            var randomChars = '';

            for (var i = 0; i <= len; i++) {
                var mixUp = lowerCaseChars[Math.floor(Math.random() * len)] + upperCaseChars[Math.floor(Math.random() * 10)] + specialChars[Math.floor(Math.random() * specialChars.length)];
                randomChars += mixUp;
            }

            return randomChars.slice(-len);
        },
        cancelButton: function cancelButton() {
            this.hideGenerateButton = false;
            this.showCancelButton = false;

            this.$root.$emit('passwordCancelled');
        },
        regenratePassword: function regenratePassword() {
            this.password = this.makePassword(this.length);
            this.$emit('passwordGenerated', this.password);
        }
    }
});

/***/ }),
/* 13 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorAddressFields_vue__ = __webpack_require__(14);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4ea3e636_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorAddressFields_vue__ = __webpack_require__(56);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorAddressFields_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4ea3e636_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorAddressFields_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/VendorAddressFields.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4ea3e636", Component.options)
  } else {
    hotAPI.reload("data-v-4ea3e636", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 14 */
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

var Multiselect = dokan_get_lib('Multiselect');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'VendorAddressFields',

    components: {
        Multiselect: Multiselect
    },

    props: {
        vendorInfo: {
            type: Object
        }
    },

    data: function data() {
        return {
            countries: [],
            states: [],
            selectedCountry: {},
            selectedState: {},
            getAddressFields: dokan.hooks.applyFilters('getVendorAddressFields', [])
        };
    },


    computed: {
        selectedCode: function selectedCode() {
            // let selected = this.selectedCountry;
            var selected = this.vendorInfo.address.country;

            if ('' !== selected) {
                return selected;
            }

            return [];
        }
    },

    created: function created() {
        this.countries = this.transformCountries(dokan.countries);
        this.states = dokan.states;

        var savedCountry = this.vendorInfo.address.country;
        var savedState = this.vendorInfo.address.state;

        if ('' !== savedCountry) {
            this.selectedCountry = {
                name: this.getCountryFromCountryCode(savedCountry),
                code: savedCountry
            };

            this.selectedState = {
                name: this.getStateFromStateCode(savedState, savedCountry),
                code: savedState
            };
        }
    },


    methods: {
        transformCountries: function transformCountries(countryObject) {
            var countries = [];

            for (var key in countryObject) {
                countries.push({
                    name: countryObject[key],
                    code: key
                });
            }

            return countries;
        },
        getCountryFromCountryCode: function getCountryFromCountryCode(countryCode) {
            if ('' === countryCode) {
                return;
            }

            return dokan.countries[countryCode];
        },
        getStateFromStateCode: function getStateFromStateCode(stateCode, countryCode) {
            if ('' === stateCode) {
                return;
            }

            var states = dokan.states[countryCode];
            var state = states && states[stateCode];

            return typeof state !== 'undefined' ? state : [];
        },
        getStatesFromCountryCode: function getStatesFromCountryCode(countryCode) {
            if ('' === countryCode) {
                return;
            }

            var states = [];
            var statesObject = this.states;

            for (var state in statesObject) {
                if (state !== countryCode) {
                    continue;
                }

                if (statesObject[state] && statesObject[state].length < 1) {
                    continue;
                }

                for (var name in statesObject[state]) {
                    states.push({
                        name: statesObject[state][name],
                        code: name
                    });
                }
            }

            return states;
        },
        saveCountry: function saveCountry(value) {
            if (!value) return;

            // if reset default state values
            this.vendorInfo.address.state = null;
            this.selectedState = {};

            this.vendorInfo.address.country = value.code;
        },
        saveState: function saveState(value) {
            if (!value) return;

            this.vendorInfo.address.state = value.code;
        }
    }
});

/***/ }),
/* 15 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorPaymentFields_vue__ = __webpack_require__(16);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5203b477_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorPaymentFields_vue__ = __webpack_require__(58);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(57)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorPaymentFields_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5203b477_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorPaymentFields_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/VendorPaymentFields.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5203b477", Component.options)
  } else {
    hotAPI.reload("data-v-5203b477", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 16 */
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

var Switches = dokan_get_lib('Switches');
var Multiselect = dokan_get_lib('Multiselect');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'VendorPaymentFields',

    components: {
        Switches: Switches,
        Multiselect: Multiselect
    },

    props: {
        vendorInfo: {
            type: Object
        }
    },

    data: function data() {
        return {
            enabled: false,
            trusted: false,
            featured: false,
            commissionTypes: ['Flat', 'Percentage'],
            selectedCommissionType: '',
            getBankFields: dokan.hooks.applyFilters('getVendorBankFields', []),
            getPyamentFields: dokan.hooks.applyFilters('AfterPyamentFields', [])
        };
    },
    created: function created() {
        if (this.vendorInfo.enabled) {
            this.enabled = true;
            this.vendorInfo.enabled = true;
        }

        if (this.vendorInfo.trusted) {
            this.trusted = true;
            this.vendorInfo.trusted = true;
        }

        if (this.vendorInfo.featured) {
            this.featured = true;
            this.vendorInfo.featured = true;
        }

        var commissionType = this.vendorInfo.admin_commission_type;

        if (commissionType) {
            this.selectedCommissionType = commissionType.charAt(0).toUpperCase() + commissionType.slice(1);
        }
    },


    methods: {
        setValue: function setValue(status, key) {
            if ('enabled' === key) {
                if (status) {
                    this.vendorInfo.enabled = true;
                } else {
                    this.vendorInfo.enabled = false;
                }
            }

            if ('trusted' === key) {
                if (status) {
                    this.vendorInfo.trusted = true;
                } else {
                    this.vendorInfo.trusted = false;
                }
            }

            if ('featured' === key) {
                if (status) {
                    this.vendorInfo.featured = true;
                } else {
                    this.vendorInfo.featured = false;
                }
            }
        },
        getId: function getId() {
            return this.$route.params.id;
        },
        saveCommissionType: function saveCommissionType(value) {
            if (!value) {
                return;
            }

            this.vendorInfo.admin_commission_type = value.toLowerCase();
        }
    }

});

/***/ }),
/* 17 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_admin_components_UploadImage_vue__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_admin_components_VendorSocialFields_vue__ = __webpack_require__(63);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_admin_components_VendorAccountFields_vue__ = __webpack_require__(8);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_admin_components_VendorPaymentFields_vue__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_admin_components_VendorAddressFields_vue__ = __webpack_require__(13);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//







var ContentLoading = dokan_get_lib('ContentLoading');
var Modal = dokan_get_lib('Modal');
var Currency = dokan_get_lib('Currency');

var VclFacebook = ContentLoading.VclFacebook;
var VclTwitch = ContentLoading.VclTwitch;

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'VendorSingle',

    components: {
        VclFacebook: VclFacebook,
        VclTwitch: VclTwitch,
        Modal: Modal,
        Currency: Currency,
        UploadImage: __WEBPACK_IMPORTED_MODULE_0_admin_components_UploadImage_vue__["a" /* default */],
        VendorPaymentFields: __WEBPACK_IMPORTED_MODULE_3_admin_components_VendorPaymentFields_vue__["a" /* default */],
        VendorSocialFields: __WEBPACK_IMPORTED_MODULE_1_admin_components_VendorSocialFields_vue__["a" /* default */],
        VendorAccountFields: __WEBPACK_IMPORTED_MODULE_2_admin_components_VendorAccountFields_vue__["a" /* default */],
        VendorAddressFields: __WEBPACK_IMPORTED_MODULE_4_admin_components_VendorAddressFields_vue__["a" /* default */]
    },

    data: function data() {
        return {
            showDialog: false,
            stats: null,
            mail: {
                subject: '',
                body: ''
            },
            editMode: false,
            isUpdating: false,
            categories: [],
            isCategoryMultiple: false,
            editingCategories: false,
            store: {
                store_name: '',
                user_pass: '',
                store_url: '',
                user_email: '',
                user_nicename: '',
                phone: '',
                banner: '',
                banner_id: '',
                gravatar: '',
                gravatar_id: '',
                social: {
                    fb: '',
                    gplus: '',
                    youtube: '',
                    twitter: '',
                    linkedin: '',
                    pinterest: '',
                    instagram: ''
                },
                payment: {
                    bank: {
                        ac_name: '',
                        ac_number: '',
                        bank_name: '',
                        bank_addr: '',
                        routing_number: '',
                        iban: '',
                        swift: ''
                    },
                    paypal: {
                        email: ''
                    }
                },
                address: {
                    street_1: '',
                    street_2: '',
                    city: '',
                    zip: '',
                    state: '',
                    country: ''
                }
            },
            fakeStore: {},
            showStoreUrl: true,
            otherStoreUrl: null
        };
    },


    computed: {
        id: function id() {
            return this.$route.params.id;
        },
        mailTo: function mailTo() {
            return this.store.store_name + ' <' + this.store.email + '>';
        },
        hasBank: function hasBank() {
            if (this.store.payment.hasOwnProperty('bank') && !_.isEmpty(this.store.payment.bank)) {
                return true;
            }

            return false;
        },
        categoriesFlattened: function categoriesFlattened() {
            var categories = {};
            var i = 0;

            for (i = 0; i < this.categories.length; i++) {
                var category = this.categories[i];

                categories[category.id] = {
                    id: category.id,
                    name: category.name,
                    slug: category.slug
                };
            }

            return categories;
        },


        storeCategories: {
            get: function get() {
                var self = this;

                if (!self.isCategoryMultiple) {
                    if (self.store.categories.length) {
                        return self.store.categories[0].id;
                    } else {
                        return null;
                    }
                } else {
                    return self.store.categories.map(function (category) {
                        return category.id;
                    });
                }
            },
            set: function set(categories) {
                var self = this;

                if ($.isArray(categories)) {
                    self.store.categories = categories.map(function (category_id) {
                        return self.categoriesFlattened[category_id];
                    });
                } else {
                    self.store.categories = [self.categoriesFlattened[categories]];
                }
            }
        },

        getEearningRate: function getEearningRate() {
            if (this.stats.others.commission_type == 'percentage') {
                return this.stats.others.commission_rate + '%';
            } else {
                return accounting.formatMoney(this.stats.others.commission_rate);
            }
        },
        saveBtn: function saveBtn() {
            return this.isUpdating ? this.__('Saving...', 'dokan') : this.__('Save Changes');
        }
    },

    watch: {
        '$route.params.id': function $routeParamsId() {
            this.fetch();
            this.fetchStats();
        }
    },

    created: function created() {
        this.fetch();
        this.fetchStats();

        if (this.$route.query.edit && this.$route.query.edit === 'true') {
            this.editMode = true;
        }
    },


    methods: {
        fetch: function fetch() {
            var self = this;

            dokan.api.get('/stores/' + self.id).done(function (response) {
                Object.assign(self.fakeStore, self.store);
                Object.assign(self.store, response);
                self.transformer(response);
            });

            dokan.api.get('/store-categories').done(function (response, status, xhr) {
                self.categories = response;
                self.isCategoryMultiple = 'multiple' === xhr.getResponseHeader('X-WP-Store-Category-Type');
            });
        },


        // map response props to store props
        transformer: function transformer(response) {
            for (var res in response) {

                if (Array.isArray(response[res]) && 0 === response[res].length) {
                    this.store[res] = this.fakeStore[res];
                }
            }

            // set default payment object for v-model
            if ('payment' in response && response.payment.bank && response.payment.bank.length < 1) {
                this.store.payment = this.fakeStore.payment;
            }

            if ('email' in response) {
                this.store.user_email = response.email;
            }

            if ('shop_url' in response) {
                this.store.user_nicename = this.getStoreName(response.shop_url);
            }
        },


        // get sotre name from url
        getStoreName: function getStoreName(url) {
            var storeName = url.split('/').filter(function (value) {
                return value !== '';
            });

            return storeName[storeName.length - 1];
        },
        fetchStats: function fetchStats() {
            var _this = this;

            dokan.api.get('/stores/' + this.id + '/stats').done(function (response) {
                return _this.stats = response;
            });
        },
        isSocialActive: function isSocialActive(profile) {
            if (this.store.social.hasOwnProperty(profile) && this.store.social[profile] !== false) {
                return true;
            }

            return false;
        },
        hasPaymentEmail: function hasPaymentEmail(method) {
            if (this.store.payment.hasOwnProperty(method) && this.store.payment[method].email !== false) {
                return true;
            }

            return false;
        },
        messageDialog: function messageDialog() {
            this.showDialog = true;
        },
        sendEmail: function sendEmail() {
            var _this2 = this;

            this.showDialog = false;

            dokan.api.post('/stores/' + this.id + '/email', {
                subject: this.mail.subject,
                body: this.mail.body
            }).done(function (response) {
                _this2.$notify({
                    title: _this2.__('Success!', 'dokan'),
                    type: 'success',
                    text: _this2.__('Email has been sent successfully.', 'dokan')
                });
            });

            this.mail = {
                subject: '',
                body: ''
            };
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
        productUrl: function productUrl() {
            return dokan.urls.adminRoot + 'edit.php?post_type=product&author=' + this.store.id;
        },
        ordersUrl: function ordersUrl() {
            return dokan.urls.adminRoot + 'edit.php?post_type=shop_order&vendor_id=' + this.store.id;
        },
        editUrl: function editUrl() {
            return dokan.urls.adminRoot + 'user-edit.php?user_id=' + this.store.id;
        },
        updateStore: function updateStore() {
            var _this3 = this;

            var self = this;

            self.isUpdating = true;

            dokan.api.put('/stores/' + self.store.id, self.store).done(function (response) {
                self.editMode = false;
                self.store = response;
                self.isUpdating = false;
                self.editingCategories = false;

                _this3.updateCommissonRate();

                _this3.showAlert(_this3.__('Vendor Updated', 'dokan'), _this3.__('Vendor Updated Successfully!', 'dokan'), 'success');
            }).fail(function (response) {
                _this3.showAlert(_this3.__(response.responseJSON.message, 'dokan'), '', 'error');
            }).always(function () {
                _this3.$router.push({
                    query: { edit: false }
                });
            });
        },
        uploadGravatar: function uploadGravatar(image) {
            this.store.gravatar_id = image.id;
        },
        uploadBanner: function uploadBanner(image) {
            this.store.banner_id = image.id;
        },
        showAlert: function showAlert($title, $des, $status) {
            this.$swal($title, $des, $status);
        },
        getDefaultPic: function getDefaultPic() {
            return dokan.urls.proAssetsUrl + '/images/store-pic.png';
        },
        updateCommissonRate: function updateCommissonRate() {
            var admin_commission = this.store.admin_commission;

            if (this.store.admin_commission_type === 'percentage') {
                this.stats.others.commission_rate = 100 - admin_commission;
            } else {
                this.stats.others.commission_rate = admin_commission;
            }

            this.stats.others.commission_type = this.store.admin_commission_type;
        }
    }
});
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(1)))

/***/ }),
/* 18 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'VendorSocialFields',

    props: {
        vendorInfo: {
            type: Object
        }
    },

    data: function data() {
        return {
            getSocialFields: dokan.hooks.applyFilters('getVendorSocialFields', [])
        };
    }
});

/***/ }),
/* 19 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

var LazyInput = dokan_get_lib('LazyInput');
var ListTable = dokan_get_lib('ListTable');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'StoreCategoriesIndex',

    components: {
        LazyInput: LazyInput,
        ListTable: ListTable
    },

    data: function data() {
        return {
            apiHandler: {
                abort: function abort() {
                    //
                }
            },
            isCreating: false,
            category: {
                name: '',
                slug: '',
                description: ''
            },
            defaultCategory: 0,
            search: '',
            categories: [],
            showCb: false,
            totalItems: 0,
            perPage: 20,
            totalPages: 1,
            loading: false,
            notFound: this.__('No category found', 'dokan'),
            columns: {
                name: {
                    label: this.__('Name', 'dokan'),
                    sortable: true
                },

                description: {
                    label: this.__('Description', 'dokan'),
                    sortable: false
                },

                slug: {
                    label: this.__('Slug', 'dokan'),
                    sortable: true
                },

                count: {
                    label: this.__('Count', 'dokan'),
                    sortable: true
                }
            },
            actionColumn: 'name',
            actions: [{
                key: 'edit',
                label: this.__('Edit', 'dokan')
            }, {
                key: 'delete',
                label: this.__('Delete', 'dokan')
            }, {
                key: 'set_as_default',
                label: this.__('Set as default', 'dokan')
            }],
            bulkActions: [],
            sortBy: 'name',
            sortOrder: 'asc'
        };
    },


    computed: {
        currentPage: function currentPage() {
            var page = this.$route.query.page || 1;

            return parseInt(page);
        }
    },

    created: function created() {
        if (this.$router.currentRoute.query.search) {
            this.search = this.$router.currentRoute.query.search;
        }

        this.fetchCategories();
    },


    watch: {
        '$route.query': 'fetchCategories',
        search: 'onChangeSearch'
    },

    methods: {
        updateHeaderParams: function updateHeaderParams(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
            this.defaultCategory = parseInt(xhr.getResponseHeader('X-WP-Default-Category'));
        },
        addCategory: function addCategory() {
            var self = this;

            self.isCreating = true;

            dokan.api.post('/store-categories', self.category).done(function () {
                self.category = {
                    name: '',
                    slug: '',
                    description: ''
                };

                self.fetchCategories();
            }).always(function () {
                self.isCreating = false;
            }).fail(function (jqXHR) {
                var message = jqXHR.responseJSON.message;
                alert(message);
            });
        },
        fetchCategories: function fetchCategories() {
            var self = this;

            self.apiHandler.abort();

            self.loading = true;

            var query = {
                per_page: self.perPage,
                page: self.currentPage,
                status: self.currentStatus,
                orderby: self.sortBy,
                order: self.sortOrder
            };

            if (self.search) {
                query.search = self.search;
            }

            self.apiHandler = dokan.api.get('/store-categories', query).done(function (response, status, xhr) {
                self.categories = response;
                self.updateHeaderParams(xhr);
            }).always(function () {
                self.loading = false;
            });
        },
        deleteCategory: function deleteCategory(category) {
            if (confirm(this.__('Are you sure you want to delete this category?', 'dokan'))) {
                var self = this;

                self.loading = true;

                dokan.api.delete(self.$route.path + '/' + category.id + '?force=true').done(function (response) {
                    self.fetchCategories();
                }).fail(function (jqXHR) {
                    self.loading = false;
                    var message = jqXHR.responseJSON.message;
                    alert(message);
                });
            }
        },
        onChangeSearch: function onChangeSearch(search) {
            var query = $.extend(true, {}, this.$router.currentRoute.query);

            if (search) {
                query.search = search;
            } else {
                delete query.search;
            }

            this.$router.replace({
                query: query
            });
        },
        goToPage: function goToPage(page) {
            this.$router.push({
                name: 'StoreCategoriesIndex',
                query: {
                    status: this.currentStatus,
                    page: page
                }
            });
        },
        makeDefaultCategory: function makeDefaultCategory(category) {
            var self = this;

            self.loading = true;

            dokan.api.put(self.$route.path + '/default-category', category).done(function (response) {
                self.fetchCategories();
            }).fail(function (jqXHR) {
                self.loading = false;
                var message = jqXHR.responseJSON.message;
                alert(message);
            });
        },
        columnName: function columnName(row) {
            var name = row.name ? row.name : __('(no name)', 'dokan');

            if (row.id === this.defaultCategory) {
                name += this.sprintf('<span class="default-category"> - %s</span>', this.__('Default', 'dokan'));
            }

            return name;
        }
    }
});
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(1)))

/***/ }),
/* 20 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'StoreCategoriesShow',

    data: function data() {
        return {
            category: {},
            loading: true
        };
    },
    created: function created() {
        this.fetchCategory();
    },


    methods: {
        fetchCategory: function fetchCategory() {
            var self = this;

            self.loading = true;

            dokan.api.get(self.$route.path).done(function (response) {
                self.category = response;
            }).always(function () {
                self.loading = false;
            });
        },
        updateCategory: function updateCategory() {
            var self = this;

            self.loading = true;

            dokan.api.put(self.$route.path, self.category).done(function (response) {
                self.category = response;
            }).always(function () {
                self.loading = false;
            }).fail(function (jqXHR) {
                var message = jqXHR.responseJSON.message;
                alert(message);
            });
        },
        deleteCategory: function deleteCategory() {
            var _this = this;

            if (confirm(this.__('Are you sure you want to delete this category?', 'dokan'))) {
                var self = this;

                self.loading = true;

                dokan.api.delete(self.$route.path + '?force=true').done(function (response) {
                    _this.$router.push({
                        name: 'StoreCategoriesIndex'
                    });
                }).always(function () {
                    self.loading = false;
                }).fail(function (jqXHR) {
                    var message = jqXHR.responseJSON.message;
                    alert(message);
                });
            }
        }
    }
});

/***/ }),
/* 21 */
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

var ListTable = dokan_get_lib('ListTable');
var Loading = dokan_get_lib('Loading');
var Switches = dokan_get_lib('Switches');

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'Modules',

    data: function data() {
        return {
            search: '',
            isLoaded: false,
            currentView: '',
            modules: [],
            count: {},
            column: {
                'name': {
                    label: 'Module Name',
                    sortable: true
                },
                'description': {
                    label: 'Description'
                },
                'active': {
                    label: 'Status'
                }
            },

            filterMenu: [{
                title: 'All',
                route: {
                    name: 'Modules',
                    params: {}
                }
            }, {
                title: 'Active',
                route: {
                    name: 'ModulesStatus',
                    params: {
                        status: 'active'
                    }
                }
            }, {
                title: 'Inactive',
                route: {
                    name: 'ModulesStatus',
                    params: {
                        status: 'inactive'
                    }
                }
            }]
        };
    },


    components: {
        Loading: Loading,
        Switches: Switches,
        ListTable: ListTable
    },

    computed: {
        currentStatus: function currentStatus() {
            return this.$route.params.status || 'all';
        },
        filteredModules: function filteredModules() {
            var self = this;

            var data = this.modules.filter(function (module) {
                return module.name.toLowerCase().indexOf(self.search.toLowerCase()) >= 0;
            });

            return data;
        },
        sortBy: function sortBy() {
            return this.$route.query.orderby || 'name';
        },
        sortOrder: function sortOrder() {
            return this.$route.query.order || 'desc';
        }
    },

    watch: {
        '$route.query.order': function $routeQueryOrder() {
            this.fetchModuels();
        },
        '$route.params.status': function $routeParamsStatus() {
            this.fetchModuels();
        }
    },

    methods: {
        changeView: function changeView(view) {
            var activetab = '';
            this.currentView = view;

            if (typeof localStorage != 'undefined') {
                localStorage.setItem("activeview", this.currentView);
            }
        },
        fetchModuels: function fetchModuels() {
            var _this = this;

            this.isLoaded = false;

            dokan.api.get('/admin/modules?status=' + this.currentStatus + '&orderby=' + this.sortBy + '&order=' + this.sortOrder).done(function (response, status, xhr) {
                _this.modules = response;
                _this.isLoaded = true;
            });
        },
        sortCallback: function sortCallback(column, order) {
            var currentRoute = this.$router.currentRoute;

            var route = {
                name: currentRoute.name,
                params: {},
                query: {
                    orderby: column,
                    order: order
                }
            };

            if (currentRoute.params.status) {
                route.params.status = currentRoute.params.status;
            }

            this.$router.push(route);
        },
        onSwitch: function onSwitch(status, moduleSlug) {
            var _this2 = this;

            var moduleData = _.findWhere(this.modules, { slug: moduleSlug });

            if (status) {
                // Need to activate
                var message = moduleData.name + this.__('is successfully activated', 'dokan');

                dokan.api.put('/admin/modules/activate', {
                    module: [moduleSlug]
                }).done(function (response) {
                    _this2.$notify({
                        title: 'Success!',
                        type: 'success',
                        text: message
                    });

                    _this2.toggleActivation = false;
                    location.reload();
                });
            } else {
                // Need to deactivate
                var message = moduleData.name + this.__('is successfully deactivated', 'dokan');

                dokan.api.put('/admin/modules/deactivate', {
                    module: [moduleSlug]
                }).done(function (response) {
                    _this2.$notify({
                        title: 'Success!',
                        type: 'success',
                        text: message
                    });

                    location.reload();
                });
            }
        },
        onBulkAction: function onBulkAction(action, items) {
            var _this3 = this;

            var message = 'activate' == action ? this.__('All selected modules are successfully activated', 'dokan') : this.__('All selected modules are successfully deactivated', 'dokan');

            dokan.api.put('/admin/modules/' + action, {
                module: items
            }).done(function (response) {
                _this3.fetchModuels();
                _this3.$notify({
                    title: 'Success!',
                    type: 'success',
                    text: message
                });
            });
        },
        filterMenuClass: function filterMenuClass(route) {
            var className = '';
            var currentRoute = this.$router.currentRoute;

            var routeParams = jQuery.extend(true, {}, route.params);
            var currentRouteParams = jQuery.extend(true, {}, currentRoute.params);

            if (route.name === currentRoute.name && _.isEqual(routeParams, currentRouteParams)) {
                className = 'active';
            }

            return className;
        }
    },

    created: function created() {
        if (typeof localStorage != 'undefined') {
            this.currentView = localStorage.getItem("activeview") ? localStorage.getItem("activeview") : 'grid';
        } else {
            this.currentView = 'grid';
        }

        this.fetchModuels();
    }
});

/***/ }),
/* 22 */
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

var ListTable = dokan_get_lib('ListTable');
var Modal = dokan_get_lib('Modal');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'Announcement',

    components: {
        ListTable: ListTable,
        Modal: Modal
    },

    data: function data() {
        return {
            requests: [],
            loading: false,

            status: {
                'publish': this.__('Published', 'dokan'),
                'pending': this.__('Pending', 'dokan'),
                'draft': this.__('Draft', 'dokan'),
                'trash': this.__('Trash', 'dokan')
            },

            counts: {
                all: 0,
                publish: 0,
                draft: 0,
                pending: 0,
                trash: 0
            },
            notFound: this.__('No announcement found.', 'dokan'),
            totalPages: 1,
            perPage: 10,
            totalItems: 0,

            showCb: true,

            columns: {
                'title': { label: this.__('Title', 'dokan') },
                'content': { label: this.__('Content', 'dokan') },
                'send_to': { label: this.__('Sent To', 'dokan') },
                'status': { label: this.__('Status', 'dokan') },
                'created_at': { label: this.__('Created Date', 'dokan') }
            },

            actionColumn: 'title',
            actions: [{
                key: 'edit',
                label: this.__('Edit', 'dokan')
            }, {
                key: 'trash',
                label: this.__('Trash', 'dokan')
            }, {
                key: 'delete',
                label: this.__('Permanent Delete', 'dokan')
            }, {
                key: 'restore',
                label: this.__('Restore', 'dokan')
            }],
            showDialog: false,
            modalContent: '',
            modalTitle: ''
        };
    },


    watch: {
        '$route.query.status': function $routeQueryStatus() {
            this.fetchAll();
        },
        '$route.query.page': function $routeQueryPage() {
            this.fetchAll();
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
        bulkActions: function bulkActions() {
            if ('trash' == this.$route.query.status) {
                return [{
                    key: 'delete',
                    label: this.__('Permanent Delete', 'dokan')
                }, {
                    key: 'restore',
                    label: this.__('Restore', 'dokan')
                }];
            } else {
                return [{
                    key: 'trash',
                    label: this.__('Move in Trash', 'dokan')
                }];
            }
        }
    },

    methods: {
        updatedCounts: function updatedCounts(xhr) {
            this.counts.all = parseInt(xhr.getResponseHeader('X-Status-All'));
            this.counts.publish = parseInt(xhr.getResponseHeader('X-Status-Publish'));
            this.counts.pending = parseInt(xhr.getResponseHeader('X-Status-Pending'));
            this.counts.draft = parseInt(xhr.getResponseHeader('X-Status-Draft'));
            this.counts.trash = parseInt(xhr.getResponseHeader('X-Status-Trash'));
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        fetchAll: function fetchAll() {
            var _this = this;

            this.loading = true;

            dokan.api.get('/announcement?per_page=' + this.perPage + '&page=' + this.currentPage + '&status=' + this.currentStatus).done(function (response, status, xhr) {
                _this.requests = response;
                _this.loading = false;

                _this.updatedCounts(xhr);
                _this.updatePagination(xhr);
            });
        },
        showContent: function showContent(row) {
            this.modalTitle = row.title;
            this.modalContent = row.content;
            this.showDialog = true;
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
        editUrl: function editUrl(id) {
            return dokan.urls.adminRoot + 'admin.php?page=dokan#/announcement/' + id + '/edit';
        },
        goToPage: function goToPage(page) {
            this.$router.push({
                name: 'Announcement',
                query: {
                    status: this.currentStatus,
                    page: page
                }
            });
        },
        onActionClick: function onActionClick(action, row) {},
        rowAction: function rowAction(action, data) {
            var _this2 = this;

            if (!data.row.id) {
                alert(this.__('No data found', 'dokan'));
                return;
            }

            if ('trash' === action || 'delete' === action) {
                this.loading = true;

                var isPermanentDelete = 'delete' === action ? '?force=true' : '';

                dokan.api.delete('/announcement/' + data.row.id + isPermanentDelete).done(function (response, status, xhr) {
                    _this2.fetchAll();
                    _this2.loading = false;
                });
            }

            if ('restore' === action) {
                this.loading = true;
                var jsonData = {};

                dokan.api.put('/announcement/' + data.row.id + '/restore').done(function (response, status, xhr) {
                    _this2.fetchAll();
                    _this2.loading = false;
                }).error(function (response, status, xhr) {
                    console.log(response);
                });
            }
        },
        onBulkAction: function onBulkAction(action, items) {
            var _this3 = this;

            if ('trash' === action) {
                this.loading = true;

                var jsonData = {};
                jsonData.trash = items;

                dokan.api.put('/announcement/batch', jsonData).done(function (response, status, xhr) {
                    _this3.fetchAll();
                    _this3.loading = false;
                });
            }

            if ('delete' === action) {
                this.loading = true;

                var _jsonData = {};
                _jsonData.delete = items;

                dokan.api.put('/announcement/batch', _jsonData).done(function (response, status, xhr) {
                    _this3.fetchAll();
                    _this3.loading = false;
                });
            }

            if ('restore' === action) {
                this.loading = true;
                var _jsonData2 = {};
                _jsonData2.restore = items;

                dokan.api.put('/announcement/batch', _jsonData2).done(function (response, status, xhr) {
                    _this3.fetchAll();
                    _this3.loading = false;
                });
            }
        }
    },

    created: function created() {
        this.fetchAll();
    }
});

/***/ }),
/* 23 */
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

var TextEditor = dokan_get_lib('TextEditor');
var Postbox = dokan_get_lib('Postbox');
var Multiselect = dokan_get_lib('Multiselect');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'NewAnnouncement',

    components: {
        Postbox: Postbox,
        TextEditor: TextEditor,
        Multiselect: Multiselect
    },

    data: function data() {
        return {
            announcement: {
                title: '',
                content: '',
                status: 'publish',
                sender_type: 'all_seller',
                sender_ids: []
            },
            message: '',
            isSaved: false,
            loadSpinner: false,
            isLoading: false,
            draftBtnLabel: this.__('Save as Draft', 'dokan'),
            publishBtnLabel: this.__('Send', 'dokan'),
            vendors: []
        };
    },


    computed: {
        submitBtnLabel: function submitBtnLabel() {
            return this.statusesLabel[this.announcement.status];
        }
    },

    methods: {
        limitText: function limitText(count) {
            return 'and ' + count + ' other vendors';
        },
        asyncFind: function asyncFind(query) {
            var _this = this;

            this.isLoading = true;
            dokan.api.get('/stores' + '?search=' + query).done(function (response) {
                _this.isLoading = false;
                _this.vendors = _.map(response, function (item) {
                    return {
                        id: item.id,
                        name: item.store_name + '( ' + item.email + ' )'
                    };
                });
            });
        },
        clearAll: function clearAll() {
            this.announcement.sender_ids = [];
        },
        createAnnouncement: function createAnnouncement(status) {
            var _this2 = this;

            var self = this;
            this.loadSpinner = true;
            var jsonData = {};
            jsonData = jQuery.extend({}, this.announcement);

            jsonData.sender_ids = _.pluck(jsonData.sender_ids, 'id');
            jsonData.status = status;

            dokan.api.post('/announcement', jsonData).done(function (response) {
                _this2.isSaved = false;
                _this2.loadSpinner = false;

                if ('draft' == status) {
                    _this2.$router.push({
                        name: 'EditAnnouncement',
                        params: { id: response.id }
                    });
                } else {
                    _this2.$router.push({
                        name: 'Announcement'
                    });
                }
            }).error(function (response) {
                _this2.isSaved = false;
                alert(response.responseJSON.message);
            });
        }
    }

});

/***/ }),
/* 24 */
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

var TextEditor = dokan_get_lib('TextEditor');
var Postbox = dokan_get_lib('Postbox');
var Multiselect = dokan_get_lib('Multiselect');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'EditAnnouncement',

    components: {
        Postbox: Postbox,
        TextEditor: TextEditor,
        Multiselect: Multiselect
    },

    data: function data() {
        return {
            announcement: {},
            loadSpinner: false,
            isSaved: false,
            isUpdated: false,
            isLoading: false,
            draftBtnLabel: this.__('Save as Draft', 'dokan'),
            publishBtnLabel: this.__('Send', 'dokan'),
            message: '',
            vendors: []
        };
    },


    methods: {
        limitText: function limitText(count) {
            return 'and ' + count + ' other vendors';
        },
        asyncFind: function asyncFind(query) {
            var _this = this;

            this.isLoading = true;
            dokan.api.get('/stores' + '?search=' + query).done(function (response) {
                _this.isLoading = false;
                _this.vendors = _.map(response, function (item) {
                    return {
                        id: item.id,
                        name: item.store_name + '( ' + item.email + ' )'
                    };
                });
            });
        },
        clearAll: function clearAll() {
            this.announcement.sender_ids = [];
        },
        fetchAnnouncement: function fetchAnnouncement() {
            var _this2 = this;

            dokan.api.get('/announcement/' + this.$route.params.id).done(function (response) {
                _this2.announcement = response;
            }).error(function (response) {
                alert(response.responseJSON.message);
            });
        },
        updateAnnouncement: function updateAnnouncement(status) {
            var _this3 = this;

            this.loadSpinner = true;
            var jsonData = {};
            jsonData = jQuery.extend({}, this.announcement);

            jsonData.sender_ids = _.pluck(jsonData.sender_ids, 'id');
            jsonData.status = status;

            dokan.api.put('/announcement/' + this.$route.params.id, jsonData).done(function (response) {
                _this3.loadSpinner = false;
                _this3.isSaved = true;
                _this3.message = _this3.__('Announcement draft successfully', 'dokan');
                if ('draft' == status) {
                    _this3.$router.push({
                        name: 'EditAnnouncement',
                        params: { id: response.id }
                    });
                } else {
                    _this3.loadSpinner = false;
                    _this3.$router.push({
                        name: 'Announcement'
                    });
                }
            }).error(function (response) {
                _this3.loadSpinner = false;
                _this3.isSaved = true;
                _this3.message = response.responseJSON.message;
            });
        }
    },

    created: function created() {
        this.fetchAnnouncement();
    }
});

/***/ }),
/* 25 */
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

var ListTable = dokan_get_lib('ListTable');
var Currency = dokan_get_lib('Currency');
var Search = dokan_get_lib('Search');

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'Refund',

    components: {
        ListTable: ListTable,
        Currency: Currency,
        Search: Search
    },

    data: function data() {
        return {
            requests: [],
            loading: false,

            counts: {
                pending: 0,
                approved: 0,
                cancelled: 0
            },
            totalPages: 1,
            perPage: 10,
            totalItems: 0,
            notFound: this.__('No request found.', 'dokan'),
            columns: {
                'order_id': { label: this.__('Order ID', 'dokan') },
                'vendor': { label: this.__('Vendor', 'dokan') },
                'amount': { label: this.__('Refund Amount', 'dokan') },
                'reason': { label: this.__('Refund Reason', 'dokan') },
                'method': { label: this.__('Payment Gateway', 'dokan') },
                'date': { label: this.__('Date', 'dokan') }
            },

            actionColumn: 'order_id',
            actions: [{
                key: 'approved',
                label: this.__('Approve Refund', 'dokan')
            }, {
                key: 'cancelled',
                label: this.__('Cancel', 'dokan')
            }, {
                key: 'delete',
                label: this.__('Delete', 'dokan')
            }]
        };
    },


    computed: {
        currentStatus: function currentStatus() {
            return this.$route.query.status || 'all';
        },
        currentPage: function currentPage() {
            var page = this.$route.query.page || 1;
            return parseInt(page);
        },
        bulkActions: function bulkActions() {
            if ('pending' == this.$route.query.status) {
                return [{
                    key: 'approved',
                    label: this.__('Approve Refund', 'dokan')
                }, {
                    key: 'cancelled',
                    label: this.__('Cancel', 'dokan')
                }];
            } else if ('cancelled' == this.$route.query.status) {
                return [{
                    key: 'delete',
                    label: this.__('Delete', 'dokan')
                }];
            } else {
                return [];
            }
        }
    },

    watch: {
        '$route.query.status': function $routeQueryStatus() {
            this.fetchRefunds();
        },
        '$route.query.page': function $routeQueryPage() {
            this.fetchRefunds();
        }
    },

    methods: {
        doSearch: function doSearch(payload) {
            var _this = this;

            this.loading = true;

            dokan.api.get('/refund?per_page=' + this.perPage + '&page=' + this.currentPage + '&status=' + this.currentStatus).done(function (response, status, xhr) {
                _this.requests = response.filter(function (refund) {
                    return refund.order_id.includes(payload) || refund.vendor.store_name.includes(payload);
                });

                _this.loading = false;
                _this.updatedCounts(xhr);
                _this.updatePagination(xhr);
            });
        },
        updatedCounts: function updatedCounts(xhr) {
            this.counts.pending = parseInt(xhr.getResponseHeader('X-Status-Pending'));
            this.counts.approved = parseInt(xhr.getResponseHeader('X-Status-Completed'));
            this.counts.cancelled = parseInt(xhr.getResponseHeader('X-Status-Cancelled'));
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        fetchRefunds: function fetchRefunds() {
            var _this2 = this;

            this.loading = true;

            dokan.api.get('/refund?per_page=' + this.perPage + '&page=' + this.currentPage + '&status=' + this.currentStatus).done(function (response, status, xhr) {
                _this2.requests = response;
                _this2.loading = false;

                _this2.updatedCounts(xhr);
                _this2.updatePagination(xhr);
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
        orderUrl: function orderUrl(id) {
            return dokan.urls.adminRoot + 'post.php?post=' + id + '&action=edit';
        },
        vendorUrl: function vendorUrl(id) {
            return dokan.urls.adminRoot + 'admin.php?page=dokan#/vendors/' + id;
        },
        goToPage: function goToPage(page) {
            this.$router.push({
                name: 'Refund',
                query: {
                    status: this.currentStatus,
                    page: page
                }
            });
        },
        onActionClick: function onActionClick(action, row) {
            console.log(action, row);
        },
        rowAction: function rowAction(action, data) {
            var _this3 = this;

            this.loading = true;
            var jsonData = {};
            jsonData.id = data.row.id;
            jsonData.order_id = data.row.order_id;

            if ('approved' === action) {
                jsonData.status = 'approved';
                dokan.api.put('/refund/' + data.row.id, jsonData).done(function (response, status, xhr) {
                    _this3.fetchRefunds();
                    _this3.loading = false;
                });
            } else if ('cancelled' === action) {
                jsonData.status = 'cancelled';
                dokan.api.put('/refund/' + data.row.id, jsonData).done(function (response, status, xhr) {
                    _this3.fetchRefunds();
                    _this3.loading = false;
                });
            } else if ('delete' === action) {
                dokan.api.delete('/refund/' + data.row.id).done(function (response, status, xhr) {
                    _this3.fetchRefunds();
                    _this3.loading = false;
                });
            }
        },
        onBulkAction: function onBulkAction(action, items) {
            var _this4 = this;

            this.loading = true;
            var jsonData = {};
            jsonData[action] = items;

            console.log(jsonData);

            dokan.api.put('/refund/batch', jsonData).done(function (response, status, xhr) {
                _this4.fetchRefunds();
                _this4.loading = false;
            });
        }
    },

    created: function created() {
        this.fetchRefunds();
    }
});

/***/ }),
/* 26 */
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

var Postbox = dokan_get_lib('Postbox');
var Progressbar = dokan_get_lib('Progressbar');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'Tools',

    components: {
        Postbox: Postbox,
        Progressbar: Progressbar
    },

    data: function data() {
        return {
            progressValue: 0,
            showBar: '',
            types: [{
                name: this.__('Page Installation', 'dokan'),
                desc: this.__('Clicking this button will create required pages for the plugin.', 'dokan'),
                button: this.__('Install Dokan Pages', 'dokan'),
                action: 'create_pages'
            }, {
                name: this.__('Regenerate Order Sync Table', 'dokan'),
                desc: this.__('This tool will delete all orders from the Dokan\'s sync table and re-build it.', 'dokan'),
                button: this.__('Re-build', 'dokan'),
                total_orders: 0,
                offset: 0,
                limit: 100,
                action: 'regen_sync_table'
            }, {
                name: this.__('Check for Duplicate Orders', 'dokan'),
                desc: this.__('This tool will check for duplicate orders from the Dokan\'s sync table.', 'dokan'),
                button: this.__('Check Orders', 'dokan'),
                total_orders: 0,
                offset: 0,
                limit: 100,
                action: 'check_duplicate_suborders'
            }]

        };
    },


    methods: {
        doAction: function doAction(key) {
            switch (key) {
                case 0:
                    this.createPages();
                    break;
                case 1:
                    this.rebuildOrderTable();
                    this.showProgressBar(key);
                    break;
                case 2:
                    this.checkDuplicateOrder();
                    this.showProgressBar(key);
                    break;
            }
        },
        createPages: function createPages() {
            var self = this;
            var data = this.types[0];

            jQuery.post(dokan.ajaxurl, data, function (res) {
                if (res.success) {
                    self.$notify({
                        title: self.__('Success!', 'dokan'),
                        text: res.data.message,
                        type: 'success'
                    });
                } else {
                    self.$notify({
                        title: self.__('Failure!', 'dokan'),
                        text: self.__('Something went wrong.'),
                        type: 'warn'
                    });
                }
            });
        },
        rebuildOrderTable: function rebuildOrderTable() {
            var self = this;
            var data = this.types[1];

            jQuery.post(dokan.ajaxurl, data, function (res) {
                if (res.success) {
                    var completed = res.data.done * 100 / res.data.total_orders;

                    if (!isNaN(completed)) {
                        self.progressValue = Math.round(completed);
                    }

                    if (res.data.done != 'All') {
                        self.types[1].offset = res.data.offset;
                        self.types[1].total_orders = res.data.total_orders;

                        self.$notify({
                            title: self.__('Order re-build in progress...', 'dokan'),
                            text: res.data.message,
                            type: 'success'
                        });

                        return self.rebuildOrderTable();
                    }

                    self.$notify({
                        title: self.__('Success!', 'dokan'),
                        text: res.data.message,
                        type: 'success'
                    });
                } else {
                    self.$notify({
                        title: self.__('Failure!', 'dokan'),
                        text: self.__('Something went wrong.'),
                        type: 'warn'
                    });
                }
            });
        },
        checkDuplicateOrder: function checkDuplicateOrder() {
            var self = this;
            var data = this.types[2];

            jQuery.post(dokan.ajaxurl, data, function (res) {
                if (res.success) {
                    var completed = res.data.done * 100 / res.data.total_orders;

                    if (!isNaN(completed)) {
                        self.progressValue = Math.round(completed);
                    }

                    if (res.data.done != 'All') {
                        self.types[2].offset = res.data.offset;
                        self.types[2].total_orders = res.data.total_orders;
                        self.types[2].done = res.data.done;

                        self.$notify({
                            title: self.__('Checking Duplication in Progress...', 'dokan'),
                            text: res.data.message,
                            type: 'success'
                        });

                        return self.checkDuplicateOrder();
                    }

                    self.$notify({
                        title: self.__('Success!', 'dokan'),
                        text: res.data.message,
                        type: 'success'
                    });
                } else {
                    self.$notify({
                        title: self.__('Failure!', 'dokan'),
                        text: self.__('Something went wrong.'),
                        type: 'warn'
                    });
                }
            });
        },
        showProgressBar: function showProgressBar(key) {
            return this.showBar = key;
        }
    }
});

/***/ }),
/* 27 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

var Chart = dokan_get_lib('Chart');
var Postbox = dokan_get_lib('Postbox');
var Loading = dokan_get_lib('Loading');
var Currency = dokan_get_lib('Currency');
var Datepicker = dokan_get_lib('Datepicker');
var Multiselect = dokan_get_lib('Multiselect');
var ListTable = dokan_get_lib('ListTable');
var debounce = dokan_get_lib('debounce');

/* harmony default export */ __webpack_exports__["a"] = ({
    name: 'Reports',

    components: {
        Chart: Chart,
        Postbox: Postbox,
        Loading: Loading,
        Currency: Currency,
        Datepicker: Datepicker,
        Multiselect: Multiselect,
        ListTable: ListTable
    },

    data: function data() {
        return {
            from_date: this.getFromDate(),
            to_date: this.getToDate(),
            overview: null,
            report: null,
            showDatePicker: true,
            showYearPicker: false,
            showStorePicker: false,
            yearRange: this.getYearRange(),
            selectedYear: moment().format('Y'),
            getStore: '',
            getStoreList: [],
            isLoading: false,
            showReportArea: true,
            showLogsAarea: false,

            // logs data
            showCb: false,
            counts: {
                all: 0
            },

            totalItems: 0,
            perPage: 20,
            totalPages: 1,
            loading: false,
            actions: [],
            bulkActions: [],
            noLogFound: this.__('No logs found.', 'dokan'),

            columns: {
                'order_id': {
                    label: this.__('Order ID', 'dokan')
                },
                'vendor_id': {
                    label: this.__('Vendor', 'dokan')
                },
                'order_total': {
                    label: this.__('Order Total', 'dokan')
                },
                'vendor_earning': {
                    label: this.__('Vendor Earning', 'dokan')
                },
                'commission': {
                    label: this.__('Commission', 'dokan')
                },
                'status': {
                    label: this.__('Status', 'dokan')
                },
                'date': {
                    label: this.__('Date', 'dokan')
                }
            },
            logs: []
        };
    },
    created: function created() {
        if (this.$route.query.tab === 'logs') {
            this.fetchLogs();
            this.prepareLogArea();
        } else {
            this.fetchOverview();
            this.fetchReport();
        }

        if (this.$route.query.type === 'by-year') {
            this.prepareYearView();
        } else if (this.$route.query.type === 'by-vendor') {
            this.prepareVendorView();
        }
    },


    computed: {
        currentPage: function currentPage() {
            var page = this.$route.query.page || 1;

            return parseInt(page);
        }
    },

    watch: {
        '$route.query.type': function $routeQueryType() {
            this.report = null;
            this.overview = null;

            if (this.$route.query.type === 'by-year') {
                this.prepareYearView();
                this.showByYear();
            }

            if (this.$route.query.type === 'by-vendor') {
                this.prepareVendorView();
            }

            if (this.$route.query.type === 'by-day') {
                this.prepareDayView();
            }
        },
        '$route.query.page': function $routeQueryPage() {
            this.fetchLogs();
        },
        '$route.query.tab': function $routeQueryTab() {
            if (this.$route.query.tab === 'logs') {
                this.prepareLogArea();
                this.fetchLogs();
            } else {
                this.prepareReportArea();
                this.fetchReport();
                this.fetchOverview();
            }
        }
    },

    methods: {
        fetchOverview: function fetchOverview() {
            var from = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

            var _this = this;

            var to = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
            var seller_id = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;


            var url = '/admin/report/summary';

            if (from !== null && to !== null && seller_id !== null) {
                url = '/admin/report/summary?from=' + from + '&to=' + to + '&seller_id=' + seller_id;
            } else if (from !== null && to !== null) {
                url = '/admin/report/summary?from=' + from + '&to=' + to;
            }

            dokan.api.get(url).done(function (response) {
                _this.overview = response;
            });
        },
        fetchReport: function fetchReport() {
            var from = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

            var _this2 = this;

            var to = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
            var seller_id = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;


            var url = '/admin/report/overview';

            if (from !== null && to !== null && seller_id !== null) {
                url = '/admin/report/overview?from=' + from + '&to=' + to + '&seller_id=' + seller_id;
            } else if (from !== null && to !== null) {
                url = '/admin/report/overview?from=' + from + '&to=' + to;
            }

            dokan.api.get(url).done(function (response) {
                _this2.report = response;
            });
        },
        showReport: function showReport() {
            this.report = null;
            this.overview = null;

            this.fetchReport(this.from_date, this.to_date, this.getStore.id);
            this.fetchOverview(this.from_date, this.to_date, this.getStore.id);
        },
        showByYear: function showByYear() {
            this.report = null;
            this.overview = null;

            var fromDate = moment(String(this.selectedYear), 'Y').startOf('year').format('Y-M-D');
            var toDate = moment(String(this.selectedYear), 'Y').endOf('year').format('Y-M-D');

            this.fetchReport(fromDate, toDate);
            this.fetchOverview(fromDate, toDate);
        },
        getSalesCount: function getSalesCount() {
            return this.overview.sales.this_period !== null ? this.overview.sales.this_period : this.overview.sales.this_month;
        },
        getEarningCount: function getEarningCount() {
            return this.overview.earning.this_period !== null ? this.overview.earning.this_period : this.overview.earning.this_month;
        },
        getProductCount: function getProductCount() {
            return this.overview.products.this_period !== null ? this.overview.products.this_period : this.overview.products.this_month;
        },
        getVendorCount: function getVendorCount() {
            return this.overview.vendors.this_period !== null ? this.overview.vendors.this_period : this.overview.vendors.this_month;
        },
        getOrderCount: function getOrderCount() {
            return this.overview.orders.this_period !== null ? this.overview.orders.this_period : this.overview.orders.this_month;
        },
        getDetails: function getDetails() {
            return this.overview.products.this_period !== null ? this.__('Created this period', 'dokan') : this.__('created this month', 'dokan');
        },
        getVendorDetails: function getVendorDetails() {
            return this.overview.vendors.this_period !== null ? this.__('Signup this period', 'dokan') : this.__('signup this month', 'dokan');
        },
        getSalesDetails: function getSalesDetails() {
            return this.overview.sales.this_period !== null ? this.__('Net sales this period', 'dokan') : this.__('Net sales this month', 'dokan');
        },
        getEarningDetails: function getEarningDetails() {
            return this.overview.earning.this_period !== null ? this.__('Commission earned this period', 'dokan') : this.__('Commission earned this month', 'dokan');
        },
        getFromDate: function getFromDate() {
            return moment().startOf('month').format('Y-M-D');
        },
        getToDate: function getToDate() {
            return moment().endOf('month').format('Y-M-D');
        },
        getYearRange: function getYearRange() {
            var endYear = Number(moment().add(5, 'years').format('Y'));
            var startYear = Number(moment().subtract(5, 'years').format('Y'));

            var yearRange = [];

            for (var i = startYear; i <= endYear; i++) {
                yearRange.push(i);
            }

            return yearRange;
        },


        searchStore: debounce(function (payload) {
            var _this3 = this;

            this.isLoading = true;

            if (!payload) {
                return this.isLoading = false;
            }

            dokan.api.get('/stores?search=' + payload).done(function (response) {
                _this3.getStoreList = response.map(function (store) {
                    return { id: store.id, name: store.store_name };
                });

                _this3.isLoading = false;
            });
        }, 300),

        prepareVendorView: function prepareVendorView() {
            this.showDatePicker = true;
            this.showYearPicker = false;
            this.showStorePicker = true;
        },
        prepareYearView: function prepareYearView() {
            this.showDatePicker = false;
            this.showYearPicker = true;
            this.showStorePicker = false;
        },
        prepareDayView: function prepareDayView() {
            this.showDatePicker = true;
            this.showYearPicker = false;
            this.showStorePicker = false;
        },
        prepareLogArea: function prepareLogArea() {
            this.showLogsAarea = true;
            this.showReportArea = false;
        },
        prepareReportArea: function prepareReportArea() {
            this.showLogsAarea = false;
            this.showReportArea = true;
        },


        // all logs methods
        fetchLogs: function fetchLogs() {
            var _this4 = this;

            this.loading = true;

            dokan.api.get('/admin/logs', {
                per_page: this.perPage,
                page: this.currentPage
            }).done(function (response, status, xhr) {
                _this4.logs = response;
                _this4.loading = false;

                _this4.updatePagination(xhr);
            });
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        goToPage: function goToPage(page) {
            this.$router.push({
                name: 'Reports',
                query: {
                    tab: 'logs',
                    page: page
                }
            });
        },
        editOrderUrl: function editOrderUrl(id) {
            return dokan.urls.adminRoot + 'post.php?action=edit&post=' + id;
        },
        editUserUrl: function editUserUrl(id) {
            return dokan.urls.adminRoot + 'user-edit.php?user_id=' + id;
        },
        exportLogs: function exportLogs() {
            var csv = this.convertToCSV(this.logs);

            this.exportCSVFile(csv);
        },
        convertToCSV: function convertToCSV(data) {
            var array = (typeof data === 'undefined' ? 'undefined' : _typeof(data)) != 'object' ? JSON.parse(data) : data;
            var str = '';

            str += '"Order ID", "Vendor ID", "Vendor Name", "Order Total", "Refund Total", "Vendor Earning", "Commission", "Status", "Date"';
            str += '\r\n';

            for (var i = 0; i < array.length; i++) {
                var line = '';

                for (var index in array[i]) {
                    if (line != '') line += ',';

                    if ('commission' == index || 'previous_order_total' == index || 'vendor_earning' == index) {
                        line += '"' + accounting.formatMoney(array[i][index], '', dokan.precision, dokan.currency.thousand, dokan.currency.decimal, dokan.currency.format) + '"';
                    } else if ('has_refund' == index) {
                        line += '';
                    } else if ('order_total' == index) {

                        // if there is refund for an order, calculate refund total
                        var total_refund = 0;

                        if (array[i]['has_refund']) {
                            total_refund = array[i]['previous_order_total'] - array[i]['order_total'];
                        }

                        line += '"' + accounting.formatMoney(total_refund, '', dokan.precision, dokan.currency.thousand, dokan.currency.decimal, dokan.currency.format) + '"';
                    } else {
                        line += '"' + array[i][index] + '"';
                    }
                }

                str += line + '\r\n';
            }

            return str;
        },
        exportCSVFile: function exportCSVFile(csv) {
            var exportedFilenmae = 'logs-' + moment().format('Y-MM-DD') + '.csv';
            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });

            if (navigator.msSaveBlob) {
                // IE 10+
                navigator.msSaveBlob(blob, exportedFilenmae);
            } else {
                var link = document.createElement("a");
                if (link.download !== undefined) {
                    // feature detection
                    // Browsers that support HTML5 download attribute
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", exportedFilenmae);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        },
        moment: function (_moment) {
            function moment(_x7) {
                return _moment.apply(this, arguments);
            }

            moment.toString = function () {
                return _moment.toString();
            };

            return moment;
        }(function (date) {
            return moment(date);
        })
    }
});

/***/ }),
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */,
/* 35 */,
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _Vendors = __webpack_require__(47);

var _Vendors2 = _interopRequireDefault(_Vendors);

var _VendorSingle = __webpack_require__(61);

var _VendorSingle2 = _interopRequireDefault(_VendorSingle);

var _StoreCategoriesIndex = __webpack_require__(66);

var _StoreCategoriesIndex2 = _interopRequireDefault(_StoreCategoriesIndex);

var _StoreCategoriesShow = __webpack_require__(69);

var _StoreCategoriesShow2 = _interopRequireDefault(_StoreCategoriesShow);

var _Modules = __webpack_require__(71);

var _Modules2 = _interopRequireDefault(_Modules);

var _Announcement = __webpack_require__(74);

var _Announcement2 = _interopRequireDefault(_Announcement);

var _NewAnnouncement = __webpack_require__(77);

var _NewAnnouncement2 = _interopRequireDefault(_NewAnnouncement);

var _EditAnnouncement = __webpack_require__(80);

var _EditAnnouncement2 = _interopRequireDefault(_EditAnnouncement);

var _Refund = __webpack_require__(83);

var _Refund2 = _interopRequireDefault(_Refund);

var _Tools = __webpack_require__(86);

var _Tools2 = _interopRequireDefault(_Tools);

var _Reports = __webpack_require__(88);

var _Reports2 = _interopRequireDefault(_Reports);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

dokan_add_route(_Vendors2.default);
dokan_add_route(_VendorSingle2.default);
dokan_add_route(_StoreCategoriesIndex2.default);
dokan_add_route(_StoreCategoriesShow2.default);
dokan_add_route(_Modules2.default);
dokan_add_route(_Announcement2.default);
dokan_add_route(_NewAnnouncement2.default);
dokan_add_route(_EditAnnouncement2.default);
dokan_add_route(_Refund2.default);
dokan_add_route(_Tools2.default);
dokan_add_route(_Reports2.default);

/***/ }),
/* 47 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Vendors_vue__ = __webpack_require__(6);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7a477aab_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Vendors_vue__ = __webpack_require__(60);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(48)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Vendors_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7a477aab_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Vendors_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/Vendors.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7a477aab", Component.options)
  } else {
    hotAPI.reload("data-v-7a477aab", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 48 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 49 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_AddVendor_vue__ = __webpack_require__(7);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2bd34ac9_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_AddVendor_vue__ = __webpack_require__(59);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(50)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_AddVendor_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2bd34ac9_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_AddVendor_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/AddVendor.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2bd34ac9", Component.options)
  } else {
    hotAPI.reload("data-v-2bd34ac9", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 50 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 51 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 52 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "dokan-upload-image", on: { click: _vm.uploadImage } },
    [
      !_vm.showButton
        ? _c("img", { attrs: { src: _vm.image.src ? _vm.image.src : _vm.src } })
        : _vm._e(),
      _vm._v(" "),
      _vm.showButton
        ? _c(
            "button",
            {
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.uploadImage($event)
                }
              }
            },
            [_vm._v("\n        " + _vm._s(_vm.buttonLabel) + "\n    ")]
          )
        : _vm._e()
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
    require("vue-hot-reload-api")      .rerender("data-v-40b3524c", esExports)
  }
}

/***/ }),
/* 53 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_passwordGenerator_vue__ = __webpack_require__(12);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_58b956d0_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_passwordGenerator_vue__ = __webpack_require__(54);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_passwordGenerator_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_58b956d0_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_passwordGenerator_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/passwordGenerator.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-58b956d0", Component.options)
  } else {
    hotAPI.reload("data-v-58b956d0", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 54 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "password-generator" }, [
    !_vm.hideGenerateButton
      ? _c(
          "button",
          {
            staticClass: "button button-secondary",
            on: {
              click: function($event) {
                $event.preventDefault()
                return _vm.generatePassword($event)
              }
            }
          },
          [_vm._v("\n        " + _vm._s(_vm.title) + "\n    ")]
        )
      : _vm._e(),
    _vm._v(" "),
    _vm.showCancelButton
      ? _c(
          "button",
          {
            staticClass: "button regen-button",
            on: {
              click: function($event) {
                $event.preventDefault()
                return _vm.regenratePassword($event)
              }
            }
          },
          [
            _c("span", { staticClass: "dashicons dashicons-controls-repeat" }),
            _vm._v("\n        " + _vm._s(_vm.regenrateTitle) + "\n    ")
          ]
        )
      : _vm._e(),
    _vm._v(" "),
    _vm.showCancelButton
      ? _c(
          "button",
          {
            staticClass: "button cancel-button",
            on: {
              click: function($event) {
                $event.preventDefault()
                return _vm.cancelButton($event)
              }
            }
          },
          [_vm._v("\n        " + _vm._s(_vm.cancelTitle) + "\n    ")]
        )
      : _vm._e()
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-58b956d0", esExports)
  }
}

/***/ }),
/* 55 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("form", { staticClass: "account-info" }, [
    _c("div", { staticClass: "content-header" }, [
      _vm._v("\n        " + _vm._s(_vm.__("Account Info", "dokan")) + "\n    ")
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "content-body" }, [
      !_vm.getId()
        ? _c("div", { staticClass: "vendor-image" }, [
            _c("div", { staticClass: "picture" }, [
              _c("p", { staticClass: "picture-header" }, [
                _vm._v(_vm._s(_vm.__("Vendor Picture", "dokan")))
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "profile-image" },
                [
                  _c("upload-image", {
                    attrs: { croppingWidth: 150, croppingHeight: 150 },
                    on: { uploadedImage: _vm.uploadGravatar }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c("p", {
                staticClass: "picture-footer",
                domProps: {
                  innerHTML: _vm._s(
                    _vm.sprintf(
                      _vm.__(
                        "You can change your profile picutre on %s",
                        "dokan"
                      ),
                      "<a href='https://gravatar.com/' target='_blank'>Gravatar</a>"
                    )
                  )
                }
              })
            ]),
            _vm._v(" "),
            _c(
              "div",
              {
                class: [
                  "picture banner",
                  { "has-banner": _vm.vendorInfo.banner_id }
                ]
              },
              [
                _c(
                  "div",
                  { staticClass: "banner-image" },
                  [
                    _c("upload-image", {
                      attrs: {
                        showButton: _vm.showButton,
                        buttonLabel: _vm.__("Upload Banner", "dokan")
                      },
                      on: { uploadedImage: _vm.uploadBanner }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _vm.showButton
                  ? _c("p", { staticClass: "picture-footer" }, [
                      _vm._v(_vm._s(_vm.getUploadBannerText()))
                    ])
                  : _vm._e()
              ]
            )
          ])
        : _vm._e(),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "dokan-form-group" },
        [
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "store-email" } }, [
              _vm._v(_vm._s(_vm.__("First Name", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.first_name,
                  expression: "vendorInfo.first_name"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "email",
                placeholder: _vm.__("First Name", "dokan")
              },
              domProps: { value: _vm.vendorInfo.first_name },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo, "first_name", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "store-email" } }, [
              _vm._v(_vm._s(_vm.__("Last Name", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.last_name,
                  expression: "vendorInfo.last_name"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "email",
                placeholder: _vm.__("Last Name", "dokan")
              },
              domProps: { value: _vm.vendorInfo.last_name },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo, "last_name", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "store-name" } }, [
              _vm._v(_vm._s(_vm.__("Store Name", "dokan")))
            ]),
            _vm._v(" "),
            !_vm.getId()
              ? _c("span", { staticClass: "required-field" }, [_vm._v("*")])
              : _vm._e(),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.store_name,
                  expression: "vendorInfo.store_name"
                }
              ],
              class: {
                "dokan-form-input": true,
                "has-error": _vm.getError("store_name")
              },
              attrs: {
                type: "text",
                placeholder: _vm.getError("store_name")
                  ? _vm.__("Store Name is required", "dokan")
                  : _vm.__("Store Name", "dokan")
              },
              domProps: { value: _vm.vendorInfo.store_name },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo, "store_name", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          !_vm.getId()
            ? _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "store-url" } }, [
                  _vm._v(_vm._s(_vm.__("Store URL", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.user_nicename,
                      expression: "vendorInfo.user_nicename"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("Store Url", "dokan")
                  },
                  domProps: { value: _vm.vendorInfo.user_nicename },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo,
                        "user_nicename",
                        $event.target.value
                      )
                    }
                  }
                }),
                _vm._v(" "),
                _c("div", { staticClass: "store-avaibility-info" }, [
                  _vm.showStoreUrl
                    ? _c("p", { staticClass: "store-url" }, [
                        _vm._v(_vm._s(_vm.storeUrl))
                      ])
                    : _c("p", { staticClass: "store-url" }, [
                        _vm._v(_vm._s(_vm.otherStoreUrl))
                      ]),
                  _vm._v(" "),
                  _c(
                    "span",
                    {
                      class: {
                        "is-available": _vm.storeAvailable,
                        "not-available": !_vm.storeAvailable
                      }
                    },
                    [_vm._v(_vm._s(_vm.storeAvailabilityText))]
                  )
                ])
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "store-phone" } }, [
              _vm._v(_vm._s(_vm.__("Phone Number", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.phone,
                  expression: "vendorInfo.phone"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "number",
                placeholder: _vm.__("123456789", "dokan")
              },
              domProps: { value: _vm.vendorInfo.phone },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo, "phone", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "store-email" } }, [
              _vm._v(_vm._s(_vm.__("Email", "dokan")))
            ]),
            _vm._v(" "),
            !_vm.getId()
              ? _c("span", { staticClass: "required-field" }, [_vm._v("*")])
              : _vm._e(),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.user_email,
                  expression: "vendorInfo.user_email"
                }
              ],
              class: {
                "dokan-form-input": true,
                "has-error": _vm.getError("user_email")
              },
              attrs: {
                type: "email",
                placeholder: _vm.getError("user_email")
                  ? _vm.__("Email is required", "dokan")
                  : _vm.__("store@email.com", "dokan")
              },
              domProps: { value: _vm.vendorInfo.user_email },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo, "user_email", $event.target.value)
                }
              }
            }),
            _vm._v(" "),
            _c("div", { staticClass: "store-avaibility-info" }, [
              _c(
                "span",
                {
                  class: {
                    "is-available": _vm.emailAvailable,
                    "not-available": !_vm.emailAvailable
                  }
                },
                [_vm._v(_vm._s(_vm.emailAvailabilityText))]
              )
            ])
          ]),
          _vm._v(" "),
          !_vm.getId()
            ? [
                _c("div", { staticClass: "column" }, [
                  _c("label", { attrs: { for: "store-username" } }, [
                    _vm._v(_vm._s(_vm.__("Username", "dokan")))
                  ]),
                  _c("span", { staticClass: "required-field" }, [_vm._v("*")]),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.vendorInfo.user_login,
                        expression: "vendorInfo.user_login"
                      }
                    ],
                    staticClass: "dokan-form-input",
                    class: {
                      "dokan-form-input": true,
                      "has-error": _vm.getError("user_login")
                    },
                    attrs: {
                      type: "text",
                      placeholder: _vm.getError("user_login")
                        ? _vm.__("Username is required", "dokan")
                        : _vm.__("Username", "dokan")
                    },
                    domProps: { value: _vm.vendorInfo.user_login },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(
                          _vm.vendorInfo,
                          "user_login",
                          $event.target.value
                        )
                      }
                    }
                  }),
                  _vm._v(" "),
                  _c("div", { staticClass: "store-avaibility-info" }, [
                    _c(
                      "span",
                      {
                        class: {
                          "is-available": _vm.userNameAvailable,
                          "not-available": !_vm.userNameAvailable
                        }
                      },
                      [_vm._v(_vm._s(_vm.userNameAvailabilityText))]
                    )
                  ]),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "checkbox-left notify-vendor" },
                    [
                      _c("switches", {
                        attrs: { enabled: true, value: "notify_vendor" },
                        on: { input: _vm.sendEmail }
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "desc" }, [
                        _vm._v(
                          _vm._s(
                            _vm.__(
                              "Send the vendor an email about their account.",
                              "dokan"
                            )
                          )
                        )
                      ])
                    ],
                    1
                  )
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "column" },
                  [
                    _c("label", { attrs: { for: "store-password" } }, [
                      _vm._v(_vm._s(_vm.__("Passwrod", "dokan")))
                    ]),
                    _vm._v(" "),
                    _vm.showPassword
                      ? _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.vendorInfo.user_pass,
                              expression: "vendorInfo.user_pass"
                            }
                          ],
                          staticClass: "dokan-form-input",
                          attrs: { type: "text", placeholder: "********" },
                          domProps: { value: _vm.vendorInfo.user_pass },
                          on: {
                            input: function($event) {
                              if ($event.target.composing) {
                                return
                              }
                              _vm.$set(
                                _vm.vendorInfo,
                                "user_pass",
                                $event.target.value
                              )
                            }
                          }
                        })
                      : _vm._e(),
                    _vm._v(" "),
                    _c("password-generator", {
                      attrs: { title: _vm.__("Generate Password", "dokan") },
                      on: { passwordGenerated: _vm.setPassword }
                    })
                  ],
                  1
                )
              ]
            : _vm._e(),
          _vm._v(" "),
          _vm._l(_vm.getAccountFields, function(component, index) {
            return _c(component, {
              key: index,
              tag: "component",
              attrs: { vendorInfo: _vm.vendorInfo }
            })
          })
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
    require("vue-hot-reload-api")      .rerender("data-v-4e4447de", esExports)
  }
}

/***/ }),
/* 56 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "account-info" }, [
    _c("div", { staticClass: "content-header" }, [
      _vm._v("\n        " + _vm._s(_vm.__("Address", "dokan")) + "\n    ")
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "content-body" }, [
      _c(
        "div",
        { staticClass: "dokan-form-group" },
        [
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "street-1" } }, [
              _vm._v(_vm._s(_vm.__("Street 1", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.address.street_1,
                  expression: "vendorInfo.address.street_1"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: { type: "text", placeholder: _vm.__("Street 1", "dokan") },
              domProps: { value: _vm.vendorInfo.address.street_1 },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(
                    _vm.vendorInfo.address,
                    "street_1",
                    $event.target.value
                  )
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "street-2" } }, [
              _vm._v(_vm._s(_vm.__("Street 2", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.address.street_2,
                  expression: "vendorInfo.address.street_2"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: { type: "text", placeholder: _vm.__("Street 2", "dokan") },
              domProps: { value: _vm.vendorInfo.address.street_2 },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(
                    _vm.vendorInfo.address,
                    "street_2",
                    $event.target.value
                  )
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "city" } }, [
              _vm._v(_vm._s(_vm.__("City", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.address.city,
                  expression: "vendorInfo.address.city"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: { type: "text", placeholder: _vm.__("City", "dokan") },
              domProps: { value: _vm.vendorInfo.address.city },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo.address, "city", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "zip" } }, [
              _vm._v(_vm._s(_vm.__("Zip", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.address.zip,
                  expression: "vendorInfo.address.zip"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: { type: "text", placeholder: _vm.__("Zip", "dokan") },
              domProps: { value: _vm.vendorInfo.address.zip },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo.address, "zip", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "column" },
            [
              _c("label", { attrs: { for: "country" } }, [
                _vm._v(_vm._s(_vm.__("Country", "dokan")))
              ]),
              _vm._v(" "),
              _c("Multiselect", {
                attrs: {
                  options: _vm.countries,
                  multiselect: false,
                  label: "name",
                  "track-by": "name",
                  showLabels: false,
                  placeholder: _vm.__("Select Country", "dokan")
                },
                on: { input: _vm.saveCountry },
                model: {
                  value: _vm.selectedCountry,
                  callback: function($$v) {
                    _vm.selectedCountry = $$v
                  },
                  expression: "selectedCountry"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "column" },
            [
              _c("label", { attrs: { for: "state" } }, [
                _vm._v(_vm._s(_vm.__("State", "dokan")))
              ]),
              _vm._v(" "),
              _vm.getStatesFromCountryCode(_vm.selectedCode).length < 1
                ? [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.vendorInfo.address.state,
                          expression: "vendorInfo.address.state"
                        }
                      ],
                      staticClass: "dokan-form-input",
                      attrs: {
                        type: "text",
                        placeholder: _vm.__("State", "dokan")
                      },
                      domProps: { value: _vm.vendorInfo.address.state },
                      on: {
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(
                            _vm.vendorInfo.address,
                            "state",
                            $event.target.value
                          )
                        }
                      }
                    })
                  ]
                : [
                    _c("Multiselect", {
                      attrs: {
                        options: _vm.getStatesFromCountryCode(_vm.selectedCode),
                        multiselect: false,
                        showLabels: false,
                        label: "name",
                        "track-by": "name",
                        placeholder: _vm.__("Select State", "dokan")
                      },
                      on: { input: _vm.saveState },
                      model: {
                        value: _vm.selectedState,
                        callback: function($$v) {
                          _vm.selectedState = $$v
                        },
                        expression: "selectedState"
                      }
                    })
                  ]
            ],
            2
          ),
          _vm._v(" "),
          _vm._l(_vm.getAddressFields, function(component, index) {
            return _c(component, {
              key: index,
              tag: "component",
              attrs: { vendorInfo: _vm.vendorInfo }
            })
          })
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
    require("vue-hot-reload-api")      .rerender("data-v-4ea3e636", esExports)
  }
}

/***/ }),
/* 57 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 58 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { class: { "payment-info": true, "edit-mode": _vm.getId() } },
    [
      _c("div", { staticClass: "content-header" }, [
        _vm._v(
          "\n        " + _vm._s(_vm.__("Payment Options", "dokan")) + "\n    "
        )
      ]),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "content-body" },
        [
          _c(
            "div",
            { staticClass: "dokan-form-group" },
            [
              _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "account-name" } }, [
                  _vm._v(_vm._s(_vm.__("Account Name", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.payment.bank.ac_name,
                      expression: "vendorInfo.payment.bank.ac_name"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("Account Name", "dokan")
                  },
                  domProps: { value: _vm.vendorInfo.payment.bank.ac_name },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo.payment.bank,
                        "ac_name",
                        $event.target.value
                      )
                    }
                  }
                })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "account-number" } }, [
                  _vm._v(_vm._s(_vm.__("Account Number", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.payment.bank.ac_number,
                      expression: "vendorInfo.payment.bank.ac_number"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("1233456789", "dokan")
                  },
                  domProps: { value: _vm.vendorInfo.payment.bank.ac_number },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo.payment.bank,
                        "ac_number",
                        $event.target.value
                      )
                    }
                  }
                })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "bank-name" } }, [
                  _vm._v(_vm._s(_vm.__("Bank Name", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.payment.bank.bank_name,
                      expression: "vendorInfo.payment.bank.bank_name"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("Bank Name", "dokan")
                  },
                  domProps: { value: _vm.vendorInfo.payment.bank.bank_name },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo.payment.bank,
                        "bank_name",
                        $event.target.value
                      )
                    }
                  }
                })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "bank-address" } }, [
                  _vm._v(_vm._s(_vm.__("Bank Address", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.payment.bank.bank_addr,
                      expression: "vendorInfo.payment.bank.bank_addr"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("Bank Address", "dokan")
                  },
                  domProps: { value: _vm.vendorInfo.payment.bank.bank_addr },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo.payment.bank,
                        "bank_addr",
                        $event.target.value
                      )
                    }
                  }
                })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "routing-number" } }, [
                  _vm._v(_vm._s(_vm.__("Routing Number", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.payment.bank.routing_number,
                      expression: "vendorInfo.payment.bank.routing_number"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("123456789", "dokan")
                  },
                  domProps: {
                    value: _vm.vendorInfo.payment.bank.routing_number
                  },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo.payment.bank,
                        "routing_number",
                        $event.target.value
                      )
                    }
                  }
                })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "iban" } }, [
                  _vm._v(_vm._s(_vm.__("IBAN", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.payment.bank.iban,
                      expression: "vendorInfo.payment.bank.iban"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("123456789", "dokan")
                  },
                  domProps: { value: _vm.vendorInfo.payment.bank.iban },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo.payment.bank,
                        "iban",
                        $event.target.value
                      )
                    }
                  }
                })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "column" }, [
                _c("label", { attrs: { for: "swift" } }, [
                  _vm._v(_vm._s(_vm.__("Swift", "dokan")))
                ]),
                _vm._v(" "),
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.vendorInfo.payment.bank.swift,
                      expression: "vendorInfo.payment.bank.swift"
                    }
                  ],
                  staticClass: "dokan-form-input",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("123456789", "dokan")
                  },
                  domProps: { value: _vm.vendorInfo.payment.bank.swift },
                  on: {
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(
                        _vm.vendorInfo.payment.bank,
                        "swift",
                        $event.target.value
                      )
                    }
                  }
                })
              ]),
              _vm._v(" "),
              _vm._l(_vm.getBankFields, function(component, index) {
                return _c(component, {
                  key: index,
                  tag: "component",
                  attrs: { vendorInfo: _vm.vendorInfo }
                })
              })
            ],
            2
          ),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "dokan-form-group" },
            [
              _c(
                "div",
                {
                  class: { column: _vm.getId(), "checkbox-group": !_vm.getId() }
                },
                [
                  _c("label", { attrs: { for: "account-name" } }, [
                    _vm._v(_vm._s(_vm.__("PayPal Email", "dokan")))
                  ]),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.vendorInfo.payment.paypal.email,
                        expression: "vendorInfo.payment.paypal.email"
                      }
                    ],
                    staticClass: "dokan-form-input",
                    attrs: {
                      type: "email",
                      placeholder: _vm.__("store@email.com", "dokan")
                    },
                    domProps: { value: _vm.vendorInfo.payment.paypal.email },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(
                          _vm.vendorInfo.payment.paypal,
                          "email",
                          $event.target.value
                        )
                      }
                    }
                  })
                ]
              ),
              _vm._v(" "),
              _vm.getId()
                ? [
                    _c("div", { staticClass: "column" }, [
                      _c(
                        "div",
                        { staticClass: "column" },
                        [
                          _c("label", [
                            _vm._v(
                              _vm._s(_vm.__("Admin Commission Type", "dokan"))
                            )
                          ]),
                          _vm._v(" "),
                          _c("Multiselect", {
                            attrs: {
                              options: _vm.commissionTypes,
                              multiselect: false,
                              searchable: false,
                              showLabels: false,
                              placeholder: _vm.__("Please Select One", "dokan")
                            },
                            on: { input: _vm.saveCommissionType },
                            model: {
                              value: _vm.selectedCommissionType,
                              callback: function($$v) {
                                _vm.selectedCommissionType = $$v
                              },
                              expression: "selectedCommissionType"
                            }
                          })
                        ],
                        1
                      )
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "column" }, [
                      _c("label", [
                        _vm._v(_vm._s(_vm.__("Admin Commission", "dokan")))
                      ]),
                      _vm._v(" "),
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.vendorInfo.admin_commission,
                            expression: "vendorInfo.admin_commission"
                          }
                        ],
                        staticClass: "dokan-form-input",
                        attrs: { type: "number", placeholder: "10" },
                        domProps: { value: _vm.vendorInfo.admin_commission },
                        on: {
                          input: function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.vendorInfo,
                              "admin_commission",
                              $event.target.value
                            )
                          }
                        }
                      })
                    ])
                  ]
                : _vm._e(),
              _vm._v(" "),
              _c("div", { staticClass: "checkbox-group" }, [
                _c(
                  "div",
                  { staticClass: "checkbox-left" },
                  [
                    _c("switches", {
                      attrs: { enabled: _vm.enabled, value: "enabled" },
                      on: { input: _vm.setValue }
                    }),
                    _vm._v(" "),
                    _c("span", { staticClass: "desc" }, [
                      _vm._v(_vm._s(_vm.__("Enable Selling", "dokan")))
                    ])
                  ],
                  1
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "checkbox-group" }, [
                _c(
                  "div",
                  { staticClass: "checkbox-left" },
                  [
                    _c("switches", {
                      attrs: { enabled: _vm.trusted, value: "trusted" },
                      on: { input: _vm.setValue }
                    }),
                    _vm._v(" "),
                    _c("span", { staticClass: "desc" }, [
                      _vm._v(
                        _vm._s(_vm.__("Publish Product Directly", "dokan"))
                      )
                    ])
                  ],
                  1
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "checkbox-group" }, [
                _c(
                  "div",
                  { staticClass: "checkbox-left" },
                  [
                    _c("switches", {
                      attrs: { enabled: _vm.featured, value: "featured" },
                      on: { input: _vm.setValue }
                    }),
                    _vm._v(" "),
                    _c("span", { staticClass: "desc" }, [
                      _vm._v(_vm._s(_vm.__("Make Vendor Featured", "dokan")))
                    ])
                  ],
                  1
                )
              ])
            ],
            2
          ),
          _vm._v(" "),
          _vm._l(_vm.getPyamentFields, function(component, index) {
            return _c(component, {
              key: index,
              tag: "component",
              attrs: { vendorInfo: _vm.vendorInfo }
            })
          })
        ],
        2
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
    require("vue-hot-reload-api")      .rerender("data-v-5203b477", esExports)
  }
}

/***/ }),
/* 59 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "dokan-vendor-edit" },
    [
      _c(
        "modal",
        {
          attrs: { title: _vm.title, width: "800px" },
          on: { close: _vm.closeModal }
        },
        [
          _c("div", { attrs: { slot: "body" }, slot: "body" }, [
            _c("div", { staticClass: "tab-header" }, [
              _c(
                "ul",
                { staticClass: "tab-list" },
                _vm._l(_vm.tabs, function(tab, index) {
                  return _c(
                    "li",
                    {
                      key: index,
                      class: {
                        "tab-title": true,
                        active: _vm.currentTab === tab.name,
                        last: tab.name === "VendorPaymentFields"
                      }
                    },
                    [
                      _c("div", { staticClass: "tab-link" }, [
                        _c(
                          "a",
                          {
                            class: {
                              first: tab.name === "VendorAccountFields"
                            },
                            attrs: { href: "#" },
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                _vm.currentTab = tab.name
                              }
                            }
                          },
                          [
                            _c("span", { class: [tab.icon] }),
                            _vm._v(
                              "\n                                " +
                                _vm._s(tab.label) +
                                "\n                            "
                            )
                          ]
                        )
                      ])
                    ]
                  )
                })
              )
            ]),
            _vm._v(" "),
            _vm.currentTab
              ? _c(
                  "div",
                  { staticClass: "tab-contents" },
                  [
                    _vm.isLoading
                      ? _c(
                          "div",
                          { staticClass: "loading" },
                          [_c("loading")],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    !_vm.isLoading
                      ? _c(
                          "transition",
                          { attrs: { name: "component-fade", mode: "out-in" } },
                          [
                            _c(_vm.currentTab, {
                              tag: "component",
                              attrs: {
                                vendorInfo: _vm.store,
                                errors: _vm.errors
                              }
                            })
                          ],
                          1
                        )
                      : _vm._e()
                  ],
                  1
                )
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { attrs: { slot: "footer" }, slot: "footer" }, [
            _c(
              "button",
              { staticClass: "dokan-btn", on: { click: _vm.createVendor } },
              [
                _vm._v(
                  _vm._s(
                    "VendorPaymentFields" === _vm.currentTab
                      ? _vm.__("Create Vendor", "dokan")
                      : this.nextBtn
                  )
                )
              ]
            )
          ])
        ]
      )
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
    require("vue-hot-reload-api")      .rerender("data-v-2bd34ac9", esExports)
  }
}

/***/ }),
/* 60 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "vendor-list" },
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [
        _vm._v(_vm._s(_vm.__("Vendors", "dokan")))
      ]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "page-title-action",
          on: {
            click: function($event) {
              _vm.addNew()
            }
          }
        },
        [_vm._v(_vm._s(_vm.__("Add New", "dokan")))]
      ),
      _vm._v(" "),
      _vm.categories.length
        ? _c(
            "router-link",
            {
              staticClass: "page-title-action",
              attrs: { to: { name: "StoreCategoriesIndex" } }
            },
            [_vm._v(_vm._s(_vm.__("Store Categories", "dokan")))]
          )
        : _vm._e(),
      _vm._v(" "),
      _c("hr", { staticClass: "wp-header-end" }),
      _vm._v(" "),
      _c("ul", { staticClass: "subsubsub" }, [
        _c(
          "li",
          [
            _c("router-link", {
              attrs: {
                to: { name: "Vendors", query: { status: "all" } },
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
                to: { name: "Vendors", query: { status: "approved" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__("Approved <span class='count'>(%s)</span>", "dokan"),
                    _vm.counts.approved
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
                to: { name: "Vendors", query: { status: "pending" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__("Pending <span class='count'>(%s)</span>", "dokan"),
                    _vm.counts.pending
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
        attrs: { title: "Search Vendors" },
        on: { searched: _vm.doSearch }
      }),
      _vm._v(" "),
      _c("list-table", {
        attrs: {
          columns: _vm.columns,
          loading: _vm.loading,
          rows: _vm.vendors,
          actions: _vm.actions,
          actionColumn: "store_name",
          "show-cb": _vm.showCb,
          "total-items": _vm.totalItems,
          "bulk-actions": _vm.bulkActions,
          "total-pages": _vm.totalPages,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage,
          "action-column": _vm.actionColumn,
          "not-found": "No vendors found.",
          "sort-by": _vm.sortBy,
          "sort-order": _vm.sortOrder
        },
        on: {
          sort: _vm.sortCallback,
          pagination: _vm.goToPage,
          "action:click": _vm.onActionClick,
          "bulk:click": _vm.onBulkAction,
          searched: _vm.doSearch
        },
        scopedSlots: _vm._u([
          {
            key: "store_name",
            fn: function(data) {
              return [
                _c("img", {
                  attrs: {
                    src: data.row.gravatar,
                    alt: data.row.store_name,
                    width: "50"
                  }
                }),
                _vm._v(" "),
                _c(
                  "strong",
                  [
                    _c(
                      "router-link",
                      { attrs: { to: "/vendors/" + data.row.id } },
                      [
                        _vm._v(
                          _vm._s(
                            data.row.store_name
                              ? data.row.store_name
                              : _vm.__("(no name)", "dokan")
                          )
                        )
                      ]
                    )
                  ],
                  1
                )
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
            key: "categories",
            fn: function(ref) {
              var row = ref.row
              return [
                _vm._v(
                  "\n            " +
                    _vm._s(
                      row.categories
                        .map(function(category) {
                          return category.name
                        })
                        .join(", ")
                    ) +
                    "\n        "
                )
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
            key: "enabled",
            fn: function(data) {
              return [
                _c("switches", {
                  attrs: { enabled: data.row.enabled, value: data.row.id },
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
                      ? _c(
                          "router-link",
                          {
                            attrs: {
                              to: {
                                path: "vendors/" + data.row.id,
                                query: { edit: "true" }
                              }
                            }
                          },
                          [_vm._v(_vm._s(action.label))]
                        )
                      : action.key == "products"
                        ? _c(
                            "a",
                            { attrs: { href: _vm.productUrl(data.row.id) } },
                            [_vm._v(_vm._s(action.label))]
                          )
                        : action.key == "orders"
                          ? _c(
                              "a",
                              { attrs: { href: _vm.ordersUrl(data.row.id) } },
                              [_vm._v(_vm._s(action.label))]
                            )
                          : _c("a", { attrs: { href: "#" } }, [
                              _vm._v(_vm._s(action.label))
                            ]),
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
      }),
      _vm._v(" "),
      _vm.loadAddVendor
        ? _c("add-vendor", { attrs: { "vendor-id": _vm.vendorId } })
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
    require("vue-hot-reload-api")      .rerender("data-v-7a477aab", esExports)
  }
}

/***/ }),
/* 61 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorSingle_vue__ = __webpack_require__(17);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_849fac40_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorSingle_vue__ = __webpack_require__(65);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(62)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorSingle_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_849fac40_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorSingle_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/VendorSingle.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-849fac40", Component.options)
  } else {
    hotAPI.reload("data-v-849fac40", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 62 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 63 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorSocialFields_vue__ = __webpack_require__(18);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2ed6d9c4_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorSocialFields_vue__ = __webpack_require__(64);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorSocialFields_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2ed6d9c4_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorSocialFields_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/VendorSocialFields.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2ed6d9c4", Component.options)
  } else {
    hotAPI.reload("data-v-2ed6d9c4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 64 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "social-info" }, [
    _c("div", { staticClass: "content-header" }, [
      _vm._v(
        "\n        " + _vm._s(_vm.__("Social Options", "dokan")) + "\n    "
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "content-body" }, [
      _c(
        "div",
        { staticClass: "dokan-form-group" },
        [
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Facebook", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.fb,
                  expression: "vendorInfo.social.fb"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.fb },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo.social, "fb", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Flickr", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.flickr,
                  expression: "vendorInfo.social.flickr"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.flickr },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo.social, "flickr", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Google Plus", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.gplus,
                  expression: "vendorInfo.social.gplus"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.gplus },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.vendorInfo.social, "gplus", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Twitter", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.twitter,
                  expression: "vendorInfo.social.twitter"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.twitter },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(
                    _vm.vendorInfo.social,
                    "twitter",
                    $event.target.value
                  )
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Youtube", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.youtube,
                  expression: "vendorInfo.social.youtube"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.youtube },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(
                    _vm.vendorInfo.social,
                    "youtube",
                    $event.target.value
                  )
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Linkedin", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.linkedin,
                  expression: "vendorInfo.social.linkedin"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.linkedin },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(
                    _vm.vendorInfo.social,
                    "linkedin",
                    $event.target.value
                  )
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Pinterest", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.pinterest,
                  expression: "vendorInfo.social.pinterest"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.pinterest },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(
                    _vm.vendorInfo.social,
                    "pinterest",
                    $event.target.value
                  )
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "column" }, [
            _c("label", { attrs: { for: "" } }, [
              _vm._v(_vm._s(_vm.__("Instagram", "dokan")))
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.vendorInfo.social.instagram,
                  expression: "vendorInfo.social.instagram"
                }
              ],
              staticClass: "dokan-form-input",
              attrs: {
                type: "text",
                placeholder: _vm.__("https://exmaple.com")
              },
              domProps: { value: _vm.vendorInfo.social.instagram },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(
                    _vm.vendorInfo.social,
                    "instagram",
                    $event.target.value
                  )
                }
              }
            })
          ]),
          _vm._v(" "),
          _vm._l(_vm.getSocialFields, function(component, index) {
            return _c(component, {
              key: index,
              tag: "component",
              attrs: { vendorInfo: _vm.vendorInfo }
            })
          })
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
    require("vue-hot-reload-api")      .rerender("data-v-2ed6d9c4", esExports)
  }
}

/***/ }),
/* 65 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "dokan-vendor-single" },
    [
      _c("div", { staticStyle: { "margin-bottom": "10px" } }, [
        _c(
          "a",
          {
            staticClass: "button",
            attrs: { href: "javascript:history.go(-1)" }
          },
          [_vm._v("← " + _vm._s(_vm.__("Go Back", "dokan")))]
        )
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "dokan-hide" }, [
        _vm._v("\n        " + _vm._s(_vm.store) + "\n    ")
      ]),
      _vm._v(" "),
      _vm.showDialog
        ? _c(
            "modal",
            {
              attrs: { title: _vm.__("Send Email", "dokan") },
              on: {
                close: function($event) {
                  _vm.showDialog = false
                }
              }
            },
            [
              _c("template", { slot: "body" }, [
                _c("div", { staticClass: "form-row" }, [
                  _c("label", { attrs: { for: "mailto" } }, [
                    _vm._v(_vm._s(_vm.__("To", "dokan")))
                  ]),
                  _vm._v(" "),
                  _c("input", {
                    attrs: { type: "text", id: "mailto", disabled: "disabled" },
                    domProps: { value: _vm.mailTo }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-row" }, [
                  _c("label", { attrs: { for: "subject" } }, [
                    _vm._v(_vm._s(_vm.__("Subject", "dokan")))
                  ]),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.mail.subject,
                        expression: "mail.subject"
                      }
                    ],
                    attrs: { type: "text", id: "subject" },
                    domProps: { value: _vm.mail.subject },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.mail, "subject", $event.target.value)
                      }
                    }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-row" }, [
                  _c("label", { attrs: { for: "message" } }, [
                    _vm._v(_vm._s(_vm.__("Message", "dokan")))
                  ]),
                  _vm._v(" "),
                  _c("textarea", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.mail.body,
                        expression: "mail.body"
                      }
                    ],
                    attrs: { id: "message", rows: "5", cols: "60" },
                    domProps: { value: _vm.mail.body },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.mail, "body", $event.target.value)
                      }
                    }
                  })
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
                        _vm.sendEmail()
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Send Email", "dokan")))]
                )
              ])
            ],
            2
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.store.id
        ? _c("div", { staticClass: "vendor-profile" }, [
            _c("section", { staticClass: "vendor-header" }, [
              _c("div", { staticClass: "profile-info" }, [
                _vm.store.featured
                  ? _c("div", { staticClass: "featured-vendor" }, [
                      _c("span", {
                        staticClass: "dashicons dashicons-star-filled",
                        attrs: { title: "Featured Vendor" }
                      })
                    ])
                  : _vm._e(),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    class: { "profile-icon": true, "edit-mode": _vm.editMode }
                  },
                  [
                    _vm.editMode
                      ? [
                          _c("upload-image", {
                            attrs: {
                              croppingWidth: 150,
                              croppingHeight: 150,
                              src:
                                _vm.store.gravatar_id && _vm.store.gravatar
                                  ? _vm.store.gravatar
                                  : _vm.getDefaultPic()
                            },
                            on: { uploadedImage: _vm.uploadGravatar }
                          })
                        ]
                      : [
                          _c("img", {
                            attrs: {
                              src: _vm.store.gravatar
                                ? _vm.store.gravatar
                                : _vm.getDefaultPic(),
                              alt: _vm.store.store_name
                            }
                          })
                        ],
                    _vm._v(" "),
                    _vm.editMode
                      ? _c(
                          "span",
                          {
                            staticClass: "edit-photo",
                            style: {
                              color: !_vm.store.gravatar_id ? "black" : ""
                            }
                          },
                          [
                            _vm._v(
                              "\n                        " +
                                _vm._s(_vm.__("Change Store Photo", "dokan")) +
                                "\n                    "
                            )
                          ]
                        )
                      : _vm._e()
                  ],
                  2
                ),
                _vm._v(" "),
                _c(
                  "div",
                  { class: { "store-info": true, "edit-mode": _vm.editMode } },
                  [
                    !_vm.editMode
                      ? [
                          _c("h2", { staticClass: "store-name" }, [
                            _vm._v(
                              _vm._s(
                                _vm.store.store_name
                                  ? _vm.store.store_name
                                  : _vm.__("(No Name)", "dokan")
                              )
                            )
                          ])
                        ]
                      : _vm._e(),
                    _vm._v(" "),
                    !_vm.editMode
                      ? _c(
                          "div",
                          { staticClass: "star-rating" },
                          _vm._l(5, function(i) {
                            return _c("span", {
                              class: [
                                "dashicons",
                                i <= _vm.store.rating.rating ? "active" : ""
                              ]
                            })
                          })
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.editMode
                      ? [
                          _c("VendorAccountFields", {
                            attrs: { vendorInfo: _vm.store }
                          })
                        ]
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.categories.length && !_vm.editMode
                      ? [
                          !_vm.editingCategories
                            ? [
                                !_vm.store.categories.length
                                  ? [
                                      _c("a", {
                                        staticClass: "store-categoy-names",
                                        attrs: { href: "#edit-categories" },
                                        domProps: {
                                          innerHTML: _vm._s(
                                            _vm.isCategoryMultiple
                                              ? _vm.__(
                                                  "Add Categories",
                                                  "dokan"
                                                )
                                              : _vm.__("Add Category", "dokan")
                                          )
                                        },
                                        on: {
                                          click: function($event) {
                                            $event.preventDefault()
                                            _vm.editingCategories = true
                                          }
                                        }
                                      })
                                    ]
                                  : [
                                      _c("a", {
                                        staticClass: "store-categoy-names",
                                        attrs: { href: "#edit-categories" },
                                        domProps: {
                                          innerHTML: _vm._s(
                                            _vm.store.categories
                                              .map(function(category) {
                                                return category.name
                                              })
                                              .join(", ")
                                          )
                                        },
                                        on: {
                                          click: function($event) {
                                            $event.preventDefault()
                                            _vm.editingCategories = true
                                          }
                                        }
                                      })
                                    ]
                              ]
                            : [
                                _c(
                                  "div",
                                  { staticClass: "store-categories-editing" },
                                  [
                                    _c("h4", [
                                      _vm._v(
                                        _vm._s(
                                          _vm.isCategoryMultiple
                                            ? _vm.__(
                                                "Set Store Categories",
                                                "dokan"
                                              )
                                            : _vm.__(
                                                "Set Store Category",
                                                "dokan"
                                              )
                                        )
                                      )
                                    ]),
                                    _vm._v(" "),
                                    _c(
                                      "fieldset",
                                      { attrs: { disabled: _vm.isUpdating } },
                                      [
                                        _c(
                                          "ul",
                                          {
                                            staticClass: "category-select-list"
                                          },
                                          _vm._l(_vm.categories, function(
                                            category
                                          ) {
                                            return _c(
                                              "li",
                                              { key: category.id },
                                              [
                                                _c("label", [
                                                  (_vm.isCategoryMultiple
                                                    ? "checkbox"
                                                    : "radio") === "checkbox"
                                                    ? _c("input", {
                                                        directives: [
                                                          {
                                                            name: "model",
                                                            rawName: "v-model",
                                                            value:
                                                              _vm.storeCategories,
                                                            expression:
                                                              "storeCategories"
                                                          }
                                                        ],
                                                        attrs: {
                                                          type: "checkbox"
                                                        },
                                                        domProps: {
                                                          value: category.id,
                                                          checked: Array.isArray(
                                                            _vm.storeCategories
                                                          )
                                                            ? _vm._i(
                                                                _vm.storeCategories,
                                                                category.id
                                                              ) > -1
                                                            : _vm.storeCategories
                                                        },
                                                        on: {
                                                          change: function(
                                                            $event
                                                          ) {
                                                            var $$a =
                                                                _vm.storeCategories,
                                                              $$el =
                                                                $event.target,
                                                              $$c = $$el.checked
                                                                ? true
                                                                : false
                                                            if (
                                                              Array.isArray($$a)
                                                            ) {
                                                              var $$v =
                                                                  category.id,
                                                                $$i = _vm._i(
                                                                  $$a,
                                                                  $$v
                                                                )
                                                              if (
                                                                $$el.checked
                                                              ) {
                                                                $$i < 0 &&
                                                                  (_vm.storeCategories = $$a.concat(
                                                                    [$$v]
                                                                  ))
                                                              } else {
                                                                $$i > -1 &&
                                                                  (_vm.storeCategories = $$a
                                                                    .slice(
                                                                      0,
                                                                      $$i
                                                                    )
                                                                    .concat(
                                                                      $$a.slice(
                                                                        $$i + 1
                                                                      )
                                                                    ))
                                                              }
                                                            } else {
                                                              _vm.storeCategories = $$c
                                                            }
                                                          }
                                                        }
                                                      })
                                                    : (_vm.isCategoryMultiple
                                                        ? "checkbox"
                                                        : "radio") === "radio"
                                                      ? _c("input", {
                                                          directives: [
                                                            {
                                                              name: "model",
                                                              rawName:
                                                                "v-model",
                                                              value:
                                                                _vm.storeCategories,
                                                              expression:
                                                                "storeCategories"
                                                            }
                                                          ],
                                                          attrs: {
                                                            type: "radio"
                                                          },
                                                          domProps: {
                                                            value: category.id,
                                                            checked: _vm._q(
                                                              _vm.storeCategories,
                                                              category.id
                                                            )
                                                          },
                                                          on: {
                                                            change: function(
                                                              $event
                                                            ) {
                                                              _vm.storeCategories =
                                                                category.id
                                                            }
                                                          }
                                                        })
                                                      : _c("input", {
                                                          directives: [
                                                            {
                                                              name: "model",
                                                              rawName:
                                                                "v-model",
                                                              value:
                                                                _vm.storeCategories,
                                                              expression:
                                                                "storeCategories"
                                                            }
                                                          ],
                                                          attrs: {
                                                            type: _vm.isCategoryMultiple
                                                              ? "checkbox"
                                                              : "radio"
                                                          },
                                                          domProps: {
                                                            value: category.id,
                                                            value:
                                                              _vm.storeCategories
                                                          },
                                                          on: {
                                                            input: function(
                                                              $event
                                                            ) {
                                                              if (
                                                                $event.target
                                                                  .composing
                                                              ) {
                                                                return
                                                              }
                                                              _vm.storeCategories =
                                                                $event.target.value
                                                            }
                                                          }
                                                        }),
                                                  _vm._v(
                                                    " " +
                                                      _vm._s(category.name) +
                                                      "\n                                            "
                                                  )
                                                ])
                                              ]
                                            )
                                          })
                                        ),
                                        _vm._v(" "),
                                        _c("p", [
                                          _c("button", {
                                            staticClass:
                                              "button button-primary button-small",
                                            domProps: {
                                              textContent: _vm._s(
                                                _vm.__("Done", "dokan")
                                              )
                                            },
                                            on: { click: _vm.updateStore }
                                          }),
                                          _vm._v(" "),
                                          _c("button", {
                                            staticClass:
                                              "button button-link button-small",
                                            domProps: {
                                              textContent: _vm._s(
                                                _vm.__("Cancel", "dokan")
                                              )
                                            },
                                            on: {
                                              click: function($event) {
                                                _vm.editingCategories = false
                                              }
                                            }
                                          })
                                        ])
                                      ]
                                    )
                                  ]
                                )
                              ]
                        ]
                      : _vm._e(),
                    _vm._v(" "),
                    !_vm.editMode
                      ? _c(
                          "ul",
                          {
                            class: {
                              "store-details": true,
                              "edit-mode": _vm.editMode
                            }
                          },
                          [
                            _c("li", { staticClass: "address" }, [
                              _vm.store.address.street_1
                                ? _c("span", { staticClass: "street_1" }, [
                                    _vm._v(
                                      _vm._s(_vm.store.address.street_1) + ", "
                                    )
                                  ])
                                : _vm._e(),
                              _vm._v(" "),
                              _vm.store.address.street_2
                                ? _c("span", { staticClass: "street_2" }, [
                                    _vm._v(
                                      _vm._s(_vm.store.address.street_2) + ", "
                                    )
                                  ])
                                : _vm._e(),
                              _vm._v(" "),
                              _vm.store.address.city
                                ? _c("span", { staticClass: "city" }, [
                                    _vm._v(
                                      _vm._s(_vm.store.address.city) + ", "
                                    )
                                  ])
                                : _vm._e(),
                              _vm._v(" "),
                              _vm.store.address.state
                                ? _c("span", { staticClass: "state-zip" }, [
                                    _vm._v(
                                      _vm._s(_vm.store.address.state) +
                                        " " +
                                        _vm._s(_vm.store.address.zip)
                                    )
                                  ])
                                : _vm._e(),
                              _vm._v(" "),
                              _vm.store.address.country
                                ? _c("span", { staticClass: "country" }, [
                                    _vm._v(_vm._s(_vm.store.address.country))
                                  ])
                                : _vm._e()
                            ]),
                            _vm._v(" "),
                            _c("li", { staticClass: "phone" }, [
                              _vm._v(
                                "\n                            " +
                                  _vm._s(
                                    _vm.store.phone ? _vm.store.phone : "—"
                                  ) +
                                  "\n                        "
                              )
                            ])
                          ]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    !_vm.editMode
                      ? _c("div", { staticClass: "actions" }, [
                          _c(
                            "button",
                            {
                              staticClass: "button message",
                              on: {
                                click: function($event) {
                                  _vm.messageDialog()
                                }
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "dashicons dashicons-email"
                              }),
                              _vm._v(
                                " " + _vm._s(_vm.__("Send Email", "dokan"))
                              )
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "button",
                            {
                              class: [
                                "button",
                                "status",
                                _vm.store.enabled ? "enabled" : "disabled"
                              ]
                            },
                            [
                              _c("span", { staticClass: "dashicons" }),
                              _vm._v(
                                " " +
                                  _vm._s(
                                    _vm.store.enabled
                                      ? _vm.__("Enabled", "dokan")
                                      : _vm.__("Disabled", "dokan")
                                  )
                              )
                            ]
                          )
                        ])
                      : _vm._e()
                  ],
                  2
                )
              ]),
              _vm._v(" "),
              _c(
                "div",
                {
                  class: { "profile-banner": true, "edit-mode": _vm.editMode }
                },
                [
                  _c(
                    "div",
                    { staticClass: "banner-wrap" },
                    [
                      _vm.editMode
                        ? [
                            _c("upload-image", {
                              attrs: { src: _vm.store.banner },
                              on: { uploadedImage: _vm.uploadBanner }
                            })
                          ]
                        : [
                            _vm.store.banner
                              ? _c("img", {
                                  attrs: {
                                    src: _vm.store.banner,
                                    alt: _vm.store.store_name
                                  }
                                })
                              : _vm._e()
                          ],
                      _vm._v(" "),
                      _vm.editMode
                        ? _c("span", { staticClass: "edit-banner" }, [
                            _c("i", {
                              staticClass:
                                "change-banner dashicons dashicons-format-image"
                            }),
                            _vm._v(
                              "\n                        " +
                                _vm._s(_vm.__("Change Store Banner", "dokan")) +
                                "\n                    "
                            )
                          ])
                        : _vm._e()
                    ],
                    2
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      class: { "action-links": true, "edit-mode": _vm.editMode }
                    },
                    [
                      _vm.editMode
                        ? [
                            _c(
                              "button",
                              {
                                staticClass: "button",
                                on: {
                                  click: function($event) {
                                    _vm.editMode = false
                                  }
                                }
                              },
                              [_vm._v(_vm._s(_vm.__("Cancel", "dokan")))]
                            ),
                            _vm._v(" "),
                            _c(
                              "button",
                              {
                                staticClass: "button button-primary",
                                on: { click: _vm.updateStore }
                              },
                              [_vm._v(_vm._s(_vm.saveBtn))]
                            )
                          ]
                        : [
                            _c(
                              "a",
                              {
                                staticClass: "button visit-store",
                                attrs: {
                                  href: _vm.store.shop_url,
                                  target: "_blank"
                                }
                              },
                              [
                                _vm._v(
                                  _vm._s(_vm.__("Visit Store", "dokan")) + " "
                                ),
                                _c("span", {
                                  staticClass:
                                    "dashicons dashicons-arrow-right-alt"
                                })
                              ]
                            ),
                            _vm._v(" "),
                            _c(
                              "router-link",
                              {
                                staticClass: "button",
                                attrs: { to: _vm.id },
                                nativeOn: {
                                  click: function($event) {
                                    _vm.editMode = true
                                  }
                                }
                              },
                              [
                                _c("span", {
                                  staticClass: "dashicons dashicons-edit"
                                })
                              ]
                            )
                          ]
                    ],
                    2
                  )
                ]
              )
            ]),
            _vm._v(" "),
            _vm.stats !== null && !_vm.editMode
              ? _c("section", { staticClass: "vendor-summary" }, [
                  _c("div", { staticClass: "summary-wrap products-revenue" }, [
                    _c("div", { staticClass: "stat-summary products" }, [
                      _c("h3", [_vm._v(_vm._s(_vm.__("Products", "dokan")))]),
                      _vm._v(" "),
                      _c("ul", { staticClass: "counts" }, [
                        _c("li", { staticClass: "products" }, [
                          _c("span", { staticClass: "count" }, [
                            _c("a", { attrs: { href: _vm.productUrl() } }, [
                              _vm._v(_vm._s(_vm.stats.products.total))
                            ])
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Total Products", "dokan")))
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "items" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(_vm._s(_vm.stats.products.sold))
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Items Sold", "dokan")))
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "visitors" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(_vm._s(_vm.stats.products.visitor))
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Store Visitors", "dokan")))
                          ])
                        ])
                      ])
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "stat-summary revenue" }, [
                      _c("h3", [_vm._v(_vm._s(_vm.__("Revenue", "dokan")))]),
                      _vm._v(" "),
                      _c("ul", { staticClass: "counts" }, [
                        _c("li", { staticClass: "orders" }, [
                          _c("span", { staticClass: "count" }, [
                            _c("a", { attrs: { href: _vm.ordersUrl() } }, [
                              _vm._v(_vm._s(_vm.stats.revenue.orders))
                            ])
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Orders Processed", "dokan")))
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "gross" }, [
                          _c(
                            "span",
                            { staticClass: "count" },
                            [
                              _c("currency", {
                                attrs: { amount: _vm.stats.revenue.sales }
                              })
                            ],
                            1
                          ),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Gross Sales", "dokan")))
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "earning" }, [
                          _c(
                            "span",
                            { staticClass: "count" },
                            [
                              _c("currency", {
                                attrs: { amount: _vm.stats.revenue.earning }
                              })
                            ],
                            1
                          ),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Total Earning", "dokan")))
                          ])
                        ])
                      ])
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "stat-summary others" }, [
                      _c("h3", [_vm._v(_vm._s(_vm.__("Others", "dokan")))]),
                      _vm._v(" "),
                      _c("ul", { staticClass: "counts" }, [
                        _c("li", { staticClass: "commision" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(_vm._s(_vm.getEearningRate))
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Commission Rate", "dokan")))
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "balance" }, [
                          _c(
                            "span",
                            { staticClass: "count" },
                            [
                              _c("currency", {
                                attrs: { amount: _vm.stats.others.balance }
                              })
                            ],
                            1
                          ),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Current Balance", "dokan")))
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "reviews" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(_vm._s(_vm.stats.others.reviews))
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v(_vm._s(_vm.__("Reviews", "dokan")))
                          ])
                        ])
                      ])
                    ])
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "vendor-info" }, [
                    _c("ul", [
                      _c("li", { staticClass: "registered" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v(_vm._s(_vm.__("Registered Since", "dokan")))
                        ]),
                        _vm._v(" "),
                        _c("span", { staticClass: "date" }, [
                          _vm._v(
                            "\n                            " +
                              _vm._s(
                                _vm
                                  .moment(_vm.store.registered)
                                  .format("MMM D, YYYY")
                              ) +
                              "\n                            (" +
                              _vm._s(
                                _vm.moment(_vm.store.registered).toNow(true)
                              ) +
                              ")\n                        "
                          )
                        ])
                      ]),
                      _vm._v(" "),
                      _c("li", { staticClass: "social-profiles" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v(_vm._s(_vm.__("Social Profiles", "dokan")))
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "profiles" }, [
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("fb") },
                              attrs: {
                                href: _vm.store.social.fb,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-facebook-logo"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("flickr") },
                              attrs: {
                                href: _vm.store.social.flickr,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass:
                                  "flaticon-flickr-website-logo-silhouette"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("twitter") },
                              attrs: {
                                href: _vm.store.social.twitter,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-twitter-logo-silhouette"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("gplus") },
                              attrs: {
                                href: _vm.store.social.gplus,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-google-plus"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: {
                                active: _vm.isSocialActive("instagram")
                              },
                              attrs: {
                                href: _vm.store.social.instagram,
                                target: "_blank"
                              }
                            },
                            [_c("span", { staticClass: "flaticon-instagram" })]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("youtube") },
                              attrs: {
                                href: _vm.store.social.youtube,
                                target: "_blank"
                              }
                            },
                            [_c("span", { staticClass: "flaticon-youtube" })]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("linkedin") },
                              attrs: {
                                href: _vm.store.social.linkedin,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-linkedin-logo"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: {
                                active: _vm.isSocialActive("pinterest")
                              },
                              attrs: {
                                href: _vm.store.social.pinterest,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-pinterest-logo"
                              })
                            ]
                          )
                        ])
                      ]),
                      _vm._v(" "),
                      _c("li", { staticClass: "payments" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v(_vm._s(_vm.__("Payment Methods", "dokan")))
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "payment-methods" }, [
                          _c("span", {
                            class: [
                              "flaticon-money",
                              _vm.hasPaymentEmail("paypal") ? "active" : ""
                            ],
                            attrs: { title: _vm.__("PayPal Payment", "dokan") }
                          }),
                          _vm._v(" "),
                          _c("span", {
                            staticClass: "flaticon-stripe-logo",
                            attrs: { title: _vm.__("Stripe Connect", "dokan") }
                          }),
                          _vm._v(" "),
                          _c("span", {
                            class: [
                              "flaticon-bank-building",
                              _vm.hasBank ? "active" : ""
                            ],
                            attrs: { title: _vm.__("Bank Payment", "dokan") }
                          }),
                          _vm._v(" "),
                          _c("span", {
                            class: [
                              "flaticon-skrill-pay-logo",
                              _vm.hasPaymentEmail("skrill") ? "active" : ""
                            ],
                            attrs: { title: _vm.__("Skrill", "dokan") }
                          })
                        ])
                      ]),
                      _vm._v(" "),
                      _c("li", { staticClass: "publishing" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v(_vm._s(_vm.__("Product Publishing", "dokan")))
                        ]),
                        _vm._v(" "),
                        _vm.store.trusted
                          ? _c("span", [
                              _c("span", {
                                staticClass: "dashicons dashicons-shield"
                              }),
                              _vm._v(" " + _vm._s(_vm.__("Direct", "dokan")))
                            ])
                          : _c("span", [
                              _c("span", {
                                staticClass: "dashicons dashicons-backup"
                              }),
                              _vm._v(
                                " " + _vm._s(_vm.__("Requires Review", "dokan"))
                              )
                            ])
                      ])
                    ])
                  ])
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.editMode
              ? _c("section", { staticClass: "vendor-other-info" }, [
                  _c(
                    "div",
                    { staticClass: "address-social-info" },
                    [
                      _c("VendorAddressFields", {
                        attrs: { vendorInfo: _vm.store }
                      }),
                      _vm._v(" "),
                      _c("VendorSocialFields", {
                        attrs: { vendorInfo: _vm.store }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "payment-info" },
                    [
                      _c("VendorPaymentFields", {
                        attrs: { vendorInfo: _vm.store }
                      })
                    ],
                    1
                  )
                ])
              : _vm._e(),
            _vm._v(" "),
            _c(
              "div",
              {
                class: {
                  "action-links": true,
                  footer: true,
                  "edit-mode": _vm.editMode
                }
              },
              [
                _vm.editMode
                  ? [
                      _c(
                        "button",
                        {
                          staticClass: "button",
                          on: {
                            click: function($event) {
                              _vm.editMode = false
                            }
                          }
                        },
                        [_vm._v(_vm._s(_vm.__("Cancel", "dokan")))]
                      ),
                      _vm._v(" "),
                      _c(
                        "button",
                        {
                          staticClass: "button button-primary",
                          on: { click: _vm.updateStore }
                        },
                        [_vm._v(_vm._s(_vm.saveBtn))]
                      )
                    ]
                  : _vm._e()
              ],
              2
            )
          ])
        : _c("vcl-twitch", { attrs: { height: "300", primary: "#ffffff" } })
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
    require("vue-hot-reload-api")      .rerender("data-v-849fac40", esExports)
  }
}

/***/ }),
/* 66 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_StoreCategoriesIndex_vue__ = __webpack_require__(19);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5a590ca5_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_StoreCategoriesIndex_vue__ = __webpack_require__(68);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(67)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_StoreCategoriesIndex_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5a590ca5_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_StoreCategoriesIndex_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/StoreCategoriesIndex.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5a590ca5", Component.options)
  } else {
    hotAPI.reload("data-v-5a590ca5", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 67 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 68 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { attrs: { id: "dokan-store-categories" } }, [
    _c("h1", { staticClass: "wp-heading-inline" }, [
      _vm._v(_vm._s(_vm.__("Store Categories", "dokan")))
    ]),
    _vm._v(" "),
    _c(
      "form",
      {
        staticClass: "search-form wp-clearfix",
        on: {
          submit: function($event) {
            $event.preventDefault()
          }
        }
      },
      [
        _c(
          "p",
          { staticClass: "search-box" },
          [
            _c("lazy-input", {
              attrs: {
                name: "s",
                type: "search",
                placeholder: _vm.__("Search Categories")
              },
              model: {
                value: _vm.search,
                callback: function($$v) {
                  _vm.search = $$v
                },
                expression: "search"
              }
            })
          ],
          1
        )
      ]
    ),
    _vm._v(" "),
    _c("div", { staticClass: "wp-clearfix", attrs: { id: "col-container" } }, [
      _c("div", { attrs: { id: "col-left" } }, [
        _c("div", { staticClass: "col-wrap" }, [
          _c("div", { staticClass: "form-wrap" }, [
            _c("h2", [_vm._v(_vm._s(_vm.__("Add New Category", "dokan")))]),
            _vm._v(" "),
            _c(
              "form",
              {
                attrs: { id: "addtag" },
                on: {
                  submit: function($event) {
                    $event.preventDefault()
                    return _vm.addCategory($event)
                  }
                }
              },
              [
                _c("fieldset", { attrs: { disabled: _vm.isCreating } }, [
                  _c(
                    "div",
                    { staticClass: "form-field form-required term-name-wrap" },
                    [
                      _c("label", { attrs: { for: "tag-name" } }, [
                        _vm._v(_vm._s(_vm.__("Name", "dokan")))
                      ]),
                      _vm._v(" "),
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.category.name,
                            expression: "category.name"
                          }
                        ],
                        attrs: {
                          id: "tag-name",
                          type: "text",
                          size: "40",
                          "aria-required": "true"
                        },
                        domProps: { value: _vm.category.name },
                        on: {
                          input: function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(_vm.category, "name", $event.target.value)
                          }
                        }
                      }),
                      _vm._v(" "),
                      _c("p", [
                        _vm._v(
                          _vm._s(_vm.__("The name of the category.", "dokan"))
                        )
                      ])
                    ]
                  ),
                  _vm._v(" "),
                  _c("div", { staticClass: "form-field term-slug-wrap" }, [
                    _c("label", { attrs: { for: "tag-slug" } }, [
                      _vm._v(_vm._s(_vm.__("Slug", "dokan")))
                    ]),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.category.slug,
                          expression: "category.slug"
                        }
                      ],
                      attrs: { id: "tag-slug", type: "text", size: "40" },
                      domProps: { value: _vm.category.slug },
                      on: {
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.category, "slug", $event.target.value)
                        }
                      }
                    }),
                    _vm._v(" "),
                    _c("p", [
                      _vm._v(
                        _vm._s(
                          _vm.__(
                            "The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.",
                            "dokan"
                          )
                        )
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "form-field term-description-wrap" },
                    [
                      _c("label", { attrs: { for: "tag-description" } }, [
                        _vm._v(_vm._s(_vm.__("Description", "dokan")))
                      ]),
                      _vm._v(" "),
                      _c("textarea", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.category.description,
                            expression: "category.description"
                          }
                        ],
                        attrs: { id: "tag-description", rows: "5", cols: "40" },
                        domProps: { value: _vm.category.description },
                        on: {
                          input: function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.category,
                              "description",
                              $event.target.value
                            )
                          }
                        }
                      }),
                      _vm._v(" "),
                      _c("p", [
                        _vm._v(
                          _vm._s(
                            _vm.__(
                              "The description is not prominent by default; however, some themes may show it.",
                              "dokan"
                            )
                          )
                        )
                      ])
                    ]
                  ),
                  _vm._v(" "),
                  _c("p", { staticClass: "submit" }, [
                    _c("input", {
                      staticClass: "button button-primary",
                      attrs: { type: "submit", name: "submit", id: "submit" },
                      domProps: { value: _vm.__("Add New Category", "dokan") }
                    })
                  ])
                ])
              ]
            )
          ])
        ])
      ]),
      _vm._v(" "),
      _c("div", { attrs: { id: "col-right" } }, [
        _c("div", { staticClass: "col-wrap" }, [
          _c(
            "form",
            { attrs: { id: "post-filter" } },
            [
              _c("list-table", {
                attrs: {
                  columns: _vm.columns,
                  loading: _vm.loading,
                  rows: _vm.categories,
                  actions: _vm.actions,
                  "action-column": _vm.actionColumn,
                  "show-cb": _vm.showCb,
                  "total-items": _vm.totalItems,
                  "bulk-actions": _vm.bulkActions,
                  "total-pages": _vm.totalPages,
                  "per-page": _vm.perPage,
                  "current-page": _vm.currentPage,
                  "not-found": _vm.notFound,
                  "sort-by": _vm.sortBy,
                  "sort-order": _vm.sortOrder
                },
                on: { pagination: _vm.goToPage },
                scopedSlots: _vm._u([
                  {
                    key: "name",
                    fn: function(ref) {
                      var row = ref.row
                      return [
                        _c(
                          "strong",
                          [
                            _c("router-link", {
                              attrs: {
                                to: {
                                  name: "StoreCategoriesShow",
                                  params: { id: row.id }
                                }
                              },
                              domProps: {
                                innerHTML: _vm._s(_vm.columnName(row))
                              }
                            })
                          ],
                          1
                        )
                      ]
                    }
                  },
                  {
                    key: "row-actions",
                    fn: function(ref) {
                      var row = ref.row
                      return _vm._l(_vm.actions, function(action, index) {
                        return _c(
                          "span",
                          { class: action.key },
                          [
                            action.key === "edit"
                              ? _c("router-link", {
                                  attrs: {
                                    to: {
                                      name: "StoreCategoriesShow",
                                      params: { id: row.id }
                                    }
                                  },
                                  domProps: {
                                    textContent: _vm._s(action.label)
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            row.id !== _vm.defaultCategory
                              ? [
                                  action.key === "delete"
                                    ? _c("a", {
                                        attrs: { href: "#delete" },
                                        domProps: {
                                          textContent: _vm._s(action.label)
                                        },
                                        on: {
                                          click: function($event) {
                                            $event.preventDefault()
                                            _vm.deleteCategory(row)
                                          }
                                        }
                                      })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  action.key === "set_as_default"
                                    ? _c("a", {
                                        attrs: { href: "#make-default" },
                                        domProps: {
                                          textContent: _vm._s(action.label)
                                        },
                                        on: {
                                          click: function($event) {
                                            $event.preventDefault()
                                            _vm.makeDefaultCategory(row)
                                          }
                                        }
                                      })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  index !== _vm.actions.length - 1
                                    ? [_vm._v(" | ")]
                                    : _vm._e()
                                ]
                              : _vm._e()
                          ],
                          2
                        )
                      })
                    }
                  },
                  {
                    key: "count",
                    fn: function(ref) {
                      var row = ref.row
                      return [
                        _c(
                          "router-link",
                          {
                            attrs: {
                              to: {
                                name: "Vendors",
                                query: { store_category: row.slug }
                              }
                            }
                          },
                          [_vm._v(_vm._s(row.count))]
                        )
                      ]
                    }
                  }
                ])
              })
            ],
            1
          )
        ])
      ])
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
    require("vue-hot-reload-api")      .rerender("data-v-5a590ca5", esExports)
  }
}

/***/ }),
/* 69 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_StoreCategoriesShow_vue__ = __webpack_require__(20);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_113d297a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_StoreCategoriesShow_vue__ = __webpack_require__(70);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_StoreCategoriesShow_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_113d297a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_StoreCategoriesShow_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/StoreCategoriesShow.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-113d297a", Component.options)
  } else {
    hotAPI.reload("data-v-113d297a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 70 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { attrs: { id: "dokan-store-category-single" } }, [
    _c("h1", [
      _vm._v(
        "\n        " + _vm._s(_vm.__("Edit Category", "dokan")) + "\n\n        "
      ),
      _c(
        "a",
        {
          staticClass: "alignright button",
          attrs: { href: "#" },
          on: {
            click: function($event) {
              $event.preventDefault()
              return _vm.deleteCategory($event)
            }
          }
        },
        [_vm._v(_vm._s(_vm.__("Delete Category", "dokan")))]
      )
    ]),
    _vm._v(" "),
    _c(
      "form",
      {
        on: {
          submit: function($event) {
            $event.preventDefault()
            return _vm.updateCategory($event)
          }
        }
      },
      [
        _c("fieldset", { attrs: { disabled: _vm.loading } }, [
          _c("table", { staticClass: "form-table" }, [
            _c("tbody", [
              _c(
                "tr",
                { staticClass: "form-field form-required term-name-wrap" },
                [
                  _c("th", { attrs: { scope: "row" } }, [
                    _c("label", { attrs: { for: "name" } }, [
                      _vm._v(_vm._s(_vm.__("Name", "dokan")))
                    ])
                  ]),
                  _vm._v(" "),
                  _c("td", [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.category.name,
                          expression: "category.name"
                        }
                      ],
                      attrs: {
                        id: "name",
                        type: "text",
                        size: "40",
                        "aria-required": "true"
                      },
                      domProps: { value: _vm.category.name },
                      on: {
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.category, "name", $event.target.value)
                        }
                      }
                    }),
                    _vm._v(" "),
                    _c("p", { staticClass: "description" }, [
                      _vm._v(
                        _vm._s(_vm.__("Name of the store category", "dokan"))
                      )
                    ])
                  ])
                ]
              ),
              _vm._v(" "),
              _c("tr", { staticClass: "form-field term-slug-wrap" }, [
                _c("th", { attrs: { scope: "row" } }, [
                  _c("label", { attrs: { for: "slug" } }, [
                    _vm._v(_vm._s(_vm.__("Slug", "dokan")))
                  ])
                ]),
                _vm._v(" "),
                _c("td", [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.category.slug,
                        expression: "category.slug"
                      }
                    ],
                    attrs: { id: "slug", type: "text", size: "40" },
                    domProps: { value: _vm.category.slug },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.category, "slug", $event.target.value)
                      }
                    }
                  }),
                  _vm._v(" "),
                  _c("p", { staticClass: "description" }, [
                    _vm._v(
                      _vm._s(
                        _vm.__(
                          "The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.",
                          "dokan"
                        )
                      )
                    )
                  ])
                ])
              ]),
              _vm._v(" "),
              _c("tr", { staticClass: "form-field term-description-wrap" }, [
                _c("th", { attrs: { scope: "row" } }, [
                  _c("label", { attrs: { for: "description" } }, [
                    _vm._v(_vm._s(_vm.__("Description", "dokan")))
                  ])
                ]),
                _vm._v(" "),
                _c("td", [
                  _c("textarea", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.category.description,
                        expression: "category.description"
                      }
                    ],
                    staticClass: "large-text",
                    attrs: { id: "description", rows: "5", cols: "50" },
                    domProps: { value: _vm.category.description },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(
                          _vm.category,
                          "description",
                          $event.target.value
                        )
                      }
                    }
                  }),
                  _vm._v(" "),
                  _c("p", { staticClass: "description" }, [
                    _vm._v(
                      _vm._s(
                        _vm.__(
                          "The description is not prominent by default; however, some themes may show it.",
                          "dokan"
                        )
                      )
                    )
                  ])
                ])
              ])
            ])
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "edit-tag-actions" }, [
            _c(
              "button",
              {
                staticClass: "button button-primary",
                attrs: { type: "submit" }
              },
              [_vm._v(_vm._s(_vm.__("Update", "dokan")))]
            )
          ])
        ])
      ]
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-113d297a", esExports)
  }
}

/***/ }),
/* 71 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Modules_vue__ = __webpack_require__(21);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2f819007_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Modules_vue__ = __webpack_require__(73);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(72)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Modules_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2f819007_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Modules_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/Modules.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2f819007", Component.options)
  } else {
    hotAPI.reload("data-v-2f819007", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 72 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 73 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "dokan-modules-wrap" }, [
    _c("h1", [_vm._v(_vm._s(_vm.__("Modules", "dokan")))]),
    _vm._v(" "),
    _c("div", { staticClass: "wp-filter module-filter" }, [
      _c("div", { staticClass: "filter-items" }, [
        _c(
          "ul",
          _vm._l(_vm.filterMenu, function(menu, index) {
            return _c(
              "li",
              { key: index, class: [_vm.filterMenuClass(menu.route)] },
              [
                _c("router-link", { attrs: { to: menu.route } }, [
                  _vm._v(
                    "\n                           " +
                      _vm._s(menu.title) +
                      "\n                       "
                  )
                ])
              ],
              1
            )
          })
        )
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "search-form" }, [
        _c("div", { staticClass: "view-switch" }, [
          _c(
            "a",
            {
              staticClass: "view-grid",
              class: { current: _vm.currentView == "grid" },
              attrs: { href: "#", id: "view-switch-grid" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  _vm.changeView("grid")
                }
              }
            },
            [
              _c("span", { staticClass: "screen-reader-text" }, [
                _vm._v("Grid View")
              ])
            ]
          ),
          _vm._v(" "),
          _c(
            "a",
            {
              staticClass: "view-list",
              class: { current: _vm.currentView == "list" },
              attrs: { href: "#", id: "view-switch-list" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  _vm.changeView("list")
                }
              }
            },
            [
              _c("span", { staticClass: "screen-reader-text" }, [
                _vm._v("List View")
              ])
            ]
          )
        ]),
        _vm._v(" "),
        _c(
          "label",
          {
            staticClass: "screen-reader-text",
            attrs: { for: "media-search-input" }
          },
          [_vm._v("Search Media")]
        ),
        _vm._v(" "),
        _c("input", {
          directives: [
            {
              name: "model",
              rawName: "v-model",
              value: _vm.search,
              expression: "search"
            }
          ],
          staticClass: "search",
          attrs: {
            type: "search",
            placeholder: "Search Module...",
            id: "media-search-input"
          },
          domProps: { value: _vm.search },
          on: {
            input: function($event) {
              if ($event.target.composing) {
                return
              }
              _vm.search = $event.target.value
            }
          }
        })
      ])
    ]),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "module-content" },
      [
        _vm.isLoaded
          ? [
              _vm.currentView == "list"
                ? _c("list-table", {
                    attrs: {
                      columns: _vm.column,
                      loading: false,
                      rows: _vm.filteredModules,
                      actions: [],
                      "show-cb": true,
                      "not-found": "No module found.",
                      "bulk-actions": [
                        {
                          key: "activate",
                          label: "Activate"
                        },
                        {
                          key: "deactivate",
                          label: "Deactivate"
                        }
                      ],
                      "sort-by": _vm.sortBy,
                      "sort-order": _vm.sortOrder,
                      "action-column": "name"
                    },
                    on: {
                      sort: _vm.sortCallback,
                      "bulk:click": _vm.onBulkAction
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "name",
                        fn: function(data) {
                          return [
                            _c("img", {
                              attrs: {
                                src: data.row.thumbnail,
                                alt: data.row.name,
                                width: "50"
                              }
                            }),
                            _vm._v(" "),
                            _c("strong", [
                              _c("a", { attrs: { href: "#" } }, [
                                _vm._v(_vm._s(data.row.name))
                              ])
                            ])
                          ]
                        }
                      },
                      {
                        key: "active",
                        fn: function(data) {
                          return [
                            _c("switches", {
                              attrs: {
                                enabled: data.row.active,
                                value: data.row.slug
                              },
                              on: { input: _vm.onSwitch }
                            })
                          ]
                        }
                      }
                    ])
                  })
                : _vm._e(),
              _vm._v(" "),
              _vm.currentView == "grid"
                ? _c(
                    "div",
                    { staticClass: "wp-list-table widefat dokan-modules" },
                    [
                      _vm.filteredModules.length > 0
                        ? _vm._l(_vm.filteredModules, function(module) {
                            return _c("div", { staticClass: "plugin-card" }, [
                              _c("div", { staticClass: "plugin-card-top" }, [
                                _c("div", { staticClass: "name column-name" }, [
                                  _c("h3", [
                                    _c("span", { staticClass: "plugin-name" }, [
                                      _vm._v(_vm._s(module.name))
                                    ]),
                                    _vm._v(" "),
                                    _c("img", {
                                      staticClass: "plugin-icon",
                                      attrs: {
                                        src: module.thumbnail,
                                        alt: module.name
                                      }
                                    })
                                  ])
                                ]),
                                _vm._v(" "),
                                _c("div", { staticClass: "action-links" }, [
                                  _c(
                                    "ul",
                                    { staticClass: "plugin-action-buttons" },
                                    [
                                      _c(
                                        "li",
                                        {
                                          attrs: { "data-module": module.slug }
                                        },
                                        [
                                          _c("switches", {
                                            attrs: {
                                              enabled: module.active,
                                              value: module.slug
                                            },
                                            on: { input: _vm.onSwitch }
                                          })
                                        ],
                                        1
                                      )
                                    ]
                                  )
                                ]),
                                _vm._v(" "),
                                _c(
                                  "div",
                                  { staticClass: "desc column-description" },
                                  [
                                    _c("p", {
                                      domProps: {
                                        innerHTML: _vm._s(module.description)
                                      }
                                    })
                                  ]
                                )
                              ])
                            ])
                          })
                        : [
                            _c(
                              "div",
                              {
                                staticClass: "notice notice-info",
                                attrs: { id: "message" }
                              },
                              [
                                _c("p", [
                                  _c("strong", [
                                    _vm._v(
                                      _vm._s(
                                        _vm.__("No modules found.", "dokan")
                                      )
                                    )
                                  ])
                                ])
                              ]
                            )
                          ]
                    ],
                    2
                  )
                : _vm._e()
            ]
          : _c("div", { staticClass: "loading" }, [_c("loading")], 1)
      ],
      2
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-2f819007", esExports)
  }
}

/***/ }),
/* 74 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Announcement_vue__ = __webpack_require__(22);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_b4865812_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Announcement_vue__ = __webpack_require__(76);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(75)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Announcement_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_b4865812_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Announcement_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/Announcement.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-b4865812", Component.options)
  } else {
    hotAPI.reload("data-v-b4865812", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 75 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 76 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "dokan-announcement-wrapper" },
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [
        _vm._v(_vm._s(_vm.__("Announcement", "dokan")))
      ]),
      _vm._v(" "),
      _c(
        "router-link",
        {
          staticClass: "page-title-action",
          attrs: { to: { name: "NewAnnouncement" } }
        },
        [_vm._v(_vm._s(_vm.__("Add Announcement", "dokan")))]
      ),
      _vm._v(" "),
      _c("div", { staticClass: "help-block" }, [
        _c("span", { staticClass: "help-text" }, [
          _c(
            "a",
            {
              attrs: {
                href: "https://wedevs.com/docs/dokan/announcements/",
                target: "_blank"
              }
            },
            [_vm._v(_vm._s(_vm.__("Need Any Help ?", "dokan")))]
          )
        ]),
        _vm._v(" "),
        _c("span", { staticClass: "dashicons dashicons-smiley" })
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
                to: { name: "Announcement" },
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
                to: { name: "Announcement", query: { status: "publish" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Published <span class='count'>(%s)</span>",
                      "dokan-lite"
                    ),
                    _vm.counts.publish
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
                to: { name: "Announcement", query: { status: "pending" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Pending <span class='count'>(%s)</span>",
                      "dokan-lite"
                    ),
                    _vm.counts.pending
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
                to: { name: "Announcement", query: { status: "draft" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Draft <span class='count'>(%s)</span>",
                      "dokan-lite"
                    ),
                    _vm.counts.draft
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
                to: { name: "Announcement", query: { status: "trash" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Trash <span class='count'>(%s)</span>",
                      "dokan-lite"
                    ),
                    _vm.counts.trash
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
          rows: _vm.requests,
          loading: _vm.loading,
          "action-column": _vm.actionColumn,
          actions: _vm.actions,
          "show-cb": _vm.showCb,
          "bulk-actions": _vm.bulkActions,
          "not-found": _vm.notFound,
          "total-pages": _vm.totalPages,
          "total-items": _vm.totalItems,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage
        },
        on: {
          pagination: _vm.goToPage,
          "action:click": _vm.onActionClick,
          "bulk:click": _vm.onBulkAction
        },
        scopedSlots: _vm._u([
          {
            key: "title",
            fn: function(data) {
              return [
                "publish" == data.row.status
                  ? _c("strong", [_vm._v(_vm._s(data.row.title))])
                  : _c("strong", [
                      _c("a", { attrs: { href: _vm.editUrl(data.row.id) } }, [
                        _vm._v(_vm._s(data.row.title))
                      ])
                    ])
              ]
            }
          },
          {
            key: "status",
            fn: function(data) {
              return [
                _c("span", { class: data.row.status }, [
                  _vm._v(_vm._s(_vm.status[data.row.status]))
                ])
              ]
            }
          },
          {
            key: "content",
            fn: function(data) {
              return [
                _c("span", { class: data.row.status }, [
                  _c(
                    "a",
                    {
                      attrs: { href: "#" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.showContent(data.row)
                        }
                      }
                    },
                    [
                      _c("span", {
                        staticClass: "dashicons dashicons-visibility"
                      })
                    ]
                  )
                ])
              ]
            }
          },
          {
            key: "created_at",
            fn: function(data) {
              return [
                _vm._v(
                  "\n            " +
                    _vm._s(
                      _vm.moment(data.row.created_at).format("MMM D, YYYY")
                    ) +
                    "\n        "
                )
              ]
            }
          },
          {
            key: "send_to",
            fn: function(data) {
              return [
                "all_seller" === data.row.sender_type
                  ? _c("span", [_vm._v(_vm._s(_vm.__("All Vendor", "dokan")))])
                  : _vm._e(),
                _vm._v(" "),
                "selected_seller" === data.row.sender_type
                  ? _c("span", [
                      _vm._v(_vm._s(_vm.__("Selected Vendor", "dokan")))
                    ])
                  : _vm._e()
              ]
            }
          },
          {
            key: "row-actions",
            fn: function(data) {
              return [
                _vm._l(_vm.actions, function(action, index) {
                  return [
                    action.key == "edit" && "publish" != data.row.status
                      ? _c(
                          "span",
                          { class: action.key },
                          [
                            _c(
                              "a",
                              { attrs: { href: _vm.editUrl(data.row.id) } },
                              [_vm._v(_vm._s(action.label))]
                            ),
                            _vm._v(" "),
                            index !== _vm.actions.length - 1
                              ? [_vm._v(" | ")]
                              : _vm._e()
                          ],
                          2
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    action.key == "trash" && _vm.currentStatus != "trash"
                      ? _c("span", { class: action.key }, [
                          _c(
                            "a",
                            {
                              attrs: { href: "#" },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  _vm.rowAction(action.key, data)
                                }
                              }
                            },
                            [_vm._v(_vm._s(action.label))]
                          )
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    action.key == "delete" && _vm.currentStatus == "trash"
                      ? _c(
                          "span",
                          { class: action.key },
                          [
                            _c(
                              "a",
                              {
                                attrs: { href: "#" },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    _vm.rowAction(action.key, data)
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
                      : _vm._e(),
                    _vm._v(" "),
                    action.key == "restore" && _vm.currentStatus == "trash"
                      ? _c(
                          "span",
                          { class: action.key },
                          [
                            _c(
                              "a",
                              {
                                attrs: { href: "#" },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    _vm.rowAction(action.key, data)
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
                      : _vm._e()
                  ]
                })
              ]
            }
          }
        ])
      }),
      _vm._v(" "),
      _vm.showDialog
        ? _c(
            "modal",
            {
              attrs: { title: _vm.modalTitle, footer: false },
              on: {
                close: function($event) {
                  _vm.showDialog = false
                }
              }
            },
            [
              _c("template", { slot: "body" }, [
                _c("div", { domProps: { innerHTML: _vm._s(_vm.modalContent) } })
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
    require("vue-hot-reload-api")      .rerender("data-v-b4865812", esExports)
  }
}

/***/ }),
/* 77 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_NewAnnouncement_vue__ = __webpack_require__(23);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0a129b87_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_NewAnnouncement_vue__ = __webpack_require__(79);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(78)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_NewAnnouncement_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0a129b87_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_NewAnnouncement_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/NewAnnouncement.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0a129b87", Component.options)
  } else {
    hotAPI.reload("data-v-0a129b87", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 78 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 79 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "dokan-announcement-form-wrapper" }, [
    _c("h1", { staticClass: "wp-heading-inline" }, [
      _vm._v(_vm._s(_vm.__("Add New Announcement", "dokan")))
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "help-block" }, [
      _c("span", { staticClass: "help-text" }, [
        _c(
          "a",
          {
            attrs: {
              href: "https://wedevs.com/docs/dokan/announcements/",
              target: "_blank"
            }
          },
          [_vm._v(_vm._s(_vm.__("Need Any Help ?", "dokan")))]
        )
      ]),
      _vm._v(" "),
      _c("span", { staticClass: "dashicons dashicons-smiley" })
    ]),
    _vm._v(" "),
    _c("hr", { staticClass: "wp-header-end" }),
    _vm._v(" "),
    _c("form", { attrs: { action: "", method: "post", id: "post" } }, [
      _c("div", { attrs: { id: "poststuff" } }, [
        _c(
          "div",
          {
            staticClass: "metabox-holder columns-2",
            attrs: { id: "post-body" }
          },
          [
            _c("div", { staticClass: "post-body-content" }, [
              _c("div", { attrs: { id: "titlediv" } }, [
                _c("div", { attrs: { id: "titlewrap" } }, [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.announcement.title,
                        expression: "announcement.title"
                      }
                    ],
                    attrs: {
                      type: "text",
                      name: "post_title",
                      size: "30",
                      id: "title",
                      autocomplete: "off",
                      placeholder: "Enter announcement title"
                    },
                    domProps: { value: _vm.announcement.title },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.announcement, "title", $event.target.value)
                      }
                    }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "inside" })
              ]),
              _vm._v(" "),
              _c(
                "div",
                {
                  staticClass: "postarea wp-editor-expand",
                  attrs: { id: "postdivrich" }
                },
                [
                  _c("text-editor", {
                    model: {
                      value: _vm.announcement.content,
                      callback: function($$v) {
                        _vm.$set(_vm.announcement, "content", $$v)
                      },
                      expression: "announcement.content"
                    }
                  })
                ],
                1
              )
            ]),
            _vm._v(" "),
            _c(
              "div",
              {
                staticClass: "postbox-container",
                attrs: { id: "postbox-container-1" }
              },
              [
                _c(
                  "postbox",
                  {
                    attrs: {
                      title: _vm.__("Publish", "dokan"),
                      extraClass: "announcement-actions"
                    }
                  },
                  [
                    _c("div", { staticClass: "action" }, [
                      _c("input", {
                        staticClass: "button button-default draft-btn",
                        attrs: { type: "submit", disabled: _vm.loadSpinner },
                        domProps: { value: _vm.draftBtnLabel },
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            _vm.createAnnouncement("draft")
                          }
                        }
                      }),
                      _vm._v(" "),
                      _vm.loadSpinner
                        ? _c("span", { staticClass: "spinner" })
                        : _vm._e(),
                      _vm._v(" "),
                      _c("input", {
                        staticClass: "button button-primary publish-btn",
                        attrs: { type: "submit", disabled: _vm.loadSpinner },
                        domProps: { value: _vm.publishBtnLabel },
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            _vm.createAnnouncement("publish")
                          }
                        }
                      }),
                      _vm._v(" "),
                      _c("div", { staticClass: "clear" })
                    ])
                  ]
                )
              ],
              1
            ),
            _vm._v(" "),
            _c(
              "div",
              {
                staticClass: "postbox-container",
                attrs: { id: "postbox-container-2" }
              },
              [
                _c(
                  "postbox",
                  {
                    attrs: {
                      title: _vm.__("Announcement Settings", "dokan"),
                      extraClass: "announcement-settings"
                    }
                  },
                  [
                    _c(
                      "table",
                      { staticClass: "form-table announcement-meta-options" },
                      [
                        _c("tbody", [
                          _c("tr", [
                            _c("th", [
                              _vm._v(
                                _vm._s(_vm.__("Send Announcement To", "dokan"))
                              )
                            ]),
                            _vm._v(" "),
                            _c("td", [
                              _c(
                                "select",
                                {
                                  directives: [
                                    {
                                      name: "model",
                                      rawName: "v-model",
                                      value: _vm.announcement.sender_type,
                                      expression: "announcement.sender_type"
                                    }
                                  ],
                                  attrs: {
                                    name: "announcement_sender_type",
                                    id: "announcement_sender_type"
                                  },
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
                                        _vm.announcement,
                                        "sender_type",
                                        $event.target.multiple
                                          ? $$selectedVal
                                          : $$selectedVal[0]
                                      )
                                    }
                                  }
                                },
                                [
                                  _c(
                                    "option",
                                    { attrs: { value: "all_seller" } },
                                    [
                                      _vm._v(
                                        _vm._s(_vm.__("All Vendor", "dokan"))
                                      )
                                    ]
                                  ),
                                  _vm._v(" "),
                                  _c(
                                    "option",
                                    { attrs: { value: "selected_seller" } },
                                    [
                                      _vm._v(
                                        _vm._s(
                                          _vm.__("Selected Vendor", "dokan")
                                        )
                                      )
                                    ]
                                  )
                                ]
                              )
                            ])
                          ]),
                          _vm._v(" "),
                          "selected_seller" === _vm.announcement.sender_type
                            ? _c("tr", [
                                _c("th", [
                                  _vm._v(
                                    _vm._s(_vm.__("Select Vendors", "dokan"))
                                  )
                                ]),
                                _vm._v(" "),
                                _c(
                                  "td",
                                  [
                                    _c(
                                      "multiselect",
                                      {
                                        attrs: {
                                          id: "ajax",
                                          label: "name",
                                          "track-by": "id",
                                          placeholder: "Type to search",
                                          "open-direction": "bottom",
                                          options: _vm.vendors,
                                          multiple: true,
                                          searchable: true,
                                          loading: _vm.isLoading,
                                          "internal-search": false,
                                          "clear-on-select": true,
                                          "close-on-select": false,
                                          "options-limit": 300,
                                          limit: 3,
                                          "limit-text": _vm.limitText,
                                          "max-height": 700,
                                          "show-no-results": false,
                                          "hide-selected": true
                                        },
                                        on: { "search-change": _vm.asyncFind },
                                        scopedSlots: _vm._u([
                                          {
                                            key: "clear",
                                            fn: function(props) {
                                              return [
                                                _vm.announcement.sender_ids
                                                  .length
                                                  ? _c("div", {
                                                      staticClass:
                                                        "multiselect__clear",
                                                      on: {
                                                        mousedown: function(
                                                          $event
                                                        ) {
                                                          $event.preventDefault()
                                                          $event.stopPropagation()
                                                          _vm.clearAll(
                                                            props.search
                                                          )
                                                        }
                                                      }
                                                    })
                                                  : _vm._e()
                                              ]
                                            }
                                          }
                                        ]),
                                        model: {
                                          value: _vm.announcement.sender_ids,
                                          callback: function($$v) {
                                            _vm.$set(
                                              _vm.announcement,
                                              "sender_ids",
                                              $$v
                                            )
                                          },
                                          expression: "announcement.sender_ids"
                                        }
                                      },
                                      [
                                        _c(
                                          "span",
                                          {
                                            attrs: { slot: "noResult" },
                                            slot: "noResult"
                                          },
                                          [
                                            _vm._v(
                                              "Oops! No elements found. Consider changing the search query."
                                            )
                                          ]
                                        )
                                      ]
                                    )
                                  ],
                                  1
                                )
                              ])
                            : _vm._e()
                        ])
                      ]
                    )
                  ]
                )
              ],
              1
            )
          ]
        ),
        _vm._v(" "),
        _c("br", { staticClass: "clear" })
      ])
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
    require("vue-hot-reload-api")      .rerender("data-v-0a129b87", esExports)
  }
}

/***/ }),
/* 80 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_EditAnnouncement_vue__ = __webpack_require__(24);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_34d4b3be_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_EditAnnouncement_vue__ = __webpack_require__(82);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(81)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_EditAnnouncement_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_34d4b3be_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_EditAnnouncement_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/EditAnnouncement.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-34d4b3be", Component.options)
  } else {
    hotAPI.reload("data-v-34d4b3be", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 81 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 82 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.announcement.id
    ? _c(
        "div",
        { staticClass: "dokan-announcement-form-wrapper" },
        [
          _c("h1", { staticClass: "wp-heading-inline" }, [
            _vm._v(_vm._s(_vm.__("Edit Announcement", "dokan")))
          ]),
          _vm._v(" "),
          _c(
            "router-link",
            {
              staticClass: "page-title-action",
              attrs: { to: { name: "NewAnnouncement" } }
            },
            [_vm._v(_vm._s(_vm.__("Add Announcement", "dokan")))]
          ),
          _vm._v(" "),
          _c("div", { staticClass: "help-block" }, [
            _c("span", { staticClass: "help-text" }, [
              _c(
                "a",
                {
                  attrs: {
                    href: "https://wedevs.com/docs/dokan/announcements/",
                    target: "_blank"
                  }
                },
                [_vm._v(_vm._s(_vm.__("Need Any Help ?", "dokan")))]
              )
            ]),
            _vm._v(" "),
            _c("span", { staticClass: "dashicons dashicons-smiley" })
          ]),
          _vm._v(" "),
          _vm.isSaved
            ? _c(
                "div",
                {
                  staticClass:
                    "announcement-error notice is-dismissible updated",
                  attrs: { id: "announcement-message_updated" }
                },
                [
                  _c("p", [
                    _c("strong", {
                      domProps: { innerHTML: _vm._s(_vm.message) }
                    })
                  ]),
                  _vm._v(" "),
                  _c(
                    "button",
                    {
                      staticClass: "notice-dismiss",
                      attrs: { type: "button" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.isSaved = false
                        }
                      }
                    },
                    [
                      _c("span", { staticClass: "screen-reader-text" }, [
                        _vm._v(
                          _vm._s(_vm.__("Dismiss this notice.", "dokan-lite"))
                        )
                      ])
                    ]
                  )
                ]
              )
            : _vm._e(),
          _vm._v(" "),
          _c("hr", { staticClass: "wp-header-end" }),
          _vm._v(" "),
          _c("form", { attrs: { action: "", method: "post", id: "post" } }, [
            _c("div", { attrs: { id: "poststuff" } }, [
              _c(
                "div",
                {
                  staticClass: "metabox-holder columns-2",
                  attrs: { id: "post-body" }
                },
                [
                  _c("div", { staticClass: "post-body-content" }, [
                    _c("div", { attrs: { id: "titlediv" } }, [
                      _c("div", { attrs: { id: "titlewrap" } }, [
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.announcement.title,
                              expression: "announcement.title"
                            }
                          ],
                          attrs: {
                            type: "text",
                            name: "post_title",
                            size: "30",
                            id: "title",
                            autocomplete: "off",
                            placeholder: "Enter announcement title"
                          },
                          domProps: { value: _vm.announcement.title },
                          on: {
                            input: function($event) {
                              if ($event.target.composing) {
                                return
                              }
                              _vm.$set(
                                _vm.announcement,
                                "title",
                                $event.target.value
                              )
                            }
                          }
                        })
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "inside" })
                    ]),
                    _vm._v(" "),
                    _c(
                      "div",
                      {
                        staticClass: "postarea wp-editor-expand",
                        attrs: { id: "postdivrich" }
                      },
                      [
                        _c("text-editor", {
                          model: {
                            value: _vm.announcement.content,
                            callback: function($$v) {
                              _vm.$set(_vm.announcement, "content", $$v)
                            },
                            expression: "announcement.content"
                          }
                        })
                      ],
                      1
                    )
                  ]),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      staticClass: "postbox-container",
                      attrs: { id: "postbox-container-1" }
                    },
                    [
                      _c(
                        "postbox",
                        {
                          attrs: {
                            title: _vm.__("Publish", "dokan"),
                            extraClass: "announcement-actions"
                          }
                        },
                        [
                          _c("div", { staticClass: "action" }, [
                            _c("input", {
                              staticClass: "button button-default draft-btn",
                              attrs: { type: "submit" },
                              domProps: { value: _vm.draftBtnLabel },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  _vm.updateAnnouncement("draft")
                                }
                              }
                            }),
                            _vm._v(" "),
                            _vm.loadSpinner
                              ? _c("span", { staticClass: "spinner" })
                              : _vm._e(),
                            _vm._v(" "),
                            _c("input", {
                              staticClass: "button button-primary publish-btn",
                              attrs: { type: "submit" },
                              domProps: { value: _vm.publishBtnLabel },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  _vm.updateAnnouncement("publish")
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("div", { staticClass: "clear" })
                          ])
                        ]
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      staticClass: "postbox-container",
                      attrs: { id: "postbox-container-2" }
                    },
                    [
                      _c(
                        "postbox",
                        {
                          attrs: {
                            title: _vm.__("Announcement Settings", "dokan"),
                            extraClass: "announcement-settings"
                          }
                        },
                        [
                          _c(
                            "table",
                            {
                              staticClass:
                                "form-table announcement-meta-options"
                            },
                            [
                              _c("tbody", [
                                _c("tr", [
                                  _c("th", [
                                    _vm._v(
                                      _vm._s(
                                        _vm.__("Send Announcement To", "dokan")
                                      )
                                    )
                                  ]),
                                  _vm._v(" "),
                                  _c("td", [
                                    _c(
                                      "select",
                                      {
                                        directives: [
                                          {
                                            name: "model",
                                            rawName: "v-model",
                                            value: _vm.announcement.sender_type,
                                            expression:
                                              "announcement.sender_type"
                                          }
                                        ],
                                        attrs: {
                                          name: "announcement_sender_type",
                                          id: "announcement_sender_type"
                                        },
                                        on: {
                                          change: function($event) {
                                            var $$selectedVal = Array.prototype.filter
                                              .call(
                                                $event.target.options,
                                                function(o) {
                                                  return o.selected
                                                }
                                              )
                                              .map(function(o) {
                                                var val =
                                                  "_value" in o
                                                    ? o._value
                                                    : o.value
                                                return val
                                              })
                                            _vm.$set(
                                              _vm.announcement,
                                              "sender_type",
                                              $event.target.multiple
                                                ? $$selectedVal
                                                : $$selectedVal[0]
                                            )
                                          }
                                        }
                                      },
                                      [
                                        _c(
                                          "option",
                                          { attrs: { value: "all_seller" } },
                                          [
                                            _vm._v(
                                              _vm._s(
                                                _vm.__("All Vendor", "dokan")
                                              )
                                            )
                                          ]
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "option",
                                          {
                                            attrs: { value: "selected_seller" }
                                          },
                                          [
                                            _vm._v(
                                              _vm._s(
                                                _vm.__(
                                                  "Selected Vendor",
                                                  "dokan"
                                                )
                                              )
                                            )
                                          ]
                                        )
                                      ]
                                    )
                                  ])
                                ]),
                                _vm._v(" "),
                                "selected_seller" ===
                                _vm.announcement.sender_type
                                  ? _c("tr", [
                                      _c("th", [
                                        _vm._v(
                                          _vm._s(
                                            _vm.__("Select Vendors", "dokan")
                                          )
                                        )
                                      ]),
                                      _vm._v(" "),
                                      _c(
                                        "td",
                                        [
                                          _c(
                                            "multiselect",
                                            {
                                              attrs: {
                                                id: "ajax",
                                                label: "name",
                                                "track-by": "id",
                                                placeholder: "Type to search",
                                                "open-direction": "bottom",
                                                options: _vm.vendors,
                                                multiple: true,
                                                searchable: true,
                                                loading: _vm.isLoading,
                                                "internal-search": false,
                                                "clear-on-select": false,
                                                "close-on-select": false,
                                                "options-limit": 300,
                                                limit: 3,
                                                "limit-text": _vm.limitText,
                                                "max-height": 600,
                                                "show-no-results": false,
                                                "hide-selected": true
                                              },
                                              on: {
                                                "search-change": _vm.asyncFind
                                              },
                                              scopedSlots: _vm._u([
                                                {
                                                  key: "clear",
                                                  fn: function(props) {
                                                    return [
                                                      _vm.announcement
                                                        .sender_ids.length
                                                        ? _c("div", {
                                                            staticClass:
                                                              "multiselect__clear",
                                                            on: {
                                                              mousedown: function(
                                                                $event
                                                              ) {
                                                                $event.preventDefault()
                                                                $event.stopPropagation()
                                                                _vm.clearAll(
                                                                  props.search
                                                                )
                                                              }
                                                            }
                                                          })
                                                        : _vm._e()
                                                    ]
                                                  }
                                                }
                                              ]),
                                              model: {
                                                value:
                                                  _vm.announcement.sender_ids,
                                                callback: function($$v) {
                                                  _vm.$set(
                                                    _vm.announcement,
                                                    "sender_ids",
                                                    $$v
                                                  )
                                                },
                                                expression:
                                                  "announcement.sender_ids"
                                              }
                                            },
                                            [
                                              _c(
                                                "span",
                                                {
                                                  attrs: { slot: "noResult" },
                                                  slot: "noResult"
                                                },
                                                [
                                                  _vm._v(
                                                    "Oops! No elements found. Consider changing the search query."
                                                  )
                                                ]
                                              )
                                            ]
                                          )
                                        ],
                                        1
                                      )
                                    ])
                                  : _vm._e()
                              ])
                            ]
                          )
                        ]
                      )
                    ],
                    1
                  )
                ]
              ),
              _vm._v(" "),
              _c("br", { staticClass: "clear" })
            ])
          ])
        ],
        1
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-34d4b3be", esExports)
  }
}

/***/ }),
/* 83 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Refund_vue__ = __webpack_require__(25);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_21df77a8_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Refund_vue__ = __webpack_require__(85);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(84)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Refund_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_21df77a8_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Refund_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/Refund.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-21df77a8", Component.options)
  } else {
    hotAPI.reload("data-v-21df77a8", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 84 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 85 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "dokan-refund-wrapper" },
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [
        _vm._v(_vm._s(_vm.__("Refund Requests", "dokan")))
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "help-block" }, [
        _c("span", { staticClass: "help-text" }, [
          _c(
            "a",
            {
              attrs: {
                href: "https://wedevs.com/docs/dokan/refund/",
                target: "_blank"
              }
            },
            [_vm._v(_vm._s(_vm.__("Need Any Help ?", "dokan")))]
          )
        ]),
        _vm._v(" "),
        _c("span", { staticClass: "dashicons dashicons-smiley" })
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
                to: { name: "Refund", query: { status: "pending" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Pending <span class='count'>(%s)</span>",
                      "dokan-lite"
                    ),
                    _vm.counts.pending
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
                to: { name: "Refund", query: { status: "approved" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Approved <span class='count'>(%s)</span>",
                      "dokan-lite"
                    ),
                    _vm.counts.approved
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
                to: { name: "Refund", query: { status: "cancelled" } },
                "active-class": "current",
                exact: ""
              },
              domProps: {
                innerHTML: _vm._s(
                  _vm.sprintf(
                    _vm.__(
                      "Cancelled <span class='count'>(%s)</span>",
                      "dokan-lite"
                    ),
                    _vm.counts.cancelled
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
        attrs: { title: "Search Refund" },
        on: { searched: _vm.doSearch }
      }),
      _vm._v(" "),
      _c("list-table", {
        attrs: {
          columns: _vm.columns,
          rows: _vm.requests,
          loading: _vm.loading,
          "action-column": _vm.actionColumn,
          actions: _vm.actions,
          "show-cb": true,
          "bulk-actions": _vm.bulkActions,
          "not-found": _vm.notFound,
          "total-pages": _vm.totalPages,
          "total-items": _vm.totalItems,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage
        },
        on: {
          pagination: _vm.goToPage,
          "action:click": _vm.onActionClick,
          "bulk:click": _vm.onBulkAction,
          searched: _vm.doSearch
        },
        scopedSlots: _vm._u([
          {
            key: "order_id",
            fn: function(data) {
              return [
                _c("a", { attrs: { href: _vm.orderUrl(data.row.order_id) } }, [
                  _c("strong", [_vm._v("#" + _vm._s(data.row.order_id))])
                ])
              ]
            }
          },
          {
            key: "amount",
            fn: function(data) {
              return [_c("currency", { attrs: { amount: data.row.amount } })]
            }
          },
          {
            key: "vendor",
            fn: function(data) {
              return [
                _c(
                  "a",
                  {
                    attrs: {
                      href: _vm.vendorUrl(data.row.vendor.id),
                      title: data.row.vendor.email
                    }
                  },
                  [_vm._v(_vm._s(data.row.vendor.store_name))]
                )
              ]
            }
          },
          {
            key: "date",
            fn: function(data) {
              return [
                _vm._v(
                  "\n            " +
                    _vm._s(_vm.moment(data.row.created).format("MMM D, YYYY")) +
                    "\n        "
                )
              ]
            }
          },
          {
            key: "row-actions",
            fn: function(data) {
              return [
                _vm._l(_vm.actions, function(action, index) {
                  return [
                    action.key == "approved" && _vm.currentStatus == "pending"
                      ? _c(
                          "span",
                          { class: action.key },
                          [
                            _c(
                              "a",
                              {
                                attrs: { href: "#" },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    _vm.rowAction(action.key, data)
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
                      : _vm._e(),
                    _vm._v(" "),
                    action.key == "cancelled" && _vm.currentStatus == "pending"
                      ? _c("span", { class: action.key }, [
                          _c(
                            "a",
                            {
                              attrs: { href: "#" },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  _vm.rowAction(action.key, data)
                                }
                              }
                            },
                            [_vm._v(_vm._s(action.label))]
                          )
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    action.key == "delete" && _vm.currentStatus == "cancelled"
                      ? _c("span", { class: action.key }, [
                          _c(
                            "a",
                            {
                              attrs: { href: "#" },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  _vm.rowAction(action.key, data)
                                }
                              }
                            },
                            [_vm._v(_vm._s(action.label))]
                          )
                        ])
                      : _vm._e()
                  ]
                })
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
    require("vue-hot-reload-api")      .rerender("data-v-21df77a8", esExports)
  }
}

/***/ }),
/* 86 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Tools_vue__ = __webpack_require__(26);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_9a79bb4a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Tools_vue__ = __webpack_require__(87);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Tools_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_9a79bb4a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Tools_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/Tools.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-9a79bb4a", Component.options)
  } else {
    hotAPI.reload("data-v-9a79bb4a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 87 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "tools-page" },
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [
        _vm._v(_vm._s(_vm.__("Tools Page", "dokan")))
      ]),
      _vm._v(" "),
      _vm._l(_vm.types, function(type, key) {
        return _c("postbox", { key: key, attrs: { title: type.name } }, [
          _c("p", { domProps: { textContent: _vm._s(type.desc) } }),
          _vm._v(" "),
          _vm.showBar == key && key != ""
            ? _c(
                "div",
                [_c("progressbar", { attrs: { value: _vm.progressValue } })],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _c("a", {
            staticClass: "button button-primary",
            domProps: { textContent: _vm._s(type.button) },
            on: {
              click: function($event) {
                _vm.doAction(key)
              }
            }
          })
        ])
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
    require("vue-hot-reload-api")      .rerender("data-v-9a79bb4a", esExports)
  }
}

/***/ }),
/* 88 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Reports_vue__ = __webpack_require__(27);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_6806de3f_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Reports_vue__ = __webpack_require__(90);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(89)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Reports_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_6806de3f_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Reports_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/Reports.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-6806de3f", Component.options)
  } else {
    hotAPI.reload("data-v-6806de3f", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 89 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 90 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "reports-page dokan-dashboard" }, [
    _c(
      "h2",
      { staticClass: "nav-tab-wrapper woo-nav-tab-wrapper" },
      [
        _c(
          "router-link",
          {
            staticClass: "nav-tab",
            attrs: {
              to: { name: "Reports", query: { tab: "report", type: "by-day" } },
              "active-class": "nav-tab-active"
            }
          },
          [
            _vm._v(
              "\n            " +
                _vm._s(_vm.__("Reports", "dokan")) +
                "\n        "
            )
          ]
        ),
        _vm._v(" "),
        _c(
          "router-link",
          {
            staticClass: "nav-tab",
            attrs: {
              to: { name: "Reports", query: { tab: "logs" } },
              "active-class": "nav-tab-active",
              exact: ""
            }
          },
          [
            _vm._v(
              "\n            " +
                _vm._s(_vm.__("All Logs", "dokan")) +
                "\n        "
            )
          ]
        )
      ],
      1
    ),
    _vm._v(" "),
    _vm.showLogsAarea
      ? _c("div", { staticClass: "export-area" }, [
          _c(
            "button",
            {
              staticClass: "button",
              attrs: { id: "export-logs" },
              on: {
                click: function($event) {
                  _vm.exportLogs()
                }
              }
            },
            [_vm._v(_vm._s(_vm.__("Export Logs", "dokan")))]
          )
        ])
      : _vm._e(),
    _vm._v(" "),
    _vm.showReportArea
      ? _c("div", { staticClass: "report-area" }, [
          _c(
            "ul",
            {
              staticClass: "subsubsub dokan-report-sub",
              staticStyle: { float: "none" }
            },
            [
              _c(
                "li",
                [
                  _c(
                    "router-link",
                    {
                      attrs: {
                        to: {
                          name: "Reports",
                          query: { tab: "report", type: "by-day" }
                        },
                        "active-class": "current",
                        exact: ""
                      }
                    },
                    [
                      _vm._v(
                        "\n                    " +
                          _vm._s(_vm.__("By Day", "dokan")) +
                          " |\n                "
                      )
                    ]
                  )
                ],
                1
              ),
              _c(
                "li",
                [
                  _c(
                    "router-link",
                    {
                      attrs: {
                        to: {
                          name: "Reports",
                          query: { tab: "report", type: "by-year" }
                        },
                        "active-class": "current",
                        exact: ""
                      }
                    },
                    [
                      _vm._v(
                        "\n                    " +
                          _vm._s(_vm.__("By Year", "dokan")) +
                          " |\n                "
                      )
                    ]
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "li",
                [
                  _c(
                    "router-link",
                    {
                      attrs: {
                        to: {
                          name: "Reports",
                          query: { tab: "report", type: "by-vendor" }
                        },
                        "active-class": "current",
                        exact: ""
                      }
                    },
                    [
                      _vm._v(
                        "\n                    " +
                          _vm._s(_vm.__("By Vendor", "dokan")) +
                          "\n                "
                      )
                    ]
                  )
                ],
                1
              )
            ]
          ),
          _vm._v(" "),
          _vm.showDatePicker
            ? _c(
                "form",
                {
                  staticClass: "form-inline report-filter",
                  on: {
                    submit: function($event) {
                      $event.preventDefault()
                      return _vm.showReport($event)
                    }
                  }
                },
                [
                  _vm.showStorePicker
                    ? _c(
                        "span",
                        [
                          _c("span", { staticClass: "form-group" }, [
                            _c("label", { attrs: { for: "vendor" } }, [
                              _vm._v(
                                _vm._s(_vm.__("Store Name", "dokan")) + " :"
                              )
                            ])
                          ]),
                          _vm._v(" "),
                          _c("multiselect", {
                            staticClass: "vendor-picker",
                            attrs: {
                              loading: _vm.isLoading,
                              options: _vm.getStoreList,
                              placeholder: _vm.__("Search Store...", "dokan"),
                              showLabels: false,
                              label: "name",
                              "track-by": "name"
                            },
                            on: { "search-change": _vm.searchStore },
                            model: {
                              value: _vm.getStore,
                              callback: function($$v) {
                                _vm.getStore = $$v
                              },
                              expression: "getStore"
                            }
                          })
                        ],
                        1
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _c(
                    "span",
                    { staticClass: "form-group" },
                    [
                      _c("label", { attrs: { for: "to" } }, [
                        _vm._v(_vm._s(_vm.__("From", "dokan")) + " :")
                      ]),
                      _vm._v(" "),
                      _c("datepicker", {
                        staticClass: "dokan-input",
                        attrs: { value: _vm.from_date, format: "yy-mm-d" },
                        model: {
                          value: _vm.from_date,
                          callback: function($$v) {
                            _vm.from_date = $$v
                          },
                          expression: "from_date"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "span",
                    { staticClass: "form-group" },
                    [
                      _c("label", { attrs: { for: "to" } }, [
                        _vm._v(_vm._s(_vm.__("To", "dokan")) + " :")
                      ]),
                      _vm._v(" "),
                      _c("datepicker", {
                        staticClass: "dokan-input",
                        attrs: { value: _vm.from_date, format: "yy-mm-d" },
                        model: {
                          value: _vm.to_date,
                          callback: function($$v) {
                            _vm.to_date = $$v
                          },
                          expression: "to_date"
                        }
                      }),
                      _vm._v(" "),
                      _c(
                        "button",
                        { staticClass: "button", attrs: { type: "submit" } },
                        [_vm._v(_vm._s(_vm.__("Show", "dokan")))]
                      )
                    ],
                    1
                  )
                ]
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.showYearPicker
            ? _c(
                "form",
                {
                  staticClass: "form-inline report-filter",
                  on: {
                    submit: function($event) {
                      $event.preventDefault()
                      return _vm.showByYear($event)
                    }
                  }
                },
                [
                  _c("span", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "from" } }, [
                      _vm._v(_vm._s(_vm.__("Year", "dokan")) + ":")
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "select",
                    {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.selectedYear,
                          expression: "selectedYear"
                        }
                      ],
                      staticClass: "dokan-input",
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
                          _vm.selectedYear = $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        }
                      }
                    },
                    _vm._l(_vm.yearRange, function(year, key) {
                      return _c(
                        "option",
                        { key: key, domProps: { value: year } },
                        [_vm._v(_vm._s(year))]
                      )
                    })
                  ),
                  _vm._v(" "),
                  _c(
                    "button",
                    { staticClass: "button", attrs: { type: "submit" } },
                    [_vm._v(_vm._s(_vm.__("Show", "dokan")))]
                  )
                ]
              )
            : _vm._e(),
          _vm._v(" "),
          _c("div", { staticClass: "widgets-wrapper" }, [
            _c(
              "div",
              { staticClass: "left-side" },
              [
                _c(
                  "postbox",
                  {
                    attrs: {
                      title: _vm.__("At a Glance", "dokan"),
                      extraClass: "dokan-status"
                    }
                  },
                  [
                    _vm.overview !== null
                      ? _c("div", { staticClass: "dokan-status" }, [
                          _c("ul", [
                            _c("li", { staticClass: "sale" }, [
                              _c("div", {
                                staticClass: "dashicons dashicons-chart-bar"
                              }),
                              _vm._v(" "),
                              _c("a", { attrs: { href: "#" } }, [
                                _c(
                                  "strong",
                                  [
                                    _c("currency", {
                                      attrs: { amount: _vm.getSalesCount() }
                                    })
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c("div", { staticClass: "details" }, [
                                  _vm._v(
                                    "\n                                        " +
                                      _vm._s(_vm.getSalesDetails()) +
                                      " "
                                  ),
                                  _c(
                                    "span",
                                    { class: _vm.overview.sales.class },
                                    [_vm._v(_vm._s(_vm.overview.sales.parcent))]
                                  )
                                ])
                              ])
                            ]),
                            _vm._v(" "),
                            _c("li", { staticClass: "commission" }, [
                              _c("div", {
                                staticClass: "dashicons dashicons-chart-pie"
                              }),
                              _vm._v(" "),
                              _c("a", { attrs: { href: "#" } }, [
                                _c(
                                  "strong",
                                  [
                                    _c("currency", {
                                      attrs: { amount: _vm.getEarningCount() }
                                    })
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c("div", { staticClass: "details" }, [
                                  _vm._v(
                                    "\n                                        " +
                                      _vm._s(_vm.getEarningDetails()) +
                                      " "
                                  ),
                                  _c(
                                    "span",
                                    { class: _vm.overview.earning.class },
                                    [
                                      _vm._v(
                                        _vm._s(_vm.overview.earning.parcent)
                                      )
                                    ]
                                  )
                                ])
                              ])
                            ]),
                            _vm._v(" "),
                            !_vm.showStorePicker
                              ? _c("li", { staticClass: "vendor" }, [
                                  _c("div", {
                                    staticClass: "dashicons dashicons-id"
                                  }),
                                  _vm._v(" "),
                                  _c("a", { attrs: { href: "#" } }, [
                                    _c("strong", [
                                      _vm._v(
                                        _vm._s(
                                          _vm.sprintf(
                                            _vm.__("%s Vendor", "dokan"),
                                            _vm.getVendorCount()
                                          )
                                        )
                                      )
                                    ]),
                                    _vm._v(" "),
                                    _c("div", { staticClass: "details" }, [
                                      _vm._v(
                                        "\n                                        " +
                                          _vm._s(_vm.getVendorDetails()) +
                                          " "
                                      ),
                                      _c(
                                        "span",
                                        { class: _vm.overview.vendors.class },
                                        [
                                          _vm._v(
                                            _vm._s(_vm.overview.vendors.parcent)
                                          )
                                        ]
                                      )
                                    ])
                                  ])
                                ])
                              : _vm._e(),
                            _vm._v(" "),
                            _vm.showStorePicker
                              ? _c("li", { staticClass: "order" }, [
                                  _c("div", {
                                    staticClass: "dashicons dashicons-id"
                                  }),
                                  _vm._v(" "),
                                  _c("a", { attrs: { href: "#" } }, [
                                    _c("strong", [
                                      _vm._v(
                                        _vm._s(
                                          _vm.sprintf(
                                            _vm.__("%s Orders", "dokan"),
                                            _vm.getOrderCount()
                                          )
                                        )
                                      )
                                    ]),
                                    _vm._v(" "),
                                    _c("div", { staticClass: "details" }, [
                                      _vm._v(
                                        "\n                                        " +
                                          _vm._s(
                                            _vm.__(
                                              "Orders placed in this period",
                                              "dokan"
                                            )
                                          ) +
                                          " "
                                      ),
                                      _c(
                                        "span",
                                        { class: _vm.overview.orders.class },
                                        [
                                          _vm._v(
                                            _vm._s(_vm.overview.orders.parcent)
                                          )
                                        ]
                                      )
                                    ])
                                  ])
                                ])
                              : _vm._e(),
                            _vm._v(" "),
                            !_vm.showStorePicker
                              ? _c("li", { staticClass: "approval" }, [
                                  _c("div", {
                                    staticClass:
                                      "dashicons dashicons-businessman"
                                  }),
                                  _vm._v(" "),
                                  _c("a", { attrs: { href: "#" } }, [
                                    _c("strong", [
                                      _vm._v(
                                        _vm._s(
                                          _vm.sprintf(
                                            _vm.__("%s Vendor", "dokan"),
                                            _vm.overview.vendors.inactive
                                          )
                                        )
                                      )
                                    ]),
                                    _vm._v(" "),
                                    _c("div", { staticClass: "details" }, [
                                      _vm._v(
                                        _vm._s(
                                          _vm.__("awaiting approval", "dokan")
                                        )
                                      )
                                    ])
                                  ])
                                ])
                              : _vm._e(),
                            _vm._v(" "),
                            _c("li", { staticClass: "product" }, [
                              _c("div", {
                                staticClass: "dashicons dashicons-cart"
                              }),
                              _vm._v(" "),
                              _c("a", { attrs: { href: "#" } }, [
                                _c("strong", [
                                  _vm._v(
                                    _vm._s(
                                      _vm.sprintf(
                                        _vm.__("%s Products", "dokan"),
                                        _vm.getProductCount()
                                      )
                                    )
                                  )
                                ]),
                                _vm._v(" "),
                                _c("div", { staticClass: "details" }, [
                                  _vm._v(
                                    "\n                                        " +
                                      _vm._s(_vm.getDetails()) +
                                      " "
                                  ),
                                  _c(
                                    "span",
                                    { class: _vm.overview.products.class },
                                    [
                                      _vm._v(
                                        _vm._s(_vm.overview.products.parcent)
                                      )
                                    ]
                                  )
                                ])
                              ])
                            ]),
                            _vm._v(" "),
                            !_vm.showStorePicker
                              ? _c("li", { staticClass: "withdraw" }, [
                                  _c("div", {
                                    staticClass: "dashicons dashicons-money"
                                  }),
                                  _vm._v(" "),
                                  _c("a", { attrs: { href: "#" } }, [
                                    _c("strong", [
                                      _vm._v(
                                        _vm._s(
                                          _vm.sprintf(
                                            _vm.__("%s Withdrawals", "dokan"),
                                            _vm.overview.withdraw.pending
                                          )
                                        )
                                      )
                                    ]),
                                    _vm._v(" "),
                                    _c("div", { staticClass: "details" }, [
                                      _vm._v(
                                        _vm._s(
                                          _vm.__("awaiting approval", "dokan")
                                        )
                                      )
                                    ])
                                  ])
                                ])
                              : _vm._e()
                          ])
                        ])
                      : _c(
                          "div",
                          { staticClass: "loading" },
                          [_c("loading")],
                          1
                        )
                  ]
                )
              ],
              1
            ),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "right-side" },
              [
                _c(
                  "postbox",
                  {
                    staticClass: "overview-chart",
                    attrs: { title: _vm.__("Overview", "dokan") }
                  },
                  [
                    _vm.report !== null
                      ? _c("chart", { attrs: { data: _vm.report } })
                      : _c(
                          "div",
                          { staticClass: "loading" },
                          [_c("loading")],
                          1
                        )
                  ],
                  1
                )
              ],
              1
            )
          ])
        ])
      : _vm._e(),
    _vm._v(" "),
    _vm.showLogsAarea
      ? _c(
          "div",
          { staticClass: "logs-area" },
          [
            _c("list-table", {
              attrs: {
                columns: _vm.columns,
                loading: _vm.loading,
                rows: _vm.logs,
                actions: _vm.actions,
                "bulk-actions": _vm.bulkActions,
                "show-cb": _vm.showCb,
                "total-items": _vm.totalItems,
                "total-pages": _vm.totalPages,
                "per-page": _vm.perPage,
                "current-page": _vm.currentPage,
                "not-found": _vm.noLogFound
              },
              on: { pagination: _vm.goToPage },
              scopedSlots: _vm._u([
                {
                  key: "order_id",
                  fn: function(data) {
                    return [
                      _c(
                        "a",
                        {
                          attrs: {
                            target: "_blank",
                            href: _vm.editOrderUrl(data.row.order_id)
                          }
                        },
                        [_vm._v("#" + _vm._s(data.row.order_id))]
                      )
                    ]
                  }
                },
                {
                  key: "vendor_id",
                  fn: function(data) {
                    return [
                      _c(
                        "a",
                        {
                          attrs: {
                            target: "_blank",
                            href: _vm.editUserUrl(data.row.vendor_id)
                          }
                        },
                        [
                          _vm._v(
                            _vm._s(
                              data.row.vendor_name
                                ? data.row.vendor_name
                                : _vm.__("(no name)", "dokan")
                            )
                          )
                        ]
                      )
                    ]
                  }
                },
                {
                  key: "order_total",
                  fn: function(data) {
                    return [
                      data.row.has_refund
                        ? _c(
                            "del",
                            [
                              _c("currency", {
                                attrs: { amount: data.row.previous_order_total }
                              })
                            ],
                            1
                          )
                        : _vm._e(),
                      _vm._v(" "),
                      _c("currency", {
                        attrs: { amount: data.row.order_total }
                      })
                    ]
                  }
                },
                {
                  key: "vendor_earning",
                  fn: function(data) {
                    return [
                      _c("currency", {
                        attrs: { amount: data.row.vendor_earning }
                      })
                    ]
                  }
                },
                {
                  key: "commission",
                  fn: function(data) {
                    return [
                      _c("currency", { attrs: { amount: data.row.commission } })
                    ]
                  }
                },
                {
                  key: "date",
                  fn: function(data) {
                    return [
                      _vm._v(
                        "\n                " +
                          _vm._s(
                            _vm.moment(data.row.date).format("MMM D, YYYY")
                          ) +
                          "\n            "
                      )
                    ]
                  }
                }
              ])
            })
          ],
          1
        )
      : _vm._e()
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-6806de3f", esExports)
  }
}

/***/ })
/******/ ]);