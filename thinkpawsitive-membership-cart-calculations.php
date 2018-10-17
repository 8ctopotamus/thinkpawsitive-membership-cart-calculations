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
// For testing
function var_awesome($value) {
  echo '<pre>';
  var_export($value);
  echo '</pre>';
}



 /*
 Handles month/year increment calculations in a safe way,
 avoiding the pitfall of 'fuzzy' month units.

 Returns a DateTime object with incremented month values, and a date value == 1.
 */
 // function incrementDate($startDate, $monthIncrement = 0) {
 //   $startingTimeStamp = $startDate->getTimestamp();
 //   // Get the month value of the given date:
 //   $monthString = date('Y-m', $startingTimeStamp);
 //   // Create a date string corresponding to the 1st of the give month,
 //   // making it safe for monthly calculations:
 //   $safeDateString = "first day of $monthString";
 //   // Increment date by given month increments:
 //   $incrementedDateString = "$safeDateString $monthIncrement month";
 //   $newTimeStamp = strtotime($incrementedDateString);
 //   $newDate = DateTime::createFromFormat('U', $newTimeStamp);
 //   return $newDate;
 // }
 // $currentDate = new DateTime();
 // $oneMonthAgo = incrementDate($currentDate, -1);
 // $twoMonthsAgo = incrementDate($currentDate, -2);
 // $threeMonthsAgo = incrementDate($currentDate, -3);
 //
 // echo "THIS: ".$currentDate->format('F Y') . "<br>";
 // echo "1 AGO: ".$oneMonthAgo->format('F Y') . "<br>";
 // echo "2 AGO: ".$twoMonthsAgo->format('F Y') . "<br>";
 // echo "3 AGO: ".$threeMonthsAgo->format('F Y') . "<br>";

/**
 * Check the user's membership, past orders of current month, and any cart items.
 ***********************************************************
 * Then just cart prices according to TP Biz plan, outlined in the
 * tp-biz-plan.xlsx spreadsheet in the root directory of this plugin.
 */
function thinkpawsitive_get_past_orders($id, $months) {
  $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
  $now = new \DateTime('now');
  $month = $now->format('m');
  $year = $now->format('Y');
  // TODO: figure out how to determine past business quater?
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

function thinkpawsitive_count_past_orders($customer_orders, $cat_ids) {
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
    die();
  }



  /**
   * FREE CLASSES / mo
  */
  $memberships_max_classes = array(
    'gold' => 6,
    'silver' => 4,
    'bronze' => 4
  );
  $countable_class_cat_ids = array(
    33, // training classes category
  );
 /* check the current user's orders from this month */
  $customer_orders = thinkpawsitive_get_past_orders($user_id, 1);
  $classCount = thinkpawsitive_count_past_orders($customer_orders, $countable_class_cat_ids);
  $maxClassAmount = $memberships_max_classes[$user_membership_plan];
  /**
   * Count items items in cart and adjust price
   */
  foreach( $cart_obj->get_cart() as $key=>$value ) {
    $item_cats = $value['data']->get_category_ids();
    $isInCat = !empty(array_intersect($countable_class_cat_ids, $item_cats));
    if ($isInCat) {
      $classCount++;
      // change the price if within limits
      if ($classCount <= $maxClassAmount) {
        $price = 0;
        $value['data']->set_price( ( $price ) );
      }
    }
  }



  /**
   * FREE BOOKABLES / mo
  */
  // (1 free 20 minute pro swim) OR (1 free Mat or Turf Rental) / mo
  $memberships_max_bookables = array(
    'gold' => 1,
    'silver' => 1,
  );
  $countable_bookables_cat_ids = array(
    46, // Turf Rental
    47, // Mat Rental
  );
  $customer_orders = thinkpawsitive_get_past_orders($user_id, 1);
  $bookablesCount = thinkpawsitive_count_past_orders($customer_orders, $countable_bookables_cat_ids);
  $maxBookablesAmount = $memberships_max_bookables[$user_membership_plan];
  // /* check items in cart */
  foreach( $cart_obj->get_cart() as $key=>$value ) {
    $item_cats = $value['data']->get_category_ids();
    $isInCat = !empty(array_intersect($countable_bookables_cat_ids, $item_cats));
    if ($isInCat) {
      $bookablesCount++;
      // change the price if within limits
      if ($bookablesCount <= $maxBookablesAmount) {
        $price = 0;
        $value['data']->set_price( ( $price ) );
      }
    }
  }


  /**
   * FREE Private Lesson/ Qtr
  */
  // TODO: 1 free private lesson per qtr cat 52
  $memberships_max_privLessons = array(
    'gold' => 1,
    'silver' => 1,
  );
  $countable_privLessons_cat_ids = array(
    52, // Private Lessons
  );
  $customer_orders = thinkpawsitive_get_past_orders($user_id, 3);
  $privLessonsCount = thinkpawsitive_count_past_orders($customer_orders, $countable_privLessons_cat_ids);
  $maxPrivLessonsAmount = $memberships_max_privLessons[$user_membership_plan];
  // /* check items in cart */
  foreach( $cart_obj->get_cart() as $key=>$value ) {
    $item_cats = $value['data']->get_category_ids();
    $isInCat = !empty(array_intersect($countable_privLessons_cat_ids, $item_cats));
    if ($isInCat) {
      $privLessonsCount++;
      // change the price if within limits
      if ($privLessonsCount <= $maxPrivLessonsAmount) {
        $price = 0;
        $value['data']->set_price( ( $price ) );
      }
    }
  }




// NOTE: you can use this to test, but echoing from here causes issues

  // <style media="screen">
  //   .membership-status-bar {
  //     background: black;
  //     color: #fff;
  //     padding: 3px 30px;
  //   }
  // </style>

  // Status bar
  // echo '<div class="membership-status-bar">';
  // echo 'Membership level: <span style="color: '. $user_membership_plan .';">' . $user_membership_plan . '.</span>';
  // echo ' FREE Classes ' . $classCount . ' / ' . $maxClassAmount . '. FREE Bookables: ' . $bookablesCount . ' / ' . $maxBookablesAmount . '.';
  // echo '</div>';

  // var_dump('TEST');

}

add_action( 'woocommerce_before_calculate_totals', 'thinkpawsitive_before_calculate_totals', 10, 1 );

?>
