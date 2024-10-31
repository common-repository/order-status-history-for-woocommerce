<?php

/***********************************************
 *
 *  Plugin constants and (namespace) globals
 *  
 ***********************************************/

namespace oshwoo;

/**
 *
 *  NS-scope Constants (strings)
 *   
**/
# plugin namespace shorthand
const NS = __NAMESPACE__;
#
# own statuses for guest orders, and totals
const ST_GUEST      = 'guest';
const ST_AGGREGATE  = 'aggregate';
#
# WooCommerce Core Statuses
const ST_PENDING    = 'pending';
const ST_PROCESSING = 'processing';
const ST_ONHOLD     = 'on-hold';    
const ST_COMPLETED  = 'completed';
const ST_REFUNDED   = 'refunded';
const ST_CANCELLED  = 'cancelled';
const ST_FAILED     = 'failed';
# Generic status to handle non-core statuses
const ST_OTHER      = 'other';
#
# Default WC Status Colors 
# (must exist paired with WC Core statuses above)
const HX_PENDING    = '#91D2FF';
const HX_PROCESSING = '#B8DCBC';
const HX_ONHOLD     = '#FAD58F';
const HX_COMPLETED  = '#9FBECE';
const HX_CANCELLED  = '#BD7864';
const HX_REFUNDED   = '#BB6A7E';
const HX_FAILED     = '#F8898C';
# Generic status
const HX_OTHER      = '#B8DCBC';
#
# Additional status colors
const HX_AGGREGATE  = '#999999'; 
const HX_GUEST      = '#999999';
# text status
const HX_TEXT       = '#FFFFFF';
# History icon
const HX_HISTORY    = '#999999';

/**
 *
 *  NS-scope Constants (functions)
 *   
**/
# namespace\FILE defined in main plugin file
define( NS.'\\PLUGIN_FILE', plugin_basename( namespace\FILE ) );             # path to plugin file relative to the plugins directory    
define( NS.'\\VERSION',     get_plugin_data( namespace\FILE )['Version'] );  # Current plugin version
define( NS.'\\NAME',        get_plugin_data( namespace\FILE )['Name'] );     # Plugin name as defined in the main plugin file
define( NS.'\\DIR',         plugin_dir_path( namespace\FILE ) );             # Root plugin path (get up dir ref as this file is in a subdir)
define( NS.'\\URL',         plugin_dir_url ( namespace\FILE ) );             # Root plugin URL

//error_log( var_export(namespace\FILE, true ) );

/**
 *
 *  global container for temporary storage of variables defined in a nested scope  
 *   
**/
$GLOBALS[NS] = array(); 
