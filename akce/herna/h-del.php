<?php
$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
if ($sslink != "pj") {
	header ("Location: $inc/herna/$slink/");
	exit;
}
if ($hItem->uid == $_SESSION['uid'] && md5("c-".$hItem->cid."-".$hItem->uid."-".$_SESSION['uid']) == $_GET['c'] && $_POST['smazat_jeskyni'] == "delete") {
	$pjs_S = mysql_query("SELECT ico FROM 3_herna_pj WHERE cid = '$hItem->id'");
	if (mysql_num_rows($pjs_S)>0) {
		while ($pjs = mysql_fetch_row($pjs_S)) {
			if ($pjs[0] != 'default.jpg' && $pjs[0] == '') {
				@unlink("./system/icos/$pjs[0]");
			}
		}
	}

	$hraci_S = mysql_query("SELECT uid,ico FROM 3_herna_postava_$jTypString WHERE cid = $hItem->id");
	if (mysql_num_rows($hraci_S)>0) {
		$icos = array();
		$icos[] = $hItem->ico;
		while ($hrac1 = mysql_fetch_row($hraci_S)) {
			$hraciA[] = $hrac1[0];
			if ($hrac1[1]!="") { $icos[] = $hrac1[1]; }
		}
		$texte = "Pán jeskyně - <a href='/uzivatele/$hItem->vlastnik_rew' class='permalink2'>$hItem->vlastnik</a> - <strong>smazal</strong> jeskyni <strong>$hItem->nazev</strong>.<br />Vaše postava byla tímto také smazána.";
		sysPost($hraciA,$texte);
		for ($i=0;$i<count($icos);$i++) {
			if ($icos[$i] != "default.jpg") {
				@unlink("./system/icos/$icos[$i]");
			}
		}
	}

	$mapSrc = mysql_query("SELECT datas FROM 3_herna_maps WHERE cid = $hItem->id AND soubor != 'js'");
	$dbCnt++;
	if (mysql_num_rows($mapSrc)>0) {
		while ($mapa = mysql_fetch_row($mapSrc)) {
			@unlink("./system/mapy/$mapa[0]");
		}
	}

	mysql_query("DELETE FROM 3_visited_4 WHERE aid = '$hItem->id';
	DELETE FROM 3_cave_users WHERE cid = '$hItem->id';
	DELETE FROM 3_cave_mess WHERE cid = '$hItem->id';
	DELETE FROM 3_herna_all WHERE id = '$hItem->id';
	DELETE FROM 3_herna_pj WHERE cid = '$hItem->id';
	DELETE FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->cid';
	DELETE FROM 3_comm_4 WHERE aid = '$hItem->id';
	DELETE FROM 3_herna_all WHERE id = $hItem->id';
	DELETE FROM 3_herna_pj WHERE cid = $hItem->id';
	DELETE FROM 3_herna_sets_open WHERE cid = $hItem->id");
	header ("Location: $inc/herna/my/?ok=4");
}
else {
	header ("Location: $inc/herna/$slink/$sslink/");
	exit;
}

?>
