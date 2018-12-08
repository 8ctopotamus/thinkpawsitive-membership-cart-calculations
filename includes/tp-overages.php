<?php
  function nice_var_dump($val) {
    echo '<pre>';
    var_dump($val);
    echo '</pre>';
  }

  function determineBGColor($membership) {
    $membership = strtolower($membership);
    $bgClass = '';
    if (strpos($membership, 'gold') !== false) {
      $bgClass = 'gold';
    } else if (strpos($membership, 'silver') !== false) {
      $bgClass = 'silver';
    } else if (strpos($membership, 'bronze') !== false) {
      $bgClass = 'bronze';
    }
    return $bgClass;
  }

  function calculate_overages($member_orders) {

  }

  function renderMemberTemplate($member) {
    $name = $member['name'];
    $email = $member['email'];
    $memberships = $member['memberships'];
    $products_by_cat = $member['products_by_cat'];
    ?>
      <div class="tp-member-card">
        <h3 class="tp-member-name"><?php echo $name; ?></h3>
        <?php foreach($memberships as $membership): ?>
          <span class="tp-membership-label <?php echo determineBGColor($membership->plan->name); ?>"><?php echo $membership->plan->name; ?></span>
        <?php endforeach; ?>
        <p><a href="mailto: <?php echo $email; ?>"><?php echo $email; ?></a></p>
        <hr/>
        <?php foreach($products_by_cat as $cat => $products): ?>
          <h4><?php echo $cat; ?></h4>
          <ol>
            <?php foreach($products as $product): ?>
              <li><a href="<?php echo $product->get_permalink(); ?>" target="_blank"><?php echo $product->get_name(); ?></a></li>
            <?php endforeach; ?>
          </ol>
        <?php endforeach; ?>
      </div>
    <?php
  }

  function tp_get_members_prev_mo() {
    $RUNNING_TOTAL = array();
    // loop over past month's orders
    $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
    $past_orders = wc_get_orders(array(
      // 'date_query' => date("Y-n-j", strtotime("first day of previous month")),
      'date_query' => array(
        'after' => date("Y-n-j", strtotime("first day of previous month")),
        'before' => date("Y-n-j", strtotime("last day of previous month"))
      ),
      'post_status' => $order_statuses,
      'numberposts' => -1,
    ));
    foreach ($past_orders as $order):
      $user = $order->get_user();
      $user_id = $user->ID;
      // we only care about users who are members
      $memberships = wc_memberships_get_user_active_memberships( $user_id );
      if (empty($memberships)) {
        continue;
      } else {
        // add user to $RUNNING_TOTAL if doesn't exist
        if ( !isset( $RUNNING_TOTAL[$user_id] ) ):
          $RUNNING_TOTAL[$user_id] = array(
            'name' => $user->display_name,
            'email' => $user->user_email,
            'memberships' => $memberships,
            'products_by_cat' => array(),
          );
        endif;
        // add products to user obj
        foreach($order->get_items() as $item_id => $item):
          $product = wc_get_product( $item['product_id'] );
          // skip if not a wc bookable product
          if (!$product->is_type( 'booking' )) {
            continue;
          }
          // check if product category ids are in membership rules
          $product_cats = $product->get_category_ids();
          foreach($_SESSION['category_ids'] as $cat_name => $cat_id):
            $matches = array_intersect($cat_id, $product_cats);
            if ($matches) {
              if (!isset($RUNNING_TOTAL[$user_id]['products_by_cat'][$cat_name])) {
                $RUNNING_TOTAL[$user_id]['products_by_cat'][$cat_name] = array($product);
              } else {
                array_push($RUNNING_TOTAL[$user_id]['products_by_cat'][$cat_name], $product);
              }
            }
          endforeach;
        endforeach;
      }
    endforeach;
    return $RUNNING_TOTAL;
  }

  function tp_membership_overages_page_template() {
    $RUNNING_TOTAL = tp_get_members_prev_mo();
    ?>
      <div class="wrap tp-membership-overages">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <?php
          foreach($RUNNING_TOTAL as $member):
            renderMemberTemplate($member);
          endforeach;
        ?>
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
      'tp_membership_overages_page_template',
      'dashicons-welcome-view-site'
    );
  }
  add_action( 'admin_menu', 'tp_membership_overages_page' );

?>
