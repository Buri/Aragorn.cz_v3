<?php
	
$menuLinks['herna'] = "Herna (".getHerNew().")";
$requireRights['herna'] = true;

function herna_head($rub) {
global $dbCnt,$time;
$_GET['id'] = intval($_GET['id']);
switch ($_GET['op']) {
	//editace jeskyne
	case 1:
		if (mb_strlen($_POST['nazev']) > 0 && mb_strlen($_POST['popis']) > 5 && mb_strlen($_POST['hleda']) > 4 && $_POST['pocet'] > 0){
			$admins = "";
			
			$autorUID = mysql_fetch_row(mysql_query("SELECT uid,id,schvaleno,nazev,nazev_rew FROM 3_herna_all WHERE id = '$_GET[id]'"));
	  	if (isset($_POST['vratitSkomentarem']) && $_POST['vratitSkomentarem'] == "on") {
				$admins .= "schvaleno = '-1', ";
		    sysPost($autorUID[0],"System INFO: Jeden z administrátorů <a href='/herna/'>Herny</a> na serveru Aragorn.cz ti vrátil tvou hru k přepracování.<br />Více se dozvíš v sekci Herna pod odkazem <a href='/herna/my/'>Moje hry</a> u konkrétní vrácené hry. Tam se pak podívej do sekce <strong>PJ - Obecné vlastnosti hry</strong> do kolonky <strong>Text pro adminy</strong>. Určitě je tam důležitý komentář nebo užitečná rada.");
			}
			if (isset($_POST['changeNazev'])) {
				$nm = addslashes(trim($_POST['changeNazev']));
				$nmR = addslashes(do_seo(trim($_POST['changeNazev'])));
				$admins .= " nazev = '$nm', nazev_rew = '$nmR', ";
			}
			$_POST['pocet'] = intval($_POST['pocet']);
			mysql_query ("UPDATE 3_herna_all SET $admins kdoschvalil = '$_SESSION[uid]', keywords = ".(addslashes($_POST['keywords']) ? "'".addslashes($_POST['keywords'])."'": "NULL").", pro_adminy = '".addslashes($_POST['pro_adminy'])."', popis = '".addslashes(htmlspecialchars($_POST['popis'],ENT_QUOTES,"UTF-8"))."', hraci_pocet = '$_POST[pocet]', aktivita = '$time', hraci_hleda = '".addslashes(htmlspecialchars($_POST['hleda'],ENT_QUOTES,"UTF-8"))."' WHERE id = $_GET[id]");
			$dbCnt++;
			$info = 1;
		}else{
			$info = 2;
		}
	break;
	//delete cave
	case 2:
		if (mb_strlen($_POST['nazev']) > 0){
			$hItm = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_all WHERE id = $_GET[id]"));
			$dbCnt++;
			if ($hItm[0]<1) {
				$info = 4;
			}
			else {
				$hItem = mysql_fetch_object(mysql_query("SELECT h.*,u.login,u.login_rew FROM 3_herna_all AS h LEFT JOIN 3_users AS u ON u.id = h.uid WHERE h.id = '$_GET[id]'"));
				$dbCnt++;
				$jTypString = $hItem->typ=='0'?"drd":"orp";
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

				$hraciSrc = mysql_query("SELECT uid,ico FROM 3_herna_postava_$jTypString WHERE cid = $hItem->id");
				$dbCnt++;
				if (mysql_num_rows($hraciSrc)>0) {
					$hraci = array();
					if (mysql_num_rows($hraciSsrc)>0) {
						while ($hrac = mysql_fetch_row($hraciSsrc)) {
							$hraci[] = $hrac[0];
							if ($hrac[1] != "" && $hrac[1] != 'default.jpg') {
								@unlink("./system/icos/$hrac[1]");
							}
						}
						$texte = "Administrátor $_SESSION[login] smazal jeskyni <strong>$hItem->nazev</strong>. Vaše postava tak byla také <strong>zabita</strong>.<br />Důvod: $_POST[reason]";
						sysPost($hraci,$texte);
					}
					if ($hItem->ico != "" && $hItem->ico != 'default.jpg') {
						@unlink("./system/icos/$hItem->ico");
					}
				}
				mysql_query("LOCK TABLES 3_herna_all WRITE, 3_herna_postava_orp WRITE, 3_herna_postava_drd WRITE, 3_herna_maps WRITE, 3_visited_4 WRITE, 3_comm_4 WRITE, 3_cave_mess WRITE, 3_cave_users WRITE, 3_herna_sets_open WRITE");
				mysql_query("DELETE FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->id';");
				mysql_query("DELETE FROM 3_herna_maps WHERE cid = '$hItem->id';");
				mysql_query("DELETE FROM 3_comm_4 WHERE aid = '$hItem->id';");
				mysql_query("DELETE FROM 3_visited_4 WHERE aid = '$hItem->id';");
				mysql_query("DELETE FROM 3_herna_sets_open WHERE cid = '$hItem->id';");
				mysql_query("DELETE FROM 3_cave_users WHERE cid = '$hItem->id';");
				mysql_query("DELETE FROM 3_cave_mess WHERE cid = '$hItem->id';");
				mysql_query("DELETE FROM 3_herna_all WHERE id = '$hItem->id'");
				mysql_query("DELETE FROM 3_herna_pj WHERE cid = '$hItem->id'");
				mysql_query("OPTIMIZE TABLE 3_comm_4, 3_herna_all, 3_visited_4");
				mysql_query("UNLOCK TABLES");
				$dbCnt+=9;
				if ($hItem->ico != "" && $hItem->ico != 'default.jpg') {
					@unlink("./system/icos/$hItem->ico");
				}
				$mess = "Vaše jeskyně $_POST[nazev] byla administrátorem $_SESSION[login] smazána.<br />$_POST[reason]";
				sysPost($hItem->uid, $mess);
	    	if (isset($_POST['sendInfo']) && $_POST['sendInfo'] == "yes") {
					sysPost($_SESSION['uid'],"System INFO: Jeskyně $_POST[nazev] (<a href='/uzivatele/$hItem->login_rew/'>$hItem->login</a>) byla smazána.<br />$_POST[reason]");
		    }
				$info = 3;
			}
		}else{
			$info = 4;
		}
	break;
	//schvaleni cave
	case 3:
		$_GET['id'] = intval($_GET['id']);
		if (mb_strlen($_GET[id]) > 0){
			mysql_query ("UPDATE 3_herna_all SET aktivita = '$time', zalozeno = '$time', schvaleno = '1', kdoschvalil = '$_SESSION[uid]' WHERE id = '$_GET[id]' AND schvaleno != '1'");
			$dbCnt++;
			if (mysql_affected_rows()>0) {
				$c = mysql_fetch_object( mysql_query("SELECT nazev, uid FROM 3_herna_all WHERE id = '$_GET[id]'") );
				$dbCnt++;
				$mess = "Vaše jeskyně <a href='/herna/".do_seo($c->nazev)."/' title='$c->nazev'>$c->nazev</a> byla dnes schválena administrátorem $_SESSION[login].\nPřejeme příjemnou hru.";
				sysPost($c->uid, $mess);
				$info = 5;
			}
			else {
				$info = 6;
			}
		}else{
			$info = 6;
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


function herna_body(){
	global $dbCnt;
$typJeskyne = array("DrD","ORP");	
$doPages = true;

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
$oI->hraci_hleda = stripslashes($oI->hraci_hleda);
$oI->popis = stripslashes($oI->popis);
$oI->nazev = stripslashes($oI->nazev);
$oI->pro_adminy = stripslashes($oI->pro_adminy);
switch ($_GET['action']){

	case "view":
	
	echo "<h2>Prohlédnout</h2>";
	echo "<form action='/rs/herna/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
		echo "<table width='80%'>";
				echo "<tr><td width='20%'>Systém</td><td>".$typJeskyne[$oI->typ]."</td></tr>";
				echo "<tr><td width='20%'>Název</td><td><a href='/herna/$oI->nazev_rew/' target='_blank'>$oI->nazev</a> via Aragorn.cz</td></tr>";
				echo "<tr><td width='20%'>Majitel</td><td><a href='/rs/uzivatele/?action=view&amp;id=$oI->uid'>$oI->login</a></td></tr>";
				echo "<tr><td width='20%'>Klíčová slova</td><td>"._htmlspec($oI->keywords)."</td></tr>";
				echo "<tr><td width='20%'>Popis</td><td>".nl2br($oI->popis)."</td></tr>";
				echo "<tr><td width='20%'>Popis pro adminy</td><td>".nl2br($oI->pro_adminy)."</td></tr>";
				echo "<tr><td width='20%'>Počet hráčů</td><td>".$oI->hraci_pocet."</td></tr>";
				echo "<tr><td width='20%'>Hledá hráče</td><td>".nl2br($oI->hraci_hleda)."</td></tr>";
				echo "<tr><td width='20%'>Poslední aktivita</td><td>".date("d.m.Y H:i" ,$oI->aktivita)."</td></tr>";
				echo "<tr><td colspan='2' align='center'><input type='button' value='Zavřít' onClick=\"window.location.href='/rs/herna/?index=$index'\" /></td></tr>";
				echo "<tr><td colspan='2' align='center'>".(($oI->schvaleno < 1)?"<input type='button' onclick=\"javascript: conf('/rs/herna/?op=3&amp;id=$oI->id&amp;index=$index')\" value='&raquo; schválit' />":"")."<input type='button' value='&raquo; editace' onclick=\"window.location.href='/rs/herna/?action=edit&amp;id=$oI->id&amp;index=$index';\" /><input type='button' value='&raquo; smazání' onclick=\"window.location.href='/rs/herna/?action=delete&amp;id=$oI->id&amp;index=$index';\" /></tr>";
		echo "</table>";
	echo "</form>";
	break;

	case "edit":
	
		echo "<h2>Editace</h2>";
		echo "<form action='/rs/herna/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
		echo "<table width='80%'>";
	
				echo "<tr><td width='20%'>Název</td><td><a target='_blank' href='/herna/".$oI->nazev_rew."/'>".stripslashes($oI->nazev)."</a> via Aragorn.cz<input type='hidden' value='".stripslashes($oI->nazev)."' size='100' name='nazev' readonly='readonly' /></td></tr>";
				echo "<tr><td width='20%'><a href='#' onclick='\$(\"changeNameSpan\").toggleClass(\"hide\");if(\$(\"changeNameSpan\").hasClass(\"hide\")){\$(\"changeNameSpan\").getFirst().getNext().set(\"name\",\"nazevNew\")};return false;'>Změnit název</a></td><td><div id='changeNameSpan' class='hide'><input type='hidden' value='$_GET[id]' name='the_hidden_id' /><input type='text' value='".stripslashes($oI->nazev)."' size='100' name='nazevNew' onkeyup='checkInput(this,\"game-name\")' /> <div>&nbsp;</div></div></td></tr>";
				echo "<tr><td>Majitel</td><td><a href='/rs/uzivatele/?action=view&amp;id=$oI->uid'>$oI->login</a></td></tr>";
				echo "<tr><td>Klíčová slova</td><td><input type='text' value='"._htmlspec($oI->keywords)."' size='254' name='keywords' /></td></tr>";
				echo "<tr><td>Počet hráčů</td><td><input type='text' value='".$oI->hraci_pocet."' size='100' name='pocet' /></td></tr>";
				echo "<tr><td>Hledá hráče</td><td><textarea rows='5' cols='74' name='hleda'>"._htmlspec($oI->hraci_hleda)."</textarea></td></tr>";
				echo "<tr><td>Pro adminy</td><td><textarea rows='5' cols='74' name='pro_adminy'>"._htmlspec($oI->pro_adminy)."</textarea></td></tr>";
if ($oI->schvaleno == 0){
        echo "<tr><td></td><td><input type='checkbox' value='on' name='vratitSkomentarem' id='sendecho' /><label for='sendecho'>vrátit autorovi k možnému přepracování <strong title='odešle autorovi poštolku o tom, že admin herny vrátil jeho hru a autor ji může upravit v sekci herna/my/'>(?)</strong></label></td></tr>";
}
				echo "<tr><td valign='top'>Popis</td><td><textarea rows='15' cols='74' name='popis'>"._htmlspec($oI->popis)."</textarea></td></tr>";
				echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/herna/?index=$index'\" /></td></tr>";
			
		echo "</table>";
		echo "</form>";
	break;
	
	case "delete":
	
		echo "<h2>Smazat</h2>";
		echo "<form action='/rs/herna/?op=2&amp;id=$_GET[id]&amp;vlastnik=$oI->uid&amp;index=$index' method='post' onsubmit=\"if (confirm('Smazat jeskyni?')){return true;}else{return false;}\">";
		echo "<table width='80%'>";
				echo "<tr><td width='20%'>Název</td><td colspan='2'><a target='_blank' href='/herna/".$oI->nazev_rew."/'>".$oI->nazev."</a> via Aragorn.cz<input type='hidden' value='"._htmlspec($oI->nazev)."' size='100' name='nazev' readonly='readonly' /></td></tr>";
				echo "<tr><td width='20%'>Majitel</td><td><a href='/rs/uzivatele/?action=view&amp;id=$oI->uid'>$oI->login</a></td></tr>";
				echo "<tr><td width='20%'>Klíčová slova</td><td>"._htmlspec($oI->keywords)."</td></tr>";
				echo "<tr><td width='20%'>Popis</td><td colspan='2'>".nl2br($oI->popis)."</td></tr>";
				echo "<tr><td width='20%'>Popis pro adminy</td><td colspan='2'>".nl2br($oI->pro_adminy)."</td></tr>";
				echo "<tr><td width='20%'>Počet hráčů</td><td colspan='2'>".$oI->hraci_pocet."</td></tr>";
				echo "<tr><td width='20%'>Hledá hráče</td><td colspan='2'>".nl2br($oI->hraci_hleda)."</td></tr>";
				echo "<tr><td width='20%'>Poslední aktivita</td><td colspan='2'>".date("d.m.Y H:i" ,$oI->aktivita)."</td></tr>";
				echo "<tr><td width='20%' valign='top'>Důvod</td><td><textarea rows='10' cols='40' name='reason'></textarea></td><td><u>Ukázkový text:</u><br /><br /><em>Jeskyně by měla mít smysluplný popis, který vyjadřuje, o čem se bude hrát či se již hraje, text pro hráče, aby věděli, zda se mají hlásit, a také jméno jeskyně by mělo respektovat pravidla malých a velkých písmen pro názvy. Neúplný popis může být zapříčiněn nedostatečným popisem zápletky, děje, místa či času. Taktéž gramatika a stylistika jsou základem úspěchu... <br />Vtipy a jim podobné pokusy už vůbec neschvalujeme.</em></td></tr>";
        echo "<tr><td></td><td><input type='checkbox' value='yes' name='sendInfo' id='sendInfo' /><label for='sendInfo'>poslat mi do pošty INFO-poštolku o smazání</label></td></tr>";
				echo "<tr><td colspan='3' align='center'><input type='submit' value='Smazat jeskyni' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/herna/?index=$index'\" /></td></tr>";
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
		echo "<span class='ok'>Ok: Jeskyně v pořádku editována</span>";
	break;
	case 2:
		echo "<span class='error'>Chyba: Jeden z atributů příliš krátký či prázdný</span>";
	break;
	case 3:
		echo "<span class='ok'>Ok: Jeskyně smazána</span>";
	break;
	case 4:
		echo "<span class='error'>Chyba: Nastala chyba při mazání</span>";
	break;
	case 5:
		echo "<span class='ok'>Ok: Jeskyně schválena</span>";
	break;
	case 6:
		echo "<span class='error'>Chyba: Nastala chyba při schvalování jeskyně</span>";
	break;

}

echo "<div><form action='/rs/herna/' method='post'>
	<p>Část názvu: <input type='text' value='"._htmlspec(trim($_POST['q']))."' size='10' name='q' /> <input type='submit' value='Hledat' /></a></p>
</form></div>\n";

if (!isset($_POST['q'])) {
	$artc = mysql_query ("SELECT count(*) FROM 3_herna_all");
	$dbCnt++;
	$count = array_shift(mysql_fetch_row($artc));
	$art = mysql_query ("SELECT c.*, u.login, u.level, v.login AS adminname FROM 3_herna_all AS c LEFT JOIN 3_users AS u ON u.id = c.uid LEFT JOIN 3_users AS v ON v.id = c.kdoschvalil ORDER BY c.schvaleno ASC, c.zalozeno DESC LIMIT $from, ".ARTICLE_COUNT);
	$dbCnt++;
}
else {
	$count = 0;
	if (strlen(do_seo(trim($_POST['q'])))<3) {
		echo "<p>Pro hledání je potřeba zadat alespoň 3 znaky z názvu (mezera se nepočítá)!</p>\n";
	}
	else {
		echo "<p>Hledám výraz <strong>"._htmlspec(trim($_POST['q']))."</strong>.</p>\n";
		$art = mysql_query ("SELECT c.*, u.login, u.level, v.login AS adminname FROM 3_herna_all AS c LEFT JOIN 3_users AS u ON u.id = c.uid LEFT JOIN 3_users AS v ON v.id = c.kdoschvalil WHERE c.nazev_rew LIKE '%".do_seo(trim($_POST['q']))."%' ORDER BY c.nazev_rew ASC");
		$dbCnt++;
		$count = mysql_num_rows($art);
		$doPages = false;
	}
}

if ($count > 0){

	if ($doPages) echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

	echo "<table class='list'>\n";
	echo "<tr><th>Typ</th><th>Název</th><th>Vlastník</th><th>Popis</th><th>Stav</th><th>Akce</th></tr>\n";

$i = 1;

while ($s = mysql_fetch_object($art)){

	$nazev = stripslashes($s->nazev);
	$popis = stripslashes($s->popis);
		
	if ($s->schvaleno == '0' || $s->schvaleno == '-1'){
		$acc = "<a href=\"javascript: conf('/rs/herna/?op=3&amp;id=$s->id&amp;index=$index')\" title='schválit'>schválit</a> -";
	}else{
		$acc = "";
	}

	$time = date("j.n.",$s->zalozeno);
	
	echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
	echo "<td>".$typJeskyne[$s->typ]."</td>";
	echo "<td><a href='/rs/herna/?action=view&id=$s->id&amp;index=$index' title='"._htmlspec($nazev)."'>".mb_strimwidth($nazev, 0, 21, "...")."</a></td>\n";
	echo "<td><a href='/rs/uzivatele/?action=view&id=$s->uid' title='"._htmlspec($s->login)."'>$s->login".(($s->level>=2)?"(*)":"")."</a></b></td>\n";
	echo "<td>".mb_strimwidth($popis, 0, 30, "...")."</td>\n";
  echo "<td class='bg".(($s->schvaleno < 1)? 3 : 4)."'>".(($s->schvaleno < 1)? (($s->schvaleno<0) ? ('vrátil(a) '.$s->adminname) : 'čeká od') : ('schválil(a) '.$s->adminname))." $time</td>\n";
  if ($s->schvaleno == 1){
		echo "<td>$acc <a href='/rs/herna/?action=edit&amp;id=$s->id&amp;index=$index' title='editovat'>edit</a> - <a href=\"/rs/herna/?action=delete&amp;id=$s->id&amp;index=$index\" title='smazat'>smazat</a></td>\n";
	}
	else {
		echo "<td>$acc <a href='/rs/herna/?action=edit&amp;id=$s->id&amp;index=$index' title='editovat'>edit/vrátit</a> - <a href=\"/rs/herna/?action=delete&amp;id=$s->id&amp;index=$index\" title='smazat'>smazat</a></td>\n";
	}
	echo "</tr>\n";
	$i++;
}

	echo "</table>";
	if ($doPages) echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

}else{

	if (!isset($_POST['q'])) echo "Žádná jeskyně v databázi.";
	else {
		if (strlen(trim($_POST['q']))>=3) echo "Žádná jeskyně v databázi, která by vyhovovala hledání.";
	}

}
?>
<script charset="utf-8" type="text/javascript">/* <![CDATA[ */
var inpSearch = false,timerSearch = 0, whereToSearch = "game-name";
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
