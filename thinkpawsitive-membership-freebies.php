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

function tp_memberships_freebies_init() {
  if (is_admin() || !is_user_logged_in() || !function_exists( 'wc_memberships' )) {
    return;
  }

  session_start();

  include('includes/tp-get-member.php');
  include('includes/tp-membership-rules.php');
  include('includes/tp-functions.php');
  include('includes/tp-my-account-status.php');
  include('includes/tp-cart-calculate.php');
}

add_action( 'wp_loaded', 'tp_memberships_freebies_init' );

?>
