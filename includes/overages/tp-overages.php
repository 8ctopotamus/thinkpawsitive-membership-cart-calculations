<?php

  function get_prev_months_orders() {
    $RUNNING_TOTAL = array();
    $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
    $past_orders = wc_get_orders(array(
      'date_query' => date("Y-n-j", strtotime("first day of previous month")),
      'post_status' => $order_statuses,
      'numberposts' => -1,
    ));
    foreach ($past_orders as $order):
      $user = $order->get_user();
      $userId = $user->ID;
      // add user if doesn't exist
      if ( !isset( $RUNNING_TOTAL[$userId] ) ):
        $RUNNING_TOTAL[$userId] = array(
          'display_name' => $user->display_name,
          'user_email' => $user->user_email,
          'products' => array(),
        );
      endif;
      // add products to user
      foreach($order->get_items() as $item_id => $item):
        $product = wc_get_product( $item['product_id'] );
        // skip if not a wc bookable product
        if (!$product->is_type( 'booking' )) {
          continue;
        }
        array_push($RUNNING_TOTAL[$userId]['products'], $product);
      endforeach;
    endforeach;
    return $RUNNING_TOTAL;
  }

  function tp_membership_overages_page_html() {
    $results = get_prev_months_orders();

    echo '<pre>';
    var_dump( $results );
    echo '</pre>';

    // for each user in RUNNING_TOTAL
        // get membership level(s)
        // if over allowed limit, display overage in report
    ?>
      <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      </div>
    <?php
  }

  // register the admin page
  function tp_membership_overages_page() {
    add_menu_page(
      'ThinkPawsitive Member Overages',
      'TP Overages',
      'manage_options',
      'tp_membership_overages',
      'tp_membership_overages_page_html'
    );
  }

  add_action( 'admin_menu', 'tp_membership_overages_page' );

?>
