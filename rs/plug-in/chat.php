<?php

$menuLinks['chat'] = "Chat (Bany/práva)";
$requireRights['chat'] = true;

function chat_head($rub) {
	global $menuLinks;
	if (!isset($menuLinks['chat'])) $_GET['op'] = 5;

switch ($_GET['op']) {
	case 1:
		$id = addslashes($_GET['id']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_admin WHERE id = '$id'"));
		if ($countEr[0]>0) {
			if (mb_strlen($_POST['jmeno'])>1 && (($_POST['typ'] == '0') || ($_POST['typ'] == '1')) && $_POST['typ'] != "") {
				$USERname = do_seo($_POST['jmeno']);
				$userSrc = mysql_query("SELECT id,level FROM 3_users WHERE login_rew = '$USERname'");
				if (mysql_num_rows($userSrc)>0) {
					$user = mysql_fetch_object($userSrc);
					if ($user->level<4) {
						mysql_query("UPDATE 3_chat_admin SET typ = '$_POST[typ]', uid='$user->id', adminid = '$_SESSION[uid]' WHERE id = $id");
						$info = 1;
					}else {
						$info = 6;
					}
				}else {
					$info = 6;
				}
			}elseif ($_POST['typ'] != '1' && $_POST ['typ'] != '0') {
				$info = 7;
			}else {
				$info = 4;
			}
		}else {
			$info = 8;
		}
	break;
	case 2:
		if (mb_strlen($_POST['jmeno'])>1 && (($_POST['typ'] == '-1') || ($_POST['typ'] == '0') || ($_POST['typ'] == '1')) && $_POST['typ'] != "") {
			$USERname = do_seo($_POST['jmeno']);
			$userSrc = mysql_query("SELECT id,level FROM 3_users WHERE login_rew = '$USERname'");
			if (mysql_num_rows($userSrc)>0) {
				$user = mysql_fetch_object($userSrc);
				if ($user->level<3) {
					if ($_POST['typ'] == '-1'){
						mysql_query("INSERT INTO 3_chat_admin (typ, uid, adminid, cas) VALUES ($_POST[typ], $user->id, $_SESSION[uid], $time)");
					}
					else {
						mysql_query("INSERT INTO 3_chat_admin (typ, uid, adminid) VALUES ($_POST[typ], $user->id, $_SESSION[uid])");
					}
					$info = 3;
				}else {
					$info = 6;
				}
			}else {
				$info = 6;
			}
		}elseif ($_POST['typ'] != '-1' && $_POST['typ'] != '1' && $_POST ['typ'] != '0') {
			$info = 7;
		}else {
			$info = 4;
		}
	break;
	case 3:
		$id = addslashes($_GET['id']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_admin WHERE id = '$id'"));
		if ($countEr[0]>0) {
			mysql_query("DELETE FROM 3_chat_admin WHERE id = $id");
			$info = 2;
		}else {
			$info = 8;
		}
	break;
}
Header ("Location: /rs/$rub/?info=$info");
exit;
}

function chat_body() {
$typPrav = array("Stálý Správce","PJ Rozcestí");  
	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}
if ($_GET['id'] > 0){
$fId = mysql_query ("SELECT a.*, u.login FROM 3_chat_admin AS a, 3_users AS u WHERE a.id = $_GET[id] AND a.uid = u.id AND a.typ != -1");
if (mysql_num_rows($fId) > 0){
$oI = mysql_fetch_object($fId);
switch ($_GET['action']){
  case "edit":
  echo "<h2>Editace práv na chatu</h2>";
  echo "<form action='/rs/chat/?op=1&amp;id=$_GET[id]' method='post'>";
    echo "<table>";
        echo "<tr><td width='20%'>Uživatel</td><td><input type='text' value='".stripslashes($oI->login)."' size='40' name='jmeno' /></td></tr>\n";
        echo "<tr><td>Práva</td><td>";
$i = 0;
$o = "";
foreach ($typPrav as $s){
	$o .= "<option value='$i'".(($oI->typ == $i)? " selected='selected'" : '').">$s</option>";
	$i++;
}
  echo "<select name='typ'>".$o."</select>";
				echo "</td></tr>\n";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/chat/'\" /></td></tr>\n";
    echo "</table>\n";
  echo "</form>";
  break;
}
}
else {
  echo "<span class='error'>Chyba: ID záznamu nenalezeno nebo nepovolená akce se záznamem</span>";
}
}elseif ($_GET['action'] == "new"){
  echo "<h2>Nová práva na chatu</h2>";
  echo "<form action='/rs/chat/?op=2' method='post'>";
    echo "<table width='40%'>";
        echo "<tr><td width='20%'>Uživatel</td><td><input type='text' value='".stripslashes($oI->login)."' size='40' name='jmeno' /></td></tr>\n";
        echo "<tr><td>Práva</td><td>";
$i = 0;
$o = "<option value='-1'>Zákaz vstupu na Rozcestí</option>";
foreach ($typPrav as $s){
	$o .= "<option value='$i'>$s</option>";
	$i++;
}
  echo "<select name='typ'>".$o."</select>";
				echo "</td></tr>\n";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Přidat práva' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/chat/'\" /></td></tr>\n";
    echo "</table>\n";
  echo "</form>";

}
switch($_GET['info']){
  case 8:
    echo "<span class='error'>Error: Hledané ID neexistuje</span>";
  break;
  case 1:
    echo "<span class='ok'>Ok: Záznam vpořádku upraven</span>";
  break;
  case 2:
    echo "<span class='ok'>Ok: Záznam smazán</span>";
  break;
  case 3:
    echo "<span class='ok'>Ok: Práva přidána</span>";
  break;
  case 4:
    echo "<span class='error'>Chyba: Jméno uživatele příliš krátké, nebo prázdné</span>";
  break;
  case 5:
    echo "<span class='error'>Chyba: Nastala chyba při mazání</span>";
  break;
  case 6:
    echo "<span class='error'>Chyba: Neexistující uživatel nebo uživatel s administrátorskými právy!</span>";
  break;
  case 7:
    echo "<span class='error'>Chyba: Špatně odesílaná práva</span>";
  break;
}
$art = mysql_query ("SELECT a.*, u.login, u.timestamp FROM 3_chat_admin AS a, 3_users AS u WHERE a.uid = u.id ORDER BY u.login_rew ASC, typ ASC");
$count = mysql_num_rows($art);
if ($count > 0){
	echo "<p><a href='/rs/chat/?action=new'>Přidat nová práva</a></p>\n";
  echo "<table class='list autolayout'>\n";
  echo "<tr><th>Uživatel</th><th>Status</th><th width='20%'>Práva</th><th>Akce</th></tr>\n";
$i = 1;

while ($s = mysql_fetch_object($art)){

	$nazev = stripslashes($s->nazev);
	$popis = stripslashes($s->popis);
	if ($s->typ!=-1)
		$acc = $typPrav[$s->typ];
	else
		$acc = "Zákaz vstupu na Rozcestí platný do ".date("j.n.Y H:i:s");

	echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
	echo "<td>$s->login</td>";
	echo "<td".($s->timestamp>0?" class='ok'>online":" class='error'>offline")."</td>";
	echo "<td>$acc</td>\n";
	if ($s->typ!=-1) {
		echo "<td><a href='/rs/chat/?action=edit&id=$s->id' title='Editace'>Upravit</a> | <a href=\"javascript: conf('/rs/chat/?op=3&id=$s->id')\">Smazat</a></td>\n";
	}
	else {
		echo "<td><a href=\"javascript: conf('/rs/chat/?op=3&id=$s->id')\">Smazat</a></td>\n";
	}
	echo "</tr>\n";
	$i++;

}
  echo "</table>";
}else{
  echo "<p>Žádná nastavená práva.</p><p><a href='/rs/chat/?action=new'>Přidat nová práva</a></p>";
}
// END BODY FUNC
}
?>
