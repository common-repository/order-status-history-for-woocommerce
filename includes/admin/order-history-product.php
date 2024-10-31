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
$total_amount        = array_fill_keys( array_keys( wc_get_order_statuses() ), 0 );
$aggregated_products = 0;
$st_aggregated       = 0;
$customer_email      = $eml;
$customer_id         = 0;
$customer_name       = '';

# compile common data from all placed Orders
foreach( $orders as $order ) {
    $customer_id         = $customer_id    ? $customer_id    :      $order->get_customer_id();
    $customer_name       = $customer_name  ? $customer_name  : trim($order->get_formatted_billing_full_name()); // redundant space when unset
    $customer_currency[] = $order->get_currency();
    $orders_status_wc[]  = status_wc( $order->get_status() ); // for clarity duplicates allowed
}
# edge: manual order with no name
if( !$customer_name ) $customer_name = __('Nameless', 'order-status-history-for-woocommerce');

# get all statuses supported by WC (prefixed)
$order_statuses_wc = wc_get_order_statuses();

# total orders placed for the given Customer
$st_aggregated  = count( $orders );

//error_log( var_export( $customer_name, true ) );

?>
<div class="wrap">
<h1>
<span class="dashicons dashicons-admin-users" style="padding-top: 7px;"></span>
<?php esc_html_e('Customer Products History', 'order-status-history-for-woocommerce') ?>
</h1>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2 oshwoo-product">
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
<?php esc_html_e('Orders & Products', 'order-status-history-for-woocommerce') ?>
</h2>
<div class="inside">
    <table class="widefat">
    <thead>
    <tr>
        <th><b>#</b></th>
        <th><b><?php esc_html_e('Order#',        'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('SKU',           'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Image',         'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Product Title', 'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Qty',           'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Unit Price',    'order-status-history-for-woocommerce') ?></b></th>
        <th><b><?php esc_html_e('Qty * Price',   'order-status-history-for-woocommerce') ?></b></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $cnt = 1;
    foreach( $orders as $order ) { #2

        # only process those orders in the given status of loop #1 
        if( status_wc( $order->get_status() ) != $status ) continue;

        $items = $order->get_items();

        # edge: manual orders with no items
        if( !$items ) {
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
                <!-- SKU -->
                <td>&nbsp;</td>
                <!-- Image -->
                <td>&nbsp;</td>
                <!-- Product -->
                <td>&nbsp;</td>
                <!-- qty -->
                <td>&nbsp;</td>
                <!-- price -->
                <td>&nbsp;</td>
                <!-- qty*price -->
                <td>&nbsp;</td>
            </tr>
        <?php
        }
        
        foreach( $items as $item ) { #3
            # get frontend link to product 
            $url = get_the_permalink( $item['product_id'] );

            # get SKU
            # No HPOS-compatibility required, as sku is fetched from products, not orders
            $sku = $item['variation_id'] ? get_post_meta( $item['variation_id'], '_sku', true ) 
                                         : get_post_meta( $item['product_id']  , '_sku', true );

            # get product image, if isn't available load default
            if( has_post_thumbnail( $item['product_id'] ) ) {
                   $img = get_the_post_thumbnail( $item['product_id'], array(75, 75), array( 'class' => 'product-thumbnail' ) );
            } else $img = '<img src="'. namespace\URL . 'assets/images/default.png' . '" width="75" height="75" alt="no image available" />';
            
            # support for composite products and their components
            $meta_data    = $item->get_meta_data();

            #error_log( var_export( $meta_data, true ) );

            $item_data    = ( is_array( $meta_data ) && array_key_exists( 0, $meta_data ) && is_object( $meta_data[0] ) ) ? $meta_data[0]->get_data() : array();
            $is_composite = preg_match('/.*component.*/i', json_encode( $item_data ) );

            $item_data    = ( is_array( $meta_data ) && array_key_exists( 1, $meta_data ) && is_object( $meta_data[1] ) ) ? $meta_data[1]->get_data() : array();
            $is_component = preg_match('/.*component.*/i', json_encode( $item_data ) );

            #
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
                <!-- SKU -->
                <td "row-title">
                <?php if( $sku ): ?>
                <?php if( $is_component ): # no links to components ?>
                <?php echo $sku ?>
                <?php else: ?>
                <a href="<?php echo $url ?>"><?php echo $sku ?></a>
                <?php endif ?>
                <?php endif ?>
               </td>
                <!-- Image -->
                <td "row-title">
                <?php if( $is_component ): # no links to components ?>
                <?php echo $img ?>
                <?php else: ?>
                <a href="<?php echo $url ?>" title="<?php echo htmlspecialchars( $item['name'] ) ?>"><?php echo $img ?></a>
                <?php endif ?>
               </td>
                <!-- Product -->
                <td><?php echo htmlspecialchars( $item['name'] ) ?></td>
                <!-- qty -->
                <td><?php echo  $item['total'] ? $item['quantity'] : ''; # hide 0/1 of zero-priced products ?></td>
                <!-- price -->
                <td><?php echo ($item['total'] && $item['quantity']) ? wc_price( bcdiv( $item['total'], $item['quantity'], 2 ), array( 'currency' => $order->get_currency() ) ) : '' ?></td>
                <!-- qty*price -->
                <td><?php echo  $item['total'] ? wc_price( $item['total'], array( 'currency' => $order->get_currency() ) ) : '' ?></td>
            </tr>
            <?php
            $total_amount[$status] += $item['total'];
            # only count non-zero-priced products
            if( $item['total'] ) $aggregated_products += $item['quantity'];
        } #3
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
    <tr class="<?php oddrow('alternate') ?>">
        <td>
            <label for="tablecell">
                <?php esc_html_e('Number of products', 'order-status-history-for-woocommerce') ?>:</label>
        </td>
        <td class="row-title">
            <?php echo _global('aggregated_products') ?>
        </td>
    </tr>
    <?php foreach( _global('order_statuses_wc') as $status => $status_text ): # get total amounts for each status ?>

        <?php if( _global('total_amount')[$status] > 0 ): ?>
        <tr class="<?php oddrow('alternate') ?>">
            <td>
                <?php esc_html_e('Total', 'order-status-history-for-woocommerce') ?> <?php echo strtolower($status_text) ?>:
            </td>
            <td class="row-title">
                <?php echo wc_price( _global('total_amount')[$status], check_currency(_global('customer_currency')) ) ?>
                <?php echo wc_help_tip( sprintf( /* translators: %s: status text */
                                             __( 'Total in %s orders & products, minus shipping', 'order-status-history-for-woocommerce' ), 
                                             strtolower($status_text) ) ) ?>
            </td>
        </tr>
        <?php endif ?>

    <?php endforeach; #status ?>
    </tbody>
    </table>
<?php
}); # /oshwoo_summary
?>
<!------------------------------- /Summary ------------------------------------>
<!-------------------------------- Download ----------------------------------->
<?php
add_meta_box('oshwoo_actions-product', __('Actions', 'order-status-history-for-woocommerce'), function() {
    echo osh()->history_button_render( _global('cid'), _global('eml'), _global('tab'), 'products', __('Download Product History', 'order-status-history-for-woocommerce') );
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
