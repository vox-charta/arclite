<?php /* Arclite/digitalnature */ ?>
<?php global $institution; ?>

<!-- 2nd column (sidebar) -->
<div class="col2" id="sidebardiv">
 <ul id="sidebar">
    <?php if(get_option('arclite_sidebarcat')<>'no')  { ?>
    <li class="block">
      <!-- sidebar menu (categories) -->
      <ul class="menu">
        <?php if(get_option('arclite_jquery')=='no') {
          echo preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', 'sidebar_category_graphics', wp_list_categories('show_count=0&echo=0&title_li='));
        } else {
			  if (!empty($institution->events)) {
				  $catslugs = $wpdb->get_col("SELECT categoryslug FROM {$wpdb->prefix}votes_events WHERE affiliation = '{$institution->ID}'");
				  $catids = array();
				  foreach ($catslugs as $slug) {
					  $cat = get_category_by_slug($slug);
			          array_push($catids, $cat->term_id);
				  }
				  $catidstr = ",".implode(",", $catids);
			  } else $catidstr = '';

			  //$main_cats = array('astro-ph','astro-ph','astro-ph','astro-ph');
			  //for ($i = 1; $i <= count($main_cats); $i++) {
			  //    $cat = get_category_by_slug($main_cats[$i]);
			  //    echo '<li>' . $cat->name;
			  //}
              echo str_replace('voxcharta.org',$institution->subdomain.'.voxcharta.org',
			       preg_replace_callback('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', 'sidebar_category_graphics',
		  	       wp_list_categories('show_count=0&echo=0&title_li=&include=7,10451,10452,10461,10458,10455,10456,3396,748669,748671,820242,820243,748672,748676,748673')));
              //echo preg_replace_callback('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a> \(\<a ([^>]*) ([^>]*)>(.*?)\<\/a>\)@i', 'sidebar_category_graphics',
		  	  //  wp_list_categories('show_count=0&echo=0&title_li=&feed=XML&include=100896,11438'));
              echo str_replace('voxcharta.org',$institution->subdomain.'.voxcharta.org',
                   preg_replace_callback('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', 'sidebar_category_graphics',
			       wp_list_categories('show_count=0&echo=0&title_li=&include=12,18011,721997'.$catidstr)));
		} ?>

        <?php if (function_exists('xili_language_list')) { xili_language_list(); } ?>

		<?php
			ob_flush();
			flush();
		?>
      </ul>
      <!-- /sidebar menu -->
    </li>
	<br>
    <?php } ?>

    <?php 	/* Widgetized sidebar, if you have the plugin installed. */
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
    <?php // wp_list_bookmarks('category_before=&category_after=&title_li=&title_before=&title_after='); ?>

    <li class="block">
      <!-- box -->
      <div class="box">
       <div class="titlewrap"><h4><span><?php _e('Archives','arclite'); ?></span></h4></div>      
       <div class="wrapleft">
        <div class="wrapright">
         <div class="tr">
          <div class="bl">
           <div class="tl">
            <div class="br the-content">
             <ul>
              <?php wp_get_archives('type=monthly&show_post_count=1'); ?>
             </ul>
            </div>
           </div>
          </div>
         </div>
        </div>
       </div>
      </div>
      <!-- /box -->
    </li>

    <li class="block">
      <!-- box -->
      <div class="box">
       <div class="titlewrap"><h4><span><?php _e('Meta','arclite'); ?></span></h4></div>
       <div class="wrapleft">
        <div class="wrapright">
         <div class="tr">
          <div class="bl">
           <div class="tl">
            <div class="br the-content">
             <ul>
              <?php wp_register(); ?>
              <li><?php wp_loginout(); ?></li>
              <li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
              <li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
              <li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
              <?php wp_meta(); ?>
             </ul>
            </div>
           </div>
          </div>
         </div>
        </div>
       </div>
      </div>
      <!-- /box -->
    </li>
    <?php endif; ?>
 </ul>
</div>
<!-- /2nd column -->
