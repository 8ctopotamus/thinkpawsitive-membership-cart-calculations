<?php
/*
  Plugin Name: Thinkpawsitive Membership Freebies
  Plugin URI:  https://icshelpsyou.com
  Description: Calculate cart items prices based on Woocommerce Membership and ThinkPawsitive's business model.
  Version:     1.0
  Author:      ICS, LLC
  Author URI:  https://icshelpsyou.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// tally overages
include('includes/overages/tp-overages.php');

// cart calculations
// function tp_memberships_freebies_init() {
//   if (is_admin() || !is_user_logged_in() || !function_exists( 'wc_memberships' )) {
//     return;
//   }
//   session_start();
//   include('includes/cart-calculations/tp-get-member.php');
//   include('includes/cart-calculations/tp-membership-rules.php');
//   include('includes/cart-calculations/tp-functions.php');
//   include('includes/cart-calculations/tp-my-account-status.php');
//   include('includes/cart-calculations/tp-cart-calculate.php');
// }
// add_action( 'wp_loaded', 'tp_memberships_freebies_init' );

?>
