<?php

echo '<h2 class="h2-head"><a href="/herna/" title="Herna">Herna</a></h2>
<h3><a href="/herna/'.$hItem->nazev_rew.'/reg/">Přihláška do jeskyně</a></h3>
	<p class="submenu"><a href="/herna/" class="permalink" title="Zpět do herny">Zpět do herny</a><span class="hide"> | </span><a class="permalink2" title="Zpět na hlavní stránku jeskyně '.$hItem->nazev.'" href="/herna/'.$hItem->nazev_rew.'/">Zpět do jeskyně</a></p>
';

if (!$LogedIn) {
	info("Tato sekce je vyhrazena registrovaným a přihlášeným uživatelům.");
}
else {
	if (!in_array($hItem->id, herna_omezeni(0,0)) && herna_omezeni($_SESSION['uid'],$_SESSION['lvl']) >= $herna_nebonus) {
		echo '	<div class="art">
	<p>Již jste aktivní ve '.$herna_nebonus.' jeskyních. Systém <acronym title="Online herna RPG (Drd, Vampire a jiné)" xml:lang="cs">Aragornu</acronym> nepovoluje být hráčem či Pánem jeskyně ve více
	než '.$herna_nebonus.' jeskyních. Toto je čistě z&nbsp;<acronym title="Bylo zjištěno, že průměrný hráč kvalitně hraje '.($herna_nebonus-1).' až '.$herna_nebonus.' hry." xml:lang="cs">kvalitativního hlediska</acronym>.</p>
	<p>Pro&nbsp;založení nové jeskyně nebo přihlášení do&nbsp;dalšího dobrodružství si zařiďte <acronym title="Placená verze Aragornu (nadstandardní funkce, získání hvězdy)" xml:lang="cs">bonus</acronym> na serveru <acronym title="Online herna RPG (Drd, Vampire a jiné)" xml:lang="cs">Aragorn.cz</acronym></p>
	<p>Učinit tak můžete v <a href="/nastaveni/systemove/" title="Přímý odkaz do Nastavení" class="permalink2">Systémovém nastavení</a></p>
	</div>
';
	}
	elseif ($_SESSION['uid'] == $hItem->uid) {
		info("Toto je vaše jeskyně. Nemůžete odeslat postavu do vlastní jeskyně.");
	}
	elseif ($uzMamPostavu > 0) {
		info("Do této jeskyně již máte postavu přihlášenou.");
	}
	elseif ($hItem->povolreg == 0) {
		info("Pán jeskyně nepřijímá další přihlášky do jeskyně. Nemůžete odeslat postavu do této jeskyně.");
	}
	elseif (isset($uzivateleVeHre[$_SESSION['uid']])) {
		info("V této jeskyni již aktivní jsi. Jako pomocný PJ.");
	}
	elseif ($hItem->hraci_pocet == $pAktivPlayers) {
		info("V této jeskyni je aktivních hráčů přesně tolik, kolik PJ nastavil. Nemůžete odeslat postavu do této jeskyně.");
	}
	else {
		if ($hItem->typ == 0) {
			if (isSet($error)) {
				switch ($error) {
					case 1:
						infow("Již jste aktivní v $herna_nebonus jeskyních. Systém Aragornu nedovoluje nebonusovým uživatelům hrát ve více jeskyních. V Systémovém nastavení nebo Nápovědě se dozvíte více.");
					break;
					case 2:
						info("Musíte vybrat rasu postavy z nabízených možností.");
					break;
					case 3:
						info ("Jméno postavy je příliš krátké. Minimální délka jména je <strong class='warning'>5 alfanumerických znaků</strong>.");
					break;
					case 4:
						info("<acronym title='Přesněji, postava s SEO verzí jména postavy již v jeskyni existuje.' xml:lang='cs'>Podobné jméno postavy</acronym> již v jeskyni existuje. Doporučujeme ho nějak pozměnit.");
					break;
					case 5:
						info("Jméno postavy je příliš dlouhé. Nejvyšší povolená délka je 40 znaků.");
					break;
					case 6:
						info("Musíte vybrat přesvědčení postavy z nabízených možností.");
					break;
					case 7:
						info("Musíte vybrat povolání postavy z nabízených možností.");
					break;
					case 8:
						info("V této jeskyni již nějak aktivní jsi.");
					break;
				}
			}
		}
		else {
			if (isSet($error)) {
				switch ($error) {
					case 1:
						infow("Již jste aktivní v $herna_nebonus jeskyních. Systém Aragornu nedovoluje nebonusovým uživatelům hrát ve více jeskyních. V Systémovém nastavení nebo Nápovědě se dozvíte více.");
					break;
					case 2:
						info ("Jméno postavy je příliš krátké. Minimální délka jména je <strong class='warning'>5 alfanumerických znaků</strong>.");
					break;
					case 3:
						info("<acronym title='Přesněji, postava s SEO verzí jména postavy již v jeskyni existuje.' xml:lang='cs'>Podobné jméno postavy</acronym> již v jeskyni existuje existuje. Doporučujeme ho nějak pozměnit.");
					break;
					case 4:
						info ("Jméno postavy je příliš dlouhé. Nejvyšší povolená délka je 40 znaků.");
					break;
					case 8:
						info("V této jeskyni již máte postavu.");
					break;
				}
			}
		}

echo '<script type="text/javascript">
function formCheck(fid){
	var f=document.getElementById(fid);
	var ins=document.getElementById(fid).elements;
	for(var pq=0;pq<ins.length;pq++){
	  var inp=ins[pq];
		if (inp.tagName.toUpperCase()=="FIELDSET") {
			continue;
		}
		var p=inp;
		while(p.tagName.toUpperCase()!="LABEL" && p.parentNode)p=p.parentNode;
		var nazev_policka=p.firstChild.innerHTML;
		if(inp.tagName.toUpperCase()=="SELECT"){
			if(inp.selectedIndex==0||inp.options[inp.selectedIndex].value=""){
				alert("Musíte vybrat jednu z možností pro "+nazev_policka+".");
				inp.focus();
				return false;
			}
		}else if(inp.tagName.toUpperCase()=="INPUT"||inp.tagName.toUpperCase()=="TEXTAREA"){
			if (inp.hasAttribute("rel")) {
				if(inp.getAttribute("rel").toString().substr(0,6)=="number"){
					var ar=inp.getAttribute("rel").toString().slice(7,-1);
					ar=ar.split(":");
					ar[0]=parseInt(ar[0]);ar[1]=parseInt(ar[1]);if(ar[0]>ar[1]){ar[2]=ar[0];ar[0]=ar[1];ar[1]=ar[2];}
					if(inp.value.length<1||inp.value<ar[0]||inp.value>ar[1]){
						alert("Políčko "+nazev_policka+" musí být číslo větší než "+ar[0]+" a menší než "+ar[1]+".");
						inp.focus();
						return false;
					}
				}
			}
			if(inp.value==""){
				alert("Musíte vyplnit políčko "+nazev_policka+".");
				inp.focus();
				return false;
			}
		}
	}
	return true;
}
</script>
';
		if ($hItem->typ == 1) {
			$settsS = mysql_query ("SELECT cid,struktura FROM 3_herna_sets_open WHERE cid = '$hItem->id'");
			$orp = array();
			if (mysql_num_rows($settsS)>0) {
				$setts = mysql_fetch_row($settsS);
				$attrs = explode($hCh,$setts[1]);
				foreach($attrs as $nmb => $attr) {
					$att = explode(">",$attr);
					if ($att[1] == "r") continue;
					$orp[$nmb] = array();
					switch ($att[1]) {
						case "t":
						  $orp[$nmb]['typ']="t";
						  $orp[$nmb]['nazev']=$att[0];
						break;
						case "a":
						  $orp[$nmb]['typ']="a";
						  $orp[$nmb]['nazev']=$att[0];
						break;
						case "s":
						  $t = "";
							for ($i=3;$i<count($att);$i++) {
								$t .= "<option>".$att[$i]."</option>";
							}
							$orp[$nmb]['typ']="s";
							$orp[$nmb]['nazev']=$att[0];
							$orp[$nmb]['add']="<option value=''>- - -</option>".$t;
						break;
						case "n":
							$orp[$nmb]['typ']="n";
							$orp[$nmb]['nazev']=$att[0];
							$orp[$nmb]['min']=$att[3];
							$orp[$nmb]['max']=$att[4];
						break;
					}
				}
			}
		}
		echo "	<form action='/herna/$slink/reg/?akce=herna-reg' method='post' class='f' name='new-postava' id='new-postava' onSubmit='return formCheck(\"new-postava\")'>
	<fieldset>
		<legend>Systém ";
		if($hItem->typ==0) echo "DrD";
		else echo "ORP";
		echo ': jeskyně <a href="/herna/'.$slink.'/" class="permalink2" title="Hlavní stránka jeskyně">'.$hItem->nazev.'</a></legend>
';
		if ($hItem->typ == 0) {
			$polePolicek = array("jmeno_postavy", "rasa_postavy", "povolani_postavy", "presvedceni_postavy");
			$poleNazvu = array("Jméno postavy", "Rasa", "Povolání", "Přesvědčení");
			$poleTypu = array("t",
												array("hobit","kudůk","trpaslík","elf","člověk","barbar","kroll"),
												array("válečník bojovník","válečník šermíř","hraničář druid","hraničář chodec","alchymista theurg","alchymista pyrofor","kouzelník mág","kouzelník čaroděj","zloděj lupič","zloděj sicco"),
												array("dobro - zákonné","dobro - zmatené","neutrální","zlo - zmatené","zlo - zákonné"));
			for ($i=0;$i<count($polePolicek);$i++) {
				if ($poleTypu[$i] == "t") echo "		<label for='$polePolicek[$i]'><span>$poleNazvu[$i]</span><input id='$polePolicek[$i]' type='text' name='$polePolicek[$i]' maxlength='35' /></label>\n";
				elseif (is_array($poleTypu[$i])) {
					echo "		<label for='$polePolicek[$i]'><span>$poleNazvu[$i]</span><select id='$polePolicek[$i]' name='$polePolicek[$i]'>
					";
					echo "<option value=''>- - - - -</option>";
					for ($ii=0;$ii<count($poleTypu[$i]);$ii++) {
						echo "<option value='$ii'>".$poleTypu[$i][$ii]."</option>";
					}
					echo "
				</select></label>\n";
				}
			}
		}
		else {
			echo "		<label for='jmeno_postavy'><span>Jméno postavy</span><input id='jmeno_postavy' type='text' name='jmeno_postavy' maxlength='40' /></label>\n";
			foreach($orp as $k=>$v) {
				if ($v['typ'] == "t") {
					echo "		<label for='attr-$k-postavy'><span>".$v['nazev']."</span><input id='attr-$k-postavy' type='text' name='attr-$k-postavy' maxlength='200' /></label>\n";
				}
				elseif ($v['typ'] == "n" || $v['typ'] == "r") {
					echo "		<label for='attr-$k-postavy'><span>".$v['nazev']."</span><input id='attr-$k-postavy' rel='number[".$v['min'].":".$v['max']."]' type='text' name='attr-$k-postavy' maxlength='255' /></label>\n";
				}
				elseif ($v['typ'] == "a") {
					echo "		<label for='attr-$k-postavy'><span>".$v['nazev']."</span><textarea id='attr-$k-postavy' rows='4' name='attr-$k-postavy'></textarea></label>\n";
				}
				elseif ($v['typ'] == "s") {
					echo "		<label for='attr-$k-postavy'><span>".$v['nazev']."</span><select id='attr-$k-postavy' name='attr-$k-postavy'>".$v['add']."</select></label>\n";
				}
			}
			echo "		<label for='atributy_postavy'><span>Atributy</span><textarea rows='4' name='atributy_postavy' id='atributy_postavy'></textarea></label>\n";
			echo "		<label for='specials_postavy'><span>Kouzla a schopnosti</span><textarea rows='4' name='specials_postavy' id='specials_postavy'></textarea></label>\n";
			echo "		<label for='inventar_postavy'><span>Inventář</span><textarea rows='4' name='inventar_postavy' id='inventar_postavy'></textarea></label>\n";
		}
		echo "		<label for='popis_postavy'><span>Popis</span><textarea rows='8' name='popis_postavy' id='popis_postavy'></textarea></label>
		<label for='zivotopis_postavy'><span>Životopis</span><textarea rows='14' name='zivotopis_postavy' id='zivotopis_postavy'></textarea></label>
		<input type='submit' class='button' value='Odeslat ke schválení' />
	</fieldset>
	</form>\n";
	}
}
?>