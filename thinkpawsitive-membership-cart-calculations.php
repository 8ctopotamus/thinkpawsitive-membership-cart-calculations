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


function tp_memberships_freebies_init() {
  if (is_admin() || !is_user_logged_in() || !function_exists( 'wc_memberships' )) {
    return;
  }

  session_start();

  /**
  * get current user's membership
  */
  $_SESSION['tp_user_memberships'] = array();
  $user = wp_get_current_user();
  $user_id = $user->ID;
  $memberships = wc_memberships_get_user_active_memberships( $user_id );
  if (empty($memberships)) {
    return;
  } else {
    foreach($memberships as $membership) {
      $plan_name = $membership->plan->name;//strtolower($membership->plan->name);
      array_push($_SESSION['tp_user_memberships'], $plan_name);
    }
  }

  // The maximum number of free bookable products allowed per month, organized by membership.
  $_SESSION['thinkpawsitive_memberships_max_rules'] = array(
    'Gold' => array(
      'Training Classes' => array(
        'limit' => 6,
        'range' => 'month'
      ),
      'Turf or Mat Rentals' => array(
        'limit' => 1,
        'range' => 'month'
      ),
      'Private Lessons' => array(
        'limit' => 1,
        'range' => 'quarter'
      ),
    ),
    'Silver' => array(
      'Training Classes' => array(
        'limit' => 4,
        'range' => 'month'
      ),
      'Turf or Mat Rentals' => array(
        'limit' => 1,
        'range' => 'month'
      ),
      'Private Lessons' => array(
        'limit' => 1,
        'range' => 'quarter'
      ),
    ),
    'Bronze' => array(
      'Training Classes' => array(
        'limit' => 4,
        'range' => 'month'
      ),
      'Turf or Mat Rentals' => array(
        'limit' => 0,
        'range' => 'month'
      ),
      'Private Lessons' => array(
        'limit' => 0,
        'range' => 'quarter'
      ),
    ),
  );

  // The bookable products category ids
  $_SESSION['category_ids'] = array(
    'Training Classes' => array(57),
    'Turf or Mat Rentals' => array(46, 47),
    'Private Lessons' => array(52),
  );

  include('includes/tp-functions.php');
  include('includes/tp-my-account-status.php');
  // include('includes/tp-cart-calculate.php');

}
add_action( 'wp_loaded', 'tp_memberships_freebies_init' );


?>
