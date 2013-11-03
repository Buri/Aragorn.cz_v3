<?php
$error = $ok = 0;

if (!$LogedIn || $hFound != true || !isSet($_GET['c']) || !isSet($_POST['smazat_jeskyni'])) {
	header ("Location: $inc/herna/");
	exit;
}
if ($sslink != "pj") {
	header ("Location: $inc/herna/$slink/");
	exit;
}
if (($_GET['c'] == md5("c-".$hItem->id."-".$hItem->uid."-".$_SESSION['uid'])) && ($_POST['smazat_jeskyni'] == "ano") && ($hItem->uid == $_SESSION['uid'])) {
	// smazani jeskyne
	$hraciSrc = mysql_query("SELECT uid,ico FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->id'");
	if (mysql_num_rows($hraciSrc)>0) {
		$hraci = array();
		while ($hrac = mysql_fetch_row($hraciSrc)) {
			$hraci[] = $hrac[0];
			if ($hrac[1] != "" && $hrac[1] != 'default.jpg') {
				@unlink("./system/icos/$hrac[1]");
			}
		}
		$texte = "Pán jeskyně - <a href='/uzivatele/$hItem->vlastnik_rew'>$hItem->vlastnik</a> - smazal svou jeskyni <strong>$hItem->nazev</strong>. Vaše postava tak byla <strong>zabita</strong>.";
		sysPost($hraci,$texte);
		if ($hItem->ico != "" && $hItem->ico != 'default.jpg') {
			@unlink("./system/icos/$hItem->ico");
		}
	}

	$mapSrc = mysql_query("SELECT soubor FROM 3_herna_maps WHERE cid = $hItem->id AND soubor != 'js'");
	if (mysql_num_rows($mapSrc)>0) {
		while ($mapa = mysql_fetch_row($mapSrc)) {
			@unlink("./system/mapy/$mapa[0]");
		}
	}
	mysql_query("DELETE FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->id'");
	mysql_query("DELETE FROM 3_herna_maps WHERE cid = '$hItem->id'");
	mysql_query("DELETE FROM 3_comm_4 WHERE aid = '$hItem->id'");
	mysql_query("DELETE FROM 3_visited_4 WHERE aid = '$hItem->id'");
	mysql_query("DELETE FROM 3_herna_sets_open WHERE cid = '$hItem->id'");
	mysql_query("DELETE FROM 3_cave_users WHERE cid = '$hItem->id'");
	mysql_query("DELETE FROM 3_cave_mess WHERE cid = '$hItem->id'");
	mysql_query("DELETE FROM 3_herna_all WHERE id = '$hItem->id' AND uid = '$_SESSION[uid]'");
	if ($hItem->ico != "" && $hItem->ico != 'default.jpg') {
		@unlink("./system/icos/$hItem->ico");
	}

	header ("Location: $inc/herna/my/?ok=5");
	exit;

}
else {
	header ("Location: $inc/herna/$slink/$sslink/");
	exit;
}

?>
