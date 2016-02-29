<?php
 /* Arclite/digitalnature */
 get_header();
 global $post, $posts, $firstload;
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
	    <?php

		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

		$wpdb->get_results("SET SESSION group_concat_max_len = 10000;");
		$author = $wpdb->get_row("SELECT p.ID AS pid,
			GROUP_CONCAT(if (vo.votes ='', null, vo.votes) SEPARATOR ',') AS votes,
			GROUP_CONCAT(if (vo.usersinks ='', null, vo.usersinks) SEPARATOR ',') AS sinks FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->prefix}votes AS vo ON (p.ID = vo.post)
			INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
			INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			INNER JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
			WHERE t.term_id = {$term->term_id}
			AND (vo.votes != '' || vo.usersinks != '');");

		$num_votes = (trim($author->votes) != '') ? count(explode(",",$author->votes)) : 0;
		$num_sinks = (trim($author->sinks) != '') ? count(explode(",",$author->sinks)) : 0;

		$aliases = explode(",", $wpdb->get_var("SELECT GROUP_CONCAT(aliases SEPARATOR ',') FROM wp_votes_authors WHERE LOWER(aliases) LIKE LOWER('%{$term->name}%')"));
		$affiliations =
			$wpdb->get_row("SELECT
				GROUP_CONCAT(affiliations SEPARATOR '|') AS affiliations,
				GROUP_CONCAT(affilmindates SEPARATOR ',') AS affilmindates,
				GROUP_CONCAT(affilmaxdates SEPARATOR ',') AS affilmaxdates
				FROM wp_votes_authors WHERE LOWER(aliases) LIKE LOWER('%{$term->name}%')");
		?>
		<div class="boxheadr"><div class="boxheadl" style="height: 20px;"></h2></div></div>
		<div class="lightpostbody"><div style="padding-bottom:12px;">
		<p><h5>Information about author <?php echo $term->name; ?></h5></p>
		<p>Aliases: <?php echo implode(", ", $aliases); ?></p>
		<p>Known institution affiliations:</p>
		<div style="margin-left: 30px;">
		<?php
		$affilnames = explode('|', $affiliations->affiliations);
		$affilmindates = explode(',', $affiliations->affilmindates);
		$affilmaxdates = explode(',', $affiliations->affilmaxdates);
		foreach ($affilnames as $i => $name) {
			echo '<p>' . $name . ' (' . $affilmindates[$i] . ' - ' . $affilmaxdates[$i] . ')</p>';
		}
		?>
		</div>
		<p><img src="http://voxcharta.org/wp-content/plugins/vote-it-up/thumbup.png" class="voteicon"> <?php echo $num_votes; ?> promotions received.
		<p><img src="http://voxcharta.org/wp-content/plugins/vote-it-up/thumbdown.png" class="voteicon"> <?php echo $num_sinks; ?> demotions received.
		</div></div>
		<div class="boxfootr"><div class="boxfootl"></div></div>
        <?php if (have_posts()) { ?>
		<div class="boxheadr"><div class="boxheadl"><h2>Posts by Author <?php echo single_cat_title('', false); ?></h2></div></div>
        <?php while (have_posts()) { the_post();
		    $show_poster = (get_the_author_meta('id') == 35) ? false : true;
		    $show_votebox = (!$show_poster || single_cat_title('', false) == 'Special Topics') ? true : false;
			DisplayPost($post, true, array(), '', ($c < count($posts)), '', $show_poster, $show_votebox, !$show_poster, false);
        } ?>
		<div class="boxfootr"><div class="boxfootl"></div></div>
		<?php } ?>

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
