<?php
namespace oshwoo;

#
# check access
#
# this page opens into a tab
# no direct page access
defined( 'ABSPATH' ) || exit;

# check if wp_options settings can be exported to CVS
if( isset( $_GET['action'] ) && strtolower( $_GET['action'] == 'export') ) {

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="oshwoo_settings.csv"' );

    ob_end_clean();
    $fp = fopen('php://output', 'w');
    $fh = $fv = array();

    foreach ( wp_load_alloptions() as $option => $value ) {
        if ( strpos( $option, 'oshwoo_' ) === 0 ) {
            $fh[] = $option;
            $fv[] = $value;
        }
    }

    # create a comma-separated csv
    fputcsv( $fp, $fh, "," ); 
    fputcsv( $fp, $fv );
    fclose(  $fp );
    exit; 

}
