<?php
$titleGalerie = $itIsApril ? 'Omalovánky' : 'Galerie';

if ($slink == "new" && !$LogedIn) {
	include("./sekce/zakaz2.php");
}
elseif ($slink == "" || $slink == "od") {

	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

	$txtInS = mysql_fetch_row(mysql_query("SELECT text FROM 3_notes WHERE uid=0"));
	$txtIn = $txtInS[0];

	$i = 0;
	$ukoncenRadek = true;
	$imgOnRow = 3;
	$kolik = $galeriePC;
	$od = ($index - 1) * $kolik; //od kolikate polozky zobrazit

	if ($slink == "od") {
		$slink = "od/$sslink";
		$shortTitle = "autor ~ $searchUser[1]";
	}
	else {
		$aZ = mysql_query("SELECT count(*) FROM 3_galerie WHERE schvaleno = '1'");
		$aS = mysql_fetch_row($aZ);
		$aC = $aS[0];

	}

?>
<h2 class='h2-head'><a href='/galerie/' title='<?php echo $titleGalerie;?>'><?php echo $titleGalerie;?></a></h2>
<h3><a href='<?php if ($slink){$add = "/";} echo "/$link/$slink$add"; ?>' title='<?php echo $shortTitle; ?>'><?php echo $shortTitle; ?></a></h3>
<?php
	echo "<p class='submenu'>";
	if ($LogedIn) {
  	$myPicAdd = "";
		$myPicturesCount = mysql_fetch_object(mysql_query("SELECT SUM(IF(schvaleno='0',1,0)) AS odeslano FROM 3_galerie WHERE autor = $_SESSION[uid] AND schvaleno != 1 GROUP BY schvaleno"));
		if ($myPicturesCount->odeslano>0) {
			$myPicAdd = " (<strong title='Počet obrázků odeslaných ke schválení' class='helper'>$myPicturesCount->odeslano</strong>)";
		}
  	echo "<a href='/galerie/new/' class='permalink' title='K odeslání obrázku do Galerie'>Nahrát vlastní obrázek</a><span class='hide'> | </span><a href='/galerie/my/' class='permalink' title='Moje obrázky'>Moje obrázky".$myPicAdd."</a><span class='hide'> | </span><a href=\"#\" onclick=\"hide('jsStats'); checkStats(); return false\" class='permalink' title='Statistiky'>Statistiky</a>";
		echo "<span class='hide'> | </span> ";
	}
	echo "<a href='/diskuze/galerie/' class='permalink' title='Diskuze k sekci'>Diskuze k sekci</a></p>\n";
	
	$statsL = ($LogedIn)? $_SESSION['uid'] : 0;

?>
<script type='text/javascript'>var init = 0;function checkStats(){if(init == 0){init = 1;makeStats(1, <?php echo $statsL;?>);}}</script>
<div id='jsStats' class='hide'></div>
<?php
  
  if ($searchUser === false && strlen($txtIn)>2) {
	  echo "<div class='art text'>$txtIn</div>\n";
	}

	$limiter = "LIMIT $od, $kolik";

	if ($searchUser !== false) {
		$limiter = "";
	}


	if ($LogedIn)
		$sql = "SELECT g.id, g.nazev, g.nazev_rew, g.source, g.thumb, g.hodnoceni, g.hodnotilo, u.level, u.login, u.login_rew,
		(SELECT COUNT(*) FROM 3_comm_2 AS c WHERE c.aid = g.id) AS all_comms,
		3_visited_2.news AS unread_comms,
		3_visited_2.uid AS v_uid
		FROM 3_galerie AS g
		LEFT JOIN 3_visited_2 ON 3_visited_2.aid = g.id AND 3_visited_2.uid = $_SESSION[uid]
		LEFT JOIN 3_users AS u ON u.id = g.autor
		WHERE g.schvaleno = 1 $autorSQL
    ORDER BY g.schvalenotime DESC $limiter";
		
	else $sql = "SELECT g.id, g.nazev, g.nazev_rew, g.source, g.thumb, g.hodnoceni, g.hodnotilo, 0 AS unread_comms, (SELECT COUNT(*) FROM 3_comm_2 AS c WHERE c.aid = g.id) AS all_comms, 0 AS v_uid, u.level, u.login, u.login_rew 
  FROM 3_galerie AS g, 3_users AS u	
	WHERE g.schvaleno = '1' AND 
  u.id = g.autor $autorSQL
  ORDER BY g.schvalenotime DESC $limiter";
	$gS = mysql_query($sql);

	if ($searchUser === false) {
		$galerieOd = "Všechna díla ~ ";
		$uzivateleLink = "galerie/od";
	
		echo "
	<p class='strankovani'>";
		$pagination = make_pages($aC, $kolik, $index);
		echo $pagination;
		echo "</p>\n";
	}
	else {
		$galerieOd = "Profil ~ ";
		$uzivateleLink = "uzivatele";
		$pagination = "";
	}

	if (mysql_num_rows($gS)>0) {
	echo "<table width='100%' class='galerie-vypis' cellspacing='0'><tbody>";
	
	while($gItem = mysql_fetch_object($gS)){
		if (($i+1) == $gC){
			$bD = " style='border-width: 0'";
		}else{
			$bD = "";
		}

		if ((($i % $imgOnRow) == 0) && $ukoncenRadek) {
			$ukoncenRadek = false;
			echo "<tr><td$bD>\n";
		}

		$gItem->nazev = mb_strtoupper(mb_substr($gItem->nazev, 0, 1)).mb_substr($gItem->nazev, 1);
		$vN = _htmlspec(mb_strimwidth($gItem->nazev, 0, 15, "...", "UTF-8"));

		echo "	<div><p class='g-p'><a href='/galerie/$gItem->nazev_rew/' title='$gItem->nazev' class='permalink2'>$vN</a><br /><a href='/$uzivateleLink/$gItem->login_rew/' title='$galerieOd"._htmlspec($gItem->login)."' class='permalink2'><span".sl($gItem->level, 1).">".$gItem->login."</span></a><span class='ge'>".rating($gItem->hodnoceni, $gItem->hodnotilo)."</span><span class='ge'><a href='/galerie/$gItem->nazev_rew/#kom' class='ge2' title='Přečíst komentáře'>Komentáře</a> : ".getComm($gItem->id, 2,true,$gItem->unread_comms,$gItem->all_comms,$gItem->v_uid)."</span></p><a href='/galerie/$gItem->source' rel='lightbox[galerie]' title='$gItem->nazev ~ "._htmlspec($gItem->login)."'><img src='http://s1.aragorn.cz/gg/$gItem->thumb' title='$gItem->nazev' alt='$gItem->nazev' /></a></div>\n";
		if (($i % $imgOnRow) == ($imgOnRow - 1) && !$ukoncenRadek) {
			$ukoncenRadek = true;
			echo "</td></tr>\n";
		}
		$i++;
	}

	if (!$ukoncenRadek) {
		$ukoncenRadek = true;
		echo "</td></tr>\n";
	}
	echo "</tbody></table>\n";
	}
	else {
		if ($searchUser !== false) {
			info("$searchUser[1] nemá v Galerii žádná zveřejněná díla.");
		}
	}
	if ($searchUser === false) {
?>
<p class='strankovani'><?php echo $pagination; ?></p>
<?php
	}
}
elseif ($slink == "new" && $LogedIn) {
?>
<h2 class='h2-head'><a href='/galerie/' title='<?php echo $titleGalerie;?>'><?php echo $titleGalerie;?></a></h2>
<h3><a href='<?php if ($slink){$add = "/";} echo "/$link/$slink$add"; ?>' title='<?php echo $shortTitle; ?>'><?php echo $shortTitle; ?></a></h3>
	<p class='submenu'><a href='/galerie/' class='permalink' title='Zpět na výpis miniatur galerie'>Zpět do Galerie</a></p>
<?php
	$txtInS = mysql_fetch_row(mysql_query("SELECT text FROM 3_notes WHERE uid=0"));
	$txtIn = $txtInS[0];
	if (strlen($txtIn)>2) {
	  echo "	<div class='art text'>$txtIn</div>\n";
	}
	$countSent = array_pop(mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM 3_galerie WHERE autor = '$_SESSION[uid]' AND schvaleno = '0'")));
?>
<?php
if ($error>0){
	switch ($error){
	case 1:
	  $error = "Již existuje obrázek s <acronym title='Přesněji jeho SEO verze již v Galerii existuje.' xml:lang='cs'>podobným názvem</acronym>. Doporučujeme ho nějak přejmenovat.";
	  info($error);$error=1;
	break;
	case 2:
	  $error = "Musíš vybrat obrázek a odeslat jej k&nbsp;nahrání na&nbsp;server. Tato akce může však chvíli trvat, záleží na&nbsp;rychlosti tvého připojení k internetu.";
	  info($error);$error=2;
	break;
	case 3:
	  $error = "Obrázek má nesprávný formát.<br /> Podporované formáty jsou : <strong>GIF</strong>, <strong>JPEG</strong>, <strong>PNG</strong>.";
	  info($error);$error=3;
	break;
	case 4:
	  $error = "Velikost obrázku je vyšší než 500kB.";
	  info($error);$error=4;
	break;
	case 5:
	  $error = "Minimální rozměry pro obrázek do Galerie jsou 150&times;150 px a maximální 1600&times;1600 px.";
	  info($error);$error=5;
	break;
	case 10:
		info("V Galerii můžeš mít nejvýše dvě neschválená díla.<br />Až je administrátor schválí, budeš moci odeslat další.");
	break;
}
}elseif ($ok>0){
	switch ($ok){
	case 1:
	  $ok = "Obrázek byl vpořádku nahrán a odeslán ke schválení.";
	  ok($ok);
	break;
	}
}
if ($countSent < 2) {
?>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/galerie/new/?akce=galerie-new' method='post' name='form_for_new' id='form_for_new' onsubmit="return checkForNew('galerie','nazev_img',['nazev','popis','sendfile'],false);" enctype='multipart/form-data' class='f'>
	<input type="hidden" name="MAX_FILE_SIZE" value="1024000" />
	<fieldset>
	<legend>Nahrání obrázku do Galerie</legend>
	<label><span>Název</span><input type='text' id='nazev_img' name='nazev' size='20' value='' maxlength='60' /></label>
	<label><span>Popis</span><input type='text' name='popis' size='20' value='' maxlength='400' /></label>
	<label><span>Obrázek</span><input type='file' name='sendfile' size='20' /></label>
		<div class="hvyber">
			<ul>
				<li><small>Poznámka: Velikost akceptovatelného obrázku je maximálně 500 kB = 0,5 MB, což samozřejmě neznamená, že musíte nahrávat obrázky o velikosti blízké této hranici, ba právě naopak.<br />Ve většině případů stačí umístit do Galerie o trochu nižší (vyšší komprese) kvalitu a do komentářů pod dílo pak uvést odkaz na originální rozlišení s nejvyšší kvalitou umístěné na nějakém free serveru pro nahrávání obrázků.</small></li>
			</ul>
		</div>
	<input class='button' type='submit' value='Odeslat a nahrát' />
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>
<?php
}
else {
	info("V Galerii můžeš mít nejvýše dvě neschválená díla.<br />Až je administrátor schválí, budeš moci odeslat další.");
}
}
elseif ($slink == "my") {
  echo "<h2 class='h2-head'><a href='/galerie/' title='$titleGalerie'>$titleGalerie</a></h2><h3><a href='/galerie/my/' title='Moje obrázky'>Moje obrázky</a></h3>";
	echo "	<p class='submenu'><a href='/galerie/' class='permalink' title='Zpět na výpis obrázků'>Zpět do Galerie</a></p>\n"; 
	if ($LogedIn) {?>
<div class='highlight-top'></div>
<div class='highlight-mid'>
<?php
  echo "	<table class='diskuze-one'>\n";
		$myS = mysql_query("SELECT * FROM 3_galerie WHERE autor = '$_SESSION[uid]' ORDER BY schvaleno ASC, nazev ASC");
		if (mysql_num_rows($myS)>0) {
			echo "	<tr><td><ul class='ml20'>";
			while ($myGM1 = mysql_fetch_object($myS)) {
				if ($myGM1->schvaleno == "0") {
					echo "\n\t<li>"._htmlspec($myGM1->nazev)." | (odeslán ke schválení)</li>";
				}	else {
					echo "\n\t<li><a class='permalink2' href='/galerie/$myGM1->nazev_rew/'>"._htmlspec($myGM1->nazev)."</a> | (schváleno)</li>";
				}
			}
  } else {
	   echo "	<tr><td colspan='2'>Žádné obrázky</td></tr>\n";
  }
  echo "</ul></td>
  </tr>\n";
  echo "</table>\n";

?>
</div>
<div class='highlight-bot'></div>
<?php
  } else {
    info("Tato sekce je vyhrazena jen registrovaným uživatelům.");
}

}
else {

$gS = mysql_query ("SELECT g.id, g.nazev, g.source, g.thumb, g.x, g.y, g.schvalenotime, g.popis, g.hodnoceni, g.hodnotilo, u.id AS oid, u.login AS vlastnik, u.login_rew AS vlastnik_rew
FROM 3_galerie AS g, 3_users AS u 
WHERE u.id = g.autor AND g.nazev_rew = '$slink' AND schvaleno = '1'");
$gC = mysql_num_rows($gS);

if ($gC < 1) {
  echo "<h2 class='h2-head'><a href='/galerie/' title='$titleGalerie'>$titleGalerie</a></h2>";
	echo "<h3>$shortTitle</h3>\n";
	info("Obrázek, který hledáte, bohužel nebyl nalezen.");
}
else {
$gItem = mysql_fetch_object($gS);
$autorId = $gItem->autor;
$gItem->nazev = _htmlspec(mb_strtoupper(mb_substr($gItem->nazev, 0, 1)).mb_substr($gItem->nazev, 1));

  //overeni, zda uzivatel hodnotil
	if ($LogedIn == true && $_SESSION['uid'] !== $autorId){
		$sR = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_rating WHERE uid = $_SESSION[uid] AND aid = $gItem->id AND sid = 2") );
		if ($sR[0] > 0){
			$vL = "";
			$vF = "";
		}else{
			$vL = "<a href=\"#\" onclick=\"hide('rate');return false;\" class='permalink' title='Ohodnotit obrázek'>Ohodnotit obrázek</a>";
			$vF = "";
		}
	}
	if ($LogedIn) {
		$hasAdminRight = 0;
		if ($_SESSION['lvl']>2) $hasAdminRight = get_admin_prava();
		if ($hasAdminRight) {
			$vL = "<a class='permalink' href='/galerie/$slink/ad/' title='Administrace Obrázku'>Administrace</a>".$vL;
		}
	}
  //kontrola zalozky
$sB = chBook();
  echo "<h2 class='h2-head'><a href='/galerie/' title='$titleGalerie'>$titleGalerie</a></h2>";
	echo "<h3><a href='$_SERVER[REQUEST_URI]' title='$gItem->nazev'>$gItem->nazev</a></h3>
	<p class='submenu'><a href='/galerie/' class='permalink' title='Na výpis miniatur galerie'>Zpět na výpis</a>$vL$sB";
	if ($LogedIn) echo "<span class='hide'> | </span><a class='permalink' href='/galerie/$slink/stats/' title='Jednoduché statistiky návštěvnosti obrázku'>Statistiky</a>";
	echo "</p>\n";
	

if ($cFound && $LogedIn) {

	if ($hasAdminRight && isset($sslink) && $sslink == "ad") {
		echo "<div>\n";
		echo "<p class='t-a-c'>Administrace obrázku $nazev</p>\n";
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
		$res = mysql_query("SELECT u.login, p.uid FROM 3_users AS u, 3_sekce_prava AS p WHERE p.uid = u.id AND p.sid = $sid AND p.aid = $id ORDER BY u.login_rew ASC");
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
		else echo "		<tr><td>Obrázek nikdo nenavštěvuje.</td></tr>\n";
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

if ($LogedIn){
?>

<div id='rate' class='hide'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/<?php echo $link."/".$slink."/"; ?>?akce=rating' method='post' class='f'>
<fieldset>
<legend>Ohodnotit obrázek <a href="#" onclick="hide('rate');return false;" class='permalink flink' title='Zavřít'>Zavřít</a></legend>
<label><span>Vaše hodnocení</span><select name='rating' style='width: 152px'><option value="x" selected="selected">- - - -</option><option value='0.2'>odpad, zahodit</option><option value='1.4'>stále nic moc</option><option value='2.6'>líbí se mi to</option><option value='3.8'>je to skutečně pěkný</option><option value='5.0'>božská dokonalost</option></select></label>
<input class='button' type='submit' value='Ohodnotit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>

<?php
}

$gItem->vlastnik = _htmlspec($gItem->vlastnik);
$gItem->popis = _htmlspec($gItem->popis);

echo "
  <div class='galerie-img'>
		<ul>
		<li><a href='/galerie/$gItem->source' rel='lightbox' title='".$gItem->nazev." ~ "._htmlspec($gItem->vlastnik)."'><img src='http://s1.aragorn.cz/gg/$gItem->thumb' title='$gItem->nazev - miniatura' alt='$gItem->nazev - obrázek' /></a></li>
		<li>Autor: <a href='/uzivatele/".$gItem->vlastnik_rew."/' class='permalink2' title='$gItem->vlastnik'><span".sl($gItem->level, 1).">".$gItem->vlastnik."</span></a></li>
		<li>".rating($gItem->hodnoceni, $gItem->hodnotilo)."</li>
    <li>Publikováno: ".sd($gItem->schvalenotime)."</li>
    <li>Rozměry originálu: [$gItem->x px &#215; $gItem->y px]</li>
		<li>Popis: <p>$gItem->popis</p></li>
		</ul>
	</div>
";

	$sqlH = "SELECT u.login,r.rate FROM 3_rating AS r LEFT JOIN 3_users AS u ON u.id = r.uid WHERE r.sid = '2' AND r.aid = '$gItem->id' ORDER BY 1 ASC";
	$hodnotiloS = mysql_query($sqlH);
	if ($hodnotiloS && mysql_num_rows($hodnotiloS)>0) {
		echo "<div class='cl'>\n";
		echo "<p class='text'><a rel='nofollow' href='#' onclick='hide(\"hodnoceni-$slink\");return false;'>Kdo hodnotil obrázek $gItem->nazev?</a><br />\n";
		echo "<span id='hodnoceni-$slink' class='hide'>\n";
		$hodnotici = array();
		while ($osoba = mysql_fetch_row($hodnotiloS)){
			if ($osoba[1] > 0) $osoba[0] .= " (".$osoba[1]."*)";
			$hodnotici[] = $osoba[0];
		}
		echo join(", ",$hodnotici);
		echo "</span></p>\n</div>\n";
	}

  //modul pro diskuzi
  $id = $gItem->id;
  $sid = 2;
	$AllowedTo = get_prava_sekce($sid,$id);
  include "./add/dis.php";
}

}
?>
