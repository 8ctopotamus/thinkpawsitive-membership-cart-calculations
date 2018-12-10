<?php
/*
  Plugin Name: Thinkpawsitive Membership Overages
  Plugin URI:  https://icshelpsyou.com
  Description: Membership overages reporting.
  Version:     1.0
  Author:      ICS, LLC
  Author URI:  https://icshelpsyou.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

session_start();
include('includes/setup.php');
include('includes/tp-membership-rules.php');
include('includes/tp-overages.php');

?>
