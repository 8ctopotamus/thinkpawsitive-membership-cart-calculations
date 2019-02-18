<?php
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

  function renderOverageWarning($cat, $prod_count, $limit) {
    $isOverLimit = $prod_count > $limit;
    $overageClass = $isOverLimit ? 'overage' : '';
    ?>
      <h4 class="tp-cat-group-title">
        <span class="<?php echo$overageClass; ?>">
          <?php echo $cat . ' - ' . $prod_count . '/' . $limit; ?>
        </span>
        <?php
          if ($isOverLimit): ?>
            <div class="tp-overage-alert"><span class="dashicons dashicons-warning"></span> OVERAGE!</div>
            <?php
          endif;
        ?>
      </h4>
    <?php
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

  function renderDateNavLinks($moStart) {
    $navDate = new DateTime( date("Y-m", $moStart) );
    $navDate->modify( '-1 month' );
    $prev = $navDate->format( 'Y-m' );
    $navDate->modify( '+2 month' );
    $next = $navDate->format( 'Y-m' );
    ?>
      <div class="tp-date-nav">
        <a class="date-nav-link" href="<?php echo admin_url('admin.php?page=tp_membership_overages&date=' . $prev); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span></a>
        <?php echo date("F Y", $moStart); ?>
        <a class="date-nav-link" href="<?php echo admin_url('admin.php?page=tp_membership_overages&date=' . $next); ?>"><span class="dashicons dashicons-arrow-right-alt2"></span></a>
      </div>
    <?php
  }

  function renderMemberTemplate($member) {
    $name = $member['name'];
    $email = $member['email'];
    $memberships = $member['memberships'];
    $products_by_cat = $member['products_by_cat'];
    ?>
      <div class="tp-member-card">
        <h3 class="tp-member-name"><?php echo $name; ?> - <small><a href="mailto: <?php echo $email; ?>"><?php echo $email; ?></a></small></h3>
        Memberships:
        <?php foreach($memberships as $membership): ?>
          <span class="tp-membership-label <?php echo determineMembershipBGColor($membership->plan->name); ?>"><?php echo $membership->plan->name; ?></span>
        <?php endforeach; ?>
        <hr/>
        <div class="tp-cats-container">
          <?php foreach($products_by_cat as $cat => $products):
            $prod_count = count($products);
            $limit = get_membership_cat_limit($cat, $memberships);
            ?>
              <div class="tp-cat-group">
                <?php renderOverageWarning($cat, $prod_count, $limit); ?>
                <ol>
                  <?php foreach($products as $product):?>
                    <li>
                      <?php echo $product->get_name(); ?>
                      <?php echo 'Booking: '; ?>
                      <small>(<a href="<?php echo admin_url( 'post.php?post=' . absint( $product->order_id ) . '&action=edit' ); ?>" target="_blank">Order #<?php echo $product->order_id; ?></a>)</small>
                    </li>
                  <?php endforeach; ?>
                </ol>
              </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php
  }

  function tp_get_bookings($start, $end, $bizQuarterStart) {
    $RUNNING_TOTAL = array();
    // format the start date for later use
    $start = new DateTime( date("Y-m", $start) );
    $start = $start->format('m');
    // get bookings from beginning of current biz quarter to date
    $WCBookings = new WP_Query(array(
      'post_type' => 'wc_booking',
      'post_status' => array('complete', 'paid', 'processing'),
      'posts_per_page' => -1,
      'date_query' => array(
        'after' => $bizQuarterStart,
        'before' => date("Y-n-j", $end),
        'inclusive' => true,
      ),
    ));
    if ( $WCBookings->have_posts() ) :
    	while ( $WCBookings->have_posts() ) : $WCBookings->the_post();
        $booking = new WC_Booking( get_the_id() );
        $user_id = $booking->get_customer()->user_id;
        $memberships = wc_memberships_get_user_active_memberships( $user_id );
        if (empty($memberships)) {
          continue;
        } else {
          // add user to $RUNNING_TOTAL if doesn't exist
          if ( !isset( $RUNNING_TOTAL[$user_id] ) ):
            $RUNNING_TOTAL[$user_id] = array(
              'id' => $booking->get_customer()->user_id,
              'name' => $booking->get_customer()->name,
              'email' => $booking->get_customer()->email,
              'memberships' => $memberships,
              'products_by_cat' => array()
            );
          endif;
          // get booking month
          $bookingMonth = strtotime( $booking->get_start_date() );
          $bookingMonth = new DateTime( date("Y-m", $bookingMonth) );
          $bookingMonth = $bookingMonth->format('m');
          // check if product category ids are in membership rules
          $product = $booking->get_product();
          $product_cats = $product->get_category_ids();
          $order_id = $booking->get_order_id();

          foreach($_SESSION['category_ids'] as $cat_name => $cat_id):
            $matches = !empty(array_intersect($cat_id, $product_cats));
            $isPrivateLesson = !empty(array_intersect($_SESSION['category_ids']['Private Lessons'], $product_cats));
            if ($matches) {
              // add order date to each product for later use
              $product->order_id = $order_id;
              // if booking date is not in current month (which means it is in current biz quarter) and is a Private Lesson
              if ($start !== $bookingMonth && !$isPrivateLesson) {
                continue;
              }
              // add to our member
              if (!isset($RUNNING_TOTAL[$user_id]['products_by_cat'][$cat_name])) {
                $RUNNING_TOTAL[$user_id]['products_by_cat'][$cat_name] = array($product);
              } else {
                array_push($RUNNING_TOTAL[$user_id]['products_by_cat'][$cat_name], $product);
              }
            }
          endforeach;
        }
    	endwhile;
    	wp_reset_postdata();
      return $RUNNING_TOTAL;
    else :
    	echo 'No bookables found.';
      return false;
    endif;
  }

  function tp_membership_overages_page_template() {
    // parse requested date
    if (isset($_GET['date'])) {
      $moStart = strtotime("first day of" . $_GET['date']);
      $moEnd = strtotime("last day of" . $_GET['date']);
    } else {
      $moStart = strtotime("first day of this month");
      $moEnd = strtotime("last day of this month");
    }

    // determine first month of current business quarter
    $biz = new DateTime( date("Y-m", $moStart) );
    $month = $biz->format('m');
    $year = $biz->format('Y');
    if (1 <= $month && $month <= 3) {
      $bizQuarterStartMonth = 1;
    } else if (4 <= $month && $month <= 6) {
      $bizQuarterStartMonth = 4;
    } else if (7 <= $month && $month <= 9) {
      $bizQuarterStartMonth = 7;
    } else if (10 <= $month && $month <= 12) {
      $bizQuarterStartMonth = 10;
    }

    $bizQuarterStart = $year . '-' . $bizQuarterStartMonth . '-01';

    ?>
      <div class="wrap tp-membership-overages">
        <h1 class="tp-membership-overages-page-title">
          <?php echo esc_html( get_admin_page_title() ); ?>
          <small><?php renderDateNavLinks( $moStart ); ?></small>
        </h1>
        <?php
          $RUNNING_TOTAL = tp_get_bookings($moStart, $moEnd, $bizQuarterStart);
          if ($RUNNING_TOTAL):
            foreach($RUNNING_TOTAL as $member):
              renderMemberTemplate($member);
            endforeach;
          endif;
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
