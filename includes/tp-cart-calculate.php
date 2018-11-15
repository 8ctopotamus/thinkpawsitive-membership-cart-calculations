<?php

add_action( 'woocommerce_before_calculate_totals', 'thinkpawsitive_before_calculate_totals', 10, 1);

function thinkpawsitive_before_calculate_totals( $cart_obj ) {
  $user = wp_get_current_user();
  $user_id = $user->ID;

  $customer_past_orders = thinkpawsitive_get_past_orders($user_id);

  // store the counts
  $current_freebies_object = array();

  // Count Past Orders
  foreach ($_SESSION['tp_user_membership_plans'] as $membership) {
    foreach($_SESSION['thinkpawsitive_memberships_max_rules'][$membership] as $key => $rules) {
      if ($rules['limit'] === 0)
        continue;
      $count = count_past_orders_by_cat($customer_past_orders, $_SESSION['category_ids'][$key]);
      $current_freebies_object[$key] = $count;
    }
  }

  // Count Cart Items and
  // adjust cart prices, if necessary
  foreach( $cart_obj->get_cart() as $key=>$value ) {
    if (empty($value['booking'])) { continue; } // skip if is not bookable product
    $item_cats = $value['data']->get_category_ids();
    if ($item_cats) {
      foreach ($_SESSION['tp_user_membership_plans'] as $membership) {
        foreach($_SESSION['thinkpawsitive_memberships_max_rules'][$membership] as $key => $rules) {
          if ($rules['limit'] === 0)
            continue;
          $matches = !empty(array_intersect($_SESSION['category_ids'][$key], $item_cats));
          if ($matches) {
            if (array_key_exists($key, $current_freebies_object)) {
              $current_freebies_object[$key]++;
            } else {
              $current_freebies_object[$key] = 1;
            }
            if ($current_freebies_object[$key] <= $_SESSION['thinkpawsitive_memberships_max_rules'][$membership][$key]['limit']) {
              $price = 0;
              $value['data']->set_price( ( $price ) );
            }
          }
        }
      }
    }
  }
  
}

?>
