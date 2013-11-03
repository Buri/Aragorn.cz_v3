<?php
$error = $ok = 0;
$info = "";

if (($link != "diskuze") || ($slink != "ad")) {
	Header ("Location: $inc/diskuze/");
	exit;
}
if (!$LogedIn || !isSet($_POST['akce_tema'])) {
	Header ("Location: $inc/diskuze/");
	exit;
}

$sel_spr = mysql_query("SELECT count(id) FROM 3_diskuze_prava WHERE id_user = '$_SESSION[uid]' AND prava = 'admin'");
$out_spr = mysql_fetch_row($sel_spr);
$counter = 0;
if($out_spr[0]>0){
	if(isSet($_POST['d_tema'])){
		if ($_POST['akce_tema'] == "schvalit") {
			while (list($ind,$hod) = each($_POST['d_tema'])){
				$hledej = addslashes($hod);
				$tema_idS = mysql_query("SELECT id,nazev,nazev_rew,owner FROM 3_diskuze_topics WHERE nazev_rew = '$hledej'");
				$tema_idEx = mysql_fetch_row($tema_idS);
				if ($tema_idEx[0]>0) {
					$mess = "Vaše diskuze <a href='/diskuze/$tema_idEx[2]/'>"._htmlspec(stripslashes($tema_idEx[1]))."</a> byla schválena administrátorem $_SESSION[login].";
					sysPost($tema_idEx[3], $mess);
					mysql_query ("UPDATE 3_diskuze_topics SET schvaleno = '1', schvalenotime = '$time' WHERE id = '$tema_idEx[0]'");
					$counter++;
				}
				$hledej = "";
			}
		}
		elseif ($_POST['akce_tema'] == "smazat") {
			while (list($ind,$hod) = each($_POST['d_tema'])){
				$hledej = addslashes($hod);
				$tema_idS = mysql_query("SELECT t.id,t.nazev,t.nazev_rew,t.owner,u.login FROM 3_diskuze_topics AS t LEFT JOIN 3_users AS u ON u.id = t.owner WHERE t.nazev_rew = '$hledej'");
				$tema_idEx = mysql_fetch_row($tema_idS);
				if ($tema_idEx[0]>0) {
					$mess = "Vaše diskuze "._htmlspec(stripslashes($tema_idEx[1]))." byla smazána administrátorem $_SESSION[login].";
					if (isset($_POST['text_postolka'])){
						if (strlen($_POST['text_postolka'])>1){
							$mess .= " <br /> "._htmlspec($_POST['text_postolka']);
						}
					}
					sysPost($tema_idEx[3], $mess);
					if (isset($_POST['sendInfo']) && $_POST['sendInfo'] == "yes") {
						sysPost($_SESSION['uid'], "System INFO: Diskuze "._htmlspec(stripslashes($tema_idEx[1]))." ~ $tema_idEx[4] ~ smazána.");
					}
					mysql_query ("DELETE FROM 3_diskuze_topics WHERE id = '$tema_idEx[0]'");
					$counter++;
				}
				$hledej = "";
			}
		}
		elseif ($_POST['akce_tema'] != ""){
			$oblast = addslashes($_POST['akce_tema']);
			$tema_okruhyS = mysql_query("SELECT id FROM 3_diskuze_groups WHERE nazev_rew = '$oblast'");
			if (mysql_num_rows($tema_okruhyS)>0) {
				$oblastID = mysql_fetch_row($tema_okruhyS);
				while (list($ind,$hod) = each($_POST['d_tema'])){
					$hledej = addslashes($hod);
					$tema_idS = mysql_query("SELECT count(id) FROM 3_diskuze_topics WHERE nazev_rew = '$hledej'");
					$tema_idEx = mysql_fetch_row($tema_idS);
					if ($tema_idEx[0]>0) {
						mysql_query ("UPDATE 3_diskuze_topics SET okruh = '$oblastID[0]' WHERE nazev_rew = '$hledej'");
						$counter++;
					}
					$hledej = "";
				}
			}
		}
		else {
			Header ("Location: $inc/$link/ad/");
			exit;
		}
	}
	else {
		Header ("Location: $inc/$link/ad/");
		exit;
	}
}
else {
	Header ("Location: $inc/$link/");
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
