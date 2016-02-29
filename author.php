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
     <div class="col1">
      <div id="main-content">

       <?php
       // global $wp_query;
       // $curauth = $wp_query->get_queried_object();

       if(isset($_GET['author_name'])) : $curauth = get_userdatabylogin($author_name); else : $curauth = get_userdata(intval($author)); endif;
       ?>

	   <?php $vote_record = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}votes_users WHERE user='{$curauth->ID}'"); ?>
       <h2><?php echo $curauth->display_name; ?></h2>

       <div class="profile">
        <div style="display: block;">
		<em>Currently affiliated with <?php echo $vote_record->affiliation; ?></em>.
		</div>
	    <div style="float: left;">
		<?php
		$vote_count = ($vote_record->votes == '') ? 0 : count(explode(",",$vote_record->votes));
		$sink_count = ($vote_record->sinks == '') ? 0 : count(explode(",",$vote_record->sinks));
		echo "<p><img src='http://voxcharta.org/wp-content/plugins/vote-it-up/thumbup.png' class='voteicon'>" . $vote_count . " promotions</p>";
		echo "<p><img src='http://voxcharta.org/wp-content/plugins/vote-it-up/thumbdown.png' class='voteicon'>" . $sink_count . " demotions</p>";
		?>
		</div>
        <div style="float: left; padding-left: 15px;">
        <?php
         if($curauth->user_description<>'') {
			 echo '<p>'.$curauth->user_description.'</p>';
		 }
         if(($curauth->user_url<>'http://') && ($curauth->user_url<>'')) echo '<p class="im">'.__('Homepage:','arclite').' <a href="'.$curauth->user_url.'">'.$curauth->user_url.'</a></p>';
         if($curauth->yim<>'') echo '<p class="im">'.__('Yahoo Messenger:','arclite').' <a class="im_yahoo" href="ymsgr:sendIM?'.$curauth->yim.'">'.$curauth->yim.'</a></p>';
         if($curauth->jabber<>'') echo '<p class="im">'.__('Jabber/GTalk:','arclite').' <a class="im_jabber" href="gtalk:chat?jid='.$curauth->jabber.'">'.$curauth->jabber.'</a></p>';
         if($curauth->aim<>'') echo '<p class="im">'.__('AIM:','arclite').' <a class="im_aim" href="aim:goIM?screenname='.$curauth->aim.'">'.$curauth->aim.'</a></p>';
         ?>
          <div class="clear"></div>
        </div>
        <div class="clear"></div>
       </div>
       <br />

       <?php if (have_posts()): ?>
        <h2><?php printf(__('Posts by %s', 'arclite'), $curauth->display_name); ?></h2>
        <?php while (have_posts()) : the_post(); ?>
         <div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>
  	   	   <h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
           <small><?php the_time(__('l, F jS, Y','arclite')) ?></small>
  		   <p><?php the_tags(__('Tags:','arclite').' ', ', ', '<br />'); ?> <?php printf(__('Posted in %s','arclite'), get_the_category_list(', '));?>  | <?php edit_post_link(__('Edit','arclite'), '', ' | '); ?>  <?php comments_popup_link(__('No Comments','arclite'), __('1 Comment','arclite'), __('% Comments','arclite')); ?></p>
  	     </div>
        <?php endwhile; ?>

        <div class="navigation" id="pagenavi">
      	 <?php if(function_exists('wp_pagenavi')) : ?>
          <?php wp_pagenavi() ?>
     	 <?php else : ?>
          <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','arclite')) ?></div>
  	      <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','arclite')) ?></div>
          <div class="clear"></div>
         <?php endif; ?>
        </div>

       <?php else : ?>
        <p class="error"><?php _e('No posts found by this author.','arclite'); ?></p>
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
