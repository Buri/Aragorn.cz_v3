<?php
$titleUsers = $itIsApril ? 'Parchanti' : 'Uživatelé';

if ($id > -1){

if ($LogedIn == true && $id > 1){

$cF = mysql_fetch_row ( mysql_query ("select count(*) from 3_friends where uid = $_SESSION[uid] and fid = $id") );

if ($cF[0] > 0 && $id != $_SESSION['uid']){
	$addM = "<a href='/uzivatele/$slink/?akce=friends&amp;del=$id' class='permalink' title='Odebrat z přátel'>Odebrat z přátel</a>";
}elseif($id != $_SESSION['uid'] && isset($uI) && !$uI->reg_code){
	$addM = "<a href='/uzivatele/$slink/?akce=friends&amp;add=$id' class='permalink' title='Přidat do přátel'>Přidat do přátel</a>";
}
}

if ($LogedIn && $id != $_SESSION['uid'] && $_SESSION['uid'] > 0 && $id > 1){
	$addC = "<a href=\"#\" onclick=\"hide('koment');return false\" class='permalink' title='Okomentovat'>Okomentovat</a> <a href=\"/posta/?to="._htmlspec($user)."\" class=\"permalink\" title=\"Napsat "._htmlspec($user)." zprávu interní poštou\">Napsat</a>";
	$yours_comment = mysql_fetch_row( mysql_query("SELECT text FROM 3_u_comm WHERE cid = '$id' AND uid = $_SESSION[uid]") );
}

?>
<h2 class='h2-head'><a href='/uzivatele/' title='<?php echo $titleUsers;?> Aragorn.cz'><?php echo $titleUsers;?> Aragorn.cz</a></h2>
<h3><a href='<?php echo "/uzivatele/$slink/"; ?>' title='Profil uživatele <?php echo $user; ?>'><strong><?php echo $user; ?></strong></a></h3>

<p class='submenu'><a href='/uzivatele/' title='Zpět na výpis uživatelů' class='permalink'>Výpis</a><?php echo $addM; echo $addC; ?></p>

<?php
	if ($LogedIn && $id != $_SESSION['uid'] && $_SESSION['uid'] > 0 && $id > 1){
?>
<div id='koment' class='hide'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/<?php echo $link; ?>/<?php echo $slink; ?>/?akce=u-comm' name='txt' method='post' class='f fd'>
<fieldset>
<legend>Okomentovat uživatele <a href="#" onclick="hide('koment');return false;" class='permalink flink' title='Zavřít'>Zavřít</a></legend>
<label><span>Váš komentář</span><textarea rows='4' cols='70' name='mess' id='km' /><?php echo stripslashes($yours_comment[0]); ?></textarea><span><a href='#k' onclick='vloz_tag("b");return false;'><img src='/system/editor/bold.jpg' alt='Tučně' title='Tučně' /></a> <a href='#k' onclick='vloz_tag("i");return false;'><img src='/system/editor/kur.jpg' alt='Kurzívou' title='Kurzívou' /></a> <a href='#k' onclick='vloz_tag("u");return false;'><img src='/system/editor/und.jpg' alt='Podtrhnout' title='Podtrhnout' /></a> <a href='#k' onclick='editor(4);return false;'><img src='/system/editor/link.jpg' alt='Odkaz' title='Odkaz' /></a> <a href='#k' onclick='editor(5);return false;'><img src='/system/editor/pict.jpg' alt='Obrázek' title='Obrázek' /></a> <a href='#k' onclick='vloz_tag("color1");return false;' class='hlight1'>Barva 1</a> <a href='#k' onclick='vloz_tag("color2");return false;' class='hlight2'>Barva 2</a> <a href='#k' onclick='vloz_tag("color3");return false;' class='hlight3'>Barva 3</a></span></label>
<input class='button' type='submit' value='Odeslat komentář' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>

<?php
	}

switch($_GET[ok]){
	case 1:
		ok("$user přidán(a) do přátel.");
	break;
	case 2:
		ok("$user odebrán(a) z přátel.");
	break;
}

$strLevelIn = "";
if ($level > 1) {
	if ($level > 2) $strLevelIn = " (<a href='/admins/'>admin</a>)";
	else $strLevelIn = " (bonus)";
}

$osobLink = "";
if ($id == $_SESSION['uid']) $osobLink = " (<a href='/nastaveni/osobni/'>Upravit</a>)";

if ($id > 1) {
	echo "
<div class='users'>
	<table width='100%'>
		<tr><th colspan='2'>Osobní údaje$osobLink</th></tr>\n
		<tr><td width='40%'>Uživatelské jméno :</td><td><span ".sl($level, 1).">$user</span>$strLevelIn</td></tr>
		<tr><td><img src='http://s1.aragorn.cz/i/$ico' alt='Ikonka uživatele "._htmlspec($user)."' title='Ikonka uživatele "._htmlspec($user)."' /></td><td>$status</td></tr>
$lastLogin
		<tr><td>Poslední akce :</td><td>$lastAction</td></tr>
		<tr><td>Skutečné jméno :</td><td>$jmeno</td></tr>
		<tr><td>Město :</td><td>$mesto</td></tr>
		<tr><td>ICQ :</td><td>$icq</td></tr>
		<tr><td>Účet založen :</td><td>$accCreated</td></tr>
		".$bon." ".$ipp."
	</table>
</div>
";
}
else {
	echo "
<div class='users'>
	<table width='100%'>
		<tr><th colspan='2'>Údaje</th></tr>\n
		<tr><td width='40%'>Uživatelské jméno :</td><td><span ".sl($level, 1).">$user</span>$strLevelIn</td></tr>
		<tr><td><img src='http://s1.aragorn.cz/i/$ico' alt='Ikonka uživatele "._htmlspec($user)."' title='Ikonka uživatele "._htmlspec($user)."' /></td><td>$status</td></tr>
	</table>
</div>
";
}
//notes - NEW

if ($LogedIn) {
	if ($id == $_SESSION['uid']) {
		$noteSrc = mysql_query("SELECT * FROM 3_notes WHERE uid = $_SESSION[uid]");
		if (mysql_num_rows($noteSrc) < 1) {
echo "
<div class='users'>
	<table width='100%'>
	<tr><th colspan='3'><a name=\"poznamky\" href=\"#\" onclick=\"hide('poznamky-user-".$_SESSION['uid']."');return false;\">Moje poznámky</a></th></tr>
	<tr><td>
	<form action='?akce=poznamky' id='poznamky-user-".$_SESSION['uid']."' class='f fd hide' method='post'>
		<textarea rows='8' cols='70' name='mess'></textarea><br />
		<input type='submit' value='Upravit' class='button' />
	</form>
</td></tr></table>
</div>
";
		}
		else {
			$note = mysql_fetch_object($noteSrc);
echo "
<div class='users'>
	<table width='100%'>
	<tr><th colspan='3'><a name=\"poznamky\" href=\"#\" onclick=\"hide('poznamky-user-".$_SESSION['uid']."');return false;\">Moje poznámky</a></th></tr>
	<tr><td>
	<form action='?akce=poznamky' id='poznamky-user-".$_SESSION['uid']."' class='f fd hide' method='post'>
		<textarea rows='8' cols='70' name='mess'>"._htmlspec($note->text)."</textarea><br />
		<input type='submit' value='Uložit' class='button' />
	</form>
</td></tr></table>
</div>
";
		}
	}
}

if ($id>1) {
	// rozcesti

	if ($roz_name && $roz_popis) {
	
		function set_rl($xp){
			if ($xp === "sp"){
				return "Správce Rozcestí";
			}elseif ($xp < 0){
				return "Odpad";
			}elseif($xp > 25){
				return "Cheater";
			}elseif($xp > 8){
				return "Excelentní";
			}elseif($xp > 5){
				return "Zkušený";
			}elseif($xp > 2){
				return "Pokročilý";
			}elseif ($xp >= 0){
				return "Začátečník";
			}
		}
	
		if ($roz_ico != "")
			$roz_ico = "<img alt='Ikonka na Rozcesti - postava $roz_name ~ $user' src='http://s1.aragorn.cz/r/$roz_ico' />";
		else
			$roz_ico = "Postava";
	
		if ($roz_name != "")
			$roz_name = "<strong>$roz_name</strong><br />";
		if ($roz_popis != "")
			$roz_name .= $roz_popis;
	
		if ($level > 2)
			$roz_exp = array_shift(mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = '$id' AND chat = '1'")));
		else
			$roz_exp = array_shift(mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_admin WHERE uid = '$id' AND typ = '1'")));
	
		if ($roz_exp > 0) $roz_xp = "sp";
		
		$roz_xp = set_rl($roz_xp);
	
		$rozLink = "";
		if ($id == $_SESSION['uid']) $rozLink = " (<a href='/nastaveni/rozcesti/'>Upravit</a>)";
	
		$roz = "	<table width='100%'>
		<tr><th colspan='2'>Rozcestí$rozLink</th></tr>
";
	$roz .= "		<tr><td>Status</td><td>$roz_xp</td></tr>\n";
	$roz .= "		<tr><td style='width:70px;'>$roz_ico</td><td>$roz_name</td></tr>
";
	$roz .= "	</table>\n";
}
else {
	$roz = "$user nehraje na Rozcestí, nebo nemá vyplněny kolonky jméno a popis postavy.";
}

//$roxppristup=array("apophis","amthauer","buri the great","dart","grom","hater","indyján","ixiik","jilm","mikymauz","nefrete","niam","saltzhornia","scout","yakaman");
$roxppristup=array("hater","testhater1");

if (in_array(strtolower($_SESSION["login"]),$roxppristup)) {
  include("../chat/roz_xp_add_parser.php");
  //$roz .= doXP($user,"onlypos");
}

echo "
<div class='users'>
$roz
</div>
";
}

//clanky uzivatele
// $art = mysql_query ("SELECT id, nazev, nazev_rew, hodnoceni, hodnotilo, schvaleno, schvalenotime FROM 3_clanky WHERE autor = '$id' ORDER BY schvaleno DESC, nazev_rew ASC");
if (!$LogedIn) $sql = "SELECT c.id, c.nazev, c.nazev_rew, c.schvaleno, c.schvalenotime, c.hodnoceni, c.hodnotilo,
0 AS v_uid, 0 AS unread_comms, (SELECT COUNT(*) FROM 3_comm_1 AS cm WHERE cm.aid = c.id) AS all_comms
FROM 3_clanky AS c
WHERE c.autor = '$id' 
ORDER BY c.schvaleno DESC, c.nazev_rew ASC";
	else $sql = "SELECT c.id, c.nazev, c.nazev_rew, c.schvaleno, c.schvalenotime, c.hodnoceni, c.hodnotilo,
(SELECT COUNT(*) FROM 3_comm_1 WHERE 3_comm_1.aid = c.id) AS all_comms,
3_visited_1.news AS unread_comms, 3_visited_1.uid AS v_uid
FROM 3_clanky AS c
LEFT JOIN 3_visited_1 ON 3_visited_1.aid = c.id AND 3_visited_1.uid = $_SESSION[uid]
WHERE c.autor = '$id' 
ORDER BY c.schvaleno DESC, c.nazev_rew ASC";

$art = mysql_query($sql);

if ($art && mysql_num_rows($art) > 0){

	$clanky = "<table width='100%'>\n";
	$clanky .= "<tr><th colspan='4'><a title='Zobrazit všechny Články od $user' href='/clanky/od/$slink/'>Články ~ $user</a></th></tr>\n";
	
		while($aI = mysql_fetch_object($art)){
		$nC = $nCS = stripslashes($aI->nazev);
			if ($aI->schvaleno == "1") {
				$clanky .= "<tr><td>".date("d/m Y",$aI->schvalenotime)."</td><td><a href='/clanky/$aI->nazev_rew/' title='"._htmlspec($nC)."' class='permalink2'>"._htmlspec($nCS)."</a></td><td width='25%'>".rating($aI->hodnoceni, $aI->hodnotilo)."</td><td>".getComm($aI->id, 1,true,$aI->unread_comms,$aI->all_comms,$aI->v_uid)."</td></tr>";
			}
			else {
				$clanky .= "<tr><td></td><td>$nC</td><td colspan='2'>(neschváleno)</td></tr>";
			}
		
		}
	
	$clanky .= "</table>\n";

}else{
	$clanky = "$user nepřispěl(a) žádným článkem.";
}


echo "
<div class='users'>
$clanky
</div>
";

//kresby uzivatele
//$kart = mysql_query ("SELECT id, nazev, nazev_rew, hodnoceni, hodnotilo, schvaleno, schvalenotime FROM 3_galerie WHERE autor = '$id' ORDER BY schvaleno DESC, nazev_rew ASC");
if (!$LogedIn) $sql = "SELECT c.id, c.nazev, c.nazev_rew, c.schvaleno, c.schvalenotime, c.hodnoceni, c.hodnotilo,
0 AS v_uid, 0 AS unread_comms, (SELECT COUNT(*) FROM 3_comm_2 AS cm WHERE cm.aid = c.id) AS all_comms
FROM 3_galerie AS c
WHERE c.autor = '$id' 
ORDER BY c.schvaleno DESC, c.nazev_rew ASC";
	else $sql = "SELECT g.id, g.nazev, g.nazev_rew, g.schvaleno, g.schvalenotime, g.hodnoceni, g.hodnotilo,
(SELECT COUNT(*) FROM 3_comm_2 WHERE 3_comm_2.aid = g.id) AS all_comms,
3_visited_2.news AS unread_comms, 3_visited_2.uid AS v_uid
FROM 3_galerie AS g
LEFT JOIN 3_visited_2 ON 3_visited_2.aid = g.id AND 3_visited_2.uid = $_SESSION[uid]
WHERE g.autor = '$id' 
ORDER BY g.schvaleno DESC, g.nazev_rew ASC";

$kart = mysql_query ($sql);

if ($kart && mysql_num_rows($kart) > 0){

	$kresby = "<table width='100%'>\n";
	$kresby .= "<tr><th colspan='4'><a href='/galerie/od/$slink/' title='Všechna díla v Galerii od $user'>Díla v Galerii ~ $user</a></th></tr>\n";
	
		while($aI = mysql_fetch_object($kart)){
		$nC = stripslashes($aI->nazev);
		$nC = $nCS = stripslashes($aI->nazev);
			if ($aI->schvaleno == "0") {
				$kresby .= "<tr><td></td><td>$nC</td><td colspan='2'>(neschváleno)</td></tr>";
			}
			else {
				$kresby .= "<tr><td>".date("d/m Y",$aI->schvalenotime)."</td><td><a href='/galerie/$aI->nazev_rew/' title='"._htmlspec($nC)."' class='permalink2'>"._htmlspec($nCS)."</a></td><td width='25%'>".rating($aI->hodnoceni, $aI->hodnotilo)."</td><td>".getComm($aI->id, 2,true,$aI->unread_comms,$aI->all_comms,$aI->v_uid)."</td></tr>";
			}
		}
	
	$kresby .= "</table>\n";

}else{
	$kresby = "$user nepřispěl(a) žádnou kresbou.";
}

echo "
<div class='users'>
$kresby
</div>
";

//diskuze uzivatele
// $dart = mysql_query ("SELECT id, nazev, nazev_rew, schvaleno FROM 3_diskuze_topics WHERE owner = $id ORDER BY schvaleno DESC, nazev_rew ASC");
if ($LogedIn) {
	$sql = "SELECT t.id, t.nazev, t.nazev_rew, t.schvaleno,
	3_visited_3.news AS unread_comms, 3_visited_3.uid AS v_uid, 
	(SELECT COUNT(*) FROM 3_comm_3 WHERE 3_comm_3.aid = t.id) AS all_comms
	FROM 3_diskuze_topics AS t
	LEFT JOIN 3_visited_3 ON 3_visited_3.aid = t.id AND 3_visited_3.uid = $_SESSION[uid] 
  WHERE t.owner = '$id' 
  ORDER BY t.schvaleno DESC, t.nazev_rew ASC";
}
else {
	$sql = "SELECT t.id, t.nazev, t.nazev_rew, t.schvaleno,
	0 AS unread_comms, 0 AS v_uid, 
	(SELECT COUNT(*) FROM 3_comm_3 WHERE 3_comm_3.aid = t.id) AS all_comms
	FROM 3_diskuze_topics AS t
  WHERE t.owner = '$id' 
  ORDER BY t.schvaleno DESC, t.nazev_rew ASC";
}
$dart = mysql_query($sql);

if ($dart && mysql_num_rows($dart) > 0){

	$diskuze = "<table width='100%'>\n";
	$diskuze .= "<tr><th>Diskuze, které vlastní $user</th><th style='width:100px;'></th></tr>\n";
	
		while($aI = mysql_fetch_object($dart)){
		$nC = $nCS = stripslashes($aI->nazev);
			if ($aI->schvaleno == "0") {
				$diskuze .= "<tr><td>$nC</td><td>(neschváleno)</td></tr>";
			}
			else {
				$diskuze .= "<tr><td><a href='/diskuze/$aI->nazev_rew/' title='"._htmlspec($nC)."' class='permalink2'>"._htmlspec($nCS)."</a></td><td>".getComm($aI->id, 3, true, $aI->unread_comms, $aI->all_comms, $aI->v_uid)."</td></tr>";
			}
		}
	
	$diskuze .= "</table>\n";

}else{
	$diskuze = "$user nevlastní žádnou diskuzi.";
}

echo "
<div class='users'>
$diskuze
</div>
";

$jeskyneStart = "";
$jeskyne = "";
//jeskyne uzivatele

$myGM1s = mysql_query("SELECT h.uid, j.uid AS pj, h.jmeno, h.jmeno_rew, h.schvaleno, j.nazev, j.nazev_rew, j.id FROM 3_herna_postava_drd AS h, 3_herna_all AS j WHERE h.uid = $id AND h.cid = j.id ORDER BY h.schvaleno DESC, h.jmeno_rew ASC");
$myGM1c = mysql_num_rows($myGM1s);

if ($myGM1c > 0) {
	while ($aI = mysql_fetch_object($myGM1s)) {
		$nC = _htmlspec(stripslashes($aI->nazev));
		$nP = _htmlspec(stripslashes($aI->jmeno));
		$lnk = $nP." (neschváleno)";

		if ($aI->schvaleno == "0") {
			if ($LogedIn) {
				if ($aI->uid == $_SESSION['uid'] || $aI->pj == $_SESSION['uid']) {
					$lnk = "<a href='/herna/$aI->nazev_rew/$aI->jmeno_rew/' title='$nP' class='permalink2'>$nP</a> (neschváleno)";
				}
			}
		}
		else {
			$lnk = "<a href='/herna/$aI->nazev_rew/$aI->jmeno_rew/' title='$nP' class='permalink2'>$nP</a>";
		}

		$jeskyne .= "<tr><td><a href='/herna/?sekce=drd&amp;podle=zalozeni'><acronym title='Dračí Doupě'>DrD</acronym></td><td><a href='/herna/$aI->nazev_rew/' title='$nC' class='permalink2'>$nC</a></td><td>Postava $lnk</td><td>".getComm($aI->id, 4)."</td></tr>";

	}
}

$myGM2s = mysql_query("SELECT h.uid, j.uid AS pj, h.jmeno, h.jmeno_rew, h.schvaleno, j.nazev, j.nazev_rew, j.id FROM 3_herna_postava_orp AS h, 3_herna_all AS j WHERE h.uid = $id AND h.cid = j.id ORDER BY h.schvaleno DESC, h.jmeno_rew ASC");
$myGM2c = mysql_num_rows($myGM2s);
if ($myGM2c > 0) {
	while ($aI = mysql_fetch_object($myGM2s)) {
		$nC = _htmlspec(stripslashes($aI->nazev));
		$nP = _htmlspec(stripslashes($aI->jmeno));
		$lnk = $nP." (neschváleno)";

		if ($aI->schvaleno == "0") {
			if ($LogedIn) {
				if ($aI->uid == $_SESSION['uid'] || $aI->pj == $_SESSION['uid']) {
					$lnk = "<a href='/herna/$aI->nazev_rew/$aI->jmeno_rew/' title='$nP' class='permalink2'>$nP</a> (neschváleno)";
				}
			}
		}
		else {
			$lnk = "<a href='/herna/$aI->nazev_rew/$aI->jmeno_rew/' title='$nP' class='permalink2'>$nP</a>";
		}

		$jeskyne .= "<tr><td><a href='/herna/?sekce=orp&amp;podle=zalozeni'><acronym title='Open Role Play'>ORP</acronym></a></td><td><a href='/herna/$aI->nazev_rew/' title='$nC' class='permalink2'>$nC</a></td><td>Postava $lnk</td><td>".getComm($aI->id, 4)."</td></tr>";

	}
}

$myPJs	= mysql_query("SELECT uid, id, nazev, nazev_rew, schvaleno FROM 3_herna_all WHERE uid = $id ORDER BY schvaleno DESC, nazev_rew ASC");
$myPJc = mysql_num_rows($myPJs);
if ($myPJc > 0) {
	while ($aI = mysql_fetch_object($myPJs)) {
		$nC = _htmlspec(stripslashes($aI->nazev));
		$lnk = $nC." (neschváleno)";

		if ($aI->schvaleno == "0") {
			if ($LogedIn) {
				if ($aI->uid == $_SESSION['uid']) {
					$lnk = "<a href='/herna/$aI->nazev_rew/' title='$nC' class='permalink2'>$nC</a> (neschváleno)";
				}
			}
		}
		else {
			$lnk = "<a href='/herna/$aI->nazev_rew/' title='$nC' class='permalink2'>$nC</a>";
		}

		$jeskyne .= "<tr><td>Hra</td><td colspan='2'>$lnk</td><td>".getComm($aI->id, 4)."</td></tr>";

	}
}

$myPJPs	= mysql_query("SELECT h.id, h.nazev, h.nazev_rew FROM 3_herna_pj AS p, 3_herna_all AS h WHERE h.id = p.cid AND p.uid = '$id' ORDER BY h.nazev ASC");
$myPJPc = mysql_num_rows($myPJPs);
if ($myPJPc > 0) {
	while ($aI = mysql_fetch_object($myPJPs)) {
		$nC = _htmlspec(stripslashes($aI->nazev));
		$lnk = $nC." (neschváleno)";

		if ($aI->schvaleno == "0") {
			$lnk = "<a href='/herna/$aI->nazev_rew/' title='$nC' class='permalink2'>$nC</a> (neschváleno)";
		}
		else {
			$lnk = "<a href='/herna/$aI->nazev_rew/' title='$nC' class='permalink2'>$nC</a>";
		}

		$jeskyne .= "<tr><td>Hra</td><td>$lnk</td><td>Pomocný PJ</td><td>".getComm($aI->id, 4)."</td></tr>";

	}
}

if ($myGM1c > 0 || $myGM2c > 0 || $myPJc > 0 || $myPJPc > 0) {
	$jeskyne = "<table width='100%'><tr><th colspan='3'>Jeskyně a postavy ~ $user</th><th style='width:100px;'></th></tr>\n" . $jeskyne . "</table>\n";
}
else {
	$jeskyne = "$user aktivně nehraje v žádné jeskyni.";
}

echo "
<div class='users'>
$jeskyne
</div>
";

if ($id > 1) {
	//pratele uzivatele
	$fart = mysql_query ("SELECT u.login, u.login_rew FROM 3_users AS u, 3_friends AS f WHERE f.uid = $id AND u.id = f.fid AND u.reg_code = '0' ORDER BY u.login_rew ASC");

	if ($fart && mysql_num_rows($fart) > 0){
	
		$pratele = "<table width='100%'>\n";
		$pratele .= "<tr><th>Koho má $user v přátelích</th></tr>
<tr><td>";
		$prateleA = array();
		while($aI = mysql_fetch_object($fart)){
			$nC = stripslashes($aI->login);
			$prateleA[] = "<a href='/uzivatele/$aI->login_rew/' title='$nC' class='permalink2'>$nC</a>";
		}
		$pratele .= join(", ",$prateleA);
		$pratele .= "</td></tr></table>\n";

	}else{
		$pratele = "$user nemá nikoho v přátelích.";
	}


	echo "
<div class='users'>
$pratele
</div>
";

	//maji ho v pratelich
	$part = mysql_query ("SELECT u.login, u.login_rew FROM 3_users AS u, 3_friends AS f WHERE f.fid = $id AND u.id = f.uid AND u.reg_code = '0' ORDER BY u.login_rew ASC");

	if ($part && mysql_num_rows($part) > 0){

		$pratele = "<table width='100%'>\n";
		$pratele .= "<tr><th>Kdo má $user v přátelích</th></tr>
<tr><td>";
		$prateleA = array();
			while($aI = mysql_fetch_object($part)){
				$nC = stripslashes($aI->login);
				$prateleA[] = "<a href='/uzivatele/$aI->login_rew/' title='$nC' class='permalink2'>$nC</a>";
			}	
		$pratele .= join(", ",$prateleA);
		$pratele .= "</td></tr></table>\n";

	}else{
		$pratele = "$user nemá nikdo v přátelích.";
	}


	echo "
<div class='users'>
$pratele
</div>
";

	if (mb_strlen($about) > 0){
		echo "
		<div class='users' style='overflow: hidden'>
			<table width='100%'>
				<tr><th>Říká o sobě</th></tr>
				<tr><td>
					$about
				</td></tr>
			</table>
		</div>
	";
	}

	//komentare
	$uc = mysql_query ("SELECT c.*, u.login,u.login_rew FROM 3_u_comm AS c, 3_users AS u WHERE c.cid = $id AND c.uid = u.id ORDER BY u.login_rew ASC");
	if ($uc && mysql_num_rows($uc) > -1){
		echo "<div style='padding: 10px'>";
		echo "<h3><a href='/$link/$slink/#kom' name='kom' title='Komentáře'>Komentáře</a></h3>";
		while($gc = mysql_fetch_object($uc)){
			echo "<p class='text'><strong><a name='comm-$gc->login_rew' href='/uzivatele/".$gc->login_rew."/' title='$gc->login'>$gc->login</a></strong><br />\n".spit($gc->text, 1)."</p>";
		}
		echo "</div>";
	}
}

}elseif (isset($_POST['user']) && $_POST['user'] != "") {
	$search = do_seo(trim($_POST['user']));

?>
<h2 class='h2-head'><a href='/uzivatele/' title='<?php echo $titleUsers;?> Aragorn.cz'><?php echo $titleUsers;?> Aragorn.cz</a></h2>
<h3><a href='/uzivatele/' title='<?php echo $titleUsers;?> Aragorn.cz'>Výpis</a></h3>
<p class='submenu'><a href='#' onclick='hide("search");return false;' rel='nofollow' class='permalink' title='Vyhledat uživatele'>Vyhledat uživatele</a></p>
<div id='search' class='hide'>
	<div class='f-top'></div>
	<div class='f-middle'>
		<form action='/uzivatele/' method='post' class='f'>
			<fieldset>
				<legend>Vyhledat uživatele</legend>
				<label><span>Část přezdívky</span><input type='text' name='user' maxlength='15' value='<?php echo _htmlspec($_POST['user']); ?>' /></label>
				<input class='button' type='submit' value='Vyhledat' />
			</fieldset>
		</form>
	</div>
	<div class='f-bottom'></div>
</div>
<?php
if (mb_strlen($_POST['user'])<3 || mb_strlen($search)<3) {
	info("Pro vyhledání uživatele musíte zadat nejméně 3 znaky.");
}
else {
	$search = addslashes($search);
	$sel_users = mysql_query ("SELECT login, login_rew, level, ico, timestamp FROM 3_users WHERE login_rew LIKE '%$search%' AND reg_code = 0 ORDER BY login_rew");
	$uC = mysql_num_rows($sel_users);
	if ($uC>0) {
		echo "<div class='art'><p>Nalezení uživatelé podle výrazu <em>'"._htmlspec($_POST['user'])."'</em>.</p></div>\n";
		echo "<table class='tb' cellspacing='0'>";
		$i = 0;
		while($oU = mysql_fetch_object($sel_users)){
			$i++;
			if($oU->timestamp > 0) {
				$status = "online";
			} else $status = "offline";
			if ($i == $uC){
				$bD = " style='border-width: 0'";
			}else $bD = "";
			echo "<tr><td$bD><a href='/uzivatele/$oU->login_rew/' class='permalink' title='Detailní info'>$oU->login</a></td><td$bD><a href='/uzivatele/$oU->login_rew/' title='Detailní info'><img src='http://s1.aragorn.cz/i/$oU->ico' title='$oU->login' alt='$oU->login' /></a></td><td$bD><span class='$status'></span></td></tr>\n";
		}
		echo "</table>\n";
	}
	else {
		echo "<div class='art'><p>Hledanému výrazu <em>'"._htmlspec($_POST['user'])."'</em> nevyhovuje žádný uživatel.</p></div>\n";
	}
}

}else{
?>
<h2 class='h2-head'><a href='/uzivatele/' title='<?php echo $titleUsers;?> Aragorn.cz'><?php echo $titleUsers;?> Aragorn.cz</a></h2>
<h3><a href='/uzivatele/' title='<?php echo $titleUsers;?> Aragorn.cz'>Výpis</a></h3>

<p class='submenu'><a href='#' onclick='hide("search");return false' rel='nofollow' class='permalink' title='Vyhledat uživatele'>Vyhledat uživatele</a></p>

<div id='search' class='hide'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/uzivatele/' method='post' class='f'>
<fieldset>
<legend>Vyhledat uživatele</legend>
<label><span>Část přezdívky</span><input type='text' name='user' maxlength='15' /></label>
<input id='button' type='submit' value='Vyhledat' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>

<?php

switch($_GET[error]){
	case 1:
		info("Nebyl zadán nick hledaného uživatele.");
	break;
	case 2:
		info("Hledaný uživatel se nenachází v databázi.");
	break;
	case 3:
		info("Hledaný uživatel se nenachází v databázi.");
	break;
	case 4:
		info("Hledaný uživatel není ve vašich přátelích.");
	break;
}

	if (!isSet($_GET[index])){
		$index = 1;
	}else{
		$index = (int) ($_GET[index]);
	}

$from = ($index - 1) * $usersPC; //od kolikate polozky zobrazit

$aS = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM 3_users WHERE reg_code = 0"));
$aC = $aS[0];

$sel_users = mysql_query ("SELECT login, login_rew, level, ico, timestamp FROM 3_users WHERE reg_code = 0 ORDER BY timestamp DESC, login_rew ASC LIMIT $from, $usersPC");
$uC = mysql_num_rows($sel_users);

if ($uC < 1){
	info ("Na serveru Aragorn.cz není zatím nikdo registrovaný.");
}else{

?>

<p class='strankovani'><?php $pagination = make_pages($aC, $usersPC, $index); echo $pagination; ?></p>

<?php
echo "<table class='tb' cellspacing='0'>";

	$i = 0;

while($oU = mysql_fetch_object($sel_users)){
$i++;

if($oU->timestamp > 0){
	$status = "online";
}else{
	$status = "offline";
}

if ($i == $uC){
	$bD = " style='border-width: 0'";
}else{
	$bD = "";
}

echo "<tr><td$bD><a href='/uzivatele/$oU->login_rew/' class='permalink' title='Detailní info'><span".sl($oU->level, 1).">$oU->login</span></a></td><td$bD><a href='/uzivatele/$oU->login_rew/' title='Detailní info'><img src='http://s1.aragorn.cz/i/$oU->ico' title='$oU->login' alt='$oU->login' /></a></td><td$bD><span class='$status'></span></td></tr>\n";
}

echo "</table>\n";
?>
<p class='strankovani'><?php echo $pagination; ?></p>
<?php
}
}
?>
