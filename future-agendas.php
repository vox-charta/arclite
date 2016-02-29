<?php /* Arclite/digitalnature

 Template Name: Future Agendas
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
		global $wpdb, $show_everyone;
		$show_everyone = false;
		$this_day = GetResetTime(time()) + 1;
		//$offset = AgendaOffset('next', 'co', $this_day);
		//$this_day = $this_day + $offset*86400;
		$i = 0;
	  	while (1) {
			//if ($this_day > time() + 60*86400) break;
	  		$sorted = SortVotes($this_day, 'previous');
	  		$offset = max(AgendaOffset('next', 'co', $this_day), 1);
			//$old_day = $this_day;
	  		$this_day = $this_day + $offset*86400;
			$this_day = GetResetTime($this_day) + 1;
			$i++;
			if (count($sorted['pid']) == 0) break;
			//if (AgendaOffset('prev', 'co', $old_day) == 0) continue;
			//if (count($sorted['pid']) == 0) continue;
			echo '<div class="lightpostbody"><div style="padding: 10px 30px 11px;">';
	  		echo '<h5>'.date('l, F j, Y', $this_day).'</h5>';
	  		MostVotedAllTime($sorted);
			echo '</div></div>';
			echo '<div class="darksep"><div></div></div>';
			ob_flush();
			flush();
			if ($i > 100) break;
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
