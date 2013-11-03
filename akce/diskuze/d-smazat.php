<?php
$error = $ok = 0;
$info = "";
if ($sslink != "admin" || $link != "diskuze" || $slink == "" || $dFound == false) {
	Header ("Location: $inc/diskuze/");
	exit;
}
if (!$LogedIn || !isSet($_POST['akce_tema'])) {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}
$Allow = GetPravaHere($dItem->id, $dItem->owner, $dItem->prava_reg, $dItem->prava_guest, $LogedIn);
if ($Allow != "superall") {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}
$smaz_ok = $_POST['akce_tema'];
if ($smaz_ok == "unlock") {
	mysql_query("UPDATE 3_diskuze_topics SET closed = '0' WHERE id = '$dItem->id'");
	Header ("Location: $inc/diskuze/$slink/admin/?info=2");
	exit;
}
elseif ($smaz_ok == "lock") {
	mysql_query("UPDATE 3_diskuze_topics SET closed = '1' WHERE id = '$dItem->id'");
	Header ("Location: $inc/diskuze/$slink/admin/?info=2");
	exit;
}
elseif($smaz_ok == "delete") {
	mysql_query("DELETE FROM 3_diskuze_topics WHERE id = '$dItem->id'");
	mysql_query("DELETE FROM 3_diskuze_prava WHERE id_dis = '$dItem->id'");
	mysql_query("DELETE FROM 3_comm_3 WHERE aid = '$dItem->id'");
	mysql_query("DELETE FROM 3_visited_3 WHERE aid = $dItem->id");
	mysql_query("OPTIMIZE TABLE 3_comm_3, 3_diskuze_topics, 3_diskuze_prava, 3_visited_3");
	if ($dItem->owner != $_SESSION['uid'] && $dItem->owner > 1) {
		sysPost($dItem->owner,"Administrátor $_SESSION[login] smazal diskuzi "._htmlspec($dItem->nazev).".");
	}
	Header ("Location: $inc/diskuze/?info=10");
	exit;
}
else {
	Header ("Location: $inc/diskuze/$slink/admin/");
	exit;
}
?>
