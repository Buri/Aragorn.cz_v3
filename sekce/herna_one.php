<?php
$sB = "";
if ($hItem->schvaleno == '1') $sB = chBook();

$jTyp = $hItem->typ;
$cid = $hItem->id;
$jTypString = ($jTyp == 0) ? "drd"  :"orp";
$chatJeskyneOver = "";
$users_XYZs = mysql_query("SELECT login FROM 3_cave_users WHERE cid = $hItem->id AND pozice != 'g'");
if (mysql_num_rows($users_XYZs) > 0) {
	while ($ChatUser = mysql_fetch_object($users_XYZs)) {
		$chatOver[] = stripslashes($ChatUser->login);
	}
	$chatJeskyneOver = " onmouseover=\"ddrivetip('<b>Uživatelé na chatu:</b><br />"._htmlspec(join("<br />",$chatOver))."')\" onmouseout=\"hidedrivetip();\"";
}

echo "<h2 class=\"h2-head\"><a href=\"/herna/\" title=\"$titleHerna\">$titleHerna</a></h2><h3><a href='/herna/$slink/'>".$hItem->nazev.$subSection."</a></h3>
	<p class='submenu'><a href='/herna/' class='permalink' title='Zpět na výpis jeskyní'>Herna</a>$sB";

$allow = "no";

if ($LogedIn && $hItem->schvaleno == '1') {
	if ($pFound == true) {
		if ($postava->uid != $_SESSION['uid']) {
			$MojePostava = false; 
			if ($uzMamPostavu > 0) {
				$MyPostava = $mojePostavaObjekt;
				$MyPostava->jmeno = _htmlspec(stripslashes($MyPostava->jmeno));
				$schvalenaPostava = $MyPostava->schvaleno;
			}
			else {
				$uzMamPostavu = 0;
			}
		}
		else {
			$MojePostava = true; 
			$uzMamPostavu = 1;
			$MyPostava = $mojePostavaObjekt;
		}
	}
	elseif ($_SESSION['uid'] != $hItem->uid) {
		if ($uzMamPostavu > 0) {
			$postava = $mojePostavaObjekt;
			$postava->jmeno = _htmlspec(stripslashes($postava->jmeno));
			$schvalenaPostava = $postava->schvaleno;
		}
		else {
			$uzMamPostavu = 0;
		}
	}
	else {
		$uzMamPostavu = 0;
	}

	$komu = array();
	$KomuJmena = array();
	$KomuIco = array();
	$KomuRewIds = array();
	$allow = "no";
	if ($LogedIn) {
		$allow = "reg";
	}
	if ($hItem->povolreg == '0') {
		$allow = "no";
	}

	if ( $LogedIn && ($hItem->uid == $_SESSION["uid"] || ($hItem->PJs && isset($hItem->PJs[$_SESSION['uid']])) ) ) {
		if ($hItem->uid == $_SESSION['uid']) {
			$allow = "pj";
		}
		else {
			$allow = "pj2";
		}
	}
	elseif ($uzMamPostavu > 0) {
		$allow = "edit";
		if ($schvalenaPostava == '1') {
			$KomuJmena[$hItem->vlastnik_rew] = $hItem->vlastnik;
			$KomuIco[$hItem->vlastnik_rew] = $hItem->ico;
			$KomuRewIds[$hItem->vlastnik_rew] = $hItem->uid;
			$komu[] = "<label for='lbl-$hItem->uid'><input id='lbl-$hItem->uid' type='checkbox' class='checkbox' name='septat[]' value='$hItem->vlastnik_rew' />&nbsp;$hItem->vlastnik&nbsp;(PJ)</label>";
			$allow = "hrac";
		}
	}

	if ($allow == "pj" || $allow == "pj2" || $allow == "hrac") {

		if ($allowsPJ['prispevky'] && $hItem->uid != $_SESSION['uid']) {
			$KomuJmena[$hItem->vlastnik_rew] = $hItem->vlastnik;
			$KomuIco[$hItem->vlastnik_rew] = $hItem->ico;
			$KomuRewIds[$hItem->vlastnik_rew] = $hItem->uid;
			$komu[-1] = "<label for='lbl-$hItem->uid'><input id='lbl-$hItem->uid' type='checkbox' class='checkbox' name='septat[]' value='$hItem->vlastnik_rew' />&nbsp;$hItem->vlastnik&nbsp;(PJ)</label>";
		}

		if ($hItem->PJs) {
			foreach ($hItem->PJs as $k => $pjHelp) {
				if ($pjHelp->uid != $_SESSION['uid']) {
					$komu[] = "<label for='lbl-$k'><input id='lbl-$k' type='checkbox' class='checkbox' name='septat[]' value='$pjHelp->login_rew' />&nbsp;$pjHelp->login&nbsp;(pomocný PJ)</label>";
				}
				$KomuJmena[$pjHelp->login_rew] = $pjHelp->login;
				$KomuRewIds[$pjHelp->login_rew] = $pjHelp->uid;
				$KomuIco[$pjHelp->login_rew] = $pjHelp->ico;
			}
		}

		for ($ai=0;$ai<count($jeskyneHraci);$ai++) {
			$hracPostava = $jeskyneHraci[$ai]['objekt'];
			if ($hracPostava->schvaleno == '1' && $hracPostava->uid != $_SESSION['uid']) {
				$komu[] = "<label for='lbl-$hracPostava->uid'><input id='lbl-$hracPostava->uid' type='checkbox' class='checkbox' name='septat[]' value='$hracPostava->login_rew' />&nbsp;$hracPostava->login&nbsp;($hracPostava->jmeno)</label>";
				$KomuJmena[$hracPostava->login_rew] = $hracPostava->login;
				$KomuRewIds[$hracPostava->login_rew] = $hracPostava->uid;
				$KomuIco[$hracPostava->login_rew] = $hracPostava->ico;
			}
		}
	}

	switch ($allow) {
		case "edit":
			if ($pFound == true) {
			}
			else {
				echo "<span class='hide'> | </span><a$chatJeskyneOver href='/herna/$slink/ch/?akce=cave-enter' title='Jeskynní chat' class='permalink'>Chat</a><span class='hide'> | </span><a href='/herna/$slink/mapy/' class='permalink' title='Seznam map jeskyně'>Mapy</a><span class='hide'> | </span><a href='/herna/$slink/$postava->jmeno_rew/' class='permalink' title='$postava->jmeno'>Stránka postavy</a>";
			}
		break;
		case "reg":
			echo "<span class='hide'> | </span><a href='/herna/$slink/reg/' class='permalink' title='Registrovat se do této jeskyně'>Přihláška</a><span class='hide'> | </span><a$chatJeskyneOver href='/herna/$slink/ch/?akce=cave-enter' title='Jeskynní chat' class='permalink'>Chat</a><span class='hide'> | </span><a href='/herna/$slink/mapy/' class='permalink' title='Seznam map jeskyně'>Mapy</a>";
		break;
		case "hrac":
			echo "<span class='hide'> | </span><a$chatJeskyneOver href='/herna/$slink/ch/?akce=cave-enter' title='Jeskynní chat' class='permalink'>Chat</a><span class='hide'> | </span><a href='/herna/$slink/mapy/' class='permalink' title='Seznam map jeskyně'>Mapy</a>";
			if ($hItem->obchod == "1" && $hItem->typ == '0') echo "<span class='hide'> | </span><a href='/herna/$slink/shop/' class='permalink2' title='Nakupte si vybavení'>Obchod</a>";
			if ($pFound == true) {
			}
			else echo "<span class='hide'> | </span><a href='/herna/$slink/$postava->jmeno_rew/' class='permalink' title='$postava->jmeno'>Postava</a>";
		break;
		case "pj":
		case "pj2":
			echo "<span class='hide'> | </span><a href='/herna/$slink/pj/' title='Nastavení jeskyně a další kousky pro Pána jeskyně' class='permalink'>PJ</a>";
			echo "<span class='hide'> | </span><a$chatJeskyneOver href='/herna/$slink/ch/?akce=cave-enter' title='Jeskynní chat' class='permalink'>Chat</a>";
			echo "<span class='hide'> | </span><a href='/herna/$slink/mapy/' class='permalink' title='Seznam map jeskyně'>Mapy</a>";
			if ($hItem->typ == '0') {
				echo "<span class='hide'> | </span><a href='/herna/$slink/shop/' class='permalink' title='Povolit / zakázat obchod, nastavit ceny vybavení'>Obchod</a>";
			}
		break;
		default:
			if ($LogedIn) {
				echo "<span class='hide'> | </span><a$chatJeskyneOver href='/herna/$slink/ch/?akce=cave-enter' title='Jeskynní chat' class='permalink'>Chat</a>";
			}
			echo "<span class='hide'> | </span><a href='/herna/$slink/mapy/' class='permalink' title='Seznam map jeskyně'>Mapy</a>";
		break;
	}
}
elseif ($LogedIn == true && $hItem->schvaleno != '1') {
	if ($hItem->uid == $_SESSION['uid']) {
		echo "<span class='hide'> | </span><a href='/herna/$slink/pj/' title='Nastavení jeskyně a další kousky pro Pána jeskyně' class='permalink'>PJ</a>";
	}
}

echo "</p>\n";

if (isSet($_GET['error'])) {
	switch ($_GET['error']) {
	case 15:
	  info("Záložka nemohla být vytvořena.");
	break;
	case 16:
	  info("Překročen limit $zalozkyOmezeniCount povolených záložek.");
	break;
	case 17:
	  info("Záložka nebyla odebrána.");
	break;
	}
}
elseif (isSet($_GET['ok'])) {
	switch ($_GET['ok']) {
	case 15:
	  ok("Záložka vytvořena.");
	break;
	case 16:
	  ok("Záložka odebrána.");
	break;
	}
}

if ($jInc != "") {
	include $jInc;
}

if (isset($usersOnlineArray[$hItem->uid])) {
	$pjMouseOver = "<span class=\\'hpositive\\'>Online</span>";
}
else {
	$pjMouseOver = "<span class=\\'hnegative\\'>Offline</span>";
}
$pjMouseOver = "onmouseover=\"ddrivetip('".$pjMouseOver."<br /> Posl.aktivita: ".date("j.n.Y H:i", $hItem->aktivitapj)."<br /><img src=\\'/system/icos/$hItem->ico\\' />')\" onmouseout='hidedrivetip();'";
$uniCave = $hItem->id."-".$hItem->uid."-".mt_rand(0, 10).mt_rand(0, 10).mt_rand(0, 10)."-cave-texts";

if ($hItem->nastenka == "") {
	$hItem->nastenka = "<em>Nástěnka jeskyně je prázdná.</em>";
}

echo "
<div class='highlight-top'></div>
<div class='highlight-mid'>
	<table class='diskuze-one'>
		<tr><td class='hpopis'>Pán Jeskyně:</td><td>
		<p><a href='/uzivatele/$hItem->vlastnik_rew/' title='Profil uživatele' ".$pjMouseOver." class='permalink2'>$hItem->vlastnik</a></p>
		</td></tr>\n";
		if ($hItem->PJs) {
			echo "<tr><td class='hpopis'>Pomocn". ((count($hItem->PJs) > 1) ? "í" : "ý") . " PJ:</td><td><p>";
			$kk = array();
			foreach ($hItem->PJs as $k=>$v) {
				$mouseover = "onmouseover=\"ddrivetip('<span class=\\'".($uzivatele[$k]['stav'] ? "hpositive\\'>Online" : "hnegative\\'>Offline")."</span>".($v->aktivita ? ("<br /> Posl.aktivita: ".date("j.n.Y H:i", $v->aktivita)) : '') . "<br /><img src=\\'http://s1.aragorn.cz/i/" . ($v->ico ? $v->ico : 'default.jpg') . "\\' />')\" onmouseout='hidedrivetip();'";
				$kk[] = "<a href='/uzivatele/$v->login_rew/' title='Profil uživatele' class='permalink2' $mouseover>$v->login</a>";
			}
			echo join($kk, ", ");
			echo "</p></td></tr>\n";
		}
		echo "<tr><td>Systém:</td><td>
		<p>" . ($jTypString == "drd" ? "<acronym title='Dračí Doupě' xml:lang='cs'>DrD</acronym>" : "<acronym title='Open Role Play - Volný hrací systém' xml:lang='cs'>ORP</acronym>") . "</p>
		</td></tr>
		<tr><td class='hpopis'>Nové příhlášky:</td><td><p>" . ($hItem->povolreg ? 'Přijímáme' : 'Nepřijímáme') . "</p></td></tr>
		<tr><td class='hpopis'>Popis jeskyně:</td><td>
		<p>" . nl2br($hItem->popis) . "</p>
		</td></tr>\n";

if ($hItem->keywords && strlen($hItem->keywords) > 0) {
	echo "		<tr><td>Klíčová slova:</td><td>
		<p><small>"._htmlspec($hItem->keywords)."</small></p>
		</td></tr>\n";
}
else {
	echo "		<tr><td>Klíčová slova</td><td><p><span class='hlight3'>Tato jeskyně nemá nastavena klíčová slova.</span>". ($LogedIn && $_SESSION['uid'] == $hItem->uid ? " <a href='/$link/$slink/pj/'>Upravit</a>" : "")."</p></td></tr>\n";
}

echo "		<tr><td>Jaké hráče:</td><td>
		<p>" . nl2br($hItem->hraci_hleda) . "</p>
		</td></tr>
		<tr><td>Nástěnka:</td><td><a class=\"permalink2\" href=\"#\" onclick=\"hide('$uniCave');return false;\" title=\"Zobrazí/skryje Nástěnku jeskyně\">Zobrazit/skrýt Nástěnku</a>
		<div id='$uniCave' class='hide' style='white-space:pre-line;'>
" . stripslashes($hItem->nastenka) . "
		</div></td></tr>
		<tr><td>Hráči: (<span>$pAktivPlayers</span>/$hItem->hraci_pocet)</td><td>".$pp."</td></tr>";
if ($allowsPJ['poznamky'] && $hItem->schvaleno == '1') {
	echo "<tr><td colspan='2'><div><a href='#' onclick='var t=$(this);t.getNext().setProperty(\"text\",\"Načítám...\").getParent().load(\"/ajaxing.php?do=poznamky-pj&amp;nazev=$slink\");t.dispose();return false;'>Poznámky PJ</a><em></em></div></td></tr>\n";
}
echo "
	</table>
</div>
<div class='highlight-bot'></div>\n";

if ($hFound == true && $hItem->schvaleno == '1') {
	ob_flush();
//	echo "	<a name='kom'></a>\n";
	include "./add/dis.php";
}
elseif ($hFound && $LogedIn && $hItem->schvaleno != '1') {
	echo "<div class='art'><p>Tato jeskyně není schválená, proto nelze psát příspěvky do&nbsp;fóra.</p></div>\n";
}

?>
