<?php
namespace oshwoo;
use Automattic\WooCommerce\Utilities\OrderUtil;

# object binding & access 
function osh() {
    return osh::get_instance();
}

/**
 * class osh
 *
 * main osh class, initializes the plugin
 *
 * @class       osh
 * @version     2.0
 * @author      alx359
 */
class osh {

    #--> properties|attributes
    #
    public $show_donation_box = true;

    /**
     *  singleton lazy
     *  https://hardcorewp.com/2013/initializing-singleton-classes-used-in-wordpress-plugins/
     */
    private static $_instance;
    public  static function get_instance() {
        if( !isset( self::$_instance ) )
                    self::$_instance = new self;
             return self::$_instance;
    }

    #--> init
    public function __construct() {
        $this->includes();
        $this->hooks();
    }

    private function includes() {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once plugin_dir_path( namespace\FILE ) . 'includes/constants.php';
        require_once plugin_dir_path( namespace\FILE ) . 'includes/php_ext.php';
        require_once plugin_dir_path( namespace\FILE ) . 'includes/php_2js.php';
    }

    private function hooks() {
        
        # HPOS-compatibility
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $orders_page = array_fill(0, 2, 'woocommerce_page_wc-orders');
        # Legacy support
        } else {
            $orders_page = ['edit-shop_order', 'shop_order_posts'];
        }
        # call register settings
        add_action( 'admin_init',                                 array( $this, 'register_plugin_settings' ) ); 
        # insert plugin submenus in WooC section
        add_action( 'admin_menu',                                 array( $this, 'add_submenus__wooc' ), 99 );
        # hide submenus
        add_filter( 'submenu_file',                               array( $this, 'hide_submenus__wooc' ) ); 
        # insert settings link in plugins page 
        add_action( 'plugin_action_links_'.namespace\PLUGIN_FILE, array( $this, 'add_settings__plugins_page' ), 10, 1 );
        # add browser-side scripts                                
        add_action( 'admin_enqueue_scripts',                      array( $this, 'enqueue_user_agent_scripts' ) );
        # insert new table columns                                
        # add a new column to WooCommerce > Orders                
        add_filter( "manage_{$orders_page[0]}_columns",           array( $this, 'add_orders_table_column__orders_history' ), 20 );
        add_filter( "manage_{$orders_page[0]}_sortable_columns",  array( $this, 'add_orders_table_column_sortable__orders_history' ) );
        add_action( 'pre_get_posts',                              array( $this, 'add_orders_table_column_pre_get__orders_history' ) );
        add_action( "manage_{$orders_page[1]}_custom_column",     array( $this, 'add_orders_table_column_custom__orders_history' ), 20, 2 );
        # add a new column to Users > All Users                   
        add_filter( 'manage_users_columns',                       array( $this, 'add_users_table_column__orders_history' ) );
        add_filter( 'manage_users_sortable_columns',              array( $this, 'add_users_table_column_sortable__orders_history' ) );
        add_filter( 'pre_get_users',                              array( $this, 'add_users_table_column_pre_get__orders_history' ) );
        add_filter( 'manage_users_custom_column',                 array( $this, 'add_users_table_column_custom__orders_history' ), 10, 3 );
        # add a metabox to Order edit page                        
        add_action( 'add_meta_boxes',                             array( $this, 'add_metabox__orders_history' ) );
        # add Bulk Actions menu entries to manually rebuild the history cache
        add_filter( "bulk_actions-{$orders_page[0]}",             array( $this, 'bulk_menu_add_entry' ) );
        add_filter( 'bulk_actions-users',                         array( $this, 'bulk_menu_add_entry' ) );
        add_filter( "handle_bulk_actions-{$orders_page[0]}",      array( $this, 'bulk_menu_add_handler' ), 10, 3 );
        add_filter( 'handle_bulk_actions-users',                  array( $this, 'bulk_menu_add_handler' ), 10, 3 );
        add_action( 'admin_notices',                              array( $this, 'bulk_menu_add_notice' ) );
        # enable WC_Order_Query meta_query support
        add_filter( 'woocommerce_get_wp_query_args',              array( $this, 'enable_meta_query' ), 10, 2 );
        # load translations                                        
        load_plugin_textdomain( 'order-status-history-for-woocommerce', false, namespace\DIR . 'languages' );
        # optional update of WC statuses colors                    
        add_action( 'admin_head',                                 array( $this, 'modify_wc_statuses_colors' ) );
        # update of WC widget statuses colors                      
        add_action( 'admin_footer',                               array( $this, 'modify_wc_widget_statuses_colors' ) );
        # add support for generic currency symbol within the report pages, and the WC widget
        if( !empty( get_option( 'oshwoo_multicurrency_symbol' ) ) ) {
            add_filter( 'woocommerce_currencies',                 NS.'\\add_currency' );
            add_filter( 'woocommerce_currency_symbol',            NS.'\\add_currency_symbol', 10, 2);
            # PHP_INT_MAX needed to override other multi-currency tweaking plugins, like Booster
            add_filter( 'woocommerce_currency_symbol',            NS.'\\add_currency_symbol_widget', PHP_INT_MAX, 1 ); 
        }
    }
    
    /**
     *  register plugin settings in wp_options
     */ 
    public function register_plugin_settings() {
        register_setting( 'oshwoo-settings', 'oshwoo_hx_guest',             array( 'default' => HX_GUEST ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_aggregate',         array( 'default' => HX_AGGREGATE ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_pending',           array( 'default' => HX_PENDING ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_processing',        array( 'default' => HX_PROCESSING ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_onhold',            array( 'default' => HX_ONHOLD ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_completed',         array( 'default' => HX_COMPLETED ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_cancelled',         array( 'default' => HX_CANCELLED ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_refunded',          array( 'default' => HX_REFUNDED ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_failed',            array( 'default' => HX_FAILED ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_other',             array( 'default' => HX_OTHER ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_text',              array( 'default' => HX_TEXT ) );
        register_setting( 'oshwoo-settings', 'oshwoo_hx_history',           array( 'default' => HX_HISTORY ) );
        register_setting( 'oshwoo-settings', 'oshwoo_wc_colors_update',     array( 'default' => '1') );
        register_setting( 'oshwoo-settings', 'oshwoo_multicurrency_symbol', array( 'default' => '' ) );
    }

    /**
     *  enqueue browser-side assets to only those pages this plugin works
     */  
    public function enqueue_user_agent_scripts() {
        $screen = get_current_screen();

        # [WC] for resource imports
        $wc_suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $wc_version = get_option( 'woocommerce_version' );

        #error_log( var_export( $screen->id, true ) );        
        switch( $screen->id  ) {

        case 'woocommerce_page_oshwoo-settings':
            # affects settings page only:
            #
            wp_enqueue_style ( 'wp-jquery-ui-dialog');
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_script( 'jquery-csv',      namespace\URL . 'assets/js/jquery.csv.min.js', array('jquery'), '1.0.11', false );
            wp_enqueue_script( 'oshwoo-settings', namespace\URL . 'assets/js/settings.js', array('jquery', 'oshwoo-js', 'jquery-csv'), namespace\VERSION, false );
            wp_enqueue_script( 'oshwoo-themes',   namespace\URL . 'assets/js/themes.js',   array('jquery', 'oshwoo-js'),               namespace\VERSION, false );
            
        case 'woocommerce_page_oshwoo-order-history-t3':
        case 'woocommerce_page_oshwoo-order-history-t2': 
        case 'woocommerce_page_oshwoo-order-history-t1': 

            # [WC] notes bubbles & tooltip
            wp_enqueue_style ( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), $wc_version );
            # [WC] tooltip (activator in oshwoo.js)
            wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip'. $wc_suffix. '.js', array( 'jquery' ), $wc_version, true );

            # affects custom pages only:
            #           
            wp_enqueue_style ( 'oshwoo-css', namespace\URL . 'assets/css/oshwoo.css', array(), namespace\VERSION, false );
            wp_enqueue_script( 'oshwoo-js',  namespace\URL . 'assets/js/oshwoo.js', array('jquery', 'postbox'), namespace\VERSION, false );
            
            # export php variables to js, and define js-specific localized strings
            # attaches to the 'oshwoo-js' handle
            # needs to be done right after the js enqueues for this to work
            php_2js();

        case 'edit-shop_order':
        case 'shop_order':
        case 'woocommerce_page_wc-orders': #<-- HPOS-compatibility
        case 'users':
            # affects all pages concerning this plugin:
            #
            wp_enqueue_style ( 'oshwoo-css', namespace\URL . 'assets/css/oshwoo.css' );
        }
    }

    /**
     *  add plugin submenus into the WooCommerce menu
     */ 
    public function add_submenus__wooc() {

        add_submenu_page('woocommerce', 
                      __('Customer History', 'order-status-history-for-woocommerce'), 
                      __('Customer History', 'order-status-history-for-woocommerce'), 
                         'manage_woocommerce', 
                         'oshwoo-order-history-t1', 
            array($this, 'add_order_history')
                        );
        # hidden
        add_submenu_page('woocommerce', 
                      __('Customer History', 'order-status-history-for-woocommerce'), 
                      __('Customer History', 'order-status-history-for-woocommerce'),  
                         'manage_woocommerce', 
                         'oshwoo-order-history-t2',
            array($this, 'add_order_history')
                        );
        # hidden
        add_submenu_page('woocommerce', 
                      __('Customer History', 'order-status-history-for-woocommerce'), 
                      __('Customer History', 'order-status-history-for-woocommerce'),  
                         'manage_woocommerce', 
                         'oshwoo-order-history-t3', 
            array($this, 'add_order_history')
                        );
        add_submenu_page('woocommerce', 
                      __('Status History Settings', 'order-status-history-for-woocommerce'), 
                      __('Status History Settings', 'order-status-history-for-woocommerce'), 
                         'manage_options', 
                         'oshwoo-settings', 
            array($this, 'add_settings')
                        );
    }

    /**
     *  hide plugin submenus
     *  https://stackoverflow.com/questions/3902760
     */
    public function hide_submenus__wooc( $submenu_file ) {
        global $plugin_page;
        
        # submenus to hide
        $hidden_submenus = array(
            'oshwoo-order-history-t2' => true,
            'oshwoo-order-history-t3' => true,
        );
        # Select another submenu item to highlight instead
        if ( $plugin_page && isset( $hidden_submenus[ $plugin_page ] ) ) {
            $submenu_file = 'oshwoo-order-history-t1';
        }
        # Hide the submenu.
        foreach ( $hidden_submenus as $submenu => $unused ) {
            remove_submenu_page( 'woocommerce', $submenu );
        }
        return $submenu_file;
    }
    
    public function add_order_history() {
        include( namespace\DIR . 'includes/admin/order-history.php' );
    }
    
    public function add_settings() {
        include( namespace\DIR . 'includes/admin/settings.php' );
    }

    /**
     *  Add link to settings page in WP plugins page 
     *  https://neliosoftware.com/blog/how-to-add-a-link-to-your-settings-in-the-wordpress-plugin-list
     */
    public function add_settings__plugins_page( $links ) {
        # build and escape the URL
        $url = esc_url( add_query_arg(
            'page',
            'oshwoo-settings',
            get_admin_url() . 'admin.php'
        ) );
        # create the link
        $settings_link = "<a href='$url'>" . __('Settings', 'order-status-history-for-woocommerce') . '</a>';
        # adds the link to the end of the array
        array_push(
            $links,
            $settings_link
        );
        return $links;
    }
    #<-- init

    #--> Orders page column builder
    /**
     *  Add column to Orders page
     */
    public function add_orders_table_column__orders_history( $columns ) {
        $reordered_columns = array();
        # insert column to a specific location
        foreach( $columns as $key => $column ) {
            $reordered_columns[ $key ] = $column;
            if( $key == 'order_status' ) {
                # insert after 'Status' column
                $reordered_columns['oshwoo_orders_history'] = __('Orders history', 'order-status-history-for-woocommerce');
            }
        }
        return $reordered_columns;
    }

    public function add_orders_table_column_sortable__orders_history( $columns ) {
        $columns['oshwoo_orders_history'] 
               = 'oshwoo_orders_history';
        return $columns;
    }

    # fix sorting: https://stackoverflow.com/questions/48908754/
    function add_orders_table_column_pre_get__orders_history( $query ) {
        
        //error_log( var_export( $query->get('orderby'), true ) );

        if ( 'oshwoo_orders_history' == $query->get('orderby') ) {
            $query->set('meta_key', 'oshwoo_aggregated');
            $query->set('orderby' , 'meta_value_num');
        }

    }
    
    public function add_orders_table_column_custom__orders_history( $column, $order_data ) {

        # HPOS-compatibility
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $customer_order = $order_data; # in HPOS mode the full order object is passed
            $order_id       = $order_data->get_id();
        
        # Legacy support
        } else {

            # [optimization] get order data from the loop, instead of calling wc_get_order on every row
            # https://stackoverflow.com/questions/48908754
            global $post, $the_order;
            $order_id  = $order_data; # in legacy mode only the order id is passed
            
            if ( empty( $the_order ) || $the_order->get_id() != $post->ID ) {
                   $customer_order = wc_get_order( $post->ID );
            } else $customer_order = $the_order;

            # edge: add fix for admin page call with an order_id that isn't actually an order
            if( !$customer_order ) return;
        }
        
        $customer_id   = $customer_order->get_customer_id();
        $billing_email = $customer_order->get_billing_email();
        $is_guest      = $customer_id ? false : true;
        
        list( $orders, $busted_cache ) = $this->get_orders_cache( $customer_id, $billing_email );

        # Total number of Orders placed by this Customer
        $st_aggregated = count( $orders );

        # for Orders history sorting in Orders table, set aggregated totals into wp_postmeta for each Order metadata 
        # note: bust_cache status may change from within get_orders_cache

        # HPOS-compatibility
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            if( $busted_cache ) {
                $customer_order->update_meta_data( 'oshwoo_aggregated', $st_aggregated );
                $customer_order->save();
            }
        # Legacy support
        } else {
            if( $busted_cache ) { 
                update_post_meta( $order_id, 'oshwoo_aggregated', $st_aggregated );
            }
        }
        
        # WC statuses counters init
        $wc_pending = $wc_processing = $wc_onhold = $wc_completed = $wc_refunded = $wc_cancelled = $wc_failed = $st_other = 0;
        
        if( $st_aggregated > 0 ) {
            foreach( $orders as $order ) { #1

                if( method_exists($order, 'get_status') ) 
                    $status = $order->get_status();
                else continue;

                switch( $status ) { #2
                case ST_PENDING:
                          $wc_pending++;
                          break;
                case ST_PROCESSING:
                          $wc_processing++;
                          break;
                case ST_ONHOLD:
                          $wc_onhold++;
                          break;
                case ST_COMPLETED:
                          $wc_completed++;
                          break;
                case ST_CANCELLED:
                          $wc_cancelled++;
                          break;
                case ST_REFUNDED:
                          $wc_refunded++;
                          break;
                case ST_FAILED:
                          $wc_failed++;
                          break;
                default:
                          $st_other++;
                          break;
                } #2
            } #1
        } #if
        
        switch( $column ) { #3
            
        case "oshwoo_orders_history": 

            # [WooCommerce > Orders] Render swatch statuses 
            if ( $is_guest ) { # Render Guest swatch
                echo $this->status_swatch_render( 'ST_GUEST', 'G', __('Order placed as Guest', 'order-status-history-for-woocommerce') );
            }
            if( $st_aggregated > 0 ) {
            # Render status counters for registered customer, or a (returning) Guest
                echo $this->status_swatch_render( 'ST_AGGREGATE',  $st_aggregated, __('Total orders placed',    'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_PENDING',    $wc_pending,    __('Orders pending payment', 'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_PROCESSING', $wc_processing, __('Orders processing',      'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_ONHOLD',     $wc_onhold,     __('Orders on hold',         'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_COMPLETED',  $wc_completed,  __('Orders completed',       'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_CANCELLED',  $wc_cancelled,  __('Orders cancelled',       'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_REFUNDED',   $wc_refunded,   __('Orders refunded',        'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_FAILED',     $wc_failed,     __('Orders failed',          'order-status-history-for-woocommerce') );
                echo $this->status_swatch_render( 'ST_OTHER',      $st_other,      __('Orders in other status', 'order-status-history-for-woocommerce') );
            }
            # Show history page link for orders > 1, to avoid cluttering
            if( $st_aggregated > 1 ) { 
                echo $this->history_swatch_render( $customer_id, $billing_email );
            }
        } #3
    }
    #<-- Orders page column builder

    #--> Users page column builder
    /**
     *  Add column to Users page
     */
    public function add_users_table_column__orders_history( $column ) {
        $column['oshwoo_orders_history'] = __('Orders History', 'order-status-history-for-woocommerce');
        return $column;
    }
    
    public function add_users_table_column_sortable__orders_history( $columns ) {
        $columns['oshwoo_orders_history'] 
               = 'oshwoo_orders_history';
        return $columns;
    }
    
    public function add_users_table_column_pre_get__orders_history( $query ) {
        if ( 'oshwoo_orders_history' == $query->get('orderby') ) {
            $query->set('meta_key', 'oshwoo_aggregated');
            $query->set('orderby' , 'meta_value_num');
        }
    }
    
    public function add_users_table_column_custom__orders_history( $html, $column_name, $user_id ) {
        switch( $column_name ) { #1
        case 'oshwoo_orders_history':
            
            $user_email = strtolower( get_user_by( 'id', $user_id )->user_email );

            list( $orders, $busted_cache ) = $this->get_orders_cache( $user_id, $user_email );
            
            # Total number of Orders placed as a Customer
            $st_aggregated = count( $orders );

            # for Orders history sorting in Users table, set aggregated totals into wp_usermeta for each User metadata 
            # note: bust_cache status may change from within get_orders_cache
            if( $busted_cache ) update_user_meta( $user_id, 'oshwoo_aggregated', $st_aggregated );
            
            # WC statuses counters init
            $wc_pending = $wc_processing = $wc_onhold = $wc_completed = $wc_refunded = $wc_cancelled = $wc_failed = $st_other = 0;
            
            if( $st_aggregated > 0 ) {
                foreach( $orders as $order ) { #2

                    if( method_exists($order, 'get_status') ) 
                        $status = $order->get_status();
                    else continue;
                
                    switch( $status ) { #3
                    case ST_PENDING:
                              $wc_pending++;
                              break;
                    case ST_PROCESSING:
                              $wc_processing++;
                              break;
                    case ST_ONHOLD:
                              $wc_onhold++;
                              break;
                    case ST_COMPLETED:
                              $wc_completed++;
                              break;
                    case ST_CANCELLED:
                              $wc_cancelled++;
                              break;
                    case ST_REFUNDED:
                              $wc_refunded++;
                              break;
                    case ST_FAILED:
                              $wc_failed++;
                              break;
                    default:
                              $st_other++;
                              break;
                    } #3
                } #2

                # [Users > All Users] Render swatch statuses for those WP users that have purchased something
                $html  = '';
                $html .= $this->status_swatch_render( 'ST_AGGREGATE',  $st_aggregated, __('Total orders placed',    'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_PENDING',    $wc_pending,    __('Orders pending payment', 'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_PROCESSING', $wc_processing, __('Orders processing',      'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_ONHOLD',     $wc_onhold,     __('Orders on hold',         'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_COMPLETED',  $wc_completed,  __('Orders completed',       'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_CANCELLED',  $wc_cancelled,  __('Orders cancelled',       'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_REFUNDED',   $wc_refunded,   __('Orders refunded',        'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_FAILED',     $wc_failed,     __('Orders failed',          'order-status-history-for-woocommerce'), 1 );
                $html .= $this->status_swatch_render( 'ST_OTHER',      $st_other,      __('Orders in other status', 'order-status-history-for-woocommerce'), 1 );
                # Always show history page link 
                $html .= $this->history_swatch_render( $user_id, $user_email, 1 );
            } #if
        } #1
        return $html;
    }
    #<-- Users page column builder

    #--> Order page metabox builder
    public function add_metabox__orders_history() {

        # HPOS-compatibility
        if( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $screen = wc_get_page_screen_id( 'shop-order' );
        # Legacy support
        } else {
            $screen = 'shop_order';
        }
        add_meta_box(
            'oshwoo_orders_history',
            __('Orders history', 'order-status-history-for-woocommerce'),
            array( $this, 'add_metabox_data__orders_history' ),
            $screen,
            'side',
            'high'
        );
    }
    
    public function add_metabox_data__orders_history( $post_data, $metabox_data ) {
        
        # HPOS-compatibility
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $order_id = $post_data->get_id();
        # Legacy support
        } else {
            $order_id = $post_data->ID;
        }
        
        $customer_order = wc_get_order( $order_id );
        $customer_id    = $customer_order->get_user_id();
        $billing_email  = $customer_order->get_billing_email();
        $is_guest       = $customer_id ? false : true;
        # to keep it simpler, the cache for a given order gets busted just by viewing it individually
        $orders         = $this->get_orders_cache( $customer_id, $billing_email, true )[0];

        # Total number of Orders placed by this Customer 
        $st_aggregated  = count( $orders );
        
        # WC statuses counters init
        $wc_pending = $wc_processing = $wc_onhold = $wc_completed = $wc_refunded = $wc_cancelled = $wc_failed = $st_other = 0;
        
        if( $st_aggregated > 0 ) {
            foreach( $orders as $order ) { #1
                if( method_exists($order, 'get_status') ) 
                    $status = $order->get_status();
                else continue;
                  
                switch( $status ) { #2
                case ST_PENDING:
                          $wc_pending++;
                          break;
                case ST_PROCESSING:
                          $wc_processing++;
                          break;
                case ST_ONHOLD:
                          $wc_onhold++;
                          break;
                case ST_COMPLETED:
                          $wc_completed++;
                          break;
                case ST_CANCELLED:
                          $wc_cancelled++;
                          break;
                case ST_REFUNDED:
                          $wc_refunded++;
                          break;
                case ST_FAILED:
                          $wc_failed++;
                          break;
                default:
                          $st_other++;
                          break;
                } #2
            } #1
        } #if
        
        # [Edit Order details] Render swatch statuses
        echo '<div id="oshwoo-status-holder">'.PHP_EOL;
        if ( $is_guest ) { # Render Guest swatch
            echo $this->status_swatch_render( 'ST_GUEST', 'G', __('Order placed as Guest', 'order-status-history-for-woocommerce') );
        }
        if( $st_aggregated > 0 ) {
            # Render status counters for registered customer, or a (returning) Guest
            echo $this->status_swatch_render( 'ST_AGGREGATE',  $st_aggregated, __('Total orders placed',    'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_PENDING',    $wc_pending,    __('Orders pending payment', 'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_PROCESSING', $wc_processing, __('Orders processing',      'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_ONHOLD',     $wc_onhold,     __('Orders on hold',         'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_COMPLETED',  $wc_completed,  __('Orders completed',       'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_CANCELLED',  $wc_cancelled,  __('Orders cancelled',       'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_REFUNDED',   $wc_refunded,   __('Orders refunded',        'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_FAILED',     $wc_failed,     __('Orders failed',          'order-status-history-for-woocommerce') );
            echo $this->status_swatch_render( 'ST_OTHER',      $st_other,      __('Orders in other status', 'order-status-history-for-woocommerce') );
        }
        # Show history page link for orders > 1, to avoid cluttering
        if( $st_aggregated > 1 ) {
            echo $this->history_swatch_render( $customer_id, $billing_email );
        }
        echo '</div>' . PHP_EOL;
    }
    #<-- Order page metabox builder

    #--> html renders
    public function status_swatch_render( $status_str, $text, $title, $btitle=false ) {
        $status_color = $this->get_color( $status_str );
        $text_color   = $this->get_color('HX_TEXT');
        $title_type   = $btitle ? 'title' : 'data-tip';

        # Show aggregate swatch only for orders > 1, to avoid cluttering
        if( $status_str == 'ST_AGGREGATE' && $text == 1 ) return;

        return $text ? '<span data-status="' . $text . '" class="oshwoo swatch tips ' . strtolower( $status_str ) . '" '
                     . 'style="color: ' . $text_color . '; background-color: ' . $status_color . ';" ' 
                     . $title_type . '="' . $title . '">'
                     . $text . '</span>' . PHP_EOL : '';
    }

    public function history_swatch_render( $customer_id, $billing_email=false, $btitle=false ) {
        $history_color = $this->get_color('HX_HISTORY');
        $text_color    = $this->get_color('HX_TEXT');
        $title_type    = $btitle ? 'title' : 'data-tip';
        
        if( $customer_id )   $arg['cid'] = $customer_id;
        if( $billing_email ) $arg['eml'] = urlencode( $billing_email ); # necessary to encode special chars like '+'

        return '<a href="' . esc_url( add_query_arg( $arg, site_url( '/wp-admin/admin.php?page=oshwoo-order-history-t1' ) ) )
             . '"><span class="oshwoo swatch icon tips history" '
             . 'style="color: ' . $text_color . '; background-color: ' . $history_color . ';" '
             . $title_type . '="Orders History"></span></a>' . PHP_EOL;
    }

    public function history_button_render( $customer_id, $billing_email, $tab, $type, $title ) {
        if( $customer_id )   $arg['cid'] = $customer_id;
        if( $billing_email ) $arg['eml'] = urlencode( $billing_email ); # necessary to encode special chars like '+'
        $arg['type']   = $type;
        $arg['action'] = 'download';
        
        return '<a href="'. esc_url( add_query_arg( $arg, site_url('/wp-admin/admin.php?page=oshwoo-order-history-'.$tab) ) ) 
              .'" class="button-primary" style="cursor: pointer;" target="_blank">' . $title . '</a>' . PHP_EOL;
    }

    public function settings_button_render( $action, $title ) {
        $html = '';
        switch( $action ) {
        case 'import':
            $html .= '<input type="file" id="oshwoo_import_settings" name="oshwoo_import_settings" style="display:none" onclick="this.value=null" accept=".csv">' . PHP_EOL;
            $html .= '<label for="oshwoo_import_settings" class="button-primary" style="vertical-align:top">' .$title. '</label>' . PHP_EOL;
            break;
        case 'export':
            $arg['action'] = $action;
            $html .= '<a href="'. esc_url( add_query_arg( $arg, site_url('/wp-admin/admin.php?page=oshwoo-settings') ) ) 
                    .'" class="button-primary" style="cursor: pointer;" target="_blank">' .$title. '</a>' . PHP_EOL;
            break;
        }
        return $html;
    }

    public function create_page_tabs( $tab_active, $customer_id, $billing_email ) {
        $arg = array();
        if( $customer_id )   $arg['cid'] = $customer_id;
        if( $billing_email ) $arg['eml'] = urlencode( $billing_email ); # necessary to encode special chars like '+'
        
        $tabs = array(
            't1' => __('Orders', 'order-status-history-for-woocommerce'), 
            't2' => __('Products', 'order-status-history-for-woocommerce'),
            't3' => __('Notes', 'order-status-history-for-woocommerce'),
        );
        $html = '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ) {
            $class = ( $tab == $tab_active ) ? 'nav-tab-active' : '';
            $html .= '<a href="' . esc_url( add_query_arg( $arg, site_url( '/wp-admin/admin.php?page=oshwoo-order-history-'. $tab ) ) )
                  .  '" class="nav-tab ' . $class . '">' . $name . '</a>';
        }
        $html .= '</h2>' . PHP_EOL;
        echo $html;
    }

    /**
     *  Process settings option to update WC color statuses 
     */
    public function modify_wc_statuses_colors() {
        global $pagenow, $post;

        if( $pagenow != 'edit.php' ) return;
        # HPOS-compatibility
        if( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            if( !$post || 'shop_order' !== OrderUtil::get_order_type( $post->ID ) ) return;
        # Legacy support
        } else {
            if( !$post || 'shop_order' !== get_post_type( $post->ID ) ) return;
        }
        if( !get_option( 'oshwoo_wc_colors_update' ) ) return;
        
        echo '<style>'  . PHP_EOL;
        foreach( wc_get_order_statuses() as $status => $value ) {
             # normalize WC status name to get HEX color for it
             $hex_name = 'HX_' . strtoupper( str_replace( '-', '', substr( $status, 3 ) ) );
             //error_log( var_export( $hex_name, true ) );
             echo '.order-status.status-' . status_nwc( $status ) . PHP_EOL;
             # check if a WC status name was passed, and normalize it to extract our HEX counterpart 
             echo '{ color: ' .  $this->get_color('HX_TEXT') . '; background-color: ' . $this->get_color( $hex_name ) . '}' . PHP_EOL;
             
        }
        echo '</style>' . PHP_EOL;
    }
    
    /**
     *  Process settings option to also update WC color widget statuses 
     */
    public function modify_wc_widget_statuses_colors() {
        global $pagenow;

        if( $pagenow != 'index.php' ) return;
        if( !get_option( 'oshwoo_wc_colors_update' ) ) return;

        # unicode to octal, e.g. U+1F680 = \1F680
        ?>
        <style type="text/css">
        /* oshwoo: custom color for 'processing' status */
        #woocommerce_dashboard_status .wc_status_list li.processing-orders a::before
        { color:<?php echo $this->get_color('HX_PROCESSING') ?>; }
        /* oshwoo: custom color for 'on-hold' status */
        #woocommerce_dashboard_status .wc_status_list li.on-hold-orders a::before
        { color:<?php echo $this->get_color('HX_ONHOLD') ?>; }
        /* oshwoo: custom icon for'Pending' status */
        #woocommerce_dashboard_status .wc_status_list li.pending a::before
        { content:'\e016'; color:<?php echo $this->get_color('HX_PENDING') ?>; }
        /* oshwoo: custom icon for'Best Seller this month' */
        #woocommerce_dashboard_status .wc_status_list li.best-seller-this-month a::before
        { content:'\1F680'; color:#000; }
        </style>
        <?php
    }

    /**
     *  Invokes the cache buster manually for all orders from inside the Bulk actions menu  
     */
    public function bulk_menu_add_entry( $actions ) {
        $actions['osh_do'] = __('Rebuild history cache', 'order-status-history-for-woocommerce');
        return $actions;
    }

    /**
     *  delete all history transients for all orders, to rebuild the cache again
     *  (need to check something in the list to make this filter fire up)
     */
    public function bulk_menu_add_handler( $redirect, $action, $ids ) {
        global $wpdb;
        
        if( $action == 'osh_do' ) {
            $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_osh_orders_query__wp-%'" );
            $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_osh_orders_query__wp-%'" );
            
            $redirect = add_query_arg( array( 'osh_done' => '1' ), $redirect );
        }
        //error_log( var_export( $redirect, true ) );
        return $redirect;
    }
    /**
     *  show system message that the cache was rebuild
     */
    public function bulk_menu_add_notice() {
        if( isset( $_REQUEST['osh_done'] ) ) {
            echo'<div id="message" class="updated">'
            .__('History cache rebuilt done', 'order-status-history-for-woocommerce')
               .'</div>';
        }
    }
    
    #<-- html renders

    #--> DB access    

    /**
     *  Retrieves orders as cached WC_Order objects
     */
    public function get_orders_cache( $customer_id, $billing_email, $busted_cache = false, $status = false, $limit = -1 ) {

        # edge: for guest order with no email there's nothing to do (e.g. manual drafts)
        if( !$customer_id && !$billing_email ) return array([], $busted_cache);
        
        # define uniqueness of the cached query
        $transient_suffix = $customer_id ? $customer_id : $billing_email;

        # check if caller may want the cache busted or retrieved
        if( $busted_cache ) delete_transient( 'osh_orders_query__wp-' . $transient_suffix );

        # check for cached query. If none, then execute actual query
        if( $busted_cache || ( false === ( $orders = get_transient( 'osh_orders_query__wp-' . $transient_suffix ) ) ) ) {

            $orders = $this->get_orders( $customer_id, $billing_email, $status, $limit );

            # for consistent handling of possibly incomplete/manually entered orders/drafts
            if( !is_countable( $orders ) ) $orders = array();

            # hours-time-to-live of the transient cache before getting stale (default 1h)
            $cache_ttl = apply_filters( 'oshwoo_cache_ttl', 1 );
            
            # put new query results in another transient. Expire after default of 1 hour
            set_transient( 'osh_orders_query__wp-' . $transient_suffix , $orders, $cache_ttl * 60 * 60 );

               # to inform the caller of the cache state
               $busted_cache = true;
        } else $busted_cache = false;

        return array($orders, $busted_cache);
    }

    /**
     *  Retrieves WC_Order objects 
     */
    public function get_orders( $customer_id, $billing_email, $status = false, $limit = -1 ) {

        # edge: for guest order with no email there's nothing to do (e.g. manual drafts)
        if( !$billing_email && !$customer_id ) return array();

        # Get all Orders linked to a WC customer (registered or guest)
            $data = 
                 [
                  'limit'       => $limit,
                  'orderby'     => 'date',
                  'order'       => 'DESC',
                  'type'        => array('shop_order'),
                  'return'      => 'objects',
                  # WC_Order_Query expects non-prefixed status
                  'status'      => ( $status ? status_nwc($status) : array_keys( $this->get_order_statuses() ) ),
                 ];

        # HPOS-compatibility
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {

            # orders made as a registered customer
            if( $customer_id ) {
                $data['field_query'][] = 
                    [
                        'field' => 'customer_id',
                        'value' => $customer_id
                    ];
            }

            # orders made as guest, or logged-out
            if( $billing_email ) {
                if( $customer_id ) {
                $data['field_query']
                     ['relation']      = 'OR';
                }
                $data['field_query'][] = 
                     [
                      'field'       => 'billing_email',
                      'value'       => $billing_email,
                     ];
            }

        # Legacy support
        } else {

            # orders made as a registered customer
            if( $customer_id ) {
                $data['meta_query'][] = 
                     [
                      'key'         => '_customer_user',
                      'value'       => $customer_id,
                     ];
            }

            # orders made as guest, or logged-out
            if( $billing_email ) {
                if( $customer_id ) {
                $data['meta_query']
                     ['relation']     = 'OR'; 
                }
                $data['meta_query'][] = 
                     [
                      'key'         => '_billing_email',
                      'value'       => $billing_email,
                     ];
            }
        
        }
        
        //error_log( var_export( $data, true ) );
        $query  = new \WC_Order_Query( $data );  # meta_query support must be enabled for Legacy support
        $orders = $query->get_orders();

        # for consistent handling of possibly incomplete/manually entered orders/drafts
        if( !is_countable( $orders ) ) $orders = array();

        return $orders;
    }

    /**
     *  Enable WC_Order_Query meta_query support
     *  https://wordpress.stackexchange.com/questions/337852
     */
    public function enable_meta_query( $wp_query_args, $query_vars ) {
        
        # HPOS-compatibility doesn't require meta_query
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) return $wp_query_args;
        
        // Legacy support
        if ( isset( $query_vars['meta_query'] ) ) {
            $meta_query = isset( $wp_query_args['meta_query'] ) ? $wp_query_args['meta_query'] : [];
            $wp_query_args['meta_query'] = array_merge( $meta_query, $query_vars['meta_query'] );
        }
        return $wp_query_args;
    }

    #<-- DB access

    #--> class helpers

    /**
     *  Get valid statuses for query types that do not support wc prefixes
     *  https://woocommerce.wp-a2z.org/oik_api/wc_api_ordersget_order_statuses/
     */
    public static function get_order_statuses() {
        $order_statuses = array();        
        foreach( wc_get_order_statuses() as $slug => $name ) {
            $order_statuses[ str_replace( 'wc-', '', $slug ) ] = $name;
        }
        return $order_statuses;
    }
    
    /**
     *  Get customized (wp_options) or default (const) colors
     */
    public function get_color( $str_name ) {
        # extract hex const from paired status counterpart (st/hx pair must exists)
        $str_name = strtoupper( $str_name );
            if( strpos( $str_name, 'ST_' ) !== false ) $hex_name = 'HX_' . substr( $str_name, 3);
        elseif( strpos( $str_name, 'HX_' ) !== false ) $hex_name =                 $str_name;
        else return;
        //error_log( var_export( "{$hex_name}=" . defined(__NAMESPACE__ . "\\{$hex_name}"), true ) );
        # get value from global const
        if( !defined( __NAMESPACE__ . "\\{$hex_name}" ) || !( $hex_val = constant( __NAMESPACE__ . "\\{$hex_name}" ) ) ) return;
        return empty( get_option( 'oshwoo_' . strtolower( $hex_name ) ) ) ? $hex_val 
                    : get_option( 'oshwoo_' . strtolower( $hex_name ) );
    }
    #<-- class helpers

} # osh class
