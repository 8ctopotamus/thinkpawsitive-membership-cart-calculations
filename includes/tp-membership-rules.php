<?php

// The bookable products category ids from wordpress
$_SESSION['category_ids'] = array(
  'Training Classes' => array(57),
  'Turf or Mat Rentals' => array(46, 47),
  'Private Lessons' => array(52),
  'K9 Splash 20 Minute' => array(62),
  'K9 Splash 30 Minute' => array(63),
);

// The maximum number of free bookable products allowed per month, organized by membership.
// As outlined in the tp-biz-plan.xlsx.
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
  'K9 Splash Gold 20' => array(
    'K9 Splash 20 Minute' => array(
      'limit' => 8,
      'range' => 'month'
    ),
  ),
  'K9 Splash Gold 30' => array(
    'K9 Splash 30 Minute' => array(
      'limit' => 8,
      'range' => 'month'
    ),
  ),
  'K9 Splash Silver 20' => array(
    'K9 Splash 20 Minute' => array(
      'limit' => 6,
      'range' => 'month'
    ),
  ),
  'K9 Splash Silver 30' => array(
    'K9 Splash 30 Minute' => array(
      'limit' => 6,
      'range' => 'month'
    ),
  ),
  'K9 Splash Bronze 20' => array(
    'K9 Splash 20 Minute' => array(
      'limit' => 4,
      'range' => 'month'
    ),
  ),
  'K9 Splash Bronze 30' => array(
    'K9 Splash 30 Minute' => array(
      'limit' => 4,
      'range' => 'month'
    ),
  ),
);

?>
