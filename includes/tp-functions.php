<?php
// For testing
function var_awesome($value) {
  echo '<pre>';
  var_export($value);
  echo '</pre>';
}

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

?>
