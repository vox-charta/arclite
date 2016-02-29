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

        <?php /* If this is a tag archive */ if( is_tag() ) { ?>
         <h1 style="font-size: 200%"><?php printf( __('Posts Tagged %s', 'arclite'), single_cat_title('', false) ); ?></h1>
        <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
         <h1 style="font-size: 200%"><?php  printf(__('Archive for %s', 'arclite'), get_the_time(__('F jS, Y','arclite')));  ?></h1>
        <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
         <h1 style="font-size: 200%"><?php  printf(__('%s\'s papers', 'arclite'), get_the_time(__('F Y','arclite')));  ?></h1>
        <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
         <h1 style="font-size: 200%"><?php  printf(__('Archive for %s', 'arclite'), get_the_time(__('Y','arclite')));  ?></h1>
        <?php /* If this is an author archive */ } elseif (is_author()) { ?>
         <h1 style="font-size: 200%"><?php _e('Author Archive','arclite'); ?></h1>
        <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
         <h1 style="font-size: 200%"><?php _e('Blog Archives','arclite'); ?></h1>
        <?php }

        if (is_month() || is_year()) { ?>
        <i>Please select a day in the calendar to the right...</i>
        <?php } elseif (is_category() || is_tag()) { ?>
		<?php if (single_cat_title('', false) == 'Colloquium Club' || single_cat_title('', false) == 'Journal Club' || single_cat_title('', false) == 'Announcements'
				  || single_cat_title('', false) == 'Special Topics' || single_cat_title('', false) == 'Vox Charta Blog') {
			query_posts($query_string);
		} elseif (single_cat_title('', false) == 'Refereed Journals' || single_cat_title('', false) == 'Monthly Notices') {
			query_posts($query_string.'&orderby=date');
		} else {
			query_posts($query_string.'&meta_key=wpo_arxivid&orderby=meta_value_num&order=DESC&posts_per_page=50');
		} ?>
        <?php if (have_posts()) { ?>
		<div class="boxheadr"><div class="boxheadl"><h2>Recent Postings from <?php echo single_cat_title('', false);?></h2></div></div>
        <?php while (have_posts()) { the_post();
		    $show_poster = (get_the_author_meta('id') == 35) ? false : true;
		    $show_votebox = (!$show_poster || single_cat_title('', false) == 'Special Topics') ? true : false;
			DisplayPost($post, true, array(), '', ($c < count($posts)), '', $show_poster, $show_votebox, !$show_poster, false);
        } ?>
		<div class="boxfootr"><div class="boxfootl"></div></div>
		<?php }} elseif (is_day()) {
		  //date_default_timezone_set($institution->timezone);
	  	  $today = GetResetTime(get_the_time('U'));
		  $next_an_offset = AgendaOffset('next', 'an', $today);
	  	  //$today = get_the_time('U');
		  //$reset_time = GetResetTime($today);
	      //$next_offset = AgendaOffset('next', 'an');
		  //$club_check = $agenda_info;
	      //$next_coffee = AgendaOffset('next', 'co');
		  //if ($club_check === 'jc' || $club_check === 'cc') $prev_club = AgendaOffset('prev', $club_check);
          //$club_where_string = " AND post_date > '" . date('Y-m-d H:i:s', $reset_time - $prev_club*86400) . "' AND post_date < '".date('Y-m-d H:i:s', $today + $next_offset*86400)."'";
        ?>

<!--Discussion Agenda-->
	   <div id='mostvoted'>
<?php
	   if ($next_an_offset == 0) {
		   $firstload = true;
		   include_once("wp-content/plugins/vote-it-up/skins/ticker/mostvoted.php");
	   }
?>
	   </div>

<!--Astro-ph postings here-->
		<div id='postloop'><span class="loading"><img height="16" width="16" src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif">Loading this day's posts...</span></div>
		<script type="text/javascript">
		<!--
			lg_AJAXsort('/wp-content/themes/arclite', '<?php echo urlencode($today); ?>', 0, 1, '<?php echo $_SERVER['REQUEST_URI'];?>');
		-->
		</script>

		<?php } ?>

        <div class="navigation" id="pagenavi">
        <?php if(function_exists('wp_pagenavi')) { ?>
         <?php wp_pagenavi() ?>
        <?php } else { ?>
         <?php if (!is_month() && !is_year()) { ?>
         <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','arclite')) ?></div>
         <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','arclite')) ?></div>
         <div class="clear"></div>
        
        <?php }} ?>
        </div>

        <?php $all_query = new WP_Query($query_string); ?>
        <?php if (!$all_query->have_posts()) {
        if ( is_category() ) { // If this is a category archive
        ?> <h2> <?php printf(__("Sorry, but there aren't any posts in the %s category yet.", "arclite"),single_cat_title('',false)); ?> </h2> <?php
        } else if ( is_date() ) { // If this is a date archive
    	?> <h2> <?php _e("Sorry, but there aren't any posts with this date."); ?> </h2> <?php
        } else if ( is_author() ) { // If this is a category archive
    	$userdata = get_userdatabylogin(get_query_var('author_name'));
    	?> <h2> <?php printf(__("Sorry, but there aren't any posts by %s yet.", "arclite"),$userdata->display_name); ?> </h2> <?php
        } else {
    	?> <h2> <?php _e('No posts found.'); ?> </h2> <?php
        }
        get_search_form();
		}
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
