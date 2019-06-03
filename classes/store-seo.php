<?php
/**
 * Dokan SEO class
 *
 * Integrates Dokan SEO template in front-end Settings menu and provides
 * SEO settings for single stores
 *
 * @since 2.3
 */
class Dokan_Pro_Store_Seo {

    public $feedback    = false;
    private $store_info = false;

    /**
     * Load automatically when class initiate
     *
     * @uses actions hook
     * @uses filter hook
     */
    public function __construct() {

        $this->init_hooks();
    }

    /**
     * * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Store_Seo();
        }

        return $instance;
    }

    /**
     * Init hooks and filters
     *
     * @return void
     */
    function init_hooks() {
        add_action( 'init', array( $this, 'register_sitemap' ) );
        add_action( 'wp_ajax_dokan_seo_form_handler', array( $this, 'dokan_seo_form_handler' ) );
        add_action( 'template_redirect', array( $this, 'output_meta_tags' ) );
        add_filter( 'wpseo_sitemap_index', array( $this, 'add_sellers_sitemap' ), 100 );
    }

    /**
     * Adds proper hooks for output of meta tags
     *
     * @return void
     */
    function output_meta_tags() {
        if ( !dokan_is_store_page() ) {
            return;
        }

        if ( dokan_get_option( 'store_seo', 'dokan_general' ) === 'off' ) {
            return;
        }

        $this->store_info = dokan_get_store_info( get_query_var( 'author' ) );

        if ( class_exists( 'All_in_One_SEO_Pack' ) ) {

            add_filter( 'aioseop_title', array( $this, 'replace_title' ), 100 );
            add_filter( 'aioseop_keywords', array( $this, 'replace_keywords' ), 100 );
            add_filter( 'aioseop_description', array( $this, 'replace_desc' ), 100 );
            add_action( 'wp_head', array( $this, 'print_social_tags' ), 1 );
        } elseif ( class_exists( 'WPSEO_Frontend' ) ) {

           //For WP > 4.4
            add_filter( 'wpseo_title', array( $this, 'replace_title' ), 100 );
            //For WP < 4.4
            add_filter( 'wp_title', array( $this, 'replace_title' ), 100 );
            add_filter( 'wpseo_metakeywords', array( $this, 'replace_keywords' ) );
            add_filter( 'wpseo_metadesc', array( $this, 'replace_desc' ) );

            add_filter( 'wpseo_opengraph_url', array( $this, 'replace_og_url' ) );
            add_filter( 'wpseo_opengraph_title', array( $this, 'replace_og_title' ) );
            add_filter( 'wpseo_opengraph_desc', array( $this, 'replace_og_desc' ) );
            add_filter( 'wpseo_opengraph_image', array( $this, 'replace_og_img' ) );
            add_action( 'wpseo_opengraph', array( $this, 'print_og_img' ), 20 );

            add_filter( 'wpseo_twitter_title', array( $this, 'replace_twitter_title' ) );
            add_filter( 'wpseo_twitter_description', array( $this, 'replace_twitter_desc' ) );
            add_filter( 'wpseo_twitter_image', array( $this, 'replace_twitter_img' ) );
            add_action( 'wpseo_twitter', array( $this, 'print_twitter_img' ), 20 );
        } else {

            add_filter( 'wp_title', array( $this, 'replace_title' ), 100 );
            add_action( 'wp_head', array( $this, 'print_tags' ), 1 );
            add_action( 'wp_head', array( $this, 'print_social_tags' ), 1 );
        }
    }

   /**
    * Register dokan_sellers sitemap on yoast SEO
    */
    function register_sitemap() {
        global $wpseo_sitemaps;

        if ( is_a( $wpseo_sitemaps, 'WPSEO_Sitemaps' ) ) {
            $wpseo_sitemaps->register_sitemap( 'dokan_sellers', array( $this, 'sitemap_output' ) );
        }

    }
    /**
     * Add dokan_sellers sitemap url to sitemap_index list
     */
    function add_sellers_sitemap() {

        if ( WPSEO_VERSION < 3.2 ) {
            $base_url = wpseo_xml_sitemaps_base_url( 'dokan_sellers-sitemap.xml' );
        } else {
            $base_url = WPSEO_Sitemaps_Router::get_base_url( 'dokan_sellers-sitemap.xml' );
        }

        ob_start();
        ?>
        <sitemap>
        <loc><?php echo $base_url ?></loc>

        </sitemap>
        <?php

        return ob_get_clean();
    }

    /**
     * Generate output for dokan_sellers sitemap
     */
    function sitemap_output(){
        global $wpseo_sitemaps;

        $seller_q = new WP_User_Query( array(
            'role' => 'seller',
            'meta_key' => 'dokan_enable_selling',
            'meta_value'=>'yes',
        ) );

        $sellers = $seller_q->get_results();

        if ( class_exists( 'WPSEO_Sitemap_Timezone' ) ) {
            $time_formater = new WPSEO_Sitemap_Timezone;
        }

        ob_start();
        ?>

        <urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <?php foreach ( $sellers as $seller ) {
                $product = dokan_get_latest_products( 1, $seller->ID );

                if ( ! $product->post || ! $product->post->post_modified ) {
                    continue;
                }

                $last_modified = $time_formater->format_date( $product->post->post_modified );
            ?>
            <url>
                <loc><?php echo dokan_get_store_url( $seller->ID ) ?></loc>
                <priority><?php echo apply_filters( 'dokan_yoast_store_sitemap_priority', 0.8 )  ?></priority>
                <changefreq><?php echo apply_filters( 'dokan_yoast_store_sitemap_changefreq', 'weekly' )  ?></changefreq>
                <lastmod><?php echo $last_modified ?></lastmod>
            </url>
        <?php } ?>
        </urlset>

        <?php
        $sitemap = ob_get_clean();
        $wpseo_sitemaps->set_sitemap( $sitemap );
    }

    /**
     * prints out default meta tags from user meta
     *
     * @since 2.3
     *
     * @return void
     */
    function print_tags() {
        //get values of title,desc and keywords
        $meta_values = $this->store_info;

        if ( !isset( $meta_values['store_seo'] ) || $meta_values == false ) {
            return;
        }

        $desc     = isset( $meta_values['store_seo']['dokan-seo-meta-desc'] ) ? $meta_values['store_seo']['dokan-seo-meta-desc'] : '';
        $keywords = isset( $meta_values['store_seo']['dokan-seo-meta-keywords'] ) ? $meta_values['store_seo']['dokan-seo-meta-keywords'] : '';

        if ( $desc ) {
            echo PHP_EOL . '<meta name="description" content="' . $this->print_saved_meta( $desc ) . '"/>';
        }
        if ( $keywords ) {
            echo PHP_EOL . '<meta name="keywords" content="' . $this->print_saved_meta( $keywords ) . '"/>';
        }
    }

    /**
     * Prints out social tags
     *
     * @since 2.3
     */
    function print_social_tags() {
        $meta_values = $this->store_info;

        if ( !isset( $meta_values['store_seo'] ) || $meta_values == false ) {
            return;
        }

        $seller = get_userdata( get_query_var( 'author' ) );

        $og_url        = dokan_get_store_url( $seller->ID );
        $og_title      = isset( $meta_values['store_seo']['dokan-seo-og-title'] ) ? $meta_values['store_seo']['dokan-seo-og-title'] : '';
        $og_desc       = isset( $meta_values['store_seo']['dokan-seo-og-desc'] ) ? $meta_values['store_seo']['dokan-seo-og-desc'] : '';
        $og_img        = isset( $meta_values['store_seo']['dokan-seo-og-image'] ) ? $meta_values['store_seo']['dokan-seo-og-image'] : '';
        $twitter_title = isset( $meta_values['store_seo']['dokan-seo-twitter-title'] ) ? $meta_values['store_seo']['dokan-seo-twitter-title'] : '';
        $twitter_desc  = isset( $meta_values['store_seo']['dokan-seo-twitter-desc'] ) ? $meta_values['store_seo']['dokan-seo-twitter-desc'] : '';
        $twitter_img   = isset( $meta_values['store_seo']['dokan-seo-twitter-image'] ) ? $meta_values['store_seo']['dokan-seo-twitter-image'] : '';

        if ( $og_url ) {
            echo PHP_EOL . '<meta property="og:url" content="' . $og_url . '">';
        }

        if ( $og_title ) {
            echo PHP_EOL . '<meta property="og:title" content="' . $this->print_saved_meta( $og_title ) . '"/>';
        }

        if ( $og_desc ) {
            echo PHP_EOL . '<meta property="og:description" content="' . $this->print_saved_meta( $og_desc ) . '"/>';
        }

        if ( $og_img ) {
            echo PHP_EOL . '<meta property="og:image" content="' . wp_get_attachment_url( $og_img ) . '"/>';
        }

        if ( $twitter_title ) {
            echo PHP_EOL . '<meta name="twitter:title" content="' . $this->print_saved_meta( $twitter_title ) . '"/>';
        }

        if ( $twitter_desc ) {
            echo PHP_EOL . '<meta name="twitter:description" content="' . $this->print_saved_meta( $twitter_desc ) . '"/>';
        }

        if ( $twitter_img ) {
            echo PHP_EOL . '<meta name="twitter:image" content="' . wp_get_attachment_url( $twitter_img ) . '"/>';
        }
    }

    /**
     * Generic meta replacer for meta tags
     *
     * @since 2.3
     *
     * @param string $val_default
     * @param string $meta
     * @param string $type
     *
     * @return string $meta
     */
    function replace_meta( $val_default, $meta, $type = '' ) {

        $meta_values = $this->store_info;

        if ( !isset( $meta_values['store_seo'] ) || $meta_values == false ) {
            return $val_default;
        }

        $key = 'dokan-seo-' . $type . '-' . $meta;
        $val = ! empty( $meta_values['store_seo'][$key] ) ? $meta_values['store_seo'][$key] : '';

        if ( $val ) {
            return $val;
        }

        return $val_default;
    }

    /**
     * Replace title meta of other SEO plugin
     *
     * @since 2.3
     *
     * @param string $title
     *
     * @return string title
     */
    function replace_title( $title ) {
        return $this->replace_meta( $title, 'title', 'meta' );
    }

    /**
     * Replace keywords meta of other SEO plugin
     *
     * @since 2.3
     *
     * @param string $keywords
     *
     * @return string $keywords
     */
    function replace_keywords( $keywords ) {
        return $this->replace_meta( $keywords, 'keywords', 'meta' );
    }

    /**
     * Replace description meta of other SEO plugin
     *
     * @since 2.3
     *
     * @param string $desc
     *
     * @return string $desc
     */
    function replace_desc( $desc ) {
        return $this->replace_meta( $desc, 'desc', 'meta' );
    }

    /**
     * Replace OG tag title for WP_SEO
     *
     * @since 2.3
     *
     * @param string $title
     *
     * @return string $title
     */
    function replace_og_title( $title ) {
        return $this->replace_meta( $title, 'title', 'og' );
    }

    /**
     * Replace OG tag description for WP_SEO
     *
     * @since 2.3
     *
     * @param string $desc
     *
     * @return string $desc
     */
    function replace_og_desc( $desc ) {
        return $this->replace_meta( $desc, 'desc', 'og' );
    }

    /**
     * Replace OG tag Image for WP_SEO
     *
     * @since 2.3
     *
     * @param string $img
     *
     * @return string $img
     */
    function replace_og_img( $img ) {
        $img_default = $img;

        $meta_values = $this->store_info;

        if ( !isset( $meta_values['store_seo'] ) || $meta_values == false ) {
            return $img_default;
        }

        $img = ! empty( $meta_values['store_seo']['dokan-seo-og-image'] ) ? $meta_values['store_seo']['dokan-seo-og-image'] : '';

        if ( $img ) {
            return wp_get_attachment_url( $img );
        } else {
            return $img_default;
        }
    }

    /**
     * Print og img
     *
     * @since 2.3
     *
     * @return void
     */
    function print_og_img() {
        $meta_values = $this->store_info;

        if ( !isset( $meta_values['store_seo'] ) || $meta_values == false ) {
            return;
        }

        $og_img = ! empty( $meta_values['store_seo']['dokan-seo-og-image'] ) ? $meta_values['store_seo']['dokan-seo-og-image'] : '';

        if ( $og_img ) {
            echo '<meta property="og:image" content="' . wp_get_attachment_url( $og_img ) . '"/>';
        }
    }

    /**
     * Replace twitter tag title for WP_SEO
     *
     * @since 2.3
     *
     * @param string $val_default
     *
     * @return string
     */
    function replace_twitter_title( $val_default ) {
        return $this->replace_meta( $val_default, 'title', 'twitter' );
    }

    /**
     * replace twitter tag description for WP_SEO
     *
     * @since 2.3
     *
     * @param string $val_default
     *
     * @return string
     */
    function replace_twitter_desc( $val_default ) {
        return $this->replace_meta( $val_default, 'desc', 'twitter' );
    }

    /**
     * Replace twitter image tag for WP_SEO
     *
     * @since 2.3
     *
     * @param string $img
     *
     * @return string
     */
    function replace_twitter_img( $img ) {
        $img_default = $img;

        $meta_values = $this->store_info;

        if ( !isset( $meta_values['store_seo'] ) || $meta_values == false ) {
            return $img_default;
        }

        $img = ! empty( $meta_values['store_seo']['dokan-seo-twitter-image'] ) ? $meta_values['store_seo']['dokan-seo-twitter-image'] : '';

        if ( $img ) {
            return wp_get_attachment_url( $img );
        }

        return $img_default;
    }

    /**
     * Prints out twitter image tag
     *
     * @since 2.3
     *
     * @return void
     */
    function print_twitter_img() {
        $meta_values = $this->store_info;

        if ( !isset( $meta_values['store_seo'] ) || $meta_values == false ) {
            return;
        }

        $tw_img = ! empty( $meta_values['store_seo']['dokan-seo-twitter-image'] ) ? $meta_values['store_seo']['dokan-seo-twitter-image'] : '';

        if ( $tw_img ) {
            echo '<meta name="twitter:image" content="' . wp_get_attachment_url( $tw_img ) . '"/>';
        }
    }

    /**
     * Print SEO meta input form on frontend
     *
     * @since 2.3
     *
     * @return void
     */
    function frontend_meta_form() {
        $current_user   = dokan_get_current_user_id();
        $seller_profile = dokan_get_store_info( $current_user );
        $seo_meta       = isset( $seller_profile['store_seo'] ) ? $seller_profile['store_seo'] : array();

        $default_store_seo = array(
            'dokan-seo-meta-title'    => false,
            'dokan-seo-meta-desc'     => false,
            'dokan-seo-meta-keywords' => false,
            'dokan-seo-og-title'      => false,
            'dokan-seo-og-desc'       => false,
            'dokan-seo-og-image'      => false,
            'dokan-seo-twitter-title' => false,
            'dokan-seo-twitter-desc'  => false,
            'dokan-seo-twitter-image' => false,
        );

        $seo_meta = wp_parse_args( $seo_meta, $default_store_seo );

        dokan_get_template_part('settings/seo-form', '', array(
            'pro'            => true,
            'seo_meta'       => $seo_meta,
            'seller_profile' => $seller_profile,
            'seo'            => $this
        ));
    }

    /**
     * Check meta data and print
     *
     * @since 2.3
     *
     * @param string|boolean $val
     *
     * @return string|empty
     */
    function print_saved_meta( $val ) {
        if ( $val == false ) {
            return '';
        } else {
            return esc_attr( $val );
        }
    }

    /**
     * Submit handler for settings form
     *
     * @since 2.3
     *
     * @return json
     */
    function dokan_seo_form_handler() {
        parse_str( $_POST['data'], $postdata );

        if ( !wp_verify_nonce( $postdata['dokan_store_seo_form_nonce'], 'dokan_store_seo_form_action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }

        if ( ! current_user_can( 'dokan_view_store_seo_menu' ) ) {
            wp_send_json_error( __( 'Pemission denied', 'dokan' ) );
        }

        unset( $postdata['dokan_store_seo_form_nonce'] );
        unset( $postdata['_wp_http_referer'] );

        $default_store_seo = array(
            'dokan-seo-meta-title'    => false,
            'dokan-seo-meta-desc'     => false,
            'dokan-seo-meta-keywords' => false,
            'dokan-seo-og-title'      => false,
            'dokan-seo-og-desc'       => false,
            'dokan-seo-og-image'      => false,
            'dokan-seo-twitter-title' => false,
            'dokan-seo-twitter-desc'  => false,
            'dokan-seo-twitter-image' => false,
        );

        $current_user   = dokan_get_current_user_id();
        $seller_profile = dokan_get_store_info( $current_user );

        $seller_profile['store_seo'] = wp_parse_args( $postdata, $default_store_seo );

        update_user_meta( $current_user, 'dokan_profile_settings', $seller_profile );

        wp_send_json_success( __( 'Your changes has been updated!', 'dokan' ) );
    }

    function replace_og_url(){
        $seller = get_userdata( get_query_var( 'author' ) );
        $og_url = dokan_get_store_url( $seller->ID );
        return $og_url;
    }

}

$seo = Dokan_Pro_Store_Seo::init();
