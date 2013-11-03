<?php
$noOutputBuffer = true;

require "../db/conn.php";

    if (! isSet($_SESSION['prava'])) {   
      include_once("../hater_custom_functions.php");
      if (isRozAdmin($_SESSION["uid"])) $_SESSION['prava'] = "RozAdmin";
      if (isAdmin($_SESSION["uid"])) $_SESSION['prava'] = "Admin";
      if (isProgrammer($_SESSION['login'])) $_SESSION['prava'] = "Programmer";
    }

$time = time();
if (!isset($_SESSION["uid"]) || !isset($_SESSION["lvl"]) || !isset($_SESSION["login"]) || !ctype_digit($_GET['id'])) {
  echo "<script type=\"text/javascript\">window.parent.location.href=\"/chat/\";</script>";
  die ("<span style='color: red'>Chat je pristupny pouze prihlasenym uzivatelum serveru Aragorn.cz.</span>");
}

$SEZENI = $_SESSION;
session_write_close();
$dgjRS56VdcvTOvz = "_SESSION";
$$dgjRS56VdcvTOvz = $SEZENI;

function get_admin_prava(){
	if ($_SESSION['lvl']>3) return 1;
	$selS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = $_SESSION[uid] AND chat = 1"));
	return $selS[0];
}

$ms = "";
$get_set = 0;
if (isset($_GET['set'])) {
	$get_set = $_GET['set'];
}
$get_adm = 0;
if (isset($_GET['admin'])) {
	$get_adm = $_GET['admin'];
}

$vu = mysql_query("SELECT u.*,r.*,up.ico,up.* FROM 3_chat_users AS u, 3_chat_rooms AS r, 3_users AS up WHERE up.id = $_SESSION[uid] AND u.uid = '$_SESSION[uid]' AND u.rid = '$_GET[id]' AND u.odesel = '0' AND u.rid = r.id AND r.id = '$_GET[id]' AND u.rid != '1' LIMIT 1");

$isadmin = false;
$admin = false;
$vuNR = mysql_num_rows($vu);

if ($vuNR>0) {
	$vuItem = mysql_fetch_object($vu);

	if ($vuItem->prava == "1") {
		$isadmin = true;
	}
	elseif (get_admin_prava()){
		$isadmin = true;
	}
	if ($get_adm>0 && $isadmin) {
		$admin = true;
	}
}

if ($vuNR && $get_set > 0){
include "./set.php";
  $ref = 99999999;
}elseif($vuNR){
  $ref = $vuItem->chat_ref;
}

if ($vuNR && $isadmin && (isSet($_POST['del']) || isSet($_GET['del']))) {
	if (is_array($_POST['del'])) {
	  $delA = array();
		foreach ($_POST['del'] as $v) {
			if(ctype_digit($v)){
				$delA[]=$v;
			}
		}
		$delA = join(",",$delA);
		mysql_query("DELETE FROM 3_chat_mess WHERE rid=$_GET[id] AND id IN ($delA)");
		if ($vuItem->saving > 0) mysql_query("DELETE FROM 3_chat_save_text WHERE rid=$_GET[id] AND mid IN ($delA)");
		header("Location: /chat/game.php?id=$_GET[id]&admin=1");
		exit;
	}
	elseif (ctype_digit($_GET['del'])) {
	  $delA = addslashes($_GET['del']);
		mysql_query("DELETE FROM 3_chat_mess WHERE rid=$_GET[id] AND id = '$delA'");
		if ($vuItem->saving > 0) mysql_query("DELETE FROM 3_chat_save_text WHERE rid=$_GET[id] AND mid = '$delA'");
		header("Location: /chat/game.php?id=$_GET[id]&admin=1");
		exit;
	}
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head><meta http-equiv='Content-language' content='cs' /><meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' /><meta http-equiv='pragma' content='no-cache' /><meta http-equiv='Refresh' content='<?php if (!$admin) {echo $ref;}else {echo "9999999";} ?>' /><meta name='description' content='Aragorn.cz, chat' /><title>Arachat</title><link rel="stylesheet" type="text/css" href="./style/chat.css" /></head>
<body>
<?php
//vykop, pokud neni v chat_users
if (!$vu || $vuNR < 1){
  echo "<script type=\"text/javascript\">window.parent.location.href=\"/chat/\";</script>";
    die ("<span style='color: white'>Do teto mistnosti nejste prihlasen(a).</span>");
}

if ($get_set < 1){

if ($admin) {
?>
<script type="text/javascript">/* <![CDATA[ */
function check_boxes(){var chbs = document.getElementsByTagName('INPUT'),chb,ab;for(ab=0;ab<chbs.length;ab++){if (chbs[ab].type.toUpperCase()=="CHECKBOX"){if(chbs[ab].checked){chbs[ab].checked = false;}else chbs[ab].checked=true;}}}
/* ]]> */</script>
<?php

echo "<form name='del-frm' id='del-frm' method='post' action='game.php?id=$_GET[id]'><p><a href='/chat/game.php?id=$_GET[id]'>Zpět na normální výpis</a> &nbsp; &nbsp; <a href='javascript:check_boxes()'>Zaškrtnout/Odškrtnout Vše</a> &nbsp; <input type='submit' value='Smazat zaškrtnuté' /></p>\n";
}
if ($vuItem->saving){
	echo "<p style='color:red'><b>Je zapnutý záznam chatu. Veškeré zprávy (včetně šeptaných) jsou ukládány.</b></p>\n";
}
?>
<div id='chat-vypis-zprav' class='game' style='font-size:<?php echo $vuItem->chat_font;?>px'>
<?php

$votedq = mysql_query("SELECT * FROM 3_chat_votes WHERE rid = " . $_GET['id'] . " AND uid = " . $_SESSION['uid']);

if(!is_null($vuItem->vote_situation)){
$vote_uq = mysql_query("SELECT login FROM 3_users WHERE id = $vuItem->vote_uid");
$vote_u = mysql_fetch_object($vote_uq);
$vote_sitq = mysql_query("SELECT nazev FROM 3_roz_situace WHERE id = $vuItem->vote_situation");
$vote_sit = mysql_fetch_object($vote_sitq);
echo "<b>Systém</b>: " . $vote_u->login . " chce změnit situaci na <b>" . $vote_sit->nazev . "</b> ";
if( mysql_num_rows($votedq) == 0){
echo "<a href='/chat/vote.php?id=".$_GET['id']."&vote=yes'>Ano</a> - <a href='/chat/vote.php?id=".$_GET['id']."&vote=no'>Ne</a>";
}else{
echo "- ";
$vote_votesq = mysql_query("SELECT (SELECT COUNT(id) FROM 3_chat_votes WHERE rid = " . $_GET['id'] . " AND vote = 1) AS 'yes',(SELECT COUNT(id) FROM 3_chat_votes WHERE rid = " . $_GET['id'] . " AND vote = 0) AS 'no'");
$vote_votes = mysql_fetch_object($vote_votesq);
$voted = mysql_fetch_object($votedq);
echo "Ano: $vote_votes->yes : Ne: $vote_votes->no<br/>";
}
}

$s_m = mysql_query ("SELECT c.login_from, c.login_to, c.color, c.uid, c.wh, c.text, c.time, c.id AS messid FROM 3_chat_mess AS c WHERE c.rid = '$_GET[id]' AND (c.wh = 0 OR c.wh = $_SESSION[uid] OR c.uid = $_SESSION[uid]) ORDER BY c.id DESC LIMIT 50");

while ($cm = mysql_fetch_object($s_m)){
	$text = stripslashes($cm->text);
	$aw = $cm->wh;
	$ax = $cm->uid;
	if ($cm->wh == $_SESSION['uid']){
		if ($cm->uid > 1) {
			$chatName = "$cm->login_from -&gt; $_SESSION[login]";
		}
		elseif ($cm->uid == 1) {
			$chatName = "Vypravěč -&gt; $_SESSION[login]";
		}
		else {
			$chatName = "Systém -&gt; $_SESSION[login]";
		}
	}elseif($cm->wh > 0){
		$chatName = "$_SESSION[login] -&gt; $cm->login_to";
	}elseif ($cm->uid == 1){
		$chatName = "Vypravěč";
	}else{
		$chatName = $cm->login_from;
	}

	if ($vuItem->chat_time > 0){
	  $t = "(".date("H:i", $cm->time).") ";
	}else{
	  $t = "";
	}

	if ($cm->color == ""){
		$cm->color = "#fff";
	}

$messAdmin = ">";
if ($admin && $isadmin) {
	$messAdmin = " onclick='cb(event);'><input type='checkbox' name='del[]' value='$cm->messid' /> <a href='/chat/game.php?id=$_GET[id]&amp;del=$cm->messid'>x</a> ";
}
if ($ax > 0){
  $message = "<div".$messAdmin."".$t."<span style='color:$cm->color'><b>$chatName</b>: $text</span></div>\n";
}elseif ($ax == 0 && $aw != 0) {
  $message = "<div".$messAdmin."".$t."<span style='color:white'><b>Systém -&gt; $_SESSION[login]</b>: $text</span></div>\n";
}elseif ($vuItem->chat_sys > 0){
  $message = "<div".$messAdmin.""."<span style='color: #516C62; font-size: 10px'><b>Systém</b>: $text</span></div>\n";
}else{
  $message = false;
}

if ($message !== false){
  if ($vuItem->chat_order == "desc"){
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

if ($vuItem->saving){
	echo "<p style='color:red'><b>Je zapnutý záznam chatu. Veškeré zprávy (včetně šeptaných) jsou ukládány.</b></p>\n";
}

if ($admin) {
	echo "<a href='/chat/game.php?id=$_GET[id]'>Zpět na normální výpis</a> <input type='submit' value='Smazat zaškrtnuté' /></form>\n";
?>

<script type="text/javascript">/* <![CDATA[ */
	function cb(e){var t,q,qw;if(!e)var e=window.event;if(e.target){t=e.target;}else if(e.srcElement){t=e.srcElement;}if(t.nodeType==3)t=t.parentNode;if (t.tagName.toLowerCase()=="input"){return;}while (t.parentNode && t.tagName.toUpperCase()!="DIV"){t=t.parentNode;}q=t.getElementsByTagName("INPUT");for(qw=0;qw<q.length;qw++){q[qw].checked=!q[qw].checked;}}
/* ]]> */</script>
<?php
}

//najeti dolu
if ($vuItem->chat_order == "asc"){
echo "
<script type=\"text/javascript\">
window.scrollBy(0,document.body.parentNode.clientHeight)
</script>
";
}

}else{

//users do selectu
$chatUsers = mysql_query ("SELECT u.id, u.login, c.odesel FROM 3_users AS u, 3_chat_users AS c WHERE u.id = c.uid AND rid = '$_GET[id]' AND u.id != '$_SESSION[uid]' ORDER BY u.id ASC");

$disAll = $disAktiv = "";
$uSelAktiv = $uSelAll = "<option value=''>- - - -</option>";

if (mysql_num_rows($chatUsers) > 0){
	while ($chItem = mysql_fetch_object($chatUsers)){
		$us = stripslashes($chItem->login);
		if ($chItem->odesel == "0" && c.prava == '0') {
			$disAktiv = "";
			$uSelAktiv .= "<option value='$chItem->id' id='u$chItem->id'>$us</option>";
		}
		$uSelAll .= "<option value='$chItem->id' id='u$chItem->id'>$us</option>";
	}
}
else{
  $disAll = $disAktiv = " disabled='disabled'";
  $uSelAll = $uSelAktiv = "<option value='' id='u'>Nikdo přítomen</option>";
}

//je mistnosti rozcesti?
$isR = mysql_fetch_object( mysql_query ("select type, saving from 3_chat_rooms where id = $_GET[id]") );
$saving = $isR->saving;

if ($isR->type > 0){

//ma uz ikonu?
if (strlen($vuItem->roz_ico) > 4){
  $path = "roz_icos/$vuItem->roz_ico";
}else{
  $path = "icos/$vuItem->ico";
}

//vypise warning
function info($text){
  echo "<p class='info' id='inf'><span class='war' title='Varování'></span>$text <a href=\"javascript: hide('inf')\" title='Zavřít'>Zavřít</a></p>";
}

//vypise ok
function ok($text){
  echo "<p class='info' id='inf'><span class='ok' title='Ok'></span>$text <a href=\"javascript: hide('inf')\" title='Zavřít'>Zavřít</a></p>";
}

if (isSet($error)){

switch ($error){

case 1:
  $error = "Ikonka nebyla zadána.";
break;

case 2:
  $error = "Ikonka má nesprávný formát.<br /> Dovolené formáty jsou : <strong>GIF</strong>, <strong>JPEG</strong>, <strong>PNG</strong>.";
break;

case 3:
  $error = "Ikonka nemá povolené rozměry.<br /> Limit je <strong>40-50px</strong> / <strong>40-70px</strong> (š, v).";
break;

case 4:
  $error = "Velikost ikonky je vyšší než 16kB.";
break;

case 5:
  $error = "Je třeba zadat důvod udělení banu.";
break;

case 6:
  $error = "Hlasovat lze jen jednou za 5 minut.";
break;

}

info($error);
}elseif (isSet($ok)){

switch ($ok){

case 1:
  $ok = "Ikonka byla v pořádku nahrána.";
break;

case 2:
  $ok = "Údaje o postavě uloženy.";
break;

case 3:
  $ok = "Filtr změnen.";
break;

case 4:
  $ok = "Ban byl udělen.";
break;

case 5:
  $ok = "XP přiděleno.";
break;

case 7:
  $ok = "Situace přidána.";
break;

case 8:
  $ok = "Podsituace přidána.";
break;

case 9:
  $ok = "Uživatel vyhozen z místnosti.";
break;

case 10:
  $ok = "Zákaz vstupu do Rozcestí na 60 minut byl udělen.";
break;
case 11:
  $ok = "Hlasování začato.";
break;
}

ok($ok);

}

$situackyS = mysql_query("SELECT id, nazev, left(category,1) AS cat FROM 3_roz_situace WHERE nadrazena = '0' AND category = '$vuItem->category' ORDER BY id ASC");
	$sitR = array();
	while ($situacka = mysql_fetch_object($situackyS)) {
		$sitR[] = "<option value='$situacka->id'>($situacka->cat) $situacka->nazev</option>";
	}
	$sitR = "<select name='situace'><option>- - - - -</option>".join("",$sitR)."</select>";

if ($isadmin) {
	
	$sC = mysql_fetch_row(mysql_query("SELECT special FROM 3_chat_mess WHERE rid = '$_GET[id]' AND special > 0 ORDER BY id DESC LIMIT 1"));
	$podsituackyS = mysql_query("SELECT id, popis FROM 3_roz_situace WHERE nadrazena = '$sC[0]' ORDER BY id ASC");
	$podR = array();
	while ($podsituacka = mysql_fetch_object($podsituackyS)) {
		$podR[] = "<option value='$podsituacka->id'>".mb_strimwidth($podsituacka->popis,0,50,"...","UTF-8")."</option>";
	}
	$podR = "<select name='podsituace'><option>- - - - -</option>".join("",$podR)."</select>";

	$optForData = "";
	$infoAboutSaving = "(<strong>Vypnuto</strong>)";
	if ($isR->saving > 0) {
		$optForSaves = "<option value=\"end\">Ukončit AKTUÁLNÍ záznam</option>";
		$infoAboutSaving = "(<strong>Zapnuto</strong>)";
	}
	else {
		$optForSaves = "<option value=\"new\">Založit NOVÝ záznam</option>";
	}
	$selectedSaves = mysql_query("SELECT * FROM 3_chat_save_data WHERE rid = '$_GET[id]' AND aktivni = '0' ORDER BY id DESC");
	if ($selectedSaves && mysql_num_rows($selectedSaves)>0) {
	  while($saveItem = mysql_fetch_object($selectedSaves)) {
			$optForData .= "<option value='$saveItem->id'>".date("d/m H:i",$saveItem->timeStart)." - ".date("d/m H:i",$saveItem->timeEnd)."</option>";
		}
	  $optForSaves .= "<option value=\"show\">Zobrazit VYBRANÝ záznam</option><option value=\"\">- - - - -</option><option value=\"delete\">Smazat VYBRANÝ záznam</option>";
	}

?>

<div class='mod_width'>

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=10' method='post' class='f'>
<fieldset>
<div>Záznam hry <?php echo $infoAboutSaving; ?><a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<div><label><span>Data</span><select class='button2' name='data'><option value="">- - - - -</option><?php echo $optForData; ?></select></label>
<label><span>Akce</span><select name='akce' class='button2'><option value="">- - - - -</option><?php echo $optForSaves;?></select></label>
<input class='button' type='submit' value='Provést' /></div>
</fieldset>
</form>

</div>
<div class="mod_width">

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=5' method='post' class='f'>
<fieldset>
<div>Udělit XP <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<div><label><span>Uživatel</span><select class='button2' name='user'<?php echo $disAll; ?>><?php echo $uSelAll; ?></select></label>
<label><span>Za</span><select class='button2' onchange='document.getElementById("other-reason-input").style.display = (this.selectedIndex == 3 ? "block" : "none");' name='reas'<?php echo $disAll; ?>><option value='dobrý nápad při hře'>Dobrý nápad</option><option value='dobré RP hraní'>Dobré RP hraní</option><option value='dobré dokreslení atmosféry'>Dokreslení atmosféry</option><option value='x'>Jiný důvod (nutné doplnit)</option></select></label>
<label style='display:none' id='other-reason-input'><span>Jiný důvod</span><input class='button2' name='reas2'<?php echo $disAll; ?> value='' /></label>
<input class='button' type='submit' value='Udělit 1 XP'<?php echo $disAll; ?> /></div>
</fieldset>
</form>

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=6' method='post' class='f'>
<fieldset>
<div>Odebrat XP <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<div><label><span>Uživatel</span><select class='button2' name='user'<?php echo $disAll; ?>><?php echo $uSelAll; ?></select></label>
<label><span>Za</span><input class='button2' name='reas'<?php echo $disAll; ?> value='stávajícímu počtu XP neodpovídající hraní' /></label>
<input class='button' type='submit' value='Odebrat 1 XP'<?php echo $disAll; ?> /></div>
</fieldset>
</form>

<?php 
if ($_SESSION['prava']=="Admin" || $_SESSION['prava']=="Programmer") {
?>
<script>
function addxp() {
  var xpcount=document.getElementById('xpcount').value;
  var xpto=document.getElementById('xpto').value;
  var xpwhy=document.getElementById('xpwhy').value;
  var sel=document.getElementById('xpto');
  var xplogin=sel.options[sel.selectedIndex].text;

  var xhr; 
  try { xhr = new XMLHttpRequest(); }                 
  catch(e) {    
    xhr = new ActiveXObject("Microsoft.XMLHTTP");
  } 
  
  xhr.onreadystatechange  = function() { 
    if(xhr.readyState  == 4) {
      if(xhr.status  == 200) { 
        document.getElementById("xpaddresult").innerHTML=xhr.responseText;
        if (xhr.responseText.substr(0,2)=="OK") {                 
          document.getElementById("xpaddresult").style.color="lightgreen";
        }
        else {
          document.getElementById("xpaddresult").style.color="red";
        }
      } 
      else 
        document.getElementById("xpaddresult").innerHTML="<span style='background-color: red'>Chyba: " + xhr.status + "</span>";
    }         
  }; 
  
  base = 'http://www.aragorn.cz/chat/'; 
  url = base + 'roz_xp_add.php?xpcount='+xpcount+'&xpto='+xpto+'&xpwhy='+xpwhy+'&xplogin='+xplogin;     
  xhr.open("GET", url, true); 
  xhr.send(null);
  document.getElementById("xpaddresult").innerHTML="Odesílání";
  document.getElementById("xpaddresult").style.color="#DDD1B3";
}
</script>    
<fieldset class="formbyhater">
  <div class="atright"><a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
  <div>Hodnotit hráče: </div>
  <table>
   <tr><td><label for="xpcount">Počet xp (0-5): </label></td><td><input type="range" min="0" max="5" name="xpcount" id="xpcount"></td></tr>
   <tr><td><label for="xpto">Komu: </label></td><td><select name="user" id="xpto" class="button2"><?php echo $uSelAll; ?></select></td></tr>
   <tr><td><label for="xpwhy">Důvod: </label></td><td><input type="text" maxlength="250" autocomplete="off" name="xpwhy" id="xpwhy"></td></tr>
   <tr><td><button class="button" onclick="javascript: addxp()">Odeslat</button></td><td>
   <span id="xpaddresult"></span></td></tr>
   </table>
</fieldset>  
<?php
}
?>

</div>

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=8' method='post' class='f'>
<fieldset>
<div>Přidání podsituace <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<div><label><span>Podsituace</span><?php echo $podR;?></label>
<input class='button' type='submit' value='Přidej podsituaci' /></div>
</fieldset>
</form>

<?php  
}
?>

</div>
<div class="mod_width">

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=7' method='post' class='f'>
<fieldset>
<div>Změna situace <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<div><label><span>Nová situace</span><?php echo $sitR;?></label>
<input class='button' type='submit' value='<?php echo $isAdmin ? "Změň situaci" : "Hlasovat o změně"; ?>' /></div>
</fieldset>
</form>

<div class="mod_width">

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=3' method='post' enctype='multipart/form-data' class='f'>
<fieldset>
<div>Nahrání ikonky postavy <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<div><img src='../system/<?php echo $path; ?>' style='position: relative; left: 5px' alt='<?php echo $_SESSION['login']; ?>' title='<?php echo $_SESSION['login']; ?>' /></div>
<label><span>Ikonka</span><input type='file' name='ico' size='20' /></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=4' method='post' enctype='multipart/form-data' class='f'>
<fieldset>
<div>Údaje Vaší postavy<a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<label><span>Jméno postavy</span><input type='text' name='rn' value='<?php echo stripslashes($vuItem->roz_name); ?>' maxlength='20' size='20' /></label>
<label><span>Stručný popis postavy</span><input type='text' name='rp' value='<?php echo stripslashes($vuItem->roz_popis); ?>' maxlength='150' size='20' /></label>
<label><span>Používat vypravěče</span><input type='checkbox' name='usePJ' value='yes' <?php echo $vuItem->roz_pj == 1 ? ' checked="checked"' : ''; ?> /></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
<?php
}
?>

</div>

<div class="mod_width">

<script type="text/javascript">
function hide(obj){
  if(document.getElementById(obj).style.display == "none"){
    document.getElementById(obj).style.display = ""
  }else{
    document.getElementById(obj).style.display = "none"
  }
}
</script>

<?php
if ($isadmin){
	$listToDelete = array();;
	$listToDeleteSrc = mysql_query("SELECT u.login,a.uid,a.id,a.cas FROM 3_chat_admin AS a LEFT JOIN 3_users AS u ON u.id = a.uid WHERE a.typ = -1 ORDER BY u.login ASC");
	if ($listToDeleteSrc && mysql_num_rows($listToDeleteSrc) > 0) {
		while($userToDelete = mysql_fetch_object($listToDeleteSrc)){
			$listToDelete[] = "<li><a href='game.php?id=$_GET[id]&amp;set=1&amp;akce=14&amp;a=$userToDelete->id&amp;uid=$userToDelete->uid'>x</a> $userToDelete->login (-".date("i\m:s\s",($userToDelete->cas-$time)).")</li>";
		}
		$listToDelete = "<div><p><br />Odebrat zákaz:</p><ul>".join("",$listToDelete)."</ul></div>";
	}
	else {
		$listToDelete = "";
	}
?>

</div>

<div class="mod_width">
<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=13' method='post' class='f'>
<fieldset>
<div>Zákaz vstup <strong>POUZE</strong> na Rozcestí <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<label><span>Uživatel</span><select class='button2' name='banroz'<?php echo $disAktiv; ?>><?php echo $uSelAll; ?></select></label>
<input class='button' type='submit' value='Zakázat na 60 minut!'<?php echo $disAktiv; ?> />
<?php echo $listToDelete;?>
</fieldset>
</form>

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=9' method='post' class='f'>
<fieldset>
<div>Vyhození uživatele <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<label><span>Uživatel</span><select class='button2' name='kick'<?php echo $disAktiv; ?>><?php echo $uSelAll; ?></select></label>
<input class='button' type='submit' value='Vyhodit'<?php echo $disAktiv; ?> />
</fieldset>
</form>

<form action='game.php?id=<?php echo $_GET['id'];?>&amp;set=1&amp;akce=2' method='post' class='f'>
<fieldset>
<div>Udělit ban <a href='game.php?id=<?php echo $_GET['id'];?>' class='flink' title='Zavřít'>Zavřít</a></div>
<label><span>Uživatel</span><select class='button2' name='kick'<?php echo $disAll; ?>><?php echo $uSelAll; ?></select></label>
<label><span>Na jak dlouho</span><select class='button2' name='ban'<?php echo $disAll; ?>><option value='0.16667'>10 minut</option><option value='1'>hodina</option><option value='24'>den</option><option value='168'>týden</option><option value='720'>měsíc</option></select></label>
<label><span>Důvod</span><textarea class='button2' name='reason'<?php echo $disAll; ?> />Spamování na chatu.</textarea></label>
<input class='button' type='submit' value='Udělit BAN'<?php echo $disAll; ?> />
</fieldset>
</form>

<?php
}
?>
</div>
<?php
}
?>
</body></html>