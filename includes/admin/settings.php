<?php namespace oshwoo; ?>
<?php # no direct page access ?>
<?php defined( 'ABSPATH' ) || exit; ?> 
<?php
# if export/import button was hit open the CSV page first
if( isset( $_GET['action'] ) ) include namespace\DIR . 'includes/admin/settings-csv.php';
?>
<div class="wrap">
<h1>
<span class="dashicons dashicons-image-filter" style="padding-top: 7px;"></span>
<?php esc_html_e('Status History Settings', 'order-status-history-for-woocommerce') ?>
</h1>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2 oshwoo-settings">
<form method="post" action="options.php">
<?php    settings_fields  ('oshwoo-settings') ?>
<?php do_settings_sections('oshwoo-settings') ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
<?php wp_nonce_field( 'meta-box-order',  'meta-box-order-nonce', false ) ?>

<!-- main content -->
<div id="post-body-content">
<?php settings_errors() ?>
<div class="meta-box-sortables ui-sortable">
<div class="postbox">
<div class="inside">
<table class="form-table">
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="color-themes">
            <?php esc_html_e('Choose Color Theme', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <select id="color-themes" name="color-themes"></select>
    </td>
</tr>
<tr>
    <td colspan="2" class="col2"><b><?php esc_html_e('WooCommerce Statuses', 'order-status-history-for-woocommerce') ?></b><hr /></td>
</tr>
<!------------------------------- v Pending ---------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_pending">
            <?php esc_html_e('Orders Pending', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_pending" name="oshwoo_hx_pending" value="<?php echo osh()->get_color('HX_PENDING') ?>" />
    <label for="oshwoo_hx_pending" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">1</label>
    </td>
</tr>
<!------------------------------- v Processing ------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_processing">
            <?php esc_html_e('Orders Processing', 'order-status-history-for-woocommerce') ?>    
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_processing" name="oshwoo_hx_processing" value="<?php echo osh()->get_color('HX_PROCESSING') ?>" />
    <label for="oshwoo_hx_processing" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">2</label>
    </td>
</tr>
<!------------------------------- v On-Hold ---------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_onhold">
            <?php esc_html_e('Orders On Hold', 'order-status-history-for-woocommerce') ?>    
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_onhold" name="oshwoo_hx_onhold" value="<?php echo osh()->get_color('HX_ONHOLD') ?>" />
    <label for="oshwoo_hx_onhold" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">3</label>
    </td>
</tr>
<!------------------------------- v Completed -------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_completed">
            <?php esc_html_e('Orders Completed', 'order-status-history-for-woocommerce') ?> 
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_completed" name="oshwoo_hx_completed" value="<?php echo osh()->get_color('HX_COMPLETED') ?>" />
    <label for="oshwoo_hx_completed" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">4</label>
    </td>
</tr>
<!------------------------------- v Refunded --------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_refunded">
            <?php esc_html_e('Orders Refunded', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_refunded" name="oshwoo_hx_refunded" value="<?php echo osh()->get_color('HX_REFUNDED') ?>" />
    <label for="oshwoo_hx_refunded" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">5</label>
    </td>
</tr>
<!------------------------------- v Cancelled -------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_cancelled">
            <?php esc_html_e('Orders Cancelled', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_cancelled" name="oshwoo_hx_cancelled" value="<?php echo osh()->get_color('HX_CANCELLED') ?>" />
    <label for="oshwoo_hx_cancelled" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">6</label>
    </td>
</tr>
<!------------------------------- v Failed ----------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_failed">
            <?php esc_html_e('Orders Failed', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_failed" name="oshwoo_hx_failed" value="<?php echo osh()->get_color('HX_FAILED') ?>" />
    <label for="oshwoo_hx_failed" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">7</label>
    </td>
</tr>
<!------------------------------- v Other ----------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_other">
            <?php esc_html_e('Orders in other status', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_other" name="oshwoo_hx_other" value="<?php echo osh()->get_color('HX_OTHER') ?>" />
    <label for="oshwoo_hx_other" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">8</label>
    </td>
</tr>
<!------------------------------- v WC defaults ------------------------------>
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_wc_colors_update">
            <?php esc_html_e('Also modify WC defaults', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="checkbox" id="oshwoo_wc_colors_update" name="oshwoo_wc_colors_update" value="1" 
    <?php echo get_option('oshwoo_wc_colors_update') ? 'checked' : '' ?> 
    title="<?php esc_attr_e('Match the default status colors of WooCommerce to this plugin', 'order-status-history-for-woocommerce') ?>">
    </td>
</tr>
<!------------------------------- ^ WC defaults ------------------------------>
<tr>
    <td colspan="2" class="col2"><b><?php esc_html_e('Additional Statuses', 'order-status-history-for-woocommerce') ?></b><hr /></td>
</tr>
<!------------------------------- v Aggregate -------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_aggregate">
            <?php esc_html_e('Total Orders', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_aggregate" name="oshwoo_hx_aggregate" value="<?php echo osh()->get_color('HX_AGGREGATE') ?>" />
    <label for="oshwoo_hx_aggregate" class="alfnum" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">9</label>
    </td>
</tr>
<!------------------------------- v Guest ------------------------------------>
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_guest">
            <?php esc_html_e('Guest Indicator', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_guest" name="oshwoo_hx_guest" value="<?php echo osh()->get_color('HX_GUEST') ?>" />
    <label for="oshwoo_hx_guest" class="alfnum glyph" style="color: <?php echo osh()->get_color('HX_TEXT') ?>">G</label>
    </td>
</tr>
<!------------------------------- ^ Guest ------------------------------------>
<!------------------------------- v History ---------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_history">
            <?php esc_html_e('History Reports Button', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_history" name="oshwoo_hx_history" value="<?php echo osh()->get_color('HX_HISTORY') ?>" />
    <label for="oshwoo_hx_history" class="alfnum history" style="color: <?php echo osh()->get_color('HX_TEXT') ?>"></label>
    </td>
</tr>
<!------------------------------- ^ History ---------------------------------->
<tr>
    <td colspan="2" class="col2"><b><?php esc_html_e('Other settings', 'order-status-history-for-woocommerce') ?></b><hr /></td>
</tr>
<!------------------------------- v Text ------------------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_hx_text">
            <?php esc_html_e('Color for all Status Symbols', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="color" id="oshwoo_hx_text" name="oshwoo_hx_text" value="<?php echo osh()->get_color('HX_TEXT') ?>" />
    <label for="oshwoo_hx_text" class="text alfnum glyph">&#10063;</label>
    </td>
</tr>
<!------------------------------- ^ Text ------------------------------------->
<!------------------------------- v Multicurrency ---------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td scope="row">
        <label for="oshwoo_multicurrency_symbol">
            <?php esc_html_e('Enhanced multi-currency support', 'order-status-history-for-woocommerce') ?>
        </label>
    </td>
    <td>
    <input type="checkbox" id="oshwoo_multicurrency_symbol" name="oshwoo_multicurrency_symbol" value="1" 
    <?php echo get_option('oshwoo_multicurrency_symbol', '') ? 'checked' : '' ?> 
    title="<?php esc_attr_e("Enable contextual use of the '&curren;' sign in multi-currency shops", 'order-status-history-for-woocommerce') ?>">
    </td>
</tr>
<!------------------------------- ^ Multicurrency ---------------------------->
<tr valign="top" class="<?php oddrow('alternate') ?>">
    <td colspan="2" align="center"> 
        <input type="submit" name="btn_submit" id="btn_submit" class="button button-primary" value="Save Changes">
    </td>
</tr>
</table>

</div><!-- /inside -->
</div><!-- /postbox -->
</div><!-- /meta-box-sortables -->
</div><!-- /post-body-content -->

<!-- sidebar -->
<div id="postbox-container-1" class="postbox-container">
<!------------------------------- v highlights ------------------------------->

<?php
add_meta_box('oshwoo_highlights-settings', __('Highlights', 'order-status-history-for-woocommerce'), function() {

    echo '&bull; ';
    esc_html_e('Hit the swatches to customize the colors used by the plugin. '
              .'Optionally, the default WC status colors can also be modified.', 'order-status-history-for-woocommerce');
    echo '<br>&bull; '; 
    esc_html_e('The "Total Orders" status counts how many Orders a given Customer (registered or guest) has placed in your shop. ' 
              .'It also recognizes those Buyers that had signed-up, but have not signed-in at the time of purchase.', 'order-status-history-for-woocommerce') ;
    echo '<br>&bull; '; 
    esc_html_e('"Orders in other status" consolidates all custom non-core WC statuses configured by other plugins.', 'order-status-history-for-woocommerce') ;
    echo '<br>&bull; ';
    esc_html_e('The "Guest" indicator is shown for Orders placed by non-registered (or non signed-in) Buyers.', 'order-status-history-for-woocommerce');
    echo '<br>&bull; ';
    esc_html_e('"History Reports" gives access to past Orders and Products purchased. ' 
              .'It usually appears for Orders from Customers shopped more than once, ' 
              .'and also for Buyers from the WP Users list.', 'order-status-history-for-woocommerce');

});
?>
<!------------------------------- ^ highlights -------------------------------->
<!-------------------------------- Donate ------------------------------------->
<?php if( osh()->show_donation_box ) include( namespace\DIR . 'includes/parts/donation_box.php' ) ?>
<!------------------------------- /Donate ------------------------------------->
<!------------------------------- v Export/Import ----------------------------->
<?php
add_meta_box('oshwoo_actions-settings', __('Actions', 'order-status-history-for-woocommerce'), function() {
    echo osh()->settings_button_render( 'import', __('Import', 'order-status-history-for-woocommerce') );
    echo osh()->settings_button_render( 'export', __('Export', 'order-status-history-for-woocommerce') );
});
?>
<!------------------------------- ^ Export/Import ----------------------------->

<?php do_meta_boxes( get_current_screen()->id, 'advanced', null ) ?>

</div><!-- /postbox-container-1 .postbox-container -->

</form>
</div><!-- /post-body .metabox-holder .columns-2 -->
<br class="clear">
</div><!-- /poststuff -->
</div> <!-- /wrap -->

     