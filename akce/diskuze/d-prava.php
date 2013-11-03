<?php
if (($sslink != "admin") || ($link != "diskuze") || ($slink == "")) {
	Header ("Location: $inc/diskuze/");
	exit;
}
if (!$LogedIn || !isSet($_POST['novy_uzivatel']) || !isSet($_POST['prava_nastav'])) {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}

$sel_spr = mysql_query("SELECT id FROM 3_diskuze_prava WHERE id_user = '$_SESSION[uid]' AND ((prava = 'moderator' AND id_dis = '$dItem->id') OR (id_dis = '0' AND prava = 'admin')) ORDER BY id_dis ASC LIMIT 1");
$out_spr = mysql_fetch_row($sel_spr);
if(($dItem->owner == $_SESSION['uid']) || ($out_spr[0]>0)){

	switch ($_POST['prava_nastav']) {
		case "cist":
			$typ_prav = "reader";
		break;
		case "psat":
			$typ_prav = "writer";
		break;
		case "zakazat":
			$typ_prav = "hide";
		break;
		case "smazat":
			$typ_prav = "delete";
		break;
		default:
			$typ_prav = "";
		break;
	}

	if (isSet($_POST['prava_uzivatel']) && $typ_prav != "") {
		while (list($ind,$hod) = each($_POST['prava_uzivatel'])){
			$uzivatel_idS = mysql_query("SELECT u.id FROM 3_users AS u, 3_diskuze_prava AS p WHERE u.login_rew = '$hod' AND u.id = p.id_user AND p.id_dis = '$dItem->id' AND p.prava != 'moderator' AND p.prava != 'admin' AND u.id != $dItem->owner");
			if (mysql_num_rows($uzivatel_idS)>0) {
				$uzivatel_id = mysql_fetch_object($uzivatel_idS);
				if ($typ_prav == "delete") {
					mysql_query ("DELETE FROM 3_diskuze_prava WHERE id_dis = '$dItem->id' AND id_user='$uzivatel_id->id' AND prava != 'admin'");
				}
				elseif($typ_prav != "") {
					mysql_query ("UPDATE 3_diskuze_prava SET prava = '$typ_prav' WHERE id_dis = '$dItem->id' AND id_user = '$uzivatel_id->id' AND prava != 'moderator' AND prava != 'admin'");
				}
			}
		}
	}
	elseif (($_POST['novy_uzivatel'] != "") && ($typ_prav != "") && ($typ_prav != "delete")) {
		$deleni = explode(",",$_POST['novy_uzivatel']);
		$d_count = count($deleni);
		for ($i = 0; $i < $d_count; $i++){
			$name = addslashes(trim($deleni[$i]));
			$sel_topic = mysql_query ("SELECT count(u.id) FROM 3_diskuze_topics AS t, 3_users AS u WHERE t.id='$dItem->id' AND t.owner=u.id AND u.login = '$name'");
			$out_topic = mysql_fetch_row($sel_topic);
			$sel_spravci=mysql_query ("SELECT count(u.id) FROM 3_diskuze_prava AS p, 3_users AS u WHERE u.login='$name' AND u.id = p.id_user AND p.prava = 'moderator' AND p.id_dis = '$dItem->id'");
			$out_spravci=mysql_fetch_row($sel_users);
			$sel_users = mysql_query ("SELECT id AS uid FROM 3_users WHERE login='$name'");
			if ((mysql_num_rows($sel_users)>0) && ($out_spravci[0]==0) && ($out_topic[0]==0)){
				$out_user = mysql_fetch_object($sel_users);
				$jsou_prava = mysql_query("SELECT prava FROM 3_diskuze_prava WHERE id_dis = '$dItem->id' AND id_user='$out_user->uid' AND prava != 'moderator'");
				if (mysql_num_rows($jsou_prava)<1) {
					mysql_query("DELETE FROM 3_diskuze_prava WHERE id_dis = $dItem->id AND prava != 'admin' AND id_user = '$out_user->uid'");
					mysql_query("INSERT INTO 3_diskuze_prava (id_user, prava, id_dis) values ('$out_user->uid','$typ_prav','$dItem->id')");
				}
				else {
					mysql_query ("UPDATE 3_diskuze_prava SET prava = '$typ_prav' WHERE id_dis = '$dItem->id' AND id_user = '$out_user->uid')");
				}
			}
		}
	}
	Header("Location: $inc/$link/$slink/admin/?info=8");
exit;
}
else {
	Header("Location: $inc/$link/$slink/");
}

?>
