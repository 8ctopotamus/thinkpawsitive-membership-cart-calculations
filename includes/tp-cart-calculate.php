<?php

function thinkpawsitive_before_calculate_totals( $cart_obj ) {
  /**
  * FREE CLASSES / mo
  */
  // $customer_orders = thinkpawsitive_get_past_orders($user_id, 1);
  // $classCount = thinkpawsitive_count_past_orders($customer_orders, $countable_class_cat_ids);
  // $maxClassAmount = $memberships_max_classes[$user_membership_plan];
  // /**
  // * Count items items in cart and adjust price
  // */
  // foreach( $cart_obj->get_cart() as $key=>$value ) {
  //  $item_cats = $value['data']->get_category_ids();
  //  $isInCat = !empty(array_intersect($countable_class_cat_ids, $item_cats));
  //  if ($isInCat) {
  //    $classCount++;
  //    // change the price if within limits
  //    if ($classCount <= $maxClassAmount) {
  //      $price = 0;
  //      $value['data']->set_price( ( $price ) );
  //    }
  //  }
  // }
  //
  //
  //
  // /**
  // * FREE BOOKABLES / mo
  // */
  // // (1 free 20 minute pro swim) OR (1 free Mat or Turf Rental) / mo
  // $memberships_max_bookables = array(
  //  'gold' => 1,
  //  'silver' => 1,
  // );
  // $countable_bookables_cat_ids = array(
  //  46, // Turf Rental
  //  47, // Mat Rental
  // );
  // $customer_orders = thinkpawsitive_get_past_orders($user_id, 1);
  // $bookablesCount = thinkpawsitive_count_past_orders($customer_orders, $countable_bookables_cat_ids);
  // $maxBookablesAmount = $memberships_max_bookables[$user_membership_plan];
  // // /* check items in cart */
  // foreach( $cart_obj->get_cart() as $key=>$value ) {
  //  $item_cats = $value['data']->get_category_ids();
  //  $isInCat = !empty(array_intersect($countable_bookables_cat_ids, $item_cats));
  //  if ($isInCat) {
  //    $bookablesCount++;
  //    // change the price if within limits
  //    if ($bookablesCount <= $maxBookablesAmount) {
  //      $price = 0;
  //      $value['data']->set_price( ( $price ) );
  //    }
  //  }
  // }
  //
  //
  // /**
  // * FREE Private Lesson/ Qtr
  // */
  // // TODO: 1 free private lesson per qtr cat 52
  // $memberships_max_privLessons = array(
  //  'gold' => 1,
  //  'silver' => 1,
  // );
  // $countable_privLessons_cat_ids = array(
  //  52, // Private Lessons
  // );
  // $customer_orders = thinkpawsitive_get_past_orders($user_id, 3);
  // $privLessonsCount = thinkpawsitive_count_past_orders($customer_orders, $countable_privLessons_cat_ids);
  // $maxPrivLessonsAmount = $memberships_max_privLessons[$user_membership_plan];
  // // /* check items in cart */
  // foreach( $cart_obj->get_cart() as $key=>$value ) {
  //  $item_cats = $value['data']->get_category_ids();
  //  $isInCat = !empty(array_intersect($countable_privLessons_cat_ids, $item_cats));
  //  if ($isInCat) {
  //    $privLessonsCount++;
  //    // change the price if within limits
  //    if ($privLessonsCount <= $maxPrivLessonsAmount) {
  //      $price = 0;
  //      $value['data']->set_price( ( $price ) );
  //    }
  //  }
  // }




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

add_action( 'woocommerce_before_calculate_totals', 'thinkpawsitive_before_calculate_totals', 10, 1);

?>
