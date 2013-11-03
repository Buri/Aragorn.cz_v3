<?php
$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
if (!isset($_POST['uzivatel']) || !isset($_POST['akce_hrac'])) {
	header ("Location: $inc/herna/$slink/");
	exit;
}
if (($allowsPJ['postavy'] || $hItem->uid == $_SESSION['uid']) && $hItem->schvaleno == '1') {
	switch ($_POST['akce_hrac']) {
		case "y":
		case "n":
			$akce = $_POST['akce_hrac'];
		break;
		default :
			$akce = "";
		break;
	}
	if ($akce == "") {
		header ("Location: $inc/herna/$slink/pj/");
		exit;
	}
	else {
		$hraciN = array();
		while (list($key,$val)=each($_POST['uzivatel'])) {
			$hraciN[] = addslashes(trim($val));
		}
		if (count($hraciN) == 0) {
			header ("Location: $inc/herna/$slink/pj/");
			exit;
		}

		$hraciNs = join("','",$hraciN);
		$hraci = $icos = array();
		$hraciSsrc = mysql_query ("SELECT u.id,h.ico FROM 3_users AS u, 3_herna_postava_$jTypString AS h WHERE h.cid = $hItem->id AND u.id = h.uid AND u.login_rew IN ('$hraciNs')");

		if (mysql_num_rows($hraciSsrc)>0) {
			while ($hrac = mysql_fetch_row($hraciSsrc)) {
				$hraci[] = $hrac[0];
				$icos[] = $hrac[1];
			}
		}
		else {
			header ("Location: $inc/herna/$slink/pj/");
			exit;
		}
		if ($akce == "y") {
			$hraciS = join("','",$hraci);
			$pocetAktiv = mysql_fetch_row(mysql_query ("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE cid = $hItem->id AND schvaleno = '1' AND uid NOT IN ('$hraciS')"));
			if ($pocetAktiv[0] + count($hraci) <= $hItem->hraci_pocet) {
				mysql_query("UPDATE 3_herna_postava_$jTypString SET schvaleno = '1' WHERE cid = $hItem->id AND uid IN ('$hraciS')");
				$texte = "Vaše postava v jeskyni <a href='/herna/$hItem->nazev_rew/'>$hItem->nazev</a> byla <strong>schválena</strong> Pánem jeskyně = <a href='/uzivatele/$_SESSION[login_rew]/'>$_SESSION[login]</a>.<br />Příjemnou zábavu při hře.";
				sysPost($hraci,$texte);
			}
			else {
				header ("Location: $inc/herna/$slink/pj/#hraci");
				exit;
			}
		}
		else {
			$hraciS = join("','",$hraci);
			mysql_query("UPDATE 3_herna_postava_$jTypString SET schvaleno = '0' WHERE cid = $hItem->id AND uid IN ('$hraciS')");
			$texte = "Vaše postava v jeskyni <a href='/herna/$hItem->nazev_rew/'>$hItem->nazev</a> byla <strong>odhlášena</strong> Pánem jeskyně = <a href='/uzivatele/$_SESSION[login_rew]/'>$_SESSION[login]</a>.";
			sysPost($hraci,$texte);
		}
		header ("Location: $inc/herna/$slink/pj/#hraci");
		exit;
	}
}
else {
	header ("Location: $inc/herna/$slink/");
	exit;
}

?>
