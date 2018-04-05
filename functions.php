<?php
add_action('init', 'create_my_taxonomies', 0);
add_filter( 'show_admin_bar', '__return_false' );

require_once("arxiv.php");

setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if ( SITECOOKIEPATH != COOKIEPATH )
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

function create_my_taxonomies() {
	register_taxonomy( 'post_author', 'post', array('hierarchical' => false, 'label' => __('Post Authors', 'series'), 'query_var' => true, 'rewrite' => array('slug' => 'post_author')) ) ;
}

/* Arclite/digitalnature */

function mobile_device_detect($iphone=true,$ipad=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false){

  $mobile_browser   = false; 
  $user_agent       = $_SERVER['HTTP_USER_AGENT']; 
  $accept           = $_SERVER['HTTP_ACCEPT']; 

  switch(true){ 

    case (preg_match('/ipad/i',$user_agent)); 
      $mobile_browser = $ipad; 
      $status = 'Apple iPad';
      if(substr($ipad,0,4)=='http'){ 
        $mobileredirect = $ipad; 
      } 
    break; 

    case (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent)); 
      $mobile_browser = $iphone; 
      $status = 'Apple';
      if(substr($iphone,0,4)=='http'){ 
        $mobileredirect = $iphone; 
      } 
    break; 

    case (preg_match('/android/i',$user_agent));  
      $mobile_browser = $android; 
      $status = 'Android';
      if(substr($android,0,4)=='http'){ 
        $mobileredirect = $android; 
      } 
    break; 

    case (preg_match('/opera mini/i',$user_agent)); 
      $mobile_browser = $opera; 
      $status = 'Opera';
      if(substr($opera,0,4)=='http'){ 
        $mobileredirect = $opera; 
      } 
    break; 

    case (preg_match('/blackberry/i',$user_agent)); 
      $mobile_browser = $blackberry; 
      $status = 'Blackberry';
      if(substr($blackberry,0,4)=='http'){ 
        $mobileredirect = $blackberry; 
      } 
    break; 

    case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent)); 
      $mobile_browser = $palm; 
      $status = 'Palm';
      if(substr($palm,0,4)=='http'){ 
        $mobileredirect = $palm; 
      } 
    break; 

    case (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent)); 
      $mobile_browser = $windows; 
      $status = 'Windows Smartphone';
      if(substr($windows,0,4)=='http'){ 
        $mobileredirect = $windows; 
      } 
    break; 

    case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent)); 
      $mobile_browser = true; 
      $status = 'Mobile matched on piped preg_match';
    break; 

    case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); 
      $mobile_browser = true; 
      $status = 'Mobile matched on content accept header';
    break; 

    case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); 
      $mobile_browser = true; 
      $status = 'Mobile matched on profile headers being set';
    break; 

    case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_',
	  'haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti',
	  'oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40',
	  'vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play',
	  'pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',))); 
      $mobile_browser = true; 
      $status = 'Mobile matched on in_array';
    break; 

    default;
      $mobile_browser = false; 
      $status = 'Desktop / full capability browser';
    break; 

  } 

  if($redirect = ($mobile_browser==true) ? $mobileredirect : $desktopredirect){
    header('Location: '.$redirect); 
    exit;
  }else{ 
		
		if($mobile_browser==''){
			return $mobile_browser; 
		}else{
			return array($mobile_browser,$status); 
		}
	}

}

function sidebar_category_graphics($matches) {
	$graphic = '';
	switch ($matches[3]) {
		case 'astro-ph':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_as_sm.png">'; break;
		case 'gr-qc':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_gq_sm.png">'; break;
		case 'hep-ex':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_hx_sm.png">'; break;
		case 'hep-lat':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_hl_sm.png">'; break;
		case 'hep-ph':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_hp_sm.png">'; break;
		case 'hep-th':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_ht_sm.png">'; break;
		case 'nucl-ex':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_nx_sm.png">'; break;
		case 'nucl-th':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_nt_sm.png">'; break;
		case 'Announcements':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_an.png">'; break;
		case 'Special Topics':
			$graphic = '<img class="sidecatprimary" src="'.get_option("siteurl").'/icon_st.png">'; break;
	}
	$is_sub = ($graphic == '');
	switch ($matches[3]) {
		case 'Cosmology and Nongalactic':
			$graphic = '<img class="sidecatsecondary" src="'.get_option("siteurl").'/icon_co_sm.png">'; break;
		case 'Earth and Planetary':
			$graphic = '<img class="sidecatsecondary" src="'.get_option("siteurl").'/icon_ep_sm.png">'; break;
		case 'Galaxies':
			$graphic = '<img class="sidecatsecondary" src="'.get_option("siteurl").'/icon_ga_sm.png">'; break;
		case 'High Energy':
			$graphic = '<img class="sidecatsecondary" src="'.get_option("siteurl").'/icon_he_sm.png">'; break;
		case 'Instrumentation and Methods':
			$graphic = '<img class="sidecatsecondary" src="'.get_option("siteurl").'/icon_im_sm.png">'; break;
		case 'Solar and Stellar':
			$graphic = '<img class="sidecatsecondary" src="'.get_option("siteurl").'/icon_sr_sm.png">'; break;
	}
	$is_sub = (!$is_sub || $graphic == '') ? false : true;
	if (!$is_sub) $graphic = "<div class='sidecatdiv'>{$graphic}</div>";
	return '<li '.$matches[1].'>'.'<a class="fadeThis"'.$matches[2].'>'.$graphic.$matches[3].'</a>';
}

function is_proceeding($co, $jo) {
	$proc_hint = false;
	$jour_hint = true;

	$proc_strs = array('proceeding', 'conference', 'symposium', 'proc.', 'presentation', 'meeting');
	foreach ($proc_strs as $str) {
		if (stripos($co, $str) !== false) $proc_hint = true;
		if (stripos($jo, $str) !== false) $proc_hint = true;
	}

	$jour_strs = array('apj', 'aj', 'mnras', 'icarus', 'astrophysical journal', 'phys. rev.', 'science', 'nature', 'mon. not.');
	foreach ($jour_strs as $str) {
		if (stripos($co, $str) !== false) $jour_hint = false;
		if (stripos($jo, $str) !== false) $jour_hint = false;
	}

	if ($proc_hint && $jour_hint) return true;

	return false;
}

function is_submitted($co, $jo) {
	if ($jo != '') {
		return false;
	} elseif (stripos($co, 'submitted') !== false) return true;
	return false;
}

function utf8tohtml($utf8, $encodeTags = false) {
    $result = '';
    for ($i = 0; $i < strlen($utf8); $i++) {
        $char = $utf8[$i];
        $ascii = ord($char);
        if ($ascii < 128) {
            // one-byte character
            $result .= ($encodeTags) ? htmlentities($char) : $char;
        } else if ($ascii < 192) {
            // non-utf8 character or not a start byte
        } else if ($ascii < 224) {
            // two-byte character
            $result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
            $i++;
        } else if ($ascii < 240) {
            // three-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $unicode = (15 & $ascii) * 4096 +
                       (63 & $ascii1) * 64 +
                       (63 & $ascii2);
            $result .= "&#$unicode;";
            $i += 2;
        } else if ($ascii < 248) {
            // four-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $ascii3 = ord($utf8[$i+3]);
            $unicode = (15 & $ascii) * 262144 +
                       (63 & $ascii1) * 4096 +
                       (63 & $ascii2) * 64 +
                       (63 & $ascii3);
            $result .= "&#$unicode;";
            $i += 3;
        }
    }
    return $result;
}

function array_transpose($arr) {
	$out = array();
	foreach ($arr as $key => $subarr) {
		foreach ($subarr as $subkey => $subvalue) {
				$out[$subkey][$key] = $subvalue;
		}
	}
	return $out;
}

function array_cartesian(array $array, $delimiter = ' ') {
	$current = array_shift($array);
	if(count($array) > 0) {
		$results = array();
		$temp = array_cartesian($array);
		foreach($current as $word) {
		  foreach($temp as $value) {
			$results[] =  $word . $delimiter . $value;
		  }
		}
		return $results;           
	}
	else {
	   return $current;
	}
}


function is_chrome() {
	return(eregi("chrome", $_SERVER['HTTP_USER_AGENT']));
}

function get_post_meta_data($posts) {
	global $wpdb, $authorshort;

	if (empty($posts)) return array();
	$post_meta_data = array_fill(0, count($posts), '');
	foreach ($posts as $p => $post) {
		if ($p != 0) $pid_str .= ',';
		$pid_str .= $post->ID;
	}
	$metacaches = $wpdb->get_results("SELECT metacache AS cache, cachedate AS date, post FROM wp_votes_posts WHERE post IN ({$pid_str}) ORDER BY FIELD(post, {$pid_str})");
	//$metacaches = array();
	$counter = 0;
	foreach ($posts as $p => $post) {
		$curtime = time();

		$have_authors = false;
		$metacache = $metacaches[$counter];
		if ($metacache->post != $post->ID) {
			$metacache = '';
		} else $counter++;
		if ($metacache == '' || $curtime > $metacache->date + 365*86400 || $metacache->date == '' || $metacache->cache == '') {
			$pm = get_post_meta($post->ID, 'wpo_authors', true);
			if (mb_detect_encoding($pm) != 'UTF-8') {
				$authors = utf8_decode($pm);
			} else $authors = $pm;
			// This can happen if wpo_authors isn't populated yet.
			if ($authors !== '') {
				$have_authors = true;
			}
		}
		if ($have_authors) {
			//$authors = utf8tohtml($authors, false);
			//$authors_arr = array_transpose($wpdb->get_results("SELECT * FROM wp_votes_authors WHERE FIND_IN_SET(term, (SELECT authors FROM wp_votes_posts WHERE post = '{$post->ID}'))", ARRAY_A));
			//print_r($authors_arr['aliases']);

			$authors_stripped = preg_match_all('/\<a href=["\'](.*?)["\']\\>/iu', $authors, $author_links);
			$author_links = $author_links[0];
			$authors_stripped = preg_replace( '/\<a href=["\'](.*?)["\']\\>/iu', '', $authors);
			$authors_stripped = preg_replace( '/\<\/a\>/iu', '', $authors_stripped);
			$authors_stripped = strip_tags($authors_stripped);
			$authors_stripped = trim($authors_stripped);
			$authors_arr = explode(",", $authors_stripped);
			//Fix commas within parentheses
			$new_authors_arr = array();
			$j = 0;
			while ($j <= count($authors_arr) - 2) {
				if (strpos($authors_arr[$j], '(') !== false && strpos($authors_arr[$j], ')') === false &&
					strpos($authors_arr[$j+1], '(') === false) {
					$authors_arr[$j] .= ', '. trim($authors_arr[$j+1]);
					array_splice($authors_arr, $j+1, 1);
				} else {
					$new_authors_arr[] = $authors_arr[$j];
					$j++;
				}
			}
			$new_authors_arr[] = $authors_arr[count($authors_arr)-1];
			$authors_arr = $new_authors_arr;
					
			$matches = array();
			$isderived = array();
			
			foreach ($authors_arr as $j => $author) {
				preg_match_all('/\s\(.*?\)/', $author, $single_matches);
				$authors_arr[$j] = trim(preg_replace('/\s\(.*?\)/', '', $authors_arr[$j]));
				if (count($single_matches[0]) > 0) {
					$matches[] = $single_matches[0][0];
					$isderived[] = false;
					continue;
				}
				$author_parts = explode(' ', $authors_arr[$j]);
				foreach ($author_parts as $k => $author_part) {
					if (strlen($author_part) == 1 || (strlen($author_part) == 2 && substr($author_part, 1) == '-'))
						$author_parts[$k] .= '.';
				}
				$authors_arr[$j] = implode(" ", $author_parts);
				$author_esc = $wpdb->escape($authors_arr[$j]);
				$author_matches = $wpdb->get_results("SELECT * FROM wp_votes_authors AS va WHERE INSTR(REPLACE(aliases, SPACE(1), ''), REPLACE('{$author_esc}', SPACE(1), ''))");
				$max_counts = 0;
				$match = false;
				foreach ($author_matches as $author_match) {
					$counts = explode(",", $author_match->affilcounts);
					$affiliations = explode("|", $author_match->affiliations);
					foreach ($counts as $k => $count) {
						if ($count > $max_counts) {
							$max_counts = $count;
							$match = utf8tohtml(stripslashes($affiliations[$k]));
						}
					}
				}
				$matches[] = $match;
				$isderived[] = true;
			}

			$tmp = array();
			$match_ids = array();
			$c = 0;

			$uniq_matches = array_filter(array_values(array_unique($matches)));

			foreach ($matches as $j => $match) {
				if ($match != false) {
					/*if (array_search($match, $tmp) === false) {
						$tmp[] = $match;
						$match_ids[] = '['.$c.']';
						$c++;
					}*/
					$tmp[] = $match;
					$match_ids[] = '['.(array_search($match, $uniq_matches)+1).']';	
				} else {
					$tmp[] = '';
					$match_ids[] = '';	
				} //else $authors_arr[$j] = $author_links[$j] . $authors_arr[$j] . '</a>';
				$authors_arr[$j] = $author_links[$j] . $authors_arr[$j] . '</a>' . $match_ids[$j];
			}
			$matches = $tmp;
			$authors = implode(", ", $authors_arr);

			$authors_arr = explode(", ", $authors);
			$orig_count = count($authors_arr);
			array_splice($authors_arr, 20 + ($orig_count == 21 ? 1 : 0));
			$authors = implode(", ", $authors_arr);

			$derived_matches = 0;
			$all_matches = 0;
			$shown_matches = array();
			foreach ($matches as $j => $match) { 
				if ($match_ids[$j] == '') continue;
				if (substr_count($authors, $match_ids[$j]) < 1) continue;
				$shown_matches[] = $match;
				$all_matches++;
				$authors = str_replace($match_ids[$j], '<sup>'.$all_matches.(($isderived[$j]) ? '&dagger;' : '').'</sup>', $authors);
				if ($isderived[$j]) $derived_matches++;
			}

			if (count($authors_arr) != $orig_count) $authors .= ", and " . ($orig_count - count($authors_arr)) . " others";

			$authors .= '</p><p class="metafoot">';
			$i = 0;
			foreach ($shown_matches as $j => $match) {
				$i++;
				$authors .= '<sup>'.$i.'</sup>'.str_replace(array(' (',')'), '', $match);
				if ($i >= $all_matches) break;
				if ($i != count($uniq_matches)) $authors .= ', ';
			}
			if ($all_matches < count($uniq_matches)) $authors .= ", and " . (count($uniq_matches) - $i) . " others";
			$authors .= '</p>';
			if ($derived_matches > 0) $authors .= '<p class="metafoot"><sup>&dagger;</sup>Listed affiliation is based on previous publications and was not specified in this preprint.</p>';
			$author_cache = $wpdb->escape($authors);
			$wpdb->query("INSERT INTO wp_votes_posts (post, metacache, cachedate) VALUES('{$post->ID}', '{$author_cache}', '{$curtime}')
						  ON DUPLICATE KEY UPDATE metacache = VALUES(metacache), cachedate = VALUES(cachedate)");
		} else $authors = $metacache->cache;

		$authorshort = strip_tags($authors);
		$post_custom = get_post_custom($post->ID);
		$arxivid = $post_custom['wpo_arxivid'][0];
		$permalink = $post_custom['wpo_sourcepermalink'][0];
		$comments = $post_custom['wpo_comments'][0];
		$journal = $post_custom['wpo_journal'][0];
		//$arxivid = get_post_meta($post->ID, 'wpo_arxivid', true);
		//$permalink = get_post_meta($post->ID, 'wpo_sourcepermalink', true); 
		//$comments = utf8_decode(get_post_meta($post->ID, 'wpo_comments', true)); 
		//$journal = utf8_decode(get_post_meta($post->ID, 'wpo_journal', true)); 

		$chstr = '';
		//if (is_chrome()) {
		//	$chstr = 'aps.';
		//}
		$pmd = '';
		$pmd .= '<p>'.$authors.'</p><p>';
		$pmd .= 'ArXiv #: <a href="'.$permalink.'">'.$arxivid.'</a> (';
		$pmd .= '<a href="http://'.$chstr.'arxiv.org/pdf/'.$arxivid.'">PDF</a>, ';
		$pmd .= '<a href="http://'.$chstr.'arxiv.org/ps/'.$arxivid.'">PS</a>, ';
		$ads_url = 'http://adsabs.harvard.edu/cgi-bin/bib_query?arXiv:'.$arxivid;
		$pmd .= '<a href="'.$ads_url.'">ADS</a>, ';
		if (time() - strtotime($post->post_date) > 3*3600) {
			$pmd .= '<a href="papers://url/'.rawurlencode($ads_url).'&title='.rawurlencode($post->post_title).'&selectedText='.rawurlencode($ads_url).'&identifier='.rawurlencode($arxivid).'">Papers</a>, ';
		} else {
			$pmd .= '<a href="papers://url/'.rawurlencode($permalink).'&title='.rawurlencode($post->post_title).'&selectedText='.rawurlencode($permalink).'&identifier='.rawurlencode($arxivid).'">Papers</a>, ';
		}
		$pmd .= '<a href="http://'.$chstr.'arxiv.org/format/'.$arxivid.'">Other</a>)</p>';
		if ($comments != '') {
			$pmd .= '<p>Comments: '.$comments.'</p>';
		}
		if ($journal != '') {
			$pmd .= '<p>Journal: '.$journal.'</p>';
		}
		$post_meta_data[$p] = $pmd;
	}

	return $post_meta_data;
}

function get_category_graphics($p_ID) {
	global $arxiv_cat_slugs, $arxiv_cat_abbrv, $arxiv_has_children, $arxiv_cat_titles;
	$args = array('orderby' => 'none');
	$categories = wp_get_object_terms($p_ID, 'category', $args);
	$img_str = '';
	foreach ($categories as $category) {
		$cat = get_category($category);
		switch ($cat->category_nicename) {
			case 'announcements':
				$img_str = $img_str.' <a href="'.get_option("siteurl").'/category/announcements/">
					<img src="'.get_option("siteurl").'/icon_an.png" class="caticon"></a>';
				break;
			case 'journal-club':
				$img_str = $img_str.' <a href="'.get_option("siteurl").'/category/journal-club/">
					<img src="'.get_option("siteurl").'/icon_jc.png" class="caticon"></a>';
				break;
			case 'colloquium-club':
				$img_str = $img_str.' <a href="'.get_option("siteurl").'/category/colloquium-club/">
					<img src="'.get_option("siteurl").'/icon_cc.png" class="caticon"></a>';
				break;
			case 'special-topics':
				$img_str = $img_str.' <a href="'.get_option("siteurl").'/category/special-topics/">
					<img src="'.get_option("siteurl").'/icon_st.png" class="caticon"></a>';
				break;
			default:
				$ci = array_search($cat->category_nicename, $arxiv_cat_slugs);
				if ($arxiv_has_children[$ci]) break;
				$cat_abbrv = $arxiv_cat_abbrv[$ci];
				$img_str = $img_str." <a href='".get_option("siteurl").
					"/category/{$cat->category_nicename}/'><div class='caticon'>
					<img src='".get_option("siteurl")."/icon_{$cat_abbrv}_sm.png'><br>".strtoupper($arxiv_cat_titles[$ci])."</a></div>";
				break;
		}
	}

	return $img_str;
}	

// xili-language plugin check
function init_language(){
	if (class_exists('xili_language')) {
		define('THEME_TEXTDOMAIN','arclite');
		define('THEME_LANGS_FOLDER','/lang');
	} else {
	   load_theme_textdomain('arclite', get_template_directory() . '/lang');
	}
}
add_action ('init', 'init_language');


// theme options
$options = array (
  array("type" => "open"),

  array(
   "id" => "arclite_imageless",
   "default" => "no",
   "type" => "arclite_imageless"),

  array(
   "id" => "arclite_3col",
   "default" => "no",
   "type" => "arclite_3col"),

  array(
   "id" => "arclite_jquery",
   "default" => "yes",
   "type" => "arclite_jquery"),

  array(
   "id" => "arclite_meta",
   "default" => "",
   "type" => "arclite_meta"),

  array(
   "id" => "arclite_header",
   "default" => "default",
   "type" => "arclite_header"),

  array(
   "id" => "arclite_logo",
   "default" => "no",
   "type" => "arclite_logo"),

  array(
   "id" => "arclite_sidebarpos",
   "default" => "right",
   "type" => "arclite_sidebarpos"),

  array(
   "id" => "arclite_sidebarcat",
   "default" => "yes",
   "type" => "arclite_sidebarcat"),

  array(
   "id" => "arclite_widgetbg",
   "default" => "",
   "type" => "arclite_widgetbg"),

  array(
   "id" => "arclite_contentbg",
   "default" => "",
   "type" => "arclite_contentbg"),

  array(
   "id" => "arclite_indexposts",
   "default" => "full",
   "type" => "arclite_indexposts"),

  array(
   "id" => "arclite_topnav",
   "default" => "pages",
   "type" => "arclite_topnav"),

  array(
   "id" => "arclite_search",
   "default" => "yes",
   "type" => "arclite_search"),

  array(
   "id" => "arclite_footer",
   "default" => "",
   "type" => "arclite_footer"),

  array(
   "id" => "arclite_css",
   "default" => "",
   "type" => "arclite_css"),

  array(
   "id" => "arclite_headercolor",
   "default" => "261c13",
   "type" => "arclite_headercolor"),

  array("type" => "close")
);
$uploadpath = wp_upload_dir();
if ($uploadpath['baseurl']=='') $uploadpath['baseurl'] = get_bloginfo('siteurl').'/wp-content/uploads';

function arclite_options() {
  global $options, $uploadpath;

  if (array_key_exists('action', $_REQUEST) && 'arclite_save'== $_REQUEST['action'] ) {
    foreach ($options as $value) {
     if( !isset( $_REQUEST[ $value['id'] ] ) ) {  } else { update_option( $value['id'], stripslashes($_REQUEST[ $value['id']])); } }
     if(stristr($_SERVER['REQUEST_URI'],'&saved=true')) {
     $location = $_SERVER['REQUEST_URI'];
    } else {
     $location = $_SERVER['REQUEST_URI'] . "&saved=true";
    }

    if ($_FILES["file-logo"]["type"]){
     $directory = $uploadpath['basedir'].'/';
     move_uploaded_file($_FILES["file-logo"]["tmp_name"],
     $directory . $_FILES["file-logo"]["name"]);
     update_option('arclite_logoimage', $uploadpath['baseurl']. "/". $_FILES["file-logo"]["name"]);
    }

    if ($_FILES["file-header"]["type"]){
     $directory = $uploadpath['basedir'].'/';
     move_uploaded_file($_FILES["file-header"]["tmp_name"],
     $directory . $_FILES["file-header"]["name"]);
     update_option('arclite_headerimage', $uploadpath['baseurl']. "/". $_FILES["file-header"]["name"]);
    }

    if ($_FILES["file-header2"]["type"]){
     $directory = $uploadpath['basedir'].'/';
     move_uploaded_file($_FILES["file-header2"]["tmp_name"],
     $directory . $_FILES["file-header2"]["name"]);
     update_option('arclite_headerimage2', $uploadpath['baseurl']. "/". $_FILES["file-header2"]["name"]);
    }
    header("Location: $location");
    die;
  }

  // set default options
  foreach ($options as $default) {
  if(array_key_exists('id', $default) && get_option($default['id'])=="") {
  	update_option($default['id'],$default['default']);
   }
  }

  /*
  // delete all options
  foreach ($options as $default) {
  delete_option($default['id'],$default['default']);
  }
  */

  // add_menu_page('Arclite', __('Arclite theme','arclite'), 10, 'arclite-settings', 'arclite_admin');
  add_theme_page(__('Arclite settings','arclite'), __('Arclite settings','arclite'), 'administrator', 'arclite-settings', 'arclite_admin');
}

function arclite_admin() {
    global $options, $uploadpath;
?>
<div class="wrap">
  <h2 class="alignleft"><?php _e("Arclite theme settings","arclite");?></h2><a class="alignright" style="margin: 20px;" href="http://digitalnature.ro/projects/arclite">Arclite homepage</a>
  <br clear="all" />
  <?php if ( $_REQUEST['saved'] ) { ?><div class="updated fade"><p><strong><?php _e('Settings saved.','arclite'); ?></strong></p></div><?php } ?>

  <style type="text/css"> @import "<?php print get_option('siteurl'). "/wp-content/themes/". get_option('template') ?>/js/colorpicker/colorpicker.css"; </style>
  <script type="text/javascript" src="<?php print get_option('siteurl'). "/wp-content/themes/". get_option('template') ?>/js/colorpicker/colorpicker.js"></script>
  <script type="text/javascript">

   // disable the ability to change options based on what the user previously selected
   function checkoptions(){
    if (document.getElementById('arclite_imageless-yes').checked){
      document.getElementById('arclite_header').disabled=true;
      document.getElementById('arclite_widgetbg').disabled=true;
      document.getElementById('arclite_contentbg').disabled=true;
      document.getElementById("userheader").style.display = "none";
      document.getElementById("usercolor").style.display = "none";
    }
    else {
      document.getElementById('arclite_header').disabled=false;
      document.getElementById('arclite_widgetbg').disabled=false;
      document.getElementById('arclite_contentbg').disabled=false;
      var headervalue = document.getElementById("arclite_header").value;
      if(headervalue == "user") { document.getElementById("userheader").style.display = "block"; } else { document.getElementById("userheader").style.display = "none"; }
      if(headervalue == "user2") { document.getElementById("usercolor").style.display = "block"; } else { document.getElementById("usercolor").style.display = "none"; }
    };

    if (document.getElementById('arclite_logo-yes').checked){
      document.getElementById("userlogo").style.display = "block";
    }
    else { document.getElementById("userlogo").style.display = "none"; }


   }

   // run at page load
   jQuery(document).ready(function() {
    checkoptions();


   jQuery('#arclite_headercolor').ColorPicker({
	onSubmit: function(hsb, hex, rgb) {
		jQuery('#arclite_headercolor').val(hex);
	},
	onBeforeShow: function () {
		jQuery(this).ColorPickerSetColor(this.value);
	},
	onChange: function(hsb, hex, rgb) {
		jQuery('#arclite_headercolor').val(hex);
        jQuery('#arclite_headercolor').css("background-color","#"+hex);
        colortype = hex[0];
        if (isNaN(colortype)) jQuery('#arclite_headercolor').css("color","#000");
        else jQuery('#arclite_headercolor').css("color","#fff");
	}
    })
    .bind('keyup', function(){
    	jQuery(this).ColorPickerSetColor(this.value);
    });
   });

  </script>

<form method="post" id="myForm" enctype="multipart/form-data" onclick="checkoptions();">
<div id="poststuff" class="metabox-holder">

 <div class="stuffbox">
  <h3><label for="link_url"><?php _e("General","arclite"); ?></label></h3>
  <div class="inside">
    <table class="form-table" style="width: auto">
    <?php
     foreach ($options as $value) {
      switch ( $value['type'] ) {
        case "arclite_imageless": ?>
        <tr>
        <th scope="row"><?php _e("Imageless layout","arclite") ?><br /><?php _e("(no background images; reduces pages to just a few KB, with the cost of less graphic details)","arclite"); ?></th>
        <td>
         <label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-yes" type="radio" value="yes"<?php if ( get_option( $value['id'] ) == "yes") { echo " checked"; } ?> /><?php _e("Yes","arclite"); ?></label>
         &nbsp;&nbsp;
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-no" type="radio" value="no"<?php if ( get_option( $value['id'] ) == "no") { echo " checked"; } ?> /><?php _e("No","arclite"); ?></label>
        </td>
        </tr>

      <?php break;
        case "arclite_jquery": ?>

        <tr>
        <th scope="row"><?php _e("Use jQuery","arclite"); ?><br /><?php _e("(for testing purposes only, you shouldnt change this)","arclite"); ?></th>
        <td>
         <label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-yes" type="radio" value="yes"<?php if ( get_option( $value['id'] ) == "yes") { echo " checked"; } ?> /><?php _e("Yes","arclite"); ?></label>
         &nbsp;&nbsp;
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-no" type="radio" value="no"<?php if ( get_option( $value['id'] ) == "no") { echo " checked"; } ?> /><?php _e("No","arclite"); ?></label>
        </td>
        </tr>

      <?php break;
      case "arclite_meta": ?>
        <tr>
        <th scope="row"><?php _e("Homepage meta keywords","arclite"); ?><br><?php _e("(Separate with commas. Tags are used as keywords on other pages. Leave empty if you are using a SEO plugin)","arclite"); ?></th>
        <td>
         <label>
          <input type="text" size="60" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php print get_option($value['id']); ?>" />
         </label>
        </td>
        </tr>

      <?php break;
        case "arclite_contentbg": ?>

        <tr>
        <th scope="row"><?php _e("Content background","arclite"); ?></th>
        <td>
         <label>
            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="code">
              <option <?php if (get_option($value['id'])=='') {?> selected="selected" <?php } ?> value="">Texture: Light brown + noise (default)</option>
              <option <?php if (get_option($value['id'])=='grunge') {?> selected="selected" <?php } ?> value="grunge">Texture: Grunge</option>
              <option <?php if (get_option($value['id'])=='white') {?> selected="selected" <?php } ?> value="white">White color</option>
            </select>
         </label>
        </td>
        </tr>


      <?php break;
        case "arclite_indexposts": ?>
        <tr>
        <th scope="row"><?php _e("Index page/Archives show:","arclite"); ?></th>
        <td>
         <label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-full" type="radio" value="full"<?php if ( get_option( $value['id'] ) == "full") { echo " checked"; } ?> /><?php _e("Full posts","arclite"); ?></label>
         &nbsp;&nbsp;
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-excerpt" type="radio" value="excerpt"<?php if ( get_option( $value['id'] ) == "excerpt") { echo " checked"; } ?> /><?php _e("Excerpts only","arclite"); ?></label>
        </td>
        </tr>

      <?php break;
        case "arclite_3col": ?>
        <tr>
        <th scope="row"><?php _e("Enable 3rd column on all pages","arclite"); ?><br /><?php _e("(apply the 3-column template if you only want it on certain pages)","arclite"); ?></th>
        <td>
         <label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-yes" type="radio" value="yes"<?php if ( get_option( $value['id'] ) == "yes") { echo " checked"; } ?> /><?php _e("Yes","arclite"); ?></label>
         &nbsp;&nbsp;
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-no" type="radio" value="no"<?php if ( get_option( $value['id'] ) == "no") { echo " checked"; } ?> /><?php _e("No","arclite"); ?></label>
        </td>
        </tr>

      <?php break;
	}
	}
	?>
   </table>
  </div>
 </div>

 <div class="stuffbox">
  <h3><label for="link_url"><?php _e("Header","arclite"); ?></label></h3>
  <div class="inside">
   <table class="form-table" style="width: auto">
    <?php
     foreach ($options as $value) {
      switch ( $value['type'] ) {
        case "arclite_topnav": ?>
        <tr>
        <th scope="row"><?php _e("Top navigation shows","arclite"); ?></th>
        <td>
         <label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-pages" type="radio" value="pages"<?php if ( get_option( $value['id'] ) == "pages") { echo " checked"; } ?> /><?php _e("Pages","arclite"); ?></label>
         &nbsp;&nbsp;
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-categories" type="radio" value="categories"<?php if ( get_option( $value['id'] ) == "categories") { echo " checked"; } ?> /><?php _e("Categories","arclite"); ?></label>
        </td>
        </tr>

      <?php break;
        case "arclite_search": ?>
        <tr>
        <th scope="row"><?php _e("Show search","arclite"); ?></th>
        <td>
         <label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-yes" type="radio" value="yes"<?php if ( get_option( $value['id'] ) == "yes") { echo " checked"; } ?> /><?php _e("Yes","arclite"); ?></label>
         &nbsp;&nbsp;
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-no" type="radio" value="no"<?php if ( get_option( $value['id'] ) == "no") { echo " checked"; } ?> /><?php _e("No","arclite"); ?></label>
        </td>
        </tr>

      <?php break;
        case "arclite_header": ?>

        <tr>
        <th scope="row"><?php _e("Header background","arclite"); ?></th>
        <td>
         <label>
            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="code">
              <option <?php if (get_option($value['id'])=='default') {?> selected="selected" <?php } ?> value="default">Texture: Dark brown (default)</option>
              <option <?php if (get_option($value['id'])=='green') {?> selected="selected" <?php } ?> value="green">Texture: Dark green</option>
              <option <?php if (get_option($value['id'])=='red') {?> selected="selected" <?php } ?> value="red">Texture: Dark red</option>
              <option <?php if (get_option($value['id'])=='blue') {?> selected="selected" <?php } ?> value="blue">Texture: Dark blue</option>
              <option <?php if (get_option($value['id'])=='field') {?> selected="selected" <?php } ?> value="field">Texture: Green Field</option>
              <option <?php if (get_option($value['id'])=='fire') {?> selected="selected" <?php } ?> value="fire">Texture: Burning</option>
              <option <?php if (get_option($value['id'])=='wall') {?> selected="selected" <?php } ?> value="wall">Texture: Dirty Wall</option>
              <option <?php if (get_option($value['id'])=='wood') {?> selected="selected" <?php } ?> value="wood">Texture: Wood</option>
              <option style="color: #ed1f24" <?php if (get_option($value['id'])=='user') {?> selected="selected" <?php } ?> value="user"><?php _e('User defined image (upload)','arclite'); ?></option>
              <option style="color: #ed1f24" <?php if (get_option($value['id'])=='user2') {?> selected="selected" <?php } ?> value="user2"><?php _e('User defined color','arclite'); ?></option>
            </select>
         </label>

         <div id="userheader" style="display: none;">
         <?php if(is_writable($uploadpath['basedir'])) {
          _e('Centered image (upload a 960x190 image for best fit):','arclite'); ?><br />
          <input type="file" name="file-header" id="file-header" />
          <?php if(get_option('arclite_headerimage')) { echo '<div><img src="'; echo get_option('arclite_headerimage'); echo '"  style="margin-top:10px;" /></div>'; } ?>
          <br />
          <?php _e('Tiled image, repeats itself across the entire header (centered image will show on top of it, if specified):','arclite'); ?><br />
          <input type="file" name="file-header2" id="file-header2" />
          <?php if(get_option('arclite_headerimage2')) { echo '<div><img src="'; echo get_option('arclite_headerimage2'); echo '"  style="margin-top:10px;" /></div>'; } ?>

          <em style="color:#228B22"><?php  printf(__('Files are uploaded to %s','arclite'), $uploadpath['baseurl']); ?></em>
        <?php } else {  ?>
          <em style="color:#ed1f24"><?php printf(__('Can\'t upload! Directory %s is not writable!<br />Change write permissions with CHMOD 755 or 777','arclite'), $uploadpath['baseurl']); ?></em>
        <?php }  ?>

         </div>

         <div id="usercolor" style="display: none;">
          <?php _e('Pick a color','arclite'); ?> <input type="text" id="arclite_headercolor" name="arclite_headercolor" style="background: #<?php print get_option('arclite_headercolor'); ?>; color: #<?php $colortype = get_option('arclite_headercolor'); $colortype = $colortype[0]; if(is_numeric($colortype)) print 'fff'; else print '000';  ?>" value="<?php print get_option('arclite_headercolor'); ?>" />
         </div>

        </td>
        </tr>

      <?php break;
      case "arclite_logo": ?>

        <tr>
        <th scope="row"><?php _e("Logo image","arclite"); ?></th>
        <td>
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-yes" type="radio" value="yes"<?php if ( get_option( $value['id'] ) == "yes") { echo " checked"; } ?> /><?php _e("Yes","arclite"); ?></label>

         &nbsp;&nbsp;
        <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>-no" type="radio" value="no"<?php if ( get_option( $value['id'] ) == "no") { echo " checked"; } ?> /><?php _e("No","arclite"); ?></label>

        <div id="userlogo">
        <?php if(is_writable($uploadpath['basedir'])) {
         _e("Upload a custom logo image","arclite"); ?><br />
         <input type="file" name="file-logo" id="file-logo" />
         <?php if(get_option('arclite_logoimage')) { echo '<div><img src="'; echo get_option('arclite_logoimage'); echo '"  style="margin-top:10px;border:1px solid #aaa;padding:10px;" /></div>'; } ?>
          <em style="color:#228B22"><?php printf(__('Files are uploaded to %s','arclite'), $uploadpath['baseurl']); ?></em>
        <?php } else {  ?>
          <em style="color:#ed1f24"><?php printf(__('Can\'t upload! Directory %s is not writable!<br />Change write permissions with CHMOD 755 or 777','arclite'), $uploadpath['baseurl']); ?></em>
        <?php }  ?>
        </div>

        </td>
        </tr>
      <?php break;
    	}
      }
	?>
   </table>
  </div>
 </div>

 <div class="stuffbox">
  <h3><label for="link_url"><?php _e("Sidebar","arclite"); ?></label></h3>
  <div class="inside">
   <table class="form-table" style="width: auto">
<?php
 foreach ($options as $value) {
  switch ( $value['type'] ) {
	case "arclite_sidebarpos": ?>

        <tr>
        <th scope="row"><?php _e("Sidebar position","arclite"); ?></th>
        <td>
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="left"<?php if ( get_option( $value['id'] ) == "left") { echo " checked"; } ?> /><?php _e("Left","arclite"); ?></label>

         &nbsp;&nbsp;
        <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="right"<?php if ( get_option( $value['id'] ) == "right") { echo " checked"; } ?> /><?php _e("Right","arclite"); ?></label>
        </td>
        </tr>
	<?php break;
	case "arclite_sidebarcat": ?>
        <tr>
        <th scope="row"><?php _e("Show theme-default category block","arclite"); ?></th>
        <td>
         <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="yes"<?php if ( get_option( $value['id'] ) == "yes") { echo " checked"; } ?> /><?php _e("Yes","arclite"); ?></label>

         &nbsp;&nbsp;
        <label><input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="no"<?php if ( get_option( $value['id'] ) == "no") { echo " checked"; } ?> /><?php _e("No","arclite"); ?></label>
        </td>
        </tr>
    <?php break;
        case "arclite_widgetbg": ?>

        <tr>
        <th scope="row"><?php _e("Widget title background","arclite"); ?></th>
        <td>
         <label>
            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="code">
              <option <?php if (get_option($value['id'])=='') {?> selected="selected" <?php } ?> value="" style="background: #ef3e60; color: #fff;">Pink (default)</option>
              <option <?php if (get_option($value['id'])=='green') {?> selected="selected" <?php } ?> value="green" style="background: #77bc34; color: #fff">Green</option>
              <option <?php if (get_option($value['id'])=='blue') {?> selected="selected" <?php } ?> value="blue" style="background: #3283b4; color: #fff">Blue</option>
              <option <?php if (get_option($value['id'])=='gray') {?> selected="selected" <?php } ?> value="gray" style="background: #939394; color: #fff">Gray</option>
            </select>
         </label>
        </td>
        </tr>

     <?php
    	}
      }
	?>
   </table>
  </div>
 </div>


 <div class="stuffbox">
  <h3><label for="link_url"><?php _e("Footer","arclite"); ?></label></h3>
  <div class="inside">
   <table class="form-table" style="width: auto">
    <?php
     foreach ($options as $value) {
      switch ( $value['type'] ) {
    	case "arclite_footer": ?>

        <tr>
        <th scope="row"><?php _e("Add content","arclite"); ?><br /><?php _e("(HTML allowed)","arclite"); ?></th>
        <td>
         <label>
          <textarea class="code" rows="4" cols="60" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php print get_option($value['id']); ?></textarea>
         </label>
        </td>
        </tr>

    <?php
      }
     }
	?>
   </table>
  </div>
 </div>

 <div class="stuffbox">
  <h3><label for="link_url"><?php _e("User CSS code","arclite"); ?></label></h3>
  <div class="inside">
   <table class="form-table" style="width: auto">
    <?php
     foreach ($options as $value) {
      switch ( $value['type'] ) {
    	case "arclite_css": ?>

        <tr>
        <th scope="row"><?php _e("Modify anything related to design using simple CSS","arclite"); ?><br /><br /><span style="color: #ed1f24"><?php _e("Avoid modifying theme files and use this option instead to preserve changes after update","arclite"); ?></span></th>
        <td valign="top">
         <label>
          <textarea class="code" rows="12" cols="60" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php print get_option($value['id']); ?></textarea>
         </label>
        </td>
        <td valign="top">
        Examples:
        <p><em style="color: #5db408">/* Set a fixed page width (960px) */</em><br /><code>.block-content{ width: 960px; max-width: 960px; }</code></p>
        <p><em style="color: #5db408">/* Set fluid page width (not recommended) */</em><br /><code>.block-content{ width: 95%; max-width: 95%; }</code></p>

        <p><em style="color: #5db408">/* Hide post information bar */</em><br /><code>.post p.post-date, .post p.post-author{ display: none; }</code></p>
        <p><em style="color: #5db408">/* Use Windows Arial style fonts, instead of Mac's Lucida */</em><br /><code>body, input, textarea, select, h1, h2, h6,<br />.post h3, .box .titlewrap h4{ font-family: Arial, Helvetica; }</code></p>

        <p><em style="color: #5db408">/* Make text logo/headline smaller */</em><br /><code>#pagetitle{ font-size: 75%; }</code></p>

        </td>
        </tr>

    <?php
      }
     }
	?>
   </table>
  </div>
 </div>

</div>
<input name="arclite_save" type="submit" class="button-primary" value="Save changes" />
<input type="hidden" name="action" value="arclite_save" />
</form>

</div>
<?php
}
add_action('admin_menu', 'arclite_options');

// check if sidebar has widgets
function is_sidebar_active($index = 1) {
  global $wp_registered_sidebars;

  if (is_int($index)): $index = "sidebar-$index";
  else :
  	$index = sanitize_title($index);
  	foreach ((array) $wp_registered_sidebars as $key => $value):
    	if ( sanitize_title($value['name']) == $index):
		 $index = $key;
	     break;
		endif;
	endforeach;
  endif;
  $sidebars_widgets = wp_get_sidebars_widgets();
  if (empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]))
    return false;
  else
  	return true;
}


// register sidebars
if ( function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Default sidebar',
        'id' => 'sidebar-1',
		'before_widget' => '<li class="block widget %2$s" id="%1$s"><div class="box"> <div class="wrapleft"><div class="wrapright"><div class="tr"><div class="bl"><div class="tl"><div class="br the-content">',
		'after_widget' => '</div></div></div></div></div></div></div> </div></li>',
		'before_title' => '<div class="titlewrap"><h4><span>',
		'after_title' => '</span></h4></div><div class="contentwrap">'
    ));

    register_sidebar(array(
        'name' => 'Footer',
        'id' => 'sidebar-2',
		'before_widget' => '<li class="block widget %2$s" id="%1$s"><div class="the-content">',
		'after_widget' => '</div></li>',
		'before_title' => '<h6 class="title">',
		'after_title' => '</h6>'
    ));

    register_sidebar(array(
        'name' => 'Secondary sidebar',
        'id' => 'sidebar-3',
		'before_widget' => '<li class="block widget %2$s" id="%1$s"><div class="box"> <div class="wrapleft"><div class="wrapright"><div class="tr"><div class="bl"><div class="tl"><div class="br the-content">',
		'after_widget' => '</div></div></div></div></div></div> </div></li>',
		'before_title' => '<div class="titlewrap"><h4><span>',
		'after_title' => '</span></h4></div>'
    ));
}

// list pings
function list_pings($comment, $args, $depth) {
 $GLOBALS['comment'] = $comment;
 ?>
 <li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php
}

// list comments
function list_comments($comment, $args, $depth) {
 $GLOBALS['comment'] = $comment;
 global $commentcount;
 if(!$commentcount) { $commentcount = 0; }

 if($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback') { ?>
  <li class="trackback">
   <div class="comment-mask">
    <div class="comment-main">
     <div class="comment-wrap1">
      <div class="comment-wrap2">
       <div class="comment-head">
         <p class="with-tooltip"><span><?php if ($comment->comment_type == 'trackback') _e("Trackback:","arclite"); else _e("Pingback:","arclite"); ?></span> <?php comment_author_link(); ?></p>
        </div>
      </div>
     </div>
    </div>
   </div>


 <?php
 }
 else { ?>

  <!-- comment entry -->
  <li <?php if (function_exists('comment_class')) { if (function_exists('get_avatar') && get_option('show_avatars')) echo comment_class('with-avatar'); else comment_class(); } else { print 'class="comment';if (function_exists('get_avatar') && get_option('show_avatars')) print ' with-avatar'; print '"';  } ?> id="comment-<?php comment_ID() ?>">
   <div class="comment-mask<?php if($comment->user_id == 1) echo ' admincomment'; else echo ' regularcomment'; // <- thanks to Jiri! ?>">
    <div class="comment-main tiptrigger">
     <div class="comment-wrap1">
      <div class="comment-wrap2">
       <div class="comment-head">
        <p>
          <?php
           if (get_comment_author_url()):
            $authorlink='<span class="with-tooltip"><a id="commentauthor-'.get_comment_ID().'" href="'.get_comment_author_url().'">'.get_comment_author().'</a></span>';
           else:
            $authorlink='<b id="commentauthor-'.get_comment_ID().'">'.get_comment_author().'</b>';
           endif;
           printf(__('%s by %s on %s', 'arclite'), '<a href="#comment-'.get_comment_ID().'">#'.++$commentcount.'</a>', $authorlink, get_comment_time(__('F jS, Y', 'arclite')), get_comment_time(__('H:i', 'arclite')));
          ?>
        </p>

        <?php if(comments_open()) { ?>
        <p class="controls tip">
             <?php
              if (function_exists('comment_reply_link')) {
               comment_reply_link(array_merge( $args, array('add_below' => 'comment-reply', 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<span>'.__('Reply','arclite').'</span>'.$my_comment_count)));
              } ?>
              <a class="quote" title="<?php _e('Quote','arclite'); ?>" href="javascript:void(0);" onclick="MGJS_CMT.quote('commentauthor-<?php comment_ID() ?>', 'comment-<?php comment_ID() ?>', 'comment-body-<?php comment_ID() ?>', 'comment');"><span><?php _e('Quote','arclite'); ?></span></a> <?php edit_comment_link('Edit','',''); ?>

        </p>
        <?php } ?>
       </div>
       <div class="comment-body clearfix" id="comment-body-<?php comment_ID() ?>">
         <?php if (function_exists('get_avatar') && get_option('show_avatars')) { ?>
         <div class="avatar"><?php echo get_avatar($comment, 64); ?></div>
         <?php } ?>

         <?php if ($comment->comment_approved == '0') : ?>
	     <p class="error"><small><?php _e('Your comment is awaiting moderation.','arclite'); ?></small></p>
	     <?php endif; ?>

         <?php comment_text(); ?>
         <a id="comment-reply-<?php comment_ID() ?>"></a>
       </div>
      </div>
     </div>
    </div>
   </div>
<?php  // </li> is added automatically
  } }

/**
 * Add new permissions/capabilities to a specific role
 *
 * @param string $role
 * @param string $cap
 */
function add_capability($role,$cap) {
	$role_obj = get_role($role); // get the the role object
	$role_obj->add_cap($cap); // add $cap capability to this role object
}
add_capability('liaison','edit_institution'); //Example
add_capability('administrator','edit_institution'); //Example

/**
 * Remove existing permissions/capabilities to a specific role
 *
 * @param string $role
 * @param string $cap
 */
function remove_capability($role,$cap) {
	$role_obj = get_role($role); // get the the role object
	$role_obj->remove_cap($cap); // add $cap capability to this role object
}
//remove_capability('liaison','Sabre'); //Example

function display_vote_info($postID, $userID) {
	global $institution, $today, $ishome, $wpdb;

	$user_voted = UserVoted($postID,$userID);
	if(!$user_voted) {
		$next_co_offset = 86400*AgendaOffset('next', 'co', time());
		$disc_time = GetResetTime(time() + $next_co_offset);
		$close_time = GetResetTime(time() + $next_co_offset) - 60*$institution->closedelay;
		$disc_end_time = strtotime($institution->resettime . date(' m/d/Y', $disc_time));
		if ($institution->closevoting == 0 && time() > $close_time) {
			echo "<span class='voteclosed'>Voting for {$institution->name} is now closed!</span><br>";
		} else {
			$votelink = "href=\"javascript:vote('votecount{$postID}','Voted!',{$postID},{$userID},'".VoteItUp_ExtPath()."','{$today}','{$ishome}');\"";
			$sinklink = "href=\"javascript:sink('votecount{$postID}','Voted!',{$postID},{$userID},'".VoteItUp_ExtPath()."','{$today}','{$ishome}');\"";
			echo "<a {$votelink}><img src='http://voxcharta.org/wp-content/plugins/vote-it-up/thumbup.png' class='voteicon' alt='Promote this paper'><span class='votelinkspace'></span>Promote</a>&nbsp;";
			echo "<a {$sinklink}><img src='http://voxcharta.org/wp-content/plugins/vote-it-up/thumbdown.png' class='voteicon' alt='Demote this paper'><span class='votelinkspace'></span>Demote</a></span>";
			if (time() > $close_time  && $institution->closevoting == 1) { 
				$disc_time = GetResetTime($disc_end_time + 86400*AgendaOffset('next', 'co', $disc_end_time+1));
				echo "<br><span class='voteclosed'>Note: Voting has closed, votes will count towards the ".date("n/j/Y", $disc_time)." discussion.</span><br>";
			}
		}
	} else { 
		//NOTE: Currently, bumping is determined using the current portal as the institution, when in fact it should
		//be the user's institution. AgendaOffset needs to be changed to accept an institution as an argument.
		$user_inst = $wpdb->get_var("SELECT affiliation FROM {$wpdb->prefix}votes_users WHERE user='{$userID}'");
		$affil_id = $wpdb->get_var("SELECT ID, closedelay FROM {$wpdb->prefix}votes_institutions WHERE name='{$user_inst}'");
		$last_vote_time = GetLastVoteTime($postID, $affil_id, $userID);
		$next_coffee = AgendaOffset('next', 'co', $last_vote_time + 60*$institution->closedelay);
		$bump_time = GetResetTime($last_vote_time + 60*$institution->closedelay + 86400*$next_coffee);
		if ($user_voted == 1) {
			echo "<img src='http://voxcharta.org/wp-content/plugins/vote-it-up/thumbup.png' class='voteicon'>";
			echo "You promoted this paper";
		} elseif ($user_voted == 2) {
			echo "<img src='http://voxcharta.org/wp-content/plugins/vote-it-up/thumbdown.png' class='voteicon'>";
			echo "You demoted this paper";
		}
		$user_committed = UserCommitted($postID,$userID);
		if (time() > $bump_time) {
			// For now, don't allow past papers to be marked as presented.
			if (!$user_committed) {
				echo "<div><b>You discussed this previously!</b></div>";
			//	echo "<div><a href=\"javascript:present('votecount{$postID}','Done!',{$postID},{$userID},'".VoteItUp_ExtPath()."','{$today}','{$ishome}');\">";
			//	echo "<img src='http://voxcharta.org/wp-content/plugins/vote-it-up/present.png' class='voteicon'>";
			//	echo "Mark as presented</a></div>";
			} else {
				echo "<div><img src='http://voxcharta.org/wp-content/plugins/vote-it-up/present.png' class='voteicon'>";
				echo "<b>You presented this previously!</b></div>";
			}
			echo "<div><a href=\"javascript:bump('votecount{$postID}','Done!',{$postID},{$userID},'".VoteItUp_ExtPath()."');\">";
			echo "<img src='http://voxcharta.org/wp-content/plugins/vote-it-up/bump.png' class='voteicon'><span class='votelinkspace'></span>";
			echo "Bump vote to next discussion</a></div>";
		} else {
			if (strtolower($user_inst) != 'unaffiliated') {
				if (!$user_committed) {
					echo "<div><a href=\"javascript:present('votecount{$postID}','Done!',{$postID},{$userID},'".VoteItUp_ExtPath()."','{$today}','{$ishome}');\">";
					echo "<img src='http://voxcharta.org/wp-content/plugins/vote-it-up/present.png' class='voteicon'>";
					echo "Commit to present</a></div>";
				} else {
					echo "<div><img src='http://voxcharta.org/wp-content/plugins/vote-it-up/present.png' class='voteicon'>";
					echo "You're presenting this paper</div>";
				}
			}
			$ndiscussions = 10;
			$disctime = time();
			$uvt = UserVoteTime($postID,$userID);
			//$uvtdate = date('m/d/Y', $uvt + 86400*AgendaOffset('next', 'co', $uvt));
			$uvtdate = date('m/d/Y', $uvt);
			$nextdiscussions = array($disctime);
			for ($n = 1; $n <= $ndiscussions; $n++) {
				//if ($n == 1 && GetResetTime($disctime) > $disctime) {
				//	array_push($nextdiscussions, $disctime);
				//} else {
					$disctime = $disctime + 86400*AgendaOffset('next', 'an', GetResetTime($disctime) + 1);
					array_push($nextdiscussions, GetResetTime($disctime) - 1);
				//}
			}
			$nextdiscussions = array_values(array_unique($nextdiscussions));
			if (strtolower($user_inst) != 'unaffiliated') {
				echo "<form style='font-size: x-small;'>";
				echo "<div><img src='http://voxcharta.org/wp-content/plugins/vote-it-up/calendar.gif' class='voteicon'>";
				echo ($user_committed) ? "Present on " : "Discuss on ";
				echo "<select name='datesel' id='datesel{$postID}' class='regular-text' onchange=\"javascript:changedate('votecount{$postID}','Done!',{$postID},{$userID},'".VoteItUp_ExtPath()."','{$today}','{$ishome}');\">";
				foreach ($nextdiscussions as $nd) {

					$ndformatted = date('m/d/Y', $nd);
					$selected = ($ndformatted == $uvtdate) ? 'selected ' : '';
					echo "<option {$selected}value='{$nd}'>{$ndformatted}</option>";
				}
				echo "</select></form></div>";
			}
			echo "<div><a href=\"javascript:unvote('votecount{$postID}','Done!',{$postID},{$userID},'".VoteItUp_ExtPath()."','{$today}','{$ishome}');\">
				  <img src='http://voxcharta.org/wp-content/plugins/vote-it-up/icon_remove.png' class='voteicon' alt='Remove your vote and commitment'><span class='votelinkspace'></span>Remove Vote";
			if ($user_committed) echo " and Commitment";
			echo "</a></div>";
		}
	}
}

function fixTags($post = NULL, $silent = false)
{
	require_once(dirname(__FILE__) . '/pluralize.php');
	require_once(dirname(__FILE__) . '/../../plugins/simple-tags/inc/admin.php');

	$inf = new Inflect;
	global $wpdb;
	$allnames = $wpdb->get_col("SELECT a.name
								FROM wp_terms AS a
									LEFT JOIN wp_term_taxonomy AS c ON a.term_id = c.term_id
									LEFT JOIN wp_term_relationships AS b ON b.term_taxonomy_id = c.term_taxonomy_id
								WHERE (c.taxonomy = 'post_tag')
								GROUP BY a.name");

	if ($post == NULL) {
		$posttags = $wpdb->get_results("SELECT a.name, a.term_id
									FROM wp_terms AS a
										LEFT JOIN wp_term_taxonomy AS c ON a.term_id = c.term_id
										LEFT JOIN wp_term_relationships AS b ON b.term_taxonomy_id = c.term_taxonomy_id
									WHERE (c.taxonomy = 'post_tag')
									GROUP BY a.name");
	} else {
		$posttags = wp_get_object_terms($post, 'post_tag');
	}
	
	$count = 1;
	foreach ($posttags as $i => $tag) {
		$j++;
		if (strlen($tag->name) <= 3) continue;
		$sing = $inf->singularize($tag->name);
		if (strcasecmp($sing, $tag->name) == 0) continue;
		if (!in_array($sing, $allnames)) continue;

		// Get objects from term ID
		$objects_id = get_objects_in_term( $tag->term_id, 'post_tag', array('fields' => 'all_with_object_id'));
				
		// Delete old term
		wp_delete_term( $tag->term_id, 'post_tag' );
				
		// Set objects to new term ! (Append no replace)
		foreach ( (array) $objects_id as $object_id ) {
			wp_set_object_terms( $object_id, $sing, 'post_tag', true );
		}

		if (!$silent) echo '['.$count.'] '.$tag->name.' --> '.$sing.'<br>'."\n";
		if ($post == NULL) {
			ob_flush();
			flush();
		}
		$count++;
					
		// Clean cache
		clean_object_term_cache( $objects_id, 'post_tag');
		clean_term_cache($tag->term_id, 'post_tag');
	}

	if (!$silent) echo 'Total tags: ' . $j . "<br><br>\n";
}

function fixAuthors($post = NULL)
{
	require_once(dirname(__FILE__) . '/pluralize.php');
	require_once(dirname(__FILE__) . '/../../plugins/simple-tags/inc/admin.php');

	$inf = new Inflect;
	global $wpdb;
	if ($post == NULL) {
		$posttags = $wpdb->get_results("SELECT a.name, a.term_id
									FROM wp_terms AS a
										LEFT JOIN wp_term_taxonomy AS c ON a.term_id = c.term_id
										LEFT JOIN wp_term_relationships AS b ON b.term_taxonomy_id = c.term_taxonomy_id
									WHERE (c.taxonomy = 'post_author')
									GROUP BY a.name");
	} else {
		$posttags = wp_get_object_terms($post, 'post_author');
	}
	
	$count = 1;
	foreach ($posttags as $i => $tag) {
		$j++;
		$author_arr = explode(' ', $tag->name);
		$last_name_mark = 1;
		if (count($author_arr) > 2) switch ($author_arr[count($author_arr)-1]) {
			case 'I': case 'II': case 'III': case 'IV': case 'V': case 'VI': case 'VII': case 'VIII': case 'IX':
				$last_name_mark = 2; break;
		}
		for ($j = 0; $j < count($author_arr) - $last_name_mark; $j++) {
			$author_arr[$j] = preg_replace("/[^a-zA-Z\-]/", '', $author_arr[$j]);
		}
		$author_str = '';
		for ($j = 0; $j < count($author_arr) - $last_name_mark; $j++) {
			if ($author_arr[$j] == '') continue;
			$initials_arr = explode('-', $author_arr[$j]);
			$initials = $initials_arr[0];
			for ($k = 1; $k < count($initials_arr); $k++) {
				if ($initials_arr[$k] == '') continue;
				$initials = ". -".$initials.$initials_arr[$k];
			}
			$initials = $initials.". ";
			$author_str = $author_str.$initials;
		}
		$author_str = $author_str.$author_arr[count($author_arr) - $last_name_mark];
		echo $author_str . "<br>\n";
		$count++;
		continue;
		// Get objects from term ID
		$objects_id = get_objects_in_term( $tag->term_id, 'post_author', array('fields' => 'all_with_object_id'));
				
		// Delete old term
		wp_delete_term( $tag->term_id, 'post_author' );
				
		// Set objects to new term ! (Append no replace)
		foreach ( (array) $objects_id as $object_id ) {
			wp_set_object_terms( $object_id, $author_str, 'post_author', true );
		}

		echo '['.$count.'] '.$tag->name.' --> '.$author_str.'<br>'."\n";
		if ($post == NULL) {
			ob_flush();
			flush();
		}
					
		// Clean cache
		clean_object_term_cache( $objects_id, 'post_author');
		clean_term_cache($tag->term_id, 'post_author');
	}

	echo $j;
}

function isTime($time,$is24Hours=true,$seconds=false) {
    $pattern = "/^".($is24Hours ? "([1-2][0-3]|[01]?[1-9])" : "(1[0-2]|0?[1-9])").":([0-5]?[0-9])".($seconds ? ":([0-5]?[0-9])" : "")."$/";
    if (preg_match($pattern, $time)) {
        return true;
    }
    return false;
}

//add_filter('pre_get_posts', 'optimized_get_posts', 100);
//function optimized_get_posts() {
//	global $wp_query, $wpdb;
//
//	$wp_query->query_vars['no_found_rows'] = 1;
//	$wp_query->found_posts = $wpdb->get_var( "SELECT COUNT(*) FROM wp_posts WHERE 1=1 AND wp_posts.post_type = 'post' AND (wp_posts.post_status = 'publish' OR wp_posts.post_status = 'private')" );
//	$wp_query->found_posts = apply_filters_ref_array( 'found_posts', array( $wp_query->found_posts, &$wp_query ) );
//	$wp_query->max_num_pages = ceil($wp_query->found_posts / $wp_query->query_vars['posts_per_page']);
//
// 	return $wp_query;
//}

function leo_array_diff($a, $b) {
    $map = $out = array();
    foreach($a as $val) $map[$val] = 1;
    foreach($b as $val) unset($map[$val]);
    return array_keys($map);
}

function count_many_users_comments( $users ) {
    global $wpdb;

    $count = array();
    if ( empty( $users ) || ! is_array( $users ) )
        return $count;

    $userlist = implode( ',', array_map( 'absint', $users ) );
	$where = 'WHERE comment_approved = 1 AND user_id <> 0';

    $result = $wpdb->get_results( "SELECT user_id, COUNT(*) FROM $wpdb->comments $where AND user_id IN ($userlist) GROUP BY user_id", ARRAY_N );
    foreach ( $result as $row ) {
        $count[ $row[0] ] = $row[1];
    }

    foreach ( $users as $id ) {
        if ( ! isset( $count[ $id ] ) )
            $count[ $id ] = 0;
    }

    return $count;
}

function erase_default_query($query) {
	if ($query->is_main_query()) {
		$query->set('posts_per_page', 0);
	}
}
add_action('pre_get_posts', 'erase_default_query');

function my_page_template_redirect()
{
	global $schedaffil, $institution, $institutions, $wpdb, $subdomains;
	require_once('array_column.php');

	$url_parts = explode('.', $_SERVER['HTTP_HOST']);
	$sub_is_inst = false;
	$institutions = $wpdb->get_results("SELECT name, url, subdomain FROM {$wpdb->prefix}votes_institutions WHERE name <> 'Unaffiliated' AND active = 1", ARRAY_A);
	$subdomains = array_map('strtolower', array_column($institutions, 'subdomain'));
	$inst_names = array_column($institutions, 'name');
	array_multisort($inst_names, SORT_STRING, $institutions, $subdomains);
	if (count($url_parts) == 3) {
		$lsd = strtolower($url_parts[0]);
		$inst_key = array_search($lsd, $subdomains);
		if ($inst_key !== false) $sub_is_inst = true;
	}
	if ($sub_is_inst) {
		$schedaffil = $institutions[$inst_key]['name'];
		setcookie('schedule_affiliation',$schedaffil,time()+365*24*3600,'/','.voxcharta.org');
	} else {
		if (!isset($_COOKIE['show_everyone'])) {
			setcookie('show_everyone','0',time()+365*24*3600,'/','.voxcharta.org');
			$show_everyone = false;
		} else {
			$show_everyone = ($_COOKIE['show_everyone'] == '1') ? true : false;
		}
		if (!isset($_COOKIE['schedule_affiliation'])) {
			setcookie('schedule_affiliation','Harvard ITC',time()+365*24*3600,'/','.voxcharta.org');
			$schedaffil = 'Harvard ITC';
		} else {
			$schedaffil = $_COOKIE['schedule_affiliation'];
		}
	}

	$institution = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}votes_institutions WHERE name='{$schedaffil}'");

	if (count($url_parts) == 2) {
		$newloc = "Location: http://{$institution->subdomain}.voxcharta.org{$_SERVER['REQUEST_URI']}";
		header( $newloc );
	}

	if (strlen($_SERVER['REDIRECT_URL']) == 10) {
		$id = substr($_SERVER['REDIRECT_URL'], 1);
		$id_parts = explode('.', $id);
		//print_r($id_parts);
		if (count($id_parts) == 2) {
			if (is_numeric($id_parts[0]) && is_numeric($id_parts[1]) && strlen($id_parts[0]) == 4 && strlen($id_parts[1]) == 4) {
				$sql = $wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'wpo_arxivid' AND meta_value = '%s' LIMIT 1", $id);
				echo $sql;
				$pid = $wpdb->get_var($sql);
				$guid = str_replace('voxcharta.org', $institution->subdomain.'.voxcharta.org', $wpdb->get_var("SELECT guid FROM {$wpdb->prefix}posts WHERE ID = '{$pid}'"));
				if ($pid !== NULL) header("Location: {$guid}");
			}
		}
	}
}

function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/login-logo.png);
			height: 60px;
			width: 325px;
			-webkit-background-size: 325px 60px;
			background-size: 325px 60px;
        }
    </style>
<?php }

// Allow users to edit their own comments no matter who owns the post (default is to only let users edit comments on their own posts).
function allow_user_to_edit_comment( $caps, $cap, $user_id, $args ) {
	if ( 'edit_comment' == $cap ) {
		$comment = get_comment( $args[0] );
		if ( empty( $comment ) ) {
			$caps[] = 'do_not_allow';
			return $caps;
		} elseif ( $comment->user_id != $user_id && !user_can( $user_id, 'edit_others_posts') ) {
			$caps[] = 'do_not_allow';
			return $caps;
		} else {
			$caps = array();
		}
	}
	return $caps;
}

// Make cookies expire after a longer period.
add_filter('auth_cookie_expiration', 'my_expiration_filter', 99, 3);
function my_expiration_filter($seconds, $user_id, $remember){

    //if "remember me" is checked;
    if ( $remember ) {
        //WP defaults to 2 weeks;
        $expiration = 180*24*60*60;
    } else {
        //WP defaults to 48 hrs/2 days;
        $expiration = 2*24*60*60;
    }

    //http://en.wikipedia.org/wiki/Year_2038_problem
    if ( PHP_INT_MAX - time() < $expiration ) {
        //Fix to a little bit earlier!
        $expiration =  PHP_INT_MAX - time() - 5;
    }

    return $expiration;
}

function apply_portal() {
	if (is_page_template('create-portal.php')) {
		global $reCAPTCHA;	

		require_once('recaptchalib.php');

		$reCAPTCHA = new reCAPTCHA(
			'6LdGRFEUAAAAAG1fcTRbKBGM2EB6dpCdP50LY5qP',
			'6LdGRFEUAAAAAD4REqGNsgFabH5wOkCOLblh3ZpQ');

		echo $reCAPTCHA->getScript();
	}
}

add_filter('map_meta_cap', 'allow_user_to_edit_comment', 10, 4 );
add_action('wp_head', 'apply_portal');
add_action( 'template_redirect', 'my_page_template_redirect' );
add_action( 'login_enqueue_scripts', 'my_login_logo' );
remove_filter('the_content', 'wptexturize');
