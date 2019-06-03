<?php

namespace DokanPro\Brands;

class AdminSettings {

    /**
     * Class constructor
     *
     * @since 2.9.7
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'dokan_settings_fields', [ self::class, 'add_admin_settings_fields' ], 11, 2 );
    }

    /**
     * Add admin settings for Brands feature
     *
     * @since 2.9.7
     *
     * @param array           $settings_fields
     * @param \Dokan_Settings $dokan_settings
     *
     * @return array
     */
    public static function add_admin_settings_fields( $settings_fields, $dokan_settings ) {
        $brands_settings = [
            'product_brands_mode' => [
                'name'    => 'product_brands_mode',
                'label'   => __( 'Brands Selection Mode', 'dokan' ),
                'desc'    => __( 'Enable single or multiple brand selection mode for vendors.', 'dokan' ),
                'type'    => 'select',
                'default' => 'single',
                'options' => [
                    'single'   => __( 'Single', 'dokan' ),
                    'multiple' => __( 'Multiple', 'dokan' )
                ]
            ]
        ];

        return $dokan_settings->add_settings_after(
            $settings_fields,
            'dokan_selling',
            'product_vendors_can_create_tags',
            $brands_settings
        );
    }
}
