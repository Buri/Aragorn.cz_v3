<h2 class='h2-head'><a href='/' title='Aragorn.cz'><?php echo ($itIsApril ? "Wo co go???" : "Úvodníky");?></a></h2>

<?php
$randIMG = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_galerie WHERE schvaleno = '1'"));
$randIMG = mt_rand(0,$randIMG[0]-1);
$randIMG = mysql_fetch_row(mysql_query("SELECT nazev,nazev_rew,thumb FROM 3_galerie WHERE schvaleno = '1' LIMIT $randIMG,1"));

$randPopis = _htmlspec($randIMG[0]);

$w = $AragornCache->getVal('sizes:galerie/'.$randIMG[2]);
if ($w === false) {
	$w = @getimagesize('./galerie/'.$randIMG[2]);
	if ($w) {
		$w = " ".$w[3]." ";
	}
	else {
		$w = " ";
	}
	$AragornCache->setVal('sizes:galerie/'.$randIMG[2], $w, 3600);
}


$randimg_a = "<a href=\"/galerie/$randIMG[1]/\" title=\"$randPopis\"><img src=\"http://s1.aragorn.cz/gg/$randIMG[2]\" $w alt=\"$randPopis\" /></a>";

$newCaveS = mysql_query("SELECT nazev,nazev_rew,popis FROM 3_herna_all WHERE schvaleno = '1' ORDER BY zalozeno DESC LIMIT 4");
$caves = array();
while ($newCave = mysql_fetch_row($newCaveS)) {
	$newNazev = _htmlspec(mb_strimwidth($newCave[0], 0, 20, "..."));
	$newNazevL= _htmlspec($newCave[0]);
	$newPopis = _htmlspec(mb_strimwidth($newCave[2], 0, 25, "..."));
	$caves[] = "\t\t\t\t<p><a href=\"/herna/$newCave[1]/\" title=\"$newNazevL\">$newNazev</a><br />$newPopis</p>\n";
}
$caves = join("",$caves);

$newClankyS = mysql_query("SELECT nazev,nazev_rew,anotace FROM 3_clanky WHERE schvaleno = '1' ORDER BY schvalenotime DESC LIMIT 4");
$clanky = array();
while ($newCl = mysql_fetch_row($newClankyS)) {
	$newNazev = _htmlspec(mb_strimwidth($newCl[0], 0, 20, "..."));
	$newNazevL= _htmlspec($newCl[0]);
	$newPopis = _htmlspec(mb_strimwidth($newCl[2], 0, 25, "..."));
	$clanky[] = "\t\t\t\t<p><a href=\"/clanky/$newCl[1]/\" title=\"$newNazevL\">$newNazev</a><br />$newPopis</p>\n";
}
$clanky = join("",$clanky);

if (!isSet($_GET['index'])) $index = 1;
else $index = (int)($_GET['index']);

if ($index<1) $index = 1;

$from = ($index - 1) * $adminBlogPC; //od kolikate polozky zobrazit

$aS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_blog WHERE vydano = 1"));
$aC = $aS[0];

$sel_admin = mysql_query ("SELECT c.*, u.login, u.ico, u.login_rew FROM 3_admin_blog AS c, 3_users AS u WHERE c.uid = u.id AND c.vydano = 1 ORDER BY c.time DESC LIMIT $from, $adminBlogPC");
$tUvodnik = "<a name='kom' id='kom'></a>";
$aUvodnik = array();

if ($aC > 0){
	$cc = 1;
	while ($oAdmin = mysql_fetch_object($sel_admin)){
		$anchor = "";
		$anchor = "<a id='kom-$cc' name='kom-$cc'></a>";
		$aUvodnik[] = _htmlspec(stripslashes($oAdmin->headline));
		$tUvodnik .= "$anchor<table class='adminTable'><tbody>\n";
		$w = $AragornCache->getVal('sizes:system/icos/'.$oAdmin->ico);
		if ($w === false) {
			$w = @getimagesize('./system/icos/'.$oAdmin->ico);
			if ($w) {
				$w = $w[3];
			}
			else {
				$w = " ";
			}
			$AragornCache->setVal('sizes:system/icos/'.$oAdmin->ico, $w, 3600);
		}

		$tUvodnik .= "<tr><td rowspan='3' class='c2'> <a href='/uzivatele/".$oAdmin->login_rew."/' title='Profil uživatele'><img src='http://s1.aragorn.cz/i/$oAdmin->ico' alt='Ikonka - $oAdmin->login' ".str_replace('"', "'", " ".$w)." title='Ikonka - $oAdmin->login' /></a> </td>\n<td class='adminHeadline'><span class='dblock'>"._htmlspec(stripslashes($oAdmin->headline))."</span></td></tr>";
		$tUvodnik .= "<tr><td class='adminInfo'>Autor : <a href='/uzivatele/".$oAdmin->login_rew."/' title='Profil uživatele'>$oAdmin->login</a> ".aprilovyZertik($lastRandomNumber)."</td></tr>";
		$tUvodnik .= "<tr><td class='adminInfo'>Datum : ".sdh($oAdmin->time)."</td></tr>";
		$tUvodnik .= "<tr><td colspan='2' class='adminSpot'>".spit($oAdmin->content, 1)."</td></tr>";
		$tUvodnik .= "</tbody></table>\n";
		$cc+=1;
	}
	$tUvodnik .= "<a name='kom2' id='kom2'></a>";

}else{
	$tUvodnik .= "<p class='text'>Žádný spot v databázi.</p>";
}

if (!$LogedIn) {
?>
<div class='uvodnik'>
<span class='uvodnik-img'></span>
	<p>Vítej na <strong>portále</strong> hráčů <a href="/napoveda/#rpg" title="Co je to hra na hrdiny?"><strong>her na hrdiny</strong></a>.</p>
	<p>Uživatelům nabízíme:</p>
<?php
/*
<p style="clear:none;float:right"><a href="http://conventicon.cz/?r=b_sqr_1" title="Conventicon - 28. až 30. srpna 2009" target="_blank"><img src="http://conventicon.cz/ex/b09-sqr-1.gif" alt="Conventicon 2009" border="0"></a></p>
*/
?>
	<ul>
		<li>Jednorázovou a snadnou hru v <a class="helper" href="/napoveda/#rozcesti" title="Jak začít hrát v Rozcestí?"><strong>Rozcestí</strong></a>.</li>
		<li>Dlouhodobější <a class="helper" href="/napoveda/#herna" title="Jak začít hrát v jeskyni?">hraní v jeskyních</a>, a to jak přímo podporovaný systém <acronym title="Dračí Doupě"><strong>DrD</strong></acronym>, tak <strong>jakýkoli jiný herní systém</strong> (pomocí <a class="helper" href="/napoveda/#orp" title="Co je to ORP?">ORP</a>).</li>
		<li>Možnost domluvit si <a href="http://www.aragorn.cz/diskuze/aragorn-srazy/" title="Diskuze o srazech">setkání</a>, společné hraní v <a href="/herna/" title="Výpis herny - jeskyně a veškerá dobrodružství. Najděte si to své."><strong>jeskyni</strong></a>, <a href="http://www.aragorn.cz/diskuze/?oblast=8" title="Živá akční hra na hrdiny ve stylu fantasy a dalších podob"><strong>LARPy</strong></a> anebo cokoli jiného s uživateli, které server již sdružuje. K dispozici jsou otevřené i uzamčené diskuze, interní pošta a v neposlední řadě instantní <acronym title="Hospoda u Edwina"><strong>chat</strong></acronym> (založený na technologii <a class="helper" href="http://cs.wikipedia.org/wiki/AJAX" target="_blank" title="Asynchronous Javascript And XML - vysvětlení pojmu AJAX na Wikipedii"><strong>AJAX</strong></a>).</li>
		<li>Sdílet svou tvorbu se zdejšími uživateli v <a href="/galerie/" title="Galerie - obrázky, malby, kresby, črty, komixy - od uživatelů Aragorn.cz"><strong>Galerii</strong></a> anebo v sekci <a href="/clanky/" title="Poezie i próza, úvahy, zamyšlení, záznamy skvělých her - originály od uživatelů Aragorn.cz"><strong>Články</strong></a> (povídky, básničky, teorie her na hrdiny, doplňky ke hrám a jiné - například <a href="http://www.aragorn.cz/fidlaci.html" title="Hra z dílny zdejších hráčů"><strong>Fidláci</strong></a>).</li>
		<li><a class="helper" href="/napoveda/#bonus" title="Výhody zakoupení bonusu">Bonusovou verzi</a> pro uživatele ochotné Aragorn.cz finančně podpořit.</li>
	</ul>
	<p>Aktivní účast vyžaduje <a href="/registrace/" title="Zaregistrujte se!">registraci</a>.</p>
	<p>Příjemnou zábavu přejí <a href="/administratori/" title="Administrátoři Aragorn.cz">administrátoři</a>.</p>

<?php
}
else {
	if ($uvodniky == true) {
		echo "<div class='uvodnik'>\n";
	}
	else {
		echo "	<div class='uvodnik'><span class='uvodnik-img'></span>\n\n";
?>
	<p>Vítej na <strong>portále</strong> hráčů <a href="/napoveda/#rpg" title="Co je to hra na hrdiny?"><strong>her na hrdiny</strong></a>.</p>
	<p><a href="/?nabizime" onclick="hide('nabizime');return false;">Co uživatelům nabízíme?</a></p>
<?php
/*
<p style="clear:none;float:right"><a href="http://conventicon.cz/?r=b_sqr_1" title="Conventicon - 28. až 30. srpna 2009" target="_blank"><img src="http://conventicon.cz/ex/b09-sqr-1.gif" alt="Conventicon 2009" border="0"></a></p>
*/
?>
	<ul id="nabizime"<?php if (!isset($_GET['nabizime'])) { echo ' class="hide"';}?>>
		<li>Jednorázovou a snadnou hru v <a class="helper" href="/napoveda/#rozcesti" title="Jak začít hrát v Rozcestí?"><strong>Rozcestí</strong></a>.</li>
		<li>Dlouhodobější <a class="helper" href="/napoveda/#herna" title="Jak začít hrát v jeskyni?">hraní v jeskyních</a>, a to jak přímo podporovaný systém <acronym title="Dračí Doupě"><strong>DrD</strong></acronym>, tak <strong>jakýkoli jiný herní systém</strong> (pomocí <a class="helper" href="/napoveda/#orp" title="Co je to ORP?">ORP</a>).</li>
		<li>Možnost domluvit si <a href="http://www.aragorn.cz/diskuze/aragorn-srazy/" title="Diskuze o srazech">setkání</a>, společné hraní v <a href="/herna/" title="Výpis herny - jeskyně a veškerá dobrodružství. Najděte si to své."><strong>jeskyni</strong></a>, <a href="http://www.aragorn.cz/diskuze/?oblast=8" title="Živá akční hra na hrdiny ve stylu fantasy a dalších podob"><strong>LARPy</strong></a> anebo cokoli jiného s uživateli, které server již sdružuje. K dispozici jsou otevřené i uzamčené diskuze, interní pošta a v neposlední řadě instantní <acronym title="Hospoda u Edwina"><strong>chat</strong></acronym> (založený na technologii <a class="helper" href="http://cs.wikipedia.org/wiki/AJAX" target="_blank" title="Asynchronous Javascript And XML - vysvětlení pojmu AJAX na Wikipedii"><strong>AJAX</strong></a>).</li>
		<li>Sdílet svou tvorbu se zdejšími uživateli v <a href="/galerie/" title="Galerie - obrázky, malby, kresby, črty, komixy - od uživatelů Aragorn.cz"><strong>Galerii</strong></a> anebo v sekci <a href="/clanky/" title="Poezie i próza, úvahy, zamyšlení, záznamy skvělých her - originály od uživatelů Aragorn.cz"><strong>Články</strong></a> (povídky, básničky, teorie her na hrdiny, doplňky ke hrám a jiné - například <a href="http://www.aragorn.cz/fidlaci.html" title="Hra z dílny zdejších hráčů"><strong>Fidláci</strong></a>).</li>
		<li><a class="helper" href="/napoveda/#bonus" title="Výhody zakoupení bonusu">Bonusovou verzi</a> pro uživatele ochotné Aragorn.cz finančně podpořit.</li>
	</ul>
<?php
echo "	<p>V <a href=\"/nastaveni/\" title=\"Uprav si Nastavení Aragornu\"> nastavení</a> nalezneš možnosti, jak Aragorn.cz přizpůsobit svým potřebám, a další <a href=\"/nastaveni/osobni/\" title=\"Upravit Osobní nastavení\">osobní preference</a>.</p>
	<p>Příjemnou zábavu přejí <a href=\"/administratori/\" title=\"Administrátoři Aragorn.cz\">administrátoři</a>.</p>
";
	}
}

if ($uvodniky == false) {
}
else {
	echo "<p class='text'><a href='/pokracovat/' title='Pokračovat dál na Aragorn.cz' class='permalink'>... pokračovat dál ... &raquo;</a></p>\n";
}
?>
</div>
<p class='strankovani clearer uvodni-strankovani'><?php $pagination = make_pages($aC, $adminBlogPC, $index); echo $pagination; ?></p>

<?php

echo $tUvodnik; // vypsani uvodniku

?>
<p class='strankovani'><?php echo $pagination; ?></p>

<div class="uvodnik-news">

<table cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td class="thn" colspan="3">Aktuality</td>
		</tr>
		<tr><td class="t-a-c" colspan="3">
			<a href="#kom-1" class="permalink" title="Aktuální úvodník"><?php echo $aUvodnik[0]; if (isset($aUvodnik[1])) { echo "</a><br /><a href='#kom-2' class='permalink' title='Aktuální 2.úvodník'>$aUvodnik[1]"; } if (isset($aUvodnik[2])) { echo "</a><br /><a href='#kom-3' class='permalink' title='Aktuální 3.úvodník'>$aUvodnik[2]"; }?></a></td></tr>
		<tr>
			<td class="thn">Náhodný <a href="/galerie/" title="Výpis miniatur z Galerie">obrázek</a></td><td class="thn">Nové <a href="/herna/" title="Výpis jeskyní v Herně">jeskyně</a></td><td class="thn">Nové <a href="/clanky/" title="Výpis příspěvků z Článků">články</a></td>
		</tr>
		<tr>
			<td class="randimg"><?php echo $randimg_a;?></td>
			<td>
<?php echo $caves;?>
			</td>
			<td>
<?php echo $clanky;?>
			</td>
		</tr>
	</tbody>
</table>
</div>

