<?php
 /* Arclite/digitalnature */
 get_header();
 global $firstload;
?>

<script type="text/javascript">
<!--
lg_AJAXsort('/wp-content/themes/arclite', '<?php echo urlencode($today); ?>', <?php echo $ishome; ?>, 1, '<?php echo $_SERVER['REQUEST_URI'];?>');
scripturl = '/wp-content/plugins/vote-it-up/skins/ticker/mostvoted.php';
setInterval('lg_AJAXagenda(scripturl, <?php echo $today; ?>, <?php echo $ishome; ?>)',300000);
-->
</script>

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

<!--Discussion Agenda-->
	   <div id='mostvoted'><?php $firstload = true; include_once("wp-content/plugins/vote-it-up/skins/ticker/mostvoted.php"); ?></div>
<!--Astro-ph postings here-->
	   <div id='postloop'><span style="height: 20px;" id="loading"><img height="16" width="16" style="float: left; margin-left: 10px; margin-right: 5px;" src="<?php echo get_option('siteurl'); ?>/wp-content/themes/arclite/loading.gif">Fetching today's posts...</span></div>

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
