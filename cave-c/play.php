<?php
$noOutputBuffer = true;

$time = time();
$mess = $uSel = $us_js = "";
require "../db/conn.php";
$get_l = "";
$dice = $addLinker = "";
$notCross = true;
mb_internal_encoding("UTF-8");

//mazani starych zprav
$timeout = $time - 60*30;

$id = "";
$id = addslashes($_GET['id']);
$wrongCave = true;
$jeCo = 'g';

$G_qn = 0;

$b = mysql_query("SELECT id,nazev,nazev_rew,uid,typ FROM 3_herna_all WHERE nazev_rew = '$id' AND schvaleno = '1'");
$G_qn++;
if (mysql_num_rows($b)>0) {
	$wrongCave = false;
	$cave = mysql_fetch_object($b);
	$ppj = mysql_query("SELECT schvaleno FROM 3_herna_pj WHERE cid = " . $cave->id . " AND uid = " . $_SESSION['uid'] . ";");
	if ($cave->typ=='0') {
		$jTypString = "drd";
	}else {
		$jTypString = "orp";
	} 
	if ($_SESSION['uid'] == $cave->uid || mysql_num_rows($ppj) > 0) {
		$jeCo = 'p';
	}
	else {
		$c = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE cid = '$cave->id' AND uid = $_SESSION[uid] AND schvaleno = '1'"));
		$G_qn++;
		if ($c[0]=='1') {
			$jeCo = 'h';
		}
	}
}

if ($wrongCave) {
	echo "<script type=\"text/javascript\">window.parent.location.href=\"/herna/\";</script>";
		die ("<span style='color: white'>Tato jeskyne neexistuje.</span>");
}
$vu = mysql_query ("SELECT * FROM 3_cave_users WHERE uid = '$_SESSION[uid]' AND cid = '$cave->id'");
$G_qn++;
if (mysql_num_rows($vu) < 1){
	echo "<script type=\"text/javascript\">window.parent.location.href=\"/herna/$id/\";</script>";
		die ("<span style='color: white'>Do teto mistnosti nejste prihlasen(a).</span>");
}
else {
	$user = mysql_fetch_object($vu);
}

if (isset($_GET['l'])) {
	$get_l = $_GET['l'];
}

$komu = $komuStr = $komuXstr = "";
$komuRews = array();
$get_chb = false;
if ($jeCo != 'g' && isset($_POST['mess']) && $_GET['add'] > 0 && mb_strlen(trim($_POST['mess'])) > 0 && $get_l != 1){
	if (isset($_POST['to'])) {
		$komuA = $_POST['to'];
		while (list($index,$login_rews) = each($komuA)) {
			$komuRews[] = addslashes($login_rews);
		}
		$komujoin = join("','", $komuRews);
		$sql_X = "SELECT uid,login FROM 3_cave_users WHERE login_rew IN ('".$komujoin."') AND cid = $cave->id";
		$G_qn++;
		$komuIdS = mysql_query($sql_X);
		if (mysql_num_rows($komuIdS)>0) {
			$komuAs = array();
			while ($komuItem = mysql_fetch_row($komuIdS)) {
				$komuAs[$komuItem[0]] = $komuItem[0];
				$komuStr[$komuItem[0]] = $komuItem[1];
			}
			$komu = "#".join("#",$komuAs)."#";
			$komuXstr = join(",",$komuAs);
			$komuStr = join(", ",$komuStr);
		}
		else {
			$komu = "";
		}
	}
	$mess = trim($_POST['mess']);

	$text = trim(htmlspecialchars($mess,ENT_QUOTES,"UTF-8"));
	$text = addslashes($text);
	$odkoho = $_SESSION['uid'];

	if ($komu == "") {
		mysql_query ("INSERT INTO 3_cave_mess (uid, cid, time, text) VALUES ($odkoho, $cave->id, $time, '$text')");
		$G_qn++;
	}
	else {
		if(isset($_POST['trvale'])) {
			if ($_POST['trvale'] == "on") $addLinker = "&whom=".$komuXstr;
		}
		mysql_query ("INSERT INTO 3_cave_mess (uid, cid, komu, komuText, time, text) VALUES ($odkoho, $cave->id, '$komu', '".addslashes($komuStr)."', $time, '$text')");
		$G_qn++;
	}
	mysql_query ("UPDATE 3_cave_users SET timestamp = $time WHERE uid = $_SESSION[uid] and cid = $cave->id");
	$G_qn++;
	$add_sqlPJ = "";
	if ($jeCo == 'h') mysql_query ("UPDATE 3_herna_postava_$jTypString SET aktivita = $time WHERE uid = $_SESSION[uid] and cid = $cave->id");
	elseif ($jeCo == 'p') $add_sqlPJ = ", aktivitapj = $time";

	mysql_query ("UPDATE 3_herna_all SET ohrozeni='0', aktivita = $time $add_sqlPJ WHERE id = $cave->id");
	mysql_query ("UPDATE 3_users SET timestamp = $time WHERE id = $_SESSION[uid]");
	if (isset($_SESSION['updated'])){
		if ($_SESSION['updated'] + 1800 < $time ){
			session_regenerate_id();
			$_SESSION["updated"] = $time;
		}
	}
	else {
		session_regenerate_id();
		$_SESSION["updated"] = $time;
	}
	echo "<script type=\"text/javascript\">window.parent.game.location.href=\"/cave-c/game.php?id=$cave->nazev_rew\";</script>
<script type=\"text/javascript\">window.location.href=\"/cave-c/play.php?id=$cave->nazev_rew".$addLinker."\";</script>";
exit;
}

if($get_l == "1"){
	mysql_query("DELETE FROM 3_cave_users WHERE uid = '$_SESSION[uid]' AND cid = $cave->id");
	$G_qn++;
	if ($jeCo != 'g') {
		$pjadd = "";
		if ($jeCo == 'p') {
			$pjadd = "PJ ";
		}
		$text = addslashes($pjadd.$_SESSION['login']." odchází z místnosti.");
		mysql_query ("INSERT into 3_cave_mess (uid, cid, time, text) values (0, $cave->id, $time, '$text')");
		$G_qn++;
	}
	echo "<script type=\"text/javascript\">window.parent.location.href=\"/herna/$cave->nazev_rew/\";</script>";
}

	$SEZENI = $_SESSION;
	session_write_close();
	$dgjRS56VdcvTOvz = "_SESSION";
	$$dgjRS56VdcvTOvz = $SEZENI;


?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<meta http-equiv='pragma' content='no-cache' />
<title>AraCave - <?php echo _htmlspec($cave->nazev); ?></title>
<link rel="stylesheet" type="text/css" href="./style/cave.css" />
</head>
<?php 
if ($jeCo == 'g') {
	echo "<body>\n";
?>

<div class='play'>
<form action='play.php?id=<?php echo $id;?>' class='fg' name='cave' method='post' target='_self'>
<table border='0' cellpadding='2'>
<tr>
	<td valign='top'><input type='button' onClick="window.parent.location.href='/cave-c/<?php echo $id; ?>/'" value='Refresh' /><input type='button' onclick="location.href='play.php?id=<?php echo $id; ?>&amp;l=1'" value='Odejít' /></td>
</tr>
</table>
</form>
</div>


<?php
}
else {
?>
<body onLoad="document.forms['cave']['mess'].focus()">
<?php

$chatUsers = mysql_query("SELECT p.jmeno, c.login, c.login_rew, c.uid, c.pozice FROM 3_cave_users AS c
LEFT JOIN 3_herna_postava_$jTypString AS p ON ( c.uid = p.uid AND p.cid = $cave->id)
WHERE (( c.pozice != 'g') AND (c.uid != $_SESSION[uid]) AND (c.cid=$cave->id)) ORDER BY c.login ASC ");

$G_qn++;

$uSel = $whom = array();
if (isset($_GET['whom']) && $_GET['whom'] != "") {
	$whom = explode(",",$_GET['whom']);
}
elseif (isset($_POST['trvale']) && $_POST['trvale'] == "on") {
	if (isset($_POST['to'])) {
		$whom = $_POST['to'];
	}
}

$whomX = array();
while ($chItem = mysql_fetch_object($chatUsers)){
	$us = stripslashes($chItem->login);
	$us2 = htmlspecialchars(stripslashes($chItem->jmeno),ENT_QUOTES);
	$counter++;
	$check = "";
	if (in_array($chItem->uid, $whom) || in_array($chItem->login_rew,$whom)) {
	  $whomX[] = $chItem->login_rew;
		$check = "checked='checked'";
	}
	if ($chItem->pozice == 'p') {
		$uSel[] = "<input class='bt' id='chb-$chItem->uid' type='checkbox' name='to[]' value='$chItem->login_rew'$check><label for='chb-$chItem->uid'>PJ&nbsp;($us)</label>";
	}
	else {
		$uSel[] = "<input class='bt' id='chb-$chItem->uid' type='checkbox' name='to[]' value='$chItem->login_rew'$check><label for='chb-$chItem->uid'>$us2</label>";
	}
}
if (count($whomX > 0)) {
	$addLinker = "&whom=".join(",",$whomX);
}

$allowDel = "";
if ($jeCo == 'p') {
	$allowDel = "<input class='bt' type='button' value='Mazání zpráv' onclick=\"window.parent.game.location.href='game.php?id=$id&admin=1'\" />\n";
}

?>
<div class='play'>
<form action='play.php?id=<?php echo $id;?>&amp;add=1' class='fg' name='cave' method='post' target='_self'>
<table border='0' cellpadding='1'>
<tr>
	<td>
		<input type='text' name='mess' id='mess' class='txt' size='75' maxlength='1000' onkeypress="update(this.value.length);" onkeydown="update(this.value.length);" onkeyup="update(this.value.length);" />
		<input title='Trvalé šeptání' class='p-t' name='trvale' type='checkbox' value='on' <?php if(count($whom)>0) {echo " checked='checked' ";} ?> />
		<input class='bt' type='submit' value='Odeslat' />
	</td>
	<td>
		<input class='bt' type='button' onClick="window.parent.location.href='/cave/<?php echo $id; ?>/'" value='Refresh' />
		<?php echo $allowDel;?>
		<input class='bt' type='button' onClick="location.href='play.php?id=<?php echo $id; ?>&amp;l=1'" value='Odejít' />
	</td>
</tr>
<tr>
	<td class='lim'>
		<?php echo join("&nbsp; ",$uSel); ?>
	</td>
	<td class='lim'>
		<p>Zbývá znaků:&nbsp;<span id='counter'>1000</span></p>
	</td>
</tr>
</table>
</form>
</div>

<script type="text/javascript">
var limit = 1000;
function update(t) {
	document.getElementById("counter").innerHTML=""+limit-t;
}
</script>
<?php
}
?>
<a href="http://www.toplist.cz/toplist/?search=drd&amp;a=s" title="TOPlist" target="_blank"><script language="JavaScript" type="text/javascript">
<!--
document.write ('<img src="http://toplist.cz/dot.asp?id=40769&amp;http='+escape(top.document.referrer)+'" width="1" height="1" border=0 alt="TOPlist" />');
//--></script></a><noscript><img src="http://toplist.cz/dot.asp?id=40769" border="0"
alt="TOPlist" width="1" height="1" /></noscript><?php if($_SESSION['uid'] == 2 || $_SESSION['uid'] == 1990){echo "<em>_".$G_qn."_</em>";}?>
</body>
</html>
