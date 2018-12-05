<?php

/**
* get current user's membership
*/
$_SESSION['tp_user_membership_plans'] = array();
$user = wp_get_current_user();
$user_id = $user->ID;
$memberships = wc_memberships_get_user_active_memberships( $user_id );
if (empty($memberships)) {
  return;
} else {
  foreach($memberships as $membership) {
    $plan_name = $membership->plan->name;
    array_push($_SESSION['tp_user_membership_plans'], $plan_name);
  }
}

?>
