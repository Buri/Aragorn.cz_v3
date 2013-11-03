<?php
$error = $ok = 0;
$info = "";

if ($sslink != "admin" || $link != "diskuze" || $slink == "") {
	Header ("Location: $inc/diskuze/");
	exit;
}
if (!$LogedIn || !isSet($_POST['nazev']) || !isSet($_POST['popis']) || !isSet($_POST['nastenka']) || !isSet($_POST['registrovani']) || !isSet($_POST['hoste'])) {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}
$sel_spr = mysql_query("SELECT id, prava FROM 3_diskuze_prava WHERE id_user = '$_SESSION[uid]' AND ((prava = 'moderator' AND id_dis = '$dItem->id') OR (id_dis = '0' AND prava = 'admin')) ORDER BY id_dis ASC LIMIT 1");
$out_spr = mysql_fetch_row($sel_spr);
if(($dItem->owner == $_SESSION['uid']) || ($out_spr[0]>0)){

	if ($out_spr[0] > 0) { // jen admin muze menit nazev diskuze
		$nazev_new = addslashes(trim($_POST['nazev']));
		$nazev_rew_new = do_seo(trim($_POST['nazev']));
	}
	else {
		$nazev_new = addslashes($dItem->nazev);
		$nazev_rew_new = $slink;
	}
	$popis_new = addslashes(trim($_POST['popis']));
	$nastenka_new = strtr(trim($_POST['nastenka']),$changeToXHTML);
//	$nastenka_new = remove_HTML($nastenka_new, "a|abbr|acronym|address|area|b|big|blockquote|br|caption|center|cite|code|col|colgroup|dd|del|dfn|dir|div|dl|dt|em|font|h1|h2|h3|h4|h5|h6|hr|i|iframe|img|ins|li|map|object|ol|p|param|pre|q|s|samp|small|span|strike|strong|sub|sup|table|tbody|td|tfoot|th|thead|tr|tt|u|ul|var|xmp");
	$nastenka_new = " nastenka = '".addslashes($nastenka_new)."', nastenka_compressed = 0 ";

	switch ($_POST['hoste']) {
		case "cist":
			$guest_new = "read";
		break;
		case "skryt":
			$guest_new = "hide";
		break;
		default:
			$guest_new = "read";
		break;
	}
	switch ($_POST['registrovani']) {
		case "oboje":
			$reg_new = "write";
		break;
		case "cist":
			$reg_new = "read";
		break;
		case "skryt":
			$reg_new = "hide";
		break;
		default:
			$reg_new = "write";
		break;
	}
	
	if (strlen($nazev_rew_new)>40 || strlen($nazev_rew_new)<=3) {
		$info = 4;
	}
	elseif (mb_strlen($popis_new)>255){
		$info = 5;
	}
	
	if ($slink != $nazev_rew_new) {
		$kontrola_nazvu = mysql_query("SELECT * FROM 3_diskuze_topics WHERE nazev_rew = '$nazev_rew_new'");
		if (mysql_num_rows($kontrola_nazvu)>0) {
			$info = 3;
		}
	}
	$oblastSql = "";
	if ($info == "" && $out_spr[1] == 'admin' && isset($_POST['oblast']) && ctype_digit($_POST['oblast'])){
		$checkOblastSql = "SELECT count(*) FROM 3_diskuze_groups WHERE id = '$_POST[oblast]'";
		$checkOblast = mysql_fetch_row(mysql_query($checkOblastSql));
		if ($checkOblast[0] == 1) {
			$oblastSql = "okruh = '$_POST[oblast]', ";
		}
	}
	if ($info == ""){
		mysql_query("UPDATE 3_diskuze_topics SET $oblastSql nazev = '$nazev_new', nazev_rew = '$nazev_rew_new', popis = '$popis_new', prava_reg = '$reg_new', prava_guest = '$guest_new', $nastenka_new WHERE id = '$dItem->id'");
		Header ("Location: $inc/diskuze/$nazev_rew_new/admin/?info=6");
		exit;
	}
	else {
		Header ("Location: $inc/diskuze/$slink/admin/?info=$info");
		exit;
	}
}
else {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}
?>
