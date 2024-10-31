/**
 *
 * Settings JS
 *
 */

jQuery(document).ready(function($) {

//--> ns oshwoo
(function () { 
'use strict';

var o = this; // byref

// change new text color to all swatches
$('#oshwoo_hx_text').on( 'change', function() {
	$('label.alfnum').css( 'color', $(this).val() ); 
})

// show hex values in swatch titles
$('input[type=color]').prop( 'title', function() {
	return 'hex color: ' + $(this).val().toUpperCase();
});

/**
 *  Theme Chooser
 *
 *///-->
// populate theme selector
for( var key in o.hx_themes ) {
	$('#color-themes').append( $('<option>').val( key ).text( o.hx_themes[key]['name'] ));
}
// bind theme selector action to dropdown
$('#color-themes').on( 'change', function() {
	var idx = $(this).find('option:selected').val();
	o.theme_chooser(idx);
})
// change theme
o.theme_chooser = function( idx=0 ) {
	// apply theme to swatches
	if( !$.isEmptyObject( o.hx_themes[idx]['color'] ) )
	for( var key  in o.hx_themes[idx]['color'] ) {
		 var value = o.hx_themes[idx]['color'][key];
		$('#oshwoo_' + key).val(value);
	}
	// apply theme to checkboxes
	if( !$.isEmptyObject( o.hx_themes[idx]['checkbox'] ) )
	for( var key  in o.hx_themes[idx]['checkbox'] ) {
		 var value = o.hx_themes[idx]['checkbox'][key];
		$('#oshwoo_' + key).prop('checked', value);
	}
	// apply theme foreground text to swatches
	$('label.alfnum').css( 'color', $('#oshwoo_hx_text').val() ); 
}
//<-- Theme Chooser

/**
 *  CSV Importer
 *
 *///-->
$('#oshwoo_import_settings').on('change', function () {
	var file = $(this)[0].files[0];
	var reader = new FileReader();
	reader.onload = function(e) {
		var text  = reader.result;
		var notice;
		try {
			var arr = $.csv.toObjects(text)[0]; //jquery.csv
			$.each( arr, function(key, val) {
				// color input: hex
				if( /^#[0-9A-F]{6}$/i.test(val) ) $('#'+key).val(val);
				// checkbox: unchecked=0/checked=1
				else if( Number.isInteger( Number(val) ) ) $('#'+key).prop('checked', Number(val));
				// wrong format
				else throw o.i18n.dialog_format_er;
			});
			notice = o.i18n.dialog_notice_ok;
		} catch(e) {
			notice = o.i18n.dialog_notice_er+'&nbsp;<i>'+e+'</i>';
		}
		$('<div><p>'+notice+'</p></div>')
		.dialog({
				title  : o.i18n.dialog_title,
				buttons: { 'OK': function(){ $(this).dialog('close') } } 
				});
	}
	reader.readAsText(file);
});
//<-- CSV Importer

/**
 *  browser capabilities check for color input
 *  https://stackoverflow.com/questions/13789163
 *
 *///-->  
o.isColorInput = function() {
	var colorInput = jQuery('<input type="color" value="!" />')[0];
	return colorInput.type === 'color' && colorInput.value !== '!';
}
if( !o.isColorInput() ) {
	$('#post-body-content').append('<div class=error><p>' +o.i18n.colorInput_notice+ '</p></div>');
	$('.oshwoo-settings form').find('input, textarea, button, select').attr('disabled','disabled');
}
//<-- browser capabilities


}).apply( oshwoo );
//<-- /ns

}); //ready
