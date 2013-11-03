<?php
require "../db/conn.php";

$time = time();

$SEZENI = $_SESSION;
session_write_close();
$dgjRS56VdcvTOvz = "_SESSION";
$$dgjRS56VdcvTOvz = $SEZENI;

//"cron" pro chat ;)

//mazani starych zprav
$timeout = $time - 60*15;

$_GET['id'] = addslashes($_GET['id']);
  
//vyhozeni odejivsich homies
mysql_query("DELETE FROM 3_chat_users WHERE odesel = '1' AND timestamp < ($time-60*2)");

/* o ostatn ise stara http://www.aragorn.cz/cron.php */
?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<meta http-equiv='pragma' content='no-cache' />
<meta http-equiv='Refresh' content='30' />
<meta name='description' content='Aragorn.cz, chat' />
<title>Arachat</title>
<link rel="stylesheet" type="text/css" href="./style/chat.css" />
</head>
<body>

<span class='arachat'></span>

<div class='menu'>
<?php
//konec, pokud neni v chat_users

$vu = mysql_query ("SELECT * FROM 3_chat_users WHERE uid = '$_SESSION[uid]' AND rid = '$_GET[id]' AND odesel = '0'");
if (mysql_num_rows($vu) < 1){
    die ("<span style='color: red'>Do teto mistnosti nejste prihlasen(a).</span></div></body></html>");
    exit;
}
else {
	$vuItem = mysql_fetch_object($vu);
}

//odpocet (chat)
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
$room = mysql_fetch_object(mysql_query("SELECT nazev, category, locked, type, saving FROM 3_chat_rooms WHERE id = '$_GET[id]'"));
$saving = $room->saving;
//rozcesti situace
if ($room->type > 0){
	//urceni lvl
	function set_rl($xp){
		if ($xp < 0){
			return 0;
		}elseif($xp > 25){
			return 9;
		}elseif($xp > 8){
			return 4;
		}elseif($xp > 5){
			return 3;
		}elseif($xp > 2){
			return 2;
		}elseif ($xp > -1){
			return 1;
		}
	}

	$limit_main = 60*60*2; //po kolika s. dat hlavni situaci
	$limit_new = 60*10; //po kolika s. dat novou situace

	$isPJin = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_users WHERE rid = '$_GET[id]' AND prava = '1' AND odesel = '0'"));

	if ($isPJin[0]>0) {
		// PJ nebo nekdo, kdo ma vyssi prava, nez klasicky user, je v mistnosti...
		$mT = mysql_fetch_row(mysql_query("SELECT time FROM 3_chat_mess WHERE rid = '$_GET[id]' AND special > 0 ORDER BY time DESC LIMIT 1"));
		if (false && ($time - $mT[0]) > $limit_main && $vuItem->prava == '1'){
			// posle se info PJi, ktery je v mistnosti
			$txt = addslashes("Ve výpisu zpráv již není popis hlavní situace <span style='font-size:80%;font-style:italic'>(tato zpráva dočasně zastupuje její funkci)</span>");
			$minId = array_shift(mysql_fetch_row(mysql_query("SELECT MIN(id) FROM 3_roz_situace WHERE category = '$room->category' LIMIT 1")));
			mysql_query("INSERT INTO 3_chat_mess (uid, rid, time, text, wh, special) VALUES (0, $_GET[id], $time, '$txt', $_SESSION[uid], '$minId')");
		  $lastInserted = mysql_insert_id();
  		if ($saving) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (0, $_GET[id], $_SESSION[uid], $lastInserted, '$txt', $time)");
		}
	}
	else {
		$pjactQ = mysql_query("SELECT 
	(SELECT COUNT(id) FROM 3_users WHERE roz_pj = 1 AND id IN(
		SELECT uid FROM 3_chat_users WHERE rid = $_GET[id] AND odesel = 0
	))
AS 'yes',
(SELECT COUNT(*) FROM 3_chat_users WHERE rid = $_GET[id] AND odesel = 0)
AS 'total'");
		$pjact = mysql_fetch_object($pjactQ);
		if($pjact->yes >= $pjact->total/2){
			//stridani hlavni situace
			$mT = mysql_fetch_row(mysql_query("SELECT time FROM 3_chat_mess WHERE rid = '$_GET[id]' AND special > 0 ORDER BY time DESC LIMIT 1"));
			if (($time - $mT[0]) > $limit_main) {
				mysql_query ("DELETE FROM 3_chat_mess WHERE rid = '$_GET[id]' AND (special > 0 OR special2 > 0)");
			}
			//pokud neni situace, nahodne se vybere a vlozi
			$sC = mysql_fetch_row(mysql_query("SELECT special FROM 3_chat_mess WHERE rid = '$_GET[id]' AND special > 0 ORDER BY time DESC"));
			if ($sC[0] < 1){
				$hour = date("G");
				$add_sqlXXX = "";
				if ($hour < 22 && $hour > 6) {
					$add_sqlXXX = "AND id != 155";
				}
				$smC = mysql_fetch_object(mysql_query("SELECT * FROM 3_roz_situace WHERE nadrazena = 0 AND category = '$room->category' $add_sqlXXX ORDER BY rand() LIMIT 1"));
				$situace = addslashes("<span class='vypravec'>".$smC->nazev."</span><br />".$smC->popis);
				mysql_query("INSERT INTO 3_chat_mess (uid, rid, time, text, special) VALUES (1, $_GET[id], $time, '$situace', $smC->id)");
			  $lastInserted = mysql_insert_id();
	  		if ($saving) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (1, $_GET[id], 0, $lastInserted, '$situace', $time)");
				mysql_query("UPDATE 3_chat_rooms SET popis = '".addslashes($smC->nazev)."' WHERE id = $_GET[id]");
			}
			//podsituace
			$lT = mysql_fetch_row(mysql_query("SELECT time FROM 3_chat_mess WHERE rid = '$_GET[id]' AND (special > 0 OR special2 > 0) ORDER BY time DESC LIMIT 1"));
			if (($time - $lT[0]) > $limit_new){
				//urceni hlavni situace
				$sC = mysql_fetch_row(mysql_query("SELECT special FROM 3_chat_mess WHERE rid = $_GET[id] AND special > 0 ORDER BY time DESC"));
				//pouzite podsituace
				$uuS = mysql_query ("SELECT special2 FROM 3_chat_mess WHERE rid = $_GET[id] AND special = 0 AND special2 > 0");
				$sId = array();
				while ($ouS = mysql_fetch_object($uuS)){
					$sId[] = $ouS->special2;
				}
				if (count($sId) > 0){
					$add_sql = "AND id NOT IN (".join (",",$sId).")";
				}else{
					$add_sql = "";
				}
				$rS = mysql_fetch_object(mysql_query("SELECT * FROM 3_roz_situace WHERE nadrazena = $sC[0] $add_sql ORDER BY rand()"));
				mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text, special, special2) VALUES (1, $_GET[id], $time, '".addslashes($rS->popis)."', 0, $rS->id)");
			  $lastInserted = mysql_insert_id();
  			if ($saving) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (1, $_GET[id], 0, $lastInserted, '".addslashes($rS->popis)."', $time)");
			}
		}
	}
}

if ($room->category != "") $room->category = "<br />$room->category";

echo "<span class='nazev'>".stripslashes($room->nazev).$room->category."</span>";
echo "<p class='links_out'><a href='/' class='big mb5' target='_blank' title='Hlavní stránka Aragornu'>Aragorn.cz</a></p>";
if ($room->type > 0){
	$pjactQ = mysql_query("SELECT 
	(SELECT COUNT(id) FROM 3_users WHERE roz_pj = 1 AND id IN(
		SELECT uid FROM 3_chat_users WHERE rid = $_GET[id] AND odesel = 0
	))
AS 'yes',
(SELECT COUNT(*) FROM 3_chat_users WHERE rid = $_GET[id] AND odesel = 0)
AS 'total'");
	$pjact = mysql_fetch_object($pjactQ);
	echo '<p class="links_out">
	<a href="/chat/situation.php?id='.$_GET['id'].'" onclick="javascript:window.open(\'situation.php?id='.$_GET['id'].'\', \'_blank\', \'width=500,height=400\'); return false;">Aktuální Situace</a> 
	<a href="/diskuze/rozcesti/" class="mb5" target="_blank" title="Diskuze s &quot;pravidly&quot;, doporučeními a dalšími informacemi o Rozcestí" onclick="if(!confirm(\'Otevřít diskuzi Rozcestí v novém okně?\')){return false;}">Diskuze Rozcestí</a>
	<a href="/chat/roz_help.htm" onclick="javascript:window.open(\'roz_help.htm\', \'_blank\', \'width=800,height=400,resizable=yes,scrollbars=yes\'); return false;"  class="mt5" title="Stručná nápověda k hraní v Rozcestí">Pravidla, tipy, rady</a>
	<a href="#" onclick="return false;">Vypravěč <span style="color: '.(
	$pjact->yes >= $pjact->total/2 ? 'lime;">' : 'red;">ne'
	). 'aktivní ('.$pjact->yes.'/'.$pjact->total.')</a>';
	if ($_SESSION['prava']=="Admin" || $_SESSION['prava']=="Programmer") {
	  echo '<a href="/chat/roz_xp_list.php" target="_blank" class="mt5" title="Žebříček XP Rozcestí">Žebříček XP</a>';
  }
  echo '</p><br />';
}

echo "<span class='odkaz'>";
//uzivatele mistnosti
$chatUsers = mysql_query ("SELECT u.id, u.login, u.ico, u.roz_name, u.roz_popis, u.roz_ico, u.roz_exp, c.timestamp, c.prava FROM 3_users AS u, 3_chat_users AS c WHERE u.id = c.uid AND rid = '$_GET[id]' AND c.odesel = '0' ORDER BY u.login ASC LIMIT 30");
$show_stats = "";
while ($chItem = mysql_fetch_object($chatUsers)){

if ($room->type > 0){

  if (strlen($chItem->roz_name) > 1){
   $us = stripslashes($chItem->roz_name);
  }else{
   $us = stripslashes($chItem->login);
  }
  
	if (strlen($chItem->roz_popis) > 1){
		$rp = "<tr><td class='rp'>".stripslashes($chItem->roz_popis)."</td></tr>";
	}else{
		$rp = "";
	}
  
  if (strlen($chItem->roz_ico) > 1){
   $path = "r/$chItem->roz_ico";
  }else{
   $path = "i/$chItem->ico";
  }
	if ($chItem->prava == '0') {
		$xp_bar = "";
	   $xp_bar = "<tr><td align='center'><img src='./style/l".set_rl($chItem->roz_exp).".jpg' alt='RP hodnota' title='RP hodnota' /></td></tr>\n";
	}
	else {
	  $xp_bar = "<tr><td align='center'><img src='./style/l5.jpg' alt='Správce rozcestí' title='Správce rozcestí' /></td></tr>\n";
	}
	if ($vuItem->prava != '0') {
		$xp_count = "<tr><td align='center'>Stav XP : ".$chItem->roz_exp."</td></tr>\n";
	}
	else {
		$xp_count = "";
	}
  
}else{
  $us = stripslashes($chItem->login);
  $path = "i/$chItem->ico";
  $rp = $xp_bar = "";

}

$us2 = stripslashes($chItem->login);


$show_stats .= "
<table width='110' border='0' cellpadding='2' id='m$chItem->id' style='display:none' class='stats'>
<tr><td>$us</td></tr>
<tr><td>".countdown($time - $chItem->timestamp)."</td></tr>
<tr><td><img src='http://s1.aragorn.cz/$path' alt='$us' title='$us' /></td></tr>$rp$xp_bar$xp_count
</table>
";

if ($_SESSION['uid'] == $chItem->id){
  echo "<span class='myself' onmouseover=\"return hide('m$chItem->id')\" onmouseout=\"return hide('m$chItem->id')\">".stripslashes($chItem->login)."</span>";
}else{
  echo "<a href=\"javascript: sel('u$chItem->id')\" title='Napsat zprávu' onmouseover=\"return hide('m$chItem->id')\" onmouseout=\"return hide('m$chItem->id')\">$us2</a>";
}

}
echo "</span>";
?>
</div>

<?php
echo $show_stats;
?>

<a href="http://www.toplist.cz/toplist/?search=drd&amp;a=s" title="TOPlist" target="_blank"><script language="JavaScript" type="text/javascript">
<!--
document.write ('<img src="http://toplist.cz/dot.asp?id=40769&amp;http='+escape(top.document.referrer)+'" width="1" height="1" border=0 alt="TOPlist" />');
//--></script></a><noscript><img src="http://toplist.cz/dot.asp?id=40769" border="0"
alt="TOPlist" width="1" height="1" /></noscript>
<script type="text/javascript">
function hide(obj){

  if(document.getElementById(obj).style.display == "none"){
    document.getElementById(obj).style.display = "" 
  }else{
    document.getElementById(obj).style.display = "none"
  }
  return 1
}

//vyber selectu u play
function sel(id){
parent.play.document.getElementById(id).selected="selected";
parent.play.document.getElementById('mess').focus();
}
</script>

</body>
</html>
