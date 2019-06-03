<?php
/**
 * Dokan Admin Dashboard Tools Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<div class="wrap">
    <h2><?php _e( 'Dokan Tools', 'dokan' ); ?></h2>

    <?php
    $msg = isset( $_GET['msg'] ) ? $_GET['msg'] : '';
    $text = '';

    switch ($msg) {
        case 'page_installed':
            $text = __( 'Pages have been created and installed!', 'dokan' );
            break;

        case 'regenerated':
            $text = __( 'Order sync table has been regenerated!', 'dokan' );
            break;
    }

    if ( $text ) {
        ?>
        <div class="updated">
            <p>
                <?php echo $text; ?>
            </p>
        </div>

    <?php } ?>

    <style type="text/css">
        .regen-sync-response span, .duplicate-search-response span {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border-color: #faebcc;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid transparent;
            border-radius: 4px;
            display: block;
        }
        .regen-sync-loader, .duplicate-sync-loader {
            background: url('<?php echo admin_url( 'images/spinner-2x.gif') ?>') no-repeat;
            width: 20px;
            height: 20px;
            display: inline-block;
            background-size: cover;
        }

        #progressbar,#duplicate-progressbar{
            background-color: #EEE;
            border-radius: 13px; /* (height of inner div) / 2 + padding */
            padding: 3px;
            margin-bottom : 20px;
        }

        #regen-pro,#duplicate-pro{
            background-color: #00A0D2;
            width: 0%; /* Adjust with JavaScript */
            height: 20px;
            border-radius: 10px;
            text-align: center;
            color:#FFF;
        }
    </style>
    <script type="text/javascript">
        jQuery(function($) {
            var total_orders = 0;
            $('form#regen-sync-table').on('submit', function(e) {
                e.preventDefault();
                var form = $(this),
                    submit = form.find('input[type=submit]'),
                    loader = form.find('.regen-sync-loader');
                    responseDiv = $('.regen-sync-response');

                submit.attr('disabled', 'disabled');
                loader.show();

                var s_data = {
                    data: form.serialize(),
                    action : 'regen_sync_table',
                    total_orders : total_orders
                };

                $.post( ajaxurl, s_data, function(resp) {
                    if ( resp.success ) {
                        if( resp.data.total_orders != 0 ){
                            total_orders = resp.data.total_orders;
                        }
                        completed = (resp.data.done*100)/total_orders;

                        completed = Math.round(completed);

                        $('#regen-pro').width(completed+'%');
                        if(!$.isNumeric(completed)){
                            $('#regen-pro').html('Finished');
                        }else{
                            $('#regen-pro').html(completed+'%');
                        }

                        $('#progressbar').show();


                        responseDiv.html( '<span>' + resp.data.message +'</span>' );

                        if ( resp.data.done != 'All' ) {
                            form.find('input[name="offset"]').val( resp.data.offset );
                            form.submit();
                            return;
                        } else {
                            submit.removeAttr('disabled');
                            loader.hide();
                        }
                    }
                });
            });


            var orders = 0;
            var done = 0;

            $('form#duplicate-order-check').on('submit', function(e){
                e.preventDefault();
                var form = $(this),
                    submit = form.find('input[type=submit]'),
                    loader = form.find('.duplicate-sync-loader');
                    responseDiv = $('.duplicate-search-response');

                submit.attr('disabled', 'disabled');
                loader.show();

                var s_data = {
                    data: form.serialize(),
                    action : 'check_duplicate_suborders',
                    total_orders : orders,
                    done : done
                };
                $.post( ajaxurl, s_data, function(resp) {
                    if ( resp.success ) {
                        if( resp.data.total_orders != 0 ){
                            orders = resp.data.total_orders;
                            done = resp.data.done;
                        }
                        completed = (resp.data.done*100)/orders;

                        completed = Math.round(completed);

                        $('#duplicate-pro').width(completed+'%');
                        if(!$.isNumeric(completed)){
                            $('#duplicate-pro').html('Finished');
                        }else{
                            $('#duplicate-pro').html(completed+'%');
                        }

                        $('#duplicate-progressbar').show();


                        responseDiv.html( '<span>' + resp.data.message +'</span>' );

                        if ( resp.data.done != 'All' ) {
                            form.find('input[name="offset"]').val( resp.data.offset );
                            form.submit();
                            return;
                        } else {
                            submit.removeAttr('disabled');
                            loader.hide();
                            if ( resp.data.duplicate == true ){
                                get_duplicate_orders_list();
                            }

                        }
                    }
                });


            });

            function get_duplicate_orders_list(){
                var s_data = {
                    action : 'print_duplicate_suborders'
                };

                $.post( ajaxurl, s_data, function(resp) {
                    if ( resp.success ) {
                       $('.duplicate-orders-wrapper').html(resp.data.html);
                       $('.duplicate-orders-wrapper').show();

                    }
                });
            }

            $('.duplicate-orders-wrapper').on('click', 'a.dokan-order-action-delete', function(e) {
                e.preventDefault();
                var self = $(this);

                self.closest( 'tr' ).addClass('custom-spinner');
                data = {
                    action: 'dokan_duplicate_order_delete',
                    formData : $('#dokan-duplicate-orders-action').serialize(),
                    order_id : self.closest( 'tr' ).data( 'order-id' )
                }

                $.post(ajaxurl, data, function( resp ) {

                    if( resp.success ) {
                        self.closest( 'tr' ).removeClass('custom-spinner');
                        self.closest( 'tr' ).hide();

                        //alert(resp.data.html);
                    } else {
                        self.closest( 'tr' ).removeClass('custom-spinner');
                        alert( 'Something wrong' );
                    }
                });

            });

            $('.duplicate-orders-wrapper').on('click', 'input.dokan-duplicate-orders-allcheck', function(e) {

                $('input.order-checkbox').attr( 'checked',this.checked);
                $('input.dokan-duplicate-orders-allcheck').attr( 'checked',this.checked);

            });

            $('.duplicate-orders-wrapper').on('click', 'input.dokan-bulk-action', function(e) {
                e.preventDefault();

                if( $('select[name="dokan_duplicate_order_bulk_select"]').val() != 'delete'){
                    return;
                }

                var self = $(this);

                self.closest( 'table' ).addClass('custom-spinner');
                data = {
                    action: 'dokan_duplicate_orders_bulk_delete',
                    formData : $('#dokan-duplicate-orders-action').serialize(),
                }

                $.post(ajaxurl, data, function( resp ) {

                    if( resp.success ) {
                        if( resp.data.status == 1 ){
                            var d_orders = $.parseJSON(resp.data.deleted);

                            $.each(d_orders, function ( key, val ){
                                $('#row-'+val).remove();
                            });

                        }
                        alert(resp.data.msg);

                    } else {
                       alert( '<?php echo esc_js( __( 'Something went wrong!', 'dokan' ) ); ?>' );
                    }
                });

            });

        });
    </script>

    <div class="metabox-holder">
        <div class="postbox">
            <h3><?php _e( 'Page Installation', 'dokan' ); ?></h3>

            <div class="inside">
                <p><?php _e( 'Clicking this button will create required pages for the plugin.', 'dokan' ); ?></p>
                <a class="button button-primary" href="<?php echo wp_nonce_url( add_query_arg( array( 'dokan_action' => 'dokan_install_pages' ), 'admin.php?page=dokan-tools' ), 'dokan-tools-action' ); ?>"><?php _e( 'Install Dokan Pages', 'dokan' ); ?></a>
            </div>
        </div>

        <div class="postbox">
            <h3><?php _e( 'Regenerate Order Sync Table', 'dokan' ); ?></h3>

            <div class="inside">
                <p><?php _e( 'This tool will delete all orders from the Dokan\'s sync table and re-build it.', 'dokan' ); ?></p>
                <div class="regen-sync-response"></div>
                <div id="progressbar" style="display: none">
                    <div id="regen-pro" >0</div>
                </div>
                <form id="regen-sync-table" action="" method="post">
                    <?php wp_nonce_field( 'regen_sync_table' ); ?>
                    <input type="hidden" name="limit" value="<?php echo apply_filters( 'regen_sync_table_limit', 100 ); ?>">
                    <input type="hidden" name="offset" value="0">
                    <input type="submit" class="button button-primary" value="<?php _e( 'Re-build', 'dokan' ); ?>" >
                    <span class="regen-sync-loader" style="display:none"></span>
                </form>
            </div>
        </div>
<!--    check for duplicate orders first-->
        <div class="postbox">
            <h3><?php _e( 'Check for Duplicate Orders', 'dokan' ); ?></h3>

            <div class="inside">
                <p><?php _e( 'This tool will check for duplicate orders from the Dokan\'s sync table.', 'dokan' ); ?></p>
                <div class="duplicate-search-response"></div>
                <div id="duplicate-progressbar" style="display: none">
                    <div id="duplicate-pro" >0</div>
                </div>
                <form id="duplicate-order-check" action="" method="post">
                    <?php wp_nonce_field( 'regen_sync_table' ); ?>
                    <input type="hidden" name="limit" value="<?php echo apply_filters( 'duplicate-order-check-limit', 100 ); ?>">
                    <input type="hidden" name="offset" value="0">
                    <input type="submit" class="button button-primary" value="<?php _e( 'Check Orders', 'dokan' ); ?>" >
                    <span class="duplicate-sync-loader" style="display:none"></span>
                </form>
            </div>
        </div>

        <div class="postbox duplicate-orders-wrapper" style="display: none"></div>
    </div>