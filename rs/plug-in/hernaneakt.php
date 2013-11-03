<?php

function getHerNot() {
	global $time,$dbCnt;
	$c = mysql_fetch_row(mysql_query ("SELECT count(*) FROM 3_herna_all WHERE aktivita < $time-60*60*24*3*7 AND ohrozeni='0' AND schvaleno='1'") );
	$dbCnt++;
	$b = mysql_fetch_row(mysql_query ("SELECT count(*) FROM 3_herna_all WHERE aktivita < $time-60*60*24*5*7 AND ohrozeni='1' AND schvaleno='1'") );
	$dbCnt++;
	if ($c[0]>0) $c[0] = "<b>".$c[0]."</b>";
	if ($b[0]>0) $b[0] = "<b>".$b[0]."</b>";
	return $c[0]."/".$b[0];
}

$menuLinks['hernaneakt'] = "Neaktivita (".getHerNot().")";
$requireRights['hernaneakt'] = true;

function hernaneakt_head($rub) {
global $time,$dbCnt;
switch ($_GET['op']) {
	//poslani neaktivniho postu
  case 1:
		if (mb_strlen($_POST['nazev']) > 0){
			$hItem = mysql_fetch_object(mysql_query("SELECT * FROM 3_herna_all WHERE id = '$_GET[id]' AND ohrozeni = '0'"));
			$dbCnt++;
			if ($hItem->typ=='0') {
				$jTypString = "drd";
			}
			else {
				$jTypString = "orp";
			}
			$hraciSrc = mysql_query("SELECT uid FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->id'");
			$dbCnt++;
			if (mysql_num_rows($hraciSrc)>0) {
				$hraci = array();
				while ($hrac = mysql_fetch_row($hraciSrc)) {
					$hraci[] = $hrac[0];
				}
				$texte = "Administrátor $_SESSION[login] tě tímto upozorňuje, že jeskyně <strong>$hItem->nazev</strong> je již déle, než 3 týdny neaktivní. Pokud toto bude nadále trvat, bude jeskyně po datumu <strong>".date("j.n.Y",$hItem->aktivita+60*60*24*5*7)."</strong> smazána se vším všudy.";
				sysPost($hraci,$texte);
			}
			mysql_query ("UPDATE 3_herna_all SET ohrozeni = '1', pro_adminy = 'Upomínka ze dne ".date("d.m.Y - H:i",$time)."' WHERE id = $_GET[id]");
			$dbCnt++;
			if ($hItem->id>0) {
				$mess = "Administrátor $_SESSION[login] tě tímto upozorňuje, že jeskyně <strong>$hItem->nazev</strong> je již déle, než 3 týdny neaktivní. Pokud toto bude nadále trvat, bude jeskyně po datumu <strong>".date("j.n.Y",$hItem->aktivita+60*60*24*5*7)."</strong> smazána se vším všudy.<br /><br />V jeskyni v sekci <a href='/herna/$hItem->nazev_rew/pj/'>PJ</a> je položka <strong>text pro adminy</strong>, kam můžeš (například při plánované delší neaktivitě) napsat důvod onoho stavu.";
				sysPost($hItem->uid, $mess);
				$info = 1;
			}else{
				$info = 2;
			}
		}else{
			$info = 2;
		}
  break;
	//delete cave
  case 2:
		if (mb_strlen($_POST['nazev']) > 0){
			$hItem = mysql_fetch_object(mysql_query("SELECT * FROM 3_herna_all WHERE id = '$_GET[id]'"));
			$dbCnt++;
			if ($hItem->typ=='0') {
				$jTypString = "drd";
			}
			else {
				$jTypString = "orp";
			}
			$hraciSrc = mysql_query("SELECT uid,ico FROM 3_herna_postava_$jTypString WHERE cid = $hItem->id");
			$dbCnt++;
			if (mysql_num_rows($hraciSrc)>0) {
				$hraci = array();
				while ($hrac = mysql_fetch_row($hraciSrc)) {
					$hraci[] = $hrac[0];
					if ($hrac[1] != "" && $hrac[1] != 'default.jpg') {
						@unlink("./system/icos/$hrac[1]");
					}
				}
				$texte = "Administrátor $_SESSION[login] smazal jeskyni <strong>$hItem->nazev</strong> z důvodu <strong>neaktivity</strong> delší než 5 týdnů. Vaše postava tak byla také <strong>zabita</strong>.";
				sysPost($hraci,$texte);
				if ($hItem->ico != "" && $hItem->ico != 'default.jpg') {
					@unlink("./system/icos/$hItem->ico");
				}
			}
			$mapSrc = mysql_query("SELECT datas FROM 3_herna_maps WHERE cid = $hItem->id AND soubor != 'js'");
			$dbCnt++;
			if (mysql_num_rows($mapSrc)>0) {
				while ($mapa = mysql_fetch_row($mapSrc)) {
					@unlink("./system/mapy/$mapa[0]");
				}
			}

			$pjs_S = mysql_query("SELECT ico FROM 3_herna_pj WHERE cid = '$hItem->id'");
			if (mysql_num_rows($pjs_S)>0) {
				while ($pjs = mysql_fetch_row($pjs_S)) {
					if ($pjs[0] != 'default.jpg' && $pjs[0] == '') {
						@unlink("./system/icos/$pjs[0]");
					}
				}
			}

//			mysql_query("LOCK TABLES 3_herna_all WRITE, 3_herna_postava_orp WRITE, 3_herna_postava_drd WRITE, 3_herna_maps WRITE, 3_visited_4 WRITE, 3_comm_4 WRITE, 3_cave_mess WRITE, 3_cave_users WRITE, 3_herna_sets_open WRITE");
			mysql_query("DELETE FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->id';");
			mysql_query("DELETE FROM 3_herna_maps WHERE cid = '$hItem->id';");
			mysql_query("DELETE FROM 3_comm_4 WHERE aid = '$hItem->id';");
			mysql_query("DELETE FROM 3_visited_4 WHERE aid = '$hItem->id';");
			mysql_query("DELETE FROM 3_herna_sets_open WHERE cid = '$hItem->id';");
			mysql_query("DELETE FROM 3_cave_users WHERE cid = '$hItem->id';");
			mysql_query("DELETE FROM 3_cave_mess WHERE cid = '$hItem->id';");
			mysql_query("DELETE FROM 3_herna_pj WHERE cid = '$hItem->id';");
			mysql_query("DELETE FROM 3_herna_all WHERE id = '$hItem->id';");
//			mysql_query("UNLOCK TABLES");
			mysql_query("OPTIMIZE TABLE 3_comm_4, 3_herna_all, 3_visited_4");
			$dbCnt+=9;
			if ($hItem->ico != "" && $hItem->ico != 'default.jpg') {
				@unlink("./system/icos/$hItem->ico");
			}
			$mess = "Administrátor $_SESSION[login] smazal jeskyni <strong>$hItem->nazev</strong> z důvodu <strong>neaktivity</strong> délší než 5 týdnů.";
			sysPost($hItem->uid, $mess);
			$info = 3;
		}else{
			$info = 4;
		}
  break;
}
	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}
Header ("Location: /rs/$rub/?info=$info&index=$index");
exit;
}


function hernaneakt_body(){
global $time,$dbCnt;
$typJeskyne = array("DrD","ORP");  

	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

$from = ($index - 1) * ARTICLE_COUNT; //od kolikate polozky zobrazit

if ($_GET['id'] > 0 && ctype_digit($_GET['id'])){
$fId = mysql_query ("SELECT h.*,u.login FROM 3_herna_all AS h LEFT JOIN 3_users AS u ON u.id = h.uid WHERE h.id = '$_GET[id]'");
$dbCnt++;

if (mysql_num_rows($fId) > 0){
	$oI = mysql_fetch_object($fId);
	switch ($_GET['action']){
		case "view":
			echo "<h2>Prohlédnout</h2>";
			echo "<form action='/rs/hernaneakt/?index=$index' method='post'>";
			echo "<table width='80%'>";
			echo "<tr><td width='20%'>Systém</td><td>".$typJeskyne[$oI->typ]."</td></tr>";
			echo "<tr><td width='20%'>Název</td><td><a href='/rs/herna/?action=view&id=$oI->id'>".stripslashes($oI->nazev)."</a> via RS</td></tr>";
			echo "<tr><td width='20%'>Majitel</td><td><a href='/rs/uzivatele/?action=view&amp;id=$oI->uid'>$oI->login</a></td></tr>";
			echo "<tr><td width='20%'>Poslední aktivita</td><td>".date("d.m.Y H:i" ,$oI->aktivita)."</td></tr>";
			echo "<tr><td width='20%'>Text pro ADMINY</td><td>".stripslashes($oI->pro_adminy)."</td></tr>";
			echo "<tr><td colspan='2' align='center'><input type='button' value='Zavřít' onClick=\"window.location.href='/rs/hernaneakt/?index=$index'\" /></td></tr>";
			echo "</table>";
			echo "</form>";
		break;
		case "delete":
			echo "<h2>Smazat</h2>";
			echo "<form action='/rs/hernaneakt/?op=2&amp;id=$_GET[id]&amp;vlastnik=$oI->uid&amp;index=$index' method='post' onsubmit=\"if (confirm('Smazat jeskyni?')){return true;}else{return false;}\">";
			echo "<table width='80%'>";
			echo "<tr><td width='20%'>Název</td><td><a href='/rs/herna/?action=view&id=$oI->id'>".stripslashes($oI->nazev)."</a> via RS<input type='hidden' value='".stripslashes($oI->nazev)."' size='100' name='nazev' readonly='readonly' /></td></tr>";
			echo "<tr><td width='20%'>Majitel</td><td><a href='/rs/uzivatele/?action=view&amp;id=$oI->uid'>$oI->login</a></td></tr>";
			echo "<tr><td width='20%'>Text pro ADMINY</td><td>".stripslashes($oI->pro_adminy)."</td></tr>";
			echo "<tr><td colspan='2' align='center'><input type='submit' value='Smazat jeskyni' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/hernaneakt/?index=$index'\" /></td></tr>";
			echo "</table>";
			echo "</form>";
		break;
		case "send":
			echo "<h2>Odeslání informační zprávy všem hráčům a PJovi jeskyně</h2>";
			echo "<form action='/rs/hernaneakt/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
			echo "<table width='80%'>";
			echo "<tr><td width='20%'>Název</td><td><a href='/rs/herna/?action=view&id=$oI->id'>".stripslashes($oI->nazev)."</a> via RS<input type='hidden' value='".stripslashes($oI->nazev)."' size='100' name='nazev' readonly='readonly' /></td></tr>";
			echo "<tr><td width='20%'>Majitel</td><td><a href='/rs/uzivatele/?action=view&amp;id=$oI->uid'>$oI->login</a></td></tr>";
			echo "<tr><td width='20%'>Text pro ADMINY</td><td>".stripslashes($oI->pro_adminy)."</td></tr>";
			echo "<tr><td colspan='2' align='center'><input type='submit' value='Odeslat zprávu' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/hernaneakt/?index=$index'\" /></td></tr>";
			echo "</table>";
			echo "</form>";
		break;
	}
}else{
	echo "<span class='error'>Chyba: Id nenalezeno</span>";
}
}

switch($_GET['info']){
  case 1:
    echo "<span class='ok'>Ok: Upozornění hráčům a PJovi bylo odesláno</span>";
  break;
  case 2:
    echo "<span class='error'>Chyba: Jeskyni se nepodařilo nastavit atribut Ohrožení</span>";
  break;
  case 3:
    echo "<span class='ok'>Ok: Jeskyně smazána</span>";
  break;
  case 4:
    echo "<span class='error'>Chyba: Nastala chyba při mazání</span>";
  break;
  break;
}

$artc = mysql_query ("SELECT count(*) FROM 3_herna_all WHERE aktivita < $time-60*60*24*3*7 OR ohrozeni='1'");
$dbCnt++;

$count = array_shift(mysql_fetch_row($artc));
$art = mysql_query ("SELECT c.*,u.login, ($time-c.aktivita) AS howlong FROM 3_herna_all AS c, 3_users AS u WHERE c.schvaleno = '1' AND c.uid = u.id AND (c.aktivita < $time-60*60*24*3*7 OR ohrozeni='1') ORDER BY c.ohrozeni DESC, c.aktivita ASC LIMIT $from, ".ARTICLE_COUNT);
$dbCnt++;

if ($count > 0){

  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

  echo "<table class='list autolayout'>\n";
  echo "<tr><th>Název</th><th>Vlastník</th><th>Stav</th><th>Posl.aktivita</th><th>Text pro adminy</th><th>Akce</th></tr>\n";

$i = 1;
if ($count == 1) {
	$index -= 1;
}
else $index = $index;
while ($s = mysql_fetch_object($art)){

$nazev = stripslashes($s->nazev);
  
  echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
  echo "	<td><a href='/rs/hernaneakt/?action=view&id=$s->id&amp;index=$index' title='Editace'>".((mb_strlen($nazev,"UTF-8") > 18)? mb_substr($nazev, 0, 18,"UTF-8").'...' : $nazev)."</a></td>\n";
  echo "	<td><a href='/rs/uzivatele/?action=view&amp;id=$s->uid'>$s->login</a></td>\n";
  echo "	<td>".((($s->ohrozeni) =='1')? "<span>informováno</span>" : "<em>neaktivní <b>".date("z",$s->howlong)."</b> dnů &raquo;</em>")."</td>\n";
  echo "	<td class=\"ac\">".( ($s->aktivita<$time-60*60*24*5*7) ? "<em>".date("z",$s->howlong)." dnů - smazat</em>" : date("d.m.Y - H:i",$s->aktivita ) )."</td>\n";
	echo "	<td>".mb_strimwidth(stripslashes($s->pro_adminy),0,40,"...","UTF-8")."</td>\n";
  echo "	<td><a href='/rs/hernaneakt/?action=send&id=$s->id&amp;index=$_GET[index]' title='Odeslat'>upomínka</a> - <a href=\"/rs/hernaneakt/?action=delete&amp;id=$s->id&amp;index=$index\" title='smazat'>smazat?</a></td>\n";
  echo "</tr>\n";
$i++;
}

  echo "</table>";
  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

}else{

  echo "Žádná jeskyně v databázi.";

}
// END BODY FUNC
}
?>
