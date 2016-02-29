<?php

include_once("/var/www/html/voxcharta/wp-blog-header.php");
//include_once(VoteItUp_Path()."/votingfunctions.php");
header('HTTP/1.1 200 OK'); //Wordpress sends a 404 for some reason, override this (added by JFG).
global $wpdb, $current_user, $today, $reset_time, $prev_coffee, $institution, $schedaffil, $ishome;
global $where_string, $where_string2, $page_uri, $post, $postloop, $arxiv_cats, $arxiv_cnums;
$postloop = true;
get_currentuserinfo();
$begtime = microtime(true);
if (!isset($_COOKIE['catvis']) || count(explode(',', $_COOKIE['catvis'])) != 9) {
	$catvis = array(1,1,1,1,1,1,0,1,1);
} else {
	$catvis = explode(",", $_COOKIE['catvis']);
	//if (count($catvis) != 9) $catvis = array(1,1,1,1,1,1,0,1,1);
}
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

$arxiv_cats = array('astro-ph','gr-qc','hep-ph','hep-th','hep-lat','hep-ex');
$arxiv_cnums = array(7, 748669, 7, 7, 7, 7); //Need to change numbers to match new categories -- JFG

//date_default_timezone_set('GMT');
//$reset_time = strtotime('23:59', $today);
//date_default_timezone_set($institution->timezone);
date_default_timezone_set('US/Pacific');
$reset_time = strtotime(date('Y-m-d ', $today) . '17:00');
$last_post_date = date('l, F j, Y', $last_post_time);
$yesterday = $reset_time - 86400;
if ($today > $reset_time) {
	$where_string = "post_date > '" . date('Y-m-d H:i:s', $reset_time) . "' AND post_date < '".date('Y-m-d H:i:s', $reset_time + 86400)."'";
} else {
	$where_string = "post_date > '" . date('Y-m-d H:i:s', $yesterday) . "' AND post_date < '".date('Y-m-d H:i:s', $today)."'";
}
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
$nquery = new WP_Query('category_name=new'.$orderby);
$cquery = new WP_Query('category_name=cross-listings'.$orderby);
$rquery = new WP_Query('category_name=replacements'.$orderby);
remove_filter('posts_where', 'fw');
add_filter('posts_where', 'fw2');
$squery = new WP_Query('category_name=special-topics&orderby=post_date&order=ASC');
remove_filter('posts_where', 'fw2');

$is_processing = $wpdb->get_var("SELECT processing FROM {$wpdb->prefix}wpo_campaign WHERE title = 'astro-ph'");
if ($ishome && $is_processing) {
	?><h2><?php _e("Processing today's listings...","arclite"); ?></h2>
	<p class="error"><?php _e("Vox Charta is currently processing today's new astro-ph listings, and the list of posting shown below is likely incomplete. The day's full listing will be all available shortly! If you see this message for more than an hour, please notify the site administrator.","arclite");?></p><?php
} elseif (!$nquery->have_posts() && (date('m/d/Y', $last_post_time) == date('m/d/Y', time()))) { ?>
   <h2><?php _e("Still Waiting...","arclite"); ?></h2>
   <?php
     date_default_timezone_set('US/Eastern');
     $arxivrss = mktime(20, 30, 0, date('m'), date('d'), date('Y')); 
     date_default_timezone_set($institution->timezone);
	 $arxivstring = date('g:ia', $arxivrss);
	 $arxivlstring = date('g:ia', $arxivrss - 30*60);
   if (date("D", $arxivrss) == "Fri") {
	 $estring = "Astro-ph's RSS feed will update on Sunday around {$arxivstring}.";
   } elseif (date("D", $arxivrss) == "Sat") {
	 $estring = "Astro-ph's RSS feed will update tomorrow around {$arxivstring}.";
   } else $estring = "Astro-ph's RSS feed hasn't updated yet today. It should update around {$arxivstring}."; ?>
   <p class="error"><?php _e($estring." You can still vote for papers from previous days by using the calendar on the right-hand side of the page. Or, if it is between {$arxivlstring} and {$arxivstring}, visit <a href=\"http://arxiv.org/list/astro-ph/new\">astro-ph's new submissions</a>","arclite"); ?>, and don't forget to vote for the papers you want to discuss before <?php echo date("h:i a",strtotime($institution->discussiontime.date(" m/d/Y")) - $institution->closedelay*60); ?>!</p>
   <p>
   <?php get_search_form();
}
if ($nquery->have_posts() || $squery->have_posts()) {
	?>
	<div class="boxheadr"><div class="boxheadl"><h2>Today's Postings</h2></div></div>
	<?php
	class PostData
	{
		public $count, $icount, $cat_nums, $vc, $evc,
		       $primary_cat, $cats, $post, $proc, $submit;

		function __construct() {
			global $arxiv_cats;
			$icount = 0;
			$cat_nums = array_fill(1, count($arxiv_cats), 0);
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
			foreach ($a->cat_nums as $i => $cn) { 
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
	
	foreach ($nquery->posts as $posting) {
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->cats = wp_get_post_categories($posting->ID);
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
		$pd->proc = is_proceeding($co, $jo);
		$pd->submit = is_submitted($co, $jo);
		$npd[] = $pd;
	}
	foreach ($cquery->posts as $n => $posting) {
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->cats = wp_get_post_categories($posting->ID);
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
		$pd->proc = is_proceeding($co, $jo);
		$pd->submit = is_submitted($co, $jo);
		$cpd[] = $pd;
	}
	foreach ($rquery->posts as $posting) {
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->cats = wp_get_post_categories($posting->ID);
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
		$pd->proc = is_proceeding($co, $jo);
		$pd->submit = is_submitted($co, $jo);
		$rpd[] = $pd;
	}
	foreach ($squery->posts as $posting) {
		GetVoteCounts($posting->ID, $vc, $evc);
		$pd = new PostData();
		$pd->vc = $vc;
		$pd->evc = $evc;
		$pd->cats = wp_get_post_categories($posting->ID);
		$pd->cat_nums['astro-ph'] = $special_p; //A bit hacky, just setting astro-ph counter.
		$special_p++;
		$pd->post = $posting;
		$special_pids[] = $posting->ID;
		$co = get_post_meta($posting->ID, 'wpo_comments', true); 
		$jo = get_post_meta($posting->ID, 'wpo_journal', true); 
		$pd->proc = is_proceeding($co, $jo);
		$pd->submit = is_submitted($co, $jo);
		$spd[] = $pd;
	}

	$user_where = ($sortval == 'evotehistory') ? "1=1" : "vu.affiliation = '{$institution->name}' OR wu.ID = '{$current_user->ID}'";
	$users = $wpdb->get_results("SELECT wu.ID as ID, wu.display_name AS name, wu.user_nicename AS nicename,
		vu.votes AS votes, vu.sinks AS sinks, vu.affiliation AS affiliation
		FROM {$wpdb->prefix}votes_users AS vu INNER JOIN {$wpdb->prefix}users AS wu ON (wu.ID = vu.user)
		WHERE {$user_where} ORDER BY wu.display_name");

	if (is_user_logged_in()) {
		$user_list = '';
		$i = 0;
		foreach ($users as $user) {
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
				WHERE p.ID IN ({$post_list}) GROUP BY pm.post_id ORDER BY pm.meta_value ASC;";
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
				WHERE p.ID IN ({$post_list}) GROUP BY p.ID ORDER BY p.post_date;";
			$sposts = $wpdb->get_results($select_sql_spe);
		} else {
			$sposts = array();
		}

		$nposts = array();
		$cposts = array();
		$rposts = array();
		foreach ($allposts as $allpost) {
			switch ($allpost->catid) {
				case '8':
					$nposts[] = $allpost;
					break;
				case '314':
					$cposts[] = $allpost;
					break;
				case '6':
					$rposts[] = $allpost;
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

		foreach ($users as $user) {
			$is_cur = $current_user->ID == $user->ID;
			if ($sortval != 'ivotehistory' && $sortval != 'evotehistory' && !$is_cur) continue;
			if (is_user_logged_in() && $is_cur) {
				if ($user->votes == '' && $user->sinks == '') {
					$user_has_no_votes = true;
				}
				SetRecommend($current_user->ID);
			}

			if ($sortval == 'ivotehistory' && !$is_cur) {
				$timelimit = "AND post_date > '" . gmdate('Y-m-d H:i:s', gmdate('U') - 60*86400) . "' ";
			} elseif ($sortval == 'evotehistory' && !$is_cur) {
				$timelimit = "AND post_date > '" . gmdate('Y-m-d H:i:s', gmdate('U') - 14*86400) . "' ";
			} else {
				$timelimit = ' ';
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
	}
	
	if ($orderval == 'DESC') {
		$npd = array_reverse($npd);
		$cpd = array_reverse($cpd);
		$rpd = array_reverse($rpd);
		$spd = array_reverse($spd);
	}

	$nasarr = array();
	$ncoarr = array();
	$neparr = array();
	$ngaarr = array();
	$nhearr = array();
	$nimarr = array();
	$nsrarr = array();
	$ngqarr = array();
	$ncparr = array();
	$nsuarr = array();
	$casarr = array();
	$ccoarr = array();
	$ceparr = array();
	$cgaarr = array();
	$chearr = array();
	$cimarr = array();
	$csrarr = array();
	$cgqarr = array();
	$ccparr = array();
	$csuarr = array();
	$rasarr = array();
	$rcoarr = array();
	$reparr = array();
	$rgaarr = array();
	$rhearr = array();
	$rimarr = array();
	$rsrarr = array();
	$rgqarr = array();
	$rcparr = array();
	$rsuarr = array();
	$scoarr = array();
	$separr = array();
	$sgaarr = array();
	$shearr = array();
	$simarr = array();
	$ssrarr = array();
	$sgqarr = array();
	$scparr = array();
	$ssuarr = array();
	$npcatsh = (count($npd) == 0) ? array() : array_fill(0, count($npd), 'new');
	$cpcatsh = (count($cpd) == 0) ? array() : array_fill(0, count($cpd), 'cro');
	$rpcatsh = (count($rpd) == 0) ? array() : array_fill(0, count($rpd), 'rep');
	foreach ($npd as $n => $pd) {
		foreach ($pd->cats as $ncat) {
			$cat = get_category($ncat);
			if ($cat->slug === 'astro-ph') {
				array_push($nasarr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|as';
			}
			if ($cat->slug === 'cosmology-extragalactic-astro-ph') {
				array_push($ncoarr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|co';
			}
			if ($cat->slug === 'earth-planetary-astro-ph') {
				array_push($neparr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|ep';
			}
			if ($cat->slug === 'galactic-astro-ph') {
				array_push($ngaarr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|ga';
			}
			if ($cat->slug === 'high-energy-astro-ph') {
				array_push($nhearr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|he';
			}
			if ($cat->slug === 'instrumentation-methods-astro-ph') {
				array_push($nimarr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|im';
			}
			if ($cat->slug === 'solar-stellar-astro-ph') {
				array_push($nsrarr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|sr';
			}
			if ($cat->slug === 'gr-qc') {
				array_push($ngqarr, 'container-'.strval($pd->post->ID));
				$npcatsh[$n] .= '|gq';
			}
		}
		if ($pd->proc) {
			array_push($ncparr, 'container-'.strval($pd->post->ID));
			$npcatsh[$n] .= '|cp';
		}
		if ($pd->submit) {
			array_push($nsuarr, 'container-'.strval($pd->post->ID));
			$npcatsh[$n] .= '|su';
		}
	}	
	foreach ($cpd as $n => $pd) {
		foreach ($pd->cats as $ncat) {
			$cat = get_category($ncat);
			if ($cat->slug === 'astro-ph') {
				array_push($casarr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|as';
			}
			if ($cat->slug === 'cosmology-extragalactic-astro-ph') {
				array_push($ccoarr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|co';
			}
			if ($cat->slug === 'earth-planetary-astro-ph') {
				array_push($ceparr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|ep';
			}
			if ($cat->slug === 'galactic-astro-ph') {
				array_push($cgaarr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|ga';
			}
			if ($cat->slug === 'high-energy-astro-ph') {
				array_push($chearr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|he';
			}
			if ($cat->slug === 'instrumentation-methods-astro-ph') {
				array_push($cimarr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|im';
			}
			if ($cat->slug === 'solar-stellar-astro-ph') {
				array_push($csrarr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|sr';
			}
			if ($cat->slug === 'gr-qc') {
				array_push($cgqarr, 'container-'.strval($pd->post->ID));
				$cpcatsh[$n] .= '|gq';
			}
		}
		if ($pd->proc) {
			array_push($ccparr, 'container-'.strval($pd->post->ID));
			$cpcatsh[$n] .= '|cp';
		}
		if ($pd->submit) {
			array_push($csuarr, 'container-'.strval($pd->post->ID));
			$cpcatsh[$n] .= '|su';
		}
	}	
	foreach ($rpd as $n => $pd) {
		foreach ($pd->cats as $ncat) {
			$cat = get_category($ncat);
			if ($cat->slug === 'astro-ph') {
				array_push($rasarr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|as';
			}
			if ($cat->slug === 'cosmology-extragalactic-astro-ph') {
				array_push($rcoarr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|co';
			}
			if ($cat->slug === 'earth-planetary-astro-ph') {
				array_push($reparr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|ep';
			}
			if ($cat->slug === 'galactic-astro-ph') {
				array_push($rgaarr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|ga';
			}
			if ($cat->slug === 'high-energy-astro-ph') {
				array_push($rhearr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|he';
			}
			if ($cat->slug === 'instrumentation-methods-astro-ph') {
				array_push($rimarr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|im';
			}
			if ($cat->slug === 'solar-stellar-astro-ph') {
				array_push($rsrarr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|sr';
			}
			if ($cat->slug === 'gr-qc') {
				array_push($rgqarr, 'container-'.strval($pd->post->ID));
				$rpcatsh[$n] .= '|gq';
			}
		}
		if ($pd->proc) {
			array_push($rcparr, 'container-'.strval($pd->post->ID));
			$rpcatsh[$n] .= '|cp';
		}
		if ($pd->submit) {
			array_push($rsuarr, 'container-'.strval($pd->post->ID));
			$rpcatsh[$n] .= '|su';
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

	$nunion = array();
	$coarr = array_merge($ncoarr, $rcoarr, $ccoarr, $scoarr);
	$eparr = array_merge($neparr, $reparr, $ceparr, $separr);
	$gaarr = array_merge($ngaarr, $rgaarr, $cgaarr, $sgaarr);
	$hearr = array_merge($nhearr, $rhearr, $chearr, $shearr);
	$imarr = array_merge($nimarr, $rimarr, $cimarr, $simarr);
	$srarr = array_merge($nsrarr, $rsrarr, $csrarr, $ssrarr);
	$gqarr = array_merge($ngqarr, $rgqarr, $cgqarr, $sgqarr);
	if ($catvis[0] == 0) $nunion = array_merge($nunion, $ncoarr);
	if ($catvis[1] == 0) $nunion = array_merge($nunion, $neparr);
	if ($catvis[2] == 0) $nunion = array_merge($nunion, $ngaarr);
	if ($catvis[3] == 0) $nunion = array_merge($nunion, $nhearr);
	if ($catvis[4] == 0) $nunion = array_merge($nunion, $nimarr);
	if ($catvis[5] == 0) $nunion = array_merge($nunion, $nsrarr);
	if ($catvis[6] == 0) $nunion = array_merge($nunion, $ngqarr);
	$ncnt = $nquery->post_count - count(array_unique($nunion))
		- (($catvis[7] == 0) ? count($ncparr) : 0)
		- (($catvis[8] == 0) ? count($nsuarr) : 0);
	$cunion = array();
	if ($catvis[0] == 0) $cunion = array_merge($cunion, $ccoarr);
	if ($catvis[1] == 0) $cunion = array_merge($cunion, $ceparr);
	if ($catvis[2] == 0) $cunion = array_merge($cunion, $cgaarr);
	if ($catvis[3] == 0) $cunion = array_merge($cunion, $chearr);
	if ($catvis[4] == 0) $cunion = array_merge($cunion, $cimarr);
	if ($catvis[5] == 0) $cunion = array_merge($cunion, $csrarr);
	if ($catvis[6] == 0) $cunion = array_merge($cunion, $cgqarr);
	$ccnt = $cquery->post_count - count(array_unique($cunion))
		- (($catvis[7] == 0) ? count($ccparr) : 0)
		- (($catvis[8] == 0) ? count($csuarr) : 0);
	$runion = array();
	if ($catvis[0] == 0) $runion = array_merge($runion, $rcoarr);
	if ($catvis[1] == 0) $runion = array_merge($runion, $reparr);
	if ($catvis[2] == 0) $runion = array_merge($runion, $rgaarr);
	if ($catvis[3] == 0) $runion = array_merge($runion, $rhearr);
	if ($catvis[4] == 0) $runion = array_merge($runion, $rimarr);
	if ($catvis[5] == 0) $runion = array_merge($runion, $rsrarr);
	if ($catvis[6] == 0) $runion = array_merge($runion, $rgqarr);
	$rcnt = $rquery->post_count - count(array_unique($runion))
		- (($catvis[7] == 0) ? count($rcparr) : 0)
		- (($catvis[8] == 0) ? count($rsuarr) : 0);
	$sunion = array();
	if ($catvis[0] == 0) $sunion = array_merge($sunion, $scoarr);
	if ($catvis[1] == 0) $sunion = array_merge($sunion, $separr);
	if ($catvis[2] == 0) $sunion = array_merge($sunion, $sgaarr);
	if ($catvis[3] == 0) $sunion = array_merge($sunion, $shearr);
	if ($catvis[4] == 0) $sunion = array_merge($sunion, $simarr);
	if ($catvis[5] == 0) $sunion = array_merge($sunion, $ssrarr);
	if ($catvis[6] == 0) $sunion = array_merge($sunion, $sgqarr);
	$scnt = $squery->post_count - count(array_unique($sunion))
		- (($catvis[7] == 0) ? count($scparr) : 0)
		- (($catvis[8] == 0) ? count($ssuarr) : 0);
	$sorttime = microtime(true) - $begtime;
	?>
	
	<div class="lightpostbody"><div style="padding-top: 11px; height: 18px;">
	<form name="sorttype" id="sorttype" action="" method="POST">
	<span id="sortarea1" style="display: block; margin-top: -4px; margin-bottom: -4px;">
	<span style="display: inline-block; width: 50px;">Sort:</span>
	<select id="sortdrop" onchange="javascript:changeSort('<?php echo urlencode($today); ?>', '<?php echo $ishome; ?>');">
	<option<?php if ($sortval == 'postnum') echo ' selected'; ?> value="postnum">By post number</option>
	<option<?php if ($sortval == 'alpha') echo ' selected'; ?> value="alpha">Alphabetically</option>
	<option<?php if ($sortval == 'vc') echo ' selected'; ?> value="vc">By <?php echo $institution->name; ?>'s votes</option>
	<option<?php if ($sortval == 'evc') echo ' selected'; ?> value="evc">By everyone's votes</option>
	<option<?php if ($sortval == 'votehistory' && is_user_logged_in()) echo ' selected'; ?> <?php if (!is_user_logged_in() || $user_has_no_votes) echo 'disabled="disabled"'; ?> value="votehistory">Based on your voting history</option>
	<option<?php if ($sortval == 'ivotehistory') echo ' selected'; ?> value="ivotehistory">Based on <?php echo $institution->name; ?>'s voting history</option>
	<option<?php if ($sortval == 'evotehistory') echo ' selected'; ?> value="evotehistory">Based on everyone's voting history</option>
	</select> in
	<select id="orderdrop" onchange="javascript:changeSortOrder('<?php echo urlencode($today); ?>', '<?php echo $ishome; ?>');">
	<option<?php if ($orderval == 'ASC') echo ' selected'; ?> value='ASC'>ascending</option>
	<option<?php if ($orderval == 'DESC') echo ' selected'; ?> value='DESC'>descending</option>
	</select> order. <span class="sorttime">(Listings sorted in <?php echo round($sorttime, 3); ?> seconds.)</span></span> 
	<div class="loadingtext" id="resorting"><img src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif">Resorting posts...</div>
	<div class="loadingtext" id="loading"><img src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif">Loading posts...</div>
	</div></div><div class="darksep"><div></div></div>
	<div class="lightpostbody"><div style="height: 22px;">
	<div id="sortarea2" style="vertical-align: middle; 10px; margin-top: -4px;"><span style="display: inline-block; width: 50px;">Filters:</span> 
	<?php if (count($coarr) != 0) { ?>
	<input style="vertical-align: middle;" id="cocheck" type="checkbox" <?php if ($catvis[0] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="cocheck"><img src="<?php echo get_option("siteurl"); ?>/icon_co.png"></label>
	<span class="catsep">|</span>	
	<?php } if (count($eparr) != 0) { ?>
	<input style="vertical-align: middle;" id="epcheck" type="checkbox" <?php if ($catvis[1] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="epcheck"><img src="<?php echo get_option("siteurl"); ?>/icon_ep.png"></label>
	<span class="catsep">|</span>	
	<?php } if (count($gaarr) != 0) { ?>
	<input style="vertical-align: middle;" id="gacheck" type="checkbox" <?php if ($catvis[2] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="gacheck"><img src="<?php echo get_option("siteurl"); ?>/icon_ga.png"></label>
	<span class="catsep">|</span>	
	<?php } if (count($hearr) != 0) { ?>
	<input style="vertical-align: middle;" id="hecheck" type="checkbox" <?php if ($catvis[3] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="hecheck"><img src="<?php echo get_option("siteurl"); ?>/icon_he.png"></label>
	<span class="catsep">|</span>	
	<?php } if (count($imarr) != 0) { ?>
	<input style="vertical-align: middle;" id="imcheck" type="checkbox" <?php if ($catvis[4] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="imcheck"><img src="<?php echo get_option("siteurl"); ?>/icon_im.png"></label>
	<span class="catsep">|</span>	
	<?php } if (count($srarr) != 0) { ?>
	<input style="vertical-align: middle;" id="srcheck" type="checkbox" <?php if ($catvis[5] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="srcheck"><img src="<?php echo get_option("siteurl"); ?>/icon_sr.png"></label>
	<span class="catsep">|</span>	
	<?php } if (count($gqarr) != 0) { ?>
	<input style="vertical-align: middle;" id="gqcheck" type="checkbox" <?php if ($catvis[6] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="gqcheck"><img src="<?php echo get_option("siteurl"); ?>/icon_gq.png"></label>
	<span class="catsep">|</span>	
	<?php } ?>
	<input style="vertical-align: middle;" id="cpcheck" type="checkbox" <?php if ($catvis[7] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="cpcheck"><span style="line-height: 27px;">Conf. Proceedings</span></label>
	<span class="catsep">|</span>	
	<input style="vertical-align: middle;" id="sucheck" type="checkbox" <?php if ($catvis[8] == 1) echo 'checked';?> onchange="
	toggleCat(); ">
	<label for="sucheck">Submitted</label>
	</div></div></div><div class="darksep"><div></div></div>
	<div class="lightpostbody"><div style="height: 22px;">
	<div id="sortarea2" style="vertical-align: middle; 10px; margin-top: -4px;"><span style="display: inline-block; width: 50px;">Show:</span>
	<input style="vertical-align: middle;" id="abstractscheck" type="checkbox" <?php if (!$showabstracts) echo 'checked';?> onchange="
	toggleabstracts(); ">
	<label for="abstractscheck"><span style="line-height: 27px;">Titles Only</span></label>
	</form>
	</div>
	</div></div>


	<!--Special Topics-->
	<?php if (count($squery->posts) > 0) { ?>
	<div class="darkpostbody"><div><div class="specialhead">Special topics (<?php echo $squery->post_count; ?>)<span style="float: right; padding-right: 10px; font-size: 12pt;"><input id="specialbutton" type="submit" value="<?php echo ($showspecial) ? 'Collapse' : 'Expand';?>" onclick="togglespecial()"></span></div></div></div>
	<div id="specialsep" class="lightpostbody" style="height: 10px; <?php if ($showspecial) echo 'display: none;';?>"><div></div></div>
	<div id="specialsection" <?php if (!$showspecial) echo 'style="display: none;"';?>>
	<div id="sallhide" class="lightpostbody" style="display: none;"><div>All special topics are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($spd as $c => $pd) { $post = $pd->post;
	   $sep = ($c + 1 < $squery->post_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, '', $sep, $user_list, true, true, false, false);
	} ?>
	</div>
	<?php } ?>

	<!--New astro-ph-->
	<?php if ($nquery->have_posts()) { ?>
	<div class="darkpostbody"><div><div class="newhead">New papers (<span id="nfrac"><?php echo $nquery->post_count; ?></span>)<span style="float: right; padding-right: 10px; font-size: 12pt;"><input id="newbutton" type="submit" value="<?php echo ($shownew) ? 'Collapse' : 'Expand';?>" onclick="togglenew()"></span></div></div></div>
	<div id="newsep" class="lightpostbody" style="height: 10px; <?php if ($shownew) echo 'display: none;';?>"><div></div></div>
	<div id="newsection" <?php if (!$shownew) echo 'style="display: none;"';?>>
	<div id="nallhide" class="lightpostbody" style="display: none;"><div>All new postings are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($npd as $c => $pd) {
	   $sep = ($c + 1 < $nquery->post_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, $npcatsh[$c], $sep, $user_list); ?>
	<?php } ?>
	</div>
	
	<!--End new astro-ph-->
	
	<!--Cross-listings-->
	<?php if ($cquery->have_posts()) { ?>
	<div class="darkpostbody"><div><div class="crosslisthead">Cross-Listings (<span id="cfrac"><?php echo $cquery->post_count; ?></span>)<span style="float: right; padding-right: 10px; font-size: 12pt;"><input id="crossbutton" type="submit" value="<?php echo ($showcro) ? 'Collapse' : 'Expand';?>" onclick="togglecross()"></span></div></div></div>
	<div id="crosssep" class="lightpostbody" style="height: 10px; <?php if ($showcro) echo 'display: none;'?>"><div></div></div>
	<div id="crosssection"<?php if (!$showcro) echo 'style="display: none;"'?>>
	<div id="callhide" class="lightpostbody" style="display: none;"><div>All cross-listed postings are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($cpd as $c => $pd) {
	   $sep = ($c + 1 < $cquery->post_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, $cpcatsh[$c], $sep, $user_list); ?>
	<?php } ?>
	</div>
	<?php } ?>
	
	<!--End third loop-->
	
	<!--Fourth loop, for replacements-->
	<?php if ($rquery->have_posts()) { ?>
	<div class="darkpostbody"><div><div class="replacehead">Replacements (<span id="rfrac"><?php echo $rquery->post_count; ?></span>)<span style="float: right; padding-right: 10px; font-size: 12pt;"><input id="replacebutton" type="submit" value="<?php echo ($showrep) ? 'Collapse' : 'Expand';?>" onclick="togglereplace()"></span></div></div></div>
	<div id="replacesep" class="lightpostbody" style="height: 10px; <?php if ($showrep) echo 'display: none;';?>"><div></div></div>
	<div id="replacesection" <?php if (!$showrep) echo 'style="display: none;"';?>>
	<div id="rallhide" class="lightpostbody" style="display: none;"><div>All replacement postings are hidden, check the category boxes at the top of the page to reveal them.</div></div>
	<?php foreach ($rpd as $c => $pd) {
	   $sep = ($c + 1 < $rquery->post_count) ? true : false;
	   DisplayPost($pd->post, $pd, $bpids, $rpcatsh[$c], $sep, $user_list); ?>
	<?php } ?>
	</div>
	<?php }} ?>
	<div class="boxfootr"><div class="boxfootl"></div></div>
<?php } ?>
