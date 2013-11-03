<?php
$noOutputBuffer = true;

$get_l = "";
$G_qn = 0;

if (isset($_GET['l'])) {
	$get_l = $_GET['l'];
}

require "../db/conn.php";

$time = time();
if (!isset($_SESSION['uid']) || !isset($_SESSION['lvl']) || !isset($_SESSION['login'])) {
  echo "<script type=\"text/javascript\">window.parent.location.href=\"/\";</script>";
  die ("<span style='color: red'>Chat je pristupny pouze prihlasenym uzivatelum serveru Aragorn.cz.</span>");
  exit;
}


$id = "";
$id = addslashes($_GET['id']);
$wrongCave = true;
$jeCo = 'g';

	$SEZENI = $_SESSION;
	session_write_close();
	$dgjRS56VdcvTOvz = "_SESSION";
	$$dgjRS56VdcvTOvz = $SEZENI;

$G_qn++;
$b = mysql_query("SELECT h.id,h.nazev,h.nazev_rew,h.uid,h.typ,u.login FROM 3_herna_all AS h LEFT JOIN 3_users AS u ON u.id = h.uid WHERE h.nazev_rew = '$id' AND h.schvaleno = '1'");
$G_qn++;
if (mysql_num_rows($b)>0) {
	$wrongCave = false;
	$cave = mysql_fetch_object($b);
	if ($cave->typ=='0') {
		$jTypString = "drd";
	}else {
		$jTypString = "orp";
	} 
	if ($_SESSION['uid'] == $cave->uid) {
		$jeCo = 'p';
	}
	else {
		$c = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE cid = '$cave->id' AND uid = $_SESSION[uid] AND schvaleno = '1'"));
		$G_qn++;
		if ($c[0]=='1') {
			$jeCo = 'h';
		}
		else {
			$d = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_pj WHERE cid = '$cave->id' AND uid = $_SESSION[uid] AND schvaleno = '1' AND prispevky = '1'"));
			if ($d[0] == 1) {
				$jeCo = 'p';
			}
		}
	}
}

$ms = "";
$get_set = 0;
$get_adm = 0;

if (isset($_GET['admin']) && !$wrongCave) {
	$get_adm = $_GET['admin'];
}

$admin = false;
if (!$wrongCave && $jeCo=='p' && $get_adm>0) {
	$admin = true;
}
if (!$wrongCave && $jeCo == 'p' && (isSet($_GET['del']) || isSet($_POST['del']))) {
	if (is_array($_POST['del'])) {
	  $delA = array();
		foreach ($_POST['del'] as $v) {
			if(ctype_digit($v)){
				$delA[]=$v;
			}
		}
		$delA = join(",",$delA);
		mysql_query("DELETE FROM 3_cave_mess WHERE id IN ($delA)");
		header("Location: /cave-c/game.php?id=$id&admin=1");
		exit;
	}
	elseif (ctype_digit($_GET['del'])) {
	  $delA = addslashes($_GET['del']);
		mysql_query("DELETE FROM 3_cave_mess WHERE id = '$delA'");
		header("Location: /cave-c/game.php?id=$id&admin=1");
		exit;
	}
}

if ($get_l == "k6" || $get_l == "k10" || $get_l == "k100" || $get_l == "k20" || $get_l == "2k6plus" || $get_l == "4k6" || $get_l == "XkY") {
	switch ($get_l) {
		case "k6":
			$dice = "na šestistěnné kostce (k6): <b>".mt_rand(1,6)."</b>";
		break;
		case "k10":
			$dice = "na desetistěnné kostce (k10): <b>".mt_rand(1,10)."</b>";
		break;
		case "k20":
			$dice = "na dvacetistěnné kostce (k20): <b>".mt_rand(1,20)."</b>";
		break;
		case "2k6plus":
			$dice = "při hodu 2k6+ : ";
			$a = mt_rand(1,6); $b = mt_rand(1,6); $c = 0; $d = array();
			if (($a+$b) == 12) { // padly 2 sestky
				$c = mt_rand(1,6);
				while ($c >= 4) {
					$d[] = $c."(+1)";
					$c = mt_rand(1,6);
				}
			}elseif (($a+$b) == 2) { // padly 2 jednicky
				$c = mt_rand(1,6);
				while ($c <= 3) {
					$d[] = $c."(-1)";
					$c = mt_rand(1,6);
				}
			}
			if (count($d)>0) {
				if ($a+$b == 2) {
					$dice .= "$a + $b a pak ".join(", ",$d).", $c(0) = <b>".(($a+$b)-count($d))."</b>";
				}else {
					$dice .= "$a + $b a pak ".join(", ",$d).", $c(0) = <b>".(($a+$b)+count($d))."</b>";
				}
			}elseif ($c > 0) {
				$dice .= "$a + $b a $c(0) = <b>".($a+$b)."</b>";
			}else {
				$dice .= "$a + $b = <b>".($a+$b)."</b>";
			}
		break;
		case "4k6":
			$dice = "při hodu 4k6 (Fate): <b>";
			$d = array();
			for($a=0;$a<4;$a++) {
				$b = mt_rand(1, 6);
				$dice .= "$b, ";
				$d[] = ($b > 4 ? '+' : ($b < 3 ? '-' : '0'));
			}
			$dice .= " (" . join(", ", $d) . ")</b>";
		break;
		case "k100":
			$dice = "na procentuální kostce (k%): <b>".mt_rand(1,100)."%</b>";
		break;
		case "XkY":
			if ( isset($_GET['x']) && isset($_GET['y']) ) {
				$x = $_GET['x'];
				$y = $_GET['y'];
				if (ctype_digit($x) && ($x > 0) && ctype_digit($y) && ($y > 0) && ($x <= 10) && ($y <= 10000)) {
					$d = array();
					for ($i = 0; $i < $x; $i++) {
						$d[] = mt_rand(1,$y);
					}
					if ($x > 1) {
						$dice = "při <b>$x hodech</b> na rozsahu <b>1&hellip;$y</b> byly hodnoty <b>".join(", ",$d)."</b>.";
					}
					else {
						$dice = "na rozsahu <b>1&hellip;$y</strong> hodnotu <b>$d[0]</b>.";
					}
				}
				else {
					$dice = "";
				}
			}
			else {
				$dice = "";
			}
		break;
	}

	if ($jeCo == 'p' && $dice != "") {
		$text = addslashes("Váš hod $dice");
	 	mysql_query ("INSERT into 3_cave_mess (uid, cid, time, text, komu) values (0, $cave->id, $time, '$text', '#$_SESSION[uid]#')");
	}
	elseif ($jeCo == 'h' && $dice != "") {
		$text = addslashes("Hod $dice");
	 	mysql_query ("INSERT into 3_cave_mess (uid, cid, time, text, komu, komuText) values (0, $cave->id, $time, '$text', '#$_SESSION[uid]#$cave->uid#', '".addslashes($_SESSION['login'].", ".$cave->login)."')");
	}

	echo "<script type=\"text/javascript\">window.location.href=\"/cave-c/game.php?id=$cave->nazev_rew\";</script>\n\n";
	exit;
}

//vykop, pokud neni v chat_users
$vu = mysql_query ("SELECT c.*,u.chat_ref,u.chat_sys,u.chat_order,u.chat_time,u.chat_font FROM 3_cave_users AS c LEFT JOIN 3_users AS u ON u.id = c.uid WHERE c.uid = '$_SESSION[uid]' AND c.cid = '$cave->id'");
if (mysql_num_rows($vu) < 1 || $wrongCave){
  echo "<script type=\"text/javascript\">window.parent.location.href=\"/herna/$id/\";</script>";
    die ("<span style='color: white'>Do teto mistnosti nejste prihlasen(a).</span>");
}
else {
	$user = mysql_fetch_object($vu);
}

$ref = $user->chat_ref;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head><meta http-equiv='Content-language' content='cs' /><meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' /><meta http-equiv='pragma' content='no-cache' /><meta http-equiv='Refresh' content='<?php if (!$admin) {echo $ref;}else {echo "9999";} ?>;game.php?id=<?php echo $id;?>' /><meta name='description' content='Aragorn.cz' /><title>AraCave</title><link rel="stylesheet" type="text/css" href="./style/cave.css" /></head>
<body>
<?php

if ($admin) {
?>
<script type="text/javascript">/* <![CDATA[ */
	function check_boxes(){var chbs = document.getElementsByTagName('INPUT'),chb,ab;for(ab=0;ab<chbs.length;ab++){if (chbs[ab].type.toUpperCase()=="CHECKBOX"){chbs[ab].checked = !chbs[ab].checked;}}}
/* ]]> */</script>
<?php

echo "<form name='del-frm' id='del-frm' method='post' action='game.php?id=$id'><p><a href='/cave-c/game.php?id=$id'>Zpět na normální výpis</a> &nbsp; &nbsp; <a href='javascript:check_boxes()'>Zaškrtnout/Odškrtnout Vše</a> &nbsp; <input type='submit' value='Smazat zaškrtnuté' /></p>\n";
}

?>
<div class='game' style='font-size:<?php echo $user->chat_font;?>px'>
<?php

//seznam nicku na septani
/*
	$KomuSeptani = array();
	$cKomuS = mysql_query("SELECT DISTINCT(komu) FROM 3_cave_mess WHERE cid = $cave->id AND komu != '' AND (uid = $_SESSION[uid] OR komu LIKE '%#$_SESSION[uid]#%') LIMIT 40");
	if (mysql_num_rows($cKomuS) > 0) {
		$KomuSeptaniS = array();
		$KomuSeptani = array();
		while ($cKomu = mysql_fetch_object($cKomuS)) {
			$KomuSeptaniS[] = $cKomu->komu;
		}
		mysql_free_result($cKomuS);
		for ($i=0;$i<count($KomuSeptaniS);$i++) {
			$whispersA = explode("#",substr($KomuSeptaniS[$i],1,-1));
			for ($a=0;$a<count($whispersA);$a++) {
				if (!in_array($whispersA[$a],$KomuSeptani)) {
					$KomuSeptani[] = $whispersA[$a];
				}
			}
		}
		$KomuSeptani = array_unique($KomuSeptani);
		$KomuSeptaniIDs = join(",", $KomuSeptani);
		$komuSeptalS = mysql_query ("SELECT id, login, login_rew FROM 3_users WHERE id IN ($KomuSeptaniIDs) ORDER BY login_rew ASC");
		if (mysql_num_rows($komuSeptalS)>0) {
			while ($komuSeptal = mysql_fetch_object($komuSeptalS)) {
				$KomuSeptani[$komuSeptal->id] = $komuSeptal->login;
			}
			mysql_free_result($komuSeptalS);
		}
		else {
			$KomuSeptani = ""; $KomuSeptani = array();
		}
	}
*/

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

$s_m = mysql_query ("SELECT u.id, u.login, u.chat_color, c.komu, c.uid, c.text, c.time, c.komuText, c.id AS messid FROM 3_cave_mess AS c, 3_users AS u WHERE u.id = c.uid AND c.cid = '$cave->id' AND (c.komu = '' OR c.komu LIKE '%#$_SESSION[uid]#%' OR c.uid = $_SESSION[uid]) ORDER BY c.time DESC, c.id DESC LIMIT 40");

while ($cm = mysql_fetch_object($s_m)){
	$text = stripslashes($cm->text);
	$aw = $cm->komu;
	$ax = $cm->uid;
	if ($cm->komu != "") {
		if ($cm->komu == "#".$_SESSION['uid']."#")
			$chatName = "$cm->login -&gt; $_SESSION[login]";
		else
			$chatName = "$cm->login -&gt; $cm->komuText";
//		$chatName = "$cm->login -&gt; ".preloz_ids($KomuSeptani,$cm->komu);
	}else{
	  $chatName = $cm->login;
	}
	
	if ($user->chat_time > 0){
	  $t = "(".date("H:i", $cm->time).") ";
	}else{
	  $t = "";
	}
	
	$messAdmin = ""; 
	if ($admin) {
		$messAdmin = "<input type='checkbox' name='del[]' value='$cm->messid' /> <a href='/cave-c/game.php?id=$id&amp;del=$cm->messid'>x</a> ";
	}
	if ($ax > 0){
	  $message = "<div>".$messAdmin.$t."<span style='color: $cm->chat_color;'><span class='ch-bold'>$chatName</span>: $text</span></div>\n";
	}
	elseif ($ax == 0 && $cm->komu != "") {
	  $message = "<div>".$messAdmin.$t."<span style='color: #CEBE09'><span class='ch-bold'>$chatName</span>: $text</span></div>\n";
	}
	elseif ($user->chat_sys > 0){
	  $message = "<div>".$messAdmin."<span style='color: #CEBE09; font-size: 10px'><span class='ch-bold'>Systém</span>: $text</span></div>\n";
	}else{
	  $message= "";
	}
	
	if (mb_strlen($message) > 0){
	  if ($user->chat_order == "desc"){
	    $ms .= $message;
	  }else{
	    $ms = $message.$ms;
	  }
	}
}

echo $ms;
?>
</div>
<?php

if ($admin) {
	echo "<input type='submit' value='Smazat zaškrtnuté' /></form>\n";
}

//najeti dolu
if ($user->chat_order == "asc"){
echo "
<script type=\"text/javascript\">
window.scrollBy(0,document.body.parentNode.clientHeight)
</script>
";
}
?>
</body></html>