<?php

if (!function_exists("herna_posta")) {
	//posle postu(info) uzivateli
	function herna_posta($text,$komu) {
		global $dbCnt, $AragornCache;
		$time = time();
		$hash = addslashes(md5($text));
		$sql = "";
		$messId = 0;
		mysql_query("LOCK TABLES 3_post_text WRITE, 3_post_new WRITE");
		$jeHashS = mysql_query("SELECT id,content,compressed FROM 3_post_text WHERE hash = '$hash' ORDER BY id ASC");
		$dbCnt++;
		if ($jeHashS && mysql_num_rows($jeHashS)>0){
			while($jeHash = mysql_fetch_row($jeHashS)) {
				if ($jeHash[2]>0) $jeHash[1] = gzuncompress($jeHash[1]); 
				if ($jeHash[1] == $text) {
					$messId = $jeHash[0];
					break;
				}
			}
		}
		if ($messId == 0) {
/*
			$binarka = gzcompress($text,9);
			if (strlen($text)>strlen($binarka)) {
				$text = bin2hex($binarka);
				mysql_query("INSERT INTO 3_post_text (compressed, content, hash) VALUES ('1', 0x$text, '$hash')");
			}
			else {
*/
				$text = addslashes($text);
				mysql_query("INSERT INTO 3_post_text (compressed, content, hash) VALUES ('0','$text','$hash')");
//			}
			$dbCnt++;
			$messId = mysql_insert_id();
		}
		if (is_array($komu)) {
			$localSQL = "";
			for ($i=0,$cntr=count($komu);$i<$cntr;$i++) {
				$AragornCache->delVal("post-unread:$komu[$i]");
				$localSQL .= "INSERT INTO 3_post_new (mid, tid, fid, stavfrom, stavto, cas) VALUES ('$messId', '$komu[$i]', '0', '3', '0', '$time');\n";
				$dbCnt++;
			}
			mysql_query($localSQL);
		}
		else {
			$AragornCache->delVal("post-unread:$komu");
			mysql_query("INSERT INTO 3_post_new (mid, tid, fid, stavfrom, stavto, cas) VALUES ('$messId', '$komu', '0', '3', '0', '$time');");
			$dbCnt++;
		}
		mysql_query("UNLOCK TABLES");
	}
}

if (!function_exists("setSekce")) {
	function setSekce($set){
		$sekce = array("Povídky", "Poezie", "Úvahy", "Recenze", "Postavy", "Ostatní", "Vildovy cesty", "Rozhovory", "300 z místa", "Předměty");
		$i = 0;
		foreach ($sekce as $s){
			$o .= "<option value='$i'".(($set == $i)? " selected='selected'" : '').">$s</option>";
			$i++;
		}
		return "<select name='sekce'>".$o."</select>";
	}
}

if (!function_exists("viewSekce")) {
	function viewSekce($set){
		$sekce = array("Povídky", "Poezie", "Úvahy", "Recenze", "Postavy", "Ostatní", "Vildovy cesty", "Rozhovory", "300 z místa", "Předměty");
		$set = intval($set);
		$s = $sekce[$set];
		return $s;
	}
}

if (!function_exists("getSekceAll")) {
	function getSekceAll(){
		$sekce = array("Povídky", "Poezie", "Úvahy", "Recenze", "Postavy", "Ostatní", "Vildovy cesty", "Rozhovory", "300 z místa", "Předměty");
		return $sekce;
	}
}

function updateTimestamp(){
	global $time,$dbCnt;
	mysql_query ("UPDATE 3_users SET timestamp = $time WHERE id = '$_SESSION[uid]'");
	$dbCnt++;
}

function getHerNew(){
	global $dbCnt;
	$c = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_herna_all WHERE schvaleno = '0'") );
	$dbCnt++;
	if ($c[0]>0) $c[0] = "<b>".$c[0]."</b>";
	return $c[0];
}

function getArtNew(){
	global $dbCnt;
	$c = mysql_fetch_row ( mysql_query ("SELECT count(*) FROM 3_clanky WHERE schvaleno != '1'") );
	$dbCnt++;
	if ($c[0]>0) $c[0] = "<b>".$c[0]."</b>";
	return $c[0];
}

function getGalNew(){
	global $dbCnt;
	$c = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_galerie WHERE schvaleno = '0'") );
	$dbCnt++;
	if ($c[0]>0) $c[0] = "<b>".$c[0]."</b>";
	return $c[0];
}

function getBonus(){
	global $dbCnt;
	$dbCnt++;
	$c = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_users WHERE level = '1'") );
	return $c[0];
}

function cashForTime($c, $var){
global $time;
	$cD = (365/300)*$c;
	if ($var > 0){
		return $cD*3600*24;
	}else{
		return $time+$cD*3600*24;
	}
}
