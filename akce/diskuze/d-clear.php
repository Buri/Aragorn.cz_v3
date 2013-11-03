<?php
$error = $ok = 0;

if (!$LogedIn || $dFound !== true) {
	header ("Location: $inc/diskuze/");
	exit;
}
$sel_spr = mysql_query("SELECT count(id) FROM 3_diskuze_prava WHERE id_user = '$_SESSION[uid]' AND ((prava = 'moderator' AND id_dis = '$dItem->id') OR (id_dis = '0' AND prava = 'admin')) ORDER BY id_dis ASC LIMIT 1");
$out_spr = mysql_fetch_row($sel_spr);
if(($dItem->owner == $_SESSION['uid']) || ($out_spr[0]>0)){
  $error = 1;
	if (md5("clear-".$dItem->id."-".$dItem->owner."-".$_SESSION['uid']) == $_GET['c'] && isset($_POST['clr_rok']) && isset($_POST['clr_den']) && isset($_POST['clr_mesic'])) {
		if (ctype_digit($_POST['clr_rok']) && $_POST['clr_rok'] >= 2006 && $_POST['clr_rok'] <= date("Y") && ctype_digit($_POST['clr_mesic']) && $_POST['clr_mesic'] >= 1 && $_POST['clr_mesic'] <= 12) {
			$dayCnt = date("t",mktime(2,2,2,$_POST['clr_mesic'],3,$_POST['clr_rok']));
			if ($_POST['clr_den'] >= 1 && $_POST['clr_den'] <= $dayCnt && ctype_digit($_POST['clr_den'])) {
			  $error = 0;
			  $promaz = mktime(0,0,1,$_POST['clr_mesic'],$_POST['clr_den'],$_POST['clr_rok']);
			}
		}
	}

	if ($error < 1) {
	  mysql_query("DELETE FROM 3_comm_3 WHERE aid = '$dItem->id' AND time <= $promaz");
		header ("Location: $inc/diskuze/$slink/admin/?ok=20");
		exit;
	}
	else {
		header ("Location: $inc/diskuze/$slink/admin/#promazani");
		exit;
	}
}
else {
	header ("Location: $inc/diskuze/$slink/");
	exit;
}

?>
