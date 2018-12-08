<?php
/*
  Plugin Name: Thinkpawsitive Membership Freebies
  Plugin URI:  https://icshelpsyou.com
  Description: Displays membership overages from previous month.
  Version:     2.0
  Author:      ICS, LLC
  Author URI:  https://icshelpsyou.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

session_start();
include('includes/tp-membership-rules.php');
include('includes/tp-overages.php');

function tp_admin_style() {
  wp_register_style('tp-admin-styles', plugins_url('/css/overages.css',  __FILE__ ));
  if (!empty($_GET['page']) && $_GET['page'] === 'tp_membership_overages'):
    wp_enqueue_style('tp-admin-styles');
  endif;
}
add_action('admin_enqueue_scripts', 'tp_admin_style');

// cart calculations - this strategy has been scrapped.
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
