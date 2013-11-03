<?php
	
$menuLinks['doplnky'] = "Doplňky";
$requireRights['doplnky'] = true;

if (!function_exists("item_cena")){
	function item_cena($prize) {
		$zl = floor($prize);
		$st = floor(($prize-$zl)*10);
		$md = round(($prize-$zl-0.1*$st)*100);
		return "$zl zl $st st $md md";
	}
}

if (!function_exists("arrayCena")){
	function arrayCena($cena) {
		$zl = floor($cena);
		$st = floor(($cena-$zl)*10);
		$md = floor(($cena-$zl-$st*0.1)*100);
		return array($zl,$st,$md);
	}
}

function doplnky_head($rub) {

	$info = 1;

	switch ($_GET['op']) {
		//editace doplnku
		case 1:
			if (isset($_GET['id']) && isset($_POST['i_nazev'],$_POST['i_popis'],$_POST['i_typ'],$_POST['i_cena_zl'],$_POST['i_cena_st'],$_POST['i_cena_md'])) {
				$iid = addslashes($_GET['id']);
				$sIt = mysql_query("SELECT * FROM 3_herna_items WHERE id = '$iid'");
				if ($sIt && mysql_num_rows($sIt)>0) {
					$oI = mysql_fetch_object($sIt);
					if ($oI->typ != $_POST['i_typ']) $info = 3;
					else {
						switch ($oI->typ) {
							case "s":
							case "w":
								$typ = $oI->typ;
								$attrCisla = array("i_sz","i_oz","i_utoc","i_hands");
							break;
							case "z":
							case "a":
								$typ = "z";
								$attrCisla = array("i_kz","i_hands");
							break;
							case "i":
								$typ = "i";
								$attrCisla = array();
							break;
						}
						for ($i=0;$i<count($attrCisla);$i++) {
							if (is_numeric($_POST[$attrCisla[$i]]) && $info==1) {
								if ($_POST[$attrCisla[$i]] > 100 || $_POST[$attrCisla[$i]] < -100 ) {
									$info = 8;
									break;
								}
								else $$attrCisla[$i] = intval($_POST[$attrCisla[$i]]);
							}
							else {
								$info = 8;
								break;
							}
						}
						if ($info == 1) {
							$nazev = addslashes($_POST['i_nazev']);
							$popis = addslashes(_htmlspec($_POST['i_popis']));
							$cena = round(intval($_POST['i_cena_zl'])+intval($_POST['i_cena_st'])*0.1+intval($_POST['i_cena_md'])*0.01,2);
							switch ($oI->typ) {
								case "s":
								case "w":
									$sqlP = " nazev = '$nazev', popis = '$popis',  sila = '$i_sz', obrana = '$i_oz', oprava = '$i_utoc', hands = '$i_hands', typ = '$typ', cena = '$cena'";
								break;
								break;
								case "z":
								case "a":
									$sqlP = " nazev = '$nazev', popis = '$popis', sila = '$i_kz', obrana = '$i_kz', hands = '$i_hands', typ = '$typ', cena = '$cena'";
								break;
								case "i":
									$sqlP = " nazev = '$nazev', popis = '$popis', typ = '$typ', cena = '$cena'";
								break;
							}
							$sql = "UPDATE 3_herna_items SET ".$sqlP." WHERE id = '$iid'";
							mysql_query($sql);
							if (mysql_affected_rows()>0) $info = 2;
							else $info = 3;
						}
					}
				}
				else $info = 4;
			}
		break;
		//zalozeni doplnku
		case 2:
			if (isset($_POST['i_nazev'],$_POST['i_popis'],$_POST['i_typ'],$_POST['i_cena_zl'],$_POST['i_cena_st'],$_POST['i_cena_md'])) {
				switch ($_POST['i_typ']) {
					case "s":
					case "w":
						$typ = $_POST['i_typ'];
						$attrCisla = array("i_sz","i_oz","i_utoc","i_hands");
					break;
					case "z":
					case "a":
						$typ = "z";
						$attrCisla = array("i_kz","i_hands");
					break;
					case "i":
						$typ = "i";
						$attrCisla = array();
					break;
					default:
						$info = 6;
					break;
				}
				for ($i=0;$i<count($attrCisla);$i++) {
					if (is_numeric($_POST[$attrCisla[$i]]) && $info==1) {
						if ($_POST[$attrCisla[$i]] > 100 || $_POST[$attrCisla[$i]] < -100 ) {
							$info = 8;
							break;
						}
						else $$attrCisla[$i] = intval($_POST[$attrCisla[$i]]);
					}
					else {
						$info = 8;
						break;
					}
				}
				if ($info == 1) {
					$nazev = addslashes($_POST['i_nazev']);
					$popis = addslashes($_POST['i_popis']);
					$cena = round(intval($_POST['i_cena_zl'])+intval($_POST['i_cena_st'])*0.1+intval($_POST['i_cena_md'])*0.01,2);
					switch ($typ) {
						case "s":
						case "w":
							$sqlA = "nazev, popis, sila, obrana, oprava, hands, typ, cena";
							$sqlP = " '$nazev', '$popis', '$i_sz', '$i_oz', '$i_utoc', '$i_hands', '$typ', '$cena'";
						break;
						break;
						case "z":
						case "a":
							$sqlA = "nazev, popis, sila, obrana, hands, typ, cena";
							$sqlP = " '$nazev', '$popis', '$i_kz', '$i_kz', '$i_hands', '$typ', '$cena'";
						break;
						case "i":
							$sqlA = "nazev, popis, typ, cena";
							$sqlP = " '$nazev', '$popis', '$typ', '$cena'";
						break;
						default:
							$info = 6;
					}
					if ($info == 1) {
						$sql = "INSERT INTO 3_herna_items ($sqlA) VALUES ($sqlP)";
						mysql_query($sql);
						if (mysql_affected_rows()>0) $info = "5&id=".mysql_insert_id();
						else $info = 6;
					}
				}
			}
			else $info = 4;
			exit;
		break;
	}
//	Header ("Location: /rs/$rub/?info=$info");
//	exit;
}

function doplnky_body(){

$doPages = false;
$typyItemu = array("i"=>"Vybavení","z"=>"Zbroje a štíty","a"=>"Zbroje a štíty","w"=>"Zbraně pro boj tváří v tvář","s"=>"Střelné a vrhací zbraně");

switch($_GET['info']){
	case 1:
		echo "<span class='error'>Chyba: Chybná administrace doplňku</span>";
	break;
	case 2:
		echo "<span class='ok'>Ok: Doplněk v pořádku editován</span>";
	break;
	case 3:
		echo "<span class='error'>Chyba: Jeden či více atributů bylo chybných</span>";
	break;
	case 4:
		echo "<span class='error'>Chyba: Doplněk nenalezen</span>";
	break;
	case 5:
		echo "<span class='ok'>Ok: Nový doplněk vytvořen</span>";
	break;
	case 6:
		echo "<span class='error'>Chyba: Nový doplněk nebyl vytvořen</span>";
	break;
	case 8:
		echo "<span class='error'>Chyba: Atributy u doplňku musí být čísla od -100 do 100</span>";
	break;

}

if ($_GET['id'] > 0){
$fId = mysql_query ("SELECT * FROM 3_herna_items WHERE id = $_GET[id]");

if (mysql_num_rows($fId) > 0){

$oI = mysql_fetch_object($fId);

switch ($_GET['action']){

	case "view":
		echo "<h2>Prohlédnout věc</h2>";
		echo "<form method='post' action='?action=view&amp;id=$oI->id' name='prohlizeni_predmetu'>\n";
		echo "<table width='80%' class='autolayout'>\n";
		switch ($oI->typ) {
			case "z":
			case "a":
				$oI->typ = "z";
				echo "<tr><td>";
				if ($oI->hands == 0) echo "Zbroj";
				else echo "Štít (jednoruční)";
				echo ":</td><td>".stripslashes($oI->nazev)."</td></tr>\n";
				echo "<tr><td><acronym title='Kvalita zbroje'>KZ</acronym>:</td><td>".$oI->sila."</td></tr>\n";
			break;
			case "s":
			case "w":
					if ($oI->typ == "w") $inmess = "Na blízko";
					else $inmess = "Střelná";
				echo "<tr><td>Zbraň:</td><td>".stripslashes($oI->nazev)."</td></tr>\n";
				echo "<tr><td><acronym title='Síla zbraně'>SZ</acronym>:</td><td>".$oI->sila."</td></tr>\n";
					$oI->oprava = intval($oI->oprava);
					$oI->oprava = $oI->oprava>0? "+".$oI->oprava:$oI->oprava;
				echo "<tr><td><acronym title='Útočnost zbraně'>Útoč.</acronym>:</td><td>".$oI->oprava."</td></tr>\n";
				echo "<tr><td><acronym title='Obrana zbraně'>OZ</acronym>:</td><td>".$oI->obrana."</td></tr>\n";
				echo "<tr><td>".$inmess.":</td><td>";
					if ($oI->hands == 1) echo "Jednoruční";
					else echo "Obouruční";
				echo "</td></tr>\n";
			break;
			case "i":
				echo "<tr><td>Předmět:</td><td>".stripslashes($oI->nazev)."</td></tr>\n";
			break;
		}
		echo "<tr><td width='20%'>Popis:</td><td><p>".nl2br(stripslashes($oI->popis))."</p></td></tr>\n";
		echo "<tr><td>Cena:</td><td><p>".item_cena($oI->cena)."</p></td></tr>\n";
		echo "<tr><td colspan='2' align='center'><input type='button' value='Zavřít' onClick=\"window.location.href='/rs/doplnky/'\" /></td></tr>";
		echo "</table></form>\n";
	break;

	case "edit":
	
		echo "<h2>Editace</h2>";

		echo "<form method='post' action='?action=view&amp;op=1&amp;id=$oI->id' name='editace_predmetu'><table width='80%' class='autolayout'>\n";
		switch ($oI->typ) {
			case "z":
			case "a":
				$oI->typ = "z";
				echo "<tr><td>Název zbroje/štítu:</td><td><input type='text' value='".stripslashes($oI->nazev)."' name='i_nazev' /></td></tr>\n";
				echo "<tr><td></td><td><select name='i_hands'><option value='0'";
					if ($oI->hands == 0) echo " selected>Zbroj</option><option value='1'";
					else echo ">Zbroj</option><option value='1' selected";
				echo ">Štít (jednoruční)</option></select></td></tr>\n";
				echo "<tr><td><acronym title='Kvalita zbroje'>KZ</acronym>:</td><td><input class='sinp' type='text' value='".stripslashes($oI->obrana)."' maxlength='3' size='3' name='i_kz' /></td></tr>\n";
			break;
			case "i":
				echo "<tr><td>Název předmětu:</td><td><input type='text' value='".stripslashes($oI->nazev)."' name='i_nazev' /></td></tr>\n";
			break;
			case "w":
			case "s":
				echo "<tr><td>Název zbraně:</td><td><input type='text' value='".stripslashes($oI->nazev)."' name='i_nazev' /></td></tr>\n";
				echo "<tr><td><acronym title='Síla zbraně'>SZ</acronym>:</td><td><input type='text' class='sinp' value='".$oI->sila."' maxlength='3' size='3' name='i_sz' /></td></tr>\n";
				$oI->oprava = intval($oI->oprava);
				$oI->oprava = $oI->oprava>0? "+".$oI->oprava:$oI->oprava;
				echo "<tr><td><acronym title='Útočnost zbraně'>Útoč.</acronym>:</td><td><input class='sinp' type='text' value='".$oI->oprava."' maxlength='3' size='3' name='i_utoc' /></td></tr>\n";
				echo "<tr><td><acronym title='Obrana zbraně'>OZ</acronym>:</td><td><input type='text' class='sinp' value='".$oI->obrana."' maxlength='3' size='3' name='i_oz' /></td></tr>\n";
					if ($oI->typ == "w") $inmess = "Na blízko:";
					else $inmess = "Střelná:";
				echo "<tr><td>$inmess</td><td><select name='i_hands'><option value='1'";
					if ($oI->hands == 1) echo " selected>Jednoruční</option><option value='2'";
					else echo ">Jednoruční</option><option value='2' selected";
				echo ">Obouruční</option></select></td></tr>\n";
			break;
		}
		echo "<tr><td width='20%'>Popis:</td><td><textarea name='i_popis' rows='8' cols='60'>".stripslashes($oI->popis)."</textarea></td></tr>\n";
		$Cena = arrayCena($oI->cena);
		echo "<tr><td>Cena:</td><td><input class='sinp' name='i_cena_zl' value='$Cena[0]' /> zl &nbsp; <input maxlength='3' name='i_cena_st' class='sinp' value='$Cena[1]' /> st &nbsp; <input maxlength='3' class='sinp' name='i_cena_md' value='$Cena[2]' /> md</td></tr>\n";
		echo "<tr><td colspan='2' align='center'><input type='hidden' name='i_typ' value='$oI->typ' /><input type='submit' class='button' value='Upravit předmět' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/doplnky/'\" /></td></tr>\n";
		echo "</table></form>\n";
			
	break;


}
}else{
	echo "<span class='error'>Chyba: Id nenalezeno</span>";
}

}elseif ($_GET['action'] == "new"){
?>
<h2>Nový předmět - POZOR - mazat předměty (zatím) nelze, protože mohou být v inventářích!</h2>
<form action='/rs/doplnky/?op=2' method='post'>
<div id="choiceTyp">
	<p><a href='javascript:vecShow("w")'>Zbraň pro boj tváří v tvář</a> | <a href='javascript:vecShow("s")'>Střelná nebo vrhací zbraň</a> | <a href='javascript:vecShow("z")'>Zbroj nebo štít</a> | <a href='javascript:vecShow("i")'>Předmět</a></p>
</div>
<div id="formItemTyp"></div>
</form>

<script type='text/javascript'>
function backToVyber(){
	var starter = document.getElementById('choiceTyp');
	var formik = document.getElementById('formItemTyp');
	formik.innerHTML = '';
	starter.style.display = 'block';
	formik.style.display = 'none';
}
function vecShow(typ) {
	var starter = document.getElementById('choiceTyp');
	var formik = document.getElementById('formItemTyp');
	starter.style.display = "none";
	formik.style.display = "block";
	inmess0 = "<p><a href='/rs/doplnky?action=new' onclick='backToVyber();return false;'>Zpět na výběr</a></p><table class='autolayout' width='80%'><tr><td colspan='2'><input type='hidden' name='i_typ' value='"+typ+"' />";
	inmess1 = "</td></tr><tr><td>Název:</td><td><input type='text' maxlength='40' name='i_nazev' /></td></tr>";
	inmess2 = "<tr><td>Popis:</td><td><textarea name='i_popis' rows='6' cols='40'></textarea></td></tr><tr><td>Cena:</td><td><input class='sinp' value='0' name='i_cena_zl' /> zl <input maxlength='3' name='i_cena_st' value='0' class='sinp' /> st <input maxlength='3' value='0' class='sinp' name='i_cena_md' /> md</td></tr><tr><td colspan='2' align='center'><input type='submit' value='Vytvořit předmět' /></td></tr></table>";

	mess = "";

	if (typ == "z" || typ == "a") {
		mess = "Zbroj nebo štít";
		formik.innerHTML = inmess0+mess+inmess1+"<tr><td></td><td><select name='i_hands'><option value='0'>Zbroj</option><option value='1'>Štít (jednoruční)</option></select></td></tr><tr><td><acronym title='Kvalita zbroje/štítu'>KZ</acronym>:</td><td><input type='text' name='i_kz' class='sinp' maxlength='3' /></td></tr>"+inmess2;
	}
	else if (typ == "i") {
		mess = "Předmět (lektvar, svitek, vybavení)";
		formik.innerHTML = inmess0+mess+inmess1+inmess2;
	}
	else if (typ == "w" || typ == "s") {
		mess = typ=="w"?"Zbraň pro boj tváří v tvář":"Střelná nebo vrhací zbraň";
		formik.innerHTML = inmess0+mess+inmess1+"<tr><td>Síla zbraně:</td><td><input type='text' class='sinp' name='i_sz' maxlength='3' /></td></tr><tr><td>Útočnost:</td><td><input class='sinp' type='text' class='sinp' name='i_utoc' maxlength='3' /></td></tr><tr><td>Obrana zbraně:</td><td><input class='sinp' type='text' name='i_oz' maxlength='3' /></td></tr><tr><td>&nbsp;</td><td><select name='i_hands'><option value='1'>Jednoruční</option><option value='2'>Obouruční</option></select></td></tr>"+inmess2;
	}
	formik.focus();
}
</script>

<?php

}
else {
	echo "<p><a href='/rs/doplnky/?action=new'>Založit nový předmět</a></p>";

	$art = mysql_query("SELECT * FROM 3_herna_items ORDER BY typ DESC, nazev ASC");
	$count = mysql_num_rows($art);

	if ($count > 0){

	echo "<table class='list'>\n<tr><th>Název</th><th width='50%'>Popis</th><th>Cena</th><th width='10%'>Akce</th></tr>\n";	
	$i = 1;
	$typOld = "";
	while ($s = mysql_fetch_object($art)){
		$i%=2;
	
		if ($s->typ != $typOld) {
			$typOld = $s->typ;
			echo "<tr><th colspan='4'>$typyItemu[$typOld]</th></tr>\n";
			ob_flush();
		}
		$nazev = stripslashes($s->nazev);
		$popis = mb_strimwidth(strip_tags(stripslashes($s->popis)), 0, 60, "...");
	
		echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>";
		echo "<td><a href='/rs/doplnky/?action=view&id=$s->id' title='Zobrazit'>$nazev</a></td>";
		echo "<td><a href='/rs/doplnky/?action=view&id=$s->id' title='Zobrazit'>$popis</a></b></td>";
		echo "<td>".item_cena($s->cena)."</td>";
		echo "<td><a href='/rs/doplnky/?action=edit&id=$s->id' title='editovat'>editovat</a></td>";
		echo "</tr>\n";
		$i++;
	}
	
		echo "</table>";
	
	}else echo "<p>V databázi není ani jeden inventární doplněk pro hru Dračí Doupě.</p>";

}
// END BODY FUNC
}
?>
