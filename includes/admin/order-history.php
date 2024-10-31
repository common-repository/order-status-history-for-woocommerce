<?php
namespace oshwoo;

#
# check access
#
# no direct page access
defined( 'ABSPATH' ) || exit;

#
# Get Customer
#
$cid = isset( $_GET['cid'] ) && ctype_digit( $_GET['cid'] ) ? $_GET['cid'] : NULL;
$eml = isset( $_GET['eml'] ) &&  filter_var( $_GET['eml'], FILTER_VALIDATE_EMAIL ) ? $_GET['eml'] : '';

//error_log( var_export( $_GET['eml'], true ) );

# No data to work with, go to Users > All Users instead
if( !$cid && !$eml ) wp_redirect( site_url('/wp-admin/users.php') );

# Get current tab suffix
$tab = ( !empty( $_GET['page'] ) ) ? substr( $_GET['page'], -2 ): 't1';

# if download button was hit open the CSV Exporter first
if ( isset( $_GET['action'] ) and $_GET['action'] == 'download' ) {
	include namespace\DIR . 'includes/admin/order-history-csv.php';
}

# Build tabs
osh()->create_page_tabs( $tab, $cid, $eml );

switch( $tab ) {

    case 't1':
    default:
        include( namespace\DIR . 'includes/admin/order-history-customer.php' );
        break;

    case 't2':
        include( namespace\DIR . 'includes/admin/order-history-product.php' );
        break;

    case 't3':
        include( namespace\DIR . 'includes/admin/order-history-notes.php' );
        break;

}

# (Code outside the tabs goes here)

?>
