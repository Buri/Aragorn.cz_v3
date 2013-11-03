<?php

  function make_row($nazev,$value,$ed=false) {
    if ($ed > 100 || $ed === true) {
			$nazev = "<a href=\"#\" onclick=\"return hide_row(event)\">".$nazev."</a>";
			$value = " <div class=\"hide\">".$value."</div> ";
		}
		return "		<tr><td><h4>$nazev&nbsp;:</h4></td><td>$value</td></tr>\n";
	}

  function textareatize($name,$rows,$cols,$value) {
    return "<textarea name='$name' rows='$rows' cols='$cols'>"._htmlspec($value)."</textarea>";
	}

	function inputize($name,$max,$value,$size=20) {
    return "<input name='$name' type='text' size='$size' maxlength='$max' value='"._htmlspec($value)."' />";
	}

	function input_textarea($inp,$inp_name,$typ,$short) {
		if ($short>0) $short = " class='sinp' maxlength='5'";
		else $short = "";
		if ($typ == "a") $t = "<textarea name='$inp_name' rows='8'>"._htmlspec($inp)."</textarea>";
		else $t = "<input$short type='text' name='$inp_name' value='"._htmlspec($inp)."' />";
		return $t;
	}

	function radek_tabulky($bunka1,$bunka2) {
		echo "		<tr><td><h4>$bunka1&nbsp;:</h4></td><td>$bunka2</td></tr>\n";
	}

$uidH = 0;
$edt = false;
$povol = 0;
$adminlink = $vlastLink = $systemShop = "";
$postavaBack = "<div class='art'><p><a href='/herna/$slink/$sslink/' title='Zpět na stránku postavy'>Zpátky na postavu</a>&nbsp;</p></div>\n";

$uidH = $_SESSION['uid'];
$md5Check = md5($hItem->id."-".$postava->id."-".$uidH);

if ($jTypString == "drd") $systemShop = " - <a href='/herna/$slink/shop/?do=add&amp;p=$postava->jmeno_rew' title='Přidat postavě předmět z databáze doplňků nebo vlastní'>Přidat předmět do inventáře</a>";

$vlastLink = make_row("Vlastník","<a title='Profil uživatele' class='permalink2' href='/uzivatele/$postava->vlastnik_rew/'>$postava->vlastnik</a>",false);
if ($LogedIn == true && $hFound) {
	$uidH = $_SESSION['uid'];
	$md5Check = md5($hItem->id."-".$postava->id."-".$uidH);
	if ($_SESSION['uid'] == $postava->uid) {
		$vlastLink = make_row("Má&nbsp;postava","<p><a href='javascript:conf(\"/herna/$slink/$sslink/?akce=postava-kill&amp;c=".md5($_SESSION['uid']."-".$postava->uid."-".$postava->id)."\");'>zabít</a></p>",false);
		$povol = 1;
		$adminlink = "<div class='art'><p><a href='/herna/$slink/$sslink/?useredit=1&amp;e=".$md5Check."' title='Editace postavy'>Editace postavy</a>" . ($allowsPJ['postavy'] ? "<a href='/herna/$slink/$sslink/?pjedit=1&amp;e=".$md5Check."' title='Editace postavy jako PJ'>Editace postavy jako PJ</a>" : "") . " - <a href='/herna/$slink/' title='Zpátky do jeskyně'>Zpět do jeskyně</a></p></div>\n";
		if (isSet($_GET['e'])) {
			if ($md5Check == $_GET['e']) {
				$edt = true;
				$adminlink = $postavaBack;
			}
		}
	}
	elseif ($_SESSION['uid'] == $hItem->uid || $allowsPJ['postavy']) {
		$povol = 2;
		if ($postava->schvaleno == '1') $adminlink = "<div class='art'><p><a href='/herna/$slink/$sslink/?pjedit=1&amp;e=".$md5Check."' title='Editace postavy'>Editace postavy</a> - <a href='/herna/$slink/' title='Zpátky do jeskyně'>Zpět do jeskyně</a>$systemShop</p></div>\n";
		if (isSet($_GET['e'])) {
			if ($md5Check == $_GET['e']) {
				if ($postava->schvaleno == '1')	$edt = true;
				else $edt = false;
				$adminlink = $postavaBack;
				$vlastLink = "";
			}
		}
	}
}

$enctype = "";
$form = "";
$formEnd = "";
$pjEdit = 0;
$hracEdit = 0;

if ($postava->uid == $_SESSION['uid'] && $edt==true) {
	$enctype = " enctype=\"multipart/form-data\"";
}

if (isset($_GET['pjedit']) && $allowsPJ['postavy']) {
	$pjEdit = 1;
}
elseif (isset($_GET['useredit']) && $edt && $povol == 1) {
	$hracEdit = 1;
}

if (isset($_GET['e']) && $md5Check == $_GET['e']) {
	$form = "\t<form method=\"post\" onsubmit=\"return check_form('frm-edt-postava')\" id=\"frm-edt-postava\" action=\"/herna/$slink/$sslink/?akce=postava-edit\"$enctype>";
	$formEnd = "</form>\n";
}

if ($edt==true && $povol == 1) $edt = true;
elseif ($edt == true && $povol == 2 && $postava->schvaleno == '1') $edt = true;
else $edt = false;

echo '
<script type="text/javascript">
function hide_row(e){var p;if(!e)var e=window.event;if(e.target)p=e.target;else if(e.srcElement)p=e.srcElement;if(p.nodeType==3){p=p.parentNode;}while(p.tagName.toUpperCase()!="TR"&&p.parentNode){p=p.parentNode;}p=p.getElementsByTagName("DIV");for(var q=0;q<p.length;q++){if(p[q].className=="hide" || p[q].style.display=="none"){p[q].className="";p[q].style.display="block";p[q].style.visibility="visible";}else{p[q].className="";p[q].style.display="none";p[q].style.visibility="hidden";}}return false;}';
if($edt && $jTypString == "orp") echo '
function check_form(fid){
	var f=$(fid);
	var ins=f.getElements("input[type=text],select");
	for(var aq=0;aq<ins.length;aq++){
		var inp=ins[aq];
		if(inp.get("tag")=="select"){
			if (inp.get("value") == "") {
				var p=inp;while(p.tagName.toUpperCase()!="TR"&&p.parentNode)p=p.parentNode;
				var h=p.getElementsByTagName("H4")[0];
				alert("Musíte vybrat jednu z možností pro "+h.firstChild.data.slice(0,-2)+".");
				inp.focus();
				return false;
			}
		}
		if (inp.hasAttribute("rel") && (inp.getAttribute("rel").substr(0,6)=="number"||inp.getAttribute("rel").substr(0,6)=="random")){
			var ar = inp.getAttribute("rel")||inp.rel;
			ar = ar.slice(7,-1) ;
			ar = ar.split(":");
			ar[0]=parseInt(ar[0]);
			ar[1]=parseInt(ar[1]);
			if(ar[0]>ar[1]){
				ar[2]=ar[0];
				ar[0]=ar[1];
				ar[1]=ar[2];
			}
			if(inp.value.length<1||inp.value<ar[0]||inp.value>ar[1]){
				var p=inp;while(p.tagName.toUpperCase()!="TR"&&p.parentNode)p=p.parentNode;
				var h=p.getElementsByTagName("H4")[0];
				alert("Políčko "+h.firstChild.data.slice(0,-2)+" musí být číslo větší než "+ar[0]+" a menší než "+ar[1]+".");
				inp.focus();
				return false;
			}
		}
	}
	return true;
}';
elseif ($edt && $povol > 0) echo '
function check_form(fid){var f=document.getElementById(fid);var ins=f.getElementsByTagName("INPUT");for(var aq=0;aq<ins.length;aq++){var inp=ins[aq];if(inp.type.toLowerCase()=="text"){continue;}if (inp.hasAttribute("rel") && inp.getAttribute("rel").substr(0,6)=="number"){if (inp.value.length<1){var p=inp;while(p.tagName.toUpperCase()!="TR"&&p.parentNode)p=p.parentNode;var h=p.getElementsByTagName("H4").item(0);alert("Políčko "+h.firstChild.data.slice(0,-2)+" musí být číslo.");inp.focus();return false;}}}return true;}';

echo '
</script>';

if ($pFound == true) {
	if (isSet($_GET['error'])) {
		switch ($_GET['error']) {
			case 1: info("Postava s podobným jménem již v jeskyni je. Zkuste ho tedy nějak pozměnit.");
			break;
			case 2: info ("Jméno postavy je buď příliš krátké (minimální délka jsou 2 znaky) nebo je zakázané (chat/mapy/pj...)");
			break;
			case 3: info("Ikonka musí být obrázek ve formátu GIF, JPG nebo PNG.");
			break;
			case 4: info("Maximální povolená velikost ikonky postavy je 16kB.");
			break;
			case 5: info("Rozměry ikonky musí být mezi 40&times;50 až 50&times;70 (šířka&times;výška) obrazových bodů.");
			break;
			case 6: if ($hItem->typ == 0) info("Přesvědčení postavy musíte vybrat z nabízených možností.");
			break;
			case 7: if ($hItem->typ == 0) info("Atributy postavy musí být celá čísla od 1 do 21.");
			break;
			case 8: if ($hItem->typ == 0) info("Váha, výška, zkušenosti, životy a maximum životů musí být celá nezáporná čísla.");
			break;
		}
	}
	elseif (isSet($_GET['ok'])) {
		switch ($_GET['ok']) {
			case 1: ok("Postava byla vpořádku upravena.");
			break;
			case 2: if ($hItem->typ == 0) ok("Úroveň postavy zvýšena o jedna.");
			break;
			case 3: if ($hItem->typ == 0) ok("Úroveň postavy snížena o jedna.");
			break;
			case 4: if ($hItem->typ == 0) ok("Kouzlo přidáno.");
			break;
			case 5: if ($hItem->typ == 0) ok("Kouzlo odebráno.");
			break;
		}
	}
}

echo "<div class='highlight-top'></div><div class='highlight-mid'>\n\t".$adminlink.$form;
echo "	<table class='edttbl'>\n";

if ($hItem->typ == 1) {	// ========== POSTAVA === OPEN === ROLE === PLAY =======

	$polePolicek = $poleNazvu = $poleTypu = $poleHodnot = array();
	$inp = array();

	if ($edt==true && $povol>0) { // ----------------- EDITACE --------------
		$button = true;
		if ($postava->uid == $_SESSION['uid'] || $hracEdit) { // ------------- vlastnik ORP
			$inp['jmeno'] = make_row("Jméno",inputize("jmeno_edit",35,$postava->jmeno),false);
			$inp['zivotopis'] = make_row("Životopis",textareatize("zivotopis_edit",14,30,$postava->zivotopis),false);
			$inp['popis'] = make_row("Popis",textareatize('popis_edit',8,30,$postava->popis),false);
			$inp['poznamky_hrac'] = make_row("Soukromé<br />poznámky",textareatize('poznamky_hrac_edit',8,30,$postava_poznamka->poznamka),false);
		}
		else { // ---------- PJ ORP JESKYNE
			$inp['jmeno'] = make_row("Jméno",_htmlspec($postava->jmeno),false);
			$inp['zivotopis'] = make_row("Životopis",nl2br(_htmlspec($postava->zivotopis)),mb_strlen($postava->zivotopis));
			$inp['popis'] = make_row("Popis",textareatize('popis_edit',8,30,$postava->popis),false);
		}
		
		$inp['inventar'] = make_row("Inventář",textareatize('inventar_edit',8,30,$postava->inventar),false);
		$inp['atributy'] = make_row("Atributy",textareatize('atributy_edit',8,30,$postava->atributy),false);
		$inp['kouzla'] = make_row("Kouzla a schopnosti",textareatize('specials_edit',8,30,$postava->kouzla),false);
		$inp['ikonka'] = "<tr><td><span class='hico'>".((strlen($postava->ico)>3)?"<img src='http://s1.aragorn.cz/i/$postava->ico' />":"<img src='http://s1.aragorn.cz/i/default.jpg' />")."</span></td><td>&nbsp;</td></tr>\n";
	}
	else {
		$button = false;
		$inp['jmeno'] = make_row("Jméno",_htmlspec($postava->jmeno),false);
		$inp['popis'] = make_row("Popis",nl2br(_htmlspec($postava->popis)),mb_strlen($postava->popis));
		if ($povol > 0) {
			$inp['zivotopis'] = make_row("Životopis",nl2br(_htmlspec($postava->zivotopis)),mb_strlen($postava->zivotopis));
			$inp['inventar'] = make_row("Inventář",nl2br(_htmlspec($postava->inventar)),mb_strlen($postava->inventar));
			$inp['atributy'] = make_row("Atributy",nl2br(_htmlspec($postava->atributy)),mb_strlen($postava->atributy));
			$inp['kouzla'] = make_row("Kouzla a schopnosti",nl2br(_htmlspec($postava->kouzla)),mb_strlen($postava->kouzla));
		}
		$inp['ikonka'] = "<tr><td><span class='hico'>".((strlen($postava->ico)>3)?"<img src='http://s1.aragorn.cz/i/$postava->ico' />":"<img src='http://s1.aragorn.cz/i/default.jpg' />")."</span></td><td>&nbsp;</td></tr>\n";
	}
	if (($povol == 1 && $edt) || $hracEdit) {
		$inp['ikonka'] = "<tr><td><h4>Ikonka&nbsp;:</h4><span class='hico'>".((strlen($postava->ico)>3)?"<img src='http://s1.aragorn.cz/i/$postava->ico' />":"<img src='http://s1.aragorn.cz/i/default.jpg' />")."</span></td><td><input type='file' name='ico' /></td></tr>\n";
	}
	if (!$button) {
		echo $vlastLink;
	}
	echo $inp['jmeno'];
	echo "\t\t".$inp['ikonka'];

/*

──────────────────────────────────────────┐
		NEW ORP 0.8 "playable version"        │
──────────────────────────────────────────┘

*/

	if ( mb_strlen($postava->by_pj) > 5 ) {
	  $ext = "";
		$attrs = explode($hCh,$postava->by_pj);
		$atnmb = 0;
		foreach($attrs as $nmb => $attrText){
			$hider = false;
			$attr = explode(">",$attrText);
			if ($povol > 0) {
				if ($postava->uid == $_SESSION['uid'] || $hracEdit) {
					if ($edt) {		// ----------------------------------------------------- vlastnik edituje
						if ($attr[3] == "n" || $attr[3] == "v") { // nepovoli editaci vlastnikovi postavy
							if (mb_strlen($attr[0])>100) $hider = true;
							if ($attr[2] == "a") $attr[0] = nl2br($attr[0]);
							$ext .= make_row($attr[1],$attr[0],$hider);
						}
						else {
							switch($attr[2]) {
								case "a": // area :)
								  if (mb_strlen($attr[0])>100) $hider = true;
									$attr[0] = "<textarea name=\"orp-attr-$atnmb\" rows=\"4\" cols=\"30\">".$attr[0]."</textarea>";
								break;
								case "t": // textove policko
									$attr[0] = "<input type=\"text\" name=\"orp-attr-$atnmb\" value=\"$attr[0]\" rel=\"text\" />";
								break;
								case "n": // normalni cislo
									$attr[0] = "<input type=\"text\" name=\"orp-attr-$atnmb\" maxlength=\"5\" size=\"5\" value=\"$attr[0]\" rel=\"number[".$attr[4].":".$attr[5]."]\" />";
								break;
								case "r": // nahodne cislo
									$attr[0] = "$attr[0]";
								break;
								case "s":
									$sel = "<select name=\"orp-attr-$atnmb\" rel=\"select\">";
									/* value, name, type, editview, option1, option2, option3 ... */
									$opt = "";
									for ($aa=4;$aa<count($attr);$aa++) {
										if ($attr[0] != $attr[$aa]) {
										  $opt .= "<option value=\"$attr[$aa]\">$attr[$aa]</option>";
										}
									}
									$sel .= "<option value=\"$attr[0]\" selected=\"selected\">$attr[0]</option>".$opt."</select>";
									$attr[0] = $sel;
								break;
							}
							$ext .= make_row($attr[1],$attr[0],$hider);
						}
					}
					else {  // ----------------------------------------------------------- vlastnik prohlizi
						if (mb_strlen($attr[0])>100) $hider = true;
						if ($attr[2] == "a") $attr[0] = nl2br($attr[0]);
						$ext .= make_row($attr[1],$attr[0],$hider);
					}
				}
				else {  // PJ
					if ($edt) {		// ----------------------------------------------------- PJ edituje
						switch($attr[2]) {
							case "a": // area :)
							  if (mb_strlen($attr[0])>100) $hider = true;
								$attr[0] = "<textarea name=\"orp-attr-$atnmb\" rows=\"4\" cols=\"30\">".$attr[0]."</textarea>";
							break;
							case "t": // textove policko
								$attr[0] = "<input type=\"text\" name=\"orp-attr-$atnmb\" value=\"$attr[0]\" rel=\"text\" />";
							break;
							case "n": // normalni cislo
								$attr[0] = "<input type=\"text\" name=\"orp-attr-$atnmb\" maxlength=\"5\" size=\"5\" value=\"$attr[0]\" rel=\"number[".$attr[4].":".$attr[5]."]\" />";
							break;
							case "r": // nahodne cislo
								$attr[0] = "<input type=\"text\" name=\"orp-attr-$atnmb\" maxlength=\"5\" size=\"5\" value=\"$attr[0]\" rel=\"number[".$attr[4].":".$attr[5]."]\" />";
							break;
							case "s":
								$sel = "<select name=\"orp-attr-$atnmb\" rel=\"select\">";
								/* value, name, type, editview, option1, option2, option3 ... */
								$opt = "";
								for ($aa=4;$aa<count($attr);$aa++) {
									if ($attr[0] != $attr[$aa]) {
									  $opt .= "<option value=\"$attr[$aa]\">$attr[$aa]</option>";
									}
								}
								$sel .= "<option value=\"$attr[0]\" selected=\"selected\">$attr[0]</option>".$opt."</select>";
								$attr[0] = $sel;
							break;
						}
						$ext .= make_row($attr[1],$attr[0],$hider);
					}
					else {  // ----------------------------------------------------------- PJ prohlizi
						if (mb_strlen($attr[0])>100) $hider = true;
						if ($attr[2] == "a") $attr[0] = nl2br($attr[0]);
						$ext .= make_row($attr[1],$attr[0],$hider);
					}
				}
			}
			else { // ostatni
				if ($attr[3] == "a" || $attr[3] == "v") {
					if (mb_strlen($attr[0])>100) $hider = true;
					if ($attr[2] == "a") $attr[0] = nl2br($attr[0]);
					$ext .= make_row($attr[1],$attr[0],$hider);
				}
			}
			$atnmb++;
		}
		echo $ext;
	}

	if ($povol > 0) {
		echo $inp['atributy'];
		echo $inp['kouzla'];
		echo $inp['popis'];
		echo $inp['zivotopis'];
		echo $inp['inventar'];
		echo $inp['poznamky_hrac'];
	}
	else {
		echo $inp['popis'];
	}
	if ($button) {
		echo "\t\t<tr><td></td><td><input type='submit' value='Editovat' /></td></tr>\n";
	}
	echo "	</table>\n";
}	//																			DRACI DOUPE !!!!!!!!!!!!!!!!!!!!!!
else {//																	DrD ----- Draci Doupe ----- Dracak
	if ($edt == true && $povol>0) {

		if ($povol == 2 || $pjEdit) {				// POSTAVU DrD EDITUJE PJ
			include "./add/drd-fce.php";
			$uroven = level_postavy($postava->povolani,$postava->xp);
//			$bojeschopnost = bojeschopnost($postava->zivoty_max,$postava->odolnost);
			if ($postava->uroven < $uroven) $lvlup = " | <a href='/herna/$slink/$sslink/?akce=postava-level-up' class='permalink2'>Zvýšit úroveň o 1</a>";
			elseif ($postava->uroven > $uroven) $lvlup = " | <a href='/herna/$slink/$sslink/?akce=postava-level-down' class='permalink2'>Snížit úroveň o 1</a>";
			else $lvlup = "";

			$postava->zivoty = input_textarea($postava->zivoty,"zivoty_edit","t",1);
			$postava->zivoty_max = input_textarea($postava->zivoty_max,"zivoty_max_edit","t",1);
			$postava->magy = input_textarea($postava->magy,"magy_edit","t",1);
			$postava->magy_max = input_textarea($postava->magy_max,"magy_max_edit","t",1);
			$postava->schopnosti = input_textarea($postava->schopnosti,"schopnosti_edit","a",0);
			$postava->xp = input_textarea($postava->xp,"zkusenosti_edit","t",0);
			$postava->vyska = input_textarea($postava->vyska,"vyska_edit","t",1);
			$postava->vaha = input_textarea($postava->vaha,"vaha_edit","t",1);
			$postava->popis = input_textarea($postava->popis,"popis_edit","a",0);

			echo $vlastLink;
			echo make_row("Jméno",$postava->jmeno);
			$attrS = array("sila", "obratnost", "odolnost", "inteligence", "charisma");
			$attrN = array("Síla", "Obratnost", "Odolnost", "Inteligence", "Charisma");

			for ($a = 0; $a < count($attrS); $a++) {
				$atS = $attrS[$a]; $atN = $attrN[$a];
				$postava->$atS = input_textarea($postava->$atS,$atS."_edit","t",1);
				echo make_row($atN,$postava->$atS);
			}

			echo "\t\t<tr><td colspan='2'><hr /></td></tr>\n";
			echo make_row("Životy",$postava->zivoty." / ".$postava->zivoty_max);
			if (floor($postava->povolani / 2) > 0 && floor($postava->povolani / 2) < 4) {
				echo make_row("Magy", $postava->magy . " / " . $postava->magy_max);
			}

			echo make_row("Váha",$postava->vaha." mincí (20 mincí = 1kg)");
			echo make_row("Výška",$postava->vyska." coulů (1 coul = 1 cm)");
			echo "\t\t<tr><td colspan='2'><hr /></td></tr>\n";
			echo make_row("Schopnosti",$postava->schopnosti);
			echo make_row("Popis",$postava->popis);
			echo make_row("Zkušenosti",$postava->xp." zk => $uroven.úroveň");
			echo make_row("Úroveň",$postava->uroven.$lvlup);
			$prachy = arrayCena($postava->penize);
			echo make_row("Finance","<input name='zl_edit' class='sinp' value='$prachy[0]' /> zl <input class='sinp' name='st_edit' value='$prachy[1]' /> st <input class='sinp' name='md_edit' value='$prachy[2]' /> md");
		}
		elseif ($povol==1 || $hracEdit) {		// POSTAVU DrD EDITUJE vlastnik postavy = hrac
			echo make_row("Jméno",inputize("jmeno_edit",40,$postava->jmeno),false);
			echo "		<tr><td><h4>Ikonka&nbsp;:</h4><span class='hico'>".(strlen($postava->ico)>3?"<img src='http://s1.aragorn.cz/i/$postava->ico' />":"<img src='http://s1.aragorn.cz/i/default.jpg' />")."</span></td><td><input type='file' name='ico' /></td></tr>\n";
			$arrPres = array("dobro - zákonné","dobro - zmatené","neutrální","zlo - zmatené","zlo - zákonné");
			$parts = "";
			for ($i=0;$i<count($arrPres);$i++) {
				$parts .= "<option";
				if ($i == $postava->presvedceni) $parts .= " selected";
				$parts .= " value='$i'>$arrPres[$i]</option>";
			}
			$postava->presvedceni = "<select name=\"presvedceni_edit\">$parts</select>";
			echo make_row("Přesvědčení",$postava->presvedceni,false);
			echo make_row("Popis",textareatize("popis_edit",8,30,$postava->popis),false);
			echo make_row("Životopis",textareatize("zivotopis_edit",14,30,$postava->zivotopis),false);
			echo make_row("Soukromé<br />poznámky",textareatize("poznamky_hrac_edit",14,30,$postava_poznamka->poznamka),false);
		}
		echo "		<tr><td></td><td><input type='submit' value='Editovat'/></td></tr>\n";
		echo "	</table>\n";
	}
	elseif ($povol > 0) {
		echo $vlastLink;
		$arrPres = array("dobro - zákonné","dobro - zmatené","neutrální","zlo - zmatené","zlo - zákonné");
		include "./add/drd-fce.php";
		echo make_row("Jméno",_htmlspec($postava->jmeno));
		echo "		<tr><td><h4>Ikonka&nbsp;:</h4></td><td>".(strlen($postava->ico)>3?"<img src='http://s1.aragorn.cz/i/$postava->ico' />":"<img src='http://s1.aragorn.cz/i/default.jpg' />")."</td></tr>\n";
		echo make_row("Rasa",rasa_postavy($postava->rasa));
		echo make_row("Povolání",(($postava->uroven<=5)?povolani_postavy($postava->povolani):povolani_postavy($postava->povolani)." (".subpovolani_postavy($postava->povolani).")"));
		echo make_row("Úroveň",$postava->uroven);
		echo make_row("Zkušenosti","$postava->xp zk =&gt; ".level_postavy($postava->povolani,$postava->xp).".úroveň");
		echo "		<tr><td><h4>Životy&nbsp;:</h4></td><td>";
			if ($postava->zivoty == $postava->zivoty_max) { echo "<span class='hpositive'>".$postava->zivoty."</span>"; }
			elseif ($postava->zivoty < $postava->zivoty_max/3) { echo "<span class='hnegative'>".$postava->zivoty."</span>"; }
			else { echo $postava->zivoty; }
			echo " / $postava->zivoty_max</td></tr>\n";
		if (floor($postava->povolani/2) > 0 && floor($postava->povolani/2) < 4) {
			echo make_row("Magy","$postava->magy / $postava->magy_max");
		}
		echo "		<tr><td><h4>Přesvědčení&nbsp;:</h4></td><td>".$arrPres[$postava->presvedceni]."</td></tr>\n";
		$atributS = array("sila", "obratnost", "odolnost", "inteligence", "charisma");
		$atributN = array("Síla", "Obratnost", "Odolnost", "Inteligence", "Charisma");
		for ($a=0;$a<count($atributS);$a++) {
			echo "		<tr><td><h4>$atributN[$a]&nbsp;:</h4></td><td>".(($postava->$atributS[$a]<10)?"&nbsp;".$postava->$atributS[$a]:$postava->$atributS[$a])." / ".get_bonus($postava->$atributS[$a],0)."</td></tr>\n";
		}
		echo make_row("Výška","$postava->vyska coulů");
		echo make_row("Váha","$postava->vaha mincí (&plusmn; ".round($postava->vaha/20,1)." kg)");
		echo make_row("Finance",item_cena($postava->penize));
		echo make_row("Schopnosti",nl2br(_htmlspec($postava->schopnosti)),mb_strlen($postava->schopnosti));
		echo make_row("Popis",nl2br(_htmlspec($postava->popis)),mb_strlen($postava->popis));
		echo make_row("Životopis",nl2br(_htmlspec($postava->zivotopis)),mb_strlen($postava->zivotopis));

		include_once "./add/xml_parser_func.php";
		$postava->inventar = inventar($postava->inventar);
		if (mb_strlen($postava->inventar)>2) {
			echo "	</table><a name='inv'></a>\n	$postava->inventar\n";
		}
		else {
			echo "	</table><a name='inv'></a><div class='art'><p>Postava má prázdný inventář.</p></div>\n";
		}
		if (floor($postava->povolani / 2) == 1 || floor($postava->povolani / 2) == 3) {
			if (floor($postava->povolani / 2) == 1) {
				$typKouzel = "h";
				$maxKouzel = 100;
			}
			elseif (floor($postava->povolani / 2) == 3) {
				$maxKouzel = array(0, 3, 5, 7, 9, 11, 14, 17, 20, 23, 26, 30, 34, 38, 42, 46, 51, 56, 61, 66, 71, 77, 83, 89, 95, 101, 108, 115, 122, 129, 136, 144, 152, 160, 168, 178, 5000);
				$maxKouzel = $maxKouzel[$postava->uroven];
				$typKouzel = "k";
			}
			$NotInS = $NotIn = array();
			$formKouzla1 = "\t<form name='kouzleni' method='post' action='/herna/$slink/$sslink/?akce=kouzla&amp;do=add' class='f'>\n\t\t<select name='new_kouzlo'>\n";
			$options = $kouzlas = array();
			if ($postava->kouzla!="") {
				$NotInS = explode(">",$postava->kouzla);
				for ($i=0;$i<count($NotInS);$i++) $NotIn[$NotInS[$i]] = 1;
			}
			$kouzlaSel = mysql_query("SELECT * FROM 3_herna_kouzla WHERE typ = '$typKouzel' ORDER BY nazev ASC");
			while ($kouzlo = mysql_fetch_object($kouzlaSel)) {
				if (isset($NotIn[$kouzlo->id])) {
					$deleteLink = "";
					if ($allow == "pj") $deleteLink = "</td><td><a href='?akce=kouzla&amp;do=del&amp;i=$kouzlo->id' title='smazat kouzlo'>X</a>";
					$kouzlas[] = "\t\t<tr><td><div onmouseover=\"ddrivetip('"._htmlspec($kouzlo->popis)."')\" onmouseout=\"hidedrivetip();\">"._htmlspec($kouzlo->nazev)."</div>$deleteLink</td></tr>\n";
				}
				else $options[] = "<option value='$kouzlo->id'>"._htmlspec($kouzlo->nazev)."</option>";
			}
			$formKouzla2 = "\n\t\t</select>\n\t\t<input type='submit' class='button' value='Přidat nové kouzlo' />\n\t</form>\n";

			if (count($options)>0 && count($kouzlas)<$maxKouzel) {
				echo "\t<table class='edttbl text'>\n";
				echo $formKouzla1."\t\t".join("",$options).$formKouzla2;
				echo "\t</table>\n";
			}
			if (mb_strlen($postava->kouzla)>0) {
				echo "\t<table class='text'>\n";
				echo join("",$kouzlas);
				echo "\t</table>\n";
			}
			else echo "	<div class='art'><p>Postava nemá žádná kouzla.</p></div>\n";
		}
	}
	else {
		echo $vlastLink;
		echo "		<tr><td><h4>Jméno&nbsp;:</h4></td><td>"._htmlspec($postava->jmeno)."</td></tr>
		<tr><td><h4>Ikonka&nbsp;:</h4></td><td>";
		if (strlen($postava->ico) > 3) {
			echo "<img src='http://s1.aragorn.cz/i/$postava->ico' />";
		} else {
			echo "<img src='http://s1.aragorn.cz/i/default.jpg' />";
		}
		echo "</td></tr>
		<tr><td><h4>Popis&nbsp;:<h4></td><td><p>".nl2br(_htmlspec($postava->popis))."</p></td></tr>
	</table>\n";
	}
}
echo "	$formEnd</div><div class='highlight-bot'></div>\n";

?>