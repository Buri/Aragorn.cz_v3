<?php

$menuLinks['reklamy'] = "Chat (Reklamy)";
$requireRights['reklamy'] = true;

function reklamy_head($rub) {
	global $menuLinks;
	if (!isset($menuLinks['reklamy'])) $_GET['op'] = 5;

switch ($_GET['op']) {
	case 1:
		$id = addslashes($_GET['id']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_advert WHERE id = '$id'"));
		if ($countEr[0]>0) {
			if (mb_strlen($_POST['text']) > 1 && $_POST['cykle'] != "") {
				$text = addslashes($_POST['text']);
				$cykle = addslashes((int)$_POST['cykle']);
				$active = isset($_POST['active']) ? 1 : 0;
				mysql_query("UPDATE 3_chat_advert SET text = '$text', cykle='$cykle', active='$active' WHERE id = '$id'");
				$info = 1;
			}else {
				$info = 4;
			}
		}else {
			$info = 8;
		}
	break;
	case 2:
		if (mb_strlen($_POST['text']) > 1 && $_POST['cykle'] != "") {
			$text = addslashes($_POST['text']);
			$cykle = addslashes((int)$_POST['cykle']);
			$active = isset($_POST['active']) ? 1 : 0;
			mysql_query("INSERT INTO 3_chat_advert (text, cykle, active, time) VALUES ('$text', '$cykle', '$active', NOW())");
			$info = 3;
		}else {
			$info = 4;
		}
	break;
	case 3:
		$id = addslashes($_GET['id']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_advert WHERE id = '$id'"));
		if ($countEr[0]>0) {
			mysql_query("DELETE FROM 3_chat_advert WHERE id = '$id'");
			$info = 2;
		}else {
			$info = 8;
		}
	break;

	case 4:
		$id = addslashes($_GET['id']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_advert WHERE id = '$id'"));
		if ($countEr[0]>0) {
			mysql_query("UPDATE 3_chat_advert SET active = (1 - active) WHERE id = '$id'");
			$info = 5;
		}else {
			$info = 8;
		}
	break;
}
Header ("Location: /rs/$rub/?info=$info");
exit;
}

function reklamy_body() {

	$advert_timers = array('0'=>'0', '10'=>'10', '20'=>'20', '30'=>'30', '60'=>'60', '120'=>'120', '180'=>'180');

	if ($_GET['id'] > 0){
		$_GET['id'] = (int)$_GET['id'];
		$fId = mysql_query ("SELECT * FROM 3_chat_advert WHERE id = '$_GET[id]'");
		if (mysql_num_rows($fId) > 0){
			$oI = mysql_fetch_object($fId);
			switch ($_GET['action']){
				case "edit":
					echo "<h2>Editace reklamy na chatu</h2>";
					echo "<form action='/rs/reklamy/?op=1&amp;id=$_GET[id]' method='post'>";
					echo "<table>";
					echo "<tr><td width='20%' valign='top'>Text (xHTML je O.K.)</td><td><textarea rows='13' cols='50' name='text'>"._htmlspec($oI->text)."</textarea></td></tr>";
					echo "<tr><td colspan='2'><label for='is_active_advert'><input id='is_active_advert' name='active' type='checkbox' ".($oI->active ? "checked='checked' " : '')."value='1' /> aktivní</label></td></tr>";
	        echo "<tr><td>Popis</td><td><select name='cykle'>";
	        foreach($advert_timers as $k) {
	        	if ($oI->cykle == $k) {
	        		echo "<option value='$k' selected='selected'>$k minut</option>";
						}
						else {
							echo "<option value='$k'>$k minut</option>";
						}
					}
					echo "</select></td></tr>";
					echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/reklamy/'\" /></td></tr>\n";
					echo "</table>\n";
					echo "</form>";
				break;
			}
		}
		else {
			echo "<span class='error'>Chyba: ID záznamu nenalezeno nebo nepovolená akce se záznamem</span>";
		}
	}
	elseif ($_GET['action'] == "new") {
		echo "<h2>Nová reklama na chatu</h2>";
		echo "<form action='/rs/reklamy/?op=2' method='post'>";
		echo "<table width='40%'>";
		echo "<tr><td width='20%' valign='top'>Text (xHTML je O.K.)</td><td><textarea rows='13' cols='50' name='text'></textarea></td></tr>";
		echo "<tr><td colspan='2'><label for='is_active_advert'><input id='is_active_advert' name='active' type='checkbox' value='1' /> aktivní</label></td></tr>";
		echo "<tr><td>Popis</td><td><select name='cykle'>";
		foreach($advert_timers as $k) {
			echo "<option value='$k'>$k minut</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td colspan='2' align='center'><input type='submit' value='Přidat reklamu' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/reklamy/'\" /></td></tr>\n";
		echo "</table>\n";
		echo "</form>";
	}

switch($_GET['info']){
	case 8:
		echo "<span class='error'>Error: Hledané ID neexistuje</span>";
	break;
	case 1:
		echo "<span class='ok'>Ok: Reklama vpořádku upravena</span>";
	break;
	case 2:
		echo "<span class='ok'>Ok: Reklama smazána</span>";
	break;
	case 3:
		echo "<span class='ok'>Ok: Reklama přidána</span>";
	break;
	case 4:
		echo "<span class='error'>Chyba: Chybí povinné údaje</span>";
	break;
	case 5:
		echo "<span class='ok'>OK: Aktivita reklamy změněna</span>";
	break;
}

$art = mysql_query ("SELECT * FROM 3_chat_advert ORDER BY active DESC, id ASC");
$count = mysql_num_rows($art);
if ($count > 0){
	echo "<p><a href='/rs/reklamy/?action=new'>Přidat novou reklamu</a></p>\n";
	echo "<table class='list autolayout'>\n";
	echo "<tr><th>Text</th><th>Stav</th><th>Cyklus (minuty)</th><th width='20%'>Akce</th></tr>\n";
	$i = 1;

	while ($s = mysql_fetch_object($art)) {

		$text = _htmlspec(mb_strimwidth($s->text, 0, 50, '...', "UTF-8"));
		echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
		echo "<td>$text</td>";
		echo "<td".($s->active > 0 ? " class='ok'>aktivní | <a href=\"javascript: conf('/rs/reklamy/?op=4&id=$s->id')\">Vypnout</a>":" class='error'>vypnutá | <a href=\"javascript: conf('/rs/reklamy/?op=4&id=$s->id')\">Zapnout</a>")."</td>";
		echo "<td>$s->cykle</td>\n";
		echo "<td><a href='/rs/reklamy/?action=edit&id=$s->id' title='Editace'>Upravit</a> | <a href=\"javascript: conf('/rs/reklamy/?op=3&id=$s->id')\">Smazat</a></td>\n";
		echo "</tr>\n";
		$i++;

}
	echo "</table>";
}else{
	echo "<p>Žádná uložená reklama na chatu.</p><p><a href='/rs/reklamy/?action=new'>Přidat novou reklamu</a></p>";
}
// END BODY FUNC
}
?>
