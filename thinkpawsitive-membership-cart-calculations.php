<?php
/*
  Plugin Name: Thinkpawsitive Membership Cart Calculations
  Plugin URI:  https://icshelpsyou.com
  Description: Calculate cart items prices based on Woocommerce Membership and ThinkPawsitive's business model.
  Version:     1.0
  Author:      ICS, LLC
  Author URI:  https://icshelpsyou.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Check the user's membership, past orders of current month, and any cart items.
 ***********************************************************
 * Then just cart prices according to TP Biz plan, outlined in the
 * tp-biz-plan.xlsx spreadsheet in the root directory of this plugin.
 */
function thinkpawsitive_before_calculate_totals( $cart_obj ) {
  if ( is_admin() && ! defined( 'DOING_AJAX' ) || !is_user_logged_in() || !function_exists( 'wc_memberships' )) {
    return;
  }

  $classCount = 0;

  $memberships_max_classes = array(
    'gold' => 6,
    'silver' => 4,
    'bronze' => 4
  );

  $countable_class_cat_ids = array(
    33, // training classes
    37, // agility training
    43, // obedience training
    44, // nosework
    45, // special training
  );

  $user = wp_get_current_user();
  $user_id = $user->ID;
  $user_membership_plan = NULL;

  /**
  * get current user's membership
  */
  $memberships = wc_memberships_get_user_active_memberships( $user_id );
  if ( !empty( $memberships ) ) {
    // do something for this active member
    foreach($memberships as $membership) {
      $user_membership_plan = strtolower($membership->plan->name);
    }
  } else {
    return;
  }

  /**
  * check the current user's orders from this month
  */
  $now = new \DateTime('now');
  $month = $now->format('m');
  $year = $now->format('Y');
  $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');

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

  foreach($customer_orders as $order ) {
    // Order ID (added WooCommerce 3+ compatibility)
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
    // Iterating through current orders items
    foreach($order->get_items() as $item_id => $item) {
      $product = wc_get_product( $item['product_id'] );
      $product_cats = $product->get_category_ids();
      $isInCat = !empty(array_intersect($countable_class_cat_ids, $product_cats));
      if ($isInCat) {
        $classCount++;
      }
    }
  }

 /**
   * check items in cart
   */
  foreach( $cart_obj->get_cart() as $key=>$value ) {
    $item_cats = $value['data']->get_category_ids();
    $isInCat = !empty(array_intersect($countable_class_cat_ids, $item_cats));
    if ($isInCat) {
      $classCount++;
      // change the price if within $memberships_max_classes limits
      if ($classCount <= $memberships_max_classes[$user_membership_plan]) {
        $price = 0;
        $value['data']->set_price( ( $price ) );
      }
    }
  }
}
add_action( 'woocommerce_before_calculate_totals', 'thinkpawsitive_before_calculate_totals', 10, 1 );

?>
