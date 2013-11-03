<?php

mb_internal_encoding("UTF-8");

$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
if ($sslink != "pj") {
	header ("Location: $inc/herna/$slink/");
	exit;
}
if ($hItem->uid == $_SESSION['uid'] && isSet($_POST['vlastnik_new']) && isSet($_POST['vlastnik_new2'])) {
	if (mb_strlen(trim($_POST['vlastnik_new']))==0) {
		$error = 6;
	}
	$jm1 = addslashes(trim($_POST['vlastnik_new']));
	$jm2= addslashes(trim($_POST['vlastnik_new2']));
	if ($jm1 != $jm2) {
		$error = 8;
	}
	elseif ($jm1 == $_SESSION['login']) {
		$error = 7;
	}
	else {
		$jeUserem = mysql_query("SELECT id, level FROM 3_users WHERE login = '$jm1'");
		if (mysql_num_rows($jeUserem)==0) {
			$error = 6;
		}
		else {
			$new_owner = mysql_fetch_object($jeUserem);
			$jeHracem = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE uid = '$new_owner->id' AND cid = '$hItem->id'"));
			if ($jeHracem[0] > 0) {
				$error = 6;
			}
			else {
				if (herna_omezeni($new_owner->id, $new_owner->level)>= $herna_nebonus) {
					$error = 9;
				}
			}
		}
	}
	if ($error == 0) {
		mysql_query("UPDATE 3_herna_all SET uid = '$new_owner->id' WHERE id = '$hItem->id' AND schvaleno = '1'");
		header ("Location: $inc/herna/my/?ok=4");
		$texte = "Uživatel(ka) ".$_SESSION['login']." Vám předal(a) vlastnictví jeskyni <a href='/herna/$hItem->nazev_rew'>$hItem->nazev</a>. Jste jejím novým majitelem a Pánem Jeskyně.";
		sysPost($new_owner->id,$texte);
		exit;
	}
	else {
		header ("Location: $inc/herna/$slink/pj/?error=$error");
		exit;
	}
}
else {
	header ("Location: $inc/herna/$slink/pj/");
	exit;
}

?>
