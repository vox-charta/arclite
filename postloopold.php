<?php

include_once("/var/www/html/voxcharta/wp-blog-header.php");
include_once(VoteItUp_Path()."/votingfunctions.php");
global $wpdb, $current_user, $query_string, $today, $reset_time, $prev_coffee, $institution, $schedaffil, $ishome;
get_currentuserinfo();
if (!isset($schedaffil)) $schedaffil = $_COOKIE['schedule_affiliation'];
if (!isset($institution)) $institution = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}votes_institutions WHERE name='{$schedaffil}'");

if (!isset($ishome) || $ishome == null) {
	$ishome = isset($_POST['ishome']) ? $_POST['ishome'] : 0;
}
if ($ishome == null) $ishome = 0;

if ($ishome) {
	$query_string = '';
} elseif (isset($_POST['query'])) {
	$query_string = urldecode($_POST['query']);
}

if (!isset($_POST['order'])) {
	$orderby = '&orderby=meta_value&order=ASC';
} elseif ($_POST['order']) {
	$orderby = '&orderby=meta_value&order='.$_POST['order'];
}

if ($ishome) {
	$last_post_time = time();
} else {
	$query = new WP_Query($query_string.'&category_name=new&meta_key=wpo_sourcepermalink'.$orderby);
	$last_post_time = get_the_time('U', $query->post->ID);
}
$last_post_date = date('l, F j, Y', $last_post_time);
$today = $last_post_time;
$reset_time = strtotime($institution->resettime.date(" m/d/Y", $today));
$yesterday = $reset_time - 86400;
$next_offset = AgendaOffset('next', 'an');
$club_check = $agenda_info;
$next_coffee = AgendaOffset('next', 'co');
if ($today > $reset_time) {
$where_string = " AND post_date > '" . date('Y-m-d H:i:s', $reset_time) . "' AND post_date < '".date('Y-m-d H:i:s', $reset_time + $next_coffee*86400)."'";
} else {
$where_string = " AND post_date > '" . date('Y-m-d H:i:s', $yesterday) . "' AND post_date < '".date('Y-m-d H:i:s', $today + $next_coffee*86400)."'";
}

function fw($where = '') {
	global $where_string;
	return $where.$where_string;
}

add_filter('posts_where', 'fw');
$query = new WP_Query($query_string.'&category_name=new&meta_key=wpo_sourcepermalink'.$orderby);
$qpids = array();
foreach ($query->posts as $pst) {
	array_push($qpids, $pst->ID);
}
if ($query->have_posts()) {
$query->rewind_posts();
$bpids = array();
if (is_user_logged_in()) {
$c_ID = $current_user->ID;
SetRecommend($c_ID);
$user_raw = $wpdb->get_row("SELECT votes,sinks FROM {$wpdb->prefix}votes_users WHERE user='{$c_ID}' LIMIT 1;");

if (strlen($user_raw->votes) != '' || strlen($user_raw->sinks != '')) {
	$bannedtags = '"'.implode('", "', explode(",", $wpdb->get_var("SELECT bannedtags FROM ".$wpdb->prefix."votes_recommend WHERE user='".$c_ID."'"))).'"';
	$banstr = ($bannedtags == '""') ? '' : "AND name NOT IN ({$bannedtags})";
	$showreplace = $wpdb->get_var("SELECT showreplace FROM ".$wpdb->prefix."votes_recommend WHERE user='".$c_ID."'");
	$showcrosslist = $wpdb->get_var("SELECT showcrosslist FROM ".$wpdb->prefix."votes_recommend WHERE user='".$c_ID."'");
	$tag_list = '"';
	if ($user_raw->votes != '') {
		$tags = $wpdb->get_results("SELECT t.name as name, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
			INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
			WHERE (tt.taxonomy = 'post_tag' AND p.ID IN (".$user_raw->votes."))
			{$banstr}
			AND p.post_status = 'publish'
			AND p.post_type = 'post'
			GROUP BY tt.term_id
			ORDER BY counter DESC;");
		$authors = $wpdb->get_results("SELECT t.name as name, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
			INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
			WHERE (tt.taxonomy = 'post_author' AND p.ID IN (".$user_raw->votes."))
			{$banstr}
			AND p.post_status = 'publish'
			AND p.post_type = 'post'
			GROUP BY tt.term_id
			ORDER BY counter DESC;");
		foreach ($tags as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
		foreach ($authors as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
	}
	if ($user_raw->sinks != '') {
		$stags = $wpdb->get_results("SELECT t.name as name, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
			INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
			WHERE (tt.taxonomy = 'post_tag' AND p.ID IN (".$user_raw->sinks."))
			{$banstr}
			AND p.post_status = 'publish'
			AND p.post_type = 'post'
			GROUP BY tt.term_id
			ORDER BY counter DESC;");
		$sauthors = $wpdb->get_results("SELECT t.name as name, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
			INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
			WHERE (tt.taxonomy = 'post_author' AND p.ID IN (".$user_raw->sinks."))
			{$banstr}
			AND p.post_status = 'publish'
			AND p.post_type = 'post'
			GROUP BY tt.term_id
			ORDER BY counter DESC;");
		foreach ($stags as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
		foreach ($sauthors as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
	}
	$thresh = floor(1.5*$tags[0]->counter);
	$tag_list = substr($tag_list, 0, strlen($tag_list) - 3);

	// Include category
	$include_cat_sql = '';
	$inner_cat_sql = '';
	$include_cat = '8';
	//if ($showreplace) $include_cat .= ',6';
	//if ($showcrosslist) $include_cat .=  ',314';
	if ($include_cat != '') {
		$include_cat = (array) explode(',', $include_cat);
		$include_cat = array_unique($include_cat);
		foreach ( $include_cat as $value ) {
			$value = (int) $value;
			if( $value > 0 ) {
				$sql_cat_in .= '"'.$value.'", ';
			}
		}
		$sql_cat_in = substr($sql_cat_in, 0, strlen($sql_cat_in) - 2);
		$include_cat_sql = " AND (ctt.taxonomy = 'category' AND ctt.term_id IN ({$sql_cat_in})) ";
		$inner_cat_sql = " INNER JOIN {$wpdb->term_relationships} AS ctr ON (p.ID = ctr.object_id) ";
		$inner_cat_sql .= " INNER JOIN {$wpdb->term_taxonomy} AS ctt ON (ctr.term_taxonomy_id = ctt.term_taxonomy_id) ";
	}

	$allposts = $wpdb->get_results("SELECT p.ID, p.post_title AS title, GROUP_CONCAT(tt.term_id) as terms_id FROM {$wpdb->posts} AS p
		INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
		INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
		JOIN {$wpdb->postmeta} AS pm ON (p.ID = pm.post_id)
		{$inner_cat_sql}
		WHERE ((tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author'))
		{$include_cat_sql}
		AND p.post_status = 'publish'
		AND p.post_type = 'post'
		{$where_string}
		GROUP BY tr.object_id
		ORDER BY pm.meta_value ASC;");
	$pcounts = array();
	$pids = array();
	$ptitles = array();
	$pnums = array();
	foreach ($allposts as $p => $post) {
		$p_ID = $post->ID;
		$ptags = explode(",",$post->terms_id);
		$count = 0;
		foreach ($ptags as $ptag) {
			if ($user_raw->votes != '') {
				foreach ($tags as $tag) {
					if ($tag->ID == $ptag) $count += $tag->counter;
				}
				foreach ($authors as $tag) {
					if ($tag->ID == $ptag) $count += $tag->counter;
				}
			}
			if ($user_raw->sinks != '') {
				foreach ($stags as $tag) {
					if ($tag->ID == $ptag) $count -= $tag->counter;
				}
				foreach ($authors as $tag) {
					if ($tag->ID == $ptag) $count -= $tag->counter;
				}
			}
		}
		array_push($ptitles, $post->title);
		array_push($pcounts, $count + 1.0*rand()/getrandmax());
		array_push($pids, $post->ID);
		array_push($pnums, $p + 1);
	}
	$pcountscopy = $pcounts;
	echo $query->request;
		foreach ($pids as $p => $pid) {
			echo $qpids[$p] . ' ' . $pids[$p] . '<br>';
		}
	//array_multisort($qpids, SORT_ASC, $query->posts); 
	if ($_POST['type'] == 'votehistory') {
		array_multisort($pcounts, SORT_DESC, SORT_NUMERIC, $pnums, $query->posts, $pids); 
	} elseif ($_POST['type'] == 'alpha') {
		array_multisort($ptitles, SORT_DESC, SORT_STRING, $pnums, $query->posts, $pids); 
	}
	for ($i = 0; $i < count($allposts); $i++) {
		if ($pcounts[$i] >= $thresh) array_push($bpids, $pids[$i]);
		if (count($bpids) >= 5) break;
	}
	if ($_POST['type'] != 'postnum' && $_POST['order'] == 'ASC') {

		$query->posts = array_reverse($query->posts);
		$pnums = array_reverse($pnums);
	}
}} else {
	if ($_POST['type'] == 'alpha') {
		//$allposts = $wpdb->get_results("SELECT p.ID, p.post_title AS title FROM {$wpdb->posts} AS p
		//	{$inner_cat_sql}
		//	{$include_cat_sql}
		//	AND p.post_status = 'publish'
		//	AND p.post_type = 'post'
		//	{$where_string}
		//	;");
		//$pids = array();
		//$ptitles = array();
		//$pnums = array();
		//foreach ($allposts as $p => $post) {
		//	$p_ID = $post->ID;
		//	array_push($ptitles, $post->title);
		//	array_push($pids, $post->ID);
		//	array_push($pnums, $p + 1);
		//}
		//echo count($query->posts) . ' '. count($ptitles);
		//array_multisort($ptitles, SORT_ASC, SORT_STRING, $query->posts); 
	}
}
?>

<!--New astro-ph-->
<div class="boxheadr"><div class="boxheadl"><h2>Today's Postings</h2></div></div>
<div class="lightpostbodyr"><div class="lightpostbodyl">
<form name="sorttype" id="sorttype" action="" method="POST">
Sort <select id="sortdrop" onchange="
var baseurl = '/wp-content/themes/arclite';
var sortval = document.getElementById('sortdrop').options[document.getElementById('sortdrop').selectedIndex].value;
var orderval = document.getElementById('orderdrop').options[document.getElementById('orderdrop').selectedIndex].value;
if (sortval == 'votehistory') {
	lg_AJAXsort(baseurl, sortval, 'DESC', '<?php echo urlencode($query_string); ?>', <?php echo $ishome; ?>);
} else {
	lg_AJAXsort(baseurl, sortval, orderval, '<?php echo urlencode($query_string); ?>', <?php echo $ishome; ?>);
}
">
<option<?php if (!isset($_POST['type']) || $_POST['type'] == 'postnum') echo ' selected'; ?> value="postnum">by post number</option>
<option<?php if ($_POST['type'] == 'alpha') echo ' selected'; ?> value="alpha">alphabetically</option>
<option<?php if ($_POST['type'] == 'votes') echo ' selected'; ?> disabled="disabled" value="votes">by number of votes</option>
<option<?php if ($_POST['type'] == 'votehistory') echo ' selected'; ?> value="votehistory">based on your voting history</option>
</select> in
<select id="orderdrop" onchange="
var baseurl = '/wp-content/themes/arclite';
lg_AJAXsort(baseurl, document.getElementById('sortdrop').options[document.getElementById('sortdrop').selectedIndex].value,
	document.getElementById('orderdrop').options[document.getElementById('orderdrop').selectedIndex].value,'<?php echo urlencode($query_string); ?>', <?php echo $ishome; ?>);
">
<option<?php if ($_POST['order'] == 'ASC') echo ' selected'; ?> value='ASC'>ascending</option>
<option<?php if ($_POST['order'] == 'DESC') echo ' selected'; ?> value='DESC'>descending</option>
</select> order. <span style="float: right; display: none;" id="resorting"><img src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif"> Resorting posts...</span>
</form>
</div></div>
<div class="darkpostbodyr"><div class="darkpostbodyl"><div class="newhead">New papers (<?php echo $query->post_count; ?>)<span style="float: right; padding-right: 10px; font-size: 12pt;"><input id="newbutton" type="submit" value="Collapse" onclick="togglenew()"></span></div></div></div>
<div id="newsep" class="lightpostbodyr" style="height: 10px; display: none;"><div class="lightpostbodyl"></div></div>
<div id="newsection">
<?php $counter = 0; ?>
<?php while ($query->have_posts()) { $query->the_post(); ?>
<!-- post -->
<?php if ($counter > 0) echo '<div class="darkpostbodyr"><div class="darkpostbodyl" style="height: 5px;"></div></div>'; ?>
<div id="container-<?php the_ID(); ?>" class="lightpostbodyr"><div class="lightpostbodyl">
<div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>
  <div class="post-header">
<?php echo $pnums[$counter] . ' ' . $query->post->ID . '<br>'; ?>
   <h3><?php if (array_search($query->post->ID, $bpids) !== false) { echo '<span style="float: right; vertical-align: middle; margin-top: -0.5em;"><img src="'.get_option("siteurl").'/recommended.png"></span>'; } ?><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link:','arclite'); echo ' '; the_title_attribute(); ?>"><?php echo '['.$pnums[$counter].'] '; the_title(); ?></a></h3>
   <p class="post-date"><?php DisplayVotes($query->post->ID); ?>
   </p>
   <p class="post-author" style="margin-left: 3.5em">
	<div style="margin-top: -2.9em; margin-left: 4.2em; height: 1.7em;">
	  <?php comments_popup_link(__('No Comments', 'arclite'), __('1 Comment', 'arclite'), __('% Comments', 'arclite'), 'comments', __('Comments off', 'arclite')); ?>  <?php edit_post_link(__('Edit','arclite'),' | '); ?>
	  <?php DisplaySuggest($query->post->ID); ?></div>
	  <?php echo get_category_graphics($query->post->ID); ?>
  </div>

  <div class="post-content clearfix">
  <?php if(get_option('arclite_indexposts')=='excerpt') the_excerpt(); else the_content(__('Read the rest of this entry &raquo;', 'arclite')); ?>

  </div>

</div>
<!-- /post -->

<?php $counter++; ?>
</div></div>
<?php } ?>
</div>
<?php $pcounter = $counter; ?>

<!--End new astro-ph-->

<!--Cross-listings-->
<?php $query = new WP_Query(
 'category_name=cross-listings&meta_key=wpo_sourcepermalink'.$orderby); ?>
<div class="darkpostbodyr"><div class="darkpostbodyl"><div class="crosslisthead">Cross-Listings (<?php echo $query->post_count; ?>)<span style="float: right; padding-right: 10px; font-size: 12pt;"><input id="crossbutton" type="submit" value="Collapse" onclick="togglecross()"></span></div></div></div>
<div id="crosssep" class="lightpostbodyr" style="height: 10px; display: none;"><div class="lightpostbodyl"></div></div>
<div id="crosssection">
<?php if ($query->have_posts()) { ?>
<?php while ($query->have_posts()) { $query->the_post(); ?>
<!-- post -->
<?php if ($counter > $pcounter) echo '<div class="darkpostbodyr"><div class="darkpostbodyl" style="height: 5px;"></div></div>'; ?>
<div class="lightpostbodyr"><div class="lightpostbodyl">
<div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>

  <div class="post-header">
   <h3><?php if (array_search($query->post->ID, $bpids) !== false) { echo '<span style="float: right; vertical-align: middle; margin-top: -0.5em;"><img src="'.get_option("siteurl").'/recommended.png"></span>'; } ?><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link:','arclite'); echo ' '; the_title_attribute(); ?>"><?php echo '['.$counter.'] '; the_title(); ?></a></h3>
   <p class="post-date"><?php DisplayVotes($query->post->ID); ?>
   </p>
   <p class="post-author" style="margin-left: 3.5em">
	<div style="margin-top: -2.9em; margin-left: 4.2em; height: 1.7em;">
	  <?php comments_popup_link(__('No Comments', 'arclite'), __('1 Comment', 'arclite'), __('% Comments', 'arclite'), 'comments', __('Comments off', 'arclite')); ?>  <?php edit_post_link(__('Edit','arclite'),' | '); ?>
	  <?php DisplaySuggest($query->post->ID); ?></div>
	  <?php echo get_category_graphics($query->post->ID); ?>
  </div>

  <div class="post-content clearfix">
  <?php if(get_option('arclite_indexposts')=='excerpt') the_excerpt(); else the_content(__('Read the rest of this entry &raquo;', 'arclite')); ?>
  </div>

</div>
<!-- /post -->

<?php $counter++; ?>
</div></div>
<?php } ?>
<?php $pcounter = $counter;?>
</div>
<?php } ?>

<!--End third loop-->

<!--Fourth loop, for replacements-->
<?php $query = new WP_Query('category_name=replacements&meta_key=wpo_sourcepermalink'.$orderby); ?>
<div class="darkpostbodyr"><div class="darkpostbodyl"><div class="replacehead">Replacements (<?php echo $query->post_count; ?>)<span style="float: right; padding-right: 10px; font-size: 12pt;"><input id="replacebutton" type="submit" value="Collapse" onclick="togglereplace()"></span></div></div></div>
<div id="replacesep" class="lightpostbodyr" style="height: 10px; display: none;"><div class="lightpostbodyl"></div></div>
<div id="replacesection">
<?php if ($query->have_posts()) { ?>
<?php while ($query->have_posts()) { $query->the_post(); ?>
<!-- post -->
<?php if ($counter > $pcounter) echo '<div class="darkpostbodyr"><div class="darkpostbodyl" style="height: 5px;"></div></div>'; ?>
<div class="lightpostbodyr"><div class="lightpostbodyl">
<div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>

  <div class="post-header">
   <h3><?php if (array_search($query->post->ID, $bpids) !== false) { echo '<span style="float: right; vertical-align: middle; margin-top: -0.5em;"><img src="'.get_option("siteurl").'/recommended.png"></span>'; } ?><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link:','arclite'); echo ' '; the_title_attribute(); ?>"><?php echo '['.$counter.'] '; the_title(); ?></a></h3>
   <p class="post-date"><?php DisplayVotes($query->post->ID); ?>
   </p>
   <p class="post-author" style="margin-left: 3.5em">
	<div style="margin-top: -2.9em; margin-left: 4.2em; height: 1.7em;">
	  <?php comments_popup_link(__('No Comments', 'arclite'), __('1 Comment', 'arclite'), __('% Comments', 'arclite'), 'comments', __('Comments off', 'arclite')); ?>  <?php edit_post_link(__('Edit','arclite'),' | '); ?>
	  <?php DisplaySuggest($query->post->ID); ?></div>
	  <?php echo get_category_graphics($query->post->ID); ?>
  </div>

  <div class="post-content clearfix">
  <?php if(get_option('arclite_indexposts')=='excerpt') the_excerpt(); else the_content(__('Read the rest of this entry &raquo;', 'arclite')); ?>
  </div>

</div>
<!-- /post -->

<?php $counter++; ?>
</div></div>
<?php }} ?>
</div>
<div class="boxfootr"><div class="boxfootl"></div></div>
<?php } elseif (date('m/d/Y', $last_post_time) == date('m/d/Y', time()) && $last_vote_time < strtotime($affiliation->resettime.date(" m/d/Y", time()))) { ?>
   <h2><?php _e("Still Waiting...","arclite"); ?></h2>
   <?php $arxivrss = gmmktime(0, 30, 0, date('m'), date('d'), date('Y')); 
	 $arxivstring = date('g:ia', $arxivrss);
	 $arxivlstring = date('g:ia', $arxivrss - 30*60);
   if  (date("D") == "Fri") {
	 $estring = "Astro-ph's RSS feed will update on Sunday around {$arxivstring}.";
   } elseif (date("D") == "Sat") {
	 $estring = "Astro-ph's RSS feed will update tomorrow around {$arxivstring}.";
   } else $estring = "Astro-ph's RSS feed hasn't updated yet today. It should update around {$arxivstring}."; ?>
   <p class="error"><?php _e($estring." You can still vote for papers from previous days by using the calendar on the right-hand side of the page. Or, if it is between {$arxivlstring} and {$arxivstring}, visit <a href=\"http://arxiv.org/list/astro-ph/new\">astro-ph's new submissions</a>","arclite"); ?>, and don't forget to vote for the papers you want to discuss before <?php echo date("h:i a",strtotime($institution->discussiontime.date(" m/d/Y"))-600); ?>!</p>
   <p>
   <?php get_search_form();
} ?>
