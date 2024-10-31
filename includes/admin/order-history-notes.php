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
$total_amount   = array_fill_keys( array_keys( wc_get_order_statuses() ), 0 );
$st_aggregated  = 0;
$customer_email = $eml;
$customer_id    = 0;
$customer_name  = '';

# compile common data from all placed Orders
foreach( $orders as $order ) {
    $customer_id         = $customer_id    ? $customer_id    :      $order->get_customer_id();
    $customer_name       = $customer_name  ? $customer_name  : trim($order->get_formatted_billing_full_name()); // redundant space when unset
    $customer_currency[] = $order->get_currency();
    $orders_status_wc[]  = status_wc( $order->get_status() ); // for clarity duplicates allowed
}

# edge: manual order with no name
if( !$customer_name ) $customer_name = __('Nameless', 'order-status-history-for-woocommerce');;

# get all statuses supported by WC (prefixed)
$order_statuses_wc = wc_get_order_statuses();

# total orders placed for the given Customer
$st_aggregated = count( $orders );

//error_log( var_export( $orders, true ) );

?>
<div class="wrap">
<h1>
<span class="dashicons dashicons-admin-users" style="padding-top: 7px;"></span>
<?php esc_html_e('Customer Notes History', 'order-status-history-for-woocommerce') ?>
</h1>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2 oshwoo-notes">
<form method="post" action="">
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
<?php wp_nonce_field( 'meta-box-order',  'meta-box-order-nonce', false ) ?>

<!-- main column -->
<div id="post-body-content">

<!-- Notice -->
<div class="notice"><p>
<?php if( $customer_id ): # search Customer in Users page ?>

<a href="<?php echo esc_url( add_query_arg( array('s' => urlencode($customer_email)), site_url('/wp-admin/users.php') ) ) ?>"><?php
      esc_html_e('Registered', 'order-status-history-for-woocommerce') ?></a>
<?php esc_html_e('Customer',   'order-status-history-for-woocommerce') ?>:&nbsp;
<a href="<?php echo esc_url( add_query_arg( array('s' => urlencode($customer_email), 'post_type' => 'shop_order'), site_url('/wp-admin/edit.php') ) ) ?>">
<b><?php echo htmlspecialchars( $customer_name ) ?></b></a>

<?php else: # search Guest in Orders page ?>

<?php esc_html_e( 'Guest Customer', 'order-status-history-for-woocommerce') ?>:&nbsp;
<a href="<?php echo esc_url( add_query_arg( array('s' => urlencode($customer_email), 'post_type' => 'shop_order'), site_url('/wp-admin/edit.php') ) ) ?>">
<b><?php echo htmlspecialchars( $customer_name ) ?></b></a>

<?php endif ?>
</p></div>
<!-- /Notice -->

<!-- table start  -->

<?php 
foreach( $order_statuses_wc as $status => $status_text ) { #1 
   
    # if no orders were found in the given status, check next status
    if( !in_array( $status, $orders_status_wc ) ) continue;
?>

<div class="meta-box-sortables ui-sortable">
<div class="postbox">
<h2>
<?php echo ucfirst( $status_text ) ?> 
<?php esc_html_e('Orders', 'order-status-history-for-woocommerce') ?>
</h2>
<div class="inside">
    <table class="widefat">
    <thead>
    <tr>
        <th><b>#</b></th>
        <th><b><?php esc_html_e('Order#',         'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Notes',          'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Shipping Costs', 'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Total',          'order-status-history-for-woocommerce') ?></b></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $cnt = 1;
    foreach( $orders as $order ) { #2

        # only process those orders in the given status of loop #1 
        if( status_wc( $order->get_status() ) != $status ) continue;
        ?>
            <tr class="<?php oddrow('alternate') ?>">
                <!-- Counter -->
                <td><?php echo $cnt++ ?></td>
                <!-- Order -->
                <td "row-title">
                   <a href="<?php echo esc_url( add_query_arg( array('post' => $order->get_id(), 'action' => 'edit'), site_url('/wp-admin/post.php') ) ) ?>">
                            <?php echo $order->get_order_number() ?>
                   </a>
               </td>
                <!-- Note -->
                <td "row-title" style="width: 70%">
                  <ul class="order_notes">
                  
                  <li rel="<?php echo $order->get_id() ?>" class="note order-note">
                  <?php if( $order->get_customer_note() ) { ?>
                    <div class="note_content">
                    <p><?php echo $order->get_customer_note() ?></p>
                    </div>
                    <p class="meta">
                    <abbr class="exact-date" title="<?php echo date( 'd/m/y H:i', strtotime( $order->get_date_created() ) ) ?>">
                    <?php echo esc_html('added on', 'order-status-history-for-woocommerce') . ' '. date( 'd/m/y H:i', strtotime( $order->get_date_created() ) ) ?>
                    </abbr>
                    <?php echo esc_html('by', 'order-status-history-for-woocommerce') . ' '. htmlspecialchars( $customer_name ) ?>
                    </p>
                    </li>
                  <?php } #endif ?>
                  <?php 
                  # get all notes for a given order (private, and note to customer)
                  # https://pastebin.com/X64neRP2
                  $notes = wc_get_order_notes([ 'order_id' => $order->get_id(), 
                                                'order_by' => 'date_created'
                                              ]);
                  //error_log( var_export( $notes, true ) );
                  foreach( $notes as $note ) { #3
                    # get operator notes only
                    if( $note->added_by == 'system') continue;
                    ?>
                    <li rel="<?php echo $note->id ?>" class="note <?php echo ($note->customer_note ? 'customer-note' : '') ?>">
                    <div class="note_content">
                    <p><?php echo $note->content ?></p>
                    </div>
                    <p class="meta">
                    <abbr class="exact-date" title="<?php echo date( 'd/m/y H:i', strtotime( $note->date_created ) ) ?>">
                    <?php echo esc_html('added on', 'order-status-history-for-woocommerce') . ' '. date( 'd/m/y H:i', strtotime( $note->date_created ) ) ?>
                    </abbr>
                    <?php echo esc_html('by', 'order-status-history-for-woocommerce') . ' '. $note->added_by ?>
                    </p>
                    </li>
                  <?php } #3 ?>
                  </ul>
               </td>
               <!-- Shipping Cost -->
               <td>
                   <?php echo wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) ) ?>
               </td>
               <!-- Order Total -->
               <td>
                   <?php echo wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ?>
               </td>
           </tr>
        <?php
    $total_amount[$status] += $order->get_total();
    } #2 
    ?>
    </tbody>
    </table>
</div><!-- /inside -->
</div><!-- /postbox -->
</div><!-- /meta-box-sortables .ui-sortable -->

<?php } #1 ?>

<!-- table end  -->

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
add_meta_box('oshwoo_customer-highlights', __('Highlights', 'order-status-history-for-woocommerce'), function() {

        echo '&bull;';
        esc_html_e('Private notes aren\'t always relevant, '
                  .'because WooCommerce doesn\'t currently differentiate between system notes generated from changes in order status, '
                  .'from those notes manually added by the operator.', 'order-status-history-for-woocommerce');
        echo '<br>'; 

});
?>
<!------------------------------- ^ Highlights ------------------------------->
<!-------------------------------- Summary ------------------------------------>
<?php
add_meta_box('oshwoo_summary-product', __('Summary', 'order-status-history-for-woocommerce'), function() {
?>
    <table class="widefat">
    <tbody>
    <tr class="<?php oddrow('alternate') ?>">
        <td>
            <label for="tablecell">
                <?php esc_html_e('Number of orders', 'order-status-history-for-woocommerce') ?>:</label>
        </td>
        <td class="row-title">
            <?php echo _global('st_aggregated') ?>
        </td>
    </tr>
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
<!------------------------------- /Summary ------------------------------------>
<!-------------------------------- Download ----------------------------------->
<?php
add_meta_box('oshwoo_actions-notes', __('Actions', 'order-status-history-for-woocommerce'), function() {
    echo osh()->history_button_render( _global('cid'), _global('eml'), _global('tab'), 'notes', __('Download Notes History', 'order-status-history-for-woocommerce') );
});
?>
<!------------------------------- /Download ----------------------------------->
<!-------------------------------- Donate ------------------------------------->
<?php if( osh()->show_donation_box ) include( namespace\DIR . 'includes/parts/donation_box.php' ) ?>
<!------------------------------- /Donate ------------------------------------->

<?php do_meta_boxes( get_current_screen()->id, 'advanced', null ) ?>

</div><!-- /postbox-container-1 -->
<!-- /sidebar -->

</form>
</div><!-- /post-body -->
<br class="clear">
</div><!-- /poststuff -->
</div><!-- /wrap -->
