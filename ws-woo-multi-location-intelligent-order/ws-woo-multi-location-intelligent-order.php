<?php
/**
* Plugin Name: WS Multi-Location Intelligent Order for Woocommerce
* Plugin URI: https://websitesection.com/plugins
* Description: Display the right shipping zone on admin's order panel. 
* Version: 1.0
* Author: Konstantinos Alexiadis
* Author URI: https://websitesection.com
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
**/

if ( ! defined( 'ABSPATH' ) ) {
    die;
} 

add_filter('manage_edit-shop_order_columns', 'wslt_add_order_items_column' );
function wslt_add_order_items_column( $order_columns ) {
    $order_columns['shipping_zone'] = "Shipping Zone";
    return $order_columns;
}

add_action( 'manage_shop_order_posts_custom_column' , 'wslt_manage_order_items_column_cnt' );
function wslt_manage_order_items_column_cnt( $colname ) {
 	global $the_order; // the global order object
	global $woocommerce, $post;

	$order = new WC_Order($post->ID);// new order for that post
	
 	if( $colname === 'shipping_zone' ) {
 
		$meta = get_post_meta( $order->get_id(), '_shipping_first_name' );
		$myvals = get_post_meta( get_the_ID());
			foreach($myvals as $key=>$val){
  				foreach($val as $vals){
    				if ($key=='_shipping_postcode'){
       					$meta =$vals;
    				}
   				}
			 }
		
		if ( !is_wp_error( $order_items ) ) {
			
 			echo wslt_getZoneByPostalCode($meta);
		}
	}
}

function wslt_getZoneByPostalCode( $postalCode ){
	global $wpdb;
	$t1 = $wpdb->prefix . 'woocommerce_shipping_zone_locations';
	$t2 = $wpdb->prefix . 'woocommerce_shipping_zones';
	$prepared_query = $wpdb->prepare( "SELECT zone_name FROM {$t1} as loc, {$t2} as zon WHERE loc.zone_id = zon.zone_id AND location_code = %d", $postalCode);
	$results = $wpdb->get_results( $prepared_query );
	if (is_null($results)){
		echo "default";
	}
	else{
		foreach ($results as $key => $value) {
		echo $value->zone_name;
	}
	}
}