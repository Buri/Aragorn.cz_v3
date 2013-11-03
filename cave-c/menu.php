<?php
$noOutputBuffer = true;
session_set_cookie_params(2*3600);
session_start();
$time = time();
require "../db/conn.php";

$SEZENI = $_SESSION;
session_write_close();
$dgjRS56VdcvTOvz = "_SESSION";
$$dgjRS56VdcvTOvz = $SEZENI;

//mazani starych zprav
$timeout = $time - 60*30;

$id = "";
$id = addslashes($_GET['id']);
$wrongCave = true;
$jeCo = 'g';

$s = mysql_fetch_object(mysql_query ("SELECT * FROM 3_users WHERE id = $_SESSION[uid]"));
$b = mysql_query("SELECT id,nazev,nazev_rew,uid,typ FROM 3_herna_all WHERE nazev_rew = '$id' AND schvaleno = '1'");
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
		if ($c[0]=='1') {
			$jeCo = 'h';
		}
	}
}

?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html><head><meta http-equiv='Content-language' content='cs' /><meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' /><meta http-equiv='pragma' content='no-cache' /><meta http-equiv='Refresh' content='600' /><title>AraCave - Cave Menu</title><link rel="stylesheet" type="text/css" href="./style/cave.css" />
<script type="text/javascript">
function throw_me(i,w){window.parent.game.location.href='game.php?id='+i+'&l='+w;return false;}
function throw_dices(t){xK=prompt("Počet kostek (1 - 10)");if(xK>0&&!isNaN(xK)&&(xK>0&&xK<=10)){yK=prompt("Nejvyšší hodnota na kostce (minimální hod je napevno 1, maximální si určíte)");if(yK>0&&!isNaN(yK)&&(yK>1&&yK<=10000)){throw_me(t,'XkY&x='+xK+'&y='+yK);}}return false;}
</script>
</head>
<body>
<span class='aracave'></span>

<div class='menu'>
<div class='arag'><a href='/' title='Zpět na Aragorn.cz (otevře se v nové stránce)' target='_blank'>Aragorn.cz</a></div>
<?php
//vykop, pokud neni v cave_users
if ($wrongCave) {
  echo "<script type=\"text/javascript\">window.parent.location.href=\"/herna/\";</script>";
    die ("<span style='color: white'>Tato jeskyne neexistuje.</span>");
}
$vu = mysql_query ("SELECT * FROM 3_cave_users WHERE uid = '$_SESSION[uid]' AND cid = '$cave->id'");
if (mysql_num_rows($vu) < 1){
  echo "<script type=\"text/javascript\">window.parent.location.href=\"/herna/$id/\";</script>";
    die ("<span style='color: white'>Do teto mistnosti nejste prihlasen(a).</span>");
}
else {
	$user = mysql_fetch_object($vu);
}

if ($user->pozice != 'g') {
?>
<a href="#" onclick="return throw_me('<?php echo $id; ?>','k6');" value='Hod na klasické šestistěnné kostce'>k6</a>
<a href="#" onclick="return throw_me('<?php echo $id; ?>','k10');" value='Hod na desetistěnce'>k10</a>
<a href="#" onclick="return throw_me('<?php echo $id; ?>','k100');" title='Procentuální hod'>k%</a>
<a href="#" onclick="return throw_me('<?php echo $id; ?>','2k6plus');" title='Hod 2k6+ (hlavně pro DrD+)'>2k6+</a>
<a href="#" onclick="return throw_me('<?php echo $id; ?>','4k6');" title='Hod Fate (1,2 = minus, 3,4 = 0, 5,6 = plus)'>4k6</a>
<a href="#" onclick="return throw_me('<?php echo $id; ?>','k20');" title='Hod na dvacetistěnné kostce'>k20</a>
<a href="#" onclick="return throw_dices('<?php echo $id; ?>');" title='Hod X kostkami na rozsahu Y'>X&middot;kY</a>
<?php

}

//odpocet (cave)
function countdown($navrat){
	$navrat = date("i:s", $navrat);
	$navrat = explode (":", $navrat);
	if ($navrat[0][0] == 0){
	  $min = $navrat[0][1];
	}else{
	  $min = $navrat[0];
	}
	if ($navrat[1][0]==0){
	  $sec = $navrat[1][1];
	}else{
	  $sec = $navrat[1];
	}
	return "$min min. a $sec sec.";
}

//info o roomu
$room = $cave;

echo "<span class='nazev'>".stripslashes($room->nazev)."</span><br />";

//uzivatele cavu
$chatUsers = mysql_query ("SELECT c.*, h.jmeno, h.ico FROM 3_cave_users AS c, 3_herna_postava_$jTypString AS h WHERE h.uid = c.uid AND h.schvaleno='1' AND c.pozice = 'h' AND c.cid = h.cid AND c.cid = $cave->id ORDER BY c.login ASC");
$show_stats = "";

while ($chItem = mysql_fetch_object($chatUsers)){
	$us = "";
  $us = htmlspecialchars(stripslashes($chItem->jmeno),ENT_QUOTES);
  if (strlen($chItem->ico) > 2){
   $path = "icos/$chItem->ico";
  }else{
   $path = "icos/default.jpg";
  }
  
	$us2 = stripslashes($chItem->login);

$show_stats .= "
<table width='110' border='0' cellpadding='2' id='m$chItem->login_rew' style='display:none' class='stats'>
<tr><td>$us2</td></tr>
<tr><td>".countdown($time - $chItem->timestamp)."</td></tr>
<tr><td><img src='../system/$path' alt='$us' title='$us' /></td></tr>
</table>
";

  echo "<span class='plr' onmouseover=\"return hide('m$chItem->login_rew')\" onmouseout=\"return hide('m$chItem->login_rew')\">".$us."</span><br />";

}
$pjS = mysql_query("SELECT c.*, h.ico FROM 3_cave_users AS c, 3_herna_all AS h WHERE c.pozice = 'p' AND c.cid = $cave->id AND c.uid = h.uid AND h.id = $cave->id");
if (mysql_num_rows($pjS)>0) {
	$pj = mysql_fetch_object($pjS);
	$us2 = stripslashes($pj->login);
  if (strlen($pj->ico) > 2){
   $path = "icos/$pj->ico";
  }else{
   $path = "icos/default.jpg";
  }
$show_stats .= "
<table width='110' border='0' cellpadding='2' id='m$pj->login_rew' style='display:none' class='stats'>
<tr><td>PJ ($us2)</td></tr>
<tr><td>".countdown($time - $pj->timestamp)."</td></tr>
<tr><td><img src='../system/$path' alt='PJ' title='PJ' /></td></tr>
</table>
";
  echo "<hr /><span class='pjs' onmouseover=\"return hide('m$pj->login_rew')\" onmouseout=\"return hide('m$pj->login_rew')\">PJ (".$us2.")</span><br />";
}
?>
</div>
<a href="http://www.toplist.cz/toplist/?search=drd&amp;a=s" title="TOPlist" target="_blank"><script language="JavaScript" type="text/javascript">
<!--
document.write ('<img src="http://toplist.cz/dot.asp?id=40769&amp;http='+escape(top.document.referrer)+'" width="1" height="1" border=0 alt="TOPlist" />');
//--></script></a><noscript><img src="http://toplist.cz/dot.asp?id=40769" border="0"
alt="TOPlist" width="1" height="1" /></noscript>
<?php
echo $show_stats;
?>

<script type="text/javascript">
function hide(obj){
  if(document.getElementById(obj).style.display == "none"){
    document.getElementById(obj).style.display = "" 
  }else{
    document.getElementById(obj).style.display = "none"
  }
  return 1
}
</script>

</body>
</html>
