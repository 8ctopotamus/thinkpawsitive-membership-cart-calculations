<?php

function thinkpawsitive_get_past_orders($user_id) {
  $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
  $now = new \DateTime('now');
  $month = $now->format('m');
  $year = $now->format('Y');

  // get past orders from the current month
  $past_orders = wc_get_orders(array(
    'meta_key' => '_customer_user',
    'meta_value' => $user_id,
    'post_status' => $order_statuses,
    'numberposts' => -1,
    'date_query' => array(
      'year' => $year,
      'month' => $month
    )
  ));

  // get past orders from within the current business quarter,
  // but before this current month
  if (1 <= $month && $month <= 3) {
    $quarterStartMonth = 1;
  } else if (4 <= $month && $month <= 6) {
    $quarterStartMonth = 4;
  } else if (7 <= $month && $month <= 9) {
    $quarterStartMonth = 7;
  } else if (10 <= $month && $month <= 12) {
    $quarterStartMonth = 10;
  }

  $current_quarter_orders = wc_get_orders(array(
    'meta_key' => '_customer_user',
    'meta_value' => $user_id,
    'post_status' => $order_statuses,
    'numberposts' => -1,
    'date_query' => array(
      array(
        'after' => $year . '-' . $quarterStartMonth . '-01',
        'before' => $year . '-' . $month . '-01',
        'inclusive' => true
      ),
    )
  ));

  // if any of the quarter's orders have a product from the "Private Lessons" category,
  // combine this order with $past_orders.
  foreach ($current_quarter_orders as $order) {
    foreach($order->get_items() as $item_id => $item) {
      $product = wc_get_product( $item['product_id'] );
      $product_cats = $product->get_category_ids();
      $matches = !empty(array_intersect($_SESSION['category_ids']['Private Lessons'], $product_cats));
      if ($matches) {
        // add to our result
        array_push($past_orders, $order);
        continue;
      }
    }
  }

  return $past_orders;
}

function count_past_orders_by_cat($orders, $cats) {
  $count = 0;
  foreach($orders as $order) {
    foreach($order->get_items() as $item_id => $item) {
      $product = wc_get_product( $item['product_id'] );
      if (!$product->is_type( 'booking' )) {
        continue;
      }
      $product_cats = $product->get_category_ids();
      $isInCat = !empty(array_intersect($cats, $product_cats));
      if ($isInCat) {
        $count++;
      }
    }
  }
  return $count;
}

?>
