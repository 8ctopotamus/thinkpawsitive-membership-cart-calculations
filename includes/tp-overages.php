<?php
  function nice_var_dump($val) {
    echo '<pre>';
    var_dump($val);
    echo '</pre>';
  }

  function determineMembershipBGColor($membership_name) {
    $membership_name = strtolower($membership_name);
    $bgClass = '';
    if (strpos($membership_name, 'gold') !== false) {
      $bgClass = 'gold';
    } else if (strpos($membership_name, 'silver') !== false) {
      $bgClass = 'silver';
    } else if (strpos($membership_name, 'bronze') !== false) {
      $bgClass = 'bronze';
    }
    return $bgClass;
  }

  function renderOverageWarning($prod_count, $limit) {
    if ($prod_count > $limit): ?>
      <div class="tp-overage-alert"><span class="dashicons dashicons-warning"></span>OVERAGE!</div>
      <?php
    endif;
  }

  function get_membership_cat_limit($cat, $memberships) {
    foreach($memberships as $membership):
      $membership_name = $membership->plan->name;
      $plan_rules = $_SESSION['thinkpawsitive_memberships_max_rules'][$membership_name];
      if ( isset($plan_rules[$cat]) ):
        return $plan_rules[$cat]['limit'];
      else:
        continue;
      endif;
    endforeach;
  }

  function renderDateNav($moStart) {
    // nav link dates
    $navDate = new DateTime( date("Y-m", $moStart) );
    $navDate->modify( '-1 month' );
    $prev = $navDate->format( 'Y-m' );
    $navDate->modify( '+2 month' );
    $next = $navDate->format( 'Y-m' );
    ?>
      <a class="date-nav-link" href="<?php echo admin_url('admin.php?page=tp_membership_overages&date=' . $prev); ?>"><<</a>
      <?php echo date("F Y", $moStart); ?>
      <a class="date-nav-link" href="<?php echo admin_url('admin.php?page=tp_membership_overages&date=' . $next); ?>">>></a>
    <?php
  }

  function renderMemberTemplate($member) {
    $name = $member['name'];
    $email = $member['email'];
    $memberships = $member['memberships'];
    $products_by_cat = $member['products_by_cat'];
    ?>
      <div class="tp-member-card">
        <h3 class="tp-member-name"><?php echo $name; ?> - </h3>
        <?php foreach($memberships as $membership): ?>
          <span class="tp-membership-label <?php echo determineMembershipBGColor($membership->plan->name); ?>"><?php echo $membership->plan->name; ?></span>
        <?php endforeach; ?>
        <p><a href="mailto: <?php echo $email; ?>"><?php echo $email; ?></a></p>
        <hr/>
        <?php foreach($products_by_cat as $cat => $products):
          $prod_count = count($products);
          $limit = get_membership_cat_limit($cat, $memberships);
          ?>
          <div class="tp-cat-group">
            <h4 class="tp-cat-group-title">
              <?php echo $cat . ' - ' . $prod_count . '/' . $limit; ?>
              <?php echo renderOverageWarning($prod_count, $limit); ?>
            </h4>
            <ol>
              <?php foreach($products as $product):?>
                <li><?php echo $product->get_name(); ?> <a href="<?php echo admin_url( 'post.php?post=' . absint( $product->order_id ) . '&action=edit' ); ?>" target="_blank">View order</a></li>
              <?php endforeach; ?>
            </ol>
          </div>
        <?php endforeach; ?>
      </div>
    <?php
  }

  function tp_get_bookings($start, $end) {
    $WCBookings = new WP_Query(array(
      'post_type' => 'wc_booking',
      'posts_per_page' => -1,
      'date_query' => array(
        'after' => date("Y-n-j", $start),
        'before' => date("Y-n-j", $end)
      ),
    ));
    if ( $WCBookings->have_posts() ) :
    	while ( $WCBookings->have_posts() ) : $WCBookings->the_post();
        $booking = new WC_Booking( get_the_id() );
        // $current_timestamp = $booking->get_start_date();
        // nice_var_dump($current_timestamp);
        // nice_var_dump( $booking->get_product() );
        nice_var_dump( $booking->get_customer() );
    	endwhile;
    	wp_reset_postdata();
    else :
    	echo 'No bookables found.';
    endif;

    // loop over past month's orders - OLD
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

    // data store
    $RUNNING_TOTAL = array();

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
              // add order date to each product for later use
              $product->order_id = $order->get_id();
              // add to our member
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
    // parse the date for bookings query
    if (isset($_GET['date'])) {
      $moStart = strtotime("first day of" . $_GET['date']);
      $moEnd = strtotime("last day of" . $_GET['date']);
    } else {
      $moStart = strtotime("first day of this month");
      $moEnd = strtotime("last day of this month");
    }
    ?>
      <div class="wrap tp-membership-overages">
        <h1 class="tp-membership-overages-page-title">
          <?php echo esc_html( get_admin_page_title() ); ?>
          <small><?php renderDateNav( $moStart ); ?></small>
        </h1>
        <?php
          $RUNNING_TOTAL = tp_get_bookings($moStart, $moEnd);

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
