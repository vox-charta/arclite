<?php /* Arclite/digitalnature */ ?>
<?php
get_header();
global $post;
?>

<style>
#page.with-sidebar .mask-main .col1 {
  width:100%;			/* left column width */
  left:28%;			    /* right column width */
}
</style>

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
        <div class="navigation">
          <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
          <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
          <div class="clear"></div>
        </div>
	   <?php
	   $show_meta = (get_the_author_meta('id') == 35) ? true : false;
	   DisplayPost($post, null, array(), '', false, '', true, true, $show_meta, false, true);
       comments_template();
      endwhile; else: ?>
        <p><?php _e("Sorry, no posts matched your criteria.","arclite"); ?></p>
      <?php endif; ?>
      </div>
     </div>
     <!-- /first column -->

    </div>
   </div>
   <div class="clear-content"></div>
  </div>
  <!-- /main page block -->

 </div>
</div>
<!-- /main wrappers -->

<?php get_footer(); ?>
