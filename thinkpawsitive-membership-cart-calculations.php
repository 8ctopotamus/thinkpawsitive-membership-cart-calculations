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

function var_awesome($value) {
  echo '<pre>';
  var_export($value);
  echo '</pre>';
}

/**
 * Check the user's membership, past orders of current month, and any cart items.
 ***********************************************************
 * Then just cart prices according to TP Biz plan, outlined in the
 * tp-biz-plan.xlsx spreadsheet in the root directory of this plugin.
 */


function thinkpawsitive_get_orders_from_months($id, $months) {
  $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
  $now = new \DateTime('now');
  $month = $now->format('m');
  $year = $now->format('Y');

  return wc_get_orders(array(
    'meta_key' => '_customer_user',
    'meta_value' => $id,
    'post_status' => $order_statuses,
    'numberposts' => -1,
    'date_query' => array(
      'year' => $year,
      'month' => $month
    )
  ));
}

function thinkpawsitive_count_orders($customer_orders, $cat_ids) {
  $count = 0;
  foreach($customer_orders as $order ) {
    // Order ID (added WooCommerce 3+ compatibility)
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
    // Iterating through current orders items
    foreach($order->get_items() as $item_id => $item) {
      $product = wc_get_product( $item['product_id'] );
      $product_cats = $product->get_category_ids();
      $isInCat = !empty(array_intersect($cat_ids, $product_cats));
      if ($isInCat) {
        $classCount++;
      }
    }
  }
  return $count;
}

function thinkpawsitive_update_cart($maxAmount, $cat_ids, $cart_obj) {
  foreach( $cart_obj->get_cart() as $key=>$value ) {
    $item_cats = $value['data']->get_category_ids();
    $isInCat = !empty(array_intersect($cat_ids, $item_cats));
    if ($isInCat) {
      $classCount++;
      // change the price if within $memberships_max_classes limits
      if ($classCount <= $maxAmount) {
        $price = 0;
        $value['data']->set_price( ( $price ) );
      }
    }
  }
}

function thinkpawsitive_before_calculate_totals( $cart_obj ) {
  if ( is_admin() && ! defined( 'DOING_AJAX' ) || !is_user_logged_in() || !function_exists( 'wc_memberships' )) {
    return;
  }

  /**
  * get current user's membership
  */
  $user = wp_get_current_user();
  $user_id = $user->ID;
  $user_membership_plan = NULL;

  $memberships = wc_memberships_get_user_active_memberships( $user_id );
  if ( !empty( $memberships ) ) {
    // do something for this active member
    foreach($memberships as $membership) {
      $user_membership_plan = strtolower($membership->plan->name);
    }
  } else {
    return;
  }

  // FREE CLASSES
  $memberships_max_classes = array(
    'gold' => 6,
    'silver' => 4,
    'bronze' => 4
  );
  $countable_class_cat_ids = array(
    33, // training classes category
  );

  $customer_orders = thinkpawsitive_get_orders_from_months($user_id, 1);
  $classCount = thinkpawsitive_count_orders($customer_orders, $countable_class_cat_ids);
  thinkpawsitive_update_cart($memberships_max_classes[$user_membership_plan], $countable_class_cat_ids, $cart_obj);


  // TODO
  // 1 free private lesson per qtr cat 52
  // (1 free 20 minute pro swim) OR (1 free Mat or Turf Rental)


  // Determine

  // FREE BOOKABLES:
    // 46 Turf Rental
    // 47 Mat Rental



  // Status bar

  ?>

  <style media="screen">
    .membership-status-bar {
      background: black;
      color: #fff;
      padding: 3px 30px;
    }
  </style>

  <?php

  echo '<div class="membership-status-bar">';
  echo 'Membership level: <span style="color: '. $user_membership_plan .';">' . $user_membership_plan . '.</span>';
  echo ' You have used ' . $classCount . ' of your ' . $memberships_max_classes[$user_membership_plan] . ' FREE classes this month.';
  echo '</div>';

}

add_action( 'woocommerce_before_calculate_totals', 'thinkpawsitive_before_calculate_totals', 10, 1 );

?>
