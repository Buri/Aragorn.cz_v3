<?php

$menuLinks['galerie'] = "Galerie (".getGalNew().")";
$requireRights['galerie'] = true;

function galerie_head($rub) {
global $time,$dbCnt;
switch ($_GET['op']) {
	//editace obrazku
  case 1:
		if (mb_strlen($_POST['nazev']) > 0 && mb_strlen($_POST['popis']) > 5){
			mysql_query("LOCK TABLES 3_galerie WRITE");
			mysql_query("UPDATE 3_galerie SET popis = '".htmlspecialchars($_POST['popis'])."' WHERE id = $_GET[id]");
			mysql_query("UNLOCK TABLES");
			$dbCnt++;
			$info = 1;
		}else{
			$info = 2;
		}
  break;
	//delete image
  case 2:
		if (mb_strlen($_POST['nazev']) > 0 && ctype_digit($_GET['autor'])){
			$id = ((intval($_GET['id'])+1)-1);
			mysql_query("LOCK TABLES 3_galerie WRITE, 3_comm_2 WRITE, 3_sekce_prava WRITE, 3_rating WRITE, 3_visited_2 WRITE");
			$im = mysql_fetch_object(mysql_query("SELECT source, thumb FROM 3_galerie WHERE id = $id"));
			$dbCnt++;
			@unlink("./galerie/$im->source");
			@unlink("./galerie/$im->thumb");
			mysql_query("DELETE FROM 3_galerie WHERE id = $id");
			mysql_query("DELETE FROM 3_comm_2 WHERE aid = $id");
			mysql_query("DELETE FROM 3_sekce_prava WHERE sid = '2' AND aid = $id");
			mysql_query("DELETE FROM 3_rating WHERE sid = '2' AND aid = $id");
			mysql_query("DELETE FROM 3_visited_2 WHERE aid = $id");
			$dbCnt+=5;
			$mess = "Váš obrázek $_POST[nazev] byl administrátorem $_SESSION[login] smazán.<br />$_POST[reason]";
			$autorA = mysql_fetch_row(mysql_query("SELECT login FROM 3_users WHERE id = '$_GET[autor]'"));
			$dbCnt++;
			mysql_query("UNLOCK TABLES");
			sysPost($_GET['autor'], $mess);
	    if (isset($_POST['sendInfo']) && $_POST['sendInfo'] == "yes") {
		    sysPost($_SESSION['uid'],"System INFO: Obrázek $_POST[nazev] ~ $autorA[0] ~ byl smazán.<br />$_POST[reason]");
		  }
			$info = 3;
		}else{
			$info = 4;
		}
  break;
  //schvaleni image
  case 3:
		if (mb_strlen($_GET['id']) > 0){
			mysql_query("LOCK TABLES 3_galerie WRITE");
			mysql_query ("UPDATE 3_galerie SET schvaleno = '1', schvalenotime = '$time' WHERE id = $_GET[id]");
			$dbCnt++;
			$c = mysql_fetch_object( mysql_query("SELECT nazev, autor FROM 3_galerie WHERE id = '$_GET[id]'") );
			mysql_query("UNLOCK TABLES");
			$dbCnt++;
			$mess = "Váš obrázek <a href='/galerie/".do_seo($c->nazev)."/' title='$c->nazev'>$c->nazev</a> byl dnes schválen administrátorem $_SESSION[login].\nDěkujeme za něj.";
			sysPost($c->autor, $mess);
			$info = 5;
		}else{
			$info = 6;
		}
  break;
  //editace frontpage nad vypisem obrazku
  case 5:
    $messa = addslashes($_POST['mess']);
		mysql_query("LOCK TABLES 3_notes WRITE");
		mysql_query("UPDATE 3_notes SET text = '$messa' WHERE uid=0");
		$dbCnt++;
		if (mysql_affected_rows()<1) {
			mysql_query("INSERT INTO 3_notes (uid,text) VALUES (0,'$messa')");
			$dbCnt++;
		}
		mysql_query("UNLOCK TABLES");
		$info=8;
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

function galerie_body(){
	global $dbCnt;
	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

$from = ($index - 1) * ARTICLE_COUNT; //od kolikate polozky zobrazit

if ($_GET['id'] > 0){
$fId = mysql_query ("SELECT * FROM 3_galerie WHERE id = $_GET[id]");
$dbCnt++;

if (mysql_num_rows($fId) > 0){

$oI = mysql_fetch_object($fId);

switch ($_GET['action']){
  case "view":
  echo "<h2>Prohlédnout</h2>";
  echo "<form action='/rs/galerie/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Název</td><td>".stripslashes($oI->nazev)."</td></tr>";
        echo "<tr><td>Popis</td><td>".stripslashes($oI->popis)."</td></tr>";
        echo "<tr><td>Odkaz</td><td>na <a href='/galerie/$oI->source' rel='lightbox' target='_blank'>obrázek</a> via LightBox</td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='button' value='Zavřít' onClick=\"window.location.href='/rs/galerie/?index=$index'\" /> <input type='button' value='Editace' onClick=\"window.location.href='/rs/galerie/?action=edit&amp;id=$_GET[id]&amp;index=$index'\" /> <input type='button' value='Ke smazání' onClick=\"window.location.href='/rs/galerie/?action=delete&amp;id=$_GET[id]&amp;index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
  break;
  case "edit":
  echo "<h2>Editace</h2>";
  echo "<form action='/rs/galerie/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Název</td><td><a href='/rs/galerie/?action=view&amp;id=".$oI->id."'>".stripslashes($oI->nazev)."</a><input type='hidden' value='"._htmlspec(stripslashes($oI->nazev))."' size='150' name='nazev' readonly='readonly' /></td></tr>";
        echo "<tr><td>Popis</td><td><input type='text' value='".stripslashes($oI->popis)."' size='100' name='popis' /></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/galerie/?index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
  break;
    case "delete":
  echo "<h2>Smazat</h2>";
  echo "<form action='/rs/galerie/?op=2&amp;id=$_GET[id]&amp;autor=$oI->autor&amp;index=$index' method='post' onsubmit=\"if (confirm('Smazat obrázek?')){return true;}else{return false;}\">";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Název</td><td><a href='/rs/galerie/?action=view&amp;id=".$oI->id."'>".stripslashes($oI->nazev)."</a><input type='hidden' value='"._htmlspec(stripslashes($oI->nazev))."' size='150' name='nazev' readonly='readonly' /></td></tr>";
        echo "<tr><td valign='top'>Důvod</td><td><textarea rows='10' cols='40' name='reason'></textarea></td></tr>";
        echo "<tr><td></td><td><input type='checkbox' value='yes' name='sendInfo' id='sendInfo' /><label for='sendInfo'>poslat mi do pošty INFO-poštolku o smazání</label></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Smazat obrázek' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/galerie/?index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
  break;
}
}else{
  echo "<span class='error'>Chyba: Id nenalezeno</span>";
}
}
else {
	switch ($_GET['action']) {
	  case "frontpage":
		$txtInS = mysql_fetch_row(mysql_query("SELECT text FROM 3_notes WHERE uid=0"));
		$dbCnt++;
		$txtIn = _htmlspec($txtInS[0]);
	  echo "<h2>Text nad výpisem obrázků</h2>\n";
	  echo "<form action='/rs/galerie/?op=5' method='post'>";
	    echo "<table width='80%'>";
	        echo "<tr><td valign='top'>Text<br />HTML tagy OK!<br />(konce řádků psát ručně <strong>&lt;br&nbsp;/&gt;</strong>)</td><td><textarea rows='10' cols='55' name='mess'>$txtIn</textarea></td></tr>";
	        echo "<tr><td colspan='2' align='center'><input type='submit' value='Upravit' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/galerie/'\" /></td></tr>";
	    echo "</table>";
	  echo "</form>";
	  break;
	}
}

switch($_GET['info']){
  case 1:
    echo "<span class='ok'>Ok: Obrázek v pořádku editován</span>";
  break;
  case 2:
    echo "<span class='error'>Chyba: Popis je prázdný</span>";
  break;
  case 3:
    echo "<span class='ok'>Ok: Obrázek smazán</span>";
  break;
  case 4:
    echo "<span class='error'>Chyba: Nastala chyba při mazání</span>";
  break;
  case 5:
    echo "<span class='ok'>Ok: Obrázek schválen</span>";
  break;
  case 6:
    echo "<span class='error'>Chyba: Nastala chyba při schvalování obrázku</span>";
  break;
  case 8:
    echo "<span class='ok'>Ok: Nástěnka Galerie byla upravena</span>";
  break;
}

echo "<div><a href='/rs/galerie/?action=frontpage' title='Upravit text nástěnky'>Nástěnka v Galerii</a>
<form action='/rs/galerie/' method='post'>
	<p>Část názvu: <input type='text' value='"._htmlspec(trim($_POST['q']))."' size='10' name='q' /> <input type='submit' value='Hledat' /></a></p>
</form></div>\n";

$doPages = true;
if (!isset($_POST['q'])) {
	$artc = mysql_query ("SELECT count(*) FROM 3_galerie");
	$dbCnt++;
	$art = mysql_query ("SELECT c.*, u.login, u.level FROM 3_galerie AS c, 3_users AS u WHERE c.autor = u.id ORDER BY c.schvaleno, c.schvalenotime DESC LIMIT $from, ".ARTICLE_COUNT);
	$dbCnt++;
	$count = array_shift(mysql_fetch_row($artc));
}
else {
	$count = 0;
	if (strlen(do_seo(trim($_POST['q'])))<3) {
		echo "<p>Pro hledání je potřeba zadat alespoň 3 znaky z názvu (mezera se nepočítá)!</p>\n";
	}
	else {
		echo "<p>Hledám výraz <strong>"._htmlspec(trim($_POST['q']))."</strong>.</p>\n";
		$art = mysql_query ("SELECT c.*, u.login, u.level FROM 3_galerie AS c, 3_users AS u WHERE c.autor = u.id AND c.nazev_rew LIKE '%".do_seo(trim($_POST['q']))."%' ORDER BY c.nazev_rew ASC");
		$dbCnt++;
		$count = mysql_num_rows($art);
		$doPages = false;
	}
}

if ($count > 0){

  if ($doPages) echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

  echo "<table class='list'>\n";
  echo "<tr><th>Název</th><th>Autor</th><th>Náhled</th><th>Hodnocení</th><th>Stav</th><th>Akce</th></tr>\n";

$i = 1;
while ($s = mysql_fetch_object($art)){

$nazev = stripslashes($s->nazev);
$anotace = stripslashes($s->anotace);

$rate = " (N/A) ";
  
if ($s->schvaleno < 1){

  $time = date("d.m." ,$s->odeslanotime);
  $acc = "<a href=\"javascript: conf('/rs/galerie/?op=3&amp;id=$s->id&amp;index=$index')\" title='schválit'>schválit</a> - ";

}else{

	if ($s->hodnoceni > 0) {
		$rtg = $s->hodnoceni/$s->hodnotilo;
		if ($rtg <= 2) $rate = number_format(round($rtg,2), 2, ',','')." <b style='color:red'>".str_repeat("* ",round($rtg))."</b>";
		else $rate = number_format(round($rtg,2), 2, ',','')." <b style='color:green'>".str_repeat("* ",round($rtg))."</b>";
	}
  $time = date("d.m." ,$s->schvalenotime);
  $acc = "";

}

if (!is_file("./galerie/$s->thumb")){
  $nahled = "<span class='error'>chyba v náhledu</span>";
}else{
  $nahled = "<img src='/galerie/$s->thumb' alt='$s->thumb' title='$s->thumb' />";
}
  
  echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
  echo "<td><a href='/rs/galerie/?action=view&amp;id=$s->id&amp;index=$index' title='Detaily'>".mb_strimwidth($nazev,0,20,"...")."</a></td>\n";
  echo "<td><a href='/rs/uzivatele/?action=view&id=$s->autor&amp;index=$index' title='Profil autora'>$s->login".(($s->level>=2)?"(*)":"")."</a></b></td>";
  echo "<td align='center'><a href='/galerie/$s->source' rel='lightbox[galerka]' title='"._htmlspec($s->nazev)."'>$nahled</a></td>\n";
  echo "<td>$rate</td>";
  echo "<td class='bg".(($s->schvaleno < 1)? 3 : 4)."'>".(($s->schvaleno < 1)? 'čeká' : 'schváleno')." od $time</td>\n";
  echo "<td>$acc <a href='/rs/galerie/?action=edit&id=$s->id&amp;index=$index' title='editovat'>editovat</a> - <a href=\"/rs/galerie/?action=delete&amp;id=$s->id&amp;index=$index\" title='smazat'>smazat</a><br /><a href='http://www.tineye.com/search/?url=".urlencode("http://www.aragorn.cz/galerie/$s->source")."'>hledač_kopií</a></td>";
  echo "</tr>\n";
$i++;
}

  echo "</table>";

  if ($doPages) echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

}else{

	if (!isset($_POST['q'])) echo "Žádný obrázek v databázi.";
	else {
		if (strlen(trim($_POST['q']))>=3) echo "Žádný obrázek v databázi, který by vyhovoval hledání.";
	}

}
// END BODY FUNC
}
?>
