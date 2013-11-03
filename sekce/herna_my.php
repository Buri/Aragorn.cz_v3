<h2 class="h2-head"><a href="/herna/" title="<?php echo $titleHerna;?>"><?php echo $titleHerna;?></a></h2><h3><a href='/herna/my/' title='Mé jeskyně a postavy'>Moje hry</a></h3>
<p class='submenu'><a href='/herna/' class='permalink' title='Zpět na výpis jeskyní'>Zpět do herny</a></p>
<div class='highlight-top'></div>
<div class='highlight-mid'>
<?php
if (!$LogedIn) {
	info("Tato sekce je přístupná jen registrovaným a přihlášeným uživatelům.");
}
else {

	if ($_GET['ok']>0) {
		switch ($_GET['ok']) {
			case "1":
				ok("Nová jeskyně byla úspěšně odeslána ke schválení.");
			break;
			case "2":
				ok("Nová postava byla úspěšně odeslána ke schválení.");
			break;
			case "3":
				ok("Postava byla úspěšně smazána.");
			break;
			case "4":
				ok("Předání vlastnictví jeskyně proběhlo vpořádku.");
			break;
			case "5":
				ok("Jeskyně vpořádku smazána.");
			break;
		}
	}

	echo "	<table class='diskuze-one'>\n";
	$myGM1s = mysql_query("SELECT h.*,j.nazev AS jeskyne,j.nazev_rew AS jeskyne_rew FROM 3_herna_postava_drd AS h, 3_herna_all AS j WHERE h.uid = $_SESSION[uid] AND h.cid = j.id ORDER BY h.jmeno_rew ASC");
	if (mysql_num_rows($myGM1s)>0) {
		echo "	<tr><td><h4>Postavy DrD</h4></td><td><ul class='ml20'>";
		while ($myGM1 = mysql_fetch_object($myGM1s)) {
			$schvalena = ($myGM1->schvaleno == "0") ? "(neschváleno)":"(schváleno)";
			echo "
		<li><a class='permalink2' href='/herna/$myGM1->jeskyne_rew/'>"._htmlspec($myGM1->jeskyne)."</a> | <a class='permalink2' href='/herna/$myGM1->jeskyne_rew/$myGM1->jmeno_rew/'>"._htmlspec($myGM1->jmeno)."</a> $schvalena</li>";
		}
		echo "</ul></td>
	</tr>\n";
	}
	else {
		echo "	<tr><td colspan='2'><h4>Žádné postavy v Systému DrD</h4></td></tr>\n";
	}
	$myGM2s = mysql_query("SELECT h.*,j.nazev AS jeskyne,j.nazev_rew AS jeskyne_rew FROM 3_herna_postava_orp AS h, 3_herna_all AS j WHERE h.uid = $_SESSION[uid] AND h.cid = j.id ORDER BY h.jmeno_rew ASC");
	if (mysql_num_rows($myGM2s)>0) {
		echo "	<tr><td><h4>Postavy ORP</h4></td><td><ul class='ml20'>";
		while ($myGM2 = mysql_fetch_object($myGM2s)) {
			$schvalena = ($myGM2->schvaleno == "0") ? "(neschváleno)":"(schváleno)";
			echo "
		<li><a class='permalink2' href='/herna/$myGM2->jeskyne_rew/'>"._htmlspec($myGM2->jeskyne)."</a> | <a class='permalink2' href='/herna/$myGM2->jeskyne_rew/$myGM2->jmeno_rew/'>"._htmlspec($myGM2->jmeno)."</a> $schvalena</li>";
		}
		echo "</ul></td>
	</tr>\n";
	}
	else {
		echo "	<tr><td colspan='2'><h4>Žádné postavy v Systému ORP</h4></td></tr>\n";
	}
	$myPJs  = mysql_query("SELECT * FROM 3_herna_all WHERE uid = $_SESSION[uid] ORDER BY typ ASC, nazev_rew ASC");
	if (mysql_num_rows($myPJs)>0) {
		echo "	<tr><td><h4>Jeskyně</h4></td><td><ul class='ml20'>";
		while ($myPJ = mysql_fetch_object($myPJs)) {
			$systemJ = ($myPJ->typ == 1)? "ORP":"DrD";
			$schvalena = ($myPJ->schvaleno == "1") ? "(schváleno)" : ($myPJ->schvaleno == "0" ? "(odesláno)" : "(vráceno)");
			echo "
		<li>$systemJ | <a class='permalink2' href='/herna/$myPJ->nazev_rew/'>"._htmlspec($myPJ->nazev)."</a> $schvalena</li>";
		}
		echo "</ul></td>
	</tr>\n";
	}
	else {
		echo "	<tr><td colspan='2'><h4>Žádné založené jeskyně</h4></td></tr>\n";
	}
	$myPPJs  = mysql_query("SELECT c.nazev, c.nazev_rew, p.schvaleno, c.typ FROM 3_herna_pj AS p, 3_herna_all AS c WHERE c.id = p.cid AND p.uid = '$_SESSION[uid]' ORDER BY p.schvaleno DESC, c.nazev_rew ASC");
	if (mysql_num_rows($myPPJs)>0) {
		echo "	<tr><td><h4>Pomocný PJ</h4></td><td><ul class='ml20'>";
		while ($myPPJ = mysql_fetch_object($myPPJs)) {
			$systemJ = ($myPPJ->typ == 1)? "ORP" : "DrD";
			$schvalena = ($myPPJ->schvaleno == "1") ? "(funkce PPJ povolena)" : "(funkce PPJ neschválena)";
			echo "
		<li>$systemJ | <a class='permalink2' href='/herna/$myPPJ->nazev_rew/'>"._htmlspec($myPPJ->nazev)."</a> $schvalena</li>";
		}
		echo "</ul></td>
	</tr>\n";
	}
	else {
		echo "	<tr><td colspan='2'><h4>Žádné hry, kde bys měl/a funkci Pomocného PJ</h4></td></tr>\n";
	}
	echo "</table>\n";
}
?>
</div>
<div class='highlight-bot'></div>
