<?php
if ($LogedIn) {
	if (herna_omezeni($_SESSION['uid'],$_SESSION['lvl']) >= $herna_nebonus) {
		echo "<h2 class=\"h2-head\"><a href=\"/herna/\" title=\"$titleHerna\">$titleHerna</a></h2><h3><a href='/herna/new/' title='Nová jeskyně'>Herna - nová jeskyně</a></h3>
	<p class='submenu'><a href='/herna/' class='permalink' title='Zpět na výpis jeskyní'>Zpět do herny</a></p>
	<div class='art'>
	<p>Již jste aktivní ve $herna_nebonus jeskyních. Systém <acronym title='Online herna RPG (Drd, Vampire a jiné)' xml:lang='cs'>Aragornu</acronym> nepovoluje být hráčem či Pánem jeskyně ve více
	než $herna_nebonus jeskyních. Toto je čistě z&nbsp;<acronym title='Bylo zjištěno, že průměrný hráč kvalitně hraje ".($herna_nebonus-1)." až $herna_nebonus hry.' xml:lang='cs'>kvalitativního hlediska</acronym>.</p>
	<p>Pro&nbsp;založení nové jeskyně nebo registraci do&nbsp;dalšího dobrodružství si zařiďte <acronym title='Placená verze Aragornu (nadstandardní funkce, získání hvězdy)' xml:lang='cs'>bonus</acronym> na serveru <acronym title='Online herna RPG (Drd, Vampire a jiné)' xml:lang='cs'>Aragorn.cz</acronym></p>
	<p>Učinit tak můžete v <a href='/nastaveni/systemove/' title='Přímý odkaz do Nastavení' class='permalink2'>Systémovém nastavení</a></p>
	</div>\n";
	}
	else {
	?>
<script language='javascript' type='text/javascript'>
function sender(f,idcko){
	var i,a,c,o,p,s,t,y,cnfm,end,nah,ver,ver2,ver_count,vynechej=false;
	end=0;
	s=document.getElementById(idcko).elements;
	ver=new Array("cave_nazev","cave_hraci","cave_hleda","cave_popis","cave_adminy");
	ver2=new Array("Název jeskyně","Počet hráčů","Jaké hráče hledám","Popis jeskyně","Popis pro adminy");
	ver_count=ver.length;
	for(i=0;i<ver_count;i++){
		if(s[ver[i]].value.length==0&&end<1){
			alert("Nebylo vyplněno pole "+ver2[i]);
			s[ver[i]].focus();
			end=1;
			return false;
		}
	}
	if(s["cave_nazev"].value.length<5){
		alert("Název jeskyně musí mít alespoň 5 znaků.");
		s["cave_nazev"].focus();
		return false;
	}
	if(ukaz){
		alert("Musíte vybrat herní systém.");
		s["cave_system"].focus();
		return false;
	}
	if(isNaN(s["cave_hraci"].value)){
		alert("Počet hráčů musí být číslo.");
		s["cave_hraci"].focus();
		return false;
	}
	if(s["cave_hraci"].value>15 || s["cave_hraci"].value<1){
		alert("Počet hráčů může být maximálně 15 a minimálně 1.");
		s["cave_hraci"].focus();
		return false;
	}

	if(s["cave_system"].options[s["cave_system"].selectedIndex].value=="drd"){
	}else{
		if(document.getElementById("fieldsets").getElementsByTagName("INPUT").length==0){
			var cnfm=confirm("Opravdu chcete založit jeskyni v systému ORP bez vlastních políček?");
			if(cnfm!=true){
				document.getElementById("cave-starter").firstChild.focus();
				return false;
			}
		}
		for(a=0;a<s.length;a++){
			if(s[a].tagName.toUpperCase() == "SELECT" && s[a].name.substr(0,7) != "typ_orp" && s[a].name != "cave_system" && s[a].hasChildNodes()){
				o=s[a].childNodes;
				t="";
				for(i=0;i<o.length;i++){
					if(i>0){
						t+=",";
					}
					t += encodeURIComponent($(o[i]).get('text'));
				}
				p=s[a];
				while(p.tagName.toUpperCase() != "DIV" && p.parentNode){
					p=p.parentNode;
				}
				y=p.getElementsByTagName("INPUT");
				y[1].value=t;
			}
			else if(s[a].tagName.toUpperCase() == "SELECT" && s[a].name.substr(0,7) != "typ_orp" && !s[a].hasChildNodes()){
				alert("Pro typ 'výběr z možností' musíte vytvořit nějaké možnosti.");
				s[a].focus();
				return false;
			}else if(s[a].tagName.toUpperCase() == "SELECT" && s[a].name.substr(0,7) == "typ_orp" && (s[a].value == "r" || s[a].value == "n")){
				p=s[a];
				while(p.tagName.toUpperCase() != "DIV" && p.parentNode){
					p=p.parentNode;
				}
				y=p.getElementsByTagName("INPUT");
				if(y.length==6){
					if(isNaN(y[4].value) || isNaN(y[5].value) || y[4].value.length == 0 || y[5].value.length == 0){
						nah="";
						if(s[a].value == "r"){
							nah="náhodné ";
						}
						alert("Pro typ '"+nah+"číslo' musíte zadat číselně dolní a horní mez.");
						if(isNaN(y[4].value) || y[4].value.length == 0){
							y[4].focus();
						}
						else{
							y[5].focus();
						}
						return false;
					}
					else{
						if(parseInt(y[4].value,10) > parseInt(y[5].value,10)){
							y[1].value = y[4].value;
							y[4].value = y[5].value;
							y[5].value = y[1].value;
						}
						y[1].value = y[4].value+':'+y[5].value;
					}
				}
				else{
					alert("Pro typ políčka 'náhodné číslo' musíte zadat číselně horní a dolní mez.");
					return false;
				}
			}
			else if(s[a].tagName.toUpperCase() == "INPUT" && s[a].name.substr(0,9) == "nazev_orp" && s[a].value == ""){
				vynechej=true;
			}
		}
		if(vynechej){
			cnfm=confirm("Chcete odeslat i s nevyplněnými položkami?");
			if(cnfm!=true){
				$("cave-starter").getFirst().focus();
				return false;
			}
		}
	}
	return checkForNew('herna','cave_nazev','cave_nazev',true);
}
function changeTyp(p,n){
	var a=p,sel1,sel2,sels,inp;
	while(p.tagName.toUpperCase() != "DIV" && p.parentNode){
		p=p.parentNode;
	}
	inp=p.getElementsByTagName("INPUT");
	switch(a.value){
		case "t":
		case "a":
			sels=p.getElementsByTagName("SPAN");
			if(!sels || sels.length<1){
			}
			else if(sels.length==2){
				p.removeChild(p.lastChild);
				p.removeChild(p.lastChild);
			}
			else{
				p.removeChild(p.lastChild);
			}
			break;
		case "n":
		case "r":
			sels=p.getElementsByTagName("SPAN") || false;
			if(!sels || sels.length<1){
			}
			else if(sels.length==1){
				p.removeChild(p.lastChild);
			}
			else if(sels.length==2){
				return false;
			}
			sel1=document.createElement("SPAN");
			sel1.innerHTML=" | <input style='display:inline;width:50px' size='5' maxlength='5' type='text' name='number_low["+n+"]' value='min' /> &lt;";
			p.appendChild(sel1);
			sel2=document.createElement("SPAN");
			sel2.innerHTML=" x &lt; <input style='display:inline;width:50px' size='5' type='text' maxlength='5' name='number_up["+n+"]' value='max' />";
			p.appendChild(sel2);
		break;
		case "s":
			sels = p.getElementsByTagName("SPAN") || false;
			if(!sels || sels.length<1){
			}
			else if(sels.length==2){
				p.removeChild(p.lastChild);
				p.removeChild(p.lastChild);
			}
			else{
				return false;
			}
			var sel1=document.createElement("SPAN");
			sel1.innerHTML="| <select style='display:inline;width:90px' name='optiones["+n+"]'><"+"/select> | <a href='#' onclick='return optionize(this,true)'>+<"+"/a> / <a href='#' onclick='return optionize(this,false)'>&ndash;<"+"/a>";
			p.appendChild(sel1);
		break;
	}
	return false;
}
function optionize(s,ins){
	var a,e;
	while(s.tagName.toUpperCase() != "SPAN" && s.parentNode){
		s=s.parentNode;
	}
	s = $(s).getElement("select");
	if(ins){
		e=prompt('Nová možnost');
		if(!e){
			return false;
		}
		a=new Element("option",{text:e,value:e});
		s.adopt(a);
	}
	else{
		if(s.hasChildNodes()){
			e=s.options[s.selectedIndex];
			s=s.removeChild(e);
		}
	}
	return false;
}
function add_input(f){
	var s,c,odkaz,aa,stat;
	s=$("fieldsets");
	if(s.hasChildNodes()){
		c=s.childNodes.length;
	}else{
		c=0;
	}
	if (c>=15){
		alert("Maximum je 15 volitelných položek.");
		return false;
	}
	odkaz=f;
	aa=new Element("DIV");
	stat="<option value=''>- typ -<"+"/option><option value='n'>číslo<"+"/option><option value='r'>náhodné číslo<"+"/option><option value='t'>krátký text<"+"/option><option value='a'>větší text<"+"/option><option value='s'>výběr z možností<"+"/option>";
	aa.innerHTML="<input type='text' name='nazev_orp["+c+"]' size='10' style='display:inline;width:100px;' /><input type='hidden' name='helping["+c+"]' /> | <select name='typ_orp["+c+"]' style='display:inline;width:100px;' onchange='changeTyp(this,"+c+")'>"+stat+"<"+"/select> | <input type='checkbox' style='display:inline;width:auto' name='view["+c+"]' value='a' checked='checked' id='view["+c+"]' /><label style='display:inline;width:auto' for='view["+c+"]'>veřejně<"+"/label>| <input style='display:inline;width:auto' type='checkbox' name='edit["+c+"]' value='a' checked='checked' id='edit["+c+"]' /><label style='display:inline;width:auto' for='edit["+c+"]'>úpravy<"+"/label>";
	aa.injectBottom(s);
	odkaz.focus();
	return false;
}
function hide_me(selObj){sh_me=selObj.options[selObj.selectedIndex].value;var dly=document.getElementById('drd-layer');var oly=document.getElementById('orp-layer');if(sh_me=="orp"){oly.style.display="block";dly.style.display="none";ukaz=false;}else if(sh_me=="drd"){oly.style.display="none";dly.style.display="block";ukaz=false;}else{oly.style.display="none";dly.style.display="none";ukaz=true;}}
var ukaz=true;
</script><?php
echo "
<h2 class=\"h2-head\"><a href=\"/herna/\" title=\"$titleHerna\">$titleHerna</a></h2><h3><a href='/herna/new/' title='Nová jeskyně'>Herna - nová jeskyně</a></h3>
	<p class='submenu'><a href='/herna/' class='permalink' title='Zpět na výpis jeskyní'>Zpět do herny</a></p>\n";
if (isSet($error)) {
	switch ($error) {
		case 1:
			info("Již jste aktivní v $herna_nebonus jeskyních. Systém Aragornu nedovoluje nebonusovým uživatelům hrát ve více jeskyních. V Systémovém nastavení nebo Nápovědě se dozvíte více.");
		break;
		case 2:
			info("Musíte vybrat herní systém z možností.");
		break;
		case 3:
			info ("Název je příliš krátký. Minimální délka názvu jeskyně je <strong class='warning'>5 písmenných znaků</strong>.");
		break;
		case 4:
			info("Již existuje jeskyně s <acronym title='Přesněji, jeho SEO verze již v herně existuje.' xml:lang='cs'>podobným názvem</acronym>. Doporučujeme ho nějak pozměnit.");
		break;
		case 5:
			info ("Název jeskyně byl příliš dlouhý. Nejvyšší povolená délka je 40 znaků.");
		break;
		case 6:
			info("Popis jeskyně musí obsahovat nějaký text.");
		break;
		case 7:
			info("Popis pro adminy musí obsahovat nějaký text.");
		break;
		case 8:
			info("Text Jaké hráče hledám musí obsahovat nějaký text.");
		break;
		case 9:
			info("Maximální počet hráčů musí být číslo od 1 do 15.");
		break;
	}
}
?>	<div class='f-top'></div>
<div class='f-middle'>
	<form action="/herna/new/?akce=herna-new" class="f" name="form_for_new" id="form_for_new" method="post" onsubmit="return sender(this,'form_for_new');" enctype="multipart/form-data">
	<fieldset>
		<legend>Odeslání jeskyně ke schválení</legend>
		<div><label for="cave_nazev"><span>Název jeskyně</span><input id="cave_nazev" type="text" name="cave_nazev" maxlength="30" /></label></div>
		<div><label for="cave_system"><span>Herní systém</span><select id="cave_system" name="cave_system" onblur="hide_me(this)" onchange="hide_me(this)">
		<option value="">- - - - -</option><option value="drd">Dračí Doupě</option><option value="" disabled>- - - - -</option><option value="orp">Open Role Play</option>
		</select></label></div>
		<div class="hvyber">
			<div id="orp-layer" style="display:none;">
				<h4>Systém Open Role Play</h4>
				<p>Mám vlastní představu o pravidlech a světě, ale nikde na internetu neexistuje nic podobného. Nebo ještě lépe na pravidla vlastně vůbec hrát nechci. Od toho je tu ORP - Volný Hrací Systém. DrD+, GURPS, Shadowrun, Stín meče, Vampire, Warhammer a mnoho dalších systémů v jednom. Nic se sice samo nepočítá, ale mohu si i již existující systém upravit dle svého. Není nad volnou ruku. Budu mít jeskyni v systému ORP.</p>
				<p>Tento vlastně neurčený, volný herní systém pracuje s předem neurčenou podobou osobních deníků postav, kdy PJ sám určí, jaké a kolik položek v něm chce mít. U každé postavy jsou <em>napevno</em> pole:</p>
					<ul>
						<li>Jméno postavy (text)</li>
						<li>Atributy (text s odstavci)</li>
						<li>Kouzla a schopnosti (text s odstavci)</li>
						<li>Životopis (text s odstavci)</li>
						<li>Popis postavy (text s odstavci)</li>
						<li>Inventář (text s odstavci)</li>
					</ul>
				<p>U každého pole můžete určit, jakého typu bude. Buď jednoduché políčko na kratší text (názvy, pojmy),
				číslo (fyzikální veličiny), náhodné číslo (generované systémem - prvek náhody),
				formátovaný text s odstavci (delší texty) nebo výběr z možností (od ras, přes povolání až po výběr barvy vlasů či očí).</p>
				<p>Navíc u každé vlastní položky určujete také další dvě vlastnosti. Zaprvé je to její viditelnost, zda má být viditelná (<em>veřejně</em>) nebo jen pro vlastníka postavy a majitele jeskyně (neveřejná).
				Druhá věc je možnost editace položky (<em>úpravy</em>) i hráčem (nezaškrtnutá = jen majitel jeskyně)</p>
				<p id="cave-starter"><strong><a href="#" onclick="return add_input(this)">PŘIDAT DALŠÍ POLOŽKU</a></strong></p>
				<div id="fieldsets"></div>
				<p>Vlastnosti, které necháte prázdné, nebudou použity (pro případ, že jste se překlikli nebo přepočítali).<br />
				Nezapomeňte, že některé typy položek vyžadují ještě další nastavení.</p>

			</div>
			<div id="drd-layer" style="display:none;">
				<h4>Systém Dračí doupě</h4>
				<p>Otevírá se vám brána do&nbsp;světa fantasy plného skřetích doupat a pokladů, lapků bojujících se strážemi, kouzel a pastí.</p>
				<p>Zde můžete změnit postavám celý život v dobrodružný příběh, který se ještě po&nbsp;staletí vyprávěl dětem. Mýtus o&nbsp;silném válečníkovi, zadumaném kouzelníku, divočinou zoceleném hraničáři, všeznalém mistru alchymie nebo lstivém zloději čeká na&nbsp;sepsání.</p>
				<p>Náš herní systém k&nbsp;tomuto systému nabízí elektronické deníky a kostky. Hra může probíhat pomocí fóra (po&nbsp;této možnosti sáhnou hlavně ti časově vytíženější z&nbsp;vás) nebo přes chat (téměř jako naživo), oboje s&nbsp;možností šeptání více lidem.</p>
				<p>Přidejte se k&nbsp;rozsáhlé komunitě hráčů vůbec nejstarší a nejproslulejší hry na&nbsp;hrdiny u&nbsp;nás. Usedněte na&nbsp;pomyslný trůn jakožto Pán jeskyně a formujte svůj vlastní herní svět. Hrajte Dračí Doupě.</p>
			</div>
		</div>
		<div><label for="cave_popis"><span>Popis jeskyně <sup>1</sup></span><textarea id="cave_popis" name="cave_popis" rows="4" cols="40"></textarea></label></div>
		<div><label for="cave_keywords"><span>Klíčová slova</span><input id="cave_keywords" type="text" name="cave_keywords" maxlength="250" /></label></div>
		<div><label for="cave_hraci"><span>Max.počet hráčů</span><input id="cave_hraci" name="cave_hraci" maxlength="2" /></label></div>
		<div><label for="cave_hleda"><span>Jaké hráče hledám <sup>2</sup></span><textarea id="cave_hleda" name="cave_hleda" rows="4" cols="40"></textarea></label></div>
		<div><label for="cave_adminy"><span>Popis pro adminy <sup>3</sup></span><textarea id="cave_adminy" name="cave_adminy" rows="4" cols="40"></textarea></label></div>
		<div class="hvyber">
			<ol>
				<li><small>&nbsp;Pozn. 1: Popis jeskyně slouží hlavně pro ostatní uživatele a zájemce o hru. Měl by obsahovat stručné uvedení do děje, reálií a podobné informace. Stejně tak se hodí připsat větičku, že se hraje přes fórum nebo přes chat či jak jinak.</small></li>
				<li><small>&nbsp;Pozn. 2: Text "Jaké hráče hledám" je důležitý pro ty, kdo hledají jeskyni. Buď nehledám nikoho a jsem domluvený, mám plno, nebo hledám zkušené, nezkušené, denně s 2 hodinami na chat, týdně pro jeden příspěvek. Umělce, vysokoškoláky, humanitně zaměřené hráče ... atd.</small></li>
				<li><small>&nbsp;Pozn. 3: Při schvalování jeskyně, pokud není dostatečný popis / text pro hráče, může tato položka hrát svou důležitou roli. Později je určená pro komunikaci mezi vlastníkem jeskyně a administrátory. Ať už se sem dají napsat věci jako "schvalte mi to, dík :)", Text pro adminy může sloužit i k oznámení, že jeskyně bude mít do x-tého v onom měsíci pozastavenou činnost, čímž částečně předejdete ohrožení smazáním.</small></li>
			</ol>
		</div>
		<input type="submit" class="button" value="Odeslat ke schválení" />
	</fieldset>
	</form>
</div>
<div class="f-bottom"></div>

<?php	}
}
else {
include "zakaz2.php";
echo "<p class='submenu'><a href='/herna/' class='permalink' title='Zpět na výpis jeskyní'>Zpět do herny</a></p>\n";
}
?>
