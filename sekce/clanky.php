<?php

$txtInS = mysql_fetch_row(mysql_query("SELECT text FROM 3_notes WHERE uid='1'"));
$txtIn = $txtInS[0];

$hasAdminRight = 0;
if ($LogedIn) {
	if ($_SESSION['lvl']>2) {
		$hasAdminRight = get_admin_prava();
	}
}

$titleClanky = $itIsApril ? 'Pisálkova suť' : 'Články';

if ($slink == "my") {
	echo "<h2 class='h2-head'><a href='/clanky/' title='$titleClanky'>$titleClanky</a></h2><h3><a href='/clanky/my/' title='Moje články'>Moje články</a></h3>";
	echo "	<p class='submenu'><a href='/clanky/' class='permalink' title='Zpět na výpis Článků'>Zpět do Článků</a></p>\n";
	if ($LogedIn) {
?>
<?php

		if (isSet($_GET['error'])){
			switch ($_GET['error']){
				case 2:
				$error = "Anotace článku je příliš krátká.";
				break;
				
				case 3:
				$error = "Text článku je příliš krátký.";
				break;
			}

			info($error);
		}

		if (isset($_GET['edit']) && $_GET['edit'] != "") {
			$edt = addslashes($_GET['edit']);
			$clanekKeditaciSrc = mysql_query("SELECT c.*, u.login FROM 3_clanky AS c LEFT JOIN 3_users AS u ON u.id = c.kdoschvalil WHERE c.autor = $_SESSION[uid] AND c.nazev_rew = '$edt' AND c.schvaleno != '1'");
			if ($clanekKeditaciSrc && mysql_num_rows($clanekKeditaciSrc)>0) {
				$clanekKeditaci = mysql_fetch_object($clanekKeditaciSrc);

				if ($clanekKeditaci->compressed) $clanekKeditaci->text = gzuncompress($clanekKeditaci->text);

				if ($clanekKeditaci->schvaleno == 0) {
					inf("Článek je nyní ve stavu <strong>odeslán ke schválení</strong>, autorské úpravy nejsou možné.");
					$edit = false;
				}
				else {
					$edit = true;
				}
				if ($edit) {
?>
<div class='f-top'></div><div class='f-middle'>
<form action="/clanky/my/?akce=clanek-edit&amp;edit=<?php echo $edt;?>" method="post" class="f fd">
<fieldset>
<legend>Úprava článku <?php echo $clanekKeditaci->nazev;?></legend>
<div>
<label><span>Vrátil(a)</span><input value="<?php echo _htmlspec($clanekKeditaci->login);?>" maxlength="255" disabled="disabled" readonly="readonly" type="text" /></label>
</div>
<label for='anotace'><span>Anotace (stručně o článku)</span><input value="<?php echo _htmlspec($clanekKeditaci->anotace);?>" maxlength="255" type="text" name="anotace" id="anotace" /></label>
<div><label><span>Text článku</span></label><textarea rows='20' name='mess' /><?php
	if (stripos($oI->text, "</p>") !== false) {
	  echo nl2br(_htmlspec(stripslashes($clanekKeditaci->text)));
	}
	else {
	  echo _htmlspec(nl2p(trim(stripslashes($clanekKeditaci->text))));
	}
?></textarea></div>
<label><span>Komunikace s adminy</span><textarea rows='10' name='admins' /><?php echo _htmlspec($clanekKeditaci->admins);?></textarea></label>
<label><span>Poslední úpravy?</span><select name="finalizace"><option value="-">ne</option><option value="yes">ano</option></select></label>
<input class='button' type='submit' value='Upravit' />
</fieldset>
</form>
</div><div class='f-bottom'></div>
<?php
				}
				else {
?>
<div class='highlight-top'></div>
<div class='highlight-mid'>
<div class='cl'>Článek: <strong><?php echo $clanekKeditaci->nazev;?></strong></div>
<div class='cl'>Komunikace s administrátory:<br /><?php echo spit($clanekKeditaci->admins,1);?></div>
<div class='cl'>Anotace:<br /><?php echo _htmlspec($clanekKeditaci->anotace);?></div>
<div class='cl wysiwyg'>Text článku:<br /><?php echo spit($clanekKeditaci->text,1);?></div>
</div>
<div class='highlight-bot'></div>
<?php
				}
			}
			else {
				inf("Hledaný článek buď neexistuje nebo již byl schválen. Editace není možná.");
			}
		}

			echo "	<table class='diskuze-one'>\n";
			$myS = mysql_query("SELECT * FROM 3_clanky WHERE autor = '$_SESSION[uid]' ORDER BY schvaleno ASC, nazev ASC");
			if (mysql_num_rows($myS)>0) {
				echo "	<tr><td><ul class='ml20'>";
				while ($myGM1 = mysql_fetch_object($myS)) {
					$myGM1->odeslanotime = date("j.n.Y", $myGM1->odeslanotime);
					$myGM1->schvalenotime = date("j.n.Y", $myGM1->schvalenotime);
					if ($myGM1->schvaleno == "0") {
						echo "\n\t<li>"._htmlspec($myGM1->nazev)." | (odeslán ke schválení $myGM1->odeslanotime) - <a href='/clanky/my/?edit=$myGM1->nazev_rew'>zobrazit</a></li>";
					}
					elseif ($myGM1->schvaleno == "-1") {
						echo "\n\t<li>"._htmlspec($myGM1->nazev)." | (vráceno $myGM1->schvalenotime) - <a href='/clanky/my/?edit=$myGM1->nazev_rew'>zobrazit / upravit</a> ($myGM1->odeslanotime)</li>";
					}
					else {
						echo "\n\t<li><a class='permalink2' href='/clanky/$myGM1->nazev_rew/'>"._htmlspec($myGM1->nazev)."</a> | (schváleno)</li>";
					}
				}
				echo "</ul></td>
</tr>\n";
				echo "</table>\n";
			}
			else {
				echo "	<tr><td colspan='2'>Žádné články</td></tr>\n";
				echo "</ul></td>
  </tr>\n";
			  echo "</table>\n";
			}

?>
<?php
		}
		else {
			info("Tato sekce je vyhrazena jen registrovaným uživatelům.");
		}
		ob_flush();
	}
	elseif($slink != "" && $slink !== "new" && $slink !== "od" && $cA > 0){
	//overeni, zda uzivatel hodnotil
	$vL = "";
	$vF = "";
	if ($LogedIn == true && $_SESSION['uid'] != $autorId){
		$sR = mysql_fetch_row( mysql_query ("SELECT COUNT(*) FROM 3_rating WHERE uid = '$_SESSION[uid]' AND aid = '$id' AND sid = '1'") );
		if ($sR[0] > 0){
			$vL = "";
			$vF = "";
		}else{
			$vL = "<a href=\"#\" onclick=\"hide('rate');return false;\" class='permalink' title='Ohodnotit článek'>Ohodnotit</a>";
			$vF = "";
		}
	}

	if ($hasAdminRight) {
		$vL = "<a class='permalink' href='/clanky/$slink/ad/' title='Administrace přístupů k článku'>Admin</a>".$vL;
	}
	//kontrola zalozky
	$sB = chBook();
	echo "<h2 class='h2-head'><a href='/clanky/' title='$titleClanky'>$titleClanky</a></h2>";
	echo "<h3><a href='/$link/$nazev_rew/' title='$h2'>$nazev</a></h3>";

	echo "<p class='submenu'><a href='/clanky/' class='permalink' title='Zpět na výpis článků'>Výpis</a>$vL$sB<a href='#kom' class='permalink' title='Komentáře'>Komentáře</a>";
	if ($LogedIn) echo "<span class='hide'> | </span><a class='permalink' href='/clanky/$slink/stats/' title='Jednoduché statistiky návštěvnosti článku'>Statistiky</a>";
	echo "</p>\n";

	if ($cFound && $LogedIn) {
		if ($hasAdminRight && isset($sslink) && $sslink == "ad") {
			echo "<div>\n";
			echo "<p class='t-a-c'>Administrace článku $nazev</p>\n";
?>
<div class='f-top'></div><div class='f-middle'>
<form action="/<?php echo $link."/".$slink; ?>/?akce=administrace-dila&amp;d=add" method="post" class="f">
<fieldset>
<legend>Zakázání práv komentování</legend>
<label for='nickname'><span>Nick</span><input value="" maxlength="30" type="text" name="nickname" id="nickname" /></label>
<input class='button' type='submit' value='Přidat zákaz' />
</fieldset>
</form>
</div><div class='f-bottom'></div>
<?php
			$res = mysql_query("SELECT u.login, p.uid FROM 3_users AS u, 3_sekce_prava AS p WHERE p.uid = u.id AND p.sid = '$sid' AND p.aid = '$id' ORDER BY u.login_rew ASC");
			if ($res && mysql_num_rows($res)> 0) {
				echo "\n<div class='f-top'></div><div class='f-middle'><form class='f' method='post' action='/$link/$slink/ad/?akce=administrace-dila&amp;d=delete'><fieldset><legend>Zrušení zákazu komentování</legend>\n<ul class='hvyber'>\n";
				while($retItem = mysql_fetch_object($res)) {
					echo "<li><input class='checkbox' type='checkbox' value='$retItem->uid' name='nick[]' /> $retItem->login</li>\n";
				}
				echo "<input class='button' type='submit' value='Odebrat zákaz' />\n";
				echo "</ul>\n</fieldset></form></div><div class='f-bottom'></div>\n\n";
			}
			echo "</div>\n";
		}
		elseif ($sslink == "stats") {
			echo "<div class='highlight-top'></div>\n<div class='highlight-mid'>\n";
			echo "	<table cellspacing='0' cellpadding='0' border='0' class='edttbl'>\n";

			$statS = mysql_query("SELECT u.login,v.time,v.bookmark FROM 3_visited_$sid AS v, 3_users AS u WHERE u.id = v.uid AND v.aid = $id ORDER BY u.login ASC");
			$statSleduje = mysql_num_rows($statS);

			if ($statSleduje>0) {
				echo "		<tr><td>Nick</td><td>Čas posl.návštěvy</td><td>Záložka</td></tr>\n";
				$statZalozkyCnt = 0;
				while($stat = mysql_fetch_row($statS)){
					$maZalozku = "ne";
					if ($stat[2] == "1") {
						$statZalozkyCnt++;
						$maZalozku = "ano";
					}
					echo "		<tr><td>$stat[0]</td><td>".date("d.m.Y v H:i:s",$stat[1])."</td><td>$maZalozku</td></tr>\n";
				}
				$konc = "";
				if ($statSleduje < 5 && $statSleduje > 1) $konc = "é";
				elseif ($statSleduje >= 5) $konc = "ů";
				$koncZ = "ka";

				if ($statZalozkyCnt < 5 && $statZalozkyCnt > 1) $koncZ = "ky";
				elseif ($statZalozkyCnt >= 5 || $statZalozkyCnt == 0) $koncZ = "ek";

				echo "		<tr><td colspan='2'>$statSleduje uživatel".$konc."</td><td>".$statZalozkyCnt." zálož".$koncZ."</td></tr>\n";
			}
			else echo "		<tr><td>Článek nikdo nenavštěvuje.</td></tr>\n";
			echo "	</table>\n";
			echo "	<p class='art text t-a-c'><a href='/$link/$slink/' class='permalink2'>Zavřít Statistiky</a></p>\n";
			echo "</div>\n<div class='highlight-bot'></div>\n";
		}

	}

	//hodnoceni vraceno s chybou
	if (isSet($_GET['error'])){
	
		switch ($_GET['error']){
	
			case 1:
			$error = "Hodnocení nebylo vloženo. Zřejmě jste již hodnotil(a).";
			break;
			
			case 15:
			$error = "Záložka nemohla být vytvořena.";
			break;
			
			case 16:
			$error = "Překročen limit $zalozkyOmezeniCount povolených záložek.";
			break;
			
			case 17:
			$error = "Záložka nebyla odebrána.";
			break;
	
		}
	
		info($error);
	}elseif (isSet($_GET['ok'])){
			
		switch ($_GET['ok']){
			
			case 1:
				$ok = "Hodnocení uloženo, děkujeme.";
			break;
			
			case 15:
				$ok = "Záložka vytvořena.";
			break;
			
			case 16:
				$ok = "Záložka odebrána.";
			break;
	
		}
	
		ok($ok);

	}


	if ($sR[0] < 1 && $LogedIn){
?>
<div id='rate' class='hide'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/<?php echo $link."/".$slink."/"; ?>?akce=rating' method='post' class='f'>
<fieldset>
<legend>Ohodnotit článek <a href="#" onclick="hide('rate');return false;" class='permalinkb flink' title='Zavřít'>Zavřít</a></legend>
<label><span>Vaše hodnocení</span><select name='rating' style='width: 152px'><option value="x" selected="selected">- - - -</option><option value='0.5'>0,5 - mizerný</option><option value='1.0'>1,0 - bídný</option><option value='1.5'>1,5 - špatný</option><option value='2.0'>2,0 - lehce slabší</option><option value='2.5'>2,5 - průměrný</option><option value='3.0'>3,0 - slušný</option><option value='3.5'>3,5 - dobrý</option><option value='4.0'>4,0 - výborný</option><option value='4.5'>4,5 - vynikající</option><option value='5.0'>5,0 - nejlepší</option></select></label>
<input class='button' type='submit' value='Ohodnotit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>

<?php
	}
	ob_flush();

	echo "<div class='ci'>Autor : <a href='/uzivatele/".$autor_rew."/' class='permalink2' title='"._htmlspec($autor)."'><span".sl($oA->level, 1).">"._htmlspec($autor)."</span></a> &nbsp; ".sd($date)." &nbsp; <strong><a title='Články - sekce ".ss($oA->sekce)."' href='/clanky/?sekce=".$sekceSEO[$oA->sekce]."'>".ss($oA->sekce)."</a></strong> ".rating($hodnoceni, $hodnotilo)."</div>\n";
	echo "<div class='cl'>"._htmlspec($anotace)."</div>\n";
	echo "<div class='cl wysiwyg'>".$clanek."\n</div>\n";

	ob_flush();

	$lmt = 2;
	$sql = "SELECT nazev,nazev_rew,schvalenotime FROM 3_clanky WHERE id != '$oA->id' AND sekce = '".$oA->sekce."' AND schvaleno = '1' AND schvalenotime > '$date' ORDER BY schvalenotime ASC LIMIT 2";
	$novejsiS = mysql_query($sql);
	if ($novejsiS && mysql_num_rows($novejsiS)>0) {
	echo "<div class='cl'>Další články v kategorii <strong>".ss($oA->sekce)."</strong>:\n<ul>";
		if (mysql_num_rows($novejsiS) == 1) {
			$lmt = 3;
		}
		while($novejsi = mysql_fetch_object($novejsiS)){
			echo "<li>".date("d.m.Y", $novejsi->schvalenotime)." - <a href='/clanky/$novejsi->nazev_rew/' title='"._htmlspec($novejsi->nazev)."'>$novejsi->nazev</a></li>";
		}
	}
	else {
		$lmt = 4;
		echo "<div class='cl'>Další <strong>články</strong> v kategorii <strong>".ss($oA->sekce)."</strong>\n<ul>\n";
	}

	echo "<li>".date("d.m.Y", $date)." - ".$nazev."</li>";

	$sql = "SELECT nazev,nazev_rew,schvalenotime FROM 3_clanky WHERE id != '$oA->id' AND  sekce = '".$oA->sekce."' AND schvaleno = '1' AND schvalenotime < '$date' ORDER BY schvalenotime DESC LIMIT $lmt";
	$starsiS = mysql_query($sql);

	if ($starsiS && mysql_num_rows($starsiS)>0) {
		while($starsi = mysql_fetch_object($starsiS)){
			echo "<li>".date("d.m.Y", $starsi->schvalenotime)." - <a href='/clanky/$starsi->nazev_rew/' title='"._htmlspec($starsi->nazev)."'>$starsi->nazev</a></li>";
		}
	}
	echo "\n<ul>\n";
	echo "</div>\n";

	$sqlH = "SELECT u.login,r.rate FROM 3_rating AS r LEFT JOIN 3_users AS u ON u.id = r.uid WHERE r.sid = '1' AND r.aid = '$id' ORDER BY 1 ASC";
	$hodnotiloS = mysql_query($sqlH);

	if ($hodnotiloS && mysql_num_rows($hodnotiloS)>0) {

		echo "<div class='cl'>\n";
		echo "<p class='text'><a rel='nofollow' href='#' onclick='hide(\"hodnoceni-$slink\");return false;'>Kdo hodnotil článek $nazev?</a><br />\n";
		echo "<span id='hodnoceni-$slink' class='hide'>\n";

		$hodnotici = array();

		while ($osoba = mysql_fetch_row($hodnotiloS)){
			if ($osoba[1] > 0) {
				$osoba[0] .= " (".$osoba[1]."*)";
			}
			$hodnotici[] = $osoba[0];
		}
		echo join(", ",$hodnotici);
		echo "</span></p>\n</div>\n";
	}

	//modul pro diskuzi
	$AllowedTo = get_prava_sekce($sid,$id);
	ob_flush();
	include "./add/dis.php";

}elseif (isSet($cA) && $cA < 1){
echo "<h2 class='h2-head'><a href='/clanky/' title='$titleClanky'>$titleClanky</a></h2><h3><a href='/clanky/' title='Články - chyba'>Error</a></h3>";
echo "	<p class='submenu'><a href='/clanky/' class='permalink' title='Zpět na výpis Článků'>Zpět do Článků</a></p>\n";
info("Hledaný článek nebyl v databázi nalezen.");

}elseif ($slink == "new" && !$LogedIn) {
echo "<h2 class='h2-head'><a href='/clanky/' title='$titleClanky'>$titleClanky</a></h2><h3><a href='/clanky/new/' title='Odeslat nový článek ke schválení'>Odeslat nový článek</a></h3>";
echo "	<p class='submenu'><a href='/clanky/' class='permalink' title='Zpět na výpis Článků'>Zpět do Článků</a></p>\n";
info("Tato sekce je vyhrazena jen registrovaným uživatelům.");

}else{
?>
<h2 class='h2-head'><a href='/clanky/' title='<?php echo $titleClanky;?>'><?php echo $titleClanky;?></a></h2>
<h3><a href='<?php $_SERVER['REQUEST_URI']; ?>' title='<?php echo $shortTitle; ?>'><?php echo $shortTitle; ?></a></h3>
<?php
if ($slink == "new"){

//nastaveni vraceno s chybou
if (isSet($_GET['error'])){

switch ($_GET['error']){

case 1:
$error = "Název článku je příliš krátký.";
break;

case 2:
$error = "Anotace článku je příliš krátká.";
break;

case 3:
$error = "Text článku je příliš krátký.";
break;

}

info($error);
}elseif (isSet($_GET['ok'])){

$ok = "Článek byl v pořádku odeslán ke schválení.";

ok($ok);

}


?>

<p class='submenu'><a href='/clanky/' class='permalink' title='Zpět na výpis Článků'>Zpět do Článků</a></p>

<?php
if (strlen($txtIn)>2) {
echo "<div class='art text'>$txtIn</div>\n";
}
?>

<div class='f-top'></div>
<div class='f-middle'>
<form action="/clanky/?akce=clanky-new" method="post" name='form_for_new' id='form_for_new' class="f fd" onsubmit="return checkForNew('clanky','nazev_clanku',['nazev','sekce','anotace','mess'],false);">
<fieldset>
<legend>Odeslání článku ke schválení</legend>
<label><span>Název</span><input type='text' name='nazev' id='nazev_clanku' value='' maxlength='60' /></label>
<label><span>Sekce</span><select name='sekce' style='width: 402px'><option value='0'>Povídky</option><option value='1'>Poezie</option><option value='2'>Úvahy</option><option value='3'>Recenze</option><option value='4'>Postavy</option><option value='5'>Ostatní</option><option value='7'>Rozhovory</option><option value='9'>Předměty</option></select></label>
<label><span>Anotace</span><input type='text' name='anotace' value='' maxlength='250' /></label>
<div><label style="margin-bottom:0px;"><span>Text (smeták ukáže, jak bude vypadat po odeslání)</span></label><textarea rows='20' name='mess' /></textarea></div>
<input id='button' type='submit' value='Odeslat' style="margin-top: 5px;"/>
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<?php
}else{
?>
<p class='submenu'><?php if ($LogedIn) {
	$myArtAdd = "";
	$myArticlesCount = mysql_fetch_object(mysql_query("SELECT SUM(IF(schvaleno='-1',1,0)) AS vraceno, SUM(IF(schvaleno='0',1,0)) AS odeslano FROM 3_clanky WHERE autor = $_SESSION[uid] AND schvaleno != 1 GROUP BY schvaleno"));
	if ($myArticlesCount->vraceno>0 || $myArticlesCount->odeslano>0) {
		$myArtAdd = " (<strong title='Počet vrácených článků' class='helper'>$myArticlesCount->vraceno</strong> / <strong title='Počet článků odeslaných ke schválení' class='helper'>$myArticlesCount->odeslano</strong>)";
	}
echo "<a href='/clanky/new/' class='permalink' title='Odeslat nový článek ke schválení'>Odeslat nový článek</a><span class='hide'> | </span><a href='/clanky/my/' class='permalink' title='Moje články'>Moje články".$myArtAdd."</a><span class='hide'> | </span>";  
?> <a href="#" onclick="hide('jsStats');checkStats();return false;" class='permalink' title='Statistiky'>Statistiky</a> <?php } echo "<span class='hide'> | </span><a href='/diskuse/clanky/' class='permalink' title='Diskuze k sekci'>Diskuze k sekci</a><span class='hide'> | </span>";
 ?></p>

<?php
$statsL = ($LogedIn)? $_SESSION['uid'] : 0;
?>

<script type='text/javascript'>
var init = 0;
function checkStats(){
if(init == 0){
init = 1;
makeStats(2, <?php echo $statsL;?>);
}
}
</script>

<div id='jsStats' class='hide'></div>

<?php
	if (!isSet($_GET['index'])){
		$index = 1;
	}else{
		$index = (int) ($_GET['index']);
	}

	$from = ($index - 1) * $clankyPC; //od kolikate polozky zobrazit
	
	$sekce = "";
	if (isSet($_POST['sekce'])){
		$sekce = addslashes($_POST['sekce']);
	}elseif(isSet($_GET['sekce'])){
		$sekce = addslashes($_GET['sekce']);
	}

	//byla zvolena sekce?
	$sqlAdd = "";
	if ($sekce!=""){
		if (!is_numeric($sekce)) $sekce = $sekceSEO[$sekce];
		$sqlAdd = "AND c.sekce = '$sekce'";
	}

	$aS = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM 3_clanky AS c WHERE c.schvaleno = '1' $sqlAdd"));
	$aC = $aS[0];

	$limiter = "LIMIT $from, $clankyPC";

	if ($searchUser !== false) {
		$limiter = "";
	}

	if (!$LogedIn) $sql = "SELECT c.id, c.nazev, c.nazev_rew, c.schvalenotime, u.level, u.login_rew, u.login, c.anotace, c.sekce, c.hodnoceni, c.hodnotilo,
0 AS v_uid, 0 AS unread_comms,
(SELECT COUNT(*) FROM 3_comm_1 AS cm WHERE cm.aid = c.id) AS all_comms

FROM 3_clanky AS c, 3_users AS u

WHERE c.schvaleno = 1 AND u.id = c.autor $sqlAdd $autorSQL 
ORDER BY c.schvalenotime 
DESC $limiter";
	else $sql = "SELECT c.id, c.nazev, c.nazev_rew, c.sekce, c.anotace, c.schvalenotime, u.level, u.login, u.login_rew, c.hodnotilo, c.hodnoceni,
(SELECT COUNT(*) FROM 3_comm_1 WHERE 3_comm_1.aid = c.id) AS all_comms,
3_visited_1.news AS unread_comms, 3_visited_1.uid AS v_uid
FROM 3_clanky AS c
LEFT JOIN 3_visited_1 ON 3_visited_1.aid = c.id AND 3_visited_1.uid = $_SESSION[uid]
LEFT JOIN 3_users AS u ON c.autor = u.id
WHERE c.schvaleno = 1 $sqlAdd $autorSQL 
ORDER BY c.schvalenotime 
DESC $limiter";

	$sel_clanky = mysql_query($sql);

	if (mysql_num_rows($sel_clanky)>0) {
		$uC = mysql_num_rows($sel_clanky);
	}
	else {
		$uC = 0;
	}

	if ($uC < 1){
		if ($sekce == "" && $slink != "od") {
			info ("V článcích zatím není žádný příspěvek.");
		}
		else {
			if ($searchUser === false) {
				info ("V této sekci se zatím nenachází žádný článek.");
			}
			else {
				if ($sekce == "") {
					info ("Autor $searchUser[1] zatím nemá žádné publikované články.");
				}
				else {
					info ("Autor $searchUser[1] zatím nemá v této sekci žádné publikované články.");
				}
			}
		}
	}else{
		if ($slink == "" && $from == 0) {
			echo "<div class='art text t-a-c'><p><strong>Jednotlivé sekce:</strong><br />
<a class='permalink' href='/clanky/?sekce=povidky' title='Články - sekce Povídky'>Povídky</a> <a class='permalink' href='/clanky/?sekce=poezie' title='Články - sekce Poezie'>Poezie</a> <a class='permalink' href='/clanky/?sekce=uvahy' title='Články - sekce Úvahy'>Úvahy</a> <a class='permalink' href='/clanky/?sekce=recenze' title='Články - sekce Recenze'>Recenze</a> <a class='permalink' href='/clanky/?sekce=postavy' title='Články - sekce Postavy'>Postavy</a> <a class='permalink' href='/clanky/?sekce=vildovy-cesty' title='Články - sekce Vildovy cesty'>Vildovy cesty</a> <a class='permalink' href='/clanky/?sekce=ostatni' title='Články - sekce Ostatní'>Ostatní</a> <a class='permalink' href='/clanky/?sekce=rozhovory' title='Články - sekce Rozhovory'>Rozhovory</a>
<a class='permalink' href='/clanky/?sekce=300-z-mista' title='Články - sekce 300 z místa'>300 z místa</a> <a class='permalink' href='/clanky/?sekce=predmety' title='Články - sekce Předměty'>Předměty</a>
</p></div>\n";
		}
		if ($searchUser === false && strlen($txtIn)>2) {
			echo "<div class='art text'>$txtIn</div>\n";
		}
		if ($searchUser === false) {
?>

<p class='strankovani'><?php $pagination = make_pages($aC, $clankyPC, $index); echo $pagination; ?></p>

<?php
		}

		$i = 0;

		if ($searchUser !== false){
			$clankyOd = "Profil ~ ";
			$uzivateleLink = "uzivatele";
		}
		else {
			$clankyOd = "Všechny články ~ ";
			$uzivateleLink = "clanky/od";
		}

		while ($oA = mysql_fetch_object($sel_clanky)){
		
			$i++;
		
			$nazev = $oA->nazev;
			$nazev = _htmlspec(mb_strtoupper(mb_substr($nazev, 0, 1)).mb_substr($nazev, 1));
			$anotace = _htmlspec(stripslashes($oA->anotace));
			$sekceC = ss($oA->sekce);
			$w = rating($oA->hodnoceni, $oA->hodnotilo);
			$oA->login = _htmlspec(stripslashes($oA->login));
			$cC = getComm($oA->id, 1,true,$oA->unread_comms,$oA->all_comms,$oA->v_uid);

			echo "
<div class='highlight-top'></div><div class='highlight-mid'><table width='95%' cellpadding='1'>
<tr><td class='c-nazev' width='75%'><a href='/clanky/$oA->nazev_rew/' class='permalinkb' title='$nazev'>$nazev</a></td><td class='c-sub'>&sect; <strong><a title='Články - sekce $sekceC' href='/clanky/?sekce=".$sekceSEO[$oA->sekce]."'>$sekceC</a></strong></td></tr>
<tr><td class='c-sub'>$anotace</td><td class='c-sub'>$w</td></tr>
<tr><td class='c-sub'>".sd($oA->schvalenotime)." ~ <a href='/$uzivateleLink/$oA->login_rew/' class='permalinkb2' title='".$clankyOd.$oA->login."'><span".sl($oA->level, 1).">$oA->login</span></a></td><td class='c-sub'><a href='/clanky/$oA->nazev_rew/#kom' title='Přečíst komentáře'>Komentáře</a> : $cC</td></tr>
</table></div><div class='highlight-bot'></div>
";
		
			if ($i%10 == 0)
				ob_flush();
			}
		
			if ($searchUser === false) {
?>

<p class='strankovani'><?php echo $pagination; ?></p>

<?php
			}
		}

?>

<?php
	}
}
?>