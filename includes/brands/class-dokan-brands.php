<?php

namespace DokanPro\Brands;

use Dokan\Traits\Singleton;

class Brands {

    use Singleton;

    /**
     * Is YITH plugin active or not
     *
     * @since 2.9.7
     *
     * @var bool
     */
    public $is_active = false;

    /**
     * Is YITH premium plugin active or not
     *
     * @since 2.9.7
     *
     * @var bool
     */
    public $is_premium_active = false;

    /**
     * Feature related admin settings
     *
     * @since 2.9.7
     *
     * @var array
     */
    public $settings = [];

    /**
     * Executes after first class instantiation
     *
     * @since 2.9.7
     *
     * @return void
     */
    public function boot() {
        add_filter( 'dokan_get_class_container', [ $this, 'add_dokan_container' ] );
        add_action( 'yith_wcbr_init', [ $this, 'init' ], 11 );
    }

    /**
     * Initiate functionalities
     *
     * @since 2.9.7
     *
     * @return void
     */
    public function init() {
        $this->is_active = true;

        if ( class_exists('YITH_WCBR_Premium') ) {
            $this->is_premium_active = true;
        }

        $this->settings = [
            'mode' => dokan_get_option( 'product_brands_mode', 'dokan_selling', 'single' ),
        ];

        $this->includes();
        $this->instances();
    }

    /**
     * Add `brands` container to WeDevs_Dokan class
     *
     * @since 2.9.7
     *
     * @param array $container
     *
     * @return array
     */
    public function add_dokan_container( $container ) {
        $container['brands'] = Brands::instance();
        return $container;
    }

    /**
     * Include feature related files
     *
     * @since 2.9.7
     *
     * @return void
     */
    protected function includes() {
        require_once DOKAN_PRO_INC . '/brands/class-dokan-brands-admin-settings.php';
        require_once DOKAN_PRO_INC . '/brands/class-dokan-brands-form-fields.php';
    }

    /**
     * Feature related class instances
     *
     * @since 2.9.7
     *
     * @return void
     */
    protected function instances() {
        new \DokanPro\Brands\AdminSettings();
        new \DokanPro\Brands\FormFields();
    }

    /**
     * Get Brand taxonomy
     *
     * When premium addon is active, admin can switch
     * taxonomy from admin panel settings
     *
     * @since 2.9.7
     *
     * @return string
     */
    public function get_taxonomy() {
        $yith_wcbr = YITH_WCBR();
        $taxonomy = $yith_wcbr::$brands_taxonomy;

        if ( $this->is_premium_active ) {
            $yith_wcbr_premium = YITH_WCBR_Premium();
            $taxonomy = $yith_wcbr_premium::$brands_taxonomy;
        }

        return $taxonomy;
    }
}

Brands::instance();
