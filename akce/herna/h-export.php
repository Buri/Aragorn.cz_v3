<?php
$error = $ok = 0;

if (!$LogedIn || $hFound !== true || $sslink != "pj") {
	header ("Location: $inc/herna/");
	exit;
}
if($hItem->uid == $_SESSION['uid'] && isset($_GET['c']) && $_GET['c'] != "" && isSet($_POST['export_co'])) {

	if(md5("exp-".$hItem->id."-".$hItem->uid."-".$_SESSION['uid']) != $_GET['c'] || $_POST['export_co'] == "") {
		header("Location: $inc/herna/$slink/pj/");
		exit;
	}

	if (!function_exists("preloz_ids")) {
		function preloz_ids($ar_id_login,$what) {
			$t = substr($what,1,-1);
			$t = explode ("#",$t);
			$prelozeno = array();
			for ($i=0;$i<count($t);$i++) {
				$prelozeno[] = $ar_id_login[$t[$i]];
			}
			$prelozeno = join (", ",$prelozeno);
			return $prelozeno;
		}
	}


/* Export START */

	$sufix = "html";

	$cave_name = $hItem->nazev;

	if ($_POST['export_co'] == "forum"){

		$cKomuS = mysql_query("SELECT DISTINCT(whispering) FROM 3_comm_4 WHERE aid = $hItem->id AND whispering IS NOT NULL AND whispering != ''");
		if (mysql_num_rows($cKomuS) > 0) {
			$KomuSeptaniS = array(); $KomuSeptani = array();

			while ($cKomu = mysql_fetch_object($cKomuS)) $KomuSeptaniS[$cKomu->whispering] = $cKomu->whispering;
			mysql_free_result($cKomuS);

			foreach ($KomuSeptaniS as $KomuSeptaniOne) {
				$whispersA = explode("#",substr($KomuSeptaniOne,1,-1));
				for ($a=0;$a<count($whispersA);$a++) $KomuSeptani[$whispersA[$a]] = $whispersA[$a];
			}

			$KomuSeptani = array_keys($KomuSeptani);
			$KomuSeptaniIDs = join(",", $KomuSeptani);
			$komuSeptalS = mysql_query("SELECT id, login, login_rew FROM 3_users WHERE id IN ($KomuSeptaniIDs) ORDER BY login_rew ASC");

			$KomuSeptani = array();

			if(mysql_num_rows($komuSeptalS)>0){
				while ($komuSeptal = mysql_fetch_object($komuSeptalS)) $KomuSeptani[$komuSeptal->id] = $komuSeptal->login;
				mysql_free_result($komuSeptalS);
			}
			else {
				$KomuSeptani = "";
				$KomuSeptani = array();
			}
		}

		$message="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<title>$cave_name - Export fóra</title>
<style>hr{margin-botom:10px;}p{line-height:1.2;}body{background-color:#f5f5f5;color:#333;}.hlight1{color:#4D7A29;}.hlight2{color:#AC4D43;}.hlight3{color:#1D5088;}</style>
</head>
<body>
<h2>Export fóra jeskyně $cave_name (provedeno ".date("d.m.Y H:i:s",$time).")</h2><hr />";

		$sel_exp = mysql_query("SELECT p.jmeno,u.login,t.uid,ct.text_content AS text,ct.text_whisText AS whisText, t.whispering,t.time FROM 3_comm_4 AS t
LEFT JOIN 3_comm_4_texts AS ct ON ct.text_id = t.mid
LEFT JOIN 3_users AS u ON t.uid = u.id
LEFT JOIN 3_herna_postava_$jTypString AS p ON p.uid = t.uid AND p.cid = $hItem->id
WHERE t.aid = $hItem->id ORDER BY t.id ASC");

		while ( $pT = mysql_fetch_object($sel_exp) ){
//			if ($pT->compressed) $pT->text = gzuncompress($pT->text);
			if ($pT->uid == 0 && $pT->whispering != "") {

				$cN = $pT->login;

				$kostky = explode($hCh,$pT->text);
				if (count($kostky) > 1) {
					$rea = "";
					$icoHere = "";

					if ($pT->whisText != "") $Kdo = $pT->whisText;
					else $Kdo = preloz_ids($KomuSeptani,$pT->whispering);

					if (count(explode("#",substr($pT->whispering,1,-1)))>1) $Hod = mb_strpos($Kdo, ",");
					else $Hod = mb_strlen($Kdo);

					$Hod = mb_substr($Kdo,0,$Hod);
					if ($Hod == $_SESSION['login']) {
						$Hod = "Hodil(a) jsi";
					} else {
						$Hod = "<b>$Hod</b> hodil(a)";
					}
					switch ($kostky[0]) {
						case "k6": $pT->text = "$Hod na šestistěnné kostce (k6) hodnotu <b>$kostky[1]</b>."; break;
						case "k10": $pT->text = "$Hod na desetistěnné kostce (k10) hodnotu <b>$kostky[1]</b>."; break;
						case "k20": $pT->text = "$Hod na dvacetistěnné kostce (k20) hodnotu <b>$kostky[1]</b>."; break;
						case "4k6": $pT->text = "$Hod v hodu 4k6 (Fate) hodnoty <strong>$kostky[1]</strong>."; break;
						case "k%": $pT->text = "$Hod na procentuální kostce (k%) hodnotu <b>$kostky[1] %</b>."; break;
						case "kP": if (count($kostky)==4) { $pT->text = $Hod . " při hodu <b>2k6+</b> hodnoty $kostky[2] + $kostky[3] =&nbsp;<b>$kostky[1]</b>.";}elseif(count($kostky)>2) { $pT->text = $Hod . " při hodu <b>2k6+</b> hodnoty $kostky[2] + $kostky[3] a pak $kostky[4] =&nbsp;<b>$kostky[1]</b>."; } break;
						case "kX": if ($kostky[1] > 1) { $pT->text = $Hod . " při <b>$kostky[1] hodech</b> na rozsahu <b>1&hellip;$kostky[2]</b> hodnoty <b>$kostky[3]</b>."; } else { $pT->text = $Hod . " na rozsahu <b>1&hellip;$kostky[1]</b> hodnotu <b>$kostky[2]</b>."; } break;
					}
				}
				$message .= "\n<b>".$cN."</b>".$septanda." : ".$cTime."\n<p>".spit($pT->text, 1)."</p><hr />";
			}
			else {
				if ($pT->jmeno != "") $cN = "$pT->jmeno ($pT->login)";
				elseif ($pT->uid == $hItem->uid) $cN = "Pán Jeskyně ($pT->login)";
				else $cN = $pT->login;
	
				$cTime = date("d.m.Y H:i:s",$pT->time);
				$septanda = "";
	
				if ($pT->whispering != "") {
					if ($pT->whisText != "") $septanda = " &raquo; [ ".$pT->whisText." ]";
					else $septanda = " &raquo; [ ".preloz_ids($KomuSeptani,$pT->whispering)." ]";
				}
				$message .= "\n<b>".$cN."</b>".$septanda." : ".$cTime."\n<p>".spit($pT->text, 1)."</p><hr />";
			}

		}
		$message.="\n</body>\n</html>\n";

	}elseif ($_POST['export_co'] == "chat"){ //export chatu

		$KomuSeptani = array();
		$cKomuS = mysql_query("SELECT distinct(komu) FROM 3_cave_mess WHERE cid = $hItem->id AND komu != ''");
		if (mysql_num_rows($cKomuS) > 0) {
			$KomuSeptaniS = array();
			$KomuSeptani = array();
			while ($cKomu = mysql_fetch_object($cKomuS)) $KomuSeptaniS[] = $cKomu->komu;
			mysql_free_result($cKomuS);
			for ($i=0;$i<count($KomuSeptaniS);$i++) {
				$whispersA = explode("#",substr($KomuSeptaniS[$i],1,-1));
				for ($a=0;$a<count($whispersA);$a++) {
					if (!in_array($whispersA[$a],$KomuSeptani)) $KomuSeptani[] = $whispersA[$a];
				}
			}
			$KomuSeptani = array_unique($KomuSeptani);
			$KomuSeptaniIDs = join(",", $KomuSeptani);
			$komuSeptalS = mysql_query ("SELECT id, login, login_rew FROM 3_users WHERE id IN ($KomuSeptaniIDs) ORDER BY login_rew ASC");
			if (mysql_num_rows($komuSeptalS)>0) {
				while ($komuSeptal = mysql_fetch_object($komuSeptalS)) $KomuSeptani[$komuSeptal->id] = $komuSeptal->login;
				mysql_free_result($komuSeptalS);
			}
			else {
				$KomuSeptani = ""; $KomuSeptani = array();
			}
		}

		$message="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<title>$cave_name - Export chatu</title>
<style>p{line-height:1.1;}body{background-color:#000;color:#999;}</style>
</head>
<body>
<h2>Export chatu jeskyně $cave_name (provedeno ".date("d.m.Y H:i:s",$time).")</h2><hr />";

		$sel_exp = mysql_query("SELECT u.login, u.chat_color, c.komu, c.uid, c.text, c.time FROM 3_cave_mess AS c, 3_users AS u WHERE u.id = c.uid AND c.cid = '$hItem->id' ORDER BY c.id ASC");

		while ( $pT = mysql_fetch_object($sel_exp) ){
			$text = $pT->text;
			if ($pT->komu != "") $chatName = $pT->login." &raquo; ".preloz_ids($KomuSeptani,$pT->komu);
			else $chatName = $pT->login;

		  $t = "[".date("H:i", $pT->time)."] ";

			if ($pT->uid > 0) $mess = "<p>".$t."<span style='color: $pT->chat_color'><b>$chatName</b>: $text</span></p>";
			elseif ($pT->uid == 0 && $pT->komu != "") $mess = "<p>".$t."<span style='color: #CEBE09'>$chatName: $text</span></p>";
			else $mess= "";

			if (mb_strlen($mess) > 0) $message = $message."\n".$mess;
		}

		$message.="\n</body>\n</html>\n";

	}
	else {
		header ("Location: $inc/herna/$slink/pj/#export");
		exit;
	}

	//zazipovani exportu
	include_once "./add/zip.lib.php";
	$zip=new zipfile();
	$zip->addFile($message, "$hItem->nazev_rew"."_export_".$_POST['export_co'].".html");
	header("Content-Type: application/x-zip");
	header("Content-disposition: attachment; filename=$hItem->nazev_rew"."_export_".$_POST['export_co'].".zip");
	echo $zip->file();
	exit;

	/* Export  END  */
}
else {
	header ("Location: $inc/herna/$slink/pj/");
	exit;
}

?>