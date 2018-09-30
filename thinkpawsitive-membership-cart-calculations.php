<?php
/*
  Plugin Name: Thinkpawsitive Membership Cart Calculations
  Plugin URI:  https://icshelpsyou.com
  Description: Calculate cart items prices based on ThinkPawsitive's business model.
  Version:     1.0
  Author:      ICS, LLC
  Author URI:  https://icshelpsyou.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// utility for devs
function var_awesome($value) {
  echo '<pre>';
  var_export($value);
  echo '</pre>';
}

/**
 * Check the user's membership, past orders of current month, and any cart items.
 ***********************************************************
 * Then just cart prices according to TP Biz plan, outlined in the
 * TP-Memb USE!.xlsx spreadsheet in the root directory of this plugin.
 */
function thinkpawsitive_before_calculate_totals( $cart_obj ) {
 if ( is_admin() && ! defined( 'DOING_AJAX' ) || !is_user_logged_in() || !function_exists( 'wc_memberships' )) {
   return;
 }

 $user = wp_get_current_user();
 $user_id = $user->ID;

/**
 * get current user's membership
 */
 $memberships = wc_memberships_get_user_active_memberships( $user_id );
 if ( !empty( $memberships ) ) {
   // do something for this active member
   foreach($memberships as $membership) {
     // var_awesome($membership->plan);
     var_awesome($membership->plan->name);
   }
 }


 /**
  * check the current user's orders from this month
  */
  $now = new \DateTime('now');
  $month = $now->format('m');
  $year = $now->format('Y');

  ## ==> Define HERE the statuses of that orders
  $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
  // Getting current customer orders
  $customer_orders = wc_get_orders(array(
    'meta_key' => '_customer_user',
    'meta_value' => $user_id,
    'post_status' => $order_statuses,
    'numberposts' => -1,
    'date_query' => array(
      'year' => $year,
      'month' => $month
    )
  ));

 // count through each customer WC_Order objects
 $classCount = 0;

 foreach($customer_orders as $order ) {
   // Order ID (added WooCommerce 3+ compatibility)
   $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
   // Iterating through current orders items
   foreach($order->get_items() as $item_id => $item) {
     $classCount++;
     var_awesome($item['product_id'] . ' ' . $item['name']);

     // // The corresponding product ID (Added Compatibility with WC 3+)
     // $product_id = method_exists( $item, 'get_product_id' ) ? $order->get_product_id() : $item['product_id'];
     //
     // Order Item meta data
     // $item_meta_data = wc_get_order_item_meta( $item_id );
     // var_dump($item_meta_data);

     // TES: Some output
     // echo '<p>Line total for '.wc_get_order_item_meta( $item_id, '_line_total', true ).'</p><br>';
   }
 }


 /**
  * get items in cart
  */
 foreach( $cart_obj->get_cart() as $key=>$value ) {
   var_awesome($value);
   $classCount++;

   // change the prices if need be.
   // $price = 100;
   // $value['data']->set_price( ( $price ) );
 }

 var_awesome('Class count:' . $classCount);

}
add_action( 'woocommerce_before_calculate_totals', 'thinkpawsitive_before_calculate_totals', 10, 1 );




// example

// public function get_customer_total_order() {
//     $customer_orders = get_posts( array(
//         'numberposts' => - 1,
//         'meta_key'    => '_customer_user',
//         'meta_value'  => get_current_user_id(),
//         'post_type'   => array( 'shop_order' ),
//         'post_status' => array( 'wc-completed' ),
//         'date_query' => array(
//             'after' => date('Y-m-d', strtotime('-10 days')),
//             'before' => date('Y-m-d', strtotime('today'))
//         )
//
//     ) );
//
//     $total = 0;
//     foreach ( $customer_orders as $customer_order ) {
//         $order = wc_get_order( $customer_order );
//         $total += $order->get_total();
//     }
//
//     return $total;
// }






 ?>
