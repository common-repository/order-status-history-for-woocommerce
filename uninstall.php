<?php
/**
 * Uninstaller
 *
 * @package osh
 */

# no direct page access
defined( 'ABSPATH' ) || exit;

# Delete from wp_usermeta the aggregated meta-key from each Customer metadata
$user_ids = get_users( array(
	'blog_id' => '',
	'fields'  => 'ID',
) );
foreach( $user_ids as $user_id ) {
	delete_user_meta( $user_id, 'oshwoo_aggregated' );
}
#
# Delete from wp_usermeta the aggregated meta-key from each Order metadata
$order_ids = get_posts(array(
    'numberposts' => -1,
    'post_type'   => array('shop_order'),
));
foreach( $order_ids as $order_id ) {
    #--> HPOS-compatibility
   #delete_post_meta( $order_id, 'oshwoo_aggregated' );
    $order_hpos = wc_get_order( $order_id );
    $order_hpos->delete_meta_data( 'oshwoo_aggregated' );
    $order_hpos->save();
    #<--
}
#
# Wipe wp_options from all custom settings (like color codes, etc.) stored by this plugin
foreach ( wp_load_alloptions() as $option => $value ) {
    if ( strpos( $option, 'oshwoo_' ) === 0 ) {
        delete_option( $option );
    }
}
#
# Wipe all history orders cache transients
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_osh_orders_query__wp-%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_osh_orders_query__wp-%'" );

#
# Clear any cached data that has been removed
wp_cache_flush();

/* bye! */
