function set_cookie( name, value, expires, path, domain, secure )
{
// set time, it's in milliseconds
var today = new Date();
today.setTime( today.getTime() );

/*
if the expires variable is set, make the correct
expires time, the current script below will set
it for x number of days, to make it for hours,
delete * 24, for minutes, delete * 60 * 24
*/
if ( expires )
{
expires = expires * 1000 * 60 * 60 * 24;
}
var expires_date = new Date( today.getTime() + (expires) );

document.cookie = name + "=" +escape( value ) +
( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
( ( path ) ? ";path=" + path : "" ) +
( ( domain ) ? ";domain=" + domain : "" ) +
( ( secure ) ? ";secure" : "" );
}

function get_cookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=");
  if (c_start!=-1)
    {
    c_start=c_start + c_name.length+1;
    c_end=document.cookie.indexOf(";",c_start);
    if (c_end==-1) c_end=document.cookie.length;
    return unescape(document.cookie.substring(c_start,c_end));
    }
  }
return "";
}

function loadcssfile(filename){
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
}

function togglespecial(){
	if (document.getElementById('specialsection').style.display == 'none') {
		document.getElementById('specialsection').style.display = 'block';
		document.getElementById('specialsep').style.display = 'none';
		document.getElementById('specialbutton').value = 'Collapse';
		set_cookie('showspecial','1',365,'/','.voxcharta.org','');
	} else {
		document.getElementById('specialsection').style.display = 'none';
		document.getElementById('specialsep').style.display = 'block';
		document.getElementById('specialbutton').value = 'Expand';
		set_cookie('showspecial','0',365,'/','.voxcharta.org','');
	}
}

function toggleabstracts() {
	var disp = 'block'
	var cook = '1'
	var pad = '10';
	if (document.getElementById('ticheck').checked == true) {
		disp = 'none';
		cook = '0';
		pad = '50';
	}

	var postinfos = document.getElementsByClassName('additional-info');
	for (i = 0; i < postinfos.length; i++) {
		document.getElementById(postinfos[i].id).style.display = disp;
	}
	set_cookie('showabstracts',cook,365,'/','.voxcharta.org','');
}

function togglenew(){
	if (document.getElementById('newsection').style.display == 'none') {
		document.getElementById('newsection').style.display = 'block';
		document.getElementById('newsep').style.display = 'none';
		document.getElementById('newbutton').value = 'Collapse';
		set_cookie('shownew','1',365,'/','.voxcharta.org','');
	} else {
		document.getElementById('newsection').style.display = 'none';
		document.getElementById('newsep').style.display = 'block';
		document.getElementById('newbutton').value = 'Expand';
		set_cookie('shownew','0',365,'/','.voxcharta.org','');
	}
}

function togglecross(){
	if (document.getElementById('crosssection').style.display == 'none') {
		document.getElementById('crosssection').style.display = 'block';
		document.getElementById('crosssep').style.display = 'none';
		document.getElementById('crossbutton').value = 'Collapse';
		set_cookie('showcro','1',365,'/','.voxcharta.org','');
	} else {
		document.getElementById('crosssection').style.display = 'none';
		document.getElementById('crosssep').style.display = 'block';
		document.getElementById('crossbutton').value = 'Expand';
		set_cookie('showcro','0',365,'/','.voxcharta.org','');
	}
}

function togglereplace(){
	if (document.getElementById('replacesection').style.display == 'none') {
		document.getElementById('replacesection').style.display = 'block';
		document.getElementById('replacesep').style.display = 'none';
		document.getElementById('replacebutton').value = 'Collapse';
		set_cookie('showrep','1',365,'/','.voxcharta.org','');
	} else {
		document.getElementById('replacesection').style.display = 'none';
		document.getElementById('replacesep').style.display = 'block';
		document.getElementById('replacebutton').value = 'Expand';
		set_cookie('showrep','0',365,'/','.voxcharta.org','');
	}
}

function toggleParent(update, disable) {
	if (!update) var update = true;
	if (!disable) var disable = false;
	var state = document.getElementById('ascheck').checked;
	document.getElementById('cocheck').checked = state;
	document.getElementById('epcheck').checked = state;
	document.getElementById('gacheck').checked = state;
	document.getElementById('hecheck').checked = state;
	document.getElementById('imcheck').checked = state;
	document.getElementById('srcheck').checked = state;
	toggleCat(update, disable);
}

function disableElements(state) {
	if (!state) var state = true;
	document.getElementById('ascheck').disabled = state;
	document.getElementById('cocheck').disabled = state;
	document.getElementById('epcheck').disabled = state;
	document.getElementById('gacheck').disabled = state;
	document.getElementById('hecheck').disabled = state;
	document.getElementById('imcheck').disabled = state;
	document.getElementById('srcheck').disabled = state;
	document.getElementById('gqcheck').disabled = state;
	document.getElementById('hpcheck').disabled = state;
	document.getElementById('htcheck').disabled = state;
	document.getElementById('hlcheck').disabled = state;
	document.getElementById('hxcheck').disabled = state;
	document.getElementById('ntcheck').disabled = state;
	document.getElementById('nxcheck').disabled = state;
	document.getElementById('cpcheck').disabled = state;
	document.getElementById('sucheck').disabled = state;
	document.getElementById('ticheck').disabled = state;
}

function toggleCat(update, disable) {
	if (!update) var update = true;
	if (!disable) var disable = false;

	if (disable) {
		disableElements();
	}

	var state = document.getElementById('cocheck').checked;
	if (document.getElementById('epcheck').checked == state &&
		document.getElementById('gacheck').checked == state &&
		document.getElementById('hecheck').checked == state &&
		document.getElementById('imcheck').checked == state &&
		document.getElementById('srcheck').checked == state) {
		document.getElementById('ascheck').checked = state;
	}
	var containers = document.getElementsByClassName('container');
	ncnt = 0;
	ccnt = 0;
	rcnt = 0;
	tncnt = 0;
	tccnt = 0;
	trcnt = 0;
	var allcats = ['as','co','ep','ga','he','im','sr','gq','hp','ht','hl','hx','nt','nx'];
	var catparent = ['','as','as','as','as','as','as','','','','',''];
	for (i = 0; i < containers.length; i++) {
		var disp = 'block';
		var catstr = document.getElementById(containers[i].id).getAttribute('categories');
		var cats = catstr.split("|");
		// Hide everything first
		for (j = 0; j < allcats.length; j++) {
			if (cats.indexOf(allcats[j]) != -1) disp = 'none';
		}
		// Now show things that satisfy certain criteria
		for (j = 0; j < allcats.length; j++) {
			if (catparent[j] == '') {
				has_children = false;
				for (k = 0; k < catparent.length; k++) {
					if (catparent[k] == allcats[j]) {
						has_children = true;
						break;
					}
				}
				if (!has_children) {
					if (cats.indexOf(allcats[j]) != -1 && document.getElementById(allcats[j]+'check').checked == true) {
						disp = 'block';
					}
				//		broke = false;
				//		for (k = 0; k < allcats.length; k++) {
				//			if (catparent[k] != '' || k == j) continue;
				//			if (cats.indexOf(allcats[k]) != -1) {
				//				broke = true;
				//				break;
				//			}
				//		}
				//		if (!broke) disp = 'block';
				//	}
				}
			} else {
				if (cats.indexOf(allcats[j]) != -1 && document.getElementById(allcats[j]+'check').checked == true) {
					for (k = 0; k < allcats.length; k++) {
						if (catparent[j] != allcats[k] && cats.indexOf(allcats[k]) == -1) disp = 'block';
					}
				}
			}
		}

		//if (cats.indexOf('co') != -1 && (cats.indexOf('hp') == -1 || cats.indexOf('gq') == -1 || cats.indexOf('as') != -1) && document.getElementById('cocheck').checked == true) disp = 'block';
		//if (cats.indexOf('ep') != -1 && (cats.indexOf('hp') == -1 || cats.indexOf('gq') == -1 || cats.indexOf('as') != -1) && document.getElementById('epcheck').checked == true) disp = 'block';
		//if (cats.indexOf('ga') != -1 && (cats.indexOf('hp') == -1 || cats.indexOf('gq') == -1 || cats.indexOf('as') != -1) && document.getElementById('gacheck').checked == true) disp = 'block';
		//if (cats.indexOf('he') != -1 && (cats.indexOf('hp') == -1 || cats.indexOf('gq') == -1 || cats.indexOf('as') != -1) && document.getElementById('hecheck').checked == true) disp = 'block';
		//if (cats.indexOf('im') != -1 && (cats.indexOf('hp') == -1 || cats.indexOf('gq') == -1 || cats.indexOf('as') != -1) && document.getElementById('imcheck').checked == true) disp = 'block';
		//if (cats.indexOf('sr') != -1 && (cats.indexOf('hp') == -1 || cats.indexOf('gq') == -1 || cats.indexOf('as') != -1) && document.getElementById('srcheck').checked == true) disp = 'block';
		//if (cats.indexOf('gq') != -1 && cats.indexOf('as') == -1 && cats.indexOf('hp') == -1 && document.getElementById('gqcheck').checked == true) disp = 'block';
		//if (cats.indexOf('hp') != -1 && cats.indexOf('as') == -1 && cats.indexOf('gq') == -1 && document.getElementById('hpcheck').checked == true) disp = 'block';

		if (cats.indexOf('cp') != -1 && document.getElementById('cpcheck').checked == false) disp = 'none';
		if (cats.indexOf('su') != -1 && document.getElementById('sucheck').checked == false) disp = 'none';

		if (cats.indexOf('new') != -1) {
			tncnt++;
			if (disp == 'block') ncnt++;
		}
		if (cats.indexOf('cro') != -1) {
			tccnt++;
			if (disp == 'block') ccnt++;
		}
		if (cats.indexOf('rep') != -1) {
			trcnt++;
			if (disp == 'block') rcnt++;
		}
		document.getElementById(containers[i].id).style.display = disp;
	}
	var ncntstr;
	if (ncnt == tncnt) {
		ncntstr = tncnt + '';
	} else {
		ncntstr = ncnt+'/'+tncnt;
	}
	if (tncnt != 0) {
		if (ncnt == 0 && tncnt != 0) {
			document.getElementById('nallhide').style.display = 'block';
		} else {
			document.getElementById('nallhide').style.display = 'none';
		}
		document.getElementById('nfrac').innerHTML = ncntstr;
	}
	var ccntstr;
	if (ccnt == tccnt) {
		ccntstr = tccnt + '';
	} else {
		ccntstr = ccnt+'/'+tccnt;
	}
	if (tccnt != 0) {
		if (ccnt == 0) {
			document.getElementById('callhide').style.display = 'block';
		} else {
			document.getElementById('callhide').style.display = 'none';
		}
		document.getElementById('cfrac').innerHTML = ccntstr;
	}
	var rcntstr;
	if (rcnt == trcnt) {
		rcntstr = trcnt + '';
	} else {
		rcntstr = rcnt+'/'+trcnt;
	}
	if (trcnt != 0) {
		if (rcnt == 0) {
			document.getElementById('rallhide').style.display = 'block';
		} else {
			document.getElementById('rallhide').style.display = 'none';
		}
		document.getElementById('rfrac').innerHTML = rcntstr;
	}
	if (update) updateCatCookie();
}

function toggleSidebar(logged_in, nonce){
  var inner;
  var toggle;
  if(document.getElementById('sidebardiv').style.display == 'none'){
    document.getElementById('sidebardiv').style.display = 'block';
    document.getElementById('maindiv').style.width = '70%';
	inner = '<a href="javascript:;" onclick="toggleSidebar('+logged_in+', \''+nonce+'\');">Hide';
	toggle = '1';
  }else{
    document.getElementById('sidebardiv').style.display = 'none';
    document.getElementById('maindiv').style.width = '100%';
	inner = '<a href="javascript:;" onclick="toggleSidebar('+logged_in+', \''+nonce+'\');">Show';
	toggle = '0';
  }
  inner += ' Sidebar</a> | <a href="http://voxcharta.org/wp-login.php';
  if (logged_in) {
  	inner += '?action=logout&_wpnonce='+nonce+'">Log out</a> | <a href="http://voxcharta.org/wp-admin/">Site admin</a>';
  } else {
  	inner += '">Log in</a> | <a href="http://voxcharta.org/wp-login.php?action=register">Register</a>';
  }
  document.getElementById('sidebartoggle').innerHTML = inner;
  set_cookie('showsidebar',toggle,365,'/','.voxcharta.org','');
}

function updateCatCookie() {
	//if (document.getElementById('cocheck') == null || document.getElementById('epcheck') == null ||
	//    document.getElementById('gacheck') == null || document.getElementById('hecheck') == null ||
	//    document.getElementById('imcheck') == null || document.getElementById('srcheck') == null ||
	//    document.getElementById('gqcheck') == null ||
	//    document.getElementById('cpcheck') == null || document.getElementById('sucheck') == null) return;
	var catvis = ""
		+ ((document.getElementById('ascheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('cocheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('epcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('gacheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('hecheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('imcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('srcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('gqcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('hpcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('htcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('hlcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('hxcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('cpcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('sucheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('ntcheck').checked) ? 1 : 0) + ','
		+ ((document.getElementById('nxcheck').checked) ? 1 : 0);
	set_cookie('catvis',catvis,365,'/','.voxcharta.org','');
}

function changeSort(today, ishome) {
	var baseurl = '/wp-content/themes/arclite';
	var sortval = document.getElementById('sortdrop').options[document.getElementById('sortdrop').selectedIndex].value;

	disableElements();

	set_cookie('sortval',sortval,365,'/','.voxcharta.org','');
	if (sortval == 'votehistory' || sortval == 'ivotehistory' || sortval == 'evotehistory' || sortval == 'vc' || sortval == 'evc') {
		var orderval = 'DESC';
	} else if (sortval == 'postnum' || sortval == 'alpha') {
		var orderval = 'ASC';
	} else {
		var orderval = document.getElementById('orderdrop').options[document.getElementById('orderdrop').selectedIndex].value;
	}
	set_cookie('orderval',orderval,365,'/','.voxcharta.org','');
	lg_AJAXsort(baseurl, today, ishome);
}

function changeSortOrder(today, ishome) {
	var baseurl = '/wp-content/themes/arclite';
	var orderval = document.getElementById('orderdrop').options[document.getElementById('orderdrop').selectedIndex].value;
	set_cookie('orderval',orderval,365,'/','.voxcharta.org','');
	lg_AJAXsort(baseurl, today, ishome);
}

function showHiddenAgendaItems()
{
	var containers = document.getElementsByClassName('hiddenitem');
	document.getElementById('hiddenagendalink').style.display = 'none';
	document.getElementById('hiddenagendaspace').style.padding = '0px 30px 11px';
	document.getElementById('visibleagendaspace').style.padding = '10px 30px 0px';
	for (i = containers.length - 1; i >= 0; i--) {
		containers[i].className = 'visibleitem';
	}
}

onload=setPageSettings;
