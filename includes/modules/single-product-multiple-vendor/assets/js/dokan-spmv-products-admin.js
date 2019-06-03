(function($) {
    $('#dokan-spmv-products-admin-assign-vendors').selectWoo({
        minimumInputLength: 3,
        closeOnSelect: false,
        ajax: {
            url: dokan_admin.ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'dokan_spmv_products_admin_search_vendors',
                    _wpnonce: dokan_admin.nonce,
                    s: params.term,
                    product_id: dokan_admin.dokanSPMVAdmin.product_id
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.data.vendors,
                    pagination: {
                        more: false,// (params.page * 30) < data.total_count
                    }
                }
            },
            cache: true
        },
        language: {
            errorLoading: function () {
                return dokan_admin.dokanSPMVAdmin.i18n.error_loading;
            },
            searching: function () {
                return dokan_admin.dokanSPMVAdmin.i18n.searching + '...';
            },
            inputTooShort: function () {
                return dokan_admin.dokanSPMVAdmin.i18n.input_too_short + '...';
            }
        },
        escapeMarkup: function (markup) { return markup; },
        templateResult: function (vendor) {
            if (vendor.loading) {
                return vendor.text;
            }

            var markup = "<div class='dokan-spmv-vendor-dropdown-results clearfix'>" +
            "<div class='dokan-spmv-vendor-dropdown-results__avatar'><img src='" + vendor.avatar + "' /></div>" +
            "<div class='dokan-spmv-vendor-dropdown-results__title'>" + vendor.name + "</div></div>";

            return markup;
        },
        templateSelection: function (vendor) {
            return vendor.name;
        }
    });

    $('#dokan-spmv-products-admin-assign-vendors-btn').on('click', function (e) {
        e.preventDefault();

        var button = $(this);
        var select = $('#dokan-spmv-products-admin-assign-vendors');
        var vendors = select.selectWoo('val');

        if (vendors && vendors.length) {
            button.prop('disabled', true);
            select.prop('disabled', true);

            $.ajax({
                url: dokan_admin.ajaxurl,
                method: 'post',
                dataType: 'json',
                data: {
                    action: 'dokan_spmv_products_admin_assign_vendors',
                    _wpnonce: dokan_admin.nonce,
                    product_id: dokan_admin.dokanSPMVAdmin.product_id,
                    vendors: vendors,
                }
            }).done(function (response) {
                window.location.href = window.location.href;
            }).always(function () {
                button.prop('disabled', true);
                select.prop('disabled', true);
            });
        }
    });

    $('#dokan-spmv-products-admin .delete-product').on('click', function (e) {
        e.preventDefault();

        if (confirm(dokan_admin.dokanSPMVAdmin.i18n.confirm_delete)) {
            var product_id = $(this).data('product-id');

            $.ajax({
                url: dokan_admin.ajaxurl,
                method: 'post',
                dataType: 'json',
                data: {
                    action: 'dokan_spmv_products_admin_delete_clone_product',
                    _wpnonce: dokan_admin.nonce,
                    product_id: product_id
                }
            }).done(function (response) {
                window.location.href = window.location.href;
            });
        }

    });
})(jQuery);
