<?php
 /* Arclite/digitalnature */
global $wp_query;
global $today, $institution;
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
     <div class="col1">
      <div id="main-content">

	   <!-- Allows days with no posts to be shown -->
	   <?php global $wp_query, $today, $institution; ?>
	   <?php if ($wp_query->query_vars['day'] > 0) {
	   //if (1) {
		   //$today = GetResetTime(strtotime(sprintf(' %d/%d/%d', $wp_query->query_vars['monthnum'],
		   // 	   $wp_query->query_vars['day'], $wp_query->query_vars['year']))) - 60;
		   //$reset_time = GetResetTime($today);
		   $today = GetResetTime(strtotime(sprintf(' %s-%s-%s', $wp_query->query_vars['year'],
			        str_pad($wp_query->query_vars['monthnum'], 2, '0', STR_PAD_LEFT),
				    str_pad($wp_query->query_vars['day'], 2, '0', STR_PAD_LEFT))));
		   $next_an_offset = AgendaOffset('next', 'an', $today - 1);
		   ?>
		   <h1 style="font-size: 200%"><?php  printf(__('Archive for %s', 'arclite'), date('F jS, Y', $today));  ?></h1>
		   <div id='mostvoted'>
<?php
	       if ($next_an_offset == 0) {
	           $firstload = true;
	           include_once("wp-content/plugins/vote-it-up/skins/ticker/mostvoted.php");
	       }
?>
		   </div>
		   <div id='postloop'>
		   <span class="loading"><img src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif">Loading this day's posts...</span>
		   <script type="text/javascript">
		   <!--
		   lg_AJAXsort('/wp-content/themes/arclite', '<?php echo urlencode($today); ?>', 0, 1, '<?php echo $_SERVER['REQUEST_URI'];?>');
		   -->
		   </script>
		   </div>
	   <?php } else { ?>
		   <h2><?php _e('Page not found (404)','arclite'); ?></h2>

		   <br />
		   <h6><?php _e('Try one of these links:','arclite'); ?></h6>
		   <ul>
			<?php wp_list_pages('title_li='); ?>
		   </ul>
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
