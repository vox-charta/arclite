<?php
if (!isset($schedaffil)) $schedaffil = $_COOKIE['schedule_affiliation'];
if (!isset($institution)) $institution = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}votes_institutions WHERE name='{$schedaffil}'");
date_default_timezone_set($institution->timezone);
//if (!isset($ishome)) {
//    if (isset($_GET['ishome'])) {
//  	  $ishome = $_GET['ishome'];
//    } else $ishome = false;
//}
if (!isset($ishome) || $ishome == null) {
	$ishome = isset($_GET['ishome']) ? $_GET['ishome'] : 0;
}
if ($ishome == null) $ishome = 0;
if (!isset($today)) {
    if (isset($_GET['time'])) {
      $today = $_GET['time'];
    } else {
  	  $today = time();
    }
}
?>

