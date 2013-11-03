<?php
mb_internal_encoding("UTF-8");

$noOutputBuffer = true;

require "../db/conn.php";

session_set_cookie_params(2*3600);
session_start();
$time = time();
$mess = $uSel = $us_js = "";
$get_l = "";
$notCross = true;
$komuto = "";

function get_admin_prava(){
	if ($_SESSION['lvl']>3) return 1;
	$selS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = $_SESSION[uid] AND chat = 1"));
	return $selS[0];
}

$SEZENI = $_SESSION;
session_write_close();
$dgjRS56VdcvTOvz = "_SESSION";
$$dgjRS56VdcvTOvz = $SEZENI;

if (!ctype_digit($_GET['id'])) {
  echo "<html>\n<head>\n<title>Error</title>\n</head>\n<body>\n<script type=\"text/javascript\">window.parent.location.href=\"/chat/\";</script>\n";
    die ("<span style='color: white'>Do teto mistnosti nejste prihlasen(a).</span></body></html>");
}

$komu = 0;
$get_chb = false;
if (isset($_POST['chb']) || isset($_GET['chb'])) {
	$get_chb = true;
	$komu = $_GET['to'];
	if (isset($_POST['to'])) {
		$komu = $_POST['to'];
	}
}

if (isset($_GET['l'])) {
	$get_l = $_GET['l'];
}

if($_GET['call'] == 1){
	$text = $_SESSION['login'] . ' zavolal správce do místnosti.';
	$text = addslashes($text);
	mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, ".$_GET['id'].", ".time().", '$text')");
	mysql_query ("UPDATE 3_chat_rooms SET need_admin = 1 WHERE ID = ".$_GET['id']);
	echo "<script type=\"text/javascript\">window.parent.location.href=\"/room/".$_GET['id']."\";</script>";
exit;
}

//konec, pokud neni v chat_users
$endHere = false;
$vu = mysql_query ("SELECT c.*,r.elite,r.type,r.saving,u.chat_color,u.roz_exp FROM 3_chat_rooms AS r, 3_chat_users AS c, 3_users AS u WHERE r.id = '$_GET[id]' AND u.id = c.uid AND c.uid = '$SEZENI[uid]' AND c.rid = '$_GET[id]' AND c.odesel = '0'");
if ($vu && mysql_num_rows($vu) > 0) {
		$saving = $vuItem->saving;
		$vuItem = mysql_fetch_object($vu);
		if ((int)$vuItem->prava < 1 && (int)$vuItem->elite > 0 && (int)$vuItem->roz_exp < 2) {
			$endHere = true;
		}
}
else {
	$endHere = true;
}
if ($endHere){
	echo "<script type=\"text/javascript\">window.parent.location.href=\"/chat/\";</script>";
	die("<span style='color: white'>Do teto mistnosti nejste prihlasen(a).</span>");
	exit;
}
echo "<!-- " . $_SESSION['login'] . "-->";
//odeslani mess
if (isset($_POST['mess']) && isset($_POST['to']) && $_GET['add'] > 0 && mb_strlen(trim($_POST['mess'])) > 0 && $get_l != 1){
setcookie('chathistory'.$_GET['id'], '');

$mess = trim($_POST['mess']);
$komuLogin = "";
//byla pouzita JS fce add_js()?
$js_test = explode("#",mb_substr($mess,0,40),2);
$test_nick = addslashes($js_test[0]);
$komu = $_POST['to'];
$sel_js = mysql_query ("SELECT c.uid,u.login FROM 3_chat_users AS c, 3_users AS u WHERE u.login='$test_nick' AND c.rid='$_GET[id]' AND c.uid != '$_SESSION[uid]' AND u.id = c.uid");

if (mysql_num_rows($sel_js)>0) {
	$komuItem = mysql_fetch_object($sel_js);
	$komu = $komuItem->uid;
	$komuLogin = $komuItem->login;
	$mess = trim(mb_substr($mess,(mb_strlen($test_nick)+1)));
	$notCross = false;
}

$vypravec = false;
//admins mohou html

if ($vuItem->type == '0') {
	include "smileyadd.php";
}
else {
	function addsmileys($text,$a=false) {
		return $text;
	}
}

$isadmin = get_admin_prava();

if ($isadmin){
	if ($_POST['pjing'] == "on") {
		$vypravec = true;
		$text = trim($mess);
	}else {
	  $text = addsmileys(trim($mess),true);
	}
}
elseif ($vuItem->prava == "1") {
	if ($_POST['pjing'] == "on") {
		$vypravec = true;
		$text = trim(htmlspecialchars($mess,ENT_QUOTES,"UTF-8"));
	}
	else {
		$text = addsmileys(trim(htmlspecialchars($mess,ENT_QUOTES,"UTF-8")),true);
	}
}
else {
	$text = addsmileys(trim(htmlspecialchars($mess,ENT_QUOTES,"UTF-8")),false);
}

$text = addslashes($text);

$special2 = 0;
if ($vypravec == true) {
	$odkoho = 1;
	$komu = 0;
	$loginFrom = "Vypravěč";
	$color = "#fff";
	$special2 = 1;
}
else {
	$odkoho = $_SESSION['uid'];
	if (ctype_xdigit($vuItem->chat_color) && (strlen($vuItem->chat_color) == 3 || strlen($vuItem->chat_color) == 6)) {
		$vuItem->chat_color = "#".$vuItem->chat_color;
	}
	$color = addslashes($vuItem->chat_color);
}

if ($komu > 0 && $notCross && $get_chb) {
$komuto = "&to=$komu&chb=on";
}

	if ($komu > 0) {
		$komuLogin = array_shift(mysql_fetch_row(mysql_query("SELECT login FROM 3_users WHERE id = '$komu'")));
	}

  mysql_query ("INSERT INTO 3_chat_mess (uid, rid, wh, login_from, login_to, time, color, text, type, special2) VALUES ($odkoho, $_GET[id], $komu, '".addslashes($_SESSION['login'])."', '".addslashes($komuLogin)."', $time, '$color','$text', $vuItem->type, $special2)");
  if ($saving) {
  	$lastInserted = mysql_insert_id();
		mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES ($odkoho, $_GET[id], $komu, $lastInserted, '$text', $time)");
	}
  mysql_query ("UPDATE 3_chat_users SET timestamp = $time WHERE uid = $_SESSION[uid] and rid = $_GET[id]");
  mysql_query ("UPDATE 3_users SET timestamp = $time WHERE id = $_SESSION[uid]");
	echo "<script type=\"text/javascript\">window.parent.game.location.href=\"game.php?id=$_GET[id]\";</script><script type=\"text/javascript\">window.location.href=\"play.php?id=$_GET[id]".$komuto."\";</script>";
}elseif($get_l == "1"){
  mysql_query ("DELETE FROM 3_chat_votes WHERE rid = ".$_GET['id']." AND uid = ".$_SESSION['uid']);
  mysql_query ("UPDATE 3_chat_users SET odesel='1',timestamp='$time' WHERE uid = $_SESSION[uid] AND rid = $_GET[id]");
  $text = addslashes("$_SESSION[login] odchází z místnosti.");
  mysql_query("INSERT into 3_chat_mess (uid, rid, wh, login_from, time, text, type) values (0, $_GET[id], 0, 'Systém', $time, '$text', $vuItem->type)");
  if ($saving) {
		$lastInserted = mysql_insert_id();
		mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (0, $_GET[id], 0, $lastInserted, '$text', $time)");
	}
  echo "<script type=\"text/javascript\">window.parent.location.href=\"/chat/vote.php?vote=leave&id=".$_GET["id"]."\";</script>";

}

?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html><head><meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' /><meta http-equiv='pragma' content='no-cache' />
<title>Arachat</title><link rel="stylesheet" type="text/css" href="./style/chat.css" />
<script src="/js/classic-chat.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8" src="http://s1.aragorn.cz/j/_8a1d67927b1a4beb384607250c84caef.v101moo.joined.js"></script>
<script type="text/javascript">
var refr = true;
function refit() {
	if (refr) {
		window.location.href="<?php echo "/chat/play.php?id=".$_GET['id'].$komuto;?>"
	}
	return true;
}
</script>
</head>
<body onLoad="setTimeout('refit()',600*1000);">
<?php

//users do selectu
$chatUsers = mysql_query ("SELECT u.id, u.login FROM 3_users AS u, 3_chat_users AS c WHERE u.id = c.uid AND rid = $_GET[id] AND u.id != $_SESSION[uid] AND c.odesel = 0 ORDER BY u.login ASC");

$counter = 1;
$selected = "";
$checkedBox = "";
$us_js = array();
while ($chItem = mysql_fetch_object($chatUsers)){
	$us = stripslashes($chItem->login);
	$us_js[] = $us;
	$counter++;
	if ($get_chb == true && $notCross) {
		$checkedBox = " checked";
		if (($_POST['to'] == $chItem->id || $_GET['to'] == $chItem->id)) {
			$selected = " selected='selected'";
		}
		else {
			$selected = "";
		}
	}
	$uSel .= "<option value='$chItem->id' id='u$chItem->id'$selected>$us</option>";
}
$us_js = join(",",$us_js);

$allowPJ = "";
if ($vuItem->type > 0 && ($isadmin || $vuItem->prava == "1")) {
	$allowPJ = "<input type='checkbox' name='pjing' id='pjing' value='on' style='margin:0 10px 1px 30px' /><label for='pjing'>jako Vypravěč</label>";
}
$allowDel = "";
if ($isadmin || $vuItem->prava == "1") {
	$allowDel = "<input type='button' value='Mazání zpráv' onclick=\"window.parent.game.location.href='game.php?id=$_GET[id]&admin=1'\" />";
}

?>
<div class='play'>
<form action='play.php?id=<?php echo $_GET['id'];?>&amp;add=1' class='fg' name='chat' method='post' target='_self'>
<input type='hidden' name='chat_js' value="<?php echo $us_js; ?>" />
<table border='0' cellpadding='2' width="100%">
<tr>
	<td valign='top'><textarea name='mess' id='mess' class='txt' rows="2" cols="70" maxlength='1000' onKeyDown="return add_js(event)" onclick="return update(event);" onKeyUp="return update(event);"><?php echo $_COOKIE['chathistory'.$_GET['id']]; ?></textarea></td>
	<td valign='top'>
		<select name='to'><option value='0'>Všem (<?php echo $counter;?>)</option>
		<?php echo $uSel."\n"; ?>
		</select><input type='checkbox' name='chb' value='save' style='margin:0 2px 1px 5px'<?php echo $checkedBox; ?> /><input type='submit' value='Odeslat' />&nbsp; | &nbsp;<input type='button' onClick="window.parent.location.href='/room/<?php echo $_GET['id']; ?>/'" value='Refresh' /><input type="button" onclick="if(!confirm('Opravdu je nutné volat správce?'))return false; location.href='play.php?id=<?php echo $_GET['id']; ?>&amp;call=1';" value="Zavolat správce" /><input type='button' onClick="window.parent.game.location.href='game.php?id=<?php echo $_GET['id']; ?>&amp;set=1'" value='Nastavení' /><?php echo $allowDel;?><input type='button' onClick="location.href='play.php?id=<?php echo $_GET['id']; ?>&amp;l=1'" value='Odejít' />
		<?php if ($vuItem->type == '0') {
?>
<div class='smiles'>
<a href="#" onclick="add_smile('*:-D*');return 0"><img title="výtlem" src="smile/grin.gif" /></a>
<a href="#" onclick="add_smile('*:green:*');return 0"><img title="zelený výtlem" src="smile/green.gif" /></a>
<a href="#" onclick="add_smile('*:-)*');return 0"><img title=":-)" src="smile/smile.gif" /></a>
<a href="#" onclick="add_smile('*=)*');return 0"><img title="=)" src="smile/smile2.gif" /></a>
<a href="#" onclick="add_smile('*:-/*');return 0"><img title="nevím" src="smile/dontknow.gif" /></a>
<a href="#" onclick="add_smile('*:(*');return 0"><img title="smutný" src="smile/sad.gif" /></a>
<a href="#" onclick="add_smile('*:wow:*');return 0"><img title="óóóóó ..." src="smile/wtf.gif" /></a>
<a href="#" onclick="add_smile('*:oops:*');return 0"><img title="stydlivý" src="smile/ashamed.gif" /></a>
<a href="#" onclick="add_smile('*:-X*');return 0"><img title="mlčím" src="smile/quiet.gif" /></a>
<a href="#" onclick="add_smile('*:-P*');return 0"><img title=":-P" src="smile/tongue.gif" /></a>
<a href="#" onclick="add_smile('*]:-P*');return 0"><img title="bléééé" src="smile/tongue2.gif" /></a>
<a href="#" onclick="add_smile('*:cry:*');return 0"><img title="brečím" src="smile/cry.gif" /></a>
<a href="#" onclick="add_smile('*]:-O*');return 0"><img title="nazlobený !!!" src="smile/angry.gif" /></a>
<a href="#" onclick="add_smile('*:vamp:*');return 0"><img title="Vampír" src="smile/vamp.gif" /></a>
<a href="#" onclick="add_smile('*:twisted:*');return 0"><img title="muhe muhe muhehe" src="smile/twisted.gif" /></a>
<a href="#" onclick="add_smile('*;-)*');return 0"><img title="mrkám" src="smile/wink.gif" /></a>
<a href="#" onclick="add_smile('*:yes:*');return 0"><img title="JO!!!" src="smile/yes.gif" /></a>
<a href="#" onclick="add_smile('*:no:*');return 0"><img title="NE!!!" src="smile/no.gif" /></a>
</div><?php } ?>
	</td>
</tr>
<tr>
  <td class='lim'>Zbývá znaků : <span id='counter'>1000</span></td><td class='lim'<?php if ($vuItem->type=='0') echo " style='vertical-align: top'"; ?>><?php echo " ".$allowPJ;?>
</td>
</tr>
</table>
</form>

</div>
<a href="http://www.toplist.cz/toplist/?search=drd&amp;a=s" title="TOPlist" target="_blank"><script type="text/javascript"><!--
document.write ('<img src="http://toplist.cz/dot.asp?id=40769&amp;http='+escape(top.document.referrer)+'" width="1" height="1" border=0 alt="TOPlist" />');
//--></script></a><noscript><img src="http://toplist.cz/dot.asp?id=40769" border="0" alt="TOPlist" width="1" height="1" /></noscript>
<script type="text/javascript">/* <![CDATA[ */window.c='chathistory'+location.href.split('=')[1];var m=document.forms['chat']['mess'];m.focus();m.addEvent('keyup',function(e){Cookie.write(window.c, this.value);});/* ]]> */</script>
</body></html>
