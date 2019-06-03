<?php

/**
* Vendor staff class
*/
class Dokan_staffs {

    private static $errors;

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'dokan_add_staff_content',   array( $this, 'display_errors' ), 10 );
        add_action( 'dokan_add_staff_content',   array( $this, 'add_staff_content' ), 15 );
        add_action( 'template_redirect',         array( $this, 'handle_staff' ), 10 );
        add_action( 'template_redirect',         array( $this, 'delete_staff' ), 99 );
        add_action( 'template_redirect',         array( $this, 'handle_pemission' ), 99 );
        add_action( 'dokan_new_product_added',   array( $this, 'filter_product' ), 10, 2 );
        add_action( 'dokan_product_updated',     array( $this, 'update_product' ), 10 );
        add_action( 'dokan_product_listing_arg', array( $this, 'listing_product' ), 10 );
    }

    /**
     * Display all errors
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function display_errors() {
        if ( ! empty( self::$errors ) ) {
            foreach ( self::$errors as $key => $error ) {
                if ( is_wp_error( $error ) ) {
                    dokan_get_template_part('global/dokan-error', '', array( 'deleted' => true, 'message' => $error->get_error_message() ) );
                }
            }
        }
    }

    /**
     * Add staff content
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_staff_content() {
        $is_edit = ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && ! empty( $_GET['staff_id'] ) ) ? $_GET['staff_id'] : 0;

        if ( ! $is_edit ) {
            $first_name  = '';
            $last_name   = '';
            $email       = '';
            $phone       = '';
            $button_name = __( 'Create staff', 'dokan' );
        } else {
            $user        = get_user_by( 'id', $_GET['staff_id'] );
            $first_name  = $user->first_name;
            $last_name   = $user->last_name;
            $email       = $user->user_email;
            $phone       = get_user_meta( $user->ID, '_staff_phone', true );
            $button_name = __( 'Update staff', 'dokan' );
        }

        include DOKAN_VENDOR_staff_DIR . '/templates/form.php';

    }

    /**
     * Hande form submission
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function handle_staff() {
        if ( ! isset( $_POST['staff_creation'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['vendor_staff_nonce_field'], 'vendor_staff_nonce' ) ) {
            return;
        }

        $is_edit = ! empty( $_POST['staff_id'] ) ? $_POST['staff_id'] : false;
        $user_password = '';

        if ( empty( $_POST['first_name'] ) ) {
            self::$errors[] = new WP_Error( 'no-first-name', __( 'First Name must be required', 'dokan' ) );
        }

        if ( empty( $_POST['last_name'] ) ) {
            self::$errors[] = new WP_Error( 'no-last-name', __( 'Last Name must be required', 'dokan' ) );
        }

        if ( empty( $_POST['email'] ) ) {
            self::$errors[] = new WP_Error( 'no-email', __( 'Email must be required', 'dokan' ) );
        }

        if ( empty( $_POST['vendor_id'] ) ) {
            self::$errors[] = new WP_Error( 'no-vendor', __( 'No vendor found for assigning this staff', 'dokan' ) );
        }

        if ( ! empty( $_POST['staff_id'] ) ) {
            if ( ! empty( $_POST['password'] ) ) {
                $user_password = $_POST['password'];
            }
        }

        if ( ! $is_edit ) {
            $userdata = array(
                'user_email'   => $_POST['email'],
                'user_pass'    => wp_generate_password(),
                'user_login'   => $_POST['email'],
                'first_name'   => $_POST['first_name'],
                'last_name'    => $_POST['last_name'],
                'role'         => 'vendor_staff',
                'display_name' => $_POST['first_name'] . ' ' . $_POST['last_name']
            );
        } else {
            $userdata = array(
                'ID'           => (int)$is_edit,
                'user_email'   => $_POST['email'],
                'user_login'   => $_POST['email'],
                'first_name'   => $_POST['first_name'],
                'last_name'    => $_POST['last_name'],
                'role'         => 'vendor_staff',
                'display_name' => $_POST['first_name'] . ' ' . $_POST['last_name']
            );

            if ( ! empty( $user_password ) ) {
                $userdata['user_pass'] = wp_hash_password( $user_password );
            }
        }

        remove_filter( 'pre_user_display_name', 'dokan_seller_displayname' );
        $user = wp_insert_user( $userdata );
        add_filter( 'pre_user_display_name', 'dokan_seller_displayname' );

        if ( is_wp_error( $user ) ) {
            self::$errors[] = $user;
            return;
        }

        if ( ! $is_edit ) {
            wp_send_new_user_notifications( $user, 'user' );
        }

        $staff = new WP_User( $user );
        $staff_caps = dokan_get_staff_capabilities();

        $staff->add_cap( 'dokandar' );
        $staff->add_cap( 'delete_pages' );
        $staff->add_cap( 'publish_posts' );
        $staff->add_cap( 'edit_posts' );
        $staff->add_cap( 'delete_published_posts' );
        $staff->add_cap( 'edit_published_posts' );
        $staff->add_cap( 'delete_posts' );
        $staff->add_cap( 'manage_categories' );
        $staff->add_cap( 'moderate_comments' );
        $staff->add_cap( 'unfiltered_html' );
        $staff->add_cap( 'upload_files' );
        $staff->add_cap( 'edit_shop_orders' );
        $staff->add_cap( 'edit_product' );

        foreach ( $staff_caps as $key => $staff_cap ) {
            $staff->add_cap( $staff_cap );
        }

        update_user_meta( $user, 'dokan_enable_selling', 'yes' );
        update_user_meta( $user, '_vendor_id', sanitize_text_field( $_POST['vendor_id'] ) );
        update_user_meta( $user, '_staff_phone', sanitize_text_field( $_POST['phone'] ) );
        wp_redirect( dokan_get_navigation_url( 'staffs' ) );
        exit();
    }

    /**
     * Delete staff
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete_staff() {
        if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete_staff' ) {
            if ( wp_verify_nonce( $_GET['_staff_delete_nonce'], 'staff_delete_nonce' ) ) {

                $user_id   = ! empty( $_GET['staff_id'] ) ? $_GET['staff_id'] : 0;
                $vendor_id = get_user_meta( $user_id, '_vendor_id', true );

                if ( $vendor_id == get_current_user_id() ) {
                    if ( $user_id ) {
                        require_once ABSPATH . 'wp-admin/includes/user.php';
                        wp_delete_user( $user_id );

                        $redirect_url = add_query_arg( array( 'message' => 'deleted' ), dokan_get_navigation_url( 'staffs' ) );
                        wp_redirect( $redirect_url );
                        exit();
                    }
                }
            }
        }
    }

    /**
     * Handle staff permissions
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function handle_pemission() {
        if ( ! isset( $_POST['update_staff_permission'] ) ) {
            return;
        }

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['_dokan_manage_staff_permission_nonce'], 'dokan_manage_staff_permission' ) ) {
            return;
        }

        if ( isset( $_GET['view'] ) && $_GET['view']  != 'manage_permissions' ) {
            return;
        }

        $staff_id  = ! empty( $_GET['staff_id'] ) ? $_GET['staff_id'] : 0;
        $vendor_id = get_user_meta( $staff_id, '_vendor_id', true );

        if ( $staff_id && $vendor_id != get_current_user_id() ) {
            return;
        }

        $capabilities = array();
        $all_cap      = dokan_get_all_caps();
        $staff        = new WP_User( $staff_id );

        if ( ! $staff ) {
            return;
        }

        foreach( $all_cap as $key=>$cap ) {
            $capabilities = array_merge( $capabilities, array_keys( $cap ) );
        }

        foreach ( $capabilities as $key => $value ) {
            if ( isset( $_POST[$value] ) && $_POST[$value] ) {
                $staff->add_cap( $value );
            } else {
                $staff->remove_cap( $value );
            }
        }

        $redirect_url = add_query_arg( array( 'view' => 'manage_permissions', 'action' => 'manage', 'staff_id' => $staff_id, 'message' => 'success' ), dokan_get_navigation_url( 'staffs' ) );
        wp_redirect( $redirect_url );

    }

    /**
      * Handle product for staff uploading and editing
      *
      * @since 1.0.0
      *
      * @return void
      */
    public function filter_product( $product_id, $post_data ) {

        if ( ! $product_id ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! current_user_can( 'vendor_staff' ) ) {
            return;
        }

        $staff_id  = get_current_user_id();
        $vendor_id = get_user_meta( $staff_id, '_vendor_id', true );

        if ( empty( $vendor_id ) ) {
            return;
        }

        wp_update_post( array( 'ID' => $product_id, 'post_author' => $vendor_id ) );
        update_post_meta( $product_id, '_staff_id', $staff_id );
    }

    /**
     * Update product data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update_product( $post_id ) {
        $this->filter_product( $post_id, array() );
    }

    /**
     * Listing product argument filter
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function listing_product( $args ) {
        if ( current_user_can( 'vendor_staff' ) ) {
            $staff_id = get_current_user_id();
            $vendor_id = get_user_meta( $staff_id, '_vendor_id', true );

            if ( empty( $vendor_id ) ) {
                return $args;
            }

            $args['author'] = $vendor_id;
        }

        return $args;
    }
}
