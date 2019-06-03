<?php

namespace DokanPro\Modules\Subscription;

use Dokan\Traits\Singleton;
use DokanPro\Modules\Subscription\Helper;
use DokanPro\Modules\Subscription\SubscriptionPack;

/**
* Description of Pack_On_Registration
*
* Show dropdown of Subscription packs on Registration form
*
* @author WeDevs
*
* @since 1.0.2
*/
class Registration {
    use Singleton;

    /**
     * Boot method
     *
     * @return void
     */
    public function boot() {
        $this->init_hooks();
    }

    /**
     * Init hooks and filters
     *
     * @return void
     */
    function init_hooks() {
        add_action( 'dokan_seller_registration_field_after', array( $this, 'generate_form_fields' ) );
        add_action( 'dokan_after_seller_migration_fields', array( $this, 'generate_form_fields') );
        add_filter( 'woocommerce_registration_redirect', array( $this, 'redirect_to_checkout' ), 99, 1 );
        add_filter( 'dokan_customer_migration_required_fields', array( $this, 'add_subscription_to_dokan_customer_migration_required_fields' ) );
        add_filter( 'dokan_customer_migration_redirect', array( $this, 'redirect_after_migration' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'redirect_to_seller_setup_wizard_after_checkout' ) );
        add_action( 'dokan_seller_wizard_introduction', array( $this, 'make_vendor_has_seen_setup_wizard' ) );
    }

    /**
     * Generate select options and details for created subscription packs
     *
     * @since 1.0.2
     *
     */
    public function generate_form_fields() {
        $subscription_packs         = dokan()->subscription->all();
        $available_recurring_period = Helper::get_subscription_period_strings();

        $packs = $subscription_packs->get_posts();

        //if packs not empty show dropdown
        if ( empty( $packs ) ) {
            return;
        }
        ?>
        <label for="dokan-subscription-pack"><?php _e( 'Choose Subscription Pack', 'dokan' ) ?><span class="required"> *</span></label>
        <div class="form-row form-group form-row-wide dps-pack-wrappper" style="border: 1px solid #D3CED2;">

            <select required="required" class="dokan-form-control" name="dokan-subscription-pack" id="dokan-subscription-pack">
                <?php
                while ( $subscription_packs->have_posts() ) {
                    $subscription_packs->the_post();
                    ?>
                    <option value="<?php echo get_the_ID() ?>"><?php echo the_title() ?></option>
                    <?php
                }
                ?>
            </select>
            <?php
            while ( $subscription_packs->have_posts() ) {
                $subscription_packs->the_post();

                // get individual subscriptoin pack details
                $sub_pack           = dokan()->subscription->get( get_the_ID() );
                $is_recurring       = $sub_pack->is_recurring();
                $recurring_interval = $sub_pack->get_recurring_interval();
                $recurring_period   = $sub_pack->get_period_type();
                ?>

                <div class="dps-pack dps-pack-<?php echo get_the_ID() ?>">
                    <div class="dps-pack-price">

                        <span class="dps-amount">
                            <i>
                                <?php _e( 'Price :', 'dokan' ) ?>
                                <?php if ( get_post_meta( get_the_ID(), '_regular_price', true ) == '0' ): ?>
                                    <?php _e( 'Free', 'dokan' ); ?>
                                <?php else: ?>
                                    <?php if ( get_post_meta( get_the_ID(), '_sale_price', true ) ): ?>
                                        <strike><?php echo get_woocommerce_currency_symbol() . get_post_meta( get_the_ID(), '_regular_price', true ); ?></strike> <?php echo get_woocommerce_currency_symbol() . get_post_meta( get_the_ID(), '_sale_price', true ); ?>
                                    <?php else: ?>
                                        <?php echo get_woocommerce_currency_symbol() . get_post_meta( get_the_ID(), '_regular_price', true ); ?>
                                    <?php endif ?>
                                <?php endif; ?>
                            </i>
                        </span>

                        <?php if ( $is_recurring && $recurring_interval === 1 ) { ?>
                            <span class="dps-rec-period">
                                <span class="sep">/</span><?php echo isset( $available_recurring_period[$recurring_period] ) ? $available_recurring_period[$recurring_period] : ''; ?>
                            </span>
                        <?php } ?>
                    </div><!-- .pack_price -->

                    <div class="pack_content">
                        <b><?php the_title(); ?></b>

                        <?php the_content(); ?>

                        <?php if ( $is_recurring && $recurring_interval > 1 ) { ?>
                            <span class="dps-rec-period">
                                <i>
                                    <?php printf( __( 'In every %d %s(s)', 'dokan' ), $recurring_interval, $recurring_period ); ?>
                                </i>
                            </span>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
            ?>

        </div>
            <?php
            wp_reset_query();
        }

    /**
     * Redirect users to checkout directly with selected
     * subscription added in cart
     *
     * @since 1.0.2
     * @param string redirect_url
     *
     * @return string redirect_url
     */
    public function redirect_to_checkout( $redirect_url ) {

        if ( current_user_can( 'dokandar' ) && Helper::is_subscription_enabled_on_registration() ) {

            if ( ! isset( $_POST['dokan-subscription-pack'] ) ) {
                return $redirect_url;
            }

            return get_site_url() . '/?add-to-cart=' . $_POST['dokan-subscription-pack'];
        }

        return $redirect_url;
    }


    /**
    * Check if subscriptin pack is selected
    * @since 1.1.5
    * @param array $fields
    * @return array $fields
    */
    public function add_subscription_to_dokan_customer_migration_required_fields( $fields ) {
        $fields['dokan-subscription-pack'] = __( 'Select subscription a pack', 'dokan' );

        return $fields;
    }

    /**
    * Redirect after migration
    * @since 1.1.5
    * @param string $url
    * @return string
    */
    public function redirect_after_migration( $url ) {
        if ( isset( $_POST['dokan-subscription-pack'] ) ) {
            return get_site_url() . '/?add-to-cart=' . $_POST['dokan-subscription-pack'];
        }

        return $url;
    }

    /**
     * Get subscription pack id
     *
     * @return string
     */
    public function redirect_to_seller_setup_wizard_after_checkout( $order_id ) {
        $order = wc_get_order( $order_id );
        $items = $order->get_items( 'line_item' );

        if ( empty( $items ) || ! is_array( $items ) ) {
            return;
        }

        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            break;
        }

        if ( ! $product_id ) {
            return;
        }

        if ( ! Helper::is_subscription_product( $product_id ) ) {
            return;
        }

        $redirect_url             = get_site_url() . '/?page=dokan-seller-setup';
        $is_setup_wizard_disabled = dokan_get_option( 'disable_welcome_wizard', 'dokan_selling', 'off' );
        $is_setup_wizard_disabled = 'on' === $is_setup_wizard_disabled ? true : false;

        if ( $is_setup_wizard_disabled || $this->vendor_has_seen_setup_wizard() ) {
            return;
        }

        ?>
        <script>
            jQuery(document).ready(function() {
                setTimeout(function(){
                    window.location.replace("<?php echo $redirect_url; ?>");
                }, 3000);
            });
        </script>
        <?php
    }

    /**
     * Vendor has seen setup wizard
     *
     * @since  DOKAN_PLUGIN_SINCE
     *
     * @return void
     */
    public function make_vendor_has_seen_setup_wizard( $store ) {
        $vendor_id = $store->store_id;

        if ( ! $vendor_id ) {
            return;
        }

        update_user_meta( $vendor_id, 'dokan_vendor_seen_setup_wizard', true );
    }

    /**
     * Check whether vendor has seen setup wizard or not
     *
     * @since  DOKAN_PLUGIN_SINCE
     *
     * @return boolean
     */
    public function vendor_has_seen_setup_wizard() {
        return get_user_meta( dokan_get_current_user_id(), 'dokan_vendor_seen_setup_wizard', true );
    }
}

$dps_enable                 = Helper::is_subscription_module_enabled();
$dps_enable_in_registration = Helper::is_subscription_enabled_on_registration();

if ( $dps_enable && $dps_enable_in_registration ) {
    Registration::instance();
}
