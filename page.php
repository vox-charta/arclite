<?php
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
	    <div class="lightpostbody"><div style="padding: 10px 30px 11px;">
         <?php the_content(__('Read the rest of this page &raquo;', 'arclite')); ?>
         <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
         <?php edit_post_link(__('Edit this entry', 'arclite')); ?>
		</div></div>
		<div class="boxfootr"><div class="boxfootl"></div></div>
       <?php endwhile; endif; ?>

       <?php comments_template(); ?>
       
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
