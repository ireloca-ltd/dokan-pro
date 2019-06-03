/* ========================================================================
 * Bootstrap: dropdown.js v3.3.7
 * ======================================================================== */
+function ($) {
  'use strict';

  // DROPDOWN CLASS DEFINITION
  // =========================

  var backdrop = '.dokan-geo-dropdown-backdrop'
  var toggle   = '[data-toggle="dokan-geo-dropdown"]'
  var Dropdown = function (element) {
    $(element).on('click.dokan.geo.dropdown', this.toggle)
  }

  Dropdown.VERSION = '3.3.7'

  function getParent($this) {
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = selector && $(selector)

    return $parent && $parent.length ? $parent : $this.parent()
  }

  function clearMenus(e) {
    if (e && e.which === 3) return
    $(backdrop).remove()
    $(toggle).each(function () {
      var $this         = $(this)
      var $parent       = getParent($this)
      var relatedTarget = { relatedTarget: this }

      if (!$parent.hasClass('open')) return

      if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return

      $parent.trigger(e = $.Event('hide.dokan.geo.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this.attr('aria-expanded', 'false')
      $parent.removeClass('open').trigger($.Event('hidden.dokan.geo.dropdown', relatedTarget))
    })
  }

  Dropdown.prototype.toggle = function (e) {
    var $this = $(this)

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    clearMenus()

    if (!isActive) {
      if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
        // if mobile we use a backdrop because click events don't delegate
        $(document.createElement('div'))
          .addClass('dokan-geo-dropdown-backdrop')
          .insertAfter($(this))
          .on('click', clearMenus)
      }

      var relatedTarget = { relatedTarget: this }
      $parent.trigger(e = $.Event('show.dokan.geo.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this
        .trigger('focus')
        .attr('aria-expanded', 'true')

      $parent
        .toggleClass('open')
        .trigger($.Event('shown.dokan.geo.dropdown', relatedTarget))
    }

    return false
  }

  Dropdown.prototype.keydown = function (e) {
    if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return

    var $this = $(this)

    e.preventDefault()
    e.stopPropagation()

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    if (!isActive && e.which != 27 || isActive && e.which == 27) {
      if (e.which == 27) $parent.find(toggle).trigger('focus')
      return $this.trigger('click')
    }

    var desc = ' li:not(.disabled):visible a'
    var $items = $parent.find('.dokan-geo-dropdown-menu' + desc)

    if (!$items.length) return

    var index = $items.index(e.target)

    if (e.which == 38 && index > 0)                 index--         // up
    if (e.which == 40 && index < $items.length - 1) index++         // down
    if (!~index)                                    index = 0

    $items.eq(index).trigger('focus')
  }


  // DROPDOWN PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('dokan.geo.dropdown')

      if (!data) $this.data('dokan.geo.dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.dropdown

  $.fn.dropdown             = Plugin
  $.fn.dropdown.Constructor = Dropdown


  // DROPDOWN NO CONFLICT
  // ====================

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  // APPLY TO STANDARD DROPDOWN ELEMENTS
  // ===================================

  $(document)
    .on('click.dokan.geo.dropdown.data-api', clearMenus)
    .on('click.dokan.geo.dropdown.data-api', '.dokan-geo-dropdown form', function (e) { e.stopPropagation() })
    .on('click.dokan.geo.dropdown.data-api', toggle, Dropdown.prototype.toggle)
    .on('keydown.dokan.geo.dropdown.data-api', toggle, Dropdown.prototype.keydown)
    .on('keydown.dokan.geo.dropdown.data-api', '.dokan-geo-dropdown-menu', Dropdown.prototype.keydown)

}(jQuery);

/**
 * Dokan Geolocation Module: Filter Form
 */
( function( $ ) {
    function GeolocationFilter ( form ) {
        this.form = null;
        this.slider = null;
        this.slider_value = 0;
        this.href = '';
        this.base_url = '';
        this.queries = {};
        this.query_params = [];
        this.scope = null;
        this.switchable_scope = null;
        this.display = '';
        this.s = '';
        this.dokan_seller_search = '';
        this.product_cat = '';
        this.latitude = 0.00;
        this.longitude = 0.00;
        this.address = '';
        this.distance = 0;
        this.geocoder = null;
        this.isStoreCategoryOn = false;

        this.form = form;

        this.init();
    }

    GeolocationFilter.prototype.init = function () {
        var self = this;

        self.set_query_params();

        self.form.on( 'submit', function (e) {
            e.preventDefault();
        } );

        self.display_form();

        self.s = self.form.find( '[name="s"]' ).val();
        self.dokan_seller_search = self.form.find( '[name="dokan_seller_search"]' ).val();
        self.latitude = self.form.find( '[name="latitude"]' ).val();
        self.longitude = self.form.find( '[name="longitude"]' ).val();

        self.slider = self.form.find( '.dokan-range-slider' );
        self.slider_value = self.slider.prev( '.dokan-range-slider-value' ).find( 'span' );

        self.slider.on( 'input', function () {
            self.slider_value.html( $( this ).val() );
        } );

        self.slider.on( 'change', function () {
            self.set_param( 'distance', $( this ).val() );
        } );

        self.form.find( '[name="s"], [name="dokan_seller_search"]' ).on( 'blur', function () {
            self.set_search_term( $( this ).val() );
        } );

        self.form.find( '[name="s"], [name="dokan_seller_search"]' ).on( 'keypress', function ( e ) {
            if ( 13 === e.which ) {
                self.set_search_term( $( this ).val() );
            }
        } );

        self.form.find( '[name="product_cat"]' ).on( 'change', function () {
            var category = $(this).val();

            self.set_param( 'product_cat', category );
        } );

        self.form.find( '[name="store_category"]' ).on( 'change', function () {
            var category = $(this).val();

            self.set_param( 'store_category', category );
        } );

        self.form.find( '.dokan-geo-filters-search-btn' ).on( 'click', function ( e ) {
            e.preventDefault();

            self.redirect( self.switchable_scope );
        } );

        self.bind_address_input();
    };

    GeolocationFilter.prototype.display_form = function () {
        var self = this;

        self.form.find( '.dokan-geolocation-filters-loading' ).remove();
        self.form.find( '.dokan-row' ).removeClass( 'dokan-hide' );
        self.scope = self.form.data( 'scope' );
        self.display = self.form.data( 'display' );
        self.isStoreCategoryOn = self.form.find( '#store-category-dropdown' ).length;

        if ( 'inline' !== self.display ) {
            self.form.find( '.dokan-geo-filters-column' ).addClass( 'dokan-w12' );

        } else if ( 'product' === self.scope ) {
            self.form.find( '.dokan-geo-filters-column' ).addClass( 'dokan-w4' );

        } else if ( 'vendor' === self.scope && self.isStoreCategoryOn ) {
            self.form.find( '.dokan-geo-filters-column' ).addClass( 'dokan-w4' );

        } else if ( 'vendor' === self.scope ) {
            self.form.find( '.dokan-geo-filters-column' ).addClass( 'dokan-w6' );

        } else {
            self.form.find( '.dokan-geo-filters-column' ).addClass( 'dokan-w3' );
        }

        if ( ! self.scope ) {
            self.switchable_scope = 'product';
            self.form.find( '[name="store_category"]' ).parent().addClass( 'dokan-hide' );
        }

        var scope_switch = self.form.find( '.dokan-geo-filter-scope-switch a' ),
            scope_label  = self.form.find( '.dokan-geo-filter-scope' );

        scope_switch.on( 'click', function ( e ) {
            e.preventDefault();

            var scope = $( this ).data( 'switch-scope' );

            if ( 'product' === scope ) {
                self.form.find( '[name="s"]' ).removeClass( 'dokan-hide' );
                self.form.find( '[name="dokan_seller_search"]' ).addClass( 'dokan-hide' );
                self.form.find( '.dokan-geo-product-categories' ).removeClass( 'dokan-hide' );
                self.form.find( '.dokan-geo-filters-column' ).removeClass( 'dokan-w4' ).addClass( 'dokan-w3' );
                self.form.find( '[name="store_category"]' ).parent().addClass( 'dokan-hide' );
            } else {
                var removeClass = self.isStoreCategoryOn ? 'dokan-w4' : 'dokan-w3';
                var addClass = self.isStoreCategoryOn ? 'dokan-w3' : 'dokan-w4';

                self.form.find( '[name="s"]' ).addClass( 'dokan-hide' );
                self.form.find( '[name="dokan_seller_search"]' ).removeClass( 'dokan-hide' );
                self.form.find( '.dokan-geo-product-categories' ).addClass( 'dokan-hide' );
                self.form.find( '.dokan-geo-filters-column' ).removeClass( removeClass ).addClass( addClass );
                self.form.find( '[name="store_category"]' ).parent().removeClass( 'dokan-hide' );
            }

            scope_label.html( $( this ).html() );
            self.switchable_scope = scope;
        } );
    };

    GeolocationFilter.prototype.bind_address_input = function () {
        var self = this;

        self.geocoder = new google.maps.Geocoder;

        // Autocomplete location address
        var address_input = self.form.find( '.location-address input' ),
            autocomplete = new google.maps.places.Autocomplete( address_input.get(0) );

        autocomplete.addListener( 'place_changed', function () {
            var place = autocomplete.getPlace(),
                location = place.geometry.location;

            self.latitude = location.lat();
            self.longitude = location.lng();

            self.set_address( place.formatted_address );
        } );

        // Locate button functions
        var locate_btn = self.form.find( '.locate-icon' ),
            loader = locate_btn.next();

        if ( navigator.geolocation ) {
            locate_btn.removeClass( 'dokan-hide' ).on( 'click', function () {
                locate_btn.addClass( 'dokan-hide' );
                loader.removeClass( 'dokan-hide' );

                navigator.geolocation.getCurrentPosition( function( position ) {
                    locate_btn.removeClass( 'dokan-hide' );
                    loader.addClass( 'dokan-hide' );

                    self.latitude = position.coords.latitude,
                    self.longitude = position.coords.longitude,

                    self.geocoder.geocode( {
                        location: {
                            lat: self.latitude,
                            lng: self.longitude,
                        }
                    }, function ( results, status ) {
                        var address = '';

                        if ( 'OK' === status ) {
                            address = results[0].formatted_address;
                        }

                        self.set_address( address );
                        address_input.val( address );
                    } );
                });
            });
        }
    };

    GeolocationFilter.prototype.set_search_term = function ( s ) {
        this.set_param( 's', s );
        this.set_param( 'dokan_seller_search', s );
    };

    GeolocationFilter.prototype.set_address = function ( address ) {
        this.set_param( 'address', address );

        if ( ! this.distance ) {
            var distance = 0,
                slider_val = this.slider.val();

            if ( slider_val ) {
                distance = slider_val;

            } else {
                var min = parseInt( this.slider.attr( 'min' ), 10 ),
                    max = parseInt( this.slider.attr( 'max' ), 10 );

                distance = Math.ceil( ( min + max ) / 2 );
            }

            this.set_param( 'distance', distance );
        }

        this.set_param( 'latitude', this.latitude );
        this.set_param( 'longitude', this.longitude );
    };

    GeolocationFilter.prototype.set_query_params = function () {
        var self = this;

        self.href = window.location.href;

        var search = window.location.search;

        self.base_url = self.href.replace( search, '' );

        search.replace( '?', '' ).split( '&' ).forEach( function ( query ) {
            if ( ! query ) {
                return;
            }

            query = query.split( '=' );

            var param = query[0].toLowerCase(),
                value = query[1];

            switch ( param ) {
                case 'distance':
                    self.distance = parseInt( value, 10 );
                    break;

                case 'latitude':
                    self.latitude = parseFloat( value );
                    break;

                case 'longitude':
                    self.longitude = parseFloat( value );
                    break;

                case 'address':
                    self.address = value;
            }

            if ( self.query_params.indexOf( param ) < 0 ) {
                self.query_params.push( param );
            }

            self.queries[param] = value;
        } );
    };

    GeolocationFilter.prototype.set_param = function ( param, val ) {
        if ( this.query_params.indexOf( param ) < 0 ) {
            this.query_params.push( param );
        }

        this[param] = val;

        if ( val ) {
            this.queries[param] = val;
        } else {
            delete this.queries[param];
        }

        if ( this.scope ) {
            if ( 'distance' === param && ( ! this.latitude || ! this.longitude ) ) {
                return;
            }

            this.redirect( this.scope );
        }
    };

    GeolocationFilter.prototype.redirect = function ( scope ) {
        var search = [],
            param = '';

        for ( param in this.queries ) {
            if ( [ 'post_type', 'dokan_seller_search', 's' ].indexOf( param ) < 0 ) {
                if ( param === 'distance' && ( ! this.latitude || ! this.longitude ) ) {
                    continue;
                }

                search.push( param + '=' + this.queries[ param ] );
            }
        }

        var s = this.s || '',
            dokan_seller_search = this.dokan_seller_search || '',
            base_url = '';

        if ( 'product' === scope ) {
            if ( s ) {
                search.push( 's=' + s );
            }

            search.push( 'post_type=product' );
            base_url = this.form.find( '[name="wc_shop_page"]' ).val();
        } else {
            if ( dokan_seller_search ) {
                search.push( 'dokan_seller_search=' + dokan_seller_search );
            }

            base_url = this.form.find( '[name="dokan_store_listing_page"]' ).val();
        }

        window.location.href = base_url + '?' + search.join( '&' );
    };

    $( '.dokan-geolocation-location-filters' ).each( function () {
        new GeolocationFilter( $( this ) );
    } );
} )( jQuery );
