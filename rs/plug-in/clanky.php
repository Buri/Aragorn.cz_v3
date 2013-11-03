<?php

$menuLinks['clanky'] = "Články (".getArtNew().")";
$requireRights['clanky'] = true;
$requireRights['redaktorina'] = true;

function clanky_head($rub) {
	global $time,$dbCnt,$canDoThat;

 	$sections = $canDoThat->redaktorina ? unserialize($canDoThat->redaktorina) : array();

	switch ($_GET['op']) {
	//editace clanku
  case 1:
	  if (mb_strlen($_POST['nazev']) > 0 && mb_strlen($_POST['anotace']) > 0 && mb_strlen($_POST['text']) > 0 && $_POST['sekce'] != ""){
	  	$text = $_POST['text'];
			$admins = "admins = '".addslashes($_POST['admins'])."', ";

	  	if (isset($_POST['vratitSkomentarem']) && $_POST['vratitSkomentarem'] == "on") {
				$admins .= "schvaleno = '-1', schvalenotime = '$time', ";
				$autorUID = mysql_fetch_row(mysql_query("SELECT autor FROM 3_clanky WHERE id = '$_GET[id]'"));
		    sysPost($autorUID[0],"System INFO: Jeden z administrátorů <a href='/clanky/'>Článků</a> na serveru Aragorn.cz ti vrátil tvůj článek s komentářem / návrhem úprav či oprav.<br />Více se dozvíš v sekci Články pod odkazem <a href='/clanky/my/'>Moje články</a> u konkrétního článku.");
			}
			if (isset($_POST['changeNazev'])) {
				$nm = addslashes(trim($_POST['changeNazev']));
				$nmR = addslashes(do_seo(trim($_POST['changeNazev'])));
				$admins .= " nazev = '$nm', nazev_rew = '$nmR', ";
			}

//			$binarka = gzcompress($text,9);
//			if (strlen($text)>strlen($binarka)) {
//				$text = "0x".bin2hex($binarka);
//				$sqlAdd = ", compressed = 1";
//			}
//			else {
				$text = "'".addslashes($text)."'";
				$sqlAdd = ", compressed = 0";
//			}
	    mysql_query ("UPDATE 3_clanky SET $admins anotace = '".addslashes($_POST['anotace'])."', sekce = '$_POST[sekce]', kdoschvalil = '$_SESSION[uid]', text = $text $sqlAdd WHERE id = $_GET[id]");
	    	$dbCnt++;
	    $info = 1;
	  }else{
	      $info = 2;
	  }
  break;
	//delete clanku
  case 2:
	  if (mb_strlen($_POST['nazev']) > 0 && ctype_digit($_GET['autor'])){
	  	$id = ((intval($_GET['id'])+1)-1);
			mysql_query("LOCK TABLES 3_comm_1 WRITE, 3_clanky WRITE, 3_rating WRITE, 3_sekce_prava WRITE, 3_visited_1 WRITE");
	    mysql_query("DELETE FROM 3_comm_1 WHERE aid = '$id';");
			mysql_query("DELETE FROM 3_rating WHERE sid = '1' AND aid = '$id';");
			mysql_query("DELETE FROM 3_sekce_prava WHERE sid='1' AND aid = '$id';");
			mysql_query("DELETE FROM 3_visited_1 WHERE aid = '$id';");
			mysql_query("DELETE FROM 3_clanky WHERE id = '$id'");
			mysql_query("OPTIMIZE TABLE 3_visited_1, 3_comm_1, 3_clanky, 3_rating, 3_sekce_prava");
			mysql_query("UNLOCK TABLES");
	    $mess = "Váš článek $_POST[nazev] byl administrátorem $_SESSION[login] smazán.<br />$_POST[reason]";
	    sysPost($_GET['autor'], $mess);
	    $autorA = mysql_fetch_row(mysql_query("SELECT login FROM 3_users WHERE id = '$_GET[autor]'"));
	    $dbCnt++;
	    if (isset($_POST['sendInfo']) && $_POST['sendInfo'] == "yes") {
		    sysPost($_SESSION['uid'],"System INFO: Článek $_POST[nazev] ~ $autorA[0] ~ byl smazán.<br />$_POST[reason]");
			}
	    $info = 3;
	  }else{
	    $info = 4;
	  }
  break;
	//schvaleni clanku
  case 3:
	  if (mb_strlen($_GET['id']) > 0){
	    mysql_query ("UPDATE 3_clanky SET admins = '', schvaleno = '1', schvalenotime = '$time', kdoschvalil = '$_SESSION[uid]' WHERE id = $_GET[id]");
	    $dbCnt++;
	    $c = mysql_fetch_object( mysql_query("SELECT nazev, nazev_rew, autor FROM 3_clanky WHERE id = '$_GET[id]'") );
	    $dbCnt++;
	    $mess = "Váš článek <a href='/clanky/".$c->nazev_rew."/' title='$c->nazev'>$c->nazev</a> byl dnes schválen administrátorem $_SESSION[login].\nDěkujeme za něj.";
	    sysPost($c->autor, $mess);
	    $info = 5;
	  }else{
	    $info = 6;
	  }
  break;
  //editace frontpage nad vypisem clanku
  case 5:
    $messa = addslashes($_POST['mess']);
		mysql_query("UPDATE 3_notes SET text = '$messa' WHERE uid=1");
		$dbCnt++;
		if (mysql_affected_rows()<1) {
			mysql_query("INSERT INTO 3_notes (uid,text) VALUES (1,'$messa')");
			$dbCnt++;
		}
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

function clanky_body() {
	global $dbCnt, $canDoThat;

 	$sections = $canDoThat->redaktorina ? unserialize($canDoThat->redaktorina) : array();
 	$sectionsSQL = "'".join("','",array_keys($sections))."'";

	$doPages = true;

	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

$from = ($index - 1) * ARTICLE_LONG_COUNT; //od kolikate polozky zobrazit

if ($_GET['id'] > 0){
$fId = mysql_query ("SELECT c.*,u.login,u.login_rew FROM 3_clanky AS c, 3_users AS u WHERE c.id = $_GET[id] AND c.autor=u.id");
$dbCnt++;

if (mysql_num_rows($fId) > 0){

$oI = mysql_fetch_object($fId);
if ($oI->compressed) $oI->text = gzuncompress($oI->text);

switch ($_GET['action']){

  case "view":
  
  echo "<h2>Prohlédnout</h2>";

	$tags = false;
  if (stripos($oI->text, "<h1") !== false)
		$tags = true;
	elseif (stripos($oI->text, "<h2") !== false)
		$tags = true;
  elseif (stripos($oI->text, "<h3") !== false)
		$tags = true;
	elseif (stripos($oI->text, "<script") !== false)
		$tags = true;

	if ($tags) {
	  echo "<p class='error'><strong>Text článku obsahuje některý ze zakázaných tagů (h1, h2, h3, script). Raději se podívej na zdrojový kód článku v <a href='/rs/clanky/?action=edit&amp;id=$_GET[id]&amp;index=$index'>editaci</a>.</strong></p>";
	}
  echo "<form action='/rs/clanky/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
          echo "<tr><td width='20%'>Název</td><td>".stripslashes($oI->nazev)."</td></tr>";
        echo "<tr><td width='20%'>Anotace</td><td>".stripslashes($oI->anotace)."</td></tr>";
        echo "<tr><td width='20%'>Sekce</td><td>".viewSekce($oI->sekce)."</td></tr>";
        echo "<tr><td width='20%'>Autor</td><td><a href='/rs/uzivatele/?action=view&id=$oI->autor' title='Profil autora'>$oI->login</a></td></tr>";
        echo "<tr><td width='20%' valign='top'>Text</td><td>".spit($oI->text)."</td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='button' value='Zavřít' onClick=\"window.location.href='/rs/clanky/?index=$index'\" /> <input type='button' value='Editace' onClick=\"window.location.href='/rs/clanky/?action=edit&amp;id=$_GET[id]&amp;index=$index'\" /> <input type='button' value='Ke smazání' onClick=\"window.location.href='/rs/clanky/?action=delete&amp;id=$_GET[id]&amp;index=$index'\" /></td></tr>";
      
    echo "</table>";
  echo "</form>";
  break;

  case "edit":
  
  echo "<h2>Editace</h2>";
  echo "<form action='/rs/clanky/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
  
        echo "<tr><td width='20%'>Název</td><td><input type='text' value='".stripslashes($oI->nazev)."' size='100' name='nazev' readonly='readonly' /></td></tr>";
				echo "<tr><td width='20%'><a href='#' onclick='\$(\"changeNameSpan\").toggleClass(\"hide\");if(\$(\"changeNameSpan\").hasClass(\"hide\")){\$(\"changeNameSpan\").getFirst().getNext().set(\"name\",\"nazevNew\")};return false;'>Změnit název</a></td><td><div id='changeNameSpan' class='hide'><input type='hidden' value='$_GET[id]' name='the_hidden_id' /><input type='text' value='".stripslashes($oI->nazev)."' size='100' name='nazevNew' onkeyup='checkInput(this,\"clanek-name\")' /> <div>&nbsp;</div></div></td></tr>";
        echo "<tr><td width='20%'>Anotace</td><td><input type='text' value='".stripslashes($oI->anotace)."' size='100' name='anotace' /></td></tr>";
        echo "<tr><td width='20%'>Sekce</td><td>".setSekce($oI->sekce)."</td></tr>";
        echo "<tr><td width='20%'>Autor</td><td><a href='/rs/uzivatele/?action=view&id=$oI->autor' title='Profil autora'>$oI->login</a></td></tr>";
        echo "<tr><td></td><td><input type='checkbox' value='on' name='vratitSkomentarem' id='sendecho' /><label for='sendecho'>vrátit autorovi k možnému přepracování <strong title='odešle autorovi poštolku o tom, že admin článků vrátil jeho text a autor jej může upravit v sekci clanky/my/'>(?)</strong></label></td></tr>";
				if (stripos($oI->text, "</p>") !== false) {
	        echo "<tr><td width='20%' valign='top'>Text</td><td><textarea rows='25' ".($tags ? "class='cleanfirst '" : "")."id='mooeditable-1' cols='74' name='text'>"._htmlspec(trim($oI->text))."</textarea></td></tr>";
				}
				else {
	        echo "<tr><td width='20%' valign='top'>Text (první úprava)</td><td><textarea rows='25' ".($tags ? "class='cleanfirst '" : "")."id='mooeditable-1' cols='74' name='text'>".nl2p(trim($oI->text))."</textarea></td></tr>";
				}
        echo "<tr><td width='20%' valign='top'>Komunikace<br />s autorem</td><td><textarea rows='15' cols='74' name='admins'>"._htmlspec(stripslashes($oI->admins))."</textarea></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/clanky/?index=$index'\" /></td></tr>";
      
    echo "</table>";
  echo "</form>";
	?>
<script charset="utf-8" type="text/javascript">
/* <![CDATA[ */
	window.addEvent('domready', function() {
		var v;
		v = $('mooeditable-1');
		v.mooEditable({
			actions: 'bold italic | removeformat | toggleview',
			cleanup: v.hasClass('cleanfirst'),
			xhtml: true
		});
	});
/* ]]> */
</script>
<?php
  break;
  
    case "delete":
  
  echo "<h2>Smazat</h2>";
  echo "<form action='/rs/clanky/?op=2&amp;id=$_GET[id]&amp;autor=$oI->autor&amp;index=$index' method='post' onsubmit=\"if (confirm('Smazat článek?')){return true;}else{return false;}\">";
    echo "<table width='80%'>";
				echo "<tr><td width='20%'>Název</td><td colspan='2'><a href='/rs/clanky/?action=view&amp;id=".$oI->id."&amp;index=$index'>".stripslashes($oI->nazev)."</a><input type='hidden' value='"._htmlspec(stripslashes($oI->nazev))."' size='200' name='nazev' readonly='readonly' /></td></tr>";
        echo "<tr><td>Anotace</td><td>".stripslashes($oI->anotace)."</td></tr>";
        echo "<tr><td>Autor</td><td><a href='/rs/uzivatele/?action=view&id=$oI->autor' title='Profil autora'>$oI->login</a></td></tr>";
        echo "<tr><td valign='top'>Důvod</td><td><textarea rows='10' cols='40' name='reason'></textarea></td></tr>";
        echo "<tr><td></td><td><input type='checkbox' value='yes' name='sendInfo' id='sendInfo' /><label for='sendInfo'>poslat mi do pošty INFO-poštolku o smazání</label></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Smazat článek' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/clanky/?index=$index'\" /></td></tr>";
      
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
		$txtInS = mysql_fetch_row(mysql_query("SELECT text FROM 3_notes WHERE uid=1"));
		$dbCnt++;
		$txtIn = _htmlspec($txtInS[0]);
	  echo "<h2>Text nad výpisem článků</h2>\n";
	  echo "<form action='/rs/clanky/?op=5' method='post'>";
	    echo "<table width='80%'>";
	        echo "<tr><td valign='top'>Text<br />HTML tagy OK!<br />(konce řádků psát ručně <strong>&lt;br&nbsp;/&gt;</strong>)</td><td><textarea rows='10' cols='55' name='mess'>$txtIn</textarea></td></tr>";
	        echo "<tr><td colspan='2' align='center'><input type='submit' value='Upravit' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/clanky/'\" /></td></tr>";
	    echo "</table>";
	  echo "</form>";
	  break;
	}
}

switch($_GET['info']){
  case 1:
    echo "<span class='ok'>Ok: Článek v pořádku editován</span>";
  break;
  case 2:
    echo "<span class='error'>Chyba: Název, anotace nebo text prázdný</span>";
  break;
  case 3:
    echo "<span class='ok'>Ok: Článek smazán</span>";
  break;
  case 4:
    echo "<span class='error'>Chyba: Nastala chyba při mazání</span>";
  break;
  case 5:
    echo "<span class='ok'>Ok: Článek schválen</span>";
  break;
  case 6:
    echo "<span class='error'>Chyba: Nastala chyba při schvalování článku</span>";
  break;
  case 8:
    echo "<span class='ok'>Ok: Nástěnka Článků byla upravena</span>";
  break;
}

echo "<div>Máš přístup k Článkům pro tyto sekce: <strong>".join(", ", array_intersect_key(getSekceAll(), $sections))."</strong></div>";

echo "<div><a href='/rs/clanky/?action=frontpage' title='Upravit text nástěnky'>Nástěnka v Článcích</a>
<form action='/rs/clanky/' method='post'>
	<p>Část názvu: <input type='text' value='"._htmlspec(trim($_POST['q']))."' size='10' name='q' /> <input type='submit' value='Hledat' /></a></p>
</form></div>\n";

if (!isset($_POST['q'])) {
	$artc = mysql_query ("select count(*) from 3_clanky WHERE sekce IN ($sectionsSQL)");
	$dbCnt++;
	$art = mysql_query ("SELECT c.*, u.login, u.level, v.login AS adminname FROM 3_clanky AS c LEFT JOIN 3_users AS u ON u.id = c.autor LEFT JOIN 3_users AS v ON v.id = c.kdoschvalil WHERE c.sekce IN ($sectionsSQL) ORDER BY c.schvaleno ASC, c.schvalenotime DESC, c.odeslanotime ASC LIMIT $from, ".ARTICLE_LONG_COUNT);
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
		$art = mysql_query ("SELECT c.*, u.login, u.level, v.login AS adminname FROM 3_clanky AS c LEFT JOIN 3_users AS u ON u.id = c.autor LEFT JOIN 3_users AS v ON v.id = c.kdoschvalil WHERE c.sekce IN ($sectionsSQL) AND c.nazev_rew LIKE '%".do_seo(trim($_POST['q']))."%' ORDER BY c.nazev_rew ASC");
		$dbCnt++;
		$count = mysql_num_rows($art);
		$doPages = false;
	}
}

if ($count > 0){

  if ($doPages) echo "<p>".make_pages($count, ARTICLE_LONG_COUNT, $index)."</p>";

  echo "<table class='list autolayout'>\n";
  echo "<tr><th>Název</th><th>Autor</th><th>Anotace</th><th>Hodnocení</th><th>Stav</th><th>Akce</th></tr>\n";

$i = 1;
while ($s = mysql_fetch_object($art)){

$nazev = stripslashes($s->nazev);
$anotace = stripslashes($s->anotace);
$section = viewSekce(intval($s->sekce));

$rate = " N/A ";

if ($s->schvaleno < 1){
	if ($s->schvaleno < 0)
		$time = date("d.m.Y" ,$s->schvalenotime);
  else
		$time = date("d.m.Y" ,$s->odeslanotime);

  $acc = "<a href=\"javascript: conf('/rs/clanky/?op=3&amp;id=$s->id&amp;index=$index')\" title='schválit'>schválit</a> - ";
}else{

	if ($s->hodnoceni > 0) {
		$rtg = $s->hodnoceni/$s->hodnotilo;
		if ($rtg <= 2) $rate = number_format(round($rtg,2), 2, ',','')." <b style='color:red'>".str_repeat("* ",round($rtg))."</b>";
		else $rate = number_format(round($rtg,2), 2, ',','')." <b style='color:green'>".str_repeat("* ",round($rtg))."</b>";
	}

  $time = date("d.m.Y" ,$s->schvalenotime);
  $acc = "";
}

  echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
  echo "<td><a href='/rs/clanky/?action=view&amp;id=$s->id&amp;index=$index' title='Editace'>".((mb_strlen($nazev) > 18)? mb_substr($nazev, 0, 18).'...' : $nazev)."</a></td>\n";
  echo "<td><a href='/rs/uzivatele/?action=view&amp;id=$s->autor&amp;index=$index' title='Profil autora'>$s->login".(($s->level>=2)?"(*)":"")."</a> | <b>$section</b></td>";
  echo "<td>".((mb_strlen($anotace) > 25)? mb_substr($anotace, 0, 25).'...' : $anotace)."</td>\n";
  echo "<td>$rate</td>";
  echo "<td class='bg".(($s->schvaleno < 1)? 3 : 4)."'>".(($s->schvaleno < 1)? (($s->schvaleno<0) ? ('vrátil(a) '.$s->adminname) : 'čeká od') : ('schválil(a) '.$s->adminname))." $time</td>\n";
  echo "<td>$acc <a href='/rs/clanky/?action=edit&amp;id=$s->id&amp;index=$index' title='editovat'>editovat</a> - <a href=\"/rs/clanky/?action=delete&amp;id=$s->id&amp;index=$index\" title='smazat'>smazat</a></td>";
  echo "</tr>\n";
$i++;
}

  echo "</table>";
  if ($doPages) echo "<p>".make_pages($count, ARTICLE_LONG_COUNT, $index)."</p>";

}else{

	if (!isset($_POST['q'])) echo "Žádný článek v databázi.";
	else {
		if (strlen(trim($_POST['q']))>=3) echo "Žádný článek v databázi, který by vyhovoval hledání.";
	}

}
?>
<script charset="utf-8" type="text/javascript">/* <![CDATA[ */
var inpSearch = false,timerSearch = 0, whereToSearch = "clanky-name";
function checkInput(inp, wh){
	var str;
	whereToSearch = wh;
	if (!inp) return;
	if (!inpSearch)
		inpSearch = $(inp);

	inpSearch.set('name','nazevNew');

	str = inpSearch.getProperty('value').clean();

	if (timerSearch)
		$clear(timerSearch);

	if (str.length >= 3) {
		timerSearch = setTimeout(doSearch,1000);
	}
}

var oldSearch = "",req=false;
function doSearch() {
	var str;
	str = inpSearch.getProperty('value').clean();
	if (oldSearch != str) {
		if (req) {
			req.cancel();
		}
		inpSearch.getNext().set('text','Kontroluji...');
		req = new Request({
			'url':'/ajaxing.php?do=' + whereToSearch + '&_t='+$time(),
			'method':'post',
			'onSuccess': function(responseText) {
				if (responseText == '1') {
					inpSearch.set('name','changeNazev').getNext().set('text','OK - Nový název ještě není použitý.');
				}
				else {
					inpSearch.getNext().set('text',responseText);
				}
			}
		});
		req.send('nazev='+encodeURIComponent(str)+'&id='+inpSearch.getPrevious().get('value'));
	}
}
/* ]]> */</script>
<?php
// END BODY FUNC
}
?>
