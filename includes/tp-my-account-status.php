<?php

function tp_memberships_freebies_status() {
  $user = wp_get_current_user();
  $user_id = $user->ID;
  $customer_orders = thinkpawsitive_get_past_orders($user_id);

  foreach ($_SESSION['tp_user_membership_plans'] as $membership) {
    echo '<h3>' . $membership . ' Membership - Freebies</h3>';
    echo '<ul>';
    foreach($_SESSION['thinkpawsitive_memberships_max_rules'][$membership] as $key => $rules) {
      if ($rules['limit'] === 0)
        continue;
      $count = count_past_orders_by_cat($customer_orders, $_SESSION['category_ids'][$key]);
      echo '<li>';
      echo '<strong>' . $key . ':</strong> ' . $count . ' out of ' . $rules['limit'] . ' used this ' . $rules['range'] . '.';
      echo '</li>';
    }
    echo '</ul>';
  }
}

add_action('woocommerce_account_dashboard', 'tp_memberships_freebies_status', 10, 0);

?>
