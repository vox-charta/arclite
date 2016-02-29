<?php
 /* Arclite/digitalnature */
 get_header();
 global $post, $posts;
?>

<!-- main wrappers -->
<div id="main-wrap1">
 <div id="main-wrap2">

  <!-- main page block -->
  <div id="main" class="block-content">
   <div class="mask-main rightdiv">
    <div class="mask-left">

     <!-- first column -->
     <div class="col1">
      <div id="main-content">


	   <?php if (have_posts()) : ?>
		<div class="boxheadr"><div class="boxheadl"><h2>Search Results</h2></div></div>
		<div class="lightpostbody"><div style="height: 5px;"></div></div>
		<div class="darkpostbody"><div style="padding-top: 5px; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
	      <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','arclite')) ?></div>
	      <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','arclite')) ?></div>
          <div class="clear"></div>
		</div></div>

		<?php
		$c = 1;
        while (have_posts()) : the_post();
			DisplayPost($post, true, array(), '', ($c < count($posts)), '', true, true, true, false);
			$c++;
        endwhile;
		?>

		<div class="darkpostbody"><div style="padding-top: 5px; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
	      <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','arclite')) ?></div>
	      <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','arclite')) ?></div>
          <div class="clear"></div>
		</div></div>
		<div class="lightpostbody"><div style="height: 5px;"></div></div>
		<div class="boxfootr"><div class="boxfootl"></div></div>

	   <?php else : ?>
  	    <h2 class="center"><?php _e('No posts found. Try a different search?','arclite'); ?></h2>
        <?php if (function_exists("get_search_form")) get_search_form(); ?>
       <?php endif; ?>

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
