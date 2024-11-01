<?php if ( ! defined( 'ABSPATH' ) ) exit; 
/*
	Plugin Name: Split Order by Warehouse
	Plugin URI: 
	Description: This plugin split order multiple Warehouse orders.
	Version: 1.0
	Author: SunArc
	Author URI: https://sunarctechnologies.com/
	Text Domain: woocommerce-split-order-warehouse
	License: GPL2

*/

global $wpdb;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} else {

    clearstatcache();
}


require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
define('wsowsunarc_sunarc_plugin_dir', dirname(__FILE__));


register_activation_hook(__FILE__, 'wsowsunarc_plugin_activate');

function wsowsunarc_plugin_activate() {
    $option_name = 'split_by_warehouse_falg';
    $new_value = 'no';
    update_option($option_name, $new_value);
	
}

// Deactivation Pluign 
function wsowsunarc_deactivation() {
     $option_name = 'split_by_warehouse_falg';
    $new_value = '';
    update_option($option_name, $new_value);
	$option_name1 = 'splitorderwarehouse';
    $new_value1 = '';
    update_option($option_name1, $new_value1);
}

register_deactivation_hook(__FILE__, 'wsowsunarc_deactivation');

// Uninstall Pluign 
function wsowsunarc_uninstall() {
    $option_name = 'split_by_warehouse_falg';
    $new_value = '';
    update_option($option_name, $new_value);
	$option_name1 = 'splitorderwarehouse';
    $new_value1 = '';
    update_option($option_name1, $new_value1);
    
}


$SUNARC_all_plugins = get_plugins();

$SUNARC_activate_all_plugins = apply_filters('active_plugins', get_option('active_plugins'));

if (array_key_exists('woocommerce/woocommerce.php', $SUNARC_all_plugins) && in_array('woocommerce/woocommerce.php', $SUNARC_activate_all_plugins)) {
     $optionVal = get_option('split_by_warehouse_falg');
     $splitDefault = get_option('splitorderwarehouse');
      if($optionVal == 'yes' && $splitDefault == 'default')	{
		require_once wsowsunarc_sunarc_plugin_dir . '/include/defaultsplitorderwarehouse.php';
	}		
}


function wsowsunarc_register_my_custom_submenu_page() {
    add_submenu_page( 'woocommerce', 'Split By Warehouse', 'Split By Warehouse', 'manage_options', 'split-order-by-warehouse', 'wsowsunarc_my_custom_submenu_page_callback' ); 
}
function wsowsunarc_my_custom_submenu_page_callback() {
  
	 require_once wsowsunarc_sunarc_plugin_dir . '/include/setting.php';
}
add_action('admin_menu', 'wsowsunarc_register_my_custom_submenu_page',99);


add_action( 'woocommerce_email', 'wsowsunarc_remove_hooks' );

function wsowsunarc_remove_hooks( $email_class ) {
		remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
		remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
		remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
		
		// New order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		
		// Processing order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		
		// Completed order emails
		remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
			
		// Note emails
		remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
}


add_action( 'woocommerce_order_item_meta_end', 'wsowsunarc_display_custom_data_in_emails', 10, 4 );
function wsowsunarc_display_custom_data_in_emails( $item_id, $item, $order, $bool ) {
	     $optionVal = get_option('split_by_warehouse_falg');
     $splitDefault = get_option('splitorderwarehouse');
     if ($optionVal == 'yes' && $splitDefault == 'splitaccordingwarehouse') {
    $terms = wp_get_post_terms( $item->get_product_id(), 'warehouse', array( 'fields' => 'names' ) ); 
  if(!empty($terms)){
        echo "<br><small>" .'Warehouse Name : '. implode(', ', $terms) . "</small>";
		}
		else
		{
			 echo "<br><small>".'Warehouse Name : No Warehouse Selected '."</small>";
		}
	 }
}


add_action( 'woocommerce_after_order_itemmeta', 'wsowsunarc_custom_admin_order_itemmeta', 15, 3 );
function wsowsunarc_custom_admin_order_itemmeta( $item_id, $item, $product ){
	     $optionVal = get_option('split_by_warehouse_falg');
     $splitDefault = get_option('splitorderwarehouse');
     if ($optionVal == 'yes' && $splitDefault == 'splitaccordingwarehouse') {
    if( $item->is_type( 'line_item' ) ){
        $terms = wp_get_post_terms( $item->get_product_id(), 'warehouse', array( 'fields' => 'names' ) );
		if(!empty($terms)){
        echo "<br><small>" .'Warehouse Name : '. implode(', ', $terms) . "</small>";
		}
		else
		{
			 echo "<br><small>".'Warehouse Name : No Warehouse Selected '."</small>";
		}
    }
	 }
}


	

add_action('woocommerce_checkout_create_order', 'wsowsunarc_before_checkout_create_order', 20, 2);
function wsowsunarc_before_checkout_create_order( $order, $data ) {
 $order->update_meta_data( '_custom_meta_hide', 'yes' );

}

function action_woocommerce_checkout_order_processed( $order_id, $posted_data, $order ) {
 $optionVal = get_option('split_by_warehouse_falg');
	 $splitDefault = get_option('splitorderwarehouse');
	if($optionVal=='yes'){	
   update_post_meta($order_id,'_order_total',0);  
	}
}; 
add_action( 'woocommerce_checkout_order_processed', 'action_woocommerce_checkout_order_processed', 10, 3 ); 


add_filter( 'woocommerce_order_number', 'change_woocommerce_order_number' );
function change_woocommerce_order_number( $order_id ) {
	
	$optionVal = get_option('split_by_warehouse_falg');
    $splitDefault = get_option('splitorderwarehouse');
	if($optionVal=='yes'){
	$pricetotal = get_post_meta($order_id,'_order_total',true);
	if($pricetotal==0){
    $suffix = '--Main Order--';
    $new_order_id = $order_id . $suffix;
    return $new_order_id;
	}
	else 
	{
		$suffix = '--Split Order--';
    $new_order_id = $order_id . $suffix;
    return $new_order_id;
	}
	}
	
 }

add_filter( 'woocommerce_endpoint_order-received_title', 'sunarc_thank_you_title' );
 
function sunarc_thank_you_title( $old_title ){
	   $optionVal = get_option('split_by_warehouse_falg');
     $splitDefault = get_option('splitorderwarehouse');
	 if ($optionVal == 'yes' && $splitDefault == 'splitaccordingwarehouse') {
  $order_id = wc_get_order_id_by_order_key( $_GET['key'] ); 
  update_post_meta($order_id,'_order_total',0);  
 	?>
	<script>
	jQuery(document).ready(function () {
    jQuery('.woocommerce-order-details__title').text('Main Order details');
});
</script>
	<?php
	 }
}



// Register Custom Taxonomy
function wsowsunarc_warehouse_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Warehouses', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Warehouse', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Warehouses', 'text_domain' ),
		'all_items'                  => __( 'All Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Item Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
		'update_item'                => __( 'Update Item', 'text_domain' ),
		'view_item'                  => __( 'View Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => false,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'warehouse', array( 'product' ), $args );

}
add_action( 'init', 'wsowsunarc_warehouse_taxonomy', 0 );
