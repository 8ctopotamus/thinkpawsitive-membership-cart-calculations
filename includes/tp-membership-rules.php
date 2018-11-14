<?php
// k9 SPLASH LIMITS
// Bronze	4/MO
// Silver	6/MO
// Gold	8/MO

// The maximum number of free bookable products allowed per month, organized by membership.
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
);

// The bookable products category ids
$_SESSION['category_ids'] = array(
  'Training Classes' => array(57),
  'Turf or Mat Rentals' => array(46, 47),
  'Private Lessons' => array(52),
);

?>
