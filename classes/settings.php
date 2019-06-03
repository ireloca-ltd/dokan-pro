<?php

/**
 * Dokan Pro Template Settings class
 *
 * @since 2.4
 *
 * @package dokan
 */
class Dokan_Pro_Settings extends Dokan_Template_Settings {

    /**
     * Load automatically when class initiate
     *
     * @since 2.4
     *
     * @uses actions hook
     * @uses filter hook
     *
     * @return void
     */
    public function __construct() {
        $this->currentuser = dokan_get_current_user_id();
        $this->profile_info = dokan_get_store_info( dokan_get_current_user_id() );

        add_filter( 'dokan_get_dashboard_settings_nav', array( $this, 'load_settings_menu' ), 10 );
        add_filter( 'dokan_dashboard_nav_active', array( $this, 'filter_nav_active' ), 10, 3 );
        add_filter( 'dokan_dashboard_settings_heading_title', array( $this, 'load_settings_header' ), 10, 2 );
        add_filter( 'dokan_dashboard_settings_helper_text', array( $this, 'load_settings_helper_text' ), 10, 2 );

        add_action( 'dokan_ajax_settings_response', array( $this, 'add_progressbar_in_settings_save_response' ), 10 );
        add_action( 'dokan_settings_load_ajax_response', array( $this, 'render_pro_settings_load_progressbar' ), 25 );
        add_action( 'dokan_settings_render_profile_progressbar', array( $this, 'load_settings_progressbar' ), 10, 2 );
        add_action( 'dokan_settings_content_area_header', array( $this, 'render_shipping_status_message' ), 25 );
        add_action( 'dokan_render_settings_content', array( $this, 'load_settings_content' ), 10 );
        add_action( 'dokan_settings_form_bottom', array( $this, 'add_discount_option' ), 10, 2 );
        add_action( 'dokan_store_profile_saved', array( $this, 'save_store_data' ), 10, 2 );
    }

    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {

        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Settings();
        }

        return $instance;
    }

    /**
     * filter_nav_active
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function filter_nav_active( $active_menu, $request, $active ) {

        if ( 'settings/regular-shipping' == $active_menu ) {
            return 'settings/shipping';
        }

        return $active_menu;
    }


    /**
     * Load Settings Menu for Pro
     *
     * @since 2.4
     *
     * @param  array $sub_settins
     *
     * @return array
     */
    public function load_settings_menu( $sub_settins ) {

        $dokan_shipping_option = get_option( 'woocommerce_dokan_product_shipping_settings' );
        $enable_shipping       = ( isset( $dokan_shipping_option['enabled'] ) ) ? $dokan_shipping_option['enabled'] : 'yes';
        $disable_woo_shipping  = get_option( 'woocommerce_ship_to_countries' );

        if ( $disable_woo_shipping != 'disabled' ) {
            $sub_settins['shipping'] = array(
                'title'      => __( 'Shipping', 'dokan' ),
                'icon'       => '<i class="fa fa-truck"></i>',
                'url'        => dokan_get_navigation_url( 'settings/shipping' ),
                'pos'        => 70,
                'permission' => 'dokan_view_store_shipping_menu'
            );
        }

        $sub_settins['social'] = array(
            'title'      => __( 'Social Profile', 'dokan' ),
            'icon'       => '<i class="fa fa-share-alt-square"></i>',
            'url'        => dokan_get_navigation_url( 'settings/social' ),
            'pos'        => 90,
            'permission' => 'dokan_view_store_social_menu'
        );

        if ( dokan_get_option( 'store_seo', 'dokan_general', 'on' ) === 'on' ) {
            $sub_settins['seo'] = array(
                'title'      => __( 'Store SEO', 'dokan' ),
                'icon'       => '<i class="fa fa-globe"></i>',
                'url'        => dokan_get_navigation_url( 'settings/seo' ),
                'pos'        => 110,
                'permission' => 'dokan_view_store_seo_menu'
            );
        }

        return $sub_settins;
    }

    /**
     * Load Settings Template
     *
     * @since 2.4
     *
     * @param  string $template
     * @param  array $query_vars
     *
     * @return void
     */
    public function load_settings_template( $template, $query_vars ) {

        if ( $query_vars == 'social' ) {
            dokan_get_template_part( 'settings/store' );
            return;
        }

        if ( $query_vars == 'shipping' ) {
            dokan_get_template_part( 'settings/store' );
            return;
        }

        if ( $query_vars == 'seo' ) {
            dokan_get_template_part( 'settings/store' );
            return;
        }
    }

    /**
     * Load Settings Header
     *
     * @since 2.4
     *
     * @param  string $header
     * @param  array $query_vars
     *
     * @return string
     */
    public function load_settings_header( $header, $query_vars ) {
        if ( $query_vars == 'social' ) {
            $header = __( 'Social Profiles', 'dokan' );
        }

        if ( $query_vars == 'shipping' ) {
            $settings_url = dokan_get_navigation_url( 'settings/shipping' ) . '#/settings';
            $header = sprintf( '%s <span style="position:absolute; right:0px;"><a href="%s" class="dokan-btn dokan-btn-default"><i class="fa fa-gear"></i></a></span>', __( 'Shipping Settings', 'dokan' ), $settings_url ) ;
        }

        if ( $query_vars == 'seo' ) {
            $header = __( 'Store SEO', 'dokan' );
        }

        return $header;
    }

    /**
     * Load Settings Progressbar
     *
     * @since 2.4
     *
     * @param  $array $query_vars
     *
     * @return void
     */
    public function render_pro_settings_load_progressbar() {
        global $wp;

        if ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'store' ) {
            echo dokan_get_profile_progressbar();
        }

        if ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'payment' ) {
            echo dokan_get_profile_progressbar();
        }

        if ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'social' ) {
            echo dokan_get_profile_progressbar();
        }

    }

    /**
     * Add progressbar in settings save feedback message
     *
     * @since 2.4
     *
     * @param array $message
     *
     * @return array
     */
    public function add_progressbar_in_settings_save_response( $message ) {
        $progress_bar = dokan_get_profile_progressbar();
        $message['progress'] = $progress_bar;

        return $message;
    }

    /**
     * Load Settings page helper
     *
     * @since 2.4
     *
     * @param  string $help_text
     * @param  array $query_vars
     *
     * @return string
     */
    public function load_settings_helper_text( $help_text, $query_vars ) {
        $dokan_shipping_option = get_option( 'woocommerce_dokan_product_shipping_settings' );
        $enable_shipping       = ( isset( $dokan_shipping_option['enabled'] ) ) ? $dokan_shipping_option['enabled'] : 'yes';

        if ( $query_vars == 'social' ) {
            $help_text = __( 'Social profiles help you to gain more trust. Consider adding your social profile links for better user interaction.', 'dokan' );
        }

        if ( $query_vars == 'shipping' ) {

            $help_text = sprintf ( '<p>%s</p>',
                __( 'A shipping zone is a geographic region where a certain set of shipping methods are offered. We will match a customer to a single zone using their shipping address and present the shipping methods within that zone to them.', 'dokan' ),
                __( 'If you want to use the previous shipping system then', 'dokan' ),
                esc_url( dokan_get_navigation_url('settings/regular-shipping' ) ),
                __( 'Click Here', 'dokan' )
            );

            if ( 'yes' == $enable_shipping ) {
                $help_text .= sprintf ( '<p>%s <a href="%s">%s</a></p>',
                    __( 'If you want to use the previous shipping system then', 'dokan' ),
                    esc_url( dokan_get_navigation_url('settings/regular-shipping' ) ),
                    __( 'Click Here', 'dokan' )
                );
            }
        }

        if ( $query_vars == 'regular-shipping' && $enable_shipping == 'yes' ) {
            $help_text = sprintf ( '<p>%s</p><p>%s</p><p>%s <a href="%s">%s</a></p>',
                __( 'This page contains your store-wide shipping settings, costs, shipping and refund policy.', 'dokan' ),
                __( 'You can enable/disable shipping for your products. Also you can override these shipping costs while creating or editing a product.', 'dokan' ),
                __( 'If you want to configure zone wise shipping then', 'dokan' ),
                esc_url( dokan_get_navigation_url('settings/shipping' ) ),
                __( 'Click Here', 'dokan' )
            );
        }

        return $help_text;
    }

    /**
     * Load Settings Content
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return void
     */
    public function load_settings_content( $query_vars ) {

        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'social' ) {
             if ( ! current_user_can( 'dokan_view_store_social_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan' ) ) );
            } else {
                $this->load_social_content();
            }
        }

        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'shipping' ) {
             if ( ! current_user_can( 'dokan_view_store_shipping_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan' ) ) );
            } else {
                $disable_woo_shipping  = get_option( 'woocommerce_ship_to_countries' );

                if ( 'disabled' == $disable_woo_shipping ) {
                    dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'Shipping functionality is currentlly disabled by site owner', 'dokan' ) ) );
                } else {
                    $this->load_shipping_content();
                }
            }
        }

        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'regular-shipping' ) {
             if ( ! current_user_can( 'dokan_view_store_shipping_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan' ) ) );
            } else {
                $disable_woo_shipping  = get_option( 'woocommerce_ship_to_countries' );
                $dokan_shipping_option = get_option( 'woocommerce_dokan_product_shipping_settings' );
                $enable_shipping       = ( isset( $dokan_shipping_option['enabled'] ) ) ? $dokan_shipping_option['enabled'] : 'yes';

                if ( 'disabled' == $disable_woo_shipping || 'no' == $enable_shipping ) {
                    dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'Shipping functionality is currentlly disabled by site owner', 'dokan' ) ) );
                } else {
                    dokan_get_template_part( 'settings/shipping', '', array( 'pro' => true ) );
                }

            }
        }

        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'seo' ) {
             if ( ! current_user_can( 'dokan_view_store_seo_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan' ) ) );
            } else {
                $this->load_seo_content();
            }
        }
    }

    /**
     * Load Social Page Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_social_content() {
        $social_fields = dokan_get_social_profile_fields();

        dokan_get_template_part( 'settings/social', '', array(
            'pro'           => true,
            'social_fields' => $social_fields,
            'current_user'  => $this->currentuser,
            'profile_info'  => $this->profile_info,
        ) );
    }

    /**
     * Load Shipping Page Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_shipping_content() {
        echo "<div id='dokan-vue-shipping'></div>";
    }

    /**
     * Render Shipping status message
     *
     * @since 2.4
     *
     * @return void
     */
    public function render_shipping_status_message() {
        if ( isset( $_GET['message'] ) && $_GET['message'] == 'shipping_saved' ) {
            dokan_get_template_part( 'global/dokan-message', '', array(
                'message' => __( 'Shipping options saved successfully', 'dokan' )
            ) );
        }
    }

    /**
     * Load SEO Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_seo_content() {
        dokan_get_template_part( 'settings/seo', '', array( 'pro' => true ) );
    }

    /**
    * Render discount options
    *
    * @since 2.6
    *
    * @return void
    **/
    public function add_discount_option( $current_user, $profile_info ) {
        $is_enable_op_discount = dokan_get_option( 'discount_edit', 'dokan_selling' );
        $is_enable_op_discount = $is_enable_op_discount ? $is_enable_op_discount : array();
        $is_enable_order_discount = isset( $profile_info['show_min_order_discount'] ) ? $profile_info['show_min_order_discount'] : 'no';
        $setting_minimum_order_amount = isset( $profile_info['setting_minimum_order_amount'] ) ? $profile_info['setting_minimum_order_amount'] : '';
        $setting_order_percentage = isset( $profile_info['setting_order_percentage'] ) ? $profile_info['setting_order_percentage'] : '';

        dokan_get_template_part( 'settings/discount', '', array(
            'pro'                          => true,
            'is_enable_op_discount'        => $is_enable_op_discount,
            'is_enable_order_discount'     => $is_enable_order_discount,
            'setting_minimum_order_amount' => $setting_minimum_order_amount,
            'setting_order_percentage'     => $setting_order_percentage
        ) );
    }

    /**
    * Save doscount settings data
    *
    * @since 2.6
    *
    * @return void
    **/
    public function save_store_data( $store_id, $dokan_settings ) {
        if ( ! $store_id ) {
            return;
        }

        $dokan_settings = get_user_meta( $store_id, 'dokan_profile_settings', true );

        $profile_completeness = $this->calculate_profile_completeness_value( $dokan_settings );
        $dokan_settings['profile_completion'] = $profile_completeness;


        // Set discount data in seller profile
        $data = array(
            'show_min_order_discount'      => isset( $_POST['setting_show_minimum_order_discount_option'] ) ? 'yes' : 'no',
            'setting_minimum_order_amount' => isset( $_POST['setting_minimum_order_amount'] ) ? sanitize_text_field( $_POST['setting_minimum_order_amount'] ) : '',
            'setting_order_percentage'     => isset( $_POST['setting_order_percentage'] ) ? sanitize_text_field( $_POST['setting_order_percentage'] ) : '',
        );

        $settings_data = wp_parse_args( $data, $dokan_settings );

        update_user_meta( $store_id, 'dokan_profile_settings', $settings_data );
    }

    /**
     * Calculate Profile Completeness meta value
     *
     * @since 2.1
     *
     * @param  array  $dokan_settings
     *
     * @return array
     */
    function calculate_profile_completeness_value( $dokan_settings ) {

        $profile_val = 0;
        $next_add    = '';
        $track_val   = array();

        $progress_values = array(
           'banner_val'          => 15,
           'profile_picture_val' => 15,
           'store_name_val'      => 10,
           'social_val'          => array(
               'fb'       => 2,
               'gplus'    => 2,
               'twitter'  => 2,
               'youtube'  => 2,
               'linkedin' => 2,
           ),
           'payment_method_val'  => 15,
           'phone_val'           => 10,
           'address_val'         => 10,
           'map_val'             => 15,
        );

        // setting values for completion
        $progress_values = apply_filters('dokan_profile_completion_values', $progress_values );

        extract( $progress_values );

        //settings wise completeness section
        if ( isset( $profile_picture_val ) && isset( $dokan_settings['gravatar'] ) ):
            if ( $dokan_settings['gravatar'] != 0 ) {
                $profile_val           = $profile_val + $profile_picture_val;
                $track_val['gravatar'] = $profile_picture_val;
            } else {
                if ( strlen( $next_add ) == 0 ) {
                    $next_add = 'profile_picture_val';
                }
            }
        endif;

        // Calculate Social profiles
        if ( isset( $social_val ) && isset( $dokan_settings['social'] ) ):

            foreach ( $dokan_settings['social'] as $key => $value ) {

                if ( isset( $social_val[$key] ) && $value != false ) {
                    $profile_val     = $profile_val + $social_val[$key];
                    $track_val[$key] = $social_val[$key];
                }

                if ( isset( $social_val[$key] ) && $value == false ) {

                    if ( strlen( $next_add ) == 0 ) {
                        $next_add = 'social_val' . '-' . $key;
                    }
                }
            }
        endif;

        //calculate completeness for phone
        if ( isset( $phone_val ) && isset( $dokan_settings['phone'] ) ):

            if ( strlen( trim( $dokan_settings['phone'] ) ) != 0 ) {
                $profile_val        = $profile_val + $phone_val;
                $track_val['phone'] = $phone_val;
            } else {
                if ( strlen( $next_add ) == 0 ) {
                    $next_add = 'phone_val';
                }
            }

        endif;

        //calculate completeness for banner
        if ( isset( $banner_val ) && isset( $dokan_settings['banner'] ) ):

            if ( $dokan_settings['banner'] != 0 ) {
                $profile_val         = $profile_val + $banner_val;
                $track_val['banner'] = $banner_val;
            } else {
                $next_add = 'banner_val';
            }

        endif;

        //calculate completeness for store name
        if ( isset( $store_name_val ) && isset( $dokan_settings['store_name'] ) ):
            if ( isset( $dokan_settings['store_name'] ) ) {
                $profile_val             = $profile_val + $store_name_val;
                $track_val['store_name'] = $store_name_val;
            } else {
                if ( strlen( $next_add ) == 0 ) {
                    $next_add = 'store_name_val';
                }
            }
        endif;

        //calculate completeness for address
        if ( isset( $address_val ) && isset( $dokan_settings['address'] ) ):
            if ( !empty($dokan_settings['address']['street_1']) ) {
                $profile_val          = $profile_val + $address_val;
                $track_val['address'] = $address_val;
            } else {
                if ( strlen( $next_add ) == 0 ) {
                    $next_add = 'address_val';
                }
            }
        endif;

        // Calculate Payment method val for Bank
        if ( isset( $dokan_settings['payment'] ) && isset( $dokan_settings['payment']['bank'] ) ) {
            $count_bank = true;

            // if any of the values for bank details are blank, check_bank will be set as false
            foreach ( $dokan_settings['payment']['bank'] as $value ) {
                if ( strlen( trim( $value )) == 0)   {
                    $count_bank = false;
                }
            }

            if ( $count_bank ) {
                $profile_val        = $profile_val + $payment_method_val;
                $track_val['Bank']  = $payment_method_val;
                $payment_method_val = 0;
                $payment_added      = 'true';
            }
        }

        // Calculate Payment method val for Paypal
        if ( isset( $dokan_settings['payment'] ) && isset( $dokan_settings['payment']['paypal'] ) ) {
            $p_email = isset($dokan_settings['payment']['paypal']['email']) ? $dokan_settings['payment']['paypal']['email'] : false;
            if ( $p_email != false ) {

                $profile_val         = $profile_val + $payment_method_val;
                $track_val['paypal'] = $payment_method_val;
                $payment_method_val  = 0;
            }
        }

        // Calculate Payment method val for skrill
        if ( isset( $dokan_settings['payment'] ) && isset( $dokan_settings['payment']['skrill'] ) ) {

            $s_email = isset( $dokan_settings['payment']['skrill']['email'] ) ? $dokan_settings['payment']['skrill']['email'] : false;
            if ( $s_email != false ) {
                $profile_val         = $profile_val + $payment_method_val;
                $track_val['skrill'] = $payment_method_val;
                $payment_method_val  = 0;
            }
        }

        // Calculate Payment method val for stripe
        if ( isset( $dokan_settings['payment'] ) && isset( $dokan_settings['payment']['stripe'] ) ) {

            if ( $dokan_settings['payment']['stripe'] ) {
                $profile_val         = $profile_val + $payment_method_val;
                $track_val['stripe'] = $payment_method_val;
                $payment_method_val  = 0;
            }
        }

        // set message if no payment method found
        if ( strlen( $next_add ) == 0 && $payment_method_val !=0 ) {
            $next_add = 'payment_method_val';
        }

        if ( isset( $dokan_settings['location'] ) && strlen(trim($dokan_settings['location'])) != 0 ) {
            $profile_val           = $profile_val + $map_val;
            $track_val['location'] = $map_val;
        } else {
            if ( strlen( $next_add ) == 0 ) {
                $next_add = 'map_val';
            }
        }

        $track_val['next_todo']     = $next_add;
        $track_val['progress']      = $profile_val;
        $track_val['progress_vals'] = $progress_values;

        return apply_filters( 'dokan_profile_completion_progress_value', $track_val ) ;
    }

}
