<?php

$menuLinks['rozcesti'] = "Rozcestí";
$requireRights['chat'] = true;

$roz_categories = array('Fantasy'=>'Fantasy','Sci-fi'=>'Sci-fi');

function roz_category($t){
	global $roz_categories;
	if (isset($roz_categories[$t])) return $roz_categories[$t];
	return $roz_categories['Fantasy'];
}

function rozcesti_head($rub) {
global $time,$dbCnt,$roz_categories;
$lastedit = addslashes(date("j.n.y",$time)." ~ ".$_SESSION['login']);
switch ($_GET['op']) {
	//pridani situace do Rozcesti
	case 1:
		if (mb_strlen($_POST['sit_nazev'],"utf-8") > 4 && mb_strlen($_POST['sit_text'],"utf-8") > 10) {
			$maxSit = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_roz_situace WHERE nadrazena = '0'"));
			$dbCnt++;
			$category = roz_category($_POST['category']);
			$tn = "Situace č.".($maxSit[0]+1)." - ".addslashes($_POST['sit_nazev']);
			$tx = addslashes($_POST['sit_text']);
			mysql_query("INSERT INTO 3_roz_situace (nazev,nadrazena,category,popis,lastedit) VALUES ('$tn',0,'$category','$tx','$lastedit')");
			$dbCnt++;
			$info = 1;
		}else {
			$info = 2;
		}
	break;

	//pridani podsituace do Rozcesti
	case 2:
		$parentS = addslashes($_POST['nadrazena']);
		$rozz = mysql_fetch_row(mysql_query("SELECT id,category FROM 3_roz_situace WHERE id = '$parentS'"));
		if ($rozz[0] > 0 && mb_strlen($_POST['nadrazena'],"utf-8") > 0 && mb_strlen($_POST['podsit_text'],"utf-8") > 0) {
			$maxSit = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_roz_situace WHERE nadrazena = '$parentS'"));
			$dbCnt++;
			$tn = ($maxSit[0]+1)."-".$parentS;
			$tx = addslashes($_POST['podsit_text']);
			mysql_query("INSERT INTO 3_roz_situace (nazev,nadrazena,category,popis,lastedit) VALUES ('$tn','$parentS','$rozz[1]','$tx','$lastedit')");
			$dbCnt++;
			$info = 3;
		}else {
			$info = 4;
		}
	break;
	
	//editace situace do Rozcesti
	case 3:
		$id = addslashes($_GET['id']);
		$rozz = mysql_fetch_row(mysql_query("SELECT id,nadrazena FROM 3_roz_situace WHERE id = '$id'"));
		if ($rozz[0] > 0 && mb_strlen($_POST['nazev'],"utf-8") > 0 && mb_strlen($_POST['text'],"utf-8") > 0) {
			$tn = addslashes($_POST['nazev']);
			$tx = addslashes($_POST['text']);
			$cat = "";
			if ($rozz[1] == 0) {
				$cat = roz_category($_POST['category']);
				$sql = "UPDATE 3_roz_situace SET category='$cat' WHERE nadrazena = '$id'";
				mysql_query($sql);
				$cat = "category = '$cat', ";
			}
			mysql_query("UPDATE 3_roz_situace SET $cat nazev='$tn', popis='$tx', lastedit='$lastedit' WHERE id = '$id'");
			$dbCnt++;
			$info = 5;
		}else {
			$info = 6;
		}
	break;
	
	//smazani situace
	case 4:
		$id = addslashes($_GET['id']);
		$checkSit = mysql_fetch_row(mysql_query("SELECT nadrazena,id AS nadrazena FROM 3_roz_situace WHERE id ='$id'"));
		$dbCnt++;
		if ($checkSit[0] == '0' && $checkSit[1] > 0) {
			mysql_query("DELETE FROM 3_roz_situace WHERE nadrazena = '$checkSit[1]'");
			$dbCnt++;
		}
		mysql_query("DELETE FROM 3_roz_situace WHERE id = '$id'");
		$dbCnt++;
		$info = 7;
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

function rozcesti_body() {
global $dbCnt,$roz_categories;
if (!isSet($_GET['index'])){
  $index = 1;
}else{
  $index = (int) ($_GET['index']);
}
$from = ($index - 1) * ARTICLE_COUNT; //od kolikate polozky zobrazit

	echo "<a href='/rs/rozcesti/?action=new_sit&amp;index=$index' title='Nová situace'>Nová Situace</a> | <a href='/rs/rozcesti/?action=new_podsit&amp;index=$index' title='Nová podsituace'>Nová podsituace</a><br />";

if ($_GET['action'] == "new_sit"){
  echo "<h2>Nová Situace</h2>";
  echo "<form action='/rs/rozcesti/?op=1&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
				$maxSit = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_roz_situace WHERE nadrazena = 0"));
				$dbCnt++;
        echo "<tr><td width='20%'>Název</td><td>Situace č.".($maxSit[0]+1)." - <input type='text' size='40' name='sit_nazev' /> <em>např. Roklina ~ NickName (Nové kroniky)</em></td></tr>";
        echo "<tr><td></td><td>text \"<strong>Situace č.".($maxSit[0]+1)." - </strong>\" bude doplněno automaticky</td></tr>";
        echo "<tr><td valign='top'>Typ Situace</td><td><select name='category'>";
      	foreach($roz_categories as $k=>$v) {
      		echo "<option value='$k'>$k</option>";
				}
				echo "</select></td></tr>\n";
        echo "<tr><td valign='top'>Text Situace</td><td><textarea rows='10' cols='74' name='sit_text'>&lt;img src='/ns/blank.gif' class='rimg' /&gt; ... </textarea></td></tr>\n";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Vložit' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/rozcesti/?inndex=$index'\" /></td></tr>\n";
    echo "</table>\n";
  echo "</form>\n";
}
if ($_GET['action'] == "new_podsit"){
  echo "<h2>Nová podsituace</h2>";
  echo "<form action='/rs/rozcesti/?op=2&amp;index=$index' method='post'>\n";
    echo "<table width='80%'>\n";
    	if (isset($_GET['sit']) && $_GET['sit'] > 0){
    		$parentS = addslashes($_GET['sit']);
    		$parentSS = mysql_fetch_row(mysql_query("SELECT id,nazev FROM 3_roz_situace WHERE id = '$parentS' AND nadrazena = 0"));
    		$dbCnt++;
    		$parentS = $parentSS[0];
    		$prnt = $parentSS[1];
			}
			else {
				$parentS = 0;
			}

			if ($parentS > 0){
				$prnt .= "<input type='hidden' name='nadrazena' value='$parentS' />";
			}
			else {
				$prnt = "<select name='nadrazena'><option value=''>- - - - -</option>\n";
				$sits = mysql_query("SELECT id, nazev FROM 3_roz_situace WHERE nadrazena = '0' ORDER BY id ASC");
				$dbCnt++;
				while ($sit = mysql_fetch_object($sits)){
					$prnt .= "<option value='$sit->id'>$sit->nazev</option>\n";
				}
				$prnt .= "</select>";
			}

        echo "<tr><td width='20%'>Nadřazená situace</td><td>$prnt</td></tr>\n";
        echo "<tr><td valign='top'>Text podsituace</td><td><textarea rows='5' cols='74' name='podsit_text'></textarea></td></tr>\n";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Vložit' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/rozcesti/?index=$index'\" /></td></tr>\n";
    echo "</table>\n";
  echo "</form>\n";
}

if ($_GET['id'] > 0){
	$gid = addslashes($_GET['id']);
$fId = mysql_query ("SELECT * FROM 3_roz_situace WHERE id = '$gid'");
$dbCnt++;

if (mysql_num_rows($fId) > 0){

$oI = mysql_fetch_object($fId);

switch ($_GET['action']){

  case "edit":
  echo "<h2>Editace</h2>\n";
  echo "<form action='/rs/rozcesti/?op=3&amp;id=$gid&amp;index=$index' method='post'>\n";
    echo "<table width='80%'>\n";
        echo "<tr><td width='20%'>Název</td><td><input type='text' value='"._htmlspec($oI->nazev)."' size='100' name='nazev' /></td></tr>\n";
        if ($oI->nadrazena == 0) {
        	echo "<tr><td>Typ</td><td><select name='category'>";
        	foreach($roz_categories as $k=>$v) {
        		echo "<option value='$k'";
        		if ($k == $oI->category) echo " selected='selected'";
        		echo ">$k</option>";
					}
					echo "</select></td></tr>\n";
				}
        echo "<tr><td valign='top'>Text</td><td><textarea rows='15' cols='74' name='text'>"._htmlspec($oI->popis)."</textarea></td></tr>\n";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/rozcesti/?index=$index'\" /></td></tr>\n";
    echo "</table>\n";
  echo "</form>\n";
  break;
  
    case "delete":
  echo "<h2>Smazat</h2>";
  echo "<form action='/rs/rozcesti/?op=4&amp;id=$gid&amp;index=$index' method='post' onsubmit=\"if (confirm('Smazat příspěvek?')){return true;}else{return false;}\">";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Název</td><td>"._htmlspec($oI->nazev)."</td></tr>";
        echo "<tr><td valign='top'>Text</td><td>$oI->popis</textarea></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Smazat příspěvek' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/rozcesti/?index=$index'\" /></td></tr>";
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
    echo "<span class='ok'>Ok: Situace přidána</span>";
  break;
  case 2:
    echo "<span class='error'>Chyba: Název (5 znaků) nebo text (30 znaků) situace byl příliš krátký</span>";
  break;
  case 3:
    echo "<span class='ok'>Ok: Podsituace přidána</span>";
  break;
  case 4:
    echo "<span class='error'>Chyba: Nebyla určena nadřazená situace nebo text podsituace byl krátký</span>";
  break;
  case 5:
    echo "<span class='ok'>Ok: Úpravy proběhly vpořádku</span>";
  break;
  case 6:
    echo "<span class='error'>Chyba: Nastala chyba při úpravách</span>";
  break;
  case 7:
    echo "<span class='ok'>Ok: Smazání proběhlo vpořádku</span>";
  break;
}
$artc = mysql_query ("SELECT * FROM 3_roz_situace");
$dbCnt++;
$art = mysql_query ("SELECT * FROM 3_roz_situace ORDER BY id DESC LIMIT $from,".ARTICLE_COUNT);
$dbCnt++;
$count = mysql_num_rows($artc);

if ($count > 0){

  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

  echo "<table class='list autolayout'>\n";
  echo "<tr><th>Typ</th><th>Název</th><th>Text</th><th width='120'>Posl. úpravy</th><th width='120'>Akce</th></tr>\n";

$i = 1;
while ($s = mysql_fetch_object($art)){

$nazev = $s->nazev;
$popis = $s->popis;
$lastedit = $s->lastedit;
$acc = "";
if ($s->nadrazena < 1) {
	$acc = "<a href='/rs/rozcesti/?action=new_podsit&amp;sit=$s->id&amp;index=$index'>přidat podsituaci</a><br />";
}
else {
	$s->category = "";
}
  echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>";
  echo "<td>$s->category</td>";
  echo "<td>$nazev</td>";
  echo "<td>$popis</td>";
  echo "<td>$lastedit</td>";
  echo "<td>$acc <a href='/rs/rozcesti/?action=edit&id=$s->id&amp;index=$index' title='editovat'>edit</a> - <a href=\"/rs/rozcesti/?action=delete&id=$s->id\" title='smazat'>smazat</a></td>";
  echo "</tr>\n";
$i++;
}

  echo "</table>";
  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

}else{

  echo "Žádná Situace či podsituace v databázi.";

}
// END BODY FUNC
}
?>