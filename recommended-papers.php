<?php /* Arclite/digitalnature

 Template Name: Recommended Papers
 */
 /* Arclite/digitalnature */
 get_header();
?>

<!-- main wrappers -->
<div id="main-wrap1">
 <div id="main-wrap2">

  <!-- main page block -->
  <div id="main" class="block-content">
   <div class="mask-main rightdiv">
    <div class="mask-left">

     <!-- first column -->
     <div id="maindiv" class="col1">
      <div id="main-content">

      <?php set_time_limit(60); ?>
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      	<?php if (!get_post_meta($post->ID, 'hide_title', true)): ?><h2><?php the_title(); ?></h2><?php endif; ?>
		<?php if(!is_user_logged_in()) {echo 'Please <a href="'.get_option("siteurl").'/wp-login.php">log in</a> to view your recommended papers!';} else { ?>
		<?php
		global $wpdb, $current_user;
		$c_ID = $current_user->ID;
		$reminddays = $wpdb->get_var("SELECT reminddays FROM ".$wpdb->prefix."votes_recommend WHERE user='".$c_ID."'");
		?>
		This page shows a list of recommended papers based on your voting record on this website. All papers are tagged with keywords when they are added to the database. By collecting the keywords from all the papers you have voted for and comparing them to recently added papers, we construct a list of papers that you might be interested in. The list only shows papers posted in the last <?php echo $reminddays; ?> days, with papers you have already voted for being excluded from the list.<br><br>To modify your recommendation settings for both this page and the main page, visit the <a href="<?php get_option("siteurl") ?>/wp-admin/options-general.php?page=voteituprecommend">recommendations settings page</a>.<br><br>
		<?php
		SetRecommend($c_ID);
		$user_raw = $wpdb->get_row("SELECT votes,sinks FROM {$wpdb->prefix}votes_users WHERE user='{$c_ID}' LIMIT 1;");

		if (strlen($user_raw->votes) == 0 && strlen($user_raw->sinks == 0)) {
			echo '<b><em>You haven\'t voted on any papers yet!</em></b> This page will be empty until you vote for at least one paper!</b>';
		} else {
		$bannedtags = '"'.implode('", "', explode(",", $wpdb->get_var("SELECT bannedtags FROM ".$wpdb->prefix."votes_recommend WHERE user='".$c_ID."'"))).'"';
		$showreplace = $wpdb->get_var("SELECT showreplace FROM ".$wpdb->prefix."votes_recommend WHERE user='".$c_ID."'");
		$showcrosslist = $wpdb->get_var("SELECT showcrosslist FROM ".$wpdb->prefix."votes_recommend WHERE user='".$c_ID."'");
		if ($user_raw->votes != "") {
			$tags = $wpdb->get_results("SELECT t.name, t.slug, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
				INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
				WHERE (tt.taxonomy = 'post_tag' AND p.ID IN (".$user_raw->votes."))
				AND name NOT IN ({$bannedtags})
				AND p.post_status = 'publish'
				AND p.post_type = 'post'
				GROUP BY tt.term_id
				ORDER BY counter DESC;");
			$authors = $wpdb->get_results("SELECT t.name, t.slug, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
				INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
				WHERE (tt.taxonomy = 'post_author' AND p.ID IN (".$user_raw->votes."))
				AND name NOT IN ({$bannedtags})
				AND p.post_status = 'publish'
				AND p.post_type = 'post'
				GROUP BY tt.term_id
				ORDER BY counter DESC;");
		} else {
			$tags = array();
			$authors = array();
		}
		if ($user_raw->sinks != "") {
			$stags = $wpdb->get_results("SELECT t.name, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
				INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
				WHERE (tt.taxonomy = 'post_tag' AND p.ID IN (".$user_raw->sinks."))
				AND name NOT IN ({$bannedtags})
				AND p.post_status = 'publish'
				AND p.post_type = 'post'
				GROUP BY tt.term_id
				ORDER BY counter DESC;");
			$sauthors = $wpdb->get_results("SELECT t.name, tt.term_id as ID, COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
				INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
				WHERE (tt.taxonomy = 'post_author' AND p.ID IN (".$user_raw->sinks."))
				AND name NOT IN ({$bannedtags})
				AND p.post_status = 'publish'
				AND p.post_type = 'post'
				GROUP BY tt.term_id
				ORDER BY counter DESC;");
		} else {
			$stags = array();
			$sauthors = array();
		}

		echo '<span style="float: left; max-width: 50%; text-align:left;"><h5>Your top ten topics:</h5>';
		echo '<table class="simple">';
		foreach ($tags as $i => $tag) {
			echo '<tr class="simple"><td class="simple">'.strval($i+1).'.</td><td class="simple">['.$tag->counter.' votes]</td><td class="simple"><a href="'.get_option('home').'/tag/'.$tag->slug.'">'.$tag->name.'</a></td></tr>';
			if ($i >= 10) break;
		}
		echo '</table></span>';
		echo '<span style="float: right; max-width: 50%; text-align:left;"><h5>Your top ten authors:</h5>';
		echo '<table class="simple">';
		foreach ($authors as $i => $author) {
			echo '<tr class="simple"><td class="simple">'.strval($i+1).'.</td><td class="simple">['.$author->counter.' votes]</td><td class="simple"><a href="'.get_option('home').'/post_author/'.$author->slug.'">'.$author->name.'</a></tr>';
			if ($i >= 10) break;
		}
		echo '</table></span>';
		echo '<div style="clear: both;"></div>';
		$tag_list = '"';
		foreach ($tags as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
		foreach ($authors as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
		foreach ($stags as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
		foreach ($sauthors as $tag) {
			$tag_list .= $tag->ID . '", "';
		}
		$tag_list = substr($tag_list, 0, strlen($tag_list) - 3);

		// Include category
		$include_cat_sql = '';
		$inner_cat_sql = '';
		$include_cat = '8';
		if ($showreplace) $include_cat .= ',6';
		if ($showcrosslist) $include_cat .=  ',314';
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

		$allposts = $wpdb->get_results("SELECT p.ID, p.post_title, p.guid, GROUP_CONCAT(tt.term_id) as terms_id FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
			INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			{$inner_cat_sql}
			WHERE ((tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author') AND tt.term_id IN ({$tag_list}))
			{$include_cat_sql}
			AND p.ID NOT IN (".$user_raw->votes.")
			AND p.post_status = 'publish'
			AND p.post_type = 'post' AND p.post_date > '".date('Y-m-d H:i:s', time() - $reminddays*86400)."'
			GROUP BY tr.object_id
			ORDER BY tr.object_id;");
		$pcounts = array_fill(0, count($allposts), 0);
		foreach ($allposts as $p => $post) {
			$p_ID = $post->ID;
			$ptags = explode(",",$post->terms_id);
			$count = 0;
			foreach ($ptags as $ptag) {
				$newval = 0;
				foreach ($tags as $tag) {
					if ($tag->ID == $ptag) $newval += $tag->counter;
				}
				foreach ($authors as $tag) {
					if ($tag->ID == $ptag) $newval += $tag->counter;
				}
				foreach ($stags as $tag) {
					if ($tag->ID == $ptag) $newval -= $tag->counter;
				}
				foreach ($sauthors as $tag) {
					if ($tag->ID == $ptag) $newval -= $tag->counter;
				}
				//if ($newval != 0) $newval /= $wpdb->get_var("SELECT COUNT(tt.term_id) AS counter FROM {$wpdb->posts} AS p
				//	INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				//	INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
				//	WHERE ((tt.taxonomy = 'post_tag' OR tt.taxonomy = 'post_author') AND tt.term_id = {$ptag})
				//	AND p.post_status = 'publish'
				//	AND p.post_type = 'post'
				//	GROUP BY tt.term_id;"); 
				$count += $newval;
			}
			$pcounts[$p] = $count;
		}
		array_multisort($pcounts, SORT_DESC, $allposts); 
		echo '<br><h5>Your recommended reading list:</h5>';
		echo '<table class="simple">';
		foreach ($allposts as $i => $post) {
			if ($pcounts[$i] < 1) continue;
			echo '<tr class="simple"><td class="simple">['.round($pcounts[$i]).'&nbsp;points]</td><td class="simple"><a href="'.$post->guid.'">'.$post->post_title.'</a></td></tr>';
		}
		echo '</table>';
		}
		?>
      <?php } endwhile; endif; ?>
       
      </div>
     </div>
     <!-- /first column -->
     <?php get_sidebar(); ?>
     <?php include(TEMPLATEPATH . '/sidebar-secondary.php'); ?>

    </div>
   </div>
   <div class="clear-content"></div>
  </div>
  <!-- /main page block -->

 </div>
</div>
<!-- /main wrappers -->

<?php get_footer(); ?>
