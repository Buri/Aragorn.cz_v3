<?php
mysql_query ("DELETE FROM 3_chat_admin WHERE typ = -1 AND cas <= $time");

$PJRozcesti		= " style='font-style:italic' title='Správce Rozcestí ";
$StalySpravce	= " style='font-style:italic' title='Stálý správce místnosti ";
$Administrator = " style='font-style:italic' title='Administrátor ";

if ($slink == "ad" && $hasRight){

	$ad = "<p class='submenu'><a href='/chat/' class='permalink' title='Zpět'>Zpět</a></p>";

}elseif($hasRight){
	$ad = "<p class='submenu'><a href='/chat/ad/' class='permalink' title='Administrace'>Administrace</a></p>";
	$title = "Chat - místnosti";
}
else {
	$title = "Chat - místnosti";
	$ad = "";
}
?>

<h2 class='h2-head'><a href='<?php echo "/$link/"; ?>' title='<?php echo $title2; ?>'><?php echo $title2; ?></a></h2>
<h3><a href='<?php if ($slink){$add = "/";} echo "/$link/$slink$add"; ?>' title='<?php echo $title; ?>'><?php echo $title; ?></a></h3>
<?php echo $ad; ?>

<?php
if ($slink == "ad" && $hasRight){

if (isSet($_GET['error'])){

	switch ($_GET['error']){
		case 1:
			$error = "Název místnosti musí mít alespoň 3 znaky.";
		break;
		case 2:
			$error = "Popis místnosti musí mít alespoň 3 znaky.";
		break;
		case 3:
			$error = "Nebyla zvolena místnost.";
		break;
		case 4:
			$error = "Takováto místnost neexistuje.";
		break;
		case 5:
			$error = "Nebyla zvolena místnost.";
		break;
		case 6:
			$error = "Rozcestí je již plné.";
		break;
		case 7:
			$error = "Akce na změnu typu či smazání fungují jen pro dočasné místnosti!";
		break;
		case 8:
			$error = "Musíš zvolit typ místnosti.";
		break;
		case 9:
			$error = "Maximálně 2 dočasně založené místnosti! Pokud to chceš změnit, kontaktuj vývojáře Aragornu.";
		break;
	}
	info($error);

}elseif (isSet($_GET['ok'])){

	switch ($_GET['ok']){
		case 1:
			$ok = "Místnost úspěšně založena.";
		break;
		case 2:
			$ok = "Místnost upravena.";
		break;
		case 3:
			$ok = "Místnost úspěšně smazána.";
		break;
	}

	ok($ok);

}

// if (false){
?>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/chat/ad/?akce=chat-create' method='post' class='f'>
<fieldset>
<legend>Nová místnost</legend>
<label><span>Název</span><input type='text' name='chat_nazev' value='' maxlength='60' /></label>
<label><span>Popis</span><input type='text' name='chat_popis' value='' maxlength='250' /></label>
<label><span>Typ</span><select name="chat_type"><option value="-1">- - - - -</option><option value="0">Klasický chat</option><option value="1">Rozcestí (klasické fantasy)</option><option value="2">Rozcestí (sci-fi/wod)</option></select></label>
<input class='button' type='submit' value='Založit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
<?php
// }
?>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/chat/ad/?akce=chat-adjust' method='post' class='f'>
<fieldset>
<legend>Administrace stávajících místností</legend>

<table width='80%' cellspacing='3'>
<thead><th width='5%'></th><th width='40%'>Název</th><th>Popis</th><th colspan="2"></th></thead>
<tbody>

<?php
$sel_rooms = mysql_query ("SELECT * FROM 3_chat_rooms ORDER BY nazev ASC");
$statAr = array("dočasná","trvalá");
while ($cItem = mysql_fetch_object($sel_rooms)){

	$nazev = stripslashes($cItem->nazev);
	$popis = stripslashes($cItem->popis);
?>

<tr><td><input class='checkbox' type='radio' name='chat_room' id='c_r_id<?php echo $cItem->id; ?>' value='<?php echo $cItem->id; ?>' /></td><td><label for="c_r_id<?php echo $cItem->id; ?>"><span><?php echo $nazev; ?></span></label></td><td><?php echo $popis; ?></td><td><?php echo $statAr[$cItem->staticka]?></td><td><?php echo $cItem->category;?></td></tr>

<?php
}
?>

</tbody>
</table>

<label><span>Název</span><input type='text' name='chat_nazev' value='' maxlength='60' /></label>
<label><span>Popis</span><input type='text' name='chat_popis' value='' maxlength='250' /></label>
<label><span>Typ</span><select name="chat_type"><option value="">- - - - -</option><option value="0">Klasický chat</option><option value="1">Rozcestí (klasické fantasy)</option><option value="2">Rozcestí (sci-fi/wod)</option></select></label>
<label><span>Akce</span><select name="chat_action"><option value="">- - - - -</option><option value="adjust">Upravit</option><option value="delete">Smazat</option></select></label>
<input class='button' type='submit' value='Provést akci' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<?php
}else{
?>

<div class='clanky'>
<table class='chat' width='100%' cellspacing='6'>
<?php

//$xp = mysql_fetch_row(mysql_query("SELECT roz_exp FROM 3_users WHERE id = $_SESSION[uid]"));
$hasRozBan = false;

$isPJ = false;
if ($LogedIn) {
	if ($hasRight && $_SESSION['lvl'] > 2) {
		$isPJ = true;
	}
	else {
		$jeCestiS = mysql_query("SELECT typ, cas FROM 3_chat_admin WHERE uid = $_SESSION[uid] AND (typ = 1 OR typ = -1)");
		if ($jeCestiS && mysql_num_rows($jeCestiS)>0) {
			$jePJCesti = mysql_fetch_row($jeCestiS);
			if ($jePJCesti[0] == '1') {
				$isPJ = true;
			}
			elseif ($jePJCesti[0] == '-1'){
				$jePJCesti[1] = $jePJCesti[1]-$time;
				$hasRozBan = true;

				$afterTime = round(date("i",$jePJCesti[1]));
				$afterTimeB = round(date("s",$jePJCesti[1]));

				if ($afterTime > 5)
					$afterTime .= " minut";
				elseif ($afterTime > 1)
					$afterTime .= " minuty";
				elseif ($afterTime > 0)
					$afterTime .= " minutu";
				else
					$afterTime = "";
				if ($afterTimeB > 5)
					$afterTimeB .= " vteřin";
				elseif ($afterTime > 1)
					$afterTimeB .= " vteřiny";
				elseif ($afterTimeB > 0)
					$afterTimeB .= " vteřinu";
				else
					$afterTimeB = "";

				if ($afterTime != "" && $afterTimeB != "") {
					$afterTime .= " a ".$afterTimeB;
				}
				else {
					$afterTime = trim($afterTime.$afterTimeB);
				}
			}
		}
	}
}

$sel_r = mysql_query ("SELECT * FROM 3_chat_rooms ORDER BY type ASC, nazev ASC");
while ($cItem = mysql_fetch_object($sel_r)){

//"cron" pro chat ;)

//mazani starych zprav
$timeout = $time - 60*60*1.5;
	mysql_query ("DELETE FROM 3_chat_mess WHERE time < $timeout AND type = 0 AND special = 0 AND special2 = 0");
	
//vymazani odejivsich uzivatelu - u tech se pri odchodu nastavil "odesel" na 1, a cas odchodu na time()
mysql_query("DELETE FROM 3_chat_users WHERE odesel = 1 AND timestamp < $time-60*6");

/* //vyhozeni neaktivnich homies
$chatKick = mysql_query ("SELECT u.id, u.login, c.id AS chid FROM 3_users AS u, 3_chat_users AS c WHERE u.id = c.uid AND rid = $cItem->id AND c.timestamp < $timeout ORDER BY u.login");

if (mysql_num_rows($chatKick)>0) {
	while ($chItem = mysql_fetch_object($chatKick)){
		$uk = stripslashes($chItem->login);
		$text = "$uk vyhozen(a) pro 15 minutovou neaktivitu.";
		mysql_query ("UPDATE 3_chat_users SET odesel='1' WHERE id = $chItem->chid");
		mysql_query ("INSERT INTO 3_chat_mess (uid, rid, wh, time, text) VALUES (0, $cItem->id, 0, $time, '$text')");
	}
}
*/

	$cUs = array();
	//osazenstvo mistnosti
	$cU = mysql_query ("SELECT u.login, u.login_rew, u.level, c.prava, c.uid FROM 3_users AS u, 3_chat_users AS c WHERE u.id = c.uid AND c.rid = $cItem->id AND c.odesel = 0 ORDER BY u.login_rew ASC");
	$IsInRoom = false;
	while ($cUItem = mysql_fetch_object($cU)){
		$iswhat = "";
		if ($cUItem->prava > 0 || $cUItem->level > 2) {
			if ($cItem->type == 0) {
				if ($cUItem->level > 2) {
					$iswhat = $Administrator._htmlspec($cUItem->login)."'";
				}
				else {
					$iswhat = $StalySpravce._htmlspec($cUItem->login)."'";
				}
			}
			else {
				$iswhat = $PJRozcesti._htmlspec($cUItem->login)."'";
			}
		}
		if ($LogedIn) {
			if ($_SESSION['uid'] == $cUItem->uid) {
				$IsInRoom = true;
			}
		}
		$cUs[] = "<a href='/uzivatele/$cUItem->login_rew/'><span".$iswhat.sl($cUItem->level, 1).">$cUItem->login</span></a>";
	}
	
	if (count($cUs) > 0){
		$users_string = join(", ", $cUs);
	}else{
		$users_string = "<span style='font-style: italic'>místnost je prázdná</span>";
	}

	if ($cItem->category != ""){
		$cItem->category = "<br /><em>".$cItem->category."</em>";
	}

	if ($LogedIn == false){
		$nazev = $cItem->nazev.$cItem->category."<br /><span class='chat-locked'>přihlašte se</span>";
	}elseif($cItem->type > 0 && $hasRozBan){
		$nazev = $cItem->nazev.$cItem->category."<br /><span class='chat-locked helper'><acronym title='Zákaz vstupu (ban) vyprší za $afterTime'>zakázaný vstup</acronym></span>";
	}elseif($cItem->locked > 0 && !$hasRight){
		$nazev = $cItem->nazev.$cItem->category."<br /><span class='chat-locked'>zamčeno</span>";
	//}elseif($cItem->elite && !$isPJ && !$IsInRoom && (int)$xp[0] < 2){
	//	$nazev = $cItem->nazev.$cItem->category."<br /><span class='chat-locked'>vstup jen pro uživatele s 2 a více XP</span>";
	}elseif($cItem->type > 0 && count($cUs) > 7 && $isPJ == false && $IsInRoom == false){
		$nazev = $cItem->nazev.$cItem->category."<br /><span class='chat-locked'>plná kapacita</span>";
	}else{
		$nazev = "<a href='/chat/?akce=chat-enter&amp;id=$cItem->id' title='Vstoupit do místnosti' class='permalink" . ($cItem->need_admin == 1 && $isPJ == true ? " need_admin" : ""). "'>$cItem->nazev</a>".$cItem->category;
	}
	$popis = stripslashes($cItem->popis);
?>

	<tr><td width='35%' class='chat-nazev'><?php echo $nazev; ?></td><td width='30%' class='chat-popis'><?php echo $popis; ?></td><td class='chat-users'><?php echo $users_string; ?></td></tr>

<?php
unset($cUs);
}

$sql = "SELECT u.login, u.login_rew, u.level, c.typ FROM 3_users AS u, 3_chat_admin AS c WHERE u.timestamp > 0 AND c.uid = u.id ORDER BY u.login_rew ASC";
$sql2 = "SELECT u.login, u.login_rew, u.level FROM 3_users AS u, 3_admin_prava AS a WHERE u.timestamp > 0 AND ((a.uid = u.id AND a.chat = '1') OR (u.level > 3)) ORDER BY u.login_rew ASC";

$spravciOnS = mysql_query($sql);

$spravciOnText = "<p>Ani jeden Správce trvalých místností není online.</p>\n";
$spravciOnText .= "<p>Ani jeden Správce Rozcestí není online.</p>\n";

if ($spravciOnS && mysql_num_rows($spravciOnS)){
	$spravciOn = array(array(),array());
	$spravciOnText = "";
	while($theOne = mysql_fetch_object($spravciOnS)){
		if ($theOne->level > 1) $theOne->login = "<span".sl($theOne->level,2).">".$theOne->login."</span>";
		$spravciOn[$theOne->typ][$theOne->login_rew] = "<a href='/uzivatele/$theOne->login_rew/'>".$theOne->login."</a>";
	}
	$spravciOn[0] = join(", ",$spravciOn[0]);
	$spravciOn[1] = join(", ",$spravciOn[1]);
	if ($spravciOn[0] != "") {
		$spravciOnText .= "<p>Správci trvalých místností: ".$spravciOn[0]."</p>\n";
	}
	else {
		$spravciOnText .= "<p>Ani jeden Správce trvalých místností online.</p>\n";
	}
	if ($spravciOn[1] != "") {
		$spravciOnText .= "<p>Správci Rozcestí: ".$spravciOn[1].".</p>\n";
	}
	else {
		$spravciOnText .= "<p>Ani jeden Správce Rozcestí online.</p>\n";
	}
}

$chatAdminsOnS = mysql_query($sql2);
if ($chatAdminsOnS && mysql_num_rows($chatAdminsOnS)>0){
	$chatAdminsOn = array();
	$chatAdminsOnText = "";
	while ($oneAdmin = mysql_fetch_object($chatAdminsOnS)) {
		$chatAdminsOn[$oneAdmin->login_rew] = "<a href='/uzivatele/$oneAdmin->login_rew/'><span".sl($oneAdmin->level,2).">".$oneAdmin->login."</span></a>";
	}
	$chatAdminsOn = join(", ",$chatAdminsOn);
		$chatAdminsOnText .= "<p>Administrátoři chatu: ".$chatAdminsOn.".</p>\n";
}
?>
<tr>
<td colspan='3'>
<div class="text">
<strong>Online</strong>
<br />
<?php if (isset($chatAdminsOnText) && $chatAdminsOnText != "") echo $chatAdminsOnText;
else echo "<p>Ani jeden administrátor chatu není online.</p>\n";?>
<?php if (isset($spravciOnText) && $spravciOnText != "") echo $spravciOnText;
else echo "<p><em>Žádný správce chatu online.</em></p>\n";?>
</div>
</td>
</tr>
</table>
</div>

<?php
}
?>
