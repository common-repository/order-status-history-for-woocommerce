<?php
namespace oshwoo;

#
# check access
#
# this page opens into a tab
# no direct page access
defined( 'ABSPATH' ) || exit;

# $cid, $eml populated from order-history.php

# if no orders were found there's nothing else to do
if( empty( $orders = osh()->get_orders( $cid, $eml ) ) ):
    wp_die( __('No Orders found', 'order-status-history-for-woocommerce') );
endif;

# Accepted types
$types = array('orders', 'products', 'notes');

# get type of csv we have to generate
$type = isset( $_GET['type'] ) ? strtolower( $_GET['type'] ) : '';

# if no valid type was found there's nothing else to do
if( !$type || !in_array( $type, $types ) ):
    wp_die( __('Invalid type', 'order-status-history-for-woocommerce') );
endif;

# csv filename format for saving
if( $cid ) $filename = $type . '__cid=' . $cid . '.csv';
if( $eml ) $filename = $type . '__' . sanitize_file_name($eml) . '.csv';

# CSV Export begin -->
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"' );

ob_end_clean();
$fp = fopen('php://output', 'w');

switch( $type ) {

case 'products':

    # header row
    $fh = array('Order ID', 'Customer', 'Billing email', 'Status', 'Order Date', 'SKU', 'Image URL', 'Product Title', 'Qty', 'Unit Price', 'Qty*Price' );

    # create a comma-separated csv
    fputcsv( $fp, $fh, "," ); 

    foreach( $orders as $order => $order_data ) { #1
        $status = $order_data->get_status();
           
        switch( $status ) {
            case ST_COMPLETED:
                $order_date = $order_data->get_date_completed();
                break;
            case ST_REFUNDED:
                $order_date = $order_data->get_date_paid();
                break;
            case ST_PENDING:
            case ST_PROCESSING:
            case ST_ONHOLD:
            case ST_CANCELLED:
            case ST_FAILED:
            default:
                $order_date = $order_data->get_date_created();
        }

        $items = $order_data->get_items();

        # edge: data row with no items, for manual orders  
        if( !$items ) {
            fputcsv( $fp, 
                array(
                # Order ID
                $order_data->get_order_number(),
                # Customer
                $order_data->get_formatted_billing_full_name(),
                # Billing email
                $order_data->get_billing_email(),
                # Status
                $status,
                # Order Date
                date("D,M-j,y -g:ia", strtotime( $order_date )),
                # SKU
                '',
                # Image URL
                '',
                # Product Title
                '',
                # Qty
                '',
                # Unit Price
                '',
                # Qty*Price
                '',
                ) 
            );
        }
        # data row with items
        foreach ($items as $item) { #2
            # get SKU
            # No HPOS-compatibility required, as sku is fetched from products, not orders
            $sku = $item['variation_id'] ? get_post_meta( $item['variation_id'], '_sku', true ) 
                                         : get_post_meta( $item['product_id']  , '_sku', true );

            $img = get_the_post_thumbnail_url( $item['product_id'] );

            fputcsv( $fp, 
                array(
                # Order ID
                $order_data->get_order_number(),
                # Customer
                $order_data->get_formatted_billing_full_name(),
                # Billing email
                $order_data->get_billing_email(),
                # Status
                $status,
                # Order Date
                date("D,M-j,y -g:ia", strtotime( $order_date )),
                # SKU
                $sku,
                # Image URL
                $img,
                # Product Title
                $item['name'],
                # Qty
                $item['quantity'],
                # Unit Price
                ($item['total'] && $item['quantity']) ? bcdiv( $item['total'], $item['quantity'], 2 ) : 0,
                # Qty*Price
                $item['total'],
                ) 
            );
        } #2
    } #1
    
    break;

case 'notes':
   
    # header row
    $fh = array('Order ID', 'Customer', 'Billing email', 'Status', 'Order Date', 'Note', 'Note Date', 'Note Type', 'Shipping costs', 'Total');

    # create a comma-separated csv
    fputcsv( $fp, $fh, "," ); 

    foreach( $orders as $order => $order_data ) { #1

        $status = $order_data->get_status();

        switch( $status ) {
            case ST_COMPLETED:
                $order_date = $order_data->get_date_completed();
                break;
            case ST_REFUNDED:
                $order_date = $order_data->get_date_paid();
                break;
            case ST_PENDING:
            case ST_PROCESSING:
            case ST_ONHOLD:
            case ST_CANCELLED:
            case ST_FAILED:
            default:
                $order_date = $order_data->get_date_created();
        }

        # get all notes for a given order (private, and note to customer)
        # https://pastebin.com/X64neRP2
        $notes = wc_get_order_notes([ 'order_id' => $order_data->get_id(), 
                                      'order_by' => 'date_created'
                                    ]);
        //error_log( var_export( count( $notes ), true ) ); 
         
        $notes_count  = count( $notes );
        # loop at least once through an order, to output note from customer and orders w/o any notes
        $i = -1;
        
        while( $i < $notes_count ) { #2
            switch( $i ) {
                case -1:
                    # start first by singling out any note from customer, as it isn't part of the notes array
                    if( $note_content = $order_data->get_customer_note() ) {
                        $note_date    = $order_date;
                        $note_type    = 'from_customer';
                    # ensures an order w/o notes to also get into the CSV
                    } else {
                        $note_content = $note_date = $note_type = '';
                    }
                    break;
                default:
                    # skip most system notes
                    if( $notes[$i]->added_by == 'system' ) {
                        $i++;
                        continue 2; # while
                    # to_customer or private note
                    } else {
                        $note_content = $notes[$i]->content;
                        $note_date    = $notes[$i]->date_created;
                        $note_type    = $notes[$i]->customer_note ? 'to_customer' : 'private';
                    }
            }
            # output order note
            fputcsv( $fp, 
                array(
                # Order ID
                $order_data->get_order_number(),
                # Customer
                $order_data->get_formatted_billing_full_name(),
                # Billing email
                $order_data->get_billing_email(),
                # Status
                $status,
                # Order Date
                date("D,M-j,y -g:ia", strtotime( $order_date )),
                # Note
                $note_content,
                # Note Date
                $note_date ? date("D,M-j,y -g:ia", strtotime( $note_date )) : '',
                # Note Type
                $note_type,
                # Shipping totals
                $order_data->get_total_shipping(),
                # Total
                $order_data->get_total(),
                ) 
            );
            # next note row
            $i++;
        } #2
    } #1
    
    break;

case 'orders':
default:

    # header row
    $fh = array('Order ID', 'Guest', 'Customer', 'Billing email', 'Status', 'Order Date', 'Order Products', 'Payment Method', 'Billing', 'Phone', 'Shipping costs', 'Total');

    # create a comma-separated csv
    fputcsv( $fp, $fh, "," ); 

    foreach( $orders as $order => $order_data ) { #1
        $items = $order_data->get_items();
        $product_list_text = '';
        
        # data row
        foreach( $items as $item ) { #2
            # get SKU
            # No HPOS-compatibility required, as sku is fetched from products, not orders
            $sku = $item['variation_id'] ? get_post_meta( $item['variation_id'], '_sku', true ) 
                                         : get_post_meta( $item['product_id']  , '_sku', true );

            $img = get_the_post_thumbnail_url( $item['product_id'] );
            $product_list_text  .= '['.$sku.'][' .$img.'] ' . $item['name'] . ' | ';
        } #2
        # strip last separator
        if( is_array( $items ) ) $product_list_text = mb_substr( $product_list_text, 0, -3 );

        $status = $order_data->get_status();
           
        switch( $status ) {
            case ST_COMPLETED:
                $order_date = $order_data->get_date_completed();
                break;
            case ST_REFUNDED:
                $order_date = $order_data->get_date_paid();
                break;
            case ST_PENDING:
            case ST_PROCESSING:
            case ST_ONHOLD:
            case ST_CANCELLED:
            case ST_FAILED:
            default:
                $order_date = $order_data->get_date_created();
        }
        
        fputcsv( $fp, 
            array(
            # Order ID
            $order_data->get_order_number(),
            # Guest
            $order_data->get_customer_id() ? 'N' : 'Y',
            # Customer
            $order_data->get_formatted_billing_full_name(),
            # Billing email
            $order_data->get_billing_email(),
            # Status
            $status,
            # Order Date
            date("D,M-j,y -g:ia", strtotime( $order_date )),
            # Order Products
            $product_list_text,
            # Payment Method
            $order_data->get_payment_method_title(),
            # Billing
            $order_data->get_billing_city(). ' - ' . $order_data->get_billing_postcode() . ', ' . $order_data->get_billing_country(),
            # Phone
            $order_data->get_billing_phone(),
            # Shipping totals
            $order_data->get_total_shipping(),
            # Total
            $order_data->get_total(),
            ) 
        );
    } #1

} #switch

fclose( $fp );
exit;

# <-- CSV Export end
