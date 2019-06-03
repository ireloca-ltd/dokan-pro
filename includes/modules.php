<?php

/**
 * Parses module file and retrieves module metadata
 *
 * @param  string $module_file Path to module file
 *
 * @return array
 */
function dokan_pro_get_module_data( $module_file ) {
    $default_headers = array(
        'name'        => 'Plugin Name',
        'description' => 'Description',
        'plugin_uri'  => 'Plugin URI',
        'thumbnail'   => 'Thumbnail Name',
        'class'       => 'Integration Class',
        'author'      => 'Author',
        'author_uri'  => 'Author URI',
        'version'     => 'Version',
    );

    $module_data = get_file_data( $module_file, $default_headers, 'dokan_pro_modules' );

    return $module_data;
}

/**
 * Gets all the available modules
 *
 * @return array
 */
function dokan_pro_get_modules() {
    $module_root  = dirname( __FILE__) . '/modules';
    $modules_dir  = @opendir( $module_root);
    $modules      = array();
    $module_files = array();

    if ( $modules_dir ) {

        while ( ( $file = readdir( $modules_dir ) ) !== false ) {

            if ( substr( $file, 0, 1 ) == '.' ) {
                continue;
            }

            if ( is_dir( $module_root . '/' . $file ) ) {
                $plugins_subdir = @opendir( $module_root . '/' . $file );

                if ( $plugins_subdir ) {

                    while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
                        if ( substr( $subfile, 0, 1 ) == '.' ) {
                            continue;
                        }

                        if ( substr($subfile, -4) == '.php' ) {
                            $module_files[] = "$file/$subfile";
                        }
                    }

                    closedir( $plugins_subdir );
                }
            }
        }

        closedir( $modules_dir );
    }

    if ( $module_files ) {

        foreach ( $module_files as $module_file ) {

            if ( ! is_readable( "$module_root/$module_file" ) ) {
                continue;
            }

            $module_data = dokan_pro_get_module_data( "$module_root/$module_file" );

            if ( empty ( $module_data['name'] ) ) {
                continue;
            }

            if ( ! empty ( $module_data['thumbnail'] ) ) {
                $module_data['thumbnail'] = DOKAN_PRO_PLUGIN_ASSEST . '/images/modules/' . $module_data['thumbnail'];
            }

            $file_base = wp_normalize_path( $module_file );

            $modules[ $file_base ] = $module_data;
        }
    }

    uasort( $modules, 'dokan_module_short_by_name_asc' );

    return $modules;
}

/**
 * Short by module name A-Z
 *
 * @param array $a
 * @param array $b
 *
 * @return array
 */
function dokan_module_short_by_name_desc( $a, $b ) {
    return strcmp( $a["name"], $b["name"] );
}

/**
 * Short by module name Z-A
 *
 * @param array $a
 * @param array $b
 *
 * @return array
 */
function dokan_module_short_by_name_asc( $a, $b ) {
    return strcmp( $b["name"], $a["name"] );
}

/**
 * Get a single module data
 *
 * @param  string $module
 *
 * @return WP_Error|Array
 */
function dokan_pro_get_module( $module ) {
    $module_root  = dirname( __FILE__) . '/modules';

    $module_data = dokan_pro_get_module_data( "$module_root/$module" );

    if ( empty ( $module_data['name'] ) ) {
        return new WP_Error( 'not-valid-plugin', __( 'This is not a valid plugin', 'dokan' ) );
    }

    return $module_data;
}

/**
 * Get the meta key to store the active module list
 *
 * @return string
 */
function dokan_pro_active_module_key() {
    return 'dokan_pro_active_modules';
}

/**
 * Get active modules
 *
 * @return array
 */
function dokan_pro_get_active_modules() {
    return get_option( dokan_pro_active_module_key(), array() );
}

/**
 * Check if a module is active
 *
 * @param  string $module basename
 *
 * @return boolean
 */
function dokan_pro_is_module_active( $module ) {
    return in_array( $module, dokan_pro_get_active_modules() );
}

/**
 * Check if a module is inactive
 *
 * @param  string $module basename
 *
 * @return boolean
 */
function dokan_pro_is_module_inactive( $module ) {
    return ! dokan_pro_is_module_active( $module );
}

/**
 * Activate a module
 *
 * @param  string $module basename of the module file
 *
 * @return WP_Error|boolean WP_Error on invalid file or boolean.
 */
function dokan_pro_activate_module( $module ) {
    $current   = dokan_pro_get_active_modules();
    $activated = false;

    $module_root = dirname( __FILE__) . '/modules';
    $module_data = dokan_pro_get_module_data( "$module_root/$module" );

    if ( empty ( $module_data['name'] ) ) {
        return new WP_Error( 'invalid-module', __( 'The module is invalid', 'dokan' ) );
    }

    // activate if enactive
    if ( dokan_pro_is_module_inactive( $module ) ) {
        $current[] = $module;
        sort($current);

        // deactivate the addon if exists
        $module_class = dokan_module_class_map( $module );

        if ( $module_class && class_exists( $module_class ) ) {
            $reflector  = new ReflectionClass( $module_class );
            $addon_path = plugin_basename( $reflector->getFileName() );

            deactivate_plugins( $addon_path );

            return new WP_Error( 'plugin-exists', __( 'Deactivated the plugin, please try again.', 'dokan' ) );
        }

        $file_path = plugin_basename( "$module_root/$module" );

        if ( file_exists( "$module_root/$module" ) ) {
            require_once "$module_root/$module";
            do_action( "dokan_activate_{$file_path}", $module );
        }

        $activated = true;

        update_option( dokan_pro_active_module_key(), $current );
    }

    return $activated;
}

/**
 * Deactivate a module
 *
 * @param  string $module basename of the module file
 *
 * @return boolean
 */
function dokan_pro_deactivate_module( $module ) {
    $current = dokan_pro_get_active_modules();

    if ( dokan_pro_is_module_active( $module ) ) {

        $key = array_search( $module, $current );

        if ( false !== $key ) {
            unset( $current[ $key ] );
            sort($current);
        }

        $module_root = dirname( __FILE__) . '/modules';
        $file_path = plugin_basename( "$module_root/$module" );

        if ( file_exists( "$module_root/$module" ) ) {
            require_once "$module_root/$module";
            do_action( "dokan_deactivate_{$file_path}", $module );
        }

        update_option( dokan_pro_active_module_key(), $current );

        return true;
    }

    return false;
}

/**
 * Dokan register activation hook description]
 *
 * @param string $file     full file path
 * @param array|string $function callback function
 *
 * @return void
 */
function dokan_register_activation_hook( $file, $function ) {
    if ( file_exists( $file ) ) {
        require_once $file;
        $base_name = plugin_basename( $file );
        add_action( "dokan_activate_{$base_name}", $function );
    }
}

/**
 * Dokan register deactivation hook description]
 *
 * @param string $file     full file path
 * @param array|string $function callback function
 *
 * @return void
 */
function dokan_register_deactivation_hook( $file, $function ) {
    if ( file_exists( $file ) ) {
        require_once $file;
        $base_name = plugin_basename( $file );
        add_action( "dokan_deactivate_{$base_name}", $function );
    }
}

function dokan_module_class_map( $module ) {
    $modules = array(
        'appearance/appearance.php'                    => 'Dokan_Apperance',
        'booking/booking.php'                          => 'Dokan_WC_Booking',
        'export-import/export-import.php'              => 'Dokan_Product_Importer',
        'live-search/live-search.php'                  => 'Dokan_Live_Search',
        'paypal-adaptive-payments/dokan-paypal-ap.php' => 'Dokan_Paypal_AP',
        'product-enquiry/enquiry.php'                  => 'Dokan_Product_Enquiry',
        'seller-vacation/seller-vacation.php'          => 'Dokan_Seller_Vacation',
        'store-reviews/store-reviews.php'              => 'Dokan_Store_Reviews',
        'store-support/store-support.php'              => 'Dokan_Store_Support',
        'stripe/gateway-stripe.php'                    => 'Dokan_Stripe',
        'subscription/product-subscription.php'        => 'Dokan_Product_Subscription',
        'vendor-verification/vendor-verification.php'  => 'Dokan_Seller_Verification',
        'geolocation/geolocation.php'                  => 'Dokan_Geolocation',
    );

    if ( array_key_exists( $module, $modules) ) {
        return $modules[ $module ];
    }

    return false;
}
