<?php

$menuLinks['blog'] = "Admin blog";
$requireRights['blog'] = true;

function blog_head($rub) {
global $time,$dbCnt;
switch ($_GET['op']) {
	//pridani prispevku do blogu
	case 1:
		$id = addslashes($_GET['id']);
		if (mb_strlen($_POST['nazev'],"utf-8") > 0 && mb_strlen($_POST['text'],"utf-8") > 0) {
			$tn = addslashes($_POST['nazev']);
			$tx = addslashes($_POST['text']);
			mysql_query("INSERT INTO 3_admin_blog (uid, time, headline, content) VALUES ($_SESSION[uid], $time, '$tn', '$tx')");
			$dbCnt++;
			$info = 1;
		}else {
			$info = 2;
		}
	break;
	
	//editace prispevku do blogu
	case 2:
		$id = addslashes($_GET['id']);
		if (mb_strlen($_POST['nazev'],"utf-8") > 0 && mb_strlen($_POST['text'],"utf-8") > 0) {
			$tn = addslashes($_POST['nazev']);
			$tx = addslashes($_POST['text']);
			$vy = addslashes($_POST['vydano']);
			mysql_query("UPDATE 3_admin_blog SET headline='$tn', content='$tx', vydano='$vy' WHERE id = $id");
			$dbCnt++;
			$info = 3;
		}else {
			$info = 2;
		}
	break;
	
	//smazani prispevku do blogu
	case 3:
		$id = addslashes($_GET['id']);
		mysql_query("DELETE FROM 3_admin_blog WHERE id = $id");
		$dbCnt++;
		$info = 7;
	break;

	// vydani prispevku v blogu
	case 4:
		$id = addslashes($_GET['id']);
		mysql_query("UPDATE 3_admin_blog SET vydano = '1' WHERE id = $id");
		$dbCnt++;
		$info = 8;
	break;
}
	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}
	header ("Location: /rs/$rub/?info=$info&index=$index");
exit;
}

function blog_body() {
global $time,$dbCnt;

echo "<a href='/rs/blog/?action=new&amp;index=$index' title='Nový příspěvek'>Nový příspěvek</a><br />";

	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

if ($_GET['action'] == "new"){
  echo "<h2>Nový příspěvek</h2>";
  echo "<form action='/rs/blog/?op=1&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Název</td><td><input type='text' size='100' name='nazev' /></td></tr>";
        echo "<tr><td width='20%' valign='top'>Text</td><td><textarea rows='15' id='mooeditable-1' cols='74' name='text'></textarea></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Vložit' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/blog/?index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
?>
<script charset="utf-8" type="text/javascript">
/* <![CDATA[ */
	window.addEvent('domready', function() {
		$('mooeditable-1').mooEditable({
			actions: 'bold italic strikethrough | createlink unlink | urlimage | undo redo removeformat | toggleview'
		});
	});
/* ]]> */
</script>

<?php
}

$from = ($index - 1) * ARTICLE_COUNT; //od kolikate polozky zobrazit

if ($_GET['id'] > 0){
$fId = mysql_query ("SELECT * FROM 3_admin_blog WHERE id = $_GET[id]");
$dbCnt++;

if (mysql_num_rows($fId) > 0){

$oI = mysql_fetch_object($fId);

switch ($_GET['action']){

  case "view":
  echo "<h2>Prohlédnout</h2>";
  echo "<form action='/rs/blog/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Název</td><td>".stripslashes($oI->headline)."</td></tr>";
        echo "<tr><td>Vydáno</td><td>".($oI->vydano?"ano":"ne")."</td></tr>";
        echo "<tr><td valign='top'>Text</td><td>".spit($oI->content, 1)."</td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='button' value='Zavřít' onClick=\"window.location.href='/rs/blog/?index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
  break;

  case "edit":
  echo "<h2>Editace</h2>";
  echo "<form action='/rs/blog/?op=2&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Název</td><td><input type='text' value='"._htmlspec(stripslashes($oI->headline))."' size='100' name='nazev' /></td></tr>";
        echo "<tr><td>Vydáno</td><td><select name='vydano'><option value='0'".($oI->vydano?"":" selected='selected'").">Ne</option><option value='1'".($oI->vydano?" selected='selected'":"").">Ano</option></select></td></tr>";
        echo "<tr><td valign='top'>Text</td><td><textarea rows='15' id='mooeditable-1' cols='74' name='text'>"._htmlspec(nl2p(stripslashes($oI->content)))."</textarea></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/blog/?index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
?>
<script charset="utf-8" type="text/javascript">
/* <![CDATA[ */
	window.addEvent('domready', function() {
		$('mooeditable-1').mooEditable({
			actions: 'bold italic strikethrough | createlink unlink | urlimage | undo redo removeformat | toggleview'
		});
	});
/* ]]> */
</script>

<?php

  break;
  
    case "delete":
  echo "<h2>Smazat</h2>";
  echo "<form action='/rs/blog/?op=3&amp;id=$_GET[id]&amp;index=$index' method='post' onsubmit=\"if (confirm('Smazat příspěvek?')){return true;}else{return false;}\">";
    echo "<table width='80%'>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Smazat příspěvek' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/blog/?index=$index'\" /></td></tr>";
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
    echo "<span class='ok'>Ok: Příspěvek přidán</span>";
  break;
  case 2:
    echo "<span class='error'>Chyba: Název nebo text prázdný</span>";
  break;
  case 3:
    echo "<span class='ok'>Ok: Příspěvek v pořádku editován</span>";
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
  case 7:
    echo "<span class='ok'>Ok: Příspěvek v pořádku smazán</span>";
  break;
  case 7:
    echo "<span class='ok'>Ok: Příspěvek byl vydán na veřejnost</span>";
  break;

}
$artc = mysql_query ("SELECT COUNT(*) FROM 3_admin_blog");
$dbCnt++;
$art = mysql_query ("SELECT c.*, u.login FROM 3_admin_blog AS c, 3_users AS u WHERE c.uid = u.id ORDER BY c.time DESC LIMIT $from, ".ARTICLE_COUNT);
$dbCnt++;
$count = array_shift(mysql_fetch_row($artc));

if ($count > 0){

  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

  echo "<table class='list autolayout'>\n";
  echo "<tr><th>Název</th><th>Autor</th><th>Stav</th><th>Akce</th></tr>\n";

$i = 1;
while ($s = mysql_fetch_object($art)){

$nazev = stripslashes($s->headline);

  echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
  echo "<td><a href='/rs/blog/?action=view&id=$s->id&amp;index=$index' title='Editace'>".$nazev."</a></td>\n";
  echo "<td><a href='/rs/uzivatele/?action=view&id=$s->uid' title='Profil autora'>$s->login</a></td>";
  if ($s->vydano) {
		echo "<td>publikovaný</td>";
	}
	else {
		echo "<td><a href='/rs/blog/?op=4&id=$s->id' title='Zveřejnit'>Zveřejnit!</a></td>";
	}
  echo "<td>$acc <a href='/rs/blog/?action=edit&id=$s->id&amp;index=$index' title='editovat'>editovat</a> - <a href=\"/rs/blog/?action=delete&id=$s->id&amp;index=$index\" title='smazat'>smazat</a></td>";
  echo "</tr>\n";
$i++;
}

  echo "</table>";
  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";

}else{

  echo "Žádný článek v databázi.";

}
// END BODY FUNC
}
?>
