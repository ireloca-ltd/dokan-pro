<?php
/**
 * Dokan Live Chat Start Class
 */
class Dokan_Live_Chat_Start {

    /**
     * Hold class instance
     *
     * @var object
     */
    public static $instance;

    /**
     * API endpoint url
     *
     * @var string
     */
    protected $api_endpoint;

    /**
     * Hold the app_id
     *
     * @var string
     */
    public $app_id;

    /**
     * Hold the app_secret
     *
     * @var string
     */
    public $app_secret;

    /**
     * Hold value of live chat on off
     *
     * @var string
     */
    public $enabled;

    /**
     * Constructor method for this class
     */
    public function __construct() {
        $this->app_id       = dokan_get_option( 'app_id', 'dokan_live_chat' );
        $this->app_secret   = dokan_get_option( 'app_secret', 'dokan_live_chat' );
        $this->enabled      = dokan_get_option( 'enable', 'dokan_live_chat' );
        $this->api_endpoint = 'https://api.talkjs.com/';
        $this->init_hooks();
    }

    /**
     * Return single instance of the class
     *
     * @since 1.0
     *
     * @return object;
     */
    public static function init() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initialize all hooks
     *
     * @return void
     */
    public function init_hooks() {
        // chat button on seller store page
        add_action( 'dokan_after_store_tabs', array( $this, 'dokan_render_live_chat_button' ) );

        // chat button on product page
        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'dokan_render_live_chat_button_product_page' ) );
        add_action( 'dokan_product_seller_tab_end', array( $this, 'dokan_render_live_chat_button_product_tab' ), 10, 2 );

        // enqueue various scripts
        add_action( 'dokan_after_load_script', array( $this, 'dokan_include_scripts' ) );
        add_action( 'dokan_enqueue_scripts', array( $this, 'dokan_include_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'dokan_enqueue_scripts' ) );

        // handle ajax call to login to chat
        add_action( 'wp_ajax_nopriv_dokan_live_chat_login', array( $this, 'dokan_ajax_handler' ) );

        // create shortcode
        add_action( 'init', array( $this, 'dokan_live_chat_shortcode' ) );

        // init chat sessions
        add_action( 'wp_head', array( $this, 'init_chat_sessions' ), 999 );
    }

    /**
     * Initialize scripts after dokan script loaded
     *
     * @since  1.3.4
     *
     * @return void
     */
    public function dokan_include_scripts() {
        wp_enqueue_style( 'dokan-magnific-popup' );
        wp_enqueue_script( 'dokan-popup' );
    }

    /**
     * Enqueue admin scripts
     *
     * Allows plugin assets to be loaded.
     *
     * @uses wp_enqueue_script()
     * @uses wp_localize_script()
     * @uses wp_enqueue_style
     */
    public function dokan_enqueue_scripts() {
        if ( dokan_is_store_page() || is_product() || is_account_page() ) {
            wp_enqueue_style( 'dokan-live-chat-login', DOKAN_LIVE_CHAT_ASSETS . '/css/style.css' );
            wp_enqueue_script( 'dokan-live-chat-login', DOKAN_LIVE_CHAT_ASSETS . '/js/script.js', array( 'jquery' ), false, true );

            wp_localize_script( 'dokan-live-chat-login', 'dokan_live_chat', array(
                'wait'       => __( 'wait...', 'dokan' ),
                'my_account' => 'yes'
            ) );
        }

        if ( dokan_is_seller_dashboard() && ! $this->dokan_is_seller_settings_page() ) {
            wp_enqueue_script( 'dokan-live-chat-login', DOKAN_LIVE_CHAT_ASSETS . '/js/script.js', array( 'jquery' ), false, true );

            wp_localize_script( 'dokan-live-chat-login', 'dokan_live_chat', array(
                'seller_dashboard' => 'yes',
            ) );
        }
    }

	/**
     * Check if it's dokan seller settings page
     *
	 * @return bool
     *
     * @since 1.0
	 */
    public function dokan_is_seller_settings_page() {
        global $wp;

        if ( ! isset( $wp->query_vars['settings'] ) ) {
            return false;
        }

        return true;
    }

    /**
     * Dokan is seller online
     *
     * @param  int user_id
     *
     * @since 1.0
     *
     * @return mixed true,false or error
     */
    public function dokan_is_seller_online() {
        if ( get_transient( 'dokan_seller_is_online' ) == 'maybe' ) {
            return false;
        }

        if ( get_transient( 'dokan_seller_is_online' ) == 'yes' ) {
            return true;
        }

        if ( dokan_is_store_page() ) {
            $user_id = dokan()->vendor->get( get_query_var( 'author' ) )->get_id();
        } else {
            $user_id = get_post_field( 'post_author', get_the_ID() );
        }

        if ( empty( $user_id ) ) {
            return false;
        }

        $url = $this->api_endpoint . 'v1/' . $this->app_id . '/users/' . $user_id . '/sessions' ;

        $response = wp_remote_get( $url, array(
            'sslverify' => false,
            'headers' => array(
                'Authorization' => 'Bearer '. $this->app_secret
            )
        ) );

        set_transient( 'dokan_seller_is_online', 'maybe', 10 );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'live-chat-error', __( 'Something went wrong', 'dokan' ) );
        }

        $api_response = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! is_array( $api_response ) || empty( $api_response ) ) {
            return false;
        }

        // currentConversationId exists means user is online
        if ( ! array_key_exists( 'currentConversationId', $api_response[0] ) ) {
            return false;
        }

        set_transient( 'dokan_seller_is_online', 'yes', 15 );

        return true;
    }

    /**
     * Dokan ajax handler
     *
     * @since 1.0
     *
     * @return void
     */
    public function dokan_ajax_handler() {
        switch ( $_POST['data'] ) {
            case 'login_form':
                wp_send_json_success( $this->login_to_chat() );
                break;

            case 'login_data_submit':
                $this->login_data_submit();
                break;

            default:
                wp_send_json_success( '<div>Error!! try again!</div>' );
                break;
        }
    }

    /**
     * Initialize seller chat sessions on every page so that we can
     * get new message notifications
     *
     * @since 1.0
     *
     * @return void
     */
    public function init_chat_sessions() {

        if ( empty( $this->app_id ) ) {
            return;
        }

        global $wp_query;
        $seller = wp_get_current_user();

        // if user is not logged in;
        if ( $seller->ID == 0 ) {
            return;
        }

        // if its customer my account page return early
        if ( isset( $wp_query->query_vars['customer-inbox'] ) ) {
            return;
        }

        $this->make_popup_responsive();

        if ( dokan_is_user_seller( $seller->ID ) ) {
            // if is inbox page then return early
            if ( isset( $wp_query->query_vars['inbox'] ) ) {
                return;
            }

            $this->get_talkjs_script();
            $this->render_seller_js( $seller );
        } else {
            // if its customer
            $this->get_talkjs_script();
            $this->get_customer_seller_chat_js();
        }
    }

    /**
     * Make the popup responsive
     *
     * @return string
     *
     * @since 1.1
     */
    public function make_popup_responsive() {
        ?>
        <style type="text/css">
            @media only screen and (max-width: 600px) {
                .__talkjs_popup {
                    top: 100px !important;
                    height: 80% !important;
                }
            }
        </style>
        <?php
    }

    /**
     * Render chat javascript for seller
     *
     * @param  object $seller object
     *
     * @since 1.0
     *
     * @return void
     */
    public function render_seller_js( $seller ) {
        ?>
        <script type="text/javascript">
        Talk.ready.then( function() {
            var customer = new Talk.User({
                id: "<?php echo $seller->ID ?>",
                name: "<?php echo $seller->display_name ?>",
                email: "<?php echo $seller->user_email ?>",
                photoUrl: "<?php echo esc_url( get_avatar_url( $seller->ID ) ) ?>",
            });

            window.talkSession = new Talk.Session( {
                appId: "<?php echo esc_attr( $this->app_id ); ?>",
                me: customer
            } );

            // inbox
            var inbox = window.talkSession.createInbox();

            // init popup chat unless it's seller dashboard
            window.talkSession.unreads.on('change', function (conversation) {

                if ( typeof dokan_live_chat !== 'undefined' && dokan_live_chat.seller_dashboard == 'yes' ) {
                    return;
                }

                // if it's customer my account page return early (disable popup)
                if ( typeof dokan_live_chat !== 'undefined' && dokan_live_chat.my_account == 'yes' ) {
                    return;
                }

                var unreadCount = conversation.length;

                if (unreadCount > 0) {
                    var popup = talkSession.createPopup();
                }

                // if popup is not empty and there is a unread message
                if (popup != '') {
                    if (unreadCount > 0) {
                        popup.mount();
                    }
                }
            });

        } );
        </script>
        <?php

        $this->get_unread_message_count();
    }

    /**
     * Get unread message count
     *
     * @since 1.0
     *
     * @return string
     */
    public function get_unread_message_count() {
        ?>
        <script type="text/javascript">
        Talk.ready.then( function() {
            var unreadMessage = document.createElement( 'span' );

            // if there is a new message update the message count in seller dashboard
            window.talkSession.unreads.on( 'change', function ( conversationId ) {
                var unreadCount = conversationId.length;

                if ( unreadCount > 0) {
                    var inboxMenu = document.querySelector( '.dokan-dashboard-menu .inbox a' );

                    inboxMenu.appendChild( unreadMessage );
                    unreadMessage.innerText = unreadCount;

                    var inboxCount = document.querySelector( '.dokan-dashboard-menu .inbox a span' );

                    inboxCount.style.position       = 'absolute';
                    inboxCount.style.top            = '14px';
                    inboxCount.style.right          = '23px';
                    inboxCount.style.color          = 'white';
                    inboxCount.style.fontSize       = '12px';
                    inboxCount.style.background     = 'rgb(242, 5, 37) none repeat scroll 0% 0%';
                    inboxCount.style.borderRadius   = '50%';
                    inboxCount.style.width          = '18px';
                    inboxCount.style.height         = '18px';
                    inboxCount.style.textAlign      = 'center';
                    inboxCount.style.lineHeight     = '17px';
                    inboxCount.style.fontWeight     = 'bold';
                }

            } );
        } );
        </script>
        <?php
    }

    /**
     * Render live chat button on seller store page
     *
     * @param  int store id
     *
     * @since 1.0
     *
     * @return string
     */
    public function dokan_render_live_chat_button( $store_id ) {
        $store = dokan()->vendor->get( $store_id )->get_shop_info();

        if ( ! isset( $store['live_chat'] ) || $store['live_chat'] !== 'yes' ) {
            return;
        }

        if ( dokan_get_option( 'chat_button_seller_page', 'dokan_live_chat' ) !== 'on'  ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            $this->get_login_to_chat_button();
            return $this->login_to_chat();
        }

        ?>
        <li class="dokan-store-support-btn-wrap dokan-right">
            <button class="dokan-btn dokan-btn-theme dokan-btn-sm dokan-live-chat">
                <?php echo esc_html__( 'Chat Now', 'dokan' ); ?>
            </button>
            <?php echo do_shortcode( '[dokan-live-chat]' ); ?>
        </li>

        <?php

        if ( ! $this->dokan_is_seller_online() ) {
            return;
        }

        $this->make_seller_online();
    }

    /**
     * Make a seller online
     *
     * @since 1.0
     *
     * @return string
     */
    public function make_seller_online() {
        ?>
        <script type="text/javascript">
        var dokan_chat = document.querySelector( '.dokan-live-chat' );
        var span = document.createElement( 'span' );

        dokan_chat.appendChild( span );

        var chat_btn = document.querySelector( '.dokan-live-chat span' );

        dokan_chat.style.paddingLeft = '23px';
        chat_btn.style.position = 'absolute';
        chat_btn.style.top = '9px';
        chat_btn.style.left = '7px';
        chat_btn.style.width = '9px';
        chat_btn.style.height = '9px';
        chat_btn.style.borderRadius = '50%';
        chat_btn.style.background = '#79e379';
        chat_btn.style.zIndex = '999';
        </script>
        <?php
    }

    /**
     * Dokan render live chat button on product page
     *
     * @since 1.0
     *
     * @return string
     */
    public function dokan_render_live_chat_button_product_page() {
        $product_id = get_the_ID();
        $seller_id  = get_post_field( 'post_author', $product_id );
        $store      = dokan()->vendor->get( $seller_id )->get_shop_info();

        if ( ! isset( $store['live_chat'] ) || $store['live_chat'] !== 'yes' ) {
            return;
        }

        if ( dokan_get_option( 'chat_button_product_page', 'dokan_live_chat' ) !== 'above_tab'  ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            $this->get_login_to_chat_button();
            return $this->login_to_chat();
        }

        echo sprintf( '<button type="submit" style="margin-left: 5px;" class="dokan-live-chat button alt">%s</button>', __( 'Chat Now', 'dokan' ) );
        echo do_shortcode( '[dokan-live-chat]' );

        if ( ! $this->dokan_is_seller_online() ) {
            return;
        }

        $this->make_seller_online();
    }

    /**
     * Dokan render live chat button on product tab
     *
     * @param  object $author
     *
     * @param  array $store
     *
     * @since 1.0
     *
     * @return string
     */
    public function dokan_render_live_chat_button_product_tab( $author, $store ) {
        if ( ! isset( $store['live_chat'] ) || $store['live_chat'] !== 'yes' ) {
            return;
        }

        if ( dokan_get_option( 'chat_button_product_page', 'dokan_live_chat' ) !== 'inside_tab'  ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            $this->get_login_to_chat_button();
            return $this->login_to_chat();
        }

        echo sprintf( '<button type="submit" style="padding-left: 23px" class="dokan-live-chat button alt">%s</button>', __( 'Chat Now', 'dokan' ) );
        echo do_shortcode( '[dokan-live-chat]' );

        if ( ! $this->dokan_is_seller_online() ) {
            return;
        }

        $this->make_seller_online();
    }

    /**
     * Get login to chat button
     *
     * @since 1.0
     *
     * @return string
     */
    public function get_login_to_chat_button() {
        if ( dokan_is_store_page() ) {
            ?>
                <li class="dokan-store-support-btn-wrap dokan-right">
                    <button class="dokan-btn dokan-btn-theme dokan-btn-sm dokan-live-chat-login">
                        <?php echo esc_html__( 'Chat Now', 'dokan' ); ?>
                    </button>
                </li>
            <?php
        } else {
            printf( '<button type="submit" style="margin-left: 5px" class="dokan-live-chat-login button alt">%s</button>', __( 'Chat Now', 'dokan' ) );
        }
    }

    /**
     * Login to chat
     *
     * @since 1.0
     *
     * @return string;
     */
    public function login_to_chat() {
        ob_start();
        ?>
        <h2><?php _e( 'Please Login to Chat', 'dokan' ); ?></h2>

        <form class="dokan-form-container" id="dokan-chat-login">
            <div class="dokan-form-group">
                <label class="dokan-form-label" for="login-name"><?php _e( 'Username :', 'dokan' ) ?></label>
                <input required class="dokan-form-control" type="text" name='login-name' id='login-name'/>
            </div>
            <div class="dokan-form-group">
                <label class="dokan-form-label" for="login-password"><?php _e( 'Password :', 'dokan' ) ?></label>
                <input required class="dokan-form-control" type="password" name='login-password' id='login-password'/>
            </div>
            <?php wp_nonce_field( 'dokan-chat-login-action', 'dokan-chat-login-nonce' ); ?>
            <div class="dokan-form-group login-to-chat ">
                <input id='dokan-chat-login-btn' type="submit" value="<?php _e( 'Login', 'dokan' ) ?>" class="dokan-w5 dokan-btn dokan-btn-theme"/>
                <a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>" class="dokan-w5 dokan-btn dokan-btn-theme">
                    <?php _e( 'Register', 'dokan' ); ?>
                </a>
            </div>
        </form>
        <div class="dokan-clearfix"></div>
        <?php

        return ob_get_clean();
    }

    /**
     * handles login data and sign in user
     *
     * @since 1.0
     *
     * @return string success|failed
     */
    public function login_data_submit() {
        parse_str( $_POST['form_data'], $postdata );

        if ( ! wp_verify_nonce( $postdata['dokan-chat-login-nonce'], 'dokan-chat-login-action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }
        $info                  = array();
        $info['user_login']    = $postdata['login-name'];
        $info['user_password'] = $postdata['login-password'];
        $user_signon           = wp_signon( $info, false );

        if ( is_wp_error( $user_signon ) ) {
            wp_send_json( array(
                'success' => false,
                'msg'     => __( 'Invalid Username or Password', 'dokan' ),
            ) );
        } else {
            wp_send_json( array(
                'success' => true,
                'msg'     => __( 'Logged in', 'dokan' ),
            ) );
        }
    }

    /**
     * Create all the shortcodes
     *
     * @since 1.0
     *
     * @return void
     */
    public function dokan_live_chat_shortcode() {
        add_shortcode( 'dokan-live-chat', array( $this, 'create_short_code' ) );
        add_shortcode( 'dokan-chat-inbox', array( $this, 'create_chat_inbox') );
    }

    /**
     * Create seller chat inbox
     *
     * @since 1.0
     *
     * @return string
     */
    public function create_chat_inbox() {
        $seller = wp_get_current_user();

        $this->get_talkjs_script();
        $this->render_seller_js( $seller );
        ?>
        <div id="dokan-inbox" style=""></div>

        <script>
            Talk.ready.then( function() {
                var inbox = window.talkSession.createInbox();
                inbox.mount( document.getElementById( 'dokan-inbox' ) );
            } );
        </script>
        <?php
    }

    /**
     * Create dokan-live-chat shortcode
     *
     * @since 1.0
     *
     * @return string
     */
    public function create_short_code() {
        $this->get_talkjs_script();
        $this->get_customer_seller_chat_js();
    }

    /**
     * Get main talkjs library
     *
     *@since 1.0
     *
     * @return string
     */
    public function get_talkjs_script() {
        ?>
        <script type="text/javascript">
        (function(t,a,l,k,j,s){
        s=a.createElement('script');s.async=1;s.src="https://cdn.talkjs.com/talk.js";a.getElementsByTagName('head')[0].appendChild(s);k=t.Promise;
        t.Talk={ready:{then:function(f){if(k)return new k(function(r,e){l.push([f,r,e])});l.push([f])},catch:function(){return k&&new k()},c:l}};
        })(window,document,[]);
        </script>
        <?php
    }

    /**
     * Get customer seller chat js ( create customer seller chat )
     *
     * @since 1.0
     *
     * @return string;
     */
    public function get_customer_seller_chat_js() {
        // if it's product page then get the store user from that product
        if ( ! dokan_is_store_page() ) {
            $seller_id = get_post_field( 'post_author', get_the_ID() );
            $store_user = dokan()->vendor->get( $seller_id );
        } else {
            // get store user from dokan seller store page;
            $store_user = dokan()->vendor->get( get_query_var( 'author' ) );
        }

        $customer = wp_get_current_user();
        ?>
        <script type="text/javascript">
        Talk.ready.then( function() {
            var customer = new Talk.User( {
                id: "<?php echo $customer->ID ?>",
                name: "<?php echo $customer->display_name ?>",
                email: "<?php echo ! empty( $customer->user_email ) ? $customer->user_email : 'fake@email.com'; ?>",
                configuration: "vendor",
                photoUrl: "<?php echo esc_url( get_avatar_url( $customer->ID ) ) ?>",
            } );

            window.talkSession = new Talk.Session( {
                appId: "<?php echo esc_attr( $this->app_id ); ?>",
                me: customer
            } );

            var seller = new Talk.User( {
                id: "<?php echo $store_user->get_id(); ?>",
                name: "<?php echo ! empty( $store_user->get_shop_name() ) ? $store_user->get_shop_name() : 'fakename'; ?>",
                email: "<?php echo ! empty( $store_user->get_email() ) ? $store_user->get_email() : 'fake@email.com'; ?>",
                configuration: "vendor",
                photoUrl: "<?php echo esc_url( get_avatar_url( $store_user->get_id() ) ) ?>",
                welcomeMessage: "<?php _e( 'How may I help you?', 'dokan' ) ?>"
            } );

            // popup the chat box when there is a new message and close the popop if browser get refreashed
            window.talkSession.unreads.on( 'change', function ( conversation ) {
                var unreadCount = conversation.length;

                if ( unreadCount > 0 ) {
                    var popup = talkSession.createPopup();
                }

                // if popup is not empty and there is a unread message
                if ( popup != '' ) {
                    if ( unreadCount > 0 ) {
                        popup.mount();
                    }
                }

            } );

            // if its shortcode ( meaning if its product page or seller store page ): chat_btn !== null
            var chat_btn = document.querySelector( '.dokan-live-chat' );

            if ( chat_btn !== null ) {
                chat_btn.addEventListener( 'click', function( e ) {
                    e.preventDefault();

                    var conversation = talkSession.getOrCreateConversation(Talk.oneOnOneId(customer, seller));
                    conversation.setParticipant(customer);
                    conversation.setParticipant(seller);
                    var inbox = talkSession.createInbox({selected: conversation});
                    var popup = talkSession.createPopup(conversation);
                    popup.mount();
                } );
            }
        } );
        </script>
        <?php
    }

}

Dokan_Live_Chat_Start::init();
