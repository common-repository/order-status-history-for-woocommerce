/**
 *
 * main JS
 *
 */

// global namespace
// Actually declared first by WP in php2js
var oshwoo = window.oshwoo || {};

jQuery(document).ready(function($) {

//--> ns oshwoo
(function () { 
'use strict';

var o = this; // byref

/**
 * initialise previously saved open/close toggles and their order
 * based on: https://www.themoyles.co.uk/2013/03/using-meta-boxes-on-plugin-admin-pages/
 */
$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
// global postboxes
postboxes.add_postbox_toggles(pagenow);

// activate WC tooltip support
$('.woocommerce-help-tip').tipTip({
	'attribute': 'data-tip',
	'fadeIn':    50,
	'fadeOut':   50,
	'delay':     200
});

}).apply( oshwoo );
//<-- /ns

}); //ready
