<?php
$error = $ok = 0;
$info = "";

if ($sslink != "admin" || $link != "diskuze" || $slink == "") {
	Header ("Location: $inc/diskuze/");
	exit;
}
if (!$LogedIn || !isSet($_POST['novy_vlastnik']) || !isSet($_POST['novy_vlastnik2'])) {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}
$Allow = GetPravaHere($dItem->id, $dItem->owner, $dItem->prava_reg, $dItem->prava_guest, $LogedIn);
if ($Allow != "superall") {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}
if ($_POST['novy_vlastnik'] != $_POST['novy_vlastnik2']) {
	Header ("Location: $inc/diskuze/$slink/admin/");
	exit;
}

$vlastnik_new = addslashes(trim($_POST['novy_vlastnik']));

$vlastnik_s = mysql_query("SELECT id FROM 3_users WHERE login = '$vlastnik_new'");

if (mysql_num_rows($vlastnik_s)>0) {
	$vlastnik = mysql_fetch_row($vlastnik_s);
	mysql_free_result($vlastnik_s);
	if (($dItem->owner != $vlastnik[0])) {
		mysql_query("UPDATE 3_diskuze_topics SET owner = '$vlastnik[0]' WHERE id = '$dItem->id'");
		mysql_query("DELETE FROM 3_diskuze_prava WHERE id_user = '$vlastnik[0]' AND id_dis = '$dItem->id'");
		Header ("Location: $inc/diskuze/$slink/admin/?info=9");
		exit;
	}
}
else {
	Header ("Location: $inc/diskuze/$slink/admin/");
	exit;
}

?>
