<?php
/**
 *
 *  PHP to JS Connector: export php variables and localization strings into js
 *  
 */
namespace oshwoo;

function php_2js() {
 
    /**
     *  export php-vars into js-vars
     */
    $vars = array(

        # current theme
        'c_hx_pending'    => osh()->get_color('HX_PENDING'), 
        'c_hx_processing' => osh()->get_color('HX_PROCESSING'), 
        'c_hx_onhold'     => osh()->get_color('HX_ONHOLD'), 
        'c_hx_completed'  => osh()->get_color('HX_COMPLETED'), 
        'c_hx_cancelled'  => osh()->get_color('HX_CANCELLED'), 
        'c_hx_refunded'   => osh()->get_color('HX_REFUNDED'), 
        'c_hx_failed'     => osh()->get_color('HX_FAILED'), 
        'c_hx_other'      => osh()->get_color('HX_OTHER'), 
        'c_hx_aggregate'  => osh()->get_color('HX_AGGREGATE'), 
        'c_hx_guest'      => osh()->get_color('HX_GUEST'), 
        'c_hx_history'    => osh()->get_color('HX_HISTORY'), 
        'c_hx_text'       => osh()->get_color('HX_TEXT'),
        'c_wc_colors_update'     => get_option('oshwoo_wc_colors_update'),
        'c_multicurrency_symbol' => get_option('oshwoo_multicurrency_symbol', ''),
        # default theme
        'd_hx_pending'    => HX_PENDING, 
        'd_hx_processing' => HX_PROCESSING, 
        'd_hx_onhold'     => HX_ONHOLD, 
        'd_hx_completed'  => HX_COMPLETED, 
        'd_hx_cancelled'  => HX_CANCELLED, 
        'd_hx_refunded'   => HX_REFUNDED, 
        'd_hx_failed'     => HX_FAILED, 
        'd_hx_other'      => HX_OTHER, 
        'd_hx_aggregate'  => HX_AGGREGATE, 
        'd_hx_guest'      => HX_GUEST, 
        'd_hx_history'    => HX_HISTORY, 
        'd_hx_text'       => HX_TEXT,
    );

    /**
     *  export translation-ready php strings into js strings
     */
    $langs = array(

        'current'           => __('Current', 'order-status-history-for-woocommerce'),
        'sdefault'          => __('Default', 'order-status-history-for-woocommerce'),
        'dialog_title'      => __('CSV Importer', 'order-status-history-for-woocommerce'),
        'dialog_notice_ok'  => __('File parsed and values imported into the current theme. Save changes to finish.', 'order-status-history-for-woocommerce'),
        'dialog_notice_er'  => __('There was an error while parsing the CSV:', 'order-status-history-for-woocommerce'),
        'dialog_format_er'  => __('Incorrect format', 'order-status-history-for-woocommerce'),
        'colorInput_notice' => __("This page isn't operational, as your browser isn't compatible with the Color Picker.", 'order-status-history-for-woocommerce'),
    );

    /**
     *
     *  - handle 'oshwoo-js' should already belong to a different js file!
     *  - 'oshwoo' becomes a global js object 
     *  - needs to be called right after wp_enqueue_script
     *  https://wordpress.stackexchange.com/questions/290746/
     *
     */
    wp_localize_script( 'oshwoo-js', 'oshwoo', array( 'vars' => $vars, 'i18n' => $langs ) );
}
