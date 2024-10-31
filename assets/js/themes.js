/**
 *
 * Themes support
 *
 */

//--> ns oshwoo
(function () {
'use strict';

var o = this; // byref
o.idx = 0; 
o.hx_themes = {};

//
// To change order in the drop-down, just rearrange the code blocks 
//

//--> current theme (default/saved, populated from php)
o.hx_themes[o.idx++] = {
    name : '<' +o.i18n.current+ '>',
    color: {
        'hx_pending'    : o.vars.c_hx_pending, 
        'hx_processing' : o.vars.c_hx_processing, 
        'hx_onhold'     : o.vars.c_hx_onhold, 
        'hx_completed'  : o.vars.c_hx_completed, 
        'hx_cancelled'  : o.vars.c_hx_cancelled, 
        'hx_refunded'   : o.vars.c_hx_refunded, 
        'hx_failed'     : o.vars.c_hx_failed, 
        'hx_other'      : o.vars.c_hx_other,
        'hx_aggregate'  : o.vars.c_hx_aggregate, 
        'hx_guest'      : o.vars.c_hx_guest, 
        'hx_history'    : o.vars.c_hx_history, 
        'hx_text'       : o.vars.c_hx_text
    },
    checkbox: {
        'wc_colors_update'     : o.vars.c_wc_colors_update,
        'multicurrency_symbol' : o.vars.c_multicurrency_symbol
    }
}
//<-- current theme

//--> default theme (populated from php)
o.hx_themes[o.idx++] = {
    name : 'Woo (' +o.i18n.sdefault+ ')',
    color: {
        'hx_pending'    : o.vars.d_hx_pending, 
        'hx_processing' : o.vars.d_hx_processing, 
        'hx_onhold'     : o.vars.d_hx_onhold, 
        'hx_completed'  : o.vars.d_hx_completed, 
        'hx_cancelled'  : o.vars.d_hx_cancelled, 
        'hx_refunded'   : o.vars.d_hx_refunded, 
        'hx_failed'     : o.vars.d_hx_failed, 
        'hx_other'      : o.vars.d_hx_other, 
        'hx_aggregate'  : o.vars.d_hx_aggregate, 
        'hx_guest'      : o.vars.d_hx_guest, 
        'hx_history'    : o.vars.d_hx_history, 
        'hx_text'       : o.vars.d_hx_text
    },
    checkbox: {
        'wc_colors_update' : '1'
    }
}
//<-- default theme

//--> OrderSpace theme
o.hx_themes[o.idx++] = { 
    name : 'OrderSpace',
    color: {
        'hx_pending'    : '#F9AF70', 
        'hx_processing' : '#00F2D3', 
        'hx_onhold'     : '#FF90EA', 
        'hx_completed'  : '#C9FFBA', 
        'hx_cancelled'  : '#997E69', 
        'hx_refunded'   : '#FAB6D5', 
        'hx_failed'     : '#B3B3B3', 
        'hx_other'      : '#00F2D3', 
        'hx_aggregate'  : '#999999', 
        'hx_guest'      : '#999999', 
        'hx_history'    : '#999999', 
        'hx_text'       : '#FFFFFF'
    },
    checkbox: {
        'wc_colors_update' : '1'
    }
}
//<-- OrderSpace theme

//--> PrestaShop theme
o.hx_themes[o.idx++] = { 
    name : 'PrestaShop',
    color: {
        'hx_pending'    : '#3567DB', 
        'hx_processing' : '#FF8A00', 
        'hx_onhold'     : '#FF62B1', 
        'hx_completed'  : '#8A22E6', 
        'hx_cancelled'  : '#E50037', 
        'hx_refunded'   : '#FB1600', 
        'hx_failed'     : '#9A0019', 
        'hx_other'      : '#FF8A00', 
        'hx_aggregate'  : '#999999', 
        'hx_guest'      : '#999999', 
        'hx_history'    : '#999999', 
        'hx_text'       : '#FFFFFF'
    },
    checkbox: {
        'wc_colors_update' : '1'
    }
}
//<-- PrestaShop theme

//--> Shopify theme
o.hx_themes[o.idx++] = { 
    name : 'Shopify',
    color: {
        'hx_pending'    : '#00B1EE', 
        'hx_processing' : '#FCD703', 
        'hx_onhold'     : '#F9F202', 
        'hx_completed'  : '#00B453', 
        'hx_cancelled'  : '#D70000', 
        'hx_refunded'   : '#D70000', 
        'hx_failed'     : '#FF0000', 
        'hx_other'      : '#FCD703', 
        'hx_aggregate'  : '#999999', 
        'hx_guest'      : '#999999', 
        'hx_history'    : '#999999', 
        'hx_text'       : '#FFFFFF'
    },
    checkbox: {
        'wc_colors_update' : '1'
    }
}
//<-- Shopify theme

//--> OpenCart theme
o.hx_themes[o.idx++] = { 
    name : 'OpenCart',
    color: {
        'hx_pending'    : '#008767', 
        'hx_processing' : '#0000FD', 
        'hx_onhold'     : '#A200B3', 
        'hx_completed'  : '#00B4E5', 
        'hx_cancelled'  : '#FF0000', 
        'hx_refunded'   : '#FF0000', 
        'hx_failed'     : '#C2008D', 
        'hx_other'      : '#0000FD', 
        'hx_aggregate'  : '#999999', 
        'hx_guest'      : '#999999', 
        'hx_history'    : '#999999', 
        'hx_text'       : '#FFFFFF'
    },
    checkbox: {
        'wc_colors_update' : '1'
    }
}
//<-- OpenCart theme

//--> Apollo theme
o.hx_themes[o.idx++] = { 
    name : 'Apollo',
    color: {
        'hx_pending'    : '#853D7F', 
        'hx_processing' : '#9C6C77', 
        'hx_onhold'     : '#D62900', 
        'hx_completed'  : '#FF5F95', 
        'hx_cancelled'  : '#D360FD', 
        'hx_refunded'   : '#0099FE', 
        'hx_failed'     : '#D19833',
        'hx_other'      : '#9C6C77', 
        'hx_aggregate'  : '#999999', 
        'hx_guest'      : '#999999', 
        'hx_history'    : '#999999', 
        'hx_text'       : '#FFFFFF' 
    },
    checkbox: {
        'wc_colors_update' : '1'
    }
}
//<-- Apollo theme

//--> CS-Cart theme
o.hx_themes[o.idx++] = { 
    name : 'CS-Cart',
    color: {
        'hx_pending'    : '#E0614A', 
        'hx_processing' : '#FF8D02', 
        'hx_onhold'     : '#00A6F5', 
        'hx_completed'  : '#AADC7B', 
        'hx_cancelled'  : '#BFBFBF', 
        'hx_refunded'   : '#C1C1C1', 
        'hx_failed'     : '#FF5000', 
        'hx_other'      : '#FF8D02', 
        'hx_aggregate'  : '#999999', 
        'hx_guest'      : '#999999', 
        'hx_history'    : '#999999', 
        'hx_text'       : '#FFFFFF'
    },
    checkbox: {
        'wc_colors_update' : '1'
    }
}
//<-- CS-Cart theme

}).apply( oshwoo ); 
//<-- /ns
