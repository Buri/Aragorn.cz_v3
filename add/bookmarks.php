<?php

	$runBookmarks = true;

	$addZalNmb = 0;
	$zF = "";
	$zFInPage = "";

	if ($runBookmarksList) {
		$theListTags = array("<ul>", "<li>", "</li>", "</ul>", "", "");
		$theListNameTrimLength = 100;
	}
	else {
		$theListTags = array("", "", "", "");
		$theListNameTrimLength = 23;
	}

	if (isset($_REQUEST['heads'])) {
		$bookmarkLinkAddon = "?_r=1";
	}
	else {
		$bookmarkLinkAddon = "";
		$theListTags[4] = "<h6>";
		$theListTags[5] = "</h6>";
	}


//if ($_SESSION['uid'] == 2) {
	$sql = "SELECT x.id, x.nazev, x.nazev_rew, v.news AS count_new 
		FROM 3_visited_1 v, 3_clanky x  
		WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
		ORDER BY x.nazev_rew ASC";
/*}
else {
	$sql = "SELECT x.id, x.nazev, x.nazev_rew, (SELECT COUNT(*) FROM 3_comm_1 c WHERE c.aid = v.aid AND c.id > v.lastid) AS count_new 
		FROM 3_visited_1 v, 3_clanky x  
		WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
		ORDER BY x.nazev_rew ASC";
}
*/

	//clanky
	$sBC = mysql_query ($sql);
	if ($sBC && mysql_num_rows($sBC)>0) {
		if ($title || isset($_REQUEST['heads'])) {
			$zF .= "<h5><a class='hlight3' href='/clanky/'>• • • ".($itIsApril ? 'Pisálkova suť' : 'Články')." • • •</a></h5>\n";
			$zFInPage .= "<div class='diskuze-okruh'><a class='hlight3' href='/clanky/'>Záložky v sekci Články</a></div>\n";
		}
		$zFInPage .= $theListTags[0];
		while ($oBC = mysql_fetch_object($sBC)){
			$naz = $nazL = stripslashes($oBC->nazev);
			$naz = mb_strimwidth($naz, 0, $theListNameTrimLength, "...");
			$cm = getHighlight($oBC->count_new);
			if ($cm != "") {$addZalNmb++;}

			$zF .= $theListTags[4]."<a href='/clanky/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Články : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>".$theListTags[5];

			$zFInPage .= $theListTags[1];
			$zFInPage .= "<a href='/clanky/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Články : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>";
			$zFInPage .= $theListTags[2];
		}
		$zFInPage .= $theListTags[3];
	}
	
	//galerie

//if ($_SESSION['uid'] == 2){
	$sql = "SELECT x.id, x.nazev, x.nazev_rew, v.news AS count_new 
		FROM 3_visited_2 v, 3_galerie x  
		WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
		ORDER BY x.nazev_rew ASC";
/*}
else {
	$sql = "SELECT x.id, x.nazev, x.nazev_rew,(SELECT COUNT(*) FROM 3_comm_2 c WHERE c.aid = v.aid AND c.id > v.lastid) AS count_new 
		FROM 3_visited_2 v, 3_galerie x  
		WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
		ORDER BY x.nazev_rew ASC";
}	
*/

	$sBC = mysql_query ($sql);
	if ($sBC && mysql_num_rows($sBC)>0) {
		if ($title || isset($_REQUEST['heads'])) {
			$zF .= "<h5><a class='hlight3' href='/galerie/'>• • • ".($itIsApril ? 'Omalovánky' : 'Galerie')." • • •</a></h5>\n";
			$zFInPage .= "<div class='diskuze-okruh'><a class='hlight3' href='/galerie/'>Záložky v sekci Galerie</a></div>\n";
		}
		$zFInPage .= $theListTags[0];
		while ($oBC = mysql_fetch_object($sBC)){
			$naz = $nazL = stripslashes($oBC->nazev);
			$naz = mb_strimwidth($naz, 0, $theListNameTrimLength, "...");
			$cm = getHighlight($oBC->count_new);
			if ($cm != "") {$addZalNmb++;}

			$zF .= $theListTags[4]."<a href='/galerie/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Galerie : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>".$theListTags[5];

			$zFInPage .= $theListTags[1];
			$zFInPage .= "<a href='/galerie/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Galerie : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>";
			$zFInPage .= $theListTags[2];
		}
		$zFInPage .= $theListTags[3];
	}
	
	
	//diskuze
//if ($_SESSION['uid'] == 2){
	$sql = "SELECT x.id, x.nazev, x.nazev_rew, v.news AS count_new 
		FROM 3_visited_3 v, 3_diskuze_topics x  
		WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
		ORDER BY x.nazev_rew ASC";	
/*}
else {
	$sql = "SELECT x.id, x.nazev, x.nazev_rew, (SELECT COUNT(*) FROM 3_comm_3 c WHERE c.aid = v.aid AND c.id > v.lastid) AS count_new 
		FROM 3_visited_3 v, 3_diskuze_topics x  
		WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
		ORDER BY x.nazev_rew ASC";	
}
*/

	$sBC = mysql_query ($sql);
	if ($sBC && mysql_num_rows($sBC)>0) {
		if ($title || isset($_REQUEST['heads'])) {
			$zF .= "<h5><a class='hlight3' href='/diskuze/'>• • • ".($itIsApril ? 'Krafárna' : 'Diskuze')." • • •</a></h5>\n";
			$zFInPage .= "<div class='diskuze-okruh'><a class='hlight3' href='/diskuze/'>Záložky v sekci Diskuze</a></div>\n";
		}
		$zFInPage .= $theListTags[0];
		while ($oBC = mysql_fetch_object($sBC)){
			$naz = $nazL = stripslashes($oBC->nazev);
			$naz = mb_strimwidth($naz, 0, $theListNameTrimLength, "...");
			$cm = getHighlight($oBC->count_new);
			if ($cm != "") {$addZalNmb++;}

			$zF .= $theListTags[4]."<a href='/diskuze/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Diskuze : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>"
				.$theListTags[5];

			$zFInPage .= $theListTags[1];
			$zFInPage .= "<a href='/diskuze/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Diskuze : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>";
			$zFInPage .= $theListTags[2];
		}
		$zFInPage .= $theListTags[3];
	}
	
  //jeskyne
if (false/* && ($_SESSION['uid'] == 2 || $_SESSION['uid'] == 1990)*/) {
	$sql = "SELECT x.id, x.nazev, x.nazev_rew, v.news AS count_new 
		FROM 3_visited_4 v, 3_herna_all x  
		WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
		ORDER BY x.nazev_rew ASC";
	}
	else {
$sql = "SELECT x.nazev, x.nazev_rew,
 (SELECT COUNT(*) FROM 3_comm_4 c WHERE c.aid = v.aid AND c.id > v.lastid AND ((c.whispering LIKE '%#$_SESSION[uid]#%') OR (c.whispering IS NULL))) AS count_new 
FROM 3_visited_4 v, 3_herna_all x  
WHERE v.aid = x.id AND v.uid = ".$_SESSION['uid']." AND v.bookmark = '1'
ORDER BY x.nazev_rew ASC";		
}
	
	$sBC = mysql_query ($sql);
	if ($sBC && mysql_num_rows($sBC)>0) {
		if ($title || isset($_REQUEST['heads'])) {
			$zF .= "<h5><a class='hlight3' href='/herna/'>• • • ".($itIsApril ? 'Xbox / PS3' : 'Herna')." • • •</a></h5>\n";
			$zFInPage .= "<div class='diskuze-okruh'><a class='hlight3' href='/herna/'>Záložky v sekci Herna</a></div>\n";
		}
		$zFInPage .= $theListTags[0];
		while ($oBC = mysql_fetch_object($sBC)){
			$naz = $nazL = stripslashes($oBC->nazev);
			$naz = mb_strimwidth($naz, 0, $theListNameTrimLength, "...");
			$cm = getHighlight($oBC->count_new);
			if ($cm != "") {$addZalNmb++;}

			$zF .= $theListTags[4]."<a href='/herna/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Herna : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>".$theListTags[5];

			$zFInPage .= $theListTags[1];
			$zFInPage .= "<a href='/herna/$oBC->nazev_rew/$bookmarkLinkAddon#kom' title='Herna : "._htmlspec($nazL)."'>"._htmlspec($naz)." $cm</a>";
			$zFInPage .= $theListTags[2];
		}
		$zFInPage .= $theListTags[3];
	}

	if (strlen($zF) < 1){
		$zF .= "<a href='#' title='Prázdné záložky'>Záložky prázdné</a>";
	}
	$addZalCount = ($addZalNmb > 0) ? " <span class='c-ub'>($addZalNmb)</span>":" (0)";
?>