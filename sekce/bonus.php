<h2 class='h2-head'><a href='/bonus/' title='Bonus Aragorn.cz'>Bonus Aragorn.cz</a></h2>
<h3><a href='/bonus/' title='Bonus Aragorn.cz'>Bonus Aragorn.cz</a></h3>
<div class='art'>
<?php
if ($_SESSION['lvl'] > 0) {

	if ($_SESSION['lvl'] == 2) {
?>
		<p>
			<strong>Tvůj <acronym title='Placená verze Aragornu (nadstandardní funkce, získání hvězdy)' xml:lang='cs'>bonus</acronym> na serveru <acronym title='Online herna RPG (Drd, Vampire a jiné)' xml:lang='cs'>Aragorn.cz</acronym> je aktivovaný.</strong>
		</p>
		<p>
			Pokud plánuješ svůj Bonus prodloužit ještě před jeho vypršením, můžeš tak samozřejmě učinit :-).
		</p>
		<p>
			Je třeba převést částku nejméně 50 Kč (150 Kč/6 měsíců, 300 Kč/rok, atd.) ve prospěch účtu <br /><strong><?php echo $cisloUctuAragornu;?> variabilní symbol <?php echo $_SESSION['uid'];?></strong>.
		</p>
		<p>
			Jakmile peníze dorazí na účet a všechny náležitosti budou zkontrolovány <a href='/administratori/'>administrátorem</a> přes Bonusy, bude Ti bonus prodloužen. 
		</p>
		<p>
			Vrátit se zpět do <a href='/nastaveni/systemove/' title='Systémové nastavení'>Systémového nastavení</a>
		</p>
<?php
	}
	elseif ($_SESSION['lvl'] == 3) {
?>
		<p>
			Nazdar <strong>admine...</strong>
		</p>
		<p>
			Potřebuješ něco?
		</p>
<?php
	}
	else {
?>
		<p>
			Tvá žádost o <acronym title='Placená verze Aragornu (nadstandardní funkce, získání hvězdy)' xml:lang='cs'>bonus</acronym> na serveru <acronym title='Online herna RPG (Drd, Vampire a jiné)' xml:lang='cs'>Aragorn.cz</acronym> byla zaznamenána do systému.<br />
	</p>
		<p>
			Je třeba převést částku nejméně 50 Kč (150 Kč/6 měsíců, 300 Kč/rok, atd.) ve prospěch účtu <br /><strong><?php echo $cisloUctuAragornu;?> variabilní symbol <?php echo $_SESSION['uid'];?></strong>.
		</p>
		<p>
			Pokud chceš, můžeš žádost o bonus v <a href='/nastaveni/systemove/'>Systémovém nastavení</a> zrušit.
		</p>
<?php
	}
}elseif ($_GET['bon'] > 0) {

if ($_GET['bon'] == 6){
  $cash = 150;
}else{
  $cash = 300;
}

?>
		<p>
			Tvá žádost o <acronym title='Placená verze Aragornu (nadstandardní funkce, získání hvězdy)' xml:lang='cs'>bonus</acronym> na serveru <acronym title='Online herna RPG (Drd, Vampire a jiné)' xml:lang='cs'>Aragorn.cz</acronym> byla přijata do systému.<br />
			Nyní je třeba převést částku <?php echo $cash;?> Kč ve prospěch účtu <br /><strong><?php echo $cisloUctuAragornu;?> variabilní symbol <?php echo $_SESSION['uid'];?></strong>. <br />Jakmile peníze dorazí na účet, bude Ti aktivována placená verze serveru a získáš následující... 
		</p>
		<p>
			+ Hvězda u jména<br />+ Neomezené zakládání jeskyní<br />+ Hra v libovolném množství jeskyní<br />+ Speciální filtrování Herny<br />+ Obnovování záložek a nepřečtené pošty<br />+ a jiné - více v <a href='/napoveda/#bonus'>Nápovědě</a>
		</p>
		<p>
			Pokud se jednalo o překlik, žádost o bonus je možné v Nastavení zrušit.
		</p>
		<p>
			Vrátit se zpět do <a href='/nastaveni/systemove/' title='Systémové nastavení'>Systémového nastavení</a>
		</p>
<?php
}elseif ($_GET['ok'] == 1) {
  echo "<p>
          Tvá žádost o <acronym title='Placená verze Aragornu (nadstandardní funkce, získání hvězdy)' xml:lang='cs'>bonus</acronym> byla <strong>stornována</strong>.
        </p>
        <p>
          Vrátit se zpět do <a href='/nastaveni/systemove/' title='Systémové nastavení'>Systémového nastavení</a>
        </p>";
}
else {
?>
	<p>
		<strong>Nemáš</strong> <acronym title='Placená verze Aragornu (nadstandardní funkce, získání hvězdy)' xml:lang='cs'>bonus</acronym> ani odeslanou <strong>žádost o bonus</strong>.
	</p>
	<p>Chceš získat nadstandardní výhody?</p>
	<p>
		+ Hvězda u jména<br />+ Neomezené zakládání jeskyní<br />+ Hra v libovolném množství jeskyní<br />+ Obnovování záložek a nepřečtené pošty<br />+ a jiné - více v <a href='/napoveda/#bonus'>Nápovědě</a>
	</p>
	<p>
		<strong>Jdeš do toho?</strong><br />
		Pokud ano, můžeš žádost o bonus v <a href='/nastaveni/systemove/'>Systémovém nastavení</a> odeslat a pokračovat dle dalších instrukcí.
	</p>
<?php
}
?>
</div>
