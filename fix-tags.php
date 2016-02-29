<?php
	require_once(dirname(__FILE__) . '/../../../wp-config.php');

	nocache_headers();

	// if uninstalled, let's not do anything
	if(! get_option('wpo_version'))
	  return false;

	//$wpdb->query("UPDATE wp_votes_posts SET metacache = '', cachedate = '' WHERE 1=1");

	// check password
	if(isset($_REQUEST['code']) && $_REQUEST['code'] == get_option('wpo_croncode')) 
	{
		$pid = 23987;
		$wpdb->query("UPDATE wp_votes_posts SET metacache = '', cachedate = '' WHERE post = {$pid}");
		fixAuthors($pid);
		$post = get_post($pid);
		get_post_meta_data($post);
		fixTags($pid);
	}
?>
