<? php

include_once $_SERVER['PHP_ROOT'].'/html/init.php';
include_once $_SERVER['PHP_ROOT'].'/lib/home.php';
include_once $_SERVER['PHP_ROOT'].'/lib/requests.php';
include_once $_SERVER['PHP_ROOT'].'/lib/feed/newsfeed.php';
include_once $_SERVER['PHP_ROOT'].'/lib/poke.php';
include_once $_SERVER['PHP_ROOT'].'/lib/share.php';
include_once $_SERVER['PHP_ROOT'].'/lib/orientation.php';
include_once $_SERVER['PHP_ROOT'].'/lib/feed/newsfeed.php';
include_once $_SERVER['PHP_ROOT'].'/lib/mobile/register.php';
include_once $_SERVER['PHP_ROOT'].'/lib/forms_lib.php';
include_once $_SERVER['PHP_ROOT'].'/lib/contact_importer/contact_importer.php';
include_once $_SERVER['PHP_ROOT'].'/lib/feed/util.php';
include_once $_SERVER['PHP_ROOT'].'/lib/hiding_prefs.php';
include_once $_SERVER['PHP_ROOT'].'/lib/abtesting.php';
include_once $_SERVER['PHP_ROOT'].'/lib/friends.php';
include_once $_SERVER['PHP_ROOT'].'/lib/statusupdates.php';

// lib/display/feed.php has to be declared here for scope issues.
// This keeps display/feed.php cleaner and easier to understand.
include_once $_SERVER['PHP_ROOT'].'/lib/display/feed.php';
include_once $_SERVER['PHP_ROOT'].'/lib/monetization_box.php';

// require login
$user = require_login();
print_time('require_login');
param_request(array('react' = > $PARAM_EXISTS));

// Check and fix broken emails
// LN - disabling due to excessive can_see dirties and sets when enabled.
//check_and_fix_broken_emails($user);
// migrate AIM screenname from profile to screenname table if needed
migrate_screenname($user);

// homepage announcement variables
$HIDE_ANNOUNCEMENT_BIT = get_site_variable('HIDE_ANNOUNCEMENT_BIT');
$HIDE_INTRO_BITMASK = get_site_variable('HIDE_INTRO_BITMASK');

// redirects
if (is_sponsor_user()) {
  redirect('bizhome.php', 'www');
}

include_once $_SERVER['PHP_ROOT'].'/lib/mesg.php';
include_once $_SERVER['PHP_ROOT'].'/lib/invitetool.php';
include_once $_SERVER['PHP_ROOT'].'/lib/grammar.php';
include_once $_SERVER['PHP_ROOT'].'/lib/securityq.php';
include_once $_SERVER['PHP_ROOT'].'/lib/events.php';
include_once $_SERVER['PHP_ROOT'].'/lib/rooster/stories.php';

// todo: password confirmation redirects here (from html/reset.php),
// do we want a confirmation message?
param_get_slashed(array(
  'feeduser' = > $PARAM_INT, //debug: gets feed for user here
  'err' = > $PARAM_STRING, // returning from a failed entry on an orientation form
  'error' = > $PARAM_STRING, // an error can also be here because the profile photo upload code is crazy
  'ret' = > $PARAM_INT, 'success' = > $PARAM_INT, // successful profile picture upload
  'jn' = > $PARAM_INT, // joined a network for orientation
  'np' = > $PARAM_INT, // network pending (for work/address network)
  'me' = > $PARAM_STRING, // mobile error
  'mr' = > $PARAM_EXISTS, // force mobile reg view
  'mobile' = > $PARAM_EXISTS, // mobile confirmation code sent
  'jif' = > $PARAM_EXISTS, // just imported friends
  'ied' = > $PARAM_STRING, // import email domain
  'o' = > $PARAM_EXISTS, // first time orientation, passed on confirm
  'verified' = > $PARAM_EXISTS)); // verified mobile phone

param_post(array(
  'leave_orientation' = > $PARAM_EXISTS, 
  'show_orientation' = > $PARAM_INT, // show an orientation step
  'hide_orientation' = > $PARAM_INT)); // skip an orientation step

// homepage actions
if ($req_react && validate_expiring_hash($req_react, $GLOBALS['url_md5key'])) {
  $show_reactivated_message = true;
} else {
  $show_reactivated_message = false;
}
tpl_set('show_reactivated_message', $show_reactivated_message);


// upcoming events
events_check_future_events($user); // make sure big tunas haven't moved around
$upcoming_events = events_get_imminent_for_user($user);

// this is all stuff that can be fetched together!
$upcoming_events_short = array();
obj_multiget_short(array_keys($upcoming_events), true, $upcoming_events_short);
$new_pokes = 0;

//only get the next N pokes for display
//where N is set in the dbget to avoid caching issues
$poke_stats = get_num_pokes($user);
get_next_pokes($user, true, $new_pokes);
$poke_count = $poke_stats['unseen'];

$targeted_data = array();
home_get_cache_targeted_data($user, true, $targeted_data);
$announcement_data = array();
home_get_cache_announcement_data($user, true, $announcement_data);
$orientation = 0;
orientation_get_status($user, true, $orientation);
$short_profile = array();
profile_get_short($user, true, $short_profile);

// pure priming stuff
privacy_get_network_settings($user, true);
$presence = array();
mobile_get_presence_data($user, true, $presence);
feedback_get_event_weights($user, true);

// Determine if we want to display the feed intro message
$intro_settings = 0;
user_get_hide_intro_bitmask($user, true, $intro_settings);
$user_friend_finder = true;
contact_importer_get_used_friend_finder($user, true, $used_friend_finder);
$all_requests = requests_get_cache_data($user);

// FIXME?: is it sub-optimal to call this both in requests_get_cache_data and here?
$friends_status = statusupdates_get_recent($user, null, 3);
memcache_dispatch(); // populate cache data

// Merman's Admin profile always links to the Merman's home
if (user_has_obj_attached($user)) {
  redirect('mhome.php', 'www');
}

if (is_array($upcoming_events)) {
  foreach($upcoming_events as $event_id = > $data) {
    $upcoming_events[$event_id]['name'] = txt_set($upcoming_events_short[$event_id]['name']);
  }
}

tpl_set('upcoming_events', $upcoming_events);

// disabled account actions
$disabled_warning = ((IS_DEV_SITE || IS_QA_SITE) && is_disabled_user($user));
tpl_set('disabled_warning', $disabled_warning);

// new pokes (no more messages here, they are in the top nav!)
if (!user_is_guest($user)) {
  tpl_set('poke_count', $poke_count);
  tpl_set('pokes', $new_pokes);
}

// get announcement computations
tpl_set('targeted_data', $targeted_data);
tpl_set('announcement_data', $announcement_data);


// birthday notifications
tpl_set('birthdays', $birthdays = user_get_birthday_notifications($user, $short_profile));
tpl_set('show_birthdays', $show_birthdays = (count($birthdays) || !$orientation));

// user info
tpl_set('first_name', user_get_first_name(txt_set($short_profile['id'])));
tpl_set('user', $user);

// decide if there are now any requests to show
$show_requests = false;
foreach($all_requests as $request_category) {
  if ($request_category) {
    $show_requests = true;
    break;
  }
}
tpl_set('all_requests', $show_requests ? $all_requests : null);

$permissions = privacy_get_reduced_network_permissions($user, $user);

// status
$user_info = array('user' = > $user, 'firstname' = > user_get_first_name($user), 'see_all' = > '/statusupdates/?ref=hp', 'profile_pic' = > make_profile_image_src_direct($user, 'thumb'), 'square_pic' = > make_profile_image_src_direct($user, 'square'));

if (!empty($presence) && $presence['status_time'] > (time() - 60 * 60 * 24 * 7)) {
  $status = array('message' = > txt_set($presence['status']), 'time' = > $presence['status_time'], 'source' = > $presence['status_source']);
} else {
  $status = array('message' = > null, 'time' = > null, 'source' = > null);
}
tpl_set('user_info', $user_info);

tpl_set('show_status', $show_status = !$orientation);
tpl_set('status', $status);
tpl_set('status_custom', $status_custom = mobile_get_status_custom($user));
tpl_set('friends_status', $friends_status);

// orientation
if ($orientation) {
  if ($post_leave_orientation) {
    orientation_update_status($user, $orientation, 2);
    notification_notify_exit_orientation($user);
    dirty_user($user);
    redirect('home.php');
  } else if (orientation_eligible_exit(array('uid' = > $user)) == 2) {
    orientation_update_status($user, $orientation, 1);
    notification_notify_exit_orientation($user);
    dirty_user($user);
    redirect('home.php');
  }
}

// timezone - outside of stealth, update user's timezone if necessary
$set_time = !user_is_alpha($user, 'stealth');
tpl_set('timezone_autoset', $set_time);
if ($set_time) {
  $daylight_savings = get_site_variable('DAYLIGHT_SAVINGS_ON');
  tpl_set('timezone', $short_profile['timezone'] - ($daylight_savings ? 4 : 5));
}

// set next step if we can
if (!$orientation) {
  user_set_next_step($user, $short_profile);
}

// note: don't make this an else with the above statement, because then no news feed stories will be fetched if they're exiting orientation
if ($orientation) {
  extract(orientation_get_const());

  require_js('js/dynamic_dialog.js');
  require_js('js/suggest.js');
  require_js('js/typeahead_ns.js');
  require_js('js/suggest.js');
  require_js('js/editregion.js');
  require_js('js/orientation.js');
  require_css('css/typeahead.css');
  require_css('css/editor.css');

  if ($post_hide_orientation && $post_hide_orientation <= $ORIENTATION_MAX) {
    $orientation['orientation_bitmask'] |= ($post_hide_orientation * $ORIENTATION_SKIPPED_MODIFIER);
    orientation_update_status($user, $orientation);
  } else if ($post_show_orientation && $post_show_orientation <= $ORIENTATION_MAX) {
    $orientation['orientation_bitmask'] &= ~ ($post_show_orientation * $ORIENTATION_SKIPPED_MODIFIER);
    orientation_update_status($user, $orientation);
  }

  $stories = orientation_get_stories($user, $orientation);
  switch ($get_err) {
  case $ORIENTATION_ERR_COLLEGE:
    $temp = array(); // the affil_retval_msg needs some parameters won't be used
    $stories[$ORIENTATION_NETWORK]['failed_college'] = affil_retval_msg($get_ret, $temp, $temp);
    break;
  case $ORIENTATION_ERR_CORP:
    $temp = array();
    // We special case the network not recognized error here, because affil_retval_msg is retarded.
    $stories[$ORIENTATION_NETWORK]['failed_corp'] = ($get_ret == 70) ? 'The email you entered did not match any of our supported networks. '.'Click here to see our supported list. '.'Go here to suggest your network for the future.' : affil_retval_msg($get_ret, $temp, $temp);
    break;
  }

  // photo upload error
  if ($get_error) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_PROFILE]]['upload_error'] = pic_get_error_text($get_error);
  }
  // photo upload success
  else if ($get_success == 1) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_PROFILE]]['uploaded_pic'] = true;
    // join network success
  } else if ($get_jn) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_NETWORK]]['joined'] = array('id' = > $get_jn, 'name' = > network_get_name($get_jn));
    // network join pending
  } else if ($get_np) {

    $stories[$ORIENTATION_ORDER[$ORIENTATION_NETWORK]]['join_pending'] = array('id' = > $get_np, 'email' = > get_affil_email_conf($user, $get_np), 'network' = > network_get_name($get_np));
    // just imported friend confirmation
  } else if ($get_jif) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_NETWORK]]['just_imported_friends'] = true;
    $stories[$ORIENTATION_ORDER[$ORIENTATION_NETWORK]]['domain'] = $get_ied;
  }

  // Mobile web API params
  if ($get_mobile) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_MOBILE]]['sent_code'] = true;
    $stories[$ORIENTATION_ORDER[$ORIENTATION_MOBILE]]['view'] = 'confirm';
  }
  if ($get_verified) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_MOBILE]]['verified'] = true;
  }
  if ($get_me) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_MOBILE]]['error'] = $get_me;
  }
  if ($get_mr) {
    $stories[$ORIENTATION_ORDER[$ORIENTATION_MOBILE]]['view'] = 'register';
  }

  if (orientation_eligible_exit($orientation)) {
    tpl_set('orientation_show_exit', true);
  }
  tpl_set('orientation_stories', $stories);

  //if in orientation, we hide all feed intros (all 1's in bitmask)
  $intro_settings = -1;

}
tpl_set('orientation', $orientation);

// Rooster Stories
if (!$orientation && ((get_site_variable('ROOSTER_ENABLED') == 2) || (get_site_variable('ROOSTER_DEV_ENABLED') == 2))) {
  $rooster_story_count = get_site_variable('ROOSTER_STORY_COUNT');
  if (!isset($rooster_story_count)) {
    // Set default if something is wrong with the sitevar
    $rooster_story_count = 2;
  }
  $rooster_stories = rooster_get_stories($user, $rooster_story_count, $log_omissions = true);
  if (!empty($rooster_stories) && !empty($rooster_stories['stories'])) {
    // Do page-view level logging here
    foreach($rooster_stories['stories'] as $story) {
      rooster_log_action($user, $story, ROOSTER_LOG_ACTION_VIEW);
    }
    tpl_set('rooster_stories', $rooster_stories);
  }
}

// set the variables for the home announcement code
$hide_announcement_tpl = ($intro_settings | $HIDE_INTRO_BITMASK) & $HIDE_ANNOUNCEMENT_BIT;
// if on qa/dev site, special rules
$HIDE_INTRO_ON_DEV = get_site_variable('HIDE_INTRO_ON_DEV');
if ((IS_QA_SITE || IS_DEV_SITE) && !$HIDE_INTRO_ON_DEV) {
  $hide_announcement_tpl = 0;
}

tpl_set('hide_announcement', $hide_announcement_tpl);
if ($is_candidate = is_candidate_user($user)) {
  tpl_set('hide_announcement', false);
}
$home_announcement_tpl = !$hide_announcement_tpl || $is_candidate ? home_get_announcement_info($user) : 0;
tpl_set('home_announcement', $home_announcement_tpl);
tpl_set('hide_announcement_bit', $HIDE_ANNOUNCEMENT_BIT);

$show_friend_finder = !$orientation && contact_importer_enabled($user) && !user_get_hiding_pref($user, 'home_friend_finder');
tpl_set('show_friend_finder', $show_friend_finder);
if ($show_friend_finder && (user_get_friend_count($user) > 20)) {
  tpl_set('friend_finder_hide_options', array('text' = > 'close', 'onclick' = > "return clearFriendFinder()"));
} else {
  tpl_set('friend_finder_hide_options', null);
}

$account_info = user_get_account_info($user);
$account_create_time = $account_info['time'];

tpl_set('show_friend_finder_top', !$used_friend_finder);

tpl_set('user', $user);


// MONETIZATION BOX
$minimize_monetization_box = user_get_hiding_pref($user, 'home_monetization');
$show_monetization_box = (!$orientation && get_site_variable('HOMEPAGE_MONETIZATION_BOX'));
tpl_set('show_monetization_box', $show_monetization_box);
tpl_set('minimize_monetization_box', $minimize_monetization_box);

if ($show_monetization_box) {
  $monetization_box_data = monetization_box_user_get_data($user);
  txt_set('monetization_box_data', $monetization_box_data);
}


// ORIENTATION
if ($orientation) {
  $network_ids = id_get_networks($user);
  $network_names = multiget_network_name($network_ids);
  $in_corp_network = in_array($GLOBALS['TYPE_CORP'], array_map('extract_network_type', $network_ids));
  $show_corp_search = $in_corp_network || get_age(user_get_basic_info_attr($user, 'birthday')) >= 21;
  $pending_hs = is_hs_pending_user($user);
  $hs_id = null;
  $hs_name = null;
  if ($pending_hs) {
    foreach(id_get_pending_networks($user) as $network) {
      if (extract_network_type($network['network_key']) == $GLOBALS['TYPE_HS']) {
        $hs_id = $network['network_key'];
        $hs_name = network_get_name($hs_id);
        break;
      }
    }
  }
  //$orientation_people = orientation_get_friend_and_inviter_ids($user);
  $orientation_people = array('friends' = > user_get_all_friends($user), 'pending' = > array_keys(user_get_friend_requests($user)), 'inviters' = > array(), // wc: don't show inviters for now
  );
  $orientation_info = array_merge($orientation_people, array('network_names' = > $network_names, 'show_corp_search' = > $show_corp_search, 'pending_hs' = > array('hs_id' = > $hs_id, 'hs_name' = > $hs_name), 'user' = > $user, ));
  tpl_set('orientation_info', $orientation_info);

  tpl_set('simple_orientation_first_login', $get_o); // unused right now
}


// Roughly determine page length for ads
// first, try page length using right-hand panel
$ads_page_length_data = 3 + // 3 for profile pic + next step
($show_friend_finder ? 1 : 0) + ($show_status ? ($status_custom ? count($friends_status) : 0) : 0) + ($show_monetization_box ? 1 : 0) + ($show_birthdays ? count($birthdays) : 0) + count($new_pokes);

// page length using feed stories
if ($orientation) {
  $ads_page_length_data = max($ads_page_length_data, count($stories) * 5);
}
tpl_set('ads_page_length_data', $ads_page_length_data);

$feed_stories = null;
if (!$orientation) { // if they're not in orientation they get other cool stuff
  // ad_insert: the ad type to try to insert for the user
  // (0 if we don't want to try an insert) 
  $ad_insert = get_site_variable('FEED_ADS_ENABLE_INSERTS');

  $feed_off = false;

  if (check_super($user) && $get_feeduser) {
    $feed_stories = user_get_displayable_stories($get_feeduser, 0, null, $ad_insert);
  } else if (can_see($user, $user, 'feed')) {
    $feed_stories = user_get_displayable_stories($user, 0, null, $ad_insert);
  } else {
    $feed_off = true;
  }

  // Friend's Feed Selector - Requires dev.php constant
  if (is_friendfeed_user($user)) {
    $friendfeed = array();
    $friendfeed['feeduser'] = $get_feeduser;
    $friendfeed['feeduser_name'] = user_get_name($get_feeduser);
    $friendfeed['friends'] = user_get_all_friends($user);
    tpl_set('friendfeed', $friendfeed);
  }

  $feed_stories = feed_adjust_timezone($user, $feed_stories);

  tpl_set('feed_off', $feed_off ? redirect('privacy.php?view=feeds', null, false) : false);
}
tpl_set('feed_stories', $feed_stories);

render_template($_SERVER['PHP_ROOT'].'/html/home.phpt');