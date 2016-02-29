<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php /* Arclite/digitalnature */
global $ishome, $show_everyone, $schedaffil, $institution, $institutions, $subdomains, $wpdb, $mobile, $page_uri, $today, $showabstracts, $arxiv_cat_abbrv;
require_once('array_column.php');
$page_uri = $_SERVER['REQUEST_URI'];

require_once('arxiv.php');
$ishome = (is_home()) ? 1 : 0;
?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php //language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">

<?php
$mobile = wpmd_is_phone();
if (!isset($_COOKIE['showsidebar'])) {
	$tog = ($mobile) ? '0' : '1';
	$showsidebar = !$mobile;
	setcookie('showsidebar',$tog,time()+365*24*3600,'/','.voxcharta.org');
} else {
	$showsidebar = $_COOKIE['showsidebar'];
}
if (!isset($_COOKIE['showabstracts'])) {
	$showabstracts = 1;
	setcookie('showabstracts',$showabstracts,time()+365*24*3600,'/','.voxcharta.org');
}
if (!isset($_COOKIE['sortval'])) {
	$sortval = 'postnum';
	setcookie('sortval',$sortval,time()+365*24*3600,'/','.voxcharta.org');
}
if (!isset($_COOKIE['orderval'])) {
	$orderval = 'ASC';
	setcookie('orderval',$orderval,time()+365*24*3600,'/','.voxcharta.org');
}
if (!isset($_COOKIE['showspecial'])) {
	setcookie('showspecial',1,time()+365*24*3600,'/','.voxcharta.org');
}
if (!isset($_COOKIE['shownew'])) {
	setcookie('shownew',1,time()+365*24*3600,'/','.voxcharta.org');
}
if (!isset($_COOKIE['showcro'])) {
	setcookie('showcro',1,time()+365*24*3600,'/','.voxcharta.org');
}
if (!isset($_COOKIE['showrep'])) {
	setcookie('showrep',1,time()+365*24*3600,'/','.voxcharta.org');
}

$catnamstr = md5(implode(",", $arxiv_cat_abbrv) . '1');
if (!isset($_COOKIE['catvis']) || !isset($_COOKIE['catnam']) || $_COOKIE['catnam'] != $catnamstr) {
	//$catvis = array_fill(1, count($arxiv_cat_abbrv), 1);
	//$catvisstr = implode(",", $catvis);
	$catvisstr = $institution->catvis;
	setcookie('catvis',$catvisstr,time()+365*24*3600,'/','.voxcharta.org');
	setcookie('catnam',$catnamstr,time()+365*24*3600,'/','.voxcharta.org');
}

date_default_timezone_set($institution->timezone);

if ($ishome) {
	include_once("/var/www/html/voxcharta/wp-content/themes/arclite/setupglobals.php");
} else {
	//$today = strtotime(sprintf(' %d/%d/%d',
	//	$wp_query->query_vars['monthnum'], $wp_query->query_vars['day'], $wp_query->query_vars['year']));
    $today = strtotime(sprintf(' %s-%s-%s', $wp_query->query_vars['year'], str_pad($wp_query->query_vars['monthnum'], 2, '0', STR_PAD_LEFT),
	    str_pad($wp_query->query_vars['day'], 2, '0', STR_PAD_LEFT)));
	//$today = GetResetTime($today) - 60;
}

?>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
$metakeywords = '';
if(get_option('arclite_meta')<>'') {
   if (is_home()) {
  	$metakeywords = get_option('arclite_meta');
   }
   else if(is_category()) {
    foreach((get_the_category()) as $category) { $metakeywords = $metakeywords.$category->cat_name . ','; }
   }
   else{
  	$metakeywords = '';
  	$tags = wp_get_post_tags($post->ID);
  	foreach ($tags as $tag ) { $metakeywords = $metakeywords . $tag->name . ", "; }
   }
}
if(($metakeywords<>'') && (!is_404())) { ?>
<meta name="keywords" content="<?php print $metakeywords; ?>" />
<meta name="description" content="<?php bloginfo('description'); ?>" />
<?php } ?>

<title>
<?php if (!$wp_query->is_single && $wp_query->query_vars['day'] > 0) { //Hack to fix title for 404 pages
    echo date('d', $today) . ' &laquo; ' . date('F', $today) . ' &laquo; ' . date('Y', $today) . ' &laquo; ';
} else {
	wp_title('&laquo;', true, 'right'); if (get_query_var('cpage') ) print ' Page '.get_query_var('cpage').' &laquo; ';
}
bloginfo('name');
?>
</title>

<link rel="alternate" type="application/rss+xml" title="Vox Charta RSS Feed" href="<?php get_option("siteurl") ?>/feed/" />
<link rel="alternate" type="application/atom+xml" title="Vox Charta Atom Feed" href="<?php get_option("siteurl") ?>/feed/atom/" />
<link rel="pingback" href="<?php get_option("siteurl") ?>/xmlrpc.ph" />
<link rel="shortcut icon" href="<?php get_option("siteurl") ?>/wp-content/themes/arclite/favicon.ico" />

<?php
  print '<style type="text/css" media="all">'.PHP_EOL;

  print '@import "'.get_option("siteurl").'/wp-content/themes/arclite/style-imageless.css?d='.date( 'Ymd', time()).'";'.PHP_EOL;
  if ($mobile) {
	  print '@import "'.get_option("siteurl").'/wp-content/themes/arclite/mobile.css";'.PHP_EOL;
  }
  if ($showsidebar == false) {
	  print '@import "'.get_option("siteurl").'/wp-content/themes/arclite/nosidebar.css";'.PHP_EOL;
  }

  $usercss = get_option('arclite_css');
  if($usercss<>'') print $usercss;

  print '</style>'.PHP_EOL;
?>

<!--[if lte IE 6]>
<style type="text/css" media="screen">
@import "<?php get_option("siteurl") ?>/wp-content/themes/arclite/ie6.css";
</style>
<![endif]-->

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php if(get_option('arclite_jquery')<>'no') { ?>
 <?php wp_enqueue_script('jquery'); ?>
 <?php wp_enqueue_script('voxcharta',get_option("siteurl").'/wp-content/themes/arclite/extra.js'); ?>
 <?php wp_enqueue_script('arclite',get_option("siteurl").'/wp-content/themes/arclite/js/arclite.js'); ?>
<?php } ?>

<script language="javascript">
<!--
function setPageSettings()
{
	if (<?php echo $showsidebar; ?> == 0) {
		document.getElementById('sidebardiv').style.display = 'none';
	}
	toggleCat(false);
}

onload=setPageSettings;
//-->
</script>

<a name="top"></a>
<?php wp_head(); ?>

<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    tex2jax: {
      inlineMath: [ ['$','$'], ["\\(","\\)"] ],
	  skipTags: ["script","noscript","style","textarea","pre","code","h3","img"],
      preview: "TeX",
      processEscapes: true
    },
	TeX: {
      noUndefined: {
        attributes: {
          mathcolor: "#000000"
        }
      },
	  Macros: {
	    msun: 'M_{\\odot}'
	  }
	},
	showProcessingMessages: false,
	messageStyle: "none"
  });
</script>
<script type="text/javascript"
   src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML&delayStartupUntil=configured">
</script>
</head>
<body <?php if (is_home()) { ?>class="home"<?php } else { ?>class="inner"<?php } ?>>
 <!-- page wrap -->
 <div id="page"<?php if(!is_page_template('page-nosidebar.php')) { print ' class="with-sidebar'; if((get_option('arclite_3col')=='yes') || (is_page_template('page-3col.php'))) print ' and-secondary'; print '"';  } ?>>

  <!-- header -->
  <div id="header-wrap">
   <div id="header" class="block-content">
     <div id="pagetitle">

      <?php
      // logo image?
      if(get_option('arclite_logo')=='yes' && get_option('arclite_logoimage')) { ?>
      <h1 class="logo"><a href="<?php get_option("siteurl") ?>/"><img src="<?php print get_option('arclite_logoimage'); ?>"
		  title="<?php bloginfo('name');  ?>" alt="<?php bloginfo('name');  ?>" style="position: absolute; top: 14px; left: 13px;"/></a></h1><br clear=all>
	  <div id="pagehead">
	  <h5>ArXiv discussions for <a href="http://<?php echo $institution->subdomain; ?>.voxcharta.org/tools/institution-stats/" class="titlelink"><?php
	    $ci = count($institutions);
	  	$showmax = 5;
	    echo $ci;
		?> institutions</a> including <?php
		$insts_copy = $institutions;
		for ($i = 0; $i < $showmax; $i++) {
			$c = count($insts_copy);
			$ind = rand(0, $c - 1);
			echo ($i > 0 && $i < $showmax && $showmax > 2) ? ', ' : ' ';
			if ($ci > 1 && $i == $showmax - 1) echo 'and ';
			$inst = $insts_copy[$ind];
			if ($inst['url'] != '') echo "<a href='{$inst['url']}' class='titlelink'>";
			echo $inst['name'];
			if ($inst['url'] != '') echo "</a>";
			array_splice($insts_copy, $ind, 1);
		}
	  	//foreach ($institutions as $i => $inst) {
		//	echo ($i > 0 && $i < $ci && $ci > 2) ? ', ' : ' ';
		//	if ($ci > 1 && $i == $ci - 1) echo 'and ';
		//	if ($inst->url != '') echo "<a href='{$inst->url}' class='titlelink'>";
		//	echo $inst->name;
		//	if ($inst->url != '') echo "</a>";
		//}
	  ?>.</h5></div>
      <?php } else { ?>
      <h1 class="logo"><a href="<?php get_option("siteurl") ?>/"><?php bloginfo('name'); ?></a></h1>
      <?php }  ?>

      <div class="clear"></div>

      <?php if(get_option('arclite_search')<>'no') { ?>
      <?php // get_search_form(); ?>
      <div id="portal-block">
      <!-- search form -->
	  <form name="portal" action="" method="POST">
	  <select name="scheduledrop" onchange="
	  	set_cookie('schedule_affiliation',this.options[this.selectedIndex].value,365,'/','.voxcharta.org','');
	    var url = location.href;
		var regex = new RegExp('http://(.+)\.voxcharta\.');
		if (url.match(regex)) {
			url = url.replace(regex, 'http://'+this.options[this.selectedIndex].value+'.voxcharta.');
		} else {
			regex = new RegExp('http://');
			url = url.replace(regex, 'http://'+this.options[this.selectedIndex].value+'.');
		}
	    location.href = url.toLowerCase();
	  ">
	  <?php
	  foreach ($institutions as $i => $inst) {
		$trimmed_inst = mb_strimwidth($inst['name'], 0, 50, "...");
	  	if ($inst['name'] === $schedaffil) {
	  		echo "<option selected value='{$subdomains[$i]}'>{$trimmed_inst}</option>";
	  	} else {
	  		echo "<option value='{$subdomains[$i]}'>{$trimmed_inst}</option>";
	  	}
	  }
	  ?>
	  </select><strong>'s Portal</strong>
	  </form>
	  </div>
	  <div class="disc-details">
		<p><?php echo $institution->primaryevent; ?> <?php echo $institution->location; ?></p>
		<?php $discussiontimes = explode(",", $institution->discussiontime);
		$discussiondays = explode(",", $institution->normaldays);
		$discussionends = explode(",", $institution->resettime);
		if (count($discussiontimes) > 1) {
			$sched = '';
			foreach ($discussiontimes as $i => $discussiontime) {
				$sched .= $discussiondays[$i] . ' (' . date('g:i a', strtotime($discussiontime.date(' m/d/Y'))) . date(' - g:i a', strtotime($discussionends[$i].date(' m/d/Y'))) . ')';
				if ($i != count($discussiontimes) - 1) {
					$sched .= ', ';
				} else $sched .= '.';
			}
		} else {
			$sched = implode(", ", $discussiondays) . " (" . date('g:i a', strtotime($discussiontimes[0].date(' m/d/Y'))) . date(' - g:i a', strtotime($discussionends[0].date(' m/d/Y'))) . ')';
		} ?>
        <p><?php echo $sched; ?></p>
	  </div>
      <div class="search-block">
        <div class="searchform-wrap">
          <form method="get" id="searchform" action="<?php get_option("siteurl") ?>/">
            <fieldset>
			<?php $search_str = "Keyword, author, or arXiv #"; ?>
            <input type="text" name="s" id="searchbox" class="searchfield" value="<?php _e($search_str,"arclite"); ?>" onfocus="if(this.value == '<?php _e($search_str,"arclite"); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e($search_str,"arclite"); ?>';}" />
             <input type="submit" value="Go" class="go" />
            </fieldset>
          </form>
        </div>
      </div>
      <!-- /search form -->
      <?php } ?>

     </div>

     <!-- main navigation -->
     <div id="nav-wrap1">
      <div id="nav-wrap2">
        <ul id="nav">
         <?php
          if((get_option('show_on_front')<>'page') && (get_option('arclite_topnav')<>'categories')) {
           if(is_home() && !is_paged()){ ?>
            <li id="nav-homelink" class="current_page_item"><a class="fadeThis" href="<?php echo 'http://' . $institution->subdomain . '.voxcharta.org'; ?>" title="<?php _e('You are Home','arclite'); ?>"><span><?php _e('Home','arclite'); ?></span></a></li>
           <?php } else { ?>
            <li id="nav-homelink"><a class="fadeThis" href="<?php echo 'http://' . $institution->subdomain . '.voxcharta.org'; ?>" title="<?php _e('Click for Home','arclite'); ?>"><span><?php _e('Home','arclite'); ?></span></a></li>
          <?php
           }
          } ?>
         <?php
           if(get_option('arclite_topnav')=='categories') {
            echo preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', '<li$1><a class="fadeThis"$2><span>$3</span></a>', wp_list_categories('show_count=0&echo=0&title_li='));
            }
           else {
			 $user_search1 = new WP_User_Query( array( 'role' => 'Administrator', 'fields' => array('ID')) );
			 $user_search2 = new WP_User_Query( array( 'role' => 'Liaison', 'fields' => array('ID')) );
			 $us_res_list1 = array();
			 $us_res_list2 = array();
			 foreach ($user_search1->get_results() as $res) {
				 array_push($us_res_list1, $res->ID);
			 }
			 foreach ($user_search2->get_results() as $res) {
				 array_push($us_res_list2, $res->ID);
			 }
			 $inst_users = $wpdb->get_col("SELECT user FROM wp_votes_users WHERE affiliation='{$institution->name}'");
			 $users = array_merge($us_res_list1, array_intersect($us_res_list2, $inst_users));
             //echo preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', '<li$1><a class="fadeThis"$2><span>$3</span></a>', wp_list_pages('echo=0&authors='.implode(",",$users).'&orderby=post_author&title_li=&'));
             echo str_replace('voxcharta.org', $institution->subdomain . '.voxcharta.org', preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', '<li$1><a class="fadeThis"$2><span>$3</span></a>', wp_list_pages(array('authors' => implode(",",$users), 'echo' => 0, 'sort_column' => 'post_author', 'title_li' => ''))));
           }
          ?>
        </ul>
      </div>
     </div>
     <!-- /main navigation -->

   </div>
  </div>
  <!-- /header -->
<div class="sidebartoggler"><div class="sidebartogglel">
<div id="sidebartoggle">
<a href="javascript:;" onclick="toggleSidebar(<?php echo (is_user_logged_in()) ? '1' : '0'; ?>, '<?php echo wp_create_nonce('log-out'); ?>');">
<?php if ($showsidebar) {
	echo 'Hide';
} else {
	echo 'Show';
}
echo ' Sidebar</a> | <a href="http://'.$institution->subdomain.'.voxcharta.org/wp-login.php?redirect_to='.$_SERVER['REQUEST_URI'];
echo (is_user_logged_in()) ? '&action=logout&_wpnonce='.wp_create_nonce('log-out').'">Log out</a> | <a href="http://'.$institution->subdomain.'.voxcharta.org/wp-admin/">Site admin' : '">Log in</a> | <a href="http://'.$institution->subdomain.'.voxcharta.org/wp-login.php?action=register&affiliation='.$institution->name.'">Register';
echo '</a>';
?></div></div></div>
