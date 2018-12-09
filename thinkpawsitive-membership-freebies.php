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

if (!is_admin() && !function_exists( 'wc_memberships' )) { return; }

function tp_admin_style() {
  wp_register_style('tp-admin-styles', plugins_url('/css/overages.css',  __FILE__ ));
  if (!empty($_GET['page']) && $_GET['page'] === 'tp_membership_overages'):
    wp_enqueue_style('tp-admin-styles');
  endif;
}
add_action('admin_enqueue_scripts', 'tp_admin_style');

session_start();
include('includes/tp-membership-rules.php');
include('includes/tp-overages.php');

?>
