<?php 
namespace oshwoo;

#
# check access
#
# this page opens in a tab
# no direct page access
defined( 'ABSPATH' ) || exit;

# $cid, $eml populated from order-history.php

# if no orders were found there's nothing else to do
if( empty( $orders = osh()->get_orders( $cid, $eml ) ) ):
    wp_die( __('No Orders found', 'order-status-history-for-woocommerce') );
endif;
#
# define variables (nested scope)
#
# (workaround to fix 'undefined index' notice)
$total_amount      = array_fill_keys( array_keys( wc_get_order_statuses() ), 0 );
$highlights_guest  = false;
$customer_email    = $eml;
$customer_address  = '';
$customer_phone    = '';

# compile common data from all placed Orders
foreach( $orders as $order ) {
    $customer_address    = $customer_address ? $customer_address : $order->get_formatted_billing_address();
    $customer_phone      = $customer_phone   ? $customer_phone   : $order->get_billing_phone();
    $customer_currency[] = $order->get_currency();
}

//error_log( var_export( $customer_currency, true ) );
?>
<div class="wrap">
<h1>
<span class="dashicons dashicons-admin-users" style="padding-top: 7px;"></span>
<?php esc_html_e('Customer Orders History', 'order-status-history-for-woocommerce') ?>
</h1>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2 oshwoo-customer">
<form method="post" action="">
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
<?php wp_nonce_field( 'meta-box-order',  'meta-box-order-nonce', false ) ?>

<!-- main column -->
<div id="post-body-content">

<!-------------------------------- Statuses ------------------------------------>
<?php

$order_statuses_wc = wc_get_order_statuses();

foreach( $order_statuses_wc as $status => $status_text ) { #1
   
    $orders = osh()->get_orders( $cid, $eml, $status );

    $status_orders_count = count( $orders );

    if( $status_orders_count != 0 ):
    ?>
    <div class="meta-box-sortables ui-sortable">
    <div class="postbox">
    <h2><?php echo $status_text ?> <?php esc_html_e('Orders', 'order-status-history-for-woocommerce') ?></h2>
    <div class="inside">
    <table class="widefat">
    <thead>
    <tr>
        <th><b>#</b></th>
        <th><span title="<?php esc_attr_e('Guest Order', 'order-status-history-for-woocommerce') ?>">
            <b><?php esc_html_e('G',               'order-status-history-for-woocommerce') ?></b></span></th>
        <th><b><?php esc_html_e('Order#',          'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Customer',        'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Date',            'order-status-history-for-woocommerce') ?> <?php echo $status_text ?></b></th>
        <th><b><?php esc_html_e('Payment Method',  'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Billing Address', 'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Phone',           'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Shipping Costs',  'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Total',           'order-status-history-for-woocommerce') ?></b></th>
    </tr>
    </thead>
    <tbody>
    <?php 
    $cnt = 1;
    foreach ( $orders as $order ) { #2
        switch( $status ) {
            case ST_COMPLETED:
                 $order_date = $order->get_date_completed();
                 break;
            case ST_REFUNDED:
                 $order_date = $order->get_date_paid();
                 break;
            case ST_PENDING:
            case ST_PROCESSING:
            case ST_ONHOLD:
            case ST_CANCELLED:
            case ST_FAILED:
            default:
                 $order_date = $order->get_date_created();
        }
        $is_guest = ( !$order->get_customer_id() ) ? true : false;
        # if there's at least 1 guest order mark so to show highlights for it
        if( $is_guest ) $highlights_guest = true; 
        
        //error_log( var_export( $order->get_user()->user_login, true ) );
        ?>
       <tr class="<?php oddrow('alternate') ?>">
           <!-- Counter -->
           <td><?php echo $cnt++ ?></td>
           <!-- Guest -->
           <td><?php echo $is_guest ? 'Y' : '' ?></td>
           <!-- Order -->
           <td class="row-title">
               <a href="<?php echo esc_url( add_query_arg( array('post' => $order->get_id() ,'action' => 'edit') , site_url('/wp-admin/post.php') ) ) ?>">
                        <?php echo $order->get_order_number() ?>
               </a>
           </td>
            <!-- Customer -->
           <td>
                <?php if( $is_guest ): # search Guest in Orders page ?>
                <a href="<?php echo esc_url( add_query_arg( array('s' => $order->get_id(), 'post_type' => 'shop_order' ) , site_url('/wp-admin/edit.php') ) ) ?>">
                <?php echo htmlspecialchars( $order->get_formatted_billing_full_name() ) ?>
                </a>
                <?php else: # search Customer in Users page ?>
                <a href="<?php echo esc_url( add_query_arg( array('s' => $order->get_billing_email() ) , site_url('/wp-admin/users.php') ) ) ?>">
                <?php echo htmlspecialchars( $order->get_formatted_billing_full_name() ) ?>
                </a>
                <?php endif ?>
           </td>
           <!-- Date -->
           <td>
               <?php echo date("l, M j, Y - g:i a", strtotime( $order_date )) ?>
           </td>
           <!-- Shipping -->
           <td>
               <?php echo $order->get_payment_method_title() ?>
           </td>
           <td>
               <!-- Billing details -->
               <?php
               echo htmlspecialchars( $order->get_billing_city() . " - " . $order->get_billing_postcode() . ", " );
               echo htmlspecialchars( $order->get_billing_country() );
               ?>
           </td>
           <td>
               <?php echo $order->get_billing_phone() ?>
           </td>
           <td>
               <?php echo wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) ) ?>
           </td>
           <td>
               <?php echo wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ?>
           </td>
       </tr>
        <?php
        $total_amount[$status] += $order->get_total();
    }; #2
    ?>
    </tbody>
    </table>
    </div><!-- /inside -->
    </div><!-- /postbox -->
    </div><!-- /meta-box-sortables .ui-sortable -->

    <?php endif ?>

<?php } #1 ?>
<!-------------------------------- /Statuses --------------------------------->

</div><!-- /post-body-content -->
<!-- /main column -->

<!-- sidebar -->
<div id="postbox-container-1" class="postbox-container">
<?php
# Get access from globals of page variables in nested scope from include()
# required once per page / context switch for _global() to work
scope_vars( get_defined_vars() );
?>
<!------------------------------- v Highlights -------------------------------->
<?php
if( $highlights_guest || check_currency($customer_currency) == array('currency' => 'AAA') ) {

add_meta_box('oshwoo_customer-highlights', __('Highlights', 'order-status-history-for-woocommerce'), function() {
  
    if( _global('highlights_guest') ) {
        echo '&bull;';
        esc_html_e('Guest Orders are also displayed. For example, a repeating Guest, '
                  .'or a registered Customer that has not signed-in at the time of purchase.', 'order-status-history-for-woocommerce');
        echo '<br>'; 
    }

    # These were transactions in multiple currencies 
    if( check_currency( _global('customer_currency') ) == array('currency' => 'AAA') ) {
        echo '&bull;';
        esc_html_e('Orders placed in varied currencies are simply added up here without performing any conversion. ' 
                  .'Therefore, a generic currency symbol next to the given amount is shown instead.', 'order-status-history-for-woocommerce');
    }
});

}
?>
<!------------------------------- ^ Highlights ------------------------------->
<!------------------------------- v Summary ---------------------------------->
<?php
add_meta_box('oshwoo_summary-customer', __('Summary', 'order-status-history-for-woocommerce'), function() {
?>
    <table class="widefat">
    <tbody>
    <?php
    # get total orders placed from cache
    $orders = osh()->get_orders_cache( _global('cid'), _global('eml'), true )[0];

    $st_aggregated = count( $orders );

    if( $st_aggregated > 0 ): #1
    ?>
    <tr class="<?php oddrow('alternate') ?>">
        <td>
            <label for="tablecell">
                <?php esc_html_e('Number of orders', 'order-status-history-for-woocommerce') ?>:</label>
        </td>
        <td class="row-title">
            <?php echo $st_aggregated ?>
        </td>
    </tr>
    <?php endif; #1 ?>

    <?php foreach( _global('order_statuses_wc') as $status => $status_text ): #2 get total amounts for each status ?>

        <?php if( _global('total_amount')[$status] > 0 ): #3 ?>
        <tr class="<?php oddrow('alternate') ?>">
            <td>
                <?php esc_html_e('Total', 'order-status-history-for-woocommerce') ?> <?php echo strtolower($status_text) ?>:
            </td>
            <td class="row-title">
                <?php echo wc_price( _global('total_amount')[$status], check_currency(_global('customer_currency')) ) ?>
                <?php echo wc_help_tip( sprintf( /* translators: %s: status text */
                                             __( 'Total in %s orders, plus shipping', 'order-status-history-for-woocommerce' ), 
                                             strtolower($status_text) ) ) ?>
            </td>
        </tr>
        <?php endif; #3 ?>

    <?php endforeach; #2 ?>

    </tbody>
    </table>
    
<?php
}); # /oshwoo_summary
?>
<!------------------------------- ^ Summary ---------------------------------->
<!------------------------------- v Address ---------------------------------->
<?php
add_meta_box('oshwoo_address-customer', __('Customer Address', 'order-status-history-for-woocommerce'), function() {
    echo _global('customer_address');
    echo '<br> Phone:' . PHP_EOL;
    echo _global('customer_phone');
    echo '<br> Email:' . PHP_EOL;
    echo _global('customer_email');
});
?>
<!------------------------------- ^ Address ---------------------------------->
<!------------------------------- v Download --------------------------------->
<?php
add_meta_box('oshwoo_actions-customer', __('Actions', 'order-status-history-for-woocommerce'), function() {
    echo osh()->history_button_render( _global('cid'), _global('eml'), _global('tab'), 'orders', __('Download Order History', 'order-status-history-for-woocommerce') );
});
?>
<!------------------------------- ^ Download --------------------------------->
<!-------------------------------- Donate ------------------------------------>
<?php if( osh()->show_donation_box ) include( namespace\DIR . 'includes/parts/donation_box.php' ) ?>
<!------------------------------- /Donate ------------------------------------>

<?php do_meta_boxes( get_current_screen()->id, 'advanced', null ) ?>

</div><!-- /postbox-container-1 -->
<!-- /sidebar -->

</form>
</div><!-- /post-body -->
<br class="clear">
</div><!-- /poststuff -->
</div><!-- /wrap -->
