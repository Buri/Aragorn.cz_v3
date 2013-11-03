<?php
$error = $ok = 0;
$info = "";

if (($sslink != "admin") || ($link != "diskuze") || ($slink == "")) {
	Header ("Location: $inc/diskuze/");
	exit;
}
if (!$LogedIn || !isSet($_POST['novy-spravce']) || !isSet($_POST['akce-spravce'])) {
	Header ("Location: $inc/diskuze/$slink/");
	exit;
}

$sel_spr = mysql_query("SELECT count(id) FROM 3_diskuze_prava WHERE id_user = '$_SESSION[uid]' AND ((prava = 'moderator' AND id_dis = '$dItem->id') OR (id_dis = '0' AND prava = 'admin'))");
$out_spr = mysql_fetch_row($sel_spr);
if(($dItem->owner == $_SESSION['uid']) || ($out_spr[0]>0)){
	$counter = 0;
	switch ($_POST['akce-spravce']){
		case "pridat":
			$deleni = explode(",",$_POST['novy-spravce']);
			for ($i=0;$i<count($deleni);$i++){
				$names = do_seo(trim($deleni[$i]));
				if (mb_strlen($names,"UTF-8")>0) {
					$sel_users = mysql_query ("SELECT id AS uid FROM 3_users WHERE login_rew='$names'");
					if (mysql_num_rows($sel_users)>0){
						$out_user = mysql_fetch_object($sel_users);
						$iduseru = $out_user->uid;
						$sel_topic = mysql_query ("SELECT count(id) FROM 3_diskuze_topics WHERE id = '$dItem->id' AND owner = '$iduseru' AND schvaleno = '1'");
						$out_topic = mysql_fetch_row($sel_topic);
						$sel_spravci=mysql_query ("SELECT count(id) FROM 3_diskuze_prava WHERE id_user = '$iduseru' AND ((prava = 'moderator' AND id_dis = '$dItem->id') OR (prava = 'admin' AND id_dis=0))");
						$out_spravci=mysql_fetch_row($sel_spravci);
						if (($iduseru>0) && ($out_spravci[0]=="0") && ($out_topic[0]=="0")){
							mysql_query ("DELETE FROM 3_diskuze_prava WHERE id_user = '$iduseru' AND id_dis = '$dItem->id'");
							mysql_query ("INSERT INTO 3_diskuze_prava (id_user, prava, id_dis) values ('$iduseru','moderator','$dItem->id')");
							$counter++;
						}
						mysql_free_result($sel_topic);
						mysql_free_result($sel_spravci);
						mysql_free_result($sel_users);
					}
					else {
						$iduseru = 0;
					}
				}
			}
		break;
		case "smazat":
			if(isSet($_POST['spravci_tematu'])){
				while (list($ind,$hod) = each($_POST['spravci_tematu'])){
					$spravce_idS = mysql_query("SELECT id FROM 3_users WHERE login_rew = '$hod'");
					if (mysql_num_rows($spravce_idS)>0) {
						$spravce_id = mysql_fetch_object($spravce_idS);
						mysql_query ("DELETE FROM 3_diskuze_prava WHERE id_dis = '$dItem->id' AND id_user='$spravce_id->id' AND prava = 'moderator'");
						$counter++;
					}
				}
			}
		break;
	}
}
if ($counter > 0){
	Header ("Location: $inc/$link/$slink/admin/?info=7");
	exit;
}
else {
	Header ("Location: $inc/$link/$slink/admin/");
	exit;
}

?>
