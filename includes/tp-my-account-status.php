<?php

function tp_memberships_status_bar() {
  $user = wp_get_current_user();
  $user_id = $user->ID;

  foreach ($_SESSION['tp_user_memberships'] as $membership) {
    echo '<div class="tp-freebies-status-bar">';
    echo '<h3>' . $membership . ' Membership Freebies</h3>';
    echo '<ul>';

    $customer_orders = thinkpawsitive_get_past_orders($user_id);

    foreach($_SESSION['thinkpawsitive_memberships_max_rules'][$membership] as $key => $value) {
      if ($value === 0)
        continue;
      $count = count_past_orders_by_cat($customer_orders, $_SESSION['category_ids'][$key]);
      echo '<li>';
      echo '<strong>' . $key . ':</strong> ' . $count . ' out of ' . $value['limit'] . ' used this ' . $value['range'] . '.';
      echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
  }
}
add_action('woocommerce_account_dashboard', 'tp_memberships_status_bar', 10, 0);

?>
