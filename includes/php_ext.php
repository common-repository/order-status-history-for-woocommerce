<?php
/**
 *
 *  Extending PHP/WP core with some useful functions, independent of plugins
 *  Simply in namespace scope for brevity of usage
 *
 */
namespace oshwoo;

/**
 *
 * Fix for issue with variables that aren't global anymore, 
 * but in a nested scope because of the way the page was included
 *
 * https://heine.familiedeelstra.com/drupal-developer-faq/cannot-access-global-variable
 * https://stackoverflow.com/questions/5449526/cant-access-global-variable-inside-function
 *
 *
 * First, need to get all vars in the current (nested) scope via get_defined_vars, 
 * to make them accessible from the global scope again via GLOBALS.
 * The initializer must be called at least once in the page, to gather all nested variables, 
 * before being called inside functions that depend on globals
 */
#-->
function scope_vars( $get_defined_vars ) {
    # as an array value, to keep polluting the global space to a minimum
    $GLOBALS[NS] = $get_defined_vars; 
}
/**
 *  short-hand to retrieve nested-scope variables,
 *  to be called instead of 'global $var' or $GLOBALS['var'] inside a function
 */
function _global( $str_var ) {
    if( array_key_exists( $str_var, $GLOBALS[NS] ) ) 
                             return $GLOBALS[NS][$str_var];
    else error_log("ERR: scope_vars() not called first, or var not found: $str_var on ".$_SERVER['REQUEST_URI']);
}
#<--

/**
 *  Calculate odd column
 *  usually for displaying of alternate colors in HTML tables
 */
function oddrow( $css ) {
    static $odd;
    if ( $odd ) {
         echo $css;
         $odd = 0; 
         } 
    else $odd = 1;
}

/**
 *  Convert a multi-dimensional array into a single-dimensional array
 *  https://gist.github.com/SeanCannon/6585889
 */
function flatten_array( array $array ) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
}

/**
 *  ensure page is admin, but not the front-end loaded below the admin toolbar nav
 */
function is_backend_page() {
    return !empty( $GLOBALS['admin_page_hooks'] );
}

/**
 *  Helpers for handling the prefix inconsistencies of the Woo statuses across WP 
 *
**/

/**
 *  normalize status to wc-prefix
 */
#-->
function status_wc( $status ) {
    return ( stristr( $status, 'wc-' ) === FALSE ) ? 'wc-' . $status : $status;
}
/**
 *  normalize status to non-prefix
 */
function status_nwc( $status ) {
    return str_replace( 'wc-', '', $status );
}
#<--

/**
 *  Helpers for enhanced multicurrency support
 *
**/
#--> 
/**
 *  Register generic currency symbol
 */
function add_currency( $currencies ) {
     $currencies['AAA'] = __('Generic Currency', 'order-status-history-for-woocommerce');
     return $currencies;
}

function add_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'AAA': $currency_symbol = '&curren;'; break;
     }
     return $currency_symbol;
}
/**
 *  Generic currency sign display workaround for the WC widget
 */
function add_currency_symbol_widget( $currency_symbol ) {
    global $pagenow;
    # WP Admin Dashboard > WC Status Widget 
    if( is_backend_page() && $pagenow == 'index.php' ) $currency_symbol = '&curren;';
    return $currency_symbol;
}     
/**
 *  Check if all placed orders by a given Customer were all in the same currency
 *  if yes, return their currency, otherwise return a generic symbol
 */  
function check_currency( $orders_currencies ) {
     return ( sizeof(array_unique( $orders_currencies )) == 1 ) 
             ? array('currency' => $orders_currencies[0]) 
             : array('currency' => 'AAA');
}
#<--
/**
 *  Helpers for missing core functionality
 *
**/
#--> 
/**
 *  bcdiv alternative for lack of BCMath extension in PHP7.0
 */  
function bcdiv( $num_str1, $num_str2, $scaleVal ) {
    if( function_exists('\bcdiv') ) {
        return \bcdiv( $num_str1,  $num_str2, $scaleVal );
    } else {
        return  round( $num_str1 / $num_str2, $scaleVal );
    }
}
#<--
