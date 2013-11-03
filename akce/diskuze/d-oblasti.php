<?php
$error = $ok = 0;
$info = "";

if (($link != "diskuze") || ($slink != "ad")) {
	Header ("Location: $inc/diskuze/");
	exit;
}
if (!$LogedIn || !isSet($_POST['nazev_oblast']) || !isSet($_POST['popis_oblast'])) {
	Header ("Location: $inc/diskuze/");
	exit;
}

$sel_spr = mysql_query("SELECT count(id) FROM 3_diskuze_prava WHERE id_user = '$_SESSION[uid]' AND id_dis = '0' AND prava = 'admin'");
$out_spr = mysql_fetch_row($sel_spr);
$counter = 0;
if($out_spr[0]>0){
	$counter = 0;
	if ($_POST['akce_oblast'] == "zalozit") {
			$nazev_new = addslashes(trim($_POST['nazev_oblast']));
			$popis_new = addslashes(trim($_POST['popis_oblast']));
			$nazev_rew_new = do_seo_advanced(trim($_POST['nazev_oblast']));
			if (strlen($nazev_new)>=3 && strlen($nazev_rew_new)>=3 && strlen($popis_new)>=3) {
				$tema_idS = mysql_query("SELECT count(id) FROM 3_diskuze_groups WHERE nazev_rew = '$nazev_rew_new'");
				$tema_id = mysql_fetch_row($tema_idS);
				if ($tema_id[0]==0) {
					mysql_query ("INSERT INTO 3_diskuze_groups (nazev_rew, nazev, popis) VALUES ('$nazev_rew_new','$nazev_new','$popis_new')");
					$counter++;
				}
			}
	}
	elseif($_POST['akce_oblast'] == "upravit"){
		$hledane_tema = addslashes(trim($_POST['d_oblast']));
		$tema_idS = mysql_query("SELECT count(id) FROM 3_diskuze_groups WHERE nazev_rew = '$hledane_tema'");
		$tema_id = mysql_fetch_row($tema_idS);
		if ($tema_id[0]>0) {
			if ($_POST['akce_oblast'] == "upravit") {
				$nazev_new = addslashes(trim($_POST['nazev_oblast']));
				$popis_new = addslashes(trim($_POST['popis_oblast']));
				$nazev_rew_new = do_seo_advanced(trim($_POST['nazev_oblast']));
				if (mb_strlen($nazev_new,"UTF-8")>=3 && mb_strlen($nazev_rew_new,"UTF-8")>=3 && mb_strlen($popis_new,"UTF-8")>=3) {
					mysql_query ("UPDATE 3_diskuze_groups SET nazev = '$nazev_new', nazev_rew = '$nazev_rew_new', popis = '$popis_new' WHERE nazev_rew = '$hledane_tema'");
					$counter++;
				}
			}
		}
	}
	else {
		Header ("Location: $inc/diskuze/ad/");
		exit;
	}
}
else {
	Header ("Location: $inc/diskuze/");
	exit;
}
if ($counter > 0){
	Header ("Location: $inc/$link/ad/?info=2");
	exit;
}
else {
	Header ("Location: $inc/$link/ad/");
	exit;
}

?>
