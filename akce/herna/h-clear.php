<?php
$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
if ($hItem->uid == $_SESSION['uid']) {
  $error = 1;
	if (md5("clear-".$hItem->id."-".$hItem->uid."-".$_SESSION['uid']) == $_GET['c'] && isset($_POST['clr_rok']) && isset($_POST['clr_den']) && isset($_POST['clr_mesic'])) {
		if (ctype_digit($_POST['clr_rok']) && $_POST['clr_rok'] >= 2006 && $_POST['clr_rok'] <= date("Y") && ctype_digit($_POST['clr_mesic']) && $_POST['clr_mesic'] >= 1 && $_POST['clr_mesic'] <= 12) {
			$dayCnt = date("t",mktime(2,2,2,$_POST['clr_mesic'],3,$_POST['clr_rok']));
			if ($_POST['clr_den'] >= 1 && $_POST['clr_den'] <= $dayCnt && ctype_digit($_POST['clr_den'])) {
			  $error = 0;
			  $promaz = mktime(0,0,1,$_POST['clr_mesic'],$_POST['clr_den'],$_POST['clr_rok']);
			}
		}
	}
	
	if ($error < 1) {
	  mysql_query("DELETE FROM 3_comm_4 WHERE aid = $hItem->id AND time <= $promaz");
		header ("Location: $inc/herna/$slink/pj/?ok=1");
		exit;
	}
	else {
		header ("Location: $inc/herna/$slink/pj/#promazani");
		exit;
	}
}
else {
	header ("Location: $inc/herna/$slink/");
	exit;
}

?>
