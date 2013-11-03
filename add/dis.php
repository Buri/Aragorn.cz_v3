<?php
/*
diskuzni modul
clanky - 1z
galerie - 2
diskuze - 3
herna - 4
*/

$js = array();
$jsR = "";
$forumUpAddon = false;
$pagination = $display = "";
if ($LogedIn) {
	$sessionUid = $_SESSION['uid'];
}

switch($link){

case "clanky":
	$sid = 1;
	$display = "hide";
	$titleDis = "Komentáře";
break;
case "galerie":
	$sid = 2;
	$display = "hide";
	$titleDis = "Komentáře";
break;
case "diskuze":
	$sid = 3;
	$titleDis = "Diskuze";
break;
case "herna":
	$sid = 4;
	$titleDis = "Fórum";
	if ($LogedIn && $allowsPJ['prispevky'] && isset($_GET['podle']) && isset($KomuJmena[$_GET['podle']]) && $_SESSION['login_rew'] !== $_GET['podle']) {
		$forumUpAddon = 'Výpis příspěvků jako <em><strong>'.$KomuJmena[$_GET['podle']]."</strong></em> <a href='/herna/$slink/#kom'><em>Vlastní&nbsp;pohled!</em></a> ";
		$sessionUid = $KomuRewIds[$_GET['podle']];
	}
break;

}

$triSta = $sid == 1 && isset($oA) && $oA->sekce == 8 ? true : false;

$ajaxTxt = "<?xml version=\"1.0\" encoding=\"utf-8\" ?"."><div>";

if (!$ajaxed) {
	function echoNonAjaxed($t) {
		echo $t;
	}
	function echoAjaxed($t) {
		echo $t;
	}
}
else {
	function echoNonAjaxed($t) {
		return;
	}
	function echoAjaxed($t) {
		global $ajaxTxt;
		$ajaxTxt .= "<comm><![CDATA[$t]]></comm>";
	}
}

	if ($LogedIn) echoNonAjaxed("<h3 class='h3-middle'><a href='#kom' title='Nejnovější příspěvek'>$titleDis &dArr;</a></h3>\n");
	else echoNonAjaxed("<h3 class='h3-middle'><a href='#kom' title='$titleDis'>$titleDis &dArr;</a></h3>\n");


if ($LogedIn == false){
	echoNonAjaxed("<a name='kom' id='kom' title='$titleDis'></a>\n");
	switch ($sid) {
		case 1:
		case 2:
			info("Pro přidání komentářů je třeba se nejprve přihlásit.",$ajaxed);
		break;
		case 3:
			info("Pro přidávání diskuzních příspěvků je třeba se nejprve přihlásit.",$ajaxed);
		break;
		case 4:
			info("Pro přidání příspěvku je třeba se nejprve přihlásit a být registrovaný v této jeskyni.",$ajaxed);
		break;
	}

}elseif ($sid == 3){
	if ($dFound && $dItem->closed == 0 && ($AllowedTo == "write" || $AllowedTo == "all" || $AllowedTo == "superall") && !$ajaxed) {
?>

<p class='submenu'><a href="#" onclick="hide('k');return false;" class='permalink' title='Přidat příspěvek'>Přidat příspěvek</a> <a href="#" onclick="comm_del();return false;" class='permalink' title='Smazat označené'>Smazat označené</a> <a href="#" onclick="$$('#dis-module-x input[type=checkbox]').each(function(el){el.setProperty('checked',!el.getProperty('checked'))});return false;" class='permalink' title='Označit opačně boxy pro smazání'>Označit opačně</a></p>
<div id='k' class='<?php echo $display; ?>'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/<?php echo $link; ?>/<?php echo $slink; ?>/?akce=post-comm' name='txt' method='post' class='f fd'>
<fieldset>
<legend>Nová zpráva <a href="#" onclick="hide('k');return false;" class='permalink flink' title='Zavřít'>Zavřít</a></legend>
<label><span>Příspěvek</span><textarea cols='70' rows='8' name='mess' id='km'></textarea><span><a href='#k' onclick='vloz_tag("b");return false;'><img src='/system/editor/bold.jpg' alt='Tučně' title='Tučně' /></a> <a href='#k' onclick='vloz_tag("i");return false;'><img src='/system/editor/kur.jpg' alt='Kurzívou' title='Kurzívou' /></a> <a href='#k' onclick='vloz_tag("u");return false;'><img src='/system/editor/und.jpg' alt='Podtrhnout' title='Podtrhnout' /></a> <a href='#k' onclick='editor(4);return false;'><img src='/system/editor/link.jpg' alt='Odkaz' title='Odkaz' /></a> <a href='#k' onclick='editor(5);return false;'><img src='/system/editor/pict.jpg' alt='Obrázek' title='Obrázek' /></a> <a href='#k' onclick='vloz_tag("spoiler");return false;'>Spoiler</a> <a href='#k' onclick='vloz_tag("color1");return false;' class='hlight1'>Barva 1</a> <a href='#k' onclick='vloz_tag("color2");return false;' class='hlight2'>Barva 2</a> <a href='#k' onclick='vloz_tag("color3");return false;' class='hlight3'>Barva 3</a></span></label>
<input class='button' type="button" onclick="do_preview('km'); return false;" value="Náhled příspěvku" /><br /><br /><input class='button' type='submit' onclick='javascript:this.value="Odesílám...";' value='Odeslat zprávu' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>
<?php
	}
	else {
		if ($dFound && $dItem->closed == 1 && $AllowedTo != "nothing") {
			$AllowedTo = "read";
		}
	}
}
elseif ($sid == 4) {
	if ((($allow == "pj2" && $allowsPJ['prispevky']) || $allow == "hrac" || $allow == "pj") && !$ajaxed) {
?>
<p class='submenu'><a href="#" onclick="hide('k');return false;" class='permalink' title='Přidat příspěvek'>Přidat příspěvek</a> <a href="#" onclick="comm_del();return false;" class='permalink' title='Smazat označené'>Smazat označené</a> <a href="#" onclick="$$('#dis-module-x input[type=checkbox]').each(function(el){el.setProperty('checked',!el.getProperty('checked'))});return false;" class='permalink' title='Označit opačně boxy pro smazání'>Označit opačně</a></p>
<div id='k' class='<?php echo $display; ?>'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/herna/<?php echo $slink; ?>/?akce=post-comm' name='txt' method='post' class='f fd'>
<fieldset>
<legend>Nový příspěvek <a href="#" onclick="hide('k');return false;" class='permalink flink' title='Zavřít'>Zavřít</a></legend>
<label><span>Příspěvek</span><textarea cols='70' rows='8' name='mess' id='km'></textarea><span><a href='#k' onclick='vloz_tag("b");return false;'><img src='/system/editor/bold.jpg' alt='Tučně' title='Tučně' /></a> <a href='#k' onclick='vloz_tag("i");return false;'><img src='/system/editor/kur.jpg' alt='Kurzívou' title='Kurzívou' /></a> <a href='#k' onclick='vloz_tag("u");return false;'><img src='/system/editor/und.jpg' alt='Podtrhnout' title='Podtrhnout' /></a> <a href='#k' onclick='editor(4);return false;'><img src='/system/editor/link.jpg' alt='Odkaz' title='Odkaz' /></a> <a href='#k' onclick='editor(5);return false;'><img src='/system/editor/pict.jpg' alt='Obrázek' title='Obrázek' /></a> <a href='#k' onclick='vloz_tag("spoiler");return false;'>Spoiler</a> <a href='#k' onclick='vloz_tag("color1");return false;' class='hlight1'>Barva 1</a> <a href='#k' onclick='vloz_tag("color2");return false;' class='hlight2'>Barva 2</a> <a href='#k' onclick='vloz_tag("color3");return false;' class='hlight3'>Barva 3</a></span></label>
<?php
if (($allow == "pj" || $allow == "hrac" || $allowsPJ['prispevky']) && count($komu) > 0) {
	$komu = join(" | ",$komu);
	echo "<p class='komuseptat'><span>Komu šeptat:</span>&nbsp;".$komu." [<a href='' onclick=\"$$('.komuseptat input[type=checkbox]').each(function(e){e.set('checked', !e.get('checked'));});return false;\">Označit opačně </a>]</p>";
	if ($allowsPJ['prispevky']) {
		$komu2 = array();
		foreach($KomuJmena as $u_rew => $u_login) {
		  if ($u_rew != $_SESSION['login_rew']) {
				$komu2[$u_rew] = $u_rew."#kom'>".$u_login;
			}
		}
		if (count($komu2) > 0) {
			echo "<p class='komuseptat'><span>Zobrazit fórum jako:</span>&nbsp;";
				echo "<a href='/herna/$slink/?podle=".join("</a> | <a href='/herna/$slink/?podle=", $komu2)."</a>";
			echo "</p>";
		}
	}
}
else {
	echo "Není komu šeptat.";
}
echo "<p class='hkostka'><a href='/herna/$slink/?akce=k6' title='Hodit na šestistěnné kostce'>k6</a> | <a href='/herna/$slink/?akce=k10' title='Hodit na desetistěnné kostce'>k10</a> | <a href='/herna/$slink/?akce=k100' title='Hodit na procentuální kostce'>k%</a>";
echo " | <a href='/herna/$slink/?akce=2k6plus' title='Primárně pro DrD+ - hodit na šestistěnné kostce hod 2k6+'>2k6+</a> | <a href='/herna/$slink/?akce=4k6' title='Hod Fate (1,2 = minus, 3,4 = 0, 5,6 = plus)'>4k6</a> | <a href='/herna/$slink/?akce=k20' title='Hodit na dvacetistěnné kostce'>k20</a> | <a href='/herna/$slink/?akce=XkY' title='Hodit vlastní rozsah' onclick='return throw_dices(this)'>x&middot;kY</a>";
echo "</p>\n";
?>
<input class='button' type="button" onclick="do_preview('km'); return false;" value="Náhled příspěvku" /><br /><br /><input class='button' type='submit' onclick='javascript:this.value="Odesílám...";' value='Odeslat zprávu' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>
<?php	}
}
else {
	if ($AllowedTo == 0) {
		info("Zde nemáte právo psát příspěvky!",$ajaxed);
	}
	else {
		if (!$ajaxed) {
?>

<p class='submenu'><a href="#" onclick="hide('k');return false;" class='permalink' title='Přidat komentář'>Přidat příspěvek</a> <a href="#" onclick="comm_del();return false;" class='permalink' title='Smazat označené'>Smazat označené</a> <a href="#" onclick="$$('#dis-module-x input[type=checkbox]').each(function(el){el.setProperty('checked',!el.getProperty('checked'))});return false;" class='permalink' title='Označit opačně boxy pro smazání'>Označit opačně</a></p>
<div id='k' class='<?php echo $display; ?>'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/<?php echo $link; ?>/<?php echo $slink; ?>/?akce=post-comm' name='txt'	method='post' class='f fd'>
<div class='ml20'><p><strong>Pravidla pro vkládání příspěvků:</strong></p>
<ul>
<li>Veškeré <strong>vulgární</strong> (včetně vyhvězdičkovaných), <strong>urážlivé</strong> a nesouvisející komentáře (mimo téma) budou <strong>smazány</strong>!</li>
<li>Pokud s něčím nesouhlasíte, <strong>uveďte důvod a přidejte argumenty</strong>. Jinak je váš názor k ničemu a nemá tu co dělat.</li>
<li>Komentář psaný celý velkými písmeny anebo tučně či jiné <strong>formátovací zvrhlosti</strong>, bude smazán bez ohledu na jeho obsah.</li>
<li>Pište, prosím, <strong>s diakritikou</strong>. Tolik vás to nezdrží a bude se to po vás ostatním lépe číst.</li>
</ul>
<p>Vložením komentáře berete tyto body na vědomí.</p>
</div>
<fieldset>
<legend>Nová zpráva <?php if($triSta) : ?> (MAX 300 ZNAKŮ)<?php endif ?><a href="#" onclick="hide('k');return false;" class='permalink flink' title='Zavřít'>Zavřít</a></legend>
<label><span>Příspěvek<?php if($triSta) : ?> <em id="charCounter">0 znaků</em><?php endif; ?></span><textarea cols='70' <?php if($triSta){ ?> maxlength="300" onkeyup="setTimeout(function(){var d=document.getElementById('km');if(d.value.length>299){d.value=d.value.substring(0,300);}document.getElementById('charCounter').innerHTML=' '+(d=d.value.length)+' znak'+((d < 1 || d > 4) ? 'ů' : (d < 2 ? '' : 'y' ))},25);" <?php } ?> rows='8' name='mess' id='km'></textarea><?php if (!$triSta){ ?><span><a href='#k' onclick='vloz_tag("b");return false;'><img src='/system/editor/bold.jpg' alt='Tučně' title='Tučně' /></a>
	<a href='#k' onclick='vloz_tag("i");return false;'><img src='/system/editor/kur.jpg' alt='Kurzívou' title='Kurzívou' /></a>
	<a href='#k' onclick='vloz_tag("u");return false;'><img src='/system/editor/und.jpg' alt='Podtrhnout' title='Podtrhnout' /></a>
	<a href='#k' onclick='editor(4);return false;'><img src='/system/editor/link.jpg' alt='Odkaz' title='Odkaz' /></a>
	<a href='#k' onclick='editor(5);return false;'><img src='/system/editor/pict.jpg' alt='Obrázek' title='Obrázek' /></a>
	<a href='#k' onclick='vloz_tag("spoiler");return false;'>Spoiler</a> <a href='#k' onclick='vloz_tag("color1");return false;' class='hlight1'>Barva 1</a>
	<a href='#k' onclick='vloz_tag("color2");return false;' class='hlight2'>Barva 2</a>
	<a href='#k' onclick='vloz_tag("color3");return false;' class='hlight3'>Barva 3</a></span><?php } ?></label>
<input class='button' type="button" onclick="do_preview('km'); return false;" value="Náhled příspěvku" /><br /><br /><input class='button' type='submit' onclick='javascript:this.value="Odesílám...";' value='Odeslat zprávu' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>
<?php
		}
	}
}

ob_flush();

//vypis komentaru
if ($sid == 4) {
	if ($LogedIn) {
		if (!$ajaxed) {
			$sql = "SELECT count(*) FROM 3_comm_4 WHERE aid = $id AND ((whispering LIKE '%#$sessionUid#%') OR (whispering = '') OR (whispering IS NULL) OR (uid = $sessionUid))";
			$cP = mysql_fetch_row( mysql_query ( $sql ) );
			$aC = $cP[0];
			if ($aC > 0) {
				$where = "id";
				$vT = visitedGetId($id, $sid); //kdy zde byl uzivatel naposledy
				if ($vT == 0) {
					$vT = visitedGetTime($id, $sid);
					$where = "time";
				}
			}
		}
		else {
			$sql = "SELECT count(*) FROM 3_comm_4 WHERE aid = $id AND ((whispering LIKE '%#$sessionUid#%') OR (whispering = '') OR (whispering IS NULL) OR (uid = $sessionUid))";
			$cP = mysql_fetch_row( mysql_query ( $sql ) );
			$aC = $cP[0];
			if ($aC > 0) {
				$vT = visitedGetId($id, $sid); //kdy zde byl uzivatel naposledy
				$where = "id";
				if ($vT == 0) {
					$vT = visitedGetTime($id, $sid);
					$where = "time";
				}
			}
		}
	}
	else {
		$sql = "SELECT count(*) FROM 3_comm_4 WHERE aid = $id AND ((whispering = '') OR (whispering IS NULL))";
		$cP = mysql_fetch_row( mysql_query ( $sql ) );
		$aC = $cP[0];
		if ($aC > 0) {
			$vT = visitedGetId($id, $sid); //kdy zde byl uzivatel naposledy
			$where = "id";
			if ($vT == 0) {
				$vT = visitedGetTime($id, $sid);
				$where = "time";
			}
		}
	}
}
else {
	$sql = "SELECT count(*) FROM 3_comm_$sid WHERE aid = $id";
	$cP = mysql_fetch_row( mysql_query ( $sql ) );
	$aC = $cP[0];
	if ($aC > 0) {
		$vT = visitedGetId($id, $sid); //kdy zde byl uzivatel naposledy
		$where = "id";
		if ($vT == 0) {
			$vT = visitedGetTime($id, $sid);
			$where = "time";
		}
	}
}

echoNonAjaxed("<div id=\"dis-module-x\">\n");

if ($aC > 0){

	if (!isset($_GET['index'])){
		 $index = 1;
	}else{
		 $index = (int)($_GET['index']);
	}
	if ($index < 1) {
		$index = 1;
	}

	$from = ($index - 1) * $commPC; //od kolikate polozky zobrazit
	$viewOnPage = $commPC;

	$countUnr = 0;
	//pokud je hodne neprectenych, meni se limit
	if ($LogedIn && $aC > 20){
		if ($sid == 4) {
			$b = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_comm_4 WHERE aid = $id AND $where > $vT AND ((whispering LIKE '%#$sessionUid#%') OR (whispering = '') OR (whispering IS NULL) OR uid = $sessionUid)"));
		}
		else {
			$b = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_comm_$sid WHERE aid = $id AND $where > $vT"));
		}
		if ($b[0] > 20 && $b[0] < 101){
			$viewOnPage = $commPC;
			$countUnr = $b[0];
			$commPC = $b[0]+10;
		}elseif($b[0] > 100){
			$viewOnPage = $commPC;
			$commPC = $countUnr = 100;
		}
		else $countUnr = $b[0];
	}
	else if ($LogedIn) {
		$b = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_comm_$sid WHERE aid = $id AND $where > $vT"));
		$countUnr = $b[0];
	}

	if (!$ajaxed) {
		$pagination = "<p class='strankovani'>".make_pages($aC, $viewOnPage, $index, $viewOnPage)."</p>\n";
		echoNonAjaxed($pagination);
		$ajaxingSql = "";
		$ajaxingSqlNoC = "";
	}
	else {
		$ajaxingSql = " AND c.$where > $vT ";
		$ajaxingSqlNoC = " AND $where > $vT ";
	}


if ($sid == 4) {
	if ($LogedIn == true) {
		$cKomuS = mysql_query("SELECT DISTINCT(whispering) FROM 3_comm_4 WHERE aid = $id AND whispering IS NOT NULL LIMIT 100");

		if (mysql_num_rows($cKomuS) > 0) {
			$KomuSeptaniS = array();
			$KomuSeptani = array();
			$a = $cc = 0;
			while ($cKomu = mysql_fetch_object($cKomuS)) {
				$whispersA = explode("#",substr($cKomu->whispering,1,-1));
				for ($a=0,$cc=count($whispersA);$a<$cc;$a++) {
					$KomuSeptani[$whispersA[$a]] = $whispersA[$a];
				}
			}
			mysql_free_result($cKomuS);
			$KomuSeptani = array_keys($KomuSeptani);
			$KomuSeptaniIDs = join(",", $KomuSeptani);
			$komuSeptalS = mysql_query ("SELECT id, login, login_rew FROM 3_users WHERE id IN ($KomuSeptaniIDs) ORDER BY login_rew ASC");
			if (mysql_num_rows($komuSeptalS)>0) {
				while ($komuSeptal = mysql_fetch_object($komuSeptalS)) {
					$KomuSeptani[$komuSeptal->id] = $komuSeptal->login;
				}
			}
			else {
				$KomuSeptani = ""; $KomuSeptani = array();
			}
			mysql_free_result($komuSeptalS);
		}

		if ($splitMetaFromText) {
			$sqlX = "SELECT c.id, c.uid, c.time, c.whispering, ct.text_content AS text, ct.text_whisText AS whisText, 0 AS compressed, u.login, u.login_rew, u.level FROM 3_comm_4 AS c LEFT JOIN 3_users AS u ON u.id = c.uid LEFT JOIN 3_comm_4_texts AS ct ON ct.text_id = c.mid WHERE c.aid = $id AND ((c.whispering LIKE '%#$sessionUid#%') OR (c.whispering = '') OR (c.whispering IS NULL) OR (c.uid = $sessionUid)) $ajaxingSql ORDER BY c.id DESC LIMIT $from, $commPC";
		}
		else {
			$sqlX = "SELECT c.id,c.uid,c.text,c.time,c.whispering,c.whisText,0 AS compressed, u.login, u.login_rew, u.level FROM 3_comm_4 AS c LEFT JOIN 3_users AS u ON u.id = c.uid WHERE c.aid = $id AND ((c.whispering LIKE '%#$sessionUid#%') OR (c.whispering = '') OR (c.whispering IS NULL) OR (c.uid = $sessionUid)) $ajaxingSql ORDER BY c.id DESC LIMIT $from, $commPC";
		}
		$sel_com = mysql_query($sqlX);
	}
	else {
		if ($splitMetaFromText) {
			$sqlX = "SELECT c.id, c.uid, c.time, c.whispering, ct.text_content AS text, NULL AS whisText, 0 AS compressed, u.login, u.login_rew, u.level FROM 3_comm_4 AS c LEFT JOIN 3_users AS u ON u.id = c.uid LEFT JOIN 3_comm_4_texts AS ct ON ct.text_id = c.mid WHERE c.aid = $id AND ((c.whispering = '') OR (c.whispering IS NULL)) $ajaxingSql ORDER BY c.id DESC LIMIT $from, $commPC";
		}
		else {
			$sqlX = "SELECT c.id,c.uid,c.text,c.time,0 AS compressed, u.login, u.login_rew, u.level FROM 3_comm_$sid AS c LEFT JOIN 3_users AS u ON u.id = c.uid WHERE c.aid = $id AND ((c.whispering = '') OR (c.whispering IS NULL)) $ajaxingSql ORDER BY c.id DESC LIMIT $from, $commPC";
		}
		$sel_com = mysql_query($sqlX);
	}
}
else {
	if ($splitMetaFromText) {
		$sqlX = "SELECT c.id, c.uid, c.time, ct.text_content AS text, 0 AS compressed, u.login, u.login_rew, u.ico, u.level, u.signature FROM 3_comm_".$sid." AS c LEFT JOIN 3_users AS u ON u.id = c.uid LEFT JOIN 3_comm_".$sid."_texts AS ct ON ct.text_id = c.mid WHERE c.aid = $id $ajaxingSql ORDER BY c.id DESC LIMIT $from, $commPC";
	}
	else {
		$sqlX = "SELECT c.id,c.uid,c.text,c.time, 0 AS compressed, u.login, u.login_rew, u.ico, u.level, u.signature FROM 3_comm_$sid AS c LEFT JOIN 3_users AS u ON u.id = c.uid WHERE c.aid = $id $ajaxingSql ORDER BY c.id DESC LIMIT $from, $commPC";
	}
	$sel_com = mysql_query($sqlX);
}

if ($LogedIn && $_SESSION['uid'] == 2) {
	// echo "<!-- $sqlX -->";
}

$cIds = $cTms = array(0);
$i = 0;

if ($countUnr == 0) echoNonAjaxed("\n	<a name='kom' id='kom'></a>\n\n");

$charCnt = 0;

if ($forumUpAddon) {
	inf($forumUpAddon);
}

while($pT = mysql_fetch_object($sel_com)){
	$i++;
	if ($i == $countUnr) {
		echoNonAjaxed("\n	<a name='kom' id='kom'></a>\n\n");
	}
	$lastUnr = "";

	$cIds[] = $pT->id;
//	if ($pT->compressed) $pT->text = gzuncompress($pT->text);
	$cTms[] = $pT->time;

	$cTime = sdh($pT->time);

	//podpis
	if ($sid != 4 && strlen($pT->signature) > 0){
		$sig = "<br /><span class='signature'>".stripslashes($pT->signature)."</span>";
	}else{
		$sig = "";
	}

	if ($triSta) {
		$t = strtr(strip_tags(spit($pT->text, 1)), array("&lt;" => "<", "&gt;" => ">", "&quot;" => "\"", "&#039;" => "'", "&amp;" => "&"));
		$d = mb_strlen($t, "UTF-8");
		$sig = "<br /><br /><small>(".$d." znak".($d > 4 ? 'ů' : ($d > 1 ? 'y': '')).")</small>".$sig;
	}

	$chc = "";
	if ($sid == 3) {
		if (!$LogedIn || $dItem->closed == "1") {
		}
		elseif ($AllowedTo == "superall" || $AllowedTo == "all") {
			$chc = " <input type='checkbox' value='".base_convert($pT->id,10,35)."' /> ";
		}
		elseif ($LogedIn && $_SESSION['uid'] == $pT->uid && $AllowedTo != "read" && $AllowedTo != "hide") {
			$chc = " <input type='checkbox' value='".base_convert($pT->id,10,35)."' /> ";
		}
	}
	elseif ($sid == 4) {
		if ($allow == "pj" || $allowsPJ['prispevky']) {
			$chc = " <input type='checkbox' value='".base_convert($pT->id,10,35)."' /> ";
		}
		elseif ($allow == "no") {
			$chc = "";
		}
		elseif ($_SESSION['uid'] == $pT->uid) {
			$chc = " <input type='checkbox' value='".base_convert($pT->id,10,35)."' /> ";
		}
	}
	elseif ($hasAdminRight || ($AllowedTo > 0 && $_SESSION['uid'] == $pT->uid)) {
			$chc = " <input type='checkbox' value='".base_convert($pT->id,10,35)."' /> ";
	}

	$rea = "";
	if ($LogedIn == true) {

		$schovavacka = "<em class=\"ar\" onclick=\"hide('h_".base_convert($pT->id,10,35)."');\" title='Schovat'></em>";

		if ($sid == 4) {
			if ($allow == "pj" || $allow == "hrac" || $allowsPJ['prispevky']) {
				if ($pT->uid == $hItem->uid || ($hItem->PJs && isset($hItem->PJs[$pT->uid]))) {
					$rea = " - <span><a class=\"rl\" href=\"#k\" onclick=\"react('Pán Jeskyně $cTime');return false;\">RE</a></span> ";
				}
				elseif (isset($uzivatele[$pT->uid]) && isset($uzivatele[$pT->uid]['postava'])) {
					$rea = " - <span><a class=\"rl\" href=\"#k\" onclick=\"react('".addslashes($uzivatele[$pT->uid]['postava'])." $cTime');return false;\">RE</a></span> ";
				}
				else {
					$rea = " - <span><a class=\"rl\" href=\"#k\" onclick=\"react('"._htmlspec($pT->login)." $cTime');return false;\">RE</a></span> ";
				}
			}
			if ($pT->$where > $vT && $sessionUid != $pT->uid){
				$unR = " unr";
			}else{
				$unR = "";
			}
		}
		else {
			if ($AllowedTo != '0' && $AllowedTo != "nothing" && $AllowedTo != "read") {
				$rea = " - <span><a class=\"rl\" href=\"#k\" onclick=\"react('$pT->login $cTime');return false;\">RE</a></span> ";
			}
			//unr
			if ($pT->$where > $vT && $_SESSION['uid'] != $pT->uid){
				$unR = " unr";
			}else{
				$unR = "";
			}
		}
	}else{
		$schovavacka = "";
		$rea = "";
		$unR = "";
	}

	if ($sid == 4) {
		$cN = "<span".sl($pT->level, 2)."><a href='/uzivatele/$pT->login_rew/'>$pT->login</a></span>";
		if ($pT->whispering != "" && $LogedIn) {
			if ($pT->whisText != "") {
				$septanda = "		<tr><td class='c1 cspt' colspan='2'><h4><span class='hspk'>&rArr;</span> [ ".stripslashes($pT->whisText)." ]</h4></td></tr>";
			}
			else {
				$septanda = "		<tr><td class='c1 cspt' colspan='2'><h4><span class='hspk'>&rArr;</span> [ ".preloz_ids($KomuSeptani, $pT->whispering)." ]</h4></td></tr>";
			}
		}
		else {
			$septanda = "";
		}

		$icoHere = "http://s1.aragorn.cz/i/default.jpg";

		if (isset($uzivatele[$pT->uid]['ico'])) {
			if ($uzivatele[$pT->uid]['ico'] != "") {
				$icoHere = "http://s1.aragorn.cz/i/".$uzivatele[$pT->uid]['ico'];
			}
		}

		mb_internal_encoding("UTF-8");

		if ($pT->uid == 0 && $pT->whispering != "" && $LogedIn) {
			$kostky = explode($hCh, $pT->text);
			if (count($kostky) > 1) {
				$rea = "";
				$icoHere = "";
				if ($_SESSION['uid'] != $hItem->uid && !$allowsPJ['prispevky']) { $chc = ""; }
				else {
					$chc = " <input type='checkbox' value='".base_convert($pT->id, 10, 35)."' /> ";
				}
				if ($pT->whisText != "") {
					$Kdo = $pT->whisText;
				}
				else {
					$Kdo = preloz_ids($KomuSeptani,$pT->whispering);
				}
				if (count(explode("#", substr($pT->whispering, 1, -1)))>1) {
					$Hod = mb_strpos($Kdo, ",");
				}
				else {
					$Hod = mb_strlen($Kdo);
				}
				$Hod = mb_substr($Kdo,0,$Hod);
				if ($Hod == $_SESSION['login']) {
					$Hod = "Hodil(a) jsi";
				} else {
					$Hod = "<strong>$Hod</strong> hodil(a)";
				}
				switch ($kostky[0]) {
					case "k6":
						$pT->text = "$Hod na šestistěnné kostce (k6) hodnotu <strong>$kostky[1]</strong>.";
					break;
					case "k10":
						$pT->text = "$Hod na desetistěnné kostce (k10) hodnotu <strong>$kostky[1]</strong>.";
					break;
					case "4k6":
						$pT->text = "$Hod v hodu 4k6 (Fate) hodnoty <strong>$kostky[1]</strong>.";
					break;
					case "k20":
						$pT->text = "$Hod na dvacetistěnné kostce (k20) hodnotu <strong>$kostky[1]</strong>.";
					break;
					case "k%":
						$pT->text = "$Hod na procentuální kostce (k%) hodnotu <strong>$kostky[1] %</strong>.";
					break;
					case "kP":
						if (count($kostky)==4) {
							$pT->text = $Hod . " při hodu <strong>2k6+</strong> hodnoty $kostky[2] + $kostky[3] =&nbsp;<strong>$kostky[1]</strong>.";
						}
						elseif (count($kostky)>2) {
							$pT->text = $Hod . " při hodu <strong>2k6+</strong> hodnoty $kostky[2] + $kostky[3] a pak $kostky[4] =&nbsp;<strong>$kostky[1]</strong>.";
						}
					break;
					case "kX":
						if ($kostky[1] > 1) {
							$pT->text = $Hod . " při <strong>$kostky[1] hodech</strong> na rozsahu <strong>1&hellip;$kostky[2]</strong> hodnoty <strong>$kostky[3]</strong>.";
						}
						else {
							$pT->text = $Hod . " na rozsahu <strong>1&hellip;$kostky[2]</strong> hodnotu <strong>$kostky[3]</strong>.";
						}
					break;
				}
			}
			$cN = "<span><a href='#h_".base_convert($pT->id,10,35)."'>Systém</a></span>";
			$profileIco = " ";
			$sig = "";
		}
		else {
			$profileIco = "<a href='/uzivatele/$pT->login_rew/'><img src='$icoHere' alt='$pT->login' /></a>";
			if (isset($uzivatele[$pT->uid])) {
				$cN = "<span".sl($pT->level, 2)."><a href='/herna/$slink/";
				if ($pT->uid != $hItem->uid && isset($uzivatele[$pT->uid]['postava'])) {
					$profileIco = "<a href='/herna/$slink/".$uzivatele[$pT->uid]['postava_rew']."/' title='".$uzivatele[$pT->uid]['postava']."'><img src='$icoHere' alt='".$uzivatele[$pT->uid]['postava']."' /></a>";
					$cN .= $uzivatele[$pT->uid]['postava_rew']."/' alt='".$uzivatele[$pT->uid]['postava']."' title='".$uzivatele[$pT->uid]['postava']."'>".$uzivatele[$pT->uid]['postava']."</a></span>";
				}
				elseif ($pT->uid == $hItem->uid) {
					$profileIco = "<a href='/herna/$slink/' title='$pT->login (PJ)'><img src='$icoHere' alt='Pán jeskyně $pT->login' /></a>";
					$cN .= "' title='PJ'>Pán Jeskyně</a></span>";
				}
				elseif ($hItem->PJs && isset($hItem->PJs[$pT->uid])) {
					$profileIco = "<a href='/herna/$slink/' title='$pT->login (Pomocný PJ)'><img src='$icoHere' alt='Pomocný Pán jeskyně $pT->login' /></a>";
					$cN .= "' title='PJ'>Pomocný PJ $pT->login</a></span>";
				}
			}
		}
	echoNonAjaxed("\n<div>");
	echoAjaxed("<table class='commtb' cellspacing='0' cellpadding='0'>\n		<tr><td class='c1' colspan='2' >".$cN.aprilovyZertik($lastRandomNumber)." - ".$cTime.$rea.$chc.$schovavacka."</td></tr>".$septanda."\n		<tr id='h_".base_convert($pT->id,10,35)."'>\n		<td class='c2'>$profileIco</td>\n		<td class='c3'>\n			<p class='c4$unR'>".spit($pT->text, 1)."\n$sig</p>\n		</td>\n		</tr>\n	</table>");
	echoNonAjaxed("</div>\n");
	}
	else {
		if (strlen($pT->ico) < 3) {
			$pT->ico = "default.jpg";
		}
		$cN = "<span".sl($pT->level, 2)."><a href='/uzivatele/$pT->login_rew/' title='$pT->login'>$pT->login</a></span>";
		echoNonAjaxed("\n<div>");
		echoAjaxed("<table class='commtb' cellspacing='0' cellpadding='0'>\n		<tr><td class='c1' colspan='2' >".$cN.aprilovyZertik($lastRandomNumber)." - ".$cTime.$rea.$chc.$schovavacka."</td></tr>\n		<tr id='h_".base_convert($pT->id,10,35)."'>\n		<td class='c2'><a href='/uzivatele/$pT->login_rew/' title='$pT->login'><img src='http://s1.aragorn.cz/i/$pT->ico' alt='$pT->login' /></a></td>\n		<td class='c3'>\n			<p class='c4".$unR."'>".spit(vypatlavac($pT->text), 1)."\n".$sig."</p>\n		</td>\n		</tr>\n	</table>");
		echoNonAjaxed("</div>\n");

	}
	$charCnt += strlen($pT->text);
	if ($charCnt > 4086) {
		ob_flush();
		$charCnt = 0;
	}
	unset($cN,$rea,$chc);
}	// END WHILE
	echoNonAjaxed("\n<a name=\"kom2\" id=\"kom2\"></a>\n");
	echoNonAjaxed($pagination);
}
else {
	if ($sid == 4) {
		inf("V jeskyni nejsou žádné veřejně přístupné příspěvky.");
	}
	elseif ($sid == 3) {
		inf("V diskuzi zatím není ani jeden příspěvek.");
	}
	else {
		inf("Zatím zde není žádný komentář.");
	}
}

if ($LogedIn) {
	$max_cIds = (count($cIds) > 1 ? max($cIds) : join("", $cIds));
	$max_cTms = (count($cTms) > 1 ? max($cTms) : join("", $cTms));

	//indikace navstivene diskuze, pripadne navstiveni
	if ($index > 1) {
		if ($sid < 3) {
			visitedVerify($id, $sid, $time, $vT, $where);
		}
		elseif (($sid == 4 && $sslink == "") || ($sid == 3 && $sslink == "")) {
			visitedVerify($id, $sid, $time, $vT, $where);
		}
	}
	else {
		if ($sid < 3) {
			visitedVerify($id, $sid, $time, $max_cIds, $where);
		}
		elseif (($sid == 4 && $sslink == "") || ($sid == 3 && $sslink == "")) {
			visitedVerify($id, $sid, $time, $max_cIds, $where);
		}
	}

if (!$ajaxed) {
?>
<script charset="utf-8" type="text/javascript">
/* <![CDATA[ */
var urlPartDiskuze = '<?php echo "/".$link."/".$slink;?>/<?php echo $index > 1?"?index=$index":"";?>';
var theSender = null;
/* ]]> */
</script>
<?php
}
else echo $ajaxTxt;
?>
<?php } ?></div>