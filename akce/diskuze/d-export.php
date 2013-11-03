<?php
$error = $ok = 0;

if (!$LogedIn || $dFound !== true || $sslink != "admin") {
	header ("Location: $inc/diskuze/");
	exit;
}
$sel_spr = mysql_query("SELECT count(*) FROM 3_diskuze_prava WHERE id_user = '$_SESSION[uid]' AND ((prava = 'moderator' AND id_dis = '$dItem->id') OR (id_dis = '0' AND prava = 'admin')) ORDER BY id_dis ASC");
$out_spr = mysql_fetch_row($sel_spr);
if(($dItem->owner == $_SESSION['uid']) || ($out_spr[0]>0)){
	if(isset($_GET['c']) && $_GET['c'] != "") {

		if(md5("exp-".$dItem->id."-".$dItem->owner."-".$_SESSION['uid']) != $_GET['c']) {
			header("Location: $inc/diskuze/$slink/admin/?errorCheck");
			exit;
		}
	
/* Export START */

		$sufix = "html";
	
		$cave_name = $dItem->nazev;
	
		$searchId = "0";
		$error = 1;
		if (ctype_digit($_POST['export_rok']) && $_POST['export_rok'] >= 2005 && $_POST['export_rok'] <= date("Y") && ctype_digit($_POST['export_mesic']) && $_POST['export_mesic'] >= 1 && $_POST['export_mesic'] <= 12) {
			$dayCnt = date("t",mktime(2,2,2,$_POST['export_mesic'],3,$_POST['export_rok']));
			if ($_POST['export_den'] >= 1 && $_POST['export_den'] <= $dayCnt && ctype_digit($_POST['export_den'])) {
			  $error = 0;
			  $promaz = mktime(0,0,1,$_POST['export_mesic'],$_POST['export_den'],$_POST['export_rok']);
			}
		}
		if ($error > 0) {
			header ("Location: $inc/diskuze/$slink/admin/?noDate");
			exit;
		}
		$searchIdS = mysql_fetch_row(mysql_query("SELECT MAX(id),MAX(time) FROM 3_comm_3 WHERE aid = $dItem->id AND time <= $promaz"));
		if ($searchIdS[0] > 0) $searchId = $searchIdS[0];

		$starsiPrispevky = "";
		if ($promaz > 0 && $searchId > 0) {
			$starsiPrispevky = ", příspěvky mladší ".date("j.n.Y H:i:s",$searchIdS[1]);
		}

		$message="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<title>$cave_name - Export diskuze</title>
<style>hr{margin-botom:5px;}p{line-height:1.2;margin:1ex;padding:0;}body{background-color:#f5f5f5;color:#333;}.hlight1{color:#4D7A29;}.hlight2{color:#AC4D43;}.hlight3{color:#1D5088;}</style>
</head>
<body>
<h2>Export diskuze $cave_name (provedeno ".date("j.n.Y H:i:s",$time)."$starsiPrispevky)</h2><hr />";

		$sel_exp = mysql_query("SELECT u.login,ct.text_content AS text,t.time FROM 3_comm_3 AS t 
		LEFT JOIN 3_comm_3_texts AS ct ON ct.text_id = t.mid
		LEFT JOIN 3_users AS u ON u.id = t.uid
WHERE t.id <= $searchId AND t.aid = $dItem->id ORDER BY t.id ASC");

		while ( $pT = mysql_fetch_object($sel_exp) ){
//			if ($pT->compressed) $pT->text = gzuncompress($pT->text);
			$cN = $pT->login;
	
			$cTime = date("d.m.Y H:i:s",$pT->time);
			$septanda = "";
	
			$message .= "\n<b>".$cN."</b>"." : ".$cTime."\n<p>".spit($pT->text, 1)."</p><hr />";
	
		}
		$message.="\n</body>\n</html>\n";
	
		//zazipovani exportu
		include_once "./add/zip.lib.php";
		$zip=new zipfile();
		$zip->addFile($message, "diskuze_$slink"."-export.html");
		header("Content-Type: application/x-zip");
		header("Content-disposition: attachment; filename=diskuze_$slink"."-export.zip");
		echo $zip->file();
		exit;
	
		/* Export  END  */
	}
	else {
		header ("Location: $inc/diskuze/$slink/admin/?falseFormData");
		exit;
	}
}
else {
	header ("Location: $inc/diskuze/$slink/admin/?noRights");
	exit;
}
?>