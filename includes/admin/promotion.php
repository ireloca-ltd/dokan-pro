<?php

if ( ! class_exists( 'WeDevs_Promotion' ) ) {
    require_once DOKAN_LIB_DIR . '/promotions.php';
}

/**
* Promotion class
*
* For displaying AI base add on admin panel
*/
class Dokan_Pro_Promotion extends WeDevs_Promotion {

    /**
     * Time interval for displaying promo
     *
     * @var integer
     */
    public $time_interval = 60*60*24*7;

    /**
     * Upgrade option key
     *
     * @var string
     */
    public $promo_option_key = '_dokan_pro_upgrade_promo';
    /**
     * Get prmotion data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_promotion_data() {

        $license = get_option( 'dokan_license' );
        $query_arg = '';

        if ( ! empty( $license ) ) {
            $query_arg = '&wedevs_upgrade=dokan&license=' . $license['key'] . '&email=' . $license['email'];
        }

        if ( 'dokan-starter' == Dokan_Pro::init()->get_plan() || 'dokan-professional' == Dokan_Pro::init()->get_plan() )  {
            return array(
                'staff_manager' => array(
                    'title'     => __( 'Want your vendors to better manage their store?', 'dokan' ),
                    'content'   => __( 'Attract your vendors With Better Store Management. Start with the Vendor Staff Manager module to let them manage their own store with an unlimited number of staffs.', 'dokan' ),
                    'thumbnail' => 'https://cdn.wedevs.com/uploads/2018/02/Vendor-Staff-Manager-2.png',
                    'link'      => 'https://wedevs.com/dokan/features/let-your-vendors-manage-their-stores-with-staffs/?utm_source=dokan-plugin&utm_medium=banner&utm_content=staff-manager&utm_campaign=upgrade-package'. $query_arg,
                    'priority'  => 10,
                ),

                'export_import' => array(
                    'title'     => __( 'Why would vendors from other marketplaces want to start their business in your platform?', 'dokan' ),
                    'content'   => __( 'Because they wouldn’t need to start from scratch and create/import their products within minutes using the Export/Import module.', 'dokan' ),
                    'thumbnail' => 'https://cdn.wedevs.com/uploads/2018/03/Export-Import-dokan.png',
                    'link'      => 'https://wedevs.com/dokan/features/attract-other-store-vendors/?utm_source=dokan-plugin&utm_medium=banner&utm_content=export-import&utm_campaign=upgrade-package'. $query_arg,
                    'priority'  => 20,
                ),

                'live_chat' => array(
                    'title'     => __( '77% of customers won’t make a purchase if there’s no live chat feature', 'dokan' ),
                    'content'   => __( 'Let Your Customers Communicate With Vendors In Real time with the live chat module. Attain more customers than ever before.', 'dokan' ),
                    'thumbnail' => 'https://cdn.wedevs.com/uploads/2018/05/Dokan_Live_Chat.png',
                    'link'      => 'https://wedevs.com/dokan/features/retain-customers-using-live-chat/?utm_source=dokan-plugin&utm_medium=banner&utm_content=live-chat&utm_campaign=upgrade-package'. $query_arg,
                    'priority'  => 30,
                ),

                'vendor_vacation' => array(
                    'title'     => __( 'Avoid embarrassing situations', 'dokan' ),
                    'content'   => __( 'Let your customers know when a vendor store Is open or the vendor is on vacation with the Vendor Vacation module.', 'dokan' ),
                    'thumbnail' => 'https://cdn.wedevs.com/uploads/2018/01/Seller-Vacation.jpg',
                    'link'      => 'https://wedevs.com/dokan/features/let-your-vendor-take-vacation/?utm_source=dokan-plugin&utm_medium=banner&utm_content=vendor-vacation&utm_campaign=upgrade-package'. $query_arg,
                    'priority'  => 40,
                ),
            );
        };
    }
}
