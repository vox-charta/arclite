<?php /* Arclite/digitalnature

 Template Name: Previous Agendas
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

      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      	<div class="boxheadr"><div class="boxheadl"><h2><?php the_title(); ?></h2></div></div>
	  	<?php
		global $wpdb;
		set_time_limit(120);
		$oldest_vote_time = $wpdb->get_var("SELECT lastvotetime FROM  ".$wpdb->prefix."votes WHERE lastvotetime > 1");
		$this_day = GetResetTime(time()) - 1; //Stupid Daylight Savings Time...
		$i = 0;
	  	while (1) {
			if ($this_day < $oldest_vote_time) break;
	  		$sorted = SortVotes($this_day, 'previous');
	  		$offset = max(AgendaOffset('prev', 'co', $this_day), 1);
			$old_day = $this_day;
	  		$this_day = $this_day - $offset*86400;
			$this_day = GetResetTime($this_day) - 1; //Stupid Daylight Savings Time...
			$i++;
			if (AgendaOffset('next', 'co', $old_day) != 0) continue;
			if (count($sorted['pid']) == 0) continue;
			echo '<div class="lightpostbody"><div id="visibleagendaspace" style="padding: 10px 30px 11px;">';
	  		echo '<h5>'.date('l, F j, Y', $old_day).'</h5>';
	  		MostVotedAllTime($sorted);
			echo '</div></div>';
			echo '<div class="darksep"><div></div></div>';
			ob_flush();
			flush();
	  	}
		echo '<div class="boxfootr"><div class="boxfootl"></div></div>';
      endwhile; endif;
	  ?>
       
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
