<?php

include_once("/var/www/html/voxcharta/wp-blog-header.php");
require_once("arxiv.php");
//include_once(VoteItUp_Path()."/votingfunctions.php");
header('HTTP/1.1 200 OK'); //Wordpress sends a 404 for some reason, override this (added by JFG).
global $wpdb, $current_user, $today, $reset_time, $prev_coffee, $institution, $schedaffil, $ishome;
global $where_string, $where_string2, $page_uri, $post, $postloop, $arxiv_cat_abbrv;
global $arxiv_cats, $arxiv_cat_slugs, $arxiv_cat_kind;

$site_url = get_option('siteurl');
$postloop = true;
get_currentuserinfo();
$begtime = microtime(true);
//$catnamstr = md5(implode(",", $arxiv_cat_abbrv));
//if (!isset($_COOKIE['catvis']) || !isset($_COOKIE['catnam']) || $_COOKIE['catnam'] != $catnamstr) {
//	$catvis = array_fill(0, count($arxiv_cat_abbrv), 1);
//} else {
	$catvis = explode(",", $_COOKIE['catvis']);
//}
if (!isset($_COOKIE['sortval'])) {
	$sortval = 'postnum';
} else {
	$sortval = $_COOKIE['sortval'];
}
if (!isset($_COOKIE['showabstracts'])) {
	$showabstracts = 1;
} else {
	$showabstracts = $_COOKIE['showabstracts'];
}
if (!isset($_COOKIE['orderval'])) {
	$orderval = 'ASC';
} else {
	$orderval = $_COOKIE['orderval'];
}
if (!isset($_COOKIE['showspecial'])) {
	$showspecial = 1;
} else {
	$showspecial = $_COOKIE['showspecial'];
}
if (!isset($_COOKIE['shownew'])) {
	$shownew = 1;
} else {
	$shownew = $_COOKIE['shownew'];
}
if (!isset($_COOKIE['showcro'])) {
	$showcro = 1;
} else {
	$showcro = $_COOKIE['showcro'];
}
if (!isset($_COOKIE['showrep'])) {
	$showrep = 1;
} else {
	$showrep = $_COOKIE['showrep'];
}
if ($sortval == 'votehistory' && !is_user_logged_in()) {
	$sortval = 'postnum';
	$orderval = 'ASC';
}
if (!isset($schedaffil)) $schedaffil = $_COOKIE['schedule_affiliation'];
if (!isset($institution)) $institution = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}votes_institutions WHERE name='{$schedaffil}'");
if (!isset($ishome) || $ishome == null) {
	$ishome = isset($_GET['ishome']) ? $_GET['ishome'] : 0;
}
if ($ishome == null) $ishome = 0;

$orderby = '&meta_key=wpo_sourcepermalink&orderby=meta_value&order=ASC';

if (isset($_GET['query'])) {
	$today = urldecode($_GET['query']);
}
if (isset($_GET['page_uri'])) {
	$page_uri = $_GET['page_uri'];
}

if ($ishome) {
	$last_post_time = time();
} else {
	$last_post_time = $today;
}

//date_default_timezone_set('GMT');
//$reset_time = strtotime('23:59', $today);
//date_default_timezone_set($institution->timezone);
date_default_timezone_set('US/Pacific');
$reset_time = strtotime(date('Y-m-d ', $today) . '17:30');
if ($today > $reset_time) {
	if (date('D', $today) == 'Fri') {
		$thisday = $reset_time - 86400;
		$headline = "Today";
	} elseif (date('D', $today) == 'Sat') {
		$thisday = $reset_time - 2*86400;
		$headline = "Yesterday";
	} else {
		$headline = "Today";
		$thisday = $reset_time;
	}
} else {
	if (date('D', $today) == 'Sat') {
		$thisday = $reset_time - 2*86400;
		$headline = "Yesterday";
	} elseif (date('D', $today) == 'Sun') {
		$thisday = $reset_time - 3*86400;
		$headline = "Friday";
	} else {
		$headline = "Yesterday";
		$thisday = $reset_time - 86400;
	}
}
$where_string = "post_date > '" . date('Y-m-d H:i:s', $thisday) . "' AND post_date < '".date('Y-m-d H:i:s', $thisday + 86400)."'";
//if (!$ishome) {
	$where_string2 = "post_date > '" . date('Y-m-d 00:00:00', $today) . "' AND post_date < '".date('Y-m-d 00:00:00', $today + 86400)."'";
//} else {
//	$where_string2 = $where_string;
//}
date_default_timezone_set($institution->timezone);
$next_offset = AgendaOffset('next', 'an');
$club_check = $agenda_info;
$next_coffee = AgendaOffset('next', 'co');
$prev_coffee = AgendaOffset('prev', 'co');

function fw($where = '') {
	global $where_string;
	return $where.' AND '.$where_string;
}
function fw2($where = '') {
	global $where_string2;
	return $where.' AND '.$where_string2;
}

remove_all_filters('posts_where');
add_filter('posts_where', 'fw');
$myquery = new WP_Query('nopaging=true'.$orderby);
remove_filter('posts_where', 'fw');
add_filter('posts_where', 'fw2');
$squery = new WP_Query('category_name=special-topics&orderby=post_date&order=ASC&nopaging=true');
remove_filter('posts_where', 'fw2');

$is_processing = $wpdb->get_var("SELECT processing FROM {$wpdb->prefix}wpo_campaign WHERE title = 'astro-ph'");
if ($ishome && $is_processing) {
	?><h2><?php _e("Processing today's listings...","arclite"); ?></h2>
	<p class="error"><?php _e("Vox Charta is currently processing today's new ArXiv postings, and the list shown below is likely incomplete. The day's full listing will be all available shortly! If you see this message for more than an hour, please notify the site administrator.","arclite");?></p><?php
	ob_flush();
}
if ($myquery->have_posts() || $squery->have_posts()) {
	class PostData
	{
		public $count, $icount, $cat_nums, $vc, $evc,
		       $primary_cat, $cats, $post, $pmd;

		function __construct() {
			global $arxiv_cats;
			$icount = 0;
		}
		static function alpha_sort($a, $b)
		{
			$a1 = strtolower($a->post->post_title);
			$b1 = strtolower($b->post->post_title);
			if ($a1 == $b1) {
				return self::num_sort($a, $b);	
			}
			return ($a1 > $b1) ? +1 : -1;
		}
		static function num_sort($a, $b)
		{
			foreach (array_reverse($a->cat_nums) as $i => $cn) { 
				if ($a->cat_nums[$i] != $b->cat_nums[$i]) return ($a->cat_nums[$i] > $b->cat_nums[$i]) ? +1 : -1;
			}
			return 0;
		}
		static function count_sort($a, $b)
		{
			if ($a->count == $b->count) {
				return self::num_sort($a, $b);	
			}
			return ($a->count > $b->count) ? +1 : -1;
		}
		static function icount_sort($a, $b)
		{
			if ($a->icount == $b->icount) {
				return self::num_sort($a, $b);	
			}
			return ($a->icount > $b->icount) ? +1 : -1;
		}
		static function vc_sort($a, $b, $l = 0)
		{
			if ($a->vc == $b->vc) {
				return ($l == 1) ? -self::num_sort($a, $b) : self::evc_sort($a, $b, 1);	
			}
			return ($a->vc > $b->vc) ? +1 : -1;
		}
		static function evc_sort($a, $b, $l = 0)
		{
			if ($a->evc == $b->evc) {
				return ($l == 1) ? -self::num_sort($a, $b) : self::vc_sort($a, $b, 1);	
			}
			return ($a->evc > $b->evc) ? +1 : -1;
		}
	}

	$bpids = array();
	$npd = array();
	$cpd = array();
	$rpd = array();
	$spd = array();
	$all_pids = array();
	$special_pids = array();
	$gcat_nums = array_fill_keys($arxiv_cats, 1);
	$special_p = 1;
	
	// Pruning to remove duplicates
	$nposts = array();
	$cposts = array();
	$rposts = array();
	$pcats = array();
	$ncats = array();
	$ccats = array();
	$rcats = array();
	$arxivids = array();
	$rarxivids = array();
	$posttypes = array();

	$primary_cats = array();
	foreach ($catvis as $c => $cv) {
		if ($cv == 1) {
			$ci = array_search($arxiv_cat_slugs[$c], $arxiv_cats);
			if ($ci !== false) $primary_cats[] = $arxiv_cnums[$ci];
		}
	}

	foreach ($myquery->posts as $p => $posting) {
		$pcats[] = wp_get_post_categories($posting->ID);
		if (!in_array(8, $pcats[$p])) continue;
		$ai = array_values(array_intersect($pcats[$p], $primary_cats));
		if (count($ai) == 0) continue;
		if (!in_array($ai[0], $primary_cats)) continue;
		$arxivids[] = get_post_custom_values('wpo_arxivid',$posting->ID);
		$nposts[] = $posting;
		$ncats[] = $pcats[$p];
		$posttypes[] = 'new';
	}

	// Two passes for cross-lists: First find 'new' posts from other categories,
	// then search the cross-listings.
	foreach ($myquery->posts as $p => $posting) {
		if (!in_array(8, $pcats[$p])) continue;
		if (in_array(314, $pcats[$p])) continue;
		if (in_array(6, $pcats[$p])) continue;

		$modpcats = get_parent_nums($pcats[$p]);
		$ai = array_intersect($modpcats, $primary_cats);
		//if ($ai !== false) continue;
		if (count($ai) == 0) continue;
		//if (count($ai) > 0 && in_array($ai[0], $primary_cats)) continue;

		$arxivid = get_post_custom_values('wpo_arxivid',$posting->ID);
		if (in_array($arxivid, $arxivids)) continue;
		$arxivids[] = $arxivid;

		$cposts[] = $posting;
		$ccats[] = $pcats[$p];
		$posttypes[] = 'cross-listing';
	}
	foreach ($myquery->posts as $p => $posting) {
		if (!in_array(314, $pcats[$p])) continue;
		if (in_array(8, $pcats[$p])) continue;
		if (in_array(6, $pcats[$p])) continue;

		$modpcats = get_parent_nums($pcats[$p]);
		$ai = array_intersect($modpcats, $primary_cats);
		if (count($ai) == 0) continue;
		//if (count($ai) > 0 && in_array($ai[0], $primary_cats)) continue;
		$arxivid = get_post_custom_values('wpo_arxivid',$posting->ID);
		if (in_array($arxivid, $arxivids)) continue;
		$arxivids[] = $arxivid;
		$cposts[] = $posting;
		$ccats[] = $pcats[$p];
		$posttypes[] = 'cross-listing';
	}
	foreach ($myquery->posts as $p => $posting) {
		if (!in_array(6, $pcats[$p])) continue;
		$ai = array_intersect($pcats[$p], $primary_cats);
		if ($ai === false || count($ai) == 0) continue;
		if (!in_array($ai[0], $primary_cats)) continue;
		$arxivid = get_post_custom_values('wpo_arxivid',$posting->ID);
		if (in_array($arxivid, $rarxivids)) continue;
		array_push($rarxivids, $arxivid);
		$rposts[] = $posting;
		$rcats[] = $pcats[$p];
		$posttypes[] = 'replacement';
	}
	$npost_count = count($nposts);
	$cpost_count = count($cposts);
	$rpost_count = count($rposts);
	$all_count = $npost_count + $cpost_count + $rpost_count;

	$pmds = get_post_meta_data(array_merge($nposts, $cposts, $rposts));
	$allcount = -1;
	foreach ($nposts as $p => $posting) {
		$allcount++;
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->cat_nums = array_fill_keys($arxiv_cats, 0);
		//$pd->cats = $pcats[$allcount];
		$pd->cats = $ncats[$p];
		$pd->pmd = $pmds[$allcount];
		foreach ($arxiv_cats as $i => $ac) {
			if (in_array($arxiv_cnums[$i], $pd->cats)) {
				$pd->primary_cat = $ac;
				$pd->cat_nums[$ac] = $gcat_nums[$ac];
				$gcat_nums[$ac]++;
				break;
			}
		}
		$pd->vc = $vc;
		$pd->evc = $evc;
		$pd->post = $posting;
		$all_pids[] = $posting->ID;
		$co = get_post_meta($posting->ID, 'wpo_comments', true); 
		$jo = get_post_meta($posting->ID, 'wpo_journal', true); 
		if (is_proceeding($co, $jo)) array_push($pd->cats, 'conference-proceeding');
		if (is_submitted($co, $jo)) array_push($pd->cats, 'submitted');
		$npd[] = $pd;
	}
	foreach ($cposts as $p => $posting) {
		$allcount++;
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->cat_nums = array_fill_keys($arxiv_cats, 0);
		$pd->cats = $ccats[$p];
		$pd->pmd = $pmds[$allcount];
		foreach ($arxiv_cats as $i => $ac) {
			if (in_array($arxiv_cnums[$i], $pd->cats)) {
				$pd->primary_cat = $ac;
				$pd->cat_nums[$ac] = $gcat_nums[$ac];
				$gcat_nums[$ac]++;
				break;
			}
		}
		$pd->vc = $vc;
		$pd->evc = $evc;
		$pd->post = $posting;
		$all_pids[] = $posting->ID;
		$co = get_post_meta($posting->ID, 'wpo_comments', true); 
		$jo = get_post_meta($posting->ID, 'wpo_journal', true); 
		if (is_proceeding($co, $jo)) array_push($pd->cats, 'conference-proceeding');
		if (is_submitted($co, $jo)) array_push($pd->cats, 'submitted');
		$cpd[] = $pd;
	}
	foreach ($rposts as $p => $posting) {
		$allcount++;
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->cat_nums = array_fill_keys($arxiv_cats, 0);
		$pd->cats = $rcats[$p];
		$pd->pmd = $pmds[$allcount];
		foreach ($arxiv_cats as $i => $ac) {
			if (in_array($arxiv_cnums[$i], $pd->cats)) {
				$pd->primary_cat = $ac;
				$pd->cat_nums[$ac] = $gcat_nums[$ac];
				$gcat_nums[$ac]++;
				break;
			}
		}
		$pd->vc = $vc;
		$pd->evc = $evc;
		$pd->post = $posting;
		$all_pids[] = $posting->ID;
		$co = get_post_meta($posting->ID, 'wpo_comments', true); 
		$jo = get_post_meta($posting->ID, 'wpo_journal', true); 
		if (is_proceeding($co, $jo)) array_push($pd->cats, 'conference-proceeding');
		if (is_submitted($co, $jo)) array_push($pd->cats, 'submitted');
		$rpd[] = $pd;
	}
	foreach ($squery->posts as $posting) {
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->cat_nums = array_fill_keys($arxiv_cats, 0);
		$pd->vc = $vc;
		$pd->evc = $evc;
		$pd->cats = wp_get_post_categories($posting->ID);
		$pd->cat_nums['astro-ph'] = $special_p; //A bit hacky, just setting astro-ph counter.
		$special_p++;
		$pd->post = $posting;
		$special_pids[] = $posting->ID;
		$co = get_post_meta($posting->ID, 'wpo_comments', true); 
		$jo = get_post_meta($posting->ID, 'wpo_journal', true); 
		if (is_proceeding($co, $jo)) array_push($pd->cats, 'conference-proceeding');
		if (is_submitted($co, $jo)) array_push($pd->cats, 'submitted');
		$spd[] = $pd;
	}

	switch ($sortval) {
		case "votehistory":
			$user_where = "wu.ID = '{$current_user->ID}'";
			break;
		case "ivotehistory":
			$user_where = "vu.affiliation = '{$institution->name}' OR wu.ID = '{$current_user->ID}'";
			break;
		default:
			$user_where = "1=1";
	}
	//$user_where = ($sortval == 'evotehistory') ? "1=1" : "vu.affiliation = '{$institution->name}' OR wu.ID = '{$current_user->ID}'";

	if (is_user_logged_in()) {
		$user_list = '';
		$i = 0;
		$users_list = $wpdb->get_results("SELECT wu.display_name AS name, wu.user_nicename AS nicename,
			vu.affiliation AS affiliation
			FROM {$wpdb->prefix}votes_users AS vu INNER JOIN {$wpdb->prefix}users AS wu ON (wu.ID = vu.user)
			WHERE {$user_where} ORDER BY wu.display_name");
		foreach ($users_list as $user) {
			if ($user->name != '' && $user->affiliation == $institution->name) {
				$user_list .= '<option value="'.$user->nicename.'">'.$user->name.'</option>';
			}
		}
		$user_has_no_votes = false;
	}

	if ($sortval == 'ivotehistory' || $sortval == 'evotehistory' || is_user_logged_in()) {
		#$select_sql_all = "SELECT ctt.term_id AS catid, {$wpdb->posts}.ID, {$wpdb->posts}.post_title AS title, GROUP_CONCAT(tt.term_id) as terms_id FROM {$wpdb->posts}
		#	JOIN {$wpdb->postmeta} AS pm ON ({$wpdb->posts}.ID = pm.post_id AND pm.meta_key = 'wpo_sourcepermalink')
		#	JOIN {$wpdb->term_relationships} AS ctr ON ({$wpdb->posts}.ID = ctr.object_id)
		#	JOIN {$wpdb->term_taxonomy} AS ctt ON (ctr.term_taxonomy_id = ctt.term_taxonomy_id AND ctt.taxonomy = 'category' AND ctt.term_id IN ('8','314','6'))
		#	JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id)
		#	JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author'))
		#	WHERE {$wpdb->posts}.post_status = 'publish' AND {$wpdb->posts}.post_type = 'post' AND {$where_string}
		#	GROUP BY tr.object_id ORDER BY pm.meta_value ASC;";
		//$post_list = implode(",",$wpdb->get_col("SELECT id FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' AND {$where_string}"));
		$post_list = implode(",",$all_pids);
		if ($post_list != '') {
			$select_sql_all = "SELECT ctt.term_id AS catid, p.ID, p.post_title AS title, GROUP_CONCAT(tt.term_id) as terms_id
				FROM {$wpdb->posts} AS p
				JOIN {$wpdb->postmeta} AS pm ON (p.ID = pm.post_id AND pm.meta_key = 'wpo_sourcepermalink')
				JOIN {$wpdb->term_relationships} AS ctr ON (p.ID = ctr.object_id)
				JOIN {$wpdb->term_taxonomy} AS ctt ON (ctr.term_taxonomy_id = ctt.term_taxonomy_id AND ctt.taxonomy = 'category' AND ctt.term_id IN ('8','314','6'))
				JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author'))
				WHERE p.ID IN ({$post_list}) GROUP BY p.ID ORDER BY FIELD(p.ID, {$post_list});";
				//WHERE p.ID IN ({$post_list}) GROUP BY p.ID ORDER BY pm.meta_value ASC;";
			//$post_list = implode(",",$wpdb->get_col("SELECT id FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' AND {$where_string2}"));
			$allposts = $wpdb->get_results($select_sql_all);
		} else {
			$allposts = array();
		}

		$post_list = implode(",",$special_pids);
		if ($post_list != '') {
			$select_sql_spe = "SELECT ctt.term_id AS catid, p.ID, p.post_title AS title, GROUP_CONCAT(tt.term_id) as terms_id
				FROM {$wpdb->posts} AS p
				JOIN {$wpdb->postmeta} AS pm ON (p.ID = pm.post_id AND pm.meta_key = 'wpo_sourcepermalink')
				JOIN {$wpdb->term_relationships} AS ctr ON (p.ID = ctr.object_id)
				JOIN {$wpdb->term_taxonomy} AS ctt ON (ctr.term_taxonomy_id = ctt.term_taxonomy_id AND ctt.taxonomy = 'category' AND ctt.term_id = '18011')
				JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author'))
				WHERE p.ID IN ({$post_list}) GROUP BY p.ID ORDER BY FIELD(p.ID, {$post_list});";
			$sposts = $wpdb->get_results($select_sql_spe);
		} else {
			$sposts = array();
		}

		$nposts = array();
		$cposts = array();
		$rposts = array();
		foreach ($posttypes as $p => $pt) {
			switch ($pt) {
				case 'new':
					$nposts[] = $allposts[$p];
					break;
				case 'cross-listing':
					$cposts[] = $allposts[$p];
					break;
				case 'replacement':
					$rposts[] = $allposts[$p];
					break;
			}
		}

		//$nposts = $wpdb->get_results("
		//	{$select_sql}
		//	AND pm.meta_key = 'wpo_sourcepermalink'
		//	AND ctt.taxonomy = 'category' AND ctt.term_id = '8' 
		//	{$where_string}
		//	{$group_sql}");
		//$cposts = $wpdb->get_results("
		//	{$select_sql}
		//	AND pm.meta_key = 'wpo_sourcepermalink'
		//	AND ctt.taxonomy = 'category' AND ctt.term_id = '314' 
		//	{$where_string}
		//	{$group_sql}");
		//$rposts = $wpdb->get_results("
		//	{$select_sql}
		//	AND pm.meta_key = 'wpo_sourcepermalink'
		//	AND ctt.taxonomy = 'category' AND ctt.term_id = '6' 
		//	{$where_string}
		//	{$group_sql}");

		if ($sortval == 'votehistory' || $sortval == 'ivotehistory' || $sortval == 'evotehistory') {
			$users = $wpdb->get_results("SELECT wu.ID as ID, vu.votes AS votes, vu.sinks AS sinks
				FROM {$wpdb->prefix}votes_users AS vu INNER JOIN {$wpdb->prefix}users AS wu ON (wu.ID = vu.user)
				WHERE (({$user_where}) AND (vu.votes != '' OR vu.sinks != ''))
				ORDER BY (LENGTH(vu.votes) - LENGTH(REPLACE(vu.votes, ',', ''))) DESC LIMIT 100");

			foreach ($users as $user) {
				$is_cur = $current_user->ID == $user->ID;
				if (is_user_logged_in() && $is_cur) {
					if ($user->votes == '' && $user->sinks == '') {
						$user_has_no_votes = true;
					}
					SetRecommend($current_user->ID);
				}

				if ($sortval == 'ivotehistory' && !$is_cur) {
					$timelimit = "AND post_date > '" . gmdate('Y-m-d H:i:s', gmdate('U') - 30*86400) . "' ";
				} elseif ($sortval == 'evotehistory' && !$is_cur) {
					$timelimit = "AND post_date > '" . gmdate('Y-m-d H:i:s', gmdate('U') - 14*86400) . "' ";
				} else {
					$timelimit = "AND post_date > '" . gmdate('Y-m-d H:i:s', gmdate('U') - 365*86400) . "' ";
				}

				if (!$user_has_no_votes) {
					$bannedtags = '"'.implode('", "', explode(",", $wpdb->get_var("SELECT bannedtags FROM ".$wpdb->prefix."votes_recommend WHERE user='".$user->ID."'"))).'"';
					$banstr = ($bannedtags == '""' || !$is_cur || $sortval == 'ivotehistory' || $sortval == 'evotehistory') ? '' : "AND t.name NOT IN ({$bannedtags}) ";
					$tags = array();
					$stags = array();
					$uweight = 0;
					if ($user->votes != '') {
						$vote_list = implode(",",$wpdb->get_col("SELECT id FROM {$wpdb->posts} WHERE id IN ({$user->votes}) AND post_status = 'publish' AND post_type = 'post' {$timelimit}"));
						if ($vote_list != '') {
							$tags = $wpdb->get_results("SELECT t.name as name, tt.term_id as ID, COUNT(tt.term_id) AS counter
								FROM {$wpdb->posts} AS p
								INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
								INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author'))
								INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id {$banstr})
								WHERE p.ID IN ({$vote_list}) GROUP BY tt.term_id ORDER BY counter DESC;");
							$uweight += count($tags);
						}
					}
					if ($user->sinks != '') {
						$sink_list = implode(",",$wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE ID IN ({$user->sinks}) AND post_status = 'publish' AND post_type = 'post' {$timelimit}"));
						if ($sink_list != '') {
							$stags = $wpdb->get_results("SELECT t.name as name, tt.term_id as ID, COUNT(tt.term_id) AS counter
								FROM {$wpdb->posts} AS p
								INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
								INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author'))
								INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id {$banstr})
								WHERE p.ID IN ({$sink_list}) GROUP BY tt.term_id ORDER BY counter DESC;");
							$uweight += count($stags);
						}
					}

					$tagids = array();
					$tagcnts = array();
					$stagids = array();
					$stagcnts = array();
					foreach ($tags as $tag) {
						$tagids[] = $tag->ID;
						$tagcnts[] = $tag->counter;
					}
					foreach ($stags as $tag) {
						$stagids[] = $tag->ID;
						$stagcnts[] = $tag->counter;
					}
					if (count($tagids) > 0) $tagids = array_combine($tagids, $tagcnts);
					if (count($stagids) > 0) $stagids = array_combine($stagids, $stagcnts);
					//array_flip($tagids);
					//array_flip($stagids);

					foreach ($nposts as $p => $post) {
						$ptags = explode(",",$post->terms_id);
						$ptags = array_flip($ptags);
						$count = 0;
						$count += array_sum(array_values(array_intersect_key($tagids, $ptags)));
						$count -= array_sum(array_values(array_intersect_key($stagids, $ptags)));
						if ($is_cur) $npd[$p]->count = $count;
						if ($uweight != 0) $npd[$p]->icount += $count/$uweight;
					}
				
					foreach ($cposts as $p => $post) {
						$ptags = explode(",",$post->terms_id);
						$ptags = array_flip($ptags);
						$count = 0;
						$count += array_sum(array_values(array_intersect_key($tagids, $ptags)));
						$count -= array_sum(array_values(array_intersect_key($stagids, $ptags)));
						if ($is_cur) $cpd[$p]->count = $count;
						if ($uweight != 0) $cpd[$p]->icount += $count/$uweight;
					}
				
					foreach ($rposts as $p => $post) {
						$ptags = explode(",",$post->terms_id);
						$ptags = array_flip($ptags);
						$count = 0;
						$count += array_sum(array_values(array_intersect_key($tagids, $ptags)));
						$count -= array_sum(array_values(array_intersect_key($stagids, $ptags)));
						if ($is_cur) $rpd[$p]->count = $count;
						if ($uweight != 0) $rpd[$p]->icount += $count/$uweight;
					}

					foreach ($sposts as $p => $post) {
						$ptags = explode(",",$post->terms_id);
						$ptags = array_flip($ptags);
						$count = 0;
						$count += array_sum(array_values(array_intersect_key($tagids, $ptags)));
						$count -= array_sum(array_values(array_intersect_key($stagids, $ptags)));
						if ($is_cur) $spd[$p]->count = $count;
						if ($uweight != 0) $spd[$p]->icount += $count/$uweight;
					}

					if ($is_cur) {
						$thresh = max(floor(0.25*array_sum(array_splice($tagcnts, 0, min(20, count($tagcnts)-1)))), 1);

						$combpd = array_merge($npd, $spd);

						$show = $wpdb->get_row("SELECT showreplace, showcrosslist FROM ".$wpdb->prefix."votes_recommend WHERE user = '".$user->ID."'");

						if ($show->showcrosslist) $combpd = array_merge($combpd, $cpd);
						if ($show->showreplace) $combpd = array_merge($combpd, $rpd);
						usort($combpd, array('PostData', 'count_sort'));
						foreach (array_reverse($combpd) as $pd) {
							if ($pd->count >= $thresh) {
								$bpids[] = $pd->post->ID;
							}
							if (count($bpids) >= max(5, floor(0.15*count($combpd)))) break;
						}
					}
				}
			}
		}
	}

	if ($sortval == 'votehistory') {
		usort($npd, array('PostData', 'count_sort'));
		usort($cpd, array('PostData', 'count_sort'));
		usort($rpd, array('PostData', 'count_sort'));
		usort($spd, array('PostData', 'count_sort'));
	} elseif ($sortval == 'ivotehistory' || $sortval == 'evotehistory') {
		usort($npd, array('PostData', 'icount_sort'));
		usort($cpd, array('PostData', 'icount_sort'));
		usort($rpd, array('PostData', 'icount_sort'));
		usort($spd, array('PostData', 'icount_sort'));
	} elseif ($sortval == 'alpha') {
		usort($npd, array('PostData', 'alpha_sort'));
		usort($cpd, array('PostData', 'alpha_sort'));
		usort($rpd, array('PostData', 'alpha_sort'));
		usort($spd, array('PostData', 'alpha_sort'));
	} elseif ($sortval == 'vc') {
		usort($npd, array('PostData', 'vc_sort'));
		usort($cpd, array('PostData', 'vc_sort'));
		usort($rpd, array('PostData', 'vc_sort'));
		usort($spd, array('PostData', 'vc_sort'));
	} elseif ($sortval == 'evc') {                                            
		usort($npd, array('PostData', 'evc_sort'));
		usort($cpd, array('PostData', 'evc_sort'));
		usort($rpd, array('PostData', 'evc_sort'));
		usort($spd, array('PostData', 'evc_sort'));
	} elseif ($sortval == 'postnum') {
		usort($npd, array('PostData', 'num_sort'));
		usort($cpd, array('PostData', 'num_sort'));
		usort($rpd, array('PostData', 'num_sort'));
		usort($spd, array('PostData', 'num_sort'));
	} elseif ($sortval == 'random') {
		if (is_user_logged_in()) {
			srand($current_user->ID.date('m/d/Y', $today));
		}
		shuffle($npd);
		shuffle($cpd);
		shuffle($rpd);
		shuffle($spd);
	}
	
	if ($orderval == 'DESC') {
		$npd = array_reverse($npd);
		$cpd = array_reverse($cpd);
		$rpd = array_reverse($rpd);
		$spd = array_reverse($spd);
	}

	$ncont_arr = array_fill_keys($arxiv_cat_abbrv, array());
	$ccont_arr = array_fill_keys($arxiv_cat_abbrv, array());
	$rcont_arr = array_fill_keys($arxiv_cat_abbrv, array());
	$scont_arr = array_fill_keys($arxiv_cat_abbrv, array());
	$npcatsh = (count($npd) == 0) ? array() : array_fill(0, count($npd), 'new');
	$cpcatsh = (count($cpd) == 0) ? array() : array_fill(0, count($cpd), 'cro');
	$rpcatsh = (count($rpd) == 0) ? array() : array_fill(0, count($rpd), 'rep');
	foreach ($npd as $n => $pd) {
		foreach ($pd->cats as $ncat) {
			if (is_numeric($ncat)) {
				$cat = get_category($ncat);
				$cat = $cat->slug;
			} else $cat = $ncat;
			$ci = array_search($cat, $arxiv_cat_slugs);
			if ($ci !== false) {
				array_push($ncont_arr[$arxiv_cat_abbrv[$ci]], 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|'.$arxiv_cat_abbrv[$ci];
			}
		}
	}	
	foreach ($cpd as $n => $pd) {
		foreach ($pd->cats as $ncat) {
			if (is_numeric($ncat)) {
				$cat = get_category($ncat);
				$cat = $cat->slug;
			} else $cat = $ncat;
			$ci = array_search($cat, $arxiv_cat_slugs);
			if ($ci !== false) {
				array_push($ccont_arr[$arxiv_cat_abbrv[$ci]], 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|'.$arxiv_cat_abbrv[$ci];
			}
		}
	}	
	foreach ($rpd as $n => $pd) {
		foreach ($pd->cats as $ncat) {
			if (is_numeric($ncat)) {
				$cat = get_category($ncat);
				$cat = $cat->slug;
			} else $cat = $ncat;
			$ci = array_search($cat, $arxiv_cat_slugs);
			if ($ci !== false) {
				array_push($rcont_arr[$arxiv_cat_abbrv[$ci]], 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|'.$arxiv_cat_abbrv[$ci];
			}
		}
	}	
	//if (count($spcats) > 0) {
	//	$spcatsh = array_fill(0, count($spcats), 'spe');
	//	foreach ($spd as $n => $pd) {
	//		foreach ($pd->cats as $ncat) {
	//			$cat = get_category($ncat);
	//			if ($cat->slug === 'cosmology-extragalactic-astro-ph') {
	//				array_push($scoarr, 'container-'.strval($pd->post->ID));
	//				$spcatsh[$n] .= '|co';
	//			}
	//			if ($cat->slug === 'earth-planetary-astro-ph') {
	//				array_push($separr, 'container-'.strval($pd->post->ID));
	//				$spcatsh[$n] .= '|ep';
	//			}
	//			if ($cat->slug === 'galactic-astro-ph') {
	//				array_push($sgaarr, 'container-'.strval($pd->post->ID));
	//				$spcatsh[$n] .= '|ga';
	//			}
	//			if ($cat->slug === 'high-energy-astro-ph') {
	//				array_push($shearr, 'container-'.strval($pd->post->ID));
	//				$spcatsh[$n] .= '|he';
	//			}
	//			if ($cat->slug === 'instrumentation-methods-astro-ph') {
	//				array_push($simarr, 'container-'.strval($pd->post->ID));
	//				$spcatsh[$n] .= '|im';
	//			}
	//			if ($cat->slug === 'solar-stellar-astro-ph') {
	//				array_push($ssrarr, 'container-'.strval($pd->post->ID));
	//				$spcatsh[$n] .= '|sr';
	//			}
	//		}
	//		if ($pd->proc) {
	//			array_push($scparr, 'container-'.strval($pd->post->ID));
	//			$spcatsh[$n] .= '|cp';
	//		}
	//		if ($pd->submit) {
	//			array_push($rsuarr, 'container-'.strval($pd->post->ID));
	//			$spcatsh[$n] .= '|su';
	//		}
	//	}	
	//}

	$cat_arrs = array_fill_keys($arxiv_cat_abbrv, array());
	foreach ($arxiv_cat_abbrv as $i => $ca) {
		$cat_arrs[$ca] = array_merge($ncont_arr[$ca], $ccont_arr[$ca],
			$rcont_arr[$ca], $scont_arr[$ca]);
	}
	$nunion = array();
	$cunion = array();
	$runion = array();
	$sunion = array();
	$nexcl_cnt = 0;
	$cexcl_cnt = 0;
	$rexcl_cnt = 0;
	$sexcl_cnt = 0;
	foreach ($arxiv_cat_abbrv as $i => $ca) {
		if ($arxiv_cat_kind[$i] != 2) {
			if ($catvis[$i] == 0) $nunion = array_merge($nunion, $ncont_arr[$ca]);
			if ($catvis[$i] == 0) $cunion = array_merge($cunion, $ccont_arr[$ca]);
			if ($catvis[$i] == 0) $runion = array_merge($runion, $rcont_arr[$ca]);
			if ($catvis[$i] == 0) $sunion = array_merge($sunion, $scont_arr[$ca]);
		} else {
			if ($catvis[$i] == 0) $nexcl_cnt += count($ncont_arr[$ca]);
			if ($catvis[$i] == 0) $cexcl_cnt += count($ccont_arr[$ca]);
			if ($catvis[$i] == 0) $rexcl_cnt += count($rcont_arr[$ca]);
			if ($catvis[$i] == 0) $sexcl_cnt += count($scont_arr[$ca]);
		}
	}
	$ncnt = $npost_count - count(array_unique($nunion)) - $nexcl_cnt;
	$ccnt = $cpost_count - count(array_unique($cunion)) - $cexcl_cnt;
	$rcnt = $rpost_count - count(array_unique($runion)) - $rexcl_cnt;
	$scnt = $spost_count - count(array_unique($sunion)) - $sexcl_cnt;
	$sorttime = microtime(true) - $begtime;
	?>
		<div class="boxheadr"><div class="boxheadl"><h2><?php echo $headline;?>'s Postings</h2></div></div>
	<div class="lightpostbody"><div style="padding-top: 11px;">
	<form name="sorttype" id="sorttype" action="" method="POST">
	<div id="sortarea1">
	<span class='loopheadtitle'>Sort:</span>
	<select id="sortdrop" onchange="javascript:changeSort('<?php echo urlencode($today); ?>', '<?php echo $ishome; ?>');">
	<option<?php if ($sortval == 'postnum') echo ' selected'; ?> value="postnum">By post number</option>
	<option<?php if ($sortval == 'alpha') echo ' selected'; ?> value="alpha">Alphabetically</option>
	<option<?php if ($sortval == 'vc') echo ' selected'; ?> value="vc">By <?php echo $institution->name; ?>'s votes</option>
	<option<?php if ($sortval == 'evc') echo ' selected'; ?> value="evc">By everyone's votes</option>
	<option<?php if ($sortval == 'votehistory' && is_user_logged_in()) echo ' selected'; ?> <?php if (!is_user_logged_in() || $user_has_no_votes) echo 'disabled="disabled"'; ?> value="votehistory">Based on your voting history <?php if (!is_user_logged_in() || $user_has_no_votes) echo '(must be logged in)'; ?></option>
	<option<?php if ($sortval == 'ivotehistory') echo ' selected'; ?> value="ivotehistory">Based on <?php echo $institution->name; ?>'s voting history</option>
	<option<?php if ($sortval == 'evotehistory') echo ' selected'; ?> value="evotehistory">Based on everyone's voting history</option>
	<option<?php if ($sortval == 'random') echo ' selected'; ?> value="random">In random order</option>
	</select>
	<?php if ($sortval != 'random') { ?>
	in <select id="orderdrop" onchange="javascript:changeSortOrder('<?php echo urlencode($today); ?>', '<?php echo $ishome; ?>');">
	<option<?php if ($orderval == 'ASC') echo ' selected'; ?> value='ASC'>ascending</option>
	<option<?php if ($orderval == 'DESC') echo ' selected'; ?> value='DESC'>descending</option>
	</select> order. 
	<?php } ?>
    <span class="sorttime">(Listings sorted in <?php echo round($sorttime, 3); ?> seconds.)</span></div> 
	<div class="loadingtext" id="resorting"><img src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif">Resorting posts...</div>
	<div class="loadingtext" id="loading"><img src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif">Loading posts...</div>
	</div></div><div class="darksep"><div></div></div>
	<div class='lightpostbody'><div>
	<div id="sortarea2">
	<span class='loopheadtitle'>Filters:</span> 
	<?php
	foreach ($arxiv_cat_abbrv as $i => $ca) {
		if ($arxiv_cat_kind[$i] == 2) continue;
		//if ($arxiv_cat_kind[$i] == 2 || count($cat_arrs[$ca]) == 0) continue;
		//if (in_array($ca, array('hl'))) continue;
		if ($i == 7) { ?>
			</div></div></div>
			<div class='lightpostbody' style="margin-top: -7px;"><div>
			<div id="sortarea2">
			<span class='loopheadtitle'></span> 
		<?php
		} elseif ($i != 0) {
			if ($arxiv_cat_kind[$i] == 0) {
				echo "<span class='catsep'>|</span>&nbsp;";
			} else {
				echo "<span class='catsep'>&#8942;</span>&nbsp;";
			}
		}
		$checked = ($catvis[array_search($ca, $arxiv_cat_abbrv)]) ? 'checked' : '';
			$toggle = ($ca == 'as') ? "toggleParent(" : "toggleCat(";
		if ($arxiv_cat_kind[$i] == 0) $toggle .= "true, true";
		$toggle .= ")";
		$onchange = "onchange='{$toggle}; ";
		if ($arxiv_cat_kind[$i] != 1) {
			$urltoday = urlencode($today);
			$onchange .= "setTimeout(changeSort(\"{$urltoday}\", \"{$ishome}\"), 200);'";
		}
		$onchange .= "'";
		echo "<div class='filters'><div><input id='{$ca}check' type='checkbox' {$checked} {$onchange}></div>&nbsp;";
		echo "<label for='{$ca}check'><div><img src='{$siteurl}/icon_{$ca}_sm.png'><br>".strtoupper($arxiv_cat_titles[$i])."</div></label></div>";
	}
	?>
	</div></div></div><div class="darksep"><div></div></div>
	<div class="lightpostbody"><div style="height: 22px;">
	<div id="sortarea2"><span class='loopheadtitle'>Show:</span>
	<input id="ticheck" type="checkbox" <?php if (!$showabstracts) echo 'checked';?> onchange="
	toggleabstracts(); ">
	<label for="ticheck"><span style="line-height: 27px;">Titles Only</span></label>
	<?php
	foreach ($arxiv_cat_abbrv as $i => $ca) {
		if ($arxiv_cat_kind[$i] != 2) continue;
		if ($i != 0) echo "<span class='catsep'>|</span>&nbsp;";
		$checked = ($catvis[array_search($ca, $arxiv_cat_abbrv)]) ? 'checked' : '';
		$text = ($ca == 'cp') ? 'Conf. Proceedings' : 'Submitted';
		echo "<input id='{$ca}check' type='checkbox' {$checked} onchange='toggleCat();'>&nbsp;";
		echo "<label for='{$ca}check'>{$text}</label>&nbsp;";
	}
	?>
	</form>
	</div>
	</div></div>

	<?php $show_number = ($sortval == 'random') ? false : true; ?>
	<!--Special Topics-->
	<?php if (count($squery->posts) > 0) { ?>
	<div class="darkpostbody"><div><div class="specialhead">Special topics (<?php echo $squery->post_count; ?>)<span class='viewbutton'><input id="specialbutton" type="submit" value="<?php echo ($showspecial) ? 'Collapse' : 'Expand';?>" onclick="togglespecial()"></span></div></div></div>
	<div id="specialsep" class="lightpostbody sep" style="<?php if ($showspecial) echo 'display: none;';?>"><div></div></div>
	<div id="specialsection" <?php if (!$showspecial) echo 'style="display: none;"';?>>
	<div id="sallhide" class="lightpostbody" style="display: none;"><div>All special topics are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($spd as $c => $pd) { $post = $pd->post;
	   $sep = ($c + 1 < $squery->post_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, '', $sep, $user_list, true, true, false, false, false);
	} ?>
	</div>
	<?php } ?>

	<!--New astro-ph-->
	<?php if ($npost_count > 0) { ?>
	<div class="darkpostbody"><div><div class="newhead">New papers (<span id="nfrac"><?php echo $npost_count; ?></span>)<span class='viewbutton'><input id="newbutton" type="submit" value="<?php echo ($shownew) ? 'Collapse' : 'Expand';?>" onclick="togglenew()"></span></div></div></div>
	<div id="newsep" class="lightpostbody sep" style="<?php if ($shownew) echo 'display: none;';?>"><div></div></div>
	<div id="newsection" <?php if (!$shownew) echo 'style="display: none;"';?>>
	<div id="nallhide" class="lightpostbody" style="display: none;"><div>All new postings are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($npd as $c => $pd) {
	   $sep = ($c + 1 < $npost_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, $npcatsh[$c], $sep, $user_list, false, true, true, $show_number, false); ?>
	<?php } ?>
	</div>
	
	<!--End new astro-ph-->

	<!--Cross-listings-->
	<?php if ($cpost_count > 0) { ?>
	<div class="darkpostbody"><div><div class="crosslisthead">Cross-Listings (<span id="cfrac"><?php echo $cpost_count; ?></span>)<span class='viewbutton'><input id="crossbutton" type="submit" value="<?php echo ($showcro) ? 'Collapse' : 'Expand';?>" onclick="togglecross()"></span></div></div></div>
	<div id="crosssep" class="lightpostbody sep" style="<?php if ($showcro) echo 'display: none;'?>"><div></div></div>
	<div id="crosssection"<?php if (!$showcro) echo 'style="display: none;"'?>>
	<div id="callhide" class="lightpostbody" style="display: none;"><div>All cross-listed postings are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($cpd as $c => $pd) {
	   $sep = ($c + 1 < $cpost_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, $cpcatsh[$c], $sep, $user_list, false, true, true, $show_number, false); ?>
	<?php } ?>
	</div>
	<?php } ?>
	
	<!--End third loop-->

	<!--Fourth loop, for replacements-->
	<?php if ($rpost_count > 0) { ?>
	<div class="darkpostbody"><div><div class="replacehead">Replacements (<span id="rfrac"><?php echo $rpost_count; ?></span>)<span class='viewbutton'><input id="replacebutton" type="submit" value="<?php echo ($showrep) ? 'Collapse' : 'Expand';?>" onclick="togglereplace()"></span></div></div></div>
	<div id="replacesep" class="lightpostbody sep" style="<?php if ($showrep) echo 'display: none;';?>"><div></div></div>
	<div id="replacesection" <?php if (!$showrep) echo 'style="display: none;"';?>>
	<div id="rallhide" class="lightpostbody" style="display: none;"><div>All replacement postings are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($rpd as $c => $pd) {
	   $sep = ($c + 1 < $rpost_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, $rpcatsh[$c], $sep, $user_list, false, true, true, $show_number, false); ?>
	<?php } ?>
	</div>
	<?php } ?>
	<?php } ?>
	<div class="boxfootr"><div class="boxfootl"></div></div>
<?php
}
//print_r(array($all_count, $npost_count, $cpost_count, $rpost_count));
if ($all_count == 0 && (date('m/d/Y', $last_post_time) == date('m/d/Y', time()))) { ?>
   <h2><?php _e("Still Waiting...","arclite"); ?></h2>
   <?php
     date_default_timezone_set('US/Eastern');
     $arxivrss = mktime(20, 30, 0, date('m'), date('d'), date('Y')); 
     date_default_timezone_set($institution->timezone);
	 $arxivstring = date('g:ia', $arxivrss);
	 $arxivlstring = date('g:ia', $arxivrss - 30*60);
   if (date("D", $arxivrss) == "Fri") {
	 $estring = "ArXiv's RSS feed will update on Sunday around {$arxivstring}.";
   } elseif (date("D", $arxivrss) == "Sat") {
	 $estring = "ArXiv's RSS feed will update tomorrow around {$arxivstring}.";
   } else $estring = "ArXiv's RSS feed hasn't updated yet today. It should update around {$arxivstring}."; ?>
   <p class="error"><?php _e($estring." You can still vote for papers from previous days by using the calendar on the right-hand side of the page. Or, if it is between {$arxivlstring} and {$arxivstring}, visit <a href=\"http://arxiv.org/list/astro-ph/new\">astro-ph's new submissions</a>","arclite"); ?>, and don't forget to vote for the papers you want to discuss before <?php echo date("h:i a",strtotime($institution->discussiontime.date(" m/d/Y")) - $institution->closedelay*60); ?>!</p>
   <p>
   <?php get_search_form();
}
?>
