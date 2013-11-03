<?php
$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
if ($sslink != "shop") {
	header ("Location: $inc/herna/$slink/");
	exit;
}
switch ($_GET['c']) {
	case "e":
		if ($_GET['k'] != md5("c-".$_SESSION['uid']."-".$hItem->id) || $_SESSION['uid'] != $hItem->uid) {
			header ("Location: $inc/herna/$slink/$sslink/");
			exit;
		}
		else {
			mysql_query("UPDATE 3_herna_all SET shoped = '' WHERE id = $hItem->id AND uid = '$_SESSION[uid]'");
		}
		header ("Location: $inc/herna/$slink/$sslink/");
		exit;
	break;
	case "a":
		if ($_GET['k'] != md5("c-".$_SESSION['uid']."-".$hItem->id) || $_SESSION['uid'] != $hItem->uid) {
			header ("Location: $inc/herna/$slink/$sslink/");
			exit;
		}
		switch ($_GET['h']) {
			case "1":
				mysql_query("UPDATE 3_herna_all SET obchod = '1' WHERE id = '$hItem->id' AND uid = '$_SESSION[uid]'");
				header ("Location: $inc/herna/$slink/$sslink/");
				exit;
			break;
			case "0":
				mysql_query("UPDATE 3_herna_all SET obchod = '0' WHERE id = '$hItem->id' AND uid = '$_SESSION[uid]'");
				header ("Location: $inc/herna/$slink/$sslink/");
				exit;
			break;
			default:
				header ("Location: $inc/herna/$slink/$sslink/");
				exit;
			break;
		}
	break;
	case "c":
		if (!isSet($_GET['v']) || !isSet($_POST['new_cena_zl']) || !isSet($_POST['new_cena_st']) || !isSet($_POST['new_cena_md']) || $_SESSION['uid'] != $hItem->uid) {
			header ("Location: $inc/herna/$slink/$sslink/?error=3");
			exit;
		}
		if (!ctype_digit($_GET['v']) || !ctype_digit($_POST['new_cena_zl']) || !ctype_digit($_POST['new_cena_st']) || !ctype_digit($_POST['new_cena_md'])) {
			header ("Location: $inc/herna/$slink/$sslink/$error=2");
			exit;
		}
		if ($hItem->shoped != "") {
			$obchodEdSrc = explode("*",$hItem->shoped);
			$obchodEd = array();
			for ($i=0;$i<count($obchodEdSrc);$i++){
				$oneItem = explode("/",$obchodEdSrc[$i]);
				$obchodEd[$oneItem[0]] = $oneItem[1];
			}
		}
		else {
			$obchodEd = array();
		}
		$obchodEd[$_GET['v']] = $_POST['new_cena_zl']+$_POST['new_cena_st']*0.1+$_POST['new_cena_md']*0.01;
		$txt = array();
		while (list ($key, $val) = each ($obchodEd)) {
			$txt[] = $key."/".round($val,2);
		}
		$txt = join("*",$txt);
		mysql_query("UPDATE 3_herna_all SET shoped = '$txt' WHERE id = $hItem->id AND uid = '$_SESSION[uid]'");
		header ("Location: $inc/herna/$slink/shop/");
		exit;
	break;
	case "z":
		if (!isSet($_GET['v']) || $_SESSION['uid'] != $hItem->uid) { header ("Location: $inc/herna/$slink/$sslink/"); exit;
		}
		if (!ctype_digit($_GET['v'])) { header ("Location: $inc/herna/$slink/$sslink/"); exit;
		}
		if ($hItem->shoped != "") {
			$obchodEdSrc = explode("*",$hItem->shoped);
			$obchodEd = array();
			for ($i=0;$i<count($obchodEdSrc);$i++){
				$oneItem = explode("/",$obchodEdSrc[$i]);
				$obchodEd[$oneItem[0]] = $oneItem[1];
			}
		}
		else {
			$obchodEd = array();
		}
		$obchodEd[$_GET['v']] = -1;
		$txt = array();
		while (list ($key, $val) = each ($obchodEd)) {
			$txt[] = "$key/$val";
		}
		$txt = join("*",$txt);
		mysql_query("UPDATE 3_herna_all SET shoped = '$txt' WHERE id = $hItem->id AND uid = '$_SESSION[uid]'");
		header ("Location: $inc/herna/$slink/shop/");
		exit;
	break;
	case "n":
		if (!isSet($_GET['v']) || $_SESSION['uid'] != $hItem->uid) { header ("Location: $inc/herna/$slink/$sslink/"); exit;
		}
		if (!ctype_digit($_GET['v'])) { header ("Location: $inc/herna/$slink/$sslink/"); exit;
		}
		if ($hItem->shoped != "") {
			$obchodEdSrc = explode("*",$hItem->shoped);
			$obchodEd = array();
			for ($i=0;$i<count($obchodEdSrc);$i++){
				$oneItem = explode("/",$obchodEdSrc[$i]);
				$obchodEd[$oneItem[0]] = $oneItem[1];
			}
		}
		else {
			$obchodEd = array();
		}
		unset($obchodEd[$_GET['v']]);
		$txt = array();
		while (list ($key, $val) = each ($obchodEd)) {
			$txt[] = "$key/$val";
		}
		$txt = join("*",$txt);
		mysql_query("UPDATE 3_herna_all SET shoped = '$txt' WHERE id = $hItem->id AND uid = '$_SESSION[uid]'");
		header ("Location: $inc/herna/$slink/shop/");
		exit;
	break;
	default:
		header ("Location: $inc/herna/$slink/$sslink/");
		exit;
	break;
}

?>
