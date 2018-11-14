<?php

function thinkpawsitive_get_past_orders($user_id) {
   $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
   $now = new \DateTime('now');
   $month = $now->format('m');
   $year = $now->format('Y');
   // TODO: figure out how to determine past business quater?
   return wc_get_orders(array(
     'meta_key' => '_customer_user',
     'meta_value' => $user_id,
     'post_status' => $order_statuses,
     'numberposts' => -1,
     'date_query' => array(
       'year' => $year,
       'month' => $month
     )
   ));
}

function count_past_orders_by_cat($orders, $cats) {
  $count = 0;
  foreach($orders as $order) {
    foreach($order->get_items() as $item_id => $item) {
      $product = wc_get_product( $item['product_id'] );
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
