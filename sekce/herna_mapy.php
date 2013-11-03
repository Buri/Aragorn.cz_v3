<?php

function mapa_menu($id,$nazev,$soubor,$datas="",$size=0) {
	if ($soubor=="js") {
		$datas = explode("|",$datas);
		$rozmerY = $datas[0];
		$rozmerX = $datas[1];
		return "<a target='_blank' title='"._htmlspec(stripslashes($nazev))."' href='/map.php?id=$id'>".stripslashes($nazev)."</a>&nbsp;(rozměry:&nbsp;$rozmerX&times;$rozmerY polí)";
	}
	else {
		$size = round($size/1024,1);
		return "<a target='_blank' title='"._htmlspec(stripslashes($nazev))."' href='/map.php?id=$id'>".stripslashes($nazev)."</a>&nbsp;(velikost:&nbsp;$size&nbsp;kB)";
	}
}

if ($hItem->schvaleno == '1') {
	$zLink = "/herna/$slink/$sslink";
	if (isSet($_GET['ok'])) {
		switch ($_GET['ok']) {
			case "1":
				ok("Mapa byla vpořádku nahrána.");
			break;
			case "2":
				ok("Mapa byla vpořádku upravena.");
			break;
			case "3":
				ok("Mapa byla vpořádku smazána.");
			break;
		}
	}
	elseif (isSet($_GET['error'])) {
		switch ($_GET['error']) {
			case "1":
				info("Mapa musí mít název a musí být obrázek ve formátu GIF, JPG nebo PNG.");
			break;
			case "2":
				info("Maximální velikost jedné mapy je 200kB.");
			break;
			case "3":
				info("Celková velikost map v jeskyni může být maximálně 1MB.");
			break;
		}
	}

	$mapySrc = mysql_query("SELECT * FROM 3_herna_maps WHERE cid = '$hItem->id' ORDER BY nazev ASC");

echo "<div class='highlight-top'></div>
<div class='highlight-mid'>\n";

	if (mysql_num_rows($mapySrc)<1) {
		echo "<p class='art text t-a-c'>V jeskyni nejsou žádné mapy</p>\n";
	}
	else {
		echo "<table class='diskuze-one'><tbody>\n";
		$counter = 0;
		if ($allow == "pj" || $allowsPJ['mapy']) {
			function mapa_edit($id,$js) {
				global $slink,$sslink;
				if ($js=="js") {
					return "<td><a href='/map.php?do=edit&amp;id=$id' title='Upravit mapu v MapEditoru' target='_blank'>upravit</a> | <a href='/herna/$slink/$sslink/?akce=map&amp;do=del&amp;id=$id'>smazat</a></td>";
				}
				else {
//					return "<td><a href='/herna/$slink/$sslink/?do=edit&amp;map=$id' title='Upravit mapu'>upravit</a> | <a href='/herna/$slink/$sslink/?akce=map&amp;do=del&amp;id=$id'>smazat</a></td>";
					return "<td><a href='/herna/$slink/$sslink/?akce=map&amp;do=del&amp;id=$id'>smazat</a></td>";
				}
			}
		}
		elseif ($allow == "hrac") {
			function mapa_edit($id,$js) {
				global $slink,$sslink;
				if ($js=="js") {
					return "<td><a href='/map.php?do=edit&amp;id=$id' title='Upravit mapu v MapEditoru' target='_blank'>upravit</a> | <a href='/herna/$slink/$sslink/?akce=map&amp;do=del&amp;id=$id'>smazat</a></td>";
				}
				else {
					return "";
				}
			}
		}
		else {
			function mapa_edit($id,$js) {
				return "";
			}
		}
		while($mapa = mysql_fetch_object($mapySrc)) {
			$counter++;
			echo "<tr><td>".mapa_menu($mapa->id,$mapa->nazev,$mapa->soubor,$mapa->datas,$mapa->size)."</td>".mapa_edit($mapa->id,$mapa->soubor)."</tr>";
		}
		echo "</tbody></table>\n";
	}

echo "</div><div class='highlight-bot'></div>\n";

if ($allow == "pj" || $allow == "hrac" || $allowsPJ['mapy']) {
	if ($allow == "pj" || $allowsPJ['mapy']) {
		echo "
<div class='f-top'></div>
<div class='f-middle'>
<form action='$zLink/?akce=map&amp;do=upload' method='post' class='f' enctype='multipart/form-data'>
	<fieldset>
	<legend>Nahrání nové mapy (obrázku)</legend>
	<label><span>Název mapy</span><input type='text' name='nazev_mapy_img' maxlength='30' /></label>
	max.velikost je 200kB na jeden obrázek
	<label><span>Obrázek</span><input type='file' name='map' /></label>
	<input class='button' type='submit' value='Nahrát' />
	<ol>
		<li><small>Pokud volné místo na Aragorn.cz pro nahrání vaší mapy nestačí, je dobrou volbou použít pro <strong>obrázky</strong> server <a href='http://www.imageshack.us' target='_blank'>ImageShack.us</a> a pak na jeskynní Nástěnku připsat odkaz.</small></li>
		<li><small>Co se týče jiných <strong>dokumentů nebo tabulek</strong> - ideálním místem jsou <em>uložiště na internetu</em> zdarma. Ty sice poskytují časově omezený prostor (většinou mažou takové soubory po 30 dnech od posledního stažení), ale jsou rychlé a bezproblémové. Zde přikladem výborný server <a href='http://www.uloz.to' target='_blank'>Ulož.to</a></small></li>
	</ol>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>\n";
	}
	echo "<div id='map-maker-div'></div>\n";
?>

<script type='text/javascript' language='Javascript'>
ukaz = true;
function surface(obj) {
	sh_me = obj.options[obj.selectedIndex].value;
	var chodby = document.getElementById('map_chodby-layer');
	var imge_s = "<img src='/system/lay/"; var imge_e = ".gif' />";
	if (sh_me == "map_chodby" || sh_me == "map_world") { chodby.style.display = "block";chodby.innerHTML = imge_s+sh_me+imge_e; ukaz=false;}
	else {chodby.style.display = "none";chodby.innerHTML = ""; ukaz=true; }
}
function map_form_check() {
	if (document.forms["form-4-mapeditor"]["nazev_mapy_js"].value.length < 2){
		alert ("Musíte vyplnit název mapy (nejméně dva znaky)");
		document.forms["form-4-mapeditor"]["nazev_mapy_js"].focus();
		return false;
	}
	if (ukaz) {
		alert ("Musíte vybrat typ povrchu mapy.");
		document.forms["form-4-mapeditor"]["povrch_typ"].focus();
		return false;
	}
	else {
		return true;
	}
}

divv = document.getElementById("map-maker-div");
divv.innerText = "ahoj";
divv.innerHTML = '<div class="f-top"></div><div class="f-middle"><form action="/map.php?do=new&amp;typ=js&amp;cave=<?php echo $slink;?>" target="_blank" method="post" class="f" id="form-4-mapeditor" name="form-4-mapeditor" onsubmit="return map_form_check()"><fieldset><legend>Vytvoření nové mapy pomocí MapEditoru</legend><label><span>Název mapy</span><input type="text" name="nazev_mapy_js" maxlength="30" /></label><label><span>Typ povrchu</span><select name="povrch_typ" onblur="surface(this)" onchange="surface(this)"><option>- - - - -</option><option value="map_chodby">Jeskyně - chodby</option><option value="map_world">Svět - cesty / tráva</option></select></label><div id="map_chodby-layer" style="display:none; padding: 5px; height: 30px;"></div><input class="button" type="submit" value="Pokračovat" /></fieldset></form></div><div class="f-bottom"></div>';
</script>
<?php
	}
}

?>
