<?php

if ($hItem->typ!='0') {
	echo "<div class='art'><p class='t-a-c'>Obchod je přístupný jen v jeskyních se systémem Dračí Doupě</p></div>\n";
}
else {

$typyItemu = array("i"=>"Vybavení","z"=>"Zbroje a štíty","a"=>"Zbroje a štíty","w"=>"Zbraně pro boj tváří v tvář","s"=>"Střelné a vrhací zbraně");
$counter = 0;

function checkMoney($penize=0,$cena=0) {
		if ($penize>$cena) {
			return true;
		}
		else return false;
}
function item_kup($prize=0,$vec) {
	global $postava;
	if ($postava->penize >= $prize) {
		return "<a href='?akce=nakup&amp;v=$vec'>Koupit</a>";
	}
	else return "";
}

if ($allow == "pj" || $allow == "hrac" || $allow == "pj2") {
	if ($hItem->shoped != "") {
		$obchodEdSrc = explode("*",$hItem->shoped);
		$obchodEd = array();
		for ($i=0;$i<count($obchodEdSrc);$i++){
			$oneItem = explode("/",$obchodEdSrc[$i]);
			$obchodEd[$oneItem[0]] = $oneItem[1];
		}
	}
	else {
		$obchodEd = array();
	}
}

$do = "normal";
$vec = "";
$v = "";
$p = "";

if (isSet($_GET['do'])) $do = $_GET['do'];
if (isSet($_GET['vec'])) $vec = $_GET['vec'];
if (isSet($_GET['v'])) $v = $_GET['v'];
if (isSet($_GET['p'])) $p = $_GET['p'];

if ($allowsPJ['obchod'] && $do == "edit" && ctype_digit($v) && $v!="" && $p != "") {
	$postavaFound = false;
	for ($i = 0; $i < count($jeskyneHraci); $i++) {
		if ($jeskyneHraci[$i]['postava_rew'] == $p) {
			$postavaHrace = $jeskyneHraci[$i]['objekt'];
			if ($postavaHrace->schvaleno == '1') {
				$postavaFound = true;
			}
			break;
		}
	}
	if (!$postavaFound) {
		echo "<p class='art text t-a-c'>Hledaná postava v jeskyni neexistuje nebo není schválená.</p>\n";
	}
	else {
		echo "<p class='art text t-a-c'>Editace inventáře postavy: <a class='permalink2' href='/$link/$slink/$postavaHrace->jmeno_rew/' title='Stránka postavy'>".stripslashes($postavaHrace->jmeno)."</a></p>
	<div class='f-top'></div><div class='f-middle'>\n";
		include_once("./add/xml_parser_func.php");
		if (mb_strlen($postavaHrace->inventar,"UTF-8") < 5) {
			inf("Postava má prázdný inventář.");
		}
		else {
			$inventar = inventar_read(stripslashes($postavaHrace->inventar));
			$invItem = returnVec($inventar,$v);
			if ($invItem[0] == false) {
				inf("V inventáři postavy není hledaný předmět.");
			}
			else {
				if (isset($invItem['id'])) {
					$itemSrc = mysql_query("SELECT * FROM 3_herna_items WHERE id = '$invItem[id]'");
					if (mysql_num_rows($itemSrc)<1) {
						inf("V databázi neexistuje hledaný předmět.");
					}
					else {
						$item = mysql_fetch_object($itemSrc);
						echo "<form method='post' action='?akce=item&amp;do=edit&amp;p=$postavaHrace->jmeno_rew&amp;v=$v' name='editace_predmetu'><table class='edttbl'>\n";
						switch ($item->typ) {
							case "z":
							case "a":
								echo "<tr><td><h4>Zbroj</h4></td><td>".stripslashes($item->nazev)."</td></tr>\n";
								echo "<tr><td><h4><acronym title='Kvalita zbroje'>KZ</acronym></h4></td><td>".$item->sila."</td></tr>\n";
							break;
							case "s":
							case "w":
								if ($item->typ == "w") {
									$inmess = "Na blízko";
								}
								else {
									$inmess = "Střelná";
								}
								echo "<tr><td><h4>Zbraň</h4></td><td>".stripslashes($item->nazev)."</td></tr>\n";
								echo "<tr><td><h4><acronym title='Síla zbraně'>SZ</acronym></h4></td><td>".$item->sila."</td></tr>\n";
								$item->oprava = intval($item->oprava);
								$item->oprava = $item->oprava>0? "+".$item->oprava:$item->oprava;
								echo "<tr><td><h4><acronym title='Útočnost zbraně'>Útoč.</acronym></h4></td><td>".$item->oprava."</td></tr>\n";
								echo "<tr><td><h4><acronym title='Obrana zbraně'>OZ</acronym></h4></td><td>".$item->obrana."</td></tr>\n";
								echo "<tr><td><h4>$inmess</h4></td><td>";
								if ($item->hands == 1) {
									echo "Jednoruční";
								}
								else {
									echo "Obouruční";
								}
								echo "</td></tr>\n";
							break;
							case "i":
								echo "<tr><td><h4>Předmět</h4></td><td>".stripslashes($item->nazev)."</td></tr>\n";
							break;
						}
						echo "<tr><td><h4>Počet</h4></td><td><input type='text' value='".$invItem['pocet']."' name='item_pocet' maxlength='5' size='5' /></td></tr>\n";
						echo "<tr><td><h4>Popis</h4></td><td><p>".stripslashes($item->popis)."</p></td></tr>\n";
						echo "<tr><td></td><td><input type='submit' class='button' value='Upravit' /></td></tr>\n";
						echo "</table></form>\n";
					}
				}
				else {
					echo "<form method='post' action='?akce=item&amp;do=edit&amp;p=$postavaHrace->jmeno_rew&amp;v=$v' name='editace_predmetu'><table class='edttbl'>\n";
					switch ($invItem['typ']) {
						case "z":
							echo "<tr><td><h4>Název zbroje</h4></td><td><input type='text' value='".stripslashes($invItem['jmeno'])."' name='item_nazev' /></td></tr>\n";
							echo "<tr><td><h4><acronym title='Kvalita zbroje'>KZ</acronym></h4></td><td><input type='text' value='".stripslashes($invItem['obrana'])."' maxlength='3' size='3' name='item_kz' /></td></tr>\n";
						break;
						case "i":
							echo "<tr><td><h4>Název předmětu</h4></td><td><input type='text' value='".stripslashes($invItem['jmeno'])."' name='item_nazev' /></td></tr>\n";
						break;
						case "w":
						case "s":
							echo "<tr><td><h4>Název zbraně</h4></td><td><input type='text' value='".stripslashes($invItem['jmeno'])."' name='item_nazev' /></td></tr>\n";
							echo "<tr><td><h4><acronym title='Síla zbraně'>SZ</acronym></h4></td><td><input type='text' value='".$invItem['sila']."' maxlength='3' size='3' name='item_sz' /></td></tr>\n";
							$invItem['oprava'] = intval($invItem['oprava']);
							$invItem['oprava'] = $invItem['oprava']>0? "+".$invItem['oprava']:$invItem['oprava'];
							echo "<tr><td><h4><acronym title='Útočnost zbraně'>Útoč.</acronym></h4></td><td><input type='text' value='".$invItem['oprava']."' maxlength='3' size='3' name='item_utoc' /></td></tr>\n";
							echo "<tr><td><h4><acronym title='Obrana zbraně'>OZ</acronym></h4></td><td><input type='text' value='".$invItem['obrana']."' maxlength='3' size='3' name='item_oz' /></td></tr>\n";
								if ($invItem['typ'] == "w") {
									$inmess = "Na blízko";
								}
								else {
									$inmess = "Střelná";
								}							echo "<tr><td><h4>$inmess</h4></td><td><select name='item_hands'><option value='1'";
								if ($invItem['hands'] == 1) {
									echo " selected>Jednoruční</option><option value='2'";
								}
								else {
									echo ">Jednoruční</option><option value='2' selected";
								}
								echo ">Obouruční</option></select></td></tr>\n";
						break;
					}
					echo "<tr><td><h4>Počet</h4></td><td><input type='text' value='".$invItem['pocet']."' name='item_pocet' maxlength='5' size='5' /></td></tr>\n";
					echo "<tr><td><h4>Popis</h4></td><td><input type='text' value='".stripslashes($invItem['popis'])."' name='item_popis' size='60' /></td></tr>\n";
					echo "<tr><td></td><td><input type='submit' class='button' value='Upravit' /></td></tr>\n";
					echo "</table></form>\n";
				}
			}
		}
		echo "</div><div class='f-bottom'></div>";
	}
}
elseif ($p != "" && $do == "add" && ($allow == "pj" || $allowsPJ['obchod'])) {
	$postavaFound = false;
	for ($i = 0; $i < count($jeskyneHraci); $i++) {
		if ($jeskyneHraci[$i]['postava_rew'] == $p) {
			$postavaHrace = $jeskyneHraci[$i]['objekt'];
			$postavaFound = true;
			break;
		}
	}
	if (!$postavaFound) {
		echo "<p class='art text t-a-c'>Hledaná postava v jeskyni neexistuje nebo není schválená.</p>\n";
	}
	else {
		if (isset($_GET['ok'])) {
			if ($_GET['ok'] == "1") {
				ok("Editace inventáře proběhla vpořádku.");
			}
		}
		echo "<p class='art text t-a-c'>Přidání předmětu do inventáře postavy: <a class='permalink2' href='/$link/$slink/$postavaHrace->jmeno_rew/' title='Stránka postavy'>".stripslashes($postavaHrace->jmeno)."</a></p>
	<div class='f-top'></div><div class='f-middle'>
	<form name='klasicky_item' action='?akce=item&amp;do=add&amp;p=$postavaHrace->jmeno_rew' method='post'>

	<div id='choiceTyp'>
<table>
	<tr><td class='t-a-c art'><h4>Z databáze doplňků</h4>
		<p class='edttbl text art t-a-c'>Předmět:\n";
$doplnkySel = mysql_query("SELECT * FROM 3_herna_items ORDER BY typ DESC, nazev ASC");
$dA = $dAopt = array();
$oldTyp = "";
while ($doplnek = mysql_fetch_object($doplnkySel)) {
	if ($doplnek->typ != $oldTyp) {
		$dAopt[$doplnek->typ] = "<optgroup label='".$typyItemu[$doplnek->typ]."'>\n";
		$oldTyp = $doplnek->typ;
	}
	$dA[$doplnek->typ][] = "<option value='$doplnek->id'>".stripslashes($doplnek->nazev)."</option>";
}
unset($oldTyp);

echo "		<select name='item'><option>- - - - -</option>\n" . 
$dAopt['z'] . join("\n",$dA['z'])."\n</optgroup>\n".
$dAopt['w'] . join("\n",$dA['w'])."\n</optgroup>\n".
$dAopt['s'] . join("\n",$dA['s'])."\n</optgroup>\n".
$dAopt['i'] . join("\n",$dA['i'])."\n</optgroup>\n".
"</select>\n";
echo "&nbsp;&nbsp;počet: <input type='text' name='item_pocet' value='1'  maxlength='5' class='sinp' />&nbsp;&nbsp;<input type='submit' value='Přidat' />
		</p>
	</td></tr>
</table>
<table>
	<tr><td class='t-a-c art'><h4>Vlastní</h4><p class='text art'>
		<a href='#' onclick='javascript:vecShow(\"w\")'>Zbraň pro boj tváří v tvář</a> | 
		<a href='#' onclick='javascript:vecShow(\"s\")'>Střelná nebo vrhací zbraň</a> | 
		<a href='#' onclick='javascript:vecShow(\"z\")'>Zbroj nebo štít</a> | 
		<a href='#' onclick='javascript:vecShow(\"i\")'>Předmět</a>
	</p></td></tr>
</table>
</div>
<table><tbody><tr><td id='formItemTyp'></td></tr></tbody></table></form>
</div><div class='f-bottom'></div>";
ob_flush();
?>
<script type='text/javascript'>
function vecShow(typ) {
	var starter = document.getElementById('choiceTyp');
	var formik = document.getElementById('formItemTyp');
	if (starter.innerHTML != '') {
	starter.innerHTML = "";
	starter.style.visibility = "hidden";
	starter.style.display = "none";
	formik.style.display = "block";
	inmess0 = "<p class='art text'><a href='./?do=add&amp;p=<?php echo $_GET['p'];?>' class='permalinkb2'>Zpět na výběr</a></p><table class='edttbl'><tr><td colspan='2'><h4>";
	inmess1 = "</h4></td></tr><tr><td><h4>Název:</h4></td><td><input type='text' maxlength='40' name='item_nazev' /></td></tr>";
	inmess2 = "<tr><td><h4>Popis:</h4></td><td><input type='text' name='item_popis' maxlength='250' size='60' /></td></tr><tr><td><h4>Počet:</h4></td><td><input type='text' name='item_pocet' class='sinp' maxlength='5' value='1' /></td></tr><tr><td><input type='hidden' name='item_typ' value='"+typ+"' /></td><td><input type='submit' value='Přidat' /></td></tr></table>";
	mess = "";
	if (typ == "z" || typ == "a") {
		mess = "Zbroj nebo štít";
		formik.innerHTML = inmess0+mess+inmess1+"<tr><td><h4>Kvalita:</h4></td><td><input type='text' name='item_kz' maxlength='3' class='sinp' /></td></tr>"+inmess2;
	}
	else if (typ == "i") {
		mess = "Předmět (lektvar, svitek, vybavení)";
		formik.innerHTML = inmess0+mess+inmess1+inmess2;
	}
	else if (typ == "w" || typ == "s") {
		mess = typ=="w"?"Zbraň pro boj tváří v tvář":"Střelná nebo vrhací zbraň";
		formik.innerHTML = inmess0+mess+inmess1+"<tr><td><h4>Síla:</h4></td><td><input type='text' name='item_sz' maxlength='3' class='sinp' /></td></tr><tr><td><h4>Útočnost:</h4></td><td><input type='text' class='sinp' name='item_utoc' maxlength='3' /></td></tr><tr><td><h4>Obrana:</h4></td><td><input type='text' class='sinp' name='item_oz' maxlength='3' /></td></tr><tr><td>&nbsp;</td><td><select name='item_hands'><option value='1'>Jednoruční</option><option value='2'>Obouruční</option></select></td></tr>"+inmess2;
	}
	formik.focus();
	}
}
</script>
	<?php
	}
}
elseif (($allow == "pj" || $allowsPJ['obchod']) && $do == "c" && ctype_digit($v) && $v!="") {
	$vecSrc = mysql_query("SELECT * FROM 3_herna_items WHERE id = '$v'");
	if (mysql_num_rows($vecSrc)>0) {
		$vecItem = mysql_fetch_object($vecSrc);
		$vaseCena = $vecItem->cena;
		if (isSet($obchodEd[$v])) {
			$vaseCena = $obchodEd[$v];
		}
		if ($vaseCena < 0) {
			echo "<a class='permalink2' href='/herna/$slink/shop/' class='permalink2'>Zpět do obchodu</a>
	<div class='art'><p>"._htmlspec($vecItem->nazev)." má nastaveno nepovolené obchodování.</p>\n";
			echo "<a class='permalink2' href='?akce=obchod&amp;c=n&amp;v=$v'>Povolit obchodování předmětu</a></div>\n";
		}
		else {
			$vaseCena = arrayCena($vaseCena);
			$normCena = arrayCena($vecItem->cena);
			echo "<a href='/herna/$slink/shop/' class='permalink2'>Zpět do obchodu</a>\n";
			echo "<div class='art'><form action='?akce=obchod&amp;c=c&amp;v=$v' method='post' name='zmena_ceny' class='f edttbl'>
	<div><strong>"._htmlspec($vecItem->nazev)."</strong></div>
	Vaše cena: <input name='new_cena_zl' class='sinp' value='$vaseCena[0]' /> zl <input class='sinp' name='new_cena_st' value='$vaseCena[1]' /> st <input class='sinp' name='new_cena_md' value='$vaseCena[2]' /> md<br />  
	Standartní: <span>$normCena[0]</span> zl <span>$normCena[1]</span> st <span>$normCena[2]</span> md<br />
	<input type='submit' value='Upravit' class='button' />
</form>
</div>\n";
		}
	}
	else {
		info("Položka s číslem $v nebyla v obchodě nalezena ;)");
		echo "<a href='/herna/$slink/shop/' class='permalink2'>Zpět do obchodu</a>\n";
	}
}
else {
if ($allow == "pj" || $allowsPJ['obchod']) {

	echo "<div class='art text t-a-c'><form class='edttbl' action='' method='get'>Přidat vybavení do inventáře: <input type='hidden' name='do' value='add' /><select name='p'>";

	for ($i = 0; $i < count($jeskyneHraci); $i++) {
		$postavaHrace = $jeskyneHraci[$i]['objekt'];
		if ($postavaHrace->schvaleno == '1') {
			echo "<option value='$postavaHrace->jmeno_rew'>".stripslashes($postavaHrace->jmeno)."</option>\n";
		}
	}
	
	echo "</select> <input type='submit' value='Pokračovat' /></form></div>\n";
	// PJ jeskyne je v obchode
	if ($hItem->obchod == '0') {
		echo "<div class='art'><p class='t-a-c'><a href='?akce=obchod&amp;c=a&amp;h=1&amp;k=".md5("c-".$_SESSION['uid']."-".$hItem->id)."' title='Povolí hráčům v jeskyni prodávat či nakupovat výbavu v obchodě' class='permalink2'>Povolit vstup do obchodu</a></p></div>\n";
		inf("Obchod je zakázaný, hráči nemohou nakupovat, ani prodávat vybavení.");
	}
	else {
		echo "<div class='art'><p class='t-a-c'><a href='?akce=obchod&amp;c=a&amp;h=0&amp;k=".md5("c-".$_SESSION['uid']."-".$hItem->id)."' title='Zakáže hráčům v jeskyni prodávat i nakupovat vybavení v obchodě' class='permalink2'>Zakázat obchodování</a>";
		if ($hItem->shoped != "") {
			echo " | <a href='?akce=obchod&amp;c=e&amp;k=".md5("c-".$_SESSION['uid']."-".$hItem->id)."' title='Vrátí všechny provedené úpravy ceníku do výchozích hodnot' class='permalink2'>Výchozí ceny</a>";
		}
		echo "</p>\n";
		$obchodSrc = mysql_query("SELECT * FROM 3_herna_items ORDER BY typ DESC, nazev ASC");
		echo "<table class='obchod'>
	<tbody>\n";
		$typOld = "";
		while ($zbozi = mysql_fetch_object($obchodSrc)) {
			$counter++;
			if ($zbozi->typ != $typOld) {
				$typOld = $zbozi->typ;
				echo "<tr><td class='obchod-ctgr' colspan='2'>$typyItemu[$typOld]</td><td class='obchod-ctgr obchod-ctrl'>Cena</td><td class='obchod-ctgr obchod-ctrl'>Možnosti</td></tr>\n";
			}
			if (isSet($obchodEd[$zbozi->id])) {
				if ($obchodEd[$zbozi->id]>=0) {
					$povoleni = item_nabidka(true,$zbozi->id);
					$cena = item_cena($obchodEd[$zbozi->id]);
				}
				else {
					$povoleni = item_nabidka(false,$zbozi->id);
					$cena = " ";
				}
			}
			else {
				$povoleni = item_nabidka(true,$zbozi->id);
				$cena = item_cena($zbozi->cena);
			}
			$onmouseover = _htmlspec($zbozi->popis);
			switch ($zbozi->typ) {
				case "z":
					if ($zbozi->hands == '1') {
						$zbozi->hands = "Jednoruční | ";
						if ($zbozi->sila > 0) {
							$zbozi->sila = "+".$zbozi->sila;
						}
					}
					else {
						$zbozi->hands = "";
					}
					$onmouse = "<b>".$zbozi->hands."KZ: $zbozi->sila </b><br />";
				break;
				case "s":
					if ($zbozi->hands == '2') {
						$zbozi->hands = "Obouruční";
					}
					else {
						$zbozi->hands = "Jednoruční";
					}
					$onmouse = "<b>$zbozi->hands | SZ: $zbozi->sila | Útoč: ".(($zbozi->oprava>0) ? "+".$zbozi->oprava : $zbozi->oprava)." | OZ: $zbozi->obrana </b><br />";
				break;
				case "w":
					if ($zbozi->hands == '2') {
						$zbozi->hands = "Obouruční";
					}
					else {
						$zbozi->hands = "Jednoruční";
					}
					$onmouse = "<b>$zbozi->hands | SZ: $zbozi->sila | Útoč: ".(($zbozi->oprava>0) ? "+".$zbozi->oprava : $zbozi->oprava)." | OZ: $zbozi->obrana </b><br />";
				break;
				case "i":
					$onmouse = "";
				break;
			}
			echo "<tr><td>$counter&nbsp;</td><td><div class=\"shp-$zbozi->typ\" onmouseover=\"ddrivetip('".$onmouse._htmlspec($zbozi->popis)."')\" onmouseout='hidedrivetip();'>"._htmlspec(stripslashes($zbozi->nazev))."</div></td><td>$cena</td><td>".$povoleni."</td></tr>\n";
		}
		echo "</tbody></table>
	</div>\n";
	}
	ob_flush();
}
elseif ($allow == "hrac") {
	// hrac jeskyne je v obchode
	if ($hItem->obchod == '0') {
		echo "<div class='art'><p class='t-a-c'>Obchod je zakázaný</p></div>\n";
		inf("Obchod je zakázaný, nemůžete nakupovat, ani prodávat vybavení.");
	}
	else {
		echo "<div class='art'><h4>Obchod</h4>\n";
		echo "\t<p class='t-a-c'>Postava: <strong>$postava->jmeno</strong> | Finance: ".item_cena($postava->penize)."</p>\n";
		$obchodSrc = mysql_query("SELECT * FROM 3_herna_items ORDER BY typ DESC, nazev ASC");
		echo "<table class='obchod'>
	<tbody>\n";
		$typOld = "";
		while ($zbozi = mysql_fetch_object($obchodSrc)) {
			$counter++;
			if ($zbozi->typ != $typOld) {
				$typOld = $zbozi->typ;
				echo "<tr><td class='obchod-ctgr' colspan='2'>$typyItemu[$typOld]</td><td class='obchod-ctgr obchod-ctrl'>Cena</td><td class='obchod-ctgr obchod-ctrl'>Možnosti</td></tr>\n";
			}
			if (isSet($obchodEd[$zbozi->id])) {
				if ($obchodEd[$zbozi->id]>=0) {
					$povoleni = item_kup($obchodEd[$zbozi->id],$zbozi->id);
					$cena = item_cena($obchodEd[$zbozi->id]);
				}
				else {
					$povoleni = "";
					$cena = " ";
				}
			}
			else {
				$povoleni = item_kup($zbozi->cena,$zbozi->id);
				$cena = item_cena($zbozi->cena);
			}
			$onmouseover = _htmlspec($zbozi->popis);
			switch ($zbozi->typ) {
				case "z":
					if ($zbozi->hands == '1') {
						$zbozi->hands = "Jednoruční | ";
						if ($zbozi->sila > 0) {
							$zbozi->sila = "+".$zbozi->sila;
						}
					}
					else {
						$zbozi->hands = "";
					}
					$onmouse = "<b>".$zbozi->hands."KZ: $zbozi->sila </b><br />";
				break;
				case "s":
					if ($zbozi->hands == '2') {
						$zbozi->hands = "Obouruční";
					}
					else {
						$zbozi->hands = "Jednoruční";
					}
					$onmouse = "<b>$zbozi->hands | SZ: $zbozi->sila | Útoč: ".(($zbozi->oprava>0) ? "+".$zbozi->oprava : $zbozi->oprava)." | OZ: $zbozi->obrana </b><br />";
				break;
				case "w":
					if ($zbozi->hands == '2') {
						$zbozi->hands = "Obouruční";
					}
					else {
						$zbozi->hands = "Jednoruční";
					}
					$onmouse = "<b>$zbozi->hands | SZ: $zbozi->sila | Útoč: ".(($zbozi->oprava>0) ? "+".$zbozi->oprava : $zbozi->oprava)." | OZ: $zbozi->obrana </b><br />";
				break;
				case "i":
					$onmouse = "";
				break;
			}
			echo "<tr><td>$counter&nbsp;</td><td><div class=\"shp-$zbozi->typ\" onmouseover=\"ddrivetip('".$onmouse._htmlspec($zbozi->popis)."<br />Váha: ".$zbozi->vaha." mincí')\" onmouseout='hidedrivetip();'>"._htmlspec(stripslashes($zbozi->nazev))."</div></td><td>$cena</td><td>".$povoleni."</td></tr>\n";
		}
		echo "</tbody></table>
	</div>\n";
		ob_flush();
	}
}
else {
	info("Tato sekce je přístupná jen hráčům a majiteli této jeskyně.");
}
}
}
?>
