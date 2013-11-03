<?php
function input($name,$label,$value="",$len = 35) {
	echo "	<label for='$name'><span>$label</span><input id='$name' name='$name' type='text' maxlength='$len' value='"._htmlspec($value)."' /></label>\n";
}
function input_checkbox($name,$value="",$checked = false) {
	$checked = $checked ? " checked='checked'" : "";
	return "<input name='$name' type='checkbox' class='checkbox' value='"._htmlspec($value)."'$checked />";
}
function textarea($name,$label,$value="",$len = 4) {
	echo "	<label for='$name'><span>$label</span><textarea id='$name' name='$name' rows='$len'>"._htmlspec($value)."</textarea></label>\n";
}
if (!$LogedIn) {
	echo "<p class='info' id='infi'><span class='war' title='Varování'></span>Tato sekce je přístupná jen majiteli jeskyně. <a href=\"javascript: hide('infi')\" class='permalink2' title='Zavřít'>Zavřít</a></p>";
}
elseif ($_SESSION['uid'] != $hItem->uid && !$hItem->PJs && !isset($hItem->PJs[$_SESSION['uid']])) {
	echo "<p class='info' id='infi'><span class='war' title='Varování'></span>Tato sekce je přístupná jen majiteli jeskyně. <a href=\"javascript: hide('infi')\" class='permalink2' title='Zavřít'>Zavřít</a></p>";
}
elseif ($hItem->schvaleno != '1') {
	$zLink = "/herna/$slink/$sslink";
	if (isSet($_GET['ok'])) {
		switch ($_GET['ok']) {
			case "1":
				ok("Příkazy z rozhraní pána jeskyně vpořádku vykonány.");
			break;
			case "2":
				ok("Příkazy z rozhraní pána jeskyně vpořádku vykonány.<br />Statut Jeskyně byl změněn na odesláno.");
			break;
			case "3":
				ok("Ikonka byla úspěšně nahrána a změněna.");
			break;
		}
	}
	elseif (isSet($_GET['error'])) {
		switch ($_GET['error']) {
			case "2":
				info("Musíte odeslat obrázek.");
			break;
			case "3":
				info("Ikonka musí být ve formátu GIF, JPG nebo PNG.");
			break;
			case "4":
				info("Maximální velikost odesílané ikonky je 16kB.");
			break;
			case "5":
				inf("Povolené rozměry pro ikonku jsou šířka: 40&ndash;50 a výška: 50&ndash;70 obrazových bodů (px).");
			break;
		}
	}
	echo "
<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=pj-ico' method='post' class='f' enctype='multipart/form-data'>
	<fieldset>
	<legend class='tglr'>Ikonka</legend>
	<div class='tgld'>
";
	if ($hItem->ico != "") {
		echo "	<div><img src='http://s1.aragorn.cz/i/$hItem->ico' alt='Ikonka PJ' title='Ikonka PJ' /></div>
	<label><span>Moje ikonka</span><input type='file' name='ico' /></label>\n";
	}
	else {
		echo "	<div><img src='http://s1.aragorn.cz/i/default.jpg' alt='Výchozí ikonka PJ' title='Výchozí ikonka PJ' /></div>
	<label><span>Moje ikonka</span><input type='file' name='ico' /></label>\n";
	}
echo "	<input class='button' type='submit' value='Změnit' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>
<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=pj-obecne' method='post' class='f' enctype='multipart/form-data'>
	<fieldset>
	<legend class='tglr'>Základní parametry jeskyně + Poznámky PJ</legend>
	<div class='tgld'>
";
	input("keywords_edit","Klíčová slova",$hItem->keywords,250);
	textarea("popis_edit","Popis",$hItem->popis,4);
	input("hraci_edit","Počet hráčů",$hItem->hraci_pocet,3);
	if ($hItem->povolreg == '1') {
		$RegAno = " selected"; $RegNe = "";
	} else {
 		$RegAno = ""; $RegNe = " selected";
	}
	echo "	<label for='povol_prihlasky'><span>Registrace hráčů</span><select name='povol_prihlasky' size='1'><option value='ano'$RegAno>Povolena</option><option value='ne'$RegNe>Zakazána</option></select></label>\n";
	textarea("hleda_edit","Jaké hráče",$hItem->hraci_hleda,4);
	textarea("adminy_edit","Text pro adminy",$hItem->pro_adminy,4);
?>
<label><span>Poslední úpravy?</span><select name="final_edit"><option value="-">ne</option><option value="on">ano</option></select></label>
<?php
	textarea("notes_edit","Poznámky PJ",$hItem->poznamky,10);
	textarea("nastenka_edit","Nástěnka",$hItem->nastenka,10);
	echo "	<input class='button' type='submit' value='Upravit' />
	</div>
	</fieldset>
</div>
<div class='f-bottom'></div>
</form>
<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=pj-delete&amp;c=".md5("c-".$hItem->id."-".$hItem->uid."-".$_SESSION['uid'])."' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Smazání jeskyně</legend>
	<div class='tgld'>
	<label for='smazat_jeskyni'><span>Akce</span><select id='smazat_jeskyni' name='smazat_jeskyni'>
		<option value=''> - - - - - </option>
		<option value='' disabled>&nbsp;</option>
		<option value='ano'>Smazat jeskyni</option>
		</select></label>
	<input class='button' type='submit' value='Provést akci' onclick='return confirm(\"Opravdu si přejete smazat tuto jeskyni?\");' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>
";	
}
else {
	$zLink = "/herna/$slink/$sslink";
	if (isSet($_GET['ok'])) {
		switch ($_GET['ok']) {
			case "1":
				ok("Příkazy z rozhraní Pána jeskyně vpořádku vykonány.");
			break;
			case "3":
				ok("Ikonka byla úspěšně nahrána a změněna.");
			break;
		}
	}
	elseif (isSet($_GET['error'])) {
		switch ($_GET['error']) {
			case "2":
				info("Musíte odeslat obrázek.");
			break;
			case "3":
				info("Ikonka musí být obrázek ve formátu GIF, JPG nebo PNG.");
			break;
			case "4":
				info("Maximální velikost odesílané ikonky je 16kB.");
			break;
			case "5":
				inf("Povolené rozměry pro ikonku jsou šířka: 40&ndash;50 a výška: 50&ndash;70 obrazových bodů (px).");
			break;
			case "6":
				info("Nový vlastník jeskyně musí být existující uživatel a nesmí mít v jeskyni postavu.");
			break;
			case "7":
				info("Nelze předat vlastnictví jeskyně sám sobě!");
			break;
			case "8":
				info("Obě políčka pro nového vlastníka musí obsahovat totéž jméno.");
			break;
			case "9":
				info("Uživatel, kterému chcete předat jeskyni je již aktivní v $herna_nebonus jeskyních.");
			break;
			case "24":
				info("Uživatele se nepodařilo nalézt.");
			break;
			case "25":
				info("Uživatele již v jeskyni figuruje jako hráč (i neschválený) nebo pomocný PJ.");
			break;
			case "26":
				info("Vytvoření pomocného PJ bylo omezeno (max. 3 PPJ / jeskyni, omezení bonusu na straně uživatele).");
			break;
		}
	}

	if ($allowsPJ['prispevky']) {
		$c = 1;
	}
	else {
		$c = 0;
	}

	echo "<div class='art'>
	<p class='t-a-c' id='navi_for_pjs'>
		<a href='/herna/$slink/' title='Zpátky na hlavní stránku jeskyně'>Zpět do jeskyně</a>
	";
	if ($hItem->uid == $_SESSION['uid'] || $allowsPJ['nastenka']) {
		echo "\t\t| <a href='#obecne' id='navi_for_obecne' onclick='Accorder.display($c);return false;' title='Popis a další informační texty'>Obecné&nbsp;údaje&nbsp;/&nbsp;Poznámky&nbsp;PJ</a>\n";
		$c++;
	}
	if ($hItem->uid == $_SESSION['uid'] || $allowsPJ['postavy']) {
		echo "\t\t| <a href='#hraci' id='navi_for_hraci' onclick='Accorder.display($c);return false;' title='Schválené i neschválené postavy'>Postavy&nbsp;hráčů</a>\n";
		$c++;
	}
	if ($hItem->uid == $_SESSION['uid']) {
		echo "\t\t| <a href='#pjs' id='navi_for_pjs' onclick='Accorder.display($c);return false;' title='Pomocní Páni Jeskyně'>Pomocní PJs</a>\n";
		$c++;
		echo "\t\t| <a href='#vlastnik' id='navi_for_vlastnik' onclick='Accorder.display($c);return false;' title='Předání vlastnictví jeskyně někomu jinému'>Změna&nbsp;majitele</a>\n";
		$c++;
		echo "\t\t| <a href='#promazani' id='navi_for_promazavani' onclick='Accorder.display($c);return false;' title='Promazání fóra jeskyně dle datumu'>Promazání&nbsp;textů</a>\n";
		$c++;
		echo "\t\t| <a href='#export' id='navi_for_export' onclick='Accorder.display($c);return false;' title='Export chatu / fóra jeskyně do souboru'>Export&nbsp;textů</a>\n";
		$c++;
		echo "\t\t| <a href='#delete' id='navi_for_delete' onclick='Accorder.display($c);return false;' title='Smazání jeskyně se vším všudy'>Smazání&nbsp;jeskyně</a>\n";
	}
	echo "</p>
	</div>

";

if ($allowsPJ['prispevky']) {
   	echo "<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=pj-ico' method='post' class='f' enctype='multipart/form-data'>
	<fieldset>
	<legend class='tglr'>" . ($hItem->uid == $_SESSION['uid'] ? 'Ikonka PJ' : 'Ikonka Pomocného PJ') . "</legend>
	<div class='tgld'>
";
	if (isset($uzivatele[$_SESSION['uid']]) && isset($uzivatele[$_SESSION['uid']]['ico']) && $uzivatele[$_SESSION['uid']]['ico'] != "" && $uzivatele[$_SESSION['uid']]['ico'] != "default.jpg") {
		echo "	<div><img src='http://s1.aragorn.cz/i/".$uzivatele[$_SESSION['uid']]['ico']."' alt='Ikonka' title='Ikonka' /></div>
	<label><span>Moje ikonka</span><input type='file' name='ico' /></label>\n";
	}
	else {
		echo "	<div><img src='http://s1.aragorn.cz/i/default.jpg' alt='Výchozí ikonka' title='Výchozí ikonka' /></div>
	<label><span>Moje ikonka</span><input type='file' name='ico' /></label>\n";
	}
	echo "	<input class='button' type='submit' value='Změnit' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>";
}

if ($allowsPJ['nastenka']) {
	echo "<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=pj-obecne' method='post' class='f clearer' enctype='multipart/form-data'>
	<fieldset>
	<legend class='tglr'>Základní parametry jeskyně + Poznámky PJ</legend>
	<div class='tgld'>
";
	input("keywords_edit","Klíčová slova",$hItem->keywords,250);
	textarea("popis_edit","Popis",$hItem->popis,4);
	if ($hItem->povolreg == '1') {
		$RegAno = " selected"; $RegNe = "";
	} else {
 		$RegAno = ""; $RegNe = " selected";
	}
	echo "	<label for='povol_prihlasky'><span>Registrace hráčů</span><select name='povol_prihlasky' size='1'><option value='ano'$RegAno>Povolena</option><option value='ne'$RegNe>Zakazána</option></select></label>\n";
		textarea("hleda_edit","Jaké hráče",$hItem->hraci_hleda,4);
	input("hraci_edit","Počet hráčů",$hItem->hraci_pocet,3);
	textarea("nastenka_edit","Nástěnka",$hItem->nastenka,10);
	textarea("notes_edit","Poznámky PJ",$hItem->poznamky,10);
	textarea("adminy_edit","Text pro adminy",$hItem->pro_adminy,4);
	echo "	<input class='button' type='submit' value='Upravit' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>";
}

if ($allowsPJ['postavy']) {
echo "<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=pj-schvalovani' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Hráči a jejich postavy</legend>
	<div class='tgld'>
	<ul class='hvyber'>\n";
	$hraci = mysql_query("SELECT h.jmeno, h.jmeno_rew, h.schvaleno, u.login, u.login_rew FROM 3_herna_postava_$jTypString AS h, 3_users AS u WHERE h.cid = $hItem->id AND u.id = h.uid ORDER BY h.schvaleno DESC, h.jmeno ASC");
	if (mysql_num_rows($hraci)>0) {
		while($hrac = mysql_fetch_object($hraci)) {
			$hrac->jmeno = stripslashes($hrac->jmeno);
			$hrac->login = stripslashes($hrac->login);
			$hrac->schvaleno = (($hrac->schvaleno == 0)?"neschváleno":"schváleno");
			echo "		<li><input type='checkbox' class='checkbox' name='uzivatel[]' value='$hrac->login_rew' />&nbsp;<a href='/uzivatele/$hrac->login_rew/'>$hrac->login</a>&nbsp;|&nbsp;postava&nbsp;:&nbsp;<a href='/herna/$slink/$hrac->jmeno_rew/'>$hrac->jmeno</a>&nbsp;<em>($hrac->schvaleno)</em></li>\n";
		}
	}
	else {
		echo "		<li>V jeskyni nejsou přihlášeni žádní hráči.</li>\n";
	}
	echo "	</ul>
	<label for='akce_hrac'><span>Akce</span><select id='akce_hrac' name='akce_hrac' size='1'><option value=''> - - - - - </option><option value='y'>Schválit</option><option value='n'>Neschválit</option></select></label>
	<input class='button' type='submit' value='Provést akci' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>
";
}

if ($hItem->uid == $_SESSION['uid']) {
echo "<div class='f-top'></div>
<div class='f-middle'>
<form name='pomocnipjs' action='$zLink/?akce=pj-helper' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Pomocný PJ</legend>
	<div class='tgld'>
		<table cellspacing='0' cellpadding='2' border='1' class='edttbl' style='margin-bottom:10px;'>\n";
	if ($hItem->allPJs && count($hItem->allPJs) > 0) {
echo "
			<tr><td rowspan='2'><p class='t-a-c'>Pomocný PJ</p></td><td colspan='6'>Práva<td rowspan='2'>Schválený</td></tr>
			<tr><td>Nástěnka</td><td>Poznámky</td><td>Mapy</td><td>Postavy</td><td>Obchod</td><td title='Mazat jakékoliv příspěvky na fóru'>Příspěvky</td></tr>
";
		foreach($hItem->allPJs as $ppj_id=>$ppj) {
			echo "			<tr><td><input type='checkbox' class='checkbox' name='uzivatel[$ppj->login_rew]' value='$ppj->login_rew' />&nbsp;<a href='/uzivatele/$ppj->login_rew/'>$ppj->login</a></td><td>".input_checkbox("p[$ppj->login_rew][nastenka]",1,$ppj->nastenka)."</td><td>".input_checkbox("p[$ppj->login_rew][poznamky]",1,$ppj->poznamky)."</td><td>".input_checkbox("p[$ppj->login_rew][mapy]",1,$ppj->mapy)."</td><td>".input_checkbox("p[$ppj->login_rew][postavy]",1,$ppj->postavy)."</td><td>".input_checkbox("p[$ppj->login_rew][obchod]",1,$ppj->obchod)."</td><td>".input_checkbox("p[$ppj->login_rew][prispevky]",1,$ppj->prispevky)."</td><td>".input_checkbox("p[$ppj->login_rew][schvaleno]",1,$ppj->schvaleno)."</td></tr>\n";
		}
	}
	else {
		echo "			<tr><td>Nejsou nastaveni žádní pomocní PJ.</td></tr>\n";
	}

echo "
		</table>
		<label for='new_ppj'><span>Nový pomocný PJ</span><input id='new_ppj' type='text' name='new_ppj' maxlength='40' /></label>
		<label for='akce_ppj'><span>Akce</span><select id='akce_ppj' name='akce_ppj' size='1'><option value=''> - - - - - </option><option value='update'>Upravit vybrané</option><option value='delete'>Smazat vybrané</option><option value='create'>Vytvořit</option></select></label>
		<input class='button' type='submit' value='Provést akci' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>\n";

echo "<div class='f-top'></div>
<div class='f-middle'>
<script type='text/javascript'>
function checkVlastnik() {
	if (document.forms[\"vlastnik\"][\"vlastnik_new\"].value.length < 3){
		alert (\"Jméno vlastníka musí být minimálně 3 znaky.\");
		return false;
	}

	if (document.forms[\"vlastnik\"][\"vlastnik_new\"].value != document.forms[\"vlastnik\"][\"vlastnik_new2\"].value){
		alert (\"Obě políčka se musí shodovat.\");
		return false;
	}
	return true;
}
</script>
<form name='vlastnictvi' action='$zLink/?akce=pj-vlastnik' method='post' class='f' onSubmit='javascript: checkVlastnik();'>
	<fieldset>
	<legend class='tglr'>Předat vlastnictví jeskyně</legend>
	<div class='tgld'>
";
	input("vlastnik_new","Nový vlastník","",40);
	input("vlastnik_new2","Nový vlastník znovu","",40);
	echo "	<input class='button' type='submit' value='Předat vlastnictví' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>\n";
echo "<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=jeskyne-clear&amp;c=".md5("clear-".$hItem->id."-".$hItem->uid."-".$_SESSION['uid'])."' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Promazání textů fóra jeskyně starších než:</legend>
	<div class='tgld'>
	<label for='d-rok'><span>Rok</span><select id='d-rok' name='clr_rok' type='text'><option value=''> - - - - - </option>";
	$old = date("Y");
	do {
	  echo "<option value='$old'>$old</option>";
	  $old--;
	} while ($old >= 2006);
	echo "</select></label>
	<label for='d-mesic'><span>Měsíc</span><select id='d-mesic' name='clr_mesic' type='text'><option value=''> - - - - - </option>";
	$old = array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");
	for ($i=1;$i<=count($old);$i++) {
	  echo "<option value='$i'>($i) ".$old[$i-1]."</option>";
	}
	echo "</select></label>
	<label for='d-den'><span>Den</span><select id='d-den' name='clr_den' type='text'><option value=''> - - - - - </option>";
	for ($i=1;$i<=31;$i++) {
	  echo "<option value='$i'>$i</option>";
	}
	echo "</select></label>
	<input class='button' type='submit' value='Smazat starší texty' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=jeskyne-export&amp;c=".md5("exp-".$hItem->id."-".$hItem->uid."-".$_SESSION['uid'])."' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Export</legend>
	<div class='tgld'>
	<label for='export_co'><span>Texty z</span><select id='export_co' name='export_co'>
		<option value=''> - - - - - </option>
		<option value='chat'>chatu</option>
		<option value='forum'>fóra</option>
		</select></label>
	<input class='button' type='submit' value='Provést export' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=pj-delete&amp;c=".md5("c-".$hItem->id."-".$hItem->uid."-".$_SESSION['uid'])."' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Smazání jeskyně</legend>
	<div class='tgld'>
	<label for='smazat_jeskyni'><span>Akce</span><select id='smazat_jeskyni' name='smazat_jeskyni'>
		<option value=''> - - - - - </option>
		<option value='' disabled>&nbsp;</option>
		<option value='ano'>Smazat jeskyni</option>
		</select></label>
	<input class='button' type='submit' value='Provést akci' onclick='return confirm(\"Opravdu si přejete smazat tuto jeskyni?\");' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>
";
}
?>
<script type="text/javascript">
var Accorder;
window.addEvent("load",function(){
	$$('.tglr').setStyle('cursor','pointer');
	Accorder=new Accordion($$('.tglr'),$$('.tgld'),{'show':0,'display':0,'height':true,'width':false,'opacity':false});
	if (window.location.href.indexOf('#')){
		var t = window.location.href.split('#').pop();
		if (t.length > 2) {
			t = $('navi_for_'+t);
			if (t) {
				if (t.onclick){
					t.onclick();
				}
			}
		}
	}
});</script>
<?php
}
?>
