<?php
global $arxiv_cats, $arxiv_cnums, $arxiv_cat_slugs, $arxiv_cat_abbrv, $arxiv_cat_kind, $arxiv_cat_parents, $arxiv_has_children;

$arxiv_cats = array('astro-ph','gr-qc','hep-ph','hep-th','hep-lat','hep-ex','nucl-th','nucl-ex');
$arxiv_cnums = array(7, 748669, 748671, 820242, 820243, 748672, 748676, 748673); //Need to change numbers to match new categories -- JFG

$arxiv_cat_slugs = array('astro-ph','cosmology-extragalactic-astro-ph',
	'earth-planetary-astro-ph','galactic-astro-ph',
	'high-energy-astro-ph','instrumentation-methods-astro-ph',
	'solar-stellar-astro-ph',
	'gr-qc','hep-ph','hep-th','hep-lat','hep-ex',
	'conference-proceeding','submitted','nucl-th','nucl-ex');
$arxiv_cat_abbrv = array('as','co','ep','ga','he','im','sr','gq','hp','ht','hl','hx','cp','su','nt','nx');
$arxiv_cat_titles = array('astro-ph','co','ep','ga','he','im','sr','gr-qc','hep-ph','hep-th','hep-lat','hep-ex','cp','su','nucl-th','nucl-ex');
$arxiv_cat_kind = array(0,1,1,1,1,1,1,0,0,0,0,0,2,2,0,0);
$arxiv_cat_parents = array('','as','as','as','as','as','as','','','','','','','','','');
$arxiv_has_children = array_fill(0, count($arxiv_cat_abbrv), false);
foreach ($arxiv_cat_slugs as $slug) {
	$cat = get_category_by_slug($slug);
	$arxiv_cat_nums[] = $cat->term_id;
}
foreach ($arxiv_cat_abbrv as $i => $ca) {
	if (in_array($ca, $arxiv_cat_parents)) $arxiv_has_children[$i] = true;
}

function get_parent_nums ($ids) {
	global $arxiv_cat_nums, $arxiv_cat_parents, $arxiv_cat_abbrv;
	$pids = array();
	foreach ($ids as $id) {
		$loc = array_search($id, $arxiv_cat_nums);
		if ($loc === false) continue;
		$par = $arxiv_cat_parents[$loc];
		if ($par == '') {
			$pids[] = $id;
		} else {
			$loc = array_search($par, $arxiv_cat_abbrv);
			$pids[] = $arxiv_cat_nums[$loc];
		}
	}
	return array_unique($pids);
}
?>
